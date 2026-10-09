<?php
/**
 * X LIGHTS — Page Composer.
 *
 * One widget that renders a complete page from a chosen template. Each
 * template has its own section panels (prefixed control IDs), so editing the
 * Soffit page never changes Home. Sections can be hidden per device and
 * reordered by dragging rows in "Sections — Visibility & Order".
 *
 * Saved data contains only values that differ from defaults (Elementor strips
 * defaults on save), so plugin updates can improve default copy without
 * overwriting anything an editor changed.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use XLights\Elementor\Controls_Builder;
use XLights\Elementor\Integration;
use XLights\Elementor\Style_Controls;
use XLights\Pages\Page_Registry;
use XLights\Sections\Component_Registry;
use XLights\Sections\Section_Renderer;

defined( 'ABSPATH' ) || exit;

class Page_Composer extends Widget_Base {

	public function get_name() {
		return 'xlights-page-composer';
	}

	public function get_title() {
		return __( 'X LIGHTS — Page Composer', 'xlights-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-site-identity';
	}

	public function get_categories() {
		return array( Integration::CATEGORY );
	}

	public function get_keywords() {
		return array( 'xlights', 'x lights', 'page', 'composer', 'template', 'lighting' );
	}

	public function get_style_depends() {
		return array( 'xlights' );
	}

	public function get_script_depends() {
		return array( 'xlights' );
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * @param string $template Template.
	 * @param string $section  Section.
	 * @param string $field    Field.
	 * @return string
	 */
	public static function control_id( $template, $section, $field = '' ) {
		return $template . '__' . $section . ( '' !== $field ? '__' . $field : '' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'page_config',
			array(
				'label' => __( 'Page Configuration', 'xlights-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'page_template',
			array(
				'label'   => __( 'Page template', 'xlights-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'options' => Page_Registry::options(),
				'default' => 'home',
			)
		);
		$this->add_control(
			'page_help',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => wp_kses_post(
					'<p>' . __( 'Each template has its own sections below. Edits apply to this page only.', 'xlights-elementor-widgets' ) . '</p>'
					. '<p><strong>' . __( 'Tokens', 'xlights-elementor-widgets' ) . '</strong>: {dealer} {market} {area} {phone} {email} {brand} {cta}. '
					. __( 'Links', 'xlights-elementor-widgets' ) . ': {quote_url} {page:contact} {page:soffit} {tel}.</p>'
					. '<p>' . __( 'Dealer details, credentials and financing live in X LIGHTS → Settings. Items marked "pending" are visible here but hidden on the live site.', 'xlights-elementor-widgets' ) . '</p>'
					. '<p>' . __( 'Tip: place this widget in a full-width container with no padding.', 'xlights-elementor-widgets' ) . '</p>'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);
		$this->end_controls_section();

		// Section visibility & order: one sortable list per template.
		$this->start_controls_section(
			'sections_order',
			array(
				'label' => __( 'Sections — Visibility & Order', 'xlights-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'sections_order_help',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Drag rows to reorder. Open a row to hide the section or hide it on specific devices.', 'xlights-elementor-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		foreach ( Page_Registry::all() as $key => $page ) {
			$sections = Page_Registry::sections( $key );
			$default  = array();
			$n        = 0;
			foreach ( $sections as $sec_key => $def ) {
				$n++;
				$default[] = array(
					'section'      => $sec_key,
					'label'        => sprintf( '%02d · %s', $n, $def['label'] ),
					'enabled'      => 'yes',
					'hide_desktop' => '',
					'hide_tablet'  => '',
					'hide_mobile'  => '',
				);
			}
			$repeater = new \Elementor\Repeater();
			$repeater->add_control(
				'section',
				array(
					'type' => Controls_Manager::HIDDEN,
				)
			);
			$repeater->add_control(
				'label',
				array(
					'type' => Controls_Manager::HIDDEN,
				)
			);
			$repeater->add_control(
				'enabled',
				array(
					'label'        => __( 'Show section', 'xlights-elementor-widgets' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			foreach ( array(
				'hide_desktop' => __( 'Hide on desktop', 'xlights-elementor-widgets' ),
				'hide_tablet'  => __( 'Hide on tablet', 'xlights-elementor-widgets' ),
				'hide_mobile'  => __( 'Hide on mobile', 'xlights-elementor-widgets' ),
			) as $id => $label ) {
				$repeater->add_control(
					$id,
					array(
						'label'        => $label,
						'type'         => Controls_Manager::SWITCHER,
						'return_value' => 'yes',
						'default'      => '',
						'condition'    => array( 'enabled' => 'yes' ),
					)
				);
			}
			$this->add_control(
				self::control_id( $key, 'order' ),
				array(
					'label'         => sprintf( /* translators: %s: page */ __( '%s sections', 'xlights-elementor-widgets' ), $page['label'] ),
					'type'          => Controls_Manager::REPEATER,
					'fields'        => $repeater->get_controls(),
					'default'       => $default,
					'title_field'   => '{{{ label }}}{{{ "yes" === enabled ? "" : " — hidden" }}}',
					'prevent_empty' => false,
					'item_actions'  => array(
						'add'       => false,
						'duplicate' => false,
						'remove'    => false,
						'sort'      => true,
					),
					'condition'     => array( 'page_template' => $key ),
				)
			);
		}
		$this->end_controls_section();

		// Per-template section panels.
		foreach ( Page_Registry::all() as $key => $page ) {
			$n = 0;
			foreach ( Page_Registry::sections( $key ) as $sec_key => $def ) {
				$n++;
				$component = Component_Registry::get( $def['type'] );
				if ( ! $component ) {
					continue;
				}
				$this->start_controls_section(
					self::control_id( $key, $sec_key ),
					array(
						'label'     => sprintf( '%02d · %s', $n, $def['label'] ),
						'tab'       => Controls_Manager::TAB_CONTENT,
						'condition' => array( 'page_template' => $key ),
					)
				);
				Controls_Builder::add( $this, $component->fields(), self::control_id( $key, $sec_key ) . '__', isset( $def['defaults'] ) ? $def['defaults'] : array() );
				$this->end_controls_section();
			}
		}

		Style_Controls::register( $this );
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$template = isset( $settings['page_template'] ) && Page_Registry::exists( $settings['page_template'] ) ? $settings['page_template'] : 'home';
		$order    = isset( $settings[ self::control_id( $template, 'order' ) ] ) && is_array( $settings[ self::control_id( $template, 'order' ) ] ) ? $settings[ self::control_id( $template, 'order' ) ] : array();

		$html = Section_Renderer::page(
			$template,
			static function ( $section, $def ) use ( $settings, $template ) {
				$component = Component_Registry::get( $def['type'] );
				$data      = array();
				$prefix    = Page_Composer::control_id( $template, $section ) . '__';
				foreach ( array_keys( $component->fields() ) as $field ) {
					if ( array_key_exists( $prefix . $field, $settings ) ) {
						$data[ $field ] = $settings[ $prefix . $field ];
					} elseif ( isset( $def['defaults'][ $field ] ) ) {
						$data[ $field ] = $def['defaults'][ $field ];
					}
				}
				// Values that exist only as template defaults (not editable controls).
				foreach ( (array) ( isset( $def['defaults'] ) ? $def['defaults'] : array() ) as $field => $value ) {
					if ( ! array_key_exists( $field, $data ) ) {
						$data[ $field ] = $value;
					}
				}
				return $data;
			},
			$order,
			array(
				'source'  => 'composer',
				'page_id' => (int) get_the_ID(),
			)
		);

		printf(
			'<div class="xl-root xl-root--page %1$s" data-xl-template="%2$s">%3$s</div>',
			esc_attr( Style_Controls::root_classes( $settings ) ),
			esc_attr( $template ),
			$html // phpcs:ignore WordPress.Security.EscapeOutput -- Components escape their own output.
		);
	}
}
