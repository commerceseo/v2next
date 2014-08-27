<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_image_processing_popup.php 420 2013-06-19 18:04:39Z akausch $
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

define('MODULE_POPUP_IMAGE_PROCESS_TEXT_DESCRIPTION', 'Es werden alle Bilder in den Verzeichnissen<br /><br />
/images/product_images/popup_images/ <br />
<br /> neu erstellt.<br /> <br />

Hierzu verarbeitet das Script nur eine begrenzte Anzahl von %s Bildern und ruft sich danach selbst wieder auf.<br /> <br />');
define('MODULE_POPUP_IMAGE_PROCESS_TEXT_TITLE', 'CSEO-Imageprocessing-Mini <b>-V2.2 (popup_images)</b>');
define('MODULE_POPUP_IMAGE_PROCESS_STATUS_DESC', 'Modulstatus');
define('MODULE_POPUP_IMAGE_PROCESS_STATUS_TITLE', 'Status');
define('IMAGE_EXPORT', 'Dr&uuml;cken Sie Ok um die Stapelverarbeitung zu starten, dieser Vorgang kann einige Zeit dauern, auf keinen Fall unterbrechen!.');
define('IMAGE_EXPORT_TYPE', '<hr noshade><strong>Stapelverarbeitung:</strong>');

define('IMAGE_STEP_INFO', 'Bilder erstellt: ');
define('IMAGE_STEP_INFO_READY', '<br /><br /> Fertig!');
define('TEXT_MAX_IMAGES', 'max. Bilder pro Seitenreload');
define('TEXT_ONLY_MISSING_IMAGES', 'Nur fehlende Bilder erstellen');



if (!class_exists("cseo_image_processing_popup")) {

    class cseo_image_processing_popup {

        var $code, $title, $description, $enabled;

        function cseo_image_processing_popup() {
            global $order;

            $this->code = 'cseo_image_processing_popup';
            $this->title = MODULE_POPUP_IMAGE_PROCESS_TEXT_TITLE;
            $this->description = sprintF(MODULE_POPUP_IMAGE_PROCESS_TEXT_DESCRIPTION, $_GET['max']);
            $this->sort_order = MODULE_POPUP_IMAGE_PROCESS_SORT_ORDER;
            $this->enabled = ((MODULE_POPUP_IMAGE_PROCESS_STATUS == 'True') ? true : false);
        }

        function process($file, $offset) {
            global $limit, $selbstaufruf, $infotext, $count;

            include ('includes/classes/class.image_manipulator_gd2.php');

            $ext_array = array('gif', 'jpg', 'jpeg', 'png'); //GГјltige Dateiendungen

            @xtc_set_time_limit(0);
            $files = array();
            if ($dir = opendir(DIR_FS_CATALOG_ORIGINAL_IMAGES)) {
                while ($file = readdir($dir)) {
                    $tmp = explode('.', $file);
                    if (is_array($tmp)) {
                        $ext = strtolower($tmp[count($tmp) - 1]);
                        if (is_file(DIR_FS_CATALOG_ORIGINAL_IMAGES . $file) && in_array($ext, $ext_array)) {
                            $files[] = array(
                                'id' => $file,
                                'text' => $file);
                        }
                    }
                }
                closedir($dir);
            }

            $max_files = sizeof($files);
            $step = $_GET['max'];
            $count = $_GET['count'];
            $limit = $offset + $step;
            for ($i = $offset; $i < $limit; $i++) {
                if ($i >= $max_files) { // FERTIG
                    $infotext = urlencode(IMAGE_STEP_INFO . $count . IMAGE_STEP_INFO_READY);
                    xtc_redirect(xtc_href_link(FILENAME_MODULE_SYSTEM, 'set=' . $_GET['set'] . '&module=cseo_image_processing_popup&infotext=' . $infotext . '&max=' . $_GET['max'])); //FERTIG
                }
                $products_image_name = $files[$i]['text'];

                if ($_GET['miss'] == 1) {
                    $flag = false;
                    if (!is_file(DIR_FS_CATALOG_PUPUP_IMAGES . $files[$i]['text'])) {
                        require(DIR_WS_INCLUDES . 'product_popup_images.php');
                        $flag = true;
                    }
                    if ($flag) {
                        $count += 1;
                    }
                } else {
                    require(DIR_WS_INCLUDES . 'product_popup_images.php');
                    $count += 1;
                }
            }
            //Animierte Gif-Datei und Hinweistext
            $info_wait = '<img src="images/loading.gif"> ';
            $infotext = '<div style="margin:10px; font-family:Verdana; font-size:15px; text-align:center;">' . $info_wait . IMAGE_STEP_INFO . $count . '</div>';
            $selbstaufruf = '<script language="javascript" type="text/javascript">setTimeout("document.img_continue.submit()", 3000);</script>';
        }

        function display() {

            //Array fГјr max. Bilder pro Seitenreload
            $max_array = array(array('id' => '5', 'text' => '5'));
            $max_array[] = array('id' => '10', 'text' => '10');
            $max_array[] = array('id' => '15', 'text' => '15');
            $max_array[] = array('id' => '20', 'text' => '20');
            $max_array[] = array('id' => '50', 'text' => '50');

            return array('text' =>
                xtc_draw_hidden_field('process', 'image_processing_do') .
                xtc_draw_hidden_field('max_images1', '5') .
                IMAGE_EXPORT_TYPE . '<br />' .
                IMAGE_EXPORT . '<br />' .
                '<br />' . xtc_draw_pull_down_menu('max_images', $max_array, '5') . ' ' . TEXT_MAX_IMAGES . '<br />' .
                '<br />' . xtc_draw_checkbox_field('only_missing_images', '1', false) . ' ' . TEXT_ONLY_MISSING_IMAGES . '<br />' .
                '<br />' . xtc_button(BUTTON_START) . '&nbsp;' .
                xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_SYSTEM, 'set=' . $_GET['set'] . '&module=cseo_image_processing_popup'))
            );
        }

        function check() {
            if (!isset($this->_check)) {
                $check_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_POPUP_IMAGE_PROCESS_STATUS'");
                $this->_check = xtc_db_num_rows($check_query);
            }
            return $this->_check;
        }

        function install() {
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_POPUP_IMAGE_PROCESS_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        }

        function remove() {
            xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('" . implode("', '", $this->keys()) . "')");
        }

        function keys() {
            return array('MODULE_POPUP_IMAGE_PROCESS_STATUS');
        }

    }

}
?>