<?php

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category  PayIntelligent
 * @package   PayIntelligent_ClickandBuy
 * @copyright (C) 2010 PayIntelligent GmbH  <http://www.payintelligent.de/>
 * @license   http://www.gnu.org/licenses/  GNU General Public License 3
 */
/*
 * @todo boilerplate for shop specific functions
 */
require_once('pi_clickandbuy_constants.php');

class pi_clickandbuy_functions_shop {

    var $businessOriginId;

    /**
     * @todo insert right business origin id
     */
    function pi_clickandbuy_functions_shop() {
        $this->businessOriginId = 'xtcModified';
    }

    /**
     * @return string returns soap endpoint defaults to sandbox
     */
    function getSoapEndpoint() {
        require_once('includes/application_top.php');
        $paymentMethod = '';

        if (isset($_REQUEST['oID'])) {
            $query = xtc_db_query('SELECT payment_method FROM ' . TABLE_ORDERS . ' WHERE orders_id = "' . xtc_db_input($_REQUEST['oID']) . '"');
            $order = xtc_db_fetch_array($query);
            $paymentMethod = $order['payment_method'];
        } elseif (isset($_SESSION['payment'])) {
            $paymentMethod = $_SESSION['payment'];
        } else {
                $paymentMethod = 'pi_clickandbuy';
        }

        if ($paymentMethod == 'pi_clickandbuy') {
            require_once(DIR_FS_DOCUMENT_ROOT . 'includes/modules/payment/' . $paymentMethod . '.php');
            $cab = new pi_clickandbuy();
            if ($cab->sandbox == 'False') {
                return SOAP_ENDPOINT;
            } else {
                return SOAP_ENDPOINT_SANDBOX;
            }
        } elseif ($paymentMethod == 'pi_clickandbuy_recurring') {
            require_once(DIR_FS_DOCUMENT_ROOT . 'includes/modules/payment/' . $paymentMethod . '.php');
            $cab = new pi_clickandbuy_recurring();
            if ($cab->sandbox == 'False') {
                return SOAP_ENDPOINT;
            } else {
                return SOAP_ENDPOINT_SANDBOX;
            }
        } else {
            return SOAP_ENDPOINT;
        }
    }

    /**
     * Save the date which needed for token generating in the session
     */
    function saveTokenData($merchantId, $sharedSecretProject, $sharedSecretMms, $sharedSecretRegistration, $projectId) {
        require_once(DIR_FS_DOCUMENT_ROOT . 'includes/modules/payment/pi_clickandbuy.php');
        require_once(DIR_FS_DOCUMENT_ROOT . 'includes/modules/payment/pi_clickandbuy_recurring.php');
        $cab = new pi_clickandbuy();
        $cabRecurring = new pi_clickandbuy_recurring();

        if (empty($_SESSION['cab']['sharedSecretRegistration'])) {
            $_SESSION['cab']['sharedSecretRegistration'] = $sharedSecretRegistration;
            $_SESSION['cab']['merchantId'] = $merchantId;
        }

        if (empty($cab->merchantId)) {
            $sql = 'UPDATE ' . TABLE_CONFIGURATION . ' SET configuration_value = "' . $merchantId . '" WHERE configuration_key = "MODULE_PAYMENT_PI_CLICKANDBUY_MERCHANT_ID"';
            xtc_db_query($sql);

            $sql = 'UPDATE ' . TABLE_CONFIGURATION . ' SET configuration_value = "' . $projectId . '" WHERE configuration_key = "MODULE_PAYMENT_PI_CLICKANDBUY_PROJECT_ID"';
            xtc_db_query($sql);

            $sql = 'UPDATE ' . TABLE_CONFIGURATION . ' SET configuration_value = "' . $sharedSecretMms . '" WHERE configuration_key = "MODULE_PAYMENT_PI_CLICKANDBUY_SECRET_KEY_MMS"';
            xtc_db_query($sql);

            $sql = 'UPDATE ' . TABLE_CONFIGURATION . ' SET configuration_value = "' . $sharedSecretProject . '" WHERE configuration_key = "MODULE_PAYMENT_PI_CLICKANDBUY_SECRET_KEY"';
            xtc_db_query($sql);
        }

        if (empty($cabRecurring->merchantId)) {
            $sql = 'UPDATE ' . TABLE_CONFIGURATION . ' SET configuration_value = "' . $merchantId . '" WHERE configuration_key = "MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_ID"';
            xtc_db_query($sql);

            $sql = 'UPDATE ' . TABLE_CONFIGURATION . ' SET configuration_value = "' . $projectId . '" WHERE configuration_key = "MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_PROJECT_ID"';
            xtc_db_query($sql);

            $sql = 'UPDATE ' . TABLE_CONFIGURATION . ' SET configuration_value = "' . $sharedSecretMms . '" WHERE configuration_key = "MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_MMS"';
            xtc_db_query($sql);

            $sql = 'UPDATE ' . TABLE_CONFIGURATION . ' SET configuration_value = "' . $sharedSecretProject . '" WHERE configuration_key = "MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY"';
            xtc_db_query($sql);
        }
    }

    /**
     * Set the languange
     *
     * @param string $langCode
     * @todo auslagern da shop spezifisch
     */
    function setLanguangeSession($langCode) {
        $_SESSION['pi']['cab']['languange'] = $langCode;
    }

    function getBuisinessOriginId() {
        return $this->businessOriginId;
    }

}

?>
