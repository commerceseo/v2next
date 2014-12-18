<?php
/* -----------------------------------------------------------------
 * 	$Id: footer.php 1157 2014-07-21 12:31:00Z akausch $
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
<br><br><br><br>
</div>
<nav class="navbar navbar-default navbar-fixed-bottom" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#footernav">
			<span class="sr-only">Navigation ein-/ausblenden</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		  </button>
		  <b class="navbar-brand hidden-sm hidden-md hidden-lg">Footer Menu</b>
		</div>
		<div class="navbar-collapse collapse" id="footernav">
			<ul class="nav navbar-nav">
				<li><a href="../index.php" title="Front" target="_blank"><i class="glyphicon glyphicon-home"></i> Shop</a></li>
				<li><a href="<?php echo xtc_href_link('blog.php'); ?>" title="Blog"><i class="glyphicon glyphicon-globe"></i> Blog</a></li>
				<li><a href="<?php echo xtc_href_link('cseo_center_security.php'); ?>" title="Security"><i class="glyphicon glyphicon-lock"></i> Security</a></li>
				<li><a href="<?php echo xtc_href_link('delete_cache.php'); ?>" title="Cache leeren"><i class="glyphicon glyphicon-trash"></i> Cache leeren</a></li>
				<li><a href="<?php echo xtc_href_link('module_system.php', 'set=&module=commerce_seo_url'); ?>" title="SEO-URL"><i class="glyphicon glyphicon-link"></i> SEO-URL</a></li>
				<li><a href="http://plussupport.commerce-seo.de/" target="_blank" title="Support"><i class="glyphicon glyphicon-comment"></i> Support</a></li>
				<li><a href="http://bugtracker.commerce-seo.de/" target="_blank" title="Bugtracker"><i class="glyphicon glyphicon-eye-open"></i> Bugtracker</a></li>
				<li><a href="<?php echo xtc_href_link(FILENAME_CREDITS) ?>" title="Credits"><i class="glyphicon glyphicon-copyright-mark"></i> Credits</a></li>
				<li><a href="<?php echo xtc_href_link(FILENAME_LOGOUT) ?>" title="Logout"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li>
					<form class="navbar-form navbar-left" role="search" name="search" id="search" action="global_search.php" method="POST">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Suchen" name="search" value="" />
							<button type="submit" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-search"></span></button>
						</div>
					</form>
				</li>
			</ul>
		</div>
	</div>
</nav>



<?php
/*
  echo ('<b>Session Debug:</b><br />');
  echo "<pre>";
  print_r($_SESSION);
  echo "</pre>";
  echo xtc_session_id();
 */
echo '<script src="'.DIR_WS_CATALOG.'shopscripte/head.min.js"></script>';
echo '<script>
	head.js(
	';
echo '"'.DIR_WS_CATALOG.'shopscripte/js/bootstrap.min.js",';
// echo '"includes/javascript/growl/jquery.gritter.min.js",';
echo '"templates/' . CURRENT_ADMIN_TEMPLATE . '/javascript/tinycolor-0.9.15.min.js",';
echo '"templates/' . CURRENT_ADMIN_TEMPLATE . '/javascript/pick-a-color-1.2.2.min.js",';
echo '"includes/javascript/cseocustom.js",';
echo '"includes/javascript/general.js"';

echo ');
</script>';
if (file_exists(DIR_WS_INCLUDES . 'addons/footer_addon.php')) {
	include (DIR_WS_INCLUDES .'addons/footer_addon.php');
}
?>
<script>
head.ready(function() {
$('.dropdown.menu').hover(function() { $(this).addClass('open');}, function() {$(this).removeClass('open');});
});
</script>
</body>
</html>
