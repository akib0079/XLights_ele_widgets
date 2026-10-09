<?php
/**
 * Service-area list with a click-to-load map (no third-party requests until asked).
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Html;
use XLights\Settings\Settings;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Service_Area extends Component {

	public function type() {
		return 'service_area';
	}

	public function label() {
		return __( 'Service Area & Map', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-google-maps';
	}

	public function backgrounds() {
		return array( 'warm', 'light', 'dark' );
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'areas'      => self::textarea(
					__( 'Areas (one per line, blank = settings)', 'xlights-elementor-widgets' ),
					'',
					4,
					array(
						'separator'   => 'before',
						'description' => __( 'List only confirmed service areas.', 'xlights-elementor-widgets' ),
					)
				),
				'show_map'   => self::toggle( __( 'Show map', 'xlights-elementor-widgets' ), true ),
				'map_query'  => self::text( __( 'Map location (blank = settings)', 'xlights-elementor-widgets' ) ),
				'link_label' => self::text( __( 'Link label', 'xlights-elementor-widgets' ), '', array( 'separator' => 'before' ) ),
				'link'       => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
			)
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$areas         = Html::lines( $data['areas'] );
		$data['areas'] = $areas ? $areas : Settings::lines( 'service_areas' );
		$data['note']  = Settings::get( 'service_area_note' );

		$data['map_url'] = '';
		if ( 'yes' === $data['show_map'] && Settings::enabled( 'map_enabled' ) ) {
			$custom = (string) Settings::get( 'map_embed_url' );
			if ( '' !== $custom ) {
				$data['map_url'] = $custom;
			} else {
				$query = trim( (string) $data['map_query'] );
				$query = '' !== $query ? $query : (string) Settings::get( 'map_query' );
				if ( '' !== $query ) {
					$data['map_url'] = 'https://www.google.com/maps?q=' . rawurlencode( $query ) . '&output=embed';
				}
			}
		}
		if ( ! $data['areas'] && '' === $data['map_url'] && ! $ctx->editor ) {
			return null;
		}
		return $data;
	}
}
