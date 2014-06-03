<?php
/*-----------------------------------------------------------------
* 	$Id: ot_gv.php 420 2013-06-19 18:04:39Z akausch $
* 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ------------------------------------------------------------------
* 	based on:
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/

class ot_gv {
	var $title, $output;

	function ot_gv() {
		global $xtPrice;
		$this->code = 'ot_gv';
		$this->header = MODULE_ORDER_TOTAL_GV_HEADER;
		$this->title = MODULE_ORDER_TOTAL_GV_TITLE;
		$this->description = MODULE_ORDER_TOTAL_GV_DESCRIPTION;
		$this->enabled = MODULE_ORDER_TOTAL_GV_STATUS;
		$this->sort_order = MODULE_ORDER_TOTAL_GV_SORT_ORDER;
		$this->include_shipping = MODULE_ORDER_TOTAL_GV_INC_SHIPPING;
		$this->calculate_tax = MODULE_ORDER_TOTAL_GV_CALC_TAX;
		$this->credit_class = true;
		$this->user_prompt = MODULE_ORDER_TOTAL_GV_USER_PROMPT;
		$this->checkbox = '<input type="checkbox" onclick="xajax_useGV(1)" name="' . 'c' . $this->code . '" id="' . 'c' . $this->code . '"/>';
		$this->output = array ();
		
		// MODUL DEAKTIVIEREN FALLS KUNDE KEINE PREISE SEHEN DARF
		if ($_SESSION['customers_status']['customers_status_show_price'] == 0) {
			$this->enabled = 'false';
		}		
		
	}

	// AUSGABE AUF CHECKOUT_CONFIRMATION
	function process() {
		global $order, $xtPrice;

		if ($_SESSION['cot_gv'] == true && $this->enabled == 'true') {

			// ENDPREIS FESTLEGEN
			$order_total = $order->info['total'];

			// WENN VERSANDKOSTEN NICHT INKLUSIVE, DANN VERSANDKOSTEN VON ENDPREIS ABZIEHEN
			if ($this->include_shipping == 'false') {
				$order_total = $order_total - $order->info['shipping_cost'];
			}

			// WENN STEUER NICHT INKLUSIVE UND KUNDE MIT NETTO PREISEN, DANN STEUER ZUR RABATT-SUMME ADDIEREN
		   	if ($this->calculate_tax == 'false' && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
				$order_total = $order_total + $order->info['tax'];
			}			

			// RABATT BERECHNEN
			$this->deduction = $this->calculate_credit($order_total);

			// STEUER BERECHNEN			
		   	if ($this->calculate_tax == 'true') {
				$tax_deduction = $this->calculate_tax_deduction($this->deduction, $order_total);
				$order->info['subtotal'] = round($order->info['subtotal'] - $this->deduction, 2);
			}

			// AUSLESEN DES GUTSCHEIN CODES
			$coupon_query = xtc_db_query("select c.coupon_code from " . TABLE_COUPONS . " c, " . TABLE_COUPON_REDEEM_TRACK . " crt where crt.customer_id = '" . (int) $_SESSION['customer_id'] . "' and c.coupon_id = crt.coupon_id and c.coupon_type = 'G' order by crt.redeem_date desc");
			$coupon = xtc_db_fetch_array($coupon_query);	  

			// AUSGABE
			if ($this->deduction > 0) {			
				$order->info['total'] = $order->info['total'] - round($this->deduction, 2);
				if ($order->info['total'] < 0.0049) {
					$order->info['total'] = 0.00; 
					$order->info['tax'] = 0.00; 
				}
				$this->output[] = array ('title' => $this->title . ' (' . $coupon['coupon_code'] .'):', 'text' => '<font color="#ff0000">-' . $xtPrice->xtcFormat($this->deduction, true).'</font>', 'value' => $xtPrice->xtcFormat($this->deduction, false));
			}
		}
	}

	// RABATT BERECHNEN
	function calculate_credit($amount) {
		global $order;
		
		// GUTHABEN DES KUNDEN AUSLESEN
		$gv_query = xtc_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . (int) $_SESSION['customer_id'] . "' limit 1");
		$gv_result = xtc_db_fetch_array($gv_query);
		$gv_amount = $gv_result['amount'];
		
		// ENDSUMME (ABZÜGLICH GUTHABEN) BERECHNEN
		$full_cost = $amount - $gv_amount;
		
		// WENN ENDSUMME KLEINER NULL, DANN RABATT GLEICH BESTELLSUMME
		if ($full_cost <= 0) {
			$gv_amount = $amount;
		}
		
		return $gv_amount;
	}
	// STEUER NEU BERECHNEN
	function calculate_tax_deduction($od_amount, $order_total) {
		global $order;
		$tax_deduction = 0.00;

		// SCHLEIFE FÜR STEUERKLASSEN
		reset($order->info['tax_groups']);
		while (list ($key, $value) = each($order->info['tax_groups'])) {
	
			// WENN ENDSUMME FAST NULL, DANN STEUER GLEICH NULL SETZEN
			if ($order->info['total'] - $od_amount <= 0.0049) {
				$order->info['tax_groups'][$key] = 0.00;
	
			// ANSONSTEN STEUER AUS RABATT BERECHNEN
			} else {
				$key_no_pre = str_replace(TAX_NO_TAX, "", $key);
				$key_no_pre = str_replace(TAX_ADD_TAX, "", $key_no_pre);
				
				// PROZENTUALER ANTEIL DIESER STEUERKLASSE VON DER RABATT-SUMME BERECHNEN
				$tax_rate = xtc_get_tax_rate_from_desc($key_no_pre);
				if ($tax_rate > 0) {
					if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
						$tax_group_order_total = ($order->info['tax_groups'][$key] * (100 + $tax_rate) / $tax_rate) - $order->info['tax_groups'][$key];
					} else {
						$tax_group_order_total = $order->info['tax_groups'][$key] * (100 + $tax_rate) / $tax_rate;
					}	
					$tax_group_order_total = $tax_group_order_total / $order_total;
				} else {
					$tax_group_order_total = 0.00;
				}
				$group_od_amount = $od_amount * $tax_group_order_total;
				if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
					$group_tax_deduction = ($group_od_amount * ($tax_rate / 100 + 1)) - $group_od_amount;
				} else {
					$group_tax_deduction = $group_od_amount - ($group_od_amount / (100 + $tax_rate) * 100);
				}

				$order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $group_tax_deduction;
				$order->info['tax'] = $order->info['tax'] - $group_tax_deduction;
				$tax_deduction += $group_tax_deduction;
			}
		}

		return $tax_deduction;
	}
	
	function pre_confirmation_check($order_total) {
		global $order;

		if ($_SESSION['cot_gv'] == true && $this->enabled == 'true') {
			$od_amount = $this->calculate_credit($order_total);
		}

		return $od_amount;
	}

	function use_credit_amount() {
		
		$gv_query = xtc_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . (int) $_SESSION['customer_id'] . "' limit 1");
		$gv_result = xtc_db_fetch_array($gv_query);
		if ($gv_result['amount'] > 0) {
			if ($_SESSION['cot_gv'] != true) {
				$_SESSION['cot_gv'] = false;
			}
			$use_credit_string = '<span id="gv_user_checkbox">' . $this->checkbox . '</span><span id="gv_user_prompt">' . $this->user_prompt . '</span>' . "\n";
			
			return $use_credit_string;
		}
	}

	function credit_selection() {
		$selection_string =  TEXT_ENTER_COUPON_CODE;
		$selection_string .= xtc_draw_input_field('gv_redeem_code');

		$selection_string = '';
		return $selection_string;

	}

	function apply_credit() {
		global $xtPrice;

		if ($_SESSION['cot_gv'] == true) {
			
			// ALTES GUTHABEN AUSLESEN
			$gv_query = xtc_db_query("SELECT amount FROM " . TABLE_COUPON_GV_CUSTOMER . " WHERE customer_id = '" . (int) $_SESSION['customer_id'] . "' LIMIT 1");
			$gv_result = xtc_db_fetch_array($gv_query);
				
			// NEUES GUTHABEN BERECHNEN	
			$gv_amount = $gv_result['amount'] - $xtPrice->xtcRemoveCurr($this->deduction);
			$gv_amount = str_replace(",", ".", $gv_amount);
			
			// NEUES GUTHABEN IN DATENBANK EINTRAGEN
			$gv_update = xtc_db_query("UPDATE " . TABLE_COUPON_GV_CUSTOMER . " SET amount = '" . $gv_amount . "' WHERE customer_id = '" . (int) $_SESSION['customer_id'] . "'");
		
		}
		return $this->deduction;
	}

	function update_credit_account($i) {
		global $order, $insert_id, $REMOTE_ADDR;
		if (preg_match('/^GIFT/', addslashes($order->products[$i]['model']))) {
			$gv_order_amount = ($order->products[$i]['final_price']);
			if ($this->credit_tax == 'true') {
				$gv_order_amount = $gv_order_amount * (100 + $order->products[$i]['tax']) / 100;
			}
			$gv_order_amount = $gv_order_amount * 100 / 100;
			if (MODULE_ORDER_TOTAL_GV_QUEUE == 'false') {
				$gv_query = xtc_db_query("SELECT amount FROM ".TABLE_COUPON_GV_CUSTOMER." WHERE customer_id = '".$_SESSION['customer_id']."'");
				$customer_gv = false;
				$total_gv_amount = 0;
				if ($gv_result = xtc_db_fetch_array($gv_query)) {
					$total_gv_amount = $gv_result['amount'];
					$customer_gv = true;
				}
				$total_gv_amount = $total_gv_amount + $gv_order_amount;
				if ($customer_gv) {
					$gv_update = xtc_db_query("UPDATE ".TABLE_COUPON_GV_CUSTOMER." SET amount = '".$total_gv_amount."' WHERE customer_id = '".$_SESSION['customer_id']."'");
				} else {
					$gv_insert = xtc_db_query("INSERT INTO ".TABLE_COUPON_GV_CUSTOMER." (customer_id, amount) VALUES ('".$_SESSION['customer_id']."', '".$total_gv_amount."')");
				}
			} else {
				$gv_insert = xtc_db_query("INSERT INTO ".TABLE_COUPON_GV_QUEUE." (customer_id, order_id, amount, date_created, ipaddr) VALUES ('".$_SESSION['customer_id']."', '".$insert_id."', '".$gv_order_amount."', NOW(), '".$REMOTE_ADDR."')");
			}
		}
	}


	function collect_posts() {
	
	}

	function check() {
		if (!isset ($this->check)) {
			$check_query = xtc_db_query("SELECT configuration_value from " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_ORDER_TOTAL_GV_STATUS'");
			$this->check = xtc_db_num_rows($check_query);
		}

		return $this->check;
	}

	function keys() {
		return array ('MODULE_ORDER_TOTAL_GV_STATUS', 'MODULE_ORDER_TOTAL_GV_SORT_ORDER', 'MODULE_ORDER_TOTAL_GV_INC_SHIPPING', 'MODULE_ORDER_TOTAL_GV_CALC_TAX');
	}

	function install() {
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('', 'MODULE_ORDER_TOTAL_GV_STATUS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('', 'MODULE_ORDER_TOTAL_GV_SORT_ORDER', '39', '6', '2', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) VALUES ('', 'MODULE_ORDER_TOTAL_GV_INC_SHIPPING', 'true', '6', '3', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) VALUES ('', 'MODULE_ORDER_TOTAL_GV_CALC_TAX', 'true', '6', '4', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
	}

	function remove() {
		xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}
	
}
?>