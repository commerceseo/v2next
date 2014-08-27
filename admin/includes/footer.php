<?php
/* -----------------------------------------------------------------
 * 	$Id: footer.php 1127 2014-06-30 11:54:44Z akausch $
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
				<li><a href="../index.php" title="Front" target="_blank"><span class="glyphicon glyphicon-home"></span><span class="hidden-sm hidden-md hidden-lg">Home</span></a></li>
				<li><a href="<?php echo xtc_href_link('blog.php'); ?>" title="Blog"><span class="glyphicon glyphicon-globe"></span><span class="hidden-sm hidden-md hidden-lg">Blog</span></a></li>
				<li><a href="<?php echo xtc_href_link('cseo_center_security.php'); ?>" title="Security"><span class="glyphicon glyphicon-lock"></span><span class="hidden-sm hidden-md hidden-lg">Security</span></a></li>
				<li><a href="<?php echo xtc_href_link('delete_cache.php'); ?>" title="Cache leeren"><span class="glyphicon glyphicon-trash"></span><span class="hidden-sm hidden-md hidden-lg">Cache leeren</span></a></li>
				<li><a href="<?php echo xtc_href_link('module_system.php', 'set=&module=commerce_seo_url'); ?>" title="SEO-URL"><span class="glyphicon glyphicon-link"></span><span class="hidden-sm hidden-md hidden-lg">SEO-URL</span></a></li>
				<li><a href="https://www.commerce-seo.de/support/" target="_blank" title="Support"><span class="glyphicon glyphicon-comment"></span><span class="hidden-sm hidden-md hidden-lg">Support</span></a></li>
				<li><a href="https://www.facebook.com/commerce.seo.v2" target="_blank" title="Like"><span class="glyphicon glyphicon-thumbs-up"></span><span class="hidden-sm hidden-md hidden-lg">Like</span></a></li>
				<li><a href="<?php echo xtc_href_link(FILENAME_CREDITS) ?>" title="Credits"><span class="glyphicon glyphicon-copyright-mark"></span><span class="hidden-sm hidden-md hidden-lg">Credits</span></a></li>
				<li><a href="<?php echo xtc_href_link(FILENAME_LOGOUT) ?>" title="Logout"><span class="glyphicon glyphicon-off"></span><span class="hidden-sm hidden-md hidden-lg">Logout</span></a></li>
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
