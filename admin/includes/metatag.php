<?php
/* -----------------------------------------------------------------
 * 	$Id: metatag.php 943 2014-04-08 13:26:37Z akausch $
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
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language_code']; ?>">
    <head>
        <meta charset="<?php echo $_SESSION['language_charset']; ?>" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo TITLE; ?></title>
		<?php 
		echo '<link rel="stylesheet" type="text/css" href="templates/' . CURRENT_ADMIN_TEMPLATE . '/css/bootstrap.css">';
		echo '<link rel="stylesheet" type="text/css" href="templates/' . CURRENT_ADMIN_TEMPLATE . '/css/bootstrap-theme.css">';
		echo '<link rel="stylesheet" type="text/css" href="templates/' . CURRENT_ADMIN_TEMPLATE . '/css/pick-a-color-1.2.2.min.css">';
		echo '<link rel="stylesheet" type="text/css" href="templates/' . CURRENT_ADMIN_TEMPLATE . '/css/icon-picker.css">';
		echo '<link rel="stylesheet" type="text/css" href="templates/' . CURRENT_ADMIN_TEMPLATE . '/css/bootstrap-cseo.css">';
		echo '<link rel="stylesheet" type="text/css" href="templates/' . CURRENT_ADMIN_TEMPLATE . '/css/blog.css">';
		echo '<link rel="stylesheet" type="text/css" href="templates/' . CURRENT_ADMIN_TEMPLATE . '/css/admin.css">';
		?>
        <link rel="stylesheet" type="text/css" href="includes/javascript/jquerytabs/jquery-ui-1.10.2.custom.min.css" />
        <link rel="stylesheet" type="text/css" href="includes/javascript/growl/jquery.gritter.css" media="screen" charset="utf-8" />

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
        <script>
            !window.jQuery && document.write(unescape('%3Cscript src="includes/javascript/jquery.min.js"%3E%3C/script%3E') + unescape('%3Cscript src="includes/javascript/jquery-ui.min.js"%3E%3C/script%3E'));
        </script>
        <script src="includes/javascript/growl/jquery.gritter.min.js"></script>
        <script>
            $('#btnUpdate').click(function() {

                $.gritter.add({
                    title: '<?php echo UPDATE_TITLE; ?>',
                    text: '<?php echo UPDATE_TEXT; ?>',
                    image: 'images/icons/dialog-success.png',
                    sticky: false,
                    time: '1200'
                });

                return false;

            });
        </script>

        <script type="text/javascript">
            $(function() {
                $("#tabslang").tabs();
                $("#tabsstaffel").tabs();
            });
        </script>
        <script type="text/javascript">
            $(function() {
                //  jQueryUI 1.10 and HTML5 ready
                //      http://jqueryui.com/upgrade-guide/1.10/#removed-cookie-option 
                //  Documentation
                //      http://api.jqueryui.com/tabs/#option-active
                //      http://api.jqueryui.com/tabs/#event-activate
                //      http://balaarjunan.wordpress.com/2010/11/10/html5-session-storage-key-things-to-consider/
                //
                //  Define friendly index name
                var index = 'prodtabs';
                //  Define friendly data store name
                var dataStore = window.sessionStorage;
                //  Start magic!
                try {
                    // getter: Fetch previous value
                    var oldIndex = dataStore.getItem(index);
                } catch (e) {
                    // getter: Always default to first tab in error state
                    var oldIndex = 0;
                }
                $('#prodtabs').tabs({
                    // The zero-based index of the panel that is active (open)
                    active: oldIndex,
                    // Triggered after a tab has been activated
                    activate: function(event, ui) {
                        //  Get future value
                        var newIndex = ui.newTab.parent().children().index(ui.newTab);
                        //  Set future value
                        dataStore.setItem(index, newIndex)
                    }
                });
            });
        </script>
        <script type="text/javascript">
            $(function() {
                var index = 'cattabs';
                var dataStore = window.sessionStorage;
                try {
                    var oldIndex = dataStore.getItem(index);
                } catch (e) {
                    var oldIndex = 0;
                }
                $('#cattabs').tabs({
                    active: oldIndex,
                    activate: function(event, ui) {
                        var newIndex = ui.newTab.parent().children().index(ui.newTab);
                        dataStore.setItem(index, newIndex)
                    }
                });
            });
        </script>
        <script type="text/javascript">
            $(function() {
                var index = 'optiontabs';
                var dataStore = window.sessionStorage;
                try {
                    var oldIndex = dataStore.getItem(index);
                } catch (e) {
                    var oldIndex = 0;
                }
                $('#optiontabs').tabs({
                    active: oldIndex,
                    activate: function(event, ui) {
                        var newIndex = ui.newTab.parent().children().index(ui.newTab);
                        dataStore.setItem(index, newIndex)
                    }
                });
            });
        </script>
        <script type="text/javascript">
            $(function() {
                var index = 'tabslisting';
                var dataStore = window.sessionStorage;
                try {
                    var oldIndex = dataStore.getItem(index);
                } catch (e) {
                    var oldIndex = 0;
                }
                $('#tabslisting').tabs({
                    active: oldIndex,
                    activate: function(event, ui) {
                        var newIndex = ui.newTab.parent().children().index(ui.newTab);
                        dataStore.setItem(index, newIndex)
                    }
                });
            });
        </script>
        <script type="text/javascript">
            $(function() {
                var index = 'csstabs';
                var dataStore = window.sessionStorage;
                try {
                    var oldIndex = dataStore.getItem(index);
                } catch (e) {
                    var oldIndex = 0;
                }
                $('#csstabs').tabs({
                    active: oldIndex,
                    activate: function(event, ui) {
                        var newIndex = ui.newTab.parent().children().index(ui.newTab);
                        dataStore.setItem(index, newIndex)
                    }
                });
            });
        </script>
        <script type="text/javascript">
            $(function() {
                var index = 'securitytabs';
                var dataStore = window.sessionStorage;
                try {
                    var oldIndex = dataStore.getItem(index);
                } catch (e) {
                    var oldIndex = 0;
                }
                $('#securitytabs').tabs({
                    active: oldIndex,
                    activate: function(event, ui) {
                        var newIndex = ui.newTab.parent().children().index(ui.newTab);
                        dataStore.setItem(index, newIndex)
                    }
                });
            });
        </script>
        <link href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>favicon.ico" rel="shortcut icon" type="image/x-icon" />

    </head>
    <body>
	
<?php 
if (file_exists(DIR_WS_INCLUDES . 'addons/metatag_addon.php')) {
	include (DIR_WS_INCLUDES .'addons/metatag_addon.php');
}