<?php
/**
 * PSR-4 style autoloader for the XLights namespace.
 *
 * XLights\Forms\Quote_Form_Handler => includes/Forms/Quote_Form_Handler.php
 *
 * @package XLights
 */

namespace XLights;

defined( 'ABSPATH' ) || exit;

final class Autoloader {

	const PREFIX = 'XLights\\';

	public static function register() {
		spl_autoload_register( array( __CLASS__, 'load' ) );
	}

	/**
	 * @param string $class Fully qualified class name.
	 */
	public static function load( $class ) {
		if ( 0 !== strpos( $class, self::PREFIX ) ) {
			return;
		}
		$relative = substr( $class, strlen( self::PREFIX ) );
		// Only allow safe class-name characters before touching the filesystem.
		if ( ! preg_match( '/^[A-Za-z0-9_\\\\]+$/', $relative ) ) {
			return;
		}
		$file = XLIGHTS_PATH . 'includes/' . str_replace( '\\', '/', $relative ) . '.php';
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
}
