<?php

/**
 * Load the necessary javascript and css
 * These files can be overwritten by including files in your theme's directory
 *
 * @since 0.1
 */
function load_scripts() {
    wp_register_script( 'flexslider', ACFS_INCLUDES_URL . 'js/jquery.flexslider-min.js', array( 'jquery' ), '1.8', true );

    /* Allow user to override javascript by including his own */
    if( file_exists( get_stylesheet_directory() . "/arconix-flexslider.js" ) ) {
	wp_register_script( 'arconix-flexslider-js', get_stylesheet_directory_uri() . '/arconix-flexslider.js', array( 'flexslider' ), ACFS_VERSION, true );
    }
    elseif( file_exists( get_template_directory() . "/arconix-flexslider.js" ) ) {
	wp_register_script( 'arconix-flexslider-js', get_template_directory_uri() . '/arconix-flexslider.js', array( 'flexslider' ), ACFS_VERSION, true );
    }
    else {
	wp_register_script( 'arconix-flexslider-js', ACFS_INCLUDES_URL . 'js/flexslider.js', array( 'flexslider' ), ACFS_VERSION, true );
    }

    /* Allow user to override css by including his own */
    if( file_exists( get_stylesheet_directory() . "/arconix-flexslider.css" ) ) {
	wp_enqueue_style( 'arconix-flexslider', get_stylesheet_directory_uri() . '/arconix-flexslider.css', array(), ACFS_VERSION );
    }
    elseif( file_exists( get_template_directory() . "/arconix-flexslider.css" ) ) {
	wp_enqueue_style( 'arconix-flexslider', get_template_directory_uri() . '/arconix-flexslider.css', array(), ACFS_VERSION );
    }
    else {
	wp_enqueue_style( 'arconix-flexslider', ACFS_INCLUDES_URL . 'flexslider.css', array(), ACFS_VERSION );
    }
}

/**
 * Check the state of the variable. If true, load the registered javascript
 *
 * @since 0.5
 */
function print_script() {
    if( !self::$load_flex_js )
        return;

    wp_print_scripts( 'arconix-flexslider-js' );
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
            'attachment',
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
 * Flexslider Shortcode
 *
 * @param type $atts
 * @param type $content
 * @since 0.5
 */
function flexslider_shortcode( $atts, $content = null ) {
    //wp_enqueue_script( 'arconix-flexslider-js' );

    $atts = shortcode_atts( array(
        'post_type' => 'post',
        'posts_per_page' => 5,
        'orderby' => 'date',
        'order' => 'DESC',
        'image_size' => 'thumbnail',
        'image_link' => true,
        'show_caption' => 'none',
        'show_content' => 'none'
    ) );

    return get_flexslider_query( $atts );
}

/**
 * Returns flexslider query results
 *
 * @param type $args array
 * @since 0.5
 */
function get_flexslider_query( $args = '' ) {
    /* Load the javascript */
    self::$load_flex_js = true;

    $defaults = array(
        'post_type' => 'post',
        'posts_per_page' => 5,
        'orderby' => 'date',
        'order' => 'DESC',
        'image_size' => 'thumbnail',
        'image_link' => true,
        'show_caption' => 'none',
        'show_content' => 'none'
    );

     /* Parse incomming $args into an array and merge it with $defaults */
    $args = wp_parse_args( $args, $defaults );

    /* Declare each item in $args as its own variable */
    extract( $args, EXTR_SKIP );

    $query_args = array(
        'post_type' => $post_type,
        'posts_per_page' => $posts_per_page,
        'orderby' => $orderby,
        'order' => $order,
        'meta_key' => '_thumbnail_id' // Should pull only content with featured images
    );

    $fquery = new WP_Query( $query_args );

    $return = '';

    if ( $fquery->have_posts() ) {
        $return .= '<div class="flex-container">
            <div class="flexslider">
            <ul class="slides">';

        while ( $fquery->have_posts() ) : $fquery->the_post();

            $return .= '<li>';

            if( $image_link )
                $return .= '<a href="' . get_permalink() . '" rel="bookmark">';

            get_the_post_thumbnail( get_the_ID(), $image_size );

            switch( $show_caption ) {
                case 'post title':
                    $return .= '<p class="flex-caption">' . get_the_title() . '</p>';
                    break;

                case 'image title':
                    global $post;
                    $return .= '<p class="flex-caption">' . get_post( get_post_thumbnail_id( $post->ID ) )->post_title . '</p>';
                    break;

                case 'image caption':
                    global $post;
                    $return .= '<p class="flex-caption">' . get_post( get_post_thumbnail_id( $post->ID ) )->post_excerpt . '</p>';
                    break;

                default:
                    break;
            }

            if( $image_link )
                $return .= '</a>';

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

    return $return;
}

/**
 * Display flexslider query results
 *
 * @param type $args
 * @since 0.5
 */
function flexslider_query( $args = '' ) {
    $flex = get_flexslider_query( $args );

    echo $flex;
}

?>