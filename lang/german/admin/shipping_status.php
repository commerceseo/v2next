<?php
/*-----------------------------------------------------------------
* 	$Id: shipping_status.php 420 2013-06-19 18:04:39Z akausch $
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




define('HEADING_TITLE', 'Lieferstatus');

define('TABLE_HEADING_SHIPPING_STATUS', 'Lieferstatus');
define('TABLE_HEADING_ACTION', 'Aktion');

define('TEXT_INFO_EDIT_INTRO', 'Bitte f&uuml;hren Sie notwendige &Auml;nderungen durch');
define('TEXT_INFO_SHIPPING_STATUS_NAME', 'Lieferstatus:');
define('TEXT_INFO_INSERT_INTRO', 'Bitte geben Sie den neuen Lieferstatus mit allen relevanten Daten ein');
define('TEXT_INFO_DELETE_INTRO', 'Sind Sie sicher, dass Sie diesen Lieferstatus l&ouml;schen m&ouml;chten?');
define('TEXT_INFO_HEADING_NEW_SHIPPING_STATUS', 'Neuer Lieferstatus');
define('TEXT_INFO_HEADING_EDIT_SHIPPING_STATUS', 'Lieferstatus bearbeiten');
define('TEXT_INFO_SHIPPING_STATUS_IMAGE', 'Bild:');
define('TEXT_INFO_HEADING_DELETE_SHIPPING_STATUS', 'Lieferstatus l&ouml;schen');

define('ERROR_REMOVE_DEFAULT_SHIPPING_STATUS', 'Fehler: Der Standard-Lieferstatus kann nicht gel&ouml;scht werden. Bitte definieren Sie einen neuen Standard-Lieferstatus und wiederholen Sie den Vorgang.');
define('ERROR_STATUS_USED_IN_ORDERS', 'Fehler: Dieser Lieferstatus wird zur Zeit noch f&uuml;r Artikel verwendet.');
define('ERROR_STATUS_USED_IN_HISTORY', 'Fehler: Dieser Lieferstatus wird zur Zeit noch f&uuml;r Artikel verwendet.');
?>