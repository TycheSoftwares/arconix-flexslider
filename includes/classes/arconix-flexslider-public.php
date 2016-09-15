<?php
/**
 * The Public Class for Arconix Flexslider
 *
 * Handles the front-end registration work for scripts, shortcodes and widgets
 *
 * @since  1.0.0
 */
class Arconix_Flexslider_Public {

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

        add_action( 'wp_enqueue_scripts',   array( $this, 'scripts' ) );
        
        add_filter( 'the_posts',            array( $this, 'conditional_styles' ) );

        add_shortcode( 'ac-flexslider',     array( $this, 'flexslider_shortcode' ) );
    }

    /**
     * Register the necessary Javascript.
     *
     * If you would like to bundle the Javacsript funtionality into another file and prevent the plugin files
     * from loading at all, add theme support for javascript
     *
     * @example add_theme_support( 'arconix-flexslider', 'js' );
     *
     * If you'd like to modify the Javascript that is used by the plugin, you can copy the arconix-flexslider.js
     * file to the root of your theme's folder. That will be loaded in place of the plugin's
     * version, which means you can modify it to your heart's content and know the file will be safe when the plugin
     * is updated in the future.
     *
     * @since   0.1
     * @version 1.0.0
     */
    public function scripts() {
        // Provide script registration args so they can be filtered if necessary
        $script_args = apply_filters( 'arconix_flexslider_reg', array(
            'url' => $this->url . 'js/owl.carousel.min.js',
            'ver' => '1.3.2',
            'dep' => 'jquery'
        ) );

        wp_register_script( 'owl-carousel', $script_args['url'], array( $script_args['dep'] ), $script_args['ver'], true );
        
        // Register the javascript - Check the child theme directory first, the parent theme second, otherwise load the plugin version
        if ( ! current_theme_supports( 'arconix-flexslider', 'js') && apply_filters( 'pre_register_arconix_flexslider_js', true ) ) {
            if ( file_exists( get_stylesheet_directory() . '/arconix-flexslider.js' ) )
                wp_register_script( 'arconix-flexslider-js', get_stylesheet_directory_uri() . '/arconix-flexslider.js', array( 'owl-carousel' ), Arconix_Flexslider_Plugin::version, true );
            elseif ( file_exists( get_template_directory() . '/arconix-flexslider.js' ) )
                wp_register_script( 'arconix-flexslider-js', get_template_directory_uri() . '/arconix-flexslider.js', array( 'owl-carousel' ), Arconix_Flexslider_Plugin::version, true );
            else
                wp_register_script( 'arconix-flexslider-js', $this->url . 'js/arconix-flexslider.js', array( 'owl-carousel' ), Arconix_Flexslider_Plugin::version, true );
        }
    }
    
    /**
     * Conditional load of CSS
     * 
     * Loops through all the posts and checks for the [ac-flexslider] shortcode, loading the CSS if found
     * 
     * @since   
     * @param   array       $posts      List of posts
     * @return  array                   List of posts
     */
    public function conditional_load_css( $posts ){
        if ( empty( $posts ) || is_admin() ) return $posts;
        
        $found = false;
        
        foreach ( $posts as $post ) {
            if ( has_shortcode( $post->post_content, 'ac-flexslider' ) ) {
                $found = true;
                break;
            }
        }
        
        if ( $found )
            add_action( 'wp_enqueue_scripts', array( $this, 'load_css' ) );    
        
        return $posts;
    }
    
    /**
     * Enqueue the the CSS
     *
     * If you would like to bundle the CSS funtionality into another file and prevent the plugin's CSS from loading
     * at all, add theme support
     * 
     * @example add_theme_support( 'arconix-flexslider', 'css' );
     *
     * If you'd like to use your own CSS file, you can copy the arconix-flexslider.css files to the
     * root of your theme's folder. That will be loaded in place of the plugin's version, which means you can modify
     * it to your heart's content and know the file will be safe when the plugin is updated in the future.
     *
     * @since   
     */
    public function load_css() {
        // Load the CSS - Check the child theme directory first, the parent theme second, otherwise load the plugin version
        if ( ! current_theme_supports( 'arconix-flexslider', 'css') && apply_filters( 'pre_register_arconix_flexslider_css', true ) ) {
            if ( file_exists( get_stylesheet_directory() . '/arconix-flexslider.css' ) )
                wp_enqueue_style( 'arconix-flexslider', get_stylesheet_directory_uri() . '/arconix-flexslider.css', false, Arconix_Flexslider_Plugin::version );
            elseif ( file_exists( get_template_directory() . '/arconix-flexslider.css' ) )
                wp_enqueue_style( 'arconix-flexslider', get_template_directory_uri() . '/arconix-flexslider.css', false, Arconix_Flexslider_Plugin::version );
            else
                wp_enqueue_style( 'arconix-flexslider-css', $this->url . 'css/arconix-flexslider.css', false, Arconix_Flexslider_Plugin::version );
        }
    }


    /**
     * Flexslider Shortcode
     *
     * This shortcode return's the flexslider query lookup. The user can pass any accepted values as an attribute which will
     * be passed to the main query function
     *
     * @since   0.5
     * @version 1.0.0
     * @param   array   $atts       shortcode arguments
     * @param   null    $content    self-enclosing shortcode
     */
    public function flexslider_shortcode( $atts, $content = null ) {
        // Load the javascript if it hasn't been overridden
        if( wp_script_is( 'arconix-flexslider-js', 'registered' ) ) wp_enqueue_script( 'arconix-flexslider-js' );

        $fs = new Arconix_Flexslider;

        return $fs->loop( $atts );
    }

}