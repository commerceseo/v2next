<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_dbv7.inc.php 661 2015-10-22 15:57:57Z akausch $
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

function xtc_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $socket = DB_SOCKET) {
    global $link, $db_error;
	// echo $socket;
	$db_error = false;
    if (!$server) {
      $db_error = 'No Server selected.';
      return false;
    }
    if (!$username) {
      $db_error = 'No User selected.';
      return false;
    }
	if ($socket == '') {
		$link = mysqli_connect($server, $username, $password, $database);
	} else {
		$link = mysqli_connect($server, $username, $password, $database, null, $socket);
	}
    if ($link) {
        mysqli_select_db($link, $database) or $db_error = mysqli_error($link);
    }
    if (!defined('DB_SERVER_CHARSET')) {
        define('DB_SERVER_CHARSET', 'utf8');
    }
    if (function_exists('mysqli_set_charset') == true) {
        mysqli_set_charset($link, DB_SERVER_CHARSET);
    } else {
        mysqli_query('SET NAMES ' . DB_SERVER_CHARSET);
    }
    return $link;
}

function xtc_db_close() {
    global $link;
    return mysqli_close($link);
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
                // }
                xtc_db_query("REPAIR TABLE sessions");
                xtc_db_query("REPAIR TABLE whos_online");
                xtc_db_query("REPAIR TABLE whos_online_month");
                xtc_db_query("REPAIR TABLE whos_online_year");
                echo "<b>Die Abfrage wurde abgebrochen, kontaktieren Sie den Administrator...</b><br /><br />\n\n";
            } else {
                echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
                echo "  Fatal error on line $errline in file $errfile";
                echo "<br /><b>Die Abfrage wurde abgebrochen, kontaktieren Sie den Administrator...</b><br />\n";
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
    trigger_error("(SQL) Query:" . $QUERY . "-File: " . $PHPFILE . "-Line: " . $LINE, E_USER_ERROR);
}

set_error_handler("xtc_db_error");

function xtc_db_perform($p_table, $p_data_array = array(), $p_action = 'insert', $p_parameters = '', $p_link = 'db_link', $p_quoted_values = true) {
    $t_result = false;
    $t_quote = '';
    if ($p_quoted_values) {
        $t_quote = '\'';
    }

    reset($p_data_array);

    switch ($p_action) {
        case 'insert':
            $t_sql = 'INSERT INTO ' . $p_table . ' (';

            while (list($t_columns, ) = each($p_data_array)) {
                $t_sql .= $t_columns . ', ';
            }

            $t_sql = substr($t_sql, 0, -2) . ') VALUES (';

            reset($p_data_array);

            while (list(, $t_value) = each($p_data_array)) {
                $t_value = (is_Float($t_value) & PHP4_3_10) ? sprintf("%.F", $t_value) : (string) ($t_value);

                switch ($t_value) {
                    case 'now()':
                        $t_sql .= 'NOW(), ';
                        break;
                    case 'null':
                        $t_sql .= 'NULL, ';
                        break;
                    default:
                        if ($p_quoted_values) {
                            $t_value = xtc_db_input($t_value);
                        }
                        $t_sql .= $t_quote . $t_value . $t_quote . ', ';
                        break;
                }
            }

            $t_sql = substr($t_sql, 0, -2) . ')';

            break;

        case 'replace':
            $t_sql = 'REPLACE INTO ' . $p_table . ' (';

            while (list($t_columns, ) = each($p_data_array)) {
                $t_sql .= $t_columns . ', ';
            }

            $t_sql = substr($t_sql, 0, -2) . ') VALUES (';

            reset($p_data_array);

            while (list(, $t_value) = each($p_data_array)) {
                $t_value = (is_Float($t_value) & PHP4_3_10) ? sprintf("%.F", $t_value) : (string) ($t_value);

                switch ($t_value) {
                    case 'now()':
                        $t_sql .= 'NOW(), ';
                        break;
                    case 'null':
                        $t_sql .= 'NULL, ';
                        break;
                    default:
                        if ($p_quoted_values) {
                            $t_value = xtc_db_input($t_value);
                        }
                        $t_sql .= $t_quote . $t_value . $t_quote . ', ';
                        break;
                }
            }

            $t_sql = substr($t_sql, 0, -2) . ')';

            break;

        case 'update':
            $t_sql = 'UPDATE ' . $p_table . ' SET ';

            while (list($t_columns, $t_value) = each($p_data_array)) {
                $t_value = (is_Float($t_value) & PHP4_3_10) ? sprintf("%.F", $t_value) : (string) ($t_value);

                switch ($t_value) {
                    case 'now()':
                        $t_sql .= $t_columns . ' = NOW(), ';
                        break;
                    case 'null':
                        $t_sql .= $t_columns . ' = NULL, ';
                        break;
                    default:
                        if ($p_quoted_values) {
                            $t_value = xtc_db_input($t_value);
                        }
                        $t_sql .= $t_columns . ' = ' . $t_quote . $t_value . $t_quote . ', ';
                        break;
                }
            }

            $t_sql = substr($t_sql, 0, -2) . ' WHERE ' . $p_parameters;

            break;

        case 'delete':
            $t_sql = 'DELETE FROM ' . $p_table . ' WHERE ' . $p_parameters;

            break;
    }

    if (empty($t_sql) == false) {
        $t_result = xtc_db_query($t_sql, $p_link);
    }

    return $t_result;
}

// function xtc_db_query($query) {
function xtc_db_query($query, $link = 'db_link', $p_enable_data_cache=true, $p_enable_logging = true) {
    global $link;
	if($p_enable_logging) {
		if(file_exists(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/system/core/logging')) {
			require_once(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/system/core/logging/LogControl.inc.php');
		}
		$coo_logger = LogControl::get_instance();
		$t_is_shop = false;
		if($coo_logger->is_shop_environment()) {
			$t_is_shop = true;
			$coo_stop_watch = $coo_logger->get_stop_watch();
			$coo_stop_watch->start('sql_queries');
		}
	}
	if(defined('APPLICATION_RUN_MODE') && APPLICATION_RUN_MODE == 'frontend' && $p_enable_data_cache == true) {
		$coo_cache = DataCache::get_instance();
		$t_use_cache = true;
		$t_cache_key = '';
		require_once(DIR_FS_INC.'strtoupper_wrapper.inc.php');
		if(strtoupper_wrapper(substr(ltrim($query), 0, 6)) != 'SELECT') {
			# cache selects only
			$t_use_cache = false;
		} else {
			# use cache, build key
			$t_use_cache = true;
			$t_cache_key = $coo_cache->build_key($query);
		}
		
		if($t_use_cache && $coo_cache->key_exists($t_cache_key)) {
			# use cached result
			$result = $coo_cache->get_data($t_cache_key);
			// @mysql_data_seek($result, 0);
			@mysqli_data_seek($result, 0);
		} else {
			# execute query
			$result = @mysqli_query($link, $query) or sqlerrorhandler("(" . mysqli_errno($link) . ") " . mysqli_error($link), $query, ($_REQUEST['linkurl'] != '' ? $_REQUEST['linkurl'] : $_SERVER['PHP_SELF']), __LINE__);

			# save result to cache
			$coo_cache->set_data($t_cache_key, $result);
		}
	} else {
		# ALL OTHER RUN MODES
		# execute query
		// $result = mysql_query($query, $$link) or xtc_db_error($query, mysql_errno(), mysql_error());		
		$result = @mysqli_query($link, $query) or sqlerrorhandler("(" . mysqli_errno($link) . ") " . mysqli_error($link), $query, ($_REQUEST['linkurl'] != '' ? $_REQUEST['linkurl'] : $_SERVER['PHP_SELF']), __LINE__);
	}
	
    return $result;
}

function xtc_db_queryCached($query) {
    global $link;

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
        $result = mysqli_query($link, $query) or xtc_db_error($query, mysqli_errno($link), mysqli_error($link));

        if (STORE_DB_TRANSACTIONS == 'true') {
            $result_error = mysqli_error($link);
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
        return mysqli_fetch_array($db_query, MYSQLI_ASSOC);
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
            return mysqli_num_rows($db_query);
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
            return mysqli_data_seek($db_query, $row_number);
        }
    }
}

function xtc_db_insert_id() {
    global $link;
    return mysqli_insert_id($link);
}

function xtc_db_free_result($db_query) {
    return mysqli_free_result($db_query);
}

function xtc_db_fetch_fields($db_query) {
    return mysqli_fetch_field($db_query);
}

function xtc_db_fetch_object($db_query) {
    return mysqli_fetch_object($db_query);
}

function xtc_db_output($string) {
    return htmlspecialchars($string);
}

function xtc_db_input($string) {
    global $link;
    if (function_exists('mysqli_real_escape_string')) {
        // mysqli_character_set_name($link);
        // printf("<br>Initial character set: %s\n", mysqli_character_set_name($link));
        return mysqli_real_escape_string($link, $string);
    }
    return addslashes($string);
}

function xtc_db_prepare_input($string) {
    if (is_string($string)) {
        $string = preg_replace('/union.*select.*from/i', '', $string);
        return trim(stripslashes($string));
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
    return mysqli_fetch_row($string);
}
