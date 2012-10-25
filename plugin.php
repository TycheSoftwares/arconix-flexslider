<?php
/*
  Plugin Name: Arconix FlexSlider Dev
  Plugin URI: http://www.arconixpc.com/plugins/arconix-flexslider
  Description: A featured slider using WooThemes FlexSlider script.
  Author: John Gardner
  Author URI: http://www.arconixpc.com

  Version: 0.5

  License: GNU General Public License v2.0
  License URI: http://www.opensource.org/licenses/gpl-license.php
 */

class Arconix_FlexSlider {
    
    /**
     * Constructor
     * 
     * @since 0.5
     */
    function __construct() {        
        add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );

        $this->hooks();
        
        register_activation_hook( __FILE__, array( $this, 'activation' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

        add_shortcode( 'ac-flexslider', array( $this, 'flexslider_shortcode' ) );   
    }    
    
    /**
     * Define the plugin constants
     * 
     * @since 0.5
     */
    function constants() {        
        define( 'ACFS_VERSION', '0.5');        
        define( 'ACFS_URL', plugin_dir_url( __FILE__ ) );
        define( 'ACFS_INCLUDES_URL', ACFS_URL . 'includes' );
        define( 'ACFS_DIR', plugin_dir_path( __FILE__ ) );
        define( 'ACFS_INCLUDES_DIR', ACFS_DIR . 'includes' );        
    }    
    
    /**
     * Run the necessary functions and pull in the necessary supporting files
     * 
     * @since 0.5
     */
    function hooks() {
        add_action( 'wp_enqueue_scripts', 'load_scripts' );
        add_action( 'widgets_init', 'register_widget' );
        add_action( 'wp_dashboard_setup', 'register_dashboard_widget' ); 
        
        require_once( ACFS_INCLUDES_DIR . 'functions.php' );
        require_once( ACFS_INCLUDES_DIR . 'widget.php' );
        if( is_admin() )
            require_once( ACFS_INCLUDES_DIR . 'admin.php' );
    }    
    
}

new Arconix_FlexSlider;