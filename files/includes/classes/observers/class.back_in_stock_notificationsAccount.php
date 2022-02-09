<?php

/**
 * Back In Stock Notifications Account Page Notification Notice
 *
 * @author     Conor Kerr <back_in_stock_notifications@dev.ceon.net>
 * @copyright  Copyright 2007-2008 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/back_in_stock_notifications
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: class.back_in_stock_notificationsAccount.php 676 2008-07-02 20:24:46Z conor $
 */

// {{{ class back_in_stock_notificationsAccount

/**
 * Checks if the current user is subscribed to any Back In Stock Notification lists.
 *
 * @author     Conor Kerr <back_in_stock_notifications@dev.ceon.net>
 * @copyright  Copyright 2007-2008 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/back_in_stock_notifications
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: class.back_in_stock_notificationsAccount.php 676 2008-07-02 20:24:46Z conor $
 */
class back_in_stock_notificationsAccount extends base
{
	
	function back_in_stock_notificationsAccount()
	{
		global $zco_notifier;
		
		$zco_notifier->attach($this,
			array(
				'NOTIFY_HEADER_END_ACCOUNT'
				)
			);
	}
	
	function update(&$callingClass, $notifier, $paramsArray)
	{
		global $db, $subscribed_to_notification_lists;
		
		// Check if this user is subscribed to any back in stock notification lists
		if (BACK_IN_STOCK_NOTIFICATION_ENABLED == 1) {
			$subscribed_notification_lists_query = "
			SELECT
				product_id
			FROM
				" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . "
			WHERE
				customer_id = '" . (int)$_SESSION['customer_id'] . "';";
			
			$subscribed_notification_lists_result = $db->Execute($subscribed_notification_lists_query);
			
			if ($subscribed_notification_lists_result->RecordCount() > 0) {
				// User is subscribed to at least one Back In Stock Notification List
				$subscribed_to_notification_lists = true;
			} else {
				$subscribed_to_notification_lists = false;
			}
		}
	}
}

// }}}
 
?>