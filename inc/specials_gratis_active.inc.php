<?php

/* -----------------------------------------------------------------
 * 	$Id: specials_gratis_active.inc.php 1055 2014-05-16 15:58:42Z akausch $
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

function getspecial_gratis_active() {

    global $currencies;
    $acartqyt = $_SESSION['wk_summe'];
    $acartgratis = $_SESSION['cart']->total;
    $ameine_gratis = array();
    $ameine_gratis = '';
    $aspecial_gratis = array();
    $aspecial_gratis = '';
    $gratisart = $_SESSION['gratisart'];

    $aconfig_query = xtc_db_query("SELECT 
										p.products_id,
										ct.categories_id AS cat_id,
										p.products_image,
										p.manufacturers_id AS manufac_id,
										p.products_price,
										s.* 
									FROM
										" . TABLE_PRODUCTS . " AS p
									INNER JOIN
										" . TABLE_SPECIALS_GRATIS . " AS s ON(s.products_id = p.products_id)
									INNER JOIN
										" . TABLE_PRODUCTS_TO_CATEGORIES . " AS ct ON(s.products_id = ct.products_id)
									WHERE 
										s.status = 1
									AND 
										s.specials_gratis_quantity > 0;");

    while ($amaingratis = xtc_db_fetch_array($aconfig_query)) {
        if (isset($_SESSION['gratisart'][$amaingratis["products_id"]])) {
            $ameine_gratis[$amaingratis["products_id"]] = $amaingratis;
        }
    };

    if (count($ameine_gratis) > 0) {
        $agratis_query = xtc_db_query("SELECT 
                                            p.products_id,
                                            p.products_model,
                                            ct.categories_id AS cat_id,
                                            pd.products_name,
                                            p.products_image,
                                            p.manufacturers_id AS manufac_id,
                                            p.products_price,
                                            s.* 
										FROM
                                            " . TABLE_PRODUCTS . " AS p
										INNER JOIN
                                            " . TABLE_SPECIALS_GRATIS . " AS s ON(s.products_id = p.products_id)
										INNER JOIN
                                            " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON(s.products_id = pd.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "')
										INNER JOIN
                                            " . TABLE_PRODUCTS_TO_CATEGORIES . " AS ct ON(s.products_id = ct.products_id)
										WHERE 
											s.status = 1
										AND 
											s.specials_gratis_quantity > 0
										AND 
											s.specials_gratis_min_price <= $acartgratis
										AND 
											s.specials_gratis_ab_value <= $acartqyt;");
        while ($aspecial_gratis_ob = xtc_db_fetch_array($agratis_query)) {
            if (isset($_SESSION['gratisart'][$aspecial_gratis_ob["products_id"]])) {
                $aspecial_gratis_ob['product_link'] = xtc_href_link('checkout_product_info.php', xtc_product_link($aspecial_gratis_ob['products_id'], $aspecial_gratis_ob['products_name']));
               
				if ($aspecial_gratis_ob['products_image'] != '') {
					$aspecial_gratis_ob['gratis_image'] = xtc_image(DIR_WS_MINI_IMAGES . $aspecial_gratis_ob['products_image'], $aspecial_gratis_ob['products_name']);
				} else {
					$aspecial_gratis_ob['gratis_image'] = xtc_image(DIR_WS_MINI_IMAGES . 'no_img.jpg', $aspecial_gratis_ob['products_name']);
				}
                $aspecial_gratis[$aspecial_gratis_ob['products_id']] = $aspecial_gratis_ob;
            }
        }
        return $aspecial_gratis;
    }
}
