<?php
/*-----------------------------------------------------------------
* 	$Id: tagcloud.php 1006 2014-05-07 12:06:27Z akausch $
* 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ------------------------------------------------------------------
* 	based on:
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/

$box_smarty = new smarty;

if(!CacheCheck())
	$box_smarty->caching = false;
else {
	$box_smarty->caching = true;
	$box_smarty->cache_lifetime=CACHE_LIFETIME;
	$box_smarty->cache_modified_check=CACHE_CHECK;
	$cache_id = $_SESSION['language'].$_SESSION['tag_box'].'tagcloud';
}

if(!$box_smarty->isCached(CURRENT_TEMPLATE.'/boxes/box_tagcloud.html', $cache_id) || !$cache){
	function kshuffle2(&$array) {
	    if(!is_array($array) || empty($array))
	        return false;
	    $tmp = array();
	    foreach($array as $key => $value)
	        $tmp[] = array('k' => $key, 'v' => $value);

	    shuffle($tmp);
	    $array = array();
	    foreach($tmp as $entry)
	        $array[$entry['k']] = $entry['v'];
	    return true;
	}

	function printTagCloud2($tags) {
		kshuffle2($tags); // Zufällige Anzeige
	    $max_size = MAX_DISPLAY_TAGS_FONT; // max font size in pixels
	    $min_size = MIN_DISPLAY_TAGS_FONT; // min font size in pixels
	    $max_qty = max(array_values($tags));
	    $min_qty = min(array_values($tags));
	    $spread = $max_qty - $min_qty;
	    if($spread == 0)
	        $spread = 1;

	    $step = ($max_size - $min_size) / ($spread);

	    foreach ($tags as $key => $value) {
			$size = round($min_size + (($value - $min_qty) * $step));
			$cloud .= '<a href="'.xtc_href_link('tag/'.urlencode($key)).'/" class="fs'.$size.'" title="' . $value . ' Produkte wurden mit ' . $key . ' getagged">' . $key . '</a> ';
	    }
	    return $cloud;
	}

	$data_query = xtc_db_query("SELECT
									tag, count(tag) AS tag_anzahl,
									p.products_status
								FROM
									tag_to_product,
									".TABLE_PRODUCTS." as p
								WHERE
									lID = '".(int)$_SESSION['languages_id']."'
								AND 
									p.products_status = '1'
								AND
									p.products_id = pID
								GROUP BY
									tag
								ORDER BY
									rand()
								LIMIT ".MAX_DISPLAY_TAGS_RESULTS."");

	if(xtc_db_num_rows($data_query)) {
		$tag_array = array();
		while($data = xtc_db_fetch_array($data_query)) {
			if(!empty($data))
				$tag_array[$data['tag']] = $data['tag_anzahl'];
		}
	}
	if(is_array($tag_array))
		$tag_cloud = printTagCloud2($tag_array);
	if ($tag_cloud!='')	{
		$box_smarty->assign('box_name', getBoxName('tagcloud'));
		$box_smarty->assign('box_class_name', getBoxCSSName('tagcloud'));
		$box_smarty->assign('BOX_CONTENT', $tag_cloud);
		$box_smarty->assign('language', $_SESSION['language']);
		$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');

		if(!$cache)
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
		else
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html', $cache_id);
	}
}
