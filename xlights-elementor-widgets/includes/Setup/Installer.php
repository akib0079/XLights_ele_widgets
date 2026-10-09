<?php
/**
 * Activation, deactivation, upgrades and scheduled lead retention.
 *
 * @package XLights
 */

namespace XLights\Setup;

use XLights\Forms\Upload_Handler;
use XLights\PostTypes\Leads;
use XLights\PostTypes\Projects;
use XLights\PostTypes\Reviews;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Installer {

	const VERSION_OPTION = 'xlights_version';
	const CRON_HOOK      = 'xlights_daily';

	public static function init() {
		add_action( self::CRON_HOOK, array( __CLASS__, 'purge_old_leads' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_upgrade' ) );
	}

	/**
	 * @param bool $network_wide Network activation.
	 */
	public static function activate( $network_wide = false ) {
		if ( is_multisite() && $network_wide ) {
			foreach ( get_sites( array( 'fields' => 'ids', 'number' => 500 ) ) as $site_id ) {
				switch_to_blog( $site_id );
				self::activate_site();
				restore_current_blog();
			}
			return;
		}
		self::activate_site();
	}

	private static function activate_site() {
		Projects::register();
		Reviews::register();
		Leads::register();
		Projects::ensure_terms();

		$admin = get_role( 'administrator' );
		if ( $admin && ! $admin->has_cap( Leads::CAP ) ) {
			$admin->add_cap( Leads::CAP );
		}

		if ( false === get_option( Settings::OPTION ) ) {
			add_option( Settings::OPTION, Settings::defaults() );
		}
		Upload_Handler::ensure_dir();

		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::CRON_HOOK );
		}
		update_option( self::VERSION_OPTION, XLIGHTS_VERSION );
		set_transient( 'xlights_activated', 1, DAY_IN_SECONDS );
		flush_rewrite_rules();
	}

	public static function deactivate() {
		wp_clear_scheduled_hook( self::CRON_HOOK );
		flush_rewrite_rules();
	}

	/**
	 * Run lightweight upgrade steps when the stored version changes.
	 */
	public static function maybe_upgrade() {
		$stored = get_option( self::VERSION_OPTION );
		if ( XLIGHTS_VERSION === $stored ) {
			return;
		}
		Projects::ensure_terms();
		Upload_Handler::ensure_dir();
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::CRON_HOOK );
		}
		if ( $stored && version_compare( (string) $stored, '1.1.0', '<' ) ) {
			self::migrate_brand_defaults();
		}
		update_option( self::VERSION_OPTION, XLIGHTS_VERSION );
	}

	/**
	 * 1.1.0 moved the default palette from the brochure amber to the live
	 * X LIGHTS website palette. Colors still equal to the old defaults follow
	 * the new defaults; values a dealer changed are left untouched.
	 */
	private static function migrate_brand_defaults() {
		$stored = get_option( Settings::OPTION, array() );
		if ( ! is_array( $stored ) ) {
			return;
		}
		$legacy  = array(
			'color_accent'  => '#C98A3F',
			'color_dark'    => '#0E0E10',
			'color_surface' => '#F5F1EA',
			'color_text'    => '#1E1E22',
		);
		$fields  = Settings::fields();
		$changed = array();
		foreach ( $legacy as $key => $old ) {
			if ( isset( $stored[ $key ], $fields[ $key ] ) && 0 === strcasecmp( (string) $stored[ $key ], $old ) ) {
				$changed[ $key ] = $fields[ $key ]['default'];
			}
		}
		if ( $changed ) {
			Settings::update( $changed );
		}
	}

	/**
	 * Delete leads (and their photos) older than the retention period.
	 *
	 * @param bool $dry_run Only count.
	 * @return int Number of leads deleted (or that would be).
	 */
	public static function purge_old_leads( $dry_run = false ) {
		$days = (int) Settings::get( 'retention_days', 0 );
		if ( $days <= 0 ) {
			return 0;
		}
		$count = 0;
		do {
			$ids = get_posts(
				array(
					'post_type'      => Leads::POST_TYPE,
					'post_status'    => 'any',
					'fields'         => 'ids',
					'posts_per_page' => 100,
					'offset'         => $dry_run ? $count : 0,
					'date_query'     => array(
						array(
							'before' => gmdate( 'Y-m-d H:i:s', time() - $days * DAY_IN_SECONDS ),
							'column' => 'post_date_gmt',
						),
					),
				)
			);
			foreach ( $ids as $id ) {
				if ( ! $dry_run ) {
					Leads::delete_files( $id );
					wp_delete_post( $id, true );
				}
				$count++;
			}
		} while ( count( $ids ) === 100 && $count < 5000 );
		return $count;
	}
}
