<?php

/* --------------------------------------------------------------
  ProtectedShopsUpdateApplicationBottomExtender.inc.php 2014-05-26_1650 mabr
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class ProtectedShopsUpdateApplicationBottomExtender extends ProtectedShopsUpdateApplicationBottomExtender_parent {

    public function proceed() {
        parent::proceed();
        $t_last_run = (int) cseo_get_conf(ProtectedShops::CFG_PREFIX . 'UPDATE_LAST_RUN');
        $t_update_interval = (int) cseo_get_conf(ProtectedShops::CFG_PREFIX . 'UPDATE_INTERVAL');
        $t_run_now = $t_update_interval > 0 && ((time() - $t_last_run) > $t_update_interval);
        if ($t_run_now === true) {
            echo '<script async src="' . HTTP_SERVER . DIR_WS_CATALOG . 'request_port.php?module=ProtectedShopsCron&mode=frontend">' . "\n";
        }
    }

}
