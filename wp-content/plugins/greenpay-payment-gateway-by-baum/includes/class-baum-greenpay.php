<?php
if (! defined('ABSPATH')) exit;

if (class_exists('WC_Payment_Gateway')) {
	include WC_BAUM_GREENPAY_PLUGIN_PATH . '/includes/greenpay-checkout-process.php';

	class WC_Baum_Greenpay extends WC_Payment_Gateway {
		public function __construct() {
			$this->logger = new WC_Logger();
			$this->wcbgp = new WC_Baum_Geenpay_Process();
			$this->id = WC_BAUM_GREENPAY_ID;
			$this->method_title = __('GreenPay Payment Method by Baum', WC_BAUM_GREENPAY_TEXTDOMAIN);
			$this->method_description = sprintf('%s Current version: %s', __('GreenPay Gateway configurations.', WC_BAUM_GREENPAY_TEXTDOMAIN), WC_BAUM_GREENPAY_VERSION);
			$this->has_fields = true;
			$this->supports = array(
				'products',
				'tokenization',
			);

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
			$this->get_icon();

			$this->title = __('GreenPay', WC_BAUM_GREENPAY_TEXTDOMAIN);
			$this->description = apply_filters('baum_greenpay_method_description', __('Pague con tarjeta de crédito o débito.', WC_BAUM_GREENPAY_TEXTDOMAIN));

			// Turn these settings into variables we can use
			foreach ( $this->settings as $setting_key => $value ) {
				$this->$setting_key = $value;
			}

			// GreenPay URLs
			$this->is_sandbox = ($this->payment_type == 'sandbox') ? true : false;
			$this->allow_save_cc = ($this->allow_save_cc == 'yes') ? true : false;
			$this->accepted_cc = apply_filters('baum_greenpay_accepted_cc', array('nd', 'visa', 'mastercard'));

			// Allow debug
			$this->test_mode = ($this->is_sandbox || (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)) ? true : false;

			// Actions
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
			add_action('wp_enqueue_scripts', array($this, 'baum_greenpay_payment_scripts'));
			add_action('wp_head', array($this, 'baum_greenpay_styles'));
			
			// Check for credentials
			add_action('admin_notices', array($this,  'baum_greenpay_check_credentials'));
		}

		public function baum_greenpay_check_credentials() {
			if($this->enabled == 'yes' && (empty($this->merchand_id) || empty($this->secret) || empty($this->public_key))) {
				?>
				<div class="notice notice-error">
					<p><?php echo sprintf( __('Las credenciales para <strong>%s</strong> están vacías. Ir a <a href="%s">configuraciones</a>.', WC_BAUM_GREENPAY_TEXTDOMAIN), $this->method_title, admin_url('admin.php?page=wc-settings&tab=checkout&section=baum_greenpay') ); ?></p>
				</div>
				<?php
			}
		}

	    public function payment_fields() {
			$baum_greenpay_cc = new WC_Payment_Gateway_CC();
			$baum_greenpay_cc->id = $this->id;
			$baum_greenpay_cc->supports = $this->supports;
			$description = $this->get_description();

			if ( $description ) {
				if($this->is_sandbox) 
					$description .= ' <strong>Test Mode</strong>';
				echo wpautop(wptexturize($description));
			}

			echo parent::get_icon();

			$tokens = parent::get_tokens();
			if (count($tokens) > 0)
				parent::saved_payment_methods();
			
			?>
			<div class="collapse <?= count($tokens) == 0 ? 'show' : '' ?>" id="collapse-cc-form">
				<?php do_action('bgp_before_cc_form_fields'); ?>

				<?php
					// This method will call the default CC fields from WC
					$baum_greenpay_cc->form();
				?>
				<p class="form-row form-row-wide">
					<label for="baum_greenpay-card-holder"><?= __('Titular de la tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN) ?> <span class="required">*</span></label>
					<input id="baum_greenpay-card-holder" name="baum_greenpay_card_holder" class="input-text" type="text" placeholder="John Doe">
				</p>

				<?php 
					if (is_user_logged_in() && $this->allow_save_cc) :
						$payment_method_page = is_wc_endpoint_url('add-payment-method');
				?>
					<label for="save-cc" class="checkbox" <?= ($payment_method_page) ? ' style="display: none;"' : '' ?> >
						<input type="checkbox" id="save-cc" name="save_cc" <?php checked($payment_method_page, true) ?> > <?= __('Guardar tarjeta?', WC_BAUM_GREENPAY_TEXTDOMAIN) ?>
					</label>
				<?php endif; ?>
				<input type="hidden" id="is-valid-cc" name="is_valid_cc">
				<input type="hidden" id="token_ld" name="token_ld">
				<input type="hidden" id="token_lk" name="token_lk">

				<?php do_action('bgp_after_cc_form_fields'); ?>
			</div>
			<?php
		}

		/**
		** Get saved payment method HTML from a token
		** Override from parent class
		**/
		public function get_saved_payment_method_option_html($token) {
			$add_payment_method = is_wc_endpoint_url('add-payment-method');
			$html = sprintf(
				'<li class="woocommerce-SavedPaymentMethods-token">
					<input id="wc-%1$s-payment-token-%2$s" type="radio" name="wc-%1$s-payment-token" value="%2$s" style="width:auto;" class="woocommerce-SavedPaymentMethods-tokenInput" %4$s />
					<label for="wc-%1$s-payment-token-%2$s">%5$s %3$s</label>
				</li>',
				esc_attr( $this->id ),
				esc_attr($token->get_id()),
				$this->baum_greenpay_get_payment_cc_display_name($token),
				($add_payment_method) ? 'disabled=disabled' : checked($token->is_default(), true, false),
				$this->baum_greenpay_get_payment_cc_icon($token->get_card_type())
			);

			return apply_filters( 'woocommerce_payment_gateway_get_saved_payment_method_option_html', $html, $token, $this );
		}

		/**
		** Init form fields
		**/
	    public function init_form_fields() {
	        $this->form_fields = require(WC_BAUM_GREENPAY_PLUGIN_PATH . '/includes/admin/greenpay-settings.php');
	    }

		/**
		** Baum Payment Gateway Scripts
		**/
		public function baum_greenpay_payment_scripts() {
			if (!is_cart() && !is_checkout() && !is_account_page())
				return;

			if ('no' === $this->enabled)
				return;

			$min = (preg_match('/localhost/', home_url()) || $this->test_mode) ? '' : '.min';

			wp_enqueue_script('aes', '//cdnjs.cloudflare.com/ajax/libs/aes-js/3.1.2/index' . $min . '.js');
			wp_enqueue_script('jsencrypt', '//cdnjs.cloudflare.com/ajax/libs/jsencrypt/2.3.1/jsencrypt' . $min . '.js');
			wp_enqueue_script('helper_baum', WC_BAUM_GREENPAY_PLUGIN_URL . '/assets/js/helper_baum.js', array('aes', 'jsencrypt'));
			wp_enqueue_script('baum_greenpay', WC_BAUM_GREENPAY_PLUGIN_URL . '/assets/js/bgp_script' . $min . '.js', array('jquery', 'helper_baum'));

			wp_localize_script('baum_greenpay', 'bgp_params', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'get_card_token' => $this->pay_order,
				'get_request_token' => $this->create_order,
				'get_public_key' => json_encode($this->public_key)
			));
		}

		/**
		** Validate payment fields on the frontend.
		**/
		public function validate_fields() {
			$new_card = (isset($_POST['wc-baum_greenpay-payment-token']) && $_POST['wc-baum_greenpay-payment-token'] == 'new') ? true : false;
			// $use_token = (isset($_POST['wc-baum_greenpay-payment-token']) && $_POST['wc-baum_greenpay-payment-token'] != 'new') ? true : false;
			$is_payment_method = ($_POST['payment_method'] == $this->id && !isset($_POST['wc-baum_greenpay-payment-token'])) ? true : false; // Not necessary

			if ( $new_card ) {
				if ( empty($_POST['is_valid_cc']) || ($is_payment_method && empty($_POST['is_valid_cc'])) ) {
					wc_add_notice(__('Revise los datos de su tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN), 'error');
					return;
				}

				if (!isset($_POST['baum_greenpay_card_holder']) || strlen($_POST['baum_greenpay_card_holder']) < 5) {
					wc_add_notice(__('Titular de tarjeta requerido. Extensión mínima 5 caracteres.', WC_BAUM_GREENPAY_TEXTDOMAIN), 'error');
					return;
				}
			}

			return true;
		}

		/**
		** Process the order payment
		**/
		public function process_payment($order_id) {
			// $order = new WC_Order($order_id);
			$order = wc_get_order($order_id);
			$this->baum_greenpay_start_session('order_id', trim(str_replace('#', '', $order->get_order_number())));
			$session_order_id = WC()->session->get('order_id');

			if (isset($_POST['wc-baum_greenpay-payment-token']) && $_POST['wc-baum_greenpay-payment-token'] != 'new') {
				$token_id = (int) $_POST['wc-baum_greenpay-payment-token'];
	            $token = WC_Payment_Tokens::get($token_id);

	            //Verify token belongs to the logged in user
	            if ($token->get_user_id() !== get_current_user_id()) {
	                wc_add_notice(__('Error de pago: Usuario no está asociado a token de tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN), 'error');
	                return;
	            }

	            $payment_processed = $this->baum_greenpay_process_payment_with_token($session_order_id, $token->get_token());
			} else {
				$response = $this->baum_greenpay_generate_token_card($session_order_id);

				if ($response && !is_null($response['token']['token'])) {
					if ($session_order_id != $response['token']['requestId']) {
						$session_order_id = $response['token']['requestId'];
					}
					$payment_processed = $this->baum_greenpay_process_payment_with_token($session_order_id, $response['token']);
				}

				if (!$response['response']) {
					$this->bgp_log('Adding payment method: ' . json_encode($response));
					$payment_response = $response;
				}
			}

			// Check for payment failure
			if ($payment_processed['result'] == 'failure') {
				$payment_response = $payment_processed;
				$message = $payment_processed['message'];
				wc_add_notice($payment_processed['message'], 'error');
			}
				

			if (isset($payment_response)) {
				$this->bgp_log('Payment unavailable: ' . json_encode($payment_response));
				return array(
					'result' => 'failure'
				);
			}

			if ($this->test_mode) {
				$this->bgp_log('Payment success: ' . json_encode($response));
			}

			return array(
				'result' => 'success',
				'redirect' => $this->get_return_url($order)
			);
		}

		/**
		** Processing order with CC token
		** param $token => array o string
		**/
		public function baum_greenpay_process_payment_with_token($order_id, $token, $from_endpoint = false) {
			global $wpdb;
			$order = wc_get_order($order_id);
			$response = array(
				'result' => 'success'
			);

			// Check for order status to pay from endpoint
			if ($order->has_status(array('processing', 'cancelled'))) {
				return array(
					'result' => 'failure',
					'message' => sprintf(__('Orden #%d no puede ser pagada. Status: %s', WC_BAUM_GREENPAY_TEXTDOMAIN), $order_id, $order->get_status())
				);
			}

			if ($order->get_total() > 0) {
				$nonce = $this->baum_greenpay_create_nonce('checkout', $order_id);
				$products = $this->baum_greenpay_get_additional_order_data($order);
				$payload = $this->baum_greenpay_create_payment_payload($order, $products);
				$payload['token'] = (is_array($token)) ?  $token['token'] : $token;
				unset($payload['callback']);
				$checkout_with_token = $this->baum_greenpay_post($this->pay_with_token, $payload, $nonce);
			}

			/* BEGIN Insert into database */
			$table_bitacora = $wpdb->prefix . 'bgp_transacciones';
			$data = array(
				'persona' => (is_user_logged_in()) ? get_current_user_id() : 0,
				'monto' => $order->get_total(),
				'orden' => $order_id,
				'metodo_pago' => ($from_endpoint) ? 'endpoint' : $this->id,
				'authcode' => isset($checkout_with_token['authorization']) ? $checkout_with_token['authorization'] : '',
				'transactionid' => isset($checkout_with_token['result']['retrieval_ref_num']) ? $checkout_with_token['result']['retrieval_ref_num'] : '',
				'estado' => isset($checkout_with_token['status']) ? $checkout_with_token['status'] : 201,
				'fecha_creacion' => date('Y-m-d H:i:s')
			);

			if ($checkout_with_token['status'] != 200 && $order->get_total() > 0) {
				$data['error'] = json_encode(array(
					'status' => $checkout_with_token['status'],
					'resp_code' => $checkout_with_token['result']['resp_code'],
					'message' => $checkout_with_token['result']['reserved_private4'],
					'custom_message' => $this->wcbgp->responses[$checkout_with_token['result']['resp_code']],
					'error' => json_encode($checkout_with_token['errors'])
				));
			}
			$wpdb->insert( $table_bitacora, $data );
			$wpdb->flush();
			/* END Insert into database */

			if ($from_endpoint && $order->get_total() == 0) {
				$response['order_note'] = __( 'Transacción Procesada sin pago.', WC_BAUM_GREENPAY_TEXTDOMAIN);
				$response['order_id'] = $order->get_id();
				$this->bgp_log('Orden sin GreenPay: ' . json_encode($response));

				$order->update_status('wc-processing', $response['order_note']);

				return $response;
			}

			$checkout_with_token['order_status'] = $order->get_status();

			if ($this->test_mode)
				$this->bgp_log('GreenPay Response: ' . json_encode($checkout_with_token));

			if ($checkout_with_token['status'] == 200) {
				$order->payment_complete($checkout_with_token['result']['retrieval_ref_num']); // Not working in account page
				
				$order->add_order_note(sprintf(
					"<strong>%s:</strong> %s \n<strong>%s:</strong> %s \n<strong>%s:</strong> %s \n<strong>%s:</strong> %s",
					__('Ref. del Banco', WC_BAUM_GREENPAY_TEXTDOMAIN), $checkout_with_token['result']['retrieval_ref_num'],
					__('N° Autorización', WC_BAUM_GREENPAY_TEXTDOMAIN), $checkout_with_token['authorization'],
					__('Tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN), $checkout_with_token['brand'],
					__('Últimos dígitos', WC_BAUM_GREENPAY_TEXTDOMAIN), $checkout_with_token['last4']
				));
				update_post_meta($order_id, $this->id . '_autorization', $checkout_with_token['authorization']);
			} else {
				$checkout_with_token['order_status'] = 'wc-failed';
				$resp_code = 'ND1';
				if (is_null($checkout_with_token['result'])) {
					$checkout_with_token['resp_code'] = $resp_code;
					$message = $this->wcbgp->responses[$resp_code];
				} else {
					$resp_code = $checkout_with_token['result']['resp_code'];
					$gp_error_message = (!empty($this->wcbgp->responses[$resp_code])) ? $this->wcbgp->responses[$resp_code] : $this->wcbgp->responses['ND2'];
					$message = __('Error de pago: ', WC_BAUM_GREENPAY_TEXTDOMAIN) . $gp_error_message;
				}

				$order->add_order_note(sprintf(
					"<strong>%s:</strong> %s \n%s",
					__('Cod. Respuesta', WC_BAUM_GREENPAY_TEXTDOMAIN), $resp_code,
					$message
				));

				$response['result'] = 'failure';
				$response['message'] = $message;
				$response['order_id'] = $order->get_id();
				$this->bgp_log('GreenPay Error: ' . json_encode($checkout_with_token));

				$order->update_status($checkout_with_token['order_status'], __( 'Transacción Cancelada.', WC_BAUM_GREENPAY_TEXTDOMAIN));
			}

			if ($from_endpoint) {
				$response['checkout_with_token'] = $checkout_with_token;
			}

			return $response;
		}

		/**
		** Generate a Tokenize order in GreenPay
		**/
		public function baum_greenpay_generate_tokenize_order($session_order_id, $from_endpoint = false) {
			$nonce = $this->baum_greenpay_create_nonce('tokenize', $session_order_id);
			$payload = $this->baum_greenpay_create_token_payload(false, $session_order_id);
			$tokenize_order = $this->baum_greenpay_post($this->get_request_token, $payload, $nonce);

			if (isset($tokenize_order['session']) && $from_endpoint) {
				$tokenize_order['public_key'] = $this->public_key;
			}

			return $tokenize_order;
		}

		/**
		** Generate CC token
		**/
		public function baum_greenpay_generate_token_card($session_order_id) {
			global $wpdb;
			$response = $log = array();
			$session_order_id = (string) $session_order_id;
			$tokenize_order = $this->baum_greenpay_generate_tokenize_order($session_order_id);

			$log[] = json_encode($tokenize_order);

			if (isset($tokenize_order['session'])) {
				$token_ld = $_POST['token_ld'];
				$token_lk = $_POST['token_lk'];
				$save_cc = ($_POST['save_cc'] == 'on') ? true : false;
				$save_cc = (isset($_POST['woocommerce_add_payment_method']) && $_POST['woocommerce_add_payment_method'] == '1') ? true : $save_cc;
				$invocation_nonce = $this->baum_greenpay_create_nonce('tokenize', $session_order_id);
				$tokenize_invocation = $this->baum_greenpay_post(
					$this->get_card_token,
					array(
						'session' => $tokenize_order['session'],
						'ld' => $token_ld,
						'lk' => $token_lk 
					),
					$invocation_nonce,
					array(
						'liszt-token' => $tokenize_order['token']
					)
				);
				$log[] = json_encode(array(
					'session' => $tokenize_order['session'],
					'ld' => $token_ld,
					'lk' => $token_lk,
					'liszt-token' => $tokenize_order['token'],
					'invocation_nonce' => $invocation_nonce,
					'tokenize_invocation' => $tokenize_invocation,
				));

				if (preg_match('/20/', $tokenize_invocation['status'])) {
					/* BEGIN Insert into database */
					$table_tokens = $wpdb->prefix . 'bgp_tokens';
					$data = array(
						'user_id' => (is_user_logged_in()) ? get_current_user_id() : 0,
						'token' => (isset($tokenize_invocation['result'])) ? $tokenize_invocation['result']['token'] : '',
						'description' => $tokenize_invocation['status'],
						'estado' => sprintf('%s: %s', ($save_cc && is_user_logged_in()) ? __('Tarjeta guardada', WC_BAUM_GREENPAY_TEXTDOMAIN) : __('Tarjeta usada en compra', WC_BAUM_GREENPAY_TEXTDOMAIN), $tokenize_invocation['result']['last_digits']),
						'fecha_creacion' => date('Y-m-d H:i:s')
					);
					$wpdb->insert( $table_tokens, $data );
					$wpdb->flush();
					/* END Insert into database */

					// $tokenize_invocation['errors']
					$the_token = array(
						'status' => $tokenize_invocation['status'],
						'requestId' => $tokenize_invocation['requestId'],
						'token' => $tokenize_invocation['result']['token'],
						'last_digits' => $tokenize_invocation['result']['last_digits'],
						'bin' => $tokenize_invocation['result']['bin'],
						'khash' => $tokenize_invocation['result']['khash'],
						'expiration_date' => $tokenize_invocation['expiration_date'],
						'brand' => $tokenize_invocation['brand'],
						'nickname' => $tokenize_invocation['nickname'],
						'cardHolder' => $tokenize_invocation['cardHolder'],
						'signature' => $tokenize_invocation['_signature'],
						'save_cc' => $save_cc
					);

					// Validate accepted CC
					$is_accepted_cc = $this->baum_greenpay_validate_cc_brand($the_token['brand']);
					$response['response'] = $is_accepted_cc['response'];
					if ($is_accepted_cc['response']) {
						// $response['response'] = true;
						$response['token'] = $the_token;
						if ($save_cc && is_user_logged_in()) {
							$response['token_saved'] = $this->baum_greenpay_save_token($the_token);
						}
					} else {
						// $response['response'] = false;
						wc_add_notice($is_accepted_cc['error'], 'error');
					}

				} else {
					$this->bgp_log('Error tokenizando tarjeta: ' . json_encode($log));
					$response['response'] = false;
					wc_add_notice(__('Error tokenizando tarjeta. Revise los detalles de su tarjeta.', WC_BAUM_GREENPAY_TEXTDOMAIN), 'error');
				}
			} else {
				wc_add_notice(__('Error creando orden de tokenización. Intente luego.', WC_BAUM_GREENPAY_TEXTDOMAIN), 'error');
			}

			$response['log'] = $log;

			return $response;
		}

		public function baum_greenpay_validate_cc_brand($cc_brand) {
			if (in_array(strtolower($cc_brand), $this->accepted_cc)) {
				return array('response' => true);
			} else {
				return array(
					'response' => false,
					'error' => sprintf(__('Marca de tarjeta no soportada por el procesador de pago: %s', WC_BAUM_GREENPAY_TEXTDOMAIN), $cc_brand)
				);
			}
		}

		/**
		** Save CC token
		**/
		public function baum_greenpay_save_token($cc_token, $from_endpoint = false) {
			$expiration_month = substr($cc_token['expiration_date'], 2, 4);
			$expiration_year = substr($cc_token['expiration_date'], 0, 2);
			$user_id = isset($cc_token['user_id']) ? $cc_token['user_id'] : get_current_user_id();
			$card_holder = (!empty($cc_token['card_holder'])) ? $cc_token['card_holder'] : '';
			$response = array(
				'result' => 'success'
			);

			// Validate cc
			$is_accepted_cc = $this->baum_greenpay_validate_cc_brand($cc_token['brand']);
			if (!$is_accepted_cc['response']) {
				return array(
					'result' => 'failure',
					'message' => $is_accepted_cc['error']
				);
			}

			$token = new WC_Payment_Token_CC();
			$token->set_token( $cc_token['token'] );
			$token->set_gateway_id( $this->id );
			$token->set_card_type( strtolower($cc_token['brand']) );
			$token->set_last4( $cc_token['last_digits'] );
			$token->set_expiry_month( $expiration_month );
			$token->set_expiry_year( '20' . $expiration_year );
			$token->set_user_id( $user_id );

			if (isset($_POST['baum_greenpay_card_holder'])) 
				$card_holder = $_POST['baum_greenpay_card_holder'];

			if (!empty($card_holder))
				$token->add_meta_data('card_holder', $card_holder, true);

			if ($from_endpoint)
				$response['message'] = __('Token guardado con éxito', WC_BAUM_GREENPAY_TEXTDOMAIN);

			if (!$token->save()) {
				$message = __('Hubo un problema al guardar su tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN);
				wc_add_notice( $message, 'error');
				$response['result'] = 'failure';

				if ($from_endpoint)
					$response['message'] = $message;
			}

			return $response;
		}

		/**
		** Override from parent class
		**/
		public function add_payment_method() {
			if (is_checkout())
				return;

			if (!is_user_logged_in()) {
				wc_add_notice( __('No es posible guardar la tarjeta porque el usuario no está logueado.', WC_BAUM_GREENPAY_TEXTDOMAIN), 'error' );
				return;
			}

			$response = $this->baum_greenpay_generate_token_card(mt_rand(1000, 9999));
			if (!$response['response']) {
				$this->bgp_log('Adding payment method: ' . json_encode($response));
			}

			if ($response['response']) {
				return array(
					'result'   => 'success',
					'redirect' => wc_get_endpoint_url('payment-methods'),
				);
			} else {
				$message = (isset($response['token_saved']['message'])) ? $response['token_saved']['message'] : __('Hubo un error guardado los datos de la tarjeta.', WC_BAUM_GREENPAY_TEXTDOMAIN);
				wc_add_notice( __( $message, 'woocommerce' ), 'error' );
				return;
			}
		}

		/**
		** Get CC payment icon
		**/
		public function baum_greenpay_get_payment_cc_icon($icon = '') {
			switch ($icon) {
				case 'visa':
					$icon_tag  = '<img src="' . $this->baum_greenpay_get_cc_icon_image('visa') . '" alt="Visa" />';
				break;
				case 'mastercard':
					$icon_tag = '<img src="' . $this->baum_greenpay_get_cc_icon_image('mastercard') . '" alt="MasterCard" />';
				break;
				
				default:
					# code...
				break;
			}

			return apply_filters('baum_greenpay_cc_icon_tag', $icon_tag, $icon);
		}

		/**
		** Custom styles
		**/
		public function baum_greenpay_styles() {
			if (!is_cart() && !is_checkout() && !is_account_page() && !is_order_received_page()) //  && ! isset( $_GET['pay_for_order'] )
				return;

			if ('no' === $this->enabled)
				return;
			?>
			<style type="text/css">
				.wc-credit-card-form-card-number {
					background-position: center right;
					background-repeat: no-repeat;
					background-size: 45px;
				}

				.wc-credit-card-form-card-number.visa {
					background-image: url(<?= $this->baum_greenpay_get_cc_icon_image('visa') ?>);
				}
				
				.wc-credit-card-form-card-number.mastercard {
					background-image: url(<?= $this->baum_greenpay_get_cc_icon_image('mastercard') ?>);
				}

				.baum-greenpay-order-actions {
					display: block;
					margin: auto;
					text-align: center;
					background: #f8f8f8;
					padding: 15px;
				}

				.baum-greenpay-order-actions .order-actions .button {
					margin: 7px 5px 0 5px;
				}

				.payment_method_baum_greenpay .form-row.woocommerce-invalid .input-text {
					border-color: red;
				}
			</style>
			<?php
		}

		private function baum_greenpay_get_cc_icon_image($icon) {
			switch ($icon) {
				case 'visa':
					$icon_image  = WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/visa.png' );
				break;
				case 'mastercard':
					$icon_image = WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/mastercard.png' );
				break;
				
				default:
					# code...
				break;
			}

			return apply_filters('baum_greenpay_cc_icon_image', $icon_image, $icon);
		}

		public function baum_greenpay_admin_styles() {
			?>
			<style type="text/css">
				.form-table.greenpay-urls input.input-text:not(.active) {
					opacity: 0.5;
					pointer-events: none;
				}
			</style>
			<?php
		}

		/**
		** Format the CC display name
		**/
		public function baum_greenpay_get_payment_cc_display_name($token) {
			$display = sprintf(
				/* translators: 1: credit card type 2: last 4 digits 3: expiry month 4: expiry year */
				__( '%1$s ending in %2$s', 'woocommerce' ),
				'<strong>' . wc_get_credit_card_type_label($token->get_card_type()) . '</strong>',
				$token->get_last4(),
				$token->get_expiry_month()
			);
			return $display;
		}

		/**
		** Logger
		**/
		public function bgp_log($message) {
			$this->logger->add($this->id, $message . "\n");
		}

		/**
		** WC Session & Nonce
		**/
		private function baum_greenpay_start_session($param, $data) {
			if (!is_admin()) {
				WC()->session->set($param, $data);
			}
		}

		public function baum_greenpay_create_nonce($action, $order_id) {
			$nonce = wp_create_nonce($order_id);
			$this->baum_greenpay_start_session('nonce', $nonce);
			$res = array(
				'action' => $action,
				'nonce' => $nonce
			);

			return $res;
		}

		/**
		** Create Token & Payment payload
		**/
		public function baum_greenpay_create_token_payload($update, $order_id) {
			if ($update) {
				$callback = add_query_arg('x_uresult', '', wc_get_page_permalink('myaccount'));
			} else {
				$callback = add_query_arg('x_result', '', wc_get_checkout_url());
			}

			$payload = array(
				'secret'     => $this->secret,
				'merchantId' => $this->merchand_id,
				'callback'   => $callback,
				'requestId'  => $order_id
			);

			return $payload;
		}

		public function baum_greenpay_create_payment_payload($order, $data) {
			$description = 'Compra orden '.trim(str_replace('#', '', $order->get_order_number())) .' - '.$data['customer']['name'];
			$callback = add_query_arg('x_result', '', wc_get_checkout_url());

			$payload = array(
				'secret'         => $this->secret,
				'merchantId'     => $this->merchand_id,
				'terminal'       => $this->terminal_id,
				'currency'       => $this->currency,
				'description'   => $description,
				'orderReference' => trim(str_replace('#', '', $order->get_order_number())),
				'callback'		 => $callback,
				'amount'         => (double) $order->get_total(),
				'additional'     => $data
			);

			return $payload;
		}

		/**
		** Get Product details & Order data
		**/
		private function baum_greenpay_get_product_details($order) {
			$products = array();
			foreach ($order->get_items() as $item) {
				$product = wc_get_product($item['product_id']);

				if($product->get_type() === 'variable-subscription'){
					$object['price']       = floatval(wc_get_product($item->get_variation_id())->get_price());
				} else {
					$object['price']       = floatval($product->get_price());
				}

				$object['description'] = $product->get_name();
				$object['skuId']       = strval($product->get_id());
				$object['quantity']    = intval($item['qty']);
				$object['type']        = $product->get_type();
				array_push($products, $object);
			}
			return $products;
		}

		public function baum_greenpay_get_additional_order_data($order) {
			$base_location = wc_get_base_location();
			$base_country = $base_location['country'];
			$customer_data = array(
				'name' => $order->get_formatted_billing_full_name(),
				'email' => $order->get_billing_email(),
				'billingAddress' => array(
					'country' => (!empty($billing_country = $order->get_billing_country())) ? $billing_country : $base_country,
					'province' => $order->get_billing_state(),
					'city' => $order->get_billing_city(),
					'street1' => $order->get_billing_address_1(),
					'street2' => $order->get_billing_address_2(),
					'zip' => $order->get_billing_postcode(),
					'phone' => $order->get_billing_phone()
				)
			);

			if(empty($order->get_shipping_country())){
				$customer_data['shippingAddress'] = $customer_data['billingAddress'];
			} else {
				$customer_data['shippingAddress'] = array(
					'country' => (!empty($shipping_country = $order->get_shipping_country())) ? $shipping_country : $base_country,
					'province' => $order->get_shipping_state(),
					'city' => $order->get_shipping_city(),
					'street1' => $order->get_shipping_address_1(),
					'street2' => $order->get_shipping_address_2(),
					'zip' => $order->get_shipping_postcode(),
					'phone' => ''
				);

			}

			return array(
				'customer' => $customer_data,
				'products' => $this->baum_greenpay_get_product_details($order)
			);
		}

		/**
		** Greenpay post
		**/
		public function baum_greenpay_post($url, $payload, $nonce_args, $headers = array()) {
			$action = $nonce_args['action'];
			$nonce = $nonce_args['nonce'];
			$response = array();
			$headers = wp_parse_args($headers, array(
				'Content-Type' => 'application/json; charset=utf-8'
			));
			if (($action == 'tokenize' || $action == 'checkout' || $action == 'update_card') && ($nonce == WC()->session->get('nonce'))) {
				$args = array(
					'headers'     => $headers,
					'timeout'     => 180,
					'body'        => json_encode($payload),
					'method'      => 'POST',
					'data_format' => 'body',
				);
				$response = wp_safe_remote_post($url, $args);
			}
			if (is_wp_error($response) || empty($response['body'])) {
				$this->bgp_log(
					"\n".'There was a problem connecting to the Greenpay API endpoint.'. 'Error Response: ' ."\n". print_r($response, true) . "\n".'Failed request: '."\n" . print_r(
						array(
							'api' => $url,
							'headers' => $headers,
							'request' => $payload,
							'nonce' => array_merge($nonce_args, array(
								'wc_nonce' => WC()->session->get('nonce')
							)),
						),
						true
					)
				);
			}
			
			return json_decode($response['body'], true);
		}
	}
}