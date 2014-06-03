<?php
/*-----------------------------------------------------------------
* 	$Id: paypal.php 659 2013-10-08 16:48:08Z akausch $
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



define('MODULE_PAYMENT_PAYPAL_TEXT_TITLE', 'PayPal');
define('MODULE_PAYMENT_PAYPAL_TEXT_INFO','<a href="https://www.paypal.com/de/webapps/mpp/paypal-popup" class="iframe"><img src="https://www.paypalobjects.com/webstatic/de_DE/i/de-pp-logo-150px.png" border="0" alt="PayPal Logo"></a>');
define('MODULE_PAYMENT_PAYPAL_TEXT_DESCRIPTION', 'Sie werden nach dem "Best&auml;tigen" zu PayPal geleitet um hier Ihre Bestellung zu bezahlen.<br />Danach gelangen Sie zur&uuml;ck in den Shop und erhalten Ihre Bestell-Best&auml;tigung.');
define('MODULE_PAYMENT_PAYPAL_ALLOWED_TITLE' , 'Erlaubte Zonen');
define('MODULE_PAYMENT_PAYPAL_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
define('MODULE_PAYMENT_PAYPAL_STATUS_TITLE' , 'PayPal Modul aktivieren');
define('MODULE_PAYMENT_PAYPAL_STATUS_DESC' , 'M&ouml;chten Sie Zahlungen per PayPal akzeptieren?');
define('MODULE_PAYMENT_PAYPAL_SORT_ORDER_TITLE' , 'Anzeigereihenfolge');
define('MODULE_PAYMENT_PAYPAL_SORT_ORDER_DESC' , 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt');
define('MODULE_PAYMENT_PAYPAL_ZONE_TITLE' , 'Zahlungszone');
define('MODULE_PAYMENT_PAYPAL_ZONE_DESC' , 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
?>