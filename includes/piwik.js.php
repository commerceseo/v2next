<?php
/*-----------------------------------------------------------------
* 	$Id: piwik.js.php 420 2013-06-19 18:04:39Z akausch $
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

$url = TRACKING_PIWIK_LOCAL_PATH;
$url = str_replace(array('http://', 'https://'), '', $url);
$url = trim($url);
if (TRACKING_PIWIK_LOCAL_SSL_PATH == '') {
	$sslurl = TRACKING_PIWIK_LOCAL_PATH;
} else {
	$sslurl = TRACKING_PIWIK_LOCAL_SSL_PATH;
}
$sslurl = str_replace(array('http://', 'https://'), '', $url);
$sslurl = trim($sslurl);
if (TRACKING_PIWIK_ID == '') {
	define(TRACKING_PIWIK_ID, '1');
}

if (PRODUCT_ID > 0 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
	// Produkt Details
	$products_id = PRODUCT_ID;
	$products_query = xtc_db_fetch_array(xtc_db_query("SELECT 
							p.products_id, 
							p.products_model, 
							p.products_price, 
							p.products_tax_class_id, 
							pd.products_name, 
							cd.categories_name 
							FROM 
								".TABLE_PRODUCTS." p 
							LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd ON(p.products_id = pd.products_id)
							LEFT JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." p2c ON(p2c.products_id = pd.products_id) 
							LEFT JOIN ".TABLE_CATEGORIES_DESCRIPTION." cd ON(p2c.categories_id = cd.categories_id) 
							WHERE 
								p.products_id = '".(int)PRODUCT_ID."' 
							AND 
								pd.language_id ='".(int)$_SESSION['languages_id']."' 
							AND 
								cd.language_id ='".(int)$_SESSION['languages_id']."'"));
	
	if ($products_query['products_model'] != '') {
		$pmodel = $products_query['products_model'];
	} else {
		$pmodel = $products_query['products_id'];
	}
	$products_price = $xtPrice->xtcGetPrice(PRODUCT_ID, $format = true, 1, $products_query['products_tax_class_id'], $products_query['products_price'], 1, '','info');
	$preis = $products_price['plain'];	
	$show .= 'piwikTracker.setEcommerceView("'.$pmodel.'","'.$products_query['products_name'].'","'.$products_query['categories_name'].'",'.$preis.');'."\n";
	
} elseif (CAT_ID > 0 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
	// Kategorie Ebene
	$catID = explode('_', CAT_ID);
	array_reverse($catID);
	$categories_query = xtc_db_fetch_array(xtc_db_query("SELECT categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = '".(int)$catID[0]."' AND language_id = '".(int)$_SESSION['languages_id']."'"));
	$show .= 'piwikTracker.setEcommerceView(productSku=false,productName=false,category="'.$categories_query['categories_name'].'");'."\n";
	
} elseif (strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
	// Warenkorb
	$products = $_SESSION['cart']->get_products();
	if ($_SESSION['cart']->count_contents() > 0) {
		for ($i=0, $n=sizeof($products); $i<$n; $i++) {
			$categories = xtc_db_fetch_array(xtc_db_query("SELECT 
											cd.categories_name 
										FROM 
											" . TABLE_CATEGORIES_DESCRIPTION ." cd 
										LEFT JOIN ". TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON(cd.categories_id = p2c.categories_id)
										WHERE 
											p2c.products_id = ".(int)$products[$i]['id']." 
										AND 
											cd.language_id ='".(int)$_SESSION['languages_id']."'"));

			if ($products[$i]['model'] == '') {
				$pmodel = (int)$products[$i]['id'];
			} else {
				$pmodel = $products[$i]['model'];
			}
			
			$show .= 'piwikTracker.addEcommerceItem("'.$pmodel.'","'.$products[$i]['name'].'","'.$categories['categories_name'].'",'.format_raw($products[$i]['final_price']).','.(int)$products[$i]['quantity'].');' . "\n";
		}
		$show .= 'piwikTracker.trackEcommerceCartUpdate(' . format_raw($_SESSION['cart']->show_total()) . ');' . "\n";
	}
	
} elseif ((strstr($_REQUEST['linkurl'], FILENAME_CHECKOUT_SUCCESS) || strstr($PHP_SELF, FILENAME_CHECKOUT_SUCCESS) || strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SUCCESS))) {
	// Bestellung abgeschlossen
	$order_query = xtc_db_query("SELECT orders_id FROM ". TABLE_ORDERS . " WHERE customers_id = '" . (int)$_SESSION['customer_id'] . "' ORDER BY date_purchased DESC LIMIT 1");

	if (xtc_db_num_rows($order_query) == 1) {
		$order = xtc_db_fetch_array($order_query);
		$totals = array();
		$order_totals_query = xtc_db_query("SELECT value, class FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . (int)$order['orders_id'] . "'");
		while ($order_totals = xtc_db_fetch_array($order_totals_query)) {
			$totals[$order_totals['class']] = $order_totals['value'];
		}
		$order_products_query = xtc_db_query("SELECT 
											op.products_id, 
											pd.products_name, 
											op.final_price, 
											op.products_quantity 
											FROM 
												" . TABLE_ORDERS_PRODUCTS . " op, 
												" . TABLE_PRODUCTS_DESCRIPTION . " pd, 
												" . TABLE_LANGUAGES . " l 
											WHERE 
												op.orders_id = '" . (int)$order['orders_id'] . "' 
											AND 
												op.products_id = pd.products_id 
											AND 
												l.code = '" . xtc_db_input(DEFAULT_LANGUAGE) . "' 
											AND 
												l.languages_id = pd.language_id");
		while ($order_products = xtc_db_fetch_array($order_products_query)) {
			$category_query = xtc_db_query("SELECT cd.categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " cd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_LANGUAGES . " l WHERE p2c.products_id = '" . (int)$order_products['products_id'] . "' AND p2c.categories_id = cd.categories_id AND l.code = '" . xtc_db_input(DEFAULT_LANGUAGE) . "' AND l.languages_id = cd.language_id LIMIT 1");
			$category = xtc_db_fetch_array($category_query);
			$products_query = xtc_db_fetch_array(xtc_db_query("SELECT 
							products_id, 
							products_model
							FROM 
								".TABLE_PRODUCTS."
							WHERE 
								products_id = '".(int)$order_products['products_id']."'"));
								
			if ($products_query['products_model'] == '') {
				$pmodel = (int)$order_products['products_id'];
			} else {
				$pmodel = $products_query['products_model'];
			}
			$show .= 'piwikTracker.addEcommerceItem("'.$pmodel.'","'.$order_products['products_name'].'","'.$category['categories_name'].'",'.format_raw($order_products['final_price']).','.(int)$order_products['products_quantity'].');' . "\n";
		}
		$show .= 'piwikTracker.trackEcommerceOrder("'.(int)$order['orders_id'].'",'.(isset($totals['ot_total']) ? format_raw($totals['ot_total']) : 0).','.(isset($totals['ot_subtotal']) ? format_raw($totals['ot_subtotal']) : 0).','.(isset($totals['ot_tax']) ? format_raw($totals['ot_tax']) : 0).','.(isset($totals['ot_shipping']) ? format_raw($totals['ot_shipping']) : 0).',false);' . "\n";            
	}
}

function format_raw($number) {      
	return number_format($number, 2, '.', '');
}
?>

<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://<?php echo $sslurl; ?>" : "http://<?php echo $url; ?>");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", <?php echo (int)TRACKING_PIWIK_ID; ?>);
<?php echo $show; ?>
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script>
<?php
if (TRACKING_PIWIK_LOCAL_SSL_PATH == '') {
?>
<noscript>
<img src="http://<?php echo $url; ?>piwik.php?idsite=<?php echo (int)TRACKING_PIWIK_ID; ?>&rec=1" style="border:0" alt="" />
</noscript>
<?php
} else {
?>
<noscript>
<img src="https://<?php echo $sslurl; ?>piwik.php?idsite=<?php echo (int)TRACKING_PIWIK_ID; ?>&rec=1" style="border:0" alt="" />
</noscript>
<?php
}
?>