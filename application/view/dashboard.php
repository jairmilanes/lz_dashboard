<?php $data = View::newInstance()->_get('lz_data'); ?>
<div id="lz_dashboard" class="grid-system">
	<div class="lz_tabs grid-row grid-first-row grid-100">
		<ul>
			<li><a href="<?php echo osc_route_admin_url('lz_payments/index');?>"><?php _e('Home','lz_payments');?></a></li>
			<?php $i =0; foreach( $data->plugins as $plugin ){ ?>
				<li class="<?php echo $plugin->shortname;?> box" data-plugin="<?php echo $plugin->shortname;?>">
					<a href="<?php echo osc_admin_ajax_hook_url('lz_dashboard_load_'.$plugin->shortname)?>"><?php echo $plugin->name?></a>
				</li>
			<?php } ?>
		</ul>
	</div>
	<div class="lz_inner grid-row grid-100">
		<div class="dashboard">
		 
		</div>
		<div class="lz_container grid-row grid-100"></div>
	
		<div class="lz_loading"><img src="<?php echo osc_plugin_url('lz_payments/assets/img').'img/loader32.gif'; ?>" /></div>
	</div>
</div>