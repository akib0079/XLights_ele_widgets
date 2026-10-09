<?php
/**
 * Plugin Name:       X LIGHTS Elementor Widgets
 * Plugin URI:        https://github.com/akib0079/XLights_ele_widgets
 * Description:       Page Composer, section widgets, dealer settings, projects, reviews and secure quote-request handling for X LIGHTS dealer websites built with Elementor.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            AvixDigital
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       xlights-elementor-widgets
 * Domain Path:       /languages
 * Elementor tested up to: 4.0.8
 *
 * @package XLights
 */

defined( 'ABSPATH' ) || exit;

define( 'XLIGHTS_VERSION', '1.0.0' );
define( 'XLIGHTS_FILE', __FILE__ );
define( 'XLIGHTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'XLIGHTS_URL', plugin_dir_url( __FILE__ ) );
define( 'XLIGHTS_MIN_PHP', '7.4' );
define( 'XLIGHTS_MIN_ELEMENTOR', '3.20.0' );

if ( version_compare( PHP_VERSION, XLIGHTS_MIN_PHP, '<' ) ) {
	add_action(
		'admin_notices',
		static function () {
			echo '<div class="notice notice-error"><p>';
			echo esc_html(
				sprintf(
					/* translators: %s: required PHP version */
					__( 'X LIGHTS Elementor Widgets requires PHP %s or newer.', 'xlights-elementor-widgets' ),
					XLIGHTS_MIN_PHP
				)
			);
			echo '</p></div>';
		}
	);
	return;
}

require_once XLIGHTS_PATH . 'includes/Autoloader.php';
\XLights\Autoloader::register();

register_activation_hook( __FILE__, array( '\XLights\Setup\Installer', 'activate' ) );
register_deactivation_hook( __FILE__, array( '\XLights\Setup\Installer', 'deactivate' ) );

add_action( 'plugins_loaded', array( '\XLights\Plugin', 'instance' ) );
