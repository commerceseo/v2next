<?php
/*-----------------------------------------------------------------
* 	$Id: cod.php 1074 2014-05-27 08:40:22Z akausch $
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

define('MODULE_PAYMENT_TYPE_PERMISSION', 'cod');
define('MODULE_PAYMENT_COD_TEXT_TITLE', ' Nachnahme');
define('MODULE_PAYMENT_COD_TEXT_DESCRIPTION', ' Nachnahme');
define('MODULE_PAYMENT_COD_ZONE_TITLE', 'Zahlungszone');
define('MODULE_PAYMENT_COD_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
define('MODULE_PAYMENT_COD_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_COD_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
define('MODULE_PAYMENT_COD_STATUS_TITLE', 'Nachnahme Modul aktivieren');
define('MODULE_PAYMENT_COD_STATUS_DESC', 'M&ouml;chten Sie Zahlungen per Nachnahme akzeptieren?');
define('MODULE_PAYMENT_COD_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_COD_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
define('MODULE_PAYMENT_COD_ORDER_STATUS_ID_TITLE', 'Bestellstatus festlegen');
define('MODULE_PAYMENT_COD_ORDER_STATUS_ID_DESC', 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');
define('MODULE_PAYMENT_COD_HINWEIS_STATUS_TITLE', 'Zusatztext anzeigen?');
define('MODULE_PAYMENT_COD_HINWEIS_STATUS_DESC', 'Soll ein Zusatztext für die Nachnahme angezeigt werden?');
define('MODULE_PAYMENT_COD_HINWEIS_TEXT_TITLE', 'Zusatztext im Checkout');
define('MODULE_PAYMENT_COD_HINWEIS_TEXT_DESC', 'Geben Sie den Zusatztext hier ein, der im Checkout angezeigt werden soll.');
if (MODULE_PAYMENT_COD_HINWEIS_STATUS == 'True') {
	define('MODULE_PAYMENT_COD_TEXT_INFO', MODULE_PAYMENT_COD_HINWEIS_TEXT);
} else {
	define('MODULE_PAYMENT_COD_TEXT_INFO', '');
}