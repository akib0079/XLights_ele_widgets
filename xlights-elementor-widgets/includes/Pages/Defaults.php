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
	 * Bundled client photo (assets/images/photos/<name>.webp, with -640/-1024 copies for srcset).
	 *
	 * @param string $name File name without extension.
	 * @return string
	 */
	public static function photo( $name ) {
		return Html::asset_image( 'photos/' . $name . '.webp' );
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
				'title'  => __( 'Pick any color, set the brightness', 'xlights-elementor-widgets' ),
				'text'   => __( 'Choose a shade on the color selector or enter exact RGB values, set the brightness with a slider, or tap one of the common colors — from soft warm whites to full color.', 'xlights-elementor-widgets' ),
				'screen' => 'color',
			),
			'patterns' => array(
				'label'  => __( 'Patterns', 'xlights-elementor-widgets' ),
				'icon'   => 'sparkles',
				'title'  => __( 'Built-in patterns for every occasion', 'xlights-elementor-widgets' ),
				'text'   => __( 'Tap a pattern for Christmas, Easter, Halloween, Thanksgiving, St. Patrick’s Day and more, use architectural warm or bright white looks, keep favorites, or add your own.', 'xlights-elementor-widgets' ),
				'screen' => 'patterns',
			),
			'music'    => array(
				'label'  => __( 'Music mode', 'xlights-elementor-widgets' ),
				'icon'   => 'music',
				'title'  => __( 'Lights that dance to the music', 'xlights-elementor-widgets' ),
				'text'   => __( 'Switch to Music, pick a song from your list and choose an effect — the lights move with the music.', 'xlights-elementor-widgets' ),
				'screen' => 'music',
			),
			'schedule' => array(
				'label'  => __( 'Timers', 'xlights-elementor-widgets' ),
				'icon'   => 'timer',
				'title'  => __( 'Timers for every day and every season', 'xlights-elementor-widgets' ),
				'text'   => __( 'Set lights to turn on and off every day, or schedule a holiday look for specific dates — each timer with its own colors.', 'xlights-elementor-widgets' ),
				'screen' => 'schedule',
			),
			'zones'    => array(
				'label'  => __( 'Controllers', 'xlights-elementor-widgets' ),
				'icon'   => 'smartphone',
				'title'  => __( 'Every zone in one list', 'xlights-elementor-widgets' ),
				'text'   => __( 'Soffit, deck, yard and backyard controllers each appear in My Controller with their online status — switch any zone on or off with a tap.', 'xlights-elementor-widgets' ),
				'screen' => 'zones',
			),
			'share'    => array(
				'label'  => __( 'Family sharing', 'xlights-elementor-widgets' ),
				'icon'   => 'users',
				'title'  => __( 'Share control with your family', 'xlights-elementor-widgets' ),
				'text'   => __( 'Add family members under Family Management so everyone at home can use the lights from their own phone.', 'xlights-elementor-widgets' ),
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
	 * "All fixtures are" feature strip from the official X LIGHTS deck and
	 * landscape product sheets.
	 *
	 * @param string $bg Background.
	 * @return array
	 */
	public static function fixture_features( $bg = 'light' ) {
		return array(
			'type'     => 'cards',
			'label'    => __( 'Fixture Features', 'xlights-elementor-widgets' ),
			'defaults' => array(
				'eyebrow' => __( 'All fixtures are', 'xlights-elementor-widgets' ),
				'heading' => __( 'Premium lighting. <em>Endless possibilities.</em>', 'xlights-elementor-widgets' ),
				'style'   => 'strip',
				'items'   => array(
					array(
						'icon'  => 'palette',
						'title' => __( 'Individually programmable RGBW', 'xlights-elementor-widgets' ),
						'text'  => __( 'Control every fixture with millions of colors, dynamic effects and custom scenes.', 'xlights-elementor-widgets' ),
					),
					array(
						'icon'  => 'droplet',
						'title' => __( 'IP67 weatherproof', 'xlights-elementor-widgets' ),
						'text'  => __( 'Engineered to perform in the harshest outdoor environments.', 'xlights-elementor-widgets' ),
					),
					array(
						'icon'  => 'link',
						'title' => __( 'Quick connect technology', 'xlights-elementor-widgets' ),
						'text'  => __( 'Plug-and-play connections for fast, clean and secure installation.', 'xlights-elementor-widgets' ),
					),
					array(
						'icon'  => 'shield',
						'title' => __( 'Premium build quality', 'xlights-elementor-widgets' ),
						'text'  => __( 'Durable materials and professional design for long-lasting reliability.', 'xlights-elementor-widgets' ),
					),
					array(
						'icon'  => 'plus',
						'title' => __( 'Expandable system', 'xlights-elementor-widgets' ),
						'text'  => __( 'Easily add fixtures, zones and controllers as your project grows.', 'xlights-elementor-widgets' ),
					),
					array(
						'icon'  => 'bolt',
						'title' => __( 'Low voltage', 'xlights-elementor-widgets' ),
						'text'  => __( 'Safe and efficient 12V–24V system with advanced LED technology.', 'xlights-elementor-widgets' ),
					),
				),
				'bg'      => $bg,
			),
		);
	}

	/**
	 * Final consultation CTA. Passing an "image" in $extra switches it to the
	 * background-photo style.
	 *
	 * @param string $heading Heading.
	 * @param string $text    Text.
	 * @param array  $extra   Extra defaults.
	 * @return array
	 */
	public static function cta( $heading, $text, array $extra = array() ) {
		if ( ! empty( $extra['image'] ) ) {
			$extra += array(
				'style'   => 'image',
				'overlay' => 62,
			);
		}
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
	 * Everyday ↔ Holiday comparison slider: the same home in warm white and in
	 * holiday color (Soffit and Home pages).
	 *
	 * @param array $extra Extra defaults.
	 * @return array
	 */
	public static function holiday_compare( array $extra = array() ) {
		return array(
			'type'     => 'before_after',
			'label'    => __( 'Everyday / Holiday Comparison', 'xlights-elementor-widgets' ),
			'defaults' => array_merge(
				array(
					'eyebrow'      => __( 'Same roofline, two looks', 'xlights-elementor-widgets' ),
					'heading'      => __( 'Warm white every night. <em>Color when you want it.</em>', 'xlights-elementor-widgets' ),
					'text'         => __( 'Drag the slider to switch the same home from everyday warm white to holiday color.', 'xlights-elementor-widgets' ),
					'source'       => 'manual',
					'before_image' => self::photo( 'roofline-warm-white-modern' ),
					'after_image'  => self::photo( 'roofline-holiday-color-modern' ),
					'before_label' => __( 'Everyday', 'xlights-elementor-widgets' ),
					'after_label'  => __( 'Holiday', 'xlights-elementor-widgets' ),
					'bg'           => 'dark',
					'editor_note'  => __( 'Shows the same home in two looks. To use your own, upload two photos of one home taken from the same spot (for example day and night, or warm white and holiday color).', 'xlights-elementor-widgets' ),
				),
				$extra
			),
		);
	}

	/**
	 * Luxury home automation: compatibility with Control4, Lutron and Crestron
	 * (as stated by X LIGHTS) and the Bond partnership for outdoor patio systems.
	 *
	 * @param array $extra Extra defaults.
	 * @return array
	 */
	public static function home_automation( array $extra = array() ) {
		return array(
			'type'     => 'split',
			'label'    => __( 'Luxury Home Automation', 'xlights-elementor-widgets' ),
			'defaults' => array_merge(
				array(
					'layout'          => 'cinematic',
					'eyebrow'         => __( 'Luxury home automation', 'xlights-elementor-widgets' ),
					'heading'         => __( 'Part of the smart home <em>you already have.</em>', 'xlights-elementor-widgets' ),
					'text'            => __( '<p>X LIGHTS is compatible with luxury home automation systems such as Control4, Lutron and Crestron, so your outdoor lighting becomes part of the system that already runs your home.</p><p>We have also partnered with Bond for outdoor patio systems. Through Bond Bridge, your lights work alongside louvers, shades, screens, awnings and fans from a single app or remote — while the X LIGHTS app handles advanced color customization.</p>', 'xlights-elementor-widgets' ),
					'points'          => __( "Control4, Lutron and Crestron
Bond Bridge for outdoor patio systems
Lights, shades and fans from one app or remote
Advanced colors and patterns in the X LIGHTS app", 'xlights-elementor-widgets' ),
					'secondary_label' => __( 'More about the app and integrations', 'xlights-elementor-widgets' ),
					'secondary_link'  => '{page:app}',
					'visual'          => 'image',
					'image'           => self::photo( 'pergola-lounge' ),
					'image_alt'       => __( 'Covered outdoor lounge and kitchen under a louvered pergola with recessed lighting at dusk', 'xlights-elementor-widgets' ),
					'scene'           => 'roofline',
					'image_position'  => 'right',
					'bg'              => 'dark',
					'editor_note'     => __( 'Compatibility as stated by X LIGHTS. Confirm the systems in each customer’s proposal.', 'xlights-elementor-widgets' ),
				),
				$extra
			),
		);
	}

	/**
	 * Bond Bridge smart-home integration (official X LIGHTS copy).
	 *
	 * @param array $extra Extra defaults (bg, image_position, layout).
	 * @return array
	 */
	public static function bond_bridge( array $extra = array() ) {
		return array(
			'type'     => 'split',
			'label'    => __( 'Smart-Home Integration (Bond Bridge)', 'xlights-elementor-widgets' ),
			'defaults' => array_merge(
				array(
					'eyebrow'        => __( 'Bond Bridge integration', 'xlights-elementor-widgets' ),
					'heading'        => __( 'Seamless integration with <em>luxury home automation.</em>', 'xlights-elementor-widgets' ),
					'text'           => __( '<p>X LIGHTS now integrates with Bond Bridge, syncing your outdoor lighting with luxury home automation systems like Control4 and Savant. You can now manage lights, shades, and fans through a single app or remote, while still using the X Lights app for advanced color customization.</p><p>Your lighting works alongside other automated patio features such as louvers, shades, screens, awnings and fans, with scheduling and scene creation from the remote or smart-home app you already use.</p>', 'xlights-elementor-widgets' ),
					'points'         => __(
						'Alexa and Google Home
Control4, Savant and Crestron
Loxone, RTI and Josh.ai
Advanced colors and patterns stay in the X LIGHTS app',
						'xlights-elementor-widgets'
					),
					'visual'         => 'image',
					'image'          => self::photo( 'bond-integrations' ),
					'image_alt'      => __( 'Bond works with Alexa, Google Home, Samsung SmartThings, Josh.ai, Control4, Nice, Crestron, Savant, RTI, Hubitat, Homebridge, URC and Loxone', 'xlights-elementor-widgets' ),
					'image_fit'      => 'contain',
					'scene'          => 'roofline',
					'image_position' => 'right',
					'bg'             => 'warm',
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
