<?php
/* -----------------------------------------------------------------
 * 	$Id: emails.php 723 2013-11-07 18:10:28Z akausch $
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

$query = xtc_db_query("SELECT code FROM " . TABLE_LANGUAGES . " WHERE languages_id='" . $_SESSION['languages_id'] . "'");
$data = xtc_db_fetch_array($query);
$languages = xtc_get_languages();

if (isset($_GET['action']) && $_GET['action'] == 'save_mail') {
    if (!empty($_POST['email_content']) || $_POST['mail'] == 'contact') {

        $sql_array = array('email_address' => xtc_db_prepare_input($_POST['email_address']),
            'email_address_name' => xtc_db_prepare_input($_POST['email_address_name']),
            'email_replay_address' => xtc_db_prepare_input($_POST['email_replay_address']),
            'email_replay_address_name' => xtc_db_prepare_input($_POST['email_replay_address_name']),
            'email_subject' => xtc_db_prepare_input($_POST['email_subject']),
            'email_forward' => xtc_db_prepare_input($_POST['email_forward']),
            (($_POST['type'] == 'html') ? 'email_content_html' : 'email_content_text') => xtc_db_prepare_input($_POST['email_content']));

        xtc_db_perform('emails', $sql_array, 'update', 'email_name = \'' . xtc_db_input($_POST['mail']) . '\' AND languages_id = \'' . xtc_db_input($_POST['lang']) . '\'');
        $messageStack->add_session('Emaildaten wurden erfolgreich gespeichert.', 'success');
        xtc_redirect(xtc_href_link('emails.php'));
    } else {
        $messageStack->add_session('Der Mailinhalt darf nicht leer sein!', 'error');
        xtc_redirect(xtc_href_link('emails.php'));
    }
} elseif ($_GET['action'] == 'reset_mail') {
    xtc_db_query("UPDATE emails SET email_content_html = email_backup_html WHERE email_name = '" . $_GET['mail'] . "' AND languages_id = '" . (int) $_GET['lang'] . "'");
    xtc_db_query("UPDATE emails SET email_content_text = email_backup_text WHERE email_name = '" . $_GET['mail'] . "' AND languages_id = '" . (int) $_GET['lang'] . "'");

    $messageStack->add_session('Die Sicherung wurde erfolgreich zur&uuml;ckgespielt.', 'success');
    xtc_redirect(xtc_href_link('emails.php', 'action=edit_mail&mail=' . $_GET['mail'] . '&lang=' . (int) $_GET['lang'] . '&type=html'));
}

require(DIR_WS_INCLUDES . 'header.php');
?>
<script src="includes/javascript/code_editor/codemirror.js" type="text/javascript"></script>
<script src="includes/javascript/code_editor/xml.js" type="text/javascript"></script>
<script src="includes/javascript/code_editor/javascript.js" type="text/javascript"></script>
<script src="includes/javascript/code_editor/css.js" type="text/javascript"></script>
<script src="includes/javascript/code_editor/htmlmixed.js" type="text/javascript"></script>
<link rel="stylesheet" href="includes/javascript/code_editor/codemirror.css" type="text/css" />
<style type=text/css>
    iframe {
        width: 95%;
        height: 300px;
        border: 1px solid black;
    }
</style>


<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td>
                        <table class="table_pageHeading" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
                            <tr class="dataTableHeadingRow">
                                <th class="dataTableHeadingContent" width="20%">Email</th>
                                <th class="dataTableHeadingContent" colspan="<?php echo sizeof($languages) * 4; ?>">Aktion</th>
                            </tr>
<?php
$mail_list = xtc_db_query("SELECT email_name FROM emails WHERE languages_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY email_name;");
while ($mail = xtc_db_fetch_array($mail_list)) {
    $rows++;
    $td .= '<tr class="' . (($rows % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '">' . "\n";
    $td .= '<td>' . constant(strtoupper($mail['email_name'] . '_TITLE')) . '</td>';
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $td .= '<td width="10"><img src="../lang/' . $languages[$i]['directory'] . '/icon.gif" alt="' . $languages[$i]['name'] . '" /></td>';
        $td .= '<td width="10">
					<a href="' . xtc_href_link('emails.php', 'action=edit_mail&mail=' . $mail['email_name'] . '&lang=' . $languages[$i]['id'] . '&type=html#bearbeiten') . '">
						' . xtc_image('images/icons/icon_page_white_code.gif', 'HTML Mail bearbeiten') . '
					</a>
				</td>';
        $td .= '<td width="10">
					<a href="' . xtc_href_link('emails.php', 'action=edit_mail&mail=' . $mail['email_name'] . '&lang=' . $languages[$i]['id'] . '&type=text#bearbeiten') . '">
						' . xtc_image('images/icons/icon_page_white_text.gif', 'Text Mail bearbeiten') . '
					</a>
				</td>';
        $td .= '<td><a href="' . xtc_href_link('emails.php', 'action=reset_mail&mail=' . $mail['email_name'] . '&lang=' . $languages[$i]['id']) . '">' . xtc_image('images/icons/icon_database_go.gif', 'Originale Mail wiederherstellen') . '</a></td>';
    }
    $td .= '</tr>';
}
echo $td;
?>
                        </table>
                            <?php
                            if (isset($_GET['action']) && $_GET['action'] == 'edit_mail') {
                                $mail_edit_query = xtc_db_query("SELECT * FROM emails WHERE languages_id = '" . (int) $_GET['lang'] . "' AND email_name = '" . $_GET['mail'] . "' ");
                                $mail_edit = xtc_db_fetch_array($mail_edit_query);
                                ?>

                            <br />
                            <table class="table_pageHeading" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pageHeading"><?php echo constant(strtoupper($mail_edit['email_name'] . '_TITLE')) ?> bearbeiten <a name="bearbeiten"></a></td>
                                </tr>
                            </table>
    <?php echo xtc_draw_form('save_mail', 'emails.php', 'action=save_mail', 'post', ''); ?>
                            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                <tr>
                                    <td class="main" width="1"><nobr>Email Adresse:</nobr></td>
                        <td class="main"><?php echo xtc_draw_input_field('email_address', $mail_edit['email_address'], 'size="30"') ?></td>
                    </tr>
                    <tr>
                        <td class="main"><nobr>Email Adresse Name:</nobr></td>
            <td class="main"><?php echo xtc_draw_input_field('email_address_name', $mail_edit['email_address_name'], 'size="30"') ?></td>
        </tr>
        <tr>
            <td class="main"><nobr>Email Antwort Adresse:</nobr></td>
    <td class="main"><?php echo xtc_draw_input_field('email_replay_address', $mail_edit['email_replay_address'], 'size="30"') ?></td>
    </tr>
    <tr>
        <td class="main"><nobr>Email Antwort Adresse Name:</nobr></td>
    <td class="main"><?php echo xtc_draw_input_field('email_replay_address_name', $mail_edit['email_replay_address_name'], 'size="30"') ?></td>
    </tr>
    <tr>
        <td class="main"><nobr>Email Betreff:</nobr></td>
    <td class="main"><?php echo xtc_draw_input_field('email_subject', $mail_edit['email_subject'], 'size="30"') ?></td>
    </tr>
    <tr>
        <td class="main"><nobr>Email Kopie:</nobr></td>
    <td class="main"><?php echo xtc_draw_input_field('email_forward', $mail_edit['email_forward'], 'size="30"') ?></td>
    </tr>

    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="left" valign="top">
            <div style="border: 1px solid #ccc">
    <?php if (isset($_GET['type']) && $_GET['type'] == 'text') { ?>
                    <textarea id="email_content" rows="40" cols="50" style="width:99%" name="email_content"><?php echo $mail_edit['email_content_text']; ?></textarea>
                    <?php echo xtc_draw_hidden_field('type', 'text');
                } else {
                    ?>

                    <textarea id="email_content" rows="40" cols="50" style="width:99%" name="email_content"><?php echo $mail_edit['email_content_html']; ?></textarea>
                    <?php
                    echo xtc_draw_hidden_field('type', 'html');
                }
                echo xtc_draw_hidden_field('lang', $_GET['lang']);
                echo xtc_draw_hidden_field('mail', $_GET['mail']);
                ?>
            </div>
            <input type="submit" class="button" value="<?php echo BUTTON_SAVE; ?>" />
            <a href="<?php echo xtc_href_link('emails.php'); ?>" class="button"><?php echo BUTTON_CANCEL; ?></a>
            <script>
                var delay;
                var editor = CodeMirror.fromTextArea(document.getElementById('email_content'), {
                    mode: 'text/html',
                    tabMode: 'indent',
                    lineNumbers: true,
                });
                editor.on("change", function() {
                    clearTimeout(delay);
                    delay = setTimeout(updatePreview, 300);
                });

                function updatePreview() {
                    var previewFrame = document.getElementById('preview');
                    var preview = previewFrame.contentDocument || previewFrame.contentWindow.document;
                    preview.open();
                    preview.write(editor.getValue());
                    preview.close();
                }
                setTimeout(updatePreview, 300);
            </script>
        </td>
        <td align="right" valign="top">
            <iframe id=preview></iframe>
        </td>
    </tr>
    </table>
    </form>
<?php } ?>
</td>
</tr>
</table>
</td>
</tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
