<?php
function cm_register_admin_pages() {
    add_menu_page('Principal Assignments', 'Assignments', 'manage_options', 'principal-assignments', 'cm_principal_assignments_page');
}
add_action('admin_menu', 'cm_register_admin_pages');

function cm_principal_assignments_page() {
    ?>
    <div class="wrap">
        <h1>Principal Assignments</h1>
        <form method="post">
            <label for="attendee_id">Attendee</label>
            <select id="attendee_id" name="attendee_id" required>
                <?php
                $users = get_users();
                foreach ($users as $user) {
                    echo '<option value="' . $user->ID . '">' . $user->display_name . '</option>';
                }
                ?>
            </select>

            <label for="session_id">Session</label>
            <select id="session_id" name="session_id" required>
                <?php
                $sessions = new WP_Query(['post_type' => 'session', 'posts_per_page' => -1]);
                while ($sessions->have_posts()) : $sessions->the_post();
                    ?>
                    <option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </select>

            <input type="submit" name="assign_session" value="Assign">
        </form>
    </div>
    <?php
}

function cm_handle_principal_assignment() {
    if (isset($_POST['assign_session'])) {
        $attendee_id = intval($_POST['attendee_id']);
        $session_id = intval($_POST['session_id']);

        // Register the attendee for the session
        $registered_sessions = get_user_meta($attendee_id, '_registered_sessions', true) ?: [];
        $registered_sessions[] = $session_id;
        update_user_meta($attendee_id, '_registered_sessions', $registered_sessions);

        wp_redirect(add_query_arg('assignment_success', '1', menu_page_url('principal-assignments', false)));
        exit;
    }
}
add_action('admin_init', 'cm_handle_principal_assignment');
?>
