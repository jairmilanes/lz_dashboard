<?php
$data = View::newInstance()->_get('lz_data');
$options = $data->form;
?>
<div id="<?php echo $options->getPlugin();?>">
	<div id="tabs">
	  <ul>
	  	<?php $menu = $options->getMenu();
	  	foreach( $menu as $group => $field ){ ?>
	   		<?php if( isset($field->submenus) ){ ?>
	   			<li data-group="<?php echo 'menu_'.$group; ?>" class="group_header open"><h3><a href="<?php echo '#'.$group;?>"><?php echo $field->title; ?><span class="icon-plus"></span></a></h3></li>
	   			<?php
	   			$i = 1;
	   			$count = count((array)$field->submenus);
	   			foreach( $field->submenus as $name => $submenu ){
					if( $i==1){ $class="first"; } else if( $i == $count ){ $class="last"; } else { $class = ""; };
					?>
					<li data-form="<?php echo $options->getPlugin().'-'.$group;?>" data-group="<?php echo 'menu_'.$group.'_item'; ?>" class="submenu <?php echo $class; ?>"><a href="<?php echo '#'.$group.'_'.$name;?>"><?php echo $submenu->title; ?><span class="icon-down-open"></span></a></li>
				<?php $i++;} ?>
	   		<?php } else { ?>
	   			<li data-form="<?php echo $options->getPlugin().'-'.$group;?>"><a href="<?php echo '#'.$group;?>"><?php echo $field->title; ?><span class="icon-right-open"></span></a></li>
	   		<?php } ?>
	   	<?php } ?>
	  </ul>
	  <?php require LZ_DASHBOARD_VIEW_PATH.'parts/form_render_all.php';?>
	</div>
</div>


	

