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
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

if(isset($_GET['state']) && preg_match('/^refund_/', $_GET['state'])) {
    header("HTTP/1.1 200 OK");
    header("Status: 200 OK");
    die();
}

require_once('model.ipn.php');
chdir('../../');
require_once('includes/application_top.php');
$query = xtc_db_query("SELECT directory FROM " . TABLE_LANGUAGES . " WHERE code = '" . DEFAULT_LANGUAGE . "'");
$result = xtc_db_fetch_array($query);
require_once(DIR_WS_LANGUAGES . $result['directory'] . '/modules/payment/barzahlen.php');

$ipn = new BZ_Ipn;

if ($ipn->sendResponseHeader($_GET)) {
    header("HTTP/1.1 200 OK");
    header("Status: 200 OK");
    $ipn->updateDatabase();
} else {
    header("HTTP/1.1 400 Bad Request");
    header("Status: 400 Bad Request");
}
