<?php
/*-----------------------------------------------------------------
* 	configure.php
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

// Define the webserver and path parameters
// * DIR_FS_* = Filesystem directories (local/physical)
// * DIR_WS_* = Webserver directories (virtual/URL)
define('HTTP_SERVER', 'http://localhost'); // eg, http://localhost - should not be empty for productive servers
define('HTTPS_SERVER', 'https://localhost'); // eg, https://localhost - should not be empty for productive servers
define('ENABLE_SSL', false); // secure webserver for checkout procedure?
define('DIR_WS_CATALOG', '/v2nextce/trunk/'); // absolute path required
define('DIR_FS_DOCUMENT_ROOT', 'C:/xampp/htdocs/v2nextce/trunk/');
define('DIR_FS_CATALOG', 'C:/xampp/htdocs/v2nextce/trunk/');
define('COMMERCE_SEO_V22_INSTALLED', 'true');
define('DIR_WS_IMAGES', 'images/');
define('DIR_WS_ORIGINAL_IMAGES', DIR_WS_IMAGES .'product_images/original_images/');
define('DIR_WS_THUMBNAIL_IMAGES', DIR_WS_IMAGES .'product_images/thumbnail_images/');
define('DIR_WS_INFO_IMAGES', DIR_WS_IMAGES .'product_images/info_images/');
define('DIR_WS_POPUP_IMAGES', DIR_WS_IMAGES .'product_images/popup_images/');
define('DIR_WS_MINI_IMAGES', DIR_WS_IMAGES .'product_images/mini_images/');
define('DIR_WS_CATALOG_MOVIES', DIR_WS_IMAGES .'products_movies/');
define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');
define('DIR_WS_INCLUDES',DIR_FS_DOCUMENT_ROOT. 'includes/');
define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');
define('DIR_WS_LANGUAGES', DIR_FS_CATALOG . 'lang/');

define('DIR_WS_DOWNLOAD_PUBLIC', DIR_WS_CATALOG . 'pub/');
define('DIR_FS_DOWNLOAD', DIR_FS_CATALOG . 'download/');
define('DIR_FS_DOWNLOAD_PUBLIC', DIR_FS_CATALOG . 'pub/');
define('DIR_FS_INC', DIR_FS_CATALOG . 'inc/');
define('SALT_KEY', 'ebed71abd7af7c88333e875ba586654c');

// define our database connection
define('DB_SERVER', 'localhost'); // eg, localhost - should not be empty for productive servers
define('DB_SERVER_USERNAME', 'root');
define('DB_SERVER_PASSWORD', '');
define('DB_DATABASE', 'v2nextce');
define('USE_PCONNECT', 'false'); // use persistent connections?
define('STORE_SESSIONS', 'mysql'); // leave empty '' for default handler or set to 'mysql'
?>