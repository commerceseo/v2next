<?php
/* -----------------------------------------------------------------
 * 	$Id: manufacturers.php 434 2013-06-25 17:30:40Z akausch $
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

function xtc_remove_product($product_id) {
    $product_image_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . xtc_db_input($product_id) . "'");
    $product_image = xtc_db_fetch_array($product_image_query);

    $duplicate_image_query = xtc_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where products_image = '" . xtc_db_input($product_image['products_image']) . "'");
    $duplicate_image = xtc_db_fetch_array($duplicate_image_query);

    if ($duplicate_image['total'] < 2) {
        if (file_exists(DIR_FS_CATALOG_POPUP_IMAGES . $product_image['products_image'])) {
            @unlink(DIR_FS_CATALOG_POPUP_IMAGES . $product_image['products_image']);
        }
        // START CHANGES
        $image_subdir = BIG_IMAGE_SUBDIR;
        if (substr($image_subdir, -1) != '/')
            $image_subdir .= '/';
        if (file_exists(DIR_FS_CATALOG_IMAGES . $image_subdir . $product_image['products_image'])) {
            @unlink(DIR_FS_CATALOG_IMAGES . $image_subdir . $product_image['products_image']);
        }
        // END CHANGES
    }

    xtc_db_query("delete from " . TABLE_SPECIALS . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_PRODUCTS . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where products_id = '" . xtc_db_input($product_id) . "'");


    // get statuses
    $customers_statuses_array = array(array());

    $customers_statuses_query = xtc_db_query("select * from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . $LangID . "' order by customers_status_id");

    while ($customers_statuses = xtc_db_fetch_array($customers_statuses_query)) {
        $customers_statuses_array[] = array('id' => $customers_statuses['customers_status_id'],
            'text' => $customers_statuses['customers_status_name']);
    }

    for ($i = 0, $n = sizeof($customers_status_array); $i < $n; $i++) {
        xtc_db_query("delete from personal_offers_by_customers_status_" . $i . " where products_id = '" . xtc_db_input($product_id) . "'");
    }

    $product_reviews_query = xtc_db_query("select reviews_id from " . TABLE_REVIEWS . " where products_id = '" . xtc_db_input($product_id) . "'");
    while ($product_reviews = xtc_db_fetch_array($product_reviews_query)) {
        xtc_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $product_reviews['reviews_id'] . "'");
    }
    xtc_db_query("delete from " . TABLE_REVIEWS . " where products_id = '" . xtc_db_input($product_id) . "'");
}

//end function
//HERSTELLER:
switch ($_GET['action']) {
    case 'insert':
    case 'save':
        $manufacturers_id = xtc_db_prepare_input($_GET['mID']);
        $manufacturers_name = xtc_db_prepare_input($_POST['manufacturers_name']);
        $sql_data_array = array('manufacturers_name' => $manufacturers_name);

        if ($_GET['action'] == 'insert') {
            $insert_sql_data = array('date_added' => 'now()');
            $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
            xtc_db_perform(TABLE_MANUFACTURERS, $sql_data_array);
            $manufacturers_id = xtc_db_insert_id();
        } else if ($_GET['action'] == 'save') {
            $update_sql_data = array('last_modified' => 'now()');
            $sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
            xtc_db_perform(TABLE_MANUFACTURERS, $sql_data_array, 'update', "manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
        }

        $dir_manufacturers = DIR_FS_CATALOG_IMAGES . "/manufacturers";
        if ($manufacturers_image = &xtc_try_upload('manufacturers_image', $dir_manufacturers)) {
            xtc_db_query("UPDATE " . TABLE_MANUFACTURERS . " SET
									 manufacturers_image ='manufacturers/" . $manufacturers_image->filename . "'
									 WHERE manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
        }

        //andere db abfrage!
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $manufacturers_url_array = $_POST['manufacturers_url'];
            $language_id = $languages[$i]['id'];

            $manufacturers_description = $_POST['manufacturers_description'];
            $manufacturers_meta_title = $_POST['manufacturers_meta_title'];
            $manufacturers_meta_description = $_POST['manufacturers_meta_description'];
            $manufacturers_meta_keywords = $_POST['manufacturers_meta_keywords'];


            $sql_data_array = array('manufacturers_url' => xtc_db_prepare_input($manufacturers_url_array[$language_id]),
                'manufacturers_description' => xtc_db_prepare_input($manufacturers_description[$language_id]),
                'manufacturers_meta_title' => xtc_db_prepare_input($manufacturers_meta_title[$language_id]),
                'manufacturers_meta_description' => xtc_db_prepare_input($manufacturers_meta_description[$language_id]),
                'manufacturers_meta_keywords' => xtc_db_prepare_input($manufacturers_meta_keywords[$language_id]));

            if ($_GET['action'] == 'insert') {
                $insert_sql_data = array('manufacturers_id' => $manufacturers_id, 'languages_id' => $language_id,);
                $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);

                xtc_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);
            } else if ($_GET['action'] == 'save') {

                xtc_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', "manufacturers_id = '" . xtc_db_input($manufacturers_id) . "' AND languages_id = '" . $language_id . "'");
            }
        }

        if (USE_CACHE == 'true') {
            xtc_reset_cache_block('manufacturers');
        }

        xtc_redirect(xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers_id));
        break;
    case 'deleteconfirm':
        $manufacturers_id = xtc_db_prepare_input($_GET['mID']);

        if ($_POST['delete_image'] == 'on') {
            $manufacturer_query = xtc_db_query("SELECT manufacturers_image FROM " . TABLE_MANUFACTURERS . " WHERE manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
            $manufacturer = xtc_db_fetch_array($manufacturer_query);
            $image_location = DIR_FS_DOCUMENT_ROOT . DIR_WS_IMAGES . $manufacturer['manufacturers_image'];
            if (file_exists($image_location))
                @unlink($image_location);
        }

        xtc_db_query("DELETE FROM " . TABLE_MANUFACTURERS . " WHERE manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
        xtc_db_query("DELETE FROM " . TABLE_MANUFACTURERS_INFO . " WHERE manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");

        if ($_POST['delete_products'] == 'on') {
            $products_query = xtc_db_query("SELECT products_id from " . TABLE_PRODUCTS . " WHERE manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
            while ($products = xtc_db_fetch_array($products_query))
                xtc_remove_product($products['products_id']);
        }
        else
            xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET manufacturers_id = '' WHERE manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");


        if (USE_CACHE == 'true')
            xtc_reset_cache_block('manufacturers');

        xtc_redirect(xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page']));
        break;
}//switch

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
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
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top"><table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                        <tr class="dataTableHeadingRow">
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_MANUFACTURERS; ?></th>
                                            <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</th>
                                        </tr>
<?php
//Datenbank abfragen, Herstellerinfos beziehen:
$manufacturers_query_raw = "select manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified from " . TABLE_MANUFACTURERS . " order by manufacturers_name";
$manufacturers_split = new splitPageResults($_GET['page'], '20', $manufacturers_query_raw, $manufacturers_query_numrows);
$manufacturers_query = xtc_db_query($manufacturers_query_raw);
while ($manufacturers = xtc_db_fetch_array($manufacturers_query)) {
    $rows++;
    if (((!$_GET['mID']) || (@$_GET['mID'] == $manufacturers['manufacturers_id'])) && (!$mInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
        $manufacturer_products_query = xtc_db_query("select count(*) as products_count from " . TABLE_PRODUCTS . " where manufacturers_id = '" . $manufacturers['manufacturers_id'] . "'");
        $manufacturer_products = xtc_db_fetch_array($manufacturer_products_query);

        $mInfo_array = xtc_array_merge($manufacturers, $manufacturer_products);
        $mInfo = new objectInfo($mInfo_array);
    }

    if ((is_object($mInfo)) && ($manufacturers['manufacturers_id'] == $mInfo->manufacturers_id)) {
        echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers['manufacturers_id'] . '&action=edit') . '\'">' . "\n";
    } else {
        echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers['manufacturers_id']) . '\'">' . "\n";
    }
    ?>
                                            <td class="dataTableContent"><?php echo $manufacturers['manufacturers_name']; ?></td>
                                            <td class="dataTableContent" align="right">
                                            <?php
                                            if ((is_object($mInfo)) && ($manufacturers['manufacturers_id'] == $mInfo->manufacturers_id))
                                                echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
                                            else {
                                                echo '<a href="' . xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers['manufacturers_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                                echo ' <a href="' . xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers['manufacturers_id'] . '&action=edit') . '">' . xtc_image(DIR_WS_IMAGES . 'icon_edit.gif', IMAGE_ICON_EDIT) . '</a>';
                                            }
                                            ?>
                                                &nbsp;
                                            </td>
                                            <?php
                                            echo '</tr>';
                                        }//while
                                        //"Angezeigt x bis n (von m Herstellern)" erzeugen:
                                        ?>
                                    </table>
                                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                        <tr>
                                            <td class="smallText" valign="top"><?php echo $manufacturers_split->display_count($manufacturers_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS); ?></td>
                                            <td class="smallText" align="right"><?php echo $manufacturers_split->display_links($manufacturers_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                                        </tr>
                                    </table>
                                    <table width="100%">
                                        <?php
                                        if ($_GET['action'] != 'new') {
                                            ?>
                                            <tr>
                                                <td align="right" colspan="2" class="smallText"><?php echo xtc_button_link(BUTTON_INSERT, xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=new')); ?></td>
                                            </tr>
    <?php
}
?>
                                    </table>
                                </td>
<?php
//hersteller anlegen, berabeiten,usw:
$heading = array();
$contents = array();

switch ($_GET['action']) {

    case 'new':
        $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_MANUFACTURER . '</b>');

        //eingabefelder usw erzeugen:
        $contents = array('form' => xtc_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'action=insert', 'post', 'enctype="multipart/form-data"'));
        $contents[] = array('text' => TEXT_NEW_INTRO);
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_NAME . '<br>' . xtc_draw_input_field('manufacturers_name'));
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_IMAGE . '<br>' . xtc_draw_file_field('manufacturers_image'));

        $manufacturer_inputs_string = '';
        $manufacturer_description_string = "";
        $manufacturers_meta_title_string = "";
        $manufacturers_meta_description_string = "";
        $manufacturers_meta_keywords_string = "";
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $manufacturer_inputs_string .= '<br>' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . xtc_draw_input_field('manufacturers_url[' . $languages[$i]['id'] . ']');
            $manufacturer_description_string .= "<br>" . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . xtc_draw_textarea_field('manufacturers_description[' . $languages[$i]['id'] . "]", 10, 100, 150); //beschreibung erstellen
            $manufacturers_meta_title_string .= "<br>" . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . xtc_draw_input_field('manufacturers_meta_title[' . $languages[$i]['id'] . ']', '', 'size="50"');
            $manufacturers_meta_description_string .= "<br>" . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . xtc_draw_input_field('manufacturers_meta_description[' . $languages[$i]['id'] . ']', '', 'size="50"');
            $manufacturers_meta_keywords_string .= "<br>" . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . xtc_draw_input_field('manufacturers_meta_keywords[' . $languages[$i]['id'] . ']', '', 'size="50"');
        }
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_DESCRIPTION . "<br>" . $manufacturer_description_string);
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_META_TITLE . "<br>" . $manufacturers_meta_title_string);
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_META_DESCRIPTION . "<br>" . $manufacturers_meta_description_string);
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_META_KEYWORDS . "<br>" . $manufacturers_meta_keywords_string);

        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_URL . $manufacturer_inputs_string);
        $contents[] = array('align' => 'center', 'text' => '<br>' . xtc_button(BUTTON_SAVE) . '&nbsp;' . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID'])));
        break;

    case 'edit':
        $heading[] = array('text' => '<b>' . TEXT_HEADING_EDIT_MANUFACTURER . '</b>');

        $contents = array('form' => xtc_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
        $contents[] = array('text' => TEXT_EDIT_INTRO);
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_NAME . '<br>' . xtc_draw_input_field('manufacturers_name', $mInfo->manufacturers_name));
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_IMAGE . '<br>' . xtc_draw_file_field('manufacturers_image') . '<br>' . $mInfo->manufacturers_image);

        $manufacturer_inputs_string = '';
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $langID = $languages[$i]['id'];
            $manQuery = xtc_db_query("SELECT * FROM " . TABLE_MANUFACTURERS_INFO . " WHERE manufacturers_id='" . $mInfo->manufacturers_id . "' AND languages_id='" . $langID . "'");
            $manRes = xtc_db_fetch_array($manQuery);
            $manufacturer_inputs_string .= '<br>' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . xtc_draw_input_field('manufacturers_url[' . $languages[$i]['id'] . ']', xtc_get_manufacturer_url($mInfo->manufacturers_id, $languages[$i]['id']));
            $manufacturer_description_string .= "<br>" . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . xtc_draw_textarea_field('manufacturers_description[' . $langID . "]", 10, 100, 150, $manRes['manufacturers_description']); //beschreibung erstellen
            $manufacturers_meta_title_string .= "<br>" . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . xtc_draw_input_field('manufacturers_meta_title[' . $langID . "]", $manRes['manufacturers_meta_title'], 'size="50"'); //beschreibung erstellen
            $manufacturers_meta_description_string .= "<br>" . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . xtc_draw_input_field('manufacturers_meta_description[' . $langID . "]", $manRes['manufacturers_meta_description'], 'size="50"'); //beschreibung erstellen
            $manufacturers_meta_keywords_string .= "<br>" . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . xtc_draw_input_field('manufacturers_meta_keywords[' . $langID . "]", $manRes['manufacturers_meta_keywords'], 'size="50"'); //beschreibung erstellen
        }
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_DESCRIPTION . "<br>" . $manufacturer_description_string);
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_META_TITLE . "<br>" . $manufacturers_meta_title_string);
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_META_DESCRIPTION . "<br>" . $manufacturers_meta_description_string);
        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_META_KEYWORDS . "<br>" . $manufacturers_meta_keywords_string);

        $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_URL . $manufacturer_inputs_string);
        $contents[] = array('align' => 'center', 'text' => '<br>' . xtc_button(BUTTON_SAVE) . '&nbsp;' . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id)));
        break;

    case 'delete':
        $heading[] = array('text' => '<b>' . TEXT_HEADING_DELETE_MANUFACTURER . '</b>');

        $contents = array('form' => xtc_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=deleteconfirm'));
        $contents[] = array('text' => TEXT_DELETE_INTRO);
        $contents[] = array('text' => '<br><b>' . $mInfo->manufacturers_name . '</b>');
        $contents[] = array('text' => '<br>' . xtc_draw_checkbox_field('delete_image', '', true) . ' ' . TEXT_DELETE_IMAGE);

        if ($mInfo->products_count > 0) {
            $contents[] = array('text' => '<br>' . xtc_draw_checkbox_field('delete_products') . ' ' . TEXT_DELETE_PRODUCTS);
            $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $mInfo->products_count));
        }

        $contents[] = array('align' => 'center', 'text' => '<br>' . xtc_button(BUTTON_DELETE) . '&nbsp;' . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id)));
        break;

    default:
        if (is_object($mInfo)) {
            $heading[] = array('text' => '<b>' . $mInfo->manufacturers_name . '</b>');

            $contents[] = array('align' => 'center', 'text' => xtc_button_link(BUTTON_EDIT, xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=edit')) . '&nbsp;' . xtc_button_link(BUTTON_DELETE, xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=delete')));
            $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . xtc_date_short($mInfo->date_added));
            if (xtc_not_null($mInfo->last_modified))
                $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . xtc_date_short($mInfo->last_modified));
            $contents[] = array('text' => '<br>' . xtc_info_image($mInfo->manufacturers_image, $mInfo->manufacturers_name));
            $contents[] = array('text' => '<br>' . TEXT_PRODUCTS . ' ' . $mInfo->products_count);
        }
        break;
}

if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
    echo '            <td width="25%" class="border" valign="top">' . "\n";
    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
}
?>
                            </tr>
                        </table></td>
                </tr>
            </table></td>
    </tr>
</table>
                                <?php
                                require(DIR_WS_INCLUDES . 'footer.php');
                                require(DIR_WS_INCLUDES . 'application_bottom.php');

                                