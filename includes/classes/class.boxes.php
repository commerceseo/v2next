<?php

/* -----------------------------------------------------------------
 * 	$Id: class.boxes.php 660 2013-10-08 16:53:16Z akausch $
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

class tableBox {

    var $table_border = '0';
    var $table_width = '100%';
    var $table_cellspacing = '0';
    var $table_cellpadding = '';
    var $table_parameters = '';
    var $table_row_parameters = '';
    var $table_data_parameters = '';

    // class constructor
    function tableBox($contents, $direct_output = false) {
        $tableBox_string = '<div';
        if (xtc_not_null($this->table_parameters))
            $tableBox_string .= ' ' . $this->table_parameters;
        $tableBox_string .= '>' . "\n";

        for ($i = 0, $n = sizeof($contents); $i < $n; $i++) {
            if (isset($contents[$i]['form']) && xtc_not_null($contents[$i]['form']))
                $tableBox_string .= $contents[$i]['form'] . "\n";

            $tableBox_string .= '  <div';
            if (xtc_not_null($this->table_row_parameters))
                $tableBox_string .= ' ' . $this->table_row_parameters;

            if (isset($contents[$i]['params']) && xtc_not_null($contents[$i]['params']))
                $tableBox_string .= ' ' . $contents[$i]['params'];

            $tableBox_string .= '>' . "\n";

            if (is_array($contents[$i][0])) {
                for ($x = 0, $n2 = sizeof($contents[$i]); $x < $n2; $x++) {
                    if (isset($contents[$i][$x]['text']) && xtc_not_null($contents[$i][$x]['text'])) {
                        $tableBox_string .= '    <div';
                        if (isset($contents[$i][$x]['align']) && xtc_not_null($contents[$i][$x]['align']))
                            $tableBox_string .= ' align="' . $contents[$i][$x]['align'] . '"';

                        if (isset($contents[$i][$x]['params']) && xtc_not_null($contents[$i][$x]['params'])) {
                            $tableBox_string .= ' ' . $contents[$i][$x]['params'];
                        } elseif (xtc_not_null($this->table_data_parameters)) {
                            $tableBox_string .= ' ' . $this->table_data_parameters;
                        }
                        $tableBox_string .= '>';
                        if (isset($contents[$i][$x]['form']) && xtc_not_null($contents[$i][$x]['form']))
                            $tableBox_string .= $contents[$i][$x]['form'];

                        $tableBox_string .= $contents[$i][$x]['text'];
                        if (isset($contents[$i][$x]['form']) && xtc_not_null($contents[$i][$x]['form']))
                            $tableBox_string .= '</form>';

                        $tableBox_string .= '</div>' . "\n";
                    }
                }
            } else {
                $tableBox_string .= '<div';
                if (isset($contents[$i]['align']) && xtc_not_null($contents[$i]['align']))
                    $tableBox_string .= ' align="' . $contents[$i]['align'] . '"';

                if (isset($contents[$i]['params']) && xtc_not_null($contents[$i]['params'])) {
                    $tableBox_string .= ' ' . $contents[$i]['params'];
                } elseif (xtc_not_null($this->table_data_parameters)) {
                    $tableBox_string .= ' ' . $this->table_data_parameters;
                }
                $tableBox_string .= '>';
                $tableBox_string .= $contents[$i]['text'];
                $tableBox_string .= '</div>' . "\n";
            }

            $tableBox_string .= '  </div>' . "\n";
            if (isset($contents[$i]['form']) && xtc_not_null($contents[$i]['form']))
                $tableBox_string .= '</form>' . "\n";
        }

        $tableBox_string .= '</div>' . "\n";

        if ($direct_output == true)
            echo $tableBox_string;

        return $tableBox_string;
    }

}

class indexErrorBox {

    function indexErrorBox($contents, $direct_output = false) {
        $tableBox_string = '<ul style="list-style:none;">';
        for ($i = 0, $n = sizeof($contents); $i < $n; $i++) {
            if (is_array($contents[$i][0])) {
                for ($x = 0, $n2 = sizeof($contents[$i]); $x < $n2; $x++) {
                    if (isset($contents[$i][$x]['text']) && xtc_not_null($contents[$i][$x]['text'])) {
                        $tableBox_string .= $contents[$i][$x]['text'];
                    }
                }
            } else {
                $tableBox_string .= $contents[$i]['text'];
            }
        }
        $tableBox_string .= '</ul>' . "\n";
        if ($direct_output == true)
            echo $tableBox_string;

        return $tableBox_string;
    }

}

class infoBox extends tableBox {

    function infoBox($contents) {
        $info_box_contents = array();
        $info_box_contents[] = array('text' => $this->infoBoxContents($contents));
        $this->table_cellpadding = '1';
        $this->table_parameters = 'class="infoBox"';
        $this->tableBox($info_box_contents, true);
    }

    function infoBoxContents($contents) {
        $this->table_cellpadding = '3';
        $this->table_parameters = 'class="infoBoxContents"';
        $info_box_contents = array();
        $info_box_contents[] = array(array('text' => '&nbsp;'));
        for ($i = 0, $n = sizeof($contents); $i < $n; $i++) {
            $info_box_contents[] = array(array('align' => $contents[$i]['align'],
                    'form' => $contents[$i]['form'],
                    'params' => 'class="boxText"',
                    'text' => $contents[$i]['text']));
        }
        $info_box_contents[] = array(array('text' => '&nbsp;'));
        return $this->tableBox($info_box_contents);
    }

}

class infoBoxHeading extends tableBox {

    function infoBoxHeading($contents, $left_corner = true, $right_corner = true, $right_arrow = false) {
        $this->table_cellpadding = '0';

        if ($left_corner == true) {
            $left_corner = xtc_image(DIR_WS_IMAGES . 'infobox/corner_left.gif');
        } else {
            $left_corner = xtc_image(DIR_WS_IMAGES . 'infobox/corner_right_left.gif');
        }
        if ($right_arrow == true) {
            $right_arrow = '<a href="' . $right_arrow . '">' . xtc_image(DIR_WS_IMAGES . 'infobox/arrow_right.gif', ICON_ARROW_RIGHT) . '</a>';
        } else {
            $right_arrow = '';
        }
        if ($right_corner == true) {
            $right_corner = $right_arrow . xtc_image(DIR_WS_IMAGES . 'infobox/corner_right.gif');
        } else {
            $right_corner = $right_arrow . '&nbsp;';
        }

        $info_box_contents = array();
        $info_box_contents[] = array(array('params' => 'height="14" class="infoBoxHeading"',
                'text' => $left_corner),
            array('params' => 'width="100%" height="14" class="infoBoxHeading"',
                'text' => $contents[0]['text']),
            array('params' => 'height="14" class="infoBoxHeading" nowrap',
                'text' => $right_corner));

        $this->tableBox($info_box_contents, true);
    }

}

class contentBox extends tableBox {

    function contentBox($contents) {
        $info_box_contents = array();
        $info_box_contents[] = array('text' => $this->contentBoxContents($contents));
        $this->table_cellpadding = '1';
        $this->table_parameters = 'class="infoBox"';
        $this->tableBox($info_box_contents, true);
    }

    function contentBoxContents($contents) {
        $this->table_cellpadding = '4';
        $this->table_parameters = 'class="infoBoxContents"';
        return $this->tableBox($contents);
    }

}

class contentBoxHeading extends tableBox {

    function contentBoxHeading($contents) {
        $this->table_width = '100%';
        $this->table_cellpadding = '0';

        $info_box_contents = array();
        $info_box_contents[] = array(array('params' => 'height="14" class="infoBoxHeading"',
                'text' => xtc_image(DIR_WS_IMAGES . 'infobox/corner_left.gif')),
            array('params' => 'height="14" class="infoBoxHeading" width="100%"',
                'text' => $contents[0]['text']),
            array('params' => 'height="14" class="infoBoxHeading"',
                'text' => xtc_image(DIR_WS_IMAGES . 'infobox/corner_right_left.gif')));

        $this->tableBox($info_box_contents, true);
    }

}

class errorBox extends indexErrorBox {

    function errorBox($contents) {
        $this->error_data_parameters = 'class="error_index"';
        $this->indexErrorBox($contents, true);
    }

}

class productListingBox extends tableBox {

    function productListingBox($contents) {
        $this->table_parameters = 'class="productListing"';
        $this->tableBox($contents, true);
    }

}

