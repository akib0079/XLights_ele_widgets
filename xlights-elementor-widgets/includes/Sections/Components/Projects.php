<?php
/**
 * Dynamic project gallery (featured preview or full filterable portfolio).
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\PostTypes\Projects as Project_Type;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Projects extends Component {

	public function type() {
		return 'projects';
	}

	public function label() {
		return __( 'Projects Gallery', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-gallery-masonry';
	}

	protected function define_fields() {
		$filters = Project_Type::filters();
		return array_merge(
			self::head_fields( true ),
			array(
				'source'         => self::select(
					__( 'Show projects', 'xlights-elementor-widgets' ),
					array_merge( array( 'featured' => __( 'Featured projects', 'xlights-elementor-widgets' ) ), $filters ),
					'featured',
					array(
						'separator'   => 'before',
						'description' => __( 'Projects are managed under X LIGHTS → Projects. Featured projects appear first in every list.', 'xlights-elementor-widgets' ),
					)
				),
				'count'          => array(
					'type'    => 'number',
					'label'   => __( 'Number of projects', 'xlights-elementor-widgets' ),
					'default' => 6,
					'min'     => 1,
					'max'     => 48,
				),
				'columns'        => self::select(
					__( 'Columns (desktop)', 'xlights-elementor-widgets' ),
					array(
						'2' => '2',
						'3' => '3',
						'4' => '4',
					),
					'3'
				),
				'show_filters'   => self::toggle( __( 'Show filter buttons', 'xlights-elementor-widgets' ), false ),
				'load_more'      => self::toggle( __( 'Show "Load more" button', 'xlights-elementor-widgets' ), false ),
				'link_label'     => self::text( __( 'Link label', 'xlights-elementor-widgets' ), '', array( 'separator' => 'before' ) ),
				'link'           => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
				'empty_behavior' => self::select(
					__( 'When no projects match', 'xlights-elementor-widgets' ),
					array(
						'hide'    => __( 'Hide the section', 'xlights-elementor-widgets' ),
						'message' => __( 'Show a message', 'xlights-elementor-widgets' ),
					),
					'hide'
				),
				'empty_text'     => self::text( __( 'Empty message', 'xlights-elementor-widgets' ), __( 'New local projects are being added. Contact us to see examples of our work.', 'xlights-elementor-widgets' ) ),
			)
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$data['count']  = max( 1, min( 48, (int) $data['count'] ) );
		$data['source'] = (string) $data['source'];
		$data['filter'] = 'featured' === $data['source'] ? 'all' : $data['source'];
		$query          = Project_Type::query(
			array(
				'filter'        => $data['filter'],
				'featured_only' => 'featured' === $data['source'],
				'count'         => $data['count'],
				'page'          => 1,
			)
		);
		// Featured preview falls back to the latest projects when nothing is flagged.
		if ( 'featured' === $data['source'] && ! $query->have_posts() ) {
			$query = Project_Type::query(
				array(
					'filter' => 'all',
					'count'  => $data['count'],
				)
			);
		}
		$data['query'] = $query;
		if ( ! $query->have_posts() && 'hide' === $data['empty_behavior'] && ! $ctx->editor && 'yes' !== $data['show_filters'] ) {
			return null;
		}
		return $data;
	}
}
