<?php

class classdefault_ORIGINAL {

    function classdefault_ORIGINAL() {
        
    }

    function category($current_category_id) {
		global $browser;
        if (GROUP_CHECK == 'true') {
            $group_check_c = "AND c.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = 1 "; // Kategorie
        }
        $category = xtc_db_fetch_array(xtDBquery("SELECT cd.*, c.*
							FROM " . TABLE_CATEGORIES . " c 
							JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON(cd.categories_id = '" . $current_category_id . "' AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "')
							WHERE c.categories_id = '" . $current_category_id . "'
							GROUP BY c.categories_id
							" . $group_check_c . ";"));

        if (isset($cPath) && preg_match('/_/', $cPath)) {
            $category_links = array_reverse($cPath_array);
            for ($i = 0, $n = sizeof($category_links); $i < $n; $i++) {
                $categories_query = xtDBquery("SELECT cd.*, c.*
									FROM " . TABLE_CATEGORIES . " c 
									JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON(c.categories_id = cd.categories_id AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "')
									WHERE c.categories_status = '1'
									AND c.parent_id = '" . $category_links[$i] . "'
									" . $group_check_c . "
									GROUP BY c.categories_id
									ORDER BY sort_order, cd.categories_name;");

                if (xtc_db_num_rows($categories_query) >= 1) {
                    break;
                }
            }
        } else {
            $categories_query = xtDBquery("SELECT cd.*, c.*
								FROM " . TABLE_CATEGORIES . " c 
								JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON(c.categories_id = cd.categories_id AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "')
								WHERE c.categories_status = '1'
								AND c.parent_id = '" . $current_category_id . "'
								" . $group_check_c . "
								GROUP BY c.categories_id
								ORDER BY sort_order, cd.categories_name;");
        }


        $rows = 0;
        //Unterkategorien anzeigen
        while ($categories = xtc_db_fetch_array($categories_query)) {
            $rows++;
            $cPath_new = xtc_category_link($categories['categories_id'], $categories['categories_name']);
            $image = '';
            if ($categories['categories_image'] != '') {
                $image = xtc_image(DIR_WS_IMAGES . 'categories/' . $categories['categories_image'], ($categories['categories_heading_title'] != '' ? $categories['categories_heading_title'] : $categories['categories_name']), ($categories['categories_pic_alt'] != '' ? $categories['categories_pic_alt'] : $categories['categories_name']));
                $image_org = xtc_image(DIR_WS_IMAGES . 'categories_org/' . $categories['categories_image'], ($categories['categories_heading_title'] != '' ? $categories['categories_heading_title'] : $categories['categories_name']), ($categories['categories_pic_alt'] != '' ? $categories['categories_pic_alt'] : $categories['categories_name']), 'img-responsive');
                if (!file_exists(DIR_WS_IMAGES . 'categories/' . $categories['categories_image'])) {
                    $image = xtc_image(DIR_WS_IMAGES . 'categories/noimage.gif', ($categories['categories_pic_alt'] != '' ? $categories['categories_pic_alt'] : $categories['categories_name']), ($categories['categories_heading_title'] != '' ? $categories['categories_heading_title'] : $categories['categories_name']));
                    $image_org = xtc_image(DIR_WS_IMAGES . 'categories_org/noimage.gif', ($categories['categories_pic_alt'] != '' ? $categories['categories_pic_alt'] : $categories['categories_name']), ($categories['categories_heading_title'] != '' ? $categories['categories_heading_title'] : $categories['categories_name']));
                }
            }

            if (DISPLAY_MORE_CAT_DESC == 'true') {
				if ($categories['categories_short_description'] == '') {
					$cat_desc = cseo_truncate(strip_tags(str_replace('"', '', $categories['categories_description'])), 70);
				} else {
					$cat_desc = $categories['categories_short_description'];
				}
            } else {
                $cat_desc = '';
            }
            if ($categories['categories_blogs'] != '0') {
                $mylink = xtc_href_link(FILENAME_BLOG, 'blog_cat=' . $categories['categories_blogs']);
            } elseif ($categories['categories_contents'] != '0') {
                $my_contlink = xtc_db_fetch_array(xtc_db_query("SELECT content_out_link FROM " . TABLE_CONTENT_MANAGER . " WHERE content_group = " . $categories['categories_contents'] . " "));
                if ($my_contlink['content_out_link'] != '') {
                    $mylink = xtc_href_link($my_contlink['content_out_link']);
                } else {
                    $mylink = xtc_href_link(FILENAME_CONTENT, 'coID=' . $categories['categories_contents']);
                }
            } else {
                $mylink = xtc_href_link(FILENAME_DEFAULT, $cPath_new);
            }
			if (MOBILE_CONF_CATEGORY_FOOTER == 'true' || $browser->getBrowser() != Browser::BROWSER_IPHONE) {
				$footertext = $categories['categories_description_footer'];
			} elseif (MOBILE_CONF_CATEGORY_FOOTER == 'false' || $browser->getBrowser() == Browser::BROWSER_IPHONE) {
				$footertext = '';
			}
            $categories_content[] = array(
			'categories_meta_keywords' => $categories['categories_meta_keywords'],
			'categories_meta_description' => $categories['categories_meta_description'],
			'categories_meta_title' => $categories['categories_meta_title'],
			'CATEGORIES_NAME' => $categories['categories_name'],
			'CATEGORIES_HEADING_TITLE' => $categories['categories_heading_title'],
			'CATEGORIES_IMAGE_OG' => DIR_WS_IMAGES . 'categories/' . $categories['categories_image'],
			'CATEGORIES_IMAGE' => $image,
			'CATEGORIES_IMAGE_ORG' => $image_org,
			'CATEGORIES_LINK' => $mylink,
			'CATEGORIES_DESCRIPTION' => $cat_desc,
			'CATEGORIES_DESCRIPTION_FOOTER' => $footertext);
        }


        $image = '';
        if ($category['categories_image'] != '') {
            $image = xtc_image(DIR_WS_IMAGES . 'categories_info/' . $category['categories_image'], ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']));
            $image_org = xtc_image(DIR_WS_IMAGES . 'categories_org/' . $category['categories_image'], ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']), 'img-responsive');
            if (!file_exists(DIR_WS_IMAGES . 'categories_info/' . $category['categories_image'])) {
                $image = xtc_image(DIR_WS_IMAGES . 'categories/noimage.gif', ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']), ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']));
                $image_org = xtc_image(DIR_WS_IMAGES . 'categories_org/noimage.gif', ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']), ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']));
            }
        }
        $image_footer = '';
        if ($category['categories_footer_image'] != '') {
            $image_footer = xtc_image(DIR_WS_IMAGES . 'categories_footer/' . $category['categories_footer_image'], ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), ($category['categories_pic_footer_alt'] != '' ? $category['categories_pic_footer_alt'] : $category['categories_name']), 'img-responsive');
            if (!file_exists(DIR_WS_IMAGES . 'categories_footer/' . $category['categories_footer_image'])) {
                $image_footer = xtc_image(DIR_WS_IMAGES . 'categories/noimage.gif', ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']), ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']));
            }
        }

        $this->v_data_array['module_content'] = $categories_content;
        $this->v_data_array['categories_meta_keywords'] = $category['categories_meta_keywords'];
        $this->v_data_array['categories_meta_description'] = $category['categories_meta_description'];
        $this->v_data_array['categories_meta_title'] = $category['categories_meta_title'];
        $this->v_data_array['CATEGORIES_HEADING_TITLE'] = $category['categories_heading_title'];
        $this->v_data_array['CATEGORIES_NAME'] = $category['categories_name'];
        $this->v_data_array['CATEGORIES_IMAGE'] = $image;
        $this->v_data_array['CATEGORIES_IMAGE_OG'] = DIR_WS_IMAGES . 'categories/' . $category['categories_image'];
        $this->v_data_array['CATEGORIES_IMAGE_ORG'] = $image_org;
        $this->v_data_array['CATEGORIES_FOOTER_IMAGE'] = $image_footer;
        $this->v_data_array['CATEGORIES_DESCRIPTION'] = $category['categories_description'];
        $this->v_data_array['CATEGORIES_SHORT_DESCRIPTION'] = $category['categories_short_description'];
        $this->v_data_array['CATEGORIES_DESCRIPTION_FOOTER'] = $category['categories_description_footer'];

        return $this->v_data_array;
    }

    function products($current_category_id, $new_products_category_id) {
        $fsk_lock = '';
        if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
            $fsk_lock = ' AND p.products_fsk18!=1';
        }
        if (GROUP_CHECK == 'true') {
            $group_check_c = "AND c.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = 1 "; // Kategorie
            $group_check_p = "AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = 1 "; // Produkt
        }

        if (isset($_GET['manufacturers_id'])) {
            $sorting_data = xtc_db_fetch_array(xtDBquery("SELECT products_sorting, products_sorting2 FROM " . TABLE_CATEGORIES . " WHERE categories_id='" . $new_products_category_id . "';"));
            if ($sorting_data['products_sorting'] == '') {
                $sorting_data['products_sorting'] = 'pd.products_name';
            }
            if (isset($_GET['filter_id']) && xtc_not_null($_GET['filter_id'])) {
                if ($_GET['multisort'] == 'specialprice' || $_GET['multisort'] == 'new_asc' || $_GET['multisort'] == 'new_desc' || $_GET['multisort'] == 'name_asc' || $_GET['multisort'] == 'name_desc' || $_GET['multisort'] == 'price_asc' || $_GET['multisort'] == 'price_desc' || $_GET['multisort'] == 'manu_asc' || $_GET['multisort'] == 'manu_desc') {
                    switch ($_GET['multisort']) {
                        case 'specialprice':
                            $sorting = ' GROUP BY p.products_id ORDER BY s.specials_new_products_price DESC';
                            $field = ' JOIN ' . TABLE_SPECIALS . ' s ON(p.products_id = s.products_id )';
                            break;
                        case 'new_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_date_added ASC';
                            break;
                        case 'new_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_date_added DESC';
                            break;
                        case 'name_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY pd.products_name ASC';
                            break;
                        case 'name_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY pd.products_name DESC';
                            break;
                        case 'price_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_price ASC';
                            break;
                        case 'price_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_price DESC';
                            break;
                        case 'manu_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY m.manufacturers_name ASC';
                            break;
                        case 'manu_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY m.manufacturers_name DESC';
                            break;
                        default:
                            $sorting = ' GROUP BY p.products_id ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
                    }
                } else {
                    $sorting = ' ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
                }

                // We are asked to show only a specific category
                $listing_sql = "SELECT p.*, m.*, pd.*
								FROM " . TABLE_PRODUCTS . " p
								JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON(p.products_id = p2c.products_id AND p2c.categories_id = '" . (int) $_GET['filter_id'] . "')
								JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON(pd.products_id = p2c.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "') 
								JOIN " . TABLE_MANUFACTURERS . " m ON(p.manufacturers_id = m.manufacturers_id AND m.manufacturers_id = '" . (int) $_GET['manufacturers_id'] . "')
								" . $field . "
								WHERE p.products_status = '1'
								AND (p.products_slave_in_list = '1' OR p.products_master = '1' OR ((p.products_slave_in_list = '0' OR p.products_slave_in_list = '') AND (p.products_master_article = '' OR p.products_master_article = '0')))
								" . $group_check_p . "
								" . $fsk_lock . "
								" . $sorting;
            } else {
                if ($_GET['multisort'] == 'specialprice' || $_GET['multisort'] == 'new_asc' || $_GET['multisort'] == 'new_desc' || $_GET['multisort'] == 'name_asc' || $_GET['multisort'] == 'name_desc' || $_GET['multisort'] == 'price_asc' || $_GET['multisort'] == 'price_desc' || $_GET['multisort'] == 'manu_asc' || $_GET['multisort'] == 'manu_desc') {
                    switch ($_GET['multisort']) {
                        case 'specialprice':
                            $sorting = ' GROUP BY p.products_id ORDER BY s.specials_new_products_price DESC';
                            $field = ' INNER JOIN ' . TABLE_SPECIALS . ' s ON(p.products_id = s.products_id)';
                            break;
                        case 'new_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_date_added ASC';
                            break;
                        case 'new_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_date_added DESC';
                            break;
                        case 'name_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY pd.products_name ASC';
                            break;
                        case 'name_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY pd.products_name DESC';
                            break;
                        case 'price_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_price ASC';
                            break;
                        case 'price_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_price DESC';
                            break;
                        case 'manu_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY m.manufacturers_name ASC';
                            break;
                        case 'manu_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY m.manufacturers_name DESC';
                            break;
                        default:
                            $sorting = ' GROUP BY p.products_id ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
                    }
                } else {
                    $sorting = ' ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
                }
                $listing_sql = "SELECT p.*, pd.*, m.*
								FROM " . TABLE_PRODUCTS . " p
								JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON(pd.products_id = p.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
								JOIN " . TABLE_MANUFACTURERS . " m ON(p.manufacturers_id = m.manufacturers_id AND m.manufacturers_id = '" . (int) $_GET['manufacturers_id'] . "')
								" . $field . "
								WHERE p.products_status = '1'
								AND (p.products_slave_in_list = '1' OR p.products_master = '1' OR ((p.products_slave_in_list = '0' OR p.products_slave_in_list = '') AND (p.products_master_article = '' OR p.products_master_article = '0')))" 
								. $group_check_p
								. $fsk_lock
								. $sorting;
            }
        } else {
            // Hersteller ist drin
            if (isset($_GET['filter_id']) && xtc_not_null($_GET['filter_id'])) {
                // sorting query
                $sorting_data = xtc_db_fetch_array(xtDBquery("SELECT products_sorting, products_sorting2 FROM " . TABLE_CATEGORIES . " WHERE categories_id='" . $current_category_id . "';"));

                if ($sorting_data['products_sorting'] == '')
                    $sorting_data['products_sorting'] = 'pd.products_name';
                $sorting = ' ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
                if ($_GET['multisort'] == 'specialprice' || $_GET['multisort'] == 'new_asc' || $_GET['multisort'] == 'new_desc' || $_GET['multisort'] == 'name_asc' || $_GET['multisort'] == 'name_desc' || $_GET['multisort'] == 'price_asc' || $_GET['multisort'] == 'price_desc' || $_GET['multisort'] == 'manu_asc' || $_GET['multisort'] == 'manu_desc') {
                    switch ($_GET['multisort']) {
                        case 'specialprice':
                            $field = ' INNER JOIN ' . TABLE_SPECIALS . ' s ON ( p.products_id = s.products_id )';
                            $sorting = ' GROUP BY p.products_id ORDER BY s.specials_new_products_price DESC';
                            break;
                        case 'new_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_date_added ASC';
                            break;
                        case 'new_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_date_added DESC';
                            break;
                        case 'name_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY pd.products_name ASC';
                            break;
                        case 'name_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY pd.products_name DESC';
                            break;
                        case 'price_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_price ASC';
                            break;
                        case 'price_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_price DESC';
                            break;
                        case 'manu_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY m.manufacturers_name ASC';
                            break;
                        case 'manu_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY m.manufacturers_name DESC';
                            break;
                        default:
                            $sorting = ' GROUP BY p.products_id ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
                    }
                } else {
                    $sorting = ' GROUP BY p.products_id ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
                }
                $listing_sql = "SELECT p.*, pd.*, m.*
								FROM " . TABLE_PRODUCTS . " p
								JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON(p.products_id = p2c.products_id AND p2c.categories_id = '" . $current_category_id . "')
								JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON(pd.products_id = p2c.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
								JOIN " . TABLE_MANUFACTURERS . " m ON(p.manufacturers_id = m.manufacturers_id AND m.manufacturers_id = '" . (int) $_GET['filter_id'] . "')
								" . $field . "
								WHERE p.products_status = '1'
								AND (p.products_slave_in_list = '1' OR p.products_master = '1' OR ((p.products_slave_in_list = '0' OR p.products_slave_in_list = '') AND (p.products_master_article = '' OR p.products_master_article = '0')))"
								. $group_check_p
								. $fsk_lock
								. $sorting;
            } else {
                //normale Kategorie
                $sorting_data = xtc_db_fetch_array(xtDBquery("SELECT products_sorting, products_sorting2 FROM " . TABLE_CATEGORIES . " where categories_id='" . $current_category_id . "';"));

                if (!$sorting_data['products_sorting'])
                    $sorting_data['products_sorting'] = 'pd.products_name';

                $sorting = ' ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
                if ($_GET['multisort'] == 'specialprice' || $_GET['multisort'] == 'new_asc' || $_GET['multisort'] == 'new_desc' || $_GET['multisort'] == 'name_asc' || $_GET['multisort'] == 'name_desc' || $_GET['multisort'] == 'price_asc' || $_GET['multisort'] == 'price_desc' || $_GET['multisort'] == 'manu_asc' || $_GET['multisort'] == 'manu_desc') {
                    switch ($_GET['multisort']) {
                        case 'specialprice':
                            $sorting = ' GROUP BY p.products_id ORDER BY s.specials_new_products_price DESC';
                            $field = ' JOIN ' . TABLE_SPECIALS . ' s ON ( p.products_id = s.products_id AND s.status = 1)';
                            break;
                        case 'new_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_date_added ASC';
                            break;
                        case 'new_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_date_added DESC';
                            break;
                        case 'name_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY pd.products_name ASC';
                            break;
                        case 'name_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY pd.products_name DESC';
                            break;
                        case 'price_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_price ASC';
                            break;
                        case 'price_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY p.products_price DESC';
                            break;
                        case 'manu_asc':
                            $sorting = ' GROUP BY p.products_id ORDER BY m.manufacturers_name ASC';
                            $field = ' JOIN ' . TABLE_MANUFACTURERS . ' m ON ( p.manufacturers_id = m.manufacturers_id )';
                            break;
                        case 'manu_desc':
                            $sorting = ' GROUP BY p.products_id ORDER BY m.manufacturers_name DESC';
                            $field = ' JOIN ' . TABLE_MANUFACTURERS . ' m ON ( p.manufacturers_id = m.manufacturers_id )';
                            break;
                        default:
                            $sorting = ' GROUP BY p.products_id ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
                    }
                } else {
                    $sorting = ' GROUP BY p.products_id ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
                }
                $listing_sql = "SELECT p.*, pd.products_name, pd.products_description, pd.products_short_description, pd.products_img_alt
								FROM " . TABLE_PRODUCTS . " AS p
								JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS p2c ON (p2c.categories_id = '" . $current_category_id . "' AND p.products_id = p2c.products_id)
								JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON (pd.products_id = p2c.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
								" . $field . "
								WHERE p.products_status = '1'
								AND (p.products_slave_in_list = '1' OR p.products_master = '1' OR ((p.products_slave_in_list = '0' OR p.products_slave_in_list = '') AND (p.products_master_article = '' OR p.products_master_article = '0')))
								" . $group_check_p . "
								" . $fsk_lock . "
								" . $sorting;
            }
        }

        return $listing_sql;
    }

    function multisort_dropdown($current_category_id) {
        if (PRODUCT_LIST_FILTER_SORT == 'true') {
            $multisort_dropdown = xtc_draw_form('multisort', $_SERVER['REQUEST_URI'], 'GET') . "\n";
            if ($_GET['page'] != '') {
                $multisort_dropdown .= xtc_draw_hidden_field('page', $_GET['page']);
            }
            if ($_GET['cPath'] != '' && MODULE_COMMERCE_SEO_INDEX_STATUS != 'True') {
                $multisort_dropdown .= xtc_draw_hidden_field('cPath', $_GET['cPath']);
            }
            if (isset($_GET['manufacturers_id'])) {
                $multisort_dropdown .= xtc_draw_hidden_field('manufacturers_id', $_GET['manufacturers_id']);
            }
            if ($_GET['result'] != '') {
                $manufacturer_dropdown .= xtc_draw_hidden_field('result', $_GET['result']);
            }
            if (isset($_GET['filter_id'])) {
                $multisort_dropdown .= xtc_draw_hidden_field('filter_id', (int) $_GET['filter_id']);
            }

            // Abfrage, ob Sonderangebote da sind
            $specials_query_raw = xtDBquery("SELECT s.products_id
											FROM " . TABLE_SPECIALS . " AS s
											JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS ptc ON(ptc.products_id = s.products_id AND ptc.categories_id = " . (int) $current_category_id . ")
											WHERE status = '1' GROUP BY s.products_id;");
            $count_specials = xtc_db_num_rows($specials_query_raw);
			// Abfrage, ob Hersteller da sind
			$count_manu = xtc_db_fetch_array(xtDBquery("SELECT COUNT(manufacturers_id) AS counter FROM " . TABLE_MANUFACTURERS . ";"));

            $options = array(array('text' => MULTISORT_STANDARD));

            if (($count_specials > 0)) {
                $options[] = array('id' => 'specialprice', 'text' => MULTISORT_SPECIALS_DESC);
            }
            $options[] = array('id' => 'new_desc', 'text' => MULTISORT_NEW_DESC);
            $options[] = array('id' => 'new_asc', 'text' => MULTISORT_NEW_ASC);
            $options[] = array('id' => 'price_asc', 'text' => MULTISORT_PRICE_ASC);
            $options[] = array('id' => 'price_desc', 'text' => MULTISORT_PRICE_DESC);
            $options[] = array('id' => 'name_asc', 'text' => MULTISORT_ABC_AZ);
            $options[] = array('id' => 'name_desc', 'text' => MULTISORT_ABC_ZA);
			if (($count_manu['counter'] > 0)) {
				$options[] = array('id' => 'manu_asc', 'text' => MULTISORT_MANUFACTURER_ASC);
				$options[] = array('id' => 'manu_desc', 'text' => MULTISORT_MANUFACTURER_DESC);
			}

            $multisort_dropdown .= xtc_draw_pull_down_menu('multisort', $options, $_GET['multisort'], 'onchange="javascript:this.form.submit();"') . "\n";
            $multisort_dropdown .= '</form>' . "\n";
        }
        return $multisort_dropdown;
    }

    function manufacturer_dropdown($current_category_id) {
        if (isset($_GET['manufacturers_id'])) {
            $filterlist_query = xtDBquery("SELECT c.categories_id AS id, cd.categories_name AS name
								FROM " . TABLE_PRODUCTS . " AS p
								JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS p2c ON(p.products_id = p2c.products_id)
								JOIN " . TABLE_CATEGORIES . " AS c ON(p2c.categories_id = c.categories_id AND c.categories_status = 1)
								JOIN " . TABLE_CATEGORIES_DESCRIPTION . " AS cd ON(p2c.categories_id = cd.categories_id AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "')
								WHERE p.products_status = '1'
								AND p.manufacturers_id = '" . (int) $_GET['manufacturers_id'] . "'
								GROUP BY c.categories_id
								ORDER BY cd.categories_name;");
        } else {
            $filterlist_query = xtDBquery("SELECT m.manufacturers_id AS id, m.manufacturers_name AS name
								FROM " . TABLE_PRODUCTS . " AS p
								JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS p2c ON(p.products_id = p2c.products_id AND p2c.categories_id = '" . $current_category_id . "')
								JOIN " . TABLE_MANUFACTURERS . " AS m ON(p.manufacturers_id = m.manufacturers_id)
								WHERE p.products_status = '1'
								GROUP BY m.manufacturers_id
								ORDER BY m.manufacturers_name;");
        }
        if (xtc_db_num_rows($filterlist_query) > 1) {
            $manufacturer_dropdown = xtc_draw_form('filter', $_SERVER['REQUEST_URI'], 'get');
            if (isset($_GET['manufacturers_id'])) {
                $manufacturer_dropdown .= xtc_draw_hidden_field('manufacturers_id', (int) $_GET['manufacturers_id']);
                $options = array(array('text' => TEXT_ALL_CATEGORIES));
            } else {
                $options = array(array('text' => TEXT_ALL_MANUFACTURERS));
            }
            if ($_GET['page'] != '') {
                $manufacturer_dropdown .= xtc_draw_hidden_field('page', $_GET['page']);
            }
            if ($_GET['cPath'] != '' && MODULE_COMMERCE_SEO_INDEX_STATUS != 'True') {
                $manufacturer_dropdown .= xtc_draw_hidden_field('cPath', $_GET['cPath']);
            }
            if ($_GET['multisort'] != '') {
                $manufacturer_dropdown .= xtc_draw_hidden_field('multisort', $_GET['multisort']);
            }
            if ($_GET['result'] != '') {
                $manufacturer_dropdown .= xtc_draw_hidden_field('result', $_GET['result']);
            }

            while ($filterlist = xtc_db_fetch_array($filterlist_query)) {
                $options[] = array('id' => $filterlist['id'], 'text' => $filterlist['name']);
            }

            $manufacturer_dropdown .= xtc_draw_pull_down_menu('filter_id', $options, $_GET['filter_id'], 'onchange="javascript:this.form.submit();"');
            $manufacturer_dropdown .= '</form>' . "\n";
        }
        return $manufacturer_dropdown;
    }

    function content() {
		global $browser;
        if (GROUP_CHECK == 'true') {
            $group_check = " AND group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
        }
        $shop_content_data = xtc_db_fetch_array(xtDBquery("SELECT * FROM " . TABLE_CONTENT_MANAGER . " WHERE content_group = '5' " . $group_check . " AND languages_id='" . (int) $_SESSION['languages_id'] . "' LIMIT 1;"));
        $this->v_data_array['title'] = $shop_content_data['content_heading'];
		
        if ($shop_content_data['content_file'] != '') {
            ob_start();
            if (strpos($shop_content_data['content_file'], '.txt')) {
                echo '<pre>';
				include (DIR_FS_CATALOG . 'media/content/' . $shop_content_data['content_file']);
                echo '</pre>';
			} else {
				include (DIR_FS_CATALOG . 'media/content/' . $shop_content_data['content_file']);
			}
            $shop_content_data['content_text'] = ob_get_contents();
            ob_end_clean();
        }
		
        $content_main = $shop_content_data['content_text'];
        $content_main = preg_replace('/##(\w+)/', '<a href="' . xtc_href_link('hashtag/\1') . '">#\1</a>', $content_main);
        $this->v_data_array['text'] = str_replace('{$greeting}', xtc_customer_greeting(), $content_main);

		if (MOBILE_CONF_START_FOOTER == 'false' && $browser->getBrowser() == Browser::BROWSER_IPHONE) {
			
		} elseif (MOBILE_CONF_START_FOOTER == 'true' || $browser->getBrowser() != Browser::BROWSER_IPHONE) {
			if (GROUP_CHECK == 'true') {
				$group_check = " AND group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
			}
			$shop_content_footer_data = xtc_db_fetch_array(xtDBquery("SELECT * FROM " . TABLE_CONTENT_MANAGER . " WHERE content_group = '15' " . $group_check . " AND languages_id='" . (int) $_SESSION['languages_id'] . "' LIMIT 1;"));
			if ($shop_content_footer_data['content_file'] != '') {
				ob_start();
				if (strpos($shop_content_footer_data['content_file'], '.txt')) {
					echo '<pre>';
					include (DIR_FS_CATALOG . 'media/content/' . $shop_content_footer_data['content_file']);
					echo '</pre>';
				} else {
					include (DIR_FS_CATALOG . 'media/content/' . $shop_content_footer_data['content_file']);
				}

				$shop_content_footer_data['content_text'] = ob_get_contents();
				ob_end_clean();
			}
			if ($shop_content_footer_data['content_heading'] != '') {
				$this->v_data_array['title_footer'] = $shop_content_footer_data['content_heading'];
			}
			$content_footer = $shop_content_footer_data['content_text'];
			$content_footer = preg_replace('/##(\w+)/', '<a href="' . xtc_href_link('hashtag/\1') . '">#\1</a>', $content_footer);
			$this->v_data_array['text_footer'] = $content_footer;
		}
        return $this->v_data_array;
    }

    function getSorting() {
        $row = xtc_db_fetch_array(xtc_db_query("SELECT configuration_value FROM configuration WHERE configuration_key='MAIN_BOX_ORDER' LIMIT 1;"));
		$sorting = explode('|', $row['configuration_value']);
        return $sorting;
    }
}
