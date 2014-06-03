<?php

/* -----------------------------------------------------------------
 * 	$Id: class.blog.php 979 2014-04-14 14:46:39Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * Copyright by H&S eCom 
 * @author little Pit(S.B.)
 * 	http://www.commerce-seo.de : www.indiv-style.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

function xtc_set_reviews_status($reviews_id, $status) {
    return xtc_db_query("UPDATE " . TABLE_BLOG_COMMENT . " SET comment_status = '" . $status . "' WHERE id = '" . $reviews_id . "'");
}

function blogCategories($post, $action = 'insert') {
    if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
        require_once (DIR_FS_INC . 'commerce_seo.inc.php');
        $commerceSeo = new CommerceSeo();
    }

    include(DIR_FS_ADMIN . 'includes/classes/categories.php');
    $languages = xtc_get_languages();
    $catfunc = new categories();
    $categorie = array(
        'sort_order' => $_POST['tmpos'],
        'status' => '1',
        'groups' => $_POST['groups'],
    );


    if ($action == 'insert') {
        $next_query = xtc_db_query("SELECT MAX(id) AS ID FROM " . TABLE_BLOG_CATEGORIES);
        $next_id = xtc_db_fetch_array($next_query);
        $next_id = $next_id['ID'];
        if ($next_id != 0) {
            $next_id+=1;
        } else {
            $next_id = 1;
        }
    }
    $lang = sizeof($languages);
    for ($i = 0; $i < $lang; $i++) {

        $languages_id = $languages[$i]['id'];
        $group_ids = '';
        if (isset($_POST['groups'])) {
            foreach ($_POST['groups'] as $b) {
                $group_ids .= 'c_' . $b . "_group ,";
            }
        }
        $customers_statuses_array = xtc_get_customers_statuses();
        if (strstr($group_ids, 'c_all_group')) {
            $group_ids = 'c_all_group,';
            foreach ($customers_statuses_array AS $t_gm_key => $t_gm_value) {
                $group_ids .='c_' . $t_gm_value['id'] . '_group,';
            }
        }
        if ($_POST['tmcat'] == '') {
            $_POST['tmcat'] = 'false';
        }
        $sql_categorie_array = array(
            'language_id' => $languages_id,
            'titel' => xtc_db_prepare_input($_POST['titel'][$languages_id]),
            'parent_id' => xtc_db_prepare_input($_POST['parent_id']),
            'description' => xtc_db_prepare_input($_POST['description_' . $languages_id]),
            'short_description' => xtc_db_prepare_input($_POST['short_description_' . $languages_id]),
            'group_ids' => $group_ids,
            'tmid' => $_POST['parent_id2'],
            'sort_order' => $_POST['tmpos'],
            'tmselect' => $_POST['tmcat'],
            'position' => xtc_db_prepare_input($_POST['position'][$languages_id]),
            'meta_title' => xtc_db_prepare_input($_POST['meta_title'][$languages_id]),
            'meta_desc' => xtc_db_prepare_input($_POST['meta_desc'][$languages_id]),
            'meta_key' => xtc_db_prepare_input($_POST['meta_key'][$languages_id])
        );
        $gm_url_keywords[$languages_id] = xtc_db_prepare_input($_POST['titel'][$languages_id]);
        $categories_description[$languages_id] = $_POST['description_' . $languages_id];
        if ($action == 'insert') {
            $blog_id = $next_id;
        } else {
            $blog_id = $post['categorie_id'];
        }
        $categorie2_array = array(
            'categories_name' => xtc_db_prepare_input($gm_url_keywords),
            'categories_heading_title' => xtc_db_prepare_input($gm_url_keywords),
            'categories_description' => xtc_db_prepare_input($categories_description),
            'blog_id' => xtc_db_prepare_input($blog_id)
        );
        if ($action == 'insert') {
            $insert_sql_data = array('categories_id' => $next_id,
                'date' => date('d.m.Y')
            );
            $sql_data_array = xtc_array_merge($sql_categorie_array, $insert_sql_data);
            xtc_db_perform(TABLE_BLOG_CATEGORIES, $sql_data_array);
        } elseif ($action == 'update') {
            $insert_sql_data = array(
                'update_date' => date('d.m.Y')
            );
            $sql_data_array = xtc_array_merge($sql_categorie_array, $insert_sql_data);
            xtc_db_perform(TABLE_BLOG_CATEGORIES, $sql_data_array, 'update', "categories_id = '" . $post['categorie_id'] . "' and language_id = '" . $languages_id . "' ");
        }
    }
    if ($_POST['tm_cat'] == 'false' && $_POST['tmcat'] == 'true') {
        $sql_data_array44 = xtc_array_merge($categorie2_array, $categorie);
        $catfunc->insert_category($sql_data_array44, $_POST['parent_id2']);
    } elseif ($_POST['tm_cat'] == 'true' && $_POST['tmcat'] == 'true') {
        $catidmenue = array(
            'categories_id' => $_POST['tmcatid']
        );
        $sql_data_array44 = xtc_array_merge($categorie2_array, $categorie);
        $sql_data_array444 = xtc_array_merge($sql_data_array44, $catidmenue);
        $catfunc->insert_category($sql_data_array444, $_POST['parent_id2'], 'update');
        $catfunc->move_category($_POST['tmcatid'], $_POST['parent_id2']);
    } else {
        if (isset($_POST['tmcatid'])) {
            $catfunc->remove_category($_POST['tmcatid']);
        }
    }


//Bildergallerie
    if ($action == 'insert') {
        for ($i = 0; $i < PICCAT; $i++) {
            if ($mypicload = xtc_try_upload('ism_image_' . ($i + 1), DIR_FS_CATALOG_IMAGES . 'blog_image/original_images/')) {
                $sql_images_array = array(
                    'cat_id' => xtc_db_prepare_input($next_id),
                    'image_nr' => ($i + 1),
                    'image' => xtc_db_prepare_input($mypicload->filename)
                );
                xtc_db_perform(TABLE_BLOG_CATIMG, $sql_images_array);
                $products_image_name = $mypicload->filename;
                require (DIR_WS_INCLUDES . 'blog_info_images.php');
                require (DIR_WS_INCLUDES . 'blog_popup_images.php');
                require (DIR_WS_INCLUDES . 'blog_thumbnail_images.php');
            }
        }
    } elseif ($action == 'update') {
        for ($i = 0; $i < PICCAT; $i++) {
            if ($mypicload = xtc_try_upload('ism_image_' . ($i + 1), DIR_FS_CATALOG_IMAGES . 'blog_image/original_images/')) {
                xtc_db_query("DELETE from " . TABLE_BLOG_CATIMG . " where cat_id = '" . $_POST['categorie_id'] . "' and image_nr = '" . ($i + 1) . "' ");
                $sql_images_array = array(
                    'cat_id' => xtc_db_prepare_input($_POST['categorie_id']),
                    'image_nr' => ($i + 1),
                    'image' => xtc_db_prepare_input($mypicload->filename)
                );
                xtc_db_perform(TABLE_BLOG_CATIMG, $sql_images_array);
                $products_image_name = $mypicload->filename;
                require (DIR_WS_INCLUDES . 'blog_info_images.php');
                require (DIR_WS_INCLUDES . 'blog_popup_images.php');
                require (DIR_WS_INCLUDES . 'blog_thumbnail_images.php');
            }
        }
        for ($i = 0, $groesse = count($_POST['imageremove']); $i < $groesse; ++$i) {
            xtc_db_query("DELETE from " . TABLE_BLOG_CATIMG . " where cat_id = '" . $_POST['categorie_id'] . "' and image = '" . $_POST['imageremove'][$i] . "' ");
            @xtc_del_image_file($_POST['imageremove'][$i]);
            if (file_exists(DIR_FS_CATALOG_IMAGES . 'blog_image/original_images/' . $_POST['imageremove'][$i])) {
                @ unlink(DIR_FS_CATALOG_IMAGES . 'blog_image/original_images/' . $_POST['imageremove'][$i]);
            }
        }
        xtc_db_query("DELETE from " . TABLE_BLOG_CATIMG . " where image = '' ");
    }
    if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True')
        $commerceSeo->createSeoDBTable();
}

function SetFlag($post, $get) {
    if (isset($_GET['set_item_status']) && $_GET['set_item_status'] != '') {
        $update_array = array('status' => $_GET['set_item_status']);
        xtc_db_perform(TABLE_BLOG_ITEMS, $update_array, 'update', "item_id = '" . $_GET['itemid'] . "'");
    } else {

        $max_elements = count($post['status']);
        // echo $max_elements;
        if ($post['set_cat_status'] == 1 || $post['set_cat_status'] == 2) {
            $update_array = array('status' => $post['set_cat_status']);
            // echo'<pre>';
            // print_r($_POST);

            for ($i = 0; $i < $max_elements; $i++) {
                xtc_db_perform(TABLE_BLOG_CATEGORIES, $update_array, 'update', "categories_id = '" . $post['status'][$i] . "'");
                xtc_db_perform(TABLE_BLOG_ITEMS, $update_array, 'update', "categories_id = '" . $post['status'][$i] . "'");
            }
        } elseif ($_POST['set_cat_status'] == 3) {
            for ($i = 0; $i < $max_elements; $i++) {
                xtc_db_query("DELETE FROM " . TABLE_BLOG_CATEGORIES . " WHERE categories_id = '" . $post['status'][$i] . "'");
                xtc_db_query("DELETE FROM " . TABLE_BLOG_ITEMS . " WHERE categories_id = '" . $post['status'][$i] . "'");
                xtc_db_query("DELETE FROM " . TABLE_BLOG_CATIMG . " WHERE cat_id = '" . $post['status'][$i] . "'");
                $mxcatdelete = xtc_db_fetch_array(xtc_db_query("select categories_id from " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_blogs = '" . $post['status'][$i] . "'"));
                xtc_db_query("DELETE FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . $mxcatdelete['categories_id'] . "'");
                xtc_db_query("DELETE FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_blogs = '" . $post['status'][$i] . "'");
            }
        } elseif ($post['set_item_status'] == 1 || $post['set_item_status'] == 2) {
            $update_array = array('status' => $post['set_item_status']);
            for ($i = 0; $i < $max_elements; $i++)
                xtc_db_perform(TABLE_BLOG_ITEMS, $update_array, 'update', "item_id = '" . $post['status'][$i] . "'");
        } elseif ($_POST['set_item_status'] == 3) {
            for ($i = 0; $i < $max_elements; $i++)
                xtc_db_query("DELETE FROM " . TABLE_BLOG_ITEMS . " WHERE item_id = '" . $post['status'][$i] . "'");
        }
    }
}

function blogItem($post, $action = 'insert') {
    if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
        require_once (DIR_FS_INC . 'commerce_seo.inc.php');
        $commerceSeo = new CommerceSeo();
    }
    $language = xtc_get_languages();
    if ($action == 'insert') {
        $next_query = xtc_db_query("SELECT MAX(item_id) AS ID FROM " . TABLE_BLOG_ITEMS);
        $next_id = xtc_db_fetch_array($next_query);
        $next_id = $next_id['ID'];
        if ($next_id != 0 ? $next_id+=1 : $next_id = 1)
            ;
    }else {
        $next_query9 = xtc_db_query("SELECT MAX(tblock_id) AS ID FROM " . TABLE_BLOG_ITEMTEXT);
        $next_id9 = xtc_db_fetch_array($next_query9);
        $next_id9 = $next_id9['ID'];
        if ($next_id9 != 0 ? $next_id9+=1 : $next_id9 = 1)
            ;
    }
    foreach ($language AS $lang) {
        $group_ids = '';
        if (isset($_POST['groups']))
            foreach ($_POST['groups'] as $b) {
                $group_ids .= 'c_' . $b . "_group ,";
            }
        $customers_statuses_array = xtc_get_customers_statuses();
        if (strstr($group_ids, 'c_all_group')) {
            $group_ids = 'c_all_group,';
            foreach ($customers_statuses_array AS $t_gm_key => $t_gm_value) {
                $group_ids .='c_' . $t_gm_value['id'] . '_group,';
            }
        }
        $sql_item_array = array('language_id' => $lang['id'],
            'categories_id' => xtc_db_prepare_input($post['categories_id']),
            'title' => xtc_db_prepare_input($post['title'][$lang['id']]),
            'name' => xtc_db_prepare_input($post['name'][$lang['id']]),
            'shortdesc' => xtc_db_prepare_input($post['shortdesc_' . $lang['id']]),
            'description' => xtc_db_prepare_input($post['description_' . $lang['id']]),
            'position' => xtc_db_prepare_input($post['position']),
            'date' => xtc_db_prepare_input($post['date']),
            'date_update' => xtc_db_prepare_input($post['date_update']),
            'date_release' => xtc_db_prepare_input($post['date_release']),
            'date_out' => xtc_db_prepare_input($post['date_out']),
            'group_ids' => $group_ids,
            'status' => xtc_db_prepare_input($post['status']),
            'meta_title' => xtc_db_prepare_input($post['meta_title'][$lang['id']]),
            'meta_description' => xtc_db_prepare_input($post['meta_desc'][$lang['id']]),
            'meta_keywords' => xtc_db_prepare_input($post['meta_key'][$lang['id']]),
            'lenght' => xtc_db_prepare_input($post['lenght']));

        if ($action == 'insert') {
            $insert_sql_data = array(
                'item_id' => $next_id,
                'date' => date('d.m.Y'),
                'date2' => date('Y-m-d')
            );
            $sql_data_array = xtc_array_merge($sql_item_array, $insert_sql_data);
            xtc_db_perform(TABLE_BLOG_ITEMS, $sql_data_array, 'insert');
        } elseif ($action == 'update') {
            $insert_sql_data = array('date_update' => date('d.m.Y'));
            $sql_data_array = xtc_array_merge($sql_item_array, $insert_sql_data);
            xtc_db_perform(TABLE_BLOG_ITEMS, $sql_data_array, 'update', "item_id = '" . $post['item'] . "' and language_id = '" . $lang['id'] . "' ");

            if (isset($_POST['mydesc_' . $lang['id']]) && $_POST['mydesc_' . $lang['id']] != '') {
                $text_sql_data = array(
                    'tblock_id' => $next_id9,
                    'item_id' => $post['item'],
                    'language_id' => $lang['id'],
                    'position' => $_POST['tposition'],
                    'description' => $_POST['mydesc_' . $lang['id']]
                );
                xtc_db_perform(TABLE_BLOG_ITEMTEXT, $text_sql_data, 'insert');
            }
            if (isset($_POST['artikel']) && $_POST['artikel'] != '' && $_POST['artikel'] != '0') {
                $artname = xtc_db_fetch_array(xtc_db_query("select products_name from products_description where products_id = " . $post['artikel'] . " and language_id = " . $lang['id'] . "  "));
                $art_sql_data = array(
                    'art_id' => $post['artikel'],
                    'item_id' => $post['item'],
                    'language_id' => $lang['id'],
                    'position' => $_POST['aposition'],
                    'name' => $artname['products_name']
                );
                xtc_db_perform(TABLE_BLOG_ITEMART, $art_sql_data, 'insert');
            }
            if (isset($_POST['blogitem']) && $_POST['blogitem'] != '' && $_POST['blogitem'] != '0') {
                $artname = xtc_db_fetch_array(xtc_db_query("select name from blog_items where item_id = " . $post['blogitem'] . " and language_id = " . $lang['id'] . "  "));
                $art_sql_data = array(
                    'bitem_id' => $post['blogitem'],
                    'item_id' => $post['item'],
                    'language_id' => $lang['id'],
                    'position' => $_POST['biposition'],
                    'name' => $artname['name']
                );
                xtc_db_perform(TABLE_BLOG_ITEMITEM, $art_sql_data, 'insert');
            }
            if (isset($_POST['kategorie']) && $_POST['kategorie'] != '' && $_POST['kategorie'] != '0') {
                $katname = xtc_db_fetch_array(xtc_db_query("select categories_name from categories_description where categories_id = " . $post['kategorie'] . " and language_id = " . $lang['id'] . "  "));
                $kat_sql_data = array(
                    'kat_id' => $post['kategorie'],
                    'item_id' => $post['item'],
                    'language_id' => $lang['id'],
                    'position' => $_POST['kposition'],
                    'name' => $katname['categories_name']
                );
                xtc_db_perform(TABLE_BLOG_ITEMKAT, $kat_sql_data, 'insert');
            }
        }
    }



//Bildergallerie
    $next_query2 = xtc_db_query("SELECT MAX(slid_id) AS ID FROM " . TABLE_BLOG_ITEMIMG);
    $next_id2 = xtc_db_fetch_array($next_query2);
    $next_id2 = $next_id2['ID'];
    if ($next_id2 != 0 ? $next_id2+=1 : $next_id2 = 1)
        ;
    for ($i = 0; $i < PICITEM; $i++) {
        if ($mypicload = xtc_try_upload('ism_image_' . ($i + 1), DIR_FS_CATALOG_IMAGES . 'blog_image/original_images/', '777')) {

            $sql_images_array = array(
                'item_id' => xtc_db_prepare_input($_POST['item']),
                'slid_id' => $next_id2,
                'image_nr' => ($i + 1),
                'width' => xtc_db_prepare_input($_POST['width']),
                'position' => xtc_db_prepare_input($_POST['slposition']),
                'height' => xtc_db_prepare_input($_POST['height']),
                'floating' => xtc_db_prepare_input($_POST['floating']),
                'image' => xtc_db_prepare_input($mypicload->filename)
            );
            xtc_db_perform(TABLE_BLOG_ITEMIMG, $sql_images_array);
            $products_image_name = $mypicload->filename;
            require (DIR_WS_INCLUDES . 'blog_info_images.php');
            require (DIR_WS_INCLUDES . 'blog_popup_images.php');
            require (DIR_WS_INCLUDES . 'blog_thumbnail_images.php');
        }
    }

    xtc_db_query("DELETE from " . TABLE_BLOG_ITEMIMG . " where image = '' ");
    if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True')
        $commerceSeo->createSeoDBTable();
    if ($action == 'insert') {
        xtc_redirect(FILENAME_BLOG . '?action=edit_item&cat=' . $_POST['categories_id'] . '&item=' . $next_id . '');
    } elseif ($action == 'update') {
        xtc_redirect(FILENAME_BLOG . '?action=edit_item&cat=' . $_POST['categories_id'] . '&item=' . $_POST['item'] . '');
    }
}

function blogStart($post, $action = 'update') {
    $language = xtc_get_languages();
    foreach ($language AS $lang) {
        $group_ids = '';
        if (isset($_POST['groups']))
            foreach ($_POST['groups'] as $b) {
                $group_ids .= 'c_' . $b . "_group ,";
            }
        $customers_statuses_array = xtc_get_customers_statuses();
        if (strstr($group_ids, 'c_all_group')) {
            $group_ids = 'c_all_group,';
            foreach ($customers_statuses_array AS $t_gm_key => $t_gm_value) {
                $group_ids .='c_' . $t_gm_value['id'] . '_group,';
            }
        }
        $sql_item_array = array(
            'language_id' => $lang['id'],
            'id' => '1',
            'description' => xtc_db_prepare_input($post['description_' . $lang['id']]),
            'group_ids' => $group_ids,
            'meta_title' => xtc_db_prepare_input($_POST['meta_title_' . $lang['id']]),
            'meta_description' => xtc_db_prepare_input($_POST['meta_description_' . $lang['id']]),
            'meta_keywords' => xtc_db_prepare_input($_POST['meta_keywords_' . $lang['id']])
        );
        if ($action == 'update') {
            $insert_sql_data = array('date' => date('Y-m-d'));
            $sql_data_array = xtc_array_merge($sql_item_array, $insert_sql_data);
            xtc_db_perform(TABLE_BLOG_START, $sql_data_array, 'update', "id = '1' and language_id = '" . $lang['id'] . "' ");
        }
    }
    for ($i = 0; $i < PICSTART; $i++) {
        if ($mypicload = xtc_try_upload('ism_image_' . ($i + 1), DIR_FS_CATALOG_IMAGES . 'blog_image/original_images/', '777')) {
            xtc_db_query("DELETE from " . TABLE_BLOG_STARTIMG . " where start_id = 1 and image_nr = '" . ($i + 1) . "' ");

            $sql_image_array = array(
                'start_id' => '1',
                'image_nr' => ($i + 1),
                'image' => xtc_db_prepare_input($mypicload->filename)
            );
            xtc_db_perform(TABLE_BLOG_STARTIMG, $sql_image_array);
            $products_image_name = $mypicload->filename;
            require (DIR_WS_INCLUDES . 'blog_info_images.php');
            require (DIR_WS_INCLUDES . 'blog_popup_images.php');
            require (DIR_WS_INCLUDES . 'blog_thumbnail_images.php');
        }
    }
    for ($i = 0, $groesse = count($_POST['imageremove']); $i < $groesse; ++$i) {
        xtc_db_query("DELETE from " . TABLE_BLOG_STARTIMG . " where start_id = '1' and image = '" . $_POST['imageremove'][$i] . "' ");
        @xtc_del_image_file($_POST['imageremove'][$i]);
        if (file_exists(DIR_FS_CATALOG_IMAGES . 'blog_image/original_images/' . $_POST['imageremove'][$i])) {
            @ unlink(DIR_FS_CATALOG_IMAGES . 'blog_image/original_images/' . $_POST['imageremove'][$i]);
        }
    }
    xtc_db_query("DELETE from " . TABLE_BLOG_STARTIMG . " where image = '' ");
    xtc_redirect(FILENAME_BLOG . '?action=startsite');
}

function BlogCommentsRval($itemid = '') {
    $blogrval = xtc_db_fetch_array(xtDBquery("SELECT 
										sum(comment_rating) as Rval, count(comment_rating) as total
									FROM 
										blog_comment
									WHERE 
										blog_id = '" . xtc_db_input($itemid) . "' 
									AND 
										comment_status = '1' 
									AND 
										comment_rating != '0';"));
    $blogrevval = $blogrval['Rval'] / $blogrval['total'];
    return $blogrevval;
}
