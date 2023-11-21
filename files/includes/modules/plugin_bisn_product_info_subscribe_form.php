<?php

declare(strict_types=1);
/**
 * Ceon Back In Stock Notifications common Subscribe form for product info pages.
 *
 * Allows users to unsubscribe from a "Back In Stock" notification list for a given product.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        https://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: header_php.php 2023-11-19 torvista
 */

if (!empty($back_in_stock_notification_build_form)) {
    // Build the notification request form

    /**
     * Load the template class
     */
    require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.CeonXHTMLHiTemplate.php');

    // Load in and extract the template parts for Back In Stock Notification functionality
    $bisn_template_filename = $template->get_template_dir('inc.html.back_in_stock_notifications.html', DIR_WS_TEMPLATE, $current_page_base, 'templates' ) . '/' . 'inc.html.back_in_stock_notifications.html';

    $bisn_template = new CeonXHTMLHiTemplate($bisn_template_filename);

    $bisn_template_parts = $bisn_template->extractTemplateParts();

    $back_in_stock_notification_form = new CeonXHTMLHiTemplate();

    // Load in the source for the form
    $back_in_stock_notification_form->setXHTMLSource($bisn_template_parts['PRODUCT_INFO_BACK_IN_STOCK_NOTIFICATION_FORM']);

    // Add the form action, titles, labels and button
    $form_start_tag = zen_draw_form('back_in_stock_notification', zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATION_SUBSCRIBE, zen_get_all_get_params(), $request_type), 'POST');

    $back_in_stock_notification_form->setVariable('back_in_stock_notification_form_start_tag', $form_start_tag);

    $product_back_in_stock_notification_form_title = BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_TITLE;

    $back_in_stock_notification_form->setVariable('title', $product_back_in_stock_notification_form_title);

    $name_label = BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_NAME;
    $email_label = BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_EMAIL;
    $email_confirmation_label = BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_CONFIRM_EMAIL;

    $back_in_stock_notification_form->setVariable('name_label', $name_label);
    $back_in_stock_notification_form->setVariable('email_label', $email_label);
    $back_in_stock_notification_form->setVariable('email_confirmation_label', $email_confirmation_label);

    $submit_button = zen_image_submit(BUTTON_IMAGE_NOTIFY_ME, BUTTON_NOTIFY_ME_ALT, 'name="notify_me"');
    $back_in_stock_notification_form->setVariable('submit_button', $submit_button);

    // Add in the introductory text
    $intro_text = sprintf(
        BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_INTRO,
        htmlentities($products_name, ENT_COMPAT, CHARSET)
    );
    $notice_text = BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_NOTICE;

    $back_in_stock_notification_form->setVariable('intro', $intro_text);
    $back_in_stock_notification_form->setVariable('notice', $notice_text);

    // Add the customer's details to the form (empty unless logged in)
    $back_in_stock_notification_form->setVariable(
        'name',
        $back_in_stock_notification_form_customer_name
    );
    $back_in_stock_notification_form->setVariable(
        'email',
        $back_in_stock_notification_form_customer_email
    );
    $back_in_stock_notification_form->setVariable(
        'cofnospam',
        $back_in_stock_notification_form_customer_email_confirmation
    );
    // Google reCaptcha: https://github.com/torvista/Zen_Cart-Google_reCAPTCHA
    if (defined('GOOGLE_RECAPCHTA_BISN') && GOOGLE_RECAPCHTA_BISN === 'true') {
        $reCaptcha = recaptcha_get_html(false, 'light', 'normal', 'margin:5px');
        $back_in_stock_notification_form->setVariable('reCaptcha', $reCaptcha);
    }

    print $back_in_stock_notification_form->getXHTMLSource();

} else {
    echo '<!-- BISN form not generated (link is null) -->';
}