<a title="<?php echo $btn->title;?>"
	class="lz_tooltip <?php echo ( !isset($btn->class) ? 'secondary' : $btn->class);?> action"
	
	<?php if( isset($btn->do) ){ ?>
		data-do="<?php echo $btn->do;?>"
		href="<?php echo osc_admin_ajax_hook_url('lzds', array('&do' => $btn->do, 'plugin' => $plugin)); ?>"
	<?php } ?>
	
	<?php if( isset($btn->form) ){ ?>
		href="#"
		data-form="<?php echo $btn->form;?>"
	<?php } ?>
	
	<?php if( isset($btn->trigger) ){ ?>
		data-trigger="<?php echo $btn->trigger;?>"
	<?php } ?>
	
	data-method="<?php echo ( !isset($btn->method)? 'get' : $btn->method );?>"
	
	data-type="<?php echo ( !isset( $btn->type )? 'window' : $btn->type );?>"
	
	<?php if( isset($btn->confirm) ){ ?>
		data-confirm="<?php echo $btn->confirm;?>"
	<?php } ?>
	
	data-name="<?php echo $name;?>">
    		<?php // <span class="<?php echo $icon;"></span> ?>
    		<?php echo $btn->title;?>
</a>