<?php

/* -----------------------------------------------------------------
 * 	$Id: SliderManager.inc.php 1316 2014-12-15 16:11:35Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de - http://www.indiv-style.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

cseohookfactory::load_class('ExtenderComponent');

class SliderManager extends ExtenderComponent {

    function proceed($sid, $site) {
        if ($site == 'content') {
            $start = xtc_db_query("SELECT slider_set FROM " . TABLE_CONTENT_MANAGER . " WHERE content_group = '" . $sid . "' AND slider_set != '' AND slider_set != '0' AND languages_id = '" . intval($_SESSION['languages_id']) . "';");
        } elseif ($site == 'cat') {
            $start = xtc_db_query("SELECT slider_set FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = '" . $sid . "' AND slider_set != '' AND slider_set != '0' AND language_id = '" . intval($_SESSION['languages_id']) . "';");
        } elseif ($site == 'blogstart') {
            $start = xtc_db_query("SELECT slider_set FROM " . TABLE_BLOG_START . " WHERE id = '" . $sid . "' AND slider_set != '' AND slider_set != '0' AND language_id = '" . intval($_SESSION['languages_id']) . "';");
        } elseif ($site == 'blogcat') {
            $start = xtc_db_query("SELECT slider_set FROM " . TABLE_BLOG_CATEGORIES . " WHERE categories_id = '" . $sid . "' AND slider_set != '' AND slider_set != '0' AND language_id = '" . intval($_SESSION['languages_id']) . "';");
        } elseif ($site == 'blogitem') {
            $start = xtc_db_query("SELECT slider_set FROM " . TABLE_BLOG_ITEMS . " WHERE item_id = '" . $sid . "' AND slider_set != '' AND slider_set != '0' AND language_id = '" . intval($_SESSION['languages_id']) . "';");
        }

        if (xtc_db_num_rows($start) > 0) {
            $start = xtc_db_fetch_array($start);
            $slider_sql = xtc_db_query("SELECT * FROM cseo_slider_gallery WHERE slider_id = ' " . $start['slider_set'] . " ' AND status = '1' AND language_id = '" . intval($_SESSION['languages_id']) . "';");
			if (xtc_db_num_rows($slider_sql) > 0) {
                $slider = xtc_db_fetch_array($slider_sql);
				
				$this->v_data_array = array(
                'slider_text' => $slider['slider_text'],
                'slider_url' => $slider['slider_url'],
                'slider_url_2' => $slider['slider_url_2'],
                'slider_url_3' => $slider['slider_url_3'],
                'slider_url_4' => $slider['slider_url_4'],
                'slider_url_5' => $slider['slider_url_5'],
                'slider_url_6' => $slider['slider_url_6'],
                'slider_url_7' => $slider['slider_url_7'],
                'slider_url_8' => $slider['slider_url_8'],
                'slider_image' => ($slider['slider_image'] != '' ? xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image'], ($slider['slider_alt_text'] !='' ? $slider['slider_alt_text'] : $slider['slider_link_text']), ($slider['slider_title_text'] != '' ? $slider['slider_title_text'] : $slider['slider_link_text'])) : ''),
				'slider_image_2' => ($slider['slider_image_2'] != '' ? xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image_2'], ($slider['slider_alt_text_2'] != '' ? $slider['slider_alt_text_2'] : $slider['slider_link_text_2']), ($slider['slider_title_text_2'] != '' ? $slider['slider_title_text_2'] : $slider['slider_link_text_2'])) : ''),
				'slider_image_3' => ($slider['slider_image_3'] != '' ? xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image_3'], ($slider['slider_alt_text_3'] != '' ? $slider['slider_alt_text_3'] : $slider['slider_link_text_3']), ($slider['slider_title_text_3'] != '' ? $slider['slider_title_text_3'] : $slider['slider_link_text_3'])) : ''),
				'slider_image_4' => ($slider['slider_image_4'] != '' ? xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image_4'], ($slider['slider_alt_text_4'] != '' ? $slider['slider_alt_text_4'] : $slider['slider_link_text_4']), ($slider['slider_title_text_4'] != '' ? $slider['slider_title_text_4'] : $slider['slider_link_text_4'])) : ''),
				'slider_image_5' => ($slider['slider_image_5'] != '' ? xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image_5'], ($slider['slider_alt_text_5'] != '' ? $slider['slider_alt_text_5'] : $slider['slider_link_text_5']), ($slider['slider_title_text_5'] != '' ? $slider['slider_title_text_5'] : $slider['slider_link_text_5'])) : ''),
				'slider_image_6' => ($slider['slider_image_6'] != '' ? xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image_6'], ($slider['slider_alt_text_6'] != '' ? $slider['slider_alt_text_6'] : $slider['slider_link_text_6']), ($slider['slider_title_text_6'] != '' ? $slider['slider_title_text_6'] : $slider['slider_link_text_6'])) : ''),
				'slider_image_7' => ($slider['slider_image_7'] != '' ? xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image_7'], ($slider['slider_alt_text_7'] != '' ? $slider['slider_alt_text_7'] : $slider['slider_link_text_7']), ($slider['slider_title_text_7'] != '' ? $slider['slider_title_text_7'] : $slider['slider_link_text_7'])) : ''),
				'slider_image_8' => ($slider['slider_image_8'] != '' ? xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image_8'], ($slider['slider_alt_text_8'] != '' ? $slider['slider_alt_text_8'] : $slider['slider_link_text_8']), ($slider['slider_title_text_8'] != '' ? $slider['slider_title_text_8'] : $slider['slider_link_text_8'])) : ''),
                'slider_desc' => $slider['slider_desc'],
                'slider_desc_2' => $slider['slider_desc_2'],
                'slider_desc_3' => $slider['slider_desc_3'],
                'slider_desc_4' => $slider['slider_desc_4'],
                'slider_desc_5' => $slider['slider_desc_5'],
                'slider_desc_6' => $slider['slider_desc_6'],
                'slider_desc_7' => $slider['slider_desc_7'],
                'slider_desc_8' => $slider['slider_desc_8'],
                'slider_link_text' => $slider['slider_link_text'],
                'slider_link_text_2' => $slider['slider_link_text_2'],
                'slider_link_text_3' => $slider['slider_link_text_3'],
                'slider_link_text_4' => $slider['slider_link_text_4'],
                'slider_link_text_5' => $slider['slider_link_text_5'],
                'slider_link_text_6' => $slider['slider_link_text_6'],
                'slider_link_text_7' => $slider['slider_link_text_7'],
                'slider_link_text_8' => $slider['slider_link_text_8'],
                'fullsize' => $slider['fullsize'],
                'slider_mobile' => $slider['slider_mobile'],
                'slider_nav_status' => $slider['slider_nav_status'],
				);
            }
        }
        $this->v_data_array['language'] = $_SESSION['language'];
		return $this->v_data_array;
    }

}
