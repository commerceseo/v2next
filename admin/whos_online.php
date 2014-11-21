<?php
/* -----------------------------------------------------------------
 * 	$Id: whos_online.php 1210 2014-09-22 09:04:30Z akausch $
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

// require_once('../includes/classes/class.xtcprice.php');

if (!function_exists("unserialize_session_data")) {
    function unserialize_session_data($session_data) {
        $variables = array();
        $a = preg_split("/(\w+)\|/", $session_data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        for ($i = 0; $i < count($a); $i = $i + 2) {
            $variables[$a[$i]] = unserialize($a[$i + 1]);
        }
        return( $variables );
    }
}

if (!function_exists("xtc_get_products")) {
    function xtc_get_products($session) {
        if (!is_array($session))
            return false;

        $products_array = array();
        reset($session);
        if ($session['cart']->contents != '') {
            while (list($products_id, ) = each($session['cart']->contents)) {
                $products_query = xtc_db_query("select p.products_id, pd.products_name,p.products_image, p.products_model, p.products_price, p.products_discount_allowed, p.products_weight, p.products_tax_class_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id='" . xtc_get_prid($products_id) . "' and pd.products_id = p.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "'");
                if ($products = xtc_db_fetch_array($products_query)) {
                    $prid = $products['products_id'];
                    $xtPrice = new xtcPrice($session['currency'], $session['customers_status']['customers_status_id']);
                    $products_price = $xtPrice->xtcGetPrice($products['products_id'], $format = false, $session['cart']->contents[$products_id]['qty'], $products['products_tax_class_id'], $products['products_price']);
                    $products_array[] = array('id' => $products_id,
                        'name' => $products['products_name'],
                        'model' => $products['products_model'],
                        'image' => $products['products_image'],
                        'price' => $products_price + attributes_price($products_id, $session),
                        'quantity' => $session['cart']->contents[$products_id]['qty'],
                        'weight' => $products['products_weight'],
                        'final_price' => ($products_price + attributes_price($products_id, $session)),
                        'tax_class_id' => $products['products_tax_class_id'],
                        'attributes' => $session['contents'][$products_id]['attributes']);
                }
            }
        }
        return $products_array;
    }
}

if (!function_exists("attributes_price")) {
    function attributes_price($products_id, $session) {
        $xtPrice = new xtcPrice($session['currency'], $session['customers_status']['customers_status_id']);
        if (isset($session['contents'][$products_id]['attributes'])) {
            reset($session['contents'][$products_id]['attributes']);
            while (list($option, $value) = each($session['contents'][$products_id]['attributes'])) {
                $attribute_price_query = xtc_db_query("select pd.products_tax_class_id, p.options_values_price, p.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " p, " . TABLE_PRODUCTS . " pd where p.products_id = '" . $products_id . "' and p.options_id = '" . $option . "' and pd.products_id = p.products_id and p.options_values_id = '" . $value . "'");
                $attribute_price = xtc_db_fetch_array($attribute_price_query);
                if ($attribute_price['price_prefix'] == '+') {
                    $attributes_price += $xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']);
                } else {
                    $attributes_price -= $xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']);
                }
            }
        }
        return $attributes_price;
    }
}

function xtc_check_cart($which) {
    if (STORE_SESSIONS == 'mysql') {
        $session_data = xtc_db_fetch_array(xtc_db_query("SELECT sesskey, value FROM " . TABLE_SESSIONS . " WHERE sesskey = '" . $which . "'"));
    } else {
        if ((file_exists(xtc_session_save_path() . '/sess_' . $which)) && (filesize(xtc_session_save_path() . '/sess_' . $which) > 0)) {
            $session_data = file(xtc_session_save_path() . '/sess_' . $which);
            $session_data = trim(implode('', $session_data));
        }
    }
    $which_query = $session_data;
	$which_query['value'] = base64_decode($which_query['value']);
// removed , host_address
    $who_query = xtc_db_fetch_array(xtc_db_query("SELECT session_id, time_entry, time_last_click
                                 FROM " . TABLE_WHOS_ONLINE . "
                                 WHERE session_id='" . $which . "'"));

    // longer than 3 minutes = inactive
    $xx_mins_ago_long = (time() - 180);
    switch (true) {
        // no cart
        case (strstr($which_query['value'], 's:3:"qty";i:0')):
            if ($who_query['time_last_click'] < $xx_mins_ago_long) {
                // inactive
                return xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', TEXT_STATUS_INACTIVE_NOCART, '10', '10');
            } else {
                // active
                return xtc_image(DIR_WS_IMAGES . 'icon_status_blue.gif', TEXT_STATUS_ACTIVE_NOCART, '10', '10');
            }
        // cart
        case (!strstr($which_query['value'], 's:3:"qty";i:0')):
            if ($who_query['time_last_click'] < $xx_mins_ago_long) {
                // inactive
                return xtc_image(DIR_WS_IMAGES . 'icon_status_yellow.gif', TEXT_STATUS_INACTIVE_CART, '10', '10');
            } else {
                // active
                return xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', TEXT_STATUS_ACTIVE_CART, '10', '10');
            }
    }
}

$xx_mins_ago = (time() - 900);
require_once('includes/application_top.php');
require_once(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();
xtc_db_query("DELETE FROM " . TABLE_WHOS_ONLINE . " WHERE time_last_click < '" . $xx_mins_ago . "'");
require_once(DIR_WS_INCLUDES . 'header.php');
?>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>"/ >
<?php if ($_SERVER["QUERY_STRING"] == 300) { ?>
          <meta http-equiv="refresh" content="300;URL=whos_online.php?300" />
<?php } elseif ($_SERVER["QUERY_STRING"] == 180) { ?>
    <meta http-equiv="refresh" content="180;URL=whos_online.php?180" />
<?php } elseif ($_SERVER["QUERY_STRING"] == 120) { ?>
    <meta http-equiv="refresh" content="120;URL=whos_online.php?120" />
<?php } elseif ($_SERVER["QUERY_STRING"] == 60) { ?>
    <meta http-equiv="refresh" content="60;URL=whos_online.php?60" />
<?php } elseif ($_SERVER["QUERY_STRING"] == 30) { ?>
    <meta http-equiv="refresh" content="30;URL=whos_online.php?30" />
<?php } else { ?>

<?php } ?>


<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td>
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading" width="60%">
<?php echo HEADING_TITLE; ?>
                                    <span class="dataTableContent fs85"><br /><?php echo TABLE_HEADING_RATE; ?></span>
                                    <input type='button' class="button" value='<?php echo RATE_NEVER; ?>' onclick="location.href = 'whos_online.php'">
                                    <input type='button' class="button" value='<?php echo RATE_NEVER_3MIN; ?>' onclick="location.href = 'whos_online.php?180'">
                                    <input type='button' class="button" value='<?php echo RATE_NEVER_2MIN; ?>' onclick="location.href = 'whos_online.php?120'">
                                    <input type='button' class="button" value='<?php echo RATE_NEVER_60SEC; ?>' onclick="location.href = 'whos_online.php?60'">
                                    <input type='button' class="button" value='<?php echo RATE_NEVER_30SEC; ?>' onclick="location.href = 'whos_online.php?30'">
                                </td>
                                <td width="40%">
                                    <?php
                                    echo
                                    xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', TEXT_STATUS_ACTIVE_CART, '10', '10') . '&nbsp;' . TEXT_STATUS_ACTIVE_CART . '<br />' .
                                    xtc_image(DIR_WS_IMAGES . 'icon_status_yellow.gif', TEXT_STATUS_INACTIVE_CART, '10', '10') . '&nbsp;' . TEXT_STATUS_INACTIVE_CART . '<br />' .
                                    xtc_image(DIR_WS_IMAGES . 'icon_status_white.gif', TEXT_STATUS_NO_SESSION_BOT, '10', '10') . '&nbsp;' . TEXT_STATUS_NO_SESSION_BOT . '<br />' .
                                    xtc_image(DIR_WS_IMAGES . 'icon_status_blue.gif', TEXT_STATUS_ACTIVE_NOCART, '10', '10') . '&nbsp;' . TEXT_STATUS_ACTIVE_NOCART . '<br />' .
                                    xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', TEXT_STATUS_INACTIVE_NOCART, '10', '10') . '&nbsp;' . TEXT_STATUS_INACTIVE_NOCART . '<br><br>';
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top"><table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                        <tr class="dataTableHeadingRow">
                                            <th class="dataTableHeadingContent">&nbsp;</th>
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_ONLINE; ?></th>
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_FULL_NAME; ?></th>
                                            <th class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_IP_ADDRESS; ?></th>
                                            <th class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_COUNTRY; ?></th>
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_ENTRY_TIME; ?></th>
                                            <th class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LAST_CLICK; ?></th>
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_HTTP_REFERER; ?></th>
                                            <th class="dataTableHeadingContent">Agent</th>
                                            <th class="dataTableHeadingContent last"><?php echo TABLE_HEADING_SHOPPING_CART; ?></th>
                                        </tr>
                                        <?php
                                        $whos_online_query = xtc_db_query("SELECT * FROM " . TABLE_WHOS_ONLINE . " ORDER BY time_last_click DESC, time_entry DESC;");
                                        $total_bots = 0;
                                        $total_me = 0;
                                        $td = 1;
                                        while ($whos_online = xtc_db_fetch_array($whos_online_query)) {
                                            if ($td == 1)
                                                $td_zelle = ' dunkel';
                                            else
                                                $td_zelle = '';
                                            $time_online = ($whos_online['time_last_click'] - $whos_online['time_entry']);
                                            if ((!isset($_GET['info']) || (isset($_GET['info']) && ($_GET['info'] == $whos_online['session_id']))) && !isset($info)) {
                                                $info = $whos_online['session_id'];
                                            }

                                            if ($old_array['ip_address'] == $whos_online['ip_address']) {
                                                $i++;
                                            }

                                            // spider detection??
                                            if (($whos_online['session_id'] == '') | (!(isset($whos_online['session_id'])))) {
                                                $noSessionDetected = '&nbsp;?&nbsp;';
                                            } else {
                                                $noSessionDetected = '';
                                            }

                                            if ($i % 2 == 0)
                                                $f = 'dataTableRow';
                                            else
                                                $f = '';
                                            ?>
                                            <tr class="<?php echo $f; ?>">
                                                <td class="dataTableContent" align="left" valign="top">
                                                <?php
                                                if ($noSessionDetected == '') {
                                                    echo '&nbsp;' . xtc_check_cart($whos_online['session_id']);
                                                } else {
                                                    echo '&nbsp;' . xtc_image(DIR_WS_IMAGES . 'icon_status_white.gif', TEXT_STATUS_NO_SESSION_BOT, '10', '10');
                                                }
                                                ?>
                                                </td>
                                                <td class="dataTableContent" valign="top"><?php echo gmdate('H:i:s', $time_online); ?></td>
                                                    <?php if ($whos_online['customer_id'] == 0) { ?> <!-- Guest -->
                                                    <td class="dataTableContent" valign="top"><?php echo $whos_online['full_name']; ?>
                                                    </td>
                                                <?php } else { ?>
                                                    <td class="dataTableContent" valign="top">
                                                    <?php
                                                    if (TRUSTED_SHOP_IP_LOG == 'false') {
                                                        echo TRUST_NAME_LOG;
                                                    } else {
                                                        ?>
                                                            <a href="customers.php?selected_box=customers&cID=<?php echo $whos_online['customer_id']; ?>&action=edit">
                                                        <?php echo $whos_online['full_name']; ?>
                                                            </a>
                                                    <?php } ?>
                                                    </td>
                                                <?php
                                                }
                                                if ($whos_online['ip_address'] == $_SERVER["REMOTE_ADDR"]) {
                                                    if (TRUSTED_SHOP_IP_LOG == 'true') {
                                                        echo '<td class="dataTableContent" align="center">' . ME . '</td>';
                                                    } else {
                                                        echo '<td class="dataTableContent" align="center">' . TRUST_IP_LOG . '</td>';
                                                    }
                                                    $total_me++;
                                                } else {

                                                    //-> open iptable.csv
                                                    $ip_data = DIR_FS_CATALOG . DIR_WS_INCLUDES . 'data/iptable.csv';
                                                    $delimiter = ',';

                                                    $found = false;
                                                    $ip_array = array();
                                                    if (file_exists($ip_data)) {
                                                        $file_list = file($ip_data);
                                                        for ($i = 0; $i < count($file_list); $i++) {
                                                            $ip_array = explode($delimiter, $file_list[$i]);
                                                            if (sizeof($ip_array) > 1 && substr($ip_array[0], 0, 1) != '#' && substr($ip_array[0], 0, 1) != '//') {
                                                                $ip_adress = $ip_array[0];
                                                                if (strncmp($whos_online['ip_address'], $ip_adress, 15) == 0 || $whos_online['ip_address'] == $ip_adress) {
                                                                    echo '<td class="dataTableContent" align="center" valign="top">' . $ip_array[1] . '</td>';
                                                                    $found = true;
                                                                    $total_bots++;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        $i = 0;
                                                    }
                                                    if ($found == false) {
                                                        if (TRUSTED_SHOP_IP_LOG == 'true') {
                                                            echo '<td class="dataTableContent" align="center" valign="top"><a href="http://apps.db.ripe.net/search/query.html?form_type=simple&full_query_string=&searchtext=' . $whos_online['ip_address'] . ' " target="_blank">' . $whos_online['ip_address'] . '</a></td>';
                                                        } else {
                                                            echo '<td class="dataTableContent" align="center" valign="top">' . TRUST_IP_LOG . '</td>';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <td class="dataTableContent" valign="top" align="center">
                                            <nobr>
                                                <?php
                                                if ($whos_online['ip_address'] == $_SERVER["REMOTE_ADDR"]) {
                                                    echo ME;
                                                } else {

                                                    $get_country = strtolower(substr($whos_online['user_language'], 3, 2));
                                                    // echo $get_country;
                                                    if (!$get_country) {
                                                        echo 'Bot';
                                                    } else {
                                                        $title = xtc_db_fetch_array(xtc_db_query("SELECT countries_name FROM " . TABLE_COUNTRIES . " WHERE countries_iso_code_2 = '" . $get_country . "'"));
                                                        if (file_exists(DIR_WS_IMAGES . 'flaggen/' . $get_country . '.png'))
                                                            echo '<img src="' . DIR_WS_IMAGES . 'flaggen/' . $get_country . '.png" title="' . $title['countries_name'] . '">';
                                                        else
                                                            echo $whos_online['user_language'];
                                                    }
                                                }
                                                ?>
                                            </nobr>
                                    </td>
                                    <td class="dataTableContent" align="center" valign="top"><?php echo date('H:i:s', $whos_online['time_last_click']); ?></td>
                                    <td class="dataTableContent" valign="top">
                                        <?php
                                        if (preg_match('/^(.*)' . xtc_session_name() . '=[a-f,0-9]+[&]*(.*)/i', $whos_online['last_page_url'], $array)) {
                                            echo $array[1] . $array[2];
                                        } else {
                                            echo htmlentities_wrapper($whos_online['last_page_url']);
                                        }
                                        ?>
                                    </td>
                                    <td class="dataTableContent" valign="top">
                                        <?php
                                        if ($whos_online['http_referer'] != '')
                                            echo htmlentities_wrapper($whos_online['http_referer']);
                                        else
                                            echo '&nbsp;';
                                        ?>
                                    </td>
                                    <td class="dataTableContent" valign="top">
                                        <?php
                                        if ($whos_online['user_agent'] != '') {
                                            $ua = explode("(", $whos_online['user_agent']);
                                            $ua2 = explode(";", $ua[1]);

                                            if ($ua2[0] == "compatible") {
                                                //IE's
                                                $device = $ua2[1];
                                                $os = $ua2[2];
                                            } else { //andere Browser
                                                $os = $ua2[0];

                                                if (strstr($ua2[1], "Firefox") || strstr($ua2[2], "Firefox"))
                                                    $device = "Firefox";
                                                else if (strstr($whos_online['user_agent'], "Opera"))
                                                    $device = "Opera";
                                                else if (strstr($ua[2], "Chrome"))
                                                    $device = "Chrome";
                                                else if (strstr($ua[2], "Safari"))
                                                    $device = "Safari";
                                                else {
                                                    $device = htmlentities_wrapper($whos_online['user_agent']);
                                                    $os = "Unbekannt";
                                                }
                                            }
                                            echo $os . " - " . $device;
                                        }
                                        else
                                            echo '&nbsp;';
                                        ?>
                                    </td>
                                    <td class="dataTableContent last" valign="top">
                                        <?php
                                        $session_data = '';
                                        $products = '';
                                        if (STORE_SESSIONS == 'mysql') {
                                            $session_data = xtc_db_fetch_array(xtc_db_query("SELECT value FROM " . TABLE_SESSIONS . " WHERE sesskey = '" . $whos_online['session_id'] . "';"));
                                            $session_data = base64_decode($session_data['value']);
                                        } else {
                                            if ((file_exists(xtc_session_save_path() . '/sess_' . $whos_online['session_id'])) && (filesize(xtc_session_save_path() . '/sess_' . $whos_online['session_id']) > 0)) {
                                                $session_data = file(xtc_session_save_path() . '/sess_' . $whos_online['session_id']);
                                                $session_data = trim(implode('', $session_data));
                                            }
                                        }
                                        $user_session = unserialize_session_data($session_data);
                                        if ($user_session) {
                                            $products = xtc_get_products($user_session);
                                            for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
                                                echo $products[$i]['quantity'] . ' x ' . $products[$i]['name'] . '<br />';
                                            }

                                            if (sizeof($products) > 0) {
                                                echo '<hr style="color:#ccc;" size="1" /><span style="display:block; text-align:right;font-weight:700">' . TEXT_SHOPPING_CART_SUBTOTAL . ':<br /> ' . sprintf("%.2F", $user_session['cart']->total) . ' ' . $user_session['currency'] . '</span>';
                                            } else {
                                                echo '&nbsp;';
                                            }
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                if ($td == 2)
                                    $td = 1;
                                else
                                    $td++;
                                $old_array = $whos_online;
                            }
                            ?>
<?php
if (isset($http_referer_url)) {
    ?>
                                <tr>
                                    <td class="smallText" colspan="8"><?php echo '<strong>' . TEXT_HTTP_REFERER_URL . ':</strong> <a href="' . $http_referer_url . '">' . $http_referer_url . '</a>'; ?></td>
                                </tr>
    <?php
}
?>
                        </table>
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
