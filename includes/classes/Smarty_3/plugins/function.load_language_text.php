<?php
/* --------------------------------------------------------------
   function.object_product_list.php 2010-09-30 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_load_language_text($params, &$smarty) {
	# select section
	$t_section = array();
	if(isset($params['section'])) $t_section = $params['section'];

	# set array name
	$_text_array_name = 'txt';
	if(isset($params['name'])) $_text_array_name = $params['name'];

	# select language
	$t_language_id = $_SESSION['languages_id'];
	$coo_text_mgr = cseohookfactory::create_object('LanguageTextManager', array($t_section, $t_language_id), false);
	$t_section_array = $coo_text_mgr->get_section_array();
    $smarty->assign($_text_array_name, $t_section_array);
}
