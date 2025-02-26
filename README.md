# Zen Cart - Back in Stock Notifications
If a product is out of stock, customers can subscribe/request to receive a notification when that product becomes available again.

This was based on the original CEON version, not the forked ajax version. Neither are supported anymore, but the functionality is well worthwhile, and I use it, so am encouraging use and development here despite it being the usual yelling into the void...

I've been modifying it for years, multi-language and attributes handling being the most significant omissions from the original code.

As a result, this code is hugely different from the plugin version, so always test on a development server: DO NOT drop it into your production server without testing first.
It's compatible with the current Zen Cart 1.5.8 and php7.3+.

Note that the original documentation in the docs folder has NOT been updated, so the file list is out of date.

## Installation/Upgrade
1. On your development server, remove original BISN files.
1. Copying all this fileset will not overwrite any other files: they are all new.  
But, regarding the template files, you will find bootstrap CLONE and responsive_classic CLONE folders containing modified template files (suffixed BISN php) for you to compare and merge into your own equivalents.
1. If you are upgrading from a previous version of BISN or from a variant, the "languages _id" column may not be present in the "back_in_stock_notification_subscriptions" table.
   - Using a program such as PHPMyAdmin check the structure of the "back_in_stock_notification_subscriptions" table.
   - If the field "languages _id" does not exist run the following SQL statement
   
      `ALTER TABLE back_in_stock_notification_subscriptions ADD languages_id INT(2) UNSIGNED NOT NULL DEFAULT '1' AFTER date_subscribed;`

1. Go to the Admin Catalog->BISN Notifications Admin page to auto upgrade/install.

## Use/How it Works
The use of the BISN service may be restricted to only logged-in users to prevent spam from the BISN form, or you can try the ReCaptcha plugin  
https://github.com/torvista/Zen_Cart-Google_reCAPTCHA

The BISN observer determines if the product is out of stock and hence shows the BISN link and form.  
The user fills in the form on the product info page or if logged-in, the data is filled automatically.  
If the submitted form data has an error, the BISN subscribe page is shown, similar to Ask a Question.
If the form data is valid, the BISN subscribe page is skipped, the subscripion is recorded and a success message is shown.

## Languages
If you have a single language store, you should not see anything about languages.

For a multiple-language store, although real notification emails will be sent in the same language as that used by the customer for the subscription, it requires manual intervention.

This is done by selecting Option 4: this sends the emails in the language that matches the currently-selected admin-language.
Then, changing the admin language will reload the Option 4 page and send the *other* emails that correspond to this other admin language etc.  
Todo: make this automatic.

## Testing
If you want to test the real sending of notifications repeatedly/not deleting the subscriptions:  
in admin\back_in_stock_notifications.php  
set this to false  
$delete_customer_subscriptions = true;  

The BISN configuration allows an alternative email destination for the test emails to prevent cluttering up the business email account while testing.

Optional copies of real Notification Subscription Emails are sent to what is defined in the BISN Admin (SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAILS_TO).

### Ceon XHTML template
This plugin uses a unique (to CEON/Conor) method of building the forms and emails using templates and variable substitutions.  
It's complicated to understand/modify, and something that needs replacing by ZC core methods... feel free to have a go and contribute that.

## Problems/Ideas
Any problems or suggestions: open an issue in GitHub, not in the forum.

## Changelog
16/08/2024: update and simplify template files

20/01/2023: added support for Google Recaptcha: https://github.com/torvista/Zen_Cart-Google_reCAPTCHA

18/11/2023 or thereabouts
Add multi-language to email sending.
A reply to the Admin copy of BISN subscription email now replies to customer
Replace tabs with spaces with all files.
Admin
Option 1 list subscriptions by product
Option 2 list all subscriptions 
Bugfix: handle fatal error for a missing/deleted product
Added column sorts/set column sort links to table id anchor
Hide model column if not used.
Add Delete buttons for each product/subscription.
Corrected paging display text.
Added support for Google reCaptcha.
And lots more fun for all the family.

11/11/2023: moved admin functions file so only loaded with BISN admin page
Remove duplicated function zen_get_products_model from bis_functions.php
Renamed BACK_IN_STOCK_NOTIFICATION_ENABLED to BACK_IN_STOCK_NOTIFICATIONS_ENABLED
Minor changes to installer messages and processing.
Add delete of single subscriptions of a product

06/11/2023: relocated required/optional template files to main file structure.
Updated template files based on ZC158 responsive_classic.

23/07/23:
Use ZC158 admin header, move css to separate file.

16/02/22:
Bugfix for duplicated subscription links when no login required.

Removed: /modules/ceon_form_bis as functionality duplicated in observer class

Removed: unnecessary observer auto loaders/observers made auto-loading 

Bugfix for missing product_model in account BISN listing

Bugfix for missing image in account BISN listing, Update button

Modified: language defines

Removed: modified core file functions_general

Removed: empty language folders

Fixes for warnings in strict mode/php8 compatibility

Miscellaneous IDE recommendations, strict comparisons, short-array syntax
