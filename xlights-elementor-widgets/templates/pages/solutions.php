<?php
/**
 * Page template: Lighting Solutions.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'      => array(
		'type'     => 'hero',
		'label'    => __( 'Lighting Solutions Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'media',
			'height'          => 'tall',
			'eyebrow'         => __( 'Lighting Solutions', 'xlights-elementor-widgets' ),
			'heading'         => __( 'Outdoor lighting for every part <em>of your property.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( 'Permanent soffit, hanging bulb bistro, deck and landscape lighting — designed together and controlled from one app.', 'xlights-elementor-widgets' ),
			'primary_label'   => '{cta}',
			'primary_link'    => '{quote_url}',
			'secondary_label' => __( 'View projects', 'xlights-elementor-widgets' ),
			'secondary_link'  => '{page:projects}',
			'visual'          => 'image',
			'image'           => Defaults::photo( 'pool-patio-dusk' ),
			'image_alt'       => __( 'Pool patio at dusk with a lit pergola, dining area and landscape lighting', 'xlights-elementor-widgets' ),
			'scene'           => 'roofline',
			'overlay'         => 60,
		),
	),
	'overview'  => array(
		'type'     => 'cards',
		'label'    => __( 'System Overview', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'The X LIGHTS system', 'xlights-elementor-widgets' ),
			'heading' => __( 'Four ways to light your home. <em>One connected experience.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( 'Start with one category or plan them together. Compatible lighting can be grouped in the X LIGHTS app.', 'xlights-elementor-widgets' ),
			'style'   => 'stack',
			'columns' => '4',
			'items'   => array(
				array(
					'title'      => __( 'Permanent Soffit Lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Our signature roofline system: discreet by day, warm white every night, festive whenever you like.', 'xlights-elementor-widgets' ),
					'image'      => Defaults::img( 'home-two-story-night.jpg' ),
					'scene'      => 'roofline',
					'link_label' => __( 'Explore', 'xlights-elementor-widgets' ),
					'link'       => '#xl-solutions-soffit',
				),
				array(
					'title'      => __( 'Hanging Bulb Bistro Lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'A warm, welcoming glow for patios and outdoor dining.', 'xlights-elementor-widgets' ),
					'image'      => Defaults::photo( 'bistro-string-backyard' ),
					'scene'      => 'patio',
					'link_label' => __( 'Explore', 'xlights-elementor-widgets' ),
					'link'       => '#xl-solutions-bistro',
				),
				array(
					'title'      => __( 'Deck Lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Steps, railings and walkways that are easier to enjoy after dark.', 'xlights-elementor-widgets' ),
					'image'      => Defaults::photo( 'floating-steps' ),
					'scene'      => 'deck',
					'link_label' => __( 'Explore', 'xlights-elementor-widgets' ),
					'link'       => '#xl-solutions-deck',
				),
				array(
					'title'      => __( 'Landscape Lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Trees, gardens and paths that shine after sunset.', 'xlights-elementor-widgets' ),
					'image'      => Defaults::photo( 'garden-steps-path-lights' ),
					'scene'      => 'landscape',
					'link_label' => __( 'Explore', 'xlights-elementor-widgets' ),
					'link'       => '#xl-solutions-landscape',
				),
			),
			'bg'      => 'light',
		),
	),
	'soffit'    => array(
		'type'     => 'split',
		'label'    => __( 'Permanent Soffit Lighting', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'cinematic',
			'eyebrow'         => __( 'Permanent soffit lighting', 'xlights-elementor-widgets' ),
			'heading'         => __( 'Beautiful every night. <em>Ready for every celebration.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( '<p>A low-profile light and track system mounted along the roofline. Choose warm white to highlight your home’s architecture every evening, then switch to color and patterns for holidays and special occasions.</p>', 'xlights-elementor-widgets' ),
			'points'          => __( "Low-profile, color-matched track\nWarm white and full color\nApp scheduling and saved favorites", 'xlights-elementor-widgets' ),
			'primary_label'   => __( 'Permanent Soffit Lighting', 'xlights-elementor-widgets' ),
			'primary_link'    => '{page:soffit}',
			'secondary_label' => __( 'Request a soffit quote', 'xlights-elementor-widgets' ),
			'secondary_link'  => '{page:contact}?service=soffit#quote',
			'visual'          => 'image',
			'image'           => Defaults::photo( 'roofline-holiday-color-modern' ),
			'image_alt'       => __( 'Modern single-story home with multicolor holiday lighting along the roofline at dusk', 'xlights-elementor-widgets' ),
			'scene'           => 'roofline',
			'image_position'  => 'right',
			'bg'              => 'light',
		),
	),
	'connected' => array(
		'type'     => 'connected',
		'label'    => __( 'One Connected Outdoor Lighting System', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Better together', 'xlights-elementor-widgets' ),
			'heading' => __( 'One connected outdoor lighting system.', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>When your lighting is planned together, it can work together: one schedule for the whole property, coordinated holiday themes, and a single app to manage compatible products.</p>', 'xlights-elementor-widgets' ),
			'items'   => Defaults::connected_items(),
			'note'    => Defaults::compatibility_note(),
			'bg'      => 'dark',
		),
	),
	'app_gallery' => array(
		'type'     => 'app_showcase',
		'label'    => __( 'App Screens Gallery', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'     => 'gallery',
			'eyebrow'    => __( 'The X LIGHTS app', 'xlights-elementor-widgets' ),
			'heading'    => __( 'Control it all from <em>your phone.</em>', 'xlights-elementor-widgets' ),
			'text'       => __( 'Scroll through the real app: pick colors and brightness, choose patterns, set timers, dance to music, manage every controller and share control with your family.', 'xlights-elementor-widgets' ),
			'items'      => Defaults::app_tabs(),
			'show_store' => 'yes',
			'link_label' => __( 'Explore the app', 'xlights-elementor-widgets' ),
			'link'       => '{page:app}',
			'bg'         => 'warm',
		),
	),
	'benefits'  => array(
		'type'     => 'cards',
		'label'    => __( 'Benefits & Differentiators', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Why X LIGHTS', 'xlights-elementor-widgets' ),
			'heading' => __( 'What sets our lighting apart.', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '3',
			'items'   => Defaults::why_items(),
			'bg'      => 'light',
		),
	),
	'financing' => Defaults::financing( 'warm' ),
	'projects'  => Defaults::projects( 'featured', __( 'Recent installations', 'xlights-elementor-widgets' ) ),
	'cta'       => Defaults::cta(
		__( 'Not sure where to start?', 'xlights-elementor-widgets' ),
		__( 'Tell us about your property and we will recommend the right mix of lighting for it.', 'xlights-elementor-widgets' ),
		array( 'image' => Defaults::photo( 'pool-patio-dusk' ), 'bg' => 'warm' )
	),
);
