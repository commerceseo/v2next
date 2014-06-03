<?php

/* -----------------------------------------------------------------
 * 	$Id: janolaw.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function WriteJanolawTemp($type) {
    if (function_exists(curl_init)) {
        $data = curl_init(JANOLAW_BASEURL . '/' . JANOLAW_USERID . '/' . JANOLAW_SHOPID . '/' . $type . '.html');
        curl_setopt($data, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($data);
        $output = str_replace('@import url("https://www.janolaw.de/agb-service/shops/janolaw.css");', '', $output);
        $fp = fopen(JANOLAW_CACHEPATH . '/' . md5(JANOLAW_USERID . JANOLAW_SHOPID . 'janolaw_' . $type) . '.html', 'w');
        fwrite($fp, $output);
        fclose($fp);
        curl_close($data);
    } elseif ($file = file_get_contents(JANOLAW_BASEURL . '/' . JANOLAW_USERID . '/' . JANOLAW_SHOPID . '/' . $type . '.html')) {
        unlink(JANOLAW_CACHEPATH . '/' . md5(JANOLAW_USERID . JANOLAW_SHOPID . 'janolaw_' . $type) . '.html');
        $fp = fopen(JANOLAW_CACHEPATH . '/' . md5(JANOLAW_USERID . JANOLAW_SHOPID . 'janolaw_' . $type) . '.html', 'w');
        $file = str_replace('@import url("https://www.janolaw.de/agb-service/shops/janolaw.css");', '', $file);
        fwrite($fp, $file);
        fclose($fp);
    } else {
        $host = 'janolaw.de';
        $uri = '/agb-service/shops/' . JANOLAW_USERID . '/' . JANOLAW_SHOPID . '/' . $type . '.html';

        header("Content-type: text/plain");
        $sock = fsockopen($host, 80, $errno, $errstr, 5);
        fputs($sock, "GET " . $uri . " HTTP/1.1\r\n");
        fputs($sock, "Host: " . $host . "\r\n");
        fputs($sock, "Connection: close\r\n\r\n");
        $result = array();
        while (!feof($sock))
            $result[] = fgets($sock, 4096);
        fclose($sock);
        if (!empty($result['1'])) {
            $fp = fopen(JANOLAW_CACHEPATH . '/' . md5(JANOLAW_USERID . JANOLAW_SHOPID . 'janolaw_' . $type) . '.html', 'w');
            for ($i = 1, $size = sizeof($result); $i < $size; ++$i) {
                if (!empty($result[$i]) || $result[$i] != '0')
                    $tmp .= $result[$i];
            }
            fwrite($fp, $tmp);
            fclose($fp);
        }
    }
}

function JanolawContent($type = 'agb', $sort = 'txt') {
    if (file_exists(JANOLAW_CACHEPATH . '/' . md5(JANOLAW_USERID . JANOLAW_SHOPID . 'janolaw_' . $type) . '.html')) {
        if (filectime(JANOLAW_CACHEPATH . '/' . md5(JANOLAW_USERID . JANOLAW_SHOPID . 'janolaw_' . $type) . '.html') + JANOLAW_CACHETIME <= time()) {
            WriteJanolawTemp($type);
        }
    } else
        WriteJanolawTemp($type);

    if (file(JANOLAW_CACHEPATH . '/' . md5(JANOLAW_USERID . JANOLAW_SHOPID . 'janolaw_' . $type) . '.html')) {
        $str = implode(" ", file(JANOLAW_CACHEPATH . '/' . md5(JANOLAW_USERID . JANOLAW_SHOPID . 'janolaw_' . $type) . '.html'));
        if ($sort == 'html')
            return str_replace('id="janolaw-paragraph"', 'class="janolaw-paragraph"', $str); // doppelte ID's sind nicht zulĂ¤ssig! W3C
        else
            return '<textarea name="textarea_' . $type . '" cols="60" rows="10" readonly="readonly">' . strip_tags(str_replace('</div>', "\n\n", $str)) . '</textarea>';
    } else
        return "Ein Fehler ist aufgetreten! Bitte &uuml;berpr&uuml;fen Sie ihre Janolaw UserID und ShopID im Adminbereich!";
}
