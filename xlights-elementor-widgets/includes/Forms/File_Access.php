<?php
/**
 * Authenticated access to lead photos. Requires a logged-in user with the
 * manage_xlights_leads capability; the files themselves are not web-accessible.
 *
 * @package XLights
 */

namespace XLights\Forms;

use XLights\PostTypes\Leads;

defined( 'ABSPATH' ) || exit;

final class File_Access {

	const ACTION = 'xlights_lead_file';

	public static function init() {
		add_action( 'admin_post_' . self::ACTION, array( __CLASS__, 'serve' ) );
		add_action( 'admin_post_nopriv_' . self::ACTION, array( __CLASS__, 'login_first' ) );
	}

	/**
	 * @param int $lead_id Lead.
	 * @param int $index   File index.
	 * @return string
	 */
	public static function url( $lead_id, $index ) {
		return add_query_arg(
			array(
				'action' => self::ACTION,
				'lead'   => (int) $lead_id,
				'file'   => (int) $index,
			),
			admin_url( 'admin-post.php' )
		);
	}

	/**
	 * Logged-out users are sent to the login screen and back.
	 */
	public static function login_first() {
		$request = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		wp_safe_redirect( wp_login_url( home_url( $request ) ) );
		exit;
	}

	/**
	 * Stream a stored file after capability checks (read-only GET).
	 */
	public static function serve() {
		if ( ! current_user_can( Leads::CAP ) ) {
			wp_die( esc_html__( 'You do not have permission to view this file.', 'xlights-elementor-widgets' ), 403 );
		}
		$lead_id = isset( $_GET['lead'] ) ? absint( $_GET['lead'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$index   = isset( $_GET['file'] ) ? absint( $_GET['file'] ) : -1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! $lead_id || Leads::POST_TYPE !== get_post_type( $lead_id ) ) {
			wp_die( esc_html__( 'File not found.', 'xlights-elementor-widgets' ), 404 );
		}
		$files = get_post_meta( $lead_id, '_xl_files', true );
		if ( ! is_array( $files ) || ! isset( $files[ $index ] ) ) {
			wp_die( esc_html__( 'File not found.', 'xlights-elementor-widgets' ), 404 );
		}
		$file = $files[ $index ];
		$path = Upload_Handler::absolute_path( isset( $file['path'] ) ? $file['path'] : '' );
		if ( ! $path || ! is_file( $path ) ) {
			wp_die( esc_html__( 'File not found.', 'xlights-elementor-widgets' ), 404 );
		}
		$mime = in_array( $file['mime'], array_values( Upload_Handler::MIMES ), true ) ? $file['mime'] : 'application/octet-stream';
		$name = sanitize_file_name( isset( $file['name'] ) && '' !== $file['name'] ? $file['name'] : wp_basename( $path ) );

		nocache_headers();
		header( 'Content-Type: ' . $mime );
		header( 'Content-Length: ' . filesize( $path ) );
		header( 'Content-Disposition: inline; filename="' . str_replace( '"', '', $name ) . '"' );
		header( 'X-Content-Type-Options: nosniff' );
		header( "Content-Security-Policy: default-src 'none'; img-src 'self'; style-src 'unsafe-inline'" );
		header( 'Cache-Control: private, no-store' );
		readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
		exit;
	}
}
