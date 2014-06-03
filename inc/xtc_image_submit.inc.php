<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_image_submit.inc.php 866 2014-03-17 12:07:35Z akausch $
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

if (CSS_BUTTON_ACTIVE == 'true' || CSS_BUTTON_ACTIVE == 'css') {

    function xtc_image_submit($image, $alt = '', $parameters = '', $mouseover = false, $mousedown = false) {
        $image_submit = '<input type="submit" class="css_img_button" value="' . xtc_parse_input_field_data($alt, array('"' => '&quot;')) . '"';
        if (xtc_not_null($alt))
            $image_submit .= ' title="' . xtc_parse_input_field_data($alt, array('"' => '&quot;')) . '"';

        if (xtc_not_null($parameters))
            $image_submit .= ' ' . $parameters;

        $image_submit .= ' />';

        return $image_submit;
    }

} else {

    function xtc_image_submit($image, $alt = '', $parameters = '', $mouseover = true, $mousedown = true) {
        $src = xtc_parse_input_field_data('templates/' . CURRENT_TEMPLATE . '/buttons/' . $_SESSION['language'] . '/' . $image, array('"' => '&quot;'));

        $image_submit = '<input type="' . ((CSS_BUTTON_ACTIVE == 'true' || CSS_BUTTON_ACTIVE == 'css') ? 'submit' : 'image') . '" src="' . $src . '" onfocus="if(this.blur)this.blur()" alt="' . xtc_parse_input_field_data($alt, array('"' => '&quot;')) . '"';
        if (xtc_not_null($alt))
            $image_submit .= ' title="' . xtc_parse_input_field_data($alt, array('"' => '&quot;')) . '"';

        if (xtc_not_null($parameters))
            $image_submit .= ' ' . $parameters;

        if ($mouseover == true || $mousedown == true) {
            require_once(DIR_FS_INC . 'xtc_image.inc.php');
            $image_submit .= image_mouseover($mouseover, $mousedown, $src);
        }

        $image_submit .= ' />';

        return $image_submit;
    }

}

