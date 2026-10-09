<?php
/**
 * X LIGHTS — Footer widget.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use XLights\Elementor\Integration;
use XLights\Frontend\Header_Footer;

defined( 'ABSPATH' ) || exit;

class Footer extends Widget_Base {

	public function get_name() {
		return 'xlights-footer';
	}

	public function get_title() {
		return __( 'X LIGHTS — Footer', 'xlights-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	public function get_categories() {
		return array( Integration::CATEGORY );
	}

	public function get_keywords() {
		return array( 'xlights', 'footer' );
	}

	public function get_style_depends() {
		return array( 'xlights' );
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'xl_footer',
			array(
				'label' => __( 'Footer', 'xlights-elementor-widgets' ),
			)
		);
		$this->add_control(
			'notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Contact details, service areas and social links come from X LIGHTS → Settings. Columns come from the "X LIGHTS Footer" menu (top-level items become headings) or the default page structure.', 'xlights-elementor-widgets' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);
		$this->add_control(
			'show_cta',
			array(
				'label'        => __( 'Show quote band above footer', 'xlights-elementor-widgets' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'auto' => __( 'Automatic (hidden on Page Composer pages)', 'xlights-elementor-widgets' ),
					'yes'  => __( 'Always', 'xlights-elementor-widgets' ),
					'no'   => __( 'Never', 'xlights-elementor-widgets' ),
				),
				'default'      => 'auto',
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$opts = 'auto' === $s['show_cta'] ? array() : array( 'show_cta' => 'yes' === $s['show_cta'] );
		echo Header_Footer::footer( $opts ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
