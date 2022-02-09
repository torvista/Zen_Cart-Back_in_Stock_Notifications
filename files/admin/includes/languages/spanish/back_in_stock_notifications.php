<?php

/**
 * Ceon Back In Stock Notifications Admin Language Definitions.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      author: torvista, Ana Bobes 2011-05-13
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @author      Tony Niemann <tony@loosechicken.com>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications.php 937 2012-02-10 11:42:20Z conor $
 */

define('BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE', 'Notificación de Reposición');

define('TEXT_ACTION_TO_PERFORM', 'Acción:');

define('TEXT_LIST_ALL_SUBSCRIBED_PRODUCTS', 'Listado de todos los productos con suscritores');
define('TEXT_LIST_ALL_SUBSCRIPTIONS', 'Listado de suscripciones, ordenado por producto y fecha de petición');
define('TEXT_PREVIEW_NOTIFICATION_EMAILS', 'Prueba: muestra un listado de los emails que van a ser enviados');
define('TEXT_SEND_NOTIFICATION_EMAILS', 'ENVIAR LOS EMAILS DE NOTIFICACIÓN');
define('TEXT_REMOVE_DELETED_PRODUCTS', 'Borrar suscripciones a productos obsoletos (que se han sido borrados de la base de datos)');

define('TEXT_PRODUCTS_WITH_SUBSCRIPTIONS', 'Los productos con suscritores');
define('TEXT_ALL_SUBSCRIPTIONS', 'Todos los suscripciones');

define('TABLE_HEADING_PRODUCT_NAME', 'Nombre del Producto');
define('TABLE_HEADING_PRODUCT_CATEGORY', 'Categoría');
define('TABLE_HEADING_NUM_SUBSCRIBERS', 'Num. Suscritores');
define('TABLE_HEADING_CURRENT_STOCK', 'Stock Actual');
define('TABLE_HEADING_DATE_SUBSCRIBED', 'Fecha de Suscripción');
define('TABLE_HEADING_CUSTOMER_NAME', 'Nombre del Cliente');
define('TABLE_HEADING_CUSTOMER_EMAIL', 'E-mail del Cliente ');

define('TEXT_SORT_BY_PRODUCT_NAME', 'Ordenar por Nombre del Producto');
define('TEXT_SORT_BY_PRODUCT_CATEGORY', 'Ordenar por Categoría');
define('TEXT_SORT_BY_NUM_SUBSCRIBERS', 'Ordenar por Cantidad de Suscritores');
define('TEXT_SORT_BY_CURRENT_STOCK', 'Ordenar por Nivel de Stock Actual');
define('TEXT_SORT_BY_DATE_SUBSCRIBED', 'Ordenar por Fecha de Suscripción');
define('TEXT_SORT_BY_CUSTOMER_NAME', 'Ordenar por Nombre del Cliente');
define('TEXT_SORT_BY_CUSTOMER_EMAIL', 'Ordenar por E-mail del Cliente');

define('TEXT_DISPLAY_NUMBER_OF_BACK_IN_STOCK_NOTIFICATIONS', 'Mostrando <b>%d</b> a <b>%d</b> (de <b>%d</b> suscripciones) ');
define('TEXT_SHOW_ALL', 'Mostrar todo');
define('TEXT_DISPLAY_BY_PAGE', 'Mostrar por página');

define('TEXT_SEND_OUTPUT_TITLE', 'Info. de Salidas');
define('TEXT_PREVIEW_OR_SEND_OUTPUT_TITLE_NONE', 'Actualmente no hay avisos para enviar.');
define('TEXT_PREVIEW_OUTPUT_TITLE_SINGULAR', 'Solamente se hubiera enviado <strong>un</strong> aviso en este momento. Un ejemplo de este aviso ha sido enviado al email del propietario de la tienda.');
define('TEXT_PREVIEW_OUTPUT_TITLE_PLURAL', '%s avisos se hubieran enviado en este momento. Un ejemplo de este aviso ha sido enviado al email del propietario de la tienda.');
define('TEXT_SEND_OUTPUT_TITLE_SINGULAR', 'Solamente se ha enviado un aviso. Los detalles son:');
define('TEXT_SEND_OUTPUT_TITLE_PLURAL', '%s avisos han sido enviados. Los detalles son:');

define('TEXT_DELETED_PRODUCTS_SUBSCRIPTIONS_REMOVED', '%s suscripcion(es) a productos obsoletos ha(n) sido(s) borrado(s).');

define('EMAIL_BACK_IN_STOCK_NOTIFICATIONS_SUBJECT', STORE_NAME . ' Notificación de Reposición');

define('EMAIL_GREETING', 'Estimado %s,');
define('EMAIL_INTRO_SINGULAR1', 'Ya está disponible el producto sobre el que ha solicitado que le avisemos.');
define('EMAIL_INTRO_SINGULAR2', '¡Revíselo antes de que no tengamos stock!');
define('EMAIL_INTRO_PLURAL1', 'Hemos repuesto varios productos sobre los que ha solicitado que le avisemos.');
define('EMAIL_INTRO_PLURAL2', '¡Revíselos antes de que no tengamos stock!');//original, unnecessary
define('PRODUCTS_DETAIL_TITLE_SINGULAR', 'Producto Disponible:');
define('PRODUCTS_DETAIL_TITLE_PLURAL', 'Productos Disponibles:');
define('EMAIL_CONTACT', 'For help with your testimonial submission, contáctenos: ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n\n");
define('EMAIL_GV_CLOSURE','Un saludo,' . "\n\n" . STORE_OWNER . "\nStore Owner\n\n". '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'.HTTP_SERVER . DIR_WS_CATALOG ."</a>\n\n");
define('EMAIL_DISCLAIMER_NEW_CUSTOMER', 'Está petición de recibir un aviso de reposición era hecho por usted o otro usuario de la tienda. Si no pediste un aviso o ha recibido este email en error, por favor envia un email a %s ');

define('TEXT_PLEASE_WAIT', 'Espere .. enviando los emails ..<br /><br />¡Por favor, no interrumpe este proceso!');
define('TEXT_FINISHED_SENDING_EMAILS', '¡Todos los emails han sido enviados!');

define('TEXT_AFTER_EMAIL_INSTRUCTIONS','<p>¡%s emails han sido enviados!</p><p>Este listado de los emails suscritos a este producto ha sido borrado.</p>');

define('EMAIL_LINK', 'Enlace: ');

?>