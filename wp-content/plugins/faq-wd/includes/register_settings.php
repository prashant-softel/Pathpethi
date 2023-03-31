<?php

function faqwd_register_settings() {
    global $faqwd_settings;

    $faqwd_settings = array(
        /* General Settings */
        'general' => array(
            'single_display_share_buttons' => array(
                'id'   => 'single_display_share_buttons',
                'name' => __( 'Display social sharing buttons in the question page', 'faqwd' ),
                'desc' => __( 'Check to display social sharing buttons in the question page', 'faqwd' ),
                'type' => 'checkbox'
            ),
            'single_display_date' => array(
                'id'   => 'single_display_date',
                'name' => __( 'Display date in the question page', 'faqwd' ),
                'desc' => __( 'Check to display date in the question page', 'faqwd' ),
                'type' => 'checkbox'
            ),
            'single_display_views' => array(
                'id'   => 'single_display_views',
                'name' => __( 'Display the number of views in the question page', 'faqwd' ),
                'desc' => __( 'Check to display the number of views in the question page', 'faqwd' ),
                'type' => 'checkbox'
            ),
            'single_display_comments' => array(
            'id'   => 'single_display_comments',
            'name' => __( 'Display the number of comments in the question page', 'faqwd' ),
            'desc' => __( 'Check to display the number of comments in the question page', 'faqwd' ),
            'type' => 'checkbox'
            ),
            'enable_comments' => array(
                'id'   => 'enable_comments',
                'name' => __( 'Enable Comments', 'faqwd' ),
                'desc' => __( 'Check to enable comments', 'faqwd' ),
                'type' => 'checkbox'
            ),
        ),

    );

    foreach ( $faqwd_settings as $key => $settings ) {

        add_settings_section(
           'faqwd_settings_' . $key, __(  'General' , 'faqwd' ), '__return_false','faqwd_settings_' . $key
        );

        foreach ( $settings as $option ) {
            add_settings_field(
                'faqwd_settings_' . $key . '[' . $option['id'] . ']', $option['name'], function_exists('faqwd_' . $option['type'] . '_callback' ) ?'faqwd_' . $option['type'] . '_callback' :'faqwd_missing_callback','faqwd_settings_' . $key,'faqwd_settings_' . $key, faqwd_get_settings_field_args( $option, $key )
            );
        }

        /* Register all settings or we will get an error when trying to save */
        register_setting('faqwd_settings_' . $key,  'faqwd_settings_' . $key,'faqwd_settings_sanitize' );
    }
}

add_action( 'admin_init', 'faqwd_register_settings');


/*
 * Return generic add_settings_field $args parameter array.
 *
 * @param   string  $option   Single settings option key.
 * @param   string  $section  Section of settings apge.
 * @return  array             $args parameter to use with add_settings_field call.
 */


function faqwd_get_settings_field_args( $option, $section ) {
    $settings_args = array(
        'id'      => $option['id'],
        'desc'    => $option['desc'],
        'name'    => $option['name'],
        'section' => $section,
        'size'    => isset( $option['size'] ) ? $option['size'] : null,
        'options' => isset( $option['options'] ) ? $option['options'] : '',
        'std'     => isset( $option['std'] ) ? $option['std'] : '',
        'href'    => isset( $option['href'] ) ? $option['href'] : ''
    );

    // Link label to input using 'label_for' argument if text, textarea, password, select, or variations of.
    // Just add to existing settings args array if needed.
    if ( in_array( $option['type'], array( 'text', 'select', 'textarea', 'password', 'number' ) ) ) {
        $settings_args = array_merge( $settings_args, array( 'label_for' =>'faqwd_settings_' . $section . '[' . $option['id'] . ']' ) );
    }

    return $settings_args;
}

function faqwd_checkbox_callback( $args ) {
    global $faqwd_options;
    $checked = isset( $faqwd_options[ $args['id'] ] ) ? checked( 1, $faqwd_options[ $args['id'] ], false ) : '';
    $html    = "\n" . '<div class="checkbox-div"><input type="checkbox" id="faqwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="faqwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/><label for="faqwd_settings_' . $args['section'] . '[' . $args['id'] . ']"></label></div>' . "\n";
    // Render description text directly to the right in a label if it exists.
    if ( ! empty( $args['desc'] ) ) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }

    echo $html;
}


/*
 * Function we can use to sanitize the input data and return it when saving options
 *
 * @since 2.0.0
 *
 */
function faqwd_settings_sanitize( $input ) {
    //add_settings_error( 'gce-notices', '', '', '' );
    return $input;
}

/*
 *  Default callback function if correct one does not exist
 *
 * @since 2.0.0
 *
 */
function faqwd_missing_callback( $args ) {
    printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'faqwd' ), $args['id'] );
}

function faqwd_get_settings() {

    // Set default settings
    // If this is the first time running we need to set the defaults
    if ( ! get_option( 'faqwd_upgrade_has_run' ) ) {
        $general = get_option('faqwd_settings_general' );


        if(!isset($general['save_settings'])){
            $general['save_settings'] = 1;
            $general['single_display_share_buttons'] = 1;
            $general['single_display_comments'] = 1;
            $general['single_display_date'] = 1;
            $general['single_display_views'] = 1;
            $general['enable_comments'] = 1;
        }
        update_option('faqwd_upgrade_has_run', $general );
        update_option('faqwd_settings_general', $general );
    }

    $general_settings = is_array( get_option('faqwd_settings_general' ) ) ? get_option('faqwd_settings_general' ) : array();

    return $general_settings;
}