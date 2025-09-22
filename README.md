# Zen Cart - Back in Stock Notifications

## Functionality
If a product is out of stock, customers can subscribe/request to receive a notification when that product becomes available again.

## Compatibility
Tested from Zen Cart 157 (earlier probably) to the current version 2.1, and with php8+.

## Background
This fileset is based on the original CEON version, not the forked ajax version. Neither are supported by the original developers.

I've been modifying it for years, multi-language (now included) and attributes handling being the most significant omissions from the original code.

As a result, this code is hugely different from the plugin version, so always test on a development server: DO NOT drop it into your production server without testing first.  

Note that the original documentation in the docs folder has NOT been updated, so the file list is out of date.

## Installation/Upgrade
1. On your development server, remove any original BISN files that originated from the Zen cart Plugins.
1. Copying all this fileset should not overwrite any other files: they are all new.  
BUT, you should always use comparison software to check that is so, and to get an idea of what you are dumping into your shop. Trust no-one!  
Regarding the template files, you will find template folders "bootstrap BISN TO MERGE" and "responsive_classic BISN TO MERGE" containing modified template files for you to compare and merge into your own equivalents.
1. Go to the Admin Catalog->BISN Notifications Admin page to auto upgrade/install.

## Use/How it Works
If necessary, you may restrict the use of the BISN service to only logged-in users to prevent spam from the BISN form, or you can try the ReCaptcha plugin  
https://github.com/torvista/Zen_Cart-Google_reCAPTCHA

On a product page, the BISN observer determines if the product is out of stock and adds the BISN link and form to the page.  
The user fills in the form on the product info page (or if logged-in, their customer details are inserted automatically).  
If the submitted form data has an error, a BISN subscribe page is shown, similar to Ask a Question.
If the form data is valid, the BISN subscribe page is skipped, the subscription is recorded and a success message is shown.  
The shop admin may receive a copy of the notification.

When a product is back in stock, notification emails may be sent (manually) from the Admin BISN page.

## Languages
If you have a single language store, you should not see anything about languages.

For a multiple-language store, although the real (not test) notification emails will be sent in the same language as that used by the customer for the subscription, it requires manual intervention (is not automatic).

This is done by selecting Option 4: this sends the emails in the language that matches the **currently-selected** admin-language.
Then, changing the admin language will reload the Option 4 page and send the *other* emails that correspond to this other admin language etc.

## Testing
If you want to test the real sending of notifications repeatedly (and not delete the subscriptions):  
in admin\back_in_stock_notifications.php  
set this to false  
$delete_customer_subscriptions = true;  

The BISN configuration allows an alternative email destination for the test emails to prevent cluttering up the business email account while testing.

Optional copies of real Notification Subscription Emails are sent to what is defined in the BISN Admin (SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAILS_TO).

### Ceon XHTML template
This plugin uses a unique (to CEON/Conor) method of building the forms and emails using templates and variable substitutions.  
It's complicated to understand/modify, and something that at some point needs replacing by ZC core methods... feel free to have a go and contribute that.

## Problems/Ideas
Any problems or suggestions: open an issue in GitHub, not in the forum.

## Changelog
For minor changes: see the commit history.

22/09/2025: Bugfix for error on dedicated subscribe page when login required.

26/02/2025: add check and install for db column languages_id. Drop old template files.  

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
Use ZC158 admin header, moved CSS to a separate file.

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
