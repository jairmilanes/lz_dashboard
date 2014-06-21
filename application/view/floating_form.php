<?php
$data = View::newInstance()->_get('lz_data');
$options = $data->form;
?>
<div id="modal_<?php echo $options->getPlugin();?>" class="lz_dashboard_form">
	<div id="<?php echo $options->getPlugin();?>">
		<?php require LZ_DASHBOARD_VIEW_PATH.'parts/form_render_all.php';?>
	</div>
	<?php require LZ_DASHBOARD_VIEW_PATH.'parts/form_ajax_uploads_init.php';?>
</div>