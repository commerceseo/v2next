<?php

/**
 * This file takes hash as request and changes the status of the order
 */
include ('includes/application_top.php');

if (file_exists(DIR_WS_CLASSES . 'payment/class.secupay_api.php')) {
    require_once(DIR_WS_CLASSES . 'payment/class.secupay_api.php');
} else {
    require_once("../" . DIR_WS_CLASSES . 'payment/class.secupay_api.php');
}

try {

	include ('secupay_conf.php');

	$hash = xtc_db_input($_POST['hash']);
	$api_key = xtc_db_input($_POST['apikey']);
	$payment_status = xtc_db_input($_POST['payment_status']);
	$comment = xtc_db_input($_POST['hint']);
	$payment_type = xtc_db_input($_REQUEST['payment_type']);
	$referer = parse_url($_SERVER['HTTP_REFERER']);

	if (isset($referer['host']) && $referer['host'] === SECUPAY_HOST) {


		if (isset($api_key) && $api_key === MODULE_PAYMENT_SECUPAY_APIKEY) {
			// select from secupay_transaction_order table
			try {
				$order_nr_q = @xtc_db_query("SELECT ordernr FROM secupay_transaction_order WHERE `hash` = '{$hash}'");
				$order_nr_row = @xtc_db_fetch_array($order_nr_q);
				$order_id = $order_nr_row['ordernr'];
			} catch (Exception $e) {
				secupay_log(SECUPAY_PUSH_LOG, ' status_push - get ordernr - EXCEPTION: ' . $e->getMessage());			
			}

			if (!isset($order_id) || !is_numeric($order_id)) {
				$response = 'ack=Disapproved&error=no+matching+order+found+for+hash';
			} else {

				switch ($payment_type) {
					case "debit":
						$order_status_waiting = MODULE_PAYMENT_SPLS_ORDER_STATUS_ID;
						$order_status_accepted = MODULE_PAYMENT_SPLS_ORDER_STATUS_ACCEPTED_ID;
						$order_status_denied = MODULE_PAYMENT_SPLS_ORDER_STATUS_DENIED_ID;
						$order_status_issue = MODULE_PAYMENT_SPLS_ORDER_STATUS_ISSUE_ID;
						$order_status_void = MODULE_PAYMENT_SPLS_ORDER_STATUS_VOID_ID;
						$order_status_authorized = MODULE_PAYMENT_SPLS_ORDER_STATUS_AUTHORIZED_ID;
						break;
					case "creditcard":
						$order_status_waiting = MODULE_PAYMENT_SPKK_ORDER_STATUS_ID;
						$order_status_accepted = MODULE_PAYMENT_SPKK_ORDER_STATUS_ACCEPTED_ID;
						$order_status_denied = MODULE_PAYMENT_SPKK_ORDER_STATUS_DENIED_ID;
						$order_status_issue = MODULE_PAYMENT_SPKK_ORDER_STATUS_ISSUE_ID;
						$order_status_void = MODULE_PAYMENT_SPKK_ORDER_STATUS_VOID_ID;
						$order_status_authorized = MODULE_PAYMENT_SPKK_ORDER_STATUS_AUTHORIZED_ID;
						break;
					case "invoice":
						$order_status_waiting = MODULE_PAYMENT_SPINV_ORDER_STATUS_ID;
						$order_status_accepted = MODULE_PAYMENT_SPINV_ORDER_STATUS_ACCEPTED_ID;
						$order_status_denied = MODULE_PAYMENT_SPINV_ORDER_STATUS_DENIED_ID;
						$order_status_issue = MODULE_PAYMENT_SPINV_ORDER_STATUS_ISSUE_ID;
						$order_status_void = MODULE_PAYMENT_SPINV_ORDER_STATUS_VOID_ID;
						$order_status_authorized = MODULE_PAYMENT_SPINV_ORDER_STATUS_AUTHORIZED_ID;
						break;
					default :
						$response = 'ack=Disapproved&error=payment_type+not+supported';
						break;
				}

				if (isset($order_status_accepted)) {

					//get order status
					try {
						$status_query = @xtc_db_query("SELECT orders_status FROM " . TABLE_ORDERS . " WHERE `orders_id` = '{$order_id}'");
						$status_query_row = @xtc_db_fetch_array($status_query);
						$original_order_status_id = $status_query_row['orders_status'];
						
						secupay_log(SECUPAY_PUSH_LOG, ' status_push - original_order_status_id: ' . $original_order_status_id);
						
					} catch (Exception $e) {
						secupay_log(SECUPAY_PUSH_LOG, ' status_push - get orderstatus - EXCEPTION: ' . $e->getMessage());			
					}
				
					switch ($payment_status) {
						case 'accepted':
							if ($original_order_status_id !== $order_status_waiting) {
								//don't overwrite, order status changed from other source
								
								try {
									$orders_status_name_q = @xtc_db_query("SELECT orders_status_name FROM " . TABLE_ORDERS_STATUS . " WHERE orders_status_id = {$original_order_status_id} LIMIT 1");
									$orders_status_name_row = @xtc_db_fetch_array($orders_status_name_q);
									$orders_status_name = $orders_status_name_row['orders_status_name'];
								} catch (Exception $e) {
									secupay_log(SECUPAY_PUSH_LOG, ' status_push - get orderstatus name - EXCEPTION: ' . $e->getMessage());
									$orders_status_name = "unkown";
								}
								
								$response = 'ack=Disapproved&error=order+status+not+waiting&original_status_id='.$original_order_status_id.'&original_status='.$orders_status_name;
							} else {
								$order_status = $order_status_accepted;						
							}
							break;
						case 'denied':
							$order_status = $order_status_denied;
							break;
						case 'issue':
							$order_status = $order_status_issue;
							break;
						case 'void':
							$order_status = $order_status_void;
							break;
						case 'authorized':
                                                        $order_status = $order_status_authorized;
							break;
						default:
							$response = 'ack=Disapproved&error=payment_status+not+supported';
							break;
					}
				}
			}

			if (isset($order_status) && is_numeric($order_status) && $order_status != 0) {
				//update order status
				try {
					@xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status='" . xtc_db_input($order_status) . "' WHERE orders_id='" . xtc_db_input($order_id) . "'");

					$comment = "secupay status push (".$payment_status."): " . $comment;
					
					//update order status history
					@xtc_db_query("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, comments) VALUES (" . xtc_db_input($order_id) . ", " . xtc_db_input($order_status) . ", NOW(), '" . xtc_db_input($comment) . "') ");

					$response = 'ack=Approved';
				} catch (Exception $e) {
					secupay_log(SECUPAY_PUSH_LOG, ' status_push - get orderstatus - EXCEPTION: ' . $e->getMessage());
					$response = 'ack=Disapproved&error=order+status+not+changed';
				}
			} elseif (!isset($response)) {

				$response = 'ack=Disapproved&error=order+status+not+updated';
			}
		} else {

			$response = 'ack=Disapproved&error=apikey+invalid';
		}
	} else {
		secupay_log(SECUPAY_PUSH_LOG, ' status_push invalid Referer: ' . $_SERVER['HTTP_REFERER']);
		secupay_log(SECUPAY_PUSH_LOG, ' status_push invalid host: ' . $referer['host']);
		$response = 'ack=Disapproved&error=request+invalid';
	}
} catch (Exception $e) {
	$response = 'ack=Disapproved&error=unexpected+error';
	secupay_log(SECUPAY_PUSH_LOG, ' status_push EXCEPTION: ' . $e->getMessage());
}	
secupay_log(SECUPAY_PUSH_LOG, ' status_push RESPONSE: ' . $response . '&' . http_build_query($_POST));
//append original request (post) data to response
echo $response . '&' . http_build_query($_POST);