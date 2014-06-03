<?php

define('PDF_HEADER_TITLE', 'PDF Konfigurator für Rechnung und Lieferschein');
define('TEXT_PDF_HEADER_TITLE', 'Kopfzeile');
define('TEXT_PDF_HEADER_DESC', 'Die Kopfzeile auf dem PDF Dokument.');

define('TEXT_PDF_ANSCHRIFT_TITLE', 'Absenderadresse Footer');
define('TEXT_PDF_ANSCHRIFT_DESC', 'Die Absenderadresse des Shops.');

define('TEXT_PDF_KONTAKT_TITLE', 'Kontaktdaten Footer');
define('TEXT_PDF_KONTAKT_DESC', 'Ihre Kontaktdaten am unteren Rand des PDF-Dokumentes.');

define('TEXT_PDF_BANK_TITLE', 'Bankinformationen Footer');
define('TEXT_PDF_BANK_DESC', 'Ihre Bankinformationen am unteren Rand des PDF-Dokumentes.');

define('TEXT_PDF_GESCHAEFT_TITLE', 'Gesch&auml;ftsinformationen');
define('TEXT_PDF_GESCHAEFT_DESC', 'Ihre Gesch&auml;ftsinformationen am unteren Rand des PDF-Dokumentes.');

define('TEXT_PDF_SHOPADRESSEKLEIN_TITLE', 'Shopadresse klein');
define('TEXT_PDF_SHOPADRESSEKLEIN_DESC', 'Diese Adresse steht klein über dem Adressfeld in einer Zeile');

define('TEXT_PDF_DANKE_MANN_TITLE', 'Anrede "Herr"');
define('TEXT_PDF_DANKE_MANN_DESC', 'Wie sollen die Herren angesprochen werden?');

define('TEXT_PDF_DANKE_FRAU_TITLE', 'Anrede "Damen"');
define('TEXT_PDF_DANKE_FRAU_DESC', 'Wie sollen die Damen angesprochen werden?');

define('TEXT_PDF_DANKE_UNISEX_TITLE', 'Anrede Unisex');
define('TEXT_PDF_DANKE_UNISEX_DESC', 'Wenn das Geschlecht nicht ermittelt werden kann wird diese Anrede genutzt.');

define('TEXT_PDF_SCHLUSSTEXT_TITLE', 'Abschlusstext');
define('TEXT_PDF_SCHLUSSTEXT_DESC', 'Dieser Text erscheint unterhalb der Produktauflistung.');

define('TEXT_PDF_FILE_NAME_TITLE', 'Dateiname');
define('TEXT_PDF_FILE_NAME_DESC', 'Der Namen der erzeugten PDF Rechnung.<br>Folgende Variablen k&ouml;nnen genutzt werden:<br><b>#bn#</b> - BestellNr. / <b>#rn#</b> - fortlaufende Rechnungsnummer / <b>#vn#</b> - Vorname des Kunden / <b>#nn#</b> - Nachname des Kunden / <b>#d#</b> - das aktuelle Datum');
		
// Lieferschein
define('TEXT_PDF_DELIVERY_SCHLUSSTEXT_TITLE', 'Abschlusstext Lieferschein');
define('TEXT_PDF_DELIVERY_SCHLUSSTEXT_DESC', 'Dieser Text erscheint unterhalb der Produktauflistung.<br>Folgende Variable kann genutzt werden:<br><b>#vm#</b> - Versandart.<br>');

define('TEXT_PDF_DELIVERY_FILE_NAME_TITLE', 'Lieferschein');
define('TEXT_PDF_DELIVERY_FILE_NAME_DESC', 'Der Namen des erzeugten PDF Lieferscheins.<br>Folgende Variablen k&ouml;nnen genutzt werden:<br><b>#bn#</b> - BestellNr. / <b>#vn#</b> - Vorname des Kunden / <b>#nn#</b> - Nachname des Kunden / <b>#d#</b> - das aktuelle Datum');

define('LAYOUT_LEFT_MARGIN_TITLE', 'Abstand links');
define('LAYOUT_LEFT_MARGIN_DESC', 'Abstand von links in mm (Standard = 20).');

define('LAYOUT_TOP_MARGIN_TITLE', 'Abstand oben');
define('LAYOUT_TOP_MARGIN_DESC', 'Abstand von oben in mm (Standard = 10).');

define('LAYOUT_LOGO_X_TITLE', 'Logo Abstand links');
define('LAYOUT_LOGO_X_DESC', 'Abstand des Logo von links in mm (Standard = 120).');

define('LAYOUT_LOGO_Y_TITLE', 'Logo Abstand oben');
define('LAYOUT_LOGO_Y_DESC', 'Logo Abstand von oben in mm (Standard = 30).');

define('LAYOUT_LOGO_DPI_TITLE', 'Logo DPI');
define('LAYOUT_LOGO_DPI_DESC', 'Auflösung des Logo, höhere Werte = schärfer, aber auch kleiner (Standard = 80).');

define('LAYOUT_LOGO_FILE_TITLE', 'Logo Datei');
define('LAYOUT_LOGO_FILE_DESC', 'Der Dateiname vom Logo, muss im aktuellem Template im Ordner img liegen (Standard = logo.jpg).');

define('LAYOUT_LEFT_TEXTOFFSET_TITLE', 'Abstand links');
define('LAYOUT_LEFT_TEXTOFFSET_DESC', 'Der Abstand des Textes von links (Standard = 20).');

define('LAYOUT_FOOTER_Y_TITLE', 'Abstand Footer');
define('LAYOUT_FOOTER_Y_DESC', 'Abstand des Footer von unten (Standard = -35).');

define('LAYOUT_ADDRESSWINDOWMAXLEN_TITLE', 'Adressfenster Größe');
define('LAYOUT_ADDRESSWINDOWMAXLEN_DESC', 'Größe des Adressfester (Standard = 80).');

define('LAYOUT_ADDRESSWINDOWTOP_TITLE', 'Abstand Adressfenster oben');
define('LAYOUT_ADDRESSWINDOWTOP_DESC', 'Der Abstand des Adressfenster von oben (Standard = 50).');

define('LAYOUT_FONTFAMILY_TITLE', 'Font');
define('LAYOUT_FONTFAMILY_DESC', 'Standard Font für PDF Rechnung (Standard = helvetica). Verfügbare Schriften: courier, helvetica, times');

define('LAYOUT_RECHNUNGSDATEN_X_TITLE', 'Rechnungsblock Abstand links');
define('LAYOUT_RECHNUNGSDATEN_X_DESC', 'Abstand des Rechnungsblock, wie Rechnungsnummer, UST-ID etc. von links (Standard = 130).');

define('LAYOUT_RECHNUNGSDATEN_Y_TITLE', 'Rechnungsblock Abstand oben');
define('LAYOUT_RECHNUNGSDATEN_Y_DESC', 'Abstand von oben des Rechnungsblock (Standard = 55).');

define('LAYOUT_RECHNUNG_START_TITLE', 'Beginn Rechnungdaten');
define('LAYOUT_RECHNUNG_START_DESC', 'Abstand der Rechnungsdaten von oben (Standard = 100).');

define('LAYOUT_MENGE_LEN_TITLE', 'Artikel Menge');
define('LAYOUT_MENGE_LEN_DESC', 'Breite des Mengefeld (Standard = 18).');

define('LAYOUT_ARTIKEL_LEN_TITLE', 'Artikel Name');
define('LAYOUT_ARTIKEL_LEN_DESC', 'Breite des Artikel Name Feldes (Standard = 82).');

define('LAYOUT_ARTIKELNR_LEN_TITLE', 'Breite Artikel Nummer');
define('LAYOUT_ARTIKELNR_LEN_DESC', 'Breite des Artikel Nummer Feldes (Standard = 20).');

define('LAYOUT_EINZELPREIS_LEN_TITLE', 'Breite Einzelpreis');
define('LAYOUT_EINZELPREIS_LEN_DESC', 'Breite des Einzelpreis Feldes (Standard = 28).');

define('LAYOUT_PREIS_LEN_TITLE', 'Breite der Summe');
define('LAYOUT_PREIS_LEN_DESC', 'Breite des Summen Feldes (Standard = 28).');

define('TEXT_PDF_INVOICE_TEXT_TITLE', 'Text für Rechnung');
define('TEXT_PDF_INVOICE_TEXT_DESC', 'Geben Sie hier den Text für die Zahlart Rechnung ein.');

define('_TITLE', '');
define('_DESC', ' (Standard = 80).');

define('_TITLE', '');
define('_DESC', ' (Standard = 80).');
