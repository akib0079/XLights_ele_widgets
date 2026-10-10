<?php
/**
 * Page template: Permanent Soffit Lighting (primary service page).
 *
 * Technical figures from the corporate site (voltage, lights per run, diodes)
 * are pre-filled in the specification table but stay unpublished until each
 * row is verified for this market. HOA and warranty statements avoid promises.
 *
 * @package XLights
 */

use XLights\Pages\Defaults;

defined( 'ABSPATH' ) || exit;

return array(
	'hero'      => array(
		'type'     => 'hero',
		'label'    => __( 'Soffit Lighting Hero', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'          => 'media',
			'height'          => 'full',
			'eyebrow'         => __( 'Permanent Seasonal Lighting', 'xlights-elementor-widgets' ),
			'eyebrow_in_h1'   => 'yes',
			'heading'         => __( 'Beautiful every night. <em>Ready for every celebration.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( 'Permanent soffit lighting for homes in {area}: an elegant glow every evening, holiday color when you want it, and no more ladders.', 'xlights-elementor-widgets' ),
			'primary_label'   => __( 'Get a Soffit Lighting Quote', 'xlights-elementor-widgets' ),
			'primary_link'    => '{page:contact}?service=soffit#quote',
			'secondary_label' => __( 'See it by day', 'xlights-elementor-widgets' ),
			'secondary_link'  => '#xl-soffit-daytime',
			'highlights'      => __( "Low-profile, color-matched track\nWarm white or full color\nScheduling in the X LIGHTS app", 'xlights-elementor-widgets' ),
			'visual'          => 'image',
			'image'           => Defaults::img( 'home-two-story-night.jpg' ),
			'image_alt'       => __( 'Two-story home outlined with warm white permanent soffit lighting at night', 'xlights-elementor-widgets' ),
			'scene'           => 'roofline',
			'overlay'         => 55,
		),
	),
	'intro'     => array(
		'type'     => 'intro',
		'label'    => __( 'Product Introduction', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'  => 'split',
			'eyebrow' => __( 'Permanent soffit lighting', 'xlights-elementor-widgets' ),
			'heading' => __( 'An elegant glow without the <em>holiday-light hassle.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( '<p>X LIGHTS permanent soffit lighting gives your home an elegant glow without the hassle of putting up and taking down holiday lights. The low-profile lights blend into the roofline during the day.</p><p>At night, choose warm white to highlight your home’s architecture, or switch to colors and patterns for Christmas, Halloween, birthdays and other celebrations.</p>', 'xlights-elementor-widgets' ),
			'points'  => __( "Discreet appearance\nClean architectural finish\nWarm white for everyday use\nSeasonal flexibility\nYear-round convenience", 'xlights-elementor-widgets' ),
			'bg'      => 'light',
		),
	),
	'daytime'   => array(
		'type'     => 'split',
		'label'    => __( 'Daytime Appearance / Low-Profile Design', 'xlights-elementor-widgets' ),
		'anchor'   => 'xl-soffit-daytime',
		'defaults' => array(
			'eyebrow'        => __( 'By day', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Designed to <em>disappear</em> into the roofline.', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Most permanent lighting looks like hardware when it is switched off. The X LIGHTS track is low-profile and color-matched to sit close to your fascia, so your home keeps its clean lines during the day.</p>', 'xlights-elementor-widgets' ),
			'points'         => __( "Slim profile along the fascia\nTrack color matched to your trim\nDesigned for a clean, finished look", 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'image'          => Defaults::img( 'soffit-track-daytime.jpg' ),
			'image_alt'      => __( 'Daylight close-up of the slim X LIGHTS track and lights mounted along a dark fascia', 'xlights-elementor-widgets' ),
			'scene'          => 'daytime',
			'image_position' => 'left',
			'badge'          => __( 'Daytime view', 'xlights-elementor-widgets' ),
			'caption'        => __( 'Close-up of an installed track in daylight.', 'xlights-elementor-widgets' ),
			'bg'             => 'warm',
		),
	),
	'everyday'  => array(
		'type'     => 'split',
		'label'    => __( 'Architectural Everyday Lighting', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'         => 'offset',
			'eyebrow'        => __( 'Every night', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Warm white that <em>celebrates your architecture.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Soft, even light along the roofline brings out the shape of your home and makes it feel welcoming when you pull into the driveway. Set the brightness you like and let a schedule take care of the rest.</p>', 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'image'          => Defaults::photo( 'roofline-two-story-modern' ),
			'image_alt'      => __( 'Modern multi-level home outlined with warm white permanent lighting at night', 'xlights-elementor-widgets' ),
			'scene'          => 'roofline',
			'image_position' => 'right',
			'bg'             => 'light',
		),
	),
	'seasonal'  => array(
		'type'     => 'split',
		'label'    => __( 'Seasonal & Holiday Lighting', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'layout'         => 'cinematic',
			'eyebrow'        => __( 'Every celebration', 'xlights-elementor-widgets' ),
			'heading'        => __( 'Holiday lights, <em>without the ladder.</em>', 'xlights-elementor-widgets' ),
			'text'           => __( '<p>Switch to red and green for Christmas, orange and purple for Halloween, team colors for game day or a festive pattern for a birthday — then go back to warm white with a tap.</p>', 'xlights-elementor-widgets' ),
			'points'         => __( "Christmas and winter holidays\nHalloween and fall\nBirthdays, parties and game days", 'xlights-elementor-widgets' ),
			'visual'         => 'image',
			'image'          => Defaults::photo( 'roofline-holiday-color-modern' ),
			'image_alt'      => __( 'Modern single-story home with multicolor holiday lighting along the roofline at dusk', 'xlights-elementor-widgets' ),
			'scene'          => 'roofline',
			'palette'        => 'holiday',
			'image_position' => 'left',
			'bg'             => 'dark',
		),
	),
	'track'     => array(
		'type'     => 'cards',
		'label'    => __( 'Color-Matched Track Benefits', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'The color-matched track', 'xlights-elementor-widgets' ),
			'heading' => __( 'Small details that make a big difference.', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '3',
			'items'   => array(
				array(
					'icon'  => 'paint',
					'title' => __( 'Matched to your trim', 'xlights-elementor-widgets' ),
					'text'  => __( 'Track color is chosen to blend with your fascia so it reads as part of the house.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'ruler',
					'title' => __( 'Clean, continuous lines', 'xlights-elementor-widgets' ),
					'text'  => __( 'Lights sit in a tidy, evenly spaced run along the roofline.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'home',
					'title' => __( 'Respects your architecture', 'xlights-elementor-widgets' ),
					'text'  => __( 'A discreet daytime look that keeps the focus on your home, not the hardware.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'light',
		),
	),
	'app'       => array(
		'type'     => 'split',
		'label'    => __( 'Smart App Control', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'         => __( 'Smart app control', 'xlights-elementor-widgets' ),
			'heading'         => __( 'Lighting you can enjoy <em>all year.</em>', 'xlights-elementor-widgets' ),
			'text'            => __( '<p>Use the X LIGHTS app to adjust colors and brightness, save favorite looks and set schedules. It is lighting you can enjoy all year, with the fun of seasonal lights whenever you want them.</p>', 'xlights-elementor-widgets' ),
			'points'          => __( "Colors and brightness\nSaved favorites\nSchedules and timers", 'xlights-elementor-widgets' ),
			'secondary_label' => __( 'Explore the app', 'xlights-elementor-widgets' ),
			'secondary_link'  => '{page:app}',
			'visual'          => 'phone',
			'screen'          => 'color',
			'image_position'  => 'right',
			'bg'              => 'warm',
		),
	),
	'features'  => array(
		'type'     => 'cards',
		'label'    => __( 'Key Product Features', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Key features', 'xlights-elementor-widgets' ),
			'heading' => __( 'Everything you need for <em>year-round lighting.</em>', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '4',
			'items'   => array(
				array(
					'icon'  => 'eye',
					'title' => __( 'Discreet daytime look', 'xlights-elementor-widgets' ),
					'text'  => __( 'Low-profile lights and track.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'palette',
					'title' => __( 'Warm white & color', 'xlights-elementor-widgets' ),
					'text'  => __( 'Everyday white or festive color.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'sliders',
					'title' => __( 'Individually programmable', 'xlights-elementor-widgets' ),
					'text'  => __( 'Patterns, effects and dimming per light.', 'xlights-elementor-widgets' ),
					'gate'  => 'pending',
				),
				array(
					'icon'  => 'timer',
					'title' => __( 'Schedules', 'xlights-elementor-widgets' ),
					'text'  => __( 'Automatic on/off timers.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'droplet',
					'title' => __( 'Weather-resistant', 'xlights-elementor-widgets' ),
					'text'  => __( 'Built for outdoor use year-round.', 'xlights-elementor-widgets' ),
					'gate'  => 'pending',
				),
				array(
					'icon'  => 'bolt',
					'title' => __( 'Low-voltage LEDs', 'xlights-elementor-widgets' ),
					'text'  => __( 'Efficient LED lighting.', 'xlights-elementor-widgets' ),
					'gate'  => 'pending',
				),
				array(
					'icon'  => 'hub',
					'title' => __( 'Connected system', 'xlights-elementor-widgets' ),
					'text'  => __( 'Works with compatible X LIGHTS products.', 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'wrench',
					'title' => __( 'Professional install', 'xlights-elementor-widgets' ),
					'text'  => __( 'Installed by our local team.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'light',
		),
	),
	'automation'  => Defaults::bond_bridge( array( 'bg' => 'light' ) ),
	'products'    => array(
		'type'     => 'cards',
		'label'    => __( 'Soffit Lighting Products', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'The products', 'xlights-elementor-widgets' ),
			'heading' => __( 'What goes into <em>your system.</em>', 'xlights-elementor-widgets' ),
			'text'    => __( 'Low-profile, high-performance LED fixtures designed to discreetly illuminate eaves, overhangs and rooflines — all fully programmable from the X LIGHTS app.', 'xlights-elementor-widgets' ),
			'style'   => 'icon',
			'columns' => '3',
			'items'   => array(
				array(
					'icon'  => 'smartphone',
					'title' => __( 'Smart control box', 'xlights-elementor-widgets' ),
					'text'  => __( "Connects to your Wi-Fi or Bluetooth device for app-based control.\nBuilt-in and custom patterns · Full RGBW · Timers and schedules\nIP65 · 11\" × 8\" × 4\"", 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'roofline',
					'title' => __( '1" soffit lights', 'xlights-elementor-widgets' ),
					'text'  => __( "30 mm recessed light designed for illuminating soffits.\nSoft light that does not glare · Full RGBW\n120 lumens · 1 W · 48 V · IP67", 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'lightbulb',
					'title' => __( '4" recessed light', 'xlights-elementor-widgets' ),
					'text'  => __( "101 mm recessed light designed for illuminating soffits.\nSoft light that does not glare · Full RGBW\n250 lumens · 9 W · 24 V · IP65 · Black or white", 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'string-lights',
					'title' => __( 'String bulb lights', 'xlights-elementor-widgets' ),
					'text'  => __( "31\" spacing, 4\" drop, impact-resistant bulbs.\nDance-to-the-music feature · Full RGBW\n60 lumens · 1 W · 24 V", 'xlights-elementor-widgets' ),
				),
				array(
					'icon'  => 'layers',
					'title' => __( 'Flex light', 'xlights-elementor-widgets' ),
					'text'  => __( "3/4\" × 3/8\" in 49' or 19' lengths; can be cut to size, with accessories for different mounting applications.\nFull RGBW · 200 lumens/m · 10 W/m · 24 V · IP67", 'xlights-elementor-widgets' ),
				),
			),
			'note'    => __( 'Product details from X LIGHTS. The products for your home are confirmed in your proposal.', 'xlights-elementor-widgets' ),
			'bg'      => 'warm',
		),
	),
	'lifestyle' => array(
		'type'     => 'cards',
		'label'    => __( 'Lifestyle & Home Examples', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Made for real life', 'xlights-elementor-widgets' ),
			'heading' => __( 'One roofline, <em>many moods.</em>', 'xlights-elementor-widgets' ),
			'style'   => 'image',
			'columns' => '3',
			'items'   => array(
				array(
					'title'   => __( 'Everyday elegance', 'xlights-elementor-widgets' ),
					'text'    => __( 'Warm white that welcomes you home every evening.', 'xlights-elementor-widgets' ),
					'image'   => Defaults::img( 'home-single-story-dusk.jpg' ),
					'scene'   => 'roofline',
					'palette' => 'warm',
				),
				array(
					'title'   => __( 'The holidays', 'xlights-elementor-widgets' ),
					'text'    => __( 'Festive color for the season — no ladders, no tangles.', 'xlights-elementor-widgets' ),
					'image'   => Defaults::photo( 'roofline-holiday-color-modern' ),
					'scene'   => 'roofline',
					'palette' => 'holiday',
				),
				array(
					'title'   => __( 'Every celebration', 'xlights-elementor-widgets' ),
					'text'    => __( 'Birthdays, game days, Halloween and more.', 'xlights-elementor-widgets' ),
					'image'   => Defaults::photo( 'steps-color-celebration' ),
					'scene'   => 'roofline',
					'palette' => 'halloween',
				),
			),
			'bg'      => 'light',
		),
	),
	'compare'   => array(
		'type'     => 'before_after',
		'label'    => __( 'Everyday / Holiday Comparison', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow'      => __( 'Same roofline, two looks', 'xlights-elementor-widgets' ),
			'heading'      => __( 'Warm white every night. <em>Color when you want it.</em>', 'xlights-elementor-widgets' ),
			'text'         => __( 'Drag the slider to switch the same home from everyday warm white to holiday color.', 'xlights-elementor-widgets' ),
			'source'       => 'manual',
			'before_image' => Defaults::photo( 'roofline-warm-white-modern' ),
			'after_image'  => Defaults::photo( 'roofline-holiday-color-modern' ),
			'before_label' => __( 'Everyday', 'xlights-elementor-widgets' ),
			'after_label'  => __( 'Holiday', 'xlights-elementor-widgets' ),
			'bg'           => 'dark',
			'editor_note'  => __( 'Shows the same home in two looks. To use your own, upload two photos of one home taken from the same spot (for example day and night, or warm white and holiday color).', 'xlights-elementor-widgets' ),
		),
	),
	'permanent' => array(
		'type'     => 'cards',
		'label'    => __( 'Why Permanent Installation Makes Sense', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'Why go permanent', 'xlights-elementor-widgets' ),
			'heading' => __( 'Install once. <em>Enjoy every season.</em>', 'xlights-elementor-widgets' ),
			'style'   => 'steps',
			'columns' => '4',
			'items'   => array(
				array(
					'title' => __( 'No more ladders', 'xlights-elementor-widgets' ),
					'text'  => __( 'Skip the twice-a-year climb to hang and remove lights.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'No storage boxes', 'xlights-elementor-widgets' ),
					'text'  => __( 'Nothing to untangle, replace or store between seasons.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Useful every night', 'xlights-elementor-widgets' ),
					'text'  => __( 'Everyday architectural lighting, not just a holiday display.', 'xlights-elementor-widgets' ),
				),
				array(
					'title' => __( 'Ready on short notice', 'xlights-elementor-widgets' ),
					'text'  => __( 'Switch themes for any occasion from your phone.', 'xlights-elementor-widgets' ),
				),
			),
			'bg'      => 'warm',
		),
	),
	'financing' => Defaults::financing( 'light' ),
	'projects'  => Defaults::projects(
		'soffit',
		__( 'Permanent soffit lighting projects', 'xlights-elementor-widgets' ),
		__( 'Recent roofline installations by {dealer}.', 'xlights-elementor-widgets' ),
		array( 'bg' => 'light' )
	),
	'faq'       => array(
		'type'     => 'faq',
		'label'    => __( 'FAQ', 'xlights-elementor-widgets' ),
		'defaults' => array(
			'eyebrow' => __( 'FAQ', 'xlights-elementor-widgets' ),
			'heading' => __( 'Permanent soffit lighting questions', 'xlights-elementor-widgets' ),
			'text'    => __( 'Don’t see your question? Call us at {phone}.', 'xlights-elementor-widgets' ),
			'items'   => array(
				array(
					'question' => __( 'Will the lights be noticeable during the day?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'The X LIGHTS track is low-profile and color-matched, so it is designed to blend into your roofline when the lights are off. We are happy to show you examples during your consultation.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Can I use them every day, not just for the holidays?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Yes. Many homeowners use warm white as everyday architectural lighting, then switch to colors and patterns for holidays and special occasions.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'How do I control the lights?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'With the X LIGHTS app. You can change colors and brightness, save favorite looks and set schedules. We will help you get set up after installation.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Will my HOA allow permanent lighting?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'HOA rules vary, so we recommend checking your association’s guidelines. The discreet daytime appearance is one reason homeowners choose permanent soffit lighting, and we can provide product information for an HOA review.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'How long does installation take?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'It depends on the size and complexity of your roofline. We will give you a time estimate along with your quote.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'What about specifications and warranty?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'We provide product specifications and warranty details for your specific system with your proposal.', 'xlights-elementor-widgets' ),
				),
				array(
					'question' => __( 'Is financing available?', 'xlights-elementor-widgets' ),
					'answer'   => __( 'Financing may be available. Ask about current options during your consultation.', 'xlights-elementor-widgets' ),
					'gate'     => 'financing',
				),
			),
			'bg'      => 'light',
		),
	),
	'cta'       => Defaults::cta(
		__( 'Get your permanent soffit lighting quote.', 'xlights-elementor-widgets' ),
		__( 'Share your address and a photo of your roofline, and we will prepare recommendations for your home.', 'xlights-elementor-widgets' ),
		array(
			'primary_label' => __( 'Get a Soffit Lighting Quote', 'xlights-elementor-widgets' ),
			'primary_link'  => '{page:contact}?service=soffit#quote',
			'bg'            => 'warm',
		)
	),
);
