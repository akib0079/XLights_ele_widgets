<?php
/**
 * Page template: Deck Lighting. No safety certifications or product ratings
 * are claimed; product categories mirror those shown on the corporate site.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'      => array(
		'type'     => 'hero',
		'label'    => __( 'Deck Lighting Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'media',
			'height'          => 'tall',
			'eyebrow'         => __( 'Deck Lighting', 'xlights-elementor-widgets' ),
			'eyebrow_in_h1'   => 'yes',
			'heading'         => __( 'The details that bring <em>an outdoor space together.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( 'Subtle step, railing and walkway lighting for decks and patios in {area}.', 'xlights-elementor-widgets' ),
			'primary_label'   => __( 'Get a Deck Lighting Quote', 'xlights-elementor-widgets' ),
			'primary_link'    => '{page:contact}?service=deck#quote',
			'secondary_label' => __( 'Explore product styles', 'xlights-elementor-widgets' ),
			'secondary_link'  => '#xl-deck-products',
			'visual'          => 'image',
			'image'           => Defaults::photo( 'deck-steps-desert' ),
			'image_alt'       => __( 'Raised patio with glowing step edges around a fire pit at dusk', 'xlights-elementor-widgets' ),
			'scene'           => 'deck',
			'overlay'         => 45,
		),
	),
	'intro'     => array(
		'type'     => 'intro',
		'label'    => __( 'Service Introduction', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'split',
			'eyebrow' => __( 'Deck lighting', 'xlights-elementor-widgets' ),
			'heading' => __( 'Make your deck as inviting at night <em>as it is by day.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>Add subtle light to deck steps, railings and walkways so outdoor spaces feel welcoming and easier to enjoy after sunset. X LIGHTS deck lighting is designed to fit into the space and provide a finished look without overpowering it.</p>', 'xlights-elementor-widgets' ),
			'points'  => __( "Modern design with smart technology\nLow-voltage LED lighting\nColor options, schedules and scenes in the app", 'xlights-elementor-widgets' ),
			'bg'      => 'light',
		),
	),
	'steps'     => array(
		'type'     => 'split',
		'label'    => __( 'Deck Step Lighting', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Step lighting', 'xlights-elementor-widgets' ),
			'heading'        => __( 'See every step.', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Small, low-glare lights set into or under each step help you and your guests see where you are going while keeping the light soft and architectural.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'image'          => Defaults::photo( 'floating-steps' ),
			'image_alt'      => __( 'Floating garden steps with light under each tread at night', 'xlights-elementor-widgets' ),
			'scene'          => 'deck',
			'image_position' => 'right',
			'bg'             => 'warm',
		),
	),
	'railings'  => array(
		'type'     => 'split',
		'label'    => __( 'Railing & Walkway Lighting', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Railings & walkways', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Define the edges, <em>frame the view.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Lighting along railings, posts and walkways outlines the shape of your deck and guides people through the space — without shining in anyone’s eyes.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'image'          => Defaults::photo( 'patio-pool-firepit' ),
			'image_alt'      => __( 'Patio at dusk with a fire pit, lit steps, an uplit palm and a pool', 'xlights-elementor-widgets' ),
			'scene'          => 'deck',
			'palette'        => 'cool',
			'image_position' => 'left',
			'bg'             => 'light',
		),
	),
	'usability' => array(
		'type'     => 'cards',
		'label'    => __( 'Outdoor Safety & Usability', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Usability after sunset', 'xlights-elementor-widgets' ),
			'heading' => __( 'Thoughtful lighting makes outdoor spaces <em>easier to use.</em>', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '3',
			'items'   => array(
				array(
					'icon'  => 'stairs',
					'title' => __( 'Visible steps', 'xlights-elementor-widgets' ),
					'text'  => __( 'Light where your feet go helps people move confidently in the evening.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'path',
					'title' => __( 'Clear paths', 'xlights-elementor-widgets' ),
					'text'  => __( 'Walkways and transitions are easier to follow after dark.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'moon',
					'title' => __( 'Longer evenings', 'xlights-elementor-widgets' ),
					'text'  => __( 'A well-lit deck gets used more — long after the sun goes down.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'dark',
		),
	),
	'subtle'    => array(
		'type'     => 'split',
		'label'    => __( 'Subtle Architectural Illumination', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Architectural detail', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Light the details, <em>not the neighborhood.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Good deck lighting is felt more than seen. We place fixtures to highlight materials and edges with soft, controlled light that complements your home.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'image'          => Defaults::photo( 'deck-stairs-dot-lights' ),
			'image_alt'      => __( 'Wood deck at night with warm light under the stair treads and small recessed deck lights', 'xlights-elementor-widgets' ),
			'scene'          => 'deck',
			'image_position' => 'right',
			'bg'             => 'warm',
		),
	),
	'control'   => array(
		'type'     => 'split',
		'label'    => __( 'Compatible Smart Control', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'         => __( 'Smart control', 'xlights-elementor-widgets' ),
			'heading'         => __( 'Control your deck from the X LIGHTS app.', 'xlights-elementor-widgets' ),
			'text'            => __( '<p>Compatible deck lighting can be programmed in the X LIGHTS app, with color options, schedules and custom scenes — and grouped with your soffit and landscape lighting.</p>', 'xlights-elementor-widgets' ),
			'note'            => Defaults::compatibility_note(),
			'secondary_label' => __( 'Explore the app', 'xlights-elementor-widgets' ),
			'secondary_link'  => '{page:app}',
			'visual'          => 'phone',
			'screen'          => 'zones',
			'image_position'  => 'left',
			'bg'              => 'light',
		),
	),
	'products'  => array(
		'type'     => 'cards',
		'label'    => __( 'Product Style Options', 'xlights-elementor-widgets' ),
		'anchor'   => 'xl-deck-products',
		'defaults' => array(
			'eyebrow'     => __( 'Product styles', 'xlights-elementor-widgets' ),
			'heading'     => __( 'Deck lighting options', 'xlights-elementor-widgets' ),
			'text'        => __( 'We will recommend the right combination for your deck during your consultation.', 'xlights-elementor-widgets' ),
			'style'       => 'minimal',
			'columns'     => '3',
			'items'       => array(
				array(
					'icon'  => 'lightbulb',
					'title' => __( 'Deck lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Recessed or surface-mounted lights for deck boards and posts.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'stairs',
					'title' => __( 'Stair lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Low-glare lights for steps and risers.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'layers',
					'title' => __( 'Flex lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Flexible linear lighting for curves and continuous runs.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'ruler',
					'title' => __( 'Edge lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Light along deck edges, railings and transitions.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'string-lights',
					'title' => __( 'String bulb lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Hanging bulbs for a classic bistro feel above the deck.', 'xlights-elementor-widgets' ),
				),
			),
			'note'        => __( 'Product availability and specifications are confirmed in your proposal.', 'xlights-elementor-widgets' ),
			'bg'          => 'warm',
			'editor_note' => __( 'Product categories follow the corporate X LIGHTS site. Add approved product photos (Image field per card) and switch the style to "Image cards" when available.', 'xlights-elementor-widgets' ),
		),
	),
	'projects'  => Defaults::projects( 'deck', __( 'Deck lighting projects', 'xlights-elementor-widgets' ), '', array( 'bg' => 'light' ) ),
	'faq'       => array(
		'type'     => 'faq',
		'label'    => __( 'FAQ', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'FAQ', 'xlights-elementor-widgets' ),
			'heading' => __( 'Deck lighting questions', 'xlights-elementor-widgets' ),
			'items'   => array(
				array(
					'question' => __( 'Can you add lighting to an existing deck?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'In many cases, yes. We assess your deck’s construction and access for wiring during the consultation and recommend options that suit it.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Will deck lights be too bright?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'We design for soft, low-glare light, and compatible products can be dimmed in the X LIGHTS app.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Can deck lighting work with my soffit or landscape lighting?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Compatible X LIGHTS products can be grouped and scheduled together in the app. We will confirm compatibility for your specific products.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'What does deck lighting cost?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'It depends on the size of the deck and the products chosen. We provide a detailed quote after the consultation.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'light',
		),
	),
	'cta'       => Defaults::cta(
		__( 'Ready to light your deck?', 'xlights-elementor-widgets' ),
		__( 'Send us a few photos and we will recommend the right deck lighting for your space.', 'xlights-elementor-widgets' ),
		array(
			'primary_label' => __( 'Get a Deck Lighting Quote', 'xlights-elementor-widgets' ),
			'primary_link'  => '{page:contact}?service=deck#quote',
			'bg'            => 'warm',
		)
	),
);
