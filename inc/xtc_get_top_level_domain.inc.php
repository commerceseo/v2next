<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_top_level_domain.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_top_level_domain($url) {
    if (strpos($url, '://')) {
        $url = parse_url($url);
        $url = $url['host'];
    }
    $domain_array = explode('.', $url);
    $domain_size = sizeof($domain_array);
    if ($domain_size > 1) {
        if (is_numeric($domain_array[$domain_size - 2]) && is_numeric($domain_array[$domain_size - 1])) {
            return false;
        } else {
            for ($domain_part = 1; $domain_part < $domain_size; $domain_part++) {
                $domain_path .= $domain_array[$domain_part];
                if ($domain_part != ($domain_size - 1))
                    $domain_path .= '.';
            }
            return $domain_path;
        }
    } else {
        return false;
    }
}
