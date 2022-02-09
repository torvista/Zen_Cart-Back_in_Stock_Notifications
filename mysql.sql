#
# Uncomment the following to remove the Back In Stock Notifications settings before
# creating/recreating them.
#

#SELECT @bisn_id:=configuration_group_id
#FROM configuration_group WHERE configuration_group_title="Back In Stock Notifications";
#DELETE FROM configuration WHERE configuration_group_id=@bisn_id;
#DELETE FROM configuration_group WHERE configuration_group_id=@bisn_id;



SET @t4=0;
SELECT (@t4:=configuration_group_id) as t4 
FROM configuration_group
WHERE configuration_group_title= 'Back In Stock Notifications';
DELETE FROM configuration WHERE configuration_group_id = @t4;
DELETE FROM configuration_group WHERE configuration_group_id = @t4;

INSERT INTO configuration_group (configuration_group_title, configuration_group_description, sort_order, visible) VALUES ('Back In Stock Notifications', 'Set Back In Stock Notifications Options', '1', '1');
UPDATE configuration_group SET sort_order = last_insert_id() WHERE configuration_group_id = last_insert_id();

SET @t4=0;
SELECT (@t4:=configuration_group_id) as t4 
FROM configuration_group
WHERE configuration_group_title= 'Back In Stock Notifications';

INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `last_modified`,  `use_function`, `set_function`, `date_added`) VALUES ('Enable/Disable Back In Stock Notification', 'BACK_IN_STOCK_NOTIFICATION_ENABLED', '1', 'If enabled, when a customer comes across a product that is out of stock, the customer will be offered the chance to be notified when it is back in stock<br /><br />0 = off <br />1 = on', @t4, '1', NOW(), NULL, 'zen_cfg_select_option(array(''0'', ''1''), ', NOW());

INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`) 
VALUES ('Send Copy of Back In Stock Notification Subscription E-mails To', 'SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAILS_TO', '', 'Send copy of Back In Stock Notification Subscription e-mails to the following email addresses, in this format: Name 1 <email@address1>, Name 2 <email@address2>', @t4, '2', NOW(), NOW()
);


CREATE TABLE IF NOT EXISTS `back_in_stock_notification_subscriptions` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`product_id` int(10) unsigned NOT NULL default '0',
	`customer_id` int(10) unsigned default NULL,
	`subscription_code` varchar(10) default NULL,
	`name` varchar(64) NOT NULL default '',
	`email_address` varchar(96) default NULL,
	`date_subscribed` datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY  (`id`)
);

