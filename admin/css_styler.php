<?php
/* -----------------------------------------------------------------
 * 	$Id: css_styler.php 980 2014-04-15 10:22:30Z akausch $
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

require('includes/application_top.php');

function xtc_cfg_pull_down_css_bg_pic_sets() {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
    if ($dir = opendir(DIR_FS_CATALOG . 'images/css_button_bg/')) {
        $pictures_array = array('id' => '', 'text' => TEXT_NONE);
        while (($pictures = readdir($dir)) !== false) {
            if (is_dir(DIR_FS_CATALOG . 'images/css_button_bg/') and ($pictures != ".") and ($pictures != "..")) {
                $pictures_array[] = array('id' => $pictures, 'text' => $pictures);
            }
        }
        closedir($dir);
        sort($pictures_array);
        return xtc_draw_pull_down_menu($name, $pictures_array, CSS_BUTTON_BACKGROUND_PIC);
    }
}

function xtc_cfg_pull_down_css_wk_bg_pic_sets() {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
    if ($dir = opendir(DIR_FS_CATALOG . 'images/css_button_bg/')) {
        $pictures_array = array('id' => '', 'text' => TEXT_NONE);
        while (($pictures = readdir($dir)) !== false) {
            if (is_dir(DIR_FS_CATALOG . 'images/css_button_bg/') and ($pictures != ".") and ($pictures != "..")) {
                $pictures_array[] = array('id' => $pictures, 'text' => $pictures);
            }
        }
        closedir($dir);
        sort($pictures_array);
        return xtc_draw_pull_down_menu($name, $pictures_array, WK_CSS_BUTTON_BACKGROUND_PIC);
    }
}

function xtc_cfg_pull_down_css_bg_pic_hover_sets() {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
    if ($dir = opendir(DIR_FS_CATALOG . 'images/css_button_bg/')) {
        $pictures_array = array('id' => '', 'text' => TEXT_NONE);
        while (($pictures = readdir($dir)) !== false) {
            if (is_dir(DIR_FS_CATALOG . 'images/css_button_bg/') and ($pictures != ".") and ($pictures != "..")) {
                $pictures_array[] = array('id' => $pictures, 'text' => $pictures);
            }
        }
        closedir($dir);
        sort($pictures_array);
        return xtc_draw_pull_down_menu($name, $pictures_array, CSS_BUTTON_BACKGROUND_PIC_HOVER);
    }
}

function xtc_cfg_pull_down_css_wk_bg_pic_hover_sets() {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
    if ($dir = opendir(DIR_FS_CATALOG . 'images/css_button_bg/')) {
        $pictures_array = array('id' => '', 'text' => TEXT_NONE);
        while (($pictures = readdir($dir)) !== false) {
            if (is_dir(DIR_FS_CATALOG . 'images/css_button_bg/') and ($pictures != ".") and ($pictures != "..")) {
                $pictures_array[] = array('id' => $pictures, 'text' => $pictures);
            }
        }
        closedir($dir);
        sort($pictures_array);
        return xtc_draw_pull_down_menu($name, $pictures_array, WK_CSS_BUTTON_HOVER_BACKGROUND_PIC);
    }
}

if ($_GET['action']) {
    switch ($_GET['action']) {
        case 'save':
            $configuration_query = xtc_db_query("SELECT configuration_key,configuration_id, configuration_value, use_function, set_function FROM " . TABLE_CONFIGURATION . " WHERE configuration_group_id = '23' ORDER BY sort_order;");
            while ($configuration = xtc_db_fetch_array($configuration_query)) {
                xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . $_POST[$configuration['configuration_key']] . "' where configuration_key='" . $configuration['configuration_key'] . "'");
            }
            xtc_redirect('css_styler.php');
            break;
    }
}

$configuration_query = xtc_db_query("SELECT configuration_key, configuration_id, configuration_value, use_function, set_function FROM " . TABLE_CONFIGURATION . " WHERE configuration_group_id = '23' ORDER BY sort_order;");
$i = 1;
$css_conf = array();
while ($configuration = xtc_db_fetch_array($configuration_query)) {
    $cfgValue = $configuration['configuration_value'];

    if ($configuration['set_function']) {
        eval('$value_field = ' . $configuration['set_function'] . '"' . htmlspecialchars($configuration['configuration_value']) . '");');
    } else {
        if ($configuration['configuration_key'] == 'CSS_BUTTON_BACKGROUND' ||
                $configuration['configuration_key'] == 'CSS_BUTTON_BORDER_COLOR' ||
                $configuration['configuration_key'] == 'WK_CSS_BUTTON_BORDER_COLOR' ||
                $configuration['configuration_key'] == 'CSS_BUTTON_BACKGROUND_1' ||
                $configuration['configuration_key'] == 'CSS_BUTTON_BACKGROUND_2' ||
                $configuration['configuration_key'] == 'CSS_BUTTON_HOVER_BACKGROUND_1' ||
                $configuration['configuration_key'] == 'CSS_BUTTON_HOVER_BACKGROUND_2' ||
                $configuration['configuration_key'] == 'WK_CSS_BUTTON_BACKGROUND' ||
                $configuration['configuration_key'] == 'WK_CSS_BUTTON_BACKGROUND_PIC' ||
                $configuration['configuration_key'] == 'WK_CSS_BUTTON_BACKGROUND_1' ||
                $configuration['configuration_key'] == 'WK_CSS_BUTTON_BACKGROUND_2' ||
                $configuration['configuration_key'] == 'WK_CSS_BUTTON_BACKGROUND_HOVER' ||
                $configuration['configuration_key'] == 'WK_CSS_BUTTON_HOVER_BACKGROUND_PIC' ||
                $configuration['configuration_key'] == 'WK_CSS_BUTTON_HOVER_BACKGROUND_1' ||
                $configuration['configuration_key'] == 'WK_CSS_BUTTON_HOVER_BACKGROUND_2' ||
                $configuration['configuration_key'] == 'WK_CSS_BUTTON_FONT_COLOR' ||
                $configuration['configuration_key'] == 'WK_CSS_BUTTON_FONT_COLOR_HOVER' ||
                $configuration['configuration_key'] == 'CSS_BUTTON_FONT_COLOR' ||
                $configuration['configuration_key'] == 'CSS_BUTTON_FONT_COLOR_HOVER' ||
                $configuration['configuration_key'] == 'CSS_BUTTON_BACKGROUND_HOVER' ||
                $configuration['configuration_key'] == 'CSS_FRONTEND_BACKGROUND' ||
                $configuration['configuration_key'] == 'CSS_FRONTEND_BACKGROUND_1' ||
                $configuration['configuration_key'] == 'CSS_FRONTEND_BACKGROUND_2' ||
                $configuration['configuration_key'] == 'CSS_FRONTEND_BOX_HEADER_BACKGROUND' ||
                $configuration['configuration_key'] == 'CSS_BUTTON_BORDER_COLOR_HOVER') {
            $value_field = xtc_draw_input_field($configuration['configuration_key'], substr($configuration['configuration_value'], 0 , 6), 'class="multiple pick-a-color form-control"');
        } else {
            $value_field = xtc_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'], 'class="pick-a-color form-control"');
        }
    }

    if (strstr($value_field, 'configuration_value')) {
        $value_field = str_replace('configuration_value', $configuration['configuration_key'], $value_field);
    }
    $css_conf[$configuration['configuration_key']] = array('key_name' => $configuration['configuration_key'], 'key_name_raw' => $configuration['configuration_key'], 'key_value' => $value_field, 'key_value_raw' => $configuration['configuration_value']);
    $i++;
}

function getHex($value) {
    $v = substr($value, 0, 6);
    return '#' . $v;
}

function kn($name) {
    return constant(strtoupper($name['key_name'] . '_TITLE'));
}

function knr($name) {
    return $name['key_name_raw'];
}

function kd($name) {
    return constant(strtoupper($name['key_name'] . '_DESC'));
}

function kv($name) {
    return $name['key_value'];
}

function kvr($name) {
    return $name['key_value_raw'];
}

function tr_input($conf) {
    return '<table width="100%"><tr><td valign="top" width="20%">' . kn($conf) . '</td><td valign="top" align="left" width="80%">' . kd($conf) . '<br />' . kv($conf) . '</td></tr></table>';
}

function tr_gradient_input($conf_1, $conf_2) {
    return '<table width="100%"><tr><td valign="top" width="20%">' . kn($conf_1) . '</td><td valign="top"><table width="100%" align="left"><tr>
			<td>' . kd($conf_1) . '<br />' . kv($conf_1) . '</td><td>' . kd($conf_2) . '<br />' . kv($conf_2) . '</td><td valign="bottom">
			<span id="' . strtolower(knr($conf_1)) . '" class="vorschau_gradient" style="background-image: -webkit-linear-gradient(' . getHex(kvr($conf_1)) . ', ' . getHex(kvr($conf_2)) . ');background-image: linear-gradient(' . getHex(kvr($conf_1)) . ', ' . getHex(kvr($conf_2)) . ');">&nbsp;</span>
			</td></tr></table></td></tr></table>';
}

require(DIR_WS_INCLUDES . 'header.php');
echo xtc_draw_form('configuration', 'css_styler.php', 'action=save'); 
?>
<div class="row">
<div class="col-xs-6">
<h1><span class="glyphicon glyphicon-pencil"></span> CSS - Styler</h1>
</div>
<div class="col-xs-6 text-right">
	<input type="submit" class="button" value="<?php echo BUTTON_SAVE ?>" />
</div>
<div class="col-xs-12">

<div id="csstabs">
    <ul>
        <li><a href="#buttons"><?php echo HEAD_CSS_NORMAL_BUTTON; ?></a></li>
        <li><a href="#wkbuttons"><?php echo HEAD_CSS_WK_BUTTON; ?></a></li>
        <li><a href="#frontend"><?php echo HEAD_CSS_FRONTEND; ?></a></li>
    </ul>
    <div id="buttons">
        <table class="table table-striped">
            <tr>
                <td>
                    <h2><?php echo CSS_BUTTON_CONFIG; ?></h2>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['CSS_BUTTON_ACTIVE']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['CSS_BUTTON_BACKGROUND']) . tr_input($css_conf['CSS_BUTTON_BACKGROUND_PIC']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_gradient_input($css_conf['CSS_BUTTON_BACKGROUND_1'], $css_conf['CSS_BUTTON_BACKGROUND_2']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['CSS_BUTTON_BORDER_STYLE']) . tr_input($css_conf['CSS_BUTTON_BORDER_WIDTH']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['CSS_BUTTON_BORDER_COLOR']) . tr_input($css_conf['CSS_BUTTON_BORDER_RADIUS']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['CSS_BUTTON_FONT_FAMILY']) . tr_input($css_conf['CSS_BUTTON_FONT_SIZE']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['CSS_BUTTON_FONT_ITALIC']) . tr_input($css_conf['CSS_BUTTON_FONT_UNDERLINE']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['CSS_BUTTON_FONT_COLOR']) . tr_input($css_conf['CSS_BUTTON_FONT_COLOR_HOVER']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['CSS_BUTTON_FONT_SHADOW']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['CSS_BUTTON_BACKGROUND_HOVER']) . tr_input($css_conf['CSS_BUTTON_BACKGROUND_PIC_HOVER']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_gradient_input($css_conf['CSS_BUTTON_HOVER_BACKGROUND_1'], $css_conf['CSS_BUTTON_HOVER_BACKGROUND_2']) . tr_input($css_conf['CSS_BUTTON_BORDER_COLOR_HOVER']); ?>
                </td>
            </tr>
        </table>
        <br class="clear" />
    </div>
    <div id="wkbuttons">
        <table class="table table-striped">
            <tr>
                <td>
                    <h2><?php echo WK_CSS_BUTTON_CONFIG; ?></h2>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['WK_CSS_BUTTON_BACKGROUND']) . tr_input($css_conf['WK_CSS_BUTTON_BACKGROUND_PIC']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_gradient_input($css_conf['WK_CSS_BUTTON_BACKGROUND_1'], $css_conf['WK_CSS_BUTTON_BACKGROUND_2']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['WK_CSS_BUTTON_BACKGROUND_HOVER']) . tr_input($css_conf['WK_CSS_BUTTON_HOVER_BACKGROUND_PIC']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_gradient_input($css_conf['WK_CSS_BUTTON_HOVER_BACKGROUND_1'], $css_conf['WK_CSS_BUTTON_HOVER_BACKGROUND_2']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['WK_CSS_BUTTON_FONT_COLOR']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['WK_CSS_BUTTON_FONT_COLOR_HOVER']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['WK_CSS_BUTTON_FONT_SHADOW']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['WK_CSS_BUTTON_BORDER_COLOR']); ?>
                </td>
            </tr>
        </table>
        <br class="clear" />
    </div>
	<div id="frontend">
        <table class="table table-striped">
            <tr>
                <td>
                    <h2><?php echo CSS_FRONTEND_CONFIG; ?></h2>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['CSS_FRONTEND_BACKGROUND']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_gradient_input($css_conf['CSS_FRONTEND_BACKGROUND_1'], $css_conf['CSS_FRONTEND_BACKGROUND_2']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo tr_input($css_conf['CSS_FRONTEND_BOX_HEADER_BACKGROUND']); ?>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="col-xs-12 text-right">
	<input type="submit" class="button" value="<?php echo BUTTON_SAVE ?>" />
</div>
</div>
</form>


<?php
require(DIR_WS_INCLUDES . 'footer.php');
?>
<script type="text/javascript">
head.ready(function () {

	$(".multiple").pickAColor({
	  showSpectrum : true,
		showSavedColors : true,
		saveColorsPerElement : true,
		fadeMenuToggle : true,
		showAdvanced : true,
		showBasicColors : true,
		showHexInput : true,
		allowBlank : true
	});
	
});    

</script>

<?php 

require(DIR_WS_INCLUDES . 'application_bottom.php');
