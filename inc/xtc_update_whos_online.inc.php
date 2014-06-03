<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_update_whos_online.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_update_whos_online() {
    if (isset($_SESSION['customer_id'])) {
        $wo_customer_id = (int) $_SESSION['customer_id'];
        $customer = xtc_db_fetch_array(xtc_db_query("SELECT customers_firstname, customers_lastname FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $wo_customer_id . "';"));
        $wo_full_name = addslashes($customer['customers_firstname'] . ' ' . $customer['customers_lastname']);
    } else {
        $wo_customer_id = '';
        $wo_full_name = 'Gast';
    }

    $wo_session_id = xtc_session_id();
    $wo_ip_address = xtc_db_input($_SERVER['REMOTE_ADDR']);

    if ($aktuelle_datei) {
        $wo_last_page_url = $aktuelle_datei;
    } else {
        $wo_last_page_url = xtc_db_input($_SERVER['REQUEST_URI']);
    }


    //prevent Hackers Scripting for Update whos online table
    $banned_string_pattern = '@GLOBALS|_REQUEST|base64_encode|UNION|%3C|%3E@i';

    $wo_referer = xtc_db_input($_SERVER['HTTP_REFERER']);
    $wo_referer = preg_replace($banned_string_pattern, '', $wo_referer);
    $wo_referer = preg_replace("/[^\s{}a-z,@0-9_\.\:\?\&\=\/\-]/i", "", urldecode($wo_referer));
    $wo_referer = preg_replace('@[-]+@', '-', $wo_referer);

    $wo_last_page_url = preg_replace($banned_string_pattern, '', urldecode($wo_last_page_url));
    $wo_last_page_url = preg_replace("/[^\s{}a-z,@0-9_\.\:\?\&\=\/\-]/i", "", urldecode($wo_last_page_url));
    $wo_last_page_url = preg_replace('@[-]+@', '-', $wo_last_page_url);


    $useragent_referer = xtc_db_input($_SERVER["HTTP_USER_AGENT"]);
    $useragent_lang = xtc_db_input($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    $current_time = time();

    update_admin_stat();

    $stored_customer = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(*) AS count FROM " . TABLE_WHOS_ONLINE . " WHERE session_id = '" . $wo_session_id . "';"));
    if ($stored_customer['count'] > 0) {
        xtc_db_query("UPDATE " . TABLE_WHOS_ONLINE . " SET customer_id = '" . $wo_customer_id . "', full_name = '" . $wo_full_name . "', ip_address = '" . $wo_ip_address . "', time_last_click = '" . $current_time . "', last_page_url = '" . $wo_last_page_url . "', http_referer = '" . $wo_referer . "' , user_agent = '" . $useragent_referer . "' where session_id = '" . $wo_session_id . "';");
    } else {
        xtc_db_query("INSERT INTO " . TABLE_WHOS_ONLINE . " (customer_id, full_name, session_id, ip_address, time_entry, time_last_click, last_page_url, http_referer, user_agent, user_language) VALUES ('" . $wo_customer_id . "', '" . $wo_full_name . "', '" . $wo_session_id . "', '" . $wo_ip_address . "', '" . $current_time . "', '" . $current_time . "', '" . $wo_last_page_url . "', '" . $wo_referer . "', '" . $useragent_referer . "', '" . $useragent_lang . "');");
    }
}

function update_admin_stat() {
    $year = date('Y', time());
    $month = date('m', time());
    $day = date('d', time());

    $current_time = time();
    $xx_mins_ago = ($current_time - 900);
    $expired_quey = xtc_db_query("SELECT customer_id, http_referer, session_id FROM " . TABLE_WHOS_ONLINE . " WHERE time_last_click < '" . $xx_mins_ago . "';");
    while ($expired_info = xtc_db_fetch_array($expired_quey)) {
        // delete the row 
        xtc_db_query("DELETE FROM " . TABLE_WHOS_ONLINE . "  WHERE session_id = '" . $expired_info ['session_id'] . "';");
    }
}
