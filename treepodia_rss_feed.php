<?php

/* -----------------------------------------------------------------
 * 	Id$: $
 * 	Copyright (c) since 2010-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

// include ('includes/configure.php');
include ('includes/application_top.php');
if (TREEPODIAACTIVE == 'true' && TREEPODIAID != '') {
    $_SESSION['language'] = 'german';
    $_SESSION['languages_id'] = '2';

    // $SiteLink = HTTP_SERVER .DIR_WS_CATALOG ;

    if (GROUP_CHECK == 'true') {
        $group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
    }
    if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
        $fsk_lock = ' AND p.products_fsk18!=1';
    }
    $listing_query = xtc_db_query("SELECT
				p.*,
				pd.*
				FROM (products p INNER JOIN products_description pd ON p.products_id = pd.products_id) 
				WHERE p.products_status = '1'
				AND p.products_treepodia_activate = '1'
				AND pd.language_id = '2'
				" . $group_check . $fsk_lock . "
				");

    require_once (DIR_FS_INC . 'xtc_get_products_mo_images.inc.php');
    require_once (DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
    require_once (DIR_WS_CLASSES . 'xtcPrice.php');
    $xtPrice = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);

    header("Content-Type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n";
    echo "<products>\n";

    while ($listing = xtc_db_fetch_array($listing_query, true)) {

        $link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($listing['products_id'], $listing['products_name']), 'NONSSL', false);
        $price = $xtPrice->xtcGetPrice($listing['products_id'], $format = false, 1, $listing['products_tax_class_id'], $listing['products_price']);
        $price = str_replace('0,00', '0,01', $price); // Google akzeptiert keine Preise wie 0,00	
        $price = $xtPrice->xtcFormat($price, true);
        $products_name = $listing['products_name'];
        if ($products_name == '') {
            $products_name = 'Kein Name';
        }
        $products_name = str_replace("&", "&amp;", $products_name);
        $products_name = str_replace("\n", " ", $products_name);

        if ($listing['products_short_description'] != '')
            $beschreibung = $listing['products_short_description'];
        elseif ($listing['products_description'] != '')
            $beschreibung = $listing['products_description'];
        else
            $beschreibung = $products_name;

        if ($listing['products_treepodia_catch_phrase_1'] != '') {
            $products_treepodia_catch_phrase_1 = $listing['products_treepodia_catch_phrase_1'];
        } elseif (TREEPODI_GLOBAL_CATCH_1 != '') {
            $products_treepodia_catch_phrase_1 = TREEPODI_GLOBAL_CATCH_1;
        }
        if ($listing['products_treepodia_catch_phrase_2'] != '') {
            $products_treepodia_catch_phrase_2 = $listing['products_treepodia_catch_phrase_2'];
        } elseif (TREEPODI_GLOBAL_CATCH_2 != '') {
            $products_treepodia_catch_phrase_2 = TREEPODI_GLOBAL_CATCH_2;
        }
        if ($listing['products_treepodia_catch_phrase_3'] != '') {
            $products_treepodia_catch_phrase_3 = $listing['products_treepodia_catch_phrase_3'];
        } elseif (TREEPODI_GLOBAL_CATCH_3 != '') {
            $products_treepodia_catch_phrase_3 = TREEPODI_GLOBAL_CATCH_3;
        }
        if ($listing['products_treepodia_catch_phrase_4'] != '') {
            $products_treepodia_catch_phrase_4 = $listing['products_treepodia_catch_phrase_4'];
        } elseif (TREEPODI_GLOBAL_CATCH_4 != '') {
            $products_treepodia_catch_phrase_4 = TREEPODI_GLOBAL_CATCH_4;
        }




        if ($listing['manufacturers_id'] > '0')
            $marke = xtc_db_fetch_array(xtDBquery("SELECT manufacturers_name FROM " . TABLE_MANUFACTURERS . " WHERE manufacturers_id = '" . $listing['manufacturers_id'] . "'"));

        // Start Ausgabe
        echo "\n\n\t<product>\n";

        // Alternativ zur MPN die Modelnummer
        if (!empty($listing['products_manufacturers_model']))
            echo "\t\t<sku>" . $listing['products_manufacturers_model'] . "</sku>\n";
        elseif (!empty($listing['products_model']))
            echo "\t\t<sku>" . $listing['products_model'] . "</sku>\n";
        else
            echo "\t\t<sku>" . $listing['products_id'] . "</sku>\n";


        // Produktname
        echo "\t\t<name>" . $products_name . "</name>\n";

        // Produktpreis
        echo "\t\t<price>" . $price . "</price>\n";

        // Thumbnail
        if (!empty($listing['products_image']))
            echo "\t\t<thumbnail>" . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_THUMBNAIL_IMAGES . $listing['products_image'] . "</thumbnail>\n";
        else
            echo "\t\t<thumbnail>" . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_THUMBNAIL_IMAGES . 'no_img.jpg' . "</thumbnail>\n";

        // Link
        echo "\t\t<page-url>" . $link . "</page-url>\n";


        // Kategorie 
        $cat_query = xtc_db_fetch_array(xtc_db_query("SELECT ptc.categories_id, cd.categories_name FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " AS ptc LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " AS cd ON(ptc.categories_id = cd.categories_id AND language_id = '" . $_SESSION['languages_id'] . "') WHERE products_id = '" . $listing['products_id'] . "' LIMIT 1"));
        $cat_name = str_replace("&", "&amp;", $cat_query['categories_name']);
        echo "\t\t<commodity>" . $cat_name . "</commodity>\n";

        // Beschreibung
        echo "\t\t<description><![CDATA[" . $beschreibung . "]]></description>\n";


        // Marke / Hersteller
        $marke = str_replace("&", "&amp;", $marke['manufacturers_name']);
        if ($listing['manufacturers_id'] > '0')
            echo "\t\t<brand-name>" . $marke . "</brand-name>\n";

        // Produktbilder, jedoch nicht mehr als insgesamt 10
        if (!empty($listing['products_image']))
            echo "\t\t<image-url>" . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_INFO_IMAGES . $listing['products_image'] . "</image-url>\n";
        else
            echo "\t\t<image-url>" . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_INFO_IMAGES . 'no_img_big.jpg' . "</image-url>\n";

        $images = xtc_get_products_mo_images($listing['products_id']);
        if ($images) {

            foreach ($images as $image) {
                $b++;
                echo "\t\t<image-url>" . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_INFO_IMAGES . $image['image_name'] . "</image-url>\n";
                if ($b == 9)
                    break;
            }
        }

        //CatchPhrase
        echo "\t\t<catch-phrase>" . $products_treepodia_catch_phrase_1 . "</catch-phrase>\n";
        echo "\t\t<catch-phrase>" . $products_treepodia_catch_phrase_2 . "</catch-phrase>\n";
        echo "\t\t<catch-phrase>" . $products_treepodia_catch_phrase_3 . "</catch-phrase>\n";
        echo "\t\t<catch-phrase>" . $products_treepodia_catch_phrase_4 . "</catch-phrase>\n";

        if ($listing['products_quantity'] > 0)
            echo "\t\t<product-warranty>" . $listing['products_quantity'] . "</product-warranty>\n";

        echo "\t</product>";
    }

    echo "\n</products>\n";
}
