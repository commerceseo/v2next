<?php

/* -----------------------------------------------------------------
 * 	$Id: class.fuzzy_search.php 660 2013-10-08 16:53:16Z akausch $
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

class FuzzySearch {

    var $keywords;
    var $resultKeywords = array();
    var $resultProducts = array();
    var $new_fuzzy = array();
    var $parse_time;

    function getSpecialChars($str) {
        $string = strip_tags($str);
        $trans = get_html_translation_table(HTML_ENTITIES);
        $trans = array_flip($trans);
        $string = strtr($string, $trans);

        return $string;
    }

    function getSuggest($keywords) {
        // Vorbereitungen
        global $xtPrice;
        define('PARSE_START_TIME', microtime());

        $weight_sum = (SEARCH_WEIGHT_LEVENSHTEIN + SEARCH_WEIGHT_SIMILAR_TEXT + SEARCH_WEIGHT_METAPHONE);
        if ($weight_sum > 100) {
            define('SEARCH_WEIGHT_LEVENSHTEIN', (SEARCH_WEIGHT_LEVENSHTEIN / $weight_sum) * 100);
            define('SEARCH_WEIGHT_SIMILAR_TEXT', (SEARCH_WEIGHT_SIMILAR_TEXT / $weight_sum) * 100);
            define('SEARCH_WEIGHT_METAPHONE', (SEARCH_WEIGHT_METAPHONE / $weight_sum) * 100);
        }

        if (SEARCH_WEIGHT_METAPHONE > 0)
            $keyword_metaphone = metaphone(xtc_db_input($keywords));

        $keyword_lev_sim = xtc_db_input($keywords);
        $colors = explode(';', SEARCH_PROXIMITY_COLORS);

//fsk18 lock
        if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
            $fsk_lock = " AND p.products_fsk18 != '1' ";
        } else {
            unset($fsk_lock);
        }

//group check
        if (GROUP_CHECK == 'true') {
            $group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
        } else {
            unset($group_check);
        }

// search in keywords?
        if (SEARCH_PRODUCT_KEYWORDS == 'true') {
            $search_keywords = ', pd.products_keywords ';
        } else {
            unset($search_keywords);
        }

// search in description?
        if (SEARCH_PRODUCT_DESCRIPTION == 'true') {
            $search_description = ', pd.products_short_description, pd.products_description ';
        } else {
            unset($search_description);
        }


// alle Produktnamen holen
        $sql = "SELECT pd.products_name" . $search_keywords . $search_description . "
				FROM " . TABLE_PRODUCTS . " AS p 
				LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON (p.products_id = pd.products_id)
				LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS pc ON (p.products_id = pc.products_id) 
				LEFT JOIN " . TABLE_CATEGORIES . " AS c ON (pc.categories_id = c.categories_id)
				WHERE 
				p.products_status = '1'
				AND c.categories_status = 1
				AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "'" . $fsk_lock . $group_check;
        $product_query = xtDBquery($sql);

        $max_count = 0;
        $results['name'] = array();
        $results['proximity_color'] = array();

// jeden Produktnamen durchlaufen
        while ($product_array = xtc_db_fetch_array($product_query)) {

// Schlagwortstring zusammensetzen
            $word_string = $product_array['products_name'];
            if (SEARCH_PRODUCT_KEYWORDS == 'true')
                $word_string .= ' ' . $product_array['products_keywords'];
            if (SEARCH_PRODUCT_DESCRIPTION == 'true')
                $word_string .= ' ' . $this->getSpecialChars($product_array['products_short_description']) . ' ' . $this->getSpecialChars($product_array['products_description']);

// Strings zerlegen 
            if ((SEARCH_SPLIT_PRODUCT_NAMES == 'true') || (SEARCH_PRODUCT_KEYWORDS == 'true') || (SEARCH_PRODUCT_DESCRIPTION == 'true')) {
                $split_content = preg_split("/" . SEARCH_SPLIT_PRODUCT_CHARS . "+/", $word_string);
            } else {
                $split_content[0] = $word_string;
            }

// Schleife um gesplitte Namen zu pruefen starten bix maximale Ergebnisse vorhanden sind      
            $count = 0;
            foreach ($split_content as $split_names) {
                $split_names = trim($split_names);
// wenn split keine Zahl und laenger als X Zeichen
                if (strlen($split_names) > SEARCH_SPLIT_MINIMUM_LENGTH) {

// hoehe mit similar_text, levenshtein und/oder metaphone pruefen 
                    if (SEARCH_WEIGHT_LEVENSHTEIN > 0)
                        $prl = 100 - (10 * levenshtein($keyword_lev_sim, strtolower($split_names)));

                    if (SEARCH_WEIGHT_SIMILAR_TEXT > 0)
                        similar_text($keyword_lev_sim, strtolower($split_names), $prs);

                    if (SEARCH_WEIGHT_METAPHONE > 0)
                        similar_text($keyword_metaphone, metaphone($split_names), $prm);

// Uebereinstimmung berechnen              
                    $pr = ($prl * SEARCH_WEIGHT_LEVENSHTEIN / 100) + ($prs * SEARCH_WEIGHT_SIMILAR_TEXT / 100) + ($prm * SEARCH_WEIGHT_METAPHONE / 100);

                    if (($pr > SEARCH_PROXIMITY_TRIGGER) && (!in_array($split_names, $results['name']))) {
                        $proximity = sprintf('%01.0f', $pr);
                        $results['name'][$max_count] = $split_names;
                        $results['proximity'][$max_count] = $proximity;


// Farbabstufungen berechnen
                        if (SEARCH_ENABLE_PROXIMITY_COLOR == 'true') {
                            if ($results['proximity'][$max_count] < 60)
                                $proximity_color = $colors[4];
                            if ($results['proximity'][$max_count] >= 60)
                                $proximity_color = $colors[3];
                            if ($results['proximity'][$max_count] >= 70)
                                $proximity_color = $colors[2];
                            if ($results['proximity'][$max_count] >= 80)
                                $proximity_color = $colors[1];
                            if ($results['proximity'][$max_count] >= 90)
                                $proximity_color = $colors[0];
                            $results['proximity_color'][$max_count] = $proximity_color;
                        } else {
                            $results['proximity_color'] = array();
                        }
                        $max_count++;
                    }
                }
            }
        }

// Wenn Ergebnisse vorhanden, $results nach hoehe sortieren und ausgeben
        if ($max_count > 0) {
            if (SEARCH_ENABLE_PROXIMITY_COLOR == 'true') {
                array_multisort($results['proximity'], SORT_NUMERIC, SORT_DESC, $results['name'], SORT_ASC, SORT_STRING, $results['proximity_color'], SORT_ASC, SORT_STRING);
            } else {
                array_multisort($results['proximity'], SORT_NUMERIC, SORT_DESC, $results['name'], SORT_ASC, SORT_STRING);
            }
            $counter = 0;
            while ($results['name'][$counter] && $counter < SEARCH_MAX_KEXWORD_SUGGESTS) {

// Produktanzahl zum Keyword ermitteln?
                if (SEARCH_COUNT_PRODUCTS == 'true') {

// search in keywords?
                    if (SEARCH_PRODUCT_KEYWORDS == 'true') {
                        $count_keywords = " OR pd.products_keywords LIKE ('%" . addslashes($results['name'][$counter]) . "%')";
                    } else {
                        unset($count_keywords);
                    }

// search in description?
                    if (SEARCH_PRODUCT_DESCRIPTION == 'true') {
                        $count_description = " OR pd.products_short_description LIKE ('%" . addslashes(htmlentities($results['name'][$counter])) . "%')";
                        $count_description .=" OR pd.products_description LIKE ('%" . addslashes(htmlentities($results['name'][$counter])) . "%')";
                    } else {
                        unset($count_description);
                    }

                    $products_count_query = xtDBquery("SELECT COUNT(*) as 'products_count' FROM " . TABLE_PRODUCTS . " AS p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON (p.products_id = pd.products_id)
												WHERE p.products_status = '1'
														AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "'" . $fsk_lock . $group_check . " 
												AND ( pd.products_name LIKE ('%" . addslashes($results['name'][$counter]) . "%')" . $count_keywords . $count_description . ")");
                    $products_counter = xtc_db_fetch_array($products_count_query);
                    $results['products_count'][$counter] = $products_counter['products_count'];
                }

// ins Array        
                $this->resultKeywords[$counter] = array('SUGGEST_KEYWORD' => $results['name'][$counter],
                    'SUGGEST_PROXIMITY' => $results['proximity'][$counter] . '%',
                    'SUGGEST_COUNT' => $results['products_count'][$counter],
                    'SUGGEST_COLOR' => $results['proximity_color'][$counter],
                    'SUGGEST_LINK' => xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords=' . urlencode($results['name'][$counter])));
                $counter++;
            }

// jetzt noch ein paar Produkte finden  
            if (SEARCH_ENABLE_PRODUCTS_SUGGEST == 'true') {
                $counter = 0;
                $row = 0;
                while ($counter < SEARCH_MAX_PRODUCTS_SUGGEST) {

// search in keywords?
                    if (SEARCH_PRODUCT_KEYWORDS == 'true')
                        $count_keywords = " OR pd.products_keywords LIKE ('%" . addslashes($results['name'][$counter]) . "%')";
                    else
                        unset($count_keywords);

// search in description?
                    if (SEARCH_PRODUCT_DESCRIPTION == 'true') {
                        $count_description = " OR pd.products_short_description LIKE ('%" . addslashes(htmlentities($results['name'][$counter])) . "%')";
                        $count_description .=" OR pd.products_description LIKE ('%" . addslashes(htmlentities($results['name'][$counter])) . "%')";
                    } else {
                        unset($count_description);
                    }

                    $products_suggest_query = xtDBquery("SELECT DISTINCT 
													pd.*, 
													p.*, 
													cd.categories_id, 
													cd.categories_name, 
													ptc.categories_id
												FROM 
													((products AS p LEFT JOIN products_description AS pd ON (p.products_id = pd.products_id))
												INNER JOIN 
													" . TABLE_PRODUCTS_TO_CATEGORIES . " AS ptc ON (p.products_id = ptc.products_id)) 
												INNER JOIN 
													" . TABLE_CATEGORIES_DESCRIPTION . " AS cd ON (ptc.categories_id = cd.categories_id)
												WHERE 
													p.products_status = '1'
												AND 
													pd.language_id = '" . (int) $_SESSION['languages_id'] . "'" . $fsk_lock . $group_check . "
												AND 
													( pd.products_name LIKE ('%" . addslashes($results['name'][$counter]) . "%')" . $count_keywords . $count_description . ")
												LIMIT 
													" . (SEARCH_MAX_PRODUCTS_SUGGEST - $counter));

                    if (!is_object($product))
                        $product = new product();
                    while ($products_suggest = xtc_db_fetch_array($products_suggest_query, true)) {
                        if (($counter <= SEARCH_MAX_PRODUCTS_SUGGEST) && ($results['name'][$row])) {
                            $ii++;
                            $this->results[$counter] = array('PRODUCTS_NAME' => $products_suggest['products_name'],
                                'CATEGORIES_NAME' => $products_suggest['categories_name'],
                                'CATEGORIES_LINK' => xtc_href_link(FILENAME_DEFAULT, xtc_category_link($products_suggest['categories_id'], $products_suggest['categories_name'])),
                                'PRODUCTS_PROXIMITY' => $results['proximity'][$row] . '%',
                                'PRODUCTS_PROXIMITY_COLOR' => $results['proximity_color'][$row]);

                            $this->resultProducts[$counter] = $product->buildDataArray($products_suggest, 'thumbnail', 'fuzzy_search', $ii);
                            $this->new_fuzzy[$counter] = array_merge($this->resultProducts[$counter], $this->results[$counter]);
                        }
                        $counter++;
                    }
                    $row++;
                }
            }
        }

// Parsetime berechnen
        if (SEARCH_SHOW_PARSETIME == 'true') {
            $time_start = explode(' ', PARSE_START_TIME);
            $time_end = explode(' ', microtime());
            $this->parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
        }
        return;
    }

}

