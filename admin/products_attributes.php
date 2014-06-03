<?php
/* -----------------------------------------------------------------
 * 	$Id: products_attributes.php 871 2014-03-20 09:13:14Z akausch $
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

require_once('includes/application_top.php');
include_once(DIR_FS_ADMIN . DIR_WS_CLASSES . FILENAME_IMAGEMANIPULATOR);
$languages = xtc_get_languages();

if ($_GET['action']) {
    $page_info = 'option_page=' . $_GET['option_page'] . '&value_page=' . $_GET['value_page'] . '&attribute_page=' . $_GET['attribute_page'];
    switch ($_GET['action']) {

        case 'add_product_options':
            $option_name = $_POST['option_name'];
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_OPTIONS . " (products_options_id, products_options_sortorder, products_options_name, language_id) VALUES ('" . $_POST['products_options_id'] . "', '" . $_POST['products_options_sortorder'] . "', '" . $option_name[$languages[$i]['id']] . "', '" . $languages[$i]['id'] . "')");
            }
            xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
            break;

        case 'add_product_option_values':

            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                $value_name = $_POST['value_name'];
                $value_desc = $_POST['value_desc'];
                $new_attribut_sql = '';

                if ($image = &xtc_try_upload('value_image_' . $languages[$i]['id'], DIR_FS_CATALOG_IMAGES . 'product_options/')) {
                    $paname_arr = explode('.', $image->filename);
                    $pnsuffix = array_pop($paname_arr);
                    $value_image_name = $_POST['value_id'] . '_' . $languages[$i]['id'] . '.' . $pnsuffix;

                    @unlink(DIR_FS_CATALOG_IMAGES . 'product_options/' . $value_image_name);
                    rename(DIR_FS_CATALOG_IMAGES . 'product_options/' . $image->filename, DIR_FS_CATALOG_IMAGES . 'product_options/' . $value_image_name);
					$product_option_thumb_image_name = $value_image_name;
					require(DIR_WS_INCLUDES . 'product_option_thumb_image.php');
                }

                $new_attribut_sql = array('products_options_values_id' => $_POST['value_id'],
                    'language_id' => $languages[$i]['id'],
                    'products_options_values_name' => $value_name[$languages[$i]['id']],
                    'products_options_values_desc' => $value_desc[$languages[$i]['id']],
                    'products_options_values_image' => $value_image_name);

                xtc_db_perform(TABLE_PRODUCTS_OPTIONS_VALUES, $new_attribut_sql);
            }

            $new_value_to_option_sql = array('products_options_id' => $_POST['option_id'],
                'products_options_values_id' => $_POST['value_id']);

            xtc_db_perform(TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS, $new_value_to_option_sql);

            xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
            break;

        case 'add_product_attributes':
            $add_product_attributes_sql = array('products_id' => $_POST['products_id'],
                'options_id' => $_POST['options_id'],
                'options_values_id' => $_POST['values_id'],
                'options_values_price' => $_POST['value_price'],
                'price_prefix' => $_POST['price_prefix']);

            xtc_db_perform(TABLE_PRODUCTS_ATTRIBUTES, $add_product_attributes_sql);

            $products_attributes_id = xtc_db_insert_id();
            if ((DOWNLOAD_ENABLED == 'true') && $_POST['products_attributes_filename'] != '') {
                $download_sql = array('products_attributes_id' => $products_attributes_id,
                    'products_attributes_filename' => $_POST['products_attributes_filename'],
                    'products_attributes_maxdays' => $_POST['products_attributes_maxdays'],
                    'products_attributes_maxcount' => $_POST['products_attributes_maxcount']);

                xtc_db_perform(TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD, $download_sql);
            }
            xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
            break;

        case 'update_option_name':
            $option_name = $_POST['option_name'];
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                $products_options_query = xtc_db_query("select * from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $languages[$i]['id'] . "' and products_options_id = '" . $_POST['option_id'] . "'");
                if (xtc_db_num_rows($products_options_query) == 0) {
                    xtc_db_perform(TABLE_PRODUCTS_OPTIONS, array('products_options_id' => $_POST['option_id'], 'language_id' => $languages[$i]['id']));
                }
                xtc_db_query("update " . TABLE_PRODUCTS_OPTIONS . " set products_options_name = '" . $option_name[$languages[$i]['id']] . "', products_options_sortorder = '" . $_POST['products_options_sortorder'] . "' where products_options_id = '" . $_POST['option_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
            }
            xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
            break;

        case 'update_value':
            $value_name = $_POST['value_name'];
            $value_desc = $_POST['value_desc'];

            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                if ($_FILES['value_image_' . $languages[$i]['id']]['name'] != '') {
                    if ($image = &xtc_try_upload('value_image_' . $languages[$i]['id'], DIR_FS_CATALOG_IMAGES . 'product_options/')) {
                        $paname_arr = explode('.', $image->filename);
                        $pnsuffix = array_pop($paname_arr);
                        $value_image_name = $_POST['value_id'] . '_' . $languages[$i]['id'] . '.' . $pnsuffix;

                        @unlink(DIR_FS_CATALOG_IMAGES . 'product_options/' . $value_image_name);
                        rename(DIR_FS_CATALOG_IMAGES . 'product_options/' . $image->filename, DIR_FS_CATALOG_IMAGES . 'product_options/' . $value_image_name);
						$product_option_thumb_image_name = $value_image_name;
						require(DIR_WS_INCLUDES . 'product_option_thumb_image.php');

                        $image_sql = array('products_options_values_image' => $value_image_name);
                    }
                } elseif ($_POST['del_value_image_' . $languages[$i]['id']] == 'yes') {
                    $image_sql = array('products_options_values_image' => '');
                } else {
                    $image_sql = '';
				}

                $update_value_sql = array('products_options_values_name' => $value_name[$languages[$i]['id']],
										'products_options_values_desc' => $value_desc[$languages[$i]['id']]);

                if (!empty($image_sql))
                    $update_value_sql = array_merge($update_value_sql, $image_sql);

                xtc_db_perform(TABLE_PRODUCTS_OPTIONS_VALUES, $update_value_sql, 'update', "products_options_values_id = '" . $_POST['value_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
            }
            xtc_db_query("update " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " set products_options_id = '" . $_POST['option_id'] . "' where products_options_values_id = '" . $_POST['value_id'] . "'");
            xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
            break;

        case 'update_product_attribute':
            xtc_db_query("update " . TABLE_PRODUCTS_ATTRIBUTES . " set products_id = '" . $_POST['products_id'] . "', options_id = '" . $_POST['options_id'] . "', options_values_id = '" . $_POST['values_id'] . "', options_values_price = '" . $_POST['value_price'] . "', price_prefix = '" . $_POST['price_prefix'] . "' where products_attributes_id = '" . $_POST['attribute_id'] . "'");
            if ((DOWNLOAD_ENABLED == 'true') && $_POST['products_attributes_filename'] != '') {
                xtc_db_query("update " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
				set products_attributes_filename='" . $_POST['products_attributes_filename'] . "',
				products_attributes_maxdays='" . $_POST['products_attributes_maxdays'] . "',
				products_attributes_maxcount='" . $_POST['products_attributes_maxcount'] . "'
				where products_attributes_id = '" . $_POST['attribute_id'] . "'");
            }
            xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
            break;

        case 'delete_option':

            $del_options = xtc_db_query("select products_options_values_id from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_id = '" . $_GET['option_id'] . "'");

            while ($del_options_values = xtc_db_fetch_array($del_options))
                xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_GET['option_id'] . "'");

            xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_id = '" . $_GET['option_id'] . "'");
            xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $_GET['option_id'] . "'");

            xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
            break;

        case 'delete_value':
            xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_GET['value_id'] . "'");
            xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_GET['value_id'] . "'");
            xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_values_id = '" . $_GET['value_id'] . "'");
            xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
            break;

        case 'delete_attribute':
            xtc_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_attributes_id = '" . $_GET['attribute_id'] . "'");
            xtc_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " where products_attributes_id = '" . $_GET['attribute_id'] . "'");
            xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
            break;
    }
}
require(DIR_WS_INCLUDES . 'header.php');
?>
<script type="text/javascript">
    <!--
        function go_option() {
        if (document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value != "none") {
            location = "<?php echo xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_page=' . ($_GET['option_page'] ? $_GET['option_page'] : 1)); ?>&option_order_by=" + document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value;
        }
    }
    //-->
</script>

<div id="optiontabs">

    <ul>
        <li><a href="#tabattr"><?php echo HEADING_ATTRIBUTE; ?></a></li>
        <li><a href="#taboption"><?php echo HEAD_OPTION; ?></a></li>
    </ul>
    <div id="tabattr">

        <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <?php
            if ($_GET['action'] == 'delete_product_option') { // delete product option
                $options = xtc_db_query("SELECT products_options_id, products_options_sortorder, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " WHERE products_options_id = '" . $_GET['option_id'] . "' AND language_id = '" . $_SESSION['languages_id'] . "'");
                $options_values = xtc_db_fetch_array($options);
                ?>
                <tr>
                    <td class="pageHeading"><?php echo $options_values['products_options_name']; ?></td>
                </tr>
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <?php
                            $products = xtc_db_query("select p.products_id, pd.products_name, pov.products_options_values_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov, " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pov.language_id = '" . $_SESSION['languages_id'] . "' and pd.language_id = '" . $_SESSION['languages_id'] . "' and pa.products_id = p.products_id and pa.options_id='" . $_GET['option_id'] . "' and pov.products_options_values_id = pa.options_values_id order by pd.products_name");
                            if (xtc_db_num_rows($products)) {
                                ?>
                                <tr class="dataTableHeadingRow">
                                    <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ID; ?></td>
                                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT; ?></td>
                                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_OPT_VALUE; ?></td>
                                </tr>
                                <?php
                                while ($products_values = xtc_db_fetch_array($products)) {
                                    $rows++;
                                    ?>
                                    <tr class="<?php echo (floor($rows / 2) == ($rows / 2) ? 'attributes-even' : 'attributes-odd'); ?>">
                                        <td align="center"><?php echo $products_values['products_id']; ?></td>
                                        <td><?php echo $products_values['products_options_sortorder']; ?></td>
                                        <td><?php echo $products_values['products_name']; ?></td>
                                        <td><?php echo $products_values['products_options_values_name']; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="3" class="main"><br />
                                        <?php echo TEXT_WARNING_OF_DELETE; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" colspan="3" class="main"><br />
                                        <?php echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&value_page=' . $_GET['value_page'] . '&attribute_page=' . $attribute_page, 'NONSSL')); ?>
                                    </td>
                                </tr>
                            <?php } else { ?>
                                <tr>
                                    <td class="main" colspan="3"><br />
                                        <?php echo TEXT_OK_TO_DELETE; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="main" align="right" colspan="3"><br />
                                        <?php echo xtc_button_link(BUTTON_DELETE, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_option&option_id=' . $_GET['option_id'], 'NONSSL')); ?><?php echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&order_by=' . $order_by . '&page=' . $page, 'NONSSL')); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>
            <?php
            } else {
                if ($_GET['option_order_by'])
                    $option_order_by = $_GET['option_order_by'];
                else
                    $option_order_by = 'products_options_id';
                ?>
                <tr>
                    <td colspan="2" class="pageHeading"><?php echo HEADING_TITLE_OPT; ?></td>
                    <td align="right"><br />
                        <table border="0">
                            <tr>
                                <td>
                                    <form name="search" action="<?php echo FILENAME_PRODUCTS_ATTRIBUTES; ?>" method="GET">
    <?php echo TEXT_SEARCH; ?>
                                        <input type="text" name="searchoption" size="20" value="<?php echo $_GET['searchoption']; ?>" />
                                    </form>
                                </td>
                                <td>
                                    <form name="option_order_by" action="<?php echo FILENAME_PRODUCTS_ATTRIBUTES; ?>">
                                        <select name="selected" onChange="go_option()">
                                            <option value="products_options_id"<?php if ($option_order_by == 'products_options_id') {
        echo ' SELECTED';
    } ?>> <?php echo TEXT_OPTION_ID; ?></option>
                                            <option value="products_options_name"<?php if ($option_order_by == 'products_options_name') {
        echo ' SELECTED';
    } ?>> <?php echo TEXT_OPTION_NAME; ?></option>
                                            <option value="products_options_sortorder"<?php if ($option_order_by == 'products_options_sortorder') {
        echo ' SELECTED';
    } ?>>	<?php echo TEXT_SORTORDER; ?></option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                <tr>
                    <td colspan="3">
                        <?php
                        $option_page = (int) $_GET['option_page'];
                        $per_page = MAX_ROW_LISTS_OPTIONS;
                        if (isset($_GET['searchoption'])) {
                            $options = "SELECT * FROM " . TABLE_PRODUCTS_OPTIONS . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' AND products_options_name LIKE '%" . $_GET['searchoption'] . "%' ORDER BY " . $option_order_by;
                        } else {
                            $options = "SELECT * FROM " . TABLE_PRODUCTS_OPTIONS . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY " . $option_order_by;
                        }
                        if (!$option_page) {
                            $option_page = 1;
                        }

                        $prev_option_page = $option_page - 1;
                        $next_option_page = $option_page + 1;

                        $option_query = xtc_db_query($options);

                        $option_page_start = ($per_page * $option_page) - $per_page;
                        $num_rows = xtc_db_num_rows($option_query);

                        if ($num_rows <= $per_page) {
                            $num_pages = 1;
                        } elseif (($num_rows % $per_page) == 0) {
                            $num_pages = ($num_rows / $per_page);
                        } else {
                            $num_pages = ($num_rows / $per_page) + 1;
                        }

                        $num_pages = (int) $num_pages;

                        $options = $options . " LIMIT $option_page_start, $per_page";

                        // back
                        if ($prev_option_page) {
                            echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_page=' . $prev_option_page . '&searchoption=' . $_GET['searchoption']) . '"> &lt;&lt; </a> | ';
                        }

                        for ($i = 1; $i <= $num_pages; $i++) {
                            if ($i != $option_page) {
                                echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_page=' . $i . '&searchoption=' . $_GET['searchoption']) . '">' . $i . '</a> | ';
                            } else {
                                echo '<b><span style="color:#b20000">' . $i . '</span></b> | ';
                            }
                        }

                        // vor
                        if ($option_page != $num_pages) {
                            echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_page=' . $next_option_page . '&searchoption=' . $_GET['searchoption']) . '"> &gt;&gt; </a>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ID; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SORTORDER; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_OPT_NAME; ?></td>
                    <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ACTION; ?></td>
                </tr>
                    <?php
                    $next_id = 1;
                    $options = xtc_db_query($options);
                    while ($options_values = xtc_db_fetch_array($options)) {
                        $rows++;
                        ?>
                    <tr class="<?php echo (floor($rows / 2) == ($rows / 2) ? 'attributes-even' : 'attributes-odd'); ?>">
                        <?php
                        if (($_GET['action'] == 'update_option') && ($_GET['option_id'] == $options_values['products_options_id'])) {
                            echo '<form name="option" action="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_name&option_page=' . $_GET['option_page'] . '&searchoption=' . $_GET['searchoption'], 'NONSSL') . '" method="post">';
                            $inputs = '';
                            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                $option_name = xtc_db_fetch_array(xtc_db_query("SELECT products_options_name FROM " . TABLE_PRODUCTS_OPTIONS . " WHERE products_options_id = '" . $options_values['products_options_id'] . "' AND language_id = '" . $languages[$i]['id'] . "';"));
                                $inputs .= xtc_image('../lang/' . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '<input type="text" name="option_name[' . $languages[$i]['id'] . ']" size="20" value="' . $option_name['products_options_name'] . '"><br />';
                            }
                            ?>
                            <td align="center">
            <?php echo $options_values['products_options_id']; ?>
                                <input type="hidden" name="option_id" value="<?php echo $options_values['products_options_id']; ?>" />
                            </td>
                            <td align="left" class="smallText"><?php echo TABLE_HEADING_SORTORDER; ?>:<input type="text" name="products_options_sortorder" size="4" value="<?php echo $options_values['products_options_sortorder']; ?>"></td>
                            <td><?php echo $inputs; ?></td>
                            <td align="center">
                                <?php echo xtc_button(BUTTON_UPDATE); ?> <?php echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '', 'NONSSL')); ?>
                            </td>
                            </form>
                        <?php } else { ?>
                            <td align="center"><?php echo $options_values["products_options_id"]; ?></td>
                            <td class="smallText"><?php echo $options_values["products_options_sortorder"]; ?></td>
                            <td><?php echo $options_values["products_options_name"]; ?></td>
                            <td align="center">
                        <?php echo xtc_button_link(BUTTON_EDIT, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option&option_id=' . $options_values['products_options_id'] . '&option_order_by=' . $option_order_by . '&option_page=' . $option_page . '&searchoption=' . $_GET['searchoption'], 'NONSSL')); ?><?php echo xtc_button_link(BUTTON_DELETE, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_product_option&option_id=' . $options_values['products_options_id'], 'NONSSL')); ?>
                            </td>
                    <?php } ?>
                    </tr>
                        <?php
                        $max_options_id_query = xtc_db_query("SELECT max(products_options_id) + 1 AS next_id FROM " . TABLE_PRODUCTS_OPTIONS . ";");
                        $max_options_id_values = xtc_db_fetch_array($max_options_id_query);
                        $next_id = $max_options_id_values['next_id'];
                    }
                    if ($_GET['action'] != 'update_option') {
                        ?>
                    <tr class="<?php echo (floor($rows / 2) == ($rows / 2) ? 'attributes-even' : 'attributes-odd'); ?>">
        <?php
        echo '<form name="options" action="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=add_product_options&option_page=' . $option_page, 'NONSSL') . '" method="post">
				<input type="hidden" name="products_options_id" value="' . $next_id . '" />';
        $inputs = '';
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
            $inputs .= xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' <input type="text" name="option_name[' . $languages[$i]['id'] . ']" size="20"><br />';
        ?>
                        <td align="center"><?php echo $next_id; ?></td>
                        <td class="smallText"><?php echo TABLE_HEADING_SORTORDER . ':<input type="text" name="products_options_sortorder" size="4" value="' . $option_name['products_options_sortorder'] . '">'; ?></td>
                        <td><?php echo $inputs; ?></td>
                        <td align="center"><?php echo xtc_button(BUTTON_INSERT); ?></td>
                        </form>
                    </tr>
        <?php
    }
}
?>
        </table>
    </div>
    <div id="taboption">
        <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <!-- value //-->
<?php
if ($_GET['action'] == 'delete_option_value') { // delete product option value
    $values = xtc_db_query("SELECT products_options_values_id, products_options_values_name FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " WHERE products_options_values_id = '" . $_GET['value_id'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "'");
    $values_values = xtc_db_fetch_array($values);
    ?>
                <tr>
                    <td colspan="3" class="pageHeading"><?php echo $values_values['products_options_values_name']; ?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <?php
    $products = xtc_db_query("SELECT p.products_id, pd.products_name, po.products_options_name FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS . " po, " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE pd.products_id = p.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "' AND po.language_id = '" . (int) $_SESSION['languages_id'] . "' AND pa.products_id = p.products_id AND pa.options_values_id='" . $_GET['value_id'] . "' AND po.products_options_id = pa.options_id ORDER BY pd.products_name;");
    if (xtc_db_num_rows($products, true) > 0) {
        ?>
                                <tr class="dataTableHeadingRow">
                                    <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ID; ?></td>
                                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT; ?></td>
                                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_OPT_NAME; ?></td>
                                </tr>
                                <?php while ($products_values = xtc_db_fetch_array($products)) {
                                    $rows++;
                                    ?>
                                    <tr class="<?php echo (floor($rows / 2) == ($rows / 2) ? 'attributes-even' : 'attributes-odd'); ?>">
                                        <td align="center"><?php echo $products_values['products_id']; ?></td>
                                        <td><?php echo $products_values['products_name']; ?></td>
                                        <td><?php echo $products_values['products_options_name']; ?></td>
                                    </tr>
                                        <?php } ?>
                                <tr>
                                    <td class="main" colspan="3">
        <?php echo TEXT_WARNING_OF_DELETE; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="main" align="right" colspan="3">
        <?php echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&value_page=' . $_GET['value_page'] . '&attribute_page=' . $attribute_page, 'NONSSL')); ?></td>
                                </tr>
                                    <?php } else { ?>
                                <tr>
                                    <td class="main" colspan="3">
                                <?php echo TEXT_OK_TO_DELETE; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="main" align="right" colspan="3">
                    <?php echo xtc_button_link(BUTTON_DELETE, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_value&value_id=' . $_GET['value_id'], 'NONSSL')); ?><?php echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&option_page=' . $option_page . '&value_page=' . $_GET['value_page'] . '&attribute_page=' . $attribute_page, 'NONSSL')); ?>
                                    </td>
                                </tr>
    <?php } ?>
                        </table>
                    </td>
                </tr>
                                    <?php } else {
                                        ?>
                <tr>
                    <td colspan="3" class="pageHeading"><?php echo HEADING_TITLE_VAL; ?></td>
                    <td colspan="2" align="right">
                        <table border="0">
                            <tr>
                                <td>
                                    <form name="search" action="<?php echo FILENAME_PRODUCTS_ATTRIBUTES; ?>" method="GET">
										<?php echo TEXT_SEARCH; ?> 
										<input type="text" name="search_optionsname" size="20" value="<?php echo $_GET['search_optionsname']; ?>">
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
					<?php
                        $per_page = MAX_ROW_LISTS_OPTIONS;
                        if (isset($_GET['search_optionsname'])) {
                            $values = "SELECT DISTINCT
								pov.products_options_values_id,
								pov.products_options_values_name,
								pov2po.products_options_id
							FROM 
								" . TABLE_PRODUCTS_OPTIONS . " po,
								" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
							LEFT JOIN 
								" . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po
									ON pov.products_options_values_id = pov2po.products_options_values_id
							WHERE 
								pov.language_id = '" . $_SESSION['languages_id'] . "'
							AND 
								pov2po.products_options_id = po.products_options_id
							AND (po.products_options_name 
									LIKE 
										'%" . $_GET['search_optionsname'] . "%' 
									OR 
										pov.products_options_values_name 
									LIKE 
										'%" . $_GET['search_optionsname'] . "%')
							ORDER BY 
								pov.products_options_values_id";
                        } else {
                            $values = "SELECT
								pov.products_options_values_id,
								pov.products_options_values_name,
								pov2po.products_options_id
							FROM 
								" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
							LEFT JOIN 
								" . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po
							ON 
								pov.products_options_values_id = pov2po.products_options_values_id
							WHERE 
								pov.language_id = '" . (int)$_SESSION['languages_id'] . "'
							ORDER BY 
								pov.products_options_values_id";
                        }
                        if (!$_GET['value_page']) {
                            $_GET['value_page'] = 1;
						}

                        $prev_value_page = $_GET['value_page'] - 1;
                        $next_value_page = $_GET['value_page'] + 1;

                        $value_query = xtc_db_query($values);

                        $value_page_start = ($per_page * $_GET['value_page']) - $per_page;
                        $num_rows = xtc_db_num_rows($value_query);

                        if ($num_rows <= $per_page) {
                            $num_pages = 1;
                        } elseif (($num_rows % $per_page) == 0) {
                            $num_pages = ($num_rows / $per_page);
                        } else {
                            $num_pages = ($num_rows / $per_page) + 1;
						}

                        $num_pages = (int) $num_pages;
                        $values = $values . " LIMIT $value_page_start, $per_page";

                        if ($prev_value_page) {
                            echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by=' . $option_order_by . '&value_page=' . $prev_value_page . '&search_optionsname=' . $_GET['search_optionsname']) . '"> &lt;&lt; </a> | ';
						}

                        for ($i = 1; $i <= $num_pages; $i++) {
                            if ($i != $_GET['value_page'])
                                echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by=' . $option_order_by . '&value_page=' . $i . '&search_optionsname=' . $_GET['search_optionsname']) . '">' . $i . '</a> | ';
                            else
                                echo '<b><span style="color:#b20000">' . $i . '</span></b> | ';
                        }
                        if ($_GET['value_page'] != $num_pages) {
                            echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by=' . $option_order_by . '&value_page=' . $next_value_page . '&search_optionsname=' . $_GET['search_optionsname']) . '"> &gt;&gt;</a> ';
						}
                        ?>
                    </td>
                </tr>

                <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" width="5%"><?php echo TABLE_HEADING_ID; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_OPT_NAME; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_OPT_VALUE; ?></td>
                    <td class="dataTableHeadingContent" align="center" width="30%"><?php echo TABLE_HEADING_ACTION; ?></td>
                </tr>
                <?php
                $next_id = 1;
                $values = xtc_db_query($values);
                while ($values_values = xtc_db_fetch_array($values)) {
                    $options_name = xtc_options_name($values_values['products_options_id']);
                    $values_name = $values_values['products_options_values_name'];
                    $rows++;
                    if (($_GET['action'] == 'update_option_value') && ($_GET['value_id'] == $values_values['products_options_values_id'])) {
                        echo '<form name="values" action="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_value&value_page=' . $_GET['value_page'] . '&search_optionsname=' . $_GET['search_optionsname'], 'NONSSL') . '" method="post" enctype="multipart/form-data">';
							for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
								$tr = (floor($rows + $i / 2) == ($rows + $i / 2) ? 'attributes-even' : 'attributes-odd');
				?>
                            <tr class="<?php echo $tr; ?>">
                                <td class="attributs_new" width="5%">
                                    <?php echo $values_values['products_options_values_id']; ?>
                                    <input type="hidden" name="value_id" value="<?php echo $values_values['products_options_values_id']; ?>">
                                </td>
                                <td class="attributs_new" width="15%">
                                    <strong><?php echo TABLE_HEADING_OPT_NAME; ?></strong>
                                </td>
                                <td class="attributs_new" width="40%" colspan="2">
                                        <?php
                                        echo xtc_image('../lang/' . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']);
                                        ?>
                                    <select name="option_id">
                                        <?php
                                        $options_names = xtc_db_query("SELECT 
																	products_options_id, 
																	products_options_name 
																FROM 
																	" . TABLE_PRODUCTS_OPTIONS . " 
																WHERE 
																	language_id = '" . $languages[$i]['id'] . "' 
																ORDER BY 
																	products_options_name;");
                                        while ($options_values = xtc_db_fetch_array($options_names)) {
                                            echo "\n" . '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '"';
                                            if ($values_values['products_options_id'] == $options_values['products_options_id'])
                                                echo ' selected';
                                            echo '>' . $options_values['products_options_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <?php
                            $value_name = xtc_db_fetch_array(xtc_db_query("SELECT 
																		products_options_values_name, 
																		products_options_values_desc, 
																		products_options_values_image 
																	FROM 
																		" . TABLE_PRODUCTS_OPTIONS_VALUES . " 
																	WHERE 
																		products_options_values_id = '" . $values_values['products_options_values_id'] . "' 
																	AND 
																		language_id = '" . $languages[$i]['id'] . "';"));
                            $inputs = '';
                            $inputs_desc = '';
                            $input_img = '';
                            $input_del = '';

                            $inputs = xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' <input value="' . $value_name['products_options_values_name'] . '" type="text" name="value_name[' . $languages[$i]['id'] . ']" size="15"><br />';
                            $inputs_image = xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' ' . xtc_draw_file_field('value_image_' . $languages[$i]['id']) . '<br />';
                            if (!empty($value_name['products_options_values_image'])) {
                                $input_img = '<img src="'.DIR_WS_CATALOG_IMAGES.'product_options/' . $value_name['products_options_values_image'] . '" alt="" />';
                                $input_del = $value_name['products_options_values_image'] . ' L&ouml;schen? <input type="checkbox" name="del_value_image_' . $languages[$i]['id'] . '" value="yes" />';
                            }
                            $inputs_desc = xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' <textarea name="value_desc[' . $languages[$i]['id'] . ']" cols="50" rows="4" style="width:95%">' . $value_name['products_options_values_desc'] . '</textarea><br />';
                            ?>
                            <tr class="<?php echo $tr; ?>">
                                <td class="attributs_new" width="5%"></td>
                                <td class="attributs_name">
                                    <strong><?php echo TABLE_HEADING_OPT_VALUE; ?>:</strong>
                                </td>
                                <td class="attributs_name" colspan="2">
									<?php echo $inputs; ?>
                                </td>

                            </tr>
                            <tr class="<?php echo $tr; ?>">
                                <td class="attributs_new" width="5%"></td>
                                <td class="attributs_name" valign="top">
                                    <strong><?php echo TABLE_HEADING_OPT_IMAGE; ?>:</strong>
                                </td>
                                <td class="attributs_name" colspan="2">
                                    <?php echo $inputs_image . ' ' . $input_img . ' ' . $input_del; ?>
                                </td>
                            </tr>
                            <tr class="<?php echo $tr; ?>">
                                <td class="attributs_new" width="5%"></td>
                                <td class="attributs_name" valign="top">
                                    <strong><?php echo TABLE_HEADING_OPT_DESC; ?>:</strong>
                                </td>
                                <td class="attributs_name" colspan="2">
                                    <?php echo $inputs_desc; ?>
                                </td>
                            </tr>
                        <?php } ?> 
                        <tr class="<?php echo $tr; ?>">
                            <td class="attributs_new" width="5%"></td>
                            <td class="attributs_name"></td>
                            <td colspan="2">
							<?php 
							echo xtc_button(BUTTON_UPDATE);
							echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '', 'NONSSL')); 
							?>
                            </td>
                        </tr>
                        </form>
                            <?php } else { ?>
                        <tr class="<?php echo (floor($rows / 2) == ($rows / 2) ? 'attributes-even' : 'attributes-odd'); ?>">
                            <td width="5%">
                                <?php echo $values_values["products_options_values_id"]; ?>
                            </td>
                            <td>
                            <?php echo $options_name; ?>
                            </td>
                            <td>
                            <?php echo $values_name; ?>
                            </td>
                            <td align="center">
                            <?php echo xtc_button_link(BUTTON_EDIT, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id=' . $values_values['products_options_values_id'] . '&value_page=' . $_GET['value_page'] . '&search_optionsname=' . $_GET['search_optionsname'], 'NONSSL')); ?>
                            <?php echo xtc_button_link(BUTTON_DELETE, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_option_value&value_id=' . $values_values['products_options_values_id'], 'NONSSL')); ?>
                            </td>
                        <?php
                    }
                    $max_values_id_query = xtc_db_query("SELECT max(products_options_values_id) + 1 AS next_id FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . ";");
                    $max_values_id_values = xtc_db_fetch_array($max_values_id_query);
                    $next_id = $max_values_id_values['next_id'];
                }
                ?>
                </tr>

    <?php if ($_GET['action'] != 'update_option_value') { ?> 
                    <tr>
        <?php
        echo '<form name="values" action="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=add_product_option_values&value_page=' . $_GET['value_page'], 'NONSSL') . '" method="post" enctype="multipart/form-data">';
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            ?>
                            <td class="attributs_new" width="5%">
                                <?php echo $next_id; ?>
                            </td>
                            <td class="attributs_new" width="15%">
                                <strong><?php echo TABLE_HEADING_OPT_NAME; ?>:</strong>
                            </td>
                            <td class="attributs_new" width="55%" colspan="2">
                                    <?php
                                    echo xtc_image('../lang/' . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']);
                                    ?>
                                <select name="option_id">
                                    <?php
                                    $options = xtc_db_query("SELECT 
														products_options_id, 
														products_options_name 
													FROM 
														" . TABLE_PRODUCTS_OPTIONS . " 
													WHERE 
														language_id = '" . $languages[$i]['id'] . "' 
													ORDER BY 
														products_options_name;");
                                    while ($options_values = xtc_db_fetch_array($options)) {
                                        echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '">' . $options_values['products_options_name'] . '</option>';
									}

                                    $inputs = '';
                                    $inputs_desc = '';

                                    $inputs = xtc_image('../lang/' . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' <input type="text" name="value_name[' . $languages[$i]['id'] . ']" size="15"><br />';
									$inputs_image = xtc_image('../lang/' . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' ' . xtc_draw_file_field('value_image_' . $languages[$i]['id']) . '<br />';
                                    $inputs_desc = xtc_image('../lang/' . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' <textarea name="value_desc[' . $languages[$i]['id'] . ']" cols="50" rows="4" style="width:95%"></textarea><br />';
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="attributs_new" width="5%"></td>
                            <td class="attributs_name" width="20%">
                                <strong><?php echo TABLE_HEADING_OPT_VALUE; ?>:</strong>
                            </td>
                            <td class="attributs_name" colspan="2">
                                <input type="hidden" name="value_id" value="<?php echo $next_id; ?>" />
            <?php echo $inputs; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="attributs_name" width="5%"></td>
                            <td class="attributs_name" valign="top">
                                <strong><?php echo TABLE_HEADING_OPT_IMAGE; ?>:</strong>
                            </td>
                            <td class="attributs_name" colspan="2">
                                <input type="hidden" name="value_id" value="<?php echo $next_id; ?>" />
            <?php echo $inputs_image; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="attributs_name" width="5%"></td>
                            <td class="attributs_name" valign="top">
                                <strong><?php echo TABLE_HEADING_OPT_DESC; ?>:</strong>
                            </td>
                            <td class="attributs_name" colspan="2" valign="top">
                                <input type="hidden" name="value_id" value="<?php echo $next_id; ?>" />
                                <?php echo $inputs_desc; ?>
                            </td>
                        </tr>
        <?php } ?>
                    <tr>
                        <td class="attributs_name" width="5%"></td>
                        <td class="attributs_name"></td>
                        <td class="attributs_name" colspan="2">
                            <input type="hidden" name="value_id" value="<?php echo $next_id; ?>">
        <?php echo xtc_button(BUTTON_INSERT); ?>
                        </td>
                    </tr>
                    </form>

        <?php
    }
}
?>
    </div>
</div>
</table>

<?php
echo OPTIONS_DESCRIPTION;
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
