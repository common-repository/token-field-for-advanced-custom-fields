<?php

/*
Plugin Name: Advanced Custom Fields: Token Field
Description: Adds a tokenized text box for saving comma separated values.
Version: 1.0.3
Author: foTYPE
Author URI: http://fotype.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_plugin_tokenfield') ) :

class acf_plugin_tokenfield {
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// vars
		$this->settings = array(
			'version'	=> '1.0.0',
			'url'		=> plugin_dir_url( __FILE__ ),
			'path'		=> plugin_dir_path( __FILE__ )
		);
		
		
		// set text domain
		// https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
		load_plugin_textdomain( 'acf-tokenfield', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );


		add_action('admin_enqueue_scripts', 	array($this, 'enqueue_admin_scripts_styles'));
		
		
		// include field
		add_action('acf/include_field_types', 	array($this, 'include_field_types')); // v5
		add_action('acf/register_fields', 		array($this, 'include_field_types')); // v4
		
	}

	function enqueue_admin_scripts_styles() {
		wp_enqueue_script('acf-tokenfield', $this->settings['url'].'assets/js/bootstrap-tokenfield.js');

		wp_enqueue_style('acf-tokenfield', $this->settings['url'].'assets/css/bootstrap-tokenfield.css');
	}	
	
	
	/*
	*  include_field_types
	*
	*  This function will include the field type class
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	$version (int) major ACF version. Defaults to false
	*  @return	n/a
	*/
	
	function include_field_types( $version = false ) {
		
		// support empty $version
		if( !$version ) $version = 4;
		
		// include
		include_once('fields/acf-tokenfield-v' . $version . '.php');
		
	}
	
}


// initialize
new acf_plugin_tokenfield();


// class_exists check
endif;
	
?>