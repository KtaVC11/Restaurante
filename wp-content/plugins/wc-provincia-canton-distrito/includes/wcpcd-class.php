<?php
/**
** WPCD Class
**/
class WC_PROV_CANT_DIST {
	public function __construct() {
		// Check if WC is enable in the site
		if (!class_exists('WooCommerce')) return;

		$this->includes();

		$this->id = 'wcpcd';
		$this->name = 'wc-prov-cant-dist';
		$this->title = 'Custom states, cities and postcodes for WooCommerce.';
		$this->description = 'This plugin allows you to populate your custom states, cities, and postcodes for WooCommerce. It started working only for Costa Rica but now it is compatible with multi countries.';
		
		foreach ($this->wcpcd_fields() as $wcpcd_field) {
			$this->$wcpcd_field = get_option($wcpcd_field);
		}

		$this->json_data = $this->wcpcd_get_json_file();

		add_action('wp_enqueue_scripts', array($this, $this->id . '_scripts'));
		add_action('admin_enqueue_scripts', array($this, $this->id . '_scripts'));
		add_filter('woocommerce_states', array($this, $this->id . '_cr_states'), 20);

		add_filter('woocommerce_default_address_fields', array($this, $this->id . '_address_fields'), 20);

		add_action('admin_menu', array($this, $this->id . '_admin_page'));
		add_action('admin_init', array($this, $this->id . '_register_settings'));
		add_filter('plugin_action_links_' . WPCD_PLUGIN_FILE, array($this, $this->id . '_links'));

		add_action('wp_head', array($this, $this->id . '_hide_styles'));
	}

	public function includes() {
		include_once plugin_dir_path(__DIR__) . '/includes/wcpcd-admin.php';
	}

	/**
	** Plugin locations allowed
	**/
	private function wcpcd_locations_allowed() {
		return apply_filters('wcpcd_locations_allowed', is_cart() || is_checkout() || is_account_page() || is_admin());
	}

	public function wcpcd_get_json_file() {
		$json_file = apply_filters('wcpcd_prov_cant_dist_json', plugin_dir_url(__DIR__) . 'assets/js/prov-cant-dist.json');

		return $json_file;
	}

	public function wcpcd_scripts() {
		if ($this->wcpcd_locations_allowed()) {
			$min = '';
			if (!$this->wcpcd_debug_js && !preg_match('/localhost/', site_url())) {
				$min = '.min';
			}

			wp_enqueue_script($this->id . '-script', plugin_dir_url(__dir__) . 'assets/js/prov-cant-dist' . $min . '.js', array('jquery'), '1.1', true);

			// Declare it to use ajax
			wp_localize_script($this->id . '-script', 'wcpcd_ajax', array(
						'ajax_url' => admin_url('admin-ajax.php'),
						'city_first_option' => apply_filters('wcpcd_city_field_placeholder', __('Choose a city', 'wc-prov-cant-dist')),
						'json' => $this->wcpcd_get_provincia_canton_distrito()
					));
		}
	}

	/**
	** Populate CR States to Woocommerce states field
	**/
	public function wcpcd_get_provincias( $key = '' ) {
		$provincias = apply_filters('wcpcd_cr_states', array(
			'SJ' => 'San José',
			'AL' => 'Alajuela',
			'CG' => 'Cartago',
			'HD' => 'Heredia',
			'GT' => 'Guanacaste',
			'PT' => 'Puntarenas',
			'LM' => 'Limón'
		));

		if (!empty($key)) {
			$cont = 1;
			foreach ($provincias as $pv => $provincia) {
				if ($pv == $key)
					break;

				$cont++;
			}
			return $cont;
			
		}

		return $provincias;
	}

	public function wcpcd_cr_states( $states ) {
		$states['CR'] = $this->wcpcd_get_provincias();

		return $states;
	}

	/**
	** Get JSON provincias
	**/
	public function wcpcd_get_provincia_canton_distrito() {
		if ($this->wcpcd_file_exists($this->json_data)) {
			$string = $this->wcpcd_file_exists($this->json_data);
		}

		$json = json_decode($string, true);

		return apply_filters('wcpcd_get_provincia_canton_distrito', $json);
	}

	/**
	** Check whether file exists
	**/
	public function wcpcd_file_exists( $file ) {
		// wp_remote_fopen replaces old curl structure to get custom data from file
		$data = wp_remote_fopen($file);

		return $data;
	}

	/**
	** Manage address field in checkout page
	** Valid fixing WC 3.5 checkout fields order bug
	**/
	private function wcpcd_order_fields( $fields, $main_key = '' ) {
		$checkout_new_order = array();
		foreach ($fields as $key => $single_key) {
			$checkout_new_order[$key] = $fields[$key];
			if (preg_match('/country/', $key)) {
				$checkout_new_order[$main_key . 'state'] = $fields[$main_key . 'state'];
				$checkout_new_order[$main_key . 'city'] = $fields[$main_key . 'city'];
				$checkout_new_order[$main_key . 'address_1'] = $fields[$main_key . 'address_1'];
				$checkout_new_order[$main_key . 'address_2'] = $fields[$main_key . 'address_2'];
			}
		}

		return $checkout_new_order;
	}

	public function wcpcd_address_fields( $fields ) {
		$fields['state']['label'] = apply_filters('wcpcd_state_field_label', __('State', 'wc-prov-cant-dist'));
		$fields['city']['label'] = apply_filters('wcpcd_city_field_label', __('City-District', 'wc-prov-cant-dist'));
		$fields['city']['placeholder'] = apply_filters('wcpcd_city_field_placeholder', __('Choose a city', 'wc-prov-cant-dist'));
		$fields['city']['class'] = array('city_select', 'input-text');

		if (!$this->wcpcd_priority_override) {
			// Set priority 40+, after country field
			$fields['state']['priority'] = 42;
			$fields['city']['priority'] = 43;
			$fields['address_1']['priority'] = 44;
			$fields['address_2']['priority'] = 45;

			/* Fix WC 3.5 */
			$fields = $this->wcpcd_order_fields($fields);
		}

		if ($this->wcpcd_hide_zipcode) {
			$fields['postcode']['class'] = array('hide-zipcode');
		}

		return $fields;
	}

	/**
	** Settings page
	**/		
	private function wcpcd_fields() { 
		$wcpcd_fields = array('wcpcd_priority_override', 'wcpcd_hide_zipcode', 'wcpcd_debug_js');
		$custom_fields = apply_filters('wcpcd_register_custom_settings', array());

		if(!empty($custom_fields)) {
			$wcpcd_fields = array_merge($wcpcd_fields, $custom_fields);
		}

		return $wcpcd_fields;
	}

	public function wcpcd_admin_page() {
		add_options_page('WC Provincia-Canton-Distrito', 'WC Provincia-Canton-Distrito', 'manage_options', $this->id, array($this, $this->id . '_settings_page'));
	}

	public function wcpcd_register_settings() {
		foreach ($this->wcpcd_fields() as $wcpcd_field) {
			register_setting($this->id . '-plugin-settings', $wcpcd_field);
		}
	}

	public function wcpcd_settings_page() {
		?>
		<div class="wrap">
			<h1><?php echo $this->title; ?></h1>
			<p><?php echo $this->description; ?></p>
			<form action="options.php" id="wpcd-form" method="post">
				<?php
				settings_fields($this->id . '-plugin-settings');
				
				include_once plugin_dir_path(__DIR__) . '/includes/admin/wcpcd-settings.php';
				?>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	** Hides postcode field in shipping calculator
	**/
	public function wcpcd_hide_styles() {
		if ($this->wcpcd_hide_zipcode && $this->wcpcd_locations_allowed()) {
			?>
			<style type="text/css">
				.hide-zipcode,
				#calc_shipping_postcode_field {
					display: none !important;
				}
			</style>
			<?php
		}
	}

	/**
	** Add Settings action links
	**/
	public function wcpcd_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url('admin.php?page=' . $this->id) . '">' . __('Settings', 'wc-prov-cant-dist') . '</a>',
		);

		// Merge our new link with the default ones
		return array_merge($plugin_links, $links);    
	}

	/**
	** Validate deprecated method
	** Fix for v1.2.2
	**/
	public function wpcd_get_provincias( $key ) {
		return $this->wcpcd_get_provincias($key);
	}
}

