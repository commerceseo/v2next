<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_sql_query.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function cseo_sql_query($sql_file, $langID) {
    global $db_error;

    $db_error = false;

    if (file_exists($sql_file)) {
        $fd = fopen($sql_file, 'rb');
        $restore_query = fread($fd, filesize($sql_file));
        fclose($fd);
    } else {
        $db_error = 'SQL Datei existiert nicht: ' . $sql_file;
        return false;
    }

    $sql_array = array();
    $sql_length = strlen($restore_query);
    $pos = strpos($restore_query, ';');
    for ($i = $pos; $i < $sql_length; $i++) {
        if ($restore_query[0] == '#') {
            $restore_query = ltrim(substr($restore_query, strpos($restore_query, "\n")));
            $sql_length = strlen($restore_query);
            $i = strpos($restore_query, ';') - 1;
            continue;
        }
        if ($restore_query[($i + 1)] == "\n") {
            for ($j = ($i + 2); $j < $sql_length; $j++) {
                if (trim($restore_query[$j]) != '') {
                    $next = substr($restore_query, $j, 6);
                    if ($next[0] == '#') {
                        $query = substr($restore_query, 0, $i + 1);
                        $restore_query = substr($restore_query, $k);
                        $restore_query = $query . $restore_query;
                        $sql_length = strlen($restore_query);
                        $i = strpos($restore_query, ';') - 1;
                        continue 2;
                    }
                    break;
                }
            }
            if ($next == '')
                $next = 'insert';

            if (preg_match("/create/i", $next) || preg_match("/insert/i", $next) || preg_match("/drop t/i", $next)) {
                $next = '';
                $sql_array[] = substr($restore_query, 0, $i);
                $restore_query = ltrim(substr($restore_query, $i + 1));
                $sql_length = strlen($restore_query);
                $i = strpos($restore_query, ';') - 1;
            }
        }
    }

    for ($i = 0; $i < sizeof($sql_array); $i++) {
        $sql_array[$i] = str_replace('#lang_id#', $langID, $sql_array[$i]);
        $sql_array[$i] = str_replace('#store_mail#', EMAIL_FROM, $sql_array[$i]);
        $sql_array[$i] = str_replace('#store_name#', STORE_NAME, $sql_array[$i]);
        mysql_query($sql_array[$i]);
    }
}
