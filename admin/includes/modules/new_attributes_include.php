<?php
/* -----------------------------------------------------------------
 * 	$Id: new_attributes_include.php 872 2014-03-21 14:46:30Z akausch $
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

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
// include needed functions
require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
require_once(DIR_FS_INC . 'xtc_get_tax_class_id.inc.php');
// require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.xtcprice.php');
$xtPrice = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);
?>
<tr>
    <td class="pageHeading" colspan="8">
        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="pageHeading">
                    <?php echo $pageTitle; ?>
                </td>
            </tr>
        </table>
    </td>
</tr>
<form action="<?php echo basename($_SERVER['SCRIPT_NAME']); ?>" method="post" name="SUBMIT_ATTRIBUTES" enctype="multipart/form-data">
    <input type="hidden" name="current_product_id" value="<?php echo $_POST['current_product_id']; ?>">
    <input type="hidden" name="action" value="change">
    <?php
    echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
    if ($cPath) {
        echo '<input type="hidden" name="cPathID" value="' . $cPath . '">';
    }

    require(DIR_WS_MODULES . 'new_attributes_functions.php');

// Temp id for text input contribution.. I'll put them in a seperate array.
    $tempTextID = '1999043';

// Lets get all of the possible options
    $result = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_OPTIONS . " where products_options_id LIKE '%' AND language_id = '" . (int) $_SESSION['languages_id'] . "';");
    // $result = xtc_db_query($query);
    $matches = xtc_db_num_rows($result);

    if ($matches) {
        while ($line = xtc_db_fetch_array($result)) {
            $current_product_option_name = $line['products_options_name'];
            $current_product_option_id = $line['products_options_id'];
            // Print the Option Name
            echo "<tr class=\"dataTableHeadingRow\">";
            echo "<th class=\"dataTableHeadingContent\"><b>" . $current_product_option_name . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . SORT_ORDER . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . ATTR_MODEL . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . ATTR_EAN . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . ATTR_VPE_STATUS . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . ATTR_VPE . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . ATTR_VPE_VALUE . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . ATTR_STOCK . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . ATTR_WEIGHT . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . ATTR_PREFIXWEIGHT . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . ATTR_PRICE . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . ATTR_PREFIXPRICE . "</b></th>";
            echo "<th class=\"dataTableHeadingContent\"><b>" . ATTR_SCALE_PRICE . "</b></th>";

            echo "</tr>";

            // Find all of the Current Option's Available Values
            $query2 = "SELECT * FROM " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " WHERE products_options_id = '" . $current_product_option_id . "' ORDER BY products_options_values_id DESC;";
            $result2 = xtc_db_query($query2);
            $matches2 = xtc_db_num_rows($result2);

            if ($matches2) {
                $i = '0';
                while ($line = xtc_db_fetch_array($result2)) {
                    $i++;
                    $rowClass = rowClass($i);
                    $current_value_id = $line['products_options_values_id'];
                    $isSelected = checkAttribute($current_value_id, $_POST['current_product_id'], $current_product_option_id);
                    if ($isSelected) {
                        $CHECKED = ' checked';
                    } else {
                        $CHECKED = '';
                    }
                    if ($attribute_value_vpe_status == 1) {
                        $vpe_statusCheck = 'checked';
                    } else {
                        $vpe_statusCheck = '';
                    }

                    $query3 = "SELECT * FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " WHERE products_options_values_id = '" . $current_value_id . "' AND language_id = '" . $_SESSION['languages_id'] . "'";
                    $result3 = xtc_db_query($query3);
                    while ($line = xtc_db_fetch_array($result3)) {
                        $current_value_name = $line['products_options_values_name'];
                        // Print the Current Value Name
                        echo "<tr class=\"" . $rowClass . "\">";
                        echo "<td class=\"main\">";
                        echo "<input type=\"checkbox\" name=\"optionValues[]\" value=\"" . $current_value_id . "\"" . $CHECKED . ">&nbsp;&nbsp;" . $current_value_name . "&nbsp;&nbsp;";
                        echo "</td>";
                        echo "<td class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_sortorder\" value=\"" . $sortorder . "\" size=\"4\"></td>";
                        echo "<td class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_model\" value=\"" . $attribute_value_model . "\" size=\"15\"></td>";
                        echo "<td class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_ean\" value=\"" . $attribute_value_ean . "\" size=\"15\"></td>";
                        echo "<td class=\"main\" align=\"left\">";
                        echo xtc_draw_selection_field($current_value_id . '_vpe_status', 'checkbox', '1', $attribute_value_vpe_status == 1 ? true : false);
                        echo "</td>";
                        echo "<td class=\"main\" align=\"left\">";
                        $vpe_array = array(array('id' => '', 'text' => TEXT_NONE));
                        $vpe_query = xtc_db_query("SELECT products_vpe_id, products_vpe_name FROM " . TABLE_PRODUCTS_VPE . " WHERE language_id='" . (int) $_SESSION['languages_id'] . "' ORDER BY products_vpe_name;");
                        while ($vpe = xtc_db_fetch_array($vpe_query)) {
                            $vpe_array[] = array('id' => $vpe['products_vpe_id'], 'text' => $vpe['products_vpe_name']);
                        }
                        echo xtc_draw_pull_down_menu($current_value_id . '_vpe', $vpe_array, $attribute_value_vpe = '' ? DEFAULT_PRODUCTS_VPE_ID : $attribute_value_vpe);
                        echo "</td>";
                        echo "<td class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_vpe_value\" value=\"" . $attribute_value_vpe_value . "\" size=\"15\"></td>";
                        echo "<td class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_stock\" value=\"" . $attribute_value_stock . "\" size=\"4\"></td>";
                        echo "<td class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_weight\" value=\"" . $attribute_value_weight . "\" size=\"10\"></td>";
                        echo "<td class=\"main\" align=\"left\"><select name=\"" . $current_value_id . "_weight_prefix\"><option value=\"+\"" . $posCheck_weight . ">+<option value=\"-\"" . $negCheck_weight . ">-</select></td>";
                        // brutto Admin
                        if (PRICE_IS_BRUTTO == 'true') {
                            $attribute_value_price_calculate = $xtPrice->xtcFormat(xtc_round($attribute_value_price * ((100 + (xtc_get_tax_rate(xtc_get_tax_class_id($_POST['current_product_id'])))) / 100), PRICE_PRECISION), false);
                        } else {
                            $attribute_value_price_calculate = xtc_round($attribute_value_price, PRICE_PRECISION);
                        }
                        echo "<td class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_price\" value=\"" . $attribute_value_price_calculate . "\" size=\"10\">";
                        // brutto Admin
                        if (PRICE_IS_BRUTTO == 'true') {
                            echo TEXT_NETTO . '<b>' . $xtPrice->xtcFormat(xtc_round($attribute_value_price, PRICE_PRECISION), true) . '</b>  ';
                        }

                        echo "</td>";
                        echo "<td class=\"main\" align=\"left\"><select name=\"" . $current_value_id . "_prefix\"> <option value=\"+\"" . $posCheck . ">+<option value=\"-\"" . $negCheck . ">-<option value=\"=\"" . $gleichCheck . ">=</select></td>";
                        echo "<td class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_scale_price\" value=\"" . $attribute_value_scale_price . "\" size=\"25\"></td>";
                        echo "</tr>";

                        // Download function start
                        if (strtoupper($current_product_option_name) == 'DOWNLOADS') {
                            echo "<tr>";
                            echo "<td colspan=\"2\">" . xtc_draw_pull_down_menu($current_value_id . '_download_file', xtc_getDownloads(), $attribute_value_download_filename, '') . "</td>";
                            echo "<td class=\"main\">&nbsp;" . DL_COUNT . " <input type=\"text\" name=\"" . $current_value_id . "_download_count\" value=\"" . $attribute_value_download_count . "\"></td>";
                            echo "<td class=\"main\">&nbsp;" . DL_EXPIRE . " <input type=\"text\" name=\"" . $current_value_id . "_download_expire\" value=\"" . $attribute_value_download_expire . "\"></td>";
                            echo "</tr>";
                        }
                        // Download function end
                    }
                    if ($i == $matches2)
                        $i = '0';
                }
            } else {
                echo "<tr>";
                echo "<td class=\"main\"><small>No values under this option.</small></td>";
                echo "</tr>";
            }
        }
    }
    ?>
    <tr>
        <td colspan="10" class="main"><br>
            <?php
            echo xtc_button(BUTTON_SAVE) . '&nbsp;';
            echo xtc_button_link(BUTTON_CANCEL, 'javascript:history.back()');
            ?>
        </td>
    </tr>
</form>