<?php
/**
 * Quote / consultation request form. Variants preselect the service and the
 * residential/commercial path; submission is handled by Forms\Quote_Form_Handler.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Forms\Quote_Form_Handler;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Quote_Form extends Component {

	public function type() {
		return 'quote_form';
	}

	public function label() {
		return __( 'Quote Request Form', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-form-horizontal';
	}

	public function backgrounds() {
		return array( 'warm', 'light', 'dark' );
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'variant'       => self::select(
					__( 'Form variant', 'xlights-elementor-widgets' ),
					Quote_Form_Handler::variants(),
					'general',
					array(
						'separator'   => 'before',
						'description' => __( 'Preselects the lighting interest and property type. Links ending in ?service=soffit (etc.) also preselect a service.', 'xlights-elementor-widgets' ),
					)
				),
				'submit_label'  => self::text( __( 'Submit button label', 'xlights-elementor-widgets' ), __( 'Request My Quote', 'xlights-elementor-widgets' ) ),
				'show_aside'    => self::toggle( __( 'Show side panel', 'xlights-elementor-widgets' ), true, array( 'separator' => 'before' ) ),
				'aside_heading' => self::text( __( 'Side panel heading', 'xlights-elementor-widgets' ), __( 'Why we ask for your address and photos', 'xlights-elementor-widgets' ) ),
				'aside_text'    => self::textarea( __( 'Side panel text', 'xlights-elementor-widgets' ) ),
				'aside_points'  => self::textarea( __( 'Side panel points (one per line)', 'xlights-elementor-widgets' ), '', 4 ),
				'show_contact'  => self::toggle( __( 'Show phone & email in side panel', 'xlights-elementor-widgets' ), true ),
			)
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$variants        = Quote_Form_Handler::variants();
		$data['variant'] = isset( $variants[ $data['variant'] ] ) ? $data['variant'] : 'general';
		return $data;
	}
}
