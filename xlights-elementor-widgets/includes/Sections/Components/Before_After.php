<?php
/**
 * Before/after (or day/night) comparison slider.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Html;
use XLights\PostTypes\Projects as Project_Type;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Before_After extends Component {

	public function type() {
		return 'before_after';
	}

	public function label() {
		return __( 'Before / After Comparison', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-image-before-after';
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'source'       => self::select(
					__( 'Source', 'xlights-elementor-widgets' ),
					array(
						'projects' => __( 'Projects with before & after photos', 'xlights-elementor-widgets' ),
						'manual'   => __( 'Choose two images', 'xlights-elementor-widgets' ),
					),
					'projects',
					array( 'separator' => 'before' )
				),
				'count'        => array(
					'type'      => 'number',
					'label'     => __( 'Number of comparisons', 'xlights-elementor-widgets' ),
					'default'   => 2,
					'min'       => 1,
					'max'       => 6,
					'condition' => array( 'source' => 'projects' ),
				),
				'before_image' => self::media( __( 'Before / day image', 'xlights-elementor-widgets' ), '', array( 'condition' => array( 'source' => 'manual' ) ) ),
				'after_image'  => self::media( __( 'After / night image', 'xlights-elementor-widgets' ), '', array( 'condition' => array( 'source' => 'manual' ) ) ),
				'caption'      => self::text( __( 'Caption', 'xlights-elementor-widgets' ), '', array( 'condition' => array( 'source' => 'manual' ) ) ),
				'before_label' => self::text( __( 'Before label', 'xlights-elementor-widgets' ), __( 'Before', 'xlights-elementor-widgets' ) ),
				'after_label'  => self::text( __( 'After label', 'xlights-elementor-widgets' ), __( 'After', 'xlights-elementor-widgets' ) ),
			)
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$pairs = array();
		if ( 'manual' === $data['source'] ) {
			if ( Html::has_image( $data['before_image'] ) && Html::has_image( $data['after_image'] ) ) {
				$pairs[] = array(
					'before'  => $data['before_image'],
					'after'   => $data['after_image'],
					'caption' => $data['caption'],
					'url'     => '',
				);
			}
		} else {
			$query = Project_Type::query(
				array(
					'filter' => 'before-after',
					'count'  => max( 1, min( 6, (int) $data['count'] ) ),
				)
			);
			foreach ( $query->posts as $post ) {
				$p = Project_Type::card_data( $post );
				if ( $p['before'] && $p['after'] ) {
					$pairs[] = array(
						'before'  => $p['before'],
						'after'   => $p['after'],
						'caption' => $p['title'] . ( $p['city'] ? ' · ' . $p['city'] : '' ),
						'url'     => $p['url'],
					);
				}
			}
		}
		if ( ! $pairs && ! $ctx->editor ) {
			return null;
		}
		$data['pairs'] = $pairs;
		return $data;
	}
}
