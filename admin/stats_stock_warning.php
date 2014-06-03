<?php
/* -----------------------------------------------------------------
 * 	$Id: stats_stock_warning.php 420 2013-06-19 18:04:39Z akausch $
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

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                                <td width="100%">
                                    <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td align="right">
                                    <input class="button" type="button" value="&Uuml;bersicht drucken" onclick="window.open('stats_stock_warning_print.php?lng=<?php echo $_SESSION['languages_id']; ?>', 'popup', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=500')" href="javascript:void();" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                        <table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                            <thead>
                                                <tr class="dataTableHeadingRow">
                                                    <th class="dataTableHeadingContent" align="left">ID</th>
                                                    <th class="dataTableHeadingContent" align="left">Produkt Name</th>
                                                    <th class="dataTableHeadingContent" align="center">Lagerbestand</th>
                                                    <th class="dataTableHeadingContent" align="left">Artikel Nr</th>
                                                    <th class="dataTableHeadingContent" align="left">Status</th>											
                                            </thead>
                                            <?php
                                            $products_query = xtc_db_query("SELECT p.products_id, 
											  									p.products_quantity, 
											  									p.products_model, 
																				p.products_status, 
											  									pd.products_name 
											  								FROM 
											  									" . TABLE_PRODUCTS . " p, 
											  									" . TABLE_PRODUCTS_DESCRIPTION . " pd 
											  								WHERE 
											  									pd.language_id = '" . $_SESSION['languages_id'] . "' 
											  								AND 
											  									pd.products_id = p.products_id 
											  								ORDER BY 
											  									products_name");

                                            $i = 1;
                                            while ($products = xtc_db_fetch_array($products_query)) {
                                                echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '">
											    		<td class="dataTableContent">
											    			<b>' . $products['products_id'] . '</b>
											    		</td>												
											    		<td class="dataTableContent">
											    			<a href="' . xtc_href_link(FILENAME_CATEGORIES, 'pID=' . $products['products_id'] . '&action=new_product') . '">
											    				<b>' . $products['products_name'] . '</b>
											    			</a>
											    		</td>
											    		<td class="dataTableContent" align="center">' . $products['products_quantity'] . '</td>
											    		<td class="dataTableContent">' . $products['products_model'] . '</td>';
                                                if ($products['products_status'] == '1') {
                                                    echo '<td>' . xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;' . xtc_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</td>';
                                                } else {
                                                    echo '<td>' . xtc_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '&nbsp;&nbsp;' . xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10) . '</td>';
                                                }
                                                echo '</tr>';

                                                $products_attributes_query = xtc_db_query("SELECT
											                                                   pov.products_options_values_name,
											                                                   pa.attributes_stock
											                                               FROM
											                                                   " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
											                                               WHERE
											                                                   pa.products_id = '" . $products_values['products_id'] . "' 
											                                               AND 
											                                               		pov.products_options_values_id = pa.options_values_id 
											                                               	AND 
											                                               		pov.language_id = '" . $_SESSION['languages_id'] . "' 
											                                               	ORDER BY 
											                                               		pa.attributes_stock");

                                                while ($products_attributes_values = xtc_db_fetch_array($products_attributes_query)) {
                                                    echo '<tr><td class="dataTableContent">&nbsp;&nbsp;&nbsp;&nbsp;-' . $products_attributes_values['products_options_values_name'] . '</td><td width="50%" class="dataTableContent">';
                                                    if ($products_attributes_values['attributes_stock'] <= '0') {
                                                        echo '<font color="ff0000"><b>' . $products_attributes_values['attributes_stock'] . '</b></font>';
                                                    } else {
                                                        echo $products_attributes_values['attributes_stock'];
                                                    }
                                                    echo '</td><td></td></tr>';
                                                }
                                                $i++;
                                            }
                                            ?>
                                        </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
