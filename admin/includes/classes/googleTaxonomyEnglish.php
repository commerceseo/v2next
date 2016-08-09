<?php

/* -----------------------------------------------------------------
 * 	$Id: class.google_taxonomy.php 1 2015-02-03 15:44:56Z akausch $
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

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class googleTaxonomyEnglish {

    var $local_url_en;
    var $remote_url_en;

    function __construct() {
        $this->local_url_en = DIR_FS_CATALOG . 'cache/google_taxonomy_en.txt';
        $this->remote_url_en = 'http://www.google.com/basepages/producttype/taxonomy.en-US.txt';
		
		if (file_exists($this->local_url_en) && filesize($this->local_url_en) > 0) {
            $filetime = filectime($this->local_url_en);
            $tomorrow = mktime(date('h', $filetime), date('i', $filetime), date('s', $filetime), date('m', $filetime), date('d', $filetime) + 2, date('Y', $filetime));
            if ($filetime < $tomorrow)
                return true;
        }
        if (empty($this->remote_url_en))
            return false;

        if (function_exists('curl_init')) {
            $gc = curl_init();
            curl_setopt($gc, CURLOPT_URL, $this->remote_url_en);
            curl_setopt($gc, CURLOPT_RETURNTRANSFER, 1);
            $result_en = curl_exec($gc);
        } else {
            $result_en = @file_get_contents($this->remote_url_en);
            if (!$result_en) {
                $parsed_url = parse_url($this->remote_url_en);

                $sock = fsockopen($parsed_url['host'], 80, $errno, $errstr, 5);
                fputs($sock, "GET " . $parsed_url['path'] . " HTTP/1.1\r\n");
                fputs($sock, "Host: " . $parsed_url['host'] . "\r\n");
                fputs($sock, "Connection: close\r\n\r\n");

                $header = '';
                $result_en = '';
                do {
                    $header .= fgets($sock, 4096);
                } while (strpos($header, "\r\n\r\n") === false);

                while (!feof($sock))
                    $result_en .= fgets($sock, 4096);

                fclose($sock);
            }
        }

        if ($result_en === false || empty($result_en) || strlen($result_en) < 100000)
            return false;

        $handle_en = fopen($this->local_url_en, 'w');
        $put_result = fwrite($handle_en, $result_en);
        fclose($handle_en);
    }

    function get_dropdown_data($parent = '') {
        $data = $this->get_categories($parent);
        if (!empty($data)) {
            if ($parent != '')
                $result = array(array('id' => '', 'text' => ' -- bitte wählen -- '));

            foreach ($data AS $cat) {
                if (!empty($cat))
                    $result[] = array('id' => utf8_encode($cat), 'text' => utf8_encode($cat));
            }
            return $result;
        }
        return false;
    }

    function get_categories($parent = '') {
        $parent = utf8_decode($parent);
        $handle = fopen($this->local_url_en, 'r');
        $result = array();
        while (!feof($handle)) {
            $line = fgets($handle, 4096);
            $line = trim($line);
            $line = utf8_decode($line);

            if (empty($parent) && !strstr($line, '>'))
                $result[] = $line;

            elseif (!empty($parent) && strstr($line, $parent . ' >')) {
                $value = substr($line, strlen($parent));
                $temp_array = explode(' > ', $value);
                $value = trim($temp_array[1]);
                if (!empty($value) && !isset($temp_array[2]))
                    $result[] = $value;
            }
        }
        fclose($handle);
        return $result;
    }

}