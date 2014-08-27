<?php
/* -----------------------------------------------------------------
 * 	$Id: global_search.php 1056 2014-05-17 13:17:56Z akausch $
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

require ('includes/application_top.php');
$coo_text_mgr = new LanguageTextManager('global_search', $_SESSION['languages_id']);
$smarty = new Smarty;
$smarty->assign('txt', $coo_text_mgr->v_section_content_array['global_search']);
$treffer = '';
$q = xtc_db_prepare_input($_POST['search']);
$smarty->assign('SEARCH_WORD', $q);

//Kunden
$getCustomerData = xtc_db_query("SELECT
									c.*,
									a.*
								FROM
									" . TABLE_CUSTOMERS . " c
								LEFT OUTER JOIN " . TABLE_ADDRESS_BOOK . " AS a ON(a.address_book_id = c.customers_default_address_id)
								WHERE
									c.customers_id LIKE '%" . $q . "%'
								OR
									c.customers_cid LIKE '%" . $q . "%'
								OR
									a.entry_company LIKE '%" . $q . "%'
								OR
									a.entry_postcode LIKE '%" . $q . "%'
								OR
									c.customers_firstname LIKE '%" . $q . "%'
								OR
									c.customers_lastname LIKE '%" . $q . "%'
								OR
									c.customers_telephone LIKE '%" . $q . "%'
								OR
									c.customers_email_address LIKE '%" . $q . "%'
								GROUP BY c.customers_id 
								ORDER BY c.customers_lastname, c.customers_firstname, a.entry_company, a.entry_postcode;");

if (xtc_db_num_rows($getCustomerData) > 0) {
	$smarty->assign('CUSTOMER_HITS', xtc_db_num_rows($getCustomerData));
    while ($CustomerData = xtc_db_fetch_array($getCustomerData)) {
		if ($CustomerData['customers_cid'] != '') {
			$cid = '<a href="' . xtc_href_link('customers.php', 'search=' . $CustomerData['customers_cid']) . '">'.$CustomerData['customers_cid'].' <i class="glyphicon glyphicon-search"></i></a>';
		} else {
			$cid = '<a href="' . xtc_href_link('customers.php', 'searchcid=' . $CustomerData['customers_id']) . '">'.$CustomerData['customers_id'].' <i class="glyphicon glyphicon-search"></i></a>';
		}
		$cname = '<a href="' . xtc_href_link('customers.php', 'cID=' . $CustomerData['customers_id'] . '&action=edit') . '">' . $CustomerData['customers_firstname'] . ' ' . $CustomerData['customers_lastname'] .' <i class="glyphicon glyphicon-pencil"></i></a>';
		$company = '';
		$cuid = '';
		if ($CustomerData['entry_company'] != '') {
			$cuid = $CustomerData['customers_vat_id'];
			$company = $CustomerData['entry_company'];
		}
		$getCustomerDataarray[] = array('CUSTOMER_ID' => $cid,
								'CUSTOMER_NAME' => $cname,
								'CUSTOMER_UID' => $cuid,
								'CUSTOMER_COMPANY' => $company);
	
	}
	$smarty->assign('CUSTOMER_ARRAY', $getCustomerDataarray);
	
	
}
	
//Produkte

$getProductsData = xtc_db_query("SELECT
										p.products_id,
										p.products_model,
										p.products_ean,
										pd.products_name,
										p2c.categories_id
									FROM
										" . TABLE_PRODUCTS . " p
										LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON(p.products_id = pd.products_id)
										LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS p2c ON(p2c.products_id = p.products_id)
									WHERE
										p.products_id LIKE '%" . $q . "%'
									OR
										p.products_model LIKE '%" . $q . "%'
									OR
										p.products_ean LIKE '%" . $q . "%'
									OR
										pd.products_name LIKE '%" . $q . "%'
									GROUP BY
										p.products_id");

if (xtc_db_num_rows($getProductsData) > 0) {
	$smarty->assign('PRODUCTS_HITS', xtc_db_num_rows($getProductsData));
    while ($ProductsData = xtc_db_fetch_array($getProductsData)) {
		$pplink = '<a href="' . xtc_href_link('categories.php', 'cPath=' . $ProductsData['categories_id'] . '&pID=' . $ProductsData['products_id'] . '&action=new_product">' . $ProductsData['products_name']) . '</a>';
        $getProductsDataarray[] = array('PRODUCTS_LINK' => $pplink);
    }

	$smarty->assign('PRODUCTS_ARRAY', $getProductsDataarray);
}

//Option
$getOptData = xtc_db_query("SELECT
									products_options_name,
									products_options_id
								FROM
									" . TABLE_PRODUCTS_OPTIONS . "
								WHERE
									products_options_name LIKE '%" . $q . "%'");

if (xtc_db_num_rows($getOptData) > 0) {
	$smarty->assign('OPTION_HITS', xtc_db_num_rows($getOptData));
    while ($OptData = xtc_db_fetch_array($getOptData)) {
		$oplink = '<a href="' . xtc_href_link('products_attributes.php', '&action=update_option&option_id=' . $OptData['products_options_id'] . '#option') . '">' . $OptData['products_options_name'] . '</a>';
        $getOptDataarray[] = array('OPTION_LINK' => $oplink);
    }
	$smarty->assign('OPTION_ARRAY', $getOptDataarray);

}

//Attribute
$getValData = xtc_db_query("SELECT
									products_options_values_name,
									products_options_values_id
								FROM
									" . TABLE_PRODUCTS_OPTIONS_VALUES . "
								WHERE
									products_options_values_name LIKE '%" . $q . "%'");

if (xtc_db_num_rows($getValData) > 0) {
	$smarty->assign('MERKMAL_HITS', xtc_db_num_rows($getValData));
    while ($ValData = xtc_db_fetch_array($getValData)) {
		$vlink = '<a href="' . xtc_href_link('products_attributes.php', '&action=update_option_value&value_id=' . $ValData['products_options_values_id'] . '#value') . '">' . $ValData['products_options_values_name'] . '</a>';
        $getValDataarray[] = array('VALUE_LINK' => $vlink);
    }
	$smarty->assign('MERKMAL_ARRAY', $getValDataarray);

}

//Bestellungen
$getOrderData = xtc_db_query("SELECT
									orders_id,
									customers_name,
									customers_postcode,
									customers_email_address,
									DATE_FORMAT(date_purchased, '%d.%m.%Y %H:%i') AS date_purchased
								FROM
									" . TABLE_ORDERS . "
								WHERE
									orders_id LIKE '%" . $q . "%'
								OR
									customers_name LIKE '%" . $q . "%'
								OR
									customers_postcode LIKE '%" . $q . "%'
								OR
									customers_email_address LIKE '%" . $q . "%';");

if (xtc_db_num_rows($getOrderData) > 0) {
	$smarty->assign('ORDER_HITS', xtc_db_num_rows($getOrderData));

    while ($OrderData = xtc_db_fetch_array($getOrderData)) {
		$olink = '<a href="' . xtc_href_link('orders.php', '&oID=' . $OrderData['orders_id'] . '&action=edit') . '">' . $OrderData['orders_id'] . ' - ' . $OrderData['customers_name'] . ' vom ' . $OrderData['date_purchased'] . '</a>';
        $getOrderDataarray[] = array('ORDER_NR' => $OrderData['orders_id'],
									'ORDER_LINK' => xtc_href_link('orders.php', '&oID=' . $OrderData['orders_id'] . '&action=edit'),
									'ORDER_NAME' => $OrderData['customers_name'],
									'ORDER_DATE' => $OrderData['date_purchased'],
		);
		
    }
		$smarty->assign('ORDER_ARRAY', $getOrderDataarray);
}


//Content
$getConData = xtc_db_query("SELECT
									content_text,
									content_title,
									content_id
								FROM
									" . TABLE_CONTENT_MANAGER . "
								WHERE
									content_text LIKE '%" . $q . "%'");

if (xtc_db_num_rows($getConData) > 0) {
	$smarty->assign('CONTENT_HITS', xtc_db_num_rows($getConData));
    while ($ConData = xtc_db_fetch_array($getConData)) {
		$colink = '<a href="' . xtc_href_link('content_manager.php', 'action=edit&coID=' . $ConData['content_id']) . '">' . $ConData['content_title'] . '</a>';
        $getConDataarray[] = array('CONTENT_LINK' => $colink);
    }
	$smarty->assign('CONTENT_ARRAY', $getConDataarray);

}

//Blog
$getBloData = xtc_db_query("SELECT
									description,
									name,
									title,
									item_id
								FROM
									" . TABLE_BLOG_ITEMS . "
								WHERE
									description LIKE '%" . $q . "%'");

if (xtc_db_num_rows($getBloData) > 0) {
	$smarty->assign('BLOG_HITS', xtc_db_num_rows($getBloData));
    while ($BloData = xtc_db_fetch_array($getBloData)) {
		$bloink = '<a href="' . xtc_href_link('blog.php', 'action=edit_item&item=' . $BloData['item_id']) . '">' . $BloData['title'] . '</a>';
        $getBloDataarray[] = array('BLOG_LINK' => $bloink);
    }
	$smarty->assign('BLOG_ARRAY', $getBloDataarray);

}


require_once(DIR_WS_INCLUDES . 'header.php');
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
$smarty->caching = false;
$smarty->template_dir = DIR_FS_CATALOG . 'admin/templates/';
$smarty->compile_dir = DIR_FS_CATALOG . 'admin/templates_c';
$smarty->display(CURRENT_ADMIN_TEMPLATE . '/global_search.html');

require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
