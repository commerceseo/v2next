<?php

/* -----------------------------------------------------------------
 * 	$Id: class.classregistry.inc.php 522 2013-07-24 11:44:51Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2011 Gambio GmbH
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

class ClassRegistry extends Registry {
    /*
     * pattern for "which one is a class file"
     */

    var $v_file_pattern = ".php";
    var $v_samples_dir_pattern = "_samples";

    /*
     * constructor
     */

    function ClassRegistry() {
        if (is_object($GLOBALS['cseo_debugger']))
            $GLOBALS['cseo_debugger']->log('ClassRegistry() by ' . cseo_get_env_info('REQUEST_URI'), 'ClassRegistry');
    }

    function &get_instance() {
        static $s_instance;

        if ($s_instance === NULL) {
            $s_instance = new ClassRegistry();
        }
        return $s_instance;
    }

    /*
     * scan given dir recursively or not for classes ('.inc.php') and
     * set class name and path.
     * @param string $p_path  path for scan
     * @param bool $p_recursively  do it with or without
     * @return bool true:ok | false:error
     */

    function scan_dir($p_path, $p_recursively = false) {
        $t_coo_cached_directory = new CachedDirectory($p_path);
        #print_r($t_coo_handle);
        #var_dump($p_path);echo '<br>';
        if ($t_coo_cached_directory->is_dir($p_path) == false) {
            # p_path not a directory
            return false;
        } elseif (substr($p_path, strlen($this->v_samples_dir_pattern) * -1) == $this->v_samples_dir_pattern) {
            # p_path is samples-directory
            return false;
        }

        while (false !== ($t_entry = $t_coo_cached_directory->read() )) {
            if (substr($t_entry, 0, 1) == ".")
                continue;
            #	echo $v_entry.'<br>';

            $t_part = '/';
            if (substr($p_path, -1, 1) == $t_part) {
                $t_part = '';
            }

            if ($t_coo_cached_directory->is_dir($p_path . '/' . $t_entry) && $p_recursively) {
                $t_result = $this->scan_dir($p_path . $t_part . $t_entry, $p_recursively);
            } elseif (strpos($t_entry, $this->v_file_pattern, strlen($t_entry) - strlen($this->v_file_pattern)) > 0) {
                $t_class_name = strtok($t_entry, ".");
                $this->set($t_class_name, $p_path . '/' . $t_entry);
            }
        }
        // print_r($this->get_all_data());
        return true;
    }

}
