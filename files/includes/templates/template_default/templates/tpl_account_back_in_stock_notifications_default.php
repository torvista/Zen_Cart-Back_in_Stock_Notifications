<?php

/**
 * Ceon Back In Stock Notifications Account Back In Stock Notifications Page Template.
 *
 * Allows users to see their subscriptions to "Back In Stock" notification lists for particular
 * products and remove themselves if desired.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: tpl_account_back_in_stock_notifications_default.php 937 2012-02-10 11:42:20Z conor $
 */

?>
<div class="centerColumn">
	
	<h1 id="accountDefaultHeading"><?php echo ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE; ?></h1>
<?php

/**
 * Load the template class
 */
require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.CeonXHTMLHiTemplate.php');

// Load in and extract the template parts for Back In Stock Notification functionality
$bisn_template_filename = $template->get_template_dir('inc.html.back_in_stock_notifications.html',
	DIR_WS_TEMPLATE, $current_page_base, 'templates') . '/' .
	'inc.html.back_in_stock_notifications.html';

$bisn_template = new CeonXHTMLHiTemplate($bisn_template_filename);

$bisn_template_parts = $bisn_template->extractTemplateParts();


$content_title = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE;

// Output the list of Back In Stock Notification Lists this user is subscribed to
$back_in_stock_notifications = new CeonXHTMLHiTemplate;

// Load in the source for the form
$back_in_stock_notifications->setXHTMLSource(
	$bisn_template_parts['ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS']);

// Add the form action
$form_start_tag = zen_draw_form('back_in_stock_notifications',
	zen_href_link(FILENAME_ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS, zen_get_all_get_params(), 'SSL'),
	'post');

$back_in_stock_notifications->setVariable('form_start_tag', $form_start_tag);

// Build back button
// Check if image exists
$image_src = zen_output_string($template->get_template_dir(BUTTON_IMAGE_BACK, DIR_WS_TEMPLATE,
	$current_page_base, 'buttons/' . $_SESSION['language'] . '/') . $image);

if (file_exists($image_src)) {
	$back_button_source = zen_image_submit(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT, 'name="back"');
} else {
	$back_button_source = '<input type="submit" name="back" value="' . BUTTON_BACK_ALT .'" />';
}

$back_in_stock_notifications->setVariable('back_button', $back_button_source);

// Add the title to the subscriptions overview box
$product_back_in_stock_notifications_overview_title =
	ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_OVERVIEW_TITLE;
	
$back_in_stock_notifications->setVariable('overview_title',
	$product_back_in_stock_notifications_overview_title);

// Add the text to the page
if (isset($intro1)) {
	$back_in_stock_notifications->setVariable('intro1', $intro1);
	$back_in_stock_notifications->setVariable('intro_instructions', $intro_instructions);
	
} else if (isset($intro_none_selected)) {
	$back_in_stock_notifications->setVariable('intro_none_selected', $intro_none_selected);
	$back_in_stock_notifications->setVariable('intro_instructions', $intro_instructions);
	
} else if (isset($intro_success)) {
	$back_in_stock_notifications->setVariable('intro_success', $intro_success);
	$back_in_stock_notifications->setVariable('intro_unsubscribed_products',
		$intro_unsubscribed_products);
	
	if ($intro_instructions != '') {
		$back_in_stock_notifications->setVariable('intro_instructions', $intro_instructions);
	}
}

if (sizeof($subscribed_notification_lists) == 0) {
	// User isn't subscribed to any Back In Stock Notification Lists
	$no_subscriptions_message = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_NOT_SUBSCRIBED;
	
	$back_in_stock_notifications->setVariable('no_subscriptions_message',
		$no_subscriptions_message);
} else {
	// Output the list of Back In Stock Notification Lists this user is subscribed to
	$back_in_stock_notifications_table = new CeonXHTMLHiTemplate;
	
	// Load in the source for the form
	$back_in_stock_notifications_table->setXHTMLSource(
		$bisn_template_parts['ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_TABLE']);
	
	// Add the table headers and update button
	$product_back_in_stock_notifications_table_title =
		ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_TABLE_TITLE;
		
	$back_in_stock_notifications_table->setVariable('table_title',
		$product_back_in_stock_notifications_table_title);
	
	$header_subscribed = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_TABLE_HEADER_SUBSCRIBED;
	$header_product = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_TABLE_HEADER_PRODUCT;
	$header_date_subscribed = ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_TABLE_HEADER_DATE_SUBSCRIBED;
	
	$back_in_stock_notifications_table->setVariable('header_subscribed', $header_subscribed);
	$back_in_stock_notifications_table->setVariable('header_product', $header_product);
	$back_in_stock_notifications_table->setVariable('header_date_subscribed',
		$header_date_subscribed);

	// Build the list of subscriptions
	$listbox_template_prefix = 'ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS';
	$listbox_item_index = 1;
	
	$placement_marker_key = $listbox_template_prefix . '_ITEM1';
	
	for ($i = 0, $n = sizeof($subscribed_notification_lists); $i < $n; $i++) {
		
		$back_in_stock_notifications_item = new CeonXHTMLHiTemplate();
		
		// Check if template exists, otherwise rewind back to first (Column/rows created by template!)
		if (!isset($bisn_template_parts[$listbox_template_prefix . '_ITEM' .
				$listbox_item_index])) {
			$listbox_item_index = 1;
		}
		
		$back_in_stock_notifications_item->setXHTMLSource(
			$bisn_template_parts[$listbox_template_prefix . '_ITEM' . $listbox_item_index]);
		
		$listbox_item_index++;
		
		// Add the checkbox
		$checkbox = '<input type="checkbox" name="stay_subscribed_to[]" value="' .
			$subscribed_notification_lists[$i]['id'] . '" checked="checked" />';
			
		$back_in_stock_notifications_item->setVariable('checkbox', $checkbox);
		
		
		// Add the product's name
		$product_name =
			htmlentities($subscribed_notification_lists[$i]['product_name'], ENT_COMPAT, CHARSET);
			
		$back_in_stock_notifications_item->setVariable('product_name', $product_name);
		
		
		// Add the product's model
		$product_model =
			htmlentities($subscribed_notification_lists[$i]['product_model'], ENT_COMPAT, CHARSET);
			
		$back_in_stock_notifications_item->setVariable('product_model', $product_model);
		
		
		// Add a link to the product's page
		$product_page = zen_get_info_page($subscribed_notification_lists[$i]['id']);
		
		$product_info_page_link = zen_href_link($product_page,
			'products_id=' . $subscribed_notification_lists[$i]['id'], $request_type);
			
		$back_in_stock_notifications_item->setVariable('product_info_page_link',
			$product_info_page_link);  
		
		
		// Add the date subscribed
		$date = zen_date_long($subscribed_notification_lists[$i]['date']);
		
		$back_in_stock_notifications_item->setVariable('date_subscribed', $date);
		
		
		// Append a placement for the next product after this one
		$placement_marker = '{ceon:' . $placement_marker_key . '}';
		
		$back_in_stock_notifications_item->appendSource($placement_marker);
		
		// Add the current product to the table
		$back_in_stock_notifications_table->setVariable($placement_marker_key,
			$back_in_stock_notifications_item->getXHTMLSource());
	}

	// Add the table to the page
	$back_in_stock_notifications->setVariable('ACCOUNT_BACK_IN_STOCK_NOTIFICATIONS_TABLE',
		$back_in_stock_notifications_table->getXHTMLSource());

	// Build update button
	// Check if image exists
	$image_src = zen_output_string($template->get_template_dir(BUTTON_IMAGE_UPDATE, DIR_WS_TEMPLATE,
		$current_page_base, 'buttons/' . $_SESSION['language'] . '/') . $image);
	
	if (file_exists($image_src)) {
		$update_button_source = zen_image_submit(BUTTON_IMAGE_UPDATE, BUTTON_UPDATE_ALT,
			'name="submit"');
	} else {
		$update_button_source = '<input type="submit" name="submit" value="' . BUTTON_UPDATE_ALT .
			'" />';
	}
	$back_in_stock_notifications->setVariable('update_button', $update_button_source);
}

$back_in_stock_notifications->cleanSource();

print $back_in_stock_notifications->getXHTMLSource();

?>
</div>
