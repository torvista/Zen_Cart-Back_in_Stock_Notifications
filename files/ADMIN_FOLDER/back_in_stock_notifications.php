<?php

declare(strict_types=1);
/** phpstorm inspections
 * @var currencies $currencies
 * @var queryFactory $db
 * @var messageStack $messageStack
 * @var notifier $zco_notifier
 * @var template_func $template
 * @var $current_page_base
 */
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
 * @link        https://www.ceon.net
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: back_in_stock_notifications.php 2023-11-11 torvista
 */

const CEON_BACK_IN_STOCK_NOTIFICATIONS_VERSION = '3.2.3alpha';

require('includes/application_top.php');

// Check the database subscriptions table exist. If not, run the installer
$table_exists_query = 'SHOW TABLES LIKE "' . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . '"';
$table_exists_result = $db->Execute($table_exists_query);

// Check all the admin options are created
$option_missing =
    !defined('BACK_IN_STOCK_NOTIFICATIONS_ENABLED') ||
    !defined('BACK_IN_STOCK_REQUIRES_LOGIN') ||
    !defined('SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAILS_TO');

//can use parameter on page url &check-config=1 to force check
if ($table_exists_result->EOF || $option_missing || isset($_GET['check-config'])) {
    // Instantiate and run the installation/upgrade class
    require_once(DIR_WS_CLASSES . 'class.CeonBISNInstallOrUpgrade.php');
    $install_or_upgrade = new CeonBISNInstallOrUpgrade();

    if (count($install_or_upgrade->error_messages) > 0) {
        foreach ($install_or_upgrade->error_messages as $error_message) {
            print '<p style="background: #fcc; border: 1px solid #f00; margin: 1em;' .
                ' padding: 0.4em;">Error: ' . $error_message . "</p>\n";
        }
        require(DIR_WS_INCLUDES . 'application_bottom.php');
        exit;
    }
}

require_once(DIR_WS_FUNCTIONS . 'plugin_bisn_functions.php');

$bisn_options = [
    0 => [
        'id' => 1,
        'text' => '1: ' . TEXT_LIST_ALL_SUBSCRIBED_PRODUCTS
    ],
    1 => [
        'id' => 2,
        'text' => '2: ' . TEXT_LIST_ALL_SUBSCRIPTIONS
    ],
    2 => [
        'id' => 3,
        'text' => '3: ' . TEXT_PREVIEW_NOTIFICATION_EMAILS
    ],
    3 => [
        'id' => 4,
        'text' => '4: ' . TEXT_SEND_NOTIFICATION_EMAILS
    ],
    4 => [
        'id' => 5,
        'text' => '5: ' . TEXT_REMOVE_DELETED_PRODUCTS
    ]
];

if (empty($_GET['option']) || !is_numeric($_GET['option']) || (int)$_GET['option'] < 1 || (int)$_GET['option'] > 5) {
    $_GET['option'] = 1;
}

switch($_GET['option']){
	case 1:
        $products_query_raw = "
			SELECT
				bisns.product_id, pd.products_name, p.products_model, COUNT(*) AS num_subscribers, p.products_type,
				p.products_quantity AS current_stock, cd.categories_name, cd.categories_id
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
				bisns.product_id, pd.products_name, p.products_model, p.products_type, p.products_quantity, cd.categories_name, cd.categories_id";

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

        $products_query_raw = str_replace(["\n", "\r", "\t"], ' ', $products_query_raw);

        if (isset($_GET['page']) && (int)$_GET['page'] !== -1) { //todo check strict
            $products_split = new splitPageResults(
                $_GET['page'],
                MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $products_query_raw, $num_rows
            );
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

        $sort_column = $_GET['sort'] ?? 'product';

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

        $subscriptions_query_raw = str_replace(["\n", "\r", "\t"], ' ', $subscriptions_query_raw);

        if (isset($_GET['page']) && (int)$_GET['page'] !== -1) {//todo check strict
            $subscriptions_split = new splitPageResults(
                $_GET['page'],
                MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $subscriptions_query_raw, $num_rows
            );
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
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
<head>
    <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
</head>
<body>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<div id="ceon-bisn-wrapper">
    <h1 id="ceon-bisn-page-heading"><?php echo BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE; ?></h1>

    <div class="SpacerSmall"></div>

    <ul id="ceon-panels-menu">
        <li id="transaction-action-tab" class="CeonPanelTabSelected">
            <a href="#"><?php echo BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE; ?></a>
        </li>
    </ul>
    <div id="ceon-panels-wrapper">
        <fieldset id="main-panel" class="CeonPanel">
            <legend class="DisplayNone"><?php echo BACK_IN_STOCK_NOTIFICATIONS_HEADING_TITLE; ?></legend>
            <!-- body_text //-->
            <div id="actionSelector">
                <?php
                echo zen_draw_form('back_in_stock_notifications', FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS, '', 'GET') .
                    zen_hide_session_id(); ?>
                <fieldset class="DoubleSpaceBelow">
                    <legend><?php echo TEXT_ACTION_TO_PERFORM; ?></legend>
                    <input type="hidden" name="action" value="send"/>
					<?php echo zen_draw_pull_down_menu('option', $bisn_options, $_GET['option']); ?>
					<input type="submit" value="Go!" />
                </fieldset>
                <?php echo '</form>'; ?>
            </div>
            <?php
if (isset($_GET['option']) && ($_GET['option'] == 1 || $_GET['option'] == 2)) {
                // Build the listings page count and page links code

                if (isset($products_split)) {
                    $split_object = $products_split;
                    $count_text = TEXT_DISPLAY_NUMBER_OF_PRODUCTS;
                } else {
                    if (isset($subscriptions_split)) {
                        $split_object = $subscriptions_split;
                    }
                    $count_text = TEXT_DISPLAY_NUMBER_OF_BACK_IN_STOCK_NOTIFICATIONS;
                }

                if (isset($_GET['page']) && (int)$_GET['page'] !== -1) {//todo check strict
                    // Page is to be split according to the maximum rows per page
                    $pagination_columns = '<table style="width:100%">' .
                        '<tr><td class="BISNPageCount">' .
                        $split_object->display_count(
                            $num_rows,
                            MAX_DISPLAY_SEARCH_RESULTS_REPORTS,
                            $_GET['page'],
                            $count_text
                        ) . '</td>' . "\n";

                    $pagination_columns .= '<td class="BISNPageLinks">' .
                        $split_object->display_links(
                            $num_rows,
                            MAX_DISPLAY_SEARCH_RESULTS_REPORTS,
                            MAX_DISPLAY_PAGE_LINKS,
                            $_GET['page'],
                            zen_get_all_get_params(['page', 'action'])
                        );

                    $pagination_columns .= ' [<a href="' . zen_href_link(
                            FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
                            zen_get_all_get_params(['page', 'action']) . 'page=-1'
                        ) . '">' . TEXT_SHOW_ALL .
                        '</a>]' . '</td>' . "\n";

                    $pagination_columns .= '</tr></table>' . "\n";
                } else {//page=-1: show all
                    // All results are to be shown regardless of any maximum rows per page setting
                    $pagination_columns = '<table style="width:100%">' . 
                        '<tr><td class="BISNPageCount">' .
                        sprintf($count_text, 1, $num_rows, $num_rows) . '</td>' . "\n";

                    $pagination_columns .= '<td class="BISNPageLinks">' .
                        '<a href="' . zen_href_link(
                            FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
                            zen_get_all_get_params(['page', 'action']) . 'page=1'
                        ) . '">' .
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

	echo '<th>' . TABLE_HEADING_PRODUCT_ID . '</th>';
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
                            } else {
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
<?php echo $product_subscriptions_info->fields['product_id']; ?>
					</td>
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
                                    <?php echo buildLinkToProductAdminPage(
                                        $product_subscriptions_info->fields['products_name'],
                                        $product_subscriptions_info->fields['product_id'],
                                        $product_subscriptions_info->fields['products_type']); ?>
                                </td>
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
                                    <?php echo $product_subscriptions_info->fields['num_subscribers']; ?>
                                </td>
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
                                    <?php echo $product_subscriptions_info->fields['current_stock']; ?>
                                </td>
                            </tr>
                            <?php
                            $product_subscriptions_info->MoveNext();
                        }
                        ?>
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

   echo '<th>' . TABLE_HEADING_PRODUCT_ID . '</th>';
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
                            } else {
                                echo '<th class="ClickToSort"><a href="' .
                                    zen_href_link(FILENAME_CEON_BACK_IN_STOCK_NOTIFICATIONS,
			zen_get_all_get_params(array('sort', 'action')) . 'sort=customer_name') .
                                    '" title="' . TEXT_SORT_BY_CUSTOMER_NAME . '">' .
                                    TABLE_HEADING_CUSTOMER_NAME . '</a></th>';
                            }

	if ($sort_column == 'customer_email') {
		echo '<th>' . TABLE_HEADING_CUSTOMER_NAME . '</th>';
                            } else {
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
<?php echo $subscriptions_info->fields['product_id']; ?>
                                </td>
					<td class="dataTableContent<?php if ($even) echo ' Even';?>">
                                    <?php echo buildLinkToProductAdminPage(
                                        $subscriptions_info->fields['products_name'],
                                        $subscriptions_info->fields['product_id'],
                                        $subscriptions_info->fields['products_type']); ?>
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
    <div id="ceon-footer"><p><a href="https://ceon.net" target="_blank"><img src="<?php
                echo DIR_WS_IMAGES; ?>ceon-button-logo.png" alt="Ceon" id="ceon-button-logo"/></a>Module &copy; Copyright 2004-2012
            <?php
            //echo (date('Y') > 2012 ? date('Y') : 2012); ?> <!--<a href="https://dev.ceon.net/web" target="_blank">Ceon</a></p>-->
        <p id="version-info">Module Version: <?php
            echo CEON_BACK_IN_STOCK_NOTIFICATIONS_VERSION; ?></p>
        <?php
        /*?><p id="check-for-updates"><a href="https://dev.ceon.net/web/zen-cart/back-in-stock-notifications/version-checker/<?php echo CEON_BACK_IN_STOCK_NOTIFICATIONS_VERSION; ?>" target="_blank">Check for Updates</a></p><?php */ ?>
    </div>
</div>
<!-- body_eof //-->

<!-- footer //-->
<div class="footer-area">
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
