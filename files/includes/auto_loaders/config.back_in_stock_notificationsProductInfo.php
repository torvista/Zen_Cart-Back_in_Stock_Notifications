<?php

/**
 * Back In Stock Notifications Product Info Page Notification Form Display Auto Loader
 *
 * @author     Conor Kerr <back_in_stock_notifications@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/back_in_stock_notifications
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: config.back_in_stock_notificationsProductInfo.php 317 2009-02-23 12:01:47Z Bob $
 */

$autoLoadConfig[200][] = array('autoType' => 'class',
	'loadFile' => 'observers/class.back_in_stock_notificationsProductInfo.php');
	
$autoLoadConfig[200][] = array('autoType' => 'classInstantiate',
	'className' => 'back_in_stock_notificationsProductInfo',
	'objectName' => 'back_in_stock_notifications_product_info');

?>