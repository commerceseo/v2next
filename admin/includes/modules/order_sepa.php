<?php
/* --------------------------------------------------------------
  sepa_order.php 2013-12-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2013 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */
defined("_VALID_XTC") or die("Direct access to this location isn't allowed.");

$t_result = xtc_db_query('SHOW TABLES LIKE "sepa";');

if(xtc_db_num_rows($t_result) > 0)
{
	$t_result = xtc_db_query("SELECT * FROM sepa WHERE orders_id = '" . xtc_db_input($_GET['oID']) . "'");

	if(xtc_db_num_rows($t_result) == 1)
	{
		$t_result_array = xtc_db_fetch_array($t_result);
		if($t_result_array['sepa_bankname'] || $t_result_array['sepa_bic'] || $t_result_array['sepa_iban'])
		{
		?>
			
				<tr>
					<td width="80" class="main gm_strong" valign="top">
						<?php echo TEXT_BANK_OWNER; ?>
					</td>
					<td colspan="5" class="main" valign="top">
						<?php 
						$sepa_owner = $t_result_array['sepa_owner'];
						$sepa_owner = base64_decode($sepa_owner);
						$sepa_owner = str_replace(SALT_KEY, '', $sepa_owner);
						echo $sepa_owner; 
						?>
					</td>
				</tr>
				<tr>
					<td width="80" class="main gm_strong" valign="top">
						IBAN:
					</td>
					<td colspan="5" class="main" valign="top">
						<?php 
						$sepa_iban = $t_result_array['sepa_iban'];
						$sepa_iban = base64_decode($sepa_iban);
						$sepa_iban = str_replace(SALT_KEY, '', $sepa_iban);
						echo $sepa_iban; 
						?>
					</td>
				</tr>
				<tr>
					<td width="80" class="main gm_strong" valign="top">
						BIC:
					</td>
					<td colspan="5" class="main" valign="top">
						<?php 
						$sepa_bic = $t_result_array['sepa_bic'];
						$sepa_bic = base64_decode($sepa_bic);
						$sepa_bic = str_replace(SALT_KEY, '', $sepa_bic);
						echo $sepa_bic; 
						?>
					</td>
				</tr>
				<tr>
					<td width="80" class="main gm_strong" valign="top">
						<?php echo TEXT_BANK_NAME; ?>
					</td>
					<td colspan="5" class="main" valign="top">
						<?php 
						$sepa_bankname = $t_result_array['sepa_bankname'];
						$sepa_bankname = base64_decode($sepa_bankname);
						$sepa_bankname = str_replace(SALT_KEY, '', $sepa_bankname);
						echo $sepa_bankname; 
						?>
					</td>
				</tr>

				<?php
				if($t_result_array['sepa_status'] == 0)
				{
				?>
				<tr>
					<td width="80" class="main gm_strong" valign="top">
						<?php echo TEXT_BANK_STATUS; ?>
					</td>
					<td colspan="5" class="main" valign="top">
						<?php echo "OK"; ?>
					</td>
				</tr>
				<?php
				}
				else
				{
				?>
				<tr>
					<td width="80" class="main gm_strong" valign="top">
						<?php echo TEXT_BANK_STATUS; ?>
					</td>
					<td colspan="5" class="main" valign="top">
						<?php echo $t_result_array['sepa_status']; ?>
					</td>
				</tr>
				<?php
				$t_error_text = '';

				switch($t_result_array['sepa_status'])
				{
					case 1 :
						$t_error_text = TEXT_BANK_ERROR_1;
						break;
					case 2 :
						$t_error_text = TEXT_BANK_ERROR_2;
						break;
					case 3 :
						$t_error_text = TEXT_BANK_ERROR_3;
						break;
					case 4 :
						$t_error_text = TEXT_BANK_ERROR_4;
						break;
					case 5 :
						$t_error_text = TEXT_BANK_ERROR_5;
						break;
					case 8 :
						$t_error_text = TEXT_BANK_ERROR_8;
						break;
					case 9 :
						$t_error_text = TEXT_BANK_ERROR_9;
						break;
				}
				?>
				<tr>
					<td width="80" class="main gm_strong" valign="top">
						<?php echo TEXT_BANK_ERRORCODE; ?>
					</td>
					<td colspan="5" class="main" valign="top">
						<?php echo $t_error_text; ?>
					</td>
				</tr>
				<tr>
					<td width="80" class="main gm_strong" valign="top">
						<?php echo TEXT_BANK_PRZ; ?>
					</td>
					<td colspan="5" class="main" valign="top">
						<?php echo $t_result_array['sepa_prz']; ?>
					</td>
				</tr>
				<?php
				}
				?>
		<?php
		}

		if($t_result_array['sepa_fax'])
		{
		?>
			<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTableRow">
				<tr>
					<td class="main gm_strong" valign="top">
						<?php echo TEXT_BANK_FAX; ?>
					</td>
				</tr>
			</table>
		<?php
		}
	}
}
