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

require('class.posts.php');
require('class.thumbnails.php');


function li_load_textdomain()
{
    load_plugin_textdomain('wp-lorem-ipsum', false, basename(dirname(__FILE__)) . '/languages');
}
add_action( 'init', 'li_load_textdomain' );




/**
 * Test
 */
function li_insert_posts()
{
    global $li_posts;

    $args = array(
        'posts_number' => 2
    );
    $posts_ids = $li_posts->wp_insert_post($args);

    print_r($posts_ids);

    ob_flush();
    exit;
}
add_action('wp_ajax_li_insert_posts', 'li_insert_posts');
add_action('wp_ajax_nopriv_li_insert_posts', 'li_insert_posts');