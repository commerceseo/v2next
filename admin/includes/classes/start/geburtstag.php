<table class="dataTable" width="100%">
    <tr class="dataTableHeadingRow">
        <th class="dataTableHeadingContent" height="20" width="50%">Name</th>
        <th class="dataTableHeadingContent" height="20" width="50%">Geburstag</th>
    </tr>
    <?php
    $ergebnis = xtc_db_query("SELECT customers_firstname,customers_lastname,customers_dob FROM customers WHERE customers_dob != '0000-00-00 00:00:00' ORDER BY customers_dob");
    $i = 0;
    while ($row = mysql_fetch_object($ergebnis)) {
        $gebdat = strtotime($row->customers_dob);
        $gebjahr = date('Y', $gebdat);
        $gebmonat = date('n', $gebdat);
        $gebtag = date('j', $gebdat);
        if ($gebmonat == date('n') and $gebtag == date('j')) {
            ?>
            <tr>
                <td width="78%" bgcolor="#FFF9E9">
                    <?php echo $row->customers_firstname . " " . $row->customers_lastname; ?>
                </td>
                <td width="22%" bgcolor="#FFF9E9">
                    <?php echo xtc_date_long($row->customers_dob); ?>
                </td>
            </tr>
        <?php
        }
        if ($gebmonat == date('n') and $gebtag > date('j')) {
            //(nur zwischenspeichern und nach der schleife ausgeben)
            $geb_bald[$i][0] = $row->customers_firstname;
            $geb_bald[$i][1] = $row->customers_lastname;
            $geb_bald[$i][2] = $row->customers_dob;
            $i++;
        }
    }
    $anzahl = count($geb_bald);
    if ($anzahl > 0) {
        ?>
        <tr>
            <td width="100%" colspan="2" style="border-top: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC" bgcolor="#F1F1F1">
                <b>Kunden, die noch in diesem Monat Geburtstag haben:</b>
            </td>
        </tr>
        <?php
        $anzahl = count($geb_bald);
        for ($i = 0; $i < $anzahl; $i++) {
            $geb_bald_sort[$i][0] = $geb_bald[$i][0];
            $geb_bald_sort[$i][1] = $geb_bald[$i][1];
            $geb_bald_sort[$i][2] = substr($geb_bald[$i][2], 8, 2);
        }
        if ($anzahl > 0)
            array($geb_bald_sort, 2);

        for ($i = 0; $i < $anzahl; $i++) {
            for ($a = 0; $a < $anzahl; $a++) {
                if (($geb_bald_sort[$i][0] == $geb_bald[$a][0]) && ($geb_bald_sort[$i][1] == $geb_bald[$a][1]))
                    break;
            }
            $geb_bald_sort[$i][2] = $geb_bald[$a][2];
        }

        for ($i = 0; $i < $anzahl; $i++) {
            ?>
            <tr>
                <td width="78%" bgcolor="#F9F0F1" style="border-bottom: 1px dotted #000000">
                    <?php echo $geb_bald_sort[$i][0] . ' ' . $geb_bald_sort[$i][1]; ?>
                </td>
                <td width="22%" bgcolor="#F9F0F1" style="border-bottom: 1px dotted #000000">
            <?php echo xtc_date_long($geb_bald_sort[$i][2]); ?>
                </td>
            </tr>
    <?php } ?>
    </table><br />
    <?php
    unset($geb_bald);
}
if (ACTIVATE_GIFT_SYSTEM == 'true') {
    echo xtc_draw_form('mail', FILENAME_GV_MAIL, 'action=send_email_to_user&from=admin_start') .
    xtc_draw_hidden_field('from', EMAIL_FROM) .
    xtc_draw_hidden_field('subject', 'Geburtstagsgr&uuml;&szlig;e von ' . STORE_NAME) .
    xtc_draw_hidden_field('message', 'Das ist der Text in der Mail');

    if ($_GET['from'] == 'gv_send') {
        $messageStack->add('Die Mail wurde erfolgreich versendet.', 'success');
    }
    $select = ',customers_email_address';
}
?>
<table class="dataTable" width="100%">
    <tr class="dataTableHeadingRow">
        <th class="dataTableHeadingContent" height="20" width="23%">Name</th>
        <th class="dataTableHeadingContent" height="20" width="23%">Alter</th>
        <?php if (ACTIVATE_GIFT_SYSTEM == 'true') { ?>
            <th class="dataTableHeadingContent" height="20" width="23%">Gutschein versenden?</th>
            <th class="dataTableHeadingContent" height="20" width="23%">&nbsp;</th>
    <?php } ?>
    </tr>
    <?php
    $dob_query = xtc_db_query("SELECT customers_dob,
												customers_firstname,
												customers_lastname,
												customers_gender" . $select . "
												FROM customers
												WHERE customers_dob
												LIKE '%-" . date('m') . "-" . date('d') . "%'");
    $rows = 1;
    if (xtc_db_num_rows($dob_query)) {
        while ($dob = xtc_db_fetch_array($dob_query)) {
            $jahr = substr($dob['customers_dob'], 0, 4);
            $monat = substr($dob['customers_dob'], 5, 2);
            $tag = substr($dob['customers_dob'], 8, 2);

            $jetzt = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $geburt = mktime(0, 0, 0, $monat, $tag, $jahr);
            $age = intval(($jetzt - $geburt) / (3600 * 24 * 365));

            if ($rows % 2 == 0)
                $f = 'dataTableRow';
            else
                $f = '';
            ?>
            <tr class="<?php echo $f; ?>" onmouseover="this.className = 'dataTableRowOver';
                    this.style.cursor = 'pointer'" onmouseout="this.className = '<?php echo $f; ?>'">
                <td class="dataTableContent">
                    <?php
                    if ($dob['customers_gender'] == 'm')
                        echo 'Herr ';
                    else
                        echo 'Frau ';
                    echo $dob['customers_firstname'] . ' ' . $dob['customers_lastname'] . "\n";
                    ?>
                </td>
                <td class="dataTableContent">
                    wird heute <?php echo $age . " Jahre alt"; ?>
                </td>
                    <?php if (ACTIVATE_GIFT_SYSTEM == 'true') { ?>
                    <td class="dataTableContent" align="right">
                        Gutscheinwert: <?php
                        echo xtc_draw_input_field('amount', '', 'size="3"');
                        echo xtc_draw_hidden_field('customers_email_address', $dob['customers_email_address']);
                        ?>
                    </td>
                    <td align="center">
                <?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SEND_EMAIL . '"/>'; ?>
                    </td>
            <?php } ?>
            </tr>
        <?php
        $rows++;
    }
} else {
    echo '<tr><td colspan="2" align="center">Heute hat niemand Geburtstag</td></tr>';
}
?>

</table>
</form>