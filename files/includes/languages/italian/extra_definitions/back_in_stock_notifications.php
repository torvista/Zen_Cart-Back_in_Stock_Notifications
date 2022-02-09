<?php

/**
 * Ceon Back In Stock Notifications Language Definitions - Can be used on main Back In Stock
 * Notification page, on Product Info page or any of the Product Listing pages. Also used on main
 * Account page for info about existing subscriptions.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @author      Claudio
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications.php 935 2012-02-06 14:08:25Z conor $
 */

/**
 * HTML for link on Product Listing pages. (Can be used to display a "subscribe" image etc.).
 * Note that the link is added in place of %s, %s must be present for the link to work!
 */
define('BACK_IN_STOCK_NOTIFICATION_TEXT_PRODUCT_LISTING_ALREADY_SUBSCRIBED', '<br />Hai chiesto di essere informato quando il prodotto torna in stock.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_PRODUCT_LISTING_FORM_LINK', '<br />Per essere avvisato quando questo prodotto è di nuovo disponibile prego <a href="%s">clicca qui</a>.');


/**
 * Text/HTML for other pages.
 */
define('BACK_IN_STOCK_NOTIFICATION_TEXT_ALREADY_SUBSCRIBED', 'Hai chiesto di essere informato quando il prodotto torna in stock.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_LINK', 'Per essere avvisato quando questo prodotto è di nuovo disponibile prego <a href="%s">clicca qui</a>.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_TITLE', 'Ti informeremo quando il prodotto torna in stock!');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_INTRO', 'Semplicemente inserisci i tuoi dati qui sotto e ti invieremo una e-mail quando &ldquo;%s&rdquo; è disponibile!');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_NOTICE', 'Non ti invieremo nessun\'altro tipo di email,tantomeno ti aggiungeremo alla nostra newsletter, sarai solo avvisato su questo prodotto!');

define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_NAME', 'Nome');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_EMAIL', 'Indirizzo E-mail');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_CONFIRM_EMAIL', 'Conferma E-mail');

if (!defined('BUTTON_NOTIFY_ME_ALT')) {
	define('BUTTON_NOTIFY_ME_ALT', 'Avvisami');
}

if (!defined('BUTTON_IMAGE_NOTIFY_ME')) {
	define('BUTTON_IMAGE_NOTIFY_ME', 'notify_me.png');
}

define('EMAIL_NOTIFICATIONS_BACK_IN_STOCK_NOTIFICATIONS', 'Cancellami dalla lista di notifica rifornimento.');
define('EMAIL_NOTIFICATIONS_NO_BACK_IN_STOCK_NOTIFICATIONS', 'Non sei attualmente iscritto ad alcuna lista di notifica rifornimento.');

?>