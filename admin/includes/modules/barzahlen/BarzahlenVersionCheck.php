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
 * Version Check
 */
class BarzahlenVersionCheck
{
    const SHOP_SYSTEM = "commerce:SEO";
    const PLUGIN_VERSION = "1.1.0";

    /**
     * @var BarzahlenPluginCheckRequest
     */
    private $request;

    /**
     * @param BarzahlenPluginCheckRequest $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Checks if version was checked in last week
     *
     * @return boolean
     */
    public function isCheckedInLastWeek()
    {
        $lastQuery = xtc_db_query("SELECT configuration_value
                                     FROM " . TABLE_CONFIGURATION . "
                                    WHERE configuration_key = 'MODULE_PAYMENT_BARZAHLEN_LAST_UPDATE_CHECK'");
        $lastCheck = xtc_db_fetch_array($lastQuery);

        if(!$lastCheck) {
            $sql_data = array(
                'configuration_key' => 'MODULE_PAYMENT_BARZAHLEN_LAST_UPDATE_CHECK',
                'configuration_value' => 'now()',
                'configuration_group_id' => 6,
                'date_added' => 'now()'
            );
            xtc_db_perform(TABLE_CONFIGURATION, $sql_data);

            return false;
        } elseif ((time() - strtotime($lastCheck['configuration_value'])) > 60 * 60 * 24 * 7) {
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . "
                             SET configuration_value = NOW()
                           WHERE configuration_key = 'MODULE_PAYMENT_BARZAHLEN_LAST_UPDATE_CHECK'");

            return false;
        }

        return true;
    }

    /**
     * Performs request and updates last check date
     *
     * @param integer $shopId
     * @param string $shopSystemVersion
     */
    public function check($shopId, $shopSystemVersion)
    {
        $requestArray = array(
            'shop_id' => $shopId,
            'shopsystem' => self::SHOP_SYSTEM,
            'shopsystem_version' => $shopSystemVersion,
            'plugin_version' => self::PLUGIN_VERSION,
        );

        $this->request->sendRequest($requestArray);
    }

    /**
     * @return bool
     */
    public function isNewVersionAvailable()
    {
        return self::PLUGIN_VERSION != $this->request->getPluginVersion();
    }

    /**
     * @return string
     */
    public function getNewestVersion()
    {
        return $this->request->getPluginVersion();
    }

    /**
     * @return string
     */
    public function getNewestVersionUrl()
    {
        return $this->request->getPluginUrl();
    }
}
