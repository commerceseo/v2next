<?php
/* -----------------------------------------------------------------
 * 	ID:						general.js.php
 * 	Letzter Stand:			v2.2 R365
 * 	zuletzt geaendert von:	akausch
 * 	Datum:					2012/07/03
 *
 * 	Copyright (c) since 2010 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

if (GENERAL_SCRIPT_ADDON != '') {
    echo GENERAL_SCRIPT_ADDON;
}
	
if(strstr($_REQUEST['linkurl'], substr(FILENAME_CHECKOUT, 0, -5)) || 
	strstr($_REQUEST['linkurl'], substr('checkout.php', 0, -5)) || 
	strstr($PHP_SELF, substr(FILENAME_CHECKOUT, 0, -5)) || 
	strstr($PHP_SELF, substr('checkout.php', 0, -5)) ||
	strstr($_REQUEST['linkurl'], substr(FILENAME_ADDRESS_BOOK_PROCESS, 0, -5)) || 
	strstr($_REQUEST['linkurl'], substr('address_book_process.php', 0, -5)) || 
	strstr($PHP_SELF, substr(FILENAME_ADDRESS_BOOK_PROCESS, 0, -5)) || 
	strstr($PHP_SELF, substr('address_book_process.php', 0, -5)) ||
	strstr($_REQUEST['linkurl'], substr(FILENAME_LOGIN, 0, -5)) || 
	strstr($_REQUEST['linkurl'], substr('login.php', 0, -5)) || 
	strstr($PHP_SELF, substr(FILENAME_LOGIN, 0, -5)) || 
	strstr($PHP_SELF, substr('login.php', 0, -5)) ||
	strstr($_REQUEST['linkurl'], substr(FILENAME_CREATE_ACCOUNT, 0, -5)) || 
	strstr($_REQUEST['linkurl'], substr('create_account.php', 0, -5)) || 
	strstr($PHP_SELF, substr(FILENAME_CREATE_ACCOUNT, 0, -5)) || 
	strstr($PHP_SELF, substr('create_account.php', 0, -5)) ||
	strstr($_REQUEST['linkurl'], substr(FILENAME_CREATE_GUEST_ACCOUNT, 0, -5)) || 
	strstr($_REQUEST['linkurl'], substr('create_guest_account.php', 0, -5)) || 
	strstr($PHP_SELF, substr(FILENAME_CREATE_GUEST_ACCOUNT, 0, -5)) || 
	strstr($PHP_SELF, substr('create_guest_account.php', 0, -5))) {
echo '
<script type="text/javascript">
	head.ready(function(){
		jQuery("select#country").change(function(){
			var value = jQuery("select#country").val();
			jQuery.ajax({
			  type: "GET",
			  url: "getCountry.php",
			  data: "land=" + value,
			  cache: false,
			  success: function(html){
				jQuery("#state").html(html);
			  },
			  beforeSend: function(){
				jQuery("#state").html("<p style=\'width:198px\' align=\'center\'><img src=\'images/wait.gif\' alt=\'\' /></p>");
			  }
			});
		});
	});
</script>';

echo '
<script type="text/javascript">
function passwordStrength(password){
	var desc = new Array();
	desc[0] = "'.text_pw_secure_0.'";
	desc[1] = "'.text_pw_secure_1.'";
	desc[2] = "'.text_pw_secure_2.'";
	desc[3] = "'.text_pw_secure_3.'";
	desc[4] = "'.text_pw_secure_4.'";
	desc[5] = "'.text_pw_secure_5.'";

	var score   = 1;
	if (password.length > 6) score++;
	if ((password.match(/[a-z]/)) && (password.match(/[A-Z]/))) score++;
	if (password.match(/\d+/)) score++;
	if (password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) )	score++;
	if (password.length > 12) score++;
	document.getElementById("passwordDescription").innerHTML = desc[score];
	document.getElementById("passwordStrength").className = "strength" + score;
}
</script>
';
}

if ($_SESSION['SPECIAL_DATE'] != '' && PRODUCT_DETAILS_SPECIALS_COUNTER == 'true') {
echo '<script type="text/javascript">
        head.ready(function() {
            jQuery(\'div#clock\').countdown("'.$_SESSION['SPECIAL_DATE'].'", function(event) {
                var $this = $(this);
                switch (event.type) {
                    case "seconds":
                    case "minutes":
                    case "hours":
                    case "days":
                    case "weeks":
                    case "daysLeft":
                        $this.find(\'span#\' + event.type).html(event.value);
                        break;
                    case "finished":
                        $this.hide();
                        break;
                }
            });
        });
    </script>';

}

?>


<?php
	if(strstr($_REQUEST['linkurl'], substr(FILENAME_CHECKOUT, 0, -5)) || strstr($_REQUEST['linkurl'], substr('checkout.php', 0, -5)) || strstr($PHP_SELF, substr(FILENAME_CHECKOUT, 0, -5)) || strstr($PHP_SELF, substr('checkout.php', 0, -5))) {
    ?>
    <script type="text/javascript">

        function hideFromStart(id) {
            jQuery('#chkt_' + id).hide();
            jQuery('#btn_' + id).html('+');
        }


    <?php if (CHECKOUT_SHOW_SHIPPING_MODULES != 'true') { ?>
            hideFromStart('shipping_modules');
    <?php } ?>
    <?php if (CHECKOUT_SHOW_PAYMENT_MODULES != 'true') { ?>
            hideFromStart('payment_modules');
    <?php } ?>
    <?php if (CHECKOUT_SHOW_SHIPPING_ADDRESS != 'true') { ?>
            hideFromStart('shipping_address');
    <?php } ?>
    <?php if (CHECKOUT_SHOW_PAYMENT_ADDRESS != 'true') { ?>
            hideFromStart('payment_address');
    <?php } ?>
    <?php if (CHECKOUT_SHOW_COMMENTS != 'true') { ?>
            hideFromStart('comments');
    <?php } ?>
    <?php if (CHECKOUT_SHOW_AGB != 'true') { ?>
            hideFromStart('agb');
    <?php } ?>
    <?php if (CHECKOUT_SHOW_REVOCATION != 'true') { ?>
            hideFromStart('revocation');
    <?php } ?>
    <?php if (CHECKOUT_SHOW_DSG != 'true' && DISPLAY_DATENSCHUTZ_ON_CHECKOUT == 'true') { ?>
            hideFromStart('dsg');
    <?php } ?>
    <?php if (CHECKOUT_SHOW_PRODUCTS != 'true') { ?>
            hideFromStart('products');
    <?php } ?>

    </script>
    <?php
}
?>

<?php
//iPayment Beginn
	if(strstr($_REQUEST['linkurl'], substr('checkout_ipayment.php', 0, -5)) || strstr($PHP_SELF, substr('checkout_ipayment.php', 0, -5))) {
    ?>
    <script type="text/javascript">
		head.ready(function(){if(typeof(ipayment_silentmode)=='number'&&ipayment_silentmode==0){$('#ipayment_form').submit()}$('select#ipay_addr_country').change(function(e){var country=$(this).val();if(country=='US'||country=='CA'){$('select.usca_only').show();$('select.usca_only').removeAttr('disabled');if(country=='US'){$('option.usa_only').show();$('option.canada_only').hide()}if(country=='CA'){$('option.canada_only').show();$('option.usa_only').hide()}$('select#ipay_addr_state').val($('select#ipay_addr_state option:visible').first().val())}else{$('select.usca_only').hide();$('select.usca_only').attr('disabled','disabled')}});$('select#ipay_addr_country').change();$('#ipay_cc_typ').change(function(e){$('div.solo_only').hide();var card=$(this).val();if(card=='SoloCard'||card=='MaestroCard'){$('div.solo_only').show()}});$('#ipay_cc_typ').change()});
		head.ready(function() {
			if(typeof(ipayment_silentmode) == 'number' && ipayment_silentmode == 0) {
				$('#ipayment_form').submit();
			}

			$('select#ipay_addr_country').change(function(e) {
				var country = $(this).val();
				if(country == 'US' || country == 'CA') {
					$('select.usca_only').show();
					$('select.usca_only').removeAttr('disabled');
					if(country == 'US') {
						$('option.usa_only').show();
						$('option.canada_only').hide();
					}
					if(country == 'CA') {
						$('option.canada_only').show();
						$('option.usa_only').hide();
					}
					$('select#ipay_addr_state').val($('select#ipay_addr_state option:visible').first().val());
				}
				else {
					$('select.usca_only').hide();
					$('select.usca_only').attr('disabled', 'disabled');
				}
			});
			$('select#ipay_addr_country').change();

			$('#ipay_cc_typ').change(function(e) {
				$('div.solo_only').hide();
				var card = $(this).val();
				if(card == 'SoloCard' || card == 'MaestroCard') {
					$('div.solo_only').show();
				}
			});
			$('#ipay_cc_typ').change();
		});


    </script>
    <?php
}
//iPayment End

//Billpay Beginn
if (MODULE_PAYMENT_BILLPAY_STATUS == 'True' || MODULE_PAYMENT_BILLPAYDEBIT_STATUS == 'True' || MODULE_PAYMENT_BILLPAYTRANSACTIONCREDIT_STATUS == 'True') {
	echo '<script type="text/javascript" src="includes/billpay/templates/js/billpay.js"></script>';
	echo '<link type="text/css" rel="stylesheet" href="includes/billpay/templates/css/billpay.css"/>';
}
//Billpay End
?>


<?php
//Treepodia Beginn

if((strstr($_REQUEST['linkurl'], substr('checkout_success.php', 0, -5)) || strstr($PHP_SELF, substr('checkout_success.php', 0, -5))) && TREEPODIAACTIVE == 'true' && TREEPODIAID != '') {
$orders = xtc_db_fetch_array(xtc_db_query("SELECT orders_id FROM " . TABLE_ORDERS . " WHERE customers_id = '" . (int)$_SESSION['customer_id'] . "' ORDER BY orders_id DESC LIMIT 1;"));
?>
<script type="text/javascript">
   document.write(unescape("%3Cscript src='" + document.location.protocol + "//dxa05szpct2ws.cloudfront.net/TreepodiaAsyncLoader.js' type='text/javascript'%3E%3C/script%3E"));
</script>

<script type="text/javascript">
function initTreepodia () {
<?php 
$orders_products = xtc_db_query("SELECT products_model FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id = '" . (int)$orders['orders_id'] . "';");
if (xtc_db_num_rows($orders_products)) {
	while ($p_sku = xtc_db_fetch_array($orders_products)) {
		echo "try { Treepodia.getProduct('".TREEPODIAID."','".$p_sku['products_model']."').logAddToCart(); } catch (e) {}\n";
	}
} 
   
?>
}
</script>

<?php 
}
//Treepodia End