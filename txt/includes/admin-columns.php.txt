<?php
// Add custom columns to the Sessions post list
function cm_add_session_columns($columns) {
    return array_merge($columns, [
        'session_room' => __('Room', 'asdplc-conference-manager'),
        'session_datetime' => __('Date & Time', 'asdplc-conference-manager'),
        'session_facilitator' => __('Facilitator', 'asdplc-conference-manager'),
        'session_presenters' => __('Presenters', 'asdplc-conference-manager'),
        'session_capacity' => __('Capacity', 'asdplc-conference-manager'),
    ]);
}
add_filter('manage_session_posts_columns', 'cm_add_session_columns');

// Populate custom columns with data for Sessions
function cm_custom_session_column($column, $post_id) {
    switch ($column) {
        case 'session_room':
            $room_id = get_post_meta($post_id, '_session_room', true);
            if ($room_id) {
                echo esc_html(get_the_title($room_id));
            } else {
                echo __('N/A', 'asdplc-conference-manager');
            }
            break;

        case 'session_datetime':
            $schedule_id = get_post_meta($post_id, '_session_master_schedule', true);
            if ($schedule_id) {
                $date = get_post_meta($schedule_id, '_master_schedule_date', true);
                $start_time = get_post_meta($schedule_id, '_master_schedule_time_start', true);
                $end_time = get_post_meta($schedule_id, '_master_schedule_time_end', true);
                echo esc_html("$date $start_time - $end_time");
            } else {
                echo __('N/A', 'asdplc-conference-manager');
            }
            break;
        
        case 'session_facilitator':
            $facilitator_id = get_post_meta($post_id, '_session_data', true)['facilitator'] ?? '';
            if ($facilitator_id) {
                echo esc_html(get_the_title($facilitator_id));
            } else {
                echo __('N/A', 'asdplc-conference-manager');
            }
            break;

        case 'session_presenters':
            $presenter_ids = get_post_meta($post_id, '_session_data', true)['presenters'] ?? [];
            if (!empty($presenter_ids)) {
                $presenter_names = array_map('get_the_title', $presenter_ids);
                echo esc_html(implode(', ', $presenter_names));
            } else {
                echo __('N/A', 'asdplc-conference-manager');
            }
            break;

        case 'session_capacity':
            $registration_capacity = get_post_meta($post_id, '_registration_capacity', true);
            if ($registration_capacity) {
                echo esc_html($registration_capacity);
            } else {
                echo __('N/A', 'asdplc-conference-manager');
            }
            break;
    }
}
add_action('manage_session_posts_custom_column', 'cm_custom_session_column', 10, 2);

// Make custom columns sortable for Sessions
function cm_make_session_columns_sortable($columns) {
    $columns['session_datetime'] = 'session_datetime';
    return $columns;
}
add_filter('manage_edit-session_sortable_columns', 'cm_make_session_columns_sortable');

// Order the session list by custom columns
function cm_order_session_by_custom_columns($query) {
    if (!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');

    if ('session_datetime' === $orderby) {
        $query->set('meta_key', '_session_master_schedule');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'cm_order_session_by_custom_columns');

// Add custom columns to the Master Schedule post list
function cm_add_master_schedule_columns($columns) {
    return array_merge($columns, [
        'schedule_date' => __('Date', 'asdplc-conference-manager'),
        'schedule_start_time' => __('Start Time', 'asdplc-conference-manager'),
        'schedule_end_time' => __('End Time', 'asdplc-conference-manager'),
    ]);
}
add_filter('manage_master_schedule_posts_columns', 'cm_add_master_schedule_columns');

// Populate custom columns with data for Master Schedule
function cm_custom_master_schedule_column($column, $post_id) {
    switch ($column) {
        case 'schedule_date':
            $date = get_post_meta($post_id, '_master_schedule_date', true);
            if ($date) {
                echo esc_html($date);
            } else {
                echo __('N/A', 'asdplc-conference-manager');
            }
            break;

        case 'schedule_start_time':
            $start_time = get_post_meta($post_id, '_master_schedule_time_start', true);
            if ($start_time) {
                echo esc_html($start_time);
            } else {
                echo __('N/A', 'asdplc-conference-manager');
            }
            break;

        case 'schedule_end_time':
            $end_time = get_post_meta($post_id, '_master_schedule_time_end', true);
            if ($end_time) {
                echo esc_html($end_time);
            } else {
                echo __('N/A', 'asdplc-conference-manager');
            }
            break;
    }
}
add_action('manage_master_schedule_posts_custom_column', 'cm_custom_master_schedule_column', 10, 2);

// Make custom columns sortable for Master Schedule
function cm_make_master_schedule_columns_sortable($columns) {
    $columns['schedule_date'] = 'schedule_date';
    $columns['schedule_start_time'] = 'schedule_start_time';
    return $columns;
}
add_filter('manage_edit-master_schedule_sortable_columns', 'cm_make_master_schedule_columns_sortable');

// Order the master schedule list by custom columns
function cm_order_master_schedule_by_custom_columns($query) {
    if (!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');

    if ('schedule_date' === $orderby) {
        $query->set('meta_key', '_master_schedule_date');
        $query->set('orderby', 'meta_value ASC, meta_value_num ASC');
    } elseif ('schedule_start_time' === $orderby) {
        $query->set('meta_key', '_master_schedule_time_start');
        $query->set('orderby', 'meta_value ASC, meta_value_num ASC');
    }
}
add_action('pre_get_posts', 'cm_order_master_schedule_by_custom_columns');
?>
