<?php

/* -----------------------------------------------------------------
 * 	$Id: specials_gratis.inc.php 1081 2014-05-28 12:11:50Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	S.Bräutigam www.indiv-style.de
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

function getspecial_gratis() {
    global $currencies;
    $cartqyt = $_SESSION['wk_summe'];
    $cartgratis = $_SESSION['cart']->total;
    $meine_gratis = array();
    $special_gratis = array();
    $cart_cat = array();
    $cart_man = array();
    $product_id_arr = array();

    $config_query = xtc_db_query("SELECT 
									p.products_id,
									ct.categories_id AS cat_id,
									p.products_image,
									p.manufacturers_id AS manufac_id,
									p.products_price,
									s.* 
								FROM
									" . TABLE_PRODUCTS . " p
								INNER JOIN
									" . TABLE_PRODUCTS_TO_CATEGORIES . " AS ct ON(ct.products_id = p.products_id)
								INNER JOIN
									" . TABLE_SPECIALS_GRATIS . " AS s ON(s.products_id = p.products_id)
								WHERE 
									s.status = 1
								AND 
									s.specials_gratis_quantity > 0;");
    while ($maingratis = xtc_db_fetch_array($config_query)) {
        $meine_gratis[$maingratis["products_id"]] = $maingratis;
    }

    if (count($meine_gratis) > 0) {
        $cart_contents = $_SESSION['cart']->contents;
        if (is_array($cart_contents)) {
            reset($cart_contents);
            while (list ($products_id, ) = each($cart_contents)) {
                $product_id_arr[$products_id] = xtc_get_prid($products_id);
            }
        }
    }

    if (count($meine_gratis) > 0) {
        $gratis_query = xtc_db_query("SELECT 
										p.products_id,
										p.products_model,
										ct.categories_id AS cat_id,
										pd.products_name,
										p.products_image,
										p.manufacturers_id AS manufac_id,
										p.products_price,
										sd.*,
										s.* 
									FROM
										" . TABLE_PRODUCTS . " AS p
									INNER JOIN
										" . TABLE_SPECIALS_GRATIS . " AS s ON(s.products_id = p.products_id)
									INNER JOIN
										" . TABLE_SPECIALS_GRATIS_DESCRIPTION . " AS sd ON(sd.specials_gratis_id = s.specials_gratis_id AND sd.language_id = '" . (int)$_SESSION['languages_id'] . "')
									INNER JOIN
										" . TABLE_PRODUCTS_DESCRIPTION . " pd ON(s.products_id = pd.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "')
									INNER JOIN
										" . TABLE_PRODUCTS_TO_CATEGORIES . " ct ON(s.products_id = ct.products_id)
									WHERE 
										s.status = 1
									AND 
										s.specials_gratis_quantity > 0
									AND 
										s.specials_gratis_min_price <= $cartgratis
									AND 
										s.specials_gratis_ab_value <= $cartqyt;");
										

        while ($special_gratis_ob = xtc_db_fetch_array($gratis_query)) {
            foreach ($product_id_arr as $key => $value) {
                $cart_cat = xtc_db_fetch_array(xtc_db_query("SELECT categories_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = '$value';"));
                $cart_man = xtc_db_fetch_array(xtc_db_query("SELECT manufacturers_id FROM " . TABLE_PRODUCTS . " WHERE products_id = '$value';"));
				$xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
				$special_gratis_ob['specials_gratis_new_products_price'] = $xtPrice->xtcFormat(0, true);

                if (($cart_cat['categories_id'] == $special_gratis_ob['categories_id'] || $special_gratis_ob['categories_id'] == 0) && ($cart_man['manufacturers_id'] == $special_gratis_ob['manufacturers_id'] || $special_gratis_ob['manufacturers_id'] == 0)) {
                    $special_gratis_ob['product_link'] = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($special_gratis_ob[products_id], $special_gratis_ob['products_name']));
                    if ($special_gratis_ob['products_image'] != '') {
						$special_gratis_ob['gratis_image'] = xtc_image(DIR_WS_MINI_IMAGES . $special_gratis_ob['products_image'], $special_gratis_ob['products_name']);
					} else {
						$special_gratis_ob['gratis_image'] = xtc_image(DIR_WS_MINI_IMAGES . 'no_img.jpg', $special_gratis_ob['products_name']);
					}
					if (GRATISARTIKEL_OPTION == 'select') {
						$special_gratis_ob['select'] = '<input type="checkbox" id="prodg_'.$special_gratis_ob['products_id'].'" class="gratisChecked" />';
					} else {
						$special_gratis_ob['select'] = '<input type="radio" name="id" value="'.$special_gratis_ob['products_id'].'" id="prodg_'.$special_gratis_ob['products_id'].'" class="gratisChecked" />';
					}
                    $special_gratis[$special_gratis_ob['products_id']] = $special_gratis_ob;
                }
            }
        }
    }
    return $special_gratis;
}
