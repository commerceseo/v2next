<?php
/* -----------------------------------------------------------------
 * 	$Id: orders_status.php 964 2014-04-10 13:16:54Z akausch $
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

function xtc_get_orders_status_color($orders_status_id, $language_id = '') {
    if (!$language_id)
        $language_id = $_SESSION['languages_id'];
    $orders_status_query = xtc_db_query("SELECT orders_status_color FROM " . TABLE_ORDERS_STATUS . " WHERE orders_status_id = '" . $orders_status_id . "' AND language_id = '" . $language_id . "'");
    $orders_status = xtc_db_fetch_array($orders_status_query);
    return $orders_status['orders_status_color'];
}

switch ($_GET['action']) {
    case 'insert':
    case 'save':
        $orders_status_id = xtc_db_prepare_input($_GET['oID']);

        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $orders_status_name_array = $_POST['orders_status_name'];
            $orders_status_color = $_POST['orders_status_color'];
            $language_id = $languages[$i]['id'];

            $sql_data_array = array(
				'orders_status_name' => xtc_db_prepare_input($orders_status_name_array[$language_id]),
				'orders_status_color' => xtc_db_prepare_input($orders_status_color)
			);

            if ($_GET['action'] == 'insert') {
                if (!xtc_not_null($orders_status_id)) {
                    $next_id_query = xtc_db_query("SELECT MAX(orders_status_id) AS orders_status_id FROM " . TABLE_ORDERS_STATUS . "");
                    $next_id = xtc_db_fetch_array($next_id_query);
                    $orders_status_id = $next_id['orders_status_id'] + 1;
                }

                $insert_sql_data = array(
					'orders_status_id' => $orders_status_id,
                    'language_id' => $language_id
				);
                $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
                xtc_db_perform(TABLE_ORDERS_STATUS, $sql_data_array);
            } elseif ($_GET['action'] == 'save') {
                xtc_db_perform(TABLE_ORDERS_STATUS, $sql_data_array, 'update', "orders_status_id = '" . xtc_db_input($orders_status_id) . "' and language_id = '" . $language_id . "'");
            }
        }

        if ($_POST['default'] == 'on') {
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . xtc_db_input($orders_status_id) . "' WHERE configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
        }

        xtc_redirect(xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status_id));
        break;

    case 'deleteconfirm':
        $oID = xtc_db_prepare_input($_GET['oID']);

        $orders_status_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
        $orders_status = xtc_db_fetch_array($orders_status_query);
        if ($orders_status['configuration_value'] == $oID) {
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '' WHERE configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
        }

        xtc_db_query("DELETE FROM " . TABLE_ORDERS_STATUS . " WHERE orders_status_id = '" . xtc_db_input($oID) . "'");

        xtc_redirect(xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']));
        break;

    case 'delete':
        $oID = xtc_db_prepare_input($_GET['oID']);

        $status_query = xtc_db_query("SELECT COUNT(*) AS count FROM " . TABLE_ORDERS . " WHERE orders_status = '" . xtc_db_input($oID) . "'");
        $status = xtc_db_fetch_array($status_query);

        $remove_status = true;
        if ($oID == DEFAULT_ORDERS_STATUS_ID) {
            $remove_status = false;
            $messageStack->add(ERROR_REMOVE_DEFAULT_ORDER_STATUS, 'error');
        } elseif ($status['count'] > 0) {
            $remove_status = false;
            $messageStack->add(ERROR_STATUS_USED_IN_ORDERS, 'error');
        } else {
            $history_query = xtc_db_query("SELECT COUNT(*) AS count FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_status_id = '" . xtc_db_input($oID) . "'");
            $history = xtc_db_fetch_array($history_query);
            if ($history['count'] > 0) {
                $remove_status = false;
                $messageStack->add(ERROR_STATUS_USED_IN_HISTORY, 'error');
            }
        }
        break;
}

require(DIR_WS_INCLUDES . 'header.php');
?>
<div class="row">
	<div class="col-xs-12">
	<h1><span class="glyphicon glyphicon-gift"></span> <?php echo BOX_ORDERS_STATUS; ?></h1>
	</div>
	<div class="col-xs-9">
		<table class="table table-striped">
			<tr class="dataTableHeadingRow">
				<th class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDERS_STATUS; ?></th>
				<th class="dataTableHeadingContent" align="center">ID</th>
				<th class="dataTableHeadingContent" align="center">Color</th>
				<th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
				<?php
				$orders_status_query_raw = "SELECT * FROM " . TABLE_ORDERS_STATUS . " WHERE language_id = '" . (int)$_SESSION['languages_id'] . "' ORDER BY orders_status_id";
				$orders_status_split = new splitPageResults($_GET['page'], '20', $orders_status_query_raw, $orders_status_query_numrows);
				$orders_status_query = xtc_db_query($orders_status_query_raw);
				$rows = 1;
				while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
					if (((!$_GET['oID']) || ($_GET['oID'] == $orders_status['orders_status_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
						$oInfo = new objectInfo($orders_status);
					}

					if ((is_object($oInfo)) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id)) {
						echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '\'">' . "\n";
					} else {
						echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '\'">' . "\n";
					}

					if (DEFAULT_ORDERS_STATUS_ID == $orders_status['orders_status_id']) {
						echo '<td class="dataTableContent"><b>' . $orders_status['orders_status_name'] . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
					} else {
						echo '<td class="dataTableContent">' . $orders_status['orders_status_name'] . '</td>' . "\n";
					}
					?>
				<td align="center">
					<?php echo $orders_status['orders_status_id'] ?>
				</td>
				<td align="center" style="background-color:#<?php echo $orders_status['orders_status_color'] ?>">
					<?php echo $orders_status['orders_status_color'] ?>
				</td>
				<td align="right">
					<?php if ((is_object($oInfo)) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id)) {
						echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
					} else {
						echo '<a href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
					} ?>&nbsp;</td>
				<?php
				echo '</tr>';
				$rows++;
			}
			?>
		</table>
		<table class="table">
			<tr>
				<td class="smallText" valign="top"><?php echo $orders_status_split->display_count($orders_status_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS); ?></td>
				<td class="smallText" align="right"><?php echo $orders_status_split->display_links($orders_status_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
			</tr>
		</table>

	<?php
	if (substr($_GET['action'], 0, 3) != 'new') {
	echo '<div class="col-xs-12 text-right">';
	echo '<a class="button" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=new') . '">' . BUTTON_INSERT . '</a>'; 
	echo '</div>';
	echo '</div>';
	}
	echo '<div class="col-xs-3">';
		$heading = array();
		$contents = array();
		switch ($_GET['action']) {
			case 'new':
				$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_ORDERS_STATUS . '</b>');
				$contents = array('form' => xtc_draw_form('status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=insert'));
				$contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
				$orders_status_inputs_string = '';
				$languages = xtc_get_languages();
				for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
					$orders_status_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']');
				}
				$contents[] = array('text' => '<br />' . TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);
				$contents[] = array('text' => '<br />' . TEXT_INFO_ORDERS_STATUS_COLOR . xtc_draw_input_field('orders_status_color', '', 'class="multiple"'));
				$contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
				$contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" value="' . BUTTON_INSERT . '"/> <a class="button" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>');
				break;
			case 'edit':
				$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_ORDERS_STATUS . '</b>');
				$contents = array('form' => xtc_draw_form('status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=save'));
				$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
				$orders_status_inputs_string = '';
				$languages = xtc_get_languages();
				for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
					$orders_status_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']', xtc_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']));
				}
				$contents[] = array('text' => '<br />' . TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);
				$contents[] = array('text' => '<br />' . TEXT_INFO_ORDERS_STATUS_COLOR . xtc_draw_input_field('orders_status_color', xtc_get_orders_status_color($oInfo->orders_status_id, $languages[$i]['id']), 'class="multiple"'));
				if (DEFAULT_ORDERS_STATUS_ID != $oInfo->orders_status_id)
					$contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
				$contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id) . '">' . BUTTON_CANCEL . '</a>');
				break;
			case 'delete':
				$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDERS_STATUS . '</b>');
				$contents = array('form' => xtc_draw_form('status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=deleteconfirm'));
				$contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
				$contents[] = array('text' => '<br /><b>' . $oInfo->orders_status_name . '</b>');
				if ($remove_status)
					$contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_DELETE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id) . '">' . BUTTON_CANCEL . '</a>');
				break;
			default:
				if (is_object($oInfo)) {
					$heading[] = array('text' => '<b>' . $oInfo->orders_status_name . '</b>');
					$contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>');
					$orders_status_inputs_string = '';
					$languages = xtc_get_languages();
					for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
						$orders_status_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) . '&nbsp;' . xtc_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']);
					}
					$contents[] = array('text' => $orders_status_inputs_string);
				}
				break;
		}

		if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
			echo '            <td width="25%" class="border" valign="top">' . "\n";

			$box = new box;
			echo $box->infoBox($heading, $contents);

			echo '            </td>' . "\n";
		}
		?>
	</div>
</div>

<?php
require(DIR_WS_INCLUDES . 'footer.php');
?>
<script type="text/javascript">
head.ready(function () {

	$(".multiple").pickAColor({
	  showSpectrum : true,
		showSavedColors : true,
		saveColorsPerElement : true,
		fadeMenuToggle : true,
		showAdvanced : true,
		showBasicColors : true,
		showHexInput : true,
		allowBlank : true
	});
	
});    

</script>

<?php
require(DIR_WS_INCLUDES . 'application_bottom.php');
