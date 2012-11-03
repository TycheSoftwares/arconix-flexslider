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
     * Variable flag for loading the javascript
     *
     * @var type boolean
     * @since 0.5
     */
    static $load_flex_js;


    /**
     * Constructor
     *
     * @since 0.5
     */
    function __construct() {

        define( 'ACFS_VERSION', '0.5');
        define( 'ACFS_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
        define( 'ACFS_INCLUDES_URL', trailingslashit( ACFS_URL . 'includes' ) );
        define( 'ACFS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
        define( 'ACFS_INCLUDES_DIR', trailingslashit( ACFS_DIR . 'includes' ) );

        $this->hooks();
    }

    /**
     * Run the necessary functions and pull in the necessary supporting files
     *
     * @since 0.5
     */
    function hooks() {
        add_action( 'wp_enqueue_scripts', 'load_scripts' );
        add_action( 'init', 'register_shortcodes' ) ;
        add_action( 'widgets_init', 'register_acfs_widget' );
        add_action( 'wp_dashboard_setup', 'register_dashboard_widget' );
        add_action( 'wp_footer', 'print_scripts' );

        require_once( ACFS_INCLUDES_DIR . 'functions.php' );
        require_once( ACFS_INCLUDES_DIR . 'widget.php' );
        if( is_admin() )
            require_once( ACFS_INCLUDES_DIR . 'admin.php' );
    }

}

new Arconix_FlexSlider;
?>