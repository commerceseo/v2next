<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category  PayIntelligent
 * @package   PayIntelligent_ClickandBuy
 * @copyright (C) 2010 PayIntelligent GmbH  <http://www.payintelligent.de/>
 * @license   GPLv2
 */

define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_STATUS_TITLE', 'ClickandBuy Billing Agreement aktivieren');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_STATUS_DESC', 'Akzeptieren Sie ClickandBuy Billing Agreement?');

define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT', 'ClickandBuy Billing Agreement');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_TITLE', 'ClickandBuy Billing Agreement');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION', 'Test');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_HEAD', 'Sofort mit ClickandBuy alle relevanten Bezahlmethoden weltweit akzeptieren!');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_TEXT_1', 'Kostenlose Anmeldung, keine Grundgeb&uuml;hr, keine Fixkosten!!!');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_TEXT_2', 'Provision 1,9% + 0,35 EUR pro Transaktion');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_SUBHEAD', 'Jetzt bei ClickandBuy als xtCommerce H&auml;ndler registrieren!');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_INFO', 'Mehr Geld verdienen und mehr Umsatz machen!<br />Sicher, schnell und einfach Zahlungen empfangen!<br />Kostenlose Anmeldung, Keine Grundgeb&uuml;hr!<br />');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_ID_TITLE', 'Merchant ID');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_ID_DESC', 'Anbieter Accountnummer');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_PROJECT_ID_TITLE', 'Project ID');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_PROJECT_ID_DESC', 'Projektnummer');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_TITLE', 'Project Secret Key');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_DESC', 'Geheimer Schl&uuml;ssel des Projekts');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_MMS_TITLE', 'MMS Secret Key');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_MMS_DESC', 'Geheimer Schl&uuml;ssel zur Validierung von XML Events');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SANDBOX_TITLE', 'Sandbox');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SANDBOX_DESC', 'Sandbox aktivieren?');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_REGISTRATION_BUTTON', 'Zur ClickandBuy Händlerregistrierung');

define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DESCRIPTION_TITLE', 'Beschreibung');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DESCRIPTION_DESC', 'Beschreibung f&uuml;r das Modell der wiederkehrenden Zahlungen.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_NUMBER_LIMIT_TITLE', 'Anzahl Belastungen');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_NUMBER_LIMIT_DESC', 'Maximale Anzahl der Belastungen. positive Zahl oder kein Wert f&uuml;r kein Limit.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_AMOUNT_TITLE', 'Betrag');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_AMOUNT_DESC', 'Maximaler Betrag f&uuml;r die wiederkehrenden Zahlungen. Sofern kein Wert angegeben ist wird der maximale Betrag automatisch gleich dem Warenkorbpreis gesetzt.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CURRENCY_TITLE', 'W&auml;hrung');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CURRENCY_DESC', 'Gibt die W&auml;hrung f&uuml;r die wiederkehrenden Zahlungen an. Sofern kein Wert angegeben ist wird die W&auml;hrung des Warenkorbes verwendet.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DATE_LIMIT_TITLE', 'Ablaufdatum');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DATE_LIMIT_DESC', 'Definiert das Ablaufdatum, bis zu welchem eine wiederkehrende Zahlungen eingezogen werden darf (bis einschlie&szlig;lich 23:59:59 UTC des angegebenen Tages). Wird kein Wert angegeben endet die Autorisierung f&uuml;r wiederkehrende Zahlungen nicht automatisch. (jjjj-mm-dd)');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_REVOKABLE_TITLE', 'Widerruf durch Kunden');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_REVOKABLE_DESC', 'Bei nicht aktivierter Checkbox kann der Kunde eine erteilte Autorisierung nicht widerrufen. Autorisierungen k&ouml;nnen jedoch seitens ClickandBuy jederzeit widerrufen werden.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_INITIAL_AMOUNT_TITLE', 'Initial Betrag Null');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_INITIAL_AMOUNT_DESC', 'Legt fest, ob der Warenkorbpreis sofort &uuml;ber den ClickandBuy Zahlungsprozess eingezogen werden soll. Ist die Option aktiviert muss der Betrag manuell in der Administration &uuml;ber "Bestellungen verwalten" einzogen werden.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SPECIAL_CASE_TITLE', 'Spezialf&auml;lle');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SPECIAL_CASE_DESC', 'Fast Checkout<br />Erlaubt nach einer erteilten Autorisierung, dass der Kunde beim n&auml;chsten Kauf nicht erneut zu ClickandBuy geschickt wird. Hierbei handelt es sich um einen verk&uuml;rzten Kaufprozess (ideal f&uuml;r Stammkunden).<br /><br />Partial Delivery<br />Falls der Lagerbestand eines sich im Warenkorb befindlichen Produktes gleich null ist wird dieser Betrag nicht belastet. Autorisierte Betr&auml;ge k&ouml;nnen nach Pr&uuml;fung des Lagerbestandes nachtr&auml;glich &uuml;ber die Shopadministration manuell belastet werden.<br /><br />Hinweis: Der Anwendungsfall Partial Delivery besitzt eigene vorkonfigurierte Einstellungen und &uuml;berschreibt alle oben eventuell gesetzten Settings.');

define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ZONE_TITLE', 'Zahlungszone');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ORDER_STATUS_ID_TITLE', 'Bestellstatus setzen');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ORDER_STATUS_ID_DESC', 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ALLOWED_DESC', 'Geben Sie einzeln die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');

define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CHECKOUT_TEXT_INFO', 'Einfache und sichere Zahlung per Kreditkarte, Lastschrift,<br />Online&uuml;berweisung oder Kontoaufladung');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CHECKOUT_MORE_INFO_LINK_TITLE', 'Mehr Infos');

define('CLICKANDBUY_ERROR_MESSAGE_1', 'Fehgeschlagener shash in pi_clickandbuy_do_trans.php%20');
define('CLICKANDBUY_ERROR_MESSAGE_2', 'Bei der Bezahlung ist ein Fehler aufgetreten. Fehlermeldung: ');
define('CLICKANDBUY_ERROR_MESSAGE_3', 'Falls dieses Problem weiterhin besteht, kontaktieren Sie bitte unseren Support!');
define('CLICKANDBUY_ERROR_MESSAGE_4', 'Fehlgeschlagener shash in before_process.');
define('CLICKANDBUY_ERROR_MESSAGE_5', 'Handshake Fehler in before_process.');
define('CLICKANDBUY_ERROR_MESSAGE_6', 'Unbekannt');
define('CLICKANDBUY_ERROR_MESSAGE_7', 'Fehlgeschlagener Handshake in pi_clickandbuy_trans.php%20');
define('CLICKANDBUY_ERROR_MESSAGE_8', 'Fehlgeschlagener cabsHash-1 in pi_clickandbuy_trans.php%20');
define('CLICKANDBUY_ERROR_MESSAGE_9', 'Fehlgeschlagener cabsHash-2 in pi_clickandbuy_trans.php%20');

define('CLICKANDBUY_LANG_CODE', 'DE_de');
?>