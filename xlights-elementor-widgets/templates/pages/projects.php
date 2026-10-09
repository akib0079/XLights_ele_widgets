<?php
/**
 * Page template: Projects / Gallery. The portfolio is entirely dynamic
 * (X LIGHTS → Projects); nothing here is static gallery data.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'         => array(
		'type'     => 'hero',
		'label'    => __( 'Projects Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'centered',
			'eyebrow'         => __( 'Projects / Gallery', 'xlights-elementor-widgets' ),
			'heading'         => __( 'Our work, <em>after dark.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( 'Permanent soffit, bistro, deck, landscape and commercial lighting installed by {dealer}.', 'xlights-elementor-widgets' ),
			'primary_label'   => '{cta}',
			'primary_link'    => '{quote_url}',
			'show_phone'      => '',
			'bg'              => 'dark',
		),
	),
	'intro'        => array(
		'type'     => 'intro',
		'label'    => __( 'Portfolio Introduction', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'centered',
			'heading' => __( 'A growing portfolio of local installations.', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>Browse recent projects across {area}. Use the filters to see a specific type of lighting, and select any project to view more photos.</p>', 'xlights-elementor-widgets' ),
			'spacing' => 'compact',
			'bg'      => 'light',
		),
	),
	'gallery'      => array(
		'type'     => 'projects',
		'label'    => __( 'Filters & Project Grid', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => '',
			'heading'        => '',
			'source'         => 'all',
			'count'          => 12,
			'columns'        => '3',
			'show_filters'   => 'yes',
			'load_more'      => 'yes',
			'empty_behavior' => 'message',
			'empty_text'     => __( 'New local projects are being added. Check back soon, or contact us to see examples of our work.', 'xlights-elementor-widgets' ),
			'spacing'        => 'compact',
			'bg'             => 'light',
		),
	),
	'before_after' => array(
		'type'     => 'before_after',
		'label'    => __( 'Before / After Comparisons', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Before & after', 'xlights-elementor-widgets' ),
			'heading' => __( 'See the transformation.', 'xlights-elementor-widgets' ),
			'text'    => __( 'Drag the slider to compare each property before and after installation.', 'xlights-elementor-widgets' ),
			'source'  => 'projects',
			'count'   => 4,
			'bg'      => 'dark',
		),
	),
	'featured'     => Defaults::projects(
		'featured',
		__( 'Featured projects', 'xlights-elementor-widgets' ),
		'',
		array(
			'eyebrow'    => __( 'Highlights', 'xlights-elementor-widgets' ),
			'count'      => 3,
			'link_label' => '',
			'bg'         => 'warm',
		)
	),
	'cta'          => Defaults::cta(
		__( 'Picture this at your place.', 'xlights-elementor-widgets' ),
		__( 'Tell us about your project and we will recommend lighting that suits your property.', 'xlights-elementor-widgets' ),
		array( 'bg' => 'light' )
	),
);
