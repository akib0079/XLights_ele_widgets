<?php
/**
 * Page template: X LIGHTS App.
 *
 * Smart-home (Bond Bridge) content is deliberately cautious and editable:
 * compatibility must be confirmed per product before it is promised.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'       => array(
		'type'     => 'hero',
		'label'    => __( 'App Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'split',
			'eyebrow'         => __( 'The X LIGHTS app', 'xlights-elementor-widgets' ),
			'heading'         => __( 'One app. <em>Complete control</em> of your outdoor lighting.', 'xlights-elementor-widgets' ),
			'text'            => __( 'Set the everyday look, plan the holidays and manage compatible soffit, deck, landscape and bistro lighting from your phone.', 'xlights-elementor-widgets' ),
			'primary_label'   => __( 'Design a connected system', 'xlights-elementor-widgets' ),
			'primary_link'    => '{quote_url}',
			'secondary_label' => __( 'See the features', 'xlights-elementor-widgets' ),
			'secondary_link'  => '#xl-app-showcase',
			'show_phone'      => '',
			'visual'          => 'phone',
			'screen'          => 'patterns',
			'bg'              => 'dark',
		),
	),
	'connected'  => array(
		'type'     => 'connected',
		'label'    => __( 'One Connected Lighting System', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'One connected system', 'xlights-elementor-widgets' ),
			'heading' => __( 'Every compatible zone, <em>one place to control it.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>Group your roofline, patio, deck and garden lighting, then turn everything on together, give each area its own look, or share one theme across the whole property.</p>', 'xlights-elementor-widgets' ),
			'items'   => Defaults::connected_items(),
			'note'    => Defaults::compatibility_note(),
			'bg'      => 'light',
		),
	),
	'showcase'   => array(
		'type'     => 'app_showcase',
		'label'    => __( 'App Interface Showcase', 'xlights-elementor-widgets' ),
		'anchor'   => 'xl-app-showcase',
		'defaults' => array(
			'eyebrow'        => __( 'Inside the app', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Designed to be <em>simple every day.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( 'Tap through the main features of the X LIGHTS app.', 'xlights-elementor-widgets' ),
			'editor_note'    => __( 'Each tab shows an official screenshot of the X LIGHTS app. To show a newer screen, upload a plain screenshot in that tab.', 'xlights-elementor-widgets' ),
			'items'          => Defaults::app_tabs(),
			'phone_position' => 'left',
			'show_store'     => 'yes',
			'bg'             => 'dark',
		),
	),
	'categories' => array(
		'type'     => 'cards',
		'label'    => __( 'Supported Lighting Categories', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'What it controls', 'xlights-elementor-widgets' ),
			'heading' => __( 'Built for the whole exterior.', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '4',
			'items'   => array(
				array(
					'icon'       => 'roofline',
					'title'      => __( 'Permanent soffit lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Roofline lighting with warm white and color.', 'xlights-elementor-widgets' ),
					'link_label' => __( 'Learn more', 'xlights-elementor-widgets' ),
					'link'       => '{page:soffit}',
				),
				array(
					'icon'       => 'stairs',
					'title'      => __( 'Deck lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Steps, railings and walkways.', 'xlights-elementor-widgets' ),
					'link_label' => __( 'Learn more', 'xlights-elementor-widgets' ),
					'link'       => '{page:deck}',
				),
				array(
					'icon'       => 'tree',
					'title'      => __( 'Landscape lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Trees, gardens and paths.', 'xlights-elementor-widgets' ),
					'link_label' => __( 'Learn more', 'xlights-elementor-widgets' ),
					'link'       => '{page:landscape}',
				),
				array(
					'icon'       => 'string-lights',
					'title'      => __( 'Bistro lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'App features depend on the bistro product selected.', 'xlights-elementor-widgets' ),
					'link_label' => __( 'Learn more', 'xlights-elementor-widgets' ),
					'link'       => '{page:bistro}',
				),
			),
			'note'    => Defaults::compatibility_note(),
			'bg'      => 'warm',
		),
	),
	'scenes'     => array(
		'type'     => 'cards',
		'label'    => __( 'Custom Scenes & Patterns', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Custom scenes & patterns', 'xlights-elementor-widgets' ),
			'heading' => __( 'Create a look for every occasion.', 'xlights-elementor-widgets' ),
			'style'   => 'image',
			'columns' => '3',
			'items'   => array(
				array(
					'title'   => __( 'Everyday elegance', 'xlights-elementor-widgets' ),
					'text'    => __( 'Warm white that highlights your architecture.', 'xlights-elementor-widgets' ),
					'image'   => Defaults::photo( 'roofline-warm-white-modern' ),
					'scene'   => 'roofline',
					'palette' => 'warm',
				),
				array(
					'title'   => __( 'Holiday seasons', 'xlights-elementor-widgets' ),
					'text'    => __( 'Festive color without the ladder.', 'xlights-elementor-widgets' ),
					'image'   => Defaults::photo( 'roofline-holiday-color-modern' ),
					'scene'   => 'roofline',
					'palette' => 'holiday',
				),
				array(
					'title'   => __( 'Celebrations', 'xlights-elementor-widgets' ),
					'text'    => __( 'Birthdays, parties, game days and more.', 'xlights-elementor-widgets' ),
					'image'   => Defaults::photo( 'steps-color-celebration' ),
					'scene'   => 'roofline',
					'palette' => 'celebration',
				),
			),
			'bg'      => 'light',
		),
	),
	'smart_home' => Defaults::bond_bridge( array( 'bg' => 'warm' ) ),
	'download'   => array(
		'type'     => 'cta',
		'label'    => __( 'App Download', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'style'         => 'image',
			'image'         => Defaults::photo( 'deck-steps-app' ),
			'overlay'       => 62,
			'eyebrow'       => __( 'Get the app', 'xlights-elementor-widgets' ),
			'heading'       => __( 'Download the X LIGHTS app.', 'xlights-elementor-widgets' ),
			'text'          => __( 'Available on the App Store and Google Play. Control your lights from your phone — we will help you connect your system after installation.', 'xlights-elementor-widgets' ),
			'primary_label' => '',
			'primary_link'  => '',
			'show_phone'    => '',
			'show_store'    => 'yes',
			'gate'          => 'app_links',
			'bg'            => 'dark',
		),
	),
	'cta'        => Defaults::cta(
		__( 'Design a connected lighting system.', 'xlights-elementor-widgets' ),
		__( 'We will plan your soffit, patio, deck and landscape lighting so it all works together from day one.', 'xlights-elementor-widgets' ),
		array( 'image' => Defaults::photo( 'firepit-lounge-string-lights' ), 'bg' => 'warm' )
	),
);
