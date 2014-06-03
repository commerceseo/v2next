<?php
/* --------------------------------------------------------------
 * 	$Id: hermes_config.php 879 2014-03-26 17:22:54Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------

  based on:
  hermes_collection.php 2012 gambio
  Gambio GmbH
  http://www.gambio.de
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommercecoding standards (a typical file) www.oscommerce.com
  (c) 2003	 nextcommerce ( start.php,v 1.6 2003/08/19); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: hermes_config.php 879 2014-03-26 17:22:54Z akausch $)


  Released under the GNU General Public License
  -------------------------------------------------------------- */

require('includes/application_top.php');
require DIR_FS_CATALOG . '/admin/includes/classes/class.messages.php';
require DIR_FS_CATALOG . '/includes/classes/class.hermes.php';

defined('GM_HTTP_SERVER') OR define('GM_HTTP_SERVER', HTTP_SERVER);
define('PAGE_URL', GM_HTTP_SERVER . DIR_WS_ADMIN . basename(__FILE__));

$hermes = new HermesAPI();
$messages = new Messages('hermes_messages');

$username = $hermes->getUsername();
$password = $hermes->getPassword();
$sandboxmode = $hermes->getSandboxmode();
$os_aftersave = $hermes->getOrdersStatusAfterSave();
$os_afterlabel = $hermes->getOrdersStatusAfterLabel();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hermes->setUsername($_POST['username']);
    $hermes->setPassword($_POST['password']);
    $hermes->setSandboxmode(isset($_POST['sandboxmode']));
    $hermes->setOrdersStatusAfterSave($_POST['os_aftersave']);
    $hermes->setOrdersStatusAfterLabel($_POST['os_afterlabel']);
    $messages->addMessage('Konfiguration gespeichert');
    xtc_redirect(PAGE_URL);
}

$orders_status = array();
$os_query = "SELECT * FROM orders_status WHERE language_id = :language_id ORDER BY orders_status_id";
$os_query = strtr($os_query, array(':language_id' => $_SESSION['languages_id']));
$os_result = xtc_db_query($os_query);
while ($os_row = xtc_db_fetch_array($os_result)) {
    $orders_status[$os_row['orders_status_id']] = $os_row['orders_status_name'];
}
$service_selected = array();
if ($service == 'PriPS') {
    $service_selected['PriPS'] = 'checked="checked"';
    $service_selected['ProPS'] = '';
} else {
    $service_selected['ProPS'] = 'checked="checked"';
    $service_selected['PriPS'] = '';
}
/* messages */
$session_messages = $messages->getMessages();
$messages->reset();
ob_start();
require(DIR_WS_INCLUDES . 'header.php');
?>
<style>
    .hermesorder p.message { background: #C5E6C5; border: 1px solid #A2D6A2; padding: 1ex 1em; color: #376e37; }
    .hermesorder dl.form { overflow: auto; }
    .hermesorder dl.form dt, dl.form dd { float: left; margin: .5ex 0; }
    .hermesorder dl.form dt { clear: left; width: 15em; }
    .hermesorder dl.form dt label:after { content: ':';}
    .hermesorder dl.form dt { margin-right: 1.5em; }
    .hermesorder input { vertical-align: middle; }
    .hermesorder input[type="text"] { width: 25em; }
</style>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr>
        <td class="boxCenter hermesorder" width="100%" valign="top">
            <h1>Konfiguration der Hermes-Schnittstelle</h1>

            <?php foreach ($session_messages as $msg): ?>
                <p class="message"><?php echo $msg ?></p>
            <?php endforeach ?>

            <form action="<?php echo PAGE_URL ?>" method="POST">
                <dl class="form">
                    <dd>
                        <input type="radio" name="service" value="ProPS" id="service_props" <?php echo $service_selected['ProPS'] ?>>
                        <label for="service_props">ProService</label><br>
                        <input type="radio" name="service" value="PriPS" id="service_prips" <?php echo $service_selected['PriPS'] ?>>
                        <label for="service_prips">Privatservice</label>
                    </dd>
                    <dt>
                    <label for="username">Benutzername</label>
                    </dt>
                    <dd>
                        <input id="username" name="username" type="text" value="<?php echo $username ?>">
                    </dd>
                    <dt>
                    <label for="password">Passwort</label>
                    </dt>
                    <dd>
                        <input id="password" name="password" type="text" value="<?php echo $password ?>">
                    </dd>
                    <dt>
                    <label for="sandboxmode">Sandbox-Mode</label>
                    </dt>
                    <dd>
                        <input type="checkbox" value="1" name="sandboxmode" id="sandboxmode" <?= $sandboxmode ? 'checked="checked"' : '' ?>>
                        (nur für Testbetrieb aktivieren)
                    </dd>
                    <dt>
                    <label for="os_aftersave">Bestellstatus nach Speichern eines Versandauftrags</label>
                    </dt>
                    <dd>
                        <select id="os_aftersave" name="os_aftersave">
                            <option value="-1" <?php echo $os_aftersave == '-1' ? 'selected="selected"' : '' ?>>nicht &auml;ndern</option>
                            <?php foreach ($orders_status as $os_id => $os_name): ?>
                                <option value="<?php echo $os_id ?>" <?php echo $os_aftersave == $os_id ? 'selected="selected"' : '' ?>><?php echo $os_name ?></option>
                            <?php endforeach ?>
                        </select>
                    </dd>
                    <dt>
                    <label for="os_aftersave">Bestellstatus nach Erzeugen eines Versandlabels</label>
                    </dt>
                    <dd>
                        <select id="os_afterlabel" name="os_afterlabel">
                            <option value="-1" <?php echo $os_afterlabel == '-1' ? 'selected="selected"' : '' ?>>nicht &auml;ndern</option>
                            <?php foreach ($orders_status as $os_id => $os_name): ?>
                                <option value="<?php echo $os_id ?>" <?php echo $os_afterlabel == $os_id ? 'selected="selected"' : '' ?>><?php echo $os_name ?></option>
                            <?php endforeach ?>
                        </select>
                    </dd>
                </dl>
                <input class="button" type="submit" value="speichern">
            </form>
        </td>
    </tr>
</table>
<script>
    $(function() {
        $('#hermesconfig').delegate('input[name="service"]', 'change', function(e) {
            var service = $(this).val();
            if (service != 'ProPS') {
                $('.props_only').hide('fast');
            }
            else {
                $('.props_only').show('fast');
            }
        });
        $('input[name="service"]:checked').change();
    });
</script>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
