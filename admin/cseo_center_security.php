<?php
/* -----------------------------------------------------------------
* 	$Id: cseo_center_security.php 971 2014-04-11 08:37:04Z akausch $
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
					<td class="pageHeading">Security Center 1.1</td>
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
				
				
					<table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<th class="pageHeading"><?php echo HEADING_SUM_NAME; ?></th>
							<th class="pageHeading"><?php echo HEADING_SUM_COUNT; ?></th>
						</tr>
						
						
				<?php $logging_table_query = xtc_db_query("SELECT * FROM customers WHERE login_tries > '0';"); 
					$faild_login_counter = xtc_db_num_rows($logging_table_query);
					if ($faild_login_counter == 0) {
						$faild_login_counter = '<b style="color:green">'.$faild_login_counter.'<b>';
					} else {
						$faild_login_counter = '<b style="color:red">'.$faild_login_counter.'<b>';
					}
					echo '<tr>';
					echo '<td class="dataTableContent">Faild Login Counter</td>';
					echo '<td class="dataTableContent">'.$faild_login_counter.'</td>';
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
					echo '<td class="dataTableContent">Intrusions Counter</td>';
					echo '<td class="dataTableContent">'.$intrusions_counter.'</td>';
					echo '</tr>';
				
				?>	
				<?php 
					if (ini_get(register_globals)) {
						$register_globals = '<b style="color:red">On</b>';
					} else {
						$register_globals = '<b style="color:green">Off</b>';
					}
					echo '<tr>';
					echo '<td class="dataTableContent">Register Globals</td>';
					echo '<td class="dataTableContent">'.$register_globals.'</td>';
					echo '</tr>';
				?>	
				<?php 
					if (ini_get(safe_mode)) {
						$safe_mode = '<b style="color:red">On</b>';
					} else {
						$safe_mode = '<b style="color:green">Off</b>';
					}
					echo '<tr>';
					echo '<td class="dataTableContent">Safe Mode</td>';
					echo '<td class="dataTableContent">'.$safe_mode.'</td>';
					echo '</tr>';
				?>
				
					</table>
				
				
				</div>
				<div id="logs">
					Logs:
					<?php $logging_table_query = xtc_db_query("SELECT * FROM intrusions");  ?>
					<table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<th class="pageHeading"><?php echo HEADING_CLIENT_IP; ?></th>
							<th class="pageHeading"><?php echo HEADING_IP; ?></th>
							<th class="pageHeading"><?php echo HEADING_ORIGIN; ?></th>
							<th class="pageHeading"><?php echo HEADING_VALUE; ?></th>
							<th class="pageHeading"><?php echo HEADING_PAGE; ?></th>
							<th class="pageHeading"><?php echo HEADING_IMPACT; ?></th>
							<th class="pageHeading"><?php echo HEADING_DATE; ?></th>
						</tr>

						<?php
						echo xtc_draw_form('sqllog', 'cseo_center_security.php', 'action=cleansql', 'post', '');
						if (xtc_db_num_rows($logging_table_query) > 0) {
						$rows=1;
							while ($logging_table = xtc_db_fetch_array($logging_table_query)) {
								if ($rows % 2 == 0) {
									$f = 'dataTableRow';
								} else {
									$f = '';
								}
								echo '<tr class="'.$f.'">';
								echo '<td class="dataTableContent">'.$logging_table['ip'].'</td>';
								echo '<td class="dataTableContent">'.$logging_table['ip2'].'</td>';
								echo '<td class="dataTableContent">'.$logging_table['name'].'</td>';
								echo '<td class="dataTableContent">'.$logging_table['badvalue'].'</td>';
								echo '<td class="dataTableContent">'.$logging_table['page'].'</td>';
								echo '<td class="dataTableContent">'.$logging_table['impact'].'</td>';
								echo '<td class="dataTableContent">'.$logging_table['created'].'</td>';
								echo '</tr>';
								$rows++;
							}
						}
						?>
					</table>
					<table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr><td class="dataTableContent"><input class="button" type="submit" name ="truncateinjetion" value="Injektion Tabelle leeren"></td></tr>
					</table>
					</form>
				</div>
				<div id="serverlog">
					Shop Logs:
					<table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr><td class="dataTableContent">Error Log Shop:</td></tr>
					<?php
					$userdatei = file("../logfiles/Errors.log.txt");
					foreach($userdatei AS $meine_datei) {
						echo '<tr><td class="dataTableContent">'.$meine_datei.'</td></tr>';
					}
					echo xtc_draw_form('logfiles', 'cseo_center_security.php', 'action=clean', 'post', '');
					?>
					<tr>
					<td><input class="button" name ="truncatelog" type="submit" value="Logfile leeren"></td>
					</tr>
					</form>
					</table>
				</div>
				<div id="faildlogin">
					faild Logins
						<?php $logging_table_query = xtc_db_query("SELECT * FROM customers WHERE login_tries > '0';"); ?>
					<table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<th class="pageHeading"><?php echo HEADING_ID; ?></th>
							<th class="pageHeading"><?php echo HEADING_CID; ?></th>
							<th class="pageHeading"><?php echo HEADING_FNAME; ?></th>
							<th class="pageHeading"><?php echo HEADING_LNAME; ?></th>
							<th class="pageHeading"><?php echo HEADING_EMAIL; ?></th>
							<th class="pageHeading"><?php echo HEADING_FLOGIN; ?></th>
							<th class="pageHeading"><?php echo HEADING_LLOGIN; ?></th>
						</tr>

						<?php
						if (xtc_db_num_rows($logging_table_query) > 0) {
							$rows = 1;
							while ($logging_table = xtc_db_fetch_array($logging_table_query)) {
								if ($rows % 2 == 0) {
									$f = 'dataTableRow';
								} else {
									$f = '';
								}
								echo '<tr class="' . $f . '">';
								echo '<td class="dataTableContent">' . $logging_table['customers_id'] . '</td>';
								echo '<td class="dataTableContent">' . $logging_table['customers_cid'] . '</td>';
								echo '<td class="dataTableContent">' . $logging_table['customers_firstname'] . '</td>';
								echo '<td class="dataTableContent">' . $logging_table['customers_lastname'] . '</td>';
								echo '<td class="dataTableContent">' . $logging_table['customers_email_address'] . '</td>';
								echo '<td class="dataTableContent">' . $logging_table['login_tries'] . '</td>';
								echo '<td class="dataTableContent">' . $logging_table['login_time'] . '</td>';
								echo '</tr>';
								$rows++;
							}
						}
						?>
					</table>
				</div>
				<div id="setting">
					Settings
					<table width="100%" height="100%" valign="top" cellpadding="4">
					<?php 
					$setting_table_query = xtc_db_query("SELECT * FROM configuration WHERE configuration_group_id = '363';"); 
						while ($setting_table = xtc_db_fetch_array($setting_table_query)) {
							if ($setting_table['set_function']) {
							eval('$value_field = ' . $setting_table['set_function'] . '"' . htmlspecialchars($setting_table['configuration_value']) . '");');
							} else {
							$value_field = xtc_draw_input_field($setting_table['configuration_key'], $setting_table['configuration_value'], 'size=40');
							}
							// add

							if (strstr($value_field, 'configuration_value'))
							$value_field = str_replace('configuration_value', $configuration['configuration_key'], $value_field);
							$i++;
							if ($i % 2 == 0) {
								$f = '';
							} else {
								$f = ' class="dataTableRow"';
							}
							echo '<tr' . $f . '>
												<td width="40%" valign="top"><b>' . constant(strtoupper($setting_table['configuration_key'] . '_TITLE')) . '</b></td>
												<td valign="top">' . $value_field . '<br />' . constant(strtoupper($setting_table['configuration_key'] . '_DESC')) . '</td>
											</tr>';
						}
					
					?>
					</table>
				</div>
			</div>
		</td>
	</tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
