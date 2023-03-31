<?php

/**
 * Admin page
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $faqwd_settings;

?>

<div class="wrap">
    <?php settings_errors(); ?>
    <div id="ecwd-settings">
        <div id="ecwd-settings-content">
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

            <form method="post" action="options.php">
                <?php wp_nonce_field( 'update-options' ); ?>
                <?php
                settings_fields( 'faqwd_settings_general' );
                do_settings_sections( 'faqwd_settings_general' );

                ?>

                <?php submit_button(); ?>

            </form>
        </div>
        <!-- #ecwd-settings-content -->
    </div>
    <!-- #ecwd-settings -->
</div><!-- .wrap -->
