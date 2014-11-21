function getFormValue(){
                document.getElementById('loading').style.display = 'none';
}

function getFormValueForSEPA() {
        document.getElementById('sepa_loading').style.display = 'none';
}

window.onload = function(){
        if(document.getElementById('payment_process').value == '1'){
                var captureCheckoutEvent = $('#button_save_payment').parent().attr('onclick');
                var combineNovalnetMethod = 'checkNovalnet();'+captureCheckoutEvent;
                $('#button_save_payment').parent().attr('onclick', combineNovalnetMethod);
    }else {
                var getCheckoutPageID = document.getElementById("checkout_payment");
                var getCheckoutParentElement = getCheckoutPageID.parentNode;
                var captureParentSubmitEvent = getCheckoutParentElement.getAttribute('onsubmit');
                var callNovalnetMethod = 'checkNovalnet();';
                callNovalnetMethod = callNovalnetMethod+captureParentSubmitEvent;
                getCheckoutPageID.setAttribute('onsubmit', callNovalnetMethod);
        }
}

function checkNovalnet() {

                var ccifr = document.getElementById("payment_form_novalnetCc");
                if(ccifr) {
                        var ccIframe = (ccifr.contentWindow || ccifr.contentDocument);
                        if (ccIframe.document) ccIframe=ccIframe.document;
                        var cc_type=0; var cc_owner=0; var cc_no=0; var cc_hash=0; var cc_month=0; var cc_year=0; var cc_cid=0;
                        if(ccIframe.getElementById("novalnetCc_cc_type").value!= '') cc_type=1;
                        if(ccIframe.getElementById("novalnetCc_cc_owner").value!= '') cc_owner=1;
                        if(ccIframe.getElementById("novalnetCc_cc_number").value!= '') cc_no=1;
                        if(ccIframe.getElementById("novalnetCc_expiration").value!= '') cc_month = 1;
                        if(ccIframe.getElementById("novalnetCc_expiration_yr").value!= '') cc_year = 1;
                        if(ccIframe.getElementById("novalnetCc_cc_cid").value!= '') cc_cid=1;

                        var fldVdr = cc_type+','+cc_owner+','+cc_no+','+cc_month+','+cc_year+','+cc_cid;
                        if( ccIframe.getElementById("nncc_cardno_id") != null && ccIframe.getElementById("nncc_cardno_id").value != null ) {
                                document.getElementById('cc_type').value = ccIframe.getElementById("novalnetCc_cc_type").value;
                                document.getElementById('cc_owner').value = ccIframe.getElementById("novalnetCc_cc_owner").value;
                                document.getElementById('cc_exp_month').value = ccIframe.getElementById("novalnetCc_expiration").value;
                                document.getElementById('cc_exp_year').value = ccIframe.getElementById("novalnetCc_expiration_yr").value;
                                document.getElementById('cc_cid').value = ccIframe.getElementById("novalnetCc_cc_cid").value;
                                document.getElementById("cc_panhash").value = ccIframe.getElementById("nncc_cardno_id").value;
                                document.getElementById("cc_uniqueid").value = ccIframe.getElementById("nncc_unique_id").value;
                                document.getElementById('cc_fldvdr').value = fldVdr;
								if((document.getElementById('checkout_xajax') != undefined) && (document.getElementById('checkout_xajax') != null)) {
                                $('#checkout_xajax').parent().append('<input type="hidden" name="cc_type" id="cc_type" value="'+document.getElementById("cc_type").value+'" />');
                                $('#checkout_xajax').parent().append('<input type="hidden" name="cc_owner" id="cc_owner" value="'+document.getElementById("cc_owner").value+'" />');
                                $('#checkout_xajax').parent().append('<input type="hidden" name="cc_exp_month" id="cc_exp_month" value="'+document.getElementById("cc_exp_month").value+'" />');
                                $('#checkout_xajax').parent().append('<input type="hidden" name="cc_exp_year" id="cc_exp_year" value="'+document.getElementById("cc_exp_year").value+'" />');
                                $('#checkout_xajax').parent().append('<input type="hidden" name="cc_cid" id="cc_cid" value="'+document.getElementById("cc_cid").value+'" />');
                                $('#checkout_xajax').parent().append('<input type="hidden" name="cc_panhash" id="cc_panhash" value="'+document.getElementById("cc_panhash").value+'" />');
                                $('#checkout_xajax').parent().append('<input type="hidden" name="cc_uniqueid" id="cc_uniqueid" value="'+document.getElementById("cc_uniqueid").value+'" />');
                                $('#checkout_xajax').parent().append('<input type="hidden" name="cc_fldvdr" id="cc_fldvdr" value="'+document.getElementById("cc_fldvdr").value+'" />');
								}
                        }
                }
                var ifrSepa = document.getElementById("payment_form_novalnetSEPA");
                if(ifrSepa) {
                        var sepaIframe = (ifrSepa.contentWindow || ifrSepa.contentDocument);
                        if (sepaIframe.document) sepaIframe=sepaIframe.document;
                        var sepa_owner = 0;
                        var sepa_accountno = 0;
                        var sepa_bankcode = 0;
                        var sepa_iban = 0;
                        var sepa_swiftbic = 0;
                        var sepa_hash = 0;
                        var sepa_country = 0;
                        if(sepaIframe.getElementById("novalnet_sepa_owner").value!= '') sepa_owner = 1;
                        if(sepaIframe.getElementById("novalnet_sepa_accountno").value!= '') sepa_accountno = 1;
                        if(sepaIframe.getElementById("novalnet_sepa_bankcode").value!= '') sepa_bankcode = 1;
                        if(sepaIframe.getElementById("novalnet_sepa_iban").value!= '') sepa_iban = 1;
                        if(sepaIframe.getElementById("novalnet_sepa_swiftbic").value!= '') sepa_swiftbic = 1;
                        if(sepaIframe.getElementById("nnsepa_hash").value!= '') sepa_hash = 1;
                        if(sepaIframe.getElementById("novalnet_sepa_country").value!= '') {
                                var country = sepaIframe.getElementById("novalnet_sepa_country");
                                sepa_country = 1 + '-' + country.options[country.selectedIndex].value;
                        }
                        var fldVdr = sepa_owner + ',' + sepa_accountno + ',' + sepa_bankcode + ',' + sepa_iban+ ',' + sepa_swiftbic + ',' + sepa_hash + ',' + sepa_country;


                        if( sepaIframe.getElementById("nnsepa_hash") != null && sepaIframe.getElementById("nnsepa_unique_id").value != null ) {

                                document.getElementById("sepa_owner").value = sepaIframe.getElementById("novalnet_sepa_owner").value;
                                document.getElementById("sepa_panhash").value = sepaIframe.getElementById("nnsepa_hash").value;
                                document.getElementById("sepa_uniqueid").value = sepaIframe.getElementById("nnsepa_unique_id").value;
                                document.getElementById("sepa_confirm").value = sepaIframe.getElementById("nnsepa_iban_confirmed").value;
                                document.getElementById('fldVdr').value = fldVdr;
								if((document.getElementById('checkout_xajax') != undefined) && (document.getElementById('checkout_xajax') != null)) {
                                $('#checkout_xajax').parent().append('<input type="hidden" name="sepa_owner" id="sepa_owner" value="'+document.getElementById("sepa_owner").value+'" />');
                                $('#checkout_xajax').parent().append('<input type="hidden" name="sepa_panhash" id="sepa_panhash" value="'+document.getElementById("sepa_panhash").value+'" />');
                                $('#checkout_xajax').parent().append('<input type="hidden" name="sepa_uniqueid" id="sepa_uniqueid" value="'+document.getElementById("sepa_uniqueid").value+'" />');
                                $('#checkout_xajax').parent().append('<input type="hidden" name="sepa_confirm" id="sepa_confirm" value="'+document.getElementById("sepa_confirm").value+'" />');
                                $('#checkout_xajax').parent().append('<input type="hidden" name="fldVdr" id="fldVdr" value="'+document.getElementById("fldVdr").value+'" />');
								}

                        }
                }
}
