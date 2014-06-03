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
 * Hash creation for Barzahlen params
 */
class BarzahlenHashCreate
{
    const SEPERATOR = ";";
    const HASH_ALGORITHM = "sha512";

    /**
     * Creates Hash
     *
     * @param array $array
     * @param string $key
     * @return string
     */
    public function getHash($array, $key)
    {
        $array[] = $key;
        $hash = implode(self::SEPERATOR, $array);
        return hash(self::HASH_ALGORITHM, $hash);
    }
}
