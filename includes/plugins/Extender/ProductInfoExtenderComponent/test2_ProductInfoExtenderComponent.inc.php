<?php

class test2_ProductInfoExtenderComponent extends test2_ProductInfoExtenderComponent_parent {
	function proceed() {
		//Demo Products ID
		$this->v_output_buffer['PRODUCTS_ID'] = $this->v_data_array['products_id'];
		parent::proceed();
	}

}
