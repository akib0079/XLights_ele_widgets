<?php
/**
 * Meta description fallback for dealer pages when no SEO plugin is active.
 * Pages created by the setup routine store a default description in
 * _xlights_meta_description (and in Yoast/Rank Math fields when present).
 *
 * @package XLights
 */

namespace XLights\SEO;

use XLights\Helpers\Tokens;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Meta {

	const META_KEY = '_xlights_meta_description';

	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'description' ), 1 );
	}

	public static function description() {
		if ( ! Settings::enabled( 'meta_fallback' ) || Schema::seo_plugin_active() || ! is_singular() ) {
			return;
		}
		$desc = (string) get_post_meta( get_queried_object_id(), self::META_KEY, true );
		if ( '' === trim( $desc ) ) {
			return;
		}
		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( Tokens::replace( $desc ) ) ) . '">' . "\n";
	}
}
