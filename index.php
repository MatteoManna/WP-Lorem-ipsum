<?php
/*
Plugin Name: WP Lorem ipsum
Description: Automatically create new fake posts to fill the database and get a very good impression for your website.
Author: Matteo Manna
Version: 2.4
Author URI: http://matteomanna.com/
Text Domain: wp-lorem-ipsum
License: GPL2
*/

/**
 * @param $plugin
 * @Usage: Redirect after Plugin activation
 */
function li_redirect_after_activation( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) exit( wp_redirect( admin_url('options-general.php?page=wp-lorem-ipsum') ) );
}
add_action( 'activated_plugin', 'li_redirect_after_activation' );

function li_admin_head_scripts() {
    wp_enqueue_media();
    wp_enqueue_style('li-css-style', plugins_url('css/style.css', __FILE__), array(), null);
    //wp_enqueue_script('li-js-custom', plugins_url('js/scripts.js', __FILE__), array(), '1.0', true);
}
add_action( 'admin_enqueue_scripts', 'li_admin_head_scripts' );

function li_add_action_links( $links ) {
    $new_links = array(
        '<a href="'.admin_url('options-general.php?page=wp-lorem-ipsum').'">'.__('Settings', 'wp-lorem-ipsum').'</a>',
    );
    return array_merge( $links, $new_links );
}
add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'li_add_action_links' );

function li_admin_menu() {
    add_options_page(
        __('WP Lorem ipsum settings', 'wp-lorem-ipsum'),
        __('WP Lorem ipsum', 'wp-lorem-ipsum'),
        'manage_options',
        'wp-lorem-ipsum',
        'li_admin_page'
    );
}
add_action( 'admin_menu', 'li_admin_menu' );

function li_get_nonce_string() {
    return 'matteomanna-wp-lorem-ipsum';
}

function li_get_authors() {
    global $wpdb;

    $query = "
        SELECT u.ID, u.user_login
        FROM {$wpdb->users} AS u
        WHERE 1=1
        ORDER BY u.user_login ASC
    ";
    $results = $wpdb->get_results( $query, OBJECT );

    return $results;
}

function li_admin_page() {
    $post_types = get_post_types( array( 'public' => true ), 'names', 'and' );
    unset( $post_types['attachment'] );
    $post_statuses = array( 'publish', 'draft' );
    ?>
    <div class="wrap">
        <?php if( isset($_GET['message']) && $_GET['message'] == 'success' ) : ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e('Operation performed successfully!', 'wp-lorem-ipsum'); ?></p>
            </div>
        <?php endif; ?>
        <h2><?php _e('WP Lorem ipsum', 'wp-lorem-ipsum'); ?></h2>
        <p><?php _e('You can create fake posts to fill your database.', 'wp-lorem-ipsum'); ?></p>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="li-form">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                    <tr>
                        <td><label for="post-count"><?php _e('Number of posts', 'wp-lorem-ipsum'); ?></label></td>
                        <td>
                            <select name="post_count" id="post-count" required="required">
                                <option value=""><?php _e('Select', 'wp-lorem-ipsum'); ?>...</option>
                                <?php for( $i=1; $i<=10; $i++ ) : ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="post-type"><?php _e('Post type', 'wp-lorem-ipsum'); ?></label></td>
                        <td>
                            <select name="post_type" id="post-type" required="required">
                                <option value=""><?php _e('Select', 'wp-lorem-ipsum'); ?>...</option>
                                <?php if( count($post_types) ) : ?>
                                    <?php foreach( $post_types as $post_type ) : ?>
                                        <option value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="paras"><?php _e('Paragraphs', 'wp-lorem-ipsum'); ?></label></td>
                        <td>
                            <select name="paras" id="paras" required="required">
                                <option value=""><?php _e('Select', 'wp-lorem-ipsum'); ?>...</option>
                                <option value="0"><?php _e('Random', 'wp-lorem-ipsum'); ?></option>
                                <?php for( $i=1; $i<=10; $i++ ) : ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="post-status"><?php _e('Post status', 'wp-lorem-ipsum'); ?></label></td>
                        <td>
                            <select name="post_status" id="post-status" required="required">
                                <option value=""><?php _e('Select', 'wp-lorem-ipsum'); ?>...</option>
                                <?php if( count($post_statuses) ) : ?>
                                    <?php foreach( $post_statuses as $post_status ) : ?>
                                        <option value="<?php echo $post_status; ?>"><?php echo $post_status; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="post-author"><?php _e('Post author', 'wp-lorem-ipsum'); ?></label></td>
                        <td>
                            <select name="post_author" id="post-author" required="required">
                                <option value=""><?php _e('Select', 'wp-lorem-ipsum'); ?>...</option>
                                <?php foreach( li_get_authors() as $post_author ) : ?>
                                    <option value="<?php echo (int)$post_author->ID; ?>"><?php echo $post_author->user_login; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="has-post-thumbnail"><?php _e('Post thumbnail', 'wp-lorem-ipsum'); ?></label></td>
                        <td>
                            <input type="checkbox" name="has_post_thumbnail" id="has-post-thumbnail" value="1" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="submit" class="button button-primary button-large"><?php  _e('Send', 'wp-lorem-ipsum'); ?></button>
            <input type="hidden" name="action" value="li_post_submit" />
            <?php wp_nonce_field( li_get_nonce_string(), '_wpnonce' );?>
        </form>
    </div>
    <?php
}

function li_get_content( $paras = 0 ) {
    $paras = ( isset($paras) && is_numeric($paras) && $paras > 0 ) ? (int)$paras : rand( 5, 10 );
    $url = esc_url( 'https://loripsum.net/api/'. $paras .'/medium/headers/decorate' );

    $response = wp_remote_get( $url );

    return $response['body'];
}

function li_get_attachment_id_from_src( $image_src = '' ) {
    global $wpdb;

    $image_src = str_replace( '-scaled', '', $image_src );

    $query = "
        SELECT p.ID
        FROM {$wpdb->posts} AS p
        WHERE 1=1
            AND p.guid ='{$image_src}'
        LIMIT 1
    ";
    $result = $wpdb->get_row( $query, OBJECT );

    return (int)$result->ID;
}

function li_post_attachment( $image_url = '', $post_id = 0 ) {
    // External random image URL
    $image_url = esc_url( $image_url . '.jpg' );

    // Upload Image in WordPress Media Library | image_url, post_id, description, return format
    $image_src = media_sideload_image( $image_url, $post_id, '', 'src' );

    // Set attachment on current Post
    $attachment_id = li_get_attachment_id_from_src( $image_src );
    set_post_thumbnail( $post_id, $attachment_id );

    // Update Attachment title
    $post = array(
        'ID' => $attachment_id,
        'post_title' => __('Attachment', 'wp-lorem-ipsum') . ' ' . $attachment_id
    );
    wp_update_post( $post );
}

function li_get_random_image_list( $post_count = 10 ) {
    $url = 'https://picsum.photos/v2/list';
    $url = add_query_arg( 'page', 1, $url );
    $url = add_query_arg( 'limit', $post_count, $url );

    $response = wp_remote_get( $url );
    $result = json_decode( $response['body'] );

    return $result;
}

function li_post_submit() {
    global $li_posts;

    if( !current_user_can('manage_options') ) :
        if( wp_die( __('Error.', 'wp-lorem-ipsum') ) ) exit;
    endif;

    if (
        isset( $_POST['_wpnonce'] )
        && wp_verify_nonce( $_POST['_wpnonce'], li_get_nonce_string() )
    ) :
        $redirect_ok = admin_url( 'options-general.php' );
        $redirect_ok = add_query_arg( 'page', 'wp-lorem-ipsum', $redirect_ok );
        $redirect_ok = add_query_arg( 'message', 'success', $redirect_ok );
        $random_image_list = li_get_random_image_list( $_POST['post_count'] );

        for( $i=1; $i<=$_POST['post_count']; $i++ ) :
            $post_content = li_get_content( $_POST['paras'] );
            $post_title = substr( sanitize_text_field($post_content), 0, rand( 25, 35 ) );
            $rand_start = rand( 50, 100 );
            $post_excerpt = ucfirst( substr( sanitize_text_field($post_content), $rand_start, 100 ) ) . '.';
            $has_post_thumbnail = ( isset($_POST['has_post_thumbnail']) && $_POST['has_post_thumbnail'] == 1 ) ? true : false ;

            $args = array(
                'post_author' => (int)$_POST['post_author'],
                'post_content' => $post_content,
                'post_title' => $post_title,
                'post_excerpt' => $post_excerpt,
                'post_status' => $_POST['post_status'],
                'post_type' => $_POST['post_type']
            );
            $post_id = wp_insert_post( $args );

            if( $has_post_thumbnail == true ) :
                $array_rand = array_rand( $random_image_list, 1 );
                $image_url = $random_image_list[$array_rand]->download_url;

                li_post_attachment( $image_url, $post_id );
            endif;
        endfor;

        exit( wp_redirect( $redirect_ok ) );
    else:
        // Error during insert
        if( wp_die( __('Error.', 'wp-lorem-ipsum') ) ) exit;
    endif;
}
add_action('admin_post_li_post_submit', 'li_post_submit');
//add_action('admin_post_nopriv_li_post_submit', 'li_post_submit');
