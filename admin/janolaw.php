<?php
/* -----------------------------------------------------------------
 * 	$Id: janolaw.php 420 2013-06-19 18:04:39Z akausch $
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

if (isset($_GET['action']) && $_GET['action'] == 'save') {
    $janolaw_query = xtc_db_query("SELECT configuration_key,configuration_id, configuration_value, use_function,set_function FROM " . TABLE_CONFIGURATION . " WHERE configuration_group_id = '24' order by sort_order");

    while ($janolaw = xtc_db_fetch_array($janolaw_query))
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . $_POST[$janolaw['configuration_key']] . "' WHERE configuration_key='" . $janolaw['configuration_key'] . "'");

    xtc_redirect('janolaw.php');
}

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" width="100%">
            <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="pageHeading">
                        <?php echo HEADING_TITLE; ?>
                    </td>
                </tr>
            </table>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">

                <tr>
                    <td valign="top" align="left">
                        <?php echo xtc_draw_form('janolaw', 'janolaw.php', 'action=save'); ?>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="4">
                            <tr>
                                <td colspan="2">
                                    <?php
                                    //echo DESC_1;
                                    if (function_exists(curl_init)) {
                                        $data = curl_init('http://www.janolaw.de/partner/webdesign-erfurt-header.html');
                                        curl_setopt($data, CURLOPT_RETURNTRANSFER, 1);
                                        $output = curl_exec($data);

                                        curl_close($data);
                                    } elseif ($file = file_get_contents('http://www.janolaw.de/partner/webdesign-erfurt-header.html')) {
                                        $output = $file;
                                    } else {
                                        $host = 'janolaw.de';
                                        $uri = '/partner/webdesign-erfurt-header.html';

                                        header("Content-type: text/plain");
                                        $sock = fsockopen($host, 80, $errno, $errstr, 5);
                                        fputs($sock, "GET " . $uri . " HTTP/1.1\r\n");
                                        fputs($sock, "Host: " . $host . "\r\n");
                                        fputs($sock, "Connection: close\r\n\r\n");
                                        $result = array();
                                        while (!feof($sock))
                                            $result[] = fgets($sock, 4096);
                                        fclose($sock);
                                        if (!empty($result['1'])) {

                                            for ($i = 1, $size = sizeof($result); $i < $size; ++$i) {
                                                if (!empty($result[$i]) || $result[$i] != '0')
                                                    $output .= $result[$i];
                                            }
                                        }
                                    }
                                    echo $output;
                                    ?>
                                </td>
                            </tr>	
<?php
$janolaw_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '24' order by sort_order");
while ($janolaw = xtc_db_fetch_array($janolaw_query)) {
    if (xtc_not_null($janolaw['use_function'])) {
        $use_function = $janolaw['use_function'];
        if (preg_match('/->/', $use_function)) {
            $class_method = explode('->', $use_function);
            if (!is_object(${$class_method[0]})) {
                include(DIR_WS_CLASSES . $class_method[0] . '.php');
                ${$class_method[0]} = new $class_method[0]();
            }
            $cfgValue = xtc_call_function($class_method[1], $janolaw['configuration_value'], ${$class_method[0]});
        } else {
            $cfgValue = xtc_call_function($use_function, $janolaw['configuration_value']);
        }
    } else {
        $cfgValue = $janolaw['configuration_value'];
    }

    if (((!$_GET['cID']) || (@$_GET['cID'] == $janolaw['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
        $cfg_extra_query = xtc_db_query("select configuration_key,configuration_value, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $janolaw['configuration_id'] . "'");
        $cfg_extra = xtc_db_fetch_array($cfg_extra_query);

        $cInfo_array = xtc_array_merge($janolaw, $cfg_extra);
        $cInfo = new objectInfo($cInfo_array);
    }
    if ($configuration['set_function']) {
        eval('$value_field = ' . $janolaw['set_function'] . '"' . htmlspecialchars($janolaw['configuration_value']) . '");');
    } else {
        $value_field = xtc_draw_input_field($janolaw['configuration_key'], $janolaw['configuration_value'], 'size=40');
    }
    if (strstr($value_field, 'configuration_value'))
        $value_field = str_replace('configuration_value', $janolaw['configuration_key'], $value_field);

    if ($i % 2 == 0)
        $f = '';
    else
        $f = 'dataTableRow';
    echo '
						  <tr class="' . $f . '" >
							<td width="30%" valign="top" style="border-top: 1px dashed #ccc">
								<b>' . constant(strtoupper($janolaw['configuration_key'] . '_TITLE')) . '</b>
							</td>
							<td valign="top" align="left" style="border-top: 1px dashed #ccc">
							<table width="100%"  border="0" cellspacing="0" cellpadding="2">
							  <tr>
								<td>' . $value_field . '</td>
							  </tr>
							</table>
							<br />' . constant(strtoupper($janolaw['configuration_key'] . '_DESC')) . '</td>
						  </tr>
						  ';

    $i++;
}
?>
                            <tr>
                                <td align="right" colspan="2">
                                    <input type="submit" class="button" onClick="this.blur();" value="<?php echo BUTTON_SAVE ?>"/>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
