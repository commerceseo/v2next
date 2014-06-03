<?php

/* -----------------------------------------------------------------
 * 	$Id: product_ask_a_question.php 522 2013-07-24 11:44:51Z akausch $
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

// create smarty elements
$module_smarty = new Smarty;

// include needed functions
require_once (DIR_FS_INC . 'xtc_validate_email.inc.php');

// validate and send message
if (isset($_SESSION['customer_id'])) {
    $account_query = xtc_db_query("SELECT
										customers_firstname,
										customers_lastname,
										customers_email_address
									FROM
										customers
									WHERE
										customers_id = '" . (int) $_SESSION['customer_id'] . "'");
    $account = xtc_db_fetch_array($account_query);
    $from_name = $account['customers_firstname'] . ' ' . $account['customers_lastname'];
    $from_email_address = $account['customers_email_address'];
}

if (isset($_POST['action']) && $_POST['action'] == 'process') {
    $error = false;

    $from_email_address = xtc_db_prepare_input($_POST['from_email_address']);
    $from_name = xtc_db_prepare_input($_POST['from_name']);
    $message = xtc_db_prepare_input($_POST['message']);
    $ask_subject = xtc_db_prepare_input($_POST['subject']);

    if (empty($from_name)) {
        $error = true;
        $messageStack->add('friend', ERROR_FROM_NAME);
    }

    if (empty($message)) {
        $error = true;
        $messageStack->add('friend', ERROR_MESSAGE);
    }

    if (!xtc_validate_email($from_email_address)) {
        $error = true;
        $messageStack->add('friend', ERROR_FROM_ADDRESS);
    }
    if (ANTISPAM_ASKQUESTION == 'true') {
        //Antispam
        $antispam_query = xtc_db_fetch_array(xtDBquery("SELECT 
														id, answer 
														FROM " . TABLE_CSEO_ANTISPAM . " 
														WHERE language_id = '" . (int) $_SESSION['languages_id'] . "'
														AND id = '" . $_POST['antispamid'] . "'
														"));

        if (empty($_POST["codeanwser"])) {
            $error = true;
            $messageStack->add('friend', SECURITY_CODE_ERROR);
        } elseif (mb_strtolower($antispam_query['answer'], 'UTF-8') != mb_strtolower($_POST["codeanwser"], 'UTF-8')) {
            $error = true;
            $messageStack->add('friend', SECURITY_CODE_ERROR);
        }
    }

    if ($error == false) {
        $smarty = new Smarty;
        $smarty->assign('MESSAGE', $_POST['message']);
        $smarty->assign('TEXT_NAME', $_POST['from_name']);
        $smarty->assign('FROM_EMAIL_ADDRESS', $_POST['from_email_address']);
        $smarty->assign('PRODUCT_NAME', $product->data['products_name'] . ($product->data['products_model'] != '' ? ' - Artikel-Nr.: ' . $product->data['products_model'] : ''));
        $smarty->assign('PRODUCT_LINK', xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product->data['products_id']));
        $smarty->assign('PRODUCT_IMAGE', $product_info['products_image']);
        $smarty->caching = false;
        require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
        $html_mail = nl2br($_POST['message_body']);
        $html_mail .= $smarty->fetch('html:askaquestion');
        $txt_mail = $_POST['message_body'];
        $txt_mail .= $smarty->fetch('txt:askaquestion');
        require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
        $mail_data = cseo_get_mail_data('askaquestion');
        $ask_subject = $_POST['subject'];

        xtc_php_mail($_POST['from_email_address'], $_POST['from_name'], $mail_data['EMAIL_ADDRESS'], $mail_data['EMAIL_ADDRESS_NAME'], $mail_data['EMAIL_FORWARD'], $_POST['from_email_address'], $_POST['from_name'], '', '', $ask_subject, $html_mail, $txt_mail);

        $module_smarty->assign('ALL_OK', PRODUCT_ASK_A_QUESTION_SUCCESS);
        xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $_POST['products_id']) . '#paq');
    } else {
        $module_smarty->assign('error_message', $messageStack->output('friend'));
    }
}


$eintrag1_tmp = array('id' => PRODUCT_AKS_A_QUESTION_SUBJECT_1,
    'text' => PRODUCT_AKS_A_QUESTION_SUBJECT_1);
$eintrag2_tmp = array('id' => PRODUCT_AKS_A_QUESTION_SUBJECT_2,
    'text' => PRODUCT_AKS_A_QUESTION_SUBJECT_2);
$eintrag3_tmp = array('id' => PRODUCT_AKS_A_QUESTION_SUBJECT_3,
    'text' => PRODUCT_AKS_A_QUESTION_SUBJECT_3);

$subject = array($eintrag1_tmp, $eintrag2_tmp, $eintrag3_tmp);
$subject_default = PRODUCT_AKS_A_QUESTION_SUBJECT_1;

$module_smarty->assign('FORM_START', xtc_draw_form('email_friend', xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product->data['products_id']) . '#paq') . xtc_draw_hidden_field('action', 'process') . xtc_draw_hidden_field('products_id', $product->data['products_id']));
$module_smarty->assign('FROM_NAME', xtc_draw_input_field('from_name', $_POST['from_name'] != '' ? $_POST['from_name'] : $from_name));
$module_smarty->assign('FROM_SUBJECT', xtc_draw_pull_down_menu('subject', $subject, $subject_default));
$module_smarty->assign('FROM_EMAIL', xtc_draw_input_field('from_email_address', $_POST['from_email_address'] != '' ? $_POST['from_email_address'] : $from_email_address));
$module_smarty->assign('TEXTFIELD', xtc_draw_textarea_field('message', 'soft', 68, 8));
$module_smarty->assign('BUTTON_SEND', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_SEND));

//Antispam beginn
$antispam_query = xtc_db_fetch_array(xtDBquery("SELECT id, question FROM " . TABLE_CSEO_ANTISPAM . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY rand() LIMIT 1"));
$module_smarty->assign('ANTISPAMCODEID', xtc_draw_hidden_field('antispamid', $antispam_query['id']));
$module_smarty->assign('ANTISPAMCODEQUESTION', $antispam_query['question']);
$module_smarty->assign('INPUT_ANTISPAMCODE', xtc_draw_input_field('codeanwser', '', 'size="6" maxlength="6"', 'text', false));
$module_smarty->assign('ANTISPAMCODEACTIVE', ANTISPAM_ASKQUESTION);
//Antispam end

$module_smarty->assign('FORM_END', '</form>');

$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$module_smarty->caching = false;

$ask_content = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/products_ask_a_question.html', USE_TEMPLATE_DEVMODE));

$info_smarty->assign('MODULE_products_ask_a_question', $ask_content);
