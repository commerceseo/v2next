<?php
// Copyright (c) 2005 VARIO Software AG


  define('VARIO_NO_OF_ORDERS_PER_CYCLE', 1);			// Maximalanzahl zu exportierender Bestellungen pro Export-Zyklus  
  define('VARIO_CFL_TO_NAME123', 0);					// Company, Firstname, Lastname to NAME1, NAME2, NAME3, 
  														// Standard = 0: Bei Firmen wird der Vorname + Nachname in NAME2 geschrieben,
  														// bei Wert = 1 wird Firstname = NAME2, Lastname = NAME3 und Company = NAME1 
  														// nur bei Anrede = 'Firma'
												
												
  define('VARIO_ORDERS_EXPORT_STATUS', "1");			// Bestellstatus, bei denen Bestellungen abgeholt werden sollen
														// Standard = 1 (offen)
														// Mehrere Status können mit Komma ',' getrennt gelistet werden ("1, 35, 45")
												
  define('VARIO_LKZ_VOR_PLZ', 1);						// Standard = 1: Das LKZ wird in die Anschrift im Beleg geschrieben 
  														// = 0: Kein LKZ, sondern Land in der nä. Zeile (siehe bek_export)
  														// = 0: ist in VARIO VARIO_ANSCHRIFT_NACH_POSTVORSCHRIFT

  define('VARIO_FUELLE_BEP_LANGTEXT', 0);				// Szandard = 0; = 1 sorgt dafür, dass Attributwete in den Langtext der Belegposition geschrieben werden
  														// Sinnvoll bei Import alter nicht ausmultiplizierter Attribute und "Arbeitsartikeln"

  define('VARIO_ANREDE_PRIVATPERSON_BESTELLUNG', 1);	// Bei einer Bestellung haben wir in der Tabelle orders kein Geschlecht um die Anrede einer Privatperson (Herr/Frau) zu ermitteln.
														// Durch diesen Parameter können wir dafür sorgen, dass das Geschlecht zum Ermitteln der Anrede
														// über das Adressbuch des Kunden ermittelt wird (Geprüft wird Vorname,Name und PLZ)

  define('INTERPRET_OT_PAYMENT_AS_DISCOUNT', 1);		// Es gibt Zahlungsmodule, die in die Datenbanktabelle orders_total als "ot_payment" einen Rabatt eintragen
														// 1 = ot_payment entspricht Rabatt. 0 = ot_payment entspricht keinem Rabatt

  define('EXPORT_ORDERS_DELAY', 0);						// Wert in Sekunden. Bestellungen, die mind. X Sekunden alt sind abholen (z.B. Bei Zahlungsanbieter API-Verzögerung)

  define('ORDERSTOTAL_KEY_TO_BEP_ARTIKELNR', false);	// Durch definition eines Arrays können wir Aufschläge oder Abschläge einer Bestellung in der Tabelle orders_total
														// als virtuelle Belegpositionen über den BEP-Export Faken. Ein Beispiel-Array könnte z.B. so aussehen:
														// Beispiel:
														// 	serialize( 
														// 		array( 'ot_payment' => array(
														// 								'ARTIKELNR' 	=>'ZAHLUNGSART'
														// 							  , 'BEZEICHNUNG'	 => 'Zugschlag für Zahlungsart'
														// 								)
														// 			, 'ot_shippingfee' => array(
														// 								'ARTIKELNR' 	=>'ZOLLABWICKLUNG'
														// 							  , 'BEZEICHNUNG'	 => 'Gebühren für Zollabwicklung'
														// 								)
														// 				)
														// 	)
  
?>
