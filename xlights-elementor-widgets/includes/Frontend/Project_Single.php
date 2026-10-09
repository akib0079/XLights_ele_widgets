<?php
/**
 * Single project pages: appends location, categories, the photo gallery,
 * before/after comparison and a consultation CTA to the theme's content area.
 *
 * @package XLights
 */

namespace XLights\Frontend;

use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\PostTypes\Projects;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Project_Single {

	public static function init() {
		add_filter( 'the_content', array( __CLASS__, 'content' ), 20 );
	}

	/**
	 * @param string $content Content.
	 * @return string
	 */
	public static function content( $content ) {
		if ( ! is_singular( Projects::POST_TYPE ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}
		$p = Projects::card_data( get_the_ID() );

		$meta = '<ul class="xl-project-meta" role="list">';
		if ( $p['city'] ) {
			$meta .= '<li>' . Icons::svg( 'map-pin' ) . '<span>' . esc_html( $p['city'] ) . '</span></li>';
		}
		foreach ( array_merge( $p['services'], $p['types'] ) as $term ) {
			$meta .= '<li>' . Icons::svg( 'check' ) . '<span>' . esc_html( $term ) . '</span></li>';
		}
		$meta .= '</ul>';

		$gallery = '';
		$ids     = $p['images'];
		if ( count( $ids ) > 0 ) {
			$items    = Projects::lightbox_items( $ids, $p['title'] );
			$gallery .= '<ul class="xl-grid xl-grid--3 xl-project-gallery" role="list">';
			foreach ( $ids as $i => $aid ) {
				$gallery .= '<li><button type="button" class="xl-project__open" data-xl-lightbox="' . esc_attr( wp_json_encode( $items ) ) . '" data-xl-index="' . (int) $i . '" aria-label="' . esc_attr( sprintf( /* translators: %d: photo number */ __( 'Open photo %d', 'xlights-elementor-widgets' ), $i + 1 ) ) . '">';
				$gallery .= Html::image(
					$aid,
					array(
						'size'  => 'xlights-card',
						'sizes' => '(max-width: 767px) 100vw, 33vw',
						'eager' => 0 === $i,
					)
				);
				$gallery .= '</button></li>';
			}
			$gallery .= '</ul>';
		}

		$ba = '';
		if ( $p['before'] && $p['after'] ) {
			$ba  = '<div class="xl-ba" data-xl-ba style="--xl-pos:50%">';
			$ba .= '<div class="xl-ba__layer xl-ba__after">' . Html::image( $p['after'], array( 'size' => 'large' ) ) . '</div>';
			$ba .= '<div class="xl-ba__layer xl-ba__before">' . Html::image( $p['before'], array( 'size' => 'large' ) ) . '</div>';
			$ba .= '<span class="xl-ba__label xl-ba__label--before" aria-hidden="true">' . esc_html__( 'Before', 'xlights-elementor-widgets' ) . '</span><span class="xl-ba__label xl-ba__label--after" aria-hidden="true">' . esc_html__( 'After', 'xlights-elementor-widgets' ) . '</span>';
			$ba .= '<span class="xl-ba__handle" aria-hidden="true">' . Icons::svg( 'compare' ) . '</span>';
			$ba .= '<label class="xl-sr" for="xl-ba-single">' . esc_html__( 'Comparison slider: move to reveal before or after', 'xlights-elementor-widgets' ) . '</label>';
			$ba .= '<input class="xl-ba__range" id="xl-ba-single" type="range" min="0" max="100" value="50"></div>';
		}

		$cta = '<div class="xl-project-cta">' . Html::button( Settings::get( 'primary_cta_label' ), '{quote_url}', 'primary' ) . Html::button( __( 'View more projects', 'xlights-elementor-widgets' ), '{page:projects}', 'secondary' ) . '</div>';

		return '<div class="xl-root xl-project-single">' . $meta . '</div>' . $content . '<div class="xl-root xl-project-single">' . $ba . $gallery . $cta . '</div>';
	}
}
