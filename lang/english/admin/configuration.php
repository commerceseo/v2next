<?php
/*-----------------------------------------------------------------
* 	$Id: configuration.php 833 2014-01-31 17:05:42Z akausch $
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

define('TABLE_HEADING_CONFIGURATION_TITLE', 'Title');
define('TABLE_HEADING_CONFIGURATION_VALUE', 'Value');
define('TABLE_HEADING_ACTION', 'Action');

// commerce:SEO
define('PRODUCT_LISTING_MANU_NAME_TITLE','Manufacturers name');
define('PRODUCT_LISTING_MANU_NAME_DESC','Show manufacturers name in product_listing_v1.html?<br />Linked automaticly to manufacturer.');
define('PRODUCT_LISTING_MANU_IMG_TITLE','Manufacturers image');
define('PRODUCT_LISTING_MANU_IMG_DESC','Show Manufacturers image in product_listing_v1.html?<br />Linked automaticly to manufacturer.');
define('PRODUCT_LISTING_VPE_TITLE','VPE Anzeige');
define('PRODUCT_LISTING_VPE_DESC','Show VPE in product_listing_v1.html?');
define('PRODUCT_LISTING_MODEL_TITLE','Modelnumber');
define('PRODUCT_LISTING_MODEL_DESC','Modelnumber in product_listing_v1.html?');
define('PDF_IN_ODERMAIL_TITLE','PDF in ordermail');
define('PDF_IN_ODERMAIL_DESC','Should a PDF be appended to the order confirmation email?');
define('PDF_IN_ORDERMAIL_COID_TITLE','Content ID');
define('PDF_IN_ORDERMAIL_COID_DESC','Number of the content of which the PDF should be produced.');
define('BOXLESS_CHECKOUT_TITLE','Boxenless checkout');
define('BOXLESS_CHECKOUT_DESC','If the boxes are hidden during checkout?');
define('DOWN_FOR_MAINTENANCE_TITLE','Offline Modus');
define('DOWN_FOR_MAINTENANCE_DESC','Offline Modus activate?<br />You can change the text in the <a href="content_manager.php?action=edit&coID=21">Content-Manager</a>.');
define('GOOGLE_VERIFY_TITLE','Google Verify Code');
define('GOOGLE_VERIFY_DESC','Insert your Google code to verify your site.');
define('SITE_OVERLAY_TITLE','Overlay PopUps');
define('SITE_OVERLAY_DESC','All sites which are able to open in a popup, where shown in an overlay.<br />z.B.: advanced search, Login, Reviews, etc...');

define('TEXT_INFO_EDIT_INTRO', 'Please make any necessary changes');
define('TEXT_INFO_DATE_ADDED', 'Date Added:');
define('TEXT_INFO_LAST_MODIFIED', 'Last Modified:');

define('CHECKOUT_SHOW_SHIPPING_MODULES_TITLE','Shipping modules opened?');
define('CHECKOUT_SHOW_SHIPPING_MODULES_DESC','Should the shipping modules be shown?');
define('CHECKOUT_SHOW_SHIPPING_ADDRESS_TITLE','Shipping address opened?');
define('CHECKOUT_SHOW_SHIPPING_ADDRESS_DESC','Should the shipping address be shown?');
define('CHECKOUT_SHOW_PAYMENT_MODULES_TITLE','Payment modules opened?');
define('CHECKOUT_SHOW_PAYMENT_MODULES_DESC','Should the payment modules be shown?');
define('CHECKOUT_SHOW_PAYMENT_ADDRESS_TITLE','Payment address opened?');
define('CHECKOUT_SHOW_PAYMENT_ADDRESS_DESC','Should the payment address be shown?');
define('CHECKOUT_SHOW_COMMENTS_TITLE','Comments opened?');
define('CHECKOUT_SHOW_COMMENTS_DESC','Should the comments field be shown?');
define('CHECKOUT_SHOW_PRODUCTS_TITLE','Product list opened?');
define('CHECKOUT_SHOW_PRODUCTS_DESC','Should the product list be shown?');
define('CHECKOUT_SHOW_AGB_TITLE','General business conditions opened?');
define('CHECKOUT_SHOW_AGB_DESC','Should the general business conditions be shown?');
define('CHECKOUT_SHOW_DSG_TITLE','Privacy unfolded');
define('CHECKOUT_SHOW_DSG_DESC','Should the Privacy Policy will be opened by default?');
define('CHECKOUT_SHOW_REVOCATION_TITLE','Notice of revocation opened?');
define('CHECKOUT_SHOW_REVOCATION_DESC','Should the notice of revocation be shown?');
define('CHECKOUT_AJAX_PRODUCTS_TITLE','Provide the opportunity of changing the products?');
define('CHECKOUT_AJAX_PRODUCTS_DESC','Should customers easily edit their articles during the checkout process?');
define('CHECKOUT_AJAX_STAT_TITLE','AJAX Checkout Prozess active?');
define('CHECKOUT_AJAX_STAT_DESC','Should customers order their products via an easy and dynamic Checkout page?');

// language definitions for config
define('STORE_NAME_TITLE' , 'Store Name');
define('STORE_NAME_DESC' , 'The name of my store');
define('STORE_OWNER_TITLE' , 'Store Owner');
define('STORE_OWNER_DESC' , 'The name of my store owner');
define('STORE_OWNER_EMAIL_ADDRESS_TITLE' , 'eMail Adress');
define('STORE_OWNER_EMAIL_ADDRESS_DESC' , 'The eMail Adress of my store owner');

define('EMAIL_FROM_TITLE' , 'eMail from');
define('EMAIL_FROM_DESC' , 'The eMail Adress used in (sent) eMails.');

define('STORE_COUNTRY_TITLE' , 'Country');
define('STORE_COUNTRY_DESC' , 'The country my store is located in <br /><br /><b>Note: Please remember to update the store zone.</b>');
define('STORE_ZONE_TITLE' , 'Zone');
define('STORE_ZONE_DESC' , 'The zone my store is located in.');

define('EXPECTED_PRODUCTS_SORT_TITLE' , 'Expected sort order');
define('EXPECTED_PRODUCTS_SORT_DESC' , 'This is the sort order used in the expected products box.');
define('EXPECTED_PRODUCTS_FIELD_TITLE' , 'Expexted sort field');
define('EXPECTED_PRODUCTS_FIELD_DESC' , 'The column to sort by in the expected products box.');

define('USE_DEFAULT_LANGUAGE_CURRENCY_TITLE' , 'Switch to default language currency');
define('USE_DEFAULT_LANGUAGE_CURRENCY_DESC' , 'Automatically switch to the languages currency when it is changed.');

define('SEND_EXTRA_ORDER_EMAILS_TO_TITLE' , 'Send extra order eMails to:');
define('SEND_EXTRA_ORDER_EMAILS_TO_DESC' , 'Send extra order eMails to the following eMail adresses, in this format: Name1 &lt;eMail@adress1&gt;, Name2 &lt;eMail@adress2&gt;');

define('SEARCH_ENGINE_FRIENDLY_URLS_TITLE' , 'Use Search-Engine Safe URLs?');
define('SEARCH_ENGINE_FRIENDLY_URLS_DESC' , 'Use search-engine safe urls for all site links.');

define('DISPLAY_CART_TITLE' , 'Display Cart After Adding a Product?');
define('DISPLAY_CART_DESC' , 'Display the shopping cart after adding a product or return back to their origin?');

define('ALLOW_GUEST_TO_TELL_A_FRIEND_TITLE' , 'Allow Guest To Tell a Friend?');
define('ALLOW_GUEST_TO_TELL_A_FRIEND_DESC' , 'Allow guests to tell a friend about a product?');

define('ADVANCED_SEARCH_DEFAULT_OPERATOR_TITLE' , 'Default Search Operator');
define('ADVANCED_SEARCH_DEFAULT_OPERATOR_DESC' , 'Default search operators.');

define('STORE_NAME_ADDRESS_TITLE' , 'Store Adress and Phone');
define('STORE_NAME_ADDRESS_DESC' , 'This is the Store Name, Adress and Phone used on printable documents and displayed online.');

define('SHOW_COUNTS_TITLE' , 'Show Category Counts');
define('SHOW_COUNTS_DESC' , 'Count recursively how many products are in each category');

define('DISPLAY_PRICE_WITH_TAX_TITLE' , 'Display Prices with Tax');
define('DISPLAY_PRICE_WITH_TAX_DESC' , 'Display prices with tax included (true) or add the tax at the end (false)');

define('DEFAULT_CUSTOMERS_STATUS_ID_ADMIN_TITLE' , 'Customers Status of Administration Members');
define('DEFAULT_CUSTOMERS_STATUS_ID_ADMIN_DESC' , 'Choose the customers status for Members of the Administration Team!');
define('DEFAULT_CUSTOMERS_STATUS_ID_GUEST_TITLE' , 'Customers Status Guest');
define('DEFAULT_CUSTOMERS_STATUS_ID_GUEST_DESC' , 'What would be the default customers status for a guest before logged in?');
define('DEFAULT_CUSTOMERS_STATUS_ID_TITLE' , 'Customers Status for New Customers');
define('DEFAULT_CUSTOMERS_STATUS_ID_DESC' , 'What would be the default customers status for a new customer?');

define('ALLOW_ADD_TO_CART_TITLE' , 'Allow add to cart');
define('ALLOW_ADD_TO_CART_DESC' , 'Allow customers to add products into cart if groupsetting for "show prices" is set to 0');
define('ALLOW_DISCOUNT_ON_PRODUCTS_ATTRIBUTES_TITLE' , 'Allow discount on products attribute?');
define('ALLOW_DISCOUNT_ON_PRODUCTS_ATTRIBUTES_DESC' , 'Allow customers to get discount on attribute price (if main product is not a "special" product)');
define('CURRENT_TEMPLATE_TITLE' , 'Templateset (Theme)');
define('CURRENT_TEMPLATE_DESC' , 'Choose a Templateset (Theme). The Theme must be saved before in the following folder: www.Your-Domain.com/templates/');

define('CC_KEYCHAIN_TITLE','CC String');
define('CC_KEYCHAIN_DESC','String to encrypt CC number (please change!)');

define('ENTRY_FIRST_NAME_MIN_LENGTH_TITLE' , 'First Name');
define('ENTRY_FIRST_NAME_MIN_LENGTH_DESC' , 'Minimum length of first name');
define('ENTRY_LAST_NAME_MIN_LENGTH_TITLE' , 'Last Name');
define('ENTRY_LAST_NAME_MIN_LENGTH_DESC' , 'Minimum length of last name');
define('ENTRY_DOB_MIN_LENGTH_TITLE' , 'Date of Birth');
define('ENTRY_DOB_MIN_LENGTH_DESC' , 'Minimum length of date of birth');
define('ENTRY_EMAIL_ADDRESS_MIN_LENGTH_TITLE' , 'eMail Adress');
define('ENTRY_EMAIL_ADDRESS_MIN_LENGTH_DESC' , 'Minimum length of eMail adress');
define('ENTRY_STREET_ADDRESS_MIN_LENGTH_TITLE' , 'Street Adress');
define('ENTRY_STREET_ADDRESS_MIN_LENGTH_DESC' , 'Minimum length of street adress');
define('ENTRY_COMPANY_MIN_LENGTH_TITLE' , 'Company');
define('ENTRY_COMPANY_MIN_LENGTH_DESC' , 'Minimum length of company name');
define('ENTRY_POSTCODE_MIN_LENGTH_TITLE' , 'Post Code');
define('ENTRY_POSTCODE_MIN_LENGTH_DESC' , 'Minimum length of post code');
define('ENTRY_CITY_MIN_LENGTH_TITLE' , 'City');
define('ENTRY_CITY_MIN_LENGTH_DESC' , 'Minimum length of city');
define('ENTRY_STATE_MIN_LENGTH_TITLE' , 'State');
define('ENTRY_STATE_MIN_LENGTH_DESC' , 'Minimum length of state');
define('ENTRY_TELEPHONE_MIN_LENGTH_TITLE' , 'Telephone Number');
define('ENTRY_TELEPHONE_MIN_LENGTH_DESC' , 'Minimum length of telephone number');
define('ENTRY_PASSWORD_MIN_LENGTH_TITLE' , 'Password');
define('ENTRY_PASSWORD_MIN_LENGTH_DESC' , 'Minimum length of password');

define('CC_OWNER_MIN_LENGTH_TITLE' , 'Credit Card Owner Name');
define('CC_OWNER_MIN_LENGTH_DESC' , 'Minimum length of credit card owner name');
define('CC_NUMBER_MIN_LENGTH_TITLE' , 'Credit Card Number');
define('CC_NUMBER_MIN_LENGTH_DESC' , 'Minimum length of credit card number');

define('REVIEW_TEXT_MIN_LENGTH_TITLE' , 'Reviews');
define('REVIEW_TEXT_MIN_LENGTH_DESC' , 'Minimum length of review text');

define('MIN_DISPLAY_BESTSELLERS_TITLE' , 'Best Sellers');
define('MIN_DISPLAY_BESTSELLERS_DESC' , 'Minimum number of best sellers to display');
define('MIN_DISPLAY_ALSO_PURCHASED_TITLE' , 'Also Purchased');
define('MIN_DISPLAY_ALSO_PURCHASED_DESC' , 'Minimum number of products to display in the "This Customer Also Purchased" box');

define('MAX_ADDRESS_BOOK_ENTRIES_TITLE' , 'Address Book Entries');
define('MAX_ADDRESS_BOOK_ENTRIES_DESC' , 'Maximum address book entries a customer is allowed to have');
define('MAX_DISPLAY_SEARCH_RESULTS_TITLE' , 'Search Results');
define('MAX_DISPLAY_SEARCH_RESULTS_DESC' , 'Amount of products to list');
define('MAX_DISPLAY_PAGE_LINKS_TITLE' , 'Page Links');
define('MAX_DISPLAY_PAGE_LINKS_DESC' , 'Number of "number" links use for page-sets');
define('MAX_DISPLAY_SPECIAL_PRODUCTS_TITLE' , 'Special Products');
define('MAX_DISPLAY_SPECIAL_PRODUCTS_DESC' , 'Maximum number of products on special to display');
define('MAX_DISPLAY_NEW_PRODUCTS_TITLE' , 'New Products Module');
define('MAX_DISPLAY_NEW_PRODUCTS_DESC' , 'Maximum number of new products to display in a category');
define('MAX_DISPLAY_UPCOMING_PRODUCTS_TITLE' , 'Products Expected');
define('MAX_DISPLAY_UPCOMING_PRODUCTS_DESC' , 'Maximum number of products expected to display');
define('MAX_DISPLAY_MANUFACTURERS_IN_A_LIST_TITLE' , 'Manufacturers List');
define('MAX_DISPLAY_MANUFACTURERS_IN_A_LIST_DESC' , 'Used in manufacturers box; when the number of manufacturers exceeds this number, a drop-down list will be displayed instead of the default list');
define('MAX_MANUFACTURERS_LIST_TITLE' , 'Manufacturers Select Size');
define('MAX_MANUFACTURERS_LIST_DESC' , 'Used in manufacturers box; when this value is "1" the classic drop-down list will be used for the manufacturers box. Otherwise, a list-box with the specified number of rows will be displayed.');
define('MAX_DISPLAY_MANUFACTURER_NAME_LEN_TITLE' , 'Length of Manufacturers Name');
define('MAX_DISPLAY_MANUFACTURER_NAME_LEN_DESC' , 'Used in manufacturers box; maximum length of manufacturers name to display');
define('MAX_DISPLAY_NEW_REVIEWS_TITLE' , 'New Reviews');
define('MAX_DISPLAY_NEW_REVIEWS_DESC' , 'Maximum number of new reviews to display');
define('MAX_RANDOM_SELECT_REVIEWS_TITLE' , 'Selection of Random Reviews');
define('MAX_RANDOM_SELECT_REVIEWS_DESC' , 'How many records to select from to choose one random product review');
define('MAX_RANDOM_SELECT_NEW_TITLE' , 'Selection of Random New Products');
define('MAX_RANDOM_SELECT_NEW_DESC' , 'How many records to select from to choose one random new product to display');
define('MAX_RANDOM_SELECT_SPECIALS_TITLE' , 'Selection of Products on Special');
define('MAX_RANDOM_SELECT_SPECIALS_DESC' , 'How many records to select from to choose one random product special to display');
define('MAX_DISPLAY_CATEGORIES_PER_ROW_TITLE' , 'Categories To List Per Row');
define('MAX_DISPLAY_CATEGORIES_PER_ROW_DESC' , 'How many categories to list per row');
define('MAX_DISPLAY_PRODUCTS_NEW_TITLE' , 'New Products Listing');
define('MAX_DISPLAY_PRODUCTS_NEW_DESC' , 'Maximum number of new products to display in new products page');
define('MAX_DISPLAY_BESTSELLERS_TITLE' , 'Best Sellers');
define('MAX_DISPLAY_BESTSELLERS_DESC' , 'Maximum number of best sellers to display');
define('MAX_DISPLAY_ALSO_PURCHASED_TITLE' , 'Also Purchased');
define('MAX_DISPLAY_ALSO_PURCHASED_DESC' , 'Maximum number of products to display in the "This Customer Also Purchased" box');
define('MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX_TITLE' , 'Customer Order History Box');
define('MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX_DESC' , 'Maximum number of products to display in the customer order history box');
define('MAX_DISPLAY_ORDER_HISTORY_TITLE' , 'Order History');
define('MAX_DISPLAY_ORDER_HISTORY_DESC' , 'Maximum number of orders to display in the order history page');
define('MAX_PRODUCTS_QTY_TITLE', 'Maximum Quantity');
define('MAX_PRODUCTS_QTY_DESC', 'Maximum quantity input length');
define('MAX_DISPLAY_NEW_PRODUCTS_DAYS_TITLE' , 'Maximum days for new products');
define('MAX_DISPLAY_NEW_PRODUCTS_DAYS_DESC' , 'Maximum quantity of days new products to display');


define('PRODUCT_IMAGE_MINI_WIDTH_TITLE' , 'Width of Mini-Images');
define('PRODUCT_IMAGE_MINI_WIDTH_DESC' , 'Maximal Width of Mini-Images in Pixel');
define('PRODUCT_IMAGE_MINI_HEIGHT_TITLE' , 'Height of Mini-Images');
define('PRODUCT_IMAGE_MINI_HEIGHT_DESC' , 'Maximal Height of Mini-Images in Pixel');

define('PRODUCT_IMAGE_THUMBNAIL_WIDTH_TITLE' , 'Width of Product-Thumbnails');
define('PRODUCT_IMAGE_THUMBNAIL_WIDTH_DESC' , 'Maximal Width of Product-Thumbnails in Pixel');
define('PRODUCT_IMAGE_THUMBNAIL_HEIGHT_TITLE' , 'Height of Product-Thumbnails');
define('PRODUCT_IMAGE_THUMBNAIL_HEIGHT_DESC' , 'Maximal Height of Product-Thumbnails in Pixel');

define('PRODUCT_IMAGE_INFO_WIDTH_TITLE' , 'Width of Product-Info Images');
define('PRODUCT_IMAGE_INFO_WIDTH_DESC' , 'Maximal Width of Product-Info Images in Pixel');
define('PRODUCT_IMAGE_INFO_HEIGHT_TITLE' , 'Height of Product-Info Images');
define('PRODUCT_IMAGE_INFO_HEIGHT_DESC' , 'Maximal Height of Product-Info Images in Pixel');

define('CATEGORY_IMAGE_WIDTH_TITLE' , 'Width of Product-Info Images');
define('CATEGORY_IMAGE_WIDTH_DESC' , 'Maximal Width of Product-Info Images in Pixel');
define('CATEGORY_IMAGE_HEIGHT_TITLE' , 'Height of Product-Info Images');
define('CATEGORY_IMAGE_HEIGHT_DESC' , 'Maximal Height of Product-Info Images in Pixel');

define('PRODUCT_IMAGE_POPUP_WIDTH_TITLE' , 'Width of Popup Images');
define('PRODUCT_IMAGE_POPUP_WIDTH_DESC' , 'Maximal Width of Popup Images in Pixel');
define('PRODUCT_IMAGE_POPUP_HEIGHT_TITLE' , 'Height of Popup Images');
define('PRODUCT_IMAGE_POPUP_HEIGHT_DESC' , 'Maximal Height of Popup Images in Pixel');

define('SMALL_IMAGE_WIDTH_TITLE' , 'Small Image Width');
define('SMALL_IMAGE_WIDTH_DESC' , 'The pixel width of small images');
define('SMALL_IMAGE_HEIGHT_TITLE' , 'Small Image Height');
define('SMALL_IMAGE_HEIGHT_DESC' , 'The pixel height of small images');

define('HEADING_IMAGE_WIDTH_TITLE' , 'Heading Image Width');
define('HEADING_IMAGE_WIDTH_DESC' , 'The pixel width of heading images');
define('HEADING_IMAGE_HEIGHT_TITLE' , 'Heading Image Height');
define('HEADING_IMAGE_HEIGHT_DESC' , 'The pixel height of heading images');

define('SUBCATEGORY_IMAGE_WIDTH_TITLE' , 'Subcategory Image Width');
define('SUBCATEGORY_IMAGE_WIDTH_DESC' , 'The pixel width of subcategory images');
define('SUBCATEGORY_IMAGE_HEIGHT_TITLE' , 'Subcategory Image Height');
define('SUBCATEGORY_IMAGE_HEIGHT_DESC' , 'The pixel height of subcategory images');

define('CONFIG_CALCULATE_IMAGE_SIZE_TITLE' , 'Calculate Image Size');
define('CONFIG_CALCULATE_IMAGE_SIZE_DESC' , 'Calculate the size of images?');

define('IMAGE_REQUIRED_TITLE' , 'Image Required');
define('IMAGE_REQUIRED_DESC' , 'Enable to display broken images. Good for development.');

// Kategorie Bilder

define('CATEGORY_IMAGE_BEVEL_TITLE' , 'Categories-Images:Bevel');
define('CATEGORY_IMAGE_BEVEL_DESC' , 'Categories-Images:Bevel<br /><br />Default-values: (8,FFCCCC,330000)<br /><br />shaded bevelled edges<br />Usage:<br />(edge width, hex light colour, hex dark colour)');
define('CATEGORY_IMAGE_GREYSCALE_TITLE' , 'Categories-Images:Greyscale');
define('CATEGORY_IMAGE_GREYSCALE_DESC' , 'Categories-Images:Greyscale<br /><br />Default-values: (32,22,22)<br /><br />basic black n white<br />Usage:<br />(int red, int green, int blue)');
define('CATEGORY_IMAGE_ELLIPSE_TITLE' , 'Categories-Images:Ellipse');
define('CATEGORY_IMAGE_ELLIPSE_DESC' , 'Categories-Images:Ellipse<br /><br />Default-values: (FFFFFF)<br /><br />ellipse on bg colour<br />Usage:<br />(hex background colour)');
define('CATEGORY_IMAGE_ROUND_EDGES_TITLE' , 'Categories-Images:Round-edges');
define('CATEGORY_IMAGE_ROUND_EDGES_DESC' , 'Categories-Images:Round-edges<br /><br />Default-values: (5,FFFFFF,3)<br /><br />corner trimming<br />Usage:<br />( edge_radius, background colour, anti-alias width)');
define('CATEGORY_IMAGE_MERGE_TITLE' , 'Categories-Images:Merge');
define('CATEGORY_IMAGE_MERGE_DESC' , 'Categories-Images:Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity,transparent colour on merge image)');
define('CATEGORY_IMAGE_FRAME_TITLE' , 'Categories-Images:Frame');
define('CATEGORY_IMAGE_FRAME_DESC' , 'Categories-Images:Frame<br /><br />Default-values: (FFFFFF,000000,3,EEEEEE)<br /><br />plain raised border<br />Usage:<br />(hex light colour,hex dark colour,int width of mid bit,hex frame colour [optional - defaults to half way between light and dark edges])');
define('CATEGORY_IMAGE_DROP_SHADDOW_TITLE' , 'Categories-Images:Drop-Shadow');
define('CATEGORY_IMAGE_DROP_SHADDOW_DESC' , 'Categories-Images:Drop-Shadow<br /><br />Default-values: (3,333333,FFFFFF)<br /><br />more like a dodgy motion blur [semi buggy]<br />Usage:<br />(shadow width,hex shadow colour,hex background colour)');
define('CATEGORY_IMAGE_MOTION_BLUR_TITLE' , 'Categories-Images:Motion-Blur');
define('CATEGORY_IMAGE_MOTION_BLUR_DESC' , 'Categories-Images:Motion-Blur<br /><br />Default-values: (4,FFFFFF)<br /><br />fading parallel lines<br />Usage:<br />(int number of lines,hex background colour)');

//Mini Images

define('PRODUCT_IMAGE_MINI_BEVEL_TITLE' , 'Mini-Images:Bevel<br /><img src="images/config_bevel.gif">');
define('PRODUCT_IMAGE_MINI_BEVEL_DESC' , 'Mini-Images:Bevel<br /><br />Default-values: (8,FFCCCC,330000)<br /><br />shaded bevelled edges<br />Usage:<br />(edge width,hex light colour,hex dark colour)');
define('PRODUCT_IMAGE_MINI_GREYSCALE_TITLE' , 'Mini-Images:Greyscale<br /><img src="images/config_greyscale.gif">');
define('PRODUCT_IMAGE_MINI_GREYSCALE_DESC' , 'Mini-Images:Greyscale<br /><br />Default-values: (32,22,22)<br /><br />basic black n white<br />Usage:<br />(int red,int green,int blue)');
define('PRODUCT_IMAGE_MINI_ELLIPSE_TITLE' , 'Mini-Images:Ellipse<br /><img src="images/config_eclipse.gif">');
define('PRODUCT_IMAGE_MINI_ELLIPSE_DESC' , 'Mini-Images:Ellipse<br /><br />Default-values: (FFFFFF)<br /><br />ellipse on bg colour<br />Usage:<br />(hex background colour)');
define('PRODUCT_IMAGE_MINI_ROUND_EDGES_TITLE' , 'Mini-Images:Round-edges<br /><img src="images/config_edge.gif">');
define('PRODUCT_IMAGE_MINI_ROUND_EDGES_DESC' , 'Mini-Images:Round-edges<br /><br />Default-values: (5,FFFFFF,3)<br /><br />corner trimming<br />Usage:<br />(edge_radius,background colour,anti-alias width)');
define('PRODUCT_IMAGE_MINI_MERGE_TITLE' , 'Mini-Images:Merge<br /><img src="images/config_merge.gif">');
define('PRODUCT_IMAGE_MINI_MERGE_DESC' , 'Mini-Images:Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity, transparent colour on merge image)');
define('PRODUCT_IMAGE_MINI_FRAME_TITLE' , 'Mini-Images:Frame<br /><img src="images/config_frame.gif">');
define('PRODUCT_IMAGE_MINI_FRAME_DESC' , 'Mini-Images:Frame<br /><br />Default-values: (FFFFFF,000000,3,EEEEEE)<br /><br />plain raised border<br />Usage:<br />(hex light colour,hex dark colour,int width of mid bit,hex frame colour [optional - defaults to half way between light and dark edges])');
define('PRODUCT_IMAGE_MINI_DROP_SHADDOW_TITLE' , 'Mini-Images:Drop-Shadow<br /><img src="images/config_shadow.gif">');
define('PRODUCT_IMAGE_MINI_DROP_SHADDOW_DESC' , 'Mini-Images:Drop-Shadow<br /><br />Default-values: (3,333333,FFFFFF)<br /><br />more like a dodgy motion blur [semi buggy]<br />Usage:<br />(shadow width,hex shadow colour,hex background colour)');
define('PRODUCT_IMAGE_MINI_MOTION_BLUR_TITLE' , 'Mini-Images:Motion-Blur<br /><img src="images/config_motion.gif">');
define('PRODUCT_IMAGE_MINI_MOTION_BLUR_DESC' , 'Mini-Images:Motion-Blur<br /><br />Default-values: (4,FFFFFF)<br /><br />fading parallel lines<br />Usage:<br />(int number of lines,hex background colour)');
//This is for the Images showing your products for preview. All the small stuff.

define('PRODUCT_IMAGE_THUMBNAIL_BEVEL_TITLE' , 'Products-Thumbnails:Bevel<br /><img src="images/config_bevel.gif">');
define('PRODUCT_IMAGE_THUMBNAIL_BEVEL_DESC' , 'Products-Thumbnails:Bevel<br /><br />Default-values: (8,FFCCCC,330000)<br /><br />shaded bevelled edges<br />Usage:<br />(edge width,hex light colour,hex dark colour)');
define('PRODUCT_IMAGE_THUMBNAIL_GREYSCALE_TITLE' , 'Products-Thumbnails:Greyscale<br /><img src="images/config_greyscale.gif">');
define('PRODUCT_IMAGE_THUMBNAIL_GREYSCALE_DESC' , 'Products-Thumbnails:Greyscale<br /><br />Default-values: (32,22,22)<br /><br />basic black n white<br />Usage:<br />(int red,int green,int blue)');
define('PRODUCT_IMAGE_THUMBNAIL_ELLIPSE_TITLE' , 'Products-Thumbnails:Ellipse<br /><img src="images/config_eclipse.gif">');
define('PRODUCT_IMAGE_THUMBNAIL_ELLIPSE_DESC' , 'Products-Thumbnails:Ellipse<br /><br />Default-values: (FFFFFF)<br /><br />ellipse on bg colour<br />Usage:<br />(hex background colour)');
define('PRODUCT_IMAGE_THUMBNAIL_ROUND_EDGES_TITLE' , 'Products-Thumbnails:Round-edges<br /><img src="images/config_edge.gif">');
define('PRODUCT_IMAGE_THUMBNAIL_ROUND_EDGES_DESC' , 'Products-Thumbnails:Round-edges<br /><br />Default-values: (5,FFFFFF,3)<br /><br />corner trimming<br />Usage:<br />(edge_radius,background colour,anti-alias width)');
define('PRODUCT_IMAGE_THUMBNAIL_MERGE_TITLE' , 'Products-Thumbnails:Merge<br /><img src="images/config_merge.gif">');
define('PRODUCT_IMAGE_THUMBNAIL_MERGE_DESC' , 'Products-Thumbnails:Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity, transparent colour on merge image)');
define('PRODUCT_IMAGE_THUMBNAIL_FRAME_TITLE' , 'Products-Thumbnails:Frame<br /><img src="images/config_frame.gif">');
define('PRODUCT_IMAGE_THUMBNAIL_FRAME_DESC' , 'Products-Thumbnails:Frame<br /><br />Default-values: (FFFFFF,000000,3,EEEEEE)<br /><br />plain raised border<br />Usage:<br />(hex light colour,hex dark colour,int width of mid bit,hex frame colour [optional - defaults to half way between light and dark edges])');
define('PRODUCT_IMAGE_THUMBNAIL_DROP_SHADDOW_TITLE' , 'Products-Thumbnails:Drop-Shadow<br /><img src="images/config_shadow.gif">');
define('PRODUCT_IMAGE_THUMBNAIL_DROP_SHADDOW_DESC' , 'Products-Thumbnails:Drop-Shadow<br /><br />Default-values: (3,333333,FFFFFF)<br /><br />more like a dodgy motion blur [semi buggy]<br />Usage:<br />(shadow width,hex shadow colour,hex background colour)');
define('PRODUCT_IMAGE_THUMBNAIL_MOTION_BLUR_TITLE' , 'Products-Thumbnails:Motion-Blur<br /><img src="images/config_motion.gif">');
define('PRODUCT_IMAGE_THUMBNAIL_MOTION_BLUR_DESC' , 'Products-Thumbnails:Motion-Blur<br /><br />Default-values: (4,FFFFFF)<br /><br />fading parallel lines<br />Usage:<br />(int number of lines,hex background colour)');

//And this is for the Images showing your products in single-view

define('PRODUCT_IMAGE_INFO_BEVEL_TITLE' , 'Product-Images:Bevel');
define('PRODUCT_IMAGE_INFO_BEVEL_DESC' , 'Product-Images:Bevel<br /><br />Default-values: (8,FFCCCC,330000)<br /><br />shaded bevelled edges<br />Usage:<br />(edge width, hex light colour, hex dark colour)');
define('PRODUCT_IMAGE_INFO_GREYSCALE_TITLE' , 'Product-Images:Greyscale');
define('PRODUCT_IMAGE_INFO_GREYSCALE_DESC' , 'Product-Images:Greyscale<br /><br />Default-values: (32,22,22)<br /><br />basic black n white<br />Usage:<br />(int red, int green, int blue)');
define('PRODUCT_IMAGE_INFO_ELLIPSE_TITLE' , 'Product-Images:Ellipse');
define('PRODUCT_IMAGE_INFO_ELLIPSE_DESC' , 'Product-Images:Ellipse<br /><br />Default-values: (FFFFFF)<br /><br />ellipse on bg colour<br />Usage:<br />(hex background colour)');
define('PRODUCT_IMAGE_INFO_ROUND_EDGES_TITLE' , 'Product-Images:Round-edges');
define('PRODUCT_IMAGE_INFO_ROUND_EDGES_DESC' , 'Product-Images:Round-edges<br /><br />Default-values: (5,FFFFFF,3)<br /><br />corner trimming<br />Usage:<br />( edge_radius, background colour, anti-alias width)');
define('PRODUCT_IMAGE_INFO_MERGE_TITLE' , 'Product-Images:Merge');
define('PRODUCT_IMAGE_INFO_MERGE_DESC' , 'Product-Images:Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity,transparent colour on merge image)');
define('PRODUCT_IMAGE_INFO_FRAME_TITLE' , 'Product-Images:Frame');
define('PRODUCT_IMAGE_INFO_FRAME_DESC' , 'Product-Images:Frame<br /><br />Default-values: (FFFFFF,000000,3,EEEEEE)<br /><br />plain raised border<br />Usage:<br />(hex light colour,hex dark colour,int width of mid bit,hex frame colour [optional - defaults to half way between light and dark edges])');
define('PRODUCT_IMAGE_INFO_DROP_SHADDOW_TITLE' , 'Product-Images:Drop-Shadow');
define('PRODUCT_IMAGE_INFO_DROP_SHADDOW_DESC' , 'Product-Images:Drop-Shadow<br /><br />Default-values: (3,333333,FFFFFF)<br /><br />more like a dodgy motion blur [semi buggy]<br />Usage:<br />(shadow width,hex shadow colour,hex background colour)');
define('PRODUCT_IMAGE_INFO_MOTION_BLUR_TITLE' , 'Product-Images:Motion-Blur');
define('PRODUCT_IMAGE_INFO_MOTION_BLUR_DESC' , 'Product-Images:Motion-Blur<br /><br />Default-values: (4,FFFFFF)<br /><br />fading parallel lines<br />Usage:<br />(int number of lines,hex background colour)');

//so this image is the biggest in the shop this

define('PRODUCT_IMAGE_POPUP_BEVEL_TITLE' , 'Product-Popup-Images:Bevel');
define('PRODUCT_IMAGE_POPUP_BEVEL_DESC' , 'Product-Popup-Images:Bevel<br /><br />Default-values: (8,FFCCCC,330000)<br /><br />shaded bevelled edges<br />Usage:<br />(edge width,hex light colour,hex dark colour)');
define('PRODUCT_IMAGE_POPUP_GREYSCALE_TITLE' , 'Product-Popup-Images:Greyscale');
define('PRODUCT_IMAGE_POPUP_GREYSCALE_DESC' , 'Product-Popup-Images:Greyscale<br /><br />Default-values: (32,22,22)<br /><br />basic black n white<br />Usage:<br />(int red,int green,int blue)');
define('PRODUCT_IMAGE_POPUP_ELLIPSE_TITLE' , 'Product-Popup-Images:Ellipse');
define('PRODUCT_IMAGE_POPUP_ELLIPSE_DESC' , 'Product-Popup-Images:Ellipse<br /><br />Default-values: (FFFFFF)<br /><br />ellipse on bg colour<br />Usage:<br />(hex background colour)');
define('PRODUCT_IMAGE_POPUP_ROUND_EDGES_TITLE' , 'Product-Popup-Images:Round-edges');
define('PRODUCT_IMAGE_POPUP_ROUND_EDGES_DESC' , 'Product-Popup-Images:Round-edges<br /><br />Default-values: (5,FFFFFF,3)<br /><br />corner trimming<br />Usage:<br />(edge_radius,background colour,anti-alias width)');
define('PRODUCT_IMAGE_POPUP_MERGE_TITLE' , 'Product-Popup-Images:Merge');
define('PRODUCT_IMAGE_POPUP_MERGE_DESC' , 'Product-Popup-Images:Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity,transparent colour on merge image)');
define('PRODUCT_IMAGE_POPUP_FRAME_TITLE' , 'Product-Popup-Images:Frame');
define('PRODUCT_IMAGE_POPUP_FRAME_DESC' , 'Product-Popup-Images:Frame<br /><br />Default-values: (FFFFFF,000000,3,EEEEEE)<br /><br />plain raised border<br />Usage:<br />(hex light colour,hex dark colour,int width of mid bit,hex frame colour [optional - defaults to half way between light and dark edges])');
define('PRODUCT_IMAGE_POPUP_DROP_SHADDOW_TITLE' , 'Product-Popup-Images:Drop-Shadow');
define('PRODUCT_IMAGE_POPUP_DROP_SHADDOW_DESC' , 'Product-Popup-Images:Drop-Shadow<br /><br />Default-values: (3,333333,FFFFFF)<br /><br />more like a dodgy motion blur [semi buggy]<br />Usage:<br />(shadow width,hex shadow colour,hex background colour)');
define('PRODUCT_IMAGE_POPUP_MOTION_BLUR_TITLE' , 'Product-Popup-Images:Motion-Blur');
define('PRODUCT_IMAGE_POPUP_MOTION_BLUR_DESC' , 'Product-Popup-Images:Motion-Blur<br /><br />Default-values: (4,FFFFFF)<br /><br />fading parallel lines<br />Usage:<br />(int number of lines,hex background colour)');

define('MO_PICS_TITLE','Number of products images');
define('MO_PICS_DESC','if this number is set > 0 , you will be able to upload/display more images per product');

define('IMAGE_MANIPULATOR_TITLE','GDlib processing');
define('IMAGE_MANIPULATOR_DESC','Image Manipulator for GD2 or GD1');

define('ACCOUNT_GENDER_TITLE' , 'Gender');
define('ACCOUNT_GENDER_DESC' , 'Display gender in the customers account');
define('ACCOUNT_DOB_TITLE' , 'Date of Birth');
define('ACCOUNT_DOB_DESC' , 'Display date of birth in the customers account');
define('ACCOUNT_COMPANY_TITLE' , 'Company');
define('ACCOUNT_COMPANY_DESC' , 'Display company in the customers account');
define('ACCOUNT_SUBURB_TITLE' , 'Suburb');
define('ACCOUNT_SUBURB_DESC' , 'Display suburb in the customers account');
define('ACCOUNT_STATE_TITLE' , 'State');
define('ACCOUNT_STATE_DESC' , 'Display state in the customers account');

define('DEFAULT_CURRENCY_TITLE' , 'Default Currency');
define('DEFAULT_CURRENCY_DESC' , 'Currency which is used as default');
define('DEFAULT_LANGUAGE_TITLE' , 'Default Language');
define('DEFAULT_LANGUAGE_DESC' , 'Language which is used as default');
define('DEFAULT_ORDERS_STATUS_ID_TITLE' , 'Default Order Status');
define('DEFAULT_ORDERS_STATUS_ID_DESC' , 'Default order status when a new order is placed.');

define('SHIPPING_ORIGIN_COUNTRY_TITLE' , 'Country of Origin');
define('SHIPPING_ORIGIN_COUNTRY_DESC' , 'Select the country of origin to be used in shipping quotes.');
define('SHIPPING_ORIGIN_ZIP_TITLE' , 'Postal Code');
define('SHIPPING_ORIGIN_ZIP_DESC' , 'Enter the Postal Code (ZIP) of the Store to be used in shipping quotes.');
define('SHIPPING_MAX_WEIGHT_TITLE' , 'Enter the Maximum Package Weight you will ship');
define('SHIPPING_MAX_WEIGHT_DESC' , 'Carriers have a max weight limit for a single package. This is a common one for all.');
define('SHIPPING_BOX_WEIGHT_TITLE' , 'Package Tare weight.');
define('SHIPPING_BOX_WEIGHT_DESC' , 'What is the weight of typical packaging of small to medium packages?');
define('SHIPPING_BOX_PADDING_TITLE' , 'Larger packages - percentage increase.');
define('SHIPPING_BOX_PADDING_DESC' , 'For 10% enter 10');
define('SHOW_SHIPPING_DESC' , 'Show shippingcosts link in product infos');
define('SHOW_SHIPPING_TITLE' , 'Shippingcosts in product infos');
define('SHIPPING_INFOS_DESC' , 'Group ID of shippingcosts content.');
define('SHIPPING_INFOS_TITLE' , 'Group ID');

define('PRODUCT_LIST_FILTER_TITLE' , 'Display Category/Manufacturer Filter (0=disable; 1=enable)');
define('PRODUCT_LIST_FILTER_DESC' , 'Do you want to display the Category/Manufacturer Filter?');

define('STOCK_CHECK_TITLE' , 'Check stock level');
define('STOCK_CHECK_DESC' , 'Check to see if sufficent stock is available');

define('ATTRIBUTE_STOCK_CHECK_TITLE' , 'Check attribute-stock level');
define('ATTRIBUTE_STOCK_CHECK_DESC' , 'Check to see if sufficent attribute-stock is available');

define('LOG_PRODUCT_RETURNS_TITLE', 'Log Products Returned on Search Queries');
define('LOG_PRODUCT_RETURNS_DESC', 'When using Keyword Stats reporting, also track what products were returned during the search query.');

define('STOCK_LIMITED_TITLE' , 'Subtract stock');
define('STOCK_LIMITED_DESC' , 'Subtract product in stock by product orders');
define('STOCK_ALLOW_CHECKOUT_TITLE' , 'Allow Checkout');
define('STOCK_ALLOW_CHECKOUT_DESC' , 'Allow customer to checkout even if there is insufficient stock');
define('STOCK_MARK_PRODUCT_OUT_OF_STOCK_TITLE' , 'Mark product out of stock');
define('STOCK_MARK_PRODUCT_OUT_OF_STOCK_DESC' , 'Display something on screen so customer can see which product has insufficient stock');
define('STOCK_REORDER_LEVEL_TITLE' , 'Stock Re-order level');
define('STOCK_REORDER_LEVEL_DESC' , 'Define when stock needs to be re-ordered');

define('STORE_PAGE_PARSE_TIME_TITLE' , 'Store Page Parse Time');
define('STORE_PAGE_PARSE_TIME_DESC' , 'Store the time it takes to parse a page');
define('STORE_PAGE_PARSE_TIME_LOG_TITLE' , 'Log Destination');
define('STORE_PAGE_PARSE_TIME_LOG_DESC' , 'Directory and filename of the page parse time log');
define('STORE_PARSE_DATE_TIME_FORMAT_TITLE' , 'Log Date Format');
define('STORE_PARSE_DATE_TIME_FORMAT_DESC' , 'The date format');

define('STOCK_WARNING_LISTING_TITLE' , 'Lagerampel in der Produkt&uuml;bersicht');
define('STOCK_WARNING_LISTING_DESC' , 'Soll in der Produkt&uuml;bersicht eine Lagerampel erscheinen?');
define('STOCK_WARNING_INFO_TITLE' , 'Lagerampel in der Produktdetailseite?');
define('STOCK_WARNING_INFO_DESC' , 'Soll in der Produktdetailseite eine Lagerampel erscheinen?');

define('TRUSTED_SHOP_STATUS_TITLE','Status');
define('TRUSTED_SHOP_STATUS_DESC','Soll die Box f&uuml;r Trusted Shopangezeigt werden?');
define('TRUSTED_SHOP_NR_TITLE','Shop ID');
define('TRUSTED_SHOP_NR_DESC','Tragen Sie hier die von Trusted Shop vergeben Nummer ein.');
define('TRUSTED_SHOP_TEMPLATE_TITLE','Template f&uuml;r die Box');
define('TRUSTED_SHOP_TEMPLATE_DESC','W&auml;hlen Sie das Template f&uuml;r Ihre Box.');

define('DISPLAY_PAGE_PARSE_TIME_TITLE' , 'Display The Page Parse Time');
define('DISPLAY_PAGE_PARSE_TIME_DESC' , 'Display the page parse time (store page parse time must be enabled)');

define('STORE_DB_TRANSACTIONS_TITLE' , 'Store Database Queries');
define('STORE_DB_TRANSACTIONS_DESC' , 'Store the database queries in the page parse time log (PHP4 only)');

define('USE_CACHE_TITLE' , 'Use Cache');
define('USE_CACHE_DESC' , 'Use caching features');

define('DB_CACHE_TITLE','Database Caching');
define('DB_CACHE_TITLE','If set to true, Shop can cache SELECT Queries for a period of time, to increase speed');

define('DIR_FS_CACHE_TITLE' , 'Cache Directory');
define('DIR_FS_CACHE_DESC' , 'The directory where the cached files are saved');

define('ACCOUNT_OPTIONS_TITLE','Account Options');
define('ACCOUNT_OPTIONS_DESC','How do you want to manage the login management of your store ?<br />You can choose between Customer Accounts and "One Time Orders" without creating a Customer Account (an account will be created but the customer wont be informed about that)');

define('EMAIL_TRANSPORT_TITLE' , 'eMail Transport Method');
define('EMAIL_TRANSPORT_DESC' , 'Defines if this server uses a local connection to sendmail or uses an SMTP connection via TCP/IP. Servers running on Windows and MacOS should change this setting to SMTP.');

define('EMAIL_LINEFEED_TITLE' , 'eMail Linefeeds');
define('EMAIL_LINEFEED_DESC' , 'Defines the character sequence used to separate mail headers.');
define('EMAIL_USE_HTML_TITLE' , 'Use MIME HTML When Sending eMails');
define('EMAIL_USE_HTML_DESC' , 'Send eMails in HTML format');
define('ENTRY_EMAIL_ADDRESS_CHECK_TITLE' , 'Verify eMail Addresses Through DNS');
define('ENTRY_EMAIL_ADDRESS_CHECK_DESC' , 'Verify eMail address through a DNS server');
define('SEND_EMAILS_TITLE' , 'Send eMails');
define('SEND_EMAILS_DESC' , 'Send out eMails');
define('SENDMAIL_PATH_TITLE' , 'The Path to sendmail');
define('SENDMAIL_PATH_DESC' , 'If you use sendmail, you should give us the right path (default: /usr/bin/sendmail):');
define('SMTP_MAIN_SERVER_TITLE' , 'Adress of the SMTP Server');
define('SMTP_MAIN_SERVER_DESC' , 'Please enter the adress of your main SMTP Server.');
define('SMTP_BACKUP_SERVER_TITLE' , 'Adress of the SMTP Backup Server');
define('SMTP_BACKUP_SERVER_DESC' , 'Please enter the adress of your Backup SMTP Server.');
define('SMTP_USERNAME_TITLE' , 'SMTP Username');
define('SMTP_USERNAME_DESC' , 'Please enter the username of your SMTP Account.');
define('SMTP_PASSWORD_TITLE' , 'SMTP Password');
define('SMTP_PASSWORD_DESC' , 'Please enter the password of your SMTP Account.');
define('SMTP_AUTH_TITLE' , 'SMTP AUTH');
define('SMTP_AUTH_DESC' , 'Does your SMTP Server needs secure authentication?');
define('SMTP_PORT_TITLE' , 'SMTP Port');
define('SMTP_PORT_DESC' , 'Please enter the SMTP port of your SMTP server(default: 25)?');

//Constants for contact_us
define('CONTACT_US_EMAIL_ADDRESS_TITLE' , 'Contact Us - eMail address');
define('CONTACT_US_EMAIL_ADDRESS_DESC' , 'Please enter an eMail Address used for normal "Contact Us" messages via shop to your office');
define('CONTACT_US_NAME_TITLE' , 'Contact Us - eMail address, name');
define('CONTACT_US_NAME_DESC' , 'Please Enter a name used for normal "Contact Us" messages sentded via shop to your office');
define('CONTACT_US_FORWARDING_STRING_TITLE' , 'Contact Us - forwaring addresses');
define('CONTACT_US_FORWARDING_STRING_DESC' , 'Please enter eMail addresses (seperated by , ) where "Contact Us" messages, sent via shop to your office, should be forwarded to.');
define('CONTACT_US_REPLY_ADDRESS_TITLE' , 'Contact Us - reply address');
define('CONTACT_US_REPLY_ADDRESS_DESC' , 'Please enter an eMail address where customers can reply to.');
define('CONTACT_US_REPLY_ADDRESS_NAME_TITLE' , 'Contact Us - reply address , name');
define('CONTACT_US_REPLY_ADDRESS_NAME_DESC' , 'Sender name for reply eMails.');
define('CONTACT_US_EMAIL_SUBJECT_TITLE' , 'Contact Us - eMail subject');
define('CONTACT_US_EMAIL_SUBJECT_DESC' , 'Please enter an eMail Subject for the contact-us messages via shop to your office.');

//Constants for support system
define('EMAIL_SUPPORT_ADDRESS_TITLE' , 'Technical Support - eMail adress');
define('EMAIL_SUPPORT_ADDRESS_DESC' , 'Please enter an eMail adress for sending eMails over the <b>Support System</b> (account creation, password changes).');
define('EMAIL_SUPPORT_NAME_TITLE' , 'Technical Support - eMail adress, name');
define('EMAIL_SUPPORT_NAME_DESC' , 'Please enter a name for sending eMails over the <b>Support System</b> (account creation, password changes).');
define('EMAIL_SUPPORT_FORWARDING_STRING_TITLE' , 'Technical Support - Forwarding adresses');
define('EMAIL_SUPPORT_FORWARDING_STRING_DESC' , 'Please enter forwarding adresses for the mails of the <b>Support System</b> (seperated by , )');
define('EMAIL_SUPPORT_REPLY_ADDRESS_TITLE' , 'Technical Support - reply adress');
define('EMAIL_SUPPORT_REPLY_ADDRESS_DESC' , 'Please enter an eMail adress for replies of your customers.');
define('EMAIL_SUPPORT_REPLY_ADDRESS_NAME_TITLE' , 'Technical Support - reply adress, name');
define('EMAIL_SUPPORT_REPLY_ADDRESS_NAME_DESC' , 'Please enter a sender name for the eMail adress for replies of your customers.');
define('EMAIL_SUPPORT_SUBJECT_TITLE' , 'Technical Support - eMail subject');
define('EMAIL_SUPPORT_SUBJECT_DESC' , 'Please enter an eMail subject for the <b>Support System</b> messages via shop to your office.');

//Constants for Billing system
define('EMAIL_BILLING_ADDRESS_TITLE' , 'Billing - eMail adress');
define('EMAIL_BILLING_ADDRESS_DESC' , 'Please enter an eMail adress for sending eMails over the <b>Billing system</b> (order confirmations, status changes,..).');
define('EMAIL_BILLING_NAME_TITLE' , 'Billing - eMail adress, name');
define('EMAIL_BILLING_NAME_DESC' , 'Please enter a name for sending eMails over the <b>Billing System</b> (order confirmations, status changes,..).');
define('EMAIL_BILLING_FORWARDING_STRING_TITLE' , 'Billing - Forwarding adresses');
define('EMAIL_BILLING_FORWARDING_STRING_DESC' , 'Please enter forwarding adresses for the mails of the <b>Billing System</b> (seperated by , )');
define('EMAIL_BILLING_REPLY_ADDRESS_TITLE' , 'Billing - reply adress');
define('EMAIL_BILLING_REPLY_ADDRESS_DESC' , 'Please enter an eMail adress for replies of your customers.');
define('EMAIL_BILLING_REPLY_ADDRESS_NAME_TITLE' , 'Billing - reply adress, name');
define('EMAIL_BILLING_REPLY_ADDRESS_NAME_DESC' , 'Please enter a name for the eMail adress for replies of your customers.');
define('EMAIL_BILLING_SUBJECT_TITLE' , 'Billing - eMail subject');
define('EMAIL_BILLING_SUBJECT_DESC' , 'Please enter an eMail Subject for the <b>Billing</b> messages via shop to your office.');
define('EMAIL_BILLING_SUBJECT_ORDER_TITLE','Billing - Ordermail subject');
define('EMAIL_BILLING_SUBJECT_ORDER_DESC','Please enter a subject for ordermails generated from xtc. (like <b>our order {$nr},{$date}</b>) ps: you can use, {$nr},{$date},{$firstname},{$lastname}');


define('DOWNLOAD_ENABLED_TITLE' , 'Enable download');
define('DOWNLOAD_ENABLED_DESC' , 'Enable the products download functions.');
define('DOWNLOAD_BY_REDIRECT_TITLE' , 'Download by redirect');
define('DOWNLOAD_BY_REDIRECT_DESC' , 'Use browser redirection for download. Disable on non-Unix systems.');
define('DOWNLOAD_MAX_DAYS_TITLE' , 'Expiry delay (days)');
define('DOWNLOAD_MAX_DAYS_DESC' , 'Set number of days before the download link expires. 0 means no limit.');
define('DOWNLOAD_MAX_COUNT_TITLE' , 'Maximum number of downloads');
define('DOWNLOAD_MAX_COUNT_DESC' , 'Set the maximum number of downloads. 0 means no download authorized.');

define('GZIP_COMPRESSION_TITLE' , 'Enable GZip Compression');
define('GZIP_COMPRESSION_DESC' , 'Enable HTTP GZip compression.');
define('GZIP_LEVEL_TITLE' , 'Compression Level');
define('GZIP_LEVEL_DESC' , 'Use a compression level from 0-9 (0 = minimum, 9 = maximum).');

define('SESSION_WRITE_DIRECTORY_TITLE' , 'Session Directory');
define('SESSION_WRITE_DIRECTORY_DESC' , 'If sessions are file based, store them in this directory.');
define('SESSION_FORCE_COOKIE_USE_TITLE' , 'Force Cookie Use');
define('SESSION_FORCE_COOKIE_USE_DESC' , 'Force the use of sessions when cookies are only enabled.');
define('SESSION_CHECK_SSL_SESSION_ID_TITLE' , 'Check SSL Session ID');
define('SESSION_CHECK_SSL_SESSION_ID_DESC' , 'Validate the SSL_SESSION_ID on every secure HTTPS page request.');
define('SESSION_CHECK_USER_AGENT_TITLE' , 'Check User Agent');
define('SESSION_CHECK_USER_AGENT_DESC' , 'Validate the clients browser user agent on every page request.');
define('SESSION_CHECK_IP_ADDRESS_TITLE' , 'Check IP Address');
define('SESSION_CHECK_IP_ADDRESS_DESC' , 'Validate the clients IP address on every page request.');
define('SESSION_RECREATE_TITLE' , 'Recreate Session');
define('SESSION_RECREATE_DESC' , 'Recreate the session to generate a new session ID when the customer logs on or creates an account (PHP >=4.1 needed).');

define('DISPLAY_CONDITIONS_ON_CHECKOUT_TITLE' , 'Display conditions check on checkout');
define('DISPLAY_CONDITIONS_ON_CHECKOUT_DESC' , 'Display and Signing the Conditions in the Order Process');

define('META_MIN_KEYWORD_LENGTH_TITLE' , 'Min. meta-keyword lenght');
define('META_MIN_KEYWORD_LENGTH_DESC' , 'min. length of a single keyword (generated from products description)');
define('META_KEYWORDS_NUMBER_TITLE' , 'Number of meta-keywords');
define('META_KEYWORDS_NUMBER_DESC' , 'number of keywords');
define('META_AUTHOR_TITLE' , 'author');
define('META_AUTHOR_DESC' , '<meta name="author">');
define('META_PUBLISHER_TITLE' , 'publisher');
define('META_PUBLISHER_DESC' , '<meta name="publisher">');
define('META_COMPANY_TITLE' , 'company');
define('META_COMPANY_DESC' , '<meta name="conpany">');
define('META_TOPIC_TITLE' , 'page-topic');
define('META_TOPIC_DESC' , '<meta name="page-topic">');
define('META_REPLY_TO_TITLE' , 'reply-to');
define('META_REPLY_TO_DESC' , '<meta name="reply-to">');
define('META_REVISIT_AFTER_TITLE' , 'revisit-after');
define('META_REVISIT_AFTER_DESC' , '<meta name="revisit-after">');
define('META_ROBOTS_TITLE' , 'robots');
define('META_ROBOTS_DESC' , '<meta name="robots">');
define('META_DESCRIPTION_TITLE' , 'Description');
define('META_DESCRIPTION_DESC' , '<meta name="description">');
define('META_KEYWORDS_TITLE' , 'Keywords');
define('META_KEYWORDS_DESC' , '<meta name="keywords">');

define('MODULE_PAYMENT_INSTALLED_TITLE' , 'Installed Payment Modules');
define('MODULE_PAYMENT_INSTALLED_DESC' , 'List of payment module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: cc.php;cod.php;paypal.php)');
define('MODULE_ORDER_TOTAL_INSTALLED_TITLE' , 'Installed OrderTotal-Modules');
define('MODULE_ORDER_TOTAL_INSTALLED_DESC' , 'List of order_total module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: ot_subtotal.php;ot_tax.php;ot_shipping.php;ot_total.php)');
define('MODULE_SHIPPING_INSTALLED_TITLE' , 'Installed Shipping Modules');
define('MODULE_SHIPPING_INSTALLED_DESC' , 'List of shipping module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: ups.php;flat.php;item.php)');

define('CACHE_LIFETIME_TITLE','Cache Lifetime');
define('CACHE_LIFETIME_DESC','This is the number of seconds cached content will persist');
define('CACHE_CHECK_TITLE','Check if cache modified');
define('CACHE_CHECK_DESC','If true, then If-Modified-Since headers are respected with cached content, and appropriate HTTP headers are sent. This way repeated hits to a cached page do not send the entire page to the client every time.');

define('DB_CACHE_TITLE','DB Cache');
define('DB_CACHE_DESC','Cache SELECT query results in files to gain more speed for slow databases.');

define('DB_CACHE_EXPIRE_TITLE','DB Cache lifetime');
define('DB_CACHE_EXPIRE_DESC','Time in seconds to rebuld cached resulst.');

define('PRODUCT_REVIEWS_VIEW_TITLE','Reviews in Productdetails');
define('PRODUCT_REVIEWS_VIEW_DESC','Number of displayed reviews in the productdetails page');

define('DELETE_GUEST_ACCOUNT_TITLE','Deleting Guest Accounts');
define('DELETE_GUEST_ACCOUNT_DESC','Shold guest accounts be deleted after placing orders ? (Order data will be saved)');

define('USE_WYSIWYG_TITLE','activate WYSIWYG Editor');
define('USE_WYSIWYG_DESC','activate WYSIWYG Editor for CMS and products');

define('USE_WYSIWYG_CKEDITOR_TITLE','Activate new CKEditor?');
define('USE_WYSIWYG_CKEDITOR_DESC','The new CKEditor is much faster than its old predecessor. Unfortunately, he brings no file manager with more, because this part is now Commercial.<br /> Here you can switch between the two.');

define('PRICE_IS_BRUTTO_TITLE','Gross Admin');
define('PRICE_IS_BRUTTO_DESC','Usage of prices with tax in Admin');

define('PRICE_PRECISION_TITLE','Gross/Net precision');
define('PRICE_PRECISION_DESC','Gross/Net precision');
define('CHECK_CLIENT_AGENT_TITLE','Prevent Spider Sessions');
define('CHECK_CLIENT_AGENT_DESC','Prevent known spiders from starting a session.');
define('SHOW_IP_LOG_TITLE','IP-Log in Checkout?');
define('SHOW_IP_LOG_DESC','Show Text "Your IP will be saved", in checkout?');

define('ACTIVATE_GIFT_SYSTEM_TITLE','Activate Gift Voucher System');
define('ACTIVATE_GIFT_SYSTEM_DESC','Activate Gift Voucher System');

define('ACTIVATE_SHIPPING_STATUS_TITLE','Display Shippingstatus');
define('ACTIVATE_SHIPPING_STATUS_DESC','Show shippingstatus? (Different dispatch times can be specified for individual products. After activation appear a new point <b>Delivery Status</b> at product input)');

define('SECURITY_CODE_LENGTH_TITLE','Security Code Lenght');
define('SECURITY_CODE_LENGTH_DESC','Security code lenght (Gift voucher)');

define('IMAGE_QUALITY_TITLE','Image Quality');
define('IMAGE_QUALITY_DESC','Image quality (0= highest compression, 100=best quality)');

define('GROUP_CHECK_TITLE','Customerstatus Check');
define('GROUP_CHECK_DESC','Only allow specified customergroups access to individual categories,products and Contentelements (after activation, input fields in categories, products and in Contentmanager will appear');

define('ACTIVATE_REVERSE_CROSS_SELLING_TITLE','Reverse Cross-selling');
define('ACTIVATE_REVERSE_CROSS_SELLING_DESC','Activate reverse Cross-selling?');

define('ACTIVATE_NAVIGATOR_TITLE','activate productnavigator?');
define('ACTIVATE_NAVIGATOR_DESC','activate/deactivate productnavigator in product_info, (deaktivate for better performance with lots of articles in system)');

define('QUICKLINK_ACTIVATED_TITLE','activate multilink/copyfunction');
define('QUICKLINK_ACTIVATED_DESC','The multilink/copyfunction, changes the handling for the "copy product to" action, it allows to select multiple categories to copy/link a product with 1 click');

define('DOWNLOAD_UNALLOWED_PAYMENT_TITLE', 'Download Paymentmodules');
define('DOWNLOAD_UNALLOWED_PAYMENT_DESC', 'Not allowed Payment modules for downloads. List, seperated by comma, e.g. {banktransfer,cod,invoice,moneyorder}');
define('DOWNLOAD_MIN_ORDERS_STATUS_TITLE', 'Min. Orderstatus');
define('DOWNLOAD_MIN_ORDERS_STATUS_DESC', 'Min. orderstatus to allow download of files.');

// Vat Check
define('STORE_OWNER_VAT_ID_TITLE' , 'VAT ID of Shop Owner');
define('STORE_OWNER_VAT_ID_DESC' , 'The VAT ID of the Shop Owner');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_TITLE' , 'Customer-group - correct VAT ID (Foreign country)');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_DESC' , 'Customers-group for customers with correct VAT ID, Shop country != customers country');
define('ACCOUNT_COMPANY_VAT_CHECK_TITLE' , 'Validate VAT ID');
define('ACCOUNT_COMPANY_VAT_CHECK_DESC' , 'Validate VAT ID (check correct syntax)');
define('ACCOUNT_COMPANY_VAT_LIVE_CHECK_TITLE' , 'Validate VAT ID Live');
define('ACCOUNT_COMPANY_VAT_LIVE_CHECK_DESC' , 'Validate VAT ID live (if no syntax check available for country), live check will use validation gateway of germans "Bundesamt fР С—РЎвЂ”Р вЂ¦r Finanzen"');
define('ACCOUNT_COMPANY_VAT_GROUP_TITLE' , 'automatic pruning ?');
define('ACCOUNT_COMPANY_VAT_GROUP_DESC' , 'Set to true, the customer-group will be changed automatically if a correct VAT ID is used.');
define('ACCOUNT_VAT_BLOCK_ERROR_TITLE' , 'Allow wrong UST ID?');
define('ACCOUNT_VAT_BLOCK_ERROR_DESC' , 'Set to true, only validated VAT IDs are acceptet.');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_LOCAL_TITLE','Customer-group - correct VAT ID (Shop country)');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_LOCAL_DESC','Customers-group for customers with correct VAT ID, Shop country = customers country');
// Google Conversion
define('GOOGLE_CONVERSION_TITLE','Google Conversion-Tracking');
define('GOOGLE_CONVERSION_DESC','Track the Conversion Keywords at orders');
define('GOOGLE_CONVERSION_ID_TITLE','Conversion ID');
define('GOOGLE_CONVERSION_ID_DESC','Your Google Conversion ID');
define('GOOGLE_LANG_TITLE','Google Language');
define('GOOGLE_LANG_DESC','ISO Code of used Language');

// Afterbuy
define('AFTERBUY_ACTIVATED_TITLE','Activ');
define('AFTERBUY_ACTIVATED_DESC','Activate afterbuy module');
define('AFTERBUY_PARTNERID_TITLE','Partner ID');
define('AFTERBUY_PARTNERID_DESC','Your Afterbuy Partner ID');
define('AFTERBUY_PARTNERPASS_TITLE','Partner Password');
define('AFTERBUY_PARTNERPASS_DESC','Your Partner Password for Afterbuy XML Module');
define('AFTERBUY_USERID_TITLE','User ID');
define('AFTERBUY_USERID_DESC','Your Afterbuy User ID');
define('AFTERBUY_ORDERSTATUS_TITLE','Orderstatus');
define('AFTERBUY_ORDERSTATUS_DESC','Orderstatus for exported orders');
define('AFTERBUY_URL','Afterbuy info');

// Search-Options
define('SEARCH_IN_DESC_TITLE','Search in Products Descriptions');
define('SEARCH_IN_DESC_DESC','Activate to enable search in Products Descriptions');
define('SEARCH_IN_ATTR_TITLE','Search in Products Attributes');
define('SEARCH_IN_ATTR_DESC','Activate to enable search in Products Attributes');

// changes for 3.0.4 SP2
define('REVOCATION_ID_TITLE','Revocation ID');
define('REVOCATION_ID_DESC','Content ID of Revocation content');
define('DISPLAY_REVOCATION_ON_CHECKOUT_TITLE','Display right of revocation?');
define('DISPLAY_REVOCATION_ON_CHECKOUT_DESC','Display right of revocation on checkout_confirmation?');

// Mail Attachments
define('ATTACH_ORDER_1_TITLE','Order attachment 1:');
define('ATTACH_ORDER_1_DESC','Indicate to the file names for the appendix of the Mail here.<br />The file must be in the file <b>"attachments"</b>.<br />If no appendix is wished to leave simply the field empty.');
define('ATTACH_ORDER_2_TITLE','Order attachment 2:');
define('ATTACH_ORDER_2_DESC','Indicate to the file names for the appendix of the Mail here.<br />The file must be in the file <b>"attachments"</b><br />If no appendix is wished to leave simply the field empty.');
define('ATTACH_CREATE_1_TITLE','Create Account attachment 1:');
define('ATTACH_CREATE_1_DESC','Indicate to the file names for the appendix of the Mail here.<br />The file must be in the file <b>"attachments"</b><br />If no appendix is wished to leave simply the field empty.');
define('ATTACH_CREATE_2_TITLE','Create Account attachment 2:');
define('ATTACH_CREATE_2_DESC','Indicate to the file names for the appendix of the Mail here.<br />The file must be in the file <b>"attachments"</b><br />If no appendix is wished to leave simply the field empty.');

// Google Analytics
define('GOOGLE_ANAL_ON_TITLE','Google Analytics turn on/off');
define('GOOGLE_ANAL_ON_DESC','On: true<br />Off: false');
define('GOOGLE_ANAL_CODE_TITLE','Analytics code:');
define('GOOGLE_ANAL_CODE_DESC','Insert your Analytics Code here.<br />Example: UA-XXXXXXX-1');

// Privacy Notice
define('DISPLAY_DATENSCHUTZ_ON_CHECKOUT_TITLE' , 'Accept Privacy Notice');
define('DISPLAY_DATENSCHUTZ_ON_CHECKOUT_DESC' , 'Display Accept Privacy Notice on Checkout');

// PayPal Express
define('PAYPAL_MODE_TITLE','PayPal-Modus:');
define('PAYPAL_MODE_DESC','Live (normal) or Test (Sandbox)');
define('PAYPAL_API_USER_TITLE','PayPal-API-User (Live)');
define('PAYPAL_API_USER_DESC','register here the user name.');
define('PAYPAL_API_PWD_TITLE','PayPal-API-Password (Live)');
define('PAYPAL_API_PWD_DESC','register here the password.');
define('PAYPAL_API_SIGNATURE_TITLE','PayPal-API-Signatur (Live)');
define('PAYPAL_API_SIGNATURE_DESC','register here the API Signatur.');
define('PAYPAL_API_SANDBOX_USER_TITLE','PayPal-API-User (Sandbox)');
define('PAYPAL_API_SANDBOX_USER_DESC','register here the user name.');
define('PAYPAL_API_SANDBOX_PWD_TITLE','PayPal-API-Password (Sandbox)');
define('PAYPAL_API_SANDBOX_PWD_DESC','register here the password.');
define('PAYPAL_API_SANDBOX_SIGNATURE_TITLE','PayPal-API-Signatur (Sandbox)');
define('PAYPAL_API_SANDBOX_SIGNATURE_DESC','register here the API Signatur.');
define('PAYPAL_API_VERSION_TITLE','PayPal-API-Version');
define('PAYPAL_API_VERSION_DESC','register here the PayPal API Version ein - z.B.: 57.0');
define('PAYPAL_API_IMAGE_TITLE','PayPal Shop-Logo');
define('PAYPAL_API_IMAGE_DESC','register here the Logo file, which is to be indicated with PayPal.<br />Note: Becomes only if the shop with SSL works.<br />The picture may be high max. 750px broad and 90px.<br />The file is called out: '.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
define('PAYPAL_API_CO_BACK_TITLE','PayPal background color');
define('PAYPAL_API_CO_BACK_DESC','register here the background colour, which is to be indicated with PayPal. e.g. FEE8B9');
define('PAYPAL_API_CO_BORD_TITLE','PayPal border color');
define('PAYPAL_API_CO_BORD_DESC','register here the border color, which is to be indicated with PayPal. e.g. E4C558');
define('PAYPAL_ERROR_DEBUG_TITLE','PayPal error announcement');
define('PAYPAL_ERROR_DEBUG_DESC','Is the original PayPal error to be indicated? Normal=false');
define('PAYPAL_ORDER_STATUS_TMP_ID_TITLE','Order status "cancel"');
define('PAYPAL_ORDER_STATUS_TMP_ID_DESC','select the order status for broken off action (e.g. PayPal abort)');
define('PAYPAL_ORDER_STATUS_SUCCESS_ID_TITLE','order status OK');
define('PAYPAL_ORDER_STATUS_SUCCESS_ID_DESC','select the order status for a successful transaction (e.g. open PP paid)');
define('PAYPAL_ORDER_STATUS_PENDING_ID_TITLE','order status "pending"');
define('PAYPAL_ORDER_STATUS_PENDING_ID_DESC','select the order status for a transaction, those not yet of PayPal one worked on (e.g. open PP waiting)');
define('PAYPAL_ORDER_STATUS_REJECTED_ID_TITLE',' order status "rejected"');
define('PAYPAL_ORDER_STATUS_REJECTED_ID_DESC','select the order status for a rejected transaction (e.g. PayPal rejected)');
define('PAYPAL_COUNTRY_MODE_TITLE','PayPal-country mode');
define('PAYPAL_COUNTRY_MODE_DESC','select here the attitude for the country mode. Different functions of PayPal are possible only in UK (e.g. DirectPayment)');
define('PAYPAL_EXPRESS_ADDRESS_CHANGE_TITLE','PayPal-Express-Address data');
define('PAYPAL_EXPRESS_ADDRESS_CHANGE_DESC','Permits changing address data conveyed by PayPal.');
define('PAYPAL_EXPRESS_ADDRESS_OVERRIDE_TITLE','Ship-to-address overwrite');
define('PAYPAL_EXPRESS_ADDRESS_OVERRIDE_DESC','Permits changing address data conveyed by PayPal (existing account)');

// Login Safe CSEO 1.0.7
define ('LOGIN_NUM_TITLE','Number of permitted Login:');
define ('LOGIN_NUM_DESC','Adjust here, after how much wrong attempts the diagram code queries to appear is.<br /><b>default: 3</b>');
define ('LOGIN_TIME_TITLE','Time between log in:');
define ('LOGIN_TIME_DESC','If this time passed, is again a normal Login possible. (in seconds!)<br /><b>default: 300</b>');

//PHPIDS Zusatzsicherheit
define ('PHPIDS_SECURE_TITLE','PHPIDS Aktivieren:');
define ('PHPIDS_SECURE_DESC','PHPIDS sicherhert Ihren Shop vor moeglichen Angriffszenarien ab, wie SQL-Injection oder andere.<br /><strong>HINWEIS:<br /> PHPIDS wird erst ab PHP Version 5.1.6 unterstuetzt! (Eine Versionsabfrage ist eingebaut)<br />Der Oder IDS/tmp muss vom Server beschreibbar sein! Passen Sie dies wenn notwendig mit 777  Rechten an!</strong><br /><br />Weitere Informationen und ein Update der default_filter.xml (Im Ordner IDS) erhalten Sie unter: <a href="http://phpids.org/" target="_blank">http://phpids.org/</a>');
define ('PHPIDS_SECURE_MAIL_CONFIG_TITLE','PHPIDS E-Mail:');
define ('PHPIDS_SECURE_MAIL_CONFIG_DESC','Set this Option to True, to send E-Mail.');
define ('PHPIDS_SECURE_MAIL_TITLE','PHPIDS E-Mail Address:');
define ('PHPIDS_SECURE_MAIL_DESC','Please input your E-Mail Address, for send Messages.');
define ('PHPIDS_SECURE_DB_TITLE','PHPIDS Database Logging:');
define ('PHPIDS_SECURE_DB_DESC','Set this Option to True, to loggin into Database.');
define ('PHPIDS_SECURE_FILE_TITLE','PHPIDS File-Logging:');
define ('PHPIDS_SECURE_FILE_DESC','Set this Option to True, to log in file /includes/lib/IDS/tmp/phpids_log.txt.');

// Recover Cart Sales
define('RCS_BASE_DAYS_TITLE', 'Zeitraum');
define('RCS_BASE_DAYS_DESC', 'Anzahl der vergangenen Tage f&uuml;r nicht abgeschlossene Warenk&ouml;rbe.');
define('RCS_REPORT_DAYS_TITLE', 'Verkaufsbericht Zeitraum');
define('RCS_REPORT_DAYS_DESC', 'Anzahl der Tage, die ber&uuml;cksichtigt werden sollen. Je mehr, desto l&auml;nger dauert die Abfrage!');
define('RCS_EMAIL_TTL_TITLE', 'Lebensdauer Email');
define('RCS_EMAIL_TTL_DESC','Anzahl der Tage, die die E-Mail als gesendet markiert wird');
define('RCS_EMAIL_FRIENDLY_TITLE', 'Pers&ouml;nliche E-Mails');
define('RCS_EMAIL_FRIENDLY_DESC', 'Wenn <b>true</b> wird der Name des Kunden in der Anrede verwendet. Wenn <b>false</b> wird eine allgemeine Anrede verwendet.');
define('RCS_EMAIL_COPIES_TO_TITLE', 'E-Mail Kopien an');
define('RCS_EMAIL_COPIES_TO_DESC', 'Wenn Kopien der Emails an die Kunden versendet werden sollen, bitte Empf&auml;nger hier eintragen.');
define('RCS_SHOW_ATTRIBUTES_TITLE', 'Attribute anzeigen');
define('RCS_SHOW_ATTRIBUTES_DESC', 'Kontrolliert die Anzeige von Attributen.<br>Einige Shops nutzen Produktattribute.<br>Auf <b>true</b> setzen, wenn die Attribute angezeigt werden sollen, ansonsten auf <b>false</b>.');
define('RCS_CHECK_SESSIONS_TITLE', 'Ignoriere Kunden mit Sitzung');
define('RCS_CHECK_SESSIONS_DESC', 'Wenn Kunden mit aktiver Sitzung ignoriert werden sollen (z.B. weil sie noch einkaufen), w&auml;hlen sie <b>true</b>.<br>Wenn auf <b>false</b> gesetzt, werden die Sitzungsdaten ignoriert (schneller).');
define('RCS_CURCUST_COLOR_TITLE', 'Farbe aktiver Kunde');
define('RCS_CURCUST_COLOR_DESC', 'Farbe, die aktive Kunden markiert<br>Ein &quot;aktiver Kunde&quot; hat bereits Artikel im Shop bestellt.');
define('RCS_UNCONTACTED_COLOR_TITLE', 'Farbe "noch nicht kontaktiert"');
define('RCS_UNCONTACTED_COLOR_DESC', 'Hintergrundfarbe f&uuml;r noch nicht kontaktierte Kunden.<br>Ein nicht kontaktierter Kunde wurde noch <i>nicht</i> mit diesem Tool angeschrieben.');
define('RCS_CONTACTED_COLOR_TITLE', 'Farbe kontaktiert');
define('RCS_CONTACTED_COLOR_DESC', 'Hintergrundfarbe f&uuml;r kontaktierte Kunden.<br>Ein kontaktierter Kunde wurde bereits mit diesem Tool <i>informiert</i>.');
define('RCS_MATCHED_ORDER_COLOR_TITLE', 'Farbe alternative Bestellung gefunden');
define('RCS_MATCHED_ORDER_COLOR_DESC', 'Hintergrundfarbe f&uuml;r gefundene alternative Bestellungen.<br>Diese wird verwendet, wenn sich ein oder mehrere Artikel im offenen Warenkorb befinden und die E-Mail-Adresse oder die Kundennummer mit einer anderen Bestellung &uuml;bereinstimmt (siehe n&auml;chster Punkt).');
define('RCS_SKIP_MATCHED_CARTS_TITLE', '&Uuml;berspringe alternative Warenk&ouml;rbe');
define('RCS_SKIP_MATCHED_CARTS_DESC', 'Pr&uuml;fen, ob der Kunde den Warenkorb alternativ abgeschlossen hat (z.B. &uuml;ber Gastzugang statt per Anmeldung).');
define('RCS_AUTO_CHECK_TITLE', '"sichere" Warenk&ouml;rbe automatisch markieren');
define('RCS_AUTO_CHECK_DESC', 'Um Eintr&auml;ge, die relativ sicher sind (z.B. noch nicht existierende Kunden, noch nicht angemailt, etc.) zu markieren, setzen Sie <b>true</b>.<br>Wenn auf <b>false</b> gesetzt, werden keine Eintr&auml;ge vorausgew&auml;hlt.');
define('RCS_CARTS_MATCH_ALL_DATES_TITLE', 'Verwende Bestellungen jeden Datums');
define('RCS_CARTS_MATCH_ALL_DATES_DESC', 'Wenn <b>true</b> wird jede Bestellung des Kunden f&uuml;r die alternativen Abschl&uuml;sse herangezogen.<br>Wenn <b>false</b> werden nur Bestellungen im Zeitraum nach dem ablegen des letzten Artikels im Warenkorb gesucht.');
define('RCS_PENDING_SALE_STATUS_TITLE', 'Mindestbestellstatus');
define('RCS_PENDING_SALE_STATUS_DESC', 'H&ouml;chster Status, den eine Bestellung haben kann, um immer noch als offen zu gelten. Alle Werte dar&uuml;ber werden als Kauf gewertet');
define('RCS_REPORT_EVEN_STYLE_TITLE', 'Style ungerade Reihe');
define('RCS_REPORT_EVEN_STYLE_DESC', 'Style f&uuml;r die ungeraden Reihen im Bericht. Typische Optionen sind <i>dataTableRow</i> und <i>attributes-even</i>.');
define('RCS_REPORT_ODD_STYLE_TITLE', 'Style gerade Reihe');
define('RCS_REPORT_ODD_STYLE_DESC', 'Style f&uuml;r die geraden Reihen im Bericht. Typische Optionen sind NULL (bzw. kein Eintrag) und <i>attributes-odd</i>.');
define('RCS_SHOW_BRUTTO_PRICE_TITLE', 'Brutto-Anzeige');
define('RCS_SHOW_BRUTTO_PRICE_DESC', 'Sollen die Preise Brutto (true) oder Netto (false) angezeigt werden?');
define('DEFAULT_RCS_PAYMENT_TITLE', 'Standard-Zahlweise');
define('DEFAULT_RCS_PAYMENT_DESC', 'Modulname der Zahlweise f&uuml;r das abschlie&szlig;en der Bestellung (z.B. moneyorder).');
define('DEFAULT_RCS_SHIPPING_TITLE', 'Standard-Versandart');
define('DEFAULT_RCS_SHIPPING_DESC', 'Modulname der Versandart f&uuml;r das abschlie&szlig;en der Bestellung (z.B. dp_dp).');
define('RCS_DELETE_COMPLETED_ORDERS_TITLE', 'Bestellte Warenk&ouml;rbe l&ouml;schen');
define('RCS_DELETE_COMPLETED_ORDERS_DESC', 'Soll der Warenkorb im Zuge des Bestellabschlusses automatisch gel&ouml;scht werden?');
define('IBN_BILLNR_TITLE', '[ibillnr] Next Invoivenumer');       // pdfrechnung
define('IBN_BILLNR_DESC', 'Next number for invoice.'); 
define('IBN_BILLNR_FORMAT_TITLE', '[ibillnr] Invoicenumber Format');       // pdfrechnung
define('IBN_BILLNR_FORMAT_DESC', 'Format invoicenumber.: {n}=number, {d}=day, {m}=month, {y}=year, <br>example. "100{n}-{d}-{m}-{y}" => "10099-28-02-2007"'); 
define('MAX_RANDOM_PRODUCTS_TITLE','Random Produtcs on Fronsite');
define('MAX_RANDOM_PRODUCTS_DESC','Maximum Random Produtcs on Fronsite.');
// fuzzy-search

define('SEARCH_ACTIVATE_SUGGEST_TITLE','activate fuzzy search');
define('SEARCH_ACTIVATE_SUGGEST_DESC','activate/deactivate the module');
define('SEARCH_PRODUCT_KEYWORDS_TITLE','include extra-keywords field');
define('SEARCH_PRODUCT_KEYWORDS_DESC','includes the extra-keyword field into the calculation of proximities.');
define('SEARCH_PRODUCT_DESCRIPTION_TITLE','include products-description');
define('SEARCH_PRODUCT_DESCRIPTION_DESC','searches the products-description and products-short-description for similar terms.<br /><b style="color:red">Attention: this option generates heavy server load and is not recommended for larger shops !</b>');
define('SEARCH_PROXIMITY_TRIGGER_TITLE','activate from proximities in %');
define('SEARCH_PROXIMITY_TRIGGER_DESC','show suggestions from x% proximity and above. <br /><b>Standard = 70</b>');
define('SEARCH_WEIGHT_LEVENSHTEIN_TITLE','LEVENSHTEIN-factor in % (0-100)');
define('SEARCH_WEIGHT_LEVENSHTEIN_DESC','the desired ratio of the LEVENSHTEIN-function in the calculation of proximities?<br /><span style="color:red"><b><u>Attention:</u></b> the use of more than one function increases the server load! If you encounter problems with the parsetime better only use one funvtion. <br/>all three functions should add up to 100%, like 40%-40%-20%.</span> <br /><b>Standard = 0</b>');
define('SEARCH_WEIGHT_SIMILAR_TEXT_TITLE','SIMILAR-TEXT-factor in % (0-100)');
define('SEARCH_WEIGHT_SIMILAR_TEXT_DESC','the desired ratio of the SIMILAR-TEXT-function in the calculation of proximities? <br />see above for notes<br /><b>Standard = 100</b>');
define('SEARCH_WEIGHT_METAPHONE_TITLE','METAPHONE-factor in % (0-100)');
define('SEARCH_WEIGHT_METAPHONE_DESC','the desired ratio of the METAPHONE-function in the calculation of proximities? <br />see above for notes<br /><b>Standard = 0</b>');
define('SEARCH_SPLIT_MINIMUM_LENGTH_TITLE','ignored termlength');
define('SEARCH_SPLIT_MINIMUM_LENGTH_DESC','terms are beeing ignored when containing only x chars or less.<br /><span style="color:red"><b><u>Attention:</u></b> a low setting increases server load! Set this option to 4 or 5 to reduce critical server load.</span><br /> <b>Standard = 3</b>');
define('SEARCH_SPLIT_PRODUCT_NAMES_TITLE','split product names');
define('SEARCH_SPLIT_PRODUCT_NAMES_DESC','should product names be splitted at defined chars and calculatet seperatly? <br /><span style="color:red"><b><u>Attention:</u></b> deactivation saves server load but will find less terms</span><br />this setting will be ignored when extra-keywords or products descriptions are included!<br /><b>Standard = true</b>');
define('SEARCH_SPLIT_PRODUCT_CHARS_TITLE','seperators');
define('SEARCH_SPLIT_PRODUCT_CHARS_DESC','define the chars that trigger the split? <br />the chars have to be set in brackets like: [ ] or [-] or [ -] or [ /-]<br /><b>Standard = [ ]</b>');
define('SEARCH_MAX_KEXWORD_SUGGESTS_TITLE','ammount of search term suggestions');
define('SEARCH_MAX_KEXWORD_SUGGESTS_DESC','maximum number of search term suggestions <br /><b>Standard = 6</b>');
define('SEARCH_COUNT_PRODUCTS_TITLE','count products for each term suggestion');
define('SEARCH_COUNT_PRODUCTS_DESC','display the ammount of products behind each term suggestion?<br /><b>Standard = true</b>');
define('SEARCH_ENABLE_PROXIMITY_COLOR_TITLE','activate color values for displayed proximities');
define('SEARCH_ENABLE_PROXIMITY_COLOR_DESC','color the displayed proximities? <br/> <b>Standard = true</b>');
define('SEARCH_PROXIMITY_COLORS_TITLE','color values');
define('SEARCH_PROXIMITY_COLORS_DESC','put your own colors here, seperated with a semicolon. <br/><b>Standard = #9f6;#cf6;#ff6;#fc9;#f99</b>');
define('SEARCH_ENABLE_PRODUCTS_SUGGEST_TITLE','activate product suggestions');
define('SEARCH_ENABLE_PRODUCTS_SUGGEST_DESC','additionally suggest approximate products?<br/><b>Standard = true</b>');
define('SEARCH_MAX_PRODUCTS_SUGGEST_TITLE','maximum ammount of product suggestions');
define('SEARCH_MAX_PRODUCTS_SUGGEST_DESC','maximum ammount of product suggestions? <br/><b>Standard = 15</b>');
define('SEARCH_SHOW_PARSETIME_TITLE','display parsetime');
define('SEARCH_SHOW_PARSETIME_DESC','activate the parsetime for testing and configuring purpose. This is NOT the overall shop-parsetime. The ouput can be found directly below the last suggestion of thos module and shows the calculation time of this module only!');

define('MAX_ROW_LISTS_OPTIONS_TITLE' , 'Length of the article features lists');
define('MAX_ROW_LISTS_OPTIONS_DESC' , 'Select how many items and features option values in the article management features to be displayed');

#Category Listing Frontpage 2.0.10
define('CATEGORY_LISTING_START_TITLE','Top Categories Box center');
define('CATEGORY_LISTING_START_DESC','If the Mittelbox categories are shown on the homepage?');

define('CATEGORY_LISTING_START_HEAD_TITLE','Categories Box title?');
define('CATEGORY_LISTING_START_HEAD_DESC','If the headings in the Categories are displayed on the home page?');

define('CATEGORY_LISTING_START_PICTURE_TITLE','Categories Box Category Photos?');
define('CATEGORY_LISTING_START_PICTURE_DESC','If the images in the Categories are displayed on the home page?');

define('CATEGORY_LISTING_START_DESCR_TITLE','Categories Box Show Category Description?');
define('CATEGORY_LISTING_START_DESCR_DESC','If the descriptions in the Categories are displayed on the home page?');



# New in V2.1

define ('MAX_DISPLAY_TAGS_RESULTS_TITLE', 'How many tags are displayed');
define ('MAX_DISPLAY_TAGS_RESULTS_DESC', 'setting for the box tag cloud, how many should be displayed.');
define ('MIN_DISPLAY_TAGS_FONT_TITLE', 'minimum font size tags');
define ('MIN_DISPLAY_TAGS_FONT_DESC', 'How large should the font be minimized?');
define ('MAX_DISPLAY_TAGS_FONT_TITLE', 'Maximum font size tags');
define ('MAX_DISPLAY_TAGS_FONT_DESC', 'How large should the font can be? ');
define ('DISPLAY_NEW_PRODUCTS_SLIDE_TITLE', 'New Products View as slideshow');
define ('DISPLAY_NEW_PRODUCTS_SLIDE_DESC', 'Indicates whether new products will be displayed on the home page or in categories as a slideshow.');

define ('CURRENT_MOBILE_TEMPLATE_TITLE', 'mobile template');
define ('CURRENT_MOBILE_TEMPLATE_DESC', 'This template is designed for mobile devices. The template must be in the folder / templates /. <br /> <br /> are more templates can be found at <a href = "http://www.seo- template.de "> http://www.seo-template.de </ a> ');


define ('TWITTERBOX_STATUS_TITLE', 'Twitterbox status');
define ('TWITTERBOX_STATUS_DESC', 'active = true, false = inactive');
define ('TWITTER_ACCOUNT_TITLE', 'Twitterbox-account');
define ('TWITTER_ACCOUNT_DESC', 'Use your account name here of Twitter, example: commerce_SEO (http://www.twitter.com/commerce_SEO)');
define ('TWITTER_SCROLLBAR_TITLE', 'scrollbar');
define ('TWITTER_SCROLLBAR_DESC', 'If the customer can scroll in the box contributions (true = false active = inactive)');
define ('TWITTER_LOOP_TITLE', 'Twitter Loop');
define ('TWITTER_LOOP_DESC', 'active = true, false = inactive');
define ('TWITTER_LIVE_TITLE', 'Live Twitter');
define ('TWITTER_LIVE_DESC', 'active = true, false = inactive');
define ('TWITTER_HASHTAGS_TITLE', 'Twitter hashtags');
define ('TWITTER_HASHTAGS_DESC', 'active = true, false = inactive');
define ('TWITTER_TIMESTAMP_TITLE', 'Twitter timestamp');
define ('TWITTER_TIMESTAMP_DESC', 'active = true, false = inactive');
define ('TWITTER_AVATARS_TITLE', 'Twitter avatar');
define ('TWITTER_AVATARS_DESC', 'active = true, false = inactive');
define ('TWITTER_BEHAVIOR_TITLE', 'Twitter limit');
define ('TWITTER_BEHAVIOR_DESC', 'all =');
define ('TWITTER_SHELL_BACKGROUND_TITLE', 'background-color head');
define ('TWITTER_SHELL_BACKGROUND_DESC', 'Enter the hex color values');
define ('TWITTER_SHELL_COLOR_TITLE', 'font-color head');
define ('TWITTER_SHELL_COLOR_DESC', 'Enter the hex color values');
define ('TWITTER_TWEETS_BACKGROUND_TITLE', 'background-color Tweets');
define ('TWITTER_TWEETS_BACKGROUND_DESC', 'Enter the hex color values');
define ('TWITTER_TWEETS_COLOR_TITLE', 'font-color Tweets');
define ('TWITTER_TWEETS_COLOR_DESC', 'Enter the hex color values');
define ('TWITTER_TWEETS_LINKS_TITLE', 'Link color Tweets');
define ('TWITTER_TWEETS_LINKS_DESC', 'Enter the hex color values');
define ('TWITTER_BOX_WIDTH_TITLE', 'width of the box');
define ('TWITTER_BOX_WIDTH_DESC', 'Enter the width of the box in pixels (px without) at');
define ('TWITTER_BOX_HEIGHT_TITLE', 'height of the box');
define ('TWITTER_BOX_HEIGHT_DESC', 'Enter the height of the box in pixels (px without) at');
define ('TWITTER_BOX_INTERVAL_TITLE', 'interval of the update');
define ('TWITTER_BOX_INTERVAL_DESC', 'Enter the value in milliseconds');



define ('PRODUCT_DETAILS_MODELLNR_TITLE', 'Model');
define ('PRODUCT_DETAILS_MODELLNR_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_MANUFACTURERS_MODELLNR_TITLE', 'Manufacturer Model');
define ('PRODUCT_DETAILS_MANUFACTURERS_MODELLNR_DESC', 'active = true, false = inactive');

define ('PRODUCT_DETAILS_SHIPPINGTIME_TITLE', 'Delivery');
define ('PRODUCT_DETAILS_SHIPPINGTIME_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_STOCK_TITLE', 'availability');
define ('PRODUCT_DETAILS_STOCK_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_EAN_TITLE', 'EAN');
define ('PRODUCT_DETAILS_EAN_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_VPE_TITLE', 'PU');
define ('PRODUCT_DETAILS_VPE_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_WEIGHT_TITLE', 'Weight');
define ('PRODUCT_DETAILS_WEIGHT_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_PRINT_TITLE', 'Print');
define ('PRODUCT_DETAILS_PRINT_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_WISHLIST_TITLE', 'Wish list');
define ('PRODUCT_DETAILS_WISHLIST_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_ASKQUESTION_TITLE', 'Ask a Question');
define ('PRODUCT_DETAILS_ASKQUESTION_DESC', 'active = true, false = inactive');

define ('PRODUCT_DETAILS_TAB_DESCRIPTION_TITLE', 'Tab Description');
define ('PRODUCT_DETAILS_TAB_DESCRIPTION_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_TAB_ADD_TITLE', 'General Tab additional description');
define ('PRODUCT_DETAILS_TAB_ADD_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_TAB_PRODUCT_TITLE', 'Tab Product Related Additional Description');
define ('PRODUCT_DETAILS_TAB_PRODUCT_DESC', 'active = true, false = inactive (depending on whether a content group ID for each product was specified');
define ('PRODUCT_DETAILS_TAB_ADD_CONTENT_GROUP_ID_TITLE', 'Content Group-ID');
define ('PRODUCT_DETAILS_TAB_ADD_CONTENT_GROUP_ID_DESC', 'Content <b> Group ID </ b> for additional general description, see the Content Manager you this..');
define ('PRODUCT_DETAILS_TAB_ACCESSORIES_TITLE', 'Tab Accessories');
define ('PRODUCT_DETAILS_TAB_ACCESSORIES_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_TAB_PARAMETERS_TITLE', 'Tab Product Properties');
define ('PRODUCT_DETAILS_TAB_PARAMETERS_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_TAB_REVIEWS_TITLE', 'Tab Rating');
define ('PRODUCT_DETAILS_TAB_REVIEWS_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_TAB_CROSS_SELLING_TITLE', 'Tab cross-selling');
define ('PRODUCT_DETAILS_TAB_CROSS_SELLING_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_TAB_MEDIA_TITLE', 'Tab Product Media');
define ('PRODUCT_DETAILS_TAB_MEDIA_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_TAB_ALSO_PURCHASED_TITLE', 'Tab also bought');
define ('PRODUCT_DETAILS_TAB_ALSO_PURCHASED_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_TAB_REVERSE_CROSS_SELLING_TITLE', 'Tab reverse cross-selling');
define ('PRODUCT_DETAILS_TAB_REVERSE_CROSS_SELLING_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_TAGS_TITLE', 'tag');
define ('PRODUCT_DETAILS_TAGS_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_RELATED_CAT_TITLE', 'Random Article');
define ('PRODUCT_DETAILS_RELATED_CAT_DESC', 'active = true, false = inactive');
define ('PRODUCT_DETAILS_SOCIAL_TITLE', 'twitter / facebook');
define ('PRODUCT_DETAILS_SOCIAL_DESC', 'active = true, false = inactive');


define ('META_MAX_KEYWORD_LENGTH_TITLE', 'Maximum length of meta-keywords');
define ('META_MAX_KEYWORD_LENGTH_DESC', 'Maximum length of the automatically generated meta-keywords');
define ('META_MAX_DESCRIPTION_LENGTH_TITLE', 'Maximum length Meta-Description');
define ('META_MAX_DESCRIPTION_LENGTH_DESC', 'Maximum length of the automatically generated Meta-Description (item description), a good value is 160');

define ('MAX_DISPLAY_CART_SPECIALS_TITLE', 'Maximum number of items in cart');
define ('MAX_DISPLAY_CART_SPECIALS_DESC', 'Maximum number display more items below the basket');

define ('TRUSTED_SHOP_CREATE_ACCOUNT_DS_TITLE', 'Privacy in Create an Account');
define ('TRUSTED_SHOP_CREATE_ACCOUNT_DS_DESC', 'active = true, false = inactive (should the creation of customer account data protection regulations will be checked)');
define ('TRUSTED_SHOP_IP_LOG_TITLE', 'IP display in admin');
define ('TRUSTED_SHOP_IP_LOG_DESC', 'active = true, false = inactive (if false, you no longer all IPs from visitors / customers in the admin)');
define ('TRUSTED_SHOP_PASSWORD_EMAIL_TITLE', 'login and password in order-mail');
define ('TRUSTED_SHOP_PASSWORD_EMAIL_DESC', 'active = true, false = inactive (if false, are sent to the customer after creating his account no access)');

define ('CHECKOUT_CHECKBOX_AGB_TITLE', 'terms and conditions as checkbox');
define ('CHECKOUT_CHECKBOX_AGB_DESC', 'active = true, false = inactive (if false, the GTC will only appear to be not checked)');

define ('CHECKOUT_CHECKBOX_REVOCATION_TITLE', 'Withdrawal as a check box');
define ('CHECKOUT_CHECKBOX_REVOCATION_DESC', 'active = true, false = inactive (if false, the right of withdrawal is only displayed but need not be checked)');

define ('CHECKOUT_CHECKBOX_DSG_TITLE', 'Privacy as a check box');
define ('CHECKOUT_CHECKBOX_DSG_DESC', 'active = true, false = inactive (if false, the data is only displayed but need not be checked)');

define ('SLIMSTAT_TITLE', 'Slimstat');
define ('SLIMSTAT_DESC', 'active = true, false = inactive (Slimstat Statitics)');

define('CAT_NAV_AJAX_TITLE' , 'Kategorie als AJAX?'); 
define('CAT_NAV_AJAX_DESC' , 'Unterkategorien werden als AJAX-Menue dargestellt. Horizontales ausklappen der Unterkategorien. 	(true = aktiv, false = inaktiv)');

define('SHIPPING_SPERRGUT_1_TITLE','Bulk goods 1');
define('SHIPPING_SPERRGUT_1_DESC','Bulk goods price 1');

define('SHIPPING_SPERRGUT_2_TITLE','Bulk goods 2');
define('SHIPPING_SPERRGUT_2_DESC','Bulk goods price 2');

define('SHIPPING_SPERRGUT_3_TITLE','Bulk goods 3');
define('SHIPPING_SPERRGUT_3_DESC','Bulk goods price 3');

define('STOCK_LEVEL_SHIPPINGTIME_TITLE' , 'Lieferzeit abhängig vom Lagerbestand dynamisch ändern');
define('STOCK_LEVEL_SHIPPINGTIME_DESC' , 'Lieferstatus eines Artikels wird z.B. auf "3" gesetzt, wenn die Artikelanzahl den Wert "0" erreicht hat. (Lieferstatus "3" ist die längste Bestellzeit. zB.: 2 Wochen.) Diese Option wird nur Aktiv, wenn das Feld "Warenmenge abziehen" - auf "True" gesetzt ist und durch eine Online - Bestellung auf "0" gesetzt wird.');
define('STOCK_LEVEL_SHIPPINGTIME_ID_TITLE' , 'Lieferstatus für dynamische Lieferzeitanpassung');
define('STOCK_LEVEL_SHIPPINGTIME_ID_DESC' , 'Tragen Sie hier die Lieferstatus ID ein, auf welchen Lieferstatus das Produkt bei erreichen der Menge "0" gesetzt werden soll.');


define('GENERAL_SCRIPT_ADDON_TITLE','Addon-Javascript');
define('GENERAL_SCRIPT_ADDON_DESC','Here you can include individual javascripts.');

define('GOOGLE_ANAL_CODE_BASE_TITLE','Google-Analytics Code');
define('GOOGLE_ANAL_CODE_BASE_DESC','Enter the full Google Analytics code.');

define('MODULE_CUSTOMERS_PDF_INVOICE_STATUS_TITLE','automatic bill');
define('MODULE_CUSTOMERS_PDF_INVOICE_STATUS_DESC','<b>true</b> = Invoice is generated automatically.');

define('MODULE_CUSTOMERS_PDF_INVOICE_MAIL_STATUS_TITLE','automatic invoice via e-mail');
define('MODULE_CUSTOMERS_PDF_INVOICE_MAIL_STATUS_DESC','<b>true</b> = Invoice is sent automatically by e-mail.');
