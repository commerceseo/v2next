<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_href_link.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
    global $request_type, $session_started, $http_domain, $https_domain, $truncate_session_id;

    if (!xtc_not_null($page))
        $page = FILENAME_DEFAULT;

    if ($page == FILENAME_DEFAULT && !xtc_not_null($parameters)) {
        $page = '';
    }

    $ist_blog_da = 'blog.php';
    if (file_exists($ist_blog_da))
        $blog_da = true;
    else
        $blog_da = false;

    if ($connection == 'NONSSL') {
        $link = HTTP_SERVER . DIR_WS_CATALOG;
    } elseif ($connection == 'SSL') {
        if (ENABLE_SSL == true) {
            $link = HTTPS_SERVER . DIR_WS_CATALOG;
        } else {
            $link = HTTP_SERVER . DIR_WS_CATALOG;
        }
    } else
        die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</b><br /><br />');

    while ((substr($link, -1) == '&') || (substr($link, -1) == '?'))
        $link = substr($link, 0, -1);

    /* Create GET Parameters, if Direct URL is not in use */
    if (MODULE_COMMERCE_SEO_INDEX_STATUS <> 'True' || MODULE_COMMERCE_SEO_INDEX_STATUS != 'True') {
        if (xtc_not_null($parameters)) {
            $link .= $page . '?' . $parameters;
            $separator = '&';
        } else {
            $link .= $page;
            $separator = '?';
        }
    }

    /* xtc fake Suma friendly URL */
    if ((SEARCH_ENGINE_FRIENDLY_URLS == 'true') && (MODULE_COMMERCE_SEO_INDEX_STATUS <> 'True')) {
        while (strstr($link, '&&')) {
            $link = str_replace('&&', '&', $link);
        }

        $link = str_replace('?', '/', $link);
        $link = str_replace('&', '/', $link);
        $link = str_replace('=', '/', $link);
        $separator = '?';
    }

    if (SEARCH_ENGINE_FRIENDLY_URLS == 'true' && MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {

        // Create Instance of commerce_seo_url class
        require_once(DIR_FS_INC . 'commerce_seo.inc.php');
        !$commerceSeo ? $commerceSeo = new CommerceSeo() : false;
        $realUrl = false;

        if ($page == 'product_info.php' && strpos($parameters, 'products_id') !== false && strpos($parameters, 'action=') === false) {
            $realUrl = true;
            $link = $commerceSeo->getProductLink($parameters, $connection, $_SESSION['languages_id']);  //generate product link
        }

        if ($page == 'index.php' && strpos($parameters, 'cPath') !== false && strpos($parameters, 'action=') === false && strpos($parameters, 'page=') === false) {
            $realUrl = true;
            $link = $commerceSeo->getCategoryLink($parameters, $connection, $_SESSION['languages_id']);
        }

        if ($page == 'shop_content.php' && strpos($parameters, 'coID') !== false && strpos($parameters, 'action=') === false) {
            $realUrl = true;
            $link = $commerceSeo->getContentLink($parameters, $connection, $_SESSION['languages_id']);
        }

        if ($blog_da == true) {
            if ($page == 'blog.php' && strpos($parameters, 'blog_item') !== false && strpos($parameters, 'action=') === false) {
                $realUrl = true;
                $link = $commerceSeo->getBlogLink($parameters, $connection, $_SESSION['languages_id']);
            } elseif ($page == 'blog.php' && strpos($parameters, 'blog_item') === false && strpos($parameters, 'action=') === false) {
                $realUrl = true;
                $link = $commerceSeo->getBlogCatLink($parameters, $connection, $_SESSION['languages_id']);
            }
        }

        if (($page == 'product_info.php' || $page == 'index.php' || $page == 'commerce_seo_url.php' || $page == 'shop_content.php' || $page == 'blog.php') && MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL == 'True' && strpos($parameters, 'language=') !== false && strpos($parameters, 'action=') === false && strpos($parameters, 'page=') === false) {
            $link = $commerceSeo->getLanguageChangeLink($page, $parameters, $connection = 'NONSSL');
            $realUrl = true;
        }

        $separator = '?';

        if (xtc_not_null($parameters)) {
            // Append GET Parameters if it isn't a Real URL
            if (!$realUrl) {
                $link .= $page . '?' . $parameters;
                $separator = '&';
            }
        } else {
            // Set Standard Link if it isn't a Real URL
            if (!$realUrl) {
                $link .= $page;
                $separator = '?';
            }
        }
    }

    if (($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'false')) {
        if (defined('SID') && xtc_not_null(SID))
            $sid = SID;
        elseif (( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ))
            if ($http_domain != $https_domain)
                $sid = session_name() . '=' . session_id();
    }

    // remove session if useragent is a known Spider
    if ($truncate_session_id)
        $sid = NULL;

    if (isset($sid))
        $link .= $separator . $sid;

    return $link;
}

function xtc_href_link_admin($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
    global $request_type, $session_started, $http_domain, $https_domain;

    if (!xtc_not_null($page))
        die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine the page link!<br /><br />');

    if ($connection == 'NONSSL')
        $link = HTTP_SERVER . DIR_WS_CATALOG;
    else if ($connection == 'SSL') {
        if (ENABLE_SSL == true) {
            $link = HTTPS_SERVER . DIR_WS_CATALOG;
        } else {
            $link = HTTP_SERVER . DIR_WS_CATALOG;
        }
    } else
        die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</b><br /><br />');

    if (xtc_not_null($parameters)) {
        $link .= $page . '?' . $parameters;
        $separator = '&';
    } else {
        $link .= $page;
        $separator = '?';
    }

    while ((substr($link, -1) == '&') || (substr($link, -1) == '?'))
        $link = substr($link, 0, -1);

    // Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
    if (($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'false')) {
        if (defined('SID') && xtc_not_null(SID))
            $sid = SID;
        else if (( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ))
            if ($http_domain != $https_domain)
                $sid = session_name() . '=' . session_id();
    }

    if ($truncate_session_id)
        $sid = NULL;

    if (isset($sid)) {
        $link .= $separator . $sid;
    }

    return $link;
}
