<?php

/* -----------------------------------------------------------------
 * 	$Id: write_config.php 987 2014-04-22 10:40:42Z akausch $
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

if (@is_writeable(cseo_local_install_path() . 'admin/includes/configure.php') && @is_writeable(cseo_local_install_path() . 'includes/configure.php')) {
    $db = array();
    $db['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
    $db['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
    $db['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
    $db['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));

    if ($_POST['SALT_KEY'] == '') {
        $db['SALT_KEY'] = md5(mt_rand());
    } else {
        $db['SALT_KEY'] = trim(stripslashes($_POST['SALT_KEY']));
    }
    $db_error = false;

    xtc_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);

    if (!$db_error) {
        xtc_db_test_connection($db['DB_DATABASE']);
    }

    if (!$db_error) {

        $file_contents = '<?php
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
* ---------------------------------------------------------------*/' . "\n" .
                '' . "\n" .
                '// Define the webserver and path parameters' . "\n" .
                '// * DIR_FS_* = Filesystem directories (local/physical)' . "\n" .
                '// * DIR_WS_* = Webserver directories (virtual/URL)' . "\n" .
                'define(\'HTTP_SERVER\', \'' . $_POST['HTTP_SERVER'] . '\'); // eg, http://localhost - should not be empty for productive servers' . "\n" .
                'define(\'HTTPS_SERVER\', \'' . $_POST['HTTPS_SERVER'] . '\'); // eg, https://localhost - should not be empty for productive servers' . "\n" .
                'define(\'ENABLE_SSL\', ' . (($_POST['ENABLE_SSL'] == 'true') ? 'true' : 'false') . '); // secure webserver for checkout procedure?' . "\n" .
                'define(\'DIR_WS_CATALOG\', \'' . $_POST['DIR_WS_CATALOG'] . '\'); // absolute path required' . "\n" .
                'define(\'DIR_FS_DOCUMENT_ROOT\', \'' . cseo_local_install_path() . '\');' . "\n" .
                'define(\'DIR_FS_CATALOG\', \'' . cseo_local_install_path() . '\');' . "\n" .
                'define(\'COMMERCE_SEO_V22_INSTALLED\', \'true\');' . "\n" .
                'define(\'DIR_WS_IMAGES\', \'images/\');' . "\n" .
                'define(\'DIR_WS_ORIGINAL_IMAGES\', DIR_WS_IMAGES .\'product_images/original_images/\');' . "\n" .
                'define(\'DIR_WS_THUMBNAIL_IMAGES\', DIR_WS_IMAGES .\'product_images/thumbnail_images/\');' . "\n" .
                'define(\'DIR_WS_INFO_IMAGES\', DIR_WS_IMAGES .\'product_images/info_images/\');' . "\n" .
                'define(\'DIR_WS_POPUP_IMAGES\', DIR_WS_IMAGES .\'product_images/popup_images/\');' . "\n" .
                'define(\'DIR_WS_MINI_IMAGES\', DIR_WS_IMAGES .\'product_images/mini_images/\');' . "\n" .
                'define(\'DIR_WS_CATALOG_MOVIES\', DIR_WS_IMAGES .\'products_movies/\');' . "\n" .
                'define(\'DIR_WS_ICONS\', DIR_WS_IMAGES . \'icons/\');' . "\n" .
                'define(\'DIR_WS_INCLUDES\',DIR_FS_DOCUMENT_ROOT. \'includes/\');' . "\n" .
                'define(\'DIR_WS_FUNCTIONS\', DIR_WS_INCLUDES . \'functions/\');' . "\n" .
                'define(\'DIR_WS_CLASSES\', DIR_WS_INCLUDES . \'classes/\');' . "\n" .
                'define(\'DIR_WS_MODULES\', DIR_WS_INCLUDES . \'modules/\');' . "\n" .
                'define(\'DIR_WS_LANGUAGES\', DIR_FS_CATALOG . \'lang/\');' . "\n" .
                '' . "\n" .
                'define(\'DIR_WS_DOWNLOAD_PUBLIC\', DIR_WS_CATALOG . \'pub/\');' . "\n" .
                'define(\'DIR_FS_DOWNLOAD\', DIR_FS_CATALOG . \'download/\');' . "\n" .
                'define(\'DIR_FS_DOWNLOAD_PUBLIC\', DIR_FS_CATALOG . \'pub/\');' . "\n" .
                'define(\'DIR_FS_INC\', DIR_FS_CATALOG . \'inc/\');' . "\n" .
                'define(\'SALT_KEY\', \'' . $db['SALT_KEY'] . '\');' . "\n" .
                '' . "\n" .
                '// define our database connection' . "\n" .
                'define(\'DB_SERVER\', \'' . $db['DB_SERVER'] . '\'); // eg, localhost - should not be empty for productive servers' . "\n" .
                'define(\'DB_SERVER_USERNAME\', \'' . $db['DB_SERVER_USERNAME'] . '\');' . "\n" .
                'define(\'DB_SERVER_PASSWORD\', \'' . $db['DB_SERVER_PASSWORD'] . '\');' . "\n" .
                'define(\'DB_DATABASE\', \'' . $db['DB_DATABASE'] . '\');' . "\n" .
                'define(\'USE_PCONNECT\', \'' . (($_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\'); // use persistent connections?' . "\n" .
                'define(\'STORE_SESSIONS\', \'' . (($_POST['STORE_SESSIONS'] == 'files') ? '' : 'mysql') . '\'); // leave empty \'\' for default handler or set to \'mysql\'' . "\n" . '?>';
        $fp = fopen(DIR_FS_CATALOG . 'includes/configure.php', 'w');
        fputs($fp, $file_contents);
        fclose($fp);


        //create a configure.php
        $file_contents = '<?php
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
* ---------------------------------------------------------------*/' . "\n" .
                '' . "\n" .
                '// Define the webserver and path parameters' . "\n" .
                '// * DIR_FS_* = Filesystem directories (local/physical)' . "\n" .
                '// * DIR_WS_* = Webserver directories (virtual/URL)' . "\n" .
                'define(\'HTTP_SERVER\', \'' . $_POST['HTTP_SERVER'] . '\'); // eg, http://localhost or - https://localhost should not be empty for productive servers' . "\n" .
                'define(\'HTTP_CATALOG_SERVER\', \'' . $_POST['HTTP_SERVER'] . '\');' . "\n" .
                'define(\'HTTPS_CATALOG_SERVER\', \'' . $_POST['HTTPS_SERVER'] . '\');' . "\n" .
                'define(\'ENABLE_SSL_CATALOG\', \'' . (($_POST['ENABLE_SSL'] == 'true') ? 'true' : 'false') . '\'); // secure webserver for catalog module' . "\n" .
                'define(\'DIR_FS_DOCUMENT_ROOT\', \'' . cseo_local_install_path() . '\'); // where the pages are located on the server' . "\n" .
                'define(\'DIR_WS_ADMIN\', \'' . $_POST['DIR_WS_CATALOG'] . 'admin/' . '\'); // absolute path required' . "\n" .
                'define(\'DIR_FS_ADMIN\', \'' . cseo_local_install_path() . 'admin/' . '\'); // absolute pate required' . "\n" .
                'define(\'DIR_WS_CATALOG\', \'' . $_POST['DIR_WS_CATALOG'] . '\'); // absolute path required' . "\n" .
                'define(\'DIR_FS_CATALOG\', \'' . cseo_local_install_path() . '\'); // absolute path required' . "\n" .
                'define(\'COMMERCE_SEO_V22_INSTALLED\', \'true\');' . "\n" .
                'define(\'DIR_WS_IMAGES\', \'images/\');' . "\n" .
                'define(\'DIR_FS_CATALOG_IMAGES\', DIR_FS_CATALOG . \'images/\');' . "\n" .
                'define(\'DIR_FS_CATALOG_ORIGINAL_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/original_images/\');' . "\n" .
                'define(\'DIR_FS_CATALOG_THUMBNAIL_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/thumbnail_images/\');' . "\n" .
                'define(\'DIR_FS_CATALOG_INFO_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/info_images/\');' . "\n" .
                'define(\'DIR_FS_CATALOG_POPUP_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/popup_images/\');' . "\n" .
                'define(\'DIR_FS_CATALOG_MINI_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/mini_images/\');' . "\n" .
                'define(\'DIR_FS_CATALOG_MOVIES\', DIR_FS_CATALOG_IMAGES .\'products_movies/\');' . "\n" .
                'define(\'DIR_WS_ICONS\', DIR_WS_IMAGES . \'icons/\');' . "\n" .
                'define(\'DIR_WS_CATALOG_IMAGES\', DIR_WS_CATALOG . \'images/\');' . "\n" .
                'define(\'DIR_WS_CATALOG_ORIGINAL_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/original_images/\');' . "\n" .
                'define(\'DIR_WS_CATALOG_THUMBNAIL_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/thumbnail_images/\');' . "\n" .
                'define(\'DIR_WS_CATALOG_INFO_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/info_images/\');' . "\n" .
                'define(\'DIR_WS_CATALOG_POPUP_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/popup_images/\');' . "\n" .
                'define(\'DIR_WS_CATALOG_MINI_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/mini_images/\');' . "\n" .
                'define(\'DIR_WS_CATALOG_MOVIES\', DIR_WS_CATALOG_IMAGES .\'products_movies/\');' . "\n" .
                'define(\'DIR_WS_INCLUDES\', \'includes/\');' . "\n" .
                'define(\'DIR_WS_BOXES\', DIR_WS_INCLUDES . \'boxes/\');' . "\n" .
                'define(\'DIR_WS_FUNCTIONS\', DIR_WS_INCLUDES . \'functions/\');' . "\n" .
                'define(\'DIR_WS_CLASSES\', DIR_WS_INCLUDES . \'classes/\');' . "\n" .
                'define(\'DIR_WS_MODULES\', DIR_WS_INCLUDES . \'modules/\');' . "\n" .
                'define(\'DIR_WS_LANGUAGES\', DIR_WS_CATALOG. \'lang/\');' . "\n" .
                'define(\'DIR_FS_LANGUAGES\', DIR_FS_CATALOG. \'lang/\');' . "\n" .
                'define(\'DIR_FS_CATALOG_MODULES\', DIR_FS_CATALOG . \'includes/modules/\');' . "\n" .
                'define(\'DIR_FS_BACKUP\', DIR_FS_ADMIN . \'backups/\');' . "\n" .
                'define(\'DIR_FS_INC\', DIR_FS_CATALOG . \'inc/\');' . "\n" .
                'define(\'DIR_WS_FILEMANAGER\', DIR_WS_MODULES . \'fckeditor/editor/filemanager/browser/default/\');' . "\n" .
                'define(\'SALT_KEY\', \'' . $db['SALT_KEY'] . '\');' . "\n" .
                '' . "\n" .
                '// define our database connection' . "\n" .
                'define(\'DB_SERVER\', \'' . $db['DB_SERVER'] . '\'); // eg, localhost - should not be empty for productive servers' . "\n" .
                'define(\'DB_SERVER_USERNAME\', \'' . $db['DB_SERVER_USERNAME'] . '\');' . "\n" .
                'define(\'DB_SERVER_PASSWORD\', \'' . $db['DB_SERVER_PASSWORD'] . '\');' . "\n" .
                'define(\'DB_DATABASE\', \'' . $db['DB_DATABASE'] . '\');' . "\n" .
                'define(\'USE_PCONNECT\', \'' . (($_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\'); // use persisstent connections?' . "\n" .
                'define(\'STORE_SESSIONS\', \'' . (($_POST['STORE_SESSIONS'] == 'files') ? '' : 'mysql') . '\'); // leave empty \'\' for default handler or set to \'mysql\'' . "\n" .
                '' . "\n" .
                '?>';
        $fp = fopen(DIR_FS_CATALOG . 'admin/includes/configure.php', 'w');
        fputs($fp, $file_contents);
        fclose($fp);
    }

    @mysql_close();

    $t_output = 'success';
} else {
    $t_output = 'failed';
}
