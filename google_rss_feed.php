<?php

/* -----------------------------------------------------------------
 * 	ID:						google_rss_feed.php
 * 	Letzter Stand:			v2.3
 * 	zuletzt geaendert von:	cseoak
 * 	Datum:					2012/11/19
 *
 * 	Copyright (c) since 2010 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

include ('includes/configure.php');
include ('includes/application_top.php');

$_SESSION['language'] = 'german';
$_SESSION['languages_id'] = '2';

$Title = "Produkt Feed von " . HTTP_SERVER; //Titel fĂĽr den Feed hier eingeben
$Description = "Alle Produkte von " . HTTP_SERVER; //Beschreibung des Feeds
$copyright = HTTP_SERVER; //copyright inhaber

$SiteLink = HTTP_SERVER . DIR_WS_CATALOG;

if (GROUP_CHECK == 'true') {
    $group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
}
if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
    $fsk_lock = ' AND p.products_fsk18!=1';
}
$listing_query = xtDBquery("SELECT
			p.*,
			pd.products_name,
            pd.products_description,
            pd.products_short_description,
            pd.products_google_taxonomie,
            pd.products_taxonomie
			FROM 
				products p 
			INNER JOIN 
				products_description pd ON (p.products_id = pd.products_id) 
			WHERE 
				p.products_status = '1'
			AND 
				p.products_rel = '1'
			AND 
				p.products_buyable = '1'
			AND 
				p.products_only_request = '0'
			AND 
				pd.language_id = '2'
			" . $group_check . $fsk_lock . "
			");


require_once (DIR_WS_CLASSES . 'class.order.php');
$order = new order;

$default_data = xtc_db_fetch_array(xtDBquery(" SELECT ab.entry_postcode,z.zone_name,z.zone_id,ab.entry_country_id,c.countries_id,c.countries_name,c.countries_iso_code_2,c.countries_iso_code_3,c.address_format_id
												  FROM address_book ab, zones z, countries c 
												  WHERE ab.address_book_id = '1'
												  AND z.zone_id = ab.entry_zone_id
												  AND c.countries_id = ab.entry_country_id"));

$order->customer = array('postcode' => $default_data['entry_postcode'],
						'state' => $default_data['zone_name'],
						'zone_id' => $default_data['zone_id'],
						'country' => Array('id' => $default_data['countries_id'],
						'title' => $default_data['countries_name'],
						'iso_code_2' => $default_data['countries_iso_code_2'],
						'iso_code_3' => $default_data['countries_iso_code_3']),
						'format_id' => $default_data['address_format_id']);

$order->delivery = array('postcode' => $default_data['entry_postcode'],
						'state' => $default_data['zone_name'],
						'zone_id' => $default_data['zone_id'],
						'country' => Array('id' => $default_data['countries_id'],
						'title' => $default_data['countries_name'],
						'iso_code_2' => $default_data['countries_iso_code_2'],
						'iso_code_3' => $default_data['countries_iso_code_3']),
						'format_id' => $default_data['address_format_id']);

$_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];


require (DIR_WS_CLASSES . 'class.shipping.php');
$shipping = new shipping;
require_once (DIR_FS_INC . 'xtc_get_products_mo_images.inc.php');
require_once (DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
require_once (DIR_FS_INC . 'xtc_get_vpe_name.inc.php');
require_once (DIR_WS_CLASSES . 'class.xtcprice.php');
$xtPrice = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);

header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n";
echo "<rss version=\"2.0\" xmlns:g=\"http://base.google.com/ns/1.0\" xmlns:c=\"http://base.google.com/cns/1.0\">\n\n";
echo "<channel>\n";
echo "\t<title>$Title</title>\n";
echo "\t<link>$SiteLink</link>\n";

while ($listing = xtc_db_fetch_array($listing_query, true)) {
    $_SESSION['cart']->remove_all();
    $_SESSION['cart']->add_cart($listing['products_id'], 1, '', false);
    $total_weight = $_SESSION['cart']->show_weight();
    $total_count = $_SESSION['cart']->count_contents();
    $quotes = $shipping->quote();
    $link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($listing['products_id'], $listing['products_name']), 'NONSSL', false);
    $price = $xtPrice->xtcGetPrice($listing['products_id'], $format = false, 1, $listing['products_tax_class_id'], $listing['products_price']);
    $price = str_replace('0,00', '0,01', $price); // Google akzeptiert keine Preise wie 0,00	
    $price = $xtPrice->xtcFormat($price, true);
    $products_name = $listing['products_name'];
    $products_name = str_replace("&", "&amp;", $products_name);
    $products_name = str_replace("\n", " ", $products_name);

    if ($listing['products_description'] != '')
        $beschreibung = $listing['products_description'];
    elseif ($listing['products_short_description'] != '')
        $beschreibung = $listing['products_short_description'];
    else
        $beschreibung = $products_name;

    if ($listing['manufacturers_id'] > '0')
        $marke = xtc_db_fetch_array(xtDBquery("SELECT manufacturers_name FROM " . TABLE_MANUFACTURERS . " WHERE manufacturers_id = '" . $listing['manufacturers_id'] . "'"));

    // Start Ausgabe
    echo "\n\n\t<item>\n";

    // Produktname
    echo "\t\t<title>" . $products_name . "</title>\n";
    // Link
    echo "\t\t<link>" . $link . "</link>\n";

    // Beschreibung
    echo "\t\t<description><![CDATA[" . $beschreibung . "]]></description>\n";

    // Produkt ID
    echo "\t\t<g:id>" . $listing['products_id'] . "</g:id>\n";

    // Produktkennzeichnungen 
    echo "\t\t<g:identifier_exists>" . $listing['products_g_identifier'] . "</g:identifier_exists>\n";

    // EAN Nummer
    if (!empty($listing['products_ean'])) {
        echo "\t\t<g:gtin>" . $listing['products_ean'] . "</g:gtin>\n";
	} elseif (!empty($listing['products_isbn'])){
        echo "\t\t<g:gtin>" . $listing['products_isbn'] . "</g:gtin>\n";
	} elseif (!empty($listing['products_upc'])){
        echo "\t\t<g:gtin>" . $listing['products_upc'] . "</g:gtin>\n";
	}
    // Gender
    if ($listing['products_google_gender'] != '---' ) {
        echo "\t\t<g:gender>" . $listing['products_google_gender'] . "</g:gender>\n";
	}
    // Agegroup
    if ($listing['products_google_age_group'] != '---' ) {
        echo "\t\t<g:age_group>" . $listing['products_google_age_group'] . "</g:age_group>\n";
	}
    // Color
    if ($listing['products_google_color'] != '---' ) {
        echo "\t\t<g:color>" . $listing['products_google_color'] . "</g:color>\n";
	}
    // Color
    if ($listing['products_google_size'] != '---' ) {
        echo "\t\t<g:size>" . $listing['products_google_size'] . "</g:size>\n";
	}
    // VPE
    // if ($listing['products_vpe_status'] == 1 && $listing['products_vpe_value'] != 0.0) {
        // echo "\t\t<g:unit_pricing_measure>" . $listing['products_vpe_value'] . "</g:unit_pricing_measure>\n";
        // echo "\t\t<g:unit_pricing_measure>" . $xtPrice->xtcFormat($price * (1 / $listing['products_vpe_value']), true).TXT_PER.xtc_get_vpe_name($listing['products_vpe']) . "</g:unit_pricing_measure>\n";
	// }
	
	

    //Kategorie Taxonomie ermitteln
    $cat_query = xtc_db_fetch_array(xtc_db_query("SELECT ptc.categories_id, cd.categories_google_taxonomie FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " AS ptc LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " AS cd ON(ptc.categories_id = cd.categories_id AND language_id = '" . $_SESSION['languages_id'] . "') WHERE products_id = '" . $listing['products_id'] . "' LIMIT 1"));
    // Google Taxonomie ab 22.09.2011

    if (!empty($listing['products_google_taxonomie'])) {
        $producttype = htmlspecialchars($listing['products_google_taxonomie']);
        echo "\t\t<g:google_product_category>" . $producttype . "</g:google_product_category>\n";
    } elseif (!empty($cat_query['categories_google_taxonomie'])) {
        $producttype = htmlspecialchars($cat_query['categories_google_taxonomie']);
        echo "\t\t<g:google_product_category>" . $producttype . "</g:google_product_category>\n";
    } elseif (PRODUCT_GOOGLE_STANDARD_TAXONOMIE != '') {
        $producttype = htmlspecialchars(PRODUCT_GOOGLE_STANDARD_TAXONOMIE);
        echo "\t\t<g:google_product_category>" . $producttype . "</g:google_product_category>\n";
    }


    // Produkttyp - Ihre Artikelkategorie ab 22.09.2011
    if (!empty($listing['products_taxonomie'])) {
        $producttype = htmlspecialchars($listing['products_taxonomie']);
        $producttype = str_replace('&reg;', '&gt;', $producttype);
        echo "\t\t<g:product_type>" . $producttype . "</g:product_type>\n";
    } else {
        $cat_query = xtc_db_fetch_array(xtc_db_query("SELECT ptc.categories_id, cd.categories_name FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " AS ptc LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " AS cd ON(ptc.categories_id = cd.categories_id AND language_id = '" . $_SESSION['languages_id'] . "') WHERE products_id = '" . $listing['products_id'] . "' LIMIT 1"));
        $producttype = htmlspecialchars($cat_query['categories_name']);
        $producttype = str_replace('&reg;', '&gt;', $producttype);        
		echo "\t\t<g:product_type>" . $producttype . "</g:product_type>\n";
    }

    // Marke / Hersteller
    if ($listing['manufacturers_id'] > '0' && $listing['products_brand_name'] == '') {
        echo "\t\t<g:brand>" . urlencode($marke['manufacturers_name']) . "</g:brand>\n";
	} elseif ($listing['products_brand_name'] != '') {
		echo "\t\t<g:brand>" . urlencode($listing['products_brand_name']) . "</g:brand>\n";
	}

    // Produktbilder, jedoch nicht mehr als insgesamt 10
    if (!empty($listing['products_image']))
        echo "\t\t<g:image_link>" . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_INFO_IMAGES . $listing['products_image'] . "</g:image_link>\n";
    else
        echo "\t\t<g:image_link>" . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_INFO_IMAGES . 'no_img_big.jpg' . "</g:image_link>\n";

    $images = xtc_get_products_mo_images($listing['products_id']);
    if ($images) {

        foreach ($images as $image) {
            $b++;
            echo "\t\t<g:additional_image_link>" . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_INFO_IMAGES . $image['image_name'] . "</g:additional_image_link>\n";
            if ($b == 9)
                break;
        }
    }
    // Alternativ zur MPN die Modelnummer
    if (!empty($listing['products_manufacturers_model']))
        echo "\t\t<g:mpn>" . $listing['products_manufacturers_model'] . "</g:mpn>\n";
    elseif (!empty($listing['products_model']))
        echo "\t\t<g:mpn>" . $listing['products_model'] . "</g:mpn>\n";
    else
        echo "\t\t<g:mpn>" . $listing['products_id'] . "</g:mpn>\n";

    // Fertiger Produktpreis
    echo "\t\t<g:price>" . $price . "</g:price>\n";


    // NEU Menge / VerfĂĽgbarkeit
    if ($listing['products_quantity'] > 0)
        echo "\t\t<g:availability>in stock</g:availability>\n";
    else
        echo "\t\t<g:availability>available for order</g:availability>\n";


    // Zustand
    echo "\t\t<g:condition>" . $listing['products_zustand'] . "</g:condition>\n";

    // Gewicht
    if (!empty($listing['products_weight']))
        echo "\t\t<g:shipping_weight>" . $listing['products_weight'] . " kg</g:shipping_weight>\n";

    // Versandkosten
    $i = 1;
    foreach ($quotes AS $quote) {
        echo "\t\t<g:shipping>\n";
        echo "\t\t\t<g:country>DE</g:country>\n";
        echo "\t\t\t<g:region></g:region>\n";
        echo "\t\t\t<g:service>" . $quote['module'] . "</g:service>\n";
        echo "\t\t\t<g:price>" . ($quote['tax'] > 0 ? round(($quote['methods'][0]['cost'] * ( 100 + $quote['tax'] ) / 100), 2) : $quote['methods'][0]['cost']) . "</g:price>\n";

        echo "\t\t</g:shipping>\n";
        if ($i == 10)
            break;
        $i++;
    }
    echo "\t</item>";
}

echo "</channel>\n";
echo "</rss>";
$_SESSION['cart']->reset(true);
unset($_SESSION['cart']);
