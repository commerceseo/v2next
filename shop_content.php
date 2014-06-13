<?php

/* -----------------------------------------------------------------
 * 	$Id: shop_content.php 943 2014-04-08 13:26:37Z akausch $
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

require ('includes/application_top.php');
$smarty = new Smarty;
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
require_once (DIR_FS_INC . 'xtc_validate_email.inc.php');

$coid = (int) $_GET['coID'];
$group_check = '';
if (GROUP_CHECK == 'true') {
    $group_check = "AND group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
}

$shop_content_data = xtc_db_fetch_array(xtDBquery("SELECT
									*
								 FROM 
									" . TABLE_CONTENT_MANAGER . "
								 WHERE 
									content_group = '" . $coid . "' 
								 " . $group_check . "
								 AND 
									languages_id='" . (int) $_SESSION['languages_id'] . "'"));

$breadcrumb->add($shop_content_data['content_title'], xtc_href_link(FILENAME_CONTENT, 'coID=' . (int) $_GET['coID']));

if ($coid != 7) {
    require_once (DIR_WS_INCLUDES . 'header.php');
}
if ($coid == 7 && $_GET['action'] == 'success') {
    require_once (DIR_WS_INCLUDES . 'header.php');
}

$smarty->assign('CONTENT_HEADING', $shop_content_data['content_heading']);

if ($coid == 7) {
    $error = false;
    if (isset($_GET['action']) && ($_GET['action'] == 'send')) {
        if (!xtc_validate_email(trim($_POST['email']))) {
            $error = true;
            $smarty->assign('error_message', ERROR_MAIL);
        }
        if ($_POST['datensg'] != '0') {
            $error = true;
            $smarty->assign('error_message', ERROR_CDATENSG);
        }
        if ($_POST['message_body'] == '') {
            $error = true;
            $smarty->assign('error_message', ERROR_MESSAGE);
        }
        if (ANTISPAM_CONTACT == 'true') {
            //Antispam
            $antispam_query = xtc_db_fetch_array(xtDBquery("SELECT 
															id, answer 
															FROM " . TABLE_CSEO_ANTISPAM . " 
															WHERE language_id = '" . (int) $_SESSION['languages_id'] . "'
															AND id = '" . $_POST['antispamid'] . "'
															"));

            if (empty($_POST["codeanwser"])) {
                $error = true;
                $smarty->assign('error_message', SECURITY_CODE_ERROR);
            } elseif (mb_strtolower($antispam_query['answer'], 'UTF-8') != mb_strtolower($_POST["codeanwser"], 'UTF-8')) {
                $error = true;
                $smarty->assign('error_message', SECURITY_CODE_ERROR);
            }
        }


        if ($error == false) {
            $smarty->caching = false;
            require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
            $html_mail = nl2br($_POST['message_body']);
            $html_mail .= $smarty->fetch('html:contact');
            $html_mail .= $signatur_html;
            $txt_mail = $_POST['message_body'];
            $txt_mail .= $smarty->fetch('txt:contact');
            $txt_mail .= $signatur_text;
            require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
            $mail_data = cseo_get_mail_data('contact');

            // create subject
            $contact_subject = str_replace('{$date}', strftime(DATE_FORMAT_LONG), $mail_data['EMAIL_SUBJECT']);
            $contact_subject = str_replace('{$shop_besitzer}', STORE_OWNER, $contact_subject);
            $contact_subject = str_replace('{$shop_name}', STORE_NAME, $contact_subject);

            // send mail to admin
            xtc_php_mail($_POST['email'], $_POST['name'], $mail_data['EMAIL_ADDRESS'], $mail_data['EMAIL_ADDRESS_NAME'], $mail_data['EMAIL_FORWARD'], $_POST['email'], $_POST['name'], '', '', $contact_subject, $html_mail, $txt_mail);

            if (!isset($mail_error)) {
                xtc_redirect(xtc_href_link(FILENAME_CONTENT, 'action=success&coID=' . (int) $_GET['coID'], 'SSL'));
            } else {
                $smarty->assign('error_message', $mail_error);
            }
        }
    }

    $smarty->assign('CONTACT_HEADING', $shop_content_data['content_title']);
    if (isset($_GET['action']) && ($_GET['action'] == 'success')) {
        $smarty->assign('success', '1');
        $smarty->assign('BUTTON_CONTINUE', '<a href="' . xtc_href_link(FILENAME_DEFAULT) . '">' . xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>');
    } else {
        if ($shop_content_data['content_file'] != '') {
            ob_start();
            if (strpos($shop_content_data['content_file'], '.txt'))
                echo '<pre>';
            include (DIR_FS_CATALOG . 'media/content/' . $shop_content_data['content_file']);
            if (strpos($shop_content_data['content_file'], '.txt'))
                echo '</pre>';
            $contact_content = ob_get_contents();
            ob_end_clean();
        } else {
            $contact_content = $shop_content_data['content_text'];
        }


        require_once (DIR_WS_INCLUDES . 'header.php');
        $smarty->assign('CONTACT_CONTENT', $contact_content);
        $smarty->assign('FORM_ACTION', xtc_draw_form('contact_us', xtc_href_link(FILENAME_CONTENT, 'action=send&coID=' . (int) $_GET['coID'], 'SSL')));

        function get_customer_mail($customer_id) {
            $customer_info = xtc_db_fetch_array(xtDBquery("SELECT customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . $customer_id."';"));
            return $customer_info['customers_email_address'];
        }

        if (isset($_SESSION['customer_id'])) {
            $name = $_SESSION['customer_first_name'] . ' ' . $_SESSION['customer_last_name'];
            $smarty->assign('INPUT_NAME', xtc_draw_input_field('name', $name));
            $smarty->assign('INPUT_EMAIL', xtc_draw_input_field('email', get_customer_mail((int) ($_SESSION['customer_id']))));
        } else {
            // Guest
            $name = ($error ? $_POST['name'] : $first_name);
            $smarty->assign('INPUT_NAME', xtc_draw_input_field('name', $name));
            $smarty->assign('INPUT_EMAIL', xtc_draw_input_field('email', ($error ? $_POST['email'] : $email_address)));
        }// else
        $smarty->assign('INPUT_TEXT', xtc_draw_textarea_field('message_body', 'soft', 50, 15, $_POST['message_body']));
        //Antispam beginn
        $antispam_query = xtc_db_fetch_array(xtDBquery("SELECT id, question FROM " . TABLE_CSEO_ANTISPAM . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY rand() LIMIT 1"));
        $smarty->assign('ANTISPAMCODEID', xtc_draw_hidden_field('antispamid', $antispam_query['id']));
        $smarty->assign('ANTISPAMCODEQUESTION', $antispam_query['question']);
        $smarty->assign('INPUT_ANTISPAMCODE', xtc_draw_input_field('codeanwser', '', 'size="6" maxlength="6"', 'text', false));
        $smarty->assign('ANTISPAMCODEACTIVE', ANTISPAM_CONTACT);
        //Antispam end
        $smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_SEND));
        $smarty->assign('DATENSG_checkbox', xtc_draw_checkbox_field('datensg', '0'));
        $smarty->assign('FORM_END', '</form>');
    }

    $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $smarty->assign('language', $_SESSION['language']);
    $smarty->caching = false;
    $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/contact_us.html', USE_TEMPLATE_DEVMODE));
} else {

    if ($shop_content_data['content_file'] != '') {
        ob_start();
        if (strpos($shop_content_data['content_file'], '.txt'))
            echo '<pre>';
        include (DIR_FS_CATALOG . 'media/content/' . $shop_content_data['content_file']);
        if (strpos($shop_content_data['content_file'], '.txt'))
            echo '</pre>';
        $smarty->assign('file', ob_get_contents());
        ob_end_clean();
    } else {
        $content_body = $shop_content_data['content_text'];
    }
	
	$content_body = preg_replace('/##(\w+)/', '<a href="'.xtc_href_link('hashtag/\1').'">#\1</a>', $content_body);
    $smarty->assign('CONTENT_BODY', $content_body);
    $smarty->assign('PDF_LINK', '<a href="' . xtc_href_link(FILENAME_PRINT_PDF, 'content=' . $_GET['coID']) . '">' . xtc_image('templates/' . CURRENT_TEMPLATE . '/img/button_pdf.png', IMAGE_BUTTON_PRINT_PDF) . '</a>');
    $smarty->assign('PRINT_LINK', '<a class="shipping" href="' . xtc_href_link('popup_content_print.php', 'coID=' . $_GET['coID']) . '">' . xtc_image('templates/' . CURRENT_TEMPLATE . '/img/button_print.png', IMAGE_BUTTON_PRINT_CONTENT) . '</a>');
    $smarty->assign('BUTTON_CONTINUE', '<a href="javascript:history.back(1)">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
    if (!CacheCheck()) {
        $smarty->caching = false;
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/content.html', USE_TEMPLATE_DEVMODE));
    } else {
        $smarty->caching = true;
        $smarty->cache_lifetime = CACHE_LIFETIME;
        $smarty->cache_modified_check = CACHE_CHECK;
        $cache_id = $_SESSION['language'] . $shop_content_data['content_id'] . 'shopcontent';
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/content.html', USE_TEMPLATE_DEVMODE), $cache_id);
    }
}

$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;
$smarty->loadFilter('output', 'note');
$smarty->loadFilter('output', 'trimwhitespace');
$smarty->assign('main_content', $main_content);

$slider_smarty = new smarty;
$slider_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$coo_slider = cseohookfactory::create_object('SliderManager');
$t_view_html = $coo_slider->proceed($coid, 'content');
if (is_array($t_view_html)) {
	foreach ($t_view_html AS $t_key => $t_value) {
		$slider_smarty->assign($t_key, $t_value);
	}
}

if (!CacheCheck()) {
	$slider_smarty->caching = false;
	$slider_content = $slider_smarty->fetch(cseo_get_usermod('base/module/slider_content.html', USE_TEMPLATE_DEVMODE));
} else {
	$slider_smarty->caching = true;
	$slider_smarty->cache_lifetime = CACHE_LIFETIME;
	$slider_smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'] . $_SESSION['currency'] . $_SESSION['customer_id'] . 'slider';
	$slider_content = $slider_smarty->fetch(cseo_get_usermod('base/module/slider_content.html', USE_TEMPLATE_DEVMODE), $cache_id);
}
$smarty->assign('slider_content', $slider_content);

$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));

include ('includes/application_bottom.php');
