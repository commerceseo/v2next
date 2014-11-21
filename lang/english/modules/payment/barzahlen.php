<?php
/**
 * Barzahlen Payment Module (commerce:SEO)
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

// Backend Information
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TITLE', 'Barzahlen.de');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_DESCRIPTION', 'Barzahlen let\'s your customers pay cash online. You get a payment confirmation in real-time and you benefit from our payment guarantee and new customer groups. See how Barzahlen works: <a href="https://www.barzahlen.de/en/partner/the-way-it-works" style="color: #63A924;" target="_blank">https://www.barzahlen.de/en/partner/the-way-it-works</a><br><br>
<table>
  <tr>
    <td><img src="https://cdn.barzahlen.de/images/icons/icon_registrieren.png" alt="" style="max-width: 16px; max-height: 16px;"></td>
    <td><a href="https://partner.barzahlen.de/user/register/" style="color: #60AC30;" target="_blank">Not yet registered?</a></td>
  </tr>
  <tr>
    <td><img src="https://cdn.barzahlen.de/images/icons/icon_handbuch.png" alt="" style="max-width: 16px; max-height: 16px;"></td>
    <td><a href="https://integration.barzahlen.de/en/shopsystems/commerce-seo/user-manual" style="color: #60AC30;" target="_blank">User Manual</a></td>
  </tr>
  <tr>
    <td><img src="https://cdn.barzahlen.de/images/icons/icon_conversion.png" alt="" style="max-width: 16px; max-height: 16px;"></td>
    <td><a href="https://integration.barzahlen.de/assets/downloads/Integrationsleitfaden Barzahlen.de.pdf" style="color: #60AC30;" target="_blank">Conversion Optimization</a></td>
  </tr>
  <tr>
    <td><img src="https://cdn.barzahlen.de/images/icons/icon_email.png" alt="" style="max-width: 16px; max-height: 16px;"></td>
    <td><a href="mailto:integration@barzahlen.de" style="color: #60AC30;">integration@barzahlen.de</a></td>
  </tr>
  <tr>
    <td><img src="https://cdn.barzahlen.de/images/icons/icon_telephone.png" alt="" style="max-width: 16px; max-height: 16px;"></td>
    <td>+49 (0)30 / 346 46 16 - 15</td>
  </tr>
</table>
<br><hr>');
define('MODULE_PAYMENT_BARZAHLEN_NEW_VERSION',  'Version %1$s for Barzahlen.de plugin available on: <a href="%2$s" style="font-size: 1em; color: #333;" target="_blank">%2$s</a>');

// Configuration Titles & Descriptions
define('MODULE_PAYMENT_BARZAHLEN_STATUS_TITLE', 'Enable Barzahlen Module');
define('MODULE_PAYMENT_BARZAHLEN_STATUS_DESC', 'Would you like to accept payments via Barzahlen?');
define('MODULE_PAYMENT_BARZAHLEN_ALLOWED_TITLE', 'Allowed zones');
define('MODULE_PAYMENT_BARZAHLEN_ALLOWED_DESC', 'Please enter the zones <strong>separately</strong> which should be allowed to use this module (e.g. AT,DE (leave empty if you want to allow all zones))');
define('MODULE_PAYMENT_BARZAHLEN_SANDBOX_TITLE', 'Enable Sandbox Mode');
define('MODULE_PAYMENT_BARZAHLEN_SANDBOX_DESC', 'Activate the test mode to process Barzahlen payments via sandbox.');
define('MODULE_PAYMENT_BARZAHLEN_SHOPID_TITLE', 'Shop ID');
define('MODULE_PAYMENT_BARZAHLEN_SHOPID_DESC', 'Your Barzahlen Shop ID (<a href="https://partner.barzahlen.de" style="color: #63A924;" target="_blank">https://partner.barzahlen.de</a>)');
define('MODULE_PAYMENT_BARZAHLEN_PAYMENTKEY_TITLE', 'Payment Key');
define('MODULE_PAYMENT_BARZAHLEN_PAYMENTKEY_DESC', 'Your Barzahlen Payment Key (<a href="https://partner.barzahlen.de" style="color: #63A924;" target="_blank">https://partner.barzahlen.de</a>)');
define('MODULE_PAYMENT_BARZAHLEN_NOTIFICATIONKEY_TITLE', 'Notification Key');
define('MODULE_PAYMENT_BARZAHLEN_NOTIFICATIONKEY_DESC', 'Your Notification Key (<a href="https://partner.barzahlen.de" style="color: #63A924;" target="_blank">https://partner.barzahlen.de</a>)');
define('MODULE_PAYMENT_BARZAHLEN_MAXORDERTOTAL_TITLE', 'Maximum Order Amount');
define('MODULE_PAYMENT_BARZAHLEN_MAXORDERTOTAL_DESC', 'Which is the highest cart amount to order with Barzahlen? (Max. 999.99 EUR)');
define('MODULE_PAYMENT_BARZAHLEN_DEBUG_TITLE', 'Extended Logging');
define('MODULE_PAYMENT_BARZAHLEN_DEBUG_DESC', 'Enable extended logging to log the complete communication between shop and Barzahlen beside errors.');
define('MODULE_PAYMENT_BARZAHLEN_NEW_STATUS_TITLE', 'Status for unpaid orders');
define('MODULE_PAYMENT_BARZAHLEN_NEW_STATUS_DESC', 'Choose a status for unpaid orders.');
define('MODULE_PAYMENT_BARZAHLEN_PAID_STATUS_TITLE', 'Status for paid orders');
define('MODULE_PAYMENT_BARZAHLEN_PAID_STATUS_DESC', 'Choose a status for paid orders.');
define('MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS_TITLE', 'Status for expired orders');
define('MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS_DESC', 'Choose a status for expired orders. (Pending payment slips to orders manually assigned this status will be canceled contemporary.)');
define('MODULE_PAYMENT_BARZAHLEN_SORT_ORDER_TITLE', 'Sort order');
define('MODULE_PAYMENT_BARZAHLEN_SORT_ORDER_DESC', 'Sort order for the payment selection. Lowest numeral will be displayed first.');

// Frontend Texts
define('MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_DESCRIPTION', '<br> <img id="barzahlen_logo" src="https://cdn.barzahlen.de/images/barzahlen_logo.png" alt=""> <br><br> <div id="barzahlen_description"><img id="barzahlen_special" src="https://cdn.barzahlen.de/images/barzahlen_special.png" alt="" style="float: right; margin-left: 10px; max-width: 180px; max-height: 180px;">After completing your order you get a payment slip from Barzahlen that you can easily print out or have it sent via SMS to your mobile phone. With the help of that payment slip you can pay your online purchase at one of our retail partners (e.g. supermarket).<br style="clear: both;"></div>');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_PARTNER', '<br> <strong>Pay at:</strong>&nbsp;');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_ERROR', 'Transaction Error');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_PAYMENT_ERROR', '<p>Payment via Barzahlen was unfortunately not possible. Please try again or select another payment method.</p>');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_X_ATTEMPT_SUCCESS', 'Barzahlen: payment slip requested and sent successfully');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TRANSACTION_PAID', 'Barzahlen: The payment slip was paid successfully.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TRANSACTION_EXPIRED', 'Barzahlen: The time frame for the payment slip expired.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_PAYMENT_ATTEMPT_FAILED', 'Barzahlen: Payment via Barzahlen was unfortunately not possible.');
