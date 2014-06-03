<?php
/* -----------------------------------------------------------------
 * 	$Id: module_order_products.php 420 2013-06-19 18:04:39Z akausch $
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

switch ($_GET['action']) {
    case 'delete':

        if (isset($_GET['acid']) && is_numeric($_GET['acid'])) {
            xtc_db_query("DELETE FROM " . TABLE_EMAILS_ORDER_PRODUCT_LIST . " where id = '" . (int) $_GET['acid'] . "'");
            xtc_db_query("DELETE FROM " . TABLE_EMAILS_ORDER_PRODUCTS . " where accessories_id = '" . (int) $_GET['acid'] . "'");

            xtc_redirect(xtc_href_link(FILENAME_MODULE_ORDER_PRODUCTS));
        }
        break;

    case 'new':

        if (isset($_POST['products_list'])) {
            $exist_id_query = xtc_db_query("select id from " . TABLE_EMAILS_ORDER_PRODUCT_LIST . " where list_name = '" . $_POST['products_list'] . "'");
            $exist_id = xtc_db_num_rows($exist_id_query);
            $exist = xtc_db_fetch_array($exist_id_query);

            if ($exist_id == 0) {
                $product_list_array = array('list_name' => xtc_db_prepare_input($_POST['products_list']));
                xtc_db_perform(TABLE_EMAILS_ORDER_PRODUCT_LIST, $product_list_array);
                $select_id_query = xtc_db_query("select id from " . TABLE_EMAILS_ORDER_PRODUCT_LIST . " 
												 where list_name = '" . $_POST['products_list'] . "'");
                $select_id = xtc_db_fetch_array($select_id_query);

                xtc_redirect(xtc_href_link(FILENAME_MODULE_ORDER_PRODUCTS, 'action=new&aid=' . (int) $select_id['id'] . '&name=' . $_POST['products_list']));
            } else {
                xtc_redirect(xtc_href_link(FILENAME_MODULE_ORDER_PRODUCTS, 'action=edit&acid=' . (int) $exist['id']));
                break;
            }
        }
        break;

    case 'edit':

        if ($_POST['acp']) {
            $anz = count($_POST['acp']);
            if ($anz > 0) {
                for ($i = 0; $i < $anz; $i++) {
                    xtc_db_query("DELETE FROM " . TABLE_EMAILS_ORDER_PRODUCTS . " where id = '" . $_POST['acp'][$i] . "'");
                }
                xtc_redirect(xtc_href_link(FILENAME_MODULE_ORDER_PRODUCTS, 'action=edit&acid=' . (int) $_GET['acid']));
                break;
            }
        }

        if (isset($_POST['accessories_product'])) {
            $search_accessories_query = xtc_db_query("SELECT 
			p.products_id, 
			p.products_model,
			pd.products_name 
				FROM " . TABLE_PRODUCTS . " p, 
					" . TABLE_PRODUCTS_DESCRIPTION . " pd 
					where p.products_status = 1 
					AND p.products_id = pd.products_id 
					AND pd.language_id = '" . $_SESSION['languages_id'] . "' 
					AND pd.products_name LIKE '%" . $_POST['accessories_product'] . "%' 
					OR p.products_model LIKE '%" . $_POST['accessories_product'] . "%' 
						ORDER BY p.products_id ASC LIMIT 0,10");
        }

        if ($_POST['accessories']) {
            $n = count($_POST['accessories']);
            if ($n > 0) {

                for ($i = 0; $i < $n; $i++) {
                    $accessories_product_array = array(
                        'accessories_id' => xtc_db_prepare_input((int) $_GET['acid']),
                        'product_id' => xtc_db_prepare_input($_POST['accessories'][$i]));

                    xtc_db_perform(TABLE_EMAILS_ORDER_PRODUCTS, $accessories_product_array);
                }
                xtc_redirect(xtc_href_link(FILENAME_MODULE_ORDER_PRODUCTS, 'action=edit&acid=' . (int) $_GET['acid']));
                break;
            }
        }
}
require(DIR_WS_INCLUDES . 'header.php');
?>
<script type="text/javascript">
<!--
    function checkboxes(wert) {
        var my = document.leiste;
        var len = my.length;

        for (var i = 0; i < len; i++) {
            var e = my.elements[i];
            if (e.name == "status[]") {
                e.checked = wert;
            }
        }
    }
//-->
</script>	

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="pageHeading">
                        <?php echo HEADING_TITLE; ?>
                    </td>
                </tr>
            </table>

            <table width="100%" border="0" cellspacing="1" cellpadding="2">
                <tr>
                    <td class="main"><?php echo CONTENT_NOTE; ?></td>
                </tr>  
            </table><br />

            <table border="0" cellspacing="5" cellpadding="5">
                <tr>
                    <td><a class="button" href="<?php echo xtc_href_link(FILENAME_MODULE_ORDER_PRODUCTS); ?>"><?php echo LIST_OVERVIEW; ?></a></td>
                    <td><a class="button" href="<?php echo xtc_href_link(FILENAME_MODULE_ORDER_PRODUCTS, 'action=new') ?>"><?php echo LIST_NEW; ?></a></td>
                </tr>  
            </table>
            <br>

            <?php
            if ($_GET['action'] == 'new') {
                echo xtc_draw_form('search', FILENAME_MODULE_ORDER_PRODUCTS, 'action=new', 'post', '');
                ?>
                <table border="0" cellspacing="1" cellpadding="2">
                    <tr>
                        <td class="access_step_n" width="20"><b>1.</b></td>
                        <td class="access_step_c" width="200"><?php echo STEP_1; ?></td>
                        <?php
                        if ($_GET['name']) {
                            $select_list_name = xtc_db_query("SELECT list_name FROM " . TABLE_EMAILS_ORDER_PRODUCT_LIST . "
									  WHERE id = '" . (int) $_GET['aid'] . "'");
                            $select = xtc_db_fetch_array($select_list_name);
                            echo '<td class="main">' . $select['list_name'];
                        } else {
                            echo '<td class="access_step_c">' . xtc_draw_input_field('products_list', '', 'size="25"');
                        }
                        ?>	
                        </td>
                        <?php
                        if (!$_GET['name']) {
                            ?>	
                            <td class="access_step_c"><input class="button" type="submit" onclick="this.blur();" value="<?php echo INPUT_PRODUCT; ?>"></td>
                            <?php
                        }
                        ?>		
                    </tr> 
                    <?php if (!$_GET['name']) { ?>
                        <tr>
                            <td class="main" colspan="4"><?php echo STEP_1_HELP; ?></td>  
                        </tr>
                    <?php } ?>
                </table>
                </form>  

                <?php
                if ($_GET['name']) {
                    echo xtc_draw_form('search', FILENAME_MODULE_ORDER_PRODUCTS, 'action=edit&acid=' . (int) $_GET['aid'], 'post', '');
                    ?>
                    <table border="0" cellspacing="1" cellpadding="2">
                        <tr>
                            <td class="access_step_n" width="20"><b>2.</b></td>
                            <td class="access_step_c" width="200"><?php echo STEP_2a; ?></td>
                            <td><input class="button" type="submit" onClick="this.blur();" value="<?php echo SEARCH; ?>"></td>
                        </tr>  
                        <?php if ($_GET['name']) { ?>
                            <tr>
                                <td class="main"  colspan="4"><?php echo STEP_2_HELP; ?></td>  
                            </tr>
                        <?php } ?>
                    </table>
                    </form>
                    <br>

                    <?php
                } // isset
                ?>

                <?php
            } elseif ($_GET['action'] == 'edit' && is_numeric($_GET['acid'])) {
                echo xtc_draw_form('acces', FILENAME_MODULE_ORDER_PRODUCTS, 'action=edit&acid=' . (int) $_GET['acid'], 'post', '');

                $select_list_name = xtc_db_query("SELECT list_name FROM " . TABLE_EMAILS_ORDER_PRODUCT_LIST . "
									  WHERE id = '" . (int) $_GET['acid'] . "'");
                $select = xtc_db_fetch_array($select_list_name);
                ?>	
                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                    <tr>
                        <td valign="top" width="50%">

                            <table border="0" cellspacing="1" cellpadding="2" style="border: 1px solid #ccc">
                                <tr>
                                    <td class="dataTableContent_products" width="200" colspan="2"><?php echo $select['list_name']; ?></td>	
                                </tr>
    <?php
    $select_acc_query = xtc_db_query("SELECT ap.id, p.products_model, pd.products_name 
		FROM " . TABLE_PRODUCTS . " p, 
			" . TABLE_PRODUCTS_DESCRIPTION . " pd,
			" . TABLE_EMAILS_ORDER_PRODUCT_LIST . " a,
			" . TABLE_EMAILS_ORDER_PRODUCTS . " ap  
		WHERE a.id = '" . (int) $_GET['acid'] . "' 
		AND a.id = ap.accessories_id 
		AND ap.product_id = p.products_id 
		AND p.products_id = pd.products_id 
		AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "'");

    $anz = xtc_db_num_rows($select_acc_query);

    while ($select_acc = xtc_db_fetch_array($select_acc_query)) {
        ?>  
                                    <tr>
                                        <td class="access_step_cc" width="20"><?php echo xtc_draw_selection_field('acp[]', 'checkbox', $select_acc['id']); ?></td>
                                        <td class="access_step_cc"><?php echo $select_acc['products_model'] . ' - ' . $select_acc['products_name']; ?></td>	
                                    </tr>
        <?php
    }// WHILE
    if ($anz > 0) {
        ?>  
                                    <tr>
                                        <td colspan="2"><input class="button" type="submit" onclick="this.blur();" value="<?php echo INPUT_DEL_ACCPRODUCT; ?>"></td>
                                    </tr>
    <?php } ?>  
                            </table>
                            </form>

                        <td valign="top" width="50%">

    <?php
    if (isset($_GET['acid']) && is_numeric($_GET['acid'])) {
        echo xtc_draw_form('search', FILENAME_MODULE_ORDER_PRODUCTS, 'action=edit&acid=' . (int) $_GET['acid'], 'post', '');
        ?>
                                <fieldset>
                                    <legend><?php echo ACCESSORIES; ?></legend>
                                    <table border="0" cellspacing="1" cellpadding="2">
                                        <tr>
                                            <td class="access_step_n" width="20"><b>1.</b></td>
                                            <td class="access_step_c" width="200"><?php echo STEP_2; ?></td>
                                            <td><?php echo xtc_draw_input_field('accessories_product', '', 'size="25"'); ?></td>
                                            <td><input class="button" type="submit" onClick="this.blur();" value="<?php echo SEARCH; ?>"></td>
                                        </tr>  
                                        <?php if (isset($_GET['acid']) && is_numeric($_GET['acid'])) { ?>
                                            <tr>
                                                <td class="main" colspan="4"><?php echo STEP_3_HELP; ?></td>  
                                            </tr>
                                        <?php } ?>
                                    </table>
                                    </form>
                                    <br>

                                    <?php
                                    if (isset($_POST['accessories_product'])) {
                                        if (xtc_db_num_rows($search_accessories_query) > 0) {
                                            echo xtc_draw_form('search', FILENAME_MODULE_ORDER_PRODUCTS, 'action=edit&acid=' . (int) $_GET['acid'], 'post', '');
                                            ?>
                                            <table border="0" cellspacing="1" cellpadding="2">
                                                <?php
                                                while ($accessories_product = xtc_db_fetch_array($search_accessories_query)) {
                                                    ?>
                                                    <tr>
                                                        <td width="20">&nbsp;</td>
                                                        <td class="access_step_n" width="20"><?php echo xtc_draw_selection_field('accessories[]', 'checkbox', $accessories_product['products_id']); ?></td>
                                                        <td class="access_step_c"><?php echo $accessories_product['products_model']; ?></td>
                                                        <td class="access_step_c"><?php echo $accessories_product['products_name']; ?></td>
                                                    </tr> 
                                                <?php } ?>  
                                                <tr>
                                                    <td colspan="3"><input class="button" type="submit" onclick="this.blur();" value="<?php echo INPUT_PRODUCT; ?>"></td>
                                                </tr>
                                            </table>
                                        </fieldset>
                                        </form>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                } // acid
                            } // isset
                            ?>

                        </td>
                    </tr>
                </table>
                <?php
            } else { // new
                ?>
                <table width="100%" class="dataTable" cellspacing="0" cellpadding="0">
                    <tr class="dataTableHeadingRow"> 
                        <td class="dataTableHeadingContent" width="20"></td>
                        <td class="dataTableHeadingContent"><?php echo NAME; ?></td>
                        <td class="dataTableHeadingContent"><?php echo ACCESSORIES; ?></td>
                        <td class="dataTableHeadingContent"><?php echo ACTION; ?></td>
                    </tr> 
                    <?php
                    $head_product_query = xtc_db_query("SELECT id, list_name FROM " . TABLE_EMAILS_ORDER_PRODUCT_LIST . " ORDER BY id ASC");
                    $rows = 1;
                    while ($head_product = xtc_db_fetch_array($head_product_query)) {
                        $count_accessories = xtc_db_query("SELECT COUNT(ap.id) AS total 
									   FROM " . TABLE_EMAILS_ORDER_PRODUCTS . " ap, " . TABLE_EMAILS_ORDER_PRODUCT_LIST . " a 
									   WHERE a.list_name = '" . $head_product['list_name'] . "'
									   AND a.id = ap.accessories_id");
                        $ca = xtc_db_fetch_array($count_accessories);
                        echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '">' . "\n";
                        ?> 
                        <td class="access_step_nc" width="20"><?php echo xtc_draw_selection_field('ids[]', 'checkbox', $head_product['list_name']); ?></td>
                        <td class="access_step_cc"><nobr><?php echo $head_product['list_name']; ?></nobr></td>
                <td class="access_step_cc"><?php echo $ca['total']; ?></td>
                <td class="access_step_cc">
                    <a href="<?php echo xtc_href_link(FILENAME_MODULE_ORDER_PRODUCTS, 'action=edit&acid=' . (int) $head_product['id']) ?>">
                        <img src="images/icon_edit.gif" alt="" title="<?php echo ACTION_EDIT; ?>" />
                    </a>&nbsp;
                    <a href="<?php echo xtc_href_link(FILENAME_MODULE_ORDER_PRODUCTS, 'action=delete&acid=' . (int) $head_product['id']) ?>">
                        <img src="images/cross.gif" alt="" title="<?php echo ACTION_DEL; ?>" />
                    </a>
                </td>
            </tr>
            <?php
            $rows++;
        }
        ?>  
    </table>	
    <?php
}// else
?> 

</td>
</tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
