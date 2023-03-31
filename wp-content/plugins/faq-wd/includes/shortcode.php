<?php
include_once(FAQ_DIR . "/views/faq_wd_front_end.php");
function shortcode_handler($atts){
    global $post;
    $post_id = $post->ID;

    extract(shortcode_atts(array('cat_ids' => 'no',
                                 'faq_like' => false,
                                 'faq_hits' => false,
                                 'faq_user' => false,
                                 'faq_date' => false,
                                 'faq_category_numbering' => false,
                                 'category_show_description' => false,
                                 'category_show_title' => false,
                                 'faq_expand_answers' => false,
                                 'faq_search' => false
    ), $atts));
    if(isset($cat_ids)){
        $cat_ids = explode(",", $cat_ids);
    } else{
        $cat_ids = array();
    }

    wp_reset_postdata();
    $args = array('post_id' => $post_id,
                  'cat_ids' => $cat_ids,
                  'faq_expand_answers' => $faq_expand_answers,
                  'category_show_title' => $category_show_title,
                  'faq_category_numbering' => $faq_category_numbering,
                  'category_show_description' => $category_show_description,
                  'faq_user' => $faq_user,
                  'faq_date' => $faq_date,
                  'faq_like' => $faq_like,
                  'faq_hits' => $faq_hits,
                  'faq_search' => $faq_search
    );

    $display = new Display($args);
    $html = $display->show();

    return $html;
}

add_shortcode('faq_wd', 'shortcode_handler');