<?php
/*-----------------------------------------------------------------
* 	$Id: votingbox.php 371 2013-06-10 12:44:12Z akausch $
* 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ------------------------------------------------------------------
* 	based on:
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/

require_once DIR_WS_CLASSES.'class.shopvoting.php';

$box_smarty = new smarty;
$voting = new Shopvoting();
require_once (DIR_FS_INC.'xtc_date_long.inc.php');

$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
$box_content = '';

$readCheck = $voting->getGroupAccess($voting->customer_group_read,(int)$_SESSION['customers_status']['customers_status_id']);

if ($readCheck == 1 && $voting->voting_module_aktive == 1) {

    $bewertung_query = xtc_db_query("
        SELECT
            bewertung_id,
            bewertung_vorname,
            bewertung_nachname,
            bewertung_shoprating,
            bewertung_datum,
            bewertung_text,
            bewertung_kommentar										
        FROM
            ".Shopvoting::TABLE_BEWERTUNG."
        WHERE
            bewertung_sprache = '".xtc_db_input((int) $_SESSION['languages_id'])."'
        AND
            bewertung_status = 1
        AND
            bewertung_text != ''
        ORDER BY
            rand()
        DESC LIMIT 1");

    $bewertung_ausgabe = xtc_db_fetch_array($bewertung_query);


    $box_smarty->assign('DATUM', xtc_date_long($reviews['sdate_added']));
    $box_smarty->assign('BEWERTUNG_GESAMT_DURCHSCHNITT',  $voting->getAverage(Shopvoting::COLUMN_SHOPRATING));		
    $box_smarty->assign('GESAMT_STERNE', $voting->getStar( $voting->getAverage(Shopvoting::COLUMN_SHOPRATING)));		
    $box_smarty->assign('TEXT', htmlspecialchars($bewertung_ausgabe['bewertung_text']));
    $box_smarty->assign('TEXTLEN', strlen(htmlspecialchars($bewertung_ausgabe['bewertung_text'])));		
    $box_smarty->assign('LINK',xtc_href_link(Shopvoting::FILENAME_BEWERTUNGSSEITE)); 		
    $box_smarty->assign('BOX_CONTENT', $box_content);
    $box_smarty->assign('language', $_SESSION['language']);
	$box_smarty->assign('box_name', getBoxName('votingbox'));
	$box_smarty->assign('box_class_name', getBoxCSSName('votingbox'));
    $box_smarty->caching = false;
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_votingbox.html');
    
}
$smarty->assign('box_SHOP_BEWERTUNG',$box_voting);
