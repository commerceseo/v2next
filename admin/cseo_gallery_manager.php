<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_gallery_manager.php 1060 2015-12-11 17:57:15Z akausch $
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

require_once('includes/application_top.php');

$smarty = new Smarty;
$orderlistingnum = ADMIN_DEFAULT_LISTING_NUM;

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'setflag':
            $slider_id = xtc_db_prepare_input($_GET['bID']);
            if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
                xtc_db_query("UPDATE " . TABLE_SLIDER_GALLERY . " SET status = '" . (int) $_GET['flag'] . "', date_status_change = NOW() WHERE slider_id = '" . (int) $slider_id . "'");
                $messageStack->add_session(SUCCESS_BANNER_STATUS_UPDATED, 'success');
            } else {
                $messageStack->add_session(ERROR_UNKNOWN_STATUS_FLAG, 'error');
            }
            xtc_redirect(xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . (int) $_GET['bID']));
            break;
        case 'slider_mobile':
            $slider_id = xtc_db_prepare_input($_GET['bID']);
            if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
                xtc_db_query("UPDATE " . TABLE_SLIDER_GALLERY . " SET slider_mobile = '" . (int) $_GET['flag'] . "', date_status_change = NOW() WHERE slider_id = '" . (int) $slider_id . "'");
                $messageStack->add_session(SUCCESS_BANNER_STATUS_UPDATED, 'success');
            } else {
                $messageStack->add_session(ERROR_UNKNOWN_STATUS_FLAG, 'error');
            }
            xtc_redirect(xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . (int) $_GET['bID']));
            break;
        case 'slider_nav_status':
            $slider_id = xtc_db_prepare_input($_GET['bID']);
            if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
                xtc_db_query("UPDATE " . TABLE_SLIDER_GALLERY . " SET slider_nav_status = '" . (int) $_GET['flag'] . "', date_status_change = NOW() WHERE slider_id = '" . (int) $slider_id . "'");
                $messageStack->add_session(SUCCESS_BANNER_STATUS_UPDATED, 'success');
            } else {
                $messageStack->add_session(ERROR_UNKNOWN_STATUS_FLAG, 'error');
            }
            xtc_redirect(xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . (int) $_GET['bID']));
            break;
        case 'insert':
        case 'update':
            $slider_id = xtc_db_prepare_input($_POST['slider_id']);
            $slider_mobile = xtc_db_prepare_input($_POST['slider_mobile']);
            $slider_nav_status = xtc_db_prepare_input($_POST['slider_nav_status']);
            $slider_title = xtc_db_prepare_input($_POST['slider_title']);
            $slider_url = xtc_db_prepare_input($_POST['slider_url']);
            $slider_alt_text = xtc_db_prepare_input($_POST['slider_alt_text']);
            $slider_title_text = xtc_db_prepare_input($_POST['slider_title_text']);
            $slider_url_2 = xtc_db_prepare_input($_POST['slider_url_2']);
            $slider_alt_text_2 = xtc_db_prepare_input($_POST['slider_alt_text_2']);
            $slider_title_text_2 = xtc_db_prepare_input($_POST['slider_title_text_2']);
            $slider_url_3 = xtc_db_prepare_input($_POST['slider_url_3']);
            $slider_alt_text_3 = xtc_db_prepare_input($_POST['slider_alt_text_3']);
            $slider_title_text_3 = xtc_db_prepare_input($_POST['slider_title_text_3']);
            $slider_url_4 = xtc_db_prepare_input($_POST['slider_url_4']);
            $slider_alt_text_4 = xtc_db_prepare_input($_POST['slider_alt_text_4']);
            $slider_title_text_4 = xtc_db_prepare_input($_POST['slider_title_text_4']);
            $slider_url_5 = xtc_db_prepare_input($_POST['slider_url_5']);
            $slider_alt_text_5 = xtc_db_prepare_input($_POST['slider_alt_text_5']);
            $slider_title_text_5 = xtc_db_prepare_input($_POST['slider_title_text_5']);
            $slider_url_6 = xtc_db_prepare_input($_POST['slider_url_6']);
            $slider_alt_text_6 = xtc_db_prepare_input($_POST['slider_alt_text_6']);
            $slider_title_text_6 = xtc_db_prepare_input($_POST['slider_title_text_6']);
            $slider_url_7 = xtc_db_prepare_input($_POST['slider_url_7']);
            $slider_alt_text_7 = xtc_db_prepare_input($_POST['slider_alt_text_7']);
            $slider_title_text_7 = xtc_db_prepare_input($_POST['slider_title_text_7']);
            $slider_url_8 = xtc_db_prepare_input($_POST['slider_url_8']);
            $slider_alt_text_8 = xtc_db_prepare_input($_POST['slider_alt_text_8']);
            $slider_title_text_8 = xtc_db_prepare_input($_POST['slider_title_text_8']);
            $slider_image_local = xtc_db_prepare_input($_POST['slider_image_local']);
            $slider_image_local_2 = xtc_db_prepare_input($_POST['slider_image_local_2']);
            $slider_image_local_3 = xtc_db_prepare_input($_POST['slider_image_local_3']);
            $slider_image_local_4 = xtc_db_prepare_input($_POST['slider_image_local_4']);
            $slider_image_local_5 = xtc_db_prepare_input($_POST['slider_image_local_5']);
            $slider_image_local_6 = xtc_db_prepare_input($_POST['slider_image_local_6']);
            $slider_image_local_7 = xtc_db_prepare_input($_POST['slider_image_local_7']);
            $slider_image_local_8 = xtc_db_prepare_input($_POST['slider_image_local_8']);
            $html_text = xtc_db_prepare_input($_POST['html_text']);
            $db_image_location = '';
            $db_image_location_2 = '';
            $db_image_location_3 = '';
            $db_image_location_4 = '';
            $db_image_location_5 = '';
            $db_image_location_6 = '';
            $db_image_location_7 = '';
            $db_image_location_8 = '';
            $banner_error = false;

            if (empty($slider_title)) {
                $messageStack->add(ERROR_GALERIE_TITLE_REQUIRED, 'error');
                $banner_error = true;
            }

            if (!$slider_image = &xtc_try_upload('slider_image', DIR_FS_CATALOG_IMAGES . 'slider_images/') && $_POST['slider_image_local'] == '') {
                // $banner_error = true;
            }
            if (!$slider_image_2 = &xtc_try_upload('slider_image_2', DIR_FS_CATALOG_IMAGES . 'slider_images/') && $_POST['slider_image_local_2'] == '') {
                // $banner_error = true;
            }
            if (!$slider_image_3 = &xtc_try_upload('slider_image_3', DIR_FS_CATALOG_IMAGES . 'slider_images/') && $_POST['slider_image_local_3'] == '') {
                // $banner_error = true;
            }
            if (!$slider_image_4 = &xtc_try_upload('slider_image_4', DIR_FS_CATALOG_IMAGES . 'slider_images/') && $_POST['slider_image_local_4'] == '') {
                // $banner_error = true;
            }
            if (!$slider_image_5 = &xtc_try_upload('slider_image_5', DIR_FS_CATALOG_IMAGES . 'slider_images/') && $_POST['slider_image_local_5'] == '') {
                // $banner_error = true;
            }
            if (!$slider_image_6 = &xtc_try_upload('slider_image_6', DIR_FS_CATALOG_IMAGES . 'slider_images/') && $_POST['slider_image_local_6'] == '') {
                // $banner_error = true;
            }
            if (!$slider_image_7 = &xtc_try_upload('slider_image_7', DIR_FS_CATALOG_IMAGES . 'slider_images/') && $_POST['slider_image_local_7'] == '') {
                // $banner_error = true;
            }
            if (!$slider_image_8 = &xtc_try_upload('slider_image_8', DIR_FS_CATALOG_IMAGES . 'slider_images/') && $_POST['slider_image_local_8'] == '') {
                // $banner_error = true;
            }

            if (!$banner_error) {
                $db_image_location = ($slider_image) ? $slider_image->filename : $slider_image_local;
                $db_image_location_2 = ($slider_image_2) ? $slider_image_2->filename : $slider_image_local_2;
                $db_image_location_3 = ($slider_image_3) ? $slider_image_3->filename : $slider_image_local_3;
                $db_image_location_4 = ($slider_image_4) ? $slider_image_4->filename : $slider_image_local_4;
                $db_image_location_5 = ($slider_image_5) ? $slider_image_5->filename : $slider_image_local_5;
                $db_image_location_6 = ($slider_image_6) ? $slider_image_6->filename : $slider_image_local_6;
                $db_image_location_7 = ($slider_image_7) ? $slider_image_7->filename : $slider_image_local_7;
                $db_image_location_8 = ($slider_image_8) ? $slider_image_8->filename : $slider_image_local_8;
                $sql_data_array = array(
                    'slider_title' => $slider_title,
                    'slider_mobile' => $slider_mobile,
                    'slider_nav_status' => $slider_nav_status,
                    'slider_url' => $slider_url,
                    'slider_url_2' => $slider_url_2,
                    'slider_url_3' => $slider_url_3,
                    'slider_url_4' => $slider_url_4,
                    'slider_url_5' => $slider_url_5,
                    'slider_url_6' => $slider_url_6,
                    'slider_url_7' => $slider_url_7,
                    'slider_url_8' => $slider_url_8,
                    'slider_alt_text' => xtc_db_prepare_input($_POST['slider_alt_text']),
                    'slider_alt_text_2' => xtc_db_prepare_input($_POST['slider_alt_text_2']),
                    'slider_alt_text_3' => xtc_db_prepare_input($_POST['slider_alt_text_3']),
                    'slider_alt_text_4' => xtc_db_prepare_input($_POST['slider_alt_text_4']),
                    'slider_alt_text_5' => xtc_db_prepare_input($_POST['slider_alt_text_5']),
                    'slider_alt_text_6' => xtc_db_prepare_input($_POST['slider_alt_text_6']),
                    'slider_alt_text_7' => xtc_db_prepare_input($_POST['slider_alt_text_7']),
                    'slider_alt_text_8' => xtc_db_prepare_input($_POST['slider_alt_text_8']),
                    'slider_title_text' => xtc_db_prepare_input($_POST['slider_title_text']),
                    'slider_title_text_2' => xtc_db_prepare_input($_POST['slider_title_text_2']),
                    'slider_title_text_3' => xtc_db_prepare_input($_POST['slider_title_text_3']),
                    'slider_title_text_4' => xtc_db_prepare_input($_POST['slider_title_text_4']),
                    'slider_title_text_5' => xtc_db_prepare_input($_POST['slider_title_text_5']),
                    'slider_title_text_6' => xtc_db_prepare_input($_POST['slider_title_text_6']),
                    'slider_title_text_7' => xtc_db_prepare_input($_POST['slider_title_text_7']),
                    'slider_title_text_8' => xtc_db_prepare_input($_POST['slider_title_text_8']),
                    'slider_desc' => xtc_db_prepare_input($_POST['slider_desc']),
                    'slider_desc_2' => xtc_db_prepare_input($_POST['slider_desc_2']),
                    'slider_desc_3' => xtc_db_prepare_input($_POST['slider_desc_3']),
                    'slider_desc_4' => xtc_db_prepare_input($_POST['slider_desc_4']),
                    'slider_desc_5' => xtc_db_prepare_input($_POST['slider_desc_5']),
                    'slider_desc_6' => xtc_db_prepare_input($_POST['slider_desc_6']),
                    'slider_desc_7' => xtc_db_prepare_input($_POST['slider_desc_7']),
                    'slider_desc_8' => xtc_db_prepare_input($_POST['slider_desc_8']),
                    'slider_link_text' => xtc_db_prepare_input($_POST['slider_link_text']),
                    'slider_link_text_2' => xtc_db_prepare_input($_POST['slider_link_text_2']),
                    'slider_link_text_3' => xtc_db_prepare_input($_POST['slider_link_text_3']),
                    'slider_link_text_4' => xtc_db_prepare_input($_POST['slider_link_text_4']),
                    'slider_link_text_5' => xtc_db_prepare_input($_POST['slider_link_text_5']),
                    'slider_link_text_6' => xtc_db_prepare_input($_POST['slider_link_text_6']),
                    'slider_link_text_7' => xtc_db_prepare_input($_POST['slider_link_text_7']),
                    'slider_link_text_8' => xtc_db_prepare_input($_POST['slider_link_text_8']),
                    'fullsize' => xtc_db_prepare_input($_POST['fullsize']),
                    'slider_image' => $db_image_location,
                    'slider_image_2' => $db_image_location_2,
                    'slider_image_3' => $db_image_location_3,
                    'slider_image_4' => $db_image_location_4,
                    'slider_image_5' => $db_image_location_5,
                    'slider_image_6' => $db_image_location_6,
                    'slider_image_7' => $db_image_location_7,
                    'slider_image_8' => $db_image_location_8,
                    'slider_text' => $html_text,
                    'language_id' => xtc_db_prepare_input($_POST['flip_language_id']));

                if ($_GET['action'] == 'insert') {
                    $insert_sql_data = array('date_added' => 'now()', 'status' => '1');
                    $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
                    xtc_db_perform(TABLE_SLIDER_GALLERY, $sql_data_array);
                    $slider_id = xtc_db_insert_id();
                    $messageStack->add_session(SUCCESS_GALERIE_INSERTED, 'success');
                } elseif ($_GET['action'] == 'update') {
                    xtc_db_perform(TABLE_SLIDER_GALLERY, $sql_data_array, 'update', 'slider_id = \'' . $slider_id . '\'');
                    $delete_image_1 = xtc_db_prepare_input($_POST['del_pic_1']);
                    if ($delete_image_1 == 'on') {
                        $banner = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_SLIDER_GALLERY . " WHERE slider_id = '" . xtc_db_input($slider_id) . "'"));
                        if (is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image'])) {
                            if (is_writeable(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image'])) {
                                unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image']);
                            } else {
                                $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
                            }
                        } else {
                            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
                        }
                        xtc_db_query("UPDATE " . TABLE_SLIDER_GALLERY . " SET slider_image = '' WHERE slider_id = '" . xtc_db_input($slider_id) . "'");
                    }
                    $delete_image_2 = xtc_db_prepare_input($_POST['del_pic_2']);
                    if ($delete_image_2 == 'on') {
                        $banner = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_SLIDER_GALLERY . " WHERE slider_id = '" . xtc_db_input($slider_id) . "'"));
                        if (is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_2'])) {
                            if (is_writeable(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_2'])) {
                                unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_2']);
                            } else {
                                $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
                            }
                        } else {
                            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
                        }
                        xtc_db_query("UPDATE " . TABLE_SLIDER_GALLERY . " SET slider_image_2 = '' WHERE slider_id = '" . xtc_db_input($slider_id) . "'");
                    }
                    $delete_image_3 = xtc_db_prepare_input($_POST['del_pic_3']);
                    if ($delete_image_3 == 'on') {
                        $banner = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_SLIDER_GALLERY . " WHERE slider_id = '" . xtc_db_input($slider_id) . "'"));
                        if (is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_3'])) {
                            if (is_writeable(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_3'])) {
                                unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_3']);
                            } else {
                                $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
                            }
                        } else {
                            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
                        }
                        xtc_db_query("UPDATE " . TABLE_SLIDER_GALLERY . " SET slider_image_3 = '' WHERE slider_id = '" . xtc_db_input($slider_id) . "'");
                    }
                    $delete_image_4 = xtc_db_prepare_input($_POST['del_pic_4']);
                    if ($delete_image_4 == 'on') {
                        $banner = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_SLIDER_GALLERY . " WHERE slider_id = '" . xtc_db_input($slider_id) . "'"));
                        if (is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_4'])) {
                            if (is_writeable(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_4'])) {
                                unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_4']);
                            } else {
                                $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
                            }
                        } else {
                            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
                        }
                        xtc_db_query("UPDATE " . TABLE_SLIDER_GALLERY . " SET slider_image_4 = '' WHERE slider_id = '" . xtc_db_input($slider_id) . "'");
                    }
                    $delete_image_5 = xtc_db_prepare_input($_POST['del_pic_5']);
                    if ($delete_image_5 == 'on') {
                        $banner = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_SLIDER_GALLERY . " WHERE slider_id = '" . xtc_db_input($slider_id) . "'"));
                        if (is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_5'])) {
                            if (is_writeable(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_5'])) {
                                unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_5']);
                            } else {
                                $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
                            }
                        } else {
                            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
                        }
                        xtc_db_query("UPDATE " . TABLE_SLIDER_GALLERY . " SET slider_image_5 = '' WHERE slider_id = '" . xtc_db_input($slider_id) . "'");
                    }
                    $delete_image_6 = xtc_db_prepare_input($_POST['del_pic_6']);
                    if ($delete_image_6 == 'on') {
                        $banner = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_SLIDER_GALLERY . " WHERE slider_id = '" . xtc_db_input($slider_id) . "'"));
                        if (is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_6'])) {
                            if (is_writeable(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_6'])) {
                                unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_6']);
                            } else {
                                $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
                            }
                        } else {
                            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
                        }
                        xtc_db_query("UPDATE " . TABLE_SLIDER_GALLERY . " SET slider_image_6 = '' WHERE slider_id = '" . xtc_db_input($slider_id) . "'");
                    }
                    $delete_image_7 = xtc_db_prepare_input($_POST['del_pic_7']);
                    if ($delete_image_7 == 'on') {
                        $banner = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_SLIDER_GALLERY . " WHERE slider_id = '" . xtc_db_input($slider_id) . "'"));
                        if (is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_7'])) {
                            if (is_writeable(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_7'])) {
                                unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_7']);
                            } else {
                                $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
                            }
                        } else {
                            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
                        }
                        xtc_db_query("UPDATE " . TABLE_SLIDER_GALLERY . " SET slider_image_7 = '' WHERE slider_id = '" . xtc_db_input($slider_id) . "'");
                    }
                    $delete_image_8 = xtc_db_prepare_input($_POST['del_pic_8']);
                    if ($delete_image_8 == 'on') {
                        $banner = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_SLIDER_GALLERY . " WHERE slider_id = '" . xtc_db_input($slider_id) . "'"));
                        if (is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_8'])) {
                            if (is_writeable(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_8'])) {
                                unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_8']);
                            } else {
                                $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
                            }
                        } else {
                            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
                        }
                        xtc_db_query("UPDATE " . TABLE_SLIDER_GALLERY . " SET slider_image_8 = '' WHERE slider_id = '" . xtc_db_input($slider_id) . "'");
                    }
                    $messageStack->add_session(UPDATE_TEXT, 'success');
                }
                xtc_redirect(xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $slider_id));
            } else {
                $_GET['action'] = 'new';
            }
            break;
        case 'deleteconfirm':
            $slider_id = xtc_db_prepare_input($_GET['bID']);
            $delete_image = xtc_db_prepare_input($_POST['delete_image']);
            if ($delete_image == 'on') {
                $banner = xtc_db_fetch_array(xtc_db_query("SELECT slider_image, slider_image_2, slider_image_3, slider_image_4, slider_image_5, slider_image_6, slider_image_7, slider_image_8 FROM " . TABLE_SLIDER_GALLERY . " WHERE slider_id = '" . xtc_db_input($slider_id) . "'"));
                if (is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image'])) {
                    if (is_writeable(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image'])) {
                        unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image']);
                        unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_2']);
                        unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_3']);
                        unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_4']);
                        unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_5']);
                        unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_6']);
                        unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_7']);
                        unlink(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $banner['slider_image_8']);
                    } else {
                        $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
                    }
                } else {
                    $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
                }
            }

            xtc_db_query("DELETE FROM " . TABLE_SLIDER_GALLERY . " WHERE slider_id = '" . xtc_db_input($slider_id) . "'");
            $messageStack->add_session(SUCCESS_GALERIE_REMOVED, 'success');
            xtc_redirect(xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page']));
            break;
    }
}

// load languages
$language_query = xtc_db_query("SELECT languages_id, name FROM " . TABLE_LANGUAGES);
$language_array = array();
while ($langauge = xtc_db_fetch_array($language_query)) {
    $language_array[] = array('id' => $langauge['languages_id'], 'text' => $langauge['name']);
}

$fullsize_array = array();
$fullsize_array[] = array('id' => 0, 'text' => 'Nein');
$fullsize_array[] = array('id' => 1, 'text' => 'Ja');
require_once(DIR_WS_INCLUDES . 'header.php');
if ($_GET['action'] == 'new') {
    $form_action = 'insert';
    if ($_GET['bID']) {
        $bID = xtc_db_prepare_input($_GET['bID']);
        $form_action = 'update';
        $banner = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_SLIDER_GALLERY . " WHERE slider_id = '" . xtc_db_input($bID) . "';"));
        $bInfo = new objectInfo($banner);
    } elseif ($_POST) {
        $bInfo = new objectInfo($_POST);
    } else {
        $bInfo = new objectInfo(array());
        $bInfo->fullsize = 1;
        $bInfo->slider_mobile = 1;
        $bInfo->slider_nav_status = 1;
    }
    $smarty->assign('FORM', xtc_draw_form('new_banner', FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&action=' . $form_action, 'post', 'enctype="multipart/form-data"'));
    if ($form_action == 'update') {
        $smarty->assign('HIDDEN', xtc_draw_hidden_field('slider_id', $bID));
    }
    $smarty->assign('BUTTONS', (($form_action == 'insert') ? '<input type="submit" class="btn btn-success" value="' . BUTTON_INSERT . '"/>' : '<input type="submit" class="btn btn-success" value="' . BUTTON_UPDATE . '"/>') . '&nbsp;&nbsp;<a class="btn btn-primary" href="' . xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $_GET['bID']) . '">' . BUTTON_CANCEL . '</a>');
    $smarty->assign('SLIDER_NAME', xtc_draw_input_field('slider_title', $bInfo->slider_title, '', true));
    $smarty->assign('SLIDER_LANG', xtc_draw_pull_down_menu('flip_language_id', $language_array, (isset($bInfo->language_id) ? $bInfo->language_id : $_SESSION['languages_id'])));
    $smarty->assign('SLIDER_SIZE', xtc_draw_pull_down_menu('fullsize', $fullsize_array, $bInfo->fullsize));
    $smarty->assign('SLIDER_MOBILE', xtc_draw_pull_down_menu('slider_mobile', $fullsize_array, $bInfo->slider_mobile));
    $smarty->assign('SLIDER_NAV_STATUS', xtc_draw_pull_down_menu('slider_nav_status', $fullsize_array, $bInfo->slider_nav_status));
    $smarty->assign('SLIDER_URL', xtc_draw_input_field('slider_url', $bInfo->slider_url, '', false));
    $smarty->assign('SLIDER_ALT_TEXT', xtc_draw_input_field('slider_alt_text', $bInfo->slider_alt_text, '', false));
    $smarty->assign('SLIDER_TITLE_TEXT', xtc_draw_input_field('slider_title_text', $bInfo->slider_title_text, '', false));
    $smarty->assign('SLIDER_LINK_TEXT', xtc_draw_input_field('slider_link_text', $bInfo->slider_link_text, '', false));
    $smarty->assign('SLIDER_DESC', xtc_draw_textarea_field('slider_desc', 'soft', '100', '5', $bInfo->slider_desc));
    $smarty->assign('SLIDER_IMAGE', xtc_draw_file_field('slider_image'));
    $smarty->assign('SLIDER_IMAGE_USE', DIR_WS_IMAGES . 'slider_images/' . $bInfo->slider_image . xtc_draw_hidden_field('slider_image_local', $bInfo->slider_image));
    if ($bInfo->slider_image != '') {
        $smarty->assign('SLIDER_IMAGE_DELETE_1', xtc_draw_checkbox_field('del_pic_1', 'on', false) . TEXT_DELETE);
        $smarty->assign('SLIDER_PREVIEW', xtc_image(DIR_WS_CATALOG_IMAGES . 'slider_images/' . $bInfo->slider_image, 'Preview', '300px', 'auto'));
    }

    $smarty->assign('SLIDER_URL_2', xtc_draw_input_field('slider_url_2', $bInfo->slider_url_2, '', false));
    $smarty->assign('SLIDER_ALT_TEXT_2', xtc_draw_input_field('slider_alt_text_2', $bInfo->slider_alt_text_2, '', false));
    $smarty->assign('SLIDER_TITLE_TEXT_2', xtc_draw_input_field('slider_title_text_2', $bInfo->slider_title_text_2, '', false));
    $smarty->assign('SLIDER_LINK_TEXT_2', xtc_draw_input_field('slider_link_text_2', $bInfo->slider_link_text_2, '', false));
    $smarty->assign('SLIDER_DESC_2', xtc_draw_textarea_field('slider_desc_2', 'soft', '100', '5', $bInfo->slider_desc_2));
    $smarty->assign('SLIDER_IMAGE_2', xtc_draw_file_field('slider_image_2'));
    $smarty->assign('SLIDER_IMAGE_USE_2', DIR_WS_IMAGES . 'slider_images/' . $bInfo->slider_image_2 . xtc_draw_hidden_field('slider_image_local_2', $bInfo->slider_image_2));
    if ($bInfo->slider_image_2 != '') {
        $smarty->assign('SLIDER_IMAGE_DELETE_2', xtc_draw_checkbox_field('del_pic_2', 'on', false) . TEXT_DELETE);
        $smarty->assign('SLIDER_PREVIEW_2', xtc_image(DIR_WS_CATALOG_IMAGES . 'slider_images/' . $bInfo->slider_image_2, 'Preview', '300px', 'auto'));
    }

    $smarty->assign('SLIDER_URL_3', xtc_draw_input_field('slider_url_3', $bInfo->slider_url_3, '', false));
    $smarty->assign('SLIDER_ALT_TEXT_3', xtc_draw_input_field('slider_alt_text_3', $bInfo->slider_alt_text_3, '', false));
    $smarty->assign('SLIDER_TITLE_TEXT_3', xtc_draw_input_field('slider_title_text_3', $bInfo->slider_title_text_3, '', false));
    $smarty->assign('SLIDER_LINK_TEXT_3', xtc_draw_input_field('slider_link_text_3', $bInfo->slider_link_text_3, '', false));
    $smarty->assign('SLIDER_DESC_3', xtc_draw_textarea_field('slider_desc_3', 'soft', '100', '5', $bInfo->slider_desc_3));
    $smarty->assign('SLIDER_IMAGE_3', xtc_draw_file_field('slider_image_3'));
    $smarty->assign('SLIDER_IMAGE_USE_3', DIR_WS_IMAGES . 'slider_images/' . $bInfo->slider_image_3 . xtc_draw_hidden_field('slider_image_local_3', $bInfo->slider_image_3));
    if ($bInfo->slider_image_3 != '') {
        $smarty->assign('SLIDER_IMAGE_DELETE_3', xtc_draw_checkbox_field('del_pic_3', 'on', false) . TEXT_DELETE);
        $smarty->assign('SLIDER_PREVIEW_3', xtc_image(DIR_WS_CATALOG_IMAGES . 'slider_images/' . $bInfo->slider_image_3, 'Preview', '300px', 'auto'));
    }

    $smarty->assign('SLIDER_URL_4', xtc_draw_input_field('slider_url_4', $bInfo->slider_url_4, '', false));
    $smarty->assign('SLIDER_ALT_TEXT_4', xtc_draw_input_field('slider_alt_text_4', $bInfo->slider_alt_text_4, '', false));
    $smarty->assign('SLIDER_TITLE_TEXT_4', xtc_draw_input_field('slider_title_text_4', $bInfo->slider_title_text_4, '', false));
    $smarty->assign('SLIDER_LINK_TEXT_4', xtc_draw_input_field('slider_link_text_4', $bInfo->slider_link_text_4, '', false));
    $smarty->assign('SLIDER_DESC_4', xtc_draw_textarea_field('slider_desc_4', 'soft', '100', '5', $bInfo->slider_desc_4));
    $smarty->assign('SLIDER_IMAGE_4', xtc_draw_file_field('slider_image_4'));
    $smarty->assign('SLIDER_IMAGE_USE_4', DIR_WS_IMAGES . 'slider_images/' . $bInfo->slider_image_4 . xtc_draw_hidden_field('slider_image_local_4', $bInfo->slider_image_4));
    if ($bInfo->slider_image_4 != '') {
        $smarty->assign('SLIDER_IMAGE_DELETE_4', xtc_draw_checkbox_field('del_pic_4', 'on', false) . TEXT_DELETE);
        $smarty->assign('SLIDER_PREVIEW_4', xtc_image(DIR_WS_CATALOG_IMAGES . 'slider_images/' . $bInfo->slider_image_4, 'Preview', '300px', 'auto'));
    }


    $smarty->assign('SLIDER_URL_5', xtc_draw_input_field('slider_url_5', $bInfo->slider_url_5, '', false));
    $smarty->assign('SLIDER_ALT_TEXT_5', xtc_draw_input_field('slider_alt_text_5', $bInfo->slider_alt_text_5, '', false));
    $smarty->assign('SLIDER_TITLE_TEXT_5', xtc_draw_input_field('slider_title_text_5', $bInfo->slider_title_text_5, '', false));
    $smarty->assign('SLIDER_LINK_TEXT_5', xtc_draw_input_field('slider_link_text_5', $bInfo->slider_link_text_5, '', false));
    $smarty->assign('SLIDER_DESC_5', xtc_draw_textarea_field('slider_desc_5', 'soft', '100', '5', $bInfo->slider_desc_5));
    $smarty->assign('SLIDER_IMAGE_5', xtc_draw_file_field('slider_image_5'));
    $smarty->assign('SLIDER_IMAGE_USE_5', DIR_WS_IMAGES . 'slider_images/' . $bInfo->slider_image_5 . xtc_draw_hidden_field('slider_image_local_5', $bInfo->slider_image_5));
    if ($bInfo->slider_image_5 != '') {
        $smarty->assign('SLIDER_IMAGE_DELETE_5', xtc_draw_checkbox_field('del_pic_5', 'on', false) . TEXT_DELETE);
        $smarty->assign('SLIDER_PREVIEW_5', xtc_image(DIR_WS_CATALOG_IMAGES . 'slider_images/' . $bInfo->slider_image_5, 'Preview', '300px', 'auto'));
    }
    
    $smarty->assign('SLIDER_URL_6', xtc_draw_input_field('slider_url_6', $bInfo->slider_url_6, '', false));
    $smarty->assign('SLIDER_ALT_TEXT_6', xtc_draw_input_field('slider_alt_text_6', $bInfo->slider_alt_text_6, '', false));
    $smarty->assign('SLIDER_TITLE_TEXT_6', xtc_draw_input_field('slider_title_text_6', $bInfo->slider_title_text_6, '', false));
    $smarty->assign('SLIDER_LINK_TEXT_6', xtc_draw_input_field('slider_link_text_6', $bInfo->slider_link_text_6, '', false));
    $smarty->assign('SLIDER_DESC_6', xtc_draw_textarea_field('slider_desc_6', 'soft', '100', '5', $bInfo->slider_desc_6));
    $smarty->assign('SLIDER_IMAGE_6', xtc_draw_file_field('slider_image_6'));
    $smarty->assign('SLIDER_IMAGE_USE_6', DIR_WS_IMAGES . 'slider_images/' . $bInfo->slider_image_6 . xtc_draw_hidden_field('slider_image_local_6', $bInfo->slider_image_6));
    if ($bInfo->slider_image_6 != '') {
        $smarty->assign('SLIDER_IMAGE_DELETE_6', xtc_draw_checkbox_field('del_pic_6', 'on', false) . TEXT_DELETE);
        $smarty->assign('SLIDER_PREVIEW_6', xtc_image(DIR_WS_CATALOG_IMAGES . 'slider_images/' . $bInfo->slider_image_6, 'Preview', '300px', 'auto'));
    }
    
    $smarty->assign('SLIDER_URL_7', xtc_draw_input_field('slider_url_7', $bInfo->slider_url_7, '', false));
    $smarty->assign('SLIDER_ALT_TEXT_7', xtc_draw_input_field('slider_alt_text_7', $bInfo->slider_alt_text_7, '', false));
    $smarty->assign('SLIDER_TITLE_TEXT_7', xtc_draw_input_field('slider_title_text_7', $bInfo->slider_title_text_7, '', false));
    $smarty->assign('SLIDER_LINK_TEXT_7', xtc_draw_input_field('slider_link_text_7', $bInfo->slider_link_text_7, '', false));
    $smarty->assign('SLIDER_DESC_7', xtc_draw_textarea_field('slider_desc_7', 'soft', '100', '5', $bInfo->slider_desc_7));
    $smarty->assign('SLIDER_IMAGE_7', xtc_draw_file_field('slider_image_7'));
    $smarty->assign('SLIDER_IMAGE_USE_7', DIR_WS_IMAGES . 'slider_images/' . $bInfo->slider_image_7 . xtc_draw_hidden_field('slider_image_local_7', $bInfo->slider_image_7));
    if ($bInfo->slider_image_7 != '') {
        $smarty->assign('SLIDER_IMAGE_DELETE_7', xtc_draw_checkbox_field('del_pic_7', 'on', false) . TEXT_DELETE);
        $smarty->assign('SLIDER_PREVIEW_7', xtc_image(DIR_WS_CATALOG_IMAGES . 'slider_images/' . $bInfo->slider_image_7, 'Preview', '300px', 'auto'));
    }
    
    $smarty->assign('SLIDER_URL_8', xtc_draw_input_field('slider_url_8', $bInfo->slider_url_8, '', false));
    $smarty->assign('SLIDER_ALT_TEXT_8', xtc_draw_input_field('slider_alt_text_8', $bInfo->slider_alt_text_8, '', false));
    $smarty->assign('SLIDER_TITLE_TEXT_8', xtc_draw_input_field('slider_title_text_8', $bInfo->slider_title_text_8, '', false));
    $smarty->assign('SLIDER_LINK_TEXT_8', xtc_draw_input_field('slider_link_text_8', $bInfo->slider_link_text_8, '', false));
    $smarty->assign('SLIDER_DESC_8', xtc_draw_textarea_field('slider_desc_8', 'soft', '100', '5', $bInfo->slider_desc_8));
    $smarty->assign('SLIDER_IMAGE_8', xtc_draw_file_field('slider_image_8'));
    $smarty->assign('SLIDER_IMAGE_USE_8', DIR_WS_IMAGES . 'slider_images/' . $bInfo->slider_image_8 . xtc_draw_hidden_field('slider_image_local_8', $bInfo->slider_image_8));
    if ($bInfo->slider_image_8 != '') {
        $smarty->assign('SLIDER_IMAGE_DELETE_8', xtc_draw_checkbox_field('del_pic_8', 'on', false) . TEXT_DELETE);
        $smarty->assign('SLIDER_PREVIEW_8', xtc_image(DIR_WS_CATALOG_IMAGES . 'slider_images/' . $bInfo->slider_image_8, 'Preview', '300px', 'auto'));
    }

    $smarty->assign('SLIDER_TEXFILED', xtc_draw_textarea_field('html_text', 'soft', '100', '50', $bInfo->slider_text));
    $smarty->assign('FORM_END', '</form>');
    $smarty->assign('NEW', '1');
} else {
	$languages = xtc_get_languages();
    $banners_query_raw = "SELECT * FROM " . TABLE_SLIDER_GALLERY . " ORDER BY slider_title";
    $banners_split = new splitPageResults($_GET['page'], $orderlistingnum, $banners_query_raw, $banners_query_numrows);
    $banners_query = xtc_db_query($banners_query_raw);
    $smarty->assign('DISPLAY_NUMBER', $banners_split->display_count($banners_query_numrows, $orderlistingnum, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BANNERS));
    $smarty->assign('DISPLAY_SITE', $banners_split->display_links($banners_query_numrows, $orderlistingnum, MAX_DISPLAY_PAGE_LINKS, $_GET['page']));
	while ($banners = xtc_db_fetch_array($banners_query)) {
		$languages_query = xtc_db_fetch_array(xtc_db_query("SELECT languages_id, name, image, directory FROM " . TABLE_LANGUAGES . " WHERE languages_id = '".$banners['language_id']."';"));
		$lang_icon = xtc_image(DIR_WS_LANGUAGES . $languages_query['directory'] . '/' . $languages_query['image'], $languages_query['name']);
		if (((!$_GET['bID']) || ($_GET['bID'] == $banners['slider_id'])) && (!$bInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
            $bInfo_array = xtc_array_merge($banners, $info);
            $bInfo = new objectInfo($bInfo_array);
        }
        if ($banners['status'] == '1') {
            $status = '<a href="' . xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['slider_id'] . '&action=setflag&flag=0') . '"><button class="btn btn-success btn-xs"><i class="glyphicon glyphicon-ok"></i></button></a>';
        } else {
            $status = '<a href="' . xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['slider_id'] . '&action=setflag&flag=1') . '"><button class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></button></a>';
        }
        if ($banners['slider_mobile'] == '1') {
            $slider_mobile = '<a href="' . xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['slider_id'] . '&action=slider_mobile&flag=0') . '"><button class="btn btn-success btn-xs"><i class="glyphicon glyphicon-ok"></i></button></a>';
        } else {
            $slider_mobile = '<a href="' . xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['slider_id'] . '&action=slider_mobile&flag=1') . '"><button class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></button></a>';
        }
        if ($banners['slider_nav_status'] == '1') {
            $slider_nav_status = '<a href="' . xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['slider_id'] . '&action=slider_nav_status&flag=0') . '"><button class="btn btn-success btn-xs"><i class="glyphicon glyphicon-ok"></i></button></a>';
        } else {
            $slider_nav_status = '<a href="' . xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['slider_id'] . '&action=slider_nav_status&flag=1') . '"><button class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></button></a>';
        }
		if ((is_object($bInfo)) && ($banners['slider_id'] == $bInfo->slider_id)) {
			$action = 'active';
		} else {
			$action = xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['slider_id']);
		}
        $sliderlistarray[] = array(
            'TR' => '<tr onclick="document.location.href=\'' . xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['slider_id']) . '\'">',
            'TITLE' => $banners['slider_title'],
            'STATUS' => $status,
			'MOBILE_STATUS' => $slider_mobile,
			'SLIDER_NAV_STATUS' => $slider_nav_status,
            'LANG' => $lang_icon,
            'action' => $action,
			'edit' => xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['slider_id'] . '&action=new'),
			);
    }
    $smarty->assign('sliderlistarray', $sliderlistarray);
    $smarty->assign('BUTTON_NEW', xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'action=new'));

    $heading = array();
    $contents = array();
    switch ($_GET['action']) {
        case 'delete':
            $heading[] = array('text' => '<b>' . $bInfo->slider_title . '</b>');
            $contents = array('form' => xtc_draw_form('banners', FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->slider_id . '&action=deleteconfirm'));
            $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
            $contents[] = array('text' => '<br /><b>' . $bInfo->slider_title . '</b>');
            if ($bInfo->slider_image) {
                $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('delete_image', 'on', true) . ' ' . TEXT_INFO_DELETE_IMAGE);
            }
            $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="btn btn-danger" value="' . BUTTON_DELETE . '"/>&nbsp;<a class="btn btn-primary" href="' . xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $_GET['bID']) . '">' . BUTTON_CANCEL . '</a>');
            break;
        default:
            if (is_object($bInfo)) {
                $heading[] = array('text' => '<b>' . $bInfo->slider_title . '</b>');
                $contents[] = array('align' => 'center', 'text' => '<a href="' . xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->slider_id . '&action=new') . '"><button type="button" class="btn btn-primary">' . BUTTON_EDIT . '</button></a> <a href="' . xtc_href_link(FILENAME_CSEO_GALLERY_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->slider_id . '&action=delete') . '"><button type="button" class="btn btn-danger">' . BUTTON_DELETE . '</button></a>');
                if ($bInfo->date_status_change) {
                    $contents[] = array('text' => '<br />' . sprintf(TEXT_BANNERS_STATUS_CHANGE, xtc_date_short($bInfo->date_status_change)));
                }
            }
            break;
    }

    if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
        $box = new box;
        $smarty->assign('SITE_BOX', $box->infoBox($heading, $contents));
    }
}

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
$smarty->caching = false;
$smarty->template_dir = DIR_FS_CATALOG . 'admin/templates/';
$smarty->compile_dir = DIR_FS_CATALOG . 'admin/templates_c';
$smarty->display(CURRENT_ADMIN_TEMPLATE . '/cseo_gallery_manager.html');
require_once(DIR_WS_INCLUDES . 'footer.php');
require_once(DIR_WS_INCLUDES . 'application_bottom.php');
