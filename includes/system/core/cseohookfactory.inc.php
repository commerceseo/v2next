<?php

/* -----------------------------------------------------------------
 * 	$Id: cseohookfactory.inc.php 522 2013-07-24 11:44:51Z akausch $
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

class cseohookfactory {

    public static $s_coo_class_registry = NULL;
    public static $s_coo_overload_registry = NULL;

    function __construct() {
        
    }

    public static function load_origin_class($p_class_name, $p_class_path = false) {
        // cancel, if requested class is already defined
        if (class_exists($p_class_name, false) == true)
            return;

        $t_base_class_name = $p_class_name . '_ORIGINAL';
        $t_base_class_file = $p_class_path;

        // source for alias of requested class; use base as default
        $t_operation_class_name = $t_base_class_name;

        // include base class, if class_path given
        if ($p_class_path !== false && class_exists($t_base_class_name, false) == false)
            require_once($t_base_class_file);
        // ELSE: included base_class_file is not an _ORIGINAL
        if (class_exists($t_base_class_name, false) == true) {
            # check class chain and init
            $coo_overload_registry = self::get_overload_registry();
            $coo_overload_registry->init_class_chain($t_base_class_name, $p_class_name);

            // class chain found?
            $t_overload_class_name = $coo_overload_registry->get($t_base_class_name);
            if ($t_overload_class_name != NULL) {
                // use overloading class from chain
                if (is_object($GLOBALS['cseo_debugger']))
                    $GLOBALS['cseo_debugger']->log('overload found: ' . $p_class_name . ' => ' . $t_overload_class_name, 'class_overloading');
                $t_operation_class_name = $t_overload_class_name;
            }

            # prepare extended "_origin"
            $t_eval_code = 'class ' . $p_class_name . ' extends ' . $t_operation_class_name . ' {}';
            eval($t_eval_code);
        }
    }

    public static function get_class_registry() {
        $coo_class_registry = self::$s_coo_class_registry;
        // print_r($coo_class_registry);
        if ($coo_class_registry === NULL) {
            # try building object from cache
            $t_cache_key = 'ClassRegistry';
            $coo_cache = & DataCache::get_instance();
            if ($coo_cache->key_exists($t_cache_key, true)) {
                #use cached object
                $coo_class_registry = $coo_cache->get_data($t_cache_key);
            } else {
                # build new registry object
                # directories to be scanned by ClassRegistry
                $t_scan_dirs_array = array(
                    // DIR_FS_CATALOG.'admin/includes/classes',
                    DIR_FS_CATALOG . 'includes/system/Extender',
                    DIR_FS_CATALOG . 'includes/system/ClassOveroloads',
                    DIR_FS_CATALOG . 'includes/plugins/Extender',
                    DIR_FS_CATALOG . 'includes/plugins/ClassOveroloads'
                );
                // print_r($t_scan_dirs_array);
                $coo_class_registry = & ClassRegistry::get_instance();

                foreach ($t_scan_dirs_array as $t_dir_item) {
                    $coo_class_registry->scan_dir($t_dir_item, true);
                }
                // echo '<pre>';
                // print_r($coo_class_registry);
                // echo '</pre>';
                #write object to cache
                $coo_cache->set_data($t_cache_key, $coo_class_registry, true);
            }
            // print_r ($coo_class_registry);die;
            self::$s_coo_class_registry = $coo_class_registry;
            // echo '<pre>';
            // print_r($coo_class_registry);
            // echo '</pre>';
        }
        //print_r($coo_class_registry->get_all_data());
        return $coo_class_registry;
    }

    public static function get_overload_registry() {
        $coo_overload_registry = self::$s_coo_overload_registry;
        if ($coo_overload_registry === NULL) {
            $coo_overload_registry = & ClassOverloadRegistry::get_instance();
            $coo_overload_registry->set_class_overload_system_dir(DIR_FS_CATALOG . 'includes/system/ClassOveroloads/');
            $coo_overload_registry->set_class_overload_user_dir(DIR_FS_CATALOG . 'includes/plugins/ClassOveroloads/');
            $coo_overload_registry->set_extender_system_dir(DIR_FS_CATALOG . 'includes/system/Extender/');
            $coo_overload_registry->set_extender_user_dir(DIR_FS_CATALOG . 'includes/plugins/Extender/');

            self::$s_coo_overload_registry = $coo_overload_registry;
        }
        // echo '<pre>';
        // print_r($coo_overload_registry);
        // echo '</pre>';
        return $coo_overload_registry;
    }

    public static function load_class($p_class_name) {
        # PART I: load requested base class
        $coo_class_registry = self::get_class_registry();
        // echo '<pre>';
        // print_r($coo_class_registry);
        // echo '</pre>';
        # include  definition of requested class
        if (class_exists($p_class_name, false) == false) {
            # get file path to given class
            $t_class_file = $coo_class_registry->get($p_class_name);
            // echo '<pre>';
            // print_r($t_class_file);
            // echo '</pre>';
            if (isset($t_class_file) == false || empty($t_class_file) == true) {
                return false;
            }
            # include class definition
            include_once($t_class_file);
        }

        # PART II: check overloading classes
        $coo_overload_registry = self::get_overload_registry();
        # init chain and set final class to registry
        $coo_overload_registry->init_class_chain($p_class_name);
        // echo $p_class_name.'<br>';
        return true;
    }

    public static function create_object($p_class_name, $p_args_array = array(), $p_use_singleton = false) {

        $t_operation_class_name = $p_class_name;
        // echo '<pre>';
        // print_r($t_operation_class_name);
        // echo '</pre>';
        # load/include original class definition
        $t_load_success = self::load_class($p_class_name);
        // echo $t_load_success;
        if ($t_load_success == false) {
            $coo_cache = & DataCache::get_instance();
            $coo_cache->clear_cache();

            trigger_error('Class not found in registry: ' . $p_class_name, E_USER_ERROR);
            return false;
        }

        # try loading overload-version
        if (is_object($GLOBALS['cseo_debugger'])) {
            $GLOBALS['cseo_debugger']->log('overload check: ' . $p_class_name, 'class_overloading');
        }
        $coo_overload_registry = self::get_overload_registry('coo_overload_registry');
        $t_overload_class_name = $coo_overload_registry->get($p_class_name);
        // echo '<pre>';
        // print_r($t_overload_class_name);
        // echo '</pre>';
        // echo $t_overload_class_name;
        if ($t_overload_class_name != NULL) {
            if (is_object($GLOBALS['cseo_debugger']))
                $GLOBALS['cseo_debugger']->log('overload found: ' . $p_class_name . ' => ' . $t_overload_class_name, 'class_overloading');
            $t_operation_class_name = $t_overload_class_name;
        }


        # build constructor string for eval()
        $t_constructor_args_string = '';

        if (sizeof($p_args_array) > 0) {
            $t_constructor_args_array = array();

            for ($i = 0; $i < sizeof($p_args_array); $i++) {
                $t_constructor_args_array[] = '$p_args_array[' . $i . ']';
            }
            $t_constructor_args_string .= implode(', ', $t_constructor_args_array);
        }

        # create object
        $coo_output_object = false;

        if ($p_use_singleton == true) {
            # use get_instance instead of new
            // $coo_object =& ClassName::get_instance($p_param1, $p_param2);
            $t_eval_code = '$coo_output_object =& ' . $t_operation_class_name . '::get_instance(' . $t_constructor_args_string . ');';
        } else {
            # use new for creating a new instance
            $t_eval_code = '$coo_output_object = new ' . $t_operation_class_name . '(' . $t_constructor_args_string . ');';
        }

        if ($t_overload_class_name != NULL) {
            if (is_object($GLOBALS['cseo_debugger'])) {
                $GLOBALS['cseo_debugger']->log('eval: ' . $t_eval_code, 'class_overloading');
            }
        }
        // echo $t_eval_code.'<br>';
        eval($t_eval_code);

        if ($coo_output_object === false) {
            trigger_error('create_object failed: ' . $t_operation_class_name, E_USER_ERROR);
        }

        // $GLOBALS['coo_stop_watch_array']['create_object_'.$p_class_name]->stop();
        return $coo_output_object;
    }

}
