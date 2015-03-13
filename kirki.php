<?php
/*
Plugin Name:   Kirki Framework
Plugin URI:    http://kirki.org
Description:   An options framework using and extending the WordPress Customizer
Author:        Aristeides Stathopoulos
Author URI:    http://press.codes
Version:       0.6.2
*/

// Load Kirki_Fonts before everything else
include_once( dirname( __FILE__ ) . '/includes/class-kirki-fonts.php' );

/**
 * The main Kirki class
 */
if ( ! class_exists( 'Kirki' ) ) :
class Kirki {
	public $scripts;
	public $styles;

	function __construct() {

		if ( ! defined( 'KIRKI_PATH' ) ) {
			define( 'KIRKI_PATH', dirname( __FILE__ ) );
		}
		if ( ! defined( 'KIRKI_URL' ) ) {
			define( 'KIRKI_URL', plugin_dir_url( __FILE__ ) );
		}

		$options = $this->get_config();

		include_once( dirname( __FILE__ ) . '/includes/required.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-kirki-style.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-kirki-scripts.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-kirki-fonts-script.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-kirki-color.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-kirki-settings.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-kirki-controls.php' );
		include_once( dirname( __FILE__ ) . '/includes/deprecated.php' );

		$this->scripts = new Kirki_Scripts();
		$this->styles  = new Kirki_Style();

		add_action( 'customize_register', array( $this, 'include_customizer_controls' ), 1 );
		add_action( 'customize_register', array( $this, 'customizer_builder' ), 99 );
		add_action( 'wp', array( $this, 'update' ) );

	}

	/**
	 * Include the necessary files for custom controls.
	 * Default WP Controls are not included here because they are already being loaded from WP Core.
	 */
	function include_customizer_controls() {

		$controls = $this->get_controls();
		foreach ( $controls as $control ) {
			if ( 'group_title' == $control['type'] ) {
				include_once( KIRKI_PATH . '/includes/controls/class-kirki-customize-group-title-control.php' );
			} elseif ( 'multicheck' == $control['type'] ) {
				include_once( KIRKI_PATH . '/includes/controls/class-kirki-customize-multicheck-control.php' );
			} elseif ( 'number' == $control['type'] ) {
				include_once( KIRKI_PATH . '/includes/controls/class-kirki-customize-number-control.php' );
			} elseif ( 'radio-buttonset' == $control['type'] || ( 'radio' == $control['type'] && isset( $control['mode'] ) && 'buttonset' == $control['mode'] ) ) {
				include_once( KIRKI_PATH . '/includes/controls/class-kirki-customize-radio-buttonset-control.php' );
			} elseif ( 'radio-image' == $control['type'] || ( 'radio' == $control['type'] && isset( $control['mode'] ) && 'image' == $control['mode'] ) ) {
				include_once( KIRKI_PATH . '/includes/controls/class-kirki-customize-radio-image-control.php' );
			} elseif ( 'slider' == $control['type'] ) {
				include_once( KIRKI_PATH . '/includes/controls/class-kirki-customize-slider-control.php' );
			} elseif ( 'sortable' == $control['type'] ) {
				include_once( KIRKI_PATH . '/includes/controls/class-kirki-customize-sortable-control.php' );
			} elseif ( 'switch' == $control['type'] || ( 'checkbox' == $control['type'] && isset( $control['mode'] ) && 'switch' == $control['mode'] ) ) {
				include_once( KIRKI_PATH . '/includes/controls/class-kirki-customize-switch-control.php' );
			} elseif ( 'toggle' == $control['type'] || ( 'checkbox' == $control['type'] && isset( $control['mode'] ) && 'toggle' == $control['mode'] ) ) {
				include_once( KIRKI_PATH . '/includes/controls/class-kirki-customize-toggle-control.php' );
			} elseif ( 'background' == $control['type'] ) {
				include_once( KIRKI_PATH . '/includes/controls/class-kirki-customize-slider-control.php' );
			}
		}

	}

	/**
	 * Build the controls
	 */
	function customizer_builder( $wp_customize ) {

		$controls = $this->get_controls();
		$kirki_settings = new Kirki_Settings();
		$kirki_controls = new Kirki_Controls();

		// Early exit if controls are not set or if they're empty
		if ( ! isset( $controls ) || empty( $controls ) ) {
			return;
		}
		foreach ( $controls as $control ) {
			$kirki_settings->add_setting( $wp_customize, $control );
			$kirki_controls->add_control( $wp_customize, $control );
		}

	}

	function get_config() {

		$config = apply_filters( 'kirki/config', array() );

		$controls = $this->get_controls();
		foreach( $controls as $control ) {
			if ( isset( $control['output'] ) ) {
				$uses_output = true;
			}
		}

		if ( isset( $uses_output ) && ! isset( $config['stylesheet_id'] ) ) {
			$config['stylesheet_id'] = 'kirki-styles';
		}
		return $config;

	}

	function get_controls() {

		$controls = apply_filters( 'kirki/controls', array() );
		return $controls;

	}

	function update() {

		// < 0.6.1 -> 0.6.2
		if ( ! get_option( 'kirki_version' ) ) {

			$control_ids = array();
			$controls = $this->get_controls();
			foreach ( $controls as $control ) {
				if ( 'background' != $control['type'] ) {
					$control_ids[] = $control['setting'];
				}
			}
			foreach ( $control_ids as $control_id ) {
				if ( get_theme_mod( $control_id . '_opacity' ) && ! get_theme_mod( $control_id ) ) {
					update_theme_mod( $control_id, get_theme_mod( $control_id . '_opacity' ) );
				}
			}

			update_option( 'kirki_version', '0.6.2' );

		}

	}

}

global $kirki;
$kirki = new Kirki();

endif;
