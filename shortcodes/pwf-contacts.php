<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('init', 'pwf_create_post_type_contacts');
function pwf_create_post_type_contacts() 
{
    register_post_type('contacts',
        array(
            'labels' => array(
                'name' => __('Contact', 'pwf'),
                'singular_name' => __('Contact', 'pwf')
            ),
            'public' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'rewrite' => array('slug' => 'contacts','with_front' => false),
            'supports' => array('title', 'editor'),
			
        )
    );
}

add_filter( 'cmb2_admin_init', 'cpt_contacts_metaboxes' );
function cpt_contacts_metaboxes()
{
	
	$prefix = '_pwf_';
	
	$cpt_contacts_metaboxes = new_cmb2_box( array(
		'id'            => $prefix . 'contacts_data',
		'title'         => __( 'Contact data', 'pwf-admin' ),
		'object_types'  => array( 'contacts', ), // Post type'title'     
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
	) );
	
	$cpt_contacts_metaboxes->add_field(array(
		'name'       => __( 'Nome', 'pwf' ),
		'id'         => $prefix . 'name',
		'type'       => 'text',
	));
	
	$cpt_contacts_metaboxes->add_field(array(
		'name'       => __( 'Email', 'pwf' ),
		'id'         => $prefix . 'email',
		'type'       => 'text',
	));
	
	$cpt_contacts_metaboxes->add_field(array(
		'name'       => __( 'Privacy', 'pwf-admin' ),
		'id'         => $prefix . 'privacy',
		'type'       => 'radio_inline',
		'options' => array(
			'0' => __( 'NO', 'pwf-admin' ),
			'1'   => __( 'SI', 'pwf-admin' ),
		),
	));
	
	$cpt_contacts_metaboxes->add_field(array(
		'name'       => __( 'Date', 'pwf' ),
		'id'         => $prefix . 'date',
		'type'       => 'text_datetime_timestamp',
	));
}

add_action('wp_enqueue_scripts', 'ajaxcontact_enqueuescripts');
function ajaxcontact_enqueuescripts()
{
	wp_enqueue_style( 'pwf', PWFURL.'/pwf-plugin/assets/css/pwfstyle-contacts.css', array(), 1.0 );	
	wp_enqueue_script('pwf-ajaxcontact', PWFURL.'/pwf-plugin/assets/js/ajaxcontacts.js', array(JQUERY_NAME), 1.0, true);
	wp_localize_script( 'pwf-ajaxcontact', 'ajaxcontact', admin_url( 'admin-ajax.php' ) );
}

include( "pwf-contacts-html.php" );
add_shortcode( 'pwf_contacts', 'pwf_contacts_func' );
function pwf_contacts_func( $atts, $content = null ) 
{
	extract( shortcode_atts( array(
			'link' => ''
			), $atts ) );
	
	$activeLang = ICL_LANGUAGE_CODE;
	
	$locale = 'it_IT';	
	if($activeLang == "it") {
		$locale = 'it_IT';
	} 
	elseif ($activeLang == "en") {
		$locale = 'en_GB';
	}
	
	$output  = '';
	
	$output .= '<form class="input-fields" method="post" action="" name="contactform" id="contact-form" enctype="text/plain" data-locale="'. $locale .'">';
	$output .= pwf_get_contact_form_html($link);
	$output .= '</form>';

  
   return $output;
}

add_filter( 'wp_mail_content_type','pwf_set_content_type' );
function pwf_set_content_type()
{
    return "text/html";
}

add_action( 'wp_ajax_nopriv_pwf_sendmail', 'pwf_sendmail' );
add_action( 'wp_ajax_pwf_sendmail', 'pwf_sendmail' );
function pwf_sendmail()
{
	$results = '';
	$error = 0;
	$saved = 0;
	$postData = new stdClass();
	$err_mess = array();
	$prefix = '_pwf_';

	$name 			= pwf_cleanup($_POST[$prefix .'name']);
	$email 			= pwf_cleanup($_POST[$prefix .'email']);
	$privacy		= pwf_cleanup($_POST[$prefix .'privacy']);
	$message 	= pwf_cleanup($_POST[$prefix .'message']);


	$admin_email = get_option('admin_email');

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$err_mess[] = $email. __('email address is not valid.', 'pwf');
		$error = 1;
	}
	elseif( strlen($name) == 0 ) {
		$err_mess[] = __('Name is invalid.', 'pwf');
		$error = 1;
	}
	elseif( $privacy == 0 ) {
		$err_mess[] = __('You must accept terms and conditions.', 'pwf');
		$error = 1;
	}
	elseif( strlen($message) == 0 )
	{
		$err_mess[] = __('Content is invalid.', 'pwf');
		$error = 1;
	}
		
	if($error == 0)
	{
		$postData->title 	= $name;
		$postData->content 	= $message;	
		
		$postData->meta->{$prefix .'email'} = $email;
		$postData->meta->{$prefix .'privacy'} = $email;
		$postData->meta->{$prefix .'date'} = time(); 
		
		$postID = pwf_save_contacts($postData);
		
		if($postID) { 
			$headers = 'From: ' . $name . '<' . $email . '>';
			
			$subject = $name . ' ha contattato ' . get_bloginfo( 'name' );
			
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
		$html .= '<p>'. $errors . '</p>';
		$html .= '<div class="actions">';
		$html .= '<a href="#" class="modalBtn btn-left" onclick="jQuery.magnificPopup.close();">' . __('Retry', 'pwf') . '</a>';
		$html .= '<a href="' . get_site_url() . '" class="modalBtn btn-right">' . __('keep browsing', 'pwf') . '</a>';
		$html .= '</div>';
		$html .= '</div>';	
		$results = json_encode(array('success' => false, 'html' => $html, 'user' => $name));
	}
	
	die($results);
}

function pwf_cleanup($input)
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

function pwf_save_contacts($data) 
{
	
	add_filter( 'user_has_cap', 'contacts_grant_publish_caps', 0,  3);
	
	$post = array(
			'post_author' => 3,
			'post_title' => wp_strip_all_tags( $data->title ),
			'post_status' => 'draft',            
			'post_type' => 'contacts',
			'post_content' => $data->content,
			'post_content_filtered' => wp_kses( $data->content, '<b><strong><i><em><h1><h2><h3><h4><h5><h6><pre><code><span>' ),
		);
		
	$postID = wp_insert_post($post,true);
	
	foreach ($data->meta as $key => $value) {
		add_post_meta($postID, $key, $value, true);
	}
	
	return $postID;
}

function contacts_grant_publish_caps( $caps, $cap, $args ) 
{

        if ( 'edit_post'  == $args[0] ) {
            $caps[$cap[0]] = true;
        }

        return $caps;
}
?>