<?php
/**
 * Page template: Home.
 *
 * Returns the ordered section list. Each section: type (component), label
 * (editor panel title) and defaults (initial content, editable in Elementor).
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'         => array(
		'type'     => 'hero',
		'label'    => __( 'Premium Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'media',
			'height'          => 'full',
			'eyebrow'         => __( 'Permanent Seasonal Lighting', 'xlights-elementor-widgets' ),
			'heading'         => __( 'Beautiful every night. <em>Ready for every celebration.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( 'Architectural outdoor lighting for homes in {area} — warm white every evening, festive color whenever you want it, all from the X LIGHTS app.', 'xlights-elementor-widgets' ),
			'primary_label'   => '{cta}',
			'primary_link'    => '{quote_url}',
			'secondary_label' => __( 'Explore Lighting Solutions', 'xlights-elementor-widgets' ),
			'secondary_link'  => '{page:solutions}',
			'highlights'      => __( "Low-profile, color-matched track\nControlled from the X LIGHTS app\nProfessionally installed by a local team", 'xlights-elementor-widgets' ),
			'visual'          => 'image',
			'image'           => Defaults::photo( 'roofline-warm-white-modern' ),
			'image_alt'       => __( 'Modern single-story home with warm white permanent lighting along the roofline at dusk', 'xlights-elementor-widgets' ),
			'scene'           => 'roofline',
			'overlay'         => 55,
			'focal'           => 'center',
			'breadcrumbs'     => '',
			'estimate'        => 'yes',
		),
	),
	'intro'        => array(
		'type'     => 'intro',
		'label'    => __( 'Local Introduction', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'     => 'split',
			'eyebrow'    => __( 'X LIGHTS {market}', 'xlights-elementor-widgets' ),
			'heading'    => __( 'Outdoor lighting designed around your home — <em>and installed by a local team.</em>', 'xlights-elementor-widgets' ),
			'text'       => __( '<p>{dealer} designs and installs connected outdoor lighting for homes and businesses across {area}. From permanent soffit lighting to patio, deck and landscape lighting, every system is planned around your property’s architecture and the way you spend your evenings outside.</p><p>The result is lighting that makes your home look better and work better after dark — and that is ready for every season without a ladder.</p>', 'xlights-elementor-widgets' ),
			'points'     => __( "Permanent soffit lighting for every season\nBistro, deck and landscape lighting that works together\nOne app for compatible lights", 'xlights-elementor-widgets' ),
			'link_label' => __( 'About our team', 'xlights-elementor-widgets' ),
			'link'       => '{page:about}',
			'bg'         => 'light',
		),
	),
	'soffit'       => array(
		'type'     => 'split',
		'label'    => __( 'Permanent Soffit Lighting Feature', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'offset',
			'eyebrow'         => __( 'Our signature system', 'xlights-elementor-widgets' ),
			'heading'         => __( 'Permanent soffit lighting that <em>disappears by day.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( '<p>X LIGHTS permanent soffit lighting gives your home an elegant glow without the hassle of putting up and taking down holiday lights. The low-profile track is color-matched to blend into your roofline during the day.</p>', 'xlights-elementor-widgets' ),
			'points'          => __( "Low-profile, color-matched track\nDiscreet daytime appearance\nElegant warm-white architectural lighting\nColors and patterns for holidays and special occasions\nInstalled once — no more seasonal setup\nControlled from the X LIGHTS app", 'xlights-elementor-widgets' ),
			'primary_label'   => __( 'Explore Permanent Soffit Lighting', 'xlights-elementor-widgets' ),
			'primary_link'    => '{page:soffit}',
			'secondary_label' => '{cta}',
			'secondary_link'  => '{quote_url}',
			'visual'          => 'image',
			'image'           => Defaults::photo( 'roofline-two-story-modern' ),
			'image_alt'       => __( 'Modern multi-level home outlined with warm white permanent lighting at night', 'xlights-elementor-widgets' ),
			'scene'           => 'roofline',
			'image_position'  => 'right',
			'badge'           => __( 'Warm white, every night', 'xlights-elementor-widgets' ),
			'bg'              => 'warm',
		),
	),
	'compare'      => Defaults::holiday_compare(),
	'spaces'       => array(
		'type'     => 'cards',
		'label'    => __( 'Outdoor Spaces (Bistro, Commercial, Deck, Landscape)', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Beyond the roofline', 'xlights-elementor-widgets' ),
			'heading' => __( 'Light every part of <em>your property.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( 'Patios, decks, gardens and customer-facing spaces — each lit for the way you use it after dark.', 'xlights-elementor-widgets' ),
			'style'   => 'bento',
			'columns' => '4',
			'align'   => 'left',
			'items'   => array(
				array(
					'title'      => __( 'Hanging Bulb Bistro Lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'A welcoming glow for outdoor dining areas, patios and gathering spaces.', 'xlights-elementor-widgets' ),
					'image'      => Defaults::photo( 'firepit-lounge-string-lights' ),
					'scene'      => 'patio',
					'link_label' => __( 'Explore bistro lighting', 'xlights-elementor-widgets' ),
					'link'       => '{page:bistro}',
				),
				array(
					'title'      => __( 'Commercial Lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Restaurant patios, dealerships and customer-facing spaces that look their best after dark.', 'xlights-elementor-widgets' ),
					'image'      => Defaults::photo( 'pergola-bar' ),
					'scene'      => 'commercial',
					'link_label' => __( 'Explore commercial lighting', 'xlights-elementor-widgets' ),
					'link'       => '{page:commercial}',
				),
				array(
					'title'      => __( 'Deck Lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Steps, railings and walkways with a finished look.', 'xlights-elementor-widgets' ),
					'image'      => Defaults::photo( 'deck-stairs-dot-lights' ),
					'scene'      => 'deck',
					'link_label' => __( 'Explore deck lighting', 'xlights-elementor-widgets' ),
					'link'       => '{page:deck}',
				),
				array(
					'title'      => __( 'Landscape Lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Trees, garden beds and pathways after dark.', 'xlights-elementor-widgets' ),
					'image'      => Defaults::photo( 'entry-steps-night' ),
					'scene'      => 'landscape',
					'link_label' => __( 'Explore landscape lighting', 'xlights-elementor-widgets' ),
					'link'       => '{page:landscape}',
				),
			),
			'bg'      => 'light',
		),
	),
	'connected'    => array(
		'type'     => 'connected',
		'label'    => __( 'One Connected Lighting System', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'    => __( 'One connected system', 'xlights-elementor-widgets' ),
			'heading'    => __( 'Your roofline, patio and garden — <em>working together.</em>', 'xlights-elementor-widgets' ),
			'text'       => __( '<p>Compatible X LIGHTS soffit, bistro, deck and landscape lighting can be grouped and managed in the X LIGHTS app, so your whole property can share a schedule, a color palette or a holiday theme.</p>', 'xlights-elementor-widgets' ),
			'items'      => Defaults::connected_items(),
			'note'       => Defaults::compatibility_note(),
			'link_label' => __( 'See how the app works', 'xlights-elementor-widgets' ),
			'link'       => '{page:app}',
			'bg'         => 'dark',
		),
	),
	'app'          => array(
		'type'     => 'app_showcase',
		'label'    => __( 'X LIGHTS App Showcase', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'        => __( 'The X LIGHTS app', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Your lighting, <em>in the palm of your hand.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( 'Change colors, save favorites, set schedules and switch to seasonal patterns from your phone.', 'xlights-elementor-widgets' ),
			'items'          => Defaults::app_tabs( array( 'color', 'patterns', 'music', 'schedule', 'zones', 'share' ) ),
			'phone_position' => 'right',
			'show_store'     => 'yes',
			'link_label'     => __( 'Explore the X LIGHTS app', 'xlights-elementor-widgets' ),
			'link'           => '{page:app}',
			'bg'             => 'warm',
		),
	),
	'automation'   => Defaults::home_automation(),
	'why'          => array(
		'type'     => 'cards',
		'label'    => __( 'Why Choose X LIGHTS', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Why X LIGHTS', 'xlights-elementor-widgets' ),
			'heading' => __( 'Lighting that looks as good by day <em>as it does at night.</em>', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '3',
			'items'   => Defaults::why_items(),
			'bg'      => 'light',
		),
	),
	'financing'    => Defaults::financing( 'warm' ),
	'projects'     => Defaults::projects(
		'featured',
		__( 'Featured projects', 'xlights-elementor-widgets' ),
		__( 'A look at X LIGHTS installations.', 'xlights-elementor-widgets' ),
		array( 'bg' => 'warm' )
	),
	'reviews'      => array(
		'type'     => 'reviews',
		'label'    => __( 'Reviews', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'    => __( 'Customer reviews', 'xlights-elementor-widgets' ),
			'heading'    => __( 'What X LIGHTS customers say', 'xlights-elementor-widgets' ),
			'layout'     => 'carousel',
			'scope'      => 'local_first',
			'count'      => 6,
			'link_label' => __( 'Read more reviews', 'xlights-elementor-widgets' ),
			'link'       => '{page:reviews}',
			'bg'         => 'light',
		),
	),
	'service_area' => array(
		'type'     => 'service_area',
		'label'    => __( 'Service Area Preview', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'    => __( 'Service area', 'xlights-elementor-widgets' ),
			'heading'    => __( 'Proudly serving {area}.', 'xlights-elementor-widgets' ),
			'text'       => __( '<p>{dealer} designs and installs outdoor lighting for homes and businesses throughout our local service area.</p>', 'xlights-elementor-widgets' ),
			'link_label' => '{cta}',
			'link'       => '{quote_url}',
			'bg'         => 'warm',
		),
	),
	'cta'          => Defaults::cta(
		__( 'Ready to see your home <em>in a new light?</em>', 'xlights-elementor-widgets' ),
		__( 'Schedule a consultation and we will recommend a lighting plan for your property. Sharing your address and a few photos helps us prepare.', 'xlights-elementor-widgets' ),
		array( 'image' => Defaults::photo( 'soffit-house-dusk-wide' ), 'bg' => 'light' )
	),
);
