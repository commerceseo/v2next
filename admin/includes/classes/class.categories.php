<?php

/* -----------------------------------------------------------------
 * 	$Id: class.categories.php 1058 2014-05-18 13:29:18Z akausch $
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

// holds functions for manipulating products & categories
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

if (file_exists(DIR_WS_INCLUDES . 'addons/class_categories_adon.php')) {
    include (DIR_WS_INCLUDES . 'addons/class_categories_adon.php');
}

require_once(DIR_FS_INC . 'cseo_get_url_friendly_text.inc.php');

class categories_ORIGINAL {

    function remove_categories($category_id) {

        $categories = xtc_get_category_tree($category_id, '', '0', '', true);
        $products = array();
        $products_delete = array();

        for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            $product_ids_query = xtc_db_query("SELECT products_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE categories_id = '" . $categories[$i]['id'] . "'");
            while ($product_ids = xtc_db_fetch_array($product_ids_query)) {
                $products[$product_ids['products_id']]['categories'][] = $categories[$i]['id'];
            }
        }

        reset($products);
        while (list ($key, $value) = each($products)) {
            $category_ids = '';
            for ($i = 0, $n = sizeof($value['categories']); $i < $n; $i++) {
                $category_ids .= '\'' . $value['categories'][$i] . '\', ';
            }
            $category_ids = substr($category_ids, 0, -2);

            $check_query = xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = '" . $key . "' AND categories_id NOT IN (" . $category_ids . ")");
            $check = xtc_db_fetch_array($check_query);
            if ($check['total'] < '1') {
                $products_delete[$key] = $key;
            }
        }

        @xtc_set_time_limit(0);
        for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            $this->remove_category($categories[$i]['id']);
        }

        reset($products_delete);
        while (list ($key) = each($products_delete)) {
            $this->remove_product($key);
        }
    }

    function remove_category($category_id) {
        $category_image = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . xtc_db_input($category_id) . "';"));
        $duplicate_image = xtc_db_fetch_array(xtc_db_query("SELECT count(*) AS total FROM " . TABLE_CATEGORIES . " WHERE categories_image = '" . xtc_db_input($category_image['categories_image']) . "';"));

        if ($duplicate_image['total'] < 2) {
            if (file_exists(DIR_FS_CATALOG_IMAGES . 'categories/' . $category_image['categories_image'])) {
                @unlink(DIR_FS_CATALOG_IMAGES . 'categories/' . $category_image['categories_image']);
            }
            if (file_exists(DIR_FS_CATALOG_IMAGES . 'categories_nav/' . $category_image['categories_nav_image'])) {
                @unlink(DIR_FS_CATALOG_IMAGES . 'categories_nav/' . $category_image['categories_nav_image']);
            }
            if (file_exists(DIR_FS_CATALOG_IMAGES . 'categories_footer/' . $category_image['categories_footer_image'])) {
                @unlink(DIR_FS_CATALOG_IMAGES . 'categories_footer/' . $category_image['categories_footer_image']);
            }
        }

        xtc_db_query("DELETE FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . xtc_db_input($category_id) . "'");
        xtc_db_query("DELETE FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = '" . xtc_db_input($category_id) . "'");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE categories_id = '" . xtc_db_input($category_id) . "'");

        if (USE_CACHE == 'true') {
            xtc_reset_cache_block('categories');
            xtc_reset_cache_block('also_purchased');
        }
    }

    function insert_category($categories_data, $dest_category_id, $action = 'insert') {

        $categories_id = xtc_db_prepare_input($categories_data['categories_id']);

        $sort_order = xtc_db_prepare_input($categories_data['sort_order']);
        $categories_status = xtc_db_prepare_input($categories_data['status']);
        $categories_status_main = xtc_db_prepare_input($categories_data['mainstatus']);
        $categories_status_content = xtc_db_prepare_input($categories_data['contentstatus']);

        $customers_statuses_array = xtc_get_customers_statuses();

        $permission = array();
        for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
            if (isset($customers_statuses_array[$i]['id'])) {
                $permission[$customers_statuses_array[$i]['id']] = 0;
            }
        }
        if (isset($categories_data['groups']))
            foreach ($categories_data['groups'] AS $dummy => $b) {
                $permission[$b] = 1;
            }

        if ($permission['all'] == 1) {
            $permission = array();
            end($customers_statuses_array);
            for ($i = 0; $n = key($customers_statuses_array), $i < $n + 1; $i++) {
                if (isset($customers_statuses_array[$i]['id'])) {
                    $permission[$customers_statuses_array[$i]['id']] = 1;
                }
            }
        }

        $permission_array = array();

        end($customers_statuses_array);
        for ($i = 0; $n = key($customers_statuses_array), $i < $n + 1; $i++) {
            if (isset($customers_statuses_array[$i]['id'])) {
                $permission_array = array_merge($permission_array, array('group_permission_' . $customers_statuses_array[$i]['id'] => $permission[$customers_statuses_array[$i]['id']]));
            }
        }

        $sql_data_array = array('sort_order' => $sort_order,
            'categories_status' => $categories_status,
            'categories_main_status' => $categories_status_main,
            'categories_content_status' => $categories_status_content,
            'products_sorting' => xtc_db_prepare_input($categories_data['products_sorting']),
            'products_sorting2' => xtc_db_prepare_input($categories_data['products_sorting2']),
            'section' => xtc_db_prepare_input($categories_data['section']),
            'categories_template' => xtc_db_prepare_input($categories_data['categories_template']),
            'categories_col_top' => xtc_db_prepare_input($categories_data['categories_col_top']),
            'categories_col_bottom' => xtc_db_prepare_input($categories_data['categories_col_bottom']),
            'categories_col_left' => xtc_db_prepare_input($categories_data['categories_col_left']),
            'categories_col_right' => xtc_db_prepare_input($categories_data['categories_col_right']),
            'listing_template' => xtc_db_prepare_input($categories_data['listing_template']));

        if (function_exists(add_cat_fields)) {
            $sql_data_array = add_cat_fields($sql_data_array, $categories_data);
        }
        if (trim(ADD_CATEGORIES_FIELDS) != '') {
            $sql_data_array = array_merge($sql_data_array, $this->add_data_fields(ADD_CATEGORIES_FIELDS, $categories_data));
        }
        $sql_data_array = array_merge($sql_data_array, $permission_array);

        if ($action == 'insert') {
            $insert_sql_data = array('parent_id' => $dest_category_id, 'date_added' => 'now()');
            $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
            xtc_db_perform(TABLE_CATEGORIES, $sql_data_array);
            $categories_id = xtc_db_insert_id();
        } elseif ($action == 'update') {
            $update_sql_data = array('last_modified' => 'now()');
            $sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
            xtc_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\'');
        }
        xtc_set_groups($categories_id, $permission_array);

        $languages = xtc_get_languages();
        foreach ($languages AS $lang) {
            $categories_name_array = $categories_data['name'];

            if (!empty($categories_data['categories_url_alias'][$lang['id']])) {
                $url_alias = cseo_get_url_friendly_text($categories_data['categories_url_alias'][$lang['id']]);
            }

            $sql_data_array = array('categories_name' => xtc_db_prepare_input($categories_data['categories_name'][$lang['id']]),
                'categories_heading_title' => xtc_db_prepare_input($categories_data['categories_heading_title'][$lang['id']]),
                'categories_google_taxonomie' => xtc_db_prepare_input($categories_data['categories_google_taxonomie'][$lang['id']]),
                'categories_description' => xtc_db_prepare_input($categories_data['categories_description'][$lang['id']]),
                'categories_short_description' => xtc_db_prepare_input($categories_data['categories_short_description'][$lang['id']]),
				'categories_contents' => xtc_db_prepare_input($categories_data['content_id']),
				'categories_blogs' => xtc_db_prepare_input($categories_data['blog_id']),
                'categories_description_footer' => xtc_db_prepare_input($categories_data['categories_description_footer'][$lang['id']]),
                'slider_set' => xtc_db_prepare_input($categories_data['slider_set'][$lang['id']]),
                'categories_pic_alt' => xtc_db_prepare_input($categories_data['categories_pic_alt'][$lang['id']]),
                'categories_pic_footer_alt' => xtc_db_prepare_input($categories_data['categories_pic_footer_alt'][$lang['id']]),
                'categories_pic_nav_alt' => xtc_db_prepare_input($categories_data['categories_pic_nav_alt'][$lang['id']]),
                'categories_meta_title' => xtc_db_prepare_input($categories_data['categories_meta_title'][$lang['id']]),
                'categories_meta_description' => xtc_db_prepare_input($categories_data['categories_meta_description'][$lang['id']]),
                'categories_meta_keywords' => xtc_db_prepare_input($categories_data['categories_meta_keywords'][$lang['id']]),
                'categories_url_alias' => xtc_db_prepare_input($url_alias));

            if (function_exists(add_cat_desc_fields)) {
                $sql_data_array = add_cat_desc_fields($sql_data_array, $categories_data, $lang['id']);
            }

            if (trim(ADD_CATEGORIES_DESCRIPTION_FIELDS) != '') {
                $sql_data_array = array_merge($sql_data_array, $this->add_data_fields(ADD_CATEGORIES_DESCRIPTION_FIELDS, $categories_data, $lang['id']));
            }

            if ($action == 'insert') {
                $insert_sql_data = array('categories_id' => $categories_id, 'language_id' => $lang['id']);
                $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
                xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
            } elseif ($action == 'update') {
                xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\' and language_id = \'' . $lang['id'] . '\'');
            }
        }

        if ($categories_image = xtc_try_upload('categories_image', DIR_FS_CATALOG_IMAGES . 'categories_org/')) {
            $cname_arr = explode('.', $categories_image->filename);
            $cnsuffix = array_pop($cname_arr);

            if (IMAGE_NAME_CATEGORIE == 'c_id') {
                $new_categories_name = $categories_id;
            } elseif (IMAGE_NAME_CATEGORIE == 'c_name') {
                $new_categories_name = cseo_get_url_friendly_text(xtc_db_prepare_input($categories_data['categories_name'][(int) $_SESSION['languages_id']])).'-'.$categories_id;
            } else {
                $new_categories_name = cseo_get_url_friendly_text($cname_arr[0]).'-'.$categories_id;
            }

            $categories_image_name = $new_categories_name . '.' . $cnsuffix;
            @unlink(DIR_FS_CATALOG_IMAGES . 'categories_org/' . $categories_image_name);
            rename(DIR_FS_CATALOG_IMAGES . 'categories_org/' . $categories_image->filename, DIR_FS_CATALOG_IMAGES . 'categories_org/' . $categories_image_name);
            xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET categories_image = '" . xtc_db_input($categories_image_name) . "' WHERE categories_id = '" . (int) $categories_id . "'");

            require (DIR_WS_INCLUDES . 'category_image.php');
            require (DIR_WS_INCLUDES . 'category_image_info.php');
        }

        if ($categories_data['del_cat_pic'] == 'yes') {
            @unlink(DIR_FS_CATALOG_IMAGES . 'categories_org/' . $categories_data['categories_previous_image']);
            @unlink(DIR_FS_CATALOG_IMAGES . 'categories/' . $categories_data['categories_previous_image']);
            @unlink(DIR_FS_CATALOG_IMAGES . 'categories_info/' . $categories_data['categories_previous_image']);
            xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET categories_image = '' WHERE categories_id    = '" . (int) $categories_id . "'");
        }

        // Neue Nav Images fuer Kategorie
        if ($categories_image = xtc_try_upload('categories_nav_image', DIR_FS_CATALOG_IMAGES . 'categories_nav/')) {
            $cname_arr = explode('.', $categories_image->filename);
            $cnsuffix = array_pop($cname_arr);
            if (IMAGE_NAME_CATEGORIE == 'c_id')
                $new_categories_name = $categories_id;
            elseif (IMAGE_NAME_CATEGORIE == 'c_name')
                $new_categories_name = cseo_get_url_friendly_text(xtc_db_prepare_input($categories_data['categories_name'][(int) $_SESSION['languages_id']])).'-'.$categories_id;
            else
                $new_categories_name = cseo_get_url_friendly_text($cname_arr[0]).'-'.$categories_id;

            $categories_image_name = $new_categories_name . '.' . $cnsuffix;
            // @unlink(DIR_FS_CATALOG_IMAGES . 'categories_nav/' . $categories_image_name);
            rename(DIR_FS_CATALOG_IMAGES . 'categories_nav/' . $categories_image->filename, DIR_FS_CATALOG_IMAGES . 'categories_nav/' . $categories_image_name);
            xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET categories_nav_image = '" . xtc_db_input($categories_image_name) . "' WHERE categories_id = '" . (int) $categories_id . "';");
        }

        if ($categories_data['del_cat_pic_nav'] == 'yes') {
            @unlink(DIR_FS_CATALOG_IMAGES . 'categories_nav/' . $categories_data['categories_nav_previous_image']);
            xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET categories_nav_image = '' WHERE categories_id    = '" . (int) $categories_id . "';");
        }

        // Neue Footer Images fuer Kategorie
        if ($categories_image = xtc_try_upload('categories_footer_image', DIR_FS_CATALOG_IMAGES . 'categories_footer/')) {
            $cname_arr = explode('.', $categories_image->filename);
            $cnsuffix = array_pop($cname_arr);
            if (IMAGE_NAME_CATEGORIE == 'c_id')
                $new_categories_name = $categories_id;
            elseif (IMAGE_NAME_CATEGORIE == 'c_name')
                $new_categories_name = cseo_get_url_friendly_text(xtc_db_prepare_input($categories_data['categories_name'][(int) $_SESSION['languages_id']])).'-'.$categories_id;
            else
                $new_categories_name = cseo_get_url_friendly_text($cname_arr[0]).'-'.$categories_id;

            $categories_image_name = $new_categories_name . '.' . $cnsuffix;
            @unlink(DIR_FS_CATALOG_IMAGES . 'categories_footer/' . $categories_image_name);
            rename(DIR_FS_CATALOG_IMAGES . 'categories_footer/' . $categories_image->filename, DIR_FS_CATALOG_IMAGES . 'categories_footer/' . $categories_image_name);
            xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET categories_footer_image = '" . xtc_db_input($categories_image_name) . "' WHERE categories_id = '" . (int) $categories_id . "';");
        }

        if ($categories_data['del_cat_pic_footer'] == 'yes') {
            @unlink(DIR_FS_CATALOG_IMAGES . 'categories_footer/' . $categories_data['categories_footer_previous_image']);
            xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET categories_footer_image = '' WHERE categories_id    = '" . (int) $categories_id . "';");
        }

        // Neue Promotion Images fuer Kategorie
        if ($categories_image = xtc_try_upload('categories_promo_image_1', DIR_FS_CATALOG_IMAGES . 'categories_promotion/')) {
            $cname_arr = explode('.', $categories_image->filename);
            $cnsuffix = array_pop($cname_arr);
            if (IMAGE_NAME_CATEGORIE == 'c_id')
                $new_categories_name = $categories_id;
            elseif (IMAGE_NAME_CATEGORIE == 'c_name')
                $new_categories_name = cseo_get_url_friendly_text(xtc_db_prepare_input($categories_data['categories_name'][(int) $_SESSION['languages_id']])).'-'.$categories_id;
            else
                $new_categories_name = cseo_get_url_friendly_text($cname_arr[0]).'-'.$categories_id;

            $categories_image_name = $new_categories_name . '.' . $cnsuffix;
            @unlink(DIR_FS_CATALOG_IMAGES . 'categories_promotion/' . $categories_image_name);
            rename(DIR_FS_CATALOG_IMAGES . 'categories_promotion/' . $categories_image->filename, DIR_FS_CATALOG_IMAGES . 'categories_promotion/' . $categories_image_name);
            xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET categories_promo_image_1 = '" . xtc_db_input($categories_image_name) . "' WHERE categories_id = '" . (int) $categories_id . "';");
        }

        if ($categories_data['del_categories_promo_image_1'] == 'yes') {
            @unlink(DIR_FS_CATALOG_IMAGES . 'categories_promotion/' . $categories_data['previous_categories_promo_image_1']);
            xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET categories_promo_image_1 = '' WHERE categories_id    = '" . (int) $categories_id . "';");
        }
    }

    function set_category_recursive($categories_id, $status = "0") {
        xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET categories_status = '" . $status . "' WHERE categories_id = '" . $categories_id . "';");
        $categories_query = xtc_db_query("SELECT categories_id FROM " . TABLE_CATEGORIES . " WHERE parent_id='" . $categories_id . "';");
        while ($categories = xtc_db_fetch_array($categories_query)) {
            $this->set_category_recursive($categories['categories_id'], $status);
        }
    }

    function set_category_product_recursive($categories_id, $status = "0") {
        xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET categories_status = '" . $status . "' WHERE categories_id = '" . $categories_id . "';");
        $products2cat_query = xtc_db_query("SELECT products_id FROM products_to_categories WHERE categories_id = '" . $categories_id . "';");
        while ($products2cat = xtc_db_fetch_array($products2cat_query)) {
            xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_status = '" . $status . "' WHERE products_id = '" . $products2cat['products_id'] . "';");
        }
        $categories_query = xtc_db_query("SELECT categories_id FROM " . TABLE_CATEGORIES . " WHERE parent_id='" . $categories_id . "';");
        while ($categories = xtc_db_fetch_array($categories_query)) {
            $this->set_category_recursive($categories['categories_id'], $status);
        }
    }

    function move_category($src_category_id, $dest_category_id) {
        $src_category_id = xtc_db_prepare_input($src_category_id);
        $dest_category_id = xtc_db_prepare_input($dest_category_id);
        xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET parent_id = '" . xtc_db_input($dest_category_id) . "', last_modified = now() WHERE categories_id = '" . xtc_db_input($src_category_id) . "';");
    }

    function copy_category($src_category_id, $dest_category_id, $ctype = "link") {

        if (!(in_array($src_category_id, $_SESSION['copied']))) {
            $src_category_id = xtc_db_prepare_input($src_category_id);
            $dest_category_id = xtc_db_prepare_input($dest_category_id);
            $ccopy_values = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . $src_category_id . "';"));
			//alles holen
			if (is_array($ccopy_values)) {
				foreach ($ccopy_values AS $field => $value) {
					$base_sql_data_array[$field] = $value;
				}
			}
			//die sollen neu gesetzt werden
            $sql_data_array = array('parent_id' => xtc_db_input($dest_category_id),
									'categories_id' => 'NULL',
									'date_added' => 'NOW()',
									'last_modified' => 'NOW()');
			//zusammen bringen
			$sql_data_array = array_merge($base_sql_data_array, $sql_data_array);
	
            $customers_statuses_array = xtc_get_customers_statuses();

            for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
                if (isset($customers_statuses_array[$i]['id'])) {
                    $sql_data_array = array_merge($sql_data_array, array('group_permission_' . $customers_statuses_array[$i]['id'] => $product['group_permission_' . $customers_statuses_array[$i]['id']]));
                }
            }

            xtc_db_perform(TABLE_CATEGORIES, $sql_data_array);

            $new_cat_id = xtc_db_insert_id();
            $_SESSION['copied'][] = $new_cat_id;
            $get_prod_query = xtc_db_query("SELECT products_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE categories_id = '" . $src_category_id . "';");

            while ($product = xtc_db_fetch_array($get_prod_query)) {
                if ($ctype == 'link') {
                    $this->link_product($product['products_id'], $new_cat_id);
                } elseif ($ctype == 'duplicate') {
                    $this->duplicate_product($product['products_id'], $new_cat_id);
                } else {
                    die('Undefined copy type!');
                }
            }

            $src_pic = DIR_FS_CATALOG_IMAGES . 'categories/' . $ccopy_values['categories_image'];
            if (is_file($src_pic)) {
                $get_suffix = explode('.', $ccopy_values['categories_image']);
                $suffix = array_pop($get_suffix);
                $dest_pic = $new_cat_id . '.' . $suffix;
                @ copy($src_pic, DIR_FS_CATALOG_IMAGES . 'categories/' . $dest_pic);
                xtc_db_query("UPDATE categories SET categories_image = '" . $dest_pic . "' WHERE categories_id = '" . $new_cat_id . "';");
            }
			
			$languages = xtc_get_languages();
			foreach ($languages AS $lang) {
				$cdcopy_query = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = '" . $src_category_id . "' AND language_id = '".$lang['id']."';"));
				//alles holen
				if (is_array($cdcopy_query)) {
					foreach ($cdcopy_query AS $field => $value) {
						$base_desc_sql_data_array[$field] = $value;
					}
				}
				//die sollen neu gesetzt werden
				$sql_desc_data_array = array('categories_id' => $new_cat_id,
											'language_id' => $cdcopy_query['language_id']);
				//zusammen bringen
				$sql_desc_data_array = array_merge($base_desc_sql_data_array, $sql_desc_data_array);
				
				xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_desc_data_array);
			}
			
            $crcopy_query = xtc_db_query("SELECT categories_id FROM " . TABLE_CATEGORIES . " WHERE parent_id = '" . $src_category_id . "';");

            while ($crcopy_values = xtc_db_fetch_array($crcopy_query)) {
                $this->copy_category($crcopy_values['categories_id'], $new_cat_id, $ctype);
            }
        }
    }

    function remove_product($product_id) {
        $product_content_query = xtc_db_query("SELECT content_file FROM " . TABLE_PRODUCTS_CONTENT . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
        while ($product_content = xtc_db_fetch_array($product_content_query)) {
            $duplicate_content = xtc_db_fetch_array(xtc_db_query("SELECT count(*) AS total FROM " . TABLE_PRODUCTS_CONTENT . " WHERE content_file = '" . xtc_db_input($product_content['content_file']) . "' AND products_id != '" . xtc_db_input($product_id) . "';"));
            if ($duplicate_content['total'] == 0) {
                @unlink(DIR_FS_DOCUMENT_ROOT . 'media/products/' . $product_content['content_file']);
            }
            xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_CONTENT . " WHERE products_id = '" . xtc_db_input($product_id) . "' AND (content_file = '" . $product_content['content_file'] . "' OR content_file = '');");
        }

        $product_image = xtc_db_fetch_array(xtc_db_query("SELECT products_image FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . xtc_db_input($product_id) . "';"));

        $duplicate_image = xtc_db_fetch_array(xtc_db_query("SELECT count(*) AS total FROM " . TABLE_PRODUCTS . " WHERE products_image = '" . xtc_db_input($product_image['products_image']) . "';"));

        if ($duplicate_image['total'] < 2) {
            xtc_del_image_file($product_image['products_image']);
        }

        $mo_images_query = xtc_db_query("SELECT image_name FROM " . TABLE_PRODUCTS_IMAGES . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
        while ($mo_images_values = xtc_db_fetch_array($mo_images_query)) {
            $duplicate_more_image = xtc_db_fetch_array(xtc_db_query("SELECT count(*) AS total FROM " . TABLE_PRODUCTS_IMAGES . " WHERE image_name = '" . $mo_images_values['image_name'] . "';"));
            if ($duplicate_more_image['total'] < 2) {
                xtc_del_image_file($mo_images_values['image_name']);
            }
        }

        xtc_db_query("DELETE FROM " . TABLE_SPECIALS . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_XSELL . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_IMAGES . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . ", " . TABLE_PRODUCTS_PARAMETERS . " USING " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . ", " . TABLE_PRODUCTS_PARAMETERS . " WHERE " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . ".parameters_id = " . TABLE_PRODUCTS_PARAMETERS . ".parameters_id AND " . TABLE_PRODUCTS_PARAMETERS . ".products_id = '" . xtc_db_input($product_id) . "';");
        xtc_db_query("DELETE FROM " . TABLE_ACCESSORIES_PRODUCTS . ", " . TABLE_ACCESSORIES . " USING " . TABLE_ACCESSORIES_PRODUCTS . ", " . TABLE_ACCESSORIES . " WHERE " . TABLE_ACCESSORIES_PRODUCTS . ".accessories_id = " . TABLE_ACCESSORIES . ".id AND " . TABLE_ACCESSORIES_PRODUCTS . ".product_id = '" . xtc_db_input($product_id) . "';");
        xtc_db_query("DELETE FROM " . TABLE_TAG_TO_PRODUCT . " WHERE pID = '" . xtc_db_input($product_id) . "';");
		if (table_exists('partsToKfz') == true) {
			xtc_db_query("DELETE FROM partsToKfz WHERE products_id = '".xtc_db_input($product_id)."'");
		}
        $customers_statuses_array = xtc_get_customers_statuses();
        for ($i = 0, $n = sizeof($customers_statuses_array); $i < $n; $i ++) {
            if (isset($customers_statuses_array[$i]['id'])) {
                xtc_db_query("DELETE FROM personal_offers_by_customers_status_" . $customers_statuses_array[$i]['id'] . " WHERE products_id = '" . xtc_db_input($product_id) . "';");
                xtc_db_query("DELETE FROM personal_offers_by_customers_status_" . $customers_statuses_array[$i]['id'] . " WHERE products_id = 0;");
            }
        }

        $product_reviews_query = xtc_db_query("select reviews_id from " . TABLE_REVIEWS . " where products_id = '" . xtc_db_input($product_id) . "'");
        while ($product_reviews = xtc_db_fetch_array($product_reviews_query)) {
            xtc_db_query("DELETE FROM " . TABLE_REVIEWS_DESCRIPTION . " WHERE reviews_id = '" . $product_reviews['reviews_id'] . "';");
        }

        xtc_db_query("DELETE FROM " . TABLE_REVIEWS . " WHERE products_id = '" . xtc_db_input($product_id) . "';");

        if (USE_CACHE == 'true') {
            xtc_reset_cache_block('categories');
            xtc_reset_cache_block('also_purchased');
        }
    }

    function delete_product($product_id, $product_categories) {
        for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
            xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id   = '" . xtc_db_input($product_id) . "' AND categories_id = '" . xtc_db_input($product_categories[$i]) . "';");
            if (($product_categories[$i]) == 0) {
                $this->set_product_startpage($product_id, 0);
            }
        }

        $product_categories = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = '" . xtc_db_input($product_id) . "';"));
        if ($product_categories['total'] == '0') {
            $this->remove_product($product_id);
        }
    }

    function insert_product($products_data, $dest_category_id, $action = 'insert') {
        $products_id = xtc_db_prepare_input($products_data['products_id']);
        $products_date_available = xtc_db_prepare_input($products_data['products_date_available']);
        $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';

        if ($products_data['products_startpage'] == 1) {
            $this->link_product($products_data['products_id'], 0);
            $products_status = 1;
        } else {
            $products_status = xtc_db_prepare_input($products_data['products_status']);
        }

        if ($products_data['products_startpage'] == 0) {
            $this->set_product_remove_startpage_sql($products_data['products_id'], 0);
            $products_status = xtc_db_prepare_input($products_data['products_status']);
        }

        $products_data['products_price'] = str_replace(',', '.', $products_data['products_price']);
        if (PRICE_IS_BRUTTO == 'true' && $products_data['products_price']) {
            $products_data['products_price'] = round(($products_data['products_price'] / (xtc_get_tax_rate($products_data['products_tax_class_id']) + 100) * 100), PRICE_PRECISION);
        }
        $products_data['products_ekpprice'] = str_replace(',', '.', $products_data['products_ekpprice']);
        if (PRICE_IS_BRUTTO == 'true' && $products_data['products_ekpprice']) {
            $products_data['products_ekpprice'] = round(($products_data['products_ekpprice'] / (xtc_get_tax_rate($products_data['products_tax_class_id']) + 100) * 100), PRICE_PRECISION);
        }
        $products_data['products_uvpprice'] = str_replace(',', '.', $products_data['products_uvpprice']);
        if (PRICE_IS_BRUTTO == 'true' && $products_data['products_uvpprice']) {
            $products_data['products_uvpprice'] = round(($products_data['products_uvpprice'] / (xtc_get_tax_rate($products_data['products_tax_class_id']) + 100) * 100), PRICE_PRECISION);
        }

        $customers_statuses_array = xtc_get_customers_statuses();

        $permission = array();
        for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
            if (isset($customers_statuses_array[$i]['id'])) {
                $permission[$customers_statuses_array[$i]['id']] = 0;
            }
        }
        if (isset($products_data['groups'])) {
            foreach ($products_data['groups'] AS $dummy => $b) {
                $permission[$b] = 1;
            }
        }
        if ($permission['all'] == 1) {
            $permission = array();
            end($customers_statuses_array);
            for ($i = 0; $n = key($customers_statuses_array), $i < $n + 1; $i++) {
                if (isset($customers_statuses_array[$i]['id'])) {
                    $permission[$customers_statuses_array[$i]['id']] = 1;
                }
            }
        }

        $permission_array = array();
        end($customers_statuses_array);
        for ($i = 0; $n = key($customers_statuses_array), $i < $n + 1; $i++) {
            if (isset($customers_statuses_array[$i]['id'])) {
                $permission_array = array_merge($permission_array, array('group_permission_' . $customers_statuses_array[$i]['id'] => $permission[$customers_statuses_array[$i]['id']]));
            }
        }

        if (isset($products_data['forbidden_payment']) && !empty($products_data['forbidden_payment'])) {
            $forbidden_payment = implode('|', $products_data['forbidden_payment']);
        }
        if (isset($products_data['forbidden_shipping']) && !empty($products_data['forbidden_shipping'])) {
            $forbidden_shipping = implode('|', $products_data['forbidden_shipping']);
        }

        if (!empty($products_data['filter'])) {
            xtc_db_query("DELETE FROM products_to_filter WHERE products_id = '" . $products_id . "'");
            foreach ($products_data['filter'] AS $filter) {
                xtc_db_perform('products_to_filter', array('products_id' => $products_id, 'filter_id' => $filter));
            }
        }

        $sql_data_array = array('products_quantity' => xtc_db_prepare_input($products_data['products_quantity']),
            'products_model' => xtc_db_prepare_input($products_data['products_model']),
            'products_manufacturers_model' => xtc_db_prepare_input($products_data['products_manufacturers_model']),
            'products_ean' => xtc_db_prepare_input($products_data['products_ean']),
            'products_price' => xtc_db_prepare_input($products_data['products_price']),
            'products_sort' => xtc_db_prepare_input($products_data['products_sort']),
            'products_shippingtime' => xtc_db_prepare_input($products_data['shipping_status']),
            'products_discount_allowed' => xtc_db_prepare_input($products_data['products_discount_allowed']),
            'products_date_available' => $products_date_available,
            'products_weight' => xtc_db_prepare_input($products_data['products_weight']),
            'products_status' => $products_status,
            'products_startpage' => xtc_db_prepare_input($products_data['products_startpage']),
            'products_startpage_sort' => xtc_db_prepare_input($products_data['products_startpage_sort']),
            'products_cartspecial' => xtc_db_prepare_input($products_data['products_cartspecial']),
            'products_buyable' => xtc_db_prepare_input($products_data['products_buyable']),
            'products_only_request' => xtc_db_prepare_input($products_data['products_only_request']),
            'products_rel' => xtc_db_prepare_input($products_data['products_rel']),
            'products_treepodia_activate' => xtc_db_prepare_input($products_data['products_treepodia_activate']),
            'products_tax_class_id' => xtc_db_prepare_input($products_data['products_tax_class_id']),
            'product_template' => xtc_db_prepare_input($products_data['info_template']),
            'options_template' => xtc_db_prepare_input($products_data['options_template']),
            'manufacturers_id' => xtc_db_prepare_input($products_data['manufacturers_id']),
            'products_fsk18' => xtc_db_prepare_input($products_data['fsk18']),
            'products_movie_embeded_code' => xtc_db_prepare_input($products_data['products_movie_embeded_code']),
            'products_movie_width' => xtc_db_prepare_input($products_data['products_movie_width']),
            'products_movie_height' => xtc_db_prepare_input($products_data['products_movie_height']),
            'products_movie_youtube_id' => xtc_db_prepare_input($products_data['products_movie_youtube_id']),
            'products_forbidden_payment' => $forbidden_payment,
			'products_forbidden_shipping' => $forbidden_shipping,
            'products_col_left' => xtc_db_prepare_input($products_data['products_col_left']),
            'products_col_right' => xtc_db_prepare_input($products_data['products_col_right']),
            'products_col_top' => xtc_db_prepare_input($products_data['products_col_top']),
            'products_col_bottom' => xtc_db_prepare_input($products_data['products_col_bottom']),
            'products_vpe_value' => xtc_db_prepare_input($products_data['products_vpe_value']),
            'products_vpe_status' => xtc_db_prepare_input($products_data['products_vpe_status']),
            'products_vpe' => xtc_db_prepare_input($products_data['products_vpe']),
            'products_promotion_status' => xtc_db_prepare_input($products_data['products_promotion_status']),
            'products_promotion_product_title' => xtc_db_prepare_input($products_data['products_promotion_product_title']),
            'products_promotion_product_desc' => xtc_db_prepare_input($products_data['products_promotion_product_desc']),
            'products_ekpprice' => xtc_db_prepare_input($products_data['products_ekpprice']),
            'products_uvpprice' => xtc_db_prepare_input($products_data['products_uvpprice']),
            'products_master' => xtc_db_prepare_input($products_data['products_master']),
            'products_master_article' => xtc_db_prepare_input($products_data['products_master_article']),
            'products_slave_in_list' => xtc_db_prepare_input($products_data['products_slave_in_list']),
            'products_shipping_costs' => xtc_db_prepare_input($products_data['products_shipping_costs']),
            'products_minorder' => xtc_db_prepare_input($products_data['products_minorder']),
            'products_maxorder' => xtc_db_prepare_input($products_data['products_maxorder']),
            'products_zustand' => xtc_db_prepare_input($products_data['products_zustand']),
            'products_google_gender' => xtc_db_prepare_input($products_data['products_google_gender']),
            'products_google_age_group' => xtc_db_prepare_input($products_data['products_google_age_group']),
            'products_google_color' => xtc_db_prepare_input($products_data['products_google_color']),
            'products_google_size' => xtc_db_prepare_input($products_data['products_google_size']),
            'products_isbn' => xtc_db_prepare_input($products_data['products_isbn']),
            'products_upc' => xtc_db_prepare_input($products_data['products_upc']),
            'products_g_identifier' => xtc_db_prepare_input($products_data['products_g_identifier']),
            'products_brand_name' => xtc_db_prepare_input($products_data['products_brand_name']),
            'products_g_availability' => xtc_db_prepare_input($products_data['products_g_availability']),
            'products_g_shipping_status' => xtc_db_prepare_input($products_data['products_g_shipping_status']),
            'product_type' => xtc_db_prepare_input($products_data['product_type']),
            'products_sperrgut' => xtc_db_prepare_input($products_data['products_sperrgut']));
			
		if (table_exists('kfz') == true) {
			$sql_data_array_kfz = array('partNr'=>xtc_db_prepare_input($products_data['partNr']));
			$sql_data_array = array_merge($sql_data_array, $sql_data_array_kfz);
			$res=xtc_db_query('SELECT * FROM partNrFields;');
			$fields=array();
			if(xtc_db_num_rows($res)>0){
				while($r=xtc_db_fetch_array($res)){
					$name='partNr_'.$r['id'];
					$sql_data_array[$name]=xtc_db_prepare_input($products_data[$name]);
				}
			}
		}

        if (function_exists(add_product_fields)) {
            $sql_data_array = add_product_fields($sql_data_array, $products_data);
        }

        if (trim(ADD_PRODUCTS_FIELDS) != '') {
            $sql_data_array = array_merge($sql_data_array, $this->add_data_fields(ADD_PRODUCTS_FIELDS, $products_data));
        }

        $sql_data_array = array_merge($sql_data_array, $permission_array);

        if (!$products_id || $products_id == '') {
            $new_pid_query = xtc_db_query("SHOW TABLE STATUS LIKE '" . TABLE_PRODUCTS . "'");
            $new_pid_query_values = xtc_db_fetch_array($new_pid_query);
            $products_id = $new_pid_query_values['Auto_increment'];
        }

        if ($products_movie_on_server = xtc_try_upload('products_movie_on_server', DIR_FS_CATALOG_MOVIES, '777', '')) {
            $pname_arr = explode('.', $products_movie_on_server->filename);
            $nsuffix = array_pop($pname_arr);

            if (IMAGE_NAME_PRODUCT == 'p_id') {
                $new_movie_name = $products_id;
            } elseif (IMAGE_NAME_PRODUCT == 'p_name') {
                $new_movie_name = cseo_get_url_friendly_text(xtc_db_prepare_input($products_data['products_name'][(int) $_SESSION['languages_id']]));
            } else {
                $new_movie_name = cseo_get_url_friendly_text($pname_arr[0]);
            }

            $products_movies_name = $new_movie_name . '.' . strtolower($nsuffix);
            $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_PRODUCTS . " WHERE products_movie_on_server = '" . $products_data['products_previous_movies'] . "'");

            $dup_check = xtc_db_fetch_array($dup_check_query);

            if ($dup_check['total'] < 2)
                @xtc_del_image_file($products_data['products_previous_movies']);

            $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_PRODUCTS . " WHERE products_movie_on_server = '" . $products_movie_on_server->filename . "'");

            $dup_check = xtc_db_fetch_array($dup_check_query);

            if ($dup_check['total'] == 0) {
                if (rename(DIR_FS_CATALOG_MOVIES . $products_movie_on_server->filename, DIR_FS_CATALOG_MOVIES . $products_movies_name))
                    $sql_data_array['products_movie_on_server'] = xtc_db_prepare_input($products_movies_name);
            } else {
                if (copy(DIR_FS_CATALOG_MOVIES . $products_movie_on_server->filename, DIR_FS_CATALOG_MOVIES . $products_movies_name))
                    $sql_data_array['products_movie_on_server'] = xtc_db_prepare_input($products_movies_name);
            }
        } else {
            $products_movies_name = $products_data['products_previous_movies'];
        }

        if ($products_data['del_movie'] != '') {
            $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_PRODUCTS . " WHERE products_movie_on_server = '" . $products_data['del_movie'] . "'");
            $dup_check = xtc_db_fetch_array($dup_check_query);
            if ($dup_check['total'] < 2)
                @ xtc_del_movie_file($products_data['del_movie']);
            xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_movie_on_server = '' WHERE products_id = '" . xtc_db_input($products_id) . "'");
        }

        if ($products_image = xtc_try_upload('products_image', DIR_FS_CATALOG_ORIGINAL_IMAGES, '777', '')) {
            $pname_arr = explode('.', $products_image->filename);
            $nsuffix = array_pop($pname_arr);

            if (IMAGE_NAME_PRODUCT == 'p_id') {
                $products_image_name = $products_id . '.' . strtolower($nsuffix);
            } elseif (IMAGE_NAME_PRODUCT == 'p_name') {
                $products_image_name = cseo_get_url_friendly_text(xtc_db_prepare_input($products_data['products_name'][(int) $_SESSION['languages_id']])) . '_' . $products_id . '.' . strtolower($nsuffix);
            } else {
                $products_image_name = cseo_get_url_friendly_text($pname_arr[0]) . '.' . strtolower($nsuffix);
            }

            $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_PRODUCTS . " WHERE products_image = '" . $products_data['products_previous_image_0'] . "'");
            $dup_check = xtc_db_fetch_array($dup_check_query);
            if ($dup_check['total'] < 2)
                @xtc_del_image_file($products_data['products_previous_image_0']);

            $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_PRODUCTS . " WHERE products_image = '" . $products_image->filename . "'");
            $dup_check = xtc_db_fetch_array($dup_check_query);
            if ($dup_check['total'] == 0) {
                rename(DIR_FS_CATALOG_ORIGINAL_IMAGES . $products_image->filename, DIR_FS_CATALOG_ORIGINAL_IMAGES . $products_image_name);
            } else {
                copy(DIR_FS_CATALOG_ORIGINAL_IMAGES . $products_image->filename, DIR_FS_CATALOG_ORIGINAL_IMAGES . $products_image_name);
            }
            $sql_data_array['products_image'] = xtc_db_prepare_input($products_image_name);
            require (DIR_WS_INCLUDES . 'product_thumbnail_images.php');
            require (DIR_WS_INCLUDES . 'product_info_images.php');
            require (DIR_WS_INCLUDES . 'product_popup_images.php');
            require (DIR_WS_INCLUDES . 'product_mini_images.php');
        } else
            $products_image_name = $products_data['products_previous_image_0'];

        if ($products_data['del_pic'] != '') {
            $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_PRODUCTS . " WHERE products_image = '" . $products_data['del_pic'] . "'");
            $dup_check = xtc_db_fetch_array($dup_check_query);
            if ($dup_check['total'] < 2)
                @ xtc_del_image_file($products_data['del_pic']);
            xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_image = '' WHERE products_id    = '" . xtc_db_input($products_id) . "'");
        }

        if ($products_data['del_mo_pic'] != '') {
            foreach ($products_data['del_mo_pic'] AS $dummy => $val) {
                $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_PRODUCTS_IMAGES . " WHERE image_name = '" . $val . "'");
                $dup_check = xtc_db_fetch_array($dup_check_query);
                if ($dup_check['total'] < 2)
                    @ xtc_del_image_file($val);
                xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_IMAGES . " WHERE products_id = '" . xtc_db_input($products_id) . "' AND image_name  = '" . $val . "'");
            }
        }

        $languages = xtc_get_languages();

        for ($img = 0; $img < MO_PICS; $img++) {
            if ($pIMG = xtc_try_upload('mo_pics_' . $img, DIR_FS_CATALOG_ORIGINAL_IMAGES, '777', '')) {
                $pname_arr = explode('.', $pIMG->filename);
                $nsuffix = array_pop($pname_arr);

                if (IMAGE_NAME_PRODUCT == 'p_id') {
                    $products_image_name = $products_id . '-' . ($img + 1) . '.' . strtolower($nsuffix);
                } elseif (IMAGE_NAME_PRODUCT == 'p_name') {
                    $products_image_name = cseo_get_url_friendly_text(xtc_db_prepare_input($products_data['products_name'][(int) $_SESSION['languages_id']])) . '_' . $products_id . '-' . ($img + 1) . '.' . strtolower($nsuffix);
                } else {
                    $products_image_name = cseo_get_url_friendly_text($pname_arr[0]) . '-' . ($img + 1) . '.' . strtolower($nsuffix);
                }

                // $products_image_name = $new_products_name.'-'. ($img +1).'.'.strtolower($nsuffix);
                $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
											  FROM " . TABLE_PRODUCTS_IMAGES . "
											  WHERE image_name = '" . $products_data['products_previous_image_' . ($img + 1)] . "'");
                $dup_check = xtc_db_fetch_array($dup_check_query);
                if ($dup_check['total'] < 2)
                    @xtc_del_image_file($products_data['products_previous_image_' . ($img + 1)]);

                @xtc_del_image_file($products_image_name);

                rename(DIR_FS_CATALOG_ORIGINAL_IMAGES . '/' . $pIMG->filename, DIR_FS_CATALOG_ORIGINAL_IMAGES . '/' . $products_image_name);
                $mo_img = array('products_id' => xtc_db_prepare_input($products_id),
                    'image_nr' => xtc_db_prepare_input($img + 1),
                    'image_name' => xtc_db_prepare_input($products_image_name));

                if ($action == 'insert') {
                    xtc_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img);
                } elseif ($action == 'update' && $products_data['products_previous_image_' . ($img + 1)]) {
                    if ($products_data['del_mo_pic']) {
                        foreach ($products_data['del_mo_pic'] AS $dummy => $val) {
                            if ($val == $products_data['products_previous_image_' . ($img + 1)])
                                xtc_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img);
                            break;
                        }
                    }
                    xtc_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img, 'update', 'image_name = \'' . xtc_db_input($products_data['products_previous_image_' . ($img + 1)]) . '\'');
                } elseif (!$products_data['products_previous_image_' . ($img + 1)])
                    xtc_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img);

                require (DIR_WS_INCLUDES . 'product_thumbnail_images.php');
                require (DIR_WS_INCLUDES . 'product_info_images.php');
                require (DIR_WS_INCLUDES . 'product_popup_images.php');
                require (DIR_WS_INCLUDES . 'product_mini_images.php');
            }
            foreach ($languages AS $lang) {
                $alt_tag = array('alt_langID_' . $lang['id'] => $products_data['alt_tag_' . ($img + 1) . '_' . $lang['id']]);
                xtc_db_perform(TABLE_PRODUCTS_IMAGES, $alt_tag, 'update', 'image_nr = \'' . ($img + 1) . '\' AND products_id = \'' . $products_id . '\'');
            }
        }

        if (isset($products_data['products_image']) && xtc_not_null($products_data['products_image']) && ($products_data['products_image'] != 'none'))
            $sql_data_array['products_image'] = xtc_db_prepare_input($products_data['products_image']);

        if ($action == 'insert') {
            $insert_sql_data = array('products_date_added' => 'now()');
            $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
            xtc_db_perform(TABLE_PRODUCTS, $sql_data_array);
            $products_id = xtc_db_insert_id();
            xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_TO_CATEGORIES . "
								              SET products_id   = '" . $products_id . "',
								              categories_id = '" . $dest_category_id . "'");
        } elseif ($action == 'update') {
            $update_sql_data = array('products_last_modified' => 'now()',
                'products_date_added' => $products_data['products_date_added']);
            $sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
            xtc_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . xtc_db_input($products_id) . '\'');
        }

        // Included specials
        if (file_exists("includes/modules/categories_specials.php")) {
            require_once("includes/modules/categories_specials.php");
            saveSpecialsData($products_id);
        }

        $languages = xtc_get_languages();

        $i = 0;
        $group_query = xtc_db_query("SELECT customers_status_id FROM " . TABLE_CUSTOMERS_STATUS . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' AND customers_status_id != '0'");
        while ($group_values = xtc_db_fetch_array($group_query)) {
            $i++;
            $group_data[$i] = array('STATUS_ID' => $group_values['customers_status_id']);
        }
        for ($col = 0, $n = sizeof($group_data); $col < $n + 1; $col++) {
            if ($group_data[$col]['STATUS_ID'] != '') {
                $personal_price = xtc_db_prepare_input($products_data['products_price_' . $group_data[$col]['STATUS_ID']]);
                if ($personal_price == '' || $personal_price == '0.0000') {
                    $personal_price = '0.00';
                } else {
                    if (PRICE_IS_BRUTTO == 'true')
                        $personal_price = ($personal_price / (xtc_get_tax_rate($products_data['products_tax_class_id']) + 100) * 100);

                    $personal_price = xtc_round($personal_price, PRICE_PRECISION);
                }

                if ($action == 'insert') {
                    xtc_db_query("DELETE FROM personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . " WHERE products_id = '" . $products_id . "' AND quantity = '1';");
                    $insert_array = array();
                    $insert_array = array('personal_offer' => $personal_price, 'quantity' => '1', 'products_id' => $products_id);
                    xtc_db_perform("personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'], $insert_array);
                } else {
                    xtc_db_query("DELETE FROM personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . " WHERE products_id = '" . $products_id . "' AND quantity = '1';");
                    xtc_db_query("DELETE FROM personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . " WHERE personal_offer = '0.0000';");
                    $insert_array = array();
                    $insert_array = array('personal_offer' => $personal_price, 'quantity' => '1', 'products_id' => $products_id);
                    xtc_db_perform("personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'], $insert_array);
                    xtc_db_query("UPDATE personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . "
												                 SET personal_offer = '" . $personal_price . "'
												               WHERE products_id = '" . $products_id . "'
												                 AND quantity    = '1'");
                }
            }
        }
        $i = 0;
        $group_query = xtc_db_query("SELECT 
										customers_status_id
					                 FROM 
					                 	" . TABLE_CUSTOMERS_STATUS . "
					                 WHERE 
					                 	language_id = '" . (int) $_SESSION['languages_id'] . "'
					                 AND 
					                 	customers_status_id != '0'");
        while ($group_values = xtc_db_fetch_array($group_query)) {
            $i++;
            $group_data[$i] = array('STATUS_ID' => $group_values['customers_status_id']);
        }

        for ($col = 0, $n = sizeof($group_data); $col < $n + 1; $col++) {
            if ($group_data[$col]['STATUS_ID'] != '') {
                $quantity = xtc_db_prepare_input($products_data['products_quantity_staffel_' . $group_data[$col]['STATUS_ID']]);
                $staffelpreis = xtc_db_prepare_input($products_data['products_price_staffel_' . $group_data[$col]['STATUS_ID']]);
                if (PRICE_IS_BRUTTO == 'true')
                    $staffelpreis = ($staffelpreis / (xtc_get_tax_rate($products_data['products_tax_class_id']) + 100) * 100);

                $staffelpreis = xtc_round($staffelpreis, PRICE_PRECISION);

                if ($staffelpreis != '' && $quantity != '') {
                    if ($quantity <= 1)
                        $quantity = 2;
                    $check_query = xtc_db_query("SELECT 
													quantity
												FROM 
													personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . "
												WHERE 
													products_id = '" . $products_id . "'
												AND 
													quantity    = '" . $quantity . "'");

                    if (xtc_db_num_rows($check_query) < 1) {
                        $customers_status_array = array('price_id' => '',
                            'products_id' => $products_id,
                            'quantity' => $quantity,
                            'personal_offer' => $staffelpreis);
                        xtc_db_perform('personal_offers_by_customers_status_' . $group_data[$col]['STATUS_ID'], $customers_status_array);
                    }
                }
            }
        }
        $db = xtc_db_query("DELETE FROM tag_to_product WHERE pID = '" . $products_id . "'");
        // foreach($languages AS $lang) {
        // $language_id = $lang['id'];
        $n = sizeof($languages);
        for ($i = 0; $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
            if ($_FILES['products_promotion_image' . $i]['name'] != '') {
                if ($image = xtc_try_upload('products_promotion_image' . $i, DIR_FS_CATALOG_IMAGES . 'products_promotion/')) {
                    $paname_arr = explode('.', $image->filename);
                    $pnsuffix = array_pop($paname_arr);
                    $products_promotion_imagename = $products_id . '_' . $i . '.' . $pnsuffix;

                    @unlink(DIR_FS_CATALOG_IMAGES . 'products_promotion/' . $products_promotion_imagename);
                    rename(DIR_FS_CATALOG_IMAGES . 'products_promotion/' . $image->filename, DIR_FS_CATALOG_IMAGES . 'products_promotion/' . $products_promotion_imagename);
                }
            } elseif ($products_data['del_products_promotion_image' . $i] == true) {
                $products_promotion_imagename = '';
            } else {
                $products_promotion_imagename = $products_data['products_promotion_image' . $i];
            }
            $products_url_alias = cseo_get_url_friendly_text($products_data['products_url_alias'][$language_id]);
            $sql_data_array = array('products_name' => xtc_db_prepare_input($products_data['products_name'][$language_id]),
                'products_description' => xtc_db_prepare_input($products_data['products_description'][$language_id]),
                'products_short_description' => xtc_db_prepare_input($products_data['products_short_description'][$language_id]),
                'products_cart_description' => xtc_db_prepare_input($products_data['products_cart_description'][$language_id]),
                'products_zusatz_description' => xtc_db_prepare_input($products_data['products_zusatz_description'][$language_id]),
                'products_img_alt' => xtc_db_prepare_input($products_data['products_image_alt'][$language_id]),
                'products_keywords' => xtc_db_prepare_input($products_data['products_keywords'][$language_id]),
                'products_url' => xtc_db_prepare_input($products_data['products_url'][$language_id]),
                'products_meta_title' => xtc_db_prepare_input($products_data['products_meta_title'][$language_id]),
                'products_meta_description' => xtc_db_prepare_input($products_data['products_meta_description'][$language_id]),
                'products_promotion_title' => xtc_db_prepare_input($products_data['products_promotion_title'][$language_id]),
                'products_promotion_image' => $products_promotion_imagename,
                'products_promotion_desc' => xtc_db_prepare_input($products_data['products_promotion_desc'][$language_id]),
                'products_meta_keywords' => xtc_db_prepare_input($products_data['products_meta_keywords'][$language_id]),
                'products_google_taxonomie' => xtc_db_prepare_input($products_data['products_google_taxonomie'][$language_id]),
                'products_taxonomie' => xtc_db_prepare_input($products_data['products_taxonomie'][$language_id]),
                'products_treepodia_catch_phrase_1' => xtc_db_prepare_input($products_data['products_treepodia_catch_phrase_1'][$language_id]),
                'products_treepodia_catch_phrase_2' => xtc_db_prepare_input($products_data['products_treepodia_catch_phrase_2'][$language_id]),
                'products_treepodia_catch_phrase_3' => xtc_db_prepare_input($products_data['products_treepodia_catch_phrase_3'][$language_id]),
                'products_treepodia_catch_phrase_4' => xtc_db_prepare_input($products_data['products_treepodia_catch_phrase_4'][$language_id]),
                'products_url_alias' => xtc_db_prepare_input($products_url_alias));

            if (!empty($products_data['products_tag_cloud'][$language_id])) {
                foreach ($products_data['products_tag_cloud'][$language_id] AS $tag) {
                    if (!empty($tag))
                        $db = xtc_db_query("INSERT INTO tag_to_product VALUES ('','" . $products_id . "','" . $language_id . "','" . trim($tag) . "') ");
                }
            }

            if (function_exists(add_product_desc_fields)) {
                $sql_data_array = add_product_desc_fields($sql_data_array, $products_data, $language_id);
            }

            if (trim(ADD_PRODUCTS_DESCRIPTION_FIELDS)) {
                $sql_data_array = array_merge($sql_data_array, $this->add_data_fields(ADD_PRODUCTS_DESCRIPTION_FIELDS, $products_data, $language_id));
            }

            if ($action == 'insert') {
                $insert_sql_data = array('products_id' => $products_id, 'language_id' => $language_id);
                $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
                xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
            } elseif ($action == 'update')
                xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \'' . xtc_db_input($products_id) . '\' and language_id = \'' . $language_id . '\'');
        }
        // BEGIN HermesAPI
        if (isset($products_data['hermes_minpclass'])) {
            require_once DIR_FS_CATALOG . 'includes/classes/class.hermes.php';
            $hermes = new HermesAPI();
            $hermes_data = array(
                'products_id' => (int) $products_id,
                'min_pclass' => xtc_db_prepare_input($products_data['hermes_minpclass']),
            );
            $hermes->setProductsOptions($hermes_data);
        }
        // END Hermes
        if (isset($products_data['cseo_update'])) {
            xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&action=new_product&pID=' . $products_id));
        }
    }

    function duplicate_product($src_products_id, $dest_categories_id) {

        $product_values = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . xtc_db_input($src_products_id) . "';"));

        if ($dest_categories_id == 0) {
            $startpage = 1;
            $products_status = 1;
        } else {
            $startpage = 0;
            $products_status = $product['products_status'];
        }
		//alles holen
		if (is_array($product_values)) {
			foreach ($product_values AS $field => $value) {
				$base_prod_sql_data_array[$field] = $value;
			}
		}
		//die sollen neu gesetzt werden
        $sql_prod_data_array = array('products_id' => 'NULL',
            'products_startpage' => $startpage,
            'products_date_added' => date('Y-m-d H:m:s'),
            'products_status' => $products_status);
			
		//zusammen bringen
		$sql_data_array = array_merge($base_prod_sql_data_array, $sql_prod_data_array);

        $customers_statuses_array = xtc_get_customers_statuses();

        for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i++) {
            if (isset($customers_statuses_array[$i]['id']))
                $sql_data_array = array_merge($sql_data_array, array('group_permission_' . $customers_statuses_array[$i]['id'] => $product['group_permission_' . $customers_statuses_array[$i]['id']]));
        }

        xtc_db_perform(TABLE_PRODUCTS, $sql_data_array);

        $dup_products_id = xtc_db_insert_id();

        if ($product['products_image'] != '') {
            if (IMAGE_NAME_PRODUCT == 'p_id') {
                $pname_arr = explode('.', $product['products_image']);
                $nsuffix = array_pop($pname_arr);
                $dup_products_image_name = $dup_products_id . '_0.' . strtolower($nsuffix);
            } elseif (IMAGE_NAME_PRODUCT == 'p_name' || IMAGE_NAME_PRODUCT == 'p_image') {
                $pname_arr = explode('.', $product['products_image']);
                $nsuffix = array_pop($pname_arr);
                $new_products_name = array_shift($pname_arr);
                $dup_products_image_name = $new_products_name . '_' . $dup_products_id . '.' . strtolower($nsuffix);
            }
            xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_image = '" . $dup_products_image_name . "' WHERE products_id = '" . $dup_products_id . "'");
            @copy(DIR_FS_CATALOG_ORIGINAL_IMAGES . '/' . $product['products_image'], DIR_FS_CATALOG_ORIGINAL_IMAGES . '/' . $dup_products_image_name);
            @copy(DIR_FS_CATALOG_INFO_IMAGES . '/' . $product['products_image'], DIR_FS_CATALOG_INFO_IMAGES . '/' . $dup_products_image_name);
            @copy(DIR_FS_CATALOG_THUMBNAIL_IMAGES . '/' . $product['products_image'], DIR_FS_CATALOG_THUMBNAIL_IMAGES . '/' . $dup_products_image_name);
            @copy(DIR_FS_CATALOG_MINI_IMAGES . '/' . $product['products_image'], DIR_FS_CATALOG_MINI_IMAGES . '/' . $dup_products_image_name);
            @copy(DIR_FS_CATALOG_POPUP_IMAGES . '/' . $product['products_image'], DIR_FS_CATALOG_POPUP_IMAGES . '/' . $dup_products_image_name);
        } else {
            unset($dup_products_image_name);
		}

        $old_products_id = xtc_db_input($src_products_id);
		$languages = xtc_get_languages();
		foreach ($languages AS $lang) {
			$description_query = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_id = '" . xtc_db_input($src_products_id) . "' AND language_id = '".$lang['id']."';"));
			//alles holen
			if (is_array($description_query)) {
				foreach ($description_query AS $field => $value) {
					$base_prod_desc_sql_data_array[$field] = $value;
				}
			}
			//die sollen neu gesetzt werden
			$sql_prod_desc_data_array = array('products_id' => $dup_products_id,
										'language_id' => $description_query['language_id'],
										'products_viewed' => '0');
			//zusammen bringen
			$sql_desc_data_array = array_merge($base_prod_desc_sql_data_array, $sql_prod_desc_data_array);
			
			xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_desc_data_array);
		}

        $tag_query = xtc_db_query("SELECT * FROM " . TABLE_TAG_TO_PRODUCT . " WHERE pID = '" . xtc_db_input($src_products_id) . "'");

        while ($tags = xtc_db_fetch_array($tag_query)) {
            xtc_db_query("INSERT INTO " . TABLE_TAG_TO_PRODUCT . "
						    		                 SET pID = '" . $dup_products_id . "',
						    		                     lID = '" . $tags['lID'] . "',
						    		                     tag = '" . $tags['tag'] . "'
						    		                     ");
        }
        xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_TO_CATEGORIES . " SET products_id   = '" . $dup_products_id . "', categories_id = '" . xtc_db_input($dest_categories_id) . "'");
        $mo_images = xtc_get_products_mo_images($src_products_id);
        if (is_array($mo_images)) {
            foreach ($mo_images AS $dummy => $mo_img) {
                if (IMAGE_NAME_PRODUCT == 'p_id') {
                    $pname_arr = explode('.', $mo_img['image_name']);
                    $nsuffix = array_pop($pname_arr);
                    $dup_products_image_name = $dup_products_id . '_' . $mo_img['image_nr'] . '.' . $nsuffix;
                } elseif (IMAGE_NAME_PRODUCT == 'p_name' || IMAGE_NAME_PRODUCT == 'p_image') {
                    $pname_arr = explode('.', $mo_img['image_name']);
                    $nsuffix = array_pop($pname_arr);
                    $new_products_name = array_shift($pname_arr);
                    $dup_products_image_name = $new_products_name . '_' . $dup_products_id . '-' . $mo_img['image_nr'] . '.' . $nsuffix;
                }
                @copy(DIR_FS_CATALOG_ORIGINAL_IMAGES . '/' . $mo_img['image_name'], DIR_FS_CATALOG_ORIGINAL_IMAGES . '/' . $dup_products_image_name);
                @copy(DIR_FS_CATALOG_INFO_IMAGES . '/' . $mo_img['image_name'], DIR_FS_CATALOG_INFO_IMAGES . '/' . $dup_products_image_name);
                @copy(DIR_FS_CATALOG_THUMBNAIL_IMAGES . '/' . $mo_img['image_name'], DIR_FS_CATALOG_THUMBNAIL_IMAGES . '/' . $dup_products_image_name);
                @copy(DIR_FS_CATALOG_MINI_IMAGES . '/' . $mo_img['image_name'], DIR_FS_CATALOG_MINI_IMAGES . '/' . $dup_products_image_name);
                @copy(DIR_FS_CATALOG_POPUP_IMAGES . '/' . $mo_img['image_name'], DIR_FS_CATALOG_POPUP_IMAGES . '/' . $dup_products_image_name);
                xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_IMAGES . "
								    			                 SET products_id = '" . $dup_products_id . "',
								    			                     image_nr = '" . $mo_img['image_nr'] . "',
								    			                     image_name = '" . $dup_products_image_name . "'");
            }
        }

        $products_id = $dup_products_id;

        $i = 0;
        $group_query = xtc_db_query("SELECT customers_status_id
				    	                               FROM " . TABLE_CUSTOMERS_STATUS . "
				    	                              WHERE language_id = '" . (int) $_SESSION['languages_id'] . "'
				    	                                AND customers_status_id != '0'");

        while ($group_values = xtc_db_fetch_array($group_query)) {
            $i++;
            $group_data[$i] = array('STATUS_ID' => $group_values['customers_status_id']);
        }

        for ($col = 0, $n = sizeof($group_data); $col < $n + 1; $col++) {
            if ($group_data[$col]['STATUS_ID'] != '') {
                $copy_query = xtc_db_query("SELECT quantity,personal_offer FROM personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . " WHERE products_id = '" . $old_products_id . "'");
                while ($copy_data = xtc_db_fetch_array($copy_query)) {
                    xtc_db_query("INSERT INTO personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . " SET price_id       = '',
										    				                     products_id = '" . $products_id . "',
										    				                     quantity = '" . $copy_data['quantity'] . "',
										    				                     personal_offer = '" . $copy_data['personal_offer'] . "'");
                }
            }
        }
    }

    function link_product($src_products_id, $dest_categories_id) {
        global $messageStack;
        $check_query = xtc_db_query("SELECT COUNT(*) AS total
				                                     FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
				                                     WHERE products_id = '" . xtc_db_input($src_products_id) . "'
				                                     AND categories_id = '" . xtc_db_input($dest_categories_id) . "'");
        $check = xtc_db_fetch_array($check_query);

        if ($check['total'] < '1') {
            xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_TO_CATEGORIES . "
						                          SET products_id = '" . xtc_db_input($src_products_id) . "',
						                          categories_id = '" . xtc_db_input($dest_categories_id) . "'");

            if ($dest_categories_id == 0) {
                $this->set_product_status($src_products_id, $products_status);
                $this->set_product_startpage($src_products_id, 1);
            }
        } else {
            $messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
        }
    }

    function move_product($src_products_id, $src_category_id, $dest_category_id) {
        $duplicate_check_query = xtc_db_query("SELECT COUNT(*) AS total
				    	                                         FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
				    	                                        WHERE products_id   = '" . xtc_db_input($src_products_id) . "'
				    	                                          AND categories_id = '" . xtc_db_input($dest_category_id) . "'");
        $duplicate_check = xtc_db_fetch_array($duplicate_check_query);

        if ($duplicate_check['total'] < 1) {
            xtc_db_query("UPDATE " . TABLE_PRODUCTS_TO_CATEGORIES . "
						    		                 SET categories_id = '" . xtc_db_input($dest_category_id) . "'
						    		                 WHERE products_id   = '" . xtc_db_input($src_products_id) . "'
						    		                 AND categories_id = '" . $src_category_id . "'");

            if ($dest_category_id == 0) {
                $this->set_product_status($src_products_id, 1);
                $this->set_product_startpage($src_products_id, 1);
            }

            if ($src_category_id == 0) {
                $this->set_product_status($src_products_id, $products_status);
                $this->set_product_startpage($src_products_id, 0);
            }
        }
    }

    function set_product_status($products_id, $status) {
        if ($status == '1') {
            return xtc_db_query("update " . TABLE_PRODUCTS . " set products_status = '1', products_last_modified = now() where products_id = '" . $products_id . "'");
        } elseif ($status == '0') {
            return xtc_db_query("update " . TABLE_PRODUCTS . " set products_status = '0', products_last_modified = now() where products_id = '" . $products_id . "'");
        } else {
            return -1;
        }
    }

    function set_product_startpage($products_id, $status) {
        if ($status == '1') {
            return xtc_db_query("update " . TABLE_PRODUCTS . " set products_startpage = '1', products_last_modified = now() where products_id = '" . $products_id . "'");
        } elseif ($status == '0') {
            return xtc_db_query("update " . TABLE_PRODUCTS . " set products_startpage = '0', products_last_modified = now() where products_id = '" . $products_id . "'");
        } else {
            return -1;
        }
    }

    function set_product_remove_startpage_sql($products_id, $status) {
        if ($status == '0') {
            global $messageStack;
            $check_query = xtc_db_query("SELECT COUNT(*) AS total
												   FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
												   WHERE products_id = '" . $products_id . "'
												   AND categories_id != '0'"); //changed from "= '0'" to "!= '0'"
            $check = xtc_db_fetch_array($check_query);

            if ($check['total'] >= '1') {
                return xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = '" . $products_id . "' and categories_id = '0'");
                ;
            }
        }
    }

    function count_category_products($category_id, $include_deactivated = false) {
        $products_count = 0;
        if ($include_deactivated) {
            $products_query = xtc_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . $category_id . "'");
        } else {
            $products_query = xtc_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . $category_id . "'");
        }

        $products = xtc_db_fetch_array($products_query);

        $products_count += $products['total'];

        $childs_query = xtc_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . $category_id . "'");
        if (xtc_db_num_rows($childs_query)) {
            while ($childs = xtc_db_fetch_array($childs_query))
                $products_count += $this->count_category_products($childs['categories_id'], $include_deactivated);
        }
        return $products_count;
    }

    function count_category_childs($category_id) {
        $categories_count = 0;
        $categories_query = xtc_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . $category_id . "'");
        while ($categories = xtc_db_fetch_array($categories_query)) {
            $categories_count++;
            $categories_count += $this->count_category_childs($categories['categories_id']);
        }
        return $categories_count;
    }

    function edit_cross_sell($cross_data) {

        if ($cross_data['special'] == 'add_entries') {
            if (isset($cross_data['ids'])) {
                foreach ($cross_data['ids'] AS $pID) {
                    $sql_data_array = array('products_id' => $cross_data['current_product_id'], 'xsell_id' => $pID, 'products_xsell_grp_name_id' => $cross_data['group_name'][$pID]);
                    $check_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_XSELL . " WHERE products_id='" . $cross_data['current_product_id'] . "' and xsell_id='" . $pID . "'");
                    if (!xtc_db_num_rows($check_query))
                        xtc_db_perform(TABLE_PRODUCTS_XSELL, $sql_data_array);
                }
            }
        }
        if ($cross_data['special'] == 'edit') {
            if (isset($cross_data['idse'])) {
                foreach ($cross_data['idse'] AS $pID) {
                    xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_XSELL . " WHERE ID='" . $pID . "'");
                }
            }
            if (isset($cross_data['sort'])) {
                foreach ($cross_data['sort'] AS $ID => $sort) {
                    xtc_db_query("UPDATE " . TABLE_PRODUCTS_XSELL . " SET sort_order='" . $sort . "',products_xsell_grp_name_id='" . $cross_data['group_name'][$ID] . "' WHERE ID='" . $ID . "'");
                }
            }
        }
    }

    function add_data_fields($add_data_string, $data, $language_id = '') {
        $add_data_array = explode(',', preg_replace("'[\r\n\s]+'", '', $add_data_string));
        $add_data_fields_array = array();
        for ($i = 0, $n = sizeof($add_data_array); $i < $n; $i++) {
            if ($language_id != '') {
                $add_data_fields_array[$add_data_array[$i]] = xtc_db_prepare_input($data[$add_data_array[$i]][$language_id]);
            } else {
                $add_data_fields_array[$add_data_array[$i]] = xtc_db_prepare_input($data[$add_data_array[$i]]);
            }
        }
        return $add_data_fields_array;
    }

	
}
