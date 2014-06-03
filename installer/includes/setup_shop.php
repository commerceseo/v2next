<?php

/* -----------------------------------------------------------------
 * 	$Id: setup_shop.php 791 2014-01-02 11:54:43Z akausch $
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

require_once(DIR_FS_INC . 'cseo_db.inc.php');
require_once(DIR_FS_INC . 'xtc_rand.inc.php');
require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
require_once(DIR_FS_INC . 'cseo_form.inc.php');
require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');

include('language/' . $_SESSION['language'] . '.php');

// connect do database
xtc_db_connect() or die('Unable to connect to database server!');

// get configuration data
$configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);
while ($configuration = xtc_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
}


$process = false;
if (isset($_POST['action']) && ($_POST['action'] == 'setup_shop')) {
    $process = true;

    $status_discount = xtc_db_prepare_input($_POST['STATUS_DISCOUNT']);
    $status_ot_discount_flag = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT_FLAG']);
    $status_ot_discount = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT']);
    $graduated_price = xtc_db_prepare_input($_POST['STATUS_GRADUATED_PRICE']);
    $show_price = xtc_db_prepare_input($_POST['STATUS_SHOW_PRICE']);
    $show_tax = xtc_db_prepare_input($_POST['STATUS_SHOW_TAX']);


    $status_discount2 = xtc_db_prepare_input($_POST['STATUS_DISCOUNT2']);
    $status_ot_discount_flag2 = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT_FLAG2']);
    $status_ot_discount2 = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT2']);
    $graduated_price2 = xtc_db_prepare_input($_POST['STATUS_GRADUATED_PRICE2']);
    $show_price2 = xtc_db_prepare_input($_POST['STATUS_SHOW_PRICE2']);
    $show_tax2 = xtc_db_prepare_input($_POST['STATUS_SHOW_TAX2']);

    $error = false;
    // default guests
    if (strlen($status_discount) < '3') {
        $error = true;
    }
    if (strlen($status_ot_discount) < '3') {
        $error = true;
    }
    if (($status_ot_discount_flag != '1') && ($status_ot_discount_flag != '0')) {
        $error = true;
    }
    if (($graduated_price != '1') && ($graduated_price != '0')) {
        $error = true;
    }
    if (($show_price != '1') && ($show_price != '0')) {
        $error = true;
    }
    if (($show_tax != '1') && ($show_tax != '0')) {
        $error = true;
    }

    // default customers
    if (strlen($status_discount2) < '3') {
        $error = true;
    }
    if (strlen($status_ot_discount2) < '3') {
        $error = true;
    }
    if (($status_ot_discount_flag2 != '1') && ($status_ot_discount_flag2 != '0')) {
        $error = true;
    }
    if (($graduated_price2 != '1') && ($graduated_price2 != '0')) {
        $error = true;
    }
    if (($show_price2 != '1') && ($show_price2 != '0')) {
        $error = true;
    }
    if (($show_tax2 != '1') && ($show_tax2 != '0')) {
        $error = true;
    }

    if ($error == false) {


        // admin
        xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES ('0', '1', 'Admin', 0, 'admin_status.png', '0.00', '0', '0.00', '1', '1', '1', 1)");
        xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES ('0', '2', 'Admin', 0, 'admin_status.png', '0.00', '0', '0.00', '1', '1', '1', 1)");

        // status Guest
        xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_write_reviews, customers_status_add_tax_ot) VALUES (1, 1, 'Guest', 0, 'guest_status.png', '" . $status_discount . "', '" . $status_ot_discount_flag . "', '" . $status_ot_discount . "', '" . $graduated_price . "', '" . $show_price . "', '" . $show_tax . "', '0', 1)");
        xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_write_reviews, customers_status_add_tax_ot) VALUES (1, 2, 'Gast', 0, 'guest_status.png', '" . $status_discount . "', '" . $status_ot_discount_flag . "', '" . $status_ot_discount . "', '" . $graduated_price . "', '" . $show_price . "', '" . $show_tax . "', '0', 1)");

        // status New customer
        xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES (2, 1, 'New customer', 0, 'customer_status.png', '" . $status_discount2 . "', '" . $status_ot_discount_flag2 . "', '" . $status_ot_discount2 . "', '" . $graduated_price2 . "', '" . $show_price2 . "', '" . $show_tax2 . "', 1)");
        xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES (2, 2, 'Neuer Kunde', 0, 'customer_status.png', '" . $status_discount2 . "', '" . $status_ot_discount_flag2 . "', '" . $status_ot_discount2 . "', '" . $graduated_price2 . "', '" . $show_price2 . "', '" . $show_tax2 . "', 1)");

        // status Merchant
        xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES (3, 1, 'Merchant', 0, 'merchant_status.png', '0.00', '0', '0.00', '1', 1, 0, 1)");
        xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES (3, 2, 'Händler', 0, 'merchant_status.png', '0.00', '0', '0.00', '1', 1, 0, 1)");

        // create Group prices (Admin wont get own status!)
        xtc_db_query("CREATE TABLE personal_offers_by_customers_status_ (price_id int(11) NOT NULL auto_increment, products_id int(11) NOT NULL, quantity int(11) default NULL, personal_offer decimal(15,4) default NULL, PRIMARY KEY  (price_id), KEY products_id (products_id,quantity));");
        xtc_db_query("CREATE TABLE personal_offers_by_customers_status_0 (price_id int(11) NOT NULL auto_increment, products_id int(11) NOT NULL, quantity int(11) default NULL, personal_offer decimal(15,4) default NULL, PRIMARY KEY  (price_id), KEY products_id (products_id,quantity));");
        xtc_db_query("CREATE TABLE personal_offers_by_customers_status_1 (price_id int(11) NOT NULL auto_increment, products_id int(11) NOT NULL, quantity int(11) default NULL, personal_offer decimal(15,4) default NULL, PRIMARY KEY  (price_id), KEY products_id (products_id,quantity));");
        xtc_db_query("CREATE TABLE personal_offers_by_customers_status_2 (price_id int(11) NOT NULL auto_increment, products_id int(11) NOT NULL, quantity int(11) default NULL, personal_offer decimal(15,4) default NULL, PRIMARY KEY  (price_id), KEY products_id (products_id,quantity));");
        xtc_db_query("CREATE TABLE personal_offers_by_customers_status_3 (price_id int(11) NOT NULL auto_increment, products_id int(11) NOT NULL, quantity int(11) default NULL, personal_offer decimal(15,4) default NULL, PRIMARY KEY  (price_id), KEY products_id (products_id,quantity));");
    }
}
