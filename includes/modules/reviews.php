<?php
/* -----------------------------------------------------------------
 * 	$Id: reviews.php 522 2013-07-24 11:44:51Z akausch $
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

if (sizeof($reviews_array) < 1) {
    ?> 
    <div><?php echo TEXT_NO_REVIEWS; ?></div>
    <?php
} else {
    for ($i = 0, $n = sizeof($reviews_array); $i < $n; $i++) {
        ?>
        <div style="width:100%">
            <div style="float:left; width:30%">
                <div align="center">
                    <?php echo'<a href=\"' . xtc_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $reviews_array[$i]['products_id'] . '&reviews_id=' . $reviews_array[$i]['reviews_id']) . '">' . xtc_image(DIR_WS_THUMBNAIL_IMAGES . $reviews_array[$i]['products_image'], $reviews_array[$i]['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?>
                </div>
            </div>

            <div style="float:left; width:69.8%">
                <?php echo'<a href=\"' . xtc_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $reviews_array[$i]['products_id'] . '&reviews_id=' . $reviews_array[$i]['reviews_id']) . '"><b><u>' . $reviews_array[$i]['products_name'] . '</u></b></a> (' . sprintf(TEXT_REVIEW_BY, substr($reviews_array[$i]['authors_name'], 0, strrpos($reviews_array[$i]['authors_name'], ' ') + 2) . '.') . ', ' . sprintf(TEXT_REVIEW_WORD_COUNT, $reviews_array[$i]['word_count']) . ')<br />' . $reviews_array[$i]['review'] . '<br /><br /><i>' . sprintf(TEXT_REVIEW_RATING, xtc_image(DIR_WS_IMAGES . 'stars_' . $reviews_array[$i]['rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews_array[$i]['rating'])), sprintf(TEXT_OF_5_STARS, $reviews_array[$i]['rating'])) . '<br />' . sprintf(TEXT_REVIEW_DATE_ADDED, $reviews_array[$i]['date_added']) . '</i>'; ?>
            </div>
            <br style="clear:both" />
        </div>
        <?php
        if (($i + 1) != $n) {
            ?>
            <?php
        }
    }
}
?>
