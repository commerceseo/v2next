<?php
/**
 * @version xtc3 - $Date: 2013-06-05 15:01:19 +0200 (Mi, 05 Jun 2013) $
 * @author Payment Network AG (integration@payment-network.com)
 * @link http://www.payment-network.com/
 *
 * @link http://www.xt-commerce.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 *
 ***********************************************************************************
 * this file contains code based on:
 * (c) 2000 - 2001 The Exchange Project
 * (c) 2001 - 2003 osCommerce, Open Source E-Commerce Solutions
 * (c) 2003	 nextcommerce (account_history_info.php,v 1.17 2003/08/17); www.nextcommerce.org
 * (c) 2003 - 2006 XT-Commerce
 * Released under the GNU General Public License
 ***********************************************************************************
 *
 * $Id: ot_sofort.php 420 2013-06-19 18:04:39Z akausch $
 *
 */

$num = 3;

define('MODULE_ORDER_TOTAL_SOFORT_TITLE', 'sofort.com discount module');
define('MODULE_ORDER_TOTAL_SOFORT_DESCRIPTION', 'Discount for payment by sofort.com');

define('MODULE_ORDER_TOTAL_SOFORT_STATUS_TITLE', 'Show discount');
define('MODULE_ORDER_TOTAL_SOFORT_STATUS_DESC', 'Do you want to turn on the payment discount?');

define('MODULE_ORDER_TOTAL_SOFORT_SORT_ORDER_TITLE', 'sort sequence');
define('MODULE_ORDER_TOTAL_SOFORT_SORT_ORDER_DESC', 'sort sequence');

define('MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SU_TITLE', 'Discounts for sofortueberweisung.de');
define('MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SU_DESC', 'Discount (minimum value: percentage&fixed amount)');

define('MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SL_TITLE', 'Discounts for sofortlastschrift.de');
define('MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SL_DESC', 'Discount (minimum value: percentage&fixed amount)');

define('MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SR_TITLE', 'Discounts for sofortrechnung.de');
define('MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SR_DESC', 'Discount (minimum value: percentage&fixed amount)');

define('MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SV_TITLE', 'Discounts for sofortvorkasse.de');
define('MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SV_DESC', 'Discount (minimum value: percentage&fixed amount)');

define('MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_LS_TITLE', 'Discounts for Lastschrift by sofort');
define('MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_LS_DESC', 'Discount (minimum value: percentage&fixed amount)');

define('MODULE_ORDER_TOTAL_SOFORT_INC_SHIPPING_TITLE', 'Including shipping');
define('MODULE_ORDER_TOTAL_SOFORT_INC_SHIPPING_DESC', 'Shipping costs are calculated with discount');

define('MODULE_ORDER_TOTAL_SOFORT_INC_TAX_TITLE', 'Inclusive Ust');
define('MODULE_ORDER_TOTAL_SOFORT_INC_TAX_DESC', 'Ust with discount');

define('MODULE_ORDER_TOTAL_SOFORT_CALC_TAX_TITLE', 'Sales tax calculation');
define('MODULE_ORDER_TOTAL_SOFORT_CALC_TAX_DESC', 're-calculate the tax amount');

define('MODULE_ORDER_TOTAL_SOFORT_ALLOWED_TITLE', 'Allowed zones');
define('MODULE_ORDER_TOTAL_SOFORT_ALLOWED_DESC' , 'Please enter <b>einzeln</b> the zones, which should be allowed for this module. (eg allow AT, DE (if empty, all zones))');

define('MODULE_ORDER_TOTAL_SOFORT_DISCOUNT', 'discount');
define('MODULE_ORDER_TOTAL_SOFORT_FEE', 'extra charge');

define('MODULE_ORDER_TOTAL_SOFORT_TAX_CLASS_TITLE','tax class');
define('MODULE_ORDER_TOTAL_SOFORT_TAX_CLASS_DESC','The tax class is irrelevant and only serves to prevent an error message.');

define('MODULE_ORDER_TOTAL_SOFORT_BREAK_TITLE','Multiple calculation');
define('MODULE_ORDER_TOTAL_SOFORT_BREAK_DESC','Should multiple calculations be possible? If not, it is cancelled after the first suitable discount.');
?>