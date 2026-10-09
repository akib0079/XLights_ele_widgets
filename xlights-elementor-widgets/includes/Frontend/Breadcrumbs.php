<?php
/**
 * Breadcrumb trail for inner pages, plus BreadcrumbList data for Schema.
 *
 * @package XLights
 */

namespace XLights\Frontend;

use XLights\Pages\Page_Registry;
use XLights\SEO\Schema;

defined( 'ABSPATH' ) || exit;

final class Breadcrumbs {

	/**
	 * Trail items: [ [label, url], ... ].
	 *
	 * @param int    $page_id  Current page.
	 * @param string $template Template key (fallback when previewing unsaved pages).
	 * @return array
	 */
	public static function items( $page_id, $template = '' ) {
		$page_id = (int) $page_id;
		if ( ( $page_id && (int) get_option( 'page_on_front' ) === $page_id ) || 'home' === $template ) {
			return array();
		}
		$items = array( array( __( 'Home', 'xlights-elementor-widgets' ), home_url( '/' ) ) );

		$post = $page_id ? get_post( $page_id ) : null;
		if ( $post && 'page' === $post->post_type ) {
			foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor ) {
				$items[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
			}
			$items[] = array( get_the_title( $post ), get_permalink( $post ) );
			return $items;
		}
		if ( $template && ( $def = Page_Registry::get( $template ) ) ) {
			if ( ! empty( $def['parent'] ) && ( $parent = Page_Registry::get( $def['parent'] ) ) ) {
				$items[] = array( $parent['title'], Page_Registry::url( $def['parent'] ) );
			}
			$items[] = array( $def['title'], Page_Registry::url( $template ) );
			return $items;
		}
		return array();
	}

	/**
	 * @param int    $page_id  Page.
	 * @param string $template Template key.
	 * @return string
	 */
	public static function render( $page_id, $template = '' ) {
		$items = self::items( $page_id, $template );
		if ( count( $items ) < 2 ) {
			return '';
		}
		Schema::add_breadcrumbs( $items );
		$last = count( $items ) - 1;
		$out  = '<nav class="xl-crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'xlights-elementor-widgets' ) . '"><ol>';
		foreach ( $items as $i => $item ) {
			if ( $i === $last ) {
				$out .= '<li><span aria-current="page">' . esc_html( $item[0] ) . '</span></li>';
			} else {
				$out .= '<li><a href="' . esc_url( $item[1] ) . '">' . esc_html( $item[0] ) . '</a></li>';
			}
		}
		return $out . '</ol></nav>';
	}
}
