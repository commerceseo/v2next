<?php

/* -----------------------------------------------------------------
 * 	$Id: securityfilter.php 1126 2014-06-30 11:44:54Z akausch $
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

class Fwr_Media_Security_Pro {
    // Array of files to be excluded from cleansing, these can also be added in application_top.php if preferred using Fwr_Media_Security_Pro::addExclusion()
    var $_excluded_from_cleansing = array(); 
    var $_enabled = true; // Turn on or off - bool true / false
    var $_basename;
    var $_cleanse_keys; // Turn on or off - bool true / false
    /**
    * Constructor
    * 
    * @uses defined()
    * @param bool $cleanse_keys
    */
    function Fwr_Media_Security_Pro( $cleanse_keys = false ) {
      if ( $cleanse_keys ) $this->_cleanse_keys = true;
      $this->addExclusions( array( defined ( 'FILENAME_PROTX_PROCESS' )  ? FILENAME_PROTX_PROCESS  : 'protx_process.php' ) );
    } // end constructor
    /**
    * Add file exclusions - these files will NOT have the querystring cleansed
    * 
    * @uses in_array()
    * @param string $file_to_exclude - file to exclude from cleansing
    * 
    * @access public
    * @return object Fwr_Media_Security_Pro - allows chaining
    */
    function addExclusion( $file_to_exclude = '' ) {
      if ( !in_array ( $file_to_exclude, $this->_excluded_from_cleansing ) ) {
        $this->_excluded_from_cleansing[] = (string)$file_to_exclude;
      }
      return $this;
    } // end method
    /**
    * Add multiple file exclusions as an array
    * 
    * @uses foreach()
    * @param array $args - files to exclude from cleansing
    * 
    * @access public
    * @return void
    */
    function addExclusions( array $args = array() ) {
      if ( empty ( $args ) ) return;
      foreach ( $args as $index => $exclusion_file ) {
        $this->addExclusion( $exclusion_file );  
      }
    } // end method
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
    function cleanse( $PHP_SELF = '' ) {
      if ( false === $this->_enabled ) {
        return;
      }
      if ( empty( $PHP_SELF ) ) {
        return;
      }
      $this->_basename = $PHP_SELF;
      if ( in_array ( $this->_basename, $this->_excluded_from_cleansing ) ) {
        return;
      }
      $this->cleanseGetRecursive( $_GET );
      // $this->cleanseGetRecursive( $_POST );
      $this->cleanseGetRecursive( $_COOKIE );
      $_REQUEST = $_GET + $_POST + $_COOKIE; // $_REQUEST now holds the cleansed $_GET and unchanged $_POST. $_COOKIE has been removed.
      if ( !function_exists ( 'ini_get' ) || ini_get ( 'register_globals' ) != false ) {
        $this->cleanGlobals();
      }
    } // end method
    /**
    * Recursively cleanse _GET values and optionally keys as well if Fwr_Media_Security_Pro::cleanse_keys === true
    * 
    * @uses is_array()
    * @param array $get
    * 
    * @access public
    * @return void
    */
    function cleanseGetRecursive( &$get ) {
      foreach ( $get as $key => &$value ) {
        // If cleanse keys is set to on we unset array keys if they don't conform to expectations
        if ( $this->_cleanse_keys && ( $this->cleanseKeyString( $key ) != $key ) ) {
          unset ( $get[$key] );
          continue;
        }
        if ( is_array ( $value ) ) {
            // We have an array so well run it through again
            $this->cleanseGetRecursive( $value );
        // We have a string value so we'll cleanse it
        } else $value = $this->cleanseValueString( $value );
      } 
    } // end method
    /**
    * Cleanse array keys
    * 
    * Initially set as the same as values this may need to be made less strict
    * 
    * @uses urldecode()
    * @uses preg_replace()
    * 
    * @access public
    * @return string - cleansed key string
    */
    function cleanseKeyString( $string ) {
      // $banned_string_pattern = '@GLOBALS|_REQUEST|base64_encode|UNION|%3C|%3E@i';
	  $banned_string_pattern = '@GLOBALS|_REQUEST|onclick|ondblclick|onmousedown|onmousemove|onmouseover|onmouseout|onmouseup|onkeydown|onkeyup|onabort|onerror|onload|onresize|onscroll|onunload|onblur|onchange|onfocus|onreset|onselect|onsubmit|src|<|>|base64_encode|UNION|%3C|%3E@i';
      // Apply the whitelist
      // $cleansed = preg_replace ( "/[^\s{}a-z0-9_\.\-]/i", "", urldecode ( $string ) );
      $cleansed = preg_replace("/[^\s\/\{}a-zÀÁÂÃÅÄÆÈÉÊËÌÍÎÏÒÓÔÕÖØÙÚÛÜàáâãåäæèéêëìíîïòóôõöøùúûüß,@0-9_\.\-]/i", "", urldecode($string));
      // Remove banned words
      $cleansed = preg_replace ( $banned_string_pattern, '', $cleansed );
      // Ensure that a clever hacker hasn't gained himself a naughty double hyphen -- after our cleansing
      return preg_replace ( '@[-]+@', '-', $cleansed );  
    } // end method
    /**
    * Cleanse array values
    * 
    * @uses urldecode()
    * @uses preg_replace()
    * 
    * @access public
    * @return string - cleansed value string
    */
    function cleanseValueString( $string ) {
      // $banned_string_pattern = '@GLOBALS|_REQUEST|base64_encode|UNION|%3C|%3E@i';
	  $banned_string_pattern = '@GLOBALS|_REQUEST|onclick|ondblclick|onmousedown|onmousemove|onmouseover|onmouseout|onmouseup|onkeydown|onkeyup|onabort|onerror|onload|onresize|onscroll|onunload|onblur|onchange|onfocus|onreset|onselect|onsubmit|src|<|>|base64_encode|UNION|%3C|%3E@i';
      // Apply the whitelist
      // $cleansed = preg_replace ( "/[^\s{}a-z0-9_\.\-@]/i", "", urldecode ( $string ) );
	  $cleansed = preg_replace("/[^\s\/\{}a-zÀÁÂÃÅÄÆÈÉÊËÌÍÎÏÒÓÔÕÖØÙÚÛÜàáâãåäæèéêëìíîïòóôõöøùúûüß,@0-9_\.\-]/i", "", urldecode($string));
      // Remove banned words
      $cleansed = preg_replace ( $banned_string_pattern, '', $cleansed );
      // Ensure that a clever hacker hasn't gained himself a naughty double hyphen -- after our cleansing
      return preg_replace ( '@[-]+@', '-', $cleansed );  
    } // end method
    /**
    * With register globals set to on we need to ensure that GLOBALS are cleansed
    * 
    * @uses array_key_exists()
    * 
    * @access public
    * @return void
    */
    function cleanGlobals() {
      foreach ( $_GET as $key => $value ) {
        if ( array_key_exists ( $key, $GLOBALS ) ) {
          $GLOBALS[$key] = $value;
        }
      }
    } // end method
    function cseo_security() {
        // Cross-Site Scripting attack defense
        if (count($_GET) > 0) {
		// echo 'TEst GET';
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
											('" . $_SESSION['customer_id'] . "', '" . htmlentities($secvalue) . "', 'get', 'tags', '" . $_SERVER['HTTP_CLIENT_IP'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '1', '', now());");

                        die;
                    }
                }
            }
        }

        //Lets now sanitize the POST vars
        if (count($_POST) > 0) {
		// echo 'Test POST';
            foreach ($_POST as $secvalue) {
                if (!is_array($secvalue)) {
			// echo $secvalue.'<br>';
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
											('" . $_SESSION['customer_id'] . "', '" . htmlentities($secvalue) . "', 'post', 'tags', '" . $_SERVER['HTTP_CLIENT_IP'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '1', '', now());");

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
											('" . $_SESSION['customer_id'] . "', '" . htmlentities($secvalue) . "', 'cookie', 'tags', '" . $_SERVER['HTTP_CLIENT_IP'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '1', '', now());");

                        die;
                    }
                }
            }
        }
    }
}

// end class
