<?php
/**
 * Merchant registration languange script
 *
 */
require_once('includes/application_top.php');
if (isset($_SESSION['language']) && $_SESSION['language'] == 'german') {
    echo 'de';
} else {
    echo 'en';
}
?>
