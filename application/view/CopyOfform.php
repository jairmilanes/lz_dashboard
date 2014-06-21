<?php  
$data = View::newInstance()->_get('lz_data'); 

printR($data,true);




$fields = $data->form->getFields();
?>
<div id="<?php echo $data->form->getPlugin();?>">
	<div id="tabs">
	  <ul>
	  	<?php foreach(  $fields as $grandpa => $field ){ ?>
	   		<li><a href="<?php echo '#'.$grandpa;?>"><?php echo ucfirst( strtolower( $data->form->getGroupName( $grandpa ) ) ); ?></a></li>
	    <?php } ?>
	    <li class="submit">
	    	<a href="#saving">Save <span class="icon-ok"></span></a>
	    </li>
	  </ul>
	  <?php ?>
	  
	  
	  
	  
	  <?php echo $data->form->openForm();?>
		<input type="hidden" name="page" value="ajax" />
		<input type="hidden" name="action" value="runhook" />
		<input type="hidden" name="hook" value="lzds" />
		<input type="hidden" name="do" value="dashboard/save" />
		<input type="hidden" name="plugin" value="<?php echo $data->form->getPlugin(); ?>" />
		<input type="hidden" name="name" value="<?php echo $data->form_name; ?>" />
	  <?php foreach(  $fields as $grandpa => $field ){ ?>
		  <div id="<?php echo $grandpa;?>">
		  		<div class="form-horizontal">
		  			<?php $data->form->renderFields( $field, $grandpa ); ?>
		  		</div>
		  </div>
	  <?php } ?>
	  <div id="saving">
	  	  <h2>Saving form, please wait!</h2>
	  	  <img src="<?php echo LZ_DASHBOARD_ASSETS_URL.'img/loader32.gif'?>"/>
	  </div>
	  <?php echo $data->form->closeForm();?>
	</div>
</div>
<script type="text/javascript">
$(function() {
  if( $('#lz_dashboard .upload_button').length > 0 ){
	  upload_init({
		    parent: '#<?php echo $data->form->getPlugin();?>',
			upload_endpoint: 				'<?php echo osc_admin_ajax_hook_url('lzds');?>&do=uploads/upload&plugin=<?php echo $data->form->getPlugin();?>&field_name=',
			upload_delete_endpoint: 		'<?php echo osc_admin_ajax_hook_url('lzds');?>&do=uploads/delete&plugin=<?php echo $data->form->getPlugin();?>&field_name=',
			upload_load_endpoint: 			'<?php echo osc_admin_ajax_hook_url('lzds');?>&do=uploads/load&plugin=<?php echo $data->form->getPlugin();?>&field_name=',
			upload_init_delete_endpoint: 	'<?php echo osc_admin_ajax_hook_url('lzds');?>&do=uploads/delete&plugin=<?php echo $data->form->getPlugin();?>',
			upload_template: 				
				'<div class="qq-uploader">'+
		        	'<div class="qq-upload-drop-area" style="display: none;"><span>{dragZoneText}</span></div>'+
		        	'<div class="qq-upload-button"><button >{uploadButtonText}</button></div>'+
		        	'<span class="qq-drop-processing"  style="display: none;"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>'+
		        	'<ul class="qq-upload-list"></ul>'+
		    	'</div>',
	    	upload_file_template: 			
			    '<li>'+
			        '<div class="qq-progress-bar-container-selector">'+
			    		'<div class="qq-progress-bar-selector qq-progress-bar"></div>'+
						'<span class="qq-upload-status-text-selector qq-upload-status-text"></span>'+
			    	'</div>'+
			    	'<span class="qq-upload-spinner-selector qq-upload-spinner"></span>'+
			    	'<span class="qq-upload-file-selector qq-upload-file"></span>'+
			    	'<span class="qq-upload-size-selector qq-upload-size"></span>'+
			    	'<a class="qq-upload-cancel-selector qq-upload-cancel" style="display: none;" href="#">Cancel</a>'+
			    	'<a class="qq-upload-delete-selector qq-upload-delete" href="#"></a>'+
			    	'<div class="thumb"><img src="<?php echo osc_plugin_url('lz_theme_options/assets/img').'img/thumb-placeholder.png'?>" /></div>'+
			    '<li>',
		});
  }
});
</script>