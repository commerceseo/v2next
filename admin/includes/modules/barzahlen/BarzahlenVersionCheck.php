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
 * Version Check
 */
class BarzahlenVersionCheck
{
    const SHOP_SYSTEM = "commerce:SEO";
    const PLUGIN_VERSION = "1.0.7";

    /**
     * @var BarzahlenPluginCheckRequest
     */
    private $request;
    /**
     * @var BarzahlenConfigRepository
     */
    private $configRepository;


    /**
     * @param BarzahlenPluginCheckRequest $request
     * @param BarzahlenConfigRepository $configRepository
     */
    public function __construct($request, $configRepository)
    {
        $this->request = $request;
        $this->configRepository = $configRepository;
    }

    /**
     * Checks if version was checked in last week
     *
     * @param int $now
     * @return bool
     */
    public function isCheckedInLastWeek($now)
    {
        $lastUpdate = $this->configRepository->getLastUpdateDate();
        if ($lastUpdate) {
            $isChecked = ($now - $lastUpdate) < 60 * 60 * 24 * 7;
        } else {
            $isChecked = false;
        }

        return $isChecked;
    }

    /**
     * Performs request and updates last check date
     */
    public function check($shopId, $paymentKey, $shopSystemVersion)
    {
        $this->sendRequest($shopId, $paymentKey, $shopSystemVersion);
        $this->updateLastCheckDate();
    }

    private function sendRequest($shopId, $paymentKey, $shopSystemVersion)
    {
        $requestArray = array(
            'shop_id' => $shopId,
            'shopsystem' => self::SHOP_SYSTEM,
            'shopsystem_version' => $shopSystemVersion,
            'plugin_version' => self::PLUGIN_VERSION,
        );

        $this->request->sendRequest($requestArray, $paymentKey);
    }

    private function updateLastCheckDate()
    {
        if ($this->configRepository->getLastUpdateDate() == false) {
            $this->configRepository->insertLastUpdateDate();
        } else {
            $this->configRepository->updateLastUpdateDate();
        }
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
}
