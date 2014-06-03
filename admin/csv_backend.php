<?php
/* -----------------------------------------------------------------
 * 	$Id: csv_backend.php 849 2014-02-10 14:01:15Z akausch $
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
require(DIR_WS_CLASSES . 'import.php');
require_once(DIR_FS_INC . 'xtc_format_filesize.inc.php');

define('FILENAME_CSV_BACKEND', 'csv_backend.php');

switch ($_GET['action']) {

    case 'upload':
        $upload_file = xtc_db_prepare_input($_POST['file_upload']);
        if ($upload_file = &xtc_try_upload('file_upload', DIR_FS_CATALOG . 'import/')) {
            $$upload_file_name = $upload_file->filename;
        }
        break;

    case 'import':
        $handler = new xtcImport($_POST['select_file']);
        $mapping = $handler->map_file($handler->generate_map());
        $import = $handler->import($mapping);
        break;

    case 'export':
        $handler = new xtcExport('export.csv');
        $import = $handler->exportProdFile();
        break;

    case 'export_orders':
        $handler = new ordersExport('bestellungen_export.csv');
        exit;
        break;

    case 'save':
        $configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '20' order by sort_order");
        while ($configuration = xtc_db_fetch_array($configuration_query))
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . $_POST[$configuration['configuration_key']] . "' where configuration_key='" . $configuration['configuration_key'] . "'");

        xtc_redirect(FILENAME_CSV_BACKEND);
        break;
}



$cfg_group_query = xtc_db_query("select configuration_group_title from " . TABLE_CONFIGURATION_GROUP . " where configuration_group_id = '20'");
$cfg_group = xtc_db_fetch_array($cfg_group_query);


$monate_namen = array('1' => 'Januar', '2' => 'Februar', '3' => 'M&auml;rz', '4' => 'April', '5' => 'Mai', '6' => 'Juni', '7' => 'Juli', '8' => 'August', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Dezember');

$erste_bestellung_query = xtc_db_query("SELECT date_purchased FROM " . TABLE_ORDERS . " ORDER BY date_purchased ASC LIMIT 1");
if (xtc_db_num_rows($erste_bestellung_query)) {
    if ($_GET['monat'] > 0)
        $dropdown_monat = '<select name="monat" onchange="javascript:this.form.submit();">';
    else
        $dropdown_monat = '<select name="monat" onchange="javascript:this.form.submit();"><option selected="" value="0">w&auml;hlen Sie einen Monat</option>';

    for ($monate = 1; $monate <= 12; $monate++)
        $dropdown_monat .= '<option value="' . $monate . '"' . ($monate == (int) $_GET['monat'] ? 'selected=""' : '') . '>' . $monate_namen[$monate] . '</option>';

    $dropdown_monat .= '</select>';

    #########################

    if ($_GET['jahr'] > 0)
        $dropdown_jahr = '<select name="jahr" onchange="javascript:this.form.submit();">';
    else
        $dropdown_jahr = '<select name="jahr" onchange="javascript:this.form.submit();"><option selected="" value="0">w&auml;hlen Sie einen Jahr</option>';

    $erste_bestellung = xtc_db_fetch_array($erste_bestellung_query);
    $erstes_jahr = substr($erste_bestellung['date_purchased'], 0, 4);
    $dieses_jahr = date('Y');
    for ($jahre = $erstes_jahr; $jahre <= $dieses_jahr; $jahre++)
        $dropdown_jahr .= '<option value="' . $jahre . '"' . ($jahre == (int) $_GET['jahr'] ? 'selected=""' : '') . '>' . $jahre . '</option>';

    $dropdown_jahr .= '</select>';
    $_SESSION['jahr'] = $_GET['jahr'];
    if ($_GET['monat'] == 1 || $_GET['monat'] == 2 || $_GET['monat'] == 3 || $_GET['monat'] == 4 || $_GET['monat'] == 5 || $_GET['monat'] == 6 || $_GET['monat'] == 7 || $_GET['monat'] == 8 || $_GET['monat'] == 9) {
        $_SESSION['monat'] = '0' . $_GET['monat'];
    } else {
        $_SESSION['monat'] = $_GET['monat'];
    }
}


require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading">CSV Import / Export</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="main">
                        <table class="infoBoxHeading" width="100%">
                            <tr>
                                <td width="150" align="center">
                                    <a href="#" onClick="toggleBox('config');"><?php echo CSV_SETUP; ?></a>
                                </td>
                                <td width="1">|
                                </td>
                                <td>
                                </td>
                            </tr>
                        </table>
                        <div id="config" class="longDescription">
                            <?php echo xtc_draw_form('configuration', FILENAME_CSV_BACKEND, 'gID=20&action=save'); ?>
                            <table width="100%"  border="0" cellspacing="0" cellpadding="4">
                                <?php
                                $configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '20' order by sort_order");
                                $i = 0;
                                while ($configuration = xtc_db_fetch_array($configuration_query)) {
                                    $i++;
                                    if ($_GET['gID'] == 6) {
                                        switch ($configuration['configuration_key']) {
                                            case 'MODULE_PAYMENT_INSTALLED':
                                                if ($configuration['configuration_value'] != '') {
                                                    $payment_installed = explode(';', $configuration['configuration_value']);
                                                    for ($i = 0, $n = sizeof($payment_installed); $i < $n; $i++) {
                                                        include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $payment_installed[$i]);
                                                    }
                                                }
                                                break;

                                            case 'MODULE_SHIPPING_INSTALLED':
                                                if ($configuration['configuration_value'] != '') {
                                                    $shipping_installed = explode(';', $configuration['configuration_value']);
                                                    for ($i = 0, $n = sizeof($shipping_installed); $i < $n; $i++) {
                                                        include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/' . $shipping_installed[$i]);
                                                    }
                                                }
                                                break;

                                            case 'MODULE_ORDER_TOTAL_INSTALLED':
                                                if ($configuration['configuration_value'] != '') {
                                                    $ot_installed = explode(';', $configuration['configuration_value']);
                                                    for ($i = 0, $n = sizeof($ot_installed); $i < $n; $i++) {
                                                        include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/' . $ot_installed[$i]);
                                                    }
                                                }
                                                break;
                                        }
                                    }
                                    if (xtc_not_null($configuration['use_function'])) {
                                        $use_function = $configuration['use_function'];
                                        if (preg_match('/->/', $use_function)) {
                                            $class_method = explode('->', $use_function);
                                            if (!is_object(${$class_method[0]})) {
                                                include(DIR_WS_CLASSES . $class_method[0] . '.php');
                                                ${$class_method[0]} = new $class_method[0]();
                                            }
                                            $cfgValue = xtc_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
                                        } else {
                                            $cfgValue = xtc_call_function($use_function, $configuration['configuration_value']);
                                        }
                                    } else {
                                        $cfgValue = $configuration['configuration_value'];
                                    }

                                    if (((!$_GET['cID']) || (@$_GET['cID'] == $configuration['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
                                        $cfg_extra_query = xtc_db_query("select configuration_key,configuration_value, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
                                        $cfg_extra = xtc_db_fetch_array($cfg_extra_query);

                                        $cInfo_array = xtc_array_merge($configuration, $cfg_extra);
                                        $cInfo = new objectInfo($cInfo_array);
                                    }
                                    if ($configuration['set_function']) {
                                        eval('$value_field = ' . $configuration['set_function'] . '"' . htmlspecialchars($configuration['configuration_value']) . '");');
                                    } else {
                                        $value_field = xtc_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'], 'size=40');
                                    }
                                    // add

                                    if (strstr($value_field, 'configuration_value'))
                                        $value_field = str_replace('configuration_value', $configuration['configuration_key'], $value_field);

                                    if ($i % 2 == 0)
                                        $f = '';
                                    else
                                        $f = 'dataTableRow';
                                    echo '
 <tr class="' . $f . '" >
   <td width="30%" valign="top" style="border-top: 1px dashed #ccc">
   	<b>' . constant(strtoupper($configuration['configuration_key'] . '_TITLE')) . '</b>
   </td>
   <td valign="top" style="border-top: 1px dashed #ccc">
   <table width="100%"  border="0" cellspacing="0" cellpadding="2">
     <tr>
       <td>' . $value_field . '</td>
     </tr>
   </table>
   <br />' . constant(strtoupper($configuration['configuration_key'] . '_DESC')) . '</td>
 </tr>';
                                }
                                ?>
                            </table>
                            <?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>';
                            echo '</form>'; ?>
                        </div>
                        <?php
                        if ($import) {
                            if ($import[0]) {
                                echo '<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="messageStackSuccess"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                    ';

                                if (isset($import[0]['prod_new']))
                                    echo 'new products:' . $import[0]['prod_new'] . '<br />';
                                if (isset($import[0]['cat_new']))
                                    echo 'new categories:' . $import[0]['cat_new'] . '<br />';
                                if (isset($import[0]['prod_upd']))
                                    echo 'updated products:' . $import[0]['prod_upd'] . '<br />';
                                if (isset($import[0]['cat_upd']))
                                    echo 'updated categories:' . $import[0]['cat_upd'] . '<br />';
                                if (isset($import[0]['cat_touched']))
                                    echo 'touched categories:' . $import[0]['cat_touched'] . '<br />';
                                if (isset($import[0]['prod_exp']))
                                    echo 'products exported:' . $import[0]['prod_exp'] . '<br />';
                                if (isset($import[2]))
                                    echo $import[2];

                                echo '</font></td>
                </tr>
                </table>';
                            }

                            if (isset($import[1]) && $import[1][0] != '') {
                                echo '<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="messageStackError"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                    ';

                                for ($i = 0; $i < count($import[1]); $i++) {
                                    echo $import[1][$i] . '<br />';
                                }


                                echo '</font></td>
                </tr>
                </table>';
                            }
                        }
                        ?>
                        <table width="100%"  border="0" cellspacing="5" cellpadding="0">
                            <tr>
                                <td class="pageHeading">IMPORT</td>
                            </tr>
                            <tr>
                                <td class="dataTableHeadingContent"><?php echo TEXT_IMPORT; ?>
                                    <table width="100%"  border="0" cellspacing="2" cellpadding="0">
                                        <tr>
                                            <td width="7%"></td>
                                            <td width="93%" class="infoBoxHeading"><?php echo UPLOAD; ?></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                <?php
                                                echo xtc_draw_form('upload', FILENAME_CSV_BACKEND, 'action=upload', 'POST', 'enctype="multipart/form-data"');
                                                echo xtc_draw_file_field('file_upload');
                                                echo '<br/><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPLOAD . '"/>';
                                                echo '</form>';
                                                ?>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td class="infoBoxHeading"><?php echo SELECT; ?></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                <?php
                                                $files = array();
                                                echo xtc_draw_form('import', FILENAME_CSV_BACKEND, 'action=import', 'POST', 'enctype="multipart/form-data"');
                                                if ($dir = opendir(DIR_FS_CATALOG . 'import/')) {
                                                    while (($file = readdir($dir)) !== false) {
                                                        if (is_file(DIR_FS_CATALOG . 'import/' . $file) and ($file != ".htaccess") and ($file != "index.html")) {
                                                            $size = filesize(DIR_FS_CATALOG . 'import/' . $file);
                                                            $files[] = array(
                                                                'id' => $file,
                                                                'text' => $file . ' | ' . xtc_format_filesize($size));
                                                        }
                                                    }
                                                    closedir($dir);
                                                }
                                                echo xtc_draw_pull_down_menu('select_file', $files, '');
                                                echo '<br/><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_IMPORT . '"/>';
                                                echo '</form>';
                                                ?>
                                            </td>
                                        </tr>
                                    </table>      <p>&nbsp; </p></td>
                            </tr>
                        </table>


                        <table width="100%"  border="0" cellspacing="5" cellpadding="0">
                            <tr>
                                <td class="pageHeading">Export</td>
                            </tr>
                            <tr>
                                <td class="infoBoxHeading"><?php echo TEXT_EXPORT; ?>
                                    <table width="100%"  border="0" cellspacing="2" cellpadding="0">
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                <?php
                                                echo xtc_draw_form('export', FILENAME_CSV_BACKEND, 'action=export', 'POST', 'enctype="multipart/form-data"');
                                                $content = array();
                                                $content[] = array('id' => 'products', 'text' => TEXT_PRODUCTS);
                                                echo xtc_draw_pull_down_menu('select_content', $content, 'products');
                                                echo '<br/><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_EXPORT . '"/>';
                                                echo '</form>';
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>

                                            </td>
                                        </tr>
                                    </table>      <p>&nbsp; </p></td>
                            </tr>
                        </table>

                        <table width="100%"  border="0" cellspacing="5" cellpadding="0">
                            <tr>
                                <td class="pageHeading">Export Bestellungen</td>
                            </tr>
                            <tr>
                                <td class="infoBoxHeading">Export aller Bestellungen im CSV Format
                                    <?php echo xtc_draw_form('select', 'csv_backend.php', '', 'get', ''); ?>
									<table width="100%"  border="0" cellspacing="2" cellpadding="0">
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                <table cellpadding="5" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td width="1">
<?php echo $dropdown_monat; ?>
                                                        </td>
                                                        <td width="1">
<?php echo $dropdown_jahr; ?>
                                                        </td>
                                                    </tr>
                                                </table>
												</form>
                                                <?php
                                                echo xtc_draw_form('export_orders', FILENAME_CSV_BACKEND, 'action=export_orders', 'POST', 'enctype="multipart/form-data" target="_blank"');
                                                echo '<br/><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_EXPORT . '"/>';
                                                ?>
                                                </form>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>

                                            </td>
                                        </tr>
                                    </table>      <p>&nbsp; </p></td>
                            </tr>
                        </table>


                    </td>
                </tr>
            </table></td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
