<?php
/**
 * @package FT_Calendar
 * @version 1.0.4.1
 */
/*
Plugin Name: FullThrottle Calendar
Plugin URI: http://calendar-plugin.com/
Description: A feature rich calendar plugin for WordPress.
Author: FullThrottle Development
Version: 1.0.4.1
Author URI: http://fullthrottledevelopment.com/
Primary Developer: Glenn Ansley (glenn@glennansley.com)
Primary Developer: Lew Ayotte (lew@lewayotte.com)
*/

#### CONSTANTS ####
define( 'FT_CAL_VERSION', '1.0.4.1' );
define( 'FT_CAL_DB_VERSION', '1.0' );

// From http://codex.wordpress.org/Determining_Plugin_and_Content_Directories
if ( ! function_exists( 'is_ssl' ) ) {
	
	function is_ssl() {
		
		if ( isset( $_SERVER['HTTPS'] ) ) {
			
			if ( 'on' == strtolower( $_SERVER['HTTPS'] ) || '1' == $_SERVER['HTTPS'] )
				return true;
							
		} elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
			
			return true;
		
		}
		
		return false;
		
	}
	
}

// Set wp_content URL
if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) && is_ssl() )
	$wp_content_url = str_replace( 'http://' , 'https://' , get_option( 'siteurl' ) );
else
	$wp_content_url = get_option( 'siteurl' );

$wp_content_url 	.= '/wp-content';
$wp_content_dir 	 = ABSPATH . 'wp-content';
$wp_plugin_url 		 = $wp_content_url . '/plugins';
$wp_plugin_dir 		 = $wp_content_dir . '/plugins';


// If this file is in the plugin directory, proceed as normal.
if ( strpos( __FILE__, WP_PLUGIN_DIR ) === 0 ) {
	$ftcalendar_file = plugin_basename( __FILE__ );
} else {
	// This file is most likely marked as an active plugin, so let's find it that way.
	$ft_active_plugins = preg_grep( '#/' . basename( __FILE__ ) . '$#', get_option( 'active_plugins', array() ) );
	if ( !empty( $ft_active_plugins ) ) {
		$ftcalendar_file = current( $ft_active_plugins );
	} else {
		// Last ditch effort to find the 'good' filename.
		$ftcalendar_file = plugin_basename( $plugin ? $plugin : ( $mu_plugin ? $mu_plugin : ( $network_plugin ? $network_plugin : __FILE__ ) ) );
	}
}
$ftcalendar_dir = dirname( $ftcalendar_file );

/**
 * URL to Plugin folder
 *
 * @since 0.3
 */
define( 'FT_CAL_URL', $wp_plugin_url . '/' . $ftcalendar_dir );

/**
 * Server path to plugin Dir
 *
 * @since 0.3
 */
define( 'FT_CAL_PATH', $wp_plugin_dir . '/' . $ftcalendar_dir );


#### INCLUDES ####

include_once( FT_CAL_PATH . '/includes/functions.php' );
include_once( FT_CAL_PATH . '/classes/ft-ps-client.php' );
include_once( FT_CAL_PATH . '/classes/class-options.php' );
include_once( FT_CAL_PATH . '/classes/class-admin.php' );
include_once( FT_CAL_PATH . '/classes/class-calendars.php' );
include_once( FT_CAL_PATH . '/classes/class-events.php' );
include_once( FT_CAL_PATH . '/classes/class-widgets.php' );
include_once( FT_CAL_PATH . '/classes/class-shortcodes.php' );

#### PROCEDURAL POWER FTW! ####
// Init Admin

/**
 * Options object to hold settins.
 *
 * @since 0.3
 */
$ft_cal_options		= new FT_CAL_Options();

/**
 * Class for setting up and displaying admin options
 *
 * @since 0.3
 */
$ft_cal_admin 		= new FT_CAL_Admin();
		
/**
 * Object and methods used to manage / display calendar post types
 * 
 * @since 0.3
 */
$ft_cal_calendars 	= new FT_CAL_Calendars();

/**
 * Object and methods used to manage / display events post types
 *
 * @since 0.3
 */
$ft_cal_events		= new FT_CAL_Events();

/**
 * Object used to manage / display shortcodes
 *
 * @since 0.3
 */
$ft_cal_shortcodes	= new FT_CAL_ShortCodes();

/**
 * Premium Support Client for SimpleMap
 * @since 0.3
 */
$config = array( 
	'server_url' => 'http://calendar-plugin.com', 
	'product_id' => 2, 
	'product-slug' => 'ft-calendar-premium', 
	'plugin_support_page_ids' => array( 'ft-calendar_page_ftcalendar-help' ), 
	'plugin_basename' => plugin_basename( FT_CAL_PATH . '/ft-calendar.php' ), 
	'plugin_slug' => 'ft-calendar',
	'learn_more_link' => 'http://calendar-plugin.com/premium-support/' 
);
if ( class_exists( 'FT_Premium_Support_Client' ) && ( ! isset( $ftcalendar_ps ) || ! is_object( $ftcalendar_ps ) ) )
	$ftcalendar_ps = new FT_Premium_Support_Client( $config );

// Maybe update tables
if ( false === get_option( 'ft_cal_db_version' ) 
	|| version_compare( get_option( 'ft_cal_db_version' ), FT_CAL_DB_VERSION, '<' ) )
	$ft_cal_options->install_ftcal_table();
	
// Maybe update something else
if ( false === get_option( 'ft_cal_version' ) 
	|| version_compare( get_option( 'ft_cal_version' ), FT_CAL_VERSION, '<' ) )
	$ft_cal_options->do_ftcal_update();

load_plugin_textdomain( 'ftcalendar', false, $ftcalendar_dir . '/languages/' );