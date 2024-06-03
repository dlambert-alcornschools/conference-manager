<?php
function cm_register_custom_post_types() {
    $capabilities = array(
        'edit_post' => 'edit_asdplc_admin_post',
        'read_post' => 'read_asdplc_admin_post',
        'delete_post' => 'delete_asdplc_admin_post',
        'edit_posts' => 'edit_asdplc_admin_posts',
        'edit_others_posts' => 'edit_others_asdplc_admin_posts',
        'publish_posts' => 'publish_asdplc_admin_posts',
        'read_private_posts' => 'read_private_asdplc_admin_posts',
        'delete_posts' => 'delete_asdplc_admin_posts',
        'delete_private_posts' => 'delete_private_asdplc_admin_posts',
        'delete_published_posts' => 'delete_published_asdplc_admin_posts',
        'delete_others_posts' => 'delete_others_asdplc_admin_posts',
        'edit_private_posts' => 'edit_private_asdplc_admin_posts',
        'edit_published_posts' => 'edit_published_asdplc_admin_posts',
    );

    // Sessions
    register_post_type('session', array(
        'label' => __('Sessions', 'asdplc-conference-manager'),
        'public' => true,
        'show_ui' => true,  // Ensure UI is shown
        'capability_type' => 'asdplc_admin_post',
        'capabilities' => $capabilities,
        'map_meta_cap' => true,
        'supports' => array('title', 'editor'),
        'show_in_rest' => true,
    ));

    // Repeat for other custom post types similarly
    // Rooms
    register_post_type('room', array(
        'label' => __('Rooms', 'asdplc-conference-manager'),
        'public' => true,
        'show_ui' => true,  // Ensure UI is shown
        'capability_type' => 'asdplc_admin_post',
        'capabilities' => $capabilities,
        'map_meta_cap' => true,
        'supports' => array('title'),
        'show_in_rest' => true,
    ));

    // Facilitators
    register_post_type('facilitator', array(
        'label' => __('Facilitators', 'asdplc-conference-manager'),
        'public' => true,
        'show_ui' => true,  // Ensure UI is shown
        'capability_type' => 'asdplc_admin_post',
        'capabilities' => $capabilities,
        'map_meta_cap' => true,
        'supports' => array('title', 'thumbnail'),
        'show_in_rest' => true,
    ));

    // Presenters
    register_post_type('presenter', array(
        'label' => __('Presenters', 'asdplc-conference-manager'),
        'public' => true,
        'show_ui' => true,  // Ensure UI is shown
        'capability_type' => 'asdplc_admin_post',
        'capabilities' => $capabilities,
        'map_meta_cap' => true,
        'supports' => array('title', 'thumbnail', 'editor'),
        'show_in_rest' => true,
    ));

    // Exhibitors
    register_post_type('exhibitor', array(
        'label' => __('Exhibitors', 'asdplc-conference-manager'),
        'public' => true,
        'show_ui' => true,  // Ensure UI is shown
        'capability_type' => 'asdplc_admin_post',
        'capabilities' => $capabilities,
        'map_meta_cap' => true,
        'supports' => array('title'),
        'show_in_rest' => true,
    ));

    // Master Schedule
    register_post_type('master_schedule', array(
        'label' => __('Master Schedule', 'asdplc-conference-manager'),
        'public' => true,
        'show_ui' => true,  // Ensure UI is shown
        'capability_type' => 'asdplc_admin_post',
        'capabilities' => $capabilities,
        'map_meta_cap' => true,
        'supports' => array('title'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'cm_register_custom_post_types');
?>