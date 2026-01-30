<div class="wrap">
	<h2>Simple Like Page Plugin</h2>

	<form method="post" action="options.php">
		<?php settings_fields( 'sfp_options' ); ?>
		<?php do_settings_sections( 'sfp_plugin' ); ?>
		<?php submit_button( 'Save Changes' ); ?>
	</form>
</div>
