<?php

/**
 * Back In Stock Notification Language Definitions - Can be used on main Back In Stock Notification
 * page or on Product Info page. Also used on main account page for info about existing
 * subscriptions.
 *
 * @author     Conor Kerr <back_in_stock_notifications@dev.ceon.net>
 * @copyright  Copyright 2004-2008 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/back_in_stock_notifications
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: back_in_stock_notifications.php 716 2008-07-21 23:23:41Z conor $
 */

define('BACK_IN_STOCK_NOTIFICATION_TEXT_ALREADY_SUBSCRIBED', 'You have requested to be notified when this product is back in stock.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_LINK', 'To be notified when this product is back in stock please <a href="%s">click here</a>.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_TITLE', 'Let us notify you when this product is back in stock!');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_INTRO', 'Simply enter your details below and we will send you an e-mail when &ldquo;%s&rdquo; is back in stock!');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_NOTICE', 'We will not send you any other e-mails or add you to our newsletter, you will only be e-mailed about this product!');

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

?>