<?php

/**
 * Register the dashboard widget
 * 
 * @since 0.1 
 */
function register_dashboard_widget() {
    wp_add_dashboard_widget( 'ac-flexslider', 'Arconix FlexSlider', 'dashboard_widget_output' );
}

/**
 * Output for the dashboard widget
 *
 * @since 0.1
 */
function dashboard_widget_output() {
    echo '<div class="rss-widget">';

    wp_widget_rss_output( array(
        'url' => 'http://arconixpc.com/tag/arconix-flexslider/feed', // feed url
        'title' => 'Arconix FlexSlider Posts', // feed title
        'items' => 3, // how many posts to show
        'show_summary' => 1, // display excerpt
        'show_author' => 0, // display author
        'show_date' => 1 // display post date
    ) );

    echo '<div class="acfs-widget-bottom"><ul>';
    ?>
        <li><img src="<?php echo ACFS_URL . 'images/page-16x16.png'; ?>"><a href="http://arcnx.co/afswiki">Wiki Page</a></li>
        <li><img src="<?php echo ACFS_URL . 'images/help-16x16.png'; ?>"><a href="http://arcnx.co/afshelp">Support Forum</a></li>
        <li><img src="<?php echo ACFS_URL . 'images/trello-16x16.png'; ?>"><a href="http://arcnx.co/aftrello">Dev Board</a></li>
    <?php
    echo '</ul></div></div>';

    // handle the styling
    echo '<style type="text/css">
            #ac-flexslider .rsssummary { display: block; }
            #ac-flexslider .acfs-widget-bottom { border-top: 1px solid #ddd; padding-top: 10px; text-align: center; }
            #ac-flexslider .acfs-widget-bottom ul { list-style: none; }
            #ac-flexslider .acfs-widget-bottom ul li { display: inline; padding-right: 9%; }
            #ac-flexslider .acfs-widget-bottom img { padding-right: 3px; vertical-align: middle; }
        </style>';
}

?>