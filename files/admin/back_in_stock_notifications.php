<?php

/**
 * Back In Stock Notifications
 *
 * @author     Conor Kerr <back_in_stock_notifications@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2008 RubikIntegration team @ RubikIntegration.com
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/back_in_stock_notifications
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: back_in_stock_notifications.php 279 2009-01-13 18:21:43Z Bob $
 */

require('includes/application_top.php');

require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'back_in_stock_notifications_functions.php');

$bisn_options = array (
	0 => array(
		'id' => 1,
		'text' => TEXT_LIST_ALL_SUBSCRIBED_PRODUCTS
		),
	1 => array(
		'id' => 2,
		'text' => TEXT_LIST_ALL_SUBSCRIPTIONS
		),
	2 => array(
		'id' => 3,
		'text' => TEXT_PREVIEW_NOTIFICATION_EMAILS
		),
	3 => array(
		'id' => 4,
		'text' => TEXT_SEND_NOTIFICATION_EMAILS
		),
	4 => array(
		'id' => 5,
		'text' => TEXT_REMOVE_DELETED_PRODUCTS
		)
	);

if (!isset($_GET['option']) || !is_numeric($_GET['option']) || (int)$_GET['option'] < 1 || $_GET['option'] > 5)
	$_GET['option'] = 1;

switch($_GET['option']){
	case 1:
		$products_query_raw = "
			SELECT
				bisns.product_id, pd.products_name, COUNT(*) AS num_subscribers, p.products_type,
				p.products_quantity AS current_stock
			FROM
				" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " bisns
			LEFT JOIN
				" . TABLE_PRODUCTS_DESCRIPTION . " pd
			ON
				pd.products_id = bisns.product_id
			LEFT JOIN
				" . TABLE_PRODUCTS . " p
			ON
				p.products_id = pd.products_id
			WHERE
				pd.language_id = '" . $_SESSION['languages_id'] . "'
			GROUP BY
				bisns.product_id
			ORDER BY
				pd.products_name";
		
		$products_query_raw = strtolower(str_replace("\n", ' ', $products_query_raw));
		$products_query_raw = str_replace("\r", ' ', $products_query_raw);
		$products_query_raw = str_replace("\t", ' ', $products_query_raw);
		
		$products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);
		
		// Get accurate value for the number of rows
		$products_num_rows_query = "
			SELECT
				bisns.product_id, pd.products_name
			FROM
				" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " bisns
			LEFT JOIN
				" . TABLE_PRODUCTS_DESCRIPTION . " pd
			ON
				pd.products_id = bisns.product_id
			WHERE
				pd.language_id = '" . $_SESSION['languages_id'] . "'
			GROUP BY
				bisns.product_id";
		$products_num_rows_result = $db->Execute($products_num_rows_query);
		$products_query_numrows = $products_num_rows_result->RecordCount();
		
		$products_values = $db->Execute($products_query_raw);
		break;
	case 2:
		$subscriptions_query_raw = "
			SELECT
				bisns.product_id, bisns.name, bisns.email_address, bisns.date_subscribed, 
				pd.products_name, p.products_type, c.customers_email_address
			FROM
				" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " bisns
			LEFT JOIN
				" . TABLE_PRODUCTS_DESCRIPTION . " pd
			ON
				pd.products_id = bisns.product_id
			LEFT JOIN
				" . TABLE_PRODUCTS . " p
			ON
				p.products_id = pd.products_id
			LEFT JOIN
				" . TABLE_CUSTOMERS . " c
			ON
				c.customers_id = bisns.customer_id
			WHERE
				pd.language_id = '" . $_SESSION['languages_id'] . "'
			ORDER BY
				pd.products_name, bisns.date_subscribed DESC";
		
		$subscriptions_query_raw = strtolower(str_replace("\n", ' ', $subscriptions_query_raw));
		$subscriptions_query_raw = str_replace("\r", ' ', $subscriptions_query_raw);
		$subscriptions_query_raw = str_replace("\t", ' ', $subscriptions_query_raw);
		
		$subscriptions_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $subscriptions_query_raw, $subscriptions_query_numrows);
		$subscriptions_values = $db->Execute($subscriptions_query_raw);
		break;
	case 3:
		$send_output = sendBackInStockNotifications(true);
		break;
	case 4:
		$send_output = sendBackInStockNotifications();
		break;
	case 5:
		expungeOutdatedSubscriptionsFromBackInStockNotificationsDB();		
		break;
}

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
	<title><?php echo TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
	<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
	<script language="javascript" src="includes/menu.js"></script>
	<script language="javascript" src="includes/general.js"></script>
	<script type="text/javascript">
	<!--
	function init()
	{
		cssjsmenu('navbar');
		if (document.getElementById)
		{
			var kill = document.getElementById('hoverJS');
			kill.disabled = true;
		}
	}
	// -->
	</script>
	<style type="text/css">
		dl#back_in_stock_notifications_output dt { margin-top: 0.95em; }
		dl#back_in_stock_notifications_output dd { font-weight: bold; margin-left: 2.5em; margin-top: 0.3em; }
	</style>
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
		<!-- body_text //-->
		<td width="100%" valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td class="pageHeading"><?php echo BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE; ?></td>
								<td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			
			<div id="actionSelector">
				<?php echo zen_draw_form('back_in_stock_notifications', FILENAME_BACK_IN_STOCK_NOTIFICATIONS, '', 'GET'); ?>
					<fieldset style="margin-bottom: 2em;">
						<legend><?php echo TEXT_ACTION_TO_PERFORM; ?></legend>
						<input type="hidden" name="action" value="send" />
						<?php echo zen_draw_pull_down_menu('option', $bisn_options, $_GET['option']); ?>
						<input type="submit" value="Go!" />
					</fieldset>
				</form>
			</div>
<?php
if (isset($_GET['option']) && $_GET['option'] == 1) {
?>
			<table border="0" cellspacing="1" cellpadding="2" align="center">
				<tr>
					<td colspan="2">
						<span class="forward">
							<?php 
								echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS);
								echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'action'))); 
							?>
						</span>
					</td>
				</tr>
				<tr class="dataTableHeadingRow">
					<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_NAME;?></td>
					<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NUM_SUBSCRIBERS;?></td>
					<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENT_STOCK;?></td>
				</tr>
<?php
	while (!$products_values->EOF) {
?>
				<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
					<td class="dataTableContent">
						<?php echo process_product_name($products_values->fields['products_name'], $products_values->fields['product_id'], $products_values->fields['products_type']);?>
					</td>
					<td class="dataTableContent">
						<?php echo $products_values->fields['num_subscribers'];?>
					</td>
					<td class="dataTableContent">
						<?php echo $products_values->fields['current_stock'];?>
					</td>
				</tr>
<?php
		$products_values->MoveNext();
	}
?>
				<tr>
					<td colspan="2">
						<span class="forward">
							<?php 
								echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS);
								echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'action'))); 
							?>
						</span>
					</td>
				</tr>
			</table>
<?php
} elseif(isset($_GET['option']) && $_GET['option'] == 2) {
?>
			<table border="0" cellspacing="1" cellpadding="2" align="center">
				<tr>
					<td colspan="5">
						<span class="forward">
						<?php 
							echo $subscriptions_split->display_count($subscriptions_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BACK_IN_STOCK_NOTIFICATIONS);
							echo $subscriptions_split->display_links($subscriptions_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'action'))); 
						?>
						</span>
					</td>
				</tr>
				<tr class="dataTableHeadingRow">
					<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_NAME;?></td>
					<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DATE_SUBSCRIBED;?></td>
					<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMER_NAME;?></td>
					<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMER_EMAIL;?></td>
				</tr>
<?php
	while (!$subscriptions_values->EOF) {
?>
				<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
					<td class="dataTableContent">
						<?php echo process_product_name($subscriptions_values->fields['products_name'], $subscriptions_values->fields['product_id'], $subscriptions_values->fields['products_type']);?>
					</td>
					<td class="dataTableContent">
						<?php echo zen_date_long($subscriptions_values->fields['date_subscribed']);?>
					</td>
					<td class="dataTableContent">
						<?php echo $subscriptions_values->fields['name'];?>
					</td>
					<td class="dataTableContent">
						<?php
						$customer_email_address = (!is_null($subscriptions_values->fields['email_address']) ? $subscriptions_values->fields['email_address'] :
							$subscriptions_values->fields['customers_email_address']);
						echo $customer_email_address;
						?>
					</td>
				</tr>
<?php
		$subscriptions_values->MoveNext();
	}
?>
				<tr>
					<td colspan="5">
						<span class="forward">
						<?php 
						echo $subscriptions_split->display_count($subscriptions_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BACK_IN_STOCK_NOTIFICATIONS);
						echo $subscriptions_split->display_links($subscriptions_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'action'))); 
						?>
						</span>
					</td>
				</tr>
			</table>
<?php
} else if (isset($_GET['option']) && ($_GET['option'] == 3 || $_GET['option'] == 4)) {
?>
			<fieldset>
				<legend><?php echo TEXT_SEND_OUTPUT_TITLE; ?></legend>
<?php
	print $send_output;
?>
			</fieldset>
<?php
}
?>
		</td>
	</tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<div class="footer-area">
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>