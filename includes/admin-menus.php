<?php
function cm_add_admin_menu() {
    // Main Menu Page
    add_menu_page(
        __('Conference Manager', 'asdplc-conference-manager'),
        __('Conference Manager', 'asdplc-conference-manager'),
        'manage_options',
        'conference-manager',
        'cm_conference_manager_page',
        'dashicons-welcome-learn-more',
        6
    );

    // Submenus
    add_submenu_page(
        'conference-manager',
        __('Sessions', 'asdplc-conference-manager'),
        __('Sessions', 'asdplc-conference-manager'),
        'manage_options',
        'edit.php?post_type=session'
    );

    add_submenu_page(
        'conference-manager',
        __('Rooms', 'asdplc-conference-manager'),
        __('Rooms', 'asdplc-conference-manager'),
        'manage_options',
        'edit.php?post_type=room'
    );

    add_submenu_page(
        'conference-manager',
        __('Facilitators', 'asdplc-conference-manager'),
        __('Facilitators', 'asdplc-conference-manager'),
        'manage_options',
        'edit.php?post_type=facilitator'
    );

    add_submenu_page(
        'conference-manager',
        __('Presenters', 'asdplc-conference-manager'),
        __('Presenters', 'asdplc-conference-manager'),
        'manage_options',
        'edit.php?post_type=presenter'
    );

    add_submenu_page(
        'conference-manager',
        __('Exhibitors', 'asdplc-conference-manager'),
        __('Exhibitors', 'asdplc-conference-manager'),
        'manage_options',
        'edit.php?post_type=exhibitor'
    );

    add_submenu_page(
        'conference-manager',
        __('Master Schedule', 'asdplc-conference-manager'),
        __('Master Schedule', 'asdplc-conference-manager'),
        'manage_options',
        'edit.php?post_type=master_schedule'
    );

    add_submenu_page(
        'conference-manager',
        __('Reports', 'asdplc-conference-manager'),
        __('Reports', 'asdplc-conference-manager'),
        'manage_options',
        'conference-reports',
        'cm_reports_page'
    );

    add_submenu_page(
        'conference-manager',
        __('Shortcode Guide', 'asdplc-conference-manager'),
        __('Shortcode Guide', 'asdplc-conference-manager'),
        'manage_options',
        'conference-shortcode-guide',
        'cm_shortcode_guide_page'
    );
}

add_action('admin_menu', 'cm_add_admin_menu');

function cm_conference_manager_page() {
    echo '<h1>' . __('Conference Manager Dashboard', 'asdplc-conference-manager') . '</h1>';
    echo '<p>' . __('Welcome to the Conference Manager. Use the submenus to manage different aspects of the conference.', 'asdplc-conference-manager') . '</p>';
}

/*function cm_reports_page() {
    echo '<h1>' . __('Conference Reports', 'asdplc-conference-manager') . '</h1>';
    // Add your report generation code here
}*/

function cm_shortcode_guide_page() {
    echo '<h1>' . __('Shortcode Guide', 'asdplc-conference-manager') . '</h1>';
    echo '<p>' . __('Here are the available shortcodes for the Conference Manager plugin:', 'asdplc-conference-manager') . '</p>';
    echo '<ul>';
    echo '<li><code>[session_registration_form]</code> - ' . __('Displays the session registration form.', 'asdplc-conference-manager') . '</li>';
    // Add other shortcodes here
    echo '</ul>';
}
?>
