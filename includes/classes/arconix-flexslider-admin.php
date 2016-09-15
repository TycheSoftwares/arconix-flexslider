<?php
/**
 * The Admin Class for Arconix Flexslider
 *
 * Handles the backend registration work for scripts, shortcodes and widgets
 *
 * @since  1.0.0
 */
class Arconix_Flexslider_Admin {

    /**
     * The url path to this plugin.
     *
     * @since   1.0.0
     * @access  private
     * @var     string      $url    The url path to this plugin
     */
    private $url;


    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     * @param   string      $version    The version of this plugin.
     */
    public function __construct() {
        $this->url = trailingslashit( plugin_dir_url( dirname( __FILE__ ) ) );

        add_action( 'admin_enqueue_scripts',    array( $this, 'admin_scripts' ) );
        add_action( 'widgets_init',             array( 'Arconix_Flexslider_Widget', 'register' ) );
        add_action( 'wp_dashboard_setup',       array( $this, 'register_dashboard_widget' ) );
    }

    /**
     * Load the administrative CSS
     *
     * Styles the dashboard widget. Can be prevented from adding theme support
     * 
     * @example add_theme_support( 'arconix-flexslider', 'admin-css' );
     *
     * @since   1.0.0
     */
    public function admin_scripts() {
        if( ! current_theme_supports( 'arconix-flexslider', 'admin-css') && apply_filters( 'pre_register_arconix_flexslider_admin_css', true ) )
            wp_enqueue_style( 'arconix-flexslider-admin', $this->url . 'css/admin.css', false, Arconix_Flexslider_Plugin::version );
    }
    
    /**
     * Register the dashboard widget
     *
     * @since   0.1
     */
    public function register_dashboard_widget() {
        if( apply_filters( 'pre_register_arconix_flexslider_dashboard_widget', true ) and
            apply_filters( 'arconix_flexslider_dashboard_widget_security', current_user_can( 'manage_options' ) ) )
                wp_add_dashboard_widget( 'ac-flexslider', 'Arconix FlexSlider', array( $this, 'dashboard_widget_output' ) );
    }

    /**
     * Output for the dashboard widget
     *
     * @since   0.1
     * @version 1.0.0
     */
    public function dashboard_widget_output() {
        echo '<div class="rss-widget">';

        wp_widget_rss_output( array(
            'url' => 'http://arconixpc.com/tag/arconix-flexslider/feed', // feed url
            'title' => 'Arconix FlexSlider Posts', // feed title
            'items' => 3, // how many posts to show
            'show_summary' => 1, // display excerpt
            'show_author' => 0, // display author
            'show_date' => 1 // display post date
        ) );

        echo '<div class="acfs-widget-bottom"><ul>
                  <li><a href="http://arcnx.co/afswiki" class="afs-docs">Documentation</a></li>
                  <li><a href="http://arcnx.co/afshelp" class="afs-help">Support</a></li>
                  <li><a href="http://arcnx.co/afstrello" class="afs-dev">Dev Board</a></li>
                  <li><a href="http://arcnx.co/afssource" class="afs-source">Source Code</a></li>
                </ul></div></div>';
    }

}