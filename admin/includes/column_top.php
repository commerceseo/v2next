<?php
/* -----------------------------------------------------------------
 * 	$Id: column_top.php 1012 2014-05-08 13:02:25Z akausch $
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
					<a href="<?php echo xtc_href_link(FILENAME_CATEGORIES); ?>" class="dropdown-toggle"><?php echo PRODUCTS ?> <b class="caret"></b></a>			
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' AND an.name = '1')
															WHERE 
																subsite = 'products' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
							if ($navi['gid'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['gid'] != '') {
								echo '<li ' . ($_GET['gID'] == $navi['gid'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'gID=' . $navi['gid']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
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
						$counter = '&nbsp;<b class="numberRed">' . $order_count['count'] . '</b>';
					}
					?>
					<a href="<?php echo xtc_href_link('orders.php'); ?>" class="dropdown-toggle"><?php echo CUSTOMERS . $counter ?> <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' AND an.name = '1')
															WHERE 
																subsite = 'customers' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
							if ($navi['gid'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['gid'] != '') {
								echo '<li ' . ($_GET['gID'] == $navi['gid'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'gID=' . $navi['gid']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
						}
						?>
					</ul>

				</li>
				<!-- Module -->
				<li class="dropdown menu">
					<a href="<?php echo xtc_href_link(FILENAME_MODULES, 'set=payment'); ?>" class="dropdown-toggle"> <?php echo MODULES ?> <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' AND an.name = '1')
															WHERE 
																subsite = 'modules' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
							if ($navi['nav_set'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['nav_set'] != '') {
								echo '<li ' . ($_GET['set'] == $navi['nav_set'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'set=' . $navi['nav_set']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
						}
						?>
					</ul>
				</li>			
				<!-- Gutscheine -->
	<?php if (ACTIVATE_GIFT_SYSTEM == 'true') { ?>
					<li class="dropdown menu">
						<a href="<?php echo xtc_href_link(FILENAME_COUPON_ADMIN); ?>" class="dropdown-toggle"><?php echo GIFT; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<?php
							$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' AND an.name = '1')
															WHERE 
																subsite = 'gift' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
							while ($navi = xtc_db_fetch_array($navi_products_sql)) {
								if ($navi['nav_set'] == '') {
									echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
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
					$counters = '&nbsp;<b class="numberGreen">' . $who_count['count'] . '</b>';
					?>
					<a href="<?php echo xtc_href_link('whos_online.php'); ?>" class="dropdown-toggle"><?php echo HEADER_TITLE_STATISTICS . $counters ?> <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' AND an.name = '1')
															WHERE 
																subsite = 'statistics' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
							if ($navi['nav_set'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
						}
						?>
					</ul>
				</li>
				<!-- Tools -->
				<li class="dropdown menu">
					<a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER); ?>" class="dropdown-toggle"><?php echo TOOLS ?> <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' AND an.name = '1')
															WHERE 
																subsite = 'tools' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
							if ($navi['nav_set'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
						}
						?>
					</ul>
				</li>
				<!-- SEO Config -->
				<li class="dropdown menu">
					<a href="<?php echo xtc_href_link(FILENAME_CONFIGURATION, 'gID=155'); ?>" class="dropdown-toggle">Einstellungen <b class="caret"></b></a>			
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' AND an.name = '1')
															WHERE 
																subsite = 'seo_config' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
							if ($navi['gid'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['gid'] != '') {
								echo '<li ' . ($_GET['gID'] == $navi['gid'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'gID=' . $navi['gid']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
						}
						?>
					</ul>
				</li>
				<!-- Country Config -->
				<li class="dropdown menu">
					<a href="<?php echo xtc_href_link(FILENAME_LANGUAGES); ?>" class="dropdown-toggle"><?php echo COUNRTY ?> <b class="caret"></b></a>			
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' AND an.name = '1')
															WHERE 
																subsite = 'zones' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
							if ($navi['gid'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['gid'] != '') {
								echo '<li ' . ($_GET['gID'] == $navi['gid'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'gID=' . $navi['gid']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
						}
						?>
					</ul>
				</li>
				<!-- Config -->
				<li class="dropdown menu">
					<a href="<?php echo xtc_href_link(FILENAME_CONFIGURATION, 'gID=1'); ?>" class="dropdown-toggle"><?php echo CONFIG ?> <b class="caret"></b></a>			
					<ul class="dropdown-menu">
						<?php
						$navi_products_sql = xtc_db_query("SELECT 
																* 
															FROM 
																" . TABLE_ADMIN_NAVIGATION . " AS an
															LEFT JOIN 
																" . TABLE_ADMIN_ACCESS . " AS ac ON(customers_id = '" . (int) $_SESSION['customer_id'] . "' AND an.name = '1')
															WHERE 
																subsite = 'config' 
															AND 
																an.languages_id = '" . (int) $_SESSION['languages_id'] . "' 
															ORDER BY an.sort");
						while ($navi = xtc_db_fetch_array($navi_products_sql)) {
							if ($navi['gid'] == '') {
								echo '<li ' . ($p == $navi['name'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							} elseif ($navi['gid'] != '') {
								echo '<li ' . ($_GET['gID'] == $navi['gid'] ? 'class="active"' : '') . '><a href="' . xtc_href_link($navi['filename'], 'gID=' . $navi['gid']) . '" class="menuBoxContentLink">' . $navi['title'] . '</a></li>';
							}
						}
						?>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
			<li class="dropdown menu">
			<a href="#" class="dropdown-toggle">Tools <b class="caret"></b></a>
			<ul class="dropdown-menu navright">
			<?php
			$languages = xtc_get_languages_head();
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
				echo '<li><a href="' . basename($_SERVER['SCRIPT_NAME']) . '?language=' . $languages[$i]['code'] . '">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image'], $languages[$i]['name']) . '</a></li>';
			}
			?>
    <li class="divider"></li>
        <li><b>Version: <?php echo $version['version']; ?></b></li>
		<li class="divider"></li>
        <li><a href="http://www.commerce-seo.de/support/" target="_blank">Support</a></li>
        <li><a href="<?php echo xtc_href_link('cseo_center_security.php', 'subsite=tools'); ?>">Security-Center</a></li>
<li><a href="../index.php" target="_blank">Shop</a></li>
<li><a href="<?php echo xtc_href_link(FILENAME_LOGOUT); ?>">Logout</a></li>
<li><a href="<?php echo xtc_href_link('delete_cache.php', 'subsite=tools'); ?>">Cache leeren</a></li>
<li><a href="<?php echo xtc_href_link('module_system.php', 'subsite=modules&set=&module=commerce_seo_url'); ?>">SEO-URL</a></li>
			</ul>
			</li>
			</ul>
		</div>
	</div>
</nav>