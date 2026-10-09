<?php
/**
 * Base for standalone section widgets. Controls come from the same component
 * schema as the Page Composer; defaults are borrowed from a representative
 * page-template section so each widget looks finished when dropped in.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use XLights\Elementor\Controls_Builder;
use XLights\Elementor\Integration;
use XLights\Elementor\Style_Controls;
use XLights\Pages\Page_Registry;
use XLights\Sections\Component_Registry;
use XLights\Sections\Render_Context;
use XLights\Sections\Section_Renderer;

defined( 'ABSPATH' ) || exit;

abstract class Section_Widget extends Widget_Base {

	/**
	 * @return string Component type.
	 */
	abstract protected function component_type();

	/**
	 * Template/section pair whose defaults seed this widget.
	 *
	 * @return array [template, section]
	 */
	abstract protected function defaults_source();

	/**
	 * @return \XLights\Sections\Component
	 */
	protected function component() {
		return Component_Registry::get( $this->component_type() );
	}

	public function get_name() {
		return 'xlights-' . str_replace( '_', '-', $this->component_type() );
	}

	public function get_title() {
		/* translators: %s: section name */
		return sprintf( __( 'X LIGHTS — %s', 'xlights-elementor-widgets' ), $this->component()->label() );
	}

	public function get_icon() {
		return $this->component()->icon();
	}

	public function get_categories() {
		return array( Integration::CATEGORY );
	}

	public function get_keywords() {
		return array( 'xlights', 'x lights', str_replace( '_', ' ', $this->component_type() ) );
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

	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * @return array
	 */
	protected function seed_defaults() {
		list( $template, $section ) = $this->defaults_source();
		$sections                   = Page_Registry::sections( $template );
		return isset( $sections[ $section ]['defaults'] ) ? $sections[ $section ]['defaults'] : array();
	}

	protected function register_controls() {
		$this->start_controls_section(
			'xl_content',
			array(
				'label' => $this->component()->label(),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		if ( 'hero' === $this->component_type() ) {
			$this->add_control(
				'as_h1',
				array(
					'label'        => __( 'Use as the page H1', 'xlights-elementor-widgets' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'Each page should have exactly one H1.', 'xlights-elementor-widgets' ),
				)
			);
		}
		$this->add_control(
			'xl_anchor',
			array(
				'label'       => __( 'Section ID (anchor)', 'xlights-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'quote_form' === $this->component_type() ? 'quote' : '',
				'description' => __( 'Optional. Lets buttons link to #your-id.', 'xlights-elementor-widgets' ),
			)
		);
		Controls_Builder::add( $this, $this->component()->fields(), '', $this->seed_defaults() );
		$this->end_controls_section();

		Style_Controls::register( $this );
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$component = $this->component();
		$data      = array();
		foreach ( array_keys( $component->fields() ) as $field ) {
			if ( array_key_exists( $field, $settings ) ) {
				$data[ $field ] = $settings[ $field ];
			}
		}
		$ctx = new Render_Context(
			array(
				'source'          => 'widget',
				'section'         => $component->type(),
				'anchor'          => isset( $settings['xl_anchor'] ) ? sanitize_html_class( $settings['xl_anchor'] ) : '',
				'primary_heading' => 'hero' === $component->type() && isset( $settings['as_h1'] ) && 'yes' === $settings['as_h1'],
				'page_id'         => (int) get_the_ID(),
				'uid'             => 'xl' . $this->get_id(),
			)
		);
		$html = Section_Renderer::section( $component, $data, $ctx );
		if ( '' === $html ) {
			return;
		}
		printf(
			'<div class="xl-root %1$s">%2$s</div>',
			esc_attr( Style_Controls::root_classes( $settings ) ),
			$html // phpcs:ignore WordPress.Security.EscapeOutput -- Components escape their own output.
		);
	}
}
