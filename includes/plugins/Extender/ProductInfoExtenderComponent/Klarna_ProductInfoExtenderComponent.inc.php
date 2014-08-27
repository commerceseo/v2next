<?php

class Klarna_ProductInfoExtenderComponent extends Klarna_ProductInfoExtenderComponent_parent {
	function proceed() {
		$product = new product($this->v_data_array['products_id']);
		require_once DIR_FS_CATALOG . 'includes/classes/class.klarna.php';
		$klarna = new GMKlarna();
		$xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
		$currency = $_SESSION['currency'];
		$currencies = $xtPrice->currencies;
		$tax = ($xtPrice->TAX[$product->data['products_tax_class_id']]/100)+1;
		$totalSum = $product->data['products_price']*$tax*$currencies[$currency]['value'];
		$this->v_output_buffer['KLARNA_WIDGET'] = $klarna->getWidgetCode($totalSum, true);
		parent::proceed();
	}
}
