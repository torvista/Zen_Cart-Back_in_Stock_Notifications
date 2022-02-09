<?php
/**
 * Zen Cart : Back In Stock Notification Unsubscribe
 *
 * Allows users to unsubscribe from a "Back In Stock" notification list for a given product.
 *
 * @author     Conor Kerr <back_in_stock_notifications@dev.ceon.net>
 * @copyright  Copyright 2004-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/back_in_stock_notifications
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: tpl_back_in_stock_notification_unsubscribe_default.php 279 2009-01-13 18:21:43Z Bob $
 */
?>
<div class="centerColumn">
	
	<h1 id="accountDefaultHeading"><?php echo BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_HEADING_TITLE; ?></h1>
<?php

/**
 * Load the template class
 */
require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.CeonXHTMLHiTemplate.php');

// Load in and extract the template parts for Back In Stock Notification functionality
$bisn_template_filename = DIR_FS_CATALOG . DIR_WS_TEMPLATES . 'template_default/templates/' .
	'inc.html.back_in_stock_notifications.html';

$bisn_template = new CeonXHTMLHiTemplate($bisn_template_filename);

$bisn_template_parts = $bisn_template->extractTemplateParts();

$content_title = BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_HEADING_TITLE;

if ($action == 'display_details') {
	// Output form
	// Build the notification request form
	$back_in_stock_notification_unsubscribe_form = new CeonXHTMLHiTemplate;
	
	// Load in the source for the form
	$back_in_stock_notification_unsubscribe_form->setXHTMLSource(
		$bisn_template_parts['BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_FORM']);
	
	// Add the form action, titles and button
	$form_start_tag = zen_draw_form('back_in_stock_notification',
		zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE, '', 'NONSSL'), 'POST');
	$back_in_stock_notification_unsubscribe_form->setVariable(
		'back_in_stock_notification_unsubscribe_form_start_tag', $form_start_tag);
	
	// Build and add the back button
	$back_button = '<a href="' . zen_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '">' .
		zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>';
	$back_in_stock_notification_unsubscribe_form->setVariable('back_button', $back_button);
	
	// Build and add the submit button
	$submit_button = zen_image_submit(BUTTON_IMAGE_UNSUBSCRIBE, BUTTON_UNSUBSCRIBE,
		'name="confirm"');
	$back_in_stock_notification_unsubscribe_form->setVariable('submit_button', $submit_button);
	
	// Add in the introductory text
	$message_text = sprintf(BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_TEXT_FORM_MESSAGE,
		htmlentities($product_name), htmlentities(BUTTON_UNSUBSCRIBE));
	$back_in_stock_notification_unsubscribe_form->setVariable('message', $message_text);
	
	// Add in the data about the notification to be unsubscribed from!
	$back_in_stock_notification_unsubscribe_form->setVariable('id',
		$back_in_stock_notification_id);
	$back_in_stock_notification_unsubscribe_form->setVariable('code',
		$back_in_stock_notification_code);
	
	$back_in_stock_notification_unsubscribe_form->cleanSource();
	
	print $back_in_stock_notification_unsubscribe_form->getXHTMLSource();
} else {
	// Build an error/success message
	$back_in_stock_notification_unsubscribe_success = new CeonXHTMLHiTemplate;
	
	// Load in the source for the message
	$back_in_stock_notification_unsubscribe_success->setXHTMLSource(
		$bisn_template_parts['BACK_IN_STOCK_NOTIFICATION_UNSUBSCRIBE_MESSAGE']);
	
	// Add the title
	$back_in_stock_notification_unsubscribe_success->setVariable('title',
		$back_in_stock_notification_unsubscribe_title);
	
	// Add the message text
	$back_in_stock_notification_unsubscribe_success->setVariable('message',
		$back_in_stock_notification_unsubscribe_message);
	
	// Build and add the continue button
	$continue_button = '<a href="' . zen_href_link(FILENAME_DEFAULT, zen_get_all_get_params(),
		'NONSSL') . '">' . zen_image_button(BUTTON_IMAGE_CONTINUE, BUTTON_CONTINUE_ALT) . '</a>';
	$back_in_stock_notification_unsubscribe_success->setVariable('continue_button',
		$continue_button);
	
	$back_in_stock_notification_unsubscribe_success->cleanSource();
	
	print $back_in_stock_notification_unsubscribe_success->getXHTMLSource();

}

?>
</div>