<?php
/* -----------------------------------------------------------------
 * 	$Id: customer_memo.php 420 2013-06-19 18:04:39Z akausch $
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

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
?>
<td valign="top" class="main"><?php echo ENTRY_MEMO; ?></td>
<td class="main"><?php
    $memo_query = xtc_db_query("SELECT
                                  *
                              FROM
                                  " . TABLE_CUSTOMERS_MEMO . "
                              WHERE
                                  customers_id = '" . $_GET['cID'] . "'
                              ORDER BY
                                  memo_date DESC");
    while ($memo_values = xtc_db_fetch_array($memo_query)) {
        $poster_query = xtc_db_query("SELECT customers_firstname, customers_lastname FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $memo_values['poster_id'] . "'");
        $poster_values = xtc_db_fetch_array($poster_query);
        ?><table width="100%">
            <tr>
                <td class="main"><b><?php echo TEXT_DATE; ?></b>:<i><?php echo $memo_values['memo_date']; ?></i><b><?php echo TEXT_TITLE; ?></b>:<?php echo $memo_values['memo_title']; ?><b>  <?php echo TEXT_POSTER; ?></b>:<?php echo $poster_values['customers_lastname']; ?> <?php echo $poster_values['customers_firstname']; ?></td>
            </tr>
            <tr>
                <td width="142" class="main" style="border: 1px solid; border-color: #cccccc;"><?php echo $memo_values['memo_text']; ?></td>
            </tr>
            <tr>
                <td><a href="<?php echo xtc_href_link(FILENAME_CUSTOMERS, 'cID=' . $_GET['cID'] . '&action=edit&special=remove_memo&mID=' . $memo_values['memo_id']); ?>" onClick="return confirm('<?php echo DELETE_ENTRY; ?>')" class="button"><?php echo BUTTON_DELETE; ?></a></td>
            </tr>
        </table>
        <?php
    }
    ?>
    <table width="100%">
        <tr>
            <td class="main"><b><?php echo TEXT_TITLE ?></b>:<?php echo xtc_draw_input_field('memo_title'); ?><br><?php echo xtc_draw_textarea_field('memo_text', 'soft', '80', '5'); ?><br><input type="submit" class="button" value="<?php echo BUTTON_INSERT; ?>"></td>
        </tr>
    </table></td>