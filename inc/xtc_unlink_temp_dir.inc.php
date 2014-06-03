<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_unlink_temp_dir.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Unlinks all subdirectories and files in $dir
// Works only on one subdir level, will not recurse
function xtc_unlink_temp_dir($dir) {
    $h1 = opendir($dir);
    while ($subdir = readdir($h1)) {
        // Ignore non directories
        if (!is_dir($dir . $subdir))
            continue;
        // Ignore . and .. and CVS
        if ($subdir == '.' || $subdir == '..' || $subdir == 'CVS')
            continue;
        // Loop and unlink files in subdirectory
        $h2 = opendir($dir . $subdir);
        while ($file = readdir($h2)) {
            if ($file == '.' || $file == '..')
                continue;
            @unlink($dir . $subdir . '/' . $file);
        }
        closedir($h2);
        @rmdir($dir . $subdir);
    }
    closedir($h1);
}
