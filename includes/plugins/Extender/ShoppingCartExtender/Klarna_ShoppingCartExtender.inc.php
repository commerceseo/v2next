<?php

class Klarna_ShoppingCartExtender extends Klarna_ShoppingCartExtender_parent {
	function proceed() {
		require_once DIR_FS_CATALOG . 'includes/classes/class.klarna.php';
		$klarna = new GMKlarna();
		$klarna_widget = $klarna->getWidgetCode($_SESSION['cart']->show_total());
		$this->v_output_buffer['KLARNA_WIDGET'] = $klarna_widget;
		// echo $klarna_widget;
		parent::proceed();
	}
}
