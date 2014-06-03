<?php
/**
 * Shopvoting for XT:Commerce SP2.1
 *
 * @license GPLv2
 * @author Hans-Peter Sausen
 * @copyright 2010 www.web4design.de
 * @version 1.1
 */
require ('includes/application_top.php');

include('../includes/classes/class.shopvoting.php');
$voting = new Shopvoting();

require(DIR_WS_INCLUDES . 'header.php');
?>
<link rel="stylesheet" type="text/css" href="includes/shopbewertung.css" />
<script src="includes/javascript/checkall.js" type="text/javascript"></script>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo TITLE_BEWERTUNGEN; ?></td>
                            </tr>
                            <tr>
                                <td class="main" valign="top"><?php echo SUB_TITLE_BEWERTUNGEN; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td> 
                        <div id="bewertungsbox">
                            <div id="topmenu">  
                                <?php
                                include_once ('../includes/modules/shopbewertung/admin/topmenu.php');
                                ?>
                            </div>
                            <div id="borderbox">
                                <form action="<?php echo xtc_href_link(Shopvoting::FILENAME_BEWERTUNGEN_VERWALTEN_CONFIG); ?>" method="post"> 
                                    <table width="850" cellpadding="0" cellspacing="0" class="maintab" border="0">
                                        <tr>
                                            <td colspan="2">
                                                <?php
                                                if ($_POST) {
                                                    $voting->setAdminConfig();
                                                    $voting->__construct();
                                                    ?>
                                                    <div class="speichern"><?php echo AKTUALISIERT; ?></div>
                                                <?php } ?>  
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: top;" width="410">

                                                <table cellpadding="0" cellspacing="0" width="410" class="settingtab">
                                                    <tr>
                                                        <td colspan="3" style="text-align: center; background: #efefef;"><strong>Shopbewertungen (wer darf was?)</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="borderline"><strong><?php echo LESEN; ?></strong></td>
                                                        <td class="borderline"><strong><?php echo SCHREIBEN; ?></strong></td>
                                                        <td class="borderline"><strong><?php echo CAPTCHA; ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="borderline" width="130">
<?php
echo $voting->getCheckboxGroup('kdlesen[]', $voting->customer_group_read);
?>
                                                        </td>
                                                        <td class="borderline" width="130">
<?php
echo $voting->getCheckboxGroup('kdschreiben[]', $voting->customer_group_write);
?>
                                                        </td>
                                                        <td class="borderline" width="130">
<?php
echo $voting->getCheckboxGroup('kdcaptcha[]', $voting->customer_group_captcha);
?>
                                                        </td>
                                                    </tr>
                                                </table>  

                                                <table cellpadding="0" cellspacing="0" width="410" class="settingtab" style="margin-top: 15px;">
                                                    <tr>
                                                        <td style="text-align: center; background: #efefef;" colspan="2"><strong><?php echo EINSTELLUNGEN_BACKEND; ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="borderline" width="180">

<?php echo WERTUNGSMAIL_TAGE; ?>
                                                        </td>
                                                        <td class="borderline" width="230">
                                                            <select name="email_send" size="1">
                                                                <option value="0" <?php if ($voting->send_admin_email == '0') echo 'selected="selected"'; ?>><?php echo DROPDOWN_NEIN; ?></option>
                                                                <option value="1" <?php if ($voting->send_admin_email == '1') echo 'selected="selected"'; ?>><?php echo DROPDOWN_JA; ?></option>
                                                            </select>  
                                                            <input type="text" name="email" maxlength="96" size="24" value="<?php echo $voting->admin_email; ?>">
                                                        </td>
                                                </table>    
                                            </td>
                                            <td style="vertical-align: top; padding-left: 20px;" cellpadding="0" cellspacing="0" width="410">

                                                <table cellpadding="0" cellspacing="0" class="settingtab" width="410">
                                                    <tr>
                                                        <td style="text-align: center; background: #efefef;" colspan="2"><strong><?php echo EINSTELLUNGEN_FRONTEND; ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="borderline" width="205">
<?php echo AKTIVSTATUS; ?>
                                                        </td>
                                                        <td width="195" class="borderline">
                                                            <select name="bewertung_aktiv" size="1">
                                                                <option value="0" <?php if ($voting->voting_module_aktive == '0') echo 'selected="selected"'; ?>><?php echo DROPDOWN_NEIN; ?></option>
                                                                <option value="1" <?php if ($voting->voting_module_aktive == '1') echo 'selected="selected"'; ?>><?php echo DROPDOWN_JA; ?></option>
                                                            </select>  
                                                        </td>
                                                    </tr>  
                                                    <tr>
                                                        <td class="borderline"><?php echo EINTRAGE_PRO_SEITE; ?></td>
                                                        <td class="borderline">
                                                            <input type="text" name="pro_seite" maxlength="3" size="3" value="<?php echo $voting->entry_per_page_frontend; ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="borderline"><?php echo ANZAHL_ZEICHEN; ?></td>
                                                        <td class="borderline">
                                                            <input type="text" name="front_page_character" maxlength="5" size="5" value="<?php echo $voting->front_page_character; ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="borderline"><?php echo PFLICHTFELDNAME; ?></td>
                                                        <td class="borderline">
                                                            <select name="required_name" size="1">
                                                                <option value="0" <?php if ($voting->required_name == '0') echo 'selected="selected"'; ?>><?php echo DROPDOWN_NEIN; ?></option>
                                                                <option value="1" <?php if ($voting->required_name == '1') echo 'selected="selected"'; ?>><?php echo DROPDOWN_JA; ?></option>
                                                            </select>  
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td class="borderline"><?php echo PFLICHTFELDKOMMENTAR; ?></td>
                                                        <td class="borderline">
                                                            <select name="required_comment" size="1">
                                                                <option value="0" <?php if ($voting->required_comment == '0') echo 'selected="selected"'; ?>><?php echo DROPDOWN_NEIN; ?></option>
                                                                <option value="1" <?php if ($voting->required_comment == '1') echo 'selected="selected"'; ?>><?php echo DROPDOWN_JA; ?></option>
                                                            </select>  
                                                        </td>
                                                    </tr>  
                                                    <tr>
                                                        <td class="borderline"><?php echo FREISCHALTEN; ?></td>
                                                        <td class="borderline">
                                                            <select name="activate_votings" size="1">
                                                                <option value="0" <?php if ($voting->activate_votings == '0') echo 'selected="selected"'; ?>><?php echo DROPDOWN_NEIN; ?></option>
                                                                <option value="1" <?php if ($voting->activate_votings == '1') echo 'selected="selected"'; ?>><?php echo DROPDOWN_JA; ?></option>
                                                            </select>  
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td class="borderline"><?php echo PFLICHTFELDORDERSID; ?></td>
                                                        <td class="borderline">
                                                            <select name="required_order_id" size="1">
                                                                <option value="0" <?php if ($voting->required_order_id == '0') echo 'selected="selected"'; ?>><?php echo DROPDOWN_NEIN; ?></option>
                                                                <option value="1" <?php if ($voting->required_order_id == '1') echo 'selected="selected"'; ?>><?php echo DROPDOWN_JA; ?></option>
                                                            </select>  
                                                        </td>
                                                    </tr>  
                                                    <tr>
                                                        <td class="borderline"><?php echo PFLICHTFELDORDERSIDMAIL; ?></td>
                                                        <td class="borderline">
                                                            <select name="required_order_id_email" size="1">
                                                                <option value="0" <?php if ($voting->required_order_id_email == '0') echo 'selected="selected"'; ?>><?php echo DROPDOWN_NEIN; ?></option>
                                                                <option value="1" <?php if ($voting->required_order_id_email == '1') echo 'selected="selected"'; ?>><?php echo DROPDOWN_JA; ?></option>
                                                            </select>  
                                                        </td>
                                                    </tr>    
                                                </table>    




                                            </td>
                                        </tr>
                                    </table>
                                    <div style="padding: 5px 15px 0 0; margin: 15px 0 0 0; text-align: right; border-top: 1px solid #cccccc;">
<?php echo SPEICHERN; ?><br /><br /> <input type="submit" name="" value="<?php echo SAVE_SETTINGS; ?>" /> 
                                    </div>
                                </form>
                                <p class="clr">&nbsp;</p>
                            </div>
                    </td>
            </table>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
