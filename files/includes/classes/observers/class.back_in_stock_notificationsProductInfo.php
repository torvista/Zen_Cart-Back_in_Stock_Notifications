<?php

/**
 * Ceon Back In Stock Notifications Product Info Page Notification Form Display.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: class.back_in_stock_notificationsProductInfo.php 937 2012-02-10 11:42:20Z conor $
 */

// {{{ class back_in_stock_notificationsProductInfo

/**
 * Checks if the current user is subscribed to any Back In Stock Notification lists.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 */
class back_in_stock_notificationsProductInfo extends base
{
	
	function back_in_stock_notificationsProductInfo()
	{
		global $zco_notifier;
		
		$zco_notifier->attach($this,
			array(
				'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_DOCUMENT_GENERAL_INFO',
				'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_DOCUMENT_PRODUCT_INFO',
				'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_PRODUCT_BOOK_INFO',
				'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_PRODUCT_FREE_SHIPPING_INFO',
				'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_PRODUCT_INFO',
				'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_PRODUCT_MUSIC_INFO'
				)
			);
	}
	
	function update($callingClass, $notifier, $paramsArray)
	{
		global $db, $request_type, $products_quantity,
			$product_back_in_stock_notification_form_link,
			$back_in_stock_notification_build_form,
			$back_in_stock_notification_form_customer_name,
			$back_in_stock_notification_form_customer_email,
			$back_in_stock_notification_form_customer_email_confirmation;
		
		$product_back_in_stock_notification_form_link = null;
		$back_in_stock_notification_build_form = false;
		
		// Check if customer should be offered the option to be notified when this product is back
		// in stock
		if ($products_quantity <= 0 && BACK_IN_STOCK_NOTIFICATION_ENABLED == 1) {
			$product_back_in_stock_notification_form_link = '';
			$back_in_stock_notification_build_form = true;
			
			// Update the source with the details of the customer (if available)
			if (isset($_SESSION['customer_id']) && $_SESSION['customer_id']) {
				// Check if this user has already requested to be notified when this product is back
				// in stock
				$customer_details_query = "
					SELECT
						customers_firstname, customers_lastname, customers_email_address
					FROM
						" . TABLE_CUSTOMERS . "
					WHERE
						customers_id = '" . (int) $_SESSION['customer_id'] . "'";
				
				$customer_details = $db->Execute($customer_details_query);
				
				$already_to_be_notified_query = "
					SELECT
						id
					FROM
						" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . "
					WHERE
						product_id = '" . (int) $_GET['products_id'] . "'
					AND
						(
							customer_id = '"  . (int) $_SESSION['customer_id'] . "'
						OR
							email_address = '" .
								$customer_details->fields['customers_email_address'] . "'
						);";
				
				$already_to_be_notified = $db->Execute($already_to_be_notified_query);
				
				if ($already_to_be_notified->RecordCount() > 0) {
					// Customer is already subscribed to the notification list for this product
					$back_in_stock_notification_build_form = false;
					
					$product_back_in_stock_notification_form_link =
						BACK_IN_STOCK_NOTIFICATION_TEXT_ALREADY_SUBSCRIBED;
				} else {
					// Customer is not yet subscribed to be notified - store data for notification 
					// request form
					$back_in_stock_notification_form_customer_name = htmlentities(
						$customer_details->fields['customers_firstname'] . ' ' .
						$customer_details->fields['customers_lastname'], ENT_COMPAT, CHARSET);
					$back_in_stock_notification_form_customer_email = htmlentities(
						$customer_details->fields['customers_email_address']);
					$back_in_stock_notification_form_customer_email_confirmation = htmlentities(
						$customer_details->fields['customers_email_address']);
				}
			} else {
				$back_in_stock_notification_form_customer_name = '';
				$back_in_stock_notification_form_customer_email = '';
				$back_in_stock_notification_form_customer_email_confirmation = '';
			}
			
			if ($product_back_in_stock_notification_form_link == '') {
				// Build link to form
				$product_back_in_stock_notification_form_link = sprintf(
					BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_LINK,
					zen_href_link(zen_get_info_page((int) $_GET['products_id']),
					zen_get_all_get_params(array('number_of_uploads')), $request_type) .
					'#back_in_stock_notification_form');
			}
		}
	}
}

// }}}
 
?>