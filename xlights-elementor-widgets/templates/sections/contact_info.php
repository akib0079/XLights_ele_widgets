<?php
/**
 * Contact information cards (all values from dealer settings).
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Frontend\Social_Links;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\Helpers\Tokens;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

$s     = Settings::all();
$cards = array();

if ( 'yes' === $data['show_phone'] && '' !== trim( $s['phone'] ) ) {
	$cards[] = array( 'phone', __( 'Call', 'xlights-elementor-widgets' ), '<a href="' . esc_url( Tokens::tel_href( $s['phone'] ) ) . '">' . esc_html( $s['phone'] ) . '</a>' );
}
if ( 'yes' === $data['show_email'] && ! empty( $s['show_email'] ) && is_email( $s['email'] ) ) {
	$cards[] = array( 'mail', __( 'Email', 'xlights-elementor-widgets' ), '<a href="' . esc_url( 'mailto:' . antispambot( $s['email'] ) ) . '">' . esc_html( antispambot( $s['email'] ) ) . '</a>' );
}
if ( 'yes' === $data['show_address'] && ! empty( $s['show_address'] ) && '' !== trim( $s['address_street'] ) ) {
	$addr    = esc_html( $s['address_street'] ) . '<br>' . esc_html( trim( $s['address_city'] . ', ' . $s['address_state'] . ' ' . $s['address_zip'], ', ' ) );
	$cards[] = array( 'map-pin', __( 'Visit', 'xlights-elementor-widgets' ), $addr );
}
if ( 'yes' === $data['show_hours'] && '' !== trim( $s['hours'] ) ) {
	$cards[] = array( 'clock', __( 'Hours', 'xlights-elementor-widgets' ), nl2br( esc_html( $s['hours'] ) ) );
}
if ( 'yes' === $data['show_areas'] ) {
	$areas = Settings::lines( 'service_areas' );
	if ( $areas ) {
		$cards[] = array( 'compass', __( 'Service area', 'xlights-elementor-widgets' ), esc_html( implode( ' · ', $areas ) ) );
	}
}
$social = 'yes' === $data['show_social'] ? Social_Links::render() : '';
?>
<div class="xl-container">
	<?php echo Html::section_head( $data, array( 'class' => 'xl-head--center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<ul class="xl-grid xl-grid--<?php echo esc_attr( (string) min( 4, max( 1, count( $cards ) ) ) ); ?> xl-contact-cards" role="list">
		<?php foreach ( $cards as $card ) : ?>
			<li class="xl-card xl-contact-card" data-xl-reveal>
				<span class="xl-card__icon"><?php echo Icons::svg( $card[0] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<h3 class="xl-card__title"><?php echo esc_html( $card[1] ); ?></h3>
				<p class="xl-card__text"><?php echo $card[2]; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( '' !== $social ) : ?>
		<div class="xl-actions xl-actions--center"><?php echo $social; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php endif; ?>
</div>
