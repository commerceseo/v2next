<?php

/* -----------------------------------------------------------------
 * 	$Id: import_sql.php 420 2013-06-19 18:04:39Z akausch $
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

if (!empty($_GET['db']) || xtc_in_array('database', $_POST['install'])) {
    $db = array();

    $db['DB_SERVER'] = trim(gm_prepare_string($_POST['DB_SERVER'], true));
    $db['DB_SERVER_USERNAME'] = trim(gm_prepare_string($_POST['DB_SERVER_USERNAME'], true));
    $db['DB_SERVER_PASSWORD'] = trim(gm_prepare_string($_POST['DB_SERVER_PASSWORD'], true));
    $db['DB_DATABASE'] = trim(gm_prepare_string($_POST['DB_DATABASE'], true));

    xtc_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);

    mysql_query("SET names utf8");
    xtc_db_query_installer('ALTER DATABASE ' . $db['DB_DATABASE'] . ' CHARACTER SET utf8;');
    xtc_db_query_installer('ALTER DATABASE ' . $db['DB_DATABASE'] . ' COLLATE utf8_general_ci;');
    xtc_db_query_installer('SET storage_engine = MyISAM;');

    $db_error = false;

    if ($_GET['db'] == 'css1') {
        $sql_file = DIR_FS_CATALOG . 'installer/sql/cseo_zones.sql';
    } elseif ($_GET['db'] == 'css2') {
        $sql_file = DIR_FS_CATALOG . 'installer/sql/cseo_country.sql';
    } elseif ($_GET['db'] == 'css3') {
        $sql_file = DIR_FS_CATALOG . 'installer/sql/cseo_adminnav.sql';
    } elseif ($_GET['db'] == 'css4') {
        $sql_file = DIR_FS_CATALOG . 'installer/sql/cseo_addons.sql';
    } elseif ($_GET['db'] == 'blz') {
        $sql_file = DIR_FS_CATALOG . 'installer/sql/blz.sql';
    } elseif ($_GET['db'] == 'lang') {
        $sql_file = DIR_FS_CATALOG . 'installer/sql/cseo_insert1.sql';
    } elseif ($_GET['db'] == 'lang2') {
        $sql_file = DIR_FS_CATALOG . 'installer/sql/cseo_lang.sql';
    } elseif (xtc_in_array('database', $_POST['install'])) {
        $sql_file = DIR_FS_CATALOG . 'installer/sql/cseov2plus.sql';
    }

    xtc_db_install($db['DB_DATABASE'], $sql_file);
}

if (!$db_error) {
    $t_output = 'success';
} else {
    $t_output = $db_error;
}

@mysql_close();
