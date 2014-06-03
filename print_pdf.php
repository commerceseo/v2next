<?php

/* -----------------------------------------------------------------
 * 	$Id: print_pdf.php 420 2013-06-19 18:04:39Z akausch $
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

include ('includes/application_top.php');
require('pdf/html_table.php');

$content_data = xtc_db_fetch_array(xtc_db_query("SELECT
                     content_id,
                     content_title,
                     content_heading,
                     content_text,
                     content_file
                     FROM " . TABLE_CONTENT_MANAGER . "
                     WHERE content_group='" . (int) $_GET['content'] . "' 
                     AND languages_id='" . (int) $_SESSION['languages_id'] . "'"));

$name = utf8_decode($content_data['content_heading']);

$name = str_replace(' ', '-', $name) . '.pdf';

$pdf = new PDF_HTML_Table('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'U', 12);
$pdf->Cell(20, 10, utf8_decode($content_data['content_heading']));
$pdf->Ln(20);
$pdf->SetFont('Arial', '', 11);
if ($content_data['content_file'] != '') {
    ob_start();
    include (DIR_FS_CATALOG . 'media/content/' . $content_data['content_file']);
    $text = stripslashes(ob_get_contents());
    ob_end_clean();
}
else
    $text = stripslashes($content_data['content_text']);

$text = utf8_decode($text);
$text = html_entity_decode($text);

$pdf->WriteHTML($text);

$pdf->Output($name, 'D');
