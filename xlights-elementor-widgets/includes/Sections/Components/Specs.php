<?php
/**
 * Technical specifications. Each row stays unpublished until marked approved,
 * so unverified figures (voltage, run length, diode count, IP rating, warranty)
 * never reach the live site by accident.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Specs extends Component {

	public function type() {
		return 'specs';
	}

	public function label() {
		return __( 'Technical Specifications', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-table';
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'rows' => array(
					'type'        => 'repeater',
					'label'       => __( 'Specifications', 'xlights-elementor-widgets' ),
					'title_field' => '{{{ label }}}{{{ "yes" === approved ? "" : " — pending" }}}',
					'separator'   => 'before',
					'fields'      => array(
						'label'    => self::text( __( 'Label', 'xlights-elementor-widgets' ) ),
						'value'    => self::text( __( 'Value', 'xlights-elementor-widgets' ) ),
						'approved' => self::toggle(
							__( 'Verified for this market — publish', 'xlights-elementor-widgets' ),
							false
						),
					),
					'default'     => array(),
				),
				'note' => self::text( __( 'Footnote', 'xlights-elementor-widgets' ) ),
			)
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$rows = array();
		foreach ( (array) $data['rows'] as $row ) {
			$row = wp_parse_args(
				(array) $row,
				array(
					'label'    => '',
					'value'    => '',
					'approved' => '',
				)
			);
			if ( '' === trim( $row['label'] ) ) {
				continue;
			}
			$row['is_approved'] = 'yes' === $row['approved'];
			if ( $row['is_approved'] || $ctx->editor ) {
				$rows[] = $row;
			}
		}
		if ( ! $rows ) {
			return $ctx->editor ? array_merge( $data, array( 'rows' => array() ) ) : null;
		}
		$data['rows'] = $rows;
		return $data;
	}
}
