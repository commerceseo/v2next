<?php

/* -----------------------------------------------------------------
 * 	$Id: changedatain.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// include needed functions
require_once(DIR_FS_INC . 'dectobase64.inc.php');

function changedatain($plain_data, $key) {

    // encode plain data with key using xoft encryption

    $key_length = 0; //key length counter
    $all_bin_chars = "";
    $cipher_data = "";

    for ($i = 0; $i < strlen($plain_data); $i++) {
        $p = substr($plain_data, $i, 1);   // p = plaintext
        $k = substr($key, $key_length, 1); // k = key
        $key_length++;

        if ($key_length >= strlen($key)) {
            $key_length = 0;
        }

        $dec_chars = ord($p) ^ ord($k);
        $dec_chars = $dec_chars + strlen($key);
        $bin_chars = decbin($dec_chars);

        while (strlen($bin_chars) < 8) {
            $bin_chars = "0" . $bin_chars;
        }

        $all_bin_chars = $all_bin_chars . $bin_chars;
    }

    $m = 0;

    for ($j = 0; $j < strlen($all_bin_chars); $j = $j + 4) {
        $four_bit = substr($all_bin_chars, $j, 4);     // split 8 bit to 4 bit
        $four_bit_dec = bindec($four_bit);

        $decimal_value = $four_bit_dec * 4 + $m;     //multiply by 4 plus m where m=0,1,2, or 3

        $base64_value = dectobase64($decimal_value); //convert to base64 value
        $cipher_data = $cipher_data . $base64_value;
        $m++;

        if ($m > 3) {
            $m = 0;
        }
    }

    return $cipher_data;
}
