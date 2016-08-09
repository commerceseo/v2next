<?php
/*-----------------------------------------------------------------
* 	$Id: IS_Blogstart.inc.php 945 2014-04-08 13:30:27Z akausch $
* 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
*   26.03.2014 www.indiv-style.de
* 
*   Copyright by H&S eCom 
*   @author little Pit(S.B.)
* ------------------------------------------------------------------
* 	based on:
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/


cseohookfactory::load_class('ExtenderComponent');

class IS_Blogstart extends ExtenderComponent {
	function proceed() {

		if (GROUP_CHECK == 'true') {
			$group_check = "AND group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
		}		
		$blogcat_array = array();
		$items = array();
		$blogc = 0;
		$item = 0;
		$startseite = xtc_db_fetch_array(xtc_db_query("SELECT description FROM blog_start WHERE id = 1 AND language_id = '" . (int) $_SESSION['languages_id'] . "';"));
		$this->v_data_array['starttext'] = $startseite['description'];
		
		$select_blogkat_slide_query = xtc_db_query("SELECT * FROM blog_start_images ORDER BY image_nr;");
		$i=1;
		while($select_blogkat_slide = xtc_db_fetch_array($select_blogkat_slide_query)){
			$blogslide[$i] = array(
				'IMAGENR' => $select_blogkat_slide['image_nr'],
				'image_large' => $select_blogkat_slide['image']			
			);
			$i++;
		}
		$this->v_data_array['IMAES_SLIDER'] = $blogslide;
		// categories

		$blogcat_query = xtc_db_query("SELECT categories_id, titel
              FROM blog_categories 
              WHERE status = 2 
              AND language_id = '" . (int)$_SESSION['languages_id'] . "'
              AND parent_id = 0 ".$group_check."
              ORDER BY position ASC");

		while ($blogcat = xtc_db_fetch_array($blogcat_query)) {
			$t_blog_url = xtc_href_link(FILENAME_BLOG, 'blog_cat=' . $blogcat['categories_id']);
			$blogcat_array[$blogc] = array(
				'CATEGORIE_ID' => $blogcat['categories_id'],
				'CATEGORIE_TITLE' => $blogcat['titel'],
				'CATEGORIE_LINK' => $t_blog_url,
				'ITEMS' => '',
				'SUBKAT' => '',
				'URL' => $t_blog_url);

			// Show Subcat first 
			$select_blogsubcat_query = xtc_db_query("SELECT *
				FROM blog_categories 
				WHERE status = 2 
				AND language_id = '" . (int)$_SESSION['languages_id'] . "'
				AND parent_id = '" . (int)$blogcat['categories_id'] . "' ".$group_check."
				");

			while ($blogsubcat = xtc_db_fetch_array($select_blogsubcat_query)) {
					$t_blogsub_url = xtc_href_link(FILENAME_BLOG, 'blog_cat=' . $blogsubcat['categories_id']);
				$blogcat_array[$blogc]['SUBKAT'][$blogsub] = array(
					'CATEGORIE_ID' => $blogsubcat['categories_id'],
					'CATEGORIE_TITLE' => $blogsubcat['titel'],
					'CATEGORIE_LINK' => $t_blogsub_url,
					'URL' => $t_blogsub_url);

				$blogsub++;
			}

			// SHOW ITEMS
			$items_query = xtc_db_query("SELECT item_id, title FROM blog_items WHERE status = 2 AND categories_id = '" . (int)$blogcat['categories_id'] . "' AND language_id = '" . (int)$_SESSION['languages_id'] . "' ORDER BY position ASC");

			while ($items = xtc_db_fetch_array($items_query)) {

				if ($_GET['blog_item'] == $items['item_id']) {
					$blog_id_active = ' blog_active';
				} else {
					$blog_id_active = '';
				}
					$t_item_url = xtc_href_link(FILENAME_BLOG, 'blog_cat=' . $blogcat['categories_id'] . '&blog_item=' . $items['item_id']);

				$blogcat_array[$blogc]['ITEMS'][$item] = array(
					'ITEM_ID' => $items['item_id'],
					'ITEM_TITLE' => $items['name'],
					'ITEM_ACTIVE' => $blog_id_active,
					'ITEM_LINK' => $t_item_url);
				$item++;
			}
			$blogc++;
		}
// Abfrage der einzelnen Blogbeitraege aller Kategorien
		$select_items_query = xtc_db_query("SELECT * 
									FROM " . blog_items . " 
									WHERE status = 2  
									AND language_id = '" . (int)$_SESSION['languages_id'] . "' ".$group_check."
									ORDER BY date2 DESC LIMIT 5");
		while ($select_items = xtc_db_fetch_array($select_items_query)) {

			list($blog_tag_list, $monat_raw, $blog_jahr_list) = explode(".", $select_items['date']);
			$monats_name = array('01' => 'Jan', '02' => 'Feb', '03' => 'M&auml;r', '04' => 'Apr', '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Dez');
			$blog_monat_list = $monats_name[$monat_raw];
			$t_item_url = xtc_href_link(FILENAME_BLOG, 'blog_cat=' . (int) $select_items['categories_id'] . '&blog_item=' . (int) $select_items['item_id']);

			$items[] = array('title' => $select_items['name'],
				'name' => $select_items['name'],
				'shortdesc' => $select_items['shortdesc'],
				'description' => $select_items['description'],
				'kommentare' => $kommentare,
				'date' => $select_items['date'],
				'tag' => $blog_tag_list,
				'monat' => $blog_monat_list,
				'jahr' => $blog_jahr_list,
				'blog_link' => $t_item_url);
		}
		if (sizeof($items) >= 1) {
			$this->v_data_array['blog_items']= $items;
		}
		$this->v_data_array['blogs']=  $blogcat_array;
		$this->v_data_array['language']=  $_SESSION['language'];

		return $this->v_data_array;
	}
}
