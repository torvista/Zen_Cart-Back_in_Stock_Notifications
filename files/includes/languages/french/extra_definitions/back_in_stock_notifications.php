<?php

/**
 * Ceon Back In Stock Notifications Language Definitions - Can be used on main Back In Stock
 * Notification page, on Product Info page or any of the Product Listing pages. Also used on main
 * Account page for info about existing subscriptions.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @author      Marie-Amelie Masnou <murimari@murimari.com>
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
define('BACK_IN_STOCK_NOTIFICATION_TEXT_PRODUCT_LISTING_ALREADY_SUBSCRIBED', '<br />Vous avez demandé à être tenu informé lors du réapprovisionnement de nos produits.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_PRODUCT_LISTING_FORM_LINK', '<br />Afin d\'être tenu informé du réapprovisionnement de ce produit, <a href="%s">cliquez ici</a>.');


/**
 * Text/HTML for other pages.
 */
define('BACK_IN_STOCK_NOTIFICATION_TEXT_ALREADY_SUBSCRIBED', 'Vous avez demandé à être tenu informé lors du réapprovisionnement de nos produits.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_LINK', 'Afin d\'être tenu informé du réapprovisionnement de ce produit, <a href="%s">cliquez ici</a>.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_TITLE', 'Soyez prévenu immédiatement du réapprovisionnement de ce produit!!');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_INTRO', 'Entrez vos coordonnées ci-dessous et nous vous enverrons un email de notification &ldquo;%s&rdquo; Produit réapprovisionné!');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_NOTICE', 'Vous ne recevrez pas d\'autre couriel de notre part mais serez uniquement notifié du réapprovisionnement de cet article!');

define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_NAME', 'Nom');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_EMAIL', 'Adresse E-mail');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_CONFIRM_EMAIL', 'Confirmez votre E-mail');

if (!defined('BUTTON_NOTIFY_ME_ALT')) {
	define('BUTTON_NOTIFY_ME_ALT', 'm\'avertir');
}

if (!defined('BUTTON_IMAGE_NOTIFY_ME')) {
	define('BUTTON_IMAGE_NOTIFY_ME', 'notify_me.png');
}

define('EMAIL_NOTIFICATIONS_BACK_IN_STOCK_NOTIFICATIONS', 'Résilier vos demandes de notifications.');
define('EMAIL_NOTIFICATIONS_NO_BACK_IN_STOCK_NOTIFICATIONS', 'Vous n\'êtes pas inscrit à la liste d\'information des réapprovisonnement.');

?>