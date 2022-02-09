<?php

/**
 * Ceon Back In Stock Notifications Admin Language Definitions.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @author      Claudio
 * @author      Tony Niemann <tony@loosechicken.com>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications.php 937 2012-02-10 11:42:20Z conor $
 */

define('BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE', 'Back In Stock Notifications');

define('TEXT_ACTION_TO_PERFORM', 'Operazione da eseguire:');

define('TEXT_LIST_ALL_SUBSCRIBED_PRODUCTS', 'Elenco di tutti i prodotti con sottoscrizione');
define('TEXT_LIST_ALL_SUBSCRIPTIONS', 'Elenco sottoscrizioni, ordinati per prodotto e data di sottoscrizione');
define('TEXT_PREVIEW_NOTIFICATION_EMAILS', 'Eseguire un test di notifica da inviare tramite email');
define('TEXT_SEND_NOTIFICATION_EMAILS', 'Inviare notifiche via e-mail per tutti i prodotti sottoscritti, che sono tornati in magazzino');
define('TEXT_REMOVE_DELETED_PRODUCTS', 'Rimuovere sottoscrizioni per i prodotti eliminati dal database');

define('TEXT_PRODUCTS_WITH_SUBSCRIPTIONS', 'Prodotti con sottoscrizione');
define('TEXT_ALL_SUBSCRIPTIONS', 'Ordinati per prodotto e data di sottoscrizione');

define('TABLE_HEADING_PRODUCT_NAME', 'Nome del Prodotto');
define('TABLE_HEADING_PRODUCT_CATEGORY', 'Categoria');
define('TABLE_HEADING_NUM_SUBSCRIBERS', 'Num Iscritti');
define('TABLE_HEADING_CURRENT_STOCK', 'Azione Correnti');
define('TABLE_HEADING_DATE_SUBSCRIBED', 'Data Iscrizione');
define('TABLE_HEADING_CUSTOMER_NAME', 'Nome Cliente');
define('TABLE_HEADING_CUSTOMER_EMAIL', 'Indirizzo Email Cliente');

define('TEXT_SORT_BY_PRODUCT_NAME', 'Ordina per Nome del Prodotto');
define('TEXT_SORT_BY_PRODUCT_CATEGORY', 'Ordina per Categoria');
define('TEXT_SORT_BY_NUM_SUBSCRIBERS', 'Ordina per Numero di Iscritti');
define('TEXT_SORT_BY_CURRENT_STOCK', 'Ordina per Livello di Azione Correnti');
define('TEXT_SORT_BY_DATE_SUBSCRIBED', 'Ordina per Data Iscrizione');
define('TEXT_SORT_BY_CUSTOMER_NAME', 'Ordina per Nome Cliente');
define('TEXT_SORT_BY_CUSTOMER_EMAIL', 'Ordina per Indirizzo Email Cliente');

define('TEXT_DISPLAY_NUMBER_OF_BACK_IN_STOCK_NOTIFICATIONS', 'Mostrare <b>%d</b> fino <b>%d</b> (di <b>%d</b> iscritti a back in stock notification) ');
define('TEXT_SHOW_ALL', 'Mostra Tutti');
define('TEXT_DISPLAY_BY_PAGE', 'Esposizione da Page');

define('TEXT_SEND_OUTPUT_TITLE', 'Inviare Rendimento');
define('TEXT_PREVIEW_OR_SEND_OUTPUT_TITLE_NONE', 'Non ci sono notifiche da inviare in questo momento.');
define('TEXT_PREVIEW_OUTPUT_TITLE_SINGULAR', 'Una sola notifica sarebbe stata inviata in questo momento. Un esempio di questa notifica è stata inviata all\'indirizzo email del proprietario.');
define('TEXT_PREVIEW_OUTPUT_TITLE_PLURAL', '%s notifications would have been sent at this time. An example of the first notification has been sent to the store owner\'s e-mail address.');
define('TEXT_SEND_OUTPUT_TITLE_SINGULAR', 'Solo una notifica è stata inviata. I dettagli sono i seguenti...');
define('TEXT_SEND_OUTPUT_TITLE_PLURAL', '%s notifiche sono state inviate. I dettagli sono i seguenti...');

define('TEXT_DELETED_PRODUCTS_SUBSCRIPTIONS_REMOVED', '%s iscrizione(i) per i prodotti da eliminare.');

define('EMAIL_BACK_IN_STOCK_NOTIFICATIONS_SUBJECT', STORE_NAME . ' Avviso di ritorno a magazzino');

define('EMAIL_GREETING', 'Gentile %s,');
define('EMAIL_INTRO_SINGULAR1', 'Abbiamo rifornito un prodotto del quale hai chiesto di essere avvisato.');
define('EMAIL_INTRO_SINGULAR2', 'Affrettati,prima che vada esaurito di nuovo!');
define('EMAIL_INTRO_PLURAL1', 'Abbiamo rifornito i diversi prodotti dei quali hai chiesto di essere avvisato.');
define('EMAIL_INTRO_PLURAL2', 'Affrettati,prima che vadano esauriti di nuovo!');
define('PRODUCTS_DETAIL_TITLE_SINGULAR', 'Prodotto rifornito');
define('PRODUCTS_DETAIL_TITLE_PLURAL', 'Prodotti riforniti');
define('EMAIL_CONTACT', 'Per qualsiasi aiuto, ti invitiamo a contattarci: ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n\n");
define('EMAIL_GV_CLOSURE','Cordiali saluti,' . "\n\n" . STORE_OWNER . "\nStore Owner\n\n". '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'.HTTP_SERVER . DIR_WS_CATALOG ."</a>\n\n");
define('EMAIL_DISCLAIMER_NEW_CUSTOMER', 'Questa richiesta di avviso ci è stata presentata da te o da uno dei nostri utenti. Se non ha presentato una richiesta, o pensi di aver ricevuto questo messaggio per errore, inviacu una email a %s ');

define('TEXT_PLEASE_WAIT', 'Attendere prego .. invio email ..<br /><br />Si prega di non interrompere questo processo!');
define('TEXT_FINISHED_SENDING_EMAILS', 'INVIO TERMINATO!');

define('TEXT_AFTER_EMAIL_INSTRUCTIONS','<p>%s e-mail inviata!</p><p>Gli indirizzi email che sono stati sottoscritti per essere avvisati quando questo prodotto era di nuovo in stock <strong>sono stati eliminati</strong> dalla lista di notifica per questo prodotto!</p>');

define('EMAIL_LINK', 'Enlace: ');

?>