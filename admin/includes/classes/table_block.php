<?php

/* -----------------------------------------------------------------
 * 	$Id: table_block.php 420 2013-06-19 18:04:39Z akausch $
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

class tableBlock {

    var $table_border = '0';
    var $table_width = '100%';
    var $table_cellspacing = '0';
    var $table_cellpadding = '2';
    var $table_parameters = '';
    var $table_row_parameters = '';
    var $table_data_parameters = '';

    // function __construct() {
    // }

    function tableBlock($contents) {
        $tableBox_string = '';

        $form_set = false;
        if (isset($contents['form'])) {
            $tableBox_string .= $contents['form'] . "\n";
            $form_set = true;
            xtc_array_shift($contents);
        }

        $tableBox_string .= '<table class="contentTable" border="' . $this->table_border . '" width="' . $this->table_width . '" cellspacing="' . $this->table_cellspacing . '" cellpadding="' . $this->table_cellpadding . '"';
        if ($this->table_parameters != '')
            $tableBox_string .= ' ' . $this->table_parameters;
        $tableBox_string .= '>' . "\n";

        for ($i = 0, $n = sizeof($contents); $i < $n; $i++) {
            $tableBox_string .= '  <tr';
            if ($this->table_row_parameters != '')
                $tableBox_string .= ' ' . $this->table_row_parameters;
            if (isset($contents[$i]['params']))
                $tableBox_string .= ' ' . $contents[$i]['params'];
            $tableBox_string .= '>' . "\n";
            if (!isset($contents[$i][0]))
                $contents[$i][0] = '';
            if (is_array($contents[$i][0])) {
                for ($x = 0, $y = sizeof($contents[$i]); $x < $y; $x++) {
                    if ($contents[$i][$x]['text']) {
                        $tableBox_string .= '    <td ';
                        if ($contents[$i][$x]['align'] != '')
                            $tableBox_string .= ' align="' . $contents[$i][$x]['align'] . '"';
                        if ($contents[$i][$x]['params']) {
                            $tableBox_string .= ' ' . $contents[$i][$x]['params'];
                        } elseif ($this->table_data_parameters != '') {
                            $tableBox_string .= ' ' . $this->table_data_parameters;
                        }
                        $tableBox_string .= '>';
                        if ($contents[$i][$x]['form'])
                            $tableBox_string .= $contents[$i][$x]['form'];
                        $tableBox_string .= $contents[$i][$x]['text'];
                        if ($contents[$i][$x]['form'])
                            $tableBox_string .= '</form>';
                        $tableBox_string .= '</td>' . "\n";
                    }
                }
            } else {
                $tableBox_string .= '    <td ';
                if (!isset($contents[$i]['align']))
                    $contents[$i]['align'] = '';
                if ($contents[$i]['align'] != '')
                    $tableBox_string .= ' align="' . $contents[$i]['align'] . '"';
                if (isset($contents[$i]['params'])) {
                    $tableBox_string .= ' ' . $contents[$i]['params'];
                } elseif ($this->table_data_parameters != '') {
                    $tableBox_string .= ' ' . $this->table_data_parameters;
                }
                $tableBox_string .= '>' . $contents[$i]['text'] . '</td>' . "\n";
            }

            $tableBox_string .= '  </tr>' . "\n";
        }

        $tableBox_string .= '</table>' . "\n";

        if ($form_set)
            $tableBox_string .= '</form>' . "\n";

        return $tableBox_string;
    }

    function tableBlockContent($contents) {
        $tableBox_string = '';

        $form_set = false;
        if (isset($contents['form'])) {
            $tableBox_string .= $contents['form'] . "\n";
            $form_set = true;
            xtc_array_shift($contents);
        }

        $tableBox_string .= '<table class="boxTableContent" border="' . $this->table_border . '" width="' . $this->table_width . '" cellspacing="' . $this->table_cellspacing . '" cellpadding="' . $this->table_cellpadding . '"';
        if ($this->table_parameters != '')
            $tableBox_string .= ' ' . $this->table_parameters;
        $tableBox_string .= '>' . "\n";

        for ($i = 0, $n = sizeof($contents); $i < $n; $i++) {
            $tableBox_string .= '  <tr';
            if ($this->table_row_parameters != '')
                $tableBox_string .= ' ' . $this->table_row_parameters;
            if (isset($contents[$i]['params']))
                $tableBox_string .= ' ' . $contents[$i]['params'];
            $tableBox_string .= '>' . "\n";
            if (!isset($contents[$i][0]))
                $contents[$i][0] = '';
            if (is_array($contents[$i][0])) {
                for ($x = 0, $y = sizeof($contents[$i]); $x < $y; $x++) {
                    if ($contents[$i][$x]['text']) {
                        $tableBox_string .= '    <td ';
                        if ($contents[$i][$x]['align'] != '')
                            $tableBox_string .= ' align="' . $contents[$i][$x]['align'] . '"';
                        if ($contents[$i][$x]['params']) {
                            $tableBox_string .= ' ' . $contents[$i][$x]['params'];
                        } elseif ($this->table_data_parameters != '') {
                            $tableBox_string .= ' ' . $this->table_data_parameters;
                        }
                        $tableBox_string .= '>';
                        if ($contents[$i][$x]['form'])
                            $tableBox_string .= $contents[$i][$x]['form'];
                        $tableBox_string .= $contents[$i][$x]['text'];
                        if ($contents[$i][$x]['form'])
                            $tableBox_string .= '</form>';
                        $tableBox_string .= '</td>' . "\n";
                    }
                }
            } else {
                $tableBox_string .= '    <td ';
                if (!isset($contents[$i]['align']))
                    $contents[$i]['align'] = '';
                if ($contents[$i]['align'] != '')
                    $tableBox_string .= ' align="' . $contents[$i]['align'] . '"';
                if (isset($contents[$i]['params'])) {
                    $tableBox_string .= ' ' . $contents[$i]['params'];
                } elseif ($this->table_data_parameters != '') {
                    $tableBox_string .= ' ' . $this->table_data_parameters;
                }
                $tableBox_string .= '>' . $contents[$i]['text'] . '</td>' . "\n";
            }

            $tableBox_string .= '  </tr>' . "\n";
        }

        $tableBox_string .= '</table>' . "\n";

        if ($form_set)
            $tableBox_string .= '</form>' . "\n";

        return $tableBox_string;
    }

    function tableBlockHead($contents) {
        $tableBox_string = '';

        $form_set = false;
        if (isset($contents['form'])) {
            $tableBox_string .= $contents['form'] . "\n";
            $form_set = true;
            xtc_array_shift($contents);
        }

        $tableBox_string .= '<table class="boxTableHead" border="' . $this->table_border . '" width="' . $this->table_width . '" cellspacing="' . $this->table_cellspacing . '" cellpadding="' . $this->table_cellpadding . '"';
        if ($this->table_parameters != '')
            $tableBox_string .= ' ' . $this->table_parameters;
        $tableBox_string .= '>' . "\n";

        for ($i = 0, $n = sizeof($contents); $i < $n; $i++) {
            $tableBox_string .= '  <tr';
            if ($this->table_row_parameters != '')
                $tableBox_string .= ' ' . $this->table_row_parameters;
            if (isset($contents[$i]['params']))
                $tableBox_string .= ' ' . $contents[$i]['params'];
            $tableBox_string .= '>' . "\n";
            if (!isset($contents[$i][0]))
                $contents[$i][0] = '';
            if (is_array($contents[$i][0])) {
                for ($x = 0, $y = sizeof($contents[$i]); $x < $y; $x++) {
                    if ($contents[$i][$x]['text']) {
                        $tableBox_string .= '    <td ';
                        if ($contents[$i][$x]['align'] != '')
                            $tableBox_string .= ' align="' . $contents[$i][$x]['align'] . '"';
                        if ($contents[$i][$x]['params']) {
                            $tableBox_string .= ' ' . $contents[$i][$x]['params'];
                        } elseif ($this->table_data_parameters != '') {
                            $tableBox_string .= ' ' . $this->table_data_parameters;
                        }
                        $tableBox_string .= '>';
                        if ($contents[$i][$x]['form'])
                            $tableBox_string .= $contents[$i][$x]['form'];
                        $tableBox_string .= $contents[$i][$x]['text'];
                        if ($contents[$i][$x]['form'])
                            $tableBox_string .= '</form>';
                        $tableBox_string .= '</td>' . "\n";
                    }
                }
            } else {
                $tableBox_string .= '    <td ';
                if (!isset($contents[$i]['align']))
                    $contents[$i]['align'] = '';
                if ($contents[$i]['align'] != '')
                    $tableBox_string .= ' align="' . $contents[$i]['align'] . '"';
                if (isset($contents[$i]['params'])) {
                    $tableBox_string .= ' ' . $contents[$i]['params'];
                } elseif ($this->table_data_parameters != '') {
                    $tableBox_string .= ' ' . $this->table_data_parameters;
                }
                $tableBox_string .= '>' . $contents[$i]['text'] . '</td>' . "\n";
            }

            $tableBox_string .= '  </tr>' . "\n";
        }

        $tableBox_string .= '</table>' . "\n";

        if ($form_set)
            $tableBox_string .= '</form>' . "\n";

        return $tableBox_string;
    }

}
