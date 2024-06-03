<?php
// Function to add meta boxes
function cm_add_meta_boxes() {
    // Add meta box for Rooms
    add_meta_box('room_details', 'Room Details', 'cm_room_meta_box', 'room', 'normal', 'high');
    // Add meta box for Sessions
    add_meta_box('session_details', 'Session Details', 'cm_session_meta_box', 'session', 'normal', 'high');
    // Add meta box for Facilitators
    add_meta_box('facilitator_details', 'Facilitator Details', 'cm_facilitator_meta_box', 'facilitator', 'normal', 'high');
    // Add meta box for Presenters
    add_meta_box('presenter_details', 'Presenter Details', 'cm_presenter_meta_box', 'presenter', 'normal', 'high');
    // Add meta box for Exhibitors
    add_meta_box('exhibitor_details', 'Exhibitor Details', 'cm_exhibitor_meta_box', 'exhibitor', 'normal', 'high');
}

add_action('add_meta_boxes', 'cm_add_meta_boxes');

// Callback function to display the room meta box
function cm_room_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('save_room_details', 'room_details_nonce');

    // Get existing data
    $room_data = get_post_meta($post->ID, '_room_data', true);
    $room_capacity = $room_data['capacity'] ?? '';
    $room_av = $room_data['av'] ?? [];

    // Field for seating capacity
    echo '<label for="room_capacity">Seating Capacity:</label>';
    echo '<input type="number" id="room_capacity" name="room_capacity" value="' . esc_attr($room_capacity) . '"/>';

    // Fields for A/V options
    echo '<h4>A/V Capabilities:</h4>';
    $av_options = ['Small Screen', 'Large Screen', 'Projector', 'Sound System', 'Microphone'];
    foreach ($av_options as $option) {
        $checked = in_array($option, $room_av) ? 'checked' : '';
        echo '<label><input type="checkbox" name="room_av[]" value="' . esc_attr($option) . '" ' . $checked . '> ' . esc_html($option) . '</label><br>';
    }
}

// Function to save the room details
function cm_save_room_details($post_id) {
    if (!isset($_POST['room_details_nonce']) || !wp_verify_nonce($_POST['room_details_nonce'], 'save_room_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $room_data = [
        'capacity' => intval($_POST['room_capacity']),
        'av' => array_map('sanitize_text_field', $_POST['room_av'] ?? [])
    ];

    update_post_meta($post_id, '_room_data', $room_data);
}

add_action('save_post', 'cm_save_room_details');

// Callback function to display the session meta box
function cm_session_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('save_session_details', 'session_details_nonce');

    // Get existing data
    $session_data = get_post_meta($post->ID, '_session_data', true);
    $session_room = get_post_meta($post->ID, '_session_room', true);
    $session_av = $session_data['av'] ?? [];
    $session_facilitator = $session_data['facilitator'] ?? '';
    $session_presenters = $session_data['presenters'] ?? '';
    $session_date = $session_data['date'] ?? '';
    $session_time = $session_data['time'] ?? '';
    $session_material = get_post_meta($post->ID, '_session_material', true);
    $session_code = get_post_meta($post->ID, '_session_code', true);

    // Fields for session details
    echo '<label for="session_room">Room:</label>';
    $rooms = get_posts(['post_type' => 'room', 'numberposts' => -1]);
    echo '<select id="session_room" name="session_room">';
    echo '<option value="">Select Room</option>';
    foreach ($rooms as $room) {
        $selected = ($session_room == $room->ID) ? 'selected' : '';
        echo '<option value="' . esc_attr($room->ID) . '" ' . $selected . '>' . esc_html(get_the_title($room->ID)) . '</option>';
    }
    echo '</select>';

    echo '<h4>A/V Requirements:</h4>';
    $av_options = ['Small Screen', 'Large Screen', 'Projector', 'Sound System', 'Microphone'];
    foreach ($av_options as $option) {
        $checked = in_array($option, $session_av) ? 'checked' : '';
        echo '<label><input type="checkbox" name="session_av[]" value="' . esc_attr($option) . '" ' . $checked . '> ' . esc_html($option) . '</label><br>';
    }

    echo '<label for="session_facilitator">Facilitator:</label>';
    echo '<input type="text" id="session_facilitator" name="session_facilitator" value="' . esc_attr($session_facilitator) . '"/>';

    echo '<label for="session_presenters">Presenter(s):</label>';
    echo '<input type="text" id="session_presenters" name="session_presenters" value="' . esc_attr($session_presenters) . '"/>';

    echo '<label for="session_date">Date:</label>';
    echo '<input type="date" id="session_date" name="session_date" value="' . esc_attr($session_date) . '"/>';

    echo '<label for="session_time">Time:</label>';
    echo '<input type="time" id="session_time" name="session_time" value="' . esc_attr($session_time) . '"/>';

    echo '<label for="session_material">Session Material:</label>';
    echo '<input type="file" id="session_material" name="session_material" value="' . esc_attr($session_material) . '"/>';

    echo '<label for="session_code">Session Code:</label>';
    echo '<input type="text" id="session_code" name="session_code" value="' . esc_attr($session_code) . '" readonly/>';
}

// Function to save the session details
function cm_save_session_details($post_id) {
    if (!isset($_POST['session_details_nonce']) || !wp_verify_nonce($_POST['session_details_nonce'], 'save_session_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Generate unique session code if not already set
    $session_code = get_post_meta($post_id, '_session_code', true);
    if (!$session_code) {
        $session_code = strtoupper(wp_generate_password(6, false, false));
        update_post_meta($post_id, '_session_code', $session_code);
    }

    $session_data = [
        'av' => array_map('sanitize_text_field', $_POST['session_av'] ?? []),
        'facilitator' => sanitize_text_field($_POST['session_facilitator']),
        'presenters' => sanitize_text_field($_POST['session_presenters']),
        'date' => sanitize_text_field($_POST['session_date']),
        'time' => sanitize_text_field($_POST['session_time']),
    ];

    update_post_meta($post_id, '_session_data', $session_data);
    update_post_meta($post_id, '_session_room', intval($_POST['session_room']));

    if (isset($_FILES['session_material'])) {
        $attachment_id = media_handle_upload('session_material', $post_id);
        if (is_wp_error($attachment_id)) {
            wp_die('Error uploading file.');
        }
        update_post_meta($post_id, '_session_material', $attachment_id);
    }
}

add_action('save_post', 'cm_save_session_details');

// Callback function to display the facilitator meta box
function cm_facilitator_meta_box($post) {
    wp_nonce_field('save_facilitator_details', 'facilitator_details_nonce');

    $facilitator_data = get_post_meta($post->ID, '_facilitator_data', true);
    $facilitator_phone = $facilitator_data['phone'] ?? '';
    $facilitator_email = $facilitator_data['email'] ?? '';
    $facilitator_picture = get_post_meta($post->ID, '_facilitator_picture', true);

    echo '<label for="facilitator_phone">Phone Number:</label>';
    echo '<input type="text" id="facilitator_phone" name="facilitator_phone" value="' . esc_attr($facilitator_phone) . '"/>';

    echo '<label for="facilitator_email">Email:</label>';
    echo '<input type="email" id="facilitator_email" name="facilitator_email" value="' . esc_attr($facilitator_email) . '"/>';

    echo '<label for="facilitator_picture">Profile Picture:</label>';
    echo '<input type="file" id="facilitator_picture" name="facilitator_picture" />';
    if ($facilitator_picture) {
        echo '<img src="' . wp_get_attachment_url($facilitator_picture) . '" alt="Profile Picture" style="max-width: 150px;"><br>';
    }
}

// Function to save the facilitator details
function cm_save_facilitator_details($post_id) {
    if (!isset($_POST['facilitator_details_nonce']) || !wp_verify_nonce($_POST['facilitator_details_nonce'], 'save_facilitator_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $facilitator_data = [
        'phone' => sanitize_text_field($_POST['facilitator_phone']),
        'email' => sanitize_email($_POST['facilitator_email']),
    ];

    update_post_meta($post_id, '_facilitator_data', $facilitator_data);

    if (isset($_FILES['facilitator_picture'])) {
        $attachment_id = media_handle_upload('facilitator_picture', $post_id);
        if (is_wp_error($attachment_id)) {
            wp_die('Error uploading file.');
        }
        update_post_meta($post_id, '_facilitator_picture', $attachment_id);
    }
}

add_action('save_post', 'cm_save_facilitator_details');

// Callback function to display the presenter meta box
function cm_presenter_meta_box($post) {
    wp_nonce_field('save_presenter_details', 'presenter_details_nonce');

    $presenter_data = get_post_meta($post->ID, '_presenter_data', true);
    $presenter_phone = $presenter_data['phone'] ?? '';
    $presenter_email = $presenter_data['email'] ?? '';
    $presenter_company = $presenter_data['company'] ?? '';
    $presenter_bio = $presenter_data['bio'] ?? '';
    $presenter_picture = get_post_meta($post->ID, '_presenter_picture', true);

    echo '<label for="presenter_phone">Phone Number:</label>';
    echo '<input type="text" id="presenter_phone" name="presenter_phone" value="' . esc_attr($presenter_phone) . '"/>';

    echo '<label for="presenter_email">Email:</label>';
    echo '<input type="email" id="presenter_email" name="presenter_email" value="' . esc_attr($presenter_email) . '"/>';

    echo '<label for="presenter_company">Company:</label>';
    echo '<input type="text" id="presenter_company" name="presenter_company" value="' . esc_attr($presenter_company) . '"/>';

    echo '<label for="presenter_bio">Bio:</label>';
    echo '<textarea id="presenter_bio" name="presenter_bio">' . esc_textarea($presenter_bio) . '</textarea>';

    echo '<label for="presenter_picture">Profile Picture:</label>';
    echo '<input type="file" id="presenter_picture" name="presenter_picture" />';
    if ($presenter_picture) {
        echo '<img src="' . wp_get_attachment_url($presenter_picture) . '" alt="Profile Picture" style="max-width: 150px;"><br>';
    }
}

// Function to save the presenter details
function cm_save_presenter_details($post_id) {
    if (!isset($_POST['presenter_details_nonce']) || !wp_verify_nonce($_POST['presenter_details_nonce'], 'save_presenter_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $presenter_data = [
        'phone' => sanitize_text_field($_POST['presenter_phone']),
        'email' => sanitize_email($_POST['presenter_email']),
        'company' => sanitize_text_field($_POST['presenter_company']),
        'bio' => sanitize_textarea_field($_POST['presenter_bio']),
    ];

    update_post_meta($post_id, '_presenter_data', $presenter_data);

    if (isset($_FILES['presenter_picture'])) {
        $attachment_id = media_handle_upload('presenter_picture', $post_id);
        if (is_wp_error($attachment_id)) {
            wp_die('Error uploading file.');
        }
        update_post_meta($post_id, '_presenter_picture', $attachment_id);
    }
}

add_action('save_post', 'cm_save_presenter_details');

// Callback function to display the exhibitor meta box
function cm_exhibitor_meta_box($post) {
    wp_nonce_field('save_exhibitor_details', 'exhibitor_details_nonce');

    $exhibitor_data = get_post_meta($post->ID, '_exhibitor_data', true);
    $exhibitor_phone = $exhibitor_data['phone'] ?? '';
    $exhibitor_email = $exhibitor_data['email'] ?? '';
    $exhibitor_company = $exhibitor_data['company'] ?? '';
    $exhibitor_booth = $exhibitor_data['booth'] ?? '';
    $exhibitor_notes = $exhibitor_data['notes'] ?? '';

    echo '<label for="exhibitor_phone">Phone Number:</label>';
    echo '<input type="text" id="exhibitor_phone" name="exhibitor_phone" value="' . esc_attr($exhibitor_phone) . '"/>';

    echo '<label for="exhibitor_email">Email:</label>';
    echo '<input type="email" id="exhibitor_email" name="exhibitor_email" value="' . esc_attr($exhibitor_email) . '"/>';

    echo '<label for="exhibitor_company">Company:</label>';
    echo '<input type="text" id="exhibitor_company" name="exhibitor_company" value="' . esc_attr($exhibitor_company) . '"/>';

    echo '<label for="exhibitor_booth">Booth Number:</label>';
    echo '<input type="text" id="exhibitor_booth" name="exhibitor_booth" value="' . esc_attr($exhibitor_booth) . '"/>';

    echo '<label for="exhibitor_notes">Notes:</label>';
    echo '<textarea id="exhibitor_notes" name="exhibitor_notes">' . esc_textarea($exhibitor_notes) . '</textarea>';
}

// Function to save the exhibitor details
function cm_save_exhibitor_details($post_id) {
    if (!isset($_POST['exhibitor_details_nonce']) || !wp_verify_nonce($_POST['exhibitor_details_nonce'], 'save_exhibitor_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $exhibitor_data = [
        'phone' => sanitize_text_field($_POST['exhibitor_phone']),
        'email' => sanitize_email($_POST['exhibitor_email']),
        'company' => sanitize_text_field($_POST['exhibitor_company']),
        'booth' => sanitize_text_field($_POST['exhibitor_booth']),
        'notes' => sanitize_textarea_field($_POST['exhibitor_notes']),
    ];

    update_post_meta($post_id, '_exhibitor_data', $exhibitor_data);
}

add_action('save_post', 'cm_save_exhibitor_details');
?>
