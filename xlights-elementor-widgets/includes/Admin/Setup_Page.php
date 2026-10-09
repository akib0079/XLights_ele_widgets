<?php
/**
 * Page setup screen (X LIGHTS → Page Setup).
 *
 * @package XLights
 */

namespace XLights\Admin;

use XLights\Setup\Page_Setup;

defined( 'ABSPATH' ) || exit;

final class Setup_Page {

	const SLUG = 'xlights-setup';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 30 );
		add_action( 'admin_post_xlights_setup', array( __CLASS__, 'handle' ) );
	}

	public static function menu() {
		add_submenu_page( 'xlights', __( 'Page Setup', 'xlights-elementor-widgets' ), __( 'Page Setup', 'xlights-elementor-widgets' ), 'manage_options', self::SLUG, array( __CLASS__, 'render' ) );
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$status  = Page_Setup::status();
		$report  = get_transient( 'xlights_setup_report_' . get_current_user_id() );
		$no_menu = ! has_nav_menu( \XLights\Frontend\Header_Footer::MENU_PRIMARY );
		delete_transient( 'xlights_setup_report_' . get_current_user_id() );
		?>
		<div class="wrap xl-admin">
			<h1><?php esc_html_e( 'X LIGHTS Page Setup', 'xlights-elementor-widgets' ); ?></h1>
			<?php if ( is_array( $report ) ) : ?>
				<div class="notice notice-info"><ul>
					<?php foreach ( $report as $line ) : ?>
						<li class="xl-report xl-report--<?php echo esc_attr( $line[0] ); ?>"><?php echo esc_html( $line[1] ); ?></li>
					<?php endforeach; ?>
				</ul></div>
			<?php endif; ?>
			<?php if ( ! class_exists( '\Elementor\Plugin' ) ) : ?>
				<div class="notice notice-warning"><p><?php esc_html_e( 'Elementor is not active. Pages will still be created and render their default content through the [xlights_page] shortcode, but they cannot be edited visually until Elementor is activated.', 'xlights-elementor-widgets' ); ?></p></div>
			<?php endif; ?>
			<p><?php esc_html_e( 'Creates the dealer pages with the Page Composer already configured. Existing pages with content are never changed unless you tick "Replace content" for that page (the previous Elementor content is backed up in post meta).', 'xlights-elementor-widgets' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="xlights_setup">
				<?php wp_nonce_field( 'xlights_setup' ); ?>
				<table class="widefat striped">
					<thead><tr>
						<td class="check-column"><input type="checkbox" checked data-xl-check-all aria-label="<?php esc_attr_e( 'Select all', 'xlights-elementor-widgets' ); ?>"></td>
						<th><?php esc_html_e( 'Page', 'xlights-elementor-widgets' ); ?></th>
						<th><?php esc_html_e( 'URL', 'xlights-elementor-widgets' ); ?></th>
						<th><?php esc_html_e( 'Current state', 'xlights-elementor-widgets' ); ?></th>
						<th><?php esc_html_e( 'Replace content?', 'xlights-elementor-widgets' ); ?></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $status as $row ) : ?>
						<tr>
							<th class="check-column" scope="row"><input type="checkbox" name="pages[]" value="<?php echo esc_attr( $row['key'] ); ?>" checked aria-label="<?php echo esc_attr( $row['label'] ); ?>"></th>
							<td><strong><?php echo esc_html( $row['label'] ); ?></strong></td>
							<td><code><?php echo esc_html( $row['path'] ); ?></code></td>
							<td>
								<?php
								if ( ! $row['exists'] ) {
									esc_html_e( 'Will be created', 'xlights-elementor-widgets' );
								} elseif ( $row['has_composer'] ) {
									esc_html_e( 'Configured (Page Composer)', 'xlights-elementor-widgets' );
								} elseif ( $row['has_content'] ) {
									esc_html_e( 'Exists with content — kept', 'xlights-elementor-widgets' );
								} else {
									esc_html_e( 'Exists, empty — will be configured', 'xlights-elementor-widgets' );
								}
								?>
							</td>
							<td>
								<?php if ( $row['exists'] && $row['has_content'] ) : ?>
									<label><input type="checkbox" name="overwrite[]" value="<?php echo esc_attr( $row['key'] ); ?>"> <?php esc_html_e( 'Replace', 'xlights-elementor-widgets' ); ?></label>
								<?php else : ?>
									—
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<h2><?php esc_html_e( 'Options', 'xlights-elementor-widgets' ); ?></h2>
				<fieldset class="xl-setup-options">
					<p><label><input type="checkbox" name="publish" value="1"> <?php esc_html_e( 'Publish new pages immediately (otherwise they are created as drafts for review)', 'xlights-elementor-widgets' ); ?></label></p>
					<p><label><input type="checkbox" name="front_page" value="1" checked> <?php esc_html_e( 'Use the Home page as the site front page', 'xlights-elementor-widgets' ); ?></label></p>
					<p><label><input type="checkbox" name="menu" value="1" <?php checked( $no_menu ); ?>> <?php esc_html_e( 'Create the "X LIGHTS Header" menu (skipped if a header menu is already assigned)', 'xlights-elementor-widgets' ); ?></label></p>
					<p>
						<label for="xl-template"><?php esc_html_e( 'Page template', 'xlights-elementor-widgets' ); ?></label>
						<select id="xl-template" name="template">
							<option value="canvas"><?php esc_html_e( 'Elementor Canvas + X LIGHTS header/footer (works with any theme)', 'xlights-elementor-widgets' ); ?></option>
							<option value="header_footer"><?php esc_html_e( 'Elementor Full Width (theme or Theme Builder header/footer)', 'xlights-elementor-widgets' ); ?></option>
						</select>
					</p>
				</fieldset>
				<?php submit_button( __( 'Run page setup', 'xlights-elementor-widgets' ) ); ?>
			</form>
		</div>
		<?php
	}

	public static function handle() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'xlights-elementor-widgets' ), 403 );
		}
		check_admin_referer( 'xlights_setup' );
		$pages     = isset( $_POST['pages'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['pages'] ) ) : array();
		$overwrite = isset( $_POST['overwrite'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['overwrite'] ) ) : array();
		$template  = isset( $_POST['template'] ) && 'header_footer' === $_POST['template'] ? 'header_footer' : 'canvas';
		$report    = Page_Setup::run(
			$pages,
			array(
				'publish'    => ! empty( $_POST['publish'] ),
				'overwrite'  => $overwrite,
				'front_page' => ! empty( $_POST['front_page'] ),
				'menu'       => ! empty( $_POST['menu'] ),
				'template'   => $template,
			)
		);
		set_transient( 'xlights_setup_report_' . get_current_user_id(), $report, 5 * MINUTE_IN_SECONDS );
		wp_safe_redirect( admin_url( 'admin.php?page=' . self::SLUG ) );
		exit;
	}
}
