<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_get_conf.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function cseo_get_conf($cseo_key, $result_type = 'ASSOC', $p_renew_cache = false) {
    static $t_conf_array;
    if ($t_conf_array === NULL)
        $t_conf_array = array();

    $cseo_values = false;

    # read config into a static variable
    if (count($t_conf_array) == 0 || $p_renew_cache == true) {
        $cseo_query = xtc_db_query('SELECT * FROM cseo_configuration');
        while ($row = xtc_db_fetch_array($cseo_query)) {
            $t_key = strtoupper($row['cseo_key']);
            $t_conf_array[$t_key] = $row;
        }
    }

    # write the return array
    if ($result_type == 'ASSOC' || $result_type == 'NUMERIC') {
        if (is_array($cseo_key)) {
            # multiple keys requested
            foreach ($cseo_key as $key) {
                $key_upper = strtoupper($key);
                if ($result_type == 'ASSOC') {
                    $cseo_values[$key] = $t_conf_array[$key_upper]['cseo_value'];
                } else {
                    $cseo_values[] = $t_conf_array[$key_upper]['cseo_value'];
                }
            }
        } else {
            # single key requested
            $cseo_key = strtoupper($cseo_key);
            $cseo_values = $t_conf_array[$cseo_key]['cseo_value'];
        }
    }
    return $cseo_values;
}

function cseo_set_conf($cseo_conf_key, $cseo_conf_value) {
    $gm_row = xtc_db_fetch_array(xtc_db_query("SELECT cseo_key FROM cseo_configuration WHERE cseo_key = '" . $cseo_conf_key . "';"));

    if (!empty($gm_row['cseo_key'])) {
        $result = xtc_db_query("UPDATE cseo_configuration SET cseo_key = '" . $cseo_conf_key . "', cseo_value	= '" . $cseo_conf_value . "' WHERE cseo_key = '" . $cseo_conf_key . "';");
    } else {
        $result = xtc_db_query("INSERT INTO cseo_configuration SET cseo_key = '" . $cseo_conf_key . "', cseo_value	= '" . $cseo_conf_value . "';");
    }

    return $result;
}
