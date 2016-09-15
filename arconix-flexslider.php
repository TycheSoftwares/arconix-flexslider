<?php
/*
  Plugin Name: Arconix FlexSlider
  Plugin URI: http://www.arconixpc.com/plugins/arconix-flexslider
  Description: A multi-purpose responsive jQuery slider that supports custom post types and responsive themes.

  Author: John Gardner
  Author URI: http://www.arconixpc.com

  Version: 1.0.1

  License: GPLv2 or later
  License URI: http://www.opensource.org/licenses/gpl-license.php
 */


// Set our plugin activation hook
register_activation_hook( __FILE__, 'activate_arconix_flexslider' );

function activate_arconix_faq() {
    require_once plugin_dir_path( __FILE__ ) . '/includes/classes/arconix-flexslider-activator.php';
    Arconix_Flexslider_Activator::activate();
}

// Register the autoloader
spl_autoload_register( 'arconix_flexslider_autoloader' );

/**
 * Class Autoloader
 * 
 * @param	string	$class_name		Class to check to autoload
 * @return	null                    Return if it's not a valid class
 */
function arconix_flexslider_autoloader( $class_name ) {
	/**
	 * If the class being requested does not start with our prefix,
	 * we know it's not one in our project
	 */
	if ( 0 !== strpos( $class_name, 'Arconix_' ) ) {
		return;
	}

	$file_name = str_replace(
		array( 'Arconix_', '_' ),	// Prefix | Underscores 
		array( '', '-' ),           // Remove | Replace with hyphens
		strtolower( $class_name )	// lowercase
	);

	// Compile our path from the current location
	$file = dirname( __FILE__ ) . '/includes/classes/' . $file_name . '.php';

	// If a file is found, load it
	if ( file_exists( $file ) ) {
		require_once( $file );
	}
}


final class Arconix_Flexslider_Plugin {

    /**
     * Current version of the plugin.
     *
     * @since   1.0.0
     * @access  private
     * @var     string      $version    Current plugin version
     */
    const version = '1.0.1';
    
    /**
     * Load the plugin instructions
     * 
     * @since   
     */
	public function init() {
        $this->load_public();
        
        if ( is_admin() ) {
            $this->load_admin();
        }
	}
    
    /**
     * Load the Public-facing components of the plugin
     * 
     * @since   1.7.0
     */
    private function load_public() {
        $p = new Arconix_Flexslider_Public();
        
        $p->init();
    }

    /**
     * Load the Administration portion
     *
     * @since   1.0.0
     */
    private function load_admin() {
        new Arconix_Flexslider_Admin();
    }

}

/** Vroom vroom */
add_action( 'plugins_loaded', 'arconix_flexslider_plugin_run' );
function arconix_flexslider_plugin_run() {
    $f = new Arconix_Flexslider_Plugin;
    
    $f->init();
}