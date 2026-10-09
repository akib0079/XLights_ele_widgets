<?php
/**
 * Optional CRM webhook: posts a signed JSON summary of each lead (no photo
 * files, no IP data) to an HTTPS endpoint configured in settings.
 *
 * @package XLights
 */

namespace XLights\Forms;

use XLights\PostTypes\Leads;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

final class Webhook {

	/**
	 * @param int $lead_id Lead.
	 */
	public static function send( $lead_id ) {
		$url = (string) Settings::get( 'webhook_url' );
		if ( '' === $url || 0 !== strpos( $url, 'https://' ) ) {
			return;
		}
		$lead    = Leads::get( $lead_id );
		$payload = array(
			'event'         => 'lead.created',
			'lead_id'       => (int) $lead_id,
			'dealer'        => $lead['dealer'],
			'submitted_at'  => gmdate( 'c', (int) $lead['date'] ),
			'first_name'    => $lead['first_name'],
			'last_name'     => $lead['last_name'],
			'phone'         => $lead['phone'],
			'email'         => $lead['email'],
			'address'       => $lead['address'],
			'city'          => $lead['city'],
			'state'         => $lead['state'],
			'zip'           => $lead['zip'],
			'property_type' => $lead['property_type'],
			'company'       => $lead['company'],
			'business_type' => $lead['business_type'],
			'interests'     => array_values( (array) $lead['interests'] ),
			'message'       => $lead['message'],
			'photo_count'   => count( (array) $lead['files'] ),
			'admin_url'     => admin_url( 'post.php?post=' . (int) $lead_id . '&action=edit' ),
			'source_url'    => $lead['source_url'],
		);
		/**
		 * Filter the webhook payload.
		 *
		 * @param array $payload Payload.
		 * @param int   $lead_id Lead ID.
		 */
		$payload = apply_filters( 'xlights/webhook/payload', $payload, $lead_id );
		$body    = wp_json_encode( $payload );
		$headers = array( 'Content-Type' => 'application/json' );
		$secret  = (string) Settings::get( 'webhook_secret' );
		if ( '' !== $secret ) {
			$headers['X-XLights-Signature'] = 'sha256=' . hash_hmac( 'sha256', $body, $secret );
		}
		wp_safe_remote_post(
			$url,
			array(
				'timeout'  => 3,
				'blocking' => false,
				'headers'  => $headers,
				'body'     => $body,
			)
		);
	}
}
