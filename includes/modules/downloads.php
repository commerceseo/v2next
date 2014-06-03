<?php

/* -----------------------------------------------------------------
 * 	$Id: downloads.php 971 2014-04-11 08:37:04Z akausch $
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

if (!function_exists('xtc_date_long')) {
    require_once (DIR_FS_INC . 'xtc_date_long.inc.php');
}

$module_smarty = new Smarty;

if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
    $orders = xtc_db_fetch_array(xtc_db_query("SELECT orders_id, orders_status FROM " . TABLE_ORDERS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' ORDER BY orders_id DESC LIMIT 1;"));
    $last_order = $orders['orders_id'];
    $order_status = $orders['orders_status'];
} else {
    $last_order = (int) $_GET['order_id'];
    $orders = xtc_db_fetch_array(xtc_db_query("SELECT orders_status FROM " . TABLE_ORDERS . " WHERE orders_id = '" . $last_order . "';"));
    $order_status = $orders['orders_status'];
}
if ($order_status < DOWNLOAD_MIN_ORDERS_STATUS) {
    $module_smarty->assign('dl_prevented', 'true');
}
$downloads_query = xtc_db_query("SELECT date_format(o.date_purchased, '%Y-%m-%d') AS date_purchased_day, opd.download_maxdays, op.products_name, opd.orders_products_download_id, opd.orders_products_filename, opd.download_count, opd.download_maxdays FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " opd WHERE o.customers_id = '" . $_SESSION['customer_id'] . "' AND o.orders_id = '" . $last_order . "' AND o.orders_id = op.orders_id AND op.orders_products_id = opd.orders_products_id AND opd.orders_products_filename != ''");
if (xtc_db_num_rows($downloads_query) > 0) {
    $jj = 0;
    while ($downloads = xtc_db_fetch_array($downloads_query)) {
        list ($dt_year, $dt_month, $dt_day) = explode('-', $downloads['date_purchased_day']);
        $download_timestamp = mktime(23, 59, 59, $dt_month, $dt_day + $downloads['download_maxdays'], $dt_year);
        $download_expiry = date('Y-m-d H:i:s', $download_timestamp);
        if (($downloads['download_count'] > 0) && (file_exists(DIR_FS_DOWNLOAD . $downloads['orders_products_filename'])) && (($downloads['download_maxdays'] == 0) || ($download_timestamp > time())) && ($order_status >= DOWNLOAD_MIN_ORDERS_STATUS)) {
            $dl[$jj]['download_link'] = '<a href="' . xtc_href_link(FILENAME_DOWNLOAD, 'order=' . $last_order . '&id=' . $downloads['orders_products_download_id']) . '">' . $downloads['products_name'] . '</a>';
            $dl[$jj]['pic_link'] = xtc_href_link(FILENAME_DOWNLOAD, 'order=' . $last_order . '&id=' . $downloads['orders_products_download_id']);
        } else {
            $dl[$jj]['download_link'] = $downloads['products_name'];
        }
        $dl[$jj]['date'] = xtc_date_long($download_expiry);
        $dl[$jj]['count'] = $downloads['download_count'];
        $jj++;
    }
}
$module_smarty->assign('dl', $dl);
$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$module_smarty->caching = false;
$module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/downloads.html', USE_TEMPLATE_DEVMODE));
$smarty->assign('downloads_content', $module);
