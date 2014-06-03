<?php
/* -----------------------------------------------------------------
 * 	$Id: news_ticker.php 420 2013-06-19 18:04:39Z akausch $
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

$query = xtc_db_query("SELECT code FROM " . TABLE_LANGUAGES . " WHERE languages_id='" . $_SESSION['languages_id'] . "'");
$data = xtc_db_fetch_array($query);
$languages = xtc_get_languages();

if (isset($_GET['l']) && isset($_GET['id'])) {
    xtc_db_query("DELETE FROM " . TABLE_NEWS_TICKER . " WHERE id = '" . $_GET['id'] . "' AND language_id = '" . $_GET['l'] . "' ");
    xtc_redirect(FILENAME_NEWS_TICKER . '#language_' . $_GET['l']);
}

if (isset($_POST['action']) && ($_POST['action'] == 'save' )) {

    $text_post = xtc_db_prepare_input($_POST['ticker_text']);

    if ($_POST['new_ticker_text'][$_POST['l']] != '')
        xtc_db_query("INSERT INTO " . TABLE_NEWS_TICKER . " VALUES (NULL, '" . trim($_POST['new_ticker_text'][$_POST['l']]) . "', '" . $_POST['l'] . "', '1') ");

    foreach ($text_post AS $lang => $tmp)
        foreach ($tmp AS $id => $text)
            xtc_db_query("UPDATE " . TABLE_NEWS_TICKER . " SET ticker_text = '" . $text . "' WHERE id = '" . $id . "' AND language_id = '" . $lang . "' ");

    xtc_redirect(FILENAME_NEWS_TICKER . '#language_' . $_POST['l']);
}
require(DIR_WS_INCLUDES . 'header.php');
?>
<table class="outerTable" cellpadding="0" cellspacing="0">
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
                                                News Ticker
                                            </td>
                                        </tr>
                                    </table>
                                    Hinweis: jede Sprache muss für sich gespeichert werden.<br /><br />
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <div id="tabslang">
                                        <ul>
                                            <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                                                <li><a href="#language_<?php echo $languages[$i]['id']; ?>">
                                                        <span><img src="../lang/<?php echo $languages[$i]['directory'] . '/' . $languages[$i]['image']; ?>" alt="" /> <?php echo $languages[$i]['name'] ?></span>
                                                    </a></li>
                                            <?php } ?>
                                        </ul>

                                        <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                                            <div id="language_<?php echo $languages[$i]['id']; ?>">
                                                <?php echo xtc_draw_form('new_ticker_text', FILENAME_NEWS_TICKER, '', 'post', ''); ?>
                                                <table class="dataTable" width="100%">
                                                    <tr class="dataTableHeadingRow">
                                                        <th class="dataTableHeadingContent" height="20" width="30">&nbsp;</th>
                                                        <th class="dataTableHeadingContent" height="20" colspan="2">Ticker Text</th>
                                                    </tr>
                                                    <?php
                                                    $uebersicht_query = xtc_db_query("SELECT id, ticker_text FROM news_ticker WHERE language_id = '" . $languages[$i]['id'] . "' ORDER BY id ASC ");
                                                    if (xtc_db_num_rows($uebersicht_query)) {
                                                        $w = 1;
                                                        while ($pl = xtc_db_fetch_array($uebersicht_query)) {
                                                            ?>
                                                            <tr>
                                                                <td align="center">
                                                                    <?php echo $w; ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo xtc_draw_input_field('ticker_text[' . $languages[$i]['id'] . '][' . $pl['id'] . ']', $pl['ticker_text'], 'style="width:100%"');
                                                                    ?>
                                                                </td>
                                                                <td width="1" align="right">
                                                                    <a href="news_ticker.php?l=<?php echo $languages[$i]['id'] ?>&id=<?php echo $pl['id'] ?>" title="diesen Eintrag löschen"><img src="images/cross.gif" alt="" /></a>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            $w++;
                                                        }
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td valign="top" align="center">neu</td>
                                                        <td colspan="2">
                                                            <?php
                                                            echo xtc_draw_input_field('new_ticker_text[' . $languages[$i]['id'] . ']', $pl['ticker_text'], 'style="width:100%"')
                                                            . xtc_draw_hidden_field('action', 'save')
                                                            . xtc_draw_hidden_field('l', $languages[$i]['id'])
                                                            . '<input type="submit" class="button" value="' . BUTTON_SAVE . '" /> ';
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
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
