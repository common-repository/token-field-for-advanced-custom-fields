<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_field_tokenfield') ) :


class acf_field_tokenfield extends acf_field {
	
	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options
		
		
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct( $settings )
	{
		// vars
		$this->name = 'tokenfield';
		$this->label = __('Token');
		$this->category = __("Basic",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			'default_value'	=> '',
			'max_tokens'	=> '',
			'placeholder'	=> '',
			'return_format'	=> 'string',
		);
		
		
		// do not delete!
    	parent::__construct();
    	
    	
    	// settings
		$this->settings = $settings;

	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like below) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/
		
		// key is needed in the field names to correctly save the data
		$key = $field['name'];
		
		
		// Create Field Options HTML
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Default Value",'acf'); ?></label>
				<p class="description"><?php _e("Appears when creating a new post",'acf'); ?></p>
			</td>
			<td>
				<?php
					do_action('acf/create_field', array(
						'type'		=>	'text',
						'name'		=>	'fields['.$key.'][default_value]',
						'value'		=>	$field['default_value'],
					));
				?>
			</td>
		</tr>

		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Placeholder Text",'acf'); ?></label>
				<p class="description"><?php _e("Appears within the input",'acf'); ?></p>
			</td>
			<td>
				<?php
					do_action('acf/create_field', array(
						'type'		=>	'text',
						'name'		=>	'fields['.$key.'][placeholder]',
						'value'		=>	$field['placeholder'],
					));
				?>
			</td>
		</tr>

		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Token Limit",'acf'); ?></label>
				<p class="description"><?php _e("Leave blank for no limit",'acf'); ?></p>
			</td>
			<td>
				<?php
					do_action('acf/create_field', array(
						'type'		=>	'number',
						'name'		=>	'fields['.$key.'][max_tokens]',
						'value'		=>	$field['max_tokens'],
					));
				?>
			</td>
		</tr>

		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Return Format",'acf'); ?></label>
				<p class="description"><?php _e("Specify the returned value on front end",'acf'); ?></p>
			</td>
			<td>
				<?php
					do_action('acf/create_field', array(
						'type'			=>	'radio',
						'name'			=>	'fields['.$key.'][return_format]',
						'value'			=>	$field['return_format'],
						'layout'		=> 'horizontal',
						'choices'		=> array(
							'string'		=> __('String','acf'),
							'array'			=> __('Array','acf'),
						),
					));
				?>
			</td>
		</tr>
		<?php
		
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		
		// vars
		$field['type'] = 'text';
		$field['class'] = 'tokenfield';
		$atts = array();
		$o = array( 'type', 'id', 'class', 'name', 'value', 'placeholder' );
		$s = array( 'readonly', 'disabled' );
		
		
		// maxlength
		if( $field['maxlength'] ) {
		
			$o[] = 'maxlength';
			
		}
		
		
		// append atts
		foreach( $o as $k ) {
		
			$atts[ $k ] = $field[ $k ];	
			
		}
		
		
		// append special atts
		foreach( $s as $k ) {
		
			if( !empty($field[ $k ]) ) $atts[ $k ] = $k;
			
		}


		if(empty($field['max_tokens'])) {
			$field['max_tokens'] = 0;
		}
		
		
		// create Field HTML
		$e = '
			<div>
				<input ' . $this->esc_attr( $atts ) . ' />
			</div>
			<script>
				jQuery("input#'.$field['id'].'").tokenfield({
					limit: '.$field['max_tokens'].',
					delimiter: \',\'
				});
			</script>
		';
		
		// return
		echo $e;
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed back to the API functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
		if($field['return_format'] == 'array') {
			$value = explode(',', $value);
			if(empty(end($value)))
				array_pop($value);
		}
		return $value;
	}

	function esc_attr( $atts ) {
		
		// is string?
		if( is_string($atts) ) {
			
			$atts = trim( $atts );
			return esc_attr( $atts );
			
		}
		
		
		// validate
		if( empty($atts) ) {
			
			return '';
			
		}
		
		
		// vars
		$e = array();
		
		
		// loop through and render
		foreach( $atts as $k => $v ) {
			
			// append
			$e[] = $k . '="' . esc_attr( $v ) . '"';
		}
		
		
		// echo
		return implode(' ', $e);
		
	}

}


// initialize
new acf_field_tokenfield( $this->settings );


// class_exists check
endif;

?>