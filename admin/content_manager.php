<?php
/* -----------------------------------------------------------------
 * 	$Id: content_manager.php 1056 2014-05-17 13:17:56Z akausch $
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
require_once(DIR_FS_INC . 'xtc_format_filesize.inc.php');
require_once(DIR_FS_INC . 'xtc_filesize.inc.php');
require_once(DIR_FS_INC . 'cseo_get_url_friendly_text.inc.php');
if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
    require_once (DIR_FS_INC . 'commerce_seo.inc.php');
    !$commerceSeo ? $commerceSeo = new CommerceSeo() : false;
}

$languages = xtc_get_languages();

if ($_GET['special'] == 'delete') {
    xtc_db_query("DELETE FROM " . TABLE_CONTENT_MANAGER . " where content_id='" . (int) $_GET['coID'] . "'");
    if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True')
        $commerceSeo->createSeoDBTable();
    xtc_redirect(xtc_href_link(FILENAME_CONTENT_MANAGER));
}

if ($_GET['special'] == 'delete_product') {
    xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_CONTENT . " where content_id='" . (int) $_GET['coID'] . "'");
}

if ($_GET['id'] == 'update' || $_GET['id'] == 'insert') {
    $group_ids = '';
    if (isset($_POST['groups']))
        foreach ($_POST['groups'] as $b) {
            $group_ids .= 'c_' . $b . "_group ,";
        }
    $customers_statuses_array = xtc_get_customers_statuses();
    if (strstr($group_ids, 'c_all_group')) {
        $group_ids = 'c_all_group,';
        for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
            $group_ids .='c_' . $customers_statuses_array[$i]['id'] . '_group,';
        }
    }
    $content_title = xtc_db_prepare_input($_POST['cont_title']);
    $content_header = xtc_db_prepare_input($_POST['cont_heading']);
    if (xtc_db_prepare_input($_POST['cont_url_alias'] != '')) {
        $url_alias = cseo_get_url_friendly_text($_POST['cont_url_alias']);
        $content_url_alias = xtc_db_prepare_input($url_alias);
    }
    $content_text = xtc_db_prepare_input($_POST['cont']);
    $coID = xtc_db_prepare_input($_POST['coID']);
    $upload_file = xtc_db_prepare_input($_POST['file_upload']);
    $content_status = xtc_db_prepare_input($_POST['status']);
    $content_language = xtc_db_prepare_input($_POST['language']);
    $select_file = xtc_db_prepare_input($_POST['select_file']);
    $file_flag = xtc_db_prepare_input($_POST['file_flag']);
    $slider_set = xtc_db_prepare_input($_POST['slider_set']);
    $content_out_link = xtc_db_prepare_input($_POST['content_out_link']);
    $content_link_target = xtc_db_prepare_input($_POST['content_link_target']);
    $content_link_type = xtc_db_prepare_input($_POST['content_link_type']);
    $content_col_top = xtc_db_prepare_input($_POST['content_col_top']);
    $content_col_left = xtc_db_prepare_input($_POST['content_col_left']);
    $content_col_right = xtc_db_prepare_input($_POST['content_col_right']);
    $content_col_bottom = xtc_db_prepare_input($_POST['content_col_bottom']);
    $parent_check = xtc_db_prepare_input($_POST['parent_check']);
    $parent_id = xtc_db_prepare_input($_POST['parent']);
    $group_id = xtc_db_prepare_input($_POST['content_group']);
    $group_ids = $group_ids;
    $sort_order = xtc_db_prepare_input($_POST['sort_order']);
    $content_meta_title = xtc_db_prepare_input($_POST['cont_meta_title']);
    $content_meta_description = xtc_db_prepare_input($_POST['cont_meta_description']);
    $content_meta_keywords = xtc_db_prepare_input($_POST['cont_meta_keywords']);

    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        if ($languages[$i]['code'] == $content_language)
            $content_language = $languages[$i]['id'];
    }

    $error = false;
    if (strlen($content_title) < 1) {
        $error = true;
        $messageStack->add(ERROR_TITLE, 'error');
    }

    if ($content_status == 'yes') {
        $content_status = 1;
    } else {
        $content_status = 0;
    }

    if ($error == false) {
        if ($select_file != 'default')
            $content_file_name = $select_file;

        if ($content_file = &xtc_try_upload('file_upload', DIR_FS_CATALOG . 'media/content/'))
            $content_file_name = $content_file->filename;

        $sql_data_array = array('languages_id' => $content_language,
            'content_title' => $content_title,
            'content_heading' => $content_header,
            'content_text' => $content_text,
            'content_file' => $content_file_name,
            'content_status' => $content_status,
            'parent_id' => $parent_id,
            'content_url_alias' => $content_url_alias,
            'group_ids' => $group_ids,
            'content_group' => $group_id,
            'sort_order' => $sort_order,
            'file_flag' => $file_flag,
            'slider_set' => $slider_set,
            'content_out_link' => $content_out_link,
            'content_link_target' => $content_link_target,
            'content_col_top' => $content_col_top,
            'content_col_left' => $content_col_left,
            'content_col_right' => $content_col_right,
            'content_col_bottom' => $content_col_bottom,
            'content_link_type' => $content_link_type,
            'content_meta_title' => $content_meta_title,
            'content_meta_description' => $content_meta_description,
            'content_meta_keywords' => $content_meta_keywords,
            'content_url_alias' => $content_url_alias);

        if ($_GET['id'] == 'update') {
            xtc_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array, 'update', "content_id = '" . $coID . "'");
            if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
                $commerceSeo->updateSeoDBTable('content', 'update', $group_id);
            }
        } else {
            xtc_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array);
            if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True')
                $commerceSeo->createSeoDBTable();
        }
        xtc_redirect(xtc_href_link(FILENAME_CONTENT_MANAGER));
    }
}

if ($_GET['id'] == 'update_product' || $_GET['id'] == 'insert_product') {
    $group_ids = '';
    if (isset($_POST['groups'])) {
        foreach ($_POST['groups'] as $b) {
            $group_ids .= 'c_' . $b . "_group ,";
        }
    }
    $customers_statuses_array = xtc_get_customers_statuses();
    if (strstr($group_ids, 'c_all_group')) {
        $group_ids = 'c_all_group,';
        for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
            $group_ids .='c_' . $customers_statuses_array[$i]['id'] . '_group,';
        }
    }

    $content_title = xtc_db_prepare_input($_POST['cont_title']);
    $content_link = xtc_db_prepare_input($_POST['cont_link']);
    $content_url_alias = xtc_db_prepare_input($_POST['content_url_alias']);
    $content_out_link = xtc_db_prepare_input($_POST['content_out_link']);
    $content_link_target = xtc_db_prepare_input($_POST['content_link_target']);
    $content_link_type = xtc_db_prepare_input($_POST['content_link_type']);
    $content_language = xtc_db_prepare_input($_POST['language']);
    $product = xtc_db_prepare_input($_POST['product']);
    $upload_file = xtc_db_prepare_input($_POST['file_upload']);
    $filename = xtc_db_prepare_input($_POST['file_name']);
    $coID = xtc_db_prepare_input($_POST['coID']);
    $file_comment = xtc_db_prepare_input($_POST['file_comment']);
    $select_file = xtc_db_prepare_input($_POST['select_file']);
    $group_ids = $group_ids;

    $error = false;

    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        if ($languages[$i]['code'] == $content_language)
            $content_language = $languages[$i]['id'];
    }

    if (strlen($content_title) < 1) {
        $error = true;
        $messageStack->add(ERROR_TITLE, 'error');
    }
    if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True')
        $commerceSeo->createSeoDBTable();

    if ($error == false) {

        if ($select_file == 'default') {
            if ($content_file = &xtc_try_upload('file_upload', DIR_FS_CATALOG . 'media/products/')) {
                $content_file_name = $content_file->filename;
                $old_filename = $content_file->filename;
                $timestamp = str_replace('.', '', microtime());
                $timestamp = str_replace(' ', '', $timestamp);
                $content_file_name = $timestamp . strstr($content_file_name, '.');
                $rename_string = DIR_FS_CATALOG . 'media/products/' . $content_file_name;
                rename(DIR_FS_CATALOG . 'media/products/' . $old_filename, $rename_string);
                copy($rename_string, DIR_FS_CATALOG . 'media/products/backup/' . $content_file_name);
            }
            if ($content_file_name == '')
                $content_file_name = $filename;
        } else {
            $content_file_name = $select_file;
        }

        // set allowed c.groups
        $group_ids = '';
        if (isset($_POST['groups']))
            foreach ($_POST['groups'] as $b) {
                $group_ids .= 'c_' . $b . "_group ,";
            }
        $customers_statuses_array = xtc_get_customers_statuses();
        if (strstr($group_ids, 'c_all_group')) {
            $group_ids = 'c_all_group,';
            for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
                $group_ids .='c_' . $customers_statuses_array[$i]['id'] . '_group,';
            }
        }

        $sql_data_array = array('products_id' => $product,
            'group_ids' => $group_ids,
            'content_name' => $content_title,
            'content_file' => $content_file_name,
            'content_link' => $content_link,
            'file_comment' => $file_comment,
            'languages_id' => $content_language);

        if ($_GET['id'] == 'update_product') {
            xtc_db_perform(TABLE_PRODUCTS_CONTENT, $sql_data_array, 'update', "content_id = '" . $coID . "'");
            $content_id = xtc_db_insert_id();
        } else {
            xtc_db_perform(TABLE_PRODUCTS_CONTENT, $sql_data_array);
            $content_id = xtc_db_insert_id();
        }

        // rename filename
        if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
            $commerceSeo->createSeoDBTable();
        }
        xtc_redirect(xtc_href_link(FILENAME_CONTENT_MANAGER, 'pID=' . $product));
    }
}

require(DIR_WS_INCLUDES . 'header.php');
if (USE_WYSIWYG == 'false' && USE_CODEMIRROR == 'true') {
    ?>
    <script src="includes/javascript/code_editor/codemirror.js" type="text/javascript"></script>
    <script src="includes/javascript/code_editor/xml.js" type="text/javascript"></script>
    <script src="includes/javascript/code_editor/javascript.js" type="text/javascript"></script>
    <script src="includes/javascript/code_editor/css.js" type="text/javascript"></script>
    <script src="includes/javascript/code_editor/htmlmixed.js" type="text/javascript"></script>
    <link rel="stylesheet" href="includes/javascript/code_editor/codemirror.css" type="text/css" />
    <style type=text/css>
        iframe {
            width: 95%;
            height: 300px;
            border: 1px solid black;
        }
    </style>
    <?php
}
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE_CONTENT_MANAGER; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" border="0">
                            <tr>
                                <td>
                                    <?php
                                    if (!$_GET['action']) {
                                        ?>
                                        <div class="pageHeading"><br /><?php echo HEADING_CONTENT; ?><br /></div>
                                        <div class="main"><?php echo CONTENT_NOTE; ?></div>
                                        <?php
                                        xtc_spaceUsed(DIR_FS_CATALOG . 'media/content/');
                                        echo '<div class="main">' . USED_SPACE . xtc_format_filesize($total) . '</div>';
                                        ?>
                                        <?php
// Display Content
                                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                            $content = array();
                                            $content_query = xtc_db_query("SELECT * FROM " . TABLE_CONTENT_MANAGER . " WHERE languages_id='" . $languages[$i]['id'] . "' AND parent_id = '0' ORDER BY content_group,sort_order ");

                                            while ($content_data = xtc_db_fetch_array($content_query)) {
                                                $content_sub_query = xtc_db_query("SELECT * FROM " . TABLE_CONTENT_MANAGER . " WHERE languages_id='" . $languages[$i]['id'] . "' AND parent_id = '" . $content_data['content_group'] . "' ORDER BY content_group,sort_order ");
                                                $content_sub = array();
                                                if (xtc_db_num_rows($content_sub_query)) {
                                                    while ($sub_data = xtc_db_fetch_array($content_sub_query)) {
                                                        $content_sub[] = array('CONTENT_ID' => $sub_data['content_id'],
                                                            'PARENT_ID' => $sub_data['parent_id'],
                                                            'GROUP_IDS' => $sub_data['group_ids'],
                                                            'LANGUAGES_ID' => $sub_data['languages_id'],
                                                            'CONTENT_TITLE' => $sub_data['content_title'],
                                                            'CONTENT_URL_ALIAS' => $sub_data['content_url_alias'],
                                                            'CONTENT_OUT_LINK' => $sub_data['content_out_link'],
                                                            'CONTENT_LINK_TARGET' => $sub_data['content_link_target'],
                                                            'CONTENT_LINK_TYPE' => $sub_data['content_link_type'],
                                                            'CONTENT_HEADING' => $sub_data['content_heading'],
                                                            'CONTENT_TEXT' => $sub_data['content_text'],
                                                            'SORT_ORDER' => $sub_data['sort_order'],
                                                            'FILE_FLAG' => $sub_data['file_flag'],
                                                            'SLIDER_SET' => $sub_data['slider_set'],
                                                            'CONTENT_FILE' => $sub_data['content_file'],
                                                            'CONTENT_DELETE' => $sub_data['content_delete'],
                                                            'CONTENT_GROUP' => $sub_data['content_group'],
                                                            'CONTENT_STATUS' => $sub_data['content_status'],
                                                            'CONTENT_META_TITLE' => $sub_data['content_meta_title'],
                                                            'CONTENT_META_DESCRIPTION' => $sub_data['content_meta_description'],
                                                            'CONTENT_META_KEYWORDS' => $sub_data['content_meta_keywords']);
                                                    }
                                                }
                                                $content[] = array('CONTENT_ID' => $content_data['content_id'],
                                                    'PARENT_ID' => $content_data['parent_id'],
                                                    'CONTENT_CHILD' => $content_sub,
                                                    'GROUP_IDS' => $content_data['group_ids'],
                                                    'LANGUAGES_ID' => $content_data['languages_id'],
                                                    'CONTENT_TITLE' => $content_data['content_title'],
                                                    'CONTENT_URL_ALIAS' => $content_data['content_url_alias'],
                                                    'CONTENT_OUT_LINK' => $content_data['content_out_link'],
                                                    'CONTENT_LINK_TARGET' => $content_data['content_link_target'],
                                                    'CONTENT_LINK_TYPE' => $content_data['content_link_type'],
                                                    'CONTENT_HEADING' => $content_data['content_heading'],
                                                    'CONTENT_TEXT' => $content_data['content_text'],
                                                    'SORT_ORDER' => $content_data['sort_order'],
                                                    'FILE_FLAG' => $content_data['file_flag'],
                                                    'SLIDER_SET' => $content_data['slider_set'],
                                                    'CONTENT_FILE' => $content_data['content_file'],
                                                    'CONTENT_DELETE' => $content_data['content_delete'],
                                                    'CONTENT_GROUP' => $content_data['content_group'],
                                                    'CONTENT_STATUS' => $content_data['content_status'],
                                                    'CONTENT_META_TITLE' => $content_data['content_meta_title'],
                                                    'CONTENT_META_DESCRIPTION' => $content_data['content_meta_description'],
                                                    'CONTENT_META_KEYWORDS' => $content_data['content_meta_keywords']
                                                );
                                            }
                                            ?>
                                            <div class="menu_active" style="padding:3px 0 3px 9px;font-weight:700">
        <?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) . '&nbsp;&nbsp;' . $languages[$i]['name']; ?>
                                            </div>
                                            <table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                                <tr class="dataTableHeadingRow">
                                                    <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_CONTENT_ID; ?></th>
                                                    <th class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_CONTENT_TITLE; ?></th>
                                                    <th class="dataTableHeadingContent" align="middle"><?php echo TABLE_HEADING_CONTENT_GROUP; ?></th>
                                                    <th class="dataTableHeadingContent" align="middle"><?php echo TABLE_HEADING_CONTENT_SORT; ?></th>
                                                    <th class="dataTableHeadingContent" width="2%"align="left"><?php echo TABLE_HEADING_CONTENT_FILE; ?></th>
                                                    <th class="dataTableHeadingContent" nowrap="nowrap" align="left"><?php echo TABLE_HEADING_CONTENT_STATUS; ?></th>
                                                    <th class="dataTableHeadingContent" nowrap="nowrap" align="middle"><?php echo TABLE_HEADING_CONTENT_BOX; ?></th>
                                                    <th class="dataTableHeadingContent" nowrap="nowrap" align="middle">Link ?</th>
                                                    <th class="dataTableHeadingContent last" align="middle"><?php echo TABLE_HEADING_CONTENT_ACTION; ?>&nbsp;</th>
                                                </tr>
                                                <?php
                                                for ($ii = 0, $nn = sizeof($content); $ii < $nn; $ii++) {
                                                    $file_flag_sql = xtc_db_query("SELECT file_flag_name FROM " . TABLE_CM_FILE_FLAGS . " WHERE file_flag=" . $content[$ii]['FILE_FLAG']);
                                                    $file_flag_result = xtc_db_fetch_array($file_flag_sql);
                                                    if ($ii % 2 == 0) {
                                                        $f = 'dataTableRow';
                                                    } else {
                                                        $f = '';
                                                    }
                                                    ?>
                                                    <tr class="<?php echo $f; ?>">
                                                        <td align="left">
                                                            <?php echo $content[$ii]['CONTENT_ID']; ?>
                                                        </td>
                                                        <td align="left" nowrap="nowrap">
                                                            <?php
                                                            if ($content[$ii]['CONTENT_GROUP'] == '0') {
                                                                echo '<img align="left" src="images/delete.gif" alt="" /> <span style="color:#ff0000;margin-left:5px"><strong>';
                                                                echo $content[$ii]['CONTENT_TITLE'];
                                                                echo '</strong><br /><br /> Sie haben keine Sprachgruppe definiert, daher wurde keine SEO URL erzeugt!</span>';
                                                            } else {
                                                                echo $content[$ii]['CONTENT_TITLE'];
                                                            }
                                                            if ($content[$ii]['CONTENT_DELETE'] == '0') {
                                                                echo '<font color="ff0000">*</font>';
                                                            }
                                                            if ($content[$ii]['CONTENT_URL_ALIAS'] != '')
                                                                echo '<br /><span style="font-size:85%;color:#666"><em>URL Alias: ' . $content[$ii]['CONTENT_URL_ALIAS'] . '</em><span>';
                                                            ?>
                                                        </td>
                                                        <td align="center">
                                                            <?php echo $content[$ii]['CONTENT_GROUP']; ?>
                                                        </td>
                                                        <td align="center">
                                                            <?php echo $content[$ii]['SORT_ORDER']; ?>
                                                        </td>
                                                        <td align="center">
                                                            <?php
                                                            if ($content[$ii]['CONTENT_FILE'] == '')
                                                                echo $content[$ii]['CONTENT_FILE'] = '-';
                                                            else
                                                                echo $content[$ii]['CONTENT_FILE'];
                                                            ?>
                                                        </td>
                                                        <td align="center">
                                                            <?php
                                                            if ($content[$ii]['CONTENT_STATUS'] == 0) {
                                                                echo '<img src="images/cross.gif" alt="' . TEXT_NO . '" />';
                                                            } else {
                                                                echo '<img src="images/icons/success.gif" alt="' . TEXT_YES . '" />';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td align="center">
                                                            <?php echo $file_flag_result['file_flag_name']; ?>
                                                        </td>
                                                        <td align="center">
                                                                <?php
                                                                if (!empty($content[$ii]['CONTENT_OUT_LINK'])) {
                                                                    echo '<img src="images/icons/success.gif" alt="' . TEXT_YES . '" />';
                                                                } else {
                                                                    echo '-';
                                                                }
                                                                ?>
                                                        </td>
                                                        <td align="right" class="last" nowrap="nowrap">
                                                                <?php if ($content[$ii]['CONTENT_DELETE'] == '1') { ?>
                                                                <a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER, 'special=delete&coID=' . $content[$ii]['CONTENT_ID']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
                                                                <?php echo xtc_image(DIR_WS_ICONS . 'delete.gif', 'Delete', '', '', 'style="cursor:pointer" onClick="return confirm(\'' . DELETE_ENTRY . '\')"') . '  ' . TEXT_DELETE ?>
                                                                </a>&nbsp;&nbsp;
                                                                <?php } ?>

                                                            <a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=edit&coID=' . $content[$ii]['CONTENT_ID']); ?>">
                                                            <?php echo xtc_image(DIR_WS_ICONS . 'icon_edit.gif', 'Edit', '', '', 'style="cursor:pointer"') . '  ' . TEXT_EDIT ?>
                                                            </a>
                                                    <?php if (!empty($content[$ii]['CONTENT_OUT_LINK'])) { ?>
                                                                <a style="cursor:pointer" target="_blank" href="<?php echo $content[$ii]['CONTENT_OUT_LINK']; ?>">
                                                        <?php echo xtc_image(DIR_WS_ICONS . 'preview.gif', 'Preview', '', '', 'style="cursor:pointer"') . '&nbsp;&nbsp;' . TEXT_PREVIEW ?>
                                                                </a>
            <?php } else { ?>
                                                                <a style="cursor:pointer" onclick="javascript:window.open('<?php echo xtc_href_link(FILENAME_CONTENT_PREVIEW, 'coID=' . $content[$ii]['CONTENT_ID']); ?>', 'popup', 'toolbar=0, width=640, height=600')">
                                                                <?php echo xtc_image(DIR_WS_ICONS . 'preview.gif', 'Preview', '', '', 'style="cursor:pointer"') . '&nbsp;&nbsp;' . TEXT_PREVIEW ?>
                                                                </a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                            <?php
                                                            if (is_array($content[$ii]['CONTENT_CHILD']) && !empty($content[$ii]['CONTENT_CHILD'])) {
                                                                for ($iii = 0, $nnn = sizeof($content[$ii]['CONTENT_CHILD']); $iii < $nnn; $iii++) {
                                                                    ?>
                                                            <tr class="<?php echo $f ?>" onmouseover="this.className = 'dataTableRowOver'" onmouseout="this.className = '<?php echo $f ?>'">
                                                                <td align="left">
                                                                    <?php echo $content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_ID']; ?>
                                                                </td>
                                                                <td align="left">
                                                                    <?php
                                                                    if ($content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_GROUP'] == '0') {
                                                                        echo '&nbsp;&nbsp;<img align="left" src="images/delete.gif" alt="" /> <span style="color:#ff0000;margin-left:5px"><strong><sup>|_</sup> ';
                                                                        echo $content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_TITLE'];
                                                                        echo '</strong><br /><br /> Sie haben keine Sprachgruppe definiert, daher wurde keine SEO URL erzeugt!</span>';
                                                                    } else {
                                                                        echo '&nbsp;&nbsp;<sup>|_</sup> ' . $content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_TITLE'];
                                                                    }
                                                                    if ($content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_DELETE'] == '0') {
                                                                        echo '<font color="ff0000">*</font>';
                                                                    }
                                                                    if ($content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_URL_ALIAS'] != '')
                                                                        echo '<br />&nbsp;&nbsp;<span style="font-size:85%;color:#666"><em>URL Alias: ' . $content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_URL_ALIAS'] . '</em><span>';
                                                                    ?>
                                                                </td>
                                                                <td align="center">
                                                                    <?php echo $content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_GROUP']; ?>
                                                                </td>
                                                                <td align="center">
                                                                    <?php echo $content[$ii]['CONTENT_CHILD'][$iii]['SORT_ORDER']; ?>
                                                                </td>
                                                                <td align="center">
                                                                    <?php
                                                                    if ($content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_FILE'] == '')
                                                                        echo $content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_FILE'] = '-';
                                                                    else
                                                                        echo $content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_FILE'];
                                                                    ?>
                                                                </td>
                                                                <td align="center">
                                                                    <?php
                                                                    if ($content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_STATUS'] == 0) {
                                                                        echo '<img src="images/cross.gif" alt="' . TEXT_NO . '" />';
                                                                    } else {
                                                                        echo '<img src="images/icons/success.gif" alt="' . TEXT_YES . '" />';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td align="center">
                    <?php echo $file_flag_result['file_flag_name']; ?>
                                                                </td>
                                                                <td align="center">
                                                                    <?php
                                                                    if (!empty($content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_OUT_LINK'])) {
                                                                        echo '<img src="images/icons/success.gif" alt="' . TEXT_YES . '" />';
                                                                    } else {
                                                                        echo '-';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td align="right" class="last">
                                                                    <?php if ($content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_DELETE'] == '1') { ?>
                                                                        <a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER, 'special=delete&coID=' . $content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_ID']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
                                                                <?php echo xtc_image(DIR_WS_ICONS . 'delete.gif', 'Delete', '', '', 'style="cursor:pointer" onClick="return confirm(\'' . DELETE_ENTRY . '\')"') . '  ' . TEXT_DELETE ?>
                                                                        </a>&nbsp;&nbsp;
                    <?php } ?>

                                                                    <a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=edit&coID=' . $content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_ID']); ?>">
                                                            <?php echo xtc_image(DIR_WS_ICONS . 'icon_edit.gif', 'Edit', '', '', 'style="cursor:pointer"') . '  ' . TEXT_EDIT ?>
                                                                    </a>
                                                            <?php if (!empty($content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_OUT_LINK'])) { ?>
                                                                        <a style="cursor:pointer" target="_blank" href="<?php echo $content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_OUT_LINK']; ?>">
                                                                <?php echo xtc_image(DIR_WS_ICONS . 'preview.gif', 'Preview', '', '', 'style="cursor:pointer"') . '&nbsp;&nbsp;' . TEXT_PREVIEW ?>
                                                                        </a>
                                                            <?php } else { ?>
                                                                        <a style="cursor:pointer" onclick="javascript:window.open('<?php echo xtc_href_link(FILENAME_CONTENT_PREVIEW, 'coID=' . $content[$ii]['CONTENT_CHILD'][$iii]['CONTENT_ID']); ?>', 'popup', 'toolbar=0, width=640, height=600')">
                                                                <?php echo xtc_image(DIR_WS_ICONS . 'preview.gif', 'Preview', '', '', 'style="cursor:pointer"') . '&nbsp;&nbsp;' . TEXT_PREVIEW ?>
                                                                        </a>
                                                            <?php } ?>
                                                                </td>
                                                            </tr>
                                                        <?php }
                                                    } // ENDE Content Child ?>


                                                    <?php
                                                    $content_1 = array();
                                                    $content_1_query = xtc_db_query("SELECT * FROM " . TABLE_CONTENT_MANAGER . " WHERE languages_id='" . $i . "' AND parent_id='" . $content[$ii]['CONTENT_ID'] . "' ORDER BY sort_order");
                                                    while ($content_1_data = xtc_db_fetch_array($content_1_query)) {
                                                        $content_1[] = array(
                                                            'CONTENT_ID' => $content_1_data['content_id'],
                                                            'PARENT_ID' => $content_1_data['parent_id'],
                                                            'GROUP_IDS' => $content_1_data['group_ids'],
                                                            'LANGUAGES_ID' => $content_1_data['languages_id'],
                                                            'CONTENT_TITLE' => $content_1_data['content_title'],
                                                            'CONTENT_URL_ALIAS' => $content_1_data['content_url_alias'],
                                                            'CONTENT_OUT_LINK' => $content_1_data['content_out_link'],
                                                            'CONTENT_HEADING' => $content_1_data['content_heading'],
                                                            'CONTENT_TEXT' => $content_1_data['content_text'],
                                                            'SORT_ORDER' => $content_1_data['sort_order'],
                                                            'FILE_FLAG' => $content_1_data['file_flag'],
                                                            'CONTENT_FILE' => $content_1_data['content_file'],
                                                            'CONTENT_DELETE' => $content_1_data['content_delete'],
                                                            'CONTENT_STATUS' => $content_1_data['content_status'],
                                                            'CONTENT_META_TITLE' => $content_1_data['content_meta_title'],
                                                            'CONTENT_META_DESCRIPTION' => $content_1_data['content_meta_description'],
                                                            'CONTENT_META_KEYWORDS' => $content_1_data['content_meta_keywords']
                                                        );
                                                    }
                                                    for ($a = 0, $x = sizeof($content_1); $a < $x; $a++) {
                                                        if ($content_1[$a] != '') {
                                                            $file_flag_sql = xtc_db_query("SELECT file_flag_name FROM " . TABLE_CM_FILE_FLAGS . " WHERE file_flag=" . $content_1[$a]['FILE_FLAG']);
                                                            $file_flag_result = xtc_db_fetch_array($file_flag_sql);
                                                            echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\'" onmouseout="this.className=\'dataTableRow\'">' . "\n";

                                                            if ($content_1[$a]['CONTENT_FILE'] == '')
                                                                $content_1[$a]['CONTENT_FILE'] = 'database';
                                                            ?>
                                                            <td align="left"><?php echo $content_1[$a]['CONTENT_ID']; ?></td>
                                                            <td align="left">--<?php echo $content_1[$a]['CONTENT_TITLE']; ?></td>
                                                            <td align="left"><?php echo $content_1[$a]['CONTENT_FILE']; ?></td>
                                                            <td align="middle"><?php if ($content_1[$a]['CONTENT_STATUS'] == 0) {
                                                                echo TEXT_NO;
                                                            } else {
                                                                echo TEXT_YES;
                                                            } ?></td>
                                                            <td align="middle"><?php echo $file_flag_result['file_flag_name']; ?></td>
                                                            <td align="right">
                                                                            <?php
                                                                            if ($content_1[$a]['CONTENT_DELETE'] == '1') {
                                                                                ?>
                                                                    <a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER, 'special=delete&coID=' . $content_1[$a]['CONTENT_ID']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
                                                                                <?php
                                                                                echo xtc_image(DIR_WS_ICONS . 'delete.gif', 'Delete', '', '', 'style="cursor:pointer" onClick="return confirm(\'' . DELETE_ENTRY . '\')"') . '  ' . TEXT_DELETE . '</a>&nbsp;&nbsp;';
                                                                            }
                                                                            ?>
                                                                    <a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=edit&coID=' . $content_1[$a]['CONTENT_ID']); ?>">
                                                                            <?php echo xtc_image(DIR_WS_ICONS . 'icon_edit.gif', 'Edit', '', '', 'style="cursor:pointer"') . '  ' . TEXT_EDIT . '</a>'; ?>
                                                                        <a style="cursor:pointer" onClick="javascript:window.open('<?php echo xtc_href_link(FILENAME_CONTENT_PREVIEW, 'coID=' . $content_1[$a]['CONTENT_ID']); ?>', 'popup', 'toolbar=0, width=640, height=600')">
                                                                            <?php echo xtc_image(DIR_WS_ICONS . 'preview.gif', 'Preview', '', '', 'style="cursor:pointer"') . '&nbsp;&nbsp;' . TEXT_PREVIEW . '</a>'; ?>
                                                                            </td>
                                                                            </tr>


                                                                            <?php
                                                                        }
                                                                    } // for content
                                                                } // for language
                                                                ?>
                                                                </table>


                                                                <?php
                                                            }
                                                        } else {

                                                            switch ($_GET['action']) {
// Diplay Editmask
                                                                case 'new':
                                                                case 'edit':
                                                                    if ($_GET['action'] != 'new') {
                                                                        $content_query = xtc_db_query("SELECT * FROM " . TABLE_CONTENT_MANAGER . " WHERE content_id='" . (int) $_GET['coID'] . "'");
                                                                        $content = xtc_db_fetch_array($content_query);
                                                                    }
                                                                    $languages_array = array();
                                                                    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {

                                                                        if ($languages[$i]['id'] == $content['languages_id']) {
                                                                            $languages_selected = $languages[$i]['code'];
                                                                            $languages_id = $languages[$i]['id'];
                                                                        }
                                                                        $languages_array[] = array('id' => $languages[$i]['code'], 'text' => $languages[$i]['name']);
                                                                    }
                                                                    if ($languages_id != '')
                                                                        $query_string = 'languages_id=' . $languages_id . ' AND';
                                                                    $categories_query = xtc_db_query("SELECT
                                        content_id,
                                        content_title
                                        FROM " . TABLE_CONTENT_MANAGER . "
                                        WHERE " . $query_string . " parent_id='0'
                                        AND content_id!='" . (int) $_GET['coID'] . "'");
                                                                    while ($categories_data = xtc_db_fetch_array($categories_query)) {
                                                                        $categories_array[] = array('id' => $categories_data['content_id'], 'text' => $categories_data['content_title']);
                                                                    }
                                                                    if ($_GET['action'] != 'new') {
                                                                        echo xtc_draw_form('edit_content', FILENAME_CONTENT_MANAGER, 'action=edit&id=update&coID=' . $_GET['coID'], 'post', 'enctype="multipart/form-data"') . xtc_draw_hidden_field('coID', $_GET['coID']);
                                                                    } else {
                                                                        echo xtc_draw_form('edit_content', FILENAME_CONTENT_MANAGER, 'action=edit&id=insert', 'post', 'enctype="multipart/form-data"') . xtc_draw_hidden_field('coID', $_GET['coID']);
                                                                    }
                                                                    ?>
                                                                    <table class="main" width="100%" border="0">
                                                                        <tr>
                                                                            <td colspan="2" align="right" class="main"><?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>'; ?><a class="button" onClick="this.blur();" href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER); ?>"><?php echo BUTTON_BACK; ?></a></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_LANGUAGE; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_pull_down_menu('language', $languages_array, $languages_selected); ?></td>
                                                                        </tr>
            <?php
            if ($content['content_delete'] != 0 || $_GET['action'] == 'new') {
                ?>
                                                                            <tr>
                                                                                <td width="10%"><?php echo TEXT_GROUP; ?></td>
                                                                                <td width="90%">
                                                                            <?php
                                                                            if (empty($content['content_group'])) {
                                                                                $next_id = xtc_db_fetch_array(xtc_db_query("SELECT content_group FROM ".TABLE_CONTENT_MANAGER." ORDER BY content_group DESC LIMIT 1"));
                                                                                $id = $next_id['content_group'] + 1;
                                                                            } else {
                                                                                $id = $content['content_group'];
                                                                            }
                                                                            echo xtc_draw_input_field('content_group', $id, 'size="5"');
                                                                            echo TEXT_GROUP_DESC;
                                                                            ?>
                                                                                </td>
                                                                            </tr>
                                                                            <?php
                                                                        } else {
                                                                            echo xtc_draw_hidden_field('content_group', $content['content_group']);
                                                                            ?>
                                                                            <tr>
                                                                                <td width="10%"><?php echo TEXT_GROUP; ?></td>
                                                                                <td width="90%"><?php echo $content['content_group']; ?></td>
                                                                            </tr>
                <?php
            }
            $file_flag_sql = xtc_db_query("SELECT file_flag AS id, file_flag_name AS text FROM " . TABLE_CM_FILE_FLAGS);
            while ($file_flag = xtc_db_fetch_array($file_flag_sql)) {
                $file_flag_array[] = array('id' => $file_flag['id'], 'text' => $file_flag['text']);
            }
            $slider_set_sql = xtc_db_query("SELECT slider_id AS id, slider_title AS text FROM cseo_slider_gallery GROUP BY slider_title");
            $slider_set_array = array(array('id' => 0, 'text' => TEXT_SELECT));
			while ($slider_set = xtc_db_fetch_array($slider_set_sql)) {
                $slider_set_array[] = array('id' => $slider_set['id'], 'text' => $slider_set['text']);
            }
            $link_target[] = array('id' => '_blank', 'text' => '_blank');
            $link_target[] = array('id' => '_self', 'text' => '_self');

            $link_type[] = array('id' => 'nofollow', 'text' => 'nofollow');
            $link_type[] = array('id' => 'follow', 'text' => 'follow');

            $content_dropdown_query = xtc_db_query("SELECT content_title, content_group FROM " . TABLE_CONTENT_MANAGER . " WHERE languages_id='" . (int) $_SESSION['languages_id'] . "' AND parent_id = '0' AND content_group != '5';");
            $c_dropdown[] = array('id' => '0', 'text' => TEXT_SELECT);
            while ($content_dropdown = xtc_db_fetch_array($content_dropdown_query)) {
                $c_dropdown[] = array('id' => $content_dropdown['content_group'], 'text' => $content_dropdown['content_title']);
            }
            ?>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_FILE_FLAG; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_pull_down_menu('file_flag', $file_flag_array, $content['file_flag']); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_SLIEDER_SET; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_pull_down_menu('slider_set', $slider_set_array, $content['slider_set']); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_SORT_ORDER; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_input_field('sort_order', $content['sort_order'], 'size="5"'); ?></td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td valign="top" width="10%"><?php echo TEXT_STATUS; ?></td>
                                                                            <td width="90%"><?php
                                                                            if ($content['content_status'] == '1') {
                                                                                echo xtc_draw_checkbox_field('status', 'yes', true) . ' ' . TEXT_STATUS_DESCRIPTION;
                                                                            } else {
                                                                                echo xtc_draw_checkbox_field('status', 'yes', false) . ' ' . TEXT_STATUS_DESCRIPTION;
                                                                            }
                                                                            ?><br /><br /></td>
                                                                        </tr>
            <?php
            if (GROUP_CHECK == 'true') {
                $customers_statuses_array = xtc_get_customers_statuses();
                $customers_statuses_array = array_merge(array(array('id' => 'all', 'text' => TXT_ALL)), $customers_statuses_array);
                ?>
                                                                            <tr>
                                                                            <tr>
                                                                                <td colspan="2" style="border-bottom: 1px solid #ccc">&nbsp;</td>
                                                                            </tr>
                                                                            <td style="border-top: 1px solid; border-color: #ff0000;" valign="top" class="main" ><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
                                                                            <td style="border-top: 1px solid; border-left: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-right: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-bottom: 1px solid; border-color: #ff0000;" bgcolor="#FFCC33" class="main">
                <?php
                for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
                    if (strstr($content['group_ids'], 'c_' . $customers_statuses_array[$i]['id'] . '_group')) {
                        $checked = 'checked ';
                    } else {
                        $checked = '';
                    }
                    echo '<input type="checkbox" name="groups[]" value="' . $customers_statuses_array[$i]['id'] . '"' . $checked . ' /> ' . $customers_statuses_array[$i]['text'] . '<br />';
                }
                ?>
                                                                            </td>
                                                                            </tr>
                <?php
            }
            ?>
                                                                        <tr>
                                                                            <td colspan="2" style="border-bottom: 1px solid #ccc">&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2">&nbsp;</td>
                                                                        </tr>
																		<?php if ($content['content_group'] != '5') { ?>
                                                                        <tr>
                                                                            <td colspan="2"><?php echo TEXT_CONTENT_PARENT_CONTENT_DROP; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_CONTENT_PARENT_CONTENT; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_pull_down_menu('parent', $c_dropdown, $content['parent_id']); ?></td>
                                                                        </tr>
																		<?php } ?>
                                                                        <tr>
                                                                            <td colspan="2" style="border-bottom: 1px solid #ccc">&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2">&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_CONTENT_TITLE; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_input_field('cont_title', $content['content_title'], 'size="60"'); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_HEADING; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_input_field('cont_heading', $content['content_heading'], 'size="60"'); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_URL_ALIAS; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_input_field('cont_url_alias', $content['content_url_alias'], 'size="60"'); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_META_TITLE; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_input_field('cont_meta_title', $content['content_meta_title'], 'size="60"'); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_META_DESC; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_input_field('cont_meta_description', $content['content_meta_description'], 'size="60"'); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_META_KEY; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_input_field('cont_meta_keywords', $content['content_meta_keywords'], 'size="60"'); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2" style="border-bottom: 1px solid #ccc">&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2">&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_LINK; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_input_field('content_out_link', $content['content_out_link'], 'size="60"'); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_ZIEL; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_pull_down_menu('content_link_target', $link_target, $content['content_out_link']); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_TYP; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_pull_down_menu('content_link_type', $link_type, $content['content_link_type']); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2" style="border-bottom: 1px solid #ccc">&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2">&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%" valign="top"><?php echo TEXT_UPLOAD_FILE; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_file_field('file_upload') . ' ' . TEXT_UPLOAD_FILE_LOCAL; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%" valign="top"><?php echo TEXT_CHOOSE_FILE; ?></td>
                                                                            <td width="90%">
                                                                                <?php
                                                                                if ($dir = opendir(DIR_FS_CATALOG . 'media/content/')) {
                                                                                    while (($file = readdir($dir)) !== false) {
                                                                                        if (is_file(DIR_FS_CATALOG . 'media/content/' . $file) and ($file != "index.html")) {
                                                                                            $files[] = array(
                                                                                                'id' => $file,
                                                                                                'text' => $file);
                                                                                        }//if
                                                                                    } // while
                                                                                    closedir($dir);
                                                                                }
                                                                                // set default value in dropdown!
                                                                                if ($content['content_file'] == '') {
                                                                                    $default_array[] = array('id' => 'default', 'text' => TEXT_SELECT);
                                                                                    $default_value = 'default';
                                                                                    if (count($files) == 0) {
                                                                                        $files = $default_array;
                                                                                    } else {
                                                                                        $files = array_merge($default_array, $files);
                                                                                    }
                                                                                } else {
                                                                                    $default_array[] = array('id' => 'default', 'text' => TEXT_NO_FILE);
                                                                                    $default_value = $content['content_file'];
                                                                                    if (count($files) == 0) {
                                                                                        $files = $default_array;
                                                                                    } else {
                                                                                        $files = array_merge($default_array, $files);
                                                                                    }
                                                                                }
                                                                                echo '<br />' . TEXT_CHOOSE_FILE_SERVER . '<br /><br />';
                                                                                echo xtc_draw_pull_down_menu('select_file', $files, $default_value);
                                                                                if ($content['content_file'] != '') {
                                                                                    echo TEXT_CURRENT_FILE . ' <b>' . $content['content_file'] . '</b><br />';
                                                                                }
                                                                                ?>
                                                                            </td>
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td colspan="2" style="border-bottom: 1px solid #ccc">&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2">&nbsp;</td>
                                                                        </tr>
                                                                        <tr> 
                                                                            <td width="10%">
                                                                                            <?php echo TEXT_TEMPLATE_COLUMN; ?> 
                                                                            </td>
                                                                            <td width="90%">
                                                                                <table>
                                                                                    <tr>
                                                                                        <td width="1" align="left" valign="top">
                                                                                            <?php echo xtc_image('images/template_content.gif', 'Content Seite'); ?>
                                                                                        </td>
                                                                                        <td align="left">
            <?php
            if (isset($_GET['coID'])) {
                echo xtc_draw_selection_field('content_col_top', 'checkbox', '1', $content['content_col_top'] == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_TOP . '<br />';
                echo xtc_draw_selection_field('content_col_left', 'checkbox', '1', $content['content_col_left'] == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_LEFT . '<br />';
                echo xtc_draw_selection_field('content_col_right', 'checkbox', '1', $content['content_col_right'] == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_RIGHT . '<br />';
                echo xtc_draw_selection_field('content_col_bottom', 'checkbox', '1', $content['content_col_bottom'] == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_BUTTON . '<br />';
            } else {
                echo xtc_draw_selection_field('content_col_top', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_TOP . '<br />';
                echo xtc_draw_selection_field('content_col_left', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_LEFT . '<br />';
                echo xtc_draw_selection_field('content_col_right', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_RIGHT . '<br />';
                echo xtc_draw_selection_field('content_col_bottom', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_BUTTON . '<br />';
            }
            ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr> 

                                                                        <tr>
                                                                            <td colspan="2" style="border-bottom: 1px solid #ccc">&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2">&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%" valign="top"></td>
                                                                            <td colspan="90%" valign="top"><br /><?php echo TEXT_FILE_DESCRIPTION; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%" valign="top"><?php echo TEXT_CONTENT; ?></td>
            <?php
            if (USE_WYSIWYG == 'false' && USE_CODEMIRROR == 'true') {
                ?>
                                                                                <td width="90%">
                                                                                    <div style="float:left; width: 50%">
                                                                                        <b><?php echo TEXT_EDIT; ?>:</b><br />
                <?php echo xtc_draw_textarea_field('cont', '', '100%', '35', $content['content_text']); ?>
                                                                                    </div>
                                                                                    <div style="float:left; width: 50%">
                                                                                        <b><?php echo TEXT_PREVIEW; ?>:</b><br />
                                                                                        <iframe id=preview></iframe>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <script>
                                                                                                                var delay;
                                                                                                                var editor = CodeMirror.fromTextArea(document.getElementById('cont'), {
                                                                                                                    mode: 'text/html',
                                                                                                                    tabMode: 'indent',
                                                                                                                    lineNumbers: true,
                                                                                                                });
                                                                                                                editor.on("change", function() {
                                                                                                                    clearTimeout(delay);
                                                                                                                    delay = setTimeout(updatePreview, 300);
                                                                                                                });

                                                                                                                function updatePreview() {
                                                                                                                    var previewFrame = document.getElementById('preview');
                                                                                                                    var preview = previewFrame.contentDocument || previewFrame.contentWindow.document;
                                                                                                                    preview.open();
                                                                                                                    preview.write(editor.getValue());
                                                                                                                    preview.close();
                                                                                                                }
                                                                                                                setTimeout(updatePreview, 300);
                                                                            </script>

                                                                            <?php
                                                                        } elseif (USE_WYSIWYG == 'true') {
                                                                            ?>
                                                                            <script src="includes/editor/ckeditor/ckeditor.js" type="text/javascript"></script>
                                                                            <?php
                                                                            if (file_exists('includes/editor/ckfinder/ckfinder.js')) {
                                                                                echo '<script src="includes/editor/ckfinder/ckfinder.js" type="text/javascript"></script>';
                                                                            }
                                                                            ?>
                                                                            <td width="90%">
                                                                            <?php echo xtc_draw_textarea_field('cont', 'soft', '100', '35', $content['content_text'], 'class="ckeditor" name="editor1"'); ?>
                                                                            </td>
                                                                            </tr>
																			<?php
																			if (file_exists('includes/editor/ckfinder/ckfinder.js')) {
																			?>	
                                                                                <script type="text/javascript">
                                                                                    var newCKEdit = CKEDITOR.replace('<?php echo 'cont' ?>');
                                                                                    CKFinder.setupCKEditor(newCKEdit, 'includes/editor/ckfinder/');
                                                                                </script>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                        <?php
                                                                    } else {
                                                                    ?>   

                                                                            <td width="90%">
                                                                            <?php echo xtc_draw_textarea_field('cont', 'soft', '100', '35', $content['content_text']); ?>
                                                                            </td>
                                                                            </tr>
																	<?php } ?>
                                                                        <tr>
                                                                            <td colspan="2" align="right" class="main"><?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>'; ?><a class="button" onClick="this.blur();" href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER); ?>"><?php echo BUTTON_BACK; ?></a></td>
                                                                        </tr>
                                                                    </table>
                                                                    </form>
                                                                    <?php
                                                                    break;

                                                                case 'edit_products_content':
                                                                case 'new_products_content':

                                                                    if ($_GET['action'] == 'edit_products_content') {
                                                                        $content_query = xtc_db_query("SELECT
									*
									FROM " . TABLE_PRODUCTS_CONTENT . "
									WHERE content_id='" . (int) $_GET['coID'] . "'");

                                                                        $content = xtc_db_fetch_array($content_query);
                                                                    }

                                                                    // get products names.
                                                                    $products_query = xtc_db_query("SELECT
							products_id,
							products_name
							FROM " . TABLE_PRODUCTS_DESCRIPTION . "
							WHERE language_id='" . (int) $_SESSION['languages_id'] . "'");
                                                                    $products_array = array();

                                                                    while ($products_data = xtc_db_fetch_array($products_query)) {
                                                                        $products_array[] = array(
                                                                            'id' => $products_data['products_id'],
                                                                            'text' => $products_data['products_name']);
                                                                    }

                                                                    // get languages
                                                                    $languages_array = array();

                                                                    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                                        if ($languages[$i]['id'] == $content['languages_id']) {
                                                                            $languages_selected = $languages[$i]['code'];
                                                                            $languages_id = $languages[$i]['id'];
                                                                        }
                                                                        $languages_array[] = array('id' => $languages[$i]['code'], 'text' => $languages[$i]['name']);
                                                                    }

                                                                    // get used content files
                                                                    $content_files_query = xtc_db_query("SELECT DISTINCT
                                content_name,
                                content_file
                                FROM " . TABLE_PRODUCTS_CONTENT . "
                                WHERE content_file != ''");
                                                                    $content_files = array();

                                                                    while ($content_files_data = xtc_db_fetch_array($content_files_query)) {
                                                                        $content_files[] = array('id' => $content_files_data['content_file'], 'text' => $content_files_data['content_name']);
                                                                    }

// add default value to array
                                                                    $default_array[] = array('id' => 'default', 'text' => TEXT_SELECT);
                                                                    $default_value = 'default';
                                                                    $content_files = array_merge($default_array, $content_files);
// mask for product content

                                                                    if ($_GET['action'] != 'new_products_content') {
                                                                        echo xtc_draw_form('edit_content', FILENAME_CONTENT_MANAGER, 'action=edit_products_content&id=update_product&coID=' . $_GET['coID'], 'post', 'enctype="multipart/form-data"') . xtc_draw_hidden_field('coID', $_GET['coID']);
                                                                    } else {
                                                                        echo xtc_draw_form('edit_content', FILENAME_CONTENT_MANAGER, 'action=edit_products_content&id=insert_product', 'post', 'enctype="multipart/form-data"');
                                                                    }
                                                                    ?>
                                                                    <div class="main"><?php echo TEXT_CONTENT_DESCRIPTION; ?></div>
                                                                    <table class="main" width="100%" border="0">
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_PRODUCT; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_pull_down_menu('product', $products_array, $content['products_id']); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_LANGUAGE; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_pull_down_menu('language', $languages_array, $languages_selected); ?></td>
                                                                        </tr>

                                                                                <?php
                                                                                if (GROUP_CHECK == 'true') {
                                                                                    $customers_statuses_array = xtc_get_customers_statuses();
                                                                                    $customers_statuses_array = array_merge(array(array('id' => 'all', 'text' => TXT_ALL)), $customers_statuses_array);
                                                                                    ?>
                                                                            <tr>
                                                                                <td style="border-top: 1px solid; border-color: #ff0000;" valign="top" class="main" ><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
                                                                                <td style="border-top: 1px solid; border-left: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-right: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-bottom: 1px solid; border-color: #ff0000;" bgcolor="#FFCC33" class="main">
                                                                            <?php
                                                                            for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
                                                                                if (strstr($content['group_ids'], 'c_' . $customers_statuses_array[$i]['id'] . '_group')) {
                                                                                    $checked = 'checked ';
                                                                                } else {
                                                                                    $checked = '';
                                                                                }
                                                                                echo '<input type="checkbox" name="groups[]" value="' . $customers_statuses_array[$i]['id'] . '"' . $checked . '> ' . $customers_statuses_array[$i]['text'] . '<br />';
                                                                            }
                                                                            ?>
                                                                                </td>
                                                                            </tr>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_TITLE_FILE; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_input_field('cont_title', $content['content_name'], 'size="60"'); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_LINK; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_input_field('cont_link', $content['content_link'], 'size="60"'); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%" valign="top"><?php echo TEXT_FILE_DESC; ?></td>
            <?php
            if (USE_WYSIWYG == 'false' && USE_CODEMIRROR == 'true') {
                ?>
                                                                                <td width="90%">
                                                                                    <div style="float:left; width: 50%">
                                                                                        <b><?php echo TEXT_EDIT; ?>:</b><br />
                <?php echo xtc_draw_textarea_field('file_comment', '', '100', '30', $content['file_comment']); ?>
                                                                                    </div>
                                                                                    <div style="float:left; width: 50%">
                                                                                        <b><?php echo TEXT_PREVIEW; ?>:</b><br />
                                                                                        <iframe id=preview></iframe>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <script>
                                                                                        var delay;
                                                                                        var editor = CodeMirror.fromTextArea(document.getElementById('file_comment'), {
                                                                                            mode: 'text/html',
                                                                                            tabMode: 'indent',
                                                                                            lineNumbers: true,
                                                                                        });
                                                                                        editor.on("change", function() {
                                                                                            clearTimeout(delay);
                                                                                            delay = setTimeout(updatePreview, 300);
                                                                                        });

                                                                                        function updatePreview() {
                                                                                            var previewFrame = document.getElementById('preview');
                                                                                            var preview = previewFrame.contentDocument || previewFrame.contentWindow.document;
                                                                                            preview.open();
                                                                                            preview.write(editor.getValue());
                                                                                            preview.close();
                                                                                        }
                                                                                        setTimeout(updatePreview, 300);
                                                                            </script>

                                                                            <?php
                                                                        } elseif (USE_WYSIWYG == 'true') {
                                                                            ?>
                                                                            <script src="includes/editor/ckeditor/ckeditor.js" type="text/javascript"></script>
                                                                            <?php
                                                                            if (file_exists('includes/editor/ckfinder/ckfinder.js')) {
                                                                                echo '<script src="includes/editor/ckfinder/ckfinder.js" type="text/javascript"></script>';
                                                                            }
                                                                            ?>
                                                                            <td width="90%">
                                                                            <?php echo xtc_draw_textarea_field('file_comment', 'soft', '100', '35', $content['file_comment'], 'class="ckeditor" name="editor1"'); ?>
                                                                            </td>
                <?php
                if (file_exists('includes/editor/ckfinder/ckfinder.js')) {
                    ?>	
                                                                                <script type="text/javascript">
                                                                                    var newCKEdit = CKEDITOR.replace('<?php echo 'file_comment' ?>');
                                                                                    CKFinder.setupCKEditor(newCKEdit, 'includes/editor/ckfinder/');
                                                                                </script>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                            <?php
                                                                        } else {
                                                                        ?>

                                                                            <td width="90%">
                                                                            <?php echo xtc_draw_textarea_field('file_comment', 'soft', '100', '35', $content['file_comment']); ?>
                                                                            </td>
																		<?php } ?>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%"><?php echo TEXT_CHOOSE_FILE; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_pull_down_menu('select_file', $content_files, $default_value); ?><?php echo ' ' . TEXT_CHOOSE_FILE_DESC; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="10%" valign="top"><?php echo TEXT_UPLOAD_FILE; ?></td>
                                                                            <td width="90%"><?php echo xtc_draw_file_field('file_upload') . ' ' . TEXT_UPLOAD_FILE_LOCAL; ?></td>
                                                                        </tr>
            <?php
            if ($content['content_file'] != '') {
                ?>
                                                                            <tr>
                                                                                <td width="10%"><?php echo TEXT_FILENAME; ?></td>
                                                                                <td width="90%" valign="top"><?php echo xtc_draw_hidden_field('file_name', $content['content_file']) . xtc_image(DIR_WS_CATALOG . 'admin/images/icons/icon_' . str_replace('.', '', strstr($content['content_file'], '.')) . '.gif') . $content['content_file']; ?></td>
                                                                            </tr>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                        <tr>
                                                                            <td colspan="2" align="right" class="main"><?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>'; ?><a class="button" onClick="this.blur();" href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER); ?>"><?php echo BUTTON_BACK; ?></a></td>
                                                                        </tr>
                                                                        </form>
                                                                    </table>

                                                                    <?php
                                                                    break;
                                                            }
                                                        }

                                                        if (!$_GET['action']) {
                                                            ?>
                                                            <br />
                                                            <a class="button" onClick="this.blur();" href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=new'); ?>"><?php echo BUTTON_NEW_CONTENT; ?></a>
                                                            <?php
                                                        }
                                                        ?>
                                                        </td>
                                                        </tr>
                                                        </table>
                                                        <?php
                                                        if (!$_GET['action']) {
// products content
// load products_ids into array

                                                            $products_id_query = xtc_db_query("SELECT DISTINCT
							pc.products_id,
							pd.products_name
							FROM " . TABLE_PRODUCTS_CONTENT . " pc, " . TABLE_PRODUCTS_DESCRIPTION . " pd
							WHERE pd.products_id=pc.products_id and pd.language_id='" . (int) $_SESSION['languages_id'] . "'");

                                                            $products_ids = array();
                                                            while ($products_id_data = xtc_db_fetch_array($products_id_query)) {
                                                                $products_ids[] = array('id' => $products_id_data['products_id'], 'name' => $products_id_data['products_name']);
                                                            }
                                                            ?>

                                                            <div class="pageHeading"><br /><?php echo HEADING_PRODUCTS_CONTENT; ?><br /></div>
                                                                <?php
                                                                xtc_spaceUsed(DIR_FS_CATALOG . 'media/products/');
                                                                echo '<div class="main">' . USED_SPACE . xtc_format_filesize($total) . '</div></br>';
                                                                ?>
                                                            <table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                                                <tr class="dataTableHeadingRow">
                                                                    <td class="dataTableHeadingContent" nowrap width="5%" ><?php echo TABLE_HEADING_PRODUCTS_ID; ?></td>
                                                                    <td class="dataTableHeadingContent" width="95%" align="left"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                                                                </tr>
                                                                <?php
                                                                for ($i = 0, $n = sizeof($products_ids); $i < $n; $i++) {
                                                                    if ($ii % 2 == 0)
                                                                        $f = 'dataTableRow';
                                                                    else
                                                                        $f = '';
                                                                    echo '<tr class="' . $f . '" onmouseover="this.className=\'dataTableRowOver\'" onmouseout="this.className=\'' . $f . '\'">' . "\n";
                                                                    ?>
                                                                    <td class="dataTableContent_products" align="left"><?php echo $products_ids[$i]['id']; ?></td>
                                                                    <td class="dataTableContent_products" align="left"><b><?php echo xtc_image(DIR_WS_CATALOG . 'admin/images/icon_arrow_right.gif'); ?> <a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER, 'pID=' . $products_ids[$i]['id']); ?>"><?php echo $products_ids[$i]['name']; ?></a></b></td>
                                                                    </tr>
                                                                    <?php
                                                                    if ($_GET['pID']) {
// display content elements
                                                                        $content_query = xtc_db_query("SELECT
                                        *
                                        FROM " . TABLE_PRODUCTS_CONTENT . "
                                        WHERE products_id='" . $_GET['pID'] . "' order by content_name");
                                                                        $content_array = '';
                                                                        while ($content_data = xtc_db_fetch_array($content_query)) {

                                                                            $content_array[] = array('id' => $content_data['content_id'],
                                                                                'name' => $content_data['content_name'],
                                                                                'file' => $content_data['content_file'],
                                                                                'link' => $content_data['content_link'],
                                                                                'comment' => $content_data['file_comment'],
                                                                                'languages_id' => $content_data['languages_id'],
                                                                                'read' => $content_data['content_read']);
                                                                        } // while content data

                                                                        if ($_GET['pID'] == $products_ids[$i]['id']) {
                                                                            ?>

                                                                            <tr>
                                                                                <td align="left"></td>
                                                                                <td align="left">

                                                                                    <table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                                                                        <tr class="dataTableHeadingRow">
                                                                                            <td class="dataTableHeadingContent" nowrap><?php echo TABLE_HEADING_PRODUCTS_CONTENT_ID; ?></td>
                                                                                            <td class="dataTableHeadingContent" nowrap >&nbsp;</td>
                                                                                            <td class="dataTableHeadingContent" nowrap ><?php echo TABLE_HEADING_LANGUAGE; ?></td>
                                                                                            <td class="dataTableHeadingContent" nowrap ><?php echo TABLE_HEADING_CONTENT_NAME; ?></td>
                                                                                            <td class="dataTableHeadingContent" nowrap><?php echo TABLE_HEADING_CONTENT_FILE; ?></td>
                                                                                            <td class="dataTableHeadingContent" nowrap><?php echo TABLE_HEADING_CONTENT_FILESIZE; ?></td>
                                                                                            <td class="dataTableHeadingContent" nowrap align="middle" width="20%" ><?php echo TABLE_HEADING_CONTENT_LINK; ?></td>
                                                                                            <td class="dataTableHeadingContent" nowrap><?php echo TABLE_HEADING_CONTENT_HITS; ?></td>
                                                                                            <td class="dataTableHeadingContent last" nowrap ><?php echo TABLE_HEADING_CONTENT_ACTION; ?></td>
                                                                                        </tr>

                                                                                            <?php
                                                                                            for ($ii = 0, $nn = sizeof($content_array); $ii < $nn; $ii++) {
                                                                                                if ($ii % 2 == 0)
                                                                                                    $f = 'dataTableRow';
                                                                                                else
                                                                                                    $f = '';
                                                                                                echo '<tr class="' . $f . '" onmouseover="this.className=\'dataTableRowOver\'" onmouseout="this.className=\'' . $f . '\'">' . "\n";
                                                                                                ?>
                                                                                            <td align="left"><?php echo $content_array[$ii]['id']; ?> </td>
                                                                                            <td align="left" class="last"><?php
                                                                            if ($content_array[$ii]['file'] != '') {
                                                                                echo xtc_image(DIR_WS_CATALOG . 'admin/images/icons/icon_' . str_replace('.', '', strstr($content_array[$ii]['file'], '.')) . '.gif');
                                                                            } else {
                                                                                echo xtc_image(DIR_WS_CATALOG . 'admin/images/icons/icon_link.gif');
                                                                            }

                                                                            for ($xx = 0, $zz = sizeof($languages); $xx < $zz; $xx++) {
                                                                                if ($languages[$xx]['id'] == $content_array[$ii]['languages_id']) {
                                                                                    $lang_dir = $languages[$xx]['directory'];
                                                                                    break;
                                                                                }
                                                                            }
                                                                            ?>
                                                                                            </td>
                                                                                            <td align="left"><?php echo xtc_image(DIR_WS_CATALOG . 'lang/' . $lang_dir . '/icon.gif'); ?></td>
                                                                                            <td align="left"><?php echo $content_array[$ii]['name']; ?></td>
                                                                                            <td align="left"><?php echo $content_array[$ii]['file']; ?></td>
                                                                                            <td align="left"><?php echo xtc_filesize($content_array[$ii]['file']); ?></td>
                                                                                            <td align="left" align="middle"><?php
                                                                                if ($content_array[$ii]['link'] != '') {
                                                                                    echo '<a href="' . $content_array[$ii]['link'] . '" target="new">' . $content_array[$ii]['link'] . '</a>';
                                                                                }
                                                                                                ?>
                                                                                                &nbsp;</td>
                                                                                            <td align="left"><?php echo $content_array[$ii]['read']; ?></td>
                                                                                            <td align="left" class="last">

                                                                                                <a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER, 'special=delete_product&coID=' . $content_array[$ii]['id']) . '&pID=' . $products_ids[$i]['id']; ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
                                                                                                        <?php
                                                                                                        echo xtc_image(DIR_WS_ICONS . 'delete.gif', 'Delete', '', '', 'style="cursor:pointer" onClick="return confirm(\'' . DELETE_ENTRY . '\')"') . '  ' . TEXT_DELETE . '</a>&nbsp;&nbsp;';
                                                                                                        ?>
                                                                                                    <a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=edit_products_content&coID=' . $content_array[$ii]['id']); ?>">
                                                                                                        <?php echo xtc_image(DIR_WS_ICONS . 'icon_edit.gif', 'Edit', '', '', 'style="cursor:pointer"') . '  ' . TEXT_EDIT . '</a>'; ?>

                                                                                                        <?php
// display preview button if filetype
                                                                                                        if (preg_match('/.gif/i', $content_array[$ii]['file']) or
                                                                                                                preg_match('/.jpg/i', $content_array[$ii]['file']) or
                                                                                                                preg_match('/.png/i', $content_array[$ii]['file']) or
                                                                                                                preg_match('/.html/i', $content_array[$ii]['file']) or
                                                                                                                preg_match('/.php/i', $content_array[$ii]['file']) or
                                                                                                                preg_match('/.htm/i', $content_array[$ii]['file']) or
                                                                                                                preg_match('/.avi/i', $content_array[$ii]['file']) or
                                                                                                                preg_match('/.txt/i', $content_array[$ii]['file']) or
                                                                                                                preg_match('/.doc/i', $content_array[$ii]['file']) or
                                                                                                                preg_match('/.bmp/i', $content_array[$ii]['file'])
                                                                                                        ) {
                                                                                                            ?>
                                                                                                            <a style="cursor:pointer" onClick="javascript:window.open('<?php echo xtc_href_link(FILENAME_CONTENT_PREVIEW, 'pID=media&coID=' . $content_array[$ii]['id']); ?>', 'popup', 'toolbar=0, width=640, height=600')">
                                                                                                                <?php
                                                                                                                echo xtc_image(DIR_WS_ICONS . 'preview.gif', 'Preview', '', '', ' style="cursor:pointer"') . '&nbsp;&nbsp;' . TEXT_PREVIEW . '</a>';
                                                                                                            }
                                                                                                            ?>
                                                                                                            </td>
                                                                                                            </tr>

                                                                                                            <?php
                                                                                                        } // for content_array
                                                                                                        echo '</table></td></tr>';
                                                                                                    }
                                                                                                } // for
                                                                                            }
                                                                                            ?>


                                                                                            </table><br />
                                                                                            <a class="button" onClick="this.blur();" href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=new_products_content'); ?>"><?php echo BUTTON_NEW_CONTENT; ?></a>
    <?php
} // if !$_GET['action']
?>

                                                                                        </td>
                                                                                        </tr>
                                                                                        </table></td>
                                                                                        </tr>
                                                                                        </table>
                                                                                        <?php
                                                                                        require(DIR_WS_INCLUDES . 'footer.php');
                                                                                        require(DIR_WS_INCLUDES . 'application_bottom.php');

                                                                                        