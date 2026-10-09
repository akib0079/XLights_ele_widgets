<?php
/**
 * Admin notices: post-activation setup prompt and failed lead notifications.
 *
 * @package XLights
 */

namespace XLights\Admin;

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
}
