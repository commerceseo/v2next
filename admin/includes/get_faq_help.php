<?php

/* -----------------------------------------------------------------
 * 	$Id: get_faq_help.php 420 2013-06-19 18:04:39Z akausch $
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

if (function_exists('curl_init')) {
    $url = 'http://faq.commerce-seo.de/get_help.php?id=' . (int) $_GET['id'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    echo $output;
} elseif (ini_get(allow_url_fopen) !== false && function_exists(file_get_contents)) {
    $data = file_get_contents('http://faq.commerce-seo.de/get_help.php?id=' . (int) $_GET['id']);
    if (!empty($data))
        print_r($data);
    else
        echo 'Der FAQ-Server konnte nicht erreicht werden.';
} else {
    $host = 'faq.commerce-seo.de';
    $uri = '/get_help.php?id=' . (int) $_GET['id'];

    header("Content-type: text/plain");
    $sock = fsockopen($host, 80, $errno, $errstr, 5);
    fputs($sock, "GET " . $uri . " HTTP/1.1\r\n");
    fputs($sock, "Host: " . $host . "\r\n");
    fputs($sock, "Connection: close\r\n\r\n");
    $result = array();
    while (!feof($sock))
        $result[] = fgets($sock, 4096);
    fclose($sock);
    if (!empty($result['9'])) {
        for ($i = 9, $size = sizeof($result); $i < $size; ++$i) {
            if (!empty($result[$i]) || $result[$i] != '0')
                echo $result[$i];
        }
    }
    else
        echo 'Der Artikel konnte nicht gefunden werden.';
}
