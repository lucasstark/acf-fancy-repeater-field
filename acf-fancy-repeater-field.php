<?php

/**
 * Plugin Name: Advanced Custom Fields:  Fancy Repeater
 * Plugin URI: https://github.com/lucasstark/acf-fancy-repeater-field/
 * Description: Adds the ability to manage large repeater field sets easily. 
 * Version: 1.0.0
 * Author: Lucas Stark
 * Author URI: http://lucasstark.com
 * Requires at least: 4.0
 * Tested up to: 4.1
 *
 * Text Domain: acf_fancy_repeater_field
 * Domain Path: /i18n/languages/
 *
 * GitHub Plugin URI: lucasstark/acf-fancy-repeater-field
 *
 * @package ACF_Child_Post_Field
 * @category Core
 * @author Lucas Stark
 */
class ACF_Fancy_Repeater {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new ACF_Fancy_Repeater();
		}
	}

	public function __construct() {
		require_once 'acf-fancy-repeater-field-v5.php';

		add_action( 'acf/input/admin_enqueue_scripts', array($this, 'on_admin_enqueue_scripts') );


		add_action( 'acf/include_field_types', array($this, 'on_include_field_types') );
		
		//add_action( "acf/render_field_settings/type=repeater", array($this, 'set_use_fancy_repeater') );
		//add_filter( 'acf/load_field/type=repeater', array($this, 'on_load_field'), 99, 1 );
		//add_action('acf/render_field/type=repeater', array($this, 'on_render_field'), -1, 1);
	}

	public function on_include_field_types() {

		ACF_Fancy_Repeater_Field_V5::register();
	}

	public function on_admin_enqueue_scripts() {
		$dir = plugin_dir_url( __FILE__ );

		// register & include JS
		wp_register_script( 'acf-input-fancy-repeater-field', "{$dir}assets/js/acf-fancy-repeater-field-v5.js" );
		wp_enqueue_script( 'acf-input-fancy-repeater-field' );


		// register & include CSS
		wp_register_style( 'acf-input-fancy-repeater-field', "{$dir}assets/css/acf-fancy-repeater-field-v5.css" );
		wp_enqueue_style( 'acf-input-fancy-repeater-field' );
	}

	public function on_load_field( $field ) {
		global $post;
		if ( $post && $post->post_type != 'acf-field-group' ) {
			$field['use_fancy_repeater'] = isset( $field['use_fancy_repeater'] ) ? $field['use_fancy_repeater'] : 'yes';
			$field['forced_fancy_repeater'] = false;
			if ( $field['use_fancy_repeater'] == 'yes' ) {
				$field['type'] = 'fancyrepeater';
				$field['forced_fancy_repeater'] = true;
			}
		}

		return $field;
	}

	public function set_use_fancy_repeater( $field ) {
		// layout
		acf_render_field_setting( $field, array(
		    'label' => __( 'Fancy Repeater', 'acf' ),
		    'instructions' => '',
		    'class' => 'acf-repeater-use-fancy-repeater',
		    'type' => 'radio',
		    'name' => 'use_fancy_repeater',
		    'layout' => 'horizontal',
		    'std' => 'no',
		    'choices' => array(
			'yes' => __( 'Yes', 'acf' ),
			'no' => __( 'No', 'acf' )
		    )
		) );
	}

}

ACF_Fancy_Repeater::register();


