<?php

/************************************
 * API Authentication
 ************************************/
add_filter('authenticate', 'au_auth', 10, 3);

/**
 * Calls external API when user logs in to Wordpress
 *
 * @param $user
 * @param $username
 * @param $password
 * @return bool|false|object|WP_Error|WP_User
 */
function au_auth($user, $username, $password)
{
    $options = get_option('au_options');
    $endpoint = $options['au_apiurl'];

    $user_email_key = 'email';
    $password_key = 'password';

    // Makes sure there is an endpoint set as well as username and password
    if (!$endpoint || $user !== null || (empty($username) && empty($password))) {
        return false;
    }

    // Check user exists locally
    $user_exists = wp_authenticate_username_password(null, $username, $password);

    if ($user_exists && $user_exists instanceof WP_User) {
        $user = new WP_User($user_exists);
        return $user;
    }

    // Build the POST request
    $login_data = array(
        $user_email_key => $username,
        $password_key => $password
    );

    $auth_args = array(
        'method' => 'POST',
        'headers' => array(
            'Content-type: application/x-www-form-urlencoded'
        ),
        'sslverify' => false,
        'body' => $login_data
    );


    $response = wp_remote_post($endpoint, $auth_args);

    // Token if success; Not used right now
    $response_token = json_decode($response['response']['token'], true);

    $response_code = $response['response']['code'];
    if ($response_code == 400) {
        // User does not exist, send back an error message
        $user = new WP_Error('denied', __("<strong>Error</strong>: Your username or password are incorrect."));
    } else if ($response_code == 200) {
        // External user exists, try to load the user info from the WordPress user table
        $userobj = new WP_User();
        // Does not return a WP_User object but a raw user object
        $user = $userobj->get_data_by('email', $username);
        if ($user && $user->ID) {
            // Attempt to load the user with that ID
            $user = new WP_User($user->ID);
        } else {
            // The user does not currently exist in the WordPress user table.
            // Setup the minimum required user information
            $userdata = array(
                'user_email' => $username,
                'user_login' => $username,
                'user_pass' => $password
            );
            // A new user has been created
            $new_user_id = wp_insert_user($userdata);
            // Assign editor role to the new user (so he can access protected articles)
            wp_update_user(
                array(
                    'ID' => $new_user_id,
                    'role' => 'editor'
                )
            );
            // Load the new user info
            $user = new WP_User ($new_user_id);
        }
    }
    // Useful for times when the external service is offline
    remove_action('authenticate', 'wp_authenticate_username_password', 20);

    return $user;
}


