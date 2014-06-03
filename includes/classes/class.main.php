<?php

/* -----------------------------------------------------------------
 * 	$Id: class.main.php 594 2013-09-10 16:12:54Z akausch $
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

class main_ORIGINAL {

    function main_ORIGINAL() {
        $this->SHIPPING = array();
        // prefetch shipping status
        $status_query = xtDBquery("SELECT shipping_status_name, shipping_status_image, shipping_status_id FROM " . TABLE_SHIPPING_STATUS . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "';");
        while ($status_data = xtc_db_fetch_array($status_query, true)) {
            $this->SHIPPING[$status_data['shipping_status_id']] = array('name' => $status_data['shipping_status_name'], 'image' => $status_data['shipping_status_image']);
        }
    }

    function getShippingStatusName($id) {
        return $this->SHIPPING[$id]['name'];
    }

    function getShippingStatusImage($id) {
        if ($this->SHIPPING[$id]['image']) {
            return 'images/icons/' . $this->SHIPPING[$id]['image'];
        } else {
            return;
        }
    }

    function getShippingLink() {
        return ' ' . SHIPPING_EXCL . '<a title="' . SHIPPING_COSTS . '" class="shipping" href="' . xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . SHIPPING_INFOS) . '"> ' . SHIPPING_COSTS . '</a>';
        ;
    }

    function getTaxNotice() {
        // no prices
        if ($_SESSION['customers_status']['customers_status_show_price'] == 0) {
            return;
        }
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
            return TAX_INFO_INCL_GLOBAL;
        }

        // excl tax + tax at checkout
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            return TAX_INFO_ADD_GLOBAL;
        }
        // excl tax
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) {
            return TAX_INFO_EXCL_GLOBAL;
        }

        return;
    }

    function getTaxInfo($tax_rate) {
        // price incl tax
        if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
            $tax_info = sprintf(TAX_INFO_INCL, $tax_rate . ' %');
        }
        // excl tax + tax at checkout
        if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $tax_info = sprintf(TAX_INFO_ADD, $tax_rate . ' %');
        }
        // excl tax
        if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) {
            $tax_info = sprintf(TAX_INFO_EXCL, $tax_rate . ' %');
        }
        return $tax_info;
    }

    function getShippingNotice() {
        if (SHOW_SHIPPING == 'true') {
            return ' ' . SHIPPING_EXCL . '<a title="' . SHIPPING_COSTS . '" class="shipping" href="' . xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . SHIPPING_INFOS) . '"> ' . SHIPPING_COSTS . '</a>';
            ;
        }
        return;
    }

    function getContentLink($coID, $text) {
        return '<a class="shipping" href="' . xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . $coID) . ')"><span style="color:#ff0000">' . $text . '</span></a>';
    }

}
