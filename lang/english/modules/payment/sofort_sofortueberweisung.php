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
 * $Id: sofort_sofortueberweisung.php 420 2013-06-19 18:04:39Z akausch $
 */

//include language-constants used in all Multipay Projects
require_once 'sofort_general.php';

define('MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_EXTRA',
	MODULE_PAYMENT_SOFORT_MULTIPAY_JS_LIBS.'
	<div id="suExtraDesc">
		<div class="content" style="display:none;"></div>
	</div>
	<script type="text/javascript">
		suOverlay = new sofortOverlay(jQuery("#suExtraDesc"), "'.DIR_WS_CATALOG.'callback/sofort/ressources/scripts/getContent.php", "https://documents.sofort.com/sb/shopinfo/en");
	</script>
');

define('MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_TEXT_TITLE', 'SOFORT Banking <br /> <img src="https://images.sofort.com/en/su/logo_90x30.png" alt="SOFORT Banking"/>');
define('MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_TEXT_TITLE_ADMIN', 'SOFORT Banking <br /> <img src="https://images.sofort.com/en/su/logo_90x30.png" alt="SOFORT Banking"/>');
define('MODULE_PAYMENT_SOFORT_SU_TEXT_TITLE', 'SOFORT Banking');
define('MODULE_PAYMENT_SOFORT_SU_KS_TEXT_TITLE', 'SOFORT Banking with customer protection');
define('MODULE_PAYMENT_SOFORT_SU_LOGO_HTML', '<img src="https://images.sofort.com/en/su/logo_90x30.png" alt="SOFORT Banking"/>');
define('MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION', 'SOFORT Banking is the free of charge, TÜV certified payment method by SOFORT AG.');

define('MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGE', '     <table border="0" cellspacing="0" cellpadding="0">      <tr>        <td valign="bottom">
	<a onclick="javascript:window.open(\'https://images.sofort.com/en/su/landing.php\',\'customer information\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1020, height=900\');" style="float:left; width:auto; cursor:pointer;">
		{{image}}
	</a>
	</td>      </tr>      <tr> <td class="main">{{text}}</td>      </tr>      </table>');

define('MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGEALT', 'SOFORT Banking');

define('MODULE_PAYMENT_SOFORT_MULTIPAY_SU_CHECKOUT_TEXT', '<ul><li>Payment system with data protection certified by TÜV </li><li>No registration required</li><li>Immediate shipping of stock goods</li><li>Please keep your online banking login data ready</li></ul>');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_SU_CHECKOUT_TEXT_KS', '<ul><li>If paying with SOFORT Banking you enjoy buyer protection! [[link_beginn]]More info[[link_end]]</li><li>Payment system with TÜV-certified privacy policy</li><li>No registration needed</li><li>Goods/service will be shipped immediately, if available</li><li>Please keep your online banking data ready</li></ul>');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_SU_CHECKOUT_INFOLINK_KS', 'https://www.sofort-bank.com/eng-DE/general/kaeuferschutz/informationen-fuer-kaeufer/');
define('MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_ALLOWED_TITLE', 'Allowed zones');
define('MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_ALLOWED_DESC', 'Please enter the zones <b>separately</b>, which should be allowed for this module. (eg allow AT, DE (if empty, all zones))');
define('MODULE_PAYMENT_SOFORT_SU_STATUS_TITLE', 'Activate sofort.de module');
define('MODULE_PAYMENT_SOFORT_SU_STATUS_DESC', 'Activates/deactivates the complete module');
define('MODULE_PAYMENT_SOFORT_SU_ZONE_TITLE', MODULE_PAYMENT_SOFORT_MULTIPAY_ZONE_TITLE);
define('MODULE_PAYMENT_SOFORT_SU_ZONE_DESC', MODULE_PAYMENT_SOFORT_MULTIPAY_ZONE_DESC);

define('MODULE_PAYMENT_SOFORT_SU_SORT_ORDER_TITLE', 'sort sequence');
define('MODULE_PAYMENT_SOFORT_SU_SORT_ORDER_DESC', 'Order of display. Smallest number will show first.');
define('MODULE_PAYMENT_SOFORT_SU_KS_STATUS_TITLE', 'Customer protection activated');
define('MODULE_PAYMENT_SOFORT_SU_KS_STATUS_DESC', 'Activate customer protection for SOFORT Banking');

define('MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_STATUS_ID_TITLE', 'Confirmed order status');
define('MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_STATUS_ID_DESC', 'Confirmed order status<br />Order status after successfully completing a transaction.'); // (pending-not_credited_yet)
define('MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_STATUS_ID_TITLE', 'Order status, when money is not received');
define('MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_STATUS_ID_DESC', 'Status of the order if no money is credited on your account. (Prerequisite: account with SOFORT Bank).'); // (loss-not_credited)
define('MODULE_PAYMENT_SOFORT_SU_REC_CRE_STATUS_ID_TITLE', 'Receipt of money');
define('MODULE_PAYMENT_SOFORT_SU_REC_CRE_STATUS_ID_DESC', 'Status of orders when the money has been received on the account of SOFORT Bank.'); // (received-credited)
define('MODULE_PAYMENT_SOFORT_SU_REF_COM_STATUS_ID_TITLE', 'Partial refund');
define('MODULE_PAYMENT_SOFORT_SU_REF_COM_STATUS_ID_DESC', 'Status of orders where a partial amount was refunded to the buyer.');  // (refunded-compensation)
define('MODULE_PAYMENT_SOFORT_SU_REF_REF_STATUS_ID_TITLE', 'Full refund');
define('MODULE_PAYMENT_SOFORT_SU_REF_REF_STATUS_ID_DESC', 'Status of orders where the full amount was refunded to the buyer.'); // (refunded-refunded)

define('MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT_TITLE', 'recommended payment method');
define('MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT_DESC', '"Mark this payment method as "recommended payment method". On the payment selection page a note will be displayed right behind the payment method."');
define('MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT_TEXT', '(recommended payment method)');

define('MODULE_PAYMENT_SOFORT_SU_TMP_COMMENT', 'SOFORT Banking selected. Transaction not completed yet.');
define('MODULE_PAYMENT_SOFORT_SU_TMP_COMMENT_SELLER', 'Redirection to SOFORT - payment has not yet occurred.');

define('MODULE_PAYMENT_SOFORT_SU_TEXT_ERROR_MESSAGE', 'Payment is unfortunately not possible or has been cancelled by the customer. Please select another payment method.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_SU', 'Payment is unfortunately not possible or has been cancelled by the customer. Please select another payment method.');

define('MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_SELLER', '');
define('MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_BUYER', 'Order via SOFORT Banking successfully transmitted. Your transaction ID: {{transaction}}');
define('MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_SELLER', '');
define('MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_BUYER', 'Up till now the payment has not been confirmed {{time}}');
define('MODULE_PAYMENT_SOFORT_SU_REC_CRE_BUYER', '');
define('MODULE_PAYMENT_SOFORT_SU_REC_CRE_SELLER', 'Receipt of money on account');
define('MODULE_PAYMENT_SOFORT_SU_REF_REF_SELLER', '');
define('MODULE_PAYMENT_SOFORT_SU_REF_REF_BUYER', 'Invoice amount will be refunded {{time}}');
define('MODULE_PAYMENT_SOFORT_SU_REF_COM_SELLER', 'The invoice amount will be partially refunded. Total amount being refunded: {{refunded_amount}}. {{time}}');
define('MODULE_PAYMENT_SOFORT_SU_REF_COM_BUYER', 'A potion of the amount will be refunded.');

?>