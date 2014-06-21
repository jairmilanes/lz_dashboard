<script type="text/javascript">
$(function() {
  if( $('#<?php echo $options->getPlugin();?> .upload_button').length > 0 ){
	  upload_init({
		    parent: '#<?php echo $options->getPlugin();?>',
			upload_endpoint: 				'<?php echo osc_admin_ajax_hook_url('lzds');?>&do=uploads/upload&plugin=<?php echo $options->getPlugin();?>&field_name=',
			upload_delete_endpoint: 		'<?php echo osc_admin_ajax_hook_url('lzds');?>&do=uploads/delete&plugin=<?php echo $options->getPlugin();?>&field_name=',
			upload_load_endpoint: 			'<?php echo osc_admin_ajax_hook_url('lzds');?>&do=uploads/load&plugin=<?php echo $options->getPlugin();?>&field_name=',
			upload_init_delete_endpoint: 	'<?php echo osc_admin_ajax_hook_url('lzds');?>&do=uploads/delete&plugin=<?php echo $options->getPlugin();?>',
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