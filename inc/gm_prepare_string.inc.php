<?php
/* --------------------------------------------------------------
   gm_prepare_string.inc.php 2010-12-23 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function gm_magic_check($string){
	if(preg_match('/(^"|[^\\\]{1}")/', $string) == 1) return false;
	if(preg_match('/(^\'|[^\\\]{1}\')/', $string) == 1) return false;
	else return true;	
}


function gm_prepare_string($string, $strip = false){
	if(!$strip){
		if(ini_get('magic_quotes_gpc') == 0 || ini_get('magic_quotes_gpc') == 'Off' || ini_get('magic_quotes_gpc') == 'off'){
			if(!gm_magic_check($string)) $string = mysql_real_escape_string($string);
		}
	}
	else{
		if(ini_get('magic_quotes_gpc') == 1 || ini_get('magic_quotes_gpc') == 'On' || ini_get('magic_quotes_gpc') == 'on') $string = stripslashes($string);
		else{
			if(gm_magic_check($string)) $string = stripslashes($string);
		}
	}
	return $string;
}
?>