<?php
function cm_register_attendee_custom_fields() {
    // Register custom user fields for attendees
    add_action('show_user_profile', 'cm_show_attendee_fields');
    add_action('edit_user_profile', 'cm_show_attendee_fields');
    add_action('personal_options_update', 'cm_save_attendee_fields');
    add_action('edit_user_profile_update', 'cm_save_attendee_fields');
}

add_action('admin_init', 'cm_register_attendee_custom_fields');

function cm_show_attendee_fields($user) {
    // Display custom fields for user profiles
    ?>
    <h3>Registered Sessions</h3>
    <table class="form-table">
        <tr>
            <th><label for="registered_sessions">Sessions</label></th>
            <td>
                <input type="text" name="registered_sessions" id="registered_sessions" value="<?php echo esc_attr(get_user_meta($user->ID, 'registered_sessions', true)); ?>" class="regular-text" />
                <p class="description">Comma-separated list of session IDs.</p>
            </td>
        </tr>
    </table>
    <?php
}

function cm_save_attendee_fields($user_id) {
    // Save custom user fields
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    update_user_meta($user_id, 'registered_sessions', sanitize_text_field($_POST['registered_sessions']));
}
?>
