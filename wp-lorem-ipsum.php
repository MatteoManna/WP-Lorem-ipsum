<?php
/**
 * WP Lorem Ipsum
 *
 * Plugin Name: WP Lorem Ipsum
 * Plugin URI: https://wordpress.org/plugins/wp-lorem-ipsum/
 * Description: Automatically create <strong>new fake posts</strong> to fill the database and get a very good impression for your website.
 * Version: 3.2
 * Author: Matteo Manna
 * Author URI: https://matteomanna.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-lorem-ipsum
 */

// Injection prevention
if ( !defined( 'ABSPATH' ) )
    exit;

/**
 * Global defines
 *
 * @since   2.9
 */
if ( ! defined( 'WPLI_PLUGIN_VERSION' ) )
    define( 'WPLI_PLUGIN_VERSION', 3.2 );

// Admin Class
if ( is_admin() )
    require_once plugin_dir_path( __FILE__ ) . 'admin/class-wpli-admin.php';
