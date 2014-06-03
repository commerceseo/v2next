<?php
/*-----------------------------------------------------------------
* 	$Id: invoice.php 420 2013-06-19 18:04:39Z akausch $
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




define('MODULE_PAYMENT_INVOICE_TEXT_DESCRIPTION', 'Rechnung');
define('MODULE_PAYMENT_INVOICE_TEXT_TITLE', 'Rechnung');
define('MODULE_PAYMENT_INVOICE_TEXT_INFO','');
define('MODULE_PAYMENT_INVOICE_STATUS_TITLE' , 'Rechnungsmodul aktivieren');
define('MODULE_PAYMENT_INVOICE_STATUS_DESC' , 'M&ouml;chten Sie Zahlungen per Invoices akzeptieren?');
define('MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID_TITLE' , 'Bestellstatus festlegen');
define('MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID_DESC' , 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');
define('MODULE_PAYMENT_INVOICE_SORT_ORDER_TITLE' , 'Anzeigereihenfolge');
define('MODULE_PAYMENT_INVOICE_SORT_ORDER_DESC' , 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
define('MODULE_PAYMENT_INVOICE_ZONE_TITLE' , 'Zahlungszone');
define('MODULE_PAYMENT_INVOICE_ZONE_DESC' , 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
define('MODULE_PAYMENT_INVOICE_ALLOWED_TITLE' , 'Erlaubte Zonen');
define('MODULE_PAYMENT_INVOICE_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
define('MODULE_PAYMENT_INVOICE_MIN_ORDER_TITLE' , 'Notwendige Bestellungen');
define('MODULE_PAYMENT_INVOICE_MIN_ORDER_DESC' , 'Die Mindestanzahl an Bestellungen die ein Kunden haben muss damit die Option zur Verf&uuml;gung steht.');
define('MODULE_PAYMENT_INVOICE_MIN_AMOUNT_TITLE' , 'Mindestbestellwert in EUR');
define('MODULE_PAYMENT_INVOICE_MIN_AMOUNT_DESC' , 'Der Mindestbestellwert der Bestellung um mit diesem Modul zahlen zu k&ouml;nnen.');
define('MODULE_PAYMENT_INVOICE_MAX_AMOUNT_TITLE' , 'Maximalbestellwert in EUR');
define('MODULE_PAYMENT_INVOICE_MAX_AMOUNT_DESC' , 'Der Maximalbestellwert der Bestellung um mit diesem Modul zahlen zu k&ouml;nnen.');
?>