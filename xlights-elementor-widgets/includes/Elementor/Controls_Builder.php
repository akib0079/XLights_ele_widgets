<?php
/**
 * Turns component field schemas into native Elementor controls.
 *
 * Used by the Page Composer (prefixed per template/section) and by every
 * standalone section widget (unprefixed), so a component's editing experience
 * is identical in both places.
 *
 * @package XLights
 */

namespace XLights\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

final class Controls_Builder {

	/**
	 * Add controls for a field schema.
	 *
	 * @param \Elementor\Controls_Stack $stack    Widget.
	 * @param array                     $fields   Field schema.
	 * @param string                    $prefix   Control ID prefix.
	 * @param array                     $defaults Default overrides (field => value).
	 */
	public static function add( $stack, array $fields, $prefix = '', array $defaults = array() ) {
		foreach ( $fields as $key => $field ) {
			if ( array_key_exists( $key, $defaults ) ) {
				$field['default'] = $defaults[ $key ];
			}
			$control = self::control( $field, $prefix );
			if ( null === $control ) {
				continue;
			}
			$stack->add_control( $prefix . $key, $control );
		}
	}

	/**
	 * Build one control definition.
	 *
	 * @param array  $field  Field.
	 * @param string $prefix Prefix (for conditions).
	 * @return array|null
	 */
	public static function control( array $field, $prefix = '' ) {
		$type = isset( $field['type'] ) ? $field['type'] : 'text';
		$c    = array( 'label' => isset( $field['label'] ) ? $field['label'] : '' );

		foreach ( array( 'description', 'separator', 'placeholder', 'classes' ) as $opt ) {
			if ( isset( $field[ $opt ] ) ) {
				$c[ $opt ] = $field[ $opt ];
			}
		}
		if ( ! empty( $field['condition'] ) ) {
			$c['condition'] = array();
			foreach ( $field['condition'] as $dep => $value ) {
				$c['condition'][ $prefix . $dep ] = $value;
			}
		}
		$default = isset( $field['default'] ) ? $field['default'] : null;

		switch ( $type ) {
			case 'text':
				$c['type']        = Controls_Manager::TEXT;
				$c['label_block'] = true;
				$c['default']     = (string) $default;
				$c['dynamic']     = array( 'active' => true );
				break;
			case 'textarea':
				$c['type']    = Controls_Manager::TEXTAREA;
				$c['rows']    = isset( $field['rows'] ) ? (int) $field['rows'] : 3;
				$c['default'] = (string) $default;
				$c['dynamic'] = array( 'active' => true );
				break;
			case 'wysiwyg':
				$c['type']    = Controls_Manager::WYSIWYG;
				$c['default'] = (string) $default;
				$c['dynamic'] = array( 'active' => true );
				break;
			case 'media':
				$c['type']    = Controls_Manager::MEDIA;
				$c['default'] = self::media_default( $default );
				$c['dynamic'] = array( 'active' => true );
				break;
			case 'url':
				$c['type']        = Controls_Manager::URL;
				$c['default']     = self::url_default( $default );
				$c['label_block'] = true;
				$c['dynamic']     = array( 'active' => true );
				$c['placeholder'] = isset( $field['placeholder'] ) ? $field['placeholder'] : __( 'https://… or {page:contact}', 'xlights-elementor-widgets' );
				break;
			case 'select':
				$c['type']    = Controls_Manager::SELECT;
				$c['options'] = isset( $field['options'] ) ? $field['options'] : array();
				$c['default'] = (string) $default;
				break;
			case 'choose':
				$c['type']    = Controls_Manager::CHOOSE;
				$c['options'] = isset( $field['options'] ) ? $field['options'] : array();
				$c['default'] = (string) $default;
				$c['toggle']  = false;
				break;
			case 'switcher':
				$c['type']         = Controls_Manager::SWITCHER;
				$c['return_value'] = 'yes';
				$c['default']      = 'yes' === $default ? 'yes' : '';
				$c['label_on']     = __( 'Yes', 'xlights-elementor-widgets' );
				$c['label_off']    = __( 'No', 'xlights-elementor-widgets' );
				break;
			case 'number':
				$c['type']    = Controls_Manager::NUMBER;
				$c['default'] = (int) $default;
				foreach ( array( 'min', 'max', 'step' ) as $opt ) {
					if ( isset( $field[ $opt ] ) ) {
						$c[ $opt ] = $field[ $opt ];
					}
				}
				break;
			case 'slider':
				$c['type']       = Controls_Manager::SLIDER;
				$c['size_units'] = array( '%' );
				$c['range']      = array(
					'%' => array(
						'min' => isset( $field['min'] ) ? $field['min'] : 0,
						'max' => isset( $field['max'] ) ? $field['max'] : 100,
					),
				);
				$c['default']    = array(
					'unit' => '%',
					'size' => is_array( $default ) ? $default['size'] : (float) $default,
				);
				break;
			case 'icons':
				$c['type']        = Controls_Manager::ICONS;
				$c['default']     = array(
					'value'   => '',
					'library' => '',
				);
				$c['skin']        = 'inline';
				$c['label_block'] = false;
				break;
			case 'gallery':
				$c['type']    = Controls_Manager::GALLERY;
				$c['default'] = is_array( $default ) ? $default : array();
				break;
			case 'heading':
				$c['type'] = Controls_Manager::HEADING;
				break;
			case 'notice':
				$c['type']            = Controls_Manager::RAW_HTML;
				$c['raw']             = esc_html( isset( $field['text'] ) ? $field['text'] : '' );
				$c['content_classes'] = 'elementor-panel-alert elementor-panel-alert-info';
				unset( $c['label'] );
				break;
			case 'hidden':
				$c['type']    = Controls_Manager::HIDDEN;
				$c['default'] = $default;
				break;
			case 'repeater':
				$repeater = new Repeater();
				foreach ( $field['fields'] as $sub_key => $sub_field ) {
					$sub = self::control( $sub_field, '' );
					if ( $sub ) {
						$repeater->add_control( $sub_key, $sub );
					}
				}
				$c['type']          = Controls_Manager::REPEATER;
				$c['fields']        = $repeater->get_controls();
				$c['title_field']   = isset( $field['title_field'] ) ? $field['title_field'] : '';
				$c['prevent_empty'] = false;
				$c['default']       = self::repeater_default( is_array( $default ) ? $default : array(), $field['fields'] );
				if ( isset( $field['item_actions'] ) ) {
					$c['item_actions'] = $field['item_actions'];
				}
				break;
			default:
				return null;
		}
		return $c;
	}

	/**
	 * @param mixed $default URL string or media array.
	 * @return array
	 */
	public static function media_default( $default ) {
		if ( is_array( $default ) ) {
			return wp_parse_args(
				$default,
				array(
					'url' => '',
					'id'  => '',
				)
			);
		}
		return array(
			'url' => (string) $default,
			'id'  => '',
		);
	}

	/**
	 * @param mixed $default URL string or link array.
	 * @return array
	 */
	public static function url_default( $default ) {
		if ( is_array( $default ) ) {
			return wp_parse_args(
				$default,
				array(
					'url'         => '',
					'is_external' => '',
					'nofollow'    => '',
				)
			);
		}
		return array(
			'url'         => (string) $default,
			'is_external' => '',
			'nofollow'    => '',
		);
	}

	/**
	 * Normalize repeater default items (fill missing keys, convert media/url).
	 *
	 * @param array $items  Items.
	 * @param array $fields Item schema.
	 * @return array
	 */
	public static function repeater_default( array $items, array $fields ) {
		$out = array();
		foreach ( $items as $item ) {
			$row = array();
			foreach ( $fields as $key => $field ) {
				$value = array_key_exists( $key, $item ) ? $item[ $key ] : ( isset( $field['default'] ) ? $field['default'] : '' );
				switch ( isset( $field['type'] ) ? $field['type'] : 'text' ) {
					case 'media':
						$value = self::media_default( $value );
						break;
					case 'url':
						$value = self::url_default( $value );
						break;
					case 'icons':
						$value = is_array( $value ) ? $value : array(
							'value'   => '',
							'library' => '',
						);
						break;
					case 'switcher':
						$value = 'yes' === $value || true === $value ? 'yes' : '';
						break;
				}
				$row[ $key ] = $value;
			}
			$out[] = $row;
		}
		return $out;
	}
}
