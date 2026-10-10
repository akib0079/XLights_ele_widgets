<?php
/**
 * Global font families for the X LIGHTS widgets, header and footer.
 *
 * A font setting holds one of:
 *  - ''                 the bundled default (Barlow Condensed for headings, Barlow for text)
 *  - '@barlow', '@barlow-condensed'           bundled with the plugin (self-hosted)
 *  - '@system-sans', '@system-serif'          device fonts, nothing to download
 *  - '@elementor-primary|secondary|text|accent'  Elementor Site Settings → Global Fonts
 *  - a family name      any font Elementor knows (Google, system, custom/Pro fonts),
 *                       loaded through Elementor so its "Load Google Fonts locally"
 *                       option applies; unknown names are assumed to be loaded by the theme.
 *
 * @package XLights
 */

namespace XLights\Frontend;

use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Typography {

	const BUNDLED = array(
		'@barlow-condensed' => '"XL Barlow Condensed", "Barlow Condensed", "Arial Narrow", ui-sans-serif, system-ui, sans-serif',
		'@barlow'           => '"XL Barlow", "Barlow", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
	);

	const SYSTEM = array(
		'@system-sans'  => 'ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
		'@system-serif' => 'ui-serif, Georgia, Cambria, "Times New Roman", Times, serif',
	);

	const ELEMENTOR = array( 'primary', 'secondary', 'text', 'accent' );

	/**
	 * @param string $role heading|body.
	 * @return string Token used when the setting is empty.
	 */
	public static function default_token( $role ) {
		return 'heading' === $role ? '@barlow-condensed' : '@barlow';
	}

	/**
	 * Validate a stored or submitted value.
	 *
	 * @param string $value Raw value.
	 * @return string Clean value ('' when invalid).
	 */
	public static function sanitize( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}
		if ( isset( self::BUNDLED[ $value ] ) || isset( self::SYSTEM[ $value ] ) ) {
			return $value;
		}
		if ( preg_match( '/^@elementor-(' . implode( '|', self::ELEMENTOR ) . ')$/', $value ) ) {
			return $value;
		}
		// A family name: letters, digits, spaces and a few separators — never quotes,
		// semicolons or braces, so it cannot break out of the CSS declaration.
		$value = trim( preg_replace( '/\s+/', ' ', $value ), ' ' );
		return preg_match( '/^[\p{L}\p{N}][\p{L}\p{N} ._\-]{0,63}$/u', $value ) ? $value : '';
	}

	/**
	 * Resolve a setting to a CSS stack and the family Elementor should load.
	 *
	 * @param string $value Setting value.
	 * @param string $role  heading|body.
	 * @return array{stack:string,load:string,bundled:string}
	 */
	public static function resolve( $value, $role ) {
		$value = self::sanitize( $value );
		if ( '' === $value ) {
			$value = self::default_token( $role );
		}
		if ( isset( self::BUNDLED[ $value ] ) ) {
			return array(
				'stack'   => self::BUNDLED[ $value ],
				'load'    => '',
				'bundled' => $value,
			);
		}
		if ( isset( self::SYSTEM[ $value ] ) ) {
			return array(
				'stack'   => self::SYSTEM[ $value ],
				'load'    => '',
				'bundled' => '',
			);
		}
		if ( 0 === strpos( $value, '@elementor-' ) ) {
			$family = self::elementor_global( substr( $value, 11 ) );
			return '' !== $family ? self::resolve( $family, $role ) : self::resolve( '', $role );
		}
		$generic = 'serif' === self::elementor_category( $value ) ? self::SYSTEM['@system-serif'] : self::SYSTEM['@system-sans'];
		return array(
			'stack'   => '"' . $value . '", ' . $generic,
			'load'    => self::elementor_knows( $value ) ? $value : '',
			'bundled' => '',
		);
	}

	/**
	 * Family set in Elementor → Site Settings → Global Fonts.
	 *
	 * @param string $id primary|secondary|text|accent.
	 * @return string
	 */
	public static function elementor_global( $id ) {
		if ( ! did_action( 'elementor/loaded' ) || ! isset( \Elementor\Plugin::$instance->kits_manager ) ) {
			return '';
		}
		$kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();
		if ( ! $kit ) {
			return '';
		}
		$items = (array) $kit->get_settings( 'system_typography' );
		foreach ( $items as $item ) {
			if ( isset( $item['_id'], $item['typography_font_family'] ) && $id === $item['_id'] ) {
				return self::sanitize( $item['typography_font_family'] );
			}
		}
		return '';
	}

	/**
	 * @param string $family Family name.
	 * @return bool Elementor can load it (Google, early access or a custom/Pro font).
	 */
	private static function elementor_knows( $family ) {
		if ( ! class_exists( '\Elementor\Fonts' ) ) {
			return false;
		}
		$type = \Elementor\Fonts::get_font_type( $family );
		return false !== $type && \Elementor\Fonts::SYSTEM !== $type;
	}

	/**
	 * Rough generic family for fallbacks (Google's list does not carry categories
	 * in Elementor, so this only recognizes common serif names).
	 *
	 * @param string $family Family name.
	 * @return string serif|sans
	 */
	private static function elementor_category( $family ) {
		if ( preg_match( '/sans/i', $family ) ) {
			return 'sans';
		}
		return preg_match( '/serif|playfair|merriweather|lora|georgia|garamond|times|baskerville|bodoni|cormorant|crimson|caslon|fraunces/i', $family ) ? 'serif' : 'sans';
	}

	/**
	 * Options for the settings screen, grouped.
	 *
	 * @param string $role heading|body.
	 * @return array Group label => [value => label].
	 */
	public static function choices( $role ) {
		$groups = array(
			__( 'Bundled with the plugin (no external requests)', 'xlights-elementor-widgets' ) => array(
				''                  => 'heading' === $role ? __( 'Barlow Condensed — X LIGHTS default', 'xlights-elementor-widgets' ) : __( 'Barlow — X LIGHTS default', 'xlights-elementor-widgets' ),
				'@barlow-condensed' => __( 'Barlow Condensed', 'xlights-elementor-widgets' ),
				'@barlow'           => __( 'Barlow', 'xlights-elementor-widgets' ),
			),
			__( 'Device fonts (nothing to download)', 'xlights-elementor-widgets' ) => array(
				'@system-sans'  => __( 'System sans-serif', 'xlights-elementor-widgets' ),
				'@system-serif' => __( 'System serif', 'xlights-elementor-widgets' ),
			),
		);
		// The role's default is the first entry; do not list the same face twice.
		unset( $groups[ __( 'Bundled with the plugin (no external requests)', 'xlights-elementor-widgets' ) ][ self::default_token( $role ) ] );

		if ( class_exists( '\Elementor\Fonts' ) ) {
			$labels  = array(
				'primary'   => __( 'Elementor global font: Primary', 'xlights-elementor-widgets' ),
				'secondary' => __( 'Elementor global font: Secondary', 'xlights-elementor-widgets' ),
				'text'      => __( 'Elementor global font: Text', 'xlights-elementor-widgets' ),
				'accent'    => __( 'Elementor global font: Accent', 'xlights-elementor-widgets' ),
			);
			$globals = array();
			foreach ( self::ELEMENTOR as $id ) {
				$family = self::elementor_global( $id );

				$globals[ '@elementor-' . $id ] = $labels[ $id ] . ( '' !== $family ? ' (' . $family . ')' : '' );
			}
			$groups[ __( 'Elementor → Site Settings → Global Fonts', 'xlights-elementor-widgets' ) ] = $globals;

			$by_type = array();
			foreach ( \Elementor\Fonts::get_fonts() as $family => $type ) {
				$by_type[ $type ][ $family ] = $family;
			}
			$type_labels = array(
				\Elementor\Fonts::SYSTEM      => __( 'Web-safe fonts', 'xlights-elementor-widgets' ),
				\Elementor\Fonts::GOOGLE      => __( 'Google Fonts (loaded by Elementor)', 'xlights-elementor-widgets' ),
				\Elementor\Fonts::EARLYACCESS => __( 'Google Fonts, early access (loaded by Elementor)', 'xlights-elementor-widgets' ),
			);
			foreach ( $by_type as $type => $families ) {
				if ( in_array( $type, array( \Elementor\Fonts::GOOGLE, \Elementor\Fonts::EARLYACCESS ), true ) && ! \Elementor\Fonts::is_google_fonts_enabled() ) {
					continue;
				}
				ksort( $families, SORT_NATURAL | SORT_FLAG_CASE );
				$label            = isset( $type_labels[ $type ] ) ? $type_labels[ $type ] : __( 'Custom fonts (Elementor)', 'xlights-elementor-widgets' );
				$groups[ $label ] = isset( $groups[ $label ] ) ? $groups[ $label ] + $families : $families;
			}
		}
		return $groups;
	}

	/**
	 * CSS custom properties for the font settings (empty when on defaults).
	 *
	 * @param array $s Settings.
	 * @return array
	 */
	public static function tokens( array $s ) {
		$vars = array();
		foreach ( array(
			'heading' => '--xl-font-heading',
			'body'    => '--xl-font-body',
		) as $role => $var ) {
			$key   = 'font_' . $role;
			$value = isset( $s[ $key ] ) ? self::sanitize( $s[ $key ] ) : '';
			if ( '' !== $value && self::default_token( $role ) !== $value ) {
				$vars[ $var ] = self::resolve( $value, $role )['stack'];
			}
		}
		if ( isset( $s['heading_case'] ) && 'none' === $s['heading_case'] ) {
			$vars['--xl-heading-case'] = 'none';
		}
		$scale = isset( $s['heading_scale'] ) ? (int) $s['heading_scale'] : 100;
		if ( 100 !== $scale && $scale >= 60 && $scale <= 140 ) {
			$vars['--xl-heading-scale'] = rtrim( rtrim( number_format( $scale / 100, 2, '.', '' ), '0' ), '.' );
		}
		return $vars;
	}

	/**
	 * Ask Elementor to load the selected families (fires before wp_head prints fonts).
	 */
	public static function enqueue() {
		if ( ! did_action( 'elementor/loaded' ) || ! isset( \Elementor\Plugin::$instance->frontend ) ) {
			return;
		}
		foreach ( array( 'heading', 'body' ) as $role ) {
			$font = self::resolve( (string) Settings::get( 'font_' . $role ), $role );
			if ( '' !== $font['load'] ) {
				\Elementor\Plugin::$instance->frontend->enqueue_font( $font['load'] );
			}
		}
	}

	/**
	 * Bundled font files worth preloading for the current settings.
	 *
	 * @return string[] File names in assets/fonts/.
	 */
	public static function preload_files() {
		$files = array();
		if ( '@barlow-condensed' === self::resolve( (string) Settings::get( 'font_heading' ), 'heading' )['bundled'] ) {
			$files[] = 'barlow-condensed-300.woff2';
		}
		if ( '@barlow' === self::resolve( (string) Settings::get( 'font_body' ), 'body' )['bundled'] ) {
			$files[] = 'barlow-400.woff2';
		}
		return $files;
	}
}
