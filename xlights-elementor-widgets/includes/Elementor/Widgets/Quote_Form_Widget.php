<?php
/**
 * Standalone section widget: quote_form.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Quote_Form_Widget extends Section_Widget {

	protected function component_type() {
		return 'quote_form';
	}

	protected function defaults_source() {
		return array( 'contact', 'form' );
	}
}
