<?php
function cm_register_admin_pages() {
    add_menu_page('Conference Reports', 'Reports', 'manage_options', 'conference-reports', 'cm_reports_page');
}
add_action('admin_menu', 'cm_register_admin_pages');

function cm_reports_page() {
    ?>
    <div class="wrap">
        <h1>Conference Reports</h1>
        <!-- Add your report generation code here -->
    </div>
    <?php
}
?>
