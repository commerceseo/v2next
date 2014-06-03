<?php

/* -----------------------------------------------------------------
 * 	$Id: class.breadcrumb.php 660 2013-10-08 16:53:16Z akausch $
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

class breadcrumb_ORIGINAL {

    var $_trail;

    function breadcrumb_ORIGINAL() {
        $this->reset();
    }

    function reset() {
        $this->_trail = array();
    }

    function add($title, $link = '') {
        $this->_trail[] = array('title' => $title, 'link' => $link);
    }

    function trail($separator = ' - ') {
        $trail_string = '';

        for ($i = 0, $n = sizeof($this->_trail); $i < $n; $i++) {
            if (isset($this->_trail[$i]['link']) && xtc_not_null($this->_trail[$i]['link'])) {
                $trail_string .= '<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . $this->_trail[$i]['link'] . '" itemprop="url"><span itemprop="title">' . $this->_trail[$i]['title'] . '</span></a></span>';
            } else {
                $trail_string .= $this->_trail[$i]['title'];
            }

            if (($i + 1) < $n) {
                $trail_string .= $separator;
            }
        }
        return $trail_string;
    }

}

