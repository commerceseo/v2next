<?php
/* -----------------------------------------------------------------
 * 	$Id: header.php 943 2014-04-08 13:26:37Z akausch $
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
header("Content-Type: text/html; charset=utf-8");
$version = xtc_db_fetch_array(xtc_db_query("SELECT version FROM database_version;"));
require(DIR_WS_INCLUDES . 'metatag.php');
include('includes/column_top.php');
if (file_exists(DIR_WS_INCLUDES . 'addons/header_addon.php')) {
	include (DIR_WS_INCLUDES .'addons/header_addon.php');
}
?>
<div class="container-fluid">
    <?php
    if ($messageStack->size > 0) {
        echo $messageStack->output();
    }
    ?>
