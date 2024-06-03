<?php
// Add meta box for Sessions
function cm_add_session_meta_box() {
    add_meta_box('session_details', __('Session Details', 'asdplc-conference-manager'), 'cm_session_meta_box', 'session', 'normal', 'high');
}
add_action('add_meta_boxes', 'cm_add_session_meta_box');

// Callback function to display the session meta box
function cm_session_meta_box($post) {
    wp_nonce_field('save_session_details', 'session_details_nonce');

    // Get existing data
    $session_room = get_post_meta($post->ID, '_session_room', true);
    $session_master_schedule = get_post_meta($post->ID, '_session_master_schedule', true);
    $registration_capacity = get_post_meta($post->ID, '_registration_capacity', true);
    $session_data = get_post_meta($post->ID, '_session_data', true);
    $session_av = $session_data['av'] ?? [];
    $session_facilitator = $session_data['facilitator'] ?? '';
    $session_presenters = $session_data['presenters'] ?? [];
    $session_material = get_post_meta($post->ID, '_session_material', true);
    $session_code = get_post_meta($post->ID, '_session_code', true);

    $waiting_list = get_post_meta($post->ID, '_session_waiting_list', true) ?: [];
    $registered_users = get_post_meta($post->ID, '_session_registered_users', true) ?: [];

    echo '<table class="form-table">';

    // Room
    echo '<tr>';
    echo '<th><label for="session_room">' . __('Room:', 'asdplc-conference-manager') . '</label></th>';
    echo '<td>';
    $rooms = get_posts(['post_type' => 'room', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    echo '<select id="session_room" name="session_room">';
    echo '<option value="">' . __('Select Room', 'asdplc-conference-manager') . '</option>';
    foreach ($rooms as $room) {
        $selected = ($session_room == $room->ID) ? 'selected' : '';
        echo '<option value="' . esc_attr($room->ID) . '" ' . $selected . '>' . esc_html(get_the_title($room->ID)) . '</option>';
    }
    echo '</select>';
    echo '<div id="room_capacity_display" data-capacity="0"></div>';
    echo '<div id="capacity_warning" style="color: red; display: none;"></div>';
    echo '</td>';
    echo '</tr>';

    // Master Schedule
    echo '<tr>';
    echo '<th><label for="session_master_schedule">' . __('Master Schedule:', 'asdplc-conference-manager') . '</label></th>';
    echo '<td>';
    $master_schedules = get_posts(['post_type' => 'master_schedule', 'numberposts' => -1, 'meta_key' => '_master_schedule_time_start', 'orderby' => 'meta_value', 'order' => 'ASC']);
    echo '<select id="session_master_schedule" name="session_master_schedule">';
    echo '<option value="">' . __('Select Time Slot', 'asdplc-conference-manager') . '</option>';
    foreach ($master_schedules as $master_schedule) {
        $selected = ($session_master_schedule == $master_schedule->ID) ? 'selected' : '';
        $start_time = get_post_meta($master_schedule->ID, '_master_schedule_time_start', true);
        $end_time = get_post_meta($master_schedule->ID, '_master_schedule_time_end', true);
        echo '<option value="' . esc_attr($master_schedule->ID) . '" ' . $selected . '>' . esc_html(get_the_title($master_schedule->ID)) . ' - ' . esc_html($start_time) . ' to ' . esc_html($end_time) . '</option>';
    }
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    // Registration Capacity
    echo '<tr>';
    echo '<th><label for="registration_capacity">' . __('Registration Capacity:', 'asdplc-conference-manager') . '</label></th>';
    echo '<td><input type="number" id="registration_capacity" name="registration_capacity" value="' . esc_attr($registration_capacity) . '" min="1"/></td>';
    echo '</tr>';

    // A/V Requirements
    echo '<tr>';
    echo '<th><label>' . __('A/V Requirements:', 'asdplc-conference-manager') . '</label></th>';
    echo '<td>';
    $av_options = ['Small Screen', 'Large Screen', 'Projector', 'Sound System', 'Microphone'];
    foreach ($av_options as $option) {
        $checked = in_array($option, $session_av) ? 'checked' : '';
        echo '<label><input type="checkbox" name="session_av[]" value="' . esc_attr($option) . '" ' . $checked . '> ' . esc_html($option) . '</label><br>';
    }
    echo '</td>';
    echo '</tr>';

    // Facilitator
    echo '<tr>';
    echo '<th><label for="session_facilitator">' . __('Facilitator:', 'asdplc-conference-manager') . '</label></th>';
    echo '<td>';
    $facilitators = get_posts(['post_type' => 'facilitator', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    echo '<select id="session_facilitator" name="session_facilitator">';
    echo '<option value="">' . __('Select Facilitator', 'asdplc-conference-manager') . '</option>';
    foreach ($facilitators as $facilitator) {
        $selected = ($session_facilitator == $facilitator->ID) ? 'selected' : '';
        echo '<option value="' . esc_attr($facilitator->ID) . '" ' . $selected . '>' . esc_html(get_the_title($facilitator->ID)) . '</option>';
    }
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    // Presenters
    echo '<tr>';
    echo '<th><label for="session_presenters">' . __('Presenter(s):', 'asdplc-conference-manager') . '</label></th>';
    echo '<td>';
    $presenters = get_posts(['post_type' => 'presenter', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    echo '<select id="presenter_select" name="presenter_select">';
    echo '<option value="">' . __('Select Presenter', 'asdplc-conference-manager') . '</option>';
    foreach ($presenters as $presenter) {
        if (!in_array($presenter->ID, $session_presenters)) {
            echo '<option value="' . esc_attr($presenter->ID) . '">' . esc_html(get_the_title($presenter->ID)) . '</option>';
        }
    }
    echo '</select>';
    echo '<button type="button" id="add_presenter_button">Add</button>';
    echo '<ul id="presenter_list">';
    foreach ($session_presenters as $presenter_id) {
        $presenter = get_post($presenter_id);
        echo '<li data-id="' . esc_attr($presenter_id) . '">' . esc_html(get_the_title($presenter_id)) . ' <button type="button" class="remove_presenter_button">Remove</button></li>';
    }
    echo '</ul>';
    echo '<input type="hidden" id="session_presenters" name="session_presenters" value="' . implode(',', $session_presenters) . '">';
    echo '</td>';
    echo '</tr>';

    // Session Material
    echo '<tr>';
    echo '<th><label for="session_material">' . __('Session Material:', 'asdplc-conference-manager') . '</label></th>';
    echo '<td><input type="file" id="session_material" name="session_material" value="' . esc_attr($session_material) . '"/></td>';
    echo '</tr>';

    // Session Code
    echo '<tr>';
    echo '<th><label for="session_code">' . __('Session Code:', 'asdplc-conference-manager') . '</label></th>';
    echo '<td><input type="text" id="session_code" name="session_code" value="' . esc_attr($session_code) . '" readonly/></td>';
    echo '</tr>';

    // Registered Users
    echo '<tr><th>' . __('Registered Users', 'asdplc-conference-manager') . '</th><td>';
    if (!empty($registered_users)) {
        foreach ($registered_users as $user_id) {
            $user_info = get_userdata($user_id);
            echo '<p>' . esc_html($user_info->display_name) . ' (' . esc_html($user_info->user_email) . ')</p>';
        }
    } else {
        echo '<p>' . __('No registered users.', 'asdplc-conference-manager') . '</p>';
    }
    echo '</td></tr>';

    // Waiting List
    echo '<tr><th>' . __('Waiting List', 'asdplc-conference-manager') . '</th><td>';
    if (!empty($waiting_list)) {
        foreach ($waiting_list as $index => $user_id) {
            $user_info = get_userdata($user_id);
            echo '<p>' . esc_html($user_info->display_name) . ' (' . esc_html($user_info->user_email) . ') - ' . __('Position', 'asdplc-conference-manager') . ': ' . ($index + 1) . '</p>';
        }
    } else {
        echo '<p>' . __('No users on the waiting list.', 'asdplc-conference-manager') . '</p>';
    }
    echo '</td></tr>';

    echo '</table>';

    // Output the initial capacity if available
    if ($session_room) {
        $room_capacity = get_post_meta($session_room, '_room_data', true)['capacity'] ?? 0;
        echo "<script>
            jQuery(document).ready(function($) {
                $('#room_capacity_display').text('Room Capacity: ' + $room_capacity).data('capacity', $room_capacity);
            });
        </script>";
    }
}

// Function to save the session details
function cm_save_session_details($post_id) {
    // Verify nonce
    if (!isset($_POST['session_details_nonce']) || !wp_verify_nonce($_POST['session_details_nonce'], 'save_session_details')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Generate unique session code if not already set
    $session_code = get_post_meta($post_id, '_session_code', true);
    if (!$session_code) {
        $session_code = strtoupper(wp_generate_password(6, false, false));
        update_post_meta($post_id, '_session_code', $session_code);
    }

    // Sanitize input
    $session_data = [
        'av' => array_map('sanitize_text_field', $_POST['session_av'] ?? []),
        'facilitator' => intval($_POST['session_facilitator']),
        'presenters' => array_map('intval', explode(',', $_POST['session_presenters'] ?? '')),
    ];

    update_post_meta($post_id, '_session_data', $session_data);
    update_post_meta($post_id, '_session_room', intval($_POST['session_room']));
    update_post_meta($post_id, '_session_master_schedule', intval($_POST['session_master_schedule']));
    update_post_meta($post_id, '_registration_capacity', intval($_POST['registration_capacity']));

    // Handle file upload
    if (!empty($_FILES['session_material']['name'])) {
        $attachment_id = media_handle_upload('session_material', $post_id);
        if (is_wp_error($attachment_id)) {
            wp_die(__('Error uploading file.', 'asdplc-conference-manager'));
        }
        update_post_meta($post_id, '_session_material', $attachment_id);
    }

    // Save waiting list and registered users
    update_post_meta($post_id, '_session_waiting_list', array_map('intval', $_POST['session_waiting_list'] ?? []));
    update_post_meta($post_id, '_session_registered_users', array_map('intval', $_POST['session_registered_users'] ?? []));
}

add_action('save_post', 'cm_save_session_details');
?>
