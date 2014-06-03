<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_get_stock_img.inc.php 866 2014-03-17 12:07:35Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

function cseo_get_stock_img($qty) {
    if (STOCK_CHECK == 'true') {
        if ($qty >= STOCK_WARNING_GREEN) {
            $img = xtc_image('images/icons/icon_stock_1.png', $qty, IN_STOCK, 'product_stock_img');
        } elseif ($qty > STOCK_WARNING_RED && $qty < STOCK_WARNING_GREEN) {
            $img = xtc_image('images/icons/icon_stock_2.png', $qty, IN_W_STOCK, 'product_stock_img');
        } elseif ($qty <= STOCK_WARNING_RED) {
            $img = xtc_image('images/icons/icon_stock_3.png', $qty, IN_W_STOCK, 'product_stock_img');
        } else {
            $img = '';
        }
    }

    return $img;
}
