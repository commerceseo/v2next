<?php
/**
 * Barzahlen Payment Module (commerce:SEO)
 *
 * NOTICE OF LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @copyright   Copyright (c) 2013 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

// Backend Information
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TITLE', 'Barzahlen');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_DESCRIPTION', 'Barzahlen bietet Ihren Kunden die M&ouml;glichkeit, online bar zu bezahlen. Sie werden in Echtzeit &uuml;ber die Zahlung benachrichtigt und profitieren von voller Zahlungsgarantie und neuen Kundengruppen. Sehen Sie wie Barzahlen funktioniert: <a href="http://www.barzahlen.de/partner/funktionsweise" style="color: #63A924;" target="_blank">http://www.barzahlen.de/partner/funktionsweise</a><br/><br/>');
define('MODULE_PAYMENT_BARZAHLEN_NEW_VERSION',  'Barzahlen Update: %s! Verf&uuml;gbar auf <a href="http://www.barzahlen.de/partner/integration/shopsysteme/11/commerce-seo">Barzahlen.de</a>');

// Configuration Titles & Descriptions
define('MODULE_PAYMENT_BARZAHLEN_STATUS_TITLE', 'Barzahlen Modul aktivieren');
define('MODULE_PAYMENT_BARZAHLEN_STATUS_DESC', 'M&ouml;chten Sie Zahlungen &uuml;ber Barzahlen akzeptieren?');
define('MODULE_PAYMENT_BARZAHLEN_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_BARZAHLEN_ALLOWED_DESC', 'Geben Sie <strong>einzeln</strong> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
define('MODULE_PAYMENT_BARZAHLEN_SANDBOX_TITLE', 'Testmodus aktivieren');
define('MODULE_PAYMENT_BARZAHLEN_SANDBOX_DESC', 'Aktivieren Sie den Testmodus um Zahlungen &uuml;ber die Sandbox abzuwickeln.');
define('MODULE_PAYMENT_BARZAHLEN_SHOPID_TITLE', 'Shop ID');
define('MODULE_PAYMENT_BARZAHLEN_SHOPID_DESC', 'Ihre Barzahlen Shop ID (<a href="https://partner.barzahlen.de" style="color: #63A924;" target="_blank">https://partner.barzahlen.de</a>)');
define('MODULE_PAYMENT_BARZAHLEN_PAYMENTKEY_TITLE', 'Zahlungsschl&uuml;ssel');
define('MODULE_PAYMENT_BARZAHLEN_PAYMENTKEY_DESC', 'Ihr Barzahlen Zahlungssch&uuml;ssel (<a href="https://partner.barzahlen.de" style="color: #63A924;" target="_blank">https://partner.barzahlen.de</a>)');
define('MODULE_PAYMENT_BARZAHLEN_NOTIFICATIONKEY_TITLE', 'Benachrichtigungsschl&uuml;ssel');
define('MODULE_PAYMENT_BARZAHLEN_NOTIFICATIONKEY_DESC', 'Ihr Barzahlen Benachrichtigungsschl&uuml;ssel (<a href="https://partner.barzahlen.de" style="color: #63A924;" target="_blank">https://partner.barzahlen.de</a>)');
define('MODULE_PAYMENT_BARZAHLEN_MAXORDERTOTAL_TITLE', 'Maximale Bestellsumme');
define('MODULE_PAYMENT_BARZAHLEN_MAXORDERTOTAL_DESC', 'Welcher Warenwert darf h&ouml;chstens erreicht werden, damit Barzahlen als Zahlungsweise angeboten wird? (Max. 999.99 EUR)');
define('MODULE_PAYMENT_BARZAHLEN_DEBUG_TITLE', 'Erweitertes Logging');
define('MODULE_PAYMENT_BARZAHLEN_DEBUG_DESC', 'Aktivieren Sie Debugging f&uuml;r zus&auml;tzliches Logging.');
define('MODULE_PAYMENT_BARZAHLEN_NEW_STATUS_TITLE', 'Status f&uuml;r unbezahlte Bestellungen');
define('MODULE_PAYMENT_BARZAHLEN_NEW_STATUS_DESC', 'Geben Sie den Status an, welcher unbezahlten Bestellungen zugewiesen werden soll.');
define('MODULE_PAYMENT_BARZAHLEN_PAID_STATUS_TITLE', 'Status f&uuml;r bezahlte Bestellungen');
define('MODULE_PAYMENT_BARZAHLEN_PAID_STATUS_DESC', 'Geben Sie den Status an, welcher bezahlten Bestellungen zugewiesen werden soll.');
define('MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS_TITLE', 'Status f&uuml;r abgelaufende Bestellungen');
define('MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS_DESC', 'Geben Sie den Status an, welcher abgelaufenen Bestellungen zugewiesen werden soll.');
define('MODULE_PAYMENT_BARZAHLEN_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_BARZAHLEN_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');

// Frontend Texts
define('MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_DESCRIPTION', '<br/> {{image}} <br/><br/> <div>{{special}} Mit Abschluss der Bestellung bekommen Sie einen Zahlschein angezeigt, den Sie sich ausdrucken oder auf Ihr Handy schicken lassen k&ouml;nnen. Bezahlen Sie den Online-Einkauf mit Hilfe des Zahlscheins an der Kasse einer Barzahlen-Partnerfiliale.</div>');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_PARTNER', '<br/><br/> <strong>Bezahlen Sie bei:</strong>&nbsp;');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_SANDBOX', '<br/><br/> Der <strong>Sandbox Modus</strong> ist aktiv. Allen get&auml;tigten Zahlungen wird ein Test-Zahlschein zugewiesen. Dieser kann nicht von unseren Einzelhandelspartnern verarbeitet werden.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_ERROR', 'Transaktionsfehler');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_PAYMENT_ERROR', 'Es gab einen Fehler bei der Datenübertragung. Bitte versuchen Sie es erneut oder wählen Sie eine andere Zahlungsmethode.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_X_ATTEMPT_SUCCESS', 'Barzahlen: Zahlschein erfolgreich angefordert und versendet');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TRANSACTION_PAID', 'Barzahlen: Der Zahlschein wurde beim Offline-Partner bezahlt.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TRANSACTION_EXPIRED', 'Barzahlen: Der Zahlungszeitraum des Zahlscheins ist abgelaufen.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_PAYMENT_ATTEMPT_FAILED', 'Barzahlen: Es gab einen Fehler bei der Datenübertragung.');
