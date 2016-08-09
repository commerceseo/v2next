<?php

/* -----------------------------------------------------------------
 * 	$Id: categories.php 1424 2015-02-03 15:30:26Z akausch $
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

require_once ('includes/application_top.php');
require_once ('includes/classes/class.image_manipulator_gd2.php');

/* magnalister v1.0.1 */
if (function_exists('magnaExecute'))
    magnaExecute('magnaInventoryUpdate', array('action' => 'inventoryUpdate'), array('inventoryUpdate.php'));
/* END magnalister */

require_once (DIR_WS_CLASSES . 'class.categories.php');
require_once (DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
require_once (DIR_FS_INC . 'xtc_get_products_mo_images.inc.php');
require_once (DIR_WS_CLASSES . 'currencies.php');

if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
    require_once (DIR_FS_INC . 'commerce_seo.inc.php');
    !$commerceSeo ? $commerceSeo = new CommerceSeo() : false;
}

$currencies = new currencies();
$catfunc = new categories();

if ($_GET['function']) {
    switch ($_GET['function']) {
        case 'delete' :
            xtc_db_query("DELETE FROM personal_offers_by_customers_status_" . (int) $_GET['statusID'] . "
						                     WHERE products_id = '" . (int) $_GET['pID'] . "'
						                     AND quantity    = '" . (int) $_GET['quantity'] . "'");
            break;
    }
    xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&action=new_product&pID=' . (int) $_GET['pID']));
}


if (isset($_POST['multi_status_on'])) {
    //set multi_categories status=on
    if (is_array($_POST['multi_categories'])) {
        foreach ($_POST['multi_categories'] AS $category_id) {
            $catfunc->set_category_recursive($category_id, '1');
        }
    }
    //set multi_products status=on
    if (is_array($_POST['multi_products'])) {
        foreach ($_POST['multi_products'] AS $product_id) {
            $catfunc->set_product_status($product_id, '1');
        }
    }
    xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&' . xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID'))));
}

if (isset($_POST['multi_status_off'])) {
    //set multi_categories status=off
    if (is_array($_POST['multi_categories'])) {
        foreach ($_POST['multi_categories'] AS $category_id) {
            $catfunc->set_category_recursive($category_id, "0");
        }
    }
    //set multi_products status=off
    if (is_array($_POST['multi_products'])) {
        foreach ($_POST['multi_products'] AS $product_id) {
            $catfunc->set_product_status($product_id, "0");
        }
    }
    xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&' . xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID'))));
}

if ($_GET['action']) {
    if (isset($_POST['save_as_new_product'])) {
        $_GET['action'] = 'insert_product';
    }

    switch ($_GET['action']) {
        case 'setcflag' :
            if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
                if ($_GET['cID']) {
                    $catfunc->set_category_recursive($_GET['cID'], $_GET['flag']);
                }
            }
            xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&cID=' . $_GET['cID']));
            break;

        case 'setcpflag' :
            if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
                if ($_GET['cID']) {
                    $catfunc->set_category_product_recursive($_GET['cID'], $_GET['flag']);
                }
            }
            xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&cID=' . $_GET['cID']));
            break;

        case 'setpflag' :
            if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
                if ($_GET['pID']) {
                    $catfunc->set_product_status($_GET['pID'], $_GET['flag']);
                }
            }
            if ($_GET['pID']) {
                xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID']));
            } else {
                xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&cID=' . $_GET['cID']));
            }
            break;

        case 'setsflag' :
            if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
                if ($_GET['pID']) {
                    $catfunc->set_product_startpage($_GET['pID'], $_GET['flag']);
                    if ($_GET['flag'] == '1')
                        $catfunc->link_product($_GET['pID'], 0);
                    $catfunc->set_product_remove_startpage_sql($_GET['pID'], $_GET['flag']);
                    if ($_GET['flag'] == '0')
                        xtc_redirect(xtc_href_link(FILENAME_CATEGORIES));
                }
            }
            if ($_GET['pID']) {
                xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID']));
            } else {
                xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&cID=' . $_GET['cID']));
            }
            break;

        case 'unlink_startpage' :
            if (!empty($_GET['products_id'])) {
                xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = '" . (int) $_GET['products_id'] . "' AND categories_id = '0' ");
                xtc_db_query("UPDATE products SET products_startpage = '0', products_startpage_sort = '0' WHERE products_id = '" . (int) $_GET['products_id'] . "' ");
                xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=0'));
            }
            xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=0'));
            break;

        case 'update_category' :
            $catfunc->insert_category($_POST, '', 'update');
            if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
                $commerceSeo->updateSeoDBTable('category', 'update', $_POST['categories_id']);
			}
            break;

        case 'insert_category' :
            $catfunc->insert_category($_POST, $current_category_id);
            if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
                $commerceSeo->insertSeoDBTable('category');
			}
            break;

        case 'update_product' :
            $catfunc->insert_product($_POST, '', 'update');
            if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
                $commerceSeo->updateSeoDBTable('product', 'update', $_POST['products_id'], $_POST['url_old_text']);
			}
            break;

        case 'insert_product' :
            $catfunc->insert_product($_POST, $current_category_id);
            if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
                $commerceSeo->insertSeoDBTable('product');
			}
			break;

        case 'edit_crossselling' :
            $catfunc->edit_cross_sell($_GET);
            break;

        case 'edit_konfigurator' :
            if (file_exists(DIR_WS_MODULES . 'cseo_konfigurator.php')) {
			$catfunc->edit_konfigurator($_GET);
            break;
			}

        case 'multi_action_confirm' :
            if (isset($_POST['multi_delete_confirm'])) {
                //delete multi_categories
                if (is_array($_POST['multi_categories'])) {
                    foreach ($_POST['multi_categories'] AS $category_id) {
                        $catfunc->remove_categories($category_id);
                    }
                }
                //delete multi_products
                if (is_array($_POST['multi_products']) && is_array($_POST['multi_products_categories'])) {
                    foreach ($_POST['multi_products'] AS $product_id) {
                        $catfunc->delete_product($product_id, $_POST['multi_products_categories'][$product_id]);
                    }
                }
            }

            if (isset($_POST['multi_move_confirm'])) {
                //move multi_categories
                if (is_array($_POST['multi_categories']) && xtc_not_null($_POST['move_to_category_id'])) {
                    foreach ($_POST['multi_categories'] AS $category_id) {
                        $dest_category_id = xtc_db_prepare_input($_POST['move_to_category_id']);
                        if ($category_id != $dest_category_id) {
                            $catfunc->move_category($category_id, $dest_category_id);
                        }
                    }
                    if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True')
                        $commerceSeo->createSeoDBTable();
                }
                //move multi_products
                if (is_array($_POST['multi_products']) && xtc_not_null($_POST['move_to_category_id']) && xtc_not_null($_POST['src_category_id'])) {
                    foreach ($_POST['multi_products'] AS $product_id) {
                        $product_id = xtc_db_prepare_input($product_id);
                        $src_category_id = xtc_db_prepare_input($_POST['src_category_id']);
                        $dest_category_id = xtc_db_prepare_input($_POST['move_to_category_id']);
                        $catfunc->move_product($product_id, $src_category_id, $dest_category_id);
                        if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
                            $commerceSeo->updateSeoDBTable('product', 'update', $product_id);
                        }
                    }
                }
                xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $dest_category_id . '&' . xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID'))));
            }

            if (isset($_POST['multi_copy_confirm'])) {
                //copy multi_categories
                if (is_array($_POST['multi_categories']) && (is_array($_POST['dest_cat_ids']) || xtc_not_null($_POST['dest_category_id']))) {
                    $_SESSION['copied'] = array();
                    foreach ($_POST['multi_categories'] AS $category_id) {
                        if (is_array($_POST['dest_cat_ids'])) {
                            foreach ($_POST['dest_cat_ids'] AS $dest_category_id) {
                                if ($_POST['copy_as'] == 'link') {
                                    $catfunc->copy_category($category_id, $dest_category_id, 'link');
                                } elseif ($_POST['copy_as'] == 'duplicate') {
                                    $catfunc->copy_category($category_id, $dest_category_id, 'duplicate');
                                } else {
                                    $messageStack->add('Copy type not specified.', 'error');
                                }
                            }
                        } elseif (xtc_not_null($_POST['dest_category_id'])) {
                            if ($_POST['copy_as'] == 'link') {
                                $catfunc->copy_category($category_id, $dest_category_id, 'link');
                            } elseif ($_POST['copy_as'] == 'duplicate') {
                                $catfunc->copy_category($category_id, $dest_category_id, 'duplicate');
                            } else {
                                $messageStack->add('Copy type not specified.', 'error');
                            }
                        }
                    }
                    unset($_SESSION['copied']);
                    if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True')
                        $commerceSeo->createSeoDBTable();
                }
                //copy multi_products
                if (is_array($_POST['multi_products']) && (is_array($_POST['dest_cat_ids']) || xtc_not_null($_POST['dest_category_id']))) {
                    foreach ($_POST['multi_products'] AS $product_id) {
                        $product_id = xtc_db_prepare_input($product_id);
                        if (is_array($_POST['dest_cat_ids'])) {
                            foreach ($_POST['dest_cat_ids'] AS $dest_category_id) {
                                $dest_category_id = xtc_db_prepare_input($dest_category_id);
                                if ($_POST['copy_as'] == 'link') {
                                    $catfunc->link_product($product_id, $dest_category_id);
                                } elseif ($_POST['copy_as'] == 'duplicate') {
                                    $catfunc->duplicate_product($product_id, $dest_category_id);
                                } else {
                                    $messageStack->add('Copy type not specified.', 'error');
                                }
                            }
                        } elseif (xtc_not_null($_POST['dest_category_id'])) {
                            $dest_category_id = xtc_db_prepare_input($_POST['dest_category_id']);
                            if ($_POST['copy_as'] == 'link') {
                                $catfunc->link_product($product_id, $dest_category_id);
                            } elseif ($_POST['copy_as'] == 'duplicate') {
                                $catfunc->duplicate_product($product_id, $dest_category_id);
                            } else {
                                $messageStack->add('Copy type not specified.', 'error');
                            }
                        }
                    }
                    if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
                        $commerceSeo->insertSeoDBTable('product');
                    }
                }

                xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $dest_category_id . '&' . xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID'))));
            }

            xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&' . xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID'))));
            break;
    }
}

// check if the catalog image directory exists
if (is_dir(DIR_FS_CATALOG_IMAGES)) {
    if (!is_writeable(DIR_FS_CATALOG_IMAGES)) {
        $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
    }
} else {
    $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
}

require_once(DIR_WS_INCLUDES . 'header.php');

if ($_GET['action'] == 'new_category' || $_GET['action'] == 'edit_category') {
    include (DIR_WS_MODULES . 'new_category.php');
} elseif ($_GET['action'] == 'new_product') {
    include (DIR_WS_MODULES . 'new_product.php');
} elseif ($_GET['action'] == 'edit_konfigurator' && file_exists(DIR_WS_MODULES . 'cseo_konfigurator.php')) {
    include (DIR_WS_MODULES . 'cseo_konfigurator.php');
} elseif ($_GET['action'] == 'edit_crossselling') {
    include (DIR_WS_MODULES . 'cross_selling.php');
} else {
    if (!$cPath) {
        $cPath = '0';
    }
    include (DIR_WS_MODULES . 'categories_view.php');
}

require_once(DIR_WS_INCLUDES . 'footer.php');
require_once(DIR_WS_INCLUDES . 'application_bottom.php');