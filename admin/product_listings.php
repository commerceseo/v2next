<?php
/* -----------------------------------------------------------------
 * 	$Id: product_listings.php 420 2013-06-19 18:04:39Z akausch $
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

require ('includes/application_top.php');

if ($_GET['action'] == 'save') {
    $sql_data_array = array('col' => xtc_db_prepare_input($_POST['col']),
        'p_img' => xtc_db_prepare_input($_POST['p_img']),
        'p_name' => xtc_db_prepare_input($_POST['p_name']),
        'p_price' => xtc_db_prepare_input($_POST['p_price']),
        'b_details' => xtc_db_prepare_input($_POST['b_details']),
        'b_order' => xtc_db_prepare_input($_POST['b_order']),
        'b_wishlist' => xtc_db_prepare_input($_POST['b_wishlist']),
        'p_reviews' => xtc_db_prepare_input($_POST['p_reviews']),
        'p_stockimg' => xtc_db_prepare_input($_POST['p_stockimg']),
        'p_vpe' => xtc_db_prepare_input($_POST['p_vpe']),
        'p_model' => xtc_db_prepare_input($_POST['p_model']),
        'p_manu_img' => xtc_db_prepare_input($_POST['p_manu_img']),
        'p_manu_name' => xtc_db_prepare_input($_POST['p_manu_name']),
        'p_short_desc' => xtc_db_prepare_input($_POST['p_short_desc']),
        'p_short_desc_lenght' => xtc_db_prepare_input($_POST['p_short_desc_lenght']),
        'p_long_desc' => xtc_db_prepare_input($_POST['p_long_desc']),
        'p_staffel' => xtc_db_prepare_input($_POST['p_staffel']),
        'p_attribute' => xtc_db_prepare_input($_POST['p_attribute']),
        'p_buy' => xtc_db_prepare_input($_POST['p_buy']),
        'p_weight' => xtc_db_prepare_input($_POST['p_weight']),
        'p_long_desc_lenght' => xtc_db_prepare_input($_POST['p_long_desc_lenght']),);

    xtc_db_perform('products_listings', $sql_data_array, 'update', "list_name = '" . $_POST['list_name'] . "' ");

    xtc_redirect('product_listings.php#' . $_POST['list_name']);
    exit();
}

$cols[] = array('id' => '1', 'text' => '1');
$cols[] = array('id' => '2', 'text' => '2');
$cols[] = array('id' => '3', 'text' => '3');
$cols[] = array('id' => '4', 'text' => '4');

require(DIR_WS_INCLUDES . 'header.php');
?>

<script>
    $(function() {
        $("#tabslisting").tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
        $("#tabslisting li").removeClass("ui-corner-top").addClass("ui-corner-left");
    });
</script>
<style>
    .ui-tabs-vertical { width: 100%; }
    .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 20%; }
    .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
    .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
    .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
    .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 75%;}

</style>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="3">
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading">
<?php echo HEADER; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
<?php echo HEADER_DESC; ?>
                    </td>
                </tr>
                <tr>
                    <td align="left">
                        <div id="tabslisting">
                            <ul>
                                <?php
                                $options_query = xtc_db_query("SELECT list_name FROM products_listings ORDER BY list_type,list_name;");
                                while ($options_name = xtc_db_fetch_array($options_query)) {
                                    echo '<li><a href="#' . $options_name['list_name'] . '"><span>' . constant(strtoupper($options_name['list_name']) . '_TITLE') . '</span></a></li>';
                                }
                                ?>
                            </ul>
                            <?php
                            $options_query_2 = xtc_db_query("SELECT * FROM products_listings ORDER BY list_type,list_name");

                            while ($options_data = xtc_db_fetch_array($options_query_2)) {
                                echo '<div id="' . $options_data['list_name'] . '" class="tab_container">';
                                echo '<form method="POST" action="product_listings.php?action=save" name="listing_options">';
                                echo '<h2>' . constant(strtoupper($options_data['list_name']) . '_TITLE') . '</h2>';
                                echo '	<table width="100%">
									<tr>
										<td width="33.3%" valign="top">
											<table width="100%">';
                                if ($options_data['list_type'] != 'box') {
                                    echo '<tr>
														<td class="main">' . COLS . '</td>
														<td class="main">' . xtc_draw_pull_down_menu('col', $cols, $options_data['col']) . '</td>
													</tr>';
                                } else {
                                    echo xtc_draw_hidden_field('col', 1, $options_data['col']);
                                }
                                echo '<tr>
													<td class="main">' . NAME . '</td>
													<td class="main">' . xtc_draw_selection_field('p_name', 'checkbox', '1', $options_data['p_name'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main">' . PICTURE . '</td>
													<td class="main">' . xtc_draw_selection_field('p_img', 'checkbox', '1', $options_data['p_img'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main">' . PRICE . '</td>
													<td class="main">' . xtc_draw_selection_field('p_price', 'checkbox', '1', $options_data['p_price'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main">' . VPE_DISPLAY . '</td>
													<td class="main">' . xtc_draw_selection_field('p_vpe', 'checkbox', '1', $options_data['p_vpe'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main">' . WEIGHT . '</td>
													<td class="main">' . xtc_draw_selection_field('p_weight', 'checkbox', '1', $options_data['p_weight'] == 1 ? true : false) . '</td>
												</tr>
											</table>
										</td>';
                                if ($options_data['list_type'] != 'box') {
                                    echo '
										<td width="33.3%" valign="top">
											<table width="100%">
												<tr>
													<td class="main" style="border-left: 1px solid #ccc">' . STAFFEL . '</td>
													<td class="main" style="border-right: 1px solid #ccc">' . xtc_draw_selection_field('p_staffel', 'checkbox', '1', $options_data['p_staffel'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main" style="border-left: 1px solid #ccc">' . ATTRIBUTES . '</td>
													<td class="main" style="border-right: 1px solid #ccc">' . xtc_draw_selection_field('p_attribute', 'checkbox', '1', $options_data['p_attribute'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main" style="border-left: 1px solid #ccc">' . BUY . '</td>
													<td class="main" style="border-right: 1px solid #ccc">' . xtc_draw_selection_field('p_buy', 'checkbox', '1', $options_data['p_buy'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main" style="border-left: 1px solid #ccc">' . DETAIL_BUTTON . '</td>
													<td class="main" style="border-right: 1px solid #ccc">' . xtc_draw_selection_field('b_details', 'checkbox', '1', $options_data['b_details'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main" style="border-left: 1px solid #ccc">' . BUY_BUTTON . '</td>
													<td class="main" style="border-right: 1px solid #ccc">' . xtc_draw_selection_field('b_order', 'checkbox', '1', $options_data['b_order'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main" style="border-left: 1px solid #ccc">' . WISHLIST_BUTTON . '</td>
													<td class="main" style="border-right: 1px solid #ccc">' . xtc_draw_selection_field('b_wishlist', 'checkbox', '1', $options_data['b_wishlist'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main" style="border-left: 1px solid #ccc">' . ARTICLE_NR . '</td>
													<td class="main" style="border-right: 1px solid #ccc">' . xtc_draw_selection_field('p_model', 'checkbox', '1', $options_data['p_model'] == 1 ? true : false) . '</td>
												</tr>
													<tr>
														<td class="main" style="border-left: 1px solid #ccc">' . REVIEW . '</td>
														<td class="main" style="border-right: 1px solid #ccc">' . xtc_draw_selection_field('p_reviews', 'checkbox', '1', $options_data['p_reviews'] == 1 ? true : false) . '</td>
													</tr>
											</table>
										</td>
										<td width="33.3%" valign="top">
											<table width="100%">
												<tr>
													<td class="main">' . STOCK . '</td>
													<td class="main">' . xtc_draw_selection_field('p_stockimg', 'checkbox', '1', $options_data['p_stockimg'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main">' . MANUFACTURER_IMG . '</td>
													<td class="main">' . xtc_draw_selection_field('p_manu_img', 'checkbox', '1', $options_data['p_manu_img'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main">' . MANUFACTURER_NAME . '</td>
													<td class="main">' . xtc_draw_selection_field('p_manu_name', 'checkbox', '1', $options_data['p_manu_name'] == 1 ? true : false) . '</td>
												</tr>
												<tr>
													<td class="main">' . SHORT_DESC . '</td>
													<td class="main">' . xtc_draw_selection_field('p_short_desc', 'checkbox', '1', $options_data['p_short_desc'] == 1 ? true : false) . ' ' . xtc_draw_input_field('p_short_desc_lenght', $options_data['p_short_desc_lenght'], 'size="3"') . '</td>
												</tr>
												<tr>
													<td class="main">' . LONG_DESC . '</td>
													<td class="main">' . xtc_draw_selection_field('p_long_desc', 'checkbox', '1', $options_data['p_long_desc'] == 1 ? true : false) . ' ' . xtc_draw_input_field('p_long_desc_lenght', $options_data['p_long_desc_lenght'], 'size="3"') . '</td>
												</tr>
											</table>
										</td>';
                                } else {
                                    echo '
											<td width="33.3%" valign="top">
												&nbsp;
											</td>
											<td width="33.3%" valign="top">
												&nbsp;
											</td>';
                                }
                                echo '</tr>
								</table>
								<input type="hidden" name="list_name" value="' . $options_data['list_name'] . '" />
								<div align="right">
									<input type="submit" class="button" value="' . BUTTON_SAVE . '" />
								</div>
								</form>
							</div>';
                            }
                            ?>

                        </div>
                    </td>
                </tr>
            </table></td>
    </tr>
</table>

<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
