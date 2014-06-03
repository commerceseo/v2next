<?php
/*
 * Success site of the embedded merchant registration
 */

require_once('includes/application_top.php');
require_once('../includes/classes/pi_clickandbuy_functions.php');

$cabApi = new pi_clickandbuy_functions();

$lang = $_SESSION['pi']['cab']['languange'];
if (isset($_SESSION['language']) && $_SESSION['language'] == 'german') {
    $successSiteHeadline = 'Registrierungsstatus';
    $noResponse           = 'Keine Antwort vom Server!';
    $error                 = 'Es ist ein Fehler aufgetreten!';
    $success               = 'Ihre ClickandBuy Konfigurationseinstellungen wurden automatisch in Ihren Shop &uuml;bertragen.';
    $close                 = 'Schlie&szlig;en';
} else {
    $successSiteHeadline = 'Registration status';
    $noResponse           = 'No response from the server!';
    $error                 = 'There is an error!';
    $success               = 'Your ClickandBuy configuration settings are automatically stored into your shop.';
    $close                 = 'Close';
}

$token = $cabApi->generateMerchantRegistrationToken($cabApi->getBuisinessOriginId(), $_SESSION['cab']['merchantId'],
                                                      $_SESSION['cab']['sharedSecretRegistration']);
$details = array(
    'businessOriginID'  => $cabApi->getBuisinessOriginId(),
    'merchantID'        => $_SESSION['cab']['merchantId'],
    'token'             => $token
);

$requestArray = array(
    'details' => $details
);
$soapObject     = $cabApi->getMerchantResponse($requestArray, 'getMerchantRegistrationStatus_Request');
$responseArray  = $cabApi->getMerchantRegistrationResponseData($soapObject);
?>
<div id="piCabRegistrationFirstStep"  class="piCabLeft">
    <?php require_once('merchant_registration/php/image.inc.php'); ?>
    <div style="text-align:center;">
    <?php if (!empty($responseArray['success']) && $responseArray['success'] == true) : ?>
        <h2><?php echo $successSiteHeadline; ?></h2>
        <p><?php echo $success; ?></p>
    <?php endif; ?>
        
    <?php if (empty($responseArray)) : ?>
        <h2 class="piCabCenter"><?php echo $noResponse; ?></h2>
    <?php endif; ?>
        
    <?php if (empty($responseArray['success'])) : ?>
        <h2 class="piCabCenter"><?php echo $error; ?></h2>
        <div class="piCabErrorBox"><?php echo $responseArray['description']; ?></div>
    <?php endif; ?>
        <div class="piCabCenter"><a href="#" onclick="toggleWrapper('piCabEmbeddedRegistration')"><?php echo $close; ?></a></div>
    </div>
</div>