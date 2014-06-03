<?php
/*-----------------------------------------------------------------
* 	$Id: currencies.php 420 2013-06-19 18:04:39Z akausch $
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



   
define('HEADING_TITLE', 'W&auml;hrungen');

define('TABLE_HEADING_CURRENCY_NAME', 'W&auml;hrung');
define('TABLE_HEADING_CURRENCY_CODES', 'K&uuml;rzel');
define('TABLE_HEADING_CURRENCY_VALUE', 'Wert');
define('TABLE_HEADING_ACTION', 'Aktion');

define('TEXT_INFO_EDIT_INTRO', 'Bitte f&uuml;hren Sie alle notwendigen &Auml;nderungen durch');
define('TEXT_INFO_CURRENCY_TITLE', 'Name:');
define('TEXT_INFO_CURRENCY_CODE', 'K&uuml;rzel:');
define('TEXT_INFO_CURRENCY_SYMBOL_LEFT', 'Symbol Links:');
define('TEXT_INFO_CURRENCY_SYMBOL_RIGHT', 'Symbol Rechts:');
define('TEXT_INFO_CURRENCY_DECIMAL_POINT', 'Dezimalkomma:');
define('TEXT_INFO_CURRENCY_THOUSANDS_POINT', 'Tausenderpunkt:');
define('TEXT_INFO_CURRENCY_DECIMAL_PLACES', 'Dezimalstellen:');
define('TEXT_INFO_CURRENCY_LAST_UPDATED', 'letzte &Auml;nderung:');
define('TEXT_INFO_CURRENCY_VALUE', 'Wert:');
define('TEXT_INFO_CURRENCY_EXAMPLE', 'Beispiel:');
define('BUTTON_UPDATE_CURRENCY','Wechselkurs aktualisieren');
define('TEXT_INFO_INSERT_INTRO', 'Bitte geben Sie die neue W&auml;hrung mit allen relevanten Daten ein');
define('TEXT_INFO_DELETE_INTRO', 'Sind Sie sicher, dass Sie diese W&auml;hrung l&ouml;schen m&ouml;chten?');
define('TEXT_INFO_HEADING_NEW_CURRENCY', 'neue W&auml;hrung');
define('TEXT_INFO_HEADING_EDIT_CURRENCY', 'W&auml;hrung bearbeiten');
define('TEXT_INFO_HEADING_DELETE_CURRENCY', 'W&auml;hrung l&ouml;schen');
define('TEXT_INFO_SET_AS_DEFAULT', TEXT_SET_DEFAULT . ' (manuelles Aktualisieren der Wechselkurse erforderlich.)');
define('TEXT_INFO_CURRENCY_UPDATED', 'Der Wechselkurs %s (%s) wurde erfolgreich aktualisiert');

define('ERROR_REMOVE_DEFAULT_CURRENCY', 'Fehler: Die Standardw&auml;hrung darf nicht gel&ouml;scht werden. Bitte definieren Sie eine neue Standardw&auml;hrung und wiederholen Sie den Vorgang.');
define('ERROR_CURRENCY_INVALID', 'Fehler: Der Wechselkurs f&uuml;r %s (%s) wurde nicht aktualisiert. Ist dies ein g&uuml;ltiges W&auml;hrungsk&uuml;rzel?');
?>