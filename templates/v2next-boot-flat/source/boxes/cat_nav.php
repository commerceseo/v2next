<?php

/* -----------------------------------------------------------------
 * 	$Id: cat_nav.php 1001 2014-05-02 17:00:03Z sbraeutig $
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

$box_smarty = new smarty;
$box_content = '';
$box_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$box_smarty->assign('language', $_SESSION['language']);
if (!CacheCheck()) {
    $cache = false;
    $box_smarty->caching = false;
} else {
    $cache = true;
    $box_smarty->caching = true;
    $box_smarty->cache_lifetime = CACHE_LIFETIME;
    $box_smarty->cache_modified_check = CACHE_CHECK;
    $cache_id = $_SESSION['language'] . '_StatID-' . $_SESSION['customers_status']['customers_status_id'];
    if (!empty($GLOBALS['cPath']))
        $cache_id .= '_cPath-' . $GLOBALS['cPath'];
    elseif (!empty($_GET['coID']))
        $cache_id .= '_coID-' . $_GET['coID'];
    else
        $cache_id .= '_Script-' . basename($_SERVER[SCRIPT_NAME]);
}


$CatConfig = array(
    'MinLevel' => 5,
    'MaxLevel' => false,
    'HideEmpty' => false,
    'ShowAktSub' => true,
    'ListPrefix' => 'catmenu',
    'MarkAktivLink' => true,
    'LinkCurrent' => 'active',
    'LinkCurrentParent' => 'active',
    'MarkAktivList' => true,
    'ListCurrent' => 'active',
    'ListCurrentParent' => 'active',
    'MarkSubMenue' => true,
    'SubMenueCss' => '',
    'ShowCssIdList' => false,
    'CssPrefixList' => 'MyCat',
    'ShowCssIdLink' => false,
    'CssPrefixLink' => 'MyCatLink',
    'CountPre' => '<em>(',
    'CountAfter' => ')</em>',
    // Tags außerhalb des Links?
    'LinkPre' => false, // z.B. '<div>',
    'LinkAfter' => false, // z.B. '</div>',
    // Tags innerhalb des Links?
    'NamePre' => false, // z.B. '<span>',
    'NameAfter' => false, // z.B. '</span>',
    'CssMarkersToList' => false, // Gefundene Marker zur Liste?
    'CssMarkersToLink' => false  // Gefundene Marker zum Link?
);
// -----------------------------------------------------------------------------------
$CurrentURL = xtc_href_link(basename($_SERVER[SCRIPT_NAME]), xtc_get_all_get_params(array('CSEOsid')));

// -----------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------
//	Findet heraus, ob Kategorie $category_id aktive (und für die Kundengruppe 
//	zugelassene) Artikel enthält. 
// -----------------------------------------------------------------------------------
//	Im Gegensatz zu xtc_count_products_in_category()
// 	werden hierbei die Berechtigungen und der FSK-Status geprüft.
// -----------------------------------------------------------------------------------
function countProductsInCat($category_id) {

    $products_count = 0;
    if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
        $fsk_lock = "AND \tp.products_fsk18!=1 ";
    if (GROUP_CHECK == 'true')
        $prod_group_check = "AND \tp.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";

    $products = xtc_db_fetch_array(xtDBquery("
			SELECT count(*) as total 
			FROM 
				" . TABLE_PRODUCTS . " AS p
			INNER JOIN
				" . TABLE_PRODUCTS_TO_CATEGORIES . " AS p2c (p.products_id = p2c.products_id AND p2c.categories_id = '" . $category_id . "')
			WHERE 	 
				" . $prod_group_check . "
				" . $fsk_lock . " 
			AND
				p.products_status = '1' 
			;"));
    $products_count += $products['total'];

    if (GROUP_CHECK == 'true')
        $cat_group_check = "AND \tgroup_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
    $child_categories_query = xtDBquery("
			SELECT 	categories_id 
			FROM 	" . TABLE_CATEGORIES . " 
			WHERE 	parent_id = '" . $category_id . "' 
			" . $cat_group_check . "
			AND 	categories_status = '1'");
    if (xtc_db_num_rows($child_categories_query, true)) {
        while ($child_categories = xtc_db_fetch_array($child_categories_query, true)) {
            $products_count += countProductsInCat($child_categories['categories_id']);
        }
    }

    return $products_count;
}

// -----------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------
//	... ist $CurrentURL im Kategorien-Pfad drin?
// -----------------------------------------------------------------------------------
function isInPath($CurrentURL, $CatID = false) {
    global $foo;
    if ($CatID) {
        if ($CurrentURL == $foo[$CatID]['link']) {
            return true;
        } elseif (is_array($foo[$CatID]['subcats'])) {
            foreach ($foo[$CatID]['subcats'] as $SubCatID) {
                if (isInPath($CurrentURL, $SubCatID))
                    return true;
            }
        }
    }
    return false;
}

// -----------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------
//	Hauptfunktion
// -----------------------------------------------------------------------------------
function xtc_show_category_superfish($cid, $level, $foo, $cpath) {

    global $old_level,
    $categories_string,
    $CatConfig,
    $CurrentURL;

    $CatConfig['MaxLevel'] = intval($CatConfig['MaxLevel']);
    $CatConfig['MinLevel'] = intval($CatConfig['MinLevel']);

    // 1) Zählen ist nicht immer nötig
    if ($CatConfig['HideEmpty'] || SHOW_COUNTS == 'true') {
        $pInCat = countProductsInCat($cid);
	}

    // 2) Überprüfen, ob Kategorie Produkte enthält
    if ($CatConfig['HideEmpty']) {
        $Empty = true;
        if ($pInCat > 0) {
            $Empty = false;
		}
    } else {
        $Empty = false;
    }

    // 3) Überprüfen, ob Kategorie gezeigt werden soll
    $Show = false;
    if ($CatConfig['HideEmpty']) {
        if (!$Empty)
            $Show = true;
    } else {
        $Show = true;
    }

    // 3) Überprüfen, ob Unterkategorien gezeigt werden sollen
    $ShowSub = false;
    if ($CatConfig['MaxLevel']) {
        if ($level < $CatConfig['MinLevel'] || $level < $CatConfig['MaxLevel']) {
            $ShowSub = true;
		}
    } else {
        $ShowSub = true;
    }

    if ($Show) { // Wenn Kategorie gezeigt werden soll ....
        if ($cid != 0) {
            $category_path = explode('_', $GLOBALS['cPath']);
			// $category_path = explode('_',$cPath);
            $in_path = in_array($cid, $category_path);
            $this_category = array_pop($category_path);
            if (empty($this_category)) {
                if (isInPath($CurrentURL, $cid)) {
                    $in_path = true;
				}
            }
            $ProductsCount = false;
            if (SHOW_COUNTS == 'true') {
                $ProductsCount = ' ' . $CatConfig['CountPre'] . $pInCat . $CatConfig['CountAfter'];
			}

            // Aktiv - Nicht Aktiv usw.
            $Collapse = $Expand = $Aktiv = $AktivList = $AktivLink = $CssClassMarker = false;

            // Nach Collapse- bzw. Expand-Markern suchen
            if (strstr(strtolower($foo[$cid]['heading']), '{#collapse#}'))
                $Collapse = true;
            if (strstr(strtolower($foo[$cid]['heading']), '{#expand#}'))
                $Expand = true;

            $ListClass[] = $CatConfig['ListPrefix'];

            // Nach CSS-Markern suchen
            if ($CatConfig['CssMarkersToList'] || $CatConfig['CssMarkersToLink']) {
                if (preg_match("/\{\#class\:([^\#\}]+)\#\}/i", $foo[$cid]['heading'], $Treffer)) {
                    $CssClassMarker = trim($Treffer[1]);
                    if ($CatConfig['CssMarkersToList'] && !empty($CssClassMarker))
                        $ListClass[] = $CssClassMarker;
                    if ($CatConfig['CssMarkersToLink'] && !empty($CssClassMarker))
                        $LinkClass[] = $CssClassMarker;
                }
            }

            if ($this_category == $cid || $foo[$cid]['link'] == $CurrentURL) {
                // Wenn Kategorie aktiv ist
                if ($CatConfig['MarkAktivLink']) {
                    $LinkClass[] = $CatConfig['LinkCurrent'];
                }
                if ($CatConfig['MarkAktivList']) {
                    $ListClass[] = $CatConfig['ListCurrent'];
                }
                $Aktiv = true;
            } elseif ($in_path) {
                // Wenn Oberkategorie aktiv ist
                if ($CatConfig['MarkAktivLink']) {
                    $LinkClass[] = $CatConfig['LinkCurrentParent'];
                }
                if ($CatConfig['MarkAktivList']) {
                    $ListClass[] = $CatConfig['ListCurrentParent'];
                }
                $Aktiv = true;
            }

            // Hat ein SubMenue - hat kein SubMenue
            // CSS-Klasse festlegen
            if ($CatConfig['MarkSubMenue'] && $foo[$cid]['subcats']) {
                $ListClass[] = $CatConfig['SubMenueCss'];
                $DropdownClose = '</li>';
            }

            // Quelltext einrücken
            $Tabulator = str_repeat("\t", $level + 1);

            if ($CatConfig['ShowCssIdList']) {
                $ListID[] = $CatConfig['CssPrefixList'] . $cid;
            }

            if ($CatConfig['ShowCssIdLink']) {
                $LinkID[] = $CatConfig['CssPrefixLink'] . $cid;
            }

            // Navigations-Liste hierarchisch ...
            if ($old_level) {
                if ($old_level < $level) {
                    $Pre = str_replace("<ul>", $Tabulator . "<ul" . $UlListClass, $Pre) . "\n";
                } else {
                    $Pre = "</li>\n";
                    if ($old_level > $level) {
                        // Listenpunkte schließen
                        // Quelltext einrücken
                        for ($counter = 0; $counter < $old_level - $level; $counter++) {
                            $Pre .= str_repeat("\t", $old_level - $counter + 1) . "</ul>\n" . str_repeat("\t", $old_level - $counter) . "</li>\n";
                        }
                    }
                }
            }

            if (is_array($ListClass)) {
                $ListClass = ' class="' . implode(' ', $ListClass) . '"';
            }
            if (is_array($ListID)) {
                $ListID = ' id="' . implode(' ', $ListID) . '"';
            }
            if (is_array($LinkClass)) {
                $LinkClass = ' class="' . implode(' ', $LinkClass) . '"';
            }
            if (is_array($LinkID)) {
                $LinkID = ' id="' . implode(' ', $LinkID) . '"';
            }

            if ($CatConfig['MarkSubMenue'] && $foo[$cid]['subcats']) {

                // Listenpunkte zusammensetzen wenn Unterkategorie vorhanden ist
                if ($level > 1) {
                    $DropdownClass = str_replace('class="', 'class="dropdown-submenu ', $ListClass);
                    $linkhref = 'href="' . $foo[$cid]['link'] . '"';
                } else {
                    $DropdownClass = str_replace('class="', 'class="dropdown ', $ListClass);
                    $caret = '<b class="caret"></b>';
                    $linkhref = 'href="' . $foo[$cid]['link'] . '"';
                }
                $categories_string .= $Pre . 
									$Tabulator .
									'<li' . 
									$ListID . 
									$DropdownClass . 
									'><a ' . 
									$linkhref . ' class="dropdown-toggle">' . 
									$foo[$cid]['name'] . 
									' ' . 
									$caret . 
									'</a>' . 
									"\n" . 
									str_repeat("\t", $level + 2) . '<ul class="dropdown-menu">' . "\n";
            } else {
                // Listenpunkte zusammensetzen
                $categories_string .= $Pre .
                        $Tabulator .
                        '<li' . $ListID . $ListClass . '>' . $CatConfig['LinkPre'] .
                        '<a' . $LinkID . $LinkClass . ' href="' . $foo[$cid]['link'] . '">' .
                        $CatConfig['NamePre'] .
                        $foo[$cid]['name'] .
                        $ProductsCount .
                        $CatConfig['NameAfter'] .
                        '</a>' .
                        $CatConfig['LinkAfter'];
            }
        }

        // für den nächsten Durchgang ...
        $old_level = $level;

        // Unterkategorien durchsteppen
        foreach ($foo as $key => $value) {
            if ($foo[$key]['parent'] == $cid) {
                // Sollen Unterkategorien gezeigt werden?
                if ($CatConfig['ShowAktSub'] && $Aktiv) {
                    $ShowSub = true;
				}
                // Wenn Collapse, dann immer eingeklappt
                if ($ShowSub && $Collapse && !$Aktiv) {
                    $ShowSub = false;
				}
                // Wenn Expand, dann ausgeklappt
                if ($ShowSub || $Expand) {
                    xtc_show_category_superfish($key, $level + 1, $foo, ($level != 0 ? $cpath . $cid . '_' : ''));
				}
            }
        }
    } // Ende if($Show)
}

// -----------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------
//	Das alles braucht nur dann ausgeführt zu werden, wenn noch keine gecachtes 
//	HTML-File vorliegt
// -----------------------------------------------------------------------------------
if (!$box_smarty->isCached(CURRENT_TEMPLATE . '/boxes/box_cat_nav.html', $cache_id) || !$cache) {

    // -------------------------------------------------------------------------------
    //	CategoriesArray (für $foo) zusammenbauen
    // -------------------------------------------------------------------------------
    function initCategoriesArray_superfish() {
        if (GROUP_CHECK == 'true') {
            $group_check = "and c.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
        }
        // ---------------------------------------------------------------------------
        //	Datenbank ...
        // ---------------------------------------------------------------------------
        $categories_query = xtc_db_query(" 
					SELECT	c.categories_id,
							cd.categories_name, 
							cd.categories_heading_title, 
							cd.categories_contents,
							cd.categories_blogs,							
							c.parent_id 
					FROM 	" . TABLE_CATEGORIES . " AS c
					INNER JOIN
							" . TABLE_CATEGORIES_DESCRIPTION . " AS cd ON(c.categories_id = cd.categories_id AND cd.language_id='" . (int) $_SESSION['languages_id'] . "')
					WHERE 	c.categories_status = '1' 
					AND 	c.section = '0' 
							" . $group_check . " 
					ORDER BY sort_order, cd.categories_name;");
        // ---------------------------------------------------------------------------
        //	Array zusammenfriemeln ...
        // ---------------------------------------------------------------------------
        while ($categories = xtc_db_fetch_array($categories_query)) {
				if ($categories['categories_blogs'] != '0') {
					$mylink = xtc_href_link(FILENAME_BLOG, 'blog_cat=' . $categories['categories_blogs']);
				} elseif ($categories['categories_contents'] != '0') {
					$my_contlink = xtc_db_fetch_array(xtc_db_query("select content_out_link from ".TABLE_CONTENT_MANAGER." where content_group = ". $categories['categories_contents']." "));
					if($my_contlink['content_out_link'] != ''){
						$mylink = $my_contlink['content_out_link'];
					}else{
						$mylink = xtc_href_link(FILENAME_CONTENT, 'coID=' . $categories['categories_contents']);
					}
				} else {
					$mylink = initCategoryLink($categories['categories_id'], $categories['categories_name'], $categories['categories_heading_title']);
				}			
            $Cats[$categories['categories_id']] = array(
                'id' => $categories['categories_id'],
                'name' => $categories['categories_name'],
                'heading' => $categories['categories_heading_title'],
                'parent' => $categories['parent_id'],
                'subcats' => false,
                'link' => $mylink
            );
        }
        // ---------------------------------------------------------------------------
        //	... und gleich die SubCats ermitteln. 
        //	Die Funktion xtc_has_category_subcategories() kümmert sich leider nicht um 
        // 	Berechtigungen und Status aktiv/inaktiv. Daher machen wir das hier. Spart
        //	Außerdem gleich noch ein paar Datenbank-Abfragen ...
        // ---------------------------------------------------------------------------
        $Keys = array_keys($Cats);
        foreach ($Keys as $Key) {
            if ($Cats[$Key]['parent'] != 0) {
                $Cats[$Cats[$Key]['parent']]['subcats'][] = $Key;
            }
        }
        // -------------------------------------------------------------------------------
        if (!empty($Cats))
            return $Cats;
        return false;
    }

    // -------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------
    function initContentManagerLink($coID = false) {
        if ($coID) {
            if (GROUP_CHECK == 'true')
                $group_check = "AND \tgroup_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
            $dbQuery = xtc_db_fetch_array(xtDBquery("
						SELECT	content_title 
						FROM 	" . TABLE_CONTENT_MANAGER . " 
						WHERE 	content_group = '" . intval($coID) . "' 
						AND 	languages_id = '" . (int) $_SESSION['languages_id'] . "' 
						" . $group_check . " 
						AND 	content_status = '1';"));
            if (!empty($dbQuery)) {
                if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
                    $SEF_parameter = '&content=' . xtc_cleanName($dbQuery['content_title']);
                return xtc_href_link(FILENAME_CONTENT, 'coID=' . $coID . $SEF_parameter);
            }
        }
        return false;
    }

    // -----------------------------------------------------------------------------------
    function initProductsLink($ProdID = false, $DateCheck = true) {
        if ($ProdID) {
            if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
                $fsk_lock = "AND \tp.products_fsk18!=1 ";
            if (GROUP_CHECK == 'true')
                $group_check = "AND \tp.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
           $dbQuery = xtc_db_fetch_array(xtDBquery("
						SELECT 	
							p.products_id, 
							pd.products_name 
						FROM 
							" . TABLE_PRODUCTS_DESCRIPTION . " pd 
						INNER JOIN
								" . TABLE_PRODUCTS . " p ON(pd.products_id = p.products_id AND p.products_status = '1')
						WHERE 
							pd.products_id = '" . intval($ProdID) . "' 
							" . $fsk_lock . " 
							" . $group_check . "  
						AND
							pd.language_id = '" . (int) $_SESSION['languages_id'] . "';"));

            if (!empty($dbQuery['products_id']))
                return xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link(intval($ProdID), $dbQuery['products_name']));
        }
        return false;
    }

    // -----------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------
    //	Link ermitteln - sucht dabei gleich nach "Ersatz-Markern"
    //	Mit {#coID=7#} wird z.B. zur ContentManager-Seite 7 verlinkt (Kontakt)
    //	Mit {#pID=123#} zum Produkt mit ID 123
    //	Mit {#account.php#} zur Seite "Mein Konto"
    // -----------------------------------------------------------------------------------
    //	So kann man diverse Links in EINE Kategorien-Navigation setzen.
    // -----------------------------------------------------------------------------------
    function initCategoryLink($CatID = false, $CatName = false, $CatHeading = false) {
        $CategoryLink = false;
        if ($CatID) {
            if ($CatHeading) {
                if (preg_match("/\{#([^#\{\}]*\.php[^#\{\}]*)#\}/", $CatHeading, $LinkedScriptComplete)) {
                    if (preg_match("/(.*\.php)(.*)/", $LinkedScriptComplete[1], $LinkedScript)) {
                        if (file_exists($LinkedScript[1]))
                            $CategoryLink = xtc_href_link($LinkedScript[1]) . $LinkedScript[2];
                    }
                } elseif (preg_match("/\{#[^\{\}]*coID\=(\d*)[^\{\}]*#\}/i", $CatHeading, $Treffer)) {
                    $CategoryLink = initContentManagerLink(intval($Treffer[1]));
                } elseif (preg_match("/\{#[^\{\}]*pID\=(\d*)[^\{\}]*#\}/i", $CatHeading, $Treffer)) {
                    $CategoryLink = initProductsLink(intval($Treffer[1]));
                }
            }
            if (!$CategoryLink) {
                $CategoryLink = xtc_href_link(FILENAME_DEFAULT, xtc_category_link(intval($CatID), $CatName));
            }
        }
        return $CategoryLink;
    }

    // -----------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------
    $categories_string = '';
    $foo = initCategoriesArray_superfish();
    xtc_show_category_superfish(0, 0, $foo, '');
    // -----------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------
    // 	NaviListe bekommt die ID "CatNavi"
    // -----------------------------------------------------------------------------------
    $CatNaviStart = "\t\t<ul class=\"nav navbar-nav\">\n";
    // -----------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------
    // 	Hätte man auch einfacher machen können, aber mit Tabulatoren ist schicker.
    // 	Außerdem kann man so leichter nachprüfen, ob auch wirklich alles korrekt läuft.
    // -----------------------------------------------------------------------------------
    for ($counter = 1; $counter < $old_level + 1; $counter++) {
        $CatNaviEnd .= "</li>\n" . str_repeat("\t", $old_level + 2 - $counter) . "</ul>\n";
        if ($old_level - $counter > 0)
            $CatNaviEnd .= str_repeat("\t", ($old_level + 2 - $counter) - 1);
    }
    // -----------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------
    // 	Fertige Liste zusammensetzen
    // -----------------------------------------------------------------------------------
    $box_smarty->assign('BOX_CONTENT', $CatNaviStart . $categories_string . $CatNaviEnd);
    $box_smarty->assign('language', $_SESSION['language']);
    // -----------------------------------------------------------------------------------
}
// -----------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------
//	Ausgabe ans Template
// -----------------------------------------------------------------------------------
if (!$cache) {
    $box_content = $box_smarty->fetch(CURRENT_TEMPLATE . '/boxes/box_cat_nav.html');
} else {
    $box_content = $box_smarty->fetch(CURRENT_TEMPLATE . '/boxes/box_cat_nav.html', $cache_id);
}
$smarty->assign('BOX_cat_nav', $box_content);
// -----------------------------------------------------------------------------------
?>