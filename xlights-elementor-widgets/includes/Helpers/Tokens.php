<?php
/**
 * Content tokens. Default copy is written with tokens so the same templates
 * produce correct local copy for any dealer:
 *
 *   {brand} {dealer} {market} {area} {phone} {email} {year} {cta} {cta2}
 *   URL tokens: {page:contact} {quote_url} {tel} {mailto} {google_reviews}
 *
 * @package XLights
 */

namespace XLights\Helpers;

use XLights\Pages\Page_Registry;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Tokens {

	/** @var array|null */
	private static $map = null;

	/**
	 * @return array token => value
	 */
	public static function map() {
		if ( null === self::$map ) {
			$s         = Settings::all();
			self::$map = array(
				'{brand}'  => (string) $s['brand_name'],
				'{dealer}' => (string) $s['dealer_name'],
				'{market}' => (string) $s['market_name'],
				'{area}'   => (string) $s['primary_area'],
				'{phone}'  => (string) $s['phone'],
				'{email}'  => (string) $s['email'],
				'{year}'   => gmdate( 'Y' ),
				'{cta}'    => (string) $s['primary_cta_label'],
				'{cta2}'   => (string) $s['secondary_cta_label'],
			);
			/**
			 * Filter available text tokens.
			 *
			 * @param array $map token => replacement.
			 */
			self::$map = apply_filters( 'xlights/tokens', self::$map );
		}
		return self::$map;
	}

	public static function reset() {
		self::$map = null;
	}

	/**
	 * Replace text tokens. Output must still be escaped by the caller.
	 *
	 * @param string $text Raw text.
	 * @return string
	 */
	public static function replace( $text ) {
		$text = (string) $text;
		if ( false === strpos( $text, '{' ) ) {
			return $text;
		}
		$text = strtr( $text, self::map() );
		if ( false !== strpos( $text, '{' ) ) {
			$text = self::replace_urls( $text );
		}
		return $text;
	}

	/**
	 * Resolve URL tokens inside a string.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	public static function replace_urls( $text ) {
		$text = (string) $text;
		if ( false !== strpos( $text, '{quote_url}' ) ) {
			$quote = (string) Settings::get( 'quote_url', '{page:contact}#quote' );
			$quote = str_replace( '{quote_url}', '', $quote ); // Guard against self-reference.
			$text  = str_replace( '{quote_url}', $quote, $text );
		}
		if ( false !== strpos( $text, '{tel}' ) ) {
			$text = str_replace( '{tel}', self::tel_href( Settings::get( 'phone' ) ), $text );
		}
		if ( false !== strpos( $text, '{google_reviews}' ) ) {
			$text = str_replace( '{google_reviews}', (string) Settings::get( 'google_reviews_url' ), $text );
		}
		if ( false !== strpos( $text, '{mailto}' ) ) {
			$text = str_replace( '{mailto}', 'mailto:' . Settings::get( 'email' ), $text );
		}
		return preg_replace_callback(
			'/\{page:([a-z_]+)\}/',
			static function ( $m ) {
				return Page_Registry::exists( $m[1] ) ? Page_Registry::url( $m[1] ) : $m[0];
			},
			$text
		);
	}

	/**
	 * Resolve a URL (tokens + relative anchors) without escaping.
	 *
	 * @param string $url URL or token.
	 * @return string
	 */
	public static function url( $url ) {
		$url = trim( (string) $url );
		if ( '' === $url ) {
			return '';
		}
		$url = self::replace_urls( strtr( $url, self::map() ) );
		return $url;
	}

	/**
	 * Build a tel: href from a display phone number (US-friendly).
	 *
	 * @param string $phone Display phone.
	 * @return string
	 */
	public static function tel_href( $phone ) {
		$digits = preg_replace( '/\D+/', '', (string) $phone );
		if ( '' === $digits ) {
			return '';
		}
		if ( 10 === strlen( $digits ) ) {
			$digits = '1' . $digits;
		}
		return 'tel:+' . $digits;
	}
}
