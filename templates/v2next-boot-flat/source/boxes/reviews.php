<?php
/*-----------------------------------------------------------------
* 	$Id: reviews.php 371 2013-06-10 12:44:12Z akausch $
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

if ($_SESSION['customers_status']['customers_status_read_reviews'] == 1) {
	$box_smarty = new smarty;
	$box_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
	require_once(DIR_FS_INC . 'xtc_random_select.inc.php');
	require_once(DIR_FS_INC . 'xtc_break_string.inc.php');
	require_once(DIR_FS_INC . 'cseo_get_url_friendly_text.inc.php');

	//fsk18 lock
	$fsk_lock='';
	if ($_SESSION['customers_status']['customers_fsk18_display']=='0') {
		$fsk_lock=' and p.products_fsk18!=1';
	}
	$random_select = "SELECT r.reviews_id,
							r.reviews_rating,
							p.products_id,
							p.products_image,
							pd.products_name
							FROM " . TABLE_REVIEWS . " r,
							" . TABLE_REVIEWS_DESCRIPTION . " rd,
							" . TABLE_PRODUCTS . " p,
							" . TABLE_PRODUCTS_DESCRIPTION . " pd
							WHERE p.products_status = '1'
							AND p.products_id = r.products_id
							".$fsk_lock."
							AND r.reviews_id = rd.reviews_id
							AND r.reviews_status = '1'
							AND rd.languages_id = '" . (int)$_SESSION['languages_id'] . "'
							AND p.products_id = pd.products_id
							AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "'";
	if ($product->isProduct()) {
		$random_select .= " and p.products_id = '" . $product->data['products_id'] . "'";
	}
	$random_select .= " order by r.reviews_id desc limit " . MAX_RANDOM_SELECT_REVIEWS;
	$random_product = xtc_random_select($random_select);


	if ($random_product) {
		// display random review box
		$review_query = "select substring(reviews_text, 1, 60) as reviews_text from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $random_product['reviews_id'] . "' and languages_id = '" . $_SESSION['languages_id'] . "'";
		$review_query = xtDBquery($review_query);
		$review = xtc_db_fetch_array($review_query,true);

		$review = htmlspecialchars($review['reviews_text']);
		$review = xtc_break_string($review, 15, '-<br />');

		$bild = DIR_WS_THUMBNAIL_IMAGES.'no_img.jpg';
		if($random_product['products_image'] !='')
		$bild = DIR_WS_THUMBNAIL_IMAGES.$random_product['products_image'];

		$box_reviews = '<div class="ac">
								<a href="review-'.$random_product['reviews_id'].'/'.cseo_get_url_friendly_text($random_product['products_name']).'.html'.'">
									'.xtc_image($bild, $random_product['products_name']).'
								</a>
							</div>
							<a href="review-'.$random_product['reviews_id'].'/'.cseo_get_url_friendly_text($random_product['products_name']).'.html'.'">' . $review . ' ..</a><br />
							<div class="ac">
								' . xtc_image('templates/' . CURRENT_TEMPLATE . '/img/stars_' . $random_product['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $random_product['reviews_rating'])) . '
							</div>';

	}

	if ($box_reviews!='') {
		$box_smarty->assign('REVIEWS_LINK',xtc_href_link(FILENAME_REVIEWS));
		$box_smarty->assign('BOX_CONTENT', $box_reviews);
		$box_smarty->assign('language', $_SESSION['language']);
		$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
		$box_smarty->assign('box_name', getBoxName('reviews'));
		$box_smarty->assign('box_class_name', getBoxCSSName('reviews'));
		// set cache ID
		if (!CacheCheck()) {
			$box_smarty->caching = false;
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
		} else {
			$box_smarty->caching = true;
			$box_smarty->cache_lifetime=CACHE_LIFETIME;
			$box_smarty->cache_modified_check=CACHE_CHECK;
			$cache_id = $_SESSION['language'].$random_product['reviews_id'].$product->data['products_id'].$_SESSION['language'].'reviews';
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html',$cache_id);
		}
	}
}
