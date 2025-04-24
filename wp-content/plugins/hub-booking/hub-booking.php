<?php
/**
 * Plugin Name: Hub Booking
 * Description: Simple booking management system.
 * Plugin URI: https://liquid-themes.com/
 * Version: 1.1
 * Author: Liquid Themes
 * Author URI: https://liquid-themes.com/
 * Text Domain: hub-booking
 * Elementor tested up to: 3.18
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'LD_BOOKING_PATH', plugin_dir_path( __FILE__ ) );
define( 'LD_BOOKING_URL', plugin_dir_url( __FILE__ ) );
define( 'LD_BOOKING_VERSION', get_file_data( __FILE__, array('Version' => 'Version'), false)['Version'] );

final class Hub_Booking {

    /**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var Hub_Booking The single instance of the class.
	 */
	private static $_instance = null;

    /**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return Hub_Booking An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
    }

    /**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
    public function __construct() {
       
        add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );

    }

    /**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n() {

		load_plugin_textdomain( 'hub-booking' );

	}

    /**
	 * On Plugins Loaded
	 *
	 * Checks the plugin has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_plugins_loaded() {

		if ( $this->is_compatible() ) {
			$this->init();
		}

	}

    /**
	 * Compatibility Checks
	 *
	 * Checks if the installed version of Elementor meets the plugin's minimum requirement.
	 * Checks if the installed PHP version meets the plugin's minimum requirement.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function is_compatible() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			return false;
		}

		return true;

	}

    /**
	 * Initialize the plugin
	 *
	 * Load the files required to run the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
    public function init(){

        $this->i18n();
        $this->include_files();
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_filter( 'manage_liquid-booking_posts_columns', [ $this, 'post_column_list' ] );
		add_action( 'manage_liquid-booking_posts_custom_column' , [ $this, 'post_column_values' ], 10, 2 );
		add_filter( 'manage_edit-liquid-booking_sortable_columns', [ $this, 'post_column_sort' ] );
		add_filter( 'post_row_actions', [ $this, 'modify_list_row_actions' ], 10, 2 );

    } 
    
    public function include_files(){

		include_once LD_BOOKING_PATH . 'elementor/elementor.php';
		include_once LD_BOOKING_PATH . 'post-types/booking.php';
		include_once LD_BOOKING_PATH . 'classes/booking-actions.php';
		include_once LD_BOOKING_PATH . 'inc/options.php';

    }

	function post_column_sort( $columns ) {
		$columns['status'] = 'status';
		return $columns;
	}

	function post_column_list($columns) {

		unset( $columns['author'] );
		unset( $columns['date'] );

		
		$columns['title'] = 'ID';
		$columns['status'] = 'Status';
		$columns['detail'] = 'Details';
		$columns['contact'] = 'Contact';
		$columns['price'] = 'Total';
	
		
		return $columns;
	}

	function post_column_values( $column, $post_id ) {
		switch ( $column ) {
	
			case 'detail' :
				$booking_date = get_post_meta( $post_id, 'booking_date', true );
				$adult = get_post_meta( $post_id, 'adult', true );
				$child = get_post_meta( $post_id, 'child', true );
				$message = get_post_meta( $post_id, 'message', true );
				$hours = get_post_meta( $post_id, 'hours', true );
				if ( $hours ) {
					echo sprintf( '<span class="dashicons dashicons-calendar"></span> <strong>%s</strong> %s (%s)<br>', __('Booking Date:', 'hub-booking'), $booking_date, $hours );
				} else {
					echo sprintf( '<span class="dashicons dashicons-calendar"></span> <strong>%s</strong> %s - %s<br>', __('Booking Date:', 'hub-booking'), $booking_date[0], $booking_date[1] );
				}
				echo sprintf( '<span class="dashicons dashicons-admin-users"></span> <strong>%s</strong> %s (Adult: %s, Child: %s)<br>', __('Person:', 'hub-booking'), (intval( $adult ) + intval( $child )), $adult, $child );
				if ( !empty( $message ) ) {
					echo sprintf( '<span class="dashicons dashicons-admin-comments"></span> <strong>%s</strong> %s', __('Message:', 'hub-booking'), $message );
				}
			break;
			case 'contact' :
				$phone = get_post_meta( $post_id, 'phone', true );
				$email = get_post_meta( $post_id, 'email', true );
				$first_name = get_post_meta( $post_id, 'first_name', true );
				$last_name = get_post_meta( $post_id, 'last_name', true );
				echo sprintf( '<strong>%s %s</strong><br>', $first_name, $last_name );
				echo sprintf( '<span class="dashicons dashicons-phone"></span> <strong>%s</strong> %s<br><span class="dashicons dashicons-email"></span> <strong>%s</strong> %s', __('Phone:', 'hub-booking'), $phone, __('Email:', 'hub-booking'), $email );
			break;
			case 'status' :
				$status = get_post_meta( $post_id, 'status', true ); // pending, approved, unapproved, completed, cancelled
				echo sprintf( '<span class="hub-booking-status %1$s">%1$s</span>', $status );
			break;
			case 'price' :
				$price = get_post_meta( $post_id, 'price', true ); 
				$currency = get_post_meta( $post_id, 'currency', true );
				echo sprintf( '%s %s', $price, $currency );
			break;
	
		}
	}

	function modify_list_row_actions( $actions, $post ) {

		if ( $post->post_type == "liquid-booking" ) {

			// pending, approved, unapproved, completed, cancelled

			$actions = array(
				'approved' => sprintf( '<a href="javascript:void(0);" class="hub-booking-set-status" data-post-id="%1$s" data-set="approved">%2$s</a>',
					$post->ID,
					__('Approve','hub-booking')
				),
				'unapproved' => sprintf( '<a href="javascript:void(0);" class="hub-booking-set-status" data-post-id="%1$s" data-set="unapproved">%2$s</a>',
					$post->ID,
					__('Unapprove','hub-booking')
				),
				'completed' => sprintf( '<a href="javascript:void(0);" class="hub-booking-set-status" data-post-id="%1$s" data-set="completed">%2$s</a>',
					$post->ID,
					__('Complete','hub-booking')
				),
				'cancelled' => sprintf( '<a href="javascript:void(0);" class="hub-booking-set-status" data-post-id="%1$s" data-set="cancelled">%2$s</a>',
					$post->ID,
					__('Cancel','hub-booking')
				),
				'trash' => $actions['trash'],
			);

		}

		return $actions;

	}

	function enqueue_scripts() {
	
	}

	function admin_enqueue_scripts($hook) {

		wp_enqueue_script( 
			'hub-booking-admin', 
			LD_BOOKING_URL . 'assets/js/hub-booking-form-admin.js',
			['jquery'],
			null,
			true
		);
		
		wp_enqueue_style( 
			'hub-booking-admin', 
			LD_BOOKING_URL . 'assets/css/hub-booking-form-admin.css', 
			[],
			null
		);

		if( $hook == 'liquid-booking_page_booking_settings' ) {

			wp_enqueue_style( 'air-datepicker',
				LD_BOOKING_URL . 'assets/vendors/air-datepicker/air-datepicker.css',
				[],
				'3.4'
			);
		
			wp_enqueue_script( 'air-datepicker',
				LD_BOOKING_URL . 'assets/vendors/air-datepicker/air-datepicker.js',
				[ 'jquery' ],
				'3.4',
				true
			);

		}
		
		if ( $hook == 'liquid-booking_page_booking_calendar' ) {

			wp_enqueue_script( 'fullcalendar',
				LD_BOOKING_URL . 'assets/vendors/fullcalendar/index.global.min.js',
				[ 'jquery' ],
				'6.1.5',
				true
			);

			wp_enqueue_script( 'moment' );
		}

	}

    
} // class
Hub_Booking::instance();

// Pluging activation hook
function hub_booking_plugin_activate() { 

    flush_rewrite_rules(); // Removes rewrite rules and then recreate rewrite rules.

}
register_activation_hook( __FILE__, 'hub_booking_plugin_activate' );

// Pluging deactivation hook
function hub_booking_plugin_deactivate() {

    flush_rewrite_rules(); // Removes rewrite rules and then recreate rewrite rules.

}
register_deactivation_hook( __FILE__, 'hub_booking_plugin_deactivate' );
