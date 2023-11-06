<?php

declare(strict_types=1);
/**
 * Ceon Back In Stock Notifications Admin Page Registration.
 *
 * Creates the Admin menu link Catalog->Ceon Back in Stock Notifications
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2008 RubikIntegration team @ RubikIntegration.com
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        https://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     https://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications_admin_page_reg.php 2023-11-06 torvista
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

if (function_exists('zen_register_admin_page')) {
    if (!zen_page_key_exists('ceon_bisn')) {
        // Register the Ceon Back In Stock Notifications Admin Utility with the Zen Cart admin

        // Sanity check in case user hasn't uploaded a necessary file
        $error_messages = [];

        if (!defined('FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS')) {
            $error_messages[] = 'The Back In Stock Notifications filename define "FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS" is missing.' .
                ' Please check that the file ' . DIR_WS_INCLUDES . 'extra_datafiles/' . 'back_in_stock_notifications_filenames.php has been uploaded.';
        }

        if (sizeof($error_messages) > 0) {
            // Let the user know that there are problem(s) with the installation
            foreach ($error_messages as $error_message) {
                print '<p style="background: #fcc; border: 1px solid #f00; margin: 1em;' . ' padding: 0.4em;">Error: ' . $error_message . "</p>\n";
            }
        } else {
            // The necessary file is in place, so proceed to register the admin page and create the menu item
            zen_register_admin_page(
                'ceon_bisn',
                'BOX_CEON_BACK_IN_STOCK_NOTIFICATIONS',
                'FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS',
                '',
                'catalog',
                'Y',
                40
            );
        }
    }
}
