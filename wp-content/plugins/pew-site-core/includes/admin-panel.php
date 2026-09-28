<?php
/**
 * PEW Training Center private admin panel.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pew_admin_panel_page_url( $section = 'dashboard' ) {
    return add_query_arg( 'section', sanitize_key( $section ), home_url( '/admin-panel/' ) );
}

function pew_admin_panel_redirect( $section, $message ) {
    wp_safe_redirect( add_query_arg( 'message', sanitize_key( $message ), pew_admin_panel_page_url( $section ) ) );
    exit;
}

function pew_admin_course_settings() {
    $defaults = array(
        'electrical_label'   => 'Electrical',
        'welding_label'      => 'Welding',
        'electrical_enabled' => 1,
        'welding_enabled'    => 1,
    );
    $saved = get_option( 'pew_course_settings', array() );
    return array_merge( $defaults, is_array( $saved ) ? $saved : array() );
}

function pew_ensure_admin_panel_page() {
    if ( get_page_by_path( 'admin-panel', OBJECT, 'page' ) ) {
        return;
    }
    $page_id = wp_insert_post(
        array(
            'post_title'   => 'PEW Admin Panel',
            'post_name'    => 'admin-panel',
            'post_content' => '[pew_admin_panel]',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ),
        true
    );
    if ( ! is_wp_error( $page_id ) ) {
        flush_rewrite_rules();
    }
}
add_action( 'init', 'pew_ensure_admin_panel_page', 30 );

function pew_admin_panel_assets() {
    if ( is_page( 'admin-panel' ) ) {
        // Keep the panel stylesheet same-origin when WordPress is exposed
        // through a public tunnel. The absolute plugin URL can otherwise
        // retain the local PHP host and port (for example, 127.0.0.1:8090).
        $stylesheet_url = plugins_url( 'assets/admin-panel.css', dirname( __DIR__ ) . '/pew-site-core.php' );
        wp_enqueue_style( 'pew-admin-panel', wp_make_link_relative( $stylesheet_url ), array(), '1.0.0' );
    }
}
add_action( 'wp_enqueue_scripts', 'pew_admin_panel_assets', 30 );

function pew_admin_panel_notice_message() {
    $messages = array(
        'notice_saved'   => 'Notice saved and published to the public notice board.',
        'notice_deleted' => 'Notice moved to the trash.',
        'profile_saved'   => 'Profile updated.',
        'courses_saved'   => 'Course settings updated.',
        'gallery_saved'   => 'Gallery image saved and published.',
        'gallery_deleted' => 'Gallery image moved to the trash.',
        'cert_saved'      => 'Certificate record saved.',
        'cert_deleted'    => 'Certificate moved to the trash.',
        'login_error'     => 'Login failed. Check the administrator username and password and try again.',
        'error'           => 'The request could not be completed. Please check the form and try again.',
    );
    $key = isset( $_GET['message'] ) ? sanitize_key( wp_unslash( $_GET['message'] ) ) : '';
    return $messages[ $key ] ?? '';
}

function pew_admin_panel_logout_url() {
    return wp_nonce_url( admin_url( 'admin-post.php?action=pew_admin_panel_logout' ), 'pew_admin_panel_logout', '_wpnonce' );
}
function pew_admin_panel_nav( $current ) {
    $items = array(
        'dashboard'    => 'Dashboard',
        'notices'      => 'Notice Board',
        'gallery'      => 'Gallery',
        'certificates' => 'Certificates',
        'courses'      => 'Course Settings',
        'profile'      => 'Profile',
    );
    ob_start();
    ?>
    <aside class="pew-admin-sidebar">
        <div class="pew-admin-mark"><span>PEW</span><small>Training Center</small></div>
        <nav aria-label="Admin panel navigation">
            <?php foreach ( $items as $slug => $label ) : ?>
                <a class="<?php echo $current === $slug ? 'is-active' : ''; ?>" href="<?php echo esc_url( pew_admin_panel_page_url( $slug ) ); ?>"><?php echo esc_html( $label ); ?></a>
            <?php endforeach; ?>
        </nav>
        <a class="pew-admin-back" href="<?php echo esc_url( home_url( '/' ) ); ?>">View website ↗</a>
    </aside>
    <?php
    return ob_get_clean();
}

function pew_admin_panel_render_dashboard() {
    $notice_count  = wp_count_posts( 'pew_notice' );
    $course_count  = wp_count_posts( 'pew_course' );
    $gallery_count = wp_count_posts( 'pew_gallery' );
    $cert_count    = wp_count_posts( 'pew_certificate' );
    $recent_notice = get_posts( array( 'post_type' => 'pew_notice', 'post_status' => 'publish', 'posts_per_page' => 5, 'orderby' => 'date', 'order' => 'DESC' ) );
    ob_start();
    ?>
    <div class="pew-admin-heading"><span class="pew-admin-eyebrow">Control center</span><h2>Dashboard</h2><p>Manage the public notice board, gallery, student certificates, and active courses from one place.</p></div>
    <div class="pew-admin-stats">
        <a href="<?php echo esc_url( pew_admin_panel_page_url( 'notices' ) ); ?>"><strong><?php echo esc_html( $notice_count->publish ?? 0 ); ?></strong><span>Published notices</span></a>
        <a href="<?php echo esc_url( pew_admin_panel_page_url( 'gallery' ) ); ?>"><strong><?php echo esc_html( $gallery_count->publish ?? 0 ); ?></strong><span>Gallery images</span></a>
        <a href="<?php echo esc_url( pew_admin_panel_page_url( 'certificates' ) ); ?>"><strong><?php echo esc_html( $cert_count->publish ?? 0 ); ?></strong><span>Verified certificates</span></a>
        <a href="<?php echo esc_url( pew_admin_panel_page_url( 'courses' ) ); ?>"><strong><?php echo esc_html( $course_count->publish ?? 0 ); ?></strong><span>Course records</span></a>
    </div>
    <div class="pew-admin-card">
        <div class="pew-admin-card-head"><h3>Recent notices</h3><a class="pew-admin-link" href="<?php echo esc_url( pew_admin_panel_page_url( 'notices' ) ); ?>">Manage notices ↗</a></div>
        <?php if ( $recent_notice ) : ?><ul class="pew-admin-recent"><?php foreach ( $recent_notice as $notice ) : ?><li><a href="<?php echo esc_url( get_permalink( $notice->ID ) ); ?>"><?php echo esc_html( get_the_title( $notice->ID ) ); ?></a><time datetime="<?php echo esc_attr( get_the_date( 'c', $notice->ID ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y', $notice->ID ) ); ?></time></li><?php endforeach; ?></ul><?php else : ?><p class="pew-admin-empty">No published notices yet.</p><?php endif; ?>
    </div>
    <div class="pew-admin-note"><strong>Scope for this demo</strong><span>Notice Board, Gallery, and Certificates are active and editable. Course settings control visible trade programs.</span></div>
    <?php
    return ob_get_clean();
}

function pew_admin_panel_render_gallery() {
    $edit_id = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;
    $editing = $edit_id ? get_post( $edit_id ) : null;
    if ( $editing && 'pew_gallery' !== $editing->post_type ) {
        $editing = null;
    }
    $image_id = $editing ? get_post_thumbnail_id( $editing->ID ) : 0;
    $items    = get_posts(
        array(
            'post_type'      => 'pew_gallery',
            'post_status'    => array( 'publish', 'draft' ),
            'posts_per_page' => 60,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );
    ob_start();
    ?>
    <div class="pew-admin-heading"><span class="pew-admin-eyebrow">Public content</span><h2>Gallery</h2><p>Upload and arrange the photos shown on the public Gallery page. Existing legacy gallery code remains available, but only images managed here are published.</p></div>
    <div class="pew-admin-grid">
        <div class="pew-admin-card">
            <div class="pew-admin-card-head"><h3><?php echo $editing ? 'Edit gallery image' : 'Add a gallery image'; ?></h3><?php if ( $editing ) : ?><a class="pew-admin-link" href="<?php echo esc_url( pew_admin_panel_page_url( 'gallery' ) ); ?>">Cancel edit</a><?php endif; ?></div>
            <form class="pew-admin-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="pew_save_gallery">
                <input type="hidden" name="gallery_id" value="<?php echo esc_attr( $editing ? $editing->ID : 0 ); ?>">
                <?php wp_nonce_field( 'pew_save_gallery', 'pew_gallery_nonce' ); ?>
                <label>Photo title<input type="text" name="gallery_title" required value="<?php echo esc_attr( $editing ? $editing->post_title : '' ); ?>" placeholder="Training activity or event name"></label>
                <label>Caption<input type="text" name="gallery_caption" value="<?php echo esc_attr( $editing ? $editing->post_excerpt : '' ); ?>" placeholder="Optional short caption"></label>
                <label>Image file<input type="file" name="gallery_image" accept="image/*"<?php echo $image_id ? '' : ' required'; ?>><small><?php echo $image_id ? 'Upload a new image to replace the current one.' : 'JPG, JPEG, PNG, or WebP image.'; ?></small></label>
                <?php if ( $image_id ) : ?><div class="pew-admin-gallery-current"><?php echo wp_get_attachment_image( $image_id, 'medium', false, array( 'alt' => 'Current gallery image' ) ); ?></div><?php endif; ?>
                <button class="pew-admin-button" type="submit"><?php echo $editing ? 'Update image' : 'Publish image'; ?></button>
            </form>
        </div>
        <div class="pew-admin-card">
            <div class="pew-admin-card-head"><h3>Published gallery</h3><span class="pew-admin-count"><?php echo esc_html( count( $items ) ); ?></span></div>
            <?php if ( $items ) : ?><div class="pew-admin-table-wrap"><table class="pew-admin-table"><thead><tr><th>Preview</th><th>Image</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach ( $items as $item ) : ?><tr><td><?php echo get_the_post_thumbnail( $item->ID, array( 72, 48 ), array( 'class' => 'pew-admin-gallery-thumb', 'alt' => '' ) ); ?></td><td><strong><?php echo esc_html( get_the_title( $item->ID ) ); ?></strong><?php if ( $item->post_excerpt ) : ?><small class="pew-admin-table-caption"><?php echo esc_html( $item->post_excerpt ); ?></small><?php endif; ?></td><td><span class="pew-admin-status <?php echo 'publish' === $item->post_status ? 'is-published' : ''; ?>"><?php echo esc_html( ucfirst( $item->post_status ) ); ?></span></td><td><a href="<?php echo esc_url( pew_admin_panel_page_url( 'gallery' ) . '&edit=' . $item->ID ); ?>">Edit</a><form class="pew-admin-inline-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="pew_delete_gallery"><input type="hidden" name="gallery_id" value="<?php echo esc_attr( $item->ID ); ?>"><?php wp_nonce_field( 'pew_delete_gallery_' . $item->ID, 'pew_delete_gallery_nonce' ); ?><button type="submit" onclick="return confirm('Move this image to the trash?');">Trash</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php else : ?><p class="pew-admin-empty">No gallery images have been added yet.</p><?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function pew_admin_panel_render_certificates() {
    $edit_id = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;
    $editing = $edit_id ? get_post( $edit_id ) : null;
    if ( $editing && 'pew_certificate' !== $editing->post_type ) {
        $editing = null;
    }
    $cert_details = $editing && function_exists( 'pew_get_certificate_details' ) ? pew_get_certificate_details( $editing->ID ) : null;

    $items = get_posts(
        array(
            'post_type'      => 'pew_certificate',
            'post_status'    => array( 'publish', 'draft' ),
            'posts_per_page' => 60,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );
    ob_start();
    ?>
    <div class="pew-admin-heading"><span class="pew-admin-eyebrow">Student Records</span><h2>Certificates</h2><p>Issue and manage verifiable trainee certificates. Trainees and employers can verify these on the public website via Certificate Verification.</p></div>
    <div class="pew-admin-grid">
        <div class="pew-admin-card">
            <div class="pew-admin-card-head"><h3><?php echo $editing ? 'Edit certificate' : 'Issue / Add certificate'; ?></h3><?php if ( $editing ) : ?><a class="pew-admin-link" href="<?php echo esc_url( pew_admin_panel_page_url( 'certificates' ) ); ?>">Cancel edit</a><?php endif; ?></div>
            <form class="pew-admin-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                <input type="hidden" name="action" value="pew_save_certificate">
                <input type="hidden" name="certificate_id" value="<?php echo esc_attr( $editing ? $editing->ID : 0 ); ?>">
                <?php wp_nonce_field( 'pew_save_certificate', 'pew_certificate_nonce' ); ?>
                <label>Trainee Name (শিক্ষার্থীর নাম)<input type="text" name="student_name" required value="<?php echo esc_attr( $cert_details ? $cert_details['name'] : '' ); ?>" placeholder="e.g. Md. Arif Hasan"></label>
                <div class="pew-admin-fields-two">
                    <label>Registration Number (রেজিস্ট্রেশন নম্বর)<input type="text" name="reg_no" required value="<?php echo esc_attr( $cert_details ? $cert_details['reg_no'] : '' ); ?>" placeholder="e.g. PEW-2024-REG-0101"></label>
                    <label>Certificate Serial / ID (সনদ নম্বর)<input type="text" name="cert_no" value="<?php echo esc_attr( $cert_details ? $cert_details['cert_no'] : '' ); ?>" placeholder="ঐচ্ছিক (ফাঁকা রাখলে রেজিস্ট্রেশন নম্বর প্রযোজ্য হবে)"></label>
                </div>
                <label>Father's Name (পিতার নাম)<input type="text" name="father_name" value="<?php echo esc_attr( $cert_details ? $cert_details['father_name'] : '' ); ?>" placeholder="e.g. Md. Rafiqul Islam"></label>
                <label>Course / Trade (ট্রেড / কোর্স)
                    <select name="course" style="width:100%;padding:10px;border:1px solid #cbd9e3;background:#fff;color:#213c50;font:inherit;">
                        <option value="Electrical Installation and Maintenance" <?php selected( $cert_details ? $cert_details['course'] : '', 'Electrical Installation and Maintenance' ); ?>>Electrical Installation and Maintenance (৪ মাস মেয়াদী)</option>
                        <option value="Welding" <?php selected( $cert_details ? $cert_details['course'] : '', 'Welding' ); ?>>Welding (৪ মাস মেয়াদী)</option>
                    </select>
                </label>
                <label>Training Period / Duration (প্রশিক্ষণের মেয়াদকাল: কত তারিখ হতে কত তারিখ অবধি)<input type="text" name="duration" value="<?php echo esc_attr( $cert_details ? $cert_details['duration'] : '৪ মাস মেয়াদী (০১ জানুয়ারি ২০২৪ হতে ৩০ এপ্রিল ২০২৪)' ); ?>" placeholder="e.g. ০১ জানুয়ারি ২০২৪ হতে ৩০ এপ্রিল ২০২৪"></label>
                <div class="pew-admin-fields-three">
                    <label>Roll No<input type="text" name="roll" value="<?php echo esc_attr( $cert_details ? $cert_details['roll'] : '' ); ?>" placeholder="240101"></label>
                    <label>Batch<input type="text" name="batch" value="<?php echo esc_attr( $cert_details ? $cert_details['batch'] : 'Batch 01 (SICIP-BEIOA)' ); ?>" placeholder="Batch 01"></label>
                    <label>Session<input type="text" name="session" value="<?php echo esc_attr( $cert_details ? $cert_details['session'] : '2024' ); ?>" placeholder="2024"></label>
                </div>
                <div class="pew-admin-fields-three">
                    <label>Result / Grade (ফলাফল)<input type="text" name="result" value="<?php echo esc_attr( $cert_details ? $cert_details['result'] : 'Competent (A+)' ); ?>" placeholder="Competent (A+)"></label>
                    <label>Issue Date / Submission (ইস্যুর তারিখ)<input type="text" name="issue_date" value="<?php echo esc_attr( $cert_details ? $cert_details['issue_date'] : wp_date( 'Y-m-d' ) ); ?>" placeholder="2024-07-15"></label>
                    <label>Status<input type="text" name="status" value="<?php echo esc_attr( $cert_details ? $cert_details['status'] : 'Valid' ); ?>" placeholder="Valid"></label>
                </div>
                <button class="pew-admin-button" type="submit"><?php echo $editing ? 'Update certificate' : 'Issue / Save certificate'; ?></button>
            </form>
        </div>
        <div class="pew-admin-card">
            <div class="pew-admin-card-head"><h3>Registered certificates</h3><span class="pew-admin-count"><?php echo esc_html( count( $items ) ); ?></span></div>
            <?php if ( $items ) : ?>
                <div class="pew-admin-table-wrap">
                    <table class="pew-admin-table">
                        <thead>
                            <tr>
                                <th>Reg / Cert No</th>
                                <th>Name & Course</th>
                                <th>Period / Result</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $items as $item ) :
                                $d = function_exists( 'pew_get_certificate_details' ) ? pew_get_certificate_details( $item->ID ) : array( 'reg_no' => $item->ID, 'cert_no' => $item->ID, 'name' => $item->post_title, 'course' => '', 'batch' => '', 'result' => 'Competent', 'duration' => '' );
                            ?>
                            <tr>
                                <td><code><?php echo esc_html( $d['reg_no'] ); ?></code></td>
                                <td><strong><?php echo esc_html( $d['name'] ); ?></strong><small class="pew-admin-table-caption"><?php echo esc_html( $d['course'] ); ?></small></td>
                                <td><small><?php echo esc_html( $d['duration'] ); ?></small><br><span class="pew-admin-status is-published"><?php echo esc_html( $d['result'] ); ?></span></td>
                                <td>
                                    <a href="<?php echo esc_url( pew_admin_panel_page_url( 'certificates' ) . '&edit=' . $item->ID ); ?>">Edit</a>
                                    <form class="pew-admin-inline-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                                        <input type="hidden" name="action" value="pew_delete_certificate">
                                        <input type="hidden" name="certificate_id" value="<?php echo esc_attr( $item->ID ); ?>">
                                        <?php wp_nonce_field( 'pew_delete_cert_' . $item->ID, 'pew_delete_cert_nonce' ); ?>
                                        <button type="submit" onclick="return confirm('Move this certificate to the trash?');">Trash</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="pew-admin-empty">No certificates have been registered yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function pew_admin_panel_render_notices() {
    $edit_id = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;
    $editing = $edit_id ? get_post( $edit_id ) : null;
    if ( $editing && 'pew_notice' !== $editing->post_type ) {
        $editing = null;
    }
    $day          = $editing ? get_post_meta( $editing->ID, '_dist_date_day', true ) : wp_date( 'j' );
    $month        = $editing ? get_post_meta( $editing->ID, '_dist_date_month', true ) : wp_date( 'F' );
    $year         = $editing ? get_post_meta( $editing->ID, '_dist_date_year', true ) : wp_date( 'Y' );
    $attachment   = $editing ? wp_get_attachment_url( (int) get_post_meta( $editing->ID, '_pew_notice_attachment_id', true ) ) : '';
    $notices      = get_posts( array( 'post_type' => 'pew_notice', 'post_status' => array( 'publish', 'draft' ), 'posts_per_page' => 50, 'orderby' => 'date', 'order' => 'DESC' ) );
    ob_start();
    ?>
    <div class="pew-admin-heading"><span class="pew-admin-eyebrow">Public content</span><h2>Notice Board</h2><p>Create a notice here and it will appear in the homepage notice board immediately after saving.</p></div>
    <div class="pew-admin-grid">
        <div class="pew-admin-card">
            <div class="pew-admin-card-head"><h3><?php echo $editing ? 'Edit notice' : 'Add a notice'; ?></h3><?php if ( $editing ) : ?><a class="pew-admin-link" href="<?php echo esc_url( pew_admin_panel_page_url( 'notices' ) ); ?>">Cancel edit</a><?php endif; ?></div>
            <form class="pew-admin-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="pew_save_notice">
                <input type="hidden" name="notice_id" value="<?php echo esc_attr( $editing ? $editing->ID : 0 ); ?>">
                <?php wp_nonce_field( 'pew_save_notice', 'pew_notice_nonce' ); ?>
                <label>Notice title<input type="text" name="notice_title" required value="<?php echo esc_attr( $editing ? $editing->post_title : '' ); ?>" placeholder="Enter the notice title"></label>
                <label>Notice details<textarea name="notice_content" rows="7" placeholder="Enter the notice details or instructions"><?php echo esc_textarea( $editing ? $editing->post_content : '' ); ?></textarea></label>
                <div class="pew-admin-fields-three"><label>Day<input type="text" name="notice_day" value="<?php echo esc_attr( $day ); ?>"></label><label>Month<input type="text" name="notice_month" value="<?php echo esc_attr( $month ); ?>"></label><label>Year<input type="text" name="notice_year" value="<?php echo esc_attr( $year ); ?>"></label></div>
                <label>Optional attachment<input type="file" name="notice_attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"><small>Upload a notice PDF or image. The attachment link will appear on its detail page.</small></label>
                <?php if ( $attachment ) : ?><p class="pew-admin-current-file">Current file: <a href="<?php echo esc_url( $attachment ); ?>" target="_blank" rel="noopener">Open attachment ↗</a></p><?php endif; ?>
                <button class="pew-admin-button" type="submit"><?php echo $editing ? 'Update notice' : 'Publish notice'; ?></button>
            </form>
        </div>
        <div class="pew-admin-card">
            <div class="pew-admin-card-head"><h3>Saved notices</h3><span class="pew-admin-count"><?php echo esc_html( count( $notices ) ); ?></span></div>
            <?php if ( $notices ) : ?><div class="pew-admin-table-wrap"><table class="pew-admin-table"><thead><tr><th>Notice</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead><tbody><?php foreach ( $notices as $notice ) : ?><tr><td><strong><?php echo esc_html( get_the_title( $notice->ID ) ); ?></strong></td><td><span class="pew-admin-status <?php echo 'publish' === $notice->post_status ? 'is-published' : ''; ?>"><?php echo esc_html( ucfirst( $notice->post_status ) ); ?></span></td><td><?php echo esc_html( get_the_date( 'j M Y', $notice->ID ) ); ?></td><td><a href="<?php echo esc_url( pew_admin_panel_page_url( 'notices' ) . '&edit=' . $notice->ID ); ?>">Edit</a><form class="pew-admin-inline-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="pew_delete_notice"><input type="hidden" name="notice_id" value="<?php echo esc_attr( $notice->ID ); ?>"><?php wp_nonce_field( 'pew_delete_notice_' . $notice->ID, 'pew_delete_nonce' ); ?><button type="submit" onclick="return confirm('Move this notice to the trash?');">Trash</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php else : ?><p class="pew-admin-empty">No notices have been created.</p><?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function pew_admin_panel_render_profile() {
    $user = wp_get_current_user();
    ob_start();
    ?>
    <div class="pew-admin-heading"><span class="pew-admin-eyebrow">Account</span><h2>Profile</h2><p>Update the name and email address used by the demo administrator account.</p></div>
    <div class="pew-admin-card pew-admin-narrow"><form class="pew-admin-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="pew_save_admin_profile"><?php wp_nonce_field( 'pew_save_admin_profile', 'pew_profile_nonce' ); ?><label>Username<input type="text" value="<?php echo esc_attr( $user->user_login ); ?>" disabled></label><label>Display name<input type="text" name="display_name" required value="<?php echo esc_attr( $user->display_name ); ?>"></label><label>Email<input type="email" name="user_email" required value="<?php echo esc_attr( $user->user_email ); ?>"></label><p class="pew-admin-help">Password changes remain available through the standard WordPress profile screen.</p><button class="pew-admin-button" type="submit">Save profile</button><a class="pew-admin-secondary-button" href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>">Open WordPress profile</a></form></div>
    <?php
    return ob_get_clean();
}

function pew_admin_panel_render_courses() {
    $settings = pew_admin_course_settings();
    ob_start();
    ?>
    <div class="pew-admin-heading"><span class="pew-admin-eyebrow">Public navigation</span><h2>Course Settings</h2><p>Only these two courses are currently exposed. Older course code and routes remain preserved but hidden.</p></div>
    <div class="pew-admin-card pew-admin-narrow"><form class="pew-admin-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="pew_save_course_settings"><?php wp_nonce_field( 'pew_save_course_settings', 'pew_courses_nonce' ); ?><fieldset><legend>Visible courses</legend><label class="pew-admin-check"><input type="checkbox" name="electrical_enabled" value="1" <?php checked( ! empty( $settings['electrical_enabled'] ) ); ?>> Show Electrical</label><label>Electrical label<input type="text" name="electrical_label" required value="<?php echo esc_attr( $settings['electrical_label'] ); ?>"></label><label class="pew-admin-check"><input type="checkbox" name="welding_enabled" value="1" <?php checked( ! empty( $settings['welding_enabled'] ) ); ?>> Show Welding</label><label>Welding label<input type="text" name="welding_label" required value="<?php echo esc_attr( $settings['welding_label'] ); ?>"></label></fieldset><p class="pew-admin-help">Civil, Electronics, Computer, Fashion, and Textile routes are retained in the codebase but remain hidden from the public selector.</p><button class="pew-admin-button" type="submit">Save course settings</button></form></div>
    <?php
    return ob_get_clean();
}

function pew_admin_panel_shortcode() {
    if ( ! is_user_logged_in() ) {
        $login_error = isset( $_GET['login_error'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['login_error'] ) );
        ob_start();
        ?>
        <section class="pew-admin-login"><div class="pew-admin-login-brand"><span>PEW</span><small>Training Center</small></div><h2>Administrator login</h2><p>Sign in here to manage the PEW Training Center website.</p><?php if ( $login_error ) : ?><div class="pew-admin-login-error">Login failed. Check your credentials and try again.</div><?php endif; ?><form class="pew-admin-login-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="pew_admin_panel_login"><?php wp_nonce_field( 'pew_admin_panel_login', 'pew_admin_login_nonce' ); ?><input type="hidden" name="redirect_to" value="<?php echo esc_url( pew_admin_panel_page_url() ); ?>"><label>Username or email<input type="text" name="pew_admin_login" autocomplete="username" required></label><label>Password<input type="password" name="pew_admin_password" autocomplete="current-password" required></label><label class="pew-admin-check"><input type="checkbox" name="pew_admin_remember" value="1"> Remember me</label><button class="pew-admin-button" type="submit">Sign in</button></form></section>
        <?php
        return ob_get_clean();
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        return '<section class="pew-admin-login"><h2>Administrator access required</h2><p>This panel is restricted to PEW Training Center administrators.</p><a class="pew-admin-secondary-button" href="' . esc_url( pew_admin_panel_logout_url() ) . '">Log out</a></section>';
    }
    $allowed = array( 'dashboard', 'notices', 'profile', 'courses', 'gallery', 'certificates' );
    $section = isset( $_GET['section'] ) ? sanitize_key( wp_unslash( $_GET['section'] ) ) : 'dashboard';
    if ( ! in_array( $section, $allowed, true ) ) {
        $section = 'dashboard';
    }
    if ( 'dashboard' === $section ) {
        $content = pew_admin_panel_render_dashboard();
    } elseif ( 'notices' === $section ) {
        $content = pew_admin_panel_render_notices();
    } elseif ( 'gallery' === $section ) {
        $content = pew_admin_panel_render_gallery();
    } elseif ( 'certificates' === $section ) {
        $content = pew_admin_panel_render_certificates();
    } elseif ( 'courses' === $section ) {
        $content = pew_admin_panel_render_courses();
    } else {
        $content = pew_admin_panel_render_profile();
    }
    $user = wp_get_current_user();
    ob_start();
    ?>
    <section class="pew-admin-panel"><div class="pew-admin-layout"><?php echo pew_admin_panel_nav( $section ); ?><main class="pew-admin-main"><header class="pew-admin-topbar"><div><span>PEW Training Center</span><strong>Administration</strong></div><div class="pew-admin-user"><span><?php echo esc_html( $user->display_name ); ?></span><a href="<?php echo esc_url( pew_admin_panel_logout_url() ); ?>">Log out</a></div></header><?php $message = pew_admin_panel_notice_message(); if ( $message ) : ?><div class="pew-admin-alert"><?php echo esc_html( $message ); ?></div><?php endif; ?><?php echo $content; ?></main></div></section>
    <?php
    return ob_get_clean();
}
add_shortcode( 'pew_admin_panel', 'pew_admin_panel_shortcode' );

function pew_admin_panel_login() {
    check_admin_referer( 'pew_admin_panel_login', 'pew_admin_login_nonce' );
    $login    = isset( $_POST['pew_admin_login'] ) ? sanitize_text_field( wp_unslash( $_POST['pew_admin_login'] ) ) : '';
    $password = isset( $_POST['pew_admin_password'] ) ? (string) wp_unslash( $_POST['pew_admin_password'] ) : '';
    $remember = ! empty( $_POST['pew_admin_remember'] );
    $user     = wp_signon( array( 'user_login' => $login, 'user_password' => $password, 'remember' => $remember ), is_ssl() );
    if ( is_wp_error( $user ) || ! user_can( $user, 'manage_options' ) ) {
        if ( $user instanceof WP_User ) {
            wp_logout();
        }
        wp_safe_redirect( add_query_arg( 'login_error', '1', pew_admin_panel_page_url() ) );
        exit;
    }
    wp_safe_redirect( pew_admin_panel_page_url() );
    exit;
}
add_action( 'admin_post_nopriv_pew_admin_panel_login', 'pew_admin_panel_login' );
add_action( 'admin_post_pew_admin_panel_login', 'pew_admin_panel_login' );

function pew_admin_panel_admin_bar_logout() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    global $wp_admin_bar;
    if ( ! $wp_admin_bar instanceof WP_Admin_Bar ) {
        return;
    }
    $logout = $wp_admin_bar->get_node( 'logout' );
    if ( $logout ) {
        $logout->href = pew_admin_panel_logout_url();
        $wp_admin_bar->add_node( $logout );
    }
}
add_action( 'wp_before_admin_bar_render', 'pew_admin_panel_admin_bar_logout', 100 );
function pew_admin_panel_logout() {
    check_admin_referer( 'pew_admin_panel_logout' );
    wp_logout();
    wp_safe_redirect( pew_admin_panel_page_url() );
    exit;
}
add_action( 'admin_post_pew_admin_panel_logout', 'pew_admin_panel_logout' );
function pew_admin_save_notice() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Administrator access required.' );
    }
    check_admin_referer( 'pew_save_notice', 'pew_notice_nonce' );
    $notice_id = isset( $_POST['notice_id'] ) ? absint( $_POST['notice_id'] ) : 0;
    if ( $notice_id && 'pew_notice' !== get_post_type( $notice_id ) ) {
        pew_admin_panel_redirect( 'notices', 'error' );
    }
    $title   = isset( $_POST['notice_title'] ) ? sanitize_text_field( wp_unslash( $_POST['notice_title'] ) ) : '';
    $content = isset( $_POST['notice_content'] ) ? wp_kses_post( wp_unslash( $_POST['notice_content'] ) ) : '';
    if ( '' === $title ) {
        pew_admin_panel_redirect( 'notices', 'error' );
    }
    $post_data = array( 'post_type' => 'pew_notice', 'post_status' => 'publish', 'post_title' => $title, 'post_content' => $content );
    if ( $notice_id ) {
        $post_data['ID'] = $notice_id;
        $saved_id       = wp_update_post( wp_slash( $post_data ), true );
    } else {
        $saved_id = wp_insert_post( wp_slash( $post_data ), true );
    }
    if ( is_wp_error( $saved_id ) ) {
        pew_admin_panel_redirect( 'notices', 'error' );
    }
    foreach ( array( 'notice_day' => '_dist_date_day', 'notice_month' => '_dist_date_month', 'notice_year' => '_dist_date_year' ) as $field => $meta_key ) {
        $value = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
        update_post_meta( $saved_id, $meta_key, $value );
    }
    if ( ! empty( $_FILES['notice_attachment']['name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attachment_id = media_handle_upload( 'notice_attachment', $saved_id );
        if ( ! is_wp_error( $attachment_id ) ) {
            update_post_meta( $saved_id, '_pew_notice_attachment_id', $attachment_id );
        }
    }
    pew_admin_panel_redirect( 'notices', 'notice_saved' );
}
add_action( 'admin_post_pew_save_notice', 'pew_admin_save_notice' );

function pew_admin_delete_notice() {
    $notice_id = isset( $_POST['notice_id'] ) ? absint( $_POST['notice_id'] ) : 0;
    if ( ! current_user_can( 'manage_options' ) || ! $notice_id || 'pew_notice' !== get_post_type( $notice_id ) ) {
        wp_die( 'Administrator access required.' );
    }
    check_admin_referer( 'pew_delete_notice_' . $notice_id, 'pew_delete_nonce' );
    wp_trash_post( $notice_id );
    pew_admin_panel_redirect( 'notices', 'notice_deleted' );
}
add_action( 'admin_post_pew_delete_notice', 'pew_admin_delete_notice' );

function pew_admin_save_gallery() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Administrator access required.' );
    }
    check_admin_referer( 'pew_save_gallery', 'pew_gallery_nonce' );
    $gallery_id = isset( $_POST['gallery_id'] ) ? absint( $_POST['gallery_id'] ) : 0;
    if ( $gallery_id && 'pew_gallery' !== get_post_type( $gallery_id ) ) {
        pew_admin_panel_redirect( 'gallery', 'error' );
    }
    $title   = isset( $_POST['gallery_title'] ) ? sanitize_text_field( wp_unslash( $_POST['gallery_title'] ) ) : '';
    $caption = isset( $_POST['gallery_caption'] ) ? sanitize_text_field( wp_unslash( $_POST['gallery_caption'] ) ) : '';
    if ( '' === $title || ( ! $gallery_id && empty( $_FILES['gallery_image']['name'] ) ) ) {
        pew_admin_panel_redirect( 'gallery', 'error' );
    }
    $post_data = array(
        'post_type'    => 'pew_gallery',
        'post_status'  => 'publish',
        'post_title'   => $title,
        'post_excerpt' => $caption,
    );
    if ( $gallery_id ) {
        $post_data['ID'] = $gallery_id;
        $saved_id        = wp_update_post( wp_slash( $post_data ), true );
    } else {
        $saved_id = wp_insert_post( wp_slash( $post_data ), true );
    }
    if ( is_wp_error( $saved_id ) ) {
        pew_admin_panel_redirect( 'gallery', 'error' );
    }
    if ( ! empty( $_FILES['gallery_image']['name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attachment_id = media_handle_upload( 'gallery_image', $saved_id );
        if ( is_wp_error( $attachment_id ) ) {
            pew_admin_panel_redirect( 'gallery', 'error' );
        }
        set_post_thumbnail( $saved_id, $attachment_id );
    }
    pew_admin_panel_redirect( 'gallery', 'gallery_saved' );
}
add_action( 'admin_post_pew_save_gallery', 'pew_admin_save_gallery' );

function pew_admin_delete_gallery() {
    $gallery_id = isset( $_POST['gallery_id'] ) ? absint( $_POST['gallery_id'] ) : 0;
    if ( ! current_user_can( 'manage_options' ) || ! $gallery_id || 'pew_gallery' !== get_post_type( $gallery_id ) ) {
        wp_die( 'Administrator access required.' );
    }
    check_admin_referer( 'pew_delete_gallery_' . $gallery_id, 'pew_delete_gallery_nonce' );
    wp_trash_post( $gallery_id );
    pew_admin_panel_redirect( 'gallery', 'gallery_deleted' );
}
add_action( 'admin_post_pew_delete_gallery', 'pew_admin_delete_gallery' );

function pew_admin_save_profile() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Administrator access required.' );
    }
    check_admin_referer( 'pew_save_admin_profile', 'pew_profile_nonce' );
    $user_id      = get_current_user_id();
    $display_name = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
    $email        = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
    if ( '' === $display_name || ! is_email( $email ) ) {
        pew_admin_panel_redirect( 'profile', 'error' );
    }
    $existing = email_exists( $email );
    if ( $existing && (int) $existing !== (int) $user_id ) {
        pew_admin_panel_redirect( 'profile', 'error' );
    }
    $updated = wp_update_user( array( 'ID' => $user_id, 'display_name' => $display_name, 'user_email' => $email ) );
    if ( is_wp_error( $updated ) ) {
        pew_admin_panel_redirect( 'profile', 'error' );
    }
    pew_admin_panel_redirect( 'profile', 'profile_saved' );
}
add_action( 'admin_post_pew_save_admin_profile', 'pew_admin_save_profile' );

function pew_admin_save_course_settings() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Administrator access required.' );
    }
    check_admin_referer( 'pew_save_course_settings', 'pew_courses_nonce' );
    $settings = array(
        'electrical_label'   => isset( $_POST['electrical_label'] ) ? sanitize_text_field( wp_unslash( $_POST['electrical_label'] ) ) : 'Electrical',
        'welding_label'      => isset( $_POST['welding_label'] ) ? sanitize_text_field( wp_unslash( $_POST['welding_label'] ) ) : 'Welding',
        'electrical_enabled' => isset( $_POST['electrical_enabled'] ) ? 1 : 0,
        'welding_enabled'    => isset( $_POST['welding_enabled'] ) ? 1 : 0,
    );
    if ( '' === $settings['electrical_label'] || '' === $settings['welding_label'] ) {
        pew_admin_panel_redirect( 'courses', 'error' );
    }
    update_option( 'pew_course_settings', $settings );
    pew_admin_panel_redirect( 'courses', 'courses_saved' );
}
add_action( 'admin_post_pew_save_course_settings', 'pew_admin_save_course_settings' );

function pew_admin_save_certificate() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Administrator access required.' );
    }
    check_admin_referer( 'pew_save_certificate', 'pew_certificate_nonce' );
    $cert_id = isset( $_POST['certificate_id'] ) ? absint( $_POST['certificate_id'] ) : 0;
    if ( $cert_id && 'pew_certificate' !== get_post_type( $cert_id ) ) {
        pew_admin_panel_redirect( 'certificates', 'error' );
    }
    $name    = isset( $_POST['student_name'] ) ? sanitize_text_field( wp_unslash( $_POST['student_name'] ) ) : '';
    $reg_no  = isset( $_POST['reg_no'] ) ? sanitize_text_field( wp_unslash( $_POST['reg_no'] ) ) : '';
    $cert_no = isset( $_POST['cert_no'] ) ? sanitize_text_field( wp_unslash( $_POST['cert_no'] ) ) : '';

    if ( '' === $cert_no && '' !== $reg_no ) {
        $cert_no = $reg_no;
    }
    if ( '' === $reg_no && '' !== $cert_no ) {
        $reg_no = $cert_no;
    }
    if ( '' === $name || '' === $reg_no ) {
        pew_admin_panel_redirect( 'certificates', 'error' );
    }
    $post_data = array(
        'post_type'   => 'pew_certificate',
        'post_status' => 'publish',
        'post_title'  => $name,
    );
    if ( $cert_id ) {
        $post_data['ID'] = $cert_id;
        $saved_id        = wp_update_post( wp_slash( $post_data ), true );
    } else {
        $post_data['post_name'] = sanitize_title( $reg_no );
        $saved_id               = wp_insert_post( wp_slash( $post_data ), true );
    }
    if ( is_wp_error( $saved_id ) ) {
        pew_admin_panel_redirect( 'certificates', 'error' );
    }

    $meta_fields = array(
        'reg_no'      => '_pew_cert_reg_no',
        'cert_no'     => '_pew_cert_no',
        'father_name' => '_pew_cert_father',
        'course'      => '_pew_cert_course',
        'duration'    => '_pew_cert_duration',
        'roll'        => '_pew_cert_roll',
        'batch'       => '_pew_cert_batch',
        'session'     => '_pew_cert_session',
        'result'      => '_pew_cert_result',
        'issue_date'  => '_pew_cert_date',
        'status'      => '_pew_cert_status',
    );
    foreach ( $meta_fields as $post_key => $meta_key ) {
        if ( 'reg_no' === $post_key ) {
            $val = $reg_no;
        } elseif ( 'cert_no' === $post_key ) {
            $val = $cert_no;
        } else {
            $val = isset( $_POST[ $post_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) ) : '';
        }
        update_post_meta( $saved_id, $meta_key, $val );
    }
    pew_admin_panel_redirect( 'certificates', 'cert_saved' );
}
add_action( 'admin_post_pew_save_certificate', 'pew_admin_save_certificate' );

function pew_admin_delete_certificate() {
    $cert_id = isset( $_POST['certificate_id'] ) ? absint( $_POST['certificate_id'] ) : 0;
    if ( ! current_user_can( 'manage_options' ) || ! $cert_id || 'pew_certificate' !== get_post_type( $cert_id ) ) {
        wp_die( 'Administrator access required.' );
    }
    check_admin_referer( 'pew_delete_cert_' . $cert_id, 'pew_delete_cert_nonce' );
    wp_trash_post( $cert_id );
    pew_admin_panel_redirect( 'certificates', 'cert_deleted' );
}
add_action( 'admin_post_pew_delete_certificate', 'pew_admin_delete_certificate' );

function pew_notice_content_attachment( $content ) {
    if ( is_singular( 'pew_notice' ) && in_the_loop() && is_main_query() ) {
        $attachment = wp_get_attachment_url( (int) get_post_meta( get_the_ID(), '_pew_notice_attachment_id', true ) );
        if ( $attachment ) {
            $content .= '<p class="pew-notice-attachment"><a href="' . esc_url( $attachment ) . '" target="_blank" rel="noopener">Open notice attachment ↗</a></p>';
        }
    }
    return $content;
}
add_filter( 'the_content', 'pew_notice_content_attachment' );