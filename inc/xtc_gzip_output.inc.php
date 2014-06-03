<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_gzip_output.inc.php 866 2014-03-17 12:07:35Z akausch $
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

/* $level = compression level 0-9, 0=none, 9=max */

function xtc_gzip_output($level = 5) {
    if ($encoding = xtc_check_gzip()) {
        $contents = ob_get_contents();
        ob_end_clean();

        header('Content-Encoding: ' . $encoding);

        $size = strlen($contents);
        $crc = crc32($contents);

        $contents = gzcompress($contents, $level);
        $contents = substr($contents, 0, strlen($contents) - 4);

        echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        echo $contents;
        echo pack('V', $crc);
        echo pack('V', $size);
    } else {
        ob_end_flush();
    }
}

function xtc_check_gzip() {
    if (headers_sent() || connection_aborted()) {
        return false;
    }

    if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)
        return 'x-gzip';
    if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)
        return 'gzip';

    return false;
}
