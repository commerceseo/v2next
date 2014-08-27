<?php

/* -----------------------------------------------------------------
 * 	$Id: specials.php 1139 2014-07-09 10:35:27Z akausch $
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

require('includes/application_top.php');
$coo_text_mgr = new LanguageTextManager('specials', $_SESSION['languages_id']);
$smarty = new Smarty;
$smarty->assign('txt', $coo_text_mgr->v_section_content_array['specials']);
require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
$orderlistingnum = ADMIN_DEFAULT_LISTING_NUM;

$cstatus = xtc_get_customers_statuses();
foreach ($cstatus AS $value) {
    if (!column_exists(TABLE_SPECIALS, 'specials_price_' . $value['id'])) {
        @xtc_db_query("ALTER TABLE specials ADD specials_price_" . $value['id'] . " DECIMAL( 15, 4 ) NOT NULL;");
    }
}

$xtPrice = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);

switch ($_GET['action']) {
    case 'setflag':
        xtc_set_specials_status($_GET['id'], $_GET['flag']);
        xtc_redirect(xtc_href_link(FILENAME_SPECIALS, '', 'NONSSL'));
        break;
    case 'insert':
        $sid = xtc_db_prepare_input($_POST['specials_id']);
        $specials_price = xtc_db_prepare_input($_POST['specials_price']);
        $specials_expires = xtc_db_prepare_input($_POST['specials_expires']);

        if (PRICE_IS_BRUTTO == 'true' && substr($specials_price, -1) != '%') {
            $tax = xtc_db_fetch_array(xtc_db_query("SELECT tr.tax_rate FROM " . TABLE_TAX_RATES . " tr, " . TABLE_PRODUCTS . " p  WHERE tr.tax_class_id = p. products_tax_class_id AND p.products_id = '" . $_POST['products_id'] . "';"));
            $specials_price = ($specials_price / ($tax['tax_rate'] + 100) * 100);
        }
        if (substr($specials_price, -1) == '%') {
            $new_special_insert = xtc_db_fetch_array(xtc_db_query("SELECT products_id, products_tax_class_id, products_price FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . (int) $_POST['products_id'] . "'"));
            $products_price = $new_special_insert['products_price'];
            $specials_price = ($products_price - (($specials_price / 100) * $products_price));
        }
        $expires_date = '';
        if ($specials_expires) {
            $expires_date = str_replace("-", "", $specials_expires);
        }

        $sql_data_array = array(
            'products_id' => xtc_db_prepare_input($_POST['products_id']),
            'specials_quantity' => xtc_db_prepare_input($_POST['specials_quantity']),
            'specials_new_products_price' => $specials_price,
            'specials_date_added' => 'now()',
            'expires_date' => $expires_date,
            'status' => '1',
        );
        xtc_db_perform(TABLE_SPECIALS, $sql_data_array);
        $sid = xtc_db_insert_id();

        foreach ($cstatus AS $value) {
            $new_gpricep = xtc_db_prepare_input($_POST['specials_price_' . $value['id']]);
            if (PRICE_IS_BRUTTO == 'true') {
                $new_gpricep = ($new_gpricep / ($tax[tax_rate] + 100) * 100);
            }
            $insert_sql_data = array('specials_price_' . $value['id'] => $new_gpricep);
            xtc_db_perform(TABLE_SPECIALS, $insert_sql_data, 'update', 'specials_id = \'' . $sid . '\'');
        }

        xtc_redirect(xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page']));
        break;

    case 'update':
        $sid = xtc_db_prepare_input($_POST['specials_id']);
        $specials_price = xtc_db_prepare_input($_POST['specials_price']);
        $specials_expires = xtc_db_prepare_input($_POST['specials_expires']);

        if (PRICE_IS_BRUTTO == 'true' && substr($specials_price, -1) != '%') {
            $tax = xtc_db_fetch_array(xtc_db_query("SELECT tr.tax_rate FROM " . TABLE_TAX_RATES . " tr, " . TABLE_PRODUCTS . " p  WHERE tr.tax_class_id = p. products_tax_class_id  AND p.products_id = '" . $_POST['products_up_id'] . "';"));
            $specials_price = ($specials_price / ($tax[tax_rate] + 100) * 100);
        }

        if (substr($specials_price, -1) == '%') {
            $specials_price = ($specials_price - (($specials_price / 100) * $_POST['products_price']));
        }
        $expires_date = '';
        if ($specials_expires) {
            $expires_date = str_replace("-", "", $specials_expires);
        }

        $sql_data_array = array(
            'specials_quantity' => xtc_db_prepare_input($_POST['specials_quantity']),
            'specials_new_products_price' => $specials_price,
            'specials_last_modified' => 'now()',
            'expires_date' => $expires_date,
        );
        xtc_db_perform(TABLE_SPECIALS, $sql_data_array, 'update', 'specials_id = \'' . $sid . '\'');

        foreach ($cstatus AS $value) {
            $new_gpricep = xtc_db_prepare_input($_POST['specials_price_' . $value['id']]);
            if (PRICE_IS_BRUTTO == 'true') {
                $new_gpricep = ($new_gpricep / ($tax[tax_rate] + 100) * 100);
            }
            $insert_sql_data = array('specials_price_' . $value['id'] => $new_gpricep);
            xtc_db_perform(TABLE_SPECIALS, $insert_sql_data, 'update', 'specials_id = \'' . $sid . '\'');
        }
        xtc_redirect(xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $specials_id));
        break;

    case 'deleteconfirm':
        $specials_id = xtc_db_prepare_input($_GET['sID']);
        xtc_db_query("DELETE FROM " . TABLE_SPECIALS . " WHERE specials_id = '" . xtc_db_input($specials_id) . "';");
        xtc_redirect(xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page']));
        break;
}
require_once(DIR_WS_INCLUDES . 'header.php');
if (($_GET['action'] == 'new') || ($_GET['action'] == 'edit')) {
    echo '<div id="spiffycalendar" class="text"></div>';
    $form_action = 'insert';
    if (($_GET['action'] == 'edit') && ($_GET['sID'])) {
        $form_action = 'update';

        $product = xtc_db_fetch_array(xtc_db_query("SELECT 
										p.products_tax_class_id,
										p.products_id,
										pd.products_name,
										p.products_price,
										s.*
										FROM
										" . TABLE_PRODUCTS . " AS p
										INNER JOIN
										" . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON(p.products_id = pd.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
										INNER JOIN
										" . TABLE_SPECIALS . " AS s ON(p.products_id = s.products_id)
										WHERE 
											s.specials_id = '" . (int) $_GET['sID'] . "';"));

        $sInfo = new objectInfo($product);

        if ($sInfo->expires_date != 0) {
            $expires_date = substr($sInfo->expires_date, 0, 4) . "-" .
                    substr($sInfo->expires_date, 5, 2) . "-" .
                    substr($sInfo->expires_date, 8, 2);
        } else {
            $expires_date = "";
        }
    } else {
        $sInfo = new objectInfo(array());
        $specials_array = array();
        $specials_query = xtc_db_query("SELECT p.products_id FROM " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s WHERE s.products_id = p.products_id;");
        while ($specials = xtc_db_fetch_array($specials_query)) {
            $specials_array[] = $specials['products_id'];
        }
    }
    $price = $sInfo->products_price;
    $new_price = $sInfo->specials_new_products_price;
    if (PRICE_IS_BRUTTO == 'true') {
        $price_netto = xtc_round($price, PRICE_PRECISION);
        $new_price_netto = xtc_round($new_price, PRICE_PRECISION);
        $price = ($price * (xtc_get_tax_rate($sInfo->products_tax_class_id) + 100) / 100);
        $new_price = ($new_price * (xtc_get_tax_rate($sInfo->products_tax_class_id) + 100) / 100);
    }
    $price = xtc_round($price, PRICE_PRECISION);
    $new_price = xtc_round($new_price, PRICE_PRECISION);

    $smarty->assign('EDIT', 'true');
    $smarty->assign('FORM', xtc_draw_form('new_special', FILENAME_SPECIALS, 'action=' . $form_action, 'post', ''));
    $smarty->assign('FORM_END', '</form>');
    if ($form_action == 'update') {
        $smarty->assign('HIDDEN', xtc_draw_hidden_field('specials_id', $_GET['sID']));
    }
    $smarty->assign('PRODUCT_NAME', ($sInfo->products_name) ? $sInfo->products_name . ' <small>(' . $xtPrice->xtcFormat($price, true) . ')</small>' : xtc_draw_products_pull_down('products_id', 'style="font-size:10px"', $specials_array));
    $smarty->assign('HIDDEN_UP', xtc_draw_hidden_field('products_up_id', $sInfo->products_id));
    $smarty->assign('HIDDEN_P', xtc_draw_hidden_field('products_price', $sInfo->products_price));
    $smarty->assign('SPECIAL_PRICE', xtc_draw_input_field('specials_price', $new_price));
    $smarty->assign('SPECIAL_PRICE_NETTO', TEXT_NETTO . $new_price_netto);
    foreach ($cstatus AS $value) {
        $new_gprice = xtc_db_fetch_array(xtc_db_query("SELECT specials_price_" . $value['id'] . " FROM " . TABLE_SPECIALS . " WHERE specials_id = '" . (int) $_GET['sID'] . "';"));
        $new_gpricep = $new_gprice['specials_price_' . $value['id']];
        if (PRICE_IS_BRUTTO == 'true') {
            $new_price_netto = xtc_round($new_gpricep, PRICE_PRECISION);
            $new_gpricep = ($new_gpricep * (xtc_get_tax_rate($sInfo->products_tax_class_id) + 100) / 100);
        }
        $new_gpricep = xtc_round($new_gpricep, PRICE_PRECISION);
        $specialgroup[] = array(
            'GROUNAME' => $value['text'],
            'INPUTFIELD' => xtc_draw_input_field('specials_price_' . $value['id'], $new_gpricep),
            'NETTO' => $new_price_netto,
        );
    }
    $smarty->assign('specialgrouparray', $specialgroup);
    $smarty->assign('SPECIAL_QUANTITY', xtc_draw_input_field('specials_quantity', $sInfo->specials_quantity));
    $smarty->assign('SPECIAL_DATE', '<input type="text" name="specials_expires" class="datepickers" value="' . ($expires_date != '00.00.0000' ? $expires_date : '') . '" />');
    if ($form_action == 'insert') {
        $smarty->assign('BUTTON_SUBMIT', '<button type="submit" class="btn btn-success">' . BUTTON_INSERT . '</button>');
    } else {
        $smarty->assign('BUTTON_SUBMIT', '<button type="submit" class="btn btn-success">' . BUTTON_UPDATE . '</button>');
    }
    $smarty->assign('BUTTON_CANCEL', '<a href="' . xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $_GET['sID']) . '"><button type="button" class="btn btn-default">' . BUTTON_CANCEL . '</button></a>');
    $smarty->assign('JAVASCRIPT', '
<script type="text/javascript">
    $(function() {
        $(\'.datepickers\').datepicker({
            minDate: new Date(' . date('Y') . ',' . date('m') . '-1,' . date('d') . '),
            buttonImage: "images/calendar.png",
            showOn: "button",
            dateFormat: \'yy-mm-dd\'});
    });
</script>
	');
} else {
    $smarty->assign('LIST', 'true');
    $specials_query_raw = "SELECT p.products_id, pd.products_name,p.products_tax_class_id, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status FROM " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE p.products_id = pd.products_id AND pd.language_id = '" . $_SESSION['languages_id'] . "' AND p.products_id = s.products_id ORDER BY pd.products_name";
    $specials_split = new splitPageResults($_GET['page'], $orderlistingnum, $specials_query_raw, $specials_query_numrows);
    $specials_query = xtc_db_query($specials_query_raw);
    while ($specials = xtc_db_fetch_array($specials_query)) {
        $price = $specials['products_price'];
        $new_price = $specials['specials_new_products_price'];
        if (PRICE_IS_BRUTTO == 'true') {
            $price_netto = xtc_round($price, PRICE_PRECISION);
            $new_price_netto = xtc_round($new_price, PRICE_PRECISION);
            $price = ($price * (xtc_get_tax_rate($specials['products_tax_class_id']) + 100) / 100);
            $new_price = ($new_price * (xtc_get_tax_rate($specials['products_tax_class_id']) + 100) / 100);
        }
        $specials['products_price'] = xtc_round($price, PRICE_PRECISION);
        $specials['specials_new_products_price'] = xtc_round($new_price, PRICE_PRECISION);

        if (((!$_GET['sID']) || ($_GET['sID'] == $specials['specials_id'])) && (!$sInfo)) {
            $products_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $specials['products_id'] . "'");
            $products = xtc_db_fetch_array($products_query);
            $sInfo_array = xtc_array_merge($specials, $products);
            $sInfo = new objectInfo($sInfo_array);
            $sInfo->specials_new_products_price = $specials['specials_new_products_price'];
            $sInfo->products_price = $specials['products_price'];
        }

        if ((is_object($sInfo)) && ($specials['specials_id'] == $sInfo->specials_id)) {
            $onclick = ' onclick="document.location.href=\'' . xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=edit') . '\'"';
        } else {
            $onclick = ' onclick="document.location.href=\'' . xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $specials['specials_id']) . '\'"';
        }
        $pname = $specials['products_name'];
        $o_price = '<span class="oldPrice">' . $xtPrice->xtcFormat($specials['products_price'], true) . '</span>';
        $s_price = '<span class="specialPrice">' . $xtPrice->xtcFormat($specials['specials_new_products_price'], true) . '</span>';

        if ($specials['status'] == '1') {
            $status = '<a href="' . xtc_href_link(FILENAME_SPECIALS, 'action=setflag&flag=0&id=' . $specials['specials_id'], 'NONSSL') . '"><button class="btn btn-success btn-xs"><i class="glyphicon glyphicon-ok"></i></button></a>';
        } else {
            $status = '<a href="' . xtc_href_link(FILENAME_SPECIALS, 'action=setflag&flag=1&id=' . $specials['specials_id'], 'NONSSL') . '"><button class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></button></a>';
        }
        if ((is_object($sInfo)) && ($specials['specials_id'] == $sInfo->specials_id)) {
            $action = xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ICON_ARROW_RIGHT);
        } else {
            $action = '<a href="' . xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $specials['specials_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
        }

        $specialslistarray[] = array(
            'PNAME' => $pname,
            'OPRICE' => $o_price,
            'SPRICE' => $s_price,
            'STATUS' => $status,
            'ACTION' => $action,
            'TR_ONCLICK' => $onclick,
        );
    }

    $smarty->assign('specialslistarray', $specialslistarray);
    $smarty->assign('DISPLAY_NUMBER', $specials_split->display_count($specials_query_numrows, $orderlistingnum, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS));
    $smarty->assign('DISPLAY_SITE', $specials_split->display_links($specials_query_numrows, $orderlistingnum, MAX_DISPLAY_PAGE_LINKS, $_GET['page']));
    $smarty->assign('NEW_BUTTON', xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&action=new'));

    $heading = array();
    $contents = array();
    switch ($_GET['action']) {
        case 'delete':
            $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SPECIALS . '</b>');
            $contents = array('form' => xtc_draw_form('specials', FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=deleteconfirm'));
            $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
            $contents[] = array('text' => '<br /><b>' . $sInfo->products_name . '</b>');
            $contents[] = array('align' => 'center', 'text' => '<br /><button type="submit" class="btn btn-danger">' . BUTTON_DELETE . '</button> <a href="' . xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id) . '"><button type="button" class="btn btn-primary">' . BUTTON_CANCEL . '</button></a>');
            break;

        default:
            if (is_object($sInfo)) {
                $heading[] = array('text' => '<b>' . $sInfo->products_name . '</b>');
                $contents[] = array('align' => 'center', 'text' => '<a href="' . xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=edit') . '"><button type="button" class="btn btn-primary">' . BUTTON_EDIT . '</button></a> <a href="' . xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=delete') . '"><button type="button" class="btn btn-danger">' . BUTTON_DELETE . '</button></a>');
                $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . xtc_date_short($sInfo->specials_date_added));
                $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . xtc_date_short($sInfo->specials_last_modified));
                $contents[] = array('align' => 'center', 'text' => '<br />' . xtc_product_thumb_image($sInfo->products_image, $sInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT));
                $contents[] = array('text' => '<br />' . TEXT_INFO_ORIGINAL_PRICE . ' ' . $xtPrice->xtcFormat($sInfo->products_price, true));
                $contents[] = array('text' => '' . TEXT_INFO_NEW_PRICE . ' ' . $xtPrice->xtcFormat($sInfo->specials_new_products_price, true));
                $contents[] = array('text' => '' . TEXT_INFO_PERCENTAGE . ' ' . number_format(100 - (($sInfo->specials_new_products_price / $sInfo->products_price) * 100)) . '%');
                $contents[] = array('text' => '<br />' . TEXT_INFO_EXPIRES_DATE . ' <b>' . xtc_date_short($sInfo->expires_date) . '</b>');
                $contents[] = array('text' => '' . TEXT_INFO_STATUS_CHANGE . ' ' . xtc_date_short($sInfo->date_status_change));
            }
            break;
    }
    if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
        $box = new box;
        $smarty->assign('SITE_BOX', $box->infoBox($heading, $contents));
    }
}
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
$smarty->caching = false;
$smarty->template_dir = DIR_FS_CATALOG . 'admin/templates/';
$smarty->compile_dir = DIR_FS_CATALOG . 'admin/templates_c';
$smarty->display(CURRENT_ADMIN_TEMPLATE . '/specials.html');

require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
