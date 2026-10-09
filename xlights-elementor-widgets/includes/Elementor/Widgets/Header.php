<?php
/**
 * X LIGHTS — Header widget. Use in an Elementor Pro Theme Builder header
 * template, or on any page when not using automatic header injection.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use XLights\Elementor\Integration;
use XLights\Frontend\Header_Footer;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

class Header extends Widget_Base {

	public function get_name() {
		return 'xlights-header';
	}

	public function get_title() {
		return __( 'X LIGHTS — Header', 'xlights-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	public function get_categories() {
		return array( Integration::CATEGORY );
	}

	public function get_keywords() {
		return array( 'xlights', 'header', 'menu', 'navigation' );
	}

	public function get_style_depends() {
		return array( 'xlights' );
	}

	public function get_script_depends() {
		return array( 'xlights' );
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'xl_header',
			array(
				'label' => __( 'Header', 'xlights-elementor-widgets' ),
			)
		);
		$this->add_control(
			'notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Logo, phone and CTA come from X LIGHTS → Settings. Menu items come from the "X LIGHTS Header" menu location, or the default page structure when no menu is assigned.', 'xlights-elementor-widgets' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);
		$this->add_control(
			'sticky',
			array(
				'label'        => __( 'Sticky', 'xlights-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => Settings::enabled( 'header_sticky' ) ? 'yes' : '',
			)
		);
		$this->add_control(
			'transparent',
			array(
				'label'        => __( 'Transparent over dark heroes', 'xlights-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => Settings::enabled( 'header_transparent' ) ? 'yes' : '',
			)
		);
		$this->add_control(
			'show_phone',
			array(
				'label'        => __( 'Show phone', 'xlights-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'cta_label',
			array(
				'label'   => __( 'CTA label', 'xlights-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '{cta}',
			)
		);
		$this->add_control(
			'cta_link',
			array(
				'label'   => __( 'CTA link', 'xlights-elementor-widgets' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '{quote_url}' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'xl_header_style',
			array(
				'label' => __( 'Header Style', 'xlights-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Background', 'xlights-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .xl-header' => '--xl-header-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'link',
			array(
				'label'     => __( 'Link color', 'xlights-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .xl-header' => '--xl-header-link: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_typography',
				'selector' => '{{WRAPPER}} .xl-nav__link',
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$html = Header_Footer::header(
			array(
				'sticky'      => 'yes' === $s['sticky'],
				'transparent' => 'yes' === $s['transparent'],
				'show_phone'  => 'yes' === $s['show_phone'],
				'cta_label'   => $s['cta_label'],
				'cta_link'    => $s['cta_link'],
			)
		);
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- Escaped in Header_Footer::header().
	}
}
