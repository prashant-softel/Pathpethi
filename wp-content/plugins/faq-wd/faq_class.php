<?php

class faq_class{

    protected static $instance = null;
    public $cpt;
    public $shortcode_tag = 'faq_wd';
    public $post_type = 'faq_wd';
    public $version = '1.0.0';

    private function __construct(){
        $this->includes();
        add_action('wp_enqueue_scripts', array($this, 'register_front_end_styles'));
        add_action('wp_enqueue_scripts', array($this, 'register_front_end_scripts'));
        add_action('wp_ajax_faq_wd_vote', array($this, 'faq_wd_vote'));
        add_action('wp_ajax_nopriv_faq_wd_vote', array($this, 'faq_wd_vote'));
        add_filter('the_content', array($this, 'faqwd_custom_template'));
    }

    public function includes(){
        global $faqwd_options;
        include_once('includes/register_settings.php');       
        $faqwd_options = faqwd_get_settings();
        include_once('includes/faq_cpt_class.php');
        $this->cpt = faq_cpt::get_instance();
        include_once('includes/shortcode.php');
    }

    function register_front_end_styles(){
        wp_register_style('front_end_style', FAQ_URL.'css/front_end_style.css', array(),$this->version);
        wp_enqueue_style('front_end_style');
        wp_register_style('front_end_default_style', FAQ_URL.'css/default.css', array(), $this->version);
        wp_enqueue_style('front_end_default_style');
    }

    function register_front_end_scripts(){

        wp_register_script('vote_button',FAQ_URL.'js/vote.js',array('jquery',
            'jquery-ui-widget'), $this->version, true);
        wp_enqueue_script('vote_button');
        wp_register_script('front_js', FAQ_URL.'js/faq_wd_front_end.js',array('jquery',
            'jquery-ui-widget'), $this->version, true);
        wp_enqueue_script('front_js');

        wp_localize_script('vote_button', 'faqwd', array('ajaxurl' => admin_url('admin-ajax.php'),
            'ajaxnonce' => wp_create_nonce('faqwd_ajax_nonce'), 'loadingText' => __('Loading...', 'faqwd')));
    }



    function faq_wd_vote(){
        $post_id = $_POST['post_id'];
        $type = $_POST['type'];
        if($type == 'hits'){
            $hits = 1;
            $faqwd_hits = get_post_meta($post_id, 'faqwd_hits', true);
            if($faqwd_hits != null){
                $hits = (int)$faqwd_hits + 1;
            }

            update_post_meta($post_id, 'faqwd_hits', $hits);
            echo json_encode(array("hits" => $hits));
            die;
        } else{

            $count = array();
            $current_ip = $_SERVER['REMOTE_ADDR'];
            $exists_ips = get_option('faqwd_voted_ips');
            $exists_ips = json_decode($exists_ips, true);
            if(isset($exists_ips[$post_id]) && is_array($exists_ips[$post_id])){
                if(!in_array($current_ip, $exists_ips[$post_id])){
                    $exists_ips[$post_id][] = $current_ip;
                    $count = $this->useful_non_useful($type, $post_id);
                    update_option('faqwd_voted_ips', json_encode($exists_ips), true);
                } else{
                    $count_useful = get_post_meta($post_id, 'faqwd_useful', true);
                    (isset ($count_useful)  && $count_useful!='') ? $count['useful'] = $count_useful : $count['useful'] = 0;
                    $count_non_useful = get_post_meta($post_id, 'faqwd_non_useful', true);
                    (isset ($count_non_useful) && $count_non_useful!='') ? $count['non_useful'] = $count_non_useful : $count['non_useful'] = 0;
                }
            } else{
                $exists_ips[$post_id] = array($current_ip);
                update_option('faqwd_voted_ips', json_encode($exists_ips));
                $count = $this->useful_non_useful($type, $post_id);
            }
            echo json_encode(array('useful' => $count['useful'], 'non_useful' => $count['non_useful']));
            die;
        }
    }

    function useful_non_useful($type, $id){



        $useful_arr = get_post_meta($id, 'faqwd_useful');
        $non_useful_arr = get_post_meta($id, 'faqwd_non_useful');
        ( isset($useful_arr[0]) ) ? $useful = $useful_arr[0] : $useful = 0 ;
        ( isset($non_useful_arr[0]) ) ? $non_useful = $non_useful_arr[0] : $non_useful = 0 ;


        if($type == 'useful'){
            $useful = (int)$useful + 1;
            update_post_meta($id, 'faqwd_useful', $useful);
        } else{
            $non_useful = (int)$non_useful + 1;
            update_post_meta($id, 'faqwd_non_useful', $non_useful);
        }
        $count = array('useful' => $useful, 'non_useful' => $non_useful);
        return $count;
    }

    public function faqwd_custom_template($content){
            global $post;

            if (is_single()) {
                if ($post->post_type == 'faq_wd') {
                    $event_content = '';
                    ob_start();
                    include_once(FAQ_DIR . '/views/faq_wd_content.php');
                    $event_content .= ob_get_clean();
                    $content = $event_content;
                }
            }
            return $content;
    }

    /**
     * Return an instance of this class.
     */
    public static function get_instance(){
        if(null == self::$instance){
            self::$instance = new self;
        }
        return self::$instance;
    }
}

?>