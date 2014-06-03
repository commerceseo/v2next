<?php
/**
 * Barzahlen Payment Module (commerce:SEO)
 *
 * NOTICE OF LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @copyright   Copyright (c) 2013 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

// Backend Information
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TITLE', 'Barzahlen');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_DESCRIPTION', 'Barzahlen let\'s your customers pay cash online. You get a payment confirmation in real-time and you benefit from our payment guarantee and new customer groups. See how Barzahlen works: <a href="http://www.barzahlen.de/partner/funktionsweise" style="color: #63A924;" target="_blank">http://www.barzahlen.de/partner/funktionsweise</a><br/><br/>');
define('MODULE_PAYMENT_BARZAHLEN_NEW_VERSION', 'Barzahlen Update: %s! Available on <a href="http://www.barzahlen.de/partner/integration/shopsysteme/11/commerce-seo">Barzahlen.de</a>');

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
define('MODULE_PAYMENT_BARZAHLEN_DEBUG_DESC', 'Activate debugging for additional logging.');
define('MODULE_PAYMENT_BARZAHLEN_NEW_STATUS_TITLE', 'Status for unpaid orders');
define('MODULE_PAYMENT_BARZAHLEN_NEW_STATUS_DESC', 'Choose a state for unpaid orders.');
define('MODULE_PAYMENT_BARZAHLEN_PAID_STATUS_TITLE', 'Status for paid orders');
define('MODULE_PAYMENT_BARZAHLEN_PAID_STATUS_DESC', 'Choose a state for paid orders.');
define('MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS_TITLE', 'Status for expired orders');
define('MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS_DESC', 'Choose a state for expired orders.');
define('MODULE_PAYMENT_BARZAHLEN_SORT_ORDER_TITLE', 'Sort order');
define('MODULE_PAYMENT_BARZAHLEN_SORT_ORDER_DESC', 'Sort order of the view. Lowest numeral will be displayed first.');

// Frontend Texts
define('MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_DESCRIPTION', '<br/> {{image}} <br/><br/> <div>{{special}} After completing your order you get a payment slip from Barzahlen that you can easily print out or have it sent via SMS to your mobile phone. With the help of that payment slip you can pay your online purchase at one of our retail partners (e.g. supermarket).</div>');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_PARTNER', '<br/><br/> <strong>Pay at:</strong>&nbsp;');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_SANDBOX', '<br/><br/> The <strong>Sandbox Mode</strong> is active. All placed orders receive a test payment slip. Test payment slips cannot be handled by our retail partners.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_ERROR', 'Transactionerror');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_PAYMENT_ERROR', '<p>Payment via Barzahlen was unfortunately not possible. Please try again or select another payment method.</p>');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_X_ATTEMPT_SUCCESS', 'Barzahlen: payment slip requested and sent successfully');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TRANSACTION_PAID', 'Barzahlen: The payment slip was paid successfully.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_TRANSACTION_EXPIRED', 'Barzahlen: The time frame for the payment slip expired.');
define('MODULE_PAYMENT_BARZAHLEN_TEXT_PAYMENT_ATTEMPT_FAILED', 'Barzahlen: Payment via Barzahlen was unfortunately not possible.');
