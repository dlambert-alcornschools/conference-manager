<?php
// Add meta box for Facilitators
function cm_add_facilitator_meta_box() {
    add_meta_box('facilitator_details', 'Facilitator Details', 'cm_facilitator_meta_box', 'facilitator', 'normal', 'high');
}

add_action('add_meta_boxes', 'cm_add_facilitator_meta_box');

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
?>
