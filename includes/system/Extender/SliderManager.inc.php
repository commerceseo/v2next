<?php
/*-----------------------------------------------------------------
* 	$Id: SliderManager.inc.php 945 2014-04-08 13:30:27Z akausch $
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

cseohookfactory::load_class('ExtenderComponent');

class SliderManager extends ExtenderComponent {
	function proceed($sid, $site) {
		if ($site == 'content') {
			$start = xtc_db_query("SELECT slider_set FROM ".TABLE_CONTENT_MANAGER." WHERE content_group = '" . $sid . "' AND slider_set != '' AND slider_set != '0' AND languages_id = '".intval($_SESSION['languages_id'])."';");
		} elseif ($site == 'cat') {
			$start = xtc_db_query("SELECT slider_set FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id = '" . $sid . "' AND slider_set != '' AND slider_set != '0' AND language_id = '".intval($_SESSION['languages_id'])."';");
		}
		
		if(xtc_db_num_rows($start) > 0) {
			$start = xtc_db_fetch_array($start);
			$slider_sql = xtc_db_query("SELECT * FROM cseo_slider_gallery WHERE slider_id = ' " . $start['slider_set'] . " ' AND status = '1' AND language_id = '".intval($_SESSION['languages_id'])."';");
			if(xtc_db_num_rows($slider_sql) > 0) {
				$slider = xtc_db_fetch_array($slider_sql);
				$this->v_data_array['slider_text'] = $slider['slider_text'];
				$this->v_data_array['slider_url'] = $slider['slider_url'];
				$this->v_data_array['slider_url_2'] = $slider['slider_url_2'];
				$this->v_data_array['slider_url_3'] = $slider['slider_url_3'];
				$this->v_data_array['slider_url_4'] = $slider['slider_url_4'];
				$this->v_data_array['slider_url_5'] = $slider['slider_url_5'];
				if ($slider['slider_image'] != '') {
					$this->v_data_array['slider_image'] = xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image'], $slider['slider_link_text'], $slider['slider_link_text']);
				}
				if ($slider['slider_image_2'] != '') {
					$this->v_data_array['slider_image_2'] = xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image_2'], $slider['slider_link_text_2'], $slider['slider_link_text_2']);
				}
				if ($slider['slider_image_3'] != '') {
					$this->v_data_array['slider_image_3'] = xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image_3'], $slider['slider_link_text_3'], $slider['slider_link_text_3']);
				}
				if ($slider['slider_image_4'] != '') {
					$this->v_data_array['slider_image_4'] = xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image_4'], $slider['slider_link_text_4'], $slider['slider_link_text_4']);
				}
				if ($slider['slider_image_5'] != '') {
					$this->v_data_array['slider_image_5'] = xtc_image(DIR_WS_IMAGES . 'slider_images/' . $slider['slider_image_5'], $slider['slider_link_text_5'], $slider['slider_link_text_5']);
				}
				$this->v_data_array['slider_desc'] = $slider['slider_desc'];
				$this->v_data_array['slider_desc_2'] = $slider['slider_desc_2'];
				$this->v_data_array['slider_desc_3'] = $slider['slider_desc_3'];
				$this->v_data_array['slider_desc_4'] = $slider['slider_desc_4'];
				$this->v_data_array['slider_desc_5'] = $slider['slider_desc_5'];
				$this->v_data_array['slider_link_text'] = $slider['slider_link_text'];
				$this->v_data_array['slider_link_text_2'] = $slider['slider_link_text_2'];
				$this->v_data_array['slider_link_text_3'] = $slider['slider_link_text_3'];
				$this->v_data_array['slider_link_text_4'] = $slider['slider_link_text_4'];
				$this->v_data_array['slider_link_text_5'] = $slider['slider_link_text_5'];
				$this->v_data_array['fullsize'] = $slider['fullsize'];
			}
		}

		$this->v_data_array['language'] = $_SESSION['language'];

		return $this->v_data_array;
	}
}
