<?php
/**
 * Per-section render context passed to components and templates.
 *
 * @package XLights
 */

namespace XLights\Sections;

use XLights\Helpers\Context;

defined( 'ABSPATH' ) || exit;

final class Render_Context {

	/** @var string Page template key ('' for standalone widgets). */
	public $template = '';

	/** @var string Section key inside the template. */
	public $section = '';

	/** @var int Zero-based position on the page. */
	public $index = 0;

	/** @var string HTML id for the section. */
	public $anchor = '';

	/** @var bool Whether this section should own the page H1. */
	public $primary_heading = false;

	/** @var bool */
	public $editor = false;

	/** @var string composer|widget|shortcode */
	public $source = 'composer';

	/** @var int Page being rendered. */
	public $page_id = 0;

	/** @var string Unique instance prefix for ids. */
	public $uid = '';

	/**
	 * @param array $props Properties.
	 */
	public function __construct( array $props = array() ) {
		foreach ( $props as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->$key = $value;
			}
		}
		$this->editor = Context::is_editor();
		if ( '' === $this->uid ) {
			$this->uid = 'xl' . substr( md5( $this->template . '|' . $this->section . '|' . $this->index . '|' . wp_rand() ), 0, 6 );
		}
		if ( ! $this->page_id ) {
			$this->page_id = (int) get_queried_object_id();
		}
	}

	/**
	 * Is this the first section on the page (above the fold)?
	 *
	 * @return bool
	 */
	public function is_first() {
		return 0 === $this->index;
	}

	/**
	 * Unique id for an element inside this section.
	 *
	 * @param string $suffix Suffix.
	 * @return string
	 */
	public function id( $suffix ) {
		return $this->uid . '-' . sanitize_html_class( $suffix );
	}
}
