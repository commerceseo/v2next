<?php
/* -----------------------------------------------------------------
 * 	$Id: new_product.php 1475 2015-07-22 20:49:33Z akausch $
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
defined("_VALID_XTC") or die("Direct access to this location isn't allowed.");
// BEGIN Hermes
require_once DIR_FS_CATALOG . 'includes/classes/class.hermes.php';
$hermes = new HermesAPI();
if ($hermes->getUsername() != '' && MODULE_SHIPPING_HERMESPROPS_STATUS == 'True') {
    $pclasses = $hermes->getPackageClasses();
    $hermes_options = array(
        'min_pclass' => 'XS',
    );
}
// END Hermes
if (($_GET['pID']) && (!$_POST)) {
    $product_query = xtc_db_query("SELECT 
									*, 
									date_format(p.products_date_available, '%Y-%m-%d') AS products_date_available 
									FROM 
										" . TABLE_PRODUCTS . " p, 
										" . TABLE_PRODUCTS_DESCRIPTION . " pd
									WHERE 
										p.products_id = '" . (int) $_GET['pID'] . "'
									AND 
										p.products_id = pd.products_id
									AND 
										pd.language_id = '" . (int) $_SESSION['languages_id'] . "'");

    $product = xtc_db_fetch_array($product_query);
    $pInfo = new objectInfo($product);
    // BEGIN Hermes
    if ($hermes->getUsername() != '' && MODULE_SHIPPING_HERMESPROPS_STATUS == 'True') {
        $hoptions = $hermes->getProductOptions((int) $_GET['pID']);
        if ($hoptions !== false) {
            $hermes_options = $hoptions;
        }
    }
    // END Hermes
} elseif ($_POST) {
    $pInfo = new objectInfo($_POST);
    $products_name = xtc_db_prepare_input($_POST['products_name']);
    $products_description = xtc_db_prepare_input($_POST['products_description']);
    $products_short_description = xtc_db_prepare_input($_POST['products_short_description']);
    $products_cart_description = xtc_db_prepare_input($_POST['products_cart_description']);
    $products_zusatz_description = xtc_db_prepare_input($_POST['products_zusatz_description']);
    $products_keywords = xtc_db_prepare_input($_POST['products_keywords']);
    $url_text = xtc_db_prepare_input($_POST['url_text']);
    $url_old_text = xtc_db_prepare_input($_POST['url_old_text']);
    $products_meta_title = xtc_db_prepare_input($_POST['products_meta_title']);
    $products_meta_description = xtc_db_prepare_input($_POST['products_meta_description']);
    $products_meta_keywords = xtc_db_prepare_input($_POST['products_meta_keywords']);
    $products_google_taxonomie = xtc_db_prepare_input($_POST['products_google_taxonomie']);
    $products_taxonomie = xtc_db_prepare_input($_POST['products_taxonomie']);
    $products_treepodia_catch_phrase_1 = xtc_db_prepare_input($_POST['products_treepodia_catch_phrase_1']);
    $products_treepodia_catch_phrase_2 = xtc_db_prepare_input($_POST['products_treepodia_catch_phrase_2']);
    $products_treepodia_catch_phrase_3 = xtc_db_prepare_input($_POST['products_treepodia_catch_phrase_3']);
    $products_treepodia_catch_phrase_4 = xtc_db_prepare_input($_POST['products_treepodia_catch_phrase_4']);
    $products_tag_cloud = xtc_db_prepare_input($_POST['products_tag_cloud']);
    $products_url = xtc_db_prepare_input($_POST['products_url']);
    $pInfo->products_startpage = xtc_db_prepare_input($_POST['products_startpage']);
    $pInfo->products_buyable = xtc_db_prepare_input($_POST['products_buyable']);
    $pInfo->products_rel = xtc_db_prepare_input($_POST['products_rel']);
    $products_startpage_sort = xtc_db_prepare_input($_POST['products_startpage_sort']);
    // BEGIN Hermes
    if ($hermes->getUsername() != '') {
        $hermes_options = array(
            'min_pclass' => xtc_db_prepare_input($_POST['hermes_minpclass']),
        );
    }
    // END Hermes
} else {
    $pInfo = new objectInfo(array());
    $pInfo->products_buyable = '1';
    $pInfo->products_rel = '1';
}

$manufacturers_array = array(array('id' => '', 'text' => TEXT_NONE));
$manufacturers_query = xtc_db_query("SELECT manufacturers_id, manufacturers_name FROM " . TABLE_MANUFACTURERS . " ORDER BY manufacturers_name");
while ($manufacturers = xtc_db_fetch_array($manufacturers_query)) {
    $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
}

$vpe_array = array(array('id' => '', 'text' => TEXT_NONE));
$vpe_query = xtc_db_query("SELECT products_vpe_id, products_vpe_name FROM " . TABLE_PRODUCTS_VPE . " WHERE language_id='" . $_SESSION['languages_id'] . "' ORDER BY products_vpe_name");
while ($vpe = xtc_db_fetch_array($vpe_query)) {
    $vpe_array[] = array('id' => $vpe['products_vpe_id'], 'text' => $vpe['products_vpe_name']);
}

$tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
$tax_class_query = xtc_db_query("SELECT tax_class_id, tax_class_title FROM " . TABLE_TAX_CLASS . " ORDER BY tax_class_title");
while ($tax_class = xtc_db_fetch_array($tax_class_query)) {
    $tax_class_array[] = array('id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']);
}

$sperrgut_array = array(array('id' => '0', 'text' => TEXT_NONE));
$sperrgut_query = xtc_db_query("SELECT configuration_key, configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'SHIPPING\_SPERRGUT\_%' ORDER BY configuration_key");
while ($sperrgut = xtc_db_fetch_array($sperrgut_query)) {
    $xed = explode('_', $sperrgut['configuration_key']);
    $s_id = $xed[count($xed) - 1];
    $sperrgut_array[] = array('id' => $s_id, 'text' => TEXT_PRODUCTS_SPERRGUT . ' ' . $s_id . ' (' . $currencies->format($sperrgut['configuration_value']) . ')');
}

$conditions = array(array('id' => 'new', 'text' => 'Neu'));
$conditions[] = array('id' => 'used', 'text' => 'Gebraucht');
$conditions[] = array('id' => 'refurbished', 'text' => 'Erneuert');

$products_google_gender = array(array('id' => '---', 'text' => '---'));
$products_google_gender[] = array('id' => 'male', 'text' => 'Herren');
$products_google_gender[] = array('id' => 'female', 'text' => 'Damen');
$products_google_gender[] = array('id' => 'unisex', 'text' => 'Unisex');

$products_google_age_group = array(array('id' => '---', 'text' => '---'));
$products_google_age_group[] = array('id' => 'adult', 'text' => 'Erwachsene');
$products_google_age_group[] = array('id' => 'kids', 'text' => 'Kinder');

$products_g_availability = array(array('id' => 'in stock', 'text' => 'Auf Lager'));
$products_g_availability[] = array('id' => 'available for order', 'text' => 'Bestellbar');
$products_g_availability[] = array('id' => 'out of stock', 'text' => 'Vergriffen');
$products_g_availability[] = array('id' => 'preorder', 'text' => 'Vorbestellt');

$products_g_identifier = array(array('id' => 'TRUE', 'text' => 'TRUE'));
$products_g_identifier[] = array('id' => 'FALSE', 'text' => 'FALSE');

$shipping_statuses = array();
$shipping_statuses = xtc_get_shipping_status();
$languages = xtc_get_languages();

switch ($pInfo->products_status) {
    case '0' :
        $status = false;
        $out_status = true;
        break;
    case '1' :
    default :
        $status = true;
        $out_status = false;
}

if ($pInfo->products_startpage == '1') {
    $startpage_checked = true;
} else {
    $startpage_checked = false;
}
if ($pInfo->products_cartspecial == '1') {
    $cartspecial_checked = true;
} else {
    $cartspecial_checked = false;
}
if ($pInfo->products_buyable == '1') {
    $buyable_checked = true;
} else {
    $buyable_checked = false;
}
if ($pInfo->products_only_request == '1') {
    $only_request_checked = true;
} else {
    $only_request_checked = false;
}
if ($pInfo->products_rel == '1') {
    $products_rel_checked = true;
} else {
    $products_rel_checked = false;
}

if ($pInfo->products_treepodia_activate == '1') {
    $treepodia_checked = true;
} else {
    $treepodia_checked = false;
}

$counter_filter = xtc_db_query("SELECT COUNT(*) as counter FROM " . TABLE_PRODUCT_FILTER_ITEMS . " WHERE status = '1'");
$rows = xtc_db_fetch_array($counter_filter);
if ($rows['counter'] > 0) {
    $filter_list_active = 'true';
}

if (file_exists(DIR_WS_INCLUDES . 'addons/new_product_addon.php')) {
	include (DIR_WS_INCLUDES .'addons/new_product_addon.php');
}
?>
<script type="text/javascript" src="includes/javascript/categories.js"></script>
<script>
    $(function() {
        $("#accordion").accordion({
            heightStyle: "content"
        });
    });
</script>

<script type="text/javascript">
    $(function() {
        $('.datepickers').datepicker({
            minDate: new Date(<?php echo date('Y') . ',' . date('m') . '-1,' . date('d'); ?>),
            buttonImage: "images/calendar.png",
            showOn: "button",
            dateFormat: 'yy-mm-dd'});
    });
</script>
<tr>
    <td>
        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="pageHeading">
<?php echo sprintf(TEXT_NEW_PRODUCT, xtc_output_generated_category_path($current_category_id)); ?>
<?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                        (<?php
                        echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;';
                        echo xtc_get_products_name($pInfo->products_id, $languages[$i]['id']);
                        ?>)
                    <?php } ?> 
                </td>
            </tr>

        </table>

<?php
$form_action = ($_GET['pID']) ? 'update_product' : 'insert_product';
$fsk18_array = array(array('id' => 0, 'text' => NO), array('id' => 1, 'text' => YES));
$product_type_array = array(array('id' => 1, 'text' => TEXT_DEFAULT), array('id' => 2, 'text' => TEXT_DOWNLOAD), array('id' => 3, 'text' => TEXT_SERVICE));
echo xtc_draw_form('new_product', FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID'] . '&action=' . $form_action . '#pID=' . $_GET['pID'], 'post', 'enctype="multipart/form-data"');
?>

        <div align="right" style="display:block; text-align:right">
            <input type="submit" name="save" class="button" value="<?php echo BUTTON_SAVE; ?>">
            <input type="submit" id="btnUpdate" name="cseo_update" class="button" value="<?php echo BUTTON_UPDATE; ?>">
            <input type="submit" name="save_as_new_product" class="button" value="<?php echo BUTTON_NEWPRODUCT; ?>">
<?php echo '<a class="button" href="' . xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID']) . '">' . BUTTON_CANCEL . '</a>'; ?>
        </div>
            <?php if (ADMIN_CSEO_TABS_VIEW == 'true') { ?>
            <div id="prodtabs">
                <ul>
                    <li><a href="#prodbase"><?php echo HEAD_CONFIGURATION; ?></a></li>
                    <li><a href="#proddescr"><?php echo HEAD_DESCRIPTION; ?></a></li>
					<?php 
					if (file_exists(DIR_WS_INCLUDES . 'addons/new_product_tabs_nav_addon.php')) {
						include (DIR_WS_INCLUDES .'addons/new_product_tabs_nav_addon.php');
					} 
					?>
                    <li><a href="#prodimg"><?php echo HEAD_IMAGES; ?></a></li>
                    <li><a href="#prodgoogle"><?php echo HEAD_GOOGLE; ?></a></li>
                    <?php if (TREEPODIAACTIVE == 'true') { ?>
                        <li><a href="#prodtreepodia">Treepodia</a></li>
                    <?php } ?>
                </ul>
                <?php } ?>
            <div id="prodbase">
                <table width="100%"  border="0">
                    <tr>
                        <td valign="top" width="50%">
                            <table width="100%" border="0">
                                <tr>
                                    <td class="main" width="50%">
<?php echo TEXT_PRODUCTS_STATUS; ?> 
<?php echo '&nbsp;' . xtc_draw_radio_field('products_status', '1', $status) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . xtc_draw_radio_field('products_status', '0', $out_status) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?>
                                    </td>
                                    <td class="main" width="50%">
                                        <?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?><small>(YYYY-MM-DD)</small>
                                        <input type="text" name="products_date_available" class="datepickers" value="<?php echo ($pInfo->products_date_available != '00.00.0000' ? $pInfo->products_date_available : ''); ?>" />
                                    </td>
                                </tr>
                            </table>			
                            <table width="100%" border="0">
                                <tr>
                                    <td nowrap="nowrap" class="main"><?php echo TEXT_PRODUCTS_ADDED; ?></td>
                                    <td class="main">
                                        <input type="text" name="products_date_added" class="datepickers" value="<?php echo ($pInfo->products_date_added != '00.00.0000' ? $pInfo->products_date_added : ''); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td nowrap="nowrap" class="main"><?php echo TEXT_PRODUCTS_STARTPAGE; ?></td>
                                    <td class="main"><?php echo TEXT_PRODUCTS_STARTPAGE_YES . '&nbsp;' . xtc_draw_radio_field('products_startpage', '1', $startpage_checked) . '&nbsp;' . TEXT_PRODUCTS_STARTPAGE_NO . '&nbsp;' . xtc_draw_radio_field('products_startpage', '0', !$startpage_checked) ?></td>
                                </tr>
                                <tr>
                                    <td nowrap="nowrap" class="main"><?php echo TEXT_PRODUCTS_STARTPAGE_SORT; ?></td>
                                    <td class="main"><?php echo xtc_draw_input_field('products_startpage_sort', $pInfo->products_startpage_sort); ?></td>
                                </tr>
                                <tr>
                                    <td class="main"><?php echo TEXT_PRODUCTS_SORT; ?></td>
                                    <td class="main"><?php echo xtc_draw_input_field('products_sort', $pInfo->products_sort); ?></td>
                                </tr>
                                <tr>   
                                    <td class="main"><?php echo TEXT_PRODUCTS_CART_SPECIALS; ?></td>
                                    <td class="main"><?php echo TEXT_PRODUCTS_STARTPAGE_YES . '&nbsp;' . xtc_draw_radio_field('products_cartspecial', '1', $cartspecial_checked) . '&nbsp;' . TEXT_PRODUCTS_STARTPAGE_NO . '&nbsp;' . xtc_draw_radio_field('products_cartspecial', '0', !$cartspecial_checked) ?></td>
                                </tr>
                                <tr>   
                                    <td class="main"><?php echo TEXT_PRODUCTS_BUYABLE; ?></td>
                                    <td class="main"><?php echo TEXT_PRODUCTS_STARTPAGE_YES . '&nbsp;' . xtc_draw_radio_field('products_buyable', '1', $buyable_checked) . '&nbsp;' . TEXT_PRODUCTS_STARTPAGE_NO . '&nbsp;' . xtc_draw_radio_field('products_buyable', '0', !$buyable_checked) ?></td>
                                </tr>
                                <tr>   
                                    <td class="main"><?php echo TEXT_PRODUCTS_ONLY_REQUEST; ?></td>
                                    <td class="main"><?php echo TEXT_PRODUCTS_STARTPAGE_YES . '&nbsp;' . xtc_draw_radio_field('products_only_request', '1', $only_request_checked) . '&nbsp;' . TEXT_PRODUCTS_STARTPAGE_NO . '&nbsp;' . xtc_draw_radio_field('products_only_request', '0', !$only_request_checked) ?></td>
                                </tr>
                                <tr>   
                                    <td class="main"><?php echo TEXT_PRODUCTS_REL; ?> </td>
                                    <td class="main"><?php echo TEXT_PRODUCTS_STARTPAGE_YES . '&nbsp;' . xtc_draw_radio_field('products_rel', '1', $products_rel_checked) . '&nbsp;' . TEXT_PRODUCTS_STARTPAGE_NO . '&nbsp;' . xtc_draw_radio_field('products_rel', '0', !$products_rel_checked) ?></td>
                                </tr>
                                <tr>
                                    <td class="main"><?php echo TEXT_PRODUCTS_EAN; ?></td>
                                    <td class="main"><?php echo xtc_draw_input_field('products_ean', $pInfo->products_ean); ?></td>
                                </tr>
                                <tr>
                                    <td class="main"><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></td>
                                    <td class="main"><?php echo xtc_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id); ?></td>
                                </tr>
								<tr>
									<td class="main"><?php echo TEXT_PRODUCTS_FREE_SHIPPING; ?></td>
									<td class="main"><?php echo xtc_draw_selection_field('free_shipping', 'checkbox', '1',$pInfo->free_shipping==1 ? true : false); ?></td>
								</tr>
								<tr>
									<td class="main"><?php echo TEXT_PRODUCTS_MAX_FREE_SHIPPING_CART; ?></td>
									<td class="main"><?php echo xtc_draw_input_field('max_free_shipping_cart', $pInfo->max_free_shipping_cart, 'size=3'); ?></td>
								</tr>
								<tr>
									<td class="main"><?php echo TEXT_PRODUCTS_MAX_FREE_SHIPPING_AMOUNT; ?></td>
									<td class="main"><?php echo xtc_draw_input_field('max_free_shipping_amount', $pInfo->max_free_shipping_amount, 'size=3'); ?></td>
								</tr>
                                <tr>
                                    <td colspan="2" class="main"><?php draw_product_fields($pInfo); ?></td>
                                </tr>
                                <tr>
                                    <td style="border-top: 1px solid #376e37; border-left: 1px solid #376e37;" class="main"><?php echo TEXT_PRODUCTS_VPE_VISIBLE; ?></td>
                                    <td style="border-top: 1px solid #376e37; border-right: 1px solid #376e37;" class="main"><?php echo xtc_draw_selection_field('products_vpe_status', 'checkbox', '1', $pInfo->products_vpe_status == 1 ? true : false); ?></td>
                                </tr>
                                <tr>
                                    <td style="border-left: 1px solid #376e37;" class="main"><?php echo TEXT_PRODUCTS_VPE_VALUE; ?></td>
                                    <td style="border-right: 1px solid #376e37;" class="main"><?php echo xtc_draw_input_field('products_vpe_value', $pInfo->products_vpe_value); ?></td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid #376e37; border-left: 1px solid #376e37;" class="main"><?php echo TEXT_PRODUCTS_VPE; ?></td>
                                    <td style="border-bottom: 1px solid #376e37; border-right: 1px solid #376e37;" class="main"><?php echo xtc_draw_pull_down_menu('products_vpe', $vpe_array, $pInfo->products_vpe = '' ? DEFAULT_PRODUCTS_VPE_ID : $pInfo->products_vpe); ?></td>
                                </tr>
                                <tr>
                                    <td class="main"><?php echo TEXT_FSK18; ?></td>
                                    <td class="main"><?php echo xtc_draw_pull_down_menu('fsk18', $fsk18_array, $pInfo->products_fsk18); ?></td>
                                </tr>
                                <tr>
                                    <td class="main"><?php echo TEXT_PRODUCT_TYPE; ?></td>
                                    <td class="main"><?php echo xtc_draw_pull_down_menu('product_type', $product_type_array, $pInfo->product_type); ?></td>
                                </tr>
                                <tr>
                                    <td class="main"><?php echo TEXT_PRODUCTS_WEIGHT; ?></td>
                                    <td class="main"><?php echo xtc_draw_input_field('products_weight', $pInfo->products_weight); ?><?php echo TEXT_PRODUCTS_WEIGHT_INFO; ?></td>
                                </tr>
								<?php 
								if (file_exists(DIR_WS_INCLUDES . 'addons/new_product_fields_addon.php')) {
									include (DIR_WS_INCLUDES .'addons/new_product_fields_addon.php');
								}
								?>

<?php if (ACTIVATE_SHIPPING_STATUS == 'true') { ?>
                                    <tr>
                                        <td class="main"><?php echo BOX_SHIPPING_STATUS; ?></td>
                                        <td class="main"><?php echo xtc_draw_pull_down_menu('shipping_status', $shipping_statuses, $pInfo->products_shippingtime == '' ? (int) (DEFAULT_SHIPPING_STATUS_ID) : $pInfo->products_shippingtime); ?></td>
                                    </tr>
<?php } ?>
<?php
// BEGIN HERMES 
if ($hermes->getUsername() != '') {
    ?>
                                    <tr>
                                        <td colspan="2">Hermes ProfiPaketService</td>
                                    </tr>
                                    <tr>
                                        <td class="label"><label for="hermes_minpclass">Paketklasse mindestens:</label></td>
                                        <td>
                                            <select id="hermes_minpclass" name="hermes_minpclass" size="1">
    <?php foreach ($pclasses as $pclass) { ?>
                                                    <option value="<?php echo $pclass['name'] ?>" <?php echo $pclass['name'] == $hermes_options['min_pclass'] ? 'selected' : '' ?>><?php echo $pclass['name'] . ' - ' . $pclass['desc'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
    <?php
}
// END HERMES 
?>

                                <tr>
                                <?php
                                $files = array();
                                if ($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_info/')) {
                                    while (($file = readdir($dir)) !== false) {
                                        if (is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_info/' . $file) and ($file != "index.html")) {
                                            $files[] = array('id' => $file, 'text' => $file);
                                        } //if
                                    } // while
                                    closedir($dir);
                                }
                                $default_array = array();
                                // set default value in dropdown!
                                if ($content['content_file'] == '') {
                                    $default_array[] = array('id' => 'default', 'text' => TEXT_SELECT);
                                    $default_value = $pInfo->product_template;
                                    $files = array_merge($default_array, $files);
                                } else {
                                    $default_array[] = array('id' => 'default', 'text' => TEXT_NO_FILE);
                                    $default_value = $pInfo->product_template;
                                    $files = array_merge($default_array, $files);
                                }
                                echo '<td class="main">' . TEXT_CHOOSE_INFO_TEMPLATE . '</td>';
                                echo '<td class="main">' . xtc_draw_pull_down_menu('info_template', $files, $default_value) . '</td>';
                                ?>
                                </tr>
                                <tr>
                                    <?php
                                    $files = array();
                                    if ($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_options/')) {
                                        while (($file = readdir($dir)) !== false) {
                                            if (is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_options/' . $file) and ($file != "index.html")) {
                                                $files[] = array('id' => $file, 'text' => $file);
                                            } //if
                                        } // while
                                        closedir($dir);
                                    }
                                    // set default value in dropdown!
                                    $default_array = array();
                                    if ($content['content_file'] == '') {
                                        $default_array[] = array('id' => 'default', 'text' => TEXT_SELECT);
                                        $default_value = $pInfo->options_template;
                                        $files = array_merge($default_array, $files);
                                    } else {
                                        $default_array[] = array('id' => 'default', 'text' => TEXT_NO_FILE);
                                        $default_value = $pInfo->options_template;
                                        $files = array_merge($default_array, $files);
                                    }
                                    echo '<td class="main">' . TEXT_CHOOSE_OPTIONS_TEMPLATE . ':' . '</td>';
                                    echo '<td class="main">' . xtc_draw_pull_down_menu('options_template', $files, $default_value) . '</td>';
                                    ?>
                                </tr>	  
                                <tr>
                                    <td class="main" valign="top">
                                        <table width="100%">
                                            <tr>
                                                <td align="left" valign="top">
<?php echo TEXT_TEMPLATE_COLUMN; ?>
                                                </td>
                                                <td align="right">
                                                    <?php echo xtc_image('images/template_prod.gif', 'Produkt Detail', 'align="right"'); ?></td>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="main">
<?php
if (isset($_GET['pID'])) {
    echo xtc_draw_selection_field('products_col_top', 'checkbox', '1', $pInfo->products_col_top == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_TOP . '<br>';
    echo xtc_draw_selection_field('products_col_left', 'checkbox', '1', $pInfo->products_col_left == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_LEFT . '<br>';
    echo xtc_draw_selection_field('products_col_right', 'checkbox', '1', $pInfo->products_col_right == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_RIGHT . '<br>';
    echo xtc_draw_selection_field('products_col_bottom', 'checkbox', '1', $pInfo->products_col_bottom == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_BUTTON . '<br>';
} else {
    echo xtc_draw_selection_field('products_col_top', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_TOP . '<br>';
    echo xtc_draw_selection_field('products_col_left', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_LEFT . '<br>';
    echo xtc_draw_selection_field('products_col_right', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_RIGHT . '<br>';
    echo xtc_draw_selection_field('products_col_bottom', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_BUTTON . '<br>';
}
?>
                                    </td>
                                </tr>	  
                            </table>
                        </td>
                        <td valign="top" style="border-left: 1px dashed #ccc">
<?php
if (GROUP_CHECK == 'true') {
    $customers_statuses_array = xtc_get_customers_statuses();
    $customers_statuses_array = array_merge(array(array('id' => 'all', 'text' => TXT_ALL)), $customers_statuses_array);
    ?>
                                <table width="100%" border="0">
                                    <tr>
                                        <td valign="top" class="main" ><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
                                        <td class="main">
    <?php
    for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
        $code = '$id=$pInfo->group_permission_' . $customers_statuses_array[$i]['id'] . ';';
        eval($code);
        if ($id == 1) {
            $checked = 'checked ';
        } else {
            $checked = '';
        }
        echo '<input type="checkbox" name="groups[]" value="' . $customers_statuses_array[$i]['id'] . '"' . $checked . '> ' . $customers_statuses_array[$i]['text'] . '<br>';
    }
    ?>
                                        </td>
                                    </tr>
                                </table>
                                        <?php } ?>
                            <h3><?php echo PRICE_TITLE; ?></h3>
                            <div>
                                <table width="100%" border="0">
                                    <tr>
                                        <td>
<?php
if (file_exists(DIR_WS_MODULES . 'product_prices.php')) {
    include(DIR_WS_MODULES . 'product_prices.php');
}
?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div id="accordion">
                                <h3><?php echo PRICE_TITLE_ADVANCED; ?></h3>
                                <div>
                                    <table width="100%" border="0">
                                        <tr>
                                            <td>
<?php
if (file_exists(DIR_WS_MODULES . 'product_prices_advanced.php')) {
    include(DIR_WS_MODULES . 'product_prices_advanced.php');
}
?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                        </td>
                    </tr>

                </table>

                <br class="clear">
            </div>
				
            <div id="proddescr">
<?php draw_product_desc_fields($pInfo);?>
                <div id="tabslang">
                    <ul>
<?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                            <li>
                                <a href="#language_<?php echo $i; ?>">
                                    <span> <?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' ' . $languages[$i]['name']; ?></span>
                                </a>
                            </li>
<?php } ?>
                    </ul>
					
<?php for ($i = 0; $i < sizeof($languages); $i++) { ?>
                        <div id="language_<?php echo $i; ?>">
                            <table width="100%" border="0">
                                <tr>
                                    <td width="50%" valign="top">
                                        <span><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;<?php echo $languages[$i]['name'] ?></span>
                                        <table width="100%" border="0">
                                            <tr>
                                                <td style="background:#e2e2e2" valign="top" class="main"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;<?php echo TEXT_PRODUCTS_NAME; ?> <?php echo xtc_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_name')), 'style="width:98%"'); ?></td>
                                            </tr>
                                            <tr>
                                                <td style="background:#fafafa" valign="top" class="main"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;URL Alias<em>(optional)</em>: <?php echo xtc_draw_input_field('products_url_alias[' . $languages[$i]['id'] . ']', (($products_url_alias[$languages[$i]['id']]) ? stripslashes($products_url_alias[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_url_alias')), 'style="width:98%"'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="main">
    <?php
    echo TEXT_PRODUCTS_URL;
    echo xtc_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_url')), 'style="width:98%"0');
    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="main">
    <?php echo TEXT_PRODUCTS_OLD_URL; ?><br>
    <?php echo xtc_draw_input_field('url_old_text[' . $languages[$i]['id'] . ']', (($url_old_text[$languages[$i]['id']]) ? stripslashes($url_old_text[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'url_old_text')), 'style="width:98%"'); ?><br>     
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td valign="top" align="right" >
                                        <table width="80%">
                                            <tr>
                                                <td valign="top">
    <?php echo TEXT_PRODUCTS_KEYWORDS; ?><br>
                                                    <?php echo xtc_draw_input_field('products_keywords[' . $languages[$i]['id'] . ']', (($products_keywords[$languages[$i]['id']]) ? stripslashes($products_keywords[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_keywords')), 'style="width:98%"'); ?><br>     
                                                    <?php echo TEXT_META_TITLE; ?><br>
                                                    <?php echo xtc_draw_input_field('products_meta_title[' . $languages[$i]['id'] . ']', (($products_meta_title[$languages[$i]['id']]) ? stripslashes($products_meta_title[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_meta_title')), 'style="width:98%"'); ?><br>
                                                    <?php echo TEXT_META_DESCRIPTION; ?><br>
                                                    <?php echo xtc_draw_input_field('products_meta_description[' . $languages[$i]['id'] . ']', (($products_meta_description[$languages[$i]['id']]) ? stripslashes($products_meta_description[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_meta_description')), 'style="width:98%"'); ?><br>
                                                    <?php echo TEXT_META_KEYWORDS; ?><br>
                                                    <?php echo xtc_draw_input_field('products_meta_keywords[' . $languages[$i]['id'] . ']', (($products_meta_keywords[$languages[$i]['id']]) ? stripslashes($products_meta_keywords[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_meta_keywords')), 'style="width:98%"'); ?> <br>
													

                                                    <script type="text/javascript">
    <!--
                                                            jQuery(document).ready(function() {
        jQuery('a.ajax_<?php echo $languages[$i]['id'] ?>').click(function() {
            jQuery.ajax({
                type: "POST",
                url: "includes/javascript/new_input.php",
                data: {'language':<?php echo $languages[$i]['id'] ?>},
                success: function(msg) {
                    jQuery('#quote_<?php echo $languages[$i]['id'] ?>').fadeIn("slow").append(msg);
                }
            });
        });
    });
    //-->
                                                    </script>
                                                    Tag Cloud:<br>
    <?php
    $data_query = xtc_db_query("SELECT tag FROM tag_to_product WHERE lID = '" . $languages[$i]['id'] . "' AND pID = '" . $pInfo->products_id . "';");
    if (xtc_db_num_rows($data_query)) {
        while ($data = xtc_db_fetch_array($data_query)) {
            if (!empty($data)) {
                echo xtc_draw_input_field('products_tag_cloud[' . $languages[$i]['id'] . '][]', $data['tag'], 'style="width:98%"') . '<br>';
            }
        }
    } else {
        echo xtc_draw_input_field('products_tag_cloud[' . $languages[$i]['id'] . '][]', '', 'style="width:98%"') . '<br>';
    }
    ?>
                                                    <div id="quote_<?php echo $languages[$i]['id'] ?>"></div>
                                                    <a class="ajax_<?php echo $languages[$i]['id'] ?>" href="#" onclick="javascript:return false;"><?php echo TEXT_PRODUCTS_TAGCLOUD; ?></a>        
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" border="0">
                                <tr>
                                    <td>
                                        <strong><?php echo TEXT_PRODUCTS_DESCRIPTION; ?></strong><br>
    <?php echo xtc_draw_textarea_field('products_description[' . $languages[$i]['id'] . ']', 'soft', '103', '30', (($products_description[$languages[$i]['id']]) ? stripslashes($products_description[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_description')), ''); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top">
                                        <strong><?php echo TEXT_PRODUCTS_SHORT_DESCRIPTION; ?></strong><br>
    <?php echo xtc_draw_textarea_field('products_short_description[' . $languages[$i]['id'] . ']', 'soft', '103', '20', (($products_short_description[$languages[$i]['id']]) ? stripslashes($products_short_description[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_short_description')), ''); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top">
                                        <strong><?php echo TEXT_PRODUCTS_ZUSATZ_DESCRIPTION; ?></strong><br>
    <?php echo xtc_draw_textarea_field('products_zusatz_description[' . $languages[$i]['id'] . ']', 'soft', '103', '20', (($products_zusatz_description[$languages[$i]['id']]) ? stripslashes($products_zusatz_description[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_zusatz_description')), ''); ?>
                                    </td>
                                </tr>			
                                <tr>
                                    <td valign="top">
                                        <strong><?php echo TEXT_PRODUCTS_CART_DESCRIPTION; ?></strong><br>
    <?php echo xtc_draw_textarea_field('products_cart_description[' . $languages[$i]['id'] . ']', 'soft', '103', '20', (($products_cart_description[$languages[$i]['id']]) ? stripslashes($products_cart_description[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_cart_description')), ''); ?>
                                    </td>
                                </tr>
                            </table>
<?php
if (USE_WYSIWYG == 'true') {
	echo "<script src=\"includes/ckeditor/ckeditor.js\"></script>";
	if (file_exists('includes/ckfinder/ckfinder.js')) {
		echo "<script src=\"includes/ckfinder/ckfinder.js\"></script>
				<script>
					var newCKEdit = CKEDITOR.replace('products_description[" . $languages[$i]['id'] . "]');
					CKFinder.setupCKEditor(newCKEdit, 'includes/ckfinder/');
				</script>
				<script>
					var newCKEdit = CKEDITOR.replace('products_short_description[" . $languages[$i]['id'] . "]');
					CKFinder.setupCKEditor(newCKEdit, 'includes/ckfinder/');
				</script>
				<script>
					var newCKEdit = CKEDITOR.replace('products_zusatz_description[" . $languages[$i]['id'] . "]');
					CKFinder.setupCKEditor(newCKEdit, 'includes/ckfinder/');
				</script>
				<script>
					var newCKEdit = CKEDITOR.replace('products_cart_description[" . $languages[$i]['id'] . "]');
					CKFinder.setupCKEditor(newCKEdit, 'includes/ckfinder/');
				</script>";
	} else {
	echo "<script>
			CKEDITOR.replace('products_description[" . $languages[$i]['id'] . "]', {
				toolbar: \"ImageMapper\",
				language: \"" . $_SESSION['language_code'] . "\",
				baseHref: \"" . (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . "\",
				filebrowserBrowseUrl: \"includes/ckeditor/filemanager/index.html\"
			});
		</script>
		<script>
			CKEDITOR.replace('products_short_description[" . $languages[$i]['id'] . "]', {
				toolbar: \"ImageMapper\",
				language: \"" . $_SESSION['language_code'] . "\",
				baseHref: \"" . (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . "\",
				filebrowserBrowseUrl: \"includes/ckeditor/filemanager/index.html\"
			});
		</script>
		<script>
			CKEDITOR.replace('products_zusatz_description[" . $languages[$i]['id'] . "]', {
				toolbar: \"ImageMapper\",
				language: \"" . $_SESSION['language_code'] . "\",
				baseHref: \"" . (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . "\",
				filebrowserBrowseUrl: \"includes/ckeditor/filemanager/index.html\"
			});
		</script>
		<script>
			CKEDITOR.replace('products_cart_description[" . $languages[$i]['id'] . "]', {
				toolbar: \"ImageMapper\",
				language: \"" . $_SESSION['language_code'] . "\",
				baseHref: \"" . HTTP_SERVER . DIR_WS_CATALOG . "\",
				filebrowserBrowseUrl: \"includes/ckeditor/filemanager/index.html\"
			});
		</script>";			
	}
}
?>
                        </div>
                        <?php } ?>
                    <br class="clear">
                </div>
            </div>
            
			<?php 
			if (file_exists(DIR_WS_INCLUDES . 'addons/new_product_tabs_addon.php')) {
				include (DIR_WS_INCLUDES .'addons/new_product_tabs_addon.php');
			} 
			?>
			<div id="prodimg">
                <h3><?php echo TEXT_PRODUCTS_IMAGES_FLASH; ?></h3>
                <table width="100%" border="0">
                    <tr>
                        <td width="49%" valign="top">
                            <table width="100%" border="0" cellpadding="3">
<?php include (DIR_WS_MODULES . 'products_images.php'); ?>
                            </table>
                        </td>
                        <td width="1%">
                            &nbsp;
                        </td>
                        <td valign="top">
                            <table width="100%" border="0" cellpadding="3">
                                <tr>
                                    <td class="main" colspan="2"><?php echo TEXT_PRODUCTS_FLASH_UPLOAD; ?><br></td>
                                </tr>
                                <tr>
                                    <td class="main"><?php echo TEXT_PRODUCTS_EMBEDDED_FLASH; ?></td>
                                    <td class="main">
<?php echo xtc_draw_textarea_field('products_movie_embeded_code', 'soft', '1', '5', $pInfo->products_movie_embeded_code, 'style="width:90%"'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="main"><?php echo TEXT_PRODUCTS_YOUTUBE_ID; ?></td>
                                    <td class="main">
<?php echo xtc_draw_input_field('products_movie_youtube_id', $pInfo->products_movie_youtube_id, 'style="width:90%"'); ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    <tr>
                </table>
                <br class="clear">
            </div>
            <div id="prodgoogle">
                <h3><?php echo TEXT_PRODUCTS_GOOGLE; ?></h3>
                <table width="100%" border="0">
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_ZUSTAND; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_pull_down_menu('products_zustand', $conditions, $pInfo->products_zustand); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_ZUSTAND; ?></td>
                    </tr>
<?php
for ($i = 0; $i < sizeof($languages); $i++) {
?>
                    <tr>
                        <td class="main" width="15%"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;<?php echo TEXT_PRODUCTS_GOOGLE_TAXONOMIE; ?></td>
                        <td class="main" width="60%">
<?php
    echo xtc_draw_input_field('products_google_taxonomie[' . $languages[$i]['id'] . ']', (($products_products_google_taxonomie[$languages[$i]['id']]) ? stripslashes($products_products_google_taxonomie[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_google_taxonomie')), 'id="GOOGLE_MERCHANT'.($languages[$i]['code'] == 'en' ? 'EN' : '').'" style="width:90%"');
?>
                        </td>
                        <td class="main" width="25%"><?php echo P_HELP_GOOGLE_TAXONOMIE; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;<?php echo TEXT_PRODUCTS_GOOGLE_TAXONOMIE_NEW; ?></td>
                        <td class="main" width="60%"><?php echo (($languages[$i]['code'] == 'de') ? '<span id="google_taxonomy_de"></span>' : '<span id="google_taxonomy_en"></span>'); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_GOOGLE_TAXONOMIE_NEW; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;<?php echo TEXT_PRODUCTS_TAXONOMIE; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_input_field('products_taxonomie[' . $languages[$i]['id'] . ']', (($products_products_taxonomie[$languages[$i]['id']]) ? stripslashes($products_products_taxonomie[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_taxonomie')), 'style="width:90%"');?>
                        </td>
                        <td class="main" width="25%"><?php echo P_HELP_PRODUCTS_TAXONOMIE; ?></td>
                    </tr>
<?php 
}
?>
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_G_GENDER; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_pull_down_menu('products_google_gender', $products_google_gender, $pInfo->products_google_gender); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_G_GENDER; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_G_AGE; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_pull_down_menu('products_google_age_group', $products_google_age_group, $pInfo->products_google_age_group); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_G_AGE; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_G_COLOR; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_input_field('products_google_color', $pInfo->products_google_color, 'size=50'); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_G_COLOR; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_G_SIZE; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_input_field('products_google_size', $pInfo->products_google_size, 'size=50'); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_G_SIZE; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_G_ISBN; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_input_field('products_isbn', $pInfo->products_isbn, 'size=50'); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_G_ISBN; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_G_UPC; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_input_field('products_upc', $pInfo->products_upc, 'size=50'); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_G_UPC; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_G_MPN; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_input_field('products_manufacturers_model', $pInfo->products_manufacturers_model, 'size=50'); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_G_MPN; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_G_BRAND; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_input_field('products_brand_name', $pInfo->products_brand_name, 'size=50'); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_G_BRAND; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_G_IDENTIFIER; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_pull_down_menu('products_g_identifier', $products_g_identifier, $pInfo->products_g_identifier); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_G_IDENTIFIER; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_G_AVAILABILITY; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_pull_down_menu('products_g_availability', $products_g_availability, $pInfo->products_g_availability); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_G_AVAILABILITY; ?></td>
                    </tr>
                    <tr>
                        <td class="main" width="15%"><?php echo TEXT_PRODUCTS_G_SHIPPING_STATUS; ?></td>
                        <td class="main" width="60%"><?php echo xtc_draw_input_field('products_g_shipping_status', $pInfo->products_g_shipping_status, 'size=50'); ?></td>
                        <td class="main" width="25%"><?php echo P_HELP_G_SHIPPING_STATUS; ?></td>
                    </tr>

                </table>
                <br class="clear">
            </div>
                <?php } ?>
                <?php if (TREEPODIAACTIVE == 'true') { ?>
                <div id="prodtreepodia">
                <?php echo TEXT_PRODUCTS_TREEPODIA_ACTIVE; ?> <br>
                <?php echo TEXT_PRODUCTS_TREEPODIA_YES . '&nbsp;' . xtc_draw_radio_field('products_treepodia_activate', '1', $treepodia_checked) . '&nbsp;' . TEXT_PRODUCTS_TREEPODIA_NO . '&nbsp;' . xtc_draw_radio_field('products_treepodia_activate', '0', !$treepodia_checked) ?><br>
                <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                        <?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?><?php echo TEXT_PRODUCTS_TREEPODIA_1; ?><br>
                        <?php echo xtc_draw_input_field('products_treepodia_catch_phrase_1[' . $languages[$i]['id'] . ']', (($products_products_treepodia_catch_phrase_1[$languages[$i]['id']]) ? stripslashes($products_products_treepodia_catch_phrase_1[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_treepodia_catch_phrase_1')), 'style="width:98%"'); ?> <br>
                        <?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;<?php echo TEXT_PRODUCTS_TREEPODIA_2; ?><br>
                        <?php echo xtc_draw_input_field('products_treepodia_catch_phrase_2[' . $languages[$i]['id'] . ']', (($products_products_treepodia_catch_phrase_2[$languages[$i]['id']]) ? stripslashes($products_products_treepodia_catch_phrase_2[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_treepodia_catch_phrase_2')), 'style="width:98%"'); ?> <br>
                        <?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;<?php echo TEXT_PRODUCTS_TREEPODIA_3; ?><br>
                        <?php echo xtc_draw_input_field('products_treepodia_catch_phrase_3[' . $languages[$i]['id'] . ']', (($products_products_treepodia_catch_phrase_3[$languages[$i]['id']]) ? stripslashes($products_products_treepodia_catch_phrase_3[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_treepodia_catch_phrase_3')), 'style="width:98%"'); ?> <br>
                        <?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;<?php echo TEXT_PRODUCTS_TREEPODIA_4; ?><br>
                        <?php echo xtc_draw_input_field('products_treepodia_catch_phrase_4[' . $languages[$i]['id'] . ']', (($products_products_treepodia_catch_phrase_4[$languages[$i]['id']]) ? stripslashes($products_products_treepodia_catch_phrase_4[$languages[$i]['id']]) : xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_treepodia_catch_phrase_4')), 'style="width:98%"'); ?> <br>
                    <?php } ?>
                    <br class="clear">
                </div>
                <?php } ?>
                <?php if (ADMIN_CSEO_TABS_VIEW == 'true') { ?>
            </div>
            <?php } ?>
        <br class="clear">
            <?php
            echo xtc_draw_hidden_field('products_id', $pInfo->products_id);
            ?>
        <div align="right" style="display:block; text-align:right">
            <input type="submit" name="save" class="button" value="<?php echo BUTTON_SAVE; ?>">
            <input type="submit" id="btnUpdate" name="cseo_update" class="button" value="<?php echo BUTTON_UPDATE; ?>">
            <input type="submit" name="save_as_new_product" class="button" value="<?php echo BUTTON_NEWPRODUCT; ?>">
        <?php echo '<a class="button" href="' . xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID']) . '">' . BUTTON_CANCEL . '</a>'; ?>
        </div>
    </form>


