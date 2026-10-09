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
	 * @return array screen => label
	 */
	public static function screens() {
		return array(
			'zones'    => __( 'Lighting zones', 'xlights-elementor-widgets' ),
			'patterns' => __( 'Patterns & themes', 'xlights-elementor-widgets' ),
			'color'    => __( 'Color & brightness', 'xlights-elementor-widgets' ),
			'schedule' => __( 'Schedules & timers', 'xlights-elementor-widgets' ),
			'scenes'   => __( 'Saved scenes', 'xlights-elementor-widgets' ),
			'remote'   => __( 'Remote control', 'xlights-elementor-widgets' ),
			'share'    => __( 'Shared access', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * Phone mockup with an illustrative app screen or an uploaded screenshot.
	 *
	 * @param string $screen     Screen key.
	 * @param mixed  $screenshot Optional MEDIA value; replaces the mock screen.
	 * @param array  $args       class, eager.
	 * @return string
	 */
	public static function phone( $screen, $screenshot = null, array $args = array() ) {
		$screens = self::screens();
		$screen  = isset( $screens[ $screen ] ) ? $screen : 'zones';
		$class   = 'xl-phone' . ( ! empty( $args['class'] ) ? ' ' . $args['class'] : '' );

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

		$label = sprintf(
			/* translators: %s: app screen name */
			__( 'Illustration of the X LIGHTS app: %s', 'xlights-elementor-widgets' ),
			$screens[ $screen ]
		);

		$out  = '<div class="' . esc_attr( $class ) . ' xl-phone--mock" role="img" aria-label="' . esc_attr( $label ) . '">';
		$out .= '<div class="xl-phone__screen" aria-hidden="true">';
		$out .= '<div class="xl-app__bar"><span class="xl-app__time">9:41</span><img class="xl-app__logo" src="' . esc_url( XLIGHTS_URL . 'assets/images/brand/xlights-logo.png' ) . '" width="70" height="21" alt=""><span class="xl-app__dots"></span></div>';
		$out .= '<div class="xl-app xl-app--' . esc_attr( $screen ) . '">' . self::screen_body( $screen ) . '</div>';
		$out .= '<div class="xl-app__tabs"><i></i><i></i><i></i><i></i><i></i></div>';
		$out .= '</div></div>';
		return $out;
	}

	/**
	 * @param string $screen Screen key.
	 * @return string
	 */
	private static function screen_body( $screen ) {
		$t = static function ( $s ) {
			return esc_html( $s );
		};
		switch ( $screen ) {
			case 'patterns':
				$items = array(
					array( 'star', __( 'Favorites', 'xlights-elementor-widgets' ), '#e9e2d6' ),
					array( 'home', __( 'Architectural', 'xlights-elementor-widgets' ), '#ffd08a' ),
					array( 'sparkles', __( 'Star Twinkle', 'xlights-elementor-widgets' ), '#f6f2e8' ),
					array( 'tree', __( 'Christmas', 'xlights-elementor-widgets' ), '#45d483' ),
					array( 'moon', __( 'Halloween', 'xlights-elementor-widgets' ), '#ff8a1f' ),
					array( 'leaf', __( 'Thanksgiving', 'xlights-elementor-widgets' ), '#e0a050' ),
					array( 'heart', __( 'Valentine’s', 'xlights-elementor-widgets' ), '#ff5c8a' ),
					array( 'flag', __( 'Independence', 'xlights-elementor-widgets' ), '#4d7dff' ),
					array( 'cake', __( 'Birthday', 'xlights-elementor-widgets' ), '#b07cff' ),
				);
				$out   = '<p class="xl-app__title">' . $t( __( 'Patterns', 'xlights-elementor-widgets' ) ) . '</p><div class="xl-app__grid">';
				foreach ( $items as $i ) {
					$out .= '<span class="xl-app__tile" style="--c:' . esc_attr( $i[2] ) . '">' . Icons::svg( $i[0] ) . '<b>' . $t( $i[1] ) . '</b></span>';
				}
				return $out . '</div>';

			case 'color':
				$out  = '<p class="xl-app__title">' . $t( __( 'Roofline', 'xlights-elementor-widgets' ) ) . '</p>';
				$out .= '<div class="xl-app__wheel"><span></span></div>';
				$out .= '<div class="xl-app__slider"><em>' . $t( __( 'Brightness', 'xlights-elementor-widgets' ) ) . '</em><span style="--v:72%"></span></div>';
				$out .= '<div class="xl-app__slider xl-app__slider--white"><em>' . $t( __( 'White', 'xlights-elementor-widgets' ) ) . '</em><span style="--v:38%"></span></div>';
				$out .= '<div class="xl-app__swatches"><i style="--c:#ffd08a"></i><i style="--c:#ff4d4d"></i><i style="--c:#45d483"></i><i style="--c:#4d7dff"></i><i style="--c:#b07cff"></i><i style="--c:#ff8a1f"></i></div>';
				return $out;

			case 'schedule':
				$rows = array(
					array( __( 'Every evening', 'xlights-elementor-widgets' ), __( '7:00 PM – 11:00 PM · Warm White', 'xlights-elementor-widgets' ), true ),
					array( __( 'Holiday season', 'xlights-elementor-widgets' ), __( 'Dec 1 – Jan 1 · Christmas', 'xlights-elementor-widgets' ), true ),
					array( __( 'Halloween week', 'xlights-elementor-widgets' ), __( 'Oct 25 – 31 · Halloween', 'xlights-elementor-widgets' ), false ),
					array( __( 'Weekend patio', 'xlights-elementor-widgets' ), __( 'Fri – Sun · 6:00 PM – Midnight', 'xlights-elementor-widgets' ), true ),
				);
				$out  = '<p class="xl-app__title">' . $t( __( 'Timers', 'xlights-elementor-widgets' ) ) . '</p>';
				foreach ( $rows as $r ) {
					$out .= '<div class="xl-app__row">' . Icons::svg( 'clock' ) . '<span><b>' . $t( $r[0] ) . '</b><small>' . $t( $r[1] ) . '</small></span><i class="xl-app__switch' . ( $r[2] ? ' is-on' : '' ) . '"></i></div>';
				}
				return $out;

			case 'scenes':
				$rows = array(
					array( __( 'Welcome Home', 'xlights-elementor-widgets' ), array( '#ffd08a', '#ffd08a', '#f6f2e8' ) ),
					array( __( 'Dinner Party', 'xlights-elementor-widgets' ), array( '#ffb86b', '#ffd08a', '#ff8a5c' ) ),
					array( __( 'Game Day', 'xlights-elementor-widgets' ), array( '#4d7dff', '#ff8a1f', '#4d7dff' ) ),
					array( __( 'Birthday', 'xlights-elementor-widgets' ), array( '#ff5c8a', '#ffc93c', '#3cc4ff' ) ),
				);
				$out  = '<p class="xl-app__title">' . $t( __( 'My Scenes', 'xlights-elementor-widgets' ) ) . '</p>';
				foreach ( $rows as $r ) {
					$out .= '<div class="xl-app__row"><span class="xl-app__chips">';
					foreach ( $r[1] as $c ) {
						$out .= '<i style="--c:' . esc_attr( $c ) . '"></i>';
					}
					$out .= '</span><span><b>' . $t( $r[0] ) . '</b></span>' . Icons::svg( 'play' ) . '</div>';
				}
				return $out;

			case 'remote':
				$out  = '<p class="xl-app__title">' . $t( __( 'Away from home', 'xlights-elementor-widgets' ) ) . '</p>';
				$out .= '<div class="xl-app__card">' . Icons::svg( 'globe' ) . '<span><b>' . $t( __( 'Connected', 'xlights-elementor-widgets' ) ) . '</b><small>' . $t( __( 'Control from anywhere', 'xlights-elementor-widgets' ) ) . '</small></span></div>';
				$out .= '<div class="xl-app__row">' . Icons::svg( 'roofline' ) . '<span><b>' . $t( __( 'Roofline', 'xlights-elementor-widgets' ) ) . '</b><small>' . $t( __( 'On · Warm White', 'xlights-elementor-widgets' ) ) . '</small></span><i class="xl-app__switch is-on"></i></div>';
				$out .= '<div class="xl-app__row">' . Icons::svg( 'tree' ) . '<span><b>' . $t( __( 'Yard', 'xlights-elementor-widgets' ) ) . '</b><small>' . $t( __( 'Off', 'xlights-elementor-widgets' ) ) . '</small></span><i class="xl-app__switch"></i></div>';
				return $out;

			case 'share':
				$rows = array(
					array( __( 'You', 'xlights-elementor-widgets' ), __( 'Owner', 'xlights-elementor-widgets' ) ),
					array( __( 'Family member', 'xlights-elementor-widgets' ), __( 'Can control', 'xlights-elementor-widgets' ) ),
					array( __( 'House sitter', 'xlights-elementor-widgets' ), __( 'Can control', 'xlights-elementor-widgets' ) ),
				);
				$out  = '<p class="xl-app__title">' . $t( __( 'Shared Access', 'xlights-elementor-widgets' ) ) . '</p>';
				foreach ( $rows as $r ) {
					$out .= '<div class="xl-app__row">' . Icons::svg( 'user' ) . '<span><b>' . $t( $r[0] ) . '</b><small>' . $t( $r[1] ) . '</small></span></div>';
				}
				$out .= '<span class="xl-app__btn">' . Icons::svg( 'plus' ) . $t( __( 'Invite', 'xlights-elementor-widgets' ) ) . '</span>';
				return $out;

			case 'zones':
			default:
				$rows = array(
					array( 'roofline', __( 'Soffit / Roofline', 'xlights-elementor-widgets' ), true ),
					array( 'string-lights', __( 'Patio Bistro', 'xlights-elementor-widgets' ), true ),
					array( 'stairs', __( 'Deck', 'xlights-elementor-widgets' ), false ),
					array( 'tree', __( 'Landscape', 'xlights-elementor-widgets' ), true ),
				);
				$out  = '<p class="xl-app__title">' . $t( __( 'My Controller', 'xlights-elementor-widgets' ) ) . '</p>';
				$out .= '<div class="xl-app__row xl-app__row--all"><span><b>' . $t( __( 'All of the lights', 'xlights-elementor-widgets' ) ) . '</b></span><i class="xl-app__switch is-on"></i></div>';
				foreach ( $rows as $r ) {
					$out .= '<div class="xl-app__row">' . Icons::svg( $r[0] ) . '<span><b>' . $t( $r[1] ) . '</b><small class="xl-app__online">' . $t( __( 'Online', 'xlights-elementor-widgets' ) ) . '</small></span><i class="xl-app__switch' . ( $r[2] ? ' is-on' : '' ) . '"></i></div>';
				}
				return $out;
		}
	}
}
