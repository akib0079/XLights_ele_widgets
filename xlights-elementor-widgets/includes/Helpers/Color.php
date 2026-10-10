<?php
/**
 * Color math for the global design tokens.
 *
 * The site is themed from five brand colors (X LIGHTS → Settings → Colors &
 * Fonts). Every other shade the stylesheet needs — text-safe accent, accent on
 * dark, tints, hover text, lighter/darker navy — is derived here and checked
 * against WCAG contrast so a new palette cannot produce unreadable text.
 *
 * @package XLights
 */

namespace XLights\Helpers;

defined( 'ABSPATH' ) || exit;

final class Color {

	/** X LIGHTS defaults (live website palette). */
	const DEFAULTS = array(
		'accent'  => '#3367fe',
		'ink'     => '#05070b',
		'text'    => '#1b2130',
		'dark'    => '#131c2d',
		'surface' => '#e9eaed',
	);

	/**
	 * @param string $hex #rgb or #rrggbb.
	 * @return int[]|null [r, g, b] or null when invalid.
	 */
	public static function rgb( $hex ) {
		$hex = ltrim( trim( (string) $hex ), '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
			return null;
		}
		return array( hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
	}

	/**
	 * @param int[] $rgb Channels.
	 * @return string #rrggbb
	 */
	public static function hex( array $rgb ) {
		return sprintf( '#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2] );
	}

	/**
	 * Normalize to lowercase #rrggbb (or the fallback when invalid).
	 *
	 * @param string $hex      Color.
	 * @param string $fallback Fallback color.
	 * @return string
	 */
	public static function normalize( $hex, $fallback ) {
		$rgb = self::rgb( $hex );
		return $rgb ? self::hex( $rgb ) : $fallback;
	}

	/**
	 * Mix $a toward $b.
	 *
	 * @param string $a Color.
	 * @param string $b Color.
	 * @param float  $t Weight of $b (0..1).
	 * @return string
	 */
	public static function mix( $a, $b, $t ) {
		$x = self::rgb( $a );
		$y = self::rgb( $b );
		$t = max( 0.0, min( 1.0, (float) $t ) );
		$o = array();
		for ( $i = 0; $i < 3; $i++ ) {
			$o[] = (int) round( $x[ $i ] + ( $y[ $i ] - $x[ $i ] ) * $t );
		}
		return self::hex( $o );
	}

	/**
	 * WCAG relative luminance.
	 *
	 * @param string $hex Color.
	 * @return float
	 */
	public static function luminance( $hex ) {
		$c = self::rgb( $hex );
		$l = array();
		foreach ( $c as $v ) {
			$v   = $v / 255;
			$l[] = $v <= 0.04045 ? $v / 12.92 : pow( ( $v + 0.055 ) / 1.055, 2.4 );
		}
		return 0.2126 * $l[0] + 0.7152 * $l[1] + 0.0722 * $l[2];
	}

	/**
	 * WCAG contrast ratio.
	 *
	 * @param string $a Color.
	 * @param string $b Color.
	 * @return float
	 */
	public static function contrast( $a, $b ) {
		$x = self::luminance( $a );
		$y = self::luminance( $b );
		return ( max( $x, $y ) + 0.05 ) / ( min( $x, $y ) + 0.05 );
	}

	/**
	 * Move $color toward $toward until it reaches $min contrast against $bg.
	 *
	 * @param string $color  Starting color.
	 * @param string $bg     Background it sits on.
	 * @param float  $min    Minimum ratio.
	 * @param string $toward Usually #000000 (darken) or #ffffff (lighten).
	 * @return string
	 */
	public static function ensure_contrast( $color, $bg, $min, $toward ) {
		$out = $color;
		$t   = 0.0;
		while ( $t < 1.0 && self::contrast( $out, $bg ) < $min ) {
			$t   = min( 1.0, $t + 0.04 );
			$out = self::mix( $color, $toward, $t );
		}
		return $out;
	}

	/**
	 * Whichever of white or $dark reads better on $bg (white wins at 4.5:1).
	 *
	 * @param string $bg   Background.
	 * @param string $dark Dark alternative.
	 * @return string
	 */
	public static function text_on( $bg, $dark ) {
		if ( self::contrast( '#ffffff', $bg ) >= 4.5 ) {
			return '#ffffff';
		}
		return self::contrast( $dark, $bg ) > self::contrast( '#ffffff', $bg ) ? $dark : '#ffffff';
	}

	/**
	 * "r g b" channels for rgb(var(--x-rgb) / alpha).
	 *
	 * @param string $hex Color.
	 * @return string
	 */
	public static function channels( $hex ) {
		return implode( ' ', self::rgb( $hex ) );
	}

	/**
	 * The five brand colors from settings, normalized.
	 *
	 * @param array $s Settings.
	 * @return array
	 */
	public static function palette( array $s ) {
		return array(
			'accent'  => self::normalize( isset( $s['color_accent'] ) ? $s['color_accent'] : '', self::DEFAULTS['accent'] ),
			'ink'     => self::normalize( isset( $s['color_ink'] ) ? $s['color_ink'] : '', self::DEFAULTS['ink'] ),
			'text'    => self::normalize( isset( $s['color_text'] ) ? $s['color_text'] : '', self::DEFAULTS['text'] ),
			'dark'    => self::normalize( isset( $s['color_dark'] ) ? $s['color_dark'] : '', self::DEFAULTS['dark'] ),
			'surface' => self::normalize( isset( $s['color_surface'] ) ? $s['color_surface'] : '', self::DEFAULTS['surface'] ),
		);
	}

	/**
	 * CSS custom properties for a palette. Empty for the X LIGHTS defaults: the
	 * stylesheet already carries the hand-tuned default tokens.
	 *
	 * @param array $p Palette from palette().
	 * @return array Property => value.
	 */
	public static function tokens( array $p ) {
		if ( self::DEFAULTS === $p ) {
			return array();
		}
		$white = '#ffffff';
		$a     = $p['accent'];
		$d     = $p['dark'];
		$s     = $p['surface'];

		$dark2 = self::mix( $d, $white, 0.033 );
		$deep  = self::mix( $d, '#000000', 0.42 );

		// Accent as small text: must read on white and on the light grey surface.
		$accent_text = self::ensure_contrast( self::mix( $a, '#000000', 0.2 ), $s, 4.5, '#000000' );
		$accent_text = self::ensure_contrast( $accent_text, $white, 4.5, '#000000' );
		// Accent as text on dark panels (and the lighter dark cards).
		$on_dark = self::ensure_contrast( self::mix( $a, $white, 0.73 ), $dark2, 4.5, $white );
		// Text muted on light backgrounds.
		$muted = self::ensure_contrast( self::mix( $p['text'], $white, 0.27 ), $s, 4.6, '#000000' );

		return array(
			'--xl-accent'             => $a,
			'--xl-accent-rgb'         => self::channels( $a ),
			// --xl-accent-ink follows --xl-accent-text in the stylesheet; it is not set
			// here because dark surfaces (footer, panels) redefine it on the same element.
			'--xl-accent-text'        => $accent_text,
			'--xl-accent-link'        => self::contrast( $a, $white ) >= 4.5 ? $a : $accent_text,
			'--xl-accent-on-dark'     => $on_dark,
			'--xl-accent-on-dark-rgb' => self::channels( $on_dark ),
			'--xl-accent-glow-rgb'    => self::channels( self::mix( $a, $white, 0.35 ) ),
			'--xl-on-accent'          => self::text_on( $a, $p['ink'] ),
			'--xl-ink'                => $p['ink'],
			'--xl-ink-rgb'            => self::channels( $p['ink'] ),
			'--xl-text'               => $p['text'],
			'--xl-text-muted'         => $muted,
			'--xl-dark'               => $d,
			'--xl-dark-rgb'           => self::channels( $d ),
			'--xl-dark-2'             => $dark2,
			'--xl-dark-3'             => self::mix( $d, $white, 0.07 ),
			'--xl-navy-deep'          => $deep,
			'--xl-deep-rgb'           => self::channels( $deep ),
			'--xl-surface'            => $s,
			'--xl-surface-soft'       => self::mix( $s, $white, 0.45 ),
		);
	}

	/**
	 * Readability problems in a palette (things derivation cannot fix).
	 *
	 * @param array $p Palette from palette().
	 * @return string[] Messages.
	 */
	public static function warnings( array $p ) {
		$white = '#ffffff';
		$out   = array();
		$check = array(
			/* translators: %s: contrast ratio, e.g. "3.2:1" */
			array( $p['text'], $white, __( 'Body text on white is %s — it needs at least 4.5:1. Choose a darker body text color.', 'xlights-elementor-widgets' ) ),
			/* translators: %s: contrast ratio */
			array( $p['text'], $p['surface'], __( 'Body text on the light grey surface is %s — it needs at least 4.5:1. Choose a lighter surface or darker text.', 'xlights-elementor-widgets' ) ),
			/* translators: %s: contrast ratio */
			array( $white, $p['ink'], __( 'White button text on the headings & buttons color is %s — it needs at least 4.5:1. Choose a darker color.', 'xlights-elementor-widgets' ) ),
			/* translators: %s: contrast ratio */
			array( $white, $p['dark'], __( 'White text on the dark sections color is %s — it needs at least 4.5:1. Choose a darker color.', 'xlights-elementor-widgets' ) ),
		);
		foreach ( $check as $c ) {
			$ratio = self::contrast( $c[0], $c[1] );
			if ( $ratio < 4.5 ) {
				$out[] = sprintf( $c[2], number_format_i18n( $ratio, 1 ) . ':1' );
			}
		}
		return $out;
	}
}
