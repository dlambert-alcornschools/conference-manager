<?php
function cm_add_feedback_link($session_id) {
    $feedback_link = 'https://www.surveymonkey.com/r/example'; // Example link
    echo '<a href="' . esc_url($feedback_link) . '" target="_blank">Leave Feedback</a>';
}

add_action('the_content', 'cm_add_feedback_link');
?>
