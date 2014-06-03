<?php

/* -----------------------------------------------------------------
 * 	$Id: css_styler.php 480 2013-07-14 10:40:27Z akausch $
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

function getHex($value) {
    $v = substr($value, 0, 6);
    return '#' . $v;
}

if (CSS_BUTTON_ACTIVE == 'true' && CSS_BUTTON_ACTIVE != 'css') {
    $css = 'input.css_img_button, a span.css_img_button {';
    if ((CSS_BUTTON_BACKGROUND_1 != '' || CSS_BUTTON_BACKGROUND_2 != '') && CSS_BUTTON_BACKGROUND_PIC == '') {
        $background = 'background: linear-gradient(' . getHex(CSS_BUTTON_BACKGROUND_1) . ', ' . getHex(CSS_BUTTON_BACKGROUND_2) . ') repeat scroll 0 0 transparent;';
    } elseif (CSS_BUTTON_BACKGROUND_PIC != '' && CSS_BUTTON_BACKGROUND_PIC != '-' && defined(CSS_BUTTON_BACKGROUND_PIC)) {
        $background = 'background: url(images/css_button_bg/' . CSS_BUTTON_BACKGROUND_PIC . ') repeat-x 0 100%;';
    }
    $css .= 'background:' . getHex(CSS_BUTTON_BACKGROUND) . ';' . $background;

    if (CSS_BUTTON_BORDER_WIDTH > 0 && CSS_BUTTON_BORDER_STYLE != 'none') {
        $css .= 'border:' . CSS_BUTTON_BORDER_WIDTH . ' ' . CSS_BUTTON_BORDER_STYLE . ' ' . getHex(CSS_BUTTON_BORDER_COLOR) . ';';
    } else {
        $css .= 'border: 0;';
    }

    $css .= 'color:' . getHex(CSS_BUTTON_FONT_COLOR) . ';
		cursor:pointer;
		margin-bottom: 2px; 
		font-family: ' . CSS_BUTTON_FONT_FAMILY . ';'
            . (CSS_BUTTON_FONT_SIZE != '' ? 'font-size:' . CSS_BUTTON_FONT_SIZE . ';' : '') . '
		font-style:normal;
		font-variant:normal;
		font-weight:normal;
		display:inline-block;'
            . ((CSS_BUTTON_FONT_ITALIC == 'true') ? 'font-style: italic;' : '') .
            'padding:0 4px;
		text-align:center !important;
		white-space:nowrap;';
    if (CSS_BUTTON_BORDER_RADIUS != '') {
        $css .= 'border-radius:' . CSS_BUTTON_BORDER_RADIUS . ';';
    }
    if (CSS_BUTTON_FONT_SHADOW != '') {
        $css .= 'text-shadow:' . CSS_BUTTON_FONT_SHADOW . ';';
    }
    if (CSS_BUTTON_MISC != '') {
        $css .= CSS_BUTTON_MISC;
    }
    $css .= '}';

    $css .= 'a span.css_img_button {line-height:2.0em; padding: 1px 4px;}';
    $css .= 'input.css_img_button{height:2.4em; padding: 1px 4px;}';
    $css .= 'a:hover .css_img_button, input.css_img_button:hover {';

    if ((CSS_BUTTON_HOVER_BACKGROUND_1 != '' || CSS_BUTTON_HOVER_BACKGROUND_2 != '') && CSS_BUTTON_BACKGROUND_PIC_HOVER == '') {
        $background_hover = 'background: linear-gradient(' . getHex(CSS_BUTTON_HOVER_BACKGROUND_1) . ', ' . getHex(CSS_BUTTON_HOVER_BACKGROUND_2) . ') repeat scroll 0 0 transparent;';
    } elseif (CSS_BUTTON_BACKGROUND_PIC_HOVER != '' && CSS_BUTTON_BACKGROUND_PIC_HOVER != '-' && defined(CSS_BUTTON_BACKGROUND_PIC_HOVER)) {
        $background_hover = 'background: url(images/css_button_bg/' . CSS_BUTTON_BACKGROUND_PIC_HOVER . ') repeat-x 0 100%;';
    }
    $css .= 'background:' . getHex(CSS_BUTTON_BACKGROUND_HOVER) . ';' . $background_hover;
    if (CSS_BUTTON_BORDER_COLOR_HOVER != '')
        $css .= 'border-color:' . getHex(CSS_BUTTON_BORDER_COLOR_HOVER) . ';';
    if (CSS_BUTTON_FONT_COLOR_HOVER != '')
        $css .= 'color:' . getHex(CSS_BUTTON_FONT_COLOR_HOVER) . ';';
    $css .= '}';

    $css .= 'a.css_img_button:link,a.css_img_button:visited,a.css_img_button:active,a:hover .css_img_button { text-decoration: none; }';


    //Warenkorb button

    $css .= 'input.css_wk_img_button, a span.css_wk_img_button {';
    if ((WK_CSS_BUTTON_BACKGROUND_1 != '' || WK_CSS_BUTTON_BACKGROUND_2 != '') && WK_CSS_BUTTON_BACKGROUND_PIC == '') {
        $wk_background = 'background: linear-gradient(' . getHex(WK_CSS_BUTTON_BACKGROUND_1) . ', ' . getHex(WK_CSS_BUTTON_BACKGROUND_2) . ') repeat scroll 0 0 transparent;';
    } elseif (WK_CSS_BUTTON_BACKGROUND_PIC != '' && WK_CSS_BUTTON_BACKGROUND_PIC != '-' && defined(WK_CSS_BUTTON_BACKGROUND_PIC)) {
        $wk_background = 'background: url(images/css_button_bg/' . WK_CSS_BUTTON_BACKGROUND_PIC . ') repeat-x 0 100%;';
    }
    $css .= 'background:' . getHex(WK_CSS_BUTTON_BACKGROUND) . ';' . $wk_background;

    if (CSS_BUTTON_BORDER_WIDTH > 0 && CSS_BUTTON_BORDER_STYLE != 'none') {
        $css .= 'border:' . CSS_BUTTON_BORDER_WIDTH . ' ' . CSS_BUTTON_BORDER_STYLE . ' ' . getHex(WK_CSS_BUTTON_BORDER_COLOR) . ';';
    } else {
        $css .= 'border: 0;';
    }

    $css .= 'color:' . getHex(WK_CSS_BUTTON_FONT_COLOR) . ';
		cursor:pointer;
		margin-bottom: 2px; 
		font-family: ' . CSS_BUTTON_FONT_FAMILY . ';'
            . (CSS_BUTTON_FONT_SIZE != '' ? 'font-size:' . CSS_BUTTON_FONT_SIZE . ';' : '') . '
		font-style:normal;
		font-variant:normal;
		font-weight:normal;
		display:inline-block;'
            . ((CSS_BUTTON_FONT_ITALIC == 'true') ? 'font-style: italic;' : '') .
            'padding:0 4px;
		text-align:center !important;
		white-space:nowrap;';
    if (CSS_BUTTON_BORDER_RADIUS != '') {
        $css .= 'border-radius:' . CSS_BUTTON_BORDER_RADIUS . ';';
    }
    if (WK_CSS_BUTTON_FONT_SHADOW != '') {
        $css .= 'text-shadow:' . WK_CSS_BUTTON_FONT_SHADOW . ';';
    }
    if (CSS_BUTTON_MISC != '') {
        $css .= CSS_BUTTON_MISC;
    }
    $css .= '}';

    $css .= 'a span.css_wk_img_button {
				line-height:2.0em; padding: 1px 4px;
			}';
    $css .= 'input.css_wk_img_button{height:2.3em;padding: 1px 4px;}';
    $css .= 'a:hover .css_wk_img_button, input.css_wk_img_button:hover {';

    if ((WK_CSS_BUTTON_HOVER_BACKGROUND_1 != '' || WK_CSS_BUTTON_HOVER_BACKGROUND_2 != '') && WK_CSS_BUTTON_HOVER_BACKGROUND_PIC == '') {
        $wk_background_hover = 'background: linear-gradient(' . getHex(WK_CSS_BUTTON_HOVER_BACKGROUND_1) . ', ' . getHex(WK_CSS_BUTTON_HOVER_BACKGROUND_2) . ') repeat scroll 0 0 transparent;';
    } elseif (WK_CSS_BUTTON_HOVER_BACKGROUND_PIC != '' && WK_CSS_BUTTON_HOVER_BACKGROUND_PIC != '-' && defined(WK_CSS_BUTTON_HOVER_BACKGROUND_PIC)) {
        $wk_background_hover = 'background: url(images/css_button_bg/' . WK_CSS_BUTTON_HOVER_BACKGROUND_PIC . ') repeat-x 0 100%;';
    }

    $css .= 'background:' . getHex(WK_CSS_BUTTON_BACKGROUND_HOVER) . ';' . $wk_background_hover;
    if (CSS_BUTTON_BORDER_COLOR_HOVER != '') {
        $css .= 'border-color:' . getHex(CSS_BUTTON_BORDER_COLOR_HOVER) . ';';
    }
    if (WK_CSS_BUTTON_FONT_COLOR_HOVER != '') {
        $css .= 'color:' . getHex(WK_CSS_BUTTON_FONT_COLOR_HOVER) . ';';
    }
    $css .= '}';

    $css .= 'a.css_wk_img_button:link,a.css_wk_img_button:visited,a.css_wk_img_button:active,a:hover .css_wk_img_button { text-decoration: none; }';
}

$output = str_replace("\n", '', $css);
$output = str_replace("\t", '', $output);


echo trim($output);

