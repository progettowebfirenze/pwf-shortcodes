<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('init', 'pwf_create_post_type_testimonials');
function pwf_create_post_type_testimonials() 
{
    register_post_type('testimonials',
        array(
            'labels' => array(
                'name' => __('Testimonial', 'pwf'),
                'singular_name' => __('Testimonial', 'pwf')
            ),
            'public' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'rewrite' => array('slug' => 'testimonials','with_front' => false),
            'supports' => array('title', 'editor', 'thumbnail'),
			
        )
    );
}

add_filter( 'cmb2_admin_init', 'testimonials_metaboxes' );
function testimonials_metaboxes() 
{
	
	$prefix = '_pwf_';
	
	$testimonials_metabox = new_cmb2_box( array(
		'id'            => $prefix . 'testimonials_data',
		'title'         => __( 'Registrant data', 'pwf-admin' ),
		'object_types'  => array( 'testimonials', ), // Post type'title'     
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
	) );
	
	$testimonials_metabox->add_field(array(
		'name'       => __( 'Nome', 'pwf' ),
		'id'         => $prefix . 'name',
		'type'       => 'text',
	));
	
	$testimonials_metabox->add_field(array(
		'name'       => __( 'Date', 'pwf' ),
		'id'         => $prefix . 'date',
		'type'       => 'text_datetime_timestamp',
	));
}

add_action('wp_enqueue_scripts', 'ajaxtestimonials_enqueuescripts');
function ajaxtestimonials_enqueuescripts()
{
	wp_enqueue_style( 'pwf-testimonials-css', PWFURL.'/pwf-plugin/assets/css/pwfstyle-testimonials.css', array(), 1.0 );
	wp_enqueue_script('pwf-ajaxtestimonials', PWFURL.'/pwf-plugin/assets/js/ajaxtestimonials.js', array(JQUERY_NAME), 1.0, true);
	wp_localize_script( 'pwf-ajaxtestimonials', 'ajaxtestimonials', admin_url( 'admin-ajax.php' ) );
}

include ("pwf-testimonials-html.php");
add_shortcode( 'pwf_testimonials_form', 'pwf_testimonials_form_func' );
function pwf_testimonials_form_func( $atts, $content = null ) 
{	
	$activeLang = ICL_LANGUAGE_CODE;
	$locale = 'it_IT';	
	
	if($activeLang == "it") {
		$locale = 'it_IT';	
	} 
	elseif ($activeLang == "en") {
		$locale = 'en_GB';
	} 

	$output  = '';
	
	$output .= '<form class="input-fields" method="post" action="" name="testimonialsform" id="testimonialsform" enctype="multipart/form-data" data-locale="'. $locale .'">';
	$output .= pwf_get_testimonials_form_html($link);
	$output .= '</form>';

   return $output;
}

function pwf_set_testimonials_content_type()
{
    return "text/html";
}
add_filter( 'wp_mail_content_type','pwf_set_testimonials_content_type' );

add_action( 'wp_ajax_nopriv_pwf_sendtestimonials_mail', 'pwf_sendtestimonials_mail' );
add_action( 'wp_ajax_pwf_sendtestimonials_mail', 'pwf_sendtestimonials_mail' );
function pwf_sendtestimonials_mail()
{
	$results = '';
	$error = 0;
	$saved = 0;
	$postData = new stdClass();
	$err_mess = array();
	$prefix = '_pwf_';
	$uploadedfile = $_FILES[$prefix .'image'];
	$uploaded = NULL;

	if(isset($uploadedfile) && !is_null($uploadedfile)){
		if (!function_exists('wp_generate_attachment_metadata')){
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                require_once(ABSPATH . "wp-admin" . '/includes/file.php');
                require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        }					
					
		$upload_overrides = array( 'test_form' => false );
		
		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
		
		if ( $movefile && !isset( $movefile['error'] ) ) {
			 $uploaded = $movefile['file'];
		} else {
			$err_mess[] .= $movefile['error'];
			$error = 1;
		}	
	}
	$name 			= pwf_testimonials_cleanup($_POST[$prefix .'name']);
	$title	 		= pwf_testimonials_cleanup($_POST[$prefix .'title']);
	$message        = pwf_testimonials_cleanup($_POST[$prefix .'message']);
	
	
	$admin_email = get_option('admin_email');
	$email = get_option('admin_email');
	
	if( strlen($name) == 0 ) {
		$err_mess[] = __('Name is invalid.', 'pwf');
		$error = 1;
	}
	elseif( strlen($title) == 0 ) {
		$err_mess[] = __('Title is invalid.', 'pwf');
		$error = 1;
	}
	elseif(strlen($message) == 0 ) {
		$err_mess[] = __('Message is too short.', 'pwf');
		$error = 1;
	}
	
	if($error == 0)
	{	
		$postData->title 	= $title;
		$postData->content 	= $message;		
		$postData->image 	= $uploaded;
		
		$postData->meta->{$prefix .'name'} = $name;
		$postData->meta->{$prefix .'date'} = time(); 
		
		$postID = pwf_save_testimonials($postData);
		
		if($postID) { 	
		
			$inputmessage = 'Nuova review sul sito';		
			$subject = 'Nuova review sul sito';
			
			$headers = 'From: ' . $name . '<' . $email . '>';
		
			$message  = '';
			$message .= '<p>Nome: ' . $name . '</p>';
			$message .= '<p>Email: ' . $email . '</p>';
			$message .= '<p>Oggetto: ' . $subject . '</p>';
			$message .= '<p>Messaggio: ' . $message . '</p>';
			
			
			if(wp_mail($admin_email, $subject, $message, $headers, $uploaded))
			{
				$html  = '';
				$html .= '<h1>' . __('Thank you', 'pwf') . ' ' . $name . '</h1>';
				$html .= '<h2>' . __('We received your message', 'pwf') . '</h2>';
				$html .= '<p>' . __('We will reply as soon as possible', 'pwf') . ' </p>';
				$html .= '<h3>' . __('Thanks for choosing us!', 'pwf') . ' </h3>';
				$html .= '<p>' . json_encode($postData) . ' </p>';
				$html .= '<div class="actions">';
				$html .= '<a href="#" class="modalBtn btn-left" onclick="jQuery.magnificPopup.close();location.reload();">' . __('close', 'pwf') . '</a>';
				$html .= '<a href="' . get_site_url() . '" class="modalBtn btn-right">' . __('keep browsing', 'pwf') . '</a>';
				$html .= '</div>';
				$html .= '</div>';
				$results = json_encode(array('success' => true, 'html' => $html, 'user' => $name));
			}
			else {
				$html  = '';
				$html .= '<h1>' . __('Sorry', 'pwf') . ' ' . $name . '</h1>';
				$html .= '<h2>' . __('There were errors while sending the message', 'pwf') . '</h2>';
				$html .= '<p class="recatchaerror">'. $mail->ErrorInfo . '</p>';
				$html .= '<div class="actions">';
				$html .= '<a href="#" class="modalBtn btn-left" onclick="jQuery.magnificPopup.close();location.reload();">' . __('Retry', 'pwf') . '</a>';
				$html .= '<a href="' . get_site_url() . '" class="modalBtn btn-right">' . __('keep browsing', 'pwf') . '</a>';
				$html .= '</div>';
				$html .= '</div>';				
				
				$results = json_encode(array('success' => false, 'html' => $html, 'user' => $name));
			}
		}
		else {
			$html  = '';
			$html .= '<h1>' . __('Sorry', 'pwf') . ' ' . $name . '</h1>';
			$html .= '<h2>' . __('There were errors while saving the message', 'pwf') . '</h2>';
			$html .= '<p>errori: '. $postID . '</p>';
			$html .= '<div class="actions">';
			$html .= '<a href="#" class="modalBtn btn-left" onclick="jQuery.magnificPopup.close();location.reload();">' . __('Retry', 'pwf') . '</a>';
			$html .= '<a href="' . get_site_url() . '" class="modalBtn btn-right">' . __('keep browsing', 'pwf') . '</a>';
			$html .= '</div>';
			$html .= '</div>';				
			
			$results = json_encode(array('success' => false, 'html' => $html, 'user' => $name));
		}
	}
	else 
	{
		$errors = implode ('<br>',$err_mess);
		$html  = '';
		$html .= '<h1>' . __('Sorry', 'pwf') . '</h1>';
		$html .= '<h2>' . __('There were errors while sending the message', 'pwf') . '</h2>';
		$html .= '<p>errori: '. $errors . '</p>';
		$html .= '<div class="actions">';
		$html .= '<a href="#" class="modalBtn btn-left" onclick="jQuery.magnificPopup.close();">' . __('Retry', 'pwf') . '</a>';
		$html .= '<a href="' . get_site_url() . '" class="modalBtn btn-right">' . __('keep browsing', 'pwf') . '</a>';
		$html .= '</div>';
		$html .= '</div>';	
		$results = json_encode(array('success' => false, 'html' => $html, 'user' => $name));
	}
	

	die($results);
}

function pwf_testimonials_cleanup($input)
{ // PWF funzione di pulizia variabili
	$search = array(
					'@<script[^>]*?>.*?</script>@si',   // Strip out javascript
					'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
					'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
					'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
				  );

	if (get_magic_quotes_gpc()) {
		$output = stripslashes($input);
	}		  
	$output = preg_replace($search, '', $output);		
	$output = preg_replace("/[^A-Za-z0-9@ .,]/", '', $output);		
	$output = trim($output);		
	$output = htmlspecialchars($output);

	return $input;
} // PWF fine funzione di pulizia variabili

function pwf_save_testimonials($data) 
{
	
	add_filter( 'user_has_cap', 'grant_publish_caps', 0,  3);
	
	$post = array(
			'post_author' => 3,
			'post_title' => wp_strip_all_tags( $data->title ),
			'post_status' => 'draft',            
			'post_type' => 'testimonials',
			'post_content' => $data->content,
			'post_content_filtered' => wp_kses( $data->content, '<b><strong><i><em><h1><h2><h3><h4><h5><h6><pre><code><span>' ),
		);
		
	$postID = wp_insert_post($post,true);
	
	foreach ($data->meta as $key => $value) {
		add_post_meta($postID, $key, $value, true);
	}
	
	if ($data->image) {
		$filename = $data->image;
		
		$filetype = wp_check_filetype( basename( $filename ), null );
		
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		
		$attach_id = wp_insert_attachment( $attachment, $filename, $postID );
		
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		
		set_post_thumbnail( $postID, $attach_id );
	}
	
	return $postID;
}

function grant_publish_caps( $caps, $cap, $args ) 
{

        if ( 'edit_post'  == $args[0] ) {
            $caps[$cap[0]] = true;
        }

        return $caps;
}
?>