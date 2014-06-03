<?php

/* -----------------------------------------------------------------
 * 	$Id: getCountry.php 420 2013-06-19 18:04:39Z akausch $
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

include ('includes/application_top.php');

$zones_query = xtc_db_query("SELECT zone_id,zone_name FROM zones WHERE zone_country_id = '" . (int) $_GET['land'] . "' ORDER BY zone_name");
if (xtc_db_num_rows($zones_query)) {
    $zones_array = array();
    $select = '<select style="width:215px;" name="state">';
    while ($zones_values = xtc_db_fetch_array($zones_query)) {
        $select .= '<option value="' . $zones_values['zone_id'] . '">' . $zones_values['zone_name'] . '</option>';
    }
    $select .= '</select> <span class="inputRequirement">*</span>';
} else {
    $select = 'dieses Land hat keine Bundesl&auml;nder/Bundesstaaten';
}
echo $select;
