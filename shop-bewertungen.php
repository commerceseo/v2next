<?php

/* -----------------------------------------------------------------
 * 	$Id: shop-bewertungen.php 542 2013-08-02 13:02:54Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

include('includes/application_top.php');
include(DIR_WS_CLASSES . 'class.shopvoting.php');
$smarty = new Smarty;

$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$voting = new Shopvoting();

$breadcrumb->add(NAVBAR_TITLE_SHOPBEWERTUNGEN, xtc_href_link(Shopvoting::FILENAME_BEWERTUNGSSEITE));

/**
 * check the group permission
 */
$readCheck = $voting->getGroupAccess($voting->customer_group_read, xtc_db_input((int) $_SESSION['customers_status']['customers_status_id']));

if ($readCheck != 1 || $voting->voting_module_aktive == 0) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
require (DIR_WS_INCLUDES . 'header.php');

/**
 * count the different votes
 */
$count_all_positiv = $voting->getCountSingleVotes(Shopvoting::POSITIV_RATES, Shopvoting::COLUMN_SHOPRATING);
$count_all_neutral = $voting->getCountSingleVotes(Shopvoting::NEUTRAL_RATES, Shopvoting::COLUMN_SHOPRATING);
$count_all_negative = $voting->getCountSingleVotes(Shopvoting::NEGATIVE_RATES, Shopvoting::COLUMN_SHOPRATING);

/**
 * get the average
 */
$average_all = $voting->getAverage(Shopvoting::COLUMN_SHOPRATING);
$average_site = $voting->getAverage(Shopvoting::COLUMN_SEITE);
$average_shipping = $voting->getAverage(Shopvoting::COLUMN_VERSAND);
$average_service = $voting->getAverage(Shopvoting::COLUMN_SERVICE);
$average_ware = $voting->getAverage(Shopvoting::COLUMN_WARE);

/**
 * necessary to set the css class for: show all
 */
if (strlen($_GET['anzeige']) != 8 || $_GET['anzeige'] == '') {
    $_GET['anzeige'] = "alle";
}

/**
 * assign a lot of smarty
 */
$smarty->assign('BEWERTUNG_POSITIV', $count_all_positiv);
$smarty->assign('BEWERTUNG_NEUTRAL', $count_all_neutral);
$smarty->assign('BEWERTUNG_NEGATIV', $count_all_negative);
$smarty->assign('BEWERTUNG_GESAMT', $voting->getCountDetailVotes(Shopvoting::COLUMN_SHOPRATING));
$smarty->assign('BEWERTUNG_GESAMT_SEITE', $voting->getCountDetailVotes(Shopvoting::COLUMN_SEITE));
$smarty->assign('BEWERTUNG_GESAMT_VERSAND', $voting->getCountDetailVotes(Shopvoting::COLUMN_VERSAND));
$smarty->assign('BEWERTUNG_GESAMT_SERVICE', $voting->getCountDetailVotes(Shopvoting::COLUMN_SERVICE));
$smarty->assign('BEWERTUNG_GESAMT_WARE', $voting->getCountDetailVotes(Shopvoting::COLUMN_WARE));
$smarty->assign('BEWERTUNG_POSITIV_PROZENTE', $voting->getPercentage($count_all_positiv));
$smarty->assign('BEWERTUNG_NEUTRAL_PROZENTE', $voting->getPercentage($count_all_neutral));
$smarty->assign('BEWERTUNG_NEGATIV_PROZENTE', $voting->getPercentage($count_all_negative));
$smarty->assign('BEWERTUNG_GESAMT_DURCHSCHNITT', $average_all);
$smarty->assign('BEWERTUNG_SEITE', $average_site);
$smarty->assign('BEWERTUNG_VERSAND', $average_shipping);
$smarty->assign('BEWERTUNG_SERVICE', $average_service);
$smarty->assign('BEWERTUNG_WARE', $average_ware);
$smarty->assign('GESAMT_STERNE', $voting->getStar($average_all));
$smarty->assign('SEITE_STERNE', $voting->getStar($average_site));
$smarty->assign('VERSAND_STERNE', $voting->getStar($average_shipping));
$smarty->assign('SERVICE_STERNE', $voting->getStar($average_service));
$smarty->assign('WARE_STERNE', $voting->getStar($average_ware));
$smarty->assign('AKTIVE_CLASS', $_GET['anzeige']);
$smarty->assign('LINK_ALLE', xtc_href_link(Shopvoting::FILENAME_BEWERTUNGSSEITE, '', 'NONSSL'));
$smarty->assign('LINK_POSITIV', xtc_href_link(Shopvoting::FILENAME_BEWERTUNGSSEITE, 'anzeige=positive', 'NONSSL'));
$smarty->assign('LINK_NEUTRAL', xtc_href_link(Shopvoting::FILENAME_BEWERTUNGSSEITE, 'anzeige=neutrale', 'NONSSL'));
$smarty->assign('LINK_NEGATIV', xtc_href_link(Shopvoting::FILENAME_BEWERTUNGSSEITE, 'anzeige=negative', 'NONSSL'));

/**
 * set the pagesplit info
 */
$vote_split = new splitPageResults($voting->getReviewSQL(true, $_GET['anzeige']), xtc_db_input((int) $_GET['page']), $voting->entry_per_page_frontend);

if ($vote_split->number_of_rows > 0) {
    $smarty->assign('NAVBAR', '<div class="fl">' .
            $vote_split->display_count(TEXT_DISPLAY_NUMBER_OF_REVIEWS) .
            '</div><div class="fr">' .
            $vote_split->display_links(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array('page', 'info', 'x', 'y'))) .
            '</div>');
}

if ($_GET['bewertung'] == 'ok') {
    if ($voting->activate_votings == 0) {
        $smarty->assign('BEWERTUNG', 'ok');
    } else {
        $smarty->assign('BEWERTUNG', 'free');
    }
}

$smarty->assign('module_content', $voting->getReviewVotes(xtc_db_input((int) $_GET['page']), $vote_split));
$smarty->assign('OPINION_LINK', '<a rel="nofollow" href="' . xtc_href_link(Shopvoting::FILENAME_BEWERTUNGSSEITE_SCHREIBEN, '', 'NONSSL') . '">' . cseo_wk_image_button('button_vote.gif', IMAGE_BUTTON_VOTE) . '</a>');

/**
 * caching is not necessary
 */
$smarty->caching = false;
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/shopbewertung.html', USE_TEMPLATE_DEVMODE));
$smarty->assign('main_content', $main_content);
$smarty->loadFilter('output', 'note');
$smarty->loadFilter('output', 'trimwhitespace');
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
