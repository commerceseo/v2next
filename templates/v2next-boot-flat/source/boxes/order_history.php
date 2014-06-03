<?php
/*-----------------------------------------------------------------
* 	$Id: order_history.php 371 2013-06-10 12:44:12Z akausch $
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
 
if (isset($_SESSION['customer_id'])) {
	$box_smarty = new smarty;
	$box_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/'); 

	// include needed functions
	require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php');
	
	if (isset($_SESSION['customer_id'])) {
		// retreive the last x products purchased
		$orders_query = xtc_db_query("select distinct op.products_id from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_PRODUCTS . " p where o.customers_id = '" . (int)$_SESSION['customer_id'] . "' and o.orders_id = op.orders_id and op.products_id = p.products_id and p.products_status = '1' group by products_id order by o.date_purchased desc limit " . MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX);
		if (xtc_db_num_rows($orders_query)) {
			$product_ids = '';
			while ($orders = xtc_db_fetch_array($orders_query)) {
			$product_ids .= $orders['products_id'] . ',';
			}
			$product_ids = substr($product_ids, 0, -1);
			
			$customer_orders_string = '<table border="0" width="100%" cellspacing="0" cellpadding="1">';
			$products_query = xtc_db_query("select products_id, products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id in (" . $product_ids . ") and language_id = '" . (int)$_SESSION['languages_id'] . "' order by products_name");
			while ($products = xtc_db_fetch_array($products_query)) {
				$customer_orders_string .= '  <tr>' .
			                           '    <td class="infoBoxContents"><a href="' . xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($products['products_id'],$products['products_name'])) . '">' . $products['products_name'] . '</a></td>' .
			                           '    <td class="infoBoxContents" align="right" valign="top"><a href="' . xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('action')) . 'action=cust_order&pid=' . $products['products_id']) . '">' . xtc_image(DIR_WS_ICONS . 'cart.gif', ICON_CART) . '</a></td>' .
			                           '  </tr>';
			}
			$customer_orders_string .= '</table>';
		}
	}

	$box_smarty->assign('BOX_CONTENT', $customer_orders_string);
	
	$box_smarty->caching = false;
	$box_smarty->assign('language', $_SESSION['language']);
	$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
	$box_smarty->assign('box_name', getBoxName('order_history'));
	$box_smarty->assign('box_class_name', getBoxCSSName('order_history'));
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
}
