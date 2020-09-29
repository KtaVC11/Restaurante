<?php
if (!defined('ABSPATH')) die;

/**
** Baum GreenPay Rest API class
**/
class WC_Baum_Greenpay_RestAPI {
	protected $namespace = 'wc/v3';
	protected $rest_base = 'baum_greenpay';

	public function __construct() {}

	public function register_routes() {
		// Pay Order by token
		register_rest_route($this->namespace, '/' . $this->rest_base . '/pay_order/', array(
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array($this, 'baum_greenpay_pay_order_by_token'),
				'args' => array(
					'order_id' => array(
						'required' => true,
						'description' => __('ID de orden a actualizar', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'integer'
					),
					'token' => array(
						'required' => true,
						'description' => __('Token generado a partir de la tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'string'
					),
					'card_type' => array(
						'required' => true,
						'description' => __('Tipo de tarjeta del token generado', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'string'
					)
				)
			)
		));

		// Get tokens by User ID
		register_rest_route($this->namespace, '/' . $this->rest_base . '/get_tokens/(?P<user_id>[\d]+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array($this, 'baum_greenpay_get_tokens_by_user_id'),
			),
		));

		// Save token to WC
		register_rest_route($this->namespace, '/' . $this->rest_base . '/save_token/', array(
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array($this, 'baum_greenpay_save_token_from_app'),
				'args' => array(
					'last4' => array(
						'required' => true,
						'description' => __('Últimos 4 dígitos de tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'string'
					),
					'expiry_year' => array(
						'required' => true,
						'description' => __('Año de expiración: 00', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'string'
					),
					'expiry_month' => array(
						'required' => true,
						'description' => __('Mes de expiración: 00', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'string'
					), 
					'card_type' => array(
						'required' => true,
						'description' => __('Tipo de tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'string'
					), 
					'card_holder' => array(
						'required' => false,
						'description' => __('Titular de la tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'string'
					), 
					'token' => array(
						'required' => true,
						'description' => __('Token de la tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'string'
					), 
					'user_id' => array(
						'required' => true,
						'description' => __('ID usuario', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'integer'
					)
				)
			)
		));

		// Remove token by token ID
		register_rest_route($this->namespace, '/' . $this->rest_base . '/set_default_or_delete_token/', array(
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array($this, 'baum_greenpay_set_default_or_delete_token_by_id'),
				'args' => array(
					'user_id' => array(
						'required' => true,
						'description' => __('ID usuario es requerido', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'integer'
					),
					'token_id' => array(
						'required' => true,
						'description' => __('ID token es requerido', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'integer'
					),
					'action' => array(
						'required' => true,
						'description' => __('Acción a ejecutar: set_default/delete', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'string'
					),
				)
			),
		));

		// Generate a tokenize order in GreenPay
		register_rest_route($this->namespace, '/' . $this->rest_base . '/generate_tokenize_order/', array(
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array($this, 'baum_greenpay_generate_tokenize_order_by_order_id'),
				'args' => array(
					'order_id' => array(
						'required' => true,
						'description' => __('ID orden es requerido', WC_BAUM_GREENPAY_TEXTDOMAIN),
						'type' => 'string'
					)
				)
			),
		));
	}

	/**
	** Pay order by token
	**/
	public function baum_greenpay_pay_order_by_token($request) {
		try {
			$order_id = $request['order_id'];
			$token = $request['token'];
			$card_type = $request['card_type'];
			$response = array();
			
			if (empty($order_id))
				throw new WC_REST_Exception('baum_greenpay_parameter_required', __('Parámetro order_id es requerido', WC_BAUM_GREENPAY_TEXTDOMAIN), 401);

			if (empty($token))
				throw new WC_REST_Exception('baum_greenpay_parameter_required', __('Parámetro token es requerido', WC_BAUM_GREENPAY_TEXTDOMAIN), 401);

			if (empty($token))
				throw new WC_REST_Exception('baum_greenpay_parameter_required', __('Parámetro token es requerido', WC_BAUM_GREENPAY_TEXTDOMAIN), 401);

			$wcbp = new WC_Baum_Greenpay();
			// Validate cc
			$is_accepted_cc = $wcbp->baum_greenpay_validate_cc_brand($request['card_type']);
			if (!$is_accepted_cc['response']) {
				throw new WC_REST_Exception('baum_greenpay_invalid_cc_type', $is_accepted_cc['error'], 401);
			}

			return rest_ensure_response($wcbp->baum_greenpay_process_payment_with_token($order_id, $token, true));
		} catch ( WC_REST_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	** Get tokens by User ID
	**/
	public function baum_greenpay_get_tokens_by_user_id($request) {
		try {
			$user_id = $request['user_id'];
			$response = array();

			if (!empty($user_id)) {
				$tokens = WC_Payment_Tokens::get_tokens(array('user_id' => $user_id));

				if (!$tokens) {
					throw new WC_REST_Exception('baum_greenpay_api_not_found', __('No hay tokens asociados a este usuario', WC_BAUM_GREENPAY_TEXTDOMAIN), 400);
				}

				foreach ($tokens as $key => $token) {
					$response[] = array(
						'gateway_id' => $token->get_gateway_id(),
						'is_default' => $token->is_default(),
						'token_id' => $token->get_id(),
						'card_type' => $token->get_card_type(),
						'last4' => $token->get_last4(),
						'expiry_month' => $token->get_expiry_month(),
						'expiry_year' => $token->get_expiry_year(),
						'token' => $token->get_token()
					);
				}

				return rest_ensure_response($response);
			}
		} catch ( WC_REST_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	** Save token to WC
	**/
	public function baum_greenpay_save_token_from_app($request) {
		try {
			$expiry_year = $request['expiry_year'];
			$expiry_month = $request['expiry_month'];
			$card_type = $request['card_type'];
			$last4 = $request['last4'];
			$token = $request['token'];
			$user_id = $request['user_id'];
			$card_holder = $request['card_holder'];

			if (!$expiry_year)
				throw new WC_REST_Exception('baum_greenpay_parameter_required', __('Año de expiración requerido', WC_BAUM_GREENPAY_TEXTDOMAIN), 401);
			if (!$expiry_month)
				throw new WC_REST_Exception('baum_greenpay_parameter_required', __('Mes de expiración requerido', WC_BAUM_GREENPAY_TEXTDOMAIN), 401);
			if (!$card_type)
				throw new WC_REST_Exception('baum_greenpay_parameter_required', __('Tipo de tarjeta requerido', WC_BAUM_GREENPAY_TEXTDOMAIN), 401);
			if (!$last4)
				throw new WC_REST_Exception('baum_greenpay_parameter_required', __('Últimos 4 dígitos requeridos', WC_BAUM_GREENPAY_TEXTDOMAIN), 401);
			if (!$token)
				throw new WC_REST_Exception('baum_greenpay_parameter_required', __('Token requerido', WC_BAUM_GREENPAY_TEXTDOMAIN), 401);
			if (!$user_id)
				throw new WC_REST_Exception('baum_greenpay_parameter_required', __('ID usuario requerido', WC_BAUM_GREENPAY_TEXTDOMAIN), 401);

			/* Search for duplicates */
			$tokens = $this->baum_greenpay_get_tokens_by_user_id(array('user_id' => $user_id));
			if ($tokens->data) {
				foreach ($tokens->data as $key => $_token) {
					if ($_token['token'] == $token) {
						throw new WC_REST_Exception('baum_greenpay_duplicates', __('Token ya existe', WC_BAUM_GREENPAY_TEXTDOMAIN), 201);
					}
				}
			}

			$cc_token = array(
				'expiration_date' => $expiry_year . $expiry_month,
				'user_id' => $user_id,
				'token' => $token,
				'brand' => $card_type,
				'last_digits' => $last4,
				'card_holder' => $card_holder
			);

			$wcbp = new WC_Baum_Greenpay();
			$response = $wcbp->baum_greenpay_save_token($cc_token, true);

			return rest_ensure_response($response);
		} catch ( WC_REST_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	** Set default or Delete token by token ID
	**/
	public function baum_greenpay_set_default_or_delete_token_by_id($request) {
		try {
			$token_id = $request['token_id'];
			$user_id = $request['user_id'];
			$action = $request['action'];

			$response = false;
			$token_exists_to_user = $this->baum_greenpay_search_token(array(
				'user_id' => $user_id,
				'token_id' => $token_id
			));

			if (!$token_exists_to_user) {
				throw new WC_REST_Exception('baum_greenpay_api_not_found', __('Token no existe o no está asociado a este usuario', WC_BAUM_GREENPAY_TEXTDOMAIN), 400);
			} else {
				if (is_null(WC_Payment_Tokens::get_token_type_by_id($token_id))) {
					throw new WC_REST_Exception('baum_greenpay_api_not_found', __('No es un tipo de token válido', WC_BAUM_GREENPAY_TEXTDOMAIN), 400);
				} else {
					$token = WC_Payment_Tokens::get($token_id);
					switch ($action) {
						case 'delete':
							$token->delete();
							$response = true;
						break;
						case 'set_default':
							$token->set_default(true);
							$token->update();
							$response = true;
						break;
						default:
							throw new WC_REST_Exception('baum_greenpay_api_not_found', __('Acción no válida', WC_BAUM_GREENPAY_TEXTDOMAIN), 400);
						break;
					}
				}

				return rest_ensure_response($response);
			}
		} catch ( WC_REST_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	** Generate tokenize order in GreenPay
	**/
	public function baum_greenpay_generate_tokenize_order_by_order_id($request) {
		try {
			$order_id = $request['order_id'];

			$wcbp = new WC_Baum_Greenpay();
			$response = $wcbp->baum_greenpay_generate_tokenize_order($order_id, true);

			if (isset($response['session'])) {
				return rest_ensure_response($response);
			} else {
				throw new WC_REST_Exception('baum_greenpay_api_not_found', __('No se ha podido generar la orden de tokenización en GreenPay', WC_BAUM_GREENPAY_TEXTDOMAIN), 400);
			}			
		} catch ( WC_REST_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	** Search token by User ID and Token ID
	**/
	private function baum_greenpay_search_token($data) {
		$response = false;
		$token = WC_Payment_Tokens::get_tokens(array(
			'user_id' => $data['user_id'],
			'token_id' => $data['token_id']
		));

		if ($token) {
			$response = $token;
		}

		return $response;
	}
}

/**
** Include endpoint class to WC Api Classes
**/
function baum_greenpay_include_rest_api( $wc_api_classes ) {

	if (class_exists('WC_Baum_Greenpay_RestAPI') && (!defined('WC_API_REQUEST_VERSION') || 3 == WC_API_REQUEST_VERSION)) {
		array_push($wc_api_classes, 'WC_Baum_Greenpay_RestAPI');
	}

	return $wc_api_classes;
}
add_filter('woocommerce_api_classes', 'baum_greenpay_include_rest_api');

/**
** Load the endpoint routes
**/
function baum_greenpay_register_routes() {
	global $wp_version;

	if ( version_compare( $wp_version, 4.4, '<' ) || ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, '2.6', '<' ) ) ) {
		return;
	}
	$controller = new WC_Baum_Greenpay_RestAPI();
	$controller->register_routes();
}
add_action('rest_api_init', 'baum_greenpay_register_routes', 20);
