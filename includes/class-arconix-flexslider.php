<?php

class Arconix_FlexSlider {

    /**
     * Constructor
     *
     * @since 0.5
     */
    function __construct() {
        
    }

    /**
     * Handles the plugin's default arguments
     *
     * Can be overridden by accessing the filter
     * 
     * @return array default args
     */
    function defaults() {
        $d = array(
            'post_type'         => 'post',
            'category_name'     => '',
            'tag'               => '',
            'posts_per_page'    => '5',
            'orderby'           => 'date',
            'order'             => 'DESC',
            'image_size'        => 'medium',
            'image_link'        => 1,
            'show_caption'      => 'none',
            'show_content'      => 'none'
        );

        return apply_filters( 'arconix_flexslider_function_defaults', $d );
    }
    
    /**
     * Returns Flexslider query results
     * 
     * @param  array   $args incoming query arguments
     * @param  boolean $echo echo or return results
     * @return string        flexslider slides
     */
    function loop( $args, $echo = false ) {        

        $defaults = $this->defaults();

        $args = wp_parse_args( $args, $defaults );

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
        $query_args = apply_filters( 'arconix_flexslider_loop_args', $query_args );

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

    

}