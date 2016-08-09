<?php
/* --------------------------------------------------------------
  protectedshops.php 2014-05-26_1650 mabr
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
  (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once 'includes/application_top.php';
defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);
define('PAGE_URL', GM_HTTP_SERVER . DIR_WS_ADMIN . basename(__FILE__));

function getContentPages() {
    $t_language_id = 2;
    $t_query = 'SELECT content_title, content_group FROM content_manager WHERE languages_id = ' . (int) $t_language_id;
    $t_content_pages = array();
    $t_result = xtc_db_query($t_query);
    while ($t_row = xtc_db_fetch_array($t_result)) {
        $t_content_pages[$t_row['content_group']] = $t_row;
    }
    return $t_content_pages;
}

$coo_ps = cseohookfactory::create_object('ProtectedShops', array());
$t_config = $coo_ps->getConfig();

$messages_ns = 'messages_' . basename(__FILE__);
if (!isset($_SESSION[$messages_ns])) {
    $_SESSION[$messages_ns] = array();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['config']) == false) {
        $coo_ps->setConfig($_POST['config']);
        $_SESSION[$messages_ns][] = $coo_ps->get_text('configuration_saved');
    }

    if (isset($_POST['cmd']) && $_POST['cmd'] == 'update_document') {
        try {
            $coo_ps->updateDocument($_POST['document_name'], null, true);
            $_SESSION[$messages_ns][] = $coo_ps->get_text('document_updated');
        } catch (Exception $e) {
            $_SESSION[$messages_ns][] = $coo_ps->get_text('document_update_failed') . ': ' . $e->getMessage();
        }
    }

    if (isset($_POST['cmd']) && $_POST['cmd'] == 'use_document') {
        $coo_ps->useDocument($_POST['document_name']);
        $_SESSION[$messages_ns][] = $coo_ps->get_text('using_document_as_per_configuration');
    }

    if (isset($_POST['cmd']) && $_POST['cmd'] == 'update_and_use_all') {
        try {
            $coo_ps->updateAndUseAll();
            $_SESSION[$messages_ns][] = $coo_ps->get_text('all_documents_updated_and_used');
        } catch (Exception $e) {
            $_SESSION[$messages_ns][] = $coo_ps->get_text('an_error_occurred_during_update_of_documents');
        }
    }

    xtc_redirect(PAGE_URL);
}

$messages = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

if ($coo_ps->isConfigured()) {
    $t_docinfo_array = array();
    $t_localdocs_array = array();

    try {
        $t_docinfo_array = $coo_ps->getDocumentInfo();
        foreach ($t_docinfo_array as $t_docname => $t_docdate) {
            $t_localdocs_array[$t_docname] = array();
            foreach ($coo_ps->valid_formats as $t_format) {
                $t_localdocs_array[$t_docname][$t_format] = $coo_ps->getLatestDocument($t_docname, $t_format);
            }
        }
    } catch (Exception $e) {
        $messages[] = $coo_ps->get_text('protected_shops_unreachable');
    }

    $t_content_pages = getContentPages();

    // if ($t_config['use_for_pdf_conditions'] == true) {
        // $t_cb_use_for_pdf_conditions_yes = 'checked="checked"';
        // $t_cb_use_for_pdf_conditions_no = '';
    // } else {
        // $t_cb_use_for_pdf_conditions_yes = '';
        // $t_cb_use_for_pdf_conditions_no = 'checked="checked"';
    // }

    // switch ($t_config['use_for_pdf_withdrawal']) {
        // case 'widerruf':
            // $t_cb_use_for_pdf_withdrawal_widerruf = 'checked="checked"';
            // $t_cb_use_for_pdf_withdrawal_rueckgabe = '';
            // $t_cb_use_for_pdf_withdrawal_no = '';
            // break;
        // case 'rueckgabe':
            // $t_cb_use_for_pdf_withdrawal_widerruf = '';
            // $t_cb_use_for_pdf_withdrawal_rueckgabe = 'checked="checked"';
            // $t_cb_use_for_pdf_withdrawal_no = '';
            // break;
        // default:
            // $t_cb_use_for_pdf_withdrawal_widerruf = '';
            // $t_cb_use_for_pdf_withdrawal_rueckgabe = '';
            // $t_cb_use_for_pdf_withdrawal_no = 'checked="checked"';
    // }
}

ob_start();
require_once DIR_WS_INCLUDES . 'header.php';
?>
<table class="table">
	<tr>
		<td>
			<table class="table table-striped table-bordered">
				<tr>
					<td>##protected_shops</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
<?php foreach ($messages as $msg): ?>
				<p class="message"><?php echo $msg ?></p>
<?php endforeach; ?>

			<form class="bluegray" action="<?php echo PAGE_URL ?>" method="POST">
				<fieldset>
					<legend>##credentials</legend>
					<dl class="adminform">
						<dt><label for="shop_id">##shop_id</label></dt>
						<dd><input id="shop_id" name="config[shop_id]" size="60" type="text" value="<?php echo $t_config['shop_id'] ?>"></dd>
					</dl>
				</fieldset>

<?php if (isset($t_docinfo_array)): ?>
					<fieldset>
						<legend>##use_of_documents</legend>
						<dl>
<?php foreach ($t_docinfo_array as $t_docname => $t_docdate): ?>
								<dt><?php echo $t_docname ?></dt>
								<dd>
									<select name="config[content_group_<?php echo strtolower($t_docname) ?>]">
										<option value="-1">##do_not_use</option>
<?php foreach ($t_content_pages as $t_content_page): ?>
											<option value="<?php echo $t_content_page['content_group'] ?>"
						<?php echo $t_content_page['content_group'] == $t_config['content_group_' . strtolower($t_docname)] ? 'selected="selected"' : '' ?>>
						<?php echo $t_content_page['content_title'] ?>
											</option>
<?php endforeach ?>
									</select>
								</dd>
<?php endforeach ?>
						</dl>
					</fieldset>
<!--
					<fieldset>
						<legend>##use_in_pdf</legend>
						<dl class="adminform">
							<dt>##use_for_pdf_conditions</dt>
							<dd>
								<input id="ufpc_no" type="radio" <?php echo $t_cb_use_for_pdf_conditions_no ?> name="config[use_for_pdf_conditions]" value="0">
								<label for="ufpc_no">##no</label><br>
								<input id="ufpc_yes" type="radio" <?php echo $t_cb_use_for_pdf_conditions_yes ?> name="config[use_for_pdf_conditions]" value="1">
								<label for="ufpc_yes">##yes</label>
							</dd>
							<dt>##use_for_pdf_withdrawal</dt>
							<dd>
								<input id="ufpc_no" type="radio" <?php echo $t_cb_use_for_pdf_withdrawal_no ?> name="config[use_for_pdf_withdrawal]" value="0">
								<label for="ufpc_no">##no</label><br>
								<input id="ufpc_widerruf" type="radio" <?php echo $t_cb_use_for_pdf_withdrawal_widerruf ?> name="config[use_for_pdf_withdrawal]" value="widerruf">
								<label for="ufpc_widerruf">##widerruf</label><br>
								<input id="ufpc_rueckgabe" type="radio" <?php echo $t_cb_use_for_pdf_withdrawal_rueckgabe ?> name="config[use_for_pdf_withdrawal]" value="rueckgabe">
								<label for="ufpc_rueckgabe">##rueckgabe</label>
							</dd>
						</dl>
					</fieldset>
-->
					<fieldset>
						<legend>##update_configuration</legend>
						<dl class="adminform">
							<dt>##update_interval</dt>
							<dd>
								<input name="config[update_interval]" value="<?php echo $t_config['update_interval'] ?>">
								##update_interval_info
							</dd>
						</dl>
					</fieldset>

<?php endif ?>

				<input class="btn btn-success" type="submit" value="##save">
			</form>

<?php if (isset($t_docinfo_array)): ?>
				<h2>##documents_available</h2>

				<table class="table table-striped table-bordered">
					<tr>
						<th>##document</th><th>##last_modified</th>
<?php foreach ($coo_ps->valid_formats as $t_format): ?>
							<th>##format_<?php echo strtolower($t_format); ?></th>
<?php endforeach ?>
						<th>##update</th>
						<th>##use</th>
					</tr>
<?php foreach ($t_docinfo_array as $t_docname => $t_docdate): ?>
						<tr>
							<td><?php echo $t_docname ?></td>
							<td><?php echo $t_docdate ?></td>
<?php foreach ($coo_ps->valid_formats as $t_format): ?>
<?php
if ($t_localdocs_array[$t_docname][$t_format] == false) {
	$t_localdoc_date = $coo_ps->get_text('not_available');
} else {
	$t_localdoc_date = $t_localdocs_array[$t_docname][$t_format]['document_date'];
}
?>
								<td><?php echo $t_localdoc_date ?></td>
<?php endforeach ?>
							<td>
								<form action="<?php echo PAGE_URL ?>" method="POST">
									<input type="hidden" name="cmd" value="update_document">
									<input type="hidden" name="document_name" value="<?php echo $t_docname ?>">
									<input type="submit" value="##update">
								</form>
							</td>
							<td>
								<form action="<?php echo PAGE_URL ?>" method="POST">
									<input type="hidden" name="cmd" value="use_document">
									<input type="hidden" name="document_name" value="<?php echo $t_docname ?>">
									<input type="submit" value="##use">
								</form>
							</td>
						</tr>
						<?php endforeach ?>
				</table>

				<form action="<?php echo PAGE_URL ?>" method="post">
					<input type="hidden" name="cmd" value="update_and_use_all">
					<input type="submit" class="btn btn-success" value="##update_and_use_all">
				</form>
<!--
				<p class="cron_info">
					##cron_info<br>
					<code><?php echo HTTP_SERVER . DIR_WS_CATALOG . 'request_port.php?module=ProtectedShopsCron&key=' . FileLog::get_secure_token(); ?></code>
				</p>
-->
<?php endif ?>

<?php
require DIR_WS_INCLUDES . 'footer.php';
echo $coo_ps->replaceTextPlaceholders(ob_get_clean());
require DIR_WS_INCLUDES . 'application_bottom.php';
