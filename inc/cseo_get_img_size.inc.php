<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_get_img_size.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function cseo_get_img_size($img_src, $mini = false) {
    if ($img_src != '' && file_exists($img_src)) {
        $size = getimagesize($img_src);
        if ($mini) {
            return 'width="' . round(($size[0] / 2.2), 2) . '" height="' . round(($size[1] / 2.2), 2) . '"';
        }
        return 'width="' . $size[0] . '" height="' . $size[1] . '"';
    } else {
        return false;
    }
}
