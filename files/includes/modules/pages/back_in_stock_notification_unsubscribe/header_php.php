<?php

/**
 * Ceon Back In Stock Notifications Unsubscription page.
 *
 * Allows users to unsubscribe from a "Back In Stock" notification list for a given product.
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

/**
 * Load in the language file
 */
require(DIR_FS_CATALOG . DIR_WS_MODULES . 'require_languages.php');

$breadcrumb->add(BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_NAVBAR_TITLE);

$action = 'not_found';

$back_in_stock_notification_id = 0;
$back_in_stock_notification_code = 0;
$product_name = '';

if (isset($_GET['id']) || isset($_POST['id'])) {
	// Check that the specified subscription exists!
	if (isset($_GET['id'])) {
		$back_in_stock_notification_id = (int) $_GET['id'];
		$back_in_stock_notification_code = isset($_GET['code']) ? $_GET['code'] : 0;
	} else {
		$back_in_stock_notification_id = (int) $_POST['id'];
		$back_in_stock_notification_code = isset($_POST['code']) ? $_POST['code'] : 0;
	}
	
	if (!is_numeric($back_in_stock_notification_id)) {
		$back_in_stock_notification_id = 0;
	}
	
	// Get the information about this notification
	$unsubscribe_info_query = "
		SELECT
			pd.products_name
		FROM
			" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " bisns
		LEFT JOIN
			" . TABLE_PRODUCTS_DESCRIPTION . " pd
		ON
			pd.products_id = bisns.product_id
		WHERE
			bisns.id = '" . zen_db_input($back_in_stock_notification_id) . "'
		AND
			bisns.subscription_code = '" . zen_db_input($back_in_stock_notification_code) . "'
		AND
			pd.language_id = '" . (int) $_SESSION['languages_id'] . "'";
	
	$unsubscribe_info = $db->Execute($unsubscribe_info_query);
	
	if ($unsubscribe_info->RecordCount() == 0) {
		// Unknown subscription ID/code supplied
		$action = 'not_found';
	} else {
		$product_name = $unsubscribe_info->fields['products_name'];
		
		if (isset($_POST['id'])) {
			$action = 'unsubscribe';
		} else {
			$action = 'display_details';
		}
	}
}

if ($action == 'display_details') {
	// Display the details for this notification
	
} else if ($action == 'not_found') {
	// Unknown subscription ID/code supplied
	$back_in_stock_notification_unsubscribe_title =
		BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_TEXT_UNKNOWN_NOTIFICATION_TITLE;
	$back_in_stock_notification_unsubscribe_message =
		BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_TEXT_UNKNOWN_NOTIFICATION_MESSAGE;
	
} else if ($action == 'unsubscribe') {
	$unsubscribe_query = "
		DELETE FROM
			" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . "
		WHERE
			id = '" . zen_db_input($back_in_stock_notification_id) . "'
		AND
			subscription_code = '" . zen_db_input($back_in_stock_notification_code) . "'";
	
	$unsubscribe = $db->Execute($unsubscribe_query);
	
	$back_in_stock_notification_unsubscribe_title =
		BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_SUCCESS_TITLE;
	$back_in_stock_notification_unsubscribe_message = sprintf(
		BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_SUCCESS_MESSAGE, $product_name);
}

$_SESSION['navigation']->remove_current_page();

?>