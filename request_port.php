<?php
/* --------------------------------------------------------------
   request_port.inc.php 2014-07-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
require('includes/application_top.php');

#error_reporting(E_ALL);
$t_output_content = '';

switch($_GET['module']) {
	case 'properties_combis_status':
		$coo_properties_view = cseohookfactory::create_object('PropertiesView');
		$t_output_content = $coo_properties_view->get_combis_status_json($_GET['products_id'], $_GET['properties_values_ids'], $_GET['need_qty']);
		break;

	case 'properties_combis_status_by_combis_id':
		$coo_properties_view = cseohookfactory::create_object('PropertiesView');
		$t_output_content = $coo_properties_view->get_combis_status_by_combis_id_json($_GET['combis_id'], $_GET['need_qty']);
		break;
	
	default:
		# plugin requests
		$f_module_name = $_GET['module'];

		if(trim($f_module_name) != '')
		{
			$t_class_name_suffix = 'AjaxHandler';
			$coo_request_router = cseohookfactory::create_object('RequestRouter', array($t_class_name_suffix));

			$coo_request_router->set_data('GET', $_GET);
			$coo_request_router->set_data('POST', $_POST);

			$t_proceed_status = $coo_request_router->proceed($f_module_name);
			if($t_proceed_status == true) {
				$t_output_content = $coo_request_router->get_response();
			} else {
				trigger_error('could not proceed module ['.htmlentities_wrapper($f_module_name).']', E_USER_ERROR);
			}
		}
}	

echo $t_output_content;
mysql_close();