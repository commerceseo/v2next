<?php
/*-----------------------------------------------------------------
* 	$Id: admin.php 458 2013-07-08 06:10:18Z akausch $
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

if (isset($_SESSION['customer_id']) && isset($_SESSION['customers_status']['customers_status_id']) && $_SESSION['customers_status']['customers_status_id'] == 0) {
	$box_admin = '';
	$orders_contents = '';
	$orders_status_validating = xtc_db_num_rows(xtc_db_query("select orders_status from " . TABLE_ORDERS ." where orders_status ='0'"));
	$orders_contents .='<a href="' . xtc_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&status=0', 'SSL') . '">' . TEXT_VALIDATING . '</a>: ' . $orders_status_validating . '<br />';
	$orders_status_query = xtc_db_query("select orders_status_name, orders_status_id from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$_SESSION['languages_id'] . "' ORDER BY orders_status_id ASC");
	while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
		$orders_pending_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '" . $orders_status['orders_status_id'] . "'");
		$orders_pending = xtc_db_fetch_array($orders_pending_query);
		$orders_contents .= '<li><a href="' . xtc_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&status=' . $orders_status['orders_status_id'], 'SSL') . '">' . $orders_status['orders_status_name'] . '</a>: ' . $orders_pending['count'] . '</li>';
	}
	$orders_contents = substr($orders_contents, 0, -6);
	$customers = xtc_db_fetch_array(xtc_db_query("select count(*) as count from " . TABLE_CUSTOMERS));
	$products = xtc_db_fetch_array(xtc_db_query("select count(*) as count from " . TABLE_PRODUCTS . " where products_status = '1'"));
	$reviews = xtc_db_fetch_array(xtc_db_query("select count(*) as count from " . TABLE_REVIEWS));
	$online = xtc_db_fetch_array(xtc_db_query("select count(*) as count from " . TABLE_WHOS_ONLINE));
	$admin_image = '<a class="adminbtn" href="' . xtc_href_link(FILENAME_START,'', 'SSL').'">'.IMAGE_BUTTON_ADMIN.'</a>';
	if ($product->isProduct()) {
		$admin_link = '<a class="adminbtn" href="' . xtc_href_link_admin(FILENAME_EDIT_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $product->data['products_id']) . '&action=new_product' . '" target="_blank">' . IMAGE_BUTTON_PRODUCT_EDIT . '</a>';
		$admin_attributes = 
		'<form action="admin/new_attributes.php" name="edit_attributes" method="post" target="_blank">'."\n".
		'<input type="hidden" name="action" value="edit" />'."\n".
		'<input type="hidden" name="current_product_id" value="'.$product->data['products_id'].'" />'."\n".
		'<input type="hidden" name="cpath" value="'.$cPath.'" />'."\n".
		'<input type="submit" class="adminbtn" value="'.ADMIN_EDIT_ATTR.'" />'."\n".
		'</form>';

		$admin_cross_selling = 
		'<form action="admin/categories.php" name="edit_crossselling" method="get" target="_blank">'."\n".
		'<input type="hidden" name="action" value="edit_crossselling">'."\n".
		'<input type="hidden" name="current_product_id" value="'.$product->data['products_id'].'">'."\n".
		'<input type="hidden" name="cpath" value="'.$cPath.'">'."\n".
		'<input type="submit" class="adminbtn" value="'.ADMIN_EDIT_CROSS.'">'."\n".
		'</form>';	
	}

	if(CAT_ID > 0) {
		global $cPath, $current_category_id;
		$categorie_data = xtc_db_fetch_array(xtDBquery("SELECT parent_id, categories_status FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . (int) $current_category_id . "';"));
		$admin_category = '<a class="adminbtn" href="' . xtc_href_link_admin(FILENAME_EDIT_PRODUCTS, 'cPath=' . $categorie_data['parent_id'] . '&cID=' . $current_category_id) . '&action=edit_category' . '" target="_blank">'.ADMIN_EDIT_CAT.'</a>';
	}
	
	if(BLOG_ITEM > 0) {
		$dbQuery = xtc_db_fetch_array(xtDBquery("SELECT item_id, categories_id FROM ".TABLE_BLOG_ITEMS." WHERE item_id = '".intval(BLOG_ITEM)."' AND language_id='".(int)$_SESSION['languages_id']."';"));
		if(!empty($dbQuery)) {
			$admin_category = '<a class="adminbtn" href="' . xtc_href_link_admin('admin/blog.php', '&action=edit_item' . '&cat=' . intval($dbQuery['categories_id']) . '&item=' . intval($dbQuery['item_id'])) . '" target="_blank">'.ADMIN_EDIT_BLOG_ITEM.'</a>';
		}
	}
	
	if(BLOG_CAT > 0) {
		$dbQuery = xtc_db_fetch_array(xtDBquery("SELECT categories_id FROM ".TABLE_BLOG_CATEGORIES." WHERE categories_id = '".intval(BLOG_CAT)."' AND language_id='".(int)$_SESSION['languages_id']."';"));
		if(!empty($dbQuery)) {
			$admin_category = '<a class="adminbtn" href="' . xtc_href_link_admin('admin/blog.php', '&action=editcategories' . '&cat=' . intval($dbQuery['categories_id'])) . '" target="_blank">'.ADMIN_EDIT_BLOG_CAT.'</a>';
		}
	}
	
	if(CONTENT_ID > 0) {
		$dbQuery = xtc_db_fetch_array(xtDBquery("SELECT content_id FROM ".TABLE_CONTENT_MANAGER." WHERE content_group = '".intval($_GET['coID'])."' AND languages_id='".(int)$_SESSION['languages_id']."';"));
		if(!empty($dbQuery)) {
			$admin_category = '<a class="adminbtn" href="' . xtc_href_link_admin('admin/content_manager.php', 'coID=' . intval($dbQuery['content_id'])) . '&action=edit' . '" target="_blank">'.ADMIN_EDIT_CONTENT.'</a>';
		}

	}
$box_admin = "<link rel=\"stylesheet\" href=\"".(($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG."templates/admin/admin.css\" type=\"text/css\" media=\"projection, screen\">";
$box_admin .= '<div class="head_admin">';
$box_admin .= '<div class="head_admin_left">
					<div class="adminbtn-group pull-left">
						<a class="adminbtn admindropdown-toggle" data-toggle="dropdown" href="#">
						<i class="adminicon-list-alt"></i> '.ADMIN_STATISTICS.'
						<span class="caret"></span>
						</a>
						<ul class="admindropdown-menu">'.
						$orders_contents.
						'</ul>
					</div>
				</div>
			';
$box_admin .= '<div class="head_admin_center">
					<ul>
					<li>'.ADMIN_CUSTOMERS . ' ' . $customers['count'].'</li>
					<li>'.ADMIN_PRODUCTS . ' ' . $products['count'].'</li>
					<li>'.ADMIN_REVIEWS . ' ' . $reviews['count'].'</li>
					<li>'.ADMIN_TITLE_STATISTICS . ' ' . $online['count'].'</li>
					</ul>
				</div>
			';
$box_admin .= '<div class="head_admin_right">
					<ul>
						<li>'.$admin_image.'</li>
						<li>'.$admin_link.'</li>
						<li>'.$admin_attributes.'</li>
						<li>'.$admin_cross_selling.'</li>
						<li>'.$admin_category.'</li>
					</ul>
				</div>
			';
$box_admin .= '</div>';	
echo $box_admin;
}
