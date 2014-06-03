<?php
/*-----------------------------------------------------------------
* 	$Id: product_reviews_write_addon.php 449 2013-07-02 15:23:42Z akausch $
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

// MOD review_remind
if (REVIEW_REMIND == 'true') {
	include_once(DIR_WS_FUNCTIONS."review_remind.php");
	sendAdminMailReview($product, $insert_id);
}
// END MOD
