<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_db.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
    global $$link;

    if (USE_PCONNECT == 'true') {
        $$link = mysql_pconnect($server, $username, $password);
    } else {
        $$link = mysql_connect($server, $username, $password);
    }

    if ($$link) {
        mysql_select_db($database) or die('Datenbank nicht erreichbar!');
    }

    if (!defined('DB_SERVER_CHARSET')) {
        define('DB_SERVER_CHARSET', 'utf8');
    }

    if (function_exists('mysql_set_charset') == true) {
        mysql_set_charset(DB_SERVER_CHARSET);
    } else {
        mysql_query('SET NAMES ' . DB_SERVER_CHARSET);
    }

    return $$link;
}

function xtc_db_close($link = 'db_link') {
    global $$link;
    return mysql_close($$link);
}

function xtc_db_error($errno, $errstr, $errfile, $errline) {

    switch ($errno) {
        case E_USER_ERROR:
            if ($errstr == "(SQL)") {
                // handling an sql error
                // if ($_SESSION['customers_status']['customers_status_id'] == 0) {
                    echo "<b>SQL Fehler</b> [$errno] " . SQLMESSAGE . "<br /><br />\n\n";
                    echo "<b>Query:</b> " . SQLQUERY . "<br /><br />\n\n";
                    echo "Beim Aufruf der Datei <em>" . SQLERRORFILE . "</em> ";
                    echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br /><br />\n\n";
                // }
                xtc_db_query("REPAIR TABLE sessions");
                xtc_db_query("REPAIR TABLE whos_online");
                xtc_db_query("REPAIR TABLE whos_online_month");
                xtc_db_query("REPAIR TABLE whos_online_year");
                echo "<b>Die Abfrage wurde abgebrochen, kontaktieren Sie den Administrator...</b><br /><br />\n\n";
                // Send an email to the shop owner if a sql error occurs
                if (defined('EMAIL_SQL_ERRORS') && EMAIL_SQL_ERRORS == 'true') {
                    $subject = 'Datenbankfehler - ' . STORE_NAME;
                    $message = '<font color="#000000"><strong>' . $errno . ' - ' . SQLMESSAGE . '<br /><br />' . SQLQUERY . '<br /><br />Request URL: ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '<br /><br /><small><font color="#ff0000">[CSEO SQL Error]</font></small><br /><br /></strong></font>';
                    require_once (DIR_FS_INC . 'xtc_php_mail.inc.php');
                    xtc_php_mail(STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '', '', STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER, '', '', $subject, nl2br($message), $message);
                }
            } else {
                echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
                echo "  Fatal error on line $errline in file $errfile";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                echo "<b>Die Abfrage wurde abgebrochen, kontaktieren Sie den Administrator...</b><br />\n";
            }
            exit(1);
            break;

        case E_USER_WARNING:
        case E_USER_NOTICE:
    }
    /* Don't execute PHP internal error handler */
    return true;
}

function sqlerrorhandler($ERROR, $QUERY, $PHPFILE, $LINE) {
    define("SQLQUERY", $QUERY);
    define("SQLMESSAGE", $ERROR);
    define("SQLERRORLINE", $LINE);
    define("SQLERRORFILE", $PHPFILE);
    trigger_error("(SQL)", E_USER_ERROR);
}

set_error_handler("xtc_db_error");

/**
 * Bereitet Daten fuer die Datenbank auf.
 * 
 * @param $table string: Die Ziel-Tabelle.
 * @param $data mixed: Die zu verarbeitende Daten.
 * @param $action string: Die auszufuehrende Aktion.
 * @param $parameters string: ...
 * @param $link string: Der Datenbanklink
 * @return xtc_db_query($query, $link)
 */
function xtc_db_perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link') {
    reset($data);
    $action = strtolower($action);
    if ($action == 'insert') {
        $query = 'insert into ' . $table . ' (';
        while (list($columns, ) = each($data)) {
            $query .= $columns . ', ';
        }
        $query = substr($query, 0, -2) . ') values (';
        reset($data);
        while (list(, $value) = each($data)) {
            $value = (is_Float($value) & PHP4_3_10) ? sprintf("%.F", $value) : (string) ($value);
            switch ($value) {
                case 'now()':
                    $query .= 'now(), ';
                    break;
                case 'null':
                    $query .= 'null, ';
                    break;
                default:
                    $query .= '\'' . xtc_db_input($value) . '\', ';
                    break;
            }
        }
        $query = substr($query, 0, -2) . ')';
    } elseif ($action == 'update') {
        $query = 'update ' . $table . ' set ';
        while (list($columns, $value) = each($data)) {
            $value = (is_Float($value) & PHP4_3_10) ? sprintf("%.F", $value) : (string) ($value);
            switch ($value) {
                case 'now()':
                    $query .= $columns . ' = now(), ';
                    break;
                case 'null':
                    $query .= $columns .= ' = null, ';
                    break;
                default:
                    $query .= $columns . ' = \'' . xtc_db_input($value) . '\', ';
                    break;
            }
        }
        $query = substr($query, 0, -2) . ' where ' . $parameters;
    }

    return xtc_db_query($query, $link);
}

function xtc_db_query($query, $link = 'db_link') {
    global $$link;

    if (STORE_DB_TRANSACTIONS == 'true') {
        error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    $result = @mysql_query($query, $$link) or sqlerrorhandler("(" . mysql_errno() . ") " . mysql_error(), $query, ($_REQUEST['linkurl'] != '' ? $_REQUEST['linkurl'] : $_SERVER['PHP_SELF']), __LINE__);

    if (STORE_DB_TRANSACTIONS == 'true') {
        $result_error = mysql_error();
        error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }
    return $result;
}

function xtc_db_queryCached($query, $link = 'db_link') {
    global $$link;

    // get HASH ID for filename
    $id = md5($query);
    // cache File Name
    $file = SQL_CACHEDIR . $id . '.xtc';
    // file life time
    $expire = DB_CACHE_EXPIRE;

    if (STORE_DB_TRANSACTIONS == 'true') {
        error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    if (file_exists($file) && filemtime($file) > (time() - $expire)) {
        // get cached resulst
        $result = unserialize(implode('', file($file)));
    } else {
        if (file_exists($file)) {
            @unlink($file);
        }

        // get result from DB and create new file
        $result = mysql_query($query, $$link) or xtc_db_error($query, mysql_errno(), mysql_error());

        if (STORE_DB_TRANSACTIONS == 'true') {
            $result_error = mysql_error();
            error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
        }

        // fetch data into array
        while ($record = xtc_db_fetch_array($result)) {
            $records[] = $record;
        }
        // safe result into file.
        $stream = serialize($records);
        $fp = fopen($file, "w");
        fwrite($fp, $stream);
        fclose($fp);
        $result = unserialize(implode('', file($file)));
    }

    return $result;
}

function xtc_db_fetch_array(&$db_query, $cq = false) {
    if (DB_CACHE == 'true' && $cq) {
        if (!count($db_query))
            return false;
        $curr = current($db_query);
        next($db_query);
        return $curr;
    } else {
        if (is_array($db_query)) {
            $curr = current($db_query);
            next($db_query);
            return $curr;
        }
        return mysql_fetch_array($db_query, MYSQL_ASSOC);
    }
}

function xtc_db_num_rows($db_query, $cq = false) {
    if (DB_CACHE == 'true' && $cq) {
        if (!count($db_query)) {
            return false;
        }
        return count($db_query);
    } else {
        if (!is_array($db_query)) {
            return mysql_num_rows($db_query);
        }
    }
}

function xtc_db_data_seek($db_query, $row_number, $cq = false) {
    if (DB_CACHE == 'true' && $cq) {
        if (!count($db_query)) {
            return;
        }
        return $db_query[$row_number];
    } else {
        if (!is_array($db_query)) {
            return mysql_data_seek($db_query, $row_number);
        }
    }
}

function xtc_db_insert_id() {
    return mysql_insert_id();
}

function xtc_db_free_result($db_query) {
    return mysql_free_result($db_query);
}

function xtc_db_fetch_fields($db_query) {
    return mysql_fetch_field($db_query);
}

function xtc_db_fetch_object($db_query) {
    return mysql_fetch_object($db_query);
}

function xtc_db_output($string) {
    return htmlspecialchars($string);
}

function xtc_db_input($string, $link = 'db_link') {
    global $$link;

    if (function_exists('mysql_real_escape_string')) {
        return mysql_real_escape_string($string, $$link);
    } elseif (function_exists('mysql_escape_string')) {
        return mysql_escape_string($string);
    }

    return addslashes($string);
}

function xtc_db_prepare_input($string) {
    if (is_string($string)) {
        $string = stripslashes($string);
        $string = preg_replace('/union.*select.*from/i', '', $string);
        return trim($string);
    } elseif (is_array($string)) {
        reset($string);
        while (list($key, $value) = each($string)) {
            $string[$key] = xtc_db_prepare_input($value);
        }
        return $string;
    } else {
        return $string;
    }
}

function xtc_db_fetch_row($string) {
    return mysql_fetch_row($string);
}
