<?php
// Copyright (c) 2009 VARIO Software AG

  // Liste der Tabellen, die Importiert werden sollen
  // Hier kann gezielt nacheinander der Import der Tabellen freigeschaltet werden
  $valid_tables = array();
  $valid_tables[] = 'par';			// VARIO-Tabelle PAR: 		Beschriftungen von Feldern
  $valid_tables[] = 'spr';			// VARIO-Tabelle SPR: 		Sprachen
  $valid_tables[] = 'adr';			// VARIO-Tabelle ADR:		Adressen
  $valid_tables[] = 'kat';			// VARIO-Tabelle KAT:		Kategorien, Systemsprache
  $valid_tables[] = 'kat_spr';		// VARIO-Tabelle KAT_SPR:	Kategorien, weitere Sprachen
  $valid_tables[] = 'hst';			// VARIO-Tabelle HST:		Hersteller
  $valid_tables[] = 'art';			// VARIO-Tabelle ART:		Artikel, Systemsprache
  $valid_tables[] = 'aat';			// VARIO-Tabelle AAT:		Artikeltexte, Webshoptexte und weitere Sprachen
  $valid_tables[] = 'art_xse';		// VARIO-Tabelle ART_XSE:	Cross-Selling-Artikel
  $valid_tables[] = 'xse_grp';		// VARIO-Tabelle ART_XSE_GRP: VARIO Cross-Selling-Gruppen
  $valid_tables[] = 'ap1';			// VARIO-Tabelle AP1:		Mengenabh. Preise
  $valid_tables[] = 'ap3';			// VARIO-Tabelle AP3:		Gruppenpreise
  $valid_tables[] = 'ap4';			// VARIO-Tabelle AP4:		Gruppenrabatte (Ausgerechnet für Shop)
  $valid_tables[] = 'ap5';			// VARIO-Tabelle AP5:		Mengenabh. Gruppenpreise
  $valid_tables[] = 'ap6';			// VARIO-Tabelle AP6:		Mengenabh. Staffelrabatte
  $valid_tables[] = 'ard';			// VARIO-Tabelle ARD:		Dateien
  $valid_tables[] = 'itm';			// VARIO-Tabelle ITM:		Artikel-Kategorie-Zuordnmung
  $valid_tables[] = 'gfx';			// VARIO-Tabelle GFX:		Bilder
  $valid_tables[] = 'bek';			// VARIO-Tabelle BEK:		Belegköpfe
  $valid_tables[] = 'vet';			// VARIO-Tabelle VET:		VARIO Vertreter
  $valid_tables[] = 'art_ff';		// VARIO-Tabelle ART_FF:	VARIO Freie Felder
  
  
  define('VARIO_XTC_ORDER_FINISHED', 3);						// Welcher Status ist der End-Status einer Bestellung? Siehe orders_status

  define('VARIO_PREIS_RUNDEN', 0.05);							// Bei Preisdifferenzen <= diese = 0 setzen
  
  define('VARIO_ARTIKEL_ANG_AM', 'SYSTEM');						// Verhalten des Erstellungdatum einstellen:
  																// SYSTEM (Standard): 	INSERT eines Artikel nutzt das Systemdatum
  																// VARIO: 				INSERT eines Artikel nutzt das Feld ANG_AM 
	
  define('VARIO_CUSTOMERS_STATUS_BY_ABC_KENNUNG', 0);			// Kundengruppe in xtc wird zur ABC-Gruppe gemapped
  
  define('VARIO_ATTRIBUTES_EAN_FIELD', '');       				//In dieses Feld von products_attributes wird die EAN pro Variante geschrieben (leer=EAN wird nicht in products_attributes gespeichert)
  //define('VARIO_ATTRIBUTES_EAN_FIELD', 'attributes_ean');
  
  define('VARIO_ARIKELANZAHL_FELD', 'VERFUEGBAR');				// mögliche Werte: VERFUEGBAR (=BESTAND-RESERVIERT), BESTAND 

  define('GAMBIOGX2_PROPERTIES_COMBIS_PRICEMODE', 'NOFIX');		// FIX = Fixpreis; NOFIX = Aufschlag oder Abschlag

  define('TABLE_VARIO_FF', 'vario_art_ff');
  
  define('VARIO_ATTR_SORT_FELD', 'pa.attributes_model');		// MASTER-SLAVE - Atribute sortieren nach
  //define('VARIO_ATTR_SORT_FELD', 'pa.products_attributes_id');		// MASTER-SLAVE - Atribute sortieren nach
  																// pa.attributes_model, vart.s01, pa.sortorder 
  
  define('VARIO_ART_MAX_SXX_ATT_NUMMER', 10);					// Maximale Anzahl von Sxx-Attributen
  
  define('VARIO_KEINE_SLAVE_ATTRIBUTE', 0);						// Trotz MASTER-SLAVE in VARIO alle Artikel normal ausspielen
  																// keine Attribute im Shop erzeugen

  define('VARIO_AKTIONSPREIS_MENGE_BERUECKSICHTIGEN', 0);		// Ob ein Aktionspreis immer verfügbar sein soll oder Verfügbarkeit = Artikelmenge


  define('TABLE_VARIO_VET', 'vario_vet');
?>
