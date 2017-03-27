<?php
if( !class_exists('LiPosts') ) {
    class LiPosts
    {
        public function __construct()
        {

        }

        /**
         * @param string $key
         * @return int
         */
        public function get_count_post_limit( $key = 'min' )
        {
            $array = array(
                'min' => 5,
                'max' => 50
            );
            $return = ( isset($array[$key]) && is_numeric($array[$key]) ) ? (int)$array[$key] : 0 ;

            return $return;
        }

        /**
         * @param array $args:
         *      - @post_number: 5 <=> 50. Default 5
         *      - @post_type: Default post
         *      - @post_status: publish, future, draft, pending, private, trash, auto-draft, inherit. Default publish
         *      - @post_author: Default current user ID
         *      - @has_post_thumbnail: Default true
         * @return array
         */
        public function insert_posts( $args = array() )
        {
            // Posts number: find "min" and "max" from dedicated function. Default "min"
            $posts_number = (
                isset($args['posts_number'])
                && is_numeric($args['posts_number'])
                && (
                    $args['posts_number'] >= $this->get_count_post_limit('min')
                    && $args['posts_number'] <= $this->get_count_post_limit('max')
                )
            ) ? (int)$args['posts_number'] : $this->get_count_post_limit('min') ;

            // Post type, default post
            $post_type = ( isset($args['post_type']) && !empty($args['post_type']) ) ? sanitize_text_field($args['post_type']) : 'post' ;

            // Post status: Publish, Future, Draft, Pending, Private, Trash, Auto-Draft, Inherit. Default Publish
            $post_status = ( isset($args['post_status']) && !empty($args['post_status']) ) ? sanitize_text_field($args['post_status']) : 'publish' ;

            // Post author: default current user ID
            $post_author = ( isset($args['post_author']) && is_numeric($args['post_author']) && $args['post_author'] > 0 ) ? (int)$args['post_author'] : get_current_user_id() ;

            // Has post thumbnail, default true
            $has_post_thumbnail = ( isset($args['has_post_thumbnail']) && is_bool($args['has_post_thumbnail']) ) ? $args['has_post_thumbnail'] : true ;

            // All new posts IDs, init array
            $posts_ids = array();

            for ( $i = 1; $i <= (int)$posts_number; $i++ ) {
                $post = array(
                    'post_type' => $post_type,
                    'post_status' => $post_status,
                    'post_title' => wp_strip_all_tags( li_get_rand_content('post_title') ),
                    'post_content' => li_get_rand_content('post_content'),
                    'post_excerpt' => li_get_rand_content('post_excerpt'),
                    'post_author' => (int)$post_author
                );
                $post_id = wp_insert_post( $post );

                // If has post Thumbnails
                if ( $has_post_thumbnail == true ) {
                    // Get Class LiThumbnails
                    global $li_thumbnails;

                    $args = array(
                        'post_id' => $post_id,
                        'width' => 300,
                        'height' => 200
                    );
                    $li_thumbnails->get_thumbnail($args);
                }

                // Update array with new post ID
                $posts_ids[] = $post_id;
            }

            return $posts_ids;
        }
    }
    // Init Class
    $li_posts = new LiPosts();
}