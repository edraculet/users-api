<?php
/*This file is part of accu-users-child-theme, twentynineteen child theme.

All functions of this file will be loaded before of parent theme functions.
Learn more at https://codex.wordpress.org/Child_Themes.

Note: this function loads the parent stylesheet before, then child theme stylesheet
(leave it in place unless you know what you are doing.)
*/

function accu_users_child_theme_enqueue_child_styles() {
$parent_style = 'parent-style'; 
	wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 
		'child-style', 
		get_stylesheet_directory_uri() . '/style.css',
		array( $parent_style ),
		wp_get_theme()->get('Version') );
	}
add_action( 'wp_enqueue_scripts', 'accu_users_child_theme_enqueue_child_styles' );

// add custom login page image
function wp_login_custom_logo() {
    ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url('/wp-content/uploads/2019/11/accuranker-logo-svg.png');
            padding-bottom: 30px;
        }
    </style>
    <?php
}
add_action( 'login_enqueue_scripts', 'wp_login_custom_logo' );

function custom_excerpt( $excerpt ) {
    $post_excerpt = '<p class="the-excerpt">' . $excerpt . '</p>';
    return $post_excerpt;
}
add_filter( 'get_the_excerpt', 'custom_excerpt', 10, 1 );

// restrict access to admin area
function wpse_11244_restrict_admin() {

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You are not allowed to access this part of the site. <a href="/">Go home</a>' ) );
    }
}

add_action( 'admin_init', 'wpse_11244_restrict_admin', 1 );


/**
 * Redirect non-admins to the private page after logging into the site.
 */
function au_login_redirect( $redirect_to, $request, $user  ) {
    return ( isset($user->roles) && is_array( $user->roles ) && in_array( 'administrator', $user->roles ) ) ? admin_url() : site_url('/?p=1');
}
add_filter( 'login_redirect', 'au_login_redirect', 10, 3 );


// add login / logout menu item
add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2);

function add_login_logout_link($items, $args) {
    ob_start();
    wp_loginout('index.php');
    $loginoutlink = ob_get_contents();
    ob_end_clean();
    $items .= '<li>'. $loginoutlink .'</li>';
    return $items;
}


// change theme primary color hsl
function wpse_338301_init() {
    set_theme_mod( 'primary_color', 'custom' );
    set_theme_mod( 'primary_color_hue', 21 );
}
add_action( 'init', 'wpse_338301_init' );

function wpse_338301_saturation() {

    return 87;
}
add_filter( 'twentynineteen_custom_colors_saturation', 'wpse_338301_saturation' );
add_filter( 'twentynineteen_custom_colors_saturation_selection', 'wpse_338301_saturation' );

function wpse_338301_lightness() {

    return 59;
}
add_filter( 'twentynineteen_custom_colors_lightness', 'wpse_338301_lightness' );


//hide admin bar on frontend
if ( ! current_user_can( 'manage_options' ) ) {
    add_filter('show_admin_bar', '__return_false');
}
