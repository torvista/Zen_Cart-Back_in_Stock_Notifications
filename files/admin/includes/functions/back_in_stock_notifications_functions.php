<?php

/**
 * Ceon Back In Stock Notifications Functions.
 *
 * Main functions used to perform primary operations of the module. Can be used within a cron script
 * to automate Back In Stock notification functionality.
 * 
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2008 RubikIntegration team @ RubikIntegration.com
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications_functions.php 937 2012-02-10 11:42:20Z conor $
 */


// {{{ sendBackInStockNotifications()

/**
 * Sends (or pretends to send) e-mail notifications to all users subscribed to back in stock
 * notification lists for which the product is back in stock.
 *
 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @param   boolean   $test_mode   Flag to indicate if e-mails should actually be sent or just a
 *                                 sample e-mail generated for the admin for test purposes.
 * @return  string    Information about the customers e-mailed (if any).
 */
function sendBackInStockNotifications($test_mode = false)
{
	global $db, $messageStack;
	
	if (ini_get('safe_mode') != 1) {
		set_time_limit(0);
	}
	
	// Get the list of unique e-mail addresses which are subscribed to list(s) for which the product
	// is back in stock
	$email_addresses_query_raw = "
		SELECT
			bisns.email_address, bisns.name, c.customers_email_address, c.customers_firstname,
			c.customers_lastname
		FROM
			" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " bisns
		LEFT JOIN
			" . TABLE_PRODUCTS . " p
		ON
			p.products_id = bisns.product_id
		LEFT JOIN
			" . TABLE_CUSTOMERS . " c
		ON
			c.customers_id = bisns.customer_id
		WHERE
			p.products_quantity > 0
		GROUP BY
			email_address, customers_email_address
		ORDER BY
			email_address, customers_email_address";
	
	$email_addresses_result = $db->Execute($email_addresses_query_raw);
	
	$email_addresses_notified = array();
	
	while (!$email_addresses_result->EOF) {
		$customer_email_address = (!is_null($email_addresses_result->fields['email_address']) ?
			$email_addresses_result->fields['email_address'] :
			$email_addresses_result->fields['customers_email_address']);
		
		$customer_name = (!is_null($email_addresses_result->fields['customers_firstname']) ?
			$email_addresses_result->fields['customers_firstname'] . ' ' .
			$email_addresses_result->fields['customers_lastname'] :
			$email_addresses_result->fields['name']);
		
		// Has this customer been e-mailed yet?
		if (!array_key_exists($customer_email_address, $email_addresses_notified)) {
			// Get all the products for which this e-mail address is subscribed to a back in stock
			// notification list and for which the product is back in stock
			$products_query = "
				SELECT DISTINCT
					bisns.id, bisns.product_id, pd.products_name
				FROM
					" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " bisns
				LEFT JOIN
					" . TABLE_CUSTOMERS . " c
				ON
					c.customers_id = bisns.customer_id
				LEFT JOIN
					" . TABLE_PRODUCTS . " p
				ON
					p.products_id = bisns.product_id
				LEFT JOIN
					" . TABLE_PRODUCTS_DESCRIPTION . " pd
				ON
					pd.products_id = bisns.product_id
				WHERE
					pd.language_id = '" . $_SESSION['languages_id'] . "'
				AND
					p.products_quantity > 0
				AND
					(
					bisns.email_address = '" . $customer_email_address . "'
				OR
					c.customers_email_address = '" . $customer_email_address . "'
					);";
			
			$products_result = $db->Execute($products_query);
			
			$plain_text_msg = '';
			$html_msg = '';
			
			// Record the names of the products which have come back in stock since this user
			// joined their back in stock notification list(s)
			$products = array();
			
			while (!$products_result->EOF) {
				$products[] = array(
					'subscription_id' => $products_result->fields['id'],
					'product_id' => $products_result->fields['product_id'],
					'name' => $products_result->fields['products_name']
					);
				
				$product_type_result = $db->Execute("
					SELECT
						p.products_id,
						pt.type_handler
					FROM
						" . TABLE_PRODUCTS . " p
					LEFT JOIN
						" . TABLE_PRODUCT_TYPES . " pt
					ON
						pt.type_id = p.products_type
					WHERE
						p.products_id = '" . (int) $products_result->fields['product_id'] . "'");
				
				if (!$product_type_result->EOF &&
						!is_null($product_type_result->fields['type_handler']) &&
						strlen($product_type_result->fields['type_handler']) > 0) {
					$product_page = $product_type_result->fields['type_handler'] . '_info';
				} else {
					$product_page = 'product_info';
				}
				
				$plain_text_msg .= $products_result->fields['products_name'] . "\n\n" . EMAIL_LINK .
					zen_catalog_href_link($product_page, 'products_id=' .
					$products_result->fields['product_id']) . "\n\n\n";
				
				$html_msg .= '<p class="BackInStockNotificationProduct">' . '<a href="' .
					zen_catalog_href_link($product_page, 'products_id=' .
					$products_result->fields['product_id']) . '" target="_blank">' .
					htmlentities($products_result->fields['products_name'], ENT_COMPAT, CHARSET) .
					'</a></p>' . "\n";
				
				$products_result->MoveNext();
			}
			
			// Remove last three newlines from end of plain text message
			$plain_text_msg = substr($plain_text_msg, 0, strlen($plain_text_msg) - 3);
			
			$message_sent_or_skipped = true;
			
			if (!$test_mode || sizeof($email_addresses_notified) < 1) {
				$message_sent_or_skipped = sendBackInStockNotificationEmail(
					$customer_name, $customer_email_address, $plain_text_msg, $html_msg,
					(sizeof($products) > 1), $test_mode);
			}
			
			if ($message_sent_or_skipped) {
				$email_addresses_notified[strtolower($customer_email_address)] = array(
					'name' => $customer_name,
					'products' => $products
					);
			}
		}
		
		$email_addresses_result->MoveNext();
	}
	
	// Build list of addresses and products notifications were sent for, as well as a list of IDs
	// for the subscriptions (so they can be deleted)
	$output = '';
	$subscription_ids = array();
	
	$num_addresses_notified = sizeof($email_addresses_notified) ;
	
	if ($num_addresses_notified == 0) {
		$output = '<p>' . TEXT_PREVIEW_OR_SEND_OUTPUT_TITLE_NONE . "</p>\n";
	} else {
		if ($test_mode) {
			if ($num_addresses_notified == 1) {
				$output = '<p>' . TEXT_PREVIEW_OUTPUT_TITLE_SINGULAR . "</p>\n";
			} else {
				$output = '<p>' .
					sprintf(TEXT_PREVIEW_OUTPUT_TITLE_PLURAL, $num_addresses_notified) . "</p>\n";
			}
		} else {
			if ($num_addresses_notified == 1) {
				$output = '<p>' . TEXT_SEND_OUTPUT_TITLE_SINGULAR . "</p>\n";
			} else {
				$output = '<p>' .
					sprintf(TEXT_SEND_OUTPUT_TITLE_PLURAL, $num_addresses_notified) . "</p>\n";
			}
		}
		
		$output .= "<dl id=\"back-in-stock-notifications-output\">\n";
		
		foreach ($email_addresses_notified as $email_address => $info) {
			$output .= "\t<dt>" . htmlentities($info['name'], ENT_COMPAT, CHARSET) . ' &lt;' .
				$email_address . '&gt;</dt>' . "\n";
			
			foreach ($info['products'] as $product) {
				$output .= "\t<dd>" . htmlentities($product['name'], ENT_COMPAT, CHARSET) .
					'</dd>' . "\n";
				
				$subscription_ids[] = $product['subscription_id'];
			}
		}
		
		$output .= "</dl>\n";
		
		if (!$test_mode) {
			// Now delete the subscriptions from the database
			$subscription_ids_string = implode(',', $subscription_ids);
			
			$delete_subscriptions_query = "
				DELETE FROM
					" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . "
				WHERE
					id IN (" . $subscription_ids_string . ");";
			
			$delete_subscriptions_result = $db->Execute($delete_subscriptions_query);
		}
	}
	
	return $output;
}

// }}}


// {{{ expungeOutdatedSubscriptionsFromBackInStockNotificationsDB()

/**
 * Expunges any notification subscriptions for products which no longer exist.
 *
 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @return  none
 */
function expungeOutdatedSubscriptionsFromBackInStockNotificationsDB()
{
	global $db, $messageStack;
	
	$delete_subscriptions_query = "
		DELETE FROM
			" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . "
		WHERE
			product_id NOT IN (
				SELECT
					products_id
				FROM
					" . TABLE_PRODUCTS . "
				WHERE
					1 = 1
			);";
	
	$delete_subscriptions_result = $db->Execute($delete_subscriptions_query);
	
	$messageStack->add(sprintf(TEXT_DELETED_PRODUCTS_SUBSCRIPTIONS_REMOVED,
		mysql_affected_rows($db->link)), 'back_in_stock_notifications');
}

// }}}


// {{{ sendBackInStockNotificationEmail()

/**
 * Builds and sends an e-mail notifications to a user using the back in stock notification e-mail
 * template.
 *
 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @param   string    $name             The name of the person being e-mailed.
 * @param   string    $email            The e-mail address of the person being e-mailed.
 * @param   string    $plain_text_msg   The plain text version of the product notifications message.
 * @param   string    $html_msg         The HTML version of the product notifications message.
 * @param   boolean   $more_than_one    Whether more than one product is being notified about.
 * @param   boolean   $test_mode        Whether the e-mail should simply be sent to the admin.
 * @return  boolean   Whether or not the e-mail was sent successfully.
 */
function sendBackInStockNotificationEmail($name, $email, $plain_text_msg, $html_msg,
	$more_than_one = false, $test_mode = false)
{
	global $messageStack, $ENABLE_SSL;
	
	$plain_text_msg_parts['EMAIL_GREETING'] = sprintf(EMAIL_GREETING, $name);
	
	$html_msg_parts['EMAIL_GREETING'] =
		htmlentities(sprintf(EMAIL_GREETING, $name), ENT_COMPAT, CHARSET);
	
	if (!$more_than_one) {
		$plain_text_msg_parts['EMAIL_INTRO_1'] .= EMAIL_INTRO_SINGULAR1;
		$plain_text_msg_parts['EMAIL_INTRO_2'] .= EMAIL_INTRO_SINGULAR2;
		
		$html_msg_parts['EMAIL_INTRO_1'] .= EMAIL_INTRO_SINGULAR1;
		$html_msg_parts['EMAIL_INTRO_2'] .= EMAIL_INTRO_SINGULAR2;
	} else {
		$plain_text_msg_parts['EMAIL_INTRO_1'] .= EMAIL_INTRO_PLURAL1;
		$plain_text_msg_parts['EMAIL_INTRO_2'] .= EMAIL_INTRO_PLURAL2;
		
		$html_msg_parts['EMAIL_INTRO_1'] .= EMAIL_INTRO_PLURAL1;
		$html_msg_parts['EMAIL_INTRO_2'] .= EMAIL_INTRO_PLURAL2;
	}
	
	$ssl_status = "NONSSL";
	
	if ($ENABLE_SSL) {
		$ssl_status = "SSL";
	}
	
	$plain_text_msg_parts['STORE_URL'] = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
	$plain_text_msg_parts['STORE_ACCOUNT_URL'] =
		zen_catalog_href_link(FILENAME_ACCOUNT, '', $ssl_status);
	$plain_text_msg_parts['STORE_CONTACT_URL'] =
		zen_catalog_href_link(FILENAME_CONTACT_US, '', 'NONSSL');
	
	$html_msg_parts['STORE_URL'] = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
	$html_msg_parts['STORE_ACCOUNT_URL'] = zen_catalog_href_link(FILENAME_ACCOUNT, '', $ssl_status);
	$html_msg_parts['STORE_CONTACT_URL'] = zen_catalog_href_link(FILENAME_CONTACT_US, '', 'NONSSL');
	
	if (!$more_than_one) {
		$plain_text_msg_parts['PRODUCTS_DETAIL_TITLE'] = PRODUCTS_DETAIL_TITLE_SINGULAR;
		$html_msg_parts['PRODUCTS_DETAIL_TITLE'] = PRODUCTS_DETAIL_TITLE_SINGULAR;
	} else {
		$plain_text_msg_parts['PRODUCTS_DETAIL_TITLE'] = PRODUCTS_DETAIL_TITLE_PLURAL;
		$html_msg_parts['PRODUCTS_DETAIL_TITLE'] = PRODUCTS_DETAIL_TITLE_PLURAL;
	}
	
	$plain_text_msg_parts['PRODUCTS_DETAIL'] = $plain_text_msg;
	
	$html_msg_parts['PRODUCTS_DETAIL'] =
		'<table class="product-details" border="0" width="100%" cellspacing="0" cellpadding="2">' .
		$html_msg . '</table>';
	
	// Include disclaimer
	$plain_text_msg_parts['EMAIL_DISCLAIMER'] = "\n-----\n" . 
		sprintf(EMAIL_DISCLAIMER, STORE_OWNER_EMAIL_ADDRESS) . "\n\n";
	$plain_text_msg_parts['EMAIL_DISCLAIMER'] .= "\n-----\n" . EMAIL_FOOTER_COPYRIGHT . "\n\n";
	
	$html_msg_parts['EMAIL_DISCLAIMER'] = sprintf(EMAIL_DISCLAIMER, '<a href="mailto:' .
		STORE_OWNER_EMAIL_ADDRESS . '">'. STORE_OWNER_EMAIL_ADDRESS .' </a>');
	
	if ($test_mode) {
		// Only send e-mails to store owner when in test mode
		$email = EMAIL_FROM;
	}
	
	// Create the text version of the e-mail for Zen Cart's e-mail functionality
	$language_folder_path_part = (strtolower($_SESSION['languages_code']) == 'en') ? '' :
		strtolower($_SESSION['languages_code']) . '/';
    
	$template_file = DIR_FS_EMAIL_TEMPLATES . $language_folder_path_part .
		'email_template_back_in_stock_notification.txt';
	
	if (file_exists($template_file)) {
		// Use template file for current language
		$text_msg_source = file_get_contents($template_file);
	} else if ($language_folder_path_part != '') {
		// Non-english language being used but no template file exist for it, fall back to the
		// default english template
		$text_msg_source =
			file_get_contents(str_replace($language_folder_path_part, '', $template_file));
	}
	
	foreach ($plain_text_msg_parts as $key => $value) {
		$text_msg_source = str_replace('$' . $key, $value, $text_msg_source);
	}
	
	$error = zen_mail($name, $email, EMAIL_BACK_IN_STOCK_NOTIFICATIONS_SUBJECT, $text_msg_source,
		STORE_NAME, EMAIL_FROM, $html_msg_parts, 'back_in_stock_notification');
	
	if ($error != '') {
		$messageStack->add($error, 'back_in_stock_notifications');
		return false;
	}
	
	return true;
}

// }}}


// {{{ buildLinkToProductAdminPage()

/**
 * Builds a link to a Product's admin page, with the product's name limited to a particular number
 * of characters.
 *
 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @author  RubikIntegration team @ RubikIntegration.com
 * @param   string    $name            The name of the product.
 * @param   integer   $id              The ID of the product.
 * @param   integer   $products_type   The ID of the type for the product.
 * @return  string    The HTML link to the product's admin page.
 */
function buildLinkToProductAdminPage($name, $id, $products_type)
{
	global $zc_products;
	
	$type_admin_handler = $zc_products->get_admin_handler($products_type);
	
	$name_length = 55;
	
	$new_name = '<a href="' . zen_href_link($type_admin_handler, 'pID=' . $id . '&product_type=' .
		$products_type . '&action=new_product', 'NONSSL', true, true, false, false) . '" title="' .
		htmlentities($name, ENT_COMPAT, CHARSET) . '" target="_blank">' .
		htmlentities(substr($name, 0, $name_length), ENT_COMPAT, CHARSET) .
		(strlen($name) > $name_length ? '...' : '') . '</a>'; 
	
	return $new_name;
}

// }}}

?>