<?php

/**
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 * 
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

require(DIR_WS_INCLUDES . 'metatag.php');

?>
<link rel="stylesheet" type="text/css" href="<?php echo '../callback/sofort/ressources/style/sofort.css';?>">
</head>
<body>
<?php
require (DIR_WS_INCLUDES.'header.php');

$order = new order($_GET['oID']);

if (xtc_not_null($_GET['print_oID'])) {
	if ($_GET['print_invoice'] == 'on') {
		echo '<script type="text/javascript">var invoice'.$i.' = window.open(\''.xtc_href_link(FILENAME_PRINT_ORDER,'print_oID='.$_GET['print_oID']).'\', \'invoice\', \'toolbar=0, width=640, height=600\')</script>';
	}
	if ($_GET['print_packingslip'] == 'on') {
		echo '<script type="text/javascript">var packingslip'.$i.' = window.open(\''.xtc_href_link(FILENAME_PRINT_PACKINGSLIP,'print_oID='.$_GET['print_oID']).'\', \'packingslip\', \'toolbar=0, width=640, height=600\')</script>';
	}
}
?>
<script type="text/javascript">
	<!--
	function selectAll(field) {
		var loop;
		for (loop = 0; loop < field.length; loop++)
			field[loop].checked = document.getElementsByName('checkAll')[0].checked;
	}
	//-->
</script>
<div id="wrapper">
<table class="outerTable" cellspacing="0" cellpadding="0">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
		<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
	</td>
    <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">