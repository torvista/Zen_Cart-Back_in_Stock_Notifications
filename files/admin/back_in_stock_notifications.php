<?php

/**
 * Back In Stock Notifications Admin Utility.
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @author      Tony Niemann <tony@loosechicken.com>
 * @copyright   Copyright 2004-2011 Ceon
 * @copyright   Portions Copyright 2008 RubikIntegration team @ RubikIntegration.com
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications.php 715 2011-06-12 20:06:27Z conor $
 */

/**
 * Version info - don't touch!
 */
define('CEON_BACK_IN_STOCK_NOTIFICATIONS_VERSION', '3.0.0');

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
		
		$products_query_raw = strtolower(str_replace("\n", ' ', $products_query_raw));
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
		
		$subscriptions_query_raw = strtolower(str_replace("\n", ' ', $subscriptions_query_raw));
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
		<!--
		td.dataTableHeadingContent { padding: 0.3em 0.6em 0.3em 0.4em; }
		td.dataTableContent { padding: 0.15em 0.6em 0.15em 0.4em; }
		
		td.BISNPageCount {
			padding: 0.4em 0;
		}
		td.BISNPageLinks {
			text-align: right;
			padding: 0.4em 0;
		}
		
		dl#back-in-stock-notifications-output dt { margin-top: 0.95em; }
		dl#back-in-stock-notifications-output dd { 
			font-weight: bold;
			margin-left: 2.5em;
			margin-top: 0.3em;
		}
		
		#footer {
			padding-top: 1em;
			text-align: right;
			font-size: 0.9em;
			padding-bottom: 2em;
			margin-left: 0.5em;
			margin-right: 0.5em;
			margin-top: 3em;
			border-top: 1px solid #000;
		}
		#footer img {
			border: none;
		}
		#ceon-button-logo {
			float: left;
			margin-right: 14px;
		}
		#version-info {
			padding: 0;
			margin-top: 1em;
			margin-bottom: 2em;
			line-height: 1.3
		}
		#version-info a {
			font-size: 0.9em;
		}
		-->
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
				<?php echo zen_draw_form('back_in_stock_notifications',
						FILENAME_BACK_IN_STOCK_NOTIFICATIONS, '', 'GET') . zen_hide_session_id(); ?>
					<fieldset style="margin-bottom: 2em;">
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
		
		$pagination_columns .=  ' [<a href="' . zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATIONS,
		 	zen_get_all_get_params(array('page', 'action')) . 'page=-1') . '">' . TEXT_SHOW_ALL .
			'</a>]' . '</td>' . "\n";
			
		$pagination_columns .= '</tr></table>' . "\n";
	} else {
		// All results are to be shown regardless of any maximum rows per page setting
		$pagination_columns .= '<table border="0" width="100%" cellspacing="0" cellpadding="0">' .
			'<tr><td class="BISNPageCount">' .
			sprintf($count_text, 1, $num_rows, $num_rows) . '</td>' . "\n";
		
		$pagination_columns .= '<td class="BISNPageLinks">' .
			'<a href="' . zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('page', 'action')) . 'page=1') . '">' . 
			TEXT_DISPLAY_BY_PAGE . '</a>' . '</td>' . "\n";
			
		$pagination_columns .= '</tr></table>' . "\n";
	}
}

if (isset($_GET['option']) && $_GET['option'] == 1) {
?>
			<table border="0" cellspacing="1" cellpadding="2" align="center">
				<tr>
					<td colspan="4"><?php echo $pagination_columns; ?></td>
				</tr>
				<tr class="dataTableHeadingRow">
<?php	
	if ($sort_column == 'category') {
		echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_PRODUCT_CATEGORY . '</td>';
	} else {
		echo '<td class="dataTableHeadingContent"><a href="' .
			zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=category') .
			'" title="' . TEXT_SORT_BY_PRODUCT_CATEGORY . '">' .
			TABLE_HEADING_PRODUCT_CATEGORY . '</a></td>';
	}
	
	if ($sort_column == 'product') {
		echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_PRODUCT_NAME . '</td>';
	} else {
		echo '<td class="dataTableHeadingContent"><a href="' .
			zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=product') .
			'" title="' . TEXT_SORT_BY_PRODUCT_NAME . '">' .
			TABLE_HEADING_PRODUCT_NAME . '</a></td>';
	}
	
	if ($sort_column == 'subscribers') {
		echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_NUM_SUBSCRIBERS . '</td>';
	} else {
		echo '<td class="dataTableHeadingContent"><a href="' .
			zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=subscribers') .
			'" title="' . TEXT_SORT_BY_NUM_SUBSCRIBERS . '">' .
			TABLE_HEADING_NUM_SUBSCRIBERS . '</a></td>';
	}
	
	if ($sort_column == 'stock') {
		echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_CURRENT_STOCK . '</td>';
	}  else {
		echo '<td class="dataTableHeadingContent"><a href="' .
			zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=stock') .
			'" title="' . TEXT_SORT_BY_CURRENT_STOCK . '">' .
			TABLE_HEADING_CURRENT_STOCK . '</a></td>';
	}
	
	echo "\n";
?>
				</tr>
<?php
	while (!$product_subscriptions_info->EOF) {
?>
				<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
					<td class="dataTableContent">
						<?php echo $product_subscriptions_info->fields['categories_name'];?>
					</td>
					<td class="dataTableContent">
						<?php echo buildLinkToProductAdminPage(
							$product_subscriptions_info->fields['products_name'],
							$product_subscriptions_info->fields['product_id'], 
							$product_subscriptions_info->fields['products_type']);?>
					</td>
					<td class="dataTableContent">
						<?php echo $product_subscriptions_info->fields['num_subscribers'];?>
					</td>
					<td class="dataTableContent">
						<?php echo $product_subscriptions_info->fields['current_stock'];?>
					</td>
				</tr>
<?php
		$product_subscriptions_info->MoveNext();
	}
?>
				<tr>
					<td colspan="4"><?php echo $pagination_columns; ?></td>
				</tr>
			</table>
<?php
} elseif(isset($_GET['option']) && $_GET['option'] == 2) {
?>
			<table border="0" cellspacing="1" cellpadding="2" align="center">
				<tr>
					<td colspan="5"><?php echo $pagination_columns; ?></td>
				</tr>
				<tr class="dataTableHeadingRow">
<?php	
	if ($sort_column == 'category') {
		echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_PRODUCT_CATEGORY . '</td>';
	} else {
		echo '<td class="dataTableHeadingContent"><a href="' .
			zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=category') .
			'" title="' . TEXT_SORT_BY_PRODUCT_CATEGORY . '">' .
			TABLE_HEADING_PRODUCT_CATEGORY . '</a></td>';
	}
	
	if ($sort_column == 'product') {
		echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_PRODUCT_NAME . '</td>';
	} else {
		echo '<td class="dataTableHeadingContent"><a href="' .
			zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=product') .
			'" title="' . TEXT_SORT_BY_PRODUCT_NAME . '">' .
			TABLE_HEADING_PRODUCT_NAME . '</a></td>';
	}
	
	if ($sort_column == 'date') {
		echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_DATE_SUBSCRIBED . '</td>';
	} else {
		echo '<td class="dataTableHeadingContent"><a href="' .
			zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=date') .
			'" title="' . TEXT_SORT_BY_DATE_SUBSCRIBED . '">' .
			TABLE_HEADING_DATE_SUBSCRIBED . '</a></td>';
	}
	
	if ($sort_column == 'customer_name') {
		echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_CUSTOMER_NAME . '</td>';
	}  else {
		echo '<td class="dataTableHeadingContent"><a href="' .
			zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=customer_name') .
			'" title="' . TEXT_SORT_BY_CUSTOMER_NAME . '">' .
			TABLE_HEADING_CUSTOMER_NAME . '</a></td>';
	}
	
	if ($sort_column == 'customer_email') {
		echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_CUSTOMER_NAME . '</td>';
	}  else {
		echo '<td class="dataTableHeadingContent"><a href="' .
			zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=customer_email') .
			'" title="' . TEXT_SORT_BY_CUSTOMER_EMAIL . '">' .
			TABLE_HEADING_CUSTOMER_EMAIL . '</a></td>';
	}
	
	echo "\n";
?>
				</tr>
<?php
	while (!$subscriptions_info->EOF) {
?>
				<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
					<td class="dataTableContent">
						<?php echo $subscriptions_info->fields['categories_name'];?>
					</td>
					<td class="dataTableContent">
						<?php echo buildLinkToProductAdminPage(
							$subscriptions_info->fields['products_name'],
							$subscriptions_info->fields['product_id'],
							$subscriptions_info->fields['products_type']);?>
					</td>
					<td class="dataTableContent">
						<?php echo
							zen_date_long($subscriptions_info->fields['date_subscribed']);?>
					</td>
					<td class="dataTableContent">
						<?php echo $subscriptions_info->fields['name'];?>
					</td>
					<td class="dataTableContent">
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
		$subscriptions_info->MoveNext();
	}
?>
				<tr>
					<td colspan="5"><?php echo $pagination_columns; ?></td>
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
<div id="footer"><a href="http://dev.ceon.net/web" target="_blank"><img src="<?php echo DIR_WS_IMAGES; ?>ceon-button-logo.png" alt="Ceon" id="ceon-button-logo" /></a>Module &copy; Copyright 2004-2011 <a href="http://dev.ceon.net/web" target="_blank">Ceon</a>
	<p id="version-info">Module Version: <?php echo CEON_BACK_IN_STOCK_NOTIFICATIONS_VERSION; ?><br />
	<a href="http://dev.ceon.net/web/zen-cart/back-in-stock-notifications/version-checker/<?php echo CEON_BACK_IN_STOCK_NOTIFICATIONS_VERSION; ?>" target="_blank">Check for Updates</a></p>
</div>
<div class="footer-area">
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>