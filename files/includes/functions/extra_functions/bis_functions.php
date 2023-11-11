<?php

declare(strict_types=1);

/**
 * Ceon Back In Stock Notifications
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @copyright   Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        https://www.ceon.net
 * @license     https://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: bis_functions.php 2023-11-11 torvista
 */

/**
 * @return mixed|string
 */
//TODO BISN: replace with ZC function
function get_customers_email()
{
     if (!zen_is_logged_in()) {
         return '';
     }
     if (isset($_SESSION['customers_email_address'])) {
         return $_SESSION['customers_email_address'];
     }
     global $db;

     $sql = "SELECT customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_id = :id: LIMIT 1";
    $sql = $db->bindVars($sql, ':id:', $_SESSION['customer_id'], 'integer');
    $result = $db->Execute($sql);
    if (!$result->EOF) {
       // Set it for older versions that don't save this
       $_SESSION['customers_email_address'] = $result->fields['customers_email_address'];
       return $_SESSION['customers_email_address'];
    }
      return '';
  }
