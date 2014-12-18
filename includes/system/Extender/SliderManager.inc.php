<?php
/*-----------------------------------------------------------------
* 	$Id: SliderManager.inc.php 945 2014-04-08 13:30:27Z akausch $
* 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ------------------------------------------------------------------
* 	based on:
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/

cseohookfactory::load_class('ExtenderComponent');

class SliderManager extends ExtenderComponent {
	function proceed($sid, $site) {

		$this->v_data_array['language'] = $_SESSION['language'];

		return $this->v_data_array;
	}
}
