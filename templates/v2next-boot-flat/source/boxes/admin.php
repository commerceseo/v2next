<?php
/*-----------------------------------------------------------------
* 	$Id: admin.php 371 2013-06-10 12:44:12Z akausch $
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

if ($_SESSION['customers_status']['customers_status_id'] == 0) {
	$box_smarty = new smarty;
	require_once(DIR_FS_INC . 'xtc_image_button.inc.php');
		
	$box_admin = '';
	$flag = '';
	$orders_contents = '';
	
	$orders_status_validating = xtc_db_num_rows(xtc_db_query("select orders_status from " . TABLE_ORDERS ." where orders_status ='0'"));
	$orders_contents .='<a href="' . xtc_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&status=0', 'SSL') . '">' . TEXT_VALIDATING . '</a>: ' . $orders_status_validating . '<br />';
	
	$orders_status_query = xtc_db_query("select orders_status_name, orders_status_id from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$_SESSION['languages_id'] . "' ORDER BY orders_status_id ASC");
	while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
		$orders_pending_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '" . $orders_status['orders_status_id'] . "'");
		$orders_pending = xtc_db_fetch_array($orders_pending_query);
		$orders_contents .= '<a href="' . xtc_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&status=' . $orders_status['orders_status_id'], 'SSL') . '">' . $orders_status['orders_status_name'] . '</a>: ' . $orders_pending['count'] . '<br />';
	}
	$orders_contents = substr($orders_contents, 0, -6);
	
	$customers_query = xtc_db_query("select count(*) as count from " . TABLE_CUSTOMERS);
	$customers = xtc_db_fetch_array($customers_query);
	$products_query = xtc_db_query("select count(*) as count from " . TABLE_PRODUCTS . " where products_status = '1'");
	$products = xtc_db_fetch_array($products_query);
	$reviews_query = xtc_db_query("select count(*) as count from " . TABLE_REVIEWS);
	$reviews = xtc_db_fetch_array($reviews_query);
	$admin_image = '<a href="' . xtc_href_link(FILENAME_START,'', 'SSL').'">'.xtc_image_button('button_admin.gif', IMAGE_BUTTON_ADMIN).'</a>';
	if ($browser->isMobile()) {	
		$admin_mobile_image = '<br /><a href="' . xtc_href_link(FILENAME__MOBILE_START,'', 'SSL').'">'.xtc_image_button('button_mobile_admin.gif', IMAGE_BUTTON_MOBILE_ADMIN).'</a>';
	} else {
		$admin_mobile_image = '';
	}
	if ($product->isProduct()) {
		$admin_link = '<a href="' . xtc_href_link_admin(FILENAME_EDIT_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $product->data['products_id']) . '&action=new_product' . '" onclick="window.open(this.href); return false;">' . xtc_image_button('edit_product.gif', IMAGE_BUTTON_PRODUCT_EDIT) . '</a>';
		$admin_attributes = //"<br />\n".
		'<form action="admin/new_attributes.php" name="edit_attributes" method="post">'."\n".
		'<input type="hidden" name="action" value="edit" />'."\n".
		'<input type="hidden" name="current_product_id" value="'.$product->data['products_id'].'" />'."\n".
		'<input type="hidden" name="cpath" value="'.$cPath.'" />'."\n".
		'<input type="submit" class="css_img_button" value="Attribute editieren" />'."\n".
		'</form>';

		$admin_cross_selling = //"<br />\n". 
		'<form action="admin/categories.php" name="edit_crossselling" method="get">'."\n".
		'<input type="hidden" name="action" value="edit_crossselling">'."\n".
		'<input type="hidden" name="current_product_id" value="'.$product->data['products_id'].'">'."\n".
		'<input type="hidden" name="cpath" value="'.$cPath.'">'."\n".
		'<input type="submit" class="css_img_button" value="Cross Selling">'."\n".
		'</form>';	
	}
	
// -----------------------------------------------------------------------------------
	
	if(CAT_ID > 0) {

		global $current_category_id;
		$admin_category = //"<br />\n". 
		'<form action="admin/categories.php" name="edit_category" method="get">'."\n".
		'<input type="hidden" name="cPath" value="'.$cPath.'">'."\n".
		'<input type="hidden" name="cID" value="'.$current_category_id.'">'."\n".
		'<input type="hidden" name="action" value="edit_category">'."\n".
		'<input type="submit" class="css_img_button" value="Kategorie editieren">'."\n".
		'</form>';

	}
	
// -----------------------------------------------------------------------------------

	if(CONTENT_ID > 0) {
		
		$dbQuery = xtDBquery("
			SELECT 	content_id   
		 	FROM 	".TABLE_CONTENT_MANAGER."
		 	WHERE 	content_group = '".intval($_GET['coID'])."'
		 	AND 	languages_id='".(int)$_SESSION['languages_id']."' "
		);
		
		$dbQuery = xtc_db_fetch_array($dbQuery);

		if(!empty($dbQuery)) {
	
		$admin_content = //"<br />\n". 
		'<form action="admin/content_manager.php" name="edit_content" method="get">'."\n".
		'<input type="hidden" name="action" value="edit">'."\n".
		'<input type="hidden" name="coID" value="'.intval($dbQuery['content_id']).'">'."\n".
		'<input type="submit" class="css_img_button" value="Content editieren">'."\n".
		'</form>';
		
		}

	}
// -----------------------------------------------------------------------------------
	
$box_admin= '<b>' . BOX_TITLE_STATISTICS . '</b><br />' . $orders_contents . '<br />' .
					BOX_ENTRY_CUSTOMERS . ' ' . $customers['count'] . '<br />' .
					BOX_ENTRY_PRODUCTS . ' ' . $products['count'] . '<br />' .
					BOX_ENTRY_REVIEWS . ' ' . $reviews['count'] .'<br />' .
					$admin_image . $admin_mobile_image . '<br />' .$admin_link.
					$admin_attributes.
					$admin_cross_selling.
					$admin_category.
					$admin_content;
	
	if ($flag==true) 
		define('SEARCH_ENGINE_FRIENDLY_URLS',true);

	$box_smarty->assign('BOX_CONTENT', $box_admin);
	$box_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
	$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
	$box_smarty->assign('box_name', getBoxName('admin'));
	$box_smarty->assign('box_class_name', getBoxCSSName('admin'));
	
	$box_smarty->caching = false;
	$box_smarty->assign('language', $_SESSION['language']);
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
}
