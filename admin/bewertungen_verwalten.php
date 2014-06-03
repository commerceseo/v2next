<?php
/**
 * Shopvoting for XT:Commerce SP2.1
 *
 * @license GPLv2
 * @author Hans-Peter Sausen
 * @copyright 2010 www.web4design.de
 * @version 1.1
 */
require_once ('includes/application_top.php');

include('../includes/classes/class.shopvoting.php');
$voting = new Shopvoting();

/**
 * Update voting status
 */
if ($_GET['action'] == 'statup') {
    $voting->setVotingStatus($_GET['status'], $_GET['sid']);
}
/**
 * Update voting and comment
 */
if ($_POST['modus'] == 'editupdate') {
    $voting->setAdminVoting();
}

/**
 * Single and multi delete / update
 */
if ($_POST['action']) {
    $voting->setUpdateDeleteVoting($_POST['action']);
} elseif ($_GET['action'] == "deletesingle") {
    $voting->setUpdateDeleteVoting($_GET['action'], $_GET['sid']);
}

require(DIR_WS_INCLUDES . 'header.php');
?>
<link rel="stylesheet" type="text/css" href="includes/shopbewertung.css" />
<script src="includes/javascript/checkall.js" type="text/javascript"></script>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
    </tr>
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
        <td style="vertical-align: top;" class="main">    
            <div id="bewertungsbox">
                <div id="topmenu">  
                    <?php
                    include_once ('../includes/modules/shopbewertung/admin/topmenu.php');
                    ?>
                </div>
                <div id="borderbox">
                    <form action="<?php echo xtc_href_link(Shopvoting::FILENAME_BEWERTUNGEN_VERWALTEN, 'page=' . $_GET['page']); ?>" method="post">         
                        <table width="852" cellpadding="4" cellspacing="0" class="maintab">
                            <tr>
                                <th width="15"><input onClick="check_all('bcheck[]', this)" type="checkbox"></th>
                                <th width="44"><?php echo NR; ?></th>
                                <th width="208"><?php echo DATUM; ?></th>
                                <th width="260"><?php echo USER; ?></th>                
                                <th width="35"><?php echo SHOP; ?></th>
                                <th width="40"><?php echo WARE; ?></th>                
                                <th width="35"><?php echo VERSAND; ?></th>                
                                <th width="35"><?php echo SERVICE; ?></th>                
                                <th width="35"><?php echo SEITE; ?></th>
                                <th width="40"><?php echo STATUS; ?></th>             
                                <th width="45"><?php echo KOMMENTAR; ?></th>           
                                <th width="30"><?php echo EDIT; ?></th>    
                                <th width="30"><?php echo DEL; ?></th>                       
                            </tr> 
                            <?php
                            $bewertung_query = $voting->getReviewSQL(false);
                            $reviews_split = new splitPageResults($_GET['page'], '10', $bewertung_query, $reviews_query_numrows);
                            $reviews_query = xtc_db_query($bewertung_query);
                            $i = 0;
                            while ($reviews = xtc_db_fetch_array($reviews_query)) {
                                ?>
                                <tr <?php if ($i == 1) {
                                echo 'class="cyclecolor"';
                            } ?>>
                                    <td><input type="checkbox" name="bcheck[]" value="<?php echo $reviews['bewertung_id']; ?>"></td>
                                    <td><?php echo $reviews['bewertung_id']; ?></td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($reviews['bewertung_datum'])); ?></td>
                                    <td><?php echo xtc_db_prepare_input($reviews['bewertung_vorname']) . ' ' . xtc_db_prepare_input($reviews['bewertung_nachname']); ?></td>                
                                    <td style="text-align: center;"><?php echo $reviews['bewertung_shoprating']; ?></td>
                                    <td style="text-align: center;"><?php echo $reviews['bewertung_ware']; ?></td>                
                                    <td style="text-align: center;"><?php echo $reviews['bewertung_versand']; ?></td>                
                                    <td style="text-align: center;"><?php echo $reviews['bewertung_service']; ?></td>                
                                    <td style="text-align: center;"><?php echo $reviews['bewertung_seite']; ?></td>
                                    <td>
                                        <?php if ($reviews['bewertung_status'] == 1) { ?>
                                            <img src="images/icon_status_green.gif" alt="<?php echo AKTIVIERT; ?>" title="<?php echo AKTIVIERT; ?>" />
                                            <a href="<?php echo xtc_href_link(Shopvoting::FILENAME_BEWERTUNGEN_VERWALTEN, 'page=' . $_GET['page'] . '&action=statup&status=0&sid=' . $reviews['bewertung_id']); ?>">
                                                <img src="images/icon_status_red_light.gif" alt="<?php echo DEAKTIVIEREN; ?>" title="<?php echo DEAKTIVIEREN; ?>" /></a>
                                        <?php } else { ?>            
                                            <a href="<?php echo xtc_href_link(Shopvoting::FILENAME_BEWERTUNGEN_VERWALTEN, 'page=' . $_GET['page'] . '&action=statup&status=1&sid=' . $reviews['bewertung_id']); ?>">
                                                <img src="images/icon_status_green_light.gif" alt="<?php echo AKTIVIEREN; ?>" title="<?php echo AKTIVIEREN; ?>" /></a>
                                            <img src="images/icon_status_red.gif" alt="<?php echo DEAKTIVIERT; ?>" title="<?php echo DEAKTIVIERT; ?>" />
                                        <?php } ?>    
                                    </td>             
                                    <td>
                                        <a href="<?php echo xtc_href_link(Shopvoting::FILENAME_BEWERTUNGEN_VERWALTEN, 'page=' . $_GET['page'] . '&action=vorschau&sid=' . $reviews['bewertung_id']); ?>" style=" color: #c1841b;	font-weight: bold;"><?php echo VORSCHAU; ?></a>             </td>           
                                    <td>
                                        <a href="<?php echo xtc_href_link(Shopvoting::FILENAME_BEWERTUNGEN_VERWALTEN, 'page=' . $_GET['page'] . '&action=edit&sid=' . $reviews['bewertung_id']); ?>">
                                            <img src="images/icons/icon_edit.gif" alt="<?php echo EDIT; ?>" title="<?php echo EDIT; ?>" /></a>
                                    </td>    
                                    <td>

                                        <a href="<?php echo xtc_href_link(Shopvoting::FILENAME_BEWERTUNGEN_VERWALTEN, 'page=' . $_GET['page'] . '&action=deletesingle&sid=' . $reviews['bewertung_id']); ?>">
                                            <img src="images/icons/icon_delete.png" alt="<?php echo DELETE; ?>" title="<?php echo DELETE; ?>" /></a>

                                    </td>   
                                </tr>      
                                <tr <?php if ($i == 0) {
                                        echo 'class="cyclecolor"';
                                    } ?>>
                                    <td class="bottomborder">&nbsp;</td>
                                    <td colspan="3" class="bottomborder">
                                        <strong>E-Mail:</strong> <?php echo xtc_db_prepare_input($reviews['bewertungs_email']); ?><br />
                                        <strong><?php echo BESTELLNUMMER; ?>:</strong>
                                        <?php
                                        if ($reviews['orders_id'] > 0) {
                                            ?>
                                            <a href="<?php echo xtc_href_link('orders.php', 'oID=' . xtc_db_prepare_input($reviews['orders_id'] . '&action=edit')); ?>">
                                            <?php echo xtc_db_prepare_input($reviews['orders_id']); ?></a>
                                            <?php
                                        } else {
                                            ?>
                                            ---
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td colspan="4" class="bottomborder"><strong>IP:</strong> <?php echo $reviews['bewertungs_ip']; ?></td>
                                    <td colspan="5" class="bottomborder" style="text-align: right; padding-right: 14px;">
                                        <strong><?php echo LANGUAGE; ?></strong>
                                        <img src="../lang/<?php echo $voting->getLanguageFlag($reviews['bewertung_sprache']); ?>/icon.gif" alt="<?php echo $voting->getLanguageFlag($reviews['bewertung_sprache']); ?>" title="<?php echo $voting->getLanguageFlag($reviews['bewertung_sprache']); ?>" />
                                    </td>
                                </tr> 
                                <?php
                                if ($_GET['action'] == 'edit' && $reviews['bewertung_id'] == $_GET['sid']) {
                                    ?>
                                    <tr <?php if ($i == 0) {
                                echo 'class="cyclecolor"';
                            } ?>>
                                        <td colspan="3"><strong><?php echo BEARBEITEN; ?></strong></td>
                                        <td>&nbsp;</td>
                                        <td>
                                            <select name="dropshopb">
        <?php echo $voting->getDropdownSelected($reviews['bewertung_shoprating']); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="dropwareb">
        <?php echo $voting->getDropdownSelected($reviews['bewertung_ware']); ?>    
                                            </select>
                                        </td>
                                        <td>
                                            <select name="dropversandb">
        <?php echo $voting->getDropdownSelected($reviews['bewertung_versand']); ?>   
                                            </select>    
                                        </td>    
                                        <td>
                                            <select name="dropserviceb">
        <?php echo $voting->getDropdownSelected($reviews['bewertung_service']); ?>       
                                            </select>
                                        </td>
                                        <td>
                                            <select name="dropseiteb">
        <?php echo $voting->getDropdownSelected($reviews['bewertung_seite']); ?>          
                                            </select>
                                        </td>    
                                        <td colspan="4">
                                            <input type="hidden" name="modus" value="editupdate">
                                            <input type="hidden" name="sid" value="<?php echo $reviews['bewertung_id']; ?>">
                                            <input type="submit" name="" value="Aktualisieren" />  
                                        </td>
                                    </tr>
                                    <tr <?php if ($i == 0) {
            echo 'class="cyclecolor"';
        } ?>>
                                        <td colspan="13">
                                            <div style="float: left; width: 760px; overflow: hidden; clear:both">
                                                <strong><?php echo ADMINDATE; ?></strong><br />
                                                <input type="text" name="admindatum" value="<?php echo xtc_db_prepare_input($reviews['bewertung_datum']); ?>">
                                            </div>
                                            <div style="float: left; width: 400px; overflow: hidden;">
                                                <strong><?php echo USERCOMMENT; ?></strong><br />
                                                <textarea name="bewertungstext" id="bewertungstext" cols="55" rows="6"><?php echo xtc_db_prepare_input($reviews['bewertung_text']); ?></textarea>
                                            </div>
                                            <div style="float: left; width: 360px; overflow: hidden;">
                                                <strong><?php echo ADMINCOMMENT; ?></strong><br />
                                                <textarea name="adminkommentar" id="adminkommentar" cols="55" rows="6"><?php echo xtc_db_prepare_input($reviews['bewertung_kommentar']); ?></textarea>
                                            </div>
                                            <p class="clr">&nbsp;</p>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                if ($_GET['action'] == 'vorschau' && $reviews['bewertung_id'] == $_GET['sid']) {
                                    ?>
                                    <tr <?php if ($i == 0) {
                                        echo 'class="cyclecolor"';
                                    } ?>>
                                        <td colspan="9"><strong><?php echo VORSCHAU; ?></strong></td>                                       
                                        <td colspan="4">&nbsp;</td>
                                    </tr>
                                    <tr <?php if ($i == 0) {
                                        echo 'class="cyclecolor"';
                                    } ?>>
                                        <td colspan="13">           
                                            <div style="float: left; width: 400px; overflow: hidden;">
                                                <strong><?php echo USERCOMMENT; ?></strong><br />
                                                <textarea name="bewertungstext" id="bewertungstext" cols="55" rows="6" readonly><?php echo xtc_db_prepare_input($reviews['bewertung_text']); ?></textarea>
                                            </div>
                                            <div style="float: left; width: 360px; overflow: hidden;">
                                                <strong><?php echo ADMINCOMMENT; ?></strong><br />
                                                <textarea name="adminkommentar" id="adminkommentar" cols="55" rows="6" readonly><?php echo xtc_db_prepare_input($reviews['bewertung_kommentar']); ?></textarea>
                                            </div>
                                            <p class="clr">&nbsp;</p>            
                                        </td>
                                    </tr>
        <?php
    }
    ?>           
    <?php
}
?>
                        </table>
                        <p class="clr">&nbsp;</p>
                        <div class="leftupdatebox">
                            <select name="action" size="1">
                                <option value=""><?php echo AKTION; ?></option>
                                <option value="aktivieren"><?php echo AKTIVIEREN; ?></option>
                                <option value="deaktivieren"><?php echo DEAKTIVIEREN; ?></option>
                                <option value="delete"><?php echo DELETE; ?></option>
                            </select>
                            <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
                            <input type="submit" name="" value="<?php echo AKTUALISIEREN; ?>" />  
                        </div>
                    </form> 
                    <div class="rightpagebox">
<?php echo $reviews_split->display_count($reviews_query_numrows, '10', $_GET['page'], ANZAHL_VON_BIS); ?>&nbsp;&nbsp;
                    <?php echo $reviews_split->display_links($reviews_query_numrows, '10', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
                    </div>
                    <p class="clr">&nbsp;</p>
                    </td>
                    </table>
                    </td>
                    </tr>
                    </table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
