<?php
/* --------------------------------------------------------------
   CheckoutByAmazon PaymentModul V1.2.1
   checkout_amazon.php 2011-10-28

   Ruhrmedia GmbH & Co. KG
   http://www.RuhrMedia.de
   Copyright (c) 2011 Ruhrmedia GmbH & Co. KG

   alkim media
   http://www.alkim.de

   Released under the GNU General Public License
   --------------------------------------------------------------
*/
DEFINE ('BOX_CONFIGURATION_777', 'Amazon Payments Optionen');

DEFINE('MODULE_PAYMENT_RMAMAZON_STATUS_TITLE', 'Modul aktivieren?');
DEFINE('MODULE_PAYMENT_RMAMAZON_STATUS_DESC', '"True" = aktiv, "False" = inaktiv');
DEFINE('MODULE_PAYMENT_RMAMAZON_SORT_ORDER_TITLE', 'Reihenfolge');
DEFINE('MODULE_PAYMENT_RMAMAZON_SORT_ORDER_DESC', '');
DEFINE('MODULE_PAYMENT_RMAMAZON_TEXT_TITLE', 'AmazonPayments');
DEFINE('MODULE_PAYMENT_RMAMAZON_MERCHANTID_TITLE', 'Amazon H&auml;ndler ID');
DEFINE('MODULE_PAYMENT_RMAMAZON_MERCHANTID_DESC', 'Ihre Amazon H&auml;ndler ID (Merchant ID)');

DEFINE('MODULE_PAYMENT_RMAMAZON_MARKETPLACEID_TITLE', 'Amazon Marketplace ID');
DEFINE('MODULE_PAYMENT_RMAMAZON_MARKETPLACEID_DESC', 'Ihre Amazon Marketplace ID');
DEFINE('MODULE_PAYMENT_RMAMAZON_MERCHANTTOKEN_TITLE', 'Amazon Merchant Token');
DEFINE('MODULE_PAYMENT_RMAMAZON_MERCHANTTOKEN_DESC', 'Ihr Amazon Merchant Token. Ihr Seller Central Merchant Token finden Sie unter Einstellungen -> Händlertoken');

DEFINE('MODULE_PAYMENT_RMAMAZON_ACCESKEY_TITLE', 'Access ID');
DEFINE('MODULE_PAYMENT_RMAMAZON_ACCESKEY_DESC', 'Dient zur Kommunikation und Authentifizierung mit Amazon Payments');
DEFINE('MODULE_PAYMENT_RMAMAZON_SECRETKEY_TITLE', 'Secret Acces Key');
DEFINE('MODULE_PAYMENT_RMAMAZON_SECRETKEY_DESC', 'Dient zur Kommunikation und Authentifizierung mit Amazon Payments');

define('MODULE_PAYMENT_RMAMAZON_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_RMAMAZON_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');

DEFINE('MODULE_PAYMENT_RMAMAZON_MODE_TITLE', 'Betriebsmodus');
DEFINE('MODULE_PAYMENT_RMAMAZON_MODE_DESC', 'Live = "live" oder Testbetrieb = "sandbox"');

DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_NOTIFIED_TITLE', 'Bestellstatus f&uuml;r neue Bestellungen');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_NOTIFIED_DESC', 'Dieser Status wird gesetzt, wenn die Bestellung zu Amazon &uuml;bermittelt wird.');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_OK_TITLE', 'Bestellstatus f&uuml;r best&auml;tigte Bestellungen');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_OK_DESC', 'Dieser Status wird gesetzt, nachdem Amazon die Bestellung best&auml;tigt hat.');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_PAYED_TITLE', 'Bestellstatus f&uuml;r bezahlte Bestellungen');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_PAYED_DESC', 'Dieser Status wird gesetzt, sobald Amazon die Bestellung als bezahlt markiert.');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_SHIPPED_TITLE', 'Bestellstatus f&uuml;r bezahlte versendete Bestellungen');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_SHIPPED_DESC', 'Dieser Status wird gesetzt bzw. sollte gesetzt werden, sobald der Artikel verschickt wurde.');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_STORNO_TITLE', 'Bestellstatus f&uuml;r stornierte Bestellungen');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_STORNO_DESC', 'Dieser Status wird bzw. sollte gesetzt werden, wenn die Bestellung storniert wurde.');

DEFINE('MODULE_PAYMENT_RMAMAZON_SENDMAILS_TITLE', 'Benachrichtigungen aktivieren');
DEFINE('MODULE_PAYMENT_RMAMAZON_SENDMAILS_DESC', 'Wenn aktiviert, bekommen Sie mit jeder Statusänderung, die Amazon an den Shop sendet, eine E-Mail an die hinterlegte Adresse.');
DEFINE ('MODULE_PAYMENT_RMAMAZON_MAIL_TITLE', 'E-Mail Adresse');
DEFINE ('MODULE_PAYMENT_RMAMAZON_MAIL_DESC', 'E-Mail Adresse f&uuml;r Benachichtigungen');

DEFINE ('MODULE_PAYMENT_RMAMAZON_SENDUSERMAIL_TITLE','Zus&auml;tzliche Best&auml;tigungsmail des Shops senden?');
DEFINE ('MODULE_PAYMENT_RMAMAZON_SENDUSERMAIL_DESC','Amazon versendet eine eigene Best&auml;tigungsmail an den Kunden. Wenn Sie zus&auml;tzlich die Mail des Shopsystems versenden wollen, so aktivieren Sie diese Option. ACHTUNG: Nur bei Shopkunden m&ouml;glich.');

DEFINE ('MODULE_PAYMENT_RMAMAZON_ALLOW_GUESTS_TITLE','Gastbestellungen erlauben?');
DEFINE ('MODULE_PAYMENT_RMAMAZON_ALLOW_GUESTS_DESC','Wenn aktiviert, können Kunden auch per Amazon Payments bezahlen, wenn Sie kein Kundenkonto in Ihrem Shop angelegt haben.');
DEFINE ('MODULE_PAYMENT_RMAMAZON_SHOW_ORDERID_TITLE','Amazon Bestellnummer anzeigen?');
DEFINE ('MODULE_PAYMENT_RMAMAZON_SHOW_ORDERID_DESC','Wenn aktiviert, wird auf der Bestellbest&auml;tigungsseite Ihres Shops die Amazon-Bestellnummer angezeigt.');

?>