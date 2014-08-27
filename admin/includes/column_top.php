<?php
/* -----------------------------------------------------------------
 * 	$Id: column_top.php 1144 2014-07-10 09:31:57Z akausch $
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

$p = explode('.', $current_page);
$p = $p[0];
?>
<nav id="nav_top_nav" class="navbar navbar-inverse" role="navigation">
	<div id="nav_top" class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#usernav">
        <span class="sr-only">Navigation ein-/ausblenden</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
	  <a class="navbar-brand" href="<?php echo xtc_href_link('start.php', ''); ?>"><i class="glyphicon glyphicon-home"></i></a>
    </div>
		<div class="navbar-collapse collapse" id="usernav">
			<ul class="nav navbar-nav">
				<!-- Produkte -->
				<li class="dropdown menu">
					<?php
						$admin_sql = xtc_db_fetch_array(xtc_db_query("SELECT categories FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND categories = '1';"));
						if ($admin_sql['categories'] == '1') {
							$plink = 'href="'.xtc_href_link(FILENAME_CATEGORIES).'"';
						} else {
							$plink = '';
						}
					?>
					<a <?php echo $plink; ?> class="dropdown-toggle"><span class="glyphicon glyphicon-folder-open"></span><?php echo PRODUCTS; ?> <span class="caret"></span></a>			
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' )
															WHERE 
																an.subsite = 'products' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort, an.name");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
						if($navi[$navi['name']] != '0'){
							if ($navi['gid'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['gid'] != '') {
								echo '<li ' . ($_GET['gID'] == $navi['gid'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'gID=' . $navi['gid']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
							}
						}
						?>
					</ul>
				</li>
				<!-- Kunden -->
				<li class="dropdown menu">
					<?php
					$order_count = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(*) as count FROM " . TABLE_ORDERS . " WHERE orders_status = '1'"));
					if ($order_count['count'] > 0) {
						$counter = '<span class="label label-danger">' . $order_count['count'] . '</span>';
					}
					?>
					<?php
						$admin_sql = xtc_db_fetch_array(xtc_db_query("SELECT orders FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND categories = '1';"));
						if ($admin_sql['orders'] == '1') {
							$olink = 'href="'.xtc_href_link('orders.php').'"';
						} else {
							$olink = '';
						}
					?>
					<a <?php echo $olink; ?> class="dropdown-toggle"><span class="glyphicon glyphicon-user"></span><?php echo CUSTOMERS . $counter; ?><span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' )
															WHERE 
																subsite = 'customers' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
						if($navi[$navi['name']] != '0'){
							if ($navi['gid'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['gid'] != '') {
								echo '<li ' . ($_GET['gID'] == $navi['gid'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'gID=' . $navi['gid']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
							}
						}
						?>
					</ul>

				</li>
				<!-- Module -->
				<li class="dropdown menu">
					<?php
						$admin_sql = xtc_db_fetch_array(xtc_db_query("SELECT modules FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND categories = '1';"));
						if ($admin_sql['modules'] == '1') {
							$mlink = 'href="'.xtc_href_link(FILENAME_MODULES, 'set=payment').'"';
						} else {
							$mlink = '';
						}
					?>
					<a <?php echo $mlink; ?> class="dropdown-toggle"><span class="glyphicon glyphicon-tasks"></span><?php echo MODULES; ?><span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' )
															WHERE 
																subsite = 'modules' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
						if($navi[$navi['name']] != '0'){
							if ($navi['nav_set'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['nav_set'] != '') {
								echo '<li ' . ($_GET['set'] == $navi['nav_set'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'set=' . $navi['nav_set']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
							}
						}
						?>
					</ul>
				</li>			
				<!-- Gutscheine -->
				<?php if (ACTIVATE_GIFT_SYSTEM == 'true') { ?>
					<li class="dropdown menu">
					<?php
						$admin_sql = xtc_db_fetch_array(xtc_db_query("SELECT coupon_admin FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND categories = '1';"));
						if ($admin_sql['coupon_admin'] == '1') {
							$glink = 'href="'.xtc_href_link(FILENAME_COUPON_ADMIN).'"';
						} else {
							$glink = '';
						}
					?>
						<a <?php echo $glink; ?> class="dropdown-toggle"><span class="glyphicon glyphicon-gift"></span><?php echo GIFT; ?><span class="caret"></span></a>
						<ul class="dropdown-menu">
							<?php
							$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' )
															WHERE 
																subsite = 'gift' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
							while ($navi = xtc_db_fetch_array($navi_products_sql)) {
							if($navi[$navi['name']] != '0'){
								if ($navi['nav_set'] == '') {
									echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
								}
								}
							}
							?>
						</ul>
					</li>
	<?php } ?>
				<!-- Statistik -->
				<li class="dropdown menu">
					<?php
					$who_count = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(*) as count FROM " . TABLE_WHOS_ONLINE));
					$counters = '<span class="label label-success">' . $who_count['count'] . '</span>';
					?>
					<?php
						$admin_sql = xtc_db_fetch_array(xtc_db_query("SELECT whos_online FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND categories = '1';"));
						if ($admin_sql['whos_online'] == '1') {
							$slink = 'href="'.xtc_href_link('whos_online.php').'"';
						} else {
							$slink = '';
						}
					?>
					<a <?php echo $slink; ?> class="dropdown-toggle"><span class="glyphicon glyphicon-stats"></span><?php echo HEADER_TITLE_STATISTICS . $counters; ?><span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' )
															WHERE 
																subsite = 'statistics' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
						if($navi[$navi['name']] != '0'){
							if ($navi['nav_set'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
							}
						}
						?>
					</ul>
				</li>
				<!-- Tools -->
				<li class="dropdown menu">
					<?php
						$admin_sql = xtc_db_fetch_array(xtc_db_query("SELECT content_manager FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND categories = '1';"));
						if ($admin_sql['content_manager'] == '1') {
							$tlink = 'href="'.xtc_href_link(FILENAME_CONTENT_MANAGER).'"';
						} else {
							$tlink = '';
						}
					?>
					<a <?php echo $tlink; ?> class="dropdown-toggle"><span class="glyphicon glyphicon-cog"></span><?php echo TOOLS; ?><span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' )
															WHERE 
																subsite = 'tools' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
						if($navi[$navi['name']] != '0'){
							if ($navi['nav_set'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
							}
						}
						?>
					</ul>
				</li>
				<!-- SEO Config -->
				<li class="dropdown menu">
					<?php
						$admin_sql = xtc_db_fetch_array(xtc_db_query("SELECT configuration FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND categories = '1';"));
						if ($admin_sql['configuration'] == '1') {
							$cslink = 'href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=155').'"';
						} else {
							$cslink = '';
						}
					?>
					<a <?php echo $cslink; ?> class="dropdown-toggle"><span class="glyphicon glyphicon-wrench"></span>Einstellungen<span class="caret"></span></a>			
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' )
															WHERE 
																subsite = 'seo_config' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
						if($navi[$navi['name']] != '0'){
							if ($navi['gid'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['gid'] != '') {
								echo '<li ' . ($_GET['gID'] == $navi['gid'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'gID=' . $navi['gid']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
							}
						}
						?>
					</ul>
				</li>
				<!-- Country Config -->
				<li class="dropdown menu">
					<?php
						$admin_sql = xtc_db_fetch_array(xtc_db_query("SELECT languages FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND categories = '1';"));
						if ($admin_sql['languages'] == '1') {
							$llink = 'href="'.xtc_href_link(FILENAME_LANGUAGES).'"';
						} else {
							$llink = '';
						}
					?>
					<a <?php echo $llink; ?> class="dropdown-toggle"><span class="glyphicon glyphicon-map-marker"></span><?php echo COUNRTY; ?><span class="caret"></span></a>			
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' )
															WHERE 
																subsite = 'zones' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
						if($navi[$navi['name']] != '0'){
							if ($navi['gid'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['gid'] != '') {
								echo '<li ' . ($_GET['gID'] == $navi['gid'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'gID=' . $navi['gid']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
							}
						}
						?>
					</ul>
				</li>
				<!-- Config -->
				<li class="dropdown menu">
					<?php
						$admin_sql = xtc_db_fetch_array(xtc_db_query("SELECT configuration FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND categories = '1';"));
						if ($admin_sql['configuration'] == '1') {
							$cclink = 'href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=1').'"';
						} else {
							$cclink = '';
						}
					?>
					<a <?php echo $cclink; ?> class="dropdown-toggle"><span class="glyphicon glyphicon-wrench"></span><?php echo CONFIG; ?><span class="caret"></span></a>			
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' )
															WHERE 
																subsite = 'config' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
						if($navi[$navi['name']] != '0'){
							if ($navi['gid'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['gid'] != '') {
								echo '<li ' . ($_GET['gID'] == $navi['gid'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'gID=' . $navi['gid']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
							}
						}
						?>
					</ul>
				</li>
				<!-- Shopgate -->
				 <?php include_once DIR_FS_DOCUMENT_ROOT.'includes/external/shopgate/base/admin/includes/column_left.php'; ?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?php
				$languages = xtc_get_languages_head();
				for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
					echo '<li><a href="' . basename($_SERVER['SCRIPT_NAME']) . '?language=' . $languages[$i]['code'] . '">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '</a></li>';
				}
				?>
			</ul>
		</div>
	</div>
</nav>