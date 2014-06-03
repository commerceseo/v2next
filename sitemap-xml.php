<?php

/* -----------------------------------------------------------------
 * 	$Id: sitemap-xml.php 934 2014-04-02 15:40:06Z akausch $
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

function blog_da() {
    $ist_blog_da = 'blog.php';
    if (file_exists($ist_blog_da))
        return true;
    else
        return false;
}

function iso8601_date($timestamp) {
    if (PHP_VERSION < 5) {
        $tzd = date('O', $timestamp);
        $tzd = substr(chunk_split($tzd, 3, ':'), 0, 6);
        return date('Y-m-d\TH:i:s', $timestamp) . $tzd;
    } else {
        return date('c', $timestamp);
    }
}

function xtc_get_languages() {
    $languages_query = xtc_db_query("SELECT languages_id, code FROM languages WHERE status = '1' ORDER BY sort_order");
    while ($languages = xtc_db_fetch_array($languages_query)) {
        $languages_array[] = array('id' => $languages['languages_id'], 'code' => $languages['code']);
	}
    return $languages_array;
}

$languages = xtc_get_languages();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.google.com/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.9 http://www.google.com/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

for ($i = 0; $i < sizeof($languages); $i++) {

    #####################################################################
    # PRODUKTE
    #####################################################################

    $product_query = xtc_db_query("SELECT 
									p.products_id,
									pd.language_id,  
									UNIX_TIMESTAMP(p.products_date_added) AS products_date_added, 
									UNIX_TIMESTAMP(p.products_last_modified) AS products_last_modified  
									FROM 
									" . TABLE_PRODUCTS . " AS p 
									JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON(pd.language_id = '" . $languages[$i]['id'] . "')
									WHERE 
									p.products_status = 1 
									AND p.products_id = pd.products_id  
									ORDER BY p.products_id");
    if (xtc_db_num_rows($product_query)) {
        while ($product = xtc_db_fetch_array($product_query)) {
            $lang_param = ($languages[$i]['code'] != DEFAULT_LANGUAGE) ? '&language=' . $languages[$i]['code'] : '';
            $date = ($product['products_last_modified'] != NULL) ? iso8601_date($product['products_last_modified']) : iso8601_date($product['products_date_added']);
            echo "\t" . '<url>' . "\n";
            echo "\t\t" . '<loc>' . htmlspecialchars(utf8_encode(xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($product['products_id'], $product['products_name']) . $lang_param, 'NONSSL', false, SEARCH_ENGINE_FRIENDLY_URLS))) . '</loc>' . "\n";
            echo "\t\t" . '<priority>1.0</priority>' . "\n";
            echo "\t\t" . '<lastmod>' . $date . '</lastmod>' . "\n";
            echo "\t\t" . '<changefreq>daily</changefreq>' . "\n";
            echo "\t" . '</url>' . "\n";
        }
    }
}
#####################################################################
# KATEGORIEN
#####################################################################

for ($i = 0; $i < sizeof($languages); $i++) {
    $cat_result = xtc_db_query("SELECT c.categories_id,
													c.parent_id, 
													cd.language_id, 
													UNIX_TIMESTAMP(c.date_added) AS date_added, 
													UNIX_TIMESTAMP(c.last_modified) AS last_modified 
													FROM 
													" . TABLE_CATEGORIES . " AS c
													JOIN " . TABLE_CATEGORIES_DESCRIPTION . " AS cd ON(c.categories_id = cd.categories_id AND cd.language_id = '" . $languages[$i]['id'] . "')
													WHERE 
													c.categories_status = 1 
													ORDER by cd.categories_id");

    if (xtc_db_num_rows($cat_result)) {
        while ($cat_data = xtc_db_fetch_array($cat_result)) {
            $lang_param = ($languages[$i]['code'] != DEFAULT_LANGUAGE) ? '&language=' . $languages[$i]['code'] : '';
            $date = ($cat_data['last_modified'] != NULL) ? iso8601_date($cat_data['last_modified']) : iso8601_date($cat_data['date_added']);
            echo "\t" . '<url>' . "\n";
            echo "\t\t" . '<loc>' . htmlspecialchars(utf8_encode(xtc_href_link(FILENAME_DEFAULT, xtc_category_link($cat_data['categories_id'], $cat_data['code']) . $lang_param, 'NONSSL', false, SEARCH_ENGINE_FRIENDLY_URLS))) . '</loc>' . "\n";
            echo "\t\t" . '<priority>0.8</priority>' . "\n";
            echo "\t\t" . '<lastmod>' . $date . '</lastmod>' . "\n";
            echo "\t\t" . '<changefreq>daily</changefreq>' . "\n";
            echo "\t" . '</url>' . "\n";
        }
    }
}

#####################################################################
# CONTENT
#####################################################################
for ($i = 0; $i < sizeof($languages); $i++) {
    $content_query = xtc_db_query("SELECT content_group FROM " . TABLE_CONTENT_MANAGER . " WHERE content_status = '1' AND languages_id = '" . $languages[$i]['id'] . "' ");

    while ($content = xtc_db_fetch_array($content_query)) {
        $lang_param = ($languages[$i]['code'] != DEFAULT_LANGUAGE) ? '&language=' . $languages[$i]['code'] : '';
        echo "\t" . '<url>' . "\n";
        echo "\t\t" . '<loc>' . htmlspecialchars(utf8_encode(xtc_href_link(FILENAME_CONTENT, 'coID=' . $content['content_group'] . $lang_param, 'NONSSL', false, SEARCH_ENGINE_FRIENDLY_URLS))) . '</loc>' . "\n";
        echo "\t\t" . '<priority>0.7</priority>' . "\n";
        echo "\t\t" . '<lastmod>' . iso8601_date(time()) . '</lastmod>' . "\n";
        echo "\t\t" . '<changefreq>weekly</changefreq>' . "\n";
        echo "\t" . '</url>' . "\n";
    }
} // Ende for()
#####################################################################
# WENN BLOG INSTALLIERT - DER AUCH MIT REIN
#####################################################################
if (blog_da()) {
    for ($i = 0; $i < sizeof($languages); $i++) {
        $categories_query = xtc_db_query("SELECT id, titel, date, update_date FROM " . TABLE_BLOG_CATEGORIES . " WHERE status = 1 AND language_id = '" . $languages[$i]['id'] . "' ORDER BY position ASC");

        while ($categories = xtc_db_fetch_array($categories_query)) {
            $lang_param = ($languages[$i]['code'] != DEFAULT_LANGUAGE) ? '&language=' . $languages[$i]['code'] : '';

            $datum = explode('.', $categories['date']);
            $update_date = explode('.', $categories['update_date']);
            $date = mktime(0, 0, 0, $datum[1], $datum[0], $datum[2]);
            $update = mktime(0, 0, 0, $update_date[1], $update_date[0], $update_date[2]);

            echo "\t" . '<url>' . "\n";
            echo "\t\t" . '<loc>' . htmlspecialchars(utf8_encode(xtc_href_link(FILENAME_BLOG, 'blog_cat=' . $categories['id'] . $lang_param, 'NONSSL', false, SEARCH_ENGINE_FRIENDLY_URLS))) . '</loc>' . "\n";
            echo "\t\t" . '<priority>0.7</priority>' . "\n";
            echo "\t\t" . '<lastmod>' . iso8601_date((!empty($update)) ? $update : $date ) . '</lastmod>' . "\n";
            echo "\t\t" . '<changefreq>weekly</changefreq>' . "\n";
            echo "\t" . '</url>' . "\n";

            // Einzelne Eintraege

            $items_query = xtc_db_query("SELECT id, title, date, date_update FROM " . TABLE_BLOG_ITEMS . " WHERE status = 1 AND language_id = '" . $languages[$i]['id'] . "' AND categories_id = '" . (int) $categories['id'] . "' ORDER BY position ASC");

            $lang_params = '';
            $datum = '';
            $update_date = '';
            $update = '';

            while ($items = xtc_db_fetch_array($items_query)) {
                $lang_param = ($languages[$i]['code'] != DEFAULT_LANGUAGE) ? '&language=' . $languages[$i]['code'] : '';

                $datum = explode('.', $categories['date']);
                $update_date = explode('.', $categories['date_update']);
                $date = mktime(0, 0, 0, $datum[1], $datum[0], $datum[2]);
                $update = mktime(0, 0, 0, $update_date[1], $update_date[0], $update_date[2]);

                echo "\t" . '<url>' . "\n";
                echo "\t\t" . '<loc>' . htmlspecialchars(utf8_encode(xtc_href_link(FILENAME_BLOG, 'blog_cat=' . $categories['id'] . '&blog_item=' . $items['id'] . $lang_param, 'NONSSL', false, SEARCH_ENGINE_FRIENDLY_URLS))) . '</loc>' . "\n";
                echo "\t\t" . '<priority>1.0</priority>' . "\n";
                echo "\t\t" . '<lastmod>' . iso8601_date((!empty($update)) ? $update : $date ) . '</lastmod>' . "\n";
                echo "\t\t" . '<changefreq>daily</changefreq>' . "\n";
                echo "\t" . '</url>' . "\n";
            }
        }
    }
}
#####################################################################
#####################################################################
echo '</urlset>' . "\n";
