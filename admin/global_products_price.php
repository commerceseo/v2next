<?php
/* -----------------------------------------------------------------
 * 	$Id: global_products_price.php 851 2014-02-14 18:00:31Z akausch $
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


require('includes/application_top.php');

require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');

switch ($_GET['action']) {
    case 'update_all':
        $percent = round($_POST['all_products_price_percent'], 2);
        // alten price auslesen
        if ($_POST['customers_group'] == '-') {
            $price_old = "SELECT products_id, products_price AS PRODUCTS_PRICE FROM " . TABLE_PRODUCTS;
            $price_old_query = xtc_db_query($price_old);

            // wenn kundengruppe gewaehlt ist	
        } elseif ($_POST['customers_group'] >= 0) {
            $price_old = "SELECT p.products_id, p.personal_offer AS PRODUCTS_PRICE FROM " . TABLE_PERSONAL_OFFERS_BY . $_POST['customers_group'] . " p";
            $price_old_query = xtc_db_query($price_old);
        }
        // $_POST['price_sign']: + oder -
        while ($price_old = xtc_db_fetch_array($price_old_query)) {
            // pruefen ob preis bei kundengruppen 0.00 ist
            if ($price_old['PRODUCTS_PRICE'] != '0.000') {
                // vorzeichen +
                if ($_POST['all_price_sign'] == '1') {
                    // wert oder prozent rechnen
                    if ($_POST['all_price_percent'] == '1') {
                        $percent_wert = $price_old['PRODUCTS_PRICE'] / 100;
                        $price_differenz = $percent_wert * $percent;
                        $new_price = $price_old['PRODUCTS_PRICE'] + $price_differenz;
                    } elseif ($_POST['all_price_percent'] == '2') {
                        $new_price = $price_old['PRODUCTS_PRICE'] + $percent;
                    }
                    // vorzeichen -
                } elseif ($_POST['all_price_sign'] == '2') {
                    // wert oder prozent rechnen
                    if ($_POST['all_price_percent'] == '1') {
                        $percent_wert = $price_old['PRODUCTS_PRICE'] / 100;
                        $price_differenz = $percent_wert * $percent;
                        $new_price = $price_old['PRODUCTS_PRICE'] - $price_differenz;
                    } elseif ($_POST['all_price_percent'] == '2') {
                        $new_price = $price_old['PRODUCTS_PRICE'] - $percent;
                    }
                }
                // wenn allgemein, aktualisiere die prod. tabelle
                if ($_POST['customers_group'] == '-') {
                    xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
							products_price 			= '" . $new_price . "' 
							WHERE products_id 	= '" . $price_old['products_id'] . "'");
                } elseif ($_POST['customers_group'] >= 0) {
                    xtc_db_query("UPDATE " . TABLE_PERSONAL_OFFERS_BY . $_POST['customers_group'] . " SET 
							personal_offer 			= '" . $new_price . "' 
							WHERE products_id 	= '" . $price_old['products_id'] . "'");
                }
            } // IF price = 0.00
        } // WHILE
        xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_PRICE));
        break;
    case 'update_all_manufacturer':
        $percent = round($_POST['all_products_price_percent'], 2);
        // alten price auslesen
        if ($_POST['manufacturers_group'] >= 0) {
            $price_old = "SELECT products_id, products_price AS PRODUCTS_PRICE FROM " . TABLE_PRODUCTS . " WHERE manufacturers_id = '" . $_POST['manufacturers_group'] . "'";
            $price_old_query = xtc_db_query($price_old);
        }
        // $_POST['price_sign']: + oder -
        while ($price_old = xtc_db_fetch_array($price_old_query)) {
            // pruefen ob preis bei kundengruppen 0.00 ist
            if ($price_old['PRODUCTS_PRICE'] != '0.000') {
                // vorzeichen +
                if ($_POST['all_price_sign'] == '1') {
                    // wert oder prozent rechnen
                    if ($_POST['all_price_percent'] == '1') {
                        $percent_wert = $price_old['PRODUCTS_PRICE'] / 100;
                        $price_differenz = $percent_wert * $percent;
                        $new_price = $price_old['PRODUCTS_PRICE'] + $price_differenz;
                    } elseif ($_POST['all_price_percent'] == '2') {
                        $new_price = $price_old['PRODUCTS_PRICE'] + $percent;
                    }
                    // vorzeichen -
                } elseif ($_POST['all_price_sign'] == '2') {
                    // wert oder prozent rechnen
                    if ($_POST['all_price_percent'] == '1') {
                        $percent_wert = $price_old['PRODUCTS_PRICE'] / 100;
                        $price_differenz = $percent_wert * $percent;
                        $new_price = $price_old['PRODUCTS_PRICE'] - $price_differenz;
                    } elseif ($_POST['all_price_percent'] == '2') {
                        $new_price = $price_old['PRODUCTS_PRICE'] - $percent;
                    }
                }
                // wenn allgemein, aktualisiere die prod. tabelle
                if ($_POST['manufacturers_group'] >= 0) {
                    xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
							products_price = '" . $new_price . "' 
							WHERE products_id = '" . $price_old['products_id'] . "'");
                }
            } // IF price = 0.00
        } // WHILE
        xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_PRICE));
        break;
    case 'update':
        $percent = round($_POST['products_price_percent'], 2);
        // alten price auslesen
        if ($_POST['customers_group'] == '-') {
            $price_old = "SELECT p.products_id, p.products_price AS PRODUCTS_PRICE, p.products_quantity  
					FROM " . TABLE_PRODUCTS . " p,
						" . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
					WHERE p.products_id = p2c.products_id
					AND p2c.categories_id = '" . (int) $_POST['categories_id'] . "'";
            $price_old_query = xtc_db_query($price_old);

            // wenn kundengruppe gewaehlt ist	
        } elseif ($_POST['customers_group'] >= 0) {
            $price_old = "SELECT p.price_id, p.products_id, p.personal_offer AS PRODUCTS_PRICE 
					FROM " . TABLE_PERSONAL_OFFERS_BY . $_POST['customers_group'] . " p,
						" . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
					WHERE p.products_id = p2c.products_id
					AND p2c.categories_id = '" . (int) $_POST['categories_id'] . "'";
            $price_old_query = xtc_db_query($price_old);
        }
        // $_POST['price_sign']: + oder -
        while ($price_old = xtc_db_fetch_array($price_old_query)) {
            // pruefen ob preis bei kundengruppen 0.00 ist
            if ($price_old['PRODUCTS_PRICE'] != '0.000') {

                // Staffelpreie aktiv?
                // bei Allgemein Preis gibts keine Staffelpreise !!!
                if ($_POST['staffel'] == 1) {

                    if ($_POST['price_sign'] == '1') {
                        // wert oder prozent rechnen
                        if ($_POST['price_percent'] == '1') {
                            $percent_wert = $price_old['PRODUCTS_PRICE'] / 100;
                            $price_differenz = $percent_wert * $percent;
                            $new_price = $price_old['PRODUCTS_PRICE'] + $price_differenz;
                        } elseif ($_POST['price_percent'] == '2') {
                            $new_price = $price_old['PRODUCTS_PRICE'] + $percent;
                        }
                        // vorzeichen -
                    } elseif ($_POST['price_sign'] == '2') {
                        // wert oder prozent rechnen
                        if ($_POST['price_percent'] == '1') {
                            $percent_wert = $price_old['PRODUCTS_PRICE'] / 100;
                            $price_differenz = $percent_wert * $percent;
                            $new_price = $price_old['PRODUCTS_PRICE'] - $price_differenz;
                        } elseif ($_POST['price_percent'] == '2') {
                            $new_price = $price_old['PRODUCTS_PRICE'] - $percent;
                        }
                    }

                    xtc_db_query("UPDATE " . TABLE_PERSONAL_OFFERS_BY . $_POST['customers_group'] . " SET 
							personal_offer 			= '" . $new_price . "' 
							WHERE price_id 			= '" . $price_old['price_id'] . "'");
                }

                if ($_POST['staffel'] != 1) {
                    // vorzeichen +
                    if ($_POST['price_sign'] == '1') {
                        // wert oder prozent rechnen
                        if ($_POST['price_percent'] == '1') {
                            $percent_wert = $price_old['PRODUCTS_PRICE'] / 100;
                            $price_differenz = $percent_wert * $percent;
                            $new_price = $price_old['PRODUCTS_PRICE'] + $price_differenz;
                        } elseif ($_POST['price_percent'] == '2') {
                            $new_price = $price_old['PRODUCTS_PRICE'] + $percent;
                        }
                        // vorzeichen -
                    } elseif ($_POST['price_sign'] == '2') {
                        // wert oder prozent rechnen
                        if ($_POST['price_percent'] == '1') {
                            $percent_wert = $price_old['PRODUCTS_PRICE'] / 100;
                            $price_differenz = $percent_wert * $percent;
                            $new_price = $price_old['PRODUCTS_PRICE'] - $price_differenz;
                        } elseif ($_POST['price_percent'] == '2') {
                            $new_price = $price_old['PRODUCTS_PRICE'] - $percent;
                        }
                    }
                    // wenn allgemein, aktualisiere die prod. tabelle
                    if ($_POST['customers_group'] == '-') {

                        // Sind alle Preisreduzierungen Angebote?
                        if ($_POST['special'] == 1) {
                            $products_specials_array = array(
                                'products_id' => $price_old['products_id'],
                                'specials_quantity' => $price_old['products_quantity'],
                                'specials_new_products_price' => $new_price,
                                'specials_date_added' => date("Y-m-d H:i:s"));

                            xtc_db_perform(TABLE_SPECIALS, $products_specials_array);

                            // entfernt alle angebote aus dieser kategorie bei Kundengruppe ALLGEMEIN
                        } elseif ($_POST['special_delete'] == 1) {
                            $get_products_in_this_categorie_query = xtc_db_query("SELECT products_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE categories_id = '" . (int) $_POST['categories_id'] . "'");

                            while ($get_products_in_this_categorie = xtc_db_fetch_array($get_products_in_this_categorie_query)) {
                                xtc_db_query("DELETE FROM " . TABLE_SPECIALS . " WHERE products_id = '" . $get_products_in_this_categorie['products_id'] . "'");
                            }
                        } else {

                            // wenn keine Angebote
                            xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
								products_price 			= '" . $new_price . "' 
								WHERE products_id 		= '" . $price_old['products_id'] . "'");
                        }
                    } elseif ($_POST['customers_group'] >= 0) {

                        xtc_db_query("UPDATE " . TABLE_PERSONAL_OFFERS_BY . $_POST['customers_group'] . " SET 
							personal_offer 		= '" . $new_price . "' 
							WHERE price_id 		= '" . $price_old['price_id'] . "' 
							AND quantity			= 1");
                    }
                }
            } // IF price = 0.00
            // Lieferzeit
            if ($_POST['cat_shippingtime'] != 0) {
                xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
						products_shippingtime 		= '" . $_POST['cat_shippingtime'][$i] . "'
						WHERE products_id 			= '" . $price_old['products_id'] . "'");
            }

            // Attribute
            // ------------------------------------------------------------------------------------------------------------------------------------------------------------
            if ($_POST['attributs'] == 1) {

                $attrib_query = xtc_db_query("SELECT products_attributes_id, products_id, options_values_price AS ATT_PRICE 
						FROM " . TABLE_PRODUCTS_ATTRIBUTES . " 
						WHERE products_id = '" . $price_old['products_id'] . "' 
						AND options_values_price != '0.000'");

                while ($attrib = xtc_db_fetch_array($attrib_query)) {

                    // vorzeichen +
                    if ($_POST['price_sign'] == '1') {
                        // wert oder prozent rechnen
                        if ($_POST['price_percent'] == '1') {
                            $percent_wert = $attrib['ATT_PRICE'] / 100;
                            $price_differenz = $percent_wert * $percent;
                            $new_price = $attrib['ATT_PRICE'] + $price_differenz;
                        } elseif ($_POST['price_percent'] == '2') {
                            $new_price = $attrib['ATT_PRICE'] + $percent;
                        }
                        // vorzeichen -
                    } elseif ($_POST['price_sign'] == '2') {
                        // wert oder prozent rechnen
                        if ($_POST['price_percent'] == '1') {
                            $percent_wert = $attrib['ATT_PRICE'] / 100;
                            $price_differenz = $percent_wert * $percent;
                            $new_price = $attrib['ATT_PRICE'] - $price_differenz;
                        } elseif ($_POST['price_percent'] == '2') {
                            $new_price = $attrib['ATT_PRICE'] - $percent;
                        }
                    }

                    // wenn keine Angebote
                    xtc_db_query("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . " SET 
							options_values_price 			= '" . $new_price . "' 
							WHERE products_attributes_id 	= '" . $attrib['products_attributes_id'] . "'");
                }
            }
        } // WHILE



        xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_PRICE));
        break;
    case 'products_update':
        $count_products = count($_POST['products_id']);
        for ($i = 0; $i < $count_products; $i++) {

            // MwSt. beruecksichtigen
            if (PRICE_IS_BRUTTO == 'true') {
                $products_price = $_POST['products_price'][$i] / ((100 + xtc_get_tax_rate($_POST['products_tax_class_id'][$i])) / 100);
            } else {
                $products_price = $_POST['products_price'][$i];
            }

            // eintrag in products table
            xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
				products_quantity 			= '" . $_POST['products_quantity'][$i] . "',
				products_shippingtime 		= '" . $_POST['products_shippingtime'][$i] . "', 
				products_model				=	'" . $_POST['products_model'][$i] . "', 
				products_price 				= '" . $products_price . "' 
				WHERE products_id 			= '" . $_POST['products_id'][$i] . "'");

            // eintrag in products_description	
            xtc_db_query("UPDATE " . TABLE_PRODUCTS_DESCRIPTION . " SET 
				products_name 			= '" . addslashes($_POST['products_name'][$i]) . "'
				WHERE products_id 	= '" . $_POST['products_id'][$i] . "'
				AND language_id 		= '" . (int) $_SESSION['languages_id'] . "'");

            // angebote loeschen, nur ausgewaehlte Produkte
            if (isset($_POST['specials_delete'])) {
                xtc_db_query("DELETE FROM " . TABLE_SPECIALS . " WHERE specials_id = '" . $_POST['specials_delete'][$i] . "'");
            } else {

                // MwSt. beruecksichtigen
                if (PRICE_IS_BRUTTO == 'true') {
                    $products_price = $_POST['specials_new_products_price'][$i] / ((100 + xtc_get_tax_rate($_POST['products_tax_class_id'][$i])) / 100);
                } else {
                    $products_price = $_POST['specials_new_products_price'][$i];
                }

                // Array mit neuen Angeboten
                if ($_POST['specials_id'][$i] != '') { // UPDATE, wenn id vorhanden
                    // Ist Datum vorhanden?
                    if ($_POST['day'][$i] && $_POST['month'][$i] && $_POST['year'][$i]) {
                        $expires_date = $_POST['year'][$i];
                        $expires_date .= (strlen($_POST['month'][$i]) == 1) ? '0' . $_POST['month'][$i] : $_POST['month'][$i];
                        $expires_date .= (strlen($_POST['day'][$i]) == 1) ? '0' . $_POST['day'][$i] : $_POST['day'][$i];
                    }

                    xtc_db_query("UPDATE " . TABLE_SPECIALS . " SET 
						specials_quantity 				= '" . xtc_db_prepare_input($_POST['specials_quantity'][$i]) . "',
						specials_new_products_price 	= '" . xtc_db_prepare_input($products_price) . "',
						expires_date 					= '" . xtc_db_prepare_input($expires_date) . "'
						WHERE specials_id 				= '" . $_POST['specials_id'][$i] . "'
						AND products_id 				= '" . $_POST['products_id'][$i] . "'");
                    // INSERT		
                } elseif ($_POST['specials_id'][$i] == '' && $_POST['specials_new_products_price'][$i] != '') {

                    // Ist Datum vorhanden?
                    if ($_POST['day'][$i] && $_POST['month'][$i] && $_POST['year'][$i]) {
                        $expires_date = $_POST['year'][$i];
                        $expires_date .= (strlen($_POST['month'][$i]) == 1) ? '0' . $_POST['month'][$i] : $_POST['month'][$i];
                        $expires_date .= (strlen($_POST['day'][$i]) == 1) ? '0' . $_POST['day'][$i] : $_POST['day'][$i];
                    }
                    $products_specials_array = array(
                        'products_id' => xtc_db_prepare_input($_POST['products_id'][$i]),
                        'specials_quantity' => xtc_db_prepare_input($_POST['specials_quantity'][$i]),
                        'specials_new_products_price' => xtc_db_prepare_input($products_price),
                        'specials_date_added' => date("Y-m-d H:i:s"),
                        'expires_date' => xtc_db_prepare_input($expires_date));

                    xtc_db_perform(TABLE_SPECIALS, $products_specials_array);
                }
            }// Delete
        }// FOR
        xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_PRICE, 'action=products&category=' . $_POST['categories_id']));
        break;
    default: // alle anzeigen
        // alle kategorien einlesen

        $parent_id = isset($_GET['category']) ? $_GET['category'] : 0;
        $categories = "SELECT 
			c.categories_id, 
			c.categories_status,
			cd.categories_name
		FROM " . TABLE_CATEGORIES . " c,
			" . TABLE_CATEGORIES_DESCRIPTION . " cd
		WHERE c.categories_id = cd.categories_id
		AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "' 
		AND	c.parent_id = '" . $parent_id . "'
		ORDER BY cd.categories_name, c.sort_order ASC";



        $categories_query = xtc_db_query($categories);

        $num_selected_rows = xtc_db_num_rows($categories_query);

        //print_r ($num_selected_rows);
        // Shippingtime	
        $cat_shippingtime_array = array();
        $shippingtime_cat_query = xtc_db_query("SELECT
		shipping_status_id, shipping_status_name 
			FROM " . TABLE_SHIPPING_STATUS . "
			WHERE language_id = '" . (int) $_SESSION['languages_id'] . "'
				ORDER BY shipping_status_id ASC");

        $cat_shippingtime_array[] = array('id' => 0, 'text' => '---');
        while ($shippingtime = xtc_db_fetch_array($shippingtime_cat_query)) {
            $cat_shippingtime_array[] = array(
                'id' => $shippingtime['shipping_status_id'],
                'text' => $shippingtime['shipping_status_name']);
        }
}
require(DIR_WS_INCLUDES . 'header.php');
?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr>
        <td class="boxCenter" width="100%" valign="top">

            <!-- titel -->
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                </tr>
            </table>
            <!-- titel eof -->
            <table width="100%" border="0" cellspacing="1" cellpadding="2">
                <tr>
                    <td class="main"><?php echo CONTENT_NOTE; ?></td>
                </tr>
            </table>
            <br>
            <!-- auswahl -->
<?php
if ($num_selected_rows >= 1) {
    $price_sign = array();
    $price_percent = array();
    $customers = array();

    // pull down bilden, + oder -
    $price_sign[] = array('id' => '1', 'text' => '+');
    $price_sign[] = array('id' => '2', 'text' => '-');

    // pull down bilden, prozent oder wert
    $price_percent[] = array('id' => '1', 'text' => '%');
    $price_percent[] = array('id' => '2', 'text' => 'Wert');

    // kundengruppen auslesen
    $customers_group = "SELECT customers_status_id, customers_status_name 
		FROM " . TABLE_CUSTOMERS_STATUS . " 
		WHERE language_id = '" . (int) $_SESSION['languages_id'] . "'
		AND customers_status_id != '0'
		ORDER BY customers_status_id ASC";
    $customers_group_query = xtc_db_query($customers_group);

    $customers[0] = array('id' => '-', 'text' => CUSTOMERS_GROUP_PRICE_ALL);
    while ($customers_group = xtc_db_fetch_array($customers_group_query)) {
        $customers[] = array(
            'id' => $customers_group['customers_status_id'],
            'text' => $customers_group['customers_status_name']);
    }

    $all_products = xtc_db_query("SELECT COUNT(products_id) AS ANZAHL_ALLE FROM " . TABLE_PRODUCTS . " WHERE products_status = '1'");
    $all_products = xtc_db_fetch_array($all_products);

    echo xtc_draw_form('products_price', FILENAME_PRODUCTS_PRICE, 'action=update_all', 'post', '');
    ?>
                <!-- alle preise aktualisieren -->
                <table border="0" cellspacing="1" cellpadding="3" align="center">
                    <tr>
                        <td align="left" width="200">&nbsp;<?php echo CHANGE_ALL_PRODUCTS . '&nbsp;<b>' . $all_products['ANZAHL_ALLE']; ?></b>&nbsp;</td>
                        <td align="left"><?php echo xtc_draw_pull_down_menu('customers_group', $customers, '', ' style="width:100%;"'); ?></td>
                        <td align="left">
                <?php
                echo xtc_draw_pull_down_menu('all_price_sign', $price_sign, '', 'style="width:40px;"');
                echo xtc_draw_input_field('all_products_price_percent', '', 'size="6"');
                echo xtc_draw_pull_down_menu('all_price_percent', $price_percent, '', 'style="width:50px;"');
                ?>
                        </td>
                        <td align="left">
                <?php echo '<input type="image" src="images/icons/icon_add.png" value="' . PRODUCTS_PRICE_UPDATE . '" onClick="return confirm(\'' . UPDATE_ENTRY . '\')">'; ?>
                        </td>
                    </tr>
                </table>
                </form>


    <?php
    // kundengruppen auslesen
    $manufacturers_group = "SELECT manufacturers_id, manufacturers_name 
		FROM " . TABLE_MANUFACTURERS . " 
		ORDER BY manufacturers_name ASC";
    $manufacturers_group_query = xtc_db_query($manufacturers_group);

    while ($manufacturers_group = xtc_db_fetch_array($manufacturers_group_query)) {
        $manufacturers[] = array(
            'id' => $manufacturers_group['manufacturers_id'],
            'text' => $manufacturers_group['manufacturers_name']);
    }

    $all_manufacturers = xtc_db_query("SELECT COUNT(manufacturers_id) AS ANZAHL_ALLE FROM " . TABLE_MANUFACTURERS . "");
    $all_manufacturers = xtc_db_fetch_array($all_manufacturers);

    echo xtc_draw_form('products_price', FILENAME_PRODUCTS_PRICE, 'action=update_all_manufacturer', 'post', '');
    ?>
                <!-- alle preise aktualisieren -->
                <table border="0" cellspacing="1" cellpadding="3">
                    <tr>
                        <td align="left" width="200"><?php echo CHANGE_ALL_MANUFACTURER . '&nbsp;<b>' . $all_manufacturers['ANZAHL_ALLE']; ?></b></td>
                        <td align="left"><?php echo xtc_draw_pull_down_menu('manufacturers_group', $manufacturers, '', ' style="width:100%;"'); ?></td>
                        <td align="left">
                <?php
                echo xtc_draw_pull_down_menu('all_price_sign', $price_sign, '', 'style="width:40px;"');
                echo xtc_draw_input_field('all_products_price_percent', '', 'size="6"');
                echo xtc_draw_pull_down_menu('all_price_percent', $price_percent, '', 'style="width:50px;"');
                ?>
                        </td>
                        <td align="left">
                <?php echo '<input type="image" src="images/icons/icon_add.png" value="' . PRODUCTS_PRICE_UPDATE . '" onClick="return confirm(\'' . UPDATE_ENTRY . '\')">'; ?>
                        </td>
                    </tr>
                </table>
                </form>

                <!-- alle preise aktualisieren EOF -->
                <table border="0" cellspacing="1" cellpadding="3">
                    <tr>
                        <td align="left">
                            <a class="button" href="<?php echo xtc_href_link(FILENAME_PRODUCTS_PRICE) . '">' . NAVIGATION_OVERVIEW; ?></a>
                               </td>
                               </tr>
                               </table>

                               <table width="100%" border="0" cellspacing="1" cellpadding="3" align="center">
                                <tr>
                                    <td class="header_gift" width="5%" align="center"><?php echo CATEGORIES_ID; ?></td>
                                    <td class="header_gift" width="16%"><?php echo CATEGORIES_NAME; ?></td>
                                    <td class="header_gift" width="10%" align="center"><?php echo PRODUCTS_COUNT; ?></td>
                                    <td class="header_gift" width="12%" align="center"><?php echo CUSTOMERS_GROUP; ?></td>
                                    <td class="header_gift" width="14%" align="center"><?php echo PRODUCTS_PRICE_CHANGE; ?></td>
                                    <td class="header_gift" width="10%" align="center"><?php echo PRODUCTS_SHIPPING_TIME; ?></td>
                                    <td class="header_gift" width="7%" align="center"><?php echo PRODUCTS_ATTRIBUTS_TAB; ?></td>
                                    <td class="header_gift" width="7%" align="center"><?php echo PRODUCTS_STAFFEL_TAB; ?></td>
                                    <td class="header_gift" width="7%" align="center"><?php echo PRODUCTS_SPECIALS_TAB; ?></td>
                                    <td class="header_gift" width="7%" align="center"><?php echo PRODUCTS_SPECIALS_DELETE_TAB; ?></td>
                                    <td class="header_gift" width="5%" colspan="2" align="center"><?php echo CATEGORIES_ACTION; ?></td>
                                </tr>
    <?php
    $i = 0;
    while ($categories = xtc_db_fetch_array($categories_query)) {
        // alle Artikel zaehlen und Anzahl ausgeben
        $products = "SELECT COUNT(products_id) AS ANZAHL
			FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE categories_id = '" . $categories['categories_id'] . "'";
        $products_query = xtc_db_query($products);
        $products = xtc_db_fetch_array($products_query);
        ?>
        <?php
        echo xtc_draw_form('products_price', FILENAME_PRODUCTS_PRICE, 'action=update', 'post', '');
        ?>
                                    <tr bgcolor="<?php echo ($i % 2 ? '#eeeeee' : '#f1f1f1'); ?>">
                                        <td class="content_gift" align="center"><?php echo $categories['categories_id']; ?></td>
                                        <td class="content_gift"><a href="<?php echo xtc_href_link(FILENAME_PRODUCTS_PRICE, 'category=' . $categories['categories_id']) . '">' . $categories['categories_name'] . '</a>' . xtc_draw_hidden_field('categories_id', $categories['categories_id']); ?></td>
                                                                    <td class="content_gift" align="center"><?php echo $products['ANZAHL'] . xtc_draw_hidden_field('products_count', $products['ANZAHL']); ?></td>
                                        <td class="content_gift" align="center"><?php echo xtc_draw_pull_down_menu('customers_group', $customers, '', 'style="width:100%;"'); ?></td>
                                        <td class="content_gift" align="center">
        <?php
        echo xtc_draw_pull_down_menu('price_sign', $price_sign, '', 'style="width:40px;"');
        echo xtc_draw_input_field('products_price_percent', '', 'size="6"');
        echo xtc_draw_pull_down_menu('price_percent', $price_percent, '', 'style="width:50px;"');
        ?>
                                        </td>
                                        <td class="content_gift" align="center"><?php echo xtc_draw_pull_down_menu('cat_shippingtime', $cat_shippingtime_array, 0, 'style="width:100%;"'); ?></td>
                                        <td class="content_gift" align="center"><?php echo xtc_draw_selection_field('attributs', 'checkbox', '1'); ?></td>
                                        <td class="content_gift" align="center"><?php echo xtc_draw_selection_field('staffel', 'checkbox', '1'); ?></td>
                                        <td class="content_gift" align="center"><?php echo xtc_draw_selection_field('special', 'checkbox', '1'); ?></td>
                                        <td class="content_gift" align="center"><?php echo xtc_draw_selection_field('special_delete', 'checkbox', '1'); ?></td>
                                        <td class="content_gift" align="center"><?php echo '<input type="image" src="images/icons/icon_edit.gif" value="' . PRODUCTS_PRICE_UPDATE . '" onClick="return confirm(\'' . UPDATE_ENTRY . '\')">'; ?></td>
                                    </tr>
                                    <?php
                                    $i++;
                                    ?>	
                                    </form>	
        <?php
    }  // WHILE
    ?>
                </table>
                <br><br>
                                        <?php
                                    } //IF
                                    ?>
            <table width="100%" border="0" cellspacing="1" cellpadding="3" align="center" style="border-bottom:1px solid #dddddd;">
                <tr>
                    <td align="left" class="main" colspan="2" style="border-bottom:1px solid #dddddd;"><?php echo LEGENDE; ?></td>
                </tr>
                <tr>
                    <td align="left" class="main" width="20%" bgcolor="#eeeeee"><?php echo CUSTOMERS_GROUP; ?></td>
                    <td align="left" class="main"><?php echo LEGENDE_CUSTOMERS_GROUP; ?></td>
                </tr>	
                <tr>
                    <td align="left" class="main" width="20%" bgcolor="#eeeeee"><?php echo PRODUCTS_PRICE_CHANGE; ?></td>
                    <td align="left" class="main"><?php echo LEGENDE_PRODUCTS_PRICE_CHANGE; ?></td>
                </tr>	
                <tr>
                    <td align="left" class="main" width="20%" bgcolor="#eeeeee"><?php echo PRODUCTS_SPECIALS_TAB; ?></td>
                    <td align="left" class="main"><?php echo PRODUCTS_SPECIALS_TAB_TEXT; ?></td>
                </tr>		
                <tr>
                    <td align="left" class="main" width="20%" bgcolor="#eeeeee"><?php echo PRODUCTS_SPECIALS_DELETE_TAB; ?></td>
                    <td align="left" class="main"><?php echo PRODUCTS_SPECIALS_DELETE_TAB_TEXT; ?></td>
                </tr>	  
            </table>	
            <br><br>

            <?php
            if (isset($_GET['category'])) {
                $akt_kategorie_query = xtc_db_query("SELECT categories_id, categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " 
			WHERE categories_id = '" . (int) $_GET['category'] . "'
			AND language_id = '" . (int) $_SESSION['languages_id'] . "'");
                $akt_kategorie = xtc_db_fetch_array($akt_kategorie_query);

                echo xtc_draw_form('products_update', FILENAME_PRODUCTS_PRICE, 'action=products_update', 'post', '');
                ?>
                <table border="0" cellspacing="1" cellpadding="3">
                    <tr>
                        <td><a class="button" href="<?php echo xtc_href_link(FILENAME_PRODUCTS_PRICE) . '">' . NAVIGATION_OVERVIEW; ?></a></td>
                               </tr>
                               </table>
                               <table border="0" cellspacing="1" cellpadding="3">
                                <tr>
                                    <td><b><?php echo $akt_kategorie['categories_name'] . xtc_draw_hidden_field('categories_id', $akt_kategorie['categories_id']); ?></b></td>
                                </tr>
                </table>

                <table width="100%" border="0" cellspacing="1" cellpadding="3" align="center">
                    <tr>
                        <td class="header_gift" width="5%" align="center"><?php echo PRODUCTS_ID; ?></td>
                        <td class="header_gift" width="7%" align="left"><?php echo PRODUCTS_MODEL; ?></td>
                        <td class="header_gift" width="26%" align="left"><?php echo PRODUCTS_NAME; ?></td>
                        <td class="header_gift" width="10%" align="center"><?php echo PRODUCTS_PRICE; ?></td>
                        <td class="header_gift" width="10%" align="center"><?php echo PRODUCTS_SHIPPING_TIME; ?></td>
                        <td class="header_gift" width="7%" align="center"><?php echo PRODUCTS_QTY; ?></td>
                        <td class="header_gift" width="30%" align="center"><?php echo PRODUCTS_SPECIALS; ?></td>
                        <td class="header_gift" width="5%" align="center"><?php echo PRODUCTS_SPECIAL_DELETE_TAB; ?></td>
                    </tr>
                <?php
                // Zeichenlaenge begrenzen
                // left(pd.products_name, 50) AS products_name	
                $products_query = xtc_db_query("SELECT 
				p.products_id, 
				p.products_quantity,
				p.products_shippingtime,
				p.products_model,
				p.products_price,
				p.products_tax_class_id,
				pd.products_name
			FROM " . TABLE_PRODUCTS . " p,
				" . TABLE_PRODUCTS_DESCRIPTION . " pd,
				" . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
			WHERE p.products_status = '1'
			AND p2c.categories_id = '" . (int) $_GET['category'] . "'
			AND p2c.products_id = p.products_id
			AND p.products_id = pd.products_id
			AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "'
			ORDER BY p.products_id ASC");

                // Shippingtime		
                $shippingtime_query = xtc_db_query("SELECT
		shipping_status_id, shipping_status_name 
		FROM " . TABLE_SHIPPING_STATUS . "
		WHERE language_id = '" . (int) $_SESSION['languages_id'] . "'
		ORDER BY shipping_status_id ASC");

                while ($shippingtime = xtc_db_fetch_array($shippingtime_query)) {
                    $products_shippingtime_array[] = array(
                        'id' => $shippingtime['shipping_status_id'],
                        'text' => $shippingtime['shipping_status_name']);
                }
                $i = 0;
                while ($products = xtc_db_fetch_array($products_query)) {
                    $specials_query = xtc_db_query("SELECT 
				s.specials_id,
				s.specials_quantity,
				s.specials_new_products_price,
				s.expires_date,
				s.status
			FROM " . TABLE_SPECIALS . " s
			WHERE s.products_id = '" . $products['products_id'] . "'");
                    $specials = xtc_db_fetch_array($specials_query);
                    ?>	
                        <tr bgcolor="<?php echo ($i % 2 ? '#eeeeee' : '#f1f1f1'); ?>">
                            <td class="content_gift" align="center"><?php
                        echo $products['products_id']
                        . xtc_draw_hidden_field('products_tax_class_id[]', $products['products_tax_class_id'])
                        . xtc_draw_hidden_field('products_id[]', $products['products_id']);
                        ?></td>
                            <td class="content_gift" align="left"><?php echo xtc_draw_input_field('products_model[]', $products['products_model'], 'size="10" style="width:100%;"'); ?></td>
                            <td class="content_gift" align="left"><?php echo xtc_draw_input_field('products_name[]', $products['products_name'], 'size="30" style="width:100%;"'); ?></td>
                            <td class="content_gift" align="center">
                        <?php
                        if (PRICE_IS_BRUTTO == 'true') {
                            $products_price = xtc_round($products['products_price'] * ((100 + xtc_get_tax_rate($products['products_tax_class_id'])) / 100), PRICE_PRECISION);
                        } else {
                            $products_price = xtc_round($products['products_price'], PRICE_PRECISION);
                        }

                        echo xtc_draw_input_field('products_price[]', $products_price, 'size="10"');
                        ?></td>
                            <td class="content_gift" align="center"><?php echo xtc_draw_pull_down_menu('products_shippingtime[]', $products_shippingtime_array, $products['products_shippingtime'], 'style="width:100%;"'); ?></td>
                            <td class="content_gift" align="center"><?php echo xtc_draw_input_field('products_quantity[]', $products['products_quantity'], 'size="5" style="width:100%;"'); ?></td>
                            <td class="content_gift" align="center">

                                <table border="0" cellspacing="0" cellpadding="0" align="center">
                                    <tr>
                                        <td>
        <?php
        if (PRICE_IS_BRUTTO == 'true') {
            $sp_products_price = xtc_round($specials['specials_new_products_price'] * ((100 + xtc_get_tax_rate($products['products_tax_class_id'])) / 100), PRICE_PRECISION);
        } else {
            $sp_products_price = xtc_round($specials['specials_new_products_price'], PRICE_PRECISION);
        }
        echo xtc_draw_input_field('specials_new_products_price[]', $sp_products_price, 'size="10"') . xtc_draw_hidden_field('specials_id[]', $specials['specials_id']);
        ?></td>
                                        <td><?php echo xtc_draw_input_field('specials_quantity[]', $specials['specials_quantity'], 'size="3"'); ?></td>
                                        <td style="padding-left:10px;"><?php echo xtc_draw_input_field('day[]', substr($specials['expires_date'], 8, 2), 'size="2" maxlength="2" class="expires_day"') . xtc_draw_input_field('month[]', substr($specials['expires_date'], 5, 2), 'size="2" maxlength="2" class="expires_month"') . xtc_draw_input_field('year[]', substr($specials['expires_date'], 0, 4), 'size="4" maxlength="4" class="expires_year"'); ?></td>
                                    </tr>	
                                </table>

                            </td>
                            <!--
                                            <td bgcolor="<?php echo ($specials['status'] == 1 ? '#66FF00' : '#cccccc') ?>"><?php echo xtc_draw_selection_field('status[]', 'checkbox', '1', $specials['status'] == 1 ? true : false); ?></td>
                            -->		
                            <td><?php echo xtc_draw_selection_field('specials_delete[]', 'checkbox', $specials['specials_id'], ''); ?></td>
                        </tr>	
        <?php
        $i++;
    } // WHILE products
    ?>
                </table>
                <table width="100%" border="0" cellspacing="1" cellpadding="3" style="border-top:1px solid #cccccc;">
                    <tr>
                        <td><?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>'; ?></td>
                    </tr>
                </table>
                </form>

    <?php }
?>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
