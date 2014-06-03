<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_download.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// safe download function, file get renamed bevor sending to browser!!
function xtc_get_download($content_id) {
    $content_data = xtc_db_fetch_array(xtc_db_query("SELECT content_file, content_read FROM " . TABLE_PRODUCTS_CONTENT . " WHERE content_id='" . (int) $content_id . "';"));
    // update file counter
    xtc_db_query("UPDATE " . TABLE_PRODUCTS_CONTENT . " SET content_read='" . ($content_data['content_read'] + 1) . "' WHERE content_id='" . (int) $content_id . "';");

    // original filename
    $filename = DIR_FS_CATALOG . 'media/products/' . $content_data['content_file'];
    $backup_filename = DIR_FS_CATALOG . 'media/products/backup/' . $content_data['content_file'];
    // create md5 hash id from original file
    $orign_hash_id = md5_file($filename);

    clearstatcache();

    // create new filename with timestamp
    $timestamp = str_replace('.', '', microtime());
    $timestamp = str_replace(' ', '', $timestamp);
    $new_filename = DIR_FS_CATALOG . 'media/products/' . $timestamp . strstr($content_data['content_file'], '.');

    // rename file
    rename($filename, $new_filename);

    if (file_exists($new_filename)) {
        header("Content-type: application/force-download");
        header("Content-Disposition: attachment; filename=" . $new_filename);
        @readfile($new_filename);
        rename($new_filename, $filename);
        $new_hash_id = md5_file($filename);
        clearstatcache();
        // check hash id of file again, if not same, get backup!
        if ($new_hash_id != $orign_hash_id)
            copy($backup_filename, $filename);
    }
}
