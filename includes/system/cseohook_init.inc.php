<?php
/*-----------------------------------------------------------------
* 	$Id: cseohook_init.inc.php 770 2013-11-29 09:23:38Z akausch $
* 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ------------------------------------------------------------------
* 	based on:
*   Gambio GmbH
*   http://www.gambio.de
*   Copyright (c) 2011 Gambio GmbH
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/

require_once(DIR_FS_CATALOG.'includes/system/core/class.filelog.php');
require_once(DIR_FS_CATALOG.'includes/system/core/class.debugger.inc.php');
require_once(DIR_FS_CATALOG.'includes/system/core/class.datacache.inc.php');
require_once(DIR_FS_CATALOG.'includes/system/core/class.cacheddirectory.inc.php');
require_once(DIR_FS_CATALOG.'includes/system/core/class.registry.inc.php');
require_once(DIR_FS_CATALOG.'includes/system/core/class.classregistry.inc.php');
require_once(DIR_FS_CATALOG.'includes/system/core/class.classoverloadregistry.inc.php');
require_once(DIR_FS_CATALOG.'includes/system/core/cseohookfactory.inc.php');
require_once(DIR_FS_CATALOG.'includes/system/core/cseoautoloader.inc.php');
require_once(DIR_FS_CATALOG.'includes/system/core/languagetextmanager.inc.php');

