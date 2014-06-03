<?php

/* -----------------------------------------------------------------
 * 	$Id: sessions.php 865 2014-03-16 12:44:08Z akausch $
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

@ini_set("session.gc_maxlifetime", 1440);
@ini_set("session.gc_probability", 100);

if (STORE_SESSIONS == 'mysql') {
    if (!$SESS_LIFE = get_cfg_var('session.gc_maxlifetime')) {
        $SESS_LIFE = 1440;
    }

    function _sess_open($save_path, $session_name) {
        return true;
    }

    function _sess_close() {
        return true;
    }

    function _sess_read($key) {
        $value = xtc_db_fetch_array(xtc_db_query("SELECT value FROM " . TABLE_SESSIONS . " WHERE sesskey = '" . xtc_db_input($key) . "' and expiry > '" . time() . "';"));
        if ($value['value']) {
            return base64_decode($value['value']);
        }
        return false;
    }

    function _sess_write($key, $val) {
        global $SESS_LIFE;
        $expiry = time() + $SESS_LIFE;
        $value = base64_encode($val);

        $total = xtc_db_fetch_array(xtc_db_query("SELECT count(*) AS total FROM " . TABLE_SESSIONS . " WHERE sesskey = '" . xtc_db_input($key) . "';"));

        if ($total['total'] > 0) {
            return xtc_db_query("UPDATE " . TABLE_SESSIONS . " SET expiry = '" . $expiry . "', VALUE = '" . $value . "' WHERE sesskey = '" . xtc_db_input($key) . "';");
        } else {
            return xtc_db_query("INSERT INTO " . TABLE_SESSIONS . " VALUES ('" . xtc_db_input($key) . "', '" . $expiry . "', '" . $value . "');");
        }
    }

    function _sess_destroy($key) {
        return xtc_db_query("DELETE FROM " . TABLE_SESSIONS . " WHERE sesskey = '" . xtc_db_input($key) . "';");
    }

    function _sess_gc($maxlifetime) {
        xtc_db_query("DELETE FROM " . TABLE_SESSIONS . " WHERE expiry < '" . time() . "';");

        return true;
    }

    session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
    register_shutdown_function('session_write_close');
}

function xtc_session_start() {
    return session_start();
}

function xtc_session_id($sessid = '') {
    if (!empty($sessid)) {
        return session_id($sessid);
    } else {
        return session_id();
    }
}

function xtc_session_name($name = '') {
    if (!empty($name)) {
        return session_name($name);
    } else {
        return session_name();
    }
}

function xtc_session_close() {
    if (function_exists('session_close')) {
        return session_close();
    }
}

function xtc_session_destroy() {
    return session_destroy();
}

function xtc_session_save_path($path = '') {
    if (!empty($path)) {
        return session_save_path($path);
    } else {
        return session_save_path();
    }
}

function xtc_session_recreate() {
    $session_backup = $_SESSION;
    unset($_COOKIE[xtc_session_name()]);
    xtc_session_destroy();

    if (STORE_SESSIONS == 'mysql') {
        session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
        register_shutdown_function('session_write_close');
    }

    xtc_session_start();

    $_SESSION = $session_backup;
    unset($session_backup);
}
