<?php
/**
 * Every lead notification reaches the X LIGHTS lead inbox (info@xlightsled.com).
 *
 *   wp eval-file tests/php/lead-routing.php
 *
 * Mail is captured with pre_wp_mail, nothing is sent. Settings are restored.
 */

use XLights\Forms\Mailer;
use XLights\Settings\Settings;

$xl_fail = 0;
$xl_ok   = static function ( $cond, $msg ) use ( &$xl_fail ) {
	echo ( $cond ? 'PASS ' : 'FAIL ' ), $msg, "\n";
	if ( ! $cond ) {
		++$xl_fail;
	}
};

$saved = get_option( Settings::OPTION );
$sent  = array();
add_filter(
	'pre_wp_mail',
	static function ( $null, $atts ) use ( &$sent ) {
		$sent[] = $atts;
		return true;
	},
	10,
	2
);

$xl_ok( 'info@xlightsled.com' === Settings::LEAD_INBOX, 'lead inbox is info@xlightsled.com' );
$fields = Settings::fields();
$xl_ok( Settings::LEAD_INBOX === $fields['lead_recipients']['default'], 'recipients default to the lead inbox' );

Settings::update( array( 'lead_recipients' => '' ) );
$xl_ok( array( Settings::LEAD_INBOX ) === Mailer::recipients(), 'empty recipient setting still sends to the lead inbox' );

Settings::update( array( 'lead_recipients' => 'info@xlightsled.com, owner@example.com, info@xlightsled.com' ) );
$xl_ok( array( 'info@xlightsled.com', 'owner@example.com' ) === Mailer::recipients(), 'extra recipients kept, duplicates dropped' );

// A lead: notification to the inbox, auto-reply answered by the inbox.
$lead_id = wp_insert_post(
	array(
		'post_type'   => \XLights\PostTypes\Leads::POST_TYPE,
		'post_status' => 'publish',
		'post_title'  => 'QA Routing — Testville',
	)
);
foreach ( array(
	'_xl_first_name' => 'QA',
	'_xl_last_name'  => 'Routing',
	'_xl_email'      => 'qa-routing@example.com',
	'_xl_city'       => 'Testville',
) as $k => $v ) {
	update_post_meta( $lead_id, $k, $v );
}
Settings::update( array( 'lead_recipients' => 'info@xlightsled.com' ) );
Mailer::send_lead( $lead_id );
$to = isset( $sent[0] ) ? (array) $sent[0]['to'] : array();
$xl_ok( in_array( 'info@xlightsled.com', $to, true ), 'lead notification sent to info@xlightsled.com' );
$sent = array();
Mailer::send_autoreply( $lead_id );
$headers = isset( $sent[0] ) ? implode( "\n", (array) $sent[0]['headers'] ) : '';
$xl_ok( false !== strpos( $headers, 'Reply-To: info@xlightsled.com' ), 'customer replies to the confirmation go to info@xlightsled.com' );

wp_delete_post( $lead_id, true );
update_option( Settings::OPTION, $saved );
Settings::flush_cache();

if ( $xl_fail ) {
	echo "\n{$xl_fail} check(s) failed.\n";
	exit( 1 );
}
echo "\nAll lead-routing checks passed.\n";
