<?php
/* --------------------------------------------------------------
 * 	$Id: hermes_list.php 879 2014-03-26 17:22:54Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------

  based on:
  hermes_collection.php 2012 gambio
  Gambio GmbH
  http://www.gambio.de
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommercecoding standards (a typical file) www.oscommerce.com
  (c) 2003	 nextcommerce ( start.php,v 1.6 2003/08/19); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: hermes_list.php 879 2014-03-26 17:22:54Z akausch $)


  Released under the GNU General Public License
  -------------------------------------------------------------- */

ob_start();
require('includes/application_top.php');
require DIR_FS_CATALOG . '/admin/includes/classes/class.messages.php';
require DIR_FS_CATALOG . '/includes/classes/class.hermes.php';

defined('GM_HTTP_SERVER') OR define('GM_HTTP_SERVER', HTTP_SERVER);
define('PAGE_URL', GM_HTTP_SERVER . DIR_WS_ADMIN . basename(__FILE__));

$hermes = new HermesAPI();
$messages = new Messages('hermes_messages');

if (isset($_GET['showbatch'])) {
    $labelsfile = $hermes->makeLabelsFileName();
    if (file_exists($labelsfile)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename=hermes_batch_' . time() . '.pdf');
        readfile($labelsfile);
        exit;
    } else {
        xtc_redirect(PAGE_URL);
    }
}


if (isset($_REQUEST['loadlist'])) {
    ob_clean();
    if ($hermes->getService() !== 'ProPS') {
        echo '<p>' . $hermes->get_text('feature_exclusive_to_props') . '</p>';
        exit;
    }
    $start = microtime(true);
    $propsorders = $hermes->getPropsOrders();
    if (is_array($propsorders) && isset($propsorders['code']) && isset($propsorders['message'])) {
        echo '<p class="message">' . $propsorders['code'] . ' ' . $propsorders['message'] . '</p>';
        ob_flush();
        exit;
    }
    echo '<br><form action="" method="post" id="batchlabels">';
    echo '<button class="button" id="sel_all_top">##select_all</button>';
    echo '<button class="button" id="sel_none_top">##select_none</button>';
    echo '<button class="button" id="sel_unprinted_top">##select_unprinted</button><br>';
    echo '<input class="button btn_wide" type="submit" value="##get_labels_for_selected_orders">';
    echo '<button class="button" id="refresh_top" style="float:right">##refresh</button>';
    echo '</form>';
    ?>
    <table class="propsorders" id="propsorders">
        <tr>
            <th>&nbsp;</th>
            <th>##orderno</th>
            <th>##barcode</th>
            <th title="##date_order_created">##date_created</th>
            <th>##parcel_class</th>
            <th>##status</th>
            <th>##receiver</th>
            <th>&nbsp;</th>
        </tr>
        <?php
        foreach ($propsorders as $po) {
            try {
                $ho = new HermesOrder($po->orderNo);
                $ho_ordersid = $ho->orders_id;
            } catch (Exception $e) {
                // order not found
                $ho_ordersid = false;
            }
            $labelurl = $hermes->getLabelUrl($ho);
            echo '<tr>';
            echo '<td><input type="checkbox" name="selected[' . $po->orderNo . ']" value="' . $po->orderNo . '" class="orderselect"></td>';
            echo '<td class="orderno">';
            if ($ho_ordersid !== false) {
                echo '<a href="' . xtc_href_link('hermes_order.php', 'orderno=' . $po->orderNo . '&orders_id=' . $ho_ordersid) . '">' . $po->orderNo . '</a>';
            } else {
                echo $po->orderNo;
            }
            echo '</td>';
            echo '<td class="shippingid" title="##click_for_shipment_status"><span class="sid">' . $po->shippingId . '</span><div class="sstatus"></div></td>';
            echo '<td>' . $po->creationDate . '</td>';
            echo '<td>' . $po->parcelClass . '</td>';
            echo '<td class="status">' . $po->status . ' ' . $po->status_text . '</td>';
            echo '<td>' . $po->lastname . ', ' . $po->firstname . "<br>" . $po->postcode . ' ' . $po->city . ' (' . $po->countryCode . ')</td>';
            if ($ho_ordersid !== false) {
                echo '<td>';
                echo '<form action="' . xtc_href_link('hermes_order.php') . '" method="post" class="orderlabel">';
                echo '<input type="hidden" name="orderno" value="' . $po->orderNo . '">';
                echo '<input type="submit" name="orderprintlabel" value="##retrieve_label">';
                echo '<div class="printpos">
							<input type="radio" name="printpos" value="1" title="##position 1" checked="checked">
							<input type="radio" name="printpos" value="2" title="##position 2"><br>
							<input type="radio" name="printpos" value="3" title="##position 3">
							<input type="radio" name="printpos" value="4" title="##position 4">
						</div>';
                echo '</form>';
                echo '</td>';
            } else {
                echo '<td></td>';
            }
            echo '</tr>';
        }
        echo "</table>";
        echo '<br><form action="" method="post" id="batchlabels">';
        echo '<button class="button" id="sel_all">##select_all</button>';
        echo '<button class="button" id="sel_none">##select_none</button>';
        echo '<button class="button" id="sel_unprinted">##select_unprinted</button><br>';
        echo '<input class="button btn_wide" type="submit" value="##get_labels_for_selected_orders">';
        echo '<button class="button" id="refresh" style="float:right">##refresh</button>';
        echo '</form>';
        echo $hermes->replaceTextPlaceholders(ob_get_clean());
        // xtc_db_close();
        exit;
    }

    if (isset($_REQUEST['shipmentstatus'])) {
        ob_clean();
        $shipping_id = $_REQUEST['shipmentstatus'];
        try {
            $sstatus = $hermes->getShipmentStatus($shipping_id);
            echo $sstatus['text'] . '<br>' . $sstatus['datetime'];
        } catch (Exception $e) {
            echo $hermes->get_text('status_could_not_be_determined');
        }
        ob_flush();
        exit;
    }

    if (!empty($_POST['selected'])) {
        $labelsreturn = $hermes->getLabelsPdf($_POST['selected']);
        if ($labelsreturn !== false && !empty($labelsreturn['pdfdata'])) {
            file_put_contents($hermes->makeLabelsFileName(), $labelsreturn['pdfdata']);
            foreach ($labelsreturn['orderres']->OrderResponse as $or) {
                $eitems = (array) ($or->exceptionItems);
                if (!empty($eitems)) {
                    $messages->addMessage(sprintf($hermes->get_text('label_for_orderno_could_not_be_created'), $or->orderNo));
                }
            }
            xtc_redirect(HTTP_SERVER . DIR_WS_ADMIN . basename(__FILE__) . '?showbatch=1');
            exit;
        } else {
            header('Content-Type: text/plain');
            die(print_r($_POST, true));
        }
    }

    if (!empty($_REQUEST['messages'])) {
        foreach ($messages->getMessages() as $msg) {
            echo '<p class="message">' . $msg . '</p>';
        }
        $messages->reset();
        exit;
    }


    /* messages */
    $session_messages = $messages->getMessages();
    $messages->reset();



    require_once(DIR_WS_INCLUDES . 'header.php');
    ?>

    <style>
        .hermesorder { font-family: sans-serif; font-size: 0.8em; }
        .hermesorder h1 { padding: 0; }
        .hermesorder a:link { font-size: inherit; text-decoration: underline; }
        .propsorders { background: #eeeeee; width: 100%; margin: auto; border-collapse: collapse; margin: 1em 0; }
        .propsorders td { }
        .propsorders td, .propsorders th { padding: .1ex .5ex; }
        .propsorders td.shippingid { cursor: pointer; width: 8em; }
        .propsorders th { background: #ccc; }
        .propsorders tr:hover { background: #ffffee !important; }
        .propsorders tr:nth-child(even) { background: #ddd; }
        .availability { float: right; width: 25em; border: 1px solid #555; background: #eee; padding: 1ex 1em; }
        .printpos { display: inline-block; margin-bottom: -4px; }
        .printpos input { vertical-align: middle; margin: 0; }
        .orderlabel * { vertical-align: middle; }
        p.message { background: #ffa; border: 1px solid #faa; padding: 1ex 1em; }
        button, input[type="submit"] { font-size: 1.0em; }
    </style>

    <!-- body //-->
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
        <tr>
            <td class="boxCenter hermesorder" width="100%" valign="top">
                <div class="availability">
                    ##checking_availability
                </div>

                <div id="messages">
                    <?php foreach ($session_messages as $msg): ?>
                        <p class="message"><?php echo $msg ?></p>
                    <?php endforeach ?>
                </div>

                <h2>##recorded_orders</h2>
                <p>##note_90days_max500</p>

                <div id="propsorders">
                    ##loading
                </div>

            </td>

        </tr>
    </table>

    <script>
        $(function() {
            $('a.newwindow').click(function(e) {
                e.preventDefault();
                window.open($(this).attr('href'));
            });
            $('.confirm').click(function(e) {
                return window.confirm('##really_delete');
            });

            $('.availability').load('hermes_order.php', {'ajax': 'checkavailability'}, function() {
                var afterlistload = function() {
<?php if (isset($_GET['showbatch'])): ?>
                        window.location = '<?php echo $hermes->getLabelsUrl() ?>';
<?php endif ?>
                }
                if ($('span.available').length > 0) {
                    $('#propsorders').load('hermes_list.php', {'loadlist': 1}, afterlistload);
                }
                else {
                    $('#propsorders').html('##cannot_retrieve_data');
                }
            });

            $('td.shippingid').live('click', function(e) {
                var sid = $('span.sid', this).text();
                var orderno = $('.orderno', $(this).parent()).text();
                if (sid != '') {
                    $('div.sstatus', this).text('##loading_tracking_data');
                    $('div.sstatus', this).load('hermes_list.php', {'shipmentstatus': sid});
                }
            });

            $('#propsorders input.orderselect').live('change', function(e) {
                if ($('#propsorders input.orderselect:checked').length > 40) {
                    alert('##max_40_orders');
                    $(this).removeAttr('checked');
                }
            });

            $('#batchlabels').live('submit', function(e) {
                $('#propsorders input.orderselect:checked').each(function() {
                    var orderno = $(this).val();
                    $('#batchlabels').prepend($('<input type="hidden" name="selected[]" value="' + orderno + '">'));
                    setTimeout(function() {
                        $('#propsorders').load('hermes_list.php', {'loadlist': 1});
                    }, 10);
                });
            });

            $('#sel_all, #sel_all_top').live('click', function(e) {
                e.preventDefault();
                $('.propsorders input[type="checkbox"]').attr('checked', 'checked');
            });

            $('#sel_none, #sel_none_top').live('click', function(e) {
                e.preventDefault();
                $('.propsorders input[type="checkbox"]').removeAttr('checked');
            });

            $('#sel_unprinted, #sel_unprinted_top').live('click', function(e) {
                e.preventDefault();
                var count = 0;
                $('.propsorders tr').each(function() {
                    var status = $('td.status', this).text();
                    var status_no = status.replace(/(-?\d+).*/, '$1');
                    switch (status_no) {
                        case '2':
                        case '4':
                            $('input[type="checkbox"]', this).attr('checked', 'checked');
                            break;
                    }
                });
            });

            $('#refresh, #refresh_top').live('click', function(e) {
                e.preventDefault();
                $('#propsorders').html('##refreshing');
                $('#propsorders').load('hermes_list.php', {'loadlist': 1});
            });
        });
    </script>

    <?php
    echo $hermes->replaceTextPlaceholders(ob_get_clean());
    require(DIR_WS_INCLUDES . 'footer.php');
    require(DIR_WS_INCLUDES . 'application_bottom.php');
    