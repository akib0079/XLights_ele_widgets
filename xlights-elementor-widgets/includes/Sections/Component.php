<?php
/**
 * Base class for reusable front-end section components.
 *
 * A component declares a field schema (turned into Elementor controls by
 * Elementor\Controls_Builder for both the Page Composer and standalone widgets),
 * normalizes data in prepare(), and renders templates/sections/{type}.php.
 * Components have no dependency on Elementor, so they also render through
 * shortcodes and the setup fallback.
 *
 * @package XLights
 */

namespace XLights\Sections;

use XLights\Helpers\Icons;
use XLights\Helpers\Illustrations;

defined( 'ABSPATH' ) || exit;

abstract class Component {

	/** @var array|null */
	private $fields_cache = null;

	/**
	 * @return string Machine name.
	 */
	abstract public function type();

	/**
	 * @return string Human label.
	 */
	abstract public function label();

	/**
	 * @return array Field schema (without common section fields).
	 */
	abstract protected function define_fields();

	/**
	 * Elementor panel icon.
	 *
	 * @return string
	 */
	public function icon() {
		return 'eicon-section';
	}

	/**
	 * Background styles this component supports (first = default).
	 *
	 * @return array
	 */
	public function backgrounds() {
		return array( 'light', 'warm', 'dark' );
	}

	/**
	 * Full schema including common section fields.
	 *
	 * @return array
	 */
	public function fields() {
		if ( null === $this->fields_cache ) {
			$fields = $this->define_fields();
			$bgs    = $this->backgrounds();
			if ( count( $bgs ) > 1 ) {
				$labels       = self::background_labels();
				$fields['bg'] = array(
					'type'      => 'select',
					'label'     => __( 'Section background', 'xlights-elementor-widgets' ),
					'default'   => $bgs[0],
					'options'   => array_intersect_key( $labels, array_flip( $bgs ) ),
					'separator' => 'before',
				);
			}
			$fields['spacing'] = array(
				'type'    => 'select',
				'label'   => __( 'Vertical spacing', 'xlights-elementor-widgets' ),
				'default' => 'default',
				'options' => array(
					'default'  => __( 'Default', 'xlights-elementor-widgets' ),
					'compact'  => __( 'Compact', 'xlights-elementor-widgets' ),
					'spacious' => __( 'Spacious', 'xlights-elementor-widgets' ),
					'flush'    => __( 'None', 'xlights-elementor-widgets' ),
				),
			);
			$this->fields_cache = $fields;
		}
		return $this->fields_cache;
	}

	/**
	 * @return array
	 */
	public static function background_labels() {
		return array(
			'light' => __( 'Light', 'xlights-elementor-widgets' ),
			'warm'  => __( 'Light grey', 'xlights-elementor-widgets' ),
			'dark'  => __( 'Dark (navy)', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * Default values for every field.
	 *
	 * @return array
	 */
	public function defaults() {
		$out = array();
		foreach ( $this->fields() as $key => $field ) {
			if ( isset( $field['default'] ) ) {
				$out[ $key ] = $field['default'];
			}
		}
		return $out;
	}

	/**
	 * Normalize data before rendering. Return null to skip the section.
	 *
	 * @param array          $data Data.
	 * @param Render_Context $ctx  Context.
	 * @return array|null
	 */
	public function prepare( array $data, Render_Context $ctx ) {
		return $data;
	}

	/**
	 * Extra classes for the section wrapper.
	 *
	 * @param array $data Prepared data.
	 * @return string[]
	 */
	public function wrapper_classes( array $data ) {
		return array();
	}

	/**
	 * Render the component's inner markup.
	 *
	 * @param array          $data Prepared data.
	 * @param Render_Context $ctx  Context.
	 * @return string
	 */
	public function render( array $data, Render_Context $ctx ) {
		$file = XLIGHTS_PATH . 'templates/sections/' . $this->type() . '.php';
		/**
		 * Filter the template file used for a component (theme overrides).
		 *
		 * @param string    $file Template path.
		 * @param Component $this Component.
		 */
		$file = apply_filters( 'xlights/section_template', $file, $this );
		if ( ! is_readable( $file ) ) {
			return '';
		}
		$c = $this;
		ob_start();
		include $file;
		return (string) ob_get_clean();
	}

	/* ---------------------------------------------------------------------
	 * Field factories (keep component schemas compact and consistent).
	 * ------------------------------------------------------------------ */

	/**
	 * @param string $label   Label.
	 * @param string $default Default.
	 * @param array  $extra   Extra keys.
	 * @return array
	 */
	protected static function text( $label, $default = '', array $extra = array() ) {
		return array_merge(
			array(
				'type'    => 'text',
				'label'   => $label,
				'default' => $default,
			),
			$extra
		);
	}

	/**
	 * @param string $label   Label.
	 * @param string $default Default.
	 * @param int    $rows    Rows.
	 * @param array  $extra   Extra.
	 * @return array
	 */
	protected static function textarea( $label, $default = '', $rows = 3, array $extra = array() ) {
		return array_merge(
			array(
				'type'    => 'textarea',
				'label'   => $label,
				'default' => $default,
				'rows'    => $rows,
			),
			$extra
		);
	}

	/**
	 * @param string $label   Label.
	 * @param string $default Default.
	 * @param array  $extra   Extra.
	 * @return array
	 */
	protected static function rich( $label, $default = '', array $extra = array() ) {
		return array_merge(
			array(
				'type'    => 'wysiwyg',
				'label'   => $label,
				'default' => $default,
			),
			$extra
		);
	}

	/**
	 * @param string $label   Label.
	 * @param string $default Default URL or token.
	 * @param array  $extra   Extra.
	 * @return array
	 */
	protected static function url( $label, $default = '', array $extra = array() ) {
		return array_merge(
			array(
				'type'    => 'url',
				'label'   => $label,
				'default' => $default,
			),
			$extra
		);
	}

	/**
	 * @param string $label   Label.
	 * @param array  $options Options.
	 * @param string $default Default.
	 * @param array  $extra   Extra.
	 * @return array
	 */
	protected static function select( $label, array $options, $default, array $extra = array() ) {
		return array_merge(
			array(
				'type'    => 'select',
				'label'   => $label,
				'options' => $options,
				'default' => $default,
			),
			$extra
		);
	}

	/**
	 * @param string $label   Label.
	 * @param bool   $default Default on/off.
	 * @param array  $extra   Extra.
	 * @return array
	 */
	protected static function toggle( $label, $default = false, array $extra = array() ) {
		return array_merge(
			array(
				'type'    => 'switcher',
				'label'   => $label,
				'default' => $default ? 'yes' : '',
			),
			$extra
		);
	}

	/**
	 * @param string $label   Label.
	 * @param string $default Default URL.
	 * @param array  $extra   Extra.
	 * @return array
	 */
	protected static function media( $label, $default = '', array $extra = array() ) {
		return array_merge(
			array(
				'type'    => 'media',
				'label'   => $label,
				'default' => $default,
			),
			$extra
		);
	}

	/**
	 * Separator heading inside a section panel.
	 *
	 * @param string $label Label.
	 * @return array
	 */
	protected static function heading( $label ) {
		return array(
			'type'      => 'heading',
			'label'     => $label,
			'separator' => 'before',
		);
	}

	/**
	 * Eyebrow / heading / intro fields.
	 *
	 * @param bool $rich Whether the intro is rich text.
	 * @return array
	 */
	protected static function head_fields( $rich = true ) {
		return array(
			'eyebrow' => self::text( __( 'Eyebrow', 'xlights-elementor-widgets' ) ),
			'heading' => self::textarea(
				__( 'Heading', 'xlights-elementor-widgets' ),
				'',
				2,
				array( 'description' => __( 'Wrap words in <em></em> to highlight them. Tokens: {dealer} {market} {area} {phone} {brand}.', 'xlights-elementor-widgets' ) )
			),
			'text'    => $rich ? self::rich( __( 'Intro text', 'xlights-elementor-widgets' ) ) : self::textarea( __( 'Intro text', 'xlights-elementor-widgets' ) ),
		);
	}

	/**
	 * Button pair fields.
	 *
	 * @param string $prefix Field prefix (primary/secondary).
	 * @param string $label  Group label.
	 * @return array
	 */
	protected static function button_fields( $prefix, $label ) {
		return array(
			$prefix . '_label' => self::text( $label, '', array( 'separator' => 'before' ) ),
			$prefix . '_link'  => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
		);
	}

	/**
	 * Visual picker: uploaded image, app-screen mockup or illustration.
	 *
	 * @param string $default Default visual.
	 * @param bool   $allow_none Allow "none".
	 * @return array
	 */
	protected static function visual_fields( $default = 'image', $allow_none = false ) {
		$options = array(
			'image'        => __( 'Photo', 'xlights-elementor-widgets' ),
			'phone'        => __( 'App screen mockup', 'xlights-elementor-widgets' ),
			'illustration' => __( 'Illustration', 'xlights-elementor-widgets' ),
		);
		if ( $allow_none ) {
			$options['none'] = __( 'None (text only)', 'xlights-elementor-widgets' );
		}
		return array(
			'visual'     => self::select( __( 'Visual', 'xlights-elementor-widgets' ), $options, $default, array( 'separator' => 'before' ) ),
			'image'      => self::media(
				__( 'Photo', 'xlights-elementor-widgets' ),
				'',
				array(
					'condition'   => array( 'visual' => 'image' ),
					'description' => __( 'Use approved photography only. Until a photo is added, the illustration below is shown.', 'xlights-elementor-widgets' ),
				)
			),
			'screen'     => self::select( __( 'App screen', 'xlights-elementor-widgets' ), Illustrations::screens(), 'zones', array( 'condition' => array( 'visual' => 'phone' ) ) ),
			'screenshot' => self::media(
				__( 'Real app screenshot (optional)', 'xlights-elementor-widgets' ),
				'',
				array( 'condition' => array( 'visual' => 'phone' ) )
			),
			'scene'      => self::select(
				__( 'Illustration / photo fallback', 'xlights-elementor-widgets' ),
				Illustrations::scenes(),
				'roofline',
				array( 'condition' => array( 'visual' => array( 'image', 'illustration' ) ) )
			),
			'palette'    => self::select(
				__( 'Illustration light color', 'xlights-elementor-widgets' ),
				Illustrations::palettes(),
				'warm',
				array( 'condition' => array( 'visual' => array( 'image', 'illustration' ) ) )
			),
		);
	}

	/**
	 * Approval gate select.
	 *
	 * @param string $default Default gate.
	 * @return array
	 */
	protected static function gate_field( $default = '' ) {
		return self::select(
			__( 'Publication', 'xlights-elementor-widgets' ),
			array(
				''            => __( 'Always show', 'xlights-elementor-widgets' ),
				'credentials' => __( 'Only when credentials are verified', 'xlights-elementor-widgets' ),
				'financing'   => __( 'Only when financing is approved', 'xlights-elementor-widgets' ),
				'pending'     => __( 'Pending client approval (hidden)', 'xlights-elementor-widgets' ),
			),
			$default
		);
	}

	/**
	 * Icon select field.
	 *
	 * @param string $default Default icon.
	 * @return array
	 */
	protected static function icon_field( $default = '' ) {
		return self::select( __( 'Icon', 'xlights-elementor-widgets' ), Icons::options(), $default );
	}
}
