<?php
/* -----------------------------------------------------------------
 * 	$Id: validproducts.php 420 2013-06-19 18:04:39Z akausch $
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


require('includes/application_top.php');


?>
<html>
<head>
<title>Valid Categories/Products List</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body>
<table width="550" cellspacing="1">
<tr>
<td class="pageHeading" colspan="3">
<?php echo TEXT_VALID_PRODUCTS_LIST; ?>
</td>
</tr>
<?php
    echo "<tr><th class=\"dataTableHeadingContent\">". TEXT_VALID_PRODUCTS_ID . "</th><th class=\"dataTableHeadingContent\">" . TEXT_VALID_PRODUCTS_NAME . "</th><th class=\"dataTableHeadingContent\">" . TEXT_VALID_PRODUCTS_MODEL . "</th></tr><tr>";
    $result = xtc_db_query("SELECT * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd WHERE p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' ORDER BY pd.products_name");
   
    if ($row = xtc_db_fetch_array($result)) {
        do {
            echo "<td class=\"dataTableHeadingContent\">".$row["products_id"]."</td>\n";
            echo "<td class=\"dataTableHeadingContent\">".$row["products_name"]."</td>\n";
            echo "<td class=\"dataTableHeadingContent\">".$row["products_model"]."</td>\n";
            echo "</tr>\n";
        }
        while($row = xtc_db_fetch_array($result));
    }
    echo "</table>\n";
?>
<br />
<table width="550" border="0" cellspacing="1">
<tr>
<td align=middle><input type="button" value="Close Window" onclick="window.close()"></td>
</tr></table>
</body>
</html>