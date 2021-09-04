<h1><?php esc_html_e('Welcome to ddBooling Plugin', 'ddbooking'); ?></h1>

<div class="content">
	<?php settings_errors(); ?>
	<form method="POST" action="options.php">
		<?php
			settings_fields('ddbooking_settings');
			do_settings_sections('ddbooking_settings');
			submit_button();
		?>
	</form>
</div>