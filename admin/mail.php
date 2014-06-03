<?php
/* -----------------------------------------------------------------
 * 	$Id: mail.php 420 2013-06-19 18:04:39Z akausch $
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

require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.phpmailer.php');
require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

if (($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address']) && (!$_POST['back_x'])) {
    switch ($_POST['customers_email_address']) {
        case '***':
            $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address, customers_gender from " . TABLE_CUSTOMERS);
            $mail_sent_to = TEXT_ALL_CUSTOMERS;
            break;

        case '**D':
            $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address, customers_gender from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
            $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
            break;

        default:
            if (is_numeric($_POST['customers_email_address'])) {
                $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address, customers_gender from " . TABLE_CUSTOMERS . " where customers_status = " . $_POST['customers_email_address']);
                $sent_to_query = xtc_db_query("select customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . $_POST['customers_email_address'] . "' AND language_id='" . $_SESSION['languages_id'] . "'");
                $sent_to = xtc_db_fetch_array($sent_to_query);
                $mail_sent_to = $sent_to['customers_status_name'];
            } else {
                $customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);
                $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address, customers_gender from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($customers_email_address) . "'");
                $mail_sent_to = $_POST['customers_email_address'];
            }
            break;
    }

    $smarty = new Smarty;
    $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

    $from = xtc_db_prepare_input($_POST['from']);
    $subject = xtc_db_prepare_input($_POST['subject']);

    require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
    require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
    $mail_data = cseo_get_mail_data('send_mail_from_admin');

    while ($mail = xtc_db_fetch_array($mail_query)) {

        $html_mail = '';
        $txt_mail = '';

        $smarty->caching = false;

        $smarty->assign('CONTENT', $_POST['message']);

        $smarty->assign('GENDER', $mail['customers_gender']);
        $smarty->assign('VNAME', $mail['customers_firstname']);
        $smarty->assign('NNAME', $mail['customers_lastname']);

        $html_mail = $smarty->fetch('html:send_mail_from_admin');
        $html_mail .= $signatur_html;
        $txt_mail = $smarty->fetch('txt:send_mail_from_admin');
        $txt_mail .= $signatur_text;

        xtc_php_mail($mail_data['EMAIL_ADDRESS'], $from, $mail['customers_email_address'], $mail['customers_firstname'] . ' ' . $mail['customers_lastname'], '', $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], '', '', $subject, $html_mail, strip_tags($txt_mail));
    }

    xtc_redirect(xtc_href_link(FILENAME_MAIL, 'mail_sent_to=' . urlencode($mail_sent_to)));
}

if (($_GET['action'] == 'preview') && (!$_POST['customers_email_address'])) {
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
}

if ($_GET['mail_sent_to']) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'notice');
}

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="pageHeading">
                        Mail verschicken
                    </td>
                </tr>
            </table>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
if (($_GET['action'] == 'preview') && ($_POST['customers_email_address'])) {
    switch ($_POST['customers_email_address']) {
        case '***':
            $mail_sent_to = TEXT_ALL_CUSTOMERS;
            break;

        case '**D':
            $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
            break;

        default:
            if (is_numeric($_POST['customers_email_address'])) {
                echo "hier bin ich";
                $sent_to_query = xtc_db_query("select customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . $_POST['customers_email_address'] . "' AND language_id='" . $_SESSION['languages_id'] . "'");
                $sent_to = xtc_db_fetch_array($sent_to_query);
                $mail_sent_to = $sent_to['customers_status_name'];
            } else {
                $mail_sent_to = $_POST['customers_email_address'];
            }
            break;
    }
    ?>
                    <tr><?php echo xtc_draw_form('mail', FILENAME_MAIL, 'action=send_email_to_user'); ?>
                        <td>
                            <table border="0" width="100%" cellpadding="0" cellspacing="2">
                                <tr>
                                    <td class="smallText"><b><?php echo TEXT_CUSTOMER; ?></b><br /><?php echo $mail_sent_to; ?></td>
                                </tr>
                                <tr>
                                    <td class="smallText"><b><?php echo TEXT_FROM; ?></b><br /><?php echo htmlspecialchars(stripslashes($_POST['from'])); ?></td>
                                </tr>
                                <tr>
                                    <td class="smallText"><b><?php echo TEXT_SUBJECT; ?></b><br /><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
                                </tr>
                                <tr>
                                    <td class="smallText"><b><?php echo TEXT_MESSAGE; ?></b><br /><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
                                </tr>
                                <tr>
                                    <td><?php
                // Re-Post all POST'ed variables
                reset($_POST);
                while (list($key, $value) = each($_POST)) {
                    if (!is_array($_POST[$key])) {
                        echo xtc_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
                    }
                }
                ?>
                                        <table border="0" width="100%" cellpadding="0" cellspacing="2">
                                            <tr>
                                                <td><input type="submit" class="button" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')" value="<?php echo BUTTON_BACK; ?>" name="back"></td>
                                                <td align="right"><?php echo '<a class="button" href="' . xtc_href_link(FILENAME_MAIL) . '">' . BUTTON_CANCEL . '</a> <input type="submit" class="button" value="' . BUTTON_SEND_EMAIL . '">' ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        </form>
                    </tr>
<?php } else { ?>
                    <tr><?php echo xtc_draw_form('mail', FILENAME_MAIL, 'action=preview'); ?>
                        <td>
                            <table border="0" cellpadding="0" cellspacing="2">
    <?php
    $customers = array();
    $customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
    $customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
    $customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
    // Customers Status 1.x
    //    $customers_statuses_array = xtc_get_customers_statuses();
    $customers_statuses_array = xtc_db_query("select customers_status_id , customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE language_id='" . $_SESSION['languages_id'] . "' order by customers_status_name");
    while ($customers_statuses_value = xtc_db_fetch_array($customers_statuses_array)) {
        $customers[] = array('id' => $customers_statuses_value['customers_status_id'],
            'text' => $customers_statuses_value['customers_status_name']);
    }
    // End customers Status 1.x
    $mail_query = xtc_db_query("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
    while ($customers_values = xtc_db_fetch_array($mail_query)) {
        $customers[] = array('id' => $customers_values['customers_email_address'],
            'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
    }
    ?>
                                <tr>
                                    <td class="main"><?php echo TEXT_CUSTOMER; ?></td>
                                    <td class="main"><?php echo xtc_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']); ?></td>
                                </tr>
                                <tr>
                                    <td class="main"><?php echo TEXT_FROM; ?></td>
                                    <td class="main"><?php echo xtc_draw_input_field('from', EMAIL_FROM); ?></td>
                                </tr>
                                <tr>
                                    <td class="main"><?php echo TEXT_SUBJECT; ?></td>
                                    <td class="main"><?php echo xtc_draw_input_field('subject'); ?></td>
                                </tr>
                                <tr>
                                    <td valign="top"><?php echo TEXT_MESSAGE; ?></td>
									<script src="includes/editor/ckeditor/ckeditor.js" type="text/javascript"></script>
									<?php
									if (file_exists('includes/editor/ckfinder/ckfinder.js')) {
										echo '<script src="includes/editor/ckfinder/ckfinder.js" type="text/javascript"></script>';
									}
									?>
                                    <td>
										<?php echo xtc_draw_textarea_field('message', 'soft', '100', '35', '', 'class="ckeditor" name="editor1"'); ?>
									</td>
									<?php
									if (file_exists('includes/editor/ckfinder/ckfinder.js')) {
									?>	
										<script type="text/javascript">
											var newCKEdit = CKEDITOR.replace('<?php echo 'cont' ?>');
											CKFinder.setupCKEditor(newCKEdit, 'includes/editor/ckfinder/');
										</script>
									<?php
									}
									?>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right"><input type="submit" class="button" value="<?php echo BUTTON_SEND_EMAIL; ?>"></td>
                                </tr>
                            </table>
                        </td>
                        </form>
                    </tr>
<?php } ?>
            </table>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
