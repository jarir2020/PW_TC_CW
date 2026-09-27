<?php
/**
 * Seed demo verified certificates for PEW Training Center.
 *
 * Run from the project root:
 *   php scripts/seed-demo-certificates.php
 */

$root = dirname( __DIR__ );
require_once $root . '/wp-load.php';

if ( ! post_type_exists( 'pew_certificate' ) ) {
	fwrite( STDERR, "The pew_certificate post type is not registered. Is Pew Site Core active?\n" );
	exit( 1 );
}

$certificates = array(
	array(
		'reg_no'      => 'PEW-2024-REG-0101',
		'cert_no'     => 'PEW-2024-EL-0101',
		'name'        => 'মোঃ আরিফ হাসান (Md. Arif Hasan)',
		'father_name' => 'মোঃ রফিকুল ইসলাম',
		'course'      => 'Electrical Installation and Maintenance',
		'duration'    => '৪ মাস মেয়াদী (০১ জানুয়ারি ২০২৪ হতে ৩০ এপ্রিল ২০২৪)',
		'roll'        => '240101',
		'batch'       => 'Batch 01 (SICIP-BEIOA)',
		'session'     => '2024',
		'result'      => 'Competent (A+)',
		'issue_date'  => '2024-07-15',
		'status'      => 'Valid',
	),
	array(
		'reg_no'      => 'PEW-2024-REG-0102',
		'cert_no'     => 'PEW-2024-WD-0102',
		'name'        => 'মোঃ তানভীর আহমেদ (Md. Tanvir Ahmed)',
		'father_name' => 'মোঃ শাহজাহান আলী',
		'course'      => 'Welding',
		'duration'    => '৪ মাস মেয়াদী (০১ জানুয়ারি ২০২৪ হতে ৩০ এপ্রিল ২০২৪)',
		'roll'        => '240102',
		'batch'       => 'Batch 01 (SICIP-BEIOA)',
		'session'     => '2024',
		'result'      => 'Competent (A)',
		'issue_date'  => '2024-07-15',
		'status'      => 'Valid',
	),
	array(
		'reg_no'      => 'PEW-2024-REG-0103',
		'cert_no'     => 'PEW-2024-EL-0103',
		'name'        => 'মোছাঃ সুমাইয়া খাতুন (Mst. Sumaiya Khatun)',
		'father_name' => 'মোঃ আব্দুল করিম',
		'course'      => 'Electrical Installation and Maintenance',
		'duration'    => '৪ মাস মেয়াদী (০১ জানুয়ারি ২০২৪ হতে ৩০ এপ্রিল ২০২৪)',
		'roll'        => '240103',
		'batch'       => 'Batch 01 (SICIP-BEIOA)',
		'session'     => '2024',
		'result'      => 'Competent (A+)',
		'issue_date'  => '2024-07-15',
		'status'      => 'Valid',
	),
);

foreach ( $certificates as $cert ) {
	$slug = sanitize_title( $cert['cert_no'] );
	$post = get_page_by_path( $slug, OBJECT, 'pew_certificate' );
	if ( ! $post ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'pew_certificate',
				'post_status' => 'publish',
				'post_title'  => $cert['name'],
				'post_name'   => $slug,
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			fwrite( STDERR, "Error creating certificate {$cert['cert_no']}: " . $post_id->get_error_message() . "\n" );
			continue;
		}
	} else {
		$post_id = $post->ID;
		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => $cert['name'],
			)
		);
	}

	update_post_meta( $post_id, '_pew_cert_reg_no', $cert['reg_no'] );
	update_post_meta( $post_id, '_pew_cert_no', $cert['cert_no'] );
	update_post_meta( $post_id, '_pew_cert_father', $cert['father_name'] );
	update_post_meta( $post_id, '_pew_cert_course', $cert['course'] );
	update_post_meta( $post_id, '_pew_cert_duration', $cert['duration'] );
	update_post_meta( $post_id, '_pew_cert_roll', $cert['roll'] );
	update_post_meta( $post_id, '_pew_cert_batch', $cert['batch'] );
	update_post_meta( $post_id, '_pew_cert_session', $cert['session'] );
	update_post_meta( $post_id, '_pew_cert_result', $cert['result'] );
	update_post_meta( $post_id, '_pew_cert_date', $cert['issue_date'] );
	update_post_meta( $post_id, '_pew_cert_status', $cert['status'] );

	printf( "SEEDED certificate #%d: %s (%s)\n", (int) $post_id, $cert['cert_no'], $cert['name'] );
}

echo "All demo certificates seeded successfully.\n";
