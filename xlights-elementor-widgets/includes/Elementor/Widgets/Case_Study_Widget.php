<?php
/**
 * Standalone section widget: project case study.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Case_Study_Widget extends Section_Widget {

	protected function component_type() {
		return 'case_study';
	}

	protected function defaults_source() {
		return array( 'commercial', 'case_study' );
	}
}
