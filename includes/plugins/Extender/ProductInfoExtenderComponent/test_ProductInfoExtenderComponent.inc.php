<?php

class test_ProductInfoExtenderComponent extends test_ProductInfoExtenderComponent_parent {
	function proceed() {
		//Demo Products Status
		$product = new product($this->v_data_array['products_id']);
		$this->v_output_buffer['PRODUCTS_STATUS'] = $product->data['products_status'];
		parent::proceed();
	}

}
