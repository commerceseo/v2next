<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_get_env_info.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function cseo_get_env_info($p_info) {
    $t_env_info = '';

    switch ($p_info) {
        case 'PHP_SELF':
            $t_env_info = $_SERVER['PHP_SELF'];

            if (strlen($_SERVER['PATH_INFO']) > 1) {
                $t_env_info = str_replace($_SERVER['PATH_INFO'], '', $t_env_info);

                if (empty($t_env_info)) {
                    if (!empty($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '.php') !== false) {
                        $t_env_info = $_SERVER['SCRIPT_NAME'];
                    } elseif (!empty($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], '.php') !== false) {
                        $t_env_info = $_SERVER['PHP_SELF'];
                    } elseif (!empty($_SERVER['SCRIPT_FILENAME']) && strpos($_SERVER['SCRIPT_FILENAME'], '.php') !== false && $_SERVER['DOCUMENT_ROOT'] != $_SERVER['SCRIPT_FILENAME']) {
                        $t_env_info = $_SERVER['SCRIPT_FILENAME'];
                        $t_env_info = str_replace($_SERVER['DOCUMENT_ROOT'], '', $t_env_info);
                    } elseif ($_SERVER['DOCUMENT_ROOT'] == $_SERVER['SCRIPT_FILENAME'] && defined('DIR_WS_CATALOG')) {
                        $t_filename = basename($_SERVER['SCRIPT_FILENAME']);
                        $t_env_info = DIR_WS_CATALOG . $t_filename;
                    }
                }
            }

            break;
        case 'REQUEST_URI':
            $t_env_info = $_SERVER['REQUEST_URI'];

            break;
        case 'SCRIPT_NAME':
            $t_env_info = $_SERVER['SCRIPT_NAME'];
            if (empty($t_env_info)) {
                $t_env_info = str_replace($_SERVER['DOCUMENT_ROOT'], '/', $_SERVER['SCRIPT_FILENAME']);

                if ($_SERVER['DOCUMENT_ROOT'] == $_SERVER['SCRIPT_FILENAME'] && defined('DIR_WS_CATALOG')) {
                    $t_filename = basename($_SERVER['SCRIPT_FILENAME']);
                    $t_env_info = DIR_WS_CATALOG . $t_filename;
                }

                if (empty($t_env_info)) {
                    $t_env_info = $_SERVER['PHP_SELF'];
                }
            }

            break;
        case 'PATH_INFO':
            $t_env_info = $_SERVER['PATH_INFO'];

            break;

        case 'MYSQL_VERSION':
            $t_sql = "SELECT VERSION() AS version";
            $t_result = @mysql_query($t_sql);
            $t_result_array = @mysql_fetch_array($t_result);

            if (isset($t_result_array['version'])) {
                $t_env_info = $t_result_array['version'];
            }

            break;
        default:
            trigger_error('cseo_get_env_info(): requested information not found: ' . $p_info);
    }

    return $t_env_info;
}
