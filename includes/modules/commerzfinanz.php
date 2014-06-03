<?php

/* -----------------------------------------------------------------------------------------
  $Id: commerzfinanz.php,v 2.00 2013/10/11 Andreas Kausch
  copyright (c) 2013 www.commerce-seo.de
  --------------------------------------------------------------------------------------- */
include(DIR_WS_LANGUAGES . 'commerzfinanz.php');
require_once DIR_FS_CATALOG . 'includes/classes/class.commerzfinanz.php';
$SollZinz = new commerzfinanz();
if (DRESDNERFINANZ_STATUS == 'true' && $product->data['products_id']) {
    $products_price_1 = $xtPrice->xtcGetPrice($product->data['products_id'], $format = false, 1, $product->data['products_tax_class_id'], $product->data['products_price'], 1);
    $mindest_price = DRESDNERFINANZ_MINIMUM_PRICE_TITLE;
    $maximum_price = DRESDNERFINANZ_MAXIMUM_PRICE_TITLE;
    $kredit_wert = $products_price_1;
    if ($kredit_wert >= $mindest_price && $kredit_wert <= $maximum_price) {
	
		$zinssatz_value = DRESDNERFINANZ_ZINS_EFF;
		$sollzinssatz = $SollZinz->GetSollzinz($zinssatz_value);

        $zinssatz_value_formated = number_format($zinssatz_value, 2, ",", ".");
        $kredit_wert_formated = number_format($kredit_wert, 2, ",", "");
        $zins_satz = $zinssatz_value / 100;

        $monate6 = 6;
        $monate10 = 10;
        $monate12 = 12;
        $monate18 = 18;
        $monate24 = 24;
        $monate30 = 30;
        $monate36 = 36;
        $monate48 = 48;
        $monate60 = 60;
        $monate72 = 72;

        $rate6 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate6 / 12));
        $rate10 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate10 / 12));
        $rate12 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate12 / 12));
        $rate18 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate18 / 12));
        $rate24 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate24 / 12));
        $rate30 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate30 / 12));
        $rate36 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate36 / 12));
        $rate48 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate48 / 12));
        $rate60 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate60 / 12));
        $rate72 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate72 / 12));

        if ($rate72 >= 9) {
            $dresdner_rate = $monate72;
        } elseif ($rate60 >= 9) {
            $dresdner_rate = $monate60;
        } elseif ($rate48 >= 9) {
            $dresdner_rate = $monate48;
        } elseif ($rate36 >= 9) {
            $dresdner_rate = $monate36;
        } elseif ($rate30 >= 9) {
            $dresdner_rate = $monate30;
        } elseif ($rate24 >= 9) {
            $dresdner_rate = $monate24;
        } elseif ($rate18 >= 9) {
            $dresdner_rate = $monate18;
        } elseif ($rate12 >= 9) {
            $dresdner_rate = $monate12;
        } elseif ($rate12 >= 9) {
            $dresdner_rate = $monate10;
        } elseif ($rate6 >= 9) {
            $dresdner_rate = $monate6;
        }
    }


    $info_box_content .='<div align="left">';
    $info_box_content .='<div>' . TEXT_DRESDNER_LEICHTKAUF_VORTEILE . '</div>';

    $info_box_content .='<div>' . TEXT_DRESDNER_LEICHTKAUF . '</div>';

    $info_box_content .='<table border="0" cellspacing="1" cellpadding="1" width="100%">';
    $info_box_content .='<tr>';
    $info_box_content .='<td align="center" width="150" bgcolor="#DEEFF5" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><strong style="color: ' . DRESDNERFINANZ_CONTENT_COLOR . '; font-size: 10px;">' . TEXT_DRESDNER_LAUFZEIT_HEADER . '</strong></td>';
    $info_box_content .='<td align="center" width="150" bgcolor="#DEEFF5" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><strong style="color: ' . DRESDNERFINANZ_CONTENT_COLOR . '; font-size: 10px;">' . TEXT_DRESDNER_LAUFZEIT_MONATRATE . '</strong></td>';
    $info_box_content .='<td align="center" width="150" bgcolor="#DEEFF5" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><strong style="color: ' . DRESDNERFINANZ_CONTENT_COLOR . '; font-size: 10px;">' . TEXT_DRESDNER_JAHRESZINS . '</strong></td>';
    $info_box_content .='<td align="center" width="150" bgcolor="#DEEFF5" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><strong style="color: ' . DRESDNERFINANZ_CONTENT_COLOR . '; font-size: 10px;">' . TEXT_DRESDNER_SOLLZINS . '</strong></td>';
    $info_box_content .='<td align="center" width="150" bgcolor="#DEEFF5" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><strong style="color: ' . DRESDNERFINANZ_CONTENT_COLOR . '; font-size: 10px;">Gesamtbetrag</strong></td>';
    $info_box_content .='</tr>';
    if ($rate6 >= 9) {
        $info_box_content .='<tr>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $monate6 . '</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">6 x ' . number_format($rate6, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $zinssatz_value . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $sollzinssatz . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . number_format($monate6 * $rate6, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='</tr>';
    }
    if ($rate10 >= 9) {
        $info_box_content .='<tr>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $monate10 . '</td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">12 x ' . number_format($rate10, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $zinssatz_value . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $sollzinssatz . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . number_format($monate10 * $rate10, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='</tr>';
    }
    if ($rate12 >= 9) {
        $info_box_content .='<tr>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $monate12 . '</td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">12 x ' . number_format($rate12, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $zinssatz_value . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $sollzinssatz . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . number_format($monate12 * $rate12, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='</tr>';
    }
    if ($rate18 >= 9) {
        $info_box_content .='<tr>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $monate18 . '</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">18 x ' . number_format($rate18, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $zinssatz_value . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $sollzinssatz . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . number_format($monate18 * $rate18, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='</tr>';
    }
    if ($rate24 >= 9) {
        $info_box_content .='<tr>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $monate24 . '</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">24 x ' . number_format($rate24, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $zinssatz_value . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $sollzinssatz . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . number_format($monate24 * $rate24, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='</tr>';
    }
    if ($rate30 >= 9) {
        $info_box_content .='<tr>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $monate30 . '</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">30 x ' . number_format($rate30, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $zinssatz_value . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $sollzinssatz . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . number_format($monate30 * $rate30, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='</tr>';
    }
    if ($rate36 >= 9) {
        $info_box_content .='<tr>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $monate36 . '</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">36 x ' . number_format($rate36, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $zinssatz_value . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $sollzinssatz . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . number_format($monate36 * $rate36, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='</tr>';
    }
    if ($rate48 >= 9) {
        $info_box_content .='<tr>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $monate48 . '</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">48 x ' . number_format($rate48, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $zinssatz_value . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $sollzinssatz . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . number_format($monate48 * $rate48, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='</tr>';
    }
    if ($rate60 >= 9) {
        $info_box_content .='<tr>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $monate60 . '</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">60 x ' . number_format($rate60, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $zinssatz_value . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $sollzinssatz . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . number_format($monate60 * $rate60, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='</tr>';
    }
    if ($rate72 >= 9) {
        $info_box_content .='<tr>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $monate72 . '</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">72 x ' . number_format($rate72, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $zinssatz_value . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . $sollzinssatz . ' %</span></td>';
        $info_box_content .='<td align="center" style="border: 1px solid; border-color: ' . DRESDNERFINANZ_CONTENT_COLOR . ';"><span style="color: #000; font-size: 10px;">' . number_format($monate72 * $rate72, 2, ",", ".") . ' EUR</span></td>';
        $info_box_content .='</tr>';
    }
    $info_box_content .='</table>';
    $info_box_content .='</div>';

    $text_schonab = TEXT_DRESDNER_SCHONAB;
    $text_waehrung = TEXT_DRESDNER_WAEHRUNG;
    $text_oder = TEXT_DRESDNER_ODER;
    $info_smarty->assign('DRESDNER_PRODUCT_INFO', TEXT_DRESDNER_PRODUCT_1 . $dresdner_rate . TEXT_DRESDNER_PRODUCT_2 . number_format($dresdner_rate * $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$dresdner_rate / 12)), 2, ",", ".") . TEXT_DRESDNER_PRODUCT_2_1 . $sollzinssatz . TEXT_DRESDNER_PRODUCT_3 . $zinssatz_value . TEXT_DRESDNER_PRODUCT_4);
    $info_smarty->assign('BOX_DRESDNER_FINANZ', $info_box_content);

    if ($rate72 >= 9) {
        $info_smarty->assign('DRESDNER_PPRICE_FINANZ', $text_schonab . number_format($rate72, 2, ",", ".") . $text_waehrung);
    } elseif ($rate60 >= 9) {
        $info_smarty->assign('DRESDNER_PPRICE_FINANZ', $text_schonab . number_format($rate60, 2, ",", ".") . $text_waehrung);
    } elseif ($rate48 >= 9) {
        $info_smarty->assign('DRESDNER_PPRICE_FINANZ', $text_schonab . number_format($rate48, 2, ",", ".") . $text_waehrung);
    } elseif ($rate36 >= 9) {
        $info_smarty->assign('DRESDNER_PPRICE_FINANZ', $text_schonab . number_format($rate36, 2, ",", ".") . $text_waehrung);
    } elseif ($rate30 >= 9) {
        $info_smarty->assign('DRESDNER_PPRICE_FINANZ', $text_schonab . number_format($rate30, 2, ",", ".") . $text_waehrung);
    } elseif ($rate24 >= 9) {
        $info_smarty->assign('DRESDNER_PPRICE_FINANZ', $text_schonab . number_format($rate24, 2, ",", ".") . $text_waehrung);
    } elseif ($rate18 >= 9) {
        $info_smarty->assign('DRESDNER_PPRICE_FINANZ', $text_schonab . number_format($rate18, 2, ",", ".") . $text_waehrung);
    } elseif ($rate12 >= 9) {
        $info_smarty->assign('DRESDNER_PPRICE_FINANZ', $text_schonab . number_format($rate12, 2, ",", ".") . $text_waehrung);
	} elseif ($rate10 >= 9) {
        $info_smarty->assign('DRESDNER_PPRICE_FINANZ', $text_schonab . number_format($rate10, 2, ",", ".") . $text_waehrung);
    } elseif ($rate6 >= 9) {
        $info_smarty->assign('DRESDNER_PPRICE_FINANZ', $text_schonab . number_format($rate6, 2, ",", ".") . $text_waehrung);
    }
}
?>