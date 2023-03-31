<?php

class faq_admin_class {

    protected static $instance = null;
    public $shortcode_tag = 'faq_wd';
    public $post_type = 'faq_wd';
    public $version = '1.0.0';

    private function __construct() {
        if (is_admin()) {
            add_action('admin_menu', array($this, 'faq_wd_submenu'));
            foreach (array('post.php', 'post-new.php') as $hook) {
                add_action('admin_head-' . $hook, array($this, 'faqwd_head'));
            }
            add_action('admin_head', array($this, 'admin_head'));
//categories            
            add_action('create_faq_category', array($this, 'create_faq_category'));
            add_action('admin_head-edit-tags.php', array($this, 'faqwd_categories_js'));
            add_action('admin_menu', array($this, 'fawd_remove_category_meta_box'));
            add_action('add_meta_boxes', array($this, 'faqwd_add_meta_box'));
            add_action('admin_init', array($this, 'FAQWD_add_category_ordering'));
            add_action('delete_faq_category', array($this, 'delete_faq_category'));
//posts
            add_action('pre_get_posts', array($this, 'FAQWD_get_posts'));
//ajax
            add_action('wp_ajax_faqwd_sotable', array($this, 'faqwd_sotable'));
            add_action('wp_ajax_faqwd_category_sotable', array($this, 'faqwd_category_sotable'));
//scripts
            add_action('admin_enqueue_scripts', array($this, 'include_admin_style'));
            add_action('admin_enqueue_scripts', array($this, 'include_admin_scripts'));
//notices
            add_action('admin_notices', array($this, 'admin_notices'), 10, 1);
            add_action('admin_notices', array($this, 'create_logo_to_head'));
        }
    }

    public function faq_wd_submenu() {
        add_submenu_page('edit.php?post_type=faq_wd', 'FAQ Themes', 'Themes', 'manage_options', 'theme', array($this, 'theme_submenu'));
        add_submenu_page('edit.php?post_type=faq_wd', 'Settings', 'Settings', 'manage_options', 'faq_wd_settings', array($this,
            'faq_wd_settings'
        ));
        add_submenu_page('edit.php?post_type=faq_wd', 'Featured plugins', 'Featured plugins', 'manage_options', 'featured_plugins', array($this, 'featured_plugins_submenu'));
        add_submenu_page('edit.php?post_type=faq_wd', 'Featured themes', 'Featured themes', 'manage_options', 'featured_themes', array($this, 'featured_themes_submenu'));
        add_submenu_page('edit.php?post_type=faq_wd', 'Uninstall', 'Uninstall', 'manage_options', 'uninstall_faq_wd', array($this, 'uninstall_faq_wd'));
    }

    public function theme_submenu() {
        include_once('views/admin/theme.php');
    }

    public function featured_plugins_submenu() {
        include_once('views/admin/faq-wd-featured-plugins.php');
    }

    public function featured_themes_submenu() {
        include_once('views/admin/faq-wd-featured-themes.php');
    }

    public function faq_wd_settings() {
        include_once('views/admin/faq_wd_settings.php');
    }

    public function faqwd_head() {
        $cat_ids = get_option('faqwd_categories_order');
        $cat_ids = json_decode($cat_ids);
        ?>
        <script>
            var faq_plugin_url = '<?php echo plugins_url(plugin_basename(dirname(__FILE__))); ?>';
            var categories = [];
        </script>
        <?php
        if ($cat_ids) {
            foreach ($cat_ids as $i => $id) {
                $term = get_term($id, 'faq_category');
                ?>
                <script>
                    categories[<?php echo $i; ?>] = {
                        id: '<?php echo $id; ?>',
                        name: '<?php echo $term->name; ?>'
                    };
                </script>
                <?php
            }
        }
    }

    public function admin_head() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }
        if ('true' == get_user_option('rich_editing')) {
            add_filter('mce_external_plugins', array($this,
                'mce_external_plugins'
            ));
            add_filter('mce_buttons', array($this,
                'mce_buttons'
            ));
        }
    }

    public function mce_external_plugins($plugin_array) {
        $screen = get_current_screen();
        if ($screen->post_type == 'post' || $screen->post_type == 'page') {
            $plugin_array[$this->shortcode_tag] = FAQ_URL . 'js/faqwd-mce-button.js';
        }
        return $plugin_array;
    }

    public function mce_buttons($buttons) {
        array_push($buttons, $this->shortcode_tag);
        return $buttons;
    }

    public function create_faq_category($term_id) {
        $order_ids = get_option('faqwd_categories_order');
        if ($order_ids) {
            $order_ids = json_decode($order_ids, true);
        } else {
            $order_ids = array();
        }
        $order_ids [] = $term_id;
        update_option('faqwd_categories_order', json_encode($order_ids));
    }

    public function faqwd_categories_js() {
        $cat_ids = get_option('faqwd_categories_order');
        $cat_ids = json_decode($cat_ids);
        ?>
        <script>
            var cat_ids = [];
        </script>
        <?php
        if ($cat_ids) {
            foreach ($cat_ids as $id) {
                ?>
                <script>
                    cat_ids.push(<?php echo $id; ?>);
                </script>
                <?php
            }
        }
    }

    public function fawd_remove_category_meta_box() {
        remove_meta_box('tagsdiv-faq_category', 'faq_wd', 'side');
    }

    public function faqwd_add_meta_box() {
        add_meta_box('faq_category_meta_box', 'FAQ Categories', array($this, 'faqwd_category_meta_box'), 'faq_wd', 'side', 'low');
    }

    public function FAQWD_add_category_ordering(){
        add_filter('get_terms', array($this, 'FAQWD_category_ordering'), 10, 3);
    } 
    
    public function faqwd_category_meta_box($post) {
        $taxonomy = 'faq_category';
        $tax = get_taxonomy($taxonomy);

        $cat_ids = get_option('faqwd_categories_order');
        $cat_ids = json_decode($cat_ids, true);
        $popular = get_terms($taxonomy, array(
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => 5,
            'offset' => 0,
            'hierarchical' => false
        ));
           
        
        
        $post_term_ids = wp_get_post_terms($post->ID, $taxonomy, array('fields' => 'ids'));
        $terms = array();
        if ($cat_ids) {
            foreach ($cat_ids as $i => $id) {
                $terms [$i] = get_term($id, $taxonomy);
                if (in_array($id, $post_term_ids)) {
                    $terms[$i]->checked = "checked";
                } else {
                    $terms[$i]->checked = "";
                }
            }
        }
                
        if($popular){
            foreach($popular as $t){
                if (in_array($t->term_id, $post_term_ids)) {
                    $t->checked = "checked";
                }else{
                    $t->checked = "";
                }
            }
        }             
        $name = 'tax_input[' . $taxonomy . '][]';
        include_once('views/admin/category_meta_box.php');
    }

    public function FAQWD_category_ordering($terms) {
       $screen = get_current_screen();
        if (!empty($screen) && 'edit-faq_category'== $screen->id && !empty($terms) && isset($terms[0]->taxonomy) && $terms[0]->taxonomy == "faq_category") {
            $cat_ids = get_option('faqwd_categories_order');
            $cat_ids = json_decode($cat_ids, true);
            $new_terms = array();
            if ($cat_ids) {
                foreach ($cat_ids as $id) {
                    if (get_term($id, 'faq_category')) {
                        $new_terms [] = get_term($id, 'faq_category');
                    }
                }
            }
            if ($new_terms) {
                $terms = $new_terms;
            }
        }
        return $terms;
    }

    public function delete_faq_category($term_id) {
        $cat_ids = get_option('faqwd_categories_order');
        $cat_ids = json_decode($cat_ids, true);
        foreach ($cat_ids as $i => $id) {
            if ($id == $term_id) {
                unset($cat_ids[$i]);
            }
        }
        update_option('faqwd_categories_order', json_encode($cat_ids, true));
    }

    public function faqwd_sotable() {
        if (isset($_POST['order']) && $_POST['order'] != '') {
            $ids = explode(',', $_POST['order']);
            $max = count($ids) - 1;
            foreach ($ids as $i => $id) {
                update_post_meta($id, 'faqwd_order', $max - $i);
            }
            die;
        }
    }

    public function FAQWD_get_posts($wp_query) {
        if ($wp_query->query['post_type'] == "faq_wd") {
            if (!$wp_query->get('orderby')) {
                $wp_query->set('orderby', 'meta_value');
                $wp_query->set('meta_key', 'faqwd_order');
            }
            if (!$wp_query->get('order'))
                $wp_query->set('order', 'DESC');
        }
    }

    public function faqwd_category_sotable() {
        if (isset($_POST['order']) && $_POST['order'] != '') {
            $ids = explode(',', $_POST['order']);
            update_option('faqwd_categories_order', json_encode($ids));
            die;
        }
    }

    public function include_admin_style() {
        wp_register_style('faqwd-admin-style', FAQ_URL . 'css/admin.css', 1, $this->version);
        wp_enqueue_style('faqwd-admin-style');
        wp_register_style('faqwd-evol-colorpicker-min', FAQ_URL . 'css/evol.colorpicker.css', 1, $this->version);
        wp_enqueue_style('faqwd-evol-colorpicker-min');
//
        wp_enqueue_style('faqwd-featured-plugins-style', FAQ_URL . 'css/featured_plugins.css', 1, $this->version);
        wp_enqueue_style('faqwd-featured-themes-style', FAQ_URL . 'css/featured_themes.css', 1, $this->version);
    }

    public function include_admin_scripts() {

        wp_enqueue_style('faqwd_shortcode_style', FAQ_URL . 'css/mce-button.css');
        wp_register_script('spider-faq-script', FAQ_URL . 'js/admin/admin.js', array('jquery',
            'jquery-ui-sortable',
            'jquery-ui-tabs'
                ), $this->version, true);
        wp_enqueue_script('spider-faq-script');

        wp_enqueue_media();
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

    public function create_logo_to_head() {
        $screen = get_current_screen();
        if ($screen->post_type == 'faq_wd') {
            ?>
            <div style="float:right; width: 100%; text-align: right;clear:both;">
                <a href="https://web-dorado.com/files/fromFAQWD.php" target="_blank"
                   style="text-decoration:none;box-shadow: none;">
                    <img src="<?php echo FAQ_URL . 'images/pro.png' ?>" border="0"
                         alt="https://web-dorado.com/files/fromFAQWD.php" width="215">
                </a>
            </div>
            <?php
        }
    }

    public function uninstall_faq_wd() {
        if (isset($_POST['uninstall_faq_wd']) && $_POST['uninstall_faq_wd'] == "yes") {
            $posts = get_posts(array(
                'numberposts' => -1,
                'post_type' => 'faq_wd',
                'post_status' => 'publish,draft,auto-draft'
            ));
            foreach ($posts as $post) {
                wp_delete_post($post->ID);
            }


            $terms = get_terms('faq_category', array(
                'get' => 'all'
            ));
            foreach ($terms as $term) {
                wp_delete_term($term->term_id, 'faq_category');
            }
            delete_option('faqwd_categories_order');
            delete_option('faqwd_notice');
            delete_option('faqwd_voted_ips');
            delete_option('faqwd_upgrade_has_run');
            delete_option('faqwd_settings_general');
            delete_option('faq_category_children');
            $deactivate_url = wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . FAQ_BASENAME, 'deactivate-plugin_' . FAQ_BASENAME);
            echo '<br /><a href=' . $deactivate_url . ' > Click Here <a/>To Finish The Uninstallation And Spider FAQ Will Be Deactivated Automatically.';
        } else {
            include_once('views/admin/uninstall.php');
        }
    }

    /**
     * Return an instance of this class.
     */
    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}
