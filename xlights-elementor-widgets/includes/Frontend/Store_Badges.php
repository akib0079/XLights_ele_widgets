<?php
/**
 * App download buttons. Uses neutral buttons rather than Apple/Google badge
 * artwork (official badges have their own usage guidelines; swap in approved
 * badge files via the xlights/store_badges filter if desired).
 *
 * @package XLights
 */

namespace XLights\Frontend;

use XLights\Helpers\Context;
use XLights\Helpers\Icons;
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
		$out    = '';
		if ( $pending && '' === $apple && '' === $google ) {
			$out .= '<div class="xl-stores xl-is-pending">' . Context::pending_badge( 'app_links' );
			$out .= '<span class="xl-store"><span class="xl-store__icon">' . Icons::svg( 'apple' ) . '</span><span><small>' . esc_html__( 'Get the app for', 'xlights-elementor-widgets' ) . '</small>iPhone</span></span>';
			$out .= '<span class="xl-store"><span class="xl-store__icon">' . Icons::svg( 'google-play' ) . '</span><span><small>' . esc_html__( 'Get the app for', 'xlights-elementor-widgets' ) . '</small>Android</span></span>';
			return $out . '</div>';
		}
		$links = array();
		if ( '' !== $apple ) {
			$links[] = array( $apple, 'apple', 'iPhone' );
		}
		if ( '' !== $google ) {
			$links[] = array( $google, 'google-play', 'Android' );
		}
		if ( ! $links ) {
			return '';
		}
		$out .= '<div class="xl-stores">';
		foreach ( $links as $l ) {
			$out .= '<a class="xl-store" href="' . esc_url( $l[0] ) . '" target="_blank" rel="noopener"><span class="xl-store__icon">' . Icons::svg( $l[1] ) . '</span><span><small>' . esc_html__( 'Get the app for', 'xlights-elementor-widgets' ) . '</small>' . esc_html( $l[2] ) . '</span><span class="xl-sr"> ' . esc_html__( '(opens in a new tab)', 'xlights-elementor-widgets' ) . '</span></a>';
		}
		$out .= '</div>';
		/**
		 * Filter app store button markup.
		 *
		 * @param string $out Markup.
		 */
		return apply_filters( 'xlights/store_badges', $out );
	}
}
