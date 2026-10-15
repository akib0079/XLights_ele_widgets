<?php
/**
 * Case study: a real project told as a short story — headline and key facts,
 * the narrative, a takeaway line and a photo gallery that opens in the
 * lightbox.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Html;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Case_Study extends Component {

	public function type() {
		return 'case_study';
	}

	public function label() {
		return __( 'Project Case Study', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-gallery-masonry';
	}

	public function backgrounds() {
		return array( 'dark', 'light', 'warm' );
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'takeaway' => self::textarea(
					__( 'Takeaway', 'xlights-elementor-widgets' ),
					'',
					2,
					array( 'description' => __( 'One closing line, shown highlighted under the story.', 'xlights-elementor-widgets' ) )
				),
				'facts'    => array(
					'type'        => 'repeater',
					'label'       => __( 'Project facts', 'xlights-elementor-widgets' ),
					'title_field' => '{{{ label }}}',
					'fields'      => array(
						'label' => self::text( __( 'Label', 'xlights-elementor-widgets' ) ),
						'value' => self::text( __( 'Value', 'xlights-elementor-widgets' ) ),
					),
					'default'     => array(),
					'separator'   => 'before',
				),
				'photos'   => array(
					'type'        => 'repeater',
					'label'       => __( 'Photos', 'xlights-elementor-widgets' ),
					'title_field' => '{{{ caption }}}',
					'fields'      => array(
						'image'     => self::media( __( 'Photo', 'xlights-elementor-widgets' ) ),
						'image_alt' => self::text(
							__( 'Photo description', 'xlights-elementor-widgets' ),
							'',
							array( 'description' => __( 'Used for the bundled photos. Media Library photos use their own alt text.', 'xlights-elementor-widgets' ) )
						),
						'caption'   => self::text( __( 'Caption', 'xlights-elementor-widgets' ) ),
					),
					'default'     => array(),
					'separator'   => 'before',
				),
			),
			self::button_fields( 'primary', __( 'Button', 'xlights-elementor-widgets' ) )
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$photos = array();
		foreach ( (array) $data['photos'] as $row ) {
			$row = wp_parse_args(
				(array) $row,
				array(
					'image'     => '',
					'image_alt' => '',
					'caption'   => '',
				)
			);
			if ( Html::has_image( $row['image'] ) ) {
				$photos[] = $row;
			}
		}
		$facts = array();
		foreach ( (array) $data['facts'] as $row ) {
			$row = wp_parse_args( (array) $row, array( 'label' => '', 'value' => '' ) );
			if ( '' !== trim( (string) $row['value'] ) ) {
				$facts[] = $row;
			}
		}
		if ( ! $photos && '' === trim( wp_strip_all_tags( (string) $data['text'] ) ) && ! $ctx->editor ) {
			return null;
		}
		$data['photos'] = $photos;
		$data['facts']  = $facts;
		return $data;
	}

	public function wrapper_classes( array $data ) {
		return array( 'xl-case-section' );
	}
}
