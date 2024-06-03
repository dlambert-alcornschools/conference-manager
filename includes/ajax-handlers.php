<?php

// AJAX handler to get room capacity
function cm_get_room_capacity() {
    check_ajax_referer('cm_ajax_nonce', 'nonce');

    $room_id = intval($_POST['room_id']);
    $room_capacity = get_post_meta($room_id, '_room_data', true)['capacity'] ?? 0;

    wp_send_json_success(['capacity' => intval($room_capacity)]);
}
add_action('wp_ajax_cm_get_room_capacity', 'cm_get_room_capacity');

// Handle session registration
function cm_handle_session_registration() {
    check_ajax_referer('cm_ajax_nonce', 'nonce');

    $session_id = intval($_POST['session_id']);
    $user_id = get_current_user_id();

    $registered_users = get_post_meta($session_id, '_session_registered_users', true) ?: [];
    $waiting_list = get_post_meta($session_id, '_session_waiting_list', true) ?: [];
    $registration_capacity = get_post_meta($session_id, '_registration_capacity', true);

    if (in_array($user_id, $registered_users) || in_array($user_id, $waiting_list)) {
        wp_send_json_error('You are already registered or on the waiting list.');
        return;
    }

    if (count($registered_users) < $registration_capacity) {
        // Register user
        $registered_users[] = $user_id;
        update_post_meta($session_id, '_session_registered_users', $registered_users);
        wp_send_json_success('Registration successful!');
    } else {
        // Add to waiting list
        if (!in_array($user_id, $waiting_list)) {
            $waiting_list[] = $user_id;
            update_post_meta($session_id, '_session_waiting_list', $waiting_list);
            wp_send_json_success('The session is full. You have been added to the waiting list.');
        } else {
            wp_send_json_error('You are already on the waiting list.');
        }
    }
}
add_action('wp_ajax_cm_handle_session_registration', 'cm_handle_session_registration');
add_action('wp_ajax_nopriv_cm_handle_session_registration', 'cm_handle_session_registration');

// AJAX handler to get presenters
function cm_get_presenters() {
    check_ajax_referer('cm_ajax_nonce', 'nonce');

    $presenters = get_posts(['post_type' => 'presenter', 'numberposts' => -1]);
    $presenters_data = [];

    foreach ($presenters as $presenter) {
        $presenters_data[] = [
            'id' => $presenter->ID,
            'name' => get_the_title($presenter->ID)
        ];
    }

    wp_send_json_success(['presenters' => $presenters_data]);
}
add_action('wp_ajax_cm_get_presenters', 'cm_get_presenters');
?>
