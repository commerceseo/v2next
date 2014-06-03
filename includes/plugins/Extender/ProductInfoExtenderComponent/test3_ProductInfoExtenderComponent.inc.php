<?php

class test3_ProductInfoExtenderComponent extends test3_ProductInfoExtenderComponent_parent {
	function proceed() {
		//Demo Products Status
		$product = new product($this->v_data_array['products_id']);
		$this->v_output_buffer['PRODUCTS_ORDERED'] = $product->data['products_ordered'];
		parent::proceed();
	}

}
