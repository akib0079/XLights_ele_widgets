<?php
/**
 * Server-side render test for all 12 page templates.
 *
 *   wp eval-file tests/php/render-pages.php
 *
 * Checks, per template: renders, exactly one H1, no unresolved tokens, no
 * editor-only markup on the live site, and that approval-gated content stays
 * hidden while the related setting is off. Exit code 1 on any failure.
 */

use XLights\Pages\Page_Registry;
use XLights\Settings\Settings;

$xl_fail = 0;
$xl_ok   = static function ( $cond, $msg ) use ( &$xl_fail ) {
	echo ( $cond ? 'PASS ' : 'FAIL ' ), $msg, "\n";
	if ( ! $cond ) {
		++$xl_fail;
	}
};

// Run with gates closed regardless of the site's current settings.
add_filter(
	'xlights/settings',
	static function ( $s ) {
		$s['trust_verified']     = 0;
		$s['financing_enabled']  = 0;
		$s['app_store_url']      = '';
		$s['google_play_url']    = '';
		$s['google_reviews_url'] = '';
		return $s;
	},
	99
);
Settings::flush_cache();

$token_re = '/\{(brand|dealer|market|area|phone|email|year|cta2?|tel|mailto|quote_url|google_reviews|page:[a-z_]+)\}/';

foreach ( Page_Registry::all() as $key => $page ) {
	$t    = microtime( true );
	$html = do_shortcode( '[xlights_page template="' . $key . '"]' );
	$ms   = ( microtime( true ) - $t ) * 1000;

	$sections = substr_count( $html, '<section' );
	$h1       = substr_count( $html, '<h1' );
	$xl_ok( $sections >= 3, sprintf( '%-10s renders %d sections (%.0f ms, %d KB)', $key, $sections, $ms, strlen( $html ) / 1024 ) );
	$xl_ok( 1 === $h1, sprintf( '%-10s exactly one H1 (found %d)', $key, $h1 ) );
	$xl_ok( ! preg_match( $token_re, $html, $m ), sprintf( '%-10s no unresolved tokens%s', $key, $m ? ' (' . $m[0] . ')' : '' ) );
	$xl_ok( false === strpos( $html, 'xl-pending-badge' ) && false === strpos( $html, 'xl-editor-hint' ), sprintf( '%-10s no editor-only markup on the live site', $key ) );
	$xl_ok( false === strpos( $html, 'xl-section--financing' ), sprintf( '%-10s financing hidden while disabled', $key ) );
	$xl_ok( false === stripos( $html, 'Licensed and insured' ), sprintf( '%-10s credentials hidden while unverified', $key ) );
	$xl_ok( false === strpos( $html, 'apps.apple.com' ) && false === strpos( $html, 'play.google.com' ), sprintf( '%-10s no store links without URLs', $key ) );
}

if ( $xl_fail ) {
	echo "\n{$xl_fail} check(s) failed.\n";
	exit( 1 );
}
echo "\nAll render checks passed.\n";
