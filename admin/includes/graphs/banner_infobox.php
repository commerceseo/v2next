<?php

/* -----------------------------------------------------------------
 * 	$Id: banner_infobox.php 420 2013-06-19 18:04:39Z akausch $
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
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require(DIR_WS_CLASSES . 'phplot.php');

$stats = array();
$banner_stats_query = xtc_db_query("select dayofmonth(banners_history_date) as name, banners_shown as value, banners_clicked as dvalue from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $banner_id . "' and to_days(now()) - to_days(banners_history_date) < " . $days . " order by banners_history_date");
while ($banner_stats = xtc_db_fetch_array($banner_stats_query)) {
    $stats[] = array($banner_stats['name'], $banner_stats['value'], $banner_stats['dvalue']);
}

if (sizeof($stats) < 1)
    $stats = array(array(date('j'), 0, 0));

$graph = new PHPlot(200, 220, 'images/graphs/banner_infobox-' . $banner_id . '.' . $banner_extension);

$graph->SetFileFormat($banner_extension);
$graph->SetIsInline(1);
$graph->SetPrintImage(0);

$graph->draw_vert_ticks = 0;
$graph->SetSkipBottomTick(1);
$graph->SetDrawXDataLabels(0);
$graph->SetXTickLabelPos('none');
$graph->SetDrawYGrid(0);
$graph->SetPlotType('bars');
#$graph->SetDrawDataLabels(1);
$graph->SetLabelScalePosition(1);
$graph->SetMarginsPixels(15, 15, 15, 30);

$graph->SetTitleFontSize('4');
$graph->SetTitle('3 Day Statistics');

$graph->SetDataValues($stats);
$graph->SetDataColors(array('blue', 'red'), array('blue', 'red'));

$graph->DrawGraph();

$graph->PrintImage();
