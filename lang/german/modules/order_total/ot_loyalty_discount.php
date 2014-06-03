<?php
/*
  $Id: ot_loyalty_discount.php 420 2013-06-19 18:04:39Z akausch $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TITLE', 'Treue Rabatt');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_DESCRIPTION', 'Rabatt für Wiederkehrende Kunden');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_SPENT', 'Sie haben für ');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_LAST', ' im letzten ');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_WITHUS', ' bei uns bestellt ');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_QUALIFY', ' und erhalten deshalb einen Treue Rabatt von ');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_YEAR', 'Jahr bei uns bestellt');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_MONTH', 'Monat bei uns bestellt');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_QUARTER', 'Quartal bei uns bestellt');


  
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_STATUS_TITLE','Anzeige');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_STATUS_DESC', 'Sie moechten die Anzeige aktivieren?');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_SORT_ORDER_TITLE',      'Sortierreihenfolge');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_SORT_ORDER_DESC',       'Anzeigereihenfolge.');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_SHIPPING_TITLE',    'Versand einschliessen');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_SHIPPING_DESC',     'Die Versandkosten in die Kalkulation einbeziehen?');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_TAX_TITLE',         'Steuer einbeziehen');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_TAX_DESC','Steuer einbeziehen in die Berechnung.');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CALC_TAX_TITLE',        'Steuer neu berechnen');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CALC_TAX_DESC',         'Steuer neu berechnen f&uuml;r den herabgesetzten Endbetrag');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CUMORDER_PERIOD_TITLE', 'Zeitperiode');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CUMORDER_PERIOD_DESC',  'Setzen Sie die Zeitperiode deren Bestellungen zur Errechnung eines Rabattes herangezogen werden.');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TABLE_TITLE', 'Rabatt Prozentwerte');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TABLE_DESC',  'Setzen Sie die Bestellwerte und die Rabattprozentwerte (z.B. ab 400 5%, ab 800 10% => 400:5,800:10 usw.)');
  
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_ORDER_STATUS_CONSIDER_TITLE',   'Bestellstatus');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_ORDER_STATUS_CONSIDER_DESC',    'Welcher Bestellstastus soll berücksichtigt werden? Komma separierte Liste: (leer=jeder Status) <br><br>'.xtc_order_statuses_infolist() );
  
function xtc_order_statuses_infolist() {

	$orders_status_array = array ();
	$orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$_SESSION['languages_id']."' order by orders_status_id");
	while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
		$orders_status_array[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
	}

  foreach( $orders_status_array as $orders_status ) {
    $ret .= $orders_status['id'].'='.$orders_status['text']."<br>\n";  
  }
  return $ret;
}


?>