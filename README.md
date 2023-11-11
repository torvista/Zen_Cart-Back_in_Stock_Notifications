# Zen Cart - Back in Stock Notifications
If a product is out of stock, customers can subscribe/request to receive a notification when that product becomes available again.

This is based on the original CEON version, not the forked ajax version. Neither are supported anymore, but the functionality is well worthwhile, and I use it, so am encouraging use and development here.

I've been modifying it for years, so here is where I'll add in those modifications when the mood takes me and, in the process, make the code more Zen-ish to maybe get it into the core one day.

You may report bugs here (for this CEON version only). This code is tested with the current Zen Cart 1.5.8 and is compatible with php7.3 upwards.

Note that the original documentation in the docs folder will NOT be updated for the moment, so the file list is out of date.


## Changelog
11/11/2023: moved admin functions file so only loaded with BISN admin page
Remove duplicated function zen_get_products_model from bis_functions.php

06/11/2023: relocated required/optional template files to main file structure.
Updated template files based on ZC158 responsive_classic.

23/07/23:
Use ZC158 admin header, move css to separate file.

16/02/22:
Bugfix for duplicated subscription links when no login required.

Removed: /modules/ceon_form_bis as functionality duplicated in observer class

Modified: observers made auto-loading

Removed: unnecessary observer loaders 

Bugfix for missing product_model in account BISN listing

Bugfix for missing image in account BISN listing, Update button

Modified: language defines

Removed: modified core file functions_general

Removed: empty language folders

Fixes for warnings in strict mode/php8 compatibility

Miscellaneous IDE recommendations, strict comparisons, short-array syntax
