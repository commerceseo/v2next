<?php
/**
 * @version $Id: functions.inc.php,v 1.1 2011-07-15 12:33:32 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 * 
 *   TODO:
 *   
 *   	$show geht in rm() nicht :-(
 */

if (!function_exists("array_fill_keys")) {
    function array_fill_keys($array, $values) {
        if(is_array($array)) {
            foreach($array as $key => $value) {
                $arraydisplay[$array[$key]] = $values;
            }
        }
        return $arraydisplay;
    }
}

if (!function_exists('array_combine')) {
  function array_combine($keys, $values) {
    if (count($keys) < 1 || count($keys) != count($values) || !is_array($keys) || !is_array($values)) {
      return false;
    }
    $keys = array_values($keys);
    $values = array_values($values);
    for ($x=0; $x < count($keys); $x++) {
      $return_array[$keys[$x]] = $values[$x];
    }
    return $return_array;
  }
}

if (!function_exists('_debug')) {
  function _debug($obj, $caption=null, $access_level=null) {
    global $logfilename;
    $_caption = $caption;
    if (is_int($_caption)) {
        $caption = array();
    	$caption['Line'] = $_caption;
    }
    if (is_array($caption)) {
        $caption_str = '';
        $sep = '';
    	foreach ($caption as $key=>$value) {
    	    if (! is_int($key)) {
    	    	$caption_str .= $sep . "$key=$value";
    	    } else {
    	        $caption_str .= $sep . "$value";
    	    }
    	    $sep = ', ';
    	}
    	$caption_str .= ':';
    } elseif ($caption) {
        $caption_str = $caption . ':';
    }
    
    if ((isset($_GET['Verbose'])) || 1==2) {
    	//if ($session_started) {
    		echo "<pre>$caption_str ";print_r($obj);echo "</pre>";
    	//}
    }
    if((defined('VARIO_WRITE_LOG') && VARIO_WRITE_LOG == 1) || (isset($_GET['Verbose'])) || 1==2 || access_level == 7) {
        $logfilename = ($logfilename)?$logfilename:time().".log";
        $_filename = DIR_FS_DOCUMENT_ROOT."admin/vario7/logs/$logfilename";

        $log = $caption_str.' ' . print_r($obj, true) . "\r\n";

        if (!$handle = fopen($_filename, 'a')) {
            echo "Cannot open file ($_filename)";
        }

        if (fwrite($handle, $log) === FALSE) {
            echo "Cannot write to file ($_filename)";
        }
        fclose($handle);
    }
  }
}


if (!function_exists('rm')) {
  function rm($fileglob, $show) {
    if (is_string($fileglob)) {
        if (is_file($fileglob)) {
           	// if ($show == '1')
           		print(" ($show) - delete $fileglob<br/>");
            return @unlink($fileglob);
        } else if (is_dir($fileglob)) {
            $ok = rm("$fileglob/*", $date_from, $date_to);
            if (! $ok) {
                return false;
            }
            return rmdir($fileglob);
        } else {
            $matching = glob($fileglob);
            if ($matching === false) {
                trigger_error(sprintf('No files match supplied glob %s', $fileglob), E_USER_WARNING);
                return false;
            }
			// 08.03.08: AB - > 0 abgefr., sonst Warning
			if (sizeof($matching) > 0) {
	            $rcs = array_map('rm', $matching, array_fill(0, sizeof($matching), $date_from), array_fill(0, sizeof($matching), $date_to));
    	        if (in_array(false, $rcs)) {
        	        return false;
            	}
			}
        }
    } else if (is_array($fileglob)) {
        $rcs = array_map('rm', $fileglob, array_fill(0, sizeof($fileglob), $date_from), array_fill(0, sizeof($fileglob), $date_to));
        if (in_array(false, $rcs)) {
            return false;
        }
    } else {
        trigger_error('Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR);
        return false;
    }

    return true;
    
  }
}

?>
