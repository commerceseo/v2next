<?php
/*
  $Id: whos_online.php 420 2013-06-19 18:04:39Z akausch $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Wer ist Online');

define('TABLE_HEADING_ONLINE', 'Online');
define('TABLE_HEADING_CUSTOMER_ID', 'ID');
define('TABLE_HEADING_FULL_NAME', 'Name');
define('TABLE_HEADING_IP_ADDRESS', 'IP Addresse');
define('TABLE_HEADING_ENTRY_TIME', 'Startzeit');
define('TABLE_HEADING_LAST_CLICK', 'Letzter Click');
define('TABLE_HEADING_LAST_PAGE_URL', 'Letzte URL');
define('TABLE_HEADING_ACTION', 'Aktion');
define('TABLE_HEADING_SHOPPING_CART', 'Einkaufswagen des Benutzers');
define('TEXT_SHOPPING_CART_SUBTOTAL', 'Zwischensumme');
define('TEXT_NUMBER_OF_CUSTOMERS', 'Zur Zeit sind %s Benutzer online');
define('TABLE_HEADING_HTTP_REFERER', 'Herkunfts URL');
define('TEXT_HTTP_REFERER_URL', 'HTTP Referer URL');
define('TEXT_HTTP_REFERER_FOUND', 'gefunden');
define('TEXT_HTTP_REFERER_NOT_FOUND', 'nicht gefunden');
define('TEXT_STATUS_ACTIVE_CART', 'Aktiv/Korb voll');
define('TEXT_STATUS_ACTIVE_NOCART', 'Aktiv/Korb leer');
define('TEXT_STATUS_INACTIVE_CART', 'Inaktiv/Korb voll');
define('TEXT_STATUS_INACTIVE_NOCART', 'Inaktiv/Korb leer');
define('TEXT_STATUS_NO_SESSION_BOT', 'keine Session/Bot?');
define('TABLE_HEADING_COUNTRY', 'Land');
define('TABLE_HEADING_RATE', 'Aktualisierungsrate:');
define('RATE_NEVER', 'Nie');
define('RATE_NEVER_3MIN', '3 Min.');
define('RATE_NEVER_2MIN', '2 Min.');
define('RATE_NEVER_60SEC', '60 Sek.');
define('RATE_NEVER_30SEC', '30 Sek.');

define('TRUST_IP_LOG', 'IP gesperrt');
define('TRUST_NAME_LOG', 'Name gesperrt');
define('ME', 'Ich!');
define('NOT_FOUND', 'nicht gefunden');

define('NO_REFERER', 'nicht gefunden');

define('HEADING_MONTH', 'Monatsstatistik');
define('TABLE_HEADING_MONTH', 'Besucher im Monat %s');
define('TABLE_HEADING_DAY', 'Tag');
define('TABLE_HEADING_COUNT', 'Anzahl');
define('TABLE_HEADING_MONTHLY_TOP', 'Top 10 Referer im %s');
define('TABLE_HEADING_REFERER_ULR', 'Referer URL');
define('TABLE_HEADING_REFERER_NR', 'Nr.');

define('HEADING_YEAR', 'Jahresstatistik');
define('TABLE_HEADING_YEAR', 'Besucher %s');
define('TABLE_HEADING_MONTH_OF_YEAR', 'Monat');
define('TABLE_HEADING_YEARLY_TOP', 'Top 10 Referer %s');

define('HEADING_TOTAL', 'Gesamtstatistik'); 
define('TABLE_HEADING_TOTAL', 'Besucher gesamt');
define('TABLE_HEADING_TOTAL_YEAR', 'Jahr');
define('TABLE_HEADING_TOTAL_TOP', 'Gesamt Top 10 Referer');

define('ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST', 'Fehler: Das Verzeichnis \'graphs\' ist nicht vorhanden! Bitte erstellen Sie ein Verzeichnis \'graphs\' im Verzeichnis \'images\'.');
define('ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE', 'Fehler: Das Verzeichnis \'graphs\' ist schreibgesch&uuml;tzt!');
?>