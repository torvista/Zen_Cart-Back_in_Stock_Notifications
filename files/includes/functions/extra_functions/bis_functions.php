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
//REMOVE THIS FUNCTION FOR ZEN CART 158 ONWARDS: this function is already included in shopfront functions
/**
 * lookup attributes model
 * @param int $product_id
 */
function zen_get_products_model($product_id)
{
    global $db;
    $check = $db->Execute("SELECT products_model
                    FROM " . TABLE_PRODUCTS . "
                    WHERE products_id=" . (int)$product_id, 1);
    if ($check->EOF) return '';
    return $check->fields['products_model'];
}
