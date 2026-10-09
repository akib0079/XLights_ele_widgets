<?php
/**
 * Page template: Landscape Lighting.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'      => array(
		'type'     => 'hero',
		'label'    => __( 'Landscape Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'media',
			'height'          => 'tall',
			'eyebrow'         => __( 'Landscape Lighting', 'xlights-elementor-widgets' ),
			'eyebrow_in_h1'   => 'yes',
			'heading'         => __( 'Let your home and garden <em>shine after dark.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( 'Tree, garden and pathway lighting for homes in {area}, with color and brightness in the X LIGHTS app.', 'xlights-elementor-widgets' ),
			'primary_label'   => __( 'Get a Landscape Lighting Quote', 'xlights-elementor-widgets' ),
			'primary_link'    => '{page:contact}?service=landscape#quote',
			'secondary_label' => __( 'View projects', 'xlights-elementor-widgets' ),
			'secondary_link'  => '#xl-landscape-projects',
			'visual'          => 'image',
			'scene'           => 'landscape',
			'overlay'         => 45,
			'editor_note'     => __( 'Add a real nighttime landscaping photo from an approved project here.', 'xlights-elementor-widgets' ),
		),
	),
	'intro'     => array(
		'type'     => 'intro',
		'label'    => __( 'Introduction', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'split',
			'eyebrow' => __( 'Landscape lighting', 'xlights-elementor-widgets' ),
			'heading' => __( 'Enjoy the parts of your property <em>that disappear at sunset.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>Highlight a favorite tree, softly illuminate a path, or bring depth to planting and outdoor features. X LIGHTS landscape lighting helps homeowners enjoy parts of their property that disappear when the sun goes down.</p>', 'xlights-elementor-widgets' ),
			'points'  => __( "Architecture, garden beds, walkways and trees\nWeather-resistant, energy-efficient LED lighting\nColor, brightness and scheduling in the app", 'xlights-elementor-widgets' ),
			'bg'      => 'light',
		),
	),
	'trees'     => array(
		'type'     => 'split',
		'label'    => __( 'Tree & Architectural Accent Lighting', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Trees & architecture', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Give your favorite tree <em>the spotlight.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Uplighting a mature tree or a stone wall adds depth and drama to the whole property. Accent lights on columns and entries highlight the architecture you love.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'scene'          => 'landscape',
			'image_position' => 'right',
			'bg'             => 'dark',
		),
	),
	'garden'    => array(
		'type'     => 'split',
		'label'    => __( 'Garden-Bed Illumination', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Garden beds', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Planting that looks beautiful <em>day and night.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Soft light across garden beds brings out texture and color in your landscaping, so the investment you made in your yard keeps paying off after dark.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'scene'          => 'landscape',
			'palette'        => 'warm',
			'image_position' => 'left',
			'bg'             => 'warm',
		),
	),
	'paths'     => array(
		'type'     => 'split',
		'label'    => __( 'Pathway Lighting', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Pathways', 'xlights-elementor-widgets' ),
			'heading'        => __( 'A welcoming way home.', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Pathway lights guide guests from the driveway to the door and through the garden, with low, soft light that keeps the focus on the landscape.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'scene'          => 'landscape',
			'image_position' => 'right',
			'bg'             => 'light',
		),
	),
	'features'  => array(
		'type'     => 'cards',
		'label'    => __( 'Outdoor Feature Highlights', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Feature highlights', 'xlights-elementor-widgets' ),
			'heading' => __( 'Make the most of your outdoor features.', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '4',
			'items'   => array(
				array(
					'icon'  => 'tree',
					'title' => __( 'Trees & palms', 'xlights-elementor-widgets' ),
					'text'  => __( 'Uplighting that adds height and drama.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'water',
					'title' => __( 'Water features', 'xlights-elementor-widgets' ),
					'text'  => __( 'Fountains and pools that glow after sunset.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'fence',
					'title' => __( 'Walls & fences', 'xlights-elementor-widgets' ),
					'text'  => __( 'Texture and depth along the property line.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'home',
					'title' => __( 'Entries & facades', 'xlights-elementor-widgets' ),
					'text'  => __( 'A warm, welcoming first impression.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'warm',
		),
	),
	'benefits'  => array(
		'type'     => 'cards',
		'label'    => __( 'Lighting Design Benefits', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Designed, not scattered', 'xlights-elementor-widgets' ),
			'heading' => __( 'Why a lighting design matters.', 'xlights-elementor-widgets' ),
			'style'   => 'steps',
			'columns' => '3',
			'items'   => array(
				array(
					'title' => __( 'Balanced light', 'xlights-elementor-widgets' ),
					'text'  => __( 'Layers of light create depth instead of bright spots and dark gaps.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Hidden fixtures', 'xlights-elementor-widgets' ),
					'text'  => __( 'Fixtures are placed to show off the landscape, not the hardware.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Room to grow', 'xlights-elementor-widgets' ),
					'text'  => __( 'Plans account for growing plants and future additions.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'light',
		),
	),
	'control'   => array(
		'type'     => 'split',
		'label'    => __( 'Smart Color & Brightness Control', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'         => __( 'Smart control', 'xlights-elementor-widgets' ),
			'heading'         => __( 'Color, brightness and schedules <em>in one app.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( '<p>Compatible landscape lighting can be adjusted in the X LIGHTS app — set a warm white for everyday, add color for the holidays and schedule it all to turn on automatically.</p>', 'xlights-elementor-widgets' ),
			'note'            => Defaults::compatibility_note(),
			'secondary_label' => __( 'Explore the app', 'xlights-elementor-widgets' ),
			'secondary_link'  => '{page:app}',
			'visual'          => 'phone',
			'screen'          => 'color',
			'image_position'  => 'left',
			'bg'              => 'dark',
		),
	),
	'products'  => array(
		'type'     => 'cards',
		'label'    => __( 'Product Options', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'     => __( 'Product options', 'xlights-elementor-widgets' ),
			'heading'     => __( 'Landscape lighting options', 'xlights-elementor-widgets' ),
			'style'       => 'minimal',
			'columns'     => '3',
			'items'       => array(
				array(
					'icon'  => 'target',
					'title' => __( 'Recessed / in-ground lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Flush-mounted lights for uplighting trees and walls.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'spotlight',
					'title' => __( 'Pointer lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Adjustable spot and accent lights for specific features.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'lamp',
					'title' => __( 'Pathway lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Low lights that guide the way along walks and beds.', 'xlights-elementor-widgets' ),
				),
			),
			'note'        => __( 'Product availability and specifications are confirmed in your proposal.', 'xlights-elementor-widgets' ),
			'bg'          => 'warm',
			'editor_note' => __( 'Add approved product photos to each card and switch the style to "Image cards" when available.', 'xlights-elementor-widgets' ),
		),
	),
	'projects'  => array_merge(
		Defaults::projects( 'landscape', __( 'Landscape lighting projects', 'xlights-elementor-widgets' ), '', array( 'bg' => 'light' ) ),
		array( 'anchor' => 'xl-landscape-projects' )
	),
	'faq'       => array(
		'type'     => 'faq',
		'label'    => __( 'FAQ', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'FAQ', 'xlights-elementor-widgets' ),
			'heading' => __( 'Landscape lighting questions', 'xlights-elementor-widgets' ),
			'items'   => array(
				array(
					'question' => __( 'Can landscape lights change color?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Compatible X LIGHTS landscape products offer color, brightness and scheduling in the app. We will confirm the capabilities of the products in your plan.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Will the fixtures be visible during the day?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'We place fixtures to keep them as discreet as possible, often tucked into planting or flush with the ground.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Can you light an existing landscape?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Yes. We design around your current trees, beds and paths, and plan for future changes where possible.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Do landscape lights work with my other X LIGHTS lighting?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Compatible products can be grouped with your soffit and deck lighting in the X LIGHTS app.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'light',
		),
	),
	'cta'       => Defaults::cta(
		__( 'Bring your landscape to life after dark.', 'xlights-elementor-widgets' ),
		__( 'Share a few photos of your yard and we will recommend a lighting plan.', 'xlights-elementor-widgets' ),
		array(
			'primary_label' => __( 'Get a Landscape Lighting Quote', 'xlights-elementor-widgets' ),
			'primary_link'  => '{page:contact}?service=landscape#quote',
			'bg'            => 'warm',
		)
	),
);
