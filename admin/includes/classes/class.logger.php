<?php

/* -----------------------------------------------------------------
 * 	$Id: class.logger.php 420 2013-06-19 18:04:39Z akausch $
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

class logger_ORIGINAL {

    var $timer_start, $timer_stop, $timer_total;

    // function __construct() {
    // }

    function logger_ORIGINAL() {
        $this->timer_start();
    }

    function timer_start() {
        if (defined("PAGE_PARSE_START_TIME")) {
            $this->timer_start = PAGE_PARSE_START_TIME;
        } else {
            $this->timer_start = microtime();
        }
    }

    function timer_stop($display = 'false') {
        $this->timer_stop = microtime();

        $time_start = explode(' ', $this->timer_start);
        $time_end = explode(' ', $this->timer_stop);

        $this->timer_total = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

        $this->write($_SERVER['REQUEST_URI'], $this->timer_total . 's');

        if ($display == 'true') {
            return $this->timer_display();
        }
    }

    function timer_display() {
        return '<span class="smallText">Parse Time: ' . $this->timer_total . 's</span>';
    }

    function write($message, $type) {
        error_log(strftime(STORE_PARSE_DATE_TIME_FORMAT) . ' [' . $type . '] ' . $message . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

}
