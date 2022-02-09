<?php

/**
 * Ceon Back In Stock Notifications Account Page Notification Notice Auto Loader.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: config.back_in_stock_notificationsAccount.php 935 2012-02-06 14:08:25Z conor $
 */

$autoLoadConfig[200][] = array('autoType' => 'class',
	'loadFile' => 'observers/class.back_in_stock_notificationsAccount.php');
	
$autoLoadConfig[200][] = array('autoType' => 'classInstantiate',
	'className' => 'back_in_stock_notificationsAccount',
	'objectName' => 'back_in_stock_notifications_account');

?>