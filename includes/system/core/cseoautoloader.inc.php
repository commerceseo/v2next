<?php

/* -----------------------------------------------------------------
 * 	$Id: cseoautoloader.inc.php 1056 2014-05-17 13:17:56Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2011 Gambio GmbH
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

class cseoautoloader {

    var $v_class_mapping_mode = NULL;
    var $v_frontend_classes_array = NULL;
    var $v_adminclasses_array = NULL;

    function cseoautoloader($p_mapping_mode) {
        $this->v_frontend_classes_array = array(
            'login' => DIR_WS_CLASSES . 'accounting/class.login.php',
            'create_account' => DIR_WS_CLASSES . 'accounting/class.create_account.php',
            'account' => DIR_WS_CLASSES . 'accounting/class.account.php',
            'cseo_navigation' => DIR_WS_CLASSES . 'class.navigation.php',
            'breadcrumb' => DIR_WS_CLASSES . 'class.breadcrumb.php',
            'main' => DIR_WS_CLASSES . 'class.main.php',
            'messageStack' => DIR_WS_CLASSES . 'class.message_stack.php',
            'order_total' => DIR_WS_CLASSES . 'class.order_total.php',
            'paypal_checkout' => DIR_WS_CLASSES . 'class.paypal_checkout.php',
            'shoppingCart' => DIR_WS_CLASSES . 'class.shopping_cart.php',
            'wishList' => DIR_WS_CLASSES . 'class.wish_list.php',
            'XMLParser' => DIR_WS_CLASSES . 'class.xmlparserv4.php',
            'xtcPrice' => DIR_WS_CLASSES . 'class.xtcprice.php',
            'language' => DIR_WS_CLASSES . 'class.language.php',
            'order' => DIR_WS_CLASSES . 'class.order.php',
            'payment' => DIR_WS_CLASSES . 'class.payment.php',
            'product' => DIR_WS_CLASSES . 'class.product.php',
            'blog' => DIR_WS_CLASSES . 'class.blog.php',
            'shipping' => DIR_WS_CLASSES . 'class.shipping.php',
            'classdefault' => DIR_WS_CLASSES . 'class.default.php',
            'splitPageResults' => DIR_WS_CLASSES . 'class.split_page_results.php',
            'vat_validation' => DIR_WS_CLASSES . 'class.vat_validation.php',
            'Browser' => DIR_WS_CLASSES . 'class.browser.php',
			'shareCount' => DIR_WS_CLASSES . 'class.sharecount.php',
			'getGraduatedStaffel' => DIR_WS_CLASSES . 'class.getstaffel.php',
			'GetAttributesListing' => DIR_WS_CLASSES . 'class.attributeslisting.php',
            'Checkout' => DIR_WS_CLASSES . 'class.checkout.php',
            'InputFilter' => DIR_WS_CLASSES . 'class.inputfilter.php',
            'httpClient' => DIR_WS_CLASSES . 'class.http_client.php'
        );
        $this->v_admin_classes_array = array(
            'categories' => DIR_WS_CLASSES . 'class.categories.php',
			'order' => DIR_WS_CLASSES . 'class.order.php',
			'order_total' => DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.order_total.php',
            'paypal_checkout' => DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.paypal_checkout.php',
            'xtcPrice' => DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.xtcprice.php',
            'vat_validation' => DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.vat_validation.php',
            'shoppingCart' => DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.shopping_cart.php',
            'logger' => DIR_WS_CLASSES . 'class.logger.php',
			'shipping' => DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.shipping.php',
            'language' => DIR_WS_CLASSES . 'class.language.php'
        );

        $this->v_class_mapping_mode = $p_mapping_mode;
    }

    function load($p_class) {
        # set in switch/case
        $t_class_map_array = array();

        # get default class_map
        switch ($this->v_class_mapping_mode) {
            case 'frontend':
                $t_class_map_array = $this->v_frontend_classes_array;
				$loader = 'frontend';
                break;
            case 'admin':
                $t_class_map_array = $this->v_admin_classes_array;
				$loader = 'admin';
                break;

            default:
                trigger_error('unknown class_mapping_mode: ' . $this->v_class_mapping_mode, E_USER_ERROR);
        }

        # look for overwriting user_class
        $t_user_class_file = 'includes/plugins/' . $p_class . '.inc.php';
        if (file_exists($t_user_class_file)) {
            $t_class_map_array[$p_class] = $t_user_class_file;
        }

        # load class
        if (isset($t_class_map_array[$p_class])) {
			if ($loader == 'frontend') {
				$t_mapped_class_path = $this->v_frontend_classes_array[$p_class];
			} else {
				$t_mapped_class_path = $this->v_admin_classes_array[$p_class];
			}
            cseohookfactory::load_origin_class($p_class, $t_mapped_class_path);
        } else {
            # not found in class map, try system- and user-classes
            cseohookfactory::load_class($p_class);
        }
    }
}
