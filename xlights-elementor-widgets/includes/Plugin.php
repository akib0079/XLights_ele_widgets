<?php
/**
 * Plugin bootstrap. Wires every module; each module registers its own hooks.
 *
 * @package XLights
 */

namespace XLights;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	/** @var Plugin|null */
	private static $instance = null;

	/**
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ), 0 );

		Setup\Installer::init();

		// Content models.
		PostTypes\Projects::init();
		PostTypes\Reviews::init();
		PostTypes\Leads::init();

		// Lead capture.
		Forms\Quote_Form_Handler::init();
		Forms\File_Access::init();
		Forms\Mailer::init();

		// Front end.
		Frontend\Assets::init();
		Frontend\Shortcodes::init();
		Frontend\Header_Footer::init();
		Frontend\Projects_Ajax::init();
		Frontend\Project_Single::init();

		// SEO.
		SEO\Schema::init();
		SEO\Meta::init();

		if ( is_admin() ) {
			Admin\Admin_Menu::init();
			Settings\Settings_Page::init();
			Admin\Project_Meta::init();
			Admin\Review_Meta::init();
			Admin\Leads_Admin::init();
			Admin\Setup_Page::init();
			Admin\Notices::init();
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			CLI\Command::register();
		}

		// Elementor layer: only hooks into Elementor actions, so it is inert when Elementor is inactive.
		Elementor\Integration::init();
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'xlights-elementor-widgets', false, dirname( plugin_basename( XLIGHTS_FILE ) ) . '/languages' );
	}

	/**
	 * Whether Elementor is loaded and meets the minimum version.
	 *
	 * @return bool
	 */
	public static function elementor_ready() {
		return did_action( 'elementor/loaded' ) && defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, XLIGHTS_MIN_ELEMENTOR, '>=' );
	}
}
