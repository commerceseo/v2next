<?php
/**
 * Barzahlen Payment Module (commerce:SEO)
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

// Backend Information
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TITLE', 'Barzahlen.de');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_DESCRIPTION', 'Barzahlen bietet Ihren Kunden die M&ouml;glichkeit, online bar zu bezahlen. Sie werden in Echtzeit &uuml;ber die Zahlung benachrichtigt und profitieren von voller Zahlungsgarantie und neuen Kundengruppen. Sehen Sie wie Barzahlen funktioniert: <a href="https://www.barzahlen.de/de/partner/funktionsweise" style="color: #63A924;" target="_blank">https://www.barzahlen.de/de/partner/funktionsweise</a><br><br>
<table>
  <tr>
    <td><img src="https://cdn.barzahlen.de/images/icons/icon_registrieren.png" alt="" style="max-width: 16px; max-height: 16px;"></td>
    <td><a href="https://partner.barzahlen.de/user/register/" style="color: #60AC30;" target="_blank">Noch nicht registriert?</a></td>
  </tr>
  <tr>
    <td><img src="https://cdn.barzahlen.de/images/icons/icon_handbuch.png" alt="" style="max-width: 16px; max-height: 16px;"></td>
    <td><a href="https://integration.barzahlen.de/de/shopsysteme/commerce-seo/nutzerhandbuch" style="color: #60AC30;" target="_blank">Handbuch zur Integration</a></td>
  </tr>
  <tr>
    <td><img src="https://cdn.barzahlen.de/images/icons/icon_conversion.png" alt="" style="max-width: 16px; max-height: 16px;"></td>
    <td><a href="https://integration.barzahlen.de/assets/downloads/Integrationsleitfaden Barzahlen.de.pdf" style="color: #60AC30;" target="_blank">Conversion Optimierung</a></td>
  </tr>
  <tr>
    <td><img src="https://cdn.barzahlen.de/images/icons/icon_email.png" alt="" style="max-width: 16px; max-height: 16px;"></td>
    <td><a href="mailto:integration@barzahlen.de" style="color: #60AC30;">integration@barzahlen.de</a></td>
  </tr>
  <tr>
    <td><img src="https://cdn.barzahlen.de/images/icons/icon_telephone.png" alt="" style="max-width: 16px; max-height: 16px;"></td>
    <td>+49 (0)30 / 346 46 16 - 15</td>
  </tr>
</table>
<br><hr>');
define('MODULE_PAYMENT_BARZAHLEN_NEW_VERSION',  'Version %1$s f&uuml;r Barzahlen.de-Plugin verf&uuml;gbar unter: <a href="%2$s" style="font-size: 1em; color: #333;" target="_blank">%2$s</a>');

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
define('MODULE_PAYMENT_BARZAHLEN_DEBUG_DESC', 'Aktivieren Sie erweitertes Logging um neben Fehlern die komplette Kommunikation zwischen Shop und Barzahlen zu loggen.');
define('MODULE_PAYMENT_BARZAHLEN_NEW_STATUS_TITLE', 'Status f&uuml;r unbezahlte Bestellungen');
define('MODULE_PAYMENT_BARZAHLEN_NEW_STATUS_DESC', 'Geben Sie den Status an, welcher unbezahlten Bestellungen zugewiesen werden soll.');
define('MODULE_PAYMENT_BARZAHLEN_PAID_STATUS_TITLE', 'Status f&uuml;r bezahlte Bestellungen');
define('MODULE_PAYMENT_BARZAHLEN_PAID_STATUS_DESC', 'Geben Sie den Status an, welcher bezahlten Bestellungen zugewiesen werden soll.');
define('MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS_TITLE', 'Status f&uuml;r abgelaufende Bestellungen');
define('MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS_DESC', 'Geben Sie den Status an, welcher abgelaufenen Bestellungen zugewiesen werden soll. (Wird Bestellungen bei einem ausstehenden Zahlschein dieser Status manuell zugewiesen, wird der Zahlschein zeitnah storniert.)');
define('MODULE_PAYMENT_BARZAHLEN_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_BARZAHLEN_SORT_ORDER_DESC', 'Reihenfolge der Anzeige in der Zahlungsauswahl. Kleinste Ziffer wird zuerst angezeigt.');

// Frontend Texts
define('MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_DESCRIPTION', '<br> <img id="barzahlen_logo" src="https://cdn.barzahlen.de/images/barzahlen_logo.png" alt=""> <br><br> <div id="barzahlen_description"><img id="barzahlen_special" src="https://cdn.barzahlen.de/images/barzahlen_special.png" alt="" style="float: right; margin-left: 10px; max-width: 180px; max-height: 180px;">Mit Abschluss der Bestellung bekommen Sie einen Zahlschein angezeigt, den Sie sich ausdrucken oder auf Ihr Handy schicken lassen k&ouml;nnen. Bezahlen Sie den Online-Einkauf mit Hilfe des Zahlscheins an der Kasse einer Barzahlen.de-Partnerfiliale.<br style="clear: both;"></div>');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_PARTNER', '<br> <strong>Bezahlen Sie bei:</strong>&nbsp;');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_ERROR', 'Transaktionsfehler');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_PAYMENT_ERROR', 'Es gab einen Fehler bei der Datenübertragung. Bitte versuchen Sie es erneut oder wählen Sie eine andere Zahlungsmethode.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_X_ATTEMPT_SUCCESS', 'Barzahlen: Zahlschein erfolgreich angefordert und versendet');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TRANSACTION_PAID', 'Barzahlen: Der Zahlschein wurde beim Offline-Partner bezahlt.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TRANSACTION_EXPIRED', 'Barzahlen: Der Zahlungszeitraum des Zahlscheins ist abgelaufen.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_PAYMENT_ATTEMPT_FAILED', 'Barzahlen: Es gab einen Fehler bei der Datenübertragung.');
