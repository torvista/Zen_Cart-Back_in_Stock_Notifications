<?php

/**
 * Ceon Back In Stock Notifications Admin Utility.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @author      Tony Niemann <tony@loosechicken.com>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2008 RubikIntegration team @ RubikIntegration.com
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications.php 937 2012-02-10 11:42:20Z conor $
 */

/**
 * Version info - don't touch!
 */
define('CEON_BACK_IN_STOCK_NOTIFICATIONS_VERSION', '3.2.0');

require('includes/application_top.php');

// First off, make sure that necessary database table and configuration options exist. If not,
// attempt to create them
$table_exists_query = 'SHOW TABLES LIKE "' .
	TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . '";';

$table_exists_result = $db->Execute($table_exists_query);

if ($table_exists_result->EOF ||
		!defined('SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAILS_TO') ||
		isset($_GET['check-config'])) {
	// Instantiate and run the installation/upgrade class
	require_once(DIR_WS_CLASSES . 'class.CeonBISNInstallOrUpgrade.php');
	
	$install_or_upgrade = new CeonBISNInstallOrUpgrade();
	
	if (sizeof($install_or_upgrade->error_messages) > 0) {
		foreach ($install_or_upgrade->error_messages as $error_message) {
			print '<p style="background: #fcc; border: 1px solid #f00; margin: 1em;' .
				' padding: 0.4em;">Error: ' . $error_message . "</p>\n";
		}
		
		require(DIR_WS_INCLUDES . 'application_bottom.php');
		
		exit;
	}
}

require_once(DIR_WS_FUNCTIONS . 'back_in_stock_notifications_functions.php');

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

if (!isset($_GET['option']) || !is_numeric($_GET['option']) || (int)$_GET['option'] < 1 ||
	$_GET['option'] > 5) {
	$_GET['option'] = 1;
}

switch($_GET['option']){
	case 1:
		$products_query_raw = "
			SELECT
				bisns.product_id, pd.products_name, COUNT(*) AS num_subscribers, p.products_type,
				p.products_quantity AS current_stock, cd.categories_name
			FROM
				" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " bisns
			LEFT JOIN
				" . TABLE_PRODUCTS_DESCRIPTION . " pd
			ON
				(pd.products_id = bisns.product_id
			AND
				pd.language_id = '" . $_SESSION['languages_id'] . "')
			LEFT JOIN
				" . TABLE_PRODUCTS . " p
			ON
				p.products_id = pd.products_id
			LEFT JOIN 
				" . TABLE_CATEGORIES_DESCRIPTION . " cd 
			ON 
				(p.master_categories_id = cd.categories_id
			AND
				cd.language_id = '" . $_SESSION['languages_id'] . "')
			WHERE
				1 = 1
			GROUP BY
				bisns.product_id";
		
		$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'category';
		
		switch ($sort_column) {
			case 'product':
				$products_query_raw .= ' ORDER BY pd.products_name';
				break;
			case 'subscribers':
				$products_query_raw .= ' ORDER BY num_subscribers DESC';
				break;
			case 'stock':
				$products_query_raw .= ' ORDER BY p.products_quantity DESC';
				break;
			default:
				$products_query_raw .= ' ORDER BY cd.categories_name, pd.products_name';
		}
		
		$products_query_raw = str_replace("\n", ' ', $products_query_raw);
		$products_query_raw = str_replace("\r", ' ', $products_query_raw);
		$products_query_raw = str_replace("\t", ' ', $products_query_raw);
		
		if ($_GET['page'] != -1) {
			$products_split = new splitPageResults($_GET['page'],
				MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $products_query_raw, $num_rows);
		}
		
		$product_subscriptions_info = $db->Execute($products_query_raw);
		
		// Get accurate value for the number of rows
		$num_rows_query = "
			SELECT
				bisns.id
			FROM
				" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " bisns
			WHERE
				1 = 1;";
		
		$num_rows_result = $db->Execute($num_rows_query);
		$num_rows = $num_rows_result->RecordCount();
		
		break;
		
	case 2:
		$subscriptions_query_raw = "
			SELECT
				DISTINCT bisns.id, bisns.product_id, bisns.name, bisns.email_address,
				bisns.date_subscribed, pd.products_name, p.products_type, c.customers_email_address,
				cd.categories_name
			FROM
				" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " bisns
			LEFT JOIN
				" . TABLE_PRODUCTS_DESCRIPTION . " pd
			ON
				(pd.products_id = bisns.product_id
			AND
				pd.language_id = '" . $_SESSION['languages_id'] . "')
			LEFT JOIN
				" . TABLE_PRODUCTS . " p
			ON
				p.products_id = pd.products_id
			LEFT JOIN
				" . TABLE_CUSTOMERS . " c
			ON
				c.customers_id = bisns.customer_id
			LEFT JOIN 
				" . TABLE_CATEGORIES_DESCRIPTION . " cd 
			ON 
				(p.master_categories_id = cd.categories_id
			AND
				cd.language_id = '" . $_SESSION['languages_id'] . "')
			WHERE
				1 = 1";
		
		$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'product';
		
		switch ($sort_column) {
			case 'category':
				$subscriptions_query_raw .= ' ORDER BY cd.categories_name, pd.products_name,' .
					' bisns.date_subscribed DESC';
				break;
			case 'date':
				$subscriptions_query_raw .= ' ORDER BY bisns.date_subscribed DESC,' .
					'cd.categories_name, pd.products_name';
				break;
			case 'customer_name':
				$subscriptions_query_raw .= ' ORDER BY bisns.name, bisns.date_subscribed DESC,' .
					' cd.categories_name, pd.products_name';
				break;
			case 'customer_email':
				$subscriptions_query_raw .= ' ORDER BY bisns.email_address,' .
					' c.customers_email_address, bisns.date_subscribed DESC,' .
					' cd.categories_name, pd.products_name';
				break;
			default:
				$subscriptions_query_raw .= ' ORDER BY pd.products_name,' .
					' bisns.date_subscribed DESC';
		}
		
		$subscriptions_query_raw = str_replace("\n", ' ', $subscriptions_query_raw);
		$subscriptions_query_raw = str_replace("\r", ' ', $subscriptions_query_raw);
		$subscriptions_query_raw = str_replace("\t", ' ', $subscriptions_query_raw);
		
		if ($_GET['page'] != -1) {
			$subscriptions_split = new splitPageResults($_GET['page'],
				MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $subscriptions_query_raw, $num_rows);
		}
		
		$subscriptions_info = $db->Execute($subscriptions_query_raw);
		
		// Get accurate value for the number of rows
		$num_rows_query = "
			SELECT
				bisns.id
			FROM
				" . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " bisns
			WHERE
				1 = 1;";
		
		$num_rows_result = $db->Execute($num_rows_query);
		$num_rows = $num_rows_result->RecordCount();
		
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
	<script type="text/javascript" language="JavaScript" src="includes/menu.js"></script>
	<script type="text/javascript" language="JavaScript" src="includes/general.js"></script>
	<script type="text/javascript" language="JavaScript">
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
	#ceon-bisn-wrapper {
		margin: 0 0.8em 0 0.8em;
	}
	h1#ceon-bisn-page-heading { margin-top: 1em; padding-bottom: 1.2em; }
	fieldset { padding: 0.8em 0.8em; line-height: 1.3; }
	fieldset fieldset { margin-bottom: 1em; }
	legend { font-weight: bold; font-size: 1.3em; }
	.SpacerSmall { clear: both; }
	.NoMarginBottom { margin-bottom : 0; }
	p { margin: 0 0 0.8em 0; }
	
	fieldset.CeonPanel {
		background: #fff;
		border: 1px solid #296629;
	}
	fieldset.CeonPanel legend {
		font-size: 1.4em;
		background: #fff;
		padding: 0.1em 0.4em 1em 0.4em;
	}
	fieldset.CeonPanel fieldset legend {
		background: none;
		padding-bottom: 0.4em;
		font-size: 1.3em;
	}
	
	fieldset fieldset {
		background: #f3f3f3;
		box-shadow: inset 0px 0px 8px 1px #e3e3e3;
	}
	fieldset.CeonPanel fieldset legend {
		background: transparent;
	}
	
	ul#ceon-panels-menu {
		list-style: none;
		margin: 1em 0 0 0;
		padding: 0 0 0.6em 0;
		background: #599659;
		white-space: nowrap;
	}
	ul#ceon-panels-menu li {
		display: inline;
		padding: 0;
		margin: 0;
	}
	ul#ceon-panels-menu li a {
		background: #79b679;
		color: #fff;
		font-size: 1.2em;
		padding: 0.3em 2em 0.7em 2em;
		font-weight: bold;
		margin: 0 0.4em 0 0;
		border-left: 1px solid #79b679;
		border-top: 1px solid #79b679;
	}
	ul#ceon-panels-menu li a:hover {
		background: #89c689;
		border-left: 1px solid #89c689;
		border-top: 1px solid #89c689;
	}
	ul#ceon-panels-menu li a:visited, ul#ceon-panels-menu li a:active,
	ul#ceon-panels-menu li a:focus {
		outline: none;
	}
	
	ul#ceon-panels-menu li.CeonPanelTabSelected {
		display: inline;
		padding: 0;
	}
	ul#ceon-panels-menu li.CeonPanelTabSelected a {
		background: #599659 url(<?php echo DIR_WS_IMAGES; ?>ceon-tab-background-selected.png) top left repeat-x;
		padding: 0.7em 2em 0.5em 2em;
		border-left: 1px solid #69a669;
		border-top: 1px solid #69a669;
	}
	ul#ceon-panels-menu li.CeonPanelTabSelected a:hover {
		text-decoration: none;
		background: #599659;
		border-left: 1px solid #69a669;
		border-top: 1px solid #69a669;
	}
	ul#ceon-panels-menu li.CeonPanelTabSelected a:visited,
	ul#ceon-panels-menu li.CeonPanelTabSelected a:active,
	ul#ceon-panels-menu li.CeonPanelTabSelected a:focus {
		outline: none;
	}
	
	#ceon-panels-wrapper {
		border: 1px solid #599659;
		background: #599659;
		padding: 1em 1em 1.2em 1em;
		margin: 0;
	}
	
	.FormError { font-weight: bold; color: #f00; }
	.FormError { background: #fcc; border: 1px solid #f00; margin: 1em 0; padding: 0.4em; }
	p.FormError { margin-bottom: 1em; }
	
	input[type='submit'] { font-size: 1.1em; padding: 0 0.2em; }
	
	select {
		padding: 4px;
		font-size: 1.1em;
	}
	
	.DoubleSpaceBelow {
		margin-bottom: 2em;
	}
	.NoMarginBottom {
		margin-bottom: 0;
	}
	.DisplayNone { display: none; }
	
	#ceon-bisn-wrapper th {
		vertical-align: top;
		background: #eaeaea;
		background: #dadada;
		padding: 0.6em;
		text-align: left;
		padding-right: 1.8em;
		font-size: 1.15em;
	}
	#ceon-bisn-wrapper th a {
		font-size: 1em;
	}
	#ceon-bisn-wrapper th.ClickToSort:hover {
		background: #d0d0d0;
	}
	
	#ceon-bisn-wrapper td {
		vertical-align: top;
		background: #f3f3f3;
		background: none;
		padding: 0.6em;
		padding-right: 1.8em;
	}
	
	#ceon-bisn-wrapper td.BISNPaginationWrapper, #ceon-bisn-wrapper td.BISNPageCount,
			#ceon-bisn-wrapper td.BISNPageLinks {
		background: #fff;
		background: none;
	}
	
	#ceon-bisn-wrapper td.BISNPaginationWrapper {
		padding-left: 0;
		padding-right: 0;
	}
	
	#ceon-bisn-wrapper td.BISNPageCount {
		padding: 0.4em 0;
	}
	#ceon-bisn-wrapper td.BISNPageLinks {
		text-align: right;
		padding: 0.4em 0;
	}
	
	#ceon-bisn-wrapper td.dataTableContent {
		background: #eaeaea;
		font-size: 1.1em;
	}
	#ceon-bisn-wrapper td.dataTableContent a {
		font-size: 1.1em;
	}
	#ceon-bisn-wrapper td.dataTableContent.Even {
		background: #f6f6f6;
		background: #e3e3e3;
	}
	
	table:last-child td, table tbody:last-child td { padding-bottom: 0; }
	
	dl#back-in-stock-notifications-output dt { margin-top: 0.95em; }
	dl#back-in-stock-notifications-output dd { 
		font-weight: bold;
		margin-left: 2.5em;
		margin-top: 0.3em;
	}
	
	#footer {
		margin-top: 1.5em;
		border-top: 1px solid #000;
		padding-top: 1em;
		text-align: right;
		font-size: 0.9em;
		padding-bottom: 2em;
	}
	#footer img {
		border: none;
	}
	#ceon-button-logo {
		float: left;
		margin-right: 14px;
	}
	#footer p {
		margin: 0 0 0.4em 0;
	}
	#footer p#version-info {
		padding: 0;
		line-height: 1.3;
		margin-top: 0.7em;
	}
	#footer #check-for-updates a {
		font-size: 0.9em;
	}
	</style>
	<!--[if IE]>
	<style type="text/css">
	fieldset {
		position: relative;
	}
	legend, fieldset.CeonPanel legend, fieldset.CeonPanel fieldset legend {
		position: absolute;
		top: -0.55em;
		left: .2em;
		padding: 0;
	}
	</style>
	<![endif]-->
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<div id="ceon-bisn-wrapper">
	<h1 id="ceon-bisn-page-heading"><?php echo BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE; ?></h1>
	
	<div class="SpacerSmall"></div>
	
	<ul id="ceon-panels-menu">
		<li id="transaction-action-tab" class="CeonPanelTabSelected"><a href=""><?php
		echo BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE; ?></a></li>
	</ul>
	<div id="ceon-panels-wrapper">
		<fieldset id="main-panel" class="CeonPanel">
		<legend class="DisplayNone"><?php echo BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE; ?></legend>
		<!-- body_text //-->
		<div id="actionSelector">
			<?php echo zen_draw_form('back_in_stock_notifications',
					FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS, '', 'GET') .
					zen_hide_session_id(); ?>
				<fieldset class="DoubleSpaceBelow">
					<legend><?php echo TEXT_ACTION_TO_PERFORM; ?></legend>
					<input type="hidden" name="action" value="send" />
					<?php echo zen_draw_pull_down_menu('option', $bisn_options, $_GET['option']); ?>
					<input type="submit" value="Go!" />
				</fieldset>
			</form>
		</div>
<?php
if (isset($_GET['option']) && ($_GET['option'] == 1 || $_GET['option'] == 2)) {
	// Build the listings page count and page links code
	
	if (isset($products_split)) {
		$split_object = $products_split;
		
		$count_text = TEXT_DISPLAY_NUMBER_OF_PRODUCTS;
	} else {
		$split_object = $subscriptions_split;
		
		$count_text = TEXT_DISPLAY_NUMBER_OF_BACK_IN_STOCK_NOTIFICATIONS;
	}
	
	if ($_GET['page'] != -1) {
		// Page is to be split according to the maximum rows per page
		$pagination_columns .= '<table border="0" width="100%" cellspacing="0" cellpadding="0">' .
			'<tr><td class="BISNPageCount">' .
			$split_object->display_count($num_rows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS,
			$_GET['page'], $count_text) . '</td>' . "\n";
		
		$pagination_columns .= '<td class="BISNPageLinks">' .
			$split_object->display_links($num_rows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS,
			MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'action')));
		
		$pagination_columns .=  ' [<a href="' . zen_href_link(
			FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
		 	zen_get_all_get_params(array('page', 'action')) . 'page=-1') . '">' . TEXT_SHOW_ALL .
			'</a>]' . '</td>' . "\n";
			
		$pagination_columns .= '</tr></table>' . "\n";
	} else {
		// All results are to be shown regardless of any maximum rows per page setting
		$pagination_columns .= '<table border="0" width="100%" cellspacing="0" cellpadding="0">' .
			'<tr><td class="BISNPageCount">' .
			sprintf($count_text, 1, $num_rows, $num_rows) . '</td>' . "\n";
		
		$pagination_columns .= '<td class="BISNPageLinks">' .
			'<a href="' . zen_href_link(FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('page', 'action')) . 'page=1') . '">' . 
			TEXT_DISPLAY_BY_PAGE . '</a>' . '</td>' . "\n";
			
		$pagination_columns .= '</tr></table>' . "\n";
	}
}

if (isset($_GET['option']) && $_GET['option'] == 1) {
?>
		<fieldset class="NoMarginBottom">
			<legend><?php echo TEXT_PRODUCTS_WITH_SUBSCRIPTIONS; ?></legend>
			<table border="0" cellspacing="1" cellpadding="2" align="center">
				<tr>
					<td colspan="4" class="BISNPaginationWrapper"><?php echo $pagination_columns; ?></td>
				</tr>
				<tr>
<?php	
	if ($sort_column == 'category') {
		echo '<th>' . TABLE_HEADING_PRODUCT_CATEGORY . '</th>';
	} else {
		echo '<th class="ClickToSort"><a href="' .
			zen_href_link(FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=category') .
			'" title="' . TEXT_SORT_BY_PRODUCT_CATEGORY . '">' .
			TABLE_HEADING_PRODUCT_CATEGORY . '</a></th>';
	}
	
	if ($sort_column == 'product') {
		echo '<th>' . TABLE_HEADING_PRODUCT_NAME . '</th>';
	} else {
		echo '<th class="ClickToSort"><a href="' .
			zen_href_link(FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=product') .
			'" title="' . TEXT_SORT_BY_PRODUCT_NAME . '">' .
			TABLE_HEADING_PRODUCT_NAME . '</a></th>';
	}
	
	if ($sort_column == 'subscribers') {
		echo '<th>' . TABLE_HEADING_NUM_SUBSCRIBERS . '</th>';
	} else {
		echo '<th class="ClickToSort"><a href="' .
			zen_href_link(FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=subscribers') .
			'" title="' . TEXT_SORT_BY_NUM_SUBSCRIBERS . '">' .
			TABLE_HEADING_NUM_SUBSCRIBERS . '</a></th>';
	}
	
	if ($sort_column == 'stock') {
		echo '<th>' . TABLE_HEADING_CURRENT_STOCK . '</th>';
	}  else {
		echo '<th class="ClickToSort"><a href="' .
			zen_href_link(FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=stock') .
			'" title="' . TEXT_SORT_BY_CURRENT_STOCK . '">' .
			TABLE_HEADING_CURRENT_STOCK . '</a></th>';
	}
	
	echo "\n";
?>
				</tr>
<?php
	$even = false;
	
	while (!$product_subscriptions_info->EOF) {
?>
				<tr class="dataTableRow">
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
						<?php echo $product_subscriptions_info->fields['categories_name'];?>
					</td>
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
						<?php echo buildLinkToProductAdminPage(
							$product_subscriptions_info->fields['products_name'],
							$product_subscriptions_info->fields['product_id'], 
							$product_subscriptions_info->fields['products_type']);?>
					</td>
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
						<?php echo $product_subscriptions_info->fields['num_subscribers'];?>
					</td>
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
						<?php echo $product_subscriptions_info->fields['current_stock'];?>
					</td>
				</tr>
<?php
		$even = !$even;
		
		$product_subscriptions_info->MoveNext();
	}
?>
				<tr>
					<td colspan="4" class="BISNPaginationWrapper"><?php echo $pagination_columns; ?></td>
				</tr>
			</table>
		</fieldset>
<?php
} elseif(isset($_GET['option']) && $_GET['option'] == 2) {
?>
		<fieldset class="NoMarginBottom">
			<legend><?php echo TEXT_ALL_SUBSCRIPTIONS; ?></legend>
			<table border="0" cellspacing="1" cellpadding="2" align="center">
				<tr>
					<td colspan="5" class="BISNPaginationWrapper"><?php echo $pagination_columns; ?></td>
				</tr>
				<tr>
<?php	
	if ($sort_column == 'category') {
		echo '<th>' . TABLE_HEADING_PRODUCT_CATEGORY . '</th>';
	} else {
		echo '<th class="ClickToSort"><a href="' .
			zen_href_link(FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=category') .
			'" title="' . TEXT_SORT_BY_PRODUCT_CATEGORY . '">' .
			TABLE_HEADING_PRODUCT_CATEGORY . '</a></th>';
	}
	
	if ($sort_column == 'product') {
		echo '<th>' . TABLE_HEADING_PRODUCT_NAME . '</th>';
	} else {
		echo '<th class="ClickToSort"><a href="' .
			zen_href_link(FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=product') .
			'" title="' . TEXT_SORT_BY_PRODUCT_NAME . '">' .
			TABLE_HEADING_PRODUCT_NAME . '</a></th>';
	}
	
	if ($sort_column == 'date') {
		echo '<th>' . TABLE_HEADING_DATE_SUBSCRIBED . '</th>';
	} else {
		echo '<th class="ClickToSort"><a href="' .
			zen_href_link(FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=date') .
			'" title="' . TEXT_SORT_BY_DATE_SUBSCRIBED . '">' .
			TABLE_HEADING_DATE_SUBSCRIBED . '</a></th>';
	}
	
	if ($sort_column == 'customer_name') {
		echo '<th>' . TABLE_HEADING_CUSTOMER_NAME . '</th>';
	}  else {
		echo '<th class="ClickToSort"><a href="' .
			zen_href_link(FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=customer_name') .
			'" title="' . TEXT_SORT_BY_CUSTOMER_NAME . '">' .
			TABLE_HEADING_CUSTOMER_NAME . '</a></th>';
	}
	
	if ($sort_column == 'customer_email') {
		echo '<th>' . TABLE_HEADING_CUSTOMER_NAME . '</th>';
	}  else {
		echo '<th class="ClickToSort"><a href="' .
			zen_href_link(FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=customer_email') .
			'" title="' . TEXT_SORT_BY_CUSTOMER_EMAIL . '">' .
			TABLE_HEADING_CUSTOMER_EMAIL . '</a></th>';
	}
	
	echo "\n";
?>
				</tr>
<?php
	$even = false;
	
	while (!$subscriptions_info->EOF) {
?>
				<tr class="dataTableRow">
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
						<?php echo $subscriptions_info->fields['categories_name'];?>
					</td>
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
						<?php echo buildLinkToProductAdminPage(
							$subscriptions_info->fields['products_name'],
							$subscriptions_info->fields['product_id'],
							$subscriptions_info->fields['products_type']);?>
					</td>
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
						<?php echo
							zen_date_long($subscriptions_info->fields['date_subscribed']);?>
					</td>
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
						<?php echo $subscriptions_info->fields['name'];?>
					</td>
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
						<?php
						$customer_email_address =
							(!is_null($subscriptions_info->fields['email_address']) ?
							$subscriptions_info->fields['email_address'] :
							$subscriptions_info->fields['customers_email_address']);
						
						echo $customer_email_address;
						?>
					</td>
				</tr>
<?php
		$even = !$even;
		
		$subscriptions_info->MoveNext();
	}
?>
				<tr>
					<td colspan="5" class="BISNPaginationWrapper"><?php echo $pagination_columns; ?></td>
				</tr>
			</table>
		</fieldset>
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
		</fieldset>
	</div>
	<div id="footer"><p><a href="http://dev.ceon.net/web" target="_blank"><img src="<?php echo DIR_WS_IMAGES; ?>ceon-button-logo.png" alt="Ceon" id="ceon-button-logo" /></a>Module &copy; Copyright 2004-<?php echo (date('Y') > 2012 ? date('Y') : 2012); ?> <a href="http://dev.ceon.net/web" target="_blank">Ceon</a></p>
		<p id="version-info">Module Version: <?php echo CEON_BACK_IN_STOCK_NOTIFICATIONS_VERSION; ?></p>
		<p id="check-for-updates"><a href="http://dev.ceon.net/web/zen-cart/back-in-stock-notifications/version-checker/<?php echo CEON_BACK_IN_STOCK_NOTIFICATIONS_VERSION; ?>" target="_blank">Check for Updates</a></p>
	</div>
</div>
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