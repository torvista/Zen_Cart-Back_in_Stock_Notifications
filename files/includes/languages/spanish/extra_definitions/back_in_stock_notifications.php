<?php

/**
 * Ceon Back In Stock Notifications Language Definitions - Can be used on main Back In Stock
 * Notification page, on Product Info page or any of the Product Listing pages. Also used on main
 * Account page for info about existing subscriptions.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      torvista, Ana Bobes 2011-05-13
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
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
define('BACK_IN_STOCK_NOTIFICATION_TEXT_PRODUCT_LISTING_ALREADY_SUBSCRIBED', '<br />Ha solicitado que se le notifique cuanto este producto vuelva a estar disponible.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_PRODUCT_LISTING_FORM_LINK', 'Si quiere recibir una notificación cuando el producto vuelva a estar disponible <a href="%s">pinche aquí</a>');


/**
 * Text/HTML for other pages.
 */
define('BACK_IN_STOCK_NOTIFICATION_TEXT_ALREADY_SUBSCRIBED', 'Ha solicitado que se le notifique cuando el producto vuelva a estar disponible.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_LINK', 'Si quiere recibir una notificación cuando este producto vuelva a estar disponible <a href="%s">pinche aquí</a>');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_TITLE', '¡Permítanos notificarle cuando este producto vuelva a estar en stock!');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_INTRO', 'Introduzca sus datos en las casillas inferiores y le enviaremos un email cuando<br  /><strong>&ldquo;%s&rdquo;</strong> se reponga.');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_NOTICE', 'No le enviaremos ningún otro email o boletines. Sólo se le informará sobre la disponibilidad del producto.');

define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_NAME', 'Nombre');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_EMAIL', 'E-mail');
define('BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_CONFIRM_EMAIL', 'Confirmar E-mail');

if (!defined('BUTTON_NOTIFY_ME_ALT')) {
	define('BUTTON_NOTIFY_ME_ALT', 'Avísame');
}

if (!defined('BUTTON_IMAGE_NOTIFY_ME')) {
	define('BUTTON_IMAGE_NOTIFY_ME', 'button_ceon_notify_me.gif');
}

define('EMAIL_NOTIFICATIONS_BACK_IN_STOCK_NOTIFICATIONS', 'Darse de baja en la notificación de reposición.');
define('EMAIL_NOTIFICATIONS_NO_BACK_IN_STOCK_NOTIFICATIONS', 'No está suscrito a ningún listado de notificaciones.');

?>