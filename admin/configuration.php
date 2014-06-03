<?php
/* -----------------------------------------------------------------
 * 	$Id: configuration.php 948 2014-04-09 11:30:00Z akausch $
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

if ($_GET['action']) {
    switch ($_GET['action']) {
        case 'save':

            if ($_GET['gID'] == '31') {

                // email check
                if (isset($_POST['_PAYMENT_MONEYBOOKERS_EMAILID'])) {

                    $url = 'https://www.moneybookers.com/app/email_check.pl?email=' . $_POST['_PAYMENT_MONEYBOOKERS_EMAILID'] . '&cust_id=8644877&password=1a28e429ac2fcd036aa7d789ebbfb3b0';

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                    $result = curl_exec($ch);
                    if ($result == 'NOK') {
                        $messageStack->add_session(MB_ERROR_NO_MERCHANT, 'error');
                    }

                    if (strstr($result, 'OK,')) {
                        $data = explode(',', $result);
                        $_POST['_PAYMENT_MONEYBOOKERS_MERCHANTID'] = $data[1];
                        $messageStack->add_session(sprintf(MB_MERCHANT_OK, $data[1]), 'success');
                    }
                }
            }

            $configuration_query = xtc_db_query("SELECT configuration_key, configuration_id, configuration_value, use_function, set_function FROM " . TABLE_CONFIGURATION . " WHERE configuration_group_id = '" . (int) $_GET['gID'] . "' ORDER BY sort_order");
            while ($configuration = xtc_db_fetch_array($configuration_query)) {
                xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . $_POST[$configuration['configuration_key']] . "' WHERE configuration_key='" . $configuration['configuration_key'] . "'");
            }
            xtc_redirect(FILENAME_CONFIGURATION . '?gID=' . (int) $_GET['gID']);
            break;
    }
}

$cfg_group_query = xtc_db_query("SELECT configuration_group_title FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id = '" . (int) $_GET['gID'] . "'");
$cfg_group = xtc_db_fetch_array($cfg_group_query);

require(DIR_WS_INCLUDES . 'header.php');
echo xtc_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . (int) $_GET['gID'] . '&action=save');
?>
<div class="row">
    <div class="col-xs-12">
        <h1><?php echo $cfg_group['configuration_group_title']; ?></h1>
    </div>
    <div class="col-xs-12 text-right">
        <input type="submit" class="button" value="<?php echo BUTTON_SAVE ?>"/>
    </div>
    <div class="col-xs-12">
        <?php
        switch ($_GET['gID']) {
            case 21:
            case 31:
            case 19:
            case 25:
                echo '<a class="button" href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=21', 'NONSSL') . '">Afterbuy</a> 
											<a class="button" href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=31', 'NONSSL') . '">Skrill</a> 
											<a class="button" href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=19', 'NONSSL') . '">Google Conversion</a> 
											<a class="button" href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=25', 'NONSSL') . '">PayPal</a>';

                if ($_GET['gID'] == '31')
                    echo MB_INFO;
                break;
        }
        ?>
        <table class="table table-striped">
            <?php
            $configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '" . (int) $_GET['gID'] . "' order by sort_order");
            $i = 1;
            while ($configuration = xtc_db_fetch_array($configuration_query)) {
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

                if (((!$_GET['cID']) || ($_GET['cID'] == $configuration['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
                    $cfg_extra = xtc_db_fetch_array(xtc_db_query("select configuration_key,configuration_value, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'"));
                    $cInfo_array = xtc_array_merge($configuration, $cfg_extra);
                    $cInfo = new objectInfo($cInfo_array);
                }
                if ($configuration['set_function']) {
                    eval('$value_field = ' . $configuration['set_function'] . '"' . htmlspecialchars($configuration['configuration_value']) . '");');
                } else {
                    $value_field = xtc_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'], 'size=40');
                }
                // add

                if (strstr($value_field, 'configuration_value')) {
                    $value_field = str_replace('configuration_value', $configuration['configuration_key'], $value_field);
                }

                echo '
											  <tr>
												<td>
													<b>' . constant(strtoupper($configuration['configuration_key'] . '_TITLE')) . '</b>
												</td>
												<td>' . $value_field . '</td>
												<td>' . constant(strtoupper($configuration['configuration_key'] . '_DESC')) . '</td>
											  </tr>
											  ';

                $i++;
            }
            ?>
        </table>
    </div>
    <div class="col-xs-12 text-right">
        <input type="submit" class="button" value="<?php echo BUTTON_SAVE ?>"/>
        </form>
    </div>
</div>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
    