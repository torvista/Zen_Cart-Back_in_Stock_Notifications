<?php

/**
 * Ceon Back In Stock Notifications Install/Upgrade Class - Creates the database table and Zen Cart
 * configuration group and options.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Copyright 2003-2007 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version     $Id: class.CeonBISNInstallOrUpgrade.php 937 2012-02-10 11:42:20Z conor $
 */


// {{{ CeonBISNInstallOrUpgrade

/**
 * Installs or upgrades Ceon Back In Stock Notifications. If a previous installation/upgrade attempt
 * failed before it completed, the class can be run again as it will attempt to find and fix any
 * database/configuration issues.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Copyright 2003-2007 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
class CeonBISNInstallOrUpgrade
{
	// {{{ properties
	
	/**
	 * The version of the module.
	 *
	 * @var     string
	 * @access  protected
	 */
	var $_version = null;
	
	/**
	 * The version of the module which is currently installed.
	 *
	 * @var     string
	 * @access  protected
	 */
	var $_installed_version = null;
	
	/**
	 * Tracks if the notification subscriptions table has just been created.
	 *
	 * @var     boolean
	 * @access  protected
	 */
	var $_notification_subscriptions_table_created = false;

	/**
	 * Maintains a list of any errors encountered in an installation or upgrade.
	 *
	 * @var     array
	 * @access  public
	 */
	var $error_messages = array();
	
	// }}}
	
	
	// {{{ Class Constructor
	
	/**
	 * Creates a new instance of the class. Installs/upgrades the database and adds or updates
	 * configuration options.
	 * 
	 * @access  public
	 */
	function CeonBISNInstallOrUpgrade()
	{
		$this->_checkCreateDatabase();
		
		$this->_checkZenCartConfigGroupAndOptions();
	}
	
	// }}}
	
	
	// {{{ _checkCreateDatabase()
	
	/**
	 * Makes sure that the database table exists. Creates it if it doesn't.
	 *
	 * @access  protected
	 * @return  boolean   False if a problem occurred when creating the database table, true
	 *                    otherwise.
	 */
	function _checkCreateDatabase()
	{
		global $db, $messageStack;
		
		// Add the notification subscriptions table if it doesn't exist
		$table_exists_query = 'SHOW TABLES LIKE "' .
			TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . '";';
		
		$table_exists_result = $db->Execute($table_exists_query);
		
		if ($table_exists_result->EOF) {
			$create_table_sql = "
				CREATE TABLE IF NOT EXISTS
					`" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . "`
					(
					`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`product_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
					`customer_id` int(10) UNSIGNED DEFAULT NULL,
					`subscription_code` VARCHAR(10) DEFAULT NULL,
					`name` varchar(64) NOT NULL DEFAULT '',
					`email_address` VARCHAR(96) DEFAULT NULL,
					`date_subscribed` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY  (`id`)
					);";
			
			$create_table_result = $db->Execute($create_table_sql);
			
			// Check the table was created
			$table_exists_query = 'SHOW TABLES LIKE "' .
				TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . '";';
			
			$table_exists_result = $db->Execute($table_exists_query);
			
			if ($table_exists_result->EOF) {
				$this->error_messages[] = 'Database table could not be created! The database' .
					' user may not have CREATE TABLE privileges?!';
				
				return false;
			}
			
			$this->_notification_subscriptions_table_created = true;
			
			$messageStack->add('Notification Subscriptions database table successfully created.',
				'success');
		}
		
		return true;
	}
	
	// }}}
	
	
	// {{{ _checkZenCartConfigGroupAndOptions()
	
	/**
	 * Makes sure that the configuration group and options are present in the Zen Cart configuration
	 * table. If any are missing, they are created.
	 *
	 * @access  protected
	 * @return  boolean   Whether or not the configuration group and its options are present and
	 *                    valid.
	 */
	function _checkZenCartConfigGroupAndOptions()
	{
		global $db, $messageStack;
		
		$check_config_group_exists_sql = "
			SELECT
				configuration_group_id
			FROM
				" . TABLE_CONFIGURATION_GROUP . "
			WHERE
				configuration_group_title = 'Back In Stock Notifications';";
		
		$check_config_group_exists_result = $db->Execute($check_config_group_exists_sql);
		
		if (!$check_config_group_exists_result->EOF) {
			$configuration_group_id =
				$check_config_group_exists_result->fields['configuration_group_id'];
		} else {
			$add_config_group_options_sql = "
				INSERT INTO
					" . TABLE_CONFIGURATION_GROUP . "
					(
					configuration_group_title,
					configuration_group_description,
					sort_order,
					visible
					)
				VALUES
					(
					'Back In Stock Notifications',
					'Set Ceon Back In Stock Notifications Options',
					'1',
					'1'
					);";
			
			$add_config_group_options_result = $db->Execute($add_config_group_options_sql);
			
			$configuration_group_id_sql = "
				SELECT
					configuration_group_id  
				FROM
					" . TABLE_CONFIGURATION_GROUP . "
				WHERE
					configuration_group_title = 'Back In Stock Notifications';";
			
			$configuration_group_id_result = $db->Execute($configuration_group_id_sql);
			
			if (!$configuration_group_id_result->EOF) {
				$configuration_group_id =
					$configuration_group_id_result->fields['configuration_group_id'];
			} else {
				// Problem getting ID!
				$this->error_messages[] = 'Couldn\'t get the ID of the configuration group!';
				
				return false;
			}
			
			$set_group_sort_order_sql = "
				UPDATE
					" . TABLE_CONFIGURATION_GROUP . "
				SET
					sort_order = '" . $configuration_group_id . "'
				WHERE
					configuration_group_id = '" . $configuration_group_id . "';";
			
			$set_group_sort_order_result = $db->Execute($set_group_sort_order_sql);
			
			$messageStack->add('Configuration group created.', 'success');
		}
		
		
		$check_config_option_exists_sql = "
			SELECT
				configuration_group_id
			FROM
				" . TABLE_CONFIGURATION . "
			WHERE
				configuration_key = 'BACK_IN_STOCK_NOTIFICATION_ENABLED';";
		
		$check_config_option_exists_result = $db->Execute($check_config_option_exists_sql);
		
		if (!$check_config_option_exists_result->EOF) {
			// Make sure the option is assigned to the correct group
			if ($check_config_option_exists_result->fields['configuration_group_id'] !=
					$configuration_group_id) {
				
				$set_group_id_sql = "
					UPDATE
						" . TABLE_CONFIGURATION . "
					SET
						configuration_group_id = '" . $configuration_group_id . "'
					WHERE
						configuration_key = 'BACK_IN_STOCK_NOTIFICATION_ENABLED';";
				
				$set_group_id_result = $db->Execute($set_group_id_sql);
			}
		} else {
			$add_config_option_sql = "
				INSERT INTO
					" . TABLE_CONFIGURATION . "
					(
					`configuration_title`,
					`configuration_key`,
					`configuration_value`,
					`configuration_description`,
					`configuration_group_id`,
					`sort_order`,
					`set_function`,
					`date_added`
					)
				VALUES
					(
					'Enable/Disable Back In Stock Notifications',
					'BACK_IN_STOCK_NOTIFICATION_ENABLED',
					'1',
					'<br />If enabled, when a customer comes across a product that is out of stock, the customer will be offered the chance to be notified when it is back in stock<br /><br />0 = off <br />1 = on',
					'" . $configuration_group_id . "',
					'1',
					'zen_cfg_select_option(array(''0'', ''1''), ',
					NOW()
					);";
			
			$add_config_option_result = $db->Execute($add_config_option_sql);
			
			$messageStack->add('Enable/Disable Back In Stock Notifications configuration group' .
				' option added.', 'success');
		}
		
		
		$check_config_option_exists_sql = "
			SELECT
				configuration_group_id
			FROM
				" . TABLE_CONFIGURATION . "
			WHERE
				configuration_key =
					'SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAILS_TO';";
		
		$check_config_option_exists_result = $db->Execute($check_config_option_exists_sql);
		
		if (!$check_config_option_exists_result->EOF) {
			// Make sure the option is assigned to the correct group
			if ($check_config_option_exists_result->fields['configuration_group_id'] !=
					$configuration_group_id) {
				
				$set_group_id_sql = "
					UPDATE
						" . TABLE_CONFIGURATION . "
					SET
						configuration_group_id = '" . $configuration_group_id . "'
					WHERE
						configuration_key =
							'SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAILS_TO';";
				
				$set_group_id_result = $db->Execute($set_group_id_sql);
			}
		} else {
			$add_config_option_sql = "
				INSERT INTO
					" . TABLE_CONFIGURATION . "
					(
					`configuration_title`,
					`configuration_key`,
					`configuration_value`,
					`configuration_description`,
					`configuration_group_id`,
					`sort_order`,
					`date_added`
					)
				VALUES
					(
					'Send Copy of Back In Stock Notification Subscription E-mails To',
					'SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAILS_TO',
					'',
					'<br />Send copy of Back In Stock Notification Subscription e-mails to the following email addresses, in this format: <br /><br /><code>Name 1 &lt;email@address1&gt;, Name 2 &lt;email@address2&gt;</code>',
					'" . $configuration_group_id . "',
					'2',
					NOW()
					);";
			
			$add_config_option_result = $db->Execute($add_config_option_sql);
			
			$messageStack->add('Send Copy of Subscription E-mails configuration group option' .
				' added.', 'success');
		}
		
		// Make sure configuration group can be displayed in admin menu
		if (function_exists('zen_register_admin_page')) {
			if (!zen_page_key_exists('ceon_bisn_cg')) {
				// Add the link to the Ceon Back In Stock Notifications Zen Cart configuration
				// options to the admin menu
				zen_register_admin_page('ceon_bisn_cg',
					'BOX_CEON_BACK_IN_STOCK_NOTIFICATIONS_CONFIG_GROUP', 'FILENAME_CONFIGURATION',
					'gID=' . $configuration_group_id, 'configuration', 'Y',
					$configuration_group_id);
				
				$messageStack->add('Configuration group added to admin menu.', 'success');
			}
		}
		
		return true;
	}
	
	// }}}
}

// }}}
