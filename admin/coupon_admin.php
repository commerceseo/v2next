<?php
/* -----------------------------------------------------------------
 * 	$Id: coupon_admin.php 984 2014-04-16 16:03:06Z akausch $
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
$smarty = new Smarty;
require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.phpmailer.php');
require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
require_once(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

if ($_GET['selected_box']) {
    $_GET['action'] = '';
    $_GET['old_action'] = '';
}

// EMAIL VERSENDEN
if (($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address']) && (!$_POST['back_x'])) {
    if (is_numeric($_POST['customers_email_address'])) {
        $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address, customers_id from " . TABLE_CUSTOMERS . " where customers_status = '" . $_POST['customers_email_address'] . "'");
        $customers_status_query = xtc_db_query("select customers_status_id, customers_status_name from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "' and customers_status_id = '" . $_POST['customers_email_address'] . "'");
        $customers_status = xtc_db_fetch_array($customers_status_query);
        $mail_sent_to = $customers_status['customers_status_name'];
    } else {
        switch ($_POST['customers_email_address']) {
// An alle Kunden versenden
            case '***':
                $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address, customers_id from " . TABLE_CUSTOMERS);
                $mail_sent_to = TEXT_ALL_CUSTOMERS;
                break;
// An Newsletter Abonenten versenden
            case '**D':
                $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address, customers_id from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
                $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
                break;
// An einen einzelnen Kunden versenden
            default:
                $customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);
                $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address, customers_id from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($customers_email_address) . "'");
                $mail_sent_to = $_POST['customers_email_address'];
                break;
        }
    }
// Daten ueber Kupon auslesen
    $coupon_query = xtc_db_query("SELECT * FROM " . TABLE_COUPONS . " WHERE coupon_id = '" . $_GET['cid'] . "'");
    $coupon_result = xtc_db_fetch_array($coupon_query);
    $coupon_name_query = xtc_db_query("SELECT coupon_name FROM " . TABLE_COUPONS_DESCRIPTION . " WHERE coupon_id = '" . (int) $_GET['cid'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "'");
    $coupon_name = xtc_db_fetch_array($coupon_name_query);

    $subject = xtc_db_prepare_input($_POST['subject']);
// Falls kein Betreff angegben wurde, Standard verwenden
    if ($subject == '') {
        $subject = EMAIL_BILLING_SUBJECT;
    }

// Mail versenden
    while ($mail = xtc_db_fetch_array($mail_query)) {
// Sprache des Templates einstellen
        $smarty->assign('language', $_SESSION['language']);
        $smarty->caching = false;
        $smarty->force_compile = true;

// Smartys Ordner
        $smarty->template_dir = DIR_FS_CATALOG . 'templates';
        $smarty->compile_dir = DIR_FS_CATALOG . 'templates_c';
        $smarty->config_dir = DIR_FS_CATALOG . 'lang';
        $smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
        $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

// Smartys fuer Email Text
        $smarty->assign('MESSAGE', $_POST['message']);
        $smarty->assign('NAME', $mail['customers_firstname'] . ' ' . $mail['customers_lastname']);
        $smarty->assign('COUPON_ID', $coupon_result['coupon_code']);
        $smarty->assign('GIFT_ID', $coupon_result['coupon_code']);
        $smarty->assign('COUPON_TYPE', $coupon_result['coupon_type']);
        $link = HTTP_SERVER . DIR_WS_CATALOG . 'gv_redeem.php?gv_no=' . $coupon_result['coupon_code'];
        $smarty->assign('GIFT_LINK', $link);
        $smarty->assign('WEBSITE', HTTP_SERVER . DIR_WS_CATALOG);
        if ($coupon_result['coupon_type'] == 'P') {
            $smarty->assign('COUPON_AMOUNT', round($coupon_result['coupon_amount'], 2) . ' %');
            $smarty->assign('AMMOUNT', round($coupon_result['coupon_amount'], 2) . ' %');
        } else if ($coupon_result['coupon_type'] == 'F') {
            $smarty->assign('COUPON_AMOUNT', $currencies->format($coupon_result['coupon_amount']));
            $smarty->assign('AMMOUNT', $currencies->format($coupon_result['coupon_amount']));
        } else if ($coupon_result['coupon_type'] == 'S') {
            $smarty->assign('COUPON_AMOUNT', TEXT_FREE_SHIPPING);
            $smarty->assign('AMMOUNT', TEXT_FREE_SHIPPING);
        } else if ($coupon_result['coupon_type'] == 'G') {
            $smarty->assign('COUPON_AMOUNT', $currencies->format($coupon_result['coupon_amount']));
            $smarty->assign('AMMOUNT', $currencies->format($coupon_result['coupon_amount']));
        }
        if ($coupon_result['coupon_minimum_order'] != "") {
            $smarty->assign('COUPON_MINIMUM_ORDER', $currencies->format($coupon_result['coupon_minimum_order']));
        }
        if ($coupon_result['restrict_to_products'] != "") {
            $smarty->assign('COUPON_RESTRICT_TO_PRODUCTS', $coupon_result['restrict_to_products']);
        }
        if ($coupon_result['restrict_to_categories'] != "") {
            $smarty->assign('COUPON_RESTRICT_TO_CATEGORIES', $coupon_result['restrict_to_categories']);
        }
        if ($coupon_result['coupon_type'] != 'G') {
            $smarty->assign('COUPON_START_DATE', xtc_date_short($coupon_result['coupon_start_date']));
            $smarty->assign('COUPON_EXPIRE_DATE', xtc_date_short($coupon_result['coupon_expire_date']));
        }

        if ($coupon_result['coupon_type'] == 'G') {
// Template Datei fuer Gutscheine
            require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
            $html_mail = $smarty->fetch('html:send_gift');
            $html_mail .= $signatur_html;
            $txt_mail = $smarty->fetch('txt:send_gift');
            $txt_mail .= $signatur_text;
            require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
            $mail_data = cseo_get_mail_data('send_gift');
            $gv_mail_name = str_replace('{$shop}', STORE_NAME, $mail_data['EMAIL_ADDRESS_NAME']);
            xtc_php_mail(EMAIL_FROM, $gv_mail_name, $mail['customers_email_address'], $mail['customers_firstname'] . ' ' . $mail['customers_lastname'], '', $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], '', '', $subject, $html_mail, $txt_mail);
        } else {
// Template Datei Kupons		
            require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
            $html_mail = $smarty->fetch('html:send_cupon');
            $html_mail .= $signatur_html;
            $txt_mail = $smarty->fetch('txt:send_cupon');
            $txt_mail .= $signatur_text;
            require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
            $mail_data = cseo_get_mail_data('send_cupon');
            $gv_mail_name = str_replace('{$shop}', STORE_NAME, $mail_data['EMAIL_ADDRESS_NAME']);
            xtc_php_mail(EMAIL_FROM, $gv_mail_name, $mail['customers_email_address'], $mail['customers_firstname'] . ' ' . $mail['customers_lastname'], '', $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], '', '', $subject, $html_mail, $txt_mail);
        }

// Email Versand in Liste eintragen
        $insert_query = xtc_db_query("INSERT INTO " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, sent_lastname, emailed_to, date_sent) VALUES ('" . (int) $_GET['cid'] . "', '" . $mail['customers_id'] . "', '" . $mail['customers_firstname'] . "', '" . $mail['customers_lastname'] . "', '" . $mail['customers_email_address'] . "', now() )");
    }
// Zurueck
    xtc_redirect(xtc_href_link(FILENAME_COUPON_ADMIN, 'mail_sent_to=' . urlencode($mail_sent_to)));
}

// FEHLERMELDUNGEN BEI EMAIL VERSAND
if (($_GET['action'] == 'preview_email') && (!$_POST['customers_email_address'])) {
    $_GET['action'] = 'email';
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
}
if ($_GET['mail_sent_to']) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'notice');
}

switch ($_GET['action']) {
    case 'confirmdelete':
// ACTION : EINZELN LOESCHEN
        $del_cid = xtc_db_prepare_input((int) $_GET['cid']);
        xtc_db_query("DELETE FROM " . TABLE_COUPONS . " WHERE coupon_id = '" . xtc_db_input($del_cid) . "';");
        xtc_db_query("DELETE FROM " . TABLE_COUPONS_DESCRIPTION . " WHERE coupon_id = '" . xtc_db_input($del_cid) . "';");
        xtc_redirect(xtc_href_link(FILENAME_COUPON_ADMIN, 'page=' . (int) $_GET['page']));
        break;

    case 'confirmdeleteinactive':
// ACTION : ALLE INAKTIVEN LOESCHEN
        $inactive_query = xtc_db_query("SELECT coupon_id FROM " . TABLE_COUPONS . " WHERE coupon_active = 'N';");
        while ($inactive = xtc_db_fetch_array($inactive_query)) {
            xtc_db_query("DELETE FROM " . TABLE_COUPONS . " WHERE coupon_id = '" . $inactive['coupon_id'] . "';");
            xtc_db_query("DELETE FROM " . TABLE_COUPONS_DESCRIPTION . " WHERE coupon_id = '" . $inactive['coupon_id'] . "';");
        }
        xtc_redirect(xtc_href_link(FILENAME_COUPON_ADMIN, ''));
        break;

    case 'update':
// ACTION : UPDATE
        $languages = xtc_get_languages();
        $_POST['coupon_code'] = trim($_POST['coupon_code']);
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
            $_POST['coupon_name'][$language_id] = trim($_POST['coupon_name'][$language_id]);
            $_POST['coupon_desc'][$language_id] = trim($_POST['coupon_desc'][$language_id]);
        }
        $_POST['coupon_amount'] = trim($_POST['coupon_amount']);
        $update_errors = 0;
        if (!$_POST['coupon_amount'] && $_POST['coupon_type'] != "S") {
            $update_errors = 1;
            $messageStack->add(ERROR_NO_COUPON_AMOUNT, 'error');
        }
        if (!$_POST['coupon_type']) {
            $update_errors = 1;
            $messageStack->add(ERROR_NO_COUPON_TYPE, 'error');
        }
        if (!$_POST['coupon_code']) {
            $coupon_code = create_coupon_code();
        } else if ($_POST['coupon_code']) {
            $coupon_code = $_POST['coupon_code'];
        }
        if ($_POST['coupon_products'] != '' && $_POST['coupon_categories'] != '') {
            $update_errors = 1;
            $messageStack->add(ERROR_DOUBLE_PRODUCTS_CATS, 'error');
        }

// Kupon Code auslesen
        $query1 = xtc_db_query("SELECT coupon_code FROM " . TABLE_COUPONS . " WHERE coupon_code = '" . xtc_db_prepare_input($coupon_code) . "';");

        if (xtc_db_num_rows($query1) && $_POST['coupon_code'] && $_GET['oldaction'] != 'voucheredit') {
            $update_errors = 1;
            $messageStack->add(ERROR_COUPON_EXISTS, 'error');
        }
        if ($update_errors != 0) {
            $_GET['action'] = 'new';
        } else {
            $_GET['action'] = 'update_preview';
        }
        break;

    case 'update_confirm':
// ACTION : CONFIRM UPDATE 
        if (($_POST['back_x']) || ($_POST['back_y'])) {
            $_GET['action'] = "new";
        } else {
            $sql_data_array = array('coupon_code' => xtc_db_prepare_input($_POST['coupon_code']),
                'coupon_amount' => xtc_db_prepare_input($_POST['coupon_amount']),
                'coupon_type' => xtc_db_prepare_input($_POST['coupon_type']),
                'uses_per_coupon' => xtc_db_prepare_input($_POST['coupon_uses_coupon']),
                'uses_per_user' => xtc_db_prepare_input($_POST['coupon_uses_user']),
                'coupon_minimum_order' => xtc_db_prepare_input($_POST['coupon_min_order']),
                'restrict_to_products' => xtc_db_prepare_input($_POST['coupon_products']),
                'restrict_to_categories' => xtc_db_prepare_input($_POST['coupon_categories']),
                'coupon_start_date' => xtc_db_prepare_input($_POST['coupon_startdate']),
                'coupon_expire_date' => xtc_db_prepare_input($_POST['coupon_finishdate']),
                'date_created' => 'now()', 'date_modified' => 'now()');
            $languages = xtc_get_languages();
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                $language_id = $languages[$i]['id'];
                $sql_data_marray[$i] = array('coupon_name' => xtc_db_prepare_input($_POST['coupon_name'][$language_id]), 'coupon_description' => xtc_db_prepare_input($_POST['coupon_desc'][$language_id]));
            }
            if ($_GET['oldaction'] == "voucheredit") {
                xtc_db_perform(TABLE_COUPONS, $sql_data_array, 'update', "coupon_id='" . (int) $_GET['cid'] . "'");
                for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                    $language_id = (int) $languages[$i]['id'];
                    $update = xtc_db_query("update " . TABLE_COUPONS_DESCRIPTION . " set coupon_name = '" . xtc_db_prepare_input($_POST['coupon_name'][$language_id]) . "', coupon_description = '" . xtc_db_prepare_input($_POST['coupon_desc'][$language_id]) . "' where coupon_id = '" . (int) $_GET['cid'] . "' and language_id = '" . $language_id . "'");
                }
            } else {
                $query = xtc_db_perform(TABLE_COUPONS, $sql_data_array);
                $insert_id = xtc_db_insert_id($query);
                for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                    $language_id = $languages[$i]['id'];
                    $sql_data_marray[$i]['coupon_id'] = $insert_id;
                    $sql_data_marray[$i]['language_id'] = $language_id;
                    xtc_db_perform(TABLE_COUPONS_DESCRIPTION, $sql_data_marray[$i]);
                }
            }
        }
        break;
}

require_once (DIR_WS_INCLUDES . 'header.php');
if (!defined('MODULE_ORDER_TOTAL_COUPON_STATUS') || MODULE_ORDER_TOTAL_COUPON_STATUS == 'false') {
echo '<div class="table messageStackWarning">';
echo 'Bitte aktivieren Sie das <a href="modules.php?set=ordertotal&module=ot_coupon">Coupon Modul</a>!';
echo '</div>';
}
if (!defined('MODULE_ORDER_TOTAL_GV_STATUS') || MODULE_ORDER_TOTAL_GV_STATUS == 'false') {
echo '<div class="table messageStackWarning">';
echo 'Bitte aktivieren Sie das <a href="modules.php?set=ordertotal&module=ot_gv">Gutschein Modul</a>!';
echo '</div>';
}
// REPORT ANZEIGEN
switch ($_GET['action']) {
    case 'voucherreport':
        ?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            </tr>
        </table>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr class="dataTableHeadingRow">
                            <th class="dataTableHeadingContent"><?php echo CUSTOMER_ID; ?></th>	
                            <th class="dataTableHeadingContent" align="center"><?php echo CUSTOMER_NAME; ?></th>	
                            <th class="dataTableHeadingContent" align="center"><?php echo IP_ADDRESS; ?></th>	
                            <th class="dataTableHeadingContent" align="center"><?php echo REDEEM_DATE; ?></th>	
                        </tr>
                        <?php
                        $cc_query_raw = "SELECT * FROM " . TABLE_COUPON_REDEEM_TRACK . " WHERE coupon_id = '" . (int) $_GET['cid'] . "'";
                        $cc_split = new splitPageResults($_GET['page'], '20', $cc_query_raw, $cc_query_numrows);
                        $cc_query = xtc_db_query($cc_query_raw);
                        while ($cc_list = xtc_db_fetch_array($cc_query)) {
                            $rows++;
                            if (strlen($rows) < 2) {
                                $rows = '0' . $rows;
                            }
                            if (((!$_GET['uid']) || (@$_GET['uid'] == $cc_list['unique_id'])) && (!$cInfo)) {
                                $cInfo = new objectInfo($cc_list);
                            }
                            if ((is_object($cInfo)) && ($cc_list['unique_id'] == $cInfo->unique_id)) {
                                echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cInfo->coupon_id . '&action=voucherreport&uid=' . $cinfo->unique_id) . '\'">' . "\n";
                            } else {
                                echo '<tr class="' . (($rows % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cc_list['coupon_id'] . '&action=voucherreport&uid=' . $cc_list['unique_id']) . '\'">' . "\n";
                            }
                            $customer_query = xtc_db_query("SELECT customers_firstname, customers_lastname FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $cc_list['customer_id'] . "';");
                            $customer = xtc_db_fetch_array($customer_query);
                            ?>
                            <td class="dataTableContent"><?php echo $cc_list['customer_id']; ?></td>
                            <td class="dataTableContent" align="center"><?php echo $customer['customers_firstname'] . ' ' . $customer['customers_lastname']; ?></td>
                            <td class="dataTableContent" align="center"><?php echo $cc_list['redeem_ip']; ?></td>
                            <td class="dataTableContent" align="center"><?php echo xtc_date_short($cc_list['redeem_date']); ?></td>
                </tr>
            <?php } ?>
        </table>
        </td>
        <?php
// Box rechte Seite
        $heading = array();
        $contents = array();
        $coupon_description_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
        $coupon_desc = xtc_db_fetch_array($coupon_description_query);
        $count_customers = xtc_db_query("SELECT * FROM " . TABLE_COUPON_REDEEM_TRACK . " WHERE coupon_id = '" . $_GET['cid'] . "' AND customer_id = '" . $cInfo->customer_id . "'");
        $heading[] = array('text' => '<b>[' . $_GET['cid'] . ']' . COUPON_NAME . ' ' . $coupon_desc['coupon_name'] . '</b>');
        $contents[] = array('text' => '<b>' . TEXT_REDEMPTIONS . '</b>');
        $contents[] = array('text' => TEXT_REDEMPTIONS_TOTAL . '=' . xtc_db_num_rows($cc_query));
        $contents[] = array('text' => TEXT_REDEMPTIONS_CUSTOMER . '=' . xtc_db_num_rows($count_customers));
        $contents[] = array('text' => '<center><a class="button" href="' . xtc_href_link('coupon_admin.php', 'cid=' . $_GET['cid'] . '&page=' . $_GET['page'], 'NONSSL') . '">' . BUTTON_BACK . '</a>');
        ?>
        <td width="25%" valign="top">
            <?php
            $box = new box;
            echo $box->infoBox($heading, $contents);
            echo '</td>' . "\n";
            echo '</tr>' . "\n";
            echo '</table>' . "\n";

            break;

        case 'preview_email':
// PREVIEW EMAIL ANZEIGEN
            $coupon_result = xtc_db_fetch_array(xtc_db_query("SELECT coupon_code FROM " . TABLE_COUPONS . " WHERE coupon_id = '" . (int) $_GET['cid'] . "';"));
            $coupon_name = xtc_db_fetch_array(xtc_db_query("SELECT coupon_name FROM " . TABLE_COUPONS_DESCRIPTION . " WHERE coupon_id = '" . (int) $_GET['cid'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "';"));

            if (is_numeric($_POST['customers_email_address'])) {
                $customers_status = xtc_db_fetch_array(xtc_db_query("SELECT customers_status_id, customers_status_name FROM " . TABLE_CUSTOMERS_STATUS . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' AND customers_status_id = '" . $_POST['customers_email_address'] . "';"));
                $mail_sent_to = $customers_status['customers_status_name'];
            } else {
                switch ($_POST['customers_email_address']) {
                    case '***':
                        $mail_sent_to = TEXT_ALL_CUSTOMERS;
                        break;
                    case '**D':
                        $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
                        break;
                    default:
                        $mail_sent_to = $_POST['customers_email_address'];
                        break;
                }
            }
            ?>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_EMAIL_TITLE; ?></td>
                                <td class="pageHeading" align="right">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <?php echo xtc_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=send_email_to_user&cid=' . (int) $_GET['cid']); ?>
                    <td>
                        <table border="0" width="100%" cellpadding="0" cellspacing="2">

                            <tr>
                                <td class="smallText">
                                    <b><?php echo TEXT_CUSTOMER; ?></b><br /><?php echo $mail_sent_to; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="smallText">
                                    <b><?php echo TEXT_COUPON; ?></b><br /><?php echo $coupon_name['coupon_name']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="smallText">
                                    <b><?php echo TEXT_SUBJECT; ?></b><br /><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="smallText">
                                    <b><?php echo TEXT_MESSAGE; ?></b><br /><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php
                                    /* Re-Post all POST'ed variables */
                                    reset($_POST);
                                    while (list($key, $value) = each($_POST)) {
                                        if (!is_array($_POST[$key])) {
                                            echo xtc_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
                                        }
                                    }
                                    ?>
                                    <table border="0" width="100%" cellpadding="0" cellspacing="2">
                                        <tr>
                                            <td>
                                                <?php echo '&nbsp;' ?>
                                            </td>
                                            <td align="right">
                                                <?php echo '<a class="button" href="' . xtc_href_link(FILENAME_COUPON_ADMIN) . '">' . BUTTON_CANCEL . '</a> <input type="submit" class="button" value="' . BUTTON_SEND_EMAIL . '"/>'; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

        </form>

        <?php
        break;

    case 'email':
// EMAIL FORMULAR
        $coupon_result = xtc_db_fetch_array(xtc_db_query("SELECT coupon_code FROM " . TABLE_COUPONS . " WHERE coupon_id = '" . (int) $_GET['cid'] . "';"));
        $coupon_name = xtc_db_fetch_array(xtc_db_query("SELECT coupon_name FROM " . TABLE_COUPONS_DESCRIPTION . " WHERE coupon_id = '" . (int) $_GET['cid'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "';"));
        ?>

        <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
                <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pageHeading"><?php echo HEADING_EMAIL_TITLE; ?></td>
                            <td class="pageHeading" align="right">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <?php echo xtc_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=preview_email&cid=' . (int) $_GET['cid']); ?>
                <td>
                    <table border="0" cellpadding="0" cellspacing="2">
                        <?php
                        $customers = array();
                        $customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
                        $customers[] = array('id' => '', 'text' => '------------');
                        $customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
                        $customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
                        $customers[] = array('id' => '', 'text' => '------------');
                        $customers_status_query = xtc_db_query("select customers_status_id, customers_status_name from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "' order by customers_status_id");
                        while ($customers_status = xtc_db_fetch_array($customers_status_query)) {
                            $customers[] = array('id' => $customers_status['customers_status_id'], 'text' => $customers_status['customers_status_name']);
                        }
                        $customers[] = array('id' => '', 'text' => '------------');
                        $mail_query = xtc_db_query("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
                        while ($customers_values = xtc_db_fetch_array($mail_query)) {
                            $customers[] = array('id' => $customers_values['customers_email_address'], 'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
                        }
                        ?>
                        <tr>
                            <td class="main"><?php echo '<b>' . TEXT_COUPON . '</b>'; ?></td>
                            <td><?php echo $coupon_name['coupon_name']; ?></td>
                        </tr>
                        <tr>
                            <td class="main"><?php echo '<b>' . TEXT_CUSTOMER . '</b>'; ?></td>
                            <td><?php echo xtc_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']); ?></td>
                        </tr>
                        <tr>
                            <td class="main"><?php echo '<b>' . TEXT_SUBJECT . '</b>'; ?></td>
                            <td><?php echo xtc_draw_input_field('subject'); ?></td>
                        </tr>
                        <tr>
                            <td valign="top" class="main"><?php echo '<b>' . TEXT_MESSAGE . '</b>'; ?></td>
                            <td><?php echo xtc_draw_textarea_field('message', 'soft', '60', '15'); ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right">
                                <?php echo '<input type="submit" class="button" value="' . BUTTON_SEND_EMAIL . '"/>'; ?>
                            </td>
                        </tr>
                    </table>
                </td>
                </form>
            </tr>
        </table>

        <?php
        break;
//////////////////////////////////////////////////////////////////////////////////////////////
// PREVIEW ANZEIGEN
//////////////////////////////////////////////////////////////////////////////////////////////
    case 'update_preview':
        ?>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
                <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pageHeading"><?php echo TEXT_HEADING_NEW_COUPON; ?></td>
                            <td class="pageHeading" align="right">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    if ($_POST['coupon_code']) {
                        $c_code = $_POST['coupon_code'];
                    } else {
                        $c_code = $coupon_code;
                    }
                    ?>
                    <?php echo xtc_draw_form('coupon', 'coupon_admin.php', 'action=update_confirm&oldaction=' . $_GET['oldaction'] . '&cid=' . (int) $_GET['cid']); ?>
                    <table border="0" align="center"  width="70%" cellspacing="0" cellpadding="6">
                        <?php
                        $languages = xtc_get_languages();
                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                            $language_id = $languages[$i]['id'];
                            ?>
                            <tr>
                                <td align="left" class="main" width="180px">
                                    <b><?php echo COUPON_NAME . '&nbsp;&nbsp;' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']); ?> </b>
                                </td>
                                <td align="left" class="main" >
                                    <?php
                                    if (!$_POST['coupon_name'][$language_id]) {
                                        $_POST['coupon_name'][$language_id] = $coupon_code;
                                    }
                                    echo $_POST['coupon_name'][$language_id];
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        $languages = xtc_get_languages();
                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                            $language_id = $languages[$i]['id'];
                            ?>
                            <tr>
                                <td align="left" class="main" >
                                    <b><?php echo COUPON_DESC . '&nbsp;&nbsp;' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']); ?></b>
                                </td>
                                <td align="left" class="main" >
                                    <?php echo $_POST['coupon_desc'][$language_id]; ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td align="left" class="main">
                                <b><?php echo COUPON_TYPE; ?></b>
                            </td>
                            <?php if ($_POST['coupon_type'] == "S") { ?>
                                <td align="left" class="main">
                                    <?php echo TEXT_FREE_SHIPPING; ?>
                                </td>
                            <?php } else if ($_POST['coupon_type'] == "F") { ?>
                                <td align="left" class="main">
                                    <?php echo TYPE_F; ?>
                                </td>
                            <?php } else if ($_POST['coupon_type'] == "P") { ?>
                                <td align="left" class="main">
                                    <?php echo TYPE_P; ?>
                                </td>				
                            <?php } else if ($_POST['coupon_type'] == "G") { ?>
                                <td align="left" class="main">
                                    <?php echo TYPE_G; ?>
                                </td>								
                            <?php } ?>
                        </tr>						
                        <?php if ($_POST['coupon_type'] != "S") { ?>
                            <tr>
                                <td align="left" class="main" >
                                    <b><?php echo COUPON_AMOUNT; ?></b>
                                </td>
                                <td align="left" class="main" >
                                    <?php
                                    if ($_POST['coupon_type'] != "P") {
                                        echo $currencies->format($_POST['coupon_amount']);
                                    } else {
                                        echo $_POST['coupon_amount'] . ' %';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>							
                        <tr>
                            <td align="left" class="main" >
                                <b><?php echo COUPON_CODE; ?></b>
                            </td>
                            <td align="left" class="main" class="main">
                                <?php echo $coupon_code; ?>
                            </td>
                        </tr>
                        <?php if ($_POST['coupon_type'] != "G") { ?>
                            <tr>
                                <td align="left" class="main">
                                    <b><?php echo COUPON_MIN_ORDER; ?></b>
                                </td>
                                <td align="left" class="main">
                                    <?php echo $_POST['coupon_min_order']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" class="main">
                                    <b><?php echo COUPON_USES_COUPON; ?></b>
                                </td>
                                <td align="left" class="main">
                                    <?php echo $_POST['coupon_uses_coupon']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" class="main">
                                    <b><?php echo COUPON_USES_USER; ?></b>
                                </td>
                                <td align="left" class="main">
                                    <?php echo $_POST['coupon_uses_user']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" class="main">
                                    <b><?php echo COUPON_PRODUCTS; ?></b>
                                </td>
                                <td align="left" class="main">
                                    <?php echo $_POST['coupon_products']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" class="main">
                                    <b><?php echo COUPON_CATEGORIES; ?></b>
                                </td>
                                <td align="left" class="main">
                                    <?php echo $_POST['coupon_categories']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" class="main" >
                                    <b><?php echo COUPON_STARTDATE; ?></b>
                                </td>
                                <?php $start_date = date(DATE_FORMAT, mktime(0, 0, 0, $_POST['coupon_startdate_month'], $_POST['coupon_startdate_day'], $_POST['coupon_startdate_year'])); ?>
                                <td align="left" class="main" >
                                    <?php echo $start_date; ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" class="main" ><b><?php echo COUPON_FINISHDATE; ?></b></td>
                                <?php
                                $finish_date = date(DATE_FORMAT, mktime(0, 0, 0, $_POST['coupon_finishdate_month'], $_POST['coupon_finishdate_day'], $_POST['coupon_finishdate_year']));
                                ?>
                                <td align="left" class="main" ><?php echo $finish_date; ?></td>
                            </tr>
                        <?php } ?>													
                        <?php
                        $languages = xtc_get_languages();
                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                            $language_id = $languages[$i]['id'];
                            echo xtc_draw_hidden_field('coupon_name[' . $languages[$i]['id'] . ']', $_POST['coupon_name'][$language_id]);
                            echo xtc_draw_hidden_field('coupon_desc[' . $languages[$i]['id'] . ']', $_POST['coupon_desc'][$language_id]);
                        }
                        echo xtc_draw_hidden_field('coupon_type', $_POST['coupon_type']);
                        echo xtc_draw_hidden_field('coupon_amount', $_POST['coupon_amount']);
                        echo xtc_draw_hidden_field('coupon_code', $c_code);
                        echo xtc_draw_hidden_field('coupon_min_order', $_POST['coupon_min_order']);
                        echo xtc_draw_hidden_field('coupon_uses_coupon', $_POST['coupon_uses_coupon']);
                        echo xtc_draw_hidden_field('coupon_uses_user', $_POST['coupon_uses_user']);
                        echo xtc_draw_hidden_field('coupon_products', $_POST['coupon_products']);
                        echo xtc_draw_hidden_field('coupon_categories', $_POST['coupon_categories']);
                        echo xtc_draw_hidden_field('coupon_startdate', date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_startdate_month'], $_POST['coupon_startdate_day'], $_POST['coupon_startdate_year'])));
                        echo xtc_draw_hidden_field('coupon_finishdate', date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_finishdate_month'], $_POST['coupon_finishdate_day'], $_POST['coupon_finishdate_year'])));
                        ?>
                        <tr>
                            <td align="left">
                                <?php echo '<a class="button" href="' . xtc_href_link('coupon_admin.php', 'action=' . $_GET['oldaction'] . '&cid=' . $_GET['cid'] . '&page=' . $_GET['page'], 'NONSSL') . '">' . BUTTON_BACK . '</a>'; ?></a>
                            </td>
                            <td align="left">
                                <?php echo '<input type="submit" class="button" value="' . BUTTON_CONFIRM . '"/>'; ?>
                            </td>
                        </tr>
                    </table>
                    </form>
                </td>
            </tr>
        </table>
        <?php
        break;



    case 'voucheredit':
// KUPON BEARBEITEN
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
            $coupon_query = xtc_db_query("SELECT coupon_name, coupon_description FROM " . TABLE_COUPONS_DESCRIPTION . " WHERE coupon_id = '" . (int) $_GET['cid'] . "' AND language_id = '" . (int) $language_id . "';");
            $coupon = xtc_db_fetch_array($coupon_query);
            $coupon_name[$language_id] = $coupon['coupon_name'];
            $coupon_desc[$language_id] = $coupon['coupon_description'];
        }
        $coupon_query = xtc_db_query("SELECT * FROM " . TABLE_COUPONS . " WHERE coupon_id = '" . (int) $_GET['cid'] . "';");
        $coupon = xtc_db_fetch_array($coupon_query);
        $coupon_code = $coupon['coupon_code'];
        $coupon_amount = $coupon['coupon_amount'];
        $coupon_type = $coupon['coupon_type'];
        $coupon_min_order = $coupon['coupon_minimum_order'];
        $coupon_startdate = split("[-]", $coupon['coupon_start_date']);
        $coupon_finishdate = split("[-]", $coupon['coupon_expire_date']);
        $coupon_uses_coupon = $coupon['uses_per_coupon'];
        $coupon_uses_user = $coupon['uses_per_user'];
        $coupon_products = $coupon['restrict_to_products'];
        $coupon_categories = $coupon['restrict_to_categories'];

    case 'new':
// NEUEN KUPON ERSTELLEN
        if ($_GET['action'] != "voucheredit") {
            $languages = xtc_get_languages();
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                $language_id = $languages[$i]['id'];
                $coupon_name[$language_id] = $_POST['coupon_name'][$language_id];
                $coupon_desc[$language_id] = $_POST['coupon_desc'][$language_id];
            }
            $coupon_amount = $_POST['coupon_amount'];
            $coupon_type = $_POST['coupon_type'];
            $coupon_min_order = $_POST['coupon_min_order'];
            $coupon_code = $_POST['coupon_code'];
            $coupon_uses_coupon = $_POST['coupon_uses_coupon'];
            $coupon_uses_user = $_POST['coupon_uses_user'];
            $coupon_products = $_POST['coupon_products'];
            $coupon_categories = $_POST['coupon_categories'];

            if (!$_POST['coupon_startdate_year']) {
                $coupon_startdate = split("[-]", date('Y-m-d'));
            } else {
                $coupon_startdate = array();
                $coupon_startdate[0] = $_POST['coupon_startdate_year'];
                $coupon_startdate[2] = $_POST['coupon_startdate_day'];
                $coupon_startdate[1] = $_POST['coupon_startdate_month'];
            }
            if (!$_POST['coupon_finishdate_year']) {
                $coupon_finishdate = split("[-]", date('Y-m-d'));
                $coupon_finishdate[0] = $coupon_finishdate[0] + 1;
            } else {
                $coupon_finishdate = array();
                $coupon_finishdate[0] = $_POST['coupon_finishdate_year'];
                $coupon_finishdate[2] = $_POST['coupon_finishdate_day'];
                $coupon_finishdate[1] = $_POST['coupon_finishdate_month'];
            }
        }
        ?>

        <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="pageHeading"><?php echo TEXT_HEADING_NEW_COUPON; ?></td>
            </tr>
        </table>
        <?php
        if ($_GET['oldaction'] != '') {
            echo xtc_draw_form('coupon', 'coupon_admin.php', 'action=update&oldaction=' . $_GET['oldaction'] . '&cid=' . (int) $_GET['cid'], 'post', 'enctype="multipart/form-data"');
        } else if ($_GET['action'] != '') {
            echo xtc_draw_form('coupon', 'coupon_admin.php', 'action=update&oldaction=' . $_GET['action'] . '&cid=' . (int) $_GET['cid'], 'post', 'enctype="multipart/form-data"');
        }
        ?>
        <table border="0" width="100%" align="center" cellspacing="0" cellpadding="2">
            <?php
            $languages = xtc_get_languages();
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                $language_id = $languages[$i]['id'];
                ?>
                <tr>
                    <td align="left" class="main" width="200px">
                        <b><?php echo COUPON_NAME . '&nbsp;&nbsp;' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) ?></b>
                    </td>
                    <td align="left" class="main" width="220px">
                        <?php echo xtc_draw_input_field('coupon_name[' . $languages[$i]['id'] . ']', $coupon_name[$language_id]); ?>
                    </td>
                    <td align="left" class="main">
                        <?php if ($i == 0) echo COUPON_NAME_HELP; ?>
                    </td>
                </tr>
                <?php
            }
            $languages = xtc_get_languages();
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                $language_id = $languages[$i]['id'];
                ?>
                <tr>
                    <td align="left" valign="top" class="main">
                        <b><?php echo COUPON_DESC . '&nbsp;&nbsp;' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) ?></b>
                    </td>
                    <td align="left" class="main" valign="top">
                        <?php echo xtc_draw_textarea_field('coupon_desc[' . $languages[$i]['id'] . ']', 'physical', '24', '3', $coupon_desc[$language_id]) ?>
                    </td>
                    <td align="left" valign="top" class="main">
                        <?php
                        if ($i == 0) {
                            echo COUPON_DESC_HELP;
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td align="left" class="main">
                    <b><?php echo COUPON_TYPE; ?></b>
                </td>
                <td align="left" class="main">
                    <?php
                    if ($coupon_type == "G") {
                        echo '<input type="radio" name="coupon_type" value="G" checked="checked" />' . TYPE_G;
                    } else {
                        echo '<input type="radio" name="coupon_type" value="G" />' . TYPE_G;
                    }
                    ?>
                </td>
                <td align="left" class="main">
                    <?php echo TYPE_G_HELP; ?>
                </td>
            </tr>
            <tr>
                <td align="left" class="main">&nbsp;</td>
                <td align="left" class="main">
                    <?php
                    if ($coupon_type == "F") {
                        echo '<input type="radio" name="coupon_type" value="F" checked="checked" />' . TYPE_F;
                    } else {
                        echo '<input type="radio" name="coupon_type" value="F" />' . TYPE_F;
                    }
                    ?>								
                </td>
                <td align="left" class="main">
                    <?php echo TYPE_F_HELP; ?>
                </td>
            </tr>
            <tr>
                <td align="left" class="main">&nbsp;</td>
                <td align="left" class="main">
                    <?php
                    if ($coupon_type == "P") {
                        echo '<input type="radio" name="coupon_type" value="P" checked="checked" />' . TYPE_P;
                    } else {
                        echo '<input type="radio" name="coupon_type" value="P" />' . TYPE_P;
                    }
                    ?>								
                </td>
                <td align="left" class="main">
                    <?php echo TYPE_P_HELP; ?>
                </td>
            </tr> 
            <tr>
                <td align="left" class="main">&nbsp;</td>
                <td align="left" class="main">
                    <?php
                    if ($coupon_type == "S") {
                        echo '<input type="radio" name="coupon_type" value="S" checked="checked" />' . TYPE_S;
                    } else {
                        echo '<input type="radio" name="coupon_type" value="S" />' . TYPE_S;
                    }
                    ?>																
                </td>
                <td align="left" class="main"><?php echo TYPE_S_HELP; ?></td>
            </tr> 
            <tr id="amount">
                <td align="left" class="main"><b><?php echo COUPON_AMOUNT; ?></b></td>
                <td align="left" class="main"><?php echo xtc_draw_input_field('coupon_amount', $coupon_amount); ?></td>
                <td align="left" class="main"><?php echo COUPON_AMOUNT_HELP; ?></td>
            </tr>
            <tr>
                <td align="left" class="main"><b><?php echo COUPON_CODE; ?></b></td>
                <td align="left" class="main"><?php echo xtc_draw_input_field('coupon_code', $coupon_code); ?></td>
                <td align="left" class="main"><?php echo COUPON_CODE_HELP; ?></td>
            </tr>
            <tr id="coupon1">
                <td align="left" class="main"><b><?php echo COUPON_MIN_ORDER; ?></b></td>
                <td align="left" class="main"><?php echo xtc_draw_input_field('coupon_min_order', $coupon_min_order); ?></td>
                <td align="left" class="main"><?php echo COUPON_MIN_ORDER_HELP; ?></td>
            </tr>
            <tr id="coupon2">
                <td align="left" class="main"><b><?php echo COUPON_USES_COUPON; ?></b></td>
                <td align="left" class="main"><?php echo xtc_draw_input_field('coupon_uses_coupon', $coupon_uses_coupon); ?></td>
                <td align="left" class="main"><?php echo COUPON_USES_COUPON_HELP; ?></td>
            </tr>
            <tr id="coupon3">
                <td align="left" class="main"><b><?php echo COUPON_USES_USER; ?></b></td>
                <td align="left" class="main"><?php echo xtc_draw_input_field('coupon_uses_user', $coupon_uses_user); ?></td>
                <td align="left" class="main"><?php echo COUPON_USES_USER_HELP; ?></td>
            </tr>

            <tr>
                <td align="left" class="main"><?php echo COUPON_PRODUCTS; ?></td>
                <td align="left" class="main"><?php echo xtc_draw_input_field('coupon_products', $coupon_products, 'style="width: 150px"'); ?> <a href="<?php echo xtc_href_link('validproducts.php', '', 'NONSSL'); ?>" target="_blank" onclick="window.open('validproducts.php', 'Valid_Products', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600');
                        return false"><?php echo TEXT_VIEW_SHORT; ?></a></td>
                <td align="left" class="main"><?php echo COUPON_PRODUCTS_HELP; ?></td>
            </tr>
            <tr>
                <td align="left" class="main"><?php echo COUPON_CATEGORIES; ?></td>
                <td align="left" class="main"><?php echo xtc_draw_input_field('coupon_categories', $coupon_categories, 'style="width: 150px"'); ?> <a href="<?php echo xtc_href_link('validcategories.php', '', 'NONSSL'); ?>" target="_blank" onclick="window.open('validcategories.php', 'Valid_Categories', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600');
                        return false"><?php echo TEXT_VIEW_SHORT; ?></a></td>
                <td align="left" class="main"><?php echo COUPON_CATEGORIES_HELP; ?></td>
            </tr>

            <tr id="coupon6">
                <td align="left" class="main"><b><?php echo COUPON_STARTDATE; ?></b></td>
                <td align="left" class="main"><?php echo xtc_draw_date_selector('coupon_startdate', mktime(0, 0, 0, $coupon_startdate[1], $coupon_startdate[2], $coupon_startdate[0], 0)); ?></td>
                <td align="left" class="main"><?php echo COUPON_STARTDATE_HELP; ?></td>
            </tr>

            <tr id="coupon7">
                <td align="left" class="main"><b><?php echo COUPON_FINISHDATE; ?></b></td>
                <td align="left" class="main"><?php echo xtc_draw_date_selector('coupon_finishdate', mktime(0, 0, 0, $coupon_finishdate[1], $coupon_finishdate[2], $coupon_finishdate[0], 0)); ?></td>
                <td align="left" class="main"><?php echo COUPON_FINISHDATE_HELP; ?></td>
            </tr>
            <tr>
                <td align="left">
        <?php echo '<a class="button" href="' . xtc_href_link('coupon_admin.php', '') . '">' . BUTTON_CANCEL . '</a>'; ?>
                </td>
                <td>&nbsp;</td>
                <td align="left">
        <?php echo '<input type="submit" class="button" value="' . BUTTON_PREVIEW . '"/>'; ?>
                </td>
            </tr>
        </table>
        </form>
        <?php
        break;

    default:
// TABELLE ANZEIGEN
        ?>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
                <td width="100%">
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                        </tr>
                    </table>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="main">
                                <?php
                                echo xtc_draw_form('status', FILENAME_COUPON_ADMIN, '', 'get');
                                $status_array[] = array('id' => 'Y', 'text' => TEXT_COUPON_ACTIVE);
                                $status_array[] = array('id' => 'N', 'text' => TEXT_COUPON_INACTIVE);
                                $status_array[] = array('id' => '*', 'text' => TEXT_COUPON_ALL);
                                if ($_GET['status']) {
                                    $status = xtc_db_prepare_input($_GET['status']);
                                } else {
                                    $status = 'Y';
                                }
                                echo TEXT_STATUS . ' ' . xtc_draw_pull_down_menu('status', $status_array, $status, 'onChange="this.form.submit();"');
                                if ($_GET['type']) {
                                    echo xtc_draw_hidden_field('type', $_GET['type']);
                                }
                                if ($_GET['search']) {
                                    echo xtc_draw_hidden_field('search', $_GET['search']);
                                }
                                if ($_GET['page']) {
                                    echo xtc_draw_hidden_field('page', $_GET['page']);
                                }
                                if ($_GET['paging']) {
                                    echo xtc_draw_hidden_field('paging', $_GET['paging']);
                                }
                                if ($_GET['sorting']) {
                                    echo xtc_draw_hidden_field('sorting', $_GET['sorting']);
                                }
                                ?>
                                </form>
                            </td>
                            <td class="main">
                                <?php
                                echo xtc_draw_form('type', FILENAME_COUPON_ADMIN, '', 'get');
                                $type_array[] = array('id' => 'g', 'text' => 'Gutscheine');
                                $type_array[] = array('id' => 'c', 'text' => 'Kupons');
                                $type_array[] = array('id' => '*', 'text' => 'Alle');
                                if ($_GET['type']) {
                                    $type = xtc_db_prepare_input($_GET['type']);
                                } else {
                                    $type = '*';
                                }
                                echo TEXT_TYPE . ' ' . xtc_draw_pull_down_menu('type', $type_array, $type, 'onChange="this.form.submit();"');
                                if ($_GET['status']) {
                                    echo xtc_draw_hidden_field('status', $_GET['status']);
                                }
                                if ($_GET['search']) {
                                    echo xtc_draw_hidden_field('search', $_GET['search']);
                                }
                                if ($_GET['page']) {
                                    echo xtc_draw_hidden_field('page', $_GET['page']);
                                }
                                if ($_GET['paging']) {
                                    echo xtc_draw_hidden_field('paging', $_GET['paging']);
                                }
                                if ($_GET['sorting']) {
                                    echo xtc_draw_hidden_field('sorting', $_GET['sorting']);
                                }
                                ?>
                                </form>
                            </td>	
                            <td class="main">
                                <?php
// how many products on the page
                                echo xtc_draw_form('formpaging', FILENAME_COUPON_ADMIN, '', 'get');
                                echo TEXT_SHOWN_NUMBER . ' ' . xtc_draw_pull_down_menu('paging', $pages_array, $_GET['paging'], 'onChange="this.form.submit();"');
                                if ($_GET['status']) {
                                    echo xtc_draw_hidden_field('status', $_GET['status']);
                                }
                                if ($_GET['search']) {
                                    echo xtc_draw_hidden_field('search', $_GET['search']);
                                }
                                if ($_GET['sorting']) {
                                    echo xtc_draw_hidden_field('sorting', $_GET['sorting']);
                                }
                                if ($_GET['type']) {
                                    echo xtc_draw_hidden_field('type', $_GET['type']);
                                }
                                ?>
                                </form>
                            </td>
                            <td class="main" align="right">
                                <?php
// product search field
                                echo xtc_draw_form('search', FILENAME_COUPON_ADMIN, '', 'get');
                                echo TEXT_SEARCH . ' ' . xtc_draw_input_field('search', $_GET['search']);
                                if ($_GET['type']) {
                                    echo xtc_draw_hidden_field('type', $_GET['type']);
                                }
                                if ($_GET['status']) {
                                    echo xtc_draw_hidden_field('status', $_GET['status']);
                                }
                                if ($_GET['page']) {
                                    echo xtc_draw_hidden_field('page', $_GET['page']);
                                }
                                if ($_GET['paging']) {
                                    echo xtc_draw_hidden_field('paging', $_GET['paging']);
                                }
                                if ($_GET['sorting']) {
                                    echo xtc_draw_hidden_field('sorting', $_GET['sorting']);
                                }
                                ?>
                                </form>
                            </td>   	   
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td><?php echo '<a class="button" href="' . xtc_href_link('coupon_admin.php', '&amp;action=new') . '">' . BUTTON_INSERT . '</a>'; ?>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td valign="top">
                                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                    <tr class="dataTableHeadingRow">
                                        <td class="dataTableHeadingContent"><?php echo COUPON_NAME . xtc_sorting(FILENAME_COUPON_ADMIN, 'name'); ?></td>
                                        <td class="dataTableHeadingContent" align="center"><?php echo COUPON_AMOUNT . xtc_sorting(FILENAME_COUPON_ADMIN, 'coupon_amount'); ?></td>	
                                        <td class="dataTableHeadingContent" align="center"><?php echo COUPON_CODE . xtc_sorting(FILENAME_COUPON_ADMIN, 'code'); ?></td>	
                                        <td class="dataTableHeadingContent" align="center"><?php echo DATE_CREATED . xtc_sorting(FILENAME_COUPON_ADMIN, 'date-created'); ?></td>	
                                        <td class="dataTableHeadingContent" align="center"><?php echo STATUS . xtc_sorting(FILENAME_COUPON_ADMIN, 'status'); ?></td>					
                                        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?></td>
                                    </tr>
                                    <?php
// GET STATUS
                                    if (xtc_db_input($status) == "Y") {
                                        $where_status = " and c.coupon_active = 'Y'";
                                    } else if (xtc_db_input($status) == "N") {
                                        $where_status = " and c.coupon_active = 'N'";
                                    } else if (xtc_db_input($status) == "*") {
                                        $where_status = " and (c.coupon_active = 'Y' or c.coupon_active = 'N')";
                                    }
// GET SEARCH
                                    if ($_GET['search'] != "") {
                                        $where_search = " and (cd.coupon_name like '%" . $_GET['search'] . "%' or c.coupon_amount like '%" . $_GET['search'] . "%' or c.coupon_code like '%" . $_GET['search'] . "%')";
                                    } else {
                                        $where_search = "";
                                    }
// GET TYPE
                                    if ($_GET['type'] == "g") {
                                        $where_type = " and c.coupon_type = 'G'";
                                    } else if ($_GET['type'] == "c") {
                                        $where_type = " and (c.coupon_type = 'F' or c.coupon_type = 'P' or c.coupon_type = 'S')";
                                    } else {
                                        $where_type = "";
                                    }
// get sorting option and switch accordingly        
                                    if ($_GET['sorting']) {
                                        switch ($_GET['sorting']) {
                                            case 'amount':
                                                $sort = ' order by c.coupon_amount ASC';
                                                break;
                                            case 'amount-desc':
                                                $sort = ' order by c.coupon_amount DESC';
                                                break;
                                            case 'code':
                                                $sort = ' order by c.coupon_code ASC';
                                                break;
                                            case 'code-desc':
                                                $sort = ' order by c.coupon_code DESC';
                                                break;
                                            case 'date-created':
                                                $sort = ' order by c.date_created ASC';
                                                break;
                                            case 'date-created-desc':
                                                $sort = ' order by c.date_created DESC';
                                                break;
                                            case 'status':
                                                $sort = ' order by c.coupon_active ASC';
                                                break;
                                            case 'status-desc':
                                                $sort = ' order by c.coupon_active DESC';
                                                break;
                                            case 'name':
                                                $sort = ' order by cd.coupon_name ASC';
                                                break;
                                            case 'name-desc':
                                                $sort = ' order by cd.coupon_name DESC';
                                                break;
                                            default:
                                                $sort = ' order by cd.coupon_name ASC';
                                                break;
                                        }
                                    } else {
                                        $sort = ' order by cd.coupon_name ASC';
                                    }
// END - get sorting option and switch accordingly      			
// Set the number of shown products
                                    if ($_GET['paging']) {
                                        $showpage = $_GET['paging'];
                                    } else {
                                        $showpage = SHOW_PAGE_STANDARD;
                                    }
// ENDE - Set the number of shown products	
                                    if ($_GET['page'] > 1) {
                                        $rows = $_GET['page'] * $showpage - $showpage;
                                    }
                                    $cc_query_raw = "SELECT c.*, cd.coupon_name from " . TABLE_COUPONS . " c," . TABLE_COUPONS_DESCRIPTION . " cd WHERE cd.coupon_id = c.coupon_id AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "'" . $where_status . $where_search . $where_type . $sort;
                                    $cc_split = new splitPageResults($_GET['page'], $showpage, $cc_query_raw, $cc_query_numrows);
                                    $cc_query = xtc_db_query($cc_query_raw);
                                    while ($cc_list = xtc_db_fetch_array($cc_query)) {
                                        $rows++;
                                        if (strlen($rows) < 2) {
                                            $rows = '0' . $rows;
                                        }
                                        if (((!$_GET['cid']) || (@$_GET['cid'] == $cc_list['coupon_id'])) && (!$cInfo)) {
                                            $cInfo = new objectInfo($cc_list);
                                        }
                                        if ((is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id)) {
                                            echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action')) . 'cid=' . $cInfo->coupon_id . '&action=edit') . '\'">' . "\n";
                                        } else {
                                            echo '<tr class="' . (($rows % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action')) . 'cid=' . $cc_list['coupon_id']) . '\'">' . "\n";
                                        }
                                        ?>
                                        <td class="dataTableContent"><?php echo $cc_list['coupon_name']; ?></td>
                                        <td class="dataTableContent" align="center">
                                            <?php
                                            if ($cc_list['coupon_type'] == 'P') {
                                                echo $cc_list['coupon_amount'] . '%';
                                            } else if ($cc_list['coupon_type'] == 'S') {
                                                echo TEXT_FREE_SHIPPING;
                                            } else {
                                                echo $currencies->format($cc_list['coupon_amount']);
                                            }
                                            ?>
                                        </td>
                                        <td class="dataTableContent" align="center"><?php echo $cc_list['coupon_code']; ?></td>
                                        <td class="dataTableContent" align="center"><?php echo $cc_list['date_created']; ?></td>
                                        <td class="dataTableContent" align="center">
                                            <?php
                                            if ($cc_list['coupon_active'] == "Y") {
                                                echo STATUS_ACTIVE;
                                            } else if ($cc_list['coupon_active'] == "N") {
                                                echo STATUS_INACTIVE;
                                            }
                                            ?>
                                        </td>
                                        <td class="dataTableContent" align="right">
                                            <?php
                                            if ((is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id)) {
                                                echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
                                            } else {
                                                $parameters = '';
                                                if ($_GET['status']) {
                                                    $parameters .= '&amp;status=' . $_GET['status'];
                                                }
                                                if ($_GET['search']) {
                                                    $parameters .= '&amp;search=' . $_GET['search'];
                                                }
                                                if ($_GET['page']) {
                                                    $parameters .= '&amp;page=' . $_GET['page'];
                                                }
                                                if ($_GET['paging']) {
                                                    $parameters .= '&amp;paging=' . $_GET['paging'];
                                                }
                                                if ($_GET['sorting']) {
                                                    $parameters .= '&amp;sorting=' . $_GET['sorting'];
                                                }
                                                if ($_GET['type']) {
                                                    $parameters .= '&amp;type=' . $_GET['type'];
                                                }
                                                echo '<a href="' . xtc_href_link(FILENAME_COUPON_ADMIN, 'cid=' . (int) $cc_list['coupon_id']) . $parameters . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                            }
                                            ?>
                                        </td>
                            </tr>
        <?php } ?>
                        <tr>
                            <td colspan="5">
                                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                        <?php if (is_object($cc_split)) { ?>
                                        <tr>
                                            <td class="smallText"><?php echo $cc_split->display_count($cc_query_numrows, $showpage, (int) $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUPONS); ?></td>
                                            <?php
                                            $parameters_for_page .= 'status=' . $_GET['status'];
                                            if ($_GET['search']) {
                                                $parameters_for_page .= '&search=' . $_GET['search'];
                                            }
                                            if ($_GET['paging']) {
                                                $parameters_for_page .= '&paging=' . $_GET['paging'];
                                            }
                                            if ($_GET['sorting']) {
                                                $parameters_for_page .= '&sorting=' . $_GET['sorting'];
                                            }
                                            if ($_GET['type']) {
                                                $parameters_for_page .= '&type=' . $_GET['type'];
                                            }
                                            ?>
                                            <td align="right" class="smallText"><?php echo $cc_split->display_links($cc_query_numrows, $showpage, MAX_DISPLAY_PAGE_LINKS, (int) $_GET['page'], $parameters_for_page); ?></td>
                                        </tr>
        <?php } ?>
                                    <tr>
                                        <td align="right" class="smallText"><?php echo '<a class="button" href="' . xtc_href_link('coupon_admin.php', '&amp;action=new') . '">' . BUTTON_INSERT . '</a>'; ?></td>
                                        <td align="right" class="smallText"><?php echo '<a class="button" href="' . xtc_href_link('coupon_admin.php', '&amp;action=deleteinactive') . '">' . BUTTON_DELETE_ALL_INAVTIVE . '</a>'; ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <?php
                $heading = array();
                $contents = array();
                // RECHTE SEITENLEISTE
                switch ($_GET['action']) {
                    case 'voucherdelete':
                        $heading[] = array('text' => '[' . (int) $_GET['cid'] . '] ' . DELETE_NOW);
                        $contents[] = array('text' => TEXT_CONFIRM_DELETE . '<br /><br />' . '<a class="button" href="' . xtc_href_link('coupon_admin.php', 'action=confirmdelete&cid=' . (int) $_GET['cid'] . $parameters, 'NONSSL') . '">' . BUTTON_CONFIRM . '</a>' . '<a class="button" href="' . xtc_href_link('coupon_admin.php', 'cid=' . $cInfo->coupon_id . $parameters, 'NONSSL') . '">' . BUTTON_CANCEL . '</a>');
                        break;

                    case 'deleteinactive':
                        $heading[] = array('text' => TEXT_COUPON_INACTIVE . ' ' . DELETE_NOW);
                        $contents[] = array('text' => TEXT_CONFIRM_DELETE_INACTIVE . '<br /><br />' . '<a class="button" href="' . xtc_href_link('coupon_admin.php', 'action=confirmdeleteinactive', 'NONSSL') . '">' . BUTTON_CONFIRM . '</a>' . '<a class="button" onclick="this.blur();" href="' . xtc_href_link('coupon_admin.php', '', 'NONSSL') . '">' . BUTTON_CANCEL . '</a>');
                        break;

                    default:
                        if (xtc_db_num_rows($cc_query) == 0) {
                            // KEINE GUTSCHEINE GEFUNDEN
                            $heading[] = array('text' => ERROR);
                            $contents_temp = '';
                            $contents_temp .= ERROR_NO_COUPONS_EXIST . '<br /><br />';
                            $contents_temp .= '<center>';
                            $contents_temp .= '<a class="button" href="' . xtc_href_link('coupon_admin.php', '&amp;action=new') . '">' . BUTTON_INSERT . '</a>';
                            $contents_temp .= '<a class="button" href="' . xtc_href_link('coupon_admin.php', '') . '">' . BUTTON_CANCEL . '</a>';
                            $contents_temp .= '</center>';
                            $contents[] = array('text' => $contents_temp);
                        } else {
                            // STANDARD ANZEIGE
                            $heading[] = array('text' => '[' . $cInfo->coupon_id . '] - CODE : ' . $cInfo->coupon_code);
                            $contents_temp = '';

                            $coupon_name_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cInfo->coupon_id . "' and language_id = '" . (int) $_SESSION['languages_id'] . "'");
                            $coupon_name = xtc_db_fetch_array($coupon_name_query);
                            if ($coupon_name['coupon_name'] != '') {
                                $contents_temp .= COUPON_NAME . ' : ' . $coupon_name['coupon_name'] . '<br />';
                            }

                            $amount = $cInfo->coupon_amount;
                            if ($cInfo->coupon_type == "G") {
                                $coupon_type = TYPE_G;
                                $amount = $currencies->format($amount);
                            } else if ($cInfo->coupon_type == "P") {
                                $coupon_type = TYPE_P;
                                $amount .= ' %';
                            } else if ($cInfo->coupon_type == "F") {
                                $coupon_type = TYPE_F;
                                $amount = $currencies->format($amount);
                            } else if ($cInfo->coupon_type == "S") {
                                $coupon_type = TYPE_S;
                                $amount = TEXT_FREE_SHIPPING;
                            }
                            $contents_temp .= COUPON_TYPE . ' : ' . $coupon_type . '<br />';
                            $contents_temp .= COUPON_AMOUNT . ' : ' . $amount . '<br />';
                            $contents_temp .= COUPON_CODE . ' : ' . $cInfo->coupon_code . '<br /><br />';

                            if ($cInfo->coupon_type != "G") {
                                $contents_temp .= COUPON_STARTDATE . ' : ' . xtc_date_short($cInfo->coupon_start_date) . '<br />';
                                $contents_temp .= COUPON_FINISHDATE . ' : ' . xtc_date_short($cInfo->coupon_expire_date) . '<br /><br />';
                                $contents_temp .= COUPON_USES_COUPON . ' : ' . $cInfo->uses_per_coupon . '<br />';
                                $contents_temp .= COUPON_USES_USER . ' : ' . $cInfo->uses_per_user . '<br /><br />';
                                $prod_details = NONE;
                                $cat_details = NONE;
                            }

                            $contents_temp .= DATE_CREATED . ' : ' . xtc_date_short($cInfo->date_created) . '<br />';
                            if (xtc_date_short($cInfo->date_modified) != '') {
                                $contents_temp .= DATE_MODIFIED . ' : ' . xtc_date_short($cInfo->date_modified) . '<br /><br />';
                            } else {
                                $contents_temp .= '<br />';
                            }

                            $contents_temp .= '<center>';
                            $contents_temp .= '<a class="button" href="' . xtc_href_link('coupon_admin.php', 'action=email&cid=' . $cInfo->coupon_id . $parameters, 'NONSSL') . '">' . BUTTON_EMAIL . '</a>';
                            $contents_temp .= '<a class="button" href="' . xtc_href_link('coupon_admin.php', 'action=voucherreport&cid=' . $cInfo->coupon_id . $parameters, 'NONSSL') . '">' . BUTTON_REPORT . '</a><br />';
                            $contents_temp .= '<a class="button" href="' . xtc_href_link('coupon_admin.php', 'status=*&action=voucherdelete&cid=' . $cInfo->coupon_id . $parameters, 'NONSSL') . '">' . BUTTON_DELETE . '</a>';
                            $contents_temp .= '<a class="button" href="' . xtc_href_link('coupon_admin.php', 'action=voucheredit&cid=' . $cInfo->coupon_id . $parameters, 'NONSSL') . '">' . BUTTON_EDIT . '</a>';
                            $contents_temp .= '</center>';

                            $contents[] = array('text' => $contents_temp);
                        }

                        break;
                }
                ?>                       
                <td width="25%" valign="top">
                    <?php
                    $box = new box;
                    echo $box->infoBox($heading, $contents);
                    echo '</td>' . "\n";
                    echo '</tr>' . "\n";
                    echo '</table>' . "\n";
            }
            ?>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
