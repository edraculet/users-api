<?php

/**
 * Runs on Uninstall of AccuUsers
 *
 * @package
 * @author    Elena Draculet
 */

//exit when the file is called directly
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

//Delete options
delete_option('au_options');
