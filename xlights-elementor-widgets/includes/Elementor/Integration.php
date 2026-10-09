<?php
/**
 * Elementor integration: dependency checks, widget category and registration.
 * Everything here hangs off Elementor hooks, so nothing runs when Elementor
 * is missing; an admin notice explains what to install instead.
 *
 * @package XLights
 */

namespace XLights\Elementor;

use XLights\Plugin;
use XLights\Frontend\Assets;

defined( 'ABSPATH' ) || exit;

final class Integration {

	const CATEGORY = 'xlights';

	public static function init() {
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'widgets' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( __CLASS__, 'editor_styles' ) );
		add_action( 'elementor/preview/enqueue_scripts', array( __CLASS__, 'preview_scripts' ) );
		add_action( 'admin_notices', array( __CLASS__, 'missing_notice' ) );
	}

	/**
	 * @param \Elementor\Elements_Manager $manager Elements manager.
	 */
	public static function category( $manager ) {
		$manager->add_category(
			self::CATEGORY,
			array(
				'title' => __( 'X LIGHTS Widgets', 'xlights-elementor-widgets' ),
				'icon'  => 'eicon-lightbox',
			)
		);
	}

	/**
	 * @return string[] Widget classes.
	 */
	public static function widget_classes() {
		$classes = array(
			Widgets\Page_Composer::class,
			Widgets\Header::class,
			Widgets\Footer::class,
			Widgets\Hero_Widget::class,
			Widgets\Intro_Widget::class,
			Widgets\Split_Widget::class,
			Widgets\Cards_Widget::class,
			Widgets\Connected_Widget::class,
			Widgets\App_Showcase_Widget::class,
			Widgets\Projects_Widget::class,
			Widgets\Before_After_Widget::class,
			Widgets\Reviews_Widget::class,
			Widgets\Financing_Widget::class,
			Widgets\Faq_Widget::class,
			Widgets\Service_Area_Widget::class,
			Widgets\Contact_Info_Widget::class,
			Widgets\Quote_Form_Widget::class,
			Widgets\Cta_Widget::class,
			Widgets\Team_Widget::class,
			Widgets\Specs_Widget::class,
			Widgets\Trust_Widget::class,
		);
		/**
		 * Filter the Elementor widget classes registered by the plugin.
		 *
		 * @param string[] $classes Widget classes (extend \Elementor\Widget_Base).
		 */
		return (array) apply_filters( 'xlights/elementor/widgets', $classes );
	}

	/**
	 * @param \Elementor\Widgets_Manager $manager Widgets manager.
	 */
	public static function widgets( $manager ) {
		if ( ! Plugin::elementor_ready() ) {
			return;
		}
		foreach ( self::widget_classes() as $class ) {
			if ( is_string( $class ) && is_subclass_of( $class, 'Elementor\\Widget_Base' ) ) {
				$manager->register( new $class() );
			}
		}
	}

	public static function editor_styles() {
		wp_enqueue_style( 'xlights-editor', XLIGHTS_URL . 'assets/css/editor.css', array(), XLIGHTS_VERSION );
	}

	public static function preview_scripts() {
		Assets::enqueue();
		wp_enqueue_script( Assets::FORM );
	}

	/**
	 * Explain missing or outdated Elementor (the rest of the plugin keeps working).
	 */
	public static function missing_notice() {
		if ( ! current_user_can( 'activate_plugins' ) || Plugin::elementor_ready() ) {
			return;
		}
		$message = did_action( 'elementor/loaded' )
			/* translators: %s: version */
			? sprintf( __( 'X LIGHTS Elementor Widgets requires Elementor %s or newer. Please update Elementor.', 'xlights-elementor-widgets' ), XLIGHTS_MIN_ELEMENTOR )
			: __( 'X LIGHTS Elementor Widgets: install and activate Elementor (free) to use the Page Composer and section widgets. Settings, projects, reviews, leads and shortcodes keep working without it.', 'xlights-elementor-widgets' );
		echo '<div class="notice notice-warning"><p>' . esc_html( $message ) . '</p></div>';
	}
}
