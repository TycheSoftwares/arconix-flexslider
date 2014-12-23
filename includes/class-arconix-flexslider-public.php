<?php

class Arconix_FlexSlider {

    /**
     * Holds loop defaults, populated in constructor.
     *
     * @var     array   $defaults   defaults
     *
     * @since   1.0.0
     */
    protected $defaults;

    /**
     * Constructor
     *
     * @since   0.5
     * @version 1.0.0
     */
    function __construct() {
        $this->defaults = apply_filters( 'arconix_flexslider_function_defaults', array(
            'type'              => 'slider',
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
        ) );
    }

    /**
     * Get the loop defaults
     *
     * @since   1.0.0
     * @return  array   $defaults   default args
     */
    public function getdefaults() {
        return $this->defaults;
    }

    /**
     * Returns Flexslider query results
     *
     * @todo    Break up if() statements into more manageable sub functions
     *
     * @since   0.1.0
     * @param   array   $args       incoming query arguments
     * @param   bool    $echo       echo or return results
     * @return  string              flexslider slides
     */
    function loop( $args, $echo = false ) {

        $defaults = $this->getdefaults();

        $args = wp_parse_args( $args, $defaults );

        // Declare each item in $args as its own variable
        //extract( $args, EXTR_SKIP );

        $query_args = array(
            'post_type'         => $args['post_type'],
            'posts_per_page'    => $args['posts_per_page'],
            'category_name'     => $args['category_name'],
            'tag'               => $args['tag'],
            'orderby'           => $args['orderby'],
            'order'             => $args['order']
        );

        // Allow the query args to be filtered before the query is run
        $query_args = apply_filters( 'arconix_flexslider_loop_args', $query_args );

        $fquery = new WP_Query( $query_args );

        $return = '';

        if ( $fquery->have_posts() ) {
            $return .= '<div class="arconix-flexslider-' . $args['type'] . '"><div class="flexslider">
                <ul class="slides">';

            while ( $fquery->have_posts() ) : $fquery->the_post();

                $return .= '<li>';

                if ( 'none' != $args['show_content'] )
                    $return .= '<div class="flex-image-wrap">';

                if ( $args['image_link'] )
                    $return .= '<a href="' . get_permalink() . '" rel="bookmark">';

                if ( has_post_thumbnail )
                    $return .= get_the_post_thumbnail( get_the_ID(), $args['image_size'] );

                switch( $args['show_caption'] ) {
                    case 'post title':
                    case 'post-title':
                    case 'posttitle':
                        $return .= '<p class="flex-caption">' . get_the_title() . '</p>';
                        break;

                    case 'image title':
                    case 'image-title':
                    case 'imagetitle':
                        $return .= '<p class="flex-caption">' . get_post( get_post_thumbnail_id( $fquery->ID ) )->post_title . '</p>';
                        break;

                    case 'image caption':
                    case 'image-caption':
                    case 'imagecaption':
                        $return .= '<p class="flex-caption">' . get_post( get_post_thumbnail_id( $fquery->ID ) )->post_excerpt . '</p>';
                        break;

                    default:
                        break;
                }

                if ( $args['image_link'] )
                    $return .= '</a>';

                //if( 'none' != $args['show_content'] )

                if ( 'none' != $args['show_content'] ) {
                    $return .= '</div>';
                    $return .= '<div class="flex-content-wrap">';
                    $return .= '<div class="flex-title"><a href="' . get_permalink() . '" rel="bookmark">' . get_the_title() . '</a></div>';
                    $return .= '<div class="flex-content">';

                    switch( $args['show_content'] ) {
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
        if( $echo === true )
            echo $return;
        else
            return $return;
    }



    public function slider_caption() {

    }

    public function slider_content() {

    }

    public function slider_image() {
        
    }



}