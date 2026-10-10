<?php
/**
 * Central dealer configuration store.
 *
 * Every dealer-specific value (name, phone, service areas, credentials, financing,
 * integrations) lives in one option so a new dealer site is configured without code.
 * The schema below drives defaults, sanitization and the admin UI.
 *
 * @package XLights
 */

namespace XLights\Settings;

defined( 'ABSPATH' ) || exit;

final class Settings {

	const OPTION = 'xlights_settings';

	/** @var array|null Request cache. */
	private static $cache = null;

	/**
	 * Settings schema grouped by admin tab.
	 *
	 * Field keys:
	 *  - type: text|textarea|lines|email|emails|url|tel|color|font|media|select|checkbox|number|secret
	 *  - group: optional sub-heading on the settings screen.
	 *  - approval: true marks values that need client confirmation before publication.
	 *
	 * @return array
	 */
	public static function schema() {
		return array(
			'dealer'    => array(
				'label'  => __( 'Dealer', 'xlights-elementor-widgets' ),
				'intro'  => __( 'Identity of this dealer website. Values are reused everywhere through tokens such as {dealer}, {market} and {area}.', 'xlights-elementor-widgets' ),
				'fields' => array(
					'dealer_id'         => array(
						'type'        => 'text',
						'label'       => __( 'Dealer / location identifier', 'xlights-elementor-widgets' ),
						'default'     => 'socal',
						'description' => __( 'Short internal ID added to lead emails and exports, e.g. "socal" or "newjersey".', 'xlights-elementor-widgets' ),
					),
					'brand_name'        => array(
						'type'    => 'text',
						'label'   => __( 'Brand name', 'xlights-elementor-widgets' ),
						'default' => 'X LIGHTS',
					),
					'dealer_name'       => array(
						'type'    => 'text',
						'label'   => __( 'Dealer display name', 'xlights-elementor-widgets' ),
						'default' => 'X LIGHTS Southern California',
					),
					'market_name'       => array(
						'type'        => 'text',
						'label'       => __( 'Market / location name', 'xlights-elementor-widgets' ),
						'default'     => 'Southern California',
						'description' => __( 'Used by the {market} token.', 'xlights-elementor-widgets' ),
					),
					'primary_area'      => array(
						'type'        => 'text',
						'label'       => __( 'Primary local focus', 'xlights-elementor-widgets' ),
						'default'     => 'San Clemente and South Orange County',
						'description' => __( 'Used by the {area} token in headlines and copy.', 'xlights-elementor-widgets' ),
					),
					'service_areas'     => array(
						'type'        => 'lines',
						'label'       => __( 'Service areas (one per line)', 'xlights-elementor-widgets' ),
						'default'     => "San Clemente\nSouth Orange County",
						'description' => __( 'List only verified areas. Shown in the footer, service-area sections and LocalBusiness schema.', 'xlights-elementor-widgets' ),
						'approval'    => true,
					),
					'service_area_note' => array(
						'type'    => 'text',
						'label'   => __( 'Service-area note', 'xlights-elementor-widgets' ),
						'default' => __( 'Not sure whether we serve your neighborhood? Get in touch and we will confirm.', 'xlights-elementor-widgets' ),
					),
					'legal_name'        => array(
						'type'        => 'text',
						'label'       => __( 'Legal business name', 'xlights-elementor-widgets' ),
						'default'     => '',
						'description' => __( 'Optional. Used in the footer copyright and schema when provided.', 'xlights-elementor-widgets' ),
						'approval'    => true,
					),
					'dealer_intro'      => array(
						'type'    => 'textarea',
						'label'   => __( 'Short company description', 'xlights-elementor-widgets' ),
						'default' => __( '{dealer} designs and installs permanent soffit, bistro, deck and landscape lighting for homes and businesses in {area}.', 'xlights-elementor-widgets' ),
					),
					'corporate_url'     => array(
						'type'    => 'url',
						'label'   => __( 'Corporate website', 'xlights-elementor-widgets' ),
						'default' => 'https://www.xlightsled.com/',
					),
				),
			),
			'contact'   => array(
				'label'  => __( 'Contact & CTAs', 'xlights-elementor-widgets' ),
				'fields' => array(
					'phone'               => array(
						'type'    => 'tel',
						'label'   => __( 'Dealer phone', 'xlights-elementor-widgets' ),
						'default' => '949-795-2206',
					),
					'email'               => array(
						'type'    => 'email',
						'label'   => __( 'Public email', 'xlights-elementor-widgets' ),
						'default' => 'info@xlightsled.com',
					),
					'show_email'          => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show public email on the site', 'xlights-elementor-widgets' ),
						'default' => 1,
					),
					'address_street'      => array(
						'type'     => 'text',
						'label'    => __( 'Street address', 'xlights-elementor-widgets' ),
						'default'  => '',
						'approval' => true,
					),
					'address_city'        => array(
						'type'    => 'text',
						'label'   => __( 'City', 'xlights-elementor-widgets' ),
						'default' => '',
					),
					'address_state'       => array(
						'type'    => 'text',
						'label'   => __( 'State', 'xlights-elementor-widgets' ),
						'default' => 'CA',
					),
					'address_zip'         => array(
						'type'    => 'text',
						'label'   => __( 'ZIP code', 'xlights-elementor-widgets' ),
						'default' => '',
					),
					'show_address'        => array(
						'type'        => 'checkbox',
						'label'       => __( 'Publish the street address', 'xlights-elementor-widgets' ),
						'default'     => 0,
						'description' => __( 'Leave off for service-area businesses without a public office.', 'xlights-elementor-widgets' ),
					),
					'hours'               => array(
						'type'        => 'textarea',
						'label'       => __( 'Business hours', 'xlights-elementor-widgets' ),
						'default'     => '',
						'description' => __( 'Optional, one line per entry, e.g. "Mon–Fri: 8am – 6pm".', 'xlights-elementor-widgets' ),
					),
					'primary_cta_label'   => array(
						'type'    => 'text',
						'label'   => __( 'Primary CTA label', 'xlights-elementor-widgets' ),
						'default' => __( 'Get a Quote', 'xlights-elementor-widgets' ),
					),
					'secondary_cta_label' => array(
						'type'    => 'text',
						'label'   => __( 'Secondary CTA label', 'xlights-elementor-widgets' ),
						'default' => __( 'Schedule a Consultation', 'xlights-elementor-widgets' ),
					),
					'quote_url'           => array(
						'type'        => 'text',
						'label'       => __( 'Consultation / quote URL', 'xlights-elementor-widgets' ),
						'default'     => '{page:contact}#quote',
						'description' => __( 'Where Get a Quote buttons point. Supports {page:contact}. Replace with a scheduling link if one is configured.', 'xlights-elementor-widgets' ),
					),
				),
			),
			'leads'     => array(
				'label'  => __( 'Leads & Form', 'xlights-elementor-widgets' ),
				'fields' => array(
					'lead_recipients'     => array(
						'type'        => 'emails',
						'label'       => __( 'Lead notification recipients', 'xlights-elementor-widgets' ),
						'default'     => 'info@xlightsled.com',
						'description' => __( 'Comma-separated list.', 'xlights-elementor-widgets' ),
					),
					'from_name'           => array(
						'type'    => 'text',
						'label'   => __( 'Email "From" name', 'xlights-elementor-widgets' ),
						'default' => '',
					),
					'from_email'          => array(
						'type'        => 'email',
						'label'       => __( 'Email "From" address', 'xlights-elementor-widgets' ),
						'default'     => '',
						'description' => __( 'Must be an address your SMTP provider is allowed to send from. Leave blank to use the WordPress default.', 'xlights-elementor-widgets' ),
					),
					'attach_photos'       => array(
						'type'        => 'checkbox',
						'label'       => __( 'Attach uploaded photos to the notification email', 'xlights-elementor-widgets' ),
						'default'     => 0,
						'description' => __( 'Off by default: emails contain secure, login-protected links instead.', 'xlights-elementor-widgets' ),
					),
					'autoreply_enabled'   => array(
						'type'    => 'checkbox',
						'label'   => __( 'Send the customer a confirmation email', 'xlights-elementor-widgets' ),
						'default' => 0,
					),
					'autoreply_subject'   => array(
						'type'    => 'text',
						'label'   => __( 'Confirmation subject', 'xlights-elementor-widgets' ),
						'default' => __( 'We received your request — {dealer}', 'xlights-elementor-widgets' ),
					),
					'autoreply_message'   => array(
						'type'    => 'textarea',
						'label'   => __( 'Confirmation message', 'xlights-elementor-widgets' ),
						'default' => __( "Thank you for contacting {dealer}. We have received your request and a member of our team will be in touch soon.\n\nIf you need to reach us sooner, call {phone}.", 'xlights-elementor-widgets' ),
					),
					'success_message'     => array(
						'type'    => 'textarea',
						'label'   => __( 'On-screen success message', 'xlights-elementor-widgets' ),
						'default' => __( 'Thank you — your request has been received. A member of our team will contact you soon.', 'xlights-elementor-widgets' ),
					),
					'privacy_policy_url'  => array(
						'type'        => 'url',
						'label'       => __( 'Privacy Policy URL', 'xlights-elementor-widgets' ),
						'default'     => '',
						'description' => __( 'Leave blank to use the WordPress Privacy Policy page.', 'xlights-elementor-widgets' ),
						'approval'    => true,
					),
					'privacy_ack_text'    => array(
						'type'    => 'textarea',
						'label'   => __( 'Privacy acknowledgment text', 'xlights-elementor-widgets' ),
						'default' => __( 'I agree to be contacted about this request and understand my information will be handled as described in the Privacy Policy.', 'xlights-elementor-widgets' ),
					),
					'default_state'       => array(
						'type'    => 'text',
						'label'   => __( 'Default state in the form', 'xlights-elementor-widgets' ),
						'default' => 'CA',
					),
					'upload_max_files'    => array(
						'type'    => 'number',
						'label'   => __( 'Maximum photos per request', 'xlights-elementor-widgets' ),
						'default' => 6,
						'min'     => 0,
						'max'     => 20,
					),
					'upload_max_mb'       => array(
						'type'    => 'number',
						'label'   => __( 'Maximum size per photo (MB)', 'xlights-elementor-widgets' ),
						'default' => 10,
						'min'     => 1,
						'max'     => 50,
					),
					'rate_limit_max'      => array(
						'type'    => 'number',
						'label'   => __( 'Submissions allowed per visitor per window', 'xlights-elementor-widgets' ),
						'default' => 5,
						'min'     => 1,
						'max'     => 100,
					),
					'rate_limit_window'   => array(
						'type'    => 'number',
						'label'   => __( 'Rate-limit window (minutes)', 'xlights-elementor-widgets' ),
						'default' => 30,
						'min'     => 1,
						'max'     => 1440,
					),
					'trust_proxy'         => array(
						'type'        => 'checkbox',
						'label'       => __( 'Trust proxy headers (CF-Connecting-IP / X-Forwarded-For) for rate limiting', 'xlights-elementor-widgets' ),
						'default'     => 0,
						'description' => __( 'Enable only when the site is behind Cloudflare or a trusted reverse proxy.', 'xlights-elementor-widgets' ),
					),
					'retention_days'      => array(
						'type'        => 'number',
						'label'       => __( 'Delete leads and their photos after (days)', 'xlights-elementor-widgets' ),
						'default'     => 0,
						'min'         => 0,
						'max'         => 3650,
						'description' => __( '0 keeps leads until deleted manually. Confirm the retention period with the client.', 'xlights-elementor-widgets' ),
						'approval'    => true,
					),
					'turnstile_site_key'  => array(
						'type'        => 'text',
						'label'       => __( 'Cloudflare Turnstile site key (optional)', 'xlights-elementor-widgets' ),
						'default'     => '',
						'description' => __( 'Adds a privacy-friendly bot check when both keys are set.', 'xlights-elementor-widgets' ),
					),
					'turnstile_secret'    => array(
						'type'    => 'secret',
						'label'   => __( 'Cloudflare Turnstile secret key', 'xlights-elementor-widgets' ),
						'default' => '',
					),
					'webhook_url'         => array(
						'type'        => 'url',
						'label'       => __( 'CRM webhook URL (optional)', 'xlights-elementor-widgets' ),
						'default'     => '',
						'https_only'  => true,
						'description' => __( 'Receives a JSON copy of each lead (no photo files). HTTPS only.', 'xlights-elementor-widgets' ),
					),
					'webhook_secret'      => array(
						'type'        => 'secret',
						'label'       => __( 'Webhook signing secret', 'xlights-elementor-widgets' ),
						'default'     => '',
						'description' => __( 'Used to sign the X-XLights-Signature header (HMAC-SHA256).', 'xlights-elementor-widgets' ),
					),
				),
			),
			'trust'     => array(
				'label'  => __( 'Credentials & Financing', 'xlights-elementor-widgets' ),
				'intro'  => __( 'Credential and financing content stays hidden on the live site until it is marked as approved. Editors still see it in Elementor with a "pending approval" badge.', 'xlights-elementor-widgets' ),
				'fields' => array(
					'trust_statement'      => array(
						'type'     => 'text',
						'label'    => __( 'Licensing / insurance statement', 'xlights-elementor-widgets' ),
						'default'  => __( 'Locally owned, licensed and insured.', 'xlights-elementor-widgets' ),
						'approval' => true,
					),
					'license_number'       => array(
						'type'     => 'text',
						'label'    => __( 'License number', 'xlights-elementor-widgets' ),
						'default'  => '',
						'approval' => true,
					),
					'trust_verified'       => array(
						'type'        => 'checkbox',
						'label'       => __( 'Credentials verified — publish licensing/insurance statements', 'xlights-elementor-widgets' ),
						'default'     => 0,
						'description' => __( 'Tick only after the dealer has confirmed the wording and license details.', 'xlights-elementor-widgets' ),
					),
					'financing_enabled'    => array(
						'type'        => 'checkbox',
						'label'       => __( 'Financing approved — publish financing callouts', 'xlights-elementor-widgets' ),
						'default'     => 0,
						'description' => __( 'Never publish rates, monthly payments or approval guarantees unless supplied by the lender.', 'xlights-elementor-widgets' ),
					),
					'financing_heading'    => array(
						'type'    => 'text',
						'label'   => __( 'Financing heading', 'xlights-elementor-widgets' ),
						'default' => __( 'Financing is available', 'xlights-elementor-widgets' ),
					),
					'financing_text'       => array(
						'type'    => 'textarea',
						'label'   => __( 'Financing text', 'xlights-elementor-widgets' ),
						'default' => __( 'Ask about financing options during your consultation. Our team can walk you through what is currently available for your project.', 'xlights-elementor-widgets' ),
					),
					'financing_cta_label'  => array(
						'type'    => 'text',
						'label'   => __( 'Financing button label', 'xlights-elementor-widgets' ),
						'default' => __( 'Ask About Financing', 'xlights-elementor-widgets' ),
					),
					'financing_url'        => array(
						'type'    => 'text',
						'label'   => __( 'Financing button URL', 'xlights-elementor-widgets' ),
						'default' => '{page:contact}#quote',
					),
					'financing_partner'    => array(
						'type'     => 'text',
						'label'    => __( 'Financing partner name (optional)', 'xlights-elementor-widgets' ),
						'default'  => '',
						'approval' => true,
					),
					'financing_disclaimer' => array(
						'type'     => 'textarea',
						'label'    => __( 'Financing disclaimer', 'xlights-elementor-widgets' ),
						'default'  => __( 'Financing is subject to credit approval. Terms and availability are set by the financing provider.', 'xlights-elementor-widgets' ),
						'approval' => true,
					),
				),
			),
			'brand'     => array(
				'label'  => __( 'Brand', 'xlights-elementor-widgets' ),
				'intro'  => __( 'Logos and favicon. Colors and fonts are under the Colors & Fonts tab.', 'xlights-elementor-widgets' ),
				'fields' => array(
					'logo'          => array(
						'type'        => 'media',
						'label'       => __( 'Logo (for light backgrounds)', 'xlights-elementor-widgets' ),
						'default'     => 0,
						'description' => __( 'SVG or PNG. If empty, the official X LIGHTS logo bundled with the plugin is used.', 'xlights-elementor-widgets' ),
						'approval'    => true,
					),
					'logo_light'    => array(
						'type'        => 'media',
						'label'       => __( 'Logo (for dark backgrounds)', 'xlights-elementor-widgets' ),
						'default'     => 0,
						'description' => __( 'White or light version. If empty, the bundled white X LIGHTS logo is used.', 'xlights-elementor-widgets' ),
					),
					'logo_show_market' => array(
						'type'        => 'checkbox',
						'label'       => __( 'Show the market name next to the logo', 'xlights-elementor-widgets' ),
						'default'     => 1,
						'description' => __( 'Dealer lockup, e.g. the X LIGHTS logo followed by "Southern California". Confirm with X LIGHTS that dealers may use this lockup.', 'xlights-elementor-widgets' ),
					),
					'favicon'       => array(
						'type'        => 'media',
						'label'       => __( 'Favicon', 'xlights-elementor-widgets' ),
						'default'     => 0,
						'description' => __( 'Used only when no Site Icon is set under Appearance → Customize.', 'xlights-elementor-widgets' ),
					),
				),
			),
			'design'    => array(
				'label'  => __( 'Colors & Fonts', 'xlights-elementor-widgets' ),
				'intro'  => __( 'One place for the look of every X LIGHTS widget, the header and the footer. Pick five colors; hover shades, tints, text-safe accents and on-dark variants are derived automatically and checked for readable contrast. Defaults follow the live X LIGHTS website. Individual widgets can still override these under Style in Elementor.', 'xlights-elementor-widgets' ),
				'fields' => array(
					'color_accent'  => array(
						'type'        => 'color',
						'group'       => __( 'Colors', 'xlights-elementor-widgets' ),
						'label'       => __( 'Accent', 'xlights-elementor-widgets' ),
						'default'     => '#3367FE',
						'description' => __( 'Links, active states, highlights and button hover. A darker shade is used automatically wherever the accent is small text.', 'xlights-elementor-widgets' ),
					),
					'color_ink'     => array(
						'type'        => 'color',
						'group'       => __( 'Colors', 'xlights-elementor-widgets' ),
						'label'       => __( 'Headings & buttons', 'xlights-elementor-widgets' ),
						'default'     => '#05070B',
						'description' => __( 'Headlines and primary buttons on light backgrounds (button text is white).', 'xlights-elementor-widgets' ),
					),
					'color_text'    => array(
						'type'        => 'color',
						'group'       => __( 'Colors', 'xlights-elementor-widgets' ),
						'label'       => __( 'Body text', 'xlights-elementor-widgets' ),
						'default'     => '#1B2130',
						'description' => __( 'Paragraphs and form fields. Secondary (muted) text is derived from it.', 'xlights-elementor-widgets' ),
					),
					'color_dark'    => array(
						'type'        => 'color',
						'group'       => __( 'Colors', 'xlights-elementor-widgets' ),
						'label'       => __( 'Dark sections', 'xlights-elementor-widgets' ),
						'default'     => '#131C2D',
						'description' => __( 'Dark (navy) sections, the footer, panels and dropdown menus. Text on it is white.', 'xlights-elementor-widgets' ),
					),
					'color_surface' => array(
						'type'        => 'color',
						'group'       => __( 'Colors', 'xlights-elementor-widgets' ),
						'label'       => __( 'Light grey sections', 'xlights-elementor-widgets' ),
						'default'     => '#E9EAED',
						'description' => __( 'Sections set to "Light grey" and light panels.', 'xlights-elementor-widgets' ),
					),
					'font_heading'  => array(
						'type'        => 'font',
						'role'        => 'heading',
						'group'       => __( 'Fonts', 'xlights-elementor-widgets' ),
						'label'       => __( 'Heading font', 'xlights-elementor-widgets' ),
						'default'     => '',
						'description' => __( 'Headlines, card titles and the footer menu. Google Fonts are loaded through Elementor; to keep them on your own server turn on Elementor → Settings → Performance → Load Google Fonts Locally.', 'xlights-elementor-widgets' ),
					),
					'font_body'     => array(
						'type'        => 'font',
						'role'        => 'body',
						'group'       => __( 'Fonts', 'xlights-elementor-widgets' ),
						'label'       => __( 'Body font', 'xlights-elementor-widgets' ),
						'default'     => '',
						'description' => __( 'Paragraphs, navigation, buttons, labels and forms.', 'xlights-elementor-widgets' ),
					),
					'heading_case'  => array(
						'type'    => 'select',
						'group'   => __( 'Fonts', 'xlights-elementor-widgets' ),
						'label'   => __( 'Heading letter case', 'xlights-elementor-widgets' ),
						'options' => array(
							'uppercase' => __( 'UPPERCASE (X LIGHTS style)', 'xlights-elementor-widgets' ),
							'none'      => __( 'As typed', 'xlights-elementor-widgets' ),
						),
						'default' => 'uppercase',
					),
					'heading_scale' => array(
						'type'        => 'number',
						'group'       => __( 'Fonts', 'xlights-elementor-widgets' ),
						'label'       => __( 'Heading size (%)', 'xlights-elementor-widgets' ),
						'default'     => 100,
						'min'         => 70,
						'max'         => 130,
						'step'        => 5,
						'description' => __( '100 = X LIGHTS default. Barlow Condensed is narrow; wider fonts usually look best at 80–90.', 'xlights-elementor-widgets' ),
					),
				),
			),
			'links'     => array(
				'label'  => __( 'App & Social', 'xlights-elementor-widgets' ),
				'intro'  => __( 'Leave a field blank to hide it. Do not enter placeholder URLs.', 'xlights-elementor-widgets' ),
				'fields' => array(
					'app_store_url'      => array(
						'type'     => 'url',
						'label'    => __( 'Apple App Store URL', 'xlights-elementor-widgets' ),
						'default'  => '',
						'approval' => true,
					),
					'google_play_url'    => array(
						'type'     => 'url',
						'label'    => __( 'Google Play URL', 'xlights-elementor-widgets' ),
						'default'  => '',
						'approval' => true,
					),
					'google_reviews_url' => array(
						'type'        => 'url',
						'label'       => __( 'Google Reviews URL', 'xlights-elementor-widgets' ),
						'default'     => '',
						'description' => __( 'Link to the dealer\'s Google Business Profile reviews.', 'xlights-elementor-widgets' ),
					),
					'facebook_url'       => array(
						'type'    => 'url',
						'label'   => 'Facebook',
						'default' => '',
					),
					'instagram_url'      => array(
						'type'    => 'url',
						'label'   => 'Instagram',
						'default' => '',
					),
					'youtube_url'        => array(
						'type'    => 'url',
						'label'   => 'YouTube',
						'default' => '',
					),
					'tiktok_url'         => array(
						'type'    => 'url',
						'label'   => 'TikTok',
						'default' => '',
					),
					'linkedin_url'       => array(
						'type'    => 'url',
						'label'   => 'LinkedIn',
						'default' => '',
					),
					'pinterest_url'      => array(
						'type'    => 'url',
						'label'   => 'Pinterest',
						'default' => '',
					),
					'houzz_url'          => array(
						'type'    => 'url',
						'label'   => 'Houzz',
						'default' => '',
					),
					'nextdoor_url'       => array(
						'type'    => 'url',
						'label'   => 'Nextdoor',
						'default' => '',
					),
				),
			),
			'map'       => array(
				'label'  => __( 'Map & SEO', 'xlights-elementor-widgets' ),
				'fields' => array(
					'map_enabled'    => array(
						'type'        => 'checkbox',
						'label'       => __( 'Offer an interactive map in service-area sections', 'xlights-elementor-widgets' ),
						'default'     => 1,
						'description' => __( 'The Google map loads only after the visitor clicks "Show map" (no third-party requests on page load).', 'xlights-elementor-widgets' ),
					),
					'map_query'      => array(
						'type'    => 'text',
						'label'   => __( 'Map location query', 'xlights-elementor-widgets' ),
						'default' => 'San Clemente, CA',
					),
					'map_embed_url'  => array(
						'type'        => 'url',
						'label'       => __( 'Custom map embed URL (optional)', 'xlights-elementor-widgets' ),
						'default'     => '',
						'description' => __( 'Paste a Google My Maps embed URL to show a drawn service-area boundary.', 'xlights-elementor-widgets' ),
					),
					'schema_enabled' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Output LocalBusiness / Service structured data', 'xlights-elementor-widgets' ),
						'default' => 1,
					),
					'business_type'  => array(
						'type'    => 'select',
						'label'   => __( 'Schema business type', 'xlights-elementor-widgets' ),
						'default' => 'HomeAndConstructionBusiness',
						'options' => array(
							'HomeAndConstructionBusiness' => 'HomeAndConstructionBusiness',
							'Electrician'                 => 'Electrician',
							'GeneralContractor'           => 'GeneralContractor',
							'LocalBusiness'               => 'LocalBusiness',
						),
					),
					'price_range'    => array(
						'type'    => 'text',
						'label'   => __( 'Price range (schema, optional)', 'xlights-elementor-widgets' ),
						'default' => '',
					),
					'geo_lat'        => array(
						'type'    => 'text',
						'label'   => __( 'Latitude (optional)', 'xlights-elementor-widgets' ),
						'default' => '',
					),
					'geo_lng'        => array(
						'type'    => 'text',
						'label'   => __( 'Longitude (optional)', 'xlights-elementor-widgets' ),
						'default' => '',
					),
					'meta_fallback'  => array(
						'type'        => 'checkbox',
						'label'       => __( 'Output meta descriptions when no SEO plugin is active', 'xlights-elementor-widgets' ),
						'default'     => 1,
						'description' => __( 'Automatically disabled when Yoast SEO, Rank Math, SEOPress, All in One SEO or The SEO Framework is active.', 'xlights-elementor-widgets' ),
					),
					'breadcrumbs'    => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show breadcrumbs in inner-page heroes', 'xlights-elementor-widgets' ),
						'default' => 1,
					),
				),
			),
			'layout'    => array(
				'label'  => __( 'Header & Footer', 'xlights-elementor-widgets' ),
				'fields' => array(
					'header_footer_mode' => array(
						'type'        => 'select',
						'label'       => __( 'Global header & footer', 'xlights-elementor-widgets' ),
						'default'     => 'canvas',
						'options'     => array(
							'canvas' => __( 'Elementor Canvas pages (works with any theme)', 'xlights-elementor-widgets' ),
							'global' => __( 'Every page (replace the theme header/footer)', 'xlights-elementor-widgets' ),
							'off'    => __( 'Off (Elementor Pro Theme Builder or manual widgets)', 'xlights-elementor-widgets' ),
						),
						'description' => __( '"Every page" works best with the Hello Elementor theme. For other themes, list the theme header/footer selectors below so they are hidden.', 'xlights-elementor-widgets' ),
					),
					'hide_theme_selectors' => array(
						'type'        => 'text',
						'label'       => __( 'Theme header/footer selectors to hide', 'xlights-elementor-widgets' ),
						'default'     => '',
						'placeholder' => '.site-header, .site-footer',
					),
					'header_sticky'      => array(
						'type'    => 'checkbox',
						'label'   => __( 'Sticky header', 'xlights-elementor-widgets' ),
						'default' => 1,
					),
					'header_transparent' => array(
						'type'        => 'checkbox',
						'label'       => __( 'Header overlaps the first hero (floats inside framed heroes, transparent over full-bleed ones)', 'xlights-elementor-widgets' ),
						'default'     => 1,
					),
					'header_show_phone'  => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show phone number in the header', 'xlights-elementor-widgets' ),
						'default' => 1,
					),
					'footer_note'        => array(
						'type'        => 'textarea',
						'label'       => __( 'Footer note (optional)', 'xlights-elementor-widgets' ),
						'default'     => '',
						'description' => __( 'Small print below the footer links, e.g. an independent-dealer statement.', 'xlights-elementor-widgets' ),
					),
				),
			),
		);
	}

	/**
	 * Flat map of field key => field definition.
	 *
	 * @return array
	 */
	public static function fields() {
		static $fields = null;
		if ( null === $fields ) {
			$fields = array();
			foreach ( self::schema() as $tab ) {
				foreach ( $tab['fields'] as $key => $field ) {
					$fields[ $key ] = $field;
				}
			}
		}
		return $fields;
	}

	/**
	 * @return array
	 */
	public static function defaults() {
		$defaults = array();
		foreach ( self::fields() as $key => $field ) {
			$defaults[ $key ] = $field['default'];
		}
		return $defaults;
	}

	/**
	 * All settings merged with defaults.
	 *
	 * @return array
	 */
	public static function all() {
		if ( null === self::$cache ) {
			$stored      = get_option( self::OPTION, array() );
			self::$cache = wp_parse_args( is_array( $stored ) ? $stored : array(), self::defaults() );
		}
		/**
		 * Filter the resolved dealer settings.
		 *
		 * @param array $settings
		 */
		return apply_filters( 'xlights/settings', self::$cache );
	}

	/**
	 * @param string $key     Setting key.
	 * @param mixed  $default Fallback.
	 * @return mixed
	 */
	public static function get( $key, $default = null ) {
		$all = self::all();
		return array_key_exists( $key, $all ) ? $all[ $key ] : $default;
	}

	/**
	 * @param string $key Setting key.
	 * @return bool
	 */
	public static function enabled( $key ) {
		return ! empty( self::get( $key ) );
	}

	/**
	 * Lines setting as a clean array.
	 *
	 * @param string $key Setting key.
	 * @return string[]
	 */
	public static function lines( $key ) {
		$value = (string) self::get( $key, '' );
		return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $value ) ) ) );
	}

	public static function flush_cache() {
		self::$cache = null;
	}

	/**
	 * Merge a partial array into the stored settings (used by setup and CLI).
	 *
	 * @param array $values Raw values.
	 */
	public static function update( array $values ) {
		$current = get_option( self::OPTION, array() );
		$current = is_array( $current ) ? $current : array();
		update_option( self::OPTION, self::sanitize( array_merge( $current, $values ), false ) );
		self::flush_cache();
	}

	/**
	 * Sanitize a full settings payload.
	 *
	 * @param mixed $input        Raw input.
	 * @param bool  $from_form    True when coming from the settings form (unchecked boxes are absent).
	 * @return array
	 */
	public static function sanitize( $input, $from_form = true ) {
		$input   = is_array( $input ) ? $input : array();
		$current = get_option( self::OPTION, array() );
		$current = is_array( $current ) ? $current : array();
		$clean   = array();

		foreach ( self::fields() as $key => $field ) {
			$has = array_key_exists( $key, $input );

			if ( 'checkbox' === $field['type'] ) {
				if ( $has ) {
					$clean[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
				} elseif ( $from_form ) {
					$clean[ $key ] = 0;
				} else {
					$clean[ $key ] = isset( $current[ $key ] ) ? (int) $current[ $key ] : (int) $field['default'];
				}
				continue;
			}

			if ( ! $has ) {
				$clean[ $key ] = array_key_exists( $key, $current ) ? $current[ $key ] : $field['default'];
				continue;
			}

			// Secret fields keep the stored value when the form submits the mask or an empty value.
			if ( 'secret' === $field['type'] && ( '' === $input[ $key ] || '********' === $input[ $key ] ) && $from_form ) {
				$clean[ $key ] = isset( $current[ $key ] ) ? $current[ $key ] : '';
				continue;
			}

			if ( 'font' === $field['type'] && '__custom' === $input[ $key ] ) {
				$input[ $key ] = isset( $input[ $key . '_custom' ] ) ? $input[ $key . '_custom' ] : '';
			}

			$clean[ $key ] = self::sanitize_value( $input[ $key ], $field );
		}

		self::flush_cache();
		return $clean;
	}

	/**
	 * @param mixed $value Raw value.
	 * @param array $field Field definition.
	 * @return mixed
	 */
	public static function sanitize_value( $value, array $field ) {
		$value = is_scalar( $value ) ? (string) wp_unslash( $value ) : '';

		switch ( $field['type'] ) {
			case 'textarea':
			case 'lines':
				return sanitize_textarea_field( $value );
			case 'email':
				return sanitize_email( $value );
			case 'emails':
				$emails = array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', $value ) ) ), 'is_email' );
				return implode( ', ', array_unique( $emails ) );
			case 'url':
				$url = esc_url_raw( trim( $value ), array( 'http', 'https' ) );
				if ( ! empty( $field['https_only'] ) && 0 !== strpos( $url, 'https://' ) ) {
					return '';
				}
				return $url;
			case 'tel':
				return preg_replace( '/[^0-9+().\-\s]/', '', $value );
			case 'color':
				$color = sanitize_hex_color( $value );
				return $color ? $color : $field['default'];
			case 'media':
				return absint( $value );
			case 'number':
				$num = (int) $value;
				if ( isset( $field['min'] ) ) {
					$num = max( (int) $field['min'], $num );
				}
				if ( isset( $field['max'] ) ) {
					$num = min( (int) $field['max'], $num );
				}
				return $num;
			case 'select':
				return isset( $field['options'][ $value ] ) ? $value : $field['default'];
			case 'font':
				return \XLights\Frontend\Typography::sanitize( $value );
			case 'secret':
				return trim( sanitize_text_field( $value ) );
			case 'text':
			default:
				return sanitize_text_field( $value );
		}
	}
}
