<?php
/**
 * App download badges: the official App Store and Google Play badge artwork,
 * linked to the X LIGHTS LED app (Settings → App & Social).
 *
 * @package XLights
 */

namespace XLights\Frontend;

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Store_Badges {

	/**
	 * @param bool $pending Render the editor-only pending state.
	 * @return string
	 */
	public static function render( $pending = false ) {
		$apple  = (string) Settings::get( 'app_store_url' );
		$google = (string) Settings::get( 'google_play_url' );
		$badges = array(
			array( $apple, 'badge-app-store.webp', __( 'Download on the App Store', 'xlights-elementor-widgets' ), 432 ),
			array( $google, 'badge-google-play.webp', __( 'Get it on Google Play', 'xlights-elementor-widgets' ), 486 ),
		);
		if ( '' === $apple && '' === $google ) {
			if ( ! $pending ) {
				return '';
			}
			$out = '<div class="xl-stores xl-is-pending">' . Context::pending_badge( 'app_links' );
			foreach ( $badges as $b ) {
				$out .= '<span class="xl-store">' . self::img( $b ) . '</span>';
			}
			return $out . '</div>';
		}
		$out = '<div class="xl-stores">';
		foreach ( $badges as $b ) {
			if ( '' === $b[0] ) {
				continue;
			}
			$out .= '<a class="xl-store" href="' . esc_url( $b[0] ) . '" target="_blank" rel="noopener">' . self::img( $b ) . '<span class="xl-sr"> ' . esc_html__( '(opens in a new tab)', 'xlights-elementor-widgets' ) . '</span></a>';
		}
		$out .= '</div>';
		/**
		 * Filter app store badge markup.
		 *
		 * @param string $out Markup.
		 */
		return apply_filters( 'xlights/store_badges', $out );
	}

	/**
	 * @param array $b [url, file, label, width at 144 px height].
	 * @return string
	 */
	private static function img( array $b ) {
		return '<img class="xl-store__badge" src="' . esc_url( Html::asset_image( 'brand/' . $b[1] ) ) . '" width="' . (int) round( $b[3] / 3 ) . '" height="48" alt="' . esc_attr( $b[2] ) . '" loading="lazy" decoding="async">';
	}
}
