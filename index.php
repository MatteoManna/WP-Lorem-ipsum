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

/**
 * Automatically require all library files
 */
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

function li_load_textdomain()
{
    load_plugin_textdomain( 'wp-lorem-ipsum', false, basename(dirname(__FILE__)) . '/languages' );
}
add_action( 'init', 'li_load_textdomain' );

function li_admin_head_scripts()
{
    wp_enqueue_media();
    wp_enqueue_style('li-css-style', plugins_url('css/style.css', __FILE__), array(), null);
    wp_enqueue_script('li-js-custom', plugins_url('js/scripts.js', __FILE__), array(), '1.0', true);
}
add_action( 'admin_enqueue_scripts', 'li_admin_head_scripts' );

/**
 * @param $links
 * @return array
 */
function li_add_action_links($links)
{
    $new_links = array(
        '<a href="'.admin_url('options-general.php?page=wp-lorem-ipsum').'">'.__('Settings', 'wp-lorem-ipsum').'</a>',
    );
    return array_merge( $links, $new_links );
}
add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'li_add_action_links' );

function li_admin_menu()
{
    add_options_page(
        __('WP Lorem ipsum settings', 'wp-lorem-ipsum'),
        __('WP Lorem ipsum', 'wp-lorem-ipsum'),
        'manage_options',
        'wp-lorem-ipsum',
        'li_admin_page'
    );
}
add_action( 'admin_menu', 'li_admin_menu' );

function li_admin_page()
{
    ?>
    <div class="wrap">
        <h2><?php echo __('WP Lorem ipsum', 'wp-lorem-ipsum'); ?></h2>
        <p><?php echo __('You can create fake posts to fill your database.', 'wp-lorem-ipsum'); ?></p>
    </div>
    <?php
}

function li_insert_posts()
{
    global $li_posts;

    $args = array(
        'posts_number' => 5
    );
    $posts_ids = $li_posts->insert_posts($args);

    print_r($posts_ids);

    ob_flush();
    exit;
}
add_action( 'wp_ajax_li_insert_posts', 'li_insert_posts' );
add_action( 'wp_ajax_nopriv_li_insert_posts', 'li_insert_posts' );