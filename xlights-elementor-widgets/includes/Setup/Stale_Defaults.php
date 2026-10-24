<?php
/**
 * Keeps saved Page Composer pages in step with improved default content.
 *
 * When a page is saved in Elementor, the editor stores the current text of
 * every text field and every card list — including ones nobody touched. Those
 * stored copies would hide any later improvement to the default wording,
 * photos in card lists or alt text. On upgrade, a stored value that is
 * exactly what an earlier plugin version shipped as the default is removed,
 * so the field follows the current default again. Anything an editor typed or
 * changed matches no shipped default and is left alone. The previous page data
 * is kept in post meta.
 *
 * The table of shipped defaults (data/shipped-defaults.php) is generated from
 * the plugin's git history with bin/build-shipped-defaults.sh, which runs
 * current_hashes() against each earlier version.
 *
 * @package XLights
 */

namespace XLights\Setup;

use XLights\Pages\Page_Registry;
use XLights\Sections\Component_Registry;

defined( 'ABSPATH' ) || exit;

final class Stale_Defaults {

	const BACKUP_META = '_xlights_backup_pre_refresh';
	const WIDGET      = 'xlights-page-composer';
	const PINNED      = array( 'text', 'textarea', 'wysiwyg', 'repeater' );

	/**
	 * Normalized hash of each stored-type field's default in the loaded
	 * templates, keyed by Page Composer control ID.
	 *
	 * @return array<string,string>
	 */
	public static function current_hashes() {
		$out = array();
		foreach ( Page_Registry::all() as $tpl => $page ) {
			foreach ( Page_Registry::sections( $tpl ) as $sec => $def ) {
				$component = Component_Registry::get( $def['type'] );
				if ( ! $component ) {
					continue;
				}
				$defaults = isset( $def['defaults'] ) ? (array) $def['defaults'] : array();
				foreach ( $component->fields() as $field => $spec ) {
					$type = isset( $spec['type'] ) ? $spec['type'] : 'text';
					if ( ! in_array( $type, self::PINNED, true ) ) {
						continue;
					}
					$value = array_key_exists( $field, $defaults ) ? $defaults[ $field ] : ( isset( $spec['default'] ) ? $spec['default'] : '' );
					if ( 'repeater' === $type ) {
						$value = self::rows( (array) $value, isset( $spec['fields'] ) ? (array) $spec['fields'] : array() );
					}
					$out[ $tpl . '__' . $sec . '__' . $field ] = self::hash( $value );
				}
			}
		}
		return $out;
	}

	/**
	 * Default card-list rows the way the editor fills them: every field of the
	 * row schema, with the field default where the row has no value.
	 *
	 * @param array $items  Rows.
	 * @param array $fields Row schema.
	 * @return array
	 */
	private static function rows( array $items, array $fields ) {
		$out = array();
		foreach ( $items as $item ) {
			$row = array();
			foreach ( $fields as $key => $field ) {
				$row[ $key ] = is_array( $item ) && array_key_exists( $key, $item ) ? $item[ $key ] : ( isset( $field['default'] ) ? $field['default'] : '' );
			}
			$out[] = $row;
		}
		return $out;
	}

	/**
	 * Comparable form of a stored or default value: media and links reduced to
	 * their URL, bundled-image URLs made independent of the site address,
	 * Elementor's row IDs and empty values dropped, line endings unified.
	 *
	 * @param mixed $value Value.
	 * @return mixed
	 */
	public static function normalize( $value ) {
		if ( is_array( $value ) ) {
			if ( array_key_exists( 'url', $value ) && ! array_key_exists( 'title', $value ) ) {
				return self::normalize( (string) $value['url'] );
			}
			if ( array_key_exists( 'value', $value ) && array_key_exists( 'library', $value ) ) {
				return self::normalize( is_string( $value['value'] ) ? $value['value'] : '' );
			}
			$list = array_keys( $value ) === range( 0, count( $value ) - 1 );
			$out  = array();
			foreach ( $value as $k => $v ) {
				if ( '_id' === $k ) {
					continue;
				}
				$n = self::normalize( $v );
				if ( '' === $n || array() === $n ) {
					continue;
				}
				$out[ $k ] = $n;
			}
			if ( $list ) {
				return array_values( $out );
			}
			ksort( $out );
			return $out;
		}
		if ( is_bool( $value ) ) {
			return $value ? 'yes' : '';
		}
		if ( null === $value ) {
			return '';
		}
		$s = str_replace( array( "\r\n", "\r" ), "\n", trim( (string) $value ) );
		return (string) preg_replace( '#^https?://[^\s"\']*?/wp-content/plugins/xlights-elementor-widgets/#', '{plugin}/', $s );
	}

	/**
	 * @param mixed $value Value.
	 * @return string
	 */
	public static function hash( $value ) {
		return md5( (string) wp_json_encode( self::normalize( $value ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
	}

	/**
	 * Shipped defaults table: control ID => list of hashes.
	 *
	 * @return array<string,string[]>
	 */
	private static function shipped() {
		$file = __DIR__ . '/data/shipped-defaults.php';
		$data = is_readable( $file ) ? include $file : array();
		return is_array( $data ) ? $data : array();
	}

	/**
	 * Remove stored values that are an earlier version's untouched default.
	 *
	 * @param array $settings Page Composer widget settings.
	 * @param array $shipped  Shipped hashes.
	 * @param array $current  Current hashes.
	 * @return array [settings, removed keys]
	 */
	public static function refresh_settings( array $settings, array $shipped, array $current ) {
		$removed = array();
		foreach ( $settings as $key => $value ) {
			if ( ! isset( $shipped[ $key ] ) ) {
				continue;
			}
			$hash = self::hash( $value );
			if ( in_array( $hash, $shipped[ $key ], true ) && ( ! isset( $current[ $key ] ) || $current[ $key ] !== $hash ) ) {
				unset( $settings[ $key ] );
				$removed[] = $key;
			}
		}
		return array( $settings, $removed );
	}

	/**
	 * @param array $elements Elementor elements.
	 * @param array $shipped  Shipped hashes.
	 * @param array $current  Current hashes.
	 * @param int   $count    Removed values (by reference).
	 * @return array
	 */
	private static function walk( array $elements, array $shipped, array $current, &$count ) {
		foreach ( $elements as $i => $el ) {
			if ( ! is_array( $el ) ) {
				continue;
			}
			if ( isset( $el['widgetType'] ) && self::WIDGET === $el['widgetType'] && isset( $el['settings'] ) && is_array( $el['settings'] ) ) {
				list( $settings, $removed ) = self::refresh_settings( $el['settings'], $shipped, $current );
				if ( $removed ) {
					$elements[ $i ]['settings'] = $settings;
					$count                     += count( $removed );
				}
			}
			if ( ! empty( $el['elements'] ) && is_array( $el['elements'] ) ) {
				$elements[ $i ]['elements'] = self::walk( $el['elements'], $shipped, $current, $count );
			}
		}
		return $elements;
	}

	/**
	 * Refresh every page that uses the Page Composer.
	 *
	 * @return array{posts:int,values:int}
	 */
	public static function run() {
		global $wpdb;
		$shipped = self::shipped();
		if ( ! $shipped ) {
			return array(
				'posts'  => 0,
				'values' => 0,
			);
		}
		$current = self::current_hashes();
		$ids     = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT pm.post_id FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = '_elementor_data' AND pm.meta_value LIKE %s AND p.post_type <> 'revision' AND p.post_status NOT IN ( 'auto-draft', 'trash', 'inherit' )",
				'%' . $wpdb->esc_like( self::WIDGET ) . '%'
			)
		);
		$result  = array(
			'posts'  => 0,
			'values' => 0,
		);
		foreach ( array_unique( array_map( 'intval', $ids ) ) as $post_id ) {
			$raw  = (string) get_post_meta( $post_id, '_elementor_data', true );
			$data = json_decode( $raw, true );
			if ( ! is_array( $data ) ) {
				continue;
			}
			$count = 0;
			$data  = self::walk( $data, $shipped, $current, $count );
			if ( ! $count ) {
				continue;
			}
			update_post_meta( $post_id, self::BACKUP_META, wp_slash( $raw ) );
			update_post_meta( $post_id, '_elementor_data', wp_slash( (string) wp_json_encode( $data ) ) );
			delete_post_meta( $post_id, '_elementor_element_cache' );
			delete_post_meta( $post_id, '_elementor_css' );
			++$result['posts'];
			$result['values'] += $count;
		}
		return $result;
	}
}
