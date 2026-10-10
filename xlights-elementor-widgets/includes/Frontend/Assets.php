<?php
/**
 * Asset registration and conditional loading.
 *
 * Assets are registered globally but only enqueued when an X LIGHTS widget,
 * shortcode, global header/footer or project page is present.
 *
 * @package XLights
 */

namespace XLights\Frontend;

use XLights\Forms\Quote_Form_Handler;
use XLights\Helpers\Color;
use XLights\PostTypes\Projects;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Assets {

	const STYLE  = 'xlights';
	const SCRIPT = 'xlights';
	const FORM   = 'xlights-form';

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register' ), 5 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue' ), 20 );
		// Global fonts chosen in Colors & Fonts are loaded site-wide through Elementor.
		add_action( 'wp_enqueue_scripts', array( Typography::class, 'enqueue' ), 20 );
		add_action( 'wp_head', array( __CLASS__, 'favicon' ), 2 );
		add_action( 'wp_head', array( __CLASS__, 'preload_font' ), 7 );
		// Elementor registers widget dependencies on its own hooks; make sure ours exist.
		add_action( 'elementor/frontend/after_register_styles', array( __CLASS__, 'register' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( __CLASS__, 'register' ) );
		add_action( 'elementor/preview/enqueue_styles', array( __CLASS__, 'enqueue' ) );
	}

	/**
	 * @param string $file Relative asset path.
	 * @return string Version string (file mtime in debug, plugin version otherwise).
	 */
	private static function ver( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( XLIGHTS_PATH . $file ) ) {
			return XLIGHTS_VERSION . '.' . filemtime( XLIGHTS_PATH . $file );
		}
		return XLIGHTS_VERSION;
	}

	public static function register() {
		if ( wp_style_is( self::STYLE, 'registered' ) ) {
			return;
		}
		wp_register_style( self::STYLE, XLIGHTS_URL . 'assets/css/xlights.css', array(), self::ver( 'assets/css/xlights.css' ) );
		wp_add_inline_style( self::STYLE, self::root_vars() );

		$script_args = version_compare( get_bloginfo( 'version' ), '6.3', '>=' ) ? array(
			'in_footer' => true,
			'strategy'  => 'defer',
		) : true;
		wp_register_script( self::SCRIPT, XLIGHTS_URL . 'assets/js/xlights.js', array(), self::ver( 'assets/js/xlights.js' ), $script_args );
		wp_localize_script(
			self::SCRIPT,
			'XLightsConfig',
			array(
				'i18n' => array(
					'close'    => __( 'Close', 'xlights-elementor-widgets' ),
					'previous' => __( 'Previous photo', 'xlights-elementor-widgets' ),
					'next'     => __( 'Next photo', 'xlights-elementor-widgets' ),
					/* translators: 1: current photo, 2: total photos */
					'counter'  => __( 'Photo %1$s of %2$s', 'xlights-elementor-widgets' ),
					'loading'  => __( 'Loading projects…', 'xlights-elementor-widgets' ),
					'error'    => __( 'Projects could not be loaded. Please try again.', 'xlights-elementor-widgets' ),
					'dialog'   => __( 'Project photos', 'xlights-elementor-widgets' ),
				),
			)
		);

		wp_register_script( self::FORM, XLIGHTS_URL . 'assets/js/xlights-form.js', array(), self::ver( 'assets/js/xlights-form.js' ), $script_args );
		wp_localize_script(
			self::FORM,
			'XLightsForm',
			array(
				'tokenUrl' => add_query_arg( 'action', 'xlights_quote_token', admin_url( 'admin-ajax.php' ) ),
				'i18n'     => array(
					'required'   => __( 'This field is required.', 'xlights-elementor-widgets' ),
					'email'      => __( 'Please enter a valid email address.', 'xlights-elementor-widgets' ),
					'phone'      => __( 'Please enter a valid 10-digit US phone number.', 'xlights-elementor-widgets' ),
					'zip'        => __( 'Please enter a valid ZIP code.', 'xlights-elementor-widgets' ),
					'interests'  => __( 'Please choose at least one lighting interest (or "Not sure yet").', 'xlights-elementor-widgets' ),
					'privacy'    => __( 'Please confirm the privacy acknowledgment.', 'xlights-elementor-widgets' ),
					/* translators: %d: max files */
					'tooMany'    => __( 'Please upload no more than %d photos.', 'xlights-elementor-widgets' ),
					/* translators: %d: MB */
					'tooLarge'   => __( 'Each photo must be smaller than %d MB.', 'xlights-elementor-widgets' ),
					'badType'    => __( 'Photos must be JPG, PNG or WEBP images.', 'xlights-elementor-widgets' ),
					'fixErrors'  => __( 'Please review the highlighted fields.', 'xlights-elementor-widgets' ),
					/* translators: 1: current step, 2: total steps, 3: step name */
					'stepOf'     => __( 'Step %1$s of %2$s: %3$s', 'xlights-elementor-widgets' ),
					'sending'    => __( 'Sending…', 'xlights-elementor-widgets' ),
					'network'    => __( 'We could not reach the server. Please check your connection and try again.', 'xlights-elementor-widgets' ),
					'remove'     => __( 'Remove', 'xlights-elementor-widgets' ),
					/* translators: %s: file name */
					'removeFile' => __( 'Remove %s', 'xlights-elementor-widgets' ),
				),
			)
		);
	}

	/**
	 * CSS custom properties from X LIGHTS → Settings → Colors & Fonts.
	 *
	 * Output on :root and .xl-root so it replaces the stylesheet's default tokens;
	 * Elementor's per-widget Style controls (more specific) still win.
	 *
	 * @return string
	 */
	public static function root_vars() {
		$s    = Settings::all();
		$vars = array_merge( Color::tokens( Color::palette( $s ) ), Typography::tokens( $s ) );
		$css  = '';
		foreach ( $vars as $name => $value ) {
			$css .= $name . ':' . $value . ';';
		}
		$out = '' !== $css ? ':root,.xl-root{' . $css . '}' : '';

		$hide = trim( (string) $s['hide_theme_selectors'] );
		if ( 'global' === $s['header_footer_mode'] && '' !== $hide ) {
			$hide = preg_replace( '/[^a-zA-Z0-9\s,.#_\-\[\]="\':>]/', '', $hide );
			$out .= $hide . '{display:none!important}';
		}
		return $out;
	}

	public static function enqueue() {
		self::register();
		wp_enqueue_style( self::STYLE );
		wp_enqueue_script( self::SCRIPT );
	}

	public static function enqueue_form() {
		self::enqueue();
		wp_enqueue_script( self::FORM );
		if ( Quote_Form_Handler::turnstile_enabled() ) {
			// Third-party script loaded only when Turnstile keys are configured.
			wp_enqueue_script( 'cf-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		}
	}

	/**
	 * Enqueue early when we already know the page needs assets (avoids late CSS).
	 */
	public static function maybe_enqueue() {
		if ( Header_Footer::active_on_request() || is_singular( Projects::POST_TYPE ) ) {
			self::enqueue();
			return;
		}
		$post = get_post();
		if ( $post && is_singular() && preg_match( '/\[xlights_/', (string) $post->post_content ) ) {
			self::enqueue();
		}
	}

	/**
	 * Preload the bundled heading font on pages that use the plugin styles.
	 */
	public static function preload_font() {
		if ( ! wp_style_is( self::STYLE, 'enqueued' ) ) {
			return;
		}
		// The two faces visible above the fold (hero display and body text), when bundled.
		foreach ( Typography::preload_files() as $file ) {
			echo '<link rel="preload" href="' . esc_url( XLIGHTS_URL . 'assets/fonts/' . $file ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
		}
	}

	/**
	 * Favicon fallback when no WordPress Site Icon is configured.
	 */
	public static function favicon() {
		if ( has_site_icon() ) {
			return;
		}
		$id = (int) Settings::get( 'favicon' );
		if ( $id && wp_attachment_is_image( $id ) ) {
			$url = wp_get_attachment_image_url( $id, array( 64, 64 ) );
			if ( $url ) {
				echo '<link rel="icon" href="' . esc_url( $url ) . '">' . "\n";
			}
		}
	}
}
