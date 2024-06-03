<?php
// Add meta box for Master Schedule
function cm_add_master_schedule_meta_box() {
    add_meta_box('master_schedule_details', __('Master Schedule Details', 'asdplc-conference-manager'), 'cm_master_schedule_meta_box', 'master_schedule', 'normal', 'high');
}
add_action('add_meta_boxes', 'cm_add_master_schedule_meta_box');

// Callback function to display the master schedule meta box
function cm_master_schedule_meta_box($post) {
    wp_nonce_field('save_master_schedule_details', 'master_schedule_details_nonce');

    // Get existing data
    $date = get_post_meta($post->ID, '_master_schedule_date', true);
    $start_time = get_post_meta($post->ID, '_master_schedule_time_start', true);
    $end_time = get_post_meta($post->ID, '_master_schedule_time_end', true);

    echo '<table class="form-table">';

    // Date
    echo '<tr>';
    echo '<th><label for="master_schedule_date">' . __('Date:', 'asdplc-conference-manager') . '</label></th>';
    echo '<td><input type="date" id="master_schedule_date" name="master_schedule_date" value="' . esc_attr($date) . '" required /></td>';
    echo '</tr>';

    // Start Time
    echo '<tr>';
    echo '<th><label for="master_schedule_time_start">' . __('Start Time:', 'asdplc-conference-manager') . '</label></th>';
    echo '<td><input type="time" id="master_schedule_time_start" name="master_schedule_time_start" value="' . esc_attr($start_time) . '" required /></td>';
    echo '</tr>';

    // End Time
    echo '<tr>';
    echo '<th><label for="master_schedule_time_end">' . __('End Time:', 'asdplc-conference-manager') . '</label></th>';
    echo '<td><input type="time" id="master_schedule_time_end" name="master_schedule_time_end" value="' . esc_attr($end_time) . '" required /></td>';
    echo '</tr>';

    echo '</table>';
}

// Save the master schedule details
function cm_save_master_schedule_details($post_id) {
    // Verify nonce
    if (!isset($_POST['master_schedule_details_nonce']) || !wp_verify_nonce($_POST['master_schedule_details_nonce'], 'save_master_schedule_details')) {
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

    // Sanitize and save data
    $date = sanitize_text_field($_POST['master_schedule_date']);
    $start_time = sanitize_text_field($_POST['master_schedule_time_start']);
    $end_time = sanitize_text_field($_POST['master_schedule_time_end']);

    update_post_meta($post_id, '_master_schedule_date', $date);
    update_post_meta($post_id, '_master_schedule_time_start', $start_time);
    update_post_meta($post_id, '_master_schedule_time_end', $end_time);
}
add_action('save_post', 'cm_save_master_schedule_details');
?>
