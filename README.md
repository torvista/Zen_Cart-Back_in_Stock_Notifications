# Zen Cart - Back in Stock Notifications
If a product is out of stock, customers can subscribe/request to receive a notification when that product becomes available again.

This was based on the original CEON version, not the forked ajax version. Neither are supported anymore, but the functionality is well worthwhile, and I use it, so am encouraging use and development here despite it being the usual yelling into the void...

I've been modifying it for years, multi-language and attributes handling being the most significant omissions from the original code.

As a result, this code is hugely different from the plugin version, so always test on a development server: DO NOT drop it into your production server without testing first.
It's compatible with the current Zen Cart 1.5.8 and php7.3+.

Note that the original documentation in the docs folder has NOT been updated for the moment, so the file list is out of date.

## Installation/Upgrade
On your development server, remove original BISN files and merge this fileset: apart from the template files, all are new/do not overwrite core files.  
Where files are a modification from a core file, I include the core file named as filename.158 php/filename.200 php for comparison purposes.  
Template examples are provided for responsive_classic and bootstrap.

Go to the Admin Catalog->BISN Notifications Admin page to auto upgrade/install.

## Testing
If you want to test the real sending of notifications, repeatedly/not deleting the subscriptions: set this to false  
$delete_customer_subscriptions = true;  
in admin\back_in_stock_notifications.php  
The BISN configuration allows an alternative email address for the test emails to prevent cluttering of the main email account while testing.

Optional Copies of real Notification Subscription Emails are sent to what is defined in the BISN Admin (SEND_EXTRA_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTION_EMAILS_TO).

## Languages
If you have a single language store, you should not see anything about languages.  
For a multiple-language store, real notification emails should be sent in the same language as that used by the customer for the subscription.
This is done by selecting Option 4: this sends the emails in the language that matches the currently-selected admin-language.
Then, changing the admin language will reload Option 4 and send the *other* emails that correspond to this other admin language etc.  
Todo: make this automatic.

## Use/How it Works
The use of the BISN service may be restricted to only logged-in users to prevent spam from the BISN form, or you can try the ReCaptcha plugin
https://github.com/torvista/Zen_Cart-Google_reCAPTCHA

The BISN observer determines if the product is out of stock/if the BISN link and form should be shown.  
The user fills in the form on the product info page or if logged-in, the data is filled automatically.  
If the submitted form data has an error, the BISN subscribe page is shown, similar to Ask a Question.
If the form data is valid, the BISN subscribe page is skipped, the subscripion is recorded and a success message is shown.  

### Ceon XHTML template
This plugin uses a unique method of building the forms and emails using variable substitution. Complicated to understand, and something that will require removal if this is ever to go into Zen Cart core.

## Problems/Ideas
Any problems or suggestions: open an issue here in GitHub, not in the forum.

## Changelog
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
