<?php
/**
 * Plugin Name: Strong Password Generator
 * Description: Encourage the use of strong passwords by helping users generate them easily.
 * Version: 0.3.0
 * Author: Frankie Jarrett
 * Author URI: http://frankiejarrett.com
 * Text Domain: strong-password-generator
 *
 * Copyright: © 2015 Frankie Jarrett.
 * License: GNU General Public License v2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

class Strong_Password_Generator {

	/**
	 * Plugin version number
	 *
	 * @const string
	 */
	const VERSION = '0.3.0';

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
		add_action( 'resetpass_form', array( __CLASS__, 'password_generator' ) );
		add_filter( 'show_password_fields', array( __CLASS__, 'password_generator' ) );
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
	 * @param string $hook
	 *
	 * @return void
	 */
	public static function enqueue_scripts( $hook ) {
		if (
			'admin_enqueue_scripts' === current_action()
			&&
			! in_array( $hook, array( 'profile.php', 'user-edit.php', 'user-new.php' ) )
		) {
			return;
		}

		wp_enqueue_script( 'spg-password-generator' );
		wp_enqueue_script( 'spg-button' );
		wp_enqueue_style( 'spg-button' );
	}

	/**
	 * Display the password generator button markup
	 *
	 * @action resetpass_form
	 * @filter show_password_fields
	 *
	 * @param bool $show_password_fields
	 *
	 * @return void|bool
	 */
	public static function password_generator( $show_password_fields ) {
		if (
			'show_password_fields' === current_filter()
			&&
			! $show_password_fields
		) {
			return false;
		}

		/**
		 * Filter the default password length
		 *
		 * @return int
		 */
		$length = apply_filters( 'spg_default_password_length', 20 );

		/**
		 * Filter the minimum password length allowed
		 *
		 * @return int
		 */
		$min = apply_filters( 'spg_min_password_length', 7 );

		/**
		 * Filter the maximum password length allowed
		 *
		 * @return int
		 */
		$max = apply_filters( 'spg_max_password_length', 32 );

		/**
		 * Filter whether or not to allow memorable passwords (true = decreased entropy)
		 *
		 * @return bool
		 */
		$memorable = apply_filters( 'spg_allow_memorable_passwords', false );
		?>
		<div class="spg-container">
			<p>
				<a href="#" id="spg-button" class="button button-secondary button-large">
					<span class="dashicons dashicons-admin-network"></span>
					<?php esc_html_e( 'Password Generator', 'strong-password-generator' ) ?>
				</a>
			</p>
			<div id="spg-controls">
				<p>
					<?php esc_html_e( 'Length', 'strong-password-generator' ) ?>
					<input type="range" id="spg-length" min="<?php echo absint( $min ) ?>" max="<?php echo absint( $max ) ?>" value="<?php echo absint( $length ) ?>">
					<span id="spg-display-length"><?php echo absint( $length ) ?></span>
				</p>
				<p>
					<code id="spg-display-pass"></code>
				</p>
			</div>
			<input type="hidden" id="spg-default-length" value="<?php echo absint( $length ) ?>">
			<input type="hidden" id="spg-memorable" value="<?php echo absint( $memorable ) ?>">
		</div>
		<?php
		if ( 'show_password_fields' === current_filter() ) {
			return true;
		}
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
