<?php
/**
** Baum GreenPay process class
**/
class WC_Baum_Geenpay_Process {

	function __construct() {
		$this->responses = apply_filters('baum_greenpay_response_codes', array(
			'01' => __('Refierase al emisor', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'03' => __('Comercio inválido', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'04' => __('Retirar tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'05' => __('Denegada', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'12' => __('Transacción inválida', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'13' => __('Monto inválido trate de nuevo', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'14' => __('Tarjeta inválida', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'19' => __('Reingresar transacción', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'30' => __('Error de formato', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'31' => __('Banco no soportado', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'41' => __('Tarjeta perdida', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'43' => __('Retener y llamar', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'51' => __('Fondos insuficientes', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'54' => __('Tarjeta vencida retire renovación', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'55' => __('Pin incorrecto', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'58' => __('Función no permitida', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'62' => __('Error al autorizar', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'63' => __('Error al autorizar', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'65' => __('Error al autorizar', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'78' => __('Error al autorizar', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'89' => __('Terminal inválido', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'91' => __('Emisor no contesta', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'96' => __('No soportada', WC_BAUM_GREENPAY_TEXTDOMAIN),
			'201' => __('Consulte Verbal', WC_BAUM_GREENPAY_TEXTDOMAIN), // BAC
			'203' => __('Comercio Inválido', WC_BAUM_GREENPAY_TEXTDOMAIN), // BAC
			'205' => __('Denegada', WC_BAUM_GREENPAY_TEXTDOMAIN), // BAC
			'212' => __('Transacción Invalida', WC_BAUM_GREENPAY_TEXTDOMAIN), // BAC
			'241' => __('Retener tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN), // BAC
			'251' => __('Denegada por fondos insuficientes', WC_BAUM_GREENPAY_TEXTDOMAIN), // BAC
			'255' => __('Pin Incorrecto', WC_BAUM_GREENPAY_TEXTDOMAIN), // BAC
			'278' => __('Tran. No Encontrado', WC_BAUM_GREENPAY_TEXTDOMAIN), // BAC
			'262' => __('Error al autorizar', WC_BAUM_GREENPAY_TEXTDOMAIN), // BNCR
			'899' => __('No se pudo efectuar la transacción: timeout - Se aplica reversión', WC_BAUM_GREENPAY_TEXTDOMAIN), // GreenPay
			'996' => __('Transacción fraudulenta. Kount error', WC_BAUM_GREENPAY_TEXTDOMAIN), // GreenPay
			'ND1' => __('Error desconocido al procesar el pago', WC_BAUM_GREENPAY_TEXTDOMAIN), // Baum
			'ND2' => __('Error desconocido no identificado', WC_BAUM_GREENPAY_TEXTDOMAIN) // Baum
		));

		$this->urls = array(
			'sandbox' => array(
				'pay_order' => 'https://sandbox-checkout.greenpay.me', // https://sandbox-checkoutform.greenpay.me
				'get_card_token' => 'https://sandbox-checkout.greenpay.me/tokenize',
				'create_order' => 'https://sandbox-merchant.greenpay.me',
				'get_request_token' => 'https://sandbox-merchant.greenpay.me/tokenize',
				'pay_with_token' => 'https://sandbox-merchant.greenpay.me/tokenPayment'
			),
			'prod' => array(
				'pay_order' => 'https://checkout.greenpay.me',
				'get_card_token' => 'https://checkout.greenpay.me/tokenize',
				'create_order' => 'https://merchant.greenpay.me',
				'get_request_token' => 'https://merchant.greenpay.me/tokenize',
				'pay_with_token' => 'https://merchant.greenpay.me/tokenPayment'
			)
		);
	}	
}