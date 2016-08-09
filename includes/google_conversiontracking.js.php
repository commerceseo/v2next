<?php

/* -----------------------------------------------------------------
 * 	$Id: google_conversiontracking.js.php 1385 2015-01-27 15:17:22Z akausch $
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

$orders = xtc_db_fetch_array(xtc_db_query("SELECT orders_id FROM " . TABLE_ORDERS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' ORDER BY orders_id DESC LIMIT 1"));
$ot_total = xtc_db_fetch_array(xtc_db_query("SELECT value FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id='" . $orders['orders_id'] . "' AND class = 'ot_total';"));
echo '
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = ' . GOOGLE_CONVERSION_ID . ';
var google_conversion_language = "' . GOOGLE_LANG . '";
var google_conversion_format = "2";
var google_conversion_color = "ffffff";
var google_conversion_value = ' . number_format($ot_total['value'], 2, '.', '') . ';
var google_conversion_label = "' . GOOGLE_CONVERSION_LABEL . '";
/* ]]> */
</script>
';
echo '<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js"></script>';
echo '<noscript>';
echo '<div style="display:inline;">';
echo '<img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleadservices.com/pagead/conversion/' . GOOGLE_CONVERSION_ID . '/?value=' . number_format($ot_total['value'], 2, '.', '') . '&label=' . GOOGLE_CONVERSION_LABEL . '&guid=ON&script=0"/>';
echo '</div>';
echo '</noscript>';
