<?php

/* -----------------------------------------------------------------
 * 	ID:						cart_actions.php
 * 	Letzter Stand:			v2.3
 * 	zuletzt geaendert von:	cseoak
 * 	Datum:					2012/11/19
 *
 * 	Copyright (c) since 2010 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */


// Shopping cart actions
if (isset($_GET['action'])) {
    // redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled
    if ($session_started == false) {
        xtc_redirect(xtc_href_link(FILENAME_COOKIE_USAGE));
    }

    if (DISPLAY_CART == 'true') {
        $goto = FILENAME_SHOPPING_CART;
        $parameters = array(
            'action',
            'cPath',
            'products_id',
            'pid'
        );
    } else {
        if (basename($_SERVER['SCRIPT_NAME']) == 'commerce_seo_url.php')
            $goto = $_REQUEST['linkurl'];
        else
            $goto = basename($_SERVER['SCRIPT_NAME']);

        if ($_GET['action'] == 'buy_now') {
            $parameters = array(
                'action',
                'pid',
                'products_id',
                'BUYproducts_id'
            );
        } else {
            $parameters = array(
                'action',
                'pid',
                'BUYproducts_id',
                'info'
            );
        }
    }
    if (!is_object($_SESSION['cart'])) {
        $_SESSION['cart'] = new shoppingCart();
    }
    switch ($_GET['action']) {
        // customer wants to update the product quantity in their shopping cart
        case 'update_product' :
            // Versandkosten im Warenkorb
            if (isset($_POST['country'])) {
                $_SESSION['country'] = xtc_remove_non_numeric($_POST['country']);
            }
            if (!is_object($_SESSION['cart'])) {
                break;
            }

            for ($i = 0, $n = sizeof($_POST['products_id']); $i < $n; $i ++) {
                if ($_POST['submit_target'] == 'wishlist') {
                    if (in_array($_POST['products_id'][$i], (is_array($_POST['cart_delete']) ? $_POST['cart_delete'] : array()))) {
                        $_SESSION['wishList']->remove($_POST['products_id'][$i]);
                    } else {
                        if ($_POST['cart_quantity'][$i] > MAX_PRODUCTS_QTY)
                            $_POST['cart_quantity'][$i] = MAX_PRODUCTS_QTY;
                        $attributes = ($_POST['id'][$_POST['products_id'][$i]]) ? $_POST['id'][$_POST['products_id'][$i]] : '';
                        $_SESSION['wishList']->add_cart($_POST['products_id'][$i], xtc_remove_non_numeric($_POST['cart_quantity'][$i]), $attributes, false);
                    }
                    $goto = FILENAME_WISH_LIST;
                } else {
                    if (in_array($_POST['products_id'][$i], (is_array($_POST['cart_delete']) ? $_POST['cart_delete'] : array()))) {
                        $_SESSION['cart']->remove($_POST['products_id'][$i]);
					// freitext_module
					unset($_SESSION['cart_freitext']);
                    } else {
                        if ($_POST['cart_quantity'][$i] > MAX_PRODUCTS_QTY)
                            $_POST['cart_quantity'][$i] = MAX_PRODUCTS_QTY;
                        $attributes = ($_POST['id'][$_POST['products_id'][$i]]) ? $_POST['id'][$_POST['products_id'][$i]] : '';
                        $_SESSION['cart']->add_cart($_POST['products_id'][$i], xtc_remove_non_numeric($_POST['cart_quantity'][$i]), $attributes, false);
                    }
                }
            }

            xtc_redirect(xtc_href_link($goto, xtc_get_all_get_params($parameters)));
            break;

        case 'wishlist' :
            $permission = xtc_db_fetch_array(xtc_db_query("SELECT group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " AS customer_group, products_fsk18 FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . (int) $_GET['products_id'] . "'"));

            if ($permission['products_fsk18'] == '1' && $_SESSION['customers_status']['customers_fsk18'] == '1') {
                xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['products_id'], 'NONSSL'));
            }
            if ($_SESSION['customers_status']['customers_fsk18_display'] == '0' && $permission['products_fsk18'] == '1') {
                xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['products_id'], 'NONSSL'));
            }
            if (GROUP_CHECK == 'true') {
                if ($permission['customer_group'] != '1') {
                    xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['products_id']));
                }
            }
            if (isset($_SESSION['wishList'])) {
                $_SESSION['wishList']->add_cart((int) $_GET['products_id'], $_SESSION['wishList']->get_quantity((int) $_GET['products_id']) + 1);
            } else {
                xtc_redirect(xtc_href_link(FILENAME_DEFAULT));
            }
            xtc_redirect(xtc_href_link('wish_list.php', xtc_get_all_get_params(array('action', 'products_id'))));
            break;


		case 'add_product' :
				//Properties Beta
				$f_products_id = (int) $_POST['products_id'];
				$t_products_properties_combis_id = 0;
				if(isset($_POST['properties_values_ids'])) {
					$coo_properties_control = cseohookfactory::create_object('PropertiesControl');
					$t_products_properties_combis_id = $coo_properties_control->get_combis_id_by_value_ids_array(xtc_get_prid($f_products_id), $_POST['properties_values_ids']);
					if($t_products_properties_combis_id == 0) {
						die('combi not available');
					}
				}
				// echo '<pre>';
				// print_r($_POST);
				// echo '</pre>';die;
			// $c_products_id = gm_string_filter($t_products_id, '0-9{}x');
			//Properties Beta

			if($_POST['product_options_id_freitext']) {
				$query_res = xtc_db_fetch_array(xtc_db_query("SELECT products_options_values_id FROM products_options_values WHERE products_options_values_name= 'Freitext' AND language_id= ".(int)$_SESSION['languages_id'].";"));
				$_POST['id'][$_POST['product_options_id_freitext']] = $query_res['products_options_values_id'];
			}
			if($_POST['product_options_id_freitext1']) {
				$query_res1 = xtc_db_fetch_array(xtc_db_query("SELECT products_options_values_id FROM products_options_values WHERE products_options_values_name= 'Freitext1' AND language_id= ".(int)$_SESSION['languages_id'].";"));
				$_POST['id'][$_POST['product_options_id_freitext1']] = $query_res1['products_options_values_id'];
			}
			if($_POST['product_options_id_freitext2']) {
				$query_res2 = xtc_db_fetch_array(xtc_db_query("SELECT products_options_values_id FROM products_options_values WHERE products_options_values_name= 'Freitext2' AND language_id= ".(int)$_SESSION['languages_id'].";"));
				$_POST['id'][$_POST['product_options_id_freitext2']] = $query_res2['products_options_values_id'];
			}
			if (isset($_POST['products_id'])) {
				if ($_POST['products_qty'] > MAX_PRODUCTS_QTY)
					$_POST['products_qty'] = MAX_PRODUCTS_QTY;
				if($_POST['submit_target'] == 'wishlist') {
					if (PRODUCT_DETAILS_TAB_ACCESSORIES == 'true') {
					$pids = count($_POST['products_id']);
						for($i = 0; $i < $pids; $i++){
							if ($i == 0) {
								$t_ids = $_POST['id']; 
							} else {
								$t_ids = array();	
							}
							$_SESSION['wishList']->add_cart((int)$_POST['products_id'][$i], $_SESSION['wishList']->get_quantity(xtc_get_uprid($_POST['products_id'][$i], $_POST['id']))+$_POST['products_qty'], $_POST['id']);
						}
					} else { 
						$_SESSION['wishList']->add_cart((int)$_POST['products_id'], $_SESSION['wishList']->get_quantity(xtc_get_uprid($_POST['products_id'], $_POST['id']))+$_POST['products_qty'], $_POST['id']);
					}
					$goto = 'wish_list.php';
				} elseif ($_POST['cart_quantity'] != ''){
					$_SESSION['cart']->add_cart((int)$_POST['products_id'], $_SESSION['cart']->get_quantity($_POST['products_id']) + $_POST['cart_quantity'], $_SESSION['cart']->get_attributes_from_id ($_POST['products_id']), true, $t_products_properties_combis_id);
					$_SESSION['wishList']->remove($_POST['products_id']);
				} elseif (isset($_POST['master']) && !empty($_POST['master'])) {
					//Master Slave
					if (PRODUCT_DETAILS_TAB_ACCESSORIES == 'true') {
						$pids = count($_POST['products_id']);
						for($i = 0; $i < $pids; $i++){
							if ($i == 0) {
								$t_ids = $_POST['id'];
							} else {
								$t_ids = array();
							}
							$tmp = $_POST['products_id'][$i];
							$_SESSION['cart']->add_cart((int)$_POST['products_id'][$i], $_SESSION['cart']->get_quantity(xtc_get_uprid($_POST['products_id'][$i], $t_ids)) + xtc_remove_non_numeric($_POST['products_master_qty'][$_POST['products_id'][$i]]), $t_ids[$tmp]);
						}
					} else {
						$_SESSION['cart']->add_cart((int)$_POST['products_id'], $_SESSION['cart']->get_quantity(xtc_get_uprid($_POST['products_id'], $t_ids)) + xtc_remove_non_numeric($_POST['products_master_qty'][$_POST['products_id']]), $t_ids[$tmp]);
					}
				} elseif (isset($_POST['attrib_count'])) {
                    $count = 1;
                    while ($count <= $_POST['attrib_count']) {
                        $qty = $_POST['attrib_qty_' . $count];
                        if ($qty > MAX_PRODUCTS_QTY) {
                            $qty = MAX_PRODUCTS_QTY;
                        }
                        unset($id);
                        $id[$_POST['attrib_opt_' . $count]] = $_POST['attrib_val_' . $count];
                        $_SESSION['cart']->add_cart((int) $_POST['products_multibuy_id'], $_SESSION['cart']->get_quantity(xtc_get_uprid($_POST['products_multibuy_id'], $id)) + xtc_remove_non_numeric($qty), $id);
                        $count ++;
                    }
                } else {
                    if (ATTRIBUTE_REQUIRED == 'true' && isset($_POST['id'])) {
						while (list ($option, $value) = each($_POST['id'])) {
							if ($value == 0) {
								$_SESSION['attributeerror'] = 'true';
									if (PRODUCT_DETAILS_TAB_ACCESSORIES == 'true') {
										xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_POST['products_id'][0] . '&' . xtc_get_all_get_params($parameters)));
									} else {
										xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_POST['products_id'] . '&' . xtc_get_all_get_params($parameters)));
									}
							}
						}
                    }
					if (PRODUCT_DETAILS_TAB_ACCESSORIES == 'true') {
						//Zubehoer
						$pids = count($_POST['products_id']);
						for($i = 0; $i < $pids; $i++){
							if ($i == 0) {
								$t_ids = $_POST['id'];
							} else {
								$t_ids = array();
							}					
				// echo '<pre>';
				// print_r($t_products_properties_combis_id);
				// echo '</pre>';die;
							$_SESSION['cart']->add_cart((int)$_POST['products_id'][$i], $_SESSION['cart']->get_quantity(xtc_get_uprid($_POST['products_id'][$i], $t_ids)) + xtc_remove_non_numeric($_POST['products_qty']), $t_ids, true, $t_products_properties_combis_id);
						}

					} else {
						$cart_quantity = (xtc_remove_non_numeric($_POST['products_qty']) + $_SESSION['cart']->get_quantity(xtc_get_uprid($_POST['products_id'], isset($_POST['id'])?$_POST['id']:'')));
						$_SESSION['cart']->add_cart((int)$_POST['products_id'], $cart_quantity, isset($_POST['id'])?$_POST['id']:'', true ,$t_products_properties_combis_id);
					}
				}
			}

			if($_POST['product_options_id_freitext']) {
				$_SESSION['cart']->add_cart2($_SESSION['new_products_id_in_cart'], array($_POST['product_options_id_freitext'] => array($_POST['id'][$_POST['product_options_id_freitext']] => $_POST['freitext'])));
			}
			if($_POST['product_options_id_freitext1']) {
				$_SESSION['cart']->add_cart2($_SESSION['new_products_id_in_cart'], array($_POST['product_options_id_freitext1'] => array($_POST['id'][$_POST['product_options_id_freitext1']] => $_POST['freitext1'])));
			}
			if($_POST['product_options_id_freitext2']) {
				$_SESSION['cart']->add_cart2($_SESSION['new_products_id_in_cart'], array($_POST['product_options_id_freitext2'] => array($_POST['id'][$_POST['product_options_id_freitext2']] => $_POST['freitext2'])));
			}			
			if (PRODUCT_DETAILS_TAB_ACCESSORIES == 'true') {
				xtc_redirect(xtc_href_link($goto, 'products_id=' .(int)$_POST['products_id'][0] . '&' . xtc_get_all_get_params($parameters)));
			} else {
				xtc_redirect(xtc_href_link($goto, 'products_id=' .(int)$_POST['products_id'] . '&' . xtc_get_all_get_params($parameters)));
			}
			break;


        case 'add_product_listing' :
            if (isset($_POST['products_id'])) {
                if ($_POST['products_qty'] > MAX_PRODUCTS_QTY)
                    $_POST['products_qty'] = MAX_PRODUCTS_QTY;
                if ($_POST['cart_quantity'] != '') {
                    $_SESSION['cart']->add_cart((int) $_POST['products_id'], $_SESSION['cart']->get_quantity($_POST['products_id']) + $_POST['cart_quantity'], $_SESSION['cart']->get_attributes_from_id($_POST['products_id']));
                } else {
                    $cart_quantity = (xtc_remove_non_numeric($_POST['products_qty']) + $_SESSION['cart']->get_quantity(xtc_get_uprid($_POST['products_id'], isset($_POST['id']) ? $_POST['id'] : '')));
                    $_SESSION['cart']->add_cart((int) $_POST['products_id'], $cart_quantity, isset($_POST['id']) ? $_POST['id'] : '');
                }
            }
            xtc_redirect(xtc_href_link($goto, 'products_id=' . (int) $_POST['products_id'] . '&' . xtc_get_all_get_params($parameters)));
            break;

        case 'check_gift' :
            require_once (DIR_FS_INC . 'xtc_collect_posts.inc.php');
            xtc_collect_posts();
            break;

        // customer wants to add a quickie to the cart (called from a box)
        case 'add_a_quickie' :
            $quicky = addslashes($_POST['quickie']);
            if (GROUP_CHECK == 'true') {
                $group_check = "AND group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
            }

            $quickie_query = xtc_db_query("SELECT 
												products_fsk18,products_id 
											FROM 
												" . TABLE_PRODUCTS . " 
											WHERE 
												products_model = '" . $quicky . "'
											AND 
												products_status = '1' " . $group_check);

            if (!xtc_db_num_rows($quickie_query)) {
                if (GROUP_CHECK == 'true') {
                    $group_check = "AND group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
                }
                $quickie_query = xtc_db_query("SELECT
													products_fsk18,products_id 
												FROM 
													" . TABLE_PRODUCTS . "
								                WHERE 
													products_model LIKE '%" . $quicky . "%' 
												AND 
													products_status = '1' " . $group_check);
            }
            if (xtc_db_num_rows($quickie_query, true) < 1) {
                xtc_redirect(xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords=' . $quicky, 'NONSSL'));
            }
            $quickie = xtc_db_fetch_array($quickie_query);
            if (xtc_has_product_attributes($quickie['products_id'])) {
                xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $quickie['products_id'], 'NONSSL'));
            } else {
                if ($quickie['products_fsk18'] == '1' && $_SESSION['customers_status']['customers_fsk18'] == '1') {
                    xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $quickie['products_id'], 'NONSSL'));
                }
                if ($_SESSION['customers_status']['customers_fsk18_display'] == '0' && $quickie['products_fsk18'] == '1') {
                    xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $quickie['products_id'], 'NONSSL'));
                }
                if ($_POST['quickie'] != '') {
                    $act_qty = $_SESSION['cart']->get_quantity(xtc_get_uprid($quickie['products_id'], 1));
                    if ($act_qty > MAX_PRODUCTS_QTY)
                        $act_qty = MAX_PRODUCTS_QTY - 1;
                    $_SESSION['cart']->add_cart($quickie['products_id'], $act_qty + 1, 1);
                    xtc_redirect(xtc_href_link($goto, xtc_get_all_get_params(array(
                        'action'
                                    )), 'NONSSL'));
                } else {
                    xtc_redirect(xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords=' . $quicky, 'NONSSL'));
                }
            }
            break;

        // performed by the 'buy now' button in product listings and review page
        case 'buy_now' :
            if (isset($_GET['BUYproducts_id'])) {
                // check permission to view product

                $permission = xtc_db_fetch_array(xtc_db_query("SELECT group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " as customer_group, products_fsk18 from " . TABLE_PRODUCTS . " where products_id='" . (int) $_GET['BUYproducts_id'] . "';"));

                // check for FSK18
                if ($permission['products_fsk18'] == '1' && $_SESSION['customers_status']['customers_fsk18'] == '1') {
                    xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['BUYproducts_id'], 'NONSSL'));
                }
                if ($_SESSION['customers_status']['customers_fsk18_display'] == '0' && $permission['products_fsk18'] == '1') {
                    xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['BUYproducts_id'], 'NONSSL'));
                }

                if (GROUP_CHECK == 'true') {
                    if ($permission['customer_group'] != '1') {
                        xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['BUYproducts_id']));
                    }
                }
                if (xtc_has_product_attributes($_GET['BUYproducts_id'])) {
                    xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['BUYproducts_id']));
                } else {
                    if (isset($_SESSION['cart'])) {
                        $_SESSION['cart']->add_cart((int) $_GET['BUYproducts_id'], $_SESSION['cart']->get_quantity((int) $_GET['BUYproducts_id']) + 1);
                    } else {
                        xtc_redirect(xtc_href_link(FILENAME_DEFAULT));
                    }
                }
            }
            xtc_redirect(xtc_href_link($goto, xtc_get_all_get_params(array('action', 'BUYproducts_id'))));
            break;

        case 'cust_order' :
            if (isset($_SESSION['customer_id']) && isset($_GET['pid'])) {
                if (xtc_has_product_attributes((int) $_GET['pid'])) {
                    xtc_redirect(xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['pid']));
                } else {
                    $_SESSION['cart']->add_cart((int) $_GET['pid'], $_SESSION['cart']->get_quantity((int) $_GET['pid']) + 1);
                }
            }
            xtc_redirect(xtc_href_link($goto, xtc_get_all_get_params($parameters)));
            break;

        case 'paypal_express_checkout' :
            $o_paypal->paypal_express_auth_call();
            xtc_redirect($o_paypal->payPalURL);
            break;
    }
}
