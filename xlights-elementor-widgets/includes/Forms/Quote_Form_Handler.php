<?php
/**
 * Quote / consultation form: rendering, validation and submission handling.
 *
 * Protection layers: WordPress nonce (with cache-safe refresh), honeypot,
 * minimum fill time, per-IP rate limit, duplicate-submission guard, optional
 * Cloudflare Turnstile, strict server-side validation and secure uploads.
 *
 * @package XLights
 */

namespace XLights\Forms;

use XLights\PostTypes\Leads;
use XLights\Settings\Settings;
use XLights\Helpers\Tokens;
use WP_Error;

defined( 'ABSPATH' ) || exit;

final class Quote_Form_Handler {

	const ACTION       = 'xlights_quote';
	const NONCE_ACTION = 'xlights_quote_submit';
	const MIN_SECONDS  = 3;

	/** @var array|null Errors for the current request (field => message). */
	private static $field_errors = array();

	public static function init() {
		add_action( 'wp_ajax_' . self::ACTION, array( __CLASS__, 'ajax' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, array( __CLASS__, 'ajax' ) );
		add_action( 'admin_post_' . self::ACTION, array( __CLASS__, 'post' ) );
		add_action( 'admin_post_nopriv_' . self::ACTION, array( __CLASS__, 'post' ) );
		add_action( 'wp_ajax_xlights_quote_token', array( __CLASS__, 'ajax_token' ) );
		add_action( 'wp_ajax_nopriv_xlights_quote_token', array( __CLASS__, 'ajax_token' ) );
	}

	/**
	 * @return array variant => label
	 */
	public static function variants() {
		return array(
			'general'    => __( 'General quote request', 'xlights-elementor-widgets' ),
			'soffit'     => __( 'Permanent soffit lighting', 'xlights-elementor-widgets' ),
			'bistro'     => __( 'Bistro lighting', 'xlights-elementor-widgets' ),
			'deck'       => __( 'Deck lighting', 'xlights-elementor-widgets' ),
			'landscape'  => __( 'Landscape lighting', 'xlights-elementor-widgets' ),
			'commercial' => __( 'Commercial consultation', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * @return array key => label
	 */
	public static function interests() {
		return array(
			'soffit'    => __( 'Permanent Soffit Lighting', 'xlights-elementor-widgets' ),
			'bistro'    => __( 'Hanging Bulb Bistro Lighting', 'xlights-elementor-widgets' ),
			'deck'      => __( 'Deck Lighting', 'xlights-elementor-widgets' ),
			'landscape' => __( 'Landscape Lighting', 'xlights-elementor-widgets' ),
			'not_sure'  => __( 'Not Sure Yet', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * @return array key => label
	 */
	public static function business_types() {
		return array(
			'restaurant'  => __( 'Restaurant / outdoor dining', 'xlights-elementor-widgets' ),
			'dealership'  => __( 'Car dealership', 'xlights-elementor-widgets' ),
			'hospitality' => __( 'Hotel / hospitality', 'xlights-elementor-widgets' ),
			'retail'      => __( 'Retail / shopping center', 'xlights-elementor-widgets' ),
			'office'      => __( 'Office / commercial property', 'xlights-elementor-widgets' ),
			'hoa'         => __( 'HOA / community space', 'xlights-elementor-widgets' ),
			'other'       => __( 'Other business', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * @return array code => name
	 */
	public static function states() {
		return array(
			'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'DC' => 'District of Columbia', 'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland', 'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi', 'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada', 'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York', 'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma', 'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina', 'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' => 'Wyoming', // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing
		);
	}

	/* ------------------------------------------------------------------
	 * Rendering
	 * ---------------------------------------------------------------- */

	/**
	 * Render the form.
	 *
	 * @param array $args variant, submit_label, uid, source.
	 * @return string
	 */
	public static function render_form( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'variant'      => 'general',
				'submit_label' => __( 'Request My Quote', 'xlights-elementor-widgets' ),
				'uid'          => 'xlq' . wp_rand( 1000, 9999 ),
				'source'       => '',
			)
		);
		\XLights\Frontend\Assets::enqueue_form();

		ob_start();
		$form = array(
			'args'      => $args,
			'interests' => self::interests(),
			'business'  => self::business_types(),
			'states'    => self::states(),
			'token'     => self::time_token(),
			'nonce'     => wp_create_nonce( self::NONCE_ACTION ),
			'max_files' => (int) Settings::get( 'upload_max_files', 6 ),
			'max_mb'    => (int) Settings::get( 'upload_max_mb', 10 ),
			'state'     => strtoupper( (string) Settings::get( 'default_state', 'CA' ) ),
			'privacy'   => self::privacy_url(),
			'notice'    => self::redirect_notice(),
			'turnstile' => self::turnstile_enabled() ? (string) Settings::get( 'turnstile_site_key' ) : '',
		);
		include XLIGHTS_PATH . 'templates/partials/quote-form.php';
		return (string) ob_get_clean();
	}

	/**
	 * @return string
	 */
	public static function privacy_url() {
		$url = (string) Settings::get( 'privacy_policy_url' );
		if ( '' === $url && function_exists( 'get_privacy_policy_url' ) ) {
			$url = get_privacy_policy_url();
		}
		return $url;
	}

	/**
	 * Status message after a no-JS redirect.
	 *
	 * @return array|null
	 */
	private static function redirect_notice() {
		if ( empty( $_GET['xl_quote'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return null;
		}
		$state = sanitize_key( wp_unslash( $_GET['xl_quote'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'sent' === $state ) {
			return array(
				'type'    => 'success',
				'message' => Tokens::replace( (string) Settings::get( 'success_message' ) ),
			);
		}
		$code = isset( $_GET['xl_err'] ) ? sanitize_key( wp_unslash( $_GET['xl_err'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return array(
			'type'    => 'error',
			'message' => self::generic_error( $code ),
		);
	}

	/**
	 * @param string $code Error code.
	 * @return string
	 */
	private static function generic_error( $code ) {
		$phone = (string) Settings::get( 'phone' );
		switch ( $code ) {
			case 'rate':
				/* translators: %s: phone */
				return sprintf( __( 'We have received several requests from your connection. Please wait a little while or call us at %s.', 'xlights-elementor-widgets' ), $phone );
			case 'nonce':
				return __( 'Your session expired. Please submit the form again.', 'xlights-elementor-widgets' );
			case 'invalid':
				return __( 'Please review the highlighted fields and try again.', 'xlights-elementor-widgets' );
			default:
				/* translators: %s: phone */
				return sprintf( __( 'Something went wrong while sending your request. Please try again or call us at %s.', 'xlights-elementor-widgets' ), $phone );
		}
	}

	/* ------------------------------------------------------------------
	 * Tokens
	 * ---------------------------------------------------------------- */

	/**
	 * Signed render timestamp for the minimum-fill-time check.
	 *
	 * @return string
	 */
	public static function time_token() {
		$t = (string) time();
		return $t . '.' . substr( hash_hmac( 'sha256', $t, wp_salt( 'nonce' ) . 'xl-time' ), 0, 20 );
	}

	/**
	 * @param string $token Token.
	 * @return int Seconds since render, or -1 when invalid.
	 */
	private static function token_age( $token ) {
		$parts = explode( '.', (string) $token );
		if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
			return -1;
		}
		$expected = substr( hash_hmac( 'sha256', $parts[0], wp_salt( 'nonce' ) . 'xl-time' ), 0, 20 );
		if ( ! hash_equals( $expected, $parts[1] ) ) {
			return -1;
		}
		return time() - (int) $parts[0];
	}

	/**
	 * Fresh nonce + time token for cached pages.
	 */
	public static function ajax_token() {
		nocache_headers();
		wp_send_json_success(
			array(
				'nonce' => wp_create_nonce( self::NONCE_ACTION ),
				'token' => self::time_token(),
			)
		);
	}

	/* ------------------------------------------------------------------
	 * Submission
	 * ---------------------------------------------------------------- */

	public static function ajax() {
		nocache_headers();
		$result = self::handle( $_POST, $_FILES ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified in handle().
		if ( $result['success'] ) {
			wp_send_json_success( $result, 200 );
		}
		$status = 'rate' === $result['code'] ? 429 : ( 'nonce' === $result['code'] ? 403 : 400 );
		wp_send_json_error( $result, $status );
	}

	public static function post() {
		$result = self::handle( $_POST, $_FILES ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified in handle().
		$back   = wp_get_referer();
		$back   = $back ? $back : home_url( '/' );
		$back   = remove_query_arg( array( 'xl_quote', 'xl_err' ), $back );
		$back   = $result['success']
			? add_query_arg( 'xl_quote', 'sent', $back )
			: add_query_arg(
				array(
					'xl_quote' => 'error',
					'xl_err'   => $result['code'],
				),
				$back
			);
		wp_safe_redirect( $back . '#quote' );
		exit;
	}

	/**
	 * Validate and process a submission.
	 *
	 * @param array $post  Raw POST.
	 * @param array $files Raw FILES.
	 * @return array success, code, message, fields, lead_id
	 */
	public static function handle( array $post, array $files ) {
		$post   = wp_unslash( $post );
		$result = array(
			'success' => false,
			'code'    => 'error',
			'message' => self::generic_error( '' ),
			'fields'  => array(),
		);

		// 1. Nonce.
		$nonce = isset( $post['_xl_nonce'] ) ? (string) $post['_xl_nonce'] : '';
		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			$result['code']    = 'nonce';
			$result['message'] = self::generic_error( 'nonce' );
			return $result;
		}

		$success = array(
			'success' => true,
			'code'    => 'ok',
			'message' => Tokens::replace( (string) Settings::get( 'success_message' ) ),
			'fields'  => array(),
		);

		// 2. Honeypot and minimum fill time: respond like a success so bots learn nothing.
		if ( ! empty( $post['xl_website'] ) ) {
			return $success;
		}
		$age = self::token_age( isset( $post['_xl_t'] ) ? $post['_xl_t'] : '' );
		if ( $age < 0 ) {
			$result['code']    = 'nonce';
			$result['message'] = self::generic_error( 'nonce' );
			return $result;
		}
		if ( $age < self::MIN_SECONDS ) {
			return $success;
		}

		// 3. Rate limit (successful submissions, plus a looser cap on all attempts).
		$ip_hash = Rate_Limiter::ip_hash();
		Rate_Limiter::hit( $ip_hash, 'attempt' );
		if ( ! Rate_Limiter::allow( $ip_hash ) ) {
			$result['code']    = 'rate';
			$result['message'] = self::generic_error( 'rate' );
			return $result;
		}

		// 4. Turnstile.
		if ( self::turnstile_enabled() && ! self::verify_turnstile( isset( $post['cf-turnstile-response'] ) ? (string) $post['cf-turnstile-response'] : '' ) ) {
			$result['code']    = 'invalid';
			$result['message'] = __( 'Please complete the verification check and try again.', 'xlights-elementor-widgets' );
			return $result;
		}

		// 5. Fields.
		$lead = self::validate_fields( $post );
		if ( self::$field_errors ) {
			$result['code']    = 'invalid';
			$result['message'] = self::generic_error( 'invalid' );
			$result['fields']  = self::$field_errors;
			return $result;
		}

		// 6. Duplicate guard (double clicks, retries, refresh-resubmits).
		$fingerprint = 'xl_dup_' . md5( strtolower( $lead['email'] ) . '|' . $lead['phone'] . '|' . $lead['message'] . '|' . implode( ',', $lead['interests'] ) . '|' . $lead['address'] );
		if ( get_transient( $fingerprint ) ) {
			return $success;
		}

		// 7. Files.
		$uploads = Upload_Handler::normalize( isset( $files['photos'] ) ? $files['photos'] : array() );
		$valid   = Upload_Handler::validate( $uploads );
		if ( is_wp_error( $valid ) ) {
			$result['code']    = 'invalid';
			$result['message'] = $valid->get_error_message();
			$result['fields']  = array( 'photos' => $valid->get_error_message() );
			return $result;
		}

		set_transient( $fingerprint, 1, 10 * MINUTE_IN_SECONDS );
		Rate_Limiter::hit( $ip_hash, 'submit' );

		$stored = Upload_Handler::store( $uploads );
		if ( is_wp_error( $stored ) ) {
			delete_transient( $fingerprint );
			$result['message'] = $stored->get_error_message();
			return $result;
		}

		// 8. Persist.
		$lead['files']      = $stored;
		$lead['dealer']     = (string) Settings::get( 'dealer_id' );
		$lead['ip_hash']    = $ip_hash;
		$lead['user_agent'] = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 200 ) : '';
		$lead_id            = Leads::create( $lead );
		if ( is_wp_error( $lead_id ) ) {
			delete_transient( $fingerprint );
			return $result;
		}

		// 9. Notify. Mail acceptance by the transport is recorded, not "delivery".
		$mail = Mailer::send_lead( $lead_id );
		update_post_meta( $lead_id, '_xl_mail_status', $mail['ok'] ? 'accepted' : 'failed' );
		update_post_meta( $lead_id, '_xl_mail_error', $mail['ok'] ? '' : $mail['error'] );
		if ( Settings::enabled( 'autoreply_enabled' ) ) {
			Mailer::send_autoreply( $lead_id );
		}
		Webhook::send( $lead_id );

		/**
		 * Fires after a lead is stored and notifications were attempted.
		 *
		 * @param int   $lead_id Lead post ID.
		 * @param array $lead    Sanitized lead data.
		 */
		do_action( 'xlights/lead_created', $lead_id, $lead );

		$success['lead_id'] = $lead_id;
		return $success;
	}

	/**
	 * Sanitize and validate fields. Errors collect in self::$field_errors.
	 *
	 * @param array $post Unslashed POST.
	 * @return array Sanitized lead data.
	 */
	private static function validate_fields( array $post ) {
		self::$field_errors = array();
		$get                = static function ( $key, $max = 200 ) use ( $post ) {
			$v = isset( $post[ $key ] ) && is_scalar( $post[ $key ] ) ? sanitize_text_field( (string) $post[ $key ] ) : '';
			return function_exists( 'mb_substr' ) ? mb_substr( $v, 0, $max ) : substr( $v, 0, $max );
		};

		$lead = array(
			'first_name'    => $get( 'first_name', 80 ),
			'last_name'     => $get( 'last_name', 80 ),
			'phone'         => $get( 'phone', 40 ),
			'email'         => sanitize_email( $get( 'email', 200 ) ),
			'address'       => $get( 'address', 200 ),
			'city'          => $get( 'city', 100 ),
			'state'         => strtoupper( $get( 'state', 2 ) ),
			'zip'           => $get( 'zip', 10 ),
			'property_type' => $get( 'property_type', 20 ),
			'company'       => $get( 'company', 120 ),
			'business_type' => $get( 'business_type', 30 ),
			'message'       => isset( $post['message'] ) && is_scalar( $post['message'] ) ? substr( sanitize_textarea_field( (string) $post['message'] ), 0, 5000 ) : '',
			'variant'       => $get( 'variant', 20 ),
			'source_url'    => isset( $post['source_url'] ) ? esc_url_raw( (string) $post['source_url'], array( 'http', 'https' ) ) : '',
			'interests'     => array(),
		);

		$required = array(
			'first_name' => __( 'Please enter your first name.', 'xlights-elementor-widgets' ),
			'last_name'  => __( 'Please enter your last name.', 'xlights-elementor-widgets' ),
			'address'    => __( 'Please enter the property address.', 'xlights-elementor-widgets' ),
			'city'       => __( 'Please enter the city.', 'xlights-elementor-widgets' ),
		);
		foreach ( $required as $key => $message ) {
			if ( '' === $lead[ $key ] ) {
				self::$field_errors[ $key ] = $message;
			}
		}

		if ( ! is_email( $lead['email'] ) ) {
			self::$field_errors['email'] = __( 'Please enter a valid email address.', 'xlights-elementor-widgets' );
		}

		$digits = preg_replace( '/\D+/', '', $lead['phone'] );
		if ( 11 === strlen( $digits ) && '1' === $digits[0] ) {
			$digits = substr( $digits, 1 );
		}
		if ( 10 !== strlen( $digits ) || in_array( $digits[0], array( '0', '1' ), true ) ) {
			self::$field_errors['phone'] = __( 'Please enter a valid 10-digit US phone number.', 'xlights-elementor-widgets' );
		} else {
			$lead['phone'] = sprintf( '(%s) %s-%s', substr( $digits, 0, 3 ), substr( $digits, 3, 3 ), substr( $digits, 6 ) );
		}

		if ( ! isset( self::states()[ $lead['state'] ] ) ) {
			self::$field_errors['state'] = __( 'Please choose a state.', 'xlights-elementor-widgets' );
		}
		if ( ! preg_match( '/^\d{5}(-\d{4})?$/', $lead['zip'] ) ) {
			self::$field_errors['zip'] = __( 'Please enter a valid ZIP code.', 'xlights-elementor-widgets' );
		}

		if ( ! in_array( $lead['property_type'], array( 'residential', 'commercial' ), true ) ) {
			self::$field_errors['property_type'] = __( 'Please choose residential or commercial.', 'xlights-elementor-widgets' );
		}
		if ( 'commercial' === $lead['property_type'] ) {
			if ( '' === $lead['company'] ) {
				self::$field_errors['company'] = __( 'Please enter your company name.', 'xlights-elementor-widgets' );
			}
			if ( ! isset( self::business_types()[ $lead['business_type'] ] ) ) {
				self::$field_errors['business_type'] = __( 'Please choose the type of property.', 'xlights-elementor-widgets' );
			}
		} else {
			$lead['company']       = '';
			$lead['business_type'] = '';
		}

		$raw = isset( $post['interests'] ) ? (array) $post['interests'] : array();
		foreach ( $raw as $value ) {
			$value = sanitize_key( (string) $value );
			if ( isset( self::interests()[ $value ] ) ) {
				$lead['interests'][] = $value;
			}
		}
		$lead['interests'] = array_values( array_unique( $lead['interests'] ) );
		if ( ! $lead['interests'] ) {
			self::$field_errors['interests'] = __( 'Please choose at least one lighting interest (or "Not sure yet").', 'xlights-elementor-widgets' );
		}

		if ( empty( $post['privacy'] ) ) {
			self::$field_errors['privacy'] = __( 'Please confirm the privacy acknowledgment.', 'xlights-elementor-widgets' );
		}

		if ( ! isset( self::variants()[ $lead['variant'] ] ) ) {
			$lead['variant'] = 'general';
		}
		return $lead;
	}

	/**
	 * Human-readable interests, flagging multiple selections.
	 *
	 * @param array $keys Interest keys.
	 * @return string
	 */
	public static function interests_label( array $keys ) {
		$labels = array();
		foreach ( $keys as $k ) {
			if ( isset( self::interests()[ $k ] ) ) {
				$labels[] = self::interests()[ $k ];
			}
		}
		$services = array_diff( $keys, array( 'not_sure' ) );
		$prefix   = count( $services ) > 1 ? __( 'Multiple services: ', 'xlights-elementor-widgets' ) : '';
		return $prefix . implode( ', ', $labels );
	}

	/* ------------------------------------------------------------------
	 * Turnstile
	 * ---------------------------------------------------------------- */

	/**
	 * @return bool
	 */
	public static function turnstile_enabled() {
		return '' !== (string) Settings::get( 'turnstile_site_key' ) && '' !== (string) Settings::get( 'turnstile_secret' );
	}

	/**
	 * @param string $token Widget token.
	 * @return bool
	 */
	private static function verify_turnstile( $token ) {
		if ( '' === $token ) {
			return false;
		}
		$response = wp_remote_post(
			'https://challenges.cloudflare.com/turnstile/v0/siteverify',
			array(
				'timeout' => 8,
				'body'    => array(
					'secret'   => (string) Settings::get( 'turnstile_secret' ),
					'response' => $token,
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			// Fail open on network errors so genuine leads are not lost; other layers still apply.
			return (bool) apply_filters( 'xlights/turnstile/fail_open', true );
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		return ! empty( $body['success'] );
	}
}
