<?php

/* -----------------------------------------------------------------
 * 	$Id: request_port.php 582 2013-08-27 21:16:03Z akausch $
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

if ($_POST['action'] == 'setup_shop' || $_POST['action'] == 'create_account') {
    require('../includes/configure.php');
}

require_once('includes/application.php');
require_once('includes/FTPManager.inc.php');

$t_output = '';

switch ($_POST['action']) {
    case 'test_db_connection':

        $db = array();
        $db['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
        $db['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
        $db['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
        $db['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));

        xtc_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);

        if (!$db_error) {
            $t_select_db = xtc_db_select_db($db['DB_DATABASE']);
            if (!$t_select_db)
                $db_error = 'No database selected';
        } else {
            $t_output = 'no connection';
            break;
        }

        if (!$db_error) {
            $gm_show_tables = mysql_query("SHOW TABLES");
            $gm_tables = array();
            while ($row = mysql_fetch_array($gm_show_tables)) {
                $fp = fopen("txt/tables.txt", "r");
                $found = false;
                while ($line = fgets($fp, 1024)) {
                    if (trim($line) == $row[0])
                        $found = true;
                }
                fclose($fp);

                if (!$found)
                    $gm_tables[] = $row[0];
                else
                    $gm_tables[] = '<b class="error">' . $row[0] . '</b>';
            }

            if (!empty($gm_tables)) {
                $t_output = implode('<br />', $gm_tables);
            } else {
                $t_output = 'success';
            }
        } elseif ($db_error) {
            $t_output = 'no database';
        }

        break;

    case 'import_sql':
        include_once('includes/import_sql.php');
        break;

    case 'write_config':
        include_once('includes/write_config.php');
        break;

    case 'create_account':
        include_once('includes/create_account.php');
        $t_output = 'success';
        break;

    case 'setup_shop':
        include_once('includes/setup_shop.php');
        $t_output = 'success';
        break;

    case 'get_countries':
        require_once('../includes/configure.php');
        require_once(DIR_FS_INC . 'cseo_db.inc.php');
        require_once(DIR_FS_INC . 'cseo_form.inc.php');
        require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');

        // connect do database
        xtc_db_connect() or die('Unable to connect to database server!');
        $t_output = xtc_get_country_list('COUNTRY', (int) $_POST['COUNTRY'], '');
        break;

    case 'get_states':
        require_once('../includes/configure.php');
        require_once(DIR_FS_INC . 'cseo_db.inc.php');
        require_once(DIR_FS_INC . 'cseo_form.inc.php');
        require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');

        // connect do database
        xtc_db_connect() or die('Unable to connect to database server!');

        $check = xtc_db_fetch_array(xtc_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . (int) $_POST['COUNTRY'] . "'"));
        $entry_state_has_zones = ($check['total'] > 0);

        if ($check['total'] > 0) {
            $zones_array = array();
            $zones_query = xtc_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int) $_POST['COUNTRY'] . "' order by zone_name");
            while ($zones_values = xtc_db_fetch_array($zones_query)) {
                $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
            }
            $t_output = xtc_draw_pull_down_menu('STATE', $zones_array);
        } else {
            $t_output = xtc_draw_input_field('STATE');
        }

        break;

    case 'chmod_444':
        $t_output = 'failed';
        if (!empty($_SESSION['FTP_HOST']) && !empty($_SESSION['FTP_USER']) && !empty($_SESSION['FTP_PASSWORD']) && !empty($_SESSION['FTP_PATH'])) {
            $t_host = $_SESSION['FTP_HOST'];
            $t_user = $_SESSION['FTP_USER'];
            $t_password = $_SESSION['FTP_PASSWORD'];
            $t_pasv = false;
            if (!empty($_SESSION['FTP_PASV']))
                $t_pasv = true;

            $coo_ftp_manager = new FTPManager(true, $t_host, $t_user, $t_password, $t_pasv);

            if ($coo_ftp_manager->v_error == '') {
                $t_success = $coo_ftp_manager->chmod_444($_SESSION['FTP_PATH']);

                if ($t_success) {
                    $t_output = 'success';
                }
            }
        }

        break;
}

if (is_object($coo_ftp_manager)) {
    $coo_ftp_manager->quit();
}

echo $t_output;

@mysql_close();
