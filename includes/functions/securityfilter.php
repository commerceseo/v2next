<?php

/* -----------------------------------------------------------------
 * 	$Id: securityfilter.php 865 2014-03-16 12:44:08Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * Security Pro Version 2.0
 * @link http://www.fwrmedia.co.uk
 * @copyright Copyright 2008-2009 FWR Media
 * @copyright Portions Copyright 2005 ( rewrite uri concept ) Bobby Easland
 * @author Robert Fisher, FWR Media, http://www.fwrmedia.co.uk 
 * @lastdev $Author:: akausch       $:  Author of last commit
 * @lastmod $Date:: 2013-06-05 15:01:19 +0200 (Mi, 05 Jun 2013)        $:  Date of last commit
 * --------------------------------------------------------------- */

/**
 * Recursively cleanse a variable or an array
 * 
 * @uses is_array()
 * @uses preg_replace()
 * @uses array_map()
 * @uses urldecode()
 * @param mixed $get - array or variable to cleanse
 * 
 * @return mixed - cleansed variable or array
 */
function spro_cleanse_get_recursive($get) {
    if (!is_array($get)) {
        $banned_string_pattern = '@GLOBALS|_REQUEST|base64_encode|UNION|%3C|%3E@i';
        // Apply the whitelist
        $cleansed = preg_replace("/[^\s\/\{}a-zÀÁÂÃÅÄÆÈÉÊËÌÍÎÏÒÓÔÕÖØÙÚÛÜàáâãåäæèéêëìíîïòóôõöøùúûüß,@0-9_\.\-]/i", "", urldecode($get));
        // Remove banned words
        $cleansed = preg_replace($banned_string_pattern, '', $cleansed);
        // Ensure that a clever hacker hasn't gained himself a naughty double hyphen -- after our cleansing
        return preg_replace('@[-]+@', '-', $cleansed);
    }
    // Add the preg_replace to every element.
    return array_map('spro_cleanse_get_recursive', $get);
}

class Fwr_Media_Security_Pro {

    // Array of files to be excluded from cleansing, these can also be added in application_top.php if preferred using Fwr_Media_Security_Pro::addExclusion()
    var $excluded_from_cleansing = array('protx_process.php', 'my_payment.php');
    var $enabled = true; // Turn on or off - bool true / false
    var $basename;

    /**
     * Add file exclusions - these files will NOT have the querystring cleansed
     * @param string $file_to_exclude - file to exclude from cleansing
     * @access public
     * @return void
     */
    function addExclusion($file_to_exclude = '') {
        if (!in_array($file_to_exclude, $this->excluded_from_cleansing)) {
            $this->excluded_from_cleansing[] = $file_to_exclude;
        }
    }

    /**
     * Called from application_top.php here we instigate the cleansing of the querystring
     * 
     * @uses in_array()
     * @uses function_exists()
     * @uses ini_get()
     * @see Fwr_Media_Security_Pro::cleanGlobals()
     * @param array $HTTP_GET_VARS - long array
     * @param string $PHP_SELF - base filename from osCommerce application_top.php
     * 
     * @access public
     * @return void
     */
    function cleanse($PHP_SELF = '') {
        if (false === $this->enabled) {
            return;
        }
        if (empty($PHP_SELF)) {
            return;
        }
        $this->basename = $PHP_SELF;
        if (false !== $this->excludedFile()) {
            return;
        }
        $_GET = spro_cleanse_get_recursive($_GET);
        $_REQUEST = $_GET + $_POST; // $_REQUEST now holds the cleansed $_GET and unchanged $_POST. $_COOKIE has been removed.
        if (!function_exists('ini_get') || ini_get('register_globals') != false) {
            $this->cleanGlobals();
        }
    }

    /**
     * With register globals set to on we need to ensure that GLOBALS are cleansed
     * 
     * @uses array_key_exists()
     * 
     * @access public
     * @return void
     */
    function cleanGlobals() {
        foreach ($_GET as $key => $value) {
            if (array_key_exists($key, $GLOBALS)) {
                $GLOBALS[$key] = $value;
            }
        }
    }

    /**
     * Iterate the exclude files and return false if the filename does not appear in $basename
     * 
     * @uses strpos
     * 
     * @access public
     * @return bool
     */
    function excludedFile() {
        foreach ($this->excluded_from_cleansing as $index => $filename) {
            if (false !== strpos($this->basename, $filename)) {
                return true;
            }
        }
        return false;
    }

    function cseo_security() {
        // Cross-Site Scripting attack defense
        if (count($_GET) > 0) {
            //Lets now sanitize the GET vars
            foreach ($_GET as $secvalue) {
                if (!is_array($secvalue)) {
                    if ((preg_match("/<[^>]*script.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/.*[[:space:]](or|and)[[:space:]].*(=|like).*/i", $secvalue)) ||
                            (preg_match("/<[^>]*object.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*iframe.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*applet.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*meta.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*style.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*form.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*window.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*alert.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*img.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*document.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*select.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*cookie.*\"?[^>]*>/i", $secvalue))) {
                        xtc_db_query("INSERT INTO 
											intrusions 
											(name , badvalue , page , tags , ip , ip2 , impact , origin , created )
											VALUES 
											('" . $_SESSION['customer_id'] . "', '" . $secvalue . "', 'page', 'tags', '" . $_SERVER['HTTP_CLIENT_IP'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '1', '', now());");

                        die;
                    }
                }
            }
        }

        //Lets now sanitize the POST vars
        if (count($_POST) > 0) {
            foreach ($_POST as $secvalue) {
                if (!is_array($secvalue)) {
                    if ((preg_match("<[^>]*script.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*object.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*iframe.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*applet.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*window.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*alert.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*document.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*cookie.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*meta.*\"?[^>]*>/i", $secvalue))
                    ) {
                        xtc_db_query("INSERT INTO 
											intrusions 
											(name , badvalue , page , tags , ip , ip2 , impact , origin , created )
											VALUES 
											('" . $_SESSION['customer_id'] . "', '" . $secvalue . "', 'page', 'tags', '" . $_SERVER['HTTP_CLIENT_IP'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '1', '', now());");

                        die;
                    }
                }
            }
        }

        //Lets now sanitize the COOKIE vars
        if (count($_COOKIE) > 0) {
            foreach ($_COOKIE as $secvalue) {
                if (!is_array($secvalue)) {
                    if ((preg_match("/<[^>]*script.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/.*[[:space:]](or|and)[[:space:]].*(=|like).*/i", $secvalue)) ||
                            (preg_match("/<[^>]*object.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*iframe.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*applet.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*meta.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*style.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*form.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*window.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*alert.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*document.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*cookie.*\"?[^>]*>/i", $secvalue)) ||
                            (preg_match("/<[^>]*img.*\"?[^>]*>/i", $secvalue))
                    ) {
                        xtc_db_query("INSERT INTO 
											intrusions 
											(name , badvalue , page , tags , ip , ip2 , impact , origin , created )
											VALUES 
											('" . $_SESSION['customer_id'] . "', '" . $secvalue . "', 'page', 'tags', '" . $_SERVER['HTTP_CLIENT_IP'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '1', '', now());");

                        die;
                    }
                }
            }
        }
    }
}

// end class
