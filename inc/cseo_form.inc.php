<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_form.inc.php 995 2014-04-29 17:59:27Z akausch $
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

// Output a form
function xtc_draw_form($name, $action, $method = 'post', $parameters = '', $class = '') {
    if ($class != '') {
        $cssclass = $class;
    }
    $form = '<form rel="nofollow" ' . $cssclass . ' id="' . xtc_parse_input_field_data($name, array('"' => '&quot;')) . '" action="' . xtc_parse_input_field_data($action, array('"' => '&quot;')) . '" method="' . xtc_parse_input_field_data($method, array('"' => '&quot;')) . '"';

    if (xtc_not_null($parameters))
        $form .= ' ' . $parameters;

    $form .= '>';

    return $form;
}

// Output a form hidden field
function xtc_draw_hidden_field($name, $value = '', $parameters = '') {
    $field = '<input type="hidden" id="' . xtc_parse_input_field_data($name, array('"' => '&quot;')) . '" name="' . xtc_parse_input_field_data($name, array('"' => '&quot;')) . '" value="';

    if (xtc_not_null($value)) {
        $field .= xtc_parse_input_field_data($value, array('"' => '&quot;')) . '"';
    } else {
        $field .= xtc_parse_input_field_data($GLOBALS[$name], array('"' => '&quot;')) . '"';
    }

    if (xtc_not_null($parameters))
        $field .= ' ' . $parameters;

    $field .= ' />';

    return $field;
}

// Output a form input field
function xtc_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . xtc_parse_input_field_data($type, array('"' => '&quot;')) . '" name="' . xtc_parse_input_field_data($name, array('"' => '&quot;')) . '"';

    if ((isset($GLOBALS[$name])) && ($reinsert_value == true)) {
        $field .= ' value="' . xtc_parse_input_field_data($GLOBALS[$name], array('"' => '&quot;')) . '"';
    } elseif (xtc_not_null($value)) {
        $field .= ' value="' . xtc_parse_input_field_data($value, array('"' => '&quot;')) . '"';
    }

    if (xtc_not_null($parameters))
        $field .= ' ' . $parameters;

    $field .= ' />';

    return $field;
}

function xtc_draw_input_fieldNote($data, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . xtc_parse_input_field_data($type, array('"' => '&quot;')) . '" name="' . xtc_parse_input_field_data($data['name'], array('"' => '&quot;')) . '"';

    if ((isset($GLOBALS[$data['name']])) && ($reinsert_value == true)) {
        $field .= ' value="' . xtc_parse_input_field_data($GLOBALS[$data['name']], array('"' => '&quot;')) . '"';
    } elseif (xtc_not_null($value)) {
        $field .= ' value="' . xtc_parse_input_field_data($value, array('"' => '&quot;')) . '"';
    }

    if (xtc_not_null($parameters))
        $field .= ' ' . $parameters;

    $field .= ' />' . $data['text'];

    return $field;
}

// Output a form checkbox field
function xtc_draw_checkbox_field($name, $value = '', $checked = false, $parameters = '') {
    return xtc_draw_selection_field($name, 'checkbox', $value, $checked, $parameters);
}

// Output a form password field
function xtc_draw_password_field($name, $value = '', $parameters = 'maxlength="40"') {
    return xtc_draw_input_field($name, $value, $parameters, 'password', false);
}

function xtc_draw_password_fieldNote($name, $value = '', $parameters = 'maxlength="40"') {
    return xtc_draw_input_fieldNote($name, $value, $parameters, 'password', false);
}

// Output a form pull down menu
function xtc_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . xtc_parse_input_field_data($name, array('"' => '&quot;')) . '"';

    if (xtc_not_null($parameters))
        $field .= ' ' . $parameters;

    $field .= '>';

    if (empty($default) && isset($GLOBALS[$name]))
        $default = $GLOBALS[$name];

    for ($i = 0, $n = sizeof($values); $i < $n; $i++) {
        $field .= '<option value="' . xtc_parse_input_field_data($values[$i]['id'], array('"' => '&quot;')) . '"';
        if ($default == $values[$i]['id']) {
            $field .= ' selected="selected"';
        }

        $field .= '>' . xtc_parse_input_field_data($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>';

    if ($required == true)
        $field .= TEXT_FIELD_REQUIRED;

    return $field;
}

function xtc_draw_pull_down_menuNote($data, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . xtc_parse_input_field_data($data['name'], array('"' => '&quot;')) . '"';

    if (xtc_not_null($parameters))
        $field .= ' ' . $parameters;

    $field .= '>';

    if (empty($default) && isset($GLOBALS[$data['name']]))
        $default = $GLOBALS[$data['name']];

    for ($i = 0, $n = sizeof($values); $i < $n; $i++) {
        $field .= '<option value="' . xtc_parse_input_field_data($values[$i]['id'], array('"' => '&quot;')) . '"';
        if ($default == $values[$i]['id']) {
            $field .= ' selected="selected"';
        }

        $field .= '>' . xtc_parse_input_field_data($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>' . $data['text'];

    if ($required == true)
        $field .= TEXT_FIELD_REQUIRED;

    return $field;
}

function xtc_draw_radio_field($name, $value = '', $checked = false, $parameters = '') {
    if (is_array($name))
        return xtc_draw_selection_fieldNote($name, 'radio', $value, $checked, $parameters);
    return xtc_draw_selection_field($name, 'radio', $value, $checked, $parameters);
}

// Output a selection field - alias function for xtc_draw_checkbox_field() and xtc_draw_radio_field()

function xtc_draw_selection_field($name, $type, $value = '', $checked = false, $parameters = '') {
    $selection = '<input type="' . xtc_parse_input_field_data($type, array('"' => '&quot;')) . '" name="' . xtc_parse_input_field_data($name, array('"' => '&quot;')) . '"';

    if (xtc_not_null($value))
        $selection .= ' value="' . xtc_parse_input_field_data($value, array('"' => '&quot;')) . '"';

    if (($checked == true) || ($GLOBALS[$name] == 'on') || ( (isset($value)) && ($GLOBALS[$name] == $value) )) {
        $selection .= ' checked="checked"';
    }

    if (xtc_not_null($parameters))
        $selection .= ' ' . $parameters;

    $selection .= ' />';

    return $selection;
}

function xtc_draw_selection_fieldNote($data, $type, $value = '', $checked = false, $parameters = '') {
    $selection = $data['suffix'] . '<input type="' . xtc_parse_input_field_data($type, array('"' => '&quot;')) . '" name="' . xtc_parse_input_field_data($data['name'], array('"' => '&quot;')) . '"';

    if (xtc_not_null($value))
        $selection .= ' value="' . xtc_parse_input_field_data($value, array('"' => '&quot;')) . '"';

    if (($checked == true) || ($GLOBALS[$data['name']] == 'on') || ( (isset($value)) && ($GLOBALS[$data['name']] == $value) )) {
        $selection .= ' checked="checked"';
    }

    if (xtc_not_null($parameters))
        $selection .= ' ' . $parameters;

    $selection .= ' />' . $data['text'];

    return $selection;
}

// Output a form textarea field
function xtc_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true) {
    $field = '<textarea name="' . xtc_parse_input_field_data($name, array('"' => '&quot;')) . '" id="' . xtc_parse_input_field_data($name, array('"' => '&quot;')) . '" cols="' . xtc_parse_input_field_data($width, array('"' => '&quot;')) . '" rows="' . xtc_parse_input_field_data($height, array('"' => '&quot;')) . '"';

    if (xtc_not_null($parameters))
        $field .= ' ' . $parameters;

    $field .= '>';

    if ((isset($GLOBALS[$name])) && ($reinsert_value == true)) {
        $field .= $GLOBALS[$name];
    } elseif (xtc_not_null($text)) {
        $field .= $text;
    }
    $field .= '</textarea>' . "\n";

    return $field;
}

// Parse the data used in the html tags to ensure the tags will not break
function xtc_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
}
