<?php

/* --------------------------------------------------------------
  cseo_check_data_type.inc.php 2011-07-25 mb
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2011 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

function cseo_check_data_type($p_data, $p_type, $p_strict = false, $p_error_level = E_USER_WARNING) {
    switch (strtolower($p_type)) {
        case 'int':
            if ($p_strict && is_int($p_data)) {
                return true;
            } elseif (!$p_strict && is_numeric($p_data) && (int) $p_data == (double) $p_data) {
                return true;
            } else {
                trigger_error('cseo_check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', ' . $p_type . ' expected, but ' . gettype($p_data) . ' detected', $p_error_level);
                return false;
            }
            break;
        case 'double':
            if ($p_strict && is_float($p_data)) {
                return true;
            } elseif (!$p_strict && is_numeric($p_data)) {
                return true;
            } else {
                trigger_error('cseo_check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', ' . $p_type . ' expected, but ' . gettype($p_data) . ' detected', $p_error_level);
                return false;
            }
            break;
        case 'string':
            if (is_string($p_data)) {
                return true;
            } else {
                trigger_error('cseo_check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', ' . $p_type . ' expected, but ' . gettype($p_data) . ' detected', $p_error_level);
                return false;
            }
        case 'array':
            if (is_array($p_data)) {
                return true;
            } else {
                trigger_error('cseo_check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', ' . $p_type . ' expected, but ' . gettype($p_data) . ' detected', $p_error_level);
                return false;
            }
        case 'bool':
            if (is_bool($p_data)) {
                return true;
            } else {
                trigger_error('cseo_check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', ' . $p_type . ' expected, but ' . gettype($p_data) . ' detected', $p_error_level);
                return false;
            }
        case 'object':
            if (is_object($p_data)) {
                return true;
            } else {
                trigger_error('cseo_check_data_type validation failed. Dump: ' . print_r($p_data, true) . ', ' . $p_type . ' expected, but ' . gettype($p_data) . ' detected', $p_error_level);
                return false;
            }
        default:
            trigger_error('cseo_check_data_type validation failed. Unknown data type: ' . $p_type, E_USER_ERROR);
    }
}
