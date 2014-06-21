<tr>
    <?php foreach( $headers as $slug => $title ){
    	$value =  osc_run_hook('', array('slug' => $slug, 'value' => @$row->$slug) );
    	?>
      	<td class="<?php echo $slug;?>"><?php echo ( !$value ? @$row->$slug : $value );?></td>
    <?php } ?>
    <?php
    if( !empty($buttons)){
	    foreach( (array)$buttons as $button => $info ){
		    	switch($button){
		    		case 'delete':
		    			$icon = 'icon-cancel';
		    			break;
		    		case 'update':
		    			$icon = 'icon-pencil';
		    			break;
		    		case 'view':
		    			$icon = 'icon-search';
		    			break;
		    		default:
		    			$icon = 'icon-help';
		    			break;
		    	}
		    	$info = (array)$info;
		    	?>
	    		<td class="action <?php echo $slug;?>">
	    			<a
	    				title="<?php echo $info['title'];?>"
	    				class="lz_tooltip"
	    				href="<?php echo osc_admin_ajax_hook_url('lzds', array('&do' => $info['do'], 'plugin' => $plugin, 'id' => $row->pk_i_id )); ?>"
	    				data-do="<?php echo $info['do'];?>"
	    				data-trigger="<?php echo $info['trigger'];?>"
	    				data-method="<?php echo $info['method'];?>"
	    				data-type="<?php echo $info['type'];?>"
	    				data-confirm="<?php echo $info['confirm'];?>"
	    				data-name="<?php echo $button;?>">
	    					<span class="<?php echo $icon;?>"></span>
	    			</a>
	    		</td>
	    <?php }
	 }?>
    
</tr>