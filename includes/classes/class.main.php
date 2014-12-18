<?php

/* -----------------------------------------------------------------
 * 	$Id: class.main.php 1110 2014-06-19 11:22:13Z akausch $
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
        $status_query = xtDBquery("SELECT shipping_status_name, shipping_status_image, shipping_status_id, info_link_active FROM " . TABLE_SHIPPING_STATUS . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "';");
        while ($status_data = xtc_db_fetch_array($status_query, true)) {
            $this->SHIPPING[$status_data['shipping_status_id']] = array(
                'name' => $status_data['shipping_status_name'],
                'image' => $status_data['shipping_status_image'],
                'info_link_active' => $status_data['info_link_active']);
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

    public function getShippingStatusInfoLinkActive($p_id) {
        if ($this->SHIPPING[$p_id]['info_link_active'] == '1') {
            $query = xtc_db_query("SELECT countries_id FROM " . TABLE_COUNTRIES . " WHERE status = '1';");
            if (xtc_db_num_rows($query) > 1) {
                $info_link = ' ' . '<a rel="nofollow" title="' . SHIPPING_COSTS . '" class="shipping" href="' . xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . SHIPPING_INFOS, $request_type = 'SSL' ? 'SSL' : 'NONSSL') . '"> ' . SHIPPING_AUSLAND . '</a>';
            } else {
                $info_link = '';
            }
        } else {
            $info_link = '';
        }
        return $info_link;
    }

    function getShippingLink() {
        return ' ' . SHIPPING_EXCL . '<a rel="nofollow" title="' . SHIPPING_COSTS . '" class="shipping" href="' . xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . SHIPPING_INFOS) . '"> ' . SHIPPING_COSTS . '</a>';
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
            return ' ' . SHIPPING_EXCL . '<a rel="nofollow" title="' . SHIPPING_COSTS . '" class="shipping" href="' . xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . SHIPPING_INFOS) . '"> ' . SHIPPING_COSTS . '</a>';
            ;
        }
        return;
    }

    function getContentLink($coID, $text) {
        return '<a rel="nofollow" class="shipping" href="' . xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . $coID) . ')"><span style="color:#ff0000">' . $text . '</span></a>';
    }

}
