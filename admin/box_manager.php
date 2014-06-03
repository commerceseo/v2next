<?php

/* -----------------------------------------------------------------
 * 	$Id: box_manager.php 991 2014-04-24 07:38:43Z akausch $
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
$smarty = new Smarty;
$coo_text_mgr = new LanguageTextManager('boxenmanager', $_SESSION['languages_id']);
$smarty->assign('txt', $coo_text_mgr->v_section_content_array['boxenmanager']);

$data = xtc_db_fetch_array($query = xtc_db_query("SELECT code FROM " . TABLE_LANGUAGES . " WHERE languages_id='" . (int) $_SESSION['languages_id'] . "';"));
$languages = xtc_get_languages();

if ((isset($_POST['action'])) && ($_POST['action'] == 'save')) {
    $box_mobile = xtc_db_prepare_input($_POST['box_mobile']);
    $box_position = xtc_db_prepare_input($_POST['box_position']);
    $box_sort_id = xtc_db_prepare_input($_POST['box_sort_id']);
    $box_status = xtc_db_prepare_input($_POST['box_status']);
    $box_name_status = xtc_db_prepare_input($_POST['box_name_status']);
    $name = xtc_db_prepare_input($_POST['name']);
    $box_name = xtc_db_prepare_input($_POST['box_name']);

    $db_box = xtc_db_query("UPDATE 
								boxes 
							SET 
								position = '" . $box_position . "', 
								sort_id = '" . $box_sort_id . "', 
								status = '" . $box_status . "', 
								mobile = '" . $box_mobile . "' 
							WHERE 
								box_name = '" . $name . "'  ");

    foreach ($languages AS $lang) {
        foreach ($box_name[$lang['id']] AS $box) {
            if (!empty($box)) {
                $db = xtc_db_query("UPDATE 
										boxes_names 
									SET 
										box_title = '" . trim($box) . "', 
										status = '" . $box_name_status . "' 
									WHERE 
										language_id = '" . $lang['id'] . "' 
									AND 
										box_name = '" . $name . "';");
            }
        }
    }
    xtc_redirect(xtc_href_link('box_manager.php'));
    exit();
} elseif (isset($_GET['set_flag']) && (!empty($_GET['set_flag']))) {
    $box_status = xtc_db_prepare_input($_GET['status']);
    $db_box = xtc_db_query("UPDATE boxes SET status = '" . $box_status . "' WHERE box_name = '" . $_GET['set_flag'] . "'  ");
    xtc_redirect(xtc_href_link('box_manager.php'));
    exit();
} elseif (isset($_GET['set_name_flag']) && (!empty($_GET['set_name_flag']))) {
    $box_status = xtc_db_prepare_input($_GET['status']);
    $db_box = xtc_db_query("UPDATE boxes_names SET status = '" . $box_status . "' WHERE box_name = '" . $_GET['set_name_flag'] . "'  ");
    xtc_redirect(xtc_href_link('box_manager.php'));
    exit();
} elseif (isset($_GET['delete']) && (!empty($_GET['delete']))) {
    xtc_db_query("DELETE FROM boxes WHERE box_name = '" . $_GET['delete'] . "' AND box_type != 'file' ");
    xtc_db_query("DELETE FROM boxes WHERE box_name = '" . $_GET['delete'] . "'");
    xtc_db_query("DELETE FROM boxes_names WHERE box_name = '" . $_GET['delete'] . "' ");
    xtc_redirect(xtc_href_link('box_manager.php'));
    exit();
} elseif ((isset($_POST['save'])) && (($_POST['save'] == 'new_box') || $_POST['save'] == 'edit_new_box')) {
    if ($_POST['save'] == 'new_box') {
        $sql_data_array = array('id' => '',
            'box_name' => xtc_db_prepare_input($_POST['box_int_name']),
            'box_type' => xtc_db_prepare_input($_POST['box_type']),
            'position' => xtc_db_prepare_input($_POST['box_position']),
            'sort_id' => xtc_db_prepare_input($_POST['box_sort_id']),
            'mobile' => xtc_db_prepare_input($_POST['box_mobile']),
            'status' => xtc_db_prepare_input($_POST['box_status']),
            'file_flag' => 0);
        xtc_db_perform('boxes', $sql_data_array);
    } elseif ($_POST['save'] == 'edit_new_box') {
        $sql_data_array = array('position' => xtc_db_prepare_input($_POST['box_position']),
            'sort_id' => xtc_db_prepare_input($_POST['box_sort_id']),
            'mobile' => xtc_db_prepare_input($_POST['box_mobile']),
            'status' => xtc_db_prepare_input($_POST['box_status']));
        xtc_db_perform('boxes', $sql_data_array, 'update', 'box_name = \'' . $_POST['name'] . '\'');
    }

    foreach ($languages AS $lang) {
        $language_id = $lang['id'];
        if ($_POST['save'] == 'new_box') {
            $insert_data_array = array('id' => '',
                'box_name' => xtc_db_prepare_input($_POST['box_int_name']),
                'box_title' => xtc_db_prepare_input($_POST['box_title_' . $language_id]),
                'box_desc' => xtc_db_prepare_input($_POST['new_box_' . $language_id]),
                'language_id' => $language_id,
                'status' => xtc_db_prepare_input($_POST['box_name_status']));

            xtc_db_perform('boxes_names', $insert_data_array);
        } elseif ($_POST['save'] == 'edit_new_box') {
            $update_data_array = array('box_title' => xtc_db_prepare_input($_POST['box_title_' . $language_id]),
                'box_desc' => xtc_db_prepare_input($_POST['new_box_' . $language_id]),
                'language_id' => $language_id,
                'status' => xtc_db_prepare_input($_POST['box_name_status']));
            xtc_db_perform('boxes_names', $update_data_array, 'update', 'box_name = \'' . $_POST['name'] . '\' and language_id = \'' . $language_id . '\'');
        }
    }
    xtc_redirect(xtc_href_link('box_manager.php'));
    exit();
} elseif (isset($_POST['filter']) && ($_POST['filter'] == 'boxes')) {
    $sql_where = '';
    $sql_order_by = '';

    if ($_POST['name_int'] == 'asc') {
        $sql_order_by = " ORDER BY b.box_name ASC";
    } elseif ($_POST['name_int'] == 'desc') {
        $sql_order_by = " ORDER BY b.box_name DESC";
    }
    if ($_POST['box_status'] == 'on') {
        $sql_where .= " AND b.status = '1'";
    } elseif ($_POST['box_status'] == 'off') {
        $sql_where .= " AND b.status = '0'";
    }
    if ($_POST['name'] == 'asc') {
        $sql_order_by = " ORDER BY bn.box_title ASC";
    } elseif ($_POST['name'] == 'desc') {
        $sql_order_by = " ORDER BY bn.box_title DESC";
    }
    if (!empty($_POST['position'])) {
        $sql_where .= " AND b.position = '" . $_POST['position'] . "'";
    }
    if ($_POST['box_name'] == 'on') {
        $sql_where .= " AND bn.status = '1'";
    } elseif ($_POST['box_name'] == 'off') {
        $sql_where .= " AND bn.status = '0'";
    }
    if ($_POST['box_mobile'] == 'on') {
        $sql_where .= " AND b.mobile = '1'";
    } elseif ($_POST['box_mobile'] == 'off') {
        $sql_where .= " AND b.mobile = '0'";
    }
}

if ($sql_order_by == '')
    $sql_order_by = " ORDER BY position,sort_id ASC";

$position_query = xtc_db_query("SELECT id, position_name FROM boxes_positions order by id");
$position_array = array(array('id' => '', 'text' => '------'));
while ($pos = xtc_db_fetch_array($position_query)) {
    $position_array[] = array('id' => $pos['position_name'], 'text' => $pos['position_name']);
}
$status = array(array('id' => '0', 'text' => '----'));
$status[] = array('id' => 'on', 'text' => YES);
$status[] = array('id' => 'off', 'text' => NO);

$box_name = array(array('id' => '', 'text' => '-----------'));
$box_name[] = array('id' => 'asc', 'text' => 'Alphabet A-Z');
$box_name[] = array('id' => 'desc', 'text' => 'Alphabet Z-A');

$box_type = array(array('id' => 'database', 'text' => 'database'));
$box_type[] = array('id' => 'file', 'text' => 'file');


require_once (DIR_WS_INCLUDES . 'header.php');

if (USE_WYSIWYG == 'true') {
    echo '<script src="includes/editor/ckeditor/ckeditor.js" type="text/javascript"></script>';
	echo '<script src="includes/editor/ckfinder/ckfinder.js" type="text/javascript"></script>';
}

if ((!isset($_GET['action'])) && ($_GET['action'] != 'edit_box')) {
    //Liste
    echo '<script>
			function changeFlag(box_id) {
			jQuery.ajax({
				type: "POST",
				url: "includes/javascript/box_manager_change_flag.php",
				data: {\'id\': box_id},
				dataType: "html",
				success: function(msg) {
					jQuery(\'#box_\' + box_id).html(msg).fadeIn("slow");
					jQuery(location).attr("hash", jQuery(this).attr("hash"));
				}
			});
			return false;
		}
		;
	</script>
	';

    $uebersicht_query = xtc_db_query("SELECT b.id AS bid,
											bn.id AS bnid,
											b.box_name,
											b.position,
											b.sort_id,
											b.box_type,
											b.status AS box,
											b.mobile AS mobile,
											bn.status AS bname,
											bn.box_title
											FROM boxes b,
											boxes_names bn
											WHERE bn.box_name = b.box_name
											AND bn.language_id = '" . (int) $_SESSION['languages_id'] . "'
											" . $sql_where . "
											" . $sql_order_by . " ");


    while ($box = xtc_db_fetch_array($uebersicht_query)) {
        if ($box['box_type'] == 'database') {
            $box_edit_link = '<a href="' . xtc_href_link('box_manager.php', 'action=edit_new_box&name=' . $box['box_name']) . '"><span class="glyphicon glyphicon-edit"></span></a>';
        } else {
            $box_edit_link = '<a data-toggle="modal" href="' . xtc_href_link('box_manager.php', 'action=edit_box&name=' . $box['box_name']) . '"><span class="glyphicon glyphicon-edit"></span></a>';
        }
        if ($box['box'] == 1) {
            $box_status_icon = xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', '', '10') . ' ';
            $box_status_link = '<a class="box_flag" id="bs_' . $box['bid'] . '" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="' . DIR_WS_IMAGES . 'icon_status_red_light.gif" height="10" alt="" title="deaktivieren" /></a>';
        } else {
            $box_status_link = '<a class="box_flag" id="bs_' . $box['bid'] . '" height="10" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="' . DIR_WS_IMAGES . 'icon_status_green_light.gif" alt="" height="10" title="aktivieren" /></a> ';
            $box_status_icon = xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', '', '10') . ' ';
        }
        if ($box['mobile'] == 1) {
            $box_mobile_icon = xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', '', '10') . ' ';
            $box_mobile_link = '<a class="box_flag" id="bm_' . $box['bid'] . '" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="' . DIR_WS_IMAGES . 'icon_status_red_light.gif" height="10" alt="" title="deaktivieren" /></a>';
        } else {
            $box_mobile_link = '<a class="box_flag" id="bm_' . $box['bid'] . '" height="10" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="' . DIR_WS_IMAGES . 'icon_status_green_light.gif" alt="" height="10" title="aktivieren" /></a> ';
            $box_mobile_icon = xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', '', '10') . ' ';
        }
        if ($box['bname'] == 1) {
            $box_status_n_icon = xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', '', '10') . ' ';
            $box_status_n_link = ' <a class="box_flag" id="bn_' . $box['bnid'] . '" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="' . DIR_WS_IMAGES . 'icon_status_red_light.gif" height="10" alt="" title="deaktivieren" /></a>';
        } else {
            $box_status_n_link = '<a class="box_flag" id="bn_' . $box['bnid'] . '" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="' . DIR_WS_IMAGES . 'icon_status_green_light.gif" height="10" alt="" title="aktivieren" /></a> ';
            $box_status_n_icon = xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', '', '10') . ' ';
        }
        if ($box['box_type'] == 'file') {
            $box_type = '<span class="glyphicon glyphicon-file"></span>';
        } else {
            $box_type = '<span class="glyphicon glyphicon-folder-open"></span>';
        }
        if ($box['box_type'] == 'file') {
            $box_type_link = '<a href="' . $_SERVER['REQUEST_URI'] . '?delete=' . $box['box_name'] . '"><span class="glyphicon glyphicon-trash"></span></a>';
        } else {
            $box_type_link = '<a href="' . $_SERVER['REQUEST_URI'] . '?delete=' . $box['box_name'] . '"><span class="glyphicon glyphicon-trash"></span></a>';
        }

        $boxlistarray[] = array('BOX_EDIT' => $box_edit_link,
            'BOX_TITLE' => $box['box_title'],
            'BOX_NAME' => $box['box_name'],
            'BOX_TYPE' => $box['box_type'],
            'BOX_POSITION' => $box['position'],
            'BOX_SORT_ID' => $box['sort_id'],
            'BOX_MOBILE' => $box_mobile_icon . $box_mobile_link,
            'BOX_STATUS' => $box_status_icon . $box_status_link,
            'BOX_BID' => $box['bid'],
            'BOX_N_STATUS' => $box_status_n_icon . $box_status_n_link,
            'BOX_BMID' => $box['bid'],
            'BOX_BNID' => $box['bnid'],
            'BOX_TYPE' => $box_type,
            'BOX_TYPE_LINK' => $box_type_link);
    }
    $smarty->assign('boxlistarray', $boxlistarray);
    $smarty->assign('BOX_LIST', 'true');
    $smarty->assign('BOX_NEW', xtc_button_link(BUTTON_NEW_BOX, 'box_manager.php?action=new_box'));
    $smarty->assign('BOX_HIDDEN', xtc_draw_hidden_field('filter', 'boxes'));
    $smarty->assign('BOX_SORT_TITLE', xtc_sorting(FILENAME_BOX_MANAGER, 'title'));
    $smarty->assign('BOX_SORT_NAME', xtc_sorting(FILENAME_BOX_MANAGER, 'name'));
    $smarty->assign('BOX_SORT_POSITION', xtc_sorting(FILENAME_BOX_MANAGER, 'position'));
    $smarty->assign('BOX_SORT_SORT', xtc_sorting(FILENAME_BOX_MANAGER, 'sort'));
    $smarty->assign('BOX_SORT_MOBILE', xtc_sorting(FILENAME_BOX_MANAGER, 'mobile'));
    $smarty->assign('BOX_SORT_STATUS', xtc_sorting(FILENAME_BOX_MANAGER, 'status'));
    $smarty->assign('BOX_SORT_NAMESTATUS', xtc_sorting(FILENAME_BOX_MANAGER, 'namestatus'));
    $smarty->assign('BOX_SORT_TYP', xtc_sorting(FILENAME_BOX_MANAGER, 'typ'));
} elseif ((isset($_GET['action'])) && (($_GET['action'] == 'new_box') || ($_GET['action'] == 'edit_new_box'))) {
    //Box New
    $dd[] = array('id' => '1', 'text' => YES);
    $dd[] = array('id' => '0', 'text' => NO);

    $positions_query = xtc_db_query("SELECT id, position_name FROM boxes_positions order by id;");
    while ($pos = xtc_db_fetch_array($positions_query, true))
        $positions_array[] = array('id' => $pos['position_name'], 'text' => $pos['position_name']);

    if ($_GET['action'] == 'edit_new_box') {
        $new_box = xtc_db_fetch_array(xtc_db_query("SELECT b.id, b.box_name, b.position, b.sort_id, b.status AS box, bn.status AS bname, b.mobile AS mobile FROM boxes AS b, boxes_names AS bn WHERE bn.box_name = b.box_name AND bn.language_id = '" . (int) $_SESSION['languages_id'] . "' AND b.box_name = '" . $_GET['name'] . "';"));
    }
    $smarty->assign('BOX_NEW', 'true');
    $smarty->assign('FORM', xtc_draw_form('new_box', FILENAME_BOX_MANAGER, '', 'post', 'id="database_box"'));
    $smarty->assign('FORM_END', '</form>');
    $smarty->assign('BOX_TYPE', xtc_draw_pull_down_menu('box_type', $box_type, $new_box['box_type']));
    $smarty->assign('NEW_POSITION', xtc_draw_pull_down_menu('box_position', $positions_array, $new_box['position']));
    $smarty->assign('NEW_SORT', xtc_draw_small_input_field('box_sort_id', $new_box['sort_id']));
    $smarty->assign('NEW_STATUS', xtc_draw_pull_down_menu('box_status', $dd, $new_box['box']));
    $smarty->assign('NEW_NAME_MOBILE', xtc_draw_pull_down_menu('box_mobile', $dd, $new_box['mobile']));
    $smarty->assign('NEW_NAME_STATUS', xtc_draw_pull_down_menu('box_name_status', $dd, $new_box['bname']));

    if (!empty($new_box['box_name']))
        $new_name = $new_box['box_name'] . xtc_draw_hidden_field('box_int_name', $new_box['box_name']);
    else
        $new_name = xtc_draw_input_field('box_int_name', '', 'class="box_int_name"');

    $smarty->assign('NEW_NAME', $new_name);
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        if ($_GET['action'] == 'edit_new_box') {
            $name_query = xtc_db_query("SELECT box_title,box_desc FROM boxes_names WHERE box_name = '" . $_GET['name'] . "' AND language_id = '" . $languages[$i]['id'] . "';");
            $name = xtc_db_fetch_array($name_query);
        }
        $lang_images = xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']);
        if (file_exists('includes/editor/ckfinder/ckfinder.js') && USE_WYSIWYG == 'true') {
            $field_sdesc_wy = '1';
        } else {
            $field_sdesc_wy = '0';
        }
        $boxnewarray[$i] = array(
            'tabid' => $languages[$i]['id'],
            'langid' => $languages[$i]['id'],
            'langname' => $languages[$i]['name'],
            'lang_images' => $lang_images,
            'boxtitle' => xtc_draw_input_field('box_title_' . $languages[$i]['id'], $name['box_title']),
            'boxtextfiled' => xtc_draw_textarea_field('new_box_' . $languages[$i]['id'], 'soft', '', '', $name['box_desc'], 'class="ckeditor" name="editor1"'),
            'field_sdesc_wy' => $field_sdesc_wy
        );
    }
    $smarty->assign('boxnewarray', $boxnewarray);

    if (xtc_db_num_rows($name_query))
        $hidden_save = xtc_draw_hidden_field('save', 'edit_new_box');
    else
        $hidden_save = xtc_draw_hidden_field('save', 'new_box');

    $smarty->assign('HIDDEN_SAVE', $hidden_save);
    $smarty->assign('HIDDEN_NAME', xtc_draw_hidden_field('name', $_GET['name']));
    $smarty->assign('BUTTON_SUBMIT', xtc_button(BUTTON_SAVE, 'submit'));
    $smarty->assign('BUTTON_CANCEL', xtc_button_link(BUTTON_CANCEL, 'box_manager.php'));
} elseif ($_GET['action'] == 'edit_box') {
//Edit Box
    $box_query = xtc_db_query("SELECT 
									b.id AS id,
									b.box_name AS box_name,
									b.position AS position,
									b.sort_id AS sort_id,
									b.status AS box,
									b.mobile AS mobile,
									b.box_type AS type,
									bn.status AS bname,
									bn.box_title AS title
									FROM 
										boxes AS b, 
										boxes_names AS bn
									WHERE 
										b.box_name = '" . $_GET['name'] . "'
									AND 
										bn.box_name = '" . $_GET['name'] . "' ");

    $box = xtc_db_fetch_array($box_query);

    $dd[] = array('id' => '1', 'text' => YES);
    $dd[] = array('id' => '0', 'text' => NO);

    $position_edit_query = xtc_db_query("SELECT id, position_name FROM boxes_positions ORDER BY id");
    while ($pos = xtc_db_fetch_array($position_edit_query)) {
        $position_edit_array[] = array('id' => $pos['position_name'], 'text' => $pos['position_name']);
    }
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $name = xtc_db_fetch_array(xtc_db_query("SELECT box_title FROM boxes_names WHERE box_name = '" . $_GET['name'] . "' AND language_id = '" . $languages[$i]['id'] . "';"));
        $lang_images = xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']);
        $boxeditarray[$i] = array(
            'langname' => $languages[$i]['name'],
            'lang_images' => $lang_images,
            'boxtitle' => xtc_draw_input_field('box_name[' . $languages[$i]['id'] . '][]', $name['box_title'], '')
        );
    }

    $smarty->assign('boxeditarray', $boxeditarray);
    $smarty->assign('BOX_EDIT', 'true');
    $smarty->assign('FORM', xtc_draw_form('edit_box', FILENAME_BOX_MANAGER, '', 'post', ''));
    $smarty->assign('FORM_END', '</form>');
    $smarty->assign('BOX_POSITION', xtc_draw_pull_down_menu('box_position', $position_edit_array, $box['position']));
    $smarty->assign('BOX_SORT', xtc_draw_small_input_field('box_sort_id', ($box['sort_id'] != '') ? $box['sort_id'] : '0'));
    $smarty->assign('BOX_MOBILE', xtc_draw_pull_down_menu('box_mobile', $dd, $box['mobile']));
    $smarty->assign('BOX_STATUS', xtc_draw_pull_down_menu('box_status', $dd, $box['box']));
    $smarty->assign('BOX_NAME_STATUS', xtc_draw_pull_down_menu('box_name_status', $dd, $box['bname']));
    $smarty->assign('HIDDEN', xtc_draw_hidden_field('name', $_GET['name']) . xtc_draw_hidden_field('action', 'save'));
    $smarty->assign('BUTTON_SUBMIT', xtc_button(BUTTON_SAVE, 'submit'));
    $smarty->assign('BUTTON_CANCEL', xtc_button_link(BUTTON_CANCEL, 'box_manager.php'));
}

$smarty->assign('FORMSORT', xtc_draw_form('sort_boxes', FILENAME_BOX_MANAGER, '', 'post', ''));
$smarty->assign('BOXFILTERNAME', xtc_draw_pull_down_menu('name', $box_name, $_POST['name'], 'onchange="this.form.submit();"'));
$smarty->assign('BOXFILTERINTERN', xtc_draw_pull_down_menu('name_int', $box_name, $_POST['name_int'], 'onchange="this.form.submit();"'));
$smarty->assign('BOXFILTERPOSITION', xtc_draw_pull_down_menu('position', $position_array, $_POST['position'], 'onchange="this.form.submit();"'));
$smarty->assign('BOXFILTERAKTIV', xtc_draw_pull_down_menu('box_status', $status, $_POST['box_status'], 'onchange="this.form.submit();"'));
$smarty->assign('BOXFILTERNAMEAKTIV', xtc_draw_pull_down_menu('box_name', $status, $_POST['box_name'], 'onchange="this.form.submit();"'));
$smarty->assign('BOXFILTERMOBILEAKTIV', xtc_draw_pull_down_menu('box_mobile', $status, $_POST['box_mobile'], 'onchange="this.form.submit();"'));
$smarty->assign('HIDDENSORT', '<input type="hidden" name="filter" value="boxes" />');
$smarty->assign('FORMEND', '</form>');


$smarty->assign('currencies', DEFAULT_CURRENCY);
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
$smarty->caching = false;
$smarty->template_dir = DIR_FS_CATALOG . 'admin/templates/';
$smarty->compile_dir = DIR_FS_CATALOG . 'admin/templates_c';
$smarty->display(CURRENT_ADMIN_TEMPLATE . '/box_manager.html');

require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
echo "<script>
			head.ready(function() {
			jQuery('#database_box').submit(function() {
				if (jQuery('.box_int_name').val() != '') {
					return true;
				}
				alert('Geben Sie einen eindeutigen Begriff für die interne Bezeichnung an!');
				jQuery('.box_int_name').focus().css('border', '2px solid #b20000');
				return false;
			});
			});
		</script>";