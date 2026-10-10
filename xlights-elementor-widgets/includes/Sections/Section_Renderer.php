<?php
/**
 * Renders sections and full page templates.
 *
 * @package XLights
 */

namespace XLights\Sections;

use XLights\Helpers\Html;
use XLights\Helpers\Illustrations;
use XLights\Pages\Page_Registry;

defined( 'ABSPATH' ) || exit;

final class Section_Renderer {

	/**
	 * Render one section with its wrapper.
	 *
	 * @param Component      $component Component.
	 * @param array          $data      Field values (merged with defaults).
	 * @param Render_Context $ctx       Context.
	 * @param array          $extra     classes => string[] (visibility etc.).
	 * @return string
	 */
	public static function section( Component $component, array $data, Render_Context $ctx, array $extra = array() ) {
		$data = wp_parse_args( $data, $component->defaults() );
		$data = $component->prepare( $data, $ctx );
		if ( null === $data ) {
			return '';
		}

		$inner = $component->render( $data, $ctx );
		if ( '' === trim( $inner ) ) {
			return '';
		}
		// Template-level guidance for editors (never shown on the live site).
		if ( $ctx->editor && ! empty( $data['editor_note'] ) ) {
			$inner = '<div class="xl-container">' . \XLights\Helpers\Context::editor_hint( \XLights\Helpers\Tokens::replace( $data['editor_note'] ) ) . '</div>' . $inner;
		}

		$bgs     = $component->backgrounds();
		$bg      = isset( $data['bg'] ) && in_array( $data['bg'], $bgs, true ) ? $data['bg'] : $bgs[0];
		$spacing = isset( $data['spacing'] ) ? sanitize_html_class( $data['spacing'] ) : 'default';

		$classes = array_merge(
			array(
				'xl-section',
				'xl-section--' . str_replace( '_', '-', $component->type() ),
				'xl-bg--' . $bg,
				'xl-space--' . $spacing,
			),
			$component->wrapper_classes( $data ),
			isset( $extra['classes'] ) ? (array) $extra['classes'] : array()
		);

		$attrs = array(
			'id'              => $ctx->anchor ? $ctx->anchor : null,
			'class'           => implode( ' ', array_map( 'sanitize_html_class', array_filter( $classes ) ) ),
			'data-xl-section' => $ctx->section ? $ctx->section : $component->type(),
		);

		return '<section' . Html::attrs( $attrs ) . '>' . $inner . '</section>';
	}

	/**
	 * Merge a saved section order with the template's current section list.
	 *
	 * Unknown/duplicate saved entries are dropped; sections added in later plugin
	 * versions are inserted after their default predecessor so existing pages
	 * pick them up without losing the editor's custom order.
	 *
	 * @param array $saved        Saved repeater rows (section, enabled, hide_*).
	 * @param array $default_keys Section keys in default order.
	 * @return array Ordered rows.
	 */
	public static function merge_order( array $saved, array $default_keys ) {
		$rows = array();
		$seen = array();
		foreach ( $saved as $row ) {
			$key = isset( $row['section'] ) ? (string) $row['section'] : '';
			if ( '' === $key || isset( $seen[ $key ] ) || ! in_array( $key, $default_keys, true ) ) {
				continue;
			}
			$seen[ $key ] = true;
			$rows[]       = $row;
		}
		foreach ( $default_keys as $i => $key ) {
			if ( isset( $seen[ $key ] ) ) {
				continue;
			}
			$new = array(
				'section' => $key,
				'enabled' => 'yes',
			);
			// Insert after the nearest preceding default section present in $rows.
			$pos = 0;
			for ( $j = $i - 1; $j >= 0; $j-- ) {
				foreach ( $rows as $r => $row ) {
					if ( $row['section'] === $default_keys[ $j ] ) {
						$pos = $r + 1;
						break 2;
					}
				}
			}
			array_splice( $rows, $pos, 0, array( $new ) );
			$seen[ $key ] = true;
		}
		return $rows;
	}

	/**
	 * Render a full page template.
	 *
	 * @param string   $template Template key.
	 * @param callable $data_for Callback( $section_key, array $definition ) => array data.
	 * @param array    $order    Saved order rows (may be empty).
	 * @param array    $props    Base Render_Context props.
	 * @return string
	 */
	public static function page( $template, callable $data_for, array $order = array(), array $props = array() ) {
		$sections = Page_Registry::sections( $template );
		if ( ! $sections ) {
			return '';
		}
		$rows  = self::merge_order( $order, array_keys( $sections ) );
		$html  = '';
		$index = 0;
		$h1    = false;

		foreach ( $rows as $row ) {
			$key = $row['section'];
			if ( isset( $row['enabled'] ) && 'yes' !== $row['enabled'] ) {
				continue;
			}
			$def       = $sections[ $key ];
			$component = Component_Registry::get( $def['type'] );
			if ( ! $component ) {
				continue;
			}
			// The first rendered section lets the floating header overlap it (CSS :has()).
			$classes = 0 === $index ? array( 'xl-section--lead' ) : array();
			foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
				if ( ! empty( $row[ 'hide_' . $device ] ) && 'yes' === $row[ 'hide_' . $device ] ) {
					$classes[] = 'xl-hide-' . $device;
				}
			}
			$ctx = new Render_Context(
				array_merge(
					$props,
					array(
						'template'        => $template,
						'section'         => $key,
						'index'           => $index,
						'anchor'          => isset( $def['anchor'] ) ? $def['anchor'] : 'xl-' . $template . '-' . str_replace( '_', '-', $key ),
						'primary_heading' => ! $h1 && 'hero' === $def['type'],
					)
				)
			);
			$out = self::section( $component, (array) call_user_func( $data_for, $key, $def ), $ctx, array( 'classes' => $classes ) );
			if ( '' !== $out ) {
				$html .= $out;
				$index++;
				if ( 'hero' === $def['type'] ) {
					$h1 = true;
				}
			}
		}
		return $html;
	}

	/**
	 * Render a visual: photo, app-screen mockup or illustration fallback.
	 *
	 * @param array          $d    Data with visual/image/screen/screenshot/scene/palette.
	 * @param Render_Context $ctx  Context.
	 * @param array          $args size, sizes, eager, class.
	 * @return string
	 */
	public static function visual( array $d, Render_Context $ctx, array $args = array() ) {
		$visual = isset( $d['visual'] ) ? $d['visual'] : 'image';
		$args   = wp_parse_args(
			$args,
			array(
				'size'  => 'large',
				'sizes' => '(max-width: 767px) 100vw, 50vw',
				'eager' => false,
				'class' => 'xl-media__img',
			)
		);
		if ( 'none' === $visual ) {
			return '';
		}
		if ( 'phone' === $visual ) {
			return Illustrations::phone(
				isset( $d['screen'] ) ? $d['screen'] : 'zones',
				isset( $d['screenshot'] ) ? $d['screenshot'] : null,
				array( 'eager' => $args['eager'] )
			);
		}
		if ( 'image' === $visual && isset( $d['image'] ) && Html::has_image( $d['image'] ) ) {
			return Html::image(
				$d['image'],
				array(
					'size'  => $args['size'],
					'sizes' => $args['sizes'],
					'class' => $args['class'],
					'eager' => $args['eager'],
					// Template alt text applies only to bundled default images, never to Media Library picks.
					'alt'   => ( empty( $d['image']['id'] ) && isset( $d['image_alt'] ) ) ? $d['image_alt'] : null,
				)
			);
		}
		$out = Illustrations::scene(
			! empty( $d['scene'] ) ? $d['scene'] : 'roofline',
			! empty( $d['palette'] ) ? $d['palette'] : 'warm'
		);
		if ( 'image' === $visual && $ctx->editor ) {
			$out .= '<span class="xl-photo-needed">' . esc_html__( 'Add an approved photo', 'xlights-elementor-widgets' ) . '</span>';
		}
		return $out;
	}

	/**
	 * Media wrapper class for a visual.
	 *
	 * @param array $d Data.
	 * @return string
	 */
	public static function visual_class( array $d ) {
		$visual = isset( $d['visual'] ) ? $d['visual'] : 'image';
		if ( 'image' === $visual && ( empty( $d['image'] ) || ! Html::has_image( $d['image'] ) ) ) {
			$visual = 'illustration';
		}
		return 'xl-media xl-media--' . sanitize_html_class( $visual );
	}
}
