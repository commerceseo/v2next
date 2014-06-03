<?php
/* -----------------------------------------------------------------
 * 	$Id: class.sepa_account_check.php 846 2014-02-08 19:46:51Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	xt:Commerce v 3.x PostFinance  Zahlungs-Modul by customweb GmbH
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License

 based on:
  sepa_account_check.php 2014-01-22 gambio
  Gambio GmbH
  http://www.gambio.de

  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(sepa_validation.php,v 1.17 2003/02/18 18:33:15); www.oscommerce.com
  (c) 2003	 nextcommerce (sepa_validation.php,v 1.4 2003/08/1); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: class.sepa_account_check.php 846 2014-02-08 19:46:51Z akausch $)
  (c) 2004 - 2006 fmce.de
  (c) 2004 - 2006 discus24.de
  (c) 2004 - 2006 Frank Maroke

  Released under the GNU General Public License

  -----------------------------------------------------------------------------------------

  Third Party contributions:

  OSC German Sepa v0.85a       	Autor:	Dominik Guder <osc@guder.org>
  Extensioncode: 							Marcel Bossert-Schwab <info@opensourcecommerce.de> (mbs)
  New methods 2005 - 2006: 				Frank Maroke (FrankM) <info@fmce.de>

  Released under the GNU General Public License

  --------------------------------------------------------------------------------------- */

require_once('class.sepa_blz_validation.php');

class SepaAccountCheck
{
	/* Folgende Returncodes werden übergeben                                      */
	/*                                                                            */
	/* 0 -> Kontonummer & BLZ OK                                                  */
	/* 1 -> Kontonummer & BLZ passen nicht                                        */
	/* 2 -> Für diese Kontonummer ist kein Prüfziffernverfahren definiert         */
	/* 3 -> Dieses Prüfziffernverfahren ist noch nicht implementiert              */
	/* 4 -> Diese Kontonummer ist technisch nicht prüfbar                         */
	/* 5 -> BLZ nicht gefunden                                                    */
	/* 10 -> no account holder                                                    */
	/* 11 -> no iban															  */
	/* 12 -> no iban check digits											      */
	/* 13 -> incorrect iban												          */
	/* 14 -> no bic										                          */
	/* 15 -> incorrect bic							                              */
	/* 16 -> no bankname				                                          */
	/* 128 -> interner Fehler,der zeigt, das eine Methode nicht implementiert ist */
	/*                                                                           */
	var $owner;
	var $iban;
	var $bic;
	var $bankname;
	var $prz;


	// Diese function gibt die Bankinformationen aus der csv-Datei zurück*/
	protected function csv_query($p_blz)
	{
		$c_data = -1;
		$c_handle = fopen(DIR_WS_INCLUDES . 'data/blz.csv', 'r');
		while($t_data = fgetcsv($c_handle, 1024, ";"))
		{
			if($t_data[0] == $p_blz)
			{
				$c_data = array('blz' => $t_data[0],
								'bankname' => $t_data[1],
								'prz' => $t_data[2]);
			}
		}
		return $c_data;
	}

	// Diese function gibt die Bankinformationen aus der Datenbank zurück*/
	protected function db_query($blz)
	{
		$data = -1;
		$t_result = xtc_db_query("SELECT 
									* 
								FROM 
									banktransfer_blz 
								WHERE 
									blz = '" . xtc_db_input($blz) . "'");
		if(xtc_db_num_rows($t_result) == 1)
		{
			$data = xtc_db_fetch_array($t_result);
		}
		return $data;
	}

	// Diese function gibt die Bankinformationen aus der Datenbank zurück*/
	protected function query($p_blz)
	{
		if(defined(MODULE_PAYMENT_SEPA_DATABASE_BLZ) && MODULE_PAYMENT_SEPA_DATABASE_BLZ == 'true')
		{
			$t_data = $this->db_query($p_blz);
		}
		else
		{
			$t_data = $this->csv_query($p_blz);
		}
		return $t_data;
	}

	// IBAN check
	function check_iban($p_iban)
	{
		$t_iban = str_replace(' ', '', $p_iban);
		// RETURN if no data check
		if(MODULE_PAYMENT_SEPA_DATACHECK == 'false')
		{
			return $t_iban;
		}
		if(substr($t_iban, 2, 2) == '00')
		{
			// no check digit
			return '1';
		}
		
		$t_iban_1 = substr($t_iban, 4)
				. strval(ord($t_iban{0}) - 55)
				. strval(ord($t_iban{1}) - 55)
				. substr($t_iban, 2, 2);

		for($i = 0; $i < strlen($t_iban_1); $i++)
		{
			if(ord($t_iban_1{$i}) > 64 && ord($t_iban_1{$i}) < 91)
			{
				$t_iban_1 = substr($t_iban_1, 0, $i) . strval(ord($t_iban_1{$i}) - 55) . substr($t_iban_1, $i + 1);
			}
		}
		$t_iban_rest = 0;
		for($pos = 0; $pos < strlen($t_iban_1); $pos+=7)
		{
			$part = strval($t_iban_rest) . substr($t_iban_1, $pos, 7);
			$t_iban_rest = intval($part) % 97;
		}

		if($t_iban_rest == 1)
		{
			// return valid iban
			return $t_iban;
		}
		else
		{
			// invalid iban
			return '2';
		}
	}

	function CheckAccount($p_owner, $p_iban, $p_bic, $p_bankname)
	{
		// check owner
		if(trim($p_owner) == '')
		{
			// no owner
			return 10;
		}
		
		// check iban
		if(trim($p_iban) == '')
		{
			// no iban
			return 11;
		}
		$p_iban = str_replace(' ', '', $p_iban);
		$c_iban = $this->check_iban($p_iban);
		if($c_iban == '1')
		{
			// no iban check digits
			return 12;
		}
		if($c_iban == '2')
		{
			// incorrect iban
			return 13;
		}
		
		// check bic
		if(trim($p_bic) == '')
		{
			// no bic
			return 14;
		}
		$p_bic = str_replace(' ', '', $p_bic);
		if(strlen($p_bic) != 8 && strlen($p_bic) != 11 && MODULE_PAYMENT_SEPA_DATACHECK == 'true')
		{
			// incorrect bic
			return 15;
		}
		$this->owner = trim($p_owner);
		$this->iban = $c_iban;
		$this->bic = $p_bic;
		$this->bankname = trim($p_bankname);
		
		$t_language_code = substr($c_iban, 0, 2);
		$t_result = 0;
		if($t_language_code == 'DE' && MODULE_PAYMENT_SEPA_DATACHECK == 'true')
		{
			// get konto nr from iban
			$p_kto_nr = substr($c_iban, 12, 10);
			// get blz from iban
			$t_blz = substr($c_iban, 4, 8); //$c_iban DE12 12345678

			/*     Beginn Implementierung */
			$adata = $this->query($t_blz);
			if($adata == -1)
			{
				$t_result = 5; // BLZ nicht gefunden;
				$this->prz = -1;
			}
			else
			{
				$this->bankname = $adata['bankname'];
				$this->prz = str_pad($adata['prz'], 2, "0", STR_PAD_LEFT);

				$t_prz = $adata['prz'];

				$coo_blz_validation = new BLZValidation();

				switch($t_prz)
				{
					case "52" : $t_result = $coo_blz_validation->Mark52($p_kto_nr, $t_blz);
						break;
					case "53" : $t_result = $coo_blz_validation->Mark53($p_kto_nr, $t_blz);
						break;
					/* --- Added FrankM 20060112 --- */
					case "B6" : $t_result = $coo_blz_validation->MarkB6($p_kto_nr, $t_blz);
						break;
					case "C0" : $t_result = $coo_blz_validation->MarkC0($p_kto_nr, $t_blz);
						break;
					default:
						$t_result = $coo_blz_validation->call_method("Mark$t_prz", $p_kto_nr);
				}
			}
		}
		if(trim($this->bankname) == '')
		{
			return 16;  /* Keinen Banknamen angegeben */
		}
		return $t_result;
	}
}