<?php

/**
 * @version $Id: configure.inc_neu.php,v 1.3 2011-07-18 10:04:39 ag Exp $
 * @version $Revision: 1.3 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 */


	define('VARIO_PRODUCT_USED', 	'VARIO7.1');	// Mögliche Werte: VARIO7.1, VARIO7	
	define('VARIO_SHOP_USED', 		'CSEO');

    define('SUPPRESS_REDIRECT', 1);       		    // for application_top.php

  	define('VARIO_WRITE_LOG', 		0);				// Mögliche Werte: 0 = kein Logging, 1 = Logging in admin/vario7/logs
	
  	define('VARIO_XTC_DE_LANGUAGE_ID', 1);			// Die Standard-ID aus der Tabelle languages, die der Sprache Deutsch (de) zugeordnet ist
  	define('VARIO_CONVERT_TO_UTF8', 1);				// VARIO7-Zeichensatz WIN1252 wird 0: nicht und 1: nach UTF8 bei Export und von UTF8 bei Import konvertiert
	define('VARIO_ATTR_OFFSET', 	100);			// products_id > attributes_id - Faktor
	
?>
