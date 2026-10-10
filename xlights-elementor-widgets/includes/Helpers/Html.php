<?php
/**
 * Markup helpers shared by all components. Every helper escapes its output.
 *
 * @package XLights
 */

namespace XLights\Helpers;

defined( 'ABSPATH' ) || exit;

final class Html {

	/**
	 * Inline tags allowed in headings (accent highlights and line breaks).
	 *
	 * @return array
	 */
	private static function heading_tags() {
		return array(
			'em'     => array(),
			'strong' => array(),
			'br'     => array(),
			'span'   => array( 'class' => true ),
		);
	}

	/**
	 * @param array $attrs Attribute map; null/false values are skipped, true renders a bare attribute.
	 * @return string
	 */
	public static function attrs( array $attrs ) {
		$out = '';
		foreach ( $attrs as $name => $value ) {
			if ( null === $value || false === $value || '' === $value && 'alt' !== $name ) {
				continue;
			}
			$name = preg_replace( '/[^a-zA-Z0-9_:\-]/', '', (string) $name );
			if ( true === $value ) {
				$out .= ' ' . $name;
				continue;
			}
			if ( is_array( $value ) ) {
				$value = implode( ' ', array_filter( $value ) );
			}
			$out .= ' ' . $name . '="' . ( 'href' === $name || 'src' === $name ? esc_url( $value ) : esc_attr( $value ) ) . '"';
		}
		return $out;
	}

	/**
	 * Plain text with tokens resolved.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	public static function text( $text ) {
		return esc_html( Tokens::replace( $text ) );
	}

	/**
	 * Multi-line plain text: escaped, line breaks preserved.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	public static function multiline( $text ) {
		return nl2br( esc_html( Tokens::replace( trim( (string) $text ) ) ) );
	}

	/**
	 * Heading text allowing <em>, <strong>, <br>.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	public static function heading_text( $text ) {
		return wp_kses( Tokens::replace( $text ), self::heading_tags() );
	}

	/**
	 * Rich text (WYSIWYG) with tokens resolved and post-level KSES.
	 *
	 * @param string $html HTML.
	 * @return string
	 */
	public static function rich( $html ) {
		$html = trim( (string) $html );
		if ( '' === $html ) {
			return '';
		}
		// Plain paragraphs typed without markup get paragraphs.
		if ( false === strpos( $html, '<' ) ) {
			$html = wpautop( $html );
		}
		return wp_kses_post( Tokens::replace( $html ) );
	}

	/**
	 * Split a textarea into non-empty lines.
	 *
	 * @param string $text Text.
	 * @return string[]
	 */
	public static function lines( $text ) {
		return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
	}

	/**
	 * Normalize an Elementor URL control value (or string) into link attributes.
	 *
	 * @param array|string $link Link.
	 * @return array|null Attributes or null when empty.
	 */
	public static function link_attrs( $link ) {
		if ( is_string( $link ) ) {
			$link = array( 'url' => $link );
		}
		if ( ! is_array( $link ) || empty( $link['url'] ) ) {
			return null;
		}
		$url = Tokens::url( $link['url'] );
		if ( '' === $url ) {
			return null;
		}
		$attrs = array( 'href' => $url );
		$rel   = array();
		if ( ! empty( $link['is_external'] ) ) {
			$attrs['target'] = '_blank';
			$rel[]           = 'noopener';
		}
		if ( ! empty( $link['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}
		if ( $rel ) {
			$attrs['rel'] = implode( ' ', $rel );
		}
		return $attrs;
	}

	/**
	 * Button/link markup.
	 *
	 * @param string       $label Label.
	 * @param array|string $link  Link.
	 * @param string       $style primary|secondary|ghost|light|text.
	 * @param array        $args  icon, class, aria_label, icon_before.
	 * @return string
	 */
	public static function button( $label, $link, $style = 'primary', array $args = array() ) {
		$label = trim( Tokens::replace( (string) $label ) );
		$attrs = self::link_attrs( $link );
		if ( '' === $label || ! $attrs ) {
			return '';
		}
		$icon  = isset( $args['icon'] ) ? $args['icon'] : ( 'text' === $style ? 'arrow-right' : '' );
		$class = array( 'xl-btn', 'xl-btn--' . sanitize_html_class( $style ) );
		if ( ! empty( $args['class'] ) ) {
			$class[] = $args['class'];
		}
		$attrs['class'] = implode( ' ', $class );
		if ( ! empty( $args['aria_label'] ) ) {
			$attrs['aria-label'] = $args['aria_label'];
		}
		if ( isset( $attrs['target'] ) ) {
			$attrs['aria-label'] = $label . ' ' . __( '(opens in a new tab)', 'xlights-elementor-widgets' );
		}
		$before = ! empty( $args['icon_before'] ) ? Icons::svg( $args['icon_before'] ) : '';
		$after  = $icon ? Icons::svg( $icon ) : '';
		return '<a' . self::attrs( $attrs ) . '>' . $before . '<span>' . esc_html( $label ) . '</span>' . $after . '</a>';
	}

	/**
	 * Click-to-call button using the dealer phone.
	 *
	 * @param string $style Button style.
	 * @param string $label Optional label (defaults to "Call {phone}").
	 * @return string
	 */
	public static function call_button( $style = 'secondary', $label = '' ) {
		$phone = (string) \XLights\Settings\Settings::get( 'phone' );
		if ( '' === trim( $phone ) ) {
			return '';
		}
		/* translators: %s: phone number */
		$label = '' !== $label ? $label : sprintf( __( 'Call %s', 'xlights-elementor-widgets' ), $phone );
		return self::button( $label, Tokens::tel_href( $phone ), $style, array( 'icon' => '', 'icon_before' => 'phone' ) );
	}

	/**
	 * Responsive image from an Elementor MEDIA value, attachment ID or URL.
	 *
	 * @param mixed $media Media.
	 * @param array $args  size, class, alt, eager, sizes.
	 * @return string
	 */
	public static function image( $media, array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'size'  => 'large',
				'class' => '',
				'alt'   => null,
				'eager' => false,
				'sizes' => '',
			)
		);

		$id  = 0;
		$url = '';
		if ( is_array( $media ) ) {
			$id  = isset( $media['id'] ) ? absint( $media['id'] ) : 0;
			$url = isset( $media['url'] ) ? (string) $media['url'] : '';
		} elseif ( is_numeric( $media ) ) {
			$id = absint( $media );
		} elseif ( is_string( $media ) ) {
			$url = $media;
		}

		$attrs = array(
			'class'    => $args['class'],
			'loading'  => $args['eager'] ? 'eager' : 'lazy',
			'decoding' => $args['eager'] ? 'auto' : 'async',
		);
		if ( $args['eager'] ) {
			$attrs['fetchpriority'] = 'high';
		}
		if ( $args['sizes'] ) {
			$attrs['sizes'] = $args['sizes'];
		}

		if ( $id && wp_attachment_is_image( $id ) ) {
			if ( null !== $args['alt'] && '' !== $args['alt'] ) {
				$attrs['alt'] = Tokens::replace( $args['alt'] );
			}
			$attrs = array_filter(
				$attrs,
				static function ( $v ) {
					return '' !== $v && null !== $v;
				}
			);
			return wp_get_attachment_image( $id, $args['size'], false, $attrs );
		}

		if ( '' === $url || self::is_placeholder( $url ) ) {
			return '';
		}

		$attrs['src'] = $url;
		$attrs['alt'] = null !== $args['alt'] ? Tokens::replace( $args['alt'] ) : '';
		$dims         = self::local_dimensions( $url );
		if ( $dims ) {
			$attrs['width']  = $dims[0];
			$attrs['height'] = $dims[1];
			$srcset          = self::local_srcset( $url, $dims[0] );
			if ( '' !== $srcset ) {
				$attrs['srcset'] = $srcset;
				$attrs['sizes']  = $args['sizes'] ? $args['sizes'] : '100vw';
			}
		}
		return '<img' . self::attrs( $attrs ) . '>';
	}

	/**
	 * Smaller copies of a bundled image, named "photo-640.webp", "photo-1024.webp"
	 * next to "photo.webp", as a srcset.
	 *
	 * @param string $url   Bundled image URL (already validated by local_dimensions()).
	 * @param int    $width Width of the full-size file.
	 * @return string
	 */
	private static function local_srcset( $url, $width ) {
		static $cache = array();
		if ( array_key_exists( $url, $cache ) ) {
			return $cache[ $url ];
		}
		$set = array();
		if ( preg_match( '/^(.+)\.(webp|jpe?g|png)$/i', $url, $m ) ) {
			$rel = ltrim( substr( $m[1], strlen( XLIGHTS_URL ) ), '/' );
			foreach ( array( 640, 1024, 1600 ) as $w ) {
				if ( $w < $width && is_readable( XLIGHTS_PATH . $rel . '-' . $w . '.' . $m[2] ) ) {
					$set[] = esc_url( $m[1] . '-' . $w . '.' . $m[2] ) . ' ' . $w . 'w';
				}
			}
		}
		if ( $set ) {
			$set[] = esc_url( $url ) . ' ' . (int) $width . 'w';
		}
		$cache[ $url ] = implode( ', ', $set );
		return $cache[ $url ];
	}

	/**
	 * Whether a MEDIA value contains an image.
	 *
	 * @param mixed $media Media.
	 * @return bool
	 */
	public static function has_image( $media ) {
		if ( is_array( $media ) ) {
			if ( ! empty( $media['id'] ) && wp_attachment_is_image( (int) $media['id'] ) ) {
				return true;
			}
			return ! empty( $media['url'] ) && ! self::is_placeholder( $media['url'] );
		}
		if ( is_numeric( $media ) ) {
			return $media > 0 && wp_attachment_is_image( (int) $media );
		}
		return is_string( $media ) && '' !== $media && ! self::is_placeholder( $media );
	}

	/**
	 * Elementor's generic placeholder image is treated as "no image".
	 *
	 * @param string $url URL.
	 * @return bool
	 */
	private static function is_placeholder( $url ) {
		return false !== strpos( (string) $url, '/placeholder.png' );
	}

	/**
	 * Width/height for images bundled with the plugin (prevents layout shift).
	 *
	 * @param string $url URL.
	 * @return array|null
	 */
	private static function local_dimensions( $url ) {
		static $cache = array();
		if ( 0 !== strpos( $url, XLIGHTS_URL ) ) {
			return null;
		}
		if ( ! array_key_exists( $url, $cache ) ) {
			$path          = XLIGHTS_PATH . ltrim( substr( $url, strlen( XLIGHTS_URL ) ), '/' );
			$size          = ( is_readable( $path ) && 0 === validate_file( substr( $url, strlen( XLIGHTS_URL ) ) ) ) ? wp_getimagesize( $path ) : false;
			$cache[ $url ] = $size ? array( (int) $size[0], (int) $size[1] ) : null;
		}
		return $cache[ $url ];
	}

	/**
	 * URL of an image bundled with the plugin.
	 *
	 * @param string $file File name in assets/images.
	 * @return string
	 */
	public static function asset_image( $file ) {
		return XLIGHTS_URL . 'assets/images/' . ltrim( $file, '/' );
	}

	/**
	 * Preset SVG icon, or an Elementor ICONS control value when provided.
	 *
	 * @param string     $name   Preset icon name.
	 * @param array|null $custom Elementor icon value.
	 * @return string
	 */
	public static function icon( $name, $custom = null ) {
		if ( is_array( $custom ) && ! empty( $custom['value'] ) && class_exists( '\Elementor\Icons_Manager' ) ) {
			$html = \Elementor\Icons_Manager::try_get_icon_html(
				$custom,
				array(
					'aria-hidden' => 'true',
					'class'       => 'xl-icon',
				)
			);
			if ( '' !== $html ) {
				return $html;
			}
		}
		return Icons::svg( (string) $name );
	}

	/**
	 * Eyebrow + heading + intro block used at the top of most sections.
	 *
	 * @param array  $data  Section data (eyebrow, heading, text).
	 * @param array  $args  tag, class, id, align.
	 * @return string
	 */
	public static function section_head( array $data, array $args = array() ) {
		$args    = wp_parse_args(
			$args,
			array(
				'tag'   => 'h2',
				'class' => '',
				'id'    => '',
				'text'  => 'text',
			)
		);
		$eyebrow = isset( $data['eyebrow'] ) ? trim( (string) $data['eyebrow'] ) : '';
		$heading = isset( $data['heading'] ) ? trim( (string) $data['heading'] ) : '';
		$text    = isset( $data[ $args['text'] ] ) ? trim( (string) $data[ $args['text'] ] ) : '';
		if ( '' === $eyebrow && '' === $heading && '' === $text ) {
			return '';
		}
		$tag = in_array( $args['tag'], array( 'h1', 'h2', 'h3' ), true ) ? $args['tag'] : 'h2';
		$out = '<header class="' . esc_attr( trim( 'xl-head ' . $args['class'] ) ) . '">';
		if ( '' !== $eyebrow ) {
			$out .= '<p class="xl-eyebrow">' . self::text( $eyebrow ) . '</p>';
		}
		if ( '' !== $heading ) {
			$out .= '<' . $tag . ' class="xl-title xl-' . $tag . '"' . ( $args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : '' ) . '>' . self::heading_text( $heading ) . '</' . $tag . '>';
		}
		if ( '' !== $text ) {
			$out .= '<div class="xl-lead">' . self::rich( $text ) . '</div>';
		}
		$out .= '</header>';
		return $out;
	}
}
