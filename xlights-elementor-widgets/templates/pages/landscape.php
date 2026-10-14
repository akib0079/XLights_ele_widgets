<?php
/**
 * Page template: Landscape Lighting.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'     => array(
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
			'image'           => Defaults::photo( 'entry-steps-night' ),
			'image_alt'       => __( 'Entry walkway at night with light under each stair tread and softly uplit olive trees', 'xlights-elementor-widgets' ),
			'scene'           => 'landscape',
			'overlay'         => 45,
			'editor_note'     => __( 'Add a real nighttime landscaping photo from an approved project here.', 'xlights-elementor-widgets' ),
		),
	),
	'intro'    => array(
		'type'     => 'intro',
		'label'    => __( 'Introduction', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'split',
			'eyebrow' => __( 'Landscape lighting', 'xlights-elementor-widgets' ),
			'heading' => __( 'Enjoy the parts of your property <em>that disappear at sunset.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>X LIGHTS landscape lighting brings beauty, ambiance and functionality to every outdoor space. Highlight a favorite tree, softly light a path, or bring depth to planting and outdoor features — engineered for professional performance and designed to work with the rest of your X LIGHTS system.</p>', 'xlights-elementor-widgets' ),
			'points'  => __( "IP67 rated, individually programmable RGBW fixtures\nQuick connect technology for fast, reliable installation\nColor, brightness and schedules in the X LIGHTS app", 'xlights-elementor-widgets' ),
			'bg'      => 'light',
		),
	),
	'explore'  => array(
		'type'     => 'showcase',
		'label'    => __( 'Trees, Garden Beds & Pathways (Feature Explorer)', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Around the garden', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Trees, planting and paths, <em>each lit its own way.</em>', 'xlights-elementor-widgets' ),
			'items'          => array(
				array(
					'title'     => __( 'Give your favorite tree the spotlight', 'xlights-elementor-widgets' ),
					'text'      => __( 'Uplighting a mature tree or a stone wall adds depth and drama to the whole property. Accent lights on columns and entries highlight the architecture you love.', 'xlights-elementor-widgets' ),
					'image'     => Defaults::photo( 'landscape-uplit-trees' ),
					'image_alt' => __( 'A row of tall trees uplit in warm white along a lawn at dusk', 'xlights-elementor-widgets' ),
					'scene'     => 'landscape',
				),
				array(
					'title'     => __( 'Planting that looks beautiful day and night', 'xlights-elementor-widgets' ),
					'text'      => __( 'Soft light across garden beds brings out texture and color in your landscaping, so the investment you made in your yard keeps paying off after dark.', 'xlights-elementor-widgets' ),
					'image'     => Defaults::photo( 'landscape-stone-steps' ),
					'image_alt' => __( 'Stone garden steps and retaining walls with lights under each step at dusk', 'xlights-elementor-widgets' ),
					'scene'     => 'landscape',
				),
				array(
					'title'     => __( 'A welcoming way home', 'xlights-elementor-widgets' ),
					'text'      => __( 'Pathway lights guide guests from the driveway to the door and through the garden, with low, soft light that keeps the focus on the landscape.', 'xlights-elementor-widgets' ),
					'image'     => Defaults::photo( 'landscape-walkway-pergola' ),
					'image_alt' => __( 'Wooden walkway with in-ground lights leading to a pergola, lined with lit ornamental grasses', 'xlights-elementor-widgets' ),
					'scene'     => 'landscape',
				),
			),
			'media_position' => 'left',
			'primary_label'  => __( 'Get a landscape lighting quote', 'xlights-elementor-widgets' ),
			'primary_link'   => '{quote_url}',
			'bg'             => 'dark',
		),
	),
	'features' => array(
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
	'benefits' => array(
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
	'control'  => array(
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
	'products' => array(
		'type'     => 'cards',
		'label'    => __( 'Landscape Lighting Collection', 'xlights-elementor-widgets' ),
		'anchor'   => 'xl-landscape-products',
		'defaults' => array(
			'eyebrow'     => __( 'Landscape lighting collection', 'xlights-elementor-widgets' ),
			'heading'     => __( 'The right fixture <em>for every feature.</em>', 'xlights-elementor-widgets' ),
			'text'        => __( 'We will recommend the right combination for your yard during your consultation.', 'xlights-elementor-widgets' ),
			'style'       => 'image',
			'columns'     => '3',
			'items'       => array(
				array(
					'icon'  => 'spotlight',
					'title' => __( 'Adjustable spot & accent lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Aimable spotlights for trees, walls, columns and other features.', 'xlights-elementor-widgets' ),
					'image' => Defaults::photo( 'landscape-spot-green' ),
				),
				array(
					'icon'  => 'target',
					'title' => __( 'Recessed spotlight', 'xlights-elementor-widgets' ),
					'text'  => __( 'In-ground lights set flush in rock, mulch or lawn for clean uplighting.', 'xlights-elementor-widgets' ),
					'image' => Defaults::photo( 'landscape-inground-mulch' ),
				),
				array(
					'icon'  => 'lamp',
					'title' => __( 'Architectural path lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Low lights that guide the way along walks, steps and garden beds.', 'xlights-elementor-widgets' ),
					'image' => Defaults::photo( 'landscape-path-pagoda' ),
				),
				array(
					'icon'  => 'lamp',
					'title' => __( 'Contemporary area lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Clean, modern lights that wash a wider area of path or planting.', 'xlights-elementor-widgets' ),
					'image' => Defaults::photo( 'path-lights-walkway' ),
				),
				array(
					'icon'  => 'lightbulb',
					'title' => __( 'Modern bollard lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Sleek posts that light driveways, entries and walkways.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'home',
					'title' => __( 'Wall lights', 'xlights-elementor-widgets' ),
					'text'  => __( 'Wall-mounted lights for fences, walls and outdoor living areas.', 'xlights-elementor-widgets' ),
					'image' => Defaults::photo( 'landscape-wall-light' ),
				),
			),
			'note'        => __( 'Product availability and specifications are confirmed in your proposal.', 'xlights-elementor-widgets' ),
			'bg'          => 'warm',
			'editor_note' => __( 'Collection from the official X LIGHTS landscape lighting sheet. Add a photo of a bollard light to the "Modern bollard lights" card when you have one; replace any photo with your own installation photos.', 'xlights-elementor-widgets' ),
		),
	),
	'fixtures' => Defaults::fixture_features( 'dark' ),
	'projects' => array_merge(
		Defaults::projects( 'landscape', __( 'Landscape lighting projects', 'xlights-elementor-widgets' ), '', array( 'bg' => 'light' ) ),
		array( 'anchor' => 'xl-landscape-projects' )
	),
	'faq'      => array(
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
	'cta'      => Defaults::cta(
		__( 'Bring your landscape to life after dark.', 'xlights-elementor-widgets' ),
		__( 'Share a few photos of your yard and we will recommend a lighting plan.', 'xlights-elementor-widgets' ),
		array(
			'image'         => Defaults::photo( 'landscape-uplit-trees' ),
			'primary_label' => __( 'Get a Landscape Lighting Quote', 'xlights-elementor-widgets' ),
			'primary_link'  => '{page:contact}?service=landscape#quote',
			'bg'            => 'warm',
		)
	),
);
