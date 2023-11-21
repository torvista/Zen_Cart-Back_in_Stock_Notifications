<?php

declare(strict_types=1);

/** phpstorm inspections
 * @var queryFactory $db
 * @var messageStack $messageStack
 */
/**
 * Ceon Back In Stock Notifications Functions.
 *
 * Main functions used to perform primary operations of the module. Can be used within a cron script
 * to automate Back In Stock notification functionality.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2008 RubikIntegration team @ RubikIntegration.com
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        https://www.ceon.net
 * @license     https://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: plugin_bisn_functions.php 2023-11-12 torvista
 */

/**
 * Sends (or pretends to send) e-mail notifications to all users subscribed to back in stock
 * notification lists for which the product is back in stock.
 *
 * @param  int  $languages_id  Language id to select email sending language and constants
 * @param  bool  $test_mode  Flag to indicate if e-mails should actually be sent or just a sample e-mail generated for the admin for test purposes.
 * @param  bool  $delete_subscriptions  Override for using the real sending option, but repeatedly for testing
 * @return  string    Information about the customers e-mailed (if any).
 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 */

function sendBackInStockNotifications(int $languages_id, bool $test_mode = false, $delete_subscriptions = true): string
{
    global $db;

    if (ini_get('safe_mode') != 1) {
        set_time_limit(0);
    }

    // Get the list of unique e-mail addresses which are subscribed to list(s) for which the product is back in stock
    $email_addresses_query_raw = '
      SELECT
         bisns.email_address, bisns.name, bisns.languages_id, c.customers_email_address, c.customers_firstname,
         c.customers_lastname
      FROM
         ' . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . ' bisns
      LEFT JOIN
         ' . TABLE_PRODUCTS . ' p
      ON
         p.products_id = bisns.product_id
      LEFT JOIN
         ' . TABLE_CUSTOMERS . ' c
      ON
         c.customers_id = bisns.customer_id
      WHERE
         p.products_quantity > 0
      AND
          bisns.languages_id = ' . $languages_id . '
      GROUP BY
         bisns.email_address, c.customers_email_address, bisns.name, bisns.languages_id, c.customers_firstname, c.customers_lastname
      ORDER BY
         bisns.email_address, c.customers_email_address';

    $email_addresses_result = $db->Execute($email_addresses_query_raw);

    $email_addresses_notified = [];

    foreach ($email_addresses_result as $email_address_result) {
        $customer_email_address = (!is_null($email_address_result['email_address']) ?
            $email_address_result['email_address'] :
            $email_address_result['customers_email_address']);

        $customer_name = (!is_null($email_address_result['customers_firstname']) ?
            $email_address_result['customers_firstname'] . ' ' .
            $email_address_result['customers_lastname'] :
            $email_address_result['name']); // was name entered in BISN form by guest/not a registered customer

        // Has this customer been e-mailed yet?
        if (!array_key_exists($customer_email_address, $email_addresses_notified)) {
            // Get all the products for which this e-mail address is subscribed to a back in stock
            // notification list AND for which the product is back in stock

            $products_query = '
            SELECT DISTINCT
               bisns.id, bisns.product_id, pd.products_name, p.products_model
            FROM
               ' . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . ' bisns
            LEFT JOIN
               ' . TABLE_CUSTOMERS . ' c
            ON
               c.customers_id = bisns.customer_id
            LEFT JOIN
               ' . TABLE_PRODUCTS . ' p
            ON
               p.products_id = bisns.product_id
            LEFT JOIN
               ' . TABLE_PRODUCTS_DESCRIPTION . ' pd
            ON
               pd.products_id = bisns.product_id
            WHERE
                pd.language_id = ' . $languages_id . '
            AND
               p.products_quantity > 0
            AND
               bisns.languages_id = ' . $languages_id . '
            AND
               (
               bisns.email_address = "' . $customer_email_address . '"
            OR
               c.customers_email_address = "' . $customer_email_address . '"
               )';

            $products_result = $db->Execute($products_query);

            $plain_text_msg = '';
            $html_msg = '';

            // Record the names of the products which have come back in stock since this user joined their back in stock notification list(s)
            $products = [];

            foreach ($products_result as $product_result) {
                $products[] = [
                    'subscription_id' => $product_result['id'],
                    'product_id' => $product_result['product_id'],
                    'product_model' => $product_result['products_model'],
                    'name' => $product_result['products_name']
                ];

                $product_type_result = $db->Execute(
                    '
               SELECT
                  p.products_id,
                  pt.type_handler
               FROM
                  ' . TABLE_PRODUCTS . ' p
               LEFT JOIN
                  ' . TABLE_PRODUCT_TYPES . ' pt
               ON
                  pt.type_id = p.products_type
               WHERE
                  p.products_id = ' . (int)$product_result['product_id']
                );

                if (!$product_type_result->EOF &&
                    !is_null($product_type_result->fields['type_handler']) &&
                    strlen($product_type_result->fields['type_handler']) > 0) {
                    $product_page = $product_type_result->fields['type_handler'] . '_info';
                } else {
                    $product_page = 'product_info';
                }

                $plain_text_msg .= $product_result['products_model'] . ' - ' . $product_result['products_name'] . "\n\n" . EMAIL_LINK .
                    zen_catalog_href_link(
                        $product_page,
                        'products_id=' .
                        $product_result['product_id']
                    ) . "\n\n\n";

                $html_msg .= '<p class="BackInStockNotificationProduct">' . '<a href="' .
                    zen_catalog_href_link(
                        $product_page,
                        'products_id=' .
                        $product_result['product_id']
                    ) . '" target="_blank">' .
                    htmlentities($product_result['products_model'] . ' - ' . $product_result['products_name'], ENT_COMPAT, CHARSET) .
                    '</a></p>' . "\n";
            }

            // Remove last three newlines from end of plain text message
            $plain_text_msg = substr($plain_text_msg, 0, strlen($plain_text_msg) - 3);

            $message_sent_or_skipped = true;

            if (!$test_mode || count($email_addresses_notified) < 1) {
                $message_sent_or_skipped = sendBackInStockNotificationEmail($customer_name, $customer_email_address, $plain_text_msg, $html_msg, $languages_id, (count($products) > 1), $test_mode);
            }

            if ($message_sent_or_skipped) {
                $email_addresses_notified[strtolower($customer_email_address)] = [
                    'name' => $customer_name,
                    'products' => $products
                ];
            }
        }
    }

    // Build list of addresses and products for which notifications were sent, as well as a list of IDs for the subscriptions (so they can be deleted)

    $output = '';
    $subscription_ids = [];

    $num_addresses_notified = count($email_addresses_notified);

    $output = '<h4>' . zen_get_language_icon($languages_id) . ' ' . TEXT_LANGUAGE . ' ' . $languages_id . ' - ' . ucfirst(zen_get_language_name($languages_id)) . '</h4>';

    if ($num_addresses_notified == 0) {
        $output .= '<p>' . TEXT_PREVIEW_OR_SEND_OUTPUT_TITLE_NONE . "</p>\n";
    } else {
        if ($test_mode) {
            if ($num_addresses_notified == 1) {
                $output .= '<p>' . TEXT_PREVIEW_OUTPUT_TITLE_SINGULAR . "</p>\n";
            } else {
                $output .= '<p>' .
                    sprintf(TEXT_PREVIEW_OUTPUT_TITLE_PLURAL, $num_addresses_notified) . "</p>\n";
            }
        } else {
            if ($num_addresses_notified == 1) {
                $output .= '<p>' . TEXT_SEND_OUTPUT_TITLE_SINGULAR . "</p>\n";
            } else {
                $output .= '<p>' .
                    sprintf(TEXT_SEND_OUTPUT_TITLE_PLURAL, $num_addresses_notified) . "</p>\n";
            }
        }

        $output .= "<dl class=\"back-in-stock-notifications-output\">\n";

        foreach ($email_addresses_notified as $email_address => $info) {
            $output .= "\t<dt>" . htmlentities($info['name'], ENT_COMPAT, CHARSET) . ' &lt;' .
                $email_address . '&gt;</dt>' . "\n";

            foreach ($info['products'] as $product) {
                $output .= "\t<dd>" . htmlentities($product['product_model'], ENT_COMPAT, CHARSET) . ' - ' . htmlentities($product['name'], ENT_COMPAT, CHARSET) .
                    '</dd>' . "\n";
                $output .= "\t<dd>" . '<a href="' .
                    zen_catalog_href_link($product_page, 'products_id=' . $product['product_id']) . '" title="' . TEXT_TITLE_VIEW_PRODUCT . '" target="_blank">' .
                    htmlentities(zen_catalog_href_link($product_page, 'products_id=' . $product['product_id']), ENT_COMPAT, CHARSET) . '</a>' .
                    '</dd>' . "\n";

                $subscription_ids[] = $product['subscription_id'];
            }
        }

        $output .= "</dl>\n";

        if (!$test_mode && $delete_subscriptions) {//$delete_subscriptions is manually defined in admin/back_in_stock_notifications.php
            // Now delete the subscriptions from the database
            $subscription_ids_string = implode(',', $subscription_ids);

            $delete_subscriptions_query = 'DELETE FROM ' . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . ' WHERE id IN (' . $subscription_ids_string . ')';
            $db->Execute($delete_subscriptions_query);
        }
    }

    return $output;
}

/**
 * Expunges any notification subscriptions for products which no longer exist.
 *
 * @return void
 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 */
function expungeOutdatedSubscriptionsFromBackInStockNotificationsDB(): void
{
    global $db, $messageStack;

    $delete_subscriptions_query = 'DELETE FROM ' . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . ' WHERE product_id NOT IN 
                                   (SELECT products_id FROM ' . TABLE_PRODUCTS . ' WHERE 1 = 1 )';
    $db->Execute($delete_subscriptions_query);
    $messageStack->add(sprintf(TEXT_DELETED_PRODUCTS_SUBSCRIPTIONS_REMOVED, $db->affectedRows()), 'info');
}

/**
 * Builds and sends an e-mail notifications to a user using the back in stock notification e-mail
 * template.
 *
 * @param  string  $name  The name of the person being e-mailed.
 * @param  string  $email  The e-mail address of the person being e-mailed.
 * @param  string  $plain_text_msg  The plain text version of the product notifications message.
 * @param  string  $html_msg  The HTML version of the product notifications message.
 * @param  int  $languages_id  Language id of the required email.
 * @param  bool  $more_than_one  Whether more than one product is being notified about.
 * @param  bool  $test_mode  Whether the e-mail should simply be sent to the admin.
 * @return  bool   Whether or not the e-mail was sent successfully.
 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 */
function sendBackInStockNotificationEmail(string $name, string $email, string $plain_text_msg, string $html_msg, int $languages_id, bool $more_than_one = false, bool $test_mode = false): bool
{
    global $messageStack, $ENABLE_SSL;

    $plain_text_msg_parts['EMAIL_GREETING'] = sprintf(EMAIL_GREETING, $name);

    $html_msg_parts['EMAIL_GREETING'] = htmlentities(sprintf(EMAIL_GREETING, $name), ENT_COMPAT, CHARSET);

    $plain_text_msg_parts['EMAIL_INTRO_1'] = $html_msg_parts['EMAIL_INTRO_1'] = '';
    $plain_text_msg_parts['EMAIL_INTRO_2'] = $html_msg_parts['EMAIL_INTRO_2'] = '';

    if (!$more_than_one) {
        $plain_text_msg_parts['EMAIL_INTRO_1'] .= EMAIL_INTRO_SINGULAR1;
        $plain_text_msg_parts['EMAIL_INTRO_2'] .= EMAIL_INTRO_SINGULAR2;

        $html_msg_parts['EMAIL_INTRO_1'] .= EMAIL_INTRO_SINGULAR1;
        $html_msg_parts['EMAIL_INTRO_2'] .= EMAIL_INTRO_SINGULAR2;
    } else {
        $plain_text_msg_parts['EMAIL_INTRO_1'] .= EMAIL_INTRO_PLURAL1;
        $plain_text_msg_parts['EMAIL_INTRO_2'] .= EMAIL_INTRO_PLURAL2;

        $html_msg_parts['EMAIL_INTRO_1'] .= EMAIL_INTRO_PLURAL1;
        $html_msg_parts['EMAIL_INTRO_2'] .= EMAIL_INTRO_PLURAL2;
    }

    $ssl_status = 'NONSSL';

    if ($ENABLE_SSL) {
        $ssl_status = 'SSL';
    }

    $plain_text_msg_parts['STORE_URL'] = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
    $plain_text_msg_parts['STORE_ACCOUNT_URL'] = zen_catalog_href_link(FILENAME_ACCOUNT, '', $ssl_status);
    $plain_text_msg_parts['STORE_CONTACT_URL'] = zen_catalog_href_link(FILENAME_CONTACT_US);

    $html_msg_parts['STORE_URL'] = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
    $html_msg_parts['STORE_ACCOUNT_URL'] = zen_catalog_href_link(FILENAME_ACCOUNT, '', $ssl_status);
    $html_msg_parts['STORE_CONTACT_URL'] = zen_catalog_href_link(FILENAME_CONTACT_US);

    if (!$more_than_one) {
        $plain_text_msg_parts['PRODUCTS_DETAIL_TITLE'] = PRODUCTS_DETAIL_TITLE_SINGULAR;
        $html_msg_parts['PRODUCTS_DETAIL_TITLE'] = PRODUCTS_DETAIL_TITLE_SINGULAR;
    } else {
        $plain_text_msg_parts['PRODUCTS_DETAIL_TITLE'] = PRODUCTS_DETAIL_TITLE_PLURAL;
        $html_msg_parts['PRODUCTS_DETAIL_TITLE'] = PRODUCTS_DETAIL_TITLE_PLURAL;
    }

    $plain_text_msg_parts['PRODUCTS_DETAIL'] = $plain_text_msg;

    $html_msg_parts['PRODUCTS_DETAIL'] = '<table class="product-details" border="0" width="100%" cellspacing="0" cellpadding="2">' . $html_msg . '</table>';

    // Include disclaimer
    $plain_text_msg_parts['EMAIL_DISCLAIMER'] = "\n-----\n" . sprintf(EMAIL_DISCLAIMER, STORE_OWNER_EMAIL_ADDRESS) . "\n\n";
    $plain_text_msg_parts['EMAIL_DISCLAIMER'] .= "\n-----\n" . EMAIL_FOOTER_COPYRIGHT . "\n\n";

    $html_msg_parts['EMAIL_DISCLAIMER'] = sprintf(
        EMAIL_DISCLAIMER,
        '<a href="mailto:' . STORE_OWNER_EMAIL_ADDRESS . '">' . STORE_OWNER_EMAIL_ADDRESS . ' </a>'
    );

    $test_mode_subj = '';
    if ($test_mode) {
        // Only send e-mails to store owner when in test mode
        $email = (BISN_TEST_EMAIL_TO === '' ? EMAIL_FROM : BISN_TEST_EMAIL_TO);
        $test_mode_subj = ' - TEST MODE';
    }

    // Create the text version of the e-mail for Zen Cart's e-mail functionality
    $language_folder_path_part = (strtolower($_SESSION['languages_code']) == 'en') ? '' : strtolower($_SESSION['languages_code']) . '/';

    $template_file = DIR_FS_EMAIL_TEMPLATES . $language_folder_path_part . 'email_template_back_in_stock_notification.txt';
    if (file_exists($template_file)) {
        // Use template file for current language
        $text_msg_source = file_get_contents($template_file);
    } elseif ($language_folder_path_part != '') {
        // Non-english language being used but no template file exist for it, fall back to the default english template
        $text_msg_source =
            file_get_contents(str_replace('email/' . $language_folder_path_part, 'email/', $template_file));//use email/ to target replacement accurately as .es/ was being removed from folder name with.es
    }

    foreach ($plain_text_msg_parts as $key => $value) {
        $text_msg_source = str_replace('$' . $key, $value, $text_msg_source);
    }

    $error = zen_mail($name, $email, EMAIL_BACK_IN_STOCK_NOTIFICATIONS_SUBJECT . $test_mode_subj, $text_msg_source, STORE_NAME, EMAIL_FROM, $html_msg_parts, 'back_in_stock_notification');

    if ($error != '') {
        $messageStack->add($error, 'back_in_stock_notifications');
        // if offline/email sending fails/there is error, there is no listing of the emails to be sent when using the test mode. This allows that listing to be shown.
        if ($test_mode) {
            $messageStack->add('BISN in test mode - showing emails to be sent although there are sending errors', 'back_in_stock_notifications');
            return true;
        }
        return false;
    }

    return true;
}

/**
 * Builds a link to a Product's admin page, with the product's name limited to a particular number
 * of characters.
 *
 * @param  int  $id  The ID of the product.
 * @param  string  $name  The name of the product.
 * @param  int  $products_type  The ID of the type for the product.
 * @param  string  $attribute  The product attribute.
 * @param  int $name_length Optional truncation of the name
 * @return  string    The HTML link to the product's admin page.
 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @author  RubikIntegration team @ RubikIntegration.com
 */
function buildLinkToProductAdminPage(int $id, string $name, int $products_type, string $attribute = '', int $name_length = 0): string
{
    global $zc_products;
    $type_admin_handler = $zc_products->get_admin_handler($products_type);
    $name_length = $name_length === 0 ? zen_field_length(TABLE_PRODUCTS, 'products_name') : $name_length;
    return '<a href="' . zen_href_link(
            $type_admin_handler,
            'pID=' . $id . '&product_type=' .
            $products_type . '&action=new_product',
            'NONSSL',
            true,
            true,
            false,
            false
        ) . '" 
        title="' . TEXT_TITLE_EDIT_PRODUCT . '" target="_blank">' .
        htmlentities(substr($name, 0, $name_length), ENT_COMPAT, CHARSET) .
        (strlen($name) > $name_length ? '...' : '') .
        ($attribute === '' ? '' : "<br>: <em><b>$attribute</b></em>") .
        '</a>';
}

/**
 * Check for the use of a value for model in the query results.
 * If no value found, hide model columns etc.
 * @param $query_result
 * @return bool
 */
function checkForModel($query_result): bool
{
    $use_model = false;
    foreach ($query_result as $array) {
        //debug
        //mv_printVar($array);
        $use_model = !empty($array['products_model']);
        if ($use_model) {
            break;
        }
    }
    return $use_model;
}
