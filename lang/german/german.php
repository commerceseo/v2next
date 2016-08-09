<?php
/*-----------------------------------------------------------------
* 	$Id: german.php 1469 2015-07-22 20:30:54Z akausch $
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

define('TITLE', STORE_NAME);
define('HEADER_TITLE_TOP', 'Startseite');
define('HEADER_TITLE_CATALOG', 'Katalog');
define('TEXT_CLOSE_WINDOW_NO_JS', 'Ihr Browser kann kein Javascript. Bitte schliessen Sie das Fenster selbst.');

define('HTML_PARAMS','dir="ltr" xml:lang="de" lang="de"');

@setlocale(LC_TIME, 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de-DE', 'de', 'ge', 'de_DE.UTF-8', 'German');

define('DATE_FORMAT_SHORT', '%d.%m.%Y');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%A, %d. %B %Y'); // this is used for strftime()
define('DATE_FORMAT', 'd.m.Y');  // this is used for strftime()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');
define('DOB_FORMAT_STRING', 'tt.mm.jjjj');

$monate = array(
   1=>"Januar",
   2=>"Februar",
   3=>"März",
   4=>"April",
   5=>"Mai",
   6=>"Juni",
   7=>"Juli",
   8=>"August",
   9=>"September",
   10=>"Oktober",
   11=>"November",
   12=>"Dezember");
   
if (file_exists(DIR_WS_INCLUDES . 'addons/lang_german_addon.php')) {
	include (DIR_WS_INCLUDES . 'addons/lang_german_addon.php');
}

define('PAGE_BREAK','--Seite-');
define('PREVNEXT_TITLE_LAST_PAGE','zur letzten Seite springen');
define('PREVNEXT_TITLE_FIRST_PAGE','zur ersten Seite springen');

define('BROWSER_TEST','<p style="border: 1px solid #F00;background-color: #FFE8E8;color: #F00;padding: 10px;margin-bottom: 20px;"> Sie nutzen den Internet Explorer 6 oder noch älter. Dieser Browser ist viel zu alt und hat erhebliche Sicherheitslücken! Daher unterstützen wir diese Browser nicht mehr.<br /> Installieren Sie bitte umgehend einen neueren, sicheren Browser wie <a rel="nofollow" target="_blank" href="http://www.mozilla-europe.org/de/firefox/">Mozilla Firefox</a> oder <a rel="nofollow" target="_blank" href="http://www.microsoft.com/windows/Internet-explorer/download-ie.aspx">Microsoft Internet Explorer 8</a>.</p>');
	
function xtc_date_raw($date, $reverse = false) {
	if($reverse)
		return substr($date, 0, 2) . substr($date, 3, 2) . substr($date, 6, 4);
	else
		return substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);
}

define('LANGUAGE_CURRENCY', 'EUR');

define('MALE', ' Herr');
define('FEMALE', ' Frau');

/**
	Product Listings Header
**/
define('ALSO_PURCHASED','Diese Produkte wurden ebenfalls gekauft');
define('CROSS_SELLING','Dazu passende Produkte');
define('REVERSE_CROSS_SELLING','Dieses Produkt ist z.B. kompatibel zu');
define('SPECIALS','Unsere Sonderangebote in der &Uuml;bersicht');
define('TAGCLOUD','Folgende Produkte stimmen mit diesem Tag überein');
define('RANDOM_PRODUCTS','zufällige Produkte');
define('NEW_PRODUCTS','Neue Produkte in dieser Kategorie');
define('NEW_PRODUCTS_DEFAULT','Unsere neuen Produkte');
define('NEW_PRODUCTS_OVERVIEW','Alle neuen Produkte in der &Uuml;bersicht');
define('UPCOMING_PRODUCT','Bald erscheinende Produkte');
define('HISTORY_PRODUCT','Zuletzt aufgerufene Artikel');

/**
 BOX TEXT
**/

// text for gift voucher redeeming
define('IMAGE_REDEEM_GIFT','Einlösen!');

define('BOX_TITLE_STATISTICS','Statistik:');
define('BOX_ENTRY_CUSTOMERS','Kunden');
define('BOX_ENTRY_PRODUCTS','Artikel');
define('BOX_ENTRY_REVIEWS','Bewertungen');
define('BOX_EMAIL_VALUE','E-Mail-Adresse');

define('TEXT_VALIDATING','Nicht bestätigt');

// manufacturer box text
define('BOX_MANUFACTURER_INFO_HOMEPAGE', '%s Homepage');
define('BOX_MANUFACTURER_INFO_OTHER_PRODUCTS', 'Mehr Artikel');

define('BOX_HEADING_ADD_PRODUCT_ID','In den Korb legen');

define('BOX_HEADING_SEARCH','Neue Suche starten');
define('BOX_LOGINBOX_STATUS','Kundengruppe:');
define('BOX_LOGINBOX_DISCOUNT','Artikelrabatt');
define('BOX_LOGINBOX_DISCOUNT_TEXT','Rabatt');
define('BOX_LOGINBOX_DISCOUNT_OT','');

// reviews box text in includes/boxes/reviews.php
define('BOX_REVIEWS_WRITE_REVIEW', 'Bewerten Sie diesen Artikel!');
define('BOX_REVIEWS_TEXT_OF_5_STARS', '%s von 5 Sternen!');
define('TEXT_OF_5_STARS', '%s von 5 Sternen!');

// pull down default text
define('PULL_DOWN_DEFAULT', 'Bitte wählen');

// javascript messages
define('JS_ERROR', 'Notwendige Angaben fehlen! Bitte richtig ausfüllen.\n\n');

define('JS_REVIEW_TEXT', '* Der Text muss aus mindestens ' . REVIEW_TEXT_MIN_LENGTH . ' Buchstaben bestehen.\n\n');
define('JS_REVIEW_RATING', '* Geben Sie Ihre Bewertung ein.\n\n');
define('JS_ERROR_NO_PAYMENT_MODULE_SELECTED', '* Bitte wählen Sie eine Zahlungsweise für Ihre Bestellung.\n');
define('JS_ERROR_SUBMITTED', 'Diese Seite wurde bereits bestätigt. Klicken Sie bitte OK und warten bis der Prozess durchgeführt wurde.');
define('ERROR_NO_PAYMENT_MODULE_SELECTED', '* Bitte wählen Sie eine Zahlungsweise für Ihre Bestellung.');

/**
 ACCOUNT FORMS
**/

define('ENTRY_COMPANY_ERROR', '');
define('ENTRY_COMPANY_TEXT', '');
define('ENTRY_GENDER_ERROR', 'Bitte wählen Sie Ihre Anrede aus.');
define('ENTRY_GENDER_TEXT', '*');
define('ENTRY_FIRST_NAME_ERROR', 'Ihr Vorname muss aus mindestens ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_FIRST_NAME_TEXT', '*');
define('ENTRY_LAST_NAME_ERROR', 'Ihr Nachname muss aus mindestens ' . ENTRY_LAST_NAME_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_LAST_NAME_TEXT', '*');
define('ENTRY_DATE_OF_BIRTH_ERROR', 'Ihr Geburtsdatum muss im Format TT.MM.JJJJ (zB. 21.05.1970) eingeben werden');
define('ENTRY_DATE_OF_BIRTH_TEXT', '* (z.B. 21.05.1970)');
define('ENTRY_EMAIL_ADDRESS_ERROR', 'Ihre E-Mail-Adresse muss aus mindestens ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', 'Ihre eingegebene E-Mail-Adresse ist fehlerhaft - bitte überprüfen Sie diese.');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', 'Ihre eingegebene E-Mail-Adresse existiert bereits - bitte überprüfen Sie diese.');
define('ENTRY_EMAIL_ADDRESS_TEXT', '*');
define('ENTRY_STREET_ADDRESS_ERROR', 'Strasse/Nr. muss aus mindestens ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_STREET_ADDRESS_TEXT', '*');
define('ENTRY_SUBURB_TEXT', '');
define('ENTRY_POST_CODE_ERROR', 'Ihre Postleitzahl muss aus mindestens ' . ENTRY_POSTCODE_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_POST_CODE_TEXT', '*');
define('ENTRY_CITY_ERROR', 'Ort muss aus mindestens ' . ENTRY_CITY_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_CITY_TEXT', '*');
define('ENTRY_STATE_ERROR', 'Ihr Bundesland muss aus mindestens ' . ENTRY_STATE_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_STATE_ERROR_SELECT', 'Bitte wählen Sie ihr Bundesland aus der Liste aus.');
define('ENTRY_STATE_TEXT', '*');
define('ENTRY_COUNTRY_ERROR', 'Bitte wählen Sie ihr Land aus der Liste aus.');
define('ENTRY_COUNTRY_TEXT', '*');
define('ENTRY_TELEPHONE_NUMBER_ERROR', 'Ihre Telefonnummer muss aus mindestens ' . ENTRY_TELEPHONE_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '*');
define('ENTRY_FAX_NUMBER_TEXT', '');
define('ENTRY_NEWSLETTER_TEXT', '');
define('ENTRY_PASSWORD_ERROR', 'Ihr Passwort muss aus mindestens ' . ENTRY_PASSWORD_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_PASSWORD_ERROR_NOT_MATCHING', 'Ihre Passwörter stimmen nicht überein.');
define('ENTRY_PASSWORD_TEXT', '*');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '*');
define('ENTRY_PASSWORD_CURRENT_TEXT', '*');
define('ENTRY_PASSWORD_CURRENT_ERROR', 'Ihr Passwort muss aus mindestens ' . ENTRY_PASSWORD_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_PASSWORD_NEW_TEXT', '*');
define('ENTRY_PASSWORD_NEW_ERROR', 'Ihr neues Passwort muss aus mindestens ' . ENTRY_PASSWORD_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING', 'Ihre Passwörter stimmen nicht überein.');
define('ERROR_DATENSG_NOT_ACCEPTED', 'Sofern Sie die Kenntnisnahme unserer Informationen zu den Datenschutzerklärung nicht bestätigen, können wir Ihren Account nicht einrichten!');

/**
	RESTULTPAGES
**/

define('TEXT_RESULT_PAGE', 'Seiten:');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Zeige <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Produkten)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'Zeige <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Bestellungen)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Zeige <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Bewertungen)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW', 'Zeige <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> neuen Produkten)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'Zeige <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Angeboten)');

/**
	SITE NAVIGATION
**/

define('PREVNEXT_TITLE_PREVIOUS_PAGE', 'vorherige Seite');
define('PREVNEXT_TITLE_NEXT_PAGE', 'nächste Seite');
define('PREVNEXT_TITLE_PAGE_NO', 'Seite %d');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', 'Vorhergehende %d Seiten');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', 'Nächste %d Seiten');

/**
	PRODUCT NAVIGATION
**/

define('PREVNEXT_BUTTON_PREV', '&lt;&lt;&nbsp;vorherige');
define('PREVNEXT_BUTTON_NEXT', 'nächste&nbsp;&gt;&gt;');


/**
	GREETINGS
**/

// define('TEXT_GREETING_PERSONAL', 'Schön, dass Sie wieder da sind, <span class="greetUser">%s!</span> Möchten Sie sich unsere <a class="greeting" href="%s">neuen Artikel</a> ansehen?');
// define('TEXT_GREETING_PERSONAL_RELOGON', '<small>Wenn Sie nicht %s sind, melden Sie sich bitte <a class="greeting" href="%s">hier</a> mit Ihren Anmeldedaten an.</small>');
// define('TEXT_GREETING_GUEST', 'Herzlich Willkommen <span class="greetUser">Gast!</span> Möchten Sie sich <a class="greeting" href="%s">anmelden</a>? Oder wollen Sie ein <a class="greeting" href="%s">Kundenkonto</a> eröffnen?');
define('TEXT_GREETING_PERSONAL', '<div class="greeting">Eingeloggt als: <b class="greetUser">%s</b></div>');
define('TEXT_GREETING_PERSONAL_RELOGON', '<small>Wenn Sie nicht %s sind, melden Sie sich bitte <a class="greetinglink" href="%s">hier</a> mit Ihren Anmeldedaten an.</small>');
define('TEXT_GREETING_GUEST', 'Möchten Sie sich <a class="deepblue" href="%s">anmelden</a>?');

define('TEXT_SORT_PRODUCTS', 'Sortierung der Artikel ist ');
define('TEXT_DESCENDINGLY', 'absteigend');
define('TEXT_ASCENDINGLY', 'aufsteigend');
define('TEXT_BY', ' nach ');

define('TEXT_REVIEW_BY', 'von %s');
define('TEXT_REVIEW_WORD_COUNT', '%s Worte');
define('TEXT_REVIEW_RATING', 'Bewertung: %s [%s]');
define('TEXT_REVIEW_DATE_ADDED', 'Hinzugefügt am: %s');
define('TEXT_REVIEW_SUCCESS_MSG', 'Ihr Eintrag wurde entgegen genommen und wird nun geprüft und freigeschaltet.');
define('TEXT_NO_REVIEWS', 'Es liegen noch keine Bewertungen vor.');
define('TEXT_NO_NEW_PRODUCTS', 'Zur Zeit gibt es keine neuen Artikel.');
define('TEXT_UNKNOWN_TAX_RATE', 'Unbekannter Steuersatz');

/**
	WARNINGS
**/

define('WARNING_CONFIG_FILE_WRITEABLE', 'Warnung: Commerce:SEO kann in die Konfigurationsdatei schreiben: ' . dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/includes/configure.php. Das stellt ein mögliches Sicherheitsrisiko dar - bitte korrigieren Sie die Benutzerberechtigungen zu dieser Datei!');
define('WARNING_SESSION_DIRECTORY_NON_EXISTENT', 'Warnung: Das Verzeichnis für die Sessions existiert nicht: ' . xtc_session_save_path() . '. Die Sessions werden nicht funktionieren bis das Verzeichnis erstellt wurde!');
define('WARNING_SESSION_DIRECTORY_NOT_WRITEABLE', 'Warnung: Commerce:SEO kann nicht in das Sessions Verzeichnis schreiben: ' . xtc_session_save_path() . '. Die Sessions werden nicht funktionieren bis die richtigen Benutzerberechtigungen gesetzt wurden!');
define('WARNING_SESSION_AUTO_START', 'Warnung: session.auto_start ist aktiviert (enabled) - Bitte deaktivieren (disabled) Sie dieses PHP Feature in der php.ini und starten Sie den WEB-Server neu!');
define('WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT', 'Warnung: Das Verzeichnis für den Artikel Download existiert nicht: ' . DIR_FS_DOWNLOAD . '. Diese Funktion wird nicht funktionieren bis das Verzeichnis erstellt wurde!');

define('SUCCESS_ACCOUNT_UPDATED', 'Ihr Konto wurde erfolgreich aktualisiert.');
define('SUCCESS_PASSWORD_UPDATED', 'Ihr Passwort wurde erfolgreich geändert!');
define('ERROR_CURRENT_PASSWORD_NOT_MATCHING', 'Das eingegebene Passwort stimmt nicht mit dem gespeichertem Passwort überein. Bitte versuchen Sie es noch einmal.');
define('TEXT_MAXIMUM_ENTRIES', 'Hinweis: Ihnen stehen %s Adressbucheinträge zur Verfügung!');
define('SUCCESS_ADDRESS_BOOK_ENTRY_DELETED', 'Der ausgewählte Eintrag wurde erfolgreich gelöscht.');
define('SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED', 'Ihr Adressbuch wurde erfolgreich aktualisiert!');
define('WARNING_PRIMARY_ADDRESS_DELETION', 'Die Standardadresse kann nicht gelöscht werden. Bitte erst eine andere Standardadresse wählen. Danach kann der Eintrag gelöscht werden.');
define('ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY', 'Dieser Adressbucheintrag ist nicht vorhanden.');
define('ERROR_ADDRESS_BOOK_FULL', 'Ihr Adressbuch kann keine weiteren Adressen aufnehmen. Bitte löschen Sie eine nicht mehr benötigte Adresse. Danach können Sie einen neuen Eintrag speichern.');

/**
	Conditions Check
**/
define('ERROR_CONDITIONS_NOT_ACCEPTED', '* Sofern Sie unsere Allgemeinen Geschäftsbedingungen nicht akzeptieren, können wir Ihre Bestellung bedauerlicherweise nicht entgegennehmen!');

define('SUB_TITLE_OT_DISCOUNT','Rabatt:');

define('TAX_ADD_TAX','enthaltene ');
define('TAX_NO_TAX','zzgl. ');

define('NOT_ALLOWED_TO_SEE_PRICES','Sie können als Gast (bzw. mit Ihrem derzeitigen Status) keine Preise sehen');
define('NOT_ALLOWED_TO_SEE_PRICES_TEXT','Sie haben keine Erlaubnis Preise zu sehen, erstellen Sie bitte ein Kundenkonto.');

define('TEXT_DOWNLOAD','Download');
define('TEXT_VIEW','Ansehen');
define('TEXT_PRINT','Drucken');

define('TEXT_BUY', '1x ');

define('TEXT_GUEST','Gast');

/**
	ADVANCED SEARCH
**/

define('TEXT_ALL_CATEGORIES', 'Alle Kategorien');
define('TEXT_ALL_MANUFACTURERS', 'Alle Hersteller');
define('JS_AT_LEAST_ONE_INPUT', '* Eines der folgenden Felder muss ausgefüllt werden:\n    Stichworte\n    Preis ab\n    Preis bis\n');
define('AT_LEAST_ONE_INPUT', 'Eines der folgenden Felder muss ausgefüllt werden:<br />Stichworte mit mindestens drei Zeichen<br />Preis ab<br />Preis bis<br />');
define('SEARCH_RESULTS_WORDS','Zu dem Keyword &quot;<em>%s</em>&quot; gibt es <em>%s</em> Treffer');
define('JS_INVALID_FROM_DATE', '* ungültiges Datum (von)\n');
define('JS_INVALID_TO_DATE', '* ungültides Datum (bis)\n');
define('JS_TO_DATE_LESS_THAN_FROM_DATE', '* Das Datum (von) muss grösser oder gleich sein als das Datum (bis)\n');
define('JS_PRICE_FROM_MUST_BE_NUM', '* \"Preis ab\" muss eine Zahl sein\n\n');
define('JS_PRICE_TO_MUST_BE_NUM', '* \"Preis bis\" muss eine Zahl sein\n\n');
define('JS_PRICE_TO_LESS_THAN_PRICE_FROM', '* Preis bis muss grösser oder gleich Preis ab sein.\n');
define('JS_INVALID_KEYWORDS', '* Suchbegriff unzulässig\n');
define('TEXT_LOGIN_ERROR', '<font color="#ff0000"><b>FEHLER:</b></font> Keine &Uuml;bereinstimmung der eingebenen \'E-Mail-Adresse\' und/oder dem \'Passwort\'.');
define('TEXT_NO_EMAIL_ADDRESS_FOUND', '<font color="#ff0000"><b>ACHTUNG:</b></font> Die eingegebene E-Mail-Adresse ist nicht registriert. Bitte versuchen Sie es noch einmal.');
define('TEXT_PASSWORD_SENT', 'Ein neues Passwort wurde per E-Mail verschickt.');
define('TEXT_PRODUCT_NOT_FOUND', 'Artikel wurde nicht gefunden!');
define('TEXT_MORE_INFORMATION', 'Für weitere Informationen, besuchen Sie bitte die <a style="text-decoration:underline" href="%s" onclick="window.open(this.href); return false;">Homepage</a> zu diesem Artikel.');
define('TEXT_DATE_ADDED', 'Diesen Artikel haben wir am %s in unseren Katalog aufgenommen.');
define('TEXT_DATE_AVAILABLE', '<font color="#ff0000">Dieser Artikel wird voraussichtlich ab dem %s wieder vorrätig sein.</font>');
define('SUB_TITLE_SUB_TOTAL', 'Zwischensumme:');

define('OUT_OF_STOCK_CANT_CHECKOUT', 'Die mit ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' markierten Artikel sind leider nicht in der von Ihnen gewünschten Menge auf Lager.<br />Bitte reduzieren Sie Ihre Bestellmenge für die gekennzeichneten Artikel. Vielen Dank');
define('OUT_OF_STOCK_CAN_CHECKOUT', 'Die mit ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' markierten Artikel sind leider nicht in der von Ihnen gewünschten Menge auf Lager.<br />Die bestellte Menge wird kurzfristig von uns geliefert, wenn Sie es wünschen, nehmen wir auch eine Teillieferung vor.');

define('MINIMUM_ORDER_VALUE_NOT_REACHED_1', 'Sie haben den Mindestbestellwert von: ');
define('MINIMUM_ORDER_VALUE_NOT_REACHED_2', ' leider noch nicht erreicht.<br />Bitte bestellen Sie für mindestens weitere: ');
define('MAXIMUM_ORDER_VALUE_REACHED_1', 'Sie haben die Höchstbestellsumme von: ');
define('MAXIMUM_ORDER_VALUE_REACHED_2', 'überschritten.<br /> Bitte reduzieren Sie Ihre Bestellung um mindestens: ');

define('ERROR_INVALID_PRODUCT', 'Der von Ihnen gewählte Artikel wurde nicht gefunden!');

/**
	NAVBAR Titel
**/

define('NAVBAR_TITLE_404', '404 - Seite nicht gefunden');
define('NAVBAR_TITLE_ACCOUNT', 'Ihr Konto');
define('NAVBAR_TITLE_BLOG','Blog');
define('NAVBAR_TITLE_TAGLIST','Tags');
define('NAVBAR_TITLE_SEARCH','Suche');
define('NAVBAR_TITLE_1_ACCOUNT_EDIT', 'Ihr Konto');
define('NAVBAR_TITLE_2_ACCOUNT_EDIT', 'Ihre persönliche Daten ändern');
define('NAVBAR_TITLE_1_ACCOUNT_HISTORY', 'Ihr Konto');
define('NAVBAR_TITLE_2_ACCOUNT_HISTORY', 'Ihre getätigten Bestellungen');
define('NAVBAR_TITLE_1_ACCOUNT_HISTORY_INFO', 'Ihr Konto');
define('NAVBAR_TITLE_2_ACCOUNT_HISTORY_INFO', 'Getätigte Bestellung');
define('NAVBAR_TITLE_3_ACCOUNT_HISTORY_INFO', 'Bestellnummer %s');
define('NAVBAR_TITLE_1_ACCOUNT_PASSWORD', 'Ihr Konto');
define('NAVBAR_TITLE_2_ACCOUNT_PASSWORD', 'Passwort ändern');
define('NAVBAR_TITLE_1_ADDRESS_BOOK', 'Ihr Konto');
define('NAVBAR_TITLE_2_ADDRESS_BOOK', 'Adressbuch');
define('NAVBAR_TITLE_1_ADDRESS_BOOK_PROCESS', 'Ihr Konto');
define('NAVBAR_TITLE_2_ADDRESS_BOOK_PROCESS', 'Adressbuch');
define('NAVBAR_TITLE_ADD_ENTRY_ADDRESS_BOOK_PROCESS', 'Neuer Eintrag');
define('NAVBAR_TITLE_1_CHECKOUT','Kasse');
define('NAVBAR_TITLE_MODIFY_ENTRY_ADDRESS_BOOK_PROCESS', 'Eintrag ändern');
define('NAVBAR_TITLE_DELETE_ENTRY_ADDRESS_BOOK_PROCESS', 'Eintrag löschen');
define('NAVBAR_TITLE_ADVANCED_SEARCH', 'Erweiterte Suche');
define('NAVBAR_TITLE1_ADVANCED_SEARCH', 'Erweiterte Suche');
define('NAVBAR_TITLE2_ADVANCED_SEARCH', 'Suchergebnisse');
define('NAVBAR_TITLE_1_CHECKOUT_CONFIRMATION', 'Kasse');
define('NAVBAR_TITLE_2_CHECKOUT_CONFIRMATION', 'Bestätigung');
define('NAVBAR_TITLE_1_CHECKOUT_PAYMENT', 'Kasse');
define('NAVBAR_TITLE_2_CHECKOUT_PAYMENT', 'Zahlungsweise');
define('NAVBAR_TITLE_1_PAYMENT_ADDRESS', 'Kasse');
define('NAVBAR_TITLE_2_PAYMENT_ADDRESS', 'Rechnungsadresse ändern');
define('NAVBAR_TITLE_1_CHECKOUT_SHIPPING', 'Kasse');
define('NAVBAR_TITLE_2_CHECKOUT_SHIPPING', 'Versandinformationen');
define('NAVBAR_TITLE_1_CHECKOUT_SHIPPING_ADDRESS', 'Kasse');
define('NAVBAR_TITLE_2_CHECKOUT_SHIPPING_ADDRESS', 'Versandadresse ändern');
define('NAVBAR_TITLE_1_CHECKOUT_SUCCESS', 'Kasse');
define('NAVBAR_TITLE_2_CHECKOUT_SUCCESS', 'Erfolg');
define('NAVBAR_TITLE_CREATE_ACCOUNT', 'Konto erstellen');

if ($navigation->snapshot['page'] == FILENAME_CHECKOUT_SHIPPING) {
  define('NAVBAR_TITLE_LOGIN', 'Bestellen');
} else {
  define('NAVBAR_TITLE_LOGIN', 'Anmelden');
}
define('NAVBAR_TITLE_LOGOFF','Auf Wiedersehen');
define('NAVBAR_TITLE_PRODUCTS_NEW', 'Neue Artikel');
define('NAVBAR_TITLE_SHOPPING_CART', 'Warenkorb');
define('NAVBAR_TITLE_WISH_LIST', 'Merkzettel');
define('NAVBAR_TITLE_SPECIALS', 'Angebote');
define('NAVBAR_TITLE_COOKIE_USAGE', 'Cookie-Nutzung');
define('NAVBAR_TITLE_PRODUCT_REVIEWS', 'Bewertungen');
define('NAVBAR_TITLE_REVIEWS_WRITE', 'Bewertungen');
define('NAVBAR_TITLE_REVIEWS','Bewertungen');
define('NAVBAR_TITLE_SSL_CHECK', 'Sicherheitshinweis');
define('NAVBAR_TITLE_CREATE_GUEST_ACCOUNT','Konto erstellen');
define('NAVBAR_TITLE_PASSWORD_DOUBLE_OPT','Passwort vergessen?');
define('NAVBAR_TITLE_NEWSLETTER','Newsletter');
define('NAVBAR_GV_REDEEM', 'Gutschein einlösen');
define('NAVBAR_GV_SEND', 'Gutschein versenden');

/**
	MISC
**/

define('TITLE_HELP', 'Hilfe zur erweiterten Suche');
define('TEXT_HELP','Die Suchfunktion ermöglicht Ihnen, innerhalb von Artikelnamen, Artikelbeschreibungen, Herstellern und Artikelnummern zu suchen.<br /><br />Sie haben die Möglichkeit logische Operatoren wie "AND" (Und) und "OR" (oder) zu verwenden.<br /><br />Zum Beispiel könnten Sie also angeben: <span class="underline">Microsoft AND Maus</span>.<br /><br />Desweiteren können Sie Klammern verwenden um die Suche zu verschachteln, also z.B.:<br /><br /><span class="underline">Microsoft AND (Maus OR Tastatur OR "Visual Basic")</span>.<br /><br />Mit Anführungszeichen können Sie mehrere Worte zu einem Suchbegriff zusammenfassen.');
define('TEXT_CLOSE','[x] Fenster schliessen');

define('TEXT_NEWSLETTER','Sie möchten immer auf dem Laufenden bleiben?<br />Kein Problem, tragen Sie sich in unseren Newsletter ein und Sie sind immer auf dem neuesten Stand.');
define('TEXT_EMAIL_INPUT','Ihre E-Mail-Adresse wurde in unser System eingetragen.<br />Gleichzeitig wurde Ihnen vom System eine E-Mail mit einem Aktivierungslink geschickt. Bitte klicken Sie nach dem Erhalt der E-Mail auf den Link um Ihre Eintragung zu bestätigen. Ansonsten bekommen Sie keinen Newsletter von uns zugestellt!');

define('TEXT_WRONG_CODE','Ihr eingegebener Sicherheitscode stimmte nicht mit dem angezeigten Code &Uuml;berein. Bitte versuchen Sie es erneut.');
define('TEXT_EMAIL_EXIST_NO_NEWSLETTER','Diese E-Mail-Adresse existiert bereits in unserer Datenbank ist aber noch nicht für den Empfang des Newsletters freigeschaltet!');
define('TEXT_EMAIL_EXIST_NEWSLETTER','Diese E-Mail-Adresse existiert bereits in unserer Datenbank und ist für den Newsletterempfang bereits freigeschaltet!');
define('TEXT_EMAIL_NOT_EXIST','Diese E-Mail-Adresse existiert nicht in unserer Datenbank!</span>');
define('TEXT_EMAIL_DEL','Ihre E-Mail-Adresse wurde aus unserer Newsletterdatenbank gelöscht.');
define('TEXT_EMAIL_DEL_ERROR','Es ist ein Fehler aufgetreten, Ihre E-Mail-Adresse wurde nicht gelöscht!');
define('TEXT_EMAIL_ACTIVE','Ihre E-Mail-Adresse wurde erfolgreich für den Newsletterempfang freigeschaltet!');
define('TEXT_EMAIL_ACTIVE_ERROR','Es ist ein Fehler aufgetreten, Ihre E-Mail-Adresse wurde nicht freigeschaltet!');
define('TEXT_EMAIL_SUBJECT','Ihre Newsletteranmeldung');

define('TEXT_CUSTOMER_GUEST','Gast');

define('TEXT_LINK_MAIL_SENDED','Ihre Anfrage nach einem neuen Passwort muss von Ihnen erst bestätigt werden.<br />Deshalb wurde Ihnen vom System eine E-Mail mit einem Bestätigungslink geschickt. Bitte klicken Sie nach dem Erhalt der E-Mail auf den Link und eine weitere E-Mail mit Ihrem neuen Anmelde-Passwort zu erhalten. Andernfalls wird Ihnen das neue Passwort nicht zugestellt oder eingerichtet!');
define('TEXT_PASSWORD_MAIL_SENDED','Eine E-Mail mit einem neuen Anmelde-Passwort wurde Ihnen soeben zugestellt.<br />Bitte ändern Sie nach Ihrer nächsten Anmeldung Ihr Passwort wie gewünscht.');
define('TEXT_CODE_ERROR','Bitte geben Sie Ihre E-Mail-Adresse und den Sicherheitscode erneut ein. <br />Achten Sie dabei auf Tippfehler!');
define('TEXT_EMAIL_ERROR','Bitte geben Sie Ihre E-Mail-Adresse und den Sicherheitscode erneut ein. <br />Achten Sie dabei auf Tippfehler!');
define('TEXT_NO_ACCOUNT','Leider müssen wir Ihnen mitteilen, dass Ihre Anfrage für ein neues Anmelde-Passwort entweder ungültig war oder abgelaufen ist.<br />Bitte versuchen Sie es erneut.');
define('HEADING_PASSWORD_FORGOTTEN','Passwort erneuern?');
define('TEXT_PASSWORD_FORGOTTEN','Ändern Sie Ihr Passwort in drei leichten Schritten.');
define('TEXT_EMAIL_PASSWORD_FORGOTTEN','Bestätigungs-E-Mail für Passwortänderung');
define('TEXT_EMAIL_PASSWORD_NEW_PASSWORD','Ihr neues Passwort');
define('ERROR_MAIL','Bitte überprüfen Sie Ihre eingegebenen Daten im Formular');

define('CATEGORIE_NOT_FOUND','Kategorie wurde nicht gefunden');

define('GV_FAQ', 'Gutschein FAQ');
define('ERROR_NO_REDEEM_CODE', 'Sie haben leider keinen Code eingegeben.');
define('ERROR_NO_INVALID_REDEEM_GV', 'Ungültiger Gutscheincode');
define('ERROR_ALREADY_REDEEMED_GV', 'Gutscheincode wurde bereits eingelöst');
define('TABLE_HEADING_CREDIT', 'Guthaben');
define('EMAIL_GV_TEXT_SUBJECT', 'Ein Geschenk von %s');
define('MAIN_MESSAGE', 'Sie haben sich dazu entschieden, einen Gutschein im Wert von %s an %s zu versenden, die E-Mail-Adresse %s lautet.<br /><br />Folgender Text erscheint in Ihrer E-Mail:<br /><br />Hallo %s<br /><br />Ihnen wurde ein Gutschein im Wert von %s durch %s geschickt.');
define('REDEEMED_AMOUNT','Ihr Gutschein wurde erfolgreich auf Ihr Konto verbucht. Gutscheinwert:');
define('PERSONAL_MESSAGE', '%s schreibt:');

//Popup Window
define('TEXT_CLOSE_WINDOW', 'Fenster schliessen.');

/**
	COUPON POPUP
**/

define('TEXT_COUPON_HELP_HEADER', 'Ihr Gutschein wurde erfolgreich verbucht.');
define('TEXT_COUPON_HELP_NAME', '<br />Gutscheinbezeichnung: %s');
define('TEXT_COUPON_HELP_FIXED', '<br />Der Gutscheinwert beträgt %s ');
define('TEXT_COUPON_HELP_MINORDER', '<br />Der Mindestbestellwert beträgt %s ');
define('TEXT_COUPON_HELP_FREESHIP', '<br />Gutschein für kostenlosen Versand');
define('TEXT_COUPON_HELP_DESC', '<br />Kuponbeschreibung: %s');
define('TEXT_COUPON_HELP_DATE', '<br />Dieser Kupon ist gültig vom %s bis %s');
define('TEXT_COUPON_HELP_RESTRICT', '<br />Artikel / Kategorie Einschränkungen');
define('TEXT_COUPON_HELP_CATEGORIES', 'Kategorie');
define('TEXT_COUPON_HELP_PRODUCTS', 'Artikel');

// VAT ID
define('ENTRY_VAT_TEXT', 'Nur für DE und EU!');
define('ENTRY_VAT_ERROR', 'Die Eingegebene Ust-ID ist ungültig oder kann derzeit nicht überprüft werden! Bitte geben Sie eine gültige ID ein oder lassen Sie das Feld leer.');
define('MSRP','UVP');
define('YOUR_PRICE','Ihr Preis ');
define('ONLY',' Nur ');
define('FROM','ab ');
define('SINGLE_PRICE','Einzelpreis ');
define('YOU_SAVE','Sie sparen rund ');
define('INSTEAD','unser bisheriger Preis ');
define('TXT_PER',' pro ');
if (MODULE_ORDER_TOTAL_UST_FREE_STATUS == 'true') {
define('TAX_INFO_INCL','Kein Steuerausweis gem. Kleinuntern.-Reg. §19 UStG');
define('TAX_INFO_EXCL','Kein Steuerausweis gem. Kleinuntern.-Reg. §19 UStG');
define('TAX_INFO_ADD','Kein Steuerausweis gem. Kleinuntern.-Reg. §19 UStG');
} else {
define('TAX_INFO_INCL','inkl. %s MwSt.');
define('TAX_INFO_EXCL','exkl. %s MwSt.');
define('TAX_INFO_ADD','zzgl. %s MwSt.');
}
define('SHIPPING_EXCL','zzgl.');
define('SHIPPING_COSTS','Versandkosten');

// changes 3.0.4 SP2
define('SHIPPING_TIME','Lieferzeit: ');
define('MORE_INFO','[Mehr]');

// Sortierungen
define('MULTISORT_STANDARD', 'Sortieren nach ...'); 
define('MULTISORT_NEW_DESC', 'Neuste zuerst'); 
define('MULTISORT_NEW_ASC', '&Auml;lteste zuerst'); 
define('MULTISORT_PRICE_ASC', 'Preis - aufsteigend'); 
define('MULTISORT_PRICE_DESC', 'Preis - absteigend'); 
define('MULTISORT_ABC_AZ', 'Alphabet A-Z'); 
define('MULTISORT_ABC_ZA', 'Alphabet Z-A');
define('MULTISORT_MANUFACTURER_ASC', 'Hersteller A-Z'); 
define('MULTISORT_MANUFACTURER_DESC', 'Hersteller Z-A'); 
define('MULTISORT_SPECIALS_DESC', 'Nur Sonderangebote');
define('RESULT_STANDARD','Artikel pro Seite');
define('PER_SITE','pro Seite');

// Datenschutz Check
define('ERROR_DATENSCHUTZ_NOT_ACCEPTED', '* Sofern Sie unsere Datenschutzerklärung nicht akzeptieren, können wir Ihre Bestellung bedauerlicherweise nicht entgegennehmen!');
define('ERROR_WIDERRUFSRECHT_NOT_ACCEPTED', '* Sofern Sie unser Widerrufsrecht nicht akzeptieren, können wir Ihre Bestellung bedauerlicherweise nicht entgegennehmen!');

define('NAVBAR_TITLE_1_ACCOUNT_DELETE', 'Ihr Konto');
define('NAVBAR_TITLE_2_ACCOUNT_DELETE', 'Konto löschen');
define('PRINT_CONTENT', '<img src="images/button_print.gif" alt="Druckversion" />');

define('BUTTON_PRINT_AGB', '<img src="images/button_print.gif" alt="Druckversion" />');
define('BUTTON_PRINT_DS', '<img src="images/button_print.gif" alt="Druckversion" />');
define('BUTTON_PRINT_WD', '<img src="images/button_print.gif" alt="Druckversion" />');

define('ERROR_CDATENSG','Bitte füllen Sie alle Felder aus und akzeptieren die Datenschutzerklärung');


define('HEAD_INFO_TXT','Ihr Merkzettel enthält %s Produkt im Wert von ');
define('HEAD_INFO_TXT_MORE','Ihr Merkzettel enthält %s Produkte im Wert von ');
define('WISHLIST_EMPTY','Ihr Merkzettel enthält keine Artikel.');

define('PDFBILL_DOWNLOAD_INVOICE', 'PDF-Rechnung Download' );   // pdfrechnung

define('CHECKOUT_REMOVE_CONFIRM','Sind Sie sich sicher, dass Sie diesen Artikel entfernen möchten?');
define('CHECKOUT_EMPTY_CART','Es wurden alle Artikel entfernt. Sie werden nun zurück zum Warenkorb geleitet.');
define('CHECKOUT_NOMORE_ADDRESSES','Leider können Sie keine weiteren Adressen mehr zu Ihrem Adressbuch hinzufügen. Das Maximum ist erreicht.');
define('CHECKOUT_TEXT_VIRTUAL','Diese Informationen werden nicht benötigt, da es sich bei Ihrer Bestellung um virtuelle Produkte handelt.');
define('CHECKOUT_OUT_OF_STOCK','Ihre Bestellung konnte nicht aktualisiert werden, da dieses Produkt nicht in der gewünschten Anzahl zur Verfügung steht.');
define('CHECKOUT_NO_PAYMENT_MODULE_SELECTED','Bitte wählen Sie eine Zahlungsmethode aus');
define('CHECKOUT_NO_SHIPPING_MODULE_SELECTED','Bitte wählen Sie eine Versandart aus');
define('CHECKOUT_PAYMENT_OK','Daten wurden gespeichert.');
define('CHECKOUT_SHIPPING_OK','Daten wurden gespeichert.');
define('CHECKOUT_SHIPPING_CHOOSE','Bitte eine Versandart auswählen');
define('CHECKOUT_PAYMENT_CHOOSE','Bitte eine Zahlungsart auswählen');
define('CHECKOUT_PAYMENT_NOT_COMPATIBLE','Zahlungsart nicht kompatibel. Bitte wählen Sie eine neue aus.');
define('CHECKOUT_ERROR_CONDITIONS','- Bitte akzeptieren Sie unsere Allgemeinen Gesschäftsbedingungen');
define('CHECKOUT_ERROR_REVOCATION','- Bitte bestätigen Sie unser Widerrufrecht');
define('CHECKOUT_ERROR_REVOCATION_DOWNLOAD','- Bitte bestätigen Sie den Verzicht auf Ihr Widerrufsrecht für Downloads.');
define('CHECKOUT_ERROR_REVOCATION_SERVICE','- Bitte bestätigen Sie den Verzicht auf Ihr Widerrufsrecht für Dienstleistungen.');
define('CHECKOUT_ERROR_DSG','- Bitte akzeptieren Sie unsere Datenschutzerklärung');
define('CHECKOUT_PLEASE_WAIT','Bitte warten...');
define('CHECKOUT_PAYMENT_DUE', '(+ Geb&uuml;hr)');



#New in v2.1
define('ADVANCED_SEARCH_HEADER','Treffer für ');
define('LISTING_GALLERY',xtc_image('images/icons/view_gallery.png', 'Galerie', 'Galerie Ansicht'));
define('LISTING_LIST',xtc_image('images/icons/view_list.png', 'Liste', 'Listen Ansicht'));
define('LISTING_GALLERY_ACTIVE',xtc_image('images/icons/view_gallery_active.png', 'Galerie', 'Galerie Ansicht'));
define('LISTING_LIST_ACTIVE',xtc_image('images/icons/view_list_active.png', 'Liste', 'Listen Ansicht'));
define('TEXT_TAG_NOT_FOUND','Ihre Tag-Suche ergab keine Treffer.');
define('TEXT_TAG_TREFFER1','dazu gibt es ');
define('TEXT_TAG_TREFFER2',' Treffer<br /><br />');
define('TEXT_TAG_HEAD','Tag: ');
define('TEXT_WRITE_REVIEW','Schreiben Sie als erster einen Kommentar<br />');

define('NAVBAR_TITLE_PRODUCT_FILTER','Produkt Filter');
define('PRODUCT_FILTER_AND', 'alle Kriterien müssen zutreffen');
define('PRODUCT_FILTER_OR', 'ein Kriterium muss zutreffen');
#Gutschein
define('SUB_TITLE_OT_COUPON', 'Rabatt Kupon:');
define('REDEEMED_COUPON','Ihr Kupon wurde erfolgreich eingebucht und wird bereits bei der aktuellen Bestellung berücksichtigt.');
define('ERROR_INVALID_USES_USER_COUPON','Sie können diesen Gutschein nur ');
define('ERROR_INVALID_USES_COUPON','Dieser Gutschein kann nur von insgesamt');
define('TIMES',' Kunden eingelöst werden. Dieses Limit wurde bereits erreicht!');
define('TIMES2',' mal einlösen. Dieses Limit haben Sie bereits ausgeschöpft!');
define('ERROR_INVALID_STARTDATE_COUPON','Die Laufzeit Ihres Kupons hat noch nicht begonnen.');
define('ERROR_INVALID_FINISDATE_COUPON','Ihr Kupon ist bereits abgelaufen.');
define('ERROR_INVALID_PRODUCT_COUPON','Ihr Kupon ist auf bestimmte Produkte beschränkt.');
define('ERROR_INVALID_CATEGORIE_COUPON','Ihr Kupon ist auf bestimmte Kategorien beschränkt.');
define('ERROR_MINIMUM_ORDER_COUPON_1','Der Mindestbestellwert für diesen Kupon in Höhe von ');
define('ERROR_MINIMUM_ORDER_COUPON_2',' wurde noch nicht erreicht.');
define('ERROR_GV_LOGIN','Bitte melden Sie sich an, bevor Sie einen Gutschein einlösen. Nur so kann der Wert Ihrem Konto gutgeschrieben werden.');
define('ERROR_ENTRY_AMOUNT_CHECK','Fehler: Ihr Guthaben reicht nicht aus um diesen Betrag zu verschenken.');
define('ERROR_ENTRY_NO_NAME','Fehler: Kein Name des Empfängers angegeben.');
define('ERROR_ENTRY_NO_AMOUNT','Fehler: Kein gültiger Betrag angegeben.');
define('ERROR_ENTRY_EMAIL_ADDRESS_CHECK','Fehler: Keine gültige E-Mail Adresse angegeben.');
define('COUPON_TYPE_S','Versandkostenfrei Rabatt');
define('COUPON_TYPE_F','Festbetrag Rabatt');
define('COUPON_TYPE_P','Prozentualer Rabatt');
define('CART_SPECIAL','Das könnte Sie ebenfalls interessieren');

#WCAG
define('WCAG_REGISTER','eintragen');
define('WCAG_UNREGISTER','austragen');
define('WCAG_MANUFACTURERS','Sortierung-Hersteller:');
define('WCAG_QTY','Menge');
define('WCAG_SEARCH','Suche');
define('WCAG_MANUFACTURERS_LABEL','Hersteller Auswahl');
define('CAPTCHA_DESCRIPTION','Captcha:');
define('TEXT_CURRENT_PRICE','Gesamtpreis:');
define('NAVBAR_TITLE_ADVANCED_SEARCH', 'Erweiterte Suche');

define('ERROR_FROM_NAME', 'Fehler: Bitte geben Sie Ihren Namen ein.');
define('ERROR_FROM_ADDRESS', 'Fehler: Bitte geben Sie eine g&uuml;ltige Mail-Adresse ein.');
define('ERROR_MESSAGE', 'Fehler: Bitte geben Sie eine Nachricht ein.');
define('PRODUCT_ASK_A_QUESTION_SUCCESS', 'Vielen Dank! Ihre Nachricht ist erfolgreich gesendet worden, wir setzen uns umgehend mit Ihnen in Verbindung.');

define('PRODUCT_AKS_A_QUESTION_SUBJECT_1', 'Frage zum Produkt');
define('PRODUCT_AKS_A_QUESTION_SUBJECT_2', 'Angebot zum Produkt');
define('PRODUCT_AKS_A_QUESTION_SUBJECT_3', 'technische Frage zum Produkt');

define('NAVBAR_TITLE_PAYPAL_CHECKOUT','PayPal-Checkout');
define('PAYPAL_ERROR','PayPal Abbruch');
define('PAYPAL_NOT_AVIABLE','PayPal Express steht zur Zeit leider nicht zur Verfügung.<br />Bitte wählen Sie eine andere Zahlungsart<br />oder versuchen Sie es später noch einmal.<br />Danke für Ihr Verständnis.<br />');
define('PAYPAL_FEHLER','PayPal hat einen Fehler bei der Abwicklung gemeldet.<br />Ihre Bestellung ist gespeichert, wird aber nicht ausgeführt.<br />Bitte geben Sie eine neue Bestellung ein.<br />Danke für Ihr Verständnis.<br />');
define('PAYPAL_WARTEN','PayPal hat einen Fehler bei der Abwicklung gemeldet.<br />Sie müssen noch einmal zu PayPal um die Bestellung zu bezahlen.<br />Unten sehen Sie die gespeicherte Bestellung.<br />Danke für Ihr Verständnis.<br />Bitte drücken Sie erneut den Button PayPal Express.<br />');
define('PAYPAL_NEUBUTTON','Bitte erneut drücken um die Bestellung zu bezahlen.<br />Jede andere Taste führt zum Abbruch der Bestellung.');
define('ERROR_ADDRESS_NOT_ACCEPTED', '* Solange Sie Ihre Rechnungs- und Versandadresse nicht akzeptieren,\n können wir Ihre Bestellung bedauerlicherweise nicht entgegennehmen!\n\n');
define('PAYPAL_GS','Gutschein/Coupon');
define('PAYPAL_TAX','MwSt.');
define('PAYPAL_EXP_WARN','Achtung! Eventuell anfallende Versandkosten werden erst im Shop endgültig berechnet.');
define('PAYPAL_EXP_VORL','Vorläufige Versandkosten');
define('PAYPAL_ADRESSE','Das Land in Ihrer PayPal-Versand-Adresse ist in unserem Shop nicht eingetragen.<br />Bitte nehmen Sie mit uns Kontakt auf.<br />Danke für Ihr Verständnis.<br />Von PayPal empfangenes Land: ');
define('PAYPAL_AMMOUNT_NULL','Die zu erwartende Auftrags-Summe (ohne Versand) ist gleich 0.<br />Dadurch steht PayPal Express nicht zur Verfügung.<br />Bitte wählen Sie eine andere Zahlungsart.<br />Danke für Ihr Verständnis.<br />');

define('TEXT_WISH_SINGLE', 'bestellen');

define('TEXT_IN', 'eintragen');
define('TEXT_OUT', 'austragen');


define('AUTOSUGGEST_CLOSE', 'Fenster schliessen');
define('MORE_RESULTS', '...mehr Resultate');
define('AUTOSUGGEST_NO_PRODUCTS', 'Keine Produkte gefunden');
define('AUTOSUGGEST_INTRO', 'Zu diesem Suchbegriff empfehlen wir:');
define('GAST', 'Gast');
define('TITLE_BEWERTUNGEN', 'Shop-Bewertungen Verwalten');
define('SUB_TITLE_BEWERTUNGEN', 'Verwalten Sie die erhaltenen Shopbewertungen');

define('NAVBAR_TITLE_SHOPBEWERTUNGEN', 'Shop-Bewertungen');
define('NAVBAR_TITLE_SHOPBEWERTUNGEN_WRITE', 'Shop-Bewertung schreiben');

define('SHOPBEWERTUNG_FORMULAR', 'Shopbewertung-Formular');
define('SHOPBEWERTUNG_ERHALTEN', 'Neue Shopbewertung erhalten');
define('MAIL_BEWERTUNG_VON', '<strong>Bewertung von:</strong>');
define('MAIL_BEWERTUNG_ABSENDER', '<strong>Absender:</strong>');
define('MAIL_BEWERTUNG_KOMMENTAR', '<strong>Kommentar:</strong>');

define('MAIL_BEWERTUNG_SHOP', '<strong>Bewertung-Shop:</strong>');
define('MAIL_BEWERTUNG_WARE', '<strong>Bewertung-Ware:</strong>');
define('MAIL_BEWERTUNG_VERSAND', '<strong>Bewertung-Versand:</strong>');
define('MAIL_BEWERTUNG_SERVICE', '<strong>Bewertung-Service:</strong>');
define('MAIL_BEWERTUNG_SEITE', '<strong>Bewertung-Seite:</strong>');


define('STERN_BEWERTUNGEN', 'Stern');
define('STERN_STUCK', 'Stern');
define('STERN_KEIN', 'Keine Wertung');

define('WERTUNG1', 'Ganz schlecht');
define('WERTUNG2', 'Schlecht');
define('WERTUNG3', 'Neutral');
define('WERTUNG4', 'Gut');
define('WERTUNG5', 'Sehr gut');

define('MAILERROR', 'Bitte geben Sie eine korrekte E-Mail Adresse an.');
define('RATINGERROR', 'Bitte geben Sie eine Shopbewertung ein.');
define('CAPTCHAERROR', 'Bitte geben Sie den korrekten Sicherheitscode ein.');
define('NAMEERROR', 'Bitte geben Sie einen Vor- und Nachnamen ein.');
define('ORDERIDERROR', 'Es gibt keine Bestellung mit dieser Bestellnummer.');
define('COMMENTERROR', 'Bitte geben Sie einen Kommentar ein.');
define('ORDERIDERROREMAIL', 'Bestellnummer und E-Mail stimmen nicht mit den Bestelldaten &uuml;berein.');

define('SMALL_IMAGE_BUTTON_DELETE', 'Löschen');
define('SMALL_IMAGE_BUTTON_EDIT', '&Auml;ndern');
define('SMALL_IMAGE_BUTTON_VIEW', 'Anzeigen');

define('ICON_ARROW_RIGHT', 'Zeige mehr');
define('ICON_CART', 'In den Warenkorb');
define('ICON_SUCCESS', 'Erfolg');
define('ICON_WARNING', 'Warnung');
define('ICON_ERROR','Fehler');

define('SECURITY_CODE_ERROR','Falscher Sicherheitscode');

define('MIN_REVIEW_TEXT_ERROR', '* Der Kommentar muß aus mindestens ' . REVIEW_TEXT_MIN_LENGTH . ' Buchstaben bestehen. Bitte ergänzen Sie Ihre Eingabe.');
define('MIN_REVIEW_RATING_ERROR', 'Bitte geben Sie eine Bewertung in Form der Sterne ab.');
define('CHECKOUT_SUM', 'Preis');
define('CHECKOUT_SPRICE', 'Einzelpreis');
define('CHECKOUT_DESC', 'Beschreibung');
define('WK_NETTO', 'Summe ohne MwSt. ');

define('BUTTON_PAYPAL_TEXT','Hinweis zu Expresskauf mit PayPal');

define('AMZ_SINGLE_PRICE', 'Einzelpreis');
define('AMZ_TOTAL_PRICE', 'Gesamtpreis');
define('NO_POSITIONS','Nicht vorhanden.');
define('CANCEL','Abbrechen');
define('AMZ_TOTAL', 'Gesamt');
define('ACCEPT','Bitte akzeptieren Sie unsere AGB und das Widerrufsrecht!');
define('NO_SHIPPING','Versand nicht möglich!');
define('NO_SHIPPING_TO_ADDRESS', 'Versand an diese Adresse nicht m&ouml;glich.');
define('FREE_SHIPPING_AT', 'Versandkostenfreie Lieferung ab ');
define('SUCCESS','Ihre Amazon.de-Bestellnummer f&uuml;r diese Bestellung in unserem Onlineshop lautet:');
define('AMZ_WAITING', 'Bitte warten Sie, Sie werden gleich weitergeleitet');
define('AMZ_WAITING_IMG', 'https://images-na.ssl-images-amazon.com/images/G/01/cba/images/global/Loading._V192259297_.gif');
define('AMZ_ZOLL', 'Bei Lieferung in das Nicht-EU-Ausland, k&ouml;nnen weitere Z&ouml;lle, Steuern oder Geb&uuml;hren vom Kunden zu zahlen sein, jedoch nicht an den Anbieter, sondern an die dort zust&auml;ndigen Zoll- bzw. Steuernbeh&ouml;rden. Dem Kunden wird empfohlen, die Einzelheiten vor der Bestellung bei den Zoll- bzw. Steuerbeh&ouml;rden zu erfragen.');


define('AMZ_ADMIN_HINT', '* wurde reduziert, da die Werte der eingel&ouml;sten Rabatte, bzw. Gutscheine auf die Produkte verteilt wurden.');
define('AMZ_ADMIN_BTN', 'Ausf&uuml;hren');
define('AMZ_VERSANDANTEIL', 'Versandanteil');
define('AMZ_PRODUKT', 'Produkt');
define('AMZ_SHOW_HIDE', 'zeigen/verstecken');
define('AMZ_REFUND_SUCCESS', 'R&uuml;ckbuchung erfolgreich veranlasst!');
define('AMZ_REFUND_ERROR', 'R&uuml;ckbuchung nicht verarbeitet - bitte pr&uuml;fen Sie die Betr&auml;ge auf G&uuml;ltigkeit!');
define('AMZ_DATE', 'Datum');
define('AMZ_BETRAG', 'Betrag');


define('SUBCAT_PRODUCTS', 'Produktübersicht');
define('BOX_EMAIL_PASSWD','Passwort');
define('TEXT_MINORDER','Bitte beachten Sie die Mindestbestellmenge für folgende Produkte');
define('TEXT_MINORDER_TITLE','Mindestbestellmenge');
define('RANDOM_SPECIALS','Angebote');
define('PRODUCT_NO_BUY','Der angezeigte Artikel kann derzeit nicht bestellt werden.');
define('TEXT_DATE_UPDATED', 'Aktualisiert am: %s');
define('WEIGHT', 'Versandgewicht');
define('ADMIN_TITLE_STATISTICS', 'Online:');
define('ADMIN_TITLE_STATISTICS', 'Online:');
define('ADMIN_EDIT_CONTENT', 'Content bearbeiten');
define('ADMIN_EDIT_BLOG_CAT', 'Blogkategorie bearbeiten');
define('ADMIN_EDIT_BLOG_ITEM', 'Blogbeitrag bearbeiten');
define('ADMIN_EDIT_CAT', 'Kategorie bearbeiten');
define('ADMIN_EDIT_ATTR', 'Attribute bearbeiten');
define('ADMIN_EDIT_CROSS', 'Cross Selling');
define('ADMIN_STATISTICS','Statistik');
define('ADMIN_CUSTOMERS','Kunden:');
define('ADMIN_PRODUCTS','Artikel:');
define('ADMIN_REVIEWS','Bewertungen:');

define('FILENAME_BILL', 'Rechnung');
define('FILENAME_PACKINSLIP', 'Lieferschein');
define('TEXT_PDF_SEITE', 'Seite');
define('TEXT_PDF_SEITE_VON', 'von');
define('TEXT_PDF_KUNDENNUMMER', 'Kunden-Nr.');
define('TEXT_PDF_RECHNUNGSNUMMER', 'Rechnungsnummer');
define('TEXT_PDF_LIEFERNUMMER', 'Lieferschein');
define('TEXT_PDF_BESTELLNUMMER', 'Bestellnummer');
define('TEXT_PDF_USTID', 'Ihre UST-ID');
define('TEXT_PDF_DATUM', 'Datum');
define('TEXT_PDF_ZAHLUNGSWEISE', 'Zahlungsweise');
define('TEXT_PDF_RECHNUNG', 'Rechnung');
define('TEXT_PDF_LIEFERSCHEIN', 'Lieferschein');
define('TEXT_PDF_MENGE', 'Menge');
define('TEXT_PDF_ARTIKEL', 'Artikel');
define('TEXT_PDF_ARTIKELNR', 'Artikel-Nr.');
define('TEXT_PDF_EINZELPREIS', 'Einzelpreis');
define('TEXT_PDF_PREIS', 'Gesamtpreis');
define('TEXT_PDF_KOMMENTAR', 'Kommentare');
define('TEXT_PDF_LIFERADRESSE', 'Lieferanschrift: ');
define('TEXT_PDF_INVOICE', 'Hinweis zur Rechnung: ');

define('text_pw_secure_0', 'sehr schwach');
define('text_pw_secure_1', 'schwach');
define('text_pw_secure_2', 'besser');
define('text_pw_secure_3', 'geht so');
define('text_pw_secure_4', 'gut');
define('text_pw_secure_5', 'sehr gut');
define('TEXT_ADVANCED_SEARCH', 'Erweiterte Suche');

define('ENTRY_DATE_OF_BIRTH_OLD', 'Sie haben nicht das Mindestalter zum Bestellen.');
define('ATTRIBUTE_ERROR', 'Bitte wählen sie eine Ausführung');
define('MAIN_BESTSELLER', 'Bestseller');
define('BLOG_PRODUCTS', 'passende Produkte');
define('IN_STOCK', 'auf Lager');
define('IN_W_STOCK', 'wenig auf Lager');
define('IN_N_STOCK', 'nicht auf Lager');
define('TEXT_MAXORDER','Bitte beachten Sie die Maximalbestellmenge für folgende Produkte');
define('TEXT_MAXORDER_TITLE','Maximalbestellmenge');
define('RMA_NUMBER', 'Nr.: ');
define('RMA_DATE', 'Datum: ');
define('NAVBAR_TITLE_RMA','RMA-Auftrag');
define('RMA_OVERVIEW_TEXT', 'Zur RMA-Übersicht');
define('RMA_TEXT_SHOW', 'Anzeigen');
define('RMA_TEXT_FROM', ' vom ');
define('RMA_PRODUCTS_PLEASE_SELECT','bitte wählen');
define('ENTRY_RMA_PRODUCTS','Bitte den zu reklamierenden Artikel auswählen!');
define('ENTRY_RMA_ERROR_MESSAGE','Bitte die Fehlerbeschreibung eingeben!');
define('ENTRY_RMA_ERROR_MESSAGE_LENGTH','Die Fehlerbeschreibung ist zu kurz!');
define('ENTRY_RMA_PRODUCTS_EAN','Bitte die Seriennummer des Produktes eintragen!');
define('ENTRY_RMA_REASON','Bitte den Grund Ihres Auftrages auswählen!');

define('SHIPPING_AUSLAND', '(Ausland abweichend)');
define('SHIPPING_AUSLAND_CART1', 'Für Lieferungen ausserhalb');
define('SHIPPING_AUSLAND_CART2', 'finden Sie hier Informationen zu Liefer- und Versandkosten.');

define('NAVBAR_TITLE_WITHDRAWAL', 'Widerrufsformular');
define('BUTTON_WITHDRAWAL', 'Online-Formular');
define('BUTTON_WITHDRAWAL_PDF', 'PDF-Download');
define('BUTTON_WITHDRAWAL_PDF_DOWNLOAD', ' Widerruf zum Download');

define('CHECKOUT_PAYMENT_ERROR','Bei der Bestellung ist ein Fehler aufgetreten. Bitte melden Sie sich einmal ab und leiten den Bestellvorgang erneut ein. Sollte dieser Fehler weiterhin bestehen, wenden Sie sich bitte an unseren Kundenservice.');
define('MAIN_TOP_PRODUCTS_DEFAULT','Unsere Top Produkte');

define('FREE_SHIPPING_TITLE','Versandkostenfrei');
define('FREE_SHIPPING_DESCRIPTION','Diese Artikel erhalten Sie von uns versandkostenfrei!');
define('SHIPPING_NO_COSTS_CART', 'für diese Artikel berechnen wir keine Versandkosten');
define('SHIPPING_NO_COSTS_PRODUCT', 'für diesen Artikel berechnen wir keine Versandkosten');
define('SHIPPING_MAXIMUM_CART','von diesem Produkt können Sie maximal {xx} Exemplare bestellen.');
define('PRODUCT_MAXIMUM_AMOUNT_EXCEEDED','Sie haben mehr als die erlaubte Menge eines Produktes in den Warenkorb gelegt. Die Menge wurde automatisch auf die mamximal erhältliche Anzahl reduziert!');
