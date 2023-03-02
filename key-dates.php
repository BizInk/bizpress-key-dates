<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function keydates_settings_fields( $fields, $section ) {

	if ( 'bizink-client_basic' != $section['id'] ) return $fields;
	
	$fields['keydates_content_page'] = array(
		'id'      => 'keydates_content_page',
		'label'     => __( 'Bizink Client KeyDates', 'bizink-client' ),
		'type'      => 'select',
		'desc'      => __( 'Select the page to show the content. This page must contain the <code>[bizink-content]</code> shortcode.', 'bizink-client' ),
		'options'	=> cxbc_get_posts( [ 'post_type' => 'page' ] ),
		// 'chosen'	=> true,
		'required'	=> false,
	);

	return $fields;
}
add_filter( 'cx-settings-fields', 'keydates_settings_fields', 10, 2 );

function keydates_content( $types ) {
	$options = get_option( 'bizink-client_basic' );
	if(empty($options['content_region'])){
		$options['content_region'] = 'au';
	}
	$type = 'keydates-au';
	switch(strtolower($options['content_region'])){
		case 'ca':
			$type = 'keydates-ca';
			break;
		case 'us':
			$type = 'keydates-us';
			break;
		case 'nz':
			$type = 'keydates-nz';
			break;
		case 'gb':
		case 'uk':
			$type = 'keydates-gb';
		break;
		case 'au':
		default:
			$type = 'keydates-au';
			break;			
	}

	$types[] = [
		'key' 	=> 'keydates_content_page',
		'type'	=> $type
	];

	return $types;
}
add_filter( 'bizink-content-types', 'keydates_content' );

/*
function keydates_country(){
	return 'AU';
}
add_filter('bizink-keydates-country', 'keydates_country' );
*/

if( !function_exists( 'bizink_get_keydates_page_object' ) ){
	function bizink_get_keydates_page_object(){
		$post_id = cxbc_get_option( 'bizink-client_basic', 'keydates_content_page' );
		$post = get_post( $post_id );
		return $post; 
	}
}

if( !function_exists( 'bizink_keydates_init' ) ){
	add_action( 'init', 'bizink_keydates_init');
	function bizink_keydates_init(){
		$post = bizink_get_keydates_page_object();
		if( is_object( $post ) && get_post_type( $post ) == "page" ){
			add_rewrite_tag('%'.$post->post_name.'%', '([^&]+)', 'bizpress=');
			add_rewrite_rule('^'.$post->post_name . '/([^/]+)/?$','index.php?pagename=' . $post->post_name . '&bizpress=$matches[1]','top');
			add_rewrite_rule("^".$post->post_name."/([a-z0-9-]+)[/]?$",'index.php?pagename='.$post->post_name.'&bizpress=$matches[1]','top');
			add_rewrite_rule("^".$post->post_name."/topic/([a-z0-9-]+)[/]?$",'index.php?pagename='.$post->post_name.'&topic=$matches[1]','top');
			add_rewrite_rule("^".$post->post_name."/type/([a-z0-9-]+)[/]?$" ,'index.php?pagename='.$post->post_name.'&type=$matches[1]','top');
			//flush_rewrite_rules();
		}
	}
}

add_filter('query_vars', 'bizpress_keydates_qurey');
function bizpress_keydates_qurey($vars) {
    $vars[] = "bizpress";
    return $vars;
}