<?php

/* -----------------------------------------------------------------
 * 	$Id: class.create_account.php 816 2014-01-28 07:07:08Z akausch $
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

class login_ORIGINAL {

    function login_ORIGINAL() {
        
    }

    function login_smarty($site) {
        if ($site == 'login') {
            $login_smarty['FORM_LOGIN_ACTION'] = xtc_draw_form('login', xtc_href_link(FILENAME_LOGIN, 'action=process', 'SSL'));
        } else {
            $login_smarty['FORM_LOGIN_ACTION'] = xtc_draw_form('login', xtc_href_link(FILENAME_CHECKOUT, 'action=process', 'SSL'));
        }
        $login_smarty['account_option'] = ACCOUNT_OPTIONS;
        $login_smarty['BUTTON_NEW_ACCOUNT'] = '<a href="' . xtc_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL') . '">' . xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>';
        $login_smarty['BUTTON_LOGIN'] = xtc_image_submit('button_login.gif', IMAGE_BUTTON_LOGIN, 'id="login"');
        $login_smarty['BUTTON_GUEST'] = '<a href="' . xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL') . '">' . xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>';
        $login_smarty['INPUT_MAIL'] = xtc_draw_input_field('email_address', '', 'id="login_email_address"', 'email');
        $login_smarty['INPUT_LOGIN_PASSWORD'] = xtc_draw_password_field('password', '', 'id="login_password"');
        $login_smarty['LINK_LOST_PASSWORD'] = xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'SSL');
        $login_smarty['FORM_LOGIN_END'] = '</form>';

        return $login_smarty;
    }

    function check_login($site) {
        require_once (DIR_FS_INC . 'xtc_validate_password.inc.php');
        require_once (DIR_FS_INC . 'xtc_write_user_info.inc.php');

        if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
            $email_address = xtc_db_prepare_input($_POST['email_address']);
            $password = xtc_db_prepare_input($_POST['password']);
            $check_customer_query = xtc_db_query("SELECT  * FROM " . TABLE_CUSTOMERS . " WHERE customers_email_address = '" . xtc_db_input($email_address) . "' AND account_type = '0';");

            if (!xtc_db_num_rows($check_customer_query)) {
                $_GET['login'] = 'fail';
                $info_message = TEXT_NO_EMAIL_ADDRESS_FOUND;
                xtc_db_query("INSERT INTO 
									intrusions 
									(name , badvalue , page , tags , ip , ip2 , impact , origin , created )
									VALUES 
									('" . xtc_db_prepare_input($_POST['email_address']) . "', '" . xtc_db_prepare_input($_POST['password']) . "', '" . $_SERVER['REQUEST_URI'] . "', 'login', '" . $_SERVER['HTTP_CLIENT_IP'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '1', '', now());");
            } else {
                $check_customer = xtc_db_fetch_array($check_customer_query);
                $login_success = true;

                if (LOGIN_SAFE == 'true') {
                    $blocktime = LOGIN_TIME;
                    $time = time();
                    $logintime = strtotime($check_customer['login_time']);
                    $difference = $time - $logintime;
                    $login_tries = $check_customer['login_tries'];
                    if ($login_tries >= LOGIN_NUM && $difference < $blocktime && ANTISPAM_PASSWORD == 'true') {
                        $antispam_query = xtc_db_fetch_array(xtDBquery("SELECT id, question FROM " . TABLE_CSEO_ANTISPAM . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY rand() LIMIT 1"));
                        $login_smarty['ANTISPAMCODEID'] = xtc_draw_hidden_field('antispamid', $antispam_query['id']);
                        $login_smarty['ANTISPAMCODEQUESTION'] = $antispam_query['question'];
                        $login_smarty['INPUT_ANTISPAMCODE'] = xtc_draw_input_field('codeanwser', '', 'size="6" maxlength="6" autocomplete="off"', 'text', false);
                        $login_smarty['ANTISPAMCODEACTIVE'] = ANTISPAM_PASSWORD;
                    }

                    if (!empty($_POST["codeanwser"])) {
                        if (!mb_strtolower($antispam_query['answer'], 'UTF-8') == mb_strtolower($_POST["codeanwser"], 'UTF-8')) {
                            if (!xtc_validate_password($password, $check_customer['customers_password']) || $check_customer['customers_email_address'] != $email_address) {
                                $info_message = TEXT_LOGIN_ERROR;
                                $login_success = false;
                            }
                        } else {
                            $info_message = TEXT_WRONG_CODE;
                            $login_success = false;
                        }
                    }
                }

                if (!xtc_validate_password($password, $check_customer['customers_password'])) {
                    $_GET['login'] = 'fail';
                    $info_message = TEXT_LOGIN_ERROR;
                    xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '" . xtc_db_input($email_address) . "'");
                    $login_success = false;
                }
                if ($login_success) {
                    if (SESSION_RECREATE == 'true') {
                        xtc_session_recreate();
                    }
                    $check_country_query = xtc_db_query("SELECT entry_country_id, entry_zone_id FROM " . TABLE_ADDRESS_BOOK . " WHERE customers_id = '" . (int) $check_customer['customers_id'] . "' and address_book_id = '" . $check_customer['customers_default_address_id'] . "'");
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
                    xtc_db_query("UPDATE " . TABLE_CUSTOMERS_INFO . " SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '" . (int) $_SESSION['customer_id'] . "'");
                    xtc_write_user_info((int) $_SESSION['customer_id']);
                    // restore cart contents
                    $_SESSION['cart']->restore_contents();
                    $_SESSION['wishList']->restore_contents();
                    if ($_SESSION['customer_id'] == '1' && ADMIN_AFTER_LOGIN == 'true') {
                        xtc_redirect(xtc_href_link('admin/start.php', '', 'SSL'));
                    } elseif ($site == 'checkout') {
                        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT, '', 'SSL'));
                    } elseif ($_SESSION['cart']->count_contents() > 0) {
                        xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
                    } elseif (preg_match("/login/i", $_SERVER['HTTP_REFERER'])) {
                        xtc_redirect(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
                    } else {
                        xtc_redirect($_SERVER['HTTP_REFERER']);
                    }
                }
            }
        }
        $login_smarty['info_message'] = $info_message;

        return $login_smarty;
    }

}
