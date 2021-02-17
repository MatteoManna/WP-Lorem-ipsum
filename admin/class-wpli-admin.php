<?php
if ( ! class_exists( 'WPLoremIpsum_Admin' ) ) :

    /**
     * PHP class WPLoremIpsum_Admin
     *
     * @since   2.9
     */
    class WPLoremIpsum_Admin {

        public function __construct() {

            // Scripts for wp-admin
            add_action( 'admin_enqueue_scripts', [ $this, 'custom_admin_enqueue_scripts' ] );

            // Admin Menu
            add_action( 'admin_menu', [ $this, 'custom_admin_menu' ] );

            // Actions for post call
            add_action( 'admin_post_post_submit', [ $this, 'post_submit_save' ] );

        }


        public static function init() {

            new self;

        }


        /**
         * Load CSS
         *
         * @since   1.0
         */
        public function custom_admin_enqueue_scripts() {

            // CSS Style
            wp_enqueue_style( 'wpli', plugins_url( 'css/style.css', __FILE__ ), [], WPLI_PLUGIN_VERSION, 'all' );

        }


        /**
         * Menu button in wp-admin
         * Contains callback render_admin_page()
         *
         * @since   1.0
         */
        public function custom_admin_menu() {

            add_options_page(
                __('WP Lorem ipsum settings', 'wp-lorem-ipsum'),
                __('WP Lorem ipsum', 'wp-lorem-ipsum'),
                'manage_options',
                'wp-lorem-ipsum',
                [ $this, 'render_admin_page' ]
            );

        }


        /**
         * Render admin page with HTML
         *
         * @since   1.0
         */
        public function render_admin_page() {

            $post_types = get_post_types( [ 'public' => true ], 'names', 'and' );
            unset( $post_types['attachment'] );
            $post_statuses = [ 'publish', 'draft' ];
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
                                        <?php for ( $i = 1; $i <= 10; $i++ ) : ?>
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
                                        <?php if( is_array($post_types) && count($post_types) ) : ?>
                                            <?php foreach ( $post_types as $post_type ) : ?>
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
                                        <?php for ( $i = 1; $i <= 10; $i++ ) : ?>
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
                                        <?php if ( is_array($post_statuses) && count($post_statuses) ) : ?>
                                            <?php foreach ( $post_statuses as $post_status ) : ?>
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
                                        <?php foreach ( $this->get_authors() as $post_author ) : ?>
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
                    <input type="hidden" name="action" value="post_submit" />
                    <?php wp_nonce_field( get_bloginfo('name'), '_wpnonce' ); ?>
                </form>
            </div>
            <?php

        }


        /**
         * Save call
         *
         * @since   1.0
         */
        public function post_submit_save() {

            if( ! current_user_can('manage_options') )
                wp_die( __('Error.', 'wp-lorem-ipsum') );

            // Nonce verify
            if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], get_bloginfo('name') ) ) :

                // Redirect page
                $redirect_ok = admin_url( 'options-general.php' );
                $redirect_ok = add_query_arg( 'page', 'wp-lorem-ipsum', $redirect_ok );
                $redirect_ok = add_query_arg( 'message', 'success', $redirect_ok );

                // Check if posts will have post thumbnail
                $has_post_thumbnail = ( isset($_POST['has_post_thumbnail']) && $_POST['has_post_thumbnail'] == 1 ) ? true : false ;

                // Get random images list
                if ( $has_post_thumbnail == true ) :

                    $random_image_list = $this->get_random_image_list( $_POST['post_count'] );

                endif;

                // Insert posts
                for ( $i = 1; $i <= $_POST['post_count']; $i++ ) :

                    $post_content = $this->get_content( $_POST['paras'] );
                    $post_title = substr( sanitize_text_field($post_content), 0, rand( 25, 35 ) );
                    $rand_start = rand( 50, 100 );
                    $post_excerpt = ucfirst( substr( sanitize_text_field($post_content), $rand_start, 100 ) ) . '.';

                    // Insert post
                    $args = [
                        'post_author' => (int)$_POST['post_author'],
                        'post_content' => $post_content,
                        'post_title' => $post_title,
                        'post_excerpt' => $post_excerpt,
                        'post_status' => $_POST['post_status'],
                        'post_type' => $_POST['post_type']
                    ];
                    $post_id = wp_insert_post( $args );

                    // Insert post thumbnail
                    if ( $has_post_thumbnail == true ) :

                        $array_rand = array_rand( $random_image_list, 1 );
                        $image_url = $random_image_list[$array_rand]->download_url;

                        $this->post_attachment( $image_url, $post_id );

                    endif;

                endfor;

                exit( wp_redirect( $redirect_ok ) );

            else:

                // Error during insert
                if( wp_die( __('Error.', 'wp-lorem-ipsum') ) ) exit;

            endif;

        }


        /**
         * Get authors from WordPress database
         *
         * @since   1.0
         */
        private function get_authors() {

            global $wpdb;

            // The query
            $query = "
                SELECT u.ID, u.user_login
                FROM {$wpdb->users} AS u
                WHERE 1=1
                ORDER BY u.user_login ASC
            ";
            $results = $wpdb->get_results( $query, OBJECT );

            return $results;

        }


        /**
         * Get content from an external service
         * External service provided by loripsum.net
         *
         * @since   1.0
         */
        private function get_content( $paras = 0 ) {

            $paras = ( isset($paras) && is_numeric($paras) && $paras > 0 ) ? (int)$paras : rand( 5, 10 );
            $url = esc_url( "https://loripsum.net/api/{$paras}/medium/headers/decorate" );

            $response = wp_remote_get( $url );

            return wp_remote_retrieve_body( $response );

        }


        /**
         * Retrieve attachment_id from an URL
         *
         * @since   1.0
         */
        private function get_attachment_id_from_src( $image_src = '' ) {

            global $wpdb;

            // Prevent the -scaled option
            $image_src = str_replace( '-scaled', '', $image_src );

            // The query
            $query = "
                SELECT p.ID
                FROM {$wpdb->posts} AS p
                WHERE 1=1
                    AND p.guid = '{$image_src}'
                LIMIT 1
            ";
            $result = $wpdb->get_row( $query, OBJECT );

            return (int)$result->ID;

        }


        /**
         * Set the post attachment
         *
         * @since   1.0
         */
        private function post_attachment( $image_url = '', $post_id = 0 ) {

            // External random image URL
            $image_url = esc_url( $image_url . '.jpg' );

            // Upload Image in WordPress Media Library | image_url, post_id, description, return format
            $image_src = media_sideload_image( $image_url, $post_id, '', 'src' );

            // Set thumbnail_id
            $thumbnail_id = $this->get_attachment_id_from_src( $image_src );

            // Set attachment on current Post
            set_post_thumbnail( $post_id, $thumbnail_id );

        }


        /**
         * Get a random image list
         *
         * @since   1.0
         */
        private function get_random_image_list( $post_count = 10 ) {

            $url = 'https://picsum.photos/v2/list';
            $url = add_query_arg( 'page', 1, $url );
            $url = add_query_arg( 'limit', $post_count, $url );

            $response = wp_remote_get( esc_url( $url ) );
            $result = json_decode( wp_remote_retrieve_body( $response ) );

            return $result;

        }

    }

    add_action( 'plugins_loaded', [ 'WPLoremIpsum_Admin', 'init' ] );

endif;
