<?php
function cm_send_email_notification($user_id, $session_id) {
    $user_info = get_userdata($user_id);
    $session_info = get_post($session_id);

    $to = $user_info->user_email;
    $subject = 'Session Registration Confirmation';
    $message = 'You have successfully registered for the session: ' . $session_info->post_title;
    wp_mail($to, $subject, $message);
}

function cm_google_calendar_integration($session_id) {
    $session_info = get_post($session_id);
    // Add Google Calendar integration logic here
}
?>
