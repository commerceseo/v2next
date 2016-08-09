<?php

/* -----------------------------------------------------------------
 * 	$Id: application.php 1462 2015-07-08 18:10:15Z akausch $
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

$t_timezone = @date_default_timezone_get();
if (is_string($t_timezone) && !empty($t_timezone)) {
    @date_default_timezone_set($t_timezone);
}
unset($t_timezone);
// set the level of error reporting
if (function_exists('ini_set')) {
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set("log_errors" , "1");
	ini_set("error_log" , DIR_FS_CATALOG . "logfiles/InstallErrors.log.txt");
	ini_set("display_errors" , "0"); 
}
function gm_delete_get_parameters($string) {
    if (strpos($string, '?') !== false) {
        $string = substr($string, 0, strpos($string, '?'));
    }
    return $string;
}

function gm_magic_check($string) {
    if (preg_match('/(^"|[^\\\]{1}")/', $string) == 1)
        return false;
    if (preg_match('/(^\'|[^\\\]{1}\')/', $string) == 1)
        return false;
    else
        return true;
}

function gm_prepare_string($string, $strip = false) {
    if (!$strip) {
        if (ini_get('magic_quotes_gpc') == 0 || ini_get('magic_quotes_gpc') == 'Off' || ini_get('magic_quotes_gpc') == 'off') {
            if (!gm_magic_check($string))
                $string = addslashes($string);
        }
    } else {
        if (ini_get('magic_quotes_gpc') == 1 || ini_get('magic_quotes_gpc') == 'On' || ini_get('magic_quotes_gpc') == 'on')
            $string = stripslashes($string);
        else {
            if (gm_magic_check($string))
                $string = stripslashes($string);
        }
    }
    return $string;
}

function gm_document_root() {
    if (file_exists(getcwd() . '/index.php')) {
        $gm_relative = $_SERVER['PHP_SELF'];
        if (empty($gm_relative))
            $gm_relative = $_SERVER['SCRIPT_NAME'];
        if (empty($gm_relative))
            $gm_relative = $_SERVER['REQUEST_URI'];
        if (empty($gm_relative))
            return $_SERVER['DOCUMENT_ROOT'];

        $gm_relative = gm_delete_get_parameters($gm_relative);

        $gm_pos = strrpos(getcwd(), dirname($gm_relative));
        $gm_document_root = substr(getcwd(), 0, $gm_pos);
    }
    elseif (file_exists(__FILE__)) {
        $gm_relative = $_SERVER['PHP_SELF'];
        if (empty($gm_relative))
            $gm_relative = $_SERVER['SCRIPT_NAME'];
        if (empty($gm_relative))
            $gm_relative = $_SERVER['REQUEST_URI'];
        if (empty($gm_relative))
            return $_SERVER['DOCUMENT_ROOT'];

        $gm_relative = gm_delete_get_parameters($gm_relative);

        $gm_pos = strrpos(dirname(__FILE__), dirname($gm_relative));
        $gm_document_root = substr(dirname(__FILE__), 0, $gm_pos);
    } else
        $gm_document_root = $_SERVER['DOCUMENT_ROOT'];

    $gm_document_root = str_replace("\\", '/', $gm_document_root);
    $gm_document_root = str_replace('//', '/', $gm_document_root);

    return $gm_document_root;
}

function cseo_local_install_path() {
    if (file_exists(getcwd() . '/index.php')) {
        $gm_pos = strrpos(getcwd(), 'installer');
        $cseo_local_install_path = substr(getcwd(), 0, $gm_pos);
    } elseif (file_exists(__FILE__)) {
        $gm_pos = strrpos(dirname(__FILE__), 'installer');
        $cseo_local_install_path = substr(dirname(__FILE__), 0, $gm_pos);
    } else {
        $gm_relative = $_SERVER['PHP_SELF'];
        if (empty($gm_relative))
            $gm_relative = $_SERVER['SCRIPT_NAME'];
        if (empty($gm_relative))
            $gm_relative = $_SERVER['REQUEST_URI'];
        $gm_relative = gm_delete_get_parameters($gm_relative);
        $local_install_path = str_replace('/installer', '', $gm_relative);
        $local_install_path = str_replace('index.php', '', $local_install_path);
        $cseo_local_install_path = $_SERVER['DOCUMENT_ROOT'] . $local_install_path;
    }

    $cseo_local_install_path = str_replace("\\", '/', $cseo_local_install_path);
    $cseo_local_install_path = str_replace('//', '/', $cseo_local_install_path);

    return $cseo_local_install_path;
}

if (!defined('DIR_FS_DOCUMENT_ROOT')) {
    define('DIR_FS_DOCUMENT_ROOT', gm_document_root());
    define('DIR_FS_CATALOG', cseo_local_install_path());
    $gm_relative = $_SERVER['PHP_SELF'];
    if (empty($gm_relative))
        $gm_relative = $_SERVER['SCRIPT_NAME'];
    if (empty($gm_relative))
        $gm_relative = $_SERVER['REQUEST_URI'];
    $gm_relative = gm_delete_get_parameters($gm_relative);
    $local_install_path = str_replace('/installer', '', $gm_relative);
    $local_install_path = str_replace('index.php', '', $local_install_path);
}
if (!defined('DIR_FS_INC'))
    define('DIR_FS_INC', DIR_FS_CATALOG . 'inc/');


require(DIR_FS_CATALOG . 'includes/classes/class.boxes.php');
require(DIR_FS_CATALOG . 'includes/classes/class.message_stack.php');
require(DIR_FS_CATALOG . 'includes/filenames.php');
require(DIR_FS_CATALOG . 'includes/database_tables.php');
require_once(DIR_FS_CATALOG . 'inc/xtc_image.inc.php');


# Session Handling

$t_session_started = true;

@session_start();
$_SESSION['session_test'] = true;
@session_write_close();

@session_start();

if (!isset($_SESSION['session_test'])) {
    @session_write_close();

    $t_session_save_path = (string) ini_get('upload_tmp_dir');
    @session_save_path($t_session_save_path);
    @session_start();
    $_SESSION['session_test'] = true;
    @session_write_close();

    @session_save_path($t_session_save_path);
    @session_start();

    if (!isset($_SESSION['session_test'])) {
        @session_write_close();

        $t_session_save_path = cseo_local_install_path() . 'cache';
        @session_save_path($t_session_save_path);
        @session_start();
        $_SESSION['session_test'] = true;
        @session_write_close();

        @session_save_path($t_session_save_path);
        @session_start();

        if (!isset($_SESSION['session_test'])) {
            $t_session_started = false;
        }
    }
}

unset($_SESSION['session_test']);


# set installer language
if (isset($_GET['language'])) {
    switch ($_GET['language']) {
        case 'english':
            $_SESSION['language'] = 'english';
            break;
        default:
            $_SESSION['language'] = 'german';
    }
}

// BOF GM_MOD
if (empty($_SESSION['language'])) {
    $_SESSION['language'] = 'german';
}
// EOF GM_MOD
// Set the level of error reporting
if (defined('E_DEPRECATED')) {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
} else {
    error_reporting(E_ALL & ~E_NOTICE);
}

// include General functions
require_once(DIR_FS_INC . 'xtc_set_time_limit.inc.php');
require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
require_once(DIR_FS_INC . 'xtc_in_array.inc.php');

// Include Database functions for installer
require_once(DIR_FS_INC . 'cseo_db.inc.php');

require_once('inc/xtc_db_connect_installer.inc.php');
require_once('inc/xtc_db_select_db.inc.php');
require_once('inc/xtc_db_query_installer.inc.php');
require_once('inc/xtc_db_test_create_db_permission.inc.php');
require_once('inc/xtc_db_test_connection.inc.php');
require_once('inc/xtc_db_install.inc.php');

// include Html output functions
require_once('inc/xtc_draw_hidden_field_installer.inc.php');

if (!defined('DIR_WS_ICONS'))
    define('DIR_WS_ICONS', 'images/');
