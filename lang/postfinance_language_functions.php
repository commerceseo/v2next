<?php 
/**
  * xt:Commerce v 3.x PostFinance  Zahlungs-Modul
 *  
 * @author             customweb GmbH
 * @url                http://www.customweb.com
 */


if (!function_exists('applyTransactionId')) {
	function applyTransactionId($languageString) {
		$transactionId = null;
		if (isOrderViewPage()) {
			$transactionId = getTransactionIdByOrderId($_GET['oID']);
		}

		if (!empty($transactionId)) {
			return $languageString .' [Callback ID: ' . $transactionId . ']';
		}
		else {
			return $languageString;
		}
	}
}


if (!function_exists('isOrderViewPage')) {
	function isOrderViewPage() {
		return isset($_GET['oID']) && (
				strstr($_SERVER['REQUEST_URI'], 'orders.php') ||
				strstr($_SERVER['REQUEST_URI'], 'print_order.php') ||
				strstr($_SERVER['REQUEST_URI'], 'gm_pdf_order.php')
		);
	}
}


if (!function_exists('getTransactionIdByOrderId')) {
	function getTransactionIdByOrderId($orderId) {
		$rs = xtc_db_query('SELECT callback_id FROM payment_callbacks WHERE orders_id = ' . (int)$orderId);
		if ($row = xtc_db_fetch_array($rs)) {
			return $row['callback_id'];
		}
		else {
			return null;
		}
	}
}

if (!function_exists('getLanguages')) {
	function getLanguages() {
		$rs = xtc_db_query('SELECT code,name FROM ' . TABLE_LANGUAGES . ' ORDER BY code');
		$languages = array();
		while ($row = xtc_db_fetch_array($rs)) {
			$languages[$row['code']] = $row['name'];
		}
		return $languages;
	}

}