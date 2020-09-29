<?php
    if (!defined('ABSPATH')) exit;
    $wcbgp = new WC_Baum_Geenpay_Process();
    $sandbox = $wcbgp->urls['sandbox'];
    $production = $wcbgp->urls['prod'];

    $form_fields = array(
        'enabled' => array(
            'title' => __('Enable', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'checkbox',
            'label' => __('Enable Payment Gateway', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'default' => true
        ),
        'allow_save_cc' => array(
            'title' => __('Allow save CC', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'checkbox',
            'label' => __('Allow logged in users to save a CC', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'default' => false
        ),
        'merchand_id' => array(
            'title' => __('MerchandId', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'text',
            'description' => __('Its the merchantId value in the credentials file given by GreenPay', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'default' => '',
            'desc_tip' => true,
            'css' => 'width: 285px;'
        ),
        'secret' => array(
            'title' => __('Secret', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'text',
            'description' => __('Its the secret value in the credentials file given by GreenPay', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'default' => '',
            'desc_tip' => true,
            'css' => 'width: 100%;'
        ),
        'terminal_id' => array(
            'title' => __('TerminalId', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'text',
            'description' => __('Its the terminalId value in the credentials file given by GreenPay', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'default' =>'',
            'desc_tip' => true,
            'css' => 'width: 100%;'
        ),
        'currency' => array(
            'title' => __('Currency', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'text',
            'description' => __('Its the currency code value in ISO 4217 accepted by the terminalId', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'default' => '',
            'desc_tip' => true,
            'css' => 'width :50px;'
        ),
        'public_key' => array(
            'title' => __('Public key', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'textarea',
            'description' => __('Its the public key value in the credentials file given by GreenPay', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'default' => '',
            'desc_tip' => true,
            'css' => 'padding: 2px 6px; line-height: 1.4;  width: 100%; height: 200px;'
        ),
        'payment_type' => array(
            'title' => __('Payment type', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'select',
            'description' => __('Choose the payment type in GreenPay', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'default' => 'sandbox',
            'options' => array(
                'sandbox' => 'Sandbox',
                'production' => 'Production'
            ),
            'desc_tip' => true,
        ),
        'pay_order' => array(
            'title' => __('Checkout URL', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'text',
            'description' => '<strong>Sandbox: </strong>' . $sandbox['pay_order'] . '<br><strong>Production: </strong>' . $production['pay_order'],
            'default' => $sandbox['pay_order'],
            'desc_tip' => false,
            'css' => 'width: 100%;'
        ),
        'get_card_token' => array(
            'title' => __('Get token URL', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'text',
            'description' => '<strong>Sandbox: </strong>' . $sandbox['get_card_token'] . '<br><strong>Production: </strong>' . $production['get_card_token'],
            'default' => $sandbox['get_card_token'],
            'desc_tip' => false,
            'css' => 'width: 100%;'
        ),
        'create_order' => array(
            'title' => __('Create tokenization order URL', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'text',
            'description' => '<strong>Sandbox: </strong>' . $sandbox['create_order'] . '<br><strong>Production: </strong>' . $production['create_order'],
            'default' => $sandbox['create_order'],
            'desc_tip' => false,
            'css' => 'width: 100%;'
        ),
        'get_request_token' => array(
            'title' => __('Get request token URL', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'text',
            'description' => '<strong>Sandbox: </strong>' . $sandbox['get_request_token'] . '<br><strong>Production: </strong>' . $production['get_request_token'],
            'default' => $sandbox['get_request_token'],
            'desc_tip' => false,
            'css' => 'width: 100%;'
        ),
        'pay_with_token' => array(
            'title' => __('Pay with token URL', WC_BAUM_GREENPAY_TEXTDOMAIN),
            'type' => 'text',
            'description' => '<strong>Sandbox: </strong>' . $sandbox['pay_with_token'] . '<br><strong>Production: </strong>' . $production['pay_with_token'],
            'default' => $sandbox['pay_with_token'],
            'desc_tip' => false,
            'css' => 'width: 100%;'
        )
    );

    return apply_filters('wc_greenpay_settings',$form_fields);

?>