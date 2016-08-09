<?php
/* -----------------------------------------------------------------
 * 	$Id: start.php 1333 2014-12-18 23:13:25Z akausch $
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

require ('includes/application_top.php');
$smarty = new Smarty;

$is_htaccess=(file_exists('./.htpasswd'));
if ($is_htaccess) {
	$htaccess_not_exists = false;
} else {
	$htaccess_not_exists = true;
}
	
// if (isset($_POST['htaccess'])||$action=='schutz') include ('includes/modules/protection_create.php');
// if ($action=='edithtaccess') include ('includes/modules/protection_edit.php');
// if ($action=='deletehtaccess') include ('includes/modules/protection_delete.php');

$aa = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . $_SESSION['customer_id'] . "'"));
$cs = $_SESSION['customers_status']['customers_status_id'];

if (($cs == '0') && ($aa['stats_sales_report'] == '1')) {

    $customers = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(customers_id) AS count FROM " . TABLE_CUSTOMERS));
    $customers_gast = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(customers_id) AS count FROM " . TABLE_CUSTOMERS . " WHERE customers_status='1'"));
    $customers_neukunde = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(customers_id) AS count FROM " . TABLE_CUSTOMERS . " WHERE customers_status='".DEFAULT_CUSTOMERS_STATUS_ID."'"));
    $customers_haendler = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(customers_id) AS count FROM " . TABLE_CUSTOMERS . " WHERE customers_status='3'"));
    $customers_rest = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(customers_id) AS count FROM " . TABLE_CUSTOMERS . " WHERE customers_status>'3'"));


    $products = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(products_id) AS count FROM " . TABLE_PRODUCTS . " WHERE products_status = '1'"));
    $products1 = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(products_id) AS count FROM " . TABLE_PRODUCTS . " WHERE products_status = '0'"));
    $products2 = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(products_id) AS count FROM " . TABLE_PRODUCTS . ""));
    $category = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(categories_id) AS count FROM " . TABLE_CATEGORIES . " WHERE categories_status = '1'"));
    $category2 = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(categories_id) AS count FROM " . TABLE_CATEGORIES . " WHERE categories_status = '0'"));

    $orders0 = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(orders_id) AS count FROM " . TABLE_ORDERS . " WHERE orders_status > '3' OR orders_status <= '0'"));
    $orders1 = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(orders_id) AS count FROM " . TABLE_ORDERS . " WHERE orders_status = '1'"));
    $orders2 = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(orders_id) AS count FROM " . TABLE_ORDERS . " WHERE orders_status = '2'"));
    $orders3 = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(orders_id) AS count FROM " . TABLE_ORDERS . " WHERE orders_status = '3'"));
    $specials = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(specials_id) AS count FROM " . TABLE_SPECIALS . " WHERE status = '1';"));

    $datelastyear = date("Y");
    $datethisyear = date("Y");
    if (date("m") == '01') {
        $datelastmonth = '12';
        $datelastyear = $datelastyear - 1;
    } else {
        $datelastmonth = date("m") - 1;
    }
    $datethismonth = date("m");
    $months = array('1' => '01', '2' => '02', '3' => '03', '4' => '04', '5' => '05', '6' => '06', '7' => '07', '8' => '08', '9' => '09', '10' => '10', '11' => '11', '12' => '12');

    $orders_today = xtc_db_fetch_array(xtc_db_query("SELECT 
														sum(ot.value) 
													FROM 
														" . TABLE_ORDERS . " o 
													INNER JOIN
														" . TABLE_ORDERS_TOTAL . " ot ON(ot.orders_id = o.orders_id AND ot.class = 'ot_total')
													WHERE 
														o.date_purchased LIKE '" . date("Y-m-d") . "%';"));
													
    $orders_yesterday = xtc_db_fetch_array(xtc_db_query("SELECT 
															sum(ot.value) 
														FROM 
															" . TABLE_ORDERS . " o
														INNER JOIN														
															" . TABLE_ORDERS_TOTAL . " ot ON(ot.orders_id = o.orders_id AND ot.class = 'ot_total')
														WHERE 
															o.date_purchased LIKE '" . date("Y-m-d", time() - 86400) . "%';"));
														
    $orders_thismonth = xtc_db_fetch_array(xtc_db_query("SELECT 
															sum(ot.value) 
														FROM 
															" . TABLE_ORDERS . " o
														INNER JOIN
															" . TABLE_ORDERS_TOTAL . " ot ON(ot.orders_id = o.orders_id AND ot.class = 'ot_total')
														WHERE 
															o.date_purchased LIKE '" . date('Y') . "-" . date('m') . "%';"));
														
    $orders_lastmonth = xtc_db_fetch_array(xtc_db_query("SELECT 
															sum(ot.value) 
														FROM 
															" . TABLE_ORDERS . " o
														INNER JOIN
															" . TABLE_ORDERS_TOTAL . " ot ON(ot.orders_id = o.orders_id AND ot.class = 'ot_total')
														WHERE 
															o.date_purchased LIKE '" . $datelastyear . "-" . $months[$datelastmonth] . "%';"));
														
    $orders_lastmonth_bereinigt = xtc_db_fetch_array(xtc_db_query("SELECT 
																	sum(ot.value) 
																FROM 
																	" . TABLE_ORDERS . " o
																INNER JOIN
																	" . TABLE_ORDERS_TOTAL . " ot ON(ot.orders_id = o.orders_id AND ot.class = 'ot_total')
																WHERE 
																	o.orders_status NOT LIKE 1 
																AND 
																	o.date_purchased LIKE '" . $datelastyear . "-" . $months[$datelastmonth] . "%';"));
																	
    $orders_total = xtc_db_fetch_array(xtc_db_query("SELECT 
														sum(ot.value) 
													FROM 
														" . TABLE_ORDERS . " o
													INNER JOIN
														" . TABLE_ORDERS_TOTAL . " ot ON(ot.orders_id = o.orders_id AND ot.class = 'ot_total')
													;"));
													
    $orders_thisyear = xtc_db_fetch_array(xtc_db_query("SELECT 
															sum(ot.value) 
														FROM 
															" . TABLE_ORDERS . " o
														INNER JOIN
															" . TABLE_ORDERS_TOTAL . " ot ON(ot.orders_id = o.orders_id AND ot.class = 'ot_total')
														WHERE 
															o.date_purchased LIKE '" . $datethisyear . "%';"));	

	$datelastyearn = $datethisyear -1;
	$orders_lastyear = xtc_db_fetch_array(xtc_db_query("SELECT 
															sum(ot.value) 
														FROM 
															" . TABLE_ORDERS . " o
														INNER JOIN
															" . TABLE_ORDERS_TOTAL . " ot ON(ot.orders_id = o.orders_id AND ot.class = 'ot_total')
														WHERE 
															o.date_purchased LIKE '" . $datelastyearn . "%';"));

}

require(DIR_WS_INCLUDES . 'header.php');

include(DIR_WS_MODULES . FILENAME_SECURITY_CHECK);
// if ($htaccess_not_exists) {
	// echo '<div class="myerrorlog">Achtung: Sie können einen Verzeichnisschutz für den Admin anlegen, das erhöht die Sicherheit!</div>';
// }	

 ?>
    <?php if (($cs == '0') && ($aa['stats_sales_report'] == '1')) { ?>
<div class="panel-group" id="accordion">
<div class="panel panel-default">
<div class="panel-heading pointer" data-toggle="collapse" href="#collapse1">
      <h4 class="panel-title left"><i class="glyphicon glyphicon-signal"></i> Statistik</h4>
	  <div class="collapse-indicator text-right">+</div>
	  <div class="clear">&nbsp;</div>
  </div>
	 <div id="collapse1" class="panel-collapse collapse in"> 
		<div class="table-responsive panel-body">
            <table class="table table-bordered">
                <tr>
                    <td class="col-xs-6">
                        <table>
                            <tr>
                                <td style="background:#FCF5DD">Umsatz heute:</td>
                                <td  style="background:#FCF5DD" align="right"> <?php echo number_format($orders_today['sum(ot.value)'], 2); ?>&euro;</td>
                            </tr>
                            <tr>
                                <td style="background:#FCF5DD">Umsatz gestern:</td>
                                <td style="background:#FCF5DD" align="right"><?php echo number_format($orders_yesterday['sum(ot.value)'], 2); ?>&euro;</td>
                            </tr>
                            <tr>
                                <td style="background:#FCF5DD">aktueller Monat:</td>
                                <td style="background:#FCF5DD" align="right"> <?php echo number_format($orders_thismonth['sum(ot.value)'], 2); ?>&euro;</td>
                            </tr>
                            <tr>
                                <td style="background:#F1F1F1">letzter Monat (alle):</td>
                                <td style="background:#F1F1F1" align="right"><?php echo number_format($orders_lastmonth['sum(ot.value)'], 2); ?>&euro;</td>
                            </tr>
                            <tr>
                                <td style="background:#F1F1F1">letzter Monat (bezahlt):</td>
                                <td style="background:#F1F1F1" align="right"><?php echo number_format($orders_lastmonth_bereinigt['sum(ot.value)'], 2); ?>&euro;</td>
                            </tr>
                            <tr>
                                <td style="background:#E8E8E8">Umsatz aktuelles Jahr:</td>
                                <td style="background:#E8E8E8" align="right"><?php echo number_format($orders_thisyear['sum(ot.value)'], 2); ?>&euro;</td>
                            </tr>
                            <tr>
                                <td style="background:#E8E8E8">Umsatz letztes Jahr:</td>
                                <td style="background:#E8E8E8" align="right"><?php echo number_format($orders_lastyear['sum(ot.value)'], 2); ?>&euro;</td>
                            </tr>
                            <tr>
                                <td style="background:#E8E8E8">Umsatz gesamt:</td>
                                <td style="background:#E8E8E8" align="right"><?php echo number_format($orders_total['sum(ot.value)'], 2); ?>&euro;</td>
                            </tr>
                        </table>
                    </td>
                    <td class="col-xs-6">
                        <table width="100%" class="dataTableStart">
                            <tr>
                                <td>Aktive Artikel:</td>
                                <td align="center"> <?php echo $products['count']; ?></td>
                            </tr>
                            <tr>
                                <td>Inaktive Artikel:</td>
                                <td align="center"> <?php echo $products1['count']; ?></td>
                            </tr>
                            <tr>
                                <td>Artikel gesamt:</td>
                                <td align="center"><?php echo $products2['count'] ?></td>
                            </tr>
                            <tr>
                                <td>Aktive Kategorien:</td>
                                <td align="center"><?php echo $category['count'] ?></td>
                            </tr>
                            <tr>
                                <td>Inaktive Kategorien:</td>
                                <td align="center"><?php echo $category2['count'] ?></td>
                            </tr>
                            <tr>
                                <td>Sonderangebote:</td>
                                <td align="center"><?php echo $specials['count']; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
	</div>
	</div>
<?php } ?>
    <?php if (ADMIN_CSEO_START_WHOISONLINE == 'true') { ?>
  <div class="panel panel-default">
<div class="panel-heading pointer" data-toggle="collapse" href="#collapse2">
      <h4 class="panel-title left"><i class="glyphicon glyphicon-eye-open"></i> Besucher</a></h4>
	  <div class="collapse-indicator text-right">+</div>
	  <div class="clear">&nbsp;</div>
  </div>
	 <div id="collapse2" class="panel-collapse collapse in"> 
		<div class="table-responsive panel-body">
            <table class="table table-striped table-bordered table-hover">
                <tr>
                    <th class="col-xs-3">Online seit (min.)</th>
                    <th class="col-xs-3">Name</th>
                    <th class="col-xs-3">Letzter Klick</th>
                    <th class="col-xs-3">letzte Seite</th>
                </tr>
    <?php
    $whos_online_query = xtc_db_query("SELECT customer_id, full_name, ip_address, time_entry, time_last_click, last_page_url, session_id FROM " . TABLE_WHOS_ONLINE . " ORDER BY time_last_click desc LIMIT 10");
    while ($whos_online = xtc_db_fetch_array($whos_online_query)) {
        $time_online = (time() - $whos_online['time_entry']);
        if (((!$_GET['info']) || (@$_GET['info'] == $whos_online['session_id'])) && (!$info)) {
            $info = $whos_online['session_id'];
        }
        ?>
                    <tr>
                        <td><?php echo gmdate('H:i:s', $time_online); ?></td>
                        <td><?php echo $whos_online['full_name']; ?> <a href="whos_online.php?info=<?php echo $whos_online['session_id']; ?>"><i class="glyphicon glyphicon-eye-open"></i></a></td>
                        <td><?php echo date('H:i:s', $whos_online['time_last_click']); ?></td>
                        <td><?php echo $whos_online['last_page_url']; ?></td>
                    </tr>

        <?php } ?>
            </table>
        </div>
        </div>
        </div>
    <?php } ?>
    <?php if (ADMIN_CSEO_START_ORDERS == 'true') { ?>
  <div class="panel panel-default">
<div class="panel-heading pointer" data-toggle="collapse" href="#collapse3">
      <h4 class="panel-title left"><i class="glyphicon glyphicon-user"></i> Kunden (<b>gesamt: <?php echo $customers['count']; ?></b><?php if ($customers_gast['count'] > 0) {echo ' | Gast: ' . $customers_gast['count'];} ?><?php if ($customers_neukunde['count'] > 0) {echo ' | Neukunden: ' . $customers_neukunde['count'];} ?> <?php if ($customers_haendler['count'] > 0) {echo ' | Händler: ' . $customers_haendler['count'];} ?><?php if ($customers_rest['count'] > 0) {echo ' | Rest: ' . $customers_rest['count'];} ?>)</h4>
	  <div class="collapse-indicator text-right">+</div>
	  <div class="clear">&nbsp;</div>
  </div>
	 <div id="collapse3" class="panel-collapse collapse in"> 
		<div class="table-responsive panel-body">
			<table class="table table-striped table-bordered">
				<tr>
					<th class="col-xs-3">Name</td>
					<th class="col-xs-3">Vorname</td>
					<th class="col-xs-3">angemeldet am</td>
					<th class="col-xs-3">Bestellungen</td>
				</tr>
				<?php
				$ergebnis = xtc_db_query("SELECT * FROM customers ORDER BY customers_date_added DESC LIMIT 10;");
				while ($row = xtc_db_fetch_array($ergebnis)) {
					?>
					<tr>
						<td><?php echo $row['customers_lastname']; ?><a href="customers.php?page=1&cID=<?php echo $row['customers_id']; ?>&action=edit"><i class="glyphicon glyphicon-edit"></i></a></td>
						<td><?php echo $row['customers_firstname']; ?></td>
						<td><?php echo $row['customers_date_added']; ?></td>
						<td><a href="orders.php?cID=<?php echo $row['customers_id']; ?>"><i class="glyphicon glyphicon-eye-open"></i></a></td>
					</tr>
					<?php } ?>
			</table>
		</div>
	</div>
</div>
  <div class="panel panel-default">
<div class="panel-heading pointer" data-toggle="collapse" href="#collapse4">
      <h4 class="panel-title left"><i class="glyphicon glyphicon-shopping-cart"></i> Bestellungen (<b>Offen: <?php echo $orders1['count']; ?></b><?php if ($orders2['count'] > 0) {echo ' | In Bearbeitung: ' . $orders2['count'];} ?><?php if ($orders3['count'] > 0) {echo ' | Versendet: '.$orders3['count'];} ?><?php if ($orders0['count'] > 0) {echo ' | Weitere: '.$orders0['count'];} ?>)</h4>
	  <div class="collapse-indicator text-right">+</div>
	  <div class="clear">&nbsp;</div>
  </div>
	 <div id="collapse4" class="panel-collapse collapse in"> 
		<div class="table-responsive panel-body">
			<table class="table table-striped table-bordered">
				<tr>
					<th class="col-xs-3">Best-Nr.</td>
					<th class="col-xs-3">Bestelldatum</td>
					<th class="col-xs-3">Kundenname</td>
					<th class="col-xs-3">Gesamt</td>
				</tr>
				<?php
				$ergebnis = xtc_db_query("SELECT * FROM orders ORDER BY orders_id DESC LIMIT 10;");
				while ($row = xtc_db_fetch_array($ergebnis)) {
					$preis = xtc_db_fetch_array(xtc_db_query("SELECT text FROM orders_total WHERE orders_id = '" . $row['orders_id'] . "' AND class = 'ot_total' "));
					?>
					<tr>
						<td><?php echo $row['orders_id']; ?><a href="orders.php?page=1&oID=<?php echo $row['orders_id']; ?>&action=edit"><i class="glyphicon glyphicon-edit"></i></a></td>
						<td><?php echo $row['date_purchased']; ?></td>
						<td><?php echo $row['delivery_name']; ?></td>
						<td><?php echo strip_tags($preis['text']); ?></td>
					</tr>
					<?php } ?>
			</table>
        </div>
        </div>
        </div>
    <?php } ?>
<?php if (ADMIN_CSEO_START_BIRTHDAY == 'true') { ?>
  <div class="panel panel-default">
<div class="panel-heading pointer" data-toggle="collapse" href="#collapse5">
      <h4 class="panel-title left"><i class="glyphicon glyphicon-gift"></i> Geburtstage</h4>
	  <div class="collapse-indicator text-right">+</div>
	  <div class="clear">&nbsp;</div>
  </div>
	 <div id="collapse5" class="panel-collapse collapse in"> 
		<div class="table-responsive panel-body">
        <?php require(DIR_WS_INCLUDES . 'classes/start/geburtstag.php'); ?>
        </div>
        </div>
        </div>
    <?php } ?>

        <div class="row">
            <h2>v2next Information</h2>
			<!--BOF - Barzahlen - 2013-05-17: Barzahlen Version Check-->
			<?php include(DIR_WS_MODULES . "barzahlen_version_check.php"); ?>
			<!--EOF - Barzahlen - 2013-05-17: Barzahlen Version Check-->
			<?php 
			$version = xtc_db_fetch_array(xtc_db_query("SELECT version FROM database_version;"));
			echo $version['version'].'<br>';
			echo getDataFromMasterServer(); 
			?>
        </div>
    <br class="clearfix" />
<br />
&copy; 2015 <a href="https://www.commerce-seo.de" target="_blank">commerce:seo</a> ein Projekt von Webdesign Erfurt, based on xt:Commerce <a rel="nofollow" href="http://www.fsf.org/licenses/gpl.txt" target="_blank">GNU General Public License</a>
<br />
<br />
<?php

$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;
// $smarty->display('default/start.html');
// $smarty->display(CURRENT_ADMIN_TEMPLATE . 'default/index.html');
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
