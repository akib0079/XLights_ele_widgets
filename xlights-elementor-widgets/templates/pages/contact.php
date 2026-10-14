<?php
/**
 * Page template: Contact Us / Get a Quote.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'         => array(
		'type'     => 'hero',
		'label'    => __( 'Contact Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'centered',
			'eyebrow'         => __( 'Contact Us / Get a Quote', 'xlights-elementor-widgets' ),
			'heading'         => __( 'Let’s light up <em>your property.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( 'Request a quote or schedule a consultation with {dealer}.', 'xlights-elementor-widgets' ),
			'primary_label'   => __( 'Start your request', 'xlights-elementor-widgets' ),
			'primary_link'    => '#quote',
			'secondary_label' => '',
			'show_phone'      => 'yes',
			'bg'              => 'dark',
		),
	),
	'intro'        => array(
		'type'     => 'intro',
		'label'    => __( 'Consultation Introduction', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'centered',
			'heading' => __( 'Schedule a consultation.', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>Tell us a little about your home or business and the lighting you are interested in. We will follow up to answer questions and arrange a consultation.</p>', 'xlights-elementor-widgets' ),
			'spacing' => 'compact',
			'bg'      => 'light',
		),
	),
	'form'         => array(
		'type'     => 'quote_form',
		'label'    => __( 'Quote Request Form', 'xlights-elementor-widgets' ),
		'anchor'   => 'quote',
		'defaults' => array(
			'eyebrow'       => __( 'Get a quote', 'xlights-elementor-widgets' ),
			'heading'       => __( 'Request your quote', 'xlights-elementor-widgets' ),
			'text'          => '',
			'variant'       => 'general',
			'submit_label'  => __( 'Request My Quote', 'xlights-elementor-widgets' ),
			'aside_image'   => Defaults::img( 'home-single-story-dusk.jpg' ),
			'aside_heading' => __( 'Why we ask for your address and photos', 'xlights-elementor-widgets' ),
			'aside_text'    => __( 'A few details up front help us prepare a more useful consultation.', 'xlights-elementor-widgets' ),
			'aside_points'  => __( "Your address lets us look at the property before we meet\nPhotos of your roofline, patio or yard show us what you want to light\nWe can arrive with relevant ideas and better estimates", 'xlights-elementor-widgets' ),
			'show_contact'  => 'yes',
			'bg'            => 'warm',
		),
	),
	'contact_info' => array(
		'type'     => 'contact_info',
		'label'    => __( 'Phone & Email', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Reach us directly', 'xlights-elementor-widgets' ),
			'heading' => __( 'Prefer to talk?', 'xlights-elementor-widgets' ),
			'bg'      => 'light',
		),
	),
	'map'          => array(
		'type'     => 'service_area',
		'label'    => __( 'Local Service Area Map', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Service area', 'xlights-elementor-widgets' ),
			'heading' => __( 'Where we work', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>{dealer} serves homes and businesses in {area}.</p>', 'xlights-elementor-widgets' ),
			'bg'      => 'warm',
		),
	),
	'why_photos'   => array(
		'type'     => 'cards',
		'label'    => __( 'Why Provide Photos & Address', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Helpful details', 'xlights-elementor-widgets' ),
			'heading' => __( 'What helps us prepare', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '3',
			'items'   => array(
				array(
					'icon'  => 'camera',
					'title' => __( 'Photos of the space', 'xlights-elementor-widgets' ),
					'text'  => __( 'A few phone photos of your roofline, patio, deck or yard — day or night.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'map-pin',
					'title' => __( 'Property address', 'xlights-elementor-widgets' ),
					'text'  => __( 'Helps us understand the property and plan the visit. We use it only to respond to your request.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'pencil',
					'title' => __( 'Your ideas', 'xlights-elementor-widgets' ),
					'text'  => __( 'Holidays you love, how you entertain, areas that feel too dark — anything helps.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'light',
		),
	),
	'financing'    => Defaults::financing( 'warm' ),
	'trust'        => array(
		'type'     => 'trust',
		'label'    => __( 'Local Dealer Trust Statement', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'      => __( 'Local & accountable', 'xlights-elementor-widgets' ),
			'heading'      => __( 'Work with your local X LIGHTS team.', 'xlights-elementor-widgets' ),
			'text'         => __( '{dealer} handles your project from consultation to installation.', 'xlights-elementor-widgets' ),
			'show_license' => 'yes',
			'items'        => array(),
			'bg'           => 'dark',
		),
	),
	'cta'          => Defaults::cta(
		__( 'Questions before you request a quote?', 'xlights-elementor-widgets' ),
		__( 'Call us — we are happy to talk through ideas for your home or business.', 'xlights-elementor-widgets' ),
		array(
			'image'         => Defaults::photo( 'soffit-two-story-dusk' ),
			'eyebrow'       => '',
			'primary_label' => __( 'Back to the form', 'xlights-elementor-widgets' ),
			'primary_link'  => '#quote',
			'bg'            => 'light',
		)
	),
);
