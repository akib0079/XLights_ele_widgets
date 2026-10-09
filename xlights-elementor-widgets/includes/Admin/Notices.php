<?php
/**
 * Admin notices: post-activation setup prompt and failed lead notifications.
 *
 * @package XLights
 */

namespace XLights\Admin;

use XLights\Forms\Upload_Handler;
use XLights\PostTypes\Leads;

defined( 'ABSPATH' ) || exit;

final class Notices {

	public static function init() {
		add_action( 'admin_notices', array( __CLASS__, 'render' ) );
	}

	public static function render() {
		if ( current_user_can( 'manage_options' ) && get_transient( 'xlights_activated' ) ) {
			delete_transient( 'xlights_activated' );
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'X LIGHTS is active. Next: enter dealer details in X LIGHTS → Settings, then create the pages in X LIGHTS → Page Setup.', 'xlights-elementor-widgets' ) . ' <a href="' . esc_url( admin_url( 'admin.php?page=xlights-settings' ) ) . '">' . esc_html__( 'Open settings', 'xlights-elementor-widgets' ) . '</a></p></div>';
		}

		if ( ! current_user_can( Leads::CAP ) ) {
			return;
		}
		self::leads_dir_notice();

		$failed = get_transient( 'xlights_failed_leads' );
		if ( false === $failed ) {
			$q      = new \WP_Query(
				array(
					'post_type'      => Leads::POST_TYPE,
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'date_query'     => array( array( 'after' => '7 days ago' ) ),
					'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						array(
							'key'   => '_xl_mail_status',
							'value' => 'failed',
						),
					),
				)
			);
			$failed = (int) $q->found_posts;
			set_transient( 'xlights_failed_leads', $failed, 10 * MINUTE_IN_SECONDS );
		}
		if ( $failed > 0 ) {
			echo '<div class="notice notice-error"><p>' . esc_html(
				sprintf(
					/* translators: %d: count */
					_n( '%d lead notification email failed in the last 7 days. The lead is saved — review it under X LIGHTS → Leads and check your SMTP settings.', '%d lead notification emails failed in the last 7 days. The leads are saved — review them under X LIGHTS → Leads and check your SMTP settings.', $failed, 'xlights-elementor-widgets' ),
					$failed
				)
			) . '</p></div>';
		}
	}

	/**
	 * Warn when lead photos can be downloaded directly (servers that ignore
	 * .htaccess, such as Nginx). The loopback probe only runs on X LIGHTS
	 * screens, at most once a day; the warning shows everywhere until fixed.
	 */
	private static function leads_dir_notice() {
		$screen  = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$on_ours = $screen && false !== strpos( (string) $screen->id, 'xlights' );
		$public  = $on_ours ? Upload_Handler::is_publicly_accessible() : ( 'yes' === get_transient( 'xlights_leads_dir_public' ) );
		if ( true !== $public ) {
			return;
		}
		echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Customer photos are publicly accessible.', 'xlights-elementor-widgets' ) . '</strong> '
			. esc_html__( 'This web server ignores the .htaccess protection on the lead photo folder (common on Nginx). Block the folder in the server configuration or define XLIGHTS_LEADS_DIR in wp-config.php to store photos outside the web root (see docs/DEPLOYMENT.md, section 7).', 'xlights-elementor-widgets' )
			. '</p><p><code>' . esc_html( Upload_Handler::public_base_url() . '/' ) . '</code></p></div>';
	}
}
