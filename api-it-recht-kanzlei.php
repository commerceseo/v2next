<?php
/* --------------------------------------------------------------
   api-it-recht-kanzlei.php 2014-06-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

    based on:
            Example-Interface-Software for the transmission of legal texts between IT-Recht Kanzlei München and your system
            Script version: Draft V0.2 sw2 - 26. April 2012
            Author: Jaromedia, IT-Recht Kanzlei
            Contact: Max-Lion Keller LL.M., IT-Recht Kanzlei, Alter Messeplatz 2, 80339 Munich, Germany - Phone: +49(0)89/ 130 1433-0, Email: m.keller@it-recht-kanzlei.de

    Released under the GNU General Public License
---------------------------------------------------------------------------------------*/

$_POST_unfiltered = $_POST;
require_once 'includes/application_top.php';
$_POST = $_POST_unfiltered;

function itrecht_log($message) {
	$logger = new FileLog('itrecht', true);
	$date = date('c');
	$logger->write($date.' | '.$message."\n");
}

$languages_result = xtc_db_query("SELECT languages_id, code FROM languages;");
$languages = array();
while($lang_row = xtc_db_fetch_array($languages_result)) {
	$languages[$lang_row['code']] = $lang_row['languages_id'];
}

//	----- Settings -----
$local_api_version = '1.0';
$local_api_username = cseo_get_conf('ITRECHT_API_USER');
$local_api_password = cseo_get_conf('ITRECHT_API_PASSWORD');
$local_supported_rechtstext_types = array('agb', 'impressum', 'datenschutz', 'widerruf');
$local_supported_rechtstext_languages = array('de');
//$local_supported_rechtstext_languages = array_keys($languages); // for future extension
$local_supported_actions = array('push');

$local_rechtstext_pdf_required = array(			// true or false (set to true for each rechtstext type where you require a pdf-file)
	'agb' => true,
	'impressum' => false,
	'datenschutz' => true,
	'widerruf' => true,
);
$local_dir_for_pdf_storage = DIR_FS_CATALOG.'/media/content/';
//$local_limit_download_from_host = 'www.it-recht-kanzlei.de';	//  only change when told to do so, this will limit pdf downloads to a specific source host
$local_limit_download_from_host = '';
$local_flag_multishop_system = false; 							// true or false (only set to true if your system is a multishop-system, this means that under one user/password login a user manages more than one shop)
$test_with_local_xml_file = false; 								// true or false (only set to true for testing, requires 'beispiel.xml' in DIR_FS_CATALOG.'cache')


// ----- begin automatic dependant settings (do not change) -----

// if your system is a multishop system, action 'getaccountlist' should be supported
if($local_flag_multishop_system === true){ array_push($local_supported_actions, 'getaccountlist'); }
// no host limit for downloading pdf when testing
if($test_with_local_xml_file === true){ $local_limit_download_from_host = ''; }

// ----- end automatic dependant settings (do not change) -----

// ---------- begin functions ----------
// validate URL
function url_valid($url, $limit_to_host = '') {
	$array_url = @parse_url($url);
	// check host limit
	if($limit_to_host != '') {
		if(strtolower($array_url['host']) != strtolower($limit_to_host)) {
			return false;
		}
	}
	// check HTTP protocol
	if(in_array(strtolower($array_url['scheme']), array('http','https')) !== true) {
		return false;
	}

	// idn (funktion ggf. nicht gegeben)
	// http://de2.php.net/manual/en/function.filter-var.php#104160
	// ODER idn_to_ascii (PHP 5 >= 5.3.0, PECL intl >= 1.0.2, PECL idn >= 0.1)
	$res = filter_var($url, FILTER_VALIDATE_URL);
	if($res !== false ) {
		return true;
	}
	// Check if it has unicode chars.
	$l = mb_strlen($url);
	if($l !== strlen ($url)) {
		// Replace wide chars by “X”.
		$s = str_repeat (' ', $l);
		for ($i = 0; $i < $l; ++$i) {
			$ch = mb_substr($url, $i, 1);
			$s [$i] = strlen($ch) > 1 ? 'X' : $ch;
		}
		// Re-check now
		$res = filter_var($s, FILTER_VALIDATE_URL);
		//if ($res) {    $url = $res; return 1;    }
		if($res !== false ) {
			return true;
		}
	}
	else {
		return true;
	}
	return false;
}

// check if a file is a pdf
function check_if_pdf_file($filename){
	$handle = @fopen($filename, "r");
	$contents = @fread($handle, 4);
	@fclose($handle);
	if($contents == '%PDF') {
		return true;
	}
	else {
		return false;
	}
}

// return error and end script
function return_error($errorcode){
	// output error
	header('Content-type: application/xml; charset=utf-8');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	echo "<response>\n";
	echo "	<status>error</status>\n";
	echo "	<error>".$errorcode."</error>\n";
	echo "</response>";
	xtc_db_close();
	exit();
}

// return success and end script
function return_success(){
	// output success
	header('Content-type: application/xml; charset=utf-8');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	echo "<response>\n";
	echo "	<status>success</status>\n";
	echo "</response>";
	xtc_db_close();
	exit();
}

function itrk_retrieve_from_url($p_url)
{
	$t_allow_url_fopen = ini_get('allow_url_fopen') == true;
	$t_data = false;
	if($t_allow_url_fopen == true)
	{
		$t_data = @file_get_contents($p_url);
	}
	else
	{
		$t_curl_opts = array(
			CURLOPT_URL => $p_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 5,
		);
		$t_ch = curl_init();
		curl_setopt_array($t_ch, $t_curl_opts);
		$t_data = curl_exec($t_ch);
		$t_curl_errno = curl_errno($t_ch);
		curl_close($t_ch);
		if($t_curl_errno > 0)
		{
			$t_data = false;
		}
	}
	return $t_data;
}

function itrk_convert_html($p_raw_html)
{
	if(function_exists('iconv'))
	{
		$t_processed_html = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $p_raw_html);
		if($t_processed_html === false)
		{
			$t_processed_html = utf8_decode($p_raw_html);
		}
	}
	else
	{
		$t_processed_html = utf8_decode($p_raw_html);
	}

	return $t_processed_html;
}

// ---------- end functions ----------

// additional checks and logging
itrecht_log('API contacted by '.$_SERVER['REMOTE_ADDR']);
if(!$test_with_local_xml_file) {
	if(!$_SERVER['REQUEST_METHOD'] == 'POST') {
		itrecht_log('ERROR: request type is not POST, aborting');
		xtc_db_close();
		die('invalid access');
	}
	if(empty($_POST['xml'])) {
		itrecht_log('ERROR: no XML content');
		return_error('12');
	}
}

// read POST-XML and remove form slashes
$post_xml = $_POST['xml'];
if(get_magic_quotes_gpc()) {
	$post_xml = stripslashes($post_xml);
}

// Catch errors - no data sent
if($test_with_local_xml_file !== true) {
	if(trim($post_xml) == '') {
		itrecht_log('ERROR: XML parameter is empty');
		return_error('12');
	}
}

// create xml object
if($test_with_local_xml_file !== true) {
	if(file_exists(DIR_FS_CATALOG.'/cache/itrecht-tmp.xml'))
	{
		unlink(DIR_FS_CATALOG.'/cache/itrecht-tmp.xml');
	}
	// file_put_contents(DIR_FS_CATALOG.'/cache/itrecht-tmp.xml', $post_xml);
	$xml = @simplexml_load_string($post_xml);
}
else {
	itrecht_log('INFO: test mode, using XML from local file');
	$xml = @simplexml_load_file(DIR_FS_CATALOG.'cache/beispiel.xml');  // used for testing with local xml-file
}
// Catch errors - error creating xml object
if($xml === false) {
	itrecht_log('ERROR: XML invalid');
	return_error('12');
}

// Catch errors - action not supported
if(($xml->action == '') OR (in_array($xml->action, $local_supported_actions) == false)) {
	itrecht_log('ERROR: action not supported');
	return_error('10');
}

// Check api-version
if($xml->api_version != $local_api_version) {
	itrecht_log('ERROR: API version mismatch');
	return_error('1');
}

if(!empty($xml->user_auth_token)) {
	// user authentication w token (alternative to auth username/password)
	$local_user_auth_token = gm_get_conf('ITRECHT_TOKEN');
	if($xml->user_auth_token != $local_user_auth_token) {
		itrecht_log('ERROR: wrong token');
		return_error('3');
	}
}
else {
	itrecht_log('ERROR: token empty');
	return_error('3');
}

// ---------- begin action 'push' ----------
if($xml->action == 'push') {
	itrecht_log('INFO: received PUSH action');
	// Catch errors - rechtstext_type
	if(($xml->rechtstext_type == '') OR (in_array($xml->rechtstext_type, $local_supported_rechtstext_types) == false)) {
		itrecht_log('ERROR: document type not given or unsupported '.$xml->rechtstext_type);
		return_error('4');
	}
	// Catch errors - rechtstext_text
	if(strlen($xml->rechtstext_text) < 50) {
		itrecht_log('ERROR: text too short');
		return_error('5');
	}
	// Catch errors - rechtstext_html
	if(strlen($xml->rechtstext_html) < 50) {
		itrecht_log('ERROR: html too short');
		return_error('6');
	}
	// Catch errors - rechtstext_language
	if(($xml->rechtstext_language == '') OR (in_array($xml->rechtstext_language, $local_supported_rechtstext_languages) == false)) {
		itrecht_log('ERROR: language not given or unsupported');
		return_error('9');
	}

	// check if 'user_account_id' is valid and belongs to this user or return error - for multishop systems
	if($local_flag_multishop_system == true) {
		die('multishop not implemented.');
		// Catch errors - no user_account_id transmitted
		if(trim($xml->user_account_id) == '') {
			return_error('11');
		}
	}

	$rechtstext_type = (string)$xml->rechtstext_type;
	$rechtstext_language = (string)$xml->rechtstext_language;
	itrecht_log('INFO: document type: '.$rechtstext_type);

	// download pdf-file and verify md5_hash. pdf-files will be stored in directory $local_dir_for_pdf_storage
	if($local_rechtstext_pdf_required[$rechtstext_type] === true) {
		// Catch errors - element 'rechtstext_pdf_url' empty or URL invalid
		if(($xml->rechtstext_pdf_url == '') OR (url_valid($xml->rechtstext_pdf_url, $local_limit_download_from_host) !== true)) {
			itrecht_log('ERROR: URL for PDF not given or invalid - '.$xml->rechtstext_pdf_url);
			return_error('7');
		}

		// Download pdf file
		//$file_pdf_targetfilename = md5(uniqid("")).'.pdf'; // #### adapt the created filename to your needs, if required
		$file_pdf_targetfilename = $rechtstext_type.'_'.$rechtstext_language.'.pdf';
		$file_pdf_target = $local_dir_for_pdf_storage.$file_pdf_targetfilename;
		$file_pdf = @fopen($file_pdf_target,"w+");
		if($file_pdf === false) {
			itrecht_log('ERROR: error writing output file');
			return_error('7');
		} // catch errors
		//$t_pdf_data = @file_get_contents($xml->rechtstext_pdf_url);
		itrecht_log('retrieving PDF file '.$xml->rechtstext_pdf_url.' as '.$file_pdf_target);
		$t_pdf_data = itrk_retrieve_from_url($xml->rechtstext_pdf_url);
		$retval = @fwrite($file_pdf, $t_pdf_data);
		if($retval === false) {
			itrecht_log('ERROR: error writing output file');
			return_error('7');
		} // catch errors
		$retval = @fclose($file_pdf);
		if($retval === false) {
			itrecht_log('ERROR: error writing output file');
			return_error('7');
		} // catch errors

		// Catch errors - downloaded file was not properly saved
		if(file_exists($file_pdf_target) !== true) {
			itrecht_log('ERROR: error writing output file');
			return_error('7');
		}

		// verify that file is a pdf
		if(check_if_pdf_file($file_pdf_target) !== true) {
			itrecht_log('ERROR: file is not a PDF document');
			unlink($file_pdf_target);
			return_error('7');
		}

		// verify md5-hash, delete file if hash is not equal
		if(md5_file($file_pdf_target) != $xml->rechtstext_pdf_md5hash) {
			itrecht_log('ERROR: md5 hash mismatch');
			unlink($file_pdf_target);
			return_error('8');
		}

		itrecht_log('INFO: PDF written');
	}

	// store legal text (rechtstext) in database/file, create your own PDF from it, ... and log this 'push'-call if needed
	//$sql = "UPDATE tbl_legaltexts SET legaltext_text = '".mysql_real_escape_string($xml->rechtstext_text)."', legaltext_html = '".mysql_real_escape_string($xml->rechtstext_html)."' WHERE username = '".mysql_real_escape_string($xml->user_username)."' LIMIT 1"; // AND account_id = '".mysql_real_escape_string($xml->user_account_id)."'
	// verfify that the legal text was definitely updated
	$rechtstext_text = (string)$xml->rechtstext_text;
	$rechtstext_text = utf8_decode($rechtstext_text);
	$textfile = DIR_FS_CATALOG.'media/content/'.$rechtstext_type.'_'.$rechtstext_language.'.txt';
	file_put_contents($textfile, $rechtstext_text);
	if(file_exists($textfile) !== true) {
		itrecht_log('ERROR: error writing text file');
		return_error('7');
	}
	itrecht_log('INFO: text written to '.$textfile);
	$languages_id = $languages[(string)$xml->rechtstext_language];
	if($rechtstext_type == 'agb' && gm_get_conf('ITRECHT_USE_AGB_IN_PDF') == true) {
		gm_set_content('GM_PDF_CONDITIONS', $rechtstext_text, $languages_id);
		itrecht_log('INFO: Conditions (AGB) written to database for use in PDF invoices');
	}
	if($rechtstext_type == 'widerruf' && gm_get_conf('ITRECHT_USE_WITHDRAWAL_IN_PDF') == true) {
		gm_set_content('GM_PDF_WITHDRAWAL', $rechtstext_text, $languages_id);
		itrecht_log('INFO: Withdrawal written to database for use in PDF invoices');
	}

	$rechtstext_html_pre = '<!DOCTYPE html><html><head><title>'.$rechtstext_type.'</title>';
	$rechtstext_html_pre .= '<meta charset="UTF-8">';
	$rechtstext_html_pre .= '<style>body {font: 0.8em sans-serif;}</style>';
	$rechtstext_html_pre .= '</head><body>';
	$rechtstext_html_post .= '</body></html>';
	//$rechtstext_html_body = (string)$xml->rechtstext_html;
	//$rechtstext_html_body = utf8_decode((string)$xml->rechtstext_html);
	$rechtstext_html_body = itrk_convert_html((string)$xml->rechtstext_html);
	$rechtstext_html = $rechtstext_html_pre.$rechtstext_html_body.$rechtstext_html_post;
	$htmlfile = DIR_FS_CATALOG.'media/content/'.$rechtstext_type.'_'.$rechtstext_language.'.html';
	file_put_contents($htmlfile, $rechtstext_html);
	if(file_exists($htmlfile) !== true) {
		itrecht_log('ERROR: error writing html file');
		return_error('7');
	}
	itrecht_log('INFO: html written to '.$htmlfile);

	// return success and end script
	return_success();

} // ---------- end action 'push' ----------



// ---------- begin action 'getaccountlist' ----------
if($xml->action == 'getaccountlist') {
	xtc_db_close();
	die('action not supported.');
} // ---------- end action 'getaccountlist' ----------

// return general error
return_error('99');
xtc_db_close();
exit();

