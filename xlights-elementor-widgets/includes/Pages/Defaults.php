<?php
/**
 * Shared default content used by several page templates.
 *
 * @package XLights
 */

namespace XLights\Pages;

use XLights\Helpers\Html;

defined( 'ABSPATH' ) || exit;

final class Defaults {

	/**
	 * Bundled image URL.
	 *
	 * @param string $file File.
	 * @return string
	 */
	public static function img( $file ) {
		return Html::asset_image( $file );
	}

	/**
	 * Products in the connected system diagram.
	 *
	 * @return array
	 */
	public static function connected_items() {
		return array(
			array(
				'title' => __( 'Permanent Soffit Lighting', 'xlights-elementor-widgets' ),
				'text'  => __( 'Roofline lighting for every night and every season', 'xlights-elementor-widgets' ),
				'icon'  => 'roofline',
				'link'  => '{page:soffit}',
			),
			array(
				'title' => __( 'Bistro Lighting', 'xlights-elementor-widgets' ),
				'text'  => __( 'A warm glow over patios and dining areas', 'xlights-elementor-widgets' ),
				'icon'  => 'string-lights',
				'link'  => '{page:bistro}',
			),
			array(
				'title' => __( 'Deck Lighting', 'xlights-elementor-widgets' ),
				'text'  => __( 'Steps, railings and walkways', 'xlights-elementor-widgets' ),
				'icon'  => 'stairs',
				'link'  => '{page:deck}',
			),
			array(
				'title' => __( 'Landscape Lighting', 'xlights-elementor-widgets' ),
				'text'  => __( 'Trees, garden beds and pathways', 'xlights-elementor-widgets' ),
				'icon'  => 'tree',
				'link'  => '{page:landscape}',
			),
		);
	}

	/**
	 * @return string
	 */
	public static function compatibility_note() {
		return __( 'Available controls vary by product. Your designer will confirm what each fixture in your plan supports.', 'xlights-elementor-widgets' );
	}

	/**
	 * App feature tabs.
	 *
	 * @param string[]|null $only Keys to include.
	 * @return array
	 */
	public static function app_tabs( $only = null ) {
		$all = array(
			'color'    => array(
				'label'  => __( 'Color & brightness', 'xlights-elementor-widgets' ),
				'icon'   => 'palette',
				'title'  => __( 'Every color, any brightness', 'xlights-elementor-widgets' ),
				'text'   => __( 'Choose warm white for everyday elegance or pick from a full spectrum of color, then dial in the brightness that suits the moment.', 'xlights-elementor-widgets' ),
				'screen' => 'color',
			),
			'schedule' => array(
				'label'  => __( 'Schedules', 'xlights-elementor-widgets' ),
				'icon'   => 'timer',
				'title'  => __( 'Set it once with smart schedules', 'xlights-elementor-widgets' ),
				'text'   => __( 'Create timers so your lights turn on and off when you choose — every evening, on weekends, or for a whole season.', 'xlights-elementor-widgets' ),
				'screen' => 'schedule',
			),
			'patterns' => array(
				'label'  => __( 'Seasonal patterns', 'xlights-elementor-widgets' ),
				'icon'   => 'sparkles',
				'title'  => __( 'Seasonal light shows and patterns', 'xlights-elementor-widgets' ),
				'text'   => __( 'Switch to holiday colors and patterns for Christmas, Halloween, birthdays, game days and other celebrations — no ladders required.', 'xlights-elementor-widgets' ),
				'screen' => 'patterns',
			),
			'saved'    => array(
				'label'  => __( 'Saved favorites', 'xlights-elementor-widgets' ),
				'icon'   => 'heart',
				'title'  => __( 'Save the looks you love', 'xlights-elementor-widgets' ),
				'text'   => __( 'Save and customize your preferences so a favorite scene is always one tap away.', 'xlights-elementor-widgets' ),
				'screen' => 'scenes',
			),
			'remote'   => array(
				'label'  => __( 'Remote control', 'xlights-elementor-widgets' ),
				'icon'   => 'globe',
				'title'  => __( 'Manage your lights from anywhere', 'xlights-elementor-widgets' ),
				'text'   => __( 'Turn lights on, change colors or adjust a schedule when you are away from home.', 'xlights-elementor-widgets' ),
				'screen' => 'remote',
			),
			'share'    => array(
				'label'  => __( 'Shared access', 'xlights-elementor-widgets' ),
				'icon'   => 'users',
				'title'  => __( 'Share control with your household', 'xlights-elementor-widgets' ),
				'text'   => __( 'Give family members access so everyone can enjoy — and control — the lighting.', 'xlights-elementor-widgets' ),
				'screen' => 'share',
			),
		);
		if ( null === $only ) {
			return array_values( $all );
		}
		$out = array();
		foreach ( $only as $key ) {
			if ( isset( $all[ $key ] ) ) {
				$out[] = $all[ $key ];
			}
		}
		return $out;
	}

	/**
	 * Reasons to choose X LIGHTS.
	 *
	 * @return array
	 */
	public static function why_items() {
		return array(
			array(
				'icon'  => 'pencil',
				'title' => __( 'Design-first approach', 'xlights-elementor-widgets' ),
				'text'  => __( 'Every plan starts with your home’s architecture and the way you use your outdoor spaces after dark.', 'xlights-elementor-widgets' ),
			),
			array(
				'icon'  => 'eye',
				'title' => __( 'Low-profile product design', 'xlights-elementor-widgets' ),
				'text'  => __( 'A slim, color-matched track designed to blend into the roofline during the day.', 'xlights-elementor-widgets' ),
			),
			array(
				'icon'  => 'smartphone',
				'title' => __( 'Connected technology', 'xlights-elementor-widgets' ),
				'text'  => __( 'Compatible lighting is managed in one app — colors, scenes, schedules and shared access.', 'xlights-elementor-widgets' ),
			),
			array(
				'icon'  => 'wrench',
				'title' => __( 'Professional installation', 'xlights-elementor-widgets' ),
				'text'  => __( 'Planned and installed by our local team, so you never deal with ladders or seasonal setup again.', 'xlights-elementor-widgets' ),
			),
			array(
				'icon'  => 'map-pin',
				'title' => __( 'Local service', 'xlights-elementor-widgets' ),
				'text'  => __( 'A {market} team serving {area}, here when you have questions after installation.', 'xlights-elementor-widgets' ),
			),
			array(
				'icon'  => 'shield-check',
				'title' => __( 'Licensed and insured', 'xlights-elementor-widgets' ),
				'text'  => __( 'Locally owned, licensed and insured for your peace of mind.', 'xlights-elementor-widgets' ),
				'gate'  => 'credentials',
			),
		);
	}

	/**
	 * Final consultation CTA.
	 *
	 * @param string $heading Heading.
	 * @param string $text    Text.
	 * @param array  $extra   Extra defaults.
	 * @return array
	 */
	public static function cta( $heading, $text, array $extra = array() ) {
		return array(
			'type'     => 'cta',
			'label'    => __( 'Final Consultation CTA', 'xlights-elementor-widgets' ),
			'defaults' => array_merge(
				array(
					'style'         => 'panel',
					'eyebrow'       => __( 'Free consultation', 'xlights-elementor-widgets' ),
					'heading'       => $heading,
					'text'          => $text,
					'primary_label' => '{cta}',
					'primary_link'  => '{quote_url}',
					'show_phone'    => 'yes',
					'bg'            => 'light',
				),
				$extra
			),
		);
	}

	/**
	 * Financing section definition.
	 *
	 * @param string $bg Background.
	 * @return array
	 */
	public static function financing( $bg = 'light' ) {
		return array(
			'type'     => 'financing',
			'label'    => __( 'Financing', 'xlights-elementor-widgets' ),
			'defaults' => array(
				'bg'      => $bg,
				'spacing' => 'compact',
			),
		);
	}

	/**
	 * Projects section definition.
	 *
	 * @param string $source  Filter key or "featured".
	 * @param string $heading Heading.
	 * @param string $text    Text.
	 * @param array  $extra   Extra defaults.
	 * @return array
	 */
	public static function projects( $source, $heading, $text = '', array $extra = array() ) {
		return array(
			'type'     => 'projects',
			'label'    => __( 'Project Gallery', 'xlights-elementor-widgets' ),
			'defaults' => array_merge(
				array(
					'eyebrow'    => __( 'Our work', 'xlights-elementor-widgets' ),
					'heading'    => $heading,
					'text'       => $text,
					'source'     => $source,
					'count'      => 6,
					'link_label' => __( 'View all projects', 'xlights-elementor-widgets' ),
					'link'       => '{page:projects}',
					'bg'         => 'light',
				),
				$extra
			),
		);
	}
}
