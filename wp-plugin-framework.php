<?php
/*
Plugin Name: WP Plugin Framework
Plugin URI: http://mammothology.com/
Description: A nice starting point for WordPress plugin development
Version: 1.0
Author: Mammothology
Author URI: http://mammothology.com
*/



////////////////////////////////////////////////////////////////////////////////////////////////
//OPTIONAL: If your plugin relies upon other plugins (i.e. it's an extension) here is where you check for
//the existence of that plugin and return if it is not found
/*
if (!class_exists('PP_Parent_Plugin'))
    return;
*/
////////////////////////////////////////////////////////////////////////////////////////////////

//include the main plugin file containing the class
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wp-plugin-framework-main.php';

////////////////////////////////////////////////////////////////////////////////////////////////
//OPTIONAL: If you need to create new database tables or do any other initialization when the plugin
//is activated, you can register a function in the main class to handle this here

register_activation_hook( __FILE__, array( 'WP_Plugin_Framework', 'setup_db' ) );

////////////////////////////////////////////////////////////////////////////////////////////////