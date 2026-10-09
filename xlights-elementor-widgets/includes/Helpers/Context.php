<?php
/**
 * Render context helpers: editor detection and approval gating.
 *
 * Gates keep unverified business claims off the live site while letting editors
 * see (and design around) them in Elementor:
 *   ''            always shown
 *   'credentials' shown when licensing/insurance is verified in settings
 *   'financing'   shown when financing is approved in settings
 *   'pending'     never shown publicly until the editor clears the flag
 *
 * @package XLights
 */

namespace XLights\Helpers;

use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Context {

	const SHOW    = 'show';
	const PENDING = 'pending';
	const HIDE    = 'hide';

	/** @var bool|null */
	private static $editor = null;

	/**
	 * True inside the Elementor editor canvas or preview.
	 *
	 * @return bool
	 */
	public static function is_editor() {
		if ( null !== self::$editor ) {
			return self::$editor;
		}
		$is = false;
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance ) ) {
			$el = \Elementor\Plugin::$instance;
			if ( isset( $el->editor ) && $el->editor && $el->editor->is_edit_mode() ) {
				$is = true;
			} elseif ( isset( $el->preview ) && $el->preview && $el->preview->is_preview_mode() ) {
				$is = true;
			}
		}
		// Do not cache "false" too early: Elementor toggles edit mode during AJAX renders.
		if ( $is ) {
			self::$editor = true;
		}
		return $is;
	}

	/**
	 * @param string $gate Gate key.
	 * @return bool
	 */
	public static function gate_open( $gate ) {
		switch ( (string) $gate ) {
			case '':
			case 'none':
				return true;
			case 'credentials':
				return Settings::enabled( 'trust_verified' );
			case 'financing':
				return Settings::enabled( 'financing_enabled' );
			case 'app_links':
				return '' !== Settings::get( 'app_store_url' ) || '' !== Settings::get( 'google_play_url' );
			case 'google_reviews':
				return '' !== Settings::get( 'google_reviews_url' );
			case 'pending':
			default:
				return false;
		}
	}

	/**
	 * @param string $gate Gate key.
	 * @return string show|pending|hide
	 */
	public static function visibility( $gate ) {
		if ( self::gate_open( $gate ) ) {
			return self::SHOW;
		}
		return self::is_editor() ? self::PENDING : self::HIDE;
	}

	/**
	 * Human label for why something is hidden.
	 *
	 * @param string $gate Gate key.
	 * @return string
	 */
	public static function gate_label( $gate ) {
		switch ( $gate ) {
			case 'credentials':
				return __( 'Hidden on the live site until credentials are verified (X LIGHTS → Settings → Credentials & Financing).', 'xlights-elementor-widgets' );
			case 'financing':
				return __( 'Hidden on the live site until financing is approved (X LIGHTS → Settings → Credentials & Financing).', 'xlights-elementor-widgets' );
			case 'app_links':
				return __( 'Hidden on the live site until App Store / Google Play URLs are added (X LIGHTS → Settings → App & Social).', 'xlights-elementor-widgets' );
			case 'google_reviews':
				return __( 'Hidden on the live site until the Google Reviews URL is added (X LIGHTS → Settings → App & Social).', 'xlights-elementor-widgets' );
			default:
				return __( 'Pending client approval — hidden on the live site.', 'xlights-elementor-widgets' );
		}
	}

	/**
	 * @param string $gate Gate key.
	 * @return string Badge HTML (editor only).
	 */
	public static function pending_badge( $gate ) {
		return '<span class="xl-pending-badge" role="note">' . Icons::svg( 'eye-off' ) . '<span>' . esc_html( self::gate_label( $gate ) ) . '</span></span>';
	}

	/**
	 * Editor-only hint box.
	 *
	 * @param string $message Message.
	 * @return string
	 */
	public static function editor_hint( $message ) {
		if ( ! self::is_editor() ) {
			return '';
		}
		return '<div class="xl-editor-hint" role="note">' . Icons::svg( 'info' ) . '<span>' . esc_html( $message ) . '</span></div>';
	}
}
