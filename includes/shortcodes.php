<?php
function cm_session_form_shortcode() {
    ob_start();
    ?>
    <form id="cm-session-form" method="post">
        <label for="session_name">Session Name</label>
        <input type="text" id="session_name" name="session_name" required>

        <label for="session_description">Description</label>
        <textarea id="session_description" name="session_description" required></textarea>

        <label for="session_room">Room</label>
        <input type="text" id="session_room" name="session_room" required>

        <label for="session_date">Scheduled Date</label>
        <input type="date" id="session_date" name="session_date" required>

        <label for="session_time">Scheduled Time</label>
        <input type="time" id="session_time" name="session_time" required>

        <label for="session_facilitator">Facilitator</label>
        <input type="text" id="session_facilitator" name="session_facilitator" required>

        <label for="session_presenter">Presenter</label>
        <input type="text" id="session_presenter" name="session_presenter" required>

        <input type="submit" name="submit_session" value="Submit">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('cm_session_form', 'cm_session_form_shortcode');

function cm_handle_form_submission() {
    if (isset($_POST['submit_session'])) {
        $session_code = sprintf('%06d', mt_rand(1, 999999));
        $post_data = [
            'post_title' => sanitize_text_field($_POST['session_name']),
            'post_content' => sanitize_textarea_field($_POST['session_description']),
            'post_status' => 'publish',
            'post_type' => 'session',
            'meta_input' => [
                '_session_room' => sanitize_text_field($_POST['session_room']),
                '_session_date' => sanitize_text_field($_POST['session_date']),
                '_session_time' => sanitize_text_field($_POST['session_time']),
                '_session_facilitator' => sanitize_text_field($_POST['session_facilitator']),
                '_session_presenter' => sanitize_text_field($_POST['session_presenter']),
                '_session_code' => $session_code,
            ]
        ];
        wp_insert_post($post_data);
    }
}
add_action('wp', 'cm_handle_form_submission');

function cm_display_sessions_shortcode() {
    $query = new WP_Query(['post_type' => 'session']);
    if ($query->have_posts()) {
        echo '<ul>';
        while ($query->have_posts()) {
            $query->the_post();
            echo '<li>' . get_the_title() . ' - Room: ' . get_post_meta(get_the_ID(), '_session_room', true) . ' - Date: ' . get_post_meta(get_the_ID(), '_session_date', true) . ' - Time: ' . get_post_meta(get_the_ID(), '_session_time', true) . ' - Code: ' . get_post_meta(get_the_ID(), '_session_code', true) . '</li>';
        }
        echo '</ul>';
    }
    wp_reset_postdata();
}
add_shortcode('cm_display_sessions', 'cm_display_sessions_shortcode');
?>

function cm_session_registration_form_shortcode() {
    if (isset($_GET['registration_success'])) {
        echo '<p>Registration successful!</p>';
    }

    ob_start();
    ?>
    <form id="cm-session-registration-form" method="post">
        <label for="session_id">Session</label>
        <select id="session_id" name="session_id" required>
            <?php
            $sessions = new WP_Query(['post_type' => 'session', 'posts_per_page' => -1]);
            while ($sessions->have_posts()) : $sessions->the_post();
                ?>
                <option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </select>

        <input type="submit" name="submit_registration" value="Register">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('cm_session_registration_form', 'cm_session_registration_form_shortcode');




function cm_handle_registration_submission() {
    if (isset($_POST['submit_registration'])) {
        $session_id = intval($_POST['session_id']);
        $user_id = get_current_user_id();

        // Ensure user is logged in
        if ($user_id === 0) {
            wp_redirect(wp_login_url());
            exit;
        }

        // Check if the user is already registered for a session in the same time slot
        $session_time = get_post_meta($session_id, '_session_time', true);
        $registered_sessions = get_user_meta($user_id, '_registered_sessions', true) ?: [];

        foreach ($registered_sessions as $registered_session_id) {
            $registered_session_time = get_post_meta($registered_session_id, '_session_time', true);
            if ($registered_session_time === $session_time) {
                wp_die('You are already registered for a session in this time slot.');
            }
        }

        // Register the user for the session
        $registered_sessions[] = $session_id;
        update_user_meta($user_id, '_registered_sessions', $registered_sessions);

        wp_redirect(add_query_arg('registration_success', '1', get_permalink()));
        exit;
    }
}
add_action('template_redirect', 'cm_handle_registration_submission');
