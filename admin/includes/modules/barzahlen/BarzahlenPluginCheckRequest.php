<?php
/**
 * Barzahlen Payment Module (commerce:SEO)
 *
 * NOTICE OF LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @copyright   Copyright (c) 2013 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Mathias Hertlein
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

/**
 * Responsible for requesting the Barzahlen API
 */
class BarzahlenPluginCheckRequest
{
    const URL = "https://plugincheck.barzahlen.de/check";

    const CERT_PATH = 'includes/modules/payment/ca-bundle.crt';

    /**
     * @var BarzahlenHttpClient
     */
    private $httpClient;

    /**
     * @var array
     */
    private $requestArray;

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
    private $result;
    /**
     * @var string
     */
    private $pluginVersion;

    /**
     * @param BarzahlenHttpClient $httpClient
     */
    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param array $requestParams
     * @param string $key
     */
    public function sendRequest($requestParams, $key)
    {
        $this->requestArray = array_merge($requestParams, array(
            'hash' => $this->hash($requestParams, $key),
        ));

        $this->doRequest();

        if ($this->error) {
            throw new RuntimeException("An error occurred while request Barzahlen-API");
        }

        $this->parseResponse();

        if ($this->result > 0) {
            throw new RuntimeException("Barzahlen-API returned result {$this->result}");
        }
    }

    /**
     * @return string
     */
    public function getPluginVersion()
    {
        return $this->pluginVersion;
    }

    private function doRequest()
    {
        $result = $this->httpClient->post(self::URL, DIR_FS_CATALOG . self::CERT_PATH, $this->requestArray);

        $this->response = $result['response'];
        $this->error = $result['error'];
    }

    private function parseResponse()
    {
        $domDocument = new DOMDocument();
        $domDocument->loadXML($this->response);

        $this->result = $domDocument->getElementsByTagName("result")->item(0)->nodeValue;
        $this->pluginVersion = $domDocument->getElementsByTagName("plugin-version")->item(0)->nodeValue;
    }

    /**
     * @param array $params
     * @param string $key
     * @return string
     */
    private function hash($params, $key)
    {
        $hashCreate = new BarzahlenHashCreate();
        return $hashCreate->getHash($params, $key);
    }
}
