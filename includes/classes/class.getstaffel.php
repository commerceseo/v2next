<?php
class getGraduatedStaffel_ORIGINAL {
	
	/**
		Staffelpreise Listing
	**/
	
	function getGraduatedStaffel($pid) {
		global $xtPrice;
		$product_staffel_query = xtDBquery("SELECT
											p.products_vpe_status,
											p.products_vpe_value,
											p.products_tax_class_id,
											p.products_vpe 
										FROM
											".TABLE_PRODUCTS." AS p
										WHERE
											p.products_id = '".$pid."'
										".$this->groupCheck().$this->fsk18()."");

		if(xtc_db_num_rows($product_staffel_query, true)) {
			$staffel_vpe = xtc_db_fetch_array($product_staffel_query, true);

			if($_SESSION['customers_status']['customers_status_id'] == '0')
				$gruppe = '1';
			else
				$gruppe = (int)$_SESSION['customers_status']['customers_status_id'];

			$staffel_query = xtDBquery("SELECT * FROM ".TABLE_PERSONAL_OFFERS_BY.$gruppe." WHERE products_id = '".$pid."' AND personal_offer > 0 ORDER BY quantity ASC;");
			$discount = $xtPrice->xtcCheckDiscount($pid);

			$staffel = array ();
			while ($staffel_values = xtc_db_fetch_array($staffel_query, true))
				$staffel[] = array ('stk' => $staffel_values['quantity'], 'price' => $staffel_values['personal_offer']);

			$staffel_data_listing = array ();
			for ($i = 0, $n = sizeof($staffel); $i < $n; $i ++) {
				if ($staffel[$i]['stk'] == 1) {
					$quantity = $staffel[$i]['stk'];
					if ($staffel[$i +1]['stk'] != '')
						$quantity = $staffel[$i]['stk'].'-'. ($staffel[$i +1]['stk'] - 1);
				} else {
					$quantity = ' > '.$staffel[$i]['stk'];
					if ($staffel[$i +1]['stk'] != '')
						$quantity = $staffel[$i]['stk'].'-'. ($staffel[$i +1]['stk'] - 1);
				}
				$vpe = '';
				if ($staffel_vpe['products_vpe_status'] == 1 && $staffel_vpe['products_vpe_value'] != 0.0 && $staffel[$i]['price'] > 0) {
					$vpe = $staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount;
					$vpe = $vpe * (1 / $staffel_vpe['products_vpe_value']);
					$vpe = $xtPrice->xtcFormat($vpe, true, $staffel_vpe['products_tax_class_id']).TXT_PER.xtc_get_vpe_name($staffel_vpe['products_vpe']);
				}
				$staffel_data_listing[$i] = array('QUANTITY' => $quantity, 
													'VPE' => $vpe, 
													'PRICE' => $xtPrice->xtcFormat($staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount, true, $staffel_vpe['products_tax_class_id']));
			}
			return $staffel_data_listing;
		}
	}
	
    function fsk18() {
        if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
            return $fsk_lock = ' AND p.products_fsk18!=1';
    }

    function groupCheck() {
        if (GROUP_CHECK == 'true')
            return " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
    }

}
