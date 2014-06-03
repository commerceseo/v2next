<?php
/* -----------------------------------------------------------------
 * 	$Id: 403.php 420 2013-06-19 18:04:39Z akausch $
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

header($_SERVER["SERVER_PROTOCOL"] . ' 403 Forbidden');
header('Error 403: Forbidden');
header('Content-type: text/html');
?>

<html>
    <head>
        <title>403 Forbidden</title>
    </head>
    <body>
        <h1>Forbidden</h1>
        <p>You do not have permission to access this document.</p><hr />
        <address>
            Web Server at <?php echo $_SERVER['SERVER_NAME']; ?>
        </address>
    </body>
</html>

<!--
   - Unfortunately, Microsoft has added a clever new
   - "feature" to Internet Explorer. If the text of
   - an error's message is "too small", specifically
   - less than 512 bytes, Internet Explorer returns
   - its own error message. You can turn that off,
   - but it's pretty tricky to find switch called
   - "smart error messages". That means, of course,
   - that short error messages are censored by default.
   - IIS always returns error messages that are long
   - enough to make Internet Explorer happy. The
   - workaround is pretty simple: pad the error
   - message with a big comment like this to push it
   - over the five hundred and twelve bytes minimum.
   - Of course, that's exactly what you're reading
   - right now.
-->

