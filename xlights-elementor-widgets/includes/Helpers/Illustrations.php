<?php
/**
 * Vector illustrations used as visual fallbacks until approved photography is
 * added, plus HTML/CSS app-screen mockups modeled on the X LIGHTS app screens
 * shown in the client brochures. Illustrations are clearly not photographs, so
 * they never pass as real customer projects.
 *
 * @package XLights
 */

namespace XLights\Helpers;

defined( 'ABSPATH' ) || exit;

final class Illustrations {

	/** @var int Unique ID counter for SVG defs. */
	private static $uid = 0;

	/**
	 * @return array scene => label
	 */
	public static function scenes() {
		return array(
			'roofline'   => __( 'Roofline (soffit lighting)', 'xlights-elementor-widgets' ),
			'daytime'    => __( 'Roofline by day (low-profile track)', 'xlights-elementor-widgets' ),
			'patio'      => __( 'Patio with bistro lights', 'xlights-elementor-widgets' ),
			'deck'       => __( 'Deck and steps', 'xlights-elementor-widgets' ),
			'landscape'  => __( 'Landscape, tree and path', 'xlights-elementor-widgets' ),
			'commercial' => __( 'Restaurant / storefront', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * @return array palette => label
	 */
	public static function palettes() {
		return array(
			'warm'        => __( 'Warm white', 'xlights-elementor-widgets' ),
			'holiday'     => __( 'Christmas (red & green)', 'xlights-elementor-widgets' ),
			'halloween'   => __( 'Halloween (orange & purple)', 'xlights-elementor-widgets' ),
			'celebration' => __( 'Celebration (multicolor)', 'xlights-elementor-widgets' ),
			'patriotic'   => __( 'Red, white & blue', 'xlights-elementor-widgets' ),
			'cool'        => __( 'Cool white', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * @param string $palette Palette key.
	 * @return string[]
	 */
	private static function colors( $palette ) {
		$map = array(
			'warm'        => array( '#FFD08A' ),
			'holiday'     => array( '#FF4D4D', '#45D483', '#FFE2A8' ),
			'halloween'   => array( '#FF8A1F', '#A35CFF' ),
			'celebration' => array( '#FF5C8A', '#FFC93C', '#3CC4FF', '#7CF29C', '#B07CFF' ),
			'patriotic'   => array( '#FF4D5E', '#F4F6FF', '#4D7DFF' ),
			'cool'        => array( '#E7F1FF' ),
		);
		return isset( $map[ $palette ] ) ? $map[ $palette ] : $map['warm'];
	}

	/**
	 * Row of glowing lights along a polyline.
	 *
	 * @param array  $points  [[x,y],...] vertices.
	 * @param float  $spacing Distance between lights.
	 * @param array  $colors  Colors to cycle.
	 * @param string $glow    Filter ID.
	 * @param float  $r       Core radius.
	 * @return string
	 */
	private static function light_run( array $points, $spacing, array $colors, $glow, $r = 3.2 ) {
		$dots = array();
		$n    = count( $points );
		$i    = 0;
		for ( $p = 0; $p < $n - 1; $p++ ) {
			list( $x1, $y1 ) = $points[ $p ];
			list( $x2, $y2 ) = $points[ $p + 1 ];
			$len             = sqrt( pow( $x2 - $x1, 2 ) + pow( $y2 - $y1, 2 ) );
			$steps           = max( 1, (int) floor( $len / $spacing ) );
			for ( $s = 0; $s < $steps; $s++ ) {
				$t      = $s / $steps;
				$dots[] = array( $x1 + ( $x2 - $x1 ) * $t, $y1 + ( $y2 - $y1 ) * $t, $colors[ $i % count( $colors ) ] );
				$i++;
			}
		}
		$halo = '';
		$core = '';
		foreach ( $dots as $d ) {
			$halo .= sprintf( '<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s"/>', $d[0], $d[1], $r * 3.4, $d[2] );
			$core .= sprintf( '<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s"/>', $d[0], $d[1], $r, $d[2] );
		}
		return '<g opacity=".55" filter="url(#' . $glow . ')">' . $halo . '</g><g>' . $core . '</g>';
	}

	/**
	 * Catenary-ish string of bulbs between two points.
	 *
	 * @param float  $x1   Start x.
	 * @param float  $y1   Start y.
	 * @param float  $x2   End x.
	 * @param float  $y2   End y.
	 * @param float  $sag  Sag depth.
	 * @param int    $count Bulbs.
	 * @param array  $colors Colors.
	 * @param string $glow Filter id.
	 * @return string
	 */
	private static function bulb_string( $x1, $y1, $x2, $y2, $sag, $count, array $colors, $glow ) {
		$cx    = ( $x1 + $x2 ) / 2;
		$cy    = max( $y1, $y2 ) + $sag;
		$wire  = sprintf( '<path d="M%.1f %.1fQ%.1f %.1f %.1f %.1f" stroke="#2a303c" stroke-width="2" fill="none"/>', $x1, $y1, $cx, $cy, $x2, $y2 );
		$halo  = '';
		$bulbs = '';
		for ( $i = 1; $i < $count; $i++ ) {
			$t      = $i / $count;
			$x      = pow( 1 - $t, 2 ) * $x1 + 2 * ( 1 - $t ) * $t * $cx + $t * $t * $x2;
			$y      = pow( 1 - $t, 2 ) * $y1 + 2 * ( 1 - $t ) * $t * $cy + $t * $t * $y2;
			$c      = $colors[ $i % count( $colors ) ];
			$halo  .= sprintf( '<circle cx="%.1f" cy="%.1f" r="16" fill="%s"/>', $x, $y + 12, $c );
			$bulbs .= sprintf( '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#2a303c" stroke-width="2"/><ellipse cx="%.1f" cy="%.1f" rx="5" ry="7" fill="%s"/>', $x, $y, $x, $y + 6, $x, $y + 12, $c );
		}
		return $wire . '<g opacity=".5" filter="url(#' . $glow . ')">' . $halo . '</g>' . $bulbs;
	}

	/**
	 * Render a scene.
	 *
	 * @param string $scene   Scene key.
	 * @param string $palette Palette key.
	 * @param string $label   Accessible label.
	 * @return string
	 */
	public static function scene( $scene, $palette = 'warm', $label = '' ) {
		self::$uid++;
		$id     = 'xlg' . self::$uid;
		$colors = self::colors( $palette );
		$scenes = self::scenes();
		$scene  = isset( $scenes[ $scene ] ) ? $scene : 'roofline';
		$label  = '' !== $label ? $label : sprintf(
			/* translators: %s: scene description */
			__( 'Illustration: %s', 'xlights-elementor-widgets' ),
			$scenes[ $scene ]
		);

		$day  = 'daytime' === $scene;
		$sky  = $day
			? '<linearGradient id="' . $id . 's" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#cfe2f3"/><stop offset="1" stop-color="#eef3f6"/></linearGradient>'
			: '<linearGradient id="' . $id . 's" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0b1020"/><stop offset=".65" stop-color="#1a2034"/><stop offset="1" stop-color="#2a2a33"/></linearGradient>';
		$defs = '<defs>' . $sky . '<filter id="' . $id . '" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="6"/></filter>'
			. '<radialGradient id="' . $id . 'w" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#ffcf87" stop-opacity=".9"/><stop offset="1" stop-color="#ffcf87" stop-opacity="0"/></radialGradient>'
			. '<linearGradient id="' . $id . 'u" x1="0" y1="1" x2="0" y2="0"><stop offset="0" stop-color="' . $colors[0] . '" stop-opacity=".55"/><stop offset="1" stop-color="' . $colors[0] . '" stop-opacity="0"/></linearGradient></defs>';

		$stars = '';
		if ( ! $day ) {
			foreach ( array( array( 80, 60 ), array( 190, 110 ), array( 330, 45 ), array( 470, 90 ), array( 610, 40 ), array( 720, 120 ), array( 560, 150 ), array( 260, 170 ) ) as $s ) {
				$stars .= sprintf( '<circle cx="%d" cy="%d" r="1.3" fill="#fff" opacity=".55"/>', $s[0], $s[1] );
			}
		}

		$body = '';
		switch ( $scene ) {
			case 'daytime':
				// Close-up of a fascia with the low-profile track: lights barely visible by day.
				$body .= '<path d="M0 0h800v600H0z" fill="url(#' . $id . 's)"/>';
				$body .= '<path d="M0 230 800 120v120L0 350z" fill="#4b5361"/><path d="M0 350 800 240v26L0 376z" fill="#3d4450"/>';
				$body .= '<path d="M0 376 800 266v334H0z" fill="#d6dbe2"/>';
				$body .= '<path d="M0 363 800 253" stroke="#343b47" stroke-width="10"/>';
				foreach ( array( 70, 230, 390, 550, 710 ) as $x ) {
					$y     = 363 - ( $x * 110 / 800 );
					$body .= sprintf( '<circle cx="%d" cy="%.1f" r="7" fill="#e6eaf0" stroke="#6f7785" stroke-width="1.5"/>', $x, $y );
				}
				$body .= '<path d="M0 376h800" stroke="none"/>';
				break;

			case 'patio':
				$body .= '<path d="M0 0h800v600H0z" fill="url(#' . $id . 's)"/>' . $stars;
				$body .= '<path d="M0 430h800v170H0z" fill="#161d2a"/><path d="M0 430h800" stroke="#2c3442" stroke-width="3"/>';
				$body .= '<path d="M70 150v290M730 150v290M400 160v270" stroke="#2a303c" stroke-width="10"/>';
				$body .= self::bulb_string( 70, 160, 400, 170, 70, 8, $colors, $id );
				$body .= self::bulb_string( 400, 170, 730, 160, 70, 8, $colors, $id );
				$body .= self::bulb_string( 70, 240, 730, 240, 60, 14, $colors, $id );
				$body .= '<ellipse cx="400" cy="470" rx="250" ry="40" fill="url(#' . $id . 'w)" opacity=".45"/>';
				$body .= '<path d="M300 430h200v12H300zM315 442v58M485 442v58" stroke="#0b0f17" stroke-width="8" fill="#0b0f17"/>';
				$body .= '<path d="M240 420v80M240 440h40v60M560 420v80M560 440h-40v60" stroke="#0b0f17" stroke-width="7" fill="none"/>';
				break;

			case 'deck':
				$body .= '<path d="M0 0h800v600H0z" fill="url(#' . $id . 's)"/>' . $stars;
				$body .= '<path d="M0 250h800v350H0z" fill="#161d2a"/>';
				$body .= '<path d="M120 250h560v40H120z" fill="#3a2f28"/>';
				for ( $i = 0; $i < 5; $i++ ) {
					$y     = 290 + $i * 52;
					$x     = 200 + $i * 30;
					$body .= sprintf( '<path d="M%d %dh%d v14 H%dz" fill="#4a3c32"/><path d="M%d %dh%d v38 H%dz" fill="#2c241f"/>', $x, $y, 400 - $i * 60, $x, $x, $y + 14, 400 - $i * 60, $x );
					$body .= self::light_run( array( array( $x + 30, $y + 26 ), array( $x + 400 - $i * 60 - 20, $y + 26 ) ), 70, $colors, $id, 3 );
				}
				$body .= '<path d="M120 130v120M680 130v120M120 150h560M120 200h560" stroke="#2a303c" stroke-width="8"/>';
				foreach ( array( 120, 307, 493, 680 ) as $x ) {
					$body .= self::light_run( array( array( $x, 136 ), array( $x + 1, 136 ) ), 10, $colors, $id, 4 );
				}
				break;

			case 'landscape':
				$body .= '<path d="M0 0h800v600H0z" fill="url(#' . $id . 's)"/>' . $stars;
				$body .= '<path d="M0 420c160-30 360-40 800-20v200H0z" fill="#16201a"/>';
				$body .= '<path d="M520 420V260l120-90 120 90v160z" fill="#222a37"/><path d="M570 330h40v40h-40z" fill="#ffcf87" opacity=".8"/>';
				$body .= '<path d="M200 470 140 230h120z" fill="url(#' . $id . 'u)"/>';
				$body .= '<path d="M200 470V330" stroke="#2a221d" stroke-width="12"/>';
				$body .= '<path d="M200 140c-70 0-110 50-110 100 0 55 50 90 110 90s110-35 110-90c0-50-40-100-110-100z" fill="#1f3326"/>';
				$body .= '<path d="M200 140c-70 0-110 50-110 100 0 55 50 90 110 90s110-35 110-90c0-50-40-100-110-100z" fill="' . $colors[0] . '" opacity=".22"/>';
				$body .= '<path d="M300 600c40-80 120-130 260-170" stroke="#343a44" stroke-width="40" fill="none" opacity=".8"/>';
				foreach ( array( array( 290, 560 ), array( 350, 505 ), array( 430, 465 ), array( 500, 440 ) ) as $p ) {
					$body .= sprintf( '<path d="M%d %dv-26" stroke="#2a303c" stroke-width="4"/>', $p[0] - 30, $p[1] );
					$body .= self::light_run( array( array( $p[0] - 30, $p[1] - 30 ), array( $p[0] - 29, $p[1] - 30 ) ), 10, $colors, $id, 4 );
				}
				$body .= self::light_run( array( array( 200, 470 ), array( 201, 470 ) ), 10, $colors, $id, 4 );
				break;

			case 'commercial':
				$body .= '<path d="M0 0h800v600H0z" fill="url(#' . $id . 's)"/>' . $stars;
				$body .= '<path d="M90 180h620v300H90z" fill="#222a37"/><path d="M70 160h660v24H70z" fill="#171e2b"/>';
				$body .= '<path d="M140 280h200v200H140zM460 280h200v200H460z" fill="#ffcf87" opacity=".18"/>';
				$body .= '<path d="M130 250h540l-30 30H160z" fill="#343b48"/>';
				$body .= self::light_run( array( array( 70, 172 ), array( 730, 172 ) ), 34, $colors, $id, 3.4 );
				$body .= '<path d="M0 480h800v120H0z" fill="#151b27"/>';
				$body .= self::bulb_string( 0, 400, 400, 410, 50, 8, $colors, $id );
				$body .= self::bulb_string( 400, 410, 800, 400, 50, 8, $colors, $id );
				$body .= '<path d="M180 540h120v10H180zM500 540h120v10H500z" fill="#0b0f17"/>';
				break;

			case 'roofline':
			default:
				$body .= '<path d="M0 0h800v600H0z" fill="url(#' . $id . 's)"/>' . $stars;
				$body .= '<path d="M90 300 300 160l210 140V520H90z" fill="#222a37"/>';
				$body .= '<path d="M440 330 600 220l160 110v190H440z" fill="#262e3b"/>';
				$body .= '<path d="M150 380h70v70h-70zM360 380h90v140h-90zM520 380h60v60h-60zM650 380h60v60h-60z" fill="#ffcf87" opacity=".85"/>';
				$body .= '<path d="M0 520h800v80H0z" fill="#121823"/>';
				$body .= '<ellipse cx="300" cy="540" rx="300" ry="30" fill="url(#' . $id . 'w)" opacity=".3"/>';
				$body .= self::light_run( array( array( 70, 312 ), array( 300, 158 ), array( 530, 312 ) ), 30, $colors, $id );
				$body .= self::light_run( array( array( 425, 340 ), array( 600, 218 ), array( 775, 340 ) ), 30, $colors, $id );
				break;
		}

		return '<svg class="xl-illus xl-illus--' . esc_attr( $scene ) . '" viewBox="0 0 800 600" preserveAspectRatio="xMidYMid slice" role="img" aria-label="' . esc_attr( $label ) . '">' . $defs . $body . '</svg>';
	}

	/**
	 * Official X LIGHTS app screens (screenshots of the real app, as published
	 * on the X LIGHTS website). Keys are kept from earlier versions so saved
	 * pages keep working; retired keys map to the closest real screen.
	 *
	 * @return array screen => label
	 */
	public static function screens() {
		return array(
			'zones'    => __( 'Controllers & zones', 'xlights-elementor-widgets' ),
			'patterns' => __( 'Patterns & holidays', 'xlights-elementor-widgets' ),
			'color'    => __( 'Color & brightness', 'xlights-elementor-widgets' ),
			'schedule' => __( 'Timers & schedules', 'xlights-elementor-widgets' ),
			'music'    => __( 'Music mode', 'xlights-elementor-widgets' ),
			'share'    => __( 'Family management', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * Bundled screenshot file for a screen key.
	 *
	 * @param string $screen Screen key.
	 * @return string File name in assets/images/app/.
	 */
	private static function screen_file( $screen ) {
		$files = array(
			'zones'    => 'app-controllers.webp',
			'remote'   => 'app-controllers.webp',
			'patterns' => 'app-patterns.webp',
			'scenes'   => 'app-patterns.webp',
			'color'    => 'app-color.webp',
			'schedule' => 'app-timers.webp',
			'music'    => 'app-music.webp',
			'share'    => 'app-family.webp',
		);
		return isset( $files[ $screen ] ) ? $files[ $screen ] : $files['zones'];
	}

	/**
	 * The X LIGHTS app on a phone: an uploaded screenshot (framed by CSS), or
	 * the official screenshot for the chosen screen (already in its device frame).
	 *
	 * @param string $screen     Screen key.
	 * @param mixed  $screenshot Optional MEDIA value (a plain screenshot).
	 * @param array  $args       eager, class.
	 * @return string
	 */
	public static function phone( $screen, $screenshot = null, array $args = array() ) {
		$class = 'xl-phone' . ( ! empty( $args['class'] ) ? ' ' . $args['class'] : '' );

		if ( $screenshot && Html::has_image( $screenshot ) ) {
			$inner = Html::image(
				$screenshot,
				array(
					'size'  => 'large',
					'class' => 'xl-phone__shot',
					'eager' => ! empty( $args['eager'] ),
				)
			);
			return '<div class="' . esc_attr( $class ) . '"><div class="xl-phone__screen">' . $inner . '</div></div>';
		}

		$screens = self::screens();
		$label   = isset( $screens[ $screen ] ) ? $screens[ $screen ] : $screens['zones'];
		$img     = Html::image(
			Html::asset_image( 'app/' . self::screen_file( $screen ) ),
			array(
				'class' => 'xl-phone__device',
				'sizes' => '(max-width: 767px) 74vw, 300px',
				'eager' => ! empty( $args['eager'] ),
				/* translators: %s: app screen name, e.g. "Color & brightness" */
				'alt'   => sprintf( __( 'X LIGHTS app: %s screen', 'xlights-elementor-widgets' ), $label ),
			)
		);
		return '<div class="' . esc_attr( $class ) . ' xl-phone--device">' . $img . '</div>';
	}
}
