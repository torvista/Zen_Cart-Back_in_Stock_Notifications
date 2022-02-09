<?php

/**
 * Ceon Back In Stock Notifications Admin Page Registration.
 *
 * Attempts to create a link to the Ceon Back In Stock Notifications admin utility in the Zen Cart
 * admin menu in Zen Cart 1.5+. After running successfully once, this file deletes itself as it is
 * never needed again!
 * 
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2008 RubikIntegration team @ RubikIntegration.com
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications_admin_page_reg.php 937 2012-02-10 11:42:20Z conor $
 */

if (!defined('IS_ADMIN_FLAG')) {
	die('Illegal Access');
}

// This file should normally only need to be run once, but if the user hasn't installed the software
// properly it may need to be run again. Flag tracks the situation
$can_autodelete = true;

if (function_exists('zen_register_admin_page')) {
	if (!zen_page_key_exists('ceon_bisn')) {
		// Register the Ceon Back In Stock Notifications Admin Utility with the Zen Cart admin
		
		// Quick sanity check in case user hasn't uploaded a necessary file on which this depends
		$error_messages = array();
		
		if (!defined('FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS')) {
			$error_messages[] = 'The Back In Stock Notifications filename define is missing.' .
				' Please check that the file ' . DIR_WS_INCLUDES . 'extra_datafiles/' .
				'back_in_stock_notifications_filenames.php has been uploaded.';
			
			$can_autodelete = false;
			
		}
		
		if (sizeof($error_messages) > 0) {
			// Let the user know that there are problem(s) with the installation
			foreach ($error_messages as $error_message) {
				print '<p style="background: #fcc; border: 1px solid #f00; margin: 1em;' .
					' padding: 0.4em;">Error: ' . $error_message . "</p>\n";
			}
		} else {
			// Necessary file is in place so can register the admin page and have the menu item
			// created
			zen_register_admin_page('ceon_bisn', 'BOX_CEON_BACK_IN_STOCK_NOTIFICATIONS',
				'FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS', '', 'catalog', 'Y', 40);
		}
	}
}

if ($can_autodelete) {
	// Either the admin utility file has been registered, or it doesn't need to be. Can stop the
	// wasteful process of having this script run again by having it delete itself
	@unlink(DIR_WS_INCLUDES .
		'functions/extra_functions/back_in_stock_notifications_admin_page_reg.php');
}

?>