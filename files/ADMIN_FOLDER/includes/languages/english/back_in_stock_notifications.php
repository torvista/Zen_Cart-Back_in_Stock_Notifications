<?php

declare(strict_types=1);

/**
 * Ceon Back In Stock Notifications Admin Language Definitions.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @author      Tony Niemann <tony@loosechicken.com>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        https://www.ceon.net
 * @license     https://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications.php 2023 11 11 torvista $
 */

define('BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE', 'Ceon Back In Stock Notifications');

define('TEXT_ACTION_TO_PERFORM', 'Action to Perform:');

define('TEXT_LIST_ALL_SUBSCRIBED_PRODUCTS', 'List products with subscriptions');
define('TEXT_LIST_ALL_SUBSCRIPTIONS', 'List all subscriptions');
define('TEXT_PREVIEW_NOTIFICATION_EMAILS', 'Perform a test run of notification e-mails to be sent');
define('TEXT_SEND_NOTIFICATION_EMAILS', 'SEND NOTIFICATION E-MAILS');
define('TEXT_REMOVE_DELETED_PRODUCTS', 'Remove subscriptions for deleted products from database');

define('TEXT_PRODUCTS_WITH_SUBSCRIPTIONS', 'Products with subscriptions');
define('TEXT_ALL_SUBSCRIPTIONS', 'All subscriptions');

define('TABLE_HEADING_PRODUCT_ID', 'ID');
define('TABLE_HEADING_PRODUCT_NAME', 'Product Name');
define('TABLE_HEADING_PRODUCT_CATEGORY', 'Category');
define('TABLE_HEADING_NUM_SUBSCRIBERS', 'Subscribers');
define('TABLE_HEADING_CURRENT_STOCK', 'Stock');
define('TABLE_HEADING_DATE_SUBSCRIBED', 'Date Subscribed');
define('TABLE_HEADING_CUSTOMER_NAME', 'Customer');
define('TABLE_HEADING_CUSTOMER_EMAIL', 'Email');

define('TEXT_SORT_BY_PRODUCT_NAME', 'Sort by Product Name');
define('TEXT_SORT_BY_PRODUCT_CATEGORY', 'Sort by Category');
define('TEXT_SORT_BY_NUM_SUBSCRIBERS', 'Sort by Number of Subscribers');
define('TEXT_SORT_BY_CURRENT_STOCK', 'Sort by Current Stock Level');
define('TEXT_SORT_BY_DATE_SUBSCRIBED', 'Sort by Date Subscribed');
define('TEXT_SORT_BY_CUSTOMER_NAME', 'Sort by Customer\'s Name');
define('TEXT_SORT_BY_CUSTOMER_EMAIL', 'Sort by Customer\'s E-mail Address');

define('TEXT_DISPLAY_NUMBER_OF_BACK_IN_STOCK_NOTIFICATIONS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> subscriptions) ');
define('TEXT_SHOW_ALL', 'Show All');
define('TEXT_DISPLAY_BY_PAGE', 'Paged Listing');

define('TEXT_SEND_OUTPUT_TITLE', 'Send Output');
define('TEXT_PREVIEW_OR_SEND_OUTPUT_TITLE_NONE', 'There are no notifications to be sent at this time.');
define('TEXT_PREVIEW_OUTPUT_TITLE_SINGULAR', 'Only one notification would have been sent at this time. An example of this notification has <span class="u"><strong>already</strong></span> been sent (but using the currently-selected Admin language) to the <strong>store owner\'s e-mail address</strong>.');
define('TEXT_PREVIEW_OUTPUT_TITLE_PLURAL', '%s notifications would have been sent at this time. An example of the first notification has <span class="u"><strong>already</strong></span> been sent (but using the currently-selected Admin language) to the <strong>store owner\'s e-mail address</strong>.');
define('TEXT_SEND_OUTPUT_TITLE_SINGULAR', 'Only one notification was sent. Its details are as follows...');
define('TEXT_SEND_OUTPUT_TITLE_PLURAL', '%s notifications were sent. Their details are as follows...');

define('TEXT_DELETED_PRODUCTS_SUBSCRIPTIONS_REMOVED', '%s subscription(s) for deleted products removed.');

define('EMAIL_BACK_IN_STOCK_NOTIFICATIONS_SUBJECT', STORE_NAME . ' Back In Stock Notification');

define('EMAIL_GREETING', 'Dear %s,');
define('EMAIL_INTRO_SINGULAR1', 'We now have in stock a product you asked to be notified about.');
define('EMAIL_INTRO_SINGULAR2', '');
define('EMAIL_INTRO_PLURAL1', 'We have restocked several products you asked to be notified about.');
define('EMAIL_INTRO_PLURAL2', 'Please check them out before they go out of stock again!');
define('PRODUCTS_DETAIL_TITLE_SINGULAR', 'Product Back In Stock:');
define('PRODUCTS_DETAIL_TITLE_PLURAL', 'Products Back In Stock:');
define('EMAIL_CONTACT', 'For help with your testimonial submission, please contact us: ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n\n");
define('EMAIL_GV_CLOSURE','Sincerely,' . "\n\n" . STORE_OWNER . "\nStore Owner\n\n". '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'.HTTP_SERVER . DIR_WS_CATALOG ."</a>\n\n");
define('EMAIL_DISCLAIMER_NEW_CUSTOMER', 'This restock request(s) was submitted to us by you or by one of our users. If you did not submit a request(s), or feel that you have received this email in error, please send an email to %s ');
define('EMAIL_LINK', 'Link: ');

define('TEXT_PLEASE_WAIT', 'Please wait .. sending emails ..<br><br>Do not interrupt this process!');
define('TEXT_FINISHED_SENDING_EMAILS', 'Finished sending e-mails!');

define('TEXT_AFTER_EMAIL_INSTRUCTIONS','<p>%s emails sent!</p><p>The email addresses which were subscribed to be notified when this product was back in stock <strong>have now been deleted</strong> from the Back In Stock Notification List for this product!</p>');

//added for 3.2.2
define('MODULE_COPYRIGHT', 'Module &copy; Copyright 2004-2011');
define('MODULE_VERSION', 'Module Version: ');
define('CHECK_FOR_UPDATES', 'Check for Updates');
define('TABLE_HEADING_PRODUCT_MODEL', 'Model');
define('TEXT_SORT_BY_PRODUCT_MODEL', 'Sort by Product Model');
define('TABLE_HEADING_CUSTOMER_LANGUAGES_ID', 'Language');
define('TEXT_SORT_BY_LANGUAGE_ID', 'Sort by Language');
define('TEXT_TEST_OUTPUT', '<h5>These are <strong>ALL</strong> the emails that are <strong>DUE</strong> to be sent (in both languages).</h5><p>In the real sending process (Option 4), <strong>ONLY</strong> emails matching the currently-selected admin-language will be sent (or via cron with ?action=send&amp;option=4&amp;language=es etc.) to ensure the correct language constants are used with the email template.<br>Consequently, after executing Option 4 once, the Admin language MUST then be changed to the next language to automatically reload this page and re-execute Option 4 again for the remaining emails in the second language.</p>');
define('TEXT_LANGUAGE', 'Language');
define('TEXT_NOTE_URI_MAPPING','Note: CEON URI Mapping static links are language-dependant and can only be displayed here in the current Admin language. The correct language is used in the email.');
define('MESSAGE_STACK_BISN_SUBS_NOT_DELETED', 'BISN subscriptions not deleted (repeated test mode)');
define('TEXT_TITLE_GOTO_CATEGORY','go to admin category listing');
define('TEXT_TITLE_EDIT_PRODUCT','edit product');
define('TEXT_TITLE_VIEW_PRODUCT','view product in shop');
define('TEXT_TITLE_DELETE_ALL','delete ALL subscriptions for: %s');
define('TEXT_TITLE_SEND_EMAIL','send email');
define('TEXT_TITLE_VIEW_CUSTOMER','view customer entry');
define('TEXT_PRODUCT_ID_NOT_FOUND', 'product ID#%u not found (deleted)');
define('TEXT_SUBMIT_GO','GO!');
define('TABLE_HEADING_DELETE_SUBSCRIPTIONS', '');
define('TEXT_DELETE_ALL_SUBSCRIPTIONS_CONFIRM', 'Are you sure you want to delete ALL the subscription(s) for: %s?');
define('TEXT_DELETE_SUBSCRIPTION_CONFIRM', 'Are you sure you want to delete this subscription for: %s?');
define('TEXT_DEBUG_NO_DELETE_SUBSCRIPTIONS', '<p class="messageStackError">$delete_customer_subscriptions = false (for repeat testing): Email notifications WILL be sent to the subscribers (unless overridden) but their subscriptions will NOT be deleted</p><br>');
define('ERROR_TEST_MODE','In Test Mode - showing emails to be sent although there are sending errors');//shown in message stack when there are sending errors. Emails to be sent should not be listed due to the sending errors but I decided to allow the listing in test mode to permit offline development.
define('TEXT_NOTE_OPTION_1','<h5>This listing shows only the base product: it does not (yet...) list the product variants as individual products.<br>Back In Stock Notifications will be sent to ALL subscribers for the base product/all variants as shown here.</h5><h6>Go to Option 2 for details of subscribed product variants.</h6>');
define('TEXT_NOTE_OPTION_2','<h5>Although this listing shows the product variants as individual products, this is for information only: the Back In Stock Notifications will be sent to ALL subscribers for the base product.</h5>');
