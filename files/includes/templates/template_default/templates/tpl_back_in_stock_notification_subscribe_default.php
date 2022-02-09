<?php

/**
 * Ceon Back In Stock Notifications Subscription Page Template.
 *
 * Allows users to subscribe to a "Back In Stock" notification list for a given product.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: tpl_back_in_stock_notification_subscribe_default.php 937 2012-02-10 11:42:20Z conor $
 */

?>
<div class="centerColumn">
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

$content_title = BACK_IN_STOCK_NOTIFICATION_HEADING_TITLE;

if ($build_form) {
	// Output form
	// Build the notification request form
	$back_in_stock_notification_form = new CeonXHTMLHiTemplate;
	
	// Load in the source for the form
	$back_in_stock_notification_form->setXHTMLSource(
		$bisn_template_parts['BACK_IN_STOCK_NOTIFICATION_FORM']);
	
	// Add the form action, titles, labels and button
	$form_start_tag = zen_draw_form('back_in_stock_notification',
		zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATION_SUBSCRIBE,
		zen_get_all_get_params(array('number_of_uploads')), $request_type), 'post');
	
	$back_in_stock_notification_form->setVariable('back_in_stock_notification_form_start_tag',
		$form_start_tag);

	$product_back_in_stock_notification_form_title = BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_TITLE;
	
	$back_in_stock_notification_form->setVariable('title',
		$product_back_in_stock_notification_form_title);
	
	$name_label = BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_NAME;
	$email_label = BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_EMAIL;
	$email_confirmation_label = BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_ENTRY_CONFIRM_EMAIL;
	
	$back_in_stock_notification_form->setVariable('name_label', $name_label);
	$back_in_stock_notification_form->setVariable('email_label', $email_label);
	$back_in_stock_notification_form->setVariable('email_confirmation_label',
		$email_confirmation_label);
	
	$submit_button = zen_image_submit(BUTTON_IMAGE_NOTIFY_ME, BUTTON_NOTIFY_ME_ALT,
		'name="notify_me"');
	$back_in_stock_notification_form->setVariable('submit_button', $submit_button);
	
	// Add in the introductory text
	$intro_text = sprintf(BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_INTRO, htmlentities($product_name,
		ENT_COMPAT, CHARSET));
	$notice_text = BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_NOTICE;
	
	$back_in_stock_notification_form->setVariable('intro', $intro_text);
	$back_in_stock_notification_form->setVariable('notice', $notice_text);
	
	// Add the customer's details to the form (empty unless logged in)
	$back_in_stock_notification_form->setVariable('name',
		$back_in_stock_notification_form_customer_name);
	$back_in_stock_notification_form->setVariable('email',
		$back_in_stock_notification_form_customer_email);
	$back_in_stock_notification_form->setVariable('cofnospam',
		$back_in_stock_notification_form_customer_email_confirmation);
	
	// Add any error message to the form
	if (sizeof($form_errors) > 0) {
		$error_intro_text = BACK_IN_STOCK_NOTIFICATION_FORM_ERROR_INTRO;
		$back_in_stock_notification_form->setVariable('form_error_intro', $error_intro_text);
		
		if (isset($form_errors['name'])) {
			$back_in_stock_notification_form->setVariable('name_error', $form_errors['name']);
		}
		
		if (isset($form_errors['email'])) {
			$back_in_stock_notification_form->setVariable('email_error', $form_errors['email']);
		}
		
		if (isset($form_errors['cofnospam'])) {
			$back_in_stock_notification_form->setVariable('email_confirmation_error',
				$form_errors['cofnospam']);
		}
	}
	
	$back_in_stock_notification_form->cleanSource();
	
	print $back_in_stock_notification_form->getXHTMLSource();
	
} else if ($already_subscribed) {
	// E-mail address is already subscribed, let the user know
	$back_in_stock_notification_already_subscribed = new CeonXHTMLHiTemplate;
	
	// Load in the source for the message
	$back_in_stock_notification_already_subscribed->setXHTMLSource(
		$bisn_template_parts['BACK_IN_STOCK_NOTIFICATION_ALREADY_SUBSCRIBED']);
	
	// Add the title
	$title = BACK_IN_STOCK_NOTIFICATION_ALREADY_SUBSCRIBED_TITLE;
	$back_in_stock_notification_already_subscribed->setVariable('title', $title);
	
	// Add the message
	$already_subscribed_message = sprintf(BACK_IN_STOCK_NOTIFICATION_ALREADY_SUBSCRIBED_MESSAGE,
		htmlentities($product_name, ENT_COMPAT, CHARSET));
	
	$back_in_stock_notification_already_subscribed->setVariable('message',
		$already_subscribed_message);
	
	// Build and add the continue button
	$product_page = zen_get_info_page((int) $_GET['products_id']);
	
	$continue_button = '<a href="' . zen_href_link($product_page,
		zen_get_all_get_params(array('number_of_uploads')), $request_type) . '">' .
		zen_image_button(BUTTON_IMAGE_CONTINUE, BUTTON_CONTINUE_ALT) . '</a>';
	
	$back_in_stock_notification_already_subscribed->setVariable('continue_button',
		$continue_button);
	
	$back_in_stock_notification_already_subscribed->cleanSource();
	
	print $back_in_stock_notification_already_subscribed->getXHTMLSource();
	
} else {
	// Build the success message
	$back_in_stock_notification_success = new CeonXHTMLHiTemplate;
	
	// Load in the source for the message
	$back_in_stock_notification_success->setXHTMLSource(
		$bisn_template_parts['BACK_IN_STOCK_NOTIFICATION_SUCCESS']);
	
	// Add the title
	$title = BACK_IN_STOCK_NOTIFICATION_SUCCESS_TITLE;
	$back_in_stock_notification_success->setVariable('title', $title);
	
	// Add the message text
	$back_in_stock_notification_success->setVariable('success_message1', $success_message1);
	
	$back_in_stock_notification_success->setVariable('success_message2', $success_message2);
	
	$back_in_stock_notification_success->setVariable('unsubscribe_message', $unsubscribe_message);
	
	// Build and add the continue button
	$product_page = zen_get_info_page((int) $_GET['products_id']);
	
	$continue_button = '<a href="' . zen_href_link($product_page,
		zen_get_all_get_params(array('number_of_uploads')), $request_type) . '">' .
		zen_image_button(BUTTON_IMAGE_CONTINUE, BUTTON_CONTINUE_ALT) . '</a>';
	
	$back_in_stock_notification_success->setVariable('continue_button', $continue_button);
	
	$back_in_stock_notification_success->cleanSource();
	
	print $back_in_stock_notification_success->getXHTMLSource();
}

?>
</div>