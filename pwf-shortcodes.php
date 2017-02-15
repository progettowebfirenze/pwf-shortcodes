<?php
/*
Plugin Name:       PWF Shortcodes
Description:       Plugin Personalizzato di ProgettoWebFirenze.
Version:           1.0
Author:            ProgettoWebFirenze
Author URI:        http://www.ProgettoWebFirenze.com/
License:           GPLv2 or later
*/

define('PWFURL', WP_PLUGIN_URL);
define('PWFPATH', WP_PLUGIN_DIR);
define('JQUERY_NAME', 'jquery');
global $post;

add_filter('xmlrpc_enabled', '__return_false');

$vendors_path = PWFURL.'/pwf-shortcodes/assets/vendors';

add_action('plugins_loaded', 'pwf_shortcodes_load_textdomain');
function pwf_shortcodes_load_textdomain()
{
	load_plugin_textdomain("pwf-shortcodes", false, dirname(plugin_basename(__FILE__)) . '/languages');
}

function pwf_shortcodes_scripts() {
	$vendors_path = PWFURL.'/pwf-shortcodes/assets/vendors';
	wp_enqueue_style( 'pwf-formvalidationcss', $vendors_path . '/formvalidation/dist/css/formValidation.min.css', array(), 1.0 );
	wp_enqueue_style( 'pwf-magnific-popup', $vendors_path . '/magnific-popup/dist/magnific-popup.css', array(), 1.0 );
	wp_enqueue_style( 'pwf-datedropper', $vendors_path . '/datedropper/datedropper.css', array(), 1.0 );

	wp_enqueue_script('pwf-formvalidationjs', $vendors_path . '/formvalidation/dist/js/formValidation.min.js', array(JQUERY_NAME), 1.0);
	wp_enqueue_script('pwf-formvalidation-framework', $vendors_path . '/formvalidation/dist/js/framework/bootstrap.min.js', array(JQUERY_NAME), 1.0);
	wp_enqueue_script('pwf-i18n', $vendors_path . '/formvalidation/dist/js/addon/i18n.min.js', array(JQUERY_NAME), 1.0);
	wp_enqueue_script('pwf-validIT', $vendors_path . '/formvalidation/dist/js/language/it_IT.js', array(JQUERY_NAME), 1.0);
	wp_enqueue_script('pwf-magnific-popupjs', $vendors_path . '/magnific-popup/dist/jquery.magnific-popup.min.js', array(JQUERY_NAME), 1.0);
	wp_enqueue_script('pwf-datedropper-js', $vendors_path . '/datedropper/datedropper.js', array(JQUERY_NAME), 1.0);
}
add_action( 'wp_enqueue_scripts', 'pwf_shortcodes_scripts' );

if ( ! class_exists( 'cmb_Meta_Box' ) )
	require_once 'cbm2/init.php';


include ("shortcodes/pwf-contacts.php");
//include ("shortcodes/pwf-testimonials.php");
//include ("shortcodes/pwf-newsletter.php");

?>