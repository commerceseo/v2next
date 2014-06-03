<?php

/* -----------------------------------------------------------------
 * 	$Id: class.sharecount.php 954 2014-04-09 18:20:48Z akausch $
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

class shareCount_ORIGINAL {

    private $url, $timeout;

    function __construct($url, $timeout = 3) {
        $this->url = rawurlencode($url);
        $this->timeout = $timeout;
    }

    function get_tweets($url) {
		$time = time();
		$schonda = xtc_db_query("SELECT * FROM cseo_share WHERE site = '$url' AND share = 'twitter';");
		if (xtc_db_num_rows($schonda) > 0) {
			$schonda = xtc_db_fetch_array($schonda);
			$istda = intval($schonda['time']);
			if ($istda < $time - 3600) {
				$json_string = $this->file_get_contents_curl('http://urls.api.twitter.com/1/urls/count.json?url=' . $this->url);
				$json = json_decode($json_string, true);
				$counter = intval($json['count']);
				xtc_db_query("UPDATE cseo_share SET count = '$counter', time = '$time' WHERE site = '$url' AND share = 'twitter';");
				return isset($json['count']) ? intval($json['count']) : 0;
			} else {
				return intval($schonda['count']);
			}
		} else {
				$json_string = $this->file_get_contents_curl('http://urls.api.twitter.com/1/urls/count.json?url=' . $this->url);
				$json = json_decode($json_string, true);
				$counter = intval($json['count']);
				xtc_db_query("INSERT INTO cseo_share (share, count, site, time) VALUE ('twitter', '$counter', '$url', '$time');");
				return isset($json['count']) ? intval($json['count']) : 0;
		}

    }

    function get_linkedin($url) {
		define('PAGE_PARSE_START_TIME_LINKEDIN', microtime());
		
		$json_string = $this->file_get_contents_curl("http://www.linkedin.com/countserv/count/share?url=$this->url&format=json");
		$json = json_decode($json_string, true);
		
		if ($_SESSION['customers_status']['customers_status_id'] == 0 && USE_TEMPLATE_DEVMODE == 'true') {
			$time_start = explode(' ', PAGE_PARSE_START_TIME_LINKEDIN);
			$time_end = explode(' ', microtime());
			$parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
			echo '<div id="parsetime">Parse Time Linkedin: ' . $parse_time . 's</div>';
		}
		
		return isset($json['count']) ? intval($json['count']) : 0;
    }

    function get_fb($url) {
		$time = time();
		$schonda = xtc_db_query("SELECT * FROM cseo_share WHERE site = '$url' AND share = 'facebook';");
		if (xtc_db_num_rows($schonda) > 0) {
			$schonda = xtc_db_fetch_array($schonda);
			$istda = intval($schonda['time']);
			if ($istda < $time - 3600) {
				$json_string = $this->file_get_contents_curl('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . urldecode($url));
				$json = json_decode($json_string, true);
				$counter = intval($json[0]['total_count']);
				xtc_db_query("UPDATE cseo_share SET count = '$counter', time = '$time' WHERE site = '$url' AND share = 'facebook';");
				return isset($json[0]['total_count']) ? intval($json[0]['total_count']) : 0;
			} else {
				return intval($schonda['count']);
			}
		} else {
				$json_string = $this->file_get_contents_curl('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . urldecode($url));
				$json = json_decode($json_string, true);
				$counter = intval($json[0]['total_count']);
				xtc_db_query("INSERT INTO cseo_share (share, count, site, time) VALUE ('facebook', '$counter', '$url', '$time');");
				return isset($json[0]['total_count']) ? intval($json[0]['total_count']) : 0;
		}	
    }
	
    function get_xing($url) {
        define('PAGE_PARSE_START_TIME_XING', microtime());
		
		$json_string = $this->file_get_contents_curl('https://www.xing-share.com/app/share?op=get_share_button;url=' . urldecode($this->url) . ';counter=right;lang=de;type=iframe;hovercard_position=1;shape=square');
        $json = json_decode($json_string, true);
		
		if ($_SESSION['customers_status']['customers_status_id'] == 0 && USE_TEMPLATE_DEVMODE == 'true') {
			$time_start = explode(' ', PAGE_PARSE_START_TIME_XING);
			$time_end = explode(' ', microtime());
			$parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
			echo '<div id="parsetime">Parse Time Xing: ' . $parse_time . 's</div>';
        }
        
		return isset($json[0]['total_count']) ? intval($json[0]['total_count']) : 0;
    }

    function get_plusones($url) {
		$time = time();
		$schonda = xtc_db_query("SELECT * FROM cseo_share WHERE site = '$url' AND share = 'googlep';");
		if (xtc_db_num_rows($schonda) > 0) {
			$schonda = xtc_db_fetch_array($schonda);
			$istda = intval($schonda['time']);
			if ($istda < $time - 3600) {
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . rawurldecode($this->url) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
				$curl_results = curl_exec($curl);
				curl_close($curl);
				$json = json_decode($curl_results, true);
				$counter = intval($json[0]['result']['metadata']['globalCounts']['count']);
				xtc_db_query("UPDATE cseo_share SET count = '$counter', time = '$time' WHERE site = '$url' AND share = 'googlep';");
				return isset($json[0]['result']['metadata']['globalCounts']['count']) ? intval($json[0]['result']['metadata']['globalCounts']['count']) : 0;
			} else {
				return intval($schonda['count']);
			}
		} else {
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . rawurldecode($this->url) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
				$curl_results = curl_exec($curl);
				curl_close($curl);
				$json = json_decode($curl_results, true);
				$counter = intval($json[0]['result']['metadata']['globalCounts']['count']);
				xtc_db_query("INSERT INTO cseo_share (share, count, site, time) VALUE ('googlep', '$counter', '$url', '$time');");
				return isset($json[0]['result']['metadata']['globalCounts']['count']) ? intval($json[0]['result']['metadata']['globalCounts']['count']) : 0;
		}
    }

    function get_pinterest($url) {
		$time = time();
		$schonda = xtc_db_query("SELECT * FROM cseo_share WHERE site = '$url' AND share = 'pinterest';");
		if (xtc_db_num_rows($schonda) > 0) {
			$schonda = xtc_db_fetch_array($schonda);
			$istda = intval($schonda['time']);
			if ($istda < $time - 3600) {
				$return_data = $this->file_get_contents_curl('http://api.pinterest.com/v1/urls/count.json?url=' . $this->url);
				$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
				$json = json_decode($json_string, true);
				$counter = intval($json['count']);
				xtc_db_query("UPDATE cseo_share SET count = '$counter', time = '$time' WHERE site = '$url' AND share = 'pinterest';");
				return isset($json['count']) ? intval($json['count']) : 0;
			} else {
				return intval($schonda['count']);
			}
		} else {
				$return_data = $this->file_get_contents_curl('http://api.pinterest.com/v1/urls/count.json?url=' . $this->url);
				$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
				$json = json_decode($json_string, true);
				$counter = intval($json['count']);
				xtc_db_query("INSERT INTO cseo_share (share, count, site, time) VALUE ('pinterest', '$counter', '$url', '$time');");
				return isset($json['count']) ? intval($json['count']) : 0;
		}
    }

    private function file_get_contents_curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
	
        $cont = curl_exec($ch);
        if (curl_error($ch)) {
            // die(curl_error($ch));
        }
        return $cont;
    }

}