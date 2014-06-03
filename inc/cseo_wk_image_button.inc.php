<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_wk_image_button.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Output a function button in the selected language
if (CSS_BUTTON_ACTIVE == 'true' || CSS_BUTTON_ACTIVE == 'css') {

    function cseo_wk_image_button($image, $alt = '', $parameters = '', $mouseover = true, $mousedown = true) {
        $image = '<span class="css_wk_img_button" ' . $parameters . '>' . $alt . '</span>';
        return $image;
    }

} else {

    function cseo_wk_image_button($image, $alt = '', $parameters = '', $mouseover = true, $mousedown = true) {
        return xtc_image('templates/' . CURRENT_TEMPLATE . '/buttons/' . $_SESSION['language'] . '/' . $image, $alt, '', '', $parameters, $mouseover, $mousedown);
    }

}
