<?php

/**
 * @version $Id: vario_export7.php,v 1.2 2011-07-18 14:27:48 ag Exp $
 * @version $Revision: 1.2 $
 * @copyright Copyright (c) 2005 VARIO Software GmbH

 * 06.02.2014 AB
 * - ORDERSTOTAL_KEY_TO_BEP_ARTIKELNR nutzen
 * 30.01.2012 AB
 * - VARIO_ORDES_EXPORT_STATUS --> VARIO_ORDERS_EXPORT_STATUS

 */

  include_once('vario7/configure.inc.php');								// 
  include_once('vario7/configure_export.inc.php');						// 
  include_once('vario7/functions.inc.php');								
  include_once('includes/application_top.php');	

  include_once('includes/configure.php');
  include_once('vario7/functions_export.inc.php');
  include_once('../includes/classes/xtcPrice.php');
  include_once('../inc/xtc_get_tax_rate.inc.php');
  include_once('vario7/classes/vario_export.class.php');
  include_once('vario7/classes/adr_export.class.php');
  include_once('vario7/classes/bek_export.class.php');
  include_once('vario7/classes/bep_export.class.php');
  
  if (VARIO_PRODUCT_USED == 'TEXTIL') {
  	include_once('vario6/classes/beg_export.class.php');
  }
  

  function export_customer($customer_id, $orders_id = -1) {
  	// Exportiert einen Kunden, bei Angabe einer Bestellnummer mit Bankdaten 
  	$obj = new adr_export($customer_id, $orders_id);
  }


  function export_order($orders_id, $customers_id, $new_orders_status = 2) {
  	// Exportiert einen Besetlllung eines Kunden 
  	_debug($orders_id, 'START export_order');

	// Kopf
  	$obj = new bek_export($orders_id, $customers_id);	

  	// Positionen
	$query = "select orders_products_id, products_id "
			."  from ".TABLE_ORDERS_PRODUCTS." "
			." where orders_id = $orders_id "
			." order by orders_products_id";							// Damit die Positionen in der richtigen Reihenfolge kommen 
	$result = xtc_db_query($query);
	$firstpos = true;
	while($data = xtc_db_fetch_array($result)){
		if ($firstpos == true) {										// Erste Besetllposition auf 1 normieren 
			$offset   = $data['orders_products_id'] - 1;				// wegen Position BEP.PO
			$firstpos = false;
		}
		$orders_products_id	= $data['orders_products_id'];
		$products_id		= $data['products_id'];
		$pos 				= $data['orders_products_id'] - $offset;	// das ist dann die BEP.PO in VARIO
		// Eine Bestellposition
		export_order_products($orders_products_id, $pos, $orders_id); 
	}

	// ORDERS_TOTAL TO BEP
	if( ORDERSTOTAL_KEY_TO_BEP_ARTIKELNR ){

		$i = 1;

		foreach( unserialize( ORDERSTOTAL_KEY_TO_BEP_ARTIKELNR ) as $OrdersTotalKey => $Values ){

			$query = xtc_db_fetch_array( xtc_db_query(
					"SELECT value as PREIS
					   FROM " . TABLE_ORDERS_TOTAL . " 
					  WHERE orders_id = " . $orders_id . "
						AND class = '" . $OrdersTotalKey . "'"
			));

			if( !empty( $query ) ){

				$VExport = new vario_export;

				$VExport->file_name = 'BEP_o' . $orders_id;
				$VExport->class_name = 'BEP';

				$VExport->export = array(
						1 => array(
									'Tab' 				=> 'BEP'
								  , 'BELEGART' 			=> '00'
								  , 'POART' 			=> 'A'
								  , 'WEB_BEK_ID' 		=> $orders_id
								  , 'WEB_ID' 			=> ( PHP_INT_MAX - ($i * $orders_id) )
								  , 'BELEGNR'			=> get_belegnr( $orders_id )
								  , 'BELEGSCHLUESSEL'	=> get_belegschluessel( $orders_id )
								  , 'ARTIKELNR' 		=> $Values['ARTIKELNR']
								  , 'BEZEICHNUNG' 		=> $Values['BEZEICHNUNG']
								  , 'MENGE' 			=> 1
								  , 'EK' 				=> str_replace( '.', ',', $query['PREIS'] )
								  , 'EINZELPREIS' 		=> str_replace( '.', ',', $query['PREIS'] )
								  , 'GESAMTPREIS' 		=> str_replace( '.', ',', $query['PREIS'] )
								  , 'EINZELPREISBRUTTO' => str_replace( '.', ',', $query['PREIS'] )
								  , 'GESAMTPREISBRUTTO' => str_replace( '.', ',', $query['PREIS'] )
								  , 'RABATT' 			=> 0
								  , 'VERSION' 			=> $VExport->getVARIOCfgKey('VARIO_VERSION_TAG')
								  , 'VERSION_INFO' 		=> VARIO_SHOP_USED
							)
						);

				$VExport->write_exp();

				unset( $VExport );
			}
			
			$i = $i +100;
		}
	}

	// Status setzen, damit diese Bestellung nicht mehr abgeholt wird
	$query = "update ".TABLE_ORDERS." set "
			."   orders_status = $new_orders_status "
			." where orders_id = $orders_id";
	xtc_db_query($query);
	// Statusänderung vermerken
	$query = "insert into ".TABLE_ORDERS_STATUS_HISTORY." set "
			."  orders_id = $orders_id, "
			."	orders_status_id = $new_orders_status, "
			."  date_added = now(), "
			."  customer_notified = 0, "
			."  comments = 'Bestellung $orders_id für die Abholung durch VARIO exportiert!'";
	xtc_db_query($query);
	_debug($orders_id, ' ENDE export_order');
  }

  
  function export_order_products($orders_products_id, $pos, $orders_id) {
	//_debug($orders_id.'('.$orders_products_id.')', 'START export_order_products');
  	$obj = new bep_export($orders_products_id, $pos, $orders_id);
  	// Spezial: Farben, Längen, Größen in orders_attributes
  	if (VARIO_PRODUCT_USED == 'TEXTIL') {
  		$objg = new beg_export($orders_products_id, $pos, $orders_id);
  	}
  	//_debug($orders_id.'('.$orders_products_id.')', ' ENDE export_order_products');
  }
  

/*
 * Noch nicht abgeholte Besetllungen exportieren
 */

	$query = "select customers_id, orders_id "
			."  from ".TABLE_ORDERS." "
			." where orders_status in (".VARIO_ORDERS_EXPORT_STATUS.") "
			. ( ( EXPORT_ORDERS_DELAY > 0 )
				? " and date_purchased <= DATE_SUB(NOW(), INTERVAL " . EXPORT_ORDERS_DELAY . " SECOND) " .
				: "" )
			." limit 0, ".VARIO_NO_OF_ORDERS_PER_CYCLE; 
	$result = xtc_db_query($query);
	while($data = xtc_db_fetch_array($result)){
		$customers_id 	= $data['customers_id'];
		$orders_id 		= $data['orders_id'];
		export_customer($customers_id, $orders_id);		// Kundendaten holen 
		export_order($orders_id, $customers_id); 		// Besetllungen holen
	}
	
	
?>
