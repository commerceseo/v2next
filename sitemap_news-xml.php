<?php

/* -----------------------------------------------------------------
 * 	$Id: sitemap_news-xml.php 729 2013-11-11 16:46:55Z akausch $
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

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

$items_query = xtc_db_query("SELECT * FROM " . TABLE_BLOG_ITEMS . " WHERE status = 2 AND language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY id;");

$datum = '';
$update_date = '';
$update = '';

while ($items = xtc_db_fetch_array($items_query)) {
    $datum = explode('.', $items['date']);
    $update_date = explode('.', $items['date_update']);
    $date = mktime(0, 0, 0, $datum[1], $datum[0], $datum[2]);
    $update = mktime(0, 0, 0, $update_date[1], $update_date[0], $update_date[2]);

    echo "\t" . '<url>' . "\n";
    echo "\t\t" . '<loc>' . htmlspecialchars(utf8_encode(xtc_href_link(FILENAME_BLOG, 'blog_cat=' . $items['categories_id'] . '&blog_item=' . $items['item_id']))) . '</loc>' . "\n";

    echo "\t\t" . '<news:news>' . "\n";

    echo "\t\t\t" . '<news:publication>' . "\n";
    echo "\t\t\t\t" . '<news:name>' . HTTP_SERVER . ' - Blog</news:name>' . "\n";
    echo "\t\t\t\t" . '<news:language>' . $_SESSION['language_code'] . '</news:language>' . "\n";
    echo "\t\t\t" . '</news:publication>' . "\n";

    echo "\t\t\t" . '<news:access>Subscription</news:access>' . "\n";
    echo "\t\t\t" . '<news:genres>Blog</news:genres>' . "\n";
    echo "\t\t\t" . '<news:publication_date>' . date('c', ((!empty($update)) ? $update : $date)) . '</news:publication_date>' . "\n";
    echo "\t\t\t" . '<news:title>' . $items['name'] . '</news:title>' . "\n";

    if (!empty($items['meta_keywords']))
        echo "\t\t\t" . '<news:keywords>' . $items['meta_keywords'] . '</news:keywords>' . "\n";

    echo "\t\t" . '</news:news>' . "\n";
    echo "\t" . '</url>' . "\n";
}
echo "</urlset>";