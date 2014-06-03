<?php
/* -----------------------------------------------------------------
 * 	$Id: database_manager.php 670 2013-10-15 06:02:50Z akausch $
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

if ($_POST['sql_query'] != '') {

    $success_msg = '';
    $error_msg = '';
    $db_echo = '';

    $db_echo = mysql_query("$sql_string");
    if (mysql_error() == "")
        $success_msg = SQL_SUCCESS;
    else
        $error_msg = mysql_error();
}

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td width="100%" height="100%" valign="top">
            <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="5">
                <tr>
                    <td class="pageHeading">SQL Tool</td>
                </tr>
            </table>
            <?php
            if ($success_msg != '' || $error_msg != '') {
                echo '<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery(".' . ($success_msg != '' ? 'success_msg' : 'error_msg') . '").fadeIn(function(){setTimeout(function(){jQuery(".' . ($success_msg != '' ? 'success_msg' : 'error_msg') . '").slideToggle("slow");}, 5000);});
						});
					</script>';
                if ($success_msg != '')
                    echo '<div class="success_msg" style="margin-left:10px">' . $success_msg . '</div>';
                elseif ($error_msg != '')
                    echo '<div class="error_msg" style="margin-left:10px">' . $error_msg . '</div>';
            }
            ?>
            <table cellpadding="5" cellspacing="0" width="100%">

                <tr>
                    <td>
						<?php echo xtc_draw_form('sql_update_form', 'database_manager.php', 'action=process', 'POST', 'enctype="multipart/form-data"'); ?>
<?php echo SQL ?>:<br />
                            <textarea name="sql_query" cols="120" rows="10" style="width:890px; height:100px; font-size: 13px; color:#777;padding:5px; border: 1px solid #677E98"><?php echo $_POST['sql_query']; ?></textarea><br /><br />
                            <input class="button" type="submit" value="Absenden" />
                        </form>
                    </td>
                </tr>
                        <?php if (isset($db_echo) && $success_msg != '') { ?>
                    <tr>
                        <td>
    <?php echo SQL_OUTPUT; ?>
                            <div style="width:900px; overflow:auto; height:350px; margin-top: 15px; border: 1px solid #ccc">
                                <table border="0" cellpadding="3" cellspacing="1" bgcolor="black">
                                    <tr>
                                        <?php
                                        for ($i = 0; $i < mysql_num_fields($db_echo); $i++) {
                                            $name = mysql_field_name($db_echo, $i);
                                            echo '<td style="background-color: #677E98; color: white;">';
                                            echo '<nobr><strong>' . $name . '</strong></nobr>';
                                            echo '</td>';
                                        }
                                        ?>
                                    </tr>
                                    <?php
                                    for ($i = 0; $i < mysql_num_rows($db_echo); $i++) {
                                        if ($i % 2 == 0)
                                            $bg = 'bgcolor="#FCF5DD"';
                                        else
                                            $bg = 'bgcolor="#fff"';
                                        echo '<tr ' . $bg . '>';
                                        for ($h = 0; $h < mysql_num_fields($db_echo); $h++) {
                                            $name = mysql_field_name($db_echo, $h);
                                            $value = mysql_result($db_echo, $i, $name);
                                            echo '<td valign="top" stye="border-right: 1px solid #ccc"><nobr>' . strip_tags($value) . '</nobr></td>';
                                        }
                                        echo '</tr>';
                                        flush();
                                    }
                                    ?>
                                </table>
                            </div>
                        </td>
                    </tr>
<?php } ?>
            </table>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
