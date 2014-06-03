<?php

/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2013-06-05 15:01:19 +0200 (Mi, 05 Jun 2013) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofort_sofortrechnung.php 420 2013-06-19 18:04:39Z akausch $
 */
require_once(DIR_FS_CATALOG . 'callback/sofort/sofort.php');
require_once(DIR_FS_CATALOG . 'callback/sofort/library/sofortLib.php');

class sofort_sofortrechnung extends sofort {

    function sofort_sofortrechnung() {
        global $order;

        parent::sofort();

        $this->_checkExistingSofortConstants('sr');

        //if(isset($_SESSION['sofort']['sofort_conditions_sr'])) unset($_SESSION['sofort']['sofort_conditions_sr']);

        $this->code = 'sofort_sofortrechnung';
        $this->title = MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG_TEXT_TITLE_ADMIN;
        $this->title_extern = MODULE_PAYMENT_SOFORT_SR_TEXT_TITLE;
        $this->paymentMethod = 'SR';

        if (defined('MODULE_PAYMENT_SOFORT_SR_RECOMMENDED_PAYMENT') && MODULE_PAYMENT_SOFORT_SR_RECOMMENDED_PAYMENT == 'True')
            $this->title_extern .= ' ' . MODULE_PAYMENT_SOFORT_SR_RECOMMENDED_PAYMENT_TEXT;

        $this->enabled = ((defined('MODULE_PAYMENT_SOFORT_SR_STATUS') && MODULE_PAYMENT_SOFORT_SR_STATUS == 'True') ? true : false);
        $this->description = MODULE_PAYMENT_SOFORT_SR_TEXT_DESCRIPTION . '<br />' . MODULE_PAYMENT_SOFORT_MULTIPAY_VERSIONNUMBER . ': ' . HelperFunctions::getSofortmodulVersion();

        if ($this->_isInstalled() && !$this->_modulVersionCheck()) {
            $this->description = '<span style ="color:red; font-weight: bold; font-size: 1.2em">' . MODULE_PAYMENT_SOFORT_MULTIPAY_UPDATE_NOTICE . '</span><br /><br />' . $this->description;
        }

        $this->description .= MODULE_PAYMENT_SOFORT_SR_TEXT_DESCRIPTION_EXTRA;
        $this->sort_order = (defined('MODULE_PAYMENT_SOFORT_SR_SORT_ORDER') ? MODULE_PAYMENT_SOFORT_SR_SORT_ORDER : false);

        if (is_object($order))
            $this->update_status();

        if (defined('MODULE_PAYMENT_SOFORT_SR_STATUS')) {
            $this->invoice = new PnagInvoice(MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY, '');
            $this->invoice->setVersion(HelperFunctions::getSofortmodulVersion());

            if (defined('MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED') && MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED == "True")
                $this->invoice->enableLog();
        }
    }

    function selection() {
        if (!parent::selection()) {
            $this->invoice->log("Notice: Paymentmethod " . $this->code . " will be deactivated for selection.");
            $this->enabled = false;

            return false;
        }

        if (HelperFunctions::isGambio() && strpos($_SERVER['PHP_SELF'], 'checkout_payment.php') === false) {
            //if shopsystem is gambio and its NOT the checkout_payment.php: The virtual-check would maybe disable this module at wrong request.
            //Gambio calls function selection() nearly every time when class payment->constructor() is called!
        } else {
            //virtual content with SR is not allowed
            if ($this->_orderHasVirtualProducts() || $this->_deliveryAddressDoesNotExist()) {
                $this->invoice->log("Notice: Paymentmethod " . $this->code . " will be deactivated for selection because of found virtual products in cart.");
                $this->enabled = false; //disable is compatible with xtc3/comSeo/Modified but not with gambio - dont change!

                return false;
            }
        }

        $title = MODULE_PAYMENT_SOFORT_MULTIPAY_JS_LIBS;

        $cost = '';

        if (array_key_exists('ot_sofort', $GLOBALS))
            $cost = $GLOBALS['ot_sofort']->get_percent($this->code, 'price');

        //if Gambio-Lightbox then show normal conditions-link
        $conditionsText = (defined('GM_LIGHTBOX_CHECKOUT') && function_exists('gm_get_conf') && gm_get_conf('GM_LIGHTBOX_CHECKOUT') == 'true') ? MODULE_PAYMENT_SOFORT_SR_CHECKOUT_CONDITIONS_WITH_LIGHTBOX : MODULE_PAYMENT_SOFORT_SR_CHECKOUT_CONDITIONS;

        $fields = array(
            array(
                'title' => $conditionsText,
                'field' => xtc_draw_checkbox_field('sofort_conditions_sr', 'sofort_conditions_sr', false)
            )
        );

        //commerce:SEO - Bugfix
        if (isset($_REQUEST['xajax']) && !empty($_REQUEST['xajax'])) {
            $fields[0]['title'] = utf8_decode($fields[0]['title']);

            return array('id' => $this->code, 'module' => utf8_decode($this->title_extern), 'fields' => $fields, 'description' => utf8_decode($title), 'module_cost' => utf8_decode($cost));
        } else {
            return array('id' => $this->code, 'module' => $this->title_extern, 'fields' => $fields, 'description' => $title, 'module_cost' => $cost);
        }
    }

    function before_process() {
        parent::before_process();

        //downloads and gift-voucher are not allowed with SR
        if ($this->_orderHasVirtualProducts() || $this->_deliveryAddressDoesNotExist()) {
            $this->invoice->log("Notice: " . $this->code . " selected for payment. Redirect to cancelUrl because of found virtual products in cart. Time: " . date("d.m.Y, G:i:s"));
            $errors = array(0 => array('code' => '10003'));
            xtc_redirect(HelperFunctions::getCancelUrl($this->code, $errors));
        }
    }

    function pre_confirmation_check($vars = '') {
        parent::pre_confirmation_check($vars);

        //in CommerceSEO check is sometimes done with Ajax
        if (isset($_POST['xajax']) && $_POST['xajax'] == 'updatePaymentModule') {
            $is_ajax = true;
            $requestData = $vars;
        } else {
            $is_ajax = false;
            $requestData = $_POST;
        }

        $requestData['sofort_conditions_sr'] = trim($requestData['sofort_conditions_sr']);
        $_SESSION['sofort']['sofort_conditions_sr'] = isset($requestData['sofort_conditions_sr']) ? $requestData['sofort_conditions_sr'] : '';

        if ($_SESSION['sofort']['sofort_conditions_sr'] != 'sofort_conditions_sr') {
            if ($is_ajax) {
                $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_10002);
                $_SESSION['checkout_payment_error'] = $payment_error_return;
            } else {
                $payment_error_return = 'payment_error=' . $this->code . '&error_codes=10002';
                $redirectUrl = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false);
                xtc_redirect(HelperFunctions::cleanUrlParameter($redirectUrl));
            }
        }

        return false;
    }

    function payment_action() {
        global $order, $insert_id, $order_totals;

        $orderId = (int) $insert_id;
        $customerId = $_SESSION['customer_id'];
        $currency = $_SESSION['currency'];

        //if buyer will not successfully abort the payment (e.g. close the SOFORT-Wizard) and then goes to the
        //shop-index.php and want to pay again -> set the orderStatus to "aborted" later
        $_SESSION['sofort']['cart_pn_sofortueberweisung_id'] = $_SESSION['cart']->cartID . '-' . $orderId;

        $reasons = $this->_getReasons($this->paymentMethod, $customerId, $order, $orderId);
        $paymentSecret = md5(mt_rand() . microtime());
        $userVariable_0 = $orderId;
        $userVariable_1 = $customerId;
        $userVariable_2 = $_SESSION['cart']->cartID;
        $userVariable_3 = $paymentSecret;

        $successUrl = xtc_href_link('callback/sofort/ressources/scripts/sofortReturn.php', 'sofortaction=success&sofortcode=' . $this->code, 'SSL', true, false);
        $successUrl = HelperFunctions::cleanUrlParameter($successUrl);

        $cancelUrl = xtc_href_link('callback/sofort/ressources/scripts/sofortReturn.php', 'sofortaction=cancel&sofortcode=' . $this->code, 'SSL', true, false);
        $cancelUrl = HelperFunctions::cleanUrlParameter($cancelUrl);

        $notificationUrl = xtc_href_link('callback/sofort/callback.php', 'paymentSecret=' . $paymentSecret . '&action=multipay', 'SSL', true, false);
        $notificationUrl = HelperFunctions::cleanUrlParameter($notificationUrl);

        $this->invoice->setCurrency($currency);  //others than EUR will currently not be accepted by API!
        $this->invoice->setReason(HelperFunctions::convertEncoding($reasons[0], 3), HelperFunctions::convertEncoding($reasons[1], 3));
        $this->invoice->setSuccessUrl(HelperFunctions::convertEncoding($successUrl, 3));
        $this->invoice->setAbortUrl(HelperFunctions::convertEncoding($cancelUrl, 3));
        $this->invoice->setTimeoutUrl(HelperFunctions::convertEncoding($cancelUrl, 3));
        $this->invoice->setNotificationUrl(HelperFunctions::convertEncoding($notificationUrl, 3));
        $this->invoice->addUserVariable(HelperFunctions::convertEncoding($userVariable_0, 3));
        $this->invoice->addUserVariable(HelperFunctions::convertEncoding($userVariable_1, 3));
        $this->invoice->addUserVariable(HelperFunctions::convertEncoding($userVariable_2, 3));
        $this->invoice->addUserVariable(HelperFunctions::convertEncoding($userVariable_3, 3));
        $this->invoice->setEmailCustomer(HelperFunctions::convertEncoding($order->customer['email_address'], 3));
        $this->invoice->setPhoneNumberCustomer($order->customer['telephone']);
        $this->invoice->setSofortrechnung();
        $this->invoice->setCustomerId(HelperFunctions::convertEncoding($customerId, 3));
        $this->invoice->setOrderId($orderId);
        $this->invoice->setDebitorVatNumber(HelperFunctions::convertEncoding($_SESSION['customer_vat_id'], 3));

        $this->_addCustomerAddressesToInvoice();
        $this->_addProductsToInvoice();
        $this->_addShippingToInvoice();
        $this->_addPriceModificatorsToInvoice($order_totals);

        //check shopTotal against the invoiceclassTotal
        //if the difference is bigger than 1%: articles/discounts/etc. have not been given correct to $this->invoice
        $shopTotal = $this->_getShopTotal($order_totals, $order);
        $invoiceTotal = $this->invoice->getAmount();

        if (!$this->_checkShopTotalVsInvoiceTotal($shopTotal, $invoiceTotal)) {
            $this->invoice->logError($this->code . ": ShopTotal ($shopTotal) is not near InvoiceTotal ($invoiceTotal)! Customer-ID " . $customerId . ".
								Are you using price-modification-tools which are not supported by this 'Rechnung bei SOFORT'-module? Was the tax-rate
								of all affected articles, shipping options and other positions set correctly? Did you set the currency-exchange-rate
								correctly? Buyer redirected to cancel-URL.");
            $errors = array(0 => array('code' => '10007'));
            xtc_redirect(HelperFunctions::getCancelUrl($this->code, $errors));
        }

        //send all data to the API and place an order at SOFORT if no errors
        $this->invoice->checkout();

        if ($this->invoice->isError()) {
            $this->invoice->logWarning("API-Call returned false. Redirect to cancel-URL. API-Errors: " . print_r($this->invoice->getErrors(), true) . " Time: " . date("d.m.Y, G:i:s"));
            xtc_redirect(HelperFunctions::getCancelUrl($this->code, $this->invoice->getErrors()));
        } else {
            $url = $this->invoice->getPaymentUrl();
            $transactionId = $this->invoice->getTransactionId();

            //seller and customer comment
            $time = date("d.m.Y, G:i:s");

            //set temp-status (only table orders_history, not table orders)
            $tmpStatusId = MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID;

            //dont set a new status? -> then use the last order status
            if (HelperFunctions::statusIsUnchangedStatus($tmpStatusId)) {
                $tmpStatusId = HelperFunctions::getLastOrderStatus($orderId);
            }

            $tmpStatusId = HelperFunctions::checkStatusId($tmpStatusId);
            $tmpComment = MODULE_PAYMENT_SOFORT_SR_TMP_COMMENT . ' ' . MODULE_PAYMENT_SOFORT_TRANSLATE_TIME . ': ' . $time;
            HelperFunctions::insertHistoryEntry((int) $orderId, $tmpStatusId, $tmpComment);

            //comment only for seller
            $tmpCommentSeller = constant('MODULE_PAYMENT_SOFORT_SR_TMP_COMMENT_SELLER') . ' ' . MODULE_PAYMENT_SOFORT_TRANSLATE_TIME . ': ' . $time;
            HelperFunctions::insertHistoryEntry((int) $orderId, -1, $tmpCommentSeller, 0);
            xtc_db_query("UPDATE " . HelperFunctions::escapeSql(TABLE_ORDERS) . " SET orders_ident_key='" . HelperFunctions::escapeSql($transactionId) . "' WHERE orders_id='" . HelperFunctions::escapeSql($orderId) . "'");

            //save all important data in our sofort-tables
            $sofortOrderId = HelperFunctions::insertSofortOrder($orderId, $paymentSecret, $transactionId, $this->paymentMethod);

            if (!$sofortOrderId) {
                $this->invoice->logWarning("Warning: Saving of orderdetails in table sofort_orders failed. Function insertSofortOrder() returned false.
					Given params: OrderId: $orderId, PaymentSecret: $paymentSecret, TransId: $transactionId, PaymentMethod:" . $this->paymentMethod);
            }

            $sofortProductsSaved = $this->_insertOrderAttributesInSofortTables($orderId);

            if (!$sofortProductsSaved) {
                $this->invoice->logWarning("Warning: Saving of orders_products_id in table sofort_products failed. Function insertOrderAttributesInsofortTables() returned false.
						Given params: OrderId: $orderId");
            }

            $_SESSION['sofort']['sofort_payment_url'] = $url;
            $_SESSION['sofort']['sofort_payment_method'] = $this->code;

            //following file will always redirect to SOFORT-Wizard (or to Error-URL)
            xtc_redirect(xtc_href_link('callback/sofort/ressources/scripts/processSofortPayment.php', '', 'SSL', true, false));
        }
    }

    function _addCustomerAddressesToInvoice() {
        global $order;

        //split address into street and number at last dot or space
        if (!preg_match('#(.+)[ .](.+)#i', trim($order->billing['street_address']), $billing)) {
            $billing = array();
            $billing[1] = trim($order->billing['street_address']);
            $billing[2] = '';
        }

        if (!preg_match('#(.+)[ .](.+)#i', trim($order->delivery['street_address']), $delivery)) {
            $delivery = array();
            $delivery[1] = trim($order->delivery['street_address']);
            $delivery[2] = '';
        }

        $billingCompanyName = trim($order->billing['company']);
        $billingNameAdditive = '';

        //get billing-salutation: 2=masculine, 3=feminine
        //modified eCommerce Shopsoftware Bug: only $order->customer has a gender
        $billingSalutation = $this->_getGenderFromAddressBook($order->billing['firstname'], $order->billing['lastname'], $order->billing['company'], $order->billing['street_address'], $order->billing['postcode'], $order->billing['city'], $order->billing['country_id'], $order->billing['zone_id']);

        $deliveryCompanyName = trim($order->delivery['company']);
        $deliveryNameAdditive = '';

        //get deliver-salutation: 2=masculine, 3=feminine
        $deliverSalutation = $this->_getGenderFromAddressBook($order->delivery['firstname'], $order->delivery['lastname'], $order->delivery['company'], $order->delivery['street_address'], $order->delivery['postcode'], $order->delivery['city'], $order->delivery['country_id'], $order->delivery['zone_id']);

        $this->invoice->addInvoiceAddress(HelperFunctions::convertEncoding($order->billing['firstname'], 3), HelperFunctions::convertEncoding($order->billing['lastname'], 3), HelperFunctions::convertEncoding($billing[1], 3), HelperFunctions::convertEncoding($billing[2], 3), $order->billing['postcode'], HelperFunctions::convertEncoding($order->billing['city'], 3), $billingSalutation, $order->billing['country']['iso_code_2'], HelperFunctions::convertEncoding($billingNameAdditive, 3), HelperFunctions::convertEncoding($order->billing['suburb'], 3), HelperFunctions::convertEncoding($billingCompanyName, 3));
        $this->invoice->addShippingAddress(HelperFunctions::convertEncoding($order->delivery['firstname'], 3), HelperFunctions::convertEncoding($order->delivery['lastname'], 3), HelperFunctions::convertEncoding($delivery[1], 3), HelperFunctions::convertEncoding($delivery[2], 3), $order->delivery['postcode'], HelperFunctions::convertEncoding($order->delivery['city'], 3), $deliverSalutation, $order->delivery['country']['iso_code_2'], HelperFunctions::convertEncoding($deliveryNameAdditive, 3), HelperFunctions::convertEncoding($order->delivery['suburb'], 3), HelperFunctions::convertEncoding($deliveryCompanyName, 3));

        return true;
    }

    /**
     * add all bought products to $this->invoice
     */
    function _addProductsToInvoice() {
        global $order;

        foreach ($order->products as $product) {
            //get attributes and add as description to item
            $description = '';

            $productId = (int) $product['id']; //dont use buggy xtc_get_prid()

            if ((isset($product['attributes'])) && (sizeof($product['attributes']) > 0)) {
                foreach ($product['attributes'] as $attribute) {
                    if (!empty($attribute['option']) || !empty($attribute['value'])) {
                        $description .= $attribute['option'] . ": " . $attribute['value'];
                    }

                    //add attributes articlenummer
                    $query = xtc_db_query('SELECT attributes_model
						FROM ' . TABLE_PRODUCTS_ATTRIBUTES . ' 
						WHERE products_id = "' . HelperFunctions::escapeSql($productId) . '" 
						AND options_id = "' . HelperFunctions::escapeSql($attribute['option_id']) . '"
						AND options_values_id = "' . HelperFunctions::escapeSql($attribute['value_id']) . '"');
                    $result = xtc_db_fetch_array($query);
                    $articleNumber = trim($result['attributes_model']);
                    if (!empty($result['attributes_model'])) {
                        $description .= ' (' . $articleNumber . ')';
                    }
                    $description .= "\n";
                }

                $description = trim($description);
            }

            //prevent GX-Customizer-Problems (not all) for later sync
            if (HelperFunctions::isGambio()) {
                $product['id'] = HelperFunctions::deleteGxCustomizerData($product['id'], $_SESSION['customer_id']);
            }

            $itemId = HelperFunctions::sortSofortItemId($product['id']);

            $this->invoice->addItemToInvoice(HelperFunctions::convertEncoding($itemId, 3), HelperFunctions::convertEncoding($product['model'], 3), HelperFunctions::convertEncoding($product['name'], 3), $product['price'], 0, HelperFunctions::convertEncoding($description, 3), $product['qty'], $product['tax']);
        }
    }

    /**
     * add shippinginfo to $this->invoice
     */
    function _addShippingToInvoice() {
        global $order;

        list ($shippingModule, $shippingMethod) = explode('_', $_SESSION['shipping']['id']); //e.g. "dp_dp" or "freeamount_freeamount"

        if ($shippingModule) {
            global $$shippingModule; //notice $$

            $shippingObject = $$shippingModule; //notice $$
            $shippingAmount = $order->info['shipping_cost'] * $order->info['currency_value'];

            $shippingTaxClass = (isset($shippingObject->tax_class)) ? xtc_get_tax_rate($shippingObject->tax_class) : 0;

            $itemId = 'shipping|' . substr($shippingModule . '|' . $shippingMethod, 0, 22);
            $this->invoice->addItemToInvoice($itemId, '', HelperFunctions::convertEncoding(html_entity_decode($order->info['shipping_method'], ENT_QUOTES, HelperFunctions::getIniValue('shopEncoding')), 3), $shippingAmount, 1, '', 1, (double) $shippingTaxClass);
        }
    }

    /**
     * add discounts or agio (e.g. ot_sofort, loworderfee, discount...) to $this->invoice
     */
    function _addPriceModificatorsToInvoice($orderTotals) {
        //check optional price-modificators
        if (is_array($orderTotals)) {
            foreach ($orderTotals as $totalModule) {
                $itemId = 'discount|' . substr($totalModule['code'], 0, 22);

                if ($totalModule['code'] == 'ot_sofort') {
                    $tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_SOFORT_TAX_CLASS);
                    $amountValue = $totalModule['value'];
                    $this->invoice->addItemToInvoice($itemId, '', HelperFunctions::convertEncoding(html_entity_decode($totalModule['title'], ENT_QUOTES, HelperFunctions::getIniValue('shopEncoding')), 3), $amountValue, 2, '', 1, $tax);
                    continue;
                }

                if ($totalModule['code'] == 'ot_discount') {
                    $tax = 19;
                    $amountValue = ($totalModule['value'] > 0) ? ($totalModule['value'] * -1) : $totalModule['value'];
                    $this->invoice->addItemToInvoice($itemId, '', HelperFunctions::convertEncoding(html_entity_decode($totalModule['title'], ENT_QUOTES, HelperFunctions::getIniValue('shopEncoding')), 3), $amountValue, 2, '', 1, $tax);
                    continue;
                }

                if ($totalModule['code'] == 'ot_gv') {
                    $tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_GV_TAX_CLASS);
                    $amountValue = ($totalModule['value'] > 0) ? ($totalModule['value'] * -1) : $totalModule['value'];
                    $this->invoice->addItemToInvoice($itemId, '', HelperFunctions::convertEncoding(html_entity_decode($totalModule['title'], ENT_QUOTES, HelperFunctions::getIniValue('shopEncoding')), 3), $amountValue, 2, '', 1, $tax);
                    continue;
                }

                if ($totalModule['code'] == 'ot_coupon') {
                    $tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_COUPON_TAX_CLASS);
                    $amountValue = ($totalModule['value'] > 0) ? ($totalModule['value'] * -1) : $totalModule['value'];
                    $this->invoice->addItemToInvoice($itemId, '', HelperFunctions::convertEncoding(html_entity_decode($totalModule['title'], ENT_QUOTES, HelperFunctions::getIniValue('shopEncoding')), 3), $amountValue, 2, '', 1, $tax);
                    continue;
                }

                $itemId = 'agio|' . substr($totalModule['code'], 0, 26);

                if ($totalModule['code'] == 'ot_loworderfee') {
                    $tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS);
                    $amountValue = $totalModule['value'];
                    $this->invoice->addItemToInvoice($itemId, '', HelperFunctions::convertEncoding(html_entity_decode($totalModule['title'], ENT_QUOTES, HelperFunctions::getIniValue('shopEncoding')), 3), $amountValue, 2, '', 1, $tax);
                    continue;
                }
            }
        }
    }

    function install() {
        $sofortStatuses = $this->_insertAndReturnSofortStatus();
        $canceledStatus = (isset($sofortStatuses['canceled']) && !empty($sofortStatuses['canceled'])) ? $sofortStatuses['canceled'] : '';
        $confirmedStatus = (isset($sofortStatuses['invoice_confirmed']) && !empty($sofortStatuses['invoice_confirmed'])) ? $sofortStatuses['invoice_confirmed'] : '';
        $unconfirmedStatus = (isset($sofortStatuses['unconfirmed']) && !empty($sofortStatuses['unconfirmed'])) ? $sofortStatuses['unconfirmed'] : '';

        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SR_STATUS', 'False', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SR_SORT_ORDER', '0', '6', '16', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SR_RECOMMENDED_PAYMENT', 'False', '6', '5', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG_ALLOWED', '', '6', '12', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SR_ZONE', '0', '6', '13', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");

        //"Tempor�r - Rechnung unbest�tigt" - pending-confirm_invoice
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SR_PEN_CON_INV_STATUS_ID', '" . HelperFunctions::escapeSql($unconfirmedStatus) . "',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

        //"Best�tigt - Rechnung wurde best�tigt" - pending-not_credited_yet
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SR_PEN_NOT_CRE_YET_STATUS_ID', '" . HelperFunctions::escapeSql($confirmedStatus) . "',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

        //"Stornierung (VOR Best�tigung)" - loss-canceled
        //"Automatische Stornierung (Best�tigungszeitraum abgelaufen)" - loss-confirmation_period_expired
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SR_LOS_CAN_STATUS_ID', '" . HelperFunctions::escapeSql($canceledStatus) . "',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

        //"Stornierung NACH Best�tigung (=Gutschrift)" - refunded-refunded
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SR_REF_REF_STATUS_ID', '" . HelperFunctions::escapeSql($canceledStatus) . "',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

        $this->_setSrToNotAllowedDownloads();

        //install shared keys, that are used by all/most multipay-modules
        parent::install();
    }

    function remove() {
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_PAYMENT_SOFORT_SR%'");
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG%'");

        //if this is the last removing of a multipay-paymentmethod --> we also remove all shared keys, that are used by all/most multipay-modules
        parent::remove();
    }

    function keys() {
        parent::keys();

        return array(
            'MODULE_PAYMENT_SOFORT_SR_STATUS',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_AUTH',
            'MODULE_PAYMENT_SOFORT_SR_RECOMMENDED_PAYMENT',
            'MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG_ALLOWED',
            'MODULE_PAYMENT_SOFORT_SR_ZONE',
            'MODULE_PAYMENT_SOFORT_SR_SORT_ORDER',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_PROF_SETTINGS',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_ABORTED_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_SR_PEN_CON_INV_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_SR_PEN_NOT_CRE_YET_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_SR_LOS_CAN_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_SR_REF_REF_STATUS_ID',
                //'MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED',
        );
    }

    /**
     * if there is more than 1% difference this function returns false
     * @return bool
     */
    function _checkShopTotalVsInvoiceTotal($shopTotal, $invoiceTotal) {
        $percent = ($shopTotal < $invoiceTotal) ? $shopTotal / $invoiceTotal : $invoiceTotal / $shopTotal;

        return ($percent < 0.99) ? false : true;
    }

    /**
     * set SR into the configuration-value DOWNLOAD_UNALLOWED_PAYMENT (found in shop-backend)
     * @return always true
     */
    function _setSrToNotAllowedDownloads() {
        $query = xtc_db_query('SELECT configuration_value FROM ' . TABLE_CONFIGURATION . ' WHERE configuration_key = "DOWNLOAD_UNALLOWED_PAYMENT"');

        if (!xtc_db_num_rows($query))
            return true;

        $result = xtc_db_fetch_array($query);
        $configurationValue = $result['configuration_value'];
        $configurationValues = explode(',', $configurationValue);

        foreach ($configurationValues as $key => $value)
            $configurationValues[$key] = trim($value);

        if (!in_array('sofort_sofortrechnung', $configurationValues)) {
            $configurationValues[] = 'sofort_sofortrechnung';
            $newConfigurationValue = implode(',', $configurationValues);
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . HelperFunctions::escapeSql($newConfigurationValue) . "' WHERE configuration_key = 'DOWNLOAD_UNALLOWED_PAYMENT'");
        }

        return true;
    }

    /**
     * search for downloads and gift-voucher in cart
     * @return bool
     */
    function _orderHasVirtualProducts() {
        global $order;

        if (!is_object($order))
            return false;
        if ($order->content_type == 'virtual')
            return true;

        if (is_object($_SESSION['cart'])) {
            if ($_SESSION['cart']->count_contents() != $_SESSION['cart']->count_contents_virtual())
                return true;
        }

        //search for Gift-Voucher, they (must) start with "GIFT..."
        foreach ($order->products as $oneProduct) {
            if (strpos($oneProduct['model'], 'GIFT') === 0)
                return true;
        }

        //search for downloads
        if (is_object($_SESSION['cart'])) {
            $cartContents = $_SESSION['cart']->contents;
            reset($cartContents);

            if ($cartContents) {
                foreach ($cartContents as $key => $value) {
                    if (isset($cartContents[$key]['attributes'])) {
                        $productId = explode('{', $key);
                        reset($cartContents[$key]['attributes']);

                        foreach ($cartContents[$key]['attributes'] as $value) {
                            $virtualCheck = xtc_db_fetch_array(
                                    xtc_db_query("SELECT count(*) AS total
																  FROM	 " . TABLE_PRODUCTS_ATTRIBUTES . " pa,
																		 " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
																  WHERE	 pa.products_id = '" . (int) $productId[0] . "'
																  AND	 pa.options_values_id = '" . (int) $value . "'
																  AND	 pa.products_attributes_id = pad.products_attributes_id")
                            );

                            if ($virtualCheck['total'] > 0) {
                                return true;
                            }
                        }

                        for ($i = 1; $i < count($productId); ++$i) {
                            $attributeId = explode('}', $productId[$i]);
                            $stringCheckQry = xtc_db_query("SELECT	products_options_name
															FROM	" . TABLE_PRODUCTS_OPTIONS . "
															WHERE	products_options_id = " . (int) $attributeId[0]);

                            while ($stringCheckRes = xtc_db_fetch_array($stringCheckQry)) {
                                $stringCheck = $stringCheckRes['products_options_name'];

                                if ($stringCheck == 'downloads' || $stringCheck == 'Downloads' || $stringCheck == 'download' || $stringCheck == 'Download') {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }

        //no virtual products found
        return false;
    }

    /**
     * check if delivery address exists - doesnt exist, if there are only downloads in cart
     * @return bool
     */
    function _deliveryAddressDoesNotExist() {
        global $order;

        if (!is_object($order) || !isset($order->delivery) || !is_array($order->delivery))
            return true; //delivery address does not exist

        $delivery = $order->delivery;

        if (!isset($delivery['firstname']) || !isset($delivery['lastname']) || !isset($delivery['street_address']) || !isset($delivery['city']) || !isset($delivery['postcode']) || !isset($delivery['country']['iso_code_2'])) {
            return true; //delivery address does not exist
        }

        if (empty($delivery['firstname']) && empty($delivery['lastname']) && empty($delivery['street_address']) && empty($delivery['city']) && empty($delivery['postcode']) && empty($delivery['country']['iso_code_2'])) {
            return true; //delivery address does not exist
        }

        return false; //delivery address exists
    }

    /**
     * Locates and Saves orders_products_id with corresponding item_id
     * @param int $ordersId
     */
    public function _insertOrderAttributesInSofortTables($ordersId) {
        $lang = $_SESSION['languages_id'];
        $resProd = mysql_query("SELECT	orders_products_id,
										products_id
								FROM	" . HelperFunctions::escapeSql(TABLE_ORDERS_PRODUCTS) . "
								WHERE	orders_id = '" . (int) $ordersId . "'");

        while ($rowProd = mysql_fetch_array($resProd)) {
            $itemId = $rowProd['products_id'];
            $resAttr = mysql_query("SELECT	products_options,
											products_options_values
									FROM	" . HelperFunctions::escapeSql(TABLE_ORDERS_PRODUCTS_ATTRIBUTES) . "
									WHERE	orders_id = '" . (int) $ordersId . "'
									AND		orders_products_id = '" . HelperFunctions::escapeSql($rowProd['orders_products_id']) . "'");

            if (mysql_num_rows($resAttr) >= 1) {
                while ($rowAttr = mysql_fetch_array($resAttr)) {
                    $resOpt = mysql_query(" SELECT	products_options_id
											FROM	" . HelperFunctions::escapeSql(TABLE_PRODUCTS_OPTIONS) . "
											WHERE	products_options_name = '" . HelperFunctions::escapeSql($rowAttr['products_options']) . "'
											AND		language_id = '" . HelperFunctions::escapeSql($lang) . "'");
                    $rowOpt = mysql_fetch_array($resOpt);
                    $resOptVal = mysql_query("	SELECT	products_options_values_id
												FROM	" . HelperFunctions::escapeSql(TABLE_PRODUCTS_OPTIONS_VALUES) . "
												WHERE	products_options_values_name = '" . HelperFunctions::escapeSql($rowAttr['products_options_values']) . "'
												AND		language_id = '" . HelperFunctions::escapeSql($lang) . "'");

                    while ($rowOptVal = mysql_fetch_array($resOptVal)) {
                        $resCheck = mysql_query("SELECT	products_options_values_to_products_options_id
												 FROM	" . HelperFunctions::escapeSql(TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS) . "
												 WHERE	products_options_id = '" . HelperFunctions::escapeSql($rowOpt['products_options_id']) . "'
												 AND	products_options_values_id = '" . HelperFunctions::escapeSql($rowOptVal['products_options_values_id']) . "'");

                        if (mysql_num_rows($resCheck) == 1) {
                            $itemId .= "{" . $rowOpt['products_options_id'] . "}" . $rowOptVal['products_options_values_id'];
                        }
                    }
                }
            }

            $itemId = HelperFunctions::sortSofortItemId($itemId);
            mysql_query("INSERT INTO sofort_products (orders_id, orders_products_id, item_id) VALUES ('" . (int) $ordersId . "','" . HelperFunctions::escapeSql($rowProd['orders_products_id']) . "','" . HelperFunctions::escapeSql($itemId) . "')");
        }

        return true;
    }

}

