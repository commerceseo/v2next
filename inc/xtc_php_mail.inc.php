<?php

/* -----------------------------------------------------------------
 * 	ID:						xtc_php_mail.inc.php
 * 	Letzter Stand:			v2.3
 * 	zuletzt geaendert von:	cseoak
 * 	Datum:					2012/11/19
 *
 * 	Copyright (c) since 2010 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

// include the mail classes
function xtc_php_mail($from_email_address, $from_email_name, $to_email_address, $to_name, $forwarding_to, $reply_address, $reply_address_name, $path_to_attachement, $path_to_more_attachements, $email_subject, $message_body_html, $message_body_plain, $order_mail = false) {
    global $mail_error;

    if (SEND_EMAILS != 'true')
        return;

    // require_once(DIR_WS_CLASSES . 'class.phpmailer.php');
    $mail = new PHPMailer();
    $mail->PluginDir = DIR_FS_DOCUMENT_ROOT . 'includes/classes/';

    if (isset($_SESSION['language_charset'])) {
        $mail->CharSet = $_SESSION['language_charset'];
    } else {
        $lang_query = "SELECT * FROM " . TABLE_LANGUAGES . " WHERE code = '" . DEFAULT_LANGUAGE . "'";
        $lang_query = xtc_db_query($lang_query);
        $lang_data = xtc_db_fetch_array($lang_query);
        $mail->CharSet = $lang_data['language_charset'];
    }
    if ($_SESSION['language'] == 'german') {
        $mail->SetLanguage("de", DIR_WS_CLASSES);
    } else {
        $mail->SetLanguage("en", DIR_WS_CLASSES);
    }
    if (EMAIL_TRANSPORT == 'smtp') {
        $mail->IsSMTP();
        $mail->SMTPKeepAlive = true; // set mailer to use SMTP
        $mail->SMTPAuth = SMTP_AUTH; // turn on SMTP authentication true/false
        $mail->Username = SMTP_USERNAME; // SMTP username
        $mail->Password = SMTP_PASSWORD; // SMTP password
        $mail->Host = SMTP_MAIN_SERVER . ';' . SMTP_Backup_Server; // specify main and backup server "smtp1.example.com;smtp2.example.com"
    }

    if (EMAIL_TRANSPORT == 'sendmail') { // set mailer to use SMTP
        $mail->IsSendmail();
        $mail->Sendmail = SENDMAIL_PATH;
    }

    if (EMAIL_TRANSPORT == 'mail') {
        $mail->IsMail();
    }

    if (EMAIL_USE_HTML == 'true') { // set email format to HTML
        $mail->IsHTML(true);
        $mail->Body = $message_body_html; //DPW Signatur
        // remove html tags
        $message_body_plain = str_replace('<br />', " \n", $message_body_plain); //DPW Signatur
        $message_body_plain = strip_tags($message_body_plain);
        $mail->AltBody = $message_body_plain;
    } else {
        $mail->IsHTML(false);
        //remove html tags
        $message_body_plain = str_replace('<br />', " \n", $message_body_plain); //DPW Signatur
        $message_body_plain = strip_tags($message_body_plain);
        $mail->Body = $message_body_plain;
    }

    $mail->From = $from_email_address;
    $mail->Sender = $from_email_address;
    $mail->FromName = $from_email_name;
    $mail->AddAddress($to_email_address, $to_name);
    if ($forwarding_to != '') {
        $mail->AddBCC($forwarding_to);
    }
    $mail->AddReplyTo($reply_address, $reply_address_name);

    $mail->WordWrap = 50; // set word wrap to 50 characters

    if ((PDF_IN_ODERMAIL == 'true') && (PDF_IN_ORDERMAIL_COID != '')) {
        require_once(DIR_FS_INC . 'cseo_get_url_friendly_text.inc.php');
        if ($order_mail) {
            $co = explode(',', PDF_IN_ORDERMAIL_COID);
            for ($i = 0, $n = sizeof($co); $i < $n; $i++) {
                $content_data = xtc_db_fetch_array(xtc_db_query("SELECT
												                     content_id,
												                     content_title,
												                     content_heading,
												                     content_text,
												                     content_file
												                 FROM
												                 	" . TABLE_CONTENT_MANAGER . "
												                 WHERE
												                 	content_group='" . $co[$i] . "'
												                 AND
												                 	languages_id='" . (int) $_SESSION['languages_id'] . "'"));

                $name = cseo_get_url_friendly_text($content_data['content_heading']);
                $name = str_replace(' ', '-', $name) . '.pdf';
				require_once(DIR_FS_CATALOG.'pdf/html_table.php');
				$pdf = new PDF_HTML_Table('P','mm','A4');
				$pdf->AddPage();
				$pdf->SetFont('Arial','U',12);
				$pdf->Cell(20,10,utf8_decode(html_entity_decode($content_data['content_heading'])));
				$pdf->Ln(20);
				$pdf->SetFont('Arial','',11);
				if($content_data['content_file'] !='') {
					ob_start();
					include (DIR_FS_CATALOG.'media/content/'.$content_data['content_file']);
					$text = stripslashes(html_entity_decode(ob_get_contents()));
					ob_end_clean();
				} else
					$text = stripslashes(html_entity_decode($content_data['content_text']));
				$text = utf8_decode(html_entity_decode($text));
				$pdf->WriteHTML($text);
				$pdftext = $pdf->Output('','S');
				$pdf = base64_decode($pdftext);
			  	$mail->AddStringAttachment($pdftext,$name,'base64','application/pdf');
            }
        }
    }

    // attachments
    if ($path_to_attachement != '') {
        $mail->AddAttachment($path_to_attachement); // add attachments
    }

    if ($path_to_more_attachements != '') {
        $mail->AddAttachment($path_to_more_attachements); // add more attachments
    }

    $mail->Subject = $email_subject;

    if (!$mail->Send()) {
        // echo "Die Mail konnte nicht versendet werden. <p>";
        // echo "Mailer Error: " . $mail->ErrorInfo;
        // exit;
		LogControl::get_instance()->notice($mail->ErrorInfo, 'error_handler', 'mail');
    }
}
