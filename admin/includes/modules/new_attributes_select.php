<?php
/* -----------------------------------------------------------------
 * 	$Id: new_attributes_select.php 420 2013-06-19 18:04:39Z akausch $
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
$adminImages = DIR_WS_CATALOG . "lang/" . $_SESSION['language'] . "/admin/images/buttons/";
?>
<tr>
    <td class="pageHeading" colspan="3">
        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="pageHeading">
                    <?php echo $pageTitle; ?>
                </td>
            </tr>
        </table>
    </td>
</tr>
<form action="<?php echo basename($_SERVER['SCRIPT_NAME']); ?>" name="SELECT_PRODUCT" method="post"><input type="hidden" name="action" value="edit">
    <?php echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()); ?>
    <tr>
        <td class="main">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top" width="50%">
                        <br /><strong><?php echo SELECT_PRODUCT ?><br /><br />
                            <select name="current_product_id" style="width: 300px">
                                <?php
                                $query = "SELECT * FROM  " . TABLE_PRODUCTS_DESCRIPTION . "  where products_id LIKE '%' AND language_id = '" . $_SESSION['languages_id'] . "' ORDER BY products_name ASC";

                                $result = xtc_db_query($query);

                                $matches = xtc_db_num_rows($result);

                                if ($matches) {
                                    while ($line = xtc_db_fetch_array($result)) {
                                        $title = $line['products_name'];
                                        $current_product_id = $line['products_id'];

                                        echo '<option value="' . $current_product_id . '">' . $title;
                                    }
                                } else {
                                    echo "Es wurden derzeit noch keine Produkte angelegt.";
                                }
                                ?>
                            </select><br /><br />
                            <?php echo xtc_button(BUTTON_EDIT); ?><br />
                    </td>
                    <td valign="top">
                        <br /><strong><?php echo SELECT_COPY ?><br /><br />
                            <select name="copy_product_id" style="width: 300px">
                                <?php
                                $copy_query = xtc_db_query("SELECT pd.products_name, pd.products_id FROM  " . TABLE_PRODUCTS_DESCRIPTION . "  pd, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = pd.products_id AND pd.products_id LIKE '%' AND pd.language_id = '" . $_SESSION['languages_id'] . "' GROUP BY pd.products_id ORDER BY pd.products_name ASC");
                                $copy_count = xtc_db_num_rows($copy_query);

                                if ($copy_count) {
                                    echo '<option value="0">no copy</option>';
                                    while ($copy_res = xtc_db_fetch_array($copy_query)) {
                                        echo '<option value="' . $copy_res['products_id'] . '">' . $copy_res['products_name'] . '</option>';
                                    }
                                } else {
                                    echo 'No products to copy attributes from';
                                }
                                ?>
                            </select><br /><br />
                            <?php echo xtc_button(BUTTON_EDIT); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</form>