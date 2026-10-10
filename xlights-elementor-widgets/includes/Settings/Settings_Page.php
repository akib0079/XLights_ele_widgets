<?php
/**
 * Dealer settings screen (Settings API, tabbed), plus a test-email tool.
 *
 * @package XLights
 */

namespace XLights\Settings;

use XLights\Forms\Mailer;
use XLights\Frontend\Typography;
use XLights\Helpers\Color;

defined( 'ABSPATH' ) || exit;

final class Settings_Page {

	const SLUG  = 'xlights-settings';
	const GROUP = 'xlights_settings_group';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
		add_action( 'admin_post_xlights_test_email', array( __CLASS__, 'test_email' ) );
		add_action( 'update_option_' . Settings::OPTION, array( __CLASS__, 'after_save' ) );
		add_action( 'wp_ajax_xlights_design_tokens', array( __CLASS__, 'ajax_tokens' ) );
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
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'show_in_rest'      => false,
				'default'           => Settings::defaults(),
			)
		);
	}

	/**
	 * Sanitize the settings form and flag palettes that would be hard to read.
	 *
	 * @param mixed $input Raw input.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$clean = Settings::sanitize( $input );
		foreach ( Color::warnings( Color::palette( $clean ) ) as $i => $message ) {
			/* translators: %s: readability problem */
			add_settings_error( Settings::OPTION, 'xl-contrast-' . $i, sprintf( __( 'Settings saved. Readability check: %s', 'xlights-elementor-widgets' ), $message ), 'warning' );
		}
		return $clean;
	}

	/**
	 * Live preview on the Colors & Fonts tab: the same derived tokens the site uses.
	 */
	public static function ajax_tokens() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( null, 403 );
		}
		check_ajax_referer( 'xlights_design_tokens' );
		$raw   = isset( $_POST['colors'] ) && is_array( $_POST['colors'] ) ? wp_unslash( $_POST['colors'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Each value is validated by Color::normalize().
		$input = array();
		foreach ( array( 'accent', 'ink', 'text', 'dark', 'surface' ) as $key ) {
			$input[ 'color_' . $key ] = isset( $raw[ $key ] ) ? sanitize_text_field( (string) $raw[ $key ] ) : '';
		}
		$palette = Color::palette( $input );
		wp_send_json_success(
			array(
				// Empty for the X LIGHTS defaults: the preview then uses its built-in default tokens.
				'tokens'   => (object) Color::tokens( $palette ),
				'warnings' => Color::warnings( $palette ),
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
			<?php settings_errors(); ?>
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
				<?php if ( 'design' === $active ) : ?>
					<div class="xl-design-layout"><div class="xl-design-layout__fields">
				<?php endif; ?>
				<table class="form-table" role="presentation">
					<tbody>
					<?php
					$group = '';
					foreach ( $tab['fields'] as $key => $field ) :
						if ( ! empty( $field['group'] ) && $field['group'] !== $group ) :
							$group = $field['group'];
							?>
							<tr class="xl-settings__group"><th colspan="2" scope="rowgroup"><h2><?php echo esc_html( $group ); ?></h2></th></tr>
						<?php endif; ?>
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
				<?php if ( 'design' === $active ) : ?>
					</div><?php self::design_preview(); ?></div>
					<p class="xl-settings__reset">
						<button type="button" class="button" data-xl-design-reset><?php esc_html_e( 'Reset colors & fonts to X LIGHTS defaults', 'xlights-elementor-widgets' ); ?></button>
						<span class="description"><?php esc_html_e( 'Fills in the defaults; nothing changes until you save.', 'xlights-elementor-widgets' ); ?></span>
					</p>
				<?php endif; ?>
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
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" data-default="' . esc_attr( (string) $field['default'] ) . '">';
				foreach ( $field['options'] as $opt => $label ) {
					echo '<option value="' . esc_attr( $opt ) . '" ' . selected( $value, $opt, false ) . '>' . esc_html( $label ) . '</option>';
				}
				echo '</select>';
				break;
			case 'color':
				echo '<input type="text" class="xl-color" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '" data-default-color="' . esc_attr( $field['default'] ) . '" data-xl-design="' . esc_attr( str_replace( 'color_', '', $key ) ) . '">';
				break;
			case 'font':
				self::font_field( $key, $field, (string) $value );
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
				echo '<input type="number" class="small-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '"' . ( isset( $field['min'] ) ? ' min="' . (int) $field['min'] . '"' : '' ) . ( isset( $field['max'] ) ? ' max="' . (int) $field['max'] . '"' : '' ) . ( isset( $field['step'] ) ? ' step="' . (int) $field['step'] . '"' : '' ) . ' data-default="' . esc_attr( (string) $field['default'] ) . '">';
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

	/**
	 * Font picker: grouped list plus an "Other…" family name.
	 *
	 * @param string $key   Setting key.
	 * @param array  $field Field.
	 * @param string $value Stored value.
	 */
	private static function font_field( $key, array $field, $value ) {
		$name   = Settings::OPTION . '[' . $key . ']';
		$id     = 'xl-' . $key;
		$role   = isset( $field['role'] ) ? $field['role'] : 'body';
		$value  = Typography::sanitize( $value );
		$groups = Typography::choices( $role );
		$listed = false;
		foreach ( $groups as $options ) {
			if ( array_key_exists( $value, $options ) ) {
				$listed = true;
				break;
			}
		}
		$custom = $listed ? '' : $value;
		echo '<div class="xl-font-field" data-xl-font data-role="' . esc_attr( $role ) . '">';
		echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" data-default="" class="xl-font-field__select">';
		foreach ( $groups as $label => $options ) {
			echo '<optgroup label="' . esc_attr( $label ) . '">';
			foreach ( $options as $opt => $text ) {
				$stack = Typography::resolve( (string) $opt, $role )['stack'];
				echo '<option value="' . esc_attr( (string) $opt ) . '" data-stack="' . esc_attr( $stack ) . '" ' . selected( $listed && (string) $opt === $value, true, false ) . '>' . esc_html( $text ) . '</option>';
			}
			echo '</optgroup>';
		}
		echo '<optgroup label="' . esc_attr__( 'Other', 'xlights-elementor-widgets' ) . '"><option value="__custom" ' . selected( ! $listed, true, false ) . '>' . esc_html__( 'Another font family…', 'xlights-elementor-widgets' ) . '</option></optgroup>';
		echo '</select>';
		echo '<p class="xl-font-field__custom"' . ( $listed ? ' hidden' : '' ) . '><label for="' . esc_attr( $id ) . '-custom">' . esc_html__( 'Font family name', 'xlights-elementor-widgets' ) . '</label><br><input type="text" class="regular-text" id="' . esc_attr( $id ) . '-custom" name="' . esc_attr( Settings::OPTION . '[' . $key . '_custom]' ) . '" value="' . esc_attr( $custom ) . '" placeholder="Montserrat"><span class="description"> ' . esc_html__( 'Must already be loaded by your theme or another plugin.', 'xlights-elementor-widgets' ) . '</span></p>';
		echo '</div>';
	}

	/**
	 * Live preview for the Colors & Fonts tab.
	 */
	private static function design_preview() {
		$data = array(
			'ajax'  => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'xlights_design_tokens' ),
		);
		?>
		<aside class="xl-dp" data-xl-design-preview data-config="<?php echo esc_attr( wp_json_encode( $data ) ); ?>" aria-label="<?php esc_attr_e( 'Preview', 'xlights-elementor-widgets' ); ?>">
			<div class="xl-dp__panel xl-dp__panel--light">
				<p class="xl-dp__eyebrow"><?php esc_html_e( 'Eyebrow label', 'xlights-elementor-widgets' ); ?></p>
				<p class="xl-dp__title"><?php echo wp_kses( __( 'Beautiful every night. <em>Ready for every celebration.</em>', 'xlights-elementor-widgets' ), array( 'em' => array() ) ); ?></p>
				<p class="xl-dp__text"><?php esc_html_e( 'Body text in your chosen font and color, with', 'xlights-elementor-widgets' ); ?> <span class="xl-dp__link"><?php esc_html_e( 'a text link', 'xlights-elementor-widgets' ); ?></span>.</p>
				<p class="xl-dp__muted"><?php esc_html_e( 'Secondary text, captions and notes.', 'xlights-elementor-widgets' ); ?></p>
				<p class="xl-dp__buttons"><span class="xl-dp__btn"><?php esc_html_e( 'Get a quote', 'xlights-elementor-widgets' ); ?></span><span class="xl-dp__btn is-hover"><?php esc_html_e( 'On hover', 'xlights-elementor-widgets' ); ?></span></p>
			</div>
			<div class="xl-dp__panel xl-dp__panel--grey">
				<p class="xl-dp__card-title"><?php esc_html_e( 'Card on a light grey section', 'xlights-elementor-widgets' ); ?></p>
				<p class="xl-dp__text"><?php esc_html_e( 'Text on the grey surface.', 'xlights-elementor-widgets' ); ?></p>
				<p class="xl-dp__eyebrow"><?php esc_html_e( 'Accent label', 'xlights-elementor-widgets' ); ?></p>
			</div>
			<div class="xl-dp__panel xl-dp__panel--dark">
				<p class="xl-dp__eyebrow"><?php esc_html_e( 'On a dark section', 'xlights-elementor-widgets' ); ?></p>
				<p class="xl-dp__title"><?php echo wp_kses( __( 'Your lighting, <em>in your hand.</em>', 'xlights-elementor-widgets' ), array( 'em' => array() ) ); ?></p>
				<p class="xl-dp__text"><?php esc_html_e( 'Text on dark panels and the footer.', 'xlights-elementor-widgets' ); ?></p>
				<p class="xl-dp__buttons"><span class="xl-dp__btn"><?php esc_html_e( 'Get a quote', 'xlights-elementor-widgets' ); ?></span><span class="xl-dp__btn is-hover"><?php esc_html_e( 'On hover', 'xlights-elementor-widgets' ); ?></span></p>
			</div>
			<div class="xl-dp__checks" data-xl-design-checks role="status" aria-live="polite"></div>
			<p class="xl-dp__note description"><?php esc_html_e( 'Live preview. Google Fonts may show a fallback here; on the site they are loaded by Elementor.', 'xlights-elementor-widgets' ); ?></p>
		</aside>
		<?php
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
