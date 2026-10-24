<?php
/**
 * Page template: Commercial Lighting. Ends with the commercial variant of
 * the quote form. No commercial warranties, minimums, timelines, maintenance
 * agreements or financing programs are stated.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'         => array(
		'type'     => 'hero',
		'label'    => __( 'Commercial Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'split',
			'height'          => 'tall',
			'eyebrow'         => __( 'Commercial Lighting', 'xlights-elementor-widgets' ),
			'eyebrow_in_h1'   => 'yes',
			'heading'         => __( 'Outdoor lighting that <em>works as hard as your business.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( 'Architectural, patio and landscape lighting for restaurants, dealerships, hospitality and commercial properties in {area}.', 'xlights-elementor-widgets' ),
			'primary_label'   => __( 'Request a Commercial Consultation', 'xlights-elementor-widgets' ),
			'primary_link'    => '#quote',
			'secondary_label' => __( 'See the Cottonmouth project', 'xlights-elementor-widgets' ),
			'secondary_link'  => '#xl-commercial-case-study',
			'visual'          => 'image',
			'image'           => Defaults::photo( 'commercial-cottonmouth-roofline' ),
			'image_alt'       => __( 'Cottonmouth Dispensary sign at dusk under green X LIGHTS soffit lights along the cornice', 'xlights-elementor-widgets' ),
			'scene'           => 'commercial',
			'overlay'         => 50,
			'bg'              => 'dark',
		),
	),
	'why'          => array(
		'type'     => 'intro',
		'label'    => __( 'Why Commercial Exterior Lighting Matters', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'split',
			'eyebrow' => __( 'Why it matters', 'xlights-elementor-widgets' ),
			'heading' => __( 'Your property makes its first impression <em>before anyone walks in.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>Customers notice how a business looks after dark. Thoughtful exterior lighting helps your property stand out from the street, makes outdoor spaces more inviting, and supports the experience you want guests to have.</p>', 'xlights-elementor-widgets' ),
			'points'  => __( "Stronger presence from the street\nOutdoor spaces customers want to use\nA consistent look across the property\nLighting you can adjust for seasons and events", 'xlights-elementor-widgets' ),
			'bg'      => 'light',
		),
	),
	'case_study'   => array(
		'type'     => 'case_study',
		'label'    => __( 'Featured Project: Cottonmouth Dispensary', 'xlights-elementor-widgets' ),
		'anchor'   => 'xl-commercial-case-study',
		'defaults' => array(
			'eyebrow'       => __( 'Featured commercial project', 'xlights-elementor-widgets' ),
			'heading'       => __( 'Bringing the Cottonmouth brand <em>to light.</em>', 'xlights-elementor-widgets' ),
			'text'          => __( '<p>The owner of Cottonmouth Dispensary wanted to give his commercial property a more upscale appearance and bring the company’s signature colors into the building’s exterior.</p><p>Using X LIGHTS soffit lights and line lighting, we created a coordinated lighting design that highlights the architecture and carries the brand colors across the property.</p><p>With most customers visiting in the evening, the lighting makes an impression when it matters most. The result is a polished, distinctive storefront with a stronger nighttime presence — now visible from the highway.</p>', 'xlights-elementor-widgets' ),
			'takeaway'      => __( 'This project shows how commercial lighting can enhance a building’s appearance while making its branding a visible part of the architecture.', 'xlights-elementor-widgets' ),
			'facts'         => array(
				array(
					'label' => __( 'Client', 'xlights-elementor-widgets' ),
					'value' => __( 'Cottonmouth Dispensary', 'xlights-elementor-widgets' ),
				),
				array(
					'label' => __( 'Goal', 'xlights-elementor-widgets' ),
					'value' => __( 'A more upscale look in the brand’s signature colors', 'xlights-elementor-widgets' ),
				),
				array(
					'label' => __( 'Lighting', 'xlights-elementor-widgets' ),
					'value' => __( 'X LIGHTS soffit lights and line lighting', 'xlights-elementor-widgets' ),
				),
				array(
					'label' => __( 'Result', 'xlights-elementor-widgets' ),
					'value' => __( 'A stronger nighttime presence, visible from the highway', 'xlights-elementor-widgets' ),
				),
			),
			'photos'        => array(
				array(
					'image'     => Defaults::photo( 'commercial-cottonmouth-entrance' ),
					'image_alt' => __( 'Cottonmouth Dispensary entrance at dusk with green line lighting on the columns and around the logo sign', 'xlights-elementor-widgets' ),
					'caption'   => __( 'Line lighting frames the entrance and the logo sign', 'xlights-elementor-widgets' ),
				),
				array(
					'image'     => Defaults::photo( 'commercial-cottonmouth-storefront' ),
					'image_alt' => __( 'Wide view of the Cottonmouth Dispensary storefront at dusk with green lighting across the building', 'xlights-elementor-widgets' ),
					'caption'   => __( 'Brand color carried across the whole property', 'xlights-elementor-widgets' ),
				),
			),
			'primary_label' => __( 'Plan your commercial lighting', 'xlights-elementor-widgets' ),
			'primary_link'  => '#quote',
			'bg'            => 'dark',
		),
	),
	'services'     => array(
		'type'     => 'cards',
		'label'    => __( 'Lighting Services for Businesses', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'What we light', 'xlights-elementor-widgets' ),
			'heading' => __( 'Lighting for the whole property.', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '3',
			'items'   => array(
				array(
					'icon'       => 'building',
					'title'      => __( 'Architectural & storefront lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Permanent lighting along rooflines and facades gives your building a polished nighttime presence in warm white, with the option to switch to brand colors or seasonal themes from the app.', 'xlights-elementor-widgets' ),
					'link_label' => __( 'Permanent lighting', 'xlights-elementor-widgets' ),
					'link'       => '{page:soffit}',
				),
				array(
					'icon'       => 'string-lights',
					'title'      => __( 'Patio & bistro lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Hanging bulb bistro lighting creates the warm atmosphere guests look for in outdoor dining. We plan layouts around seating, service paths and your building’s architecture.', 'xlights-elementor-widgets' ),
					'link_label' => __( 'Bistro lighting', 'xlights-elementor-widgets' ),
					'link'       => '{page:bistro}',
				),
				array(
					'icon'       => 'path',
					'title'      => __( 'Landscape & pathway lighting', 'xlights-elementor-widgets' ),
					'text'       => __( 'Pathway, tree and accent lighting helps customers find their way from the parking area to the entrance and shows off your landscaping after dark.', 'xlights-elementor-widgets' ),
					'link_label' => __( 'Landscape lighting', 'xlights-elementor-widgets' ),
					'link'       => '{page:landscape}',
				),
			),
			'bg'      => 'warm',
		),
	),
	'management'   => array(
		'type'     => 'connected',
		'label'    => __( 'Connected Lighting Management', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Connected management', 'xlights-elementor-widgets' ),
			'heading' => __( 'Manage every zone <em>from one app.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>Group compatible lighting by area, set schedules that match your business hours, and change themes for holidays, promotions or events without calling a contractor.</p>', 'xlights-elementor-widgets' ),
			'items'   => array(
				array(
					'title' => __( 'Building & roofline', 'xlights-elementor-widgets' ),
					'text'  => __( 'Permanent architectural lighting', 'xlights-elementor-widgets' ),
					'icon'  => 'building',
				),
				array(
					'title' => __( 'Patio & dining', 'xlights-elementor-widgets' ),
					'text'  => __( 'Bistro and ambient lighting', 'xlights-elementor-widgets' ),
					'icon'  => 'utensils',
				),
				array(
					'title' => __( 'Walkways', 'xlights-elementor-widgets' ),
					'text'  => __( 'Pathway and step lighting', 'xlights-elementor-widgets' ),
					'icon'  => 'path',
				),
				array(
					'title' => __( 'Landscape', 'xlights-elementor-widgets' ),
					'text'  => __( 'Trees, beds and features', 'xlights-elementor-widgets' ),
					'icon'  => 'tree',
				),
			),
			'note'    => Defaults::compatibility_note(),
			'bg'      => 'dark',
		),
	),
	'process'      => array(
		'type'     => 'cards',
		'label'    => __( 'Custom Design & Layout Process', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Our process', 'xlights-elementor-widgets' ),
			'heading' => __( 'From site visit to switch-on.', 'xlights-elementor-widgets' ),
			'style'   => 'steps',
			'columns' => '4',
			'items'   => array(
				array(
					'title' => __( 'Consultation', 'xlights-elementor-widgets' ),
					'text'  => __( 'We learn about your business, your customers and how the property is used after dark.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Site assessment', 'xlights-elementor-widgets' ),
					'text'  => __( 'We review the building, outdoor spaces, power access and any property requirements.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Lighting plan & proposal', 'xlights-elementor-widgets' ),
					'text'  => __( 'A layout, product recommendations and a detailed proposal for your review.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Installation & handover', 'xlights-elementor-widgets' ),
					'text'  => __( 'Professional installation scheduled around your operations, plus app setup for your team.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'light',
		),
	),
	'applications' => array(
		'type'     => 'cards',
		'label'    => __( 'Commercial Property Applications', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Who we help', 'xlights-elementor-widgets' ),
			'heading' => __( 'Lighting for customer-facing spaces.', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '3',
			'items'   => array(
				array(
					'icon'  => 'utensils',
					'title' => __( 'Restaurants & patios', 'xlights-elementor-widgets' ),
					'text'  => __( 'Outdoor dining that feels warm and inviting.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'car',
					'title' => __( 'Car dealerships', 'xlights-elementor-widgets' ),
					'text'  => __( 'Showroom exteriors and lots that stand out at night.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'bed',
					'title' => __( 'Hotels & hospitality', 'xlights-elementor-widgets' ),
					'text'  => __( 'Entries, courtyards and pool areas guests remember.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'store',
					'title' => __( 'Retail & shopping centers', 'xlights-elementor-widgets' ),
					'text'  => __( 'Storefronts and walkways that draw people in.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'building',
					'title' => __( 'Commercial properties', 'xlights-elementor-widgets' ),
					'text'  => __( 'Offices and mixed-use buildings with a polished look.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'sparkles',
					'title' => __( 'Other outdoor spaces', 'xlights-elementor-widgets' ),
					'text'  => __( 'Any business that needs architectural, accent or ambient lighting.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'warm',
		),
	),
	'install'      => array(
		'type'     => 'cards',
		'label'    => __( 'Benefits of Professional Installation', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Professional installation', 'xlights-elementor-widgets' ),
			'heading' => __( 'Done right, the first time.', 'xlights-elementor-widgets' ),
			'style'   => 'minimal',
			'columns' => '3',
			'items'   => array(
				array(
					'icon'  => 'clipboard',
					'title' => __( 'Planned around operations', 'xlights-elementor-widgets' ),
					'text'  => __( 'Scheduling that respects your hours and customers.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'ruler',
					'title' => __( 'Clean, consistent finish', 'xlights-elementor-widgets' ),
					'text'  => __( 'Even spacing and tidy runs that look intentional.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'smartphone',
					'title' => __( 'Ready to use', 'xlights-elementor-widgets' ),
					'text'  => __( 'App setup and a walkthrough for your staff.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'light',
		),
	),
	'trust'        => array(
		'type'     => 'trust',
		'label'    => __( 'Local Dealer Trust Section', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'      => __( 'Your local partner', 'xlights-elementor-widgets' ),
			'heading'      => __( 'A {market} team that knows the area.', 'xlights-elementor-widgets' ),
			'text'         => __( '{dealer} works with businesses across {area}.', 'xlights-elementor-widgets' ),
			'show_license' => 'yes',
			'items'        => array(
				array(
					'icon'  => 'map-pin',
					'title' => __( 'Local team', 'xlights-elementor-widgets' ),
					'text'  => __( 'Consultation through installation, handled locally.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'handshake',
					'title' => __( 'One point of contact', 'xlights-elementor-widgets' ),
					'text'  => __( 'A direct line for questions after installation.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'shield-check',
					'title' => __( 'Licensed and insured', 'xlights-elementor-widgets' ),
					'text'  => __( 'Documentation available on request.', 'xlights-elementor-widgets' ),
					'gate'  => 'credentials',
				),
			),
			'bg'           => 'dark',
		),
	),
	'financing'    => Defaults::financing( 'light' ),
	'projects'     => array_merge(
		Defaults::projects( 'commercial', __( 'Commercial projects', 'xlights-elementor-widgets' ), '', array( 'bg' => 'warm' ) ),
		array( 'anchor' => 'xl-commercial-projects' )
	),
	'faq'          => array(
		'type'     => 'faq',
		'label'    => __( 'Commercial FAQ', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'     => __( 'FAQ', 'xlights-elementor-widgets' ),
			'heading'     => __( 'Commercial lighting questions', 'xlights-elementor-widgets' ),
			'items'       => array(
				array(
					'question' => __( 'What types of businesses do you work with?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Restaurants and outdoor dining, car dealerships, hotels and hospitality venues, retail, offices and other commercial properties with customer-facing outdoor spaces.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Can you work around our business hours?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'We plan installation schedules with you during the proposal stage to keep disruption to a minimum.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Can we use our brand colors?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Color-capable products can be set to custom colors in the X LIGHTS app. We will confirm which products in your plan support color.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'What about warranty, maintenance and timelines?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Warranty coverage, service options and project timelines are detailed in your commercial proposal.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'          => 'light',
			'editor_note' => __( 'Confirm commercial warranty, maintenance, timelines, minimums and financing with the dealer before adding specifics.', 'xlights-elementor-widgets' ),
		),
	),
	'form'         => array(
		'type'     => 'quote_form',
		'label'    => __( 'Commercial Consultation Form', 'xlights-elementor-widgets' ),
		'anchor'   => 'quote',
		'defaults' => array(
			'eyebrow'       => __( 'Commercial consultation', 'xlights-elementor-widgets' ),
			'heading'       => __( 'Tell us about your property.', 'xlights-elementor-widgets' ),
			'text'          => __( 'Share a few details and photos and we will contact you to schedule a site visit.', 'xlights-elementor-widgets' ),
			'variant'       => 'commercial',
			'submit_label'  => __( 'Request a Commercial Consultation', 'xlights-elementor-widgets' ),
			'aside_heading' => __( 'What happens next', 'xlights-elementor-widgets' ),
			'aside_text'    => __( 'We review your request and photos, then contact you to arrange a site assessment.', 'xlights-elementor-widgets' ),
			'aside_points'  => __( "Photos help us understand the building and outdoor spaces\nThe property address lets us review access and layout\nWe will confirm product options in your proposal", 'xlights-elementor-widgets' ),
			'bg'            => 'warm',
		),
	),
);
