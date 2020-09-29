<hr>
<h2><?= __('Settings', 'wc-prov-cant-dist') ?></h2>

<?php do_action('wcpcd_before_table_settings'); ?>

<table class="form-table">
	<tr>
		<th><?= __('Remove priority on the Checkout', 'wc-prov-cant-dist') ?></th>
		<td>
			<label>
				<input type="checkbox" name="wcpcd_priority_override" value="1" <?php checked($this->wcpcd_priority_override, 1); ?> /> <?= __('Yes', 'wc-prov-cant-dist') ?> <br/>
			</label>
			<p class="description"><?= __('It continues using the WooCommerce priority for the fields state, city, address_1, address_2.', 'wc-prov-cant-dist') ?></p>
		</td>
	</tr>
	<tr>
		<th><?= __('Hide postcode', 'wc-prov-cant-dist') ?></th>
		<td>
			<label>
				<input type="checkbox" name="wcpcd_hide_zipcode" value="1" <?php checked($this->wcpcd_hide_zipcode, 1); ?> /> <?= __('Yes', 'wc-prov-cant-dist') ?> <br/>
			</label>
			<p class="description"><?= __('It hides de postcode field on the Checkout and shipping calculator form.', 'wc-prov-cant-dist') ?></p>
		</td>
	</tr>
	<tr>
		<th><?= __('Debug JS', 'wc-prov-cant-dist') ?></th>
		<td>
			<label>
				<input type="checkbox" name="wcpcd_debug_js" value="1" <?php checked($this->wcpcd_debug_js, 1); ?> /> <?= __('Sí', 'wc-prov-cant-dist') ?> <br/>
			</label>
			<p class="description"><?= __('It prints the .js on production. Default prints .min.js.', 'wc-prov-cant-dist') ?></p>
		</td>
	</tr>
</table>

<?php do_action('wcpcd_after_table_settings'); ?>

<hr>
<h4><?= __('Testing JSON of Locations', 'wc-prov-cant-dist') ?></h4>
<p class="description"><?= __('Shows the current locations loading to the WC dropdowns.', 'wc-prov-cant-dist') ?></p>
<div class="testing-json-data" style="max-width: 100%; overflow: auto;">
	<pre><?php
	$wcpcd = new WC_PROV_CANT_DIST();
	echo json_encode($wcpcd->wcpcd_get_provincia_canton_distrito());
	?></pre>
</div>
<hr>
