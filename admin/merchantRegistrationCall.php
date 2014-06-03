<?php

/*
 * Call script for the embedded merchant registration
 */
require_once('includes/application_top.php');
require_once('../includes/classes/pi_clickandbuy_functions.php');

$_SESSION['cab']['sharedSecretRegistration'] = '';

$query = xtc_db_query("SELECT configuration_value from " . TABLE_CONFIGURATION . " WHERE configuration_key = 'STORE_OWNER_EMAIL_ADDRESS'");
$configArray = xtc_db_fetch_array($query);
$adminEmail = $configArray['configuration_value'];

$query = xtc_db_query("SELECT configuration_value from " . TABLE_CONFIGURATION . " WHERE configuration_key = 'STORE_OWNER'");
$configArray = xtc_db_fetch_array($query);
$companyName = $configArray['configuration_value'];

$cabApi = new pi_clickandbuy_functions();

$url = $_SERVER['HTTP_REFERER'] . '&success=true';

$registrationData = array(
    'businessOriginID'  => $cabApi->getBuisinessOriginId(),
    'returnURL'         => $url
);

$registrationData = $cabApi->removeEmptyTag($registrationData);

$companyAddressData = array(
    'street'            => 'Bitte eingeben',
    'houseNumber'       => '1',
    'houseNumberSuffix' => '',
    'zip'               => '00000',
    'city'              => 'Bitte eingeben',
    'country'           => 'DE',
    'state'             => '',
    'addressSuffix'     => ''
);
$companyAddressData = $cabApi->removeEmptyTag($companyAddressData);

$telephoneNumber = array(
    'countryCode' => '',
    'phoneNumber' => ''
);
$telephoneNumber = $cabApi->removeEmptyTag($telephoneNumber);

$merchantData = array(
    'companyName'            => $companyName,
    'vatID'                  => '',
    'countryOfIncorporation' => '',
    'dateOfIncorporation'    => '',
    'companyAddress'         => $companyAddressData,
    'companyType'            => '',
    'emailAddress'           => $adminEmail,
    'website'                => HTTP_SERVER . DIR_WS_CATALOG,
    'timeZone'               => '',
    'adminFirstName'         => '',
    'adminMiddleName'        => '',
    'adminLastName'          => '',
    'adminGender'            => '',
    'telephoneNumber'        => $telephoneNumber,
    'language'               => ''
);
$merchantData = $cabApi->removeEmptyTag($merchantData);

$settlementData = array(
    'currency'      => 'EUR',
    'name'          => '',
    'categoryID'    => '3052'
);
$settlementData = $cabApi->removeEmptyTag($settlementData);

$averageTicketSize = array(
    'amount'    => 50,
    'currency'  => 'EUR'
);

$feeCardData = array(
    'invoicingCycle'    => 14,
    'settlementDelay'   => 5,
    'averageTicketSize' => $averageTicketSize
);
$feeCardData = $cabApi->removeEmptyTag($feeCardData);

$projectData = array(
    'name'   => '',
    'mmsURL' => HTTPS_CATALOG_SERVER . DIR_WS_CATALOG . 'pi_clickandbuy_mms.php'
);
$projectData = $cabApi->removeEmptyTag($projectData);

$integrationData = array(
    'settlementData' => $settlementData,
    'feeCardData'    => $feeCardData,
    'projectData'    => $projectData
);
$integrationData = $cabApi->removeEmptyTag($integrationData);

$details = array(
    'registrationData' => $registrationData,
    'merchantData'     => $merchantData,
    'integrationData'  => $integrationData
);
$details = $cabApi->removeEmptyTag($details);

$requestArray = array(
    'details' => $details
);

$soapObject   = $cabApi->getMerchantResponse($requestArray, 'createMerchantRegistration_Request');
$responseData = $cabApi->getMerchantRegistrationResponseData($soapObject);
echo json_encode($responseData);
?>
