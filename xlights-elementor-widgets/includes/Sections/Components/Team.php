<?php
/**
 * Local team members. Starts empty: real names, roles and photos must come
 * from the dealer. Hidden on the live site until members are added.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Context;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Team extends Component {

	public function type() {
		return 'team';
	}

	public function label() {
		return __( 'Local Team', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-person';
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'members' => array(
					'type'        => 'repeater',
					'label'       => __( 'Team members', 'xlights-elementor-widgets' ),
					'title_field' => '{{{ name }}}',
					'separator'   => 'before',
					'description' => __( 'Add approved names, roles, bios and photos supplied by the dealer.', 'xlights-elementor-widgets' ),
					'fields'      => array(
						'name'  => self::text( __( 'Name', 'xlights-elementor-widgets' ) ),
						'role'  => self::text( __( 'Role', 'xlights-elementor-widgets' ) ),
						'bio'   => self::textarea( __( 'Short bio', 'xlights-elementor-widgets' ) ),
						'photo' => self::media( __( 'Photo', 'xlights-elementor-widgets' ) ),
						'gate'  => self::gate_field(),
					),
					'default'     => array(),
				),
			)
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$members = array();
		foreach ( (array) $data['members'] as $m ) {
			$m = wp_parse_args(
				(array) $m,
				array(
					'name'  => '',
					'role'  => '',
					'bio'   => '',
					'photo' => '',
					'gate'  => '',
				)
			);
			if ( '' === trim( $m['name'] ) ) {
				continue;
			}
			$m['visibility'] = Context::visibility( $m['gate'] );
			if ( Context::HIDE !== $m['visibility'] ) {
				$members[] = $m;
			}
		}
		if ( ! $members && ! $ctx->editor ) {
			return null;
		}
		$data['members'] = $members;
		return $data;
	}
}
