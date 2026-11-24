<?php
/**
 * Photos X LIGHTS asked never to show again: snow scenes (this is a Southern
 * California dealer) and the old-packaging control box / installer photos.
 *
 * Matching is by file name, so it also catches copies uploaded to the Media
 * Library under their original names. Nothing is deleted: a section whose
 * picked image is retired shows its default photo instead, and retired photos
 * are left out of project cards, galleries and lightboxes.
 *
 * @package XLights
 */

namespace XLights\Helpers;

defined( 'ABSPATH' ) || exit;

final class Retired_Media {

	/**
	 * File-name fragments of retired photos (lowercase).
	 *
	 * @return string[]
	 */
	public static function fingerprints() {
		$list = array(
			// Snow.
			'soffit-winter-',
			'holiday-color-family',
			'c22e9ab9a5ac48118dbafb119488a36f',
			'4429fb_3f6e08fe5ac',
			'dsc09524',
			// Old control box and old-packaging installer photos.
			'installer-controller',
			'installer-roofline',
			'controller-box',
			'324e31418fc4487a8dbf034140e1e54f',
			'7d0b0b_75684da87a2',
			'a51f6c44',
		);
		/**
		 * File-name fragments of photos that must never be shown.
		 *
		 * @param string[] $list Lowercase fragments.
		 */
		return array_values( array_filter( array_map( 'strtolower', (array) apply_filters( 'xlights_retired_media', $list ) ) ) );
	}

	/**
	 * @param string $name File name or URL.
	 * @return bool
	 */
	public static function matches_name( $name ) {
		$name = strtolower( (string) $name );
		if ( '' === $name ) {
			return false;
		}
		foreach ( self::fingerprints() as $f ) {
			if ( false !== strpos( $name, $f ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Whether a media value (attachment ID, URL or Elementor media array) is retired.
	 *
	 * @param mixed $media Media.
	 * @return bool
	 */
	public static function is_retired( $media ) {
		static $cache = array();
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
		if ( '' !== $url && self::matches_name( $url ) ) {
			return true;
		}
		if ( ! $id ) {
			return false;
		}
		if ( ! array_key_exists( $id, $cache ) ) {
			$file         = (string) get_attached_file( $id );
			$cache[ $id ] = self::matches_name( basename( $file ) ) || self::matches_name( (string) get_post_meta( $id, '_wp_attached_file', true ) );
		}
		return $cache[ $id ];
	}

	/**
	 * Replace retired images in section data: a top-level image falls back to
	 * the section default; a card-list image is cleared (the default card photo
	 * is filled in afterwards where one exists).
	 *
	 * @param array $data     Section data.
	 * @param array $fields   Component field schema.
	 * @param array $defaults Section defaults.
	 * @return array
	 */
	public static function scrub( array $data, array $fields, array $defaults = array() ) {
		foreach ( $fields as $key => $spec ) {
			$type = isset( $spec['type'] ) ? $spec['type'] : '';
			if ( ! array_key_exists( $key, $data ) ) {
				continue;
			}
			if ( 'media' === $type && self::is_retired( $data[ $key ] ) ) {
				$fallback     = array_key_exists( $key, $defaults ) ? $defaults[ $key ] : ( isset( $spec['default'] ) ? $spec['default'] : '' );
				$data[ $key ] = self::is_retired( $fallback ) ? '' : $fallback;
			} elseif ( 'repeater' === $type && is_array( $data[ $key ] ) && ! empty( $spec['fields'] ) ) {
				foreach ( $data[ $key ] as $i => $row ) {
					if ( ! is_array( $row ) ) {
						continue;
					}
					foreach ( $spec['fields'] as $sub => $sub_spec ) {
						if ( isset( $sub_spec['type'] ) && 'media' === $sub_spec['type'] && isset( $row[ $sub ] ) && self::is_retired( $row[ $sub ] ) ) {
							$data[ $key ][ $i ][ $sub ] = array(
								'url' => '',
								'id'  => '',
							);
						}
					}
				}
			}
		}
		return $data;
	}
}
