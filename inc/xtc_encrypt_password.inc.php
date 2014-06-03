<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_encrypt_password.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// This function makes a new password from a plaintext password. 
function xtc_encrypt_password($plain) {
    if (ACCOUNT_PASSWORD_SECURITY == 'false') {
        $password = md5($plain);
    } else {
        $password = sha1($plain . SALT_KEY);
    }

    return $password;
}
