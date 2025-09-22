<?php

declare(strict_types=1);
/**
 * Ceon Back In Stock Notification Subscription page.
 *
 * Allows users to subscribe to a "Back In Stock" notification list for a given product.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        https://www.ceon.net
 * @license     https://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: header_php.php 22 Sept 2025 torvista
 */

/**phpStorm inspections
 * @var queryFactory $db
 * @var sniffer $sniffer
 * @var breadcrumb $breadcrumb
 */
if ((BACK_IN_STOCK_REQUIRES_LOGIN === '1') && !$_SESSION['customer_id']) {
    $_SESSION['navigation']->set_snapshot();

    zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
}

/**
 * Load in the language file
 */
require(DIR_FS_CATALOG . DIR_WS_MODULES . 'require_languages.php');

$breadcrumb->add(BACK_IN_STOCK_NOTIFICATION_NAVBAR_TITLE);

// Make sure that product id was supplied
if (empty($_GET['products_id'])) {//should never happen (maybe incorrect urls from spiders)
    zen_redirect(FILENAME_DEFAULT);
}

/**
 * Load in the e-mail validation class
 */
require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.CeonEmailValidation.php');

$build_form = true;
$already_subscribed = false;

// Get the name of the product
$product_name_query = '
   SELECT
      products_name
   FROM
      ' . TABLE_PRODUCTS_DESCRIPTION . '
   WHERE
      products_id = ' . (int)$_GET['products_id'] . '
   AND
      language_id = ' . (int)$_SESSION['languages_id'] . ' LIMIT 1';

$product_name_result = $db->Execute($product_name_query);

// Make sure the product exists!
if ($product_name_result->RecordCount() === 0) {//should never happen
    zen_redirect(FILENAME_DEFAULT);
    error_log('Error BISN Subscribe Page: invalid product id presented (int)$_GET[\'products_id\']=' . (int)$_GET['products_id']);
} else {
    $product_name = $product_name_result->fields['products_name'];
    $product_model = zen_get_products_model((int)$_GET['products_id']);
}

if (isset($_POST['posmProductNameExtra'])) {
    $posmProductNameExtra = zen_db_prepare_input($_POST['posmProductNameExtra']);
    //$posm_pos_name = $db->Execute("SELECT pos_model FROM " . TABLE_PRODUCTS_OPTIONS_STOCK . " WHERE pos_id = $posm_pos_id LIMIT 1");
    //$posm_pos_name = $posm_pos_name->fields['pos_model'];
    //echo __LINE__ . ': $posm_pos_name=' . $posm_pos_value_name . '<br>';
} else {
    $posmProductNameExtra = '';
}
//steve expand name for notifications and for passing to new form displayed on this page if validation failed.
$product_name = $product_model . ' - ' . $product_name . ($posmProductNameExtra === '' ? '' : " ($posmProductNameExtra)");

// Check if the form has been submitted
$form_errors = [];

$zco_notifier->notify('NOTIFY_BISN_SUBSCRIBE_CAPTCHA_CHECK', $_POST);

if (BACK_IN_STOCK_REQUIRES_LOGIN === '1') {
    $_POST['notify_me'] = 1;
    $_POST['email'] = get_customer_email_from_id((int)$_SESSION['customer_id']);
    $_POST['name'] = $_SESSION['customer_first_name'] . ' ' . $_SESSION['customer_last_name'];
}

if (isset($_POST['notify_me'])) {
    // Check that a valid e-mail address has been supplied
    if (isset($_POST['email'])) {
        $_POST['email'] = trim($_POST['email']);
    }

    if (CeonEmailValidation::isHeaderInjection($_POST['name'])) {
        $form_errors['name'] = BACK_IN_STOCK_NOTIFICATION_FORM_ERROR_HEADER_INJECTION_ATTEMPT;
    }

    if (isset($_POST['cofnospam'])) {
        $_POST['cofnospam'] = trim($_POST['cofnospam']);
    } elseif (isset($_SESSION['customer_id'], $_POST['email'])) {
        // Trust the input from anyone who has already logged in!
        $_POST['cofnospam'] = $_POST['email'];
    }

    if (!isset($_POST['email']) || $_POST['email'] === '') {
        $form_errors['email'] = BACK_IN_STOCK_NOTIFICATION_FORM_ERROR_EMAIL_NOT_ENTERED;
    } elseif (!CeonEmailValidation::isValid($_POST['email'])) {
        $form_errors['email'] = BACK_IN_STOCK_NOTIFICATION_FORM_ERROR_EMAIL_INVALID;
    } elseif (CeonEmailValidation::isHeaderInjection($_POST['email'])) {
        $form_errors['email'] = BACK_IN_STOCK_NOTIFICATION_FORM_ERROR_HEADER_INJECTION_ATTEMPT;
    }

    if (!isset($_POST['cofnospam']) || $_POST['cofnospam'] === '') {
        $form_errors['cofnospam'] = BACK_IN_STOCK_NOTIFICATION_FORM_ERROR_EMAIL_CONFIRMATION_NOT_ENTERED;
    } elseif (!CeonEmailValidation::isValid($_POST['cofnospam'])) {
        $form_errors['cofnospam'] = BACK_IN_STOCK_NOTIFICATION_FORM_ERROR_EMAIL_INVALID;
    } elseif (CeonEmailValidation::isHeaderInjection($_POST['cofnospam'])) {
        $form_errors['cofnospam'] = BACK_IN_STOCK_NOTIFICATION_FORM_ERROR_HEADER_INJECTION_ATTEMPT;
    } elseif (strtolower($_POST['email']) !== strtolower($_POST['cofnospam'])) {
        $form_errors['cofnospam'] = BACK_IN_STOCK_NOTIFICATION_FORM_ERROR_EMAIL_CONFIRMATION_DOESNT_MATCH;
    }

    // Add clause for Google reCaptcha plugin.
    // "$error" is a global created by the plugin observer.
    // Same observer adds the Recaptcha error message to messageStack. Normally messageStack->output is in the template code, but this uses CEON's unique template variable substitution to put the error message above the Recaptcha on the BISN Subscribe page.
    if (!empty($error)) {
        $captcha_error = '';
        if ($messageStack->size('bisn_subscribe') > 0) {
            foreach ($messageStack->messages as $message) {
                if ($message['class'] = 'bisn_subscribe') {
                    $captcha_error = $message['text'];
                    break;//there will only be one error message text
                }
            }
            $form_errors['captcha'] = $captcha_error;
        }
    }

    if (count($form_errors) === 0) {
        // Valid e-mail address supplied
        $build_form = false;

        $email_address = $_POST['email'];

        // Check if the user is already subscribed to the notification list for this product
        $check_notification_subscription_query = '
         SELECT
            id
         FROM
            ' . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . ' bisns
         LEFT JOIN
            ' . TABLE_CUSTOMERS . ' c
         ON
            c.customers_id = bisns.customer_id
         WHERE
            bisns.product_id = ' . (int)$_GET['products_id'] . '
         AND
            (bisns.email_address = "' . zen_db_prepare_input($email_address) . '"
         OR
            c.customers_email_address = "' . zen_db_prepare_input($email_address) . '")';
        $check_notification_subscription = $db->Execute($check_notification_subscription_query);

        if ($check_notification_subscription->RecordCount() > 0) {
            // Customer is already subscribed
            $already_subscribed = true;
        } else {
            // Subscribe the user to the notification list!
            $subscription_customer_id = null;

            if (isset($_SESSION['customer_id'])) {
                $subscription_customer_id = $_SESSION['customer_id'];

                // Get the currently logged in customer's email address
                $customer_email_address_query = '
               SELECT
                  customers_email_address
               FROM
                  ' . TABLE_CUSTOMERS . '
               WHERE
                  customers_id = ' . (int)$_SESSION['customer_id'];

                $customer_email_address_result = $db->Execute($customer_email_address_query);

                if (!$customer_email_address_result->EOF) {
                    $customer_email_address =
                        $customer_email_address_result->fields['customers_email_address'];
                }
            } else {
                // Is this an existing customer who hasn't signed in?
                $existing_customer_query = '
               SELECT
                  customers_id
               FROM
                  ' . TABLE_CUSTOMERS . '
               WHERE
                  customers_email_address = "' . zen_db_prepare_input($email_address) . '"';
                $existing_customer_result = $db->Execute($existing_customer_query);

                if (!$existing_customer_result->EOF) {
                    $subscription_customer_id = $existing_customer_result->fields['customers_id'];
                    $customer_email_address = $email_address;
                }
            }

            if (isset($customer_email_address) && $customer_email_address === $email_address) {
                // User is using their registered email address so their user id should be stored
                // instead of the entered address.
                $sql_data_array = [
                    'product_id' => (int)$_GET['products_id'],
                    'customer_id' => (int)$subscription_customer_id,
                    'name' => zen_db_prepare_input($_POST['name']),
                    'date_subscribed' => date('Y-m-d H:i:s'),
                    'languages_id' => (int)$_SESSION['languages_id']//steve added
                ];

                zen_db_perform(TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS, $sql_data_array);

                $back_in_stock_notification_id = $db->insert_ID();

                // Send e-mail
                sendBackInStockNotificationSubscriptionEmail(
                    $back_in_stock_notification_id,
                    $product_name,
                    (int)$subscription_customer_id,
                    $_POST['name'],
                    $email_address
                );

                $unsubscribe_message =
                    sprintf(
                        BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_MY_ACCOUNT_MESSAGE,
                        htmlentities($product_name, ENT_COMPAT, CHARSET)
                    );
            } else {
                // Subscribe user by email address only

                // Build a random, (fairly) unique text string to use as a code for verifying
                // any unsubscription attempts
                $subscription_code = substr(md5((string)time()), 0, 10);//time is int, md5 requires string

                $sql_data_array = [
                    'product_id' => (int)$_GET['products_id'],
                    'name' => zen_db_prepare_input($_POST['name']),
                    'email_address' => zen_db_prepare_input($_POST['email']),
                    'subscription_code' => $subscription_code,
                    'date_subscribed' => date('Y-m-d H:i:s'),
                    'languages_id' => (int)$_SESSION['languages_id']//steve added
                ];

                zen_db_perform(TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS, $sql_data_array);

                $back_in_stock_notification_id = $db->insert_ID();

                // Send e-mail
                sendBackInStockNotificationSubscriptionEmail(
                    $back_in_stock_notification_id,
                    $product_name,
                    '',
                    $_POST['name'],
                    $email_address,
                    $subscription_code
                );

                $unsubscribe_message =
                    sprintf(
                        BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_LINK_MESSAGE,
                        htmlentities($product_name, ENT_COMPAT, CHARSET)
                    );
            }

            $success_message1 = sprintf(
                BACK_IN_STOCK_NOTIFICATION_SUCCESS_MESSAGE1,
                htmlentities($product_name, ENT_COMPAT, CHARSET)
            );

            $success_message2 = BACK_IN_STOCK_NOTIFICATION_SUCCESS_MESSAGE2;
        }
    }
}

// Check if the form has to be displayed or if the request was completed
if ($build_form) {
    // Store details for form
    $back_in_stock_notification_form_customer_name = (!empty($_POST['name']) ? htmlentities($_POST['name'], ENT_COMPAT, CHARSET) : '');

    $back_in_stock_notification_form_customer_email = (!empty($_POST['email']) ? htmlentities($_POST['email']) : '');
//steve for reposting product name extra
    $back_in_stock_notification_form_posm_product_name_extra = (!empty($_POST['posmProductNameExtra']) ? $_POST['posmProductNameExtra'] : '');//steve for php notice
//eof

    if (isset($_POST['cofnospam'])) {
        $back_in_stock_notification_form_customer_email_confirmation = htmlentities($_POST['cofnospam']);
    } else {
        $back_in_stock_notification_form_customer_email_confirmation = '';
    }
}

/**
 * @param $back_in_stock_notification_id
 * @param $product_name
 * @param $customer_id
 * @param $customer_name
 * @param $email_address
 * @param  string  $subscription_code
 *
 * @return void
 */
function sendBackInStockNotificationSubscriptionEmail(
    $back_in_stock_notification_id,
    $product_name,
    $customer_id,
    $customer_name,
    $email_address,
    string $subscription_code = ''
): void {
    $text_msg_part = [];
    $html_msg = [];

    //intro area
    $text_msg_part['EMAIL_TEXT_HEADER'] = EMAIL_TEXT_HEADER;
    $text_msg_part['EMAIL_TEXT_FROM'] = EMAIL_TEXT_FROM;
    $text_msg_part['INTRO_STORE_NAME'] = STORE_NAME;

    $text_msg_part['GREETING'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_GREETING;

    $text_msg_part['CUSTOMER_NAME'] = $customer_name;

    $text_msg_part['INTRO1'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_INTRO1;
    $text_msg_part['PRODUCT_NAME'] = $product_name;
    $text_msg_part['INTRO2'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_INTRO2;

    if ($subscription_code === '') {
        // Build link to my account section
        $text_msg_part['URL_INTRO'] =
            BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_MY_ACCOUNT_INTRO;
        $text_msg_part['URL_TEXT'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_MY_ACCOUNT_TEXT;
        $text_msg_part['URL_VALUE'] = zen_href_link(
            FILENAME_ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS,
            '',
            'SSL',
            false
        );
    } else {
        // Build link to unsubscription page
        $text_msg_part['URL_INTRO'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_URL_INTRO;
        $text_msg_part['URL_TEXT'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_URL_TEXT;
        $text_msg_part['URL_VALUE'] = zen_href_link(
            FILENAME_BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE,
            'id=' . $back_in_stock_notification_id . '&code=' . $subscription_code,
            'SSL',
            false
        );
    }

    $html_msg['EMAIL_TEXT_HEADER'] = EMAIL_TEXT_HEADER;
    $html_msg['EMAIL_TEXT_FROM'] = EMAIL_TEXT_FROM;
    $html_msg['INTRO_STORE_NAME'] = STORE_NAME;

    $html_msg['GREETING'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_GREETING;

    $html_msg['CUSTOMER_NAME'] = $customer_name;

    $html_msg['INTRO1'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_INTRO1;
    $html_msg['PRODUCT_NAME'] = $product_name;
    $html_msg['INTRO2'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_INTRO2;

    if ($subscription_code === '') {
        // Build link to my account section
        $html_msg['URL_INTRO'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_MY_ACCOUNT_INTRO;
        $html_msg['URL_TEXT'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_MY_ACCOUNT_TEXT;
        $html_msg['URL_VALUE'] = zen_href_link(
            FILENAME_ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS,
            '',
            'SSL',
            false
        );
    } else {
        // Build link to unsubscription page
        $html_msg['URL_INTRO'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_URL_INTRO;
        $html_msg['URL_TEXT'] = BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_URL_TEXT;
        $html_msg['URL_VALUE'] = zen_href_link(
            FILENAME_BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE,
            'id=' . $back_in_stock_notification_id . '&code=' . $subscription_code,
            'SSL',
            false
        );
    }

    $text_msg_part['EXTRA_INFO'] = '';
    $html_msg['EXTRA_INFO'] = '';

    // Create the text version of the e-mail for Zen Cart's e-mail functionality
    // set the email directory based on language, e.g. for es "/es"
    $language_folder_path_part = (strtolower($_SESSION['languages_code']) === 'en') ? '' :
        strtolower($_SESSION['languages_code']) . '/';

    $template_file = DIR_FS_EMAIL_TEMPLATES . $language_folder_path_part .
        'email_template_back_in_stock_notification_subscribe.txt';

    $text_msg_source = '';
    if (file_exists($template_file)) {//is there a language-specific template?
        // Use template file for current language
        $text_msg_source = file_get_contents($template_file);
    } elseif ($language_folder_path_part !== '') {
        // A non-english language is being used but no template file exists for it, so use the default english template
        $text_msg_source = file_get_contents(DIR_FS_EMAIL_TEMPLATES . 'email_template_back_in_stock_notification_subscribe.txt');
    }

    if ($text_msg_source !== false) {//if file not found
        foreach ($text_msg_part as $key => $value) {
            $text_msg_source = str_replace('$' . $key, $value, $text_msg_source);
        }

        zen_mail(
            $customer_name,
            $email_address,
            sprintf(BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_SUBJECT, $product_name),
            $text_msg_source,
            STORE_NAME,
            EMAIL_FROM,
            $html_msg,
            'back_in_stock_notification_subscribe'
        );

        // Send an e-mail to the store owner as well?
        //$html_msg = $html_msg . " " . $customer_name ." ".  $email_address;//steve some of these fields are arrays, not just text
        if (SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAILS_TO !== '') {
            zen_mail(
                '',
                SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAILS_TO,
                sprintf(
                    SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAIL_SUBJECT,
                    $product_name
                ),
                $text_msg_source,
                STORE_NAME,
                EMAIL_FROM,
                $html_msg,
                'back_in_stock_notification_subscribe_extra',
                '',
                $customer_name,
                $email_address
            );
        }
    }
}

/**
 * copied from admin-only function zen_get_customer_email_from_id
 * @param $cid
 * @return string
 */
function get_customer_email_from_id($cid): string
{
    global $db;
    $query = $db->Execute("SELECT customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_id = " . (int)$cid);
    if ($query->EOF) return '';
    return $query->fields['customers_email_address'];
}
