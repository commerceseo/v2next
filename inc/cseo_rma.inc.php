<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_rma.inc.php 1002 2014-05-05 15:14:06Z akausch $
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

function sb_get_reasons($reason_array = '') {
    $reason_array = array();
    $reason_array[] = array('id' => 0, 'text' => PULL_DOWN_DEFAULT);
    $reason_query = xtc_db_query("SELECT rma_reason_id, rma_reason_name FROM " . TABLE_RMA_REASON . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "';");
    while ($reason = xtc_db_fetch_array($reason_query)) {
        $reason_array[] = array(
            'id' => $reason['rma_reason_id'],
            'text' => $reason['rma_reason_name']);
    }
    return $reason_array;
}

function sb_get_reason($reason_id) {
    $reason = xtc_db_fetch_array(xtc_db_query("SELECT rma_reason_name FROM " . TABLE_RMA_REASON . " WHERE rma_reason_id = '" . (int) $reason_id . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "';"));
    return $reason['rma_reason_name'];
}

function sb_get_rma_reasons($name, $selected = '', $parameters = '') {
    $reason_query = xtc_db_query("SELECT rma_reason_id, rma_reason_name FROM " . TABLE_RMA_REASON . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "';");
    $reason_array = array();
    $reason_array[] = array('id' => '0', 'text' => RMA_PRODUCTS_PLEASE_SELECT); // standard
    while ($reason = xtc_db_fetch_array($reason_query)) {
        $reason_array[] = array(
            'id' => $reason['rma_reason_id'],
            'text' => $reason['rma_reason_name']);
    }

    if (is_array($name)) {
        return xtc_draw_pull_down_menuNote($name, $reason_array, $selected, $parameters);
    }
    $reason_pull_down_menu = xtc_draw_pull_down_menu($name, $reason_array);
    return $reason_pull_down_menu;
}

function sb_get_rma_products($order_id, $name, $selected = '', $parameters = '') {
    $products_query = xtc_db_query("SELECT products_id, products_name FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id = '" . (int) $order_id . "';");
    $products_array = array();
    $products_array[] = array('id' => '0', 'text' => RMA_PRODUCTS_PLEASE_SELECT); // standard
    $nr = 1;
    while ($products = xtc_db_fetch_array($products_query)) {
        $products_array[] = array(
            'id' => $products['products_id'],
            'text' => $nr . '. ' . $products['products_name']);
        $nr++;
    }

    if (is_array($name)) {
        return xtc_draw_pull_down_menuNote($name, $products_array, $selected, $parameters);
    }

    $products_pull_down_menu = xtc_draw_pull_down_menu($name, $products_array);
    return $products_pull_down_menu;
}

function sb_get_status($status_id) {
    $status = xtc_db_fetch_array(xtc_db_query("SELECT rma_status_name FROM " . TABLE_RMA_STATUS . " WHERE rma_status_id = '" . (int) $status_id . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "';"));
    return $status['rma_status_name'];
}
