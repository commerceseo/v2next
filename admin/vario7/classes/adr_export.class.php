<?php
/**
 * @version $Id: adr_export.class.php,v 1.3 2011-07-20 13:37:31 ag Exp $
 * @version $Revision: 1.3 $
 * @copyright Copyright (c) 2005 VARIO Software GmbH
 * 
 * 06.02.2014
 *   Wenn MODULE_PAYMENT_SEPA_STATUS aktiviert ist, dann SEPA-Infos exportieren
 * 
 * 07.11.2011
 *   BELEG_KUNDE wird nicht mehr gefüllt, damit das Adresstemplate greift
 *
 * 15.09.2011
 *   B01 (Newsletter) wird nun mit einem leeren Defaultwert übergeben sodass das Adresstemplate genutzt wird
 *
 * 15.07.2010
 *   WEB_ADRESSNR auch noch gefüllt
 * 
 */

class adr_export extends vario_export {

 	function adr_export($customers_id, $orders_id = null) {
		_debug($customers_id.'-'.$orders_id, 'START adr_export');
		
 		$this->set_class_name('ADR');
		if ($orders_id > 0) {
            $this->set_file_name('ADR_o' . $orders_id);
		} else {
            $this->set_file_name('ADR_c' . $customers_id);
		}
		
		// fetch the data!
		$this->ds = $this->select_customers_data($customers_id, $orders_id);
		$newsletter_feld  = $this->ds['BXX'];	// XX ist richtig! Ist PseudoFeldname bei Export  
		$geburtstags_feld = $this->ds['DXX']; 	// XX ist richtig! Ist PseudoFeldname bei Export
		
		// Map and Convert!
		$this->set_fields(
				array('export_view' =>
					array('fields' =>
						array(	create_field_info('customers_id', 				'EXT_REFERENZ'),
								create_field_info('customers_cid', 				'KUNDENNR',			'',	'get_kundennr($this->ds)'),
								create_field_info('', 							'KUNDEJN', 			(!empty($orders_id))?'J':'N'),
								// create_field_info('customers_id', 				'MATCHCODE', 		'', 'get_matchcode($this->ds)'),
								create_field_info('entry_gender', 				'ANREDE', 			'', 'get_anrede($this->ds)'),
								create_field_info('entry_company',				'NAME1', 			'', 'get_nameX(1, $this->ds)'),
								create_field_info('entry_firstname',			'NAME2', 			'', 'get_nameX(2, $this->ds)'),
								create_field_info('entry_lastname',				'NAME3', 			'', 'get_nameX(3, $this->ds)'),
								create_field_info('entry_street_address', 		'STRASSE'),
								create_field_info('countries_iso_code_2',		'LKZ', 				'', ''), 
								create_field_info('entry_postcode', 			'PLZ'),
								create_field_info('entry_city', 				'ORT'),
								create_field_info('customers_telephone', 		'TELEFON1'),
								create_field_info('customers_email_address', 	'EMAIL'),
								create_field_info('', 							'INTERNETJN', 		'J', 'check_kundenkonto($this->ds)'),
								create_field_info('customers_fax', 				'TELEFAX1'),
								create_field_info('customers_vat_id', 			'USTIDNR'),
								create_field_info('', 							'FAKTURIERUNGSART', '', '$this->getFakturierungsart()'),
								create_field_info('', 							'OHNEMWST', 		'', '$this->getOhneMwst()'),
								create_field_info('customers_vat_id',			'USTIDNR', 			''),
								create_field_info('PREISGRUPPE',				'PREISGRUPPE', 		'1'),
								create_field_info('banktransfer_owner',			'KONTOINHABER'),
								create_field_info('banktransfer_number',		'KONTONR'),
								create_field_info('banktransfer_bankname',		'BANK'),
								create_field_info('banktransfer_blz',			'BLZ'),
								create_field_info('cc_number',					'KK_NR', 			''),
								create_field_info('cc_cvv',						'KK_MOPS', 			''),
								create_field_info('cc_start', 					'KK_GUELTIG_VON', 	''),
								create_field_info('cc_expires', 				'KK_GUELTIG_BIS', 	''),
								create_field_info('cc_owner', 					'KK_INHABER', 		''),
								create_field_info('', 							'KK_INSTITUT', 		''),
								create_field_info('customers_date_added',		'ERSTKONTAKTAM',	'', 'xtc_date_to_vario_date'),
								create_field_info('customers_host', 			'ERSTKONTAKTBEM'),
								create_field_info('customers_newsletter',		$newsletter_feld,	'', 'xtc_bool_to_vario_bool'),
								create_field_info('customers_dob',				$geburtstags_feld,	'',	'xtc_date_to_vario_date'),
								create_field_info('customers_password', 		'WEB_KENNWORT_MD5'),
								create_field_info('customers_date_added', 		'ANG_AM', 			'', 'xtc_date_to_vario_date'),
								create_field_info('', 							'ANG_VON', 			'VAE'),
								create_field_info('address_last_modified', 		'GEA_AM', 			'', 'xtc_date_to_vario_date'),
								create_field_info('', 							'GEA_VON', 			'VAE'),
								create_field_info('vertreter1', 				'VERTRETER1', 	''),
								create_field_info('', 				'VERSION', 	$this->getVARIOCfgKey('VARIO_VERSION_TAG')),
								create_field_info('', 				'VERSION_INFO', VARIO_SHOP_USED),
					),
				)
			)
		);

		if( $this->getVARIOCfgKey('MODULE_PAYMENT_SEPA_STATUS') == 'True' ){

			$this->set_field('export_view', create_field_info('sepa_owner',			'KONTOINHABER'));
			$this->set_field('export_view', create_field_info('sepa_iban',			'IBAN'));
			$this->set_field('export_view', create_field_info('sepa_bankname',		'BANK'));
			$this->set_field('export_view', create_field_info('sepa_bic',			'SWIFT'));
		}

		$this->assign_field_values($customers_id);
		$this->write_exp();
		_debug($this->fields, ' ENDE adr_export');
 	}

}
?>
