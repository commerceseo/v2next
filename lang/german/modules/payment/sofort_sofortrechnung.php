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
 * $Id: sofort_sofortrechnung.php 420 2013-06-19 18:04:39Z akausch $
 */

//include language-constants used in all Multipay Projects
require_once 'sofort_general.php';

define('MODULE_PAYMENT_SOFORT_SR_CHECKOUT_CONDITIONS_WITH_LIGHTBOX', '<a href="https://documents.sofort.com/de/sr/privacy_de" target="_blank">Ich habe die Datenschutzhinweise gelesen.</a>');
define('MODULE_PAYMENT_SOFORT_SR_CHECKOUT_CONDITIONS', '
	<script type="text/javascript">
		function showSrConditions() {
			srOverlay = new sofortOverlay(jQuery(".srOverlay"), "callback/sofort/ressources/scripts/getContent.php", "https://documents.sofort.com/de/sr/privacy_de");
			srOverlay.trigger();
		}
	</script>
	<noscript>
		<a href="https://documents.sofort.com/de/sr/privacy_de" target="_blank">Ich habe die Datenschutzhinweise gelesen.</a>
	</noscript>
	<!-- comSeo-Ajax-Checkout-Bugfix: show also div, when buyer doesnt use JS -->
	<div>
		<a id="srNotice" href="javascript:void(0)" onclick="showSrConditions();">Ich habe die Datenschutzhinweise gelesen.</a>
	</div>
	<div style="display:none; z-index: 1001;filter: alpha(opacity=92);filter: progid :DXImageTransform.Microsoft.Alpha(opacity=92);-moz-opacity: .92;-khtml-opacity: 0.92;opacity: 0.92;background-color: black;position: fixed;top: 0px;left: 0px;width: 100%;height: 100%;text-align: center;vertical-align: middle;" class="srOverlay">
		<div class="loader" style="z-index: 1002;position: relative;background-color: #fff;top: 40px;overflow: scroll;padding: 4px;border-radius: 7px;-moz-border-radius: 7px;-webkit-border-radius: 7px;margin: auto;width: 620px;height: 400px;overflow: scroll; overflow-x: hidden;">
			<div class="closeButton" style="position: fixed; top: 54px; background: url(callback/sofort/ressources/images/close.png) right top no-repeat;cursor:pointer;height: 30px;width: 30px;"></div>
			<div class="content"></div>
		</div>
	</div>
');

define('MODULE_PAYMENT_SOFORT_SR_TEXT_DESCRIPTION_EXTRA',
	MODULE_PAYMENT_SOFORT_MULTIPAY_JS_LIBS.'
	<div id="srExtraDesc">
		<div class="content" style="display:none;"></div>
	</div>
	<script type="text/javascript">
		srOverlay = new sofortOverlay(jQuery("#srExtraDesc"), "'.DIR_WS_CATALOG.'callback/sofort/ressources/scripts/getContent.php", "https://documents.sofort.com/rbs/shopinfo/de");
	</script>
');

define('MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG_TEXT_TITLE_ADMIN', 'Rechnung by SOFORT <br /><img src="https://images.sofort.com/de/sr/logo_90x30.png"  alt="Logo Rechnung by SOFORT"/>');
define('MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG_TEXT_TITLE', 'Kauf auf Rechnung <br /><img src="https://images.sofort.com/de/sr/logo_90x30.png"  alt="Logo Rechnung by SOFORT"/>');
define('MODULE_PAYMENT_SOFORT_SR_TEXT_TITLE', 'Kauf auf Rechnung');
define('MODULE_PAYMENT_SOFORT_SR_LOGO_HTML', '<img src="https://images.sofort.com/de/sr/logo_90x30.png"  alt="Logo Rechnung by SOFORT"/>');
define('MODULE_PAYMENT_SOFORT_SR_TEXT_ERROR_MESSAGE', 'Die gewählte Zahlart ist leider nicht möglich oder wurde auf Kundenwunsch abgebrochen. Bitte wählen Sie eine andere Zahlweise.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_SR', 'Die gewählte Zahlart ist leider nicht möglich oder wurde auf Kundenwunsch abgebrochen. Bitte wählen Sie eine andere Zahlweise.');

define('MODULE_PAYMENT_SOFORT_MULTIPAY_SR_CHECKOUT_TEXT', '');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_CONFIRM_SR', 'Rechnung hier bestätigen:');
define('MODULE_PAYMENT_SOFORT_SR_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_SOFORT_SR_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
define('MODULE_PAYMENT_SOFORT_SR_STATUS_TITLE', 'sofort.de Modul aktivieren');
define('MODULE_PAYMENT_SOFORT_SR_STATUS_DESC', 'Aktiviert/deaktiviert das komplette Modul');
define('MODULE_PAYMENT_SOFORT_SR_TEXT_DESCRIPTION', 'Kauf auf Rechnung mit Zahlungsgarantie.');
define('MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
define('MODULE_PAYMENT_SOFORT_SR_ZONE_TITLE', MODULE_PAYMENT_SOFORT_MULTIPAY_ZONE_TITLE);
define('MODULE_PAYMENT_SOFORT_SR_ZONE_DESC', MODULE_PAYMENT_SOFORT_MULTIPAY_ZONE_DESC);

define('MODULE_PAYMENT_SOFORT_SR_PEN_CON_INV_STATUS_ID_TITLE', 'Unbestätigter Bestellstatus');
define('MODULE_PAYMENT_SOFORT_SR_PEN_CON_INV_STATUS_ID_DESC', 'Bestellstatus nach erfolgreicher Zahlung. Die Rechnung wurde noch nicht durch den Händler freigegeben.'); // (pending-confirm_invoice)
define('MODULE_PAYMENT_SOFORT_SR_LOS_CAN_STATUS_ID_TITLE', 'Bestellstatus bei kompletter Stornierung');
define('MODULE_PAYMENT_SOFORT_SR_LOS_CAN_STATUS_ID_DESC', 'Stornierter Bestellstatus<br />Bestellstatus nach einer vollen Stornierung der Rechnung.');  //(loss-canceled, loss-confirmation_period_expired)
define('MODULE_PAYMENT_SOFORT_SR_PEN_NOT_CRE_YET_STATUS_ID_TITLE', 'Bestätigter Bestellstatus');
define('MODULE_PAYMENT_SOFORT_SR_PEN_NOT_CRE_YET_STATUS_ID_DESC', 'Bestellstatus nach erfolgreicher und bestätigter Transaktion und Freigabe der Rechnung durch den Händler.'); //(pending-not_credited_yet)
define('MODULE_PAYMENT_SOFORT_SR_REF_REF_STATUS_ID_TITLE', 'Stornierung nach Bestätigung (Gutschrift)');
define('MODULE_PAYMENT_SOFORT_SR_REF_REF_STATUS_ID_DESC', 'Status für Bestellungen, die nach der Bestätigung vollständig storniert wurden (Gutschrift).'); // (refunded_refunded)
define('MODULE_PAYMENT_SOFORT_SR_TMP_COMMENT', 'Kauf auf Rechnung als Zahlungsart gewählt. Transaktion nicht abgeschlossen.');
define('MODULE_PAYMENT_SOFORT_SR_TMP_COMMENT_SELLER', 'Weiterleitung zu SOFORT - Bezahlung noch nicht erfolgt.');

define('MODULE_PAYMENT_SOFORT_SR_RECOMMENDED_PAYMENT_TITLE', 'Empfohlene Zahlungsweise');
define('MODULE_PAYMENT_SOFORT_SR_RECOMMENDED_PAYMENT_DESC', 'Diese Zahlart als "empfohlene Zahlungsart" markieren. Auf der Bezahlseite erfolgt ein Hinweis direkt hinter der Zahlungsart.');
define('MODULE_PAYMENT_SOFORT_SR_RECOMMENDED_PAYMENT_TEXT', '(Empfohlene Zahlungsweise)');

define('MODULE_PAYMENT_SOFORT_SR_INVOICE_CONFIRMED_HISTORY', 'Auftrag zur Bestätigung der Rechnung wurde an SOFORT gesendet. Bestätigung seitens SOFORT ausstehend.');
define('MODULE_PAYMENT_SOFORT_SR_INVOICE_CANCELED_HISTORY', 'Auftrag zur Stornierung der Rechnung wurde an SOFORT gesendet. Bestätigung seitens SOFORT ausstehend.');
define('MODULE_PAYMENT_SOFORT_SR_INVOICE_REFUNDED_HISTORY', 'Auftrag zur Gutschrift der Rechnung wurde an SOFORT gesendet. Bestätigung seitens SOFORT ausstehend.');

/////////////////////////////////////////////////
//////// Seller-Backend and callback.php ////////
/////////////////////////////////////////////////

define('MODULE_PAYMENT_SOFORT_SR_CONFIRM_INVOICE', 'Rechnung bestätigen');
define('MODULE_PAYMENT_SOFORT_SR_CANCEL_INVOICE', 'Rechnung stornieren');
define('MODULE_PAYMENT_SOFORT_SR_CANCEL_CONFIRMED_INVOICE', 'Rechnung gutschreiben');
define('MODULE_PAYMENT_SOFORT_SR_CANCEL_INVOICE_QUESTION', 'Sind Sie sicher, dass Sie die Rechnung wirklich stornieren wollen? Dieser Vorgang kann nicht rückgängig gemacht werden.');
define('MODULE_PAYMENT_SOFORT_SR_CANCEL_CONFIRMED_INVOICE_QUESTION', 'Sind Sie sicher, dass Sie die Rechnung wirklich gutschreiben wollen? Dieser Vorgang kann nicht rückgängig gemacht werden.');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_INVOICE_CANCELED_REFUNDED', 'Die Rechnung wurde storniert. Gutschrift erstellt.');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_INVOICE_CANCELED', 'Die Rechnung wurde storniert.');

define('MODULE_PAYMENT_SOFORT_SR_DOWNLOAD_INVOICE', 'Rechnung herunterladen');
define('MODULE_PAYMENT_SOFORT_SR_DOWNLOAD_INVOICE_HINT', 'Laden Sie hier das entsprechende Dokument (Rechnungsvorschau, Rechnung, Gutschrift) herunter.');
define('MODULE_PAYMENT_SOFORT_SR_DOWNLOAD_CREDIT_MEMO', 'Gutschrift herunterladen');
define('MODULE_PAYMENT_SOFORT_SR_DOWNLOAD_INVOICE_PREVIEW', 'Rechnungsvorschau herunterladen');

define('MODULE_PAYMENT_SOFORT_SR_EDIT_CART', 'Warenkorb anpassen');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_CART', 'Warenkorb speichern');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_CART_QUESTION', 'Wollen Sie den Warenkorb wirklich anpassen?');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_CART_HINT', 'Speichern Sie hier Ihre Änderungen am Warenkorb. Bei bereits bestätigten Rechnung führt ein mengenmäßig reduzierter sowie ein von der Rechnung gelöschter Artikel zu einer Gutschrift.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_DISCOUNTS_HINT', 'Sie können Rabatte oder Aufschläge anpassen. Aufschläge dürfen nicht erhöht werden und Rabatte keine Beträge größer Null erhalten. Der Gesamtbetrag der Rechnung darf durch die Anpassung nicht erhöht werden.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_DISCOUNTS_GTZERO_HINT', 'Rabatte dürfen keinen Betrag größer Null erhalten.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_QUANTITY', 'Menge anpassen');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_QUANTITY_HINT', 'Sie können die Anzahl der Artikel pro Position anpassen. Es dürfen lediglich Mengen reduziert, nicht jedoch hinzugefügt werden.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_QUANTITY_TOTAL_GTZERO', 'Die Anzahl des Artikels kann nicht reduziert werden, da die Gesamtsumme der Rechnung nicht negativ sein darf.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_QUANTITY_ZERO_HINT', 'Anzahl muss größer 0 sein. Zum Löschen markieren Sie den Artikel bitte am Ende der Zeile.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_PRICE', 'Preis anpassen');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_PRICE_HINT', 'Sie können den Preis der einzelnen Artikel pro Position anpassen. Preise können lediglich reduziert, nicht erhöht werden.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_PRICE_TOTAL_GTZERO', 'Der Preis kann nicht reduziert werden, da die Gesamtsumme der Rechnung nicht negativ sein darf.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_PRICE_AND_QUANTITY_HINT', 'Es können nicht gleichzeitig Preis und Menge angepasst werden.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_PRICE_AND_QUANTITY_NAN', 'Sie haben ungültige Zeichen eingegeben. Bei diesen Anpassungen sind nur Zahlen zulässig.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_VALUE_LTZERO_HINT', 'Wert darf nicht kleiner 0 sein.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_CONFIRMED_INVOICE', 'Bitte Kommentar eingeben');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_CONFIRMED_INVOICE_HINT', 'Bei Anpassung einer bereits bestätigten Rechnung muss eine entsprechende Begründung hinterlegt werden. Diese erscheint später auf der Gutschrift als Kommentar zum entsprechenden Artikel.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_SHIPPING_HINT', 'Sie können den Preis der Versandkosten anpassen. Der Preis kann lediglich reduziert, nicht erhöht werden.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_SHIPPING_TOTAL_GTZERO', 'Die Versandkosten können nicht reduziert werden, da die Gesamtsumme der Rechnung nicht negativ sein darf.');
define('MODULE_PAYMENT_SOFORT_SR_RECALCULATION', 'wird neu berechnet');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_FROM_INVOICE_TOTAL_GTZERO','Dieser Artikel kann nicht gelöscht werden, da die Gesamtsumme der Rechnung nicht negativ sein darf.');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_ARTICLE_FROM_INVOICE', 'Artikel entfernen');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_FROM_INVOICE', 'Position löschen');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_FROM_INVOICE_QUESTION', 'Sie möchten folgende Artikel wirklich löschen: %s ?');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_FROM_INVOICE_HINT', 'Markieren Sie Artikel um sie zu löschen. Bei einer bereits bestätigten Rechnung führt das Löschen eines Artikels zu einer Gutschrift.');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_LAST_ARTICLE_HINT', 'Durch das Reduzieren der Anzahl aller bzw. durch Entfernen des letzten Artikels wird die Rechnung komplett storniert.');
define('MODULE_PAYMENT_SOFORT_SR_SYNC_FAILED_SELLER', 'Diese Bestellung enthält Artikel, die Probleme bei der Syncronisation mit dem SOFORT-Servern erzeugen könnten. Bitte prüfen Sie bei nachträglichen Versand-, Rabatt- oder Artikeländerungen die auf der PDF-Rechnung von SOFORT hinterlegten Daten mit den im Shop hinterlegten, um einen fehlerhaften Versand zu vermeiden. Warenkorbänderungen sind ggf. nur im Anbietermenü von SOFORT durchführbar.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_GX_CUSTOMIZER_AFFECTED', 'Es handelt sich hierbei normalerweise nur um Artikel, die Funktionen des GX-Customizers nutzen.');

define('MODULE_PAYMENT_SOFORT_SR_PEN_CON_INV_BUYER', 'Bestellung erfolgreich übermittelt. Bestätigung noch nicht erfolgt.');
define('MODULE_PAYMENT_SOFORT_SR_PEN_CON_INV_SELLER', 'Bestellung erfolgreich abgeschlossen - Rechnung kann bestätigt werden - Ihre Transactions-ID:');
define('MODULE_PAYMENT_SOFORT_SR_LOS_CAN_BUYER', 'Bestellung storniert.');
define('MODULE_PAYMENT_SOFORT_SR_LOS_CAN_SELLER', '');
define('MODULE_PAYMENT_SOFORT_SR_PEN_NOT_CRE_YET_BUYER', 'Bestellung bestätigt und in Bearbeitung.');
define('MODULE_PAYMENT_SOFORT_SR_PEN_NOT_CRE_YET_SELLER', 'Die Rechnung wurde bestätigt und erstellt.');
define('MODULE_PAYMENT_SOFORT_SR_REF_REF_BUYER', 'Die Rechnung wurde gutgeschrieben.');
define('MODULE_PAYMENT_SOFORT_SR_REF_REF_SELLER', 'Die Rechnung wurde gutgeschrieben. Gutschrift wurde erstellt.');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_INVOICE_REANIMATED', 'Die Stornierung der Rechnung wurde rückgängig gemacht.');
define('MODULE_PAYMENT_SOFORT_SR_LOS_CON_PER_EXP_BUYER', 'Bestellung storniert.');
define('MODULE_PAYMENT_SOFORT_SR_LOS_CON_PER_EXP_SELLER', 'Die Bestellung wurde storniert. Bestätigungszeitraum abgelaufen.');

define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CURRENT_TOTAL', 'Aktueller Rechnungsbetrag:');

define('MODULE_PAYMENT_SOFORT_SR_INVOICE_CONFIRMED', 'Rechnung wurde bestätigt');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_TRANSACTION_ID', 'Transaktions-ID');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CANCELED_REFUNDED', 'Die Rechnung wurde storniert. Gutschrift erstellt. {{time}}');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CART_EDITED', 'Der Warenkorb wurde angepasst.');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CART_RESET', 'Der Warenkorb wurde zurückgesetzt.');

define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9000', 'Keine Rechnungs-Transaktion gefunden.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9001', 'Die Rechnung konnte nicht bestätigt werden.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9002', 'Die Übergebene Rechnungssumme übersteigt das Kreditlimit.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9003', 'Die Rechnung konnte nicht storniert werden.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9004', 'Die Anfrage enthielt ungültige Warenkorbpositionen.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9005', 'Der Warenkorb konnte nicht angepasst werden.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9006', 'Der Zugriff zur Schnittstelle ist 30 Tage nach Zahlungseingang nicht mehr möglich.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9007', 'Die Rechnung wurde bereits storniert.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9008', 'Der Betrag der übergebenen Mehrwertsteuer ist zu hoch.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9009', 'Die Beträge der übergeben Mehrwertsteuersätze der Artikel stehen in Konflikt zueinander.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9010', 'Die Anpassung des Warenkorbs ist nicht möglich.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9011', 'Es wurde kein Kommentar für die Anpassung des Warenkorbs übergeben.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9012', 'Es können keine Positionen zum Warenkorb hinzugefügt werden. Ebenso kann die Menge pro Rechnungsposition nicht heraufgesetzt werden. Beträge einzelner Positionen dürfen den Ursprungsbetrag nicht überschreiten.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9013', 'Es befinden sich ausschließlich nichtfakturierbare Artikel im Warenkorb.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9014', 'Die übergebene Rechnungsnummer wird bereits verwendet.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9015', 'Die übergebene Nummer der Gutschrift wird bereits verwendet.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9016', 'Die übergebene Bestellnummer wird bereits verwendet.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9017', 'Die Rechnung wurde bereits bestätigt.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9018', 'Es wurden keine Daten der Rechnung angepasst.');