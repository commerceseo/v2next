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

define('CLICKANDBUY_ORDER_CLICKANDBUY', 'ClickandBuy');
define('CLICKANDBUY_ORDER_CLICKANDBUY_DETAILS', 'ClickandBuy Details');

define('CLICKANDBUY_ORDER_DETAILS', 'Details');
define('CLICKANDBUY_ORDER_DETAILS_OVERVIEW', '&Uuml;bersicht');
define('CLICKANDBUY_ORDER_DETAILS_TOTAL_AMOUNT', 'Gesamtbetrag');
define('CLICKANDBUY_ORDER_DETAILS_DEBITED', 'Belastet');
define('CLICKANDBUY_ORDER_DETAILS_REFUNDED', 'R&uuml;ckerstattet');
define('CLICKANDBUY_ORDER_DETAILS_CANCELLED', 'Storniert');
define('CLICKANDBUY_ORDER_DETAILS_REFUND', 'R&uuml;ckerstattung');
define('CLICKANDBUY_ORDER_DETAILS_REFUND_DESC', 'Erstatten Sie dem Kunden einen Teilbetrag zur&uuml;ck.');
define('CLICKANDBUY_ORDER_DETAILS_DATE_TIME', 'Datum');
define('CLICKANDBUY_ORDER_DETAILS_EXTERNALID', 'External-ID');
define('CLICKANDBUY_ORDER_DETAILS_STATUS', 'Status');
define('CLICKANDBUY_ORDER_DETAILS_AMOUNT', 'Betrag');
define('CLICKANDBUY_ORDER_DETAILS_DESCRIPTION', 'Beschreibung');
define('CLICKANDBUY_ORDER_DETAILS_CANCELLATION', 'Stornierung Transaktion');
define('CLICKANDBUY_ORDER_DETAILS_CANCELLATION_AUTHORIZATION', 'Stornierung Autorisierung');
define('CLICKANDBUY_ORDER_DETAILS_CANCELLATION_DESC', 'Stornieren Sie den gesamten Betrag.');
define('CLICKANDBUY_ORDER_DETAILS_CANCEL', 'Stornieren');
define('CLICKANDBUY_ORDER_DETAILS_CREDIT', 'Gutschrift');
define('CLICKANDBUY_ORDER_DETAILS_CREDIT_DESC', 'Gutschrift an eine E-Mailadresse senden.');
define('CLICKANDBUY_ORDER_DETAILS_CLICKANDBUY_STATUS', 'ClickandBuy Status');
define('CLICKANDBUY_ORDER_DETAILS_TRANSACTIONID', 'Transaktions-ID');
define('CLICKANDBUY_ORDER_DETAILS_TRANSACTION_STATUS', 'Transaktionsstatus');
define('CLICKANDBUY_ORDER_DETAILS_TRANSACTION_TYPE', 'Transaktionstyp');
define('CLICKANDBUY_ORDER_DETAILS_AUTHORIZATIONID', 'Autorisierungs-ID');
define('CLICKANDBUY_ORDER_DETAILS_AUTHORIZATION_STATUS', 'Autorisierungs-Status');
define('CLICKANDBUY_ORDER_DETAILS_ERROR_CODE', 'Fehler Code');
define('CLICKANDBUY_ORDER_DETAILS_ERROR_DESC', 'Fehler Beschreibung');
define('CLICKANDBUY_ORDER_DETAILS_ERROR_REASON', 'Ihre Anfrage konnte augrund eines technischen Problems nicht erfolgreich durchgef&uuml;hrt werden.');
define('CLICKANDBUY_ORDER_DETAILS_MMS', 'Merchant Messaging Service (MMS)');
define('CLICKANDBUY_ORDER_DETAILS_MMS_SHOW', 'Zeige MMS Events');
define('CLICKANDBUY_ORDER_DETAILS_RECURRING', 'Billing Agreement Payments');
define('CLICKANDBUY_ORDER_DETAILS_AUTHORIZATION_AMOUNT', 'Autorisierter Betrag');
define('CLICKANDBUY_ORDER_DETAILS_RECURRING_DESC', '&Uuml;ber diese Bereich k&ouml;nnen Sie die Abrechnung von<br />Transaktionen durchf&uuml;hren, f&uuml;r welche eine Billing<br />Agreement Autorisierung erteilt wurde.');
define('CLICKANDBUY_ORDER_DETAILS_DEBIT_TRANSACTION', 'Transaktion Abrechnen');
define('CLICKANDBUY_ORDER_DETAILS_SHOW_TRANSACTIONS', 'Zeige alle Transaktionen');
define('CLICKANDBUY_ORDER_DETAILS_BACK', 'Abbrechen');

define('CLICKANDBUY_ORDER_REFUND_REFUND_NOW', 'R&uuml;ckerstattung jetzt erteilen');
define('CLICKANDBUY_ORDER_REFUND_SUCCESSFUL', 'R&uuml;ckerstattung war erfolgreich');
define('CLICKANDBUY_ORDER_REFUND_ERROR', 'R&uuml;ckerstattung konnte nicht ausgef&uuml;hrt werden');
define('CLICKANDBUY_ORDER_REFUND_ERROR_MESSAGE_1', 'Transaktion wurde storniert');
define('CLICKANDBUY_ORDER_REFUND_ERROR_MESSAGE_2', 'Ung&uuml;ltiger Betrag');


define('CLICKANDBUY_ORDER_CANCEL_CANCEL_NOW', 'Transaktion jetzt Stornieren');
define('CLICKANDBUY_ORDER_CANCEL_SUCCESSFUL', 'Stornierung war erfolgreich');
define('CLICKANDBUY_ORDER_CANCEL_AUTHORIZE_SUCCESSFUL', 'Stornierung der Authorisierung war erfolgreich');
define('CLICKANDBUY_ORDER_CANCEL_ERROR', 'Stornierung konnte nicht ausgef&uuml;hrt werden');
define('CLICKANDBUY_ORDER_CANCEL_IS_CANCELLED', 'Transaktion ist schon storniert');

define('CLICKANDBUY_ORDER_CREDIT_SUCCESSFUL', 'Gutschrift war erfolgreich');
define('CLICKANDBUY_ORDER_CREDIT_ERROR', 'We could not process your credit');
define('CLICKANDBUY_ORDER_CREDIT_EMAIL', 'ClickandBuy E-Mail');
define('CLICKANDBUY_ORDER_CREDIT_CREDIT_NOW', 'Gutschrift jetzt erteilen');

define('CLICKANDBUY_ORDER_NO_ENTRIES', 'Keine Eintr&auml;ge vorhanden.');

define('CLICKANDBUY_ORDER_MMS', 'MMS');
define('CLICKANDBUY_ORDER_MMS_EVENTID', 'Event ID');
define('CLICKANDBUY_ORDER_MMS_STATE_OLD', 'Alter Status');
define('CLICKANDBUY_ORDER_MMS_STATE_NEW', 'Neuer Status');
define('CLICKANDBUY_ORDER_MMS_XML', 'XML');

define('CLICKANDBUY_ORDER_RECURRING', 'Billing Agreement Payments');
define('CLICKANDBUY_ORDER_RECURRING_CURRENCY', 'W&auml;hrung');
define('CLICKANDBUY_ORDER_RECURRING_DEBIT_SUCCESSFUL', 'Belastung war erfolgreich');
define('CLICKANDBUY_ORDER_RECURRING_DEBIT_ERROR', 'Belastung konnte nicht ausgef&uuml;hrt werden');
define('CLICKANDBUY_ORDER_RECURRING_DEBIT', 'Transaktion Abrechnen');
define('CLICKANDBUY_ORDER_RECURRING_AUTHORIZATION_AMOUNT', 'Autorisierter Betrag');
define('CLICKANDBUY_ORDER_RECURRING_DEBIT_NOT', 'Betrag jetzt belasten');

define('CLICKANDBUY_ORDER_RECURRING_', '');
define('CLICKANDBUY_ORDER_RECURRING_', '');
define('CLICKANDBUY_ORDER_RECURRING_', '');
define('CLICKANDBUY_ORDER_RECURRING_', '');


?>