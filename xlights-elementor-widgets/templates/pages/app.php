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
	'hero'        => array(
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
			'screen'          => 'zones',
			'bg'              => 'dark',
		),
	),
	'connected'   => array(
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
	'showcase'    => array(
		'type'     => 'app_showcase',
		'label'    => __( 'App Interface Showcase', 'xlights-elementor-widgets' ),
		'anchor'   => 'xl-app-showcase',
		'defaults' => array(
			'eyebrow'        => __( 'Inside the app', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Designed to be <em>simple every day.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( 'Tap through the main features of the X LIGHTS app.', 'xlights-elementor-widgets' ),
			'editor_note'    => __( 'Phone screens are illustrations modeled on the app. Upload current app screenshots in each feature tab when available.', 'xlights-elementor-widgets' ),
			'items'          => Defaults::app_tabs(),
			'phone_position' => 'left',
			'show_store'     => 'yes',
			'bg'             => 'dark',
		),
	),
	'categories'  => array(
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
	'preferences' => array(
		'type'     => 'split',
		'label'    => __( 'Save & Customize Preferences', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Saved preferences', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Save the looks you love.', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Found the perfect warm white? A color palette for your team? Save it, name it and bring it back with one tap.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'phone',
			'screen'         => 'scenes',
			'image_position' => 'right',
			'bg'             => 'light',
		),
	),
	'scheduling'  => array(
		'type'     => 'split',
		'label'    => __( 'Smart Scheduling & Timers', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Scheduling & timers', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Set it once. <em>Enjoy it every night.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Create timers for everyday lighting, weekend entertaining and holiday seasons, so your lights turn on and off without a second thought.</p>', 'xlights-elementor-widgets' ),
			'points'         => __( "Everyday on/off timers\nSeasonal schedules for the holidays\nDifferent schedules for different zones", 'xlights-elementor-widgets' ),
			'visual'         => 'phone',
			'screen'         => 'schedule',
			'image_position' => 'left',
			'bg'             => 'warm',
		),
	),
	'seasonal'    => array(
		'type'     => 'split',
		'label'    => __( 'Seasonal Light Shows & Patterns', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Seasonal light shows', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Ready for <em>every celebration.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Christmas, Halloween, Valentine’s Day, birthdays, game days — choose a pattern or build your own and switch back to warm white when the celebration is over.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'phone',
			'screen'         => 'patterns',
			'image_position' => 'right',
			'bg'             => 'dark',
		),
	),
	'remote'      => array(
		'type'     => 'split',
		'label'    => __( 'Remote Management', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Remote management', 'xlights-elementor-widgets' ),
			'heading'        => __( 'In control, even when you are away.', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Turn the lights on before you get home, adjust a schedule while traveling, or check that everything is set from wherever you are.</p>', 'xlights-elementor-widgets' ),
			'note'           => __( 'Remote access requires the controller to be connected to your home Wi-Fi.', 'xlights-elementor-widgets' ),
			'visual'         => 'phone',
			'screen'         => 'remote',
			'image_position' => 'left',
			'bg'             => 'light',
		),
	),
	'sharing'     => array(
		'type'     => 'split',
		'label'    => __( 'Shared Access', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Shared access', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Everyone at home can join in.', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Share access with family members so everyone can turn on the lights or pick tonight’s colors from their own phone.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'phone',
			'screen'         => 'share',
			'image_position' => 'right',
			'bg'             => 'warm',
		),
	),
	'color'       => array(
		'type'     => 'split',
		'label'    => __( 'Color & Brightness Control', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Color & brightness', 'xlights-elementor-widgets' ),
			'heading'        => __( 'From soft warm white <em>to full color.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Choose the exact shade of white that suits your home, or pick vivid colors for a celebration. Brightness is adjustable so the lighting always feels right.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'phone',
			'screen'         => 'color',
			'image_position' => 'left',
			'bg'             => 'light',
		),
	),
	'scenes'      => array(
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
					'scene'   => 'roofline',
					'palette' => 'warm',
				),
				array(
					'title'   => __( 'Holiday seasons', 'xlights-elementor-widgets' ),
					'text'    => __( 'Festive color without the ladder.', 'xlights-elementor-widgets' ),
					'scene'   => 'roofline',
					'palette' => 'holiday',
				),
				array(
					'title'   => __( 'Celebrations', 'xlights-elementor-widgets' ),
					'text'    => __( 'Birthdays, parties, game days and more.', 'xlights-elementor-widgets' ),
					'scene'   => 'roofline',
					'palette' => 'celebration',
				),
			),
			'bg'      => 'warm',
		),
	),
	'smart_home'  => array(
		'type'     => 'split',
		'label'    => __( 'Smart-Home Integration (Bond Bridge)', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Smart-home integration', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Works with your smart home — <em>where supported.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Through Bond Bridge integration, compatible X LIGHTS products can be connected to popular smart-home platforms for convenient on/off and scene control. Advanced color and pattern control stays in the X LIGHTS app.</p>', 'xlights-elementor-widgets' ),
			'note'           => __( 'Compatibility depends on the specific product and platform. Ask your designer to confirm what your system supports.', 'xlights-elementor-widgets' ),
			'visual'         => 'illustration',
			'scene'          => 'landscape',
			'image_position' => 'right',
			'gate'           => '',
			'bg'             => 'light',
			'editor_note'    => __( 'Confirm Bond Bridge compatibility for each product sold by this dealer before publishing. Set "Publication" to pending to hide this section until then.', 'xlights-elementor-widgets' ),
		),
	),
	'download'    => array(
		'type'     => 'cta',
		'label'    => __( 'App Download', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'style'         => 'band',
			'eyebrow'       => __( 'Get the app', 'xlights-elementor-widgets' ),
			'heading'       => __( 'Download the X LIGHTS app.', 'xlights-elementor-widgets' ),
			'text'          => __( 'Control your lights from your phone. We will help you connect your system after installation.', 'xlights-elementor-widgets' ),
			'primary_label' => '',
			'primary_link'  => '',
			'show_phone'    => '',
			'show_store'    => 'yes',
			'gate'          => 'app_links',
			'bg'            => 'dark',
		),
	),
	'cta'         => Defaults::cta(
		__( 'Design a connected lighting system.', 'xlights-elementor-widgets' ),
		__( 'We will plan your soffit, patio, deck and landscape lighting so it all works together from day one.', 'xlights-elementor-widgets' ),
		array( 'bg' => 'warm' )
	),
);
