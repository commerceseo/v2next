<?php
/* -----------------------------------------------------------------
 * 	$Id: new_category.php 1039 2014-05-12 16:01:33Z akausch $
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
defined("_VALID_XTC") or die("Direct access to this location isn't allowed.");

if (($_GET['cID']) && (!$_POST)) {
    $category_query = xtc_db_query("SELECT * 
						FROM 
						" . TABLE_CATEGORIES . " c, 
						" . TABLE_CATEGORIES_DESCRIPTION . " cd
	                    WHERE 
							c.categories_id = cd.categories_id
	                    AND 
							c.categories_id = '" . $_GET['cID'] . "'");

    $category = xtc_db_fetch_array($category_query);
    $cInfo = new objectInfo($category);
} elseif ($_POST) {
    $cInfo = new objectInfo($_POST);
    $categories_name = xtc_db_prepare_input($_POST['categories_name']);
    $categories_url_alias = xtc_db_prepare_input($_POST['categories_url_alias']);
    $categories_heading_title = xtc_db_prepare_input($_POST['categories_heading_title']);
    $categories_google_taxonomie = xtc_db_prepare_input($_POST['categories_google_taxonomie']);
    $categories_description = xtc_db_prepare_input($_POST['categories_description']);
    $categories_short_description = xtc_db_prepare_input($_POST['categories_short_description']);
    $categories_description_footer = xtc_db_prepare_input($_POST['categories_description_footer']);
    $categories_pic_alt = xtc_db_prepare_input($_POST['categories_pic_alt']);
    $categories_pic_footer_alt = xtc_db_prepare_input($_POST['categories_pic_footer_alt']);
    $categories_pic_nav_alt = xtc_db_prepare_input($_POST['categories_pic_nav_alt']);
    $categories_meta_title = xtc_db_prepare_input($_POST['categories_meta_title']);
    $categories_meta_description = xtc_db_prepare_input($_POST['categories_meta_description']);
    $categories_meta_keywords = xtc_db_prepare_input($_POST['categories_meta_keywords']);
} else {
    $cInfo = new objectInfo(array());
}

$languages = xtc_get_languages();

$text_new_or_edit = ($_GET['action'] == 'new_category_ACD') ? TEXT_INFO_HEADING_NEW_CATEGORY : TEXT_INFO_HEADING_EDIT_CATEGORY;
echo '<script type="text/javascript" src="includes/javascript/categories.js"></script>';
if (USE_WYSIWYG == 'true') {
    echo '<script src="includes/editor/ckeditor/ckeditor.js" type="text/javascript"></script>';
	echo '<script src="includes/editor/ckfinder/ckfinder.js" type="text/javascript"></script>';
}
if (file_exists(DIR_WS_INCLUDES . 'addons/new_category_addon.php')) {
	include (DIR_WS_INCLUDES .'addons/new_category_addon.php');
}

$form_action = ($_GET['cID']) ? 'update_category' : 'insert_category';
echo xtc_draw_form('new_category', FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID'] . '&action=' . $form_action, 'post', 'enctype="multipart/form-data"');
echo '<h1>' . sprintf($text_new_or_edit, xtc_output_generated_category_path($current_category_id)) . '</h1><hr/>';
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td align="right">
            <input type="submit" class="button" name="update_category" value="<?php echo BUTTON_SAVE; ?>">&nbsp;&nbsp;
            <?php echo'<a class="button" href="' . xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID']) . '">' . BUTTON_CANCEL . '</a>'; ?>
        </td>
    </tr>
</table>

<div id="cattabs">
    <ul>
        <li><a href="#base"><?php echo HEADING_BASE; ?></a></li>
        <li><a href="#descr"><?php echo HEAD_DESCRIPTION; ?></a></li>
		<?php 
		if (file_exists(DIR_WS_INCLUDES . 'addons/new_category_tabs_nav_addon.php')) {
			include (DIR_WS_INCLUDES .'addons/new_category_tabs_nav_addon.php');
		}
		?>
        <li><a href="#img"><?php echo HEADING_IMAGE; ?></a></li>
		
    </ul>
    <!-- Basis -->
    <div id="base">
        <table>
            <?php include_once(DIR_WS_MODULES.'is_menue.php');?>
            <?php
                $section_array = array();
                $section_array = array(array('id' => 0, 'text' => TXT_SECTION_STANDARD), array('id' => 1, 'text' => TXT_SECTION_ONE), array('id' => 2, 'text' => TXT_SECTION_TWO), array('id' => 3, 'text' => TXT_SECTION_THREE), array('id' => 4, 'text' => TXT_SECTION_FOUR), array('id' => 5, 'text' => TXT_SECTION_FIVE), array('id' => 6, 'text' => TXT_SECTION_SIX), array('id' => 7, 'text' => TXT_SECTION_SEVEN), array('id' => 8, 'text' => TXT_SECTION_EIGHT), array('id' => 9, 'text' => TXT_SECTION_NINE), array('id' => 10, 'text' => TXT_SECTION_TEN), array('id' => 11, 'text' => TXT_SECTION_ELEVEN));
			?>		  
			<tr>
				<td class="main gray" width="20%"><?php echo TABLE_HEADING_SECTION; ?></td>
				<td class="main gray" width="80%"><?php echo xtc_draw_pull_down_menu('section', $section_array, $cInfo->section); ?></td>
			</tr>
			<?php for ($i = 0; $i < sizeof($languages); $i++) { 
				$slider_set_sql = xtc_db_query("SELECT slider_id AS id, slider_title AS text FROM cseo_slider_gallery GROUP BY slider_title");
				$slider_set_array = array(array('id' => 0, 'text' => TEXT_SELECT));
				while ($slider_set = xtc_db_fetch_array($slider_set_sql)) {
					$slider_set_array[] = array('id' => $slider_set['id'], 'text' => $slider_set['text']);
				}
				?>		  
			<tr>
				<td class="main" width="20%"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' ' . TEXT_SLIEDER_SET; ?></td>
				<td class="main" width="80%"><?php echo xtc_draw_pull_down_menu('slider_set[' . $languages[$i]['id'] . ']', $slider_set_array, $cInfo->slider_set); ?></td>
			</tr>
			<?php } ?>
            <tr>
                <?php
                $files = array();
                if ($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/categorie_listing/')) {
                    while (($file = readdir($dir)) !== false) {
                        if (is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/categorie_listing/' . $file) and ($file != "index.html")) {
                            $files[] = array('id' => $file, 'text' => $file);
                        }
                    }
                    closedir($dir);
                }
                $default_array = array();
                if ($content['content_file'] == '') {
                    $default_array[] = array('id' => 'default', 'text' => TEXT_SELECT);
                    $default_value = $cInfo->categories_template;
                    $files = array_merge($default_array, $files);
                } else {
                    $default_array[] = array('id' => 'default', 'text' => TEXT_NO_FILE);
                    $default_value = $cInfo->categories_template;
                    $files = array_merge($default_array, $files);
                }
                ?>
                <td class="main gray"><?php echo TEXT_CHOOSE_INFO_TEMPLATE_CATEGORIE ?></td>
                <td class="main gray"><?php echo xtc_draw_pull_down_menu('categories_template', $files, $default_value) ?></td>
            </tr>
            <tr>
                <?php
                $files = array();
                if ($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_listing/')) {
                    while (($file = readdir($dir)) !== false) {
                        if (is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_listing/' . $file) and ($file != "index.html")) {
                            $files[] = array('id' => $file, 'text' => $file);
                        }
                    }
                    closedir($dir);
                }
                $default_array = array();
                if ($content['content_file'] == '') {
                    $default_array[] = array('id' => 'default', 'text' => TEXT_SELECT);
                    $default_value = $cInfo->listing_template;
                    $files = array_merge($default_array, $files);
                } else {
                    $default_array[] = array('id' => 'default', 'text' => TEXT_NO_FILE);
                    $default_value = $cInfo->listing_template;
                    $files = array_merge($default_array, $files);
                }
                ?>
                <td class="main"><?php echo TEXT_CHOOSE_INFO_TEMPLATE_LISTING ?></td>
                <td class="main"><?php echo xtc_draw_pull_down_menu('listing_template', $files, $default_value) ?></td>
            </tr>
            <tr>
                <td class="main gray"><?php echo TEXT_EDIT_STATUS; ?>:</td>
                <td class="main gray">
                    <?php
                    if (isset($_GET['cID'])) {
                        echo xtc_draw_selection_field('status', 'checkbox', '1', $cInfo->categories_status == 1 ? true : false);
                    } else {
                        echo xtc_draw_selection_field('status', 'checkbox', '1', true);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="main"><?php echo TEXT_CAT_BOX_START; ?>:</td>
                <td class="main">
                    <?php
                    if (isset($_GET['cID'])) {
                        echo xtc_draw_selection_field('mainstatus', 'checkbox', '1', $cInfo->categories_main_status == 1 ? true : false);
                    } else {
                        echo xtc_draw_selection_field('mainstatus', 'checkbox', '1', true);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="main gray"><?php echo TEXT_CAT_BOX_CONTENT; ?>:</td>
                <td class="main gray">
                    <?php
                    if (isset($_GET['cID'])) {
                        echo xtc_draw_selection_field('contentstatus', 'checkbox', '1', $cInfo->categories_content_status == 1 ? true : false);
                    } else {
                        echo xtc_draw_selection_field('contentstatus', 'checkbox', '0', false);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <?php
                $order_array = '';
                $order_array = array(array('id' => 'p.products_price', 'text' => TXT_PRICES), array('id' => 'pd.products_name', 'text' => TXT_NAME), array('id' => 'p.products_ordered', 'text' => TXT_ORDERED), array('id' => 'p.products_sort', 'text' => TXT_SORT), array('id' => 'p.products_date_added', 'text' => TXT_AGE), array('id' => 'p.products_weight', 'text' => TXT_WEIGHT), array('id' => 'p.products_quantity', 'text' => TXT_QTY));
                $default_value = 'pd.products_name';
                ?>
                <td class="main"><?php echo TEXT_EDIT_PRODUCT_SORT_ORDER; ?>:</td>
                <td class="main"><?php echo xtc_draw_pull_down_menu('products_sorting', $order_array, $cInfo->products_sorting); ?></td>
            </tr>
            <tr>
                <?php
                $order_array = '';
                $order_array = array(array('id' => 'ASC', 'text' => 'ASC (aufsteigend)'), array('id' => 'DESC', 'text' => 'DESC (absteigend)'));
                ?>	
                <td class="main gray"><?php echo TEXT_EDIT_PRODUCT_SORT_ORDER2; ?>:</td>
                <td class="main gray"><?php echo xtc_draw_pull_down_menu('products_sorting2', $order_array, $cInfo->products_sorting2); ?></td>
            </tr>
            <tr>
                <td class="main"><?php echo TEXT_EDIT_SORT_ORDER; ?></td>
                <td class="main"><?php echo xtc_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'); ?></td>
            </tr>
			<?php 
			if (file_exists(DIR_WS_INCLUDES . 'addons/new_category_fields_addon.php')) {
				include (DIR_WS_INCLUDES .'addons/new_category_fields_addon.php');
			}
			?>
            <tr>
                <td class="main gray"><?php echo TEXT_TEMPLATE_COLUMN; ?> <br /><?php echo xtc_image('images/template_cat.gif', 'Kategorie Seite', 'align="right"'); ?></td>
                <td class="main gray">
                    <?php
                    if (isset($_GET['cID'])) {
                        echo xtc_draw_selection_field('categories_col_top', 'checkbox', '1', $cInfo->categories_col_top == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_TOP . '<br />';
                        echo xtc_draw_selection_field('categories_col_left', 'checkbox', '1', $cInfo->categories_col_left == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_LEFT . '<br />';
                        echo xtc_draw_selection_field('categories_col_right', 'checkbox', '1', $cInfo->categories_col_right == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_RIGHT . '<br />';
                        echo xtc_draw_selection_field('categories_col_bottom', 'checkbox', '1', $cInfo->categories_col_bottom == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_BUTTON . '<br />';
                    } else {
                        echo xtc_draw_selection_field('categories_col_top', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_TOP . '<br />';
                        echo xtc_draw_selection_field('categories_col_left', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_LEFT . '<br />';
                        echo xtc_draw_selection_field('categories_col_right', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_RIGHT . '<br />';
                        echo xtc_draw_selection_field('categories_col_bottom', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_BUTTON . '<br />';
                    }
                    ?>
                </td>
            </tr>
            <?php
            if (GROUP_CHECK == 'true') {
                $customers_statuses_array = xtc_get_customers_statuses();
                $customers_statuses_array = array_merge(array(array('id' => 'all', 'text' => TXT_ALL)), $customers_statuses_array);
                ?>
                <tr>
                    <td class="main yellow"><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
                    <td class="main yellow">
                        <?php
                        for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
                            if ($category['group_permission_' . $customers_statuses_array[$i]['id']] == 1) {
                                $checked = 'checked ';
                            } else {
                                $checked = '';
                            }
                            echo '<input type="checkbox" name="groups[]" value="' . $customers_statuses_array[$i]['id'] . '"' . $checked . '> ' . $customers_statuses_array[$i]['text'] . '<br />';
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php
        if (function_exists(draw_cat_fields)) {
            draw_cat_fields($cInfo);
        }
        ?>
    </div>
    <!-- Basis -->
    <!-- Beschreibung -->
    <div id="descr">
        <?php
        if (function_exists(draw_cat_desc_fields)) {
            draw_cat_desc_fields($cInfo);
        }
        ?>
        <div id="tabslang">
            <ul>
                <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                    <li>
                        <a href="#language_<?php echo $i; ?>" onclick="javascript:return false;">
                            <span> <?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' ' . $languages[$i]['name']; ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <?php for ($i = 0; $i < sizeof($languages); $i++) { ?>
                <div id="language_<?php echo $i; ?>">
                    <table width="100%" border="0">
                        <tr>
                            <td class="main" width="20%"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?> <?php echo TEXT_EDIT_CATEGORIES_NAME; ?></td>
                            <td class="main" width="80%"><?php echo xtc_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_name')), 'size=60 style="width:90%;"'); ?></td>
                        </tr>

                        <tr>
                            <td class="main gray"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?> <?php echo TEXT_EDIT_CATEGORIES_HEADING_TITLE; ?></td>
                            <td class="main gray"><?php echo xtc_draw_input_field('categories_heading_title[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_heading_title')), 'size=60 style="width:90%;"'); ?></td>
                        </tr>
                        <tr>
                            <td class="main"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?> <?php echo TEXT_META_TITLE; ?></td>
                            <td class="main"><?php echo xtc_draw_input_field('categories_meta_title[' . $languages[$i]['id'] . ']', (($categories_meta_title[$languages[$i]['id']]) ? stripslashes($categories_meta_title[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_meta_title')), 'size=60 style="width:90%;"'); ?> </td>
                        </tr>
                        <tr>
                            <td class="main gray"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?> <?php echo TEXT_META_DESCRIPTION; ?></td>
                            <td class="main gray"><?php echo xtc_draw_input_field('categories_meta_description[' . $languages[$i]['id'] . ']', (($categories_meta_description[$languages[$i]['id']]) ? stripslashes($categories_meta_description[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_meta_description')), 'size=60 style="width:90%;"'); ?></td>
                        </tr>
                        <tr>
                            <td class="main"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?> <?php echo TEXT_META_KEYWORDS; ?></td>
                            <td class="main"> <?php echo xtc_draw_input_field('categories_meta_keywords[' . $languages[$i]['id'] . ']', (($categories_meta_keywords[$languages[$i]['id']]) ? stripslashes($categories_meta_keywords[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_meta_keywords')), 'size=60 style="width:90%;"'); ?></td>
                        </tr>
                        <tr>
                            <td class="main gray"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?> URL Alias:</td>
                            <td class="main gray"><?php echo xtc_draw_input_field('categories_url_alias[' . $languages[$i]['id'] . ']', (($categories_url_alias[$languages[$i]['id']]) ? stripslashes($categories_url_alias[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_url_alias')), 'size=60 style="width:90%;"'); ?></td>
                        </tr>
                        <tr>
                            <td class="main"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?> <?php echo TEXT_EDIT_CATEGORIES_GOOGLE_TAX_TITLE; ?></td>
                            <td class="main"><?php echo xtc_draw_input_field('categories_google_taxonomie[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_google_taxonomie')), 'id="GOOGLE_MERCHANT" size=60'); ?></td>
                        </tr>
                        <tr>
                            <td class="main"><?php echo TEXT_EDIT_CATEGORIES_GOOGLE_TAX_NEW_TITLE; ?></td>
                            <td id="google_taxonomy"></td>
                        </tr>
                        <tr>
                            <td class="main gray" valign="top"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?> <?php echo TEXT_EDIT_CATEGORIES_DESCRIPTION; ?></td>
                            <td class="main gray" valign="top">
                                <?php echo xtc_draw_textarea_field('categories_description[' . $languages[$i]['id'] . ']', 'soft', '70', '25', (($categories_description[$languages[$i]['id']]) ? stripslashes($categories_description[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_description')), 'class="ckeditor" name="editor1"'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="main gray" valign="top"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?> <?php echo TEXT_EDIT_CATEGORIES_SHORT_DESCRIPTION; ?></td>
                            <td class="main gray" valign="top">
                                <?php echo xtc_draw_textarea_field('categories_short_description[' . $languages[$i]['id'] . ']', 'soft', '70', '25', (($categories_short_description[$languages[$i]['id']]) ? stripslashes($categories_short_description[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_short_description')), 'class="ckeditor" name="editor1"'); ?>
                            </td>
                        </tr>
                        <tr>

                            <td class="main" valign="top"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']); ?> <?php echo TEXT_EDIT_CATEGORIES_DESCRIPTION_FOOTER; ?></td>
                            <td class="main" valign="top">
                                <?php echo xtc_draw_textarea_field('categories_description_footer[' . $languages[$i]['id'] . ']', 'soft', '70', '25', (($categories_description_footer[$languages[$i]['id']]) ? stripslashes($categories_description_footer[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_description_footer')), 'class="ckeditor" name="editor1"'); ?>
                            </td>
                        </tr>
                    </table>
                    <?php
                    if (USE_WYSIWYG == 'true') {
                            ?>	
                            <script type="text/javascript">
                                var newCKEdit = CKEDITOR.replace('<?php echo 'categories_short_description[' . $languages[$i]['id'] . ']' ?>');
                                CKFinder.setupCKEditor(newCKEdit, 'includes/editor/ckfinder/');
                            </script>
                            <script type="text/javascript">
                                var newCKEdit = CKEDITOR.replace('<?php echo 'categories_description[' . $languages[$i]['id'] . ']' ?>');
                                CKFinder.setupCKEditor(newCKEdit, 'includes/editor/ckfinder/');
                            </script>
                            <script type="text/javascript">
                                var newCKEdit = CKEDITOR.replace('<?php echo 'categories_description_footer[' . $languages[$i]['id'] . ']' ?>');
                                CKFinder.setupCKEditor(newCKEdit, 'includes/editor/ckfinder/');
                            </script>
                            <?php
                    }
                    ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- Beschreibung -->
    <!-- Addons -->
	
	<?php 
	if (file_exists(DIR_WS_INCLUDES . 'addons/new_category_tabs_addon.php')) {
		include (DIR_WS_INCLUDES .'addons/new_category_tabs_addon.php');
	}
	?>
    <!-- Bilder -->
    <div id="img">
        <table>
            <tr>
                <td class="main" width="20%" valign="top">
                    <?php if ($cInfo->categories_image) { ?>
                        <img class="categories_img" src="<?php echo DIR_WS_CATALOG . 'images/categories/' . $cInfo->categories_image; ?>" alt="" />
                    <?php } ?>
                </td>
                <td class="main">
                    <table width="100%">
                        <tr>
                            <td width="15%"><?php echo HEADING_IMAGE_SEARCH; ?></td>
                            <td class="main">
                                <?php
                                echo xtc_draw_file_field('categories_image') . xtc_draw_hidden_field('categories_previous_image', $cInfo->categories_image);
                                ?>
                            </td>
                        </tr>
                        <?php for ($i = 0; $i < sizeof($languages); $i++) { ?>
                            <tr>
                                <td width="15%"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) . '&nbsp;' . HEADING_IMAGE_ALT; ?></td>
                                <td class="main">
                                    <?php echo xtc_draw_input_field('categories_pic_alt[' . $languages[$i]['id'] . ']', (($categories_pic_alt[$languages[$i]['id']]) ? stripslashes($categories_pic_alt[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_pic_alt')), 'size=30'); ?>
                                </td>
                            </tr>
                        <?php }
                        if ($cInfo->categories_image) {
                            ?>
                            <tr>
                                <td width="15%">&nbsp;</td>
                                <td class="main">
                                    <?php
                                    echo $cInfo->categories_image . '&nbsp;' . xtc_draw_selection_field('del_cat_pic', 'checkbox', 'yes') . TEXT_DELETE;
                                    ?>
                                </td>
                            </tr>
                    <?php } ?>
                    </table>
<?php echo TEXT_CAT_PIC_PATH . 'images/categories/'; ?>
                </td>
            </tr>

            <tr>
                <td class="main" valign="top">
                    <?php if ($cInfo->categories_footer_image) { ?>
                        <img class="categories_img" src="<?php echo DIR_WS_CATALOG . 'images/categories_footer/' . $cInfo->categories_footer_image; ?>" alt="" />
<?php } ?>
                </td>
                <td class="main">
                    <table>
                        <tr>
                            <td width="15%"><?php echo HEADING_IMAGE_FOOTER_SEARCH; ?></td>
                            <td class="main">
                                <?php
                                echo xtc_draw_file_field('categories_footer_image') . xtc_draw_hidden_field('categories_footer_previous_image', $cInfo->categories_footer_image);
                                ?>
                            </td>
                        </tr>
<?php for ($i = 0; $i < sizeof($languages); $i++) { ?>
                            <tr>
                                <td width="15%"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) . '&nbsp;' . HEADING_IMAGE_ALT; ?></td>
                                <td class="main">
    <?php echo xtc_draw_input_field('categories_pic_footer_alt[' . $languages[$i]['id'] . ']', (($categories_pic_footer_alt[$languages[$i]['id']]) ? stripslashes($categories_pic_footer_alt[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_pic_footer_alt')), 'size=30'); ?>
                                </td>
                            </tr>
                        <?php }
                        if ($cInfo->categories_footer_image) {
                            ?>
                            <tr>
                                <td width="15%">&nbsp;</td>
                                <td class="main">
                                    <?php
                                    echo $cInfo->categories_footer_image . '&nbsp;' . xtc_draw_selection_field('del_cat_pic_footer', 'checkbox', 'yes') . TEXT_DELETE;
                                    ?>
                                </td>
                            </tr>
                    <?php } ?>
                    </table>
<?php echo TEXT_CAT_PIC_PATH . 'images/categories_footer/'; ?>
                </td>
            </tr>
            <tr>
                <td class="main" valign="top">
                    <?php if ($cInfo->categories_nav_image) { ?>
                        <img class="categories_img" src="<?php echo DIR_WS_CATALOG . 'images/categories_nav/' . $cInfo->categories_nav_image; ?>" alt="" />
<?php } ?>
                </td>
                <td class="main">
                    <table width="100%">
                        <tr>
                            <td width="15%"><?php echo HEADING_IMAGE_NAV_SEARCH; ?></td>
                            <td class="main">
                                <?php
                                echo xtc_draw_file_field('categories_nav_image') . xtc_draw_hidden_field('categories_nav_previous_image', $cInfo->categories_nav_image);
                                ?>
                            </td>
                        </tr>
<?php for ($i = 0; $i < sizeof($languages); $i++) { ?>
                            <tr>
                                <td width="15%"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) . '&nbsp;' . HEADING_IMAGE_ALT; ?></td>
                                <td class="main">
                            <?php echo xtc_draw_input_field('categories_pic_nav_alt[' . $languages[$i]['id'] . ']', (($categories_pic_nav_alt[$languages[$i]['id']]) ? stripslashes($categories_pic_nav_alt[$languages[$i]['id']]) : xtc_get_categories_field($cInfo->categories_id, $languages[$i]['id'], 'categories_pic_nav_alt')), 'size=30'); ?>
                                </td>
                            </tr>
                        <?php }

                        if ($cInfo->categories_nav_image) {
                            ?>
                            <tr>
                                <td width="15%">&nbsp;</td>
                                <td class="main">
    <?php
    echo $cInfo->categories_nav_image . '&nbsp;' . xtc_draw_selection_field('del_cat_pic_nav', 'checkbox', 'yes') . TEXT_DELETE;
    ?>
                                </td>
                            </tr>
<?php } ?>
                    </table>
<?php echo TEXT_CAT_PIC_PATH . 'images/categories_nav_image/'; ?>
                </td>
            </tr>
        </table>	

    </div>
    <!-- Bilder -->
    <br class="clear" />	

</div>

<?php echo xtc_draw_hidden_field('categories_date_added', (($cInfo->date_added) ? $cInfo->date_added : date('Y-m-d'))) . xtc_draw_hidden_field('parent_id', $cInfo->parent_id); ?> 
<?php echo xtc_draw_hidden_field('categories_id', $cInfo->categories_id); ?> 
<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td align="right">
            <input type="submit" class="button" name="update_category" value="<?php echo BUTTON_SAVE; ?>" style="cursor:pointer">&nbsp;&nbsp;
<?php echo'<a class="button" href="' . xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID']) . '">' . BUTTON_CANCEL . '</a>'; ?>
        </td>
    </tr>
</table>
</form>

<script type="text/javascript">
    xajax_get_new_dropdown();
</script>