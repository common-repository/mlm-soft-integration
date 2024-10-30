# MLM Soft Integration #

## Changelog ##

## 2023-09-04 - version 3.6.6
* Possibility of using custom CSS for checkout page.

## 2023-07-08 - version 3.6.5
* Tested up to: 6.5
* Updated translations.

## 2023-07-04 - version 3.6.4
* Added  a `registration_sponsor_field_attribute` option.
* Remove red error (issue MLM-1101).

## 2023-06-05 - version 3.6.3
* Using the walletId in the coupon code and $couponItems.

## 2023-03-04 - version 3.6.2
* Added use of the `sponsorID` field from the plugin on the `my-account` and `checkout` pages.

## 2023-03-01 - version 3.6.1
* A second e-wallet has been added.

## 2023-02-21 - version 3.5.8
* Small css and translation fixes.

## 2023-01-31 - version 3.5.7
* Checking the activity of coupons to prevent incorrect use of the e-wallet.

## 2023-01-23 - version 3.5.5
* Added $domainSignature property to the documentPayload array.

## 2023-12-21 - version 3.5.4
* Замена `add_post_meta` на `update_post_meta` чтобы не добавлять несколько мета.

## 2023-12-27 - version 3.5.4
* Fix the layout in coupon option tab.

## 2023-11-03 - version 3.5.2
* Редирект после аутентификации из внешнего сервиса на заданую в опциях страницу.

## 2023-10-30 - version 3.5.1
* Добавлена аутентификация из внешнего сервиса.

## 2023-07-27 - version 3.4.13
* Class `MLMSoftAccountInfoBlock`: Typecast to prevent `PHP Fatal error: Argument $haystack must be of type array, string given` in in_array function.
* Class `MLMSoftLocalUser`: Using `update_user_meta` function only to prevent adding multiple meta for each update.
  ( @see Description https://developer.wordpress.org/reference/functions/update_user_meta/ )
* Class `MLMSoftAdminPanel`: Revising of the function with the addition of an action.

