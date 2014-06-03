<?php
/**
    Shop System Plugins - Terms of use

    This terms of use regulates warranty and liability between Wirecard
    Central Eastern Europe (subsequently referred to as WDCEE) and it's
    contractual partners (subsequently referred to as customer or customers)
    which are related to the use of plugins provided by WDCEE.

    The Plugin is provided by WDCEE free of charge for it's customers and
    must be used for the purpose of WDCEE's payment platform integration
    only. It explicitly is not part of the general contract between WDCEE
    and it's customer. The plugin has successfully been tested under
    specific circumstances which are defined as the shopsystem's standard
    configuration (vendor's delivery state). The Customer is responsible for
    testing the plugin's functionality before putting it into production
    enviroment.
    The customer uses the plugin at own risk. WDCEE does not guarantee it's
    full functionality neither does WDCEE assume liability for any
    disadvantage related to the use of this plugin. By installing the plugin
    into the shopsystem the customer agrees to the terms of use. Please do
    not use this plugin if you do not agree to the terms of use!
*/

include ('includes/application_top.php');

// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
// include needed functions
require_once (DIR_FS_CATALOG.'includes/modules/payment/wirecard_checkout_page.php');

// if the customer is not logged on, redirect them to the login page

if (!isset ($_SESSION['customer_id']))
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() < 1)
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset ($_SESSION['cart']->cartID) && isset ($_SESSION['cartID'])) {
    if ($_SESSION['cart']->cartID != $_SESSION['cartID'])
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}

// if no shipping method has been selected, redirect the customer to the shipping method selection page
if (!isset ($_SESSION['shipping']))
    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

//check if display conditions on checkout page is true

if (isset ($_POST['payment']))
    $_SESSION['payment'] = xtc_db_prepare_input($_POST['payment']);

if ($_POST['comments_added'] != '')
    $_SESSION['comments'] = xtc_db_prepare_input($_POST['comments']);

//-- TheMedia Begin check if display conditions on checkout page is true
if (isset ($_POST['cot_gv']))
    $_SESSION['cot_gv'] = true;
// if conditions are not accepted, redirect the customer to the payment method selection page

if(isset($_SESSION['wirecard_checkout_page']['form'])) {
    $smarty->assign('CHECKOUT_FORM', xtc_draw_form('checkout_wirecard_checkout_page', MODULE_PAYMENT_WIRECARD_CHECKOUT_PAGE_INITIATION_URL, 'post', 'name="checkout_wirecard_checkout_page"') . $_SESSION['wirecard_checkout_page']['form']);
} else {
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

if (is_array($payment_modules->modules)) {
    $payment_button .= $payment_modules->process_button();
}
$smarty->assign('MODULE_BUTTONS', $payment_button);
$smarty->assign('REDIRECT_TEXT', $_SESSION['wirecard_checkout_page']['paypage_redirecttext']);
$smarty->assign('CHECKOUT_BUTTON', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONFIRM_ORDER).'</form>'."\n");

$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;

$smarty->display('base/module/wirecard_checkout_page_iframe.html');
include ('includes/application_bottom.php');
