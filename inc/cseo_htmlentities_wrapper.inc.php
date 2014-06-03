<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_htmlentities_wrapper.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function htmlentities_wrapper($p_string, $p_flags = false, $p_encoding = '', $p_double_encode = true) {
    $t_flags = $p_flags;
    if ($p_flags === false) {
        if (defined('ENT_HTML401')) {
            $t_flags = ENT_COMPAT | ENT_HTML401;
        } else {
            $t_flags = ENT_COMPAT;
        }
    }

    $t_encoding = $p_encoding;
    if ($p_encoding === '') {
        // search for UTF-8 characters
        if (preg_match('/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/xs', $p_string)) {
            $t_encoding = 'UTF-8';
        } elseif (isset($_SESSION['language_charset'])) {
            $t_allowed_charsets_array = array();
            $t_allowed_charsets_array[] = 'ISO-8859-1';
            $t_allowed_charsets_array[] = 'ISO8859-1';
            $t_allowed_charsets_array[] = 'ISO-8859-15';
            $t_allowed_charsets_array[] = 'ISO8859-15';
            $t_allowed_charsets_array[] = 'UTF-8';
            $t_allowed_charsets_array[] = 'cp866';
            $t_allowed_charsets_array[] = 'ibm866';
            $t_allowed_charsets_array[] = '866';
            $t_allowed_charsets_array[] = 'cp1251';
            $t_allowed_charsets_array[] = 'Windows-1251';
            $t_allowed_charsets_array[] = 'win-1251';
            $t_allowed_charsets_array[] = '1251';
            $t_allowed_charsets_array[] = 'cp1252';
            $t_allowed_charsets_array[] = 'Windows-1252';
            $t_allowed_charsets_array[] = '1252';
            $t_allowed_charsets_array[] = 'KOI8-R';
            $t_allowed_charsets_array[] = 'koi8-ru';
            $t_allowed_charsets_array[] = 'koi8r';
            $t_allowed_charsets_array[] = 'BIG5';
            $t_allowed_charsets_array[] = '950';
            $t_allowed_charsets_array[] = 'GB2312';
            $t_allowed_charsets_array[] = '936';
            $t_allowed_charsets_array[] = 'BIG5-HKSCS';
            $t_allowed_charsets_array[] = 'Shift_JIS';
            $t_allowed_charsets_array[] = 'SJIS';
            $t_allowed_charsets_array[] = '932';
            $t_allowed_charsets_array[] = 'EUC-JP';
            $t_allowed_charsets_array[] = 'EUCJP';

            $t_key = array_search(strtolower(trim((string) $_SESSION['language_charset'])), array_map('strtolower', $t_allowed_charsets_array));
            if ($t_key !== false) {
                $t_encoding = $t_allowed_charsets_array[$t_key];
            }
        } else {
            $t_encoding = 'ISO-8859-1';
        }
    }

    if (version_compare(PHP_VERSION, '5.2.3', '<')) {
        return htmlentities($p_string, $t_flags, $t_encoding);
    } else {
        return htmlentities($p_string, $t_flags, $t_encoding, $p_double_encode);
    }
}
