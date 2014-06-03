<?php
/* --------------------------------------------------------------
klarna_config.php 2013-04-10 mabr
Gambio GmbH
http://www.gambio.de
Copyright (c) 2013 Gambio GmbH
Released under the GNU General Public License (Version 2)
[http://www.gnu.org/licenses/gpl-2.0.html]
--------------------------------------------------------------


based on:
(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
(c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
(C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

Released under the GNU General Public License
---------------------------------------------------------------------------------------*/

require_once 'includes/application_top.php';
define('PAGE_URL', HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));
require_once DIR_FS_CATALOG . 'includes/classes/class.klarna.php';
$coo_text_mgr = new LanguageTextManager('klarna', $_SESSION['languages_id']);
$klarna = new GMKlarna();
$config = $klarna->getConfig();

$messages_ns = 'messages_'.basename(__FILE__);
if(!isset($_SESSION[$messages_ns])) {
	$_SESSION[$messages_ns] = array();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	// do something
	if(isset($_POST['config_save'])) {
		$checkbox_names = array('activate_country_AT', 'activate_country_DK', 'activate_country_FI',
			'activate_country_DE', 'activate_country_NL', 'activate_country_NO', 'activate_country_SE');
		foreach($checkbox_names as $cb_name) {
			$_POST[$cb_name] = isset($_POST[$cb_name]) ? $_POST[$cb_name] : '0';
		}
		$klarna->saveConfig($_POST);
		$_SESSION[$messages_ns][] = $coo_text_mgr->get_text('configuration_saved');
	}
	if(isset($_POST['clear_pclasses'])) {
		$result = $klarna->clearPClasses();
		if($result === false) {
			$_SESSION[$messages_ns][] = $coo_text_mgr->get_text('error_clearing_pclasses');
		}
		else {
			$_SESSION[$messages_ns][] = $coo_text_mgr->get_text('pclasses_cleared');
		}
	}

	xtc_redirect(PAGE_URL);
}

$messages = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

ob_start();
require DIR_WS_INCLUDES . 'header.php';
?>


		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="boxCenter" width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td class="pageHeading" style="padding-left: 0px"><?php echo KLARNA_CONFIGURATION ?></td>
										<td width="80" rowspan="2">&nbsp;</td>
									</tr>
									<tr>
										<td class="main" valign="top">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="main">
								<?php foreach($messages as $msg) { ?>
								<p class="message"><?php echo $msg ?></p>
								<?php } ?>

								<form action="<?php echo PAGE_URL ?>" method="POST">
									<dl class="adminform">
										<dt><label for="server"><?php echo SERVER_MODE ?></label></dt>
										<dd>
											<select name="server" id="server">
												<option value="<?php echo Klarna::BETA ?>" <?php echo $config['server'] == Klarna::BETA ? 'selected="selected"' : '' ?>>BETA</option>
												<option value="<?php echo Klarna::LIVE ?>" <?php echo $config['server'] == Klarna::LIVE ? 'selected="selected"' : '' ?>>LIVE</opfion>
											</select>
										</dd>
										<?php foreach($klarna->getCountries() as $country => $klarna_country_id) { ?>
										<dt><?php echo ACTIVATE_IN ?> <?php echo COUNTRY_.$country ?></dt>
										<dd>
											<input id="activate_country_<?php echo $country ?>" name="activate_country_<?php echo $country ?>" type="checkbox" value="1" class="countrycb" <?php echo $config['activate_country_'.$country] ? 'checked="checked"' : ''?>>
											<label for="activate_country_<?php echo $country ?>"><?php echo USE_FOR_COUNTRY ?>&nbsp;<?php echo COUNTRY_.$country ?></label>
											<dl class="adminform countrydata">
												<dt><label for="merchant_id_<?php echo $country ?>"><?php echo MERCHANT_ID ?> <?php echo COUNTRY_.$country ?></label></dt>
												<dd>
													<input id="merchant_id_<?php echo $country ?>" name="merchant_id_<?php echo $country ?>" type="text" value="<?php echo $config['merchant_id_'.$country] ?>">
												</dd>
												<dt><label for="shared_secret_<?php echo $country ?>"><?php echo SHARED_SECRET ?> <?php echo COUNTRY_.$country ?></label></dt>
												<dd>
													<input id="shared_secret_<?php echo $country ?>" name="shared_secret_<?php echo $country ?>" type="text" value="<?php echo $config['shared_secret_'.$country] ?>">
												</dd>
												<dt><label for="invoice_fee_<?php echo $country ?>"><?php echo INVOICE_FEE ?></label></dt>
												<dd>
													<input type="text" name="invoice_fee_<?php echo $country ?>" value="<?php echo $config['invoice_fee_'.$country] ?>" placeholder="1.95">
												</dd>
											</dl>
										</dd>
										<?php } ?>
										<dt><label for="show_checkout_partpay"><?php echo SHOW_CHECKOUT_PARTPAY ?></label></dt>
										<dd>
											<select id="show_checkout_partpay" name="show_checkout_partpay">
												<option value="1" <?php echo $config['show_checkout_partpay'] == true ? 'selected="selected"' : ''?>><?php echo YES ?></option>
												<option value="0" <?php echo $config['show_checkout_partpay'] == false ? 'selected="selected"' : ''?>><?php echo NO ?></option>
											</select>
										</dd>
										<dt><label for="show_product_partpay"><?php echo SHOW_PRODUCT_PARTPAY ?></label></dt>
										<dd>
											<select id="show_product_partpay" name="show_product_partpay">
												<option value="1" <?php echo $config['show_product_partpay'] == true ? 'selected="selected"' : ''?>><?php echo YES ?></option>
												<option value="0" <?php echo $config['show_product_partpay'] == false ? 'selected="selected"' : ''?>><?php echo NO ?></option>
											</select>
										</dd>
									</dl>
									<input class="button" type="submit" value="<?php echo CONFIG_SAVE ?>" name="config_save">
									<input class="button" type="submit" value="<?php echo CLEAR_PCLASSES ?>" name="clear_pclasses">
								</form>
							</td>
						</tr>
					</table>
				</td>

			</tr>
		</table>
		<style>
			p.message {	margin: .5ex auto; background: rgb(240, 230, 140); border: 1px solid rgb(255, 0, 0); padding: 1em; }
			dl.adminform { position: relative; overflow: auto; }
			dl.adminform dd, dl.adminform dt { float: left; margin: 1px 0; }
			dl.adminform dt { clear: left; width: 15em; }
			dl.adminform dt label:after { content: ':';}
			input[type="submit"].btn_wide { width: auto; }
			dl.adminform select { width: 12em; }
			dl.adminform input[type="checkbox"] { vertical-align: middle; }

		</style>
<?php
require DIR_WS_INCLUDES . 'footer.php';
$content = ob_get_clean();
echo $content;
require DIR_WS_INCLUDES . 'application_bottom.php';

