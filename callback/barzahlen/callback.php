<?php
/**
 * Barzahlen Payment Module (commerce:SEO)
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

if(isset($_GET['state']) && preg_match('/^refund_/', $_GET['state'])) {
    header("HTTP/1.1 200 OK");
    header("Status: 200 OK");
} else {
    require_once('model.ipn.php');
    chdir('../../');
    require_once('includes/application_top.php');

    $query = xtc_db_query("SELECT directory FROM " . TABLE_LANGUAGES . " WHERE code = '" . DEFAULT_LANGUAGE . "'");
    $result = xtc_db_fetch_array($query);
    require_once(DIR_WS_LANGUAGES . $result['directory'] . '/modules/payment/barzahlen.php');

    $ipn = new Barzahlen_IPN;

    if ($ipn->callback($_GET)) {
        header("HTTP/1.1 200 OK");
        header("Status: 200 OK");
    } else {
        header("HTTP/1.1 400 Bad Request");
        header("Status: 400 Bad Request");
        die();
    }
}
