<?php
/**
 * This material may not be reproduced, displayed, modified or distributed
 * without the express prior written permission of the copyright holder.
 * 
 * Copyright (c) 2000 - 2011 etracker GmbH. All Rights Reserved
 */

define('ET_CODE_VERSION',	'3.0');
define('ET_TAGHOST',		defined('ET_CODE_HOST') ? ET_CODE_HOST : 'code.etracker.com');
define('ET_CNTHOST',		defined('ET_SERVERHOST') ? ET_SERVERHOST : 'www.etracker.de');

/**
 * Get code
 * 
 * @param	string 	$cryptId
 * @param	boolean $easy [false]	kein pagename, ermittels aus ref
 * @param	boolean $ssl [false]		
 * @param	string 	$pagename ['']	
 * @param	string 	$areas ['']		bereiche übergeben, mehrere möglich
 * @param	integer $ilevel [0]		interesse level, int
 * @param	string 	$targets ['']	ziele
 * @param	float 	$tval ['']		umsatz
 * @param	integer $tsale [0]		0 oder 1 lead oder sale
 * @param	string 	$tonr ['']		target order nr
 * @param	integer $lpage [0]		landing page
 * @param	string 	$trigger ['']	Liste von kommaseparierten Kanal-Ids der konfigurierten Trigger
 * @param	integer $customer [0]	bestandskunde [1] oder neukunde [0]
 * @param	string 	$basket ['']		warenkorb
 * @param	integer $se [0]			automatischer suchmaschinenkanal
 * @param	string 	$url ['']		url der seite
 * @param	string 	$tag ['']		
 * @param	string 	$organisation ['']
 * @param	string 	$demographic ['']
 * @param	boolean $free [false]	free accounts
 * @param	boolean $showAll [false]	
 * @param	boolean $noScript [false]	
 * @param	string 	$sub ['']		
 * @return	string
 */
function getCode( $cryptId,
				  $easy			= true,
				  $ssl			= false,
				  $pagename		= '',
				  $areas		= '',
				  $ilevel		= 0,
				  $targets		= '',
				  $tval			= '',
				  $tsale		= 0,
				  $tonr			= 0,
				  $lpage		= 0,
				  $trigger		= 0,
				  $customer		= 0,
				  $basket 		= '',
				  $se 			= 0,
				  $plugins		= false,
				  $url			= '',
				  $overlay		= false,
				  $tag			= '',
				  $organisation = '',
				  $demographic  = '',
				  $free 		= false,
				  $showAll		= false,
				  $noScript		= false,
                                  $sub			= ''
				)
{
	if(!preg_match("/^[0-9a-zA-Z]+$/", $cryptId))
		return '';

	// parameter check
	$easy			= $easy ? 1 : 0;
	$ssl			= $ssl ? 1 : 0;
	$pagename		= rawurlencode( $pagename );
	$areas			= rawurlencode( $areas );
	$ilevel			= $ilevel ? $ilevel : 0;
	$targets		= rawurlencode( $targets );
	$tval 			= str_replace(',', '.', $tval);
	$tval 			= is_numeric($tval) ? $tval : 0;
	$tsale			= $tsale ? 1 : 0;
	$tonr 			= str_replace('"', '', $tonr);
	$lpage 			= is_numeric($lpage) ? $lpage : 0;
	// trigger
	$trigger		= preg_replace("/\s{1,}/", '', $trigger); // remove all \s*
	$trigger		= preg_match("/^[0-9,]+$/", $trigger) ? $trigger : ''; // comma separated list of integers
	$customer		= $customer ? 1 : 0;
	$basket			= rawurlencode( $basket );
	$se				= is_numeric($se) ? $se : 0;
	$url			= str_replace('"', '', $url);
	$tag			= str_replace('"', '', $tag);
        $sub			= str_replace('"', '', $sub);
	$organisation 	= rawurlencode($organisation);
	$demographic  	= rawurlencode($demographic);
	$noScript		= $noScript ? true : false;	

	$code  = "<!-- Copyright (c) 2000-".date("Y")." etracker GmbH. All rights reserved. -->\n";
	$code .= "<!-- This material may not be reproduced, displayed, modified or distributed -->\n";
	$code .= "<!-- without the express prior written permission of the copyright holder. -->\n\n";
	$code .= "<!-- BEGIN etracker Tracklet ".ET_CODE_VERSION." -->\n";
	$code .= "<script type=\"text/javascript\">document.write(String.fromCharCode(60)+'script type=\"text/javascript\" src=\"http'+(\"https:\"==document.location.protocol?\"s\":\"\")+'://".ET_TAGHOST."/t.js?et=".$cryptId."\">'+String.fromCharCode(60)+'/script>');</script>\n";
	//$code .= "<p style=\"display:none;\" id=\"et_count\"></p>";
	$code .= getParameters( $showAll, $easy, $pagename, $areas, $ilevel,
							$targets, $tval, $tsale, $tonr, $lpage, $trigger,
							$customer, $basket, $free, $se, $url, $tag,
							$organisation,  $demographic, $sub );

	$code .= "<script type=\"text/javascript\">_etc();</script>\n";
	$code .= "<noscript><p><a href=\"http://www.etracker.com\"><img style=\"border:0px;\" alt=\"\" src=\"https://www.etracker.com/nscnt.php?et=".$cryptId."\" /></a></p></noscript>\n";

	if($noScript)
		$code .= getNoScriptTag( $cryptId, $easy, $ssl, $pagename, $areas, $ilevel,
								 $targets, $tval, $tsale, $tonr, $lpage, $trigger,
								 $customer, $basket, $free, $se, $url, $tag,
								 $organisation,  $demographic, $sub );

	$code .= "<!-- etracker CODE END -->\n\n";

	
	return $code;
}
/***********************************************************
 * Note: private function below
 * use only main function 'getCode()'
/***********************************************************/

/**
 * Get parameters
 * gives back the parameter code block
 * 
 * @param	boolean $easy [false]
 * @param	boolean $ssl [false]
 * @param	string 	$pagename ['']
 * @param	string 	$areas ['']
 * @param	integer $ilevel [0]
 * @param	string 	$targets ['']
 * @param	float 	$tval ['']
 * @param	integer $tsale [0]
 * @param	string 	$tonr ['']
 * @param	integer $lpage [0]
 * @param	string 	$trigger ['']
 * @param	integer $customer [0]
 * @param	string 	$basket ['']
 * @param	boolean $free [false]
 * @param	integer $se [0]
 * @param	string 	$url ['']
 * @param	string 	$tag ['']
 * @param	string 	$organisation ['']
 * @param	string 	$demographic ['']
 * @param	string 	$sub ['']		
 * @return	string
 */
function getParameters(	$showAll 		= false,
						$easy 			= 0,
						$pagename 		= '',
						$areas 			= '',
						$ilevel 		= 0,
						$targets 		= '',
						$tval 			= '',
						$tsale 			= 0,
						$tonr 			= 0,
						$lpage 			= 0,
						$trigger 		= 0,
						$customer		= 0,
						$basket 		= '',
						$free 			= false,
						$se 			= 0,
						$url			= '',
						$tag			= '',
						$organisation 	= '',
						$demographic  	= '',
                                                $sub			= ''
					  )
{
	$code = '';	

	if($easy)
		$code .= "var et_easy         = $easy;\n";
	if($pagename || $showAll)
		$code .= "var et_pagename     = \"$pagename\";\n";
	if($areas || $showAll)
		$code .= "var et_areas        = \"$areas\";\n";
	if($ilevel || $showAll)
		$code .= "var et_ilevel       = ".$ilevel.";\n";
	if($url || $showAll)
		$code .= "var et_url          = \"$url\";\n";
	if($tag || $showAll)
		$code .= "var et_tag          = \"$tag\";\n";
        if($sub || $showAll)
		$code .= "var et_sub          = \"$sub\";\n";
	if($organisation)
		$code .= "var et_organisation = \"$organisation\";\n";
	if($demographic)
		$code .= "var et_demographic  = \"$demographic\";\n";
	if($targets || $showAll)
		$code .= "var et_target       = \"$targets\";\n";
	if($tval || $showAll)
		$code .= "var et_tval         = \"$tval\";\n";
	if($tonr || $showAll)
		$code .= "var et_tonr         = \"$tonr\";\n";
	if($tsale || $showAll)
		$code .= "var et_tsale        = $tsale;\n";
	if($customer || $showAll)
		$code .= "var et_cust         = $customer;\n";
	if($basket || $showAll)
		$code .= "var et_basket       = \"$basket\";\n";
	if($lpage || $showAll)
		$code .= "var et_lpage        = \"$lpage\";\n";
	if($trigger || $showAll)
		$code .= "var et_trig         = \"$trigger\";\n";
	if($se || $showAll)
		$code .= "var et_se           = \"$se\";\n";

	$ret = '';
	if($code)
	{
		$ret .= "\n<!-- etracker PARAMETER ".ET_CODE_VERSION." -->\n";
		$ret .= "<script type=\"text/javascript\">\n";
		$ret .= $code;
		$ret .= "</script>\n";
		$ret .= "<!-- etracker PARAMETER END -->\n\n";
	}
	return $ret;
}

/**
 * Get noscript block
 * gives back the noscript image tag
 * 
 * @param	string 	$cryptId
 * @param	boolean $easy [false]
 * @param	boolean $ssl [false]
 * @param	string 	$pagename ['']
 * @param	string 	$areas ['']
 * @param	integer $ilevel [0]
 * @param	string 	$targets ['']
 * @param	float 	$tval ['']
 * @param	integer $tsale [0]
 * @param	string 	$tonr ['']
 * @param	integer $lpage [0]
 * @param	string 	$trigger ['']
 * @param	integer $customer [0]
 * @param	string 	$basket ['']
 * @param	boolean $free [false]
 * @param	integer $se [0]
 * @param	string 	$url ['']
 * @param	string 	$tag ['']
 * @param	string 	$organisation ['']
 * @param	string 	$demographic ['']
 * @param	string 	$sub ['']		
 * @return	string
 */
function getNoScriptTag($cryptId,
						$easy 			= false,
						$ssl 			= false,
						$pagename 		= '',
						$areas 			= '',
						$ilevel 		= 0,
						$targets 		= '',
						$tval 			= '',
						$tsale 			= 0,
						$tonr 			= 0,
						$lpage 			= 0,
						$trigger 		= 0,
						$customer		= 0,
						$basket 		= '',
						$free 			= false,
						$se 			= 0,
						$url			= '',
						$tag			= '',
						$organisation 	= '',
						$demographic  	= '',
                                                $sub			= '')
{
	$script 		= $free ? 'fcnt' : 'cnt';
	
	$code = "<!-- etracker CODE NOSCRIPT ".ET_CODE_VERSION." -->\n";
	$code .= "<noscript>\n";
	$code .= "<p><a href='http://".ET_CNTHOST."/app?et=$cryptId'>\n";
	$code .= "<img style='border:0px;' alt='' src='";
	if($ssl==1) $code .= "https"; else $code .= "http";
	$code .= "://".ET_CNTHOST."/$script.php?\n";
	$code .= "et=$cryptId&amp;v=".ET_CODE_VERSION."&amp;java=n&amp;et_easy=$easy\n";
	$code .= "&amp;et_pagename=$pagename\n";
	$code .= "&amp;et_areas=$areas&amp;et_ilevel=$ilevel&amp;et_target=$targets,$tval,$tonr,$tsale\n";
	$code .= "&amp;et_lpage=$lpage&amp;et_trig=$trigger&amp;et_se=$se&amp;et_cust=$customer\n";
	$code .= "&amp;et_basket=$basket&amp;et_url=&amp;et_tag=".$tag."&amp;et_sub=".$sub."\n";
	$code .= "&amp;et_organisation=".$organisation."&amp;et_demographic=".$demographic."' /></a></p>\n";
	$code .= "</noscript>\n";
	$code .= "<!-- etracker CODE NOSCRIPT END-->\n\n";
	return $code;
}

/**
 * Get base code
 * gives back the base code block
 * 
 * @param	string 	$cryptId
 * @param	boolean $easy [false]
 * @param	boolean $ssl [false]
 * @param	string 	$pagename ['']
 * @param	string 	$areas ['']
 * @param	integer $ilevel [0]
 * @param	string 	$targets ['']
 * @param	float 	$tval ['']
 * @param	integer $tsale [0]
 * @param	string 	$tonr ['']
 * @param	integer $lpage [0]
 * @param	string 	$trigger ['']
 * @param	integer $customer [0]
 * @param	string 	$basket ['']
 * @param	boolean $free [false]
 * @param	integer $se [0]
 * @param	string 	$url ['']
 * @param	string 	$tag ['']
 * @param	string 	$organisation ['']
 * @param	string 	$demographic ['']
 * @param	string 	$sub ['']		
 * @return	string
 */
function getBaseCode( $cryptId,
					  $easy 		= false,
					  $ssl 			= false,
					  $pagename 	= '',
					  $areas 		= '',
					  $ilevel 		= 0,
					  $targets 		= '',
					  $tval 		= '',
					  $tsale 		= 0,
					  $tonr 		= 0,
					  $lpage 		= 0,
					  $trigger 		= 0,
					  $customer		= 0,
				  	  $basket 		= '',
					  $free 		= false,
					  $se 			= 0,
					  $url			= '',
					  $tag			= '',
					  $organisation = '',
					  $demographic  = '',
                                          $sub  = ''
)
{
	return getCode( $cryptId,
					$easy,
					$ssl,
					$pagename,
					$areas,
					$ilevel,
					$targets,
					$tval,
					$tsale,
					$tonr,
					$lpage,
					$trigger,
					$customer,
					$basket,
					$se,
					true,
					$url,
					true,
					$tag,
					$organisation,
					$demographic,
					$free,
					true,
					true,
                                        $sub
				);
}
?>