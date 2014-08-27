<?php
/**
 * Barzahlen Payment Module (commerce:SEO)
 *
 * NOTICE OF LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @copyright   Copyright (c) 2013 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Mathias Hertlein
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once("barzahlen/BarzahlenHashCreate.php");
require_once("barzahlen/BarzahlenHttpClient.php");
require_once("barzahlen/BarzahlenPluginCheckRequest.php");
require_once("barzahlen/BarzahlenVersionCheck.php");
require_once("barzahlen/BarzahlenConfigRepository.php");

require_once(DIR_FS_LANGUAGES . $_SESSION['language'] . "/modules/payment/barzahlen.php");

$httpClient = new BarzahlenHttpClient();
$barzahlenVersionCheckRequest = new BarzahlenPluginCheckRequest($httpClient);
$barzahlenRepository = new BarzahlenConfigRepository();

$barzahlenVersionCheck = new BarzahlenVersionCheck($barzahlenVersionCheckRequest, $barzahlenRepository);

try {
    if (MODULE_PAYMENT_BARZAHLEN_STATUS == "True" && !$barzahlenVersionCheck->isCheckedInLastWeek(time())) {
        $barzahlenVersionCheck->check(MODULE_PAYMENT_BARZAHLEN_SHOPID, MODULE_PAYMENT_BARZAHLEN_PAYMENTKEY, PROJECT_VERSION);
        $displayUpdateAvailableMessage = $barzahlenVersionCheck->isNewVersionAvailable();
        $barzahlenNewestVersion = $barzahlenVersionCheck->getNewestVersion();
    } else {
        $displayUpdateAvailableMessage = false;
    }
} catch (Exception $e) {
    $displayUpdateAvailableMessage = false;
}
?>

<?php
if ($displayUpdateAvailableMessage) {
?>

<table style="background: #e10c0c;
background: -moz-linear-gradient(#e10c0c, #910c0c) repeat scroll 0 0 transparent;
background: -webkit-linear-gradient(#e10c0c, #910c0c) repeat scroll 0 0 transparent;
background: linear-gradient(#e10c0c, #910c0c) repeat scroll 0 0 transparent;
border: 1px solid #910c0c;
border-radius: 10px;
margin: 10px; color: #fff" border="0" width="98%" cellspacing="0" cellpadding="8">
    <tr>
        <td><?php echo sprintf(MODULE_PAYMENT_BARZAHLEN_NEW_VERSION, $barzahlenNewestVersion) ?></td>
    </tr>
</table>

<?php
}