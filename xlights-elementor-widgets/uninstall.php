<?php
/**
 * Uninstall routine.
 *
 * Always removes plugin options, transients and scheduled events.
 * Content (projects, reviews, leads and lead photos) is deleted ONLY when
 * define( 'XLIGHTS_UNINSTALL_DELETE_DATA', true ); is set in wp-config.php,
 * so a dealer's portfolio and lead history are never lost by accident.
 *
 * @package XLights
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

delete_option( 'xlights_settings' );
delete_option( 'xlights_pages' );
delete_option( 'xlights_version' );
wp_clear_scheduled_hook( 'xlights_daily' );

// Plugin transients (rate limits, duplicate guards, notices).
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_xl\_%' OR option_name LIKE '\_transient\_timeout\_xl\_%' OR option_name LIKE '\_transient\_xlights\_%' OR option_name LIKE '\_transient\_timeout\_xlights\_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

$role = get_role( 'administrator' );
if ( $role ) {
	$role->remove_cap( 'manage_xlights_leads' );
}

if ( defined( 'XLIGHTS_UNINSTALL_DELETE_DATA' ) && XLIGHTS_UNINSTALL_DELETE_DATA ) {
	foreach ( array( 'xlights_project', 'xlights_review', 'xlights_lead' ) as $type ) {
		$ids = get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}
	}
	foreach ( array( 'xlights_project_category', 'xlights_property_type' ) as $tax ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $tax,
				'hide_empty' => false,
				'fields'     => 'ids',
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term_id ) {
				wp_delete_term( $term_id, $tax );
			}
		}
	}
	// Remove private lead photos.
	$uploads = wp_upload_dir( null, false );
	$dir     = trailingslashit( $uploads['basedir'] ) . 'xlights-leads';
	if ( is_dir( $dir ) ) {
		$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
		foreach ( $it as $file ) {
			$file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}
		rmdir( $dir ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
}
