<?php
/**
 * Plugin Name: Conference Manager
 * Description: A plugin to manage professional development conferences.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: asdplc-conference-manager
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load plugin textdomain
function cm_load_textdomain() {
    load_plugin_textdomain('asdplc-conference-manager', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'cm_load_textdomain');

// Include necessary files
include_once plugin_dir_path(__FILE__) . 'includes/custom-post-types.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-menus.php';
include_once plugin_dir_path(__FILE__) . 'includes/reporting.php';
include_once plugin_dir_path(__FILE__) . 'includes/reporting-budget.php';
include_once plugin_dir_path(__FILE__) . 'includes/session-registration.php';
include_once plugin_dir_path(__FILE__) . 'includes/ajax-handlers.php'; // Move AJAX functions here
include_once plugin_dir_path(__FILE__) . 'includes/admin-columns.php';

// Include meta boxes
include_once plugin_dir_path(__FILE__) . 'includes/meta-boxes/meta-boxes-sessions.php';
include_once plugin_dir_path(__FILE__) . 'includes/meta-boxes/meta-boxes-rooms.php';
include_once plugin_dir_path(__FILE__) . 'includes/meta-boxes/meta-boxes-facilitators.php';
include_once plugin_dir_path(__FILE__) . 'includes/meta-boxes/meta-boxes-presenters.php';
include_once plugin_dir_path(__FILE__) . 'includes/meta-boxes/meta-boxes-exhibitors.php';
include_once plugin_dir_path(__FILE__) . 'includes/meta-boxes/meta-boxes-master-schedule.php';

// Add role capabilities
function cm_add_role_capabilities() {
    // Get the role object
    $role = get_role('asdplc_admin');
    
    // Check if the role exists
    if ($role) {
        $capabilities = array(
            'edit_asdplc_admin_post',
            'read_asdplc_admin_post',
            'delete_asdplc_admin_post',
            'edit_asdplc_admin_posts',
            'edit_others_asdplc_admin_posts',
            'publish_asdplc_admin_posts',
            'read_private_asdplc_admin_posts',
            'delete_asdplc_admin_posts',
            'delete_private_asdplc_admin_posts',
            'delete_published_asdplc_admin_posts',
            'delete_others_asdplc_admin_posts',
            'edit_private_asdplc_admin_posts',
            'edit_published_asdplc_admin_posts',
        );

        // Add capabilities to the role
        foreach ($capabilities as $cap) {
            $role->add_cap($cap);
        }
    } else {
        // Role does not exist, handle this case
        error_log('asdplc_admin role does not exist.');
    }
}
add_action('admin_init', 'cm_add_role_capabilities');

// Flush rewrite rules on activation
function cm_flush_rewrite_rules() {
    cm_register_custom_post_types();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'cm_flush_rewrite_rules');

// Enqueue admin scripts
function cm_enqueue_admin_scripts($hook) {
    if ('post.php' != $hook && 'post-new.php' != $hook) {
        return;
    }

    if ('session' != get_post_type()) {
        return;
    }

    wp_enqueue_script('cm-admin-scripts', plugin_dir_url(__FILE__) . 'js/cm-admin.js', array('jquery'), null, true);

    wp_localize_script('cm-admin-scripts', 'cm_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('cm_ajax_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'cm_enqueue_admin_scripts');

?>
