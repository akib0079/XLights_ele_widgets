<?php
/**
 * Saved Page Composer values that are an earlier version's default follow the
 * current default again; edited values are kept.
 *
 *   wp eval-file tests/php/stale-defaults.php
 */

use XLights\Setup\Stale_Defaults;

$xl_fail = 0;
$xl_ok   = static function ( $cond, $msg ) use ( &$xl_fail ) {
	echo ( $cond ? 'PASS ' : 'FAIL ' ), $msg, "\n";
	if ( ! $cond ) {
		++$xl_fail;
	}
};

$shipped = include XLIGHTS_PATH . 'includes/Setup/data/shipped-defaults.php';
$current = Stale_Defaults::current_hashes();
$xl_ok( is_array( $shipped ) && count( $shipped ) > 500, 'shipped-defaults table loaded (' . count( (array) $shipped ) . ' controls)' );
$xl_ok( count( $current ) > 500, 'current defaults hashed (' . count( $current ) . ' controls)' );

// 1.6.1 Home reviews heading, now changed.
$old_heading = 'What our customers say';
$xl_ok( in_array( Stale_Defaults::hash( $old_heading ), $shipped['home__reviews__heading'], true ), 'old Home reviews heading is a shipped default' );

$settings = array(
	'page_template'             => 'home',
	'home__reviews__heading'    => $old_heading,
	'home__reviews__eyebrow'    => 'Customer reviews',               // Still the current default: kept.
	'home__hero__heading'       => 'Our own headline, typed in Elementor', // Edited: kept.
	'home__reviews__link_label' => "Read more reviews\r\n",          // Same as default after normalizing: kept.
	'unknown__key'              => 'x',
);
list( $out, $removed ) = Stale_Defaults::refresh_settings( $settings, $shipped, $current );
$xl_ok( ! isset( $out['home__reviews__heading'] ), 'old default removed, so the current default shows' );
$xl_ok( isset( $out['home__reviews__eyebrow'] ), 'value equal to the current default kept' );
$xl_ok( isset( $out['home__hero__heading'] ) && 'Our own headline, typed in Elementor' === $out['home__hero__heading'], 'edited text kept' );
$xl_ok( isset( $out['unknown__key'], $out['page_template'] ), 'unrelated settings kept' );
$xl_ok( array( 'home__reviews__heading' ) === $removed, 'only the stale value reported' );

// Card lists: an old default list with Elementor row IDs and site-specific URLs.
$sections = \XLights\Pages\Page_Registry::sections( 'deck' );
$xl_ok( isset( $shipped['deck__products__items'] ) && count( $shipped['deck__products__items'] ) > 1, 'Deck product cards changed across versions' );
$custom = array(
	array(
		'_id'   => 'abc1234',
		'title' => 'Our special deck light',
		'text'  => 'Edited.',
		'image' => array(
			'url' => 'https://dealer.example/wp-content/plugins/xlights-elementor-widgets/assets/images/photos/deck-dot-light.webp',
			'id'  => '',
		),
	),
);
list( $out ) = Stale_Defaults::refresh_settings( array( 'deck__products__items' => $custom ), $shipped, $current );
$xl_ok( isset( $out['deck__products__items'] ), 'edited card list kept' );
$xl_ok(
	Stale_Defaults::normalize( 'https://a.example/wp-content/plugins/xlights-elementor-widgets/assets/x.webp' ) === Stale_Defaults::normalize( 'http://b.test/sub/wp-content/plugins/xlights-elementor-widgets/assets/x.webp' ),
	'bundled image URLs compare the same on any site address'
);

if ( $xl_fail ) {
	echo "\n{$xl_fail} check(s) failed.\n";
	exit( 1 );
}
echo "\nAll stale-default checks passed.\n";
