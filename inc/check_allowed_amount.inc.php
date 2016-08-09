<?php

/* -----------------------------------------------------------------------------------------
   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   $Id: check_allowed_amount.inc.php 975 2006-10-03 12:49:19Z mz $
   3rd-party contribution: free shipping of selected products
   http://www.get-attention.de
   (c) Thorsten Reineke, 2006
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
// checks the allowed cart-amount of a product.

function check_allowed_amount($pID, $qty) {
	$pID = (int) $pID;
	$qty = (int) $qty;
	$check_product = xtc_db_fetch_array(xtDBquery("SELECT free_shipping, max_free_shipping_cart FROM ".TABLE_PRODUCTS." WHERE products_id = '".$pID."';"));
	if (($check_product['max_free_shipping_cart'] > 0) && ($qty > $check_product['max_free_shipping_cart']) ){
		$_SESSION['MAXIMUM_CART_AMOUNT'] = true;
		return $check_product['max_free_shipping_cart'];
	} else {
		return $qty;
	}
}
