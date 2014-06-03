<?php
/* -----------------------------------------------------------------
 * 	$Id: metatags.php 1012 2014-05-08 13:02:25Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 *   basiert auf Gunnar Tillmann metatags.php
 * 	http://www.gunnart.de
 * --------------------------------------------------------------- */

// ---------------------------------------------------------------------------------------
//	Konfiguration ... 
// ---------------------------------------------------------------------------------------
global $metaStopWords, $metaGoWords, $metaMinLength, $metaMaxLength, $metaDesLength;
$metaStopWords = ('basiert, Seite, Dieser, bitte, besuchen, welches, interessiert, daran, daraus, davon, gestellt, weder, Willkommen, Dies, Wenn, Alle, Startseite, dienen, Sie, inkl, Versandkosten, aber, alle, alles, als, auch, auf, aus, bei, beim, beinahe, bin, bis, ist, dabei, dadurch, daher, dank, darum, danach, das, daß, dass, dein, deine, dem, den, der, des, dessen, dadurch, deshalb, die, dies, diese, dieser, diesen, diesem, dieses, doch, dort, durch, eher, ein, eine, einem, einen, einer, eines, einige, einigen, einiges, eigene, eigenes, eigener, endlich, euer, eure, etwas, fast, findet, für, gab, gibt, geben, hatte, hatten, hattest, hattet, heute, hier, hinter, ich, ihr, ihre, ihn, ihm, im, immer, in, ist, ja, jede, jedem, jeden, jeder, jedes, jener, jenes, jetzt, kann, kannst, kein, können, könnt, machen, man, mein, meine, mehr, mit, muß, mußt, musst, müssen, müßt, nach, nachdem, neben, nein, nicht, nichts, noch, nun, nur, oder, statt, anstatt, seid, sein, seine, seiner, sich, sicher, sie, sind, soll, sollen, sollst, sollt, sonst, soweit, sowie, und, uns, unser, unsere, unserem, unseren, unter, vom, von, vor, wann, warum, was, war, weiter, weitere, wenn, wer, werde, widmen, widmet, viel, viele, vieles, weil, werden, werdet, weshalb, wie, wieder, wieso, wir, wird, wirst, wohl, woher, wohin, wurdezum, zur, über');
$metaGoWords = ('autoradio, car, hifi, navigation, commerce, seo, shop, online, xtc'); // Hier rein,  was nicht gefiltert werden soll
$metaMinLength = META_MIN_KEYWORD_LENGTH;  // Mindestlänge eines Keywords
$metaMaxLength = META_MAX_KEYWORD_LENGTH;  // Maximallänge eines Keywords
$metaDesLength = META_MAX_DESCRIPTION_LENGTH; // maximale Länge der "description" (in Buchstaben)
// ---------------------------------------------------------------------------------------
// Shop-Titel bei Kategorien
if (ADDCATSHOPTITLE == 'true') {
    $addCatShopTitle = true;
} else {
    $addCatShopTitle = false;
}
// Shop-Titel bei Produkten
if (ADDPRODSHOPTITLE == 'true') {
    $addProdShopTitle = true;
} else {
    $addProdShopTitle = false;
}
// Shop-Titel bei Contentseiten
if (ADDCONTENTSHOPTITLE == 'true') {
    $addContentShopTitle = true;
} else {
    $addContentShopTitle = false;
}
// Shop-Titel bei Angeboten
if (ADDSPECIALSSHOPTITLE == 'true') {
    $addSpecialsShopTitle = true;
} else {
    $addSpecialsShopTitle = false;
}
// Shop-Titel bei Neuen Artikeln
if (ADDNEWSSHOPTITLE == 'true') {
    $addNewsShopTitle = true;
} else {
    $addNewsShopTitle = false;
}
// Shop-Titel bei Suchergebnissen
if (ADDSEARCHSHOPTITLE == 'true') {
    $addSearchShopTitle = true;
} else {
    $addSearchShopTitle = false;
}
// Shop-Titel bei sonstigen Seiten
if (ADDOTHERSSHOPTITLE == 'true') {
    $addOthersShopTitle = true;
} else {
    $addOthersShopTitle = false;
}
// Seitennummerierung
if (ADDPAGINATION == 'true') {
    $addPagination = true;
} else {
    $addPagination = false;
}

$noIndexUnimportant = true;  // "unwichtige" Seiten mit noindex versehen
// ---------------------------------------------------------------------------------------
//  Diese Seiten sind "wichtig"! (ist nur relevant, wenn $noIndexUnimportand == true)
// ---------------------------------------------------------------------------------------
$pagesToShow = array(
    FILENAME_DEFAULT,
    FILENAME_PRODUCT_INFO,
    FILENAME_CONTENT,
    // FILENAME_ADVANCED_SEARCH_RESULT,  // don't index search result
    // FILENAME_ADVANCED_SEARCH,  // don't index search
    FILENAME_SPECIALS,
    FILENAME_PRODUCTS_NEW,
    'shop-bewertungen.php',
    'reviews.php',
    'hashtags.php',
    'product_reviews_info.php',
    FILENAME_TAGLISTING,
    FILENAME_BLOG
);


// ---------------------------------------------------------------------------------------
//      Einzelne Content Seiten mit noindex versehen, kommagetrennte Liste der coID
// ---------------------------------------------------------------------------------------
// $content_noIndex = array(7,9);
$content_noIndex = array(7);

// ---------------------------------------------------------------------------------------
//	Title für "sonstige" Seiten
// ---------------------------------------------------------------------------------------
//$breadcrumbTitle = 	array_pop($breadcrumb->_trail);
$breadcrumbTitle = end($breadcrumb->_trail); // <-- BugFix
$breadcrumbTitle = $breadcrumbTitle['title'];


// ---------------------------------------------------------------------------------------
//  noindex, nofollow bei "unwichtigen" Seiten
// ---------------------------------------------------------------------------------------
$meta_robots = META_ROBOTS;
if ($noIndexUnimportant && !in_array(basename($PHP_SELF), $pagesToShow)) {
    $meta_robots = 'noindex, follow, noodp';
}

// ---------------------------------------------------------------------------------------
//  MultiLanguage-Metas
// ---------------------------------------------------------------------------------------
// Wenn wir auf der Startseite sind, Metas aus der index-Seite holen
if (basename($_SERVER['SCRIPT_NAME']) == FILENAME_DEFAULT &&
        empty($_GET['cat']) &&
        empty($_GET['cPath']) &&
        empty($_GET['manufacturers_id'])
) {
    $ml_meta_where = "content_group = 5";

    // ... ansonsten Metas aus STANDARD_META holen
} else {
    $ml_meta_where = "content_title = 'STANDARD_META'";
}

// Dadadadatenbank
$ml_meta_query = xtDBquery("
		select 	content_meta_title,
				content_meta_description, 
				content_meta_keywords 
		from 	" . TABLE_CONTENT_MANAGER . " 
		where 	" . $ml_meta_where . " 
		and 	languages_id = '" . intval($_SESSION['languages_id']) . "'
	");
$ml_meta = xtc_db_fetch_array($ml_meta_query, true);

// ---------------------------------------------------------------------------------------
//	Mehrsprachige Standard-Metas definieren. Wenn leer, werden die üblichen genommen
// ---------------------------------------------------------------------------------------
define('ML_META_KEYWORDS', ($ml_meta['content_meta_keywords']) ? $ml_meta['content_meta_keywords'] : META_KEYWORDS);
define('ML_META_DESCRIPTION', ($ml_meta['content_meta_description']) ? $ml_meta['content_meta_description'] : META_DESCRIPTION);
define('ML_TITLE', ($ml_meta['content_meta_title']) ? $ml_meta['content_meta_title'] : TITLE);
// ---------------------------------------------------------------------------------------
$metaGoWords = getGoWords(); // <-- nur noch einmal ausführen
// ---------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------
//	Aufräumen: Umlaute und Sonderzeichen wandeln. 
// ---------------------------------------------------------------------------------------
function metaNoEntities($Text) {
    $translation_table = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
    $translation_table = array_flip($translation_table);
    $Return = strtr($Text, $translation_table);
    return preg_replace('/&#(\d+);/me', "chr('\\1')", $Return);
}

function metaHtmlEntities($Text) {
    $translation_table = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
    $translation_table[chr(38)] = '&';
    return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/", "&amp;", strtr($Text, $translation_table));
}

// ---------------------------------------------------------------------------------------
//	Array basteln: Text aufbereiten -> Array erzeugen -> Array unique ...  
// ---------------------------------------------------------------------------------------

function prepareWordArray($Text) {
    $Text = str_replace(array('&nbsp;', '\t', '\r', '\n', '\b', '{$greeting}'), ' ', strip_tags($Text));
    $Text = preg_replace("/(&([aou])[^;]*;)/", '$2e', $Text);
    $Text = preg_replace("/(&(s)[^;]*;)/", '$2$2', $Text);
    $Text = preg_replace("/(&([cizen])[^;]*;)/", '$2', $Text);
    $Text = preg_replace("/(&[^;]*;)/", ' ', $Text);
    $Text = preg_replace("/([,.])/", '', $Text);
    $Text = trim(preg_replace("/\s\s+/", ' ', $Text));
    return($Text);
}

function makeWordArray($Text) {
    $Text = func_get_args();
    $Words = array();
    foreach ($Text as $Word) {
        if ((!empty($Word)) && (is_string($Word))) {
            $Words = array_merge($Words, explode(' ', $Word));
        }
    }
    return array_unique($Words);
}

function WordArray($Text) {
    return makeWordArray(prepareWordArray($Text));
}

// ---------------------------------------------------------------------------------------
//     Seitennummerierung im Title (Kategorien, Sonderangebote, Neue Artikel etc.)
// ---------------------------------------------------------------------------------------
$Page = '';
if (isset($_GET['page']) && $_GET['page'] > 1 && $addPagination) {
    $Page = trim(str_replace('%d', '', PREVNEXT_TITLE_PAGE_NO)) . ' ' . intval($_GET['page']);
}

// ---------------------------------------------------------------------------------------
//	KeyWords aufräumen:
// 	Stop- und KeyWords-Liste in Array umwandeln, StopWords löschen, 
//	GoWords- und Längen-Filter anwenden
// ---------------------------------------------------------------------------------------
function cleanKeyWords($KeyWords) {
    global $metaStopWords;
    $KeyWords = WordArray($KeyWords);
    $StopWords = WordArray($metaStopWords);
    $KeyWords = array_diff($KeyWords, $StopWords);
    $KeyWords = array_filter($KeyWords, filterKeyWordArray);
	// echo '<pre>';
	// echo count($KeyWords);
	// print_r($KeyWords);
	// echo '</pre>';
	$meta_number = META_KEYWORDS_NUMBER;
	// echo $meta_number;
    return $KeyWords;
}

// ---------------------------------------------------------------------------------------
//	GoWords- und Längen-Filter: 
//	Alles, was zu kurz ist, fliegt raus, sofern nicht in der GoWords-Liste
// ---------------------------------------------------------------------------------------
function filterKeyWordArray($KeyWord) {
    global $metaMinLength, $metaMaxLength, $metaGoWords;
    $GoWords = WordArray($metaGoWords);
    if (!in_array($KeyWord, $GoWords)) {
        //$Length = strlen($KeyWord);
        $Length = strlen(preg_replace("/(&[^;]*;)/", '#', $KeyWord)); // <-- Mindest-Länge auch bei Umlauten berücksichtigen
        if ($Length < $metaMinLength) { // Mindest-Länge
            return false;
        } elseif ($Length > $metaMaxLength) { // Maximal-Länge
            return false;
        }
    }
    return true;
}

// ---------------------------------------------------------------------------------------
//	GoWords: Werden grundsätzlich nicht gefiltert
//	Sofern angelegt, werden (zusätzlich zu den Einstellungen oben) die "normalen"
//	Meta-Angaben genommen (gefixed anno Danno-Wanno)
// ---------------------------------------------------------------------------------------
function getGoWords() {
    global $metaGoWords, $categories_meta, $product;
    //$GoWords = $metaGoWords.' '.META_KEYWORDS;
    $GoWords = $metaGoWords . ' ' . ML_META_KEYWORDS . ' ' . ML_TITLE; // <-- MultiLanguage
    $GoWords .= ' ' . $categories_meta['categories_meta_keywords'];
    $GoWords .= ' ' . $product->data['products_meta_keywords'];
    return $GoWords;
}

// ---------------------------------------------------------------------------------------
//	Aufräumen: Leerzeichen und HTML-Code raus, kürzen, Umlaute und Sonderzeichen wandeln
// ---------------------------------------------------------------------------------------
function metaClean($Text, $Length = false, $Abk = ' ...') {
    $Text = strip_tags($Text);
    $Text = str_replace(array('&nbsp;', '\t', '\r', '\n', '\b', '"', '{$greeting}'), ' ', $Text);
    $Text = trim(preg_replace("/\s\s+/", ' ', $Text));
    if (($Length) && ($Length > 0)) {
        if (strlen($Text) > $Length) {
            $Length -= strlen($Abk);
            $Text = preg_replace('/\s+?(\S+)?$/', '', substr($Text, 0, $Length + 1));
            $Text = substr($Text, 0, $Length) . $Abk;
        }
    }
    return $Text;
}

// ---------------------------------------------------------------------------------------
//	metaTitle und metaKeyWords, Rückgabe bzw. Formatierung
// ---------------------------------------------------------------------------------------
function metaTitle($Title = array()) {
    $Title = func_get_args();
    $Title = array_filter($Title, metaClean);
    return implode(' - ', $Title);
}

// ---------------------------------------------------------------------------------------
function metaKeyWords($Text) {
    $KeyWords = cleanKeyWords($Text);
    return implode(', ', $KeyWords);
}

// ---------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------
//	Daten holen: Produktdetails
// ---------------------------------------------------------------------------------------
//if(basename($_SERVER['SCRIPT_NAME']) == FILENAME_PRODUCT_INFO) { 
if (isset($_GET['products_id']) && is_numeric($_GET['products_id'])) {
    if ($product->isProduct()) {
        // KeyWords ...
        if (!empty($product->data['products_meta_keywords'])) {
            $meta_keyw = $product->data['products_meta_keywords'];
        } else {
            $meta_keyw = metaKeyWords($product->data['products_name'] . ' ' . $product->data['products_description']);
        }

        // Description ...
        if (!empty($product->data['products_meta_description'])) {
            $meta_descr = $product->data['products_meta_description'];
            $metaDesLength = false;
        } else {
            $meta_descr = $product->data['products_name'] . ': ' . $product->data['products_description'];
        }

        // Title ...
        if (!empty($product->data['products_meta_title'])) {
            $meta_title = $product->data['products_meta_title'] . (($addProdShopTitle) ? ' - ' . ML_TITLE : '');
        } else {
            $meta_title = metaTitle($product->data['products_name'], $product->data['manufacturers_name'], ($addProdShopTitle) ? ML_TITLE : '');
        }
        $canonical_url = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product->data['products_id'] . '&language=' . $_SESSION['language_code'] . (SEARCH_ENGINE_FRIENDLY_URLS == 'true' ? '&cPath=' . xtc_get_product_path($product->data['products_id'], $product->data['products_name']) : ''));
    }
    echo '<meta property="og:site_name" content="'.STORE_NAME.'">';
    echo '<meta property="og:title" content="'.$product->data['products_name'].'">';
    echo '<meta property="og:description" content="'.metaClean($meta_descr, $metaDesLength).'">';
    echo '<meta property="og:type" content="product">';
    echo '<meta property="og:url" content="'.$canonical_url.'">';
	
    if ($product->data['products_image'] != '') {
        echo '<meta property="og:image" content="' . HTTP_SERVER . '/' . DIR_WS_POPUP_IMAGES . $product->data['products_image'] . '"/>';
    }
    if ($product->data['products_rel'] == 0) {
        $meta_robots = 'noindex, nofollow, noodp';
    }
	
	if ($product->data['products_last_modified'] > 0) {
		$metadate = $product->data['products_last_modified'];
	} else {
		$metadate = $product->data['products_date_added'];
	}
	
	echo '<meta name="date" content="'.date('Y-m-d\TH:i:sP', strtotime($metadate)).'">';
	
}

// ---------------------------------------------------------------------------------------
//	Daten holen: Kategorie
// ---------------------------------------------------------------------------------------
elseif ($_GET['cPath']) {
    // Sind wir in einer Kategorie?
    if (!empty($current_category_id)) {
        $categories_meta_query = xtDBquery("
				SELECT 	
					cd.categories_meta_keywords, 
					cd.categories_meta_description, 
					cd.categories_meta_title, 
					cd.categories_heading_title, 
					c.categories_image, 
					c.date_added, 
					c.last_modified, 
					cd.categories_name, 
					cd.categories_description 
				FROM 	
					" . TABLE_CATEGORIES_DESCRIPTION . " AS cd
				LEFT JOIN 
					" . TABLE_CATEGORIES . " AS c ON(c.categories_id = cd.categories_id)
				WHERE 
					cd.categories_id='" . intval($current_category_id) . "' 
				AND 
					cd.language_id='" . intval($_SESSION['languages_id']) . "'
			");
        $categories_meta = xtc_db_fetch_array($categories_meta_query, true);
    }

    $manu_id = $manu_name = false;

    // Nachsehen, ob ein Hersteller gewählt ist
    if (!empty($_GET['manu'])) {
        $manu_id = $_GET['manu'];
    }
    if (!empty($_GET['manufacturers_id'])) {
        $manu_id = $_GET['manufacturers_id'];
    }
    if (!empty($_GET['filter_id']) && !$manu_id) {
        $manu_id = $_GET['filter_id'];
    }

    // ggf. Herstellernamen herausfinden ...
    if ($manu_id) {
        $manu_name_query = xtDBquery("
				SELECT manufacturers_name FROM " . TABLE_MANUFACTURERS . " WHERE manufacturers_id ='" . intval($manu_id) . "'
			");
        $manu_name = implode('', xtc_db_fetch_array($manu_name_query, true));
        $metaGoWords .= ',' . $manu_name; // <-- zu GoWords hinzufügen
    }

    // KeyWords ...
    if (!empty($categories_meta['categories_meta_keywords'])) {
        $meta_keyw = $categories_meta['categories_meta_keywords']; // <-- 1:1 übernehmen!
    } elseif (!empty($categories_meta['categories_heading_title'])) {
        $meta_keyw = metaKeyWords($categories_meta['categories_heading_title'] . ' ' . $manu_name . ' ' . $categories_meta['categories_description']);
    } else {
        $meta_keyw = metaKeyWords($categories_meta['categories_name'] . ' ' . $manu_name . ' ' . $categories_meta['categories_description']);
    }

    // Description ...
    if (!empty($categories_meta['categories_meta_description'])) {
        $meta_descr = $categories_meta['categories_meta_description'] . (($manu_name) ? ' - ' . $manu_name : '');
        $metaDesLength = false;
    } elseif ($categories_meta) {
        $meta_descr = $categories_meta['categories_heading_title'] . ' - ' . $categories_meta['categories_name'] . (($manu_name) ? ' - ' . $manu_name : '') . (($categories_meta['categories_description']) ? ' - ' . $categories_meta['categories_description'] : '');
    }

    // Title ...
    if (!empty($categories_meta['categories_meta_title'])) {
        $meta_title = $categories_meta['categories_meta_title'] . (($manu_name) ? ' - ' . $manu_name : '') . (($Page) ? ' - ' . $Page : '') . (($addCatShopTitle) ? ' - ' . ML_TITLE : '');
    } elseif (!empty($categories_meta['categories_heading_title'])) {
        $meta_title = metaTitle($categories_meta['categories_heading_title'], $manu_name, $Page, ($addCatShopTitle) ? ML_TITLE : '');
    } else {
        $meta_title = metaTitle($categories_meta['categories_name'], $manu_name, $Page, ($addCatShopTitle) ? ML_TITLE : '');
    }
    $canonical_url = xtc_href_link(FILENAME_DEFAULT, xtc_category_link((int) $current_category_id)) . ((isset($_GET['page']) && (int) $_GET['page'] > 1 ? '&page=' . (int) $_GET['page'] : ''));
    
	echo '<meta property="og:site_name" content="'.STORE_NAME.'">';
    echo '<meta property="og:title" content="'.$meta_title.'">';
    echo '<meta property="og:description" content="'.metaClean($meta_descr, $metaDesLength).'">';
    echo '<meta property="og:type" content="website">';
    echo '<meta property="og:url" content="'.$canonical_url.'">';
	
    if ($categories_meta['categories_image'] != '') {
        echo '<meta property="og:image" content="' . HTTP_SERVER . '/categories/' . $categories_meta['categories_image'] . '"/>';
    }
	if ($categories_meta['last_modified'] > 0) {
		$metadate = $categories_meta['last_modified'];
	} else {
		$metadate = $categories_meta['date_added'];
	}
	
	echo '<meta name="date" content="'.date('Y-m-d\TH:i:sP', strtotime($metadate)).'">';
    
}
// ---------------------------------------------------------------------------------------
//	Daten holen: Inhalts-Seite (ContentManager)
// ---------------------------------------------------------------------------------------
elseif (isset($_GET['coID']) && is_numeric($_GET['coID'])) {

    //  Noindex bei bestimmten Contet Seiten
    if (in_array(intval($_GET['coID']), $content_noIndex)) {
        $meta_robots = 'noindex, follow, noodp';
    }
    $contents_meta_query = xtDBquery("
			SELECT 	content_meta_title,
					content_meta_description, 
					content_meta_keywords, 
					content_title, 
					content_heading, 
					content_text,
					content_file, 
					content_group					
			FROM 	" . TABLE_CONTENT_MANAGER . " 
			WHERE 	content_group = '" . intval($_GET['coID']) . "' 
			AND 	languages_id = '" . intval($_SESSION['languages_id']) . "'
		");
    $contents_meta = xtc_db_fetch_array($contents_meta_query, true);

    if (count($contents_meta) > 0) {

        // NEU! Eingebundene Dateien auslesen
        if ($contents_meta['content_file']) {
            // Nur Text- oder HTML-Dateien!
            if (preg_match("/\.(txt|htm|html)$/i", $contents_meta['content_file'])) {
                $contents_meta['content_text'] .= ' ' . implode(' ', @file(DIR_FS_CATALOG . 'media/content/' . $contents_meta['content_file']));
            }
        }

        // KeyWords ...
        if (!empty($contents_meta['content_meta_keywords'])) {
            $meta_keyw = $contents_meta['content_meta_keywords'];
        } else {
            $meta_keyw = metaKeyWords($contents_meta['content_title'] . ' ' . $contents_meta['content_heading'] . ' ' . $contents_meta['content_text']);
        }

        // Title ...
        if (!empty($contents_meta['content_meta_title'])) {
            $meta_title = $contents_meta['content_meta_title'] . (($addContentShopTitle) ? ' - ' . ML_TITLE : '');
        } else {
            $meta_title = metaTitle($contents_meta['content_title'], $contents_meta['content_heading'], ($addContentShopTitle) ? ML_TITLE : '');
        }

        // Description ...
        if (!empty($contents_meta['content_meta_description'])) {
            $meta_descr = $contents_meta['content_meta_description'];
            $metaDesLength = false;
        } else {
            $meta_descr = ($contents_meta['content_heading']) ? $contents_meta['content_heading'] . ': ' : '';
            $meta_descr .= $contents_meta['content_text'];
        }
    }
    if ($contents_meta['content_group'] == '5') {
        $canonical_url = (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG;
    } else {
        $canonical_url = xtc_href_link(FILENAME_CONTENT, 'coID=' . $_GET['coID'] . '&language=' . $_SESSION['language_code']);
    }
}

// ---------------------------------------------------------------------------------------
//	Daten holen: Hersteller
// ---------------------------------------------------------------------------------------
elseif ($_GET['manufacturers_id']) {
    $manufacturers_meta_query = xtDBquery("SELECT
													m.manufacturers_name,
													m.manufacturers_id,
													mi.manufacturers_meta_title ,
													mi.manufacturers_description,
													mi.manufacturers_meta_description,
													mi.manufacturers_meta_keywords
												FROM
													manufacturers m,
													manufacturers_info mi
												WHERE
													m.manufacturers_id ='" . intval($_GET['manufacturers_id']) . "'
												AND
													mi.manufacturers_id ='" . intval($_GET['manufacturers_id']) . "'
												AND
													mi.languages_id ='" . intval($_SESSION['languages_id']) . "'");
    $manufacturers_data = xtc_db_fetch_array($manufacturers_meta_query, true);

    if (!empty($manufacturers_data['manufacturers_meta_description']))
        $meta_descr = $manufacturers_data['manufacturers_meta_description'];
    else
        $meta_descr = metaTitle($manufacturers_data['manufacturers_name'], TITLE);

    if (!empty($manufacturers_data['manufacturers_meta_keywords']))
        $meta_keyw = $manufacturers_data['manufacturers_meta_keywords'];
    else
        $meta_keyw = metaTitle($manufacturers_data['manufacturers_name'], TITLE);

    if (!empty($manufacturers_data['manufacturers_meta_title']))
        $meta_title = $manufacturers_data['manufacturers_meta_title'];
    else
        $meta_title = metaTitle($manufacturers_data['manufacturers_name'], TITLE);
}

// ---------------------------------------------------------------------------------------
//	Daten hole: Blog Kategorie
// ---------------------------------------------------------------------------------------

elseif ((isset($_GET['blog_cat']) && is_numeric($_GET['blog_cat'])) && (!isset($_GET['blog_item']))) {
    $blog_meta_query = xtDBquery("SELECT meta_key,
	                                        meta_desc,
	                                        meta_title,
	                                        titel
	                                        FROM " . TABLE_BLOG_CATEGORIES . "
	                                        WHERE categories_id = '" . $_GET['blog_cat'] . "'
	                                        AND language_id = '" . intval($_SESSION['languages_id']) . "'");
    $blog_meta = xtc_db_fetch_array($blog_meta_query, true);

    if (!empty($blog_meta['meta_desc']))
        $meta_descr = $blog_meta['meta_desc'];
    else
        $meta_descr = metaTitle($blog_meta['titel'], TITLE);

    if (!empty($blog_meta['meta_key']))
        $meta_keyw = $blog_meta['meta_key'];
    else
        $meta_keyw = metaTitle($blog_meta['titel'], TITLE);

    if (!empty($blog_meta['meta_title']))
        $meta_title = $blog_meta['meta_title'];
    else
        $meta_title = metaTitle($blog_meta['titel'], TITLE);
				$image = xtc_db_fetch_array(xtc_db_query("select image from blog_cat_images where cat_id = ".$_GET['blog_cat']." AND image_nr = 1 LIMIT 1 "));
				if($image == ''){
					$image_link = HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'blog_image/original_images/no_img_big.jpg';
				}else{
					$image_link = HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'blog_image/original_images/' . $image['image'];
				}
    $url = explode('?', $_SERVER['REQUEST_URI']);
    $canonical_url = (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . $url[0];
	echo "\n".'<meta property="og:site_name" content="'.STORE_NAME.'">'."\n";
    echo '<meta property="og:title" content="'.$meta_title.'">'."\n";
    echo '<meta property="og:description" content="'.metaClean($meta_descr, $metaDesLength).'">'."\n";
    echo '<meta property="og:type" content="website">'."\n";
    echo '<meta property="og:url" content="'.$canonical_url.'">'."\n";	
	echo '<meta property="og:image" content="' . htmlentities($image_link) . '" />' . "\n";
}
// ---------------------------------------------------------------------------------------
//	Daten hole: Blog Startseite
//
elseif ($_SERVER['SCRIPT_NAME'] == '/blog.php' && !isset($_GET['blog_cat']) && !isset($_GET['blog_item'])) {
$blog_meta_query = xtDBquery("SELECT meta_keywords,
	                                        meta_description,
	                                        meta_title
	                                        FROM " . TABLE_BLOG_START . "
	                                        WHERE  language_id = '" . intval($_SESSION['languages_id']) . "'");
    $blog_meta = xtc_db_fetch_array($blog_meta_query, true);

    if (!empty($blog_meta['meta_desc']))
        $meta_descr = $blog_meta['meta_desc'];
    else
        $meta_descr = metaTitle($blog_meta['titel'], TITLE);

    if (!empty($blog_meta['meta_key']))
        $meta_keyw = $blog_meta['meta_key'];
    else
        $meta_keyw = metaTitle($blog_meta['titel'], TITLE);

    if (!empty($blog_meta['meta_title']))
        $meta_title = $blog_meta['meta_title'];
    else
        $meta_title = metaTitle($blog_meta['titel'], TITLE);
				$image = xtc_db_fetch_array(xtc_db_query("select image from ".TABLE_BLOG_STARTIMG." where image_nr = 1 LIMIT 1 "));
				if($image == ''){
					$image_link = HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'blog_image/original_images/no_img_big.jpg';
				}else{
					$image_link = HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'blog_image/original_images/' . $image['image'];
				}
    $url = explode('?', $_SERVER['REQUEST_URI']);
    $canonical_url = (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . $url[0];
	echo "\n".'<meta property="og:site_name" content="'.STORE_NAME.'">'."\n";
    echo '<meta property="og:title" content="'.$meta_title.'">'."\n";
    echo '<meta property="og:description" content="'.metaClean($meta_descr, $metaDesLength).'">'."\n";
    echo '<meta property="og:type" content="website">'."\n";
    echo '<meta property="og:url" content="'.$canonical_url.'">'."\n";	
	echo '<meta property="og:image" content="' . htmlentities($image_link) . '" />' . "\n";	
}
// ---------------------------------------------------------------------------------------
//	Daten hole: Blog Eintrag
// ---------------------------------------------------------------------------------------

elseif ((isset($_GET['blog_cat']) && is_numeric($_GET['blog_cat'])) && (isset($_GET['blog_item']) && is_numeric($_GET['blog_item']))) {
    $blog_meta_query = xtDBquery("SELECT meta_keywords,
	                                        meta_description,
	                                        meta_title,
	                                        title
	                                        FROM " . TABLE_BLOG_ITEMS . "
	                                        WHERE item_id = '" . $_GET['blog_item'] . "'
	                                        AND language_id = '" . intval($_SESSION['languages_id']) . "'");
    $blog_meta = xtc_db_fetch_array($blog_meta_query, true);

    if (!empty($blog_meta['meta_description']))
        $meta_descr = $blog_meta['meta_description'];
    else
        $meta_descr = metaTitle($blog_meta['titel'], TITLE);

    if (!empty($blog_meta['meta_keywords']))
        $meta_keyw = $blog_meta['meta_keywords'];
    else
        $meta_keyw = metaTitle($blog_meta['titel'], TITLE);

    if (!empty($blog_meta['meta_title']))
        $meta_title = $blog_meta['meta_title'];
    else
        $meta_title = metaTitle($blog_meta['titel'], TITLE);
				$image = xtc_db_fetch_array(xtc_db_query("select image from blog_item_images where item_id = ".$_GET['blog_item']." AND image_nr = 1 LIMIT 1 "));
				if($image == ''){
					$image_link = HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'blog_image/original_images/no_img_big.jpg';
				}else{
					$image_link = HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'blog_image/original_images/' . $image['image'];
				}
    $url = explode('?', $_SERVER['REQUEST_URI']);
    $canonical_url = (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . $url[0];
	echo "\n".'<meta property="og:site_name" content="'.STORE_NAME.'">'."\n";
    echo '<meta property="og:title" content="'.$meta_title.'">'."\n";
    echo '<meta property="og:description" content="'.metaClean($meta_descr, $metaDesLength).'">'."\n";
    echo '<meta property="og:type" content="website">';
    echo '<meta property="og:url" content="'.$canonical_url.'">'."\n";
	echo '<meta property="og:image" content="' . htmlentities($image_link) . '" />' . "\n";
}


// ---------------------------------------------------------------------------------------
//	Title fuer: Specials / Products New
// ---------------------------------------------------------------------------------------
elseif (basename($_SERVER['SCRIPT_NAME']) == FILENAME_SPECIALS) {
    $meta_title = metaTitle(NAVBAR_TITLE_SPECIALS, TITLE);
} elseif (basename($_SERVER['SCRIPT_NAME']) == FILENAME_PRODUCTS_NEW) {
    $meta_title = metaTitle(NAVBAR_TITLE_PRODUCTS_NEW, TITLE);
}


switch (basename($_SERVER['SCRIPT_NAME'])) { // Start Switch
// ---------------------------------------------------------------------------------------
//	Title für Suchergebnisse - Mit Suchbegriff, Kategorien-Namen, Seiten-Nummer etc.
// ---------------------------------------------------------------------------------------
    case FILENAME_ADVANCED_SEARCH_RESULT :

        // ggf. Herstellernamen herausfinden ...
        if (!empty($_GET['manufacturers_id'])) {
            $manu_name_query = xtDBquery("
				select 	manufacturers_name 
				from 	" . TABLE_MANUFACTURERS . " 
				where 	manufacturers_id ='" . intval($_GET['manufacturers_id']) . "'
			");
            $manu_name = implode('', xtc_db_fetch_array($manu_name_query, true));
            $metaGoWords .= ',' . $manu_name; // <-- zu GoWords hinzufügen
        }
        // ggf. Kategorien-Namen herausfinden ...
        if (!empty($_GET['categories_id'])) {
            $cat_name_query = xtDBquery("
				select 	categories_name 
				from 	" . TABLE_CATEGORIES_DESCRIPTION . " 
				where 	categories_id='" . intval($_GET['categories_id']) . "' 
				and 	language_id='" . intval($_SESSION['languages_id']) . "'
			");
            $cat_name = implode('', xtc_db_fetch_array($cat_name_query, true));
        }
        $meta_title = metaTitle($breadcrumbTitle, '&quot;' . trim($_GET['keywords']) . '&quot;', $Page, $cat_name, $manu_name, ($addSearchShopTitle) ? ML_TITLE : '');
        break;

// ---------------------------------------------------------------------------------------
//	Title für Taglisting - Mit Suchbegriff, Kategorien-Namen, Seiten-Nummer etc.
// ---------------------------------------------------------------------------------------
    case FILENAME_TAGLISTING :
        $meta_keyw = stripslashes(trim(urldecode($_GET['tag']))) . ',' . ML_META_KEYWORDS;
        $meta_descr .= metaTitle($breadcrumbTitle, stripslashes(trim(urldecode($_GET['tag']))), ($addSearchShopTitle) ? ML_TITLE : '');
        $meta_title = metaTitle($breadcrumbTitle, stripslashes(trim(urldecode($_GET['tag']))), ($addSearchShopTitle) ? ML_TITLE : '');
        $url = explode('?', $_SERVER['REQUEST_URI']);
        $canonical_url = (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . $url[0];
        break;
// ---------------------------------------------------------------------------------------
//	Title für Produkt-Filter
// ---------------------------------------------------------------------------------------
    case FILENAME_PRODUCT_FILTER :
        $meta_keyw = $_GET['filter'] . ',' . ML_META_KEYWORDS;
        $meta_descr .= metaTitle($breadcrumbTitle, '&quot;' . trim($_GET['filter']) . '&quot;', ($addSearchShopTitle) ? ML_TITLE : '');
        $meta_title = metaTitle($breadcrumbTitle, '&quot;' . trim($_GET['filter']) . '&quot;', ($addSearchShopTitle) ? ML_TITLE : '');
        $url = explode('?', $_SERVER['REQUEST_URI']);
        $canonical_url = (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . $url[0];
        break;

// ---------------------------------------------------------------------------------------
//	Title für Angebote
// ---------------------------------------------------------------------------------------
    case FILENAME_SPECIALS :
        $meta_title = metaTitle($breadcrumbTitle, $Page, ($addSpecialsShopTitle) ? ML_TITLE : '');
        $url = explode('?', $_SERVER['REQUEST_URI']);
        $canonical_url = (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . $url[0];
        break;

// ---------------------------------------------------------------------------------------
//	Title für Neue Artikel
// ---------------------------------------------------------------------------------------
    case FILENAME_PRODUCTS_NEW :
        $meta_title = metaTitle($breadcrumbTitle, $Page, ($addNewsShopTitle) ? ML_TITLE : '');
        $url = explode('?', $_SERVER['REQUEST_URI']);
        $canonical_url = (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . $url[0];
        break;
// ---------------------------------------------------------------------------------------
//	Title für Hashtags
// ---------------------------------------------------------------------------------------
    case 'hashtags.php' :
        $meta_title = metaTitle($breadcrumbTitle, stripslashes(trim(urldecode($_GET['hashtags']))), ($addNewsShopTitle) ? ML_TITLE : '');
		$meta_descr .= metaTitle($breadcrumbTitle, stripslashes(trim(urldecode($_GET['hashtags']))), ($addNewsShopTitle) ? ML_TITLE : '');
        $url = explode('?', $_SERVER['REQUEST_URI']);
        $canonical_url = (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . $url[0];
        break;
// ---------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------
} // Ende Switch		
// ---------------------------------------------------------------------------------------
//	... und wenn nix drin, dann Standard-Werte nehmen
// ---------------------------------------------------------------------------------------
if (empty($meta_keyw)) {
    //$meta_keyw    = metaKeyWords(META_KEYWORDS); 
    $meta_keyw = META_KEYWORDS; // <-- 1:1 übernehmen!
}
if (empty($meta_descr)) {
    $meta_descr = META_DESCRIPTION;
    $metaDesLength = false; // <-- dann auch nicht kürzen!
}
if (empty($meta_title)) {
    $meta_title = TITLE;
}
// ---------------------------------------------------------------------------------------

$meta_descr = str_replace('{$greeting}', ' ', $meta_descr);
$meta_title = str_replace('{$greeting}', ' ', $meta_title);
$meta_keyw = str_replace('{$greeting}', ' ', $meta_keyw);
//Mobile Template
$xmtitle = explode(" - ", metaClean($meta_title));
$xmumlaute = Array("/&auml;/", "/&ouml;/", "/&uuml;/", "/&Auml;/", "/&Ouml;/", "/&Uuml;/", "/ß/", "/ /");
$xmreplace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss", "_");
$xmtitle_neu = preg_replace($xmumlaute, $xmreplace, $xmtitle[0]);
$_SESSION["metatitleid"] = $xmtitle_neu;
$_SESSION["metatitle"] = $xmtitle[0];
?>
<?php if ($_GET['error'] == '404') { ?>
    <title>404 - Seite wurde nicht gefunden!</title>
<?php } else { ?>
    <title><?php echo metaClean($meta_title, META_MAX_TITLE_LENGTH); ?></title> 
<?php } ?>
<meta name="keywords" content="<?php echo metaClean($meta_keyw); ?>"> 
<meta name="description" content="<?php echo metaClean($meta_descr, $metaDesLength); ?>"> 
<?php
if (GOOGLE_VERIFY != '') {
    echo '<meta name="google-site-verification" content="' . GOOGLE_VERIFY . '">';
}
if (BING_VERIFY != '') {
    echo '<meta name="msvalidate.01" content="' . BING_VERIFY . '">';
}

if ($_GET['error'] == '404') {
    $meta_robots = 'noindex, nofollow, noodp';
}
if ($meta_robots != '') {
    echo '<meta name="robots" content="' . $meta_robots . '" />' . "\n";
}
?>
<meta name="author" content="<?php echo metaClean(META_AUTHOR); ?>">
<link rel="alternate" type="application/rss+xml" title="<?php echo META_COMPANY; ?> RSS Feed von <?php echo str_replace('www.', '', $_SERVER['HTTP_HOST']); ?>" href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>rss_news.php">
<?php
if (isset($canonical_url)) {
    $canonical_url = preg_replace('/cSEOid\=[a-z|0-9]{32}/', '', $canonical_url);
    $canonical_url = (substr($canonical_url, -1) == '?' ? substr($canonical_url, 0, -1) : $canonical_url);
    echo '<link rel="canonical" href="' . $canonical_url . '">' . "\n";
}
?>
<link href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>favicon.ico" rel="shortcut icon" type="image/x-icon">
<link rel="apple-touch-icon" href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>apple-touch-icon.png">
<?php
if (GOOGLE_PLUS_AUTHOR_ID != '') {
    echo '<link rel="author" href="https://plus.google.com/' . GOOGLE_PLUS_AUTHOR_ID . '" title="' . STORE_NAME . ' on Google+"/>';
}
?>

<?php
if ($product->isProduct() && (TREEPODIAACTIVE == 'true' && TREEPODIAID != '' && $product->data['products_treepodia_activate'] == '1')) {
    ?>

    <script type="text/javascript">
    // Grab the product SKU / Code
        var trpdSKU = '<?php echo $product->data['products_model']; ?>';

        var video;
        var product;
        function initTreepodia() {
            product = Treepodia.getProduct('<?php echo TREEPODIAID; ?>', trpdSKU);
            product.requestVideo(handleVideo);
        }
        function handleVideo(vid) {
            video = vid;
            if (vid.hasVideos()) {
				video.setShareMenuStyle("Video posten","Video auf folgenden Plattformen posten","");
                video.setPlayer("Purple");
                video.setChromeless(true);
                video.addShareItem("facebook");
                video.addShareItem("twitter");
                video.addShareItem("linkedin");
            } else {
                document.getElementById('video-btn').style.display = 'none';
            }
        }
    </script>

    <script type="text/javascript">
    // Include Dialog Script
        document.write(unescape("%3Cscript src='" + document.location.protocol + "//dxa05szpct2ws.cloudfront.net/utils/trpdDialog/video-dialog.min.js' type='text/javascript'%3E%3C/script%3E"));
    </script>

    <script type="text/javascript">
    // Include Treepodia main script
        document.write(unescape("%3Cscript src='" + document.location.protocol + "//dxa05szpct2ws.cloudfront.net/TreepodiaAsyncLoader.js' type='text/javascript'%3E%3C/script%3E"));
    </script>

    <link rel="image_src" href="<?php echo DIR_WS_POPUP_IMAGES . $product->data['products_image']; ?>">
    <link rel="thumbnail" type="image/jpeg" href="<?php echo DIR_WS_THUMBNAIL_IMAGES . $product->data['products_image']; ?>">	
    <link rel="video_src" href="http://api.treepodia.com/video/get/<?php echo TREEPODIAID; ?>/<?php echo $product->data['products_model']; ?>">

    <meta property="og:site_name" content="<?php echo STORE_NAME; ?>">
    <meta property="og:title" content="<?php echo $product->data['products_name']; ?>">
    <meta property="og:description" content="<?php echo trim(cseo_truncate(strip_tags($product->data['products_short_description']), 160)); ?>">
    <meta property="og:type" content="product">
    <meta property="og:url" content="<?php echo $canonical_url; ?>">
    <meta property="og:image" content="<?php echo DIR_WS_THUMBNAIL_IMAGES . $product->data['products_image']; ?>">
    <meta property="og:video" content="http://api.treepodia.com/video/get/<?php echo TREEPODIAID; ?>/<?php echo $product->data['products_model']; ?>">
    <meta property="og:video:type" content="video/mp4">
    <meta property="og:video:width" content="640">
    <meta property="og:video:height" content="360">

    <meta name="medium" content="video">
    <meta name="video_height" content="640">
    <meta name="video_width" content="360">

    <?php
}
?>