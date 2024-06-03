<?php
// Add meta box for Exhibitors
function cm_add_exhibitor_meta_box() {
    add_meta_box('exhibitor_details', 'Exhibitor Details', 'cm_exhibitor_meta_box', 'exhibitor', 'normal', 'high');
}

add_action('add_meta_boxes', 'cm_add_exhibitor_meta_box');

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
