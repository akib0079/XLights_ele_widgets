<?php
/**
 * Runs inside `wp eval-file --skip-plugins` with an earlier plugin version on
 * disk: loads that version's classes plus the current Stale_Defaults class
 * and prints the version's default hashes as JSON.
 *
 * Args: <old plugin dir> <current Stale_Defaults.php>
 */
list( $dir, $normalizer ) = $args;
$dir = rtrim( $dir, '/' ) . '/';
define( 'XLIGHTS_VERSION', 'history' );
define( 'XLIGHTS_FILE', $dir . 'xlights-elementor-widgets.php' );
define( 'XLIGHTS_PATH', $dir );
define( 'XLIGHTS_URL', 'https://history.invalid/wp-content/plugins/xlights-elementor-widgets/' );
require $dir . 'includes/Autoloader.php';
\XLights\Autoloader::register();
require $normalizer;
echo wp_json_encode( \XLights\Setup\Stale_Defaults::current_hashes() );
