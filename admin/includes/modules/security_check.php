<?php
/* -----------------------------------------------------------------
 * 	$Id: security_check.php 873 2014-03-25 16:42:10Z akausch $
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

$file_warning = '';
$installer_warning = '';

if (@is_readable(DIR_FS_CATALOG.'installer/')) {
	$installer_warning .= '<br>'.DIR_FS_CATALOG.'installer/';
}
if (@is_readable(DIR_FS_CATALOG.'cseov22_import/')) {
	$installer_warning .= '<br>'.DIR_FS_CATALOG.'cseov22_import/';
}

if (@is_writable(DIR_FS_CATALOG.'includes/configure.php')) {
	$file_warning .= '<br>'.DIR_FS_CATALOG.'includes/configure.php';
}
if (@is_writable(DIR_FS_ADMIN.'includes/configure.php')) {
	$file_warning .= '<br>'.DIR_FS_ADMIN.'includes/configure.php';
}

if (!@is_writable(DIR_FS_ADMIN.'templates_c/')) {
	$folder_warning .= '<br>'.DIR_FS_ADMIN.'templates_c/';
}
if (!@is_writable(DIR_FS_ADMIN.'cache/')) {
	$folder_warning .= '<br>'.DIR_FS_ADMIN.'cache/';
}
if (!@is_writable(DIR_FS_ADMIN.'backups/')) {
	$folder_warning .= '<br>'.DIR_FS_ADMIN.'backups/';
}

if (!@is_writable(DIR_FS_CATALOG.'templates_c/')) {
	$folder_warning .= '<br>'.DIR_FS_CATALOG.'templates_c/';
}
if (!@is_writable(DIR_FS_CATALOG.'cache/')) {
	$folder_warning .= '<br>'.DIR_FS_CATALOG.'cache/';
}
if (!@is_writable(DIR_FS_CATALOG.'export/')) {
	$folder_warning .= '<br>'.DIR_FS_CATALOG.'export/';
}
if (!@is_writable(DIR_FS_CATALOG.'images/')) {
	$folder_warning .= '<br>'.DIR_FS_CATALOG.'images/';
}
if (!@is_writable(DIR_FS_CATALOG.'logfiles/')) {
	$folder_warning .= '<br>'.DIR_FS_CATALOG.'logfiles/';
}
if (!@is_writable(DIR_FS_CATALOG.'media/')) {
	$folder_warning .= '<br>'.DIR_FS_CATALOG.'media/';
}
if (!@is_writable(DIR_FS_CATALOG.'media/content/')) {
	$folder_warning .= '<br>'.DIR_FS_CATALOG.'media/content/';
}

$payment = xtc_db_fetch_array(xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_PAYMENT_INSTALLED';"));
$shipping = xtc_db_fetch_array(xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED';"));

if ($installer_warning != '' || $file_warning != '' || $folder_warning != '' || (empty($payment['configuration_value'])) || (empty($shipping['configuration_value']))) {
    echo '<table id="security_check" border="0" width="98%" cellspacing="0" cellpadding="8">';
	if ($installer_warning != '') {
		echo '<tr><td>' . TEXT_INSTALLER_WARNING;
		echo '<b>' . $installer_warning . '</b></td></tr>';
	}
	if ($file_warning != '') {
		echo '<tr><td>' . TEXT_FILE_WARNING;
		echo '<b>' . $file_warning . '</b></td></tr>';
	}
	if ($folder_warning != '') {
		echo '<tr><td>' . TEXT_FOLDER_WARNING;
		echo '<b>' . $folder_warning . '</b></td></tr>';
	}
	if (empty($payment['configuration_value'])) {
		echo '<tr><td>' . TEXT_PAYMENT_ERROR . '</td></tr>';
	}
	if (empty($shipping['configuration_value'])) {
		echo '<tr><td>' . TEXT_SHIPPING_ERROR . '</td></tr>';
	}
    echo '</table>';
}
