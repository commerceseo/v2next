<?php
/* -----------------------------------------------------------------------------------------
   $Id: froogle.php 1188 2005-08-28 14:24:34Z matthias $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (invoice.php,v 1.6 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

define('MODULE_SPEDITION_TEXT_DESCRIPTION', 'Speditions Export für IDS ORDER (CSV)');
define('MODULE_SPEDITION_TEXT_TITLE', 'IDS ORDER Speditions Export CSV');
define('MODULE_SPEDITION_FILE_TITLE', '<hr noshade>Dateiname');
define('MODULE_SPEDITION_FILE_DESC', 'Geben Sie einen Dateinamen ein, falls die Exportadatei am Server gespeichert werden soll.<br>(Verzeichnis export/)');
define('MODULE_SPEDITION_STATUS_DESC', 'Modulstatus');
define('MODULE_SPEDITION_STATUS_TITLE', 'Status');
define('MODULE_SPEDITION_CURRENCY_TITLE', 'W&auml;hrung');
define('MODULE_SPEDITION_CURRENCY_DESC', 'Welche W&auml;hrung soll exportiert werden?');
define('EXPORT_YES', 'Nur Herunterladen');
define('EXPORT_NO', 'Am Server Speichern');
define('CURRENCY', '<hr noshade><b>W&auml;hrung:</b>');
define('CURRENCY_DESC', 'W&auml;hrung in der Exportdatei');
define('EXPORT', 'Bitte den Sicherungsprozess AUF KEINEN FALL unterbrechen. Dieser kann einige Minuten in Anspruch nehmen.');
define('EXPORT_TYPE', '<hr noshade><b>Speicherart:</b>');
define('EXPORT_STATUS_TYPE', '<hr noshade><b>Kundengruppe:</b>');
define('EXPORT_STATUS', 'Bitte w&auml;hlen Sie die Kundengruppe, die Basis f&uuml;r den Exportierten Preis bildet. (Falls Sie keine Kundengruppenpreise haben, w&auml;hlen Sie <i>Gast</i>):</b>');
define('ORDERS_STATUS', '<hr noshade><b>Bestellstatus:</b>');
define('ORDERS_STATUS_DESC', 'Bitte wählen Sie den Bestellstatus der Bestellungen, die Sie exportieren wollen:');
define('ORDERS_STATUS_NEW_DESC', 'Bitte wählen Sie den Bestellstatus, der nach dem Export für die jeweilige Bestellung gelten soll:');
define('DATE_FORMAT_EXPORT', '%d.%m.%Y'); // this is used for strftime()
define('PAKET', '<hr noshade><b>Paketgr&ouml;&szlig;e:</b><br><input type="text" name="paket" value="NP" size="4" />');
// include needed functions

class spedition {
	var $code, $title, $description, $enabled;

	function spedition() {
		global $order;

		$this->code = 'spedition';
		$this->title = MODULE_SPEDITION_TEXT_TITLE;
		$this->description = MODULE_SPEDITION_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_SPEDITION_SORT_ORDER;
		$this->enabled = ((MODULE_SPEDITION_STATUS == 'True') ? true : false);
		$this->CAT = array ();
		$this->PARENT = array ();

	}

	function process($file) {
		@ xtc_set_time_limit(0);
		if ($_POST['oders_status'] != null) {
			$orders_query_where = " WHERE orders_status='" . $_POST['oders_status'] . "'";
		}
		$schema = ' ';
		 $schema = 'ENAME1;ENAME2;ESTRASSE;ELAND;EPLZ;EORT;EKDNR;EMC;VNAME1;VNAME2;VSTRASSE;VLAND;VPLZ;VORT;VKDNR;VMC;FRANKATUR;NACHNAHME;WARENWERT;TERMINART;TERMINDATUM;LADEHILFANZ;LADEHILFTYP;HWTK2;WHWT1;WHWT2;SP1ANZAHL;SP1VERP;SP1INHALT;SP1GEWICHT;SP1MARK;HWTK2TEXT' . "\n";
		$orders_query = "SELECT orders_id,
														customers_id, 
														customers_telephone,
														customers_email_address,
														delivery_name,  		
														delivery_firstname, 
														delivery_lastname, 
														delivery_company, 
														delivery_street_address, 
														delivery_suburb, 
														delivery_city, 
														delivery_postcode, 
														delivery_state, 
														delivery_country, 
														delivery_country_iso_code_2, 
														payment_method, 
														comments, 
														date_purchased, 
														orders_status, 
														currency, 
														shipping_class 
														FROM orders "
														. $orders_query_where;

		$customers_query = xtc_db_query($orders_query);
		while ($customers = xtc_db_fetch_array($customers_query)) {
			$paket = trim($_POST['paket']);
			$order_value=" ";
			if ($customers[payment_method] == 'cod') {
				$cod_query = "SELECT text from orders_total where orders_id='" . $customers['orders_id'] . "' and class='ot_cod_fee'";
				$cod_query = xtc_db_query($cod_query);
				$cod_array = xtc_db_fetch_array($cod_query);
			  $order_value_query = "SELECT value FROM orders_total WHERE orders_id='".$customers['orders_id']."' AND class='ot_total'";
			  $order_value_query = xtc_db_query($order_value_query);
			  $order_value_array = xtc_db_fetch_array($order_value_query);
			  $order_value = number_format((double)$order_value_array['value'], 2);
			  $order_value = str_replace('.',',',$order_value);
			}
			$comment = preg_replace("/\s+/", " ", $customers['comments']);
			$cod_value = str_replace(" " . $customers['currency'], '', $cod_array['text']);
			
			/*				Schema IDS ORDER
							Name				: $customers['delivery_name'] . ";" .
							Name2				: $customers['delivery_company'] . ";" .
							Strasse				: $customers['delivery_street_address'] . ";" . 
							Land				: $customers['delivery_country'] . ";" .
							PLZ					: $customers['delivery_postcode'] . ";" .
							Ort					: $customers['delivery_city'] . ";" .
							Kundennummer		: $customers['customers_id'] . ";" .
							EMC					:		
							VNAME1				: Firma GmbH
							VNAME2				:
							VSTRASSE			: Firmen Str. 100
							VLAND				: Deutschland
							VPLZ				: 11111
							VORT				: Firmensitz
							VKDNR				:
							VMC					:
							FRANKATUR			:
							NACHNAHME			: 
							WARENWERT			: $order_value . ";" .
							TERMINART			:
							TERMINDATUM			:
							LADEHILFANZ			:
							LADEHILFTYP			:
							HWTK2				: -
							HWTK2TEXT			: $customers['comments'] . ";" .
							WHWT1				: $customers['customers_telephone'] . ";" .	
							WHWT2				: -
							SP1 ANZAHL			:
							SP1 VERP			:
							SP1 INHALT			:
							SP1 GEWICHT			:
							SP1MARK				:				
			*/
			$schema_entry =
			$customers['delivery_name'] . ";" .
			$customers['delivery_company'] . ";" .
			$customers['delivery_street_address'] . ";" .
			$customers['delivery_country_iso_code_2'] . ";" .
			$customers['delivery_postcode'] . ";" .
			$customers['delivery_city'] . ";" .
			$customers['customers_id'] . ";" .
			"" . ";" .
			"Hier_steht_der_Firmenname" . ";" .
			"" . ";" .
			"Firmenstrasse" . ";" .
			"DE" . ";" .
			"Hier_steht_die_PLZ" . ";" .
			"Hier_steht_der_Ort_der_Firma" . ";" .
			"" . ";" .
			"" . ";" .
			"30" . ";" .
			$order_value . ";" .
			"" . ";" .
			"" . ";" .
			"" . ";" .
			"" . ";" .
			"" . ";" .
			"" . ";" .
			$customers['customers_telephone'] . ";" .
			"" . ";" .
			"" . ";" .
			"" . ";" .
			"" . ";" .
			"" . ";" .
			"" . ";" .
			$comment . "\n";
			$schema .= $schema_entry;
		}

		if(empty($schema)) {
			$schema = ' ';
		}

		// create File
		$fp = fopen(DIR_FS_DOCUMENT_ROOT . 'export/' . $file, "w+");
		fputs($fp, $schema);
		fclose($fp);

		switch ($_POST['export']) {
			case 'yes' :
				// send File to Browser
				$extension = substr($file, -3);
				$fp = fopen(DIR_FS_DOCUMENT_ROOT . 'export/' . $file, "rb");
				$buffer = fread($fp, filesize(DIR_FS_DOCUMENT_ROOT . 'export/' . $file));
				fclose($fp);
				header('Content-type: application/x-octet-stream');
				header('Content-disposition: attachment; filename=' . $file);
				echo $buffer;
				if ($_POST['oders_status_new'] != null AND $_POST['oders_status'] != null) {
			$query = "UPDATE orders SET orders_status ='" . $_POST['oders_status_new'] . "' WHERE orders_status = '" . $_POST['oders_status'] . "'";
			xtc_db_query($query);
		}
				exit;

				break;
		}
		if ($_POST['oders_status_new'] != null AND $_POST['oders_status'] != null) {
			$query = "UPDATE orders SET orders_status ='" . $_POST['oders_status_new'] . "' WHERE orders_status = '" . $_POST['oders_status'] . "'";
			xtc_db_query($query);
		}
	}

	function display() {

		$customers_statuses_array = xtc_get_customers_statuses();

		// build Currency Select

		$orders_status_array = array (
			array (
				'id' => '',
				'text' => 'alle'
			)
		);
		$orders_status_query = xtc_db_query("select orders_status_name, orders_status_id from " . TABLE_ORDERS_STATUS . " WHERE language_id='2' order by orders_status_id");
		while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
			$orders_status_array[] = array (
				'id' => $orders_status['orders_status_id'],
				'text' => $orders_status['orders_status_name'],

				
			);
		}
		$orders_status_new_array = $orders_status_array;
		$orders_status_new_array[0]['text'] = 'nicht ändern';
		return array (
			'text' => ORDERS_STATUS . '<br>' .
			ORDERS_STATUS_DESC . '<br>' .
			xtc_draw_pull_down_menu('oders_status',
			$orders_status_array
		) . '<br><br>' .
		ORDERS_STATUS_NEW_DESC . '<br>' .
		xtc_draw_pull_down_menu('oders_status_new', $orders_status_new_array) . '<br>' .
		PAKET.'<br>'.
		EXPORT_TYPE . '<br>' .
		EXPORT . '<br>' .
		xtc_draw_radio_field('export', 'no', false) . EXPORT_NO . '<br>' .
		xtc_draw_radio_field('export', 'yes', true) . EXPORT_YES . '<br>' .
		'<br><div align="center">' . xtc_button(BUTTON_EXPORT) .
		xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=spedition')) .'</div>');

	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SPEDITION_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SPEDITION_FILE', 'spedition.txt',  '6', '1', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SPEDITION_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
	}

	function remove() {
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys() {
		return array (
			'MODULE_SPEDITION_STATUS',
			'MODULE_SPEDITION_FILE'
		);
	}

}
