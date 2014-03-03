<?php
/*
  Plugin Name: Arconix FlexSlider
  Plugin URI: http://www.arconixpc.com/plugins/arconix-flexslider
  Description: A featured slider using WooThemes FlexSlider script.

  Author: John Gardner
  Author URI: http://www.arconixpc.com

  Version: 0.5.3

  License: GNU General Public License v2.0
  License URI: http://www.opensource.org/licenses/gpl-license.php
 */

class Arconix_FlexSlider {

    /**
     * Constructor
     *
     * @since 0.5
     * @version  0.6.0
     */
    function __construct() {
        $this->constants();

        add_action( 'wp_enqueue_scripts',   array( $this, 'load_scripts' ) );
        add_action( 'init',                 array( $this, 'register_shortcodes' ) );
        add_action( 'wp_dashboard_setup',   array( $this, 'register_dashboard_widget' ) );
    }

    /**
     * Define the constants
     *
     * @since 0.5
     */
    function constants() {
        define( 'ACFS_VERSION', '0.5.3');
        define( 'ACFS_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
        define( 'ACFS_INCLUDES_URL', trailingslashit( ACFS_URL . 'includes' ) );
        define( 'ACFS_IMAGES_URL', trailingslashit( ACFS_URL . 'images' ) );
        define( 'ACFS_JS_URL', trailingslashit( ACFS_INCLUDES_URL . 'js' ) );
        define( 'ACFS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
        define( 'ACFS_INCLUDES_DIR', trailingslashit( ACFS_DIR . 'includes' ) );
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
     * @since 0.1
     * @version 0.6.0
     */
    function load_scripts() {
        // Provide script registration args so they can be filtered if necessary
        $script_args = apply_filters( 'arconix_flexslider_reg', array(
            'url' => ACFS_JS_URL . 'jquery.flexslider-min.js',
            'ver' => '1.8',
            'dep' => 'jquery'
        ) );

        wp_register_script( 'flexslider', $script_args['url'], array( $script_args['dep'] ), $script_args['ver'], true );

        // Register the javascript - Check the child theme directory first, the parent theme second, otherwise load the plugin version
        if( apply_filters( 'pre_register_arconix_flexslider_js', true ) ) {
            if( file_exists( get_stylesheet_directory() . '/arconix-flexslider.js' ) )
                wp_register_script( 'arconix-flexslider-js', get_stylesheet_directory_uri() . '/arconix-flexslider.js', array( 'flexslider' ), ACFS_VERSION, true );
            elseif( file_exists( get_template_directory() . '/arconix-flexslider.js' ) )
                wp_register_script( 'arconix-flexslider-js', get_template_directory_uri() . '/arconix-flexslider.js', array( 'flexslider' ), ACFS_VERSION, true );
            else
                wp_register_script( 'arconix-flexslider-js', ACFS_JS_URL . 'flexslider.js', array( 'flexslider' ), ACFS_VERSION, true );
        }

        // Load the CSS - Check the child theme directory first, the parent theme second, otherwise load the plugin version
        if( apply_filters( 'pre_register_arconix_flexslider_css', true ) ) {
            if( file_exists( get_stylesheet_directory() . '/arconix-flexslider.css' ) )
                wp_enqueue_style( 'arconix-flexslider', get_stylesheet_directory_uri() . '/arconix-flexslider.css', false, ACFS_VERSION );
            elseif( file_exists( get_template_directory() . '/arconix-flexslider.css' ) )
                wp_enqueue_style( 'arconix-flexslider', get_template_directory_uri() . '/arconix-flexslider.css', false, ACFS_VERSION );
            else
                wp_enqueue_style( 'arconix-flexslider', ACFS_INCLUDES_URL . 'flexslider.css', false, ACFS_VERSION );
        }
         
    }

    /**
     * Register the plugin shortcode
     *
     * @since 0.5
     */
    function register_shortcodes() {
        add_shortcode( 'ac-flexslider', array( $this, 'flexslider_shortcode' ) );
    }

    /**
     * Flexslider Shortcode
     *
     * This shortcode return's the flexslider query lookup. The user can pass any accepted values as an attribute which will
     * be passed to the main query function
     *
     * @param type $atts
     * @param type $content self-enclosing shortcode
     * @since 0.5
     */
    function flexslider_shortcode( $atts, $content = null ) {
        return ARCONIX_FLEXSLIDER::get_flexslider_query( $atts );
    }

    /**
     * Returns flexslider query results
     *
     * @param array $args
     * @since 0.5
     * @version  0.6.0
     */
    function get_flexslider_query( $args, $echo = false ) {
        // Load the javascript if it hasn't been overridden
        if( wp_script_is( 'arconix-flexslider-js', 'registered' ) ) wp_enqueue_script( 'arconix-flexslider-js' );

        // Parse incomming $args into an array and merge it with $defaults
        $query_defaults = array(
            'post_type' => 'post',
            'category_name' => '',
            'tag' => '',
            'posts_per_page' => '5',
            'orderby' => 'date',
            'order' => 'DESC',
            'image_size' => 'medium',
            'image_link' => 1,
            'show_caption' => 'none',
            'show_content' => 'none'
        );
        $args = wp_parse_args( $args, $query_defaults );

        // Declare each item in $args as its own variable
        extract( $args, EXTR_SKIP );

        $query_args = array(
            'post_type' => $post_type,
            'posts_per_page' => $posts_per_page,
            'category_name' => $category_name,
            'tag' => $tag,
            'orderby' => $orderby,
            'order' => $order,
            'meta_key' => '_thumbnail_id' // Should pull only content with featured images
        );

        // Allow the query args to be filtered before the query is run
        $query_args = apply_filters( 'arconix_flexslider_query_args', $query_args );

        $fquery = new WP_Query( $query_args );

        $return = '';

        if ( $fquery->have_posts() ) {
            $return .= '<div class="flex-container">
                <div class="flexslider">
                <ul class="slides">';

            while ( $fquery->have_posts() ) : $fquery->the_post();

                $return .= '<li>';

                if( 'none' != $show_content )
                    $return .= '<div class="flex-image-wrap">';

                if( $image_link )
                    $return .= '<a href="' . get_permalink() . '" rel="bookmark">';

                $return .= get_the_post_thumbnail( get_the_ID(), $image_size );

                switch( $show_caption ) {
                    case 'post title':
                    case 'post-title':
                    case 'posttitle':
                        $return .= '<p class="flex-caption">' . get_the_title() . '</p>';
                        break;

                    case 'image title':
                    case 'image-title':
                    case 'imagetitle':
                        global $post;
                        $return .= '<p class="flex-caption">' . get_post( get_post_thumbnail_id( $post->ID ) )->post_title . '</p>';
                        break;

                    case 'image caption':
                    case 'image-caption':
                    case 'imagecaption':
                        global $post;
                        $return .= '<p class="flex-caption">' . get_post( get_post_thumbnail_id( $post->ID ) )->post_excerpt . '</p>';
                        break;

                    default:
                        break;
                }

                if( $image_link )
                    $return .= '</a>';

                if( 'none' != $show_content )
                    $return .= '</div>';

                if( 'none' != $show_content ) {
                    $return .= '<div class="flex-content-wrap">';
                    $return .= '<div class="flex-title"><a href="' . get_permalink() . '" rel="bookmark">' . get_the_title() . '</a></div>';
                    $return .= '<div class="flex-content">';

                    switch( $show_content ) {
                        case 'content':
                            $return .= get_the_content();
                            break;

                        case 'excerpt':
                            $return .= get_the_excerpt();
                            break;

                        default: // just in case
                            break;
                    }

                    $return .= '</div>';
                }

                $return .= '</li>';

            endwhile;

            $return .= '</ul></div></div>';
        }
        wp_reset_postdata();

        // Either echo or return the results
        if( $echo )
            echo $return;
        else
            return $return;
    }

    /**
     * Returns registered image sizes.
     *
     * @global array $_wp_additional_image_sizes Additionally registered image sizes
     * @return array Two-dimensional, with width, height and crop sub-keys
     * @since 0.1
     */
    function get_image_sizes() {

        global $_wp_additional_image_sizes;
        $additional_sizes = array();

        $builtin_sizes = array(
            'thumbnail' => array(
                'width' => get_option( 'thumbnail_size_w' ),
                'height' => get_option( 'thumbnail_size_h' ),
                'crop' => get_option( 'thumbnail_crop' ),
            ),
            'medium' => array(
                'width' => get_option( 'medium_size_w' ),
                'height' => get_option( 'medium_size_h' ),
            ),
            'large' => array(
                'width' => get_option( 'large_size_w' ),
                'height' => get_option( 'large_size_h' ),
            )
        );

        if( $_wp_additional_image_sizes )
        $additional_sizes = $_wp_additional_image_sizes;

        return array_merge( $builtin_sizes, $additional_sizes );
    }

    /**
     * Return a modified list of Post Types
     *
     * This function is primarily geared towards developers who do work for clients and want to restrict
     * the post types visible in the widget drop down. The default list includes the 2 WordPress post
     * types plus the post type for the popular plugin Contact Form 7. The list can be filtered to 
     * add any other desired post types
     *
     * @return type array Post Types
     * @since 0.1
     * @version 0.5
     */
    function get_modified_post_type_list() {
        $post_types = get_post_types( '', 'names' );

        /* Post types we want excluded from the drop down */
        $excl_post_types = apply_filters( 'acfs_exclude_post_types',
            array(
                'revision',
                'nav_menu_item',
                'wpcf7_contact_form'
            )
        );

        /** Loop through and exclude the items in the list */
        foreach( $excl_post_types as $excl_post_type ) {
            if( isset( $post_types[$excl_post_type] ) ) unset( $post_types[$excl_post_type] );
        }

        return $post_types;
    }

    /**
     * Register the dashboard widget
     *
     * @since 0.1
     */
    function acfs_register_dashboard_widget() {
        wp_add_dashboard_widget( 'ac-flexslider', 'Arconix FlexSlider', 'acfs_dashboard_widget_output' );
    }

    /**
     * Output for the dashboard widget
     *
     * @since 0.1
     * @version 0.6
     */
    function acfs_dashboard_widget_output() {
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

new Arconix_FlexSlider;