<?php
class test1_product extends test1_product_parent {

	function buildDataArrayAfter(){
		$product_array_after1 = array('TEST1' => 'Test1',
									'TEST2' => 'Test2'
		);

		return $product_array_after1;
	}

}