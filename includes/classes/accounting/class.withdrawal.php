<?php

/* -----------------------------------------------------------------
 * 	$Id: class.withdrawal.php 816 2014-01-28 07:07:08Z akausch $
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

class withdrawal_ORIGINAL {

    function withdrawal_ORIGINAL() {
        
    }

	public function set_order_hash($p_order_hash) {
		$oid = xtc_db_input($p_order_hash);
		$t_result_array = xtc_db_fetch_array(xtc_db_query("SELECT orders_id FROM orders WHERE orders_id = '" . $oid . "';"));
		return $t_result_array['orders_id'];
	}
	
	function get_customer_mail($customer_id) {
		$customer_info = xtc_db_fetch_array(xtc_db_query("SELECT customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . $customer_id."';"));
		return $customer_info['customers_email_address'];
	}
	function get_customer_delivery_address($oid) {
		$order_info = xtc_db_fetch_array(xtc_db_query("SELECT 
														delivery_street_address,
														delivery_city,
														delivery_postcode,
														date_purchased,
														delivery_country
													FROM 
														" . TABLE_ORDERS . " 
													WHERE 
														orders_id = '" . xtc_db_prepare_input($oid)."';"));
		return $order_info;
	}
	function validate_oid($oid, $cid) {
		$order_validate = xtc_db_query("SELECT 
										orders_id,
										customers_id
									FROM 
										" . TABLE_ORDERS . " 
									WHERE 
										orders_id = '" . xtc_db_prepare_input($oid)."'
									AND
										customers_id = '" . xtc_db_prepare_input($cid)."';");
		$ovn = xtc_db_num_rows($order_validate);
		return $ovn;
	}
	
    function withdrawal_smarty($oid, $cid) {
		$oid = xtc_db_prepare_input($oid);
		$cid = xtc_db_prepare_input($cid);
		require_once (DIR_FS_INC . 'xtc_date_short.inc.php');
		
		$this->v_data_array['FORM_ACTION'] = xtc_draw_form('withdrawal', xtc_href_link(FILENAME_WITHDRAWAL, '', 'SSL'), 'post', 'autocomplete="off"') . xtc_draw_hidden_field('action', 'process_withdrawal');

		if ($this->validate_oid($oid, $cid) == 1 && isset($_SESSION['customer_id'])) {
			$this->v_data_array['ORDER_ID'] = xtc_draw_hidden_field('order_id', $oid);
			$customer_adress = $this->get_customer_delivery_address($oid);
		} else {
			$this->v_data_array['ORDER_ID']= xtc_draw_input_fieldNote(array('name' => 'order_id', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), '', 'required');
			$this->v_data_array['ORDER_ID_ENABLE'] = 'true';
		}

		
		if ($this->validate_oid($oid, $cid) == 1 && isset($_SESSION['customer_id'])) {
			$this->v_data_array['CUSTOMER_ID'] = xtc_draw_hidden_field('customer_id', $cid);
		} else {
			$this->v_data_array['CUSTOMER_ID'] = '';
		}
		
		$gender = array(array('id' => 'm', 'text' => MALE));
		$gender[] = array('id' => 'f', 'text' => FEMALE);
		
		$this->v_data_array['CUSTOMER_GENDER']= xtc_draw_pull_down_menu('customer_gender', $gender);
		
		if (isset($_SESSION['customer_id'])) {
			$this->v_data_array['INPUT_FIRSTNAME'] = xtc_draw_input_fieldNote(array('name' => 'customer_firstname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>' : '')), (xtc_not_null(xtc_db_prepare_input($_POST['customer_firstname'])) ? xtc_db_prepare_input($_POST['customer_firstname']) : xtc_db_prepare_input($_SESSION['customer_first_name'])), 'required');
			$this->v_data_array['INPUT_LASTNAME'] = xtc_draw_input_fieldNote(array('name' => 'customer_lastname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), (xtc_not_null(xtc_db_prepare_input($_POST['customer_lastname'])) ? xtc_db_prepare_input($_POST['customer_lastname']) : xtc_db_prepare_input($_SESSION['customer_last_name'])), 'required');
			$this->v_data_array['INPUT_EMAIL'] = xtc_draw_input_fieldNote(array('name' => 'customer_email', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), (xtc_not_null(xtc_db_prepare_input($_POST['customer_email'])) ? xtc_db_prepare_input($_POST['customer_email']) : $this->get_customer_mail((int)($_SESSION['customer_id']))), 'required');
		} else {
			$this->v_data_array['INPUT_FIRSTNAME'] = xtc_draw_input_fieldNote(array('name' => 'customer_firstname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['customer_firstname']), 'required');
			$this->v_data_array['INPUT_LASTNAME'] = xtc_draw_input_fieldNote(array('name' => 'customer_lastname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['customer_lastname']), 'required');
			$this->v_data_array['INPUT_EMAIL'] = xtc_draw_input_fieldNote(array('name' => 'customer_email', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['customer_email']), 'required');
		}

		$this->v_data_array['CUSTOMER_STREET_ADDRESS'] = xtc_draw_input_fieldNote(array('name' => 'customer_street_address', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), (xtc_not_null(xtc_db_prepare_input($_POST['customer_street_address'])) ? xtc_db_prepare_input($_POST['customer_street_address']) : xtc_db_prepare_input($customer_adress['delivery_street_address'])), 'required');
		$this->v_data_array['ORDER_DATE'] = xtc_draw_input_fieldNote(array('name' => 'order_date', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), (xtc_not_null(xtc_db_prepare_input($_POST['order_date'])) ? xtc_db_prepare_input($_POST['order_date']) : xtc_db_prepare_input(xtc_date_short($customer_adress['date_purchased']))), 'required');
		$this->v_data_array['INPUT_POSTCODE'] = xtc_draw_input_fieldNote(array('name' => 'customer_postcode', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), (xtc_not_null(xtc_db_prepare_input($_POST['customer_postcode'])) ? xtc_db_prepare_input($_POST['customer_postcode']) : xtc_db_prepare_input($customer_adress['delivery_postcode'])), 'required');
		$this->v_data_array['INPUT_CITY'] = xtc_draw_input_fieldNote(array('name' => 'customer_city', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), (xtc_not_null(xtc_db_prepare_input($_POST['customer_city'])) ? xtc_db_prepare_input($_POST['customer_city']) : xtc_db_prepare_input($customer_adress['delivery_city'])), 'required');

		if (isset($_POST['customer_country'])) {
            $selected = xtc_db_prepare_input($_POST['customer_country']);
        } elseif (isset($_SESSION['country'])) {
            $selected = xtc_db_prepare_input($_SESSION['country']);
        } else {
            $selected = STORE_COUNTRY;
        }
        $counrty_count_query = xtc_db_query("SELECT countries_id, status FROM " . TABLE_COUNTRIES . " WHERE status = '1'");
        $counrty_count = xtc_db_num_rows($counrty_count_query);
        if ($counrty_count > 1) {
            require_once (DIR_FS_INC . 'xtc_get_country_list.inc.php');
			$this->v_data_array['INPUT_COUNTRY'] = xtc_get_country_list(array('name' => 'customer_country', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>' : '')), $selected, '');
            $this->v_data_array['SELECT_COUNTRY_ENABLE'] = 'true';
        } else {
            $this->v_data_array['INPUT_COUNTRY'] = xtc_draw_hidden_field('customer_country', $selected);
        }
		
		$this->v_data_array['INPUT_DELIVERYDATE'] = xtc_draw_input_fieldNote(array('name' => 'delivery_date', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['delivery_date']), 'required');
		$this->v_data_array['INPUT_WITHDRAWALDATE'] = xtc_draw_input_fieldNote(array('name' => 'withdrawal_date', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), (xtc_not_null(xtc_db_prepare_input($_POST['withdrawal_date'])) ? xtc_db_prepare_input($_POST['withdrawal_date']) : strftime(DATE_FORMAT_SHORT)), 'required');
		$coo_text_mgr = new LanguageTextManager('withdrawal', (int)$_SESSION['languages_id']);
	
		if ($this->validate_oid($oid, $cid) == 1 && isset($_SESSION['customer_id'])) {
		$order = new order($oid);
		$order_data = $order->getOrderData($oid);
		$counter = count($order_data);
		for ($i = 0; $i < $counter; $i++) {
		$order_details .= $order_data[$i]['PRODUCTS_QTY'];
		$order_details .= ' x ';
		$order_details .= $order_data[$i]['PRODUCTS_NAME'];
		if ($order_data[$i]['PRODUCTS_MODEL'] != '') {
			$order_details .= $order_data[$i]['PRODUCTS_MODEL'];
		}
		if ($order_data[$i]['PRODUCTS_ATTRIBUTES'] != '') {
			$products_attributes = $order_data[$i]['PRODUCTS_ATTRIBUTES'];
			$products_attributes = str_replace('<br />', ' / ', $products_attributes);
			$products_attributes = str_replace('<br>', ' / ', $products_attributes);
			$order_details .= $products_attributes;
		}
		// if ($order_data[$i]['PRODUCTS_ATTRIBUTES_MODEL'] != '' && $order_data[$i]['PRODUCTS_ATTRIBUTES_MODEL'] != '<br /><br />') {
			// $products_attributes_model = $order_data[$i]['PRODUCTS_ATTRIBUTES_MODEL'];
			// $products_attributes_model = str_replace('<br />', ' - ', $products_attributes_model);
			// $products_attributes_model = str_replace('<br>', ' - ', $products_attributes_model);
			// $order_details .= $products_attributes_model;
		// }
		$order_details .= "\n";
		}
		// echo '<pre>';
		// var_dump($order_data);
		// var_dump($order_details);
		// echo '</pre>';
		}
		$this->v_data_array['INPUT_TEXT'] = xtc_draw_textarea_field('withdrawal_content', 'soft', 50, 15, (xtc_not_null(xtc_db_prepare_input($_POST['withdrawal_content'])) ? xtc_db_prepare_input($_POST['withdrawal_content']) : $coo_text_mgr->v_section_content_array['withdrawal']['withdrawal_textarea_default'] . "\n" . $order_details));
		
		$this->v_data_array['BUTTON_SUBMIT'] = xtc_image_submit('button_send.gif', IMAGE_BUTTON_CONTINUE);
		$this->v_data_array['FORM_END'] = '</form>';

	
		if (isset($_POST['action']) && ($_POST['action'] == 'process_withdrawal')) {
			$check_withdrawal = $this->check_withdrawal();
			$this->v_data_array['error'] = $check_withdrawal;
		}
		
        return $this->v_data_array;
    }
	
	function check_withdrawal() {
		$messageStack = new messageStack;
        $process = false;
        if (isset($_POST['action']) && ($_POST['action'] == 'process_withdrawal')) {
            $process = true;
			$smarty = new Smarty;
			
			$order_id = xtc_db_prepare_input($_POST['order_id']);
            $customer_id = xtc_db_prepare_input($_POST['customer_id']);
            $customer_gender = xtc_db_prepare_input($_POST['customer_gender']);
            $customer_firstname = xtc_db_prepare_input($_POST['customer_firstname']);
            $customer_lastname = xtc_db_prepare_input($_POST['customer_lastname']);
            $customer_email = xtc_db_prepare_input($_POST['customer_email']);
			$customer_street_address = xtc_db_prepare_input($_POST['customer_street_address']);
            $customer_postcode = xtc_db_prepare_input($_POST['customer_postcode']);
            $customer_city = xtc_db_prepare_input($_POST['customer_city']);
			$customer_country = xtc_db_prepare_input($_POST['customer_country']);
			$delivery_date = xtc_db_prepare_input($_POST['delivery_date']);
            $withdrawal_date = xtc_db_prepare_input($_POST['withdrawal_date']);
            $order_date = xtc_db_prepare_input($_POST['order_date']);
			$withdrawal_content = xtc_db_prepare_input($_POST['withdrawal_content']);
            
			$error = false;

            if (ACCOUNT_GENDER == 'true') {
                if (($customer_gender != 'm') && ($customer_gender != 'f')) {
                    $error = true;
                    $messageStack->add('check_withdrawal', ENTRY_GENDER_ERROR);
                }
            }
            if (strlen($customer_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
                $error = true;
                $messageStack->add('check_withdrawal', ENTRY_FIRST_NAME_ERROR);
            }

            if (strlen($customer_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
                $error = true;
                $messageStack->add('check_withdrawal', ENTRY_LAST_NAME_ERROR);
            }
			
			require_once (DIR_FS_INC . 'xtc_validate_email.inc.php');
            if (strlen($customer_email) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
                $error = true;
                $messageStack->add('check_withdrawal', ENTRY_EMAIL_ADDRESS_ERROR);
            } elseif (xtc_validate_email($customer_email) == false) {
                $error = true;
                $messageStack->add('check_withdrawal', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
            }
			
            if (strlen($customer_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
                $error = true;
                $messageStack->add('check_withdrawal', ENTRY_STREET_ADDRESS_ERROR);
            }

            if (strlen($customer_postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
                $error = true;
                $messageStack->add('check_withdrawal', ENTRY_POST_CODE_ERROR);
            }

			if (strlen($customer_city) < ENTRY_CITY_MIN_LENGTH) {
                $error = true;
                $messageStack->add('check_withdrawal', ENTRY_CITY_ERROR);
            }
			
            if (is_numeric($customer_country) == false) {
                $error = true;
                $messageStack->add('check_withdrawal', ENTRY_COUNTRY_ERROR);
            }
			
			if (ENTRY_DOB_MIN_LENGTH > 0 && $delivery_date != '') {
				if (checkdate(substr(xtc_date_raw($delivery_date), 4, 2), substr(xtc_date_raw($delivery_date), 6, 2), substr(xtc_date_raw($delivery_date), 0, 4)) == false) {
					$error = true;
					$messageStack->add('check_withdrawal', ENTRY_DATE_OF_DELIVERY_DATE_ERROR);
				}
			}
			
			if (ENTRY_DOB_MIN_LENGTH > 0 && $order_date != '') {
				if (checkdate(substr(xtc_date_raw($order_date), 4, 2), substr(xtc_date_raw($order_date), 6, 2), substr(xtc_date_raw($order_date), 0, 4)) == false) {
					$error = true;
					$messageStack->add('check_withdrawal', ENTRY_DATE_OF_ORDER_DATE_ERROR);
				}
			}
			
			if (ENTRY_DOB_MIN_LENGTH > 0 && $withdrawal_date != '') {
				if (checkdate(substr(xtc_date_raw($withdrawal_date), 4, 2), substr(xtc_date_raw($withdrawal_date), 6, 2), substr(xtc_date_raw($withdrawal_date), 0, 4)) == false) {
					$error = true;
					$messageStack->add('check_withdrawal', ENTRY_DATE_OF_WITHDRAWAL_DATE_ERROR);
				}
			}
			
			if ($error == false) {
                $sql_data_array = array(
					'order_id' => $order_id,
                    'customer_id' => $customer_id,
                    'customer_gender' => $customer_gender,
                    'customer_firstname' => $customer_firstname,
                    'customer_lastname' => $customer_lastname,
                    'customer_street_address' => $customer_street_address,
                    'customer_postcode' => $customer_postcode,
                    'customer_city' => $customer_city,
                    'customer_country' => $customer_country,
                    'customer_email' => $customer_email,
                    'order_date' => $order_date,
                    'delivery_date' => $delivery_date,
                    'withdrawal_date' => $withdrawal_date,
                    'withdrawal_content' => $withdrawal_content,
                    'date_created' => 'now()',
                    'created_by_admin' => '0');
					
				xtc_db_perform(TABLE_WITHDRAWAL, $sql_data_array);
			
			$smarty->assign('CUSTOMER_GENDER', $customer_gender);
			$smarty->assign('CUSTOMER_NAME', $customer_firstname . ' ' . $customer_lastname);
			$smarty->assign('CUSTOMER_STREET_ADDRESS', $customer_street_address);
			$smarty->assign('CUSTOMER_POSTCODE', $customer_postcode);
			$smarty->assign('CUSTOMER_CITY', $customer_city);
			$smarty->assign('CUSTOMER_COUNTRY', $customer_country);
			$smarty->assign('ORDER_DATE', $order_date);
			$smarty->assign('DELIVERY_DATE', $delivery_date);
			$smarty->assign('WITHDRAWAL_DATE', $withdrawal_date);
			$smarty->assign('WITHDRAWAL_CONTENT', $withdrawal_content);
			
			require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
			$smarty->caching = false;
			$html_mail = $smarty->fetch('html:withdrawal');
			$html_mail .= $signatur_html;
			$smarty->caching = false;
			$txt_mail = $smarty->fetch('txt:withdrawal');
			$txt_mail .= $signatur_text;
			require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
			$mail_data = cseo_get_mail_data('withdrawal');
			$email_change_subject = str_replace('{$nr}', $order_id, $mail_data['EMAIL_SUBJECT']);

			xtc_php_mail($mail_data['EMAIL_ADDRESS'], 
						$mail_data['EMAIL_ADDRESS_NAME'], 
						$customer_email, 
						$customer_firstname . $customer_lastname, 
						$mail_data['EMAIL_FORWARD'], 
						$mail_data['EMAIL_REPLAY_ADDRESS'], 
						$mail_data['EMAIL_REPLAY_ADDRESS_NAME'], 
						'', 
						'', 
						$email_change_subject, 
						$html_mail, 
						$txt_mail);

			if (!isset($mail_error)) {
				xtc_redirect(xtc_href_link(FILENAME_WITHDRAWAL, 'action=success', 'SSL'));
			} else {
				echo $mail_error;
			}
			
			}
        }
		if ($messageStack->size('check_withdrawal') > 0) {
			return $messageStack->output('check_withdrawal');
		}
	}
}
