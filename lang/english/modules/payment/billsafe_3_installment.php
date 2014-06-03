<?php
/* --------------------------------------------------------------
   billsafe_3_installment.php 2013-01-08 mabr
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once dirname(__FILE__).'/billsafe_3_base.php';

define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_TEXT_DESCRIPTION', 'Ratenkauf in Kooperation mit BillSAFE');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_TEXT_DESCRIPTION_LINK', '<a target="_new" style="text-decoration: underline;" href="https://client.billsafe.de/">BillSAFE H&auml;ndlerportal</a>');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_SYSTEM_REQUIREMENTS', 'Systemanforderungen');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_TEXT_TITLE', 'Ratenkauf in Kooperation mit BillSAFE');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_TEXT_INFO', 'Bezahlung mit BillSAFE');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_STATUS_TITLE', 'BillSAFE-Zahlungsmodul aktivieren');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_STATUS_DESC', 'M&ouml;chten Sie Zahlungen via BillSAFE akzeptieren?');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_ORDER_STATUS_ID_TITLE', 'Bestellstatus festlegen');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_ORDER_STATUS_ID_DESC', 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_TMPORDER_STATUS_ID_TITLE', 'Tempor&auml;ren Bestellstatus festlegen');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_TMPORDER_STATUS_ID_DESC', 'Bestellung w&auml;hrend der Eingabe der Zahlungsdaten auf diesen Status setzen');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_ZONE_TITLE', 'Zahlungszone');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_MERCHANT_ID_TITLE', 'Merchant ID');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_MERCHANT_ID_DESC', 'Ihre Merchant ID');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_MERCHANT_LICENSE_TITLE', 'Merchant License');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_MERCHANT_LICENSE_DESC', 'Ihre Merchant License');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_URL_IMAGE_TITLE', 'Logo-Bild');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_URL_IMAGE_DESC', 'URL einer Grafik-Datei, welche im Payment Gateway angezeigt werden soll. Die Grafik darf maximal folgende Abmessungen haben: H&ouml;he: 60 Pixel, Breite: 130 Pixel.');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_IMAGE_CODE_TITLE', 'Logo-Code');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_IMAGE_CODE_DESC', 'Von BillSAFE mitgeteilte Logo-URL-ID');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_PREVALIDATE_TITLE', 'Vorpr&uuml;fung ausf&uuml;hren');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_PREVALIDATE_DESC', 'Zahlart BillSAFE nur nach positiver Vorpr&uuml;fung des Kunden anzeigen');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_SANDBOX_TITLE', 'Sandbox-Modus');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_SANDBOX_DESC', 'Sandbox-Modus (Testmodus) aktivieren');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_ADDRESSES_MUST_MATCH', 'F&uuml;r den Kauf auf Rechnung mit BillSAFE m&uuml;ssen Versand- und Rechnungsadresse &uuml;bereinstimmen. Mit den angegebenen Daten ist eine Nutzung von BillSAFE nicht m&ouml;glich!');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_PREPAREORDER_FAILED', 'BillSAFE steht derzeit nicht zur Verfuegung');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_LAYER_TITLE', 'Payment-Layer');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_LAYER_DESC', 'Payment-Layer verwenden?');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_OK', 'OK');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_MISSING', 'FEHLT');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_MINORDER_TITLE', 'min. Warenkorbwert');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_MINORDER_DESC', 'min. Warenkorbwert');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_MAXORDER_TITLE', 'max. Warenkorbwert');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_MAXORDER_DESC', 'max. Warenkorbwert');
