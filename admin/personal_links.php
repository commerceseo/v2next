<?php
/* -----------------------------------------------------------------
 * 	$Id: personal_links.php 420 2013-06-19 18:04:39Z akausch $
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

if (isset($_POST['action']) && ($_POST['action'] == 'save' ) && (!empty($_POST['file_name'])) && (!empty($_POST['url_text'])) || isset($_POST['action']) && ($_POST['action'] == 'save_new' ) && (!empty($_POST['file_name'])) && (!empty($_POST['url_text']))) {

    function getRegExps(&$search, &$replace) {
        $search = array(
            "/ß/", //--Umlaute entfernen
            "/ä/",
            "/ü/",
            "/ö/",
            "/Ä/",
            "/Ü/",
            "/Ö/",
            "'&(auml|#228);'i",
            "'&(ouml|#246);'i",
            "'&(uuml|#252);'i",
            "'&(szlig|#223);'i",
            "'[\r\n\s]+'", // strip out white space
            "'&(quote|#34);'i", // replace html entities
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&'", // ampersant in + konvertieren
            "'%'", //-- % entfernen
            "/[\[\({]/", //--Oeffnende Klammern nach Bindestriche entfernen
            "/[\)\]\}]/", //--schliessende Klammern entfernen            
            "/'|\"|’|`/", //--Anfuehrungszeichen entfernen
            "/[:\!?\*\+]/", //--Doppelpunkte, Komma, Punkt, asterisk entfernen
            "'\s&\s'", // remove ampersant
        );
        $replace = array(
            "ss",
            "ae",
            "ue",
            "oe",
            "Ae",
            "Ue",
            "Oe",
            "ae",
            "oe",
            "ue",
            "ss",
            "-",
            "\"",
            "-",
            "<",
            ">",
            "",
            chr(161),
            chr(162),
            chr(163),
            chr(169),
            "-",
            "+",
            "-",
            "",
            "",
            "",
            "-"
        );
    }

    function getUrlFriendlyText($string) {
        $search = array();
        $replace = array();
        getRegExps($search, $replace);
        $validUrl = preg_replace("/<br>/i", "-", $string);
        $validUrl = strip_tags($validUrl);
        $validUrl = preg_replace("/\//", "-", $validUrl);
        $validUrl = preg_replace($search, $replace, $validUrl);
        $validUrl = preg_replace("/(-){2,}/", "-", $validUrl);
        $validUrl = preg_replace("/[^a-z0-9-.]/i", "", $validUrl);
        $validUrl = urlencode($validUrl);
        return($validUrl);
    }

    $url = xtc_db_prepare_input($_POST['url_text']);
    $url = getUrlFriendlyText($url);
    #$url = trim(str_replace('html','',$url));
    $url = trim($url);
    $url = str_replace('php', '', $url);

    if (isset($_POST['code']) && MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL == 'True') {
        $url = $_POST['code'] . '/' . $url;
    }

    $sql_data_array = array('url_text' => xtc_db_prepare_input($url),
        'file_name' => xtc_db_prepare_input($_POST['file_name']),
        'language_id' => xtc_db_prepare_input($_POST['l']));

    if ($_POST['action'] == 'save') {
        xtc_db_perform(TABLE_PERSONAL_LINKS_URL, $sql_data_array, 'update', 'file_name = \'' . xtc_db_input($_POST['file_name']) . '\' AND language_id = \'' . xtc_db_input($_POST['l']) . '\'');
    } elseif ($_POST['action'] == 'save_new') {
        xtc_db_perform(TABLE_PERSONAL_LINKS_URL, $sql_data_array);
    }
    xtc_redirect(FILENAME_PERSONAL_LINKS . '#language_' . $_POST['l']);
} elseif ($_GET['action'] == 'new_personal_url') {
    if (!empty($_POST['file_name']) && !empty($_POST['file_name_php'])) {
        $sql_data_array = array('file_name' => strtoupper(xtc_db_prepare_input($_POST['file_name'])),
            'file_name_php' => xtc_db_prepare_input($_POST['file_name_php']));
        xtc_db_perform('commerce_seo_url_names', $sql_data_array);
        xtc_redirect(FILENAME_PERSONAL_LINKS);
    } else {
        $messageStack->add_session('Geben Sie eine Dateikonstante und einen Dateinamen an.', 'error');
        xtc_redirect(FILENAME_PERSONAL_LINKS . '?action=new_link');
    }
} elseif ($_GET['action'] == 'delete_link') {
    if ($_GET['id'] != '')
        xtc_db_query("DELETE FROM commerce_seo_url_personal_links WHERE link_id = '" . (int) $_GET['id'] . "' AND language_id = '" . $_GET['l'] . "' ");
    xtc_redirect(FILENAME_PERSONAL_LINKS . '#language_' . $_GET['l']);
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
                                                Personal URL Links 
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="left">

<?php
if (isset($_GET['action']) && $_GET['action'] == 'new_link') {
    echo xtc_draw_form('new_personal_url', FILENAME_PERSONAL_LINKS, 'action=new_personal_url', 'post', '')
    ?>
                                        <table class="dataTable" width="100%">
                                            <tr class="dataTableHeadingRow">
                                                <th class="dataTableHeadingContent" height="20">Datei Konstante</th>
                                                <th class="dataTableHeadingContent" height="20">Datei Name</th>
                                                <th class="dataTableHeadingContent" align="left" height="20">&nbsp;</th>
                                            </tr>
                                            <tr>
                                                <td>
    <?php echo xtc_draw_input_field('file_name', '', 'size="30"') ?>
                                                </td>
                                                <td>
                                                    <?php echo xtc_draw_input_field('file_name_php', '', 'size="30"') ?>
                                                </td>
                                                <td>
                                                    <?php echo '<input type="submit" class="button" value="Speichern" /> 
										<a class="button" href="' . FILENAME_PERSONAL_LINKS . '#language_' . $languages[$i]['id'] . '">Abbruch</a>'; ?> 

                                                </td>
                                            </tr>
                                        </table>
                                        </form>
<?php } else { ?>
                                        <div align="right">
                                            <a class="button" href="<?php echo xtc_href_link(FILENAME_PERSONAL_LINKS, 'action=new_link'); ?>">Neuer Personal Link</a> 

                                        </div>
                                        <div id="tabs">
                                            <ul>
    <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                                                    <li><a href="#language_<?php echo $languages[$i]['id']; ?>">
                                                            <span><img src="../lang/<?php echo $languages[$i]['directory'] . '/' . $languages[$i]['image']; ?>" alt="" /> <?php echo $languages[$i]['name'] ?></span>
                                                        </a></li>
    <?php } ?>
                                            </ul>
                                                <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                                                <div id="language_<?php echo $languages[$i]['id']; ?>">
                                                    <table class="dataTable" width="100%">
                                                        <tr class="dataTableHeadingRow">
                                                            <th class="dataTableHeadingContent" height="20" width="30">&nbsp;</th>
                                                            <th class="dataTableHeadingContent" height="20">URL</th>
                                                            <th class="dataTableHeadingContent" align="left" height="20">Beschreibung</th>
                                                            <th class="dataTableHeadingContent">Original</th>
                                                            <th class="dataTableHeadingContent">&nbsp;</th>
                                                        </tr>
        <?php
        $uebersicht_query = xtc_db_query("SELECT cp.url_text, 
																				cp.link_id,
																				cp.file_name,
																				cp.language_id, 
																				cn.file_name,
																				cn.file_name_php
																				FROM 
																					" . TABLE_PERSONAL_LINKS_NAMES . " AS cn
																				LEFT OUTER JOIN 
																					" . TABLE_PERSONAL_LINKS_URL . " AS cp ON (cp.file_name = cn.file_name AND cp.language_id = '" . $languages[$i]['id'] . "') 
																				WHERE 
																					cn.file_name_php != 'index.php'
																				ORDER BY cn.file_name ASC ");


        while ($pl = xtc_db_fetch_array($uebersicht_query)) {
            ?>
                                                            <tr>
                                                                <td align="center" width="5%">
            <?php
            echo '<a href="' . xtc_href_link(FILENAME_PERSONAL_LINKS, 'action=edit_link&id=' . $pl['file_name_php'] . '&l=' . $languages[$i]['id'] . '#language_' . $languages[$i]['id']) . '">
															' . xtc_image(DIR_WS_IMAGES . 'icon_edit.gif') .
            '</a>';
            ?>
                                                                </td>
                                                                <td width="40%" nowrap="nowrap">
            <?php
            if (isset($_GET['action']) && ($_GET['action'] == 'edit_link') && ($_GET['id'] == $pl['file_name_php']) && ($_GET['l'] == $languages[$i]['id'])) {
                $url_text = str_replace($languages[$i]['code'] . '/', '', $pl['url_text']);
                echo xtc_draw_form('edit_personal_url', FILENAME_PERSONAL_LINKS, '', 'post', '')
                . xtc_draw_hidden_field('file_name', $pl['file_name'])
                . xtc_draw_hidden_field('l', $languages[$i]['id']);
                if (MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL == 'True')
                    echo xtc_draw_hidden_field('code', $languages[$i]['code']);
                if (empty($pl['url_text']))
                    echo xtc_draw_hidden_field('action', 'save_new');
                else
                    echo xtc_draw_hidden_field('action', 'save');
                echo xtc_draw_input_field('url_text', $url_text, 'size="30"') . ' (Link-Name mit Endung .html angeben) <input type="submit" class="button" value="Speichern" /> 
																<a class="button" href="' . FILENAME_PERSONAL_LINKS . '#language_' . $languages[$i]['id'] . '">Abbruch</a>';
                echo '</form>';
            } else {
                if (!empty($pl['url_text']))
                    echo $pl['url_text'];
                else
                    echo '-';
            }
            ?>
                                                                </td>
                                                                <td class="last" width="30%">
                                                                    <?php
                                                                    echo constant(strtoupper($pl['file_name'] . '_DESC'));
                                                                    ?>
                                                                </td>
                                                                <td class="last" width="25%">
                                                                    <?php
                                                                    echo $pl['file_name_php'];
                                                                    ?>
                                                                </td>
                                                                <td width="1">
                                                                    <?php
                                                                    if (!empty($pl['url_text']))
                                                                        echo '<a href="' . xtc_href_link(FILENAME_PERSONAL_LINKS, 'action=delete_link&id=' . $pl['link_id'] . '&l=' . $languages[$i]['id'] . '#language_' . $languages[$i]['id']) . '">' . xtc_image(DIR_WS_ICONS . 'chain--minus.png', $pl['url_text'] . ' l&ouml;schen') . '</a>';
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                                <?php } ?>
                                                    </table>
                                                </div>
                                                    <?php
                                                    }
                                                }
                                                ?>
                                </td>
                            </tr>
                        </table></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</div>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
