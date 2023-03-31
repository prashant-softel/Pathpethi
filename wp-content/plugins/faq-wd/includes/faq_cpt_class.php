<?php

class faq_cpt {

    protected static $instance = null;
    public static $post_type = 'faq_wd';
    public static $taxonomy = 'faq_category';
    public static $taxonomy_slug = 'faq_category';

    private function __construct() {
        add_action('init', array($this, 'faq_custom_post_type'));
        add_action('init', array($this, 'create_category'), 0);
        add_action('post_updated', array($this, 'uncategorized_questions'));
        add_action('save_post', array($this, 'post_order_meta'));
        add_filter('the_post', array($this, 'close_comments'));
        add_action('admin_notices', array($this, 'admin_notices'), 10, 1);
    }

    public function post_order_meta($post_id) {
        if (get_post_type($post_id) == 'faq_wd') {
            add_post_meta($post_id, 'faqwd_order', 0, true);
        }
    }

    public function close_comments($post) {
        global $faqwd_options;
        if (isset($post) && $post->post_type == 'faq_wd') {
            if (isset($faqwd_options['enable_comments']) && $faqwd_options['enable_comments'] == 1) {
                $post->comment_status = 'open';
            } else {
                $post->comment_status = 'closed';
            }
        }
        return $post;
    }

    public function faq_custom_post_type() {
        global $faqwd_options;
        $comment_support = array();
        if (isset($faqwd_options['enable_comments']) && $faqwd_options['enable_comments'] == 1) {
            $comment_support[] = 'comments';
        }
        $labels = array('name' => 'FAQ',
            'add_new' => 'Add Question',
            'add_new_item' => 'Add Question',
            'search_items' => 'Search Question'
        );
        $args = array('labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'taxonomies' => array('faq_category'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_icon' => FAQ_URL . 'images/icon-FAQ.png',
            'supports' => array_merge(array('title',
                'editor',
                'thumbnail'
                    ), $comment_support),
            'rewrite' => array('slug' => 'faqwd')
        );
        register_post_type(self::$post_type, $args);
        flush_rewrite_rules();
    }

    public function create_category() {
        $labels = array('name' => 'FAQ Categories',
            'search_items' => 'Search Category',
            'parent_item' => 'Category',
            'parent_item_colon' => 'Question',
            'edit_item' => 'Edit Category',
            'update_item' => 'Update Category',
            'add_new_item' => 'Add New Category',
            'new_item_name' => 'New Genre Category',
            'menu_name' => 'FAQ Categories'
        );
        $args = array(
            'hierarchical' => false,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => self::$taxonomy_slug),
        );
        register_taxonomy(self::$taxonomy, array(self::$post_type), $args);
        flush_rewrite_rules();
    }

    public function notification($post_id) {
        $notice = get_option('faqwd_notice');
        $notice[$post_id] = "Your themes folder is not writable";
        update_option('faqwd_notice', $notice);
    }

    public function admin_notices() {
        global $post_id;
        if ($post_id) {
            $notice = get_option('faqwd_notice');
            if (isset($notice[$post_id])) {
                echo '<div class="error">
                        <p>' . __($notice[$post_id], "faqwd") . '</p>
                     </div>';
                unset($notice[$post_id]);
                update_option('faqwd_notice', $notice);
            }
        }
    }

    public function uncategorized_questions($post_id) {

        if (get_post_type($post_id) == self::$post_type) {

            if (!isset($_POST['tax_input']['faq_category'])) {
                wp_set_post_terms($post_id, array(), self::$taxonomy);
            }
            
            $post = get_post($post_id);
            $post_cats = wp_get_post_terms($post_id, self::$taxonomy);
            
            if (empty($post_cats)) {                    
                if (get_term_by('slug', 'uncategorized', self::$taxonomy) == false) {
                    $cat_id = wp_insert_term('Uncategorized', self::$taxonomy, array('slug' => 'uncategorized'));
                }
                $term = get_term_by('slug', 'uncategorized', self::$taxonomy);
                $term_id = $term->term_id;                
                wp_set_post_terms($post_id, array($term_id), self::$taxonomy);
            }
        }
    }

    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}
