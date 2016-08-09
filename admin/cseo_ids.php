<?php
/* -----------------------------------------------------------------
 * 	$Id: cseo_ids.php 872 2014-03-21 14:46:30Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */


require('includes/application_top.php');

function get_last_orders_id() {

    $orders_id = array();

    $get_orders_id = xtc_db_query("SELECT orders_id FROM orders ORDER BY orders_id DESC LIMIT 1");
    if (xtc_db_num_rows($get_orders_id) == 1) {
        $orders_id = xtc_db_fetch_array($get_orders_id);
    }
    else
        $orders_id['orders_id'] = 1;

    return $orders_id['orders_id'];
}

function get_last_customers_id() {

    $customers_id = array();

    $get_customers_id = xtc_db_query("SELECT customers_id FROM customers ORDER BY customers_id DESC LIMIT 1");
    if (xtc_db_num_rows($get_customers_id) == 1) {
        $customers_id = xtc_db_fetch_array($get_customers_id);
    }
    else
        $customers_id['customers_id'] = 1;

    return $customers_id['customers_id'];
}

function get_orders_autoindex() {

    $orders_autoindex = 1;

    $get_current_autoindex = xtc_db_query("SHOW TABLE STATUS LIKE 'orders'");
    if (xtc_db_num_rows($get_current_autoindex) == 1) {
        $row = xtc_db_fetch_array($get_current_autoindex);
        $orders_autoindex = $row['Auto_increment'];
    }

    return $orders_autoindex;
}

function get_customers_autoindex() {

    $customers_autoindex = 1;

    $get_current_autoindex = xtc_db_query("SHOW TABLE STATUS LIKE 'customers'");
    if (xtc_db_num_rows($get_current_autoindex) == 1) {
        $row = xtc_db_fetch_array($get_current_autoindex);
        $customers_autoindex = $row['Auto_increment'];
    }

    return $customers_autoindex;
}

function set_next_orders_id($next_id) {

    $success = false;

    if (is_numeric($next_id) && $next_id >= get_last_orders_id()) {
        xtc_db_query("ALTER TABLE orders AUTO_INCREMENT = " . (int) $next_id . "");
        $success = true;
    }

    return $success;
}

function set_next_customers_id($next_id) {

    $success = false;

    if (is_numeric($next_id) && $next_id >= get_last_customers_id()) {
        xtc_db_query("ALTER TABLE customers AUTO_INCREMENT = " . (int) $next_id . "");
        $success = true;
    }

    return $success;
}

if (isset($_POST['go'])) {
    // if (get_last_orders_id() + 1 > $_POST['cseo_id_starts_orders_id'] ) {
    $cseo_orders_success = set_next_orders_id($_POST['cseo_id_starts_orders_id']);
    // }
    // if (get_last_customers_id() + 1 > $_POST['cseo_id_starts_customers_id'] ) {
    $cseo_customers_success = set_next_customers_id($_POST['cseo_id_starts_customers_id']);
    // }
}

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <form name="id_starts_form" action="<?php xtc_href_link('cseo_ids.php'); ?>" method="post">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
<?php echo ID_STARTS_NEXT_ORDER_ID; ?>
                                    </td>
                                    <td>
                                        <input type="text" name="cseo_id_starts_orders_id" id="cseo_id_starts_orders_id" value="<?php echo get_orders_autoindex(); ?>" size="30" /> (<?php echo MINIMUM; ?>: <?php echo get_last_orders_id() + 1; ?>)
                                    </td>
                                </tr>
                                <tr>
                                    <td>
<?php echo ID_STARTS_NEXT_CUSTOMER_ID; ?>
                                    </td>
                                    <td>
                                        <input type="text" name="cseo_id_starts_customers_id" id="cseo_id_starts_customers_id" value="<?php echo get_customers_autoindex(); ?>" size="30" /> (<?php echo MINIMUM; ?>: <?php echo get_last_customers_id() + 1; ?>)
                                    </td>
                                </tr>
                            </table>
                            <br />
<?php
echo '<input type="submit" name="go" class="button" value="' . BUTTON_SAVE . '"/> ';
if (isset($cseo_orders_success) && $cseo_orders_success && $cseo_customers_success) {
    echo '<br />' . ID_STARTS_SUCCESS;
} elseif (isset($cseo_orders_success)) {
    echo '<br />' . ID_STARTS_NO_SUCCESS;
    echo '<br />';
    if (!$cseo_orders_success) {
        echo ID_STARTS_ORDERS_ERROR . '<br />';
    }
    if (!$cseo_customers_success) {
        echo ID_STARTS_CUSTOMERS_ERROR . '<br />';
    }
}
?>
                        </form>

                    </td>
                </tr>
            </table></td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
