<?php

/**
 * Load Plugin Translation
 *
 * @return bool Text domain loaded
 */
function load_text_domain()
{
    if (is_textdomain_loaded('accuusers')) {
        return true;
    }

    return load_plugin_textdomain('accuusers', false, dirname(plugin_basename(__FILE__)) . '/accuusers');
}

/*
 * Load plugin components
 *
 */
function load_components()
{
    // Admin bar menu item
    add_action('admin_menu', 'new_menu_item');
}

/**
 * Register menu page
 */
function new_menu_item()
{
    add_menu_page(
        'AccuUsers',
        'Accu Users',
        'administrator',
        'accuusers',
        'start_users_manager',
        $icon_url = 'dashicons-admin-users',
        $position = null
    );
}

/**
 * Register and add settings
 */
function au_register_settings()
{
    register_setting('accuusers', 'au_options');

    add_settings_section('au_section_id', '', 'au_section_subtitle', 'accuusers');

    add_settings_field(
        'au_apiurl', // ID
        'Api URL', // Title
        'au_render_text_input_field', // Callback
        'accuusers', // Page
        'au_section_id', // section
        [
            'field' => 'au_apiurl',
            'type' => 'url',
            'label_for' => 'au_apiurl',
            'help' => ''
        ]
    );

    add_settings_field(
        'au_apikey',
        'Api Key (optional)',
        'au_render_text_input_field',
        'accuusers',
        'au_section_id',
        [
            'field' => 'au_apikey',
            'type' => 'text',
            'label_for' => 'au_apikey',
            'help' => 'The API token for your domain'
        ]
    );
}

/**
 * Execute when admin is initiated
 */
add_action('admin_init', 'au_register_settings');

/**
 * Callback function for section subtitle
 */
function au_section_subtitle()
{
    return;
}

/**
 * Render each setting field as needed
 *
 * @param array $args Contains all settings fields as array keys
 */
function au_render_text_input_field($args)
{
    $field = $args['field'];
    $help = $args['help'];
    $type = $args['type'];
    $options = get_option('au_options');
    $value = $options[$field] ?? '';
    ?>

    <input name="au_options[<?php _e($field); ?>]" type="<?php _e($type) ?>" id="<?php _e($field); ?>"
           value="<?php _e($value); ?>" class="regular-text">
    <p><?php _e($help); ?></p>

    <?php
}

/**
 * Show settings form in admin page
 */
function start_users_manager()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    ?>
    <h1>
        <?php esc_html_e('Accu Users Settings', 'accuusers'); ?>
    </h1>
    <?php settings_errors(); ?>
    <form method="POST" action="options.php">
        <?php settings_fields('accuusers'); ?>
        <?php do_settings_sections('accuusers'); ?>

        <?php submit_button(); ?>
    </form>

    <?php
}

/************************************
 * Alert
 ************************************/
/**
 * Generic function to show a message to the user using WP's
 * standard CSS classes to make use of the already-defined
 * message colour scheme.
 *
 * @param string $message The message you want to tell the user.
 * @param bool $errormsg If true, the message is an error, so use
 * the red message style. If false, the message is a status
 * message, so use the yellow information message style.
 */
function au_show_message(string $message, bool $errormsg = false)
{
    if ($errormsg) {
        echo '<div id="message" class="error">';
    } else {
        echo '<div id="message" class="updated fade">';
    }
    echo "<p><strong>$message</strong></p></div>";
}

/**
 * Show message to admin
 */
function au_show_admin_messages()
{
    // Only show to admins
    if (current_user_can('manage_options')) {
        $options = get_option('au_options');
        if (!$options['au_apiurl']) {
            au_show_message("You must <a href='/wp-admin/admin.php?page=accuusers'>provide your API's authentication endpoint</a> to use External API Authentication.", true);
        }
    }
}

/**
 * Call au_show_admin_messages() when showing other admin
 * messages. The message only gets shown in the admin
 * area.
 */
add_action('admin_notices', 'au_show_admin_messages');


