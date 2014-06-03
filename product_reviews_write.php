<?php

/* -----------------------------------------------------------------
 * 	$Id: product_reviews_write.php 643 2013-09-30 13:26:51Z akausch $
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
// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

if ($_SESSION['customers_status']['customers_status_write_reviews'] == 0) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

if (isset($_GET['action']) && $_GET['action'] == 'process') {
    if (is_object($product) && $product->isProduct()) { // We got to the process but it is an illegal product, don't write
        if ($_GET['mode'] == 'ajax')
            $url = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product->data['products_id']);
        else
            $url = xtc_href_link(FILENAME_PRODUCT_REVIEWS, $_POST['get_params']);
    }

    $customer = xtc_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int) $_SESSION['customer_id'] . "'");
    $customer_values = xtc_db_fetch_array($customer);
    $date_now = date('Ymd');
    if ($customer_values['customers_lastname'] == '')
        $customer_values['customers_lastname'] = TEXT_GUEST;

    //Antispam
    $antispam_query = xtc_db_fetch_array(xtDBquery("SELECT 
													id, answer 
													FROM " . TABLE_CSEO_ANTISPAM . " 
													WHERE language_id = '" . (int) $_SESSION['languages_id'] . "'
													AND id = '" . $_POST['antispamid'] . "'
													"));

    if (strlen($_POST['review']) < REVIEW_TEXT_MIN_LENGTH) {
        $_SESSION['error_msg'] = MIN_REVIEW_TEXT_ERROR;
        $_SESSION['tmp_review'] = $_POST['review'];
        xtc_redirect($url);
    } else {
        if ($_POST['rating'] == '') {
            $_SESSION['error_msg'] = MIN_REVIEW_RATING_ERROR;
            $_SESSION['tmp_review'] = $_POST['review'];
            xtc_redirect($url);
        } else {
            // if(strtoupper($antispam_query['answer']) == strtoupper($_POST['codeanwser'])) {
            if (mb_strtolower($antispam_query['answer'], 'UTF-8') == mb_strtolower($_POST["codeanwser"], 'UTF-8')) {

                xtc_db_query("INSERT INTO " . TABLE_REVIEWS . " (products_id, customers_id, customers_name, reviews_rating, date_added) values ('" . $product->data['products_id'] . "', '" . (int) $_SESSION['customer_id'] . "', '" . addslashes($customer_values['customers_firstname']) . ' ' . addslashes($customer_values['customers_lastname']) . "', '" . addslashes($_POST['rating']) . "', now())");
                $insert_id = xtc_db_insert_id();
                xtc_db_query("INSERT INTO " . TABLE_REVIEWS_DESCRIPTION . " (reviews_id, languages_id, reviews_text) VALUES ('" . $insert_id . "', '" . (int) $_SESSION['languages_id'] . "', '" . addslashes($_POST['review']) . "')");

                $_SESSION['success_msg'] = TEXT_REVIEW_SUCCESS_MSG;

                unset($_SESSION['tmp_rating']);
                unset($_SESSION['tmp_review']);
                unset($_POST['review']);
                xtc_redirect($url);
            } else {
                $_SESSION['error_msg'] = SECURITY_CODE_ERROR;
                $_SESSION['tmp_rating'] = $_POST['rating'];
                $_SESSION['tmp_review'] = $_POST['review'];
                xtc_redirect($url);
            }
        }
    }
}

// lets retrieve all $HTTP_GET_VARS keys and values..
$get_params = xtc_get_all_get_params();
$get_params_back = xtc_get_all_get_params(array('reviews_id')); // for back button
$get_params = substr($get_params, 0, -1); //remove trailing &
if (xtc_not_null($get_params_back)) {
    $get_params_back = substr($get_params_back, 0, -1); //remove trailing &
} else {
    $get_params_back = $get_params;
}

$breadcrumb->add(NAVBAR_TITLE_REVIEWS_WRITE, xtc_href_link(FILENAME_PRODUCT_REVIEWS, $get_params));

$name_query = xtc_db_fetch_array(xtc_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int) $_SESSION['customer_id'] . "'"));

require (DIR_WS_INCLUDES . 'header.php');

if (!$product->isProduct()) {
    $smarty->assign('error', ERROR_INVALID_PRODUCT);
} else {
    if ($name_query['customers_firstname'] == '')
        $name = TEXT_GUEST;
    else
        $name = $name_query['customers_firstname'] . ' ' . $name_query['customers_lastname'];

    $smarty->assign('AUTHOR', $name);
    $smarty->assign('PRODUCTS_NAME', $product->data['products_name']);
    $smarty->assign('FORM_ACTION', xtc_draw_form('product_reviews_write_new', xtc_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'action=process&' . xtc_product_link($product->data['products_id'], $product->data['products_name'])), 'post', 'onSubmit="return checkForm();"'));
    //Antispam beginn
    $antispam_query = xtc_db_fetch_array(xtDBquery("SELECT id, question FROM " . TABLE_CSEO_ANTISPAM . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY rand() LIMIT 1"));
    $smarty->assign('ANTISPAMCODEID', xtc_draw_hidden_field('antispamid', $antispam_query['id']));
    $smarty->assign('ANTISPAMCODEQUESTION', $antispam_query['question']);
    $smarty->assign('INPUT_ANTISPAMCODE', xtc_draw_input_field('codeanwser', '', 'size="6" maxlength="6"', 'text', false));
    $smarty->assign('ANTISPAMCODEACTIVE', ANTISPAM_REVIEWS);
    //Antispam end

    $smarty->assign('INPUT_TEXT', xtc_draw_textarea_field('review', 'soft', 60, 8, '', 'style="width:98.5%"', false));
    $smarty->assign('INPUT_RATING', xtc_draw_radio_field('rating', '1', '', 'class="star"') . ' ' . xtc_draw_radio_field('rating', '2', '', 'class="star"') . ' ' . xtc_draw_radio_field('rating', '3', '', 'class="star"') . ' ' . xtc_draw_radio_field('rating', '4', '', 'class="star"') . ' ' . xtc_draw_radio_field('rating', '5', '', 'class="star"'));
    // $smarty->assign('BUTTON_BACK', '<a href="javascript:history.back(1)">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
	$smarty->assign('BUTTON_BACK', '<a href="' . xtc_href_link(FILENAME_PRODUCT_INFO, $get_params_back) . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
    $smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE) . xtc_draw_hidden_field('get_params', $get_params));

    $smarty->assign('FORM_END', '</form>');
    unset($_SESSION['tmp_review']);
}
if (file_exists(DIR_WS_INCLUDES . 'addons/product_reviews_write_addon.php')) {
	include (DIR_WS_INCLUDES . 'addons/product_reviews_write_addon.php');
}

$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;
$smarty->loadFilter('output', 'note');
$smarty->loadFilter('output', 'trimwhitespace');
$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_reviews_write.html', USE_TEMPLATE_DEVMODE));
$smarty->assign('main_content', $main_content);

$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));

include ('includes/application_bottom.php');
