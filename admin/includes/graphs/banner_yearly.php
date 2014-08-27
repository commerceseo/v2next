<?php

/* -----------------------------------------------------------------
 * 	$Id: banner_yearly.php 420 2013-06-19 18:04:39Z akausch $
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

$stats = array(array('0', '0', '0'));
$banner_stats_query = xtc_db_query("select year(banners_history_date) as year, sum(banners_shown) as value, sum(banners_clicked) as dvalue from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $banner_id . "' group by year");
while ($banner_stats = xtc_db_fetch_array($banner_stats_query)) {
    $stats[] = array($banner_stats['year'], (($banner_stats['value']) ? $banner_stats['value'] : '0'), (($banner_stats['dvalue']) ? $banner_stats['dvalue'] : '0'));
}

$graph = new PHPlot(600, 350, 'images/graphs/banner_yearly-' . $banner_id . '.' . $banner_extension);

$graph->SetFileFormat($banner_extension);
$graph->SetIsInline(1);
$graph->SetPrintImage(0);

$graph->SetSkipBottomTick(1);
$graph->SetDrawYGrid(1);
$graph->SetPrecisionY(0);
$graph->SetPlotType('lines');
$graph->SetXTickLabelPos('none');

$graph->SetPlotBorderType('left');
$graph->SetTitleFontSize('4');
$graph->SetTitle(sprintf(TEXT_BANNERS_YEARLY_STATISTICS, $banner['banners_title']));

$graph->SetBackgroundColor('white');

$graph->SetVertTickPosition('plotleft');
$graph->SetDataValues($stats);
$graph->SetDataColors(array('blue', 'red'), array('blue', 'red'));

$graph->DrawGraph();

$graph->PrintImage();
