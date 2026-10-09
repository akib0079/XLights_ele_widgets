<?php
/**
 * Controlled page setup: creates the twelve dealer pages with a Page Composer
 * already configured, without overwriting existing content unless explicitly
 * requested per page (in which case the previous Elementor data is backed up).
 *
 * @package XLights
 */

namespace XLights\Setup;

use XLights\Frontend\Header_Footer;
use XLights\Helpers\Tokens;
use XLights\Pages\Page_Registry;
use XLights\PostTypes\Projects;
use XLights\SEO\Meta;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Page_Setup {

	const BACKUP_META = '_xlights_backup_elementor_data';

	/**
	 * Current state of every template page.
	 *
	 * @return array[]
	 */
	public static function status() {
		$out = array();
		foreach ( Page_Registry::all() as $key => $page ) {
			$id     = Page_Registry::page_id( $key );
			$post   = $id ? get_post( $id ) : null;
			$data   = $id ? (string) get_post_meta( $id, '_elementor_data', true ) : '';
			$out[ $key ] = array(
				'key'          => $key,
				'label'        => $page['label'],
				'path'         => $page['path'],
				'id'           => $id,
				'exists'       => (bool) $post,
				'status'       => $post ? $post->post_status : '',
				'has_composer' => false !== strpos( $data, 'xlights-page-composer' ),
				'has_content'  => $post && ( '' !== trim( $post->post_content ) || '' !== $data ),
				'edit'         => $id ? get_edit_post_link( $id, 'raw' ) : '',
				'view'         => $id ? get_permalink( $id ) : '',
			);
		}
		return $out;
	}

	/**
	 * Create/configure pages.
	 *
	 * @param array $keys Templates to process.
	 * @param array $opts publish, overwrite (keys), front_page, menu, template (canvas|header_footer).
	 * @return array Report lines: [type, message].
	 */
	public static function run( array $keys, array $opts = array() ) {
		$opts   = wp_parse_args(
			$opts,
			array(
				'publish'    => false,
				'overwrite'  => array(),
				'front_page' => false,
				'menu'       => false,
				'template'   => 'canvas',
			)
		);
		$report = array();
		$keys   = array_values( array_intersect( array_keys( Page_Registry::all() ), $keys ) );

		$created = Projects::ensure_terms();
		if ( $created ) {
			/* translators: %d: number of terms */
			$report[] = array( 'success', sprintf( __( 'Created %d project categories.', 'xlights-elementor-widgets' ), $created ) );
		}

		// Parents first so children get the right hierarchy.
		usort(
			$keys,
			static function ( $a, $b ) {
				$pa = Page_Registry::get( $a );
				$pb = Page_Registry::get( $b );
				return ( '' === $pa['parent'] ? 0 : 1 ) - ( '' === $pb['parent'] ? 0 : 1 );
			}
		);

		foreach ( $keys as $key ) {
			$report[] = self::setup_page( $key, $opts );
		}

		if ( $opts['front_page'] && in_array( 'home', $keys, true ) ) {
			$home = Page_Registry::page_id( 'home' );
			if ( $home ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $home );
				$report[] = array( 'success', __( 'Home is now the site front page.', 'xlights-elementor-widgets' ) );
			}
		}

		if ( $opts['menu'] ) {
			$report[] = self::create_menu();
		}

		if ( 'canvas' === $opts['template'] && 'off' !== Settings::get( 'header_footer_mode' ) && 'global' !== Settings::get( 'header_footer_mode' ) ) {
			Settings::update( array( 'header_footer_mode' => 'canvas' ) );
		}

		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
		flush_rewrite_rules();
		return $report;
	}

	/**
	 * @param string $key  Template.
	 * @param array  $opts Options.
	 * @return array Report line.
	 */
	private static function setup_page( $key, array $opts ) {
		$page     = Page_Registry::get( $key );
		$existing = Page_Registry::page_id( $key );
		$overwrite = in_array( $key, (array) $opts['overwrite'], true );

		if ( $existing ) {
			Page_Registry::set_page( $key, $existing );
			$post        = get_post( $existing );
			$data        = (string) get_post_meta( $existing, '_elementor_data', true );
			$has_content = '' !== trim( $post->post_content ) || '' !== $data;
			if ( $has_content && ! $overwrite ) {
				/* translators: 1: page title, 2: page ID */
				return array( 'info', sprintf( __( '"%1$s" already exists (ID %2$d) and was left unchanged.', 'xlights-elementor-widgets' ), $post->post_title, $existing ) );
			}
			if ( '' !== $data ) {
				update_post_meta( $existing, self::BACKUP_META, wp_slash( $data ) );
			}
			self::apply_composer( $existing, $key, $opts );
			/* translators: %s: page title */
			return array( 'success', sprintf( __( '"%s" was configured with the Page Composer (previous Elementor content backed up).', 'xlights-elementor-widgets' ), $post->post_title ) );
		}

		$parent_id = '' !== $page['parent'] ? Page_Registry::page_id( $page['parent'] ) : 0;
		$id        = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => $page['title'],
				'post_name'    => $page['slug'],
				'post_parent'  => $parent_id,
				'post_status'  => $opts['publish'] ? 'publish' : 'draft',
				'post_content' => '[xlights_page template="' . $key . '"]',
				'menu_order'   => array_search( $key, array_keys( Page_Registry::all() ), true ),
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			/* translators: 1: page, 2: error */
			return array( 'error', sprintf( __( 'Could not create "%1$s": %2$s', 'xlights-elementor-widgets' ), $page['title'], $id->get_error_message() ) );
		}
		Page_Registry::set_page( $key, $id );
		self::apply_composer( $id, $key, $opts );
		return array(
			'success',
			sprintf(
				/* translators: 1: page title, 2: status */
				__( 'Created "%1$s" (%2$s).', 'xlights-elementor-widgets' ),
				$page['title'],
				$opts['publish'] ? __( 'published', 'xlights-elementor-widgets' ) : __( 'draft', 'xlights-elementor-widgets' )
			),
		);
	}

	/**
	 * Write Elementor data containing one Page Composer widget.
	 *
	 * @param int    $post_id Page.
	 * @param string $key     Template.
	 * @param array  $opts    Options.
	 */
	private static function apply_composer( $post_id, $key, array $opts ) {
		$page = Page_Registry::get( $key );
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
		}
		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( self::elementor_data( $key ) ) ) );
		update_post_meta( $post_id, '_wp_page_template', 'canvas' === $opts['template'] ? 'elementor_canvas' : 'elementor_header_footer' );
		delete_post_meta( $post_id, '_elementor_css' );

		$description = Tokens::replace( $page['description'] );
		$title       = Tokens::replace( $page['seo_title'] );
		update_post_meta( $post_id, Meta::META_KEY, $description );
		if ( defined( 'WPSEO_VERSION' ) ) {
			if ( '' === get_post_meta( $post_id, '_yoast_wpseo_metadesc', true ) ) {
				update_post_meta( $post_id, '_yoast_wpseo_metadesc', $description );
			}
			if ( '' === get_post_meta( $post_id, '_yoast_wpseo_title', true ) ) {
				update_post_meta( $post_id, '_yoast_wpseo_title', $title );
			}
		}
		if ( defined( 'RANK_MATH_VERSION' ) ) {
			if ( '' === get_post_meta( $post_id, 'rank_math_description', true ) ) {
				update_post_meta( $post_id, 'rank_math_description', $description );
			}
			if ( '' === get_post_meta( $post_id, 'rank_math_title', true ) ) {
				update_post_meta( $post_id, 'rank_math_title', $title );
			}
		}
	}

	/**
	 * @return string Random Elementor element ID.
	 */
	private static function element_id() {
		return substr( bin2hex( random_bytes( 4 ) ), 0, 7 );
	}

	/**
	 * Elementor document data with a full-width container and the composer.
	 *
	 * @param string $key Template.
	 * @return array
	 */
	public static function elementor_data( $key ) {
		$widget = array(
			'id'         => self::element_id(),
			'elType'     => 'widget',
			'widgetType' => 'xlights-page-composer',
			'settings'   => array( 'page_template' => $key ),
			'elements'   => array(),
		);
		$zero   = array(
			'unit'     => 'px',
			'top'      => '0',
			'right'    => '0',
			'bottom'   => '0',
			'left'     => '0',
			'isLinked' => true,
		);
		$containers = true;
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->experiments ) ) {
			$containers = \Elementor\Plugin::$instance->experiments->is_feature_active( 'container' );
		}
		if ( $containers ) {
			return array(
				array(
					'id'       => self::element_id(),
					'elType'   => 'container',
					'settings' => array(
						'content_width' => 'full',
						'padding'       => $zero,
						'flex_gap'      => array(
							'unit'   => 'px',
							'size'   => 0,
							'column' => '0',
							'row'    => '0',
						),
					),
					'elements' => array( $widget ),
					'isInner'  => false,
				),
			);
		}
		return array(
			array(
				'id'       => self::element_id(),
				'elType'   => 'section',
				'settings' => array(
					'layout' => 'full_width',
					'gap'    => 'no',
				),
				'elements' => array(
					array(
						'id'       => self::element_id(),
						'elType'   => 'column',
						'settings' => array(
							'_column_size' => 100,
							'padding'      => $zero,
						),
						'elements' => array( $widget ),
						'isInner'  => false,
					),
				),
				'isInner'  => false,
			),
		);
	}

	/**
	 * Create and assign the header menu if none is assigned yet.
	 *
	 * @return array Report line.
	 */
	public static function create_menu() {
		$locations = get_nav_menu_locations();
		if ( ! empty( $locations[ Header_Footer::MENU_PRIMARY ] ) ) {
			return array( 'info', __( 'A header menu is already assigned; it was left unchanged.', 'xlights-elementor-widgets' ) );
		}
		$name    = __( 'X LIGHTS Header', 'xlights-elementor-widgets' );
		$menu    = wp_get_nav_menu_object( $name );
		$menu_id = $menu ? (int) $menu->term_id : wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			return array( 'error', $menu_id->get_error_message() );
		}
		if ( ! $menu || ! wp_get_nav_menu_items( $menu_id ) ) {
			$add = static function ( $key, $parent = 0, $title = '' ) use ( $menu_id ) {
				$id   = Page_Registry::page_id( $key );
				$args = array(
					'menu-item-title'     => '' !== $title ? $title : Page_Registry::get( $key )['title'],
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => $parent,
				);
				if ( $id ) {
					$args['menu-item-object-id'] = $id;
					$args['menu-item-object']    = 'page';
					$args['menu-item-type']      = 'post_type';
				} else {
					$args['menu-item-url']  = Page_Registry::url( $key );
					$args['menu-item-type'] = 'custom';
				}
				return (int) wp_update_nav_menu_item( $menu_id, 0, $args );
			};
			$add( 'home' );
			$about = $add( 'about' );
			$add( 'about', $about );
			$add( 'app', $about );
			$add( 'reviews', $about );
			$solutions = $add( 'solutions' );
			$add( 'solutions', $solutions, __( 'All Lighting Solutions', 'xlights-elementor-widgets' ) );
			$add( 'soffit', $solutions );
			$add( 'bistro', $solutions );
			$add( 'deck', $solutions );
			$add( 'landscape', $solutions );
			$add( 'commercial' );
			$add( 'projects', 0, __( 'Projects / Gallery', 'xlights-elementor-widgets' ) );
			$add( 'contact' );
		}
		$locations[ Header_Footer::MENU_PRIMARY ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		return array( 'success', __( 'Created the "X LIGHTS Header" menu and assigned it to the header location.', 'xlights-elementor-widgets' ) );
	}
}
