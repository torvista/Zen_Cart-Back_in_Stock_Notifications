<?php

/**
 * Ceon Back In Stock Notifications Language Definitions - Can be used on main Back In Stock
 * Notification page, on Product Info page or any of the Product Listing pages. Also used on main
 * Account page for info about existing subscriptions.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications.php 2022-02-16 torvista
 */

define('BACK_IN_STOCK_NOTIFICATION_TEXT_ALREADY_SUBSCRIBED', 'You are already subscribed to be notified when this product is back in stock.');
if (BACK_IN_STOCK_REQUIRES_LOGIN === '1') {
    //if logged in: subscription link adds subscription (no form needed)
    //if not logged in: redirects to login/account creation page
  define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_LINK', 'Customers with accounts may be notified when this product is back in stock, please <a href="%s">click here</a>.');
} else {
  define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_LINK', 'To be notified when this product is back in stock please <a href="%s">click here</a>.');
}
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_TITLE', 'Product Back in Stock Notification');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_INTRO', 'Enter your details below and you will automatically notified by e-mail when &ldquo;%s&rdquo; is back in stock.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_NOTICE', 'We will not send you any other e-mails or add you to our newsletter, you will only be e-mailed about this product.');

define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_NAME', 'Name');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_EMAIL', 'E-mail Address');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_CONFIRM_EMAIL', 'Confirm E-mail');

if (!defined('BUTTON_NOTIFY_ME_ALT')) {
	define('BUTTON_NOTIFY_ME_ALT', 'Notify Me');
}

if (!defined('BUTTON_IMAGE_NOTIFY_ME')) {
	define('BUTTON_IMAGE_NOTIFY_ME', 'notify_me.png');
}

define('EMAIL_NOTIFICATIONS_BACK_IN_STOCK_NOTIFICATIONS', 'Unsubscribe from Back In Stock Notification Lists.');
define('EMAIL_NOTIFICATIONS_NO_BACK_IN_STOCK_NOTIFICATIONS', 'You are not currently subscribed to any Back In Stock Notification Lists.');

define('EMAIL_TEXT_HEADER',' ');
define('EMAIL_TEXT_FROM',' ');

