<?php
/* -----------------------------------------------------------------
 * 	$Id: products_parameters_edit.php 420 2013-06-19 18:04:39Z akausch $
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
if ($_GET['action'] == 'save') {
    if (isset($_POST)) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'parameters_') === 0) {
                $ids = explode('_', $key);
                if ($ids[5] == 'name' && $ids[4] == 'selected') {
                    $id2 = $ids;
                    $id2[4] = 'type';
                    $new_id = implode('_', $id2);
                    if ($_POST[$new_id] == '')
                        $parameters_name = $value;
                    else
                        $parameters_name = $_POST[$new_id];

                    $id2 = $ids;
                    $id2[5] = 'value';
                    $new_id = implode('_', $id2);
                    $id2[4] = 'type';
                    $new_id2 = implode('_', $id2);
                    $parameters_value = $_POST[$new_id2] ? $_POST[$new_id2] : $_POST[$new_id];

                    $check = xtc_db_query("SELECT p.parameters_id
                                   FROM " . TABLE_PRODUCTS_PARAMETERS . " p," . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . " pd
                                   WHERE p.parameters_id=pd.parameters_id
                                   AND p.products_id=" . $_POST['products_id'] . "
                                   AND p.group_id=" . $ids[1] . "
                                   AND p.sort_order=" . $ids[2] . "
                                   AND pd.language_id=" . $ids[3]);
                    $check2 = xtc_db_query("SELECT parameters_id
                                   FROM " . TABLE_PRODUCTS_PARAMETERS . "
                                   WHERE products_id=" . $_POST['products_id'] . "
                                   AND group_id=" . $ids[1] . "
                                   AND sort_order=" . $ids[2]);
                    $check_rows = xtc_db_num_rows($check);
                    $check2_rows = xtc_db_num_rows($check2);
                    if ($check_rows == 1 && $check2_rows > 0) {
                        $erg = xtc_db_fetch_array($check);
                        $pID = $erg['parameters_id'];
                        $add = false;
                        $add_text = 'false ' . $check_rows . " " . $check2_rows;
                    } elseif ($check_rows == 0 && $check2_rows > 0) {
                        $erg = xtc_db_fetch_array($check2);
                        $pID = $erg['parameters_id'];
                        $add = true;
                        $add_text = 'true ' . $check_rows . " " . $check2_rows;
                    } else {
                        $pID = 0;
                        $add_text = 'new ' . $check_rows . " " . $check2_rows;
                    }
                    if (($parameters_name != '' && $parameters_value != '') && $pID > 0 && !$add) {
                        //update
                        xtc_db_query("UPDATE " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . "
                          SET parameters_name='" . $parameters_name . "', parameters_value='" . $parameters_value . "'
                          WHERE parameters_id=" . $pID . "
                          AND language_id=" . $ids[3]);
                    } elseif (($parameters_name != '' && $parameters_value != '') && $pID > 0 && $add) {
                        //add language specific
                        xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . " (parameters_id, language_id, parameters_name, parameters_value)
                          VALUES (" . $pID . ", " . $ids[3] . ", '" . $parameters_name . "', '" . $parameters_value . "')");
                    } elseif (($parameters_name != '' && $parameters_value != '') && $pID == 0) {
                        //insert
                        xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_PARAMETERS . " (group_id, products_id, sort_order)
                          VALUES (" . $ids[1] . ", " . $_POST['products_id'] . ", " . $ids[2] . ")");
                        $insert_id = xtc_db_insert_id();
                        xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . " (parameters_id, language_id, parameters_name, parameters_value)
                          VALUES (" . $insert_id . ", " . $ids[3] . ", '" . $parameters_name . "', '" . $parameters_value . "')");
                    } elseif (($parameters_name == '' || $parameters_value == '') && $pID > 0 && $check_rows == 1) {
                        //delete
                        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . " WHERE parameters_id=" . $pID);
                    }
                }
            }
        }
    }
    xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'products_id=' . $_POST['products_id'] . '&category_path=' . $_POST['category_path']));
} else {
    if (isset($_POST['sel']) && is_array($_POST['sel']) && $_POST['products_id']) {
        $groups = array();
        $group_values = array();
        foreach ($_POST['sel'] as $key => $value) {
            // die einzelnen Gruppen abfragen
            $sql = xtc_db_query("SELECT group_name
                         FROM " . TABLE_PRODUCTS_PARAMETERS_GROUPS_DESCRIPTION . "
                         WHERE group_id=" . $value . "
                         AND language_id=" . $_SESSION['languages_id']);
            $erg = xtc_db_fetch_array($sql);
            $groups[$value] = $erg['group_name'];

            //die dazugehoerigen Werte abfragen
            $sql = xtc_db_query("SELECT p.sort_order, pd.parameters_id, pd.language_id, pd.parameters_name, pd.parameters_value
                         FROM " . TABLE_PRODUCTS_PARAMETERS . " p, " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . " pd
                         WHERE p.parameters_id=pd.parameters_id
                         AND p.group_id=" . $value . "
                         AND p.products_id=" . $_POST['products_id'] . "
                         ORDER BY p.sort_order");
            while ($erg = xtc_db_fetch_array($sql)) {
                $group_values[$value][$erg['sort_order']][$erg['language_id']] = array('name' => $erg['parameters_name'], 'value' => $erg['parameters_value']);
            }

            //die bereits eingegebenen Werte fuer die Dropdowns abfragen
            $sql = xtc_db_query("SELECT DISTINCT pd.parameters_name, pd.language_id
                         FROM " . TABLE_PRODUCTS_PARAMETERS . " p, " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . " pd
                         WHERE p.parameters_id=pd.parameters_id
                         AND p.group_id=" . $value . "
                         ORDER BY p.sort_order");
            while ($erg = xtc_db_fetch_array($sql)) {
                $names[$value][$erg['language_id']][] = $erg['parameters_name'];
                $values[$value][$erg['language_id']][] = $erg['parameters_value'];
            }

            $sql = xtc_db_query("SELECT DISTINCT pd.parameters_value, pd.language_id
                         FROM " . TABLE_PRODUCTS_PARAMETERS . " p, " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . " pd
                         WHERE p.parameters_id=pd.parameters_id
                         AND p.group_id=" . $value . "
                         ORDER BY p.sort_order");
            while ($erg = xtc_db_fetch_array($sql)) {
                $values[$value][$erg['language_id']][] = $erg['parameters_value'];
            }
        }
    } else {
        $messageStack->add_session(PLEASE_SELECT_GROUP, 'error');
        xtc_redirect(FILENAME_PRODUCTS_PARAMETERS, '?products_id=' . $_POST['products_id'] . '&category_path=' . $_POST['category_path']);
    }
    $languages = xtc_get_languages();
    $name = xtc_db_fetch_array(xtc_db_query("SELECT products_name FROM products_description WHERE products_id = '" . (int) $_POST['products_id'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "' "));

    $fields = array(array('id' => '10', 'text' => '10'));
    $fields[] = array('id' => '15', 'text' => '15');
    $fields[] = array('id' => '20', 'text' => '20');
    $fields[] = array('id' => '25', 'text' => '25');
    $fields[] = array('id' => '30', 'text' => '30');
}
require_once(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td width="100%" colspan="2">
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE . ' - ' . $name['products_name'] ?></td>
                            </tr>
                        </table>
                    </td>
                    <td width="100%" colspan="2">
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading">
                        <?php echo xtc_draw_form('fields', FILENAME_PRODUCTS_PARAMETERS_EDIT, '', 'POST') . xtc_draw_hidden_field('products_id', $_POST['products_id']) . xtc_draw_hidden_field('category_path', $_POST['category_path']); ?>
                        Felder: <?php
                        echo xtc_draw_pull_down_menu('fields', $fields, ($_POST['fields'] != '' ? $_POST['fields'] : '10'), 'onchange="javascript:this.form.submit();"');
                        foreach ($_POST['sel'] AS $post) {
                            echo xtc_draw_hidden_field('sel[]', $post) . "\n";
						}
                        ?>
                        </form>
								</td>
                            </tr>
                        </table>
                    </td>
                </tr>
<?php echo xtc_draw_form('parameter', FILENAME_PRODUCTS_PARAMETERS_EDIT, 'action=save', 'post', 'id="parameter" onsubmit="return checkForm();"') . xtc_draw_hidden_field('products_id', $_POST['products_id']) . xtc_draw_hidden_field('category_path', $_POST['category_path']); ?>
                <tr>
                    <td style="padding-top: 5px;" align="left" width="100%">
                        <input type="submit" class="button" value="<?php echo BUTTON_SAVE; ?>"/> 
                        <a class="button" href="<?php echo xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'products_id=' . $_POST['products_id'] . '&category_path=' . $_POST['category_path']); ?>"><?php echo BUTTON_CANCEL; ?></a>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top">
                                    <table border="0" width="100%" cellspacing="0" cellpadding="2" style="border-top: 1px solid #CCC; border-left: 1px solid #CCC;">
                                        <tr class="dataTableHeadingRow">
                                            <td class="dataTableHeadingContent2" width="5%"><?php echo TABLE_HEADING_PARAMETERS_NUMBER; ?></td>
                                            <td class="dataTableHeadingContent2" width="5%"><?php echo TABLE_HEADING_PARAMETERS_LANGUAGE; ?></td>
                                            <td class="dataTableHeadingContent2" width="45%"><?php echo TABLE_HEADING_PARAMETERS_NAME; ?></td>
                                            <td class="dataTableHeadingContent2" width="45%"><?php echo TABLE_HEADING_PARAMETERS_VALUE; ?></td>
                                        </tr>
                                        <?php
                                        foreach ($groups as $gID => $gName) {
                                            echo '<tr>';
                                            echo '<td class="dataTableHeadingContent3" colspan="4"><strong>' . $gName . '</strong></td>';
                                            echo '</tr>';

                                            if (isset($_POST['fields']) && !empty($_POST['fields']))
                                                $count = $_POST['fields'];
                                            else
                                                $count = sizeof($group_values[$gID]) < 10 ? 10 : sizeof($group_values[$gID]);

                                            for ($i = 0; $i < $count; $i++) {
                                                for ($j = 0, $n = sizeof($languages); $j < $n; $j++) {
                                                    if ($i % 2 == 0)
                                                        $f = ' class="dataTableRow"';
                                                    else
                                                        $f = '';
                                                    echo '<tr' . $f . '>';
                                                    if ($j == 0)
                                                        echo '<td class="dataTableHeadingContent2" rowspan="' . $n . '">' . ($i + 1) . '</td>';
                                                    echo '<td class="dataTableHeadingContent2">' . xtc_image(DIR_WS_LANGUAGES . $languages[$j]['directory'] . '/' . $languages[$j]['image'], $languages[$j]['name']) . '</td>';
                                                    $names_sel = '<select style="width:90%" name="parameters_' . $gID . '_' . $i . '_' . $languages[$j]['id'] . '_selected_name"><option></option>';
                                                    if (sizeof($names[$gID][$languages[$j]['id']]))
                                                        foreach ($names[$gID][$languages[$j]['id']] as $name_opt)
                                                            $names_sel .= "<option>" . $name_opt . "</option>";
                                                    $names_sel .= '</select>';
                                                    $values_sel = '<select style="width:90%" name="parameters_' . $gID . '_' . $i . '_' . $languages[$j]['id'] . '_selected_value"><option></option>';
                                                    if (sizeof($values[$gID][$languages[$j]['id']]))
                                                        foreach ($values[$gID][$languages[$j]['id']] as $value_opt)
                                                            $values_sel .= "<option>" . $value_opt . "</option>";
                                                    $values_sel .= "</select>";
                                                    echo "<td class=\"dataTableHeadingContent2\">" . $names_sel . "&nbsp;" . xtc_draw_input_field('parameters_' . $gID . '_' . $i . '_' . $languages[$j]['id'] . '_type_name', $group_values[$gID][$i][$languages[$j]['id']]['name'], 'size="40"') . "</td>";
                                                    echo "<td class=\"dataTableHeadingContent2\">" . $values_sel . "&nbsp;" . xtc_draw_input_field('parameters_' . $gID . '_' . $i . '_' . $languages[$j]['id'] . '_type_value', $group_values[$gID][$i][$languages[$j]['id']]['value'], 'size="40"') . "</td>";
                                                    echo "</tr>";
                                                }
                                            }
                                        }
                                        ?>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 5px;" colspan="2">
                                    <input type="submit" class="button" value="<?php echo BUTTON_SAVE; ?>"/> 
                                    <a class="button" href="<?php echo xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'products_id=' . $_POST['products_id'] . '&category_path=' . $_POST['category_path']); ?>"><?php echo BUTTON_CANCEL; ?></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
