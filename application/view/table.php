<?php
	$data = View::newInstance()->_get('lz_data');

	$listen = false;
	if( isset($data->listen)){
		$listen = $data->listen;
	}
	
	$group 		= @$data->group;
	$headers 	= $data->headers;
	$rows 		= $data->rows;
	$plugin 	= @$data->plugin;

	if( Params::existParam('listen')){
		$this->addData('listen', Params::getParam('listen'));
	}
	
	$cols 			= count((array)$headers);
	$buttons 		= @$data->buttons;
	$action_buttons = @$data->action_buttons;
	$action_buttons = (array)$action_buttons;
	$attributes 	= @$data->attributes;

	$cols = ( count((array)$headers) + count((array)$buttons) );
	
?>
<div id="<?php echo $data->name; ?>" class="<?php echo $group; ?> lz_dashboard_table">
	<table
		id="table_<?php echo $data->name; ?>"
		class="footable"
		data-name="<?php echo $data->name; ?>"
		data-filter-minimum="3"
		data-filter="<?php echo '#'.$data->name; ?>_hidden_action .table_filter .filter"
		data-page-navigation="<?php echo '#'.$data->name; ?>_table_action .pagination"
		data-page-size="30"
		data-sort-initial="acsending">
		  <thead>
			    <tr>
			    	<?php foreach( $headers as $slug => $title ){ ?>
				      <th <?php echo (isset($attributes->$slug)? $attributes->$slug : '' ); ?>><?php echo $title;?></th>
				    <?php }
				    
				    if( !empty($buttons)){
						foreach( $buttons as $button => $info ){ ?>
							<th <?php echo $button; ?>>&nbsp;</th>
					<?php }
					} ?>
			    </tr>
		  </thead>
		  <tbody>
		 	<?php
		 	$rows = !empty($rows)? (array)$rows : null;
		 	if( !empty($rows)){ ?>
		  		<?php foreach( $rows as $index => $row ){
				    require LZ_DASHBOARD_VIEW_PATH.'parts/table_row.php';
			    } ?>
			<?php } else {
				echo '<tr><td colspan="'.( $cols ).'"><p>'._m('No records found!','lz_payments').'</p></td></tr>';
			}?>
		  </tbody>
		  <tfoot>
		  		<tr>
					<td colspan="<?php echo $cols;?>">
						
					</td>
				</tr>
		  </tfoot>
	</table>
</div>
<div id="<?php echo $data->name; ?>_hidden_action" class="dashboard_hidden_action">
	<div class="table_filter">
		<input class="filter" placeholder="<?php _e('Type a text you want to filter by','lz_dashboard')?>" type="text" name="table_filter"/>
	</div>
</div>
<div id="<?php echo $data->name; ?>_table_action" class="dashboard_action">
	<div class="pagination hide-if-no-paging"></div>
	
	<?php
	if( is_array($action_buttons) ){
		foreach( $action_buttons as $name => $btn ){
			require LZ_DASHBOARD_VIEW_PATH.'parts/form_action_button.php';
	 	} } ?>
	<a href="#" class="secondary filter"><span class="icon-search"></span></a>
</div>
<script type="text/javascript">
	$(function () {
		
		var $table = $('#<?php echo $data->name; ?> .footable');
		$table.footable();

		if( $('#<?php echo $data->name; ?>_table_action').length > 0 ){

			$('a.filter', $('#<?php echo $data->name; ?>_table_action') ).on('click', function(e){
				e.preventDefault();
				if( $('#<?php echo $data->name; ?>_hidden_action').hasClass('active') ){
					$('#<?php echo $data->name; ?>_hidden_action').removeClass('active');
					$(this).removeClass('active');
				} else {
					$('#<?php echo $data->name; ?>_hidden_action').addClass('active');
					$(this).addClass('active');
				}
			});

		}
		
		$table.on('footable_redrawn', function(){
			table_init($('#<?php echo $data->name; ?>'));
		});
		<?php if( $listen ){ ?>
			$table.on('<?php echo $listen;?>', function(e, eventInfo){
				var url = '<?php echo osc_admin_ajax_hook_url('lzds', array('&do' => $listen, 'plugin' => $data->plugin, 'group' => 'plans_create' ));?>';
				$.get(url, function(data){
					if( data ){
						$('div.<?php echo $group;?> > table > tbody').html(data).trigger('footable_redraw');
					}
				});
			});
		<?php } ?>
	});
</script>