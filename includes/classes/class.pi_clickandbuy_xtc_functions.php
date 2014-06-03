<?php

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category  PayIntelligent
 * @package   PayIntelligent_ClickandBuy
 * @copyright (C) 2010 PayIntelligent GmbH  <http://www.payintelligent.de/>
 * @license   GPLv2
 */
class pi_clickandbuy_xtc_functions {

    /**
     * Retrieve the configured authentication data
     * 
     * @param string $paymentType
     * @return array $authentication
     */
    function getCabSettings($paymentType) {
        $settings = array();
        $authentication = array();
        $settingsQuery = xtc_db_query("SELECT configuration_key,configuration_value FROM configuration WHERE configuration_key LIKE 'MODULE_PAYMENT_PI_CLICKANDBUY%'");
        if (xtc_db_num_rows($settingsQuery) > 0) {
            while ($row = xtc_db_fetch_array($settingsQuery)) {
                $settings[$row['configuration_key']] = $row['configuration_value'];
            }
            if ($paymentType == 'clickandbuy') {
                $authentication['merchantID'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_MERCHANT_ID'];
                $authentication['projectID'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_PROJECT_ID'];
                $authentication['secretKey'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_SECRET_KEY'];
            } else {
                $authentication['merchantID'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_ID'];
                $authentication['projectID'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_PROJECT_ID'];
                $authentication['secretKey'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY'];
                $authentication['recDescription'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DESCRIPTION'];
                $authentication['recNumberOfBillings'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_NUMBER_LIMIT'];
                $authentication['recAmount'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_AMOUNT'];
                $authentication['recCurrency'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CURRENCY'];
                $authentication['recDateLimit'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DATE_LIMIT'];
                $authentication['recRevokableByConsumer'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_REVOKABLE'];
                $authentication['initialAmount'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_INITIAL_AMOUNT'];
                $authentication['recUseCase'] = $settings['MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SPECIAL_CASE'];
            }
        }
        return $authentication;
    }

    /**
     * Generate a md5 hash from the given params
     * 
     * @param float $amount
     * @param string $currency
     * @param string $externalID
     * @param string $secretKey
     * @param string $additional_1
     * @param string $additional_2
     * @return string $shash
     */
    function generateSHash($amount, $currency, $externalID, $secretKey, $additional_1 = '', $additional_2 = '') {
        $shash = md5($amount . $currency . $externalID . $secretKey . $additional_1 . $additional_2);
        return $shash;
    }

    /**
     * Retrieve a item list for a partial deliver specialuse case
     * 
     * @return array $items
     */
    function getItemsPartialDelivery() {
        global $order, $partialDeliveryAmount, $basketAmount, $currency, $initialAmountZero, $shippingCostsText;

        require_once (DIR_FS_INC . 'xtc_get_products_stock.inc.php');

        $itemStock = false;
        $items = array();
        $countPartialDelivery = 0;

        //Prepare items from Order Details
        $i = 0;
        foreach ($order->products as $key => $value) {
            $item = 'item' . $i;
            $itemID = $value['id'];
            $itemDescription = $value['name'];
            $itemUnitPriceAmount = $value['price'];
            $itemTotalPriceAmount = $value['final_price'];
            $itemQuantity = $value['qty'];

            $stockLeft = xtc_get_products_stock($itemID) - $itemQuantity;
            $outOfStock = false;

            if ($stockLeft < 0) {
                $outOfStock = true;
            }

            if (!empty($outOfStock)) {
                $partialDeliveryAmount = number_format($partialDeliveryAmount + ($itemUnitPriceAmount * $itemQuantity), 2);

                $items[$i]['itemType'] = $item . 'Text';
                $items[$i]['textItemDescription'] = utf8_encode('Partial Delivery: (' . $itemQuantity . ' x ' . number_format($itemUnitPriceAmount, 2) . ' ' . $currency . ') ' . $itemDescription);
                $countPartialDelivery++;
            } else {
                $itemStock = true;
                $items[$i]['itemType'] = $item . 'Item';
                $items[$i]['itemDescription'] = utf8_encode($itemDescription);
                $items[$i]['itemQuantity'] = $itemQuantity;
                $items[$i]['itemUnitPriceAmount'] = number_format($itemUnitPriceAmount, 2);
                $items[$i]['itemUnitPriceCurrency'] = $currency;
                $items[$i]['itemTotalPriceAmount'] = number_format($itemTotalPriceAmount, 2);
                $items[$i]['itemTotalPriceCurrency'] = $currency;
            }
            $i++;
        }

        if ($i == $countPartialDelivery) {
            $initialAmountZero = true;
        }

        if ($countPartialDelivery == 0) {
            $partialDeliveryAmount = '0.00';
        }

        if ($itemStock == false) {
            $partialDeliveryAmount = $basketAmount;
        } else {
            if (empty($order->info['shipping_cost']) || $order->info['shipping_cost'] == 0) {
                $shippingCost = '0.00';
            } else {
                $shipping_tax_rate = $this->get_shipping_tax_rate($order);

                //Shop has tax class in shipping module add shipping tax to order total 
                if ($shipping_tax_rate > 0) {
                    $shipping_tax = round(($order->info ['shipping_cost'] / 100) * $shipping_tax_rate, 2);
                    $shippingCost = ($order->info['shipping_cost'] + $shipping_tax);
                } else {
                    $shippingCost = $order->info['shipping_cost'];
                }
            }

            $item = 'item' . $i;
            $itemType = $item . 'Item';
            $items[$i]['itemType'] = $itemType;
            $items[$i]['itemDescription'] = $shippingCostsText;
            $items[$i]['itemQuantity'] = 1;
            $items[$i]['itemUnitPriceAmount'] = number_format($shippingCost, 2, '.', '');
            $items[$i]['itemUnitPriceCurrency'] = $currency;
            $items[$i]['itemTotalPriceAmount'] = number_format($shippingCost, 2, '.', '');
            $items[$i]['itemTotalPriceCurrency'] = $currency;
        }
        return $items;
    }

    /**
     * Retrieve the lang code form the consumer
     * 
     * @return string langcode 
     */
    function getConsumerLanguage() {
        global $languages_id;
        $query = xtc_db_query("SELECT code FROM " . TABLE_LANGUAGES . " WHERE languages_id = '" . intval($languages_id) . "'");
        $languageCode = xtc_db_num_rows($query) ? xtc_db_fetch_array($query) : '';
        return $languageCode['code'];
    }

    /**
     * Retrieve all order items
     * 
     * @param string $currency
     * @return array $items 
     */
    function getItems($currency) {
        global $shippingCostsText;
        $order = unserialize($_SESSION['pi']['order']);
        $items = array();
        $i = 1;

        foreach ($order->products as $key => $value) {
            $item = 'item' . $i;
            $itemType = $item . 'Item';
            $items[$i]['itemType'] = $itemType;
            $itemUnitPriceAmount = $value['price'];
            $itemTotalPriceAmount = $value['final_price'];
            $itemQuantity = $value['qty'];

            $items[$i]['itemDescription'] = str_replace('&#180;', '´', utf8_encode($value['name']));
            $items[$i]['itemQuantity'] = $itemQuantity;
            $items[$i]['itemUnitPriceAmount'] = number_format($itemUnitPriceAmount, 2, '.', '');
            $items[$i]['itemUnitPriceCurrency'] = $currency;
            $items[$i]['itemTotalPriceAmount'] = number_format($itemTotalPriceAmount, 2, '.', '');
            $items[$i]['itemTotalPriceCurrency'] = $currency;
            $i++;
        }

        if (empty($order->info['shipping_cost']) || $order->info['shipping_cost'] == 0) {
            $shippingCost = '0.00';
        } else {
            $shipping_tax_rate = $this->get_shipping_tax_rate($order);

            //Shop has tax class in shipping module add shipping tax to order total 
            if ($shipping_tax_rate > 0) {
                $shipping_tax = round(($order->info ['shipping_cost'] / 100) * $shipping_tax_rate, 2);
                $shippingCost = ($order->info['shipping_cost'] + $shipping_tax);
            } else {
                $shippingCost = $order->info['shipping_cost'];
            }
        }

        $item = 'item' . $i;
        $itemType = $item . 'Item';
        $items[$i]['itemType'] = $itemType;
        $items[$i]['itemDescription'] = $shippingCostsText;
        $items[$i]['itemQuantity'] = 1;
        $items[$i]['itemUnitPriceAmount'] = number_format($shippingCost, 2, '.', '');
        $items[$i]['itemUnitPriceCurrency'] = $currency;
        $items[$i]['itemTotalPriceAmount'] = number_format($shippingCost, 2, '.', '');
        $items[$i]['itemTotalPriceCurrency'] = $currency;

        if (array_key_exists('deduction', $order->info)) {
            $i++;
            $item = 'item' . $i;
            $itemType = $item . 'Item';
            $items[$i]['itemType'] = $itemType;
            $items[$i]['itemDescription'] = 'DISCOUNT';
            $items[$i]['itemQuantity'] = 1;
            $items[$i]['itemUnitPriceAmount'] = number_format($order->info['deduction'], 2, '.', '');
            $items[$i]['itemUnitPriceCurrency'] = $currency;
            $items[$i]['itemTotalPriceAmount'] = number_format($order->info['deduction'], 2, '.', '');
            $items[$i]['itemTotalPriceCurrency'] = $currency;
        } elseif (array_key_exists('reduction', $order->info)) {
            $i++;
            $item = 'item' . $i;
            $itemType = $item . 'Item';
            $items[$i]['itemType'] = $itemType;
            $items[$i]['itemDescription'] = 'DISCOUNT';
            $items[$i]['itemQuantity'] = 1;
            $items[$i]['itemUnitPriceAmount'] = number_format($order->info['reduction'], 2, '.', '');
            $items[$i]['itemUnitPriceCurrency'] = $currency;
            $items[$i]['itemTotalPriceAmount'] = number_format($order->info['reduction'], 2, '.', '');
            $items[$i]['itemTotalPriceCurrency'] = $currency;
        }

        return $items;
    }

    /**
     * Error Redirect
     * 
     * @param array $requestResult 
     */
    function redirectError($requestResult) {
        if ($requestResult['error_type'] == 'fault') {
            $errDescription = $requestResult['values']['detail']['errorDetails']['description'];
        } else {
            $errDescription = CLICKANDBUY_ERROR_MESSAGE_6;
        }
        $errorMessage = CLICKANDBUY_ERROR_MESSAGE_2 . $errDescription . ' ' . CLICKANDBUY_ERROR_MESSAGE_3;
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '&error_message=' . $errorMessage, 'SSL'));
    }

    /**
     * Retrieve the given error message rendered in html
     * 
     * @param string $message
     * @return string html
     */
    function showMessageError($message) {
        $message = '<table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                        <td class="messageStackError"><img border="0" title="" alt="" src="images/icons/error.gif">' . $message . '</td>
                    </tr>
                </table>';
        return $message;
    }

    /**
     * Retrieve the given success message rendered in html
     * 
     * @param string $message
     * @return string 
     */
    function showMessageSuccess($message) {
        $message = '<table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                            <td class="messageStackSuccess"><img border="0" title="" alt="" src="images/icons/success.gif">' . $message . '</td>
                        </tr>
                    </table>';
        return $message;
    }

    /**
     * Retrieve the shipping tax rate
     * 
     * @param order $order
     * @return float order 
     */
    function get_shipping_tax_rate($order) {
        //Class name from shipping
        $shipping_class_array = explode("_", $order->info['shipping_class']);
        $shipping_class = strtoupper($shipping_class_array[0]);
        if (empty($shipping_class)) {
            $shipping_tax_rate = 0;
        } else {
            $const = 'MODULE_SHIPPING_' . $shipping_class . '_TAX_CLASS';
            //Shipping tax rate
            if (defined($const)) {
                $shipping_tax_rate = xtc_get_tax_rate(constant($const));
            } else {
                $shipping_tax_rate = 0;
            }
        }

        return $shipping_tax_rate;
    }

}

