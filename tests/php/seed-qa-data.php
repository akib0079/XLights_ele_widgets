<?php
/**
 * QA fixture data — LOCAL / STAGING ONLY. Never run on a live dealer site.
 *
 *   wp eval-file tests/php/seed-qa-data.php
 *   wp eval-file tests/php/cleanup-qa-data.php   # removes everything below
 *
 * Creates 4 projects (featured, categories, residential/commercial, one
 * before/after pair) and 4 reviews (one WITHOUT permission, which must never
 * render). Every title starts with "QA " so it is obvious and removable.
 * The browser tests in tests/browser/ expect exactly this data set.
 */

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

if ( get_posts( array( 'post_type' => 'xlights_project', 's' => 'QA ', 'post_status' => 'any', 'fields' => 'ids', 'numberposts' => 1 ) ) ) {
	echo "QA data already present — run cleanup-qa-data.php first.\n";
	return;
}

$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
wp_set_current_user( $admins ? (int) $admins[0] : 0 );

$src = XLIGHTS_PATH . 'assets/images/';
$att = array();
foreach ( array( 'home-two-story-night.jpg', 'home-single-story-dusk.jpg', 'soffit-track-daytime.jpg' ) as $f ) {
	$tmp = wp_tempnam( $f );
	copy( $src . $f, $tmp );
	$id = media_handle_sideload(
		array(
			'name'     => 'qa-' . $f,
			'tmp_name' => $tmp,
		),
		0,
		'QA ' . $f
	);
	if ( is_wp_error( $id ) ) {
		echo 'Sideload failed: ', $id->get_error_message(), "\n";
		exit( 1 );
	}
	update_post_meta( $id, '_wp_attachment_image_alt', 'QA test image ' . $f );
	$att[] = $id;
}

// title, city, categories, property type, featured, gallery, before, after.
$projects = array(
	array( 'QA Two-Story Roofline', 'San Clemente', array( 'permanent-soffit-lighting' ), 'residential', '1', array( $att[0], $att[1] ), $att[2], $att[0] ),
	array( 'QA Single-Story Glow', 'Dana Point', array( 'permanent-soffit-lighting', 'landscape-lighting' ), 'residential', '0', array( $att[1] ), 0, 0 ),
	array( 'QA Patio Test', 'San Clemente', array( 'bistro-patio-lighting' ), 'commercial', '1', array( $att[0] ), 0, 0 ),
	array( 'QA Deck Test', 'Laguna Niguel', array( 'deck-lighting' ), 'residential', '0', array( $att[1] ), 0, 0 ),
);
foreach ( $projects as $i => $p ) {
	$id = wp_insert_post(
		array(
			'post_type'    => 'xlights_project',
			'post_status'  => 'publish',
			'post_title'   => $p[0],
			'post_content' => 'QA test project description.',
			'menu_order'   => $i,
		)
	);
	set_post_thumbnail( $id, $p[5][0] );
	wp_set_object_terms( $id, $p[2], 'xlights_project_category' );
	wp_set_object_terms( $id, $p[3], 'xlights_property_type' );
	update_post_meta( $id, '_xl_city', $p[1] );
	update_post_meta( $id, '_xl_featured', $p[4] );
	update_post_meta( $id, '_xl_gallery', implode( ',', $p[5] ) );
	update_post_meta( $id, '_xl_before', $p[6] );
	update_post_meta( $id, '_xl_after', $p[7] );
	\XLights\PostTypes\Projects::sync_flags( $id );
}

// name, text, rating, source, attribution, featured, permission.
$reviews = array(
	array( 'QA Reviewer A.', 'QA test review text — local, featured, 5 stars.', 5, 'google', 'local', '1', '1' ),
	array( 'QA Reviewer B.', 'QA test review text — brand-level attribution.', 4, 'facebook', 'brand', '0', '1' ),
	array( 'QA Reviewer C.', 'QA unapproved review — must NOT render.', 5, 'google', 'local', '1', '0' ),
	array( 'QA Reviewer D.', 'QA test review text — local, no rating.', 0, 'direct', 'local', '0', '1' ),
);
foreach ( $reviews as $r ) {
	$id = wp_insert_post(
		array(
			'post_type'    => 'xlights_review',
			'post_status'  => 'publish',
			'post_title'   => $r[0],
			'post_content' => $r[1],
		)
	);
	update_post_meta( $id, '_xl_rating', $r[2] );
	update_post_meta( $id, '_xl_source', $r[3] );
	update_post_meta( $id, '_xl_attribution', $r[4] );
	update_post_meta( $id, '_xl_featured', $r[5] );
	update_post_meta( $id, '_xl_permission', $r[6] );
	update_post_meta( $id, '_xl_location', 'San Clemente' );
	update_post_meta( $id, '_xl_review_date', gmdate( 'Y-m-d' ) );
}

echo "Seeded 4 QA projects, 4 QA reviews, 3 QA images.\n";
