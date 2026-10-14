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
			'text'    => __( '<p>Transform decks, railings, stairs, patios, pergolas, outdoor kitchens and entertainment spaces with X LIGHTS deck lighting. Subtle light on steps, edges and walkways makes outdoor spaces welcoming and easier to enjoy after sunset — without overpowering them.</p>', 'xlights-elementor-widgets' ),
			'points'  => __( "Individually programmable RGBW fixtures\nLow-voltage 12V–24V system, IP67 weatherproof\nControlled from the X LIGHTS app", 'xlights-elementor-widgets' ),
			'bg'      => 'light',
		),
	),
	'details'   => array(
		'type'     => 'showcase',
		'label'    => __( 'Steps, Edges & Details (Feature Explorer)', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Where the light goes', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Steps, edges <em>and the details in between.</em>', 'xlights-elementor-widgets' ),
			'items'          => array(
				array(
					'title'     => __( 'See every step', 'xlights-elementor-widgets' ),
					'text'      => __( 'Small, low-glare lights set into or under each step help you and your guests see where you are going while keeping the light soft and architectural.', 'xlights-elementor-widgets' ),
					'image'     => Defaults::photo( 'deck-stairs-step-lights' ),
					'image_alt' => __( 'Deck stairs at dusk with a step light under each tread', 'xlights-elementor-widgets' ),
					'scene'     => 'deck',
				),
				array(
					'title'     => __( 'Define the edges', 'xlights-elementor-widgets' ),
					'text'      => __( 'Lighting along railings, posts and walkways outlines the shape of your deck and guides people through the space — without shining in anyone’s eyes.', 'xlights-elementor-widgets' ),
					'image'     => Defaults::photo( 'deck-flex-pergola' ),
					'image_alt' => __( 'Covered deck with flex lighting around the pergola and a fire table', 'xlights-elementor-widgets' ),
					'scene'     => 'deck',
				),
				array(
					'title'     => __( 'Light the details, not the neighborhood', 'xlights-elementor-widgets' ),
					'text'      => __( 'Good deck lighting is felt more than seen. We place fixtures to highlight materials and edges with soft, controlled light that complements your home.', 'xlights-elementor-widgets' ),
					'image'     => Defaults::photo( 'deck-dot-lights-boards' ),
					'image_alt' => __( 'Small round deck lights set flush into composite deck boards', 'xlights-elementor-widgets' ),
					'scene'     => 'deck',
				),
			),
			'media_position' => 'right',
			'primary_label'  => __( 'Get a deck lighting quote', 'xlights-elementor-widgets' ),
			'primary_link'   => '{quote_url}',
			'bg'             => 'warm',
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
			'visual'          => 'image',
			'image'           => Defaults::photo( 'deck-steps-app' ),
			'image_alt'       => __( 'Holding the X LIGHTS app in front of deck stairs lit with step lights', 'xlights-elementor-widgets' ),
			'screen'          => 'zones',
			'image_position'  => 'left',
			'bg'              => 'light',
		),
	),
	'products'  => array(
		'type'     => 'cards',
		'label'    => __( 'Deck Lighting Collection', 'xlights-elementor-widgets' ),
		'anchor'   => 'xl-deck-products',
		'defaults' => array(
			'eyebrow'     => __( 'Deck lighting collection', 'xlights-elementor-widgets' ),
			'heading'     => __( 'Fixtures for every part <em>of your deck.</em>', 'xlights-elementor-widgets' ),
			'text'        => __( 'We will recommend the right combination for your deck during your consultation.', 'xlights-elementor-widgets' ),
			'style'       => 'image',
			'columns'     => '3',
			'items'       => array(
				array(
					'icon'  => 'stairs',
					'title' => __( 'Step lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Compact lights mounted under each tread or on the riser, so every step is easy to see.', 'xlights-elementor-widgets' ),
					'image' => Defaults::photo( 'deck-stairs-step-lights' ),
				),
				array(
					'icon'  => 'lightbulb',
					'title' => __( 'Deck lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Round lights set flush into deck boards, stairs and walkways.', 'xlights-elementor-widgets' ),
					'image' => Defaults::photo( 'deck-stairs-dot-lights' ),
				),
				array(
					'icon'  => 'palette',
					'title' => __( 'RGB deck lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Flush deck lights in full color — warm white every day, any color when you want it.', 'xlights-elementor-widgets' ),
					'image' => Defaults::photo( 'deck-dot-light' ),
				),
				array(
					'icon'  => 'layers',
					'title' => __( 'Flex deck lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Flexible linear lighting for railings, fascia, pergolas and outdoor kitchens — cut to size.', 'xlights-elementor-widgets' ),
					'image' => Defaults::photo( 'deck-flex-detail' ),
				),
				array(
					'icon'  => 'string-lights',
					'title' => __( 'String lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Hanging bulbs for a classic bistro feel over the deck — and they can dance to the music.', 'xlights-elementor-widgets' ),
					'image' => Defaults::photo( 'deck-string-lights' ),
				),
			),
			'note'        => __( 'Product availability and specifications are confirmed in your proposal.', 'xlights-elementor-widgets' ),
			'bg'          => 'warm',
			'editor_note' => __( 'Collection from the official X LIGHTS deck lighting sheet. Replace any photo with your own installation photos.', 'xlights-elementor-widgets' ),
		),
	),
	'fixtures'  => Defaults::fixture_features( 'dark' ),
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
			'image'         => Defaults::photo( 'deck-stairs-step-lights' ),
			'primary_label' => __( 'Get a Deck Lighting Quote', 'xlights-elementor-widgets' ),
			'primary_link'  => '{page:contact}?service=deck#quote',
			'bg'            => 'warm',
		)
	),
);
