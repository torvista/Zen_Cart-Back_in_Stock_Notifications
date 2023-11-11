<?php

declare(strict_types=1);
/**
 * Ceon Back In Stock Notifications English Language Definitions - Can be used on main Back In Stock
 * Notification page, on Product Info page or any of the Product Listing pages. Also used on main
 * Account page for info about existing subscriptions.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        https://www.ceon.net
 * @license     https://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications.php 2023-11-11 torvista
 */

/**
 * HTML for link on Product Listing pages. (Can be used to display a "subscribe" image etc.).
 * Note that the link is added in place of %s, %s must be present for the link to work!
 */
define('BACK_IN_STOCK_NOTIFICATION_TEXT_PRODUCT_LISTING_ALREADY_SUBSCRIBED', '<br>You have requested to be notified when this product is back in stock.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_PRODUCT_LISTING_FORM_LINK', '<br>To be notified when this product is back in stock please <a href="%s">click here</a>.');


/**
 * Text/HTML for other pages.
 */
define('BACK_IN_STOCK_NOTIFICATION_TEXT_ALREADY_SUBSCRIBED', 'You have requested to be notified when this product is back in stock.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_LINK', 'To be notified when this product is back in stock please <a href="%s">click here</a>.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_TITLE', 'Let us notify you when this product is back in stock');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_INTRO', 'Enter your details below and we will send you an e-mail when this product is back in stock:<span style="font-size:medium">%s</span>');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_INTRO_ATTS', 'Enter your details below and we will send you an e-mail when the selected product is back in stock:<span style="font-size:medium">%s</span>');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_NOTICE', 'We will not send you any other e-mails or add you to our newsletter list, you will only be e-mailed about <b>this</b> product.');

define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_NAME', 'Name');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_EMAIL', 'E-mail');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_CONFIRM_EMAIL', 'Confirm E-mail');

if (!defined('BUTTON_NOTIFY_ME_ALT')) {
	define('BUTTON_NOTIFY_ME_ALT', 'Notify Me');
}

if (!defined('BUTTON_IMAGE_NOTIFY_ME')) {
	define('BUTTON_IMAGE_NOTIFY_ME', 'button_ceon_bisn_notify_me.png');
}

define('EMAIL_NOTIFICATIONS_BACK_IN_STOCK_NOTIFICATIONS', 'Unsubscribe from Back In Stock Notification Lists.');
define('EMAIL_NOTIFICATIONS_NO_BACK_IN_STOCK_NOTIFICATIONS', 'You are not currently subscribed to any Back In Stock Notification Lists.');

