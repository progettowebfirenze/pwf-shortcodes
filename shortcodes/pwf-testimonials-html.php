<?php 
function pwf_get_testimonials_form_html($link) {
	$prefix = '_pwf_';
	ob_start();
	?>

<div class="form_testimonials"> 
  <!-- FORM -->
  
  <form id="testimonialsform" class="test_form" method="post"  enctype="multipart/form-data" data-locale="en_GB">
    <fieldset class="row">
    <legend><?php echo __('Leave your review','pwf'); ?></legend>
      <div class="col-sm-6">
        <label> <?php echo __('Name','pwf'); ?> </label>
        <input type="text" class="form-control" name="<?php echo $prefix; ?>name" id="<?php echo $prefix; ?>name" placeholder="">
      </div>
      <div class="col-sm-6">
        <label> <?php echo __('Title','pwf'); ?> </label>
        <input type="text" class="form-control" name="<?php echo $prefix; ?>title" id="<?php echo $prefix; ?>title" placeholder="">
      </div>
      <div class="col-sm-12">
        <label> <?php echo __('Give us your opinion','pwf'); ?> </label>
        <textarea class="form-control" name="<?php echo $prefix; ?>message" id="<?php echo $prefix; ?>message" rows="5" placeholder=""></textarea>
      </div>
      <div class="col-sm-12 styledupload">
        <input type="file" name="<?php echo $prefix; ?>image" id="fileupload" class="inputfile styledupload form_item" placeholder="">
        <label for="fileupload"><span><?php echo __('Your file','pwf'); ?></span> <strong>
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
            <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path>
          </svg>
          <?php echo __('Upload an image','pwf'); ?></strong> </label>
      </div>
      <div class="col-sm-12">
        <div class="text-center">
          <button type="submit" class="btn width-100" value="submit" id="btn_submit"><?php echo __('send your review','pwf'); ?></button>
        </div>
      </div>
    </fieldset>
  </form>
</div>
<?php
    $output = ob_get_contents();
    ob_end_clean();
    
    return $output;
	
}




