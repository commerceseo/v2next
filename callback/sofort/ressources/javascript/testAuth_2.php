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
 * $Id: testAuth_2.php 420 2013-06-19 18:04:39Z akausch $
 */
chdir('../../../../');
require_once('includes/application_top.php');
require_once(DIR_FS_CATALOG.'callback/sofort/helperFunctions.php');

$language = HelperFunctions::getSofortLanguage($_SESSION['language']);
include_once(DIR_FS_CATALOG.'lang/'.$language.'/modules/payment/sofort_general.php');

if ($_SESSION['customers_status']['customers_status_id'] == '0') {
	ob_start();
	require_once(dirname(__FILE__).'/../../library/sofortLib.php');
	
	preg_match('#([0-9]{4,6}\:[0-9]{4,6}\:[a-z0-9]{32})#', $_POST['k'], $matches);
	$configKey = $matches[1];
	$SofortLib_TransactionData = new SofortLib_TransactionData($configKey);
	$SofortLib_TransactionData->setTransaction('00000')->sendRequest();
	
	if ($SofortLib_TransactionData->isError()) {
		xtc_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value = '".MODULE_PAYMENT_SOFORT_KEYTEST_ERROR_DESC."' WHERE configuration_key = 'MODULE_PAYMENT_SOFORT_MULTIPAY_AUTH'");
		xtc_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value = '' WHERE configuration_key = 'MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY'");
		ob_end_clean();
		echo "f".MODULE_PAYMENT_SOFORT_KEYTEST_ERROR;
	} else {
		xtc_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value = '".MODULE_PAYMENT_SOFORT_KEYTEST_SUCCESS_DESC." ".date("d.m.Y")."' WHERE configuration_key = 'MODULE_PAYMENT_SOFORT_MULTIPAY_AUTH'");
		xtc_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value = '".HelperFunctions::escapeSql($configKey)."' WHERE configuration_key = 'MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY'");
		ob_end_clean();
		echo "t".MODULE_PAYMENT_SOFORT_KEYTEST_SUCCESS;
	}
}