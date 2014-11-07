<?php $forms =  $options->getForms();

  foreach(  $forms as $group => $form ){

	$hasDo = $form->hasDo();
	
	$hasTrigger = $form->hasTrigger();
	if( $hasDo ){ ?>
 	<form name="<?php echo $options->getPlugin().'-'.$group;?>" method="<?php echo $form->getMethod() ?>" action="<?php echo osc_admin_base_url(true);?>">
 		<input type="hidden" name="page" value="ajax" />
		<input type="hidden" name="action" value="runhook" />
		<input type="hidden" name="hook" value="lzds" />
		<?php if( $hasDo ){ ?>
			<input type="hidden" name="do" value="<?php echo $form->getDo(); ?>" />
		<?php } ?>
		<input type="hidden" name="plugin" value="<?php echo $options->getPlugin();?>" />
		<input type="hidden" name="name" value="<?php echo $data->form_name; ?>" />
		<input type="hidden" name="group" value="<?php echo $group; ?>" />
		<?php if( $hasTrigger ){ ?>
			<input type="hidden" name="trigger" value="<?php echo $form->getTrigger(); ?>" />
		<?php } ?>
	<?php } ?>

  		<?php if(!empty($form->subforms)){ ?>
            <?php foreach( $form->subforms as $name => $subform ){ ?>
                <div id="<?php echo $group.'_'.$name;?>" data-do="<?php echo $hasDo;?>">
                    <?php echo $subform->renderGroup(); ?>
                </div>
            <?php }	?>
		<?php } else { ?>
			<div id="<?php echo $group;?>" data-do="<?php echo $hasDo;?>">
				<?php echo $form->renderGroup(); ?>
			</div>
		<?php } ?>

	<?php if( $hasDo ){ ?>
		</form>
	<?php } ?>

<?php } ?>