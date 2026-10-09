<?php
/**
 * Removes data created by the test suite:
 *   - projects, reviews and attachments whose title starts with "QA "
 *   - leads submitted by tests/security.sh and tests/browser/form.js
 *     (emails ending in @example.com) and their private photo folders
 *
 *   wp eval-file tests/php/cleanup-qa-data.php
 */

$removed = 0;
foreach ( array( 'xlights_project', 'xlights_review', 'attachment' ) as $type ) {
	$ids = get_posts(
		array(
			'post_type'   => $type,
			'post_status' => 'any',
			'numberposts' => -1,
			'fields'      => 'ids',
			's'           => 'QA ',
		)
	);
	foreach ( $ids as $id ) {
		if ( 0 !== strpos( get_the_title( $id ), 'QA ' ) ) {
			continue;
		}
		'attachment' === $type ? wp_delete_attachment( $id, true ) : wp_delete_post( $id, true );
		++$removed;
	}
}

$leads = get_posts(
	array(
		'post_type'   => 'xlights_lead',
		'post_status' => 'any',
		'numberposts' => -1,
		'fields'      => 'ids',
	)
);
foreach ( $leads as $id ) {
	$lead = \XLights\PostTypes\Leads::get( $id );
	if ( ! $lead || ! preg_match( '/@example\.com$/i', (string) $lead['email'] ) ) {
		continue;
	}
	wp_delete_post( $id, true ); // Deleting a lead also deletes its stored photos.
	++$removed;
}

echo "Removed {$removed} QA item(s).\n";
