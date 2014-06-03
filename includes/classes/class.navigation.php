<?php

/* -----------------------------------------------------------------
 * 	$Id: class.navigation.php 940 2014-04-05 10:24:17Z akausch $
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

class cseo_navigation_ORIGINAL {
	function cseo_navigation_ORIGINAL() {


	}
	function view_per_site($site, $cols, $per_site) {
		if (isset($site) && $site != '') {
			if ($site == 'new_products' || $site == 'specials' || $site == 'product_listing') {
				$nav_parms = xtc_get_all_get_params(array('products_id', 'x', 'y', 'cat', 'per_site', 'multisort', 'filter_id', 'page', 'view_as'));
				$file_name = $file_name;
			} elseif ($site == 'advanced_search_result') {
				$nav_parms = xtc_get_all_get_params(array('keywords', 'products_id', 'x', 'y', 'cat', 'per_site', 'multisort', 'filter_id', 'page', 'view_as_advsr'));
				$file_name = $file_name;
			} elseif ($site == 'tagcloud') {
				$nav_parms = xtc_get_all_get_params(array('tag', 'products_id', 'x', 'y', 'cat', 'per_site', 'multisort', 'filter_id', 'view_as'));
				$file_name = 'tag/' . $_GET['tag'] . '/';
			} elseif ($site == 'hashtags') {
				$nav_parms = xtc_get_all_get_params(array('hashtags', 'products_id', 'x', 'y', 'cat', 'per_site', 'multisort', 'filter_id', 'view_as'));
				$file_name = 'hashtag/' . $_GET['hashtags'] . '/';
			} elseif ($site == 'filter') {
				$nav_parms = xtc_get_all_get_params(array('per_site', 'page', 'x', 'y', 'view_as'));
				$file_name = $file_name;
			} elseif ($site == 'products_new') {
				$nav_parms = xtc_get_all_get_params(array('products_id', 'x', 'y', 'cat', 'per_site', 'multisort', 'filter_id', 'page', 'view_as'));
				$file_name = FILENAME_PRODUCTS_NEW;
			}
		}

		switch ($cols) {
			case '3' :
				$view_per_site = ($per_site == 9 ? '<b>9</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=9' . $get_param) . '">9</a>') . ' | ';
				$view_per_site .= ($per_site == 18 ? '<b>18</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=18' . $get_param) . '">18</a>') . ' | ';
				$view_per_site .= ($per_site == 27 ? '<b>27</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=27' . $get_param) . '">27</a>') . ' | ';
				$view_per_site .= ($per_site == 45 ? '<b>45</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=45' . $get_param) . '">45</a>') . ' | ';
				$view_per_site .= ($per_site == 81 ? '<b>81</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=81' . $get_param) . '">81</a>');
				break;

			case '4' :
				$view_per_site = ($per_site == 12 ? '<b>12</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=12' . $get_param) . '">12</a>') . ' | ';
				$view_per_site .= ($per_site == 24 ? '<b>24</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=24' . $get_param) . '">24</a>') . ' | ';
				$view_per_site .= ($per_site == 60 ? '<b>60</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=60' . $get_param) . '">60</a>') . ' | ';
				$view_per_site .= ($per_site == 84 ? '<b>84</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=84' . $get_param) . '">84</a>') . ' | ';
				$view_per_site .= ($per_site == 96 ? '<b>96</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=96' . $get_param) . '">96</a>');
				break;

			default :
				$view_per_site = ($per_site == 10 ? '<b>10</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=10' . $get_param) . '">10</a>') . ' | ';
				$view_per_site .= ($per_site == 20 ? '<b>20</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=20' . $get_param) . '">20</a>') . ' | ';
				$view_per_site .= ($per_site == 30 ? '<b>30</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=30' . $get_param) . '">30</a>') . ' | ';
				$view_per_site .= ($per_site == 50 ? '<b>50</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=50' . $get_param) . '">50</a>') . ' | ';
				$view_per_site .= ($per_site == 100 ? '<b>100</b>' : '<a rel="nofollow" href="' . xtc_href_link($file_name, $nav_parms . 'per_site=100' . $get_param) . '">100</a>');
				break;
		}
	return $view_per_site;

	}
}

