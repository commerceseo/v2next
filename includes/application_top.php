<?php
/*-----------------------------------------------------------------
* 	$Id: application_top.php 1012 2014-05-08 13:02:25Z akausch $
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

// start the timer for the page parse time log
define('PAGE_PARSE_START_TIME', microtime());

// set the level of error reporting
if(defined('E_DEPRECATED')) {
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
} else {
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
}


// Set the local configuration parameters - mainly for developers - if exists else the mainconfigure
if(file_exists('includes/local/configure.php') && filesize('includes/local/configure.php') !== false) {
	include('includes/local/configure.php');
} elseif(file_exists('includes/configure.php') && filesize('includes/configure.php') !== false) {
	include('includes/configure.php');
} else {
	header('Location: installer/');
	exit;
}
if(COMMERCE_SEO_V22_INSTALLED != 'true') {
	header('Location: installer/');
	exit;
}

date_default_timezone_set('Europe/Berlin');

// set the level of error reporting
if (function_exists('ini_set')) {
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set("log_errors" , "1");
	ini_set("error_log" , DIR_FS_CATALOG . "logfiles/Errors.log.txt");
	ini_set("display_errors" , "0"); 
}


require_once(DIR_FS_CATALOG.'inc/cseo_get_env_info.inc.php');
require_once(DIR_FS_CATALOG.'inc/cseo_check_data_type.inc.php');
require_once(DIR_FS_CATALOG.'includes/system/cseohook_init.inc.php');
# custom class autoloader
spl_autoload_register(array(new cseoautoloader('frontend'), 'load'));

// set the type of request (secure or not)
$request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

// set php_self in the local scope
if(basename($_SERVER['PHP_SELF']) != 'commerce_seo_url.php') {
	$PHP_SELF = basename($_SERVER['PHP_SELF']);
} else {
	$PHP_SELF = $aktuelle_datei;
}

// include the list of project filenames
require(DIR_WS_INCLUDES.'filenames.php');

// include the list of project database tables
require(DIR_WS_INCLUDES.'database_tables.php');

// SQL caching dir
define('SQL_CACHEDIR', DIR_FS_CATALOG.'cache/');

// Store DB-Querys in a Log File
if(!defined('STORE_DB_TRANSACTIONS')) {
	define('STORE_DB_TRANSACTIONS', 'false');
}

// graduated prices model or products assigned ?
define('GRADUATED_ASSIGN', 'true');

// Database
require_once(DIR_FS_INC.'cseo_db.inc.php');
xtc_db_connect() or die('Der Datenbankserver konnte nicht erreicht werden!');
$configuration_query = xtc_db_query("SELECT configuration_key as cfgKey, configuration_value as cfgValue FROM ".TABLE_CONFIGURATION." WHERE configuration_key != 'CURRENT_TEMPLATE'");
while ($configuration = xtc_db_fetch_array($configuration_query)) {
	define($configuration['cfgKey'], $configuration['cfgValue']);
}


require_once(DIR_FS_INC . 'cseo_get_conf.inc.php');
require_once (DIR_FS_INC.'xtc_get_top_level_domain.inc.php');
require_once (DIR_FS_INC.'xtc_hide_session_id.inc.php');
require_once (DIR_FS_INC.'cseo_truncate.inc.php');
require_once (DIR_FS_INC.'cseo_get_content.inc.php');
require_once (DIR_FS_INC.'cseo_get_content_child.inc.php');
require_once (DIR_FS_INC.'cseo_get_customer_name.inc.php');

// Links basics
require_once (DIR_FS_INC.'xtc_href_link.inc.php');
require_once (DIR_FS_INC.'xtc_php_mail.inc.php');
require_once (DIR_FS_INC.'xtc_product_link.inc.php');
require_once (DIR_FS_INC.'xtc_category_link.inc.php');
require_once (DIR_FS_INC.'xtc_manufacturer_link.inc.php');

// FORM functions
require_once (DIR_FS_INC.'cseo_form.inc.php');

require_once (DIR_FS_INC.'cseo_get_img_size.inc.php');

require_once (DIR_FS_INC.'xtc_not_null.inc.php');
require_once (DIR_FS_INC.'xtc_update_whos_online.inc.php');
require_once (DIR_FS_INC.'xtc_activate_banners.inc.php');
require_once (DIR_FS_INC.'xtc_expire_banners.inc.php');
require_once (DIR_FS_INC.'xtc_expire_specials.inc.php');
require_once (DIR_FS_INC.'xtc_expire_gratis.inc.php');
require_once (DIR_FS_INC.'xtc_parse_category_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_category_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_parent_categories.inc.php');
require_once (DIR_FS_INC.'xtc_get_product_path.inc.php');
require_once (DIR_FS_INC.'xtc_has_product_attributes.inc.php');
require_once (DIR_FS_INC.'xtc_get_stock_img.inc.php');
require_once (DIR_FS_INC.'xtc_check_stock_attributes.inc.php');


require_once (DIR_FS_INC.'xtc_redirect.inc.php');
require_once (DIR_FS_INC.'xtc_get_uprid.inc.php');
require_once (DIR_FS_INC.'xtc_get_all_get_params.inc.php');
require_once (DIR_FS_INC.'xtc_currency_exists.inc.php');
require_once (DIR_FS_INC.'xtc_remove_non_numeric.inc.php');
require_once (DIR_FS_INC.'xtc_get_ip_address.inc.php');
require_once (DIR_FS_INC.'xtc_setcookie.inc.php');
require_once (DIR_FS_INC.'xtc_check_agent.inc.php');
require_once (DIR_FS_INC.'xtc_count_cart.inc.php');
require_once (DIR_FS_INC.'xtc_get_qty.inc.php');
require_once (DIR_FS_INC.'create_coupon_code.inc.php');
require_once (DIR_FS_INC.'xtc_gv_account_update.inc.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate_from_desc.inc.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
require_once (DIR_FS_INC.'xtc_add_tax.inc.php');
require_once (DIR_FS_INC.'xtc_cleanName.inc.php');
require_once (DIR_FS_INC.'xtc_calculate_tax.inc.php');
require_once (DIR_FS_INC.'xtc_input_validation.inc.php');
require_once (DIR_FS_INC.'xtc_js_lang.php');	
require_once (DIR_FS_INC.'xtc_oe_customer_infos.inc.php');
require_once (DIR_FS_INC.'get_cross_sell_name.inc.php');

require_once (DIR_FS_INC.'cseo_get_usermod.inc.php');

require_once (DIR_FS_INC.'xtc_image.inc.php');
require_once (DIR_FS_INC.'xtc_image_button.inc.php');
require_once (DIR_FS_INC.'cseo_wk_image_submit.inc.php');
require_once (DIR_FS_INC.'cseo_wk_image_button.inc.php');

$blog_settings_query = xtc_db_query("SELECT blog_key, wert AS blog_wert FROM blog_settings");
while ($blog_settings = xtc_db_fetch_array($blog_settings_query)) {
	define(strtoupper($blog_settings['blog_key']), $blog_settings['blog_wert']);
}

// Set the length of the redeem code, the longer the more secure
// Kommt eigentlich schon aus der Table configuration
if(defined(SECURITY_CODE_LENGTH)) {
  define('SECURITY_CODE_LENGTH', '10');
}

require_once (DIR_WS_CLASSES.'class.phpmailer.php');
if (EMAIL_TRANSPORT == 'smtp') {
  require_once (DIR_WS_CLASSES.'class.smtp.php');
}

// set the application parameters

function xtDBquery($query) {
	if (DB_CACHE == 'true') {
		$result = xtc_db_queryCached($query);
	} else {
		$result = xtc_db_query($query);
	}
	return $result;
}

function CacheCheck() {
	if (USE_CACHE == 'false') return false;
	if (!isset($_COOKIE['cSEOid'])) return false;
	return true;
}

if((GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded = extension_loaded('zlib'))) {
	if (($ini_zlib_output_compression = (int)ini_get('zlib.output_compression')) < 1) {
		ob_start('ob_gzhandler');
	} else {
		ini_set('zlib.output_compression_level', GZIP_LEVEL);
	}
} else {
	ob_start();
}

// set the HTTP GET parameters manually if search_engine_friendly_urls is enabled
if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
  $pathinfo = ((getenv('PATH_INFO')=='') ? $_SERVER['ORIG_PATH_INFO'] : getenv('PATH_INFO'));
  if(preg_match('/\.php/',$pathinfo)):
    $PATH_INFO = substr(stristr('.php', $pathinfo),1);
  else:
    $PATH_INFO = $pathinfo;
  endif;
  if (strlen($PATH_INFO) > 1) {
    $GET_array = array ();
    $PHP_SELF = str_replace($PATH_INFO, '', $PHP_SELF);
    $vars = explode('/', substr($PATH_INFO, 1));
		for ($i = 0, $n = sizeof($vars); $i < $n; $i ++) {
			if (strpos($vars[$i], '[]')) {
				$GET_array[substr($vars[$i], 0, -2)][] = $vars[$i +1];
			} else {
				$_GET[$vars[$i]] = htmlspecialchars($vars[$i +1]);
				if(get_magic_quotes_gpc())
					$_GET[$vars[$i]] = addslashes($_GET[$vars[$i]]);
			}
			$i ++;
		}

		if (sizeof($GET_array) > 0) {
			while (list ($key, $value) = each($GET_array)) {
				$_GET[$key] = htmlspecialchars($value);
				if(get_magic_quotes_gpc())
					$_GET[$key] = addslashes($_GET[$key]);
			}
		}
	}
	if($PHP_SELF=='')
		$PHP_SELF='/index.php';
}
// check GET/POST/COOKIE VARS
// require (DIR_WS_CLASSES.'class.inputfilter.php');
$InputFilter = new InputFilter();
$_GET = $InputFilter->process($_GET);
$_POST = $InputFilter->process($_POST);
$_REQUEST = $InputFilter->process($_REQUEST);
$_GET = $InputFilter->safeSQL($_GET);
$_POST = $InputFilter->safeSQL($_POST);
$_REQUEST = $InputFilter->safeSQL($_REQUEST);

// set the top level domains
$http_domain = xtc_get_top_level_domain(HTTP_SERVER);
$https_domain = xtc_get_top_level_domain(HTTPS_SERVER);
$current_domain = (($request_type == 'NONSSL') ? $http_domain : $https_domain);

// include navigation history class
require (DIR_WS_CLASSES.'class.navigation_history.php');

// some code to solve compatibility issues
require (DIR_WS_FUNCTIONS.'compatibility.php');

// define how the session functions will be used
require (DIR_WS_FUNCTIONS.'sessions.php');

// set the session name and save path
session_name('cSEOid');
if (STORE_SESSIONS != 'mysql') 
	session_save_path(SESSION_WRITE_DIRECTORY);

// set the session cookie parameters
if (function_exists('session_set_cookie_params')) {
	session_set_cookie_params(0, '/', (xtc_not_null($current_domain) ? '.'.$current_domain : ''));
} elseif (function_exists('ini_set')) {
	ini_set('session.cookie_lifetime', '0');
	ini_set('session.cookie_path', '/');
	ini_set('session.cookie_domain', (xtc_not_null($current_domain) ? '.'.$current_domain : ''));
}

// set the session ID if it exists
if (isset ($_POST[session_name()])) {
	session_id($_POST[session_name()]);
} elseif (($request_type == 'SSL') && isset ($_GET[session_name()])) {
	session_id($_GET[session_name()]);
}

// start the session
$session_started = false;
if (SESSION_FORCE_COOKIE_USE == 'true') {
	xtc_setcookie('cookie_test', 'please_accept_for_session', time() + 60 * 60 * 24 * 30, '/', $current_domain);

	if (isset ($_COOKIE['cookie_test'])) {
		session_start();
		include (DIR_WS_INCLUDES.'tracking.php');
		$session_started = true;
	}
} else {
	session_start();
	include (DIR_WS_INCLUDES.'tracking.php');
	$session_started = true;
}

// check the Agent
$truncate_session_id = false;
if (CHECK_CLIENT_AGENT) {
	$truncate_session_id = true;
	if (xtc_check_agent() == 1) {
		$truncate_session_id = true;
	}
}

// verify the ssl_session_id if the feature is enabled
if (($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'true') && (ENABLE_SSL == true) && ($session_started == true)) {
  $ssl_session_id = getenv('SSL_SESSION_ID');
  if (!isset($_SESSION['SSL_SESSION_ID'])) {
    $_SESSION['SESSION_SSL_ID'] = $ssl_session_id;
  }
  if ($_SESSION['SESSION_SSL_ID'] != $ssl_session_id) {
    session_destroy();
    xtc_redirect(xtc_href_link(FILENAME_SSL_CHECK));
  }
}

 // Security Pro by FWR Media
include_once DIR_WS_FUNCTIONS . 'securityfilter.php';
$security_pro = new Fwr_Media_Security_Pro;
$security_pro->cleanse($PHP_SELF);
$security_pro->cseo_security($PHP_SELF);
// End - Security Pro by FWR Media


// verify the browser user agent if the feature is enabled
if (SESSION_CHECK_USER_AGENT == 'true') {
	$http_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$http_user_agent2 = strtolower(getenv("HTTP_USER_AGENT"));
	$http_user_agent = ($http_user_agent == $http_user_agent2) ? $http_user_agent : $http_user_agent.';'.$http_user_agent2;
	if (!isset ($_SESSION['SESSION_USER_AGENT'])) {
		$_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
	}
	if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
		session_destroy();
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
}

xtc_update_whos_online();
$current_script = basename(basename($_SERVER['SCRIPT_FILENAME']));

if (MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATECONTENT == 'True' && 
	MODULE_COMMERCE_SEO_INDEX_STATUS == 'True' && 
	($current_script == FILENAME_DEFAULT || $current_script==FILENAME_PRODUCT_INFO || $current_script==FILENAME_CONTENT || substr_count($_SERVER['REQUEST_URI'],'commerce_seo_url.php') > 0 )) {

	if(substr_count($_SERVER['REQUEST_URI'],'commerce_seo_url.php') > 0 ) {
		if($_GET['products_id'] != '')
			$current_script = 'product_info.php';
		elseif ($_GET['cat'] != '')	{
			$current_script = 'index.php';
			unset($_GET['cPath']);
		} elseif ($_GET['cPath'] != '') {
			$current_script = 'index.php';
			unset($_GET['cat']);
		} elseif ($_GET['coID'] != '')
			$current_script = 'shop_content.php';
		elseif ($_GET['blog_item'] != '' )
			$current_script = 'blog.php';
		elseif ($_GET['blog_cat'] != '' )
			$current_script = 'blog.php';
	}

	// Build the 301 Redirect for old xtc URL if required
	require_once(DIR_FS_INC.'commerce_seo.inc.php');
	!$CommerceSeo ? $CommerceSeo = new CommerceSeo() : false;

	$redirectElementId = $CommerceSeo->getIdForXTCSumaFriendlyURL($current_script);
}

if(($data['language_id'] != $_SESSION['languages_id']) && !empty($data['language_id']) && !isset($_GET['language'])) {
	$languageId_query   = xtc_db_query("SELECT code FROM languages WHERE languages_id='".$data['language_id']."'");
	$languageId_result	= xtc_db_fetch_array($languageId_query,false);
	$_GET['language'] = $languageId_result['code'];
}

// verify the IP address if the feature is enabled
if (SESSION_CHECK_IP_ADDRESS == 'true') {
	$ip_address = xtc_get_ip_address();
	if (!isset ($_SESSION['SESSION_IP_ADDRESS'])) {
		$_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
	}

	if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
		session_destroy();
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
}

// set the language
if (!isset ($_SESSION['language']) || isset ($_GET['language'])) {
	$lng = new language(xtc_input_validation($_GET['language'], 'char', ''));
	if (!isset ($_GET['language'])) {
		$lng->get_browser_language();
	}
	$_SESSION['language'] = $lng->language['directory'];
	$_SESSION['languages_id'] = $lng->language['id'];
	$_SESSION['language_charset'] = $lng->language['language_charset'];
	$_SESSION['language_code'] = $lng->language['code'];
}

if (isset($_SESSION['language']) && !isset($_SESSION['language_charset'])) {
	// include (DIR_WS_CLASSES.'language.php');
	$lng = new language(xtc_input_validation($_SESSION['language'], 'char', ''));
	$_SESSION['language'] = $lng->language['directory'];
	$_SESSION['languages_id'] = $lng->language['id'];
	$_SESSION['language_charset'] = $lng->language['language_charset'];
	$_SESSION['language_code'] = $lng->language['code'];
}

// include the language translations
require (DIR_WS_LANGUAGES.$_SESSION['language'].'/'.$_SESSION['language'].'.php');

$define_link_query = xtDBQuery("SELECT cp.url_text AS url, cn.file_name AS file_name, cn.file_name_php AS php FROM commerce_seo_url_names AS cn LEFT OUTER JOIN 
										commerce_seo_url_personal_links AS cp ON (cp.file_name = cn.file_name AND cp.language_id = '".$_SESSION['languages_id']."')");
while($define_link = xtc_db_fetch_array($define_link_query)) {
	define(strtoupper($define_link['file_name']), (MODULE_COMMERCE_SEO_INDEX_STATUS=='True' ? ($define_link['url']!='' ? $define_link['url'] : $define_link['php']) : $define_link['php']) );
}

// currency
if (!isset ($_SESSION['currency']) || isset ($_GET['currency']) || ((USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && (LANGUAGE_CURRENCY != $_SESSION['currency']))) {
	if (isset ($_GET['currency'])) {
		if (!$_SESSION['currency'] = xtc_currency_exists($_GET['currency'])) {
			$_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
		}
	} else {
		$_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
	}
}
if(isset($_SESSION['currency']) && $_SESSION['currency'] == '') {
	$_SESSION['currency'] = DEFAULT_CURRENCY;
}

// write customers status in session
require(DIR_WS_INCLUDES.'write_customers_status.php');

$main = new main();
$xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
$o_paypal = new paypal_checkout();

require(DIR_WS_INCLUDES.FILENAME_CART_ACTIONS);
// create the shopping cart & fix the cart if necesary
if (!is_object($_SESSION['cart'])) {
	$_SESSION['cart'] = new shoppingCart();
}
// create the wish list & fix the list if necesary
if (!is_object($_SESSION['wishList'])) {
  $_SESSION['wishList'] = new wishList();
}

// infobox
require (DIR_WS_CLASSES.'class.boxes.php');

// auto activate and expire banners and specials
xtc_activate_banners();
xtc_expire_banners();
xtc_expire_specials();
xtc_expire_gratis();

if (isset ($_GET['info'])) {
	$site = explode('_', $_GET['info']);
	$pID = $site[0];
	$actual_products_id = (int) str_replace('p', '', $pID);
	$product = new product($actual_products_id);
} elseif (isset($_GET['products_id'])) {
	$actual_products_id = (int) $_GET['products_id'];
	$product = new product($actual_products_id);
}
if(!is_object($product)) {
	$product = new product();
}

// new c URLS
if (isset ($_GET['cat'])) {
	$site = explode('_', $_GET['cat']);
	$cID = $site[0];
	$cID = str_replace('c', '', $cID);
	$_GET['cPath'] = xtc_get_category_path($cID);
}
// new m URLS
if (isset ($_GET['manu'])) {
	$site = explode('_', $_GET['manu']);
	$mID = $site[0];
	$mID = (int)str_replace('m', '', $mID);
	$_GET['manufacturers_id'] = $mID;
}

// calculate category path
if (isset($_GET['cPath']) && !$product->isProduct()) {
	$cPath = xtc_input_validation($_GET['cPath'], 'cPath', '');
} elseif (is_object($product) && !isset ($_GET['manufacturers_id'])) {
	if ($product->isProduct()) {
		$cPath = xtc_get_product_path($actual_products_id);
	} else {
		$cPath = '';
	}
} else {
	$cPath = '';
}

if (xtc_not_null($cPath)) {
	$cPath_array = xtc_parse_category_path($cPath);
	$cPath = implode('_', $cPath_array);
	$current_category_id = $cPath_array[(sizeof($cPath_array) - 1)];
} else {
	$current_category_id = 0;
}

$breadcrumb = new breadcrumb;
$breadcrumb->add(HEADER_TITLE_TOP, HTTP_SERVER);

if(DIR_WS_CATALOG != '/') {
	$breadcrumb->add(HEADER_TITLE_CATALOG, xtc_href_link(FILENAME_DEFAULT));
}

// add category names or the manufacturer name to the breadcrumb trail
if (isset ($cPath_array)) {
	for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i ++) {
		if (GROUP_CHECK == 'true') {
			$group_check = "AND c.group_permission_".(int)$_SESSION['customers_status']['customers_status_id']." = 1 ";
		}
		$categories_query = xtDBquery("SELECT
                                        cd.categories_name
                                        FROM ".TABLE_CATEGORIES_DESCRIPTION." cd,
                                        ".TABLE_CATEGORIES." c
                                        WHERE cd.categories_id = '".$cPath_array[$i]."'
                                        AND c.categories_id=cd.categories_id
                                        ".$group_check."
                                        AND cd.language_id='".(int)$_SESSION['languages_id']."'");
		if (xtc_db_num_rows($categories_query,true) > 0) {
			$categories = xtc_db_fetch_array($categories_query,true);
			$breadcrumb->add($categories['categories_name'], xtc_href_link(FILENAME_DEFAULT, xtc_category_link($cPath_array[$i], $categories['categories_name'])));
		} else {
			break;
		}
	}
} elseif (xtc_not_null($_GET['manufacturers_id'])) {
	$manufacturers_query = xtDBquery("SELECT manufacturers_name FROM ".TABLE_MANUFACTURERS." WHERE manufacturers_id = '".(int)$_GET['manufacturers_id']."'");
	$manufacturers = xtc_db_fetch_array($manufacturers_query, true);
	$breadcrumb->add($manufacturers['manufacturers_name'], xtc_href_link(FILENAME_DEFAULT, xtc_manufacturer_link((int) $_GET['manufacturers_id'], $manufacturers['manufacturers_name'])));
}

// add the products model/name to the breadcrumb trail
if ($product->isProduct()) {
	$breadcrumb->add($product->getBreadcrumbModel(), xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($product->data['products_id'], $product->data['products_name'])));
}

$messageStack = new messageStack;

// set which precautions should be checked
define('WARN_INSTALL_EXISTENCE', 'false');
define('WARN_CONFIG_WRITEABLE', 'false');
define('WARN_SESSION_DIRECTORY_NOT_WRITEABLE', 'true');
define('WARN_SESSION_AUTO_START', 'true');
define('WARN_DOWNLOAD_DIRECTORY_NOT_READABLE', 'true');

//Ist Tabelle da?
function table_exists($table_name) {
  $Table = xtc_db_query("SHOW TABLES LIKE '" . $table_name . "'");
  if(xtc_db_fetch_row($Table) === false) { 
    return(false); 
  } else {
    return(true);
  }
}

//Ist Spalte da?
	
function column_exists($table, $column) {
  $Table = xtc_db_query("SHOW COLUMNS FROM $table LIKE '" . $column . "'");
  if(xtc_db_fetch_row($Table) === false) {
    return(false);
  } else {
    return(true);
  }
}

// Include Template Engine
require (DIR_WS_CLASSES.'Smarty_3/Smarty.class.php');

if (isset ($_SESSION['customer_id'])) {
	$account_type_query = xtc_db_query("SELECT
		                                    account_type,
		                                    customers_default_address_id
		                                    FROM
		                                    ".TABLE_CUSTOMERS."
		                                    WHERE customers_id = '".(int)$_SESSION['customer_id']."'");
	$account_type = xtc_db_fetch_array($account_type_query);

	if (!isset ($_SESSION['customer_country_id'])) {
		$zone_query = xtc_db_query("SELECT  entry_country_id
				                                     FROM ".TABLE_ADDRESS_BOOK."
				                                     WHERE customers_id='".(int)$_SESSION['customer_id']."'
				                                     and address_book_id='".$account_type['customers_default_address_id']."'");

		$zone = xtc_db_fetch_array($zone_query);
		$_SESSION['customer_country_id'] = $zone['entry_country_id'];
	}
	$_SESSION['account_type'] = $account_type['account_type'];
} else {
	$_SESSION['account_type'] = '0';
}

// modification for nre graduated system
unset ($_SESSION['actual_content']);

$check = basename($_SERVER['PHP_SELF']);
if((DOWN_FOR_MAINTENANCE == 'true') && 
	($_SESSION['customers_status']['customers_status_id'] != 0) && 
	($check != FILENAME_DOWN_FOR_MAINTENANCE_LOGIN) && 
	($check != 'cseo_css.php') && 
	($check != 'css_styler.php')) {
  xtc_redirect(HTTP_SERVER.DIR_WS_CATALOG.FILENAME_DOWN_FOR_MAINTENANCE_LOGIN);
}

xtc_count_cart();

/* magnalister v1.0.0 */
if (!defined('MAGNA_CALLBACK_MODE') && file_exists(DIR_FS_DOCUMENT_ROOT.'magnaCallback.php')) {
	ob_start();
	require_once(DIR_FS_DOCUMENT_ROOT.'magnaCallback.php');
	magnaExecute('magnaCollectStats');
	ob_end_clean();
}
/* END magnalister */

function NoEntities($Input) {
	$TransTable1 = get_html_translation_table(HTML_ENTITIES);
	foreach($TransTable1 as $ASCII => $Entity) {
		$TransTable2[$ASCII] = '&#'.ord($ASCII).';';
	}
	$TransTable1 = array_flip($TransTable1);
	$TransTable2 = array_flip($TransTable2);
	return strtr (strtr ($Input, $TransTable1), $TransTable2);
}

function AmpReplace($Treffer) {
	return $Treffer[1].htmlentities(NoEntities($Treffer[2])).$Treffer[3];
}

//Addonsystem 
if (table_exists('addon_filenames')) {
	$addon_filenames_query = xtc_db_query("SELECT configuration_key, configuration_value FROM addon_filenames");
	while ($addon_filenames = xtc_db_fetch_array($addon_filenames_query)) {
		define($addon_filenames['configuration_key'], $addon_filenames['configuration_value']);
	}
}
if (table_exists('addon_database')) {
	$addon_database_query = xtc_db_query("SELECT configuration_key, configuration_value FROM addon_database");
	while ($addon_database = xtc_db_fetch_array($addon_database_query)) {
		define($addon_database['configuration_key'], $addon_database['configuration_value']);
	}
}
if (table_exists('addon_languages')) {
	$addon_languages_query = xtc_db_query("SELECT configuration_key, configuration_value FROM addon_languages WHERE languages_id = ".(int)$_SESSION['languages_id']."");
	while ($addon_languages = xtc_db_fetch_array($addon_languages_query)) {
		define($addon_languages['configuration_key'], $addon_languages['configuration_value']);
	}
}
if (table_exists('cseo_lang_button')) {
$language_settings_query = xtDBquery("SELECT button, buttontext FROM cseo_lang_button WHERE language_id = '".(int)$_SESSION['languages_id']."'");
while ($language_settings = xtc_db_fetch_array($language_settings_query)) {
	define(strtoupper($language_settings['button']), $language_settings['buttontext']);
}
}
$browser = new Browser();
$template_configuration_query = xtc_db_fetch_array(xtc_db_query('SELECT configuration_key, configuration_value FROM '.TABLE_CONFIGURATION." WHERE configuration_key = 'CURRENT_TEMPLATE' LIMIT 1"));
define($template_configuration_query['configuration_key'], $template_configuration_query['configuration_value']);

function strtolower_wrapper($p_string, $p_encoding = 'utf-8') {
	if(function_exists('mb_strtolower')) {
		$t_strtolower = mb_strtolower($p_string, $p_encoding);
	} else {
		$t_strtolower = strtolower($p_string);
	}
	return $t_strtolower; 
}
//Lagerwarung
if (MODULE_CUSTOMERS_ADMINMAIL_STATUS == 'true') {
	include_once(DIR_WS_FUNCTIONS . 'stock_mails.php');
	sendstockmails();
}

//Delete Guest inactive Account 
if (DELETE_GUEST_ACCOUNT == 'true') {
	include_once(DIR_WS_FUNCTIONS . 'delete_unused_guest_account.php');
	delete_unused_guest_accounts();
}

if (USE_TEMPLATE_DEVMODE == 'true') {
	#Debugger
	$cseo_debugger = new Debugger();
}
if (file_exists(DIR_WS_INCLUDES . 'addons/application_top_addon.php')) {
	include (DIR_WS_INCLUDES . 'addons/application_top_addon.php');
}
if (file_exists(DIR_WS_INCLUDES . 'plugins/cseo_modules/general/general.php')) {
	include (DIR_WS_INCLUDES . 'plugins/cseo_modules/general/general.php');
}


$cseo_application_bottom_extender_component = cseohookfactory::create_object('ApplicationTopExtenderComponent');
$cseo_application_bottom_extender_component->set_data('GET', $_GET);
$cseo_application_bottom_extender_component->set_data('POST', $_POST);
$cseo_application_bottom_extender_component->proceed();

define('CHECKOUT_AJAX_STAT', 'false');