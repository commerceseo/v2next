<?php

/* -----------------------------------------------------------------
 * 	$Id: class.tagcloud.php 1360 2015-01-14 07:30:09Z akausch $
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

function kshufflenew($array) {
	if(!is_array($array) || empty($array)) {
		return false;
	}
	$tmp = array();
	foreach($array as $key => $value) {
		$tmp[] = array('k' => $key, 'v' => $value);
	}
	shuffle($tmp);
	$array = array();
	foreach($tmp as $entry) {
		$array[$entry['k']] = $entry['v'];
	}
	return true;
}

function printTagCloudnew($tags) {
	kshuffle($tags);
	$max_size = MAX_DISPLAY_TAGS_FONT;
	$min_size = MIN_DISPLAY_TAGS_FONT;
	$max_qty = max(array_values($tags));
	$min_qty = min(array_values($tags));
	$spread = $max_qty - $min_qty;
	if($spread == 0) {
		$spread = 1;
	}
	$step = ($max_size - $min_size) / ($spread);
	foreach ($tags as $key => $value) {
		$size = round($min_size + (($value - $min_qty) * $step));
		if (MODULE_COMMERCE_SEO_URL_LOWERCASE == 'True') {
			$tagkey = strtolower(urlencode($key));
		} else {			
			$tagkey = urlencode($key); 
		}
		$cloud .= '<a href="'.xtc_href_link('tag/'.$tagkey).'/" class="fs'.$size.'" title="' . $value . ' Produkte wurden mit ' . $key . ' getagged">' . $key . '</a> ';
	}
	return $cloud;
}
