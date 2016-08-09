<?php
/* -----------------------------------------------------------------
 * 	$Id: index.php 1499 2015-10-27 21:54:20Z akausch $
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

if (version_compare(PHP_VERSION, '5.2.0', '<')) {
    die("Für die Installation des commerce:SEO v2 wird mindestens PHP 5.2 vorausgesetzt. Wenden Sie sich an Ihren Provider mit der Bitte PHP auf Ihrem Server zu aktualisieren. Ihre PHP Version lautet: " . PHP_VERSION);
}

require('includes/application.php');
require_once('includes/FTPManager.inc.php');

header("Content-Type: text/html; charset=utf-8");

require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
require_once(DIR_FS_INC . 'xtc_href_link.inc.php');

if (isset($_SESSION['language']) && $_SESSION['language'] == 'english') {
    include('language/english.php');
} else {
    include('language/german.php');
}

if (isset($_GET['precheck']) && $_GET['precheck'] == '1') {
    // check register_globals
    $t_register_globals = false;
    if (ini_get('register_globals') == '1' || ini_get('register_globals') == 'on' || ini_get('register_globals') == 'On') {
        $t_register_globals = true;
    }

    // check uploaded files
    $fp = fopen("txt/filelist.txt", "r");
    $t_missing_files_array = array();
    while ($t_line = fgets($fp, 1024)) {
        $t_dir = DIR_FS_CATALOG . $t_line;
        if (file_exists(trim($t_dir)) == false) {
            if (is_dir(DIR_FS_CATALOG . 'templates/v2next-new-c2-blue/') == false && strstr($t_line, 'v2next-new-c2-blue') !== false)
                continue;
            $t_missing_files_array[] = $t_line;
        }
    }
    fclose($fp);

    if ($t_register_globals === false && empty($t_missing_files_array)) {
        header('Location: index.php?language=' . rawurlencode($_GET['language']));
    }
}

if (!$script_filename = str_replace("\\", '/', getenv('PATH_TRANSLATED'))) {
    $script_filename = getenv('SCRIPT_FILENAME');
}
$script_filename = str_replace('//', '/', $script_filename);

if (!$request_uri = getenv('REQUEST_URI')) {
    if (!$request_uri = getenv('PATH_INFO')) {
        $request_uri = getenv('SCRIPT_NAME');
    }

    if (getenv('QUERY_STRING'))
        $request_uri .= '?' . getenv('QUERY_STRING');
}

$dir_fs_www_root_array = explode('/', dirname($script_filename));
$dir_fs_www_root = array();
for ($i = 0; $i < sizeof($dir_fs_www_root_array) - 2; $i++) {
    $dir_fs_www_root[] = $dir_fs_www_root_array[$i];
}
$dir_fs_www_root = implode('/', $dir_fs_www_root);

$dir_ws_www_root_array = explode('/', dirname($request_uri));
$dir_ws_www_root = array();
for ($i = 0; $i < sizeof($dir_ws_www_root_array) - 1; $i++) {
    $dir_ws_www_root[] = $dir_ws_www_root_array[$i];
}
$dir_ws_www_root = implode('/', $dir_ws_www_root);

$coo_ftp_manager2 = new FTPManager(false, '', '', '', '');
$t_wrong_chmod_array = $coo_ftp_manager2->check_chmod();

for ($i = 0; $i < count($t_wrong_chmod_array); $i++) {
    $t_wrong_chmod_array[$i] = str_replace(DIR_FS_CATALOG, '', $t_wrong_chmod_array[$i]);
}
sort($t_wrong_chmod_array);

if (isset($_POST['FTP_HOST']) && !empty($t_wrong_chmod_array) && !isset($_GET['chmod'])) {
    $t_host = $_POST['FTP_HOST'];
    $t_user = $_POST['FTP_USER'];
    $t_password = $_POST['FTP_PASSWORD'];
    $t_pasv = false;
    if (!empty($_POST['FTP_PASV']))
        $t_pasv = true;

    $coo_ftp_manager = new FTPManager(true, $t_host, $t_user, $t_password, $t_pasv);

    if ($coo_ftp_manager->v_error == '') {
        $t_dir = '/';

        if (isset($_POST['dir'])) {
            $t_dir = $_POST['dir'];
        }

        $t_list_array = $coo_ftp_manager->get_directories($t_dir);

        $_SESSION['FTP_HOST'] = $_POST['FTP_HOST'];
        $_SESSION['FTP_USER'] = $_POST['FTP_USER'];
        $_SESSION['FTP_PASSWORD'] = $_POST['FTP_PASSWORD'];
        if (!empty($_POST['FTP_PASV'])) {
            $_SESSION['FTP_PASV'] = $_POST['FTP_PASV'];
        }
    }
}

if (empty($t_wrong_chmod_array) && !isset($_GET['chmod']) && isset($_GET['language']) && (($t_register_globals === false && empty($t_missing_files_array)) || !isset($_GET['precheck']))) {
    header('Location: index.php?chmod=ok&language=' . rawurlencode($_GET['language']));
}
?>
<!DOCTYPE html>
<html lang="de" class="no-js" dir="ltr">
    <head>
        <title>Installation commerce:seo v2next</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link type="text/css" rel="stylesheet" href="css/stylesheet.css" />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

        <script type="text/javascript">

            var t_http_valid = false;
            var t_https_valid = false;
            var t_http_interval = 0;
            var t_https_interval = 0;
            var t_http_counter = 0;
            var t_https_counter = 0;

            var validate_server = function(p_ssl) {
                if ($('.server_data').css('display') != 'none') {
                    // do not allow http-address in https field
                    if ($('input[name="HTTPS_SERVER"]').length > 0) {
                        $('input[name="HTTPS_SERVER"]').val($('input[name="HTTPS_SERVER"]').val().replace('http:', 'https:'));
                    }

                    if (p_ssl == true) {
                        var t_ssl_test_image = new Image();
                        t_ssl_test_image.src = $('input[name="HTTPS_SERVER"]').val() + $('input[name="DIR_WS_CATALOG"]').val() + 'images/pixel_trans.gif';
                        if (!isNaN(t_ssl_test_image.height) && t_ssl_test_image.height > 0) {
                            clearInterval(t_https_interval);
                            t_https_valid = true;
                            t_https_counter = 0;

                            $('input[name="HTTPS_SERVER"]').removeClass('invalid');
                            $('input[name="HTTPS_SERVER"]').addClass('valid');
                            $('input[name="HTTPS_SERVER"]').closest('tr').find('.input_error').hide();
                            return true;
                        } else {
                            if (t_https_counter > 10) {
                                clearInterval(t_https_interval);
                            }
                            t_https_counter++;
                            t_https_valid = false;

                            $('input[name="HTTPS_SERVER"]').removeClass('valid');
                            $('input[name="HTTPS_SERVER"]').addClass('invalid');
                            $('input[name="HTTPS_SERVER"]').closest('tr').find('.input_error').show();

                            return false;
                        }
                    } else {
                        var t_test_image = new Image();
                        t_test_image.src = $('input[name="HTTP_SERVER"]').val() + $('input[name="DIR_WS_CATALOG"]').val() + 'images/pixel_trans.gif';
                        if (!isNaN(t_test_image.height) && t_test_image.height > 0) {
                            clearInterval(t_http_interval);
                            t_http_valid = true;
                            t_http_counter = 0;

                            $('input[name="HTTP_SERVER"]').removeClass('invalid');
                            $('input[name="HTTP_SERVER"]').addClass('valid');
                            $('input[name="HTTP_SERVER"]').closest('tr').find('.input_error').hide();

                            return true;
                        } else {
                            if (t_http_counter > 10) {
                                clearInterval(t_http_interval);
                            }
                            t_http_counter++;
                            t_http_valid = true;

                            $('input[name="HTTP_SERVER"]').removeClass('valid');
                            $('input[name="HTTP_SERVER"]').addClass('invalid');
                            $('input[name="HTTP_SERVER"]').closest('tr').find('.input_error').show();

                            return false;
                        }
                    }
                }
            }

            var validate_input = function(p_string, p_min_len, p_only_numbers, p_mail) {
                var t_string = jQuery.trim(p_string);

                if (typeof (p_mail) == 'boolean' && p_mail == true) {
                    t_pattern = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                    return t_pattern.test(t_string);
                }

                if (typeof (p_only_numbers) == 'boolean' && p_only_numbers == true) {
                    t_pattern = /(^[0-9]+$)/g;

                    if (!t_pattern.test(t_string)) {
                        return false;
                    }
                }

                if (typeof (p_min_len) == 'undefined') {
                    p_min_len = 0;
                } else {
                    if (t_string.length >= Number(p_min_len)) {
                        return true;
                    }

                    return false;
                }

                return true;
            }

            var validate_form = function() {
                var t_valid = true;

                if (!validate_input($('input[name="FIRST_NAME"]').val(), 2)) {
                    $('input[name="FIRST_NAME"]').removeClass('valid').addClass('invalid');
                    $('input[name="FIRST_NAME"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                if (!validate_input($('input[name="LAST_NAME"]').val(), 2)) {
                    $('input[name="LAST_NAME"]').removeClass('valid').addClass('invalid');
                    $('input[name="LAST_NAME"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                if (!validate_input($('input[name="CITY"]').val(), 2)) {
                    $('input[name="CITY"]').removeClass('valid').addClass('invalid');
                    $('input[name="CITY"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                if (!validate_input($('input[name="COMPANY"]').val(), 2)) {
                    $('input[name="COMPANY"]').removeClass('valid').addClass('invalid');
                    $('input[name="COMPANY"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                if ($('input[name="STATE"]').length > 0 && !validate_input($('input[name="STATE"]').val(), 2)) {
                    $('input[name="STATE"]').removeClass('valid').addClass('invalid');
                    $('input[name="STATE"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                if (!validate_input($('input[name="STREET_ADRESS"]').val(), 5)) {
                    $('input[name="STREET_ADRESS"]').removeClass('valid').addClass('invalid');
                    $('input[name="STREET_ADRESS"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                if ($('input[name="PASSWORD"]').val() != $('input[name="PASSWORD_CONFIRMATION"]').val()) {
                    $('input[name="PASSWORD_CONFIRMATION"]').val('');
                    t_valid = false;
                }

                if (!validate_input($('input[name="PASSWORD"]').val(), 6)) {
                    $('input[name="PASSWORD"]').removeClass('valid').addClass('invalid');
                    $('input[name="PASSWORD"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                if (!validate_input($('input[name="PASSWORD_CONFIRMATION"]').val(), 6)) {
                    $('input[name="PASSWORD_CONFIRMATION"]').removeClass('valid').addClass('invalid');
                    $('input[name="PASSWORD_CONFIRMATION"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                if (!validate_input($('input[name="POST_CODE"]').val(), 4)) {
                    $('input[name="POST_CODE"]').removeClass('valid').addClass('invalid');
                    $('input[name="POST_CODE"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                if (!validate_input($('input[name="STORE_NAME"]').val(), 3)) {
                    $('input[name="STORE_NAME"]').removeClass('valid').addClass('invalid');
                    $('input[name="STORE_NAME"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                if (!validate_input($('input[name="EMAIL_ADRESS"]').val(), 6, false, true)) {
                    $('input[name="EMAIL_ADRESS"]').removeClass('valid').addClass('invalid');
                    $('input[name="EMAIL_ADRESS"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                if (!validate_input($('input[name="EMAIL_ADRESS_FROM"]').val(), 6, false, true)) {
                    $('input[name="EMAIL_ADRESS_FROM"]').removeClass('valid').addClass('invalid');
                    $('input[name="EMAIL_ADRESS_FROM"]').closest('tr').find('.input_error').show();
                    t_valid = false;
                }

                return t_valid;
            }

            $(document).ready(function() {
                // try to reset shop admin data form
                $('.shop_data input[type="text"], .shop_data input[type="password"]').val('');

                var t_test_image = new Image();
                t_test_image.src = $('input[name="HTTP_SERVER"]').val() + $('input[name="DIR_WS_CATALOG"]').val() + 'images/pixel_trans.gif';

                var t_ssl_test_image = new Image();
                t_ssl_test_image.src = $('input[name="HTTPS_SERVER"]').val() + $('input[name="DIR_WS_CATALOG"]').val() + 'images/pixel_trans.gif';

                t_http_interval = setInterval(function() {
                    validate_server(false)
                }, 100);
                t_https_interval = setInterval(function() {
                    validate_server(true)
                }, 100);

                $('input[name="HTTP_SERVER"]').live('blur', function() {
                    t_http_interval = setInterval(function() {
                        validate_server(false)
                    }, 100);
                })

                $('input[name="HTTPS_SERVER"]').live('blur', function() {
                    t_https_interval = setInterval(function() {
                        validate_server(true)
                    }, 100);
                })

                $('input[name="ENABLE_SSL"]').change(function() {
                    if ($(this).attr('checked') == true) {
                        $('.https_server').show();
                    } else {
                        $('.https_server').hide();
                    }
                });


                var t_test_db_connection = Object();

                var test_db_connection = function() {
                    if ($('input[name="DB_SERVER"]').val() != '' && $('input[name="DB_SERVER_USERNAME"]').val() != '' && $('input[name="DB_SERVER_PASSWORD"]').val() != '') {
                        if (typeof (t_test_db_connection.abort) == 'function') {
                            t_test_db_connection.abort();
                        }

                        t_test_db_connection = jQuery.ajax({
                            data: 'action=test_db_connection&' + $('#install_form').serialize(),
                            url: 'request_port.php',
                            type: "POST",
                            async: true,
                            success: function(t_db_result)
                            {
                                t_result = t_db_result;

                                if (t_result == 'no connection') {
                                    $('input[name="DB_SERVER"]').removeClass('valid').addClass('invalid');
                                    $('input[name="DB_SERVER_USERNAME"]').removeClass('valid').addClass('invalid');
                                    $('input[name="DB_SERVER_PASSWORD"]').removeClass('valid').addClass('invalid');
                                    $('input[name="DB_SERVER"]').closest('tr').find('.input_error').show();
                                } else if (t_result == 'no database') {
                                    $('input[name="DB_SERVER"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_SERVER_USERNAME"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_SERVER_PASSWORD"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_SERVER"]').closest('tr').find('.input_error').hide();
                                    if ($('input[name="DB_DATABASE"]').val() != '') {
                                        $('input[name="DB_DATABASE"]').removeClass('valid').addClass('invalid');
                                        $('input[name="DB_DATABASE"]').closest('tr').find('.input_error').show();
                                    }
                                } else {
                                    $('input[name="DB_SERVER"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_SERVER_USERNAME"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_SERVER_PASSWORD"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_DATABASE"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_SERVER"]').closest('tr').find('.input_error').hide();
                                    $('input[name="DB_DATABASE"]').closest('tr').find('.input_error').hide();
                                }
                            }
                        });
                    }
                }

                $('.server_data input').live('blur', test_db_connection);


                $('#import_sql').click(function() {
                    var t_result;
                    var t_force_import = $('#install_form').serialize().search('force_db=1');

                    if ($('input[name="HTTPS_SERVER"]').val() == 'https://' || $('input[name="HTTPS_SERVER"]').val().search('https://') == -1) {
                        $('input[name="HTTPS_SERVER"]').val($('input[name="HTTP_SERVER"]').val().replace('http:', 'https:'));
                    }

                    // HTTP-Server hast to be valid, invalid HTTPS-Server is tolerated even if SSL is set active
                    if ($('input[name="HTTP_SERVER"]').hasClass('valid')) {
                        jQuery.ajax({
                            data: 'action=test_db_connection&' + $('#install_form').serialize(),
                            url: 'request_port.php',
                            type: "POST",
                            async: true,
                            success: function(t_db_result)
                            {
                                t_result = t_db_result;

                                if (t_result == 'success' || t_force_import > 0) {
                                    $('.server_data').hide();
                                    $('.progress').show();

                                    $('#ajax').html('<div class="progress-bar">0%</div>');
                                    jQuery.ajax({
                                        data: 'action=import_sql&' + $('#install_form').serialize(),
                                        url: 'request_port.php',
                                        type: "POST",
                                        async: true,
                                        success: function(t_sql_result)
                                        {
                                            t_result = t_sql_result;

                                            if (t_result == 'success') {
                                                $('.progress-bar').html('12%');
                                                $('.progress-bar').css('box-shadow', '0 1px 3px rgba(0,0,0,0.5), inset -660px 0 #fff');

                                                jQuery.ajax({
                                                    data: 'action=import_sql&' + $('#install_form').serialize(),
                                                    url: 'request_port.php?db=css1',
                                                    type: "POST",
                                                    async: true,
                                                    success: function(t_sql_result)
                                                    {
                                                        t_result = t_sql_result;

                                                        if (t_result == 'success') {
                                                            $('.progress-bar').html('25%');
                                                            $('.progress-bar').css('box-shadow', '0 1px 3px rgba(0,0,0,0.5), inset -563px 0 #fff');

                                                            jQuery.ajax({
                                                                data: 'action=import_sql&' + $('#install_form').serialize(),
                                                                url: 'request_port.php?db=css2',
                                                                type: "POST",
                                                                async: true,
                                                                success: function(t_sql_result)
                                                                {
                                                                    t_result = t_sql_result;

                                                                    if (t_result == 'success') {
                                                                        $('.progress-bar').html('38%');
                                                                        $('.progress-bar').css('box-shadow', '0 1px 3px rgba(0,0,0,0.5), inset -465px 0 #fff');

                                                                        jQuery.ajax({
                                                                            data: 'action=import_sql&' + $('#install_form').serialize(),
                                                                            url: 'request_port.php?db=css3',
                                                                            type: "POST",
                                                                            async: true,
                                                                            success: function(t_sql_result)
                                                                            {
                                                                                t_result = t_sql_result;

                                                                                if (t_result == 'success') {
                                                                                    $('.progress-bar').html('50%');
                                                                                    $('.progress-bar').css('box-shadow', '0 1px 3px rgba(0,0,0,0.5), inset -375px 0 #fff');

                                                                                    jQuery.ajax({
                                                                                        data: 'action=import_sql&' + $('#install_form').serialize(),
                                                                                        url: 'request_port.php?db=css4',
                                                                                        type: "POST",
                                                                                        async: true,
                                                                                        success: function(t_sql_result)
                                                                                        {
                                                                                            t_result = t_sql_result;

                                                                                            if (t_result == 'success') {
                                                                                                $('.progress-bar').html('68%');
                                                                                                $('.progress-bar').css('box-shadow', '0 1px 3px rgba(0,0,0,0.5), inset -240px 0 #fff');

                                                                                                jQuery.ajax({
                                                                                                    data: 'action=import_sql&' + $('#install_form').serialize(),
                                                                                                    url: 'request_port.php?db=blz',
                                                                                                    type: "POST",
                                                                                                    async: true,
                                                                                                    success: function(t_sql_result)
                                                                                                    {
                                                                                                        t_result = t_sql_result;

                                                                                                        if (t_result == 'success') {
                                                                                                            $('.progress-bar').html('75%');
                                                                                                            $('.progress-bar').css('box-shadow', '0 1px 3px rgba(0,0,0,0.5), inset -187px 0 #fff');

                                                                                                            jQuery.ajax({
                                                                                                                data: 'action=import_sql&' + $('#install_form').serialize(),
                                                                                                                url: 'request_port.php?db=lang',
                                                                                                                type: "POST",
                                                                                                                async: true,
                                                                                                                success: function(t_sql_result)
                                                                                                                {
                                                                                                                    t_result = t_sql_result;

                                                                                                                    if (t_result == 'success') {
                                                                                                                        $('.progress-bar').html('88%');
                                                                                                                        $('.progress-bar').css('box-shadow', '0 1px 3px rgba(0,0,0,0.5), inset -90px 0 #fff');

                                                                                                                        jQuery.ajax({
                                                                                                                            data: 'action=import_sql&' + $('#install_form').serialize(),
                                                                                                                            url: 'request_port.php?db=lang2',
                                                                                                                            type: "POST",
                                                                                                                            async: true,
                                                                                                                            success: function(t_sql_result)
                                                                                                                            {
                                                                                                                                t_result = t_sql_result;

                                                                                                                                if (t_result == 'success') {
                                                                                                                                    $('.progress-bar').html('99%');
                                                                                                                                    $('.progress-bar').css('box-shadow', '0 1px 3px rgba(0,0,0,0.5), inset -2px 0 #fff');

                                                                                                                                    jQuery.ajax({
                                                                                                                                        data: 'action=write_config&' + $('#install_form').serialize(),
                                                                                                                                        url: 'request_port.php',
                                                                                                                                        type: "POST",
                                                                                                                                        async: true,
                                                                                                                                        success: function(t_sql_result)
                                                                                                                                        {
                                                                                                                                            $('.progress').hide();

                                                                                                                                            t_result = t_sql_result;

                                                                                                                                            if (t_result == 'success') {
                                                                                                                                                $('#ajax').html('');

                                                                                                                                                jQuery.ajax({
                                                                                                                                                    data: 'action=get_states&' + $('#install_form').serialize(),
                                                                                                                                                    url: 'request_port.php',
                                                                                                                                                    type: "POST",
                                                                                                                                                    async: true,
                                                                                                                                                    success: function(t_states)
                                                                                                                                                    {
                                                                                                                                                        $('#states_container').html(t_states);
                                                                                                                                                    }
                                                                                                                                                }).html;

                                                                                                                                                jQuery.ajax({
                                                                                                                                                    data: 'action=get_countries&' + $('#install_form').serialize(),
                                                                                                                                                    url: 'request_port.php',
                                                                                                                                                    type: "POST",
                                                                                                                                                    async: true,
                                                                                                                                                    success: function(t_countries)
                                                                                                                                                    {
                                                                                                                                                        $('#countries_container').html(t_countries);

                                                                                                                                                        $('.shop_data').show();
                                                                                                                                                    }
                                                                                                                                                }).html;
                                                                                                                                            } else {
                                                                                                                                                $('.progress-bar').hide();
                                                                                                                                                $('#ajax').html('<div class="error"><?php echo ERROR_CONFIG_FILES; ?></div><br /><br /><a class="button gradient" href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                    }).html;
                                                                                                                                } else {
                                                                                                                                    $('.progress').hide();
                                                                                                                                    $('.progress-bar').hide();
                                                                                                                                    $('#ajax').html(t_result + '<br /><br /><a class="button gradient" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }).html;
                                                                                                                    } else {
                                                                                                                        $('.progress').hide();
                                                                                                                        $('.progress-bar').hide();
                                                                                                                        $('#ajax').html(t_result + '<br /><br /><a class="button gradient" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
                                                                                                                    }
                                                                                                                }
                                                                                                            }).html;
                                                                                                        } else {
                                                                                                            $('.progress').hide();
                                                                                                            $('.progress-bar').hide();
                                                                                                            $('#ajax').html(t_result + '<br /><br /><a class="button gradient" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
                                                                                                        }
                                                                                                    }
                                                                                                }).html;
                                                                                            } else {
                                                                                                $('.progress').hide();
                                                                                                $('.progress-bar').hide();
                                                                                                $('#ajax').html(t_result + '<br /><br /><a class="button gradient" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
                                                                                            }
                                                                                        }
                                                                                    }).html;
                                                                                } else {
                                                                                    $('.progress').hide();
                                                                                    $('.progress-bar').hide();
                                                                                    $('#ajax').html(t_result + '<br /><br /><a class="button gradient" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
                                                                                }
                                                                            }
                                                                        }).html;
                                                                    } else {
                                                                        $('.progress').hide();
                                                                        $('.progress-bar').hide();
                                                                        $('#ajax').html(t_result + '<br /><br /><a class="button gradient" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
                                                                    }
                                                                }
                                                            }).html;
                                                        } else {
                                                            $('.progress').hide();
                                                            $('.progress-bar').hide();
                                                            $('#ajax').html(t_result + '<br /><br /><a class="button gradient" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
                                                        }
                                                    }
                                                }).html;
                                            } else {
                                                $('.progress').hide();
                                                $('.progress-bar').hide();
                                                $('#ajax').html(t_result + '<br /><br /><a class="button gradient" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
                                            }
                                        }
                                    }).html;
                                } else if (t_result == 'no connection') {
                                    $('input[name="DB_SERVER"]').removeClass('valid').addClass('invalid');
                                    $('input[name="DB_SERVER_USERNAME"]').removeClass('valid').addClass('invalid');
                                    $('input[name="DB_SERVER_PASSWORD"]').removeClass('valid').addClass('invalid');
                                } else if (t_result == 'no database') {
                                    $('input[name="DB_SERVER"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_SERVER_USERNAME"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_SERVER_PASSWORD"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_DATABASE"]').removeClass('valid').addClass('invalid');
                                } else if (t_result != 'no connection' && t_result != 'no database' && t_result != '') {
                                    $('#ajax').html('<div class="error_field">' + t_result + '</div>');

                                    $('input[name="DB_SERVER"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_SERVER_USERNAME"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_SERVER_PASSWORD"]').removeClass('invalid').addClass('valid');
                                    $('input[name="DB_DATABASE"]').removeClass('invalid').addClass('valid');
                                    $('#ajax').prepend('<span class="error"><?php echo ERROR_TABLES_EXIST; ?></span><br /><br /><strong><?php echo TEXT_TABLES_EXIST; ?></strong><br /><br />');
                                    $('#ajax').append('<br /><br /><input type="checkbox" name="force_db" value="1" id="force_db" /><label for="force_db"> <?php echo LABEL_FORCE_DB; ?></label><br /><br />');
                                }
                            }
                        }).html;
                    }
                });


                $('input[name="FIRST_NAME"], input[name="LAST_NAME"], input[name="CITY"], input[name="COMPANY"], input[name="STATE"]').live('blur', function() {
                    if (validate_input($(this).val(), 2)) {
                        $(this).removeClass('invalid').addClass('valid');
                        $(this).closest('tr').find('.input_error').hide();
                    } else {
                        $(this).removeClass('valid').addClass('invalid');
                        $(this).closest('tr').find('.input_error').show();
                    }
                });

                $('input[name="STREET_ADRESS"], input[name="PASSWORD"]').live('blur', function() {
                    if ($(this).val() != $('input[name="PASSWORD_CONFIRMATION"]').val()) {
                        $('input[name="PASSWORD_CONFIRMATION"]').val('');
                    }

                    if (validate_input($(this).val(), 6)) {
                        $(this).removeClass('invalid').addClass('valid');
                        $(this).closest('tr').find('.input_error').hide();
                    } else {
                        $(this).removeClass('valid').addClass('invalid');
                        $(this).closest('tr').find('.input_error').show();
                    }
                });

                $('input[name="PASSWORD_CONFIRMATION"]').live('blur', function() {
                    if (validate_input($(this).val(), 6) && $(this).val() == $('input[name="PASSWORD"]').val()) {
                        $(this).removeClass('invalid').addClass('valid');
                        $(this).closest('tr').find('.input_error').hide();
                    } else {
                        $(this).removeClass('valid').addClass('invalid');
                        $(this).closest('tr').find('.input_error').show();
                    }
                });

                $('input[name="POST_CODE"]').live('blur', function() {
                    if (validate_input($(this).val(), 4, true)) {
                        $(this).removeClass('invalid').addClass('valid');
                        $(this).closest('tr').find('.input_error').hide();
                    } else {
                        $(this).removeClass('valid').addClass('invalid');
                        $(this).closest('tr').find('.input_error').show();
                    }
                });

                $('input[name="TELEPHONE"]').live('blur', function() {
                    if (validate_input($(this).val(), 4)) {
                        $(this).removeClass('invalid').addClass('valid');
                        $(this).closest('tr').find('.input_error').hide();
                    } else {
                        $(this).removeClass('valid').addClass('invalid');
                        $(this).closest('tr').find('.input_error').show();
                    }
                });

                $('input[name="STORE_NAME"]').live('blur', function() {
                    if (validate_input($(this).val(), 3)) {
                        $(this).removeClass('invalid').addClass('valid');
                        $(this).closest('tr').find('.input_error').hide();
                    } else {
                        $(this).removeClass('valid').addClass('invalid');
                        $(this).closest('tr').find('.input_error').show();
                    }
                });

                $('input[name="EMAIL_ADRESS"], input[name="EMAIL_ADRESS_FROM"]').live('blur', function() {
                    if (validate_input($(this).val(), 6, false, true)) {
                        $(this).removeClass('invalid').addClass('valid');
                        $(this).closest('tr').find('.input_error').hide();
                    } else {
                        $(this).removeClass('valid').addClass('invalid');
                        $(this).closest('tr').find('.input_error').show();
                    }
                });

                $('#run_config').live('click', function()
                {
                    var t_result;

                    if (validate_form() == true) {
                        $('.shop_data').hide();

                        jQuery.ajax({
                            data: 'action=create_account&' + $('#install_form').serialize(),
                            url: 'request_port.php',
                            type: "POST",
                            async: true,
                            success: function(t_sql_result)
                            {
                                t_result = t_sql_result;
                                $('#ajax').html('<?php echo TEXT_FINAL_SETTINGS; ?>');

                                if (t_result == 'success') {
                                    jQuery.ajax({
                                        data: 'action=setup_shop&' + $('#install_form').serialize(),
                                        url: 'request_port.php',
                                        type: "POST",
                                        async: true,
                                        success: function(t_sql_result)
                                        {
                                            t_result = t_sql_result;

                                            if (t_result == 'success') {
                                                $('#ajax').html('');

                                                jQuery.ajax({
                                                    data: 'action=chmod_444&' + $('#install_form').serialize(),
                                                    url: 'request_port.php',
                                                    type: "POST",
                                                    async: true
                                                }).html;

                                                if ($('input[name="DIR_WS_CATALOG"]').val() != '/') {
                                                    $('#ajax').html('<?php echo TEXT_WRITE_ROBOTS_FILE; ?>');

                                                    jQuery.ajax({
                                                        data: 'action=write_robots_file&' + $('#install_form').serialize(),
                                                        url: 'request_port.php',
                                                        type: "POST",
                                                        async: true,
                                                        success: function(t_sql_result)
                                                        {
                                                            t_result = t_sql_result;

                                                            if (t_result == 'failed') {
                                                                $('.robots_data').show();
                                                            }

                                                            $('#ajax').html('');
                                                            $('#install_service').hide();
                                                            $('.finish').show();
                                                            $('.finish.button').css('display', 'inline-block');
                                                        },
                                                        error: function()
                                                        {
                                                            $('#ajax').html('');
                                                            $('#install_service').hide();
                                                            $('.finish').show();
                                                            $('.robots_data').show();
                                                            $('.finish.button').css('display', 'inline-block');
                                                        }
                                                    }).html;
                                                } else {
                                                    $('#install_service').hide();
                                                    $('.finish').show();
                                                    $('.finish.button').css('display', 'inline-block');
                                                }
                                            } else {
                                                $('#ajax').html('<div class="error"><?php echo ERROR_UNEXPECTED; ?></div><br /><br /><a class="button gradient" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
                                            }
                                        }
                                    }).html;
                                } else {
                                    $('#ajax').html('<div class="error"><?php echo ERROR_UNEXPECTED; ?></div><br /><br /><a class="button gradient" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
                                }
                            }
                        }).html;
                    }
                });

                $('select[name="COUNTRY"]').live('change', function()
                {
                    jQuery.ajax({
                        data: 'action=get_states&' + $('#install_form').serialize(),
                        url: 'request_port.php',
                        type: "POST",
                        async: true,
                        success: function(t_states)
                        {
                            $('input[name="STATE"]').closest('tr').find('.input_error').hide();
                            $('#states_container').html(t_states);
                        }
                    }).html;
                });
            });

        </script>
    </head>

    <body>
        <div class="wrapper">
            <div id="main" class="gradient">
                <header>
                    <img src="images/cseo_logo.png" alt="commerce:SEO v2next" />
                    <h1>Installation commerce:SEO v2Next</h1>
                </header>
                <div id="install_service">
                    <p><strong><?php echo HEADING_INSTALLATION_SERVICE; ?></strong></p>
                    <p><?php echo TEXT_INSTALLATION_SERVICE; ?><br />
                        <br />
                        <a href="http://www.commerce-seo.de/" class="button gradient red" target="_blank"><?php echo BUTTON_CSEO_PORTAL; ?></a>
                    </p>


                </div>

                <form name="install" id="install_form" action="index.php?language=<?php echo rawurlencode($_GET['language']); ?>" method="post">
                    <?php
                    if ($t_session_started === false) {
                        echo ' <p><strong class="error">' . sprintf(ERROR_SESSION_SAVE_PATH, $dir_ws_www_root . '/cache') . '</strong></p>';
                    } elseif (!isset($_GET['language'])) {
                        ?>
                        <p><strong><?php echo HEADING_INSTALLATION; ?></strong></p>
                        <p>
                        <?php echo TEXT_INSTALLATION; ?><br />
                            <br />
                            <a href="index.php?language=german&precheck=1" class="button gradient green"><?php echo BUTTON_GERMAN; ?></a>&nbsp;
                            <a href="index.php?language=english&precheck=1" class="button gradient green"><?php echo BUTTON_ENGLISH; ?></a>
                        </p>
                            <?php
                        } elseif (isset($_GET) && $_GET['precheck'] == '1') {
                            ?>
                        <div class="precheck">
    <?php
    if ($t_register_globals) {
        ?>
                                <strong><?php echo HEADING_REGISTER_GLOBALS; ?></strong>
                                <br />
                                <br />
                                <?php echo TEXT_REGISTER_GLOBALS; ?>
                                <br />
                                <br />
                                <br />
                                <?php
                            }
                            if (!empty($t_missing_files_array)) {
                                ?>
                                <div class="error"><?php echo ERROR_MISSING_FILES; ?></div>
                                <br />
        <?php echo TEXT_MISSING_FILES; ?>
                                <br />
                                <br />
                                <div class="error_field">
                                    <?php
                                    echo implode('<br />', $t_missing_files_array);
                                    ?>
                                </div>
                                <br />
                                <a href="index.php?precheck=1&language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient"><?php echo BUTTON_CHECK_MISSING_FILES; ?></a>
                                <br />
                                <br />
                                <br />
                                <?php
                            }
                            ?>
                            <a href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient green"><?php echo BUTTON_CONTINUE; ?></a>
                        </div>
                        <?php
                    } elseif (!isset($_GET) || $_GET['ftp'] == 'done' || !isset($_GET['chmod'])) {
                        ?>
                        <div class="ftp_data">
                            <?php
                            if (isset($_GET) && $_GET['ftp'] == 'done') {
                                ?>

                                <span class="error"><?php echo ERROR_SET_PERMISSIONS_FAILED; ?></span>
                                <br />
                                <br />
                                <a href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient"><?php echo BUTTON_BACK; ?></a>&nbsp;
                                <a href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient green"><?php echo BUTTON_CONTINUE; ?></a>
                                <br />
                                <br />
                                <br />
                                <strong><?php echo HEADING_WRONG_PERMISSIONS; ?></strong>
                                <br />
                                <br />
                                <div class="error_field">
                                    <?php
                                    echo implode('<br />', $t_wrong_chmod_array);
                                    ?>
                                </div>
                                <br />
                                <a href="index.php?ftp=donw&language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient"><?php echo BUTTON_CHECK_PERMISSIONS; ?></a>
                                <br />
                                <br />
                                <?php
                            } else {
                                ?>

                                <strong><?php echo HEADING_WRONG_PERMISSIONS; ?></strong>
                                <br />
                                <br />
                                <div class="error_field">
                                    <?php
                                    echo implode('<br />', $t_wrong_chmod_array);
                                    ?>
                                </div>
                                <br />
                                <a href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient"><?php echo BUTTON_CHECK_PERMISSIONS; ?></a>
                                <br />
                                <br />
                                <br />
        <?php echo TEXT_SET_PERMISSIONS; ?>
                                <br />
                                <br />
                                <br />

                                <table class="block_head" width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="4%"><img src="images/icon-path.png" alt="" /></td>
                                        <td width="96%"><strong><?php echo HEADING_FTP_DATA; ?></strong></td>
                                    </tr>
                                </table>

                                <table width="620" border="0" cellspacing="5" cellpadding="0">
                                    <tr>
                                        <td width="120"><?php echo LABEL_FTP_SERVER; ?></td>
                                        <td width="500"><input type="text" class="input_field" name="FTP_HOST" size="35" value="<?php echo $_POST['FTP_HOST']; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td width="120"><?php echo LABEL_FTP_USER; ?></td>
                                        <td width="500"><input type="text" class="input_field" name="FTP_USER" size="35" value="<?php echo $_POST['FTP_USER']; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td width="120"><?php echo LABEL_FTP_PASSWORD; ?></td>
                                        <td width="500"><input type="text" class="input_field" name="FTP_PASSWORD" size="35" value="<?php echo $_POST['FTP_PASSWORD']; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td><label for="pasv"><?php echo LABEL_FTP_PASV; ?></label></td>
                                        <td><input type="checkbox" id="pasv" name="FTP_PASV" value="true" style="margin-left: 0"<?php echo (isset($_POST['FTP_PASV']) || empty($_POST)) ? ' checked="checked"' : ''; ?> /></td>
                                    </tr>
                                </table>

                                <?php
                                if (!isset($_POST['FTP_HOST'])) {
                                    ?>
                                    <br />
                                    <input type="submit" name="go" value="<?php echo BUTTON_CONNECT; ?>" class="button gradient green" />
                                    <?php
                                } else {
                                    ?>
                                    <br />
                                    <fieldset>
                                        <legend><?php echo HEADING_REMOTE_CONSOLE; ?></legend>
                                        <?php
                                        if ($coo_ftp_manager->v_error != '') {
                                            echo '<div class="error">' . $coo_ftp_manager->v_error . '</div>';
                                        } else {
                                            if (is_object($coo_ftp_manager) && $coo_ftp_manager->is_shop($t_dir)) {
                                                if (!isset($_POST['chmod_777']) || empty($_POST['chmod_777'])) {
                                                    echo '<input type="hidden" name="dir" value="' . $t_dir . '" />';
                                                    echo '<input type="submit" name="chmod_777" value="' . BUTTON_SET_PERMISSIONS . '" class="button gradient green" /><br /><br />';
                                                } else {
                                                    $coo_ftp_manager->chmod_777($t_dir);
                                                    echo '<script type="text/javascript">
                                                                <!--
                                                                self.location.href="index.php?ftp=done&language=' . rawurlencode($_GET['language']) . '";
                                                                //-->
                                                                </script>';
                                                }
                                            }

                                            if (isset($_POST['FTP_HOST']) && (!isset($_POST['chmod_777']) || empty($_POST['chmod_777']))) {
                                                if (strrpos($t_dir, '/') !== false && $t_dir != '/') {
                                                    if (strrpos($t_dir, '/') === 0) {
                                                        echo '<input type="submit" class="dir" name="dir" value="/" /> ' . LABEL_DIR_UP . '<br /><br />';
                                                    } else {
                                                        echo '<input type="submit" class="dir" name="dir" value="' . substr($t_dir, 0, strrpos($t_dir, '/')) . '" /> ' . LABEL_DIR_UP . '<br /><br />';
                                                    }
                                                }

                                                for ($i = 0; $i < count($t_list_array); $i++) {
                                                    echo '<input type="submit" class="dir" name="dir" value="' . $t_list_array[$i] . '" /><br />';
                                                }
                                            }
                                        }
                                        ?>
                                    </fieldset>
                                        <?php
                                    }

                                    if ($coo_ftp_manager->v_error != '' || (is_object($coo_ftp_manager))) {
                                        ?>
                                    <br />
                                    <input type="submit" name="go" value="<?php echo BUTTON_CONNECT_NEW; ?>" class="button gradient" />
                                        <?php
                                    }
                                    ?>

                                <?php
                            }
                            ?>
                        </div>
                            <?php
                        } else {
                            ?>
                        <table class="block_head server_data" width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="4%"><img src="images/icon-database.png" alt="" /></td>
                                <td width="96%"><strong><?php echo HEADING_DATABASE; ?></strong></td>
                            </tr>
                        </table>

                        <table class="server_data" width="750" border="0" cellspacing="5" cellpadding="0">
                            <tr>
                                <td width="16%"><?php echo LABEL_DB_SERVER; ?></td>
                                <td width="22%"><input type="text" class="input_field_short" name="DB_SERVER" size="15" value="localhost" /></td>
                                <td width="62%"><span class="input_error"><?php echo ERROR_INPUT_DB_CONNECTION; ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_DB_USER; ?></td>
                                <td colspan="2"><input type="text" class="input_field_short" name="DB_SERVER_USERNAME" size="15" /></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_DB_PASSWORD; ?></td>
                                <td colspan="2"><input type="text" class="input_field_short" name="DB_SERVER_PASSWORD" size="15" /></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_DB_DATABASE; ?></td>
                                <td><input type="text" class="input_field_short" name="DB_DATABASE" size="15" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_DB_DATABASE; ?></span></td>
                            </tr>
                        </table>
                        <br class="server_data" />
                        <br class="server_data" />
                        <table class="block_head server_data" width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="4%"><img src="images/icon-path.png" alt="" /></td>
                                <td width="96%"><strong><?php echo HEADING_SHOP_INFORMATION; ?></strong></td>
                            </tr>
                        </table>

                        <table class="server_data" width="750" border="0" cellspacing="5" cellpadding="0">
                            <tr>
                                <td width="16%"><?php echo LABEL_HTTP_SERVER; ?></td>
                                <td width="42%"><input type="text" class="input_field" name="HTTP_SERVER" size="35" value="<?php echo 'http://' . getenv('HTTP_HOST'); ?>" /></td>
                                <td width="42%"><span class="input_error"><?php echo ERROR_INPUT_SERVER_URL; ?></span></td>
                            </tr>
                            <tr>
                                <td><label for="ssl"><?php echo LABEL_SSL; ?></label></td>
                                <td colspan="2"><input type="checkbox" id="ssl" name="ENABLE_SSL" value="true" style="margin-left: 0" /></td>
                            </tr>
                            <tr class="https_server" style="display:none">
                                <td><?php echo LABEL_HTTPS_SERVER; ?></td>
                                <td><input type="text" class="input_field" name="HTTPS_SERVER" size="35" value="<?php echo 'https://' . getenv('HTTP_HOST'); ?>" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_SERVER_URL; ?></span></td>
                            </tr>

                            <tr>
                                <td><?php echo LABEL_DB_SALTKEY; ?></td>
                                <td><input type="text" class="input_field" name="SALT_KEY" size="25" value="<?php echo md5(mt_rand()); ?>" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_SALT_KEY; ?></span></td>
                            </tr>
                        </table>
                        <br class="server_data" />
                        <br class="server_data" />

                        <table class="block_head shop_data" width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="4%"><img src="images/icon-path.png" alt="" /></td>
                                <td width="96%"><strong><?php echo HEADING_ADMIN_DATA; ?></strong></td>
                            </tr>
                        </table>

                        <table class="shop_data" width="750" border="0" cellspacing="5" cellpadding="0">
                            <tr style="height: 32px">
                                <td width="16%"><?php echo LABEL_GENDER; ?></td>
                                <td width="42%"><input type="radio" value="m" name="GENDER" checked="checked" /> <?php echo LABEL_MALE; ?> <input type="radio" value="f" name="GENDER" /> <?php echo LABEL_FEMALE; ?></td>
                                <td width="42%"></td>
                            </tr>
                            <tr>
                                <td width="16%"><?php echo LABEL_FIRSTNAME; ?></td>
                                <td width="42%"><input type="text" class="input_field" name="FIRST_NAME" size="35" value="" /></td>
                                <td width="42%"><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_LASTNAME; ?></td>
                                <td><input type="text" class="input_field" name="LAST_NAME" size="35" value="" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_STREET; ?></td>
                                <td><input type="text" class="input_field" name="STREET_ADRESS" size="35" value="" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_5; ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_POSTCODE; ?></td>
                                <td><input type="text" class="input_field" name="POST_CODE" size="35" value="" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_4; ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_CITY; ?></td>
                                <td><input type="text" class="input_field" name="CITY" size="35" value="" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_STATE; ?></td>
                                <td id="states_container">
                                    <select name="STATE">
                                        <option value="81">Bremen</option>
                                    </select>
                                </td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_COUNTRY; ?></td>
                                <td id="countries_container">
                                    <select name="COUNTRY">
                                        <option value="81">Germany</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_TELEPHONE; ?></td>
                                <td><input type="text" class="input_field" name="TELEPHONE" size="35" value="" /></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_EMAIL; ?></td>
                                <td><input type="text" class="input_field" name="EMAIL_ADRESS" size="35" value="" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_EMAIL; ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_PASSWORD; ?></td>
                                <td><input type="password" class="input_field" name="PASSWORD" size="35" value="" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_6 ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_CONFIRMATION; ?></td>
                                <td><input type="password" class="input_field" name="PASSWORD_CONFIRMATION" size="35" value="" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_PASSWORD_CONFIRMATION; ?></span></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_SHOP_NAME; ?></td>
                                <td><input type="text" class="input_field" name="STORE_NAME" size="35" value="" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_3; ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_COMPANY; ?></td>
                                <td><input type="text" class="input_field" name="COMPANY" size="35" value="" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_USTID; ?></td>
                                <td><input type="text" class="input_field" name="USTID" size="35" value="" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo LABEL_EMAIL_FROM; ?></td>
                                <td><input type="text" class="input_field" name="EMAIL_ADRESS_FROM" size="35" value="" /></td>
                                <td><span class="input_error"><?php echo ERROR_INPUT_EMAIL; ?></span></td>
                            </tr>
                        </table>
                        <br class="shop_data" />
                        <br class="shop_data" />

                        <br class="robots_data" />
                        <table class="block_head robots_data" width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="4%"><img src="images/icon-path.png" alt="" /></td>
                                <td width="96%"><strong><?php echo HEADLINE_ROBOTS; ?></strong></td>
                            </tr>
                        </table>

                        <div class="progress">
                            <strong><?php echo HEADING_PROGRESS; ?></strong>
                            <p><?php echo TEXT_PROGRESS; ?></p>
                            <br />
                        </div>

                        <div id="ajax"></div>

                        <p class="robots_data">
    <?php echo TEXT_ROBOTS; ?>
                            <br />
                            <br />
                            <a class="button gradient" id="download" href="get_robots.php?download=robot"><?php echo BUTTON_DOWNLOAD; ?></a>
                            <br />
                            <br />
                        </p>
                        <div class="finish">
                            <br />
                            <strong class="finish"><?php echo HEADING_SUCCESS; ?></strong>
                            <p><?php echo TEXT_SUCCESS; ?></p>
                            <br />
                            <br />
                            <a class="button gradient green" href="<?php echo $dir_ws_www_root . '/'; ?>"><?php echo BUTTON_OPEN_SHOP; ?></a>
                        </div>

                        <a class="gradient button green server_data" id="import_sql"><?php echo BUTTON_START; ?></a>
                        <a class="gradient button green shop_data" id="run_config"><?php echo BUTTON_FINISH; ?></a>
                        <br>
                        <br>


    <?php
    echo xtc_draw_hidden_field_installer('install[]', 'database');
    echo xtc_draw_hidden_field_installer('install[]', 'configure');
    echo xtc_draw_hidden_field_installer('DIR_FS_DOCUMENT_ROOT', $dir_fs_www_root);
    echo xtc_draw_hidden_field_installer('DIR_FS_CATALOG', $local_install_path);
    echo xtc_draw_hidden_field_installer('DIR_FS_ADMIN', $local_install_path . 'admin/');
    echo xtc_draw_hidden_field_installer('DIR_WS_CATALOG', $dir_ws_www_root . '/');
    echo xtc_draw_hidden_field_installer('DIR_WS_ADMIN', $dir_ws_www_root . '/admin/');
    echo xtc_draw_hidden_field_installer('STORE_SESSIONS', 'mysql');
    echo xtc_draw_hidden_field_installer('ZONE_SETUP', 'yes');
    echo xtc_draw_hidden_field_installer('STATUS_DISCOUNT', '0.00');
    echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT_FLAG', '0');
    echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT', '0.00');
    echo xtc_draw_hidden_field_installer('STATUS_GRADUATED_PRICE', '1');
    echo xtc_draw_hidden_field_installer('STATUS_SHOW_PRICE', '1');
    echo xtc_draw_hidden_field_installer('STATUS_SHOW_TAX', '1');
    echo xtc_draw_hidden_field_installer('STATUS_DISCOUNT2', '0.00');
    echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT_FLAG2', '0');
    echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT2', '0.00');
    echo xtc_draw_hidden_field_installer('STATUS_GRADUATED_PRICE2', '1');
    echo xtc_draw_hidden_field_installer('STATUS_SHOW_PRICE2', '1');
    echo xtc_draw_hidden_field_installer('STATUS_SHOW_TAX2', '1');
    ?>
                        <?php
                    }
                    ?>

                </form>

                <footer>
                    <strong><a href="http://www.commerce-seo.de" target="_blank"><strong>commerce:SEO v2next</strong></a> - Installer 2014</strong><br />
                    commerce:SEO provides no warranty. The Shopsoftware is <br />
                    redistributable under the <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU General Public License (Version 2)</a>
                </footer>

            </div>

        </div>

    </body>
</html>
<?php
if (is_object($coo_ftp_manager)) {
    $coo_ftp_manager->quit();
}
@mysql_close();
?>