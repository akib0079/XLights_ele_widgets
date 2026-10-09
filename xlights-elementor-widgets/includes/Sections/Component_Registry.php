<?php
/**
 * Registry of section components.
 *
 * @package XLights
 */

namespace XLights\Sections;

defined( 'ABSPATH' ) || exit;

final class Component_Registry {

	/** @var Component[]|null */
	private static $components = null;

	/**
	 * @return array type => class
	 */
	private static function classes() {
		$classes = array(
			'hero'          => Components\Hero::class,
			'intro'         => Components\Intro::class,
			'split'         => Components\Split::class,
			'cards'         => Components\Cards::class,
			'connected'     => Components\Connected::class,
			'app_showcase'  => Components\App_Showcase::class,
			'projects'      => Components\Projects::class,
			'before_after'  => Components\Before_After::class,
			'reviews'       => Components\Reviews::class,
			'financing'     => Components\Financing::class,
			'faq'           => Components\Faq::class,
			'service_area'  => Components\Service_Area::class,
			'contact_info'  => Components\Contact_Info::class,
			'quote_form'    => Components\Quote_Form::class,
			'cta'           => Components\Cta::class,
			'team'          => Components\Team::class,
			'specs'         => Components\Specs::class,
			'trust'         => Components\Trust::class,
		);
		/**
		 * Filter registered section components (type => class extending Component).
		 *
		 * @param array $classes
		 */
		return apply_filters( 'xlights/components', $classes );
	}

	/**
	 * @return Component[]
	 */
	public static function all() {
		if ( null === self::$components ) {
			self::$components = array();
			foreach ( self::classes() as $type => $class ) {
				if ( class_exists( $class ) && is_subclass_of( $class, Component::class ) ) {
					self::$components[ $type ] = new $class();
				}
			}
		}
		return self::$components;
	}

	/**
	 * @param string $type Component type.
	 * @return Component|null
	 */
	public static function get( $type ) {
		$all = self::all();
		return isset( $all[ $type ] ) ? $all[ $type ] : null;
	}
}
