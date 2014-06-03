<?php

/*
 * #########################################################################################################
 * Project: Blog_3.0
 * #########################################################################################################
 * 
 * application_blog.php
 * 
 * 23.02.2014 www.indiv-style.de
 * 
 * Copyright by H&S eCom 
 * @author little Pit(S.B.)
 * 
 * #########################################################################################################
 */
define('FILENAME_BLOG','blog.php');

define('TABLE_BLOG_START','blog_start');  
define('TABLE_BLOG_CATEGORIES','blog_categories'); 
define('TABLE_BLOG_ITEMS','blog_items');
define('TABLE_BLOG_SETTINGS','blog_settings');
define('TABLE_BLOG_VOTE','blog_vote');
define('TABLE_BLOG_COMMENTS','blog_comment');

define('TABLE_BLOG_CATIMG','blog_cat_images');
define('TABLE_BLOG_ITEMIMG','blog_item_images');
define('TABLE_BLOG_ITEMKAT','blog_item_kat');
define('TABLE_BLOG_ITEMART','blog_item_article');
define('TABLE_BLOG_ITEMITEM','blog_item_item');
define('TABLE_BLOG_ITEMTEXT','blog_item_text');
define('TABLE_BLOG_STARTIMG','blog_start_images');
define('TABLE_BLOG_COMCOM','blog_com_comments');
define('TEXT_PRODUCTS_IMAGE', 'Artikelbild:');
define('TEXT_DELETE', 'l&ouml;schen');

$picstartwert = xtc_db_fetch_array(xtc_db_query("select wert from blog_settings where blog_key = 'pic_start'"));
define('PICSTART', $picstartwert['wert']);
$piccatwert = xtc_db_fetch_array(xtc_db_query("select wert from blog_settings where blog_key = 'pic_cat'"));
define('PICCAT', $piccatwert['wert']);
$picitemwert = xtc_db_fetch_array(xtc_db_query("select wert from blog_settings where blog_key = 'pic_item'"));
define('PICITEM', $picitemwert['wert']);


function xtc_get_blog_mo_images($pID = '') {
	$images_query = xtDBquery("SELECT * FROM blog_item_images WHERE slid_id =".$pID);

    while ($row = xtc_db_fetch_array($images_query, true))
        $results[($row['image_nr'] - 1)] = $row;
    if (is_array($results))
        return $results;
    else
        return false;
}

function xtc_get_blogcat_mo_images($pID = '') {
	$images_query = xtDBquery("SELECT * FROM blog_cat_images WHERE cat_id =".$pID);

    while ($row = xtc_db_fetch_array($images_query, true))
        $results[($row['image_nr'] - 1)] = $row;
    if (is_array($results))
        return $results;
    else
        return false;
}

function xtc_get_blogstart_mo_images() {
	$images_query = xtDBquery("SELECT * FROM blog_start_images WHERE start_id = 1");

    while ($row = xtc_db_fetch_array($images_query, true))
        $results[($row['image_nr'] - 1)] = $row;
    if (is_array($results))
        return $results;
    else
        return false;
}

