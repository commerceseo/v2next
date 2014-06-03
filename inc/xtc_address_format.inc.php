<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_address_format.inc.php 866 2014-03-17 12:07:35Z akausch $
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

require_once(DIR_FS_INC . 'xtc_get_zone_code.inc.php');
require_once(DIR_FS_INC . 'xtc_get_country_name.inc.php');

function xtc_address_format($address_format_id, $address, $html, $boln, $eoln) {
    $address_format = xtc_db_fetch_array(xtc_db_query("SELECT address_format AS format FROM " . TABLE_ADDRESS_FORMAT . " WHERE address_format_id = '" . (int) $address_format_id . "';"));

    $company = addslashes($address['company']);
    $firstname = addslashes($address['firstname']);
    $lastname = addslashes($address['lastname']);
    $street = addslashes($address['street_address']);
    $suburb = addslashes($address['suburb']);
    $city = addslashes($address['city']);
    $state = addslashes($address['state']);
    $country_id = $address['country_id'];
    $zone_id = $address['zone_id'];
    $postcode = addslashes($address['postcode']);
    $zip = $postcode;
    $country = xtc_get_country_name($country_id);
    $state = xtc_get_zone_code($country_id, $zone_id, $state);

    if ($html) {
        // HTML Mode
        $HR = "<hr />";
        $hr = "<hr />";
        if (($boln == '') && ($eoln == "\n")) { // Values not specified, use rational defaults
            $CR = "<br />";
            $cr = "<br />";
            $eoln = $cr;
        } else { // Use values supplied
            $CR = $eoln . $boln;
            $cr = $CR;
        }
    } else {
        // Text Mode
        $CR = $eoln;
        $cr = $CR;
        $HR = "----------------------------------------";
        $hr = "----------------------------------------";
    }

    $statecomma = '';
    $streets = $street;
    if ($suburb != '')
        $streets = $street . $cr . $suburb;
    if ($firstname == '')
        $firstname = addslashes($address['name']);
    if ($country == '')
        $country = addslashes($address['country']);
    if ($state != '')
        $statecomma = $state . ', ';

    $fmt = $address_format['format'];
    eval("\$address = \"$fmt\";");

    if ((ACCOUNT_COMPANY == 'true') && (xtc_not_null($company))) {
        $address = $company . $cr . $address;
    }

    $address = stripslashes($address);

    return $address;
}
