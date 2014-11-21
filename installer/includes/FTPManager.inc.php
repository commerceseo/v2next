<?php

/* -----------------------------------------------------------------
 * 	$Id: FTPManager.inc.php 987 2014-04-22 10:40:42Z akausch $
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

class FTPManager {

    var $v_connect_id = null;
    var $v_error = '';
    var $v_data_array = array();
    var $v_wrong_chmod_array = array();

    function FTPManager($p_connect = true, $p_host, $p_user, $p_password, $p_pasv = true) {
        if ($p_connect == true) {
            $this->connect($p_host, $p_user, $p_password, $p_pasv);
        }
    }

    function connect($p_host, $p_user, $p_password, $p_pasv) {
        $t_connect_id = @ftp_connect($p_host) or $this->v_error .= sprintf(ERROR_FTP_CONNECTION, $p_host);
        if ($t_connect_id !== false) {
            ftp_pasv($t_connect_id, $p_pasv);
            $t_login = @ftp_login($t_connect_id, $p_user, $p_password) or $this->v_error .= sprintf(ERROR_FTP_DATA, $p_user);
        }

        $this->v_connect_id = $t_connect_id;
    }

    function isdir($p_dir) {
        if (@ftp_chdir($this->v_connect_id, $p_dir)) {
            ftp_cdup($this->v_connect_id);
            return true;
        } else {
            return false;
        }
    }

    function get_directories($p_dir, $p_no_parent_dirs = false) {
        $t_list_array = ftp_nlist($this->v_connect_id, $p_dir);
        $t_final_list = array();
        for ($i = 0; $i < count($t_list_array); $i++) {
            if ($this->isdir($t_list_array[$i])) {
                if ($p_no_parent_dirs) {
                    $t_final_list[] = substr(strrchr($t_list_array[$i], '/'), 1);
                } else {
                    $t_final_list[] = $t_list_array[$i];
                }
            }
        }

        sort($t_final_list);

        return $t_final_list;
    }

    function get_dir_content($p_dir) {
        return ftp_nlist($this->v_connect_id, $p_dir);
    }

    function quit() {
        $t_success = false;

        if ($this->v_connect_id !== false && $this->v_connect_id !== null) {
            $t_success = ftp_quit($this->v_connect_id);
        }

        return $t_success;
    }

    function is_shop($p_dir) {
        $t_found_shop = false;

        if ($this->v_connect_id !== false) {
            $t_includes_dir_content_array = $this->get_dir_content($p_dir . '/includes');

            for ($i = 0; $i < count($t_includes_dir_content_array); $i++) {
                if (strpos($t_includes_dir_content_array[$i], 'application_top.php') !== false) {
                    $t_found_shop = true;
                    break;
                }
            }
        }

        return $t_found_shop;
    }

    function chmod_777($p_dir) {
        $fp = fopen(DIR_FS_CATALOG . 'installer/txt/chmod.txt', 'r');

        while ($t_line = fgets($fp, 1024)) {
            $t_line = trim($t_line);
            if (strlen($t_line) > 0) {
                $t_line = str_replace('\\', '/', $t_line);
                if (substr($t_line, 0, 1) != '/') {
                    $t_line = '/' . $t_line;
                }
                $t_mode = @ftp_chmod($this->v_connect_id, 0777, $p_dir . $t_line);
            }
        }
        fclose($fp);

        $fp = fopen(DIR_FS_CATALOG . 'installer/txt/chmod_all.txt', 'r');

        while ($t_line = fgets($fp, 1024)) {
            $t_line = trim($t_line);
            if (strlen($t_line) > 0) {
                $t_line = str_replace('\\', '/', $t_line);
                if (substr($t_line, 0, 1) != '/') {
                    $t_line = '/' . $t_line;
                }

                if (substr($p_dir, -1) == '/') {
                    $t_line = substr($p_dir, 0, -1) . $t_line;
                } else {
                    $t_line = $p_dir . $t_line;
                }

                $this->v_data_array = array();
                $this->recursive_ftpn_list($t_line);

                $t_mode = @ftp_chmod($this->v_connect_id, 0777, trim($t_line));
                for ($i = 0; $i < count($this->v_data_array); $i++) {
                    $t_mode = @ftp_chmod($this->v_connect_id, 0777, trim($this->v_data_array[$i]));
                }
            }
        }

        $_SESSION['FTP_PATH'] = $p_dir;

        fclose($fp);
    }

    function recursive_ftpn_list($p_dir, $p_directories = true, $p_files = true, $p_exclude = array('index.html', '.htaccess', '.', '..')) {
        $t_list_array = ftp_nlist($this->v_connect_id, $p_dir);
        for ($i = 0; $i < count($t_list_array); $i++) {
            if (strrchr($t_list_array[$i], '/') !== false) {
                $t_name = substr(strrchr($t_list_array[$i], '/'), 1);
            } else {
                $t_name = $t_list_array[$i];
            }
            if (!in_array($t_name, $p_exclude)) {
                if ($this->isdir($t_list_array[$i]) && $p_directories) {
                    $this->v_data_array[] = $t_list_array[$i];
                    $this->recursive_ftpn_list($t_list_array[$i], $p_directories, $p_files, $p_exclude);
                }

                if (!$this->isdir($t_list_array[$i]) && $p_files) {
                    $this->v_data_array[] = $t_list_array[$i];
                }
            }
        }

        return $this->v_data_array;
    }

    function chmod_444($p_dir) {
        // @chmod(DIR_FS_CATALOG . 'admin/includes/configure.org.php', 0444);
        @chmod(DIR_FS_CATALOG . 'admin/includes/configure.php', 0444);
        // @chmod(DIR_FS_CATALOG . 'includes/configure.org.php', 0444);
        @chmod(DIR_FS_CATALOG . 'includes/configure.php', 0444);

        // $t_mode = @ftp_chmod($this->v_connect_id, 0444, $p_dir . '/admin/includes/configure.org.php');
        $t_mode = @ftp_chmod($this->v_connect_id, 0444, $p_dir . '/admin/includes/configure.php');
        // $t_mode = @ftp_chmod($this->v_connect_id, 0444, $p_dir . '/includes/configure.org.php');
        $t_mode = @ftp_chmod($this->v_connect_id, 0444, $p_dir . '/includes/configure.php');
    }

    function check_chmod() {
        $this->wrong_chmod_array = array();

        $fp = fopen(DIR_FS_CATALOG . 'installer/txt/chmod.txt', 'r');

        while ($t_line = fgets($fp, 1024)) {
            $t_line = trim($t_line);
            if (strlen($t_line) > 0) {
                $t_line = str_replace('\\', '/', $t_line);
                if (substr($t_line, 0, 1) == '/') {
                    $t_line = substr($t_line, 1);
                }

                @chmod(DIR_FS_CATALOG . $t_line, 0777);
                if (@!is_writeable(DIR_FS_CATALOG . $t_line) && @file_exists(DIR_FS_CATALOG . $t_line)) {
                    $this->wrong_chmod_array[] = DIR_FS_CATALOG . $t_line;
                }
            }
        }
        fclose($fp);

        $fp = fopen(DIR_FS_CATALOG . 'installer/txt/chmod_all.txt', 'r');

        while ($t_line = fgets($fp, 1024)) {
            $t_line = trim($t_line);
            if (strlen($t_line) > 0) {
                $t_line = str_replace('\\', '/', $t_line);
                if (substr($t_line, 0, 1) == '/') {
                    $t_line = substr($t_line, 1);
                }

                @chmod(DIR_FS_CATALOG . $t_line, 0777);
                if (@file_exists(DIR_FS_CATALOG . $t_line)) {
                    if (@!is_writeable(DIR_FS_CATALOG . $t_line)) {
                        $this->wrong_chmod_array[] = DIR_FS_CATALOG . $t_line;
                    }

                    $this->recursive_check_chmod(DIR_FS_CATALOG . $t_line);
                }
            }
        }

        return $this->wrong_chmod_array;
    }

    function recursive_check_chmod($p_dir, $p_exclude = array('index.html', '.htaccess')) {
        if (substr($p_dir, -1) != '/') {
            $p_dir .= '/';
        }

        if (is_dir($p_dir)) {
            if ($t_dh = opendir($p_dir)) {
                while (($t_file = readdir($t_dh)) !== false) {
                    if ($t_file != '.' && $t_file != '..' && !in_array($t_file, $p_exclude)) {
                        @chmod($p_dir . $t_file, 0777);
                        if (!is_writeable($p_dir . $t_file)) {
                            $this->wrong_chmod_array[] = $p_dir . $t_file;
                            if (is_dir($p_dir . $t_file)) {
                                $this->recursive_check_chmod($p_dir . $t_file, $p_exclude);
                            }
                        }
                    }
                }
                closedir($t_dh);
            }
        }

        return $this->wrong_chmod_array;
    }

}
