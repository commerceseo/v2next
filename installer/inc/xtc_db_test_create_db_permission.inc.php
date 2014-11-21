<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_db_test_create_db_permission.inc.php 987 2014-04-22 10:40:42Z akausch $
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

function xtc_db_test_create_db_permission($database) {
    global $db_error;

    $db_created = false;
    $db_error = false;

    if (!$database) {
        $db_error = 'No Database selected.';
        return false;
    }

    if (!$db_error) {
        if (!@xtc_db_select_db($database)) {
            $db_created = true;
            if (!@xtc_db_query_installer_installer('create database ' . $database)) {
                $db_error = mysql_error();
            }
        } else {
            $db_error = mysql_error();
        }
        if (!$db_error) {
            if (@xtc_db_select_db($database)) {
                if (@xtc_db_query_installer('create table temp ( temp_id int(5) )')) {
                    if (@xtc_db_query_installer('drop table temp')) {
                        if ($db_created) {
                            if (@xtc_db_query_installer('drop database ' . $database)) {
                                
                            } else {
                                $db_error = mysql_error();
                            }
                        }
                    } else {
                        $db_error = mysql_error();
                    }
                } else {
                    $db_error = mysql_error();
                }
            } else {
                $db_error = mysql_error();
            }
        }
    }

    if ($db_error) {
        return false;
    } else {
        return true;
    }
}
