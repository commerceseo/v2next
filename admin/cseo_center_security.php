<?php
/* -----------------------------------------------------------------
* 	$Id: cseo_center_security.php 1185 2014-09-09 18:28:57Z akausch $
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
$languages = xtc_get_languages();
include ('../lang/german/admin/configuration.php');
$action = (isset($_GET['action']) ? $_GET['action'] : '');
if (xtc_not_null($action)) {
	switch ($action) {
		case 'clean':
			if ($_GET['action'] == 'clean') {
				$userdatei = fopen('../logfiles/Errors.log.txt','w');  
				fwrite($userdatei,''); 
				fclose($userdatei);
				xtc_redirect(xtc_href_link('cseo_center_security.php'));
			}
		break;
		case 'cleansql':
			if ($_GET['action'] == 'cleansql') {
				xtc_db_query("TRUNCATE intrusions;");
				xtc_redirect(xtc_href_link('cseo_center_security.php'));
			}
		break;
	}
}


require(DIR_WS_INCLUDES . 'header.php');
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="3">
			<table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="pageHeading">Security Center 1.2 Beta</td>
				</tr>
			</table>
			<?php echo CSEO_SECURITY_HINWEIS; ?>
		</td>
	</tr>
	<tr>
		<td align="left">
			<div id="securitytabs">
				<ul>
					<li><a href="#overview">Overview</a></li>
					<li><a href="#logs">SQL Logs</a></li>
					<li><a href="#serverlog">Shop Logs</a></li>
					<li><a href="#faildlogin">faild Logins</a></li>
					<li><a href="#setting">Setting</a></li>
				</ul>
				<div id="overview">
					<div class="row">
						<h1>
							Overview
						</h1>
					<table class="table table-bordered table-striped">
						<tr>
							<th><?php echo HEADING_SUM_NAME; ?></th>
							<th><?php echo HEADING_SUM_COUNT; ?></th>
						</tr>
						
						
				<?php $logging_table_query = xtc_db_query("SELECT * FROM customers WHERE login_tries > '0';"); 
					$faild_login_counter = xtc_db_num_rows($logging_table_query);
					if ($faild_login_counter == 0) {
						$faild_login_counter = '<b style="color:green">'.$faild_login_counter.'<b>';
					} else {
						$faild_login_counter = '<b style="color:red">'.$faild_login_counter.'<b>';
					}
					echo '<tr>';
					echo '<td>Faild Login Counter</td>';
					echo '<td>'.$faild_login_counter.'</td>';
					echo '</tr>';
				
				?>		
				<?php $logging_table_query = xtc_db_query("SELECT * FROM intrusions;"); 
					$intrusions_counter = xtc_db_num_rows($logging_table_query);
					if ($intrusions_counter == 0) {
						$intrusions_counter = '<b style="color:green">'.$intrusions_counter.'<b>';
					} else {
						$intrusions_counter = '<b style="color:red">'.$intrusions_counter.'<b>';
					}
					echo '<tr>';
					echo '<td>Intrusions Counter</td>';
					echo '<td>'.$intrusions_counter.'</td>';
					echo '</tr>';
				
				?>	
				<?php 
					if (ini_get(register_globals)) {
						$register_globals = '<b style="color:red">On</b>';
					} else {
						$register_globals = '<b style="color:green">Off</b>';
					}
					echo '<tr>';
					echo '<td>Register Globals</td>';
					echo '<td>'.$register_globals.'</td>';
					echo '</tr>';
				?>	
				<?php 
					if (ini_get(safe_mode)) {
						$safe_mode = '<b style="color:red">On</b>';
					} else {
						$safe_mode = '<b style="color:green">Off</b>';
					}
					echo '<tr>';
					echo '<td>Safe Mode</td>';
					echo '<td>'.$safe_mode.'</td>';
					echo '</tr>';
				?>
				
					</table>
				
				
				</div>
				</div>
				<div id="logs">
					<div class="row">
						<h1>
							Logs
						</h1>
						<?php $logging_table_query = xtc_db_query("SELECT * FROM intrusions");  ?>
						<table class="table table-bordered table-striped">
							<tr>
								<th><?php echo HEADING_CLIENT_IP; ?></th>
								<th><?php echo HEADING_IP; ?></th>
								<th><?php echo HEADING_ORIGIN; ?></th>
								<th><?php echo HEADING_VALUE; ?></th>
								<th><?php echo HEADING_PAGE; ?></th>
								<th><?php echo HEADING_IMPACT; ?></th>
								<th><?php echo HEADING_DATE; ?></th>
							</tr>

							<?php
							echo xtc_draw_form('sqllog', 'cseo_center_security.php', 'action=cleansql', 'post', '');
							if (xtc_db_num_rows($logging_table_query) > 0) {
							$rows=1;
								while ($logging_table = xtc_db_fetch_array($logging_table_query)) {
									echo '<tr>';
									echo '<td>'.htmlentities_wrapper($logging_table['ip']).'</td>';
									echo '<td>'.htmlentities_wrapper($logging_table['ip2']).'</td>';
									echo '<td>'.htmlentities_wrapper($logging_table['name']).'</td>';
									echo '<td>'.htmlentities_wrapper($logging_table['badvalue']).'</td>';
									echo '<td>'.htmlentities_wrapper($logging_table['page']).'</td>';
									echo '<td>'.htmlentities_wrapper($logging_table['impact']).'</td>';
									echo '<td>'.htmlentities_wrapper($logging_table['created']).'</td>';
									echo '</tr>';
									$rows++;
								}
							}
							?>
						</table>
						<div class="col-xs-12">
							<input class="btn btn-info" type="submit" name ="truncateinjetion" value="Injektion Tabelle leeren">
						</div>
						</form>
					</div>
				</div>
				<div id="serverlog">
					<div class="row">
						<h1>
							Shop Logs
						</h1>
						<table class="table table-bordered table-striped">
						<tr><th>Error Log Shop:</th></tr>
						<?php
						$userdatei = file("../logfiles/Errors.log.txt");
						foreach($userdatei AS $meine_datei) {
							echo '<tr><td>'.$meine_datei.'</td></tr>';
						}
						echo xtc_draw_form('logfiles', 'cseo_center_security.php', 'action=clean', 'post', '');
						?>
						<tr>
						<td></td>
						</tr>
						</table>
						<div class="col-xs-12">
							<input class="btn btn-info" name ="truncatelog" type="submit" value="Logfile leeren">
						</div>
						</form>
					</div>
				</div>
				<div id="faildlogin">
					<div class="row">
						<h1>
							faild Logins
						</h1>
						<?php $logging_table_query = xtc_db_query("SELECT * FROM customers WHERE login_tries > '0';"); ?>
						<table class="table table-bordered table-striped">
							<tr>
								<th><?php echo HEADING_ID; ?></th>
								<th><?php echo HEADING_CID; ?></th>
								<th><?php echo HEADING_FNAME; ?></th>
								<th><?php echo HEADING_LNAME; ?></th>
								<th><?php echo HEADING_EMAIL; ?></th>
								<th><?php echo HEADING_FLOGIN; ?></th>
								<th><?php echo HEADING_LLOGIN; ?></th>
							</tr>

							<?php
							if (xtc_db_num_rows($logging_table_query) > 0) {
								$rows = 1;
								while ($logging_table = xtc_db_fetch_array($logging_table_query)) {
									echo '<tr>';
									echo '<td>' . htmlentities_wrapper($logging_table['customers_id']) . '</td>';
									echo '<td>' . htmlentities_wrapper($logging_table['customers_cid']) . '</td>';
									echo '<td>' . htmlentities_wrapper($logging_table['customers_firstname']) . '</td>';
									echo '<td>' . htmlentities_wrapper($logging_table['customers_lastname']) . '</td>';
									echo '<td>' . htmlentities_wrapper($logging_table['customers_email_address']) . '</td>';
									echo '<td>' . htmlentities_wrapper($logging_table['login_tries']) . '</td>';
									echo '<td>' . htmlentities_wrapper($logging_table['login_time']) . '</td>';
									echo '</tr>';
									$rows++;
								}
							}
							?>
						</table>
					</div>
				</div>
				<div id="setting">
					<div class="row">
						<h1>
							Settings
						</h1>
						<table class="table table-bordered table-striped">
							<tr>
								<th>Settings</th>
								<th>Value</th>
								<th>Help</th>
							</tr>
						<?php 
						$setting_table_query = xtc_db_query("SELECT * FROM configuration WHERE configuration_group_id = '363';"); 
							while ($setting_table = xtc_db_fetch_array($setting_table_query)) {
								$value_field = $setting_table['configuration_value'];
								if (strstr($value_field, 'configuration_value'))
								$value_field = str_replace('configuration_value', $configuration['configuration_key'], $value_field);
								$i++;
								echo '<tr>
										<td><b>' . constant(strtoupper($setting_table['configuration_key'] . '_TITLE')) . '</b></td>
										<td>' . $value_field . '</td>
										<td>' . constant(strtoupper($setting_table['configuration_key'] . '_DESC')) . '</td>
									</tr>';
							}
						
						?>
						</table>
					<div class="col-xs-12">
						<a class="btn btn-info" href="configuration.php?gID=363"><i class="glyphicon glyphicon-pencil"></i> Zu den Einstellungen</a>
					</div>
				</div>
			</div>
			</div>
		</td>
	</tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
