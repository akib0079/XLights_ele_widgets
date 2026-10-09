<?php
/**
 * Fixed-window rate limiting keyed by a salted IP hash (raw IPs are never stored).
 *
 * @package XLights
 */

namespace XLights\Forms;

use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Rate_Limiter {

	/** All attempts may exceed successful submissions by this factor. */
	const ATTEMPT_FACTOR = 4;

	/**
	 * Client IP, honoring proxy headers only when explicitly trusted.
	 *
	 * @return string
	 */
	public static function client_ip() {
		$candidates = array();
		if ( Settings::enabled( 'trust_proxy' ) ) {
			if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
				$candidates[] = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) );
			}
			if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				$parts        = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
				$candidates[] = trim( $parts[0] );
			}
		}
		if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$candidates[] = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}
		foreach ( $candidates as $ip ) {
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}
		return '0.0.0.0';
	}

	/**
	 * @return string
	 */
	public static function ip_hash() {
		return substr( hash_hmac( 'sha256', self::client_ip(), wp_salt( 'auth' ) ), 0, 32 );
	}

	/**
	 * @param string $bucket attempt|submit.
	 * @return int Max hits per window.
	 */
	private static function limit( $bucket ) {
		$max = max( 1, (int) Settings::get( 'rate_limit_max', 5 ) );
		return 'attempt' === $bucket ? $max * self::ATTEMPT_FACTOR : $max;
	}

	/**
	 * @return int Window in seconds.
	 */
	private static function window() {
		return max( 1, (int) Settings::get( 'rate_limit_window', 30 ) ) * MINUTE_IN_SECONDS;
	}

	/**
	 * @param string $hash   IP hash.
	 * @param string $bucket Bucket.
	 * @return string
	 */
	private static function key( $hash, $bucket ) {
		return 'xl_rl_' . $bucket . '_' . $hash;
	}

	/**
	 * Whether another submission is allowed.
	 *
	 * @param string $hash IP hash.
	 * @return bool
	 */
	public static function allow( $hash ) {
		foreach ( array( 'submit', 'attempt' ) as $bucket ) {
			$data = get_transient( self::key( $hash, $bucket ) );
			if ( is_array( $data ) && $data['count'] >= self::limit( $bucket ) && ( time() - $data['start'] ) < self::window() ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Record a hit.
	 *
	 * @param string $hash   IP hash.
	 * @param string $bucket attempt|submit.
	 */
	public static function hit( $hash, $bucket = 'submit' ) {
		$key  = self::key( $hash, $bucket );
		$data = get_transient( $key );
		if ( ! is_array( $data ) || ( time() - $data['start'] ) >= self::window() ) {
			$data = array(
				'count' => 0,
				'start' => time(),
			);
		}
		$data['count']++;
		set_transient( $key, $data, self::window() );
	}
}
