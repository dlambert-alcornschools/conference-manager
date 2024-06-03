<?php
// Shortcode to display session registration form
function cm_session_registration_form() {
    if (!is_user_logged_in()) {
        return '<p>You must be logged in to register for sessions.</p>';
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['session_id'])) {
        $session_id = intval($_POST['session_id']);
        $user_id = get_current_user_id();
        
        // Attempt to register the user for the session
        $registration_result = cm_register_for_session($user_id, $session_id);
        if ($registration_result === 'registered') {
            echo '<p>Registration successful!</p>';
        } elseif ($registration_result === 'waiting_list') {
            echo '<p>The session is full. You have been added to the waiting list.</p>';
        } else {
            echo '<p>Failed to register. You might have a time conflict with another session.</p>';
        }
    }

    // Fetch all sessions
    $sessions = get_posts(['post_type' => 'session', 'numberposts' => -1]);
    $form = '<form method="POST"><select name="session_id">';
    foreach ($sessions as $session) {
        $master_schedule_id = get_post_meta($session->ID, '_session_master_schedule', true);
        $start_time = get_post_meta($master_schedule_id, '_master_schedule_time_start', true);
        $end_time = get_post_meta($master_schedule_id, '_master_schedule_time_end', true);
        $form .= '<option value="' . $session->ID . '">' . get_the_title($session->ID) . ' (' . $start_time . ' to ' . $end_time . ')</option>';
    }
    $form .= '</select><button type="submit">Register</button></form>';

    return $form;
}
add_shortcode('session_registration_form', 'cm_session_registration_form');

// Function to handle session registration
function cm_register_for_session($user_id, $session_id) {
    $registered_users = get_post_meta($session_id, '_session_registered_users', true) ?: [];
    $waiting_list = get_post_meta($session_id, '_session_waiting_list', true) ?: [];
    $registration_capacity = get_post_meta($session_id, '_registration_capacity', true);

    if (in_array($user_id, $registered_users) || in_array($user_id, $waiting_list)) {
        return 'already_registered';
    }

    if (count($registered_users) < $registration_capacity) {
        // Register user
        $registered_users[] = $user_id;
        update_post_meta($session_id, '_session_registered_users', $registered_users);
        return 'registered';
    } else {
        // Add to waiting list
        if (!in_array($user_id, $waiting_list)) {
            $waiting_list[] = $user_id;
            update_post_meta($session_id, '_session_waiting_list', $waiting_list);
            return 'waiting_list';
        }
    }

    return 'failed';
}
?>
