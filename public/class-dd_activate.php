<?php
/**
 * DD_Activate.
 *
 * @package   DD_Activate
 * @author    Cindy Leschaud <cindy.leschaud@gmail.com>
 * @license   GPL-2.0+
 * @link      http://designpond.ch
 * @copyright 2014 Your Name or Company Name
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-plugin-name-admin.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package DD_Activate
 * @author  Cindy Leschaud <cindy.leschaud@gmail.com>
 */
class DD_Activate {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * @TODO - Rename "plugin-name" to the name of your plugin
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'plugin-name';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'admin_post_submit-code', array( $this, 'activate_code' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}
	
	/**
	 * Is access code valid?
	*/
	public function isCodeAccessValid($code){
		
		global $wpdb;
		
		$validcode = $wpdb->get_row('SELECT * FROM wp_code WHERE number_code = '.$code.'');
		
		if(!empty($validcode)) 
		{
			$validity    = $validcode->validity_code;
		    $isValid     = ( ($validcode->valid_code == 1) ? true : false );
			$id_code     = $validcode->id_code;
			
			$today       = date("Y-m-d"); 

			if( ($today < $validity) && $isValid )
			{	
				return array( 'id_code' => $id_code , 'validity' => $validity);
			}
			
			return false;
		}
		
		return false;
	}
	
	/**
	 *  Function for activation of user account with code
	*/
	public function activate_code(){
		
		global $wpdb;
		
		echo '<pre>';
		print_r($_POST);
		echo '</pre>';
		exit();
		
		$page = get_ID_by_slug('reactiver-votre-compte');
		
		$code = (!empty($_POST['accescode']) ? $_POST['accescode'] : null);
		$user = (!empty($_POST['user']) ? $_POST['user'] : null);

		if( $code )
		{
			// test if code is valid and return infos id and validity
			$isValid = $this->isCodeAccessValid($code);
			
			if( $isValid )
			{	
				 // change statut of code
				 $data = array( 'valid_code' => 0 , 'user_id' => $user , 'updated' => date("Y-m-d") );
				  
				 $wpdb->update( 'wp_code', $data , array( 'id_code' => $isValid['id_code']) , array( '%d', '%d', '%s' ), array( '%d' ) );
				 
				 // Calculate code is for witch year to update user's account validity
				 // Current year 2 digit format ex: 14
				 $currentyear = date('y');
				 
				 // Get 2 first digit frmo code
				 $year = substr($isValid['validity'], 2, 2);
				 
				 if($year >= $currentyear)
				 {
					$newyear = '20'.$year.'-12-31'; 
				 }
				 
				 // Deal with user
				 if($user)
				 {
					 update_user_meta( $user, 'date_abo_active', $newyear );
					 
					 //login
			         wp_set_current_user($user, $user_login);
			         wp_set_auth_cookie($user);
			         do_action('wp_login', $user_login);					 
				 }
					
				// Return url
				$url = add_query_arg( array('reactivate' => 'ok') ,  site_url() );
				
				wp_redirect( $url ); exit;
		
			}
			else
			{
				// Return url
				$url = add_query_arg( array('user' => $user , 'error' => 1) , get_permalink($page) );
								
				 wp_redirect( $url ); exit;
			}

		}
		else
		{
			// Return url
			$url = add_query_arg( array('user' => $user , 'error' => 2) , get_permalink($page) );
			
			wp_redirect( $url ); exit;
		}
	}

}