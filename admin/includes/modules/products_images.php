<?php

/* -----------------------------------------------------------------
 * 	$Id: products_images.php 1044 2014-05-12 21:25:42Z akausch $
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

require_once (DIR_FS_INC . 'xtc_get_products_mo_images.inc.php');

$languages = xtc_get_languages();

if ($_GET['action'] == 'new_product') {
    echo '<tr><td colspan="2" style="border: 1px solid #677E98"><div class="bildernamen">' . TEXT_PRODUCTS_MAIN_IMAGE . '</div><table width="100%"><tr>';
    if ($pInfo->products_image) {
        echo '<td align="left" class="main" width="' . (PRODUCT_IMAGE_THUMBNAIL_WIDTH + 15) . '">
				' . xtc_image(DIR_WS_CATALOG_THUMBNAIL_IMAGES . $pInfo->products_image, 'Standard Image') . '
			</td>';
        echo '<td class="main">' . xtc_draw_file_field('products_image') . '<br />
					' . $pInfo->products_image . xtc_draw_hidden_field('products_previous_image_0', $pInfo->products_image) . '
			</td>';
        echo '</tr>';
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            echo '<tr>
					<td class="main" width="1">
						' . TEXT_PRODUCTS_ALTERNATE_IMAGE_DESC . '
					</td>
					<td class="main"><nobr>
						' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' ' . xtc_draw_input_field('products_image_alt[' . $languages[$i]['id'] . ']', xtc_get_product_field($pInfo->products_id, $languages[$i]['id'], 'products_img_alt'), 'style="width:90%"') . '
					</nobr></td>';
        }
        echo '</tr>';
        echo '<tr><td colspan="2" align="left" class="main">';
        echo xtc_draw_selection_field('del_pic', 'checkbox', $pInfo->products_image) . ' ' . TEXT_DELETE;
        echo '</td>';
    } else {
        echo '	<td colspan="2" class="main" align="center">
					' . xtc_draw_file_field('products_image') . $pInfo->products_image . xtc_draw_hidden_field('products_previous_image_0', $pInfo->products_image) . '
				</td>';
        echo '</tr>';

        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            echo '<tr>
					<td class="main" width="1">
						' . TEXT_PRODUCTS_ALTERNATE_IMAGE_DESC . '
					</td>
					<td class="main">
						' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' ' . xtc_draw_input_field('products_image_alt_' . $languages[$i]['id'], $pInfo->products_image_alt, 'style="width:90%"') . '
					</td></tr>';
        }
    }
    echo '</table></td></tr>';


    // Zusatzbilder
    if (MO_PICS > 0) {
        require_once(DIR_FS_INC . 'cseo_get_img_alt.inc.php');
        echo '<tr><td colspan="2"><table width="100%">';
        $mo_images = xtc_get_products_mo_images($pInfo->products_id);
        for ($i = 0; $i < MO_PICS; $i++) {
            echo '<tr><td>&nbsp;</td></tr>';
            if ($i % 2 == 0)
                $f = ' class="dataTableRow"';
            else
                $f = '';
            echo '<tr' . $f . '><td style="border: 1px solid #ccc"><div class="bildernamen">' . TEXT_PRODUCTS_IMAGE . ' ' . ($i + 1) . '</div><table width="100%"><tr>';
            if ($mo_images[$i]["image_name"]) {
                echo '	<td class="main" width="' . (PRODUCT_IMAGE_THUMBNAIL_WIDTH + 15) . '">
							' . xtc_image(DIR_WS_CATALOG_THUMBNAIL_IMAGES . $mo_images[$i]["image_name"], 'Image ' . ($i + 1)) .
                '</td>';
                echo '<td class="main">'
                . xtc_draw_file_field('mo_pics_' . $i) . '<br />'
                . $mo_images[$i]["image_name"] . xtc_draw_hidden_field('products_previous_image_' . ($i + 1), $mo_images[$i]["image_name"]);
                echo '</tr>';
                for ($ii = 0, $n = sizeof($languages); $ii < $n; $ii++) {
                    echo '<tr>
							<td class="main">
								' . TEXT_PRODUCTS_ALTERNATE_IMAGE_DESC . '
							</td>
							<td class="main"><nobr>
								' . xtc_image(DIR_WS_LANGUAGES . $languages[$ii]['directory'] . '/' . $languages[$ii]['image'], $languages[$ii]['name']) . ' ' . xtc_draw_input_field('alt_tag_' . ($i + 1) . '_' . $languages[$ii]['id'], cseo_get_img_alt($pInfo->products_id, $languages[$ii]['id'], ($i + 1)), 'style="width:90%"') . '</nobr>
							</td>
						  </tr>';
                }
                echo '<tr>
						<td colspan="2" align="left" class="main" valign="middle">' . xtc_draw_selection_field('del_mo_pic[]', 'checkbox', $mo_images[$i]["image_name"]) . ' ' . TEXT_DELETE . '</td>';
                echo '</tr>';
            } else {
                echo '<tr><td colspan="2" align="center" class="main">' . xtc_draw_file_field('mo_pics_' . $i) . xtc_draw_hidden_field('products_previous_image_' . ($i + 1), $mo_images[$i]["image_name"]) .
                '</td></tr>';
                $languages = xtc_get_languages();
                for ($ii = 0, $n = sizeof($languages); $ii < $n; $ii++) {
                    echo '<tr>
							<td class="main" width="1">
								<nobr>' . TEXT_PRODUCTS_ALTERNATE_IMAGE_DESC . '</nobr>
							</td>
							<td class="main">
								' . xtc_image(DIR_WS_LANGUAGES . $languages[$ii]['directory'] . '/' . $languages[$ii]['image'], $languages[$ii]['name']) . ' ' . xtc_draw_input_field('alt_tag_' . ($i + 1) . '_' . $languages[$ii]['id'], $mo_images[$i]["alt_tag"], 'style="width:90%"') . '
							</td>
						  </tr>';
                }
            }
            echo '</table></td></tr>';
        }
        echo '</table></td></tr>';
    }
}
?>