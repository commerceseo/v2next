<?php
/*-----------------------------------------------------------------
* 	$Id: eustandardtransfer.php 420 2013-06-19 18:04:39Z akausch $
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

define('MODULE_PAYMENT_EUTRANSFER_TEXT_TITLE', 'EU-Standard Bank Transfer');
define('MODULE_PAYMENT_EUSTANDARDTRANSFER_TEXT_TITLE', 'EU-Standard Bank Transfer');
define('MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION', '<br />Die billigste und einfachste Zahlungsmethode innerhalb der EU ist die &Uuml;berweisung mittels IBAN und BIC.' .
								   '<br />Bitte verwenden Sie folgende Daten f&uuml;r die &Uuml;berweisung des Gesamtbetrages:<br />' .
								   '<br />Name der Bank: ' . MODULE_PAYMENT_EUTRANSFER_BANKNAM .
								   '<br />Zweigstelle: ' . MODULE_PAYMENT_EUTRANSFER_BRANCH .
								   '<br />Kontoname: ' . MODULE_PAYMENT_EUTRANSFER_ACCNAM .
								   '<br />Kontonummer: ' . MODULE_PAYMENT_EUTRANSFER_ACCNUM .
								   '<br />IBAN: ' . MODULE_PAYMENT_EUTRANSFER_ACCIBAN .
								   '<br />BIC/SWIFT: ' . MODULE_PAYMENT_EUTRANSFER_BANKBIC .
								   '<br /><br />Die Ware wird ausgeliefert wenn der Betrag auf unserem Konto eingegangen ist.<br />');

define('MODULE_PAYMENT_EUTRANSFER_TEXT_INFO','&Uuml;berweisen Sie den Rechnungsbetrag auf unser Konto. Die Kontodaten erhalten Sie nach Bestellannahme per E-Mail');
define('MODULE_PAYMENT_EUTRANSFER_STATUS_TITLE','Bank Transfer erlauben');
define('MODULE_PAYMENT_EUTRANSFER_STATUS_DESC','Wollen Sie Zahlung per Banktransfer erlauben?');

define('MODULE_PAYMENT_EUTRANSFER_BRANCH_TITLE','Zweigstelle');
define('MODULE_PAYMENT_EUTRANSFER_BRANCH_DESC','Die Zweigstelle Ihrer Bank.');

define('MODULE_PAYMENT_EUTRANSFER_BANKNAM_TITLE','Bank Name');
define('MODULE_PAYMENT_EUTRANSFER_BANKNAM_DESC','Name der Bank');

define('MODULE_PAYMENT_EUTRANSFER_ACCNAM_TITLE','Inhaber');
define('MODULE_PAYMENT_EUTRANSFER_ACCNAM_DESC','Der Kontoinhaber.');

define('MODULE_PAYMENT_EUTRANSFER_ACCNUM_TITLE','Kontonummer');
define('MODULE_PAYMENT_EUTRANSFER_ACCNUM_DESC','Ihre Kontonummer.');

define('MODULE_PAYMENT_EUTRANSFER_ACCIBAN_TITLE','IBAN');
define('MODULE_PAYMENT_EUTRANSFER_ACCIBAN_DESC','Internationale Account ID.');

define('MODULE_PAYMENT_EUTRANSFER_BANKBIC_TITLE','BIC');
define('MODULE_PAYMENT_EUTRANSFER_BANKBIC_DESC','Internationale Bank ID.');

define('MODULE_PAYMENT_EUTRANSFER_SORT_ORDER_TITLE','Anzeige Reihenfolge.');
define('MODULE_PAYMENT_EUTRANSFER_SORT_ORDER_DESC','An welcher Stelle soll das Modul erscheinen.');

define('MODULE_PAYMENT_EUSTANDARDTRANSFER_ALLOWED_TITLE' , 'Erlaubte Zonen');
define('MODULE_PAYMENT_EUSTANDARDTRANSFER_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
