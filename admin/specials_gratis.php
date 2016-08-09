<?php

/* -----------------------------------------------------------------
 * 	$Id: specials_gratis.php 1150 2014-07-15 15:22:30Z akausch $
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
$smarty = new Smarty;
$coo_text_mgr = new LanguageTextManager('specials_gratis', $_SESSION['languages_id']);
$smarty->assign('txt', $coo_text_mgr->v_section_content_array['specials_gratis']);

$xtPrice = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);
$languages = xtc_get_languages();

require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
require_once(DIR_FS_INC . 'xtc_set_specials_gratis_status.inc.php');

switch ($_GET['action']) {
    case 'setflag':
        xtc_set_specials_gratis_status($_GET['id'], $_GET['flag']);
        break;
    case 'insert':
        if (substr($_POST['specials_gratis_price'], -1) == '%') {
            $new_special_insert = xtc_db_fetch_array(xtc_db_query("SELECT products_id, products_tax_class_id, products_price FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . (int) $_POST['products_id'] . "';"));
            $_POST['products_price'] = $new_special_insert['products_price'];
            $_POST['specials_gratis_price'] = ($_POST['products_price'] - (($_POST['specials_gratis_price'] / 100) * $_POST['products_price']));
        } elseif (PRICE_IS_BRUTTO == 'true' && substr($_POST['specials_gratis_price'], -1) != '%') {
            $tax = xtc_db_fetch_array(xtc_db_query("SELECT
														tr.tax_rate
													FROM
														" . TABLE_TAX_RATES . " AS tr,
														" . TABLE_ZONES_TO_GEO_ZONES . " AS ztgz,
														" . TABLE_PRODUCTS . " AS p
													WHERE
														tr.tax_class_id = p.products_tax_class_id 
													AND
														p.products_id = '" . (int) $_POST['products_id'] . "' 
													AND
														tr.tax_zone_id = ztgz.geo_zone_id 
													AND
														ztgz.zone_country_id = '" . (int) STORE_COUNTRY . "';"));

            $_POST['specials_gratis_price'] = ($_POST['specials_gratis_price'] / ($tax['tax_rate'] + 100) * 100);
        }

        $expires_date = '';
        if ($_POST['day'] && $_POST['month'] && $_POST['year']) {
            $expires_date = $_POST['year'];
            $expires_date .= (strlen($_POST['month']) == 1) ? '0' . $_POST['month'] : $_POST['month'];
            $expires_date .= (strlen($_POST['day']) == 1) ? '0' . $_POST['day'] : $_POST['day'];
        }

        xtc_db_query("INSERT INTO " . TABLE_SPECIALS_GRATIS . " (products_id, specials_gratis_quantity, specials_gratis_new_products_price, specials_gratis_min_price, specials_gratis_max_value, specials_gratis_ab_value, categories_id,  manufacturers_id, specials_gratis_date_added, expires_date, status) 
					VALUES 
						('" . xtc_db_prepare_input($_POST['products_id']) . "', 
						'" . xtc_db_prepare_input($_POST['specials_gratis_quantity']) . "', 
						'" . xtc_db_prepare_input($_POST['specials_gratis_price']) . "', 
						'" . xtc_db_prepare_input($_POST['specials_gratis_min_price']) . "', 
						'" . xtc_db_prepare_input($_POST['specials_gratis_max_value']) . "', 
						'" . xtc_db_prepare_input($_POST['specials_gratis_ab_value']) . "', 
						'" . xtc_db_prepare_input($_POST['categories_id']) . "', 
						'" . xtc_db_prepare_input($_POST['manufacturers_id']) . "', 
						now(), 
						'" . $expires_date . "', '1')");
        $specials_gratis_id = xtc_db_insert_id();

        foreach ($languages AS $lang) {
            xtc_db_query("INSERT INTO " . TABLE_SPECIALS_GRATIS_DESCRIPTION . " (specials_gratis_id, specials_gratis_description, language_id)
							VALUES ('" . $specials_gratis_id . "', 
							'" . xtc_db_prepare_input($_POST['specials_gratis_description'][$lang['id']]) . "',
							'" . $lang['id'] . "')");
        }

        xtc_redirect(xtc_href_link(FILENAME_SPECIALS_GRATIS, 'page=' . $_GET['page']));
        break;

    case 'update':
        if (PRICE_IS_BRUTTO == 'true' && substr($_POST['specials_gratis_price'], -1) != '%') {
            $tax = xtc_db_fetch_array(xtc_db_query("SELECT
														tr.tax_rate
													FROM
														" . TABLE_TAX_RATES . " AS tr,
														" . TABLE_ZONES_TO_GEO_ZONES . " AS ztgz,
														" . TABLE_PRODUCTS . " AS p
													WHERE
														tr.tax_class_id = p.products_tax_class_id 
													AND
														p.products_id = '" . (int) $_POST['products_up_id'] . "' 
													AND
														tr.tax_zone_id = ztgz.geo_zone_id 
													AND
														ztgz.zone_country_id = '" . (int) STORE_COUNTRY . "';"));

            $_POST['specials_gratis_price'] = ($_POST['specials_gratis_price'] / ($tax[tax_rate] + 100) * 100);
        }

        if (substr($_POST['specials_gratis_price'], -1) == '%') {
            $_POST['specials_gratis_price'] = ($_POST['products_price'] - (($_POST['specials_gratis_price'] / 100) * $_POST['products_price']));
        }
        $expires_date = '';
        if ($_POST['day']) {
            $expires_date = $_POST['day'];
        }

        xtc_db_query("UPDATE " . TABLE_SPECIALS_GRATIS . " 
						SET 
							specials_gratis_quantity = '" . xtc_db_prepare_input($_POST['specials_gratis_quantity']) . "', 
							specials_gratis_new_products_price = '" . xtc_db_prepare_input($_POST['specials_gratis_price']) . "', 
							specials_gratis_max_value = '" . xtc_db_prepare_input($_POST['specials_gratis_max_value']) . "', 
							specials_gratis_ab_value = '" . xtc_db_prepare_input($_POST['specials_gratis_ab_value']) . "', 
							categories_id = '" . xtc_db_prepare_input($_POST['categories_id']) . "', 
							manufacturers_id = '" . xtc_db_prepare_input($_POST['manufacturers_id']) . "', 
							specials_gratis_min_price = '" . xtc_db_prepare_input($_POST['specials_gratis_min_price']) . "', 
							specials_gratis_last_modified = now(), 
							expires_date = '" . $expires_date . "' 
						WHERE 
							specials_gratis_id = '" . (int) $_POST['specials_gratis_id'] . "'");

        foreach ($languages AS $lang) {
            xtc_db_query("UPDATE " . TABLE_SPECIALS_GRATIS_DESCRIPTION . " 
						SET 
							specials_gratis_description = '" . xtc_db_prepare_input($_POST['specials_gratis_description'][$lang['id']]) . "'
						WHERE 
							specials_gratis_id = '" . (int) $_POST['specials_gratis_id'] . "'
						AND 
							language_id = '" . $lang['id'] . "';");
        }

        xtc_redirect(xtc_href_link(FILENAME_SPECIALS_GRATIS, 'page=' . $_GET['page'] . '&sID=' . $specials_gratis_id));
        break;

    case 'deleteconfirm':
        $specials_gratis_id = xtc_db_prepare_input($_GET['sID']);
        xtc_db_query("DELETE FROM " . TABLE_SPECIALS_GRATIS . " WHERE specials_gratis_id = '" . xtc_db_input($specials_gratis_id) . "'");
        xtc_db_query("DELETE FROM " . TABLE_SPECIALS_GRATIS_DESCRIPTION . " WHERE specials_gratis_id = '" . xtc_db_input($specials_gratis_id) . "'");
        xtc_redirect(xtc_href_link(FILENAME_SPECIALS_GRATIS, 'page=' . $_GET['page']));
        break;
}

require_once(DIR_WS_INCLUDES . 'header.php');

echo '
<script>
$(function() {
$(\'.datepickers\').datepicker({
minDate: new Date(' . date('Y') . ',' . date('m') . '-1,' . date('d') . '),
buttonImage: "images/calendar.png",
showOn: "button",
dateFormat: \'yy-mm-dd\'});
});
</script>';

if (($_GET['action'] == 'new') || ($_GET['action'] == 'edit')) {
    $form_action = 'insert';
    if (($_GET['action'] == 'edit') && ($_GET['sID'])) {
        $form_action = 'update';
        $product = xtc_db_fetch_array(xtc_db_query("SELECT 
														p.products_tax_class_id,
														p.products_id,
														pd.products_name,
														p.products_price,
														s.*
													FROM " . TABLE_PRODUCTS . " AS p
													JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON(p.products_id = pd.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
													JOIN " . TABLE_SPECIALS_GRATIS . " AS s ON(p.products_id = s.products_id)
													WHERE s.specials_gratis_id = '" . (int) $_GET['sID'] . "';"));

        $sInfo = new objectInfo($product);
        $special_desc = xtc_db_query("SELECT * FROM " . TABLE_SPECIALS_GRATIS_DESCRIPTION . " WHERE specials_gratis_id = '" . (int) $_GET['sID'] . "';");
        while ($special_desc_out = xtc_db_fetch_array($special_desc)) {
            $sdesc[$special_desc_out['language_id']] = $special_desc_out['specials_gratis_description'];
        }
    } else {
        $sInfo = new objectInfo(array());
        $specials_gratis_array = array();
        if (isset($_GET['pID'])) {
            $specials_gratis_query = xtc_db_query("SELECT products_id FROM " . TABLE_PRODUCTS . " WHERE products_id != '" . (int) $_GET['pID'] . "'");
            while ($specials_gratis = xtc_db_fetch_array($specials_gratis_query)) {
                $specials_gratis_array[] = $specials_gratis['products_id'];
            }
        } else {
            $specials_gratis_query = xtc_db_query("SELECT p.products_id FROM " . TABLE_PRODUCTS . " AS p INNER JOIN " . TABLE_SPECIALS_GRATIS . " AS s ON(s.products_id = p.products_id)");
            while ($specials_gratis = xtc_db_fetch_array($specials_gratis_query)) {
                $specials_gratis_array[] = $specials_gratis['products_id'];
            }
        }
    }
    $price = $sInfo->products_price;
    $new_price = $sInfo->specials_gratis_new_products_price;
    if (PRICE_IS_BRUTTO == 'true') {
        $price_netto = xtc_round($price, PRICE_PRECISION);
        $new_price_netto = xtc_round($new_price, PRICE_PRECISION);
        $price = ($price * (xtc_get_tax_rate($sInfo->products_tax_class_id) + 100) / 100);
        $new_price = ($new_price * (xtc_get_tax_rate($sInfo->products_tax_class_id) + 100) / 100);
    }
    $price = xtc_round($price, PRICE_PRECISION);
    $new_price = xtc_round($new_price, PRICE_PRECISION);


    $cat_name_array = array(array('id' => '0', 'text' => TXT_ALL));
    $cat_name_query = xtc_db_query("SELECT categories_id, categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY categories_name");
    while ($cat_name = xtc_db_fetch_array($cat_name_query)) {
        $cat_name_array[] = array('id' => $cat_name['categories_id'], 'text' => $cat_name['categories_name']);
    }

    $man_name_array = array(array('id' => '0', 'text' => TXT_ALL));
    $man_name_query = xtc_db_query("SELECT manufacturers_id, manufacturers_name FROM " . TABLE_MANUFACTURERS . " ORDER BY manufacturers_name");
    while ($man_name = xtc_db_fetch_array($man_name_query)) {
        $man_name_array[] = array('id' => $man_name['manufacturers_id'], 'text' => $man_name['manufacturers_name']);
    }
    $smarty->assign('EDIT', 'true');
    $smarty->assign('FORM', xtc_draw_form('new_special', FILENAME_SPECIALS_GRATIS, xtc_get_all_get_params(array('action', 'info', 'sID')) . 'action=' . $form_action));
    $smarty->assign('FORM_END', '</form>');
    $smarty->assign('HIDDEN', xtc_draw_hidden_field('specials_gratis_id', $_GET['sID']) .
            xtc_draw_hidden_field('products_up_id', $sInfo->products_id) .
            xtc_draw_hidden_field('products_price', $sInfo->products_price) .
            xtc_draw_hidden_field('categories_up_id', $sInfo->categories_id) .
            xtc_draw_hidden_field('manufacturers_up_id', $sInfo->manufacturers_id));

    $smarty->assign('PRODUCTS_NAME', ($sInfo->products_name) ? $sInfo->products_name . ' <small>(' . $xtPrice->xtcFormat($price, true) . ')</small>' : xtc_draw_products_pull_down('products_id', '', $specials_gratis_array));
    $smarty->assign('MANU_NAME', xtc_draw_pull_down_menu('manufacturers_id', $man_name_array, $sInfo->manufacturers_id));
    $smarty->assign('CAT_NAME', xtc_draw_pull_down_menu('categories_id', $cat_name_array, $sInfo->categories_id));
    $smarty->assign('SPECIALS_GRATIS_QUANTITY', xtc_draw_input_field('specials_gratis_quantity', $sInfo->specials_gratis_quantity));
    $smarty->assign('SPECIALS_GRATIS_MIN_PRICE', xtc_draw_input_field('specials_gratis_min_price', $sInfo->specials_gratis_min_price));
    $smarty->assign('SPECIALS_GRATIS_AB_VALUE', xtc_draw_input_field('specials_gratis_ab_value', $sInfo->specials_gratis_ab_value));
    $smarty->assign('SPECIALS_GRATIS_MAX_VALUE', xtc_draw_input_field('specials_gratis_max_value', $sInfo->specials_gratis_max_value));
    $smarty->assign('DAY', xtc_draw_input_field('day', $sInfo->expires_date, 'size="30" class="datepickers"'));
    $smarty->assign('BUTTON', (($form_action == 'insert') ? '<input style="float:left" type="submit" class="button" value="' . BUTTON_INSERT . '"/>' : '<input style="float:left" type="submit" class="button" value="' . BUTTON_UPDATE . '"/>') . '<a style="float:left" class="button" href="' . xtc_href_link(FILENAME_SPECIALS_GRATIS, 'page=' . $_GET['page'] . '&sID=' . $_GET['sID']) . '">' . BUTTON_CANCEL . '</a>');
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $lang_img = xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']);
        $desc = xtc_draw_textarea_field('specials_gratis_description[' . $languages[$i]['id'] . ']', 'soft', '103', '20', (($sdesc[$languages[$i]['id']]) ? stripslashes($sdesc[$languages[$i]['id']]) : $sdesc[$languages[$i]['id']]), '');
		if (USE_WYSIWYG == 'true') {
			if (file_exists('includes/ckfinder/ckfinder.js')) {
				$field_sdesc_wy = "<script src=\"includes/ckeditor/ckeditor.js\"></script>
									<script src=\"includes/ckfinder/ckfinder.js\"></script>
									<script>
										var newCKEdit = CKEDITOR.replace('specials_gratis_description[" . $languages[$i]['id'] . "]');
										CKFinder.setupCKEditor(newCKEdit, 'includes/ckfinder/');
									</script>";
			} else {
				$field_sdesc_wy = "<script src=\"includes/ckeditor/ckeditor.js\"></script>
				<script>
					CKEDITOR.replace('specials_gratis_description[" . $languages[$i]['id'] . "]', {
						toolbar: \"ImageMapper\",
						language: \"" . $_SESSION['language_code'] . "\",
						baseHref: \"" . (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . "\",
						filebrowserBrowseUrl: \"includes/ckeditor/filemanager/index.html\"
					});
				</script>";
			}
		}
		$descaray[] = array(
            'DESCRIPTION' => $desc,
            'IMG' => $lang_img,
			'field_sdesc_wy' => $field_sdesc_wy
			);
    }
    $smarty->assign('descaray', $descaray);
} else {
    $smarty->assign('LIST', 'true');

    $specials_gratis_query_raw = "select p.products_id, pd.products_name,p.products_tax_class_id, p.products_price, s.specials_gratis_id, s.specials_gratis_new_products_price, s.specials_gratis_min_price, s.specials_gratis_max_value, s.specials_gratis_ab_value, s.categories_id, s.manufacturers_id, s.specials_gratis_date_added, s.specials_gratis_last_modified, s.expires_date, s.date_status_change, s.status from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS_GRATIS . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.products_id = s.products_id order by pd.products_name";
    $specials_gratis_split = new splitPageResults($_GET['page'], '20', $specials_gratis_query_raw, $specials_gratis_query_numrows);
    $specials_gratis_query = xtc_db_query($specials_gratis_query_raw);
    while ($specials_gratis = xtc_db_fetch_array($specials_gratis_query)) {

        $price = $specials_gratis['products_price'];
        $new_price = $specials_gratis['specials_gratis_new_products_price'];
        if (PRICE_IS_BRUTTO == 'true') {
            $price_netto = xtc_round($price, PRICE_PRECISION);
            $new_price_netto = xtc_round($new_price, PRICE_PRECISION);
            $price = ($price * (xtc_get_tax_rate($specials_gratis['products_tax_class_id']) + 100) / 100);
            $new_price = ($new_price * (xtc_get_tax_rate($specials_gratis['products_tax_class_id']) + 100) / 100);
        }
        $specials_gratis['products_price'] = xtc_round($price, PRICE_PRECISION);
        $specials_gratis['specials_gratis_new_products_price'] = xtc_round($new_price, PRICE_PRECISION);

        if (((!$_GET['sID']) || ($_GET['sID'] == $specials_gratis['specials_gratis_id'])) && (!$sInfo)) {
            $products = xtc_db_fetch_array(xtc_db_query("SELECT products_image FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $specials_gratis['products_id'] . "';"));
            $sInfo_array = xtc_array_merge($specials_gratis, $products);
            $sInfo = new objectInfo($sInfo_array);
            $sInfo->specials_gratis_new_products_price = $specials_gratis['specials_gratis_new_products_price'];
            $sInfo->specials_gratis_min_price = $specials_gratis['specials_gratis_min_price'];
            $sInfo->specials_gratis_ab_value = $specials_gratis['specials_gratis_ab_value'];
            $sInfo->categories_id = $specials_gratis['categories_id'];
            $sInfo->manufacturers_id = $specials_gratis['manufacturers_id'];
            $sInfo->products_price = $specials_gratis['products_price'];
            $sInfo->specials_gratis_max_value = $secials_gratis['specials_gratis_max_value'];
        }

        $products_name = $specials_gratis['products_name'];
        $specials_gratis_ab_value = $specials_gratis['specials_gratis_ab_value'];
        $specials_gratis_min_price = $xtPrice->xtcFormat($specials_gratis['specials_gratis_min_price'], true);
        if ($specials_gratis['status'] == '1') {
            $status = xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . xtc_href_link(FILENAME_SPECIALS_GRATIS, 'action=setflag&flag=0&id=' . $specials_gratis['specials_gratis_id'] . "&page=" . $_GET['page'], 'NONSSL') . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
        } else {
            $status = '<a href="' . xtc_href_link(FILENAME_SPECIALS_GRATIS, 'action=setflag&flag=1&id=' . $specials_gratis['specials_gratis_id'] . "&page=" . $_GET['page'], 'NONSSL') . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
        }

        if ((is_object($sInfo)) && ($specials_gratis['specials_gratis_id'] == $sInfo->specials_gratis_id)) {
            $action = '';
        } else {
            $action = xtc_href_link(FILENAME_SPECIALS_GRATIS, 'page=' . $_GET['page'] . '&sID=' . $specials_gratis['specials_gratis_id']);
        }
		if ((is_object($sInfo)) && ($specials_gratis['specials_gratis_id'] == $sInfo->specials_gratis_id)) {
			$trback = 'success';
		} else {
			$trback = '';
		}
        $listarray[] = array(
            'PRODUCTS_NAME' => $products_name,
            'SPECIALS_GRATIS_AB_VALUE' => $specials_gratis_ab_value,
            'SPECIALS_GRATIS_MIN_PRICE' => $specials_gratis_min_price,
            'STATUS' => $status,
            'EDITLINK' => xtc_href_link(FILENAME_SPECIALS_GRATIS, 'page=' . $_GET['page'] . '&sID=' . $specials_gratis['specials_gratis_id'] . '&action=edit'),
            'TRBACK' => $trback,
            'ACTION' => $action);
    }
    $smarty->assign('listarray', $listarray);
    $smarty->assign('specials_gratis_split', $specials_gratis_split->display_count($specials_gratis_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS));
    $smarty->assign('specials_gratis_count', $specials_gratis_split->display_links($specials_gratis_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']));
    if (!$_GET['action']) {
        $smarty->assign('newbutton', xtc_href_link(FILENAME_SPECIALS_GRATIS, 'page=' . $_GET['page'] . '&action=new'));
    }

    $heading = array();
    $contents = array();
    switch ($_GET['action']) {
        case 'delete':
            $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SPECIALS . '</b>');
            $contents = array('form' => xtc_draw_form('specials_gratis', FILENAME_SPECIALS_GRATIS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_gratis_id . '&action=deleteconfirm'));
            $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
            $contents[] = array('text' => '<br /><b>' . $sInfo->products_name . '</b>');
            $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" value="' . BUTTON_DELETE . '"/>&nbsp;<a class="button" href="' . xtc_href_link(FILENAME_SPECIALS_GRATIS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_gratis_id) . '">' . BUTTON_CANCEL . '</a>');
            break;

        default:
            if (is_object($sInfo)) {
                $heading[] = array('text' => '<b>' . $sInfo->products_name . '</b>');
                $contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold; ">' . TEXT_MARKED_ELEMENTS . '</div><br />');
                $contents[] = array('align' => 'center', 'text' => '<div align="center"><a href="' . xtc_href_link(FILENAME_SPECIALS_GRATIS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_gratis_id . '&action=edit') . '"><button type="button" class="btn btn-primary">' . BUTTON_EDIT . '</button></a> <a href="' . xtc_href_link(FILENAME_SPECIALS_GRATIS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_gratis_id . '&action=delete') . '"><button type="button" class="btn btn-danger">' . BUTTON_DELETE . '</button></a></div>');
                $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . xtc_date_short($sInfo->specials_gratis_date_added));
                $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . xtc_date_short($sInfo->specials_gratis_last_modified));
                $contents[] = array('align' => 'center', 'text' => '<br />' . xtc_product_thumb_image($sInfo->products_image, $sInfo->products_name));
                $contents[] = array('text' => '<br />' . TEXT_INFO_ORIGINAL_PRICE . ' ' . $xtPrice->xtcFormat($sInfo->products_price, true));
                $contents[] = array('text' => '' . TEXT_INFO_NEW_PRICE . ' ' . $xtPrice->xtcFormat($sInfo->specials_gratis_new_products_price, true));
                if ($sInfo->products_price > 0) {
                    $contents[] = array('text' => '' . TEXT_INFO_PERCENTAGE . ' ' . number_format((double) 100 - (($sInfo->specials_gratis_new_products_price / $sInfo->products_price) * 100)) . '%');
                }
                $contents[] = array('text' => '<br />' . TEXT_INFO_EXPIRES_DATE . ' <b>' . xtc_date_short($sInfo->expires_date) . '</b>');
                $contents[] = array('text' => '' . TEXT_INFO_STATUS_CHANGE . ' ' . xtc_date_short($sInfo->date_status_change));
            }
            break;
    }
    if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
        $box = new box;
        $smarty->assign('BOX_RIGHT', $box->infoBox($heading, $contents));
    }
}

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
$smarty->caching = false;
$smarty->template_dir = DIR_FS_CATALOG . 'admin/templates/';
$smarty->compile_dir = DIR_FS_CATALOG . 'admin/templates_c';
$smarty->config_dir = DIR_FS_CATALOG . 'lang';
$smarty->display(CURRENT_ADMIN_TEMPLATE . '/specials_gratis.html');

require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
