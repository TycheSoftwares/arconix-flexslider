<?php
/**
 * Register the Slider Widget
 *
 * @since 0.1
 */
function register_acfs_widget() {
    register_widget( 'Arconix_FlexSlider_Widget' );
}

/**
 * FlexSlider Widget
 *
 * @since 0.1
 */
class Arconix_FlexSlider_Widget extends WP_Widget {

    /**
     * Holds widget settings defaults, populated in constructor.
     *
     * @var array
     * @since 0.1
     */
    protected $defaults;

    /**
     * Constructor. Set the default widget options, create widget, and load the js
     *
     * @since 0.1
     * @version 0.5
     */
    function __construct() {

	$this->defaults = array(
	    'title'             => '',
	    'post_type'         => 'post',
            'posts_per_page'    => 5,
            'orderby'           => 'date',
            'order'             => 'DESC',
            'image_size'        => 'thumbnail',
            'image_link'        => true,
            'show_caption'      => 'none',
            'show_content'      => 'none'
	);

        $widget_ops = array(
            'classname'         => 'flexslider_widget',
            'description'       => __( 'Responsive slider able to showcase any post type', 'acfs' ),
        );

        $control_ops = array(
	    'id_base'           => 'arconix-flexslider-widget'
	);

        $this->WP_Widget( 'arconix-flexslider-widget', 'Arconix - FlexSlider', $widget_ops, $control_ops );
    }

    /**
     * Widget Output
     *
     * @param type $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param type $instance The settings for the particular instance of the widget
     * @since 0.1
     * @version 0.5
     */
    function widget( $args, $instance ) {

	extract( $args, EXTR_SKIP );

	/* Merge with defaults */
	$instance = wp_parse_args( ( array )$instance, $this->defaults );

	/* Before widget (defined by themes) */
	echo $before_widget;

	/* Title of widget (before and after defined by themes) */
	if( ! empty( $instance['title'] ) )
	    echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;

        /* Run our query and output our results */
        flexslider_query( $instance );

        /* After widget (defined by themes) */
        echo $after_widget;


    }

    /**
     * Update a particular instance.
     *
     * @param array $new_instance New settings for this instance as input by the user via form()
     * @param array $old_instance Old settings for this instance
     * @return array Settings to save or bool false to cancel saving
     * @since 0.1
     * @version 0.5
     */
    function update( $new_instance, $old_instance ) {
        $isntance = $old_instance;
	$instance['title'] = strip_tags( $new_instance['title'] );
	$instance['posts_per_page'] = (int) $new_instance['posts_per_page'];
        $instance['image_link'] = !empty( $new_instance['image_link'] ) ? 1 : 0;

	return $new_instance;
    }

    /**
     * Widget form
     *
     * @param array $instance Current settings
     * @since 0.1
     * @version 0.5
     */
    function form( $instance ) {

	/* Merge with defaults */
	$instance = wp_parse_args( (array) $instance, $this->defaults );
	?>

	<!-- Title: Input Box -->
	<p>
	    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'acfs' ); ?>:</label>
	    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
	</p>
	<!-- Post Type: Select Box -->
	<p>
	    <label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type', 'acfs' ); ?>:</label>
	    <select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
		<?php
		$types = get_modified_post_type_list();
		foreach( $types as $type )
		    echo '<option value="' . $type . '" ' . selected( $type, $instance['post_type'], FALSE ) . '>' . $type . '</option>';
		?>
	    </select>
        </p>
        <!-- Posts Number: Input Box -->
	<p>
	    <label for="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>"><?php _e( 'Number of posts to show:', 'acfs' ); ?></label>
	    <input id="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'posts_per_page' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['posts_per_page'] ); ?>" size="3" /></p>
	</p>
        <!-- Orderby: Select Box -->
	<p>
	    <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Select Orderby', 'acfs' ); ?></label>
	    <select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
		<?php
		$orderby_items = array( 'ID', 'author', 'title', 'name', 'date', 'modified', 'rand', 'comment_count', 'menu_order' );
		foreach( $orderby_items as $orderby_item )
		    echo '<option value="' . $orderby_item . '" ' . selected( $orderby_item, $instance['orderby'], FALSE ) . '>' . $orderby_item . '</option>';
		?>
	    </select>
	</p>
        <!-- Order: Select Box -->
	<p>
	    <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Select Order', 'acfs' ); ?></label>
	    <select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
		<?php
		$order_items = array( 'ASC', 'DESC' );
		foreach( $order_items as $order_item )
		    echo '<option value="' . $order_item . '" ' . selected( $order_item, $instance['order'], FALSE ) . '>' . $order_item . '</option>';
		?>
	    </select>
	</p>
	<!-- Image Size: Select Box -->
	<p>
	    <label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'Image Size', 'acfs' ); ?>:</label>
	    <select id="<?php echo $this->get_field_id( 'image_size' ); ?>" name="<?php echo $this->get_field_name( 'image_size' ); ?>">
		<?php
		$sizes = get_image_sizes();
		foreach( (array) $sizes as $name => $size )
		    echo '<option value="' . $name . '" ' . selected( $name, $instance['image_size'], FALSE ) . '>' . esc_html( $name ) . ' ( ' . $size['width'] . 'x' . $size['height'] . ' )</option>';
		?>
	    </select>
	</p>
        <!-- Image Link: Checkbox -->
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $instance['image_link'], true ) ?> id="<?php echo $this->get_field_id( 'image_link' ); ?>" name="<?php echo $this->get_field_name( 'image_link' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'image_link' ); ?>"><?php _e( 'Hyperlink the image to permalink', 'acfs' ); ?></label>
        </p>
        <!-- Show Caption: Select Box -->
	<p>
	    <label for="<?php echo $this->get_field_id( 'show_caption' ); ?>"><?php _e( 'Show caption', 'acfs' ); ?></label>
	    <select id="<?php echo $this->get_field_id( 'show_caption' ); ?>" name="<?php echo $this->get_field_name( 'show_caption' ); ?>">
		<?php
		$captions = array( 'none', 'post title', 'image title', 'image caption' );
		foreach( $captions as $caption )
		    echo '<option value="' . esc_attr( $caption ) . '" ' . selected( $caption, $instance['show_caption'], FALSE ) . '>' . esc_html( $caption ) . '</option>';
		?>
	    </select>
	</p>
        <!-- Show Content: Select Box -->
	<p>
	    <label for="<?php echo $this->get_field_id( 'show_content' ); ?>"><?php _e( 'Show content', 'acfs' ); ?></label>
	    <select id="<?php echo $this->get_field_id( 'show_content' ); ?>" name="<?php echo $this->get_field_name( 'show_content' ); ?>">
		<?php
		$content_items = array( 'none', 'excerpt', 'content' );
		foreach( $content_items as $content_item )
		    echo '<option value="' . $content_item . '" ' . selected( $content_item, $instance['show_content'], FALSE ) . '>' . $content_item . '</option>';
		?>
	    </select>
	</p>



	<?php
    }


}

?>