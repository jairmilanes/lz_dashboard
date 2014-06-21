<?php
$data = View::newInstance()->_get('lz_data');
$options = $data->form;

$plugin 		= $data->plugin;
$action_buttons = @$data->action_buttons;
$action_buttons = (array)$action_buttons;
?>
<div id="modal_<?php echo $options->getPlugin();?>" class="lz_dashboard_form">
	
	<div id="<?php echo $data->form_name;?>">
		<?php require LZ_DASHBOARD_VIEW_PATH.'parts/form_render_all.php';?>
	</div>
	
	<div id="<?php echo $data->form_name; ?>_hidden_action" class="dashboard_hidden_action"></div>
	
	<div id="<?php echo $data->form_name; ?>_action" class="dashboard_action">
		<?php
		if( is_array($action_buttons) ){
			foreach( $action_buttons as $name => $btn ){
				require LZ_DASHBOARD_VIEW_PATH.'parts/form_action_button.php';
		} }  // <a class="btn" data-prefix="<?php _e('Save ','lz_dashboard');?/>" href="#">Save</a>?>
		
	</div>
	<?php require LZ_DASHBOARD_VIEW_PATH.'parts/form_ajax_uploads_init.php';?>
</div>