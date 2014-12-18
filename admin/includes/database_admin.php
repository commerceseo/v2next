<?php

/* -----------------------------------------------------------------
 * 	$Id: database_admin.php 1191 2014-09-09 19:05:15Z akausch $
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


defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

// define the database table names used in the project
define('TABLE_SEARCH_QUERIES_SORTED', 'search_queries_sorted');
define('TABLE_PRODUCTS_PARAMETERS', 'products_parameters');
define('TABLE_PRODUCTS_PARAMETERS_DESCRIPTION', 'products_parameters_description');
define('TABLE_PRODUCTS_PARAMETERS_GROUPS', 'products_parameters_groups');
define('TABLE_PRODUCTS_PARAMETERS_GROUPS_DESCRIPTION', 'products_parameters_groups_description');
define('TABLE_NEWS_TICKER', 'news_ticker');
define('TABLE_PERSONAL_LINKS_NAMES', 'commerce_seo_url_names');
define('TABLE_PERSONAL_LINKS_URL', 'commerce_seo_url_personal_links');
define('TABLE_ADDRESS_BOOK', 'address_book');
define('TABLE_ADDRESS_FORMAT', 'address_format');
define('TABLE_ADMIN_ACCESS', 'admin_access');
define('TABLE_BANNERS', 'banners');
define('TABLE_BANNERS_HISTORY', 'banners_history');
define('TABLE_CAMPAIGNS', 'campaigns');
define('TABLE_CATEGORIES', 'categories');
define('TABLE_CATEGORIES_DESCRIPTION', 'categories_description');
define('TABLE_CONFIGURATION', 'configuration');
define('TABLE_CONFIGURATION_GROUP', 'configuration_group');
define('TABLE_COUNTRIES', 'countries');
define('TABLE_CURRENCIES', 'currencies');
define('TABLE_CUSTOMERS', 'customers');
define('TABLE_CUSTOMERS_WISHLIST', 'customers_wishlist');
define('TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES', 'customers_wishlist_attributes');
define('TABLE_CUSTOMERS_BASKET', 'customers_basket');
define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', 'customers_basket_attributes');
define('TABLE_CUSTOMERS_INFO', 'customers_info');
define('TABLE_CUSTOMERS_IP', 'customers_ip');
define('TABLE_CUSTOMERS_STATUS', 'customers_status');
define('TABLE_CUSTOMERS_STATUS_HISTORY', 'customers_status_history');
define('TABLE_FORMS', 'forms');
define('TABLE_LANGUAGES', 'languages');
define('TABLE_MANUFACTURERS', 'manufacturers');
define('TABLE_MANUFACTURERS_INFO', 'manufacturers_info');
define('TABLE_NEWSLETTERS', 'newsletters');
define('TABLE_NEWSLETTERS_HISTORY', 'newsletters_history');
define('TABLE_NEWSLETTER_RECIPIENTS', 'newsletter_recipients');
define('TABLE_ORDERS', 'orders');
define('TABLE_ORDERS_PRODUCTS', 'orders_products');
define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', 'orders_products_attributes');
define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', 'orders_products_download');
define('TABLE_ORDERS_STATUS', 'orders_status');
define('TABLE_ORDERS_STATUS_HISTORY', 'orders_status_history');
define('TABLE_ORDERS_TOTAL', 'orders_total');
define('TABLE_ORDERS_RECALCULATE', 'orders_recalculate');
define('TABLE_PERSONAL_OFFERS_BY', 'personal_offers_by_customers_status_');
define('TABLE_PRODUCTS', 'products');
define('TABLE_PRODUCTS_ATTRIBUTES', 'products_attributes');
define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', 'products_attributes_download');
define('TABLE_PRODUCTS_CONTENT', 'products_content');
define('TABLE_PRODUCTS_DESCRIPTION', 'products_description');
define('TABLE_PRODUCTS_NOTIFICATIONS', 'products_notifications');
define('TABLE_PRODUCTS_IMAGES', 'products_images');
define('TABLE_PRODUCTS_OPTIONS', 'products_options');
define('TABLE_PRODUCTS_OPTIONS_VALUES', 'products_options_values');
define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', 'products_options_values_to_products_options');
define('TABLE_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
define('TABLE_PRODUCTS_VPE', 'products_vpe');
define('TABLE_PRODUCTS_XSELL', 'products_xsell');
define('TABLE_PRODUCTS_XSELL_GROUPS', 'products_xsell_grp_name');
define('TABLE_REVIEWS', 'reviews');
define('TABLE_REVIEWS_DESCRIPTION', 'reviews_description');
define('TABLE_SESSIONS', 'sessions');
define('TABLE_SPECIALS', 'specials');
define('TABLE_TAX_CLASS', 'tax_class');
define('TABLE_TAX_RATES', 'tax_rates');
define('TABLE_TPL_MODULES_CONFIGURATION', 'tpl_modules_configuration');
define('TABLE_GEO_ZONES', 'geo_zones');
define('TABLE_ZONES_TO_GEO_ZONES', 'zones_to_geo_zones');
define('TABLE_WHOS_ONLINE', 'whos_online');
define('TABLE_ZONES', 'zones');
define('TABLE_BOX_ALIGN', 'box_align');
define('TABLE_CUSTOMERS_MEMO', 'customers_memo');
define('TABLE_CONTENT_MANAGER', 'content_manager');
define('TABLE_MEDIA_CONTENT', 'media_content');
define('TABLE_MODULE_NEWSLETTER', 'module_newsletter');
define('TABLE_CM_FILE_FLAGS', 'cm_file_flags');
define('TABLE_COUPON_GV_QUEUE', 'coupon_gv_queue');
define('TABLE_COUPON_GV_CUSTOMER', 'coupon_gv_customer');
define('TABLE_COUPON_EMAIL_TRACK', 'coupon_email_track');
define('TABLE_COUPON_REDEEM_TRACK', 'coupon_redeem_track');
define('TABLE_COUPONS', 'coupons');
define('TABLE_COUPONS_DESCRIPTION', 'coupons_description');
define('TABLE_SERVER_TRACKING', 'server_tracking');
define('TABLE_SHIPPING_STATUS', 'shipping_status');
define('TABLE_SHIPPING_TRACKING', 'shipping_tracking');
define('TABLE_BLACKLIST', 'card_blacklist');
define('TABLE_CAMPAIGNS_IP', 'campaigns_ip');
define('TABLE_CUSTOMERS_SIK', 'customers_sik');
define('TABLE_BANKTRANSFER', 'banktransfer');
define('TABLE_PAYPAL', 'paypal');
define('TABLE_PAYPAL_STATUS_HISTORY', 'paypal_status_history');
define('TABLE_SCART', 'scart');
define('TABLE_NEWSLETTER_PRODUCT_LIST', 'newsletter_product_list');
define('TABLE_NEWSLETTER_PRODUCTS', 'newsletter_products');
define('TABLE_BLOG_START', 'blog_start');
define('TABLE_BLOG_CATEGORIES', 'blog_categories');
define('TABLE_BLOG_ITEMS', 'blog_items');
define('TABLE_BLOG_SETTINGS', 'blog_settings');
define('TABLE_PDFBILL_PROFILE', 'pdfbill_profile');
define('TABLE_EMAILS', 'emails');
define('TABLE_TAG_TO_PRODUCT', 'tag_to_product');

define('TABLE_PRODUCT_FILTER_CATEGORIES', 'product_filter_categories');
define('TABLE_PRODUCT_FILTER_ITEMS', 'product_filter_items');

define('TABLE_EMAILS_ORDER_PRODUCT_LIST', 'emails_order_products_list');
define('TABLE_EMAILS_ORDER_PRODUCTS', 'emails_order_products');
define('TABLE_ADMIN_STAT_YEAR', 'whos_online_year');
define('TABLE_ADMIN_STAT_MONTH', 'whos_online_month');
define('TABLE_BUTTON_LANGUAGES', 'button_languages');
define('TABLE_ACCESSORIES', 'accessories');
define('TABLE_ACCESSORIES_PRODUCTS', 'accessories_products');

define('TABLE_CSEO_LANG_BUTTON', 'cseo_lang_button');
define('TABLE_CSEO_ANTISPAM', 'cseo_antispam');
define('TABLE_ADMIN_NAVIGATION', 'admin_navigation');
/******** SHOPGATE **********/
define('TABLE_SHOPGATE_ORDERS', 'orders_shopgate_order');
/******** SHOPGATE **********/
define('TABLE_SLIDER_GALLERY', 'cseo_slider_gallery');

define('TABLE_RMA','rma');
define('TABLE_RMA_STATUS','rma_status');
define('TABLE_RMA_REASON','rma_reason');
define('TABLE_RMA_COMMENTS','rma_comments');
define('TABLE_RMA_TEMPLATES','rma_templates');
define('TABLE_SPECIALS_GRATIS','specials_gratis');
define('TABLE_SPECIALS_GRATIS_DESCRIPTION','specials_gratis_description');

define('TABLE_PROPERTIES', 'properties');
define('TABLE_PROPERTIES_DESCRIPTION', 'properties_description');
define('TABLE_PROPERTIES_VALUES', 'properties_values');
define('TABLE_PROPERTIES_VALUES_DESCRIPTION', 'properties_values_description');
define('TABLE_PRODUCTS_PROPERTIES_COMBIS', 'products_properties_combis');
define('TABLE_PRODUCTS_PROPERTIES_INDEX', 'products_properties_index');
define('TABLE_PRODUCTS_PROPERTIES_COMBIS_VALUES', 'products_properties_combis_values');
define('TABLE_ORDERS_PRODUCTS_PROPERTIES', 'orders_products_properties');
define('TABLE_WITHDRAWAL', 'withdrawals');
define('TABLE_BOXES', 'boxes');
define('TABLE_BOXES_NAMES', 'boxes_names');
define('TABLE_BOXES_POSITIONS', 'boxes_positions');
define('TABLE_BLOG_COMMENT', 'blog_comment');