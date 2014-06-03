<?php
/*
  $Id: ot_loyalty_discount.php 420 2013-06-19 18:04:39Z akausch $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TITLE', 'Customer Loyalty Discount');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_DESCRIPTION', 'Customer Loyalty Discount');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_SPENT', 'You have spent ');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_LAST', ' in the last ');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_WITHUS', ' with us ');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_QUALIFY', ' so qualify for a discount of ');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_YEAR', 'year');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_MONTH', 'month');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_QUARTER', 'quarter');
  
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_STATUS_TITLE',          'Display Total');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_STATUS_DESC',           'Do you want to enable the Order Discount?');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_SORT_ORDER_TITLE',      'Sort Order');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_SORT_ORDER_DESC',       'Sort order of display.');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_SHIPPING_TITLE',    'Include Shipping');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_SHIPPING_DESC',     'Include Shipping in calculation');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_TAX_TITLE',         'Include Tax');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_TAX_DESC',          'Include Tax in calculation.');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CALC_TAX_TITLE',        'Calculate Tax');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CALC_TAX_DESC',         'Re-calculate Tax on discounted amount.');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CUMORDER_PERIOD_TITLE', 'Cumulative order total period');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CUMORDER_PERIOD_DESC',  'Set the period over which to calculate cumulative order total.');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TABLE_TITLE',           'Discount Percentage');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TABLE_DESC',            'Set the cumulative order total breaks per period set above, and discount percentages');

  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_ORDER_STATUS_CONSIDER_TITLE',   'Orderstatus');
  define('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_ORDER_STATUS_CONSIDER_DESC',    'Which Orderstatus should be considered? Comma separated list: (empty=every Status)<br><br>'.xtc_order_statuses_infolist() );
  
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