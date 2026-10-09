<?php
/**
 * Social profile links from settings (only those provided).
 *
 * @package XLights
 */

namespace XLights\Frontend;

use XLights\Helpers\Icons;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Social_Links {

	/**
	 * @return array setting => [icon, label]
	 */
	public static function networks() {
		return array(
			'facebook_url'  => array( 'facebook', 'Facebook' ),
			'instagram_url' => array( 'instagram', 'Instagram' ),
			'youtube_url'   => array( 'youtube', 'YouTube' ),
			'tiktok_url'    => array( 'tiktok', 'TikTok' ),
			'linkedin_url'  => array( 'linkedin', 'LinkedIn' ),
			'pinterest_url' => array( 'pinterest', 'Pinterest' ),
			'houzz_url'     => array( 'houzz', 'Houzz' ),
			'nextdoor_url'  => array( 'nextdoor', 'Nextdoor' ),
		);
	}

	/**
	 * @return string[] Profile URLs (for schema sameAs).
	 */
	public static function urls() {
		$out = array();
		foreach ( array_keys( self::networks() ) as $key ) {
			$url = (string) Settings::get( $key );
			if ( '' !== $url ) {
				$out[] = $url;
			}
		}
		return $out;
	}

	/**
	 * @param string $class Extra class.
	 * @return string
	 */
	public static function render( $class = '' ) {
		$items = '';
		foreach ( self::networks() as $key => $net ) {
			$url = (string) Settings::get( $key );
			if ( '' === $url ) {
				continue;
			}
			$items .= '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( sprintf( /* translators: %s: network */ __( '%s (opens in a new tab)', 'xlights-elementor-widgets' ), $net[1] ) ) . '">' . Icons::svg( $net[0] ) . '</a></li>';
		}
		if ( '' === $items ) {
			return '';
		}
		return '<ul class="xl-social ' . esc_attr( $class ) . '" role="list">' . $items . '</ul>';
	}
}
