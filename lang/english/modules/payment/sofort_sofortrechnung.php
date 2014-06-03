<?php
/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2013-06-05 15:01:19 +0200 (Mi, 05 Jun 2013) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofort_sofortrechnung.php 420 2013-06-19 18:04:39Z akausch $
 */

//include language-constants used in all Multipay Projects
require_once 'sofort_general.php';

define('MODULE_PAYMENT_SOFORT_SR_CHECKOUT_CONDITIONS_WITH_LIGHTBOX', '<a href="https://documents.sofort.com/de/sr/privacy_de" target="_blank">I have read the Privacy Policy.</a>');
define('MODULE_PAYMENT_SOFORT_SR_CHECKOUT_CONDITIONS', '
	<script type="text/javascript">
		function showSrConditions() {
			srOverlay = new sofortOverlay(jQuery(".srOverlay"), "callback/sofort/ressources/scripts/getContent.php", "https://documents.sofort.com/de/sr/privacy_de");
			srOverlay.trigger();
		}
	</script>
	<noscript>
		<a href="https://documents.sofort.com/de/sr/privacy_de" target="_blank">I have read the Privacy Policy.</a>
	</noscript>
	<!-- comSeo-Ajax-Checkout-Bugfix: show also div, when buyer doesnt use JS -->
	<div>
		<a id="srNotice" href="javascript:void(0)" onclick="showSrConditions();">I have read the Privacy Policy.</a>
	</div>
	<div style="display:none; z-index: 1001;filter: alpha(opacity=92);filter: progid :DXImageTransform.Microsoft.Alpha(opacity=92);-moz-opacity: .92;-khtml-opacity: 0.92;opacity: 0.92;background-color: black;position: fixed;top: 0px;left: 0px;width: 100%;height: 100%;text-align: center;vertical-align: middle;" class="srOverlay">
		<div class="loader" style="z-index: 1002;position: relative;background-color: #fff;top: 40px;overflow: scroll;padding: 4px;border-radius: 7px;-moz-border-radius: 7px;-webkit-border-radius: 7px;margin: auto;width: 620px;height: 400px;overflow: scroll; overflow-x: hidden;">
			<div class="closeButton" style="position: fixed; top: 54px; background: url(callback/sofort/ressources/images/close.png) right top no-repeat;cursor:pointer;height: 30px;width: 30px;"></div>
			<div class="content"></div>
		</div>
	</div>
');

define('MODULE_PAYMENT_SOFORT_SR_TEXT_DESCRIPTION_EXTRA',
	MODULE_PAYMENT_SOFORT_MULTIPAY_JS_LIBS.'
	<div id="srExtraDesc">
		<div class="content" style="display:none;"></div>
	</div>
	<script type="text/javascript">
		srOverlay = new sofortOverlay(jQuery("#srExtraDesc"), "'.DIR_WS_CATALOG.'callback/sofort/ressources/scripts/getContent.php", "https://documents.sofort.com/rbs/shopinfo/de");
	</script>
');

define('MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG_TEXT_TITLE_ADMIN', 'Rechnung by SOFORT <br /><img src="https://images.sofort.com/en/sr/logo_90x30.png"  alt="Logo Rechnung by SOFORT"/>');
define('MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG_TEXT_TITLE', 'Kauf auf Rechnung (Purchase on account) <br /><img src="https://images.sofort.com/en/sr/logo_90x30.png"  alt="Logo Rechnung by SOFORT"/>');
define('MODULE_PAYMENT_SOFORT_SR_TEXT_TITLE', 'Kauf auf Rechnung (Purchase on account)');
define('MODULE_PAYMENT_SOFORT_SR_LOGO_HTML', '<img src="https://images.sofort.com/en/sr/logo_90x30.png"  alt="Logo Rechnung by SOFORT"/>');
define('MODULE_PAYMENT_SOFORT_SR_TEXT_ERROR_MESSAGE', 'Payment is unfortunately not possible or has been cancelled by the customer. Please select another payment method.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_SR', 'Payment is unfortunately not possible or has been cancelled by the customer. Please select another payment method.');

define('MODULE_PAYMENT_SOFORT_MULTIPAY_SR_CHECKOUT_TEXT', '');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_CONFIRM_SR', 'Acknowledge this invoice:');
define('MODULE_PAYMENT_SOFORT_SR_SORT_ORDER_TITLE', 'sort sequence');
define('MODULE_PAYMENT_SOFORT_SR_SORT_ORDER_DESC', 'Order of display. Smallest number will show first.');
define('MODULE_PAYMENT_SOFORT_SR_STATUS_TITLE', 'Activate sofort.de module');
define('MODULE_PAYMENT_SOFORT_SR_STATUS_DESC', 'Activates/deactivates the complete module');
define('MODULE_PAYMENT_SOFORT_SR_TEXT_DESCRIPTION', 'Buy on account with consumer protection');
define('MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG_ALLOWED_TITLE', 'Allowed zones');
define('MODULE_PAYMENT_SOFORT_SOFORTRECHNUNG_ALLOWED_DESC', 'Please enter the zones <b>separately</b>, which should be allowed for this module. (eg allow AT, DE (if empty, all zones))');
define('MODULE_PAYMENT_SOFORT_SR_ZONE_TITLE', MODULE_PAYMENT_SOFORT_MULTIPAY_ZONE_TITLE);
define('MODULE_PAYMENT_SOFORT_SR_ZONE_DESC', MODULE_PAYMENT_SOFORT_MULTIPAY_ZONE_DESC);

define('MODULE_PAYMENT_SOFORT_SR_PEN_CON_INV_STATUS_ID_TITLE', 'Unconfirmed order state');
define('MODULE_PAYMENT_SOFORT_SR_PEN_CON_INV_STATUS_ID_DESC', 'Oder status after successfull payment.The bill has not yet been released by the merchant.'); // (pending-confirm_invoice)
define('MODULE_PAYMENT_SOFORT_SR_LOS_CAN_STATUS_ID_TITLE', 'Order status at full cancellation');
define('MODULE_PAYMENT_SOFORT_SR_LOS_CAN_STATUS_ID_DESC', 'Cancelled order status<br />Status after a full cancellation of the invoice.');  //(loss-canceled, loss-confirmation_period_expired)
define('MODULE_PAYMENT_SOFORT_SR_PEN_NOT_CRE_YET_STATUS_ID_TITLE', 'Confirmed order status');
define('MODULE_PAYMENT_SOFORT_SR_PEN_NOT_CRE_YET_STATUS_ID_DESC', 'Order status after the successful and confirmed transaction and approval of invoice by the retailer.'); //(pending-not_credited_yet)
define('MODULE_PAYMENT_SOFORT_SR_REF_REF_STATUS_ID_TITLE', 'Cancellation after confirmation (credit)');
define('MODULE_PAYMENT_SOFORT_SR_REF_REF_STATUS_ID_DESC', 'Status for orders that have been cancelled completely after confirmation (credit).'); // (refunded_refunded)
define('MODULE_PAYMENT_SOFORT_SR_TMP_COMMENT', 'Payment method Purchase on account chosen. Transaction not finished.');
define('MODULE_PAYMENT_SOFORT_SR_TMP_COMMENT_SELLER', 'Redirection to SOFORT - payment has not yet occurred.');

define('MODULE_PAYMENT_SOFORT_SR_RECOMMENDED_PAYMENT_TITLE', 'recommended payment method');
define('MODULE_PAYMENT_SOFORT_SR_RECOMMENDED_PAYMENT_DESC', '"Mark this payment method as "recommended payment method". On the payment selection page a note will be displayed right behind the payment method."');
define('MODULE_PAYMENT_SOFORT_SR_RECOMMENDED_PAYMENT_TEXT', '(recommended payment method)');

define('MODULE_PAYMENT_SOFORT_SR_INVOICE_CONFIRMED_HISTORY', 'Order to confirm the invoice has been sent to SOFORT. Confirmation from SOFORT pending .');
define('MODULE_PAYMENT_SOFORT_SR_INVOICE_CANCELED_HISTORY', 'Order to cancel the invoice has been sent to SOFORT. Confirmation from SOFORT pending.');
define('MODULE_PAYMENT_SOFORT_SR_INVOICE_REFUNDED_HISTORY', 'Order to credit the invoice has been sent to SOFORT. Confirmation from SOFORT pending.');

/////////////////////////////////////////////////
//////// Seller-Backend and callback.php ////////
/////////////////////////////////////////////////

define('MODULE_PAYMENT_SOFORT_SR_CONFIRM_INVOICE', 'confirm invoice');
define('MODULE_PAYMENT_SOFORT_SR_CANCEL_INVOICE', 'cancel invoice');
define('MODULE_PAYMENT_SOFORT_SR_CANCEL_CONFIRMED_INVOICE', 'Credit invoice');
define('MODULE_PAYMENT_SOFORT_SR_CANCEL_INVOICE_QUESTION', 'Are you really sure you want to cancel the invoice? This process can not be undone.');
define('MODULE_PAYMENT_SOFORT_SR_CANCEL_CONFIRMED_INVOICE_QUESTION', 'Are you sure you want to credit the invoice? This action can not be undone.');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_INVOICE_CANCELED_REFUNDED', 'The invoice has been cancelled. Refund created.');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_INVOICE_CANCELED', 'The invoice has been cancelled.');

define('MODULE_PAYMENT_SOFORT_SR_DOWNLOAD_INVOICE', 'download invoice');
define('MODULE_PAYMENT_SOFORT_SR_DOWNLOAD_INVOICE_HINT', 'You can download the appropriate document (invoice preview, invoice, credit note) here.');
define('MODULE_PAYMENT_SOFORT_SR_DOWNLOAD_CREDIT_MEMO', 'download credit note');
define('MODULE_PAYMENT_SOFORT_SR_DOWNLOAD_INVOICE_PREVIEW', 'download invoice preview');

define('MODULE_PAYMENT_SOFORT_SR_EDIT_CART', 'Edit cart');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_CART', 'save cart');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_CART_QUESTION', 'Do you really want to update the cart?');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_CART_HINT', 'Save your cart-changes here. When updating a confirmed invoice, reducing the quantity or deleting an article will cause a credit.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_DISCOUNTS_HINT', 'You can adjust discounts and surcharges. Surcharges may not be increased and discount amounts have to be greater than zero. The total amount of the invoice may not be increased by the adjustment.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_DISCOUNTS_GTZERO_HINT', 'Discounts may not have a greater amount than zero.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_QUANTITY', 'adjust quantity');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_QUANTITY_HINT', 'You can adjust the number of items per position. Amounts may be decreased, but mustn\'t be increased.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_QUANTITY_TOTAL_GTZERO', 'The quantity of the item cannot be lowered, since the total sum of the invoice must not be negative.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_QUANTITY_ZERO_HINT', 'The quantity must be greater than 0. To delete an item, please mark the item at the end of the line.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_PRICE', 'adjust price');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_PRICE_HINT', 'You can adjust the price of each item per position. Prices may be decreased, but mustn\'t be increased.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_PRICE_TOTAL_GTZERO', 'The price cannot be lowered, since the total sum of the invoice must not be negative.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_PRICE_AND_QUANTITY_HINT', 'Price and quantity mustn\'t be adjusted at the same time.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_PRICE_AND_QUANTITY_NAN', 'You entered invalid characters. These adjustments allow only numbers.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_VALUE_LTZERO_HINT', 'Value must be greater than zero.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_CONFIRMED_INVOICE', 'Please enter a comment');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_CONFIRMED_INVOICE_HINT', 'When adjusting a confirmed invoice, an appropriate reason must be provided. This reason will later appear on the credit note as a comment to the article.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_SHIPPING_HINT', 'You can adjust the price for shipping. You can only reduce the amount, not increase.');
define('MODULE_PAYMENT_SOFORT_SR_UPDATE_SHIPPING_TOTAL_GTZERO', 'The shipping costs cannot be lowered, since the total sum of the invoice must not be negative.');
define('MODULE_PAYMENT_SOFORT_SR_RECALCULATION', 'will be recalculated');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_FROM_INVOICE_TOTAL_GTZERO','This item can not be deleted, since the total sum of the invoice must not be negative.');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_ARTICLE_FROM_INVOICE', 'Remove item');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_FROM_INVOICE', 'delete article');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_FROM_INVOICE_QUESTION', 'Do you really want to delete following articles: %s ?');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_FROM_INVOICE_HINT', 'Select items to delete them. Deleting an item from a confirmed invoice will cause a credit note.');
define('MODULE_PAYMENT_SOFORT_SR_REMOVE_LAST_ARTICLE_HINT', 'By reducing the number of all or by removing the last item, the invoice will be cancelled completely.');
define('MODULE_PAYMENT_SOFORT_SR_SYNC_FAILED_SELLER', 'This order contains articles which may cause problems when being synchronised with the SOFORT servers. For subsequent changes as regards shipping, discounts or articles, please compare the data stated on the PDF invoice with the data stored in the shop to avoid  faulty shipping. The shopping cart can only be changed in the merchant menau of SOFORT if need be.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_GX_CUSTOMIZER_AFFECTED', 'It is usually only items which use functions of the GX customizer.');

define('MODULE_PAYMENT_SOFORT_SR_PEN_CON_INV_BUYER', 'Order submitted successfully. Confirmation has not yet occurred.');
define('MODULE_PAYMENT_SOFORT_SR_PEN_CON_INV_SELLER', 'Order completed successfully - Invoice can be confirmed - Your transaction ID:');
define('MODULE_PAYMENT_SOFORT_SR_LOS_CAN_BUYER', 'Order cancelled');
define('MODULE_PAYMENT_SOFORT_SR_LOS_CAN_SELLER', '');
define('MODULE_PAYMENT_SOFORT_SR_PEN_NOT_CRE_YET_BUYER', 'Order confirmed and in progress.');
define('MODULE_PAYMENT_SOFORT_SR_PEN_NOT_CRE_YET_SELLER', 'The invoice has been confirmed and created.');
define('MODULE_PAYMENT_SOFORT_SR_REF_REF_BUYER', 'The invoice was credited.');
define('MODULE_PAYMENT_SOFORT_SR_REF_REF_SELLER', 'The invoice was credited. Credit was created.');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_INVOICE_REANIMATED', 'The cancellation of the invoice has been undone.');
define('MODULE_PAYMENT_SOFORT_SR_LOS_CON_PER_EXP_BUYER', 'Order cancelled');
define('MODULE_PAYMENT_SOFORT_SR_LOS_CON_PER_EXP_SELLER', 'The order was cancelled. The confirmation period has expired.');

define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CURRENT_TOTAL', 'current invoice amount:');

define('MODULE_PAYMENT_SOFORT_SR_INVOICE_CONFIRMED', 'Invoice was confirmed');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_TRANSACTION_ID', 'transaction ID');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CANCELED_REFUNDED', 'The invoice has been cancelled. Refund created. {{time}}');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CART_EDITED', 'The cart has been edited.');
define('MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CART_RESET', 'The cart has been reset.');

define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9000', 'No invoice transaction found.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9001', 'The invoice could not be confirmed.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9002', 'The provided invoice amount exceeds the credit limit.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9003', 'The invoice could not have been cancelled.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9004', 'The request contained invalid cart positions.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9005', 'The cart could not be modified.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9006', 'Access to the interface is not longer possible 30 days after receipt of payment.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9007', 'The invoice has already been canceled.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9008', 'The amount of the provided tax is too high.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9009', 'The amounts given to the VAT rates of the items relate to each other in conflict.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9010', 'Modifying the cart is not possible.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9011', 'No comment was provided to the cart update.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9012', 'You can not add positions to the cart. Similarly, the amount per invoice item can not be increased.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9013', 'There are only non factorable items in your shopping cart.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9014', 'The provided invoice number is already in use.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9015', 'The provided credit number is already in use.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9016', 'The provided order number is already in use.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9017', 'The invoice has already been confirmed.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_9018', 'There where no data updated to the invoice.');