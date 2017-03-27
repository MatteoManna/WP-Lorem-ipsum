<?php
/*
Plugin Name: WP Lorem ipsum
Description: Automatically create new fake posts to fill the database and get a very good impression for your website.
Author: Matteo Manna
Version: 1.0
Author URI: http://matteomanna.com/
Text Domain: wp-lorem-ipsum
License: GPL2
*/

// Automatically require all library files
$lib_path = dirname(__FILE__).'/lib/';
if ( file_exists( $lib_path ) ) {
    $open = opendir( $lib_path );
    while( false !== ( $file = readdir( $open ) ) ) if ( $file != '.' && $file != '..' ) require('lib/'.$file);
}

/**
 * @param $plugin
 * @Usage: Redirect after Plugin activation
 */
function li_redirect_after_activation( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) exit( wp_redirect( admin_url('options-general.php?page=wp-lorem-ipsum') ) );
}
add_action( 'activated_plugin', 'li_redirect_after_activation' );