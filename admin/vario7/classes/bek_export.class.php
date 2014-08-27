<?php
/**
 * @version $Id: bek_export.class.php,v 1.4 2011-07-25 13:54:57 ag Exp $
 * @version $Revision: 1.4 $
 * @copyright Copyright (c) 2005 VARIO Software GmbH
 *
 * Versionshistorie
 *
 
 $Log: not supported by cvs2svn $
 Revision 1.3  2011/07/20 13:37:31  ag
 *** empty log message ***

 Revision 1.2  2011/07/18 14:27:48  ag
 -Versions-Informationen werden nun exportiert
 -Adressen werden nun über die Kunden-Nr. identifiziert und alternativ über die E-Mail-Adresse

 Revision 1.1  2011/07/15 12:33:33  ag
 -neues Modul erstellt

 Revision 1.12  2011/05/04 10:57:59  ag
 - Rabatt-Texte werden nun von HTML dekodiert, damit kein HTML-Code in VARIO ankommt

 * 22.10.2013 SV:
 *   INTERPRET_OT_PAYMENT_AS_DISCOUNT
 * 13.06.2013 SV:
 *   USTNR in bek mitexportieren.
 * 12.04.2013 SV:
 *   ANREDE, LS_ANREDE, RE_ANREDE auch bei Netto-Export für 7.3 mitselektieren (Bisher nur bei Brutto)
 * 26.03.2013 SV:
 *   ANREDE, LS_ANREDE, RE_ANREDE für 7.3 mitselektieren
 * 28.02.2013 SV:
 *   Korrektur Nachnahme -> Nachname
 * 16.11.2012 SV:
 *   OT_DISCOUNT schreibt in die orders_total negative Werte, VARIO erwartet positive. Dies wird nun abgefangen.
 * 15.11.2012 SV:
 *   OT_COUPON schreibt in die orders_total negative Werte, VARIO erwartet positive. Dies wird nun abgefangen.
 * 13.11.2012 SV:
 *   WAEHRUNG -> o.currency mit übergeben
 * 26.09.2012 SV:
 *   Für 7.3: Qualifizierte Adressfelder
 * 29.05.2012 SV:
 *   Bei einer Bestellung haben wir in der Tabelle orders kein Geschlecht um die Anrede einer Privatperson (Herr/Frau) zu ermitteln.
 *   Durch den Parameter VARIO_ANREDE_PRIVATPERSON_BESTELLUNG können wir nun dafür sorgen, dass das Geschlecht
 *   über das Adressbuch des Kunden ermittelt wird (Geprüft wird Vorname, Name und PLZ)
 * 02.02.2012 SV:
 *   Bei select_orders_data auch "o.delivery_suburb" mitselektieren und evtl. in Zukunft in get_address_line() in ZEILE6 den Adresszusatz ausgeben
 * 11.01.2012 SV:
 *   Bei $this->getOhneMwst und $this->getFakturierungsart() wird nun die Bestellnummer übergeben, damit diese Information aufgrund der Versandadresse geliefert wird
 * 10.01.2012 SV:
 *   $this->getFakturierungsart() in Methode $this->getRabatt2() nicht ermittelbar da nur über Kunden ermittelbar, 
 *   daher die zu beginn ermittelte Fakturierungsart der Methode übergeben
 * 09.09.2011 SV:
 *   Hook VARIO_HOOK_SP_GET_FRACHTKOSTEN in bek_export.class.php eingebaut.
 * 18.01.2011 AB:
 *   Beachtung des xtc:Moduls Trusted-Shop-Käuferschutz (ot_ts_schutz), getTrustedShopKS
 * 14.12.2010 AB:
 *   Nachnahme-Lieferbedingung nach altem 6er-Verfahren zusamennbauen: Bsp.: flat-cod, siehe getLieferbedingung  
 * 08.12.2010 AB:
 *   Drittländer wurden Netto + MwSt. statt nur Netto berechnet. vario_export.class.php
 * 09.10.2010 AB
 *   VARIO_LKZ_VOR_PLZ, LKZ oder Land in Belegadresse, Pseudofeld customers_country_iso_code_2 
 * 22.08.2010 AB
 *   Default-Preisgruppen-Fallback war falsch: jetzt Default = 1 (Standardpreisgruppe in VARIO)
 *   Sollte die Stadardpreisgruppe anders als 1 sein, dann
 *    insert into configuration (configuration_key, configuration_value, configuration_group_id ) values ('VARIO_PG=1', 'Kunde', 91)
 * 02.04.2010 AB 
 *   Preisgruppe jetzt über Gruppennamen ermittelt
 */

include_once('../inc/xtc_db_query.inc.php');
include_once('vario7/html2text.inc.php');

class bek_export extends vario_export {
 	var $ot_data = array();
 	 	
	function bek_export($orders_id, $customers_id) {
	

	
		_debug($customers_id.'-'.$orders_id, 'START bek_export');
		
		$this->set_class_name('BEK');
		$this->set_file_name('BEK_o' . $orders_id);

		// getFakturierungsart und getOhneMwst leider nur über den Customer ermittelbar
		// $this->ds = $this->select_orders_data_use_customers_data($customers_id, $orders_id);  das war hier falsch
		$this->ds = $this->select_customers_data($customers_id, $orders_id);

		// Bestellnummer muss übergeben werden, damit die Fakturierungsart und OhneMwst aus der Lieferadresse der Bestellung ermittelt wird
		$fa = $this->getFakturierungsart( $orders_id ); 
		$om = $this->getOhneMwst( $orders_id );

		// AB: 	bei Brutto Bruttofelder füllen, bei Netto Nettofelder füllen
		// 		der Preis bei xtc ist der in der aktuellen Fakturierungsart

		// fetch the data!
		// STANDARD
		$this->ds 	   = $this->select_orders_data($orders_id);

		// *** SPEZIAL ***: Adressdaten werden mit denen aus der address_book überschrieben (anderes select), Notplan bei alten vermurksten Adressdaten in der orders
		// $this->ds = $this->select_orders_data_use_customers_data($customers_id, $orders_id);  // ... und wäre hier richtig

		$this->ot_data = $this->getOrdersTotal($orders_id);
		$this->build_address_lines();							// Baut Adress-Array auf
		// _debug($this->ot_data, '    --> ot_data');
				
		if ($fa != 0) {	// <> 0 ist nicht Netto, also Brutto
			_debug($fa, '      bek_export-brutto');
			$this->set_fields(
				array('export_view' =>
					array('fields' =>
						array(
						create_field_info('orders_id', 					'EXT_REFERENZ'),
						create_field_info('orders_id', 					'BELEGNR', 			'', 'get_belegnr'),
						create_field_info('', 							'BELEGART', 		'00'),
						create_field_info('orders_id', 					'BELEGSCHLUESSEL', 	'', 'get_belegschluessel'),
						create_field_info('orders_id', 					'BETREFF'),
						create_field_info('customers_id', 				'WEB_ADRESSNR'),
						create_field_info('customers_cid', 				'KUNDENNR',			'',	'get_kundennr($this->ds)'),
						
						create_field_info('', 							'NAMEZEILE1', 		'', '$this->get_address_line(11)'),
						create_field_info('', 							'NAMEZEILE2', 		'', '$this->get_address_line(12)'),
						create_field_info('', 							'NAMEZEILE3', 		'', '$this->get_address_line(13)'),
						create_field_info('', 							'NAMEZEILE4', 		'', '$this->get_address_line(14)'),
						create_field_info('', 							'NAMEZEILE5', 		'', '$this->get_address_line(15)'),
						create_field_info('', 							'NAMEZEILE6', 		'', '$this->get_address_line(16)'),
						create_field_info('', 							'NAMEZEILE7', 		'', '$this->get_address_line(17)'),

						// Neu 7.3 START Kunde
						create_field_info('', 								'ANREDE', 			'', '$this->get_address_line(11)'),
						create_field_info('customers_company', 				'FIRMA'),
						create_field_info('customers_firstname', 			'VORNAME'),
						create_field_info('customers_lastname', 			'NACHNAME'),
						create_field_info('customers_street_address',		'STRASSE'),
						create_field_info('customers_postcode',		 		'PLZ'),
						create_field_info('customers_city',		 			'ORT'),
						create_field_info('customers_country_iso_code_2', 	'LKZ'),
						create_field_info('customers_country',		 		'LAND'),
						// Neu 7.3 ENDE

						create_field_info('customers_telephone', 		'TELEFON1'),
						create_field_info('customers_email_address', 	'EMAIL'),
						create_field_info('customers_email_address', 	'WEB_EMAIL'),
						create_field_info('customers_vat_id', 			'USTNR'),
						
						create_field_info('', 							'LSNAME1', 			'', '$this->get_address_line(21)'),
						create_field_info('', 							'LSNAME2', 			'', '$this->get_address_line(22)'),
						create_field_info('', 							'LSNAME3', 			'', '$this->get_address_line(23)'),
						create_field_info('', 							'LSNAME4', 			'', '$this->get_address_line(24)'),
						create_field_info('', 							'LSNAME5', 			'', '$this->get_address_line(25)'),
						create_field_info('', 							'LSNAME6', 			'', '$this->get_address_line(26)'),
						create_field_info('', 							'LSNAME7', 			'', '$this->get_address_line(27)'),

						// Neu 7.3 START Lieferanschrift
						create_field_info('', 								'LS_ANREDE', 			'', '$this->get_address_line(21)'),
						create_field_info('delivery_company', 				'LS_FIRMA'),
						create_field_info('delivery_firstname', 			'LS_VORNAME'),
						create_field_info('delivery_lastname', 				'LS_NACHNAME'),
						create_field_info('delivery_street_address',		'LS_STRASSE'),
						create_field_info('delivery_postcode',		 		'LS_PLZ'),
						create_field_info('delivery_city',		 			'LS_ORT'),
						create_field_info('delivery_country_iso_code_2', 	'LS_LKZ'),
						create_field_info('delivery_country',		 		'LS_LAND'),
						// Neu 7.3 ENDE

						create_field_info('', 							'RENAME1', 			'', '$this->get_address_line(31)'),
						create_field_info('', 							'RENAME2', 			'', '$this->get_address_line(32)'),
						create_field_info('', 							'RENAME3', 			'', '$this->get_address_line(33)'),
						create_field_info('', 							'RENAME4', 			'', '$this->get_address_line(34)'),
						create_field_info('', 							'RENAME5', 			'', '$this->get_address_line(35)'),
						create_field_info('', 							'RENAME6', 			'', '$this->get_address_line(36)'),
						create_field_info('', 							'RENAME7', 			'', '$this->get_address_line(37)'),

						// Neu 7.3 START Rechnungsanschrift
						create_field_info('', 								'RE_ANREDE', 			'', '$this->get_address_line(31)'),
						create_field_info('billing_company', 				'RE_FIRMA'),
						create_field_info('billing_firstname', 				'RE_VORNAME'),
						create_field_info('billing_lastname', 				'RE_NACHNAME'),
						create_field_info('billing_street_address',			'RE_STRASSE'),
						create_field_info('billing_postcode',		 		'RE_PLZ'),
						create_field_info('billing_city',		 			'RE_ORT'),
						create_field_info('billing_country_iso_code_2', 	'RE_LKZ'),
						create_field_info('billing_country',		 		'RE_LAND'),
						// Neu 7.3 ENDE
				
						create_field_info('', 							'BELEGSTATUS', 		'1'),
						create_field_info('date_purchased', 			'BESTELLUNGVOM', 	'', 'xtc_date_to_vario_date'),

						create_field_info('currency', 					'WAEHRUNG'),
						create_field_info('', 							'FAKTURIERUNGSART', $fa),
						create_field_info('', 							'OHNEMWST', 		$om),
						create_field_info('PREISGRUPPE',				'PREISGRUPPE', 		'1'),
						
						// create_field_info('', 						'BETRAGNETTO',  	'0', '$this->getBetrag()'),
						create_field_info('', 							'BETRAGBRUTTO', 	'0', '$this->get_total_value(\'ot_total\')'),
						// RABATTx: Rabattprozenzangabe, z.B. 5%
						create_field_info('', 							'RABATT1', 	  		'0', '$this->getRabatt1()'),
						create_field_info('', 							'RABATT1TEXT',  	'0', '$this->getRabatt1Text()'),
						// RABATTxBETRAG: der errechnete Betrag, z.B. 10.- als Wert von 20% von 50.-
						create_field_info('', 							'RABATT1BETRAG', 	'0', '$this->getRabatt1betrag()'),

						create_field_info('', 							'RABATT2', 	  		'0', '$this->getRabatt2('. $fa .')'), // Fakturierungsart übergeben
						create_field_info('', 							'RABATT2TEXT',  	'0', '$this->getRabatt2Text()'),
						create_field_info('', 							'RABATT2BETRAG', 	'0', '$this->getRabatt2betrag()'),
						// TrustedShop-Käuferschutz in Versicherung 
						create_field_info('', 							'VERSICHERUNGBRUTTO',	'0', '$this->getTrustedShopKS()'),
						
						// create_field_info('', 						'FRACHTKOSTEN', 	'0'),
						create_field_info('', 							'FRACHTKOSTENBRUTTO', 	'0', '$this->getFrachtkosten()'),
						create_field_info('', 							'FRACHTKOSTENMANUELL',	'J'),
						// create_field_info('', 						'NACHNAHME', 		'0'),
						create_field_info('', 							'NACHNAHMEBRUTTO', 	'0', '$this->getNachnahme()'),

						create_field_info('payment_class', 				'ZAHLART'),
						create_field_info('', 							'LIEFERBEDINGUNG', 	'', '$this->getLieferbedingung($this->ds)'),
						create_field_info('comments', 					'BEMERKUNG', 		'', '$this->getBemerkung($this->ds)'),
						create_field_info('orders_id', 					'EIGENEBEMERKUNG', 	'', '$this->getOrdersStatusHistoryComments'),

						create_field_info('BELEG_KUNDE',				'BELEG_KUNDE', 		'BELEG', 	'get_beleg_report'),
						create_field_info('date_purchased', 			'BESTELLT_AM', 		'', 		'xtc_date_to_vario_date'),
						create_field_info('date_purchased', 			'ANG_AM', 			'', 		'xtc_date_to_vario_date'),
						create_field_info('', 							'ANG_VON', 			'BEB'),
						create_field_info('', 							'TRANSAKTIONSTYP', 	'0'),
						create_field_info('orders_id', 					'TRANSAKTIONSNUMMER'),
						create_field_info('', 				'VERSION', 	$this->getVARIOCfgKey('VARIO_VERSION_TAG')),
						create_field_info('', 				'VERSION_INFO', VARIO_SHOP_USED)
						),
					)
				)
			);
		} else {
			_debug($fa, '      bek_export-netto');
			$this->set_fields(
				array('export_view' =>
					array('fields' =>
						array(
						create_field_info('orders_id', 					'WEB_ID'),
						create_field_info('orders_id', 					'BELEGNR', 			'', 'get_belegnr'),
						create_field_info('', 							'BELEGART', 		'00'),
						create_field_info('orders_id', 					'BELEGSCHLUESSEL', 	'', 'get_belegschluessel'),
						create_field_info('orders_id', 					'BETREFF'),
						create_field_info('customers_id', 				'WEB_ADRESSNR'),
						create_field_info('customers_cid', 				'KUNDENNR',			'',	'get_kundennr($this->ds)'),
						
						create_field_info('', 							'NAMEZEILE1', 		'', '$this->get_address_line(11)'),
						create_field_info('', 							'NAMEZEILE2', 		'', '$this->get_address_line(12)'),
						create_field_info('', 							'NAMEZEILE3', 		'', '$this->get_address_line(13)'),
						create_field_info('', 							'NAMEZEILE4', 		'', '$this->get_address_line(14)'),
						create_field_info('', 							'NAMEZEILE5', 		'', '$this->get_address_line(15)'),
						create_field_info('', 							'NAMEZEILE6', 		'', '$this->get_address_line(16)'),
						create_field_info('', 							'NAMEZEILE7', 		'', '$this->get_address_line(17)'),

						// Neu 7.3 START Kunde
						create_field_info('', 								'ANREDE', 		'', '$this->get_address_line(11)'),
						create_field_info('customers_company', 				'FIRMA'),
						create_field_info('customers_firstname', 			'VORNAME'),
						create_field_info('customers_lastname', 			'NACHNAME'),
						create_field_info('customers_street_address',		'STRASSE'),
						create_field_info('customers_postcode',		 		'PLZ'),
						create_field_info('customers_city',		 			'Ort'),
						create_field_info('customers_country_iso_code_2', 	'LKZ'),
						create_field_info('customers_country',		 		'LAND'),
						// Neu 7.3 ENDE
		
						create_field_info('customers_telephone', 		'TELEFON1'),
						create_field_info('customers_email_address', 	'EMAIL'),
						create_field_info('customers_email_address', 	'WEB_EMAIL'),
						create_field_info('customers_vat_id', 			'USTNR'),
						
						create_field_info('', 							'LSNAME1', 			'', '$this->get_address_line(21)'),
						create_field_info('', 							'LSNAME2', 			'', '$this->get_address_line(22)'),
						create_field_info('', 							'LSNAME3', 			'', '$this->get_address_line(23)'),
						create_field_info('', 							'LSNAME4', 			'', '$this->get_address_line(24)'),
						create_field_info('', 							'LSNAME5', 			'', '$this->get_address_line(25)'),
						create_field_info('', 							'LSNAME6', 			'', '$this->get_address_line(26)'),
						create_field_info('', 							'LSNAME7', 			'', '$this->get_address_line(27)'),

						// Neu 7.3 START Lieferanschrift
						create_field_info('', 								'LS_ANREDE', 	'', '$this->get_address_line(21)'),
						create_field_info('delivery_company', 				'LS_FIRMA'),
						create_field_info('delivery_firstname', 			'LS_VORNAME'),
						create_field_info('delivery_lastname', 				'LS_NACHNAME'),
						create_field_info('delivery_street_address',		'LS_STRASSE'),
						create_field_info('delivery_postcode',		 		'LS_PLZ'),
						create_field_info('delivery_city',		 			'LS_Ort'),
						create_field_info('delivery_country_iso_code_2', 	'LS_LKZ'),
						create_field_info('delivery_country',		 		'LS_LAND'),
						// Neu 7.3 ENDE

						create_field_info('', 							'RENAME1', 			'', '$this->get_address_line(31)'),
						create_field_info('', 							'RENAME2', 			'', '$this->get_address_line(32)'),
						create_field_info('', 							'RENAME3', 			'', '$this->get_address_line(33)'),
						create_field_info('', 							'RENAME4', 			'', '$this->get_address_line(34)'),
						create_field_info('', 							'RENAME5', 			'', '$this->get_address_line(35)'),
						create_field_info('', 							'RENAME6', 			'', '$this->get_address_line(36)'),
						create_field_info('', 							'RENAME7', 			'', '$this->get_address_line(37)'),

						// Neu 7.3 START Rechnungsanschrift
						create_field_info('', 								'LS_ANREDE', 	'', '$this->get_address_line(31)'),
						create_field_info('billing_company', 				'RE_FIRMA'),
						create_field_info('billing_firstname', 				'RE_VORNAME'),
						create_field_info('billing_lastname', 				'RE_NACHNAME'),
						create_field_info('billing_street_address',			'RE_STRASSE'),
						create_field_info('billing_postcode',		 		'RE_PLZ'),
						create_field_info('billing_city',		 			'RE_Ort'),
						create_field_info('billing_country_iso_code_2', 	'RE_LKZ'),
						create_field_info('billing_country',		 		'RE_LAND'),
						// Neu 7.3 ENDE
						
						create_field_info('', 							'BELEGSTATUS', 		'1'),
						create_field_info('date_purchased', 			'BESTELLUNGVOM', 	'', 'xtc_date_to_vario_date'),

						create_field_info('', 							'FAKTURIERUNGSART', $fa),
						create_field_info('', 							'OHNEMWST', 		$om),
						create_field_info('PREISGRUPPE',				'PREISGRUPPE', 		'1'),
						
						// create_field_info('', 							'BETRAGNETTO', 		'0', '$this->get_total_value(\'ot_total_no_tax\')'), // ???
						create_field_info('', 							'BETRAGNETTO', 		'0', '$this->get_total_value(\'ot_total\')'),
						// create_field_info('', 						'BETRAGBRUTTO', 	'0', '$this->get_total_value(\'ot_total\')'),
						create_field_info('', 							'RABATT1', 	  		'0', '$this->getRabatt1()'),
						create_field_info('', 							'RABATT1TEXT',  	'0', '$this->getRabatt1Text()'),
						create_field_info('', 							'RABATT1BETRAG', 	'0', '$this->getRabatt1betrag()'),
						create_field_info('', 							'RABATT2', 	  		'0', '$this->getRabatt2('. $fa .')'), // Fakturierungsart übergeben
						create_field_info('', 							'RABATT2TEXT',  	'0', '$this->getRabatt2Text()'),
						create_field_info('', 							'RABATT2BETRAG', 	'0', '$this->getRabatt2betrag()'),

						// TrustedShop-Käuferschutz in Versicherung 
						create_field_info('', 							'VERSICHERUNG',		'0', '$this->getTrustedShopKS()'),
						
						create_field_info('', 							'FRACHTKOSTEN', 	'0', '$this->getFrachtkosten()'),
						create_field_info('', 							'FRACHTKOSTENBRUTTO', 	'0', '$this->getFrachtkosten()'),
						create_field_info('', 							'FRACHTKOSTENMANUELL',	'J'),
						create_field_info('', 							'NACHNAHME', 		'0', '$this->getNachnahme()'),
						// create_field_info('', 						'NACHNAHMEBRUTTO', 	'0'),

						create_field_info('payment_class', 				'ZAHLART'),
						create_field_info('', 							'LIEFERBEDINGUNG',  '', '$this->getLieferbedingung($this->ds)'),
						create_field_info('comments', 					'BEMERKUNG', 		'', '$this->getBemerkung($this->ds)'),
						create_field_info('orders_id', 					'EIGENEBEMERKUNG', 	'', '$this->getOrdersStatusHistoryComments'),

						create_field_info('BELEG_KUNDE',				'BELEG_KUNDE', 		'BELEG', 	'get_beleg_report'),
						create_field_info('date_purchased', 			'BESTELLT_AM', 		'', 		'xtc_date_to_vario_date'),
						create_field_info('date_purchased', 			'ANG_AM', 			'', 		'xtc_date_to_vario_date'),
						create_field_info('', 							'ANG_VON', 			'BEN'),
						create_field_info('', 							'TRANSAKTIONSTYP', 	'0'),
						create_field_info('orders_id', 					'TRANSAKTIONSNUMMER'),
						create_field_info('', 				'VERSION', 	$this->getVARIOCfgKey('VARIO_VERSION_TAG')),
						create_field_info('', 				'VERSION_INFO', VARIO_SHOP_USED)
						),
					)
				)
			);
		}  // end if
		
		$this->assign_field_values($orders_id);
		$this->write_exp();
		_debug('', ' ENDE bek_export');
	}

    function select_orders_data($orders_id) {
    	_debug($orders_id, '--> START select_orders_data');
    		$query = "
select 
	o.orders_id,  
	o.customers_id,  
	o.customers_cid,  
	o.customers_vat_id,  
	o.customers_status,  
	o.customers_status_name,  
	o.customers_status_discount,  
	o.customers_name, o.customers_firstname, o.customers_lastname, o.customers_company,  
	o.customers_street_address, o.customers_city, o.customers_postcode, o.customers_country, co.countries_iso_code_2 as customers_country_iso_code_2, 
	o.customers_telephone, o.customers_email_address,  
	o.customers_address_format_id,  
	o.delivery_name, o.delivery_firstname, o.delivery_lastname, o.delivery_company,  
	o.delivery_street_address, o.delivery_city, o.delivery_postcode, o.delivery_country, o.delivery_country_iso_code_2,  
	o.delivery_address_format_id,  
	o.billing_name, o.billing_firstname, o.billing_lastname, o.billing_company,  
	o.billing_street_address, o.billing_city, o.billing_postcode, o.billing_country, o.billing_country_iso_code_2,  
	o.billing_address_format_id, 
	o.delivery_suburb,	
	o.payment_method,  
	o.cc_type, o.cc_owner, o.cc_number, o.cc_expires, o.cc_start, o.cc_issue, o.cc_cvv,  
	o.comments,  
	o.last_modified, o.date_purchased,
	o.orders_status,  
	o.orders_date_finished,  
	o.currency,  
	o.currency_value,  
	o.account_type,  
	o.payment_class,  
	o.shipping_method, o.shipping_class,  
	o.customers_ip,  
	o.language,  
	o.refferers_id,  
	o.conversion_type,  
	o.orders_ident_key,  
	s.customers_status_id,
	s.customers_status_name,
  	s.customers_status_show_price_tax,
    case
      when os.value > 0 then 1
      else 0
    end as customers_status_show_price_tax,
  	p1.configuration_value as BELEG_KUNDE,
    case
      when not p4.configuration_key is null then SUBSTRING(p4.configuration_key, 10)
	  else 1
    end as PREISGRUPPE
 from orders o
 left join orders_total os on os.orders_id = o.orders_id and os.class = 'ot_tax'
inner join customers_status s on s.customers_status_id = o.customers_status and s.language_id = 2
inner join customers c on c.customers_id = o.customers_id 
inner join address_book a on a.customers_id = o.customers_id and c.customers_default_address_id = a.address_book_id  
 left join countries co on  co.countries_id = a.entry_country_id
 left join configuration p1 on p1.configuration_key    = 'VARIO_BELEG_KUNDE'
 left join configuration p4 on p4.configuration_key LIKE 'VARIO_PG=%' and p4.configuration_value = s.customers_status_name
where o.orders_id = $orders_id";
            $result	= xtc_db_query($query);
            $data 	= xtc_db_fetch_array($result);
    	_debug($data, '--> ENDE select_orders_data');
        return $data;
    }
    
    function select_orders_data_use_customers_data($orders_id) {
    	_debug($orders_id, '--> START select_orders_data_use_customers_data');
    		$query = "
select 
	o.orders_id,  
	o.customers_id,  
	o.customers_cid,  
	o.customers_vat_id,  
	o.customers_status,  
	o.customers_status_name,  
	o.customers_status_discount,

	o.customers_name, 
	a.entry_firstname as customers_firstname, 
	a.entry_lastname as customers_lastname, 
	a.entry_company as customers_company,
	o.customers_street_address, o.customers_city, o.customers_postcode, o.customers_country, co.countries_iso_code_2 as customers_country_iso_code_2, 
	o.customers_telephone, o.customers_email_address,  
	o.customers_address_format_id,

	o.delivery_name, 
	a.entry_firstname as delivery_firstname, 
	a.entry_lastname as delivery_lastname, 
	a.entry_company as delivery_company,
	o.delivery_street_address, o.delivery_city, o.delivery_postcode, o.delivery_country, o.delivery_country_iso_code_2,  
	o.delivery_address_format_id,

	o.billing_name, a.entry_firstname as billing_firstname, a.entry_lastname as billing_lastname, a.entry_company as billing_company,
	o.billing_street_address, o.billing_city, o.billing_postcode, o.billing_country, o.billing_country_iso_code_2,  
	o.billing_address_format_id,  

	o.payment_method,  
	o.cc_type, o.cc_owner, o.cc_number, o.cc_expires, o.cc_start, o.cc_issue, o.cc_cvv,  
	o.comments,  
	o.last_modified, o.date_purchased,
	o.orders_status,  
	o.orders_date_finished,  
	o.currency,  
	o.currency_value,  
	o.account_type,  
	o.payment_class,  
	o.shipping_method, o.shipping_class,  
	o.customers_ip,  
	o.language,  
	o.refferers_id,  
	o.conversion_type,  
	o.orders_ident_key,  
	s.customers_status_id,
	s.customers_status_name,
  	s.customers_status_show_price_tax,
  	s.customers_status_add_tax_ot,
  	p1.configuration_value as BELEG_KUNDE,
    case
      when not p4.configuration_key is null then SUBSTRING(p4.configuration_key, 10)
	  else p5.configuration_value
    end as PREISGRUPPE
 from orders o
inner join customers_status s on s.customers_status_id = o.customers_status and s.language_id = 2
inner join customers c on c.customers_id = o.customers_id 
inner join address_book a on a.customers_id = o.customers_id and c.customers_default_address_id = a.address_book_id  
 left join countries co on  co.countries_id = a.entry_country_id
 left join configuration p1 on p1.configuration_key    = 'VARIO_BELEG_KUNDE'
 left join configuration p4 on p4.configuration_key LIKE 'VARIO_PG=%' and p4.configuration_value = s.customers_status_name
 left join configuration p5 on p5.configuration_key    = 'DEFAULT_CUSTOMERS_STATUS_ID'
where o.orders_id = $orders_id";
            $result	= xtc_db_query($query);
            $data 	= xtc_db_fetch_array($result);
    	_debug($data, '--> ENDE select_orders_data');
        return $data;
    }

    function build_address_lines() {
		$praefix = array(
						  1 => "customers_",
						  2 => "delivery_",
						  3 => "billing_",
					);
        foreach ($praefix as $i=>$pre) {
			$counter = 1;	// die erste Zeile

			// ZEILE1
			if (strlen($this->ds[$pre .'company']) != 0) {
				// Die Anrede ist bei xtc nicht bekannt, hier zumindest für Deutschalnd VARIO-Kompatibilität der Belegadressen
				if ($this->ds['language'] == 'german') { // TODO: noch besser machen 
		    		$this->vario_adress[($i * 10) + $counter] = 'Firma';
				}
			} else {
				// Es handelt sich nicht um eine Firma, sondern eine Privatperson

				$data = xtc_db_fetch_array(xtc_db_query('
					SELECT entry_gender
					  FROM address_book
					 WHERE customers_id = ' . $this->ds['customers_id'] . '
					   AND TRIM(entry_firstname) = TRIM("' . $this->ds[$pre .'firstname'] . '")
					   AND TRIM(entry_lastname)  = TRIM("' . $this->ds[$pre .'lastname']  . '")
					   AND TRIM(entry_postcode)  = TRIM("' . $this->ds[$pre .'postcode']  . '")
				  ORDER BY address_date_added DESC
					 LIMIT 1
				'));
				
				if( $data['entry_gender'] == 'm' )
					$this->vario_adress[($i * 10) + $counter] = 'Herr';
				else
					$this->vario_adress[($i * 10) + $counter] = 'Frau';
			}

			$counter++;	// Falls keine Anrede, dann trotzdem Zeilensprung

			// START NAMEX
			
			if (strlen($this->ds[$pre .'company']) != 0) {
				// FIRMA
		    	// ZEILE2
				$this->vario_adress[($i * 10) + $counter] = trim($this->ds[$pre .'company']);
				$counter++;
				
				if (VARIO_CFL_TO_NAME123 == 1) {
					// company, firstname, lastname an NAME1, NAME2, NAME3 durchreichen
					// ZEILE3
					$company   = trim($this->ds[$pre .'company']);
					$firstname = trim($this->ds[$pre .'firstname']);
					if ($firstname <> $company) {
						$this->vario_adress[($i * 10) + $counter] = trim($firstname);
						$counter++;
					}
					// ZEILE4
					if (strlen($this->ds[$pre .'lastname']) != 0) {
						$this->vario_adress[($i * 10) + $counter] = trim($this->ds[$pre .'lastname']);
						$counter++;
					}
				} else {
					// PERSON bei FIRMA
					if (strlen($this->ds[$pre .'name']) != 0) {
						// Evtl. Doppelfüllung abfangen, alter Kram bei AGI u.a. aus alten Versionen
						$company = trim($this->ds[$pre .'company']);
						$name    = trim($this->ds[$pre .'name']);
						if ($company && $name) {
							$pos     = strpos($name, $company);
							// Evtl. ist company in name enthalten
							if ($pos !== false) {
								$name = substr($name, strlen($name) - pos);					
							}
						}
				    	$this->vario_adress[($i * 10) + $counter] = trim($name);
						$counter++;
					}
				}
			} else {
				// PERSON
				// ZEILE2
				$this->vario_adress[($i * 10) + $counter] = trim($this->ds[$pre .'name']);
				$counter++;
			}
			
			// ENDE NAMEX
			
			// ZEILE5
			if (strlen($this->ds[$pre .'street_address']) != 0) {
		    	$this->vario_adress[($i * 10) + $counter] = trim($this->ds[$pre .'street_address']);
				$counter++;
			}

			
			// Evtl. in Zukunft in ZEILE6 den Adresszusatz ausgeben
			// Wird bei einem Kunden als DHL-Paketnummer genutzt
			/*
			if (strlen($this->ds[$pre .'suburb']) != 0) {
		    	$this->vario_adress[($i * 10) + $counter] = trim($this->ds[$pre .'suburb']);
				$counter++;
			}
			*/

			if ($counter <= 5) $counter++;	// Falls noch Platz, Leerzeile zwischen Strasse und Ort 
			
			if (VARIO_LKZ_VOR_PLZ <> 1) {
				// Land in eigene Zeile
				// ZEILE6
				$this->vario_adress[($i * 10) + $counter] = trim($this->ds[$pre .'postcode'] .' '. $this->ds[$pre .'city']);
				$counter++;

				// ZEILE7
    			$this->vario_adress[($i * 10) + $counter] = trim($this->ds[$pre .'country']);
			} else {
				// LKZ vor Ort
				// ZEILE6
				$this->vario_adress[($i * 10) + $counter] =
					trim($this->ds[$pre .'country_iso_code_2']) .' '.  
					trim($this->ds[$pre .'postcode'] .' '. 
					$this->ds[$pre .'city']);
				$counter++;
			}
		}
	}

	function get_address_line($zeile) {
		return $this->vario_adress[$zeile];
	}

	function get_total_value($class, $convert = true) {
		$aktDS = $this->getTotalRow($class);
		if ($convert) {
			return replace_dot_to_comma($aktDS['value']);
		} else {
		    return $aktDS['value'];
		}
	}

	function getFrachtkosten(){
		// VARIO hat kein Mindestbestellwertzuschlag, deshalb auf Versand addieren
		$ot_shipping 	= $this->ot_data['ot_shipping']['value'];
		_debug($ot_shipping, 'BEK_EXPORT: getFrachtkosten - ot_shipping');

		//HOOK VARIO_HOOK_SP_GET_FRACHTKOSTEN Start
		if( file_exists ( $vario_file = str_replace ( '.php', '_sp.php', __FILE__ ) ) && require_once( $vario_file ) )
		{
			$vario_go = true;
			$vario_result = true;
			$vario_result = VARIO_HOOK_SP_GET_FRACHTKOSTEN($vario_go ,$vario_result, $this->ot_data);
			if (!$vario_go)
			{
				return $vario_result;
			}
		}
		//HOOK VARIO_HOOK_SP_GET_FRACHTKOSTEN ENDE

		$ot_loworderfee = $this->ot_data['ot_loworderfee']['value'];
		_debug($ot_loworderfee, 'BEK_EXPORT: getFrachtkosten - ot_loworderfee');
		$ot_shipping 	= ($ot_loworderfee > 0) ? $ot_shipping + $ot_loworderfee : $ot_shipping;

		_debug($ot_shipping, 'BEK_EXPORT: getFrachtkosten - ot_shipping');
		return replace_dot_to_comma($ot_shipping);
	}

	function getNachnahme(){
		$ot_cod_fee = $this->ot_data['ot_cod_fee']['value'];
		return replace_dot_to_comma($ot_cod_fee);
	}

	function getLieferbedingung($ds){
		$shipping 	= $ds['shipping_class'];
		$payment 	= $ds['payment_class'];
		$lieferbed 	= '';
		// Neues Mapping in VARIO7
		if (($payment == 'cod') && (VARIO_PRODUCT_USED < 'VARIO7')) {
			return $this->getShipping($shipping) . "-$payment";
		} else {
			return $this->getShipping($shipping);
		}
		
	}

	function getShipping( $shipping_class ) {
		$mid = floor(strlen($shipping_class) / 2);
		if ($shipping_class{$mid}=='_') {
			return substr($shipping_class, 0, $mid);
		}
		return $shipping_class;
	}


	function getRabatt1betrag() {
		// ist der Nicht-Prozent-Wert, der Geldbetrag in der Währung, z.B. 5,90 EUR
	    $ot_discount = $this->ot_data['ot_discount']['value'];

		// OT_DISCOUNT schreibt in die orders_total negative Werte, VARIO erwartet positive
		$ot_discount = ($ot_discount < 0)? $ot_discount*-1 : $ot_discount;

	    if ($ot_discount) {
	    	return replace_dot_to_comma($ot_discount);
	    }
	    return null;
	}

	function getRabatt1() {
		// ist der %-Wert
	    $ot_subtotal 	= $this->ot_data['ot_subtotal']['value'];
	    $ot_discount 	= $this->ot_data['ot_discount']['value'];

		// OT_DISCOUNT schreibt in die orders_total negative Werte, VARIO erwartet positive
		$ot_discount = ($ot_discount < 0)? $ot_discount*-1 : $ot_discount;

	    if ($ot_subtotal) { 
		    $rabatt = $ot_discount/$ot_subtotal*100;
		    if ($rabatt) {
	    		return replace_dot_to_comma($rabatt);
	    	}
	    }
	    return null;
	}

    function getRabatt1Text() {
		$html = new Html2Text($this->ot_data['ot_discount']['title'], 1000);
		$text = $html->convert();
		$text = str_replace(array("\r\n"), array(' '), $text);
	    $return['ot_discount']['title'] = trim($text);
		$html = new Html2Text($this->ot_data['ot_discount']['text'], 1000);
		$text = $html->convert();
		$text = str_replace(array("\r\n"), array(' '), $text);
	    $return['ot_discount']['text'] 	= trim($text);
	    $rabatt1text = '';
	    $sep = '';
	    foreach ($return as $value) {
	        if ($value['title']) {
    	    	$rabatt1text .= $sep . $value['title'] . ' ' . $value['text'];
    	    	$sep = '; ';
	        }
	    }
	    return $rabatt1text;
	}

	function getRabatt2betrag() {
		// ist der Nicht-Prozent-Wert, der Geldbetrag in der Währung, z.B. 5,90 EUR
	    $ot_coupon						= $this->ot_data['ot_coupon']['value'];
	    $ot_bonus   					= $this->ot_data['ot_bonus']['value'];
	    $ot_gv 							= $this->ot_data['ot_gv']['value'];
	    $ot_payment 					= $this->ot_data['ot_payment']['value'];

		// OT_Payment schreibt in die orders_total negative Werte, VARIO erwartet positive
		$ot_payment = ($ot_payment < 0)? $ot_payment*-1 : $ot_payment;

		// OT_COUPON schreibt in die orders_total negative Werte, VARIO erwartet positive
		$ot_coupon = ($ot_coupon < 0)? $ot_coupon*-1 : $ot_coupon;

	     
	    $rabatt = $ot_bonus + $ot_gv + $ot_coupon; 

		if( defined('INTERPRET_OT_PAYMENT_AS_DISCOUNT') && INTERPRET_OT_PAYMENT_AS_DISCOUNT == 1 ){

			$rabatt = $rabatt + $ot_payment; 
		}

	    if ($rabatt) {
	    	return replace_dot_to_comma($rabatt);
	    }
	    return null;
	}

	function getRabatt2( $fakturierungsart ) {
		// ist der %-Wert
	    $ot_subtotal 					= $this->ot_data['ot_subtotal']['value'];
	    $ot_discount 					= $this->ot_data['ot_discount']['value'];
	    $ot_coupon 						= $this->ot_data['ot_coupon']['value'];
	    $ot_bonus 						= $this->ot_data['ot_bonus']['value'];
	    $ot_gv 							= $this->ot_data['ot_gv']['value'];
	    $ot_payment 					= $this->ot_data['ot_payment']['value']; 
		
		// OT_Payment schreibt in die orders_total negative Werte, VARIO erwartet positive
		$ot_payment = ($ot_payment < 0)? $ot_payment*-1 : $ot_payment;

		// OT_COUPON schreibt in die orders_total negative Werte, VARIO erwartet positive
		$ot_coupon = ($ot_coupon < 0)? $ot_coupon*-1 : $ot_coupon;
			
		// Punkt - Komma temp. rückgängig machen
		$fk				= str_replace(',', '.', $this->getFrachtkosten());
		$nn				= str_replace(',', '.', $this->getNachnahme());

	    $rabattbetrag 	= $ot_bonus + $ot_gv + $ot_coupon;
		
		if( defined('INTERPRET_OT_PAYMENT_AS_DISCOUNT') && INTERPRET_OT_PAYMENT_AS_DISCOUNT == 1 ){

			$rabattbetrag = $rabattbetrag + $ot_payment; 
		}
		
		// $this->getFakturierungsart() kann hier nicht genutzt werden, daher Übergabeparameter nutzen.
		if($fakturierungsart <> 1) {
			// xtc rechnet anders als VARIO, deshlab nuss hier ein coupon netto berechnet werden
			$rabattbetrag = $this->getNettoPrice($rabattbetrag);
		}
		// vario rabbattiert mit rabatt2 auf summe - rabatt1
		if ($ot_subtotal - $ot_discount <> 0) {
	    	$rabatt = ($rabattbetrag*100)/($ot_subtotal - $ot_discount);
	    	if ($rabatt) {
	    		return replace_dot_to_comma($rabatt);
	    	}
		}
	    return null;
	}

	function getRabatt2Text() {
		$html = new Html2Text($this->ot_data['ot_coupon']['title'], 1000);
		$text = $html->convert();
		$text = str_replace(array("\r\n"), array(' '), $text);
	    $return['ot_coupon']['title'] 	= trim($text);
		$html = new Html2Text($this->ot_data['ot_coupon']['text'], 1000);
		$text = $html->convert();
		$text = str_replace(array("\r\n"), array(' '), $text);
	    $return['ot_coupon']['text'] 	= trim($text);
		$html = new Html2Text($this->ot_data['ot_bonus']['title'], 1000);
		$text = $html->convert();
		$text = str_replace(array("\r\n"), array(' '), $text);
	    $return['ot_bonus']['title'] 	= trim($text);
		$html = new Html2Text($this->ot_data['ot_bonus']['text'], 1000);
		$text = $html->convert();
		$text = str_replace(array("\r\n"), array(' '), $text);
	    $return['ot_bonus']['text'] 	= trim($text);
		$html = new Html2Text($this->ot_data['ot_gv']['title'], 1000);
		$text = $html->convert();
		$text = str_replace(array("\r\n"), array(' '), $text);
	    $return['ot_gv']['title'] 		= trim($text);
		$html = new Html2Text($this->ot_data['ot_gv']['text'], 1000);
		$text = $html->convert();
		$text = str_replace(array("\r\n"), array(' '), $text);
	    $return['ot_gv']['text'] 		= trim($text);

		// Neu: "Vorkasse-Rabatt", in ['text'] steht erneut der Betrag, der hier absichtlich nich angehangen wird
	    $ot_payment 					= $this->ot_data['ot_payment']['value'];
	    if( $ot_payment < 0 ) {		// nru ein negativer Wert ist ein Rabatt
		    $html = new Html2Text($this->ot_data['ot_payment']['title'], 1000);
			$text = $html->convert();
			$text = str_replace(array("\r\n"), array(' '), $text);
		    $return['ot_payment']['title'] = trim($text);
			//$html = new Html2Text($this->ot_data['ot_payment']['text'], 1000);
			//$text = $html->convert();
			//$text = str_replace(array("\r\n"), array(' '), $text);
		    //$return['ot_payment']['text'] 	= trim($text);
	    }  

	    $rabatt2text = '';
	    $sep = '';
	    foreach ($return as $value) {
	        if ($value['title']) {
    	    	$rabatt2text .= $sep . $value['title'] . ' ' . $value['text'];
    	    	$sep = '; ';
	        }
	    }
	    return $rabatt2text;
	}

	function getNettoPrice($bruttoPrice, $tax_class_id = 1){ 
        $xtcPrice = new xtcPrice(DEFAULT_CURRENCY, $this->ds['customers_status']);
        $tax = $xtcPrice->TAX[$tax_class_id];
		// 14.03.2009 AB: round()
        $netto =  round($bruttoPrice / ( ($tax/100) + 1 ), 4); 	// PRICE_PRECISION 4 ist der Standard bei xtc;
        return $netto;
    }

    function getTrustedShopKS(){ 
		// Trusted-Shop-Käuferschutz
	    $ot_ts_schutz = $this->ot_data['ot_ts_schutz']['value'];
	    if ($ot_ts_schutz) { 
			if($this->getFakturierungsart() <> 1) {
				$ot_ts_schutz = $this->getNettoPrice($ot_ts_schutz);
			}
	    } else {
	    	$ot_ts_schutz = 0.00;
	    }
	    
	    // Neu: "Aufschlag"
	    $ot_payment	= $this->ot_data['ot_payment']['value'];
	    if( $ot_payment > 0 ) {
	    	$ot_ts_schutz = $ot_ts_schutz + $ot_payment;
	    }
	     
        return replace_dot_to_comma($ot_ts_schutz);
    }

	function get_total_title($class) {
		$ot_row = $this->getTotalRow($class);
		if ($ot_row) {
			return trim($ot_row['title']);
		}
	}

	function get_total_text($class) {
		$ot_row = $this->getTotalRow($class);
		if ($ot_row) {
            $html = new Html2Text($ot_row['text'], 1000);
            $text = $html->convert();
            $text = str_replace(array("\r\n"), array(' '), $text);
			return trim($text);
		}
	}

	function getTotalRow($class) {
   		return $this->ot_data[$class];
	}

    function getBemerkung($ds) {
		$comments = trim($ds['comments']);
        return $comments;
    }

	function getOrdersStatusHistoryComments($orders_id) {
        $comments = '';
        if ($orders_id) {
            $result = xtc_db_query("SELECT comments, date_added FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = $orders_id");
            $sep = '';
            while ($row= xtc_db_fetch_array($result)) {
                if ($row['comments']) {
            	   $comments .= $sep . $row['date_added'] . ': ' . $row['comments'] . "\n";
            	   $sep = ', ';
                }
            }
        }
        return $comments;
    }

	function getOrdersTotal($orders_id) {
		/*
		 * ot_subtotal
		 * ot_tax
		 * ot_total
		 * ot_discount
		 * ot_shipping
		 * ot_cod_fee
		 * ot_loworderfee
		 * ot_coupon
		 * ot_bonus
		 * ot_gv
		 */
   		$query = xtc_db_query(
					 "SELECT * FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = $orders_id ");
        while ($result = xtc_db_fetch_array($query)) {
        	$ot_data[$result['class']] = $result; 
        }    		
    	return $ot_data;
    }

}
?>
