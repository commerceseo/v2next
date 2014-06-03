<?php
/* -----------------------------------------------------------------
 * 	$Id: new_attributes.php 420 2013-06-19 18:04:39Z akausch $
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

require(DIR_WS_MODULES . 'new_attributes_config.php');
require(DIR_FS_INC . 'xtc_findTitle.inc.php');
require_once(DIR_FS_INC . 'xtc_format_filesize.inc.php');

if (isset($cPathID) && $_POST['action'] == 'change') {
    include(DIR_WS_MODULES . 'new_attributes_change.php');
    xtc_redirect('./' . FILENAME_CATEGORIES . '?cPath=' . $cPathID . '&pID=' . $_POST['current_product_id']);
}
require(DIR_WS_INCLUDES . 'header.php');
?>
<table class="outerTable" cellpadding="0" cellspacing="0">
    <?php
    switch ($_POST['action']) {
        case 'edit':
            if ($_POST['copy_product_id'] != 0) {
                $attrib_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id = " . $_POST['copy_product_id']);
                while ($attrib_res = xtc_db_fetch_array($attrib_query)) {
                    xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES . " 
								(products_id, options_id, options_values_id, options_values_price, price_prefix, attributes_model, attributes_stock, options_values_weight, weight_prefix, attributes_ean, attributes_vpe_status, attributes_vpe, attributes_vpe_value) 
								VALUES 
								('" . $_POST['current_product_id'] . "', 
									'" . $attrib_res['options_id'] . "', 
									'" . $attrib_res['options_values_id'] . "', 
									'" . $attrib_res['options_values_price'] . "', 
									'" . $attrib_res['price_prefix'] . "', 
									'" . $attrib_res['attributes_model'] . "', 
									'" . $attrib_res['attributes_stock'] . "', 
									'" . $attrib_res['options_values_weight'] . "', 
									'" . $attrib_res['weight_prefix'] . "', 
									'" . $attrib_res['attributes_ean'] . "', 
									'" . $attrib_res['attributes_vpe_status'] . "', 
									'" . $attrib_res['attributes_vpe'] . "', 
									'" . $attrib_res['attributes_vpe_value'] . "');");
                }
            }
            $pageTitle = TITLE_EDIT . ': ' . xtc_findTitle($_POST['current_product_id'], $languageFilter);
            include(DIR_WS_MODULES . 'new_attributes_include.php');
            break;

        case 'change':
            $pageTitle = TITLE_UPDATED;
            include(DIR_WS_MODULES . 'new_attributes_change.php');
            include(DIR_WS_MODULES . 'new_attributes_select.php');
            break;

        default:
            $pageTitle = TITLE_EDIT;
            include(DIR_WS_MODULES . 'new_attributes_select.php');
            break;
    }
    ?>
</table>

<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');