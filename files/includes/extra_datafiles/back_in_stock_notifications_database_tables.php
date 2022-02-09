<?php

/**
 * Back In Stock Notification Database Table Name Definitions
 *
 * @author     Conor Kerr <back_in_stock_notifications@dev.ceon.net>
 * @copyright  Copyright 2004-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/back_in_stock_notifications
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: back_in_stock_notifications_database_tables.php 279 2009-01-13 18:21:43Z Bob $
 */

if (!defined('DB_PREFIX')) {
    define('DB_PREFIX', '');
}
define('TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS', DB_PREFIX . 'back_in_stock_notification_subscriptions');

?>