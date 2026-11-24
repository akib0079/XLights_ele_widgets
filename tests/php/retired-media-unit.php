<?php
/**
 * Retired photos (snow, old control box) are never shown — no WordPress needed.
 *
 *   php tests/php/retired-media-unit.php xlights-elementor-widgets/includes/Helpers/Retired_Media.php
 */
define( 'ABSPATH', '/' );
function apply_filters( $h, $v ) { return $v; }
function absint( $v ) { return abs( (int) $v ); }
function get_attached_file( $id ) { return array( 7 => '/up/2026/10/imgi_93_4429fb_c22e9ab9a5ac48118dbafb119488a36fmv2.jpg', 8 => '/up/2026/10/Twilight-Craftsman-Home.webp' )[ $id ] ?? ''; }
function get_post_meta( $id, $k, $s ) { return ''; }
require $argv[1];
use XLights\Helpers\Retired_Media as R;
$fail = 0;
$t = function ( $c, $m ) use ( &$fail ) { echo ( $c ? 'PASS ' : 'FAIL ' ), $m, "\n"; if ( ! $c ) { $fail++; } };
$t( R::is_retired( 'https://socal.xlightsled.com/wp-content/uploads/2026/10/4429fb_324e31418fc4487a8dbf034140e1e54fmv2.avif' ), 'live Home CTA control-box photo is retired' );
$t( R::is_retired( array( 'url' => 'https://socal.xlightsled.com/wp-content/uploads/2026/10/imgi_93_4429fb_c22e9ab9a5ac48118dbafb119488a36fmv2-900x675.jpg', 'id' => '' ) ), 'live snow photo (by URL) is retired' );
$t( R::is_retired( 7 ), 'snow photo found by attachment file name' );
$t( ! R::is_retired( 8 ), 'live hero photo is not retired' );
$t( ! R::is_retired( 'https://socal.xlightsled.com/wp-content/uploads/2026/10/xlights-commercial-cottonmouth-roofline-900x675.webp' ), 'Cottonmouth photo is not retired' );
$t( ! R::is_retired( '' ) && ! R::is_retired( 0 ), 'empty values are not retired' );
$fields = array(
	'image' => array( 'type' => 'media' ),
	'items' => array( 'type' => 'repeater', 'fields' => array( 'image' => array( 'type' => 'media' ), 'title' => array( 'type' => 'text' ) ) ),
);
$data = array(
	'image' => array( 'url' => 'https://x/uploads/4429fb_324e31418fc4487a8dbf034140e1e54fmv2.avif', 'id' => '' ),
	'items' => array( array( 'title' => 'A', 'image' => array( 'url' => 'https://x/uploads/imgi_93_4429fb_c22e9ab9a5ac48118dbafb119488a36fmv2.jpg', 'id' => '' ) ), array( 'title' => 'B', 'image' => array( 'url' => 'https://x/ok.webp', 'id' => '' ) ) ),
);
$out = R::scrub( $data, $fields, array( 'image' => 'https://x/plugins/xlights-elementor-widgets/assets/images/photos/soffit-house-dusk-wide.webp' ) );
$t( false !== strpos( (string) $out['image'], 'soffit-house-dusk-wide' ), 'retired section image falls back to the section default photo' );
$t( '' === $out['items'][0]['image']['url'] && 'https://x/ok.webp' === $out['items'][1]['image']['url'], 'retired card photo cleared, other cards untouched' );
exit( $fail ? 1 : 0 );
