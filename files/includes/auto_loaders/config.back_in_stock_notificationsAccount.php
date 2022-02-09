<?php

/**
 * Back In Stock Notifications Account Page Notification Notice Auto Loader
 *
 * @author     Conor Kerr <back_in_stock_notifications@dev.ceon.net>
 * @copyright  Copyright 2007-2008 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/back_in_stock_notifications
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: config.back_in_stock_notificationsAccount.php 676 2008-07-02 20:24:46Z conor $
 */

$autoLoadConfig[200][] = array('autoType' => 'class',
	'loadFile' => 'observers/class.back_in_stock_notificationsAccount.php');
	
$autoLoadConfig[200][] = array('autoType' => 'classInstantiate',
	'className' => 'back_in_stock_notificationsAccount',
	'objectName' => 'back_in_stock_notifications_account');

?>