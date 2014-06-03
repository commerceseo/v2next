<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_get_usermod.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function cseo_get_usermod($p_file_path, $p_debug_output = false) {
    $t_file_path = trim($p_file_path);
    $t_coo_cached_directory = new CachedDirectory('');

    # extract filename
    $t_file_name = basename($t_file_path);

    # extend filename
    $t_file_parts = explode('.', $t_file_name);
    $t_file_parts[0] .= '-USERMOD';

    # rebuild filename
    $t_file_name = implode('.', $t_file_parts);

    # rebuild possible filepath to usermod-version
    $t_usermod_file_path = dirname($t_file_path) . '/' . $t_file_name;
    # check if -USERMOD-file exists
    if ($t_coo_cached_directory->file_exists('templates/' . $t_usermod_file_path)) {
        $t_file_path = $t_usermod_file_path;
    }
    if ($p_debug_output == 'true') {
        echo "input-template: $p_file_path <br/>\n";
        echo "tried-template: $t_usermod_file_path <br/>\n";
        echo "result-template: $t_file_path <br/>\n";
    }
    return $t_file_path;
}

?>