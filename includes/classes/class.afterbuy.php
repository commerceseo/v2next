<?php

/* -----------------------------------------------------------------
 * 	$Id: class.afterbuy.php 816 2014-01-28 07:07:08Z akausch $
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

class xtc_afterbuy_functions {

    var $order_id;

    // constructor
    function xtc_afterbuy_functions($order_id) {
        $this->order_id = $order_id;
    }

    function process_order() {

        require_once (DIR_FS_INC . 'xtc_get_attributes_model.inc.php');


        // ############ SETTINGS ################
        //Daten im XT Admin (werden von Afterbuy mitgeteilt)
        $PartnerID = AFTERBUY_PARTNERID;
        $PartnerPass = AFTERBUY_PARTNERPASS;
        $UserID = AFTERBUY_USERID;
        $order_status = AFTERBUY_ORDERSTATUS;

        // ############ THUNK ################

        $oID = $this->order_id;
        $customer = array();
        // $afterbuy_URL = 'https://api.afterbuy.de/afterbuy/ShopInterface.aspx';
        $afterbuy_URL = 'https://api.afterbuy.de/afterbuy/ShopInterfaceUTF8.aspx';

        // connect
        $ch = curl_init();

        // This is the URL that you want PHP to fetch. You can also set this option when initializing a session with the curl_init()  function.
        curl_setopt($ch, CURLOPT_URL, "$afterbuy_URL");

        // curl_setopt($ch, CURLOPT_CAFILE, 'D:/curl-ca.crt');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //bei einer leeren Transmission Error Mail + cURL Problemen die naechste Zeile auskommentieren
        //curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        // Set this option to a non-zero value if you want PHP to do a regular HTTP POST. This POST is a normal application/x-www-form-rawurlencoded  kind, most commonly used by HTML forms.
        curl_setopt($ch, CURLOPT_POST, 1);

        // get order data
        $o_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS . " WHERE orders_id='" . $oID . "'");
        $oData = xtc_db_fetch_array($o_query);

        // customers Address
        $customer['id'] = $oData['customers_id'];
        $customer['firma'] = rawurlencode($oData['billing_company']);
        $customer['vorname'] = rawurlencode($oData['billing_firstname']);
        $customer['nachname'] = rawurlencode($oData['billing_lastname']);
        $customer['strasse'] = rawurlencode($oData['billing_street_address']);
        $customer['strasse2'] = rawurlencode($oData['billing_suburb']);
        $customer['plz'] = $oData['billing_postcode'];
        $customer['ort'] = rawurlencode($oData['billing_city']);
        $customer['tel'] = $oData['customers_telephone'];
        $customer['fax'] = "";
        $customer['mail'] = $oData['customers_email_address'];
        // get ISO code
        $ctr_query = xtc_db_query("SELECT countries_iso_code_2 FROM " . TABLE_COUNTRIES . " WHERE  countries_name='" . $oData['customers_country'] . "'");
        $crt_data = xtc_db_fetch_array($ctr_query);
        $customer['land'] = $crt_data['countries_iso_code_2'];

        // ############ VAT_ID ################

        $ustid_querystrg = "SELECT customers_vat_id, customers_status FROM " . TABLE_CUSTOMERS . " WHERE customers_id ='" . $customer['id'] . "'";
        $ustid_query = xtc_db_query($ustid_querystrg);
        $ustid_data = xtc_db_fetch_array($ustid_query);
        $customer['ustid'] = $ustid_data['customers_vat_id'];

        // ############ CUSTOMERS ANREDE ################

        $c_query = xtc_db_query("SELECT customers_gender FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . $customer['id'] . "'");
        $c_data = xtc_db_fetch_array($c_query);
        switch ($c_data['customers_gender']) {
            case 'm' :
                $customer['gender'] = 'Herr';
                break;
            case 'f' :
                $customer['gender'] = 'Frau';
                break;
            default :
                $customer['gender'] = '';
                break;
        }

        // ############ DELIVERY ADRESS ################
        // modified FT (Neuer Parameter uebergabe der 2.Adresszeile)

        $customer['d_firma'] = rawurlencode($oData['delivery_company']);
        $customer['d_vorname'] = rawurlencode($oData['delivery_firstname']);
        $customer['d_nachname'] = rawurlencode($oData['delivery_lastname']);
        $customer['d_strasse'] = rawurlencode($oData['delivery_street_address']);
        $customer['d_strasse2'] = rawurlencode($oData['delivery_suburb']);
        $customer['d_plz'] = $oData['delivery_postcode'];
        $customer['d_ort'] = rawurlencode($oData['delivery_city']);
        // get ISO code
        $ctr_query = xtc_db_query("SELECT countries_iso_code_2 FROM " . TABLE_COUNTRIES . " WHERE  countries_name='" . $oData['delivery_country'] . "'");
        $crt_data = xtc_db_fetch_array($ctr_query);
        $customer['d_land'] = $crt_data['countries_iso_code_2'];

        // ############# KUNDENERKENNUNG SETZEN #############
        // Modifiziert FT

        $kundenerkennung = '1';
        $DATAstring .= "Kundenerkennung=" . $kundenerkennung . "&";
        // 0=Standard EbayName (= gesamte Zeile "Benutzername" in dieser Datei)
        // 1=Email
        // 2=EKNummer (wenn im XT vorhanden!)
        // ############ GET PRODUCT RELATED TO ORDER / INIT GET STRING ################
        // modified FT (Leerzeichen)

        $p_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id='" . $oID . "'");
        $p_count = xtc_db_num_rows($p_query);
        $DATAstring .= "Action=new&";
        $DATAstring .= "PartnerID=" . $PartnerID . "&";
        $DATAstring .= "PartnerPass=" . $PartnerPass . "&";
        $DATAstring .= "UserID=" . $UserID . "&";
        $DATAstring .= "Kbenutzername=" . $customer['id'] . "_XTC_" . $oID . "&";
        #oder
        #$DATAstring .= "Kbenutzername=".$customer['mail']."_XTC_".$oID."&";
        $DATAstring .= "Kanrede=" . $customer['gender'] . "&";
        $DATAstring .= "KFirma=" . $customer['firma'] . "&";
        $DATAstring .= "KVorname=" . $customer['vorname'] . "&";
        $DATAstring .= "KNachname=" . $customer['nachname'] . "&";
        $DATAstring .= "KStrasse=" . $customer['strasse'] . "&";
        $DATAstring .= "KStrasse2=" . $customer['strasse2'] . "&";
        $DATAstring .= "KPLZ=" . $customer['plz'] . "&";
        $DATAstring .= "KOrt=" . $customer['ort'] . "&";
        $DATAstring .= "KTelefon=" . $customer['tel'] . "&";
        $DATAstring .= "Kfax=&";
        $DATAstring .= "Kemail=" . $customer['mail'] . "&";
        $DATAstring .= "KLand=" . $customer['land'] . "&";
        $DATAstring .= "KLFirma=" . $customer['d_firma'] . "&";
        $DATAstring .= "KLVorname=" . $customer['d_vorname'] . "&";
        $DATAstring .= "KLNachname=" . $customer['d_nachname'] . "&";
        $DATAstring .= "KLStrasse=" . $customer['d_strasse'] . "&";
        $DATAstring .= "KLStrasse2=" . $customer['d_strasse2'] . "&";
        $DATAstring .= "KLPLZ=" . $customer['d_plz'] . "&";
        $DATAstring .= "KLOrt=" . $customer['d_ort'] . "&";
        $DATAstring .= "KLLand=" . $customer['d_land'] . "&";
        $DATAstring .= "UsStID=" . $customer['ustid'] . "&";
        $DATAstring .= "VID=" . $oID . "&";

        // ############# HaeNDLERMARKIERUNG AFTERBUY KUNDENDATENSATZ #############
        // Modifiziert FT
        // "H" Kennzeichnung im Kundendatensatz in Afterbuy
        // "Haendler=0&" bedeutet Checkbox deaktiviert
        // "Haendler=1&" bedeutet Checkbox aktiviert
        // "case 'X'" steht fuer die jeweilige Kundengruppen_ID im XT (-->siehe Admin)

        $customer_status = $ustid_data['customers_status'];
        switch ($customer_status) {
            case '0': //Admin
                $DATAstring .= "Haendler=0&";
                break;
            case '1': //Gast
                $DATAstring .= "Haendler=0&";
                break;
            case '2': //Kunde
                $DATAstring .= "Haendler=0&";
                break;
            case '3': //im Standard B2B
                $DATAstring .= "Haendler=1&";
                break;
            case '4': //eigene Kundengruppe
                $DATAstring .= "Haendler=0&";
                break;
            case '5': //eigene Kundengruppe
                $DATAstring .= "Haendler=0&";
                break;
            case '6': //eigene Kundengruppe
                $DATAstring .= "Haendler=0&";
                break;
            case '7': //eigene Kundengruppe
                $DATAstring .= "Haendler=0&";
                break;
            default: //wenn alles nicht zutrifft
                $DATAstring .= "Haendler=0&";
        }

        // ############# LIEFERANSCHRIFT SETZEN #############
        // Modifiziert FT (Neuer Parameter uebergabe der 2.Adresszeile)
        // hier wird die Rechnungs-und Lieferanschrift verglichen, wenn die Adressen gleich sind, wird kein "L" in der uebersicht gesetzt
        // soll generell ein "L" in der uebersicht gesetzt werden, muessen die $DATAStrings "Lieferanschrift=1&" sein

        if (($customer['firma'] == $customer['d_firma']) &&
                ($customer['vorname'] == $customer['d_vorname']) &&
                ($customer['nachname'] == $customer['d_nachname']) &&
                ($customer['strasse'] == $customer['d_strasse']) &&
                ($customer['strasse2'] == $customer['d_strasse2']) &&
                ($customer['plz'] == $customer['d_plz']) &&
                ($customer['ort'] == $customer['d_ort'])) {
            $DATAstring .= "Lieferanschrift=0&";
        } else {
            $DATAstring .= "Lieferanschrift=1&";
        }

        // ############# ARTIKELERKENNUNG SETZEN #############
        // modified FT
        $Artikelerkennung = '0';
        // 0 = Product ID (p_Model XT muss gleich Product ID Afterbuy sein)
        // 1 = Artikelnummer (p_Model XT muss gleich Arrikelnummer Afterbuy sein)
        // 2 = EAN (p_Model XT muss gleich EAN Afterbuy sein)
        // sollen keine Stammartikel erkannt werden, muss die Zeile: $DATAstring .= "Artikelerkennung=" . $Artikelerkennung ."&";  geloescht werden
        // sollen keine Stammartikel erkannt werden, muss die Zeile: $Artikelerkennung = '1';  geloescht werden
        // ############# PRODUCTS_DATA TEIL1 #############
        // modified FT
        $DATAstring .= "Artikelerkennung=" . $Artikelerkennung . "&";
        $nr = 0;
        $anzahl = 0;
        while ($pDATA = xtc_db_fetch_array($p_query)) {
            $nr++;

            $artnr = $pDATA['products_model'];
            if ($artnr == '')
                $artnr = $pDATA['products_id'];

            $a_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_id='" . $oID . "' AND orders_products_id='" . $pDATA['orders_products_id'] . "'");
            while ($aDATA = xtc_db_fetch_array($a_query)) {
                $attribute_model = xtc_get_attributes_model($pDATA['products_id'], $aDATA['products_options_values'], $aDATA['products_options']);
                if ((int) $attribute_model > 0)
                    $artnr = $attribute_model;
            }

            $DATAstring .= "Artikelnr_" . $nr . "=" . $artnr . "&";
            $DATAstring .= "ArtikelStammID_" . $nr . "=" . $artnr . "&";
            $DATAstring .= "Artikelname_" . $nr . "=" . rawurlencode($pDATA['products_name']) . "&";

            // ############# PREISueBERGABE BRUTTO/NETTO NACH KUNDENGRUPPE #############
            // Kundengruppen muessen jeweilige Zuordnung inkl/excl. Anzeige im Admin XT haben

            $price = $pDATA['products_price'];
            $tax_rate = $pDATA['products_tax'];
            if ($pDATA['allow_tax'] == 0) {
                $cQuery = xtc_db_query("SELECT customers_status_add_tax_ot FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id='" . $oData['customers_status'] . "' LIMIT 0,1");
                $cData = xtc_db_fetch_array($cQuery);
                if ($cData['customers_status_add_tax_ot'] == 0) {
                    $tax_rate = 0;
                } else {
                    $price+=$price / 100 * $tax_rate;
                }
            }
            //Waehrungspruefung
            $o_currency = $oData['currency'];
            $curreny_query = xtc_db_query("SELECT * FROM " . TABLE_CURRENCIES);
            while ($currency_array = xtc_db_fetch_array($curreny_query)) {
                if ($currency_array['code'] == $o_currency) {
                    $xt_currency = $currency_array['value'];
                }
            }
            $price = $price * $xt_currency;
            //Waehrungspruefung END
            $price = preg_replace("/\./", ",", $pDATA['products_price']); // Hetfield - 2009-08-19 - replaced depricated function ereg_replace with preg_replace to be ready for PHP >= 5.3
            $tax = preg_replace("/\./", ",", $pDATA['products_tax']); // Hetfield - 2009-08-19 - replaced depricated function ereg_replace with preg_replace to be ready for PHP >= 5.3
            // ############# PRODUCTS_DATA TEIL2 #############

            $DATAstring .= "ArtikelEPreis_" . $nr . "=" . $price . "&";
            $DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
            $DATAstring .= "ArtikelMenge_" . $nr . "=" . ereg_replace("\.", ",", $pDATA['products_quantity']) . "&";
            $url = HTTP_SERVER . DIR_WS_CATALOG . 'product_info.php?products_id=' . $pDATA['products_id'];
            $DATAstring .= "ArtikelLink_" . $nr . "=" . $url . "&";
            //Attributuebergabe
            $a_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_id='" . $oID . "' AND orders_products_id='" . $pDATA['orders_products_id'] . "'");
            $options = '';
            while ($aDATA = xtc_db_fetch_array($a_query)) {
                if ($options == '') {
                    $options = rawurlencode($aDATA['products_options']) . ":" . rawurlencode($aDATA['products_options_values']);
                } else {
                    $options .= "|" . rawurlencode($aDATA['products_options']) . ":" . rawurlencode($aDATA['products_options_values']);
                }
            }
            if ($options != "") {
                $DATAstring .= "Attribute_" . $nr . "=" . $options . "&";
            }
            $anzahl += (int) $pDATA['products_quantity'];
        }
        // ############# ORDER_TOTAL #############

        $order_total_query = xtc_db_query("SELECT
						                      class,
						                      value,
						                      sort_order
						                      FROM " . TABLE_ORDERS_TOTAL . "
						                      WHERE orders_id='" . $oID . "'
						                      ORDER BY sort_order ASC");

        $order_total = array();
        $zk = '';
        $cod_fee = '';
        $cod_flag = false;
        $discount_flag = false;
        $gv_flag = false;
        $coupon_flag = false;
        $gv = '';
        while ($order_total_values = xtc_db_fetch_array($order_total_query)) {

            $order_total[] = array('CLASS' => $order_total_values['class'], 'VALUE' => $order_total_values['value']);

            // ############# NACHNAHME/GUTSCHEINE/KUPONS/RABATTE #############
            if ($order_total_values['class'] == 'ot_shipping')
                $shipping = $order_total_values['value'];

            // Nachnamegebuehr
            if ($order_total_values['class'] == 'ot_cod_fee') {
                $cod_flag = true;
                $cod_fee = $order_total_values['value'];
            }
            // Rabatt
            if ($order_total_values['class'] == 'ot_discount') {
                $discount_flag = true;
                $discount = $order_total_values['value'];
            }
            // Gutschein
            if ($order_total_values['class'] == 'ot_gv') {
                $gv_flag = true;
                $gv = $order_total_values['value'];
            }
            // Kupon
            if ($order_total_values['class'] == 'ot_coupon') {
                $coupon_flag = true;
                $coupon = $order_total_values['value'];
            }
            // ot_payment
            if ($order_total_values['class'] == 'ot_payment') {
                $ot_payment_flag = true;
                $ot_payment = $order_total_values['value'];
            }
        }

        // ############# ueBERGABE NACHNAHME/GUTSCHEINE/KUPONS/RABATTE #############
        // Nachnamegebuehr uebergabe als Produkt
        if ($cod_flag) {
            $nr++;
            $DATAstring .= "Artikelnr_" . $nr . "=99999999&";
            $DATAstring .= "Artikelname_" . $nr . "=Nachnahme&";
            //uebergabe Brutto/Netto
            if ($pDATA['allow_tax'] == 0) {
                $cQuery = xtc_db_query("SELECT customers_status_show_price_tax FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id='" . $oData['customers_status'] . "' LIMIT 0,1");
                $cData = xtc_db_fetch_array($cQuery);
                if ($cData['customers_status_show_price_tax'] == 1) {
                    $tax_rate = 0;
                } else {
                    $cod_fee = ((($cod_fee / 100) * $tax_rate) + $cod_fee);
                }
            }
            //uebergabe Brutto/Netto END
            //Waehrungspruefung
            $o_currency = $oData['currency'];
            $curreny_query = xtc_db_query("SELECT * FROM " . TABLE_CURRENCIES);
            while ($currency_array = xtc_db_fetch_array($curreny_query)) {
                if ($currency_array['code'] == $o_currency) {
                    $xt_currency = $currency_array['value'];
                }
            }
            $cod_fee = $cod_fee * $xt_currency;
            //Waehrungspruefung END
            $cod_fee = ereg_replace("\.", ",", $cod_fee);
            $DATAstring .= "ArtikelEPreis_" . $nr . "=" . $cod_fee . "&";
            $DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
            $DATAstring .= "ArtikelMenge_" . $nr . "=1&";
            $p_count++;
        }
        // Rabatt uebergabe als Produkt
        if ($discount_flag) {
            $nr++;
            $DATAstring .= "Artikelnr_" . $nr . "=99999998&";
            $DATAstring .= "Artikelname_" . $nr . "=Rabatt&";
            //uebergabe Brutto/Netto
            if ($pDATA['allow_tax'] == 0) {
                $cQuery = xtc_db_query("SELECT customers_status_show_price_tax FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id='" . $oData['customers_status'] . "' LIMIT 0,1");
                $cData = xtc_db_fetch_array($cQuery);
                if ($cData['customers_status_show_price_tax'] == 1) {
                    $tax_rate = 0;
                } else {
                    $discount = ((($discount / 100) * $tax_rate) + $discount);
                }
            }
            //uebergabe Brutto/Netto END
            //Waehrungspruefung
            $o_currency = $oData['currency'];
            $curreny_query = xtc_db_query("SELECT * FROM " . TABLE_CURRENCIES);
            while ($currency_array = xtc_db_fetch_array($curreny_query)) {
                if ($currency_array['code'] == $o_currency) {
                    $xt_currency = $currency_array['value'];
                }
            }
            $discount = $discount * $xt_currency;
            //Waehrungspruefung END
            $discount = ereg_replace("\.", ",", $discount);
            $DATAstring .= "ArtikelEPreis_" . $nr . "=" . $discount . "&";
            $DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
            $DATAstring .= "ArtikelMenge_" . $nr . "=1&";
            $p_count++;
        }
        // Gutschein uebergabe als Produkt
        if ($gv_flag) {
            $nr++;
            $DATAstring .= "Artikelnr_" . $nr . "=99999997&";
            $DATAstring .= "Artikelname_" . $nr . "=Gutschein&";
            //uebergabe Brutto/Netto
            if ($pDATA['allow_tax'] == 0) {
                $cQuery = xtc_db_query("SELECT customers_status_show_price_tax FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id='" . $oData['customers_status'] . "' LIMIT 0,1");
                $cData = xtc_db_fetch_array($cQuery);
                if ($cData['customers_status_show_price_tax'] == 1) {
                    $tax_rate = 0;
                } else {
                    $gv = ((($gv / 100) * $tax_rate) + $gv);
                }
            }
            //uebergabe Brutto/Netto END
            //Waehrungspruefung
            $o_currency = $oData['currency'];
            $curreny_query = xtc_db_query("SELECT * FROM " . TABLE_CURRENCIES);
            while ($currency_array = xtc_db_fetch_array($curreny_query)) {
                if ($currency_array['code'] == $o_currency) {
                    $xt_currency = $currency_array['value'];
                }
            }
            $gv = $gv * $xt_currency;
            //Waehrungspruefung END
            $gv = ereg_replace("\.", ",", ($gv * (-1)));
            $DATAstring .= "ArtikelEPreis_" . $nr . "=" . $gv . "&";
            $DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
            $DATAstring .= "ArtikelMenge_" . $nr . "=1&";
            $p_count++;
        }
        // Kupon uebergabe als Produkt
        if ($coupon_flag) {
            $nr++;
            $DATAstring .= "Artikelnr_" . $nr . "=99999996&";
            $DATAstring .= "Artikelname_" . $nr . "=Kupon&";
            //uebergabe Brutto/Netto
            if ($pDATA['allow_tax'] == 0) {
                $cQuery = xtc_db_query("SELECT customers_status_show_price_tax FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id='" . $oData['customers_status'] . "' LIMIT 0,1");
                $cData = xtc_db_fetch_array($cQuery);
                if ($cData['customers_status_show_price_tax'] == 1) {
                    $tax_rate = 0;
                } else {
                    $coupon = ((($coupon / 100) * $tax_rate) + $coupon);
                }
            }
            //uebergabe Brutto/Netto END
            //Waehrungspruefung
            $o_currency = $oData['currency'];
            $curreny_query = xtc_db_query("SELECT * FROM " . TABLE_CURRENCIES);
            while ($currency_array = xtc_db_fetch_array($curreny_query)) {
                if ($currency_array['code'] == $o_currency) {
                    $xt_currency = $currency_array['value'];
                }
            }
            $coupon = $coupon * $xt_currency;
            //Waehrungspruefung END
            $coupon = ereg_replace("\.", ",", ($coupon * (-1)));
            $DATAstring .= "ArtikelEPreis_" . $nr . "=" . $coupon . "&";
            $DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
            $DATAstring .= "ArtikelMenge_" . $nr . "=1&";
            $p_count++;
        }
        //ot_payment uebergabe als Produkt
        if ($ot_payment_flag) {
            $nr++;
            $DATAstring .= "Artikelnr_" . $nr . "=99999995&";
            $DATAstring .= "Artikelname_" . $nr . "=Zahlartenrabatt&";
            //uebergabe Brutto/Netto
            if ($pDATA['allow_tax'] == 0) {
                $cQuery = xtc_db_query("SELECT customers_status_show_price_tax FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id='" . $oData['customers_status'] . "' LIMIT 0,1");
                $cData = xtc_db_fetch_array($cQuery);
                if ($cData['customers_status_show_price_tax'] == 1) {
                    $tax_rate = 0;
                } else {
                    $ot_payment = ((($ot_payment / 100) * $tax_rate) + $ot_payment);
                }
            }
            //uebergabe Brutto/Netto END
            //Waehrungspruefung
            $o_currency = $oData['currency'];
            $curreny_query = xtc_db_query("SELECT * FROM " . TABLE_CURRENCIES);
            while ($currency_array = xtc_db_fetch_array($curreny_query)) {
                if ($currency_array['code'] == $o_currency) {
                    $xt_currency = $currency_array['value'];
                }
            }
            $ot_payment = $ot_payment * $xt_currency;
            //Waehrungspruefung END
            $ot_payment = ereg_replace("\.", ",", $ot_payment);
            $DATAstring .= "ArtikelEPreis_" . $nr . "=" . ereg_replace("\.", ",", $ot_payment) . "&";
            $DATAstring .= "ArtikelMwst_" . $nr . "=" . $tax . "&";
            $DATAstring .= "ArtikelMenge_" . $nr . "=1&";
            $p_count++;
        }

        $DATAstring .= "PosAnz=" . $p_count . "&";

        // ############# ueBERGABE BRUTTO/NETTO VERSAND #############
        // mofified FT Kundengruppen muessen jeweilige Zuordnung inkl/excl. Anzeige im Admin XT haben
        $cod_vK = ereg_replace("\.", ",", $cod_fee);
        if ($order_total_values['class'] == 'ot_shipping')
            $shipping = $order_total_values['value'];
        if ($pDATA['allow_tax'] == 0) {
            $cQuery = xtc_db_query("SELECT customers_status_show_price_tax FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id='" . $oData['customers_status'] . "' LIMIT 0,1");
            $cData = xtc_db_fetch_array($cQuery);
            if ($cData['customers_status_show_price_tax'] == 1) {
                $tax_rate = 0;
            } else {
                $shipping = ((($shipping / 100) * $tax_rate) + $shipping);
            }
        }
        //Waehrungspruefung
        $o_currency = $oData['currency'];
        $curreny_query = xtc_db_query("SELECT * FROM " . TABLE_CURRENCIES);
        while ($currency_array = xtc_db_fetch_array($curreny_query)) {
            if ($currency_array['code'] == $o_currency) {
                $xt_currency = $currency_array['value'];
            }
        }
        $shipping = $shipping * $xt_currency;
        //Waehrungspruefung END
        $vK = ereg_replace("\.", ",", $shipping);
        $DATAstring .= "Versandkosten=" . $vK . "&";

        $s_method = explode('(', $oData['shipping_method']);
        $s_method = str_replace(' ', '%20', $s_method[0]);
        $DATAstring .= "kommentar=" . rawurlencode($oData['comments']) . "&";
        $DATAstring .= "Versandart=" . $s_method . "&";
        $DATAstring .= "NoVersandCalc=1&";
        // 1 = Versand aus XT
        // 0 = Versandermittlung durch Afterbuy (nur wennStammartikel erkannt wird!)
        // ############# ZAHLARTEN  "eingedeutscht!" #############
        //uebergabe Eustandardtransfer
        if ($oData['payment_method'] == 'eustandardtransfer') {
            $DATAstring .= "Zahlart=ueberweisung%20/%20Vorkasse" . "&";
        }
        //uebergabe Cash
        elseif ($oData['payment_method'] == 'cash') {
            $DATAstring .= "Zahlart=Barzahlung" . "&";
        }
        //uebergabe Cod
        elseif ($oData['payment_method'] == 'cod') {
            $DATAstring .= "Zahlart=Nachnahme" . "&";
        }
        //uebergabe Kreditkarte
        elseif ($oData['payment_method'] == 'cc') {
            $DATAstring .= "Zahlart=Kreditkarte" . "&";
        }
        //uebergabe Paypal
        elseif ($oData['payment_method'] == 'paypal') {
            $DATAstring .= "Zahlart=Paypal" . "&";
        }
        //uebergabe Paypal IPN
        elseif ($oData['payment_method'] == 'paypa_ipn') {
            $DATAstring .= "Zahlart=Paypal" . "&";
        }
        //uebergabe Rechnung
        elseif ($oData['payment_method'] == 'invoice') {
            $DATAstring .= "Zahlart=Rechnung" . "&";
        }
        //uebergabe Moneyorder
        elseif ($oData['payment_method'] == 'moneyorder') {
            $DATAstring .= "Zahlart=Vorkasse/ueberweisung" . "&";
        }
        //uebergabe ipayment
        elseif ($oData['payment_method'] == 'ipayment') {
            $DATAstring .= "Zahlart=IPayment" . "&";
            $DATAstring .= "SetPay=1&";
        }

        //uebergabe Bankdaten
        elseif ($oData['payment_method'] == 'banktransfer') {

            $DATAstring .= "Zahlart=Bankeinzug" . "&";

            if ($_GET['oID']) {
                $b_query = xtc_db_query("SELECT * FROM banktransfer WHERE orders_id='" . $_GET['oID'] . "'");
                $b_data = xtc_db_fetch_array($b_query);
                $DATAstring .= "Bankname=" . rawurlencode($b_data['banktransfer_bankname']) . "&";
                $DATAstring .= "BLZ=" . $b_data['banktransfer_blz'] . "&";
                $DATAstring .= "Kontonummer=" . $b_data['banktransfer_number'] . "&";
                $DATAstring .= "Kontoinhaber=" . rawurlencode($b_data['banktransfer_owner']) . "&";
            } else {
                $DATAstring .= "Bankname=" . rawurlencode($_POST['banktransfer_bankname']) . "&";
                $DATAstring .= "BLZ=" . $_POST['banktransfer_blz'] . "&";
                $DATAstring .= "Kontonummer=" . $_POST['banktransfer_number'] . "&";
                $DATAstring .= "Kontoinhaber=" . rawurlencode($_POST['banktransfer_owner']) . "&";
            }
        }
        //sonst uebergabe wie XT
        else {
            $DATAstring .= "Zahlart=" . $oData['payment_method'] . "&";
        }

        // ############# FEEDBACKDATUM SETZEN #############
        // Modifiziert FT
        $feedbackdatum = '0';
        $DATAstring .= "NoFeedback=" . $feedbackdatum . "&";
        //0= Feedbackdatum setzen und KEINE automatische Erstkontaktmail versenden
        //1= KEIN Feedbackdatum setzen, aber automatische Erstkontaktmail versenden (Achtung: Kunde muesste Feedback durchlaufen wenn die Erstkontakt nicht angepasst wird!)
        //2= Feedbackdatum setzen und automatische Erstkontaktmail versenden (Achtung: Erstkontaktmail muss mit Variablen angepasst werden!)
        // #############  CHECK  #############
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $DATAstring);
        $result = curl_exec($ch);
        if (preg_match("/<success>1<\/success>/", $result)) {// Hetfield - 2009-08-19 - replaced depricated function ereg with preg_match to be ready for PHP >= 5.3
            // result ok, mark order
            // extract ID from result
            $cdr = explode('<KundenNr>', $result);
            $cdr = explode('</KundenNr>', $cdr[1]);
            $cdr = $cdr[0];
            xtc_db_query("update " . TABLE_ORDERS . " set afterbuy_success='1',afterbuy_id='" . $cdr . "' where orders_id='" . $oID . "'");
            //wenn Kundenkommentar
            if ($oData['comments'] != '') {
                $mail_content .= "Name: " . $oData['billing_firstname'] . " " . $oData['billing_lastname'] . "\nEmailadresse: " . $oData['customers_email_address'] . "\nKundenkommentar: " . $oData['comments'] . "\nBestellnummer: " . $oID . chr(13) . chr(10) . "\n";
                mail(EMAIL_BILLING_ADDRESS, "Kundenkommentar bei Bestellung", $mail_content);
            }
            //set new order status
            if ($order_status != '') {
                xtc_db_query("update " . TABLE_ORDERS . " set orders_status='" . $order_status . "' where orders_id='" . $oID . "'");
            }
        } else {

            // mail to shopowner
            $mail_content = 'Fehler bei &Uuml;bertragung der Bestellung: ' . $oID . chr(13) . chr(10) . 'Folgende Fehlermeldung wurde vom afterbuy.de zur&uuml;ckgegeben:' . chr(13) . chr(10) . $result;
            mail(EMAIL_BILLING_ADDRESS, "Afterbuy-Fehl&uuml;bertragung", $mail_content);
        }
        // close session
        curl_close($ch);
    }

    // Funktion zum ueberpruefen ob Bestellung bereits an Afterbuy gesendet.
    function order_send() {
        $check_query = xtc_db_query("SELECT afterbuy_success FROM " . TABLE_ORDERS . " WHERE orders_id='" . $this->order_id . "'");
        $data = xtc_db_fetch_array($check_query);

        if ($data['afterbuy_success'] == 1)
            return false;
        return true;
    }

}
