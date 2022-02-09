<?php 
  function get_customers_email() {
     if (!zen_is_logged_in()) return ''; 
     if (isset($_SESSION['customers_email_address'])) return $_SESSION['customers_email_address']; 
     global $db;

     $sql = "SELECT customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_id = :id: LIMIT 1";
    $sql = $db->bindVars($sql, ':id:', $_SESSION['customer_id'], 'integer');
    $result = $db->execute($sql);
    if (!$result->EOF) {
       // Set it for older versions that don't save this
       $_SESSION['customers_email_address'] = $result->fields['customers_email_address']; 
       return $_SESSION['customers_email_address']; 
    } else {
       return ''; 
    }
  }
