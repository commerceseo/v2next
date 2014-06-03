<?php
/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010 - 2013 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License

 */


if (file_exists('mailhive/common/main/inc_mailhive.php')) {
    require_once('mailhive/common/main/inc_mailhive.php');
} else {
    ?>
    Please install MailBeez
<?php
}
?>