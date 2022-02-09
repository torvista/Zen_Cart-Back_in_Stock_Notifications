<?php
// BEGIN CEON BACK IN STOCK NOTIFICATIONS 1 of 1
        if (BACK_IN_STOCK_NOTIFICATION_ENABLED == 1) {
          // Add a link, to the Back In Stock Notification form on the product info page for this
          // product
          $product_back_in_stock_notification_form_link = '';
          
          if (isset($_SESSION['customer_id']) && $_SESSION['customer_id']) {
            // Check if this user has already requested to be notified when this product is back
            // in stock
            $customer_details_query = "
              SELECT
                customers_firstname, customers_lastname, customers_email_address
              FROM
                " . TABLE_CUSTOMERS . "
              WHERE
                customers_id = '" . (int) $_SESSION['customer_id'] . "'";
            
            $customer_details = $db->Execute($customer_details_query);
            
            $already_to_be_notified_query = "
              SELECT
                id
              FROM
                " . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . "
              WHERE
                product_id = '" . $product_id . "'
              AND
              (
                customer_id = '"  . (int) $_SESSION['customer_id'] . "'
              OR
                email_address = '" . $customer_details->fields['customers_email_address'] . "'
              );";
            
            $already_to_be_notified = $db->Execute($already_to_be_notified_query);
            
            if ($already_to_be_notified->RecordCount() > 0) {
              // Customer is already subscribed to the notification list for this product
              $product_back_in_stock_notification_form_link =
                BACK_IN_STOCK_NOTIFICATION_TEXT_PRODUCT_LISTING_ALREADY_SUBSCRIBED;
            }
          }
          if ($product_back_in_stock_notification_form_link == '') {
            // Build link to form
            if (BACK_IN_STOCK_REQUIRES_LOGIN != '1') { 
               $params = zen_get_all_get_params(array('sort', 'filter_id', 'alpha_filter'));
               
               if (strlen($params) > 0) {
                 if (substr($params, -1) == '&') {
                   $params = substr($params, 0, strlen($params) - 1);
                 }
               }
               
               $params .= '&products_id=' . $product_id;
               
               $product_back_in_stock_notification_form_link = sprintf(
                 BACK_IN_STOCK_NOTIFICATION_TEXT_PRODUCT_LISTING_FORM_LINK,
                 zen_href_link(zen_get_info_page($product_id), $params, 'NONSSL') .
                 '#back_in_stock_notification_form');
          } else {
            $product_back_in_stock_notification_form_link = 
              zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATION_SUBSCRIBE,
              'products_id='.(int) $_GET['products_id'], 'NONSSL'); 
          }
          $return_button .= $product_back_in_stock_notification_form_link;
        }
   }
// END CEON BACK IN STOCK NOTIFICATIONS 1 of 1
