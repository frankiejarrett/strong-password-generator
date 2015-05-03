<?php
/**
 * Plugin Name: Strong Password Generator
 * Description: Encourage the use of strong passwords by helping users generate them easily.
 * Version: 0.1.0
 * Author: Frankie Jarrett
 * Author URI: http://frankiejarrett.com
 * License: GPLv3
 * Text Domain: strong-password-generator
 */

class Strong_Password_Generator {

	/**
	 * Plugin version number
	 *
	 * @const string
	 */
	const VERSION = '0.1.0';

	/**
	 * Hold plugin instance
	 *
	 * @var string
	 */
	public static $instance;

	/**
	 * Class constructor
	 */
	private function __construct() {
		define( 'STRONG_PASSWORD_GENERATOR_PLUGIN', plugin_basename( __FILE__ ) );
		define( 'STRONG_PASSWORD_GENERATOR_DIR', plugin_dir_path( __FILE__ ) );
		define( 'STRONG_PASSWORD_GENERATOR_URL', plugin_dir_url( __FILE__ ) );
		define( 'STRONG_PASSWORD_GENERATOR_INC_DIR', STRONG_PASSWORD_GENERATOR_DIR . 'includes/' );
		define( 'STRONG_PASSWORD_GENERATOR_LANG_PATH', dirname( STRONG_PASSWORD_GENERATOR_PLUGIN ) . '/languages' );

		add_action( 'plugins_loaded', array( __CLASS__, 'i18n' ) );
		add_action( 'init', array( __CLASS__, 'load' ) );
		add_action( 'init', array( __CLASS__, 'register_scripts' ) );
	}

	/**
	 * Load languages
	 *
	 * @action plugins_loaded
	 *
	 * @return void
	 */
	public static function i18n() {
		load_plugin_textdomain( 'strong-password-generator', false, STRONG_PASSWORD_GENERATOR_LANG_PATH );
	}

	/**
	 * Register hooks
	 *
	 * @action init
	 *
	 * @return void
	 */
	public static function load() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'login_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Register scripts and styles
	 *
	 * @action init
	 *
	 * @return void
	 */
	public static function register_scripts() {
		wp_register_script( 'spg-password-generator', STRONG_PASSWORD_GENERATOR_URL . 'js/password-generator.min.js', array(), '0.2.1', true );
		wp_register_script( 'spg-button', STRONG_PASSWORD_GENERATOR_URL . 'js/button.js', array( 'jquery', 'spg-password-generator' ), self::VERSION, true );
		wp_register_style( 'spg-button', STRONG_PASSWORD_GENERATOR_URL . 'css/button.css', array(), self::VERSION );
	}

	/**
	 * Enqueue scripts in the admin and login screens
	 *
	 * @action admin_enqueue_scripts
	 * @action login_enqueue_scripts
	 *
	 * @return void
	 */
	public static function enqueue_scripts( $hook ) {
		if (
			'admin_enqueue_scripts' === current_filter()
			&&
			! in_array( $hook, array( 'profile.php', 'user-edit.php', 'user-new.php' ) )
		) {
			return;
		}

		wp_enqueue_script( 'spg-password-generator' );
		wp_enqueue_script( 'spg-button' );
		wp_enqueue_style( 'spg-button' );

		/**
		 * Filter the password length
		 *
		 * @return int
		 */
		$length = apply_filters( 'spg_password_length', 20 );

		/**
		 * Filter whether or not to allow memorable passwords (decreased entropy)
		 *
		 * @return bool
		 */
		$memorable = apply_filters( 'spg_allow_memorable_passwords', false );

		wp_localize_script(
			'spg-button',
			'spg_button',
			array(
				'length'    => absint( $length ),
				'memorable' => (bool) $memorable,
				'i18n'      => array(
					'button' => esc_html__( 'Generate Strong Password', 'strong-password-generator' ),
					'alert'  => esc_html__( 'Please save your password in a safe place:', 'strong-password-generator' ),
				),
			)
		);
	}

	/**
	 * Return active instance of Strong_Password_Generator, create one if it doesn't exist
	 *
	 * @return Strong_Password_Generator
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			$class = __CLASS__;
			self::$instance = new $class;
		}

		return self::$instance;
	}

}

$GLOBALS['strong_password_generator'] = Strong_Password_Generator::get_instance();
