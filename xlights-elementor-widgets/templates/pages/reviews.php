<?php
/**
 * Page template: Reviews. Content comes exclusively from real, permissioned
 * entries in X LIGHTS → Reviews. Brand-level reviews are labeled as such.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'        => array(
		'type'     => 'hero',
		'label'    => __( 'Reviews Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'centered',
			'eyebrow'         => __( 'Reviews', 'xlights-elementor-widgets' ),
			'heading'         => __( 'What our customers <em>are saying.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( 'Real feedback from homeowners and businesses who chose X LIGHTS.', 'xlights-elementor-widgets' ),
			'primary_label'   => '{cta}',
			'primary_link'    => '{quote_url}',
			'secondary_label' => '',
			'show_phone'      => '',
			'bg'              => 'dark',
		),
	),
	'intro'       => array(
		'type'     => 'intro',
		'label'    => __( 'Trust Introduction', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'centered',
			'eyebrow' => __( 'Honest feedback', 'xlights-elementor-widgets' ),
			'heading' => __( 'Every review here comes from a real customer.', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>We publish reviews with our customers’ permission and never edit their meaning. Reviews from {market} customers are shown first.</p>', 'xlights-elementor-widgets' ),
			'bg'      => 'light',
		),
	),
	'featured'    => array(
		'type'     => 'reviews',
		'label'    => __( 'Featured Testimonials', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'Featured', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Featured reviews', 'xlights-elementor-widgets' ),
			'layout'         => 'featured',
			'scope'          => 'local_first',
			'featured_only'  => 'yes',
			'count'          => 4,
			'google_link'    => '',
			'empty_behavior' => 'hide',
			'bg'             => 'warm',
		),
	),
	'grid'        => array(
		'type'     => 'reviews',
		'label'    => __( 'Review Grid', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'All reviews', 'xlights-elementor-widgets' ),
			'heading'        => __( 'From our customers', 'xlights-elementor-widgets' ),
			'layout'         => 'grid',
			'scope'          => 'local_first',
			'count'          => 24,
			'google_link'    => 'yes',
			'empty_behavior' => 'message',
			'empty_text'     => __( 'Reviews from local customers will appear here as projects are completed. In the meantime, browse our projects or contact us with any questions.', 'xlights-elementor-widgets' ),
			'bg'             => 'light',
		),
	),
	'photos'      => Defaults::projects(
		'featured',
		__( 'Projects from our customers', 'xlights-elementor-widgets' ),
		__( 'See the kind of work our customers are talking about.', 'xlights-elementor-widgets' ),
		array(
			'eyebrow' => __( 'Customer projects', 'xlights-elementor-widgets' ),
			'count'   => 3,
			'bg'      => 'warm',
		)
	),
	'attribution' => array(
		'type'     => 'intro',
		'label'    => __( 'Local Review Attribution', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'note',
			'heading' => __( 'About these reviews', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>Reviews marked as brand-level come from customers of other X LIGHTS locations and are shown to give a fuller picture of the product. Reviews without that label are from customers of {dealer}.</p>', 'xlights-elementor-widgets' ),
			'spacing' => 'compact',
			'bg'      => 'light',
		),
	),
	'google'      => array(
		'type'     => 'cta',
		'label'    => __( 'Google Reviews Link', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'style'         => 'band',
			'eyebrow'       => __( 'Share your experience', 'xlights-elementor-widgets' ),
			'heading'       => __( 'Had X LIGHTS installed? We would love your feedback.', 'xlights-elementor-widgets' ),
			'text'          => __( 'Leave a review on Google to help your neighbors find us.', 'xlights-elementor-widgets' ),
			'primary_label' => __( 'Review us on Google', 'xlights-elementor-widgets' ),
			'primary_link'  => array(
				'url'         => '{google_reviews}',
				'is_external' => 'on',
			),
			'show_phone'    => '',
			'gate'          => 'google_reviews',
			'bg'            => 'dark',
		),
	),
	'cta'         => Defaults::cta(
		__( 'Join our happy customers.', 'xlights-elementor-widgets' ),
		__( 'Schedule a consultation and see what X LIGHTS can do for your home or business.', 'xlights-elementor-widgets' ),
		array( 'bg' => 'warm' )
	),
);
