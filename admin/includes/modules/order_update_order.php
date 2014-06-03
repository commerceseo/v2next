<?php

/* -----------------------------------------------------------------
 * 	$Id: order_update_order.php 872 2014-03-21 14:46:30Z akausch $
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

defined("_VALID_XTC") or die("Direct access to this location isn't allowed.");

if (isset($_GET['download'])) {
    foreach ($_POST['download'] AS $id => $value)
        if ($value > 0)
            xtc_db_query("UPDATE orders_products_download SET download_count = '" . (int) $value . "' WHERE orders_products_download_id = '" . (int) $id . "'");

    $messageStack->add_session('Download wurde wieder aktiviert', 'success');
    xtc_redirect(xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('action')) . 'action=edit&oID=' . $_GET['oID']));
}

if (isset($_POST['multistatus_ids'])) {
    $oids = $_POST['multistatus_ids'];
} else {
    $oids = xtc_db_prepare_input($_GET['oID']);
}

$status = xtc_db_prepare_input($_POST['status']);
$comments = xtc_db_prepare_input($_POST['comments']);
$order_tracking_id = xtc_db_prepare_input($_POST['order_tracking_id']);
$order_delivery_id = xtc_db_prepare_input($_POST['order_delivery_id']);
$order_updated = false;

for ($i = 0, $n = sizeof($oids); $i < $n; $i++) {
    if ($_GET['action'] == 'multistatus') {
        $oID = $oids[$i];
    } else {
        $oID = $oids;
    }

    $check_status = xtc_db_fetch_array(xtc_db_query("SELECT customers_name, customers_email_address, orders_status, order_tracking_id, order_delivery_id, date_purchased FROM " . TABLE_ORDERS . " WHERE orders_id = '" . xtc_db_input($oID) . "';"));

    if ($_POST['status'] == ' ') {
        $myupdateerrorlog = 'Es wurde nichts geändert!';
    } else {
        if ($check_status['orders_status'] != $status || $comments != '' || $check_status['order_tracking_id'] != $order_tracking_id || $check_status['order_delivery_id'] != $order_delivery_id) {

            if ($status == MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_STORNO || $status == MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_SHIPPED) {

                $cba_result = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_ORDERS . " WHERE orders_id = '" . xtc_db_input($oID) . "' LIMIT 1;"));

                if ($cba_result['amazon_order_id'] != '') {
                    chdir("../CheckoutByAmazon");

                    include_once ('.config.inc.php');

                    switch ($status) {
                        case MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_STORNO:
                            $feeds = new MarketplaceWebService_MWSFeedsClient();
                            $MWSProperties = new MarketplaceWebService_MWSProperties();
                            $envelope = new SimpleXMLElement("<AmazonEnvelope></AmazonEnvelope>");
                            $envelope->Header->DocumentVersion = $MWSProperties->getDocumentVersion();
                            $envelope->Header->MerchantIdentifier = $MWSProperties->getMerchantToken();
                            $envelope->MessageType = "OrderAcknowledgement";
                            $envelope->Message->MessageID = 1;
                            $envelope->Message->OrderAcknowledgement->AmazonOrderID = $cba_result['amazon_order_id'];
                            $envelope->Message->OrderAcknowledgement->MerchantOrderID = $oID;
                            $envelope->Message->OrderAcknowledgement->StatusCode = "Failure";
                            $feedSubmissionId = $feeds->cancelOrder($envelope, DIR_FS_CATALOG . 'cache');
                            break;

                        case MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_SHIPPED:
                            $feeds = new MarketplaceWebService_MWSFeedsClient();
                            $MWSProperties = new MarketplaceWebService_MWSProperties();
                            $envelope = new SimpleXMLElement("<AmazonEnvelope></AmazonEnvelope>");
                            $envelope->Header->DocumentVersion = $MWSProperties->getDocumentVersion();
                            $envelope->Header->MerchantIdentifier = $MWSProperties->getMerchantToken();
                            $envelope->MessageType = "OrderFulfillment";
                            $envelope->Message->MessageID = 1;
                            $envelope->Message->OrderFulfillment->AmazonOrderID = $cba_result['amazon_order_id'];
                            $envelope->Message->OrderFulfillment->FulfillmentDate = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time() - 160);
                            $feedSubmissionId = $feeds->confirmShipment($envelope, DIR_FS_CATALOG . 'cache');
                            break;
                    }
                    chdir("../admin");
                }
            }
            xtc_db_query("UPDATE 
								orders 
							SET 
								orders_status = '" . xtc_db_input($status) . "', 
								order_tracking_id = '" . xtc_db_input($order_tracking_id) . "', 
								order_delivery_id = '" . xtc_db_input($order_delivery_id) . "', 
								last_modified = now() 
							WHERE 
								orders_id = '" . xtc_db_input($oID) . "';");

            if ($_POST['notify'] == 'on') {
                $customer_notified = '1';
                $notify_comments = '';
                if ($_POST['notify_comments'] == 'on') {
                    if ($comments != '') {
                        $notify_comments = $comments;
                    }
                } else {
                    $notify_comments = '';
                }

                // assign language to template for caching
                $smarty->assign('language', $_SESSION['language']);

                // set dirs manual
                $smarty->template_dir = DIR_FS_CATALOG . 'templates';
                $smarty->compile_dir = DIR_FS_CATALOG . 'templates_c';
                $smarty->config_dir = DIR_FS_CATALOG . 'lang';

                $smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
                $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

                $smarty->assign('NAME', $check_status['customers_name']);
                $smarty->assign('ORDER_NR', $oID);
                $smarty->assign('ORDER_ID', $oID);
                $smarty->assign('ORDER_LINK', xtc_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL'));
                $smarty->assign('ORDER_DATE', xtc_date_long($check_status['date_purchased']));

                $bestell_query = xtc_db_fetch_array(xtc_db_query("SELECT 
										o.orders_id AS order_id,
										o.order_tracking_id,
										o.order_delivery_id,
										o.customers_id AS order_customer_id,
										o.customers_firstname AS order_customer_firstname,
										o.customers_lastname AS order_customer_lastname,
										DATE_FORMAT(o.date_purchased, '%d.%m.%Y %H:%i:%s') AS order_datepurchased,
										s.orders_status_name AS order_status_name, 
										ot.text AS order_total 
									FROM 
										" . TABLE_ORDERS . " o 
										LEFT JOIN " . TABLE_ORDERS_TOTAL . " ot 
										ON (o.orders_id = ot.orders_id), 
										" . TABLE_ORDERS_STATUS . " s 
									WHERE 
										s.orders_status_id = o.orders_status
									AND
										o.orders_id = '" . (int) $oID . "'
									AND 
										s.language_id = '" . (int) $_SESSION['languages_id'] . "' 
									AND 
										ot.class = 'ot_total'
									ORDER BY 
										o.orders_id DESC"));

                $suche = array('##order_id##',
                    '##order_total##',
                    '##order_datepurchased##',
                    '##order_customer_id##',
                    '##order_customer_firstname##',
                    '##order_customer_lastname##',
                    '##order_status_name##',
                    '[b]',
                    '[/b]');

                $ersetze = array($bestell_query['order_id'],
                    $bestell_query['order_total'],
                    $bestell_query['order_datepurchased'],
                    $bestell_query['order_customer_id'],
                    $bestell_query['order_customer_firstname'],
                    $bestell_query['order_customer_lastname'],
                    $bestell_query['order_status_name'],
                    '<b>',
                    '</b>');

                $smarty->assign('NOTIFY_COMMENTS', nl2br(str_replace($suche, $ersetze, $notify_comments)));

                // $smarty->assign('NOTIFY_COMMENTS', nl2br($notify_comments));
                $smarty->assign('ORDER_STATUS', $orders_status_array[$status]);

                $smarty->caching = false;
                require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
                $html_mail = $smarty->fetch('html:change_order');
				if ($bestell_query['order_tracking_id'] != '') {
					$shippingtracking = xtc_db_fetch_array(xtc_db_query("SELECT * FROM shipping_tracking WHERE shipping_tracking_id = '".$bestell_query['order_delivery_id']."' AND language_id = '" . (int) $_SESSION['languages_id'] . "';"));
					$shtrtext = $shippingtracking['shipping_tracking_text'];
					$shtrurl = $shippingtracking['shipping_tracking_url'];
					$shtrnumber = $bestell_query['order_tracking_id'];
					$shipping_text = nl2br(str_replace('{$SHIPPING_TRACKING_ID}', $shtrnumber, $shtrtext));
					$shipping_url = nl2br(str_replace('{$SHIPPING_TRACKING_ID}', $shtrnumber, $shtrurl));
					$html_mail .= '<table width="90%" border="0" cellpadding="10" cellspacing="0" align="center" class="outerTable"><tr><td>';
					$html_mail .= $shipping_text . '<br>' . $shipping_url;
					$html_mail .= '</td></tr></table>';
				}
                $html_mail .= $signatur_html;
                $txt_mail = $smarty->fetch('txt:change_order');
                $txt_mail .= $signatur_text;
                require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
                $mail_data = cseo_get_mail_data('change_order');
                $email_change_order_subject = str_replace('{$nr}', $oID, $mail_data['EMAIL_SUBJECT']);
                $email_change_order_subject = str_replace('{$date}', xtc_date_long($check_status['date_purchased']), $email_change_order_subject);
                $email_change_order_subject = str_replace('{$name}', $check_status['customers_name'], $email_change_order_subject);

                $email_change_order_name = str_replace('{$shop_name}', STORE_NAME, $mail_data['EMAIL_ADDRESS_NAME']);
                $email_change_order_name = str_replace('{$shop_besitzer}', STORE_OWNER, $email_change_order_name);

                // Email an den Kunden
                xtc_php_mail($mail_data['EMAIL_ADDRESS'], $email_change_order_name, $check_status['customers_email_address'], $check_status['customers_name'], '', $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], $pdf_pfad, $pdf_name, $email_change_order_subject, $html_mail, $txt_mail);
            }

            xtc_db_query("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . xtc_db_input($oID) . "', '" . xtc_db_input($status) . "', now(), '" . $customer_notified . "', '" . xtc_db_input($comments) . "')");
            $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
            $myupdatelog;
            $myupdatelog = $myupdatelog . 'Bestellung ' . $oID . ' wurde aktualisiert.<br>';
            $order_updated = true;
        }
    }
}

if ($order_updated) {
    ### BEGIN - CALL NOVALNET PAYGATE TO UPDATE THE BOOKING STATUS ###
    $payment_method = $order->info['payment_method'];

    #print $_POST['status'].", $payment_method"; exit;
    if ($_POST['status'] == 3 && substr($payment_method, 0, 8) == 'novalnet') {
        $tid_string = $order->info['comments'];
        $aryTmp = explode('Novalnet Transaction ID :', $tid_string);
        $aryTmp = explode(' ', $aryTmp[1]);
        $tid = trim($aryTmp[1]);
        if ($tid) {
            $aryNNConfigKeys = array();
            $aryNNConfigValues = array();

            if ($payment_method == 'novalnet_cc') {
                $key = '6';
                $aryNNConfigKeys = array('MODULE_PAYMENT_NOVALNET_CC_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_CC_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_CC_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_CC_TARIFF_ID');
            } elseif ($payment_method == 'novalnet_cc3d') {
                $key = '6';
                $aryNNConfigKeys = array('MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID');
            } elseif ($payment_method == 'novalnet_elv_de') {
                $key = '2';
                $aryNNConfigKeys = array('MODULE_PAYMENT_NOVALNET_ELV_DE_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID');
            } elseif ($payment_method == 'novalnet_elv_at') {
                $key = '8';
                $aryNNConfigKeys = array('MODULE_PAYMENT_NOVALNET_ELV_AT_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID');
            }

            foreach ($aryNNConfigKeys as $conf_key => $conf_val) {
                $configuration_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '$conf_val'");
                $configuration_values = xtc_db_fetch_array($configuration_query);
                if ($configuration_values['configuration_value']) {
                    $aryNNConfigValues[] = $configuration_values['configuration_value'];
                }
            }

            if (count($aryNNConfigValues) > 0) {
                $vendor_id = $aryNNConfigValues[0];
                $auth_code = $aryNNConfigValues[1];
                $product_id = $aryNNConfigValues[2];
                $tariff_id = $aryNNConfigValues[3];

                ### Process the payment to paygate ##
                $nn_url = 'https://payport.novalnet.de/paygate.jsp';
                $nn_urlparam = "vendor=$vendor_id&auth_code=$auth_code&product=$product_id&key=$key&tariff=$tariff_id&tid=$tid&status=100&edit_status=1";
                #print "$nn_urlparam"; exit;
                ### Realtime accesspoint for communication to the Novalnet paygate ###
                $ch = curl_init($nn_url);
                curl_setopt($ch, CURLOPT_POST, 1);  // a non-zero parameter tells the library to do a regular HTTP post.
                curl_setopt($ch, CURLOPT_POSTFIELDS, $nn_urlparam);  // add POST fields
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);  // don't allow redirects
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // decomment it if you want to have effective ssl checking
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // decomment it if you want to have effective ssl checking
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // return into a variable
                curl_setopt($ch, CURLOPT_TIMEOUT, 240);  // maximum time, in seconds, that you'll allow the CURL functions to take
                ## establish connection
                $data = curl_exec($ch);

                ## determine if there were some problems on cURL execution
                $errno = curl_errno($ch);
                $errmsg = curl_error($ch);

                ###bug fix for PHP 4.1.0/4.1.2 (curl_errno() returns high negative value in case of successful termination)
                if ($errno < 0)
                    $errno = 0;
                ##bug fix for PHP 4.1.0/4.1.2
                #close connection
                curl_close($ch);
            }
        }
    }
	if(strpos(MODULE_PAYMENT_INSTALLED, 'shopgate.php') !== false){
	  /******* SHOPGATE **********/
	  include_once DIR_FS_CATALOG.'includes/external/shopgate/base/admin/orders.php';
	  setShopgateOrderStatus($oID, $status);
	  /******* SHOPGATE **********/
	}
    ### END - CALL NOVALNET PAYGATE TO UPDATE THE BOOKING STATUS ###
    // $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
} else {
    // $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
}

