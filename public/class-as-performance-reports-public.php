<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.facebook.com/anuragsingh.me
 * @since      1.0.0
 *
 * @package    As_Performance_Reports
 * @subpackage As_Performance_Reports/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    As_Performance_Reports
 * @subpackage As_Performance_Reports/public
 * @author     Anurag Singh <anuragsinghce@outlook.com>
 */
class As_Performance_Reports_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
//    public function __construct( $plugin_name, $version ) {
//
//        $this->plugin_name = $plugin_name;
//        $this->version = $version;
//
//    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in As_Performance_Reports_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The As_Performance_Reports_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jquery.dataTables.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in As_Performance_Reports_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The As_Performance_Reports_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( 'dataTables', plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );

        wp_enqueue_script( 'as-performance-reports-public', plugin_dir_url( __FILE__ ) . 'js/as-performance-reports-public.js', array( 'jquery' ), $this->version, false );




        wp_enqueue_script( 'ajax-script', plugins_url( '/js/my_query.js', __FILE__ ), array('jquery') );

		// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		wp_localize_script( 'ajax-script', 'ajax_object',
	            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
	

    }

    // Same handler function...
	function my_action_callback() {
		global $wpdb;

		$selctTotalCount = "SELECT COUNT('ID')
							FROM wp_performance_report
							";

		$whatever = $wpdb->get_results($selctTotalCount, ARRAY_N);

		// $whatever = intval( $_POST['whatever'] );
		// $whatever += 10;
	        echo $whatever[0][0];
	        // print_r ($whatever);
		wp_die();
	}

    
	        
		
}


