<?php
/**
 * Page template: About Us.
 *
 * The X LIGHTS brand story (founded in Calgary by Laura Ericson) is kept in
 * its own clearly labeled section and never presented as the local dealer's
 * history. Local history, team and credentials are separate and editable.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'       => array(
		'type'     => 'hero',
		'label'    => __( 'About Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'centered',
			'eyebrow'         => __( 'About {dealer}', 'xlights-elementor-widgets' ),
			'heading'         => __( 'Outdoor lighting designed to look as good <em>by day as it does at night.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( 'We are the local X LIGHTS team for {area}, bringing permanent, connected outdoor lighting to homes and businesses in our community.', 'xlights-elementor-widgets' ),
			'primary_label'   => '{cta}',
			'primary_link'    => '{quote_url}',
			'secondary_label' => __( 'View our projects', 'xlights-elementor-widgets' ),
			'secondary_link'  => '{page:projects}',
			'show_phone'      => '',
			'bg'              => 'dark',
		),
	),
	'meet'       => array(
		'type'     => 'intro',
		'label'    => __( 'Meet X LIGHTS', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'centered',
			'eyebrow' => __( 'Meet X LIGHTS', 'xlights-elementor-widgets' ),
			'heading' => __( 'Connected outdoor lighting that makes homes and businesses look better — and work better — after dark.', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>X LIGHTS brings together permanent soffit lighting, hanging bulb bistro lighting, deck lighting and landscape lighting in one connected system. Warm white for every night, color for every celebration, and control from a single app.</p>', 'xlights-elementor-widgets' ),
			'bg'      => 'light',
		),
	),
	'local'      => array(
		'type'     => 'split',
		'label'    => __( 'Local Operation', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Your local {market} X LIGHTS team', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Based in {market}, focused on <em>{area}.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>{dealer} is the local X LIGHTS team serving {area}. We handle every project from the first consultation through design and installation, and we are nearby when you have questions afterwards.</p><p>Because we work locally, we understand the architecture, neighborhoods and outdoor lifestyle of the communities we serve.</p>', 'xlights-elementor-widgets' ),
			'points'         => __( "Consultation, design and installation by one local team\nLighting plans tailored to your property\nSupport after installation", 'xlights-elementor-widgets' ),
			'editor_note'    => __( 'Add the dealership’s own story, year established and local highlights here once confirmed by the dealer.', 'xlights-elementor-widgets' ),
			'primary_label'  => __( 'Schedule a consultation', 'xlights-elementor-widgets' ),
			'primary_link'   => '{quote_url}',
			'visual'         => 'image',
			'image'          => Defaults::img( 'home-single-story-dusk.jpg' ),
			'image_alt'      => __( 'Family standing in front of their home with warm white permanent roofline lighting', 'xlights-elementor-widgets' ),
			'scene'          => 'roofline',
			'image_position' => 'right',
			'bg'             => 'warm',
		),
	),
	'brand'      => array(
		'type'     => 'split',
		'label'    => __( 'About the X LIGHTS Brand (Company Story)', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'About the X LIGHTS brand', 'xlights-elementor-widgets' ),
			'heading'        => __( 'A brand that started with <em>a designer’s eye.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>The X LIGHTS brand was founded in Calgary, Canada, by Laura Ericson. Her background in interior design shaped the way the company approached permanent outdoor lighting: it should look as considered during the day as it does when it is lit at night.</p><p>That idea led to a low-profile light and track system designed to blend into the architecture of a home, paired with smart controls that make it useful every night of the year.</p>', 'xlights-elementor-widgets' ),
			'note'           => __( 'This is the story of the X LIGHTS brand. {dealer} is the local X LIGHTS team serving {area}.', 'xlights-elementor-widgets' ),
			'visual'         => 'illustration',
			'scene'          => 'daytime',
			'image_position' => 'left',
			'badge'          => __( 'Brand story', 'xlights-elementor-widgets' ),
			'bg'             => 'light',
		),
	),
	'philosophy' => array(
		'type'     => 'intro',
		'label'    => __( 'Design-First Philosophy', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'split',
			'eyebrow' => __( 'Design-first philosophy', 'xlights-elementor-widgets' ),
			'heading' => __( 'Lighting should complement architecture, <em>not compete with it.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>Good outdoor lighting starts with the building and the landscape. We look at rooflines, entries, trees, paths and gathering spaces first, then decide where light will add the most — and where restraint looks better.</p><p>The goal is a home that feels finished and welcoming at night, with fixtures that stay discreet during the day.</p>', 'xlights-elementor-widgets' ),
			'points'  => __( "Start with the architecture\nLight what matters, leave the rest\nKeep fixtures discreet in daylight", 'xlights-elementor-widgets' ),
			'bg'      => 'warm',
		),
	),
	'why_permanent' => array(
		'type'     => 'split',
		'label'    => __( 'Why Permanent Lighting Was Developed', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Why permanent lighting', 'xlights-elementor-widgets' ),
			'heading'        => __( 'No more ladders, tangled strands or <em>bulky daytime hardware.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Seasonal lights mean climbing ladders twice a year, untangling strands and storing boxes. Many permanent alternatives solve that problem but leave visible hardware on the house all year.</p><p>X LIGHTS permanent lighting was developed to remove the seasonal hassle while keeping the roofline clean: install once, enjoy warm white every evening, and switch to color for the holidays from your phone.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'image'          => Defaults::img( 'soffit-track-daytime.jpg' ),
			'image_alt'      => __( 'Close-up of the low-profile X LIGHTS track and lights mounted along a fascia board in daylight', 'xlights-elementor-widgets' ),
			'scene'          => 'daytime',
			'image_position' => 'right',
			'caption'        => __( 'By day: the low-profile track sits close to the fascia.', 'xlights-elementor-widgets' ),
			'bg'             => 'light',
		),
	),
	'technology' => array(
		'type'     => 'cards',
		'label'    => __( 'Product & Technology Philosophy', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Product & technology', 'xlights-elementor-widgets' ),
			'heading' => __( 'Thoughtful hardware. <em>Simple control.</em>', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '4',
			'items'   => array(
				array(
					'icon'  => 'eye',
					'title' => __( 'Low-profile by design', 'xlights-elementor-widgets' ),
					'text'  => __( 'Slim lights and color-matched track intended to blend into the roofline.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'lightbulb',
					'title' => __( 'Bright, low-voltage LEDs', 'xlights-elementor-widgets' ),
					'text'  => __( 'Efficient LED lighting for everyday warm white and vivid color.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'smartphone',
					'title' => __( 'Smart control', 'xlights-elementor-widgets' ),
					'text'  => __( 'Colors, brightness, scenes and schedules from the X LIGHTS app.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'hub',
					'title' => __( 'One connected system', 'xlights-elementor-widgets' ),
					'text'  => __( 'Compatible soffit, deck, landscape and bistro lighting managed together.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'dark',
		),
	),
	'team'       => array(
		'type'     => 'team',
		'label'    => __( 'Local Team Photos & Bios', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Your local team', 'xlights-elementor-widgets' ),
			'heading' => __( 'The people behind your lighting.', 'xlights-elementor-widgets' ),
			'text'    => __( 'Meet the {dealer} team who will design and install your system.', 'xlights-elementor-widgets' ),
			'members' => array(),
			'bg'      => 'light',
		),
	),
	'reasons'    => array(
		'type'     => 'cards',
		'label'    => __( 'Reasons Customers Choose X LIGHTS', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Why customers choose us', 'xlights-elementor-widgets' ),
			'heading' => __( 'What working with {dealer} looks like.', 'xlights-elementor-widgets' ),
			'style'   => 'steps',
			'columns' => '4',
			'items'   => array(
				array(
					'title' => __( 'A real consultation', 'xlights-elementor-widgets' ),
					'text'  => __( 'We learn how you use your home and outdoor spaces before recommending anything.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'A plan for your property', 'xlights-elementor-widgets' ),
					'text'  => __( 'Placement, product choices and zones designed around your architecture.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Clean installation', 'xlights-elementor-widgets' ),
					'text'  => __( 'Professional installation focused on a tidy, finished look.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Help when you need it', 'xlights-elementor-widgets' ),
					'text'  => __( 'App setup guidance and a local team to call with questions.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'warm',
		),
	),
	'trust'      => array(
		'type'     => 'trust',
		'label'    => __( 'Licensed & Insured Trust Section', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'      => __( 'Local & accountable', 'xlights-elementor-widgets' ),
			'heading'      => __( 'A local team you can reach.', 'xlights-elementor-widgets' ),
			'text'         => __( 'When you work with {dealer}, you work with people who live and work in {market}.', 'xlights-elementor-widgets' ),
			'show_license' => 'yes',
			'items'        => array(
				array(
					'icon'  => 'map-pin',
					'title' => __( 'Locally operated', 'xlights-elementor-widgets' ),
					'text'  => __( 'Serving {area}.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'shield-check',
					'title' => __( 'Licensed and insured', 'xlights-elementor-widgets' ),
					'text'  => __( 'Credentials available on request.', 'xlights-elementor-widgets' ),
					'gate'  => 'credentials',
				),
				array(
					'icon'  => 'phone',
					'title' => __( 'Easy to reach', 'xlights-elementor-widgets' ),
					'text'  => __( 'Call {phone}.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'           => 'dark',
		),
	),
	'financing'  => Defaults::financing( 'light' ),
	'cta'        => Defaults::cta(
		__( 'Let’s design your outdoor lighting.', 'xlights-elementor-widgets' ),
		__( 'Tell us about your home or business and we will set up a consultation.', 'xlights-elementor-widgets' ),
		array( 'bg' => 'warm' )
	),
);
