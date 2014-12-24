<?php

class Arconix_FlexSlider {

    /**
     * Holds loop defaults, populated in constructor.
     *
     * @since   1.0.0
     * @var     array   $defaults   defaults
     */
    protected $defaults;

    /**
     * Constructor
     *
     * @since   0.5.0
     * @version 1.0.0
     */
    function __construct() {
        $this->defaults = apply_filters( 'arconix_flexslider_function_defaults', array(
            'type'              => 'slider',
            'post_type'         => 'post',
            'category_name'     => '',
            'tag'               => '',
            'posts_per_page'    => 5,
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
     * Returns Flexslider query results in an unordered list of slides
     *
     * @since   0.1.0
     * @version 1.0.0
     * @param   array   $args       Incoming query arguments
     * @param   bool    $echo       Echo or return results
     * @return  string  $return     Slider slides
     */
    function loop( $args, $echo = false ) {

        $args = wp_parse_args( $args, $this->getdefaults() );

        // Last chance to change any arguments before the query is run
        $query_args = apply_filters( 'arconix_flexslider_loop_args', array(
            'post_type'         => $args['post_type'],
            'posts_per_page'    => $args['posts_per_page'],
            'category_name'     => $args['category_name'],
            'tag'               => $args['tag'],
            'orderby'           => $args['orderby'],
            'order'             => $args['order']
        ) );

        $query = new WP_Query( $query_args );

        $return = '';

        if ( $query->have_posts() ) {
            $return .= '<div class="owl-carousel arconix-' . $args['type'] . '">';

            while ( $query->have_posts() ) : $query->the_post();

                $return .= '<div>';

                $return .= $this->slide_content( get_the_ID(), $args );

                $return .= '</div>';

            endwhile;

            $return .= '</div>';
        }
        wp_reset_postdata();

        // Either echo or return the results
        if( $echo === true )
            echo $return;
        else
            return $return;
    }

    /**
     * Get the slide content including image, caption, and content
     *
     * @since   1.0.0
     * @param   int     $id         WP Post object ID
     * @param   array   $args       Loop arguments. Will pull class defaults if parameter is not supplied or is not an array
     * @param   bool    $echo       Echo or return the content
     * @return  string  $s          Concatenated string containing the slide content
     */
    public function slide_content( $id, $args, $echo = false ) {
        if ( empty( $id ) ) $id = get_the_ID();
        if ( empty( $args ) || ! is_array( $args ) ) $args = $this->getdefaults();

        $s = '';

        if ( 'none' != $args['show_content'] )
            $s .= '<div class="flex-image-wrap">';

        $s .= $this->slide_image( $id, $args['image_link'], $args['image_size'], $args['show_caption'] );

        if ( 'none' != $args['show_content'] ) {
            $s .= '</div>';
            $s .= '<div class="flex-content-wrap">';
            $s .= '<div class="flex-title"><a href="' . get_permalink() . '" rel="bookmark">' . get_the_title() . '</a></div>';
            $s .= '<div class="flex-content">';

            switch( $args['show_content'] ) {
                case 'content':
                    $s .= get_the_content();
                    break;

                case 'excerpt':
                    $s .= get_the_excerpt();
                    break;

                default: // just in case
                    break;
            }

            $s .= '</div>';
        }


        if ( $echo === true )
            echo $s;
        else
            return $s;
    }

    /**
     * Get the slide image
     *
     * @since   1.0.0
     * @param   int     $id             WP Post ID
     * @param   bool    $image_link     Wrap the image in a hyperlink to the permalink
     * @param   string  $image_size     The size of the image to display. Accepts any valid WordPress image
     * @param   string  $show_caption   Caption to be displayed
     * @param   bool    $echo           Echo or return the results
     * @return  string  $s              Slide image
     */
    public function slide_image( $id, $image_link, $image_size, $caption, $echo = false ) {
        if ( empty( $id ) ) $id = get_the_ID();

        $s = '';

        if ( $image_link )
            $s .= '<a href="' . get_permalink() . '" rel="bookmark">';

        if ( has_post_thumbnail() )
            $s .= get_the_post_thumbnail( $id, $image_size );

        $s .= $this->slide_caption( $id, $caption );

        if ( $image_link )
            $s .= '</a>';


        if ( $echo === true )
            echo $s;
        else
            return $s;
    }

    /**
     * Get the slide caption. Returns early if the caption will not be displayed
     *
     * @since   1.0.0
     * @param   int     $id         WP Post ID
     * @param   string  $caption    The type of image caption to display
     * @param   bool    $echo       Echo or return the results
     * @return  string  $s          Slide caption wrapped in a paragraph tag
     */
    public function slide_caption( $id, $caption, $echo = false ) {
        if ( empty( $id ) ) $id = get_the_ID();

        if ( empty( $caption ) ) return;

        switch( $caption ) {
            case 'post title':
            case 'post-title':
            case 'posttitle':
                $s = '<p class="flex-caption">' . get_the_title() . '</p>';
                break;

            case 'image title':
            case 'image-title':
            case 'imagetitle':
                $s = '<p class="flex-caption">' . get_post( get_post_thumbnail_id( $id ) )->post_title . '</p>';
                break;

            case 'image caption':
            case 'image-caption':
            case 'imagecaption':
                $s = '<p class="flex-caption">' . get_post( get_post_thumbnail_id( $id ) )->post_excerpt . '</p>';
                break;

            default:
                $s = '';
                break;
        }

        if ( $echo === true )
            echo $s;
        else
            return $s;
    }



}