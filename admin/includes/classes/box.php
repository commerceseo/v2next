<?php

/* -----------------------------------------------------------------
 * 	$Id: box.php 420 2013-06-19 18:04:39Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class box extends tableBlock {

    // function __construct() {
    // }

    function box() {
        $this->heading = array();
        $this->contents = array();
    }

    function infoBox($heading, $contents) {
        $this->heading = $this->tableBlockHead($heading);
        $this->contents = $this->tableBlockContent($contents);

        return $this->heading . $this->contents;
    }

    function menuBox($heading, $contents) {
        $this->table_data_parameters = 'class="menuBoxHeading"';
        if ($heading[0]['link']) {
            $this->table_data_parameters .= ' onclick="document.location.href=\'' . $heading[0]['link'] . '\'"';
            $heading[0]['text'] = '&nbsp;<a href="' . $heading[0]['link'] . '" class="menuBoxHeadingLink">' . $heading[0]['text'] . '</a>&nbsp;';
        } else {
            $heading[0]['text'] = '&nbsp;' . $heading[0]['text'] . '&nbsp;';
        }
        $this->heading = $this->tableBlock($heading);

        $this->table_data_parameters = 'class="menuBoxContent"';
        $this->contents = $this->tableBlock($contents);

        return $this->heading . $this->contents;
    }

}
