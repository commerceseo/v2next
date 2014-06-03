<?php
/* -----------------------------------------------------------------
 * 	$Id: cseo_language_button.php 420 2013-06-19 18:04:39Z akausch $
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

$languages = xtc_get_languages();

if (isset($_GET['l']) && isset($_GET['id'])) {
    xtc_db_query("DELETE FROM " . TABLE_CSEO_LANG_BUTTON . " WHERE id = '" . $_GET['id'] . "' AND language_id = '" . $_GET['l'] . "' ");
    xtc_redirect(FILENAME_CSEO_LANGUAGE_BUTTON . '#language_' . $_GET['l']);
}

if (isset($_POST['action']) && ($_POST['action'] == 'save' )) {

    $text_post = xtc_db_prepare_input($_POST['buttontext']);

    if ($_POST['button'][$_POST['l']] != '')
        xtc_db_query("INSERT INTO " . TABLE_CSEO_LANG_BUTTON . " VALUES (NULL, '" . trim($_POST['button'][$_POST['l']]) . "', '" . trim($_POST['buttontext'][$_POST['l']]) . "', '" . $_POST['l'] . "') ");

    xtc_redirect(FILENAME_CSEO_LANGUAGE_BUTTON . '#language_' . $_POST['l']);
}

if (isset($_POST['action']) && ($_POST['action'] == 'update' )) {

    $text_post = xtc_db_prepare_input($_POST['buttontext']);
    foreach ($text_post AS $lang => $tmp)
        foreach ($tmp AS $id => $text)
            xtc_db_query("UPDATE " . TABLE_CSEO_LANG_BUTTON . " SET buttontext = '" . $text . "' WHERE id = '" . $id . "' AND language_id = '" . $lang . "' ");

    xtc_redirect(FILENAME_CSEO_LANGUAGE_BUTTON . '#language_' . $_POST['l']);
}

require(DIR_WS_INCLUDES . 'header.php');
?>
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="100%" valign="top">
            <table border="0" width="100%" cellspacing="2" cellpadding="2">
                <tr>
                    <td class="boxCenter" width="100%" valign="top">
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td colspan="3">
                                    <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="pageHeading">
                                                <?php echo HEADING_TITLE; ?>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php echo CSEO_BUTTON_HINWEIS; ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <div id="tabslang">
                                        <ul>
                                            <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                                                <li><a href="#language_<?php echo $languages[$i]['id']; ?>">
                                                        <span> <?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . ' ' . $languages[$i]['name']; ?></span>
                                                    </a></li>
                                            <?php } ?>
                                        </ul>

                                        <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                                            <div id="language_<?php echo $languages[$i]['id']; ?>">
                                                <?php echo xtc_draw_form('cseo_lang_button', FILENAME_CSEO_LANGUAGE_BUTTON, '', 'post', ''); ?>
                                                <table class="dataTable" width="100%">
                                                    <tr class="dataTableHeadingRow">
                                                        <th class="dataTableHeadingContent" height="20" width="30">ID</th>
                                                        <th class="dataTableHeadingContent" height="20"><?php echo HEADING_BUTTON; ?></th>
                                                        <th class="dataTableHeadingContent" height="20"><?php echo HEADING_TEXT; ?></th>
                                                        <th class="dataTableHeadingContent" height="20" width="30">Aktion</th>
                                                    </tr>
                                                    <?php
                                                    $uebersicht_query = xtc_db_query("SELECT id, button, buttontext FROM " . TABLE_CSEO_LANG_BUTTON . " WHERE language_id = '" . $languages[$i]['id'] . "' ORDER BY id ASC ");
                                                    if (xtc_db_num_rows($uebersicht_query)) {
                                                        // $w = 1;
                                                        while ($pl = xtc_db_fetch_array($uebersicht_query)) {
                                                            ?>
                                                            <tr>
                                                                <td align="center">
                                                                    <?php echo $pl['id']; ?>
                                                                </td>
                                                                <td align="left">
                                                                    <?php echo $pl['button']; ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo xtc_draw_input_field('buttontext[' . $languages[$i]['id'] . '][' . $pl['id'] . ']', $pl['buttontext'], 'style="width:100%"'); ?>
                                                                </td>
                                                                <td align="right">
                                                                        <!--<a href="<?php FILENAME_CSEO_LANGUAGE_BUTTON ?>?l=<?php echo $languages[$i]['id'] ?>&id=<?php echo $pl['id'] ?>" title="diesen Eintrag löschen"><img src="images/icons/icon_delete.png" title="Delete" /></a>-->
                                                                    <?php echo '<input type="submit" class="button" value="' . BUTTON_UPDATE . '" /> '; ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            // $w++; 
                                                        }
                                                    }
                                                    ?>
                                                    <?php
                                                    echo xtc_draw_hidden_field('action', 'update');
                                                    echo xtc_draw_hidden_field('l', $languages[$i]['id']);
                                                    ?>
                                                </table>
                                                </form>

                                                <?php echo xtc_draw_form('cseo_lang_button_update', FILENAME_CSEO_LANGUAGE_BUTTON, '', 'post', ''); ?>	
                                                <br />
                                                <table class="dataTable" width="100%">	
                                                    <tr>
                                                        <td class="menu_active" colspan="2"><?php echo CSEO_BUTTON_NEW; ?></td>
                                                    </tr>
                                                    <tr class="dataTableHeadingRow">
                                                        <th class="dataTableHeadingContent" height="20"><?php echo CSEO_BUTTON_VALUE; ?></th>
                                                        <th class="dataTableHeadingContent" height="20"><?php echo CSEO_BUTTON_TEXT; ?></th>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <?php echo xtc_draw_input_field('button[' . $languages[$i]['id'] . ']', $pl['button'], 'style="width:100%"'); ?>
                                                        </td>
                                                        <td>
                                                            <?php echo xtc_draw_input_field('buttontext[' . $languages[$i]['id'] . ']', $pl['buttontext'], 'style="width:100%"'); ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <?php
                                                echo xtc_draw_hidden_field('action', 'save');
                                                echo xtc_draw_hidden_field('l', $languages[$i]['id']);
                                                echo '<input type="submit" class="button" value="' . BUTTON_SAVE . '" /> ';
                                                ?>

                                                </form>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        </table></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
