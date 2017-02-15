<?php

function pwf_get_contact_form_html( $link, $type, $page ) {
	$prefix = '_pwf_';

	ob_start();
	?>
	
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 iconinput your-name">
			<input name="<?php echo $prefix; ?>name" id="<?php echo $prefix; ?>name" type="text" class="" placeholder="<?php echo __('Nome','pwf'); ?>">
		</div>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 iconinput your-email">
			<input name="<?php echo $prefix; ?>email" id="<?php echo $prefix; ?>email" type="text" class="" placeholder="<?php echo __('Email','pwf'); ?>">
		</div>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 iconinput your-message">
			<textarea name="<?php echo $prefix; ?>message" id="<?php echo $prefix; ?>message" class="" placeholder="<?php echo __('Messaggio','pwf'); ?>"></textarea>
		</div>

		<div class="col-xs-12 text-center styled field ">
			<label for="<?php echo $prefix; ?>privacy" class="control-label"> <?php echo __('Ho letto e accetto le', 'pwf'); ?> <a href="<?php echo  $link ; ?>" class="pwfpopup" data-type="html"><?php echo __('condizioni di trattamento dei dati personali', 'pwf'); ?></a> <?php echo __('di questo sito', 'pwf'); ?> </label>
			<div class="controls styledradio ">
				<input type="radio" name="<?php echo $prefix; ?>privacy" id="<?php echo $prefix; ?>privacy0" value="" checked="checked" class="form-control styled">
				<label for="<?php echo $prefix; ?>privacy0" id="<?php echo $prefix; ?>privacy0-lbl" class="radio"><i class="radio fa fa-times"></i> </label>
				<input type="radio" name="<?php echo $prefix; ?>privacy" id="<?php echo $prefix; ?>privacy1" value="1" class="form-control styled">
				<label for="<?php echo $prefix; ?>privacy1" id="<?php echo $prefix; ?>privacy1-lbl" class="radio"> <i class="radio fa fa-check"></i> </label>
			</div>
		</div>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right">
			<input type="submit" id="submit_contact" value="<?php echo __('Invia il messaggio','pwf'); ?>" class="wpcf7-form-control wpcf7-submit btn btn-default">
		</div>
		<input type="hidden" name="<?php echo $prefix; ?>type" value="<?php echo $type; ?>" />
		<input type="hidden" name="<?php echo $prefix; ?>page" value="<?php echo $page; ?>" />
	</div>


	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}