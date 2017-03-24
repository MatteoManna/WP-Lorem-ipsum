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

function li_load_textdomain() {
    load_plugin_textdomain( 'wp-lorem-ipsum', false, basename( dirname( __FILE__ ) ).'/languages' );
}
add_action( 'init', 'li_load_textdomain' );