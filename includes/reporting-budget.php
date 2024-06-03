<?php
// Add a menu item for Budget Reporting
function cm_add_budget_report_menu() {
    add_submenu_page(
        'edit.php?post_type=session',
        __('Budget Report', 'asdplc-conference-manager'),
        __('Budget Report', 'asdplc-conference-manager'),
        'manage_options',
        'budget-report',
        'cm_budget_report_page'
    );
}
add_action('admin_menu', 'cm_add_budget_report_menu');

// Display the Budget Report page
function cm_budget_report_page() {
    $district_budget = isset($_POST['district_budget']) ? floatval($_POST['district_budget']) : 0;
    $federal_budget = isset($_POST['federal_budget']) ? floatval($_POST['federal_budget']) : 0;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        update_option('cm_district_budget', $district_budget);
        update_option('cm_federal_budget', $federal_budget);
    } else {
        $district_budget = get_option('cm_district_budget', 0);
        $federal_budget = get_option('cm_federal_budget', 0);
    }

    $sessions = get_posts(['post_type' => 'session', 'numberposts' => -1]);

    $district_total = 0;
    $federal_total = 0;
    $district_sessions = [];
    $federal_sessions = [];

    foreach ($sessions as $session) {
        $cost = floatval(get_post_meta($session->ID, '_session_cost', true));
        $fund_type = get_post_meta($session->ID, '_session_fund_type', true);
        $po_number = get_post_meta($session->ID, '_session_po_number', true);

        if ($fund_type == 'District') {
            $district_total += $cost;
            $district_sessions[] = ['session' => $session, 'po_number' => $po_number, 'cost' => $cost];
        } elseif ($fund_type == 'Federal') {
            $federal_total += $cost;
            $federal_sessions[] = ['session' => $session, 'po_number' => $po_number, 'cost' => $cost];
        }
    }

    $district_remaining = $district_budget - $district_total;
    $federal_remaining = $federal_budget - $federal_total;

    echo '<div class="wrap">';
    echo '<h1>' . __('Budget Report', 'asdplc-conference-manager') . '</h1>';
    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="district_budget">' . __('District Budget ($):', 'asdplc-conference-manager') . '</label></th>';
    echo '<td><input type="number" id="district_budget" name="district_budget" value="' . esc_attr($district_budget) . '" step="0.01" required /></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th><label for="federal_budget">' . __('Federal Budget ($):', 'asdplc-conference-manager') . '</label></th>';
    echo '<td><input type="number" id="federal_budget" name="federal_budget" value="' . esc_attr($federal_budget) . '" step="0.01" required /></td>';
    echo '</tr>';
    echo '</table>';
    echo '<p><button type="submit" class="button button-primary">' . __('Update Budgets', 'asdplc-conference-manager') . '</button></p>';
    echo '</form>';

    echo '<h2>' . __('Cost Breakdown', 'asdplc-conference-manager') . '</h2>';

    // District Budget
    echo '<h3>' . __('District', 'asdplc-conference-manager') . '</h3>';
    echo '<table class="widefat">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . __('Session', 'asdplc-conference-manager') . '</th>';
    echo '<th>' . __('PO Number', 'asdplc-conference-manager') . '</th>';
    echo '<th>' . __('Cost ($)', 'asdplc-conference-manager') . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($district_sessions as $data) {
        $session_title = get_the_title($data['session']->ID);
        $po_number = esc_html($data['po_number']);
        $cost = esc_html(number_format($data['cost'], 2));
        echo '<tr>';
        echo '<td>' . esc_html($session_title) . '</td>';
        echo '<td>' . $po_number . '</td>';
        echo '<td>' . $cost . '</td>';
        echo '</tr>';
    }
    echo '<tr>';
    echo '<td><strong>' . __('Total Cost', 'asdplc-conference-manager') . '</strong></td>';
    echo '<td></td>';
    echo '<td><strong>' . esc_html(number_format($district_total, 2)) . '</strong></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td><strong>' . __('Remaining Budget', 'asdplc-conference-manager') . '</strong></td>';
    echo '<td></td>';
    echo '<td><strong>' . esc_html(number_format($district_remaining, 2)) . '</strong></td>';
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';

    // Federal Budget
    echo '<h3>' . __('Federal', 'asdplc-conference-manager') . '</h3>';
    echo '<table class="widefat">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . __('Session', 'asdplc-conference-manager') . '</th>';
    echo '<th>' . __('PO Number', 'asdplc-conference-manager') . '</th>';
    echo '<th>' . __('Cost ($)', 'asdplc-conference-manager') . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($federal_sessions as $data) {
        $session_title = get_the_title($data['session']->ID);
        $po_number = esc_html($data['po_number']);
        $cost = esc_html(number_format($data['cost'], 2));
        echo '<tr>';
        echo '<td>' . esc_html($session_title) . '</td>';
        echo '<td>' . $po_number . '</td>';
        echo '<td>' . $cost . '</td>';
        echo '</tr>';
    }
    echo '<tr>';
    echo '<td><strong>' . __('Total Cost', 'asdplc-conference-manager') . '</strong></td>';
    echo '<td></td>';
    echo '<td><strong>' . esc_html(number_format($federal_total, 2)) . '</strong></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td><strong>' . __('Remaining Budget', 'asdplc-conference-manager') . '</strong></td>';
    echo '<td></td>';
    echo '<td><strong>' . esc_html(number_format($federal_remaining, 2)) . '</strong></td>';
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';

    echo '</div>';
}
?>
