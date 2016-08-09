<?php

/** -----------------------------------------------------------------
 * 	ID:						$Id: class.split_page_results.php 598 2013-09-12 13:19:54Z akausch $
 * 	Letzter Stand:			$Revision: 360 $
 * 	zuletzt geändert von: 	$Author: akausch $
 * 	Datum:					$Date: 2013-06-05 15:01:19 +0200 (Mi, 05 Jun 2013) $
 *
 * 	commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 *
 * 	Copyright (c) since 2010 commerce:SEO
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 *
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- * */

class splitPageResults_ORIGINAL {

    var $sql_query, $number_of_rows, $current_page_number, $number_of_pages, $number_of_rows_per_page, $current_category_id;
	
	public function __construct($query, $page, $max_rows, $count_key = '*') {
        $this->sql_query = $query;
        if (empty($page) || (is_numeric($page) == false)) {
            $page = 1;
        }
        $this->current_page_number = $page;
        $this->number_of_rows_per_page = $max_rows;
        $pos_to = strlen($this->sql_query);
        $pos_from = strpos($this->sql_query, ' FROM', 0);
        $pos_group_by = strpos($this->sql_query, ' GROUP BY', $pos_from);
        if (($pos_group_by < $pos_to) && ($pos_group_by != false)) {
            $pos_to = $pos_group_by;
        }

        $pos_having = strpos($this->sql_query, ' HAVING', $pos_from);
        if (($pos_having < $pos_to) && ($pos_having != false)) {
            $pos_to = $pos_having;
        }

        $pos_order_by = strpos($this->sql_query, ' ORDER BY', $pos_from);
        if (($pos_order_by < $pos_to) && ($pos_order_by != false)) {
            $pos_to = $pos_order_by;
        }

        if (strpos($this->sql_query, 'DISTINCT') || strpos($this->sql_query, 'GROUP BY')) {
            $count_string = 'DISTINCT ' . xtc_db_input($count_key);
        } else {
            $count_string = xtc_db_input($count_key);
		}

        $count_query = xtDBquery($query);
        $count = xtc_db_num_rows($count_query, true);
        $this->number_of_rows = $count;
        $this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);
        // Avoid indexing of non existing pages, excluding the first page
        // (e.g. if there are no reviews, allow review.php!)
        if (($page > 1) && ($page > $this->number_of_pages)) {
            header("HTTP/1.0 404 Not Found");
		}

        if ($this->current_page_number > $this->number_of_pages) {
            $this->current_page_number = $this->number_of_pages;
		}

        $offset = $this->number_of_rows_per_page * ($this->current_page_number - 1);
        $offset = $offset < 0 ? 0 : $offset;

        $this->sql_query .= " LIMIT " . $offset . ", " . $this->number_of_rows_per_page . ";";
    }
    // public function splitPageResults_ORIGINAL($query, $page, $max_rows, $count_key = '*') {
        // $this->__construct($query, $page, $max_rows, $count_key);
    // }

    // display number of total products found Old Version
    public function display_links($max_page_links, $parameters = '') {
        global $PHP_SELF, $request_type;

        $display_links_string = '';
        $class = 'class="pageResults"';

        if (xtc_not_null($parameters) && (substr($parameters, -1) != '&'))
            $parameters .= '&';

        // previous button - not displayed on first page
        if ($this->current_page_number > 1)
            $display_links_string .= '<a href="' . xtc_href_link(basename($_SERVER['SCRIPT_NAME']), $parameters . 'page=' . ($this->current_page_number - 1), $request_type) . '" class="pageResults to_page_' . ($this->current_page_number - 1) . '" title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' ">' . PREVNEXT_BUTTON_PREV . '</a>&nbsp;&nbsp;';

        // check if number_of_pages > $max_page_links
        $cur_window_num = intval($this->current_page_number / $max_page_links);
        if ($this->current_page_number % $max_page_links)
            $cur_window_num++;

        $max_window_num = intval($this->number_of_pages / $max_page_links);
        if ($this->number_of_pages % $max_page_links)
            $max_window_num++;

        // previous window of pages
        if ($cur_window_num > 1)
            $display_links_string .= '<a href="' . xtc_href_link(basename($_SERVER['SCRIPT_NAME']), $parameters . 'page=' . (($cur_window_num - 1) * $max_page_links), $request_type) . '" class="pageResults to_page_' . (($cur_window_num - 1) * $max_page_links) . '" title=" ' . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>';

        // page nn button
        for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++) {
            if ($jump_to_page == $this->current_page_number) {
                $display_links_string .= '&nbsp;<b>' . $jump_to_page . '</b>&nbsp;';
            } else {
                $display_links_string .= '&nbsp;<a href="' . xtc_href_link(basename($_SERVER['SCRIPT_NAME']), $parameters . 'page=' . $jump_to_page, $request_type) . '" class="pageResults to_page_' . $jump_to_page . '" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' ">' . $jump_to_page . '</a>&nbsp;';
            }
        }

        // next window of pages
        if ($cur_window_num < $max_window_num)
            $display_links_string .= '<a href="' . xtc_href_link(basename($_SERVER['SCRIPT_NAME']), $parameters . 'page=' . (($cur_window_num) * $max_page_links + 1), $request_type) . '" class="pageResults to_page_' . (($cur_window_num) * $max_page_links + 1) . '" title=" ' . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>&nbsp;';

        // next button
        if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1))
            $display_links_string .= '&nbsp;<a href="' . xtc_href_link(basename($_SERVER['SCRIPT_NAME']), $parameters . 'page=' . ($this->current_page_number + 1), $request_type) . '" class="pageResults to_page_' . ($this->current_page_number + 1) . '" title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' ">' . PREVNEXT_BUTTON_NEXT . '</a>&nbsp;';

        return $display_links_string;
    }

    function getSEOLinksArray($max_page_links, $parameters = '', $text_output = "", $active_site = '') {
        global $request_type;
        $this->current_category_id = $_GET['cPath'];
        $start = $this->current_page_number - $max_page_links;
        $start = $start < 1 ? 1 : $start;
        $end = $this->current_page_number + $max_page_links;
        $end = $end > $this->number_of_pages ? $this->number_of_pages : $end;
        $links = array();
        $site = '&page=';
        $url = FILENAME_DEFAULT;

        // zurück Button - wird auf der ersten Seite nicht angezeigt
        if ($this->current_page_number > 1) {
            if ($this->current_page_number == 2) {
                if ($_GET['manufacturers_id'] != '') {
                    $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id']) . $parameters;
                } else {
                    $href = xtc_href_link($url, 'cPath=' . $this->current_category_id) . $parameters;
                }
            } else {
                if ($_GET['manufacturers_id'] != '') {
                    $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id']) . $site . ($this->current_page_number - 1) . $parameters;
                } else {
                    $href = xtc_href_link($url, 'cPath=' . $this->current_category_id) . $site . ($this->current_page_number - 1) . $parameters;
                }
            }

            $links['previous_button'] = array('title' => PREVNEXT_TITLE_PREVIOUS_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_PREV);

            //link: erste seite:
            if ($_GET['manufacturers_id'] != '') {
                $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id'], $parameters, $request_type);
            } else {
                $href = xtc_href_link($url, 'cPath=' . $this->current_category_id) . $parameters;
            }

            $links['first_page'] = array('title' => PREVNEXT_TITLE_FIRST_PAGE,
                'href' => $href,
                'name' => 1);
        }

        //link: letzte seite:
        if ($this->current_page_number < $this->number_of_pages) {
            if ($_GET['manufacturers_id'] != '') {
                $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id']) . $site . $this->number_of_pages . $parameters;
            } else {
                $href = xtc_href_link($url, 'cPath=' . $this->current_category_id) . $site . $this->number_of_pages . $parameters;
            }

            $links['last_page'] = array('title' => PREVNEXT_TITLE_LAST_PAGE,
                'href' => $href,
                'name' => $this->number_of_pages);
        }
        // vorwärts Button
        if ($this->current_page_number < $this->number_of_pages && $this->number_of_pages != 1) {
            if ($_GET['manufacturers_id'] != '') {
                $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id']) . $site . ($this->current_page_number + 1) . $parameters;
            } else {
                $href = xtc_href_link($url, 'cPath=' . $this->current_category_id) . $site . ($this->current_page_number + 1) . $parameters;
            }

            $links['next_button'] = array('title' => PREVNEXT_TITLE_NEXT_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_NEXT);
        }

        // check if number_of_pages > $max_page_links
        $cur_window_num = intval($this->current_page_number / $max_page_links);

        if ($this->current_page_number % $max_page_links) {
            $cur_window_num++;
        }

        $max_window_num = intval($this->number_of_pages / $max_page_links);

        if ($this->number_of_pages % $max_page_links) {
            $max_window_num++;
        }

        // previous window of pages
        if ($cur_window_num > 1) {
            $href = xtc_href_link($url, 'cPath=' . $this->current_category_id) . $site . (($cur_window_num - 1) * $max_page_links) . $parameters;


            $links['previous_window'] = array('title' => PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE,
                'href' => $href,
                'name' => $max_page_links);
        }

        if ($this->number_of_pages > 1)
            $_SESSION['template']['current_url'] = xtc_href_link($url, 'cPath=' . $this->current_category_id) . $site . $this->current_page_number . $parameters;
        else
            unset($_SESSION['template']['current_url']);

        // Seiten Anzeige (Mitte):
        if ($end > 1) {
            for ($cur = $start; $cur <= $end; $cur++) {
                if ($cur == $this->current_page_number) {
                    $links['pages'][] = array('title' => '',
                        'href' => '',
                        'name' => $cur);
                } else {
                    if ($cur == 1) {// avoid DC through page=1
                        if ($_GET['manufacturers_id'] != '') {
                            $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id']) . $parameters;
                        } else {
                            $href = xtc_href_link($url, 'cPath=' . $this->current_category_id) . $parameters;
                        }
                    } else {
                        if ($_GET['manufacturers_id'] != '') {
                            $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id']) . $site . $cur . $parameters;
                        } else {
                            $href = xtc_href_link($url, 'cPath=' . $this->current_category_id) . $site . $cur . $parameters;
                        }
                    }

                    $links['pages'][] = array('title' => sprintf(PREVNEXT_TITLE_PAGE_NO, $cur),
                        'href' => $href,
                        'name' => $cur);
                }
            }
        }

        // next window of pages
        if ($cur_window_num < $max_window_num) {

            $href = xtc_href_link($url, 'cPath=' . $this->current_category_id) . $site . (($cur_window_num) * $max_page_links + 1) . $parameters;

            $links['next_window'] = sprintf('<a href="%s" title="%s">...</a>', $href, sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links));
        }

        $links['current_page'] = $this->current_page_number;
        $links['total'] = $this->display_count($text_output);

        return $links;
    }

    public function getLinksArray($max_page_links, $parameters = '', $text_output = "", $active_site = '', $no_seo = false) {
        global $request_type;
        $PHP_SELF = $_SERVER['PHP_SELF'];
        $this->current_category_id = $_GET['cPath'];
        $start = $this->current_page_number - $max_page_links;
        $start = $start < 1 ? 1 : $start;
        $end = $this->current_page_number + $max_page_links;
        $end = $end > $this->number_of_pages ? $this->number_of_pages : $end;
        $links = array();
        $site = 'page=';

        if ($_GET['fcat'] != '') {
            $url = FILENAME_PRODUCT_FILTER;
        } else {
            $url = FILENAME_DEFAULT;
        }

        // zurueck Button - wird auf der ersten Seite nicht angezeigt
        if ($this->current_page_number > 1) {
            if ($this->current_page_number == 2) {
                if ($_GET['manufacturers_id'] != '') {
                    $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id'] . "&page=" . ($this->current_page_number - 1) . $parameters);
                } elseif ($_GET['fcat'] != '') {
                    $href = xtc_href_link($url, $parameters, $request_type);
                } else {
                    $href = xtc_href_link($url . "?cPath=" . $this->current_category_id . "&page=" . ($this->current_page_number - 1) . $parameters);
                }
            } else {
                if ($_GET['manufacturers_id'] != '') {
                    $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id'] . "&page=" . ($this->current_page_number - 1) . $parameters);
                } else if ($_GET['fcat'] != '') {
                    $href = xtc_href_link($url, $parameters . "page=" . ($this->current_page_number - 1));
                } else {
                    $href = xtc_href_link($url . "?cPath=" . $this->current_category_id . "&page=" . ($this->current_page_number - 1) . $parameters);
                }
            }

            $links['previous_button'] = array('title' => PREVNEXT_TITLE_PREVIOUS_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_PREV);

            //link: erste seite:
            if ($_GET['manufacturers_id'] != '') {
                $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id'] . "&page=1" . $parameters);
            } elseif ($_GET['fcat'] != '') {
                $href = xtc_href_link($url, $parameters . "page=1", $request_type);
            } else {
                $href = xtc_href_link($url . "?cPath=" . $this->current_category_id . "&page=1" . $parameters);
            }
            $links['first_page'] = array('title' => PREVNEXT_TITLE_FIRST_PAGE,
                'href' => $href,
                'name' => 1);
        }//if


        if ($this->current_page_number < $this->number_of_pages) {
            if ($_GET['manufacturers_id'] != '') {
                $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id'] . $parameters . $site . $this->number_of_pages);
            } else {
                $href = xtc_href_link($url . "?cPath=" . $this->current_category_id . "" . '&' . $site . $this->number_of_pages . $parameters);
            }
            $links['last_page'] = array('title' => PREVNEXT_TITLE_LAST_PAGE,
                'href' => $href,
                'name' => $this->number_of_pages);
        }
        // vorwaerts Button
        if ($this->current_page_number < $this->number_of_pages && $this->number_of_pages != 1) {
            if ($_GET['manufacturers_id'] != '') {
                $href = xtc_href_link($url . "?manufacturers_id=" . $_GET['manufacturers_id'] . "&page=" . ($this->current_page_number + 1) . $parameters);
            } elseif ($_GET['fcat'] != '') {
                $href = xtc_href_link($url, $parameters . "page=" . ($this->current_page_number + 1), $request_type);
            } else {
                $href = xtc_href_link($url . "?cPath=" . $this->current_category_id . "&page=" . ($this->current_page_number + 1) . $parameters);
            }
            $links['next_button'] = array('title' => PREVNEXT_TITLE_NEXT_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_NEXT);
        }

        // check if number_of_pages > $max_page_links
        $cur_window_num = intval($this->current_page_number / $max_page_links);

        if ($this->current_page_number % $max_page_links)
            $cur_window_num++;

        $max_window_num = intval($this->number_of_pages / $max_page_links);

        if ($this->number_of_pages % $max_page_links)
            $max_window_num++;


        // previous window of pages
        if ($cur_window_num > 1) {
            $href = xtc_href_link($url, $parameters . $site . (($cur_window_num - 1) * $max_page_links), $request_type);

            $links['previous_window'] = array('title' => PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE,
                'href' => $href,
                'name' => $max_page_links);
        }

        if ($this->number_of_pages > 1)
            $_SESSION['template']['current_url'] = $url . $parameters . $site . $this->current_page_number;
        else
            unset($_SESSION['template']['current_url']);

        // Seiten Anzeige (Mitte):
        if ($end > 1) {
            //cPath an url anhaengen

            if ($this->current_category_id != '') {
                $url .= "?cPath=" . $this->current_category_id;
            } else {
                if ($_GET['manufacturers_id'] != '') {
                    $url .= "?manufacturers_id=" . $_GET['manufacturers_id'];
                }
            }

            for ($cur = $start; $cur <= $end; $cur++) {
                if ($cur == $this->current_page_number) {
                    $links['pages'][] = array('title' => '',
                        'href' => '',
                        'name' => $cur);
                } else {
                    if ($cur == 1) { // avoid DC through page=1
                        $href = xtc_href_link($url, $parameters, $request_type);
                    } else {
                        if ($_GET['fcat'] != '') {
                            $href = xtc_href_link(FILENAME_PRODUCT_FILTER . $parameters . $site . $cur);
                        } else {
                            $href = xtc_href_link($url . '&' . $parameters . $site . $cur);
                        }
                    }

                    $links['pages'][] = array('title' => sprintf(PREVNEXT_TITLE_PAGE_NO, $cur),
                        'href' => $href,
                        'name' => $cur);
                }
            }//for
        }

        // next window of pages
        if ($cur_window_num < $max_window_num) {
            $href = xtc_href_link($url . $site . (($cur_window_num) * $max_page_links + 1), $parameters, $request_type);

            $links['next_window'] = sprintf('<a href="%s" title="%s">...</a>', $href, sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links));
        }

        $links['current_page'] = $this->current_page_number;
        $links['total'] = $this->display_count($text_output);

        return $links;
    }

    function getLinksArrayTag($max_page_links, $parameters = '', $text_output, $active_site = '', $tag) {
        global $request_type;
        $PHP_SELF = $_SERVER['PHP_SELF'];
        $start = $this->current_page_number - $max_page_links;
        $start = $start < 1 ? 1 : $start;
        $end = $this->current_page_number + $max_page_links;
        $end = $end > $this->number_of_pages ? $this->number_of_pages : $end;
        $links = array();

        if ($parameters != '' && (substr($parameters, -1) != '&'))
            $parameters .= '&';
        $site = 'page=';

        $tag = urlencode($tag);
        $url = 'tag/' . $tag . '/';
        // zurück Button - wird auf der ersten Seite nicht angezeigt
        if ($this->current_page_number > 1) {

            if ($this->current_page_number == 2)
                $href = xtc_href_link($url, $parameters, $request_type);
            else
                $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number - 1), $request_type);


            $links['previous_button'] = array('title' => PREVNEXT_TITLE_PREVIOUS_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_PREV);

            $href = xtc_href_link($url, $parameters, $request_type);

            $links['first_page'] = array('title' => PREVNEXT_TITLE_FIRST_PAGE,
                'href' => $href,
                'name' => 1);
        }

        if ($this->current_page_number < $this->number_of_pages) {
            $href = xtc_href_link($url, $parameters . $site . $this->number_of_pages, $request_type);

            $links['last_page'] = array('title' => PREVNEXT_TITLE_LAST_PAGE,
                'href' => $href,
                'name' => $this->number_of_pages);
        }
        // vorwärts Button
        if ($this->current_page_number < $this->number_of_pages && $this->number_of_pages != 1) {

            $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number + 1), $request_type);

            $links['next_button'] = array('title' => PREVNEXT_TITLE_NEXT_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_NEXT);
        }

        // check if number_of_pages > $max_page_links
        $cur_window_num = intval($this->current_page_number / $max_page_links);

        if ($this->current_page_number % $max_page_links)
            $cur_window_num++;

        $max_window_num = intval($this->number_of_pages / $max_page_links);

        if ($this->number_of_pages % $max_page_links)
            $max_window_num++;

        // previous window of pages
        if ($cur_window_num > 1) {
            $href = xtc_href_link($url, $parameters . $site . (($cur_window_num - 1) * $max_page_links), $request_type);

            $links['previous_window'] = array('title' => PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE,
                'href' => $href,
                'name' => $max_page_links);
        }

        if ($this->number_of_pages > 1)
            $_SESSION['template']['current_url'] = $url . $parameters . $site . $this->current_page_number;
        else
            unset($_SESSION['template']['current_url']);

        // page nn button
        if ($end > 1) {
            for ($cur = $start; $cur <= $end; $cur++) {
                if ($cur == $this->current_page_number) {
                    $links['pages'][] = array('title' => '',
                        'href' => '',
                        'name' => $cur);
                } else {
                    if ($cur == 1) // avoid DC through page=1
                        $href = xtc_href_link($url, $parameters, $request_type);
                    else
                        $href = xtc_href_link($url, $parameters . $site . $cur, $request_type);

                    $links['pages'][] = array('title' => sprintf(PREVNEXT_TITLE_PAGE_NO, $cur),
                        'href' => $href,
                        'name' => $cur);
                }
            }
        }

        // next window of pages
        if ($cur_window_num < $max_window_num) {

            $href = xtc_href_link($url . $site . (($cur_window_num) * $max_page_links + 1), $parameters, $request_type);

            $links['next_window'] = sprintf('<a href="%s" title="%s">...</a>', $href, sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links));
        }

        $links['current_page'] = $this->current_page_number;
        $links['total'] = $this->display_count($text_output);

        return $links;
    }

    function getLinksArrayHashtag($max_page_links, $parameters = '', $text_output, $active_site = '', $tag) {
        global $request_type;

        $PHP_SELF = $_SERVER['PHP_SELF'];

        $start = $this->current_page_number - $max_page_links;
        $start = $start < 1 ? 1 : $start;

        $end = $this->current_page_number + $max_page_links;
        $end = $end > $this->number_of_pages ? $this->number_of_pages : $end;

        $links = array();

        if ($parameters != '' && (substr($parameters, -1) != '&'))
            $parameters .= '&';
        $site = 'page=';

        $tag = urlencode($tag);

        $url = 'hashtag/' . $tag;

        // zurück Button - wird auf der ersten Seite nicht angezeigt
        if ($this->current_page_number > 1) {

            if ($this->current_page_number == 2)
                $href = xtc_href_link($url, $parameters, $request_type);
            else
                $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number - 1), $request_type);


            $links['previous_button'] = array('title' => PREVNEXT_TITLE_PREVIOUS_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_PREV);

            $href = xtc_href_link($url, $parameters, $request_type);

            $links['first_page'] = array('title' => PREVNEXT_TITLE_FIRST_PAGE,
                'href' => $href,
                'name' => 1);
        }

        if ($this->current_page_number < $this->number_of_pages) {
            $href = xtc_href_link($url, $parameters . $site . $this->number_of_pages, $request_type);

            $links['last_page'] = array('title' => PREVNEXT_TITLE_LAST_PAGE,
                'href' => $href,
                'name' => $this->number_of_pages);
        }
        // vorwärts Button
        if ($this->current_page_number < $this->number_of_pages && $this->number_of_pages != 1) {

            $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number + 1), $request_type);

            $links['next_button'] = array('title' => PREVNEXT_TITLE_NEXT_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_NEXT);
        }

        // check if number_of_pages > $max_page_links
        $cur_window_num = intval($this->current_page_number / $max_page_links);

        if ($this->current_page_number % $max_page_links)
            $cur_window_num++;

        $max_window_num = intval($this->number_of_pages / $max_page_links);

        if ($this->number_of_pages % $max_page_links)
            $max_window_num++;

        // previous window of pages
        if ($cur_window_num > 1) {
            $href = xtc_href_link($url, $parameters . $site . (($cur_window_num - 1) * $max_page_links), $request_type);

            $links['previous_window'] = array('title' => PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE,
                'href' => $href,
                'name' => $max_page_links);
        }

        if ($this->number_of_pages > 1)
            $_SESSION['template']['current_url'] = $url . $parameters . $site . $this->current_page_number;
        else
            unset($_SESSION['template']['current_url']);

        // page nn button
        if ($end > 1) {
            for ($cur = $start; $cur <= $end; $cur++) {
                if ($cur == $this->current_page_number) {
                    $links['pages'][] = array('title' => '',
                        'href' => '',
                        'name' => $cur);
                } else {
                    if ($cur == 1) // avoid DC through page=1
                        $href = xtc_href_link($url, $parameters, $request_type);
                    else
                        $href = xtc_href_link($url, $parameters . $site . $cur, $request_type);

                    $links['pages'][] = array('title' => sprintf(PREVNEXT_TITLE_PAGE_NO, $cur),
                        'href' => $href,
                        'name' => $cur);
                }
            }
        }

        // next window of pages
        if ($cur_window_num < $max_window_num) {

            $href = xtc_href_link($url . $site . (($cur_window_num) * $max_page_links + 1), $parameters, $request_type);

            $links['next_window'] = sprintf('<a href="%s" title="%s">...</a>', $href, sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links));
        }

        $links['current_page'] = $this->current_page_number;
        $links['total'] = $this->display_count($text_output);

        return $links;
    }

    function getLinksArraySearch($max_page_links, $parameters = '', $text_output, $active_site = '', $keywords) {
        global $request_type;

        $PHP_SELF = $_SERVER['PHP_SELF'];

        $start = $this->current_page_number - $max_page_links;
        $start = $start < 1 ? 1 : $start;

        $end = $this->current_page_number + $max_page_links;
        $end = $end > $this->number_of_pages ? $this->number_of_pages : $end;

        $links = array();

        if ($parameters != '' && (substr($parameters, -1) != '&'))
            $parameters .= '&';
        $site = 'page=';

        #$url = 'keywords/'.$keywords;
        $url = 'advanced_search_result.php';

        // zurück Button - wird auf der ersten Seite nicht angezeigt
        if ($this->current_page_number > 1) {

            if ($this->current_page_number == 2)
                $href = xtc_href_link($url, $parameters, $request_type);
            else
                $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number - 1), $request_type);


            $links['previous_button'] = array('title' => PREVNEXT_TITLE_PREVIOUS_PAGE,
                'href' => $href . '&keywords=' . $keywords,
                'name' => PREVNEXT_BUTTON_PREV);

            $href = xtc_href_link($url, $parameters, $request_type);

            $links['first_page'] = array('title' => PREVNEXT_TITLE_FIRST_PAGE,
                'href' => $href . '&keywords=' . $keywords,
                'name' => 1);
        }

        if ($this->current_page_number < $this->number_of_pages) {
            $href = xtc_href_link($url, $parameters . $site . $this->number_of_pages, $request_type);

            $links['last_page'] = array('title' => PREVNEXT_TITLE_LAST_PAGE,
                'href' => $href . '&keywords=' . $keywords,
                'name' => $this->number_of_pages);
        }
        // vorwärts Button
        if ($this->current_page_number < $this->number_of_pages && $this->number_of_pages != 1) {

            $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number + 1), $request_type);

            $links['next_button'] = array('title' => PREVNEXT_TITLE_NEXT_PAGE,
                'href' => $href . '&keywords=' . $keywords,
                'name' => PREVNEXT_BUTTON_NEXT);
        }

        // check if number_of_pages > $max_page_links
        $cur_window_num = intval($this->current_page_number / $max_page_links);

        if ($this->current_page_number % $max_page_links)
            $cur_window_num++;

        $max_window_num = intval($this->number_of_pages / $max_page_links);

        if ($this->number_of_pages % $max_page_links)
            $max_window_num++;

        // previous window of pages
        if ($cur_window_num > 1) {
            $href = xtc_href_link($url, $parameters . $site . (($cur_window_num - 1) * $max_page_links), $request_type);

            $links['previous_window'] = array('title' => PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE,
                'href' => $href . '&keywords=' . $keywords,
                'name' => $max_page_links);
        }

        if ($this->number_of_pages > 1)
            $_SESSION['template']['current_url'] = $url . $parameters . $site . $this->current_page_number;
        else
            unset($_SESSION['template']['current_url']);

        // page nn button
        if ($end > 1) {
            for ($cur = $start; $cur <= $end; $cur++) {
                if ($cur == $this->current_page_number) {
                    $links['pages'][] = array('title' => '',
                        'href' => '',
                        'name' => $cur);
                } else {
                    if ($cur == 1) // avoid DC through page=1
                        $href = xtc_href_link($url, $parameters, $request_type);
                    else
                        $href = xtc_href_link($url, $parameters . $site . $cur, $request_type);

                    $links['pages'][] = array('title' => sprintf(PREVNEXT_TITLE_PAGE_NO, $cur),
                        'href' => $href . '&keywords=' . $keywords,
                        'name' => $cur);
                }
            }
        }

        // next window of pages
        if ($cur_window_num < $max_window_num) {

            $href = xtc_href_link($url . $site . (($cur_window_num) * $max_page_links + 1), $parameters, $request_type);

            $links['next_window'] = sprintf('<a href="%s" title="%s">...</a>', $href, sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links));
        }

        $links['current_page'] = $this->current_page_number;
        $links['total'] = $this->display_count($text_output);

        return $links;
    }

    function getLinksArrayFilter($max_page_links, $parameters = '', $text_output, $active_site = '') {
        global $request_type;

        $PHP_SELF = $_SERVER['PHP_SELF'];

        $start = $this->current_page_number - $max_page_links;
        $start = $start < 1 ? 1 : $start;

        $end = $this->current_page_number + $max_page_links;
        $end = $end > $this->number_of_pages ? $this->number_of_pages : $end;

        $links = array();

        $parameters .= '&';
        $site = 'page=';

        $url = FILENAME_PRODUCT_FILTER . '?request';

        // zurück Button - wird auf der ersten Seite nicht angezeigt
        if ($this->current_page_number > 1) {

            if ($this->current_page_number == 2)
                $href = xtc_href_link($url . $parameters);
            else
                $href = xtc_href_link($url . $parameters . $site . ($this->current_page_number - 1));


            $links['previous_button'] = array('title' => PREVNEXT_TITLE_PREVIOUS_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_PREV);

            $href = xtc_href_link($url . $parameters);

            $links['first_page'] = array('title' => PREVNEXT_TITLE_FIRST_PAGE,
                'href' => $href,
                'name' => 1);
        }

        if ($this->current_page_number < $this->number_of_pages) {
            $href = xtc_href_link($url . $parameters . $site . $this->number_of_pages);

            $links['last_page'] = array('title' => PREVNEXT_TITLE_LAST_PAGE,
                'href' => $href,
                'name' => $this->number_of_pages);
        }
        // vorwärts Button
        if ($this->current_page_number < $this->number_of_pages && $this->number_of_pages != 1) {

            $href = xtc_href_link($url . $parameters . $site . ($this->current_page_number + 1));

            $links['next_button'] = array('title' => PREVNEXT_TITLE_NEXT_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_NEXT);
        }

        // check if number_of_pages > $max_page_links
        $cur_window_num = intval($this->current_page_number / $max_page_links);

        if ($this->current_page_number % $max_page_links)
            $cur_window_num++;

        $max_window_num = intval($this->number_of_pages / $max_page_links);

        if ($this->number_of_pages % $max_page_links)
            $max_window_num++;

        // previous window of pages
        if ($cur_window_num > 1) {
            $href = xtc_href_link($url . $parameters . $site . (($cur_window_num - 1) * $max_page_links), $request_type);

            $links['previous_window'] = array('title' => PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE,
                'href' => $href,
                'name' => $max_page_links);
        }

        if ($this->number_of_pages > 1)
            $_SESSION['template']['current_url'] = $url . $parameters . $site . $this->current_page_number;
        else
            unset($_SESSION['template']['current_url']);

        // page nn button
        if ($end > 1) {
            for ($cur = $start; $cur <= $end; $cur++) {
                if ($cur == $this->current_page_number) {
                    $links['pages'][] = array('title' => '',
                        'href' => '',
                        'name' => $cur);
                } else {
                    if ($cur == 1) // avoid DC through page=1
                        $href = xtc_href_link($url . $parameters);
                    else
                        $href = xtc_href_link($url . $parameters . $site . $cur);

                    $links['pages'][] = array('title' => sprintf(PREVNEXT_TITLE_PAGE_NO, $cur),
                        'href' => $href,
                        'name' => $cur);
                }
            }
        }

        // next window of pages
        if ($cur_window_num < $max_window_num) {

            $href = xtc_href_link($url . $site . (($cur_window_num) * $max_page_links + 1), $parameters);

            $links['next_window'] = sprintf('<a href="%s" title="%s">...</a>', $href, sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links));
        }

        $links['current_page'] = $this->current_page_number;
        $links['total'] = $this->display_count($text_output);

        return $links;
    }

    //Reviews Pagenavigation
    function getLinksArrayReviews($max_page_links, $parameters = '', $text_output, $active_site = '') {
        global $request_type;

        $PHP_SELF = $_SERVER['PHP_SELF'];

        $start = $this->current_page_number - $max_page_links;
        $start = $start < 1 ? 1 : $start;

        $end = $this->current_page_number + $max_page_links;
        $end = $end > $this->number_of_pages ? $this->number_of_pages : $end;

        $links = array();

        if ($parameters != '' && (substr($parameters, -1) != '&'))
            $parameters .= '&';
        $site = 'page=';

        $url = FILENAME_REVIEWS;

        // zurück Button - wird auf der ersten Seite nicht angezeigt
        if ($this->current_page_number > 1) {

            if ($this->current_page_number == 2)
                $href = xtc_href_link($url, $parameters, $request_type);
            else
                $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number - 1), $request_type);


            $links['previous_button'] = array('title' => PREVNEXT_TITLE_PREVIOUS_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_PREV);

            $href = xtc_href_link($url, $parameters, $request_type);

            $links['first_page'] = array('title' => PREVNEXT_TITLE_FIRST_PAGE,
                'href' => $href,
                'name' => 1);
        }

        if ($this->current_page_number < $this->number_of_pages) {
            $href = xtc_href_link($url, $parameters . $site . $this->number_of_pages, $request_type);

            $links['last_page'] = array('title' => PREVNEXT_TITLE_LAST_PAGE,
                'href' => $href,
                'name' => $this->number_of_pages);
        }
        // vorwärts Button
        if ($this->current_page_number < $this->number_of_pages && $this->number_of_pages != 1) {

            $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number + 1), $request_type);

            $links['next_button'] = array('title' => PREVNEXT_TITLE_NEXT_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_NEXT);
        }

        // check if number_of_pages > $max_page_links
        $cur_window_num = intval($this->current_page_number / $max_page_links);

        if ($this->current_page_number % $max_page_links)
            $cur_window_num++;

        $max_window_num = intval($this->number_of_pages / $max_page_links);

        if ($this->number_of_pages % $max_page_links)
            $max_window_num++;

        // previous window of pages
        if ($cur_window_num > 1) {
            $href = xtc_href_link($url, $parameters . $site . (($cur_window_num - 1) * $max_page_links), $request_type);

            $links['previous_window'] = array('title' => PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE,
                'href' => $href,
                'name' => $max_page_links);
        }

        if ($this->number_of_pages > 1)
            $_SESSION['template']['current_url'] = $url . $parameters . $site . $this->current_page_number;
        else
            unset($_SESSION['template']['current_url']);

        // page nn button
        if ($end > 1) {
            for ($cur = $start; $cur <= $end; $cur++) {
                if ($cur == $this->current_page_number) {
                    $links['pages'][] = array('title' => '',
                        'href' => '',
                        'name' => $cur);
                } else {
                    if ($cur == 1) // avoid DC through page=1
                        $href = xtc_href_link($url, $parameters, $request_type);
                    else
                        $href = xtc_href_link($url, $parameters . $site . $cur, $request_type);

                    $links['pages'][] = array('title' => sprintf(PREVNEXT_TITLE_PAGE_NO, $cur),
                        'href' => $href,
                        'name' => $cur);
                }
            }
        }

        // next window of pages
        if ($cur_window_num < $max_window_num) {

            $href = xtc_href_link($url . $site . (($cur_window_num) * $max_page_links + 1), $parameters, $request_type);

            $links['next_window'] = sprintf('<a href="%s" title="%s">...</a>', $href, sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links));
        }

        $links['current_page'] = $this->current_page_number;
        $links['total'] = $this->display_count($text_output);

        return $links;
    }

    //Products New Pagenavigation

    function getLinksArrayProductsNew($max_page_links, $parameters = '', $text_output, $active_site = '') {
        global $request_type;

        $PHP_SELF = $_SERVER['PHP_SELF'];

        $start = $this->current_page_number - $max_page_links;
        $start = $start < 1 ? 1 : $start;

        $end = $this->current_page_number + $max_page_links;
        $end = $end > $this->number_of_pages ? $this->number_of_pages : $end;

        $links = array();

        if ($parameters != '' && (substr($parameters, -1) != '&'))
            $parameters .= '&';
        $site = 'page=';

        $url = FILENAME_PRODUCTS_NEW;

        // zurück Button - wird auf der ersten Seite nicht angezeigt
        if ($this->current_page_number > 1) {

            if ($this->current_page_number == 2)
                $href = xtc_href_link($url, $parameters, $request_type);
            else
                $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number - 1), $request_type);


            $links['previous_button'] = array('title' => PREVNEXT_TITLE_PREVIOUS_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_PREV);

            $href = xtc_href_link($url, $parameters, $request_type);

            $links['first_page'] = array('title' => PREVNEXT_TITLE_FIRST_PAGE,
                'href' => $href,
                'name' => 1);
        }

        if ($this->current_page_number < $this->number_of_pages) {
            $href = xtc_href_link($url, $parameters . $site . $this->number_of_pages, $request_type);

            $links['last_page'] = array('title' => PREVNEXT_TITLE_LAST_PAGE,
                'href' => $href,
                'name' => $this->number_of_pages);
        }
        // vorwärts Button
        if ($this->current_page_number < $this->number_of_pages && $this->number_of_pages != 1) {

            $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number + 1), $request_type);

            $links['next_button'] = array('title' => PREVNEXT_TITLE_NEXT_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_NEXT);
        }

        // check if number_of_pages > $max_page_links
        $cur_window_num = intval($this->current_page_number / $max_page_links);

        if ($this->current_page_number % $max_page_links)
            $cur_window_num++;

        $max_window_num = intval($this->number_of_pages / $max_page_links);

        if ($this->number_of_pages % $max_page_links)
            $max_window_num++;

        // previous window of pages
        if ($cur_window_num > 1) {
            $href = xtc_href_link($url, $parameters . $site . (($cur_window_num - 1) * $max_page_links), $request_type);

            $links['previous_window'] = array('title' => PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE,
                'href' => $href,
                'name' => $max_page_links);
        }

        if ($this->number_of_pages > 1)
            $_SESSION['template']['current_url'] = $url . $parameters . $site . $this->current_page_number;
        else
            unset($_SESSION['template']['current_url']);

        // page nn button
        if ($end > 1) {
            for ($cur = $start; $cur <= $end; $cur++) {
                if ($cur == $this->current_page_number) {
                    $links['pages'][] = array('title' => '',
                        'href' => '',
                        'name' => $cur);
                } else {
                    if ($cur == 1) // avoid DC through page=1
                        $href = xtc_href_link($url, $parameters, $request_type);
                    else
                        $href = xtc_href_link($url, $parameters . $site . $cur, $request_type);

                    $links['pages'][] = array('title' => sprintf(PREVNEXT_TITLE_PAGE_NO, $cur),
                        'href' => $href,
                        'name' => $cur);
                }
            }
        }

        // next window of pages
        if ($cur_window_num < $max_window_num) {

            $href = xtc_href_link($url . $site . (($cur_window_num) * $max_page_links + 1), $parameters, $request_type);

            $links['next_window'] = sprintf('<a href="%s" title="%s">...</a>', $href, sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links));
        }

        $links['current_page'] = $this->current_page_number;
        $links['total'] = $this->display_count($text_output);

        return $links;
    }

    //Specials Pagenavigation

    function getLinksArraySpecials($max_page_links, $parameters = '', $text_output, $active_site = '') {
        global $request_type;

        $PHP_SELF = $_SERVER['PHP_SELF'];

        $start = $this->current_page_number - $max_page_links;
        $start = $start < 1 ? 1 : $start;

        $end = $this->current_page_number + $max_page_links;
        $end = $end > $this->number_of_pages ? $this->number_of_pages : $end;

        $links = array();

        if ($parameters != '' && (substr($parameters, -1) != '&'))
            $parameters .= '&';
        $site = 'page=';

        $url = FILENAME_SPECIALS;

        // zurück Button - wird auf der ersten Seite nicht angezeigt
        if ($this->current_page_number > 1) {

            if ($this->current_page_number == 2)
                $href = xtc_href_link($url, $parameters, $request_type);
            else
                $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number - 1), $request_type);


            $links['previous_button'] = array('title' => PREVNEXT_TITLE_PREVIOUS_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_PREV);

            $href = xtc_href_link($url, $parameters, $request_type);

            $links['first_page'] = array('title' => PREVNEXT_TITLE_FIRST_PAGE,
                'href' => $href,
                'name' => 1);
        }

        if ($this->current_page_number < $this->number_of_pages) {
            $href = xtc_href_link($url, $parameters . $site . $this->number_of_pages, $request_type);

            $links['last_page'] = array('title' => PREVNEXT_TITLE_LAST_PAGE,
                'href' => $href,
                'name' => $this->number_of_pages);
        }
        // vorwärts Button
        if ($this->current_page_number < $this->number_of_pages && $this->number_of_pages != 1) {

            $href = xtc_href_link($url, $parameters . $site . ($this->current_page_number + 1), $request_type);

            $links['next_button'] = array('title' => PREVNEXT_TITLE_NEXT_PAGE,
                'href' => $href,
                'name' => PREVNEXT_BUTTON_NEXT);
        }

        // check if number_of_pages > $max_page_links
        $cur_window_num = intval($this->current_page_number / $max_page_links);

        if ($this->current_page_number % $max_page_links)
            $cur_window_num++;

        $max_window_num = intval($this->number_of_pages / $max_page_links);

        if ($this->number_of_pages % $max_page_links)
            $max_window_num++;

        // previous window of pages
        if ($cur_window_num > 1) {
            $href = xtc_href_link($url, $parameters . $site . (($cur_window_num - 1) * $max_page_links), $request_type);

            $links['previous_window'] = array('title' => PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE,
                'href' => $href,
                'name' => $max_page_links);
        }

        if ($this->number_of_pages > 1)
            $_SESSION['template']['current_url'] = $url . $parameters . $site . $this->current_page_number;
        else
            unset($_SESSION['template']['current_url']);

        // page nn button
        if ($end > 1) {
            for ($cur = $start; $cur <= $end; $cur++) {
                if ($cur == $this->current_page_number) {
                    $links['pages'][] = array('title' => '',
                        'href' => '',
                        'name' => $cur);
                } else {
                    if ($cur == 1) // avoid DC through page=1
                        $href = xtc_href_link($url, $parameters, $request_type);
                    else
                        $href = xtc_href_link($url, $parameters . $site . $cur, $request_type);

                    $links['pages'][] = array('title' => sprintf(PREVNEXT_TITLE_PAGE_NO, $cur),
                        'href' => $href,
                        'name' => $cur);
                }
            }
        }

        // next window of pages
        if ($cur_window_num < $max_window_num) {

            $href = xtc_href_link($url . $site . (($cur_window_num) * $max_page_links + 1), $parameters, $request_type);

            $links['next_window'] = sprintf('<a href="%s" title="%s">...</a>', $href, sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links));
        }

        $links['current_page'] = $this->current_page_number;
        $links['total'] = $this->display_count($text_output);

        return $links;
    }

    // display number of total products found
    function display_count($text_output) {
        $to_num = ($this->number_of_rows_per_page * $this->current_page_number);
        if ($to_num > $this->number_of_rows)
            $to_num = $this->number_of_rows;

        $from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

        if ($to_num == 0) {
            $from_num = 0;
        } else {
            $from_num++;
        }

        return sprintf($text_output, $from_num, $to_num, $this->number_of_rows);
    }

}
