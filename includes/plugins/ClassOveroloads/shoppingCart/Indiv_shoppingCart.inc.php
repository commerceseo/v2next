<?php

class Indiv_shoppingCart extends shoppingCart_ORIGINAL {
		function count_contents() { // get total number of items in cart
		$total_items = 0;
		if (is_array($this->contents)) {
			reset($this->contents);
			while (list ($products_id,) = each($this->contents)) {
				$total_items += $this->get_quantity($products_id);
			}
		}
		//Änderung Indiv-Style.de
		$_SESSION['wk_summe'] = $total_items;
		//Änderung Indiv-Style.de Ende

		return $total_items;
	}

}

?>
