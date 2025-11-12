<?php 
use \Kszkl\Donations\Admin\AdminDisplayDebug;
?>

<div class="wrap"><h1>Debug</h1>

<form method="get" style="margin-bottom: 1em;">
    <input type="hidden" name="page" value="donations_for_paynow_debug">

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

    <select style="margin-top: -5px;" name="filter_limit">
        <option value="25" <?php selected($_GET['filter_limit'] ?? '', '25')?>>25 results</option>
        <option value="50" <?php selected($_GET['filter_limit'] ?? '', '50'); ?>>50 results</option>
        <option value="75" <?php selected($_GET['filter_limit'] ?? '', '75'); ?>>75 results</option>
        <option value="100" <?php selected($_GET['filter_limit'] ?? '', '100'); ?>>100 results</option>
        <option value="1000" <?php selected($_GET['filter_limit'] ?? '', 'ALL results'); ?>>1000 results</option>
    </select>

    <input type="text" name="filter_transaction_id" placeholder="transaction id" value="<?php echo esc_attr($_GET['filter_transaction_id'] ?? ''); ?>">
    <input type="text" name="filter_internal_ref" placeholder="internal_ref" value="<?php echo esc_attr($_GET['filter_internal_ref'] ?? ''); ?>">

    <a href="<?php echo admin_url('admin.php?page=donations_for_paynow_debug'); ?>" class="button">Clear</a>
    <button type="submit" class="button">Filter</button>
</form>

<?php
$current_sortby = $_GET['sortby'] ?? 'id';
$current_desc = $_GET['desc'] ?? '1';
AdminDisplayDebug::render_page($current_sortby, $current_desc) ?>