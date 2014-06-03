<?php
/* --------------------------------------------------------------
   debug_config.inc.php 2011-01-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_debug_config = array
(
	'ENABLED_SOURCES' => array()
);

$t_debug_config['ENABLED_SOURCES'][] = 'notice';
$t_debug_config['ENABLED_SOURCES'][] = 'warning';
$t_debug_config['ENABLED_SOURCES'][] = 'error';
$t_debug_config['ENABLED_SOURCES'][] = 'security';

$t_debug_config['ENABLED_SOURCES'][] = 'smarty_compile_check';
$t_debug_config['ENABLED_SOURCES'][] = 'print_sql_on_error';
$t_debug_config['ENABLED_SOURCES'][] = 'uncompressed_js';
$t_debug_config['ENABLED_SOURCES'][] = 'class_overloading';
$t_debug_config['ENABLED_SOURCES'][] = 'include_usermod_requested';
$t_debug_config['ENABLED_SOURCES'][] = 'include_usermod_found';



?>