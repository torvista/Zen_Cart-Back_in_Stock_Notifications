<?php

/**
 * Ceon Back In Stock Notifications Account Notifications Management Page.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: header_php.php 937 2012-02-10 11:42:20Z conor $
 */

if (!$_SESSION['customer_id']) {
	$_SESSION['navigation']->set_snapshot();
	
	zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
}

require(DIR_FS_CATALOG . DIR_WS_MODULES . 'require_languages.php');

$breadcrumb->add(ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_NAVBAR_TITLE_1, zen_href_link(FILENAME_ACCOUNT,
	'', 'SSL'));

$breadcrumb->add(ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_NAVBAR_TITLE_2);

if (isset($_POST['back']) || isset($_POST['back_x'])) {
	zen_redirect(zen_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}


// {{{ getSubscribedBackInStockNotificationLists()

/**
 * Gets the list of products for which this user has subscribed to notification lists.
 *
 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @param   integer   $customer_id   The customer's ID.
 * @return  array     An associative array with a list of notification lists.
 */
function getSubscribedBackInStockNotificationLists($customer_id)
{
	global $db;
	
	$subscribed_notification_lists = array();
	
	$subscribed_notification_lists_query = "
		SELECT
			bisns.id, bisns.product_id, pd.products_name, bisns.date_subscribed
		FROM
			" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " bisns
		LEFT JOIN
			" . TABLE_PRODUCTS_DESCRIPTION . " pd
		ON
			bisns.product_id = pd.products_id
		LEFT JOIN
			" . TABLE_CUSTOMERS . " c
		ON
			c.customers_id = bisns.customer_id
		WHERE
			(bisns.customer_id = '" . (int) $customer_id . "'
		OR
			c.customers_email_address = bisns.email_address)
		AND
			pd.language_id = '" . (int)$_SESSION['languages_id'] . "';";
	
	$subscribed_notification_lists_result = $db->Execute($subscribed_notification_lists_query);
	
	if ($subscribed_notification_lists_result->RecordCount() == 0) {
		// User is not subscribed to any back in stock notification lists
		
	} else {
		// Build the list of notification lists to which this user is subscribed
		while (!$subscribed_notification_lists_result->EOF) {
			
			$subscribed_notification_lists[] = array(
				'id' => $subscribed_notification_lists_result->fields['id'],
				'product_id' => $subscribed_notification_lists_result->fields['product_id'],
				'product_name' => $subscribed_notification_lists_result->fields['products_name'],
				'date' => $subscribed_notification_lists_result->fields['date_subscribed']
				);
			
			$subscribed_notification_lists_result->MoveNext();
		}
	}
	
	return $subscribed_notification_lists;
}

// }}}


$subscribed_notification_lists =
	getSubscribedBackInStockNotificationLists($_SESSION['customer_id']);

// Check if the user has deselected any of their subscriptions
if (isset($_POST['submit']) || isset($_POST['submit_x'])) {
	// Remove the user from the selected notification lists
	$stay_subscribed_to = array();
	
	if (isset($_POST['stay_subscribed_to'])) {
		$stay_subscribed_to = $_POST['stay_subscribed_to'];
	}
	
	$number_of_subscriptions = sizeof($subscribed_notification_lists);
	
	$number_to_stay = sizeof($stay_subscribed_to);
	
	if ($number_to_stay < $number_of_subscriptions) {
		$unsubscribe_from = array();
		
		// User wants to be removed from a few lists, get information about the products
		for ($i = 0; $i < $number_of_subscriptions; $i++) {
			if (!in_array($subscribed_notification_lists[$i]['id'], $stay_subscribed_to)) {
				$unsubscribe_from[] = array(
					'id' => $subscribed_notification_lists[$i]['id'],
					'product_name' => $subscribed_notification_lists[$i]['product_name']
					);
			}
		}
		
		$num_unsubscribe_from = sizeof($unsubscribe_from);
		
		// Unsubscribe the user from the lists
		for ($i = 0; $i < $num_unsubscribe_from; $i++) {
			$unsubscribe_query = "
				DELETE FROM
					" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . "
				WHERE
					id = '" . $unsubscribe_from[$i]['id'] . "';";
			
			$db->Execute($unsubscribe_query);
		}
		
		// Let user know that they were successfully unsubscribed
		if ($num_unsubscribe_from == 1) {
			$intro_success = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_SUCCESSFULLY_UNSUBSCRIBED_SINGULAR;
			
			$intro_unsubscribed_products =
				htmlentities($unsubscribe_from[0]['product_name'], ENT_COMPAT, CHARSET);
			
		} else {
			$intro_success = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_SUCCESSFULLY_UNSUBSCRIBED_PLURAL;
			
			for ($i = 0; $i < $num_unsubscribe_from; $i++) {
				$intro_unsubscribed_products .=
					htmlentities($unsubscribe_from[$i]['product_name'], ENT_COMPAT, CHARSET) .
					'<br />';
			}
		}
		
		$intro_instructions = '';
		if ($num_unsubscribe_from < $number_of_subscriptions) {
			// Update the list of Current Subscriptions for this user
			$subscribed_notification_lists =
				getSubscribedBackInStockNotificationLists($_SESSION['customer_id']);
			
			$intro_instructions = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_INTRO2;
		} else {
			$subscribed_notification_lists = array();
		}
	} else {
		// User hasn't selected any lists to unsubscribe from
		$intro_none_selected = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_NONE_SELECTED;
		$intro_instructions = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_INTRO2;
	}
} else {
	// Output standard introductory text
	$intro1 = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_INTRO1;
	$intro_instructions = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_INTRO2;
}

?>