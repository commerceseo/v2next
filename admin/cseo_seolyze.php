<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_seolyze.php 1471 2015-07-22 20:34:59Z akausch $
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

require('includes/application_top.php');
$coo_text_mgr = new LanguageTextManager('cseo_seolyze', $_SESSION['languages_id']);
$smarty = new Smarty;
$smarty->assign('txt', $coo_text_mgr->v_section_content_array['cseo_seolyze']);

$configuration_query = xtc_db_query("SELECT * FROM cseo_configuration WHERE cseo_group_id = '2' ORDER BY cseo_sort_order;");
if (xtc_db_num_rows($configuration_query) == 0) {
    xtc_db_query("INSERT INTO cseo_configuration (cseo_key, cseo_group_id, cseo_sort_order) VALUES ('SEOLYZEEMAIL', 2, 1);");
    xtc_db_query("INSERT INTO cseo_configuration (cseo_key, cseo_group_id, cseo_sort_order) VALUES ('SEOLYZEVORNAME', 2, 2);");
    xtc_db_query("INSERT INTO cseo_configuration (cseo_key, cseo_group_id, cseo_sort_order) VALUES ('SEOLYZENACHNAME', 2, 3);");
    xtc_db_query("INSERT INTO cseo_configuration (cseo_key, cseo_group_id, cseo_sort_order) VALUES ('SEOLYZETELE', 2, 4);");
    xtc_db_query("INSERT INTO cseo_configuration (cseo_key, cseo_group_id, cseo_sort_order) VALUES ('SEOLYZEAPIKEY', 2, 5);");
    xtc_redirect('cseo_seolyze.php');
}

function seolyze($email, $vname, $nname, $tele) {
    /**
     * @author Philipp Helminger MA - EP-Solutions.at
     * @copyright 2014
     */
    $post_vars = array();
    $post_vars["code"] = "commerceSeo!x6-37X";
    $post_vars["email"] = $email;
    $post_vars["vorname"] = $vname;
    $post_vars["nachname"] = $nname;
    $post_vars["tele"] = $tele;
    if (function_exists('curl_init')) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, "https://www.seolyze.com/createNewUserExtern.php");/** API URL * */
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $post_vars);
        curl_setopt($c, CURLOPT_ENCODING, "UTF-8");
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_TIMEOUT, 10);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        $ans = curl_exec($c);
        curl_close($c);


        /**
          die Response ist JSON encoded
          die Response ist IMMER ein array mit den beiden stellen 'error' und 'success'
         * */
        $data = json_decode($ans);
// echo '<pre>';
// print_r($data->error);
// echo '</pre>';
// echo $data->success->keyExtern;

        xtc_db_query("UPDATE cseo_configuration SET cseo_value = '" . $data->success->keyExtern . "' WHERE cseo_key = 'SEOLYZEAPIKEY'");
        $error = false;
        $messageStack = new messageStack();
        foreach ($data->error as $errorout) {
            if ($errorout == 'noEmail') {
                $error = true;
                $messageStack->add_session(ERROR_NOEMAIL, 'error');
            }
            if ($errorout == 'emailNotValid') {
                $error = true;
                $messageStack->add_session(ERROR_NOVALIDEMAIL, 'error');
            }
            if ($errorout == 'emailExists') {
                $error = true;
                $messageStack->add_session(ERROR_EMAILEXITS, 'error');
            }
            if ($errorout == 'noTele') {
                $error = true;
                $messageStack->add_session(ERROR_NOTEL, 'error');
            }
            if ($error) {
                xtc_redirect('cseo_seolyze.php');
            }
        }
    }
    return $messageStack;
}

if ($_GET['action']) {
    switch ($_GET['action']) {
        case 'save':
            xtc_db_query("UPDATE cseo_configuration SET cseo_value = '" . xtc_db_prepare_input($_POST['SEOLYZEEMAIL']) . "' WHERE cseo_key = 'SEOLYZEEMAIL'");
            xtc_db_query("UPDATE cseo_configuration SET cseo_value = '" . xtc_db_prepare_input($_POST['SEOLYZEVORNAME']) . "' WHERE cseo_key = 'SEOLYZEVORNAME'");
            xtc_db_query("UPDATE cseo_configuration SET cseo_value = '" . xtc_db_prepare_input($_POST['SEOLYZENACHNAME']) . "' WHERE cseo_key = 'SEOLYZENACHNAME'");
            xtc_db_query("UPDATE cseo_configuration SET cseo_value = '" . xtc_db_prepare_input($_POST['SEOLYZETELE']) . "' WHERE cseo_key = 'SEOLYZETELE'");
            if ($_POST['SEOLYZEAPIKEY'] == '') {
                seolyze(xtc_db_prepare_input($_POST['SEOLYZEEMAIL']), xtc_db_prepare_input($_POST['SEOLYZEVORNAME']), xtc_db_prepare_input($_POST['SEOLYZENACHNAME']), xtc_db_prepare_input($_POST['SEOLYZETELE']));
            } else {
                xtc_db_query("UPDATE cseo_configuration SET cseo_value = '" . xtc_db_prepare_input($_POST['SEOLYZEAPIKEY']) . "' WHERE cseo_key = 'SEOLYZEAPIKEY'");
            }
            xtc_redirect('cseo_seolyze.php');
            break;
    }
}

require(DIR_WS_INCLUDES . 'header.php');
echo '
<style>
iframe {
  min-height:800px;
  height:auto !important;
  height:100%;
}
</style>
';
echo xtc_draw_form('configuration', 'cseo_seolyze.php', 'action=save');

$test_query = xtc_db_fetch_array(xtc_db_query("SELECT cseo_value FROM cseo_configuration WHERE cseo_key = 'SEOLYZEAPIKEY' LIMIT 1;"));
$configuration_query = xtc_db_query("SELECT * FROM cseo_configuration WHERE cseo_group_id = '2' ORDER BY cseo_sort_order;");
if ($test_query['cseo_value'] == '') {
    echo '<div>';
    echo '<h2>' . SEOLYZEEINGANG . '</h2>';
    echo '<table class="table table-bordered table-striped">';
    while ($seolyze = xtc_db_fetch_array($configuration_query)) {
        echo '<tr>';
        echo '<td>';
        echo constant(strtoupper($seolyze['cseo_key'] . '_TITLE'));
        echo '</td>';
        echo '<td>';
        echo xtc_draw_input_field($seolyze['cseo_key'], $seolyze['cseo_value']);
        echo '</td>';
        echo '<td>';
        echo constant(strtoupper($seolyze['cseo_key'] . '_HELP'));
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<button type="submit" class="btn btn-success">' . BUTTON_SAVE . '</button>';
    echo SEOHINWEIS;
    echo '<div class="panel-footer">In Kooperation mit <a href="https://www.commerce-seo.de/seolyze/" target="_blank">SEOLYZE</a></div>';
    echo '</div>';
    echo '</form>';
} else {
    echo '<div class="pull-right">';
    echo '<button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#seolyze" aria-expanded="true" aria-controls="seolyze">';
    echo BUTTONCONFIG;
    echo '</button>';
    echo '</div>';
    echo '<div id="seolyze" class="collapse">';
    echo '<table class="table table-bordered table-striped">';
    while ($seolyze = xtc_db_fetch_array($configuration_query)) {
        echo '<tr>';
        echo '<td>';
        echo constant(strtoupper($seolyze['cseo_key'] . '_TITLE'));
        echo '</td>';
        echo '<td>';
        echo xtc_draw_input_field($seolyze['cseo_key'], $seolyze['cseo_value']);
        echo '</td>';
        echo '<td>';
        echo constant(strtoupper($seolyze['cseo_key'] . '_HELP'));
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<button type="submit" class="btn btn-success">' . BUTTON_SAVE . '</button>';
    echo '<div class="panel-footer">In Kooperation mit <a href="https://www.seolyze.com/Ref/Commerce-Seo/" target="_blank">SEOLYZE</a></div>';
    echo '</div>';
    echo '</form>';
}
$configuration_query = xtc_db_fetch_array(xtc_db_query("SELECT * FROM cseo_configuration WHERE cseo_key = 'SEOLYZEAPIKEY' LIMIT 1;"));
if ($configuration_query['cseo_value'] != '') {
    echo '<iframe width="100%" src="https://www.seolyze.com/Extern/WDF-IDF/User/' . $configuration_query['cseo_value'] . '/" frameborder="0" allowfullscreen></iframe>';
}
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
