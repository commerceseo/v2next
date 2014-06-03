<?php
/* -----------------------------------------------------------------
 * 	$Id: cseo_logo.php 943 2014-04-08 13:26:37Z akausch $
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
$coo_text_mgr = new LanguageTextManager('logomanager', $_SESSION['languages_id']);
$smarty = new Smarty;
$smarty->assign('txt', $coo_text_mgr->v_section_content_array['logomanager']);
$pdf_query = xtc_db_fetch_array(xtc_db_query("SELECT * FROM orders_pdf_profile WHERE type = 'layout' AND pdf_key = 'LAYOUT_LOGO_FILE';"));
$logo_query = xtc_db_fetch_array(xtc_db_query("SELECT cseo_value FROM cseo_configuration WHERE cseo_key = 'CSEO_LOGO';"));

if ($_GET['action']) {
    switch ($_GET['action']) {
        case 'insert_logo':
			$logo_image = xtc_try_upload('logo_image', DIR_FS_CATALOG . 'cache/');
			// if ($logo_image->filename == 'logo.png') {
				rename(DIR_FS_CATALOG . 'cache/' . $logo_image->filename, DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/' . $logo_image->filename);
				@unlink(DIR_FS_CATALOG . 'cache/' . $logo_image->filename);
				$messageStack->add_session(SUCCESS_LOGO_SHOP_INSERT, 'success');
				xtc_db_query("UPDATE cseo_configuration SET cseo_value = '$logo_image->filename' WHERE cseo_key = 'CSEO_LOGO';");
			// } else {
				// @unlink(DIR_FS_CATALOG . 'cache/' . $logo_image->filename);
				// $messageStack->add_session(ERROR_LOGO_SHOP_ERROR, 'error');
			// }
			xtc_redirect(xtc_href_link('cseo_logo.php'));
            break;
        case 'insert_logo_mail':
			$logo_image = xtc_try_upload('logo_image_mail', DIR_FS_CATALOG . 'cache/');
			if ($logo_image->filename == 'logo.gif') {
				rename(DIR_FS_CATALOG . 'cache/' . $logo_image->filename, DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/' . $logo_image->filename);
				@unlink(DIR_FS_CATALOG . 'cache/' . $logo_image->filename);
				$messageStack->add_session(SUCCESS_LOGO_MAIL_INSERT, 'success');

			} else {
				@unlink(DIR_FS_CATALOG . 'cache/' . $logo_image->filename);
				$messageStack->add_session(ERROR_LOGO_MAIL_ERROR, 'error');
			}
			xtc_redirect(xtc_href_link('cseo_logo.php'));
            break;
        case 'insert_logo_pdf':
			$logo_image = xtc_try_upload('logo_image_pdf', DIR_FS_CATALOG . 'cache/');
			if ($logo_image->filename == $pdf_query['pdf_value']) {
				rename(DIR_FS_CATALOG . 'cache/' . $logo_image->filename, DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/' . $logo_image->filename);
				@unlink(DIR_FS_CATALOG . 'cache/' . $logo_image->filename);
				$messageStack->add_session(SUCCESS_LOGO_PDF_INSERT, 'success');

			} else {
				@unlink(DIR_FS_CATALOG . 'cache/' . $logo_image->filename);
				$messageStack->add_session(ERROR_LOGO_PDF_ERROR, 'error');
			}
			xtc_redirect(xtc_href_link('cseo_logo.php'));
            break;
    }
}
require(DIR_WS_INCLUDES . 'header.php');

$smarty->assign('LOGO_FORM', '<form enctype="multipart/form-data" method="post" action="'.xtc_href_link('cseo_logo.php', 'action=insert_logo').'">');
$smarty->assign('FORM_END', '</form>');
$smarty->assign('BUTTON_SUBMIT', xtc_button(BUTTON_INSERT, 'submit'));
$smarty->assign('LOGO_FILE', xtc_draw_file_field('logo_image'));
$smarty->assign('LOGO_IMAGE', '<img src="'.DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/'.$logo_query['cseo_value'].'" />');
$smarty->assign('LOGO_NAME', 'templates/' . CURRENT_TEMPLATE . '/img/'.$logo_query['cseo_value']);

$smarty->assign('MAIL_FORM', '<form enctype="multipart/form-data" method="post" action="'.xtc_href_link('cseo_logo.php', 'action=insert_logo_mail').'">');
$smarty->assign('MAIL_FILE', xtc_draw_file_field('logo_image_mail'));
$smarty->assign('MAIL_IMAGE', '<img src="'.DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/logo.gif" />');
$smarty->assign('MAIL_NAME', 'templates/' . CURRENT_TEMPLATE . '/img/logo.gif');

$smarty->assign('PDF_FORM', '<form enctype="multipart/form-data" method="post" action="'.xtc_href_link('cseo_logo.php', 'action=insert_logo_pdf').'">');
$smarty->assign('PDF_FILE', xtc_draw_file_field('logo_image_pdf'));
$smarty->assign('PDF_IMAGE', '<img src="'.DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/' . $pdf_query['pdf_value'] .'" />');
$smarty->assign('PDF_NAME', CURRENT_TEMPLATE . '/img/' . $pdf_query['pdf_value']);

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
$smarty->caching = false;
$smarty->template_dir = DIR_FS_CATALOG . 'admin/templates/';
$smarty->compile_dir = DIR_FS_CATALOG . 'admin/templates_c';
$smarty->display(CURRENT_ADMIN_TEMPLATE . '/cseo_logo.html');


require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
