<?php

/* -----------------------------------------------------------------
 * 	$Id: checkout_payment.php 928 2014-03-31 13:56:47Z akausch $
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
$smarty = new Smarty;
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

if (CHECKOUT_AJAX_STAT == 'true') {
    // SHOW PAYMENT ERROR
    if (isset($_GET['payment_error']) && is_object(${ $_GET['payment_error'] }) && ($error = ${$_GET['payment_error']}->get_error())) {
        $error = '&payment_error='.$error['error'];
    } elseif (isset($_GET['error_c'])) {
        $error = '&error_c='.defined($_GET['error_c']) ? constant($_GET['error_c']) : $_GET['error_c'];
    } elseif (isset($_GET['error_message'])) {
       $error = '&error_message='.defined($_GET['error_message']) ? constant($_GET['error_message']) : $_GET['error_message'];
    } else {
		$error = '';
	}

	xtc_redirect(xtc_href_link(FILENAME_CHECKOUT.$error, '', 'SSL'));
}

$smarty->assign('language', $_SESSION['language']);
require_once (DIR_FS_INC . 'xtc_address_label.inc.php');
require_once (DIR_FS_INC . 'xtc_get_address_format_id.inc.php');
require_once (DIR_FS_INC . 'xtc_check_stock.inc.php');
unset($_SESSION['tmp_oID']);
unset($_SESSION['transaction_id']); // moneybookers
unset($_SESSION['all_checkout_checks_ok']);
// if the customer is not logged on, redirect them to the login page
if (!isset($_SESSION['customer_id'])) {
    if (ACCOUNT_OPTIONS == 'guest') {
        xtc_redirect(xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'));
    } else {
        xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
    }
}

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() < 1)
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));

// if no shipping method has been selected, redirect the customer to the shipping method selection page
if (!isset($_SESSION['shipping']))
    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID'])) {
    if ($_SESSION['cart']->cartID != $_SESSION['cartID'])
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}
// recover carts
require_once (DIR_FS_INC.'xtc_checkout_site.inc.php');
xtc_checkout_site('payment');
if (isset($_SESSION['credit_covers']))
    unset($_SESSION['credit_covers']); //ICW ADDED FOR CREDIT CLASS SYSTEM
// Stock Check
if ((STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true')) {
    $products = $_SESSION['cart']->get_products();
    $any_out_of_stock = 0;
    for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
        if (xtc_check_stock($products[$i]['id'], $products[$i]['quantity']))
            $any_out_of_stock = 1;
    }
    if ($any_out_of_stock == 1)
        xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

// if no billing destination address was selected, use the customers own address as default
if (!isset($_SESSION['billto'])) {
    $_SESSION['billto'] = $_SESSION['customer_default_address_id'];
} else {
    // verify the selected billing address
    $check_address_query = xtc_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int) $_SESSION['customer_id'] . "' and address_book_id = '" . (int) $_SESSION['billto'] . "'");
    $check_address = xtc_db_fetch_array($check_address_query);

    if ($check_address['total'] != '1') {
        $_SESSION['billto'] = $_SESSION['customer_default_address_id'];
        if (isset($_SESSION['payment']))
            unset($_SESSION['payment']);
    }
}

if (!isset($_SESSION['sendto']) || $_SESSION['sendto'] == "")
    $_SESSION['sendto'] = $_SESSION['billto'];

require_once (DIR_WS_CLASSES . 'class.order.php');
$order = new order();

require_once (DIR_WS_CLASSES . 'class.order_total.php'); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM
$order_total_modules = new order_total(); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM

$total_weight = $_SESSION['cart']->show_weight();

//  $total_count = $_SESSION['cart']->count_contents();
$total_count = $_SESSION['cart']->count_contents_virtual(); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM

if ($order->billing['country']['iso_code_2'] != '' && $order->delivery['country']['iso_code_2'] == '') {
    $_SESSION['delivery_zone'] = $order->billing['country']['iso_code_2'];
} else {
    $_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
}

// load all enabled payment modules
require_once (DIR_WS_CLASSES . 'class.payment.php');
$payment_modules = new payment;

$order_total_modules->process();
// redirect if Coupon matches ammount

$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_PAYMENT, xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_PAYMENT, xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));

$smarty->assign('FORM_ACTION', xtc_draw_form('checkout_payment', xtc_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post', 'onSubmit="return check_form();"'));
$smarty->assign('ADDRESS_LABEL', xtc_address_label($_SESSION['customer_id'], $_SESSION['billto'], true, ' ', '<br />'));
$smarty->assign('BUTTON_ADDRESS', '<a href="' . xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL') . '">' . xtc_image_button('button_change_address.gif', IMAGE_BUTTON_CHANGE_ADDRESS) . '</a>');
$smarty->assign('BUTTON_CONTINUE', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
$smarty->assign('FORM_END', '</form>');

require_once (DIR_WS_INCLUDES . 'header.php');
$module_smarty = new Smarty;
if ($order->info['total'] > 0) {

    if (isset($_SESSION['error_message'])) {
        $smarty->assign('error', urldecode($_SESSION['error_message']));
    }
    if (isset($_GET['error_message'])) {
        $smarty->assign('error', urldecode($_GET['error_message']));
    }

    if (isset($_GET['payment_error']) && is_object(${ $_GET['payment_error'] }) && ($error = ${$_GET['payment_error']}->get_error())) {

        $smarty->assign('error', htmlspecialchars($error['error']));
    }
    // Click & Buy
    elseif (isset($_GET['error_c'])) {
        $smarty->assign('error', htmlspecialchars(defined($_GET['error_c']) ? constant($_GET['error_c']) : $_GET['error_c']));
    }
    // End Click & Buy
    // Paypal Error Messages:
    if (isset($_SESSION['reshash']['FORMATED_ERRORS'])) {
        $smarty->assign('error', $_SESSION['reshash']['FORMATED_ERRORS']);
    }

    $selection = $payment_modules->selection();
    for ($i = 0, $n = count($order->products); $i < $n; $i++) {
        $id = $order->products[$i]['id'];
        $nachnahmesperre_query = xtc_db_query("SELECT products_forbidden_payment FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $id . "' ");
        if ($i == '0') {
            $nachnahmesperre_data = xtc_db_fetch_array($nachnahmesperre_query);
        } else {
            $puffer = xtc_db_fetch_array($nachnahmesperre_query);
            if ($puffer['products_forbidden_payment'] != '') {
                $nachnahmesperre_data['products_forbidden_payment'] .= "|";
                $nachnahmesperre_data['products_forbidden_payment'] .= $puffer['products_forbidden_payment'];
            }
        }
    }
    $nachnahmesperre_data = explode("|", $nachnahmesperre_data['products_forbidden_payment']);
    $n = sizeof($selection);
    foreach ($nachnahmesperre_data AS $paymentmodule) {
        for ($i = 0; $i <= $n; $i++) {
			$name = explode('.', $paymentmodule);
            if ($selection[$i]['id'] == $name[0])
                unset($selection[$i]);
        }
    }
    $radio_buttons = 0;
    for ($i = 0; $i < $n; $i++) {

        $selection[$i]['radio_buttons'] = $radio_buttons;
        if (($selection[$i]['id'] == $payment) || ($n == 1)) {
            $selection[$i]['checked'] = 1;
        }

        if (sizeof($selection) > 1) {
            $selection[$i]['selection'] = xtc_draw_radio_field('payment', $selection[$i]['id'], ($selection[$i]['id'] == $_SESSION['payment']), 'class="checkout_payment_input"');
            if ($selection[$i]['id'] == $_SESSION['payment'])
                $selection[$i]['checked'] = true;
            else
                $selection[$i]['checked'] = false;
        } else {
            $selection[$i]['selection'] = xtc_draw_hidden_field('payment', $selection[$i]['id'], '', 'class="checkout_payment_input"');
        }

        if (isset($selection[$i]['error'])) {
            
        } else {

            $radio_buttons++;
        }
    }

    $module_smarty->assign('module_content', $selection);
} else {
    $smarty->assign('GV_COVER', 'true');
}

// PayPal neuer Start auf jeden Fall
unset($_SESSION['reshash']);
unset($_SESSION['nvpReqArray']);


if (ACTIVATE_GIFT_SYSTEM == 'true') {
    $smarty->assign('module_gift', $order_total_modules->credit_selection());
}

$module_smarty->caching = false;
$payment_block = $module_smarty->fetch(cseo_get_usermod('base/module/checkout_payment_block.html'));

$smarty->assign('COMMENTS', xtc_draw_textarea_field('comments', 'soft', '60', '5', $_SESSION['comments']) . xtc_draw_hidden_field('comments_added', 'YES'));
// AGB
if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {

    if (GROUP_CHECK == 'true') {
        $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
    }
    $shop_content_query = xtc_db_query("SELECT
											content_title,
											content_heading,
											content_text,
											content_file
											FROM 
												" . TABLE_CONTENT_MANAGER . "
											WHERE content_group= '3' 
											" . $group_check . "
											AND 
												languages_id='" . (int) $_SESSION['languages_id'] . "'");
    $shop_content_data = xtc_db_fetch_array($shop_content_query);

    if ($shop_content_data['content_file'] != '') {
        if ($shop_content_data['content_file'] == 'janolaw_agb.php') {
            include (DIR_FS_INC . 'janolaw.inc.php');
            $conditions = JanolawContent('agb', 'txt');
        }
        else
            $conditions = '<div class="agbframe">' . file_get_contents(DIR_FS_DOCUMENT_ROOT . 'media/content/' . $shop_content_data['content_file']) . '</div>';
    } else {
        $conditions = '<div class="agbframe">' . $shop_content_data['content_text'] . '</div>';
    }

    $smarty->assign('AGB', $conditions);
    $smarty->assign('AGB_LINK', $main->getContentLink(3, MORE_INFO, 'SSL'));
    $smarty->assign('AGB_checkbox', '<input type="checkbox" value="conditions" name="conditions" />');
    $smarty->assign('BUTTON_PRINT_AGB', '(<a style="cursor:pointer" onclick="javascript:window.open(\'' . xtc_href_link(FILENAME_PRINT_CONTENT, 'coID=3') . '\', \'popup\', \'toolbar=0, width=640, height=600\')">' . PRINT_CONTENT . '</a>');
    $smarty->assign('AGB_PDF_LINK', '<a href="' . xtc_href_link(FILENAME_PRINT_PDF, 'content=3') . '">' . xtc_image('images/button_pdf.gif', IMAGE_BUTTON_PRINT_PDF) . '</a>)');
    if (CHECKOUT_CHECKBOX_AGB == 'true') {
        $smarty->assign('AGB_CHECKBOX', 'true');
    }
}
// Datenschutz
if (DISPLAY_DATENSCHUTZ_ON_CHECKOUT == 'true') {
    if (GROUP_CHECK == 'true') {
        $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
    }
    $shop_content_query = xtc_db_query("SELECT
											content_title,
											content_heading,
											content_text,
											content_file
											FROM " . TABLE_CONTENT_MANAGER . "
											WHERE 
												content_group = '2' 
												" . $group_check . "
											AND 
												languages_id='" . (int) $_SESSION['languages_id'] . "'");
    $shop_content_data = xtc_db_fetch_array($shop_content_query);

    if ($shop_content_data['content_file'] != '') {
        if ($shop_content_data['content_file'] == 'janolaw_agb.php') {
            include (DIR_FS_INC . 'janolaw.inc.php');
            $conditions = JanolawContent('agb', 'txt');
        }
        else
            $conditions = '<div class="agbframe">' . file_get_contents(DIR_FS_DOCUMENT_ROOT . 'media/content/' . $shop_content_data['content_file']) . '</div>';
    } else {
        $conditions = '<div class="agbframe">' . $shop_content_data['content_text'] . '</div>';
    }

    $smarty->assign('DS', $conditions);
    $smarty->assign('DS_LINK', $main->getContentLink(2, MORE_INFO, 'SSL'));
    $smarty->assign('DS_checkbox', '<input type="checkbox" value="datenschutz" name="datenschutz" />');
    $smarty->assign('BUTTON_PRINT_DS', '(<a style="cursor:pointer" onclick="javascript:window.open(\'' . xtc_href_link(FILENAME_PRINT_CONTENT, 'coID=2') . '\', \'popup\', \'toolbar=0, width=640, height=600\')">' . PRINT_CONTENT . '</a>');
    $smarty->assign('DS_PDF_LINK', '<a href="' . xtc_href_link(FILENAME_PRINT_PDF, 'content=2') . '">' . xtc_image('images/button_pdf.gif', IMAGE_BUTTON_PRINT_PDF) . '</a>)');
    if (CHECKOUT_CHECKBOX_DSG == 'true') {
        $smarty->assign('DSG_CHECKBOX', 'true');
    }
    if (DISPLAY_DATENSCHUTZ_ON_CHECKOUT == 'true') {
        $smarty->assign('DATENSCHUTZ_ON_CHECKOUT', 'true');
    }
}
// REVOCATION
if (DISPLAY_WIDERRUFSRECHT_ON_CHECKOUT == 'true') {
    if (GROUP_CHECK == 'true') {
        $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
    }
    $shop_content_query = xtc_db_query("SELECT
											content_title,
											content_heading,
											content_text,
											content_file
											FROM 
												" . TABLE_CONTENT_MANAGER . "
											WHERE 
												content_group = '" . REVOCATION_ID . "' 
												" . $group_check . "
											AND 
												languages_id='" . (int) $_SESSION['languages_id'] . "'");
    $shop_content_data = xtc_db_fetch_array($shop_content_query);

    if ($shop_content_data['content_file'] != '') {
        if ($shop_content_data['content_file'] == 'janolaw_agb.php') {
            include (DIR_FS_INC . 'janolaw.inc.php');
            $conditions = JanolawContent('agb', 'txt');
        }
        else
            $conditions = '<div class="agbframe">' . file_get_contents(DIR_FS_DOCUMENT_ROOT . 'media/content/' . $shop_content_data['content_file']) . '</div>';
    } else {
        $conditions = '<div class="agbframe">' . $shop_content_data['content_text'] . '</div>';
    }

    $smarty->assign('WD', $conditions);
    $smarty->assign('WD_LINK', $main->getContentLink(REVOCATION_ID, MORE_INFO, 'SSL'));
    $smarty->assign('WD_checkbox', '<input type="checkbox" value="widerrufsrecht" name="widerrufsrecht" />');
    $smarty->assign('BUTTON_PRINT_WD', '(<a style="cursor:pointer" onclick="javascript:window.open(\'' . xtc_href_link(FILENAME_PRINT_CONTENT, 'coID=10') . '\', \'popup\', \'toolbar=0, width=640, height=600\')">' . PRINT_CONTENT . '</a>');
    $smarty->assign('WD_PDF_LINK', '<a href="' . xtc_href_link(FILENAME_PRINT_PDF, 'content=10') . '">' . xtc_image('images/button_pdf.gif', IMAGE_BUTTON_PRINT_PDF) . '</a>)');
    if (CHECKOUT_CHECKBOX_REVOCATION == 'true') {
        $smarty->assign('REVOCATION_CHECKBOX', 'true');
    }
    if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true') {
        $smarty->assign('REVOCATION_ON_CHECKOUT', 'true');
    }
}


$smarty->assign('language', $_SESSION['language']);
$smarty->assign('PAYMENT_BLOCK', $payment_block);
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->caching = false;

$cseo_checkout = cseohookfactory::create_object('CheckoutPaymentExtender');
$cseo_checkout->set_data('GET', $_GET);
$cseo_checkout->set_data('POST', $_POST);
$cseo_checkout->proceed();
$cseo_extender_result_array = $cseo_checkout->get_response();
if (is_array($cseo_extender_result_array)) {
	foreach ($cseo_extender_result_array AS $t_key => $t_value) {
		$smarty->assign($t_key, $t_value);
	}
}

$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/checkout_payment.html',USE_TEMPLATE_DEVMODE));
$smarty->assign('main_content', $main_content);

$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html',USE_TEMPLATE_DEVMODE));

include ('includes/application_bottom.php');
