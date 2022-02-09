<?php

/**
 * Ceon Back In Stock Notifications Database Table Name Definition.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications_database_tables.php 935 2012-02-06 14:08:25Z conor $
 */

if (!defined('DB_PREFIX')) {
    define('DB_PREFIX', '');
}

define('TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS', DB_PREFIX .
    'back_in_stock_notification_subscriptions');

?>