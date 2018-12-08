<?php

	define( 'SHORTINIT', true );
	$path = $_SERVER['DOCUMENT_ROOT'];
	include_once $path . '/wp-load.php';
	
	$id = $_POST["contentbox_id"];
	global $wpdb;

	$results = $wpdb->get_results( 'select * from ' . $wpdb->prefix . 'optin_content where id = '.$id);
	
	if (count($results) == 1) {
		echo $results[0]->contentbox_code;
	}