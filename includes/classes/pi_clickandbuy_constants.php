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

//ClickandBuy Test Server
define('SOAP_ENDPOINT_SANDBOX', 'https://api.clickandbuy-s1.com/webservices/soap/pay_1_1_0');
// ClickandBuy Live Server
define('SOAP_ENDPOINT', 'https://api.clickandbuy.com/webservices/soap/pay_1_1_0');

define('SOAP_NAMESPACE', "http://api.clickandbuy.com/webservices/pay_1_1_0/\" xmlns=\"http://api.clickandbuy.com/webservices/pay_1_1_0/");
define('SOAP_ACTION', 'http://api.clickandbuy.com/webservices/pay_1_1_0/');

//Activate/deactivate mms.log with true/false
define('MMS_LOG', false);

//Email where the mms should be send
define('MMS_EMAIL_TO', '');

//Email from your server e.g. info@your-domain.com
define('MMS_EMAIL_FROM', '');

// Path to NUSOAP
define('NUSOAP_FOLDER', DIR_FS_DOCUMENT_ROOT . 'ext/modules/payment/pi_clickandbuy/lib/');

// cancel modes
define('CANCEL_MODE_TX', 'TX');
define('CANCEL_MODE_RPA', 'RPA');
define('CANCEL_MODE_BOTH', 'BOTH');
?>