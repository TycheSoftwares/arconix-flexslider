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
     * The version of this plugin.
     *
     * @since   1.0.0
     * @access  private
     * @var     string      $version    The vurrent version of this plugin.
     */
    private $version;

    /**
     * The directory path to this plugin.
     *
     * @since   1.0.0
     * @access  private
     * @var     string      $dir    The directory path to this plugin
     */
    private $dir;

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
    public function __construct( $version ) {
        $this->version = $version;
        $this->dir = trailingslashit( plugin_dir_path( __FILE__ ) );
        $this->url = trailingslashit( plugin_dir_url( __FILE__ ) );

        add_action( 'wp_enqueue_scripts',   array( $this, 'scripts' ) );
        add_action( 'widgets_init',         array( 'Arconix_Flexslider_Widget', 'register' ) );
        add_action( 'wp_dashboard_setup',   array( $this, 'register_dashboard_widget' ) );

        add_shortcode( 'ac-flexslider',     array( $this, 'flexslider_shortcode' ) );
    }

    /**
     * Register the necessary Javascript and CSS, which can be overridden in 3 different ways.
     * If you'd like to use a different version of the Flexslider script {@link https://github.com/woothemes/FlexSlider/releases}
     * you can add a filter that overrides the the url, version and dependency.
     *
     * If you would like to bundle the Javacsript or CSS funtionality into another file and prevent either of the plugin files
     * from loading at all, return false to the desired pre_register filters
     *
     * @example add_filter( 'pre_register_arconix_flexslider_js', '__return_false' );
     *
     * If you'd like to modify the Javascript or CSS that is used by the plugin, you can copy the arconix-flexslider.js
     * or arconix-flexslider.css files to the root of your theme's folder. That will be loaded in place of the plugin's
     * version, which means you can modify it to your heart's content and know the file will be safe when the plugin
     * is updated in the future.
     *
     * @link Codex reference: apply_filters()
     * @link Codex reference: wp_register_script()
     * @link Codex reference: get_stylesheet_directory()
     * @link Codex reference: get_stylesheet_directory_uri()
     * @link Codex reference: get_template_directory()
     * @link Codex reference: get_template_directory_uri()
     * @link Codex reference: wp_enqueue_style()
     *
     * @since   0.1
     * @version 1.0.0
     */
    public function scripts() {
        // Provide script registration args so they can be filtered if necessary
        $script_args = apply_filters( 'arconix_flexslider_reg', array(
            'url' => $this->url . 'js/jquery.flexslider-min.js',
            'ver' => '2.2.2',
            'dep' => 'jquery'
        ) );

        wp_register_script( 'flexslider', $script_args['url'], array( $script_args['dep'] ), $script_args['ver'], true );

        // Register the javascript - Check the child theme directory first, the parent theme second, otherwise load the plugin version
        if( apply_filters( 'pre_register_arconix_flexslider_js', true ) ) {
            if( file_exists( get_stylesheet_directory() . '/arconix-flexslider.js' ) )
                wp_register_script( 'arconix-flexslider-js', get_stylesheet_directory_uri() . '/arconix-flexslider.js', array( 'flexslider' ), $this->version, true );
            elseif( file_exists( get_template_directory() . '/arconix-flexslider.js' ) )
                wp_register_script( 'arconix-flexslider-js', get_template_directory_uri() . '/arconix-flexslider.js', array( 'flexslider' ), $this->version, true );
            else
                wp_register_script( 'arconix-flexslider-js', $this->url . 'js/arconix-flexslider.js', array( 'flexslider' ), $this->version, true );
        }

        // Load the CSS - Check the child theme directory first, the parent theme second, otherwise load the plugin version
        if( apply_filters( 'pre_register_arconix_flexslider_css', true ) ) {
            if( file_exists( get_stylesheet_directory() . '/arconix-flexslider.css' ) )
                wp_enqueue_style( 'arconix-flexslider', get_stylesheet_directory_uri() . '/arconix-flexslider.css', false, $this->version );
            elseif( file_exists( get_template_directory() . '/arconix-flexslider.css' ) )
                wp_enqueue_style( 'arconix-flexslider', get_template_directory_uri() . '/arconix-flexslider.css', false, $this->version );
            else
                wp_enqueue_style( 'arconix-flexslider', $this->url . 'css/arconix-flexslider.css', false, $this->version );
        }

    }

    /**
     * Flexslider Shortcode
     *
     * This shortcode return's the flexslider query lookup. The user can pass any accepted values as an attribute which will
     * be passed to the main query function
     *
     * @param type $atts    shortcode arguments
     * @param type $content self-enclosing shortcode
     *
     * @since    0.5
     * @version  1.0.0
     */
    public function flexslider_shortcode( $atts, $content = null ) {
        // Load the javascript if it hasn't been overridden
        if( wp_script_is( 'arconix-flexslider-js', 'registered' ) ) wp_enqueue_script( 'arconix-flexslider-js' );

        $fs = new Arconix_Flexslider;

        return $fs->loop( $atts );
    }

    /**
     * Register the dashboard widget
     *
     * @since 0.1
     */
    public function register_dashboard_widget() {
        if( apply_filters( 'pre_register_arconix_flexslider_dashboard_widget', true ) and
            apply_filters( 'arconix_flexslider_dashboard_widget_security', current_user_can( 'manage_options' ) ) )
                wp_add_dashboard_widget( 'ac-flexslider', 'Arconix FlexSlider', array( $this, 'dashboard_widget_output' ) );
    }

    /**
     * Output for the dashboard widget
     *
     * @since 0.1
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
                  <li><a href="http://arcnx.co/afshelp" class="afs-help">Support Forum</a></li>
                  <li><a href="http://arcnx.co/afstrello" class="afs-dev">Dev Board</a></li>
                  <li><a href="http://arcnx.co/afssource" class="afs-source">Source Code</a></li>
                </ul></div></div>';
    }

}