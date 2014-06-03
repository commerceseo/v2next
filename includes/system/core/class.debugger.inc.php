<?php

/* -----------------------------------------------------------------
 * 	$Id: class.debugger.inc.php 522 2013-07-24 11:44:51Z akausch $
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

class Debugger {

    var $v_config_array = false;

    /*
     * constructor
     */

    function Debugger() {
        $t_config = $this->get_config();
        $this->v_config_array = $t_config;
    }

    function log($p_message, $p_source = 'notice', $p_type = 'general') {
        $t_do_log = $GLOBALS['cseo_debugger']->is_enabled($p_source);

        if ($t_do_log) {
            # filename for log
            $t_name = 'debug-' . $p_type;

            $t_stamp = date("Y-m-d H:i:s");
            $t_ip = $_SERVER['REMOTE_ADDR'];
            $t_source = $p_source;
            $t_message = $p_message;
            $t_break = "\n";

            # content for log entry
            $t_content = "$t_stamp [$t_ip] <$t_source> $t_message $t_break";

            $coo_error_log = new FileLog($t_name, true);
            $coo_error_log->write($t_content);
        }
    }

    function is_enabled($p_source) {
        //return false;
        $t_output = false;

        if ($this->v_config_array !== false) {
            # debug config found
            if (in_array($p_source, $this->v_config_array['ENABLED_SOURCES'])) {
                # source output enabled in config file
                $t_output = true;
            }
        }
        return $t_output;
    }

    function get_config() {
        $t_output = false;
        $t_config_file = DIR_FS_CATALOG . 'includes/system/debug_config.inc.php';

        if (file_exists($t_config_file) == true) {
            $t_output = true;

            # load found config file
            include($t_config_file);

            # check config array and load
            if (isset($t_debug_config) && is_array($t_debug_config)) {
                $t_output = $t_debug_config;
            }
        }
        return $t_output;
    }

}
