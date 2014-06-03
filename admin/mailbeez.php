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
    ?>
    <a href="http://www.mailbeez.de/?a=cseo" target="_blank">Please install MailBeez</a>
<?php
}


?>