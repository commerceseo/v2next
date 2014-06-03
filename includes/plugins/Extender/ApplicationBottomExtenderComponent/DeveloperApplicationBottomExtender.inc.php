<?php
/* --------------------------------------------------------------
   GPrintApplicationBottomExtender.inc.php 2012-05-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class DeveloperApplicationBottomExtender extends DeveloperApplicationBottomExtender_parent
{
	function proceed() {
		if (USE_TEMPLATE_DEVMODE == 'true') {
			echo '<pre>GET:<br>';
			print_r ($this->v_data_array['GET']);
			echo '</pre>';
			echo '<pre>POST:<br>';
			print_r($this->v_data_array['POST']);
			echo '</pre>';
			echo '<br>cPath: '.$this->v_data_array['cPath'];
			echo '<br>products_id: '. $this->v_data_array['products_id'];
		}
		parent::proceed();
	}
}
?>