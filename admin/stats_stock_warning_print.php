<?php
/* -----------------------------------------------------------------
 * 	$Id: stats_stock_warning_print.php 420 2013-06-19 18:04:39Z akausch $
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
require(DIR_WS_INCLUDES . 'metatag.php');
?>
</head>
<body onload="window.print()" style="background: #fff">
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td width="100%" valign="top">
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                <tr>
                                    <td>
                                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                            <table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                                <thead>
                                                    <tr class="dataTableHeadingRow">
                                                        <td class="dataTableHeadingContent" align="left">
                                                            Produkt Name
                                                        </td>
                                                        <td class="dataTableHeadingContent" align="center">
                                                            Lagerbestand
                                                        </td>
                                                        <td class="dataTableHeadingContent" align="left">
                                                            Artikel Nr
                                                        </td>
                                                </thead>
                                                <?php
                                                $products_query = xtc_db_query("SELECT p.products_id, 
											  									p.products_quantity,
											  									p.products_model,
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

                                                while ($products = xtc_db_fetch_array($products_query)) {
                                                    echo '<tr>
											    		<td class="dataTableContent">
											    			<b>' . $products['products_name'] . '</b>
											    		</td>
											    		<td class="dataTableContent" align="center">' . $products['products_quantity'] . '</td>
											    		<td class="dataTableContent">' . $products['products_model'] . '</td>
											    	</tr>';

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