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

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-arconix-flexslider.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-arconix-flexslider-admin.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-arconix-flexslider-widget.php' );

new Arconix_Flexslider_Admin;