<?php
global $faqwd_options;
$date = date("d.m.Y", strtotime($post->post_date));
$hits_post_meta_arr = get_post_meta($post->ID, "faqwd_hits");
$viewed = ($hits_post_meta_arr) ? $hits_post_meta_arr[0]: 0;
$useful_post_meta_arr = get_post_meta($post->ID, "faqwd_useful");
$useful_count = ($useful_post_meta_arr) ? (int)$useful_post_meta_arr[0] : 0;
$non_useful_post_meta_arr = get_post_meta($post->ID, "faqwd_non_useful");
$non_useful_count = ($non_useful_post_meta_arr) ? (int)$non_useful_post_meta_arr[0] : 0;
$viewed++;
update_post_meta($post->ID, "faqwd_hits",$viewed);
?>
<div class="views">
    <?php if(isset($faqwd_options['single_display_views']) && $faqwd_options['single_display_views'] == 1 ){ ?>
        <span class="faqwd_viewed"><?php _e("Viewed ","faqwd"); echo $viewed;  _e(" Times","faqwd"); ?></span>
    <?php } ?>
    <?php if(isset($faqwd_options['single_display_comments']) && $faqwd_options['single_display_comments'] == 1 ){ ?>
        <span class="faqwd_post_comments"><?php comments_number('0 ', '1 ', '% '); _e(" Comments","faqwd"); ?></span>
    <?php } ?>
    <?php if(isset($faqwd_options['single_display_date']) && $faqwd_options['single_display_date'] == 1 ){ ?>
        <span class='faqwd_post_date'><?php _e("Date : ","faqwd");  echo $date ?></span>
    <?php } ?>
<?php
    if(isset($faqwd_options['single_display_share_buttons']) && $faqwd_options['single_display_share_buttons'] == 1 ){
?>
    <span class="faqwd_share_links">
<a  href="http://www.facebook.com/sharer.php?u=<?php echo get_permalink($post->ID)?>" class="faqwd_fb" data-original-title="Share on Facebook">
</a>
<a  href="http://plus.google.com/share?url=<?php echo get_permalink($post->ID)?>" class="faqwd_gpluse" data-original-title="Share on Google+ ">
</a>
<a  href="http://twitter.com/home?status=<?php echo get_permalink($post->ID)?>" class="faqwd_twitter" data-original-title="Tweet It">
</a>
</span>
<?php } ?>

</div>
<div class="clear"></div>
<div class="faqwd_content">
    <?php echo $content ?>
</div>
<div class="faqwd_vote_option" data-faqid= <?php echo $post->ID; ?>>
    <span><?php _e("Was this answer helpful ?","faqwd"); ?></span>
    <span class='faqwd_useful'>
        <span class="useful_yes_no"><?php _e("Yes","faqwd"); ?></span>(<span
            class='faqwd_count_useful_<?php echo $post->ID; ?>'><?php echo $useful_count; ?></span>)</span>/
    <span class='faqwd_non_useful'>
        <span class="useful_yes_no"><?php _e("No","faqwd"); ?></span>(<span
            class='faqwd_count_non_useful_<?php echo $post->ID; ?>'><?php echo $non_useful_count; ?></span>)</span>
</div>