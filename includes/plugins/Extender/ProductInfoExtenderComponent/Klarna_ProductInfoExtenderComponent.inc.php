<?php

class Klarna_ProductInfoExtenderComponent extends Klarna_ProductInfoExtenderComponent_parent {
	function proceed() {
		$product = new product($this->v_data_array['products_id']);
		require_once DIR_FS_CATALOG . 'includes/classes/class.klarna.php';
		$klarna = new GMKlarna();
		$xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
		$products_price = $xtPrice->xtcGetPrice($product->data['products_id'], $format = true, 1, $product->data['products_tax_class_id'], $product->data['products_price'], 1);
		$this->v_output_buffer['KLARNA_WIDGET'] = $klarna->getWidgetCode($products_price['plain'], 'product');
		parent::proceed();
	}
}
