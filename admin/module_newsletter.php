<?php
/* -----------------------------------------------------------------
* 	$Id: module_newsletter.php 872 2014-03-21 14:46:30Z akausch $
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

require('includes/application_top.php');

require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.phpmailer.php');
require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
require_once (DIR_FS_INC . 'commerce_seo.inc.php');
!$commerceSeo ? $commerceSeo = new CommerceSeo() : false;
}

$smarty = new Smarty;

switch ($_GET['action']) {  // actions for datahandling
case 'save': // save newsletter

$id = xtc_db_prepare_input((int) $_POST['ID']);
$status_all = xtc_db_prepare_input($_POST['status_all']);
if ($newsletter_title == '')
$newsletter_title = 'no title';
$customers_status = xtc_get_customers_statuses();

$rzp = '';
for ($i = 0, $n = sizeof($customers_status); $i < $n; $i++) {
if (xtc_db_prepare_input($_POST['status'][$i]) == 'yes') {
if ($rzp != '')
$rzp.=',';
$rzp.=$customers_status[$i]['id'];
}
}

if (xtc_db_prepare_input($_POST['status_all']) == 'yes')
$rzp.=',all';

$error = false; // reset error flag
if ($error == false) {

$newsletter_personalize = xtc_db_prepare_input($_POST['newsletter_personalize']);
$newsletter_greeting = xtc_db_prepare_input($_POST['newsletter_greeting']);
$newsletter_gift = xtc_db_prepare_input($_POST['newsletter_gift']);
$newsletter_gift_ammount = xtc_db_prepare_input($_POST['newsletter_gift_ammount']);
// Product List
if ($_POST['newsletter_product_list'] != '') {
$newsletter_product_list = xtc_db_prepare_input($_POST['newsletter_product_list']);
$sql_data_array = array('title' => xtc_db_prepare_input($_POST['title']),
'status' => '0',
'bc' => $rzp,
'cc' => xtc_db_prepare_input($_POST['cc']),
'date' => 'now()',
'body' => xtc_db_prepare_input($_POST['newsletter_body']),
'personalize' => $newsletter_personalize,
'greeting' => $newsletter_greeting,
'gift' => $newsletter_gift,
'ammount' => $newsletter_gift_ammount,
'product_list' => $newsletter_product_list);
} else {
$sql_data_array = array('title' => xtc_db_prepare_input($_POST['title']),
'status' => '0',
'bc' => $rzp,
'cc' => xtc_db_prepare_input($_POST['cc']),
'date' => 'now()',
'body' => xtc_db_prepare_input($_POST['newsletter_body']),
'personalize' => $newsletter_personalize,
'greeting' => $newsletter_greeting,
'gift' => $newsletter_gift,
'ammount' => $newsletter_gift_ammount);
}

if ($id != '') {
xtc_db_perform(TABLE_MODULE_NEWSLETTER, $sql_data_array, 'update', "newsletter_id = '" . $id . "'");
xtc_db_query("DROP TABLE IF EXISTS module_newsletter_temp_" . $id);
xtc_db_query("CREATE TABLE module_newsletter_temp_" . $id . "
(
id int(11) NOT NULL auto_increment,
customers_id int(11) NOT NULL default '0',
customers_status int(11) NOT NULL default '0',
customers_firstname varchar(64) NOT NULL default '',
customers_lastname varchar(64) NOT NULL default '',
customers_email_address text NOT NULL,
mail_key varchar(32) NOT NULL,
date datetime NOT NULL default '0000-00-00 00:00:00',
comment varchar(64) NOT NULL default '',
PRIMARY KEY  (id)
)");
} else {
xtc_db_perform(TABLE_MODULE_NEWSLETTER, $sql_data_array);
// create temp table
$id = xtc_db_insert_id();
xtc_db_query("DROP TABLE IF EXISTS module_newsletter_temp_" . $id);
xtc_db_query("CREATE TABLE module_newsletter_temp_" . $id . "
(
id int(11) NOT NULL auto_increment,
customers_id int(11) NOT NULL default '0',
customers_status int(11) NOT NULL default '0',
customers_firstname varchar(64) NOT NULL default '',
customers_lastname varchar(64) NOT NULL default '',
customers_email_address text NOT NULL,
mail_key varchar(32) NOT NULL,
date datetime NOT NULL default '0000-00-00 00:00:00',
comment varchar(64) NOT NULL default '',
PRIMARY KEY  (id)
)");
}

// filling temp table with data!
$flag = '';
if (!strpos($rzp, 'all'))
$flag = 'true';
$rzp = str_replace(',all', '', $rzp);
$groups = explode(',', $rzp);
$sql_data_array = '';

for ($i = 0, $n = sizeof($groups); $i < $n; $i++) {
// check if customer wants newsletter

if (xtc_db_prepare_input($_POST['status_all']) == 'yes') {
$customers_query = xtc_db_query("SELECT customers_id, customers_firstname, customers_lastname, customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_status='" . $groups[$i] . "'");
} else {
$customers_query = xtc_db_query("SELECT customers_email_address, customers_id, customers_firstname, customers_lastname, mail_key FROM " . TABLE_NEWSLETTER_RECIPIENTS . " WHERE customers_status='" . $groups[$i] . "' AND mail_status='1'");
}
while ($customers_data = xtc_db_fetch_array($customers_query)) {
$sql_data_array = array(
'customers_id' => $customers_data['customers_id'],
'customers_status' => $groups[$i],
'customers_firstname' => $customers_data['customers_firstname'],
'customers_lastname' => $customers_data['customers_lastname'],
'customers_email_address' => $customers_data['customers_email_address'],
'mail_key' => $customers_data['mail_key'],
'date' => 'now()');

xtc_db_perform('module_newsletter_temp_' . $id, $sql_data_array);
}
}

xtc_redirect(xtc_href_link(FILENAME_MODULE_NEWSLETTER));
}

break;

case 'delete':

xtc_db_query("DELETE FROM " . TABLE_MODULE_NEWSLETTER . " WHERE   newsletter_id='" . (int) $_GET['ID'] . "'");
xtc_redirect(xtc_href_link(FILENAME_MODULE_NEWSLETTER));

break;

case 'send':
// max email package  -> should be in admin area!
$package_size = '30';
xtc_redirect(xtc_href_link(FILENAME_MODULE_NEWSLETTER, 'send=0,' . $package_size . '&ID=' . (int) $_GET['ID']));
}

// action for sending mails!

if ($_GET['send']) {

$limits = explode(',', $_GET['send']);
$limit_low = $limits['0'];
$limit_up = $limits['1'];

$limit_data = xtc_db_fetch_array(xtc_db_query("SELECT count(*) as count FROM module_newsletter_temp_" . (int) $_GET['ID'] . " "));

// select emailrange from db

$email_query = xtc_db_query("SELECT n.customers_firstname, n.customers_lastname, n.customers_email_address, n.mail_key, n.id, c.customers_gender FROM module_newsletter_temp_" . (int) $_GET['ID'] . " n, customers c WHERE n.customers_id = c.customers_id LIMIT " . $limit_low . "," . $limit_up);
$email_data = array();
while ($email_query_data = xtc_db_fetch_array($email_query)) {

$email_data[] = array('id' => $email_query_data['id'],
'firstname' => $email_query_data['customers_firstname'],
'lastname' => $email_query_data['customers_lastname'],
'email' => $email_query_data['customers_email_address'],
'key' => $email_query_data['mail_key'],
'customers_gender' => $email_query_data['customers_gender']);
}

// ok lets send the mails in package of 30 mails, to prevent php timeout
$package_size = '30';
$break = '0';
if ($limit_data['count'] < $limit_up) {
$limit_up = $limit_data['count'];
$break = '1';
}
$max_runtime = $limit_up - $limit_low;
$newsletters_query = xtc_db_query("SELECT * FROM " . TABLE_MODULE_NEWSLETTER . " WHERE  newsletter_id='" . (int) $_GET['ID'] . "'");
$newsletters_data = xtc_db_fetch_array($newsletters_query);

require_once (DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
// require (DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.xtcprice.php');
$xtPrice = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);

$module_content = array();

define('FILENAME_PRODUCT_INFO', 'product_info.php');

$products_query = xtc_db_query("SELECT * FROM " . TABLE_NEWSLETTER_PRODUCTS . " np, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE np.accessories_id = '" . $newsletters_data['product_list'] . "' AND p.products_id = np.product_id AND pd.products_id = p.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "'");

while ($product = xtc_db_fetch_array($products_query)) {

$image = '';
if ($product['products_image'] != '')
$image = HTTP_SERVER . DIR_WS_CATALOG . 'images/product_images/thumbnail_images/' . $product['products_image'];
else
$image = HTTP_SERVER . DIR_WS_CATALOG . 'images/product_images/thumbnail_images/no_img.jpg';

$fsk18 = '';
if ($product['products_fsk18'] == '1')
$fsk18 = 'true';

$price = $xtPrice->xtcGetPrice($product['products_id'], $format = true, 1, $product['products_tax_class_id'], $product['products_price'], 1);
if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True')
$products_link = $commerceSeo->getProductLink(xtc_product_link($product['products_id'], $product['products_name']), $connection, $_SESSION['languages_id']);
else
$products_link = xtc_product_link($product['products_id'], $product['products_name']);

$module_content[] = array('PRODUCTS_NAME' => $product['products_name'],
'PRODUCTS_MODEL' => $product['products_model'],
'PRODUCTS_EAN' => $product['products_ean'],
'PRODUCTS_SHORT_DESCRIPTION' => $product['products_short_description'],
'PRODUCTS_IMAGE' => $image,
'PRODUCTS_PRICE' => $price['formated'],
'PRODUCTS_LINK' => $products_link,
'PRODUCTS_FSK18' => $fsk18,
'PRODUCTS_ID' => $product['products_id']);
}

for ($i = 1; $i <= $max_runtime; $i++) {
// mail

$link1 = chr(13) . chr(10) . chr(13) . chr(10) . TEXT_NEWSLETTER_REMOVE . chr(13) . chr(10) . chr(13) . chr(10) . HTTP_CATALOG_SERVER . DIR_WS_CATALOG . FILENAME_CATALOG_NEWSLETTER . '?action=remove&email=' . $email_data[$i - 1]['email'] . '&key=' . $email_data[$i - 1]['key'];

$link2 = $link2 = '<br><br>' . TEXT_NEWSLETTER_REMOVE . '<br><a href="' . HTTP_CATALOG_SERVER . DIR_WS_CATALOG . FILENAME_CATALOG_NEWSLETTER . '?action=remove&email=' . $email_data[$i - 1]['email'] . '&key=' . $email_data[$i - 1]['key'] . '">' . TEXT_REMOVE_LINK . '</a>';

// Gutscheincode Anfang
if ($newsletters_data['gift'] == 'yes') {
$id1 = create_coupon_code($email_data[$i - 1]['email']);
$insert_query = xtc_db_query("insert into " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) values ('" . $id1 . "', 'G', '" . $newsletters_data['ammount'] . "', now())");
$insert_id = xtc_db_insert_id($insert_query);
$insert_query = xtc_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $insert_id . "', '0', 'Admin', '" . $email_data[$i - 1]['email'] . "', now() )");


$ammount = $newsletters_data['ammount'] . " EUR";
$gift_id = $id1;
}
// Gutscheincode Ende

$smarty->template_dir = DIR_FS_CATALOG . 'templates';
$smarty->compile_dir = DIR_FS_CATALOG . 'templates_c';
$smarty->config_dir = DIR_FS_CATALOG . 'lang';

$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

$smarty->assign('background_path', HTTP_SERVER . DIR_WS_CATALOG . 'images/newsletter/');
$smarty->assign('body', $newsletters_data['body']);
$smarty->assign('gift_ammount', $ammount);
$smarty->assign('gift_id', $gift_id);
$smarty->assign('remove_link', $link2);
$smarty->assign('shop_name', STORE_NAME);
$smarty->assign('greeting_type', $newsletters_data['greeting']);
$smarty->assign('module_content', $module_content);

$smarty->assign('customers_gender', $email_data[$i - 1]['customers_gender']);
$smarty->assign('personalize', $newsletters_data['personalize']);
$smarty->assign('customers_name', $email_data[$i - 1]['firstname'] . ' ' . $email_data[$i - 1]['lastname']);


require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
$smarty->caching = false;
$html_mail = $smarty->fetch('html:newsletter');
$html_mail .= $signatur_html;
$smarty->caching = false;
$txt_mail = $smarty->fetch('txt:newsletter');
$txt_mail .= $signatur_text;
require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
$mail_data = cseo_get_mail_data('newsletter');

$newletter_name = str_replace('{$shop}', STORE_NAME, $mail_data['EMAIL_ADDRESS_NAME']);
$newletter_name = str_replace('{$date_today}', date('d.m.Y'), $mail_data['EMAIL_SUBJECT']);

xtc_php_mail($mail_data['EMAIL_ADDRESS'], $newletter_name, $email_data[$i - 1]['email'], $email_data[$i - 1]['lastname'] . ' ' . $email_data[$i - 1]['firstname'], '', $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], '', '', $newsletters_data['title'], $html_mail, $txt_mail);

xtc_db_query("UPDATE module_newsletter_temp_" . (int) $_GET['ID'] . " SET comment='send' WHERE id='" . $email_data[$i - 1]['id'] . "'");
}
if ($break == '1') {
// finished

$limit1_query = xtc_db_query("SELECT count(*) as count FROM module_newsletter_temp_" . (int) $_GET['ID'] . " WHERE comment='send'");
$limit1_data = xtc_db_fetch_array($limit1_query);

if ($limit1_data['count'] - $limit_data['count'] <= 0) {
xtc_db_query("UPDATE " . TABLE_MODULE_NEWSLETTER . " SET status='1' WHERE newsletter_id='" . (int) $_GET['ID'] . "'");
xtc_redirect(xtc_href_link(FILENAME_MODULE_NEWSLETTER));
} else {
echo '<b>' . $limit1_data['count'] . '<b> emails send<br>';
echo '<b>' . $limit1_data['count'] - $limit_data['count'] . '<b> emails left';
}
} else {
$limit_low = $limit_up + 1;
$limit_up = $limit_low + $package_size;
xtc_redirect(xtc_href_link(FILENAME_MODULE_NEWSLETTER, 'send=' . $limit_low . ',' . $limit_up . '&ID=' . (int) $_GET['ID']));
}
}

require(DIR_WS_INCLUDES . 'header.php');

if (isset($_GET['action']) && ($_GET['action'] == 'new')) {
?>
<script type="text/javascript">
<!--
function checkIt(form) {
if (form.title.value != 0)
return true;
else {
alert('Geben Sie einen Newletter Titel ein.');
return false;
}
}
//-->
</script>
<?php } ?>


<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td>
<table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td class="pageHeading"><?php echo HEADING_TITLE_NEWSLETTER; ?></td>
</tr>
</table>
</td>
</tr>
<?php
if ($_GET['send']) {
?>
<tr><td>
<?php echo NEWSLETTER_SEND; ?>
</td></tr>
<?php
}
?>
<tr>
<td>
<table width="100%" border="0">
<tr>
<td>
<?php
switch ($_GET['action']) {
default:
$customer_group_query = xtc_db_query("SELECT customers_status_name, customers_status_id, customers_status_image FROM " . TABLE_CUSTOMERS_STATUS . " WHERE language_id='" . (int) $_SESSION['languages_id'] . "'");
$customer_group = array();
while ($customer_group_data = xtc_db_fetch_array($customer_group_query)) {
$group_query = xtc_db_query("SELECT count(*) as count FROM " . TABLE_NEWSLETTER_RECIPIENTS . " WHERE mail_status='1' and customers_status='" . $customer_group_data['customers_status_id'] . "'");
$group_data = xtc_db_fetch_array($group_query);
$customer_group[] = array('ID' => $customer_group_data['customers_status_id'],
'NAME' => $customer_group_data['customers_status_name'],
'IMAGE' => $customer_group_data['customers_status_image'],
'USERS' => $group_data['count']);
}
?>
<br>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
<tr class="dataTableHeadingRow">
<th class="dataTableHeadingContent" width="150" ><?php echo TITLE_CUSTOMERS; ?></th>
<th class="dataTableHeadingContent last"  ><?php echo TITLE_STK; ?></th>
</tr>

<?php
for ($i = 0, $n = sizeof($customer_group); $i < $n; $i++) {
?>
<tr>
<td valign="middle" align="left"><?php echo xtc_image('../' . DIR_WS_ICONS . $customer_group[$i]['IMAGE'], ''); ?><?php echo $customer_group[$i]['NAME']; ?></td>
<td align="left" class="last"><?php echo $customer_group[$i]['USERS']; ?></td>
</tr>
<?php
}
?>
</table></td>
<td width="30%" align="right" valign="top"><?php
echo '<a class="button" href="' . xtc_href_link(FILENAME_MODULE_NEWSLETTER, 'action=new') . '">' . BUTTON_NEW_NEWSLETTER . '</a>';
?></td>
</tr>
</table>
<br>
<?php
// get data for newsletter overwiev
$newsletters_query = xtc_db_query("SELECT * FROM " . TABLE_MODULE_NEWSLETTER . " WHERE status='0';");
$news_data = array();
while ($newsletters_data = xtc_db_fetch_array($newsletters_query)) {
$news_data[] = array('id' => $newsletters_data['newsletter_id'],
'date' => $newsletters_data['date'],
'title' => $newsletters_data['title']);
}
?>
<table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" width="30" ><?php echo TITLE_DATE; ?></td>
<td class="dataTableHeadingContent last" width="80%" ><?php echo TITLE_NOT_SEND; ?></td>
</tr>
<?php
for ($i = 0, $n = sizeof($news_data); $i < $n; $i++) {
if ($news_data[$i]['id'] != '') {
?>
<tr>
<td class="dataTableContent_products" align="left"><?php echo $news_data[$i]['date']; ?></td>
<td class="dataTableContent_products last" valign="middle" align="left"><a href="<?php echo xtc_href_link(FILENAME_MODULE_NEWSLETTER, 'ID=' . $news_data[$i]['id']); ?>"><b><?php echo $news_data[$i]['title']; ?></b></a></td>

</tr>
<?php
if ($_GET['ID'] != '' && $_GET['ID'] == $news_data[$i]['id']) {

$total_query = xtc_db_query("SELECT count(*) as count FROM module_newsletter_temp_" . (int) $_GET['ID'] . "");
$total_data = xtc_db_fetch_array($total_query);
?>
<tr>
<td class="dataTableContent_products" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left"></td>
<td colspan="2" class="dataTableContent_products" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left"><?php echo TEXT_SEND_TO . $total_data['count']; ?></td>
</tr>
<td class="dataTableContent" valign="top" style="border-bottom: 1px solid; border-color: #999999;" align="left">
<a class="button" href="<?php echo xtc_href_link(FILENAME_MODULE_NEWSLETTER, 'action=delete&ID=' . $news_data[$i]['id']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')"><?php echo BUTTON_DELETE . '</a><br>'; ?>
<a class="button" href="<?php echo xtc_href_link(FILENAME_MODULE_NEWSLETTER, 'action=edit&ID=' . $news_data[$i]['id']); ?>"><?php echo BUTTON_EDIT . '</a>'; ?>
<br><br><br><br><br><br><br><br><div style="height: 1px; background: Black; margin: 3px 0;"></div>
<a class="button" href="<?php echo xtc_href_link(FILENAME_MODULE_NEWSLETTER, 'action=send&ID=' . $news_data[$i]['id']); ?>"><?php echo BUTTON_SEND . '</a>'; ?>

</td>
<td colspan="2" class="dataTableContent" style="border-bottom: 1px solid; border-color: #999999; text-align: left;">
<?php
$newsletters_query = xtc_db_query("SELECT * FROM " . TABLE_MODULE_NEWSLETTER . " WHERE newsletter_id='" . (int) $_GET['ID'] . "';");
$newsletters_data = xtc_db_fetch_array($newsletters_query);

echo TEXT_TITLE . $newsletters_data['title'] . '<br>';

$customers_status = xtc_get_customers_statuses();
for ($i = 0, $n = sizeof($customers_status); $i < $n; $i++) {

$newsletters_data['bc'] = str_replace($customers_status[$i]['id'], $customers_status[$i]['text'], $newsletters_data['bc']);
}

echo TEXT_TO . $newsletters_data['bc'] . '<br>';
echo TEXT_CC . $newsletters_data['cc'] . '<br><br>';
echo '<b>' . PERSONAL_NEWSLETTER . '</b>' . '<br>';
if ($newsletters_data['personalize'] == 'yes') {
echo 'personalisiert: <b>Ja</b><br>';
if ($newsletters_data['greeting'] == '0') {
echo ANREDE_HOEFLICH;
} else {
echo ANREDE_PERSONAL;
}
} else {
echo ANREDE_PERSONALISIERT;
}
if ($newsletters_data['gift'] == 'yes') {
echo NEWSLETTER_GIFT_JA;
if ($newsletters_data['ammount'] > '0' && $newsletters_data['gift'] == 'yes') {
echo NEWSLETTER_GIFT_WERT . $newsletters_data['ammount'] . ' EUR' . '<br>';
} else {
echo NEWSLETTER_GIFT_WERT . '0<br><br>';
}
} else {
echo NEWSLETTER_GIFT_NEIN;
}
if ($newsletters_data['product_list'] != '0') {
$actual_list_query = xtc_db_query("SELECT list_name FROM " . TABLE_NEWSLETTER_PRODUCT_LIST . " WHERE id = '" . $newsletters_data['product_list'] . "' ORDER BY list_name;");
$actual_list = xtc_db_fetch_array($actual_list_query);
echo NEWSLETTER_ARTICLE . $actual_list['list_name'] . '<br><br>';
}

echo '<table style="border-color: #cccccc; border: 1px solid;" width="100%"><tr><td>' . $newsletters_data['body'] . '</td></tr></table>';
?>
</td></tr>
<?php
}
?>

<?php
}
}
?>
</table>
<br>
<?php
$newsletters_query = xtc_db_query("SELECT newsletter_id, date,title FROM " . TABLE_MODULE_NEWSLETTER . " WHERE status='1';");
$news_data = array();
while ($newsletters_data = xtc_db_fetch_array($newsletters_query)) {

$news_data[] = array('id' => $newsletters_data['newsletter_id'],
'date' => $newsletters_data['date'],
'title' => $newsletters_data['title']);
}
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" width="80%" ><?php echo TITLE_SEND; ?></td>
<td class="dataTableHeadingContent"><?php echo TITLE_ACTION; ?></td>
</tr>
<?php
for ($i = 0, $n = sizeof($news_data); $i < $n; $i++) {
if ($news_data[$i]['id'] != '') {
?>
<tr>
<td class="dataTableContent" valign="middle" align="left"><?php echo $news_data[$i]['date'] . '    '; ?><b><?php echo $news_data[$i]['title']; ?></b></td>
<td class="dataTableContent" align="left">

<a href="<?php echo xtc_href_link(FILENAME_MODULE_NEWSLETTER, 'action=delete&ID=' . $news_data[$i]['id']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
<?php
echo xtc_image(DIR_WS_ICONS . 'delete.gif', 'Delete', '', '', 'style="cursor:pointer" onClick="return confirm(\'' . DELETE_ENTRY . '\')"') . '  ' . TEXT_DELETE . '</a>&nbsp;&nbsp;';
?>
<a href="<?php echo xtc_href_link(FILENAME_MODULE_NEWSLETTER, 'action=edit&ID=' . $news_data[$i]['id']); ?>">
<?php echo xtc_image(DIR_WS_ICONS . 'icon_edit.gif', 'Edit', '', '') . '  ' . TEXT_EDIT . '</a>'; ?>

</td>
</tr>
<?php
}
}
?>
</table>

<?php
break;       // end default page

case 'edit':

$newsletters_query = xtc_db_query("SELECT * FROM " . TABLE_MODULE_NEWSLETTER . " WHERE newsletter_id='" . (int) $_GET['ID'] . "'");
$newsletters_data = xtc_db_fetch_array($newsletters_query);

$product_list_array = array(array('id' => '', 'text' => TEXT_NONE));
$product_list_query = xtc_db_query("select id, list_name from " . TABLE_NEWSLETTER_PRODUCT_LIST . " order by list_name");
while ($product_list = xtc_db_fetch_array($product_list_query)) {
$product_list_array[] = array('id' => $product_list['id'], 'text' => $product_list['list_name']);
}

case 'safe':
case 'new':  // action for NEW newsletter!

$product_list_array = array(array('id' => '', 'text' => TEXT_NONE));
$product_list_query = xtc_db_query("select id, list_name from " . TABLE_NEWSLETTER_PRODUCT_LIST . " order by list_name");
while ($product_list = xtc_db_fetch_array($product_list_query)) {
$product_list_array[] = array('id' => $product_list['id'], 'text' => $product_list['list_name']);
}

$customers_status = xtc_get_customers_statuses();


echo xtc_draw_form('edit_newsletter', FILENAME_MODULE_NEWSLETTER, 'action=save', 'post', 'enctype="multipart/form-data" onsubmit="return checkIt(this);"') . xtc_draw_hidden_field('ID', $_GET['ID']);
?>

<br><br>
<table class="main" width="100%" border="0">
</tr>
<tr>
<td width="10%"><?php echo TEXT_TITLE; ?></td>
<td width="90%"><?php echo xtc_draw_input_field('title', $newsletters_data['title'], 'size=100'); ?></td>
</tr>
<tr>
<td width="10%"><?php echo TEXT_TO; ?></td>
<td width="90%"><?php
for ($i = 0, $n = sizeof($customers_status); $i < $n; $i++) {

$group_query = xtc_db_query("SELECT count(*) as count FROM " . TABLE_NEWSLETTER_RECIPIENTS . " WHERE mail_status='1' AND customers_status='" . $customers_status[$i]['id'] . "';");
$group_data = xtc_db_fetch_array($group_query);

$group_query = xtc_db_query("SELECT count(*) as count FROM " . TABLE_CUSTOMERS . " WHERE customers_status='" . $customers_status[$i]['id'] . "';");
$group_data_all = xtc_db_fetch_array($group_query);

$bc_array = explode(',', $newsletters_data['bc']);

echo xtc_draw_checkbox_field('status[' . $i . ']', 'yes', in_array($customers_status[$i]['id'], $bc_array)) . ' ' . $customers_status[$i]['text'] . '  <i>(<b>' . $group_data['count'] . '</b>' . TEXT_USERS . $group_data_all['count'] . TEXT_CUSTOMERS . '<br>';
}
echo xtc_draw_checkbox_field('status_all', 'yes', in_array('all', $bc_array)) . ' <b>' . TEXT_NEWSLETTER_ONLY . '</b>';
?></td>
</tr>
<tr>
<td width="10%"><?php echo TEXT_CC; ?></td>
<td width="90%"><?php echo xtc_draw_input_field('cc', $newsletters_data['cc'], 'size=100'); ?></td>
</tr>
<?php $greeting_array = array(array('id' => 0, 'text' => NEWSLETTER_SELECT_HOEFLICH), array('id' => 1, 'text' => NEWSLETTER_SELECT_PERSONAL)); ?>

<tr>
<td width="10%">&nbsp;</td>
<td width="90%">
<table width="100%" class="main" style="border:solid 1px #cccccc; background-color:#f1f1f1;">
<tr>		    
<td colspan="2"><b><?php echo PERSONAL_NEWLETTER; ?></b></td>
</tr>	
<tr>		    
<td align="left" width="180"><?php echo PERSONALITY_NEWLETTER; ?></b></td>
<td align="left"><?php echo xtc_draw_checkbox_field('newsletter_personalize', 'yes', $newsletters_data['personalize']); ?></td>
</tr>
<tr>		    
<td align="left" width="180">&nbsp;</td>
<td align="left"><i><?php echo PERSONALITY_NEWLETTER_INFO; ?></i></td>
</tr>
<tr>		    
<td colspan="2"><b><?php echo OPTIONS; ?></b></td>
</tr>	
<tr>		    
<td align="left" width="180"><?php echo ANREDE; ?></td>
<td align="left"><?php echo xtc_draw_pull_down_menu('newsletter_greeting', $greeting_array, $newsletters_data['greeting']); ?></td>
</tr>
<tr>
<td align="left" width="180">&nbsp;</td>
<td align="left"><i><?php echo ANREDE_HOEFLICH_INFO; ?></i></td>
</tr>
<tr>
<td align="left" width="180">&nbsp;</td>
<td align="left"><i><?php echo ANREDE_PERSONAL_INFO; ?></i></td>
</tr>
<tr>		    
<td colspan="2"><b><?php echo NEWSLETTER_GIFT; ?></b></td>
</tr>
<tr>		    
<td align="left" width="180"><?php echo NEWSLETTER_GIFT_INSERT; ?></td>
<td align="left"><?php echo xtc_draw_checkbox_field('newsletter_gift', 'yes', $newsletters_data['gift']) . NEWSLETTER_GIFT_INSERT_INFO; ?></td>
</tr>
<tr>		    
<td align="left" width="180"><?php echo NEWSLETTER_GIFT_WERT; ?></td>
<td align="left"><?php echo xtc_draw_input_field('newsletter_gift_ammount', $newsletters_data['ammount'], 'size=5') . NEWSLETTER_GIFT_WERT_INFO; ?></td>
</tr>
<tr>		    
<td colspan="2">&nbsp;</td>
</tr>
<tr>		    
<td colspan="2"><b><?php echo NEWSLETTER_ARTICLE_INSERT; ?></b></td>
</tr>
<?php
if ($newsletters_data['product_list'] != '0') {
$actual_list_query = xtc_db_query("SELECT list_name from " . TABLE_NEWSLETTER_PRODUCT_LIST . " WHERE id = '" . $newsletters_data['product_list'] . "' ORDER BY list_name;");
$actual_list = xtc_db_fetch_array($actual_list_query);
?>
<tr>		    
<td align="left" width="180"><?php echo NEWSLETTER_ARTICLE_ACT; ?></td>
<td align="left"><?php echo $actual_list['list_name']; ?></td>
</tr>
<tr>		    
<td align="left" width="180"><?php echo NEWSLETTER_ARTICLE_NEW; ?></td>
<td align="left"><?php echo xtc_draw_pull_down_menu('newsletter_product_list', $product_list_array, $product_list_array); ?></td>
</tr>
<?php } else { ?>
<tr>		    
<td align="left" width="180"><?php echo NEWSLETTER_ARTICLE; ?></td>
<td align="left"><?php echo xtc_draw_pull_down_menu('newsletter_product_list', $product_list_array, $product_list_array); ?></td>
</tr>
<?php } ?>
</table>	  
</td>      
</tr>
<tr>
<td width="10%" valign="top"><?php echo TEXT_BODY; ?></td>
<td width="90%">
<script src="includes/editor/ckeditor/ckeditor.js" type="text/javascript"></script>
<?php
if (file_exists('includes/editor/ckfinder/ckfinder.js')) {
echo '<script src="includes/editor/ckfinder/ckfinder.js" type="text/javascript"></script>';
}
?>
<?php echo xtc_draw_textarea_field('newsletter_body', 'soft', '100', '35', stripslashes($newsletters_data['body']), 'class="ckeditor" name="editor1"'); ?>
<?php
if (file_exists('includes/editor/ckfinder/ckfinder.js')) {
?>	
<script type="text/javascript">
var newCKEdit = CKEDITOR.replace('<?php echo 'newsletter_body' ?>');
CKFinder.setupCKEditor(newCKEdit, 'includes/editor/ckfinder/');
</script>
<?php
}
?>
</td>
</tr>
</table>
<a class="button" href="<?php echo xtc_href_link(FILENAME_MODULE_NEWSLETTER); ?>"><?php echo BUTTON_BACK; ?></a>
<?php echo '<input type="submit" class="button" value="' . BUTTON_SAVE . '"/>'; ?>
</form>
<?php
break;
} // end switch
?>
</td>

</tr>
</table>
</td>
</tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');

