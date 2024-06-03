<?php
// Add meta box for Presenters
function cm_add_presenter_meta_box() {
    add_meta_box('presenter_details', 'Presenter Details', 'cm_presenter_meta_box', 'presenter', 'normal', 'high');
}

add_action('add_meta_boxes', 'cm_add_presenter_meta_box');

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

// Add custom columns to Presenters list
function cm_set_custom_edit_presenter_columns($columns) {
    $columns['title'] = __('Name');
    $columns['phone'] = __('Phone Number');
    $columns['email'] = __('Email');
    $columns['company'] = __('Company');
    $columns['picture'] = __('Profile Picture');
    $columns['bio'] = __('Bio');
    return $columns;
}

add_filter('manage_presenter_posts_columns', 'cm_set_custom_edit_presenter_columns');

// Populate custom columns with presenter data
function cm_custom_presenter_column($column, $post_id) {
    $presenter_data = get_post_meta($post_id, '_presenter_data', true);
    $presenter_picture = get_post_meta($post_id, '_presenter_picture', true);
    
    switch ($column) {
        case 'phone':
            echo esc_html($presenter_data['phone'] ?? 'N/A');
            break;
        case 'email':
            echo esc_html($presenter_data['email'] ?? 'N/A');
            break;
        case 'company':
            echo esc_html($presenter_data['company'] ?? 'N/A');
            break;
        case 'picture':
            if ($presenter_picture) {
                echo '<img src="' . esc_url(wp_get_attachment_url($presenter_picture)) . '" alt="Profile Picture" style="max-width: 50px; height: auto;" />';
            } else {
                echo 'N/A';
            }
            break;
        case 'bio':
            echo esc_html($presenter_data['bio'] ?? 'N/A');
            break;
    }
}

add_action('manage_presenter_posts_custom_column', 'cm_custom_presenter_column', 10, 2);
?>
