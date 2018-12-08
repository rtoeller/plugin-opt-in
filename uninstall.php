<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

    global $wpdb;

    $delete_content_table = "DROP TABLE IF EXISTS ".$wpdb->prefix . "optin_content ";
    $wpdb->query($delete_content_table);
    $delete_overlay_table = "DROP TABLE IF EXISTS ".$wpdb->prefix . "optin_content_overlay ";
    $wpdb->query($delete_overlay_table);


    delete_option('optin_content');
    delete_option('optin_content');

?>