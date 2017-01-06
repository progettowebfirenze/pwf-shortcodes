<?php
function pwf_get_contact_form_html( $link ) {
	$prefix = '_pwf_';
	
	ob_start();
	?>
	
	<input name="<?php echo $prefix; ?>name" id="<?php echo $prefix; ?>name" type="text" placeholder="<?php echo __('Name','pwf'); ?>">
	<input name="<?php echo $prefix; ?>email" id="<?php echo $prefix; ?>email" type="text" placeholder="<?php echo __('Email','pwf'); ?>">
	<textarea name="<?php echo $prefix; ?>message" id="<?php echo $prefix; ?>message" placeholder="<?php echo __('Message','pwf'); ?>"></textarea>
	<div class="col-md-12 text-center styledfield ">
		<label for="<?php echo $prefix; ?>privacy" class="control-label"> ' .__('I have read and accept the', 'pwf'). ' <a href="' . $link . '" class="pwfpopup" data-type="html">' .__('term and condition', 'pwf'). '</a> ' .__('of this website', 'pwf'). ' </label>
		<div class="controls styledradio ">
			<input type="radio" name="<?php echo $prefix; ?>privacy" id="<?php echo $prefix; ?>privacy0" value="" checked="checked" class="form-control styled">
			<label for="<?php echo $prefix; ?>privacy0" id="<?php echo $prefix; ?>privacy0-lbl" class="radio"><i class="radio fa fa-times"></i> </label>
			<input type="radio" name="<?php echo $prefix; ?>privacy" id="<?php echo $prefix; ?>privacy1" value="1" class="form-control styled">
			<label for="<?php echo $prefix; ?>privacy1" id="<?php echo $prefix; ?>privacy1-lbl" class="radio"> <i class="radio fa fa-check"></i> </label>
		</div>
	</div>
	<input type="submit" id="submit_contact" value="<?php echo __('Submit','pwf'); ?>">
	
						
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
