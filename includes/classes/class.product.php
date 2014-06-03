<?php

/* -----------------------------------------------------------------
 * 	$Id: class.product.php 985 2014-04-17 14:53:53Z akausch $
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

class product_ORIGINAL {

    function product_ORIGINAL($pID = 0) {
        require_once(DIR_FS_INC . 'xtc_date_short.inc.php');
        $this->pID = $pID;
        $this->useStandardImage = true;
        $this->standardImage = 'no_img.jpg';
        if ($pID = 0) {
            $this->isProduct = false;
            return;
        }

        $product_query = xtDBquery("SELECT
											*
										FROM
											" . TABLE_PRODUCTS . " AS p 
										JOIN 
											" . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON (p.products_status = '1' AND pd.products_id = p.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
										WHERE
											p.products_id = '" . $this->pID . "'
										" . $this->groupCheck() . $this->fsk18() . "");

        if (!xtc_db_num_rows($product_query, true)) {
            $this->isProduct = false;
        } else {
            $this->isProduct = true;
            $this->data = xtc_db_fetch_array($product_query, true);
        }
    }

    function fsk18() {
        if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
            return $fsk_lock = ' AND p.products_fsk18!=1';
    }

    function groupCheck() {
        if (GROUP_CHECK == 'true')
            return " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
    }

    function getTagCloud() {
        $products_tags_query = xtDBquery("SELECT
												tag
											FROM
												tag_to_product
											WHERE
												pID='" . $this->pID . "'
											AND
												lID = '" . (int) $_SESSION['languages_id'] . "'
												ORDER BY tag ASC");
        if (xtc_db_num_rows($products_tags_query, true) > 0) {
            while ($products_tags = xtc_db_fetch_array($products_tags_query, true)) {
                $tags[] = array('tagcloud' => '<a class="product_info_tag" href="' . xtc_href_link('tag/' . urlencode($products_tags['tag'])) . '/">' . $products_tags['tag'] . '</a>');
            }
            return $tags;
        } else
            return;
    }

    function getAttributesCount() {
        if (ATTRIBUTE_STOCK_CHECK_DISPLAY == 'true') {
            $pastockcheck = ' AND attributes_stock > 0';
        }
        $products_attributes_query = xtDBquery("SELECT
													count(*) as total 
												FROM
													" . TABLE_PRODUCTS_ATTRIBUTES . " 
												WHERE 
													products_id='" . $this->pID . "'
												$pastockcheck;");

        $products_attributes = xtc_db_fetch_array($products_attributes_query, true);
        return $products_attributes['total'];
    }

    function getReviewsCount() {
        $reviews_query = xtDBquery("SELECT 
										count(*) as total 
									FROM 
										" . TABLE_REVIEWS . " r, 
										" . TABLE_REVIEWS_DESCRIPTION . " rd 
									WHERE 
										r.products_id = '" . $this->pID . "' 
									AND 
										r.reviews_status = '1' 
									AND 
										r.reviews_id = rd.reviews_id 
									AND 
										rd.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
									AND 
										rd.reviews_text !='';");
        $reviews = xtc_db_fetch_array($reviews_query, true);
        return $reviews['total'];
    }

    function getReviews() {
        $data_reviews = array();
        $reviews_query = xtDBquery("SELECT
	                                 r.*, 
	                                 rd.reviews_text
	                                 FROM 
										" . TABLE_REVIEWS . " r,
										" . TABLE_REVIEWS_DESCRIPTION . " rd
	                                 WHERE 
										r.products_id = '" . $this->pID . "'
	                                 AND 
										r.reviews_id = rd.reviews_id
									 AND 
										r.reviews_status = '1'
	                                 AND 
										rd.languages_id = '" . (int) $_SESSION['languages_id'] . "'
	                                 ORDER BY reviews_id DESC;");
        if (xtc_db_num_rows($reviews_query, true)) {
            $row = 0;
            $data_reviews = array();
            while ($reviews = xtc_db_fetch_array($reviews_query, true)) {
                $row++;
                $data_reviews[] = array('AUTHOR' => substr($reviews['customers_name'], 0, strrpos($reviews['customers_name'], " ") + 2) . '.',
                    'DATE' => xtc_date_short($reviews['date_added']),
                    'RATING' => xtc_image('templates/' . CURRENT_TEMPLATE . '/img/stars_' . $reviews['reviews_rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])),
                    'RATINGNUM' => $reviews['reviews_rating'],
                    'TEXT' => $reviews['reviews_text']);
                if ($row == PRODUCT_REVIEWS_VIEW)
                    break;
            }
        }
        return $data_reviews;
    }

    function getBreadcrumbModel() {
        if ($this->data['products_name'] != "")
            return $this->data['products_name'];
        return $this->data['products_model'];
    }

    function getAlsoPurchased() {
        global $xtPrice;

        $module_content = array();

        $orders_query = "SELECT 
							p.*, 
							pd.*
						FROM 
							" . TABLE_ORDERS_PRODUCTS . " op1 
						LEFT JOIN 
							" . TABLE_ORDERS_PRODUCTS . " op2 ON(op2.orders_id = op1.orders_id)
						LEFT JOIN 
							" . TABLE_PRODUCTS . " p ON(p.products_id = op2.products_id)
						LEFT JOIN 
							" . TABLE_PRODUCTS_DESCRIPTION . " pd ON(pd.products_id = op2.products_id)
						WHERE 
							op1.products_id = '" . $this->pID . "'
						AND 
							op2.products_id != '" . $this->pID . "'
						AND 
							p.products_status = '1'
						AND 
							pd.language_id = '" . (int) $_SESSION['languages_id'] . "'
						" . $this->groupCheck() . $this->fsk18() . "
						GROUP BY p.products_id 
						LIMIT " . MAX_DISPLAY_ALSO_PURCHASED;

        $orders_query = xtDBquery($orders_query);
        $row = 0;
        while ($orders = xtc_db_fetch_array($orders_query, true)) {
            $row++;
            $module_content[] = $this->buildDataArray($orders, 'thumbnail', 'also_purchased', $row);
        }
        return $module_content;
    }

    function getCrossSells() {
        $cs_groups = "SELECT products_xsell_grp_name_id FROM " . TABLE_PRODUCTS_XSELL . " WHERE products_id = '" . $this->pID . "' GROUP BY products_xsell_grp_name_id";
        $cs_groups = xtDBquery($cs_groups);
        $cross_sell_data = array();
        if (xtc_db_num_rows($cs_groups, true) > 0) {
            while ($cross_sells = xtc_db_fetch_array($cs_groups, true)) {
                $cross_query = "SELECT 
								p.*,
								pd.*,
								xp.sort_order 
							FROM 
								" . TABLE_PRODUCTS_XSELL . " xp 
							LEFT JOIN
								" . TABLE_PRODUCTS . " p ON(xp.xsell_id = p.products_id)
							LEFT JOIN	
								" . TABLE_PRODUCTS_DESCRIPTION . " pd ON(p.products_id = pd.products_id)
							WHERE 
								xp.products_id = '" . $this->pID . "'
							AND 
								xp.products_xsell_grp_name_id='" . $cross_sells['products_xsell_grp_name_id'] . "'
							AND 
								pd.language_id = '" . (int) $_SESSION['languages_id'] . "'
							AND 
								p.products_status = '1'
							" . $this->groupCheck() . $this->fsk18() . "
							ORDER BY xp.sort_order ASC;";

                $cross_query = xtDBquery($cross_query);
                if (xtc_db_num_rows($cross_query, true) > 0) {
                    require_once (DIR_FS_INC . 'get_cross_sell_name.inc.php');
                    $cross_sell_data[$cross_sells['products_xsell_grp_name_id']] = array('GROUP' => xtc_get_cross_sell_name($cross_sells['products_xsell_grp_name_id']), 'PRODUCTS' => array());
                }
                $row = 0;
                while ($xsell = xtc_db_fetch_array($cross_query, true)) {
                    $row++;
                    $cross_sell_data[$cross_sells['products_xsell_grp_name_id']]['PRODUCTS'][] = $this->buildDataArray($xsell, 'thumbnail', 'cross_selling', $row);
                }
            }
            return $cross_sell_data;
        }
    }

    function getReverseCrossSells() {
        $cross_query = xtDBquery("SELECT
									p.*,
									pd.*,
									xp.sort_order
								FROM
									" . TABLE_PRODUCTS_XSELL . " xp
								LEFT JOIN
									" . TABLE_PRODUCTS . " p ON(xp.products_id = p.products_id)
								LEFT JOIN
									" . TABLE_PRODUCTS_DESCRIPTION . " pd ON(p.products_id = pd.products_id)
								WHERE
									xp.xsell_id = '" . $this->pID . "'
								AND
									pd.language_id = '" . (int) $_SESSION['languages_id'] . "'
								AND
									p.products_status = '1'
								" . $this->groupCheck() . $this->fsk18() . "
								ORDER BY
									xp.sort_order ASC;");
        $i = 0;
        while ($xsell = xtc_db_fetch_array($cross_query, true)) {
            $i++;
            $cross_sell_data[] = $this->buildDataArray($xsell, 'thumbnail', 'reverse_cross_selling', $i);
        }
        return $cross_sell_data;
    }

    function getGraduated() {
        global $xtPrice;

        if ($_SESSION['customers_status']['customers_status_id'] == '0')
            $gruppe = '1';
        else
            $gruppe = (int) $_SESSION['customers_status']['customers_status_id'];

        $staffel_query = xtDBquery("SELECT * FROM " . TABLE_PERSONAL_OFFERS_BY . $gruppe . " WHERE products_id = '" . $this->pID . "'  AND personal_offer > 0 ORDER BY quantity ASC;");
        $discount = $xtPrice->xtcCheckDiscount($this->pID);
        $staffel = array();
        while ($staffel_values = xtc_db_fetch_array($staffel_query, true))
            $staffel[] = array('stk' => $staffel_values['quantity'], 'price' => $staffel_values['personal_offer']);

        $staffel_data = array();
        for ($i = 0, $n = sizeof($staffel); $i < $n; $i++) {
            if ($staffel[$i]['stk'] == 1) {
                $quantity = $staffel[$i]['stk'];
                if ($staffel[$i + 1]['stk'] != '')
                    $quantity = $staffel[$i]['stk'] . '-' . ($staffel[$i + 1]['stk'] - 1);
            } else {
                $quantity = ' > ' . $staffel[$i]['stk'];
                if ($staffel[$i + 1]['stk'] != '')
                    $quantity = $staffel[$i]['stk'] . '-' . ($staffel[$i + 1]['stk'] - 1);
            }
            $vpe = '';
            if ($this->data['products_vpe_status'] == 1 && $this->data['products_vpe_value'] != 0.0 && $staffel[$i]['price'] > 0) {
                $vpe = $staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount;
                $vpe = $vpe * (1 / $this->data['products_vpe_value']);
                $vpe = $xtPrice->xtcFormat($vpe, true, $this->data['products_tax_class_id']) . TXT_PER . xtc_get_vpe_name($this->data['products_vpe']);
            }
            $staffel_data[$i] = array('QUANTITY' => $quantity,
                'VPE' => $vpe,
                'PRICE' => $xtPrice->xtcFormat($staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount, true, $this->data['products_tax_class_id']));
        }
        return $staffel_data;
    }

    function isProduct() {
        return $this->isProduct;
    }

    function getBuyNowButton($id, $name) {
        global $PHP_SELF, $current_category_id;
        if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
            if (PRODUCT_ID > 0) {
                return '<a title="' . TEXT_BUY . TEXT_NOW . '" rel ="nofollow" href="' . xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $this->data['products_id'] . '&action=buy_now&BUYproducts_id=' . $id . '&' . xtc_get_all_get_params(array('action')), 'NONSSL') . '">' . cseo_wk_image_button('button_buy_now.gif', ((CSS_BUTTON_ACTIVE == 'true' || CSS_BUTTON_ACTIVE == 'css') ? TEXT_BUTTON_BUY_NOW : TEXT_BUY . $name . TEXT_NOW)) . '</a>';
            } else {
                return '<a title="' . TEXT_BUY . TEXT_NOW . '" rel ="nofollow" href="' . xtc_href_link(FILENAME_DEFAULT, 'cPath=' . $current_category_id . '&action=buy_now&BUYproducts_id=' . $id . '&' . xtc_get_all_get_params(array('action', 'cat', 'cPath')), 'NONSSL') . '">' . cseo_wk_image_button('button_buy_now.gif', ((CSS_BUTTON_ACTIVE == 'true' || CSS_BUTTON_ACTIVE == 'css') ? TEXT_BUTTON_BUY_NOW : TEXT_BUY . $name . TEXT_NOW)) . '</a>';
            }
        } else {
            return '<a title="' . TEXT_BUY . TEXT_NOW . '" rel ="nofollow" href="' . xtc_href_link(basename($_SERVER['SCRIPT_NAME']), 'action=buy_now&BUYproducts_id=' . $id . '&' . xtc_get_all_get_params(array('action')), 'NONSSL') . '">' . cseo_wk_image_button('button_buy_now.gif', ((CSS_BUTTON_ACTIVE == 'true' || CSS_BUTTON_ACTIVE == 'css') ? TEXT_BUTTON_BUY_NOW : TEXT_BUY . $name . TEXT_NOW)) . '</a>';
        }
    }

    function getWishlistButton($id, $name) {
        global $PHP_SELF, $current_category_id;
        if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
            if (PRODUCT_ID > 0) {
                return '<a title="' . $name . TEXT_NOW_TO_WISHLIST . '" rel ="nofollow" href="' . xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $this->data['products_id'] . 'cPath=' . $current_category_id . '&action=wishlist&products_id=' . $id . '&' . xtc_get_all_get_params(array('action')), 'NONSSL') . '">' . xtc_image_button('button_to_wish_list.gif', ((CSS_BUTTON_ACTIVE == 'true' || CSS_BUTTON_ACTIVE == 'css') ? TEXT_TO_WISHLIST : $name . TEXT_NOW_TO_WISHLIST)) . '</a>';
            } else {
                return '<a title="' . $name . TEXT_NOW_TO_WISHLIST . '" rel ="nofollow" href="' . xtc_href_link(FILENAME_DEFAULT, 'cPath=' . $current_category_id . '&action=wishlist&products_id=' . $id . '&' . xtc_get_all_get_params(array('action')), 'NONSSL') . '">' . xtc_image_button('button_to_wish_list.gif', ((CSS_BUTTON_ACTIVE == 'true' || CSS_BUTTON_ACTIVE == 'css') ? TEXT_TO_WISHLIST : $name . TEXT_NOW_TO_WISHLIST)) . '</a>';
            }
        } else {
            return '<a title="' . $name . TEXT_NOW_TO_WISHLIST . '" rel ="nofollow" href="' . xtc_href_link(basename($_SERVER['SCRIPT_NAME']), 'action=wishlist&products_id=' . $id . '&' . xtc_get_all_get_params(array('action')), 'NONSSL') . '">' . xtc_image_button('button_to_wish_list.gif', ((CSS_BUTTON_ACTIVE == 'true' || CSS_BUTTON_ACTIVE == 'css') ? TEXT_TO_WISHLIST : $name . TEXT_NOW_TO_WISHLIST)) . '</a>';
        }
    }

    function getVPEtext($product, $price) {
        global $xtPrice;

        require_once (DIR_FS_INC . 'xtc_get_vpe_name.inc.php');

        if (!is_array($product))
            $product = $this->data;

        if ($product['products_vpe_status'] == 1 && $product['products_vpe_value'] != 0.0 && $price > 0) {
            return $xtPrice->xtcFormat($price * (1 / $product['products_vpe_value']), true) . TXT_PER . xtc_get_vpe_name($product['products_vpe']);
        }
        return;
    }

    function getAccessoriesCount() {
        $products_accessories_query = xtDBquery("SELECT 
													COUNT(ap.id) AS total 
												FROM 
													" . TABLE_ACCESSORIES_PRODUCTS . " ap
												LEFT JOIN
													" . TABLE_ACCESSORIES . " a ON(a.id = ap.accessories_id)
												WHERE 
													a.head_product_id = '" . $this->pID . "'");

        $products_accessories = xtc_db_fetch_array($products_accessories_query, true);

        return $products_accessories['total'];
    }

    function getReviewsImg($pID) {
        $reviews_fetch = xtDBquery("SELECT 
										r.*, 
										LEFT(rd.reviews_text, 200) AS reviews_text
									FROM 
										" . TABLE_REVIEWS . " AS r 
									INNER JOIN
										" . TABLE_REVIEWS_DESCRIPTION . " AS rd ON(rd.reviews_id = r.reviews_id AND rd.languages_id = '" . (int) $_SESSION['languages_id'] . "')
									INNER JOIN
										" . TABLE_PRODUCTS . " AS p ON(p.products_id = '" . $pID . "' AND p.products_status = '1') 
									INNER JOIN
										" . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON(pd.products_id = '" . $pID . "' AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
									WHERE 
										r.products_id = '" . $pID . "' 
									ORDER BY r.reviews_id DESC LIMIT 5");


        while ($reviews = xtc_db_fetch_array($reviews_fetch)) {
            $reviews_text_alt = htmlspecialchars($reviews['reviews_text']);
        }
        $reviews_average = xtc_db_fetch_array(xtDBquery("SELECT (avg(reviews_rating) / 5 * 100) AS average_rating FROM " . TABLE_REVIEWS . " WHERE products_id = '" . $pID . "';"));
        $reviews_query = xtDBquery("SELECT 
										reviews_id,
										COUNT(reviews_rating) AS anzahl, 
										reviews_rating 
									FROM 
										" . TABLE_REVIEWS . " 
									WHERE 
										products_id = '" . $pID . "' 
									AND 
										reviews_status = '1' 
									GROUP BY products_id ORDER BY reviews_rating DESC;");

        if (xtc_db_num_rows($reviews_query) > 0) {
            $reviews = xtc_db_fetch_array($reviews_query);
            $reviews_img .= '<span class="fs85 product_listing_review_count" align="center">
						(<a href="' . xtc_href_link(FILENAME_REVIEWS, 'show_pid=' . $pID) . '"';

            if ($_SESSION['customers_status']['customers_status_read_reviews'] != 0) {
                $reviews_img .= ' title="' . $reviews_text_alt . '"';
            }
            $reviews_img .= '>' . $reviews['anzahl'] . '</a>)';
            $reviews_img .= xtc_image('templates/' . CURRENT_TEMPLATE . '/img/stars_' . $reviews['reviews_rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating']));
            $reviews_img .= ' &Oslash; ' . number_format($reviews_average['average_rating'], 0) . '%</span>';
        }
        return $reviews_img;
    }

    function buildDataArrayAfter($product_array_after) {
        $product_array_after = array();
        return $product_array_after;
    }

    function buildDataArray(&$array, $image = 'thumbnail', $site = '', $count = '1') {
        global $xtPrice, $main;
        if ($_SESSION['site'] != '') {
            $site = $_SESSION['site'];
        }

        $options = xtc_db_fetch_array(xtDBquery("SELECT * FROM products_listings WHERE list_name = '" . $site . "';"));

        switch ($options['col']) {
            case 2:
                $col_width = 'w474p';
                break;
            case 3:
                $col_width = 'w303p';
                break;
            case 4:
                $col_width = 'w223p';
                break;
            default:
                $col_width = 'w98p';
                break;
        }

        $tax_rate = $xtPrice->TAX[$array['products_tax_class_id']];

        if ((int) $options['p_price'] == 1) {
            $products_price = $xtPrice->xtcGetPrice($array['products_id'], true, 1, $array['products_tax_class_id'], $array['products_price'], 1, $options['list_type']);
            $price = $products_price['formated'];
        }

        if ($_SESSION['customers_status']['customers_status_show_price'] != '0' && $array['products_master'] == 0) {
            if ($_SESSION['customers_status']['customers_fsk18'] == '1') {
                if ($array['products_fsk18'] == '0') {
                    $buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);
                    $wishlist = $this->getWishlistButton($array['products_id'], $array['products_name']);
                }
            } else {
                $buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);
                $wishlist = $this->getWishlistButton($array['products_id'], $array['products_name']);
            }
        }

        $shipping_status_name = $main->getShippingStatusName($array['products_shippingtime']);
        $shipping_status_image = $main->getShippingStatusImage($array['products_shippingtime']);
        $shipping_status_image = xtc_image($shipping_status_image, $shipping_status_name);

        if ($options['p_manu_img'] == 1 || $options['p_manu_name'] == 1) {
            $manufacturer_query = xtDBquery("SELECT
													*
												FROM
													" . TABLE_MANUFACTURERS . " m
												INNER JOIN
													" . TABLE_MANUFACTURERS_INFO . " mi ON(m.manufacturers_id = mi.manufacturers_id AND mi.languages_id = '" . (int) $_SESSION['languages_id'] . "')
												WHERE 
													m.manufacturers_id = '" . $array['manufacturers_id'] . "';");

            $manufacturer = xtc_db_fetch_array($manufacturer_query);
        }

        if ($options['p_manu_img'] == 1) {
            if (!empty($manufacturer['manufacturers_image']))
                $manufacturer_image = xtc_image(DIR_WS_IMAGES . $manufacturer['manufacturers_image'], $manufacturer['manufacturers_name'], trim(cseo_truncate(strip_tags($manufacturer['manufacturers_description']), 35)), 'img-responsive');
        }
        if ($options['p_manu_name'] == 1) {

            if (!empty($manufacturer['manufacturers_url'])) {
                $manufacturer_url = xtc_href_link(FILENAME_REDIRECT, 'action=manufacturer&' . xtc_manufacturer_link($manufacturer['manufacturers_id'], $manufacturer['manufacturers_name']));
            }
            if (!empty($manufacturer['manufacturers_name'])) {
                $manufacturer_name = $manufacturer['manufacturers_name'];
                $manufacturers_description = $manufacturer['manufacturers_description'];
            }
        }

        if ($options['p_reviews'] == 1) {
            $reviews_img = $this->getReviewsImg($array['products_id']);
        }

        if (($options['p_vpe'] == 1)) {
            $products_price = $xtPrice->xtcGetPrice($array['products_id'], false, 0, $array['products_tax_class_id'], $array['products_price']);
            $vpe = $this->getVPEtext($array, $products_price);
        }

        if (($options['p_model'] == 1)) {
            $model = $array['products_model'];
        }

        if ($options['p_stockimg'] == 1) {
            require_once (DIR_FS_INC . 'cseo_get_stock_img.inc.php');
            $stock_img = cseo_get_stock_img($array['products_quantity']);
        }

        if ($options['b_details'] == 1) {
            $button_details = '<a title="' . $array['products_name'] . '" href="' . xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['products_name'])) . '">' . xtc_image_button('button_details.gif', IMAGE_BUTTON_DETAILS) . '</a>';
        }

        require_once (DIR_FS_INC . 'cseo_truncate.inc.php');

        if ($options['p_short_desc'] == 1 && $array['products_short_description'] != '') {
            $description = cseo_truncate($array['products_short_description'], $options['p_short_desc_lenght']);
        } elseif ($options['p_short_desc'] == 1 && $array['products_short_description'] == '' && $array['products_description'] != '') {
            $description = cseo_truncate($array['products_description'], $options['p_short_desc_lenght']);
        } elseif ($options['p_short_desc'] == 1 && $array['products_short_description'] == '' && $array['products_description'] == '') {
            $description = cseo_truncate($array['products_name'], $options['p_short_desc_lenght']);
        } elseif ($options['p_long_desc'] == 1) {
            $description = cseo_truncate($array['products_description'], $options['p_long_desc_lenght']);
        } else {
            $description = '';
        }
        $description = preg_replace('/##(\w+)/', '<a href="' . xtc_href_link('hashtag/\1') . '">#\1</a>', $description);

        #Bilder und Links Title mit Mehrwert
        if ($array['products_short_description'] != '' && $array['products_img_alt'] == '') {
            $description_img_alt = cseo_truncate(strip_tags(str_replace('"', '', $array['products_short_description'])), 35);
        } elseif ($array['products_description'] != '' && $array['products_img_alt'] == '') {
            $description_img_alt = cseo_truncate(strip_tags(str_replace('"', '', $array['products_description'])), 35);
        } elseif ($array['products_img_alt'] != '') {
            $description_img_alt = trim(str_replace('"', '', $array['products_img_alt']));
        } else {
            $description_img_alt = trim(str_replace('"', '', $array['products_name']));
        }
        $description_title_alt = cseo_truncate(strip_tags($array['products_description']), 35);

        if ($options['p_img'] == 1) {
            $img = $this->productImage($array['products_image'], $image);
            $img = xtc_image($img, $description_img_alt, str_replace('"', '', $array['products_name']));
        } else {
            $img = '';
        }

        if (MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0') {
            $date_new_products = date("Y.m.d", mktime(1, 1, 1, date(m), date(d) - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date(Y)));
            $days = " AND products_date_added > '" . $date_new_products . "' ";
        }

        $disp_new_prod = xtc_db_fetch_array(xtDBquery("SELECT products_id, products_date_added FROM " . TABLE_PRODUCTS . " WHERE products_status = 1 AND products_id ='" . $array['products_id'] . "' " . $days . " LIMIT " . MAX_RANDOM_SELECT_NEW));

        if ($disp_new_prod['products_date_added'] != '') {
            $prod_is_new = 'true';
        } else {
            $prod_is_new = 'false';
        }

        $disp_top_prod = xtc_db_fetch_array(xtDBquery("SELECT specials_id, products_id FROM " . TABLE_SPECIALS . " WHERE status = '1' AND products_id ='" . $array['products_id'] . "';"));

        if ($disp_top_prod['specials_id'] != '') {
            $prod_is_top = 'true';
        } else {
            $prod_is_top = 'false';
        }

        if ($array['products_minorder'] > 1) {
            $order_qty = $array['products_minorder'];
        } else {
            $order_qty = '1';
        }

        //Staffelpreise Listing
        if ($options['p_staffel'] == 1) {
            $getGraduatedStaffel = new getGraduatedStaffel();
            $products_staffel_price = $getGraduatedStaffel->getGraduatedStaffel($array['products_id']);
        }

        //Attribute Listing
        if ($options['p_attribute'] == 1) {
            $GetAttributesListing = new GetAttributesListing();
            $products_attributes_listing = $GetAttributesListing->GetAttributesListing($array['products_id'], $array['products_tax_class_id'], $array['products_price']);
        }

        if ($options['b_order'] == 1 && $options['p_buy'] == 0) {
            $buy_now_button = $buy_now;
            $buy_listing = 'false';
        } elseif ($options['b_order'] == 1 && $options['p_buy'] == 1) {
            $buy_now_button = cseo_wk_image_submit('button_in_cart.gif', TEXT_BUTTON_BUY_NOW);
            $buy_listing = 'true';
        } else {
            $buy_now_button = '';
            $buy_listing = 'false';
        }

        if (PRODUCT_ID == '') {
            $form_url = xtc_draw_form('cart_quantity' . $array['products_id'], xtc_href_link(FILENAME_DEFAULT, xtc_get_all_get_params(array('action')) . 'action=add_product_listing'));
        } else {
            $form_url = xtc_draw_form('cart_quantity' . $array['products_id'], xtc_href_link(FILENAME_PRODUCT_INFO, xtc_get_all_get_params(array('action')) . 'action=add_product_listing'));
        }

        //UVP Preis
        $price_uvp = '';
        if ($array['products_uvpprice'] > 0) {
            $price_uvp = $xtPrice->xtcAddTax($array['products_uvpprice'] * 1, $xtPrice->TAX[$array['products_tax_class_id']]);
            $price_uvp = $xtPrice->xtcFormat($price_uvp, true);
        }

        $products_array = array(
            'PRODUCTS_NAME' => (($options['p_name'] == 1) ? $array['products_name'] : ''),
            'COUNT' => $count,
            'COL_WIDTH' => $col_width,
            'PRODUCTS_ID' => $array['products_id'],
            'PRODUCTS_VPE' => $vpe,
            'PRODUCTS_IMAGE' => $img,
            'PRODUCTS_IMAGE_ALT' => $description_img_alt,
            'PRODUCTS_NAME_ALT' => $description_title_alt,
            'PRODUCTS_LINK' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['products_name'])),
            'PRODUCTS_PRICE' => (($array['products_buyable'] == 1) ? $price : ''),
            'PRODUCTS_UVP_PRICE' => $price_uvp,
            'PRODUCTS_MANUFACTURER_IMG' => $manufacturer_image,
            'PRODUCTS_MANUFACTURER_URL' => $manufacturer_url,
            'PRODUCTS_MANUFACTURER_NAME' => $manufacturer_name,
            'PRODUCTS_MANUFACTURERS_DESCRIPTION' => $manufacturers_description,
            'PRODUCTS_MODEL' => $model,
            'PRODUCTS_WEIGHT' => (($options['p_weight'] == 1) ? $array['products_weight'] : ''),
            'PRODUCTS_REVIEWS' => $reviews_img,
            'PRODUCTS_STOCK_IMG' => $stock_img,
            'PRODUCTS_TAX_INFO' => (DISPLAY_TAX == 'false' ? '' : $main->getTaxInfo($tax_rate)),
            'PRODUCTS_SHIPPING_LINK' => $main->getShippingLink(),
            'PRODUCTS_BUTTON_BUY_NOW' => (($options['b_order'] == 1 && $array['products_buyable'] == 1) ? $buy_now_button : ''),
            'PRODUCTS_BUTTON_WISHLIST' => (($options['b_wishlist'] == 1) ? $wishlist : ''),
            'PRODUCTS_BUTTON_DETAILS' => $button_details,
            'PRODUCTS_SHIPPING_NAME' => $shipping_status_name,
            'PRODUCTS_SHIPPING_IMAGE' => $shipping_status_image,
            'PRODUCTS_EXPIRES' => $array['expires_date'],
            'PRODUCTS_DATE' => xtc_date_short($array['products_date_available']),
            'PRODUCTS_CATEGORY_URL' => $array['cat_url'],
            'TEXT_UVP' => TEXT_UVP,
            'PRODUCTS_DESCRIPTION' => $description,
            'FORM_ACTION' => $form_url,
            'BUY_IN_LISTING' => $buy_listing,
            'QTY_VALUE' => $order_qty,
            'QTY_ID' => 'attr_calc_qty_' . $array['products_id'],
            'PRODUCTS_STAFFEL' => $products_staffel_price,
            'PRODUCTS_ATTRIBUTES' => $products_attributes_listing,
            'PRODUCTS_ATTRIBUTES_TEMPLATE' => PRODUCT_LISTING_ATTRIBUT_TEMPLATE,
            'PRODUCTS_NEW' => $prod_is_new,
            'PRODUCTS_TOP' => $prod_is_top,
            'PRODUCTS_FSK18' => $array['products_fsk18']);

        return $products_array;
    }

    function productImage($name, $type) {

        switch ($type) {
            case 'info' :
                $path = DIR_WS_INFO_IMAGES;
                break;
            case 'thumbnail' :
                $path = DIR_WS_THUMBNAIL_IMAGES;
                break;
            case 'popup' :
                $path = DIR_WS_POPUP_IMAGES;
                break;
            case 'mini' :
                $path = DIR_WS_MINI_IMAGES;
                break;
        }

        if ($name == '') {
            if ($this->useStandardImage == 'true' && $this->standardImage != '')
                return $path . $this->standardImage;
        } else {
            // check if image exists
            if (!file_exists($path . $name)) {
                if ($this->useStandardImage == 'true' && $this->standardImage != '')
                    $name = $this->standardImage;
            }
            return $path . $name;
        }
    }

}
