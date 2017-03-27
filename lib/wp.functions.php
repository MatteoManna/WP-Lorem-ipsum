<?php
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