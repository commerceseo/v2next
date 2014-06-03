<?php

/* -----------------------------------------------------------------
 * 	$Id: product_info.php 1050 2014-05-14 16:53:07Z akausch $
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
/* * ***** SHOPGATE ********* */
if (strpos(MODULE_PAYMENT_INSTALLED, 'shopgate.php') !== false && strpos($_SESSION['customers_status']['customers_status_payment_unallowed'], 'shopgate') === false) {
    include_once DIR_FS_CATALOG . 'includes/external/shopgate/base/includes/modules/product_info.php';
}
/* * ***** SHOPGATE ********* */
require_once (DIR_FS_INC . 'xtc_get_vpe_name.inc.php');

$info_smarty = new Smarty;
$info_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');

$group_check = '';

if (!is_object($product) || !$product->isProduct()) {
    $error = TEXT_PRODUCT_NOT_FOUND;
    include(DIR_WS_MODULES . FILENAME_ERROR_HANDLER);
} else {
    if (ACTIVATE_NAVIGATOR == 'true') {
        include (DIR_WS_MODULES . 'product_navigator.php');
    }

    xtDBquery("UPDATE " . TABLE_PRODUCTS_DESCRIPTION . " SET products_viewed = products_viewed+1 WHERE products_id = '" . $product->data['products_id'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "';");

    $products_price = $xtPrice->xtcGetPrice($product->data['products_id'], $format = true, 1, $product->data['products_tax_class_id'], $product->data['products_price'], 1, '', 'info');
    $price = $products_price['formated'];
	$price_uvp = '';
	if ($product->data['products_uvpprice'] > 0) {
		$price_uvp = $xtPrice->xtcAddTax($product->data['products_uvpprice'] * 1, $xtPrice->TAX[$product->data['products_tax_class_id']]);
		$price_uvp = $xtPrice->xtcFormat($price_uvp, true);
	}
    if ($product->data['products_buyable'] == 1 && $product->data['products_only_request'] == 0) {
        if ($product->data['products_minorder'] > 1) {
            $order_qty = $product->data['products_minorder'];
        } else {
            $order_qty = '1';
        }
        if ($_SESSION['customers_status']['customers_status_show_price'] != '0' && ALLOW_ADD_TO_CART == 'true') {
            if ($_SESSION['customers_status']['customers_fsk18'] == '1') {
                if ($product->data['products_fsk18'] == '0') {
                    if (PRODUCT_DETAILS_TAB_ACCESSORIES == 'true') {
                        $info_smarty->assign('ADD_QTY', xtc_draw_input_field('products_qty', $order_qty, 'size="3" class="products_qty" title="' . WCAG_QTY . '"') . ' ' . xtc_draw_hidden_field('products_id[]', $product->data['products_id']) . xtc_draw_hidden_field('products_update_id', $product->data['products_id']));
                    } else {
                        $info_smarty->assign('ADD_QTY', xtc_draw_input_field('products_qty', $order_qty, 'size="3" class="products_qty" title="' . WCAG_QTY . '"') . ' ' . xtc_draw_hidden_field('products_id', $product->data['products_id']) . xtc_draw_hidden_field('products_update_id', $product->data['products_id']));
                    }
                    $info_smarty->assign('ADD_CART_BUTTON', cseo_wk_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART));
                    if (PRODUCT_DETAILS_WISHLIST == 'true') {
                        $info_smarty->assign('ADD_WISHLIST_BUTTON', xtc_image_submit('button_to_wish_list.gif', WISHLIST, ' onclick="document.cart_quantity.submit_target.value=\'wishlist\';"'));
                    }
                }
            } else {
                if (PRODUCT_DETAILS_TAB_ACCESSORIES == 'true') {
                    $info_smarty->assign('ADD_QTY', xtc_draw_input_field('products_qty', $order_qty, 'size="3" class="products_qty" title="' . WCAG_QTY . '"') . ' ' . xtc_draw_hidden_field('products_id[]', $product->data['products_id']) . xtc_draw_hidden_field('products_update_id', $product->data['products_id']));
                    $info_smarty->assign('ADD_CART_BUTTON', cseo_wk_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART));
                } else {
                    $info_smarty->assign('ADD_QTY', xtc_draw_input_field('products_qty', $order_qty, 'size="3" class="products_qty" title="' . WCAG_QTY . '"') . ' ' . xtc_draw_hidden_field('products_id', $product->data['products_id']) . xtc_draw_hidden_field('products_update_id', $product->data['products_id']));
                    $info_smarty->assign('ADD_CART_BUTTON', cseo_wk_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART));
                }
                if (PRODUCT_DETAILS_WISHLIST == 'true') {
                    $info_smarty->assign('ADD_WISHLIST_BUTTON', xtc_image_submit('button_to_wish_list.gif', WISHLIST, ' onclick="document.cart_quantity.submit_target.value=\'wishlist\';"'));
                }
            }
        }
        $info_smarty->assign('FORM_ACTION', xtc_draw_form('cart_quantity', xtc_href_link(FILENAME_PRODUCT_INFO, xtc_get_all_get_params(array('action')) . 'action=add_product'), 'post', 'name="cart_quantity"') . xtc_draw_hidden_field('submit_target', 'cart'));
        if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
            $info_smarty->assign('JAVASCRIPT_FORM_ACTION', '<script> function onsubmitform(){ document.cart_quantity.action ="' . xtc_href_link(FILENAME_PRODUCT_INFO, xtc_get_all_get_params(array('action'))) . '?action=add_product"; return true;} </script>');
        } else {
            $info_smarty->assign('JAVASCRIPT_FORM_ACTION', '<script> function onsubmitform(){ document.cart_quantity.action ="' . xtc_href_link(FILENAME_PRODUCT_INFO, xtc_get_all_get_params(array('action'))) . 'action=add_product"; return true;} </script>');
        }
        $info_smarty->assign('FORM_END', '</form>');

        $info_smarty->assign('PRODUCTS_PRICE', $price);
		$info_smarty->assign('PRODUCTS_PRICE_PLAIN', $products_price['plain']);
        $info_smarty->assign('PRODUCTS_UVP_PRICE', $price_uvp);
        $info_smarty->assign('BUTTON_PLUS', '<a class="qty_plus products_qty_button no_target" href="' . xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product->data['products_id'] . '&qty=' . (isset($_GET['qty']) ? ($_GET['qty'] + 1) : '2')) . '">+</a>');
        $info_smarty->assign('BUTTON_MINUS', '<a class="qty_minus products_qty_button no_target" href="' . xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product->data['products_id'] . '&qty=' . (isset($_GET['qty']) && $_GET['qty'] != 1 ? ($_GET['qty'] - 1) : '1')) . '">-</a>');
        if ($product->data['products_fsk18'] == '1') {
            $info_smarty->assign('PRODUCTS_FSK18', 'true');
        }

        if (ACTIVATE_SHIPPING_STATUS == 'true') {
            $info_smarty->assign('SHIPPING_NAME', $main->getShippingStatusName($product->data['products_shippingtime']));
            $info_smarty->assign('SHIPPING_IMAGE', $main->getShippingStatusImage($product->data['products_shippingtime']));
        }
        $products_price_vpe = $xtPrice->xtcGetPrice($product->data['products_id'], false, 0, $product->data['products_tax_class_id'], $product->data['products_price']);
        if (PRODUCT_DETAILS_VPE == 'true') {
            if ($product->data['products_vpe_status'] == 1 && $product->data['products_vpe_value'] != 0.0 && $products_price_vpe > 0)
                $info_smarty->assign('PRODUCTS_VPE', $xtPrice->xtcFormat($products_price_vpe * (1 / $product->data['products_vpe_value']), true) . TXT_PER . xtc_get_vpe_name($product->data['products_vpe']));
        }
        if ($_SESSION['customers_status']['customers_status_show_price'] != 0) {
            $tax_rate = $xtPrice->TAX[$product->data['products_tax_class_id']];
            $tax_info = (DISPLAY_TAX == 'false' ? '' : $main->getTaxInfo($tax_rate));
            $info_smarty->assign('PRODUCTS_TAX_INFO', $tax_info);
            if (SHOW_SHIPPING == 'true') {
                $info_smarty->assign('PRODUCTS_SHIPPING_LINK', $main->getShippingLink());
            }
        }
        $discount = 0.00;
        if ($_SESSION['customers_status']['customers_status_public'] == 1 && $_SESSION['customers_status']['customers_status_discount'] != '0.00') {
            $discount = $_SESSION['customers_status']['customers_status_discount'];
            if ($product->data['products_discount_allowed'] < $_SESSION['customers_status']['customers_status_discount']) {
                $discount = $product->data['products_discount_allowed'];
            }
            if ($discount != '0.00') {
                $info_smarty->assign('PRODUCTS_DISCOUNT', $discount . '%');
            }
        }
        if (PRODUCT_DETAILS_SPECIALS_COUNTER == 'true') {
            //Specials Abrage für Counter
            $specials_query = xtc_db_fetch_array(xtDBquery("SELECT expires_date FROM specials WHERE products_id='" . $product->data['products_id'] . "' AND status ='1'"));
            if ($specials_query['expires_date'] != '' && $specials_query['expires_date'] != '0000-00-00 00:00:00') {
                $specials_date = $specials_query['expires_date'];
                $specials_date = substr($specials_date, 0, 4) . '/' . substr($specials_date, 5, 2) . '/' . substr($specials_date, 8, 2);
                $info_smarty->assign('PRODUCTS_SPECIAL_DATE', 'true');
                $_SESSION['SPECIAL_DATE'] = $specials_date;
            } else {
                unset($_SESSION['SPECIAL_DATE']);
            }
        }
        if ($_SESSION['customers_status']['customers_status_graduated_prices'] == 1) {
            include (DIR_WS_MODULES . FILENAME_GRADUATED_PRICE);
        }
        if (PRODUCT_DETAILS_STOCK == 'true') {
            require_once(DIR_FS_INC . 'cseo_get_stock_img.inc.php');
            $info_smarty->assign('PRODUCTS_STOCK_IMG', cseo_get_stock_img($product->data['products_quantity']));
        }
        $SPERRGUT = '';
        $t_query = xtc_db_query('SELECT products_sperrgut FROM products WHERE products_id = ' . (int) $product->data['products_id']);
        $t = xtc_db_fetch_array($t_query);
        if ($t['products_sperrgut'] > 0) {
            $SPERRGUT = $xtPrice->xtcFormat(constant('SHIPPING_SPERRGUT_' . $t['products_sperrgut']), true, 0, true);
            $info_smarty->assign('SPERRGUT', $SPERRGUT);
        }

        include (DIR_WS_MODULES . 'product_attributes.php');
    } elseif ($product->data['products_buyable'] == 0 && $product->data['products_only_request'] == 0) {
        $info_smarty->assign('PRODUCTS_PRICE', PRODUCT_NO_BUY);
    } else {
        $info_smarty->assign('PRODUCTS_PRICE', PRODUCT_ONLY_REQUEST);
        //End byeable
    }

    $info_smarty->assign('PRODUCTS_NAME', $product->data['products_name']);


    if (PRODUCT_DETAILS_MODELLNR == 'true') {
        $info_smarty->assign('PRODUCTS_MODEL', $product->data['products_model']);
    }
    if (PRODUCT_DETAILS_MANUFACTURERS_MODELLNR == 'true') {
        $info_smarty->assign('PRODUCTS_MANUFACTURERS_MODEL', $product->data['products_manufacturers_model']);
    }
    if (PRODUCT_DETAILS_EAN == 'true') {
        $info_smarty->assign('PRODUCTS_EAN', $product->data['products_ean']);
    }

    if (PRODUCT_DETAILS_WEIGHT == 'true') {
        $products_weight = $product->data['products_weight'];
		$products_weight = number_format($products_weight, 3, ',', '.');
        $info_smarty->assign('PRODUCTS_WEIGHT', $products_weight);
    }

    if (PRODUCT_DETAILS_TAGS == 'true') {
        $info_smarty->assign('PRODUCTS_TAGS', $product->getTagCloud());
    }

    if (PRODUCT_DETAILS_PRINT == 'true') {
        $info_smarty->assign('PRODUCTS_PRINT', '<a href="javascript:void(0)" onclick="javascript:window.open(\'' . xtc_href_link(FILENAME_PRINT_PRODUCT_INFO, 'products_id=' . $product->data['products_id']) . '\', \'popup\', \'toolbar=0, width=640, height=600\')">' . xtc_image_button('print.gif', IMAGE_BUTTON_PRINT) . '</a>');
    }

    if (!empty($product->data['products_movie_youtube_id'])) {
        $flash = '
		<div id="videobtn">
		<a class="youtube" href="http://www.youtube-nocookie.com/v/' . $product->data['products_movie_youtube_id'] . '&hl=de_DE&fs=1">
			<span class="css_img_button">Video ansehen</span>
		</a>
		</div>
		
		<div id="video" itemprop="video" itemscope itemtype="http://schema.org/VideoObject"> 
		<meta itemprop="name" content="' . $product->data['products_name'] . '" /> 
		<meta itemprop="description" content="' . trim(cseo_truncate(strip_tags($product->data['products_short_description']), 160)) . '" /> 
		<meta itemprop="thumbnailUrl" content="' . DIR_WS_THUMBNAIL_IMAGES . $product->data['products_image'] . '" /> 
		<meta itemprop="contentURL" content="http://www.youtube-nocookie.com/v/' . $product->data['products_movie_youtube_id'] . '&hl=de_DE&fs=1" />
		<meta itemprop="embedURL" content="http://www.youtube-nocookie.com/v/' . $product->data['products_movie_youtube_id'] . '&hl=de_DE&fs=1&feature=player_embedded" />
		</div>
		';
    }

    if (TREEPODIAACTIVE == 'true' && TREEPODIAID != '' && $product->data['products_treepodia_activate'] == '1') {
        $flash = '
		<div id="video-btn">
		<a href="javascript:showVideoDialog(\'' . $product->data['products_name'] . '\', video, \'#FFFFFF\', false)"><span class="css_img_button">Video ansehen</span></a>
		</div>
		
		<div id="video" itemprop="video" itemscope itemtype="http://schema.org/VideoObject"> 
		<meta itemprop="name" content="' . $product->data['products_name'] . '" /> 
		<meta itemprop="description" content="' . trim(cseo_truncate(strip_tags($product->data['products_short_description']), 160)) . '" /> 
		<meta itemprop="thumbnailUrl" content="' . DIR_WS_THUMBNAIL_IMAGES . $product->data['products_image'] . '" /> 
		<meta itemprop="contentURL" content="http://api.treepodia.com/video/get/' . TREEPODIAID . '/' . $product->data['products_model'] . '" />
		<meta itemprop="embedURL" content="http://api.treepodia.com/video/get/' . TREEPODIAID . '/' . $product->data['products_model'] . '" />

		<noscript>
		<object type="application/x-shockwave-flash" data="http://api.treepodia.com/video/treepodia_player.swf" width="640px" height="400px" title="product video player" rel="media:video">
		<param name="src" value="http://api.treepodia.com/video/treepodia_player.swf"/>
		<param name="flashvars" value="video=http://api.treepodia.com/video/get/' . TREEPODIAID . '/' . $product->data['products_model'] . '"/>
		</object>
		</noscript>
		</div>
		';
    }

    $info_smarty->assign('PRODUCTS_FLASH', $flash);

    if (!empty($product->data['products_movie_embeded_code'])) {
        $info_smarty->assign('PRODUCTS_VIDEO_EMBEDED', $product->data['products_movie_embeded_code']);
    }

    if (PRODUCT_DETAILS_TAB_SHORT_DESCRIPTION == 'true') {
        $info_smarty->assign('PRODUCTS_SHORT_DESCRIPTION', $product->data['products_short_description']);
    }

    if (PRODUCT_DETAILS_TAB_DESCRIPTION == 'true') {
        $products_description = $product->data['products_description'];
        $products_description = preg_replace('/##(\w+)/', '<a href="' . xtc_href_link('hashtag/\1') . '">#\1</a>', $products_description);

        $info_smarty->assign('PRODUCTS_DESCRIPTION', $products_description);
    }

    if (PRODUCT_DETAILS_TAB_MANUFACTURERS == 'true') {
        $man = xtc_db_fetch_array(xtDBquery("SELECT manufacturers_description FROM manufacturers_info WHERE manufacturers_id='" . $product->data['manufacturers_id'] . "' AND languages_id = '" . (int) $_SESSION['languages_id'] . "'"));
        $man_pic = xtc_db_fetch_array(xtDBquery("SELECT manufacturers_image, manufacturers_name  FROM manufacturers WHERE manufacturers_id='" . $product->data['manufacturers_id'] . "'"));
        $info_smarty->assign('PRODUCTS_MANUFACTURERS_NAME', $man_pic['manufacturers_name']);
        $info_smarty->assign('PRODUCTS_MANUFACTURERS_DESC', $man['manufacturers_description']);
        $info_smarty->assign('PRODUCTS_MANUFACTURERS_IMG', '<img src="' . DIR_WS_CATALOG . DIR_WS_IMAGES . $man_pic['manufacturers_image'] . ' "title="' . $man_pic['manufacturers_name'] . '" />');
    }

    if (PRODUCT_DETAILS_TAB_PRODUCT == 'true') {
        $info_smarty->assign('PRODUCTS_DESCRIPTION_PRODUCT', $product->data['products_zusatz_description']);
    }

    if (PRODUCT_DETAILS_TAB_ADD == 'true') {
        if (GROUP_CHECK == 'true') {
            $group_check = "AND group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
        }
        if (PRODUCT_DETAILS_TAB_ADD_CONTENT_GROUP_ID != '') {
            $product_details_tab_add_query = xtDBquery("SELECT
								 *
								 FROM " . TABLE_CONTENT_MANAGER . "
								 WHERE content_group='" . (int) PRODUCT_DETAILS_TAB_ADD_CONTENT_GROUP_ID . "' 
								 " . $group_check . "
								 AND languages_id='" . (int) $_SESSION['languages_id'] . "'");
            $product_details_tab_add_data = xtc_db_fetch_array($product_details_tab_add_query);
            if ($product_details_tab_add_data['content_file'] != '') {
                ob_start();
                if (strpos($product_details_tab_add_data['content_file'], '.txt')) {
                    echo '<pre>';
                }
                include (DIR_FS_CATALOG . 'media/content/' . $product_details_tab_add_data['content_file']);

                if (strpos($product_details_tab_add_data['content_file'], '.txt')) {
                    echo '</pre>';
                }
                $product_details_add_data = ob_get_contents();
                ob_end_clean();
            } else {
                $product_details_add_data = $product_details_tab_add_data['content_text'];
            }
            $info_smarty->assign('PRODUCTS_DESCRIPTION_ADD', $product_details_add_data);
        }
    }

    if (!empty($product->data['products_image'])) {
        $info_smarty->assign('img_name', $product->data['products_image']);
        $info_smarty->assign('img_nr', '1');
        $info_smarty->assign('img_path_mini', DIR_WS_CATALOG . DIR_WS_MINI_IMAGES . $product->data['products_image']);
        $info_smarty->assign('img_path_thumb', DIR_WS_CATALOG . DIR_WS_THUMBNAIL_IMAGES . $product->data['products_image']);
        $info_smarty->assign('img_path_info', DIR_WS_CATALOG . DIR_WS_INFO_IMAGES . $product->data['products_image']);
        $info_smarty->assign('img_path_popup', DIR_WS_CATALOG . DIR_WS_POPUP_IMAGES . $product->data['products_image']);
        $info_smarty->assign('img_dimension', cseo_get_img_size(DIR_WS_INFO_IMAGES . $product->data['products_image']));
        $info_smarty->assign('img_popup_dimension', cseo_get_img_size(DIR_WS_POPUP_IMAGES . $product->data['products_image']));
        $info_smarty->assign('img_mini_dimension', cseo_get_img_size(DIR_WS_MINI_IMAGES . $product->data['products_image']));
        $info_smarty->assign('img_path_org', DIR_WS_ORIGINAL_IMAGES . $product->data['products_image']);
        $info_smarty->assign('img_alt', ($product->data['products_img_alt'] != '') ? $product->data['products_img_alt'] : strip_tags($product->data['products_name']));
        $js_img = '\'' . DIR_WS_POPUP_IMAGES . $product->data['products_image'] . '\'';
        $js_title = '\'' . $product->data['products_name'] . '\'';

        $products_mo_images_query = xtc_db_query("SELECT
													image_id, image_nr, image_name, alt_langID_" . (int) $_SESSION['languages_id'] . "
												FROM
													products_images
												WHERE
													products_id = '" . $product->data['products_id'] . "'
												ORDER BY
													image_nr");

        if (xtc_db_num_rows($products_mo_images_query) > 0) {
            while ($img = xtc_db_fetch_array($products_mo_images_query)) {
                $js_img .= ',\'' . DIR_WS_POPUP_IMAGES . $img['image_name'] . '\'';
                $js_title .= ',\'' . (!empty($img['alt_langID_' . (int) $_SESSION['languages_id']]) ? $img['alt_langID_' . (int) $_SESSION['languages_id']] : strip_tags($product->data['products_name'])) . '\'';
                $more_images[] = array('name' => $img['image_name'],
                    'nr' => ($img['image_nr'] + 1),
                    'dimension' => cseo_get_img_size(DIR_WS_MINI_IMAGES . $img['image_name']),
                    'dimension_popup' => cseo_get_img_size(DIR_WS_POPUP_IMAGES . $img['image_name']),
                    'dimension_thumb' => cseo_get_img_size(DIR_WS_THUMBNAIL_IMAGES . $img['image_name']),
                    'dimension_info' => cseo_get_img_size(DIR_WS_INFO_IMAGES . $img['image_name']),
                    'path_mini' => DIR_WS_CATALOG . DIR_WS_MINI_IMAGES . $img['image_name'],
                    'path_thumb' => DIR_WS_CATALOG . DIR_WS_THUMBNAIL_IMAGES . $img['image_name'],
                    'path_info' => DIR_WS_CATALOG . DIR_WS_INFO_IMAGES . $img['image_name'],
                    'path_popup' => DIR_WS_CATALOG . DIR_WS_POPUP_IMAGES . $img['image_name'],
                    'path_org' => DIR_WS_CATALOG . DIR_WS_ORIGINAL_IMAGES . $img['image_name'],
                    'alt' => ($img['alt_langID_' . (int) $_SESSION['languages_id']] != '') ? $img['alt_langID_' . (int) $_SESSION['languages_id']] : strip_tags($product->data['products_name']));
            }
        }
        $info_smarty->assign('js_img', $js_img);
        $info_smarty->assign('js_title', $js_title);
        if (is_array($more_images)) {
            $info_smarty->assign('images', $more_images);
            $info_smarty->assign('more_image', 'true');
        }
    } else {
        $info_smarty->assign('NO_IMAGE', xtc_image(DIR_WS_INFO_IMAGES . 'no_img_big.jpg', strip_tags($product->data['products_name'])));
    }

    if (PRODUCT_DETAILS_TAB_REVIEWS == 'true' && $_SESSION['customers_status']['customers_status_read_reviews'] != 0) {
        include (DIR_WS_MODULES . 'product_reviews.php');
    }

    if (xtc_not_null($product->data['products_url']))
        $info_smarty->assign('PRODUCTS_URL', sprintf(TEXT_MORE_INFORMATION, xtc_href_link(FILENAME_REDIRECT, 'action=product&id=' . $product->data['products_id'], 'NONSSL', true, false)));

    if ($product->data['products_date_available'] > date('Y-m-d H:i:s')) {
        $info_smarty->assign('PRODUCTS_DATE_AVIABLE', sprintf(TEXT_DATE_AVAILABLE, xtc_date_long($product->data['products_date_available'])));
    } elseif ($product->data['products_date_added'] != '0000-00-00 00:00:00') {
        $info_smarty->assign('PRODUCTS_ADDED', sprintf(TEXT_DATE_ADDED, xtc_date_long($product->data['products_date_added'])));
    }

    if (PRODUCT_DETAILS_TAB_MEDIA == 'true') {
        include (DIR_WS_MODULES . FILENAME_PRODUCTS_MEDIA);
    }

    if (PRODUCT_DETAILS_TAB_ALSO_PURCHASED == 'true') {
        include (DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
    }

    if (PRODUCT_DETAILS_TAB_CROSS_SELLING == 'true') {
        include (DIR_WS_MODULES . FILENAME_CROSS_SELLING);
    }
    if (PRODUCT_DETAILS_RELATED_CAT == 'true') {
        include (DIR_WS_MODULES . FILENAME_PRODUCTS_RELATED_CAT);
    }

    if (PRODUCT_DETAILS_SOCIAL == 'true') {
        $info_smarty->assign('PRODUCTS_SOCIAL', 'true');
    }

    if ($product->data['product_template'] == '' or $product->data['product_template'] == 'default') {
        $files = array();
        if ($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_info/')) {
            while ($file = readdir($dir)) {
                if (is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_info/' . $file) and ($file != "index.html") and (substr($file, 0, 1) != "."))
                    $files[] = array('id' => $file, 'text' => $file);
            }
            closedir($dir);
        }
        $product->data['product_template'] = $files[0]['id'];
    }

    $i = count($_SESSION['tracking']['products_history']);
    if ($i > 6) {
        array_shift($_SESSION['tracking']['products_history']);
        $_SESSION['tracking']['products_history'][6] = $product->data['products_id'];
        $_SESSION['tracking']['products_history'] = array_unique($_SESSION['tracking']['products_history']);
    } else {
        $_SESSION['tracking']['products_history'][$i] = $product->data['products_id'];
        $_SESSION['tracking']['products_history'] = array_unique($_SESSION['tracking']['products_history']);
    }


    if (file_exists(DIR_WS_MODULES . 'product_ask_a_question.php') && PRODUCT_DETAILS_ASKQUESTION == 'true') {
        include (DIR_WS_MODULES . 'product_ask_a_question.php');
    }

    if (file_exists(DIR_WS_MODULES . 'product_parameters.php') && PRODUCT_DETAILS_TAB_PARAMETERS == 'true') {
        include (DIR_WS_MODULES . 'product_parameters.php');
    }

    if (file_exists(DIR_WS_MODULES . 'product_accessories.php') && PRODUCT_DETAILS_TAB_ACCESSORIES == 'true') {
        include (DIR_WS_MODULES . 'product_accessories.php');
    }

    if (file_exists(DIR_WS_MODULES . 'product_bundles.php') && MODULE_CSEO_PRODUCTS_BUNDLES == 'true') {
        if ($product->data['products_bundle'] == '1') {
            include(DIR_WS_MODULES . 'product_bundles.php');
        }
    }

    if (file_exists(DIR_WS_CLASSES . 'class.barcodeqr.php') && function_exists('curl_init') && PRODUCT_INFO_QR == 'true') {
        include_once(DIR_WS_CLASSES . 'class.barcodeqr.php');
        $qr = new BarcodeQR();
        $qr->url((($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . $_SERVER['REQUEST_URI']);
        $qr->draw(150, DIR_WS_IMAGES . 'qr_images/' . $product->data['products_id'] . '.jpg');
        $info_smarty->assign('PRODUCTS_QR', xtc_image(DIR_WS_IMAGES . 'qr_images/' . $product->data['products_id'] . '.jpg'));
    }

    if (file_exists(DIR_WS_MODULES . 'product_master_slave.php') && MASTER_SLAVE_FUNCTION == 'true') {
        include (DIR_WS_MODULES . 'product_master_slave.php');
    }

    if (file_exists(DIR_WS_MODULES . 'konfigurator.php')) {
        include (DIR_WS_MODULES . 'konfigurator.php');
    }

    $info_smarty->assign('PRODUCTS_QUANTITY', $product->data['products_quantity']);
    $info_smarty->assign('BASE_PATH', $_SERVER['REQUEST_URI']);
    $info_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);

    if (file_exists(DIR_WS_INCLUDES . 'addons/product_info_addon.php')) {
        include (DIR_WS_INCLUDES . 'addons/product_info_addon.php');
    }
    if (file_exists(DIR_WS_MODULES . 'commerzfinanz.php')) {
        include_once (DIR_WS_MODULES . 'commerzfinanz.php');
    }

	if ($_SESSION['attributeerror'] == 'true') {
	  $info_smarty->assign('error', ATTRIBUTE_ERROR);
	  unset($_SESSION['attributeerror']);
	}
    $cseo_productinfo_extender_component = cseohookfactory::create_object('ProductInfoExtenderComponent');
    $cseo_productinfo_extender_component->set_data('GET', $_GET);
    $cseo_productinfo_extender_component->set_data('POST', $_POST);
    $cseo_productinfo_extender_component->set_data('products_id', $product->data['products_id']);
    $cseo_productinfo_extender_component->set_data('product_template', $product->data['product_template']);
    $cseo_productinfo_extender_component->proceed();
    $cseo_extender_result_array = $cseo_productinfo_extender_component->get_response();
    if (is_array($cseo_extender_result_array)) {
        foreach ($cseo_extender_result_array AS $t_key => $t_value) {
            $info_smarty->assign($t_key, $t_value);
        }
    }
    $info_smarty->assign('language', $_SESSION['language']);
    // set cache ID
    if (!CacheCheck()) {
        $info_smarty->caching = false;
        $product_info = $info_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_info/' . $product->data['product_template'], USE_TEMPLATE_DEVMODE));
    } else {
        $info_smarty->caching = true;
        $info_smarty->cache_lifetime = CACHE_LIFETIME;
        $info_smarty->cache_modified_check = CACHE_CHECK;
        $cache_id = $product->data['products_id'] . $_SESSION['language'] . $_SESSION['customers_status']['customers_status_name'] . $_SESSION['currency'] . 'pinfo';
        $product_info = $info_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_info/' . $product->data['product_template'], USE_TEMPLATE_DEVMODE), $cache_id);
    }
}
$smarty->assign('main_content', $product_info);
