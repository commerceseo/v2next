<?php

/* -----------------------------------------------------------------
 * 	$Id: commerce_seo.inc.php 1301 2014-12-10 17:07:16Z akausch $
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

class CommerceSeo {

    function blog_da() {
        $ist_blog_da = 'blog.php';
        if (file_exists($ist_blog_da))
            return true;
        else
            return false;
    }

    function CommerceSeo() {
        require_once(DIR_FS_INC . 'xtc_get_product_path.inc.php');
        require_once(DIR_FS_INC . 'xtc_get_category_path.inc.php');
        require_once(DIR_FS_INC . 'xtc_get_parent_categories.inc.php');
        require_once(DIR_FS_INC . 'cseo_get_url_friendly_text.inc.php');
    }

    function getProductLink($parameters, $connection = 'NONSSL', $language) {
        $explodedParams = explode('&', $parameters);
        foreach ($explodedParams as $value) {
            if (substr($value, 0, 12) == 'products_id=') {
                $productId = substr($value, 12, strlen($value));
            }
        }

        if ($connection == 'SSL' && ENABLE_SSL)
            $link = HTTPS_SERVER . DIR_WS_CATALOG;
        else
            $link = HTTP_SERVER . DIR_WS_CATALOG;

        $product_link = xtc_db_fetch_array(xtDBquery("SELECT 
									url_text 
								FROM 
									" . TABLE_PRODUCTS_DESCRIPTION . " 
								WHERE 
									products_id = '" . $productId . "' 
								AND 
									language_id='" . $language . "'"));

        return $link . $product_link['url_text'];
    }

    function getCategoryLink($parameters, $connection = 'NONSSL', $language) {
        $explodedParams = explode('&', $parameters);

        foreach ($explodedParams as $value) {
            if (substr($value, 0, 6) == 'cPath=') {
                $xtcCategoryParameter = substr($value, 6, strlen($value));
                if (strpos($xtcCategoryParameter, '_')) {
                    $categoryId = substr(substr($value, 6, strlen($value)), strrpos($xtcCategoryParameter, '_') + 1);
                } else {
                    $categoryId = substr($value, 6, strlen($value));
                }
            }
        }
        if ($connection == 'SSL' && ENABLE_SSL) {
            $link = HTTPS_SERVER . DIR_WS_CATALOG;
        } else {
            $link = HTTP_SERVER . DIR_WS_CATALOG;
        }

        $category_link = xtc_db_fetch_array(xtDBquery("SELECT 
												url_text 
											FROM 
												commerce_seo_url 
											WHERE 
												categories_id = '" . $categoryId . "' 
											AND 
												language_id='" . $language . "'"));

        $params = '';
        foreach ($explodedParams as $value) {
            if (substr($value, 0, 9) == 'per_site=') {
                $perSite = substr($value, 9, strlen($value));
                if (!empty($params))
                    $sep = '&';
                $params .= $sep . 'per_site=' . $perSite;
            }
            if (substr($value, 0, 8) == 'view_as=') {
                $viewAs = substr($value, 8, strlen($value));
                if (!empty($params))
                    $sep = '&';
                $params .= $sep . 'view_as=' . $viewAs;
            }
            if (substr($value, 0, 10) == 'filter_id=') {
                $filterId = substr($value, 10, strlen($value));
                if (!empty($params))
                    $sep = '&';
                $params .= $sep . 'filter_id=' . $filterId;
            }
            if (substr($value, 0, 10) == 'multisort=') {
                $multisort = substr($value, 10, strlen($value));
                if (!empty($params))
                    $sep = '&';
                $params .= $sep . 'multisort=' . $multisort;
            }
        }

        return $link . $category_link['url_text'] . (!empty($params) ? '?' . $params : '');
    }

    function getContentLink($parameters, $connection = 'NONSSL', $language) {
        $explodedParams = explode('&', $parameters);

        // Extract category id and file parameter
        foreach ($explodedParams as $value) {
            if (substr($value, 0, 4) == 'coID') {
                $contentGroupId = substr($value, 5, strlen($value));
            }
        }

        if ($connection == 'SSL' && ENABLE_SSL) {
            $link = HTTPS_SERVER . DIR_WS_CATALOG;
        } else {
            $link = HTTP_SERVER . DIR_WS_CATALOG;
        }

        $content_link = xtc_db_fetch_array(xtDBquery("SELECT 
											url_text 
										FROM 
											commerce_seo_url 
										WHERE 
											content_group = '" . $contentGroupId . "' 
										AND 
											language_id='" . $language . "'"));

        return $link . $content_link['url_text'];
    }

    function getBlogLink($parameters, $connection = 'NONSSL', $language) {
        $explodedParams = explode('&', $parameters);

        foreach ($explodedParams as $value) {
            if (substr($value, 0, 9) == 'blog_item') {
                $blogID = substr($value, 10, strlen($value));
            }
        }

        if ($connection == 'SSL' && ENABLE_SSL) {
            $link = HTTPS_SERVER . DIR_WS_CATALOG;
        } else {
            $link = HTTP_SERVER . DIR_WS_CATALOG;
        }

        $blog_link = xtc_db_fetch_array(xtDBquery("SELECT 
										url_text 
									FROM 
										commerce_seo_url 
									WHERE 
										blog_id = '" . $blogID . "' 
									AND 
										language_id='" . $language . "'"));

        return $link . $blog_link['url_text'];
    }

    function getBlogCatLink($parameters, $connection = 'NONSSL', $language) {
        $explodedParams = explode('&', $parameters);

        foreach ($explodedParams as $value) {
            if (substr($value, 0, 8) == 'blog_cat') {
                $blogCatID = substr($value, 9, strlen($value));
            }
        }

        if ($connection == 'SSL' && ENABLE_SSL) {
            $link = HTTPS_SERVER . DIR_WS_CATALOG;
        } else {
            $link = HTTP_SERVER . DIR_WS_CATALOG;
        }

        $blog_cat_link = xtc_db_fetch_array(xtDBquery("SELECT 
											url_text 
										FROM 
											commerce_seo_url 
										WHERE 
											blog_cat = '" . $blogCatID . "' 
										AND 
											language_id='" . $language . "'"));

        return $link . $blog_cat_link['url_text'];
    }

    function getLanguageChangeLink($page, $parameters, $connection = 'NONSSL') {

        $languageParamStartPos = strpos($parameters, 'language=');
        $language = substr($parameters, ($languageParamStartPos + 9), 2);
        $languageId_result = xtc_db_fetch_array(xtDBquery("SELECT 
									languages_id
								FROM 
									languages
								WHERE 
									code='" . $language . "'"));

        if ($connection == 'SSL' && ENABLE_SSL) {
            $link = HTTPS_SERVER . DIR_WS_CATALOG;
        } else {
            $link = HTTP_SERVER . DIR_WS_CATALOG;
        }

        $categoryId = ' IS NULL';
        $productId = ' IS NULL';
        $coID = ' IS NULL';
        $blogID = ' IS NULL';
        $blogCAT = ' IS NULL';

        $explodedParams = explode('&', $parameters);
        switch ($page) {
            case 'product_info.php':
                foreach ($explodedParams as $value) {
                    if (substr($value, 0, 12) == 'products_id=') {
                        $productId = '=\'' . substr($value, 12, strlen($value)) . '\'';
                    }
                }
                break;
            case 'index.php':
                $catIdFound = false;
                foreach ($explodedParams as $value) {
                    if (substr($value, 0, 6) == 'cPath=') {
                        $categoryId = '=\'' . substr($value, 6, strlen($value)) . '\'';
                        $catIdFound = true;
                    }
                }

                if (!$catIdFound) {
                    return $link . $language . '/';
                }
                break;
            case 'blog.php' :
                foreach ($explodedParams as $value) {
                    if (substr($value, 0, 10) == 'blog_item=') {
                        $blogID = '=\'' . substr($value, 10, strlen($value)) . '\'';
                    }
                }
            case 'shop_content.php':
                foreach ($explodedParams as $value) {
                    if (substr($value, 0, 5) == 'coID=') {
                        $coID = '=\'' . substr($value, 5, strlen($value)) . '\'';
                    }
                }
                break;

            case 'commerce_seo_url.php':
                $categoryId = ' IS NULL';
                $productId = ' IS NULL';
                $coID = ' IS NULL';
                $blogID = ' IS NULL';
                $blogCAT = ' IS NULL';

                if (substr_count($parameters, 'products_id=') > 0)
                    foreach ($explodedParams as $value) {
                        if (substr($value, 0, 12) == 'products_id=') {
                            $productId = '=\'' . substr($value, 12, strlen($value)) . '\'';
                        }
                    } elseif (substr_count($parameters, 'cPath=') > 0 && substr_count($parameters, 'blog_cat=') == 0)
                    foreach ($explodedParams as $value) {
                        if (substr($value, 0, 6) == 'cPath=') {
                            $categoryId = '=\'' . substr($value, 6, strlen($value)) . '\'';
                            $catIdFound = true;
                        }
                    } elseif (substr_count($parameters, 'coID=') > 0)
                    foreach ($explodedParams as $value) {
                        if (substr($value, 0, 5) == 'coID=') {
                            $coID = '=\'' . substr($value, 5, strlen($value)) . '\'';
                        }
                    } elseif (substr_count($parameters, 'blog_item') > 0) {
                    foreach ($explodedParams as $value) {
                        if (substr($value, 0, 10) == 'blog_item=') {
                            $blogID = '=\'' . substr($value, 10, strlen($value)) . '\'';
                        }
                    }
                } elseif (substr_count($parameters, 'blog_cat=') > 0)
                    foreach ($explodedParams as $value) {
                        if (substr($value, 0, 9) == 'blog_cat=') {
                            $blogCAT = '=\'' . substr($value, 9, strlen($value)) . '\'';
                        }
                    }
                break;
        }

        if ($this->blog_da())
            $blog_request = "AND blog_cat" . $blogCAT . " AND blog_id" . $blogID;
        else
            $blog_request = '';

        if ($productId == ' IS NULL') {
            $link_result = xtc_db_fetch_array(xtDBquery("SELECT 
								url_text
							FROM 
								commerce_seo_url
							WHERE 
								1>0
							AND 
								products_id" . $productId . "
							AND 
								categories_id" . $categoryId . "
							AND 
								content_group" . $coID . "
							" . $blog_request . "
							AND 
								language_id=" . $languageId_result['languages_id']));
        } else {
            $link_result = xtc_db_fetch_array(xtDBquery("SELECT 
								url_text
							FROM 
								" . TABLE_PRODUCTS_DESCRIPTION . "
							WHERE 
								products_id" . $productId . "
							AND 
								language_id=" . $languageId_result['languages_id']));
        }

        switch ($page) {
            // product
            case 'product_info.php':
                return $link . $link_result['url_text'];
                break;
            // Category
            case 'index.php':
                return $link . $link_result['url_text'];
                break;
            // Content
            case 'shop_content.php':
                return $link . $link_result['url_text'];
                break;
            // Blog
            case 'blog.php':
                if ($this->blog_da())
                    return $link . $link_result['url_text'];
                break;
            case 'commerce_seo_url.php':
                if ($productId != ' IS NULL' && $link_result['url_text'] != '')
                    return $link . $link_result['url_text'];
                elseif ($categoryId != ' IS NULL' && $link_result['url_text'] != '')
                    return $link . $link_result['url_text'];
                elseif ($coID != ' IS NULL' && $link_result['url_text'] != '')
                    return $link . $link_result['url_text'];
                elseif ($blogCAT != ' IS NULL' && $link_result['url_text'] != '')
                    return $link . $link_result['url_text'];
                elseif ($blogID != ' IS NULL' && $link_result['url_text'] != '')
                    return $link . $link_result['url_text'];
                else {
                    return $link . $language;
                }
                break;
        }
    }

    function getCategoryPathForProduct($productId, $language) {
        $xtcProductPath = xtc_get_product_path($productId);
        $pathExploded = explode('_', $xtcProductPath);
        foreach ($pathExploded as $value) {
            $productPath .= $this->getCategoryNameForId($value, $language);
            $pathExploded[0] <> '' ? $productPath .= '/' : false;
        }
        return $productPath;
    }

    function getCategoryPathForCategory($categoryId, $language) {
        $xtcCategoryPath = xtc_get_category_path($categoryId);
        $pathExploded = explode('_', $xtcCategoryPath);
        foreach ($pathExploded as $value) {
            $pathExploded[0] <> '' ? $categoryPath .= '/' : false;
            $categoryPath .= $this->getCategoryNameForId($value, $language);
        }
        $category_content = xtc_db_fetch_array(xtDBquery("SELECT 
								categories_content_status
    						FROM 
								" . TABLE_CATEGORIES . "
    						WHERE 
								categories_id = '" . $categoryId . "';"));
        if ($category_content['categories_content_status'] == 1) {
            return $categoryPath . '.html';
        } else {
            return $categoryPath . '/';
        }
    }

    function getCategoryNameForId($categoryId, $language) {
        $category = xtc_db_fetch_array(xtDBquery("SELECT 
								categories_name, 
								categories_url_alias
    						FROM 
								" . TABLE_CATEGORIES_DESCRIPTION . "
    						WHERE 
								categories_id = '" . $categoryId . "' 
							AND 
								categories_name != ''
							AND 
								language_id = '" . $language . "'
							"));

        if ($category['categories_url_alias'] != '')
            $cat_name = $category['categories_url_alias'];
        else
            $cat_name = $category['categories_name'];

        return cseo_get_url_friendly_text($cat_name);
    }

    function createSeoDBTable() {

        $useLanguageUrl = false;

        $commerce_seo_query = xtDBquery("TRUNCATE TABLE commerce_seo_url");
        if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL'] && MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL == 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL'] == 'True') {
            $useLanguageUrl = true;
        }

        // ******* Language ********//
        if ($useLanguageUrl) {
            $languages_query = xtDBquery("SELECT 
									code, 
									languages_id 
								FROM 
									" . TABLE_LANGUAGES);

            while ($lang = xtc_db_fetch_array($languages_query)) {
                $lang_seo_query = xtDBquery("INSERT INTO 
												commerce_seo_url (url_md5,url_text,language_id) 
											VALUES 
												('" . md5($lang['code']) . "','" . $lang['code'] . "/','" . $lang['languages_id'] . "')");
            }
        }

        // ******* Categories Index ********//
        $category_query = xtDBquery("SELECT
								cd.categories_id,
								cd.language_id,
								cd.categories_name,
								cd.categories_url_alias,
								l.code
							FROM
								" . TABLE_CATEGORIES_DESCRIPTION . " cd,
								" . TABLE_LANGUAGES . " l
							WHERE
								cd.language_id = l.languages_id
							AND 
								cd.categories_contents = '0'
							AND 
								cd.categories_blogs = '0'
							AND 
								cd.categories_name != ''
							");

        // Kategorien durchlaufen und Indexierung fuer SEO Tabelle vornehmen
        while ($categoryList = xtc_db_fetch_array($category_query)) {
            $categoryPath = $this->getCategoryPathForCategory($categoryList['categories_id'], $categoryList['language_id']);
            if ($useLanguageUrl) {
                $categoryLink = $categoryList['code'] . $categoryPath;
            } else {
                $categoryLink = substr($categoryPath, 1);
            }

            $categoryLink = $this->validateDBKeyLink($categoryLink, '');
            if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                $categoryLink = strtolower($categoryLink);
            }

            $category_seo_query = xtDBquery("INSERT INTO 
										commerce_seo_url (url_md5,url_text,categories_id,language_id) 
									VALUES 
										('" . md5($categoryLink) . "','" . $categoryLink . "','" . $categoryList['categories_id'] . "','" . $categoryList['language_id'] . "')");
        }

        if ($this->blog_da()) {
            // ******* Blog Kategorie Indexierung  ********//
            $blog_data = xtDBquery("SELECT
										bc.categories_id as blog_cat_id,
										l.code AS code,
										bc.language_id as blog_cat_lang,
										bc.titel as blog_cat_titel
									FROM
										" . TABLE_BLOG_CATEGORIES . " bc,
										" . TABLE_LANGUAGES . " l
									WHERE
										bc.language_id = l.languages_id");

            // Blog Datensaetze durchlaufen und Indexierung fuer SEO Tabelle vornehmen
            while ($blogList = xtc_db_fetch_array($blog_data)) {
                if ($useLanguageUrl)
                    $blogLink = $blogList['code'] . '/' . cseo_get_url_friendly_text($blogList['blog_cat_titel']);
                else
                    $blogLink = cseo_get_url_friendly_text($blogList['blog_cat_titel']);

                $blogLink = $this->validateDBKeyLink($blogLink, '');
                if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                    $blogLink = strtolower($blogLink);
                }
                $blog_seo_query = xtDBquery("INSERT INTO 
												commerce_seo_url (url_md5,url_text,blog_cat,language_id) 
											VALUES 
												('" . md5($blogLink) . "','" . $blogLink . "/','" . $blogList['blog_cat_id'] . "','" . $blogList['blog_cat_lang'] . "')");
            }

            // ******* Blog Indexierung  ********//
            $blog_data = xtDBquery("SELECT
								bi.item_id AS blog_item_id,
								bi.language_id AS blog_item_lang,
								bi.name AS blog_item_title,
								bc.categories_id AS blog_cat_id,
								l.code AS code,
								bc.titel AS blog_cat_titel
							FROM
								" . TABLE_BLOG_ITEMS . " bi,
							 	" . TABLE_BLOG_CATEGORIES . " bc,
							 	" . TABLE_LANGUAGES . " l
							 WHERE
							 	bi.language_id = l.languages_id
							 AND
							 	bc.language_id = bi.language_id
							 AND
							 	bi.categories_id = bc.categories_id");

            while ($blogList = xtc_db_fetch_array($blog_data)) {
                if ($useLanguageUrl) {
                    $blogLink = $blogList['code'] . '/' . cseo_get_url_friendly_text($blogList['blog_cat_titel']) . '/' . cseo_get_url_friendly_text($blogList['blog_item_title']) . '.html';
                } else {
                    $blogLink = cseo_get_url_friendly_text($blogList['blog_cat_titel']) . '/' . cseo_get_url_friendly_text($blogList['blog_item_title']) . '.html';
                }

                $blogLink = $this->validateDBKeyLink($blogLink, '');

                if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                    $blogLink = strtolower($blogLink);
                }

                $blog_seo_query = xtDBquery("INSERT INTO 
												commerce_seo_url (url_md5,url_text,blog_id,language_id) 
											VALUES 
												('" . md5($blogLink) . "','" . $blogLink . "','" . $blogList['blog_item_id'] . "','" . $blogList['blog_item_lang'] . "')");
            }
        }

        // ******* Content Index ********//
        $content_query = xtDBquery("SELECT
										cm.content_group,
										cm.languages_id,
										cm.content_title,
										cm.content_url_alias,
										l.code
									FROM
										" . TABLE_CONTENT_MANAGER . " cm,
										" . TABLE_LANGUAGES . " l
									WHERE
										cm.languages_id = l.languages_id");

        // Content Datensaetze durchlaufen und Indexierung fuer SEO Tabelle vornehmen
        while ($contentList = xtc_db_fetch_array($content_query)) {
            if ($contentList['content_url_alias'] != '') {
                $content_url = $contentList['content_url_alias'];
            } else {
                $content_url = $contentList['content_title'];
            }

            if ($useLanguageUrl) {
                $contentLink = $contentList['code'] . '/' . cseo_get_url_friendly_text($content_url);
            } else {
                $contentLink = cseo_get_url_friendly_text($content_url);
            }

            $contentLink = $this->validateDBKeyLink($contentLink, '');

            if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                $contentLink = strtolower($contentLink);
            }

            $content_seo_query = xtDBquery("INSERT INTO 
												commerce_seo_url (url_md5,url_text,content_group,language_id) 
											VALUES 
												('" . md5($contentLink) . "','" . $contentLink . '.html' . "','" . $contentList['content_group'] . "','" . $contentList['languages_id'] . "')");
        }
    }

    function createSeoDBTableProduct($_count, $productID) {
        //Produkte Create SEO-URL Anfang
        $useLanguageUrl = false;

        if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL'] && MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL == 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL'] == 'True') {
            $useLanguageUrl = true;
        }

        // ******* Products Index ********//
        $product_query = xtDBquery("SELECT
								products_id,
								language_id,
								products_name,
								products_url_alias,
								url_old_text
							FROM
								" . TABLE_PRODUCTS_DESCRIPTION . "
							WHERE products_id=" . $productID);

        while ($productList = xtc_db_fetch_array($product_query)) {
            if ((!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LENGHT'] && MODULE_COMMERCE_SEO_URL_LENGHT == 'True') || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LENGHT'] == 'True') {
                $productPath = '';
                if ((!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LENGHT'] && MODULE_COMMERCE_SEO_URL_LENGHT == 'True') || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LENGHT'] == 'True' && (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_OLD_REWRITE'] && MODULE_COMMERCE_SEO_URL_OLD_REWRITE == 'True') || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_OLD_REWRITE'] == 'True') {
                    $productOldPath = $this->getCategoryPathForProduct($productList['products_id'], $productList['language_id']);
                }
            } else {
                $productPath = $this->getCategoryPathForProduct($productList['products_id'], $productList['language_id']);
            }

            if ($productList['products_url_alias'] != '') {
                $products_name = $productList['products_url_alias'];
            } else {
                if ($productList['products_name'] == '') {
                    $products_name = 'noname-';
                } else {
                    $products_name = trim($productList['products_name']);
                }
            }

            if ($useLanguageUrl) {
                $lang_query = xtc_db_fetch_array(xtDBquery("SELECT code FROM " . TABLE_LANGUAGES . " WHERE languages_id = '" . $productList['language_id'] . "'"));
                $productLink = $lang_query['code'] . '/' . $productPath . cseo_get_url_friendly_text($products_name);
            } else {
                $productLink = $productPath . cseo_get_url_friendly_text($products_name);
                if ((!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LENGHT'] && MODULE_COMMERCE_SEO_URL_LENGHT == 'True') || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LENGHT'] == 'True' && (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_OLD_REWRITE'] && MODULE_COMMERCE_SEO_URL_OLD_REWRITE == 'True') || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_OLD_REWRITE'] == 'True') {
                    if ($productList['url_old_text'] == '') {
                        $productOldLink = $productOldPath . cseo_get_url_friendly_text($products_name) . '.html';
                    } else {
                        $productOldLink = $productList['url_old_text'];
                    }
                }
            }

            if ((!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS'] && MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS == 'True') || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS'] == 'True') {
                if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                    $productListname = addslashes(strtolower($productList['products_name']));
                } else {
                    $productListname = addslashes($productList['products_name']);
                }

                $doppel_query = xtc_db_query("SELECT COUNT(products_name) AS counter  FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_name='" . $productListname . "'");
                $doppel_id = xtc_db_fetch_array($doppel_query);
                if ($doppel_id['counter'] > 1) {
                    $productLink = $productLink . '-' . $productList['products_id'];
                }
            }

            if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                $productLink = stripslashes(strtolower($productLink));
            } else {
                $productLink = stripslashes($productLink);
            }

            $product_seo_query = xtDBquery("UPDATE  
											" . TABLE_PRODUCTS_DESCRIPTION . " 
											SET  
												url_text = '" . $productLink . '.html' . "', 
												url_old_text = '" . $productOldLink . "', 
												url_md5 = '" . md5($productLink) . "'
											WHERE 
												products_id = '" . $productList['products_id'] . "'
											AND
												language_id = '" . $productList['language_id'] . "'
											LIMIT 1
											");
        }
    }

    //Produkte Create SEO-URL Ende

    function updateSeoDBTable($elementType, $operation, $id, $old_url = '') {

        switch ($elementType) {
            // ******* Product Update ********//
            case 'product':
                $result_query = xtDBquery("SELECT
											pd.products_id, 
											pd.language_id, 
											pd.products_name, 
											pd.products_url_alias, 
											l.code
										FROM
											" . TABLE_PRODUCTS_DESCRIPTION . " pd,
											" . TABLE_LANGUAGES . " l
										WHERE
											pd.products_id=" . $id . "
										AND
											pd.language_id  = l.languages_id
										");

                while ($resultList = xtc_db_fetch_array($result_query)) {
                    if (MODULE_COMMERCE_SEO_URL_LENGHT == 'True') {
                        if ((!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LENGHT'] && MODULE_COMMERCE_SEO_URL_LENGHT == 'True') || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LENGHT'] == 'True' && (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_OLD_REWRITE'] && MODULE_COMMERCE_SEO_URL_OLD_REWRITE == 'True') || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_OLD_REWRITE'] == 'True') {
                            $productPath = '';
                            $productOldPath = $this->getCategoryPathForProduct($resultList['products_id'], $resultList['language_id']);
                        } else {
                            $productPath = '';
                        }
                    } else {
                        $productPath = $this->getCategoryPathForProduct($resultList['products_id'], $resultList['language_id']);
                    }

                    if ($resultList['products_url_alias'] != '') {
                        $product_name = $resultList['products_url_alias'];
                    } else {
                        if ($resultList['products_name'] == '') {
                            $product_name = 'noname-';
                        } else {
                            $product_name = $resultList['products_name'];
                        }
                    }

                    if (MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL == 'True') {
                        $productLink = $resultList['code'] . '/' . $productPath . cseo_get_url_friendly_text($product_name);
                    } else {
                        if ((!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LENGHT'] && 
						MODULE_COMMERCE_SEO_URL_LENGHT == 'True') || 
						$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LENGHT'] == 'True' && 
						(!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_OLD_REWRITE'] && MODULE_COMMERCE_SEO_URL_OLD_REWRITE == 'True') || 
						$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_OLD_REWRITE'] == 'True') {
                            if ($old_url == '') {
								$productOldLink = $productOldPath . cseo_get_url_friendly_text($product_name) . '.html';
							} else {
								$productOldLink = $old_url[2];
							}
                            $productLink = $productPath . cseo_get_url_friendly_text($product_name);
                        } else {
                            $productLink = $productPath . cseo_get_url_friendly_text($product_name);
                        }
                    }
                    if ((!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS'] && MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS == 'True') || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS'] == 'True') {
                        if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                            $productListname = addslashes(strtolower($resultList['products_name']));
                        } else {
                            $productListname = addslashes($resultList['products_name']);
                        }

                        $doppel_query = xtc_db_query("SELECT COUNT(products_name) AS counter  FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_name='" . $productListname . "'");
                        $doppel_id = xtc_db_fetch_array($doppel_query);
                        if ($doppel_id['counter'] > 1) {
                            $productLink = $productLink . '-' . $resultList['products_id'];
                        }
                    }

                    if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                        $productLink = strtolower($productLink);
                    }

                    $commerce_update_seo_query = xtDBquery("UPDATE 
														" . TABLE_PRODUCTS_DESCRIPTION . "  
													SET 
														url_text='" . $productLink . '.html' . "', 
														url_old_text='" . $productOldLink . "', 
														url_md5='" . md5($productPath) . "'
													WHERE 
														products_id='" . $id . "' 
													AND 
														language_id='" . $resultList['language_id'] . "'");
                }
                break;

            // ******* Category Update ********//
            case 'category':
                $result_query = xtDBquery("SELECT
												cd.categories_id,
												cd.language_id,
												l.code
											FROM
												" . TABLE_CATEGORIES_DESCRIPTION . " cd,
												" . TABLE_LANGUAGES . " l
											WHERE
												cd.categories_id = '" . $id . "'
											AND
												cd.language_id = l.languages_id");

                while ($resultList = xtc_db_fetch_array($result_query)) {
                    $categoryPath = $this->getCategoryPathForCategory($resultList['categories_id'], $resultList['language_id']);
                    if (MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL == 'True') {
                        $categoryLink = $resultList['code'] . $categoryPath;
                    } else {
                        $categoryLink = substr($categoryPath, 1);
                    }

                    $old_categorylink = xtc_db_fetch_array(xtDBquery("SELECT url_text
																		FROM 
																			commerce_seo_url
																		WHERE 
																			categories_id='" . $id . "'
																		AND 
																			language_id='" . $resultList['language_id'] . "'"));

                    if ($categoryLink != $old_categorylink['url_text']) {
                        $categoryLink = $this->validateDBKeyLink($categoryLink, '');
                        if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                            $categoryLink = strtolower($categoryLink);
                        }

						if($old_categorylink['url_text'] != NULL) {
							$commerce_update_seo_query = xtDBquery("UPDATE 
																		commerce_seo_url 
																	SET 
																		url_md5='" . md5($categoryLink) . "', url_text='" . $categoryLink . "' 
																	WHERE 
																		categories_id='" . $id . "' 
																	AND 
																		language_id='" . $resultList['language_id'] . "'");
						} else {
							$commerce_insert_seo_query = xtDBquery("INSERT 
																		commerce_seo_url 
																	SET 
																		url_md5='" . md5($categoryLink) . "', 
																		url_text='" . $categoryLink . "',
																		categories_id='" . $id . "',
																		language_id='" . $resultList['language_id'] . "'");	
						}
                    }
                }
                break;

            case 'content':
                $content_query = xtDBquery("SELECT
										cm.content_group,
										cm.languages_id,
										cm.content_title,
										cm.content_url_alias,
										l.code
									FROM
										" . TABLE_CONTENT_MANAGER . " cm,
										" . TABLE_LANGUAGES . " l
									WHERE
										cm.content_id = '" . $id . "'
									AND
										cm.languages_id = l.languages_id");

                while ($content = xtc_db_fetch_array($content_query)) {
                    if ($content['content_url_alias'] != '') {
                        $content_url = $content['content_url_alias'];
                    } else {
                        $content_url = $content['content_title'];
                    }

                    if ($useLanguageUrl) {
                        $contentLink = $content['code'] . '/' . cseo_get_url_friendly_text($content_url);
                    } else {
                        $contentLink = cseo_get_url_friendly_text($content_url);
                    }

                    $old_contentlink = xtc_db_fetch_array(xtDBquery("SELECT 
																		url_text
																	FROM 
																		commerce_seo_url
																	WHERE 
																		content_group='" . $content['content_group'] . "'
																	AND 
																		language_id='" . $resultList['language_id'] . "'"));

                    if ($contentLink != $old_contentlink['url_text']) {
                        $contentLink = $this->validateDBKeyLink($contentLink, '');
                        if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                            $contentLink = strtolower($contentLink);
                        }
                        $content_seo_query = xtDBquery("UPDATE 
														commerce_seo_url 
													SET 
														url_md5 = '" . md5($contentLink) . "', url_text = '" . $contentLink . '.html' . "' 
													WHERE 
														content_group = '" . $content['content_group'] . "' 
													AND 
														language_id='" . $content['languages_id'] . "'");
                    }
                }

                break;
            // ******* Blog Update ********//
            case 'blog':
                if ($this->blog_da()) {
                    // ******* Get Blog Information ********//
                    $result_query = xtDBquery("SELECT
											bi.id AS blog_item_id, 
											bi.language_id AS blog_item_lang, 
											bi.title AS blog_item_title, 
											bc.id AS blog_cat_id, 
											l.code AS code, 
											bc.titel AS blog_cat_titel 
										FROM 
											" . TABLE_BLOG_ITEMS . " bi,
											" . TABLE_BLOG_CATEGORIES . " bc,
											" . TABLE_LANGUAGES . " l
										WHERE 
											bi.language_id = l.languages_id
										AND 
											bc.language_id = bi.language_id
										AND 
											bi.categories_id = bc.id");

                    while ($blogList = xtc_db_fetch_array($result_query)) {
                        // URL mit oder ohne ISO Code anlegen
                        if (MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL == 'True') {
                            $blogLink = $blogList['code'] . cseo_get_url_friendly_text($blogList['blog_cat_titel']) . '/' . cseo_get_url_friendly_text($blogList['blog_item_title']) . '.html';
                        } else {
                            $blogLink = cseo_get_url_friendly_text($blogList['blog_cat_titel']) . '/' . cseo_get_url_friendly_text($blogList['blog_item_title']) . '.html';
                        }

                        $blogLink = $this->validateDBKeyLink($blogLink, '');
                        if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                            $blogLink = strtolower($blogLink);
                        }
                        $commerce_update_seo_query = xtDBquery("UPDATE 
															commerce_seo_url
														SET 
															url_md5 = '" . md5($blogLink) . "', url_text = '" . $blogLink . "' 
														WHERE 
															blog_id = '" . $blogList['blog_item_id'] . "' 
														AND 
															language_id = '" . $blogList['language_id'] . "'");
                    }
                }
                break;
        }
    }

    function insertSeoDBTable($elementType) {
        // Create type-depending URL
        switch ($elementType) {

            // ******* Insert Product ********//
            case 'product':
                // Ermitteln, welche Produkte existieren, die noch nicht in commerce_seo_url indiziert sind
                $result_query = xtDBquery("SELECT 
										pd.products_id, 
										pd.language_id, 
										pd.products_name, 
										pd.products_url_alias, 
										l.code
									FROM 
										" . TABLE_PRODUCTS_DESCRIPTION . " pd
									INNER JOIN
										" . TABLE_LANGUAGES . " l ON (pd.language_id = l.languages_id)
									WHERE 
										pd.url_text IS NULL;");

                // Anlegen der neuen Datensaetze
                while ($resultList = xtc_db_fetch_array($result_query)) {
                    if (MODULE_COMMERCE_SEO_URL_LENGHT == 'True') {
                        $productPath = '';
                    } else {
                        $productPath = $this->getCategoryPathForProduct($resultList['products_id'], $resultList['language_id']);
                    }

                    if ($resultList['products_url_alias'] != '') {
                        $product_name = $resultList['products_url_alias'];
                    } else {
                        $product_name = $resultList['products_name'];
                    }

                    // URL mit oder ohne ISO Code anlegen
                    if (MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL == 'True') {
                        $productLink = $resultList['code'] . '/' . $productPath . cseo_get_url_friendly_text($product_name);
                    } else {
                        $productLink = $productPath . cseo_get_url_friendly_text($product_name);
                    }

                    if ((!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS'] && MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS == 'True') || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_INDEX_AVOIDDUPLICATEPRODUCTS'] == 'True') {
                        $doppel_query = xtc_db_query("SELECT COUNT(products_name) AS counter  FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_name = '" . addslashes($resultList['products_name']) . "'");
                        $doppel_id = xtc_db_fetch_array($doppel_query);
                        if ($doppel_id['counter'] > 1) {
                            $productLink = $productLink . '-' . $resultList['products_id'];
                        }
                    }

                    if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                        $productLink = strtolower($productLink);
                    }
                    $product_seo_query = xtDBquery("UPDATE  
											" . TABLE_PRODUCTS_DESCRIPTION . " 
											SET  
												url_text = '" . $productLink . '.html' . "', 
												url_md5 = '" . md5($productLink) . "'
											WHERE 
												products_id = '" . $resultList['products_id'] . "'
											AND
												language_id = '" . $resultList['language_id'] . "' LIMIT 1");
                }
                break;

            // ******* Insert Category ********//
            case 'category':
                // Ermitteln, welche Kategorien existieren, die noch nicht in commerce_seo_url indiziert sind
                $result_query = xtDBquery("SELECT 
										cd.categories_id,
										cd.language_id,
										cd.categories_name,
										l.code
									FROM 
										" . TABLE_CATEGORIES_DESCRIPTION . " cd
									LEFT JOIN 
										commerce_seo_url AS cseo ON (cd.categories_id = cseo.categories_id)
									INNER JOIN 
										" . TABLE_LANGUAGES . " l ON (cd.language_id = l.languages_id)
									WHERE 
										cseo.categories_id IS NULL");

                while ($resultList = xtc_db_fetch_array($result_query)) {
                    $categoryPath = $this->getCategoryPathForCategory($resultList['categories_id'], $resultList['language_id']);

                    // URL mit oder ohne ISO Code anlegen
                    if (MODULE_COMMERCE_SEO_INDEX_LANGUAGEURL == 'True') {
                        $categoryLink = $resultList['code'] . $categoryPath;
                    } else {
                        // Remove leading Slash from URL (/)
                        $categoryLink = substr($categoryPath, 1);
                    }

                    $categoryLink = $this->validateDBKeyLink($categoryLink, '');
                    if (!$_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] && MODULE_COMMERCE_SEO_URL_LOWERCASE === 'True' || $_REQUEST['configuration']['MODULE_COMMERCE_SEO_URL_LOWERCASE'] === 'True') {
                        $categoryLink = strtolower($categoryLink);
                    }
                    $category_seo_query = xtDBquery("INSERT INTO 
														commerce_seo_url (url_md5,url_text,categories_id,language_id) 
													VALUES 
														('" . md5($categoryLink) . "','" . $categoryLink . "','" . $resultList['categories_id'] . "','" . $resultList['language_id'] . "')");
                }
                break;
        }
    }

    function validateDBKeyLink($urlKey, $counter) {
        $product_query = xtDBquery("SELECT 
										url_text 
									FROM 
										commerce_seo_url 
									WHERE 
										url_md5='" . md5($urlKey . $counter) . "' ");

        if (xtc_db_num_rows($product_query) == 0) {
            return $urlKey . $counter;
        } else {
            $counter++;
            return $this->validateDBKeyLink($urlKey . '-' . $counter . '/');
        }
    }

    function getIdForURL($linkUrl, $type) {
        if ($this->blog_da()) {
            $id = xtc_db_fetch_array(xtDBquery("SELECT 
									products_id, 
									categories_id, 
									content_group, 
									blog_id 
								FROM 
									commerce_seo_url 
								WHERE 
									url_md5 = '" . md5($linkUrl) . "' LIMIT 0,1"));
        } else {
            $id = xtc_db_fetch_array(xtDBquery("SELECT 
									products_id, 
									categories_id, 
									content_group 
								FROM 
									commerce_seo_url 
								WHERE 
									url_md5 = '" . md5($linkUrl) . "' LIMIT 0,1"));
        }

        // Check the URL type
        switch ($type) {
            case 'product':
                return $id['products_id'];
            case 'category':
                return $id['categories_id'];
            case 'content':
                return $id['content_group'];
            case 'blog':
                return $id['blog_id'];
        }
    }

    function getIdForXTCSumaFriendlyURL($fileName) {
        if (($fileName == 'product_info.php' && $_GET['products_id'] != '' && $_GET['action'] == '') || ($fileName == 'index.php' && $_GET['cat'] != '' && $_GET['page'] == '' && $_GET['action'] == '') || ($fileName == 'index.php' && $_GET['cPath'] != '' && $_GET['page'] == '' && $_GET['action'] == '') || ($fileName == 'shop_content.php' && $_GET['coID'] != '' && $_GET['action'] == '')) {
            require_once(DIR_WS_CLASSES . 'class.language.php');
            !$temp_lng ? $temp_lng = new language(xtc_input_validation($_GET['language'], 'char', '')) : false;

            if (!isset($_SESSION['language']) || isset($_GET['language'])) {

                if (!isset($_GET['language']))
                    $temp_lng->get_browser_language();
                $_SESSION['languages_id'] = $temp_lng->language['id'];
            }

            if (isset($_SESSION['language']) && !isset($_SESSION['language_charset'])) {
                $_SESSION['languages_id'] = $temp_lng->language['id'];
            }
        }

        // *******************************************************************
        // * PRODUCT 301 REDIRECT ********************************************
        // *******************************************************************
        // e.g.: product_info.php?info=p124_Produkt-1.html
        if ($fileName == 'product_info.php' && $_GET['info'] != '' && $_GET['products_id'] == '' && $_GET['action'] == '') {
            $parameters = $_GET['info'];
            $explodedParams = explode('_', $parameters);
            $productId = substr($explodedParams[0], 1, 255);

            $product_query = xtc_db_query("SELECT products_name FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_id = '" . (int) $productId . "' AND language_id='" . (int) $_SESSION['languages_id'] . "'");

            if ($productId != '') {
                $product_link = xtc_db_fetch_array($product_query);
                $redirectLink = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($productId, $product_link['products_name']));
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: " . $redirectLink);
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
                header('Status: 404 Not Found');
                header('Content-type: text/html');
                header("Location: " . xtc_href_link('404.php'));
            }
        }

        // e.g.: product_info.php/products_id/312 or product_info.php?products_id=312
        if ($fileName == 'product_info.php' && $_GET['products_id'] != '' && $_GET['info'] == '' && $_GET['action'] == '') {
            $parameters = $_GET['products_id'];

            $product_query = xtc_db_query("SELECT products_name FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_id = '" . (int) $parameters . "' AND language_id='" . (int) $_SESSION['languages_id'] . "'");

            if ($parameters != '') {
                $product_link = xtc_db_fetch_array($product_query);
                $redirectLink = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link((int) $parameters, $product_link['products_name']));
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: " . $redirectLink);
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
                header('Status: 404 Not Found');
                header('Content-type: text/html');
                header("Location: " . xtc_href_link('404.php'));
            }
        }

        // *******************************************************************
        // * CATEGORY 301 REDIRECT *******************************************
        // *******************************************************************
        // Create 301 redirect for CATEGORY LINKS (cat)
        // e.g.: index.php?cat=c7_Kategorie-7.html or index.php/cat/c7_Kategorie-7.html or index.php?cat=7
        if ($fileName == 'index.php' && $_GET['cat'] != '' && $_GET['cPath'] == '' && $_GET['page'] == '' && $_GET['action'] == '') {
            $parameters = $_GET['cat'];
            $explodedParams = explode('_', $parameters);
            $parmCatID = substr($explodedParams[0], 0, 1);
            if ($parmCatID == 'c') {
                $CatId = substr($explodedParams[0], 1, 255);
            } else {
                $CatId = substr($explodedParams[0], 0, 255);
            }
            $cat_query = xtc_db_query("SELECT url_text FROM commerce_seo_url WHERE categories_id = '" . (int) $CatId . "' AND language_id='" . (int) $_SESSION['languages_id'] . "'");

            if (xtc_db_num_rows($cat_query) > 0) {
                $cat_link = xtc_db_fetch_array($cat_query);
                $redirectLink = xtc_href_link(FILENAME_DEFAULT, xtc_category_link($CatId, $cat_link['url_text']));
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: " . $redirectLink);
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
                header('Status: 404 Not Found');
                header('Content-type: text/html');
                header("Location: " . xtc_href_link('404.php'));
            }
        }

        // Create 301 redirect for CATEGORY LINKS (cPath)
        // e.g.: index.php?cPath=c7_Kategorie-5.html or index.php/cPath/c7_Kategorie-5.html or index.php?cPath=7
        if ($fileName == 'index.php' && $_GET['cPath'] != '' && $_GET['cat'] == '' && $_GET['page'] == '' && $_GET['action'] == '') {
            $parameters = $_GET['cPath'];
            $explodedParams = explode('_', $parameters);
            $parmCatID = substr($explodedParams[0], 0, 1);
            if ($parmCatID == 'c') {
                $CatId = substr($explodedParams[0], 1, 255);
            } else {
                $CatId = substr($explodedParams[0], 0, 255);
            }
            $cat_query = xtc_db_query("SELECT url_text FROM commerce_seo_url WHERE categories_id = '" . (int) $CatId . "' AND language_id='" . (int) $_SESSION['languages_id'] . "'");

            if (xtc_db_num_rows($cat_query) > 0) {
                $cat_link = xtc_db_fetch_array($cat_query);
                $redirectLink = xtc_href_link(FILENAME_DEFAULT, xtc_category_link($CatId, $cat_link['url_text']));
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: " . $redirectLink);
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
                header('Status: 404 Not Found');
                header('Content-type: text/html');
                header("Location: " . xtc_href_link('404.php'));
            }
        }

        // *******************************************************************
        // * CONTENT 301 REDIRECT ********************************************
        // *******************************************************************
        if ($fileName == 'shop_content.php' && $_GET['coID'] != '' && $_GET['action'] == '') {
            $parameters = 'coID=' . (int) $_GET['coID'];
            $redirectLink = $this->getContentLink($parameters, $connection, (int) $_SESSION['languages_id']);
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $redirectLink);
        }

        return false;
    }

}
