<?php

/* -----------------------------------------------------------------
 * 	$Id: class.registry.inc.php 522 2013-07-24 11:44:51Z akausch $
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

class Registry {
    #holding class-file-data (key[classname]=>value[path])

    var $v_values_array = array();

    #constructor

    function Registry() {
        
    }

    #set new entry for class name (key) with path (value)

    function set($p_name, $p_value) {
        $this->v_values_array[$p_name] = $p_value;
        return true;
    }

    #get path (value) for given name (key)

    function get($p_name) {
        if (!empty($this->v_values_array[$p_name])) {
            return $this->v_values_array[$p_name];
        }
        return NULL;
    }

    #get the whole array with name and path informations

    function get_all_data() {
        return $this->v_values_array;
    }

}
