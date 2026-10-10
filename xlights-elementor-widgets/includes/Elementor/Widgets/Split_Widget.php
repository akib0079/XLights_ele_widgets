<?php
/**
 * Standalone section widget: split.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Split_Widget extends Section_Widget {

	protected function component_type() {
		return 'split';
	}

	protected function defaults_source() {
		return array( 'home', 'soffit' );
	}

	/**
	 * Standalone Split widgets default to side by side, whatever layout the
	 * seeding template section uses (Elementor stores only non-default values,
	 * so changing this default would restyle widgets already on pages).
	 *
	 * @return array
	 */
	protected function seed_defaults() {
		$defaults           = parent::seed_defaults();
		$defaults['layout'] = 'standard';
		return $defaults;
	}
}
