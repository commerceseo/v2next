<?PHP
/* --------------------------------------------------------------
  $Id: psapi_import.php

  XT-Commerce - community made shopping
  http://www.xt-commerce.com

  Copyright (c) 2003 XT-Commerce
  --------------------------------------------------------------
  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommercecoding standards (a typical file) www.oscommerce.com

  Released under the GNU General Public License
  -------------------------------------------------------------- */
//VERSION

$modulVersion = "1.0";
require('includes/application_top.php');
require('includes/haendlerbund/haendlerbund_importer.php');

if ($_GET["api_konfiguration"] == 1) {

    $contentimporter = new haendlerbund_importer();
    echo $contentimporter->process(1);
} else {

    require(DIR_WS_INCLUDES . 'header.php');
    ?>

    <link href="includes/haendlerbund/css/main.css" rel="stylesheet" type="text/css" />
    <link href="http://fonts.googleapis.com/css?family=Cuprum" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="includes/haendlerbund/jquery.smartWizard.min.js"></script>
    <script type="text/javascript" src="includes/haendlerbund/custom.js"></script>

    <table border="0" width="100%" cellspacing="2" cellpadding="2">
        <tr>
            <td width="100%" valign="top">
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="237"><img src="includes/haendlerbund/images/haendlerbund_logo.png" hspace="10" vspace="10" style="padding:10px" /></td>
                        <td valign="top"><img src="includes/haendlerbund/images/groesster-onlinehandelsverband-europas.png" style="padding:10px"  /></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <div  style="background-color:#387CB0; height:5px;"></div>
                <?php
                $contentimporter = new haendlerbund_importer();
                echo $contentimporter->process(0);
                echo $contentimporter->getImportForm();
                ?>           
            </td>
        </tr>
    </table>

    <?php
    require(DIR_WS_INCLUDES . 'application_bottom.php');
}
