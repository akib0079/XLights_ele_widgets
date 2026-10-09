<?php
/**
 * Elementor integration test (no browser needed).
 *
 *   wp eval-file tests/php/elementor.php
 *
 * Covers widget/category registration, Page Composer control generation,
 * settings round-trip, per-template isolation, ordering, device visibility,
 * merging of sections missing from saved order, and single-H1 output.
 */

if ( ! did_action( 'elementor/loaded' ) ) {
	echo "SKIP Elementor is not active.\n";
	return;
}

$xl_fail = 0;
$xl_ok   = static function ( $cond, $msg ) use ( &$xl_fail ) {
	echo ( $cond ? 'PASS ' : 'FAIL ' ), $msg, "\n";
	if ( ! $cond ) {
		++$xl_fail;
	}
};

$el    = \Elementor\Plugin::$instance;
$types = $el->widgets_manager->get_widget_types();
$ours  = array_values(
	array_filter(
		array_keys( $types ),
		static function ( $k ) {
			return 0 === strpos( $k, 'xlights-' );
		}
	)
);
$expected = count( \XLights\Elementor\Integration::widget_classes() );
$xl_ok( count( $ours ) === $expected, sprintf( 'registered X LIGHTS widgets: %d of %d', count( $ours ), $expected ) );

$cats = $el->elements_manager->get_categories();
$xl_ok( isset( $cats['xlights'] ), 'category registered: ' . ( isset( $cats['xlights']['title'] ) ? $cats['xlights']['title'] : '-' ) );

$composer = $types['xlights-page-composer'];
$t        = microtime( true );
$controls = $composer->get_controls();
$ms       = ( microtime( true ) - $t ) * 1000;
$xl_ok( count( $controls ) > 500, sprintf( 'Page Composer controls: %d (%.0f ms)', count( $controls ), $ms ) );
$xl_ok( isset( $controls['page_template'] ), 'page_template control exists' );
$xl_ok( isset( $controls['soffit__hero__heading'], $controls['contact__form__heading'] ), 'per-template section controls exist' );
printf( "INFO editor config size: %d KB\n", strlen( wp_json_encode( $composer->get_stack( false ) ) ) / 1024 );

$data = array(
	'id'         => 'xltest1',
	'elType'     => 'widget',
	'widgetType' => 'xlights-page-composer',
	'settings'   => array(
		'page_template'         => 'soffit',
		'soffit__hero__heading' => 'Custom soffit headline from editor',
		'home__hero__heading'   => 'SHOULD NOT APPEAR',
		'soffit__order'         => array(
			array(
				'section' => 'faq',
				'enabled' => 'yes',
			),
			array(
				'section' => 'hero',
				'enabled' => 'yes',
			),
			array(
				'section' => 'intro',
				'enabled' => '',
			),
			array(
				'section'     => 'daytime',
				'enabled'     => 'yes',
				'hide_mobile' => 'yes',
			),
		),
	),
	'elements'   => array(),
);
$w = $el->elements_manager->create_element_instance( $data );
ob_start();
$w->print_element();
$html = ob_get_clean();

$xl_ok( false !== strpos( $html, 'Custom soffit headline from editor' ), 'edited value renders' );
$xl_ok( false === strpos( $html, 'SHOULD NOT APPEAR' ), 'other templates\' values do not leak' );
$xl_ok( false === strpos( $html, 'id="xl-soffit-intro"' ), 'disabled section hidden' );
$p_faq  = strpos( $html, 'id="xl-soffit-faq"' );
$p_hero = strpos( $html, 'id="xl-soffit-hero"' );
$xl_ok( false !== $p_faq && false !== $p_hero && $p_faq < $p_hero, 'custom order respected (FAQ before hero)' );
$xl_ok( (bool) preg_match( '/<section id="xl-soffit-daytime" class="[^"]*xl-hide-mobile/', $html ), 'device visibility class applied' );
$xl_ok( false !== strpos( $html, 'id="xl-soffit-cta"' ), 'sections missing from saved order are merged back' );
$xl_ok( 1 === substr_count( $html, '<h1' ), 'exactly one H1 when the hero is not first' );

$gated_ok = array( 'xlights-financing', 'xlights-team', 'xlights-specs', 'xlights-before-after' );
// Content-driven widgets hide themselves when there is nothing approved to show.
if ( ! wp_count_posts( 'xlights_project' )->publish ) {
	$gated_ok[] = 'xlights-projects';
}
if ( ! get_posts( array( 'post_type' => 'xlights_review', 'numberposts' => 1, 'fields' => 'ids', 'meta_key' => '_xl_permission', 'meta_value' => '1' ) ) ) { // phpcs:ignore WordPress.DB.SlowDBQuery
	$gated_ok[] = 'xlights-reviews';
}
foreach ( $ours as $name ) {
	if ( 'xlights-page-composer' === $name ) {
		continue;
	}
	$inst = $el->elements_manager->create_element_instance(
		array(
			'id'         => substr( md5( $name ), 0, 7 ),
			'elType'     => 'widget',
			'widgetType' => $name,
			'settings'   => array(),
			'elements'   => array(),
		)
	);
	ob_start();
	$inst->print_element();
	$out = ob_get_clean();
	$xl_ok( strlen( $out ) > 200 || in_array( $name, $gated_ok, true ), sprintf( 'standalone %-24s renders (%d bytes%s)', $name, strlen( $out ), strlen( $out ) <= 200 ? ', gated/empty → hidden as designed' : '' ) );
}

if ( $xl_fail ) {
	echo "\n{$xl_fail} check(s) failed.\n";
	exit( 1 );
}
echo "\nAll Elementor checks passed.\n";
