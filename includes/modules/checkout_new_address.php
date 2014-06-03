<?php

/* -----------------------------------------------------------------
 * 	$Id: checkout_new_address.php 522 2013-07-24 11:44:51Z akausch $
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

$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
// include needed functions

require_once (DIR_FS_INC . 'xtc_get_country_list.inc.php');

if (!isset($process))
    $process = false;

if (ACCOUNT_GENDER == 'true') {
    $male = ($gender == 'm') ? true : false;
    $female = ($gender == 'f') ? true : false;
    $module_smarty->assign('gender', '1');
    $module_smarty->assign('INPUT_MALE', xtc_draw_radio_field(array('name' => 'gender', 'suffix' => MALE), 'm'));
    $module_smarty->assign('INPUT_FEMALE', xtc_draw_radio_field(array('name' => 'gender', 'suffix' => FEMALE, 'text' => (xtc_not_null(ENTRY_GENDER_TEXT) ? '<span class="inputRequirement">' . ENTRY_GENDER_TEXT . '</span>' : '')), 'f'));
}
$module_smarty->assign('INPUT_FIRSTNAME', xtc_draw_input_fieldNote(array('name' => 'firstname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>' : ''))));
$module_smarty->assign('INPUT_LASTNAME', xtc_draw_input_fieldNote(array('name' => 'lastname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : ''))));

if (ACCOUNT_COMPANY == 'true') {
    $module_smarty->assign('company', '1');
    $module_smarty->assign('INPUT_COMPANY', xtc_draw_input_fieldNote(array('name' => 'company', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_COMPANY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COMPANY_TEXT . '</span>' : ''))));
} else {
    $module_smarty->assign('company', '0');
    //  }
}
$module_smarty->assign('INPUT_STREET', xtc_draw_input_fieldNote(array('name' => 'street_address', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_STREET_ADDRESS_TEXT . '</span>' : ''))));

if (ACCOUNT_SUBURB == 'true') {
    $module_smarty->assign('suburb', '1');
    $module_smarty->assign('INPUT_SUBURB', xtc_draw_input_fieldNote(array('name' => 'suburb', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_SUBURB_TEXT) ? '<span class="inputRequirement">' . ENTRY_SUBURB_TEXT . '</span>' : ''))));
}
$module_smarty->assign('INPUT_CODE', xtc_draw_input_fieldNote(array('name' => 'postcode', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="inputRequirement">' . ENTRY_POST_CODE_TEXT . '</span>' : ''))));
$module_smarty->assign('INPUT_CITY', xtc_draw_input_fieldNote(array('name' => 'city', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_CITY_TEXT) ? '<span class="inputRequirement">' . ENTRY_CITY_TEXT . '</span>' : ''))));
if ($_POST['country']) {
    $selected = $_POST['country'];
} else {
    $selected = STORE_COUNTRY;
}

$cid = empty($country) ? $selected : $country;
$html_country_onchange = '';

if (ACCOUNT_STATE == 'true') {
    $module_smarty->assign('state', '1');

    $zones_array = array();
    $zones_query = xtc_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . xtc_db_input($cid) . "' order by zone_name");
    while ($zones_values = xtc_db_fetch_array($zones_query)) {
        $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
    }
    if (count($zones_array) > 0) {
        $entry_state = '<span id="checkout_states">' . xtc_draw_pull_down_menuNote(array('name' => 'state', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">' . ENTRY_STATE_TEXT . '</span>' : '')), $zones_array) . '</span>';
        $module_smarty->assign('state_stat', '1');
    } else {
        $entry_state = '<span id="checkout_states"></span>';
        $module_smarty->assign('state_stat', '0');
    }

    $html_country_onchange = 'onchange="updateStates(this.value, this.form);"';

    $module_smarty->assign('INPUT_STATE', $entry_state);
} else {
    $module_smarty->assign('state', '0');
}

$module_smarty->assign('SELECT_COUNTRY', xtc_get_country_list('country', $selected, $html_country_onchange) . '&nbsp;' . (xtc_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>' : ''));

$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$module_smarty->caching = false;
$module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/checkout_new_address.html', USE_TEMPLATE_DEVMODE));

$smarty->assign('MODULE_new_address', $module);
