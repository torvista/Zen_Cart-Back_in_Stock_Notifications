<?php

/**
 * Ceon Back In Stock Notifications Admin Language Definitions.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @author      Tony Niemann <tony@loosechicken.com>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications.php 937 2012-02-10 11:42:20Z conor $
 */

define('BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE', 'Ceon Back In Stock Notifications');

define('TEXT_ACTION_TO_PERFORM', 'Action to Perform:');

define('TEXT_LIST_ALL_SUBSCRIBED_PRODUCTS', 'List all products with subscriptions');
define('TEXT_LIST_ALL_SUBSCRIPTIONS', 'List all subscriptions');
define('TEXT_PREVIEW_NOTIFICATION_EMAILS', 'Perform a test run of notification e-mails to be sent');
define('TEXT_SEND_NOTIFICATION_EMAILS', 'SEND NOTIFICATION E-MAILS for all subscribed products which are back in stock');
define('TEXT_REMOVE_DELETED_PRODUCTS', 'Remove subscriptions for deleted products from database');

define('TEXT_PRODUCTS_WITH_SUBSCRIPTIONS', 'Products with subscriptions');
define('TEXT_ALL_SUBSCRIPTIONS', 'All subscriptions');

define('TABLE_HEADING_PRODUCT_NAME', 'Product Name');
define('TABLE_HEADING_PRODUCT_CATEGORY', 'Category');
define('TABLE_HEADING_NUM_SUBSCRIBERS', 'Num Subscribers');
define('TABLE_HEADING_CURRENT_STOCK', 'Current Stock');
define('TABLE_HEADING_DATE_SUBSCRIBED', 'Date Subscribed');
define('TABLE_HEADING_CUSTOMER_NAME', 'Customer\'s Name');
define('TABLE_HEADING_CUSTOMER_EMAIL', 'Customer\'s E-mail Address');

define('TEXT_SORT_BY_PRODUCT_NAME', 'Sort by Product Name');
define('TEXT_SORT_BY_PRODUCT_CATEGORY', 'Sort by Category');
define('TEXT_SORT_BY_NUM_SUBSCRIBERS', 'Sort by Number of Subscribers');
define('TEXT_SORT_BY_CURRENT_STOCK', 'Sort by Current Stock Level');
define('TEXT_SORT_BY_DATE_SUBSCRIBED', 'Sort by Date Subscribed');
define('TEXT_SORT_BY_CUSTOMER_NAME', 'Sort by Customer\'s Name');
define('TEXT_SORT_BY_CUSTOMER_EMAIL', 'Sort by Customer\'s E-mail Address');

define('TEXT_DISPLAY_NUMBER_OF_BACK_IN_STOCK_NOTIFICATIONS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> subscriptions) ');
define('TEXT_SHOW_ALL', 'Show All');
define('TEXT_DISPLAY_BY_PAGE', 'Display by Page');

define('TEXT_SEND_OUTPUT_TITLE', 'Send Output');
define('TEXT_PREVIEW_OR_SEND_OUTPUT_TITLE_NONE', 'There are no notifications to be sent at this time.');
define('TEXT_PREVIEW_OUTPUT_TITLE_SINGULAR', 'Only one notification would have been sent at this time. An example of this notification has been sent to the store owner\'s e-mail address.');
define('TEXT_PREVIEW_OUTPUT_TITLE_PLURAL', '%s notifications would have been sent at this time. An example of the first notification has been sent to the store owner\'s e-mail address.');
define('TEXT_SEND_OUTPUT_TITLE_SINGULAR', 'Only one notification was sent. Its details are as follows...');
define('TEXT_SEND_OUTPUT_TITLE_PLURAL', '%s notifications were sent. Their details are as follows...');

define('TEXT_DELETED_PRODUCTS_SUBSCRIPTIONS_REMOVED', '%s subscription(s) for deleted products removed.');

define('EMAIL_BACK_IN_STOCK_NOTIFICATIONS_SUBJECT', STORE_NAME . ' Back In Stock Notification');

define('EMAIL_GREETING', 'Dear %s,');
define('EMAIL_INTRO_SINGULAR1', 'We have restocked a product you asked to be notified about.');
define('EMAIL_INTRO_SINGULAR2', 'Please check it out before it goes out of stock again!');
define('EMAIL_INTRO_PLURAL1', 'We have restocked several products you asked to be notified about.');
define('EMAIL_INTRO_PLURAL2', 'Please check them out before they go out of stock again!');
define('PRODUCTS_DETAIL_TITLE_SINGULAR', 'Product Back In Stock');
define('PRODUCTS_DETAIL_TITLE_PLURAL', 'Products Back In Stock');
define('EMAIL_CONTACT', 'For help with your testimonial submission, please contact us: ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n\n");
define('EMAIL_GV_CLOSURE','Sincerely,' . "\n\n" . STORE_OWNER . "\nStore Owner\n\n". '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'.HTTP_SERVER . DIR_WS_CATALOG ."</a>\n\n");
define('EMAIL_DISCLAIMER_NEW_CUSTOMER', 'This restock request(s) was submitted to us by you or by one of our users. If you did not submit a request(s), or feel that you have received this email in error, please send an email to %s ');

define('TEXT_PLEASE_WAIT', 'Please wait .. sending emails ..<br /><br />Please do not interrupt this process!');
define('TEXT_FINISHED_SENDING_EMAILS', 'Finished sending e-mails!');

define('TEXT_AFTER_EMAIL_INSTRUCTIONS','<p>%s emails sent!</p><p>The email addresses which were subscribed to be notified when this product was back in stock <strong>have now been deleted</strong> from the Back In Stock Notification List for this product!</p>');

define('EMAIL_LINK', 'Link: ');

?>