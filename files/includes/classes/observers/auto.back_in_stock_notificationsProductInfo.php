<?php

declare(strict_types=1);
/**
 * Ceon Back In Stock Notifications Product Info Page Notification Form Display.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        https://www.ceon.net
 * @license     https://www.gnu.org/copyleft/gpl.html GNU Public License V2.0
 * @version     $Id: class.back_in_stock_notificationsProductInfo.php 2023-11-19 torvista
 */

/**
 * Checks if the current user is subscribed to any Back In Stock Notification lists.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        https://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License V2.0
 */

class zcObserverBackInStockNotificationsProductInfo extends base
{

    public function __construct()
    {
        $this->attach($this, [
                'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_DOCUMENT_GENERAL_INFO',
                'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_DOCUMENT_PRODUCT_INFO',
                'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_PRODUCT_BOOK_INFO',
                'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_PRODUCT_FREE_SHIPPING_INFO',
                'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_PRODUCT_INFO',
                'NOTIFY_MAIN_TEMPLATE_VARS_EXTRA_PRODUCT_MUSIC_INFO',
            ]
        );
    }

    /**
     * @param $callingClass
     * @param $notifier
     * @param $paramsArray
     *
     * @return void
     */
    protected function update($callingClass, $notifier, $paramsArray): void
    {
        global $db, $request_type, $products_quantity,
               $product_back_in_stock_notification_form_link,
               $back_in_stock_notification_build_form,
               $back_in_stock_notification_form_customer_name,
               $back_in_stock_notification_form_customer_email,
               $back_in_stock_notification_form_customer_email_confirmation;

        $product_back_in_stock_notification_form_link = null;
        $back_in_stock_notification_build_form = false;

        // check product id is valid
        $prid_ok = true;
        if (empty($_GET['products_id'])) {
            $prid_ok = false;
        } elseif (!zen_products_id_valid($_GET['products_id'])) {
            $prid_ok = false;
        }
        if (!$prid_ok) {
            return;
        }

        $attributes_no_stock = [];

        //**************** Add your custom attribute-stock-handling in here *********************//
        //*************************************************************************************//

        // Check if customer should be offered the option to be notified when this product is back in stock
        if (BACK_IN_STOCK_NOTIFICATIONS_ENABLED === '1' && ($products_quantity <= 0 || !empty($attributes_no_stock))) {
            if (BACK_IN_STOCK_REQUIRES_LOGIN === '1' && !zen_is_logged_in()) {
                return;
            }

            $product_back_in_stock_notification_form_link = '';
            $back_in_stock_notification_build_form = true;

            // Update the source with the details of the customer (if available)
            if (isset($_SESSION['customer_id']) && $_SESSION['customer_id']) {
                // Check if this user has already requested to be notified when this product is back
                // in stock
                $customer_details_query = '
               SELECT
                  customers_firstname, customers_lastname, customers_email_address
               FROM
                  ' . TABLE_CUSTOMERS . '
               WHERE
                  customers_id = ' . (int)$_SESSION['customer_id'];

                $customer_details = $db->Execute($customer_details_query);

                $already_to_be_notified_query = '
               SELECT
                  id
               FROM
                  ' . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . '
               WHERE
                  product_id = ' . (int)$_GET['products_id'] . '
               AND
                  (
                     customer_id = ' . (int)$_SESSION['customer_id'] . "
                  OR
                     email_address = '" . $customer_details->fields['customers_email_address'] . "'
                  )";

                $already_to_be_notified = $db->Execute($already_to_be_notified_query);

                if ($already_to_be_notified->RecordCount() > 0) {
                    // Customer is already subscribed to the notification list for this product

                    $back_in_stock_notification_build_form = false;

                    $product_back_in_stock_notification_form_link = BACK_IN_STOCK_NOTIFICATION_TEXT_ALREADY_SUBSCRIBED;
                } else {
                    // Customer is not yet subscribed to be notified - store data for the notification
                    // request form
                    $back_in_stock_notification_form_customer_name = htmlentities(
                        $customer_details->fields['customers_firstname'] . ' ' .
                        $customer_details->fields['customers_lastname'],
                        ENT_COMPAT,
                        CHARSET
                    );

                    $back_in_stock_notification_form_customer_email = htmlentities(
                        $customer_details->fields['customers_email_address']
                    );

                    $back_in_stock_notification_form_customer_email_confirmation = htmlentities(
                        $customer_details->fields['customers_email_address']
                    );
                }
            } else {
                $back_in_stock_notification_form_customer_name = '';
                $back_in_stock_notification_form_customer_email = '';
                $back_in_stock_notification_form_customer_email_confirmation = '';
            }

            if ($product_back_in_stock_notification_form_link === '') {
                // Build the link to the form

                if (BACK_IN_STOCK_REQUIRES_LOGIN === '1') {
                    // account is required for subscription
                    // if logged in: the subscription link adds the subscription (no form needed)
                    // if not logged in: redirects to the login/account creation page
                    $product_back_in_stock_notification_form_link = sprintf(
                        BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_LINK,
                        zen_href_link(
                            FILENAME_BACK_IN_STOCK_NOTIFICATION_SUBSCRIBE,
                            'products_id=' . (int)$_GET['products_id']
                        )
                    );
                } else {
                    // guest may subscribe: link jumps to form at foot of page
                    $product_back_in_stock_notification_form_link = sprintf(
                        BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_LINK,
                        zen_href_link(
                            zen_get_info_page((int)$_GET['products_id']),
                            zen_get_all_get_params(['number_of_uploads']),
                            $request_type
                        ) .
                        '#back_in_stock_notification_form'
                    );
                }
                $product_back_in_stock_notification_form_link = '<div id="bisnFormSubscribeLink">' . "\n<p>" . $product_back_in_stock_notification_form_link . "</p>\n</div>";
            }
        }
    }
}
