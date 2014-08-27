<?php

/* -----------------------------------------------------------------
 * 	$Id: class.xtcprice.php 1166 2014-08-25 10:37:23Z akausch $
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

class xtcPrice_ORIGINAL {

    var $currencies;

    function xtcPrice_ORIGINAL($currency, $cGroup) {

        $this->currencies = array();
        $this->cStatus = array();
        $this->actualGroup = (int) $cGroup;
        $this->actualCurr = $currency;
        $this->TAX = array();
        $this->SHIPPING = array();
        $this->showFrom_Attributes = true;

        if (!defined('HTTP_CATALOG_SERVER') && isset($_SESSION['cart'])) {
            $this->content_type = $_SESSION['cart']->get_content_type();
        }

        // select Currencies
        $currencies_query = xtDBquery("SELECT * FROM " . TABLE_CURRENCIES);

        while ($currencies = xtc_db_fetch_array($currencies_query, true)) {
            $this->currencies[$currencies['code']] = array('title' => $currencies['title'],
                'symbol_left' => $currencies['symbol_left'],
                'symbol_right' => $currencies['symbol_right'],
                'decimal_point' => $currencies['decimal_point'],
                'thousands_point' => $currencies['thousands_point'],
                'decimal_places' => $currencies['decimal_places'],
                'value' => $currencies['value']);
        }

        if (!isset($this->currencies[$this->actualCurr])) {
            $this->actualCurr = DEFAULT_CURRENCY;
        }

        // select Customers Status data
        $customers_status_value = xtc_db_fetch_array(xtDBquery("SELECT * FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . $this->actualGroup . "' AND language_id = '" . $_SESSION['languages_id'] . "';"));
        $this->cStatus = array('customers_status_id' => $this->actualGroup,
            'customers_status_name' => $customers_status_value['customers_status_name'],
            'customers_status_image' => $customers_status_value['customers_status_image'],
            'customers_status_public' => $customers_status_value['customers_status_public'],
            'customers_status_discount' => $customers_status_value['customers_status_discount'],
            'customers_status_ot_discount_flag' => $customers_status_value['customers_status_ot_discount_flag'],
            'customers_status_ot_discount' => $customers_status_value['customers_status_ot_discount'],
            'customers_status_graduated_prices' => $customers_status_value['customers_status_graduated_prices'],
            'customers_status_show_price' => $customers_status_value['customers_status_show_price'],
            'customers_status_show_price_tax' => $customers_status_value['customers_status_show_price_tax'],
            'customers_status_add_tax_ot' => $customers_status_value['customers_status_add_tax_ot'],
            'customers_status_payment_unallowed' => $customers_status_value['customers_status_payment_unallowed'],
            'customers_status_shipping_unallowed' => $customers_status_value['customers_status_shipping_unallowed'],
            'customers_status_discount_attributes' => $customers_status_value['customers_status_discount_attributes'],
            'customers_fsk18' => $customers_status_value['customers_fsk18'],
            'customers_fsk18_display' => $customers_status_value['customers_fsk18_display']);

        // prefetch tax rates for standard zone
        $zones_query = xtDBquery("SELECT tax_class_id as class FROM " . TABLE_TAX_CLASS);
        while ($zones_data = xtc_db_fetch_array($zones_query)) {
            // calculate tax based on shipping or deliverey country (for downloads)
            if (isset($_SESSION['billto']) && isset($_SESSION['sendto'])) {
                $tax_address = xtc_db_fetch_array(xtDBquery("SELECT ab.entry_country_id,
														ab.entry_zone_id FROM " . TABLE_ADDRESS_BOOK . " ab
														LEFT JOIN " . TABLE_ZONES . " z ON (ab.entry_zone_id = z.zone_id)
														WHERE ab.customers_id = '" . $_SESSION['customer_id'] . "'
														AND ab.address_book_id = '" . ($this->content_type == 'virtual' ? $_SESSION['billto'] : $_SESSION['sendto']) . "';"));
                $this->TAX[$zones_data['class']] = xtc_get_tax_rate($zones_data['class'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']);
            } else {
                // Versandkosten im Warenkorb
                $country_id = -1;
                if (isset($_SESSION['country']) && !isset($_SESSION['customer_id'])) {
                    $country_id = $_SESSION['country'];
                }
                if (isset($_SESSION['AMZ_COUNTRY_ID']) && isset($_SESSION['AMZ_ZONE_ID'])) {
                    $tax_address = array('entry_country_id' => $_SESSION['AMZ_COUNTRY_ID'], 'entry_zone_id' => $_SESSION['AMZ_ZONE_ID']);
                    $this->TAX[$zones_data['class']] = xtc_get_tax_rate($zones_data['class'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']);
                } else {
                    $this->TAX[$zones_data['class']] = xtc_get_tax_rate($zones_data['class'], $country_id);
                }
            }
        }
    }

    // Produktpreis ermitteln
    function xtcGetPrice($pID, $format = true, $qty, $tax_class, $pPrice, $vpeStatus = 0, $cedit_id = 0, $price_type = '') {
        // Darf Kunde Preise sehen?
        if ($this->cStatus['customers_status_show_price'] == '0') {
            return $this->xtcShowNote($vpeStatus, $vpeStatus);
        }
        // Steuern ermitteln
        if ($cedit_id != 0) {
            require_once (DIR_FS_INC . 'xtc_oe_customer_infos.inc.php');
            $cinfo = xtc_oe_customer_infos($cedit_id);
            $products_tax = xtc_get_tax_rate($tax_class, $cinfo['country_id'], $cinfo['zone_id']);
        } else {
            $products_tax = $this->TAX[$tax_class];
        }
        if ($this->cStatus['customers_status_show_price_tax'] == '0') {
            $products_tax = '';
        }
        // Steuern 
        if ($pPrice == 0) {
            $pPrice = $this->getPprice($pID);
        }
        $pPrice = $this->xtcAddTax($pPrice, $products_tax);
        // Sonderangebot?
        if ($sPrice = $this->xtcCheckSpecial($pID)) {
            return $this->xtcFormatSpecial($pID, $this->xtcAddTax($sPrice, $products_tax), $pPrice, $format, $vpeStatus, $price_type);
        }
        // check graduated
        if ($this->cStatus['customers_status_graduated_prices'] == '1') {
            // Gruppenpreis ermitteln
            if ($sPrice = $this->xtcGetGraduatedPrice($pID, $qty)) {
                return $this->xtcFormatSpecialGraduated($pID, $this->xtcAddTax($sPrice, $products_tax), $pPrice, $format, $vpeStatus, $pID, $price_type);
            }
        } else {
            // check Group Price
            if ($sPrice = $this->xtcGetGroupPrice($pID, 1)) {
                return $this->xtcFormatSpecialGraduated($pID, $this->xtcAddTax($sPrice, $products_tax), $pPrice, $format, $vpeStatus, $pID, $price_type);
            }
        }
        // check Product Discount
        if ($discount = $this->xtcCheckDiscount($pID)) {
            return $this->xtcFormatSpecialDiscount($pID, $discount, $pPrice, $format, $vpeStatus);
        }

        return $this->xtcFormat($pPrice, $format, 0, false, $vpeStatus, $pID, $price_type);
    }

    function getPprice($pID) {
        $pData = xtc_db_fetch_array(xtDBquery("SELECT products_price FROM " . TABLE_PRODUCTS . " WHERE products_id='" . $pID . "';"));
        return $pData['products_price'];
    }

    function xtcAddTax($price, $tax) {
        $price = $price + $price / 100 * $tax;
        $price = $this->xtcCalculateCurr($price);
        return round($price, $this->currencies[$this->actualCurr]['decimal_places']);
    }

    function xtcCheckDiscount($pID) {
        if ($this->cStatus['customers_status_discount'] != '0.00') {
            $dData = xtc_db_fetch_array(xtDBquery("SELECT products_discount_allowed FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $pID . "';"));
            $discount = $dData['products_discount_allowed'];
            if ($this->cStatus['customers_status_discount'] < $discount) {
                $discount = $this->cStatus['customers_status_discount'];
            }
            if ($discount == '0.00') {
                return false;
            }
            return $discount;
        }
        return false;
    }

    function xtcGetGraduatedPrice($pID, $qty) {
        if (GRADUATED_ASSIGN == 'true') {
            if (xtc_get_qty($pID) > $qty) {
                $qty = xtc_get_qty($pID);
            }
        }
        if ($_SESSION['customers_status']['customers_status_id'] == '0') {
            $this->actualGroup = '1';
        }

        $graduated_price_data = xtc_db_fetch_array(xtDBquery("SELECT max(quantity) as qty
				                                FROM " . TABLE_PERSONAL_OFFERS_BY . $this->actualGroup . "
				                                WHERE products_id='" . $pID . "'
												AND personal_offer > 0
				                                AND quantity <= '" . $qty . "';"));

        if ($graduated_price_data['qty']) {
            $graduated_price_data = xtc_db_fetch_array(xtDBquery("SELECT personal_offer
						                                FROM " . TABLE_PERSONAL_OFFERS_BY . $this->actualGroup . "
						                                WHERE products_id='" . $pID . "'
														AND personal_offer > 0
						                                AND quantity = '" . $graduated_price_data['qty'] . "';"));

            $sPrice = $graduated_price_data['personal_offer'];
            if ($sPrice != 0.00) {
                return $sPrice;
            }
        } else {
            return;
        }
    }

    function xtcGetGroupPrice($pID, $qty) {
        if ($_SESSION['customers_status']['customers_status_id'] == '0') {
            $this->actualGroup = '1';
        }
        $graduated_price_data = xtc_db_fetch_array(xtDBquery("SELECT max(quantity) as qty
				                                FROM " . TABLE_PERSONAL_OFFERS_BY . $this->actualGroup . "
				                                WHERE products_id='" . $pID . "'
												AND personal_offer > 0
				                                AND quantity <= '" . $qty . "';"));

        if ($graduated_price_data['qty']) {
            $graduated_price_data = xtc_db_fetch_array(xtDBquery("SELECT personal_offer
						                                FROM " . TABLE_PERSONAL_OFFERS_BY . $this->actualGroup . "
						                                WHERE products_id='" . $pID . "'
														AND personal_offer > 0
						                                AND quantity = '" . $graduated_price_data['qty'] . "';"));

            $sPrice = $graduated_price_data['personal_offer'];
            if ($sPrice != 0.00) {
                return $sPrice;
            }
        } else {
            return;
        }
    }

    function xtcGetOptionPrice($pID, $option, $value, $products_price) {
        $attribute_price_data = xtc_db_fetch_array(xtDBquery("SELECT 
																pd.products_discount_allowed, 
																pd.products_tax_class_id, 
																p.options_values_price, 
																p.options_values_scale_price, 
																p.price_prefix, 
																p.options_values_weight, 
																p.weight_prefix 
															FROM 
																" . TABLE_PRODUCTS_ATTRIBUTES . " AS p
															INNER JOIN
																" . TABLE_PRODUCTS . " AS pd ON(pd.products_id = p.products_id )
															WHERE 
																p.products_id = '" . (int) $pID . "' 
															AND 
																p.options_id = '" . (int) $option . "' 
															AND 
																p.options_values_id = '" . (int) $value . ";'"));
        $discount = 0;
        if ($this->cStatus['customers_status_discount_attributes'] == 1 && $this->cStatus['customers_status_discount'] != 0.00) {
            $discount = $this->cStatus['customers_status_discount'];
            if ($attribute_price_data['products_discount_allowed'] < $this->cStatus['customers_status_discount'])
                $discount = $attribute_price_data['products_discount_allowed'];
        }
        if ($attribute_price_data['products_tax_class_id'] != 0) {
            $price = $this->xtcFormat($attribute_price_data['options_values_price'], false, $attribute_price_data['products_tax_class_id']);
        } else {
            $price = $this->xtcFormat($attribute_price_data['options_values_price'], false, $attribute_price_data['products_tax_class_id'], true);
        }
        $scale_price = $attribute_price_data['options_values_scale_price'];

        if ($attribute_price_data['weight_prefix'] != '+') {
            $attribute_price_data['options_values_weight'] *= -1;
        }
        if ($attribute_price_data['price_prefix'] == '+') {
            $price = $price - $price / 100 * $discount;
        } elseif ($attribute_price_data['price_prefix'] == '=') {
            $price = ($price - $price / 100 * $discount) - $products_price;
        } else {
            $price = ($price - $price / 100 * $discount) * -1;
        }

        return array('weight' => $attribute_price_data['options_values_weight'], 'price' => $price, 'scale_price' => $scale_price);
    }

    function calculate_optionscale($org_price, $scale_price, $att_quantity) {
        if (!$scale_price)
            return $org_price;
        $scale_prices = explode(',', $scale_price);
        for ($i = 0; $i < sizeof($scale_prices); $i++) {
            $scaleprice[] = explode(':', $scale_prices[$i]);
        }
        for ($j = 0; $j < sizeof($scaleprice); $j++) {
            if ($att_quantity >= $scaleprice[$j][0] && ($att_quantity < $scaleprice[$j + 1][0] || !$scaleprice[$j + 1])) {
                return $scaleprice[$j][1];
            }
        }
        return $org_price;
    }

    function xtcShowNote($vpeStatus, $vpeStatus = 0) {
        if ($vpeStatus == 1)
            return array('formated' => NOT_ALLOWED_TO_SEE_PRICES, 'plain' => 0);
        return NOT_ALLOWED_TO_SEE_PRICES;
    }

    // function xtcCheckSpecial($pID) {
    // $product = xtc_db_fetch_array(xtDBquery("SELECT specials_new_products_price FROM " . TABLE_SPECIALS . " WHERE products_id = '" . $pID . "' AND status = '1';"));
    // return $product['specials_new_products_price'];
    // }

    function xtcCheckSpecial($pID) {
        if (!column_exists(TABLE_SPECIALS, 'specials_price_' . $this->actualGroup)) {
            @xtc_db_query("ALTER TABLE specials ADD specials_price_" . $this->actualGroup . " DECIMAL( 15, 4 ) NOT NULL;");
        }
        $product = xtc_db_fetch_array(xtDBquery("SELECT specials_new_products_price, specials_price_" . $this->actualGroup . " FROM " . TABLE_SPECIALS . " WHERE products_id = '" . $pID . "' AND status = '1';"));
        $specialpr = $product['specials_new_products_price'];
        $specialgpr = $product['specials_price_' . $this->actualGroup];
        if ($specialpr != $specialgpr && $specialgpr > 0 && $specialgpr != '') {
            $specialprr = $specialgpr;
        } else {
            $specialprr = $specialpr;
        }
        return $specialprr;
    }

    function checkSQTY($pID) {
        $product = xtc_db_fetch_array(xtDBquery("SELECT specials_quantity FROM " . TABLE_SPECIALS . " WHERE products_id = '" . $pID . "' AND status = '1';"));
        return $product['specials_quantity'];
    }

    function xtcCalculateCurr($price) {
        return $this->currencies[$this->actualCurr]['value'] * $price;
    }

    function calcTax($price, $tax) {
        return $price * $tax / 100;
    }

    function xtcRemoveCurr($price) {
        if (DEFAULT_CURRENCY != $this->actualCurr) {
            return $price * (1 / $this->currencies[$this->actualCurr]['value']);
        } else {
            return $price;
        }
    }

    function xtcRemoveTax($price, $tax) {
        $price = ($price / (($tax + 100) / 100));
        return $price;
    }

    function xtcGetTax($price, $tax) {
        $tax = $price - $this->xtcRemoveTax($price, $tax);
        return $tax;
    }

    function xtcRemoveDC($price, $dc) {
        $price = $price - ($price / 100 * $dc);
        return $price;
    }

    function xtcGetDC($price, $dc) {
        $dc = $price / 100 * $dc;
        return $dc;
    }

    function checkAttributes($pID) {
        if (!$this->showFrom_Attributes)
            return;
        if ($pID == 0)
            return;
        $products_attributes = xtc_db_fetch_array(xtDBquery("SELECT count(*) AS total, sum(patrib.options_values_price) AS summe FROM " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib WHERE patrib.products_id='" . $pID . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int) $_SESSION['languages_id'] . "';"));
        if (($products_attributes['total'] > 0) && ($products_attributes['summe'] > 0)) {
            return ' ' . strtolower(FROM) . ' ';
        }
    }

    function xtcCalculateCurrEx($price, $curr) {
        return $price * ($this->currencies[$curr]['value'] / $this->currencies[$this->actualCurr]['value']);
    }

    /**
      Format Functions
     * */
    function xtcFormat($price, $format, $tax_class = 0, $curr = false, $vpeStatus = 0, $pID = 0, $price_type = '', $attr) {
        if ($curr) {
            $price = $this->xtcCalculateCurr($price);
        }

        if (STORE_COUNTRY == '22' || STORE_COUNTRY == '204') {
            $price = round($price * 20, 0) / 20;
        }

        if ($tax_class != 0) {
            $products_tax = $this->TAX[$tax_class];
            if ($this->cStatus['customers_status_show_price_tax'] == '0') {
                $products_tax = '';
            }
            $price = $this->xtcAddTax($price, $products_tax);
        }

        if ($format) {
            $Pprice = number_format((double) $price, $this->currencies[$this->actualCurr]['decimal_places'], $this->currencies[$this->actualCurr]['decimal_point'], $this->currencies[$this->actualCurr]['thousands_point']);
            $Pprice = $this->checkAttributes($pID) . $this->currencies[$this->actualCurr]['symbol_left'] . ' ' . $Pprice . ' ' . $this->currencies[$this->actualCurr]['symbol_right'];
            if ($vpeStatus == 0) {
                return $Pprice;
            } else {
                if ($price_type == 'info') {
                    return array('formated' => '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price">' . trim($Pprice) . '</span></div>', 'plain' => $price, 'cur_sm_right' => $this->currencies[$this->actualCurr]['symbol_right']);
                } else {
                    return array('formated' => trim($Pprice), 'plain' => $price, 'cur_sm_right' => $this->currencies[$this->actualCurr]['symbol_right']);
                }
            }
        } else {
            return round($price, $this->currencies[$this->actualCurr]['decimal_places']);
        }
    }

    function xtcFormatSpecialDiscount($pID, $discount, $pPrice, $format, $vpeStatus = 0, $price_type) {
        $sPrice = $pPrice - ($pPrice / 100) * $discount;
        if ($format) {
            $price = '<span class="product_info_old">' . INSTEAD . '' . $this->xtcFormat($pPrice, $format) . '</span>';
            $price .= '<br>';
            $price .= '<span class="product_info_real_price">' . $this->checkAttributes($pID) . $this->xtcFormat($sPrice, $format) . '</span>';
            if ($discount != 0) {
                $price .= '<br>';
                $price .= '<span class="product_price_save">';
                $price .= BOX_LOGINBOX_DISCOUNT . ': ' . round($discount) . '%';
                $price .= '</span>';
            }
            if ($vpeStatus == 0) {
                return $price;
            } else {
                return array('formated' => $price, 'plain' => $sPrice);
            }
        } else {
            return round($sPrice, $this->currencies[$this->actualCurr]['decimal_places']);
        }
    }

    function xtcFormatSpecial($pID, $sPrice, $pPrice, $format, $vpeStatus = 0, $price_type) {
        if ($format) {

            if ($price_type == 'info') {
                $price = '<span itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer">';
				$price .= '<meta itemprop="highPrice" content="' . trim($this->xtcFormat($pPrice, $format)) . '">';
				$price .= '<meta itemprop="lowPrice" content="' . trim($this->checkAttributes($pID) . $this->xtcFormat($sPrice, $format)) . '">';
				$price .= '<meta itemprop="offerCount" content="' . $this->checkSQTY($pID) . '">';
				$price .= '<span class="product_info_old">' . INSTEAD . '' . $this->xtcFormat($pPrice, $format) . '</span>';
				$price .= '<br>';
				$price .= '<span class="product_info_real_price">' . $this->checkAttributes($pID) . $this->xtcFormat($sPrice, $format) . '</span>';
				$price .= '</span>';
            } else {
                $price = '<span class="product_info_old">' . INSTEAD . '' . $this->xtcFormat($pPrice, $format) . '</span>';
				$price .= '<br>';
				$price .= '<span class="product_info_real_price">' . $this->checkAttributes($pID) . $this->xtcFormat($sPrice, $format) . '</span>';
            }

            if ($vpeStatus == 0) {
                return $price;
            } else {
                return array('formated' => $price, 'plain' => $sPrice);
            }
        } else {
            return round($sPrice, $this->currencies[$this->actualCurr]['decimal_places']);
        }
    }

    function xtcFormatSpecialGraduated($pID, $sPrice, $pPrice, $format, $vpeStatus = 0, $pID, $price_type) {
        $tQuery = xtc_db_fetch_array(xtDBquery("SELECT products_tax_class_id	FROM " . TABLE_PRODUCTS . " WHERE products_id='" . $pID . "';"));
        $tax_class = $tQuery[products_tax_class_id];

        if ($pPrice == 0)
            return $this->xtcFormat($sPrice, $format, 0, false, $vpeStatus, $price_type);

        if ($discount = $this->xtcCheckDiscount($pID))
            $sPrice -= $sPrice / 100 * $discount;

        if ($format) {
            if ($this->actualGroup == '0')
                $gruppe = '1';
            else
                $gruppe = $this->actualGroup;

            $sPricePlain = $sPrice;
            $pPricePlain = $pPrice;
			$sPrice = number_format($sPrice, $this->currencies[$this->actualCurr]['decimal_places'], $this->currencies[$this->actualCurr]['decimal_point'], $this->currencies[$this->actualCurr]['thousands_point']);
            $pPrice = number_format($pPrice, $this->currencies[$this->actualCurr]['decimal_places'], $this->currencies[$this->actualCurr]['decimal_point'], $this->currencies[$this->actualCurr]['thousands_point']);
            $pPriceRaw = $pPrice;
            $sPriceRaw = $sPrice;

            $sQuery = xtc_db_fetch_array(xtDBquery("SELECT max(quantity) as qty FROM " . TABLE_PERSONAL_OFFERS_BY . $gruppe . " WHERE products_id='" . $pID . "';"));

            if (($this->cStatus['customers_status_graduated_prices'] == '1') && ($sQuery['qty'] > 1)) {
                $bestPrice = $this->xtcGetGraduatedPrice($pID, $sQuery['qty']);
                if ($discount) {
                    $bestPrice -= $bestPrice / 100 * $discount;
                }
                $price .= FROM . $this->xtcFormat($bestPrice, $format, $tax_class) . ' <br><span class="single_price">' . SINGLE_PRICE . $this->xtcFormat($sPricePlain, $format) . '</span>';
            } elseif ($sPriceRaw != $pPriceRaw) {
                $price = '<span class="productOldPrice">' . MSRP . ' ' . $this->xtcFormat($pPricePlain, $format) . '</span><br>' . YOUR_PRICE . $this->checkAttributes($pID) . $this->xtcFormat($sPricePlain, $format);
            } elseif (($sQuery['qty'] == 1)) {
                $price = $this->xtcFormat($sPricePlain, $format);
            } else {
                $price = FROM . $this->xtcFormat($sPricePlain, $format);
            }
            if ($vpeStatus == 0) {
                return $price;
            } else {
                return array('formated' => $price, 'plain' => $sPrice);
            }
        } else {
            return round($sPrice, $this->currencies[$this->actualCurr]['decimal_places']);
        }
    }

    function get_decimal_places($code) {
        return $this->currencies[$this->actualCurr]['decimal_places'];
    }

}
