<?php
/**
 * Certificate Verification System for PEW Training Center.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Find a certificate by certificate number, roll number, or slug.
 */
function pew_get_certificate( $query ) {
	$query = sanitize_text_field( trim( $query ) );
	if ( '' === $query ) {
		return null;
	}

	// 1. Direct meta query on reg_no, cert_no or roll
	$posts = get_posts(
		array(
			'post_type'      => 'pew_certificate',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_pew_cert_reg_no',
					'value'   => $query,
					'compare' => '=',
				),
				array(
					'key'     => '_pew_cert_no',
					'value'   => $query,
					'compare' => '=',
				),
				array(
					'key'     => '_pew_cert_roll',
					'value'   => $query,
					'compare' => '=',
				),
			),
		)
	);

	if ( ! empty( $posts ) ) {
		return $posts[0];
	}

	// 2. Case-insensitive search on post_name or title
	$by_slug = get_page_by_path( sanitize_title( $query ), OBJECT, 'pew_certificate' );
	if ( $by_slug && 'publish' === $by_slug->post_status ) {
		return $by_slug;
	}

	// 3. Fallback search by title
	$posts_by_title = get_posts(
		array(
			'post_type'      => 'pew_certificate',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			's'              => $query,
		)
	);

	return ! empty( $posts_by_title ) ? $posts_by_title[0] : null;
}

/**
 * Get all details of a certificate.
 */
function pew_get_certificate_details( $post_id ) {
	$reg_no  = get_post_meta( $post_id, '_pew_cert_reg_no', true );
	$cert_no = get_post_meta( $post_id, '_pew_cert_no', true );
	if ( ! $reg_no && $cert_no ) {
		$reg_no = $cert_no;
	}
	if ( ! $cert_no && $reg_no ) {
		$cert_no = $reg_no;
	}
	if ( ! $cert_no ) {
		$cert_no = 'PEW-' . $post_id;
		$reg_no  = $cert_no;
	}

	return array(
		'id'          => $post_id,
		'name'        => get_the_title( $post_id ),
		'reg_no'      => $reg_no,
		'cert_no'     => $cert_no,
		'course'      => get_post_meta( $post_id, '_pew_cert_course', true ) ?: 'Technical Trade Course',
		'father_name' => get_post_meta( $post_id, '_pew_cert_father', true ) ?: '',
		'roll'        => get_post_meta( $post_id, '_pew_cert_roll', true ) ?: '',
		'batch'       => get_post_meta( $post_id, '_pew_cert_batch', true ) ?: 'Batch 01 (SICIP-BEIOA)',
		'session'     => get_post_meta( $post_id, '_pew_cert_session', true ) ?: '2024',
		'duration'    => get_post_meta( $post_id, '_pew_cert_duration', true ) ?: '৪ মাস মেয়াদী (০১ জানুয়ারি ২০২৪ হতে ৩০ এপ্রিল ২০২৪)',
		'result'      => get_post_meta( $post_id, '_pew_cert_result', true ) ?: 'Competent (উত্তীর্ণ)',
		'issue_date'  => get_post_meta( $post_id, '_pew_cert_date', true ) ?: '2024-07-15',
		'status'      => get_post_meta( $post_id, '_pew_cert_status', true ) ?: 'Valid',
	);
}

/**
 * Certificate verification portal shortcode.
 */
function pew_certificate_verification_shortcode() {
	$search_query = isset( $_GET['cert_no'] ) ? sanitize_text_field( wp_unslash( $_GET['cert_no'] ) ) : '';
	$searched     = '' !== $search_query;
	$cert_post    = $searched ? pew_get_certificate( $search_query ) : null;
	$cert         = $cert_post ? pew_get_certificate_details( $cert_post->ID ) : null;

	ob_start();
	?>
	<div class="pew-cert-portal">
		<div class="pew-cert-header">
			<div class="pew-cert-badge-top">PEW Training Center · Rajshahi</div>
			<h2>সনদ যাচাইকরণ পোর্টাল</h2>
			<p class="pew-cert-subtitle">Certificate Verification Portal — Skills for Industry Competitiveness and Innovation Program (SICIP)</p>
		</div>

		<div class="pew-cert-search-box">
			<form method="get" action="<?php echo esc_url( dist_faithful_page_url( 'certificate-verification' ) ); ?>" class="pew-cert-form">
				<label for="cert_search_input" class="pew-cert-label">শিক্ষার্থীর রেজিস্ট্রেশন নম্বর (অথবা সনদ / রোল নম্বর) লিখুন:</label>
				<div class="pew-cert-input-group">
					<input type="text" id="cert_search_input" name="cert_no" value="<?php echo esc_attr( $search_query ); ?>" placeholder="e.g. PEW-2024-REG-0101 বা PEW-2024-EL-0101" required autocomplete="off">
					<button type="submit" class="pew-cert-btn">যাচাই করুন <span>🔍</span></button>
				</div>
			</form>
			<div class="pew-cert-samples">
				<span class="pew-cert-samples-label">উদাহরণ নম্বর:</span>
				<a href="<?php echo esc_url( add_query_arg( 'cert_no', 'PEW-2024-REG-0101', dist_faithful_page_url( 'certificate-verification' ) ) ); ?>">PEW-2024-REG-0101 (Electrical)</a>
				<a href="<?php echo esc_url( add_query_arg( 'cert_no', 'PEW-2024-WD-0102', dist_faithful_page_url( 'certificate-verification' ) ) ); ?>">PEW-2024-WD-0102 (Welding)</a>
			</div>
		</div>

		<?php if ( $searched && $cert ) : ?>
			<div class="pew-cert-result-card is-valid" id="pew-certificate-card-print">
				<div class="pew-cert-result-header">
					<div class="pew-cert-status-badge">
						<span class="badge-icon">✓</span>
						<strong>যাচাইকৃত ও অনুমোদিত সনদ (Verified Valid Certificate)</strong>
					</div>
					<button type="button" class="pew-cert-print-btn" onclick="window.print();">🖨️ প্রিন্ট করুন</button>
				</div>

				<div class="pew-cert-details-table">
					<table class="pew-table-clean">
						<tbody>
							<tr>
								<th>শিক্ষার্থীর নাম (Trainee Name)</th>
								<td><strong><?php echo esc_html( $cert['name'] ); ?></strong></td>
							</tr>
							<?php if ( $cert['father_name'] ) : ?>
							<tr>
								<th>পিতার নাম (Father's Name)</th>
								<td><?php echo esc_html( $cert['father_name'] ); ?></td>
							</tr>
							<?php endif; ?>
							<tr>
								<th>কোর্স / ট্রেড (Course / Trade)</th>
								<td><strong style="color:#0f5699;"><?php echo esc_html( $cert['course'] ); ?></strong></td>
							</tr>
							<tr>
								<th>রেজিস্ট্রেশন নম্বর (Registration No)</th>
								<td><code><?php echo esc_html( $cert['reg_no'] ); ?></code></td>
							</tr>
							<?php if ( $cert['cert_no'] && $cert['cert_no'] !== $cert['reg_no'] ) : ?>
							<tr>
								<th>সনদ নম্বর (Certificate ID)</th>
								<td><code><?php echo esc_html( $cert['cert_no'] ); ?></code></td>
							</tr>
							<?php endif; ?>
							<?php if ( $cert['roll'] ) : ?>
							<tr>
								<th>রোল নম্বর (Roll Number)</th>
								<td><?php echo esc_html( $cert['roll'] ); ?></td>
							</tr>
							<?php endif; ?>
							<tr>
								<th>প্রশিক্ষণের মেয়াদকাল (Training Period)</th>
								<td><?php echo esc_html( $cert['duration'] ); ?></td>
							</tr>
							<tr>
								<th>প্রশিক্ষণ ব্যাচ (Batch & Session)</th>
								<td><?php echo esc_html( $cert['batch'] ); ?> (শিক্ষাবর্ষ: <?php echo esc_html( $cert['session'] ); ?>)</td>
							</tr>
							<tr>
								<th>ফলাফল / গ্রেড (Result / Grade)</th>
								<td><span class="pew-tag-pass"><?php echo esc_html( $cert['result'] ); ?></span></td>
							</tr>
							<tr>
								<th>ইস্যুর তারিখ / সাবমিশন (Issue Date)</th>
								<td><?php echo esc_html( $cert['issue_date'] ); ?></td>
							</tr>
							<tr>
								<th>প্রকল্প ও সহযোগিতা (Project & Support)</th>
								<td>Skills for Industry Competitiveness and Innovation Program (SICIP), অর্থ বিভাগ, অর্থ মন্ত্রণালয়।</td>
							</tr>
							<tr>
								<th>শিল্প সংস্থা ও প্রতিষ্ঠান (Authority & Center)</th>
								<td>বাংলাদেশ ইঞ্জিনিয়ারিং ইন্ডাস্ট্রি ওনার্স এসোসিয়েশন (BEIOA) তত্ত্বাবধানে PEW Training Center, রাজশাহী।</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="pew-cert-footer-note">
					<p>এই সনদটি পিইডব্লিউ ট্রেনিং সেন্টার (PEWTC)-এর সেন্ট্রাল ডাটাবেজ থেকে সরাসরি অনলাইনে যাচাইকৃত। যেকোনো তথ্যের জন্য সরাসরি যোগাযোগ করুন।</p>
				</div>
			</div>
		<?php elseif ( $searched ) : ?>
			<div class="pew-cert-result-card is-not-found">
				<div class="pew-cert-not-found-inner">
					<div class="pew-not-found-icon">⚠️</div>
					<h3>সনদ পাওয়া যায়নি</h3>
					<p>প্রদত্ত নম্বর: <code><?php echo esc_html( $search_query ); ?></code> — এর অনুকূলে কোনো রেকর্ড ডাটাবেজে পাওয়া যায়নি।</p>
					<p class="pew-help-hint">অনুগ্রহ করে সনদ নম্বরটি সঠিক কিনা যাচাই করুন অথবা আমাদের হেল্পলাইনে যোগাযোগ করুন।</p>
				</div>
			</div>
		<?php else : ?>
			<div class="pew-cert-instructions">
				<div class="pew-info-grid">
					<div class="pew-info-item">
						<h4>অনলাইনে সনদ যাচাই নির্দেশিকা</h4>
						<p>পিইডব্লিউ ট্রেনিং সেন্টার হতে প্রশিক্ষণ সম্পন্নকারী শিক্ষার্থীদের সনদপত্রের সঠিকতা যাচাইয়ের জন্য সনদপত্রের উপরে থাকা আইডি নম্বর (যেমন: <code>PEW-2024-EL-0101</code>) অথবা শিক্ষার্থীর রোল নম্বর লিখে যাচাই বাটনে ক্লিক করুন।</p>
					</div>
					<div class="pew-info-item">
						<h4>কর্মসংস্থান ও নিয়োগকারী সংস্থার জন্য</h4>
						<p>দেশি ও বিদেশি নিয়োগকারী প্রতিষ্ঠান, দূতাবাস অথবা সরকারি সংস্থা সরাসরি এই পোর্টাল থেকে অথবা ইমেলের মাধ্যমে প্রাতিষ্ঠানিক সনদ ভেরিফিকেশন অনুরোধ পাঠাতে পারেন।</p>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class="pew-cert-contact-card">
			<h4>সনদ যাচাই সংক্রান্ত সহায়তা ও যোগাযোগ</h4>
			<p>সনদ সংক্রান্ত যেকোনো তথ্য বা প্রাতিষ্ঠানিক ভেরিফিকেশনের জন্য সরাসরি যোগাযোগ করুন:</p>
			<ul class="pew-contact-inline">
				<li><strong>হটলাইন / হোয়াটসঅ্যাপ:</strong> 01342-846300</li>
				<li><strong>মোবাইল:</strong> 01342-846301, 01342-846302</li>
				<li><strong>ইমেইল:</strong> <a href="mailto:info.pewtc@gmail.com">info.pewtc@gmail.com</a></li>
				<li><strong>ঠিকানা:</strong> ছোট বনগ্রাম (বার রাস্তার মোড়), চন্দ্রিমা, সপুরা, রাজশাহী।</li>
			</ul>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'pew_certificate_verification', 'pew_certificate_verification_shortcode' );

/**
 * Filter the content of certificate-verification page.
 */
function pew_certificate_page_content( $content ) {
	if ( is_page( 'certificate-verification' ) && in_the_loop() && is_main_query() && false === strpos( $content, 'pew_certificate_verification' ) ) {
		$content = pew_certificate_verification_shortcode();
	}
	return $content;
}
add_filter( 'the_content', 'pew_certificate_page_content', 20 );
