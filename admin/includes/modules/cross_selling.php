<?php
/* -----------------------------------------------------------------
 * 	$Id: cross_selling.php 420 2013-06-19 18:04:39Z akausch $
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
// select article data
$article_query = "SELECT products_name FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_id='" . (int) $_GET['current_product_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'";
$article_data = xtc_db_fetch_array(xtc_db_query($article_query));

$cross_sell_groups = xtc_get_cross_sell_groups();

function buildCAT($catID) {

    $cat = array();
    $tmpID = $catID;

    while (getParent($catID) != 0 || $catID != 0) {
        $cat_select = xtc_db_query("SELECT categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id='" . $catID . "' and language_id='" . $_SESSION['languages_id'] . "'");
        $cat_data = xtc_db_fetch_array($cat_select);
        $catID = getParent($catID);
        $cat[] = $cat_data['categories_name'];
    }
    $catStr = '';
    for ($i = count($cat); $i > 0; $i--) {
        $catStr .= $cat[$i - 1] . ' > ';
    }

    return $catStr;
}

function getParent($catID) {
    $parent_query = xtc_db_query("SELECT parent_id FROM " . TABLE_CATEGORIES . " WHERE categories_id='" . $catID . "'");
    $parent_data = xtc_db_fetch_array($parent_query);
    return $parent_data['parent_id'];
}
?>
<tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="pageHeading"><?php echo CROSS_SELLING . ' : ' . $article_data['products_name']; ?></td>
                
            </tr>
            <tr>
                <td colspan="2"><a href="<?php echo xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cpath'] . '&pID=' . $_GET['current_product_id']); ?>"><input type="submit" class="button" value="<?php echo BUTTON_BACK; ?>"></a></td>
            </tr>
        </table></td>
</tr>
<tr>
    <td>

        <?php
        echo xtc_draw_form('cross_selling', FILENAME_CATEGORIES, '', 'GET', '');
        echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
        echo xtc_draw_hidden_field('action', 'edit_crossselling');
        echo xtc_draw_hidden_field('special', 'edit');
        echo xtc_draw_hidden_field('current_product_id', $_GET['current_product_id']);
        echo xtc_draw_hidden_field('cpath', $_GET['cpath']);
        ?>

        <script type="text/javascript">
            var marker = false;
            function marke()
            {
                var elements = document.getElementsByName("idse[]");

                if (marker == false)
                {
                    for (i = 0; i < elements.length; i++)
                    {
                        elements[i].checked = true;
                        marker = true;
                    }
                }
                else
                {
                    for (i = 0; i < elements.length; i++)
                    {
                        elements[i].checked = false;
                        marker = false;
                    }
                }
            }
        </script>


        <input type="checkbox" onClick="marke()" /><?php echo TXT_ALL; ?>

        <table width="100%" border="0">
            <tr>
                <td class="dataTableHeadingContent" width="1%"><?php echo HEADING_DEL; ?></td>
                <td class="dataTableHeadingContent" width="4%"><?php echo HEADING_SORTING; ?></td>
                <td class="dataTableHeadingContent" width="5%"><?php echo HEADING_GROUP; ?></td>
                <td class="dataTableHeadingContent" width="15%"><?php echo HEADING_MODEL; ?></td>
                <td class="dataTableHeadingContent" width="34%"><?php echo HEADING_NAME; ?></td>
                <td class="dataTableHeadingContent" width="42%"><?php echo HEADING_CATEGORY; ?></td>
            </tr>
            <?php
            $cross_query = "SELECT cs.ID,cs.products_id,pd.products_name,cs.sort_order,p.products_model,p.products_id,cs.products_xsell_grp_name_id FROM " . TABLE_PRODUCTS_XSELL . " cs, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p WHERE cs.products_id = '" . (int) $_GET['current_product_id'] . "' and cs.xsell_id=p.products_id and p.products_id=pd.products_id  and pd.language_id = '" . $_SESSION['languages_id'] . "' ORDER BY cs.sort_order";
            $cross_query = xtc_db_query($cross_query);
            if (!xtc_db_num_rows($cross_query)) {
                ?>
                <tr>
                    <td class="categories_view_data" colspan="5">- NO ENRTY -</td>
                </tr>
                <?php
            }
            while ($cross_data = xtc_db_fetch_array($cross_query)) {
                $categorie_query = xtc_db_query("SELECT
		                                            categories_id
		                                            FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
		                                            WHERE products_id='" . $cross_data['products_id'] . "' LIMIT 0,1");
                $categorie_data = xtc_db_fetch_array($categorie_query);
                ?>



                <tr>
                    <td class="categories_view_data"><input type="checkbox" name="idse[]" value="<?php echo $cross_data['ID']; ?>"></td>
                    <td class="categories_view_data"><input name="sort[<?php echo $cross_data['ID']; ?>]" type="text" size="3" value="<?php echo $cross_data['sort_order']; ?>"></td>

                    <td class="categories_view_data" style="text-align: left;"><?php echo xtc_draw_pull_down_menu('group_name[' . $cross_data['ID'] . ']', $cross_sell_groups, $cross_data['products_xsell_grp_name_id']); ?></td>

                    <td class="categories_view_data" style="text-align: left;"><?php echo $cross_data['products_model']; ?></td>
                    <td class="categories_view_data" style="text-align: left;"><?php echo $cross_data['products_name']; ?></td>
                    <td class="categories_view_data" style="text-align: left;"><?php echo buildCAT($categorie_data['categories_id']); ?> </td>
                </tr>

            <?php } ?>
        </table>
        <input type="submit" class="button" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')">
        <?php echo '</form>'; ?>
    </td>
</tr>
<tr>
    <td class="pageHeading"><hr noshade><?php echo CROSS_SELLING_SEARCH; ?>

        <table>
            <tr class="dataTableRow">
                <?php
                echo xtc_draw_form('product_search', FILENAME_CATEGORIES, '', 'GET');
                echo xtc_draw_hidden_field('action', 'edit_crossselling');
                echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
                echo xtc_draw_hidden_field('current_product_id', $_GET['current_product_id']);
                echo xtc_draw_hidden_field('cpath', $_GET['cpath']);
                ?>
                <td class="dataTableContent" width="40"><?php echo xtc_draw_input_field('search', '', 'size="30"'); ?></td>
                <td class="dataTableContent">
                    <?php
                    echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SEARCH . '"/>';
                    echo '</form></td>';
                    ?>
            </tr>
        </table>
        <hr noshade>
    </td>
</tr>
<tr>
    <td>

        <?php
        // search results
        if ($_GET['search']) {
            echo xtc_draw_form('product_search', FILENAME_CATEGORIES, '', 'GET');
            echo xtc_draw_hidden_field('action', 'edit_crossselling');
            echo xtc_draw_hidden_field('special', 'add_entries');
            echo xtc_draw_hidden_field('current_product_id', $_GET['current_product_id']);
            echo xtc_draw_hidden_field('cpath', $_GET['cpath']);
            ?>

            <script type="text/javascript">
                var marker = false;
                function mark()
                {
                    var elements = document.getElementsByName("ids[]");

                    if (marker == false)
                    {
                        for (i = 0; i < elements.length; i++)
                        {
                            elements[i].checked = true;
                            marker = true;
                        }
                    }
                    else
                    {
                        for (i = 0; i < elements.length; i++)
                        {
                            elements[i].checked = false;
                            marker = false;
                        }
                    }
                }
            </script>


            <input type="checkbox" onClick="mark()" /><?php echo TXT_ALL; ?>

            <table width="100%" border="0">
                <tr>
                    <td class="dataTableHeadingContent" width="9%"><?php echo HEADING_ADD; ?></td>
                    <td class="dataTableHeadingContent" width="10%"><?php echo HEADING_GROUP; ?></td>
                    <td class="dataTableHeadingContent" width="10%"><?php echo HEADING_MODEL; ?></td>
                    <td class="dataTableHeadingContent" width="34%"><?php echo HEADING_NAME; ?></td>
                    <td class="dataTableHeadingContent" width="42%"><?php echo HEADING_CATEGORY; ?></td>
                </tr>
                <?php
                $search_query = "SELECT * FROM " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p WHERE p.products_id=pd.products_id and pd.language_id='" . $_SESSION['languages_id'] . "' and p.products_id!='" . $_GET['current_product_id'] . "' and (pd.products_name LIKE '%" . $_GET['search'] . "%' or p.products_model LIKE '%" . $_GET['search'] . "%')";
                $search_query = xtc_db_query($search_query);

                while ($search_data = xtc_db_fetch_array($search_query)) {
                    $categorie_query = xtc_db_query("SELECT
						                                            categories_id
						                                            FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
						                                            WHERE products_id='" . $search_data['products_id'] . "' LIMIT 0,1");
                    $categorie_data = xtc_db_fetch_array($categorie_query);
                    ?>
                    <tr>
                        <td class="categories_view_data"><input type="checkbox" name="ids[]" value="<?php echo $search_data['products_id']; ?>" /></td>
                        <td class="categories_view_data" style="text-align: left;"><?php echo xtc_draw_pull_down_menu('group_name[' . $search_data['products_id'] . ']', $cross_sell_groups); ?></td>
                        <td class="categories_view_data" style="text-align: left;"><?php echo $search_data['products_model']; ?></td>
                        <td class="categories_view_data" style="text-align: left;"><?php echo $search_data['products_name']; ?></td>
                        <td class="categories_view_data" style="text-align: left;"><?php echo buildCAT($categorie_data['categories_id']); ?> </td>
                    </tr>

                    <?php
                }
                ?>

            </table>
            <input type="submit" class="button" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')">
            <?php
            echo '</form>';
        }
        ?>

