<?php
/**
 * Admin menu and the Overview screen (status + client approval checklist).
 *
 * @package XLights
 */

namespace XLights\Admin;

use XLights\Pages\Page_Registry;
use XLights\PostTypes\Leads;
use XLights\PostTypes\Projects;
use XLights\PostTypes\Reviews;
use XLights\Settings\Settings;
use XLights\Setup\Page_Setup;

defined( 'ABSPATH' ) || exit;

final class Admin_Menu {

	const SLUG = 'xlights';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 9 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	public static function menu() {
		add_menu_page(
			__( 'X LIGHTS', 'xlights-elementor-widgets' ),
			__( 'X LIGHTS', 'xlights-elementor-widgets' ),
			'edit_posts',
			self::SLUG,
			array( __CLASS__, 'overview' ),
			'dashicons-lightbulb',
			26
		);
		add_submenu_page( self::SLUG, __( 'Overview', 'xlights-elementor-widgets' ), __( 'Overview', 'xlights-elementor-widgets' ), 'edit_posts', self::SLUG, array( __CLASS__, 'overview' ) );
	}

	/**
	 * @param string $hook Screen hook.
	 */
	public static function assets( $hook ) {
		$screen = get_current_screen();
		$ours   = false !== strpos( (string) $hook, 'xlights' ) || ( $screen && in_array( $screen->post_type, array( Projects::POST_TYPE, Reviews::POST_TYPE, Leads::POST_TYPE ), true ) );
		if ( ! $ours ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'xlights-admin', XLIGHTS_URL . 'assets/css/admin.css', array(), XLIGHTS_VERSION );
		wp_enqueue_script( 'xlights-admin', XLIGHTS_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable' ), XLIGHTS_VERSION, true );
		wp_localize_script(
			'xlights-admin',
			'XLightsAdmin',
			array(
				'choose'   => __( 'Choose image', 'xlights-elementor-widgets' ),
				'chooseMany' => __( 'Choose gallery images', 'xlights-elementor-widgets' ),
				'use'      => __( 'Use image', 'xlights-elementor-widgets' ),
				'useMany'  => __( 'Add to gallery', 'xlights-elementor-widgets' ),
				'remove'   => __( 'Remove', 'xlights-elementor-widgets' ),
			)
		);
	}

	/**
	 * Items the client still needs to confirm.
	 *
	 * @return array[] [done(bool), label, hint]
	 */
	public static function approvals() {
		$s     = Settings::all();
		$count = static function ( $type, $meta = array() ) {
			$q = new \WP_Query(
				array(
					'post_type'      => $type,
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_query'     => $meta, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				)
			);
			return (int) $q->found_posts;
		};
		return array(
			array( (int) $s['logo'] > 0, __( 'Official logo files (SVG)', 'xlights-elementor-widgets' ), __( 'The PNG logo from the live X LIGHTS website is used until vector files are uploaded in Settings → Brand.', 'xlights-elementor-widgets' ) ),
			array( false, __( 'Approved brand guidelines (colors & fonts)', 'xlights-elementor-widgets' ), __( 'Defaults follow the live X LIGHTS website; confirm with the client, including the dealer logo lockup.', 'xlights-elementor-widgets' ) ),
			array( '' !== trim( $s['service_areas'] ), __( 'Confirmed service areas', 'xlights-elementor-widgets' ), __( 'Settings → Dealer', 'xlights-elementor-widgets' ) ),
			array( '' !== trim( $s['legal_name'] ), __( 'Dealer legal / business name', 'xlights-elementor-widgets' ), __( 'Settings → Dealer', 'xlights-elementor-widgets' ) ),
			array( ! empty( $s['trust_verified'] ), __( 'License details and insurance wording', 'xlights-elementor-widgets' ), __( 'Settings → Credentials & Financing', 'xlights-elementor-widgets' ) ),
			array( ! empty( $s['financing_enabled'] ), __( 'Approved financing details', 'xlights-elementor-widgets' ), __( 'Settings → Credentials & Financing', 'xlights-elementor-widgets' ) ),
			array( '' !== $s['app_store_url'] || '' !== $s['google_play_url'], __( 'App Store / Google Play URLs', 'xlights-elementor-widgets' ), __( 'Settings → App & Social', 'xlights-elementor-widgets' ) ),
			array( '' !== $s['google_reviews_url'], __( 'Google Reviews link', 'xlights-elementor-widgets' ), __( 'Settings → App & Social', 'xlights-elementor-widgets' ) ),
			array( $count( Projects::POST_TYPE ) > 0, __( 'Approved project photographs', 'xlights-elementor-widgets' ), __( 'X LIGHTS → Projects', 'xlights-elementor-widgets' ) ),
			array(
				$count(
					Reviews::POST_TYPE,
					array(
						array(
							'key'   => Reviews::META_PERMISSION,
							'value' => '1',
						),
					)
				) > 0,
				__( 'Customer reviews with permission', 'xlights-elementor-widgets' ),
				__( 'X LIGHTS → Reviews', 'xlights-elementor-widgets' ),
			),
			array( false, __( 'Team names, bios and photos', 'xlights-elementor-widgets' ), __( 'About page → Local Team section', 'xlights-elementor-widgets' ) ),
			array( false, __( 'Exact soffit, bistro, deck & landscape specifications and warranty', 'xlights-elementor-widgets' ), __( 'Technical Specifications sections (rows stay hidden until verified)', 'xlights-elementor-widgets' ) ),
			array( false, __( 'Bond Bridge / smart-home compatibility by product', 'xlights-elementor-widgets' ), __( 'X LIGHTS App page', 'xlights-elementor-widgets' ) ),
			array( false, __( 'Commercial terms (warranty, maintenance, timelines, minimums)', 'xlights-elementor-widgets' ), __( 'Commercial page FAQ', 'xlights-elementor-widgets' ) ),
			array( (bool) get_option( 'wp_page_for_privacy_policy' ) || '' !== $s['privacy_policy_url'], __( 'Privacy policy and lead retention period', 'xlights-elementor-widgets' ), __( 'Settings → Leads & Form', 'xlights-elementor-widgets' ) ),
			array( false, __( 'Hosting, DNS and SSL for the subdomain', 'xlights-elementor-widgets' ), __( 'See docs/DEPLOYMENT.md', 'xlights-elementor-widgets' ) ),
		);
	}

	public static function overview() {
		$status = Page_Setup::status();
		$pages  = count( array_filter( wp_list_pluck( $status, 'has_composer' ) ) );
		$leads  = new \WP_Query(
			array(
				'post_type'      => Leads::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'   => '_xl_status',
						'value' => 'new',
					),
				),
			)
		);
		?>
		<div class="wrap xl-admin">
			<h1><?php esc_html_e( 'X LIGHTS', 'xlights-elementor-widgets' ); ?> <span class="xl-admin__sub"><?php echo esc_html( Settings::get( 'dealer_name' ) ); ?></span></h1>
			<div class="xl-admin__cards">
				<div class="xl-admin__card">
					<h2><?php esc_html_e( 'Pages', 'xlights-elementor-widgets' ); ?></h2>
					<p class="xl-admin__num"><?php echo (int) $pages; ?> / <?php echo count( $status ); ?></p>
					<p><?php esc_html_e( 'pages use the Page Composer', 'xlights-elementor-widgets' ); ?></p>
					<?php if ( current_user_can( 'manage_options' ) ) : ?>
						<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=xlights-setup' ) ); ?>"><?php esc_html_e( 'Page setup', 'xlights-elementor-widgets' ); ?></a>
					<?php endif; ?>
				</div>
				<div class="xl-admin__card">
					<h2><?php esc_html_e( 'Projects', 'xlights-elementor-widgets' ); ?></h2>
					<p class="xl-admin__num"><?php echo (int) wp_count_posts( Projects::POST_TYPE )->publish; ?></p>
					<p><?php esc_html_e( 'published projects', 'xlights-elementor-widgets' ); ?></p>
					<a class="button" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . Projects::POST_TYPE ) ); ?>"><?php esc_html_e( 'Add project', 'xlights-elementor-widgets' ); ?></a>
				</div>
				<div class="xl-admin__card">
					<h2><?php esc_html_e( 'Reviews', 'xlights-elementor-widgets' ); ?></h2>
					<p class="xl-admin__num"><?php echo (int) wp_count_posts( Reviews::POST_TYPE )->publish; ?></p>
					<p><?php esc_html_e( 'published reviews', 'xlights-elementor-widgets' ); ?></p>
					<a class="button" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . Reviews::POST_TYPE ) ); ?>"><?php esc_html_e( 'Add review', 'xlights-elementor-widgets' ); ?></a>
				</div>
				<?php if ( current_user_can( Leads::CAP ) ) : ?>
					<div class="xl-admin__card">
						<h2><?php esc_html_e( 'Leads', 'xlights-elementor-widgets' ); ?></h2>
						<p class="xl-admin__num"><?php echo (int) $leads->found_posts; ?></p>
						<p><?php esc_html_e( 'new leads', 'xlights-elementor-widgets' ); ?></p>
						<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . Leads::POST_TYPE ) ); ?>"><?php esc_html_e( 'View leads', 'xlights-elementor-widgets' ); ?></a>
					</div>
				<?php endif; ?>
			</div>

			<h2><?php esc_html_e( 'Client approval checklist', 'xlights-elementor-widgets' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Unverified items stay hidden or editable placeholders on the live site. Items marked "confirm" cannot be detected automatically.', 'xlights-elementor-widgets' ); ?></p>
			<table class="widefat striped xl-admin__checklist">
				<tbody>
				<?php foreach ( self::approvals() as $item ) : ?>
					<tr>
						<td class="xl-admin__state"><?php echo $item[0] ? '<span class="dashicons dashicons-yes-alt" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Done', 'xlights-elementor-widgets' ) . '</span>' : '<span class="xl-admin__todo">' . esc_html__( 'confirm', 'xlights-elementor-widgets' ) . '</span>'; ?></td>
						<td><strong><?php echo esc_html( $item[1] ); ?></strong></td>
						<td><?php echo esc_html( $item[2] ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<h2><?php esc_html_e( 'Pages', 'xlights-elementor-widgets' ); ?></h2>
			<table class="widefat striped">
				<thead><tr><th><?php esc_html_e( 'Template', 'xlights-elementor-widgets' ); ?></th><th><?php esc_html_e( 'URL', 'xlights-elementor-widgets' ); ?></th><th><?php esc_html_e( 'Status', 'xlights-elementor-widgets' ); ?></th><th></th></tr></thead>
				<tbody>
				<?php foreach ( $status as $row ) : ?>
					<tr>
						<td><?php echo esc_html( $row['label'] ); ?></td>
						<td><code><?php echo esc_html( $row['path'] ); ?></code></td>
						<td>
							<?php
							if ( ! $row['exists'] ) {
								esc_html_e( 'Not created', 'xlights-elementor-widgets' );
							} else {
								echo esc_html( $row['status'] . ( $row['has_composer'] ? ' · Page Composer' : ' · ' . __( 'custom content', 'xlights-elementor-widgets' ) ) );
							}
							?>
						</td>
						<td>
							<?php if ( $row['exists'] && $row['edit'] ) : ?>
								<a href="<?php echo esc_url( $row['edit'] ); ?>"><?php esc_html_e( 'Edit', 'xlights-elementor-widgets' ); ?></a>
								<?php if ( class_exists( '\Elementor\Plugin' ) ) : ?>
									· <a href="<?php echo esc_url( admin_url( 'post.php?post=' . (int) $row['id'] . '&action=elementor' ) ); ?>"><?php esc_html_e( 'Edit with Elementor', 'xlights-elementor-widgets' ); ?></a>
								<?php endif; ?>
								· <a href="<?php echo esc_url( $row['view'] ); ?>"><?php esc_html_e( 'View', 'xlights-elementor-widgets' ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
