<?php
/* --------------------------------------------------------------
   ot_billsafe3.php 2012-12 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


define('MODULE_ORDER_TOTAL_BILLSAFE3_TITLE', 'BillSAFE Zahlungsartenaufschlag');
define('MODULE_ORDER_TOTAL_BILLSAFE3_DESCRIPTION', 'F&uuml;gt der Bestellung den BillSAFE-Zahlungsartenaufschlag hinzu.');
define('MODULE_ORDER_TOTAL_BILLSAFE3_STATUS_TITLE', 'Zahlartenaufschlag anzeigen');
define('MODULE_ORDER_TOTAL_BILLSAFE3_STATUS_DESC', 'Zahlartenaufschlag berechnen (nur nach R&uuml;cksprache mit BillSAFE!)');
define('MODULE_ORDER_TOTAL_BILLSAFE3_CHARGE_TITLE', 'Geb&uuml;hrenstaffel');
define('MODULE_ORDER_TOTAL_BILLSAFE3_CHARGE_DESC', 'Geb&uuml;hrenstaffel im Format max_warenwert:gebuehr,max_warenwert:gebuehr,...');
define('MODULE_ORDER_TOTAL_BILLSAFE3_TAX_CLASS_TITLE', 'Steuerklasse');
define('MODULE_ORDER_TOTAL_BILLSAFE3_TAX_CLASS_DESC', 'Folgende Steuerklasse für den Mindermengenzuschlag verwenden.');
define('MODULE_ORDER_TOTAL_BILLSAFE3_SORT_ORDER_TITLE', 'Sortierreihenfolge');
define('MODULE_ORDER_TOTAL_BILLSAFE3_SORT_ORDER_DESC', 'Anzeigereihenfolge.');
define('MODULE_ORDER_TOTAL_BILLSAFE3_DESCRIPTION_PAYMENT_MISSING', '<br><br><strong>Bitte installieren und konfigurieren Sie zuerst das BillSAFE-Zahlungsmodul.</strong>');

