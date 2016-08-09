<?php
/* -----------------------------------------------------------------
 * 	$Id: customers_aquise.php 420 2013-06-19 18:04:39Z akausch $
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

require ('includes/application_top.php');

require_once (DIR_FS_INC . 'xtc_validate_vatid_status.inc.php');
require_once (DIR_FS_INC . 'xtc_get_geo_zone_code.inc.php');
require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC . 'xtc_js_lang.php');

$customers_statuses_array = xtc_get_customers_statuses();

if ($_GET['page'] != '')
    $page = '&amp;page=' . $_GET['page'];

if ($_GET['sortierung'] != '')
    $sort_get = '&amp;sortierung=' . $_GET['sortierung'];

if (($_GET['action'] == 'delete') && ($_GET['del_id'] != '')) {
    xtc_db_query("delete from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int) $_GET['del_id'] . "'");
    xtc_db_query("delete from " . TABLE_CUSTOMERS . " where customers_id = '" . (int) $_GET['del_id'] . "'");
    xtc_db_query("delete from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . (int) $_GET['del_id'] . "'");
    xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int) $_GET['del_id'] . "'");
    xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int) $_GET['del_id'] . "'");
    xtc_db_query("delete from " . TABLE_PRODUCTS_NOTIFICATIONS . " where customers_id = '" . (int) $_GET['del_id'] . "'");
    xtc_db_query("delete from " . TABLE_WHOS_ONLINE . " where customer_id = '" . (int) $_GET['del_id'] . "'");
    xtc_db_query("delete from " . TABLE_CUSTOMERS_STATUS_HISTORY . " where customers_id = '" . (int) $_GET['del_id'] . "'");
    xtc_db_query("delete from " . TABLE_CUSTOMERS_IP . " where customers_id = '" . (int) $_GET['del_id'] . "'");
    xtc_db_query("delete from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . (int) $_GET['del_id'] . "'");
}

require(DIR_WS_INCLUDES . 'header.php');
?>
<script type="text/javascript" src="includes/javascript/accordion.js"></script>
<script type="text/javascript">
    <!--
                ddaccordion.init({
        headerclass: "customers_col", //Shared CSS class name of headers group that are expandable
        contentclass: "customers_items", //Shared CSS class name of contents group
        revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click" or "mouseover
        mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
        collapseprev: true, //Collapse previous content (so only one open at any time)? true/false 
        defaultexpanded: [], //index of content(s) open by default [index1, index2, etc]. [] denotes no content
        onemustopen: false, //Specify whether at least one header should be open always (so never all headers closed)
        animatedefault: true, //Should contents open by default be animated into view?
        persiststate: true, //persist state of opened contents within browser session?
        toggleclass: ["", ""], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
        togglehtml: ["", "", ""], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
        animatespeed: "slow", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
        oninit: function(headers, expandedindices) {
        },
        onopenclose: function(header, index, state, isuseractivated) {
        }
    });
    //-->
</script>
<script type="text/javascript">
<!--
    function createXMLHttpRequest() {
        var ua;
        if (window.XMLHttpRequest) {
            try {
                ua = new XMLHttpRequest();
            } catch (e) {
                ua = false;
            }
        } else if (window.ActiveXObject) {
            try {
                ua = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                ua = false;
            }
        }
        return ua;
    }

    var req = createXMLHttpRequest();

    function sendRequest(action, daten, was, id, ziel) {
        targetid = ziel;

        if (action == 'speichern') {
            $.jGrowl("<img src='images/tick.gif' align='absmiddle' /> Der Eintrag wurde gespeichert!", {header: 'Important'});
        }
        req.open('get', '<?php echo HTTP_SERVER . DIR_WS_ADMIN; ?>customers_aquise_request.php?action=' + action + '&daten=' + daten + '&was=' + was + '&id=' + id);
        req.onreadystatechange = handleResponse;
        req.send(null);
    }

    function handleResponse() {
        if (req.readyState == 4)
            document.getElementById(targetid).innerHTML = req.responseText;
        else
            document.getElementById(targetid).innerHTML = '<div id="ajax_loader"><img src="images/loading.gif" alt="" /><strong> ... einen Moment</strong></div>';
    }

//-->
</script>

<table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td class="pageHeading">Kunden Aquise v2.1</td>
    </tr>
</table>

<script type="text/javascript">
    var isShow = false;
    $(document).ready(function() {
<?php if (($_GET['action']) && ($_GET['del_id'] > 1)) { ?>
            $.jGrowl("<img src='images/cross.gif' align='absmiddle' /> <?php echo xtc_js_lang('Der Eintrag wurde erfolgreich gel&ouml;scht!'); ?>", {header: 'Important'});
    <?php unset($_GET['action']);
    unset($_GET['del_id']);
} ?>

    });
</script>
<table width="100%">
    <tr>
        <td width="40%">
            Zum bearbeiten der entsprechenden Eintr&auml;ge, einfach anklicken... .</td>
        </td>
        <td width="60%" align="right">
            <table align="right">
                <tr>
                    <td><img src="images/0.gif" alt="" /> Admin</td>
                    <td><img src="images/2.gif" alt="" /> Neuer Kunde</td>
                    <td><img src="images/1.gif" alt="" /> Gast</td>
                    <td><img src="images/3.gif" alt="" /> H&auml;ndler</td>
                </tr>
                <tr>
                    <td colspan="4" align="left"><img src="images/icon_info.gif" alt="Info" title="mehr Infos" /> Klick f&uuml;r Details</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table border="0" class="aquise" cellpadding="4" cellspacing="0" width="100%">
    <tr>
    <form action="<?php echo HTTP_SERVER . DIR_WS_ADMIN; ?>customers_aquise.php<?php echo $page; ?>" method="get">
        <td class="dataTableHeadingContent nr" width="16">&nbsp;</td>
        <td class="dataTableHeadingContent id" width="40">
            ID
            <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?sortierung=c_asc' . $page; ?>">
                <img src="images/up.gif" alt="" title="" />
            </a>
            <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?sortierung=c_desc' . $page; ?>">
                <img src="images/down.gif" alt="" title="" />
            </a>
        </td>
        <td class="dataTableHeadingContent" width="16" class="ico">
            &nbsp;
        </td>
        <td class="dataTableHeadingContent vname">
            Vorname
            <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?sortierung=vn_asc' . $page; ?>">
                <img src="images/up.gif" alt="" title="" />
            </a>
            <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?sortierung=vn_desc' . $page; ?>">
                <img src="images/down.gif" alt="" title="" />
            </a>
        </td>
        <td class="dataTableHeadingContent nname">
            Nachname
            <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?sortierung=nn_asc' . $page; ?>">
                <img src="images/up.gif" alt="" title="" />
            </a>
            <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?sortierung=nn_desc' . $page; ?>">
                <img src="images/down.gif" alt="" title="" />
            </a>
        </td>
        <td class="dataTableHeadingContent mail">
            eMail
            <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?sortierung=mail_asc' . $page; ?>">
                <img src="images/up.gif" alt="" title="" />
            </a>
            <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?sortierung=mail_desc' . $page; ?>">
                <img src="images/down.gif" alt="" title="" />
            </a>
        </td>
        <td class="dataTableHeadingContent tel">
            Telefon
            <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?sortierung=tel_asc' . $page; ?>">
                <img src="images/up.gif" alt="" title="" />
            </a>
            <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?sortierung=tel_desc' . $page; ?>">
                <img src="images/down.gif" alt="" title="" />
            </a>
        </td>
        <td class="dataTableHeadingContent memo">Memo-Text</td>
        <td class="dataTableHeadingContent del">Löschen</td>
    </form>
</tr>
</table>
<?php
switch ($_GET['sortierung']) {
    case 'c_asc' : $sort = 'ORDER BY customers_id ASC';
        break;
    case 'c_desc' : $sort = 'ORDER BY customers_id DESC';
        break;
    case 'vn_asc' : $sort = 'ORDER BY customers_firstname ASC';
        break;
    case 'vn_desc' : $sort = 'ORDER BY customers_firstname DESC';
        break;
    case 'nn_asc' : $sort = 'ORDER BY customers_lastname ASC';
        break;
    case 'nn_desc' : $sort = 'ORDER BY customers_lastname DESC';
        break;
    case 'mail_asc' : $sort = 'ORDER BY customers_email_address ASC';
        break;
    case 'mail_desc' : $sort = 'ORDER BY customers_email_address DESC';
        break;
    case 'tel_asc' : $sort = 'ORDER BY customers_telephone ASC';
        break;
    case 'tel_desc' : $sort = 'ORDER BY customers_telephone DESC';
        break;

    default : $sort = 'ORDER BY customers_id ASC';
        break;
}

$customers_query_raw = "select customers_id, customers_firstname, customers_lastname, customers_email_address, customers_telephone,  customers_status from customers " . $sort . "";
$customers_split = new splitPageResults($_GET['page'], '50', $customers_query_raw, $customers_query_numrows);
$customers_query = xtc_db_query($customers_query_raw);
$td_bg = 0;
while ($while = xtc_db_fetch_array($customers_query)) {
    $td_bg++;

    $memo = xtc_db_fetch_array(xtc_db_query("SELECT memo_text FROM " . TABLE_CUSTOMERS_MEMO . " where customers_id='" . $while['customers_id'] . "'"));
    if ($memo['memo_text'] == '')
        $memo_text = '<img src="images/write.gif" title="Einen Memotext verfassen" alt="" />';
    else
        utf8_decode($memo_text = $memo['memo_text']);

    if ($while['customers_status'] != '0') {
        $del_link = '<a href="customers_aquise.php?action=delete&amp;del_id=' . $while['customers_id'] . $page . $sort_get . '" title="Kunde komplett aus der Datenbank l&ouml;schen?">
									<img src="images/cross.gif" alt="" title="ACHTUNG! Sie l&ouml;schen den Kunden mit all seinen Daten und Bestellungen!!" />
								</a>';
    } else {
        $del_link = '&nbsp;';
    }
    $status = '<img src="images/' . $while['customers_status'] . '.gif" alt="" />';
    if ($td_bg == 2) {
        $class = ' td_bg_hell';
        $table_class = 'table_hell';
        $td_bg = 0;
    } else {
        $class = ' td_bg_dunkel';
        $table_class = 'table_dunkel';
    }

    echo '<table border="0" class="aquise" cellpadding="4" cellspacing="0" width="100%"><tr>
						<td class="nr' . $class . ' customers_col" width="16">
							<img src="images/icon_info.gif" alt="" title="mehr Infos" />
						</td>
						<td id="id_' . $while['customers_id'] . '" class="id' . $class . '">
							' . $while['customers_id'] . '
						</td>
						<td class="ico' . $class . '">
							' . $status . '
						</td>
						<td id="customers_firstname_' . $while['customers_id'] . '" class="vname' . $class . '">
							<span onclick="sendRequest(\'input\',\'vorname\',\'customers_firstname\',\'' . $while['customers_id'] . '\',\'customers_firstname_' . $while['customers_id'] . '\');">
								' . $while['customers_firstname'] . '
							</span>
						</td>
						<td id="customers_lastname_' . $while['customers_id'] . '" class="nname' . $class . '">
							<span onclick="sendRequest(\'input\',\'nachname\',\'customers_lastname\',\'' . $while['customers_id'] . '\',\'customers_lastname_' . $while['customers_id'] . '\');">	
								' . $while['customers_lastname'] . '
							</span>
						</td>
						<td id="customers_email_address_' . $while['customers_id'] . '" class="mail' . $class . '">
							<span onclick="sendRequest(\'input\',\'customers_email_address\',\'customers_email_address\',\'' . $while['customers_id'] . '\',\'customers_email_address_' . $while['customers_id'] . '\');">
								' . $while['customers_email_address'] . '
							</span>&nbsp;&nbsp;<a href="mailto:' . $while['customers_email_address'] . '"><img src="images/mail.gif" titel="Dem Kunden direkt eine eMail schicken?" alt="" /></a>
						</td>
						<td id="customers_telephone_' . $while['customers_id'] . '" class="tel' . $class . '">
							<span onclick="sendRequest(\'input\',\'customers_telephone\',\'customers_telephone\',\'' . $while['customers_id'] . '\',\'customers_telephone_' . $while['customers_id'] . '\');">
								' . $while['customers_telephone'] . '
							</span>
						</td>
						<td id="memo_text_' . $while['customers_id'] . '" class="meno' . $class . '">
							<span onclick="sendRequest(\'input\',\'memo_text\',\'memo_text\',\'' . $while['customers_id'] . '\',\'memo_text_' . $while['customers_id'] . '\');">
								' . $memo_text . '
							</span>
						</td>
						<td class="del' . $class . '" align="right">
							' . $del_link . '
						</td>
					</tr></table>
				';
    $customers_zusatz_query = xtc_db_query("SELECT 
														c.customers_id,
														c.customers_cid, 
														c.customers_gender,  
														c.customers_dob,
														c.customers_status,
														c.customers_vat_id,
														c.payment_unallowed,
														c.shipping_unallowed,
														a.entry_company, 
														a.entry_street_address, 
														a.entry_suburb, 
														a.entry_postcode, 
														a.entry_city, 
														a.entry_state, 
														a.entry_zone_id, 
														a.entry_country_id, 
														c.customers_fax, 
														c.customers_default_address_id 
														FROM " . TABLE_CUSTOMERS . " c 
														LEFT JOIN " . TABLE_ADDRESS_BOOK . " a 
														on c.customers_default_address_id = a.address_book_id 
														where 
														a.customers_id = c.customers_id 
														and c.customers_id = '" . $while['customers_id'] . "'");
    $c = xtc_db_fetch_array($customers_zusatz_query);
    $land = xtc_db_fetch_array(xtc_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . $c['entry_country_id'] . "' LIMIT 1"));
    $stats = xtc_db_fetch_array(xtc_db_query("select customers_info_date_of_last_logon, customers_info_number_of_logons from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . $c['customers_id'] . "'"));
    if ($c['customers_vat_id'])
        $ust_id = $c['customers_vat_id'] . '<div style="font-size:8pt">(' . xtc_validate_vatid_status($c['customers_id']) . ')</div>';
    else
        $ust_id = ' -- ';
    if ($c['entry_company'])
        $firma = $c['entry_company'];
    else
        $firma = ' - ';
    if ($stats['customers_info_date_of_last_logon'])
        $login_date = date('d.m.Y h:m:s', strtotime($stats['customers_info_date_of_last_logon']));
    else
        $login_date = 'nie';
    if ($c['payment_unallowed'])
        $zahlart = $c['payment_unallowed'];
    else
        $zahlart = ' -- ';
    if ($c['shipping_unallowed'])
        $versand = $c['shipping_unallowed'];
    else
        $versand = ' -- ';
    if ($c['entry_state'] != '')
        $bundesland = $c['entry_state'];
    else
        $bundesland = ' -- ';

    echo '<div class="customers_items">	
					<table border="0" class="aquise ' . $table_class . '" cellpadding="4" cellspacing="0" width="100%">
						<tr>
							<td class="' . $class . '" width="110"><strong>' . ENTRY_DATE_OF_BIRTH . '</strong></td>
							<td class="' . $class . '" width="200"><strong>Adresse:</strong></td>
							<td class="' . $class . '" width="200"><strong>' . ENTRY_COMPANY . '</strong></td>
							<td class="' . $class . '" width="200"><strong>Sperren:</strong></td>
							<td class="' . $class . '"><strong>Infos:</strong></td>
							<td class="' . $class . '">&nbsp;</td>
						</tr>
						<tr>
							<td height="80" class="' . $class . ' dob" valign="top">
								<div id="customers_dob_' . $while['customers_id'] . '">
									<span onclick="sendRequest(\'input\',\'customers_dob\',\'customers_dob\',\'' . $while['customers_id'] . '\',\'customers_dob_' . $while['customers_id'] . '\');">
										' . xtc_date_short($c['customers_dob']) . '
									</span>
								</div><br /><br />
								<strong>Status:</strong>
								<div id="customers_status_' . $while['customers_id'] . '">
									<span onclick="sendRequest(\'input\',\'' . $c['customers_status'] . '\',\'customers_status\',\'' . $while['customers_id'] . '\',\'customers_status_' . $while['customers_id'] . '\');">
										' . $customers_statuses_array[$c['customers_status']]['text'] . '
									</span>
								</div>
							</td>
							<td class="' . $class . ' line adresse" valign="top">
								<table cellpadding="3" cellspacing="0" width="100%">
									<tr>
										<td colspan="2" width="1">
											<div id="entry_street_address_' . $while['customers_id'] . '">
												<span onclick="sendRequest(\'input\',\'entry_street_address\',\'entry_street_address\',\'' . $while['customers_id'] . '\',\'entry_street_address_' . $while['customers_id'] . '\');">
													' . $c['entry_street_address'] . '<br />
												</span>
											</div>
										</td>
									<tr>
									<tr>
										<td width="45">
											<div id="entry_postcode_' . $while['customers_id'] . '">
												<span onclick="sendRequest(\'input\',\'entry_postcode\',\'entry_postcode\',\'' . $while['customers_id'] . '\',\'entry_postcode_' . $while['customers_id'] . '\');">	
													' . $c['entry_postcode'] . '
												</span>
											</div>
										</td>
										<td>
											<div id="entry_city_' . $while['customers_id'] . '">
												<span onclick="sendRequest(\'input\',\'entry_city\',\'entry_city\',\'' . $while['customers_id'] . '\',\'entry_city_' . $while['customers_id'] . '\');">
													' . $c['entry_city'] . '
												</span>
											</div>
										</td>
									<tr>
									<tr>
										<td colspan="2">
											<div id="entry_zone_id_' . $while['customers_id'] . '"> 
												<span onclick="sendRequest(\'input\',\'' . $c['entry_country_id'] . '\',\'entry_zone_id\',\'' . $while['customers_id'] . '\',\'entry_zone_id_' . $while['customers_id'] . '\');">
													' . $bundesland . '
												</span>
											</div>
										</td>
									<tr>
									<tr>
										<td colspan="2">
											<div id="entry_country_id_' . $while['customers_id'] . '">
												<span onclick="sendRequest(\'input\',\'' . $c['entry_country_id'] . '\',\'entry_country_id\',\'' . $while['customers_id'] . '\',\'entry_country_id_' . $while['customers_id'] . '\');">	
													' . $land['countries_name'] . '
												</span>
											</div>
										</td>
									<tr>
								</table>	
							</td>
							<td class="' . $class . ' line firma" valign="top">
								<table cellpadding="3" cellspacing="0" width="100%">
									<tr>
										<td width="1"><em>' . ENTRY_COMPANY . '</em></td>
										<td>
											<div id="entry_company_' . $while['customers_id'] . '">
												<span onclick="sendRequest(\'input\',\'' . $c['entry_company'] . '\',\'entry_company\',\'' . $while['customers_id'] . '\',\'entry_company_' . $while['customers_id'] . '\');">	
													' . $firma . '
												</span>
											</div>
										</td>
									</tr>
									<tr>
										<td><em>' . ENTRY_VAT_ID . '</em></td>
										<td>
											<div id="customers_vat_id_' . $while['customers_id'] . '">
												<span onclick="sendRequest(\'input\',\'' . $c['customers_vat_id'] . '\',\'customers_vat_id\',\'' . $while['customers_id'] . '\',\'customers_vat_id_' . $while['customers_id'] . '\');">	
													' . $ust_id . '
												</span>
											</div>
										</td>
									</tr>
								</table>
							</td>
							<td class="' . $class . ' line sperren">
								<table cellpadding="3" cellspacing="0" width="100%">
									<tr>
										<td width="1"><em>Zahlart:</em></td>
										<td>
											<div id="payment_unallowed_' . $while['customers_id'] . '">
												<span onclick="sendRequest(\'input\',\'' . $c['payment_unallowed'] . '\',\'payment_unallowed\',\'' . $while['customers_id'] . '\',\'payment_unallowed_' . $while['customers_id'] . '\');">	
													' . $zahlart . '
												</span>
											</div>
										</td>
									</tr>
									<tr>
										<td><em>Versandart:</em></td>
										<td>
											<div id="shipping_unallowed_' . $while['customers_id'] . '">	
												<span onclick="sendRequest(\'input\',\'' . $c['shipping_unallowed'] . '\',\'shipping_unallowed\',\'' . $while['customers_id'] . '\',\'shipping_unallowed_' . $while['customers_id'] . '\');">
													' . $versand . '
												</span>
											</div>
										</td>
									</tr>
								</table>
							</td>
							<td class="' . $class . ' line infos">
								<table cellpadding="3" cellspacing="0" width="100%">
									<tr>
										<td colspan="2">
											<div id="customers_password_' . $while['customers_id'] . '">
												<span style="color:#ff0000" onclick="sendRequest(\'input\',\'customers_password\',\'customers_password\',\'' . $while['customers_id'] . '\',\'customers_password_' . $while['customers_id'] . '\');">
													neues Passwort vergeben
												</span>
											</div>
										</td>
									</tr>
									<tr>
										<td width="1"><em><nobr>letzter Login:</nobr></em></td>
										<td><em>' . $login_date . '</em></td>
									</tr>
									<tr>
										<td><em>eingeloggt:</em></td>
										<td><em>' . $stats['customers_info_number_of_logons'] . ' mal</em></td>
									</tr>
								</table>
							</td>
							<td class="' . $class . '">&nbsp;</td>
						</tr>
					</table>
				</div>';
}
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
        <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, '50', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
        <td class="smallText" align="right"><?php echo $customers_split->display_links($customers_query_numrows, '50', MAX_DISPLAY_PAGE_LINKS, $_GET['page'], xtc_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
