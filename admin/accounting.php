<?php
/* -----------------------------------------------------------------
 * 	$Id: accounting.php 420 2013-06-19 18:04:39Z akausch $
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

if ($_GET['action']) {
    switch ($_GET['action']) {
        case 'save':
            $admin_access_query = xtc_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . (int) $_GET['cID'] . "'");
            $admin_access = xtc_db_fetch_array($admin_access_query);

            $fields = mysql_list_fields(DB_DATABASE, TABLE_ADMIN_ACCESS);
            $columns = mysql_num_fields($fields);

            for ($i = 0; $i < $columns; $i++) {
                $field = mysql_field_name($fields, $i);
                if ($field != 'customers_id') {

                    xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET
                                  " . $field . "=0 where customers_id='" . (int) $_GET['cID'] . "'");
                }
            }
            $access_ids = '';
            if (isset($_POST['access']))
                foreach ($_POST['access'] as $key)
                    xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET " . $key . "=1 where customers_id='" . (int) $_GET['cID'] . "'");
            xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, 'cID=' . (int) $_GET['cID'], 'NONSSL'));
            break;
    }
}
if ($_GET['cID'] != '') {
    if ($_GET['cID'] == 1)
        xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, 'cID=' . (int) $_GET['cID'], 'NONSSL'));
    else {
        $allow_edit_query = xtc_db_query("select customers_status, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int) $_GET['cID'] . "'");
        $allow_edit = xtc_db_fetch_array($allow_edit_query);
        if ($allow_edit['customers_status'] != 0 || $allow_edit == '')
            xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, 'cID=' . (int) $_GET['cID'], 'NONSSL'));
    }
}
require(DIR_WS_INCLUDES . 'header.php');
?>
<script type="text/javascript">
<!--
    function CheckAll(wert) {
        var maf = document.accounting;
        var len = maf.length;
        for (var i = 0; i < len; i++) {
            var e = maf.elements[i];
            if (e.name == "access[]") {
                e.checked = wert;
            }
        }
    }
//-->
</script>
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td colspan="3" class="main"><?php echo TEXT_ACCOUNTING . ' ' . $allow_edit['customers_lastname'] . ' ' . $allow_edit['customers_firstname']; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="main"> <br /><?php echo TXT_GROUPS; ?><br />
                        <table width="100%" cellpadding="0" cellspacing="2">
                            <tr>
                                <td style="border: 1px solid; border-color: #000000;" width="10" bgcolor="FF6969" >&nbsp;</td>
                                <td width="100%" class="main"><?php echo TXT_SYSTEM; ?></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid; border-color: #000000;" width="10" bgcolor="69CDFF" >&nbsp;</td>
                                <td width="100%" class="main"><?php echo TXT_CUSTOMERS; ?></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid; border-color: #000000;" width="10" bgcolor="6BFF7F" >&nbsp;</td>
                                <td width="100%" class="main"><?php echo TXT_PRODUCTS; ?></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid; border-color: #000000;" width="10" bgcolor="BFA8FF" >&nbsp;</td>
                                <td width="100%" class="main"><?php echo TXT_STATISTICS; ?></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid; border-color: #000000;" width="10" bgcolor="FFE6A8" >&nbsp;</td>
                                <td width="100%" class="main"><?php echo TXT_TOOLS; ?></td>
                            </tr>
                        </table>
                        <br />
                    </td>
                </tr>
                <tr>
                    <td>
                        <table valign="top" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr class="dataTableHeadingRow">
                                <td class="dataTableHeadingContent"><input type="checkbox" name="check_all" value="" onclick="javascript:CheckAll(this.checked);" /><?php echo TEXT_ACCESS; ?></td>
                                <td class="dataTableHeadingContent"><?php echo TEXT_ALLOWED; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="2">
                            <?php
                            echo xtc_draw_form('accounting', FILENAME_ACCOUNTING, 'cID=' . $_GET['cID'] . '&action=save', 'post', 'enctype="multipart/form-data"');

                            $admin_access = '';
                            $customers_id = xtc_db_prepare_input($_GET['cID']);
                            $admin_access_query = xtc_db_query("SELECT * FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . (int) $_GET['cID'] . "';");
                            $admin_access = xtc_db_fetch_array($admin_access_query);

                            $group_query = xtc_db_query("SELECT * FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = 'groups';");
                            $group_access = xtc_db_fetch_array($group_query);
                            if ($admin_access == '') {
                                xtc_db_query("INSERT INTO " . TABLE_ADMIN_ACCESS . " (customers_id) VALUES ('" . (int) $_GET['cID'] . "')");
                                $admin_access_query = xtc_db_query("SELECT * FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . (int) $_GET['cID'] . "';");
                                $group_query = xtc_db_query("SELECT * FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = 'groups';");
                                $group_access = xtc_db_fetch_array($group_query);
                                $admin_access = xtc_db_fetch_array($admin_access_query);
                            }

                            $fields = mysql_list_fields(DB_DATABASE, TABLE_ADMIN_ACCESS);
                            $columns = mysql_num_fields($fields);

                            for ($i = 0; $i < $columns; $i++) {
                                $field = mysql_field_name($fields, $i);
                                if ($field != 'customers_id') {
                                    $checked = '';
                                    if ($admin_access[$field] == '1')
                                        $checked = 'checked';

                                    // colors
                                    switch ($group_access[$field]) {
                                        case '1':
                                            $color = '#FF6969';
                                            break;
                                        case '2':
                                            $color = '#69CDFF';
                                            break;
                                        case '3':
                                            $color = '#6BFF7F';
                                            break;
                                        case '4':
                                            $color = '#BFA8FF';
                                            break;
                                        case '5':
                                            $color = '#FFE6A8';
                                    }
                                    echo '<tr class="dataTable">
											<td style="border: 1px solid; border-color: #000000;" width="10" bgcolor="' . $color . '" >
												&nbsp;
											</td>
											<td width="100%" class="dataTableContentRow">
												<input type="checkbox" name="access[]" value="' . $field . '"' . $checked . '>
												' . $field . '
											</td>
										</tr>';
                                }
                            }
                            ?>
                        </table>
                    </td>
                </tr>
            </table>
            <input type="submit" class="button" onclick="return confirm('<?php echo SAVE_ENTRY; ?>')" value="<?php echo BUTTON_SAVE; ?>">
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
