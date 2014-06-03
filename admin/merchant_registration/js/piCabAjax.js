/**
 * Load new HTML into the target div
 * 
 * @param html string
 * @param id string
 */
function setPiCabTargetHTML (html, id)
{
    document.getElementById(id).innerHTML = html;
}

/**
 * Send a ajax call to the given source
 * 
 * @param source string 
 * @throws new Exception('Rückgabewerte fehlen!');
 */
function getPiCabResponse (source)
{
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        ajaxhttp = new XMLHttpRequest();
    } else {// code for IE6, IE5
        ajaxhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    ajaxhttp.open("POST", source, false);
    ajaxhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxhttp.send();
    if (ajaxhttp.responseText != null) {
        return ajaxhttp.responseText;
    } else {
        throw new Exception('Rückgabewerte fehlen!');
    }
}

/**
 * Get the languange code
 * 
 * @return string lang code
 */
function getPiCabLanguange()
{
    try{
        return getPiCabResponse(piCabXtcLangPath + '/piCabLang.php');
    } catch (e) {
        return 'en';
    }
    
}

/**
 * Load the next page of the
 * merchant registration process
 * 
 * @param step string
 * @param id string
 */
function piCabNextPage(step, id)
{
    switch (step) {
        case 'first':
            try {
                setPiCabTargetHTML(getPiCabResponse(piCabPath + '/php/' + getPiCabLanguange() +'/registrationFirstStep.php'), id);
            } catch (e) {
                setPiCabTargetHTML('<p>' + e + '</p>', id);
            }
            break;
        case 'success':
            try {
                setPiCabTargetHTML(getPiCabResponse(piCabXtcLangPath + '/registrationSuccess.php'), id);
            } catch(e){
                setPiCabTargetHTML('<p>' + e + '</p>', id);
            }
            break;
        default:
            setPiCabTargetHTML('<p>Error</p>', id);
            break;
    }
}

/**
 * Handles the merchant registration response
 * with a redirect or a error
 */
function sendPiCabMerchantRegistrationRequest()
{
    try {
        response = JSON.parse(getPiCabResponse(piCabXtcLangPath + '/merchantRegistrationCall.php'));
        if (response.success) {
            handlePiCabResponseSuccess(response);
        } else{
            handlePiCabResponseError(response);
        }
    } catch (e) {
        setPiCabTargetHTML('<p>' + e + '</p>', 'piCabEmbeddedRegistration');
    }
}

/**
 * Handles a error in the response
 * 
 * @param response json object
 */
function handlePiCabResponseError(response)
{
    var img = '<center><img src="' + piCabPath + '/img/logo_header.gif" alt="clickandbuy logo"/></center>';
    var div = '<div class="piCabErrorBox">' + response.description + '</div>';
    var html = img + '<br/>' + div;
    setPiCabTargetHTML(html, 'piCabEmbeddedRegistration');
}

/**
 * Handles a success in the response
 * 
 * @param response json object
 */
function handlePiCabResponseSuccess(response)
{
    window.location = response.registrationURL;
}

/**
 * Show or hide the given id
 */
function toggledDisplay (id)
{
    if (document.getElementById) {
        var mydiv = document.getElementById(id);
        mydiv.style.display = (mydiv.style.display=='block'?'none':'block');
    }
}

/**
 * Show or hide all contents of the registration
 */
function toggleWrapper()
{
    toggledDisplay('piCabEmbeddedRegistration');
    toggledDisplay('piCabHead');
    toggledDisplay('piCabOverlay');
    document.getElementById('piCabOverlay').style.height = getDocHeight() + 'px';
}

/**
 * Initialize the merchant registration
 */
function piCabInitRegistration()
{
    piCabNextPage('first', 'piCabEmbeddedRegistration');   
}

/**
 * Initialize the merchant registration
 */
function piCabInitSuccess()
{
    piCabNextPage('success', 'piCabEmbeddedRegistration');
    toggledDisplay('piCabEmbeddedRegistration');
    toggledDisplay('piCabHead');
    toggledDisplay('piCabOverlay');
}

/**
 * Get the document height for all browser
 * 
 * @return integer document height
 */
function getDocHeight() {
    var d = document;
    return Math.max(
        Math.max(d.body.scrollHeight, d.documentElement.scrollHeight),
        Math.max(d.body.offsetHeight, d.documentElement.offsetHeight),
        Math.max(d.body.clientHeight, d.documentElement.clientHeight)
    );
}


/**
 * Getter for the complete GET array
 * 
 * @return array GET
 */
function getGetParams() {
    var GET = new Array();
    if(location.search.length > 0) {
        var getParamStr = location.search.substring(1, location.search.length);
        var getParams = getParamStr.split("&");
        for(i = 0; i < getParams.length; i++) {
            var keyValue = getParams[i].split("=");
            if(keyValue.length == 2) {
                var key = keyValue[0];
                var value = keyValue[1];
                GET[key] = value;
            }
        }
    }
    return(GET);
}
 
/**
 * Getter for a specific GET value
 * 
 * @param string key
 * @return string get value
 */
function getGetParam(key) {
    var getParams = getGetParams();
    if (getParams[key]) {
        return(getParams[key]);
    } else {
        return false;
    }
}