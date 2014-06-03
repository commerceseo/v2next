<?php

/* -----------------------------------------------------------------
 * 	$Id: login_offline.php 521 2013-07-24 11:34:09Z akausch $
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

include ('includes/application_top.php');

if (DOWN_FOR_MAINTENANCE == 'true') {
    // header("HTTP/1.1 503 Service Temporarily Unavailable");
    // header("Status: 503 Service Temporarily Unavailable");
    if (isset($_SESSION['customer_id'])) {
        xtc_redirect(xtc_href_link(FILENAME_DEFAULT, '', 'SSL'));
    }
    $smarty = new Smarty;
    require_once (DIR_FS_INC . 'xtc_validate_password.inc.php');
    require_once (DIR_FS_INC . 'xtc_write_user_info.inc.php');

    // redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled (or the session has not started)
    if ($session_started == false) {
        xtc_redirect(xtc_href_link(FILENAME_COOKIE_USAGE));
    }

    if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
        $email_address = xtc_db_prepare_input($_POST['email_address']);
        $password = xtc_db_prepare_input($_POST['password']);

        $check_customer_query = xtc_db_query("SELECT 
												  customers_id, 
												  customers_vat_id, 
												  customers_firstname,
												  customers_lastname, 
												  customers_gender, 
												  customers_password, 
												  customers_email_address, 
												  customers_default_address_id, 
												  login_tries,
												  login_time
												FROM " .
                TABLE_CUSTOMERS . " 
												WHERE 
												  customers_email_address = '" . xtc_db_input($email_address) . "' 
												AND 
												  account_type = '0'");
        if (!xtc_db_num_rows($check_customer_query)) {
            $_GET['login'] = 'fail';
            $info_message = TEXT_NO_EMAIL_ADDRESS_FOUND;
        } else {
            $check_customer = xtc_db_fetch_array($check_customer_query);
            //Login Safe
            $login_success = true;
            $blocktime = LOGIN_TIME;
            $time = time();
            $logintime = strtotime($check_customer['login_time']);
            $difference = $time - $logintime;
            $login_tries = $check_customer['login_tries'];

            if ($login_tries >= LOGIN_NUM && $difference < $blocktime && ANTISPAM_PASSWORD == 'true') {
                //Antispam beginn
                $antispam_query = xtc_db_fetch_array(xtDBquery("SELECT id, question FROM " . TABLE_CSEO_ANTISPAM . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY rand() LIMIT 1"));
                $smarty->assign('ANTISPAMCODEID', xtc_draw_hidden_field('antispamid', $antispam_query['id']));
                $smarty->assign('ANTISPAMCODEQUESTION', $antispam_query['question']);
                $smarty->assign('INPUT_ANTISPAMCODE', xtc_draw_input_field('codeanwser', '', 'size="6" maxlength="6"', 'text', false));
                $smarty->assign('ANTISPAMCODEACTIVE', ANTISPAM_PASSWORD);
                //Antispam end
            }

            if (!empty($_POST["codeanwser"])) {
                if (!mb_strtolower($antispam_query['answer'], 'UTF-8') == mb_strtolower($_POST["codeanwser"], 'UTF-8')) {
                    xtc_db_query("update " . TABLE_CUSTOMERS . " SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '" . xtc_db_input($email_address) . "'");
                    if (!xtc_validate_password($password, $check_customer['customers_password']) || $check_customer['customers_email_address'] != $email_address) {
                        $info_message = TEXT_LOGIN_ERROR;
                        $login_success = false;
                    }
                } else {
                    $info_message = TEXT_WRONG_CODE;
                    $login_success = false;
                }
            } elseif (!xtc_validate_password($password, $check_customer['customers_password'])) {
                $_GET['login'] = 'fail';
                $info_message = TEXT_LOGIN_ERROR;
                xtc_db_query("update " . TABLE_CUSTOMERS . " SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '" . xtc_db_input($email_address) . "'");
                $login_success = false;
            }

            if ($login_success) {
                xtc_db_query("update " . TABLE_CUSTOMERS . " SET login_tries = 0, login_time = now() WHERE customers_email_address = '" . xtc_db_input($email_address) . "'");
                if (SESSION_RECREATE == 'true') {
                    xtc_session_recreate();
                }

                $check_country_query = xtc_db_query("select entry_country_id, entry_zone_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int) $check_customer['customers_id'] . "' and address_book_id = '" . $check_customer['customers_default_address_id'] . "'");
                $check_country = xtc_db_fetch_array($check_country_query);

                $_SESSION['customer_gender'] = $check_customer['customers_gender'];
                $_SESSION['customer_first_name'] = $check_customer['customers_firstname'];
                $_SESSION['customer_last_name'] = $check_customer['customers_lastname'];
                $_SESSION['customer_id'] = $check_customer['customers_id'];
                $_SESSION['customer_vat_id'] = $check_customer['customers_vat_id'];
                $_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
                $_SESSION['customer_country_id'] = $check_country['entry_country_id'];
                $_SESSION['customer_zone_id'] = $check_country['entry_zone_id'];

                $date_now = date('Ymd');

                xtc_write_user_info((int) $_SESSION['customer_id']);
                $_SESSION['cart']->restore_contents();
                $_SESSION['wishList']->restore_contents();
                xtc_redirect(FILENAME_DEFAULT);
            }
        }
    }

    $content_text = xtc_db_fetch_array(xtc_db_query("SELECT content_heading, content_text FROM " . TABLE_CONTENT_MANAGER . " WHERE content_group = '12' AND languages_id = '" . (int) $_SESSION['languages_id'] . "' "));

    require_once (DIR_WS_INCLUDES . 'header.php');
    $smarty->assign('info_message', $info_message);
    $smarty->assign('HEADER_OFFLINE', $content_text['content_heading']);
    $smarty->assign('CONTENT_OFFLINE', $content_text['content_text']);
    $smarty->assign('BUTTON_LOGIN', xtc_image_submit('button_login.gif', IMAGE_BUTTON_LOGIN));
    $smarty->assign('FORM_ACTION', xtc_draw_form('login_offline', FILENAME_DOWN_FOR_MAINTENANCE_LOGIN . '?action=process'));
    $smarty->assign('INPUT_MAIL', xtc_draw_input_field('email_address'));
    $smarty->assign('INPUT_PASSWORD', xtc_draw_password_field('password'));
    $smarty->assign('FORM_END', '</form>');
    $smarty->assign('language', $_SESSION['language']);
    $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $smarty->loadFilter('output', 'note');
    $smarty->loadFilter('output', 'trimwhitespace');
    $smarty->caching = false;

    $smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/module/login_offline.html', USE_TEMPLATE_DEVMODE));
} else {
    xtc_redirect(xtc_href_link(FILENAME_DEFAULT, '', 'SSL'));
}
include ('includes/application_bottom.php');
