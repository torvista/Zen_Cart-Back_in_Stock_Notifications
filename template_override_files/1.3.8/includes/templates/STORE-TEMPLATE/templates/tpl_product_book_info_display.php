<?php //uses U.S price function. if U.K wanted, line 284 use 'zen_get_products_display_price_uk' instead jph mod
//Multilanguage code corrections / display improvements jph May 2007
//++ manufacturers multilanguage url mod june 2007
//mar 2006 book url activated as misc 5  jph
//jph extra fields version of moku book type sept 2006
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=product_book_info.<br />
 * Displays details of a book product
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * was @version $Id: tpl_product_book_info_display.php 931 2012-02-05 20:13:15Z conor $
 */
?>
<?php //================== create array jph
//The array sets product field display order, change the entries order to suit requirements
//if you want the admin field to be hidden in prodyct display, but still use it in admin,
//comment out a field below, but in zencart product type layout set it ON.
//If you don't want the field at all, set off in zc admin, it will not appear in cart or admin collect info

//note authors is not in this list
		$sort_arr = array(
		//**** = special, so unwanted, concatenated into display line A, ; type; format; size; pages
        //~~~~~~~~~~~~~~~~ unwanted, concatenated into display line A,
		'sort_book_info_condition',		
		'sort_info_model', //isbn
        'sort_info_weight',
       'sort_info_quantity',
        'sort_info_manufacturer', //publisher
        //~~~~~~~~~~~~~~~~'sort_book_info_genre', //subjects multiple
        //****'sort_book_info_type',
        //****'sort_book_info_color',
        'sort_book_info_language',
        //****'sort_book_info_pages',
        //****'sort_book_info_size',
        'sort_book_info_pub_date',
        'sort_book_info_misc_1',
        //'sort_book_info_misc_2',   added to condition line
        'sort_book_info_misc_bool_1',
        'sort_book_info_misc_bool_2',
        'sort_book_info_misc_3',
        //'sort_book_info_misc_4',	//private notes, not in cart display
        'sort_book_info_misc_5',   
        'sort_book_info_misc_6',
		'sort_book_info_misc_7',
        'sort_book_info_misc_8',
        'sort_book_info_misc_9',
        'sort_book_info_misc_10',
	    
		'sort_book_info_misc_dd1',
		'sort_book_info_misc_dd2',
		'sort_book_info_misc_dd3',
		
		'sort_book_info_misc_dd4',	
		'sort_book_info_misc_dd5',
		'sort_book_info_misc_dd6',			
	
        'sort_book_info_misc_bool_3',	//First Edition only appears when admin checked
		'sort_book_info_misc_bool_4',   
		//****'sort_book_info_misc_bool_5',	//IS used_book, is set to show used or new 
		'sort_book_info_misc_bool_6',
		
        'sort_book_info_misc_int_5_1',
        'sort_book_info_misc_int_5_2',
        'sort_book_info_misc_int_5_3',
		'sort_book_info_misc_int_11_1', 
		'sort_book_info_misc_int_11_2',
        'sort_book_info_misc_int_11_3'
		);
		for ($iz=0, $nz=sizeof($sort_arr); $iz<$nz; $iz++) {
       // echo  $sort_arr[$i] .  "<br>";
      }
	  ?>
<div class="centerColumn" id="productBookDisplay">

<!--bof Form start-->
<?php echo zen_draw_form('cart_quantity', zen_href_link(zen_get_info_page($_GET['products_id']), zen_get_all_get_params(array('action')) . 'action=add_product'), 'post', 'enctype="multipart/form-data"') . "\n"; ?>
<!--eof Form start-->

<?php if ($messageStack->size('product_info') > 0) echo $messageStack->output('product_info'); ?>

<!--bof Category Icon -->
<?php if ($module_show_categories != 0) {?>
<?php
/**
 * display the category icons
 */
require($template->get_template_dir('/tpl_modules_category_icon_display.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_category_icon_display.php'); ?>
<?php } ?>
<!--eof Category Icon -->

<!--eof Prev/Next top position -->
<?php if (PRODUCT_INFO_PREVIOUS_NEXT == 1 or PRODUCT_INFO_PREVIOUS_NEXT == 3) { ?>
<?php
/**
 * display the product previous/next helper
 */
require($template->get_template_dir('/tpl_products_next_previous.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_products_next_previous.php'); ?>
<?php } ?>
<!--eof Prev/Next top position-->

<!--bof Main Product Image -->
<?php
//mod removes no picture image display
  if (zen_not_null($products_image) && $products_image != 'no_picture.gif') {
  ?>
<?php
/**
 * display the main product image
 */
   require($template->get_template_dir('/tpl_modules_main_product_image.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_main_product_image.php'); ?>
<?php
   }
?>
<!--eof Main Product Image-->

<!--bof Product Name-->
<h1 id="productName" class="productGeneral"><?php echo $products_name; ?></h1>
<!--eof Product Name-->
<?php
if($products_book_subtitle != '') {
?>
<h1 id="productBookSubtitle" class="productGeneral">
<?php echo "( " . $products_book_subtitle . " )"; ?>
</h1>
<?php
}
//picmod jph
if ($products_image == 'no_picture.gif') echo '<span class="no_picture back">' . TEXT_BOOK_NO_PICTURE . ' </span>';

?>

<!-- moved price from here jph -->


<?php if ($flag_show_product_book_info_authors == 1 ) {
  if (sizeof($products_book_authors) == 1) {
    $author_id = $products_book_authors[0]['id'];
    
//========jph tidy up name mod
	if(strstr($products_book_authors[0]['name'], ',')) { //juggle
		$tmp = explode(',', $products_book_authors[0]['name'], 2);
		$name = trim($tmp[1]) . " " . trim($tmp[0]);
	}else{//leave as is
      	$name = $products_book_authors[0]['name'];	
	}		

//=======================
    $nickname = (($products_book_authors[0]['nickname'] and $flag_show_product_book_info_authors_nickname == 1) ? '&nbsp;(' . $products_book_authors[0]['nickname'] . ')' : '');
    echo '<div><div id="productBookAuthors class="bk_label back">' . TEXT_PRODUCT_BOOK_AUTHOR . 
		'&nbsp;&nbsp;</div><div class="authorsList back"><a href="' . 
		zen_href_link(FILENAME_DEFAULT, 'book_authors_id=' . $author_id . '&typefilter=book_authors') . 
		'" title="' . sprintf(TEXT_PRODUCT_BOOK_FIND_THIS_AUTHOR, $name) . '">' . $name . '</a>' . 
		$nickname . '</div></div>' . "\n";
  } else if (sizeof($products_book_authors) > 1) {
    echo '<div><div id="productBookAuthors" class="bk_label back">' . TEXT_PRODUCT_BOOK_AUTHORS . 
		'&nbsp;&nbsp;</div><div class="authorsList back">';
    for ($i=0, $n=sizeof($products_book_authors); $i<$n; $i++) {
      $author_id = $products_book_authors[$i]['id'];      
//=========jph tidy up mod
	  if(strstr($products_book_authors[$i]['name'], ',')) { //juggle
			$tmp = explode(',', $products_book_authors[$i]['name'], 2);
			$name =  trim($tmp[1]) . " " . trim($tmp[0]);
	  }else{//leave as is
      		$name = $products_book_authors[$i]['name'];	
	  }		
//=======================
      $nickname = (($products_book_authors[$i]['nickname'] and $flag_show_product_book_info_authors_nickname == 1) ? '&nbsp;(' . $products_book_authors[$i]['nickname'] . ')' : '');
      $separator = '<br />';//(($i<$n-1) ? '; ' : '');
      echo '<a href="' . zen_href_link(FILENAME_DEFAULT, 'book_authors_id=' . $author_id . 
	  	'&typefilter=book_authors') . '" title="' . sprintf(TEXT_PRODUCT_BOOK_FIND_THIS_AUTHOR, $name) . 
		'">' . $name . '</a>' . $nickname . $separator . "\n";
    }
    echo '</div></div>' . "\n";
  }
} ?>
<br class="clearBoth" />
 <!--bof Product description -->
<?php if ($products_description != '') { ?>
<div id="productBookDescription" class="productGeneral biggerText">
<?php echo stripslashes($products_description); ?>
</div>
<?php } ?>
<!--eof Product description -->
<br class="clearBoth" />
<?php
//=============SPECIAL     'display_line_A' new/used; type(subtype); format; size; plus pages
		$output = '';
		$start = '<li id="book_display_line_A">';
		//bool 5, new/used
		$output .= (($flag_show_product_book_info_misc_bool_5 == 1 ) ?   
		TEXT_PRODUCT_BOOK_MISC_BOOL_5 . (($products_book_misc_bool_5 == 1) ?  
		TEXT_PRODUCT_BOOK_MISC_BOOL_5_YES  : TEXT_PRODUCT_BOOK_MISC_BOOL_5_NO)  : '');   		
 	 	//type = book, magazine etc
		if (sizeof($products_book_types) == 1) {
      		$output .= '<a href="' . zen_href_link(FILENAME_DEFAULT, 'typefilter=book_type&book_type_id=' . 
				$products_book_types[0]['id']) . '" title="' . 
				sprintf(TEXT_PRODUCT_BOOK_FIND_THIS_TYPE, strtolower($products_book_types[0]['name'])) .
			 	'">' . $products_book_types[0]['name'] . '</a>' . "\n";
    	} else if (sizeof($products_book_types) > 1) {
      		for ($i=0, $n=sizeof($products_book_types); $i<$n; $i++) {
        		$output .= '<a href="' . zen_href_link(FILENAME_DEFAULT, 'typefilter=book_type&book_type_id=' .
				 	$products_book_types[$i]['id']) . '" title="' . 
				 	sprintf(TEXT_PRODUCT_BOOK_FIND_THIS_TYPE, strtolower($products_book_types[$i]['name'])) .
				  	'">' . $products_book_types[$i]['name'] . '</a>' . (($i<$n-1) ? ', ' : '') . "\n";
      		}
    	}
		//format (color field))
		$output .= (($flag_show_product_book_info_color == 1 and $products_book_color != '') ?   
  			(($products_book_color_description != '' 
			and $products_book_color_description != '&nbsp;') ? '<abbr title="' . 
			$products_book_color_description . '">' : '') . $products_book_color . 
			(($products_book_color_description != '' 
			and $products_book_color_description != '&nbsp;') ? '</abbr>' : ''): '');
		//size
		$output .= (($flag_show_product_book_info_size == 1 and $products_book_size != '') ?   
  			'&nbsp;' . TEXT_PRODUCT_BOOK_SIZE . $products_book_size . TEXT_PRODUCT_BOOK_SIZE_UNIT  . ", " : '');
	   //pages
	   	$output .= (($flag_show_product_book_info_pages == 1 and $products_book_pages != 0) ?  
  			TEXT_PRODUCT_BOOK_PAGES . $products_book_pages . TEXT_PRODUCT_BOOK_PAGES_UNIT : ''); 
	 	$end = '</li>' . "\n";
		//place this where you want it as an echo
		if($output <> '') $output = $start . $output . $end;
//========================end display line a
//======================== display line b
//GENRE = Subjects
		$op_genres = '';
		$op_genres_def = '';
    	if (sizeof($products_book_genres) == 1) {
      		$op_genres_def = '<span class="bk_label">' . TEXT_PRODUCT_BOOK_GENRE . '</span>';
			$op_genres .= '<a href="' . zen_href_link(FILENAME_DEFAULT, 
			'typefilter=book_genre&book_genre_id=' . $products_book_genres[0]['id']) . '" title="' . 
			sprintf(TEXT_PRODUCT_BOOK_FIND_THIS_GENRE, strtolower($products_book_genres[0]['name'])) .
			 '">' . $products_book_genres[0]['name'] . '</a>' . "\n";
    	} else if (sizeof($products_book_genres) > 1) {
      		$op_genres_def =  '<span class="bk_label">' . TEXT_PRODUCT_BOOK_GENRES . "</span>\n";
      		for ($i=0, $n=sizeof($products_book_genres); $i<$n; $i++) {
        	$op_genres .= '<a href="' . zen_href_link(FILENAME_DEFAULT, 
			'typefilter=book_genre&book_genre_id=' . $products_book_genres[$i]['id']) . '" title="' . 
			sprintf(TEXT_PRODUCT_BOOK_FIND_THIS_GENRE, strtolower($products_book_genres[$i]['name'])) . 
			'">' . $products_book_genres[$i]['name'] . '</a>' . (($i<$n-1) ? ', ' : '') . "\n";
      		}
    	}
		if($op_genres <>'') $op_genres = '<li id="productBookGenres">' . 
				$op_genres_def . $op_genres . '</li>' . "\n";
//========================end display line b

?>

<?php 
if($output != '' || $op_genres != '') {
?>
<ul id="productBookDetailsLines">
<?php
//===================ADD LINE A jph 
	if($output != '') echo $output; 
//===================ADD LINE B jph
	if($op_genres != '') echo $op_genres; 
//========================END ADD LINE B jph
?>
</ul>
<?php
}
?>
<!--start jph right_container below-->
<div class="floatingBoxBookRH forward">
<div class="insert_book_price back">
<!--bof Product Price block -->
<h2 id="productPrices" class="productGeneral">
<?php
// base price
  if ($show_onetime_charges_description == 'true') {
    $one_time = '<span >' . TEXT_ONETIME_CHARGE_SYMBOL . TEXT_ONETIME_CHARGE_DESCRIPTION . '</span><br />';
  } else {
    $one_time = '';
  }
		//uses us price function. if UK use 'zen_get_products_display_price_uk' instead jph mod
  echo $one_time . ((zen_has_product_attributes_values((int)$_GET['products_id']) 
	and $flag_show_product_info_starting_at == 1) ? TEXT_BASE_PRICE : '') . 
	zen_get_products_display_price((int)$_GET['products_id']);
?></h2>
<!--eof Product Price block -->

<!--bof free ship icon  -->
<?php if(zen_get_product_is_always_free_shipping($products_id_current)) { ?>
<div id="freeShippingIcon"><?php echo TEXT_PRODUCT_FREE_SHIPPING_ICON; ?></div>
<?php } ?>
<!--eof free ship icon  -->
</div>

<!--bof Add to Cart Box -->
<?php
if (CUSTOMERS_APPROVAL == 3 and TEXT_LOGIN_FOR_PRICE_BUTTON_REPLACE_SHOWROOM == '') {
  // do nothing
} else {
?>
            <?php
    $display_qty = (($flag_show_product_info_in_cart_qty == 1 and $_SESSION['cart']->in_cart($_GET['products_id'])) ? '<p>' . PRODUCTS_ORDER_QTY_TEXT_IN_CART . $_SESSION['cart']->get_quantity($_GET['products_id']) . '</p>' : '');
            if ($products_qty_box_status == 0 or $products_quantity_order_max== 1) {
              // hide the quantity box and default to 1
              $the_button = '<input type="hidden" name="cart_quantity" value="1" />' . zen_draw_hidden_field('products_id', (int)$_GET['products_id']) . zen_image_submit(BUTTON_IMAGE_IN_CART, BUTTON_IN_CART_ALT);
            } else {
              // show the quantity box
    $the_button = PRODUCTS_ORDER_QTY_TEXT . '<input type="text" name="cart_quantity" value="' . (zen_get_buy_now_qty($_GET['products_id'])) . '" maxlength="6" size="4" /><br />' . zen_get_products_quantity_min_units_display((int)$_GET['products_id']) . '<br />' . zen_draw_hidden_field('products_id', (int)$_GET['products_id']) . zen_image_submit(BUTTON_IMAGE_IN_CART, BUTTON_IN_CART_ALT);
            }
    $display_button = zen_get_buy_now_button($_GET['products_id'], $the_button);
  ?>
  <?php if ($display_qty != '' or $display_button != '') { ?>
    <div id="cartAdd">


<?php
// 2006-04-25 : moku
if ($products_date_available > date('Y-m-d H:i:s')) {
  if ($flag_show_product_info_date_available == 1) {
?>
<p id="productBookDateAvailable" class="productBook centeredContent"><?php echo sprintf(TEXT_PRODUCT_BOOK_DATE_AVAILABLE, zen_date_short($products_date_available)); ?></p>
<?php
  }
}
?>
    <?php
      echo $display_qty;
      echo $display_button;
// BEGIN CEON BACK IN STOCK NOTIFICATIONS 1 of 2
if (!is_null($product_back_in_stock_notification_form_link)) {
  echo '<p>' . $product_back_in_stock_notification_form_link . '</p>';
}
// END CEON BACK IN STOCK NOTIFICATIONS 1 of 2
            ?>
          </div>
  <?php } // display qty and button ?>
<?php } // CUSTOMERS_APPROVAL == 3 ?>
<!--eof Add to Cart Box-->
<!--end jph right_container below-->
</div>
<!--bof Product details list  -->

<?php if ( (
           ($flag_show_product_info_model == 1 and $products_model != '')
//===============added jph
 		or ($flag_show_product_book_info_condition == 1 and $products_book_condition != '')
//===================      
		or ($flag_show_product_info_weight == 1 and $products_weight != 0)
        or ($flag_show_product_info_quantity == 1)
        or ($flag_show_product_info_manufacturer == 1 and $manufacturers_id != 0 and !empty($manufacturers_name))
        or ($flag_show_product_book_info_genre == 1 and sizeof($products_book_genres) != 0)
        or ($flag_show_product_book_info_type == 1 and sizeof($products_book_types) != 0)
        or ($flag_show_product_book_info_color == 1 and $products_book_color != '')
        or ($flag_show_product_book_info_language == 1 and sizeof($products_book_languages) != 0)
        or ($flag_show_product_book_info_pages == 1 and $products_book_pages != 0)
        or ($flag_show_product_book_info_size == 1 and $products_book_size != '')
        or ($flag_show_product_book_info_pub_date == 1 and $products_book_pub_date != '')
        or ($flag_show_product_book_info_misc_1 == 1 and $products_book_misc_1 != '')
        or ($flag_show_product_book_info_misc_2 == 1 and $products_book_misc_2 != '')
        or ($flag_show_product_book_info_misc_bool_1 == 1 and $products_book_misc_bool_1 != 0)
        or ($flag_show_product_book_info_misc_bool_2 == 1 and $products_book_misc_bool_2 != 0)
//=================added fields jph
        or ($flag_show_product_book_info_misc_3 == 1 and $products_book_misc_3 != '')
        or ($flag_show_product_book_info_misc_4 == 1 and $products_book_misc_4 != '')
        or ($flag_show_product_book_info_misc_5 == 1 and $products_book_misc_5 != '')
        or ($flag_show_product_book_info_misc_6 == 1 and $products_book_misc_6 != '')	
		or ($flag_show_product_book_info_misc_7 == 1 and $products_book_misc_7 != '')
        or ($flag_show_product_book_info_misc_8 == 1 and $products_book_misc_8 != '')
        or ($flag_show_product_book_info_misc_9 == 1 and $products_book_misc_9 != '')
        or ($flag_show_product_book_info_misc_10 == 1 and $products_book_misc_10 != '')			
	    
		or ($flag_show_product_book_info_dd1 == 1 and sizeof($products_book_dd1) != 0)
		or ($flag_show_product_book_info_dd2 == 1 and sizeof($products_book_dd2) != 0)		
		or ($flag_show_product_book_info_dd3 == 1 and sizeof($products_book_dd3) != 0)
		or ($flag_show_product_book_info_dd4 == 1 and sizeof($products_book_dd4) != 0)				
		or ($flag_show_product_book_info_dd5 == 1 and sizeof($products_book_dd5) != 0)
		or ($flag_show_product_book_info_dd6 == 1 and sizeof($products_book_dd6) != 0)				
	
        or ($flag_show_product_book_info_misc_bool_3 == 1 and $products_book_misc_bool_3 != 0)
		or ($flag_show_product_book_info_misc_bool_4 == 1 and $products_book_misc_bool_4 != 0)
		or ($flag_show_product_book_info_misc_bool_5 == 1 and $products_book_misc_bool_5 != 0)
		or ($flag_show_product_book_info_misc_bool_6 == 1 and $products_book_misc_bool_6 != 0)
		
        or ($flag_show_product_book_info_misc_int_5_1 == 1 and $products_book_misc_int_5_1 != 0)
        or ($flag_show_product_book_info_misc_int_5_2 == 1 and $products_book_misc_int_5_2 != 0)
        or ($flag_show_product_book_info_misc_int_5_3 == 1 and $products_book_misc_int_5_3 != 0)
		or ($flag_show_product_book_info_misc_int_11_1 == 1 and $products_book_misc_int_11_1 != 0)
        or ($flag_show_product_book_info_misc_int_11_2 == 1 and $products_book_misc_int_11_2 != 0)
        or ($flag_show_product_book_info_misc_int_11_3 == 1 and $products_book_misc_int_11_3 != 0)		
//=======================

        ) ) { ?>
<div id="productBookDetailsList" class="floatingBox back">

<?php
// 2006-04-14 : moku MOVED this jph
//jph order
 //============= jph ordering 
for ($ix=0, $nx=sizeof($sort_arr); $ix<$nx; $ix++) {
//=============================== codition dd plus condition description misc 2       
	if ($sort_arr[$ix] == 'sort_book_info_condition') {
  		(($products_book_condition_description != '' and $products_book_condition_description != '&nbsp;') ? $show_desc = 1 : $show_desc = 0);
  		echo '<div id="productBookCondition"><span class="bk_label">' . TEXT_PRODUCT_BOOK_CONDITION . '</span>';
  		echo (($show_desc == 1) ? '<abbr title="' . $products_book_condition_description . '">' : '');
  		$tmp1 = '';
		if ($products_book_misc_2 !='') $tmp1 = ", " . $products_book_misc_2;
		echo '<span>' . $products_book_condition . $tmp1 . '</span>';
  		echo (($show_desc == 1) ? '</abbr>' : '');
  		echo '</div>' . "\n";
 	 }
?>

<?php 
//TYPE used as single value
  	if ($sort_arr[$ix] == 'sort_book_info_type') {
    	if (sizeof($products_book_types) == 1) {
      		echo '  <div id="productBookType"><span class="bk_label">' . TEXT_PRODUCT_BOOK_TYPE . '</span><a href="' . zen_href_link(FILENAME_DEFAULT, 'typefilter=book_type&book_type_id=' . $products_book_types[0]['id']) . '" title="' . sprintf(TEXT_PRODUCT_BOOK_FIND_THIS_TYPE, strtolower($products_book_types[0]['name'])) . '">' . $products_book_types[0]['name'] . '</a></div>' . "\n";
    	} else if (sizeof($products_book_types) > 1) {
      		echo '  <div id="productBookTypes"><span class="bk_label">' . TEXT_PRODUCT_BOOK_TYPES . "</span>\n";
      		for ($i=0, $n=sizeof($products_book_types); $i<$n; $i++) {
        		echo '    <a href="' . zen_href_link(FILENAME_DEFAULT, 'typefilter=book_type&book_type_id=' . $products_book_types[$i]['id']) . '" title="' . sprintf(TEXT_PRODUCT_BOOK_FIND_THIS_TYPE, strtolower($products_book_types[$i]['name'])) . '">' . $products_book_types[$i]['name'] . '</a>' . (($i<$n-1) ? ', ' : '') . "\n";
      		}
      	echo '  </div>' . "\n";
    	}
  	}
//COLOR = format used as single value
	if($sort_arr[$ix] == 'sort_book_info_color' ){
		echo (($flag_show_product_book_info_color == 1 and $products_book_color != '') ? '<div><span class="bk_label">' . 
  			TEXT_PRODUCT_BOOK_COLOR . '</span>' . (($products_book_color_description != '' 
			and $products_book_color_description != '&nbsp;') ? '<abbr title="' . 
			$products_book_color_description . '">' : '') . $products_book_color . 
			(($products_book_color_description != '' 
			and $products_book_color_description != '&nbsp;') ? '</abbr>' : '') . '</div>' . "\n": '');
	  } 
//PAGES
	if($sort_arr[$ix] == 'sort_book_info_pages' ){
		echo (($flag_show_product_book_info_pages == 1 and $products_book_pages != 0) ? '<div><span class="bk_label">' . 
  			TEXT_PRODUCT_BOOK_PAGES . '</span>' . $products_book_pages . TEXT_PRODUCT_BOOK_PAGES_UNIT .
			 '</div>' . "\n" : ''); 
	} 
//SIZE
	if($sort_arr[$ix] == 'sort_book_info_size' ){ 
		echo (($flag_show_product_book_info_size == 1 and $products_book_size != '') ? '<div><span class="bk_label">' . 
  			TEXT_PRODUCT_BOOK_SIZE . '</span>' . $products_book_size . TEXT_PRODUCT_BOOK_SIZE_UNIT . '</div>' .
			 "\n" : '');
	}
//LANGUAGES	 
	if($sort_arr[$ix] == 'sort_book_info_language' ){
    	if (sizeof($products_book_languages) == 1) {
      		echo '  <div id="productBookLanguage"><span class="bk_label">' . TEXT_PRODUCT_BOOK_LANGUAGE  . '</span>'. 
			((SHOW_LANGUAGES_FLAGS == 1) ? zen_image(DIR_WS_IMAGES . PRODUCTS_LANGUAGES_IMAGES_DIRECTORY .
			 '/' . $products_book_languages[0]['image'], $products_book_languages[0]['name']) :
			  $products_book_languages[0]['name']) . '</div>' . "\n";
    	} else if (sizeof($products_book_languages) > 1) {
      		echo '  <div id="productBookLanguages"><span class="bk_label">' . TEXT_PRODUCT_BOOK_LANGUAGES . '</span>' . "\n";
      		for ($i=0, $n=sizeof($products_book_languages); $i<$n; $i++) {
        		echo '    ' . ((SHOW_LANGUAGES_FLAGS == 1) ? zen_image(DIR_WS_IMAGES . 
				PRODUCTS_LANGUAGES_IMAGES_DIRECTORY . '/' . $products_book_languages[$i]['image'], 
				$products_book_languages[$i]['name']) . (($i<$n-1) ? ' ' : '') : 
				$products_book_languages[$i]['name'] . (($i<$n-1) ? ', ' : '')) . "\n";
      		}
      		echo '  </div>' . "\n";
    	}
	 }
//MISC_1	
	if($sort_arr[$ix] == 'sort_book_info_misc_1' ){
		echo (($flag_show_product_book_info_misc_1 == 1 and $products_book_misc_1 != '') ? '<div><span class="bk_label">' . 
			TEXT_PRODUCT_BOOK_MISC_1 . '</span>' . $products_book_misc_1 . '</div>' . "\n" : ''); 
	} 
//MISC_2  
/*
	if($sort_arr[$ix] == 'sort_book_info_misc_2' ){
		echo (($flag_show_product_book_info_misc_2 == 1 and $products_book_misc_2 != '') ? '<div><span class="bk_label">' . 
		TEXT_PRODUCT_BOOK_MISC_2 . '</span>' . $products_book_misc_2 . '</div>' . "\n" : '');
	} 
*/
//MISC_BOOL_1	
	if($sort_arr[$ix] == 'sort_book_info_misc_bool_1' ){
		echo (($flag_show_product_book_info_misc_bool_1 == 1 and $products_book_misc_bool_1 != 0) ? 
			'<div><span class="bk_label">' . TEXT_PRODUCT_BOOK_MISC_BOOL_1 . '</span>' . (($products_book_misc_bool_1 == 1) ? 
			TEXT_PRODUCT_BOOK_MISC_BOOL_1_YES : '') . '</div>' . "\n" : ''); 
	}
//MISC_BOOL_1	
	if($sort_arr[$ix] == 'sort_book_info_misc_bool_2' ){
		echo (($flag_show_product_book_info_misc_bool_2 == 1 and $products_book_misc_bool_2 != 0) ? 
		'<div><span class="bk_label">' . TEXT_PRODUCT_BOOK_MISC_BOOL_2 . '</span>' . (($products_book_misc_bool_2 == 1) ? 
		TEXT_PRODUCT_BOOK_MISC_BOOL_2_YES : '') . '</div>' . "\n" : '');
	}
//lang mod PUB_DATE + MISC_1 as Month, set for single value of month only per lang
	if($sort_arr[$ix] == 'sort_book_info_pub_date' ){
		 $outputdates = '';
		 //dd1 month
		 if ($flag_show_product_book_info_misc_dd1 == 1) {		
    		if (sizeof($products_book_dd1) == 1 ) {
      			$outputdates .= $products_book_dd1[0]['name'] . " ";
    		} 
  		}
		 //pub_date year
		 $outputdates .= (($flag_show_product_book_info_pub_date == 1 and $products_book_pub_date != '') ?  
		  $products_book_pub_date : ''); 
	
		if($outputdates != '') echo '<div id="productPubdates"><span class="bk_label">' . 
			TEXT_PRODUCT_BOOK_PUB_DATE . '</span>' . $outputdates . '</div>' . "\n";
	}		 
//WEIGHT
	if($sort_arr[$ix] == 'sort_info_weight' ){		 
		echo (($flag_show_product_info_weight == 1 and $products_weight !=0) ? '<div><span class="bk_label">' . 
			TEXT_PRODUCT_WEIGHT . '</span>' .  $products_weight . TEXT_PRODUCT_WEIGHT_UNIT . '</div>' . "\n" : ''); 
	}
//QUANTITY
	if($sort_arr[$ix] == 'sort_info_quantity' ){		
		echo (($flag_show_product_info_quantity == 1) ? '<div>' . $products_quantity . 
			TEXT_PRODUCT_QUANTITY . '</div>' . "\n"  : ''); 
	}
//MANUFACTURER / PUBLISHER	//++ url mod june 2007

	if($sort_arr[$ix] == 'sort_info_manufacturer' ){						
		if($flag_show_product_info_manufacturer == 1 and $manufacturers_id != 0 
			and !empty($manufacturers_name)) { 
			echo '<div><span class="bk_label">' . TEXT_PRODUCT_MANUFACTURER . '</span>' . 
					'<a href="' . zen_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $manufacturers_id) . 
					'" title="' . sprintf(TEXT_PRODUCT_BOOK_FIND_THIS_PUBLISHER, $manufacturers_name) . '">' . 
					$manufacturers_name . '</a>';
			$url = jph_get_manufacturers_url($manufacturers_id);
			if($url <> '') echo ' <a href="' . $url . '" title="' . TEXT_PRODUCT_BOOK_PUBLISHER_URL_ALT . 
									'" target="_blank">' . TEXT_PRODUCT_BOOK_PUBLISHER_URL . '</a>';
			echo '</div>' . "\n";
		} 
	}

//MODEL / REF
	if($sort_arr[$ix] == 'sort_info_model' ){			
		echo (($flag_show_product_info_model == 1 and $products_model !='') ? '<div><span class="bk_label">' . 
			TEXT_PRODUCT_MODEL . '</span>' . $products_model . '</div>' : '') . "\n"; 
	}
	
//===================jph added fields
//MISC_3 
	if($sort_arr[$ix] == 'sort_book_info_misc_3' ){
		echo (($flag_show_product_book_info_misc_3 == 1 and $products_book_misc_3 != '') ? '<div><span class="bk_label">' . 
		TEXT_PRODUCT_BOOK_MISC_3 . '</span>' . $products_book_misc_3 . '</div>' . "\n" : ''); 
	}
//MISC_4 
	if($sort_arr[$ix] == 'sort_book_info_misc_4' ){		
		echo (($flag_show_product_book_info_misc_4 == 1 and $products_book_misc_4 != '') ? '<div><span class="bk_label">' . 
			TEXT_PRODUCT_BOOK_MISC_4 . '</span>' . $products_book_misc_4 . '</div>' . "\n" : ''); 
	}
//MISC_5
/* 
	if($sort_arr[$ix] == 'sort_book_info_misc_5' ){					
		echo (($flag_show_product_book_info_misc_5 == 1 and $products_book_misc_5 != '') ? '<div><span class="bk_label">' . 
			TEXT_PRODUCT_BOOK_MISC_5 . '</span>' . $products_book_misc_5 . '</div>' . "\n" : ''); 
	}
*/
//MISC_5 = book url
	if($sort_arr[$ix] == 'sort_book_info_misc_5' ){					
		if($flag_show_product_book_info_misc_5 == 1 and $products_book_misc_5 != '') {
			$url_text = TEXT_PRODUCT_BOOK_MISC_5; 
			$products_book_url = $products_book_misc_5; 
		}
	}
//MISC_6 
	if($sort_arr[$ix] == 'sort_book_info_misc_6' ){		
		echo (($flag_show_product_book_info_misc_6 == 1 and $products_book_misc_6 != '') ? '<div><span class="bk_label">' . 
			TEXT_PRODUCT_BOOK_MISC_6 . '</span>' . $products_book_misc_6 . '</div>' . "\n" : ''); 	
	}
//MISC_7 
	if($sort_arr[$ix] == 'sort_book_info_misc_7' ){				
		echo (($flag_show_product_book_info_misc_7 == 1 and $products_book_misc_7 != '') ? '<div><span class="bk_label">' . 
			TEXT_PRODUCT_BOOK_MISC_7 . '</span>' . $products_book_misc_7 . '</div>' . "\n" : ''); 		
	}
//MISC_8 
	if($sort_arr[$ix] == 'sort_book_info_misc_8' ){								
	    echo (($flag_show_product_book_info_misc_8 == 1 and $products_book_misc_8 != '') ? '<div><span class="bk_label">' . 
			TEXT_PRODUCT_BOOK_MISC_8 . '</span>' . $products_book_misc_8 . '</div>' . "\n" : ''); 
	}
//MISC_9 
	if($sort_arr[$ix] == 'sort_book_info_misc_9' ){				
		echo (($flag_show_product_book_info_misc_9 == 1 and $products_book_misc_9 != '') ? '<div><span class="bk_label">' . 
		TEXT_PRODUCT_BOOK_MISC_9 . '</span>' . $products_book_misc_9 . '</div>' . "\n" : ''); 
	}
//MISC_10 
	if($sort_arr[$ix] == 'sort_book_info_misc_10' ){				
		echo (($flag_show_product_book_info_misc_10 == 1 and $products_book_misc_10 != '') ? '<div><span class="bk_label">' . 
			TEXT_PRODUCT_BOOK_MISC_10 . '</span>' . $products_book_misc_10 . '</div>' . "\n" : ''); 
	}
//MISC_BOOL_3
	if($sort_arr[$ix] == 'sort_book_info_misc_bool_3' ){							
		echo (($flag_show_product_book_info_misc_bool_3 == 1 and $products_book_misc_bool_3 != 0) ? 
			'<div><span class="bk_label">' . TEXT_PRODUCT_BOOK_MISC_BOOL_3 . (($products_book_misc_bool_3 == 1) ? 
			TEXT_PRODUCT_BOOK_MISC_BOOL_3_YES : '') . '</span>' . '</div>' . "\n" : '');
	} 
//MISC_BOOL_4
	if($sort_arr[$ix] == 'sort_book_info_misc_bool_4' ){							
		echo (($flag_show_product_book_info_misc_bool_4 == 1 and $products_book_misc_bool_4 != 0) ? 
			'<div><span class="bk_label">' . TEXT_PRODUCT_BOOK_MISC_BOOL_4 . (($products_book_misc_bool_4 == 1) ? 
			TEXT_PRODUCT_BOOK_MISC_BOOL_4_YES : '') . '</span>' . '</div>' . "\n" : ''); 
	} 

	//echo (($flag_show_product_book_info_misc_bool_5 == 1 and $products_book_misc_bool_5 != 0) ? '<div>' . TEXT_PRODUCT_BOOK_MISC_BOOL_5 . (($products_book_misc_bool_5 == 1) ? TEXT_PRODUCT_BOOK_MISC_BOOL_5_YES : '') . '</div>' . "\n" : ''); 
//echo (($flag_show_product_book_info_misc_bool_6 == 1 and $products_book_misc_bool_6 != 0) ? '<div>' . TEXT_PRODUCT_BOOK_MISC_BOOL_6 . (($products_book_misc_bool_6 == 1) ? TEXT_PRODUCT_BOOK_MISC_BOOL_6_YES : '') . '</div>' . "\n" : ''); 
//MISC_BOOL_5 y/n
	if($sort_arr[$ix] == 'sort_book_info_misc_bool_5' ){
		echo (($flag_show_product_book_info_misc_bool_5 == 1 ) ? '<div><span class="bk_label">' . 
		TEXT_PRODUCT_BOOK_MISC_BOOL_5 . '</span>' . (($products_book_misc_bool_5 == 1) ? 
		TEXT_PRODUCT_BOOK_MISC_BOOL_5_YES : TEXT_PRODUCT_BOOK_MISC_BOOL_5_NO) . '</div>' . "\n" : ''); 
	}
//MISC_BOOL_6 y/n
	if($sort_arr[$ix] == 'sort_book_info_misc_bool_6' ){				
		echo (($flag_show_product_book_info_misc_bool_6 == 1 ) ? '<div><span class="bk_label">' . 
		TEXT_PRODUCT_BOOK_MISC_BOOL_6 . '</span>' . (($products_book_misc_bool_6 == 1) ? 
		TEXT_PRODUCT_BOOK_MISC_BOOL_6_YES : TEXT_PRODUCT_BOOK_MISC_BOOL_6_NO) . '</div>' . "\n" : '');
	} 
//MISC_INT_5_1 
	if($sort_arr[$ix] == 'sort_book_info_misc_int_5_1' ){					
		echo (($flag_show_product_book_info_misc_int_5_1 == 1 and $products_book_misc_int_5_1 != 0) ? 
			'<div><span class="bk_label">' . TEXT_PRODUCT_BOOK_MISC_INT_5_1 . '</span>' . $products_book_misc_int_5_1 . 
			TEXT_PRODUCT_BOOK_MISC_INT_5_1_UNIT . '</div>' . "\n" : ''); 
	} 			
//MISC_INT_5_2 
	if($sort_arr[$ix] == 'sort_book_info_misc_int_5_2' ){					
		echo (($flag_show_product_book_info_misc_int_5_2 == 1 and $products_book_misc_int_5_2 != 0) ? 
		'<div><span class="bk_label">' . TEXT_PRODUCT_BOOK_MISC_INT_5_2 . '</span>' . $products_book_misc_int_5_2 . 
		TEXT_PRODUCT_BOOK_MISC_INT_5_2_UNIT . '</div>' . "\n" : '');
	} 		 
//MISC_INT_5_3 
	if($sort_arr[$ix] == 'sort_book_info_misc_int_5_3' ){					
		 echo (($flag_show_product_book_info_misc_int_5_3 == 1 and $products_book_misc_int_5_3 != 0) ? 
		 '<div><span class="bk_label">' . TEXT_PRODUCT_BOOK_MISC_INT_5_3 . '</span>' . $products_book_misc_int_5_3 . 
		 TEXT_PRODUCT_BOOK_MISC_INT_5_3_UNIT . '</div>' . "\n" : '');
	} 		  
//MISC_INT_11_1 
	if($sort_arr[$ix] == 'sort_book_info_misc_int_11_1' ){			 
		 echo (($flag_show_product_book_info_misc_int_11_1 == 1 and $products_book_misc_int_11_1 != 0) ? 
		 	'<div><span class="bk_label">' . TEXT_PRODUCT_BOOK_MISC_INT_11_1 . '</span>' . $products_book_misc_int_11_1 . 
			TEXT_PRODUCT_BOOK_MISC_INT_11_1_UNIT . '</div>' . "\n" : '');
	} 			 
//MISC_INT_11_2 
	if($sort_arr[$ix] == 'sort_book_info_misc_int_11_2' ){									
		echo (($flag_show_product_book_info_misc_int_11_2 == 1 and $products_book_misc_int_11_2 != 0) ? 
			'<div><span class="bk_label">' . TEXT_PRODUCT_BOOK_MISC_INT_11_2 . '</span>' . $products_book_misc_int_11_2 . 
			TEXT_PRODUCT_BOOK_MISC_INT_11_2_UNIT . '</div>' . "\n" : ''); 
	} 			 
//MISC_INT_11_3 
	if($sort_arr[$ix] == 'sort_book_info_misc_int_11_3' ){									
		echo (($flag_show_product_book_info_misc_int_11_3 == 1 and $products_book_misc_int_11_3 != 0) ? 
			'<div><span class="bk_label">' . TEXT_PRODUCT_BOOK_MISC_INT_11_3 . '</span>' . $products_book_misc_int_11_3 . 
			TEXT_PRODUCT_BOOK_MISC_INT_11_3_UNIT . '</div>' . "\n" : ''); 
	} 			 
/* used for pub month //dd1 no search links
	if($sort_arr[$ix] == 'sort_book_info_misc_dd1' ){
	  	if ($flag_show_product_book_info_misc_dd1 == 1) {		
    		if (sizeof($products_book_dd1) == 1) {
      			echo '  <div id="productBookdd1">' . TEXT_PRODUCT_BOOK_DD1 . $products_book_dd1[0]['name'] . '</div>' . "\n";
    		} else if (sizeof($products_book_dd1) > 1) {
      			echo '  <div id="productBookdd1">' . TEXT_PRODUCT_BOOK_DD1S . "\n";
      			for ($i=0, $n=sizeof($products_book_dd1); $i<$n; $i++) {
        			echo  $products_book_dd1[$i]['name'] .  (($i<$n-1) ? ', ' : '') ;
      			}
      			echo '  </div>' . "\n";
    		}
  		}
	} */
//dd2 no search links
	if($sort_arr[$ix] == 'sort_book_info_misc_dd2' ){
  		if ($flag_show_product_book_info_misc_dd2 == 1) {
    		if (sizeof($products_book_dd2) == 1) {
      			echo '  <div id="productBookdd2"><span class="bk_label">' . TEXT_PRODUCT_BOOK_DD2 . '</span>' . $products_book_dd2[0]['name'] . '</div>' . "\n";
    		} else if (sizeof($products_book_dd2) > 1) {
      			echo '  <div id="productBookdd2"><span class="bk_label">' . TEXT_PRODUCT_BOOK_DD2S . '</span>' . "\n";
      			for ($i=0, $n=sizeof($products_book_dd2); $i<$n; $i++) {
        			echo  $products_book_dd2[$i]['name'] .  (($i<$n-1) ? ', ' : '') ;
      			}
      			echo '  </div>' . "\n";
    		}
  		}
	}
//dd3 no search links
	if($sort_arr[$ix] == 'sort_book_info_misc_dd3' ){
  		if ($flag_show_product_book_info_misc_dd3 == 1) {
    		if (sizeof($products_book_dd3) == 1) {
      			echo '  <div id="productBookdd3"><span class="bk_label">' . TEXT_PRODUCT_BOOK_DD3 . '</span>' . $products_book_dd3[0]['name'] . '</div>' . "\n";
    		} else if (sizeof($products_book_dd3) > 1) {
      			echo '  <div id="productBookdd3"><span class="bk_label">' . TEXT_PRODUCT_BOOK_DD3S . '</span>' . "\n";
      			for ($i=0, $n=sizeof($products_book_dd3); $i<$n; $i++) {
        			echo  $products_book_dd3[$i]['name'] .  (($i<$n-1) ? ', ' : '') ;
      			}
      			echo '  </div>' . "\n";
    		}
  		}
	}
//dd4 no search links
	if($sort_arr[$ix] == 'sort_book_info_misc_dd4' ){
  		if ($flag_show_product_book_info_misc_dd4 == 1) {
    		if (sizeof($products_book_dd4) == 1) {
     			 echo '  <div id="productBookdd4"><span class="bk_label">' . TEXT_PRODUCT_BOOK_DD4 . '</span>' . $products_book_dd4[0]['name'] . '</div>' . "\n";
    		} else if (sizeof($products_book_dd4) > 1) {
      			echo '  <div id="productBookdd4"><span class="bk_label">' . TEXT_PRODUCT_BOOK_DD4S . '</span>' . "\n";
      			for ($i=0, $n=sizeof($products_book_dd4); $i<$n; $i++) {
        			echo  $products_book_dd4[$i]['name'] .  (($i<$n-1) ? ', ' : '') ;
      			}
      			echo '  </div>' . "\n";
    		}
  		}
	}
//dd5 no search links
 	if($sort_arr[$ix] == 'sort_book_info_misc_dd5' ){ 
  		if ($flag_show_product_book_info_misc_dd5 == 1) {
    		if (sizeof($products_book_dd5) == 1) {
      			echo '  <div id="productBookdd5"><span class="bk_label">' . TEXT_PRODUCT_BOOK_DD5 . '</span>' . $products_book_dd5[0]['name'] . '</div>' . "\n";
    		} else if (sizeof($products_book_dd5) > 1) {
      			echo '  <div id="productBookdd5"><span class="bk_label">' . TEXT_PRODUCT_BOOK_DD5S . '</span>' . "\n";
      			for ($i=0, $n=sizeof($products_book_dd5); $i<$n; $i++) {
        			echo  $products_book_dd5[$i]['name'] .  (($i<$n-1) ? ', ' : '') ;
      			}
      		echo '  </div>' . "\n";
    		}
  		}
	}
//dd6 no search links
 	if($sort_arr[$ix] == 'sort_book_info_misc_dd6' ){ 	  
  		if ($flag_show_product_book_info_misc_dd6 == 1) {
    		if (sizeof($products_book_dd6) == 1) {
      			echo '  <div id="productBookdd6"><span class="bk_label">' . TEXT_PRODUCT_BOOK_DD6 . '</span>' . $products_book_dd6[0]['name'] . '</div>' . "\n";
    		} else if (sizeof($products_book_dd6) > 1) {
      			echo '  <div id="productBookdd6"><span class="bk_label">' . TEXT_PRODUCT_BOOK_DD6S . '</span>' . "\n";
      			for ($i=0, $n=sizeof($products_book_dd6); $i<$n; $i++) {
        			echo  $products_book_dd6[$i]['name'] .  (($i<$n-1) ? ', ' : '') ;
      			}
      			echo '  </div>' . "\n";
    		}
  		}
	}
?>
<?php //end jph added fields ?>
<?php  //============= end jph ordering 
 } //for (array) loop
//==============
?>      
</div>
<br class="clearBoth" />
<?php
  }//end big switch
?>

<!--eof Product details list -->

<!--bof Attributes Module -->
<?php
  if ($pr_attr->fields['total'] > 0) {
?>
<?php
/**
 * display the product atributes
 */
  require($template->get_template_dir('/tpl_modules_attributes.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_attributes.php'); ?>
<?php
  }
?>
<!--eof Attributes Module -->

<!--bof Quantity Discounts table -->
<?php
  if ($products_discount_type != 0) { ?>
<?php
/**
 * display the products quantity discount
 */
 require($template->get_template_dir('/tpl_modules_products_quantity_discounts.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_products_quantity_discounts.php'); ?>
<?php
  }
?>
<!--eof Quantity Discounts table -->

<!--bof Additional Product Images -->
<?php
/**
 * display the products additional images
 */
  require($template->get_template_dir('/tpl_modules_additional_images.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_additional_images.php'); ?>
<!--eof Additional Product Images -->

<!--bof Prev/Next bottom position -->
<?php if (PRODUCT_INFO_PREVIOUS_NEXT == 2 or PRODUCT_INFO_PREVIOUS_NEXT == 3) { ?>
<?php
/**
 * display the product previous/next helper
 */
 require($template->get_template_dir('/tpl_products_next_previous.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_products_next_previous.php'); ?>
<?php } ?>
<!--eof Prev/Next bottom position -->

<!--bof Tell a Friend button -->
<?php
  if ($flag_show_product_info_tell_a_friend == 1) { ?>
<div id="productTellFriendLink" class="buttonRow forward"><?php echo ($flag_show_product_info_tell_a_friend == 1 ? '<a href="' . zen_href_link(FILENAME_TELL_A_FRIEND, 'products_id=' . $_GET['products_id']) . '">' . zen_image_button(BUTTON_IMAGE_TELLAFRIEND, BUTTON_TELLAFRIEND_ALT) . '</a>' : ''); ?></div>
<?php
  }
?>
<!--eof Tell a Friend button -->

<!--bof Reviews button and count-->
<?php
  if ($flag_show_product_info_reviews == 1) {
    // if more than 0 reviews, then show reviews button; otherwise, show the "write review" button
    if ($reviews->fields['count'] > 0 ) { ?>
<div id="productReviewLink" class="buttonRow back"><?php echo '<a href="' . zen_href_link(FILENAME_PRODUCT_REVIEWS, zen_get_all_get_params()) . '">' . zen_image_button(BUTTON_IMAGE_REVIEWS, BUTTON_REVIEWS_ALT) . '</a>'; ?></div>
<br class="clearBoth" />
<p class="reviewCount"><?php echo ($flag_show_product_info_reviews_count == 1 ? TEXT_CURRENT_REVIEWS . ' ' . $reviews->fields['count'] : ''); ?></p>
<?php } else { ?>
<div id="productReviewLink" class="buttonRow back"><?php echo '<a href="' . zen_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, zen_get_all_get_params(array())) . '">' . zen_image_button(BUTTON_IMAGE_WRITE_REVIEW, BUTTON_WRITE_REVIEW_ALT) . '</a>'; ?></div>
<br class="clearBoth" />
<?php
}
}
?>
<!--eof Reviews button and count -->


<!--bof Product date added/available-->
<?php
  if ($products_date_available > date('Y-m-d H:i:s')) {
    if ($flag_show_product_info_date_available == 1) {
?>
  <p id="productDateAvailable" class="productBook centeredContent"><?php echo sprintf(TEXT_DATE_AVAILABLE, zen_date_long($products_date_available)); ?></p>
<?php
    }
  } else {
    if ($flag_show_product_info_date_added == 1) {
?>
      <p id="productDateAdded" class="productBook centeredContent"><?php echo sprintf(TEXT_DATE_ADDED, zen_date_long($products_date_added)); ?></p>
<?php
    } // $flag_show_product_info_date_added
  }
?>
<!--eof Product date added/available -->

<!--bof Product URL -->
<?php
  if ($products_book_url != '') {
?>
    <p id="productInfoLink" class="productBook centeredContent">
	<?php echo sprintf($url_text, zen_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($products_book_url), 'NONSSL', true, false)); ?></p>
<?php
    }
?>
<!--eof Product URL -->
<!--bof also purchased products module-->
<?php require($template->get_template_dir('tpl_modules_also_purchased_products.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_also_purchased_products.php');?>
<!--eof also purchased products module-->
<!--bof Form close-->
</form>
<!--bof Form close-->

<?php // BEGIN CEON BACK IN STOCK NOTIFICATIONS 2 of 2
if (isset($back_in_stock_notification_build_form) && $back_in_stock_notification_build_form) {
  // Build the notification request form
  
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
  
  $back_in_stock_notification_form = new CeonXHTMLHiTemplate;
  
  // Load in the source for the form
  $back_in_stock_notification_form->setXHTMLSource(
    $bisn_template_parts['PRODUCT_INFO_BACK_IN_STOCK_NOTIFICATION_FORM']);
  
  // Add the form action, titles, labels and button
  $form_start_tag = zen_draw_form('back_in_stock_notification',
    zen_href_link(FILENAME_BACK_IN_STOCK_NOTIFICATION_SUBSCRIBE, zen_get_all_get_params(),
    $request_type), 'POST');
  
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
  $intro_text = sprintf(BACK_IN_STOCK_NOTIFICATION_TEXT_FORM_INTRO,
    htmlentities($products_name, ENT_COMPAT, CHARSET));
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
  
  print $back_in_stock_notification_form->getXHTMLSource();
}
// END CEON BACK IN STOCK NOTIFICATIONS 2 of 2 ?>

</div>
