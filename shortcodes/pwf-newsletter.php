<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('init', 'pwf_create_post_type_newsletter');
function pwf_create_post_type_newsletter() 
{
    register_post_type('newsletter',
        array(
            'labels' => array(
                'name' => __('Newsletter', 'pwf'),
                'singular_name' => __('Newsletter', 'pwf')
            ),
            'public' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'rewrite' => array('slug' => 'newsletter','with_front' => false),
            'supports' => array('title'),
			
        )
    );
}

add_filter( 'cmb2_admin_init', 'newsletter_metaboxes' );
function newsletter_metaboxes() 
{
	
	$prefix = '_pwf_';
	
	$newsletter_metabox = new_cmb2_box( array(
		'id'            => $prefix . 'newsletter_data',
		'title'         => __( 'Registrant data', 'pwf-admin' ),
		'object_types'  => array( 'newsletter', ), // Post type'title'     
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
	) );
	
	$newsletter_metabox->add_field(array(
		'name'       => __( 'Nome', 'pwf-admin' ),
		'id'         => $prefix . 'name',
		'type'       => 'text',
	));
	
	$newsletter_metabox->add_field(array(
		'name'       => __( 'Email', 'pwf-admin' ),
		'id'         => $prefix . 'email',
		'type'       => 'text',
	));
	
	$newsletter_metabox->add_field(array(
		'name'       => __( 'Date', 'pwf-admin' ),
		'id'         => $prefix . 'date',
		'type'       => 'text_datetime_timestamp',
	));
}

add_action('wp_enqueue_scripts', 'newsletter_enqueuescripts');
function newsletter_enqueuescripts()
{
	wp_enqueue_style( 'pwf', PWFURL.'/pwf-plugin/assets/css/newsletter.css', array(), 1.0 );
	wp_enqueue_script('pwf-newsletter', PWFURL.'/pwf-plugin/assets/js/newsletter.js', array(JQUERY_NAME), 1.0, true);
	wp_localize_script( 'pwf-newsletter', 'newsletterajax', admin_url('admin-ajax.php') );
}

add_shortcode( 'pwf_newsletter', 'pwf_newsletter_func' );
function pwf_newsletter_func() 
{
	ob_start();
	?>
	
	<form class="newsletter-input" method="post" action="" name="newsletterform" id="newsletter-form" enctype="text/plain">
		<input type="text" name="name" id="name" placeholder="<?php echo __('Your Name','pwf'); ?>" class="contact-form"/>
		<input type="email" name="email" id="email" placeholder="<?php echo __('Your Email','pwf'); ?>" class="contact-form"/>
		<button class="btn btn-transparent white"><?php echo __('send message','pwf'); ?></button>
		<div class="response"></div>
	</form>
   
   <?php
	$output = ob_get_contents();
	ob_end_clean();

   return $output;
}

add_action( 'wp_ajax_nopriv_pwf_process_newsletter', 'pwf_process_newsletter' );
add_action( 'wp_ajax_pwf_process_newsletter', 'pwf_process_newsletter' );
function pwf_process_newsletter()
{
	$results = '';
	$error = 0;
	$err_mess = array();
	$prefix = '_pwf_';
	
	$name 	= pwf_cleanup($_POST['name']);
	$email 	= pwf_cleanup($_POST['email']);
	
	$admin_email = get_option('admin_email');
	
	$postname = ucwords($name);
	
	$postData->title = $postname;
	$postData->meta = new stdClass();
	$postData->meta->{$prefix .'name'} = $postname;
	$postData->meta->{$prefix .'mail'} = $email;
	$postData->meta->{$prefix .'date'} = time();
	
	$postID = pwf_save_newsletter( $postData );	
	
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$err_mess[] = $email. __('email address is not valid.', 'pwf');
		$error = 1;
	}
	elseif( strlen($postname) == 0 ) {
		$err_mess[] = __('Name is invalid.', 'pwf');
		$error = 1;
	}

	if($error == 0)
	{
		$headers = 'From: ' . $name . '<' . $email . '>';
		
		$subject = $postname . ' si è registrato alla neewsletter su ' . get_bloginfo( 'name' );

		$message  = '';
		$message .= '<p>Nome: ' . $name . '</p>';
		$message .= '<p>Email: ' . $email . '</p>';
		$message .= '<p>Oggetto: ' . $subject . '</p>';
		$message .= '<p>Messaggio: ' . $inputmessage . '</p>';


		if(wp_mail($admin_email, $subject, $message, $headers, $uploaded))
		{
			$html  = '<p>' . __('Thank you for subscribing', 'pwf') . ' ' . $name . '</p>';
			$results = json_encode(array('success' => true, 'html' => $html, 'user' => $name));
		}
		else {
			$html  = '<p>' . __('Sorry there has been errors', 'pwf') . ' ' . $name . '</p>';

			$results = json_encode(array('success' => false, 'html' => $html, 'user' => $name));
		}
	}
	else 
	{
		$errors = implode ('<br>',$err_mess);
		$html  = '<p>'. $errors . '</p>';
		$results = json_encode(array('success' => false, 'html' => $html, 'user' => $name));
	}
	

	die($results);
}

function pwf_save_newsletter( $data ) 
{
	add_filter( 'user_has_cap', 'newsletter_grant_publish_caps', 0, 3 );
	$post = array(
		'post_author' => 3,
		'post_title' => wp_strip_all_tags( $data->title ),
		'post_status' => 'draft',
		'post_type' => 'newsletter',
		'post_content_filtered' => wp_kses( 'Registrazione Web', '<b><strong><i><em><h1><h2><h3><h4><h5><h6><pre><code><span>' ),
	);
	$postID = wp_insert_post( $post, true );
	foreach ( $data->meta as $key => $value ) {
		add_post_meta( $postID, $key, $value, true );
	}
	return $postID;
}

function newsletter_grant_publish_caps( $caps, $cap, $args )
{
	if ( 'edit_post' == $args[ 0 ] ) {
		$caps[ $cap[ 0 ] ] = true;
	}
	return $caps;
}
?>