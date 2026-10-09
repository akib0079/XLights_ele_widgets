<?php
/**
 * Shortcodes for use outside Elementor (or as a fallback when Elementor is
 * inactive): [xlights_page], [xlights_header], [xlights_footer],
 * [xlights_quote_form], [xlights_phone].
 *
 * @package XLights
 */

namespace XLights\Frontend;

use XLights\Forms\Quote_Form_Handler;
use XLights\Helpers\Tokens;
use XLights\Pages\Page_Registry;
use XLights\Sections\Component_Registry;
use XLights\Sections\Render_Context;
use XLights\Sections\Section_Renderer;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Shortcodes {

	public static function init() {
		add_shortcode( 'xlights_page', array( __CLASS__, 'page' ) );
		add_shortcode( 'xlights_header', array( __CLASS__, 'header' ) );
		add_shortcode( 'xlights_footer', array( __CLASS__, 'footer' ) );
		add_shortcode( 'xlights_quote_form', array( __CLASS__, 'quote_form' ) );
		add_shortcode( 'xlights_phone', array( __CLASS__, 'phone' ) );
	}

	/**
	 * Render a full page template with its default content.
	 *
	 * @param array $atts template.
	 * @return string
	 */
	public static function page( $atts ) {
		$atts = shortcode_atts( array( 'template' => 'home' ), $atts, 'xlights_page' );
		$key  = sanitize_key( $atts['template'] );
		if ( ! Page_Registry::exists( $key ) ) {
			return '';
		}
		Assets::enqueue();
		$html = Section_Renderer::page(
			$key,
			static function ( $section, $def ) {
				return isset( $def['defaults'] ) ? $def['defaults'] : array();
			},
			array(),
			array( 'source' => 'shortcode' )
		);
		return '<div class="xl-root xl-root--page xl-anim xl-hover" data-xl-template="' . esc_attr( $key ) . '">' . $html . '</div>';
	}

	/**
	 * @return string
	 */
	public static function header() {
		return Header_Footer::header();
	}

	/**
	 * @return string
	 */
	public static function footer() {
		return Header_Footer::footer();
	}

	/**
	 * Standalone quote form section.
	 *
	 * @param array $atts variant, heading.
	 * @return string
	 */
	public static function quote_form( $atts ) {
		$atts      = shortcode_atts(
			array(
				'variant' => 'general',
				'heading' => __( 'Request a quote', 'xlights-elementor-widgets' ),
			),
			$atts,
			'xlights_quote_form'
		);
		$component = Component_Registry::get( 'quote_form' );
		$ctx       = new Render_Context(
			array(
				'source' => 'shortcode',
				'anchor' => 'quote',
			)
		);
		Assets::enqueue();
		return '<div class="xl-root">' . Section_Renderer::section(
			$component,
			array(
				'variant' => sanitize_key( $atts['variant'] ),
				'heading' => sanitize_text_field( $atts['heading'] ),
			),
			$ctx
		) . '</div>';
	}

	/**
	 * Click-to-call phone link.
	 *
	 * @return string
	 */
	public static function phone() {
		$phone = (string) Settings::get( 'phone' );
		if ( '' === trim( $phone ) ) {
			return '';
		}
		return '<a class="xl-phone-link" href="' . esc_url( Tokens::tel_href( $phone ) ) . '">' . esc_html( $phone ) . '</a>';
	}
}
