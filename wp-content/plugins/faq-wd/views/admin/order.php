<div class="faqwd_order_container">
    <span class="faqwd_order_icon">.</span>
    <span  class="faqwd_order_descr"> <?php _e('Categories order', 'faqwd');?></span>
    <?php
    $cat_ids = get_option('faqwd_cats_order');
    if($cat_ids != '' and $cat_ids != null){
        echo '<ul class="faqwd_sotable_ul" id = "sortable" >';
        foreach($cat_ids as $cat_id){
            $cat = get_term($cat_id, 'faq_category');
            if($cat == null || $cat->parent != 0) continue;
            echo '
				<li class="ui-state-default" id=' . $cat->term_id . '>' . $cat->name . '</li>
			';
        }
        echo '</ul>';
    } else{
        echo '<ul class="faqwd_sotable_ul" id = "sortable" >';
        $args = array('orderby' => 'name', 'order' => 'DESC', 'hide_empty' => false, 'fields' => 'all',
            'hierarchical' => true, 'hide_empty' => false);
        $cats = get_terms('faq_category', $args);

        foreach($cats as $cat){
            echo '
				<li class="ui-state-default" id=' . $cat->term_id . '>' . $cat->name . '</li>
			';
        }
        echo "</ul>";
    }

    ?>
</div>
