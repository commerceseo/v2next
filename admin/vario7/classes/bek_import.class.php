<?php
/**
 * @version $Id: bek_import.class.php,v 1.1 2011-07-15 12:33:33 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2005 VARIO Software GmbH
 *
 * 13.12.2010 AB
 * - neue Konstante/Define VARIO_XTC_ORDER_FINISHED, genutzt in bek_import
 * 13.12.1010 (AB hat SV nachgetragen)
 * - Neu bei VARIO 7.1: VARIO 7.1 hat jetzt den Automat, da pro Shop evtl. unterschiedlich
 * - AUTOMAT in VARIO 7.1 verschoben, deshalb hier keine berechnung, kommentiert
 * - AB: Nutzung von VARIO_PRODUCT_USED
 * - create_history_message holt jetzt den Text aus der orders_status
 * 12.12.2010 AB
 * - Automat bei VARIO7.1 jetzt in VARIO 7.1, deshlabt hier nur noch durchreichen
 * 20.11.2010 AB
 * - neue Version VARIO7.1
 * 16.07.2010
 *   BELEGART / BELEGTYP für Automat je nach VARIO-VERSION
 * 11.06.2010 AB
 *   INSERT zugelassen, Kleinigkeiten
 * 09.05.2010 AB
 *   datepurchased wurde überschrieben / falsch interpretiert, entfernt
 */

 class bek_import extends vario_import {
 	
    /**
     * 	orders_status (Standard) 1=Offen, 2=In Arbeit, 3=Versendet
     * 
     * 	 Belegarten
     * 	 02:Auftrag 				
     * 	 03:Lieferschein 			
     * 	 04:Rechnung 				
     * 	 05:Lieferschein / Rechnung 
     * 	 11:Gutschrift m. WR		
     * 	 12:Gutschrift o. WR 		
     * 
     * z.Zt. verarbeitete Felder:
     * 
     * WEB_ID, BELEGTYP, BELEGNR, BELEGSTATUS, KUNDENNR, VORKASSE, FREIGABE, LIEFERTERMIN, LIEFERTERMIN_BEMERKUNG, WEB_EMAIL 
     */
	function bek_import($exp_input) {
		_debug($exp_input, 'START bek_import -- Übergeben wurde der Datensatz $exp_input');
		$this->set_exp_source($exp_input);
		
		$aktion 			= $this->get_exp_value('AKTION');
		$orders_id 			= $this->get_exp_value('WEB_ID');
		
		// Pürfen, ob es die Bestellung überhaupt gibt.
		$sql = "select orders_id from ".TABLE_ORDERS." where orders_id = $orders_id";
		$orders_temp_id = vDB::fetchone($sql);
		
		if (!$orders_temp_id) {
			_debug($orders_id, " EXIT NO orders_id bek_import -- keine Bestellnummer gefunden!");
			return;											// Dies ist eine Belegkopf, dessen Ursprung nicht in einer Shopbestellung liegt
		}
		
		if (VARIO_PRODUCT_USED >= 'VARIO7') {
			$belegtyp 			= $this->get_exp_value('BELEGTYP'); 
		} else {
			$belegtyp 			= $this->get_exp_value('BELEGART'); 
		}
		$belegnr 			= $this->get_exp_value('BELEGNR');
		$belegstatus 		= $this->get_exp_value('BELEGSTATUS');
		$vorkasse 			= $this->get_exp_value('VORKASSE');
		$freigabe 			= $this->get_exp_value('FREIGABE');
		$liefertermin 		= $this->get_exp_value('LIEFERTERMIN');

		if ($aktion == 'D') {
			_debug($orders_id, " EXIT DELETE bek_import -- DELETE-Satz weggeschmissen!");
			return;											// ein DELETE-Satz wird z.Zt. nicht verarbeitet!
		}
		if ($belegnr == '') { 
			_debug($orders_id, " EXIT NO BELEGNR bek_import -- keine VARIO-Belegnummer gefunden!");
			return;											// wo kommt nur eine leerer Satz her?  
		}
		
		// Ein STATUS-AUTOMAT
		$orders_status = -1;
		$sql = "select orders_status from ".TABLE_ORDERS." where orders_id = $orders_id";
		$old_orders_status = vDB::fetchone($sql);
		
			$orders_status = $this->get_exp_value('ORDERSTATUS');  // Neu bei VARIO 7.1: VARIO 7.1 hat jetzt den Automat, da pro Shop evtl. unterschiedlich
			_debug($orders_status, "Orders ID 7.1: " . $orders_id . " Alter Status: " . $old_orders_status . " Neuer Status: " . $orders_status, " EXIT - keine Statusänderung!");

		if ( (int)$old_orders_status == (int)$orders_status ) {
			// Ist der Status gleich geblieben, dann nix mehr tun
			return;											
		}
		
		$message = " AUTO bek_import -- orders_status ermittelt anhand Bestellnr.: $orders_id, Belegnr.: $belegnr, Belegtyp: $belegtyp, Belegstatus: $belegstatus, Freigabe: $freigabe, Vorkasse: $vorkasse. ($orders_status) ==>> "; 
		_debug($orders_status, $message, 7);
		
		$this->set_fields( array(
			'WEB_ID'		=> array(create_field_info(TABLE_ORDERS, 'orders_id')),
			'KUNDENNR'		=> array(create_field_info(TABLE_ORDERS, 'customers_cid')),
			'BELEGNR'		=> array(create_field_info(TABLE_ORDERS, 'orders_ident_key')),
			'WEB_EMAIL'		=> array(create_field_info(TABLE_ORDERS, 'customers_email_address')),
		));

		if ($orders_status > 0)  $this->setField(TABLE_ORDERS, 'orders_status', $orders_status);
		if ($orders_status == VARIO_XTC_ORDER_FINISHED) { 
			$this->setField(TABLE_ORDERS, 'orders_date_finished', date('Y-m-d H:i:s'));
		}
		$this->setField(TABLE_ORDERS, 'last_modified', date('Y-m-d H:i:s'));
		
		$this->assign_field_values();
		$this->do_SQL(array(TABLE_ORDERS=>$this->import[TABLE_ORDERS]));
		
		$this->setAction('I');	// History ist immer ein Einfügen
		$this->setField(TABLE_ORDERS_STATUS_HISTORY, 'orders_id', 			$orders_id);
		$this->setField(TABLE_ORDERS_STATUS_HISTORY, 'date_added', 			date('Y-m-d H:i:s'));
		if ($orders_id <> -1) { 
			$this->setField(TABLE_ORDERS_STATUS_HISTORY, 'orders_status_id', 	$orders_status);
			$message = $this->create_history_message($orders_status, $message);
			$this->setField(TABLE_ORDERS_STATUS_HISTORY, 'customer_notified',	1);	// benachrichtig 
		} else {
			$this->setField(TABLE_ORDERS_STATUS_HISTORY, 'orders_status_id', 	$orders_status);
			$this->setField(TABLE_ORDERS_STATUS_HISTORY, 'customer_notified',	0);	// nicht benachrichtig 
		}
		$this->setField(TABLE_ORDERS_STATUS_HISTORY, 'comments', 			$message);
		
		// _debug($this->constant_fields, 'TABLE_ORDERS_STATUS_HISTORY-Fields');
		$this->do_SQL(array(TABLE_ORDERS_STATUS_HISTORY=>$this->import[TABLE_ORDERS_STATUS_HISTORY]));
		
		_debug('', ' ENDE bek_import --');
	}
		
	function create_history_message($orders_status, $text) {
		
		// TODO: Sprache ? und Messages irgendwo speichern
		// 13.12.2010 AB
		
		$sql = "select orders_status_name from ".TABLE_ORDERS_STATUS." where orders_status_id = $orders_status and language_id = ".VARIO_XTC_DE_LANGUAGE_ID;
		$message = vDB::fetchone($sql);

		switch ($orders_status) {
			/*
    	    case 1: 
    	    	$message = 	'Bestellung noch nicht importiert.';
        	    break;
    	    case 2: 
				$message = 	'Bestellung importiert, Auftrag angelegt.';
        	    break;
	        case 3:
	        	$datum = date('Y-m-d');
    	        $message = 	"Ware am $datum versendet.";
        	    break;
	        case 4:
    	        $message = 	'Bestellung als Auftrag gespeichert, warte auf Zahlungseingang, da Vorkasseauftrag.';
        	    break;
	        case 5:
    	        $message = 	'Zahlungseingang verzeichnet,';
        	    break;
        	case 6:
    	        $message = 	'Bestellung/Auftrag freigegeben';
        	    break;
	        case 7:
    	        $message = 	'Warenrücksendung erhaltung, Gutschrift ausgestellt.';
        	    break;
	        case 8:
    	        $message = 	'Der Gutschriftbetrag wurde auf Ihr Konto überweisen.';
        	    break;
        	case 9:
    	        $message = 	'Ihre Bestellung wurde storniert.';
        	    break;
        	default:
    	        $message = 	"Unbekannte Statusänderung $orders_status erhalten.";
        	    break;
			*/
	        
    	}
    	
    	return $message;
    }
		
 		
 }
?>
