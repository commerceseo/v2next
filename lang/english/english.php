<?php
/*-----------------------------------------------------------------
* 	$Id: english.php 1155 2014-07-21 12:28:37Z akausch $
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

/*
 *
 *  DATE / TIME
 *
 */

define('TITLE', STORE_NAME);
define('HEADER_TITLE_TOP', 'Main page');
define('HEADER_TITLE_CATALOG', 'Catalogue');
define('TEXT_CLOSE_WINDOW_NO_JS', 'Your Browser support no Javascript. Please close this window.');

define('HTML_PARAMS','dir="ltr" xml:lang="en" lang="en"');

@setlocale(LC_TIME, 'en_EN@euro', 'en_US', 'en-US', 'en', 'en_US.UTF-8', 'English','en_US.UTF-8');
define('DATE_FORMAT_SHORT', '%d.%m.%Y');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%A, %d. %B %Y'); // this is used for strftime()
define('DATE_FORMAT', 'd.m.Y');  // this is used for strftime()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');
define('DOB_FORMAT_STRING', 'dd.mm.jjjj');

define('JAN','Jan');
define('FEB','Feb');
define('MRZ','Mar');
define('APR','Apr');
define('MAI','May');
define('JUN','June');
define('JUL','July');
define('AUG','Aug');
define('SEP','Sept');
define('OKT','Oct');
define('NOV','Nov');
define('DEZ','Dec');

define('PAGE_BREAK','--Page--');
define('PREVNEXT_TITLE_LAST_PAGE','jump to last page');
define('PREVNEXT_TITLE_FIRST_PAGE','jump to first page');

define('BROWSER_TEST','<p style="border: 1px solid #F00;background-color: #FFE8E8;color: #F00;padding: 10px;margin-bottom: 20px;">You use Internet Explorer 6 or even older. This browser is too old and has serious security flaws! Therefore, we don\'t support this browser anymore.<br /> Please install immediately to a newer, safer browser such as <a rel="nofollow" target="_blank" href="http://www.mozilla-europe.org/de/firefox/">Mozilla Firefox</a> or <a rel="nofollow" target="_blank" href="http://www.microsoft.com/windows/Internet-explorer/download-ie.aspx">Microsoft Internet Explorer</a> (actual versions).</p>');

function xtc_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 0, 2) . substr($date, 3, 2) . substr($date, 6, 4);
  } else {
    return substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);
  }
}

// if USE_DEFAULT_LANGUAGE_CURRENCY is true, use the following currency, instead of the applications default currency (used when changing language)
define('LANGUAGE_CURRENCY', 'EUR');

define('MALE', ' Mr.');
define('FEMALE', ' Ms./Mrs.');

/**
	Product Listings Header
**/
define('ALSO_PURCHASED','Customers who bought this product bought the following products also');
define('CROSS_SELLING','We recommend the following products');
define('REVERSE_CROSS_SELLING','This product is compatible too (g.e.)');
define('SPECIALS','Specials');
define('TAGCLOUD','The following tags matched');
define('RANDOM_PRODUCTS','Special products');
define('NEW_PRODUCTS','New products in this category');
define('NEW_PRODUCTS_DEFAULT','Our new products');
define('NEW_PRODUCTS_OVERVIEW','Overview of all our new products');
define('UPCOMING_PRODUCT','Upcoming products');
define('HISTORY_PRODUCT','Last viewed');

/*
 *
 *  BOXES
 *
 */

// text for gift voucher redeeming
define('IMAGE_REDEEM_GIFT','Redeem Gift Coupon!');

define('BOX_TITLE_STATISTICS','Statistics:');
define('BOX_ENTRY_CUSTOMERS','Customers');
define('BOX_ENTRY_PRODUCTS','Products');
define('BOX_ENTRY_REVIEWS','Reviews');
define('TEXT_VALIDATING','Not validated');
define('BOX_EMAIL_VALUE','E-Mail-Adress');

// manufacturer box text
define('BOX_MANUFACTURER_INFO_HOMEPAGE', '%s Homepage');
define('BOX_MANUFACTURER_INFO_OTHER_PRODUCTS', 'More Products');

define('BOX_HEADING_ADD_PRODUCT_ID','Add To Cart');

define('BOX_HEADING_SEARCH','Search again');
define('BOX_LOGINBOX_STATUS','Customer group:');
define('BOX_LOGINBOX_DISCOUNT','Product discount');
define('BOX_LOGINBOX_DISCOUNT_TEXT','Discount');
define('BOX_LOGINBOX_DISCOUNT_OT','');

// Sprachlinks
// Sollten Sie diese Links ädern, ändern Sie diese auch in der .htaccess
define('SPECIALS_LINK','Specials.html');
define('NEW_PRODUCTS_LINK','new-products.html');
define('SHOPPING_CART_LINK','Cart.html');
define('ACCOUNT_LINK','Account.html');
define('CHECKOUT_LINK','Checkout.html');
define('ADVANCED_SEARCH_LINK','Advanced-search.html');
define('LOGIN_LINK','Login.html');
define('LOGOUT_LINK','Logout.html');
define('PASSWORD_DOUBLE_OPT_LINK', 'lost-password.html');

// reviews box text in includes/boxes/reviews.php
define('BOX_REVIEWS_WRITE_REVIEW', 'Review this product!');
define('BOX_REVIEWS_TEXT_OF_5_STARS', '%s of 5 stars!');
define('TEXT_OF_5_STARS', '%s von 5 stars!');

// pull down default text
define('PULL_DOWN_DEFAULT', 'Please choose');

// javascript messages
define('JS_ERROR', 'Missing necessary information!\nPlease fill in correctly.\n\n');

define('JS_REVIEW_TEXT', '* The text must consist at least of ' . REVIEW_TEXT_MIN_LENGTH . ' alphabetic characters..\n');
define('JS_REVIEW_RATING', '* Enter your review.\n');
define('JS_ERROR_NO_PAYMENT_MODULE_SELECTED', '* Please choose a method of payment for your order.\n');
define('JS_ERROR_SUBMITTED', 'This page has already been confirmed. Please click okay and wait until the process has finished.');
define('ERROR_NO_PAYMENT_MODULE_SELECTED', 'Please choose a method of payment for your order.');

/*
 *
 * ACCOUNT FORMS
 *
 */

define('ENTRY_COMPANY_ERROR', '');
define('ENTRY_COMPANY_TEXT', '');
define('ENTRY_GENDER_ERROR', 'Please select your gender.');
define('ENTRY_GENDER_TEXT', '*');
define('ENTRY_FIRST_NAME_ERROR', 'Your first name must consist of at least  ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' characters.');
define('ENTRY_FIRST_NAME_TEXT', '*');
define('ENTRY_LAST_NAME_ERROR', 'Your e-mail address must consist of at least ' . ENTRY_LAST_NAME_MIN_LENGTH . ' characters.');
define('ENTRY_LAST_NAME_TEXT', '*');
define('ENTRY_DATE_OF_BIRTH_ERROR', 'Your date of birth has to be entered in the following form DD.MM.YYYY (e.g. 21.05.1970) ');
define('ENTRY_DATE_OF_BIRTH_TEXT', '* (e.g. 21.05.1970)');
define('ENTRY_EMAIL_ADDRESS_ERROR', 'Your e-mail address must consist of at least  ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' characters.');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', 'The e-mail address you’re entered is incorrect - please check it');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', 'The e-mail address your entered already exists in our database - please check it');
define('ENTRY_EMAIL_ADDRESS_TEXT', '*');
define('ENTRY_STREET_ADDRESS_ERROR', 'Street/Nr must consist of at least ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' characters.');
define('ENTRY_STREET_ADDRESS_TEXT', '*');
define('ENTRY_SUBURB_TEXT', '');
define('ENTRY_POST_CODE_ERROR', 'Your zip code must consist of at least ' . ENTRY_POSTCODE_MIN_LENGTH . ' characters.');
define('ENTRY_POST_CODE_TEXT', '*');
define('ENTRY_CITY_ERROR', 'City must consist of at least ' . ENTRY_CITY_MIN_LENGTH . ' characters.');
define('ENTRY_CITY_TEXT', '*');
define('ENTRY_STATE_ERROR', 'Your state must consist of at least ' . ENTRY_STATE_MIN_LENGTH . ' characters.');
define('ENTRY_STATE_ERROR_SELECT', 'Please choose your state out of the list.');
define('ENTRY_STATE_TEXT', '*');
define('ENTRY_COUNTRY_ERROR', 'Please choose your country.');
define('ENTRY_COUNTRY_TEXT', '*');
define('ENTRY_TELEPHONE_NUMBER_ERROR', 'Your phone number must consist of at least ' . ENTRY_TELEPHONE_MIN_LENGTH . ' characters.');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '*');
define('ENTRY_FAX_NUMBER_TEXT', '');
define('ENTRY_NEWSLETTER_TEXT', '');
define('ENTRY_PASSWORD_ERROR', 'Your password must consist of at least ' . ENTRY_PASSWORD_MIN_LENGTH . ' characters.');
define('ENTRY_PASSWORD_ERROR_NOT_MATCHING', 'Your passwords do not match.');
define('ENTRY_PASSWORD_TEXT', '*');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '*');
define('ENTRY_PASSWORD_CURRENT_TEXT', '*');
define('ENTRY_PASSWORD_CURRENT_ERROR','Your password must consist of at least ' . ENTRY_PASSWORD_MIN_LENGTH . ' characters.');
define('ENTRY_PASSWORD_NEW_TEXT', '*');
define('ENTRY_PASSWORD_NEW_ERROR', 'Your new password must consist of at least ' . ENTRY_PASSWORD_MIN_LENGTH . ' characters.');
define('ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING', 'Your passwords do not match.');
define('ERROR_DATENSG_NOT_ACCEPTED', 'If you do not confirm that the information of data protection regulation was readed, we cannot create your account!');

/*
 *
 *  RESTULTPAGES
 *
 */

define('TEXT_RESULT_PAGE', 'Sites:');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Show <b>%d</b> to <b>%d</b> (of in total <b>%d</b> products)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'Show <b>%d</b> to <b>%d</b> (of in total <b>%d</b> orders)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Show <b>%d</b> to <b>%d</b> (of in total <b>%d</b> reviews)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW', 'Show <b>%d</b> to <b>%d</b> (of in total <b>%d</b> new products)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'Show <b>%d</b> to <b>%d</b> (of in total <b>%d</b> special offers)');

/*
 *
 * SITE NAVIGATION
 *
 */

define('PREVNEXT_TITLE_PREVIOUS_PAGE', 'previous page');
define('PREVNEXT_TITLE_NEXT_PAGE', 'next page');
define('PREVNEXT_TITLE_PAGE_NO', 'page %d');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', 'Previous %d pages');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', 'Next %d pages');

/*
 *
 * PRODUCT NAVIGATION
 *
 */

define('PREVNEXT_BUTTON_PREV', '[&lt;&lt;&nbsp;previous]');
define('PREVNEXT_BUTTON_NEXT', '[next&nbsp;&gt;&gt;]');

/*
 *
 * IMAGE BUTTONS
 *
 */

define('IMAGE_BUTTON_ADD_ADDRESS', 'New address');
define('IMAGE_BUTTON_BACK', 'Back');
define('IMAGE_BUTTON_CHANGE_ADDRESS', 'Change address');
define('IMAGE_BUTTON_CHECKOUT', 'Checkout');
define('IMAGE_BUTTON_CONFIRM_ORDER', 'Confirm order');
define('IMAGE_BUTTON_CONTINUE', 'Next');
define('IMAGE_BUTTON_DELETE', 'Delete');
define('IMAGE_BUTTON_LOGIN', 'Login');
define('IMAGE_BUTTON_IN_CART', 'Into the cart');
define('IMAGE_BUTTON_SEARCH', 'Go');
define('IMAGE_BUTTON_SAVE', 'Save');
define('IMAGE_BUTTON_EDIT', 'Edit');
define('IMAGE_BUTTON_UPDATE', 'Update');
define('IMAGE_BUTTON_UPDATE_CART', 'Update shopping cart');
define('IMAGE_BUTTON_WRITE_REVIEW', 'Write Evaluation');
define('IMAGE_BUTTON_ADMIN', 'Admin');
define('IMAGE_BUTTON_MOBILE_ADMIN', 'Mobile-Admin');
define('IMAGE_BUTTON_PRODUCT_EDIT', 'Edit product');
define('IMAGE_BUTTON_LOGIN', 'Login');
define('IMAGE_BUTTON_PRINT_PDF', 'Create PDF and show me');
define('IMAGE_BUTTON_PRINT_CONTENT', 'Print text');
define('IMAGE_BUTTON_SEND', 'Send');
define('IMAGE_BUTTON_SAVE', 'Save');
define('IMAGE_BUTTON_EDIT', 'Edit');

define('SMALL_IMAGE_BUTTON_DELETE', 'Delete');
define('SMALL_IMAGE_BUTTON_EDIT', 'Edit');
define('SMALL_IMAGE_BUTTON_VIEW', 'View');

define('ICON_ARROW_RIGHT', 'Show more');
define('ICON_CART', 'Into the cart');
define('ICON_SUCCESS', 'Success');
define('ICON_WARNING', 'Warning');
define('ICON_ERROR','Error');

/*
 *
 *  GREETINGS
 *
 */

define('TEXT_GREETING_PERSONAL', 'Nice to see you again <span class="greetUser">%s!</span> Would you like to view our <a style="text-decoration:underline;" href="%s">new products</a> ?');
define('TEXT_GREETING_PERSONAL_RELOGON', '<small>If you are not %s , please  <a style="text-decoration:underline;" href="%s">login</a>  with your account.</small>');
define('TEXT_GREETING_GUEST', 'Welcome  <span class="greetUser">visitor!</span> Would you like to <a style="text-decoration:underline;" href="%s">login</a>? Or would you like to create a new <a style="text-decoration:underline;" href="%s">account</a> ?');

define('TEXT_SORT_PRODUCTS', 'Sorting of the items is ');
define('TEXT_DESCENDINGLY', 'descending');
define('TEXT_ASCENDINGLY', 'ascending');
define('TEXT_BY', ' after ');

define('TEXT_REVIEW_BY', 'from %s');
define('TEXT_REVIEW_WORD_COUNT', '%s words');
define('TEXT_REVIEW_RATING', 'Review: %s [%s]');
define('TEXT_REVIEW_DATE_ADDED', 'Date added: %s');
define('TEXT_REVIEW_SUCCESS_MSG', 'Review succes.');
define('TEXT_NO_REVIEWS', 'There are no reviews yet.');
define('TEXT_NO_NEW_PRODUCTS', 'There are no new products at the moment.');
define('TEXT_UNKNOWN_TAX_RATE', 'Unknown tax rate');

/*
 *
 * WARNINGS
 *
 */

define('WARNING_INSTALL_DIRECTORY_EXISTS', 'Warning: The installation directory is still available on: ' . dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/xtc_installer. Please delete this directory for security reasons!');
define('WARNING_CONFIG_FILE_WRITEABLE', 'Warning: Commerce:SEO is able to write to the configuration directory: ' . dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/includes/configure.php. That represents a possible safety hazard - please correct the user access rights for this directory!');
define('WARNING_SESSION_DIRECTORY_NON_EXISTENT', 'Warning: Directory for sessions doesn&acute;t exist: ' . xtc_session_save_path() . '. Sessions will not work until this directory has been created!');
define('WARNING_SESSION_DIRECTORY_NOT_WRITEABLE', 'Warning: Commerce:SEO is not able to write into the session directory: ' . xtc_session_save_path() . '. Sessions will not work until the user access rights for this directory have been changed!');
define('WARNING_SESSION_AUTO_START', 'Warning: session.auto_start is activated (enabled) - Please deactivate (disable) this PHP feature in php.ini and restart your web server!');
define('WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT', 'Warning: Directory for article download does not exist: ' . DIR_FS_DOWNLOAD . '. This feature will not work until this directory has been created!');

define('SUCCESS_ACCOUNT_UPDATED', 'Your account has been updated successfully.');
define('SUCCESS_PASSWORD_UPDATED', 'Your password has been changed successfully!');
define('ERROR_CURRENT_PASSWORD_NOT_MATCHING', 'The entered password does not match with the stored password. Please try again.');
define('TEXT_MAXIMUM_ENTRIES', '<font color="#ff0000"><b>Reference:</b></font> You are able to choose out of %s entries in your address book!');
define('SUCCESS_ADDRESS_BOOK_ENTRY_DELETED', 'The selected entry has been deleted successfully.');
define('SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED', 'Your address book has been updated successfully!');
define('WARNING_PRIMARY_ADDRESS_DELETION', 'The standard postal address cannot be deleted. Please create another address and define it as standard postal address first. Than this entry can be deleted.');
define('ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY', 'This address book entry is not available.');
define('ERROR_ADDRESS_BOOK_FULL', 'Your addressbook is full and you have to delete one address first, before you can save another.');

//  conditions check

define('ERROR_CONDITIONS_NOT_ACCEPTED', 'If you do not accept our General Business Conditions, we are not able to accept your order!');

define('SUB_TITLE_OT_DISCOUNT','Discount:');

define('TAX_ADD_TAX','incl. ');
define('TAX_NO_TAX','plus ');

define('NOT_ALLOWED_TO_SEE_PRICES','You do not have the permission to see the prices ');
define('NOT_ALLOWED_TO_SEE_PRICES_TEXT','You do not have the permission to see the prices, please create an account.');

define('TEXT_DOWNLOAD','Download');
define('TEXT_VIEW','View');
define('TEXT_PRINT','Print');

define('TEXT_BUY', '1 x \'');
define('TEXT_NOW', '\' order');
define('TEXT_BUTTON_BUY_NOW','Order');
define('TEXT_NOW_TO_WISHLIST', ' to wish list');
define('TEXT_TO_WISHLIST', 'to wish list');
define('TEXT_GUEST','Visitor');

/*
 *
 * ADVANCED SEARCH
 *
 */

define('TEXT_ALL_CATEGORIES', 'All categories');
define('TEXT_ALL_MANUFACTURERS', 'All manufacturers');
define('JS_AT_LEAST_ONE_INPUT', '* One of the following fields must be filled:\n    Keywords\n    Date added from\n    Date added to\n    Price over\n    Price up to\n');
define('AT_LEAST_ONE_INPUT', 'One of the following fields must be filled:<br />keywords consisting at least 3 characters<br />Price over<br />Price up to<br />');
define('SEARCH_RESULTS_WORDS','We found not match on your search &quot;<em>%s</em>&quot; found <em>%s</em> hits');
define('JS_INVALID_FROM_DATE', '* Invalid from date\n');
define('JS_INVALID_TO_DATE', '* Invalid up to Date\n');
define('JS_TO_DATE_LESS_THAN_FROM_DATE', '* The from date must be larger or same size as up to now\n');
define('JS_PRICE_FROM_MUST_BE_NUM', '* Price over, must be a number\n');
define('JS_PRICE_TO_MUST_BE_NUM', '* Price up to, must be a number\n');
define('JS_PRICE_TO_LESS_THAN_PRICE_FROM', '* Price up to must be larger or same size as Price over.\n');
define('JS_INVALID_KEYWORDS', '* Invalid search key\n');
define('TEXT_LOGIN_ERROR', '<font color="#ff0000"><b>ERROR:</b></font> The entered \'eMail-address\' and/or the \'password\' do not match.');
define('TEXT_NO_EMAIL_ADDRESS_FOUND', '<font color="#ff0000"><b>WARNING:</b></font> The entered e-mail address is not registered. Please try again.');
define('TEXT_PASSWORD_SENT', 'A new password was sent by e-mail.');
define('TEXT_PRODUCT_NOT_FOUND', 'Product not found!');
define('TEXT_MORE_INFORMATION', 'For further information, please visit the <a style="text-decoration:underline;" href="%s" onclick="window.open(this.href); return false;">homepage</a> of this product.');
define('TEXT_DATE_ADDED', 'This Product was added to our catalogue on %s.');
define('TEXT_DATE_AVAILABLE', '<font color="#ff0000">This Product is expected to be on stock again on %s </font>');
define('SUB_TITLE_SUB_TOTAL', 'Sub-total:');

define('OUT_OF_STOCK_CANT_CHECKOUT', 'The products marked with ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' , are not on stock in the quantity you requested.<br />Please reduce your purchase order quantity for the marked products. Thank you');
define('OUT_OF_STOCK_CAN_CHECKOUT', 'The products marked with ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' , are not on stock in the quantity you requested.<br />The entered quantity will be supplied in a short period of time by us. On request, we can do part delivery.');

define('MINIMUM_ORDER_VALUE_NOT_REACHED_1', 'You need to reach the minimum order value of: ');
define('MINIMUM_ORDER_VALUE_NOT_REACHED_2', ' <br />Please increase your order for more: ');
define('MAXIMUM_ORDER_VALUE_REACHED_1', 'You ordered more than the allowed amount of: ');
define('MAXIMUM_ORDER_VALUE_REACHED_2', '<br /> Please decrease your order for less than: ');

define('ERROR_INVALID_PRODUCT', 'The product chosen was not found!');

/*
 *
 * NAVBAR Titel
 *
 */

define('NAVBAR_TITLE_BLOG','Blog');
define('NAVBAR_TITLE_ACCOUNT', 'Your account');
define('NAVBAR_TITLE_TAGLIST','Tags');
define('NAVBAR_TITLE_SEARCH','Search');
define('NAVBAR_TITLE_1_ACCOUNT_EDIT', 'Your account');
define('NAVBAR_TITLE_2_ACCOUNT_EDIT', 'Changing your personal data');
define('NAVBAR_TITLE_1_ACCOUNT_HISTORY', 'Your account');
define('NAVBAR_TITLE_2_ACCOUNT_HISTORY', 'Your completed orders');
define('NAVBAR_TITLE_1_ACCOUNT_HISTORY_INFO', 'Your account');
define('NAVBAR_TITLE_2_ACCOUNT_HISTORY_INFO', 'Completed orders');
define('NAVBAR_TITLE_3_ACCOUNT_HISTORY_INFO', 'Order number %s');
define('NAVBAR_TITLE_1_ACCOUNT_PASSWORD', 'Your account');
define('NAVBAR_TITLE_2_ACCOUNT_PASSWORD', 'Change password');
define('NAVBAR_TITLE_1_ADDRESS_BOOK', 'Your account');
define('NAVBAR_TITLE_2_ADDRESS_BOOK', 'Address book');
define('NAVBAR_TITLE_1_ADDRESS_BOOK_PROCESS', 'Your account');
define('NAVBAR_TITLE_2_ADDRESS_BOOK_PROCESS', 'Address book');
define('NAVBAR_TITLE_ADD_ENTRY_ADDRESS_BOOK_PROCESS', 'New entry');
define('NAVBAR_TITLE_MODIFY_ENTRY_ADDRESS_BOOK_PROCESS', 'Change entry');
define('NAVBAR_TITLE_DELETE_ENTRY_ADDRESS_BOOK_PROCESS', 'Delete Entry');
define('NAVBAR_TITLE_ADVANCED_SEARCH', 'Advanced Search');
define('NAVBAR_TITLE1_ADVANCED_SEARCH', 'Advanced Search');
define('NAVBAR_TITLE2_ADVANCED_SEARCH', 'Search results');
define('NAVBAR_TITLE_1_CHECKOUT', 'Checkout');
define('NAVBAR_TITLE_1_CHECKOUT_CONFIRMATION', 'Checkout');
define('NAVBAR_TITLE_2_CHECKOUT_CONFIRMATION', 'Confirmation');
define('NAVBAR_TITLE_1_CHECKOUT_PAYMENT', 'Checkout');
define('NAVBAR_TITLE_2_CHECKOUT_PAYMENT', 'Method of payment');
define('NAVBAR_TITLE_1_PAYMENT_ADDRESS', 'Checkout');
define('NAVBAR_TITLE_2_PAYMENT_ADDRESS', 'Change billing address');
define('NAVBAR_TITLE_1_CHECKOUT_SHIPPING', 'Checkout');
define('NAVBAR_TITLE_2_CHECKOUT_SHIPPING', 'Shipping information');
define('NAVBAR_TITLE_1_CHECKOUT_SHIPPING_ADDRESS', 'Checkout');
define('NAVBAR_TITLE_2_CHECKOUT_SHIPPING_ADDRESS', 'Change shipping address');
define('NAVBAR_TITLE_1_CHECKOUT_SUCCESS', 'Checkout');
define('NAVBAR_TITLE_2_CHECKOUT_SUCCESS', 'Success');
define('NAVBAR_TITLE_CREATE_ACCOUNT', 'Create account');
if ($navigation->snapshot['page'] == FILENAME_CHECKOUT_SHIPPING) {
  define('NAVBAR_TITLE_LOGIN', 'Order');
} else {
  define('NAVBAR_TITLE_LOGIN', 'Login');
}
define('NAVBAR_TITLE_LOGOFF','Good bye');
define('NAVBAR_TITLE_PRODUCTS_NEW', 'New products');
define('NAVBAR_TITLE_SHOPPING_CART', 'Shopping cart');
define('NAVBAR_TITLE_WISH_LIST', 'Wish list');
define('NAVBAR_TITLE_SPECIALS', 'Special offers');
define('NAVBAR_TITLE_COOKIE_USAGE', 'Cookie Usage');
define('NAVBAR_TITLE_PRODUCT_REVIEWS', 'Reviews');
define('NAVBAR_TITLE_REVIEWS_WRITE', 'Opinions');
define('NAVBAR_TITLE_REVIEWS','Reviews');
define('NAVBAR_TITLE_SSL_CHECK', 'Note on safety');
define('NAVBAR_TITLE_CREATE_GUEST_ACCOUNT','Create account');
define('NAVBAR_TITLE_PASSWORD_DOUBLE_OPT','Password forgotten?');
define('NAVBAR_TITLE_NEWSLETTER','Newsletter');
define('NAVBAR_GV_REDEEM', 'Redeem Voucher');
define('NAVBAR_GV_SEND', 'Send Voucher');

/*
 *
 *  MISC
 *
 */

define('TITLE_HELP','Help for advanced search');
define('TEXT_HELP','The Search function makes the search possible for Product descriptions, manufacturers and article number.<br /><br />You can use logical operators as "AND" and "OR" br><br />For example you could write: <span class="underline">Microsoft AND Mouse</span>.<br /><br />Furthermore you can use brackets around the search interleave, i.e.:<br /><br /><span class="underline">Microsoft AND (Mouse OR Keyboard OR "Visual Basic")</span>.<br /><br />You can combine several words with quotation marks into a search key.');
define('TEXT_CLOSE','[x] Close window');

define('TEXT_NEWSLETTER','You want to stay up to date?<br />No problem, receive our Newsletter and we can inform you always up to date.');
define('TEXT_EMAIL_INPUT','Your e-Mail address has been registered by our system.<br />Therefore you will receive an E-Mail with your personally confirmation-code-link.  Please click after the receipt of the Mail on the Hyperlink inside. Otherwise no Newsletter will be send to you!');

define('TEXT_WRONG_CODE','<font color="FF0000">Please fill out the e-Mail field and the Security-Code again. <br />Be aware of Typos!</font>');
define('TEXT_EMAIL_EXIST_NO_NEWSLETTER','<font color="FF0000">This e-Mail address is registered but not yet activated!</font>');
define('TEXT_EMAIL_EXIST_NEWSLETTER','<font color="FF0000">This e-Mail address is registered is also activated for the newsletter!</font>');
define('TEXT_EMAIL_NOT_EXIST','<font color="FF0000">This e-Mail address is not registered for Newsletters!</font>');
define('TEXT_EMAIL_DEL','Your e-Mail address was deleted successfully in our newsletter-database.');
define('TEXT_EMAIL_DEL_ERROR','<font color="FF0000">An Error occurred, your e-Mailaddress has not been deleted!</font>');
define('TEXT_EMAIL_ACTIVE','<font color="FF0000">Your e-Mail address was successfully integrated in our Newsletter Service!</font>');
define('TEXT_EMAIL_ACTIVE_ERROR','<font color="FF0000">An error occurred, your e-Mail address has not been activated for Newsletter!</font>');
define('TEXT_EMAIL_SUBJECT','Your Newsletter Account');

define('TEXT_CUSTOMER_GUEST','Guest');

define('TEXT_LINK_MAIL_SENDED','Your inquiry for a new password must be confirmed by you personally.<br />Therefore you will receive an E-Mail with your personally confirmation-code-link.  Please click after the receipt of the Mail on the Hyperlink inside. A further Mail with your new Login password will receive you afterwards.  Otherwise no new password will be set or sent to you!');
define('TEXT_PASSWORD_MAIL_SENDED','You will receive an e-Mail with your new password in between minutes.<br />Please change your password after your first login like you want.');
define('TEXT_CODE_ERROR','Please fill out the e-Mail field and the Security-Code again. <br />Be aware of Typos!');
define('TEXT_EMAIL_ERROR','Please fill out the e-Mail field and the Security-Code again. <br />Be aware of Typos!');
define('TEXT_NO_ACCOUNT','Unfortunately we must communicate to you that your inquiry for a new Login password was either invalid or run out of time.<br />Please try it again.');
define('HEADING_PASSWORD_FORGOTTEN','Password renewal?');
define('TEXT_PASSWORD_FORGOTTEN','Change your password in three easy steps.');
define('TEXT_EMAIL_PASSWORD_FORGOTTEN','Confirmation Mail for password renewal');
define('TEXT_EMAIL_PASSWORD_NEW_PASSWORD','Your new password');
define('ERROR_MAIL','Please check the data entered in the form');

define('CATEGORIE_NOT_FOUND','Category was not found');

define('GV_FAQ', 'Gift Coupon FAQ');
define('ERROR_NO_REDEEM_CODE', 'You did not enter a redeem code.');
define('ERROR_NO_INVALID_REDEEM_GV', 'Invalid Gift Coupon Code');
define('TABLE_HEADING_CREDIT', 'Credits Available');
define('EMAIL_GV_TEXT_SUBJECT', 'A gift from %s');
define('MAIN_MESSAGE', 'You have decided to send a gift coupon worth %s to %s who\'s eMail address is %s<br /><br />The text accompanying the eMail will read<br /><br />Dear %s<br /><br />You have been sent a Gift Coupon worth %s by %s');
define('REDEEMED_AMOUNT','Your gift coupon was successfully added to your account. Gift coupon amount:');
define('REDEEMED_COUPON','Your coupon was successfully booked and will be redeemed automatically with your next order.');

define('ERROR_INVALID_USES_USER_COUPON','Customers can redeem this coupon only ');
define('ERROR_INVALID_USES_COUPON','Customers can redeem this coupon only ');
define('TIMES',' times.');
define('ERROR_INVALID_STARTDATE_COUPON','Your coupon is not aviable yet.');
define('ERROR_INVALID_FINISDATE_COUPON','Your coupon is out of date.');
define('PERSONAL_MESSAGE', '%s says:');

//Popup Window
define('TEXT_CLOSE_WINDOW', 'Close Window.');
define('TEXT_CLOSE_WINDOW_NO_JS', 'End of Text.');

/*
 *
 * CUOPON POPUP
 *
 */

define('TEXT_CLOSE_WINDOW', 'Close Window [x]');
define('TEXT_COUPON_HELP_HEADER', 'Congratulations, you have redeemed a Discount Coupon.');
define('TEXT_COUPON_HELP_NAME', '<br /><br />Coupon Name : %s');
define('TEXT_COUPON_HELP_FIXED', '<br /><br />The coupon is worth %s discount against your order');
define('TEXT_COUPON_HELP_MINORDER', '<br /><br />You need to spend %s to use this coupon');
define('TEXT_COUPON_HELP_FREESHIP', '<br /><br />This coupon gives you free shipping on your order');
define('TEXT_COUPON_HELP_DESC', '<br /><br />Coupon Description : %s');
define('TEXT_COUPON_HELP_DATE', '<br /><br />The coupon is valid between %s and %s');
define('TEXT_COUPON_HELP_RESTRICT', '<br /><br />Product / Category Restrictions');
define('TEXT_COUPON_HELP_CATEGORIES', 'Category');
define('TEXT_COUPON_HELP_PRODUCTS', 'Product');

// VAT ID
define('ENTRY_VAT_TEXT','* for Germany and EU-Countries only');
define('ENTRY_VAT_ERROR', 'The chosen VatID is not valid or not proofable at this moment! Please fill in a valid ID or leave the field empty.');
define('MSRP','MSRP');
define('YOUR_PRICE','your price ');
define('ONLY',' only ');
define('SINGLE_PRICE','Single price ');
define('FROM','from ');
define('YOU_SAVE','you save ');
define('INSTEAD','instead ');
define('TXT_PER',' per ');
define('TAX_INFO_INCL','incl. %s Tax');
define('TAX_INFO_EXCL','excl. %s Tax');
define('TAX_INFO_ADD','plus. %s Tax');
define('SHIPPING_EXCL','excl.');
define('SHIPPING_COSTS','Shipping costs');

// changes 3.0.4 SP2
define('SHIPPING_TIME','Shipping time: ');
define('MORE_INFO','[More]');

define('ERROR_DATENSCHUTZ_NOT_ACCEPTED', '* You cannot proced, without Accept Privacy Notice!\n\n');
define('ERROR_WIDERRUFSRECHT_NOT_ACCEPTED', '* You cannot proced, without Accept Revocation Notice!\n\n');

define('NAVBAR_TITLE_1_ACCOUNT_DELETE', 'Your Account');
define('NAVBAR_TITLE_2_ACCOUNT_DELETE', 'Delete Account');
define('PRINT_CONTENT', 'Print');

define('BUTTON_PRINT_AGB', 'Print');
define('BUTTON_PRINT_DS', 'Print');
define('BUTTON_PRINT_WD', 'Print');

// MULTISORT 
define('MULTISORT_STANDARD', 'Sorting ...'); 
define('MULTISORT_NEW_DESC', 'Newest first'); 
define('MULTISORT_NEW_ASC', 'Oldest first'); 
define('MULTISORT_PRICE_ASC', 'Price - ascending'); 
define('MULTISORT_PRICE_DESC', 'Price - descending');
define('MULTISORT_MANUFACTURER_ASC', 'Manufacturer A-Z'); 
define('MULTISORT_MANUFACTURER_DESC', 'Manufacturer Z-A');
define('MULTISORT_ABC_AZ', 'Alphabet A-Z'); 
define('MULTISORT_ABC_ZA', 'Alphabet Z-A'); 
define('MULTISORT_SPECIALS_DESC', 'Specials');
define('RESULT_STANDARD','Article per page');
define('PER_SITE','per page');

define('ERROR_CDATENSG','Please acceppt the Privacy Notice');

define('WISHLIST','Wishlist');
define('HEAD_INFO_TXT','Your wish list contains %s product in worth of ');
define('HEAD_INFO_TXT_MORE','Your wish list contains %s products in worth of ');
define('WISHLIST_EMPTY','Your wish list contains no items.');

define('PDFBILL_DOWNLOAD_INVOICE', 'PDF-Invoice Download' );

define('CHECKOUT_REMOVE_CONFIRM','Are you sure that you want to remove this product?');
define('CHECKOUT_EMPTY_CART','All products have been removed. You will be referred back to your shopping cart.');
define('CHECKOUT_NOMORE_ADDRESSES','Unfortunately you can\'t add new addresses to your address book. You reached the maximum.');
define('CHECKOUT_TEXT_VIRTUAL','This information is not needed, because your order only contains virtual products.');
define('CHECKOUT_OUT_OF_STOCK','Your order couldn\'t be updated beacause this products is not available in your desired quantity.');
define('CHECKOUT_NO_PAYMENT_MODULE_SELECTED','Please choose a payment option');
define('CHECKOUT_NO_SHIPPING_MODULE_SELECTED','Please choose a shipping option');
define('CHECKOUT_PAYMENT_OK','Data has been saved.');
define('CHECKOUT_SHIPPING_OK','Data has been saved.');
define('CHECKOUT_SHIPPING_CHOOSE','Please choose a shipping module');
define('CHECKOUT_PAYMENT_CHOOSE','Please choose a payment module');
define('CHECKOUT_PAYMENT_NOT_COMPATIBLE','Payment option not compatible. Please choose a new one.');
define('CHECKOUT_SHIPPING_NOT_COMPATIBLE','Shipping option not compatible. Please choose a new one.');
define('CHECKOUT_ERROR_CONDITIONS','- Please accept our General business conditions');
define('CHECKOUT_ERROR_REVOCATION','- Please accept our notice of revocation');
define('CHECKOUT_PLEASE_WAIT','Please wait...');
define('CHECKOUT_PAYMENT_DUE', '(+ Fee)');
define('BOX_EMAIL_VALUE', 'E-Mail Address');
define('IMAGE_BUTTON_SEND', 'Send');
define('TEXT_BUTTON_BUY_NOW', 'Buy Now');
define('IMAGE_BUTTON_ADD_A_QUICKIE', 'Add');
define('IMAGE_BUTTON_PRINT', 'Print');

#New in v2.1
define('ADVANCED_SEARCH_HEADER','Results for ');
define('LISTING_GALLERY','<img src="images/icons/view_gallery.gif" alt="Gallery" title="Gallery View">');
define('LISTING_LIST','<img src="images/icons/view_list.gif" alt="List" title="List View">');
define('LISTING_GALLERY_ACTIVE','<img src="images/icons/view_gallery_active.gif" alt="Gallery" title="Gallery View">');
define('LISTING_LIST_ACTIVE','<img src="images/icons/view_list_active.gif" alt="List" title="List View">');
define('TEXT_GALERY', 'Galery');
define('TEXT_LIST', 'List');
define('TEXT_TAG_NOT_FOUND','Your tag search returned no results.');
define('TEXT_TAG_TREFFER1','this there ');
define('TEXT_TAG_TREFFER2',' results<br /><br />');
define('TEXT_TAG_HEAD','Tag: ');
define('TEXT_ADVANCE_RESULT_HEAD', 'Hits for ');
define('IMAGE_BUTTON_BEARBEITEN', 'Change');
define('TEXT_TAG_CLOUD1', 'products are with');
define('TEXT_TAG_CLOUD2', 'tagged');
define('TEXT_WRITE_REVIEW','Be the first to comment<br />');

define('NAVBAR_TITLE_PRODUCT_FILTER','product Filters');
define('PRODUCT_FILTER_AND', 'All criteria must be met');
define('PRODUCT_FILTER_OR', 'a criterion must be true');
#Gutschein
define('SUB_TITLE_OT_COUPON', 'discount Coupons:');
define('REDEEMED_COUPON','Your coupon was successfully logged and will be considered at the current order.');
define('ERROR_INVALID_USES_USER_COUPON','You can use this coupon only ');
define('ERROR_INVALID_USES_COUPON','This coupon can only be a total of');
define('TIMES',' Customers will be redeemed. This limit has been reached!');
define('TIMES2',' Redeem times. This limit you have already exhausted!');
define('ERROR_INVALID_STARTDATE_COUPON','The term of your coupon has not yet begun.');
define('ERROR_INVALID_FINISDATE_COUPON','Your coupon has expired.');
define('ERROR_INVALID_PRODUCT_COUPON','Your coupon is limited to certain products.');
define('ERROR_INVALID_CATEGORIE_COUPON','Your coupon is limited to certain categories.');
define('ERROR_MINIMUM_ORDER_COUPON_1','The minimum order for this coupon in the amount of ');
define('ERROR_MINIMUM_ORDER_COUPON_2',' has not been reached.');
define('ERROR_GV_LOGIN','Please log in before you can redeem a coupon. Only then can the value be credited to your account.');
define('ERROR_ENTRY_AMOUNT_CHECK','Error: Your credit is not enough to give away for this amount.');
define('ERROR_ENTRY_NO_NAME','Error: No given name of the recipient.');
define('ERROR_ENTRY_NO_AMOUNT','Error: No valid value specified.');
define('ERROR_ENTRY_EMAIL_ADDRESS_CHECK','Error: No valid e-mail address specified.');
define('COUPON_TYPE_S','Free shipping discount');
define('COUPON_TYPE_F','Fixed Amount Discount');
define('COUPON_TYPE_P','percentage discount');
define('CART_SPECIAL','This might also interest you');
define('IMAGE_BUTTON_BACK_SHOP','back to shop');

#WCAG
define('WCAG_REGISTER','register');
define('WCAG_UNREGISTER','sign out');
define('WCAG_MANUFACTURERS','Sort by manufacturer:');
define('WCAG_QTY','quantity');
define('WCAG_SEARCH','search');
define('WCAG_MANUFACTURERS_LABEL','Vendor selection');
define('CAPTCHA_DESCRIPTION','Captcha:');
define('IMAGE_BUTTON_TREEPODIA','Product Video');
define('TEXT_CURRENT_PRICE','sum price:');

define('NAVBAR_TITLE_ADVANCED_SEARCH', 'Advanced Search');

define('ERROR_FROM_NAME', 'Error: Please input your Name.');
define('ERROR_FROM_ADDRESS', 'Error: Please input your email.');
define('ERROR_MESSAGE', 'Error: Please input your Message.');
define('PRODUCT_ASK_A_QUESTION_SUCCESS', 'Thank you! Your message has been sent successfully, we will contact you shortly.');

define('PRODUCT_AKS_A_QUESTION_SUBJECT_1', 'question about this Product');
define('PRODUCT_AKS_A_QUESTION_SUBJECT_2', 'Offer Product');
define('PRODUCT_AKS_A_QUESTION_SUBJECT_3', 'technical question about this Product');


// PayPal Express
define('NAVBAR_TITLE_PAYPAL_CHECKOUT','PayPal-Checkout');
define('PAYPAL_ERROR','PayPal abort');
define('PAYPAL_NOT_AVIABLE','PayPal Express is not available.<br />Please select another method of payment<br />or try again later.<br />');
define('ERROR_ADDRESS_NOT_ACCEPTED', 'We are not able to accept your order if you do not accept your address!');
define('PAYPAL_FEHLER','PayPal announced an error to the completion..<br />Your order is stored, is however not implemented.<br />Please enter a new order.<br />Thanks for your understanding.<br />');
define('PAYPAL_WARTEN','PayPal announced an error to the completion.<br />You must pay again to PayPal around the order.<br />Down you see the stored order.<br /> Thanks for it pressing to understanding request you again the button PayPal express.<br />');
define('PAYPAL_NEUBUTTON','Press please again around the order to pay.<br />Every other key leads to the abort of the order.');
define('PAYPAL_GS','Coupon');
define('PAYPAL_TAX','Tax');
define('PAYPAL_EXP_WARN','Note! Possibly resulting forwarding expenses are only computed in the shop finally.');
define('PAYPAL_EXP_VORL','Provisional forwarding expenses');
define('PAYPAL_EXP_VERS','0.00');
// 09.01.11
define('PAYPAL_ADRESSE','The country in your PayPal dispatch address is not registered in our shop.<br />Please contact us.<br />Thanks for you understanding.<br />From PayPal received country: ');
// 17.09.11
define('PAYPAL_AMMOUNT_NULL','The order sum which can be expected (without dispatch) is directly 0.<br />Thus PayPal express is not available.<br />Please select another payment means.<br />Thanks for your understanding.<br />');


define('IMAGE_BUTTON_DETAILS','Details');
define('IMAGE_BUTTON_TO_CART','to cart');
define('TEXT_WISH_SINGLE','buy');
define('IMAGE_BUTTON_ALL_WISH','buy all');


define('AUTOSUGGEST_CLOSE', 'Close window');
define('MORE_RESULTS', '...more results');
define('AUTOSUGGEST_NO_PRODUCTS', 'No products found');
define('AUTOSUGGEST_INTRO', 'We suggest this products for your keyword:');

define('CHECKOUT_SPRICE', 'Price');
define('CHECKOUT_SUM', 'Sum');
define('CHECKOUT_DESC', 'Description');
define('TEXT_IN', 'check in');
define('TEXT_OUT', 'check out');


define('AMZ_SINGLE_PRICE', 'unit price');
define('AMZ_TOTAL_PRICE', 'total price');
define('NO_POSITIONS','not available.');
define('CANCEL','Cancel');
define('AMZ_TOTAL', 'Total');
define('ACCEPT','Please, accept our our right of withdrawal and our general terms and conditions!');
define('NO_SHIPPING','Shipping is not possible!');
define('NO_SHIPPING_TO_ADDRESS', 'Shipping is not possible to this address.');
define('FREE_SHIPPING_AT', 'Free shipping at ');
define('SUCCESS','Your Amazon order number for this order is:');
define('AMZ_WAITING', 'Please wait until you are redirected');
define('AMZ_WAITING_IMG', 'https://images-na.ssl-images-amazon.com/images/G/01/cba/images/global/Loading._V192259297_.gif');
define('AMZ_ZOLL', 'If you are ordering from outside of the EU it is possible that you have to pay additional customs duties, taxes or fees to your public authorities. We recommend our customers to clear particulars before ordering.');

define('AMZ_ADMIN_HINT', '* has beed reduced, because of used coupons for the products.');
define('AMZ_ADMIN_BTN', 'Do refund');
define('AMZ_VERSANDANTEIL', 'Shipping');
define('AMZ_PRODUKT', 'Product');
define('AMZ_SHOW_HIDE', 'show/hide');
define('AMZ_REFUND_SUCCESS', 'Refund successfully submitted!');
define('AMZ_REFUND_ERROR', 'Refund not submitted - please check the submitted amounts!');
define('AMZ_DATE', 'Date');
define('AMZ_BETRAG', 'Amount');

define('TEXT_MINORDER','Bitte beachten Sie die Mindestbestellmenge für folgende Produkte');
define('TEXT_MINORDER_TITLE','Mindestbestellmenge');

define('BOX_EMAIL_PASSWD','Password');

if (file_exists(DIR_WS_INCLUDES . 'addons/lang_english_addon.php')) {
	include (DIR_WS_INCLUDES . 'addons/lang_english_addon.php');
}


define('ADMIN_TITLE_STATISTICS', 'Online:');
define('ADMIN_TITLE_STATISTICS', 'Online:');
define('ADMIN_EDIT_CONTENT', 'Content bearbeiten');
define('ADMIN_EDIT_BLOG_CAT', 'Blogkategorie bearbeiten');
define('ADMIN_EDIT_BLOG_ITEM', 'Blogbeitrag bearbeiten');
define('ADMIN_EDIT_CAT', 'Kategorie bearbeiten');
define('ADMIN_EDIT_ATTR', 'Attribute bearbeiten');
define('ADMIN_EDIT_CROSS', 'Cross Selling');
define('ADMIN_STATISTICS','Statistik');
define('ADMIN_CUSTOMERS','Kunden:');
define('ADMIN_PRODUCTS','Artikel:');
define('ADMIN_REVIEWS','Bewertungen:');

//PDF Rechnung
define('FILENAME_BILL', 'Bill');
define('FILENAME_PACKINSLIP', 'Delivery');
define('TEXT_PDF_SEITE', 'Site');
define('TEXT_PDF_SEITE_VON', 'from');
define('TEXT_PDF_KUNDENNUMMER', 'Customer-Nr.');
define('TEXT_PDF_RECHNUNGSNUMMER', 'Bill-Nr.');
define('TEXT_PDF_LIEFERNUMMER', 'Delivery');
define('TEXT_PDF_BESTELLNUMMER', 'Order-nr.');
define('TEXT_PDF_DATUM', 'Date');
define('TEXT_PDF_ZAHLUNGSWEISE', 'Payment');
define('TEXT_PDF_RECHNUNG', 'Bill');
define('TEXT_PDF_LIEFERSCHEIN', 'Delivery');
define('TEXT_PDF_MENGE', 'Qty.');
define('TEXT_PDF_ARTIKEL', 'Item');
define('TEXT_PDF_ARTIKELNR', 'Item-Nr.');
define('TEXT_PDF_EINZELPREIS', 'Single price');
define('TEXT_PDF_PREIS', 'Total price');
define('TEXT_PDF_KOMMENTAR', 'Comments');
define('TEXT_PDF_LIFERADRESSE', 'Delivery: ');
define('TEXT_PDF_INVOICE', 'Note on the invoice: ');

define('SUBCAT_PRODUCTS', 'more categorys: ');


// FELDER DIE FEHLTEN Ernst Steininger 15.03.2015
define('SHIPPING_AUSLAND', '(international delivery is divergent)');
define('SHIPPING_AUSLAND_CART1', 'For deliveries outside');
define('SHIPPING_AUSLAND_CART2', 'Click here for information about delivery and shipment costs.');

define('NAVBAR_TITLE_WITHDRAWAL', 'Withdrawal form');
define('BUTTON_WITHDRAWAL', 'Online-Form');
define('BUTTON_WITHDRAWAL_PDF', 'PDF-Download');
define('BUTTON_WITHDRAWAL_PDF_DOWNLOAD', ' Withdrawal form to download');

define('CHECKOUT_PAYMENT_ERROR','While ordering an error has occurred. Please log out once and redo the order process again. If this error persists, please contact our customer service.');

define('GAST', 'Guest');
define('TITLE_BEWERTUNGEN', 'Manage store reviews');
define('SUB_TITLE_BEWERTUNGEN', 'Manage the received store reviews');

define('NAVBAR_TITLE_SHOPBEWERTUNGEN', 'Store review');
define('NAVBAR_TITLE_SHOPBEWERTUNGEN_WRITE', 'Write a store review');

define('SHOPBEWERTUNG_FORMULAR', 'Store review Formu');
define('SHOPBEWERTUNG_ERHALTEN', 'New store review received');
define('MAIL_BEWERTUNG_VON', '<strong>Rated from:</strong>');
define('MAIL_BEWERTUNG_ABSENDER', '<strong>Sender:</strong>');
define('MAIL_BEWERTUNG_KOMMENTAR', '<strong>Comment:</strong>');

define('MAIL_BEWERTUNG_SHOP', '<strong>Review Store:</strong>');
define('MAIL_BEWERTUNG_WARE', '<strong>Review Goods:</strong>');
define('MAIL_BEWERTUNG_VERSAND', '<strong>Review delivery:</strong>');
define('MAIL_BEWERTUNG_SERVICE', '<strong>Review service:</strong>');
define('MAIL_BEWERTUNG_SEITE', '<strong>Review page:</strong>');

define('STERN_BEWERTUNGEN', 'Star');
define('STERN_STUCK', 'Star');
define('STERN_KEIN', 'No rating');

define('WERTUNG1', 'Very bad');
define('WERTUNG2', 'Bad');
define('WERTUNG3', 'Neutral');
define('WERTUNG4', 'Good');
define('WERTUNG5', 'Very good');

define('MAILERROR', 'Please enter a valid email address.');
define('RATINGERROR', 'Please enter a shop review.');
define('CAPTCHAERROR', 'Please enter the correct security code.');
define('NAMEERROR', 'Please enter your first and last name.');
define('ORDERIDERROR', 'There is no order with this order number.');
define('COMMENTERROR', 'Please enter a comment.');
define('ORDERIDERROREMAIL', 'Order number and e-mail do not agree with the order data.');

define('SECURITY_CODE_ERROR','Incorrect security code');

define('MIN_REVIEW_TEXT_ERROR', '* The comment must consist of at least ' . REVIEW_TEXT_MIN_LENGTH . ' Letters. Please complete your entry.');
define('MIN_REVIEW_RATING_ERROR', 'Please give a rating in the form of stars.');

define('WK_NETTO', 'Sum without VAT. ');

define('RANDOM_SPECIALS','Offers');
define('PRODUCT_NO_BUY','The displayed item is currently not available for purchase.');
define('TEXT_DATE_UPDATED', 'Updated on: %s');
define('WEIGHT', 'Shipping Weight');

define('text_pw_secure_0', 'very weak');
define('text_pw_secure_1', 'weak');
define('text_pw_secure_2', 'better');
define('text_pw_secure_3', 'OK');
define('text_pw_secure_4', 'good');
define('text_pw_secure_5', 'very good');
define('TEXT_ADVANCED_SEARCH', 'Advanced Search');

define('ENTRY_DATE_OF_BIRTH_OLD', 'You do not have the minimum age to buy.');
define('ATTRIBUTE_ERROR', 'Please select a model');
define('MAIN_BESTSELLER', 'Bestseller');
define('BLOG_PRODUCTS', 'matching products');
define('IN_STOCK', 'on stock');
define('IN_W_STOCK', 'few on stock');
define('IN_N_STOCK', 'not on stock');
define('TEXT_MAXORDER','Please note the maximum order quantity for the following products');
define('TEXT_MAXORDER_TITLE','maximum order quantity');
define('RMA_NUMBER', 'Nr.: ');
define('RMA_DATE', 'Date: ');
define('NAVBAR_TITLE_RMA','RMA-Contract');
define('RMA_OVERVIEW_TEXT', 'To the RMA Overview');
define('RMA_TEXT_SHOW', 'Show');
define('RMA_TEXT_FROM', ' from ');
define('RMA_PRODUCTS_PLEASE_SELECT','please select');
define('ENTRY_RMA_PRODUCTS','Please select the product you like to complain!');
define('ENTRY_RMA_ERROR_MESSAGE','Please enter the error description!');
define('ENTRY_RMA_ERROR_MESSAGE_LENGTH','The error description is too short!');
define('ENTRY_RMA_PRODUCTS_EAN','Please enter the serial number of the product!');
define('ENTRY_RMA_REASON','Please select the reason of your complain!');


define('BUTTON_PAYPAL_TEXT','Note on Express-Payment with PayPal');