<?php
/**
 * FAQ accordion with optional FAQPage structured data.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Context;
use XLights\SEO\Schema;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Faq extends Component {

	public function type() {
		return 'faq';
	}

	public function label() {
		return __( 'FAQ Accordion', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-accordion';
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'layout'     => self::select(
					__( 'Layout', 'xlights-elementor-widgets' ),
					array(
						'split'   => __( 'Heading left, questions right', 'xlights-elementor-widgets' ),
						'stacked' => __( 'Stacked', 'xlights-elementor-widgets' ),
					),
					'split',
					array( 'separator' => 'before' )
				),
				'items'      => array(
					'type'        => 'repeater',
					'label'       => __( 'Questions', 'xlights-elementor-widgets' ),
					'title_field' => '{{{ question }}}',
					'fields'      => array(
						'question' => self::text( __( 'Question', 'xlights-elementor-widgets' ) ),
						'answer'   => self::textarea( __( 'Answer', 'xlights-elementor-widgets' ), '', 5 ),
						'gate'     => self::gate_field(),
					),
					'default'     => array(),
				),
				'schema'     => self::toggle( __( 'Output FAQPage structured data', 'xlights-elementor-widgets' ), true ),
				'link_label' => self::text( __( 'Link label', 'xlights-elementor-widgets' ), '', array( 'separator' => 'before' ) ),
				'link'       => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
			)
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$items = array();
		foreach ( (array) $data['items'] as $item ) {
			$item = wp_parse_args(
				(array) $item,
				array(
					'question' => '',
					'answer'   => '',
					'gate'     => '',
				)
			);
			if ( '' === trim( $item['question'] ) ) {
				continue;
			}
			$item['visibility'] = Context::visibility( $item['gate'] );
			if ( Context::HIDE === $item['visibility'] ) {
				continue;
			}
			$items[] = $item;
		}
		if ( ! $items && ! $ctx->editor ) {
			return null;
		}
		$data['items'] = $items;
		if ( 'yes' === $data['schema'] && ! $ctx->editor ) {
			Schema::add_faq(
				array_filter(
					$items,
					static function ( $i ) {
						return Context::SHOW === $i['visibility'];
					}
				)
			);
		}
		return $data;
	}
}
