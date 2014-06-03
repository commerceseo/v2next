<?php
/**
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 */
//needed for iDEAL
chdir('../..');
include ('includes/application_top.php');

//check if admin
if ($_SESSION['customers_status']['customers_status_id'] == '0') {
	require_once('library/sofortLib.php');

	$apiKey = isset($_GET['k']) ? $_GET['k'] : '';
	preg_match('#([a-zA-Z0-9:]+)#', $apiKey, $matches);
	$configkey =  $matches[1];
	$sofort = new SofortLib_TransactionData($configkey);
	$sofort->sendRequest();

	$result = ($sofort->isError()) ? '<h2 style="color: #ff1111">ERROR!</h2>' : '<h2 style="color: #84B819">OK!</h2>';
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="content-language" content="de" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<title>SOFORT AG API Check</title>

	<style type="text/css" media="screen">
		body {
			font-family: 'Helvetica', sans-serif;
			font-size:16px;
			width: 100%;
			text-align: center;
		}

		h1 {
			font-size: 1.5em;
		}

		#page {
		width: 400px;
			margin: 0em auto;
		}

		div.element.check_api_key {
			float:left;
			margin:0 10px 20px 10px;
			padding:10px 0 0 0;
			text-align:center;
			background:#fff;
			height: 200px;
			width: 400px;
			border: 1px solid #dbdbdb;
			border-radius: 7px;
			-moz-border-radius: 7px;
			-webkit-border-radius: 7px;
			-webkit-box-shadow: 3px 3px 1px 1px rgba(0, 0, 0, 0.1);
			-moz-box-shadow: 3px 3px 1px 1px rgba(0, 0, 0, 0.1);
			box-shadow: 3px 3px 4px 1px rgba(202, 206, 216, 1);
		}

		div.element.check_api_key div.logo {
			position: absolute;
			width: 177px;
			height: 60px;
			top: 10px;
			margin: 138px 0px 0px 220px;
			background: url('https://images.sofort.com/de/pnag/logo.gif') no-repeat;
		}
	</style>

	<!--[if lte IE 7]>
	<style type="text/css" media="screen">
		div.element.check_api_key div.logo {
			margin: 122px 0px 0px 14px;
		}
	</style>
	<![endif]-->

	</head>
	<body>
	<div id="page">
		<div class="element check_api_key">
			<h1>Verbindung zur Multipay API</h1>
			<div class="result">
				<?php echo $result;?>
			</div>
			<div class="error">
				<?php echo trim($sofort->getError());?>
			</div>
			<div class="logo">&nbsp;</div>
		</div>
	</div>
	</body>
	</html>
	<?php
}
?>