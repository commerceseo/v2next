<?php
/*
	Change Global Product Price v1.4
	Copyright (C) 2005-2006 by Sergej Stroh.
	www.southbridge.de

 */

define('HEADING_TITLE','Globale Produktpreis&auml;nderung');
define('CONTENT_NOTE','<p>Legen Sie fest bei welcher Kategorie und Kundengruppe die Produktpreise aktualisiert werden sollen. Die Preise werden vollautomatisch ge&auml;ndert.<br><p><b>Bitte beachten Sie:</b></p>Der Skript kann nicht unterscheiden, welche Artikel doppelt verlinkt wurden. Es kann unter Umst&auml;nden passieren, dass einige Artikel mehrfach aktualisiert werden!</p>');
define('LEGENDE','<b>Legende:</b>');

define('CHANGE_ALL_PRODUCTS','Alle Artikel aktualisieren:');
define('CHANGE_ALL_MANUFACTURER','Alle Hersteller aktualisieren:');

define('CATEGORIES_ID','ID');
define('CATEGORIES_NAME','Kategorie');
define('PRODUCTS_COUNT','Artikelanzahl');
define('CUSTOMERS_GROUP','Kundengruppe');
define('PRODUCTS_PRICE_CHANGE','Preis&auml;nderung');
define('CATEGORIES_ACTION','Aktion');
define('CATEGORIES_DELETE','Entfernen');
define('PRODUCTS_PRICE_UPDATE','Aktualisieren');
define('UPDATE_ENTRY', 'Sollen die Preise wirklich aktualisiert werden?');

define('CUSTOMERS_GROUP_PRICE_ALL','Alle Gruppen');

define('LEGENDE_CUSTOMERS_GROUP','Auswahl der Kundengruppe, f&uuml;r die die Preise aktualisiert werden sollen. Allgemein - Entspricht der Standardpreisgruppe, gilt f&uuml;r alle.');
define('LEGENDE_PRODUCTS_PRICE_CHANGE','Eine Auswahl des Aktualisierungsverfahrens. Das Prozentzeichen (%) entspricht einer prozenttuallen Änderung der Preise, wobei der EUR-Wert (EUR) - in festen Werten.');

// Produktansicht
define('PRODUCTS_ID','ID');
define('PRODUCTS_MODEL','Art-Nr.');
define('PRODUCTS_NAME','Produkt');
define('PRODUCTS_PRICE','Preis');
define('PRODUCTS_SHIPPING_TIME','Lieferzeit');
define('PRODUCTS_QTY','Auf Lager');
define('PRODUCTS_SPECIALS', '<font color="#ff0000">Sonderangebot</font><br>(Preis, Anzahl, DD.MM.YYYY)');
define('PRODUCTS_SPECIALS_TAB', '<font color="#ff0000">Angebote erstellen</font>');
define('PRODUCTS_SPECIALS_DELETE_TAB', '<font color="#ff0000">Angebote entfernen</font>');
define('PRODUCTS_SPECIALS_TAB_TEXT', 'Erstellt neue Angebote f&uuml;r alle Produkte in dieser Kategorie, anhand der gesetzten Einstellungen.');
define('PRODUCTS_SPECIALS_DELETE_TAB_TEXT', 'Entfernt alle Angebote f&uuml;r alle Produkte in dieser Kategorie (NICHT vergessen Kundengruppe <b>Allgemein</b> auszuw&auml;hlen).');  
define('NAVIGATION_OVERVIEW','Zur Übersicht');

define('PRODUCTS_SPECIAL_DELETE_TAB', '<font color="#ff0000">Angebot entfernen</font>');
define('PRODUCTS_STAFFEL_TAB', 'Staffelpreise miteinbeziehen');
define('PRODUCTS_ATTRIBUTS_TAB', 'Attribute miteinbeziehen');
	
?>