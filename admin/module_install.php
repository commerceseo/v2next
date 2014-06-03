<?php
/* -----------------------------------------------------------------
 * 	$Id: module_install.php 420 2013-06-19 18:04:39Z akausch $
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

// include needed functions (for modules)

$module_type = 'export';
$module_directory = DIR_WS_MODULES . 'install/';
$module_key = 'MODULE_INSTALL_INSTALLED';
$file_extension = '.php';
define('HEADING_TITLE', HEADING_TITLE_MODULES_INSTALL);
if (isset($_GET['error'])) {
    $map = 'error';
    if ($_GET['kind'] == 'success')
        $map = 'success';
    $messageStack->add($_GET['error'], $map);
}

switch ($_GET['action']) {
    case 'save':
        if (is_array($_POST['configuration'])) {
            if (count($_POST['configuration'])) {
                while (list($key, $value) = each($_POST['configuration'])) {
                    xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value . "' where configuration_key = '" . $key . "'");
                    if (strpos($key, 'FILE') !== false)
                        $file = $value;
                }
            }
        }

        $class = basename($_GET['module']);
        include($module_directory . $class . $file_extension);
        $module = new $class;
        $module->process($file);
        xtc_redirect(xtc_href_link(FILENAME_MODULE_INSTALL, 'set=' . $_GET['set'] . '&module=' . $class));

        break;

    case 'install':
    case 'remove':
        $file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
        $class = basename($_GET['module']);
        if (file_exists($module_directory . $class . $file_extension)) {
            include($module_directory . $class . $file_extension);
            $module = new $class;
            if ($_GET['action'] == 'install') {
                $module->install();
            } elseif ($_GET['action'] == 'remove') {
                $module->remove();
            }
        }
        xtc_redirect(xtc_href_link(FILENAME_MODULE_INSTALL, 'set=' . $_GET['set'] . '&module=' . $class));
        break;
}

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td width="100%">
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr> 
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                            </tr>
                        </table> </td>
                </tr>
                <tr>
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top">
                                    <table width="100%" class="dataTable" cellspacing="0" cellpadding="0">
                                        <tr class="dataTableHeadingRow">
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODULES; ?></th>
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_VERSION; ?></th>
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_INSTALLED; ?></th>
                                            <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</th>
                                        </tr>
                                        <?php
                                        $file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
                                        $directory_array = array();
                                        if ($dir = @dir($module_directory)) {
                                            while ($file = $dir->read()) {
                                                if (!is_dir($module_directory . $file)) {
                                                    if (substr($file, strrpos($file, '.')) == $file_extension) {
                                                        $directory_array[] = $file;
                                                    }
                                                }
                                            }
                                            sort($directory_array);
                                            $dir->close();
                                        }

                                        $installed_modules = array();
                                        for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) {
                                            $file = $directory_array[$i];

                                            include($module_directory . $file);

                                            $class = substr($file, 0, strrpos($file, '.'));
                                            if (xtc_class_exists($class)) {
                                                $module = new $class;
                                                if ($module->check() > 0) {
                                                    if ($module->sort_order > 0) {
                                                        $installed_modules[$module->sort_order] = $file;
                                                    } else {
                                                        $installed_modules[] = $file;
                                                    }
                                                }

                                                if (((!$_GET['module']) || ($_GET['module'] == $class)) && (!$mInfo)) {
                                                    $module_info = array('code' => $module->code,
                                                        'title' => $module->title,
                                                        'description' => $module->description,
                                                        'status' => $module->check());

                                                    $module_keys = $module->keys();
                                                    $keys_extra = array();
                                                    for ($j = 0, $k = sizeof($module_keys); $j < $k; $j++) {
                                                        $key_value_query = xtc_db_query("select configuration_key,configuration_value, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_key = '" . $module_keys[$j] . "'");
                                                        $key_value = xtc_db_fetch_array($key_value_query);
                                                        if ($key_value['configuration_key'] != '')
                                                            $keys_extra[$module_keys[$j]]['title'] = constant(strtoupper($key_value['configuration_key'] . '_TITLE'));
                                                        $keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
                                                        if ($key_value['configuration_key'] != '')
                                                            $keys_extra[$module_keys[$j]]['description'] = constant(strtoupper($key_value['configuration_key'] . '_DESC'));
                                                        $keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
                                                        $keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
                                                    }

                                                    $module_info['keys'] = $keys_extra;

                                                    $mInfo = new objectInfo($module_info);
                                                }


                                                if ((is_object($mInfo)) && ($class == $mInfo->code)) {
                                                    if ($module->check() > 0) {
                                                        echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link(FILENAME_MODULE_INSTALL, 'set=' . $_GET['set'] . '&module=' . $class . '&action=edit') . '\'">' . "\n";
                                                    } else {
                                                        echo '<tr class="dataTableRowSelected">' . "\n";
                                                    }
                                                } else {
                                                    echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link(FILENAME_MODULE_INSTALL, 'set=' . $_GET['set'] . '&module=' . $class) . '\'">' . "\n";
                                                }
                                                ?>
                                                <td><?php echo $module->title; ?></td>
                                                <td><?php echo $module->version; ?></td>
                                                <td><?php if ($module->check() == 1) {
                                                    echo '<img src="images/icons/icon_add.png" />';
                                                } else {
                                                    echo '<img src="images/icons/exclamation.png" />';
                                                }; ?></td>
                                                <td align="right"><?php if ((is_object($mInfo)) && ($class == $mInfo->code)) {
                                            echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
                                        } else {
                                            echo '<a href="' . xtc_href_link(FILENAME_MODULE_INSTALL, 'set=' . $_GET['set'] . '&module=' . $class) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                        } ?>&nbsp;</td>

                                                <?php
                                                echo '</tr>';
                                            }
                                        }

                                        ksort($installed_modules);
                                        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '" . $module_key . "'");
                                        if (xtc_db_num_rows($check_query)) {
                                            $check = xtc_db_fetch_array($check_query);
                                            if ($check['configuration_value'] != implode(';', $installed_modules)) {
                                                xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . implode(';', $installed_modules) . "', last_modified = now() where configuration_key = '" . $module_key . "'");
                                            }
                                        } else {
                                            xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ( '" . $module_key . "', '" . implode(';', $installed_modules) . "','6', '0', now())");
                                        }
                                        ?>
                                        <tr>
                                            <td colspan="4" class="smallText"><?php echo TEXT_MODULE_DIRECTORY . ' admin/' . $module_directory; ?></td>
                                        </tr>
                                    </table></td>
                                <?php
                                $heading = array();
                                $contents = array();
                                switch ($_GET['action']) {


                                    default:
                                        $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');

                                        if ($mInfo->status == '1') {
                                            $keys = '';
                                            reset($mInfo->keys);
                                            while (list(, $value) = each($mInfo->keys)) {
                                                $keys .= '<b>' . $value['title'] . '</b><br />';
                                                if ($value['use_function']) {
                                                    $use_function = $value['use_function'];
                                                    if (preg_match('/->/', $use_function)) {
                                                        $class_method = explode('->', $use_function);
                                                        if (!is_object(${$class_method[0]})) {
                                                            include(DIR_WS_CLASSES . $class_method[0] . '.php');
                                                            ${$class_method[0]} = new $class_method[0]();
                                                        }
                                                        $keys .= xtc_call_function($class_method[1], $value['value'], ${$class_method[0]});
                                                    } else {
                                                        $keys .= xtc_call_function($use_function, $value['value']);
                                                    }
                                                } else {
                                                    if (strlen($value['value']) > 30) {
                                                        $keys .= substr($value['value'], 0, 30) . ' ...';
                                                    } else {
                                                        $keys .= $value['value'];
                                                    }
                                                }
                                                $keys .= '<br /><br />';
                                            }
                                            $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));

                                            $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_MODULE_INSTALL, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=remove') . '">' . BUTTON_MODULE_REMOVE . '</a>');
                                            $contents[] = array('text' => '<br />' . $mInfo->description);
                                            $contents[] = array('text' => '<br />' . $keys);
                                        } else {
                                            $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_MODULE_INSTALL, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=install') . '">' . BUTTON_MODULE_INSTALL . '</a>');
                                            $contents[] = array('text' => '<br />' . $mInfo->description);
                                        }
                                        break;
                                }

                                if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
                                    echo '            <td width="25%" class="border" valign="top">' . "\n";

                                    $box = new box;
                                    echo $box->infoBox($heading, $contents);

                                    echo '            </td>' . "\n";
                                }
                                ?>
                            </tr>
                        </table></td>
                </tr>
            </table></td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
