<?php

/* -----------------------------------------------------------------
 * 	$Id: content_manager.php 1434 2015-02-05 21:43:24Z akausch $
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
$coo_text_mgr = new LanguageTextManager('content_manager', $_SESSION['languages_id']);
$smarty = new Smarty;
$smarty->assign('txt', $coo_text_mgr->v_section_content_array['content_manager']);
require_once(DIR_FS_INC . 'xtc_format_filesize.inc.php');
require_once(DIR_FS_INC . 'xtc_filesize.inc.php');
require_once(DIR_FS_INC . 'cseo_get_url_friendly_text.inc.php');
if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
    require_once (DIR_FS_INC . 'commerce_seo.inc.php');
    !$commerceSeo ? $commerceSeo = new CommerceSeo() : false;
}

$languages = xtc_get_languages();

if (isset($_GET['special']) && $_GET['special'] == 'delete') {
    xtc_db_query("DELETE FROM " . TABLE_CONTENT_MANAGER . " WHERE content_id='" . (int) $_GET['coID'] . "';");
    if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
        $commerceSeo->createSeoDBTable();
    }
    xtc_redirect(xtc_href_link(FILENAME_CONTENT_MANAGER));
}
if ((isset($_GET['action'])) && ($_GET['action'] == 'set_flag')) {
	$coID = xtc_db_prepare_input($_GET['coID']);
	$status = xtc_db_prepare_input($_GET['status']);
	xtc_db_query("UPDATE " . TABLE_CONTENT_MANAGER . " SET content_status = '" . $status . "' WHERE content_id = '" . $coID . "';");
	xtc_redirect(xtc_href_link(FILENAME_CONTENT_MANAGER));
}

if ($_GET['id'] == 'update' || $_GET['id'] == 'insert') {
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
        if ($languages[$i]['code'] == $content_language) {
            $content_language = $languages[$i]['id'];
        }
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
        if ($select_file != 'default') {
            $content_file_name = $select_file;
        }

        if ($content_file = &xtc_try_upload('file_upload', DIR_FS_CATALOG . 'media/content/')) {
            $content_file_name = $content_file->filename;
        }

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
            'content_url_alias' => $content_url_alias,
            'last_modified' => 'now()',
        );

        if ($_GET['id'] == 'update') {
            xtc_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array, 'update', "content_id = '" . $coID . "'");
            if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
                $commerceSeo->updateSeoDBTable('content', 'update', $group_id);
            }
        } else {
            xtc_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array);
            if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
                $commerceSeo->createSeoDBTable();
            }
        }
        if (isset($_POST['cseo_update'])) {
            xtc_redirect(xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=edit&coID=' . $coID));
        } else {
            xtc_redirect(xtc_href_link(FILENAME_CONTENT_MANAGER));
        }
    }
}

require_once(DIR_WS_INCLUDES . 'header.php');

if (!$_GET['action']) {
    // Display Content
    $smarty->assign('ACTION', 'true');
    xtc_spaceUsed(DIR_FS_CATALOG . 'media/content/');
    $smarty->assign('SPACEUSED', USED_SPACE . xtc_format_filesize($total));
    $content = array();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $lang_icon = xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']);
        $content_query = xtc_db_query("SELECT * FROM " . TABLE_CONTENT_MANAGER . " WHERE languages_id='" . $languages[$i]['id'] . "' AND parent_id = '0' ORDER BY content_group, sort_order;");
        while ($content_data = xtc_db_fetch_array($content_query)) {
            $content_sub_query = xtc_db_query("SELECT * FROM " . TABLE_CONTENT_MANAGER . " WHERE languages_id='" . $languages[$i]['id'] . "' AND parent_id = '" . $content_data['content_group'] . "' ORDER BY content_group,sort_order ");
            $content_sub = array();
            if (xtc_db_num_rows($content_sub_query)) {
                while ($sub_data = xtc_db_fetch_array($content_sub_query)) {
                    $content_delete = '';
                    if ($sub_data['content_delete'] == '0') {
                        $content_delete = '<font color="ff0000">*</font>';
                    }
                    $content_url_alias = '';
                    if ($sub_data['content_url_alias'] != '') {
                        $content_url_alias = '<br /><span style="font-size:85%;color:#666"><em>URL Alias: ' . $sub_data['content_url_alias'] . '</em><span>';
                    }
                    if ($sub_data['content_file'] == '') {
                        $content_file = '-';
                    } else {
                        $content_file = $sub_data['content_file'];
                    }
                    if ($sub_data['content_status'] == 0) {
                        $content_status = '<button type="text" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></button>';
                    } else {
                        $content_status = '<button type="text" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-ok"></i></button>';
                    }
                    if (!empty($sub_data['content_out_link'])) {
                        $content_out_link = '<button type="text" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-ok"></i></button>';
                    } else {
                        $content_out_link = '-';
                    }
                    $delete_sub = '';
                    if ($sub_data['content_delete'] == '1') {
                        $delete_sub = '<a class="btn btn-danger btn-xs" href="' . xtc_href_link(FILENAME_CONTENT_MANAGER, 'special=delete&coID=' . $sub_data['content_id']) . '" onClick="return confirm(' . CONFIRM_DELETE . ')"><i onClick="return confirm(\'' . DELETE_ENTRY . '\')" class="glyphicon glyphicon-trash"></i></a>';
                    }
                    $edit_sub = '<a class="btn btn-info btn-xs" href="' . xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=edit&coID=' . $sub_data['content_id']) . '"><i class="glyphicon glyphicon-pencil"></i></a>';
                    if (!empty($sub_data['content_out_link'])) {
                        $view_sub = '<a class="btn btn-warning btn-xs" target="_blank" href="' . $sub_data['content_out_link'] . '"><i class="glyphicon glyphicon-link"></i></a>';
                    } else {
                        $view_sub = '<a class="btn btn-default btn-xs" onclick="javascript:window.open(\'' . xtc_href_link(FILENAME_CONTENT_PREVIEW, 'coID=' . $sub_data['content_id']) . '\', \'popup\', \'toolbar=0, width=640, height=600\')"><i class="glyphicon glyphicon-eye-open"></i></a>';
                    }
                    $file_flag_result = xtc_db_fetch_array(xtc_db_query("SELECT file_flag_name FROM " . TABLE_CM_FILE_FLAGS . " WHERE file_flag=" . $sub_data['file_flag']));
                    $content_sub[] = array(
                        'CONTENT_ID' => $sub_data['content_id'],
                        'PARENT_ID' => $sub_data['parent_id'],
                        'LANG_ICON' => $lang_icon,
                        'GROUP_IDS' => $sub_data['group_ids'],
                        'LANGUAGES_ID' => $sub_data['languages_id'],
                        'CONTENT_TITLE' => $sub_data['content_title'],
                        'CONTENT_URL_ALIAS' => $content_url_alias,
                        'CONTENT_OUT_LINK' => $content_out_link,
                        'CONTENT_LINK_TYPE' => $sub_data['content_link_type'],
                        'SORT_ORDER' => $sub_data['sort_order'],
                        'FILE_FLAG' => $file_flag_result['file_flag_name'],
                        'CONTENT_FILE' => $content_file,
                        'CONTENT_DELETE' => $content_delete,
                        'CONTENT_GROUP' => $sub_data['content_group'],
                        'CONTENT_STATUS' => $content_status,
                        'ACTION_DEL' => $delete_sub,
                        'ACTION_EDIT' => $edit_sub,
                        'ACTION_VIEW' => $view_sub,
                    );
                }
            }

            $file_flag_result = xtc_db_fetch_array(xtc_db_query("SELECT file_flag_name FROM " . TABLE_CM_FILE_FLAGS . " WHERE file_flag=" . $content_data['file_flag']));
            $content_title = '';
            if ($content_data['content_group'] == '0') {
                $content_title = '<img align="left" src="images/delete.gif" alt="" /> <span style="color:#ff0000;margin-left:5px"><strong>';
                $content_title .= $content_data['content_title'];
                $content_title .= '</strong><br /><br /> Sie haben keine Sprachgruppe definiert, daher wurde keine SEO URL erzeugt!</span>';
            } else {
                $content_title = $content_data['content_title'];
            }
            $content_delete = '';
            if ($content_data['content_delete'] == '0') {
                $content_delete = '<font color="ff0000">*</font>';
            }
            $content_url_alias = '';
            if ($content_data['content_url_alias'] != '') {
                $content_url_alias = '<br /><span style="font-size:85%;color:#666"><em>URL Alias: ' . $content_data['content_url_alias'] . '</em><span>';
            }
            if ($content_data['content_file'] == '') {
                $content_file = '-';
            } else {
                $content_file = $content_data['content_file'];
            }
			if ($content_data['content_status'] == 1) {
				$content_status = '<a href="' . xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=set_flag&status=0&coID=' . $content_data['content_id']) . '"><button class="btn btn-success btn-xs"><i class="glyphicon glyphicon-ok"></i></button></a>';
			} else {
				$content_status = '<a href="' . xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=set_flag&status=1&coID=' . $content_data['content_id']) . '"><button class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></button></a>';
			}
            if (!empty($content_data['content_out_link'])) {
                $content_out_link = '<button class="btn btn-success btn-xs"><i class="glyphicon glyphicon-ok"></i></button>';
            } else {
                $content_out_link = '-';
            }
            $delete = '';
            if ($content_data['content_delete'] == '1') {
                $delete = '<a class="btn btn-danger btn-xs" href="' . xtc_href_link(FILENAME_CONTENT_MANAGER, 'special=delete&coID=' . $content_data['content_id']) . '" onClick="return confirm(' . CONFIRM_DELETE . ')"><i onClick="return confirm(\'' . DELETE_ENTRY . '\')" class="glyphicon glyphicon-trash"></i></a>';
            }
            $edit = '<a class="btn btn-info btn-xs" href="' . xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=edit&coID=' . $content_data['content_id']) . '"><i class="glyphicon glyphicon-pencil"></i></a>';
            if (!empty($content_data['content_out_link'])) {
                $view = '<a class="btn btn-warning btn-xs" target="_blank" href="' . $content_data['content_out_link'] . '"><i class="glyphicon glyphicon-link"></i></a>';
            } else {
                $view = '<a class="btn btn-default btn-xs" onclick="javascript:window.open(\'' . xtc_href_link(FILENAME_CONTENT_PREVIEW, 'coID=' . $content_data['content_id']) . '\', \'popup\', \'toolbar=0, width=640, height=600\')"><i class="glyphicon glyphicon-eye-open"></i></a>';
            }
            $content[] = array(
                'CONTENT_ID' => $content_data['content_id'],
                'PARENT_ID' => $content_data['parent_id'],
                'LANG_ICON' => $lang_icon,
                'CONTENT_CHILD' => $content_sub,
                'GROUP_IDS' => $content_data['group_ids'],
                'LANGUAGES_ID' => $content_data['languages_id'],
                'CONTENT_TITLE' => $content_title,
                'CONTENT_URL_ALIAS' => $content_url_alias,
                'CONTENT_OUT_LINK' => $content_out_link,
                'SORT_ORDER' => $content_data['sort_order'],
                'FILE_FLAG' => $file_flag_result['file_flag_name'],
                'CONTENT_FILE' => $content_file,
                'CONTENT_DELETE' => $content_delete,
                'CONTENT_GROUP' => $content_data['content_group'],
                'CONTENT_STATUS' => $content_status,
                'ACTION_DEL' => $delete,
                'ACTION_EDIT' => $edit,
                'ACTION_VIEW' => $view,
            );
        }
    }

    $smarty->assign('contentlistarray', $content);
    $smarty->assign('langarray', $langarray);
    $smarty->assign('NEW_CONTENT', xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=new'));
} else {

    switch ($_GET['action']) {
	
	// Diplay Editmask
        case 'new':
        case 'edit':
            $smarty->assign('NEW_CONTENT_FORM', 'true');
            if ($_GET['action'] != 'new') {
                $content = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_CONTENT_MANAGER . " WHERE content_id='" . (int) $_GET['coID'] . "';"));
            }
            $languages_array = array();
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                if ($languages[$i]['id'] == $content['languages_id']) {
                    $languages_selected = $languages[$i]['code'];
                    $languages_id = $languages[$i]['id'];
                }
                $languages_array[] = array('id' => $languages[$i]['code'], 'text' => $languages[$i]['name']);
            }
            if ($languages_id != '') {
                $query_string = 'languages_id=' . $languages_id . ' AND';
            }
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
                $form = xtc_draw_form('edit_content', FILENAME_CONTENT_MANAGER, 'action=edit&id=update&coID=' . $_GET['coID'], 'post', 'enctype="multipart/form-data"') . xtc_draw_hidden_field('coID', $_GET['coID']);
            } else {
                $form = xtc_draw_form('edit_content', FILENAME_CONTENT_MANAGER, 'action=edit&id=insert', 'post', 'enctype="multipart/form-data"') . xtc_draw_hidden_field('coID', $_GET['coID']);
            }
            $smarty->assign('FORM', $form);
            $smarty->assign('FORM_END', '</form>');
            $smarty->assign('BUTTON_SUBMIT', '<button type="submit" class="btn btn-success">' . BUTTON_SAVE . '</button>');
            $smarty->assign('BUTTON_UPDATE', '<input type="submit" id="btnUpdate" name="cseo_update" class="btn btn-primary" value="' . BUTTON_UPDATE . '">');
            $smarty->assign('BUTTON_CANCEL', '<a href="' . xtc_href_link(FILENAME_CONTENT_MANAGER) . '"><button type="button" class="btn btn-default">' . BUTTON_BACK . '</button></a>');

            $smarty->assign('LANG_PULLDOWN', xtc_draw_pull_down_menu('language', $languages_array, $languages_selected));
            if ($content['content_delete'] != 0 || $_GET['action'] == 'new') {
                if (empty($content['content_group'])) {
                    $next_id = xtc_db_fetch_array(xtc_db_query("SELECT content_group FROM " . TABLE_CONTENT_MANAGER . " ORDER BY content_group DESC LIMIT 1"));
                    $id = $next_id['content_group'] + 1;
                } else {
                    $id = $content['content_group'];
                }
                $smarty->assign('CONTENT_GROUP', xtc_draw_input_field('content_group', $id, 'size="5"'));
            } else {
                $smarty->assign('CONTENT_GROUP', $content['content_group'] . xtc_draw_hidden_field('content_group', $content['content_group']));
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

            $smarty->assign('FILE_FLAG', xtc_draw_pull_down_menu('file_flag', $file_flag_array, $content['file_flag']));
            $smarty->assign('SLIDER_SET', xtc_draw_pull_down_menu('slider_set', $slider_set_array, $content['slider_set']));
            $smarty->assign('SORT_ORDER', xtc_draw_input_field('sort_order', $content['sort_order'], 'size="5"'));
            if ($content['content_status'] == '1') {
                $status = xtc_draw_checkbox_field('status', 'yes', true);
            } else {
                $status = xtc_draw_checkbox_field('status', 'yes', false);
            }
            $smarty->assign('STATUS', $status);
            if ($content['content_group'] != '5') {
                $smarty->assign('PARENT_CONTENT', xtc_draw_pull_down_menu('parent', $c_dropdown, $content['parent_id']));
            }
            $smarty->assign('CONT_TITLE', xtc_draw_input_field('cont_title', $content['content_title'], 'size="60"'));
            $smarty->assign('CONT_HEADING', xtc_draw_input_field('cont_heading', $content['content_heading'], 'size="60"'));
            $smarty->assign('CONT_URL_ALIAS', xtc_draw_input_field('cont_url_alias', $content['content_url_alias'], 'size="60"'));
            $smarty->assign('CONT_META_TITLE', xtc_draw_input_field('cont_meta_title', $content['content_meta_title'], 'size="60"'));
            $smarty->assign('CONTENT_TITLE_COUNT', xtc_zeichen_count($content['content_meta_title']));
            $smarty->assign('CONT_META_DESCRIPTION', xtc_draw_input_field('cont_meta_description', $content['content_meta_description'], 'size="60"'));
            $smarty->assign('CONT_META_DESCRIPTION_COUNT', xtc_zeichen_count($content['content_meta_description']));
            $smarty->assign('CONT_META_KEYWORDS', xtc_draw_input_field('cont_meta_keywords', $content['content_meta_keywords'], 'size="60"'));
            $smarty->assign('TEXT_LINK', xtc_draw_input_field('content_out_link', $content['content_out_link'], 'size="60"'));
            $smarty->assign('TEXT_ZIEL', xtc_draw_pull_down_menu('content_link_target', $link_target, $content['content_out_link']));
            $smarty->assign('TEXT_TYP', xtc_draw_pull_down_menu('content_link_type', $link_type, $content['content_link_type']));
            $smarty->assign('UPLOAD', xtc_draw_file_field('file_upload'));
            if ($dir = opendir(DIR_FS_CATALOG . 'media/content/')) {
                while (($file = readdir($dir)) !== false) {
                    if (is_file(DIR_FS_CATALOG . 'media/content/' . $file) and ( $file != "index.html")) {
                        $files[] = array(
                            'id' => $file,
                            'text' => $file);
                    }
                }
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
            $smarty->assign('FILE_SELECT', xtc_draw_pull_down_menu('select_file', $files, $default_value));
            if ($content['content_file'] != '') {
                $smarty->assign('CURRENT_FILE', $content['content_file']);
            }
            $smarty->assign('TEMPLATE_CHOSE', xtc_image('images/template_content.gif', 'Content Seite'));

            if (isset($_GET['coID'])) {
                $content_col_top = xtc_draw_selection_field('content_col_top', 'checkbox', '1', $content['content_col_top'] == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_TOP . '<br />';
                $content_col_left = xtc_draw_selection_field('content_col_left', 'checkbox', '1', $content['content_col_left'] == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_LEFT . '<br />';
                $content_col_right = xtc_draw_selection_field('content_col_right', 'checkbox', '1', $content['content_col_right'] == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_RIGHT . '<br />';
                $content_col_bottom = xtc_draw_selection_field('content_col_bottom', 'checkbox', '1', $content['content_col_bottom'] == 1 ? true : false) . TEXT_TEMPLATE_COLUMN_BUTTON;
            } else {
                $content_col_top = xtc_draw_selection_field('content_col_top', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_TOP . '<br />';
                $content_col_left = xtc_draw_selection_field('content_col_left', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_LEFT . '<br />';
                $content_col_right = xtc_draw_selection_field('content_col_right', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_RIGHT . '<br />';
                $content_col_bottom = xtc_draw_selection_field('content_col_bottom', 'checkbox', '1', true) . TEXT_TEMPLATE_COLUMN_BUTTON;
            }

            $smarty->assign('content_col_top', $content_col_top);
            $smarty->assign('content_col_left', $content_col_left);
            $smarty->assign('content_col_right', $content_col_right);
            $smarty->assign('content_col_bottom', $content_col_bottom);
			$smarty->assign('CONTENT', xtc_draw_textarea_field('cont', 'soft', '100', '35', $content['content_text'], ''));
            if (USE_WYSIWYG == 'true') {
                if (file_exists('includes/ckfinder/ckfinder.js')) {
					$smarty->assign('EDITOR', '<script src="includes/ckeditor/ckeditor.js"></script>
												<script src="includes/ckfinder/ckfinder.js"></script>
												<script>
												var newCKEdit = CKEDITOR.replace(\'cont\');
												CKFinder.setupCKEditor(newCKEdit, \'includes/ckfinder/\');
											</script>');
				} else {
					$smarty->assign('EDITOR', '<script src="includes/ckeditor/ckeditor.js"></script>
					<script>
						CKEDITOR.replace(\'cont\', {
							toolbar: "ImageMapper",
							language: "' . $_SESSION['language_code'] . '",
							baseHref: "' . (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . '",
							filebrowserBrowseUrl: "includes/ckeditor/filemanager/index.html"
						});
					</script>');
				}
            }

            if (GROUP_CHECK == 'true') {
                $customers_statuses_array = xtc_get_customers_statuses();
                $customers_statuses_array = array_merge(array(array('id' => 'all', 'text' => TXT_ALL)), $customers_statuses_array);
                for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
                    if (strstr($content['group_ids'], 'c_' . $customers_statuses_array[$i]['id'] . '_group')) {
                        $checked = 'checked ';
                    } else {
                        $checked = '';
                    }
                    $group_check[] = array('input' => '<input type="checkbox" name="groups[]" value="' . $customers_statuses_array[$i]['id'] . '"' . $checked . ' /> ' . $customers_statuses_array[$i]['text'] . '<br />');
                }
            }

            $smarty->assign('GROUPCHECK', $group_check);
            break;
    }
}
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
$smarty->caching = false;
$smarty->template_dir = DIR_FS_CATALOG . 'admin/templates/';
$smarty->compile_dir = DIR_FS_CATALOG . 'admin/templates_c';
$smarty->display(CURRENT_ADMIN_TEMPLATE . '/content_manager.html');

require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
