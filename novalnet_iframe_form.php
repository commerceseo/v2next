<?php
#########################################################
#                                                       #
#  Novalnet Iframe payment method                       #
#  This module is used for real time processing         #
#                                                       #
#  Copyright (c) Novalnet AG                            #
#                                                       #
#  Released under the GNU General Public License        #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_iframe_form.php                    #
#                                                       #
#########################################################
    include ('includes/application_top.php');
    $request = $_REQUEST;
    global $request_type;
    $novalnet_url = ($request_type == 'SSL' ? 'https://' : 'http://');
    $payment_code = array(
            'novalnet_cc'   => 'CC',
            'novalnet_sepa' => 'SEPA',
    );
    $url = array(
            'novalnet_sepa' => 'payport.novalnet.de/direct_form_sepa.jsp' ,
            'novalnet_cc' => 'payport.novalnet.de/direct_form.jsp'
    );

    $vendor_id  = trim(constant('MODULE_PAYMENT_NOVALNET_' . $payment_code[$request['type']] . '_VENDOR_ID'));
    $auth_code  = trim(constant('MODULE_PAYMENT_NOVALNET_' . $payment_code[$request['type']] . '_AUTH_CODE'));
    $product_id = trim(constant('MODULE_PAYMENT_NOVALNET_' . $payment_code[$request['type']] . '_PRODUCT_ID'));

    if ($request['type'] == 'novalnet_cc') {
        $request['nn_vendor_id_nn']  =  $vendor_id;
        $request['nn_authcode_nn']   =  $auth_code;
        $request['nn_product_id_nn'] =  $product_id;
        $nn_proxy = trim(constant('MODULE_PAYMENT_NOVALNET_' . $payment_code[$request['type']] . '_PROXY'));
    }
    if ($request['type'] == 'novalnet_sepa') {
        $request['vendor_id']  = $vendor_id;
        $request['authcode']   = $auth_code;
        $request['product_id'] = $product_id;
        $request['name'] = utf8_decode($request['name']);
        $request['address'] = utf8_decode($request['address']);
        $request['city'] = utf8_decode($request['city']);
        $request['comp'] = utf8_decode($request['company']);
        $nn_proxy = trim(constant('MODULE_PAYMENT_NOVALNET_' . $payment_code[$request['type']] . '_PROXY'));
    }
	
    $request = array_map('trim',$request);
    $form_loading_url = $novalnet_url . $url[$request['type']];
    $ch = curl_init($form_loading_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);  // add POST fields
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    if(isset($nn_proxy)){
        curl_setopt($ch, CURLOPT_PROXY, $nn_proxy);
    }
    $iframe_form_content = curl_exec($ch);
    curl_close($ch);
    echo $iframe_form_content;
