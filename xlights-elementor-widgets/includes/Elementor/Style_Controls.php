<?php
/**
 * Shared Style-tab controls. Most map to CSS custom properties on .xl-root so
 * one setting restyles every section consistently; typography, borders,
 * shadows and backgrounds use Elementor group controls. Empty values fall back
 * to the brand settings (X LIGHTS → Settings → Brand) and Elementor global
 * colors/fonts can be picked in every color and typography control.
 *
 * @package XLights
 */

namespace XLights\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || exit;

final class Style_Controls {

	/**
	 * @param \Elementor\Widget_Base $w    Widget.
	 * @param array                  $args sections: which groups to include.
	 */
	public static function register( $w, array $args = array() ) {
		$root = '{{WRAPPER}} .xl-root';

		/* Global layout */
		$w->start_controls_section(
			'xl_style_layout',
			array(
				'label' => __( 'Global Layout', 'xlights-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$w->add_responsive_control(
			'xl_container',
			array(
				'label'      => __( 'Content width', 'xlights-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 720,
						'max' => 1800,
					),
				),
				'selectors'  => array( $root => '--xl-container: {{SIZE}}{{UNIT}};' ),
			)
		);
		$w->add_responsive_control(
			'xl_section_y',
			array(
				'label'      => __( 'Section vertical padding', 'xlights-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 240,
					),
				),
				'selectors'  => array( $root => '--xl-section-y: {{SIZE}}{{UNIT}};' ),
			)
		);
		$w->add_responsive_control(
			'xl_gutter',
			array(
				'label'      => __( 'Side gutter', 'xlights-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 120,
					),
				),
				'selectors'  => array( $root => '--xl-gutter: {{SIZE}}{{UNIT}};' ),
			)
		);
		$w->add_responsive_control(
			'xl_gap',
			array(
				'label'      => __( 'Grid gap', 'xlights-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array( $root => '--xl-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$w->end_controls_section();

		/* Colors */
		$w->start_controls_section(
			'xl_style_colors',
			array(
				'label' => __( 'Colors', 'xlights-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$colors = array(
			'xl_c_accent'    => array( __( 'Accent', 'xlights-elementor-widgets' ), '--xl-accent' ),
			'xl_c_accent_ink' => array( __( 'Accent text on light backgrounds', 'xlights-elementor-widgets' ), '--xl-accent-ink' ),
			'xl_c_dark'      => array( __( 'Dark surface', 'xlights-elementor-widgets' ), '--xl-dark' ),
			'xl_c_light'     => array( __( 'Light surface', 'xlights-elementor-widgets' ), '--xl-light' ),
			'xl_c_surface'   => array( __( 'Light grey surface', 'xlights-elementor-widgets' ), '--xl-surface' ),
			'xl_c_heading'   => array( __( 'Headings', 'xlights-elementor-widgets' ), '--xl-heading' ),
			'xl_c_text'      => array( __( 'Body text', 'xlights-elementor-widgets' ), '--xl-text' ),
			'xl_c_muted'     => array( __( 'Muted text', 'xlights-elementor-widgets' ), '--xl-muted' ),
			'xl_c_on_dark'   => array( __( 'Text on dark backgrounds', 'xlights-elementor-widgets' ), '--xl-on-dark' ),
		);
		foreach ( $colors as $id => $c ) {
			$w->add_control(
				$id,
				array(
					'label'     => $c[0],
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( $root => $c[1] . ': {{VALUE}};' ),
				)
			);
		}
		$w->end_controls_section();

		/* Typography */
		$w->start_controls_section(
			'xl_style_type',
			array(
				'label' => __( 'Typography', 'xlights-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$type_groups = array(
			'xl_t_h1'      => array( __( 'Hero heading (H1)', 'xlights-elementor-widgets' ), '.xl-hero__title' ),
			'xl_t_h2'      => array( __( 'Section headings (H2)', 'xlights-elementor-widgets' ), '.xl-head .xl-title' ),
			'xl_t_h3'      => array( __( 'Card titles (H3)', 'xlights-elementor-widgets' ), '.xl-card__title, .xl-tabs__title, .xl-project__title, .xl-team__name' ),
			'xl_t_eyebrow' => array( __( 'Eyebrows', 'xlights-elementor-widgets' ), '.xl-eyebrow' ),
			'xl_t_body'    => array( __( 'Body text', 'xlights-elementor-widgets' ), '.xl-lead, .xl-prose, .xl-card__text, .xl-hero__text' ),
		);
		foreach ( $type_groups as $name => $g ) {
			$w->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => $name,
					'label'    => $g[0],
					'selector' => implode( ', ', array_map( static function ( $s ) use ( $root ) {
						return $root . ' ' . trim( $s );
					}, explode( ',', $g[1] ) ) ),
				)
			);
		}
		$w->end_controls_section();

		/* Buttons */
		$w->start_controls_section(
			'xl_style_buttons',
			array(
				'label' => __( 'Buttons', 'xlights-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$w->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'xl_btn_typography',
				'selector' => $root . ' .xl-btn',
			)
		);
		$w->add_control(
			'xl_btn_radius',
			array(
				'label'      => __( 'Corner radius', 'xlights-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array( $root => '--xl-btn-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$w->add_responsive_control(
			'xl_btn_padding',
			array(
				'label'      => __( 'Padding', 'xlights-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( $root . ' .xl-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		foreach ( array(
			'xl_btn_bg'       => array( __( 'Primary background', 'xlights-elementor-widgets' ), '--xl-btn-bg' ),
			'xl_btn_color'    => array( __( 'Primary text', 'xlights-elementor-widgets' ), '--xl-btn-color' ),
			'xl_btn_bg_hover' => array( __( 'Primary background (hover)', 'xlights-elementor-widgets' ), '--xl-btn-bg-hover' ),
			'xl_btn_line'     => array( __( 'Secondary border & text', 'xlights-elementor-widgets' ), '--xl-btn-line' ),
		) as $id => $c ) {
			$w->add_control(
				$id,
				array(
					'label'     => $c[0],
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( $root => $c[1] . ': {{VALUE}};' ),
				)
			);
		}
		$w->end_controls_section();

		/* Cards */
		$w->start_controls_section(
			'xl_style_cards',
			array(
				'label' => __( 'Cards', 'xlights-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$w->add_control(
			'xl_card_bg',
			array(
				'label'     => __( 'Card background', 'xlights-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $root => '--xl-card-bg: {{VALUE}};' ),
			)
		);
		$w->add_control(
			'xl_card_radius',
			array(
				'label'      => __( 'Corner radius', 'xlights-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'selectors'  => array( $root => '--xl-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$w->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'xl_card_border',
				'selector' => $root . ' .xl-cards--icon .xl-card, ' . $root . ' .xl-cards--image .xl-card, ' . $root . ' .xl-cards--steps .xl-card, ' . $root . ' .xl-review',
			)
		);
		$w->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'xl_card_shadow',
				'selector' => $root . ' .xl-card, ' . $root . ' .xl-review',
			)
		);
		$w->end_controls_section();

		/* Images */
		$w->start_controls_section(
			'xl_style_images',
			array(
				'label' => __( 'Images', 'xlights-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$w->add_control(
			'xl_img_radius',
			array(
				'label'      => __( 'Image corner radius', 'xlights-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'selectors'  => array( $root => '--xl-img-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$w->end_controls_section();

		/* Section backgrounds */
		$w->start_controls_section(
			'xl_style_backgrounds',
			array(
				'label' => __( 'Section Backgrounds', 'xlights-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$w->add_control(
			'xl_bg_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Each section chooses Light, Light grey or Dark (navy) in its content settings. Customize what those backgrounds look like here.', 'xlights-elementor-widgets' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);
		$w->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'xl_bg_dark',
				'label'    => __( 'Dark sections', 'xlights-elementor-widgets' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => $root . ' .xl-section.xl-bg--dark:not(.xl-section--hero)',
			)
		);
		$w->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'xl_bg_warm',
				'label'    => __( 'Light grey sections', 'xlights-elementor-widgets' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => $root . ' .xl-section.xl-bg--warm',
			)
		);
		$w->end_controls_section();

		/* Animations */
		$w->start_controls_section(
			'xl_style_motion',
			array(
				'label' => __( 'Animations & Hover', 'xlights-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$w->add_control(
			'xl_anim',
			array(
				'label'        => __( 'Entrance animations (fade-up, image reveal, hero intro)', 'xlights-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Automatically disabled for visitors who prefer reduced motion.', 'xlights-elementor-widgets' ),
			)
		);
		$w->add_control(
			'xl_anim_duration',
			array(
				'label'      => __( 'Animation duration (ms)', 'xlights-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 200,
						'max'  => 1400,
						'step' => 50,
					),
				),
				'selectors'  => array( $root => '--xl-anim-duration: {{SIZE}}ms;' ),
				'condition'  => array( 'xl_anim' => 'yes' ),
			)
		);
		$w->add_control(
			'xl_hover',
			array(
				'label'        => __( 'Hover effects (card lift, image zoom)', 'xlights-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$w->end_controls_section();
	}

	/**
	 * Root classes from motion settings.
	 *
	 * @param array $settings Widget settings.
	 * @return string
	 */
	public static function root_classes( array $settings ) {
		$cls = array();
		if ( ! isset( $settings['xl_anim'] ) || 'yes' === $settings['xl_anim'] ) {
			$cls[] = 'xl-anim';
		}
		if ( ! isset( $settings['xl_hover'] ) || 'yes' === $settings['xl_hover'] ) {
			$cls[] = 'xl-hover';
		}
		return implode( ' ', $cls );
	}
}
