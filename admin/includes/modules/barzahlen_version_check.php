<?php
/**
 * Barzahlen Payment Module (commerce:SEO)
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Mathias Hertlein
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once("barzahlen/BarzahlenHttpClient.php");
require_once("barzahlen/BarzahlenPluginCheckRequest.php");
require_once("barzahlen/BarzahlenVersionCheck.php");

require_once(DIR_FS_LANGUAGES . $_SESSION['language'] . "/modules/payment/barzahlen.php");

$httpClient = new BarzahlenHttpClient();
$barzahlenVersionCheckRequest = new BarzahlenPluginCheckRequest($httpClient);
$barzahlenVersionCheck = new BarzahlenVersionCheck($barzahlenVersionCheckRequest);

try {
    if (MODULE_PAYMENT_BARZAHLEN_STATUS == "True" && !$barzahlenVersionCheck->isCheckedInLastWeek()) {
        $barzahlenVersionCheck->check(MODULE_PAYMENT_BARZAHLEN_SHOPID, PROJECT_VERSION);
        $displayUpdateAvailableMessage = $barzahlenVersionCheck->isNewVersionAvailable();
    } else {
        $displayUpdateAvailableMessage = false;
    }
} catch (Exception $e) {
    error_log('barzahlen/versioncheck: ' . $e, 3, DIR_FS_CATALOG . 'logfiles/barzahlen.log');
    $displayUpdateAvailableMessage = false;
}

if ($displayUpdateAvailableMessage) {
    echo '<table style="background: #e10c0c; background: -moz-linear-gradient(#e10c0c, #910c0c) repeat scroll 0 0 transparent; background: -webkit-linear-gradient(#e10c0c, #910c0c) repeat scroll 0 0 transparent; background: linear-gradient(#e10c0c, #910c0c) repeat scroll 0 0 transparent; border: 1px solid #910c0c; border-radius: 10px; margin: 10px; color: #fff" border="0" width="98%" cellspacing="0" cellpadding="8"><tr><td>';
    echo sprintf(MODULE_PAYMENT_BARZAHLEN_NEW_VERSION, $barzahlenVersionCheck->getNewestVersion(), $barzahlenVersionCheck->getNewestVersionUrl());
    echo '</td></tr></table>';
}
