<?php
/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010 - 2013 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License

 */

///////////////////////////////////////////////////////////////////////////////
///																																					 //
///                 MailBeez Core file - do not edit                         //
///                                                                          //
///////////////////////////////////////////////////////////////////////////////

require('includes/application_top.php');

if (substr(DIR_FS_CATALOG, -1) != '/') {
    define('MH_DIR_FS_CATALOG', DIR_FS_CATALOG . '/');
} else {
    define('MH_DIR_FS_CATALOG', DIR_FS_CATALOG);
}
if (substr(DIR_WS_CATALOG, -1) != '/') {
    define('MH_DIR_WS_CATALOG', DIR_WS_CATALOG . '/');
} else {
    define('MH_DIR_WS_CATALOG', DIR_WS_CATALOG);
}

if (file_exists(MH_DIR_FS_CATALOG . 'mailhive/common/main/inc_mailbeez.php')) {
    require_once(MH_DIR_FS_CATALOG . 'mailhive/common/main/inc_mailbeez.php');
} else {
	require(DIR_WS_INCLUDES . 'header.php');
    echo '<h1>MailBeez noch nicht installiert? Worauf warten Sie?</h1>';
    echo '<p>Einfach das MailBeez Modul downloaden und im Shop installieren.</p>';
    echo '<img src="https://www.mailbeez.com/wp-content/uploads/2010/05/mailbeezdash.png" alt="Mailbeez dashboard" title="A typical Mailbeez dashboard, sat right within your stores admin area!" width="599" height="260" class="size-full wp-image-2179">';
    echo '<br><br><a class="btn btn-danger" href="http://www.mailbeez.de/dokumentation/installation/basic-installation-commerceseo-v2next/?a=cseo" target="_blank">zum MailBeez Modul</a>';

}

require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
