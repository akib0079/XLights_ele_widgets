<?php
/**
 * WP-CLI commands.
 *
 *   wp xlights status
 *   wp xlights setup [--pages=<keys>] [--publish] [--front-page] [--menu] [--overwrite=<keys>] [--template=<canvas|header_footer>]
 *   wp xlights settings get [<key>]
 *   wp xlights settings set <key> <value>
 *   wp xlights test-email <to>
 *   wp xlights purge-leads [--dry-run]
 *
 * @package XLights
 */

namespace XLights\CLI;

use WP_CLI;
use XLights\Forms\Mailer;
use XLights\Forms\Upload_Handler;
use XLights\Pages\Page_Registry;
use XLights\Settings\Settings;
use XLights\Setup\Installer;
use XLights\Setup\Page_Setup;

defined( 'ABSPATH' ) || exit;

final class Command {

	public static function register() {
		WP_CLI::add_command( 'xlights', __CLASS__ );
	}

	/**
	 * Show page setup status.
	 *
	 * @subcommand status
	 */
	public function status() {
		$rows = array();
		foreach ( Page_Setup::status() as $row ) {
			$rows[] = array(
				'template' => $row['key'],
				'path'     => $row['path'],
				'id'       => $row['id'],
				'status'   => $row['exists'] ? $row['status'] : '-',
				'composer' => $row['has_composer'] ? 'yes' : 'no',
			);
		}
		\WP_CLI\Utils\format_items( 'table', $rows, array( 'template', 'path', 'id', 'status', 'composer' ) );

		$public = Upload_Handler::is_publicly_accessible( true );
		$line   = 'Lead photos: ' . Upload_Handler::base_dir() . ' — ';
		if ( true === $public ) {
			WP_CLI::warning( $line . 'PUBLICLY ACCESSIBLE. Block it in the web server or define XLIGHTS_LEADS_DIR (docs/DEPLOYMENT.md §7).' );
		} elseif ( false === $public ) {
			WP_CLI::log( $line . 'protected.' );
		} else {
			WP_CLI::log( $line . 'could not be verified (loopback request failed).' );
		}
	}

	/**
	 * Create the dealer pages.
	 *
	 * ## OPTIONS
	 *
	 * [--pages=<keys>]
	 * : Comma-separated template keys. Default: all.
	 *
	 * [--publish]
	 * : Publish new pages (default: draft).
	 *
	 * [--front-page]
	 * : Set Home as the front page.
	 *
	 * [--menu]
	 * : Create and assign the header menu if none is assigned.
	 *
	 * [--overwrite=<keys>]
	 * : Comma-separated keys whose existing content may be replaced (backed up first).
	 *
	 * [--template=<template>]
	 * : canvas (default) or header_footer.
	 *
	 * @param array $args  Args.
	 * @param array $assoc Assoc args.
	 */
	public function setup( $args, $assoc ) {
		$pages     = isset( $assoc['pages'] ) ? array_map( 'trim', explode( ',', $assoc['pages'] ) ) : array_keys( Page_Registry::all() );
		$overwrite = isset( $assoc['overwrite'] ) ? array_map( 'trim', explode( ',', $assoc['overwrite'] ) ) : array();
		if ( $overwrite ) {
			WP_CLI::confirm( sprintf( 'Replace existing content on: %s?', implode( ', ', $overwrite ) ), $assoc );
		}
		$report = Page_Setup::run(
			$pages,
			array(
				'publish'    => isset( $assoc['publish'] ),
				'front_page' => isset( $assoc['front-page'] ),
				'menu'       => isset( $assoc['menu'] ),
				'overwrite'  => $overwrite,
				'template'   => isset( $assoc['template'] ) && 'header_footer' === $assoc['template'] ? 'header_footer' : 'canvas',
			)
		);
		foreach ( $report as $line ) {
			'error' === $line[0] ? WP_CLI::warning( $line[1] ) : WP_CLI::log( $line[1] );
		}
		WP_CLI::success( 'Page setup finished.' );
	}

	/**
	 * Get or set dealer settings.
	 *
	 * ## OPTIONS
	 *
	 * <action>
	 * : get or set
	 *
	 * [<key>]
	 * : Setting key.
	 *
	 * [<value>]
	 * : New value (set only).
	 *
	 * @param array $args Args.
	 */
	public function settings( $args ) {
		$action = isset( $args[0] ) ? $args[0] : 'get';
		$key    = isset( $args[1] ) ? $args[1] : '';
		$fields = Settings::fields();
		if ( 'set' === $action ) {
			if ( '' === $key || ! array_key_exists( 2, $args ) ) {
				WP_CLI::error( 'Usage: wp xlights settings set <key> <value>' );
			}
			if ( ! isset( $fields[ $key ] ) ) {
				WP_CLI::error( sprintf( 'Unknown setting "%s". Run `wp xlights settings get` to list keys.', $key ) );
			}
			Settings::update( array( $key => $args[2] ) );
			WP_CLI::success( sprintf( '%s = %s', $key, wp_json_encode( Settings::get( $key ) ) ) );
			return;
		}
		$all = Settings::all();
		if ( $key ) {
			if ( ! isset( $fields[ $key ] ) ) {
				WP_CLI::error( 'Unknown setting.' );
			}
			WP_CLI::log( is_scalar( $all[ $key ] ) ? (string) $all[ $key ] : wp_json_encode( $all[ $key ] ) );
			return;
		}
		$rows = array();
		foreach ( $all as $k => $v ) {
			if ( isset( $fields[ $k ] ) && 'secret' === $fields[ $k ]['type'] ) {
				$v = '' === $v ? '' : '********';
			}
			$rows[] = array(
				'key'   => $k,
				'value' => is_scalar( $v ) ? (string) $v : wp_json_encode( $v ),
			);
		}
		\WP_CLI\Utils\format_items( 'table', $rows, array( 'key', 'value' ) );
	}

	/**
	 * Send a test email through wp_mail().
	 *
	 * <to>
	 * : Recipient.
	 *
	 * @subcommand test-email
	 * @param array $args Args.
	 */
	public function test_email( $args ) {
		$to = isset( $args[0] ) ? sanitize_email( $args[0] ) : '';
		if ( ! is_email( $to ) ) {
			WP_CLI::error( 'Provide a valid email address.' );
		}
		$r = Mailer::send_test( $to );
		$r['ok'] ? WP_CLI::success( 'Accepted by the mail transport (check the inbox to confirm delivery).' ) : WP_CLI::error( $r['error'] );
	}

	/**
	 * Delete leads older than the configured retention period.
	 *
	 * [--dry-run]
	 * : Only count.
	 *
	 * @subcommand purge-leads
	 * @param array $args  Args.
	 * @param array $assoc Assoc.
	 */
	public function purge_leads( $args, $assoc ) {
		$dry = isset( $assoc['dry-run'] );
		$n   = Installer::purge_old_leads( $dry );
		WP_CLI::success( sprintf( '%d lead(s) %s.', $n, $dry ? 'would be deleted' : 'deleted' ) );
	}

	/**
	 * Add or remove the sample projects built from the bundled photos.
	 *
	 * ## OPTIONS
	 *
	 * [--remove]
	 * : Delete the sample projects and the images imported for them.
	 *
	 * [--force]
	 * : Add them even if the site already has projects.
	 *
	 * ## EXAMPLES
	 *
	 *     wp xlights samples
	 *     wp xlights samples --remove
	 *
	 * @param array $args  Args.
	 * @param array $assoc Assoc.
	 */
	public function samples( $args, $assoc ) {
		if ( isset( $assoc['remove'] ) ) {
			WP_CLI::success( sprintf( '%d sample project(s) removed.', \XLights\Setup\Sample_Projects::remove() ) );
			return;
		}
		if ( ! isset( $assoc['force'] ) && ! \XLights\Setup\Sample_Projects::site_is_empty() && ! \XLights\Setup\Sample_Projects::status()['count'] ) {
			WP_CLI::warning( 'This site already has projects. Use --force to add the samples anyway.' );
			return;
		}
		update_option( \XLights\Setup\Sample_Projects::STATE, 'queued', false );
		$n = \XLights\Setup\Sample_Projects::run_batch( 0 );
		wp_clear_scheduled_hook( \XLights\Setup\Sample_Projects::CRON_HOOK );
		WP_CLI::success( sprintf( '%d sample project(s) added (%d in total).', $n, \XLights\Setup\Sample_Projects::status()['count'] ) );
	}

	/**
	 * Let saved Page Composer pages follow the current default content: removes
	 * stored values that are exactly an earlier version's default. Text an
	 * editor changed is kept; the previous page data is backed up in post meta.
	 * Runs automatically on plugin updates.
	 *
	 * @subcommand refresh-defaults
	 */
	public function refresh_defaults() {
		$r = \XLights\Setup\Stale_Defaults::run();
		WP_CLI::success( sprintf( '%d stale value(s) cleared on %d page(s).', $r['values'], $r['posts'] ) );
	}
}
