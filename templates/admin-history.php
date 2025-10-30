<?php 
use \Src\Admin\AdminDisplayHistory;
?>

<div class="wrap"><h1>Transaction History</h1>

<form method="get" style="margin-bottom: 1em;">
    <input type="hidden" name="page" value="paynow_donations">

    <select style="margin-top: -5px;" name="filter_status">
        <option value="">All Statuses</option>
        <option value="NEW" <?php selected($_GET['filter_status'] ?? '', 'NEW'); ?>>NEW</option>
        <option value="PENDING" <?php selected($_GET['filter_status'] ?? '', 'PENDING'); ?>>PENDING</option>
        <option value="CONFIRMED" <?php selected($_GET['filter_status'] ?? '', 'CONFIRMED'); ?>>CONFIRMED</option>
        <option value="REJECTED" <?php selected($_GET['filter_status'] ?? '', 'REJECTED'); ?>>REJECTED</option>
        <option value="ERROR" <?php selected($_GET['filter_status'] ?? '', 'ERROR'); ?>>ERROR</option>
        <option value="EXPIRED" <?php selected($_GET['filter_status'] ?? '', 'EXPIRED'); ?>>EXPIRED</option>
        <option value="ABANDONED" <?php selected($_GET['filter_status'] ?? '', 'ABANDONED'); ?>>ABANDONED</option>
    </select>

    <select style="margin-top: -5px;" name="filet_limit">
        <option value="25" <?php selected($_GET['filter_limit'] ?? '', '25')?>>25 results</option>
        <option value="50" <?php selected($_GET['filter_limit'] ?? '', '50'); ?>>50 results</option>
        <option value="75" <?php selected($_GET['filter_limit'] ?? '', '75'); ?>>75 results</option>
        <option value="100" <?php selected($_GET['filter_limit'] ?? '', '100'); ?>>100 results</option>
        <option value="1000" <?php selected($_GET['filter_limit'] ?? '', 'ALL results'); ?>>1000 results</option>
    </select>

    <input type="number" step="0.01" name="filter_amount_min" placeholder="minimum amount" value="<?php echo esc_attr($_GET['filter_amount_min'] ?? ''); ?>">
    <input type="number" step="0.01" name="filter_amount_max" placeholder="maximum amount" value="<?php echo esc_attr($_GET['filter_amount_max'] ?? ''); ?>">
    <input style="margin-bottom: 8px;" type="text" name="filter_description" placeholder="description" value="<?php echo esc_attr($_GET['filter_descriptions'] ?? ''); ?>">
</br>
    <input type="email" name="filter_email" placeholder="email" value="<?php echo esc_attr($_GET['filter_email'] ?? ''); ?>">
    <input type="text" name="filter_name" placeholder="name" value="<?php echo esc_attr($_GET['filter_name'] ?? ''); ?>">
    <input type="text" name="filter_surname" placeholder="surname" value="<?php echo esc_attr($_GET['filter_surname'] ?? ''); ?>">
    <input type="text" name="filter_transaction_id" placeholder="transaction id" value="<?php echo esc_attr($_GET['filter_transaction_id'] ?? ''); ?>">

    <a href="<?php echo admin_url('admin.php?page=paynow_donations'); ?>" class="button">Clear</a>
    <button type="submit" class="button">Filter</button>
</form>

<?php
$current_sortby = isset($_GET['sortby']) ? $_GET['sortby'] : 'id';
$current_desc = isset( $_GET['desc']) ? $_GET['desc'] : '1';
AdminDisplayHistory::render_page($current_sortby, $current_desc) ?>

<style>

    .status{
        padding: 3px 12px;
        border-radius: 12px;
        font-weight: 600;
    }

    .NEW{
        background-color: lightblue;
        color: rgb(0, 103, 134);
    }

    .PENDING{
        background-color: 	#f3deb6;
        color: #8c6936
    }

    .CONFIRMED{
        background-color: #e3f7de;
        color: #5f9048;
    }

    .REJECTED , .ERROR{
        background-color: #f1d6d8;
        color: #8d2e38;
    }

    .EXPIRED , .ABANDONED{
        background-color: #d3d4d9;
        color: #3a3f4f;
    }

    tr:nth-child(even) {
        background-color: rgba(0, 0, 0, 0.1);
        filter: brightness(95%);
    }

    .sortButton{
        margin-left: 8px;
        margin-right: 0;
    }

    th:has(.currentSortButton) {
        background-color: rgba(0, 0, 0, 0.15);
        box-shadow: inset 0 -3px 0 #0073aa;
}

</style>