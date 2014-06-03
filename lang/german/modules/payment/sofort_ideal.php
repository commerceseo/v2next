<?php
/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2013-06-05 15:01:19 +0200 (Mi, 05 Jun 2013) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofort_ideal.php 420 2013-06-19 18:04:39Z akausch $
 */

//include language-constants for used in all Multipay Projects - NOTICE: iDEAL is not Multipay
require_once 'sofort_general.php';

define('MODULE_PAYMENT_SOFORT_IDEAL_TEXT_TITLE', 'iDEAL'); //needed by core
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_TITLE', 'iDEAL <br /><img src="https://images.sofort.com/de/ideal/logo_90x30.png" alt="Logo iDEAL"/>');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_TITLE_ADMIN', 'iDEAL <br /><img src="https://images.sofort.com/de/ideal/logo_90x30.png" alt="Logo iDEAL"/>');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_RECOMMENDED_PAYMENT_TEXT', '(Empfohlene Zahlungsweise)');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_DESCRIPTION', '<b>iDEAL</b><br />Sobald der Kunde diese Zahlungsart und seine Bank ausgewählt hat, wird er durch die SOFORT AG auf seine Bank weitergeleitet. Dort tätigt er seine Zahlung und wird danach wieder auf das Shopsystem zurückgeleitet. Bei erfolgreicher Zahlungsbestätigung findet durch die SOFORT AG ein sog. Callback auf das Shopsystem statt, der den Zahlungsstatus der Bestellung entsprechend ändert.<br />Bereitgestellt durch die SOFORT AG');

//all shopsystem-params
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_STATUS_TITLE', 'iDEAL - sofort.de Modul aktivieren');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_RECOMMENDED_PAYMENT_TITLE', 'Empfohlene Zahlungsweise');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_AUTH_TITLE', 'Konfigurationsschlüssel/API-Key testen');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_AUTH_DESC', "<script>function t(){k = document.getElementsByName(\"configuration[MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CONFIGURATION_KEY]\")[0].value;window.open(\"../callback/sofort/testAuth.php?k=\"+k);}</script><input type=\"button\" onclick=\"t()\" value=\"Test\" />");  //max 255 signs
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ZONE_TITLE' , MODULE_PAYMENT_SOFORT_MULTIPAY_ZONE_TITLE);
define('MODULE_PAYMENT_SOFORT_IDEAL_ALLOWED_TITLE' , 'Zahlungszone');
define('MODULE_PAYMENT_SOFORT_IDEAL_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TMP_STATUS_ID_TITLE' , 'Temporärer Bestellstatus');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ORDER_STATUS_ID_TITLE', 'Bestätigter Bestellstatus');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_LAT_SUC_AUT_STATUS_ID_TITLE', 'Automatische Stornierung mit Rückbuchung');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REF_COM_STATUS_ID_TITLE', 'Teilerstattung');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REF_REF_STATUS_ID_TITLE', 'Vollständige Erstattung');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CANCELED_ORDER_STATUS_ID_TITLE', 'Bestellstatus bei abgebrochener Zahlung');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_LOS_NOT_CRE_STATUS_ID_TITLE', 'Bestellstatus bei kompletter Stornierung');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CONFIGURATION_KEY_TITLE', 'Von SOFORT AG zugewiesener Konfigurationsschlüssel');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_PROJECT_PASSWORD_TITLE', 'Projekt-Passwort');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_NOTIFICATION_PASSWORD_TITLE', 'Benachrichtigungs-Passwort');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REASON_1_TITLE', 'Verwendungszweck 1');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REASON_2_TITLE', 'Verwendungszweck 2');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_IMAGE_TITLE', 'Logo+Text');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_STATUS_DESC', 'Aktiviert/deaktiviert das komplette Modul');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_RECOMMENDED_PAYMENT_DESC', 'iDEAL zur empfohlenen Zahlungsmethode machen');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ZONE_DESC', 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TMP_STATUS_ID_DESC', 'Bestellstatus für nicht abgeschlossene Transaktionen. Die Bestellung wurde erstellt aber die Transaktion von der SOFORT AG noch nicht bestätigt.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ORDER_STATUS_ID_DESC', 'Bestätigter Bestellstatus<br />Bestellstatus nach abgeschlossener Transaktion.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_LOS_NOT_CRE_STATUS_ID_DESC', 'Bestellstatus bei kompletter Stornierung.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CANCELED_ORDER_STATUS_ID_DESC', 'Bestellstatus bei Bestellungen, die während des Bezahlvorgangs abgebrochen wurden.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_LAT_SUC_AUT_STATUS_ID_DESC', 'Status für Bestellungen, bei denen der Geldeingang nach Ablauf des im SOFORT-Projekt festgelegten Timeouts festgestellt wurde und die aus diesem Grund automatisch storniert wurden. Die Rückbuchung auf das Käuferkonto wird ebenfalls automatisch angewiesen.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REF_COM_STATUS_ID_DESC', 'Status für Bestellungen, bei denen ein Teilbetrag an den Käufer zurückerstattet wurde.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REF_REF_STATUS_ID_DESC', 'Status für Bestellungen, bei denen der vollständige Betrag an den Käufer zurückerstattet wurde.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CONFIGURATION_KEY_DESC', 'Von SOFORT AG zugewiesener Konfigurationsschlüssel');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_PROJECT_PASSWORD_DESC', 'Von SOFORT AG zugewiesenes Projekt-Passwort');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_NOTIFICATION_PASSWORD_DESC', 'Von SOFORT AG zugewiesenes Benachrichtigungs-Passwort');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REASON_1_DESC', 'Verwendungszweck 1');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REASON_2_DESC', 'Verwendungszweck 2');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_IMAGE_DESC', 'Banner oder Text bei der Auswahl der Zahlungsoptionen');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CHECKOUT_TEXT', 'iDEAL.nl - Online-Überweisungen für den elektronischen Handel in den Niederlanden. Für die Bezahlung mit iDEAL benötigen Sie ein Konto bei einer der genannten Banken. Sie nehmen die Überweisung direkt bei Ihrer Bank vor. Dienstleistungen/Waren werden bei Verfügbarkeit SOFORT geliefert bzw. versendet!');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGEALT', 'iDEAL.nl - Online-Überweisungen für den elektronischen Handel in den Niederlanden. Für die Bezahlung mit iDEAL benötigen Sie ein Konto bei einer der genannten Banken. Sie nehmen die Überweisung direkt bei Ihrer Bank vor. Dienstleistungen/Waren werden bei Verfügbarkeit SOFORT geliefert bzw. versendet!');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TMP_COMMENT', 'iDEAL als Zahlungsart gewählt. Transaktion nicht abgeschlossen.');

define('MODULE_PAYMENT_SOFORT_IDEAL_PEN_NOT_CRE_YET_BUYER', 'Bestellung erfolgreich übermittelt. {{time}}');
define('MODULE_PAYMENT_SOFORT_IDEAL_REC_CRE_BUYER', '{{paymentMethodStr}} - Geld ist eingegangen. {{time}}');
define('MODULE_PAYMENT_SOFORT_IDEAL_LOS_NOT_CRE_YET_BUYER', 'Die Bezahlung wurde storniert.');
define('MODULE_PAYMENT_SOFORT_IDEAL_LAT_SUC_AUT_BUYER', 'Transaktion wurde automatisch storniert. Rückbuchung auf Käuferkonto wurde angewiesen.');
define('MODULE_PAYMENT_SOFORT_IDEAL_REF_COM_BUYER', 'Ein Teil des Betrages wird erstattet.');
define('MODULE_PAYMENT_SOFORT_IDEAL_REF_REF_BUYER', 'Rechnungsbetrag wird zurückerstattet. {{time}}');

//////////////////////////////////////////////////////////////////////////////////////////////

define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_10000', 'Bitte wählen Sie vor dem Absenden eine Bank aus!');

define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_TEXT', 'iDEAL.nl - Online-Überweisungen für den elektronischen Handel in den Niederlanden. Für die Bezahlung mit iDEAL benötigen Sie ein Konto bei einer der genannten Banken. Sie nehmen die Überweisung direkt bei Ihrer Bank vor. Dienstleistungen/Waren werden bei Verfügbarkeit SOFORT geliefert bzw. versendet!');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_ERROR_HEADING', 'Folgender Fehler wurde während des Prozesses gemeldet:');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ERROR_ALL_CODES', 'Fehler bei der Datenübertragung, wählen Sie eine andere Zahlart oder kontaktieren Sie den Shopbetreiber.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ERROR_DEFAULT', 'Fehler bei der Datenübertragung, wählen Sie eine andere Zahlart oder kontaktieren Sie den Shopbetreiber.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_IDEALSELECTED_PENDING' , 'iDEAL als Zahlungsart gewählt. Transaktion nicht abgeschlossen.');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_SELECTBOX_TITLE', 'Bitte wählen Sie Ihre Bank aus');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGE', '
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top"><a href="http://www.ideal.nl" target="_blank">{{image}}</a></td>
        <td style="padding-left: 20px;">{{text}}</td>
      </tr>
    </table>');
define('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CHECKOUT_CONFIRMATION', '
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="main">
      	<p>Nach Bestätigung der Bestellung werden Sie zum Zahlungssytem Ihrer gewählten Bank weitergeleitet und können dort die Online-Überweisung duchführen.</p><p>Hierfür benötigen Sie Ihre eBanking Zugangsdaten, d.h. Bankverbindung, Kontonummer, PIN und TAN. Mehr Informationen finden Sie hier: <a href=http://www.ideal.nl" target="_blank">iDEAL.nl</a></p>"
      </td>
    </tr>
  </table>');