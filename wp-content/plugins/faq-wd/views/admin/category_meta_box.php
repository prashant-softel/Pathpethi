<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">

    <!-- Display tabs-->
    <ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
        <li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a></li>
        <li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e('Most Used'); ?></a></li>
    </ul>

    <!-- Display taxonomy terms -->
    <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
        <ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists ="list:<?php echo $taxonomy ?>" class="categorychecklist form-no-clear">
            <?php
            foreach ($terms as $term) {
                $id = $taxonomy . '-' . $term->term_id;
                echo "<li id='$id'><label class='selectit'>";
                echo "<input type='checkbox' id='in-$id' name='{$name}'" . $term->checked . " value='$term->name' />$term->name<br />";
                echo "</label></li>";
            }
            ?>
        </ul>
        
        
    </div>


<!--     Display popular taxonomy terms 
-->    <div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
        <ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
            <?php                     
            foreach ($popular as $term) {                
                $id = 'popular-' . $taxonomy . '-' . $term->term_id;
                echo "<li id='$id'><label class='selectit'>";
                echo "<input type='checkbox' id='in-$id'" . $term->checked . " value='$term->term_id' />$term->name<br />";
                echo "</label></li>";
            }
            ?>
        </ul><!--
    </div>-->

</div>