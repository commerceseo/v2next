<?php
require_once dirname(__FILE__).'/sofortLib_abstract.inc.php';

/**
 * This class encapsulates retrieval of listed banks of the Netherlands
 *
 * Copyright (c) 2012 SOFORT AG
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Date: 2013-06-05 15:01:19 +0200 (Mi, 05 Jun 2013) $
 * @version SofortLib 1.5.0  $Id: sofortLib_ideal_banks.inc.php 420 2013-06-19 18:04:39Z akausch $
 * @author SOFORT AG http://www.sofort.com (integration@sofort.com)
 *
 */
class SofortLib_iDeal_Banks extends SofortLib_Abstract {
	
	protected $_xmlRootTag = 'ideal';
	
	protected $_parameters = array();
	
	protected $_response = array();
	
	private $banks = array();
	
	
	/**
	 * 
	 * Constructor
	 * @param string $configKey
	 * @param strign $apiUrl
	 */
	public function __construct($configKey, $apiUrl = '') {
		list ($userId, $projectId, $apiKey) = explode(':', $configKey);
		parent::__construct($userId, $apiKey, $apiUrl.'/banks');
	}
	
	
	/**
	 * 
	 * Getter for bank list
	 */
	public function getBanks() {
		return $this->_banks;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see SofortLib_Abstract::_parseXml()
	 */
	protected function _parseXml() {
		if (isset($this->_response['ideal']['banks']['bank'][0]['code']['@data'])) {
			foreach($this->_response['ideal']['banks']['bank'] as $key => $bank) {
				$this->_banks[$key]['code'] = $bank['code']['@data'];
				$this->_banks[$key]['name'] = $bank['name']['@data'];
			}
		}
	}
}
?>