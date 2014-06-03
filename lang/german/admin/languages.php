<?php
/*-----------------------------------------------------------------
* 	$Id: languages.php 420 2013-06-19 18:04:39Z akausch $
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




define('HEADING_TITLE', 'Sprachen');

define('TABLE_HEADING_LANGUAGE_NAME', 'Sprache im Shop');
define('TABLE_HEADING_LANGUAGE_CODE', 'Codierung');
define('TABLE_HEADING_LANGUAGE_STATUS','Status');
define('TABLE_HEADING_LANGUAGE_STATUS_ADMIN','Status Admin Eingabefelder');
define('TABLE_HEADING_ACTION', 'Aktion');
define('HEAD_EXTRA_LANGUAGES','Installieren zus&auml;tzlicher Sprachen');
define('BODY_EXTRA_LANGUAGES','Beachten Sie, dass beim l&ouml;schen der Sprache auch die Texte dieser Sprache aus der Datenbank entfernt werden!<br /> Sie k&ouml;nnen die Sprachen dennoch so oft installieren und l&ouml;schen wie Sie m&ouml;chten.<br /><br /><strong>Wichtig</strong>: Haben Sie die SEO-URLs aktiviert, wechseln Sie nach der Installation dort hin und starten Sie die Indexierung neu!');

define('TEXT_INFO_EDIT_INTRO', 'Bitte f&uuml;hren Sie alle notwendigen &Auml;nderungen durch');
define('TEXT_INFO_LANGUAGE_NAME', 'Name:');
define('TEXT_INFO_LANGUAGE_CODE', 'Codierung:');
define('TEXT_INFO_LANGUAGE_IMAGE', 'Symbol:');
define('TEXT_INFO_LANGUAGE_DIRECTORY', 'Verzeichnis:');
define('TEXT_INFO_LANGUAGE_SORT_ORDER', 'Sortierreihenfolge:');
define('TEXT_INFO_INSERT_INTRO', 'Bitte geben Sie die neue Sprache mit allen relevanten Daten ein');
define('TEXT_INFO_DELETE_INTRO', 'Sind Sie sicher, dass Sie die Sprache l&ouml;schen m&ouml;chten?');
define('TEXT_INFO_HEADING_NEW_LANGUAGE', 'Neue Sprache');
define('TEXT_INFO_HEADING_EDIT_LANGUAGE', 'Sprache bearbeiten');
define('TEXT_INFO_HEADING_DELETE_LANGUAGE', 'Sprache l&ouml;schen');
define('TEXT_INFO_LANGUAGE_CHARSET','Charset');
define('TEXT_INFO_LANGUAGE_CHARSET_INFO','meta-content:');

define('INFO_INDEX_URL_START','Bitte wechseln Sie nun zu <i><u>Module > cSEO Module > commerce:SEO URL </u></i>Modul, aktivieren Sie die Sprachabh&auml;ngigen URLs und indizieren Sie die Links neu.');
define('ERROR_REMOVE_DEFAULT_LANGUAGE', 'Fehler: Die Standardsprache darf nicht gel&ouml;scht werden. Bitte definieren Sie eine neue Standardsprache und wiederholen Sie den Vorgang.');
?>