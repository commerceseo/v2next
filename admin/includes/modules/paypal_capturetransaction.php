<?php
/* -----------------------------------------------------------------
 * 	$Id: paypal_capturetransaction.php 420 2013-06-19 18:04:39Z akausch $
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
defined("_VALID_XTC") or die("Direct access to this location isn't allowed.");

if (isset($error))
    echo $error;
echo xtc_draw_form('refund_transaction', FILENAME_PAYPAL, xtc_get_all_get_params(array('action')) . 'action=perform');
echo xtc_draw_hidden_field('txn_id', $ipn_data['txn_id']);
echo xtc_draw_hidden_field('amount', $ipn_data['mc_gross']);
echo xtc_draw_hidden_field('ipn_id', (int) $_GET['paypal_ipn_id']);
?>
<div class="highlightbox">
    <h1><?php echo TEXT_PAYPAL_CAPTURE_TRANSACTION; ?></h1>
    <p><?php echo TEXT_PAYPAL_NOTE_CAPTURE_INFO; ?></p>
    <table class="main" width="100%" border="0">
        <tr>
            <td colspan="2"><hr noshade></td>
        </tr>
        <tr>
            <td width="10%" nowrap="nowrap"><?php echo TEXT_PAYPAL_TXN_ID; ?></td>
            <td width="90%"><?php echo $ipn_data['txn_id']; ?></td>
        </tr>
        <tr>
            <td width="10%" valign="top"><?php echo TEXT_PAYPAL_ADRESS; ?></td>
            <td width="90%"><?php echo $ipn_data['address_name']; ?></td>
        </tr>
        <tr>
            <td width="10%" nowrap="nowrap"><?php echo TEXT_PAYPAL_PAYER_EMAIL; ?></td>
            <td width="90%" valign="middle"><?php echo $ipn_data['payer_email']; ?></td>
        </tr>
        <tr>
            <td width="10%" nowrap="nowrap"><?php echo TEXT_PAYPAL_TRANSACTION_AUTH_TOTAL; ?></td>
            <td width="90%"><?php echo $ipn_data['mc_authorization'] . ' ' . $ipn_data['mc_currency']; ?></td>
        </tr>
        <tr>
            <td width="10%"><?php echo TEXT_PAYPAL_TRANSACTION_AUTH_CAPTURED; ?></td>
            <td width="90%"><?php echo $ipn_data['mc_captured'] . ' ' . $ipn_data['mc_currency']; ?></td>
        </tr>
        <tr>
            <td width="10%"><?php echo TEXT_PAYPAL_TRANSACTION_AUTH_OPEN; ?></td>
            <td width="90%"><?php echo $ipn_data['mc_authorization'] - $ipn_data['mc_captured'] . ' ' . $ipn_data['mc_currency']; ?></td>
        </tr>
        <tr>
            <td width="10%" nowrap="nowrap"><?php echo TEXT_PAYPAL_TRANSACTION_AMOUNT; ?></td>
            <td width="90%"><?php echo xtc_draw_input_field('capture_amount', $ipn_data['mc_authorization'] - $ipn_data['mc_captured'], 'size="10"'); ?></td>
        </tr>
        <tr>
            <td width="10%" valign="top"><?php echo TEXT_PAYPAL_REFUND_NOTE; ?></td>
            <td width="90%">
                <?php echo xtc_draw_textarea_field('refund_info', '', '50', '5', ''); ?>
            </td>
        </tr>
    </table>
    <input type="submit" class="button" value="<?php echo CAPTURE; ?>">
    <?php echo '<a class="button" href="' . xtc_href_link(FILENAME_PAYPAL) . '">Zur&uuml;ck</a>'; ?>
</div>
</form>