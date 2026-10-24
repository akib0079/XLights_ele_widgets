<?php
/**
 * Sitewide header and footer.
 *
 * Rendered by the Header/Footer Elementor widgets (Elementor Pro Theme Builder
 * or any page), the [xlights_header]/[xlights_footer] shortcodes, or injected
 * automatically according to the "Global header & footer" setting:
 *   canvas: on pages using the Elementor Canvas template (any theme, Elementor Free)
 *   global: on every front-end page (best with Hello Elementor)
 *
 * Navigation comes from the "X LIGHTS Header/Footer" menu locations when menus
 * are assigned, otherwise from the page map created by the setup routine.
 *
 * @package XLights
 */

namespace XLights\Frontend;

use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\Helpers\Tokens;
use XLights\Pages\Page_Registry;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Header_Footer {

	const MENU_PRIMARY = 'xlights_primary';
	const MENU_FOOTER  = 'xlights_footer';

	/** @var bool Guards against double output. */
	private static $header_done = false;
	private static $footer_done = false;

	public static function init() {
		add_action( 'after_setup_theme', array( __CLASS__, 'register_menus' ), 20 );
		add_action( 'elementor/page_templates/canvas/before_content', array( __CLASS__, 'canvas_before' ), 5 );
		add_action( 'elementor/page_templates/canvas/after_content', array( __CLASS__, 'canvas_after' ), 50 );
		add_action( 'wp_body_open', array( __CLASS__, 'global_header' ), 5 );
		add_action( 'wp_footer', array( __CLASS__, 'global_footer' ), 5 );
		add_filter( 'hello_elementor_header_footer', array( __CLASS__, 'hello_theme_toggle' ) );
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
	}

	public static function register_menus() {
		register_nav_menus(
			array(
				self::MENU_PRIMARY => __( 'X LIGHTS Header', 'xlights-elementor-widgets' ),
				self::MENU_FOOTER  => __( 'X LIGHTS Footer', 'xlights-elementor-widgets' ),
			)
		);
	}

	/**
	 * @return string canvas|global|off
	 */
	public static function mode() {
		$mode = (string) Settings::get( 'header_footer_mode', 'canvas' );
		return in_array( $mode, array( 'canvas', 'global', 'off' ), true ) ? $mode : 'canvas';
	}

	/**
	 * Is the current request a page using the Elementor Canvas template?
	 *
	 * @return bool
	 */
	private static function is_canvas() {
		return is_singular() && 'elementor_canvas' === get_page_template_slug( get_queried_object_id() );
	}

	/**
	 * Will the global header/footer be printed on this request?
	 *
	 * @return bool
	 */
	public static function active_on_request() {
		if ( is_admin() || is_feed() || is_embed() ) {
			return false;
		}
		$mode = self::mode();
		return 'global' === $mode || ( 'canvas' === $mode && self::is_canvas() );
	}

	/**
	 * @param bool $show Hello Elementor header/footer.
	 * @return bool
	 */
	public static function hello_theme_toggle( $show ) {
		return 'global' === self::mode() ? false : $show;
	}

	/**
	 * @param array $classes Body classes.
	 * @return array
	 */
	public static function body_class( $classes ) {
		if ( self::active_on_request() ) {
			$classes[] = 'xl-has-header';
			if ( Settings::enabled( 'header_transparent' ) ) {
				$classes[] = 'xl-header-overlay';
			}
		}
		return $classes;
	}

	public static function canvas_before() {
		if ( 'canvas' !== self::mode() && 'global' !== self::mode() ) {
			return;
		}
		if ( ! self::$header_done ) {
			echo self::header(); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '<main id="xl-main" class="xl-main" tabindex="-1">';
	}

	public static function canvas_after() {
		if ( 'canvas' !== self::mode() && 'global' !== self::mode() ) {
			return;
		}
		echo '</main>';
		if ( ! self::$footer_done ) {
			echo self::footer(); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	public static function global_header() {
		if ( 'global' === self::mode() && ! self::is_canvas() && ! is_admin() ) {
			echo self::header(); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	public static function global_footer() {
		if ( 'global' === self::mode() && ! self::is_canvas() && ! is_admin() && ! self::$footer_done ) {
			echo self::footer(); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	/**
	 * Is the current request a page built with the Page Composer?
	 *
	 * @return bool
	 */
	public static function is_composer_page() {
		if ( ! is_singular() ) {
			return false;
		}
		$id = get_queried_object_id();
		if ( Page_Registry::key_for_post( $id ) ) {
			return true;
		}
		return false !== strpos( (string) get_post_meta( $id, '_elementor_data', true ), 'xlights-page-composer' );
	}

	/* ------------------------------------------------------------------
	 * Navigation tree
	 * ---------------------------------------------------------------- */

	/**
	 * Default navigation from the page map (used when no menu is assigned).
	 *
	 * @return array[] Tree: title, url, children.
	 */
	public static function default_tree() {
		$node = static function ( $key, $title = '' ) {
			$def = Page_Registry::get( $key );
			return array(
				'title'    => '' !== $title ? $title : $def['title'],
				'url'      => Page_Registry::url( $key ),
				'children' => array(),
			);
		};
		$about                = $node( 'about' );
		$about['children']    = array( $node( 'about' ), $node( 'app' ), $node( 'reviews' ) );
		$solutions            = $node( 'solutions' );
		$solutions['children'] = array(
			$node( 'solutions', __( 'All Lighting Solutions', 'xlights-elementor-widgets' ) ),
			$node( 'soffit' ),
			$node( 'bistro' ),
			$node( 'deck' ),
			$node( 'landscape' ),
		);
		$tree = array(
			$node( 'home' ),
			$about,
			$solutions,
			$node( 'commercial' ),
			$node( 'projects', __( 'Projects / Gallery', 'xlights-elementor-widgets' ) ),
			$node( 'contact' ),
		);
		/**
		 * Filter the default navigation tree.
		 *
		 * @param array $tree Tree.
		 */
		return apply_filters( 'xlights/nav/default_tree', $tree );
	}

	/**
	 * Tree from an assigned menu location, or null.
	 *
	 * @param string $location Location.
	 * @return array[]|null
	 */
	public static function menu_tree( $location ) {
		$locations = get_nav_menu_locations();
		if ( empty( $locations[ $location ] ) ) {
			return null;
		}
		$items = wp_get_nav_menu_items( $locations[ $location ], array( 'update_post_term_cache' => false ) );
		if ( ! $items ) {
			return null;
		}
		$by_parent = array();
		foreach ( $items as $item ) {
			$by_parent[ (int) $item->menu_item_parent ][] = $item;
		}
		$build = static function ( $parent ) use ( &$build, $by_parent ) {
			$out = array();
			if ( empty( $by_parent[ $parent ] ) ) {
				return $out;
			}
			foreach ( $by_parent[ $parent ] as $item ) {
				$out[] = array(
					'title'    => $item->title,
					'url'      => $item->url,
					'target'   => $item->target,
					'children' => $build( (int) $item->ID ),
				);
			}
			return $out;
		};
		return $build( 0 );
	}

	/**
	 * @param string $url URL.
	 * @return bool
	 */
	private static function is_current( $url ) {
		static $current = null;
		if ( null === $current ) {
			$req     = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
			$current = untrailingslashit( strtok( home_url( $req ), '?#' ) );
		}
		return untrailingslashit( strtok( (string) $url, '?#' ) ) === $current;
	}

	/* ------------------------------------------------------------------
	 * Header
	 * ---------------------------------------------------------------- */

	/**
	 * Logo lockup: the uploaded logo or the bundled official X LIGHTS logo,
	 * optionally followed by the market name ("Southern California").
	 *
	 * @param string $variant light|dark — the background the logo sits on.
	 * @return string
	 */
	public static function logo( $variant = 'light' ) {
		$on_dark = 'dark' === $variant;
		$id      = (int) Settings::get( $on_dark ? 'logo_light' : 'logo' );
		$invert  = false;
		if ( ! $id && $on_dark && (int) Settings::get( 'logo' ) ) {
			// Only a logo for light backgrounds exists: show it reversed.
			$id     = (int) Settings::get( 'logo' );
			$invert = true;
		}

		if ( $id && wp_attachment_is_image( $id ) ) {
			$img = wp_get_attachment_image(
				$id,
				'medium',
				false,
				array(
					'class'    => 'xl-logo__img' . ( $invert ? ' xl-logo__img--invert' : '' ),
					'alt'      => (string) Settings::get( 'dealer_name' ),
					'loading'  => 'eager',
					'decoding' => 'async',
				)
			);
		} else {
			$img = sprintf(
				'<img class="xl-logo__img" src="%1$s" width="376" height="112" alt="%2$s" decoding="async">',
				esc_url( XLIGHTS_URL . 'assets/images/brand/' . ( $on_dark ? 'xlights-logo-white.png' : 'xlights-logo.png' ) ),
				esc_attr__( 'X LIGHTS Outdoor Lighting', 'xlights-elementor-widgets' )
			);
		}

		$market = Settings::enabled( 'logo_show_market' ) ? trim( (string) Settings::get( 'market_name' ) ) : '';
		return '<span class="xl-lockup">' . $img . ( '' !== $market ? '<span class="xl-lockup__market">' . esc_html( $market ) . '</span>' : '' ) . '</span>';
	}

	/**
	 * Render the header.
	 *
	 * @param array $opts sticky, transparent, show_phone, cta_label, cta_link, menu.
	 * @return string
	 */
	public static function header( array $opts = array() ) {
		self::$header_done = true;
		Assets::enqueue();

		$opts = wp_parse_args(
			$opts,
			array(
				'sticky'      => Settings::enabled( 'header_sticky' ),
				'transparent' => Settings::enabled( 'header_transparent' ),
				'show_phone'  => Settings::enabled( 'header_show_phone' ),
				'cta_label'   => (string) Settings::get( 'primary_cta_label' ),
				'cta_link'    => '{quote_url}',
				'menu'        => self::MENU_PRIMARY,
			)
		);
		$tree  = self::menu_tree( $opts['menu'] );
		$tree  = null === $tree ? self::default_tree() : $tree;
		$phone = (string) Settings::get( 'phone' );
		$uid   = 'xlh' . wp_rand( 100, 999 );

		$classes = array( 'xl-root', 'xl-header' );
		if ( $opts['sticky'] ) {
			$classes[] = 'xl-header--sticky';
		}
		if ( $opts['transparent'] ) {
			$classes[] = 'xl-header--overlay-capable';
		}

		$cta = Html::button( $opts['cta_label'], $opts['cta_link'], 'primary', array( 'icon' => '', 'class' => 'xl-header__cta' ) );

		ob_start();
		?>
		<a class="xl-skip" href="#xl-main"><?php esc_html_e( 'Skip to content', 'xlights-elementor-widgets' ); ?></a>
		<header class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-xl-header>
			<div class="xl-header__bar">
				<div class="xl-container xl-header__inner">
					<a class="xl-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: dealer name */ __( '%s home', 'xlights-elementor-widgets' ), Settings::get( 'dealer_name' ) ) ); ?>">
						<span class="xl-logo xl-logo--default"><?php echo self::logo( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="xl-logo xl-logo--overlay"><?php echo self::logo( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					</a>
					<nav class="xl-nav" aria-label="<?php esc_attr_e( 'Primary', 'xlights-elementor-widgets' ); ?>">
						<?php echo self::nav_list( $tree, $uid, 'desktop' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</nav>
					<div class="xl-header__actions">
						<?php if ( $opts['show_phone'] && '' !== trim( $phone ) ) : ?>
							<a class="xl-header__phone" href="<?php echo esc_url( Tokens::tel_href( $phone ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: phone number */ __( 'Call %s', 'xlights-elementor-widgets' ), $phone ) ); ?>"><?php echo Icons::svg( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span aria-hidden="true"><?php echo esc_html( $phone ); ?></span></a>
						<?php endif; ?>
						<?php echo $cta; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<button type="button" class="xl-header__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $uid ); ?>-drawer" data-xl-menu-open>
							<?php echo Icons::svg( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="xl-sr"><?php esc_html_e( 'Open menu', 'xlights-elementor-widgets' ); ?></span>
						</button>
					</div>
				</div>
			</div>
			<div class="xl-drawer" id="<?php echo esc_attr( $uid ); ?>-drawer" data-xl-drawer hidden>
				<div class="xl-drawer__backdrop" data-xl-menu-close></div>
				<div class="xl-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'xlights-elementor-widgets' ); ?>">
					<div class="xl-drawer__head">
						<a class="xl-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="xl-logo"><?php echo self::logo( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></a>
						<button type="button" class="xl-icon-btn" data-xl-menu-close><?php echo Icons::svg( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="xl-sr"><?php esc_html_e( 'Close menu', 'xlights-elementor-widgets' ); ?></span></button>
					</div>
					<nav class="xl-drawer__nav" aria-label="<?php esc_attr_e( 'Mobile', 'xlights-elementor-widgets' ); ?>">
						<?php echo self::nav_list( $tree, $uid . 'm', 'mobile' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</nav>
					<div class="xl-drawer__actions">
						<?php echo Html::button( $opts['cta_label'], $opts['cta_link'], 'primary', array( 'class' => 'xl-btn--block' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo Html::call_button( 'ghost', '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</div>
			</div>
		</header>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Accessible disclosure navigation list.
	 *
	 * @param array  $tree    Tree.
	 * @param string $uid     Unique prefix.
	 * @param string $context desktop|mobile.
	 * @return string
	 */
	private static function nav_list( array $tree, $uid, $context ) {
		$out = '<ul class="xl-nav__list" role="list">';
		foreach ( $tree as $i => $item ) {
			$has_sub = ! empty( $item['children'] );
			$current = self::is_current( $item['url'] );
			$in_sub  = false;
			if ( $has_sub ) {
				foreach ( $item['children'] as $child ) {
					if ( self::is_current( $child['url'] ) ) {
						$in_sub = true;
					}
				}
			}
			$cls  = 'xl-nav__item' . ( $has_sub ? ' has-sub' : '' ) . ( $current || $in_sub ? ' is-current' : '' );
			$out .= '<li class="' . esc_attr( $cls ) . '">';
			$out .= '<a class="xl-nav__link" href="' . esc_url( $item['url'] ) . '"' . ( $current ? ' aria-current="page"' : '' ) . ( ! empty( $item['target'] ) ? ' target="_blank" rel="noopener"' : '' ) . '>' . esc_html( $item['title'] ) . '</a>';
			if ( $has_sub ) {
				$sub_id = $uid . '-sub-' . $i;
				$out   .= '<button type="button" class="xl-nav__toggle" aria-expanded="false" aria-controls="' . esc_attr( $sub_id ) . '"><span class="xl-sr">' . esc_html( sprintf( /* translators: %s: menu item */ __( 'Show %s submenu', 'xlights-elementor-widgets' ), $item['title'] ) ) . '</span>' . Icons::svg( 'chevron-down' ) . '</button>';
				$out   .= '<ul class="xl-nav__sub" id="' . esc_attr( $sub_id ) . '" role="list">';
				foreach ( $item['children'] as $child ) {
					$c_current = self::is_current( $child['url'] );
					$out      .= '<li><a class="xl-nav__sublink" href="' . esc_url( $child['url'] ) . '"' . ( $c_current ? ' aria-current="page"' : '' ) . '>' . esc_html( $child['title'] ) . '</a></li>';
				}
				$out .= '</ul>';
			}
			$out .= '</li>';
		}
		return $out . '</ul>';
	}

	/* ------------------------------------------------------------------
	 * Footer
	 * ---------------------------------------------------------------- */

	/**
	 * Default footer columns.
	 *
	 * @return array[] title, links[]
	 */
	private static function default_columns() {
		$link = static function ( $key, $title = '' ) {
			$def = Page_Registry::get( $key );
			return array(
				'title' => '' !== $title ? $title : $def['title'],
				'url'   => Page_Registry::url( $key ),
			);
		};
		return array(
			array(
				'title' => __( 'Lighting Solutions', 'xlights-elementor-widgets' ),
				'links' => array(
					$link( 'soffit' ),
					$link( 'bistro' ),
					$link( 'deck' ),
					$link( 'landscape' ),
					$link( 'commercial' ),
				),
			),
			array(
				'title' => __( 'Company', 'xlights-elementor-widgets' ),
				'links' => array(
					$link( 'about' ),
					$link( 'app' ),
					$link( 'reviews' ),
					$link( 'projects', __( 'Projects / Gallery', 'xlights-elementor-widgets' ) ),
					$link( 'contact', __( 'Contact / Get a Quote', 'xlights-elementor-widgets' ) ),
				),
			),
		);
	}

	/**
	 * Render the footer.
	 *
	 * @param array $opts show_cta, menu.
	 * @return string
	 */
	public static function footer( array $opts = array() ) {
		self::$footer_done = true;
		Assets::enqueue();
		$opts = wp_parse_args(
			$opts,
			array(
				// Page Composer templates end with their own CTA; avoid a second one directly below it.
				'show_cta'   => ! self::is_composer_page(),
				'menu'       => self::MENU_FOOTER,
				'sticky_cta' => Settings::enabled( 'mobile_cta_bar' ),
			)
		);
		$s     = Settings::all();
		$tree  = self::menu_tree( $opts['menu'] );
		$cols  = array();
		if ( $tree ) {
			foreach ( $tree as $top ) {
				$cols[] = array(
					'title' => $top['title'],
					'links' => array_map(
						static function ( $c ) {
							return array(
								'title' => $c['title'],
								'url'   => $c['url'],
							);
						},
						$top['children']
					),
				);
			}
		} else {
			$cols = self::default_columns();
		}
		$areas   = Settings::lines( 'service_areas' );
		$owner   = '' !== trim( $s['legal_name'] ) ? $s['legal_name'] : $s['dealer_name'];
		$privacy = \XLights\Forms\Quote_Form_Handler::privacy_url();

		ob_start();
		?>
		<footer class="xl-root xl-footer xl-bg--dark">
			<?php if ( $opts['show_cta'] ) : ?>
				<div class="xl-footer__cta">
					<?php
					/**
					 * Background photo behind the footer call to action (decorative). Return '' for none.
					 *
					 * @param string $url Image URL or attachment ID.
					 */
					$cta_photo = apply_filters( 'xlights_footer_cta_image', Html::asset_image( 'photos/roofline-warm-white-modern.webp' ) );
					if ( $cta_photo && Html::has_image( $cta_photo ) ) :
						?>
						<div class="xl-footer__cta-media" aria-hidden="true">
							<?php
							echo Html::image( // phpcs:ignore WordPress.Security.EscapeOutput
								$cta_photo,
								array(
									'alt'   => '',
									'size'  => 'full',
									'sizes' => '100vw',
								)
							);
							?>
						</div>
					<?php endif; ?>
					<div class="xl-container xl-footer__cta-inner">
						<p class="xl-footer__cta-text"><?php echo esc_html( Tokens::replace( __( 'Ready to light up your home or business?', 'xlights-elementor-widgets' ) ) ); ?></p>
						<div class="xl-actions">
							<?php echo Html::button( $s['primary_cta_label'], '{quote_url}', 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo Html::call_button( 'ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<div class="xl-container xl-footer__main">
				<div class="xl-footer__brand">
					<a class="xl-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="xl-logo"><?php echo self::logo( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></a>
					<p><?php echo esc_html( Tokens::replace( $s['dealer_intro'] ) ); ?></p>
					<?php echo Social_Links::render(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php
					$dealer_url = isset( $s['dealer_link_url'] ) ? trim( (string) $s['dealer_link_url'] ) : '';
					if ( '' !== $dealer_url ) :
						$dealer_label = '' !== trim( (string) $s['dealer_link_label'] ) ? $s['dealer_link_label'] : __( 'Become an X LIGHTS dealer', 'xlights-elementor-widgets' );
						?>
						<a class="xl-footer__dealer" href="<?php echo esc_url( $dealer_url ); ?>" target="_blank" rel="noopener">
							<span class="xl-footer__dealer-text">
								<strong><?php echo esc_html( Tokens::replace( $dealer_label ) ); ?></strong>
								<small><?php esc_html_e( 'Dealer information and the dealer map on xlightsled.com', 'xlights-elementor-widgets' ); ?></small>
							</span>
							<?php echo Icons::svg( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="xl-sr"><?php esc_html_e( '(opens in a new tab)', 'xlights-elementor-widgets' ); ?></span>
						</a>
					<?php endif; ?>
				</div>
				<?php foreach ( $cols as $col ) : ?>
					<nav class="xl-footer__col" aria-label="<?php echo esc_attr( $col['title'] ); ?>">
						<h2 class="xl-footer__title"><?php echo esc_html( $col['title'] ); ?></h2>
						<ul role="list">
							<?php foreach ( $col['links'] as $l ) : ?>
								<li><a href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( $l['title'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endforeach; ?>
				<div class="xl-footer__col xl-footer__col--reach">
					<h2 class="xl-footer__title"><?php esc_html_e( 'Get in touch', 'xlights-elementor-widgets' ); ?></h2>
					<ul role="list" class="xl-footer__reach">
						<?php if ( '' !== trim( $s['phone'] ) ) : ?>
							<li><a class="xl-reach" href="<?php echo esc_url( Tokens::tel_href( $s['phone'] ) ); ?>"><span class="xl-reach__icon"><?php echo Icons::svg( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span class="xl-reach__text"><small><?php esc_html_e( 'Call', 'xlights-elementor-widgets' ); ?></small><span><?php echo esc_html( $s['phone'] ); ?></span></span></a></li>
						<?php endif; ?>
						<?php if ( ! empty( $s['show_email'] ) && is_email( $s['email'] ) ) : ?>
							<li><a class="xl-reach" href="<?php echo esc_url( 'mailto:' . antispambot( $s['email'] ) ); ?>"><span class="xl-reach__icon"><?php echo Icons::svg( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span class="xl-reach__text"><small><?php esc_html_e( 'Email', 'xlights-elementor-widgets' ); ?></small><span><?php echo esc_html( antispambot( $s['email'] ) ); ?></span></span></a></li>
						<?php endif; ?>
						<?php if ( ! empty( $s['show_address'] ) && '' !== trim( $s['address_street'] ) ) : ?>
							<li><span class="xl-reach"><span class="xl-reach__icon"><?php echo Icons::svg( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span class="xl-reach__text"><small><?php esc_html_e( 'Address', 'xlights-elementor-widgets' ); ?></small><span><?php echo esc_html( $s['address_street'] ); ?><br><?php echo esc_html( trim( $s['address_city'] . ', ' . $s['address_state'] . ' ' . $s['address_zip'], ', ' ) ); ?></span></span></span></li>
						<?php endif; ?>
						<?php if ( $areas ) : ?>
							<li><span class="xl-reach"><span class="xl-reach__icon"><?php echo Icons::svg( 'compass' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span class="xl-reach__text"><small><?php esc_html_e( 'Service areas', 'xlights-elementor-widgets' ); ?></small><span><?php echo esc_html( implode( ' · ', $areas ) ); ?></span></span></span></li>
						<?php endif; ?>
						<?php
						$hours = Settings::lines( 'hours' );
						if ( $hours ) :
							?>
							<li><span class="xl-reach"><span class="xl-reach__icon"><?php echo Icons::svg( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span class="xl-reach__text"><small><?php esc_html_e( 'Hours', 'xlights-elementor-widgets' ); ?></small><span><?php echo implode( '<br>', array_map( 'esc_html', $hours ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></span></span></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
			<div class="xl-footer__legal">
				<div class="xl-container xl-footer__legal-inner">
					<?php if ( '' !== trim( $s['footer_note'] ) ) : ?>
						<p class="xl-footer__note"><?php echo esc_html( Tokens::replace( $s['footer_note'] ) ); ?></p>
					<?php endif; ?>
					<p>
						<span>&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . $owner ); ?>. <?php esc_html_e( 'All rights reserved.', 'xlights-elementor-widgets' ); ?></span>
						<?php if ( $privacy ) : ?>
							<a href="<?php echo esc_url( $privacy ); ?>"><?php esc_html_e( 'Privacy Policy', 'xlights-elementor-widgets' ); ?></a>
						<?php endif; ?>
					</p>
				</div>
			</div>
		</footer>
		<?php
		if ( $opts['sticky_cta'] ) {
			echo self::sticky_cta( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		return (string) ob_get_clean();
	}

	/**
	 * Mobile call & quote bar. Hidden until JavaScript decides it is useful
	 * (phones only, after the first screen, never over a quote form).
	 *
	 * @param array $s Settings.
	 * @return string
	 */
	private static function sticky_cta( array $s ) {
		$phone = trim( (string) $s['phone'] );
		$label = '' !== trim( (string) $s['primary_cta_label'] ) ? $s['primary_cta_label'] : __( 'Get a Quote', 'xlights-elementor-widgets' );
		ob_start();
		?>
		<div class="xl-root xl-sticky-cta<?php echo '' === $phone ? ' xl-sticky-cta--single' : ''; ?>" data-xl-sticky-cta hidden>
			<?php if ( '' !== $phone ) : ?>
				<a class="xl-sticky-cta__btn xl-sticky-cta__btn--call" href="<?php echo esc_url( Tokens::tel_href( $phone ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: phone number */ __( 'Call %s', 'xlights-elementor-widgets' ), $phone ) ); ?>"><?php echo Icons::svg( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span aria-hidden="true"><?php esc_html_e( 'Call', 'xlights-elementor-widgets' ); ?></span></a>
			<?php endif; ?>
			<a class="xl-sticky-cta__btn xl-sticky-cta__btn--quote" href="<?php echo esc_url( Tokens::url( '{quote_url}' ) ); ?>"><?php echo esc_html( Tokens::replace( $label ) ); ?><?php echo Icons::svg( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
		<?php
		return (string) ob_get_clean();
	}
}
