<?php
function cm_add_user_meta_fields($user) {
    $registered_sessions = get_user_meta($user->ID, '_registered_sessions', true);
    ?>
    <h3>Registered Sessions</h3>
    <table class="form-table">
        <tr>
            <th><label for="registered_sessions">Sessions</label></th>
            <td>
                <input type="text" id="registered_sessions" name="registered_sessions" value="<?php echo esc_attr($registered_sessions); ?>" class="regular-text"><br>
                <span class="description">Enter the session IDs the user is registered for, separated by commas.</span>
            </td>
        </tr>
    </table>
    <?php
}

add_action('show_user_profile', 'cm_add_user_meta_fields');
add_action('edit_user_profile', 'cm_add_user_meta_fields');

function cm_save_user_meta_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    if (isset($_POST['registered_sessions'])) {
        update_user_meta($user_id, '_registered_sessions', sanitize_text_field($_POST['registered_sessions']));
    }
}

add_action('personal_options_update', 'cm_save_user_meta_fields');
add_action('edit_user_profile_update', 'cm_save_user_meta_fields');
?>
