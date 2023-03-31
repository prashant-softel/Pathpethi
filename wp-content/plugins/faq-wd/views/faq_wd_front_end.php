<?php

class Display {

    public $li_class = 'expand';
    public $cat_class = 'faqwd_categories hidden';
    public $post_author = '';
    public $useful_count = '';
    public $non_useful_count = '';
    public $hits_count = '';
    public $hits_count_arr = array();
    public $non_useful_count_arr = array();
    public $useful_count_arr = array();
    public $args = array();

    public function __construct($args) {
        if ($args['faq_expand_answers']) {
            $this->li_class = 'expanded';
        }
        $cat_class = "faqwd_categories hidden";
        if ($args['category_show_title']) {
            $this->cat_class = "faqwd_categories";
        }

        $this->args = $args;
    }

    private function get_cats_and_posts() {
        $cats = array();
        $posts = array();
        $cats_order = get_option('faqwd_categories_order');
        if ($cats_order) {
            $cats_order = json_decode($cats_order);
            $tax_query = array(array('taxonomy' => faq_cpt::$taxonomy,
                                     'field' => 'term_id',
                                     'terms' => ''
                               )
            );
            $args = array('numberposts' => -1,
                          'post_type' => faq_cpt::$post_type,
                'orderby' => 'meta_value',
                'meta_key' => 'faqwd_order',
                'order' => 'DESC',
                          'tax_query' => array()
            );
            foreach ($cats_order as $cat) {
                if (in_array($cat, $this->args['cat_ids'])) {
                    $tax_query[0]['terms'] = (int) $cat;
                    $args['tax_query'] = $tax_query;

                    $cats[$cat] = get_term($cat, 'faq_category');
                    $posts[$cat] = get_posts($args);

                    $sub_cats = get_terms(array('faq_category'), array('parent' => $cat));
                    if ($sub_cats) {
                        foreach ($sub_cats as $sub_cat) {
                            $sub_cat_id = $sub_cat->term_id;
                            $cats[$sub_cat_id] = $sub_cat;
                            $tax_query[0]['terms'] = (int) $sub_cat_id;
                            $args['tax_query'] = $tax_query;
                            $posts[$sub_cat_id] = get_posts($args);
                        }
                    }
                }
            }
        } else {
            foreach ($this->args['cat_ids'] as $cat) {
                $tax_query = array(array('taxonomy' => faq_cpt::$taxonomy,
                                         'field' => 'term_id',
                        'terms' => (int) $cat,
                                   )
                );
                $args = array('numberposts' => -1,
                              'post_type' => faq_cpt::$post_type,
                    'orderby' => 'meta_value',
                    'meta_key' => 'faqwd_order',
                    'order' => 'DESC',
                              'tax_query' => $tax_query
                );
                $cats[$cat] = get_term($cat, 'faq_category');
                $posts[$cat] = get_posts($args);
            }
        }
        $this->cats = $cats;
        $this->posts = $posts;
    }

    public function show(){

        $this->get_cats_and_posts();
        $html = '';
        $html .= '<div class="faqwd_conteiner ">';
        if($this->args['faq_search']){
            $html .= $this->get_search_html();
        }
        $html .= $this->get_cats_html();
        $html .= $this->get_actions_html();
        $html .= $this->get_questions();
        $html .= '</div>';
        return $html;
    }


    public function get_search_html(){
        $html = '';
        $html .= ' <div class="faqwd_search">
                    <input type="text" class="faqwd_search_input"/>
                    <div class="faqwd_search_button"></div>
                </div>';
        return $html;

    }

    public function get_cats_html(){
        $html = '';
        $html .= '<div  class="' . $this->cat_class . '">';
        if(count($this->cats) > 0){
            $i = 1;

            $html .= '<ul class="faqwd_categories_ul">';
            foreach($this->cats as $cat_id => $cat){
                if(!isset($cat)){
                    continue;
                }
                $html .= ' <li class="faqwd_categories_li faqwd_category_id_' . $cat_id . '" data-catid="'.$cat_id.'">';
                if ($this->args['faq_category_numbering']) {
                    $html .= $i . '. ';
                    $i++;
                }
                $html .= $cat->name;
            }
            $html .= '</li>';
            $html .= '</ul>';

            if($this->args['category_show_description'] && isset($this->cats)){
                foreach($this->cats as $cat_id => $cat){
                    if(isset($cat->description) && $cat->description !== ""){
                        $html .= ' <div class="faqwd_cat_desc_' . $cat_id . ' faqwd_cat_desc hidden">';
                        $html .= $cat->description;
                        $html .= '</div>';
                    }
                }
            }
        }

        //end cats


        $html .= '</div>';
        return $html;
    }

    public function get_actions_html(){
        $html = '';
        $html .= '<div class="faqwd_expand_collapse">
    <span class="faqwd_expand">' . __('Expand All', 'faqwd') . '</span>
        <span class="hidden">|</span>
    <span class="faqwd_collapse">' . __('Collapse All', 'faqwd') . '</span>
    </div>';

        return $html;
    }

    public function get_questions(){
        $posts = $this->posts;
        $html = '';
        $html .= '<div class="faqwd_questions" style="float: left;margin-top: 0px;">';

        $current_ip = $_SERVER['REMOTE_ADDR'];
        $exists_ips = get_option('faqwd_voted_ips');
        $exists_ips = json_decode($exists_ips, true);

        foreach($this->cats as $cat_id => $cat){
            if(isset($posts[$cat_id]) && count($posts[$cat_id]) > 0){
                $html .= '<div class="faqwd_cat_' . $cat_id . ' faqwd_cat hidden" style="display:block !important;" >';
                $html .= '<ul class="faqwd_questions_ul">';
                foreach($posts[$cat_id] as $number => $post){
                    $this->useful_count_arr = get_post_meta($post->ID, 'faqwd_useful');
                    $this->useful_count = 0;
                    if(isset($this->useful_count_arr[0])){
                        $this->useful_count = (int)$this->useful_count_arr[0];
                    }

                    $this->non_useful_count_arr = get_post_meta($post->ID, 'faqwd_non_useful');
                    $this->non_useful_count = 0;
                    if(isset($this->non_useful_count_arr[0])){
                        $this->non_useful_count = (int)$this->non_useful_count_arr[0];
                    }
                    $this->hits_count_arr = get_post_meta($post->ID, 'faqwd_hits');
                    $this->hits_count = 0;
                    if(isset($this->hits_count_arr[0])){
                        $this->hits_count = $this->hits_count_arr[0];
                    }
                    $usful_title = "";
                    if(isset($exists_ips[$post->ID])){
                        foreach($exists_ips[$post->ID] as $ip){
                            if($ip == $current_ip){
                                $usful_title = __('You have already voted.','faqwd');
                                break;
                            }
                        }
                    }

                    $comments_count = wp_count_comments($post->ID)->total_comments;
                    if ( $comments_count > 1 ) {
                        $comment =  $comments_count.__(" Comments", 'faqwd');
                    } elseif ( $comments_count == 0 ) {
                        $comment =  __("No Comments", 'faqwd');
                    } else{
                        $comment = __("1 Comment", 'faqwd');
                    }

                    $html .= '<li class="faqwd_question_li ' . $this->li_class . ' faqwd_qustion_li_' . $cat_id . '_' . $post->ID . '">';
                    $html .= '<div class="faqwd_question_title_container" data-faqid=' . $cat_id . "_" . $post->ID . '>';
                    $html .= ' <div class="faqwd_question_title">';
                    $html .= ' <span class="faqwd_quest_numbering" style="vertical-align:-webkit-baseline-middle;">' . ($number + 1) . '.</span>';
                    $html .= '<span class="faqwd_post_title" id="' . $cat_id . '_' . $post->ID . '"  style="vertical-align: -webkit-baseline-middle;">' . $post->post_title . '</span>';
                    $html .= '</div>';
                    $html .= '<div class="before20"><span class="arr">&nbsp;</span></div>';
                    $html .= '</div>';
                    $html .= '<div class="faqwd_question_content faqwd_question_' . $cat_id . '_' . $post->ID . '">';
                    $html .= '<div class="faqwd_answer_container">';
                    $html .= '<div class="faqwd_post_info">';
                    if($this->args['faq_user']){
                      //  echo $post->post_author;
                        $html .= '<span class="faqwd_post_author">' . __("Author: ", "faqwd") . get_userdata( $post->post_author )->display_name . ' </span >';
                    }
                    if($this->args['faq_date']){
                        $html .= '<span class="faqwd_date">' . date("d.m.y", strtotime($post->post_date)) . '</span>';
                    }
                    $html .= '</div>';
                    $content = wpautop($post->post_content);
                    $tmp_arr = explode('<!--more-->',$content);
                    $content = $tmp_arr[0];

                    if($post->post_excerpt){
                        $html .= '<div class="faqwd_answer">' . $post->post_excerpt . '</div>';
                    } else{
                        $html .= '<div class="faqwd_answer">' . $content . '</div>';
                    }
                    $html .= '<span><a class="faqwd_read_more_button" href="' . get_permalink($post->ID) . '">' . __("More", "faqwd") . '</a></span>';
                    $html .= '<div class="faqwd_question_options">';
                    if($this->args['faq_like']){
                        $html .= '<div class="faqwd_vote_option" data-faqid= ' . $post->ID . '>';
                        $html .= '<span>' . __("Was this answer helpful ? ", "faqwd") . '</span>';

                        $html .= '<span title="'.$usful_title.'"class="faqwd_useful">
                                                        <span class="useful_yes_no">' . __("Yes", "faqwd") . '</span>
                                                        (<span class="faqwd_count_useful_' . $post->ID . '">' . $this->useful_count . '</span>)
                                                    </span>/
                                                    <span title="'.$usful_title.'" class="faqwd_non_useful">
                                                        <span class="useful_yes_no">' . __("No", "faqwd") . '</span>
                                                        (<span class="faqwd_count_non_useful_' . $post->ID . '">' . $this->non_useful_count . '</span>)
                                                    </span>';
                        $html .= '</div>';
                    }
                    if($this->args['faq_hits']){
                        $html .= '<span class="faqwd_viewed" >' . __("Viewed", "faqwd") . '
                                        <span class="faqwd_count_hits_' . $post->ID . '">' . $this->hits_count . '</span>' . __(" Times ", "faqwd") . '</span>';
                    }
                    if($post->comment_status == 'open'){
                        $html .= '<span class="faqwd_post_comments"><a href="' . get_comments_link($post->ID) . '">' . $comment . '</a></span>';
                    }
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</li >';
                }


                $html .= '</ul > ';
                $html .= '</div > ';
            }


        }
        $html .= '</div>';
        return $html;
    }
}