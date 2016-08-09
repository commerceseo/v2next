<?php

/* -----------------------------------------------------------------
 * 	$Id: orders.php 1471 2015-07-22 20:34:59Z akausch $
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

require ('includes/application_top.php');
require_once (DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.phpmailer.php');
require_once (DIR_FS_INC . 'xtc_php_mail.inc.php');
require_once (DIR_FS_INC . 'xtc_add_tax.inc.php');
require_once (DIR_FS_INC . 'xtc_validate_vatid_status.inc.php');
require_once (DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
require_once (DIR_FS_INC . 'cseo_get_pdf_nr.inc.php');

/* magnalister v1.0.1 */
if (function_exists('magnaExecute'))
    magnaExecute('magnaSubmitOrderStatus', array(), array('order_details.php'));
/* END magnalister */
// BEGIN Hermes
require_once DIR_FS_CATALOG . 'includes/classes/class.hermes.php';
$hermes = new HermesAPI();
// END Hermes
// initiate template engine for mail
$smarty = new Smarty;
require (DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

if ((($_GET['action'] == 'edit') || ($_GET['action'] == 'update_order') || ($_GET['action'] == 'update_box')) && ($_GET['oID'])) {
    $oID = xtc_db_prepare_input($_GET['oID']);
    $orders_query = xtc_db_query("SELECT orders_id FROM " . TABLE_ORDERS . " WHERE orders_id = '" . xtc_db_input($oID) . "'");
    $order_exists = true;
    if (!xtc_db_num_rows($orders_query)) {
        $order_exists = false;
        $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
    }
}

require(DIR_WS_CLASSES . 'class.order.php');
if ((($_GET['action'] == 'edit') || ($_GET['action'] == 'update_order') || ($_GET['action'] == 'update_box')) && ($order_exists)) {
    $order = new order($_GET['oID']);
    /** BEGIN BILLPAY CHANGED * */
    require_once(DIR_FS_CATALOG . 'includes/billpay/utils/billpay_status_requests.php');
    /** EOF BILLPAY CHANGED * */
    require_once(DIR_FS_INC . 'cseo_get_pdf.inc.php');
    if (isset($_GET['pdf']) && $_GET['pdf'] == 'print_order') {
        if (isset($_POST['what_to_do']) && $_POST['what_to_do'] == 'pdf_rechnung_loeschen') {
            $pdf_pfad = cseo_get_pdf($_POST['oID'], false, false, false, true);
            if (file_exists($pdf_pfad)) {
                unlink($pdf_pfad);
                xtc_db_query("DELETE FROM orders_pdf WHERE order_id = '" . $_POST['oID'] . "'");
                $messageStack->add_session(PDF_DELETE_SUCCESS, 'success');
                if ($_POST['pdf_rechnung_senden'] != '1') {
                    xtc_redirect(FILENAME_ORDERS . '?page=' . $_POST['page'] . '&oID=' . $_POST['oID'] . '&action=edit#pdf');
				}
            }
        } elseif (isset($_POST['what_to_do']) && $_POST['what_to_do'] == 'delivery_loeschen') {
            $pdf_pfad = cseo_get_pdf_delivery($_POST['oID'], false, false, false, true);
            if (file_exists($pdf_pfad)) {
                unlink($pdf_pfad);
                xtc_db_query("DELETE FROM orders_pdf_delivery WHERE order_id = '" . $_POST['oID'] . "'");
                $messageStack->add_session(PDF_DELETE_SUCCESS, 'success');
            }
		} elseif (isset($_POST['what_to_do']) && ($_POST['what_to_do'] == 'bill' || $_POST['what_to_do'] == 'delivery')) {
            if ($_POST['what_to_do'] == 'delivery') {
                define('PDF_LIEFERSCHEIN', true);
                $type = 'lieferschein';
            } else {
                define('PDF_LIEFERSCHEIN', false);
            }
            include_once('create_pdf.php');
        }
    }

    if ($_POST['pdf_rechnung_senden'] == '1') {
        $check_status = xtc_db_fetch_array(xtc_db_query("SELECT o.customers_name, o.customers_email_address, o.orders_status, o.date_purchased, op.bill_name
											FROM orders o, orders_pdf op
											WHERE o.orders_id = '" . xtc_db_input($oID) . "'
											AND op.order_id = '" . xtc_db_input($oID) . "'"));

        $smarty->assign('language', $_SESSION['language']);
        $smarty->caching = false;

        $smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
        $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

        $smarty->assign('NAME', $check_status['customers_name']);
        $smarty->assign('ORDER_NR', cseo_get_pdf_nr($oID, true));
        $smarty->assign('ORDER_LINK', xtc_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL'));
        $smarty->assign('ORDER_DATE', xtc_date_long($check_status['date_purchased']));
        $smarty->assign('NOTIFY_COMMENTS', $notify_comments);
        $smarty->assign('ORDER_STATUS', $orders_status_array[$status]);

        require_once(DIR_FS_INC . 'cseo_get_mail_body.inc.php');
        $html_mail = $smarty->fetch('html:pdf_mail');
        $html_mail .= $signatur_html;
        $txt_mail = $smarty->fetch('txt:pdf_mail');
        $txt_mail .= $signatur_text;
        require_once(DIR_FS_INC . 'cseo_get_mail_data.inc.php');
        $mail_data = cseo_get_mail_data('pdf_mail');

        $email_pdf_bill_subject = str_replace('{$date}', xtc_date_long($check_status['date_purchased']), $mail_data['EMAIL_SUBJECT']);
        $email_pdf_bill_subject = str_replace('{$renr}', $_POST['oID'], $email_pdf_bill_subject);
        $email_pdf_bill_name = str_replace('{$store_name}', STORE_NAME, $mail_data['EMAIL_ADDRESS_NAME']);

        $pdf_pfad = cseo_get_pdf($_POST['oID'], false, false, false, true);
        $pdf_name = cseo_get_pdf($_POST['oID'], false, false, true);

        xtc_php_mail($mail_data['EMAIL_ADDRESS'], //  $from_email_address,
                $email_pdf_bill_name, //  $from_email_name,
                $check_status['customers_email_address'], //  $to_email_address,
                $check_status['customers_name'], //  $to_name,
                $mail_data['EMAIL_FORWARD'], //  $forwarding_to,
                $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], //  $reply_address_name,
                $pdf_pfad, //  $path_to_attachement,
                $pdf_name, //  $name_of_attachment,
                $email_pdf_bill_subject, //  $email_subject,
                $html_mail, //  $message_body_html,
                $txt_mail); //  $message_body_plain

        xtc_db_query("UPDATE orders_pdf SET notified_date = NOW(), customer_notified = '1' WHERE order_id = '" . $_POST['oID'] . "'");
        $_SESSION['msg']['sm'] = '<div class="success_msg">' . SUCCESS_ORDER_FIRST_SEND . '</div>';
        if ($_GET['action'] == 'update_box') {
            unset($_GET['pdf_email']);
            unset($_GET['update_box']);
            xtc_redirect(FILENAME_ORDERS . '?page=' . $_GET['page'] . '&oID=' . $_GET['oID']);
        } else {
            xtc_redirect(xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('pdf_email'))) . '#pdf');
        }
    }
}

$lang_query = xtc_db_query("SELECT languages_id FROM " . TABLE_LANGUAGES . " WHERE directory = '" . $order->info['language'] . "'");
$lang = xtc_db_fetch_array($lang_query);
$lang = $lang['languages_id'];

if (!isset($lang))
    $lang = $_SESSION['languages_id'];
$orders_statuses = array();
$orders_status_array = array();
$orders_status_query = xtc_db_query("SELECT orders_status_id, orders_status_name FROM " . TABLE_ORDERS_STATUS . " WHERE language_id = '" . $lang . "'");
while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
    $orders_statuses[] = array('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
}
switch ($_GET['action']) {

    case 'create_credit' :
        include('includes/modules/order_create_credit.php');
        break;

    case 'multistatus' :
        include('includes/modules/order_update_order.php');
        require_once('create_pdf_multi.php');
        require_once('pdf/PDFMerger.php');
        require_once(DIR_FS_INC . 'cseo_get_pdf.inc.php');

        //Hier die per multistatus angehakten felder auswerten
        $process_genpdfmail = 0;
        $process_genpdf = 0;
        $process_downloadpdfmerge = 0;
        $process_delivery_genpdf = 0;
        $process_delivery_downloadpdfmerge = 0;

        if ($_POST['pdf_rechnung_senden'] == "on") {
            $process_genpdfmail = 1;
        }
        if ($_POST['multistatus_genpdf'] == "on") {
            $process_genpdf = 1;
        }
        if ($_POST['multistatus_mergepdf'] == "on") {
            $process_downloadpdfmerge = 1;
        }
		
        if ($_POST['multistatus_genpdf_delivery'] == "on") {
            $process_delivery_genpdf = 1;
        }
        if ($_POST['multistatus_mergepdf_delivery'] == "on") {
            $process_delivery_downloadpdfmerge = 1;
        }

        $box = $_POST['multistatus_ids'];

        $mypdffiles = array();
        $mypdfdeliveryfiles = array();
        $myerrorlog;
        $mysuccesslog;
        $tmpids = "";

        while (list ($key, $val) = @each($box)) {

            $tmpids .= $val . '-';

            //PDF Rechnung generieren
            if ($process_genpdf) {
                //generate pdf for this specific order
                $testd = cseo_get_pdf($val, false, false, true);

                if (empty($testd) || PDF_RECHNUNG_OID == 'true') {
                    //Rechnungen generieren
                    $renrnext = cseo_get_pdf_nr($val, false, true);

                    custom_multistatusGenPDF($val, "rechnung", $renrnext);
                    $mysuccesslog = $mysuccesslog . "Rechnung für Bestellung " . $val . " wurde erzeugt.";

                    if ($process_genpdfmail) {
                        //Wenn E-Mail angehakt
                        $oID = $val;
                        // echo $oID.'<br />';
                        if (PDF_RECHNUNG_OID == 'true') {
                            $pdf_re_id = $oID;
                        } else {
                            $pdf_re_id = $renrnext;
                        }
                        $check_status = xtc_db_fetch_array(xtc_db_query("SELECT
																	o.customers_name, o.customers_email_address, o.orders_status, o.date_purchased
																FROM
																	orders o, orders_pdf op
																WHERE
																	o.orders_id = '" . (int) $oID . "'"));

                        $smarty->assign('language', $_SESSION['language']);
                        $smarty->caching = false;

                        $smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
                        $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

                        $smarty->assign('NAME', $check_status['customers_name']);
                        $smarty->assign('ORDER_NR', cseo_get_pdf_nr($oID, true));
                        $smarty->assign('ORDER_LINK', xtc_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL'));
                        $smarty->assign('ORDER_DATE', xtc_date_long($check_status['date_purchased']));
                        $smarty->assign('NOTIFY_COMMENTS', $notify_comments);
                        $smarty->assign('ORDER_STATUS', $orders_status_array[$status]);

                        $smarty->caching = false;
                        require_once(DIR_FS_INC . 'cseo_get_mail_body.inc.php');
                        $html_mail = $smarty->fetch('html:pdf_mail');
                        $html_mail .= $signatur_html;
                        $txt_mail = $smarty->fetch('txt:pdf_mail');
                        $txt_mail .= $signatur_text;
                        require_once(DIR_FS_INC . 'cseo_get_mail_data.inc.php');
                        $mail_data = cseo_get_mail_data('pdf_mail');

                        $email_pdf_bill_subject = str_replace('{$date}', xtc_date_long($check_status['date_purchased']), $mail_data['EMAIL_SUBJECT']);
                        $email_pdf_bill_subject = str_replace('{$renr}', $pdf_re_id, $email_pdf_bill_subject);
                        $email_pdf_bill_name = str_replace('{$store_name}', STORE_NAME, $mail_data['EMAIL_ADDRESS_NAME']);

                        $pdf_pfad = cseo_get_pdf($oID, false, false, false, true);
                        $pdf_name = cseo_get_pdf($oID, false, false, true);

                        xtc_php_mail($mail_data['EMAIL_ADDRESS'], $email_pdf_bill_name, $check_status['customers_email_address'], $check_status['customers_name'], '', $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], $pdf_pfad, $pdf_name, $email_pdf_bill_subject, $html_mail, $txt_mail);

                        xtc_db_query("UPDATE orders_pdf SET notified_date = NOW(), customer_notified = '1' WHERE order_id = '" . $oID . "'");
                        $mysuccesslog = $mysuccesslog . ' / E-Mail für Bestellung ' . $val . ' wurde erzeugt.';
                    }
                    $mysuccesslog .= '<br>';
                } else {
                    $myerrorlog = $myerrorlog . 'Rechnung für Bestellung ' . $val . ' nicht erzeugt da schon vorhanden<br>';
                }
            }

            //PDF Rechnung gesammelt als Download
            if ($process_downloadpdfmerge) {
                $myre = 'pdf_rechnungen/' . cseo_get_pdf($val, false, false, true);
                array_push($mypdffiles, $myre);
            }
			

            //PDF Lieferschein generieren
            if ($process_delivery_genpdf) {
                //generate pdf for this specific order
                $testd = cseo_get_pdf_delivery($val, false, false, true);

                if (empty($testd) || PDF_RECHNUNG_OID == 'true') {
                    //Rechnungen generieren
                    $renrnext = cseo_get_pdf_nr($val, false, true);

                    custom_multistatusGenPDF($val, "delivery", $renrnext);
                    $mysuccesslog = $mysuccesslog . "Lieferschein für Bestellung " . $val . " wurde erzeugt.";
                    $mysuccesslog .= '<br>';
                } else {
                    $myerrorlog = $myerrorlog . 'Lieferschein für Bestellung ' . $val . ' nicht erzeugt da schon vorhanden<br>';
                }
            }

			
			
            //PDF Lieferschein gesammelt als Download
            if ($process_delivery_downloadpdfmerge) {
                $myredelivery = 'pdf_lieferscheine/' . cseo_get_pdf_delivery($val, false, false, true);
                array_push($mypdfdeliveryfiles, $myredelivery);
            }
        }
		
		//Rechnung Merge
        if ($process_downloadpdfmerge == 0) {
            unset($_GET['action']);
        } else {
            $pdfmerger = new PDFMerger;
            foreach ($mypdffiles as $curpdf) {
                $pdfmerger->addPDF($curpdf, 'all');
            }
            $pdfmerger->merge('download', 'outputmerge.pdf');
        }
		
		//Lieferschein Merge
        if ($process_delivery_downloadpdfmerge == 0) {
            unset($_GET['action']);
        } else {
            $pdfmerger = new PDFMerger;
            foreach ($mypdfdeliveryfiles as $curpdf) {
                $pdfmerger->addPDF($curpdf, 'all');
            }
            $pdfmerger->merge('download', 'deliverymerge.pdf');
        }

        break;

    case 'send' :
        include('includes/modules/order_send.php');
        xtc_redirect(xtc_href_link(FILENAME_ORDERS, 'oID=' . $_GET['oID']));

    case 'update_order' :

        if (!empty($_POST['mail_template_title']) && !empty($_POST['comments'])) {
            xtc_db_query("INSERT INTO mail_templates (title, mail_text) VALUES ('" . xtc_db_input($_POST['mail_template_title']) . "', '" . xtc_db_prepare_input($_POST['comments']) . "')");
        }

        if (isset($_GET['del_id']) && $_GET['del_id'] != '') {
            xtc_db_query("DELETE FROM orders_status_history WHERE orders_status_history_id = '" . intval($_GET['del_id']) . "' ");
            $messageStack->add_session(SUCCESS_HISTORY_DELETE, 'success');
        } else {
            include('includes/modules/order_update_order.php');
        }
        xtc_redirect(xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('action', 'del_id')) . 'action=edit#comment'));
        break;

    case 'deleteconfirm' :
        include('includes/modules/order_deleteconfirm.php');
        xtc_redirect(xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action'))));
        break;

    case 'afterbuy_send' :
        $oID = xtc_db_prepare_input($_GET['oID']);
        require_once (DIR_WS_CLASSES . 'class.afterbuy.php');
        $aBUY = new xtc_afterbuy_functions($oID);
        if ($aBUY->order_send()) {
            $aBUY->process_order();
        }
        break;
}

require_once(DIR_WS_INCLUDES . 'header.php');

$order = new order($_GET['oID']);

if (($_GET['action'] == 'edit') && ($order_exists)) {
    include('includes/modules/order_details.php');
} else {
    if ($process_genpdf || $process_delivery_genpdf) {
        //Multi PDF Generierung
        if (!empty($mysuccesslog) && $process_genpdf) {
            echo('<div class="mysuccesslog"');
            echo("<h1>PDF Rechnungen erfolgreich generiert</h1><br/>");
            echo($mysuccesslog);
            echo("</div>");
        }
        if (!empty($mysuccesslog) && $process_delivery_genpdf) {
            echo('<div class="mysuccesslog"');
            echo("<h1>PDF Lieferscheine erfolgreich generiert</h1><br/>");
            echo($mysuccesslog);
            echo("</div>");
        }

        if (!empty($myerrorlog)) {
            echo('<div class="myerrorlog"');
            echo("<h1>PDF Rechnungen NICHT erfolgreich generiert</h1><br/>");
            echo ($myerrorlog);
            echo("</div>");
        }
    } else {
        // Multistatus Aenderung
        if (!empty($myupdatelog)) {
            echo('<div class="mysuccesslog"');
            echo("<h1>Bestellung erfolgreich aktualisiert</h1><br/>");
            echo($myupdatelog);
            echo("</div>");
        }

        if (!empty($myupdateerrorlog)) {
            echo('<div class="myerrorlog"');
            echo("<h1>Bestellung NICHT aktualisiert</h1><br/>");
            echo($myupdateerrorlog);
            echo("</div>");
        }
    }
    include('includes/modules/order_overview.php');
}

require (DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
