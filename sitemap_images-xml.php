<?php

/* -----------------------------------------------------------------
 * 	$Id: sitemap_images-xml.php 729 2013-11-11 16:46:55Z akausch $
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

function iso8601_date($timestamp) {
    if (PHP_VERSION < 5) {
        $tzd = date('O', $timestamp);
        $tzd = substr(chunk_split($tzd, 3, ':'), 0, 6);
        return date('Y-m-d\TH:i:s', $timestamp) . $tzd;
    } else {
        return date('c', $timestamp);
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";


#####################################################################
# PRODUKTE
#####################################################################

$product_query = xtc_db_query("SELECT 
								products_id, 
								products_image, 
								UNIX_TIMESTAMP(products_date_added) AS products_date_added, 
								UNIX_TIMESTAMP(products_last_modified) AS products_last_modified  
								FROM 
									" . TABLE_PRODUCTS . "
								WHERE 
									products_status = '1'
								AND 
									products_image != ''
								;");
// echo xtc_db_num_rows($product_query);die();
if (xtc_db_num_rows($product_query)) {
    while ($product = xtc_db_fetch_array($product_query)) {
        $date = ($product['products_last_modified'] != NULL) ? iso8601_date($product['products_last_modified']) : iso8601_date($product['products_date_added']);
        $data .= "\t" . '<url>' . "\n";
        $data .= "\t\t" . '<loc>' . xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($product['products_id']), 'NONSSL', false, SEARCH_ENGINE_FRIENDLY_URLS) . '</loc>' . "\n";
        $data .= "\t\t" . '<image:image>' . "\n";
        $data .= "\t\t" . ' <image:loc>' . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_INFO_IMAGES . $product['products_image'] . '</image:loc>' . "\n";
        $data .= "\t\t" . '</image:image>' . "\n";
        $data .= "\t\t" . '<priority>1.0</priority>' . "\n";
        $data .= "\t\t" . '<lastmod>' . $date . '</lastmod>' . "\n";
        $data .= "\t\t" . '<changefreq>daily</changefreq>' . "\n";
        $data .= "\t" . '</url>' . "\n";
    }
    echo $data;
}


#####################################################################
# KATEGORIEN
#####################################################################

$cat_result = xtc_db_query("SELECT 
								categories_id, 
								categories_image, 
								parent_id,  
								UNIX_TIMESTAMP(date_added) AS date_added, 
								UNIX_TIMESTAMP(last_modified) AS last_modified 
							FROM 
								" . TABLE_CATEGORIES . " 
							WHERE 
								categories_status = '1'
							AND
								categories_image != ''
								;");

if (xtc_db_num_rows($cat_result)) {
    while ($cat_data = xtc_db_fetch_array($cat_result)) {
        $date = ($cat_data['last_modified'] != NULL) ? iso8601_date($cat_data['last_modified']) : iso8601_date($cat_data['date_added']);
        $data .= "\t" . '<url>' . "\n";
        $data .= "\t\t" . '<loc>' . xtc_href_link(FILENAME_DEFAULT, xtc_category_link($cat_data['categories_id'], $cat_data['code']), 'NONSSL', false, SEARCH_ENGINE_FRIENDLY_URLS) . '</loc>' . "\n";
        $data .= "\t\t" . '<image:image>' . "\n";
        $data .= "\t\t" . ' <image:loc>' . HTTP_SERVER . DIR_WS_CATALOG . 'categories/' . $cat_data['categories_image'] . '</image:loc>' . "\n";
        $data .= "\t\t" . '</image:image>' . "\n";
        $data .= "\t\t" . '<priority>0.8</priority>' . "\n";
        $data .= "\t\t" . '<lastmod>' . $date . '</lastmod>' . "\n";
        $data .= "\t\t" . '<changefreq>daily</changefreq>' . "\n";
        $data .= "\t" . '</url>' . "\n";
    }
    echo $data;
}

echo '</urlset>' . "\n";
