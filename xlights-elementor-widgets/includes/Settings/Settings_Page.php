<?php
/**
 * Dealer settings screen (Settings API, tabbed), plus a test-email tool.
 *
 * @package XLights
 */

namespace XLights\Settings;

use XLights\Forms\Mailer;

defined( 'ABSPATH' ) || exit;

final class Settings_Page {

	const SLUG  = 'xlights-settings';
	const GROUP = 'xlights_settings_group';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
		add_action( 'admin_post_xlights_test_email', array( __CLASS__, 'test_email' ) );
		add_action( 'update_option_' . Settings::OPTION, array( __CLASS__, 'after_save' ) );
	}

	public static function menu() {
		add_submenu_page(
			'xlights',
			__( 'X LIGHTS Settings', 'xlights-elementor-widgets' ),
			__( 'Settings', 'xlights-elementor-widgets' ),
			'manage_options',
			self::SLUG,
			array( __CLASS__, 'render' )
		);
	}

	public static function register() {
		register_setting(
			self::GROUP,
			Settings::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( Settings::class, 'sanitize' ),
				'show_in_rest'      => false,
				'default'           => Settings::defaults(),
			)
		);
	}

	/**
	 * Clear caches that depend on settings (Elementor CSS includes brand vars).
	 */
	public static function after_save() {
		Settings::flush_cache();
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}

	/**
	 * @return string Active tab.
	 */
	private static function tab() {
		$tabs = Settings::schema();
		$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'dealer'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $tabs[ $tab ] ) ? $tab : 'dealer';
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$schema = Settings::schema();
		$active = self::tab();
		$values = Settings::all();
		?>
		<div class="wrap xl-admin">
			<h1><?php esc_html_e( 'X LIGHTS Settings', 'xlights-elementor-widgets' ); ?></h1>
			<?php self::test_notice(); ?>
			<nav class="nav-tab-wrapper" aria-label="<?php esc_attr_e( 'Settings sections', 'xlights-elementor-widgets' ); ?>">
				<?php foreach ( $schema as $key => $tab ) : ?>
					<a class="nav-tab<?php echo $key === $active ? ' nav-tab-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'page' => self::SLUG, 'tab' => $key ), admin_url( 'admin.php' ) ) ); ?>"<?php echo $key === $active ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $tab['label'] ); ?></a>
				<?php endforeach; ?>
			</nav>
			<form method="post" action="options.php" class="xl-settings">
				<?php settings_fields( self::GROUP ); ?>
				<?php
				// Preserve values from other tabs (the sanitizer treats missing checkboxes as unchecked).
				foreach ( $schema as $key => $tab ) {
					if ( $key === $active ) {
						continue;
					}
					foreach ( $tab['fields'] as $field_key => $field ) {
						if ( 'checkbox' === $field['type'] && empty( $values[ $field_key ] ) ) {
							continue;
						}
						$val = 'secret' === $field['type'] ? '' : $values[ $field_key ];
						echo '<input type="hidden" name="' . esc_attr( Settings::OPTION . '[' . $field_key . ']' ) . '" value="' . esc_attr( (string) $val ) . '">';
					}
				}
				$tab = $schema[ $active ];
				?>
				<?php if ( ! empty( $tab['intro'] ) ) : ?>
					<p class="xl-settings__intro"><?php echo esc_html( $tab['intro'] ); ?></p>
				<?php endif; ?>
				<table class="form-table" role="presentation">
					<tbody>
					<?php foreach ( $tab['fields'] as $key => $field ) : ?>
						<tr>
							<th scope="row">
								<label for="xl-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
								<?php if ( ! empty( $field['approval'] ) ) : ?>
									<span class="xl-approval-tag" title="<?php esc_attr_e( 'Confirm with the client before publishing', 'xlights-elementor-widgets' ); ?>"><?php esc_html_e( 'client approval', 'xlights-elementor-widgets' ); ?></span>
								<?php endif; ?>
							</th>
							<td><?php self::field( $key, $field, $values[ $key ] ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php submit_button(); ?>
			</form>

			<?php if ( 'leads' === $active ) : ?>
				<hr>
				<h2><?php esc_html_e( 'Test email delivery', 'xlights-elementor-widgets' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Sends a test message through wp_mail(). A success here means your mail transport accepted the message — check the inbox (and spam folder) to confirm delivery. Configure authenticated SMTP (see docs) for reliable delivery.', 'xlights-elementor-widgets' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="xlights_test_email">
					<?php wp_nonce_field( 'xlights_test_email' ); ?>
					<label for="xl-test-to" class="screen-reader-text"><?php esc_html_e( 'Send test to', 'xlights-elementor-widgets' ); ?></label>
					<input type="email" id="xl-test-to" name="to" class="regular-text" value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>" required>
					<?php submit_button( __( 'Send test email', 'xlights-elementor-widgets' ), 'secondary', 'submit', false ); ?>
				</form>
				<p class="description">
					<?php
					echo defined( 'XLIGHTS_SMTP_HOST' )
						? esc_html__( 'SMTP: configured through XLIGHTS_SMTP_* constants in wp-config.php.', 'xlights-elementor-widgets' )
						: esc_html__( 'SMTP: not configured by this plugin. Use an SMTP plugin or the XLIGHTS_SMTP_* constants described in docs/DEPLOYMENT.md.', 'xlights-elementor-widgets' );
					?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render a field input.
	 *
	 * @param string $key   Key.
	 * @param array  $field Field.
	 * @param mixed  $value Value.
	 */
	private static function field( $key, array $field, $value ) {
		$name = Settings::OPTION . '[' . $key . ']';
		$id   = 'xl-' . $key;
		$ph   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		switch ( $field['type'] ) {
			case 'textarea':
			case 'lines':
				echo '<textarea class="large-text" rows="' . ( 'lines' === $field['type'] ? 5 : 3 ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">' . esc_textarea( (string) $value ) . '</textarea>';
				break;
			case 'checkbox':
				echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="0">';
				echo '<label><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1" ' . checked( ! empty( $value ), true, false ) . '> ' . esc_html__( 'Enabled', 'xlights-elementor-widgets' ) . '</label>';
				break;
			case 'select':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
				foreach ( $field['options'] as $opt => $label ) {
					echo '<option value="' . esc_attr( $opt ) . '" ' . selected( $value, $opt, false ) . '>' . esc_html( $label ) . '</option>';
				}
				echo '</select>';
				break;
			case 'color':
				echo '<input type="text" class="xl-color" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '" data-default-color="' . esc_attr( $field['default'] ) . '">';
				break;
			case 'media':
				$src = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
				echo '<div class="xl-media-field" data-xl-media>';
				echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '">';
				echo '<div class="xl-media-field__preview">' . ( $src ? '<img src="' . esc_url( $src ) . '" alt="">' : '' ) . '</div>';
				echo '<button type="button" class="button" data-xl-media-pick>' . esc_html__( 'Choose image', 'xlights-elementor-widgets' ) . '</button> ';
				echo '<button type="button" class="button-link-delete" data-xl-media-clear' . ( $value ? '' : ' hidden' ) . '>' . esc_html__( 'Remove', 'xlights-elementor-widgets' ) . '</button>';
				echo '</div>';
				break;
			case 'number':
				echo '<input type="number" class="small-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '"' . ( isset( $field['min'] ) ? ' min="' . (int) $field['min'] . '"' : '' ) . ( isset( $field['max'] ) ? ' max="' . (int) $field['max'] . '"' : '' ) . '>';
				break;
			case 'secret':
				echo '<input type="password" class="regular-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . ( '' !== (string) $value ? '********' : '' ) . '" autocomplete="new-password">';
				break;
			case 'email':
				echo '<input type="email" class="regular-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '">';
				break;
			case 'url':
				echo '<input type="url" class="regular-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '" placeholder="https://">';
				break;
			default:
				echo '<input type="text" class="regular-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '" placeholder="' . esc_attr( $ph ) . '">';
		}
		if ( ! empty( $field['description'] ) ) {
			echo '<p class="description">' . esc_html( $field['description'] ) . '</p>';
		}
	}

	public static function test_email() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'xlights-elementor-widgets' ), 403 );
		}
		check_admin_referer( 'xlights_test_email' );
		$to     = isset( $_POST['to'] ) ? sanitize_email( wp_unslash( $_POST['to'] ) ) : '';
		$result = is_email( $to ) ? Mailer::send_test( $to ) : array(
			'ok'    => false,
			'error' => __( 'Invalid email address.', 'xlights-elementor-widgets' ),
		);
		set_transient( 'xlights_test_email_' . get_current_user_id(), $result, 5 * MINUTE_IN_SECONDS );
		wp_safe_redirect( add_query_arg( array( 'page' => self::SLUG, 'tab' => 'leads' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	private static function test_notice() {
		$key    = 'xlights_test_email_' . get_current_user_id();
		$result = get_transient( $key );
		if ( ! is_array( $result ) ) {
			return;
		}
		delete_transient( $key );
		if ( $result['ok'] ) {
			echo '<div class="notice notice-success"><p>' . esc_html__( 'The test email was accepted by the mail transport. Check the inbox (and spam folder) to confirm delivery.', 'xlights-elementor-widgets' ) . '</p></div>';
		} else {
			/* translators: %s: error */
			echo '<div class="notice notice-error"><p>' . esc_html( sprintf( __( 'The test email failed: %s', 'xlights-elementor-widgets' ), $result['error'] ) ) . '</p></div>';
		}
	}
}
