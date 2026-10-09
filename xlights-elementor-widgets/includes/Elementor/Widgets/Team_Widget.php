<?php
/**
 * Standalone section widget: team.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Team_Widget extends Section_Widget {

	protected function component_type() {
		return 'team';
	}

	protected function defaults_source() {
		return array( 'about', 'team' );
	}
}
