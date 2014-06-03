<?php
/* -----------------------------------------------------------------
 * 	$Id: recover_cart_sales.php 872 2014-03-21 14:46:30Z akausch $
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
require_once(DIR_WS_CLASSES . 'currencies.php');
require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.phpmailer.php');
require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
require_once(DIR_FS_INC . 'xtc_calculate_tax.inc.php');
$smarty = new Smarty();

$currencies = new currencies();

if (isset($_GET['action']) && $_GET['action'] == 'complete') {
    $cID = (int) $_GET['customer_id'];
    $_SESSION['saved_cart'] = $_SESSION['cart'];
    $_SESSION['cart'] = new shoppingCart();
    $_SESSION['cart']->restoreCustomersCart($cID);
	
    $_SESSION['payment'] = DEFAULT_RCS_PAYMENT;
    require_once (DIR_WS_CLASSES . 'class.payment.php');
    $payment_modules = new payment($_SESSION['payment']);

    $status = xtc_db_fetch_array(xtc_db_query("SELECT c.customers_status, cs.customers_status_name,  cs.customers_status_image, cs.customers_status_ot_discount_flag, cs.customers_status_ot_discount FROM " . TABLE_CUSTOMERS . " c, " . TABLE_CUSTOMERS_STATUS . " cs WHERE c.customers_status=cs.customers_status_id AND c.customers_id=" . $cID . " AND cs.language_id='" . (int) $_SESSION['languages_id'] . "';"));
    $xtPrice = new xtcPrice(DEFAULT_CURRENCY, $status['customers_status']);

    $order = new order($cID);

    if ($order->billing['country']['iso_code_2'] != '' && $order->delivery['country']['iso_code_2'] == '') {
        $_SESSION['delivery_zone'] = $order->billing['country']['iso_code_2'];
    } else {
        $_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
    }

    $shipping_num_boxes = 1;
    $_SESSION['shipping'] = DEFAULT_RCS_SHIPPING;
    $shipping_modules = new shipping($_SESSION['shipping'], 'true');

    list ($module, $method) = explode('_', $_SESSION['shipping']);
    if (is_object($$module)) {
        $quote = $shipping_modules->quote($method, $module);
        if (isset($quote['error'])) {
            unset($_SESSION['shipping']);
        } else {
            if ((isset($quote[0]['methods'][0]['title'])) && (isset($quote[0]['methods'][0]['cost']))) {
                $_SESSION['shipping'] = array('id' => $_SESSION['shipping'], 'title' => (($free_shipping == true) ? $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'), 'cost' => $quote[0]['methods'][0]['cost']);
            }
        }
    } else {
        $shipping_modules = MODULE_SHIPPING_INSTALLED;
    }
    $order = new order($cID);


    // require_once (DIR_WS_CLASSES . 'order_total.php');
    $order_total_modules = new order_total('true');
    $order_totals = $order_total_modules->process();
	// echo '<pre>';
	// print_r($order_totals);
	// echo '</pre>';
	// die;

    $tmp = false;
    $tmp_status = $order->info['order_status'];

    if ($status['customers_status_ot_discount_flag'] == 1) {
        $discount = $status['customers_status_ot_discount'];
    } else {
        $discount = '0.00';
    }

    $sql_data_array = array(
        'customers_id' => $cID,
        'customers_name' => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
        'customers_firstname' => $order->customer['firstname'],
        'customers_lastname' => $order->customer['lastname'],
        'customers_cid' => $order->customer['csID'],
        'customers_vat_id' => '',
        'customers_company' => $order->customer['company'],
        'customers_status' => $status['customers_status'],
        'customers_status_name' => $status['customers_status_name'],
        'customers_status_image' => $status['customers_status_image'],
        'customers_status_discount' => $discount,
        'customers_street_address' => $order->customer['street_address'],
        'customers_suburb' => $order->customer['suburb'],
        'customers_city' => $order->customer['city'],
        'customers_postcode' => $order->customer['postcode'],
        'customers_state' => $order->customer['state'],
        'customers_country' => $order->customer['country']['title'],
        'customers_telephone' => $order->customer['telephone'],
        'customers_email_address' => $order->customer['email_address'],
        'customers_address_format_id' => $order->customer['format_id'],
        'delivery_name' => $order->delivery['firstname'] . ' ' . $order->delivery['lastname'],
        'delivery_firstname' => $order->delivery['firstname'],
        'delivery_lastname' => $order->delivery['lastname'],
        'delivery_company' => $order->delivery['company'],
        'delivery_street_address' => $order->delivery['street_address'],
        'delivery_suburb' => $order->delivery['suburb'],
        'delivery_city' => $order->delivery['city'],
        'delivery_postcode' => $order->delivery['postcode'],
        'delivery_state' => $order->delivery['state'],
        'delivery_country' => $order->delivery['country']['title'],
        'delivery_country_iso_code_2' => $order->delivery['country']['iso_code_2'],
        'delivery_address_format_id' => $order->delivery['format_id'],
        'billing_name' => $order->billing['firstname'] . ' ' . $order->billing['lastname'],
        'billing_firstname' => $order->billing['firstname'],
        'billing_lastname' => $order->billing['lastname'],
        'billing_company' => $order->billing['company'],
        'billing_street_address' => $order->billing['street_address'],
        'billing_suburb' => $order->billing['suburb'],
        'billing_city' => $order->billing['city'],
        'billing_postcode' => $order->billing['postcode'],
        'billing_state' => $order->billing['state'],
        'billing_country' => $order->billing['country']['title'],
        'billing_country_iso_code_2' => $order->billing['country']['iso_code_2'],
        'billing_address_format_id' => $order->billing['format_id'],
        'payment_method' => $order->info['payment_method'],
        'payment_class' => $order->info['payment_class'],
        'shipping_method' => $order->info['shipping_method'],
        'shipping_class' => $order->info['shipping_class'],
        'date_purchased' => 'now()',
        'orders_status' => $tmp_status,
        'currency' => $order->info['currency'],
        'currency_value' => $order->info['currency_value'],
        'customers_ip' => $customers_ip,
        'language' => $_SESSION['language'],
        'comments' => $order->info['comments']);

    xtc_db_perform(TABLE_ORDERS, $sql_data_array);
    $insert_id = xtc_db_insert_id();
    $_SESSION['tmp_oID'] = $insert_id;
    for ($i = 0, $n = sizeof($order_totals); $i < $n; $i++) {
        $sql_data_array = array('orders_id' => $insert_id, 'title' => $order_totals[$i]['title'], 'text' => $order_totals[$i]['text'], 'value' => $order_totals[$i]['value'], 'class' => $order_totals[$i]['code'], 'sort_order' => $order_totals[$i]['sort_order']);
        xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
    }

    $customer_notification = (SEND_EMAILS == 'true') ? '1' : '0';
    $sql_data_array = array('orders_id' => $insert_id, 'orders_status_id' => $order->info['order_status'], 'date_added' => 'now()', 'customer_notified' => $customer_notification, 'comments' => $order->info['comments']);
    xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

    // initialized for the email confirmation
    $products_ordered = '';
    $products_ordered_html = '';
    $subtotal = 0;
    $total_tax = 0;

    for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
        // Stock Update - Joao Correia
        if (STOCK_LIMITED == 'true') {
            if (DOWNLOAD_ENABLED == 'true') {
                $stock_query_raw = "SELECT products_quantity, pad.products_attributes_filename
                                        FROM " . TABLE_PRODUCTS . " p
                                        LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                        ON p.products_id=pa.products_id
                                        LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                        ON pa.products_attributes_id=pad.products_attributes_id
                                        WHERE p.products_id = '" . xtc_get_prid($order->products[$i]['id']) . "'";
                // Will work with only one option for downloadable products
                // otherwise, we have to build the query dynamically with a loop
                $products_attributes = $order->products[$i]['attributes'];
                if (is_array($products_attributes)) {
                    $stock_query_raw .= " AND pa.options_id = '" . $products_attributes[0]['option_id'] . "' AND pa.options_values_id = '" . $products_attributes[0]['value_id'] . "'";
                }
                $stock_query = xtc_db_query($stock_query_raw);
            } else {
                $stock_query = xtc_db_query("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . xtc_get_prid($order->products[$i]['id']) . "'");
            }
            if (xtc_db_num_rows($stock_query) > 0) {
                $stock_values = xtc_db_fetch_array($stock_query);
                // do not decrement quantities if products_attributes_filename exists
                if ((DOWNLOAD_ENABLED != 'true') || (!$stock_values['products_attributes_filename'])) {
                    $stock_left = $stock_values['products_quantity'] - $order->products[$i]['qty'];
                } else {
                    $stock_left = $stock_values['products_quantity'];
                }
                xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_quantity = '" . $stock_left . "' WHERE products_id = '" . xtc_get_prid($order->products[$i]['id']) . "';");
            }
        }

        // Update products_ordered (for bestsellers list)
        xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_ordered = products_ordered + " . sprintf('%d', $order->products[$i]['qty']) . " WHERE products_id = '" . xtc_get_prid($order->products[$i]['id']) . "';");

        $sql_data_array = array('orders_id' => $insert_id, 'products_id' => xtc_get_prid($order->products[$i]['id']), 'products_model' => $order->products[$i]['model'], 'products_name' => $order->products[$i]['name'], 'products_shipping_time' => $order->products[$i]['shipping_time'], 'products_price' => $order->products[$i]['price'], 'final_price' => $order->products[$i]['final_price'], 'products_tax' => $order->products[$i]['tax'], 'products_discount_made' => $order->products[$i]['discount_allowed'], 'products_quantity' => $order->products[$i]['qty'], 'allow_tax' => $_SESSION['customers_status']['customers_status_show_price_tax']);

        xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
        $order_products_id = xtc_db_insert_id();

        // Aenderung Specials Quantity Anfang
        $specials_result = xtc_db_query("SELECT products_id, specials_quantity from " . TABLE_SPECIALS . " WHERE products_id = '" . xtc_get_prid($order->products[$i]['id']) . "' ");
        if (xtc_db_num_rows($specials_result)) {
            $spq = xtc_db_fetch_array($specials_result);

            $new_sp_quantity = ($spq['specials_quantity'] - $order->products[$i]['qty']);

            if ($new_sp_quantity >= 1) {
                xtc_db_query("update " . TABLE_SPECIALS . " set specials_quantity = '" . $new_sp_quantity . "' where products_id = '" . xtc_get_prid($order->products[$i]['id']) . "' ");
            } else {
                xtc_db_query("update " . TABLE_SPECIALS . " set status = '0', specials_quantity = '" . $new_sp_quantity . "' where products_id = '" . xtc_get_prid($order->products[$i]['id']) . "' ");
            }
        }
        // Aenderung Ende

        $order_total_modules->update_credit_account($i); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM
        //------insert customer choosen option to order--------
        $attributes_exist = '0';
        $products_ordered_attributes = '';
        if (isset($order->products[$i]['attributes'])) {
            $attributes_exist = '1';
            for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j++) {
                if (DOWNLOAD_ENABLED == 'true') {
                    $attributes_query = "SELECT popt.products_options_name,
                                              poval.products_options_values_name,
                                              pa.options_values_price,
                                              pa.price_prefix,
                                              pad.products_attributes_maxdays,
                                              pad.products_attributes_maxcount,
                                              pad.products_attributes_filename
                                       FROM " . TABLE_PRODUCTS_OPTIONS . " popt,
                                            " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval,
                                            " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                       LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                       ON pa.products_attributes_id=pad.products_attributes_id
                                       WHERE pa.products_id = '" . $order->products[$i]['id'] . "'
                                       AND pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "'
                                       AND pa.options_id = popt.products_options_id
                                       AND pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                       AND pa.options_values_id = poval.products_options_values_id
                                       AND popt.language_id = " . (int) $_SESSION['languages_id'] . "
                                       AND poval.language_id = " . (int) $_SESSION['languages_id'];
                    $attributes = xtc_db_query($attributes_query);
                } else {
                    $attributes = xtc_db_query("SELECT popt.products_options_name,
                                             poval.products_options_values_name,
                                             pa.options_values_price,
                                             pa.price_prefix
                                      FROM " . TABLE_PRODUCTS_OPTIONS . " popt,
                                           " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval,
                                           " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                      WHERE pa.products_id = '" . $order->products[$i]['id'] . "'
                                      AND pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "'
                                      AND pa.options_id = popt.products_options_id
                                      AND pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                      AND pa.options_values_id = poval.products_options_values_id
                                      AND popt.language_id = " . (int) $_SESSION['languages_id'] . "
                                      AND poval.language_id = " . (int) $_SESSION['languages_id']);
                }
                // update attribute stock
                xtc_db_query("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . "
                      SET attributes_stock=attributes_stock - '" . $order->products[$i]['qty'] . "'
                                  WHERE products_id='" . $order->products[$i]['id'] . "'
                                  AND options_values_id='" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                  AND options_id='" . $order->products[$i]['attributes'][$j]['option_id'] . "'");

                $attributes_values = xtc_db_fetch_array($attributes);

                $sql_data_array = array('orders_id' => $insert_id, 'orders_products_id' => $order_products_id, 'products_options' => $attributes_values['products_options_name'], 'products_options_values' => $attributes_values['products_options_values_name'], 'options_values_price' => $attributes_values['options_values_price'], 'price_prefix' => $attributes_values['price_prefix']);
                xtc_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

                if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && xtc_not_null($attributes_values['products_attributes_filename'])) {
                    $sql_data_array = array('orders_id' => $insert_id, 'orders_products_id' => $order_products_id, 'orders_products_filename' => $attributes_values['products_attributes_filename'], 'download_maxdays' => $attributes_values['products_attributes_maxdays'], 'download_count' => $attributes_values['products_attributes_maxcount']);
                    xtc_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
                }
            }
        }
        //------insert customer choosen option eof ----
        $total_weight += ($order->products[$i]['qty'] * $order->products[$i]['weight']);
        $total_tax += xtc_calculate_tax($total_products_price, $products_tax) * $order->products[$i]['qty'];
        $total_cost += $total_products_price;
    }
    if (RCS_DELETE_COMPLETED_ORDERS == 'true') {
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id=" . $cID);
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id=" . $cID);
        xtc_db_query("DELETE FROM " . TABLE_SCART . " WHERE customers_id=" . $cID);
    }
    $_SESSION['cart'] = $_SESSION['saved_cart'];
    xtc_redirect(xtc_href_link(FILENAME_ORDERS, "oID=" . $insert_id . "&action=edit"));
}



// Delete Entry Begin
if ($_GET['action'] == 'delete') {
    $cID = (int) $_GET['customer_id'];
    xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id=" . $cID);
    xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id=" . $cID);
    xtc_db_query("DELETE FROM " . TABLE_SCART . " WHERE customers_id=" . $cID);

    xtc_redirect(xtc_href_link(FILENAME_RECOVER_CART_SALES, 'delete=1&customer_id=' . $_GET['customer_id'] . '&tdate=' . $_GET['tdate']));
}

if ($_GET['delete']) {
    $messageStack->add(MESSAGE_STACK_CUSTOMER_ID . $_GET['customer_id'] . MESSAGE_STACK_DELETE_SUCCESS, 'success');
}

// Delete Entry End
$tdate = $_POST['tdate'];
if ($tdate == '')
    $tdate = RCS_BASE_DAYS;

function seadate($day) {
    $rawtime = strtotime("-" . $day . " days");
    $ndate = date("Ymd", $rawtime);
    return $ndate;
}

function cart_date_short($raw_date) {
    if (($raw_date == '00000000') || ($raw_date == ''))
        return false;

    $year = substr($raw_date, 0, 4);
    $month = (int) substr($raw_date, 4, 2);
    $day = (int) substr($raw_date, 6, 2);

    if (@date('Y', mktime(0, 0, 0, $month, $day, $year)) == $year) {
        return date(DATE_FORMAT, mktime(0, 0, 0, $month, $day, $year));
    } else {
        return preg_replace("/2037/i", $year, date(DATE_FORMAT, mktime(0, 0, 0, $month, $day, 2037)));
    }
}

// This will return a list of customers with sessions. Handles either the mysql or file case
// Returns an empty array if the check sessions flag is not true (empty array means same SQL statement can be used)
function _GetCustomerSessions() {
    $cust_ses_ids = array();

    if (RCS_CHECK_SESSIONS == 'true') {
        if (STORE_SESSIONS == 'mysql') {
            // --- DB RECORDS ---
            $sesquery = xtc_db_query("select value from " . TABLE_SESSIONS . " where 1");
            while ($ses = xtc_db_fetch_array($sesquery)) {
                if (preg_match("/customer_id[^\"]*\"([0-9]*)\"/i", $ses['value'], $custval))
                    $cust_ses_ids[] = $custval[1];
            }
        }
        else {    // --- FILES ---
            if ($handle = opendir(xtc_session_save_path())) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $file = xtc_session_save_path() . '/' . $file;    // create full path to file!
                        if ($fp = fopen($file, 'r')) {
                            $val = fread($fp, filesize($file));
                            fclose($fp);

                            if (preg_match("/customer_id[^\"]*\"([0-9]*)\"/i", $val, $custval))
                                $cust_ses_ids[] = $custval[1];
                        }
                    }
                }
                closedir($handle);
            }
        }
    }
    return $cust_ses_ids;
}

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
<?php if (count($_POST['custid']) > 0) { ?>
                <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="pageHeading" align="left" colspan=2 width="50%"><? echo HEADING_TITLE; ?> </td>
                        <td class="pageHeading" align="left" colspan=4 width="50%"><? echo HEADING_EMAIL_SENT; ?> </td>
                    </tr>
                </table>
                <table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                    <tr class="dataTableHeadingRow">
                        <td class="dataTableHeadingContent" align="left" colspan="1" width="15%" nowrap><?php echo TABLE_HEADING_CUSTOMER; ?></td>
                        <td class="dataTableHeadingContent" align="left" colspan="1" width="30%" nowrap>&nbsp;</td>
                        <td class="dataTableHeadingContent" align="left" colspan="1" width="25%" nowrap>&nbsp;</td>
                        <td class="dataTableHeadingContent" align="left" colspan="1" width="10%" nowrap>&nbsp;</td>
                        <td class="dataTableHeadingContent" align="left" colspan="1" width="10%" nowrap>&nbsp;</td>
                        <td class="dataTableHeadingContent" align="left" colspan="1" width="10%" nowrap>&nbsp;</td>
                    </tr>
                    <tr class="dataTableHeadingRow">
                        <td class="dataTableHeadingContent" align="left"   colspan="1"  width="15%" nowrap><?php echo TABLE_HEADING_MODEL; ?></td>
                        <td class="dataTableHeadingContent" align="left"   colspan="2"  width="55%" nowrap><?php echo TABLE_HEADING_DESCRIPTION; ?></td>
                        <td class="dataTableHeadingContent" align="center" colspan="1"  width="10%" nowrap> <?php echo TABLE_HEADING_QUANTY; ?></td>
                        <td class="dataTableHeadingContent" align="right"  colspan="1"  width="10%" nowrap><?php echo TABLE_HEADING_PRICE; ?></td>
                        <td class="dataTableHeadingContent" align="right"  colspan="1"  width="10%" nowrap><?php echo TABLE_HEADING_TOTAL; ?></td>
                    </tr>
                </table>
    <?php
    foreach ($_POST['custid'] as $cid) {
        $quantity = array();
        $products_data = array();
        $quantityQuery = xtc_db_query("SELECT products_id pid, customers_basket_quantity qty FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id=" . $cid);
        while ($quantityResult = xtc_db_fetch_array($quantityQuery)) {
            $quantity[(int) $quantityResult['pid']] += $quantityResult['qty'];
        }
        $query1 = xtc_db_query("select cb.products_id pid,
                                    cb.customers_basket_quantity qty,
                                    cb.customers_basket_date_added bdate,
                                    cb.checkout_site site,
                                    cb.language,
                                    cus.customers_firstname fname,
                                    cus.customers_lastname lname,
                                    cus.customers_gender,
                                    cus.customers_email_address email,
                                    co.countries_iso_code_2 iso
                          from      " . TABLE_CUSTOMERS_BASKET . " cb,
                                    " . TABLE_CUSTOMERS . " cus,
                                    " . TABLE_ADDRESS_BOOK . " ab,
                                    " . TABLE_COUNTRIES . " co
                          where     cb.customers_id = cus.customers_id
                          and       cus.customers_id = '" . $cid . "'
                          and       cus.customers_default_address_id = ab.address_book_id
                          and       co.countries_id=ab.entry_country_id
                          order by  cb.customers_basket_date_added desc ");

        $knt = xtc_db_num_rows($query1);
        $td = 1;
        for ($i = 0; $i < $knt; $i++) {
            $inrec = xtc_db_fetch_array($query1);
            $aprice = 0;
            // set new cline and curcus
            if ($lastcid != $cid) {
                if ($lastcid != "") {
                    $text_total = RCS_SHOW_BRUTTO_PRICE == 'true' ? TABLE_CART_TOTAL_BRUTTO : TABLE_CART_TOTAL;

                    $cline .= "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" class=\"dataTable\">
              <tr>
                 <td class='dataTableContent' align='right' colspan='6' nowrap><b>" . $text_total . "</b>" . $currencies->format($tprice) . "</td>
              </tr>
              <tr>
                 <td colspan='6' align='right'><a class=\"button\" href=" . xtc_href_link(FILENAME_RECOVER_CART_SALES, "action=delete&customer_id=" . $cid . "&tdate=" . $tdate) . ">" . BUTTON_DELETE . "</a></td>
              </tr>";
                    echo $cline;
                }
                $cline = "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" class=\"dataTable\">
						<tr> 
							<td class='dataTableContent' align='left' colspan='6' nowrap><a href='" . xtc_href_link(FILENAME_CUSTOMERS, 'search=' . $inrec['lname'], 'NONSSL') . "'>" . $inrec['fname'] . " " . $inrec['lname'] . "</a>" . $customer . "
							</td>
						</tr>";
                $tprice = 0;
            }
            $lastcid = $cid;

            // get the shopping cart
            $query2 = xtc_db_query("select  p.products_price price,
                                        p.products_model model,
                                        p.products_tax_class_id tax,
                                        p.products_image image,
                                        pd.products_name name
                                from    " . TABLE_PRODUCTS . " p,
                                        " . TABLE_PRODUCTS_DESCRIPTION . " pd
                                where   p.products_id = '" . $inrec['pid'] . "' and
                                        pd.products_id = p.products_id and
                                        pd.language_id = " . (int) $_SESSION['languages_id']);

            $inrec2 = xtc_db_fetch_array($query2);

            $sprice = xtc_get_products_special_price($inrec['pid'], $cid, ($inrec['qty'] < $quantity[(int) $inrec['pid']] ? $quantity[(int) $inrec['pid']] : $inrec['qty']));
            // BEGIN OF ATTRIBUTE DB CODE
            $prodAttribs = ''; // DO NOT DELETE
            if (RCS_SHOW_ATTRIBUTES == 'true') {
                $attribquery = xtc_db_query("select cba.products_id pid,
                                                po.products_options_name poname,
                                                pov.products_options_values_name povname,
                                                pa.options_values_price price
                                         from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " cba,
                                              " . TABLE_PRODUCTS_OPTIONS . " po,
                                              " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov,
                                              " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                         where cba.products_id = '" . $inrec['pid'] . "'
                                         and cba.customers_id = " . $cid . "
                                         and po.products_options_id = cba.products_options_id
                                         and pov.products_options_values_id = cba.products_options_value_id
                                         and pa.products_id = " . (int) $inrec['pid'] . "
                                         and pa.options_id = cba.products_options_id
                                         and pa.options_values_id = cba.products_options_value_id
                                         and po.language_id = " . (int) $_SESSION['languages_id'] . "
                                         and pov.language_id = " . (int) $_SESSION['languages_id']);
                $hasAttributes = false;

                if (xtc_db_num_rows($attribquery)) {
                    $hasAttributes = true;
                    $prodAttribs = '<br>';
                    while ($attribrecs = xtc_db_fetch_array($attribquery)) {
                        $prodAttribs .= '<small><em> - ' . $attribrecs['poname'] . ' ' . $attribrecs['povname'] . '</em></small><br >';
                        $aprice += $attribrecs['price'];
                    }
                }
            }
            if ($sprice == 0)
                $sprice = $inrec2['price'];
            $sprice += $aprice;
            if (RCS_SHOW_BRUTTO_PRICE == 'true') {
                $tax = xtc_get_tax_rate($inrec2['tax']);
                $sprice = xtc_add_tax($sprice, $tax);
            }

            $tprice = $tprice + ($inrec['qty'] * $sprice);
            $pprice_formated = $currencies->format($sprice);
            $tpprice_formated = $currencies->format(($inrec['qty'] * $sprice));

            $cline .= "<tr class='" . $f . "'>
                    <td class='dataTableContent' align='left'   width='15%' nowrap>" . ($inrec2['model'] ? $inrec2['model'] : '&nbsp;') . "</td>
                    <td class='dataTableContent' align='left'  colspan='2' width='55%'><a href='" . xtc_href_link(FILENAME_CATEGORIES, 'action=new_product&pID=' . $inrec['pid'], 'NONSSL') . "'>" . $inrec2['name'] . "</a></td>
                    <td class='dataTableContent' align='center' width='10%' nowrap>" . $inrec['qty'] . "</td>
                    <td class='dataTableContent' align='right'  width='10%' nowrap>" . $pprice_formated . "</td>
                    <td class='dataTableContent' align='right'  width='10%' nowrap>" . $tpprice_formated . "</td>
                 </tr>";
            $products_data[] = array(
                'QUANTITY' => $inrec['qty'],
                'NAME' => $inrec2['name'],
                'LINK' => xtc_catalog_href_link(FILENAME_CATALOG_PRODUCT_INFO, xtc_product_link($inrec['pid'])),
                'IMAGE' => HTTP_SERVER . DIR_WS_CATALOG_INFO_IMAGES . $inrec2['image']);
            $td++;
        }

        $cline .= "</td></tr>";

        if ($inrec['language'] == null) {
            switch ($inrec['iso']) {
                case 'DE':
                case 'AT':
                case 'CH':
                    $inrec['language'] = 'german';
                    break;
                /*
                  case 'IT':
                  $inrec['language'] = 'italian';
                  break;

                  case 'ES':
                  case 'AR':
                  case 'MX':
                  $inrec['language'] = 'spanish';
                  break;

                  case 'FR':
                  case 'BE':
                  case 'LU':
                  case 'LI':
                  $inrec['language'] = 'french';
                  break;
                 */
                default:
                    $inrec['language'] = 'english';
            }
        }

        $cquery = xtc_db_query("select * from orders where customers_id = '" . $cid . "'");


        $smarty->assign('language', $inrec['language']);
        $smarty->caching = false;
        $smarty->template_dir = DIR_FS_CATALOG . 'templates';
        $smarty->compile_dir = DIR_FS_CATALOG . 'templates_c';
        $smarty->config_dir = DIR_FS_CATALOG . 'lang';
        $smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
        $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
        $smarty->assign('products_data', $products_data);
        $smarty->assign('LOGIN', xtc_catalog_href_link(FILENAME_CATALOG_LOGIN, '', 'SSL'));

        //$custname = $inrec['fname']." ".$inrec['lname'];
        if (RCS_EMAIL_FRIENDLY == 'true') {
            $smarty->assign('GENDER', $inrec['customers_gender']);
            $smarty->assign('FIRSTNAME', $inrec['fname']);
            $smarty->assign('LASTNAME', $inrec['lname']);
        } else {
            $smarty->assign('GENDER', false);
        }

        if (xtc_db_num_rows($cquery) < 1) {
            $smarty->assign('NEW', true);
        } else {
            $smarty->assign('NEW', false);
        }

        $smarty->assign('STORE_LINK', xtc_catalog_href_link('', ''));
        $smarty->assign('STORE_NAME', STORE_NAME);

        $smarty->assign('MESSAGE', $_POST['message']);

        $outEmailAddr = '"' . $custname . '" <' . $inrec['email'] . '>';
        if (xtc_not_null(RCS_EMAIL_COPIES_TO))
            $outEmailAddr .= ', ' . RCS_EMAIL_COPIES_TO;


		require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
		$smarty->caching = false;
		$html_mail = $smarty->fetch('html:recover_cart_sales');
		$html_mail .= $signatur_html;
		// require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
		$smarty->caching = false;
		$txt_mail = $smarty->fetch('txt:recover_cart_sales');
		$txt_mail .= $signatur_text;
		require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
		$mail_data = cseo_get_mail_data('recover_cart_sales');


        // create subject
        $recover_cart_sales_subject = str_replace('{$nr}', $insert_id, $mail_data['EMAIL_SUBJECT']);
        $recover_cart_sales_subject = str_replace('{$date}', strftime(DATE_FORMAT_LONG), $recover_cart_sales_subject);
        $recover_cart_sales_subject = str_replace('{$lastname}', $order->customer['lastname'], $recover_cart_sales_subject);
        $recover_cart_sales_subject = str_replace('{$firstname}', $order->customer['firstname'], $recover_cart_sales_subject);
        $recover_cart_sales_subject = str_replace('{$shop_besitzer}', STORE_OWNER, $recover_cart_sales_subject);
        $recover_cart_sales_subject = str_replace('{$shop_name}', STORE_NAME, $recover_cart_sales_subject);

        if ($inrec['email'] != '') {
            xtc_php_mail($mail_data['EMAIL_ADDRESS'], 
						$from_name, 
						$inrec['email'], 
						$custname, 
						'', 
						$mail_data['EMAIL_REPLAY_ADDRESS'], 
						$from_name, 
						'', 
						'', 
						$recover_cart_sales_subject, 
						$html_mail, 
						$txt_mail);
        }
        // See if a record for this customer already exists; if not create one and if so update it
        $donequery = xtc_db_query("select * from " . TABLE_SCART . " where customers_id = '" . $cid . "'");
        if (xtc_db_num_rows($donequery) == 0)
            xtc_db_query("insert into " . TABLE_SCART . " (customers_id, dateadded, datemodified ) values ('" . $cid . "', '" . seadate('0') . "', '" . seadate('0') . "')");
        else
            xtc_db_query("update " . TABLE_SCART . " set datemodified = '" . seadate('0') . "' where customers_id = " . $cid);

        echo $cline;
        $cline = "";
        $text_total = RCS_SHOW_BRUTTO_PRICE == 'true' ? TABLE_CART_TOTAL_BRUTTO : TABLE_CART_TOTAL;
    }
    echo "<tr><td colspan=8 align='right' class='dataTableContent'><b>" . $text_total . "</b>" . $currencies->format($tprice) . "</td> </tr>";
    echo "<tr><td colspan=6 align='right'><a class=\"button\" href=" . xtc_href_link(FILENAME_RECOVER_CART_SALES, "action=delete&customer_id=" . $cid . "&tdate=" . $tdate) . ">" . BUTTON_DELETE . "</a></td>  </tr>\n";
    echo "<tr><td colspan=6 align=center><a href=" . $PHP_SELF . ">" . TEXT_RETURN . "</a></td></tr>";
} else {     //we are NOT doing an e-mail to some customers
    ?>
                <!-- REPORT TABLE BEGIN //-->
                <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="pageHeading" align="left" width="50%" colspan="4"><?php echo HEADING_TITLE; ?></td>
                        <td class="pageHeading" align="right" width="50%" colspan="4">
                            <form method="post" action="<?php echo $PHP_SELF; ?>">
                                <table align="right" width="100%">
                                    <tr class="dataTableContent" align="right">
                                        <td><?php echo DAYS_FIELD_PREFIX; ?><input type=text size=4 width=4 value=<?php echo $tdate; ?> name=tdate><?php echo DAYS_FIELD_POSTFIX; ?><input class="button" type=submit value="<?php echo DAYS_FIELD_BUTTON; ?>"></td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                    </tr>
                </table>
                <form method="post" action="<?php echo $PHP_SELF; ?>">
                    <table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                        <tr class="dataTableHeadingRow">
                            <th class="dataTableHeadingContent" align="left" colspan="2" width="10%" nowrap><?php echo TABLE_HEADING_CONTACT; ?></th>
                            <th class="dataTableHeadingContent" align="left" colspan="1" width="15%" nowrap><?php echo TABLE_HEADING_DATE; ?></th>
                            <th class="dataTableHeadingContent" align="left" colspan="1" width="30%" nowrap><?php echo TABLE_HEADING_CUSTOMER; ?></th>
                            <th class="dataTableHeadingContent" align="left" colspan="1" width="20%" nowrap><?php echo TABLE_HEADING_EMAIL; ?></th>
                            <th class="dataTableHeadingContent" align="left" colspan="1" width="10%" nowrap><?php echo TABLE_HEADING_STOPPED; ?></th>
                            <th class="dataTableHeadingContent" align="left" colspan="2" width="15%" nowrap><?php echo TABLE_HEADING_PHONE; ?></th>
                        </tr>
                        <tr class="dataTableHeadingRow">
                            <th class="dataTableHeadingContent" align="left"   colspan="2"  width="10%" nowrap><?php echo TABLE_HEADING_OUT_DATE ?> </th>
                            <th class="dataTableHeadingContent" align="left"   colspan="1"  width="15%" nowrap><?php echo TABLE_HEADING_MODEL; ?></th>
                            <th class="dataTableHeadingContent" align="left"   colspan="2" width="55%" nowrap><?php echo TABLE_HEADING_DESCRIPTION; ?></th>
                            <th class="dataTableHeadingContent" align="center" colspan="1" width="5%" nowrap> <?php echo TABLE_HEADING_QUANTY; ?></th>
                            <th class="dataTableHeadingContent" align="right"  colspan="1"  width="5%" nowrap><?php echo TABLE_HEADING_PRICE; ?></th>
                            <th class="dataTableHeadingContent" align="right"  colspan="1" width="10%" nowrap><?php echo TABLE_HEADING_TOTAL; ?></th>
                        </tr>
    <?php
    if ($cust_ses_ids = _GetCustomerSessions()) {
        $cust_sql = " AND customers_id not in ('" . implode(", ", $cust_ses_ids) . "') ";
        //echo "-" . $cust_sql;
    }
    $ndate = seadate($tdate);
    $query1 = xtc_db_query("SELECT customers_id, MAX(customers_basket_date_added) as last FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_basket_date_added>='" . $ndate . "' " . $cust_sql . " GROUP BY customers_id ORDER BY last DESC, customers_id");

    $results = 0;
    $curcus = "";
    $tprice = 0;
    $totalAll = 0;
    $first_line = true;
    $final_line = false;
    $skip = false;
    $knt = xtc_db_num_rows($query1);
    $i = 1;
    while ($query1Res = xtc_db_fetch_array($query1)) {
        $cline .= '<table width="100%" cellspacing="0" cellpadding="0" class="dataTable">';
        $quantity = array();
        $quantityQuery = xtc_db_query("SELECT products_id pid, customers_basket_quantity qty FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id=" . $query1Res['customers_id']);
        while ($quantityResult = xtc_db_fetch_array($quantityQuery)) {
            $quantity[(int) $quantityResult['pid']] += $quantityResult['qty'];
        }
        $query2 = xtc_db_query("SELECT cb.customers_id cid,
                                 cb.products_id pid,
                                 cb.customers_basket_quantity qty,
                                 cb.customers_basket_date_added bdate,
                                 cb.checkout_site site,
                                 cus.customers_firstname fname,
                                 cus.customers_lastname lname,
                                 cus.customers_telephone phone,
                                 cus.customers_email_address email
                           FROM  " . TABLE_CUSTOMERS_BASKET . " cb,
                                 " . TABLE_CUSTOMERS . " cus
                           WHERE cb.customers_id = cus.customers_id
                           AND   cb.customers_id = " . $query1Res['customers_id'] . "
                           ORDER BY cb.customers_basket_date_added DESC");

        while ($data = xtc_db_fetch_array($query2)) {
            $inrec = $data;
            //reset attributes price
            $aprice = 0;
            // If this is a new customer, create the appropriate HTML
            if ($curcus != $inrec['cid']) {
                // output line
                $final_line = true;
                // set new cline and curcus
                $curcus = $inrec['cid'];
                if ($curcus != "") {
                    $tprice = 0;

                    // change the color on those we have contacted add customer tag to customers
                    $fcolor = RCS_UNCONTACTED_COLOR;
                    $checked = 1;    // assume we'll send an email
                    $new = 1;
                    $skip = false;
                    $sentdate = "";
                    $beforeDate = RCS_CARTS_MATCH_ALL_DATES == 'true' ? '0' : $inrec['bdate'];
                    $customer = $inrec['fname'] . " " . $inrec['lname'];
                    $status = "";

                    $donequery = xtc_db_query("select * from " . TABLE_SCART . " where customers_id = '" . $curcus . "'");
                    $emailttl = seadate(RCS_EMAIL_TTL);

                    if (xtc_db_num_rows($donequery) > 0) {
                        $ttl = xtc_db_fetch_array($donequery);
                        if ($ttl) {
                            if (xtc_not_null($ttl['datemodified']))    // allow for older scarts that have no datemodified
                                $ttldate = $ttl['datemodified'];
                            else
                                $ttldate = $ttl['dateadded'];

                            if ($emailttl <= $ttldate) {
                                $sentdate = $ttldate;
                                $fcolor = RCS_CONTACTED_COLOR;
                                $checked = 0;
                                $new = 0;
                            }
                        }
                    }

                    // See if the customer has purchased from us before
                    // Customers are identified by either their customer ID or name or email address
                    // If the customer has an order with items that match the current order, assume order completed, bail on this entry!
                    $ccquery = xtc_db_query('SELECT orders_id, orders_status
												        FROM ' . TABLE_ORDERS . '
												        WHERE (customers_id = ' . (int) $curcus . '
												        OR customers_email_address like "' . $inrec['email'] . '"
												        OR customers_name like "' . $inrec['fname'] . ' ' . $inrec['lname'] . '")
												        AND date_purchased >= "' . $beforeDate . '"');
                    if (xtc_db_num_rows($ccquery) > 0) {
                        // We have a matching order; assume current customer but not for this order
                        $customer = '<font color=' . RCS_CURCUST_COLOR . '><b>' . $customer . '</b></font>';

                        // Now, look to see if one of the orders matches this current order's items
                        while ($orec = xtc_db_fetch_array($ccquery)) {
                            $ccquery = xtc_db_query('select products_id from ' . TABLE_ORDERS_PRODUCTS . ' where orders_id = ' . (int) $orec['orders_id'] . ' AND products_id = ' . (int) $inrec['pid']);
                            if (xtc_db_num_rows($ccquery) > 0) {
                                if ($orec['orders_status'] > RCS_PENDING_SALE_STATUS)
                                    $checked = 0;

                                // OK, we have a matching order; see if we should just skip this or show the status
                                if (RCS_SKIP_MATCHED_CARTS == 'true' && !$checked) {
                                    $skip = true;    // reset flag & break us out of the while loop!
                                    break;
                                } else {
                                    // It's rare for the same customer to order the same item twice, so we probably have a matching order, show it
                                    $fcolor = RCS_MATCHED_ORDER_COLOR;
                                    $ccquery = xtc_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = " . (int) $_SESSION['languages_id'] . " AND orders_status_id = " . (int) $orec['orders_status']);

                                    if ($srec = xtc_db_fetch_array($ccquery)) {
                                        $status = ' <a href="' . xtc_href_link(FILENAME_ORDERS, "oID=" . $orec['orders_id'] . "&action=edit") . '">[' . $srec['orders_status_name'] . ']</a>';
                                    } else {
                                        $status = ' [' . TEXT_CURRENT_CUSTOMER . ']';
                                    }
                                }
                            }
                        }
                        if ($skip)
                            continue;    // got a matched cart, skip to next one
                    }
                    $sentInfo = TEXT_NOT_CONTACTED;

                    if ($sentdate != '')
                        $sentInfo = cart_date_short($sentdate);
                    $site = $inrec['site'] == 'confirm' ? TEXT_CONFIRM : ($inrec['site'] == 'payment' ? TEXT_PAYMENT : ($inrec['site'] == 'shipping' ? TEXT_SHIPPING : TEXT_CART));

                    $cline = "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" class=\"dataTable\">
                <tr bgcolor=" . $fcolor . ">
                <td class='dataTableContent' align='center' width='1%'>" . xtc_draw_checkbox_field('custid[]', $curcus, RCS_AUTO_CHECK == 'true' ? $checked : 0) . "</td>
                <td class='dataTableContent' align='left' width='9%' nowrap><b>" . $sentInfo . "</b></td>
                <td class='dataTableContent' align='left' width='15%' nowrap> " . cart_date_short($inrec['bdate']) . "</td>
                <td class='dataTableContent' align='left' width='30%' nowrap><a href='" . xtc_href_link(FILENAME_CUSTOMERS, 'search=' . $inrec['lname'], 'NONSSL') . "'>" . $customer . "</a>" . $status . "</td>
                <td class='dataTableContent' align='left' width='20%' nowrap><a href='" . xtc_href_link('mail.php', 'selected_box=tools&customer=' . $inrec['email']) . "'>" . $inrec['email'] . "</a></td>
                <td class='dataTableContent' align='left' width='10%' nowrap>" . $site . "</td>
                <td class='dataTableContent' align='left' colspan='2' width='15%' nowrap>" . $inrec['phone'] . "</td>
                </tr>";
                }
            }

            // We only have something to do for the product if the quantity selected was not zero!
            if ($inrec['qty'] != 0) {
                // Get the product information (name, price, etc)
                $query3 = xtc_db_query("select  p.products_price price,
                                                          p.products_model model,
                                                          p.products_tax_class_id tax,
                                                          pd.products_name name
                                            from    " . TABLE_PRODUCTS . " p,
                                                          " . TABLE_PRODUCTS_DESCRIPTION . " pd
                                              where   p.products_id = '" . (int) $inrec['pid'] . "'
                                              and     pd.products_id = p.products_id
                                              and     pd.language_id = " . (int) $_SESSION['languages_id']);
                $inrec2 = xtc_db_fetch_array($query3);

                // Check to see if the product is on special, and if so use that pricing
                $sprice = xtc_get_products_special_price($inrec['pid'], $inrec['cid'], ($inrec['qty'] < $quantity[(int) $inrec['pid']] ? $quantity[(int) $inrec['pid']] : $inrec['qty']));
                // BEGIN OF ATTRIBUTE DB CODE
                $prodAttribs = ''; // DO NOT DELETE

                if (RCS_SHOW_ATTRIBUTES == 'true') {
                    $attribquery = xtc_db_query("select  cba.products_id pid,
                                                                     po.products_options_name poname,
                                                                     pov.products_options_values_name povname,
                                                                     pa.options_values_price price
                                                       from    " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " cba,
                                                                     " . TABLE_PRODUCTS_OPTIONS . " po,
                                                                     " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov,
                                                                     " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                                       where   cba.products_id = '" . $inrec['pid'] . "'
                                                       and         cba.customers_id = " . $curcus . "
                                                       and         po.products_options_id = cba.products_options_id
                                                       and         pov.products_options_values_id = cba.products_options_value_id
                                                       and     pa.products_id = " . (int) $inrec['pid'] . "
                                                       and     pa.options_id = cba.products_options_id
                                                       and         pa.options_values_id = cba.products_options_value_id
                                                       and       po.language_id = " . (int) $_SESSION['languages_id'] . "
                                                       and         pov.language_id = " . (int) $_SESSION['languages_id']);
                    $hasAttributes = false;

                    if (xtc_db_num_rows($attribquery)) {
                        $hasAttributes = true;
                        $prodAttribs = '<br>';
                        while ($attribrecs = xtc_db_fetch_array($attribquery)) {
                            $prodAttribs .= '<small><em> - ' . $attribrecs['poname'] . ' ' . $attribrecs['povname'] . '</em></small><br >';
                            $aprice += $attribrecs['price'];
                        }
                    }
                }
                if ($sprice == 0)
                    $sprice = $inrec2['price'];
                $sprice += $aprice;
                if (RCS_SHOW_BRUTTO_PRICE == 'true') {
                    $tax = xtc_get_tax_rate($inrec2['tax']);
                    $sprice = xtc_add_tax($sprice, $tax);
                }

                // END OF ATTRIBUTE DB CODE
                $tprice = $tprice + ($inrec['qty'] * $sprice);
                $pprice_formated = $currencies->format($sprice);
                $tpprice_formated = $currencies->format(($inrec['qty'] * $sprice));
                if ($i % 2 == 0)
                    $f = 'dataTableRow';
                else
                    $f = '';
                $cline .= "<tr class=" . $f . ">
                    <td class='dataTableContent' align='left' vAlign='top' colspan='2' width='10%' nowrap>" . ($inrec['bdate'] < $ndate ? " x" : " &nbsp;") . "</td>
                    <td class='dataTableContent' align='left' vAlign='top' width='15%' nowrap>" . ($inrec2['model'] ? $inrec2['model'] : "&nbsp;") . "</td>
                    <td class='dataTableContent' align='left' vAlign='top' colspan='2' width='55%'><a href='" . xtc_href_link(FILENAME_CATEGORIES, 'action=new_product&pID=' . $inrec['pid'], 'NONSSL') . "'><b>" . $inrec2['name'] . "</b></a>
                    " . $prodAttribs . "
                    </td>
                    <td class='dataTableContent' align='center' vAlign='top' width='5%' nowrap>" . $inrec['qty'] . "x</td>
                    <td class='dataTableContent' align='right'  vAlign='top' width='5%' nowrap>" . $pprice_formated . "</td>
                    <td class='dataTableContent' align='right'  vAlign='top' width='10%' nowrap>" . $tpprice_formated . "</td>
                 </tr>";
            }
            $i++;
        }
        if ($final_line) {
            $totalAll += $tprice;
            $text_total = RCS_SHOW_BRUTTO_PRICE == 'true' ? TABLE_CART_TOTAL_BRUTTO : TABLE_CART_TOTAL;

            $cline .= "<table width='100%' cellpadding='5' cellspacing='5'>
                        <tr>
                          <td class='dataTableContent no_border' align='right' colspan='8'><b>" . $text_total . "</b>" . $currencies->format($tprice) . "</td>
                        </tr>
                        <tr>
                          <td class='no_border' align='right'><a class=\"button\" href=" . xtc_href_link(FILENAME_RECOVER_CART_SALES, "action=delete&customer_id=$curcus&tdate=$tdate") . ">" . BUTTON_DELETE . "</a><a class=\"button\" href=" . xtc_href_link(FILENAME_RECOVER_CART_SALES, "action=complete&customer_id=$curcus&tdate=$tdate") . ">" . BUTTON_COMPLETE . "</a></td>
                        </tr></table>\n";
            if (!$skip) {
                echo $cline;
            }
            $final_line = false;
        }$i++;
    }
// echo '</table>';
    $totalAll_formated = $currencies->format($totalAll);
    $text_total = RCS_SHOW_BRUTTO_PRICE == 'true' ? TABLE_GRAND_TOTAL_BRUTTO : TABLE_GRAND_TOTAL;
    $cline = "<table width='100%'><tr><td class='dataTableContent' align='right'><hr align=right width=200><b>" . $text_total . "</b>" . $totalAll_formated . "</td>
              </tr></table>";
    echo $cline;
    echo "<table width='100%'><tr><td><b>" . PSMSG . "</b><br>" . xtc_draw_textarea_field('message', 'soft', '80', '5')
    ?>
                        <br><input type="submit" name="submit_button" value="<?php echo TEXT_SEND_EMAIL; ?>" class="button" /></td></tr></table>
                </form>
                    <?php
                    }
//
// end footer of both e-mail and report
//
                    ?>
</table>
</td>
</tr>
</table>
                    <?php
                    require(DIR_WS_INCLUDES . 'footer.php');
                    require(DIR_WS_INCLUDES . 'application_bottom.php');

                    