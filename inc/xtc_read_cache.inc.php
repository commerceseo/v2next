<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_read_cache.inc.php 866 2014-03-17 12:07:35Z akausch $
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

//! Read in seralized data.
//  read_cache reads the serialized data in $filename and
//  fills $var using unserialize().
//  $var      -  The variable to be filled.
//  $filename -  The name of the file to read.
function read_cache(&$var, $filename, $auto_expire = false) {
    $filename = DIR_FS_CACHE . $filename;
    $success = false;

    if (($auto_expire == true) && file_exists($filename)) {
        $now = time();
        $filetime = filemtime($filename);
        $difference = $now - $filetime;

        if ($difference >= $auto_expire) {
            return false;
        }
    }

// try to open file
    if ($fp = @fopen($filename, 'r')) {
// read in serialized data
        $szdata = fread($fp, filesize($filename));
        fclose($fp);
// unserialze the data
        $var = unserialize($szdata);

        $success = true;
    }

    return $success;
}
