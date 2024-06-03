<?php
function cm_add_reporting_page() {
    add_submenu_page(
        'conference-manager',
        'Reports',
        'Reports',
        'manage_options',
        'conference-reports',
        'cm_reports_page'
    );
}

add_action('admin_menu', 'cm_add_reporting_page');

function cm_reports_page() {
    echo '<h1>Conference Reports</h1>';
    cm_display_av_mismatch_report();
}

function cm_display_av_mismatch_report() {
    $sessions = get_posts([
        'post_type' => 'session',
        'numberposts' => -1
    ]);

    $rooms = get_posts([
        'post_type' => 'room',
        'numberposts' => -1
    ]);

    $room_availability = [];
    foreach ($rooms as $room) {
        $room_data = get_post_meta($room->ID, '_room_data', true);
        $room_availability[$room->ID] = $room_data['av'] ?? [];
    }

    echo '<h2>A/V Mismatch Report</h2>';
    echo '<table>';
    echo '<tr><th>Session</th><th>Room</th><th>Missing A/V</th></tr>';

    foreach ($sessions as $session) {
        $session_data = get_post_meta($session->ID, '_session_data', true);
        $session_av = $session_data['av'] ?? [];
        $session_room = get_post_meta($session->ID, '_session_room', true);

        if ($session_room && isset($room_availability[$session_room])) {
            $room_av = $room_availability[$session_room];
            $missing_av = array_diff($session_av, $room_av);

            if (!empty($missing_av)) {
                echo '<tr>';
                echo '<td>' . esc_html(get_the_title($session->ID)) . '</td>';
                echo '<td>' . esc_html(get_the_title($session_room)) . '</td>';
                echo '<td>' . esc_html(implode(', ', $missing_av)) . '</td>';
                echo '</tr>';
            }
        }
    }

    echo '</table>';
}
?>
