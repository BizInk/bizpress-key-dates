<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'display_post_states', 'bizpress_keydates_post_states', 10, 2 );
function bizpress_keydates_post_states( $post_states, $post ) {
	if( !function_exists( 'cxbc_get_option' ) ) return $post_states;
	$keydatesPageID = cxbc_get_option( 'bizink-client_basic', 'keydates_content_page' );
    if ( $keydatesPageID == $post->ID ) {
        $post_states['bizpress_keydates'] = __('BizPress Keydates Resources','bizink-client');
    }
    return $post_states;
}

function keydates_settings_fields( $fields, $section ) {
	$pageselect = false;
	if(defined('CXBPC')){
		$bizpress = get_plugin_data( CXBPC );
		$v = intval(str_replace('.','',$bizpress['Version']));
		if($v >= 151){
			$pageselect = true;
		}
	}
	if('bizink-client_basic' == $section['id']){
		$fields['keydates_content_page'] = array(
			'id'        => 'keydates_content_page',
			'label'     => __( 'Key Dates', 'bizink-client' ),
			'type'      => $pageselect ? 'pageselect':'select',
			'desc'      => __( 'Select the page to show the content. This page must contain the <code>[bizpress-content]</code> shortcode.', 'bizink-client' ),
			'options'	=> cxbc_get_posts( [ 'post_type' => 'page' ] ),
			'required'	=> false,
			'default_page' => [
				'post_title' => 'Key Dates',
				'post_content' => '[bizpress-content]',
				'post_status' => 'publish',
				'post_type' => 'page'
			]
		);
	}
	
	if('bizink-client_content' == $section['id']){
		$fields['keydates_label'] = array(
			'id'    => 'keydates',
	        'label'	=> __( 'BizPress Key Dates', 'bizink-client' ),
	        'type'  => 'divider'
		);
		$fields['keydates_title'] = array(
			'id'        => 'keydates_title',
			'label'     => __( 'Key Dates Title', 'bizink-client' ),
			'type'      => 'text',
			'default'   => __( 'Key Dates', 'bizink-client' ),
			'required'	=> true,
		);
		$fields['keydates_desc'] = array(
			'id'      	=> 'keydates_desc',
			'label'     => __( 'Key Dates Description', 'bizink-client' ),
			'type'      => 'textarea',
			'default'   => __( 'Free resources to help you with the Key Dates.', 'bizink-client' ),
			'required'	=> false,
		);
	}

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
		case 'ie':
			$type = 'keydates-ie';
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


if( !function_exists( 'bizink_get_keydates_page_object' ) ){
	function bizink_get_keydates_page_object(){
		if( !function_exists( 'cxbc_get_option' ) ) return false;
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
			add_rewrite_rule('^'.$post->post_name . '/([^/]+)/?$','index.php?pagename=keydates&bizpress=$matches[1]','top');
			add_rewrite_rule("^".$post->post_name."/([a-z0-9-]+)[/]?$",'index.php?pagename=keydates&bizpress=$matches[1]','top');
			//add_rewrite_rule("^".$post->post_name."/topic/([a-z0-9-]+)[/]?$",'index.php?pagename=keydates&topic=$matches[1]','top');
			//add_rewrite_rule("^".$post->post_name."/type/([a-z0-9-]+)[/]?$" ,'index.php?pagename=keydates&type=$matches[1]','top');
			flush_rewrite_rules();
		}
	}
}

add_filter('query_vars', 'bizpress_keydates_qurey');
function bizpress_keydates_qurey($vars) {
    $vars[] = "bizpress";
    return $vars;
}