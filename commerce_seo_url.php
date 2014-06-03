<?php

/* -----------------------------------------------------------------
 * 	$Id: commerce_seo_url.php 435 2013-06-26 15:15:02Z akausch $
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

// Set the local configuration parameters - mainly for developers - if exists else the mainconfigure
if (file_exists('includes/local/configure.php') && filesize('includes/local/configure.php') > 0)
    include('includes/local/configure.php');
elseif (file_exists('includes/configure.php') && filesize('includes/configure.php') > 0)
    include('includes/configure.php');
elseif (!defined('COMMERCE_SEO_V22_INSTALLED')) {
    header('Location: installer/');
    exit;
} else {
    header('Location: installer/');
    exit;
}

// include used functions
require_once('inc/cseo_db.inc.php');
require('includes/database_tables.php');

// make a connection to the database... now
xtc_db_connect() or die('Unable to connect to database server!');


if (!empty($_GET['linkurl']) || !empty($_GET['manu']) && empty($_GET['error'])) {
    if (MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True') {
        $link_url = strtolower(xtc_db_input($_GET['linkurl']));
    } else {
        $link_url = xtc_db_input($_GET['linkurl']);
    }

    $data_query = xtc_db_query("SELECT url_text, categories_id, blog_id, blog_cat, content_group, language_id FROM commerce_seo_url WHERE url_text = '" . $link_url . "' LIMIT 1;");
    if (xtc_db_num_rows($data_query) > 0) {
        $data = xtc_db_fetch_array($data_query);
    } else {
        $data_query = xtc_db_query("SELECT products_id, url_text, language_id FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE url_text = '" . xtc_db_input($link_url) . "' LIMIT 1;");
        if (xtc_db_num_rows($data_query) > 0) {
            $data = xtc_db_fetch_array($data_query);
        } else {
            $link_query = xtc_db_query("SELECT
											cn.file_name_php
										FROM
											commerce_seo_url_names AS cn
										JOIN
											commerce_seo_url_personal_links AS cp
										ON
											cp.url_text = '" . $link_url . "'
										AND cn.file_name = cp.file_name LIMIT 1");
            if (xtc_db_num_rows($link_query) > 0) {
                $link = xtc_db_fetch_array($link_query);
			}
        }
    }

    function get_cpath($category) {
        $cPath = $category;
        while ($category != '0') {
            $category_data = xtc_db_fetch_array(xtc_db_query("SELECT parent_id FROM categories WHERE categories_id = '" . xtc_db_input($category) . "' LIMIT 1;"));
            if ($category_data['parent_id'] != '0')
                $cPath = $category_data['parent_id'] . '_' . $cPath;

            if ($category_data['parent_id'] == 0)
                break;

            $category = $category_data['parent_id'];
        }
        return $cPath;
    }

    unset($_GET['linkurl']);
    if (!empty($data['products_id'])) {
        $_GET['products_id'] = $data['products_id'];
        $PHP_SELF = 'product_info.php';
        include('product_info.php');
    } elseif (!empty($data['categories_id'])) {
        $_GET['cPath'] = get_cpath($data['categories_id']);
        $PHP_SELF = 'index.php';
        include('index.php');
    } elseif (!empty($data['content_group'])) {
        $_GET['coID'] = $data['content_group'];
        $PHP_SELF = 'shop_content.php';
        include('shop_content.php');
    } elseif (!empty($data['blog_id'])) {
        $_GET['blog_item'] = $data['blog_id'];
        include('blog.php');
    } elseif (!empty($data['blog_cat'])) {
        $_GET['blog_cat'] = $data['blog_cat'];
        include('blog.php');
    } elseif ((!empty($data['url_text'])) && (!empty($data['language_id']))) {
        $PHP_SELF = 'index.php';
        include('index.php');
    } elseif (!empty($link['file_name_php'])) {
        $aktuelle_datei = $link['file_name_php'];
        $_SESSION['this_page'] = $link['file_name_php'];
        include($link['file_name_php']);
    } elseif (!empty($mf_data['manufacturers_id'])) {
        $_GET['manufacturers_id'] = $mf_data['manufacturers_id'];
        include('index.php');
    } else {
        if (MODULE_COMMERCE_SEO_404_HANDLING === 'True') {
            header('Status: 404 Not Found');
            header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
        } else {
            header('Status: 410 Gone');
            header($_SERVER['SERVER_PROTOCOL'] . " 410 Gone", true, 410);
        }
        $_GET['error'] = '404';
        include_once('404.php');
    }
} elseif (!empty($_GET['error'])) {
    if (MODULE_COMMERCE_SEO_404_HANDLING === 'True') {
        header('Status: 404 Not Found');
        header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
    } else {
        header('Status: 410 Gone');
        header($_SERVER['SERVER_PROTOCOL'] . " 410 Gone", true, 410);
    }
    $_GET['error'] = '404';
    include_once('404.php');
} else {
    include('index.php');
}

header("Content-Type: text/html; charset=utf-8");
header('Connection: Keep-Alive');
header('Keep-Alive: timeout=300');