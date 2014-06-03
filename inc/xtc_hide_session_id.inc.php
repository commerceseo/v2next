<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_hide_session_id.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// include needed functions
// require_once(DIR_FS_INC . 'xtc_draw_hidden_field.inc.php');
// Hide form elements
function xtc_hide_session_id() {
    global $session_started;

    if (($session_started == true) && defined('SID') && xtc_not_null(SID)) {
        return xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
    }
}
