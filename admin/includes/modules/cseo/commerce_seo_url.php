<?php

/* -----------------------------------------------------------------
 * 	$Id: commerce_seo_url.php 549 2013-08-05 11:21:45Z akausch $
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

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

define('MODULE_COMMERCE_SEO_INDEX_TEXT_DESCRIPTION', 'Der Indexierungsdienst erstellt die SUMA-freundlichen Direct URLs für Ihren Shop.');
define('MODULE_COMMERCE_SEO_INDEX_TEXT_TITLE', 'commerce:SEO URL v2.4.2');
define('MODULE_COMMERCE_SEO_INDEX_STATUS_DESC', 'Modulstatus');
define('MODULE_COMMERCE_SEO_URL_LENGHT_TITLE', 'Kurze URLs?');
define('MODULE_COMMERCE_SEO_URL_LENGHT_DESC', 'Sollen kurze URL genutzt werden?<br />Damit werden die Kategorie-Pfade bei Produkten entfernt.');
define('MODULE_COMMERCE_SEO_URL_OLD_REWRITE_TITLE', 'Lange URL auf Kurze URL umleiten?');
define('MODULE_COMMERCE_SEO_URL_OLD_REWRITE_DESC', 'Sollen die langen URL auf kurze umgeleitet werden? Diese Einstellung gilt, wenn URL auf <b>Kurze URL</b> gestellt ist und vorher nicht.');
define('MODULE_COMMERCE_SEO_URL_LOWERCASE_TITLE', 'URL in Kleinbuchstaben?');
define('MODULE_COMMERCE_SEO_URL_LOWERCASE_DESC', 'Sollen alle URL in Kleinbuchstaben generiert werden.');
define('MODULE_COMMERCE_SEO_404_HANDLING_TITLE', '404 oder 410?');
define('MODULE_COMMERCE_SEO_404_HANDLING_DESC', 'Sollen 404 (Softfehler = True) oder 410 (nicht mehr vorhanden = False) generiert werden bei Fehlern.');
define('MODULE_COMMERCE_SEO_URL_MANUFACTURER_TITLE', 'SEO Suchwörter?');
define('MODULE_COMMERCE_SEO_URL_MANUFACTURER_DESC', 'SEO URL für Hersteller und Suchergebnisse.');
define('MODULE_COMMERCE_SEO_INDEX_STATUS_TITLE', 'Status');
define('MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL_TITLE', 'Sprachabhängige URLs<br />../de/.. | ../en/..');
define('MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL_DESC', 'Die suchmaschinenfreundlichen URLs<br /> werden sprachabhängig kodiert');
define('MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATECONTENT_TITLE', 'Doppelten Content vermeiden');
define('MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATECONTENT_DESC', 'Aktivieren Sie diese Option<br /> um Doppelten Content zu vermeiden (Code 301 Redirect).');
define('MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS_TITLE', 'Doppelte Namen vermeiden');
define('MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS_DESC', 'Aktivieren Sie diese Option<br /> um Doppelte Namen bei Produkten zu vermeiden.');
define('MODULE_COMMERCE_SEO_INDEX_CREATENEWINDEX_TITLE', 'Indexierung durchführen');
define('MODULE_COMMERCE_SEO_INDEX_CREATENEWINDEX_DESC', 'Aktivieren Sie diese Option um beim Speichern alle Seiten neu zu indexieren.');
define('INDEX_START', '<strong>Indexierung starten</strong>');
define('INDEX_START_DESCRIPTION', 'Klicken Sie auf "OK" um die Einstellungen zu speichern. Ist die Option "Indexierung durchführen" auf "True" gesetzt, kann dieser Vorgang einige Zeit in Anspruch nehmen. Brechen Sie diese Operation keinesfalls ab!');
define('IMAGE_STEP_INFO', 'Produkt-URLs: ');
define('IMAGE_STEP_INFO_READY', '<br />und die restlichen URLs generiert :-)<br />Shop-Cache wurde geleert.');

if (!class_exists("commerce_seo_url")) {

    class commerce_seo_url {

        var $code, $title, $description, $enabled, $seourl;

        function commerce_seo_url() {
            global $order;

            $this->code = 'commerce_seo_url';
            $this->title = MODULE_COMMERCE_SEO_INDEX_TEXT_TITLE;
            $this->description = MODULE_COMMERCE_SEO_INDEX_TEXT_DESCRIPTION;
            $this->sort_order = MODULE_COMMERCE_SEO_INDEX_SORT_ORDER;
            $this->enabled = ((MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') ? true : false);
            $this->lenght = ((MODULE_COMMERCE_SEO_URL_LENGHT == 'True') ? true : false);
            $this->oldurl = ((MODULE_COMMERCE_SEO_URL_OLD_REWRITE == 'False') ? true : false);
            $this->uppercase = ((MODULE_COMMERCE_SEO_URL_LOWERCASE == 'True') ? true : false);
            $this->handling = ((MODULE_COMMERCE_SEO_404_HANDLING == 'True') ? true : false);
            $this->searchkey = ((MODULE_COMMERCE_SEO_URL_MANUFACTURER == 'False') ? true : false);
        }

        function delfiles($dir, $desc) {
            if (is_dir($dir)) {
                $error = false;
                $no_file = false;
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file != '..' && $file != '.' && $file != 'index.html' && $file != '.htaccess') {
                            if (unlink($dir . $file))
                                $i++;
                            else {
                                $error = true;
                                $filename .= $dir . $file . ' <b>konnte nicht gel&ouml;scht werden</b><br />';
                            }
                        }
                        $files = $i;
                    }
                    closedir($dh);
                }
                if ($error)
                    return $filename;
                else {
                    if ($i > 0)
                        return '<br />Aus dem Verzeichnis <em>' . $dir . '</em> - ' . $desc . ' wurden <b>' . $i . '</b> Dateien gel&ouml;scht.<br />';
                    else
                        return '<br />Das Verzeichnis <em>' . $dir . '</em> - ' . $desc . ' ist bereits leer.<br />';
                }
            }
            else
                return 'Verzeichnis <em>' . $dir . '</em> nicht gefunden';
        }

        function process($offset) {
            global $limit, $selbstaufruf, $infotext, $_count;
            //variable um die schleifendurchlaeufe zu zaehlen:
            $tmp = 1;
            require_once(DIR_FS_INC . 'commerce_seo.inc.php');
            !$commerceSeo ? $commerceSeo = new CommerceSeo() : false;
            //zuerst die Content und Cat urldecode
            
            $pQuery = xtc_db_query("SELECT products_id FROM " . TABLE_PRODUCTS_DESCRIPTION);

            $pA;
            $pHash;
            //product ids in zwischen array speichern:
            while ($pID = xtc_db_fetch_array($pQuery))
                $pA[] = $pID['products_id'];
            //hashArray füllen:
            for ($k = 0; $k <= sizeof($pA); $k++)
                $pHash[] = array('pid' => $pA[$k]);
            $max_width = sizeof($pHash) - 1;
            $step = $_GET['max'];
            $_count = $_GET['count'];
            $start = $_GET['start'];
            $limit = $offset + $step;
            for ($i = $offset; $i <= $limit; $i++) {
                if ($i >= $max_width) {
                    // $commerceSeo->createSeoDBTable();
					$commerceSeo->createSeoDBTable();
                    $infotext = urlencode(IMAGE_STEP_INFO . $_count . IMAGE_STEP_INFO_READY);
                    // Cache leeren
                    $del_array = array(array('templates_c/'),
                        array(DIR_FS_CATALOG . 'cache/'),
                        array('cache/'),
                        array(DIR_FS_CATALOG . 'templates_c/'));

                    foreach ($del_array AS $key => $value)
                        $response .= $this->delfiles($value[0]);

                    xtc_redirect(xtc_href_link(FILENAME_MODULE_SYSTEM, 'set=' . $_GET['set'] . '&module=commerce_seo_url&infotext=' . $infotext)); //FERTIG	
                }
                $commerceSeo->createSeoDBTableProduct($i, $pHash[$i]['pid']);
                //counter erhoehen um alle operationen zu zaehlen:
                $_count += 1;
            }
            //anzahl methodenaufrufe vom counter abziehen:
            $_count -= $tmp;
            //Animierte Gif-Datei und Hinweistext
            $info_wait = '<img src="images/loading.gif"> ';
            $infotext = '<div style="margin:10px; font-family:Verdana; font-size:15px; text-align:center;">' . $info_wait . 'Produkt-URLs generiert: ' . $_count . '</div>';
            $infTextPlain = 'Produkt-URLs generiert: ' . $_count;
            // am ende der funktion (bevor die form abgesendet wird!) tmp inkrementieren:
            $tmp++;
            switch ($_GET['max']) {
                case 50:
                    $selbstaufruf = '<script language="javascript" type="text/javascript">setTimeout("document.img_continue.submit()", 2000);</script>';
                    break;
                case 100:
                    $selbstaufruf = '<script language="javascript" type="text/javascript">setTimeout("document.img_continue.submit()", 2500);</script>';
                    break;
                case 300:
                    $selbstaufruf = '<script language="javascript" type="text/javascript">setTimeout("document.img_continue.submit()", 3000);</script>';
                    break;
                default:
                    $selbstaufruf = '<script language="javascript" type="text/javascript">setTimeout("document.img_continue.submit()", 2000);</script>';
                    break;
            }
        }

        function display() {
            $pulldown_option[0]['text'] = 'Ja';
            $pulldown_option[0]['id'] = 'true';
            $pulldown_option[1]['text'] = 'Nein';
            $pulldown_option[1]['id'] = 'false';
            $max_array = array(array('id' => '50', 'text' => '50'));
            $max_array[] = array('id' => '100', 'text' => '100');
            $max_array[] = array('id' => '300', 'text' => '300');
            return array('text' => xtc_draw_hidden_field('process', 'image_processing_do') .
                '<br />' . xtc_draw_pull_down_menu('max_images', $max_array, '50') . ' Maximale URL pro Durchlauf<br />' .
                xtc_button(BUTTON_REVIEW_APPROVE) . '&nbsp;' .
                xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_SYSTEM, 'set=' . $_GET['set'] . '&module=commerce_seo_url')));
        }

        function check() {
            if (!isset($this->_check)) {
                $check_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_COMMERCE_SEO_INDEX_STATUS'");
                $this->_check = xtc_db_num_rows($check_query);
            }
            return $this->_check;
        }

        function install() {
            
			if (!column_exists(TABLE_PRODUCTS_DESCRIPTION, 'url_text')) {
				@xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS_DESCRIPTION . " ADD url_text VARCHAR( 255 ) NOT NULL;");
				@xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS_DESCRIPTION . " ADD url_md5 VARCHAR( 32 ) NOT NULL;");
				@xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS_DESCRIPTION . " ADD INDEX urlidx (url_text, url_md5);");
			}            
			if (!column_exists(TABLE_PRODUCTS_DESCRIPTION, 'url_old_text')) {
				@xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS_DESCRIPTION . " ADD url_old_text VARCHAR( 255 ) NOT NULL;");
			}
			
			xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_COMMERCE_SEO_INDEX_STATUS', 'True',  '111', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL', 'False',  '111', '2', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS', 'True',  '111', '3', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATECONTENT', 'True',  '111', '4', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_COMMERCE_SEO_URL_LENGHT', 'True',  '111', '5', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_COMMERCE_SEO_URL_OLD_REWRITE', 'False',  '111', '5', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_COMMERCE_SEO_URL_LOWERCASE', 'True',  '111', '6', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_COMMERCE_SEO_404_HANDLING', 'True',  '111', '7', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_COMMERCE_SEO_URL_MANUFACTURER', 'False',  '111', '8', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");

            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'true' WHERE configuration_key = 'SEARCH_ENGINE_FRIENDLY_URLS'");

            $createTableQuery = "CREATE TABLE IF NOT EXISTS commerce_seo_url (
							url_id int(32) NOT NULL AUTO_INCREMENT,
							url_md5 varchar(32) NOT NULL default '',
							url_text varchar(255) NOT NULL default '',
							products_id int(11) default NULL,
							categories_id int(11) default NULL,
							blog_id int(11) default NULL,
							blog_cat int(11) default NULL,
							content_group int(11) default NULL,
							language_id int(11) NOT NULL default '0',
							PRIMARY KEY  (url_id),
							KEY url_text (url_id,url_text)
							);";
            xtc_db_query($createTableQuery);
        }

        function remove() {
            xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
            xtc_db_query("DROP TABLE commerce_seo_url");
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'false' WHERE configuration_key = 'SEARCH_ENGINE_FRIENDLY_URLS'");
                    // Cache leeren
                    $del_array = array(array('templates_c/'),
                        array(DIR_FS_CATALOG . 'cache/'),
                        array('cache/'),
                        array(DIR_FS_CATALOG . 'templates_c/'));

                    foreach ($del_array AS $key => $value)
                        $response .= $this->delfiles($value[0]);
		
		}

        function keys() {
            return array('MODULE_COMMERCE_SEO_INDEX_STATUS', 'MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL', 'MODULE_COMMERCE_SEO_URL_LENGHT', 'MODULE_COMMERCE_SEO_URL_OLD_REWRITE', 'MODULE_COMMERCE_SEO_URL_LOWERCASE', 'MODULE_COMMERCE_SEO_URL_MANUFACTURER', 'MODULE_COMMERCE_SEO_404_HANDLING', 'MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATECONTENT', 'MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS');
        }

    }

}
?>