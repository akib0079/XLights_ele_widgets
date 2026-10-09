<?php
/**
 * Lead notifications through wp_mail(), with optional authenticated SMTP
 * configured by wp-config.php constants (secrets stay out of the database):
 *
 *   define( 'XLIGHTS_SMTP_HOST', 'smtp.example.com' );
 *   define( 'XLIGHTS_SMTP_PORT', 587 );
 *   define( 'XLIGHTS_SMTP_SECURE', 'tls' ); // tls|ssl|''
 *   define( 'XLIGHTS_SMTP_USER', 'user' );
 *   define( 'XLIGHTS_SMTP_PASS', 'secret' );
 *
 * A dedicated SMTP plugin (e.g. WP Mail SMTP) works too; when one is active
 * these constants are not required.
 *
 * wp_mail() returning true only means the message was accepted by the mail
 * transport. The plugin records "accepted", never "delivered".
 *
 * @package XLights
 */

namespace XLights\Forms;

use XLights\PostTypes\Leads;
use XLights\Settings\Settings;
use XLights\Helpers\Tokens;

defined( 'ABSPATH' ) || exit;

final class Mailer {

	/** @var string Last captured wp_mail error. */
	private static $last_error = '';

	/** @var string Plain-text alternative for the message being sent. */
	private static $alt_body = '';

	public static function init() {
		add_action( 'phpmailer_init', array( __CLASS__, 'configure_smtp' ), 5 );
	}

	/**
	 * @param \PHPMailer\PHPMailer\PHPMailer $phpmailer Mailer.
	 */
	public static function configure_smtp( $phpmailer ) {
		if ( ! defined( 'XLIGHTS_SMTP_HOST' ) || '' === (string) XLIGHTS_SMTP_HOST ) {
			return;
		}
		$phpmailer->isSMTP();
		$phpmailer->Host     = (string) XLIGHTS_SMTP_HOST; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
		$phpmailer->Port     = defined( 'XLIGHTS_SMTP_PORT' ) ? (int) XLIGHTS_SMTP_PORT : 587; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
		$secure              = defined( 'XLIGHTS_SMTP_SECURE' ) ? (string) XLIGHTS_SMTP_SECURE : 'tls';
		$phpmailer->SMTPSecure = in_array( $secure, array( 'tls', 'ssl' ), true ) ? $secure : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
		if ( defined( 'XLIGHTS_SMTP_USER' ) && '' !== (string) XLIGHTS_SMTP_USER ) {
			$phpmailer->SMTPAuth = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			$phpmailer->Username = (string) XLIGHTS_SMTP_USER; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			$phpmailer->Password = defined( 'XLIGHTS_SMTP_PASS' ) ? (string) XLIGHTS_SMTP_PASS : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
		}
	}

	/**
	 * Remove CR/LF from header values (header-injection guard).
	 *
	 * @param string $value Value.
	 * @return string
	 */
	private static function header_safe( $value ) {
		return trim( preg_replace( '/[\r\n]+/', ' ', (string) $value ) );
	}

	/**
	 * Send through wp_mail() and capture failures.
	 *
	 * @param string|array $to          Recipients.
	 * @param string       $subject     Subject.
	 * @param string       $html        HTML body.
	 * @param string       $text        Plain-text body.
	 * @param array        $headers     Extra headers.
	 * @param array        $attachments Attachment paths.
	 * @return array ok, error
	 */
	public static function send( $to, $subject, $html, $text, array $headers = array(), array $attachments = array() ) {
		self::$last_error = '';
		self::$alt_body   = $text;

		$capture = static function ( $error ) {
			if ( is_wp_error( $error ) ) {
				self::$last_error = $error->get_error_message();
			}
		};
		$alt = static function ( $phpmailer ) {
			if ( '' !== self::$alt_body ) {
				$phpmailer->AltBody = self::$alt_body; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			}
		};
		$from_email = (string) Settings::get( 'from_email' );
		$from_name  = (string) Settings::get( 'from_name' );
		$f_email    = static function ( $default ) use ( $from_email ) {
			return is_email( $from_email ) ? $from_email : $default;
		};
		$f_name     = static function ( $default ) use ( $from_name ) {
			return '' !== $from_name ? self::header_safe( $from_name ) : $default;
		};

		add_action( 'wp_mail_failed', $capture );
		add_action( 'phpmailer_init', $alt, 20 );
		add_filter( 'wp_mail_from', $f_email, 20 );
		add_filter( 'wp_mail_from_name', $f_name, 20 );

		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$ok        = wp_mail( $to, self::header_safe( $subject ), $html, $headers, $attachments );

		remove_action( 'wp_mail_failed', $capture );
		remove_action( 'phpmailer_init', $alt, 20 );
		remove_filter( 'wp_mail_from', $f_email, 20 );
		remove_filter( 'wp_mail_from_name', $f_name, 20 );
		self::$alt_body = '';

		return array(
			'ok'    => (bool) $ok,
			'error' => $ok ? '' : ( '' !== self::$last_error ? self::$last_error : __( 'The mail transport rejected the message.', 'xlights-elementor-widgets' ) ),
		);
	}

	/**
	 * @return string[]
	 */
	public static function recipients() {
		$list = array_filter( array_map( 'trim', explode( ',', (string) Settings::get( 'lead_recipients' ) ) ), 'is_email' );
		if ( ! $list ) {
			$list = array( get_option( 'admin_email' ) );
		}
		return array_values( $list );
	}

	/**
	 * Notify the dealer about a lead.
	 *
	 * @param int $lead_id Lead post ID.
	 * @return array ok, error
	 */
	public static function send_lead( $lead_id ) {
		$lead     = Leads::get( $lead_id );
		$type     = 'commercial' === $lead['property_type'] ? __( 'Commercial', 'xlights-elementor-widgets' ) : __( 'Residential', 'xlights-elementor-widgets' );
		$subject  = sprintf(
			/* translators: 1: request type, 2: name, 3: city, 4: dealer id */
			__( 'New %1$s quote request: %2$s (%3$s) [%4$s]', 'xlights-elementor-widgets' ),
			strtolower( $type ),
			trim( $lead['first_name'] . ' ' . $lead['last_name'] ),
			$lead['city'],
			$lead['dealer']
		);
		$rows     = self::lead_rows( $lead );
		$files    = self::file_links( $lead );
		$admin    = admin_url( 'post.php?post=' . (int) $lead_id . '&action=edit' );
		$html     = self::template(
			'lead',
			array(
				'lead'  => $lead,
				'rows'  => $rows,
				'files' => $files,
				'admin' => $admin,
				'type'  => $type,
			)
		);
		$text     = self::rows_to_text( $rows ) . "\n" . ( $files ? __( 'Photos (login required):', 'xlights-elementor-widgets' ) . "\n" . implode( "\n", wp_list_pluck( $files, 'url' ) ) . "\n" : '' ) . "\n" . __( 'View in WordPress:', 'xlights-elementor-widgets' ) . ' ' . $admin;
		$headers  = array();
		$reply_to = self::header_safe( trim( $lead['first_name'] . ' ' . $lead['last_name'] ) );
		if ( is_email( $lead['email'] ) ) {
			$headers[] = 'Reply-To: ' . str_replace( array( '<', '>', '"', ',' ), '', $reply_to ) . ' <' . $lead['email'] . '>';
		}

		$attachments = array();
		if ( Settings::enabled( 'attach_photos' ) ) {
			$total = 0;
			foreach ( $lead['files'] as $file ) {
				$path = Upload_Handler::absolute_path( $file['path'] );
				if ( $path && is_file( $path ) && ( $total + filesize( $path ) ) <= 15 * MB_IN_BYTES ) {
					$attachments[] = $path;
					$total        += filesize( $path );
				}
			}
		}
		return self::send( self::recipients(), $subject, $html, $text, $headers, $attachments );
	}

	/**
	 * Optional confirmation email to the customer (fixed template; only the first name is personalized).
	 *
	 * @param int $lead_id Lead.
	 * @return array ok, error
	 */
	public static function send_autoreply( $lead_id ) {
		$lead = Leads::get( $lead_id );
		if ( ! is_email( $lead['email'] ) ) {
			return array(
				'ok'    => false,
				'error' => 'invalid email',
			);
		}
		$subject = Tokens::replace( (string) Settings::get( 'autoreply_subject' ) );
		$message = Tokens::replace( (string) Settings::get( 'autoreply_message' ) );
		$first   = substr( preg_replace( '/[^\p{L}\p{M}\s\'\-]/u', '', $lead['first_name'] ), 0, 40 );
		$html    = self::template(
			'autoreply',
			array(
				'first'   => $first,
				'message' => $message,
			)
		);
		/* translators: %s: first name */
		$text = sprintf( __( 'Hi %s,', 'xlights-elementor-widgets' ), $first ) . "\n\n" . $message;
		return self::send( $lead['email'], $subject, $html, $text );
	}

	/**
	 * Send a test email from the settings page.
	 *
	 * @param string $to Recipient.
	 * @return array ok, error
	 */
	public static function send_test( $to ) {
		$html = self::template(
			'autoreply',
			array(
				'first'   => __( 'there', 'xlights-elementor-widgets' ),
				'message' => __( 'This is a test message from the X LIGHTS plugin. If you received it, WordPress can send lead notifications from this site.', 'xlights-elementor-widgets' ),
			)
		);
		return self::send( $to, __( 'X LIGHTS test email', 'xlights-elementor-widgets' ), $html, wp_strip_all_tags( $html ) );
	}

	/**
	 * Label/value rows describing a lead.
	 *
	 * @param array $lead Lead.
	 * @return array[]
	 */
	public static function lead_rows( array $lead ) {
		$business = Quote_Form_Handler::business_types();
		$variants = Quote_Form_Handler::variants();
		$rows     = array(
			array( __( 'Name', 'xlights-elementor-widgets' ), trim( $lead['first_name'] . ' ' . $lead['last_name'] ) ),
			array( __( 'Phone', 'xlights-elementor-widgets' ), $lead['phone'] ),
			array( __( 'Email', 'xlights-elementor-widgets' ), $lead['email'] ),
			array( __( 'Property type', 'xlights-elementor-widgets' ), 'commercial' === $lead['property_type'] ? __( 'Commercial', 'xlights-elementor-widgets' ) : __( 'Residential', 'xlights-elementor-widgets' ) ),
		);
		if ( 'commercial' === $lead['property_type'] ) {
			$rows[] = array( __( 'Company', 'xlights-elementor-widgets' ), $lead['company'] );
			$rows[] = array( __( 'Business type', 'xlights-elementor-widgets' ), isset( $business[ $lead['business_type'] ] ) ? $business[ $lead['business_type'] ] : '' );
		}
		$rows[] = array( __( 'Property address', 'xlights-elementor-widgets' ), trim( $lead['address'] . ', ' . $lead['city'] . ', ' . $lead['state'] . ' ' . $lead['zip'], ', ' ) );
		$rows[] = array( __( 'Lighting interest', 'xlights-elementor-widgets' ), Quote_Form_Handler::interests_label( (array) $lead['interests'] ) );
		$rows[] = array( __( 'Project description', 'xlights-elementor-widgets' ), $lead['message'] );
		$rows[] = array( __( 'Photos', 'xlights-elementor-widgets' ), (string) count( (array) $lead['files'] ) );
		$rows[] = array( __( 'Form', 'xlights-elementor-widgets' ), isset( $variants[ $lead['variant'] ] ) ? $variants[ $lead['variant'] ] : $lead['variant'] );
		$rows[] = array( __( 'Submitted from', 'xlights-elementor-widgets' ), $lead['source_url'] );
		$rows[] = array( __( 'Submitted', 'xlights-elementor-widgets' ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $lead['date'] ) );
		$rows[] = array( __( 'Dealer / location', 'xlights-elementor-widgets' ), $lead['dealer'] . ' — ' . Settings::get( 'dealer_name' ) );
		return $rows;
	}

	/**
	 * Login-protected links to uploaded photos.
	 *
	 * @param array $lead Lead.
	 * @return array[] name, url
	 */
	public static function file_links( array $lead ) {
		$out = array();
		foreach ( (array) $lead['files'] as $i => $file ) {
			$out[] = array(
				'name' => isset( $file['name'] ) ? $file['name'] : sprintf( 'photo-%d', $i + 1 ),
				'url'  => File_Access::url( $lead['id'], $i ),
			);
		}
		return $out;
	}

	/**
	 * @param array $rows Rows.
	 * @return string
	 */
	private static function rows_to_text( array $rows ) {
		$out = '';
		foreach ( $rows as $row ) {
			$out .= $row[0] . ': ' . $row[1] . "\n";
		}
		return $out;
	}

	/**
	 * Render an email template.
	 *
	 * @param string $name Template name.
	 * @param array  $vars Variables.
	 * @return string
	 */
	private static function template( $name, array $vars ) {
		$file = XLIGHTS_PATH . 'templates/email/' . $name . '.php';
		if ( ! is_readable( $file ) ) {
			return '';
		}
		extract( $vars, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract
		ob_start();
		include $file;
		return (string) ob_get_clean();
	}
}
