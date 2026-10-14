<?php
/**
 * Page template: Hanging Bulb Bistro Lighting.
 *
 * The corporate site groups string-bulb lighting with deck products; exact
 * bistro product names, bulb specs and app features are left editable and
 * approval-gated until confirmed for this dealer.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'        => array(
		'type'     => 'hero',
		'label'    => __( 'Bistro Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'media',
			'height'          => 'tall',
			'eyebrow'         => __( 'Hanging Bulb Bistro Lighting', 'xlights-elementor-widgets' ),
			'eyebrow_in_h1'   => 'yes',
			'heading'         => __( 'Make the patio <em>everyone wants to spend time on.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( 'Bistro lighting designed around your patio, pergola or outdoor dining space in {area}.', 'xlights-elementor-widgets' ),
			'primary_label'   => __( 'Get a Bistro Lighting Quote', 'xlights-elementor-widgets' ),
			'primary_link'    => '{page:contact}?service=bistro#quote',
			'secondary_label' => __( 'View patio projects', 'xlights-elementor-widgets' ),
			'secondary_link'  => '#xl-bistro-projects',
			'visual'          => 'image',
			'image'           => Defaults::photo( 'firepit-lounge-string-lights' ),
			'image_alt'       => __( 'Overhead view of a backyard lounge with a fire table under string lights', 'xlights-elementor-widgets' ),
			'scene'           => 'patio',
			'overlay'         => 45,
			'editor_note'     => __( 'Add an inviting nighttime patio photo here — this page relies on great patio photography.', 'xlights-elementor-widgets' ),
		),
	),
	'intro'       => array(
		'type'     => 'intro',
		'label'    => __( 'Lifestyle Introduction', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'split',
			'eyebrow' => __( 'Outdoor living', 'xlights-elementor-widgets' ),
			'heading' => __( 'A welcoming glow for <em>the evenings you remember.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>Hanging bulb bistro lights bring a welcoming glow to outdoor dining areas, patios and gathering spaces. X LIGHTS designs a layout around your space to create the atmosphere you want after dark.</p><p>Choose a classic warm glow, or change the mood for special occasions using the X LIGHTS app where your bistro product supports it.</p>', 'xlights-elementor-widgets' ),
			'bg'      => 'light',
		),
	),
	'atmosphere'  => array(
		'type'     => 'split',
		'label'    => __( 'Premium Patio Atmosphere', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'         => 'offset',
			'eyebrow'        => __( 'Atmosphere', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Light that makes you <em>stay a little longer.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>The right string of bulbs turns a patio into an outdoor room — soft enough for conversation, bright enough for dinner, and beautiful in photos.</p>', 'xlights-elementor-widgets' ),
			'points'         => __( "Soft, flattering light for dining\nA defined space for entertaining\nA warm view from inside the house", 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'image'          => Defaults::photo( 'bistro-string-backyard' ),
			'image_alt'      => __( 'Backyard path and seating under strings of warm bistro lights at night', 'xlights-elementor-widgets' ),
			'scene'          => 'patio',
			'image_position' => 'right',
			'bg'             => 'dark',
		),
	),
	'residential' => array(
		'type'     => 'cards',
		'label'    => __( 'Residential Applications', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'At home', 'xlights-elementor-widgets' ),
			'heading' => __( 'Where homeowners use bistro lighting.', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '4',
			'items'   => array(
				array(
					'icon'  => 'utensils',
					'title' => __( 'Outdoor dining', 'xlights-elementor-widgets' ),
					'text'  => __( 'Over the table for long dinners outside.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'home',
					'title' => __( 'Patios & pergolas', 'xlights-elementor-widgets' ),
					'text'  => __( 'Define the space and add a warm canopy of light.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'water',
					'title' => __( 'Pool & spa areas', 'xlights-elementor-widgets' ),
					'text'  => __( 'Ambient light for evening swims and lounging.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'users',
					'title' => __( 'Entertaining spaces', 'xlights-elementor-widgets' ),
					'text'  => __( 'Backyards ready for gatherings and celebrations.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'light',
		),
	),
	'restaurants' => array(
		'type'     => 'split',
		'label'    => __( 'Restaurants & Outdoor Dining', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'         => 'cinematic',
			'eyebrow'        => __( 'Restaurants & hospitality', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Patio lighting that <em>brings guests outside.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>For restaurants, wineries and hospitality venues, bistro lighting sets the tone before guests sit down. We plan layouts that work with seating, walkways and the architecture of your building.</p>', 'xlights-elementor-widgets' ),
			'primary_label'  => __( 'Commercial lighting', 'xlights-elementor-widgets' ),
			'primary_link'   => '{page:commercial}',
			'visual'         => 'image',
			'image'          => Defaults::photo( 'pergola-bar' ),
			'image_alt'      => __( 'Outdoor bar seating under a pergola with recessed and accent lighting', 'xlights-elementor-widgets' ),
			'scene'          => 'commercial',
			'image_position' => 'left',
			'bg'             => 'warm',
		),
	),
	'layouts'     => array(
		'type'     => 'cards',
		'label'    => __( 'Custom Installation Layouts', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Custom layouts', 'xlights-elementor-widgets' ),
			'heading' => __( 'Designed around your space.', 'xlights-elementor-widgets' ),
			'text'    => __( 'Every patio is different. Here is how we plan a bistro layout.', 'xlights-elementor-widgets' ),
			'style'   => 'steps',
			'columns' => '4',
			'items'   => array(
				array(
					'title' => __( 'Walk the space', 'xlights-elementor-widgets' ),
					'text'  => __( 'We look at how you use the area, where people sit and how it looks from inside.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Plan the runs', 'xlights-elementor-widgets' ),
					'text'  => __( 'Perimeter, crisscross or canopy — we choose spans and mounting points that suit the space.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Choose the look', 'xlights-elementor-widgets' ),
					'text'  => __( 'Bulb style, spacing and light color, matched to your outdoor design.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Install & fine-tune', 'xlights-elementor-widgets' ),
					'text'  => __( 'Professional installation and a walkthrough of the controls.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'light',
		),
	),
	'ambient'     => array(
		'type'     => 'split',
		'label'    => __( 'Warm Ambient Illumination', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Warm ambient light', 'xlights-elementor-widgets' ),
			'heading'        => __( 'The classic bistro glow.', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Warm white bulbs create the relaxed, golden atmosphere bistro lighting is known for — inviting without being harsh, and comfortable for hours of conversation.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'image'          => Defaults::photo( 'deck-string-lights' ),
			'image_alt'      => __( 'Backyard deck at night with string lights on a pergola and lights along the roofline', 'xlights-elementor-widgets' ),
			'scene'          => 'patio',
			'palette'        => 'warm',
			'image_position' => 'right',
			'bg'             => 'warm',
		),
	),
	'events'      => array(
		'type'     => 'split',
		'label'    => __( 'Special-Event Lighting (where supported)', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'         => 'offset',
			'eyebrow'        => __( 'Special occasions', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Change the mood for <em>special occasions.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>With compatible color-capable bistro products, you can switch to festive colors for birthdays, holidays and game nights using the X LIGHTS app.</p>', 'xlights-elementor-widgets' ),
			'note'           => __( 'Color and app features depend on the bistro product selected for your project.', 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'image'          => Defaults::photo( 'pergola-string-lights' ),
			'image_alt'      => __( 'String lights and a softly lit pergola canopy against the evening sky', 'xlights-elementor-widgets' ),
			'scene'          => 'patio',
			'palette'        => 'celebration',
			'image_position' => 'left',
			'gate'           => 'pending',
			'bg'             => 'dark',
			'editor_note'    => __( 'Hidden until the dealer confirms which bistro products support color and app control. Change "Publication" to "Always show" once confirmed.', 'xlights-elementor-widgets' ),
		),
	),
	'connected'   => array(
		'type'     => 'connected',
		'label'    => __( 'X LIGHTS Connected System', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Part of the system', 'xlights-elementor-widgets' ),
			'heading' => __( 'Pair your patio with the rest of your lighting.', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>Bistro lighting can be planned alongside soffit, deck and landscape lighting so your whole outdoor space feels connected.</p>', 'xlights-elementor-widgets' ),
			'items'   => Defaults::connected_items(),
			'note'    => Defaults::compatibility_note(),
			'bg'      => 'light',
		),
	),
	'projects'    => array_merge_recursive(
		Defaults::projects( 'bistro', __( 'Bistro & patio projects', 'xlights-elementor-widgets' ), '', array( 'bg' => 'warm' ) ),
		array( 'anchor' => 'xl-bistro-projects' )
	),
	'faq'         => array(
		'type'     => 'faq',
		'label'    => __( 'FAQ', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'FAQ', 'xlights-elementor-widgets' ),
			'heading' => __( 'Bistro lighting questions', 'xlights-elementor-widgets' ),
			'items'   => array(
				array(
					'question' => __( 'Do I need posts or a pergola?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Not always. We look at existing structures, walls and trees first, and recommend additional mounting points only where the layout needs them.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Can bistro lights change color?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'It depends on the bistro product selected. We will confirm color and app capabilities for your project during the consultation.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Do you install bistro lighting for restaurants?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Yes. We plan commercial patio lighting around seating, walkways and the building. Visit our Commercial Lighting page or request a commercial consultation.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'How do I get a quote?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Send us a request with a few photos of your patio. We will follow up to schedule a consultation.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'light',
		),
	),
	'specs'       => array(
		'type'     => 'specs',
		'label'    => __( 'Bistro Product Details', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'     => __( 'Product details', 'xlights-elementor-widgets' ),
			'heading'     => __( 'Bistro lighting details', 'xlights-elementor-widgets' ),
			'rows'        => array(
				array(
					'label'    => __( 'Product', 'xlights-elementor-widgets' ),
					'value'    => __( 'X LIGHTS string bulb lights', 'xlights-elementor-widgets' ),
					'approved' => 'yes',
				),
				array(
					'label'    => __( 'Bulb spacing & drop', 'xlights-elementor-widgets' ),
					'value'    => __( '31" spacing, 4" drop', 'xlights-elementor-widgets' ),
					'approved' => 'yes',
				),
				array(
					'label'    => __( 'Color', 'xlights-elementor-widgets' ),
					'value'    => __( 'Full RGBW', 'xlights-elementor-widgets' ),
					'approved' => 'yes',
				),
				array(
					'label'    => __( 'App control', 'xlights-elementor-widgets' ),
					'value'    => __( 'X LIGHTS app, including the dance-to-the-music feature', 'xlights-elementor-widgets' ),
					'approved' => 'yes',
				),
				array(
					'label'    => __( 'Output', 'xlights-elementor-widgets' ),
					'value'    => __( '60 lumens, 1 W per bulb, 24 V', 'xlights-elementor-widgets' ),
					'approved' => 'yes',
				),
				array(
					'label'    => __( 'Bulbs', 'xlights-elementor-widgets' ),
					'value'    => __( 'Impact-resistant', 'xlights-elementor-widgets' ),
					'approved' => 'yes',
				),
			),
			'spacing'     => 'compact',
			'bg'          => 'warm',
			'editor_note' => __( 'Values from the X LIGHTS website (string bulb lights). Update them if the dealer sells a different bistro product.', 'xlights-elementor-widgets' ),
		),
	),
	'cta'         => Defaults::cta(
		__( 'Let’s design your patio lighting.', 'xlights-elementor-widgets' ),
		__( 'Send a few photos of your patio or outdoor dining area and we will recommend a layout.', 'xlights-elementor-widgets' ),
		array(
			'image'         => Defaults::photo( 'bistro-string-backyard' ),
			'primary_label' => __( 'Get a Bistro Lighting Quote', 'xlights-elementor-widgets' ),
			'primary_link'  => '{page:contact}?service=bistro#quote',
			'bg'            => 'warm',
		)
	),
);
