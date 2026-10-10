<?php
/**
 * Inline SVG icon set (24×24 stroke icons, currentColor). Inline icons avoid an
 * icon-font request and inherit color/size from CSS.
 *
 * @package XLights
 */

namespace XLights\Helpers;

defined( 'ABSPATH' ) || exit;

final class Icons {

	/**
	 * Stroke icons: name => inner SVG markup.
	 *
	 * @return array
	 */
	private static function stroke() {
		return array(
			'arrow-right'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
			'arrow-left'     => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
			'arrow-up-right' => '<path d="M7 17 17 7M8 7h9v9"/>',
			'chevron-down'   => '<path d="m6 9 6 6 6-6"/>',
			'chevron-left'   => '<path d="m15 18-6-6 6-6"/>',
			'chevron-right'  => '<path d="m9 18 6-6-6-6"/>',
			'menu'           => '<path d="M4 7h16M4 12h16M4 17h16"/>',
			'close'          => '<path d="M6 6l12 12M18 6 6 18"/>',
			'phone'          => '<path d="M5 4h3.5l1.6 4.2-2.2 1.4a11 11 0 0 0 6.5 6.5l1.4-2.2L20 15.5V19a1.5 1.5 0 0 1-1.6 1.5A16.5 16.5 0 0 1 3.5 5.6 1.5 1.5 0 0 1 5 4z"/>',
			'mail'           => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6.5 8.5-6.5"/>',
			'map-pin'        => '<path d="M12 21s-7-6.1-7-11.5a7 7 0 0 1 14 0C19 14.9 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
			'clock'          => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
			'check'          => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
			'check-circle'   => '<circle cx="12" cy="12" r="9"/><path d="m8 12.3 2.8 2.8L16.3 9.5"/>',
			'plus'           => '<path d="M12 5v14M5 12h14"/>',
			'minus'          => '<path d="M5 12h14"/>',
			'info'           => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 7.6v.1"/>',
			'alert'          => '<path d="M12 3.5 2.8 19.5h18.4z"/><path d="M12 10v4M12 16.8v.1"/>',
			'quote'          => '<path d="M9.5 7C6.5 8 5 10.3 5 13.5V17h4.5v-4.5H7.3c.2-1.9 1.1-3.1 2.7-3.8zM18.5 7c-3 1-4.5 3.3-4.5 6.5V17h4.5v-4.5h-2.2c.2-1.9 1.1-3.1 2.7-3.8z"/>',
			'home'           => '<path d="M3.5 11 12 4l8.5 7"/><path d="M5.5 9.5V20h13V9.5"/><path d="M10 20v-5.5h4V20"/>',
			'roofline'       => '<path d="M2.5 13 12 5.5l9.5 7.5"/><circle cx="5.5" cy="13.6" r=".9"/><circle cx="9" cy="10.9" r=".9"/><circle cx="12" cy="8.6" r=".9"/><circle cx="15" cy="10.9" r=".9"/><circle cx="18.5" cy="13.6" r=".9"/><path d="M5 15v4.5h14V15"/>',
			'building'       => '<path d="M4 20.5V5.5L13 3v17.5M13 9h7v11.5M2.5 20.5h19"/><path d="M7 8h2.5M7 11.5h2.5M7 15h2.5M16 12.5h1.5M16 16h1.5"/>',
			'store'          => '<path d="M3.5 9.5 5 4.5h14l1.5 5"/><path d="M3.5 9.5a2.8 2.8 0 0 0 5.6 0 2.9 2.9 0 0 0 5.8 0 2.8 2.8 0 0 0 5.6 0"/><path d="M5 12v8.5h14V12M10 20.5v-5h4v5"/>',
			'utensils'       => '<path d="M6 3v7a2 2 0 0 0 2 2v9M10 3v7a2 2 0 0 1-2 2M8 3v5"/><path d="M17.5 21V3c-2 1.3-3 4-3 7.5h3"/>',
			'car'            => '<path d="M4 16.5V12l2.2-5h11.6L20 12v4.5"/><path d="M3 16.5h18v2H3zM4 12h16"/><circle cx="7.5" cy="14.3" r=".8"/><circle cx="16.5" cy="14.3" r=".8"/>',
			'bed'            => '<path d="M3 19V6M3 15.5h18V19M21 15.5V12a3 3 0 0 0-3-3h-7v6.5"/><circle cx="7" cy="11.5" r="1.8"/>',
			'sun'            => '<circle cx="12" cy="12" r="4"/><path d="M12 2.5v2M12 19.5v2M4.6 4.6 6 6M18 18l1.4 1.4M2.5 12h2M19.5 12h2M4.6 19.4 6 18M18 6l1.4-1.4"/>',
			'moon'           => '<path d="M20 14.5A8 8 0 1 1 9.5 4 6.5 6.5 0 0 0 20 14.5z"/>',
			'sparkles'       => '<path d="M11 3.5 12.6 8l4.4 1.6-4.4 1.6L11 15.7l-1.6-4.5L5 9.6 9.4 8z"/><path d="M18 14l.8 2.2 2.2.8-2.2.8L18 20l-.8-2.2-2.2-.8 2.2-.8z"/>',
			'palette'        => '<path d="M12 3a9 9 0 1 0 0 18c1.2 0 1.8-.8 1.8-1.7 0-1.2-1-1.5-1-2.6 0-.9.7-1.6 1.7-1.6H17a4 4 0 0 0 4-4C21 6.6 17 3 12 3z"/><circle cx="7.5" cy="11" r="1.1"/><circle cx="10" cy="7" r="1.1"/><circle cx="15" cy="7.2" r="1.1"/>',
			'sliders'        => '<path d="M5 4v6M5 14v6M12 4v3M12 11v9M19 4v9M19 17v3"/><path d="M3 12h4M10 9h4M17 15h4"/>',
			'calendar'       => '<rect x="3.5" y="5" width="17" height="15.5" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
			'music'          => '<path d="M9 18V5l11-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="17" cy="16" r="3"/>',
			'timer'          => '<circle cx="12" cy="13.5" r="7.5"/><path d="M12 13.5V9.5M9.5 2.5h5M18.5 6.5l1.5-1.5"/>',
			'smartphone'     => '<rect x="6.5" y="2.5" width="11" height="19" rx="2.3"/><path d="M11 18.5h2"/>',
			'wifi'           => '<path d="M2.5 9a14 14 0 0 1 19 0M5.5 12.3a9.5 9.5 0 0 1 13 0M8.6 15.6a5 5 0 0 1 6.8 0"/><circle cx="12" cy="18.8" r=".9"/>',
			'bluetooth'      => '<path d="m7 7.5 10 9-5 4.5v-18l5 4.5-10 9"/>',
			'users'          => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M15.5 4.7a3.5 3.5 0 0 1 0 6.6M17.5 14.3a6.5 6.5 0 0 1 4 5.7"/>',
			'user'           => '<circle cx="12" cy="8" r="4"/><path d="M4.5 20.5a7.5 7.5 0 0 1 15 0"/>',
			'share'          => '<circle cx="18" cy="5.5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="18.5" r="2.5"/><path d="m8.2 10.8 7.6-4.1M8.2 13.2l7.6 4.1"/>',
			'shield'         => '<path d="M12 3 4.5 6v5.5c0 4.6 3.1 8.2 7.5 9.5 4.4-1.3 7.5-4.9 7.5-9.5V6z"/>',
			'shield-check'   => '<path d="M12 3 4.5 6v5.5c0 4.6 3.1 8.2 7.5 9.5 4.4-1.3 7.5-4.9 7.5-9.5V6z"/><path d="m8.7 12 2.3 2.3 4.4-4.4"/>',
			'award'          => '<circle cx="12" cy="9" r="5.5"/><path d="m8.6 13.3-1.6 7.2 5-2.5 5 2.5-1.6-7.2"/>',
			'bolt'           => '<path d="M13 2.5 4.5 13.5H12L11 21.5l8.5-11H12z"/>',
			'droplet'        => '<path d="M12 3s6.5 7 6.5 11.5a6.5 6.5 0 0 1-13 0C5.5 10 12 3 12 3z"/>',
			'layers'         => '<path d="m12 3 9 5-9 5-9-5z"/><path d="m3 12.5 9 5 9-5M3 16.5l9 5 9-5"/>',
			'leaf'           => '<path d="M5 19c0-8 5-13.5 15-14-.4 9.5-6 14-13.5 14H5z"/><path d="M5 19c3.5-4 6.5-6.5 10-8.5"/>',
			'tree'           => '<path d="M12 21.5v-5M12 2.5c-3.9 0-6.5 3-6.5 6.4 0 1 .2 1.7.6 2.4A3.8 3.8 0 0 0 9 16.5h6a3.8 3.8 0 0 0 2.9-5.2c.4-.7.6-1.4.6-2.4 0-3.4-2.6-6.4-6.5-6.4z"/>',
			'path'           => '<path d="M9 21c0-4 3-5 3-9s-3-5-3-9M15 21c0-4 3-5 3-9s-3-5-3-9" opacity=".55"/><circle cx="5" cy="7" r="1.3"/><circle cx="5" cy="15" r="1.3"/><path d="M5 8.3V10M5 16.3V18"/>',
			'lamp'           => '<path d="M8 3.5h8l2 5H6z"/><path d="M12 8.5v12.5M8.5 21h7"/>',
			'lightbulb'      => '<path d="M9 18h6M10 21h4M12 3a6 6 0 0 0-3.6 10.8c.7.6 1.1 1.3 1.1 2.2h5c0-.9.4-1.6 1.1-2.2A6 6 0 0 0 12 3z"/>',
			'spotlight'      => '<path d="m4 20 5.5-5.5M7.5 12.5l4 4"/><path d="m9.5 10.5 4 4 6-9.5-.5-.5z" opacity=".9"/><path d="M15.5 4.5l1.2-1.6M19.5 8.5l1.6-1.2"/>',
			'eye'            => '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/>',
			'eye-off'        => '<path d="M4 4l16 16M9.9 6A9.8 9.8 0 0 1 12 5.5c6 0 9.5 6.5 9.5 6.5a17 17 0 0 1-2.6 3.4M6.3 7.5C3.9 9.2 2.5 12 2.5 12S6 18.5 12 18.5c1.5 0 2.8-.4 4-1M10 10.2a3 3 0 0 0 4 4"/>',
			'ruler'          => '<path d="m3.5 16.5 13-13 4 4-13 13z"/><path d="m7 13 1.8 1.8M9.8 10.2l1.8 1.8M12.6 7.4l1.8 1.8"/>',
			'pencil'         => '<path d="M4 20h4.5L19.5 9 15 4.5 4 15.5z"/><path d="m13 6.5 4.5 4.5"/>',
			'wrench'         => '<path d="M14.5 6.5a4 4 0 0 0 5 5L21 13l-8 8-2.5-2.5 1.5-1.5a4 4 0 0 0-5-5L5.5 13.5 3 11l8-8 1.5 1.5a4 4 0 0 0 2 2z"/>',
			'heart'          => '<path d="M12 20.5S3.5 15.4 3.5 9.3A4.8 4.8 0 0 1 12 6.6a4.8 4.8 0 0 1 8.5 2.7c0 6.1-8.5 11.2-8.5 11.2z"/>',
			'gift'           => '<rect x="3.5" y="8.5" width="17" height="4" rx="1"/><path d="M5 12.5v8h14v-8M12 8.5v12M12 8.5S11 4 8.3 4.3c-2.4.2-1.6 4.2 3.7 4.2zM12 8.5S13 4 15.7 4.3c2.4.2 1.6 4.2-3.7 4.2z"/>',
			'cake'           => '<path d="M4 20.5h16v-7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2z"/><path d="M4 15.5c1.3 1 2.7 1 4 0s2.7-1 4 0 2.7 1 4 0 2.7-1 4 0M12 11.5v-4M12 4.5v-.1"/>',
			'flag'           => '<path d="M5 21V4M5 4.5c5-3 9 3 14 0v9c-5 3-9-3-14 0"/>',
			'camera'         => '<path d="M3.5 8.5A1.5 1.5 0 0 1 5 7h2.5L9 4.5h6L16.5 7H19a1.5 1.5 0 0 1 1.5 1.5v10A1.5 1.5 0 0 1 19 20H5a1.5 1.5 0 0 1-1.5-1.5z"/><circle cx="12" cy="13" r="3.5"/>',
			'image'          => '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><circle cx="9" cy="9.5" r="1.6"/><path d="m4 17.5 5-5 4 4 2.5-2.5 4.5 4.5"/>',
			'play'           => '<path d="M8 5.5v13l10-6.5z"/>',
			'link'           => '<path d="M10 14a4 4 0 0 0 5.7 0l3-3A4 4 0 0 0 13 5.3l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>',
			'globe'          => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.7 5.6 3.7 9S14.5 18.4 12 21c-2.5-2.6-3.7-5.6-3.7-9S9.5 5.6 12 3z"/>',
			'cloud'          => '<path d="M7 18.5a4.5 4.5 0 0 1-.7-8.9 6 6 0 0 1 11.5 1.4A3.8 3.8 0 0 1 17.5 18.5z"/>',
			'repeat'         => '<path d="M17 2.5l3 3-3 3"/><path d="M4 11V9.5a4 4 0 0 1 4-4h12M7 21.5l-3-3 3-3"/><path d="M20 13v1.5a4 4 0 0 1-4 4H4"/>',
			'toggle'         => '<rect x="2.5" y="7" width="19" height="10" rx="5"/><circle cx="16.5" cy="12" r="3"/>',
			'grid'           => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.2"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.2"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.2"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.2"/>',
			'compass'        => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5z"/>',
			'snowflake'      => '<path d="M12 2.5v19M3.8 7.2l16.4 9.6M3.8 16.8l16.4-9.6M9.5 4l2.5 2 2.5-2M9.5 20l2.5-2 2.5 2"/>',
			'lock'           => '<rect x="4.5" y="10.5" width="15" height="10" rx="2"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/>',
			'upload'         => '<path d="M12 15.5V4M7 8.5l5-5 5 5M4.5 15v3.5A2 2 0 0 0 6.5 20.5h11a2 2 0 0 0 2-2V15"/>',
			'external'       => '<path d="M14 4h6v6M20 4l-9 9M18 14v4.5A1.5 1.5 0 0 1 16.5 20h-11A1.5 1.5 0 0 1 4 18.5v-11A1.5 1.5 0 0 1 5.5 6H10"/>',
			'star'           => '<path d="m12 3.2 2.7 5.6 6.1.8-4.5 4.2 1.1 6.1L12 17l-5.4 2.9 1.1-6.1-4.5-4.2 6.1-.8z"/>',
			'zoom'           => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m15.5 15.5 5 5M10.5 7.5v6M7.5 10.5h6"/>',
			'compare'        => '<path d="M12 3v18"/><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m8 10-2 2 2 2M16 10l2 2-2 2"/>',
			'hub'            => '<circle cx="12" cy="12" r="3"/><circle cx="4.5" cy="5" r="1.8"/><circle cx="19.5" cy="5" r="1.8"/><circle cx="4.5" cy="19" r="1.8"/><circle cx="19.5" cy="19" r="1.8"/><path d="m6 6.4 3.8 3.5M18 6.4l-3.8 3.5M6 17.6l3.8-3.5M18 17.6l-3.8-3.5"/>',
			'stairs'         => '<path d="M3.5 20.5h4v-4h4v-4h4v-4h5"/><circle cx="6" cy="18.6" r=".7"/><circle cx="10" cy="14.6" r=".7"/><circle cx="14" cy="10.6" r=".7"/>',
			'string-lights'  => '<path d="M2.5 5c3 4 6 6 9.5 6s6.5-2 9.5-6"/><path d="M6 8.3v1.5M12 11v1.5M18 8.3v1.5"/><circle cx="6" cy="11.3" r="1.5"/><circle cx="12" cy="14" r="1.5"/><circle cx="18" cy="11.3" r="1.5"/>',
			'fence'          => '<path d="M4 20.5V7l2-2.5L8 7v13.5M10 20.5V7l2-2.5L14 7v13.5M16 20.5V7l2-2.5L20 7v13.5M2.5 10.5h19M2.5 16h19"/>',
			'water'          => '<path d="M2.5 15c1.6 0 1.6-1.2 3.2-1.2S7.3 15 9 15s1.6-1.2 3.2-1.2 1.6 1.2 3.2 1.2 1.6-1.2 3.2-1.2 1.6 1.2 2.9 1.2M2.5 19.5c1.6 0 1.6-1.2 3.2-1.2s1.6 1.2 3.3 1.2 1.6-1.2 3.2-1.2 1.6 1.2 3.2 1.2 1.6-1.2 3.2-1.2 1.6 1.2 2.9 1.2"/><path d="M12 3.5s3.5 3.8 3.5 6.2a3.5 3.5 0 0 1-7 0c0-2.4 3.5-6.2 3.5-6.2z"/>',
			'target'         => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.2"/>',
			'handshake'      => '<path d="m11 7-2-1.5L3 9v6l2.5 1.5M13 7l2-1.5L21 9v6l-3 2"/><path d="m7.5 13 3.5 3.5a1.4 1.4 0 0 0 2-2L10 11.5l2.5-2.5 6 5.5a1.4 1.4 0 0 1-2 2l-2-2M9.5 15l1.5 1.5"/>',
			'clipboard'      => '<rect x="5" y="4.5" width="14" height="16.5" rx="2"/><path d="M9 4.5V3h6v1.5M8.5 10h7M8.5 13.5h7M8.5 17h4.5"/>',
			'money'          => '<rect x="2.5" y="6" width="19" height="12" rx="2"/><circle cx="12" cy="12" r="2.8"/><path d="M6 9.5v5M18 9.5v5"/>',
			'thermometer'    => '<path d="M10 14.5V5a2 2 0 0 1 4 0v9.5a4 4 0 1 1-4 0z"/>',
			'paint'          => '<path d="M4 4.5h13v5H4z"/><path d="M17 7h2.5v5H11v3"/><rect x="9.5" y="15" width="3" height="6" rx="1"/>',
		);
	}

	/**
	 * Filled brand glyphs (social / stores).
	 *
	 * @return array
	 */
	private static function filled() {
		return array(
			'facebook'    => '<path d="M13.4 21v-7.6h2.6l.4-3h-3V8.5c0-.9.3-1.5 1.5-1.5h1.6V4.3a21 21 0 0 0-2.3-.1c-2.3 0-3.9 1.4-3.9 4v2.2H7.7v3h2.6V21z"/>',
			'instagram'   => '<path d="M12 7.3a4.7 4.7 0 1 0 0 9.4 4.7 4.7 0 0 0 0-9.4zm0 7.7a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm6-7.9a1.1 1.1 0 1 1-2.2 0 1.1 1.1 0 0 1 2.2 0zM21 8.2c-.1-1.6-.4-3-1.6-4.2S16.8 2.5 15.2 2.4C13.6 2.3 10.4 2.3 8.8 2.4 7.2 2.5 5.8 2.8 4.6 4S2.9 6.6 2.9 8.2c-.1 1.6-.1 6 0 7.6.1 1.6.4 3 1.6 4.2s2.6 1.5 4.2 1.6c1.6.1 6.4.1 8 0 1.6-.1 3-.4 4.2-1.6s1.5-2.6 1.6-4.2c.1-1.6.1-6 0-7.6zm-2 9.4a3.1 3.1 0 0 1-1.7 1.7c-1.2.5-4 .4-5.3.4s-4.1.1-5.3-.4a3.1 3.1 0 0 1-1.7-1.7c-.5-1.2-.4-4-.4-5.3s-.1-4.1.4-5.3A3.1 3.1 0 0 1 6.7 5.3c1.2-.5 4-.4 5.3-.4s4.1-.1 5.3.4A3.1 3.1 0 0 1 19 7c.5 1.2.4 4 .4 5.3s.1 4.1-.4 5.3z"/>',
			'youtube'     => '<path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4a2.5 2.5 0 0 0-1.8 1.8C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5.2 3z"/>',
			'tiktok'      => '<path d="M16.6 5.8A4.3 4.3 0 0 1 15.5 3h-3.1v12.4a2.6 2.6 0 1 1-1.8-2.5V9.8a5.7 5.7 0 1 0 4.9 5.6V9.1a7.4 7.4 0 0 0 4.3 1.4V7.4a4.3 4.3 0 0 1-3.2-1.6z"/>',
			'linkedin'    => '<path d="M6.9 21H3.3V9.4h3.6zM5.1 7.8a2.1 2.1 0 1 1 0-4.2 2.1 2.1 0 0 1 0 4.2zM21 21h-3.6v-5.6c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V21H9.8V9.4h3.4V11c.5-.9 1.7-1.9 3.4-1.9 3.7 0 4.4 2.4 4.4 5.6z"/>',
			'pinterest'   => '<path d="M12.3 2.5c-5.5 0-8.3 3.9-8.3 7.2 0 2 .8 3.8 2.4 4.4.3.1.5 0 .6-.3l.2-.9c.1-.3 0-.4-.2-.7-.5-.6-.8-1.3-.8-2.4 0-3.1 2.3-5.8 6-5.8 3.3 0 5.1 2 5.1 4.7 0 3.5-1.6 6.5-3.9 6.5-1.3 0-2.2-1-1.9-2.3.4-1.5 1.1-3.2 1.1-4.3 0-1-.5-1.8-1.6-1.8-1.3 0-2.3 1.3-2.3 3.1 0 1.1.4 1.9.4 1.9l-1.5 6.5c-.4 1.9-.1 4.2 0 4.4l.2.1c.1-.2 1.4-1.8 1.9-3.4l.7-2.9c.4.7 1.5 1.4 2.7 1.4 3.6 0 6-3.2 6-7.6 0-3.3-2.8-6.4-7-6.4z"/>',
			'houzz'       => '<path d="M5 3h4.3v4.6L19 10.3V21h-5.6v-5.3h-2.8V21H5z"/>',
			'nextdoor'    => '<path d="M12.3 4.5c-2.7 0-4.7 1.3-5.6 3.2V4.8H2.5v.1L4 7v12.5h4.3v-7.3c0-2 1.3-3.5 3.9-3.5s3.9 1.5 3.9 3.5v7.3h4.3v-7.6c0-4.6-3.2-7.4-8.1-7.4z"/>',
			'x'           => '<path d="M17.8 3h3.1l-6.8 7.7 8 10.3h-6.2l-4.9-6.3L5.4 21H2.3l7.2-8.2L1.8 3h6.4l4.4 5.8zm-1.1 16.2h1.7L7.4 4.7H5.6z"/>',
			'google'      => '<path d="M21.6 12.2c0-.7-.1-1.3-.2-2H12v3.8h5.4a4.6 4.6 0 0 1-2 3v2.5h3.2c1.9-1.7 3-4.3 3-7.3z" opacity=".9"/><path d="M12 22c2.7 0 5-.9 6.6-2.4l-3.2-2.5c-.9.6-2 1-3.4 1-2.6 0-4.8-1.8-5.6-4.1H3.1v2.6A10 10 0 0 0 12 22z" opacity=".75"/><path d="M6.4 14a6 6 0 0 1 0-3.9V7.5H3.1a10 10 0 0 0 0 9z" opacity=".6"/><path d="M12 5.9c1.5 0 2.8.5 3.8 1.5l2.9-2.9A10 10 0 0 0 3.1 7.5l3.3 2.6C7.2 7.7 9.4 5.9 12 5.9z" opacity=".85"/>',
			'apple'       => '<path d="M16.4 12.6c0-2.4 2-3.6 2.1-3.7a4.6 4.6 0 0 0-3.6-2c-1.5-.2-3 .9-3.8.9s-2-.9-3.3-.9a4.9 4.9 0 0 0-4.1 2.5c-1.8 3.1-.5 7.6 1.2 10.1.8 1.2 1.8 2.6 3.1 2.5 1.3-.1 1.7-.8 3.2-.8s1.9.8 3.2.8c1.3 0 2.2-1.2 3-2.4a10.6 10.6 0 0 0 1.4-2.8 4.3 4.3 0 0 1-2.4-4.2zM14 5.4a4.4 4.4 0 0 0 1-3.2 4.5 4.5 0 0 0-2.9 1.5 4.2 4.2 0 0 0-1.1 3.1A3.7 3.7 0 0 0 14 5.4z"/>',
			'google-play' => '<path d="M4 3.3c-.2.2-.3.6-.3 1v15.4c0 .4.1.8.3 1l8.7-8.7z"/><path d="M15.6 15.3 12.7 12.4 4 21.1c.3.3.8.3 1.4 0z" opacity=".8"/><path d="M15.6 8.9 5.4 3.1C4.8 2.8 4.3 2.8 4 3.1l8.7 8.7z" opacity=".65"/><path d="m19.5 11.1-3.9-2.2-2.9 2.9 2.9 2.9 3.9-2.2c1.1-.6 1.1-1.6 0-1.4z" opacity=".9"/>',
			'star-filled' => '<path d="m12 3.2 2.7 5.6 6.1.8-4.5 4.2 1.1 6.1L12 17l-5.4 2.9 1.1-6.1-4.5-4.2 6.1-.8z"/>',
		);
	}

	/**
	 * Icon names suitable for content controls.
	 *
	 * @return array name => label
	 */
	public static function options() {
		$names = array_keys( self::stroke() );
		$skip  = array( 'arrow-right', 'arrow-left', 'arrow-up-right', 'chevron-down', 'chevron-left', 'chevron-right', 'menu', 'close', 'minus', 'external' );
		$out   = array( '' => __( '— None —', 'xlights-elementor-widgets' ) );
		foreach ( $names as $name ) {
			if ( in_array( $name, $skip, true ) ) {
				continue;
			}
			$out[ $name ] = ucwords( str_replace( '-', ' ', $name ) );
		}
		return $out;
	}

	/**
	 * @param string $name  Icon name.
	 * @param string $class Extra class.
	 * @return string SVG markup (decorative, aria-hidden).
	 */
	public static function svg( $name, $class = '' ) {
		$stroke = self::stroke();
		$filled = self::filled();
		$cls    = trim( 'xl-icon xl-icon--' . sanitize_html_class( $name ) . ' ' . $class );
		if ( isset( $stroke[ $name ] ) ) {
			return '<svg class="' . esc_attr( $cls ) . '" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $stroke[ $name ] . '</svg>';
		}
		if ( isset( $filled[ $name ] ) ) {
			return '<svg class="' . esc_attr( $cls ) . '" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" aria-hidden="true" focusable="false">' . $filled[ $name ] . '</svg>';
		}
		return '';
	}

	/**
	 * @param string $name Icon name.
	 * @return bool
	 */
	public static function has( $name ) {
		return isset( self::stroke()[ $name ] ) || isset( self::filled()[ $name ] );
	}
}
