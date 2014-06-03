<?php

cseohookfactory::load_class('ExtenderComponent');

class ApplicationBottomExtenderComponent extends ExtenderComponent {
	var $v_page = false;
	
	function init_page() {
		$t_page = '';

		if (strstr($_REQUEST['linkurl'], substr(FILENAME_CHECKOUT, 0, -5)) || strstr($PHP_SELF, substr(FILENAME_CHECKOUT, 0, -5))) {
			$t_page = 'Checkout';
		} elseif (PRODUCT_ID > 0 && strpos($PHP_SELF, FILENAME_SHOPPING_CART) === false) {
			$t_page = 'ProductInfo';
		} elseif(substr_count(strtolower($t_script_name), 'index.php') > 0) {
			$t_page = 'Index';
		} else {
			$t_page = 'unbekannt';
		}
		// echo $t_page;
		$this->v_page = $t_page;
	}
	
	function get_page() {
		return $this->v_page;
	}
	function proceed() {
		$t_page = $this->get_page();
		if($t_page === false) trigger_error('need call of init_page() method before proceed', E_USER_ERROR);
		
		parent::proceed();
	}
}
