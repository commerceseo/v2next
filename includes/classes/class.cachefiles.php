<?php

/* -----------------------------------------------------------------
 * 	$Id: class.cachefiles.php 943 2014-04-08 13:26:37Z akausch $
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

class cacheFile {

    public $type;
    public $source = array();
    public $name;
    public $data;
    var $css = array();

    function __construct($type) {
        $this->type = $type;
    }

    function __destruct() {
        
    }

    public function setCSS($path) {
        $this->css[] = array('path' => $path);
    }

    public function outputCSS() {
        $css = '';
        $css_file = '';
        foreach ($this->css AS $css_path) {
            $content = '';
            if (!file_exists($this->getCachePath($css_path['path'])) || (USE_TEMPLATE_CACHE == 'true' && filemtime($css_file) < time() - CACHE_TEMPLATE_LIFETIME)) {
                if (file_exists($css_path['path'])) {
                    $content = $this->getCleanCSS(file_get_contents($css_path['path']));
                    //new Style Editor
					if (CSS_FRONTEND_BACKGROUND != '') {
						$content .= 'body {background: #'.CSS_FRONTEND_BACKGROUND.' !important;}';
					}
					if (CSS_FRONTEND_BOX_HEADER_BACKGROUND != '') {
						$content .= '.box-heading {color: #'.CSS_FRONTEND_BOX_HEADER_BACKGROUND.';}';
					}
					if (CSS_FRONTEND_BACKGROUND_1 != '' && CSS_FRONTEND_BACKGROUND_2 != '') {
						$content .= '.box-heading {background: #'.CSS_FRONTEND_BACKGROUND_1.';}';
						$content .= '.box-heading {background-image: -webkit-linear-gradient(top, #'.CSS_FRONTEND_BACKGROUND_1.', #'.CSS_FRONTEND_BACKGROUND_2.' 100%);}';
						$content .= '.box-heading {background-image: linear-gradient(to bottom, #'.CSS_FRONTEND_BACKGROUND_1.', #'.CSS_FRONTEND_BACKGROUND_2.' 100%);}';
					}
					$content .= '.pimg, .product_img {min-height: ' .  PRODUCT_IMAGE_THUMBNAIL_HEIGHT .'px;}';

					//End Style Editor
					file_put_contents($this->getCachePath($css_path['path']), $content);
                    $css .= $content;
                }
            } else {
                $css .= file_get_contents($this->getCachePath($css_path['path']));
			}
        }

        $css_file = '_css_' . $this->getTempName($this->css);


        if (!file_exists(DIR_FS_CATALOG . 'cache/' . $css_file) || (USE_TEMPLATE_CACHE == 'true' && filemtime($css_file) < time() - CACHE_TEMPLATE_LIFETIME)) {

            @unlink($css_file);
            file_put_contents(DIR_FS_CATALOG . 'cache/' . $css_file, $css);
        }
        return file_get_contents(DIR_FS_CATALOG . 'cache/' . $css_file);
    }

    public function getCachePath($path) {
        $real = pathinfo($path);
        if ($this->type == 'css')
            return DIR_FS_CATALOG . 'cache/_cache_css_' . $real['basename'];
        elseif ($this->type == 'js')
            return DIR_FS_CATALOG . 'cache/_cache_js_' . $real['basename'];
    }

    public function getCleanCSS($data) {
        $raw = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $data);
        $raw = preg_replace('/\s\s+/', ' ', $raw);

        $spaces = array('{ ', ' }', ' {', '} ', ' ,', ', ', ': ', ' :', '; ', ' ;');

        foreach ($spaces AS $space)
            $raw = str_replace($space, trim($space), $raw);

        $content = str_replace(array("\r\n", "\r", "\n", "\t"), '', $raw);

        return $content;
    }

    function getTempName($path) {
        foreach ($this->{$this->type} AS $name_path)
            $name .= $this->getCachePath($name_path['path']);
        return md5($name);
    }

}