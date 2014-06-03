<?php
include ('includes/application_top.php');
// include needed functions
require_once (DIR_FS_INC . 'xtc_calculate_tax.inc.php');
require_once (DIR_FS_INC . 'xtc_address_label.inc.php');
require_once (DIR_FS_INC . 'changedatain.inc.php');

if(file_exists(DIR_WS_CLASSES . 'payment/class.secupay_api.php')) {
    require_once(DIR_WS_CLASSES . 'payment/class.secupay_api.php');
}else{
    require_once("../".DIR_WS_CLASSES . 'payment/class.secupay_api.php');
}

$logme = MODULE_PAYMENT_SPKK_LOGGING == "Ja" ;

secupay_log($logme, "iFrame Start");

if(isset($_SESSION['iframe_link']) && strlen($_SESSION['iframe_link']) > 10){
    $iframe=$_SESSION['iframe_link'];
    secupay_log($logme, "iFrame ".$iframe);
}else{
    secupay_log($logme, "iFrame Bedingung 1");
	if (CHECKOUT_AJAX_STAT == 'true') {
		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT, '', 'SSL'));
	} else {
		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
	}
    
}

$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
// if the customer is not logged on, redirect them to the login page
if (!isset($_SESSION['customer_id'])) {
    secupay_log($logme, "iFrame Bedingung 2");
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

if ($_SESSION['customers_status']['customers_status_show_price'] != '1') {
    secupay_log($logme, "iFrame Bedingung 3");
    xtc_redirect(xtc_href_link(FILENAME_DEFAULT, '', ''));
}

if ((xtc_not_null(MODULE_PAYMENT_INSTALLED)) && ( !isset($_SESSION['payment']))) {
    secupay_log($logme, "iFrame Bedingung 4");
	if (CHECKOUT_AJAX_STAT == 'true') {
		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT, '', 'SSL'));
	} else {
		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
	}
}

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID'])) {
    if ($_SESSION['cart']->cartID != $_SESSION['cartID']) {
        secupay_log($logme, "iFrame Bedingung 5");
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
}

secupay_log($logme, "iFrame Bedingungen ok");

require_once (DIR_WS_INCLUDES . 'header.php');
// load selected payment module
require (DIR_WS_CLASSES . 'class.payment.php');

$payment_modules = new payment($_SESSION['payment']);

$smarty->assign('iframe_url', $iframe);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;

$main_content = '
    <style type="text/css">

.navLeft{
    height:630px;
    }
.iframe_wrapper{    
    margin-top:10px;
    text-align:center;
}
#iframe_wrapper_center{
border:1px solid #212121;
margin: 0 auto;
width: 550px;
}

div.wrap_shop div.iframe_wrapper {
    z-index: 1000;
    position: absolute;
}

#spiframe{
width:550px;
}
#fail_link a{    
font-size:normal;
text-decoration:none;
font-weight:bold;
color:white;
}
#fail_link{
background-color:#b55353;
text-align:center;
}</style>

<div class="iframe_wrapper">
    <div id="iframe_wrapper_center">
        <iframe id="spiframe" src="' . $iframe . '" width="100%" height="670" scrolling="auto" name="_top" frameborder="0"></iframe>
    </div>
</div>';

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = false;
$smarty->display(CURRENT_TEMPLATE . '/index.html');
require_once('includes/application_bottom.php');
