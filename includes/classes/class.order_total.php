<?php

/* -----------------------------------------------------------------
 * 	$Id: class.order_total.php 872 2014-03-21 14:46:30Z akausch $
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

class order_total_ORIGINAL {

    var $modules;

    function order_total_ORIGINAL($admin = '') {
        if (defined('MODULE_ORDER_TOTAL_INSTALLED') && xtc_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
            $this->modules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);
            $modules = $this->modules;
            sort($modules);
            reset($modules);
			if ($admin != '') {
				$modulepath = DIR_FS_CATALOG . DIR_WS_MODULES;
				$lang_path = DIR_FS_CATALOG . 'lang/';
			} else {
				$lang_path = DIR_WS_LANGUAGES;
				$modulepath = DIR_WS_MODULES;
			}
            while (list (, $value) = each($modules)) {
                require_once ($lang_path . $_SESSION['language'] . '/modules/order_total/' . $value);
				require_once ($modulepath . 'order_total/' . $value);
                $class = substr($value, 0, strrpos($value, '.'));
                $GLOBALS[$class] = new $class ();
            }
            unset($modules);
        }
    }

    function credit_selection() {
        $selection_string = '';
        $class_desc = str_replace(' ', '_', TABLE_HEADING_CREDIT);
        $start_string = '<div class="' . strtolower($class_desc) . '">';
        $close_string = '</div>';
        $credit_class_string = '';
        if (MODULE_ORDER_TOTAL_INSTALLED) {
            $header_string = '<h3>' . TABLE_HEADING_CREDIT . '</h3>' . "\n";
            reset($this->modules);
            $output_string = '';
            while (list (, $value) = each($this->modules)) {
                $class = substr($value, 0, strrpos($value, '.'));
                if ($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class) {
                    $use_credit_string = $GLOBALS[$class]->use_credit_amount();
                    if ($selection_string == '')
                        $selection_string = $GLOBALS[$class]->credit_selection();
                    if (($use_credit_string != '') || ($selection_string != '')) {
                        $output_string = '<strong>' . $GLOBALS[$class]->header . '</strong><br /> ' . $use_credit_string;
                        $output_string .= "\n";
                        $output_string .= $selection_string;
                    }
                }
            }
            if ($output_string != '') {
                $output_string = $start_string . $header_string . $output_string;
                $output_string .= $close_string;
            }
        }
        return $output_string;
    }

    function update_credit_account($i) {
        if (MODULE_ORDER_TOTAL_INSTALLED) {
            reset($this->modules);
            while (list (, $value) = each($this->modules)) {
                $class = substr($value, 0, strrpos($value, '.'));
                if (($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class)) {
                    $GLOBALS[$class]->update_credit_account($i);
                }
            }
        }
    }

    function collect_posts() {
        if (MODULE_ORDER_TOTAL_INSTALLED) {
            reset($this->modules);
            while (list (, $value) = each($this->modules)) {
                $class = substr($value, 0, strrpos($value, '.'));
                if (($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class)) {
                    $post_var = 'c' . $GLOBALS[$class]->code;
                    if ($_POST[$post_var]) {
                        $_SESSION[$post_var] = $_POST[$post_var];
                    }
                    $GLOBALS[$class]->collect_posts();
                }
            }
        }
    }

    function pre_confirmation_check() {
        global $order;
        if (MODULE_ORDER_TOTAL_INSTALLED) {
            $total_deductions = 0;
            reset($this->modules);
            $order_total = $order->info['total'];
            while (list (, $value) = each($this->modules)) {
                $class = substr($value, 0, strrpos($value, '.'));
                $order_total = $this->get_order_total_main($class, $order_total);
                if (($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class)) {
                    $total_deductions = $total_deductions + $GLOBALS[$class]->pre_confirmation_check($order_total);
                    $order_total = $order_total - $GLOBALS[$class]->pre_confirmation_check($order_total);
                }
            }
            if ($order->info['total'] - $total_deductions <= 0) {
                $_SESSION['credit_covers'] = true;
            } else { // belts and suspenders to get rid of credit_covers variable if it gets set once and they put something else in the cart
                unset($_SESSION['credit_covers']);
            }
        }
    }

    function apply_credit() {
        if (MODULE_ORDER_TOTAL_INSTALLED) {
            reset($this->modules);
            while (list (, $value) = each($this->modules)) {
                $class = substr($value, 0, strrpos($value, '.'));
                if (($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class)) {
                    $GLOBALS[$class]->apply_credit();
                }
            }
        }
    }

    function clear_posts() {
        if (MODULE_ORDER_TOTAL_INSTALLED) {
            reset($this->modules);
            while (list (, $value) = each($this->modules)) {
                $class = substr($value, 0, strrpos($value, '.'));
                if (($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class)) {
                    $post_var = 'c' . $GLOBALS[$class]->code;
                    unset($_SESSION[$post_var]);
                }
            }
        }
    }

    function get_order_total_main($class, $order_total) {
        global $credit, $order;
        return $order_total;
    }

    function process() {
        $order_total_array = array();
        if (is_array($this->modules)) {
            reset($this->modules);
            while (list (, $value) = each($this->modules)) {
                $class = substr($value, 0, strrpos($value, '.'));
                if ($GLOBALS[$class]->enabled) {
                    $GLOBALS[$class]->process();

                    for ($i = 0, $n = sizeof($GLOBALS[$class]->output); $i < $n; $i++) {
                        if (xtc_not_null($GLOBALS[$class]->output[$i]['title']) && xtc_not_null($GLOBALS[$class]->output[$i]['text'])) {
                            $order_total_array[] = array('code' => $GLOBALS[$class]->code, 'title' => $GLOBALS[$class]->output[$i]['title'], 'text' => $GLOBALS[$class]->output[$i]['text'], 'value' => $GLOBALS[$class]->output[$i]['value'], 'sort_order' => $GLOBALS[$class]->sort_order);
                        }
                    }
                }
            }
        }

        return $order_total_array;
    }

    function output() {
        $output_string = '';
        if (is_array($this->modules)) {
            reset($this->modules);
            while (list (, $value) = each($this->modules)) {
                $class = substr($value, 0, strrpos($value, '.'));
                if ($GLOBALS[$class]->enabled) {
                    $size = sizeof($GLOBALS[$class]->output);
                    for ($i = 0; $i < $size; $i++) {
                        $output_string .= '<div class="' . $GLOBALS[$class]->code . '">' . $GLOBALS[$class]->output[$i]['title'] . $GLOBALS[$class]->output[$i]['text'] . '</div>';
                    }
                }
            }
        }

        return $output_string;
    }

    function pp_output() {
        $output_string = '';
        if (is_array($this->modules)) {
            reset($this->modules);
            while (list (, $value) = each($this->modules)) {
                $class = substr($value, 0, strrpos($value, '.'));
                if ($GLOBALS[$class]->enabled) {
                    $size = sizeof($GLOBALS[$class]->output);
                    for ($i = 0; $i < $size; $i++) {
                        $output_string[] = array('title' => $GLOBALS[$class]->output[$i]['title'], 'text' => $GLOBALS[$class]->output[$i]['text']);
                    }
                }
            }
        }

        return $output_string;
    }

}
