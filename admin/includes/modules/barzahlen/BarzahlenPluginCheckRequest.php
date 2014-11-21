<?php
/**
 * Barzahlen Payment Module (commerce:SEO)
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Mathias Hertlein
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

/**
 * Responsible for requesting the Barzahlen API
 */
class BarzahlenPluginCheckRequest
{
    const URL = "https://plugincheck.barzahlen.de/check";

    const CERT_PATH = 'includes/modules/payment/barzahlen/certs/ca-bundle.crt';

    /**
     * @var BarzahlenHttpClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $error;

    /**
     * @var string
     */
    private $response;

    /**
     * @var string
     */
    private $pluginVersion;

    /**
     * @var string
     */
    private $pluginUrl;

    /**
     * @var string
     */
    private $result;

    /**
     * @param BarzahlenHttpClient $httpClient
     */
    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param array $requestParams
     */
    public function sendRequest($requestParams)
    {
        $this->doRequest($requestParams);

        if ($this->error) {
            throw new RuntimeException("An error occurred while request Barzahlen API: " . $this->error);
        }

        $this->parseResponse();

        if ($this->result > 0) {
            throw new RuntimeException("Barzahlen API returned error result: " . $this->result);
        }
    }

    /**
     * @param array $requestParams
     */
    private function doRequest($requestParams)
    {
        $result = $this->httpClient->post(self::URL, DIR_FS_CATALOG . self::CERT_PATH, $requestParams);

        $this->response = $result['response'];
        $this->error = $result['error'];
    }

    private function parseResponse()
    {
        $domDocument = new DOMDocument();
        $domDocument->loadXML($this->response);

        $this->pluginVersion = $domDocument->getElementsByTagName("plugin-version")->item(0)->nodeValue;
        $this->pluginUrl = $domDocument->getElementsByTagName("plugin-url")->item(0)->nodeValue;
        $this->result = $domDocument->getElementsByTagName("result")->item(0)->nodeValue;
    }

    /**
     * @return string
     */
    public function getPluginVersion()
    {
        return $this->pluginVersion;
    }

    /**
     * @return string
     */
    public function getPluginUrl()
    {
        return $this->pluginUrl;
    }
}
