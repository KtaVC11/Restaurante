== References ==
- https://support.greenpay.me/portal/kb/articles/plugin-para-woocommerce
- https://wordpress.org/plugins/greenpay-payment-gateway/
-- https://documenter.getpostman.com/view/4767354/SVmwxe18?version=latest#73ced5bd-9109-485b-9f58-c90defa7bbc7
-- https://support.greenpay.me/portal/kb/articles/crear-una-orden-de-tokenizaci%C3%B3n
-- https://support.greenpay.me/portal/kb/articles/proceso-de-pago-sin-formulario-de-greenpay
- https://support.greenpay.me/portal/kb/articles/informaci%C3%B3n-general-28-10-2019
- https://wordpress.org/plugins/credit-card-payments-for-woocommerce-with-cybersource/
- https://rudrastyh.com/woocommerce/payment-gateway-plugin.html
- https://github.com/woocommerce/woocommerce/wiki/Payment-Token-API#get_customer_tokens-customer_id-gateway_id--- (Revisar)

== Changelog ==
= 1.0.7 - 09-07-2020 - KMA =
* New: Responses codes 30 and ND2 added in /includes/greenpay-checkout-process.php
* New: Update status to failed if payment has failed in /includes/class-baum-greenpay.php
* New: order_id is returned in endpoint response when it fails in /includes/class-baum-greenpay.php
* New: validation to accept payment empty from endpoint in /includes/class-baum-greenpay.php
* Fix: Validation to create entry in baum_bgp_transacciones in /includes/class-baum-greenpay.php

= 1.0.6 - 07-2020 - KMA =
* Update: order note added if GP is not processing the order in baum_greenpay_process_payment_with_token
* Update: WC and WP Compatibility check

= 1.0.5 =
* New: card_type validation on baum_greenpay_pay_order_by_token in includes/baum-greenpay-endpoints.php
* Update: test_mode available from SCRIPT_DEBUG in includes/class-baum-greenpay.php
* Update: ND1 message in includes/greenpay-checkout-process.php

= 1.0.4 =
* Fix: Test mode validation in includes/classass-baum-greenpay.php
* Fix: JS min validation in helper_baum in includes/class-baum-greenpay.php
* Fix: Demo functions removed in includes/baum-greenpay-endpoints.php
* Fix: Remove baum_greenpay_save_token from baum_greenpay_process_payment_with_token and now it is added from baum_greenpay_generate_token_card
* Fix: JS validation using tokens on the Checkout page in /assets/js/bgp_script.js
* Delete: Unused file in includes/greenpay-checkout-response.php
* New: Validation for accepted credit cards using method baum_greenpay_validate_cc_brand in baum_greenpay_generate_token_card and baum_greenpay_save_tokenv for endpoint validation in includes/class-baum-greenpay.php
* Revision: Checkbox to save CC is now hidden in Add Payment Method page in includes/class-baum-greenpay.php
* New: Validacion in admin for missing plugin settings in includes/class-baum-greenpay.php

= 1.0.3 =
* Update: Validation code responses in includes/greenpay-checkout-process.php
* New: Filter baum_greenpay_response_codes in includes/greenpay-checkout-process.php
* New: Actions bgp_before_cc_form_fields and bgp_after_cc_form_fields in includes/class-baum-greenpay.php

= 1.0.2 =
* IMPORTANT Update: Settings fields key renamed in includes/admin/greenpay-settings.php
* Fix: Validation in validate_fields in includes/class-baum-greenpay.php
* Fix: Validation js in forms with Save CC option
* Update: Save only valid tokens to WC. All generated tokens are saved on DB
* Fix: fecha_creacion in table bgp_tokens
* Fix: Validation for payment with token from invalid CC
* Update: Required mark to Card Holder
* Update: Payment method description to spanish with filter baum_greenpay_method_description

= 1.0.1 =
* Validation to allow saving cc in Checkout
* Validation if WC_Payment_Gateway class exists in includes/class-baum-greenpay.php
* Validation of billing and shipping country, default CR if country is empty in includes/class-baum-greenpay.php
* Fix form submit validation in bgp_script.js
** New form submit validation, now includes Pay Order page
** Card holder now is a required field
* Remove fecha_inicio field to be inserted in table bgp_transacciones in includes/class-baum-greenpay.php


== Upgrade Notice ==
= 1.0.4 =
* Several fixes in validations in services, core and JS files

= 1.0.2 =
* IMPORTANT Update: Settings fields key renamed in includes/admin/greenpay-settings.php