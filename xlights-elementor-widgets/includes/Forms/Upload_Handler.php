<?php
/**
 * Secure storage for customer photo uploads.
 *
 * Files are validated (count, size, real image type), renamed to random names,
 * re-encoded when possible (strips EXIF/GPS metadata) and stored in a private
 * directory that is denied to web requests. They are served only through the
 * capability-checked File_Access endpoint and never enter the Media Library.
 *
 * @package XLights
 */

namespace XLights\Forms;

use XLights\Settings\Settings;
use WP_Error;

defined( 'ABSPATH' ) || exit;

final class Upload_Handler {

	const DIR = 'xlights-leads';

	/** Allowed extension => mime. */
	const MIMES = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'webp'         => 'image/webp',
	);

	/** Longest edge after optimization. */
	const MAX_EDGE = 2560;

	/** Refuse to decode images above this pixel count (decompression-bomb guard). */
	const MAX_PIXELS = 60000000;

	/**
	 * @return string Absolute base directory (no trailing slash).
	 */
	public static function base_dir() {
		$uploads = wp_upload_dir( null, false );
		return untrailingslashit( $uploads['basedir'] ) . '/' . self::DIR;
	}

	/**
	 * Create the private directory and its access guards.
	 *
	 * @return bool
	 */
	public static function ensure_dir() {
		$base = self::base_dir();
		if ( ! wp_mkdir_p( $base ) ) {
			return false;
		}
		$guards = array(
			'.htaccess'  => "# Deny all direct web access to lead photos.\n<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n\tOrder allow,deny\n\tDeny from all\n</IfModule>\n",
			'index.php'  => "<?php\n// Silence is golden.\n",
			'web.config' => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<configuration><system.webServer><authorization><deny users=\"*\" /></authorization></system.webServer></configuration>\n",
		);
		foreach ( $guards as $file => $contents ) {
			$path = $base . '/' . $file;
			if ( ! file_exists( $path ) ) {
				file_put_contents( $path, $contents ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			}
		}
		return true;
	}

	/**
	 * Resolve a stored relative path safely (prevents traversal).
	 *
	 * @param string $relative Relative path stored in lead meta.
	 * @return string Absolute path or '' when invalid.
	 */
	public static function absolute_path( $relative ) {
		$relative = (string) $relative;
		if ( '' === $relative || ! preg_match( '#^\d{4}/\d{2}/[a-f0-9]{32}/[a-f0-9]{24}\.(jpg|png|webp)$#', $relative ) ) {
			return '';
		}
		return self::base_dir() . '/' . $relative;
	}

	/**
	 * Normalize $_FILES['photos'] into a list.
	 *
	 * @param array $files Raw $_FILES entry.
	 * @return array[]
	 */
	public static function normalize( $files ) {
		if ( ! is_array( $files ) || ! isset( $files['name'] ) ) {
			return array();
		}
		$out = array();
		if ( is_array( $files['name'] ) ) {
			foreach ( $files['name'] as $i => $name ) {
				if ( '' === (string) $name && UPLOAD_ERR_NO_FILE === (int) $files['error'][ $i ] ) {
					continue;
				}
				$out[] = array(
					'name'     => (string) $name,
					'type'     => (string) $files['type'][ $i ],
					'tmp_name' => (string) $files['tmp_name'][ $i ],
					'error'    => (int) $files['error'][ $i ],
					'size'     => (int) $files['size'][ $i ],
				);
			}
		} elseif ( '' !== (string) $files['name'] || UPLOAD_ERR_NO_FILE !== (int) $files['error'] ) {
			$out[] = array(
				'name'     => (string) $files['name'],
				'type'     => (string) $files['type'],
				'tmp_name' => (string) $files['tmp_name'],
				'error'    => (int) $files['error'],
				'size'     => (int) $files['size'],
			);
		}
		return $out;
	}

	/**
	 * Validate uploads without moving them.
	 *
	 * @param array[] $files Normalized files.
	 * @return true|WP_Error
	 */
	public static function validate( array $files ) {
		$max_files = (int) Settings::get( 'upload_max_files', 6 );
		$max_bytes = (int) Settings::get( 'upload_max_mb', 10 ) * MB_IN_BYTES;

		if ( count( $files ) > $max_files ) {
			return new WP_Error(
				'too_many',
				/* translators: %d: max photos */
				sprintf( _n( 'Please upload no more than %d photo.', 'Please upload no more than %d photos.', $max_files, 'xlights-elementor-widgets' ), $max_files )
			);
		}

		foreach ( $files as $file ) {
			if ( UPLOAD_ERR_OK !== $file['error'] ) {
				if ( in_array( $file['error'], array( UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ), true ) ) {
					return new WP_Error( 'too_large', self::size_message( $max_bytes ) );
				}
				return new WP_Error( 'upload_failed', __( 'One of your photos could not be uploaded. Please try again.', 'xlights-elementor-widgets' ) );
			}
			if ( ! is_uploaded_file( $file['tmp_name'] ) && ! apply_filters( 'xlights/uploads/allow_non_uploaded', false ) ) {
				return new WP_Error( 'upload_failed', __( 'Upload could not be verified.', 'xlights-elementor-widgets' ) );
			}
			if ( $file['size'] <= 0 || $file['size'] > $max_bytes || filesize( $file['tmp_name'] ) > $max_bytes ) {
				return new WP_Error( 'too_large', self::size_message( $max_bytes ) );
			}
			if ( ! self::detect_type( $file ) ) {
				return new WP_Error( 'bad_type', __( 'Photos must be JPG, PNG or WEBP images.', 'xlights-elementor-widgets' ) );
			}
		}
		return true;
	}

	/**
	 * @param int $max_bytes Max bytes.
	 * @return string
	 */
	private static function size_message( $max_bytes ) {
		/* translators: %s: size limit, e.g. 10 MB */
		return sprintf( __( 'Each photo must be smaller than %s.', 'xlights-elementor-widgets' ), size_format( $max_bytes ) );
	}

	/**
	 * Verify the real file type (extension, magic bytes and image decode).
	 *
	 * @param array $file Normalized file.
	 * @return array|null [ext, mime] or null.
	 */
	public static function detect_type( array $file ) {
		$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], self::MIMES );
		$ext   = $check['ext'];
		$mime  = $check['type'];
		if ( ! $ext || ! $mime ) {
			return null;
		}
		$info = function_exists( 'wp_getimagesize' ) ? wp_getimagesize( $file['tmp_name'] ) : @getimagesize( $file['tmp_name'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		if ( ! $info || empty( $info['mime'] ) || ! in_array( $info['mime'], array_values( self::MIMES ), true ) ) {
			return null;
		}
		$map = array(
			'image/jpeg' => 'jpg',
			'image/png'  => 'png',
			'image/webp' => 'webp',
		);
		return array( $map[ $info['mime'] ], $info['mime'], (int) $info[0], (int) $info[1] );
	}

	/**
	 * Move validated uploads to private storage.
	 *
	 * @param array[] $files Normalized, validated files.
	 * @return array[]|WP_Error Stored file records.
	 */
	public static function store( array $files ) {
		if ( ! $files ) {
			return array();
		}
		if ( ! self::ensure_dir() ) {
			return new WP_Error( 'storage', __( 'We could not save your photos. Please try again or email them to us.', 'xlights-elementor-widgets' ) );
		}
		$folder = gmdate( 'Y/m' ) . '/' . bin2hex( random_bytes( 16 ) );
		$dir    = self::base_dir() . '/' . $folder;
		if ( ! wp_mkdir_p( $dir ) ) {
			return new WP_Error( 'storage', __( 'We could not save your photos. Please try again or email them to us.', 'xlights-elementor-widgets' ) );
		}

		$stored = array();
		foreach ( $files as $file ) {
			$type = self::detect_type( $file );
			if ( ! $type ) {
				continue;
			}
			list( $ext, $mime, $width, $height ) = $type;
			$name = bin2hex( random_bytes( 12 ) ) . '.' . $ext;
			$dest = $dir . '/' . $name;

			$saved = false;
			if ( $width * $height <= self::MAX_PIXELS ) {
				$saved = self::reencode( $file['tmp_name'], $dest, $mime );
			}
			if ( ! $saved ) {
				$saved = self::move( $file['tmp_name'], $dest );
			}
			if ( ! $saved ) {
				continue;
			}
			@chmod( $dest, 0640 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions.file_system_operations_chmod
			$stored[] = array(
				'path' => $folder . '/' . $name,
				'name' => sanitize_file_name( wp_basename( $file['name'] ) ),
				'mime' => $mime,
				'size' => (int) filesize( $dest ),
			);
		}
		return $stored;
	}

	/**
	 * Resize/re-encode through the WordPress image editor (drops EXIF metadata).
	 *
	 * @param string $src  Temp file.
	 * @param string $dest Destination.
	 * @param string $mime Mime type.
	 * @return bool
	 */
	private static function reencode( $src, $dest, $mime ) {
		$editor = wp_get_image_editor( $src );
		if ( is_wp_error( $editor ) ) {
			return false;
		}
		if ( method_exists( $editor, 'maybe_exif_rotate' ) ) {
			$editor->maybe_exif_rotate();
		}
		$size = $editor->get_size();
		if ( $size['width'] > self::MAX_EDGE || $size['height'] > self::MAX_EDGE ) {
			$editor->resize( self::MAX_EDGE, self::MAX_EDGE, false );
		}
		$editor->set_quality( 85 );
		$result = $editor->save( $dest, $mime );
		if ( is_wp_error( $result ) || empty( $result['path'] ) ) {
			return false;
		}
		if ( $result['path'] !== $dest && file_exists( $result['path'] ) ) {
			// Editors may change the extension; keep our random name.
			return @rename( $result['path'], $dest ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions.rename_rename
		}
		return file_exists( $dest );
	}

	/**
	 * @param string $src  Temp file.
	 * @param string $dest Destination.
	 * @return bool
	 */
	private static function move( $src, $dest ) {
		if ( is_uploaded_file( $src ) ) {
			return @move_uploaded_file( $src, $dest ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		}
		return apply_filters( 'xlights/uploads/allow_non_uploaded', false ) ? @copy( $src, $dest ) : false; // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
}
