<?php
// Add meta box for Rooms
function cm_add_room_meta_box() {
    add_meta_box('room_details', 'Room Details', 'cm_room_meta_box', 'room', 'normal', 'high');
}

add_action('add_meta_boxes', 'cm_add_room_meta_box');

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
?>
